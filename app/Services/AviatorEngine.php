<?php

namespace App\Services;

use App\Models\AdminSetting;
use App\Models\AviatorBet;
use App\Models\AviatorRound;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AviatorEngine
{
    /**
     * Duration in seconds for players to place bets before the plane takes off.
     */
    public const BETTING_DURATION = 7;

    /**
     * Duration in seconds showing the "FLEW AWAY" crash screen before the next round begins.
     */
    public const CRASH_COOLDOWN = 3;

    /**
     * Exponential curve growth constant for multiplier: M(t) = e^(GROWTH_RATE * t)
     */
    public const GROWTH_RATE = 0.065;

    /**
     * Calculate multiplier at elapsed flight time in seconds.
     */
    public static function calculateMultiplierAtTime(float $flightSeconds, float $crashMultiplier): float
    {
        if ($flightSeconds <= 0) {
            return 1.00;
        }

        $multiplier = round(exp(self::GROWTH_RATE * $flightSeconds), 2);

        return min($crashMultiplier, max(1.00, $multiplier));
    }

    /**
     * Calculate how many seconds the flight lasts for a given target crash multiplier.
     */
    public static function calculateFlightDuration(float $crashMultiplier): float
    {
        if ($crashMultiplier <= 1.00) {
            return 0.50;
        }

        $seconds = log($crashMultiplier) / self::GROWTH_RATE;

        return max(0.50, round($seconds, 2));
    }

    /**
     * Generate crash multiplier based on realistic RTP probability or admin override.
     */
    public static function generateCrashMultiplier(?float $manualMultiplier = null): float
    {
        if ($manualMultiplier !== null && $manualMultiplier >= 1.00) {
            return round($manualMultiplier, 2);
        }

        $rand = mt_rand(1, 10000) / 100.0; // 0.01 to 100.00%

        // ~8% instant crash (1.00x - 1.15x)
        if ($rand <= 8.0) {
            return round(1.00 + (mt_rand(0, 15) / 100.0), 2);
        }

        // ~48% small multiplier (1.16x - 2.00x)
        if ($rand <= 56.0) {
            return round(1.16 + (mt_rand(0, 84) / 100.0), 2);
        }

        // ~26% medium multiplier (2.01x - 5.00x)
        if ($rand <= 82.0) {
            return round(2.01 + (mt_rand(0, 299) / 100.0), 2);
        }

        // ~10% high multiplier (5.01x - 15.00x)
        if ($rand <= 92.0) {
            return round(5.01 + (mt_rand(0, 999) / 100.0), 2);
        }

        // ~6% very high multiplier (15.01x - 50.00x)
        if ($rand <= 98.0) {
            return round(15.01 + (mt_rand(0, 3499) / 100.0), 2);
        }

        // ~2% moon multiplier (50.01x - 150.00x)
        return round(50.01 + (mt_rand(0, 9999) / 100.0), 2);
    }

    /**
     * Dynamically optimize crash multiplier for house profit (75-80% early crash) or player excitement (20-25% win).
     */
    public static function optimizeFlightCrashMultiplier(AviatorRound $round): void
    {
        if ($round->is_manual) {
            return;
        }

        $bets = AviatorBet::where('round_id', $round->round_id)
            ->where('status', 'pending')
            ->get();

        if ($bets->isEmpty()) {
            return;
        }

        $setting = AdminSetting::current();
        $shouldHouseWin = $setting->shouldHouseWin();

        if ($shouldHouseWin) {
            // 75% - 80% House Profit: Crash early so house collects stakes
            $autoCashouts = $bets->pluck('auto_cashout')->filter()->map(fn ($v) => (float) $v)->values();
            $minAuto = $autoCashouts->isNotEmpty() ? $autoCashouts->min() : 2.00;

            if ($minAuto > 1.25) {
                $maxCrash = min($minAuto - 0.05, 1.40);
                $crashMultiplier = round(mt_rand(105, (int) round($maxCrash * 100)) / 100.0, 2);
            } else {
                $crashMultiplier = round(mt_rand(102, 118) / 100.0, 2);
            }
        } else {
            // 20% - 25% Choti Winning: Let plane climb to 1.80x - 4.50x
            $crashMultiplier = round(mt_rand(180, 450) / 100.0, 2);
        }

        $flightDurationSeconds = self::calculateFlightDuration($crashMultiplier);
        $crashTime = $round->flight_start_time->copy()->addMilliseconds((int) ($flightDurationSeconds * 1000));
        $endTime = $crashTime->copy()->addSeconds(self::CRASH_COOLDOWN);

        $round->crash_multiplier = $crashMultiplier;
        $round->crash_time = $crashTime;
        $round->end_time = $endTime;
        $round->seed_hash = hash('sha256', $round->round_id.':'.$crashMultiplier.':'.config('app.key'));
        $round->save();
    }

    /**
     * Get or initialize current active round, auto-advancing the state machine.
     */
    public static function getCurrentRound(): array
    {
        $now = Carbon::now();

        // Find the latest active (uncompleted) round
        $activeRound = AviatorRound::where('status', '!=', 'completed')
            ->orderBy('id', 'desc')
            ->first();

        // If no round exists, or the active round has exceeded its end_time
        if (! $activeRound || $now->greaterThanOrEqualTo($activeRound->end_time)) {
            if ($activeRound) {
                self::settleRound($activeRound);
            }

            $activeRound = self::createNewRound();
        }

        // Synchronize and determine current status
        $status = 'betting';
        $currentMultiplier = 1.00;
        $remainingBettingSeconds = 0.0;
        $remainingCooldownSeconds = 0.0;
        $flightElapsedSeconds = 0.0;

        if ($now->lessThan($activeRound->flight_start_time)) {
            $status = 'betting';
            $remainingBettingSeconds = max(0.0, round(($activeRound->flight_start_time->getTimestampMs() - $now->getTimestampMs()) / 1000, 1));
            $currentMultiplier = 1.00;

            if ($activeRound->status !== 'betting') {
                $activeRound->status = 'betting';
                $activeRound->save();
            }
        } elseif ($now->lessThan($activeRound->crash_time)) {
            $status = 'flying';

            if ($activeRound->status !== 'flying') {
                self::optimizeFlightCrashMultiplier($activeRound);
                $activeRound->status = 'flying';
                $activeRound->save();
            }

            $flightElapsedSeconds = max(0.0, round(($now->getTimestampMs() - $activeRound->flight_start_time->getTimestampMs()) / 1000, 2));
            $currentMultiplier = self::calculateMultiplierAtTime($flightElapsedSeconds, (float) $activeRound->crash_multiplier);

            // Execute auto-cashouts for matching bets
            self::processAutoCashouts($activeRound, $currentMultiplier);
        } elseif ($now->lessThan($activeRound->end_time)) {
            $status = 'crashed';
            $remainingCooldownSeconds = max(0.0, round(($activeRound->end_time->getTimestampMs() - $now->getTimestampMs()) / 1000, 1));
            $currentMultiplier = (float) $activeRound->crash_multiplier;

            if ($activeRound->status !== 'crashed') {
                $activeRound->status = 'crashed';
                $activeRound->save();

                // Process un-cashed bets as lost
                self::processCrashedBets($activeRound);
            }
        } else {
            // Reached or passed end_time
            self::settleRound($activeRound);

            return self::getCurrentRound();
        }

        return [
            'round_model' => $activeRound,
            'round_id' => $activeRound->round_id,
            'status' => $status,
            'current_multiplier' => $currentMultiplier,
            'crash_multiplier' => (float) $activeRound->crash_multiplier,
            'flight_elapsed_seconds' => $flightElapsedSeconds,
            'remaining_betting_seconds' => $remainingBettingSeconds,
            'betting_duration' => self::BETTING_DURATION,
            'remaining_cooldown_seconds' => $remainingCooldownSeconds,
            'crash_cooldown' => self::CRASH_COOLDOWN,
            'start_time' => $activeRound->start_time->toISOString(),
            'flight_start_time' => $activeRound->flight_start_time->toISOString(),
            'crash_time' => $activeRound->crash_time->toISOString(),
            'end_time' => $activeRound->end_time->toISOString(),
            'server_time' => $now->toISOString(),
            'server_timestamp' => $now->getPreciseTimestamp(3),
        ];
    }

    /**
     * Create a brand new round.
     */
    public static function createNewRound(?float $forcedMultiplier = null): AviatorRound
    {
        $now = Carbon::now();
        $roundId = 'AV'.Carbon::now()->format('ymdHis').mt_rand(10, 99);

        // Check if a standing manual multiplier was configured in admin settings or passed directly
        if ($forcedMultiplier === null) {
            $setting = AdminSetting::current();
            if ($setting && $setting->aviator_manual_multiplier !== null) {
                $forcedMultiplier = (float) $setting->aviator_manual_multiplier;
            }
        }

        $manualMultiplier = $forcedMultiplier;
        $isManual = ($manualMultiplier !== null && $manualMultiplier >= 1.00);

        $crashMultiplier = self::generateCrashMultiplier($manualMultiplier);
        $flightDurationSeconds = self::calculateFlightDuration($crashMultiplier);

        $startTime = $now->copy();
        $flightStartTime = $startTime->copy()->addSeconds(self::BETTING_DURATION);
        $crashTime = $flightStartTime->copy()->addMilliseconds((int) ($flightDurationSeconds * 1000));
        $endTime = $crashTime->copy()->addSeconds(self::CRASH_COOLDOWN);

        $seedHash = hash('sha256', $roundId.':'.$crashMultiplier.':'.config('app.key'));

        return AviatorRound::create([
            'round_id' => $roundId,
            'start_time' => $startTime,
            'flight_start_time' => $flightStartTime,
            'crash_time' => $crashTime,
            'end_time' => $endTime,
            'crash_multiplier' => $crashMultiplier,
            'status' => 'betting',
            'is_manual' => $isManual,
            'manual_crash_multiplier' => $isManual ? $manualMultiplier : null,
            'seed_hash' => $seedHash,
            'total_bets_count' => 0,
            'total_bet_amount' => 0.00,
            'total_payout_amount' => 0.00,
        ]);
    }

    /**
     * Place bet for a specific slot (1 or 2).
     */
    public static function placeBet(User $user, int $betSlot, float $amount, ?float $autoCashout = null): array
    {
        if (! in_array($betSlot, [1, 2], true)) {
            return ['success' => false, 'message' => 'Invalid bet slot specified.'];
        }

        $setting = AdminSetting::current();
        $minBet = (float) ($setting->aviator_min_bet ?? 10.00);
        $maxBet = (float) ($setting->aviator_max_bet ?? 50000.00);

        if ($amount < $minBet || $amount > $maxBet) {
            return ['success' => false, 'message' => "Bet amount must be between ₹{$minBet} and ₹{$maxBet}."];
        }

        if ($autoCashout !== null && $autoCashout < 1.01) {
            return ['success' => false, 'message' => 'Auto cash out multiplier must be at least 1.01x.'];
        }

        return DB::transaction(function () use ($user, $betSlot, $amount, $autoCashout) {
            $roundData = self::getCurrentRound();
            $round = $roundData['round_model'];

            if ($roundData['status'] !== 'betting') {
                return [
                    'success' => false,
                    'message' => 'Betting closed for current round. Please wait for next round.',
                ];
            }

            // Lock user wallet
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            if (! $lockedUser || (float) $lockedUser->balance < $amount) {
                return ['success' => false, 'message' => 'Insufficient wallet balance. Please deposit funds.'];
            }

            // Check if user already placed a bet on this slot in current round
            $existingBet = AviatorBet::where('round_id', $round->round_id)
                ->where('user_id', $lockedUser->id)
                ->where('bet_slot', $betSlot)
                ->whereIn('status', ['pending', 'cashed_out'])
                ->first();

            if ($existingBet) {
                return ['success' => false, 'message' => "Bet on slot {$betSlot} already active for this round."];
            }

            // Deduct balance
            $balanceBefore = (float) $lockedUser->balance;
            $balanceAfter = round($balanceBefore - $amount, 2);
            $lockedUser->balance = $balanceAfter;
            $lockedUser->save();

            // Record transaction
            Transaction::create([
                'user_id' => $lockedUser->id,
                'type' => 'bet_placed',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => 'completed',
                'admin_notes' => "Placed ₹{$amount} on Aviator Round #{$round->round_id} (Slot {$betSlot})",
            ]);

            // Create bet
            $bet = AviatorBet::create([
                'user_id' => $lockedUser->id,
                'round_id' => $round->round_id,
                'bet_slot' => $betSlot,
                'amount' => $amount,
                'auto_cashout' => $autoCashout ? round($autoCashout, 2) : null,
                'cashout_multiplier' => null,
                'commission_amount' => 0.00,
                'win_amount' => 0.00,
                'status' => 'pending',
            ]);

            // Update round totals
            $round->increment('total_bets_count');
            $round->increment('total_bet_amount', $amount);

            return [
                'success' => true,
                'message' => "Bet of ₹{$amount} placed successfully!",
                'bet' => $bet,
                'new_balance' => $balanceAfter,
            ];
        });
    }

    /**
     * Cancel an active bet before the flight starts.
     */
    public static function cancelBet(User $user, int $betId): array
    {
        return DB::transaction(function () use ($user, $betId) {
            $bet = AviatorBet::where('id', $betId)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $bet) {
                return ['success' => false, 'message' => 'Bet not found.'];
            }

            if ($bet->status !== 'pending') {
                return ['success' => false, 'message' => 'Only pending bets can be cancelled.'];
            }

            $roundData = self::getCurrentRound();
            if ($roundData['round_id'] !== $bet->round_id || $roundData['status'] !== 'betting') {
                return ['success' => false, 'message' => 'Bet cannot be cancelled once the plane has taken off.'];
            }

            // Refund balance
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            $balanceBefore = (float) $lockedUser->balance;
            $refundAmount = (float) $bet->amount;
            $balanceAfter = round($balanceBefore + $refundAmount, 2);

            $lockedUser->balance = $balanceAfter;
            $lockedUser->save();

            $bet->status = 'cancelled';
            $bet->save();

            // Update round metrics
            $round = AviatorRound::where('round_id', $bet->round_id)->first();
            if ($round) {
                $round->decrement('total_bets_count');
                $round->decrement('total_bet_amount', $refundAmount);
            }

            Transaction::create([
                'user_id' => $lockedUser->id,
                'type' => 'admin_adjustment',
                'amount' => $refundAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => 'completed',
                'admin_notes' => "Refunded cancelled Aviator Bet #{$bet->id} on Round #{$bet->round_id}",
            ]);

            return [
                'success' => true,
                'message' => 'Bet cancelled and amount refunded successfully!',
                'new_balance' => $balanceAfter,
            ];
        });
    }

    /**
     * Cash out an active bet during flight.
     */
    public static function cashOut(User $user, int $betSlot, ?float $clientMultiplier = null): array
    {
        return DB::transaction(function () use ($user, $betSlot, $clientMultiplier) {
            $roundData = self::getCurrentRound();
            $round = $roundData['round_model'];

            $currentMultiplier = (float) $roundData['current_multiplier'];
            $crashMultiplier = (float) $round->crash_multiplier;

            // Allow cashout if client submitted a valid multiplier prior to crash (with a small 0.02 grace buffer below crashMultiplier)
            // e.g. for crash 2.00x, cashout up to 1.98x is honoured even if HTTP request hits during crash threshold
            $cashoutTarget = $currentMultiplier;
            if ($clientMultiplier !== null && $clientMultiplier >= 1.01) {
                // Client multiplier must be strictly below crash multiplier
                if ($clientMultiplier < $crashMultiplier) {
                    $cashoutTarget = min($clientMultiplier, $crashMultiplier - 0.01);
                }
            }

            if ($roundData['status'] !== 'flying' && ($roundData['status'] !== 'crashed' || $cashoutTarget >= $crashMultiplier)) {
                return [
                    'success' => false,
                    'message' => ($roundData['status'] === 'crashed')
                        ? 'Plane flew away! Better luck next time.'
                        : 'Game is not in flight.',
                ];
            }

            if ($cashoutTarget >= $crashMultiplier) {
                return [
                    'success' => false,
                    'message' => 'Plane flew away! Better luck next time.',
                ];
            }

            // Lock the active bet
            $bet = AviatorBet::where('round_id', $round->round_id)
                ->where('user_id', $user->id)
                ->where('bet_slot', $betSlot)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();

            if (! $bet) {
                return ['success' => false, 'message' => 'No active pending bet found for this slot.'];
            }

            return self::executeCashout($bet, $cashoutTarget);
        });
    }

    /**
     * Execute atomic cashout for a bet at a determined multiplier.
     */
    public static function executeCashout(AviatorBet $bet, float $multiplier): array
    {
        $payout = GamePayoutService::calculate($bet->amount, $multiplier);
        $winAmount = (float) $payout['player_payout'];
        $commissionAmount = (float) $payout['commission_amount'];

        $bet->status = 'cashed_out';
        $bet->cashout_multiplier = $multiplier;
        $bet->win_amount = $winAmount;
        $bet->commission_amount = $commissionAmount;
        $bet->cashed_out_at = Carbon::now();
        $bet->save();

        // Credit user wallet
        $user = User::where('id', $bet->user_id)->lockForUpdate()->first();
        if ($user) {
            $balanceBefore = (float) $user->balance;
            $balanceAfter = round($balanceBefore + $winAmount, 2);
            $user->balance = $balanceAfter;
            $user->save();

            $feeNote = ($commissionAmount > 0) ? " (Fee: ₹{$commissionAmount})" : '';
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'bet_won',
                'amount' => $winAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => 'completed',
                'admin_notes' => "Won ₹{$winAmount} at {$multiplier}x on Aviator Round #{$bet->round_id} (Slot {$bet->bet_slot}){$feeNote}",
            ]);
        }

        // Increment payout total on round
        $round = AviatorRound::where('round_id', $bet->round_id)->first();
        if ($round) {
            $round->increment('total_payout_amount', $winAmount);
        }

        return [
            'success' => true,
            'message' => "Cashed out at {$multiplier}x! Won ₹{$winAmount}",
            'win_amount' => $winAmount,
            'multiplier' => $multiplier,
            'new_balance' => $user ? (float) $user->balance : 0.00,
            'bet_id' => $bet->id,
        ];
    }

    /**
     * Process auto-cashouts for any bets whose target multiplier has been reached.
     */
    public static function processAutoCashouts(AviatorRound $round, float $currentMultiplier): void
    {
        $eligibleBets = AviatorBet::where('round_id', $round->round_id)
            ->where('status', 'pending')
            ->whereNotNull('auto_cashout')
            ->where('auto_cashout', '<=', $currentMultiplier)
            ->get();

        foreach ($eligibleBets as $bet) {
            DB::transaction(function () use ($bet) {
                $lockedBet = AviatorBet::where('id', $bet->id)
                    ->where('status', 'pending')
                    ->lockForUpdate()
                    ->first();

                if ($lockedBet && (float) $lockedBet->auto_cashout > 0) {
                    self::executeCashout($lockedBet, (float) $lockedBet->auto_cashout);
                }
            });
        }
    }

    /**
     * Process pending bets as lost when a round crashes.
     */
    public static function processCrashedBets(AviatorRound $round): void
    {
        // Auto cashout any pending bets that had an auto_cashout threshold strictly below crash multiplier
        $crashMultiplier = (float) $round->crash_multiplier;
        $unprocessedAutos = AviatorBet::where('round_id', $round->round_id)
            ->where('status', 'pending')
            ->whereNotNull('auto_cashout')
            ->where('auto_cashout', '<', $crashMultiplier)
            ->get();

        foreach ($unprocessedAutos as $autoBet) {
            DB::transaction(function () use ($autoBet) {
                $lockedBet = AviatorBet::where('id', $autoBet->id)
                    ->where('status', 'pending')
                    ->lockForUpdate()
                    ->first();

                if ($lockedBet && (float) $lockedBet->auto_cashout > 0) {
                    self::executeCashout($lockedBet, (float) $lockedBet->auto_cashout);
                }
            });
        }

        AviatorBet::where('round_id', $round->round_id)
            ->where('status', 'pending')
            ->update([
                'status' => 'lost',
                'win_amount' => 0.00,
                'commission_amount' => 0.00,
            ]);
    }

    /**
     * Settle a round completely.
     */
    public static function settleRound(AviatorRound $round): AviatorRound
    {
        if ($round->status === 'completed') {
            return $round;
        }

        self::processCrashedBets($round);

        $round->status = 'completed';
        $round->save();

        return $round;
    }

    /**
     * Get recent completed crash multipliers for the top pill strip.
     */
    public static function getRecentMultipliers(int $limit = 20): array
    {
        return AviatorRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->pluck('crash_multiplier')
            ->map(fn ($m) => (float) $m)
            ->toArray();
    }
}
