<?php

namespace App\Services;

use App\Models\AdminSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WingoBet;
use App\Models\WingoPeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WingoEngine
{
    public const GAME_TYPES = [
        '30s' => 30,
        '1m' => 60,
        '3m' => 180,
        '5m' => 300,
    ];

    /**
     * Get or initialize current active period for a game mode.
     */
    public static function getCurrentPeriod(string $gameType = '30s'): array
    {
        $duration = self::GAME_TYPES[$gameType] ?? 30;
        $now = Carbon::now('Asia/Kolkata');
        $todayStart = $now->copy()->startOfDay();
        $secondsSinceMidnight = ($now->hour * 3600) + ($now->minute * 60) + $now->second;

        $slotIndex = (int) floor($secondsSinceMidnight / $duration) + 1;
        $periodId = $now->format('Ymd').str_pad((string) $duration, 3, '0', STR_PAD_LEFT).str_pad((string) $slotIndex, 5, '0', STR_PAD_LEFT);

        $startSeconds = ($slotIndex - 1) * $duration;
        $endSeconds = $slotIndex * $duration;
        $startTime = $todayStart->copy()->addSeconds($startSeconds);
        $endTime = $todayStart->copy()->addSeconds($endSeconds);

        $remainingSeconds = max(0, $endSeconds - $secondsSinceMidnight);
        $isLocked = ($remainingSeconds <= 5);

        // Check if previous period needs settlement
        self::settlePreviousPendingPeriods($gameType, $periodId);

        // Retrieve or create current period
        $period = WingoPeriod::firstOrCreate(
            [
                'game_type' => $gameType,
                'period_id' => $periodId,
            ],
            [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $isLocked ? 'locked' : 'betting',
            ]
        );

        if ($isLocked && $period->status === 'betting') {
            $period->status = 'locked';
            $period->save();
        }

        return [
            'game_type' => $gameType,
            'duration' => $duration,
            'period_id' => $periodId,
            'period_model' => $period,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'remaining_seconds' => $remainingSeconds,
            'is_locked' => $isLocked,
        ];
    }

    /**
     * Settle any older period that has ended but not completed yet.
     */
    public static function settlePreviousPendingPeriods(string $gameType, string $currentPeriodId): void
    {
        $unsettled = WingoPeriod::where('game_type', $gameType)
            ->where('period_id', '<', $currentPeriodId)
            ->where('status', '!=', 'completed')
            ->orderBy('id', 'asc')
            ->limit(5)
            ->get();

        foreach ($unsettled as $period) {
            self::settlePeriod($period);
        }
    }

    /**
     * Settle a period: pick winning number, calculate attributes, and credit winners atomically.
     */
    public static function settlePeriod(WingoPeriod $period): WingoPeriod
    {
        if ($period->status === 'completed') {
            return $period;
        }

        return DB::transaction(function () use ($period) {
            $lockedPeriod = WingoPeriod::where('id', $period->id)->lockForUpdate()->first();
            if ($lockedPeriod->status === 'completed') {
                return $lockedPeriod;
            }

            // Determine winning number
            if ($lockedPeriod->is_manual) {
                if ($lockedPeriod->manual_number !== null) {
                    $winningNumber = (int) $lockedPeriod->manual_number;
                } elseif ($lockedPeriod->manual_color !== null || $lockedPeriod->manual_size !== null) {
                    $candidates = self::getMatchingNumbers($lockedPeriod->manual_color, $lockedPeriod->manual_size);
                    if (empty($candidates)) {
                        $candidates = range(0, 9);
                    }
                    $exposure = self::calculateExposure($lockedPeriod);
                    $bestNumber = $candidates[0];
                    $minPayout = PHP_FLOAT_MAX;
                    foreach ($candidates as $num) {
                        $payout = $exposure[$num]['payout'] ?? 0;
                        if ($payout < $minPayout) {
                            $minPayout = $payout;
                            $bestNumber = $num;
                        }
                    }
                    $winningNumber = $bestNumber;
                } else {
                    $winningNumber = mt_rand(0, 9);
                }
            } else {
                // Check if admin has enabled profit optimization or if bets exist
                $exposure = self::calculateExposure($lockedPeriod);
                if ($lockedPeriod->total_bets_count > 0) {
                    // Pick the number with minimum total payout (Least Exposure / Maximum House Profit)
                    $bestNumber = 0;
                    $minPayout = PHP_FLOAT_MAX;
                    foreach ($exposure as $num => $data) {
                        if ($data['payout'] < $minPayout) {
                            $minPayout = $data['payout'];
                            $bestNumber = $num;
                        }
                    }
                    $winningNumber = $bestNumber;
                } else {
                    $winningNumber = mt_rand(0, 9);
                }
            }

            $attr = WingoPeriod::getAttributesForNumber($winningNumber);

            $lockedPeriod->winning_number = $winningNumber;
            $lockedPeriod->winning_color = $attr['color_string'];
            $lockedPeriod->winning_size = $attr['size'];
            $lockedPeriod->status = 'completed';
            $lockedPeriod->save();

            // Evaluate all pending bets for this period
            self::evaluateBetsForPeriod($lockedPeriod);

            return $lockedPeriod;
        });
    }

    /**
     * Evaluate all bets placed on a completed period.
     */
    public static function evaluateBetsForPeriod(WingoPeriod $period): void
    {
        $bets = WingoBet::where('game_type', $period->game_type)
            ->where('period_id', $period->period_id)
            ->where('status', 'pending')
            ->lockForUpdate()
            ->get();

        $winningNumber = (int) $period->winning_number;
        $attr = WingoPeriod::getAttributesForNumber($winningNumber);
        $now = Carbon::now('Asia/Kolkata');
        $totalPayout = '0.00';

        foreach ($bets as $bet) {
            $isWon = false;
            $multiplier = 0.00;

            if ($bet->bet_type === 'number') {
                if ((int) $bet->bet_selection === $winningNumber) {
                    $isWon = true;
                    $multiplier = 9.00;
                }
            } elseif ($bet->bet_type === 'color') {
                $selection = strtolower($bet->bet_selection);
                if ($selection === 'green') {
                    if (in_array($winningNumber, [1, 3, 7, 9], true)) {
                        $isWon = true;
                        $multiplier = 2.00;
                    } elseif ($winningNumber === 5) {
                        $isWon = true;
                        $multiplier = 1.50;
                    }
                } elseif ($selection === 'red') {
                    if (in_array($winningNumber, [2, 4, 6, 8], true)) {
                        $isWon = true;
                        $multiplier = 2.00;
                    } elseif ($winningNumber === 0) {
                        $isWon = true;
                        $multiplier = 1.50;
                    }
                } elseif ($selection === 'violet') {
                    if ($winningNumber === 0 || $winningNumber === 5) {
                        $isWon = true;
                        $multiplier = 4.50;
                    }
                }
            } elseif ($bet->bet_type === 'size') {
                $selection = strtolower($bet->bet_selection);
                if ($selection === 'big' && $winningNumber >= 5) {
                    $isWon = true;
                    $multiplier = 2.00;
                } elseif ($selection === 'small' && $winningNumber <= 4) {
                    $isWon = true;
                    $multiplier = 2.00;
                }
            } elseif ($bet->bet_type === 'combination') {
                $sel = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '_', $bet->bet_selection), '_'));
                if ($sel === 'green_big' && in_array($winningNumber, [7, 9], true)) {
                    $isWon = true;
                    $multiplier = 4.50;
                } elseif ($sel === 'green_small' && in_array($winningNumber, [1, 3], true)) {
                    $isWon = true;
                    $multiplier = 4.50;
                } elseif ($sel === 'red_big' && in_array($winningNumber, [6, 8], true)) {
                    $isWon = true;
                    $multiplier = 4.50;
                } elseif ($sel === 'red_small' && in_array($winningNumber, [2, 4], true)) {
                    $isWon = true;
                    $multiplier = 4.50;
                } elseif ($sel === 'violet_big' && $winningNumber === 5) {
                    $isWon = true;
                    $multiplier = 9.00;
                } elseif ($sel === 'violet_small' && $winningNumber === 0) {
                    $isWon = true;
                    $multiplier = 9.00;
                }
            }

            if ($isWon) {
                $payoutDetails = GamePayoutService::calculate($bet->total_amount, $multiplier);
                $winAmount = (string) $payoutDetails['player_payout'];
                $commissionAmount = (string) $payoutDetails['commission_amount'];

                $bet->status = 'won';
                $bet->win_multiplier = $multiplier;
                $bet->commission_amount = $commissionAmount;
                $bet->win_amount = $winAmount;
                $bet->evaluated_at = $now;
                $bet->save();

                $totalPayout = bcadd($totalPayout, $winAmount, 2);

                // Credit player wallet
                $user = User::where('id', $bet->user_id)->lockForUpdate()->first();
                if ($user) {
                    $balanceBefore = $user->balance;
                    $balanceAfter = bcadd((string) $balanceBefore, $winAmount, 2);
                    $user->balance = $balanceAfter;
                    $user->save();

                    $feeNote = ((float) $commissionAmount > 0) ? " (Fee: ₹{$commissionAmount})" : '';
                    Transaction::create([
                        'user_id' => $user->id,
                        'type' => 'bet_won',
                        'amount' => $winAmount,
                        'balance_before' => $balanceBefore,
                        'balance_after' => $balanceAfter,
                        'status' => 'completed',
                        'admin_notes' => "Won WinGo {$bet->game_type} on {$bet->bet_selection} (Period #{$period->period_id}){$feeNote}",
                    ]);
                }
            } else {
                $bet->status = 'lost';
                $bet->win_multiplier = 0.00;
                $bet->commission_amount = 0.00;
                $bet->win_amount = 0.00;
                $bet->evaluated_at = $now;
                $bet->save();
            }
        }

        $period->total_payout_amount = $totalPayout;
        $period->save();
    }

    /**
     * Atomically place a bet on the current active period.
     */
    public static function placeBet(User $user, array $data): array
    {
        $gameType = $data['game_type'] ?? '30s';
        $current = self::getCurrentPeriod($gameType);

        if ($current['is_locked']) {
            return [
                'success' => false,
                'message' => 'Betting is locked for the final 5 seconds. Please wait for the next round!',
            ];
        }

        $unitPrice = (float) ($data['unit_price'] ?? 10.00);
        $multiplier = max(1, (int) ($data['multiplier'] ?? 1));
        $totalAmount = (float) bcmul((string) $unitPrice, (string) $multiplier, 2);

        $setting = AdminSetting::current();
        $minBet = (float) ($setting->wingo_min_bet ?? 1.00);
        $maxBet = (float) ($setting->wingo_max_bet ?? 50000.00);

        if ($totalAmount < $minBet) {
            return [
                'success' => false,
                'message' => 'Minimum bet amount for WinGo is ₹'.number_format($minBet, 2).'.',
            ];
        }

        if ($totalAmount > $maxBet) {
            return [
                'success' => false,
                'message' => 'Maximum bet amount for WinGo is ₹'.number_format($maxBet, 2).'.',
            ];
        }

        return DB::transaction(function () use ($user, $gameType, $current, $data, $unitPrice, $multiplier, $totalAmount) {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            if ((float) $lockedUser->balance < $totalAmount) {
                return [
                    'success' => false,
                    'message' => 'Insufficient wallet balance. Please deposit funds.',
                    'balance' => (float) $lockedUser->balance,
                ];
            }

            // Deduct balance
            $balanceBefore = $lockedUser->balance;
            $balanceAfter = bcsub((string) $balanceBefore, (string) $totalAmount, 2);
            $lockedUser->balance = $balanceAfter;
            $lockedUser->save();

            // Create Bet
            $bet = WingoBet::create([
                'user_id' => $lockedUser->id,
                'game_type' => $gameType,
                'period_id' => $current['period_id'],
                'bet_type' => $data['bet_type'],
                'bet_selection' => (string) $data['bet_selection'],
                'unit_price' => $unitPrice,
                'multiplier' => $multiplier,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            // Update period counters
            $period = $current['period_model'];
            $period->total_bets_count += 1;
            $period->total_bet_amount = bcadd((string) $period->total_bet_amount, (string) $totalAmount, 2);
            $period->save();

            // Record transaction
            Transaction::create([
                'user_id' => $lockedUser->id,
                'type' => 'bet_placed',
                'amount' => $totalAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => 'completed',
                'admin_notes' => "Placed WinGo {$gameType} bet on {$data['bet_selection']} (Period #{$current['period_id']})",
            ]);

            return [
                'success' => true,
                'message' => "Bet placed successfully on {$data['bet_selection']} for ₹".number_format($totalAmount, 2),
                'new_balance' => (float) $balanceAfter,
                'bet_id' => $bet->id,
                'period_id' => $current['period_id'],
            ];
        });
    }

    /**
     * Calculate payout exposure for all 10 potential winning numbers (0-9).
     */
    public static function calculateExposure(WingoPeriod $period): array
    {
        $bets = WingoBet::where('game_type', $period->game_type)
            ->where('period_id', $period->period_id)
            ->where('status', 'pending')
            ->get();

        $totalPool = (float) $period->total_bet_amount;
        $exposure = [];
        for ($num = 0; $num <= 9; $num++) {
            $payout = '0.00';
            $winningBetsCount = 0;
            $attr = WingoPeriod::getAttributesForNumber($num);

            foreach ($bets as $b) {
                $amt = (string) $b->total_amount;
                $isWinner = false;
                $betMultiplier = 0.00;

                if ($b->bet_type === 'number') {
                    if ((int) $b->bet_selection === $num) {
                        $betMultiplier = 9.00;
                        $isWinner = true;
                    }
                } elseif ($b->bet_type === 'color') {
                    $sel = strtolower($b->bet_selection);
                    if ($sel === 'green') {
                        if (in_array($num, [1, 3, 7, 9], true)) {
                            $betMultiplier = 2.00;
                            $isWinner = true;
                        } elseif ($num === 5) {
                            $betMultiplier = 1.50;
                            $isWinner = true;
                        }
                    } elseif ($sel === 'red') {
                        if (in_array($num, [2, 4, 6, 8], true)) {
                            $betMultiplier = 2.00;
                            $isWinner = true;
                        } elseif ($num === 0) {
                            $betMultiplier = 1.50;
                            $isWinner = true;
                        }
                    } elseif ($sel === 'violet') {
                        if ($num === 0 || $num === 5) {
                            $betMultiplier = 4.50;
                            $isWinner = true;
                        }
                    }
                } elseif ($b->bet_type === 'size') {
                    $sel = strtolower($b->bet_selection);
                    if ($sel === 'big' && $num >= 5) {
                        $betMultiplier = 2.00;
                        $isWinner = true;
                    } elseif ($sel === 'small' && $num <= 4) {
                        $betMultiplier = 2.00;
                        $isWinner = true;
                    }
                } elseif ($b->bet_type === 'combination') {
                    $sel = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '_', $b->bet_selection), '_'));
                    if ($sel === 'green_big' && in_array($num, [7, 9], true)) {
                        $betMultiplier = 4.50;
                        $isWinner = true;
                    } elseif ($sel === 'green_small' && in_array($num, [1, 3], true)) {
                        $betMultiplier = 4.50;
                        $isWinner = true;
                    } elseif ($sel === 'red_big' && in_array($num, [6, 8], true)) {
                        $betMultiplier = 4.50;
                        $isWinner = true;
                    } elseif ($sel === 'red_small' && in_array($num, [2, 4], true)) {
                        $betMultiplier = 4.50;
                        $isWinner = true;
                    } elseif ($sel === 'violet_big' && $num === 5) {
                        $betMultiplier = 9.00;
                        $isWinner = true;
                    } elseif ($sel === 'violet_small' && $num === 0) {
                        $betMultiplier = 9.00;
                        $isWinner = true;
                    }
                }

                if ($isWinner) {
                    $winningBetsCount++;
                    $calc = GamePayoutService::calculate($amt, $betMultiplier);
                    $payout = bcadd($payout, (string) $calc['player_payout'], 2);
                }
            }

            $exposure[$num] = [
                'number' => $num,
                'color' => $attr['color_string'],
                'size' => $attr['size'],
                'count' => $winningBetsCount,
                'payout' => (float) $payout,
                'net_profit' => $totalPool - (float) $payout,
            ];
        }

        return $exposure;
    }

    /**
     * Get list of numbers (0-9) matching a target color and/or size.
     */
    public static function getMatchingNumbers(?string $color = null, ?string $size = null): array
    {
        $color = $color ? strtolower(trim($color)) : null;
        $size = $size ? strtolower(trim($size)) : null;

        // If both color and size are specified (Combination Mode)
        if ($color !== null && $size !== null) {
            if ($color === 'violet') {
                return ($size === 'big') ? [5] : [0];
            }
            if ($color === 'green') {
                return ($size === 'big') ? [7, 9] : [1, 3];
            }
            if ($color === 'red') {
                return ($size === 'big') ? [6, 8] : [2, 4];
            }
        }

        // If only Color is specified
        if ($color !== null && $size === null) {
            if ($color === 'green') {
                return [1, 3, 5, 7, 9];
            }
            if ($color === 'red') {
                return [0, 2, 4, 6, 8];
            }
            if ($color === 'violet') {
                return [0, 5];
            }
        }

        // If only Size is specified
        if ($color === null && $size !== null) {
            return ($size === 'big') ? [5, 6, 7, 8, 9] : [0, 1, 2, 3, 4];
        }

        return range(0, 9);
    }
}
