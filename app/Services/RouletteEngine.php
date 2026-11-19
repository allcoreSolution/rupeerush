<?php

namespace App\Services;

use App\Models\AdminSetting;
use App\Models\RouletteBet;
use App\Models\RouletteRound;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RouletteEngine
{
    public const DEFAULT_ROUND_DURATION = 30; // 30-second cycles: 20s betting + 7s spinning + 3s result reveal

    public const BETTING_DURATION = 20; // 20 seconds for betting (countdown: 20, 19, 18... 1)

    public const SPIN_DURATION = 7; // 7 seconds of wheel spinning (BETS CLOSED)

    public const RESULT_DURATION = 3; // 3 seconds showing winning number result overlay before next round

    public const LOCK_COUNTDOWN = 10; // Lock bets when remaining seconds <= 10 (7s spin + 3s result)

    /**
     * Physical wheel numbers in exact European Roulette sequence.
     */
    public const WHEEL_ORDER = [
        0, 32, 15, 19, 4, 21, 2, 25, 17, 34, 6, 27, 13, 36, 11, 30, 8, 23, 10, 5,
        24, 16, 33, 1, 20, 14, 31, 9, 22, 18, 29, 7, 28, 12, 35, 3, 26,
    ];

    /**
     * 18 Red pockets.
     */
    public const RED_NUMBERS = [
        1, 3, 5, 7, 9, 12, 14, 16, 18, 19, 21, 23, 25, 27, 30, 32, 34, 36,
    ];

    /**
     * 18 Black pockets.
     */
    public const BLACK_NUMBERS = [
        2, 4, 6, 8, 10, 11, 13, 15, 17, 20, 22, 24, 26, 28, 29, 31, 33, 35,
    ];

    /**
     * Odds & payouts for all standard roulette bet types.
     */
    public const BET_ODDS = [
        'straight' => 36.00, // 35:1 + original stake (e.g. 0 Green pays 36x)
        'red' => 5.00,      // 5x as requested by user
        'black' => 7.00,    // 7x as requested by user
        'even' => 2.00,     // 1:1
        'odd' => 2.00,      // 1:1
        'low' => 2.00,      // 1-18 (1:1)
        'high' => 2.00,     // 19-36 (1:1)
        'dozen1' => 3.00,   // 1-12 (2:1)
        'dozen2' => 3.00,   // 13-24 (2:1)
        'dozen3' => 3.00,   // 25-36 (2:1)
        'col1' => 3.00,     // 1st column (2:1)
        'col2' => 3.00,     // 2nd column (2:1)
        'col3' => 3.00,     // 3rd column (2:1)
        'split' => 18.00,   // 2 numbers (17:1)
        'street' => 12.00,  // 3 numbers (11:1)
        'corner' => 9.00,   // 4 numbers (8:1)
        'line' => 6.00,     // 6 numbers (5:1)
    ];

    /**
     * Retrieve dynamic bet odds merged with AdminSetting multipliers.
     */
    public static function getBetOdds(?AdminSetting $setting = null): array
    {
        $setting ??= AdminSetting::current();
        $odds = self::BET_ODDS;

        if ($setting && ! empty($setting->roulette_red_multiplier)) {
            $odds['red'] = (float) $setting->roulette_red_multiplier;
        }

        if ($setting && ! empty($setting->roulette_black_multiplier)) {
            $odds['black'] = (float) $setting->roulette_black_multiplier;
        }

        return $odds;
    }

    /**
     * Retrieve accurate payout multiplier for a specific bet type & spot value.
     * - Number 0 (Green): 36x Payout
     * - Red numbers (1, 3, 5, 7, 9, 12, 14, 16, 18, 19, 21, 23, 25, 27, 30, 32, 34, 36): 5x Payout (or admin setting)
     * - Black numbers (2, 4, 6, 8, 10, 11, 13, 15, 17, 20, 22, 24, 26, 28, 29, 31, 33, 35): 7x Payout (or admin setting)
     */
    public static function getOddsForBet(string $type, string $value, ?AdminSetting $setting = null): float
    {
        $setting ??= AdminSetting::current();
        $redMult = (float) ($setting->roulette_red_multiplier ?: 5.00);
        $blackMult = (float) ($setting->roulette_black_multiplier ?: 7.00);

        if ($type === 'straight') {
            $num = (int) $value;
            if ($num === 0) {
                return 36.00;
            }
            if (in_array($num, self::RED_NUMBERS, true)) {
                return $redMult;
            }
            if (in_array($num, self::BLACK_NUMBERS, true)) {
                return $blackMult;
            }

            return 36.00;
        }

        if ($type === 'red') {
            return $redMult;
        }

        if ($type === 'black') {
            return $blackMult;
        }

        $oddsTable = self::getBetOdds($setting);

        return (float) ($oddsTable[$type] ?? (self::BET_ODDS[$type] ?? 1.00));
    }

    /**
     * Generate biased winning number according to configured weights.
     * Default pattern: 70% Red, 29% Black, 1% Green (rare).
     */
    public static function generateBiasedWinningNumber(?AdminSetting $setting = null): int
    {
        $setting ??= AdminSetting::current();
        $redWeight = (float) ($setting->roulette_red_weight ?? 70.00);
        $blackWeight = (float) ($setting->roulette_black_weight ?? 29.00);
        $greenWeight = (float) ($setting->roulette_green_weight ?? 1.00);

        $totalWeight = $redWeight + $blackWeight + $greenWeight;
        if ($totalWeight <= 0) {
            $redWeight = 70.00;
            $blackWeight = 29.00;
            $greenWeight = 1.00;
            $totalWeight = 100.00;
        }

        // Random roll scaled up by 100 for high precision
        $rand = random_int(1, (int) round($totalWeight * 100));
        $greenCutoff = (int) round($greenWeight * 100);
        $blackCutoff = $greenCutoff + (int) round($blackWeight * 100);

        if ($rand <= $greenCutoff) {
            return 0;
        }

        if ($rand <= $blackCutoff) {
            return self::BLACK_NUMBERS[array_rand(self::BLACK_NUMBERS)];
        }

        return self::RED_NUMBERS[array_rand(self::RED_NUMBERS)];
    }

    /**
     * Pick a winning number from candidate numbers respecting the configured color distribution.
     *
     * @param  array<int>  $candidates
     */
    public static function pickBiasedCandidate(array $candidates): int
    {
        if (empty($candidates)) {
            return self::generateBiasedWinningNumber();
        }

        if (count($candidates) === 1) {
            return (int) $candidates[0];
        }

        $redCandidates = array_values(array_intersect($candidates, self::RED_NUMBERS));
        $blackCandidates = array_values(array_intersect($candidates, self::BLACK_NUMBERS));
        $hasZero = in_array(0, $candidates, true);

        $setting = AdminSetting::current();
        $redWeight = (float) ($setting->roulette_red_weight ?? 70.00);
        $blackWeight = (float) ($setting->roulette_black_weight ?? 29.00);
        $greenWeight = (float) ($setting->roulette_green_weight ?? 1.00);

        $pool = [];
        if (! empty($redCandidates)) {
            $pool['red'] = $redWeight;
        }
        if (! empty($blackCandidates)) {
            $pool['black'] = $blackWeight;
        }
        if ($hasZero) {
            $pool['green'] = $greenWeight;
        }

        $total = array_sum($pool);
        if ($total <= 0) {
            return (int) $candidates[array_rand($candidates)];
        }

        $rand = random_int(1, (int) round($total * 100));
        $cumulative = 0;
        foreach ($pool as $color => $w) {
            $cumulative += (int) round($w * 100);
            if ($rand <= $cumulative) {
                if ($color === 'red') {
                    return (int) $redCandidates[array_rand($redCandidates)];
                }
                if ($color === 'black') {
                    return (int) $blackCandidates[array_rand($blackCandidates)];
                }

                return 0;
            }
        }

        return (int) $candidates[array_rand($candidates)];
    }

    /**
     * Get number metadata (color, even/odd, high/low, dozen, column, wheel angle).
     */
    public static function getNumberDetails(int $number): array
    {
        $color = 'green';
        if (in_array($number, self::RED_NUMBERS, true)) {
            $color = 'red';
        } elseif (in_array($number, self::BLACK_NUMBERS, true)) {
            $color = 'black';
        }

        $wheelIndex = array_search($number, self::WHEEL_ORDER, true);
        if ($wheelIndex === false) {
            $wheelIndex = 0;
        }

        $dozen = null;
        $column = null;
        $isEven = null;
        $isHigh = null;

        if ($number > 0) {
            $isEven = ($number % 2 === 0);
            $isHigh = ($number >= 19 && $number <= 36);
            $dozen = (int) ceil($number / 12);
            $column = (($number - 1) % 3) + 1;
        }

        // Each pocket occupies 360 / 37 degrees = 9.7297 degrees
        $angle = round($wheelIndex * (360.0 / 37.0), 3);

        return [
            'number' => $number,
            'color' => $color,
            'is_even' => $isEven,
            'is_high' => $isHigh,
            'dozen' => $dozen,
            'column' => $column,
            'wheel_index' => $wheelIndex,
            'angle' => $angle,
        ];
    }

    /**
     * Get or initialize the currently active round synchronized across all users.
     */
    public static function getCurrentRound(): array
    {
        $setting = AdminSetting::current();
        $duration = (int) ($setting->roulette_round_duration ?: self::DEFAULT_ROUND_DURATION);

        $now = Carbon::now('Asia/Kolkata');
        $todayStart = $now->copy()->startOfDay();
        $secondsSinceMidnight = ($now->hour * 3600) + ($now->minute * 60) + $now->second;

        $slotIndex = (int) floor($secondsSinceMidnight / $duration) + 1;
        $roundId = 'ROU'.$now->format('ymd').str_pad((string) $slotIndex, 5, '0', STR_PAD_LEFT);

        $startSeconds = ($slotIndex - 1) * $duration;
        $endSeconds = $slotIndex * $duration;
        $startTime = $todayStart->copy()->addSeconds($startSeconds);
        $endTime = $todayStart->copy()->addSeconds($endSeconds);

        $remainingSeconds = max(0, $endSeconds - $secondsSinceMidnight);

        // Exact Phase breakdown:
        // When remaining > 8s: Betting phase (betting_seconds: remaining - 8, counts 10 down to 1)
        // When remaining 4..8s: Spinning phase (spin_seconds: remaining - 3, counts 5 down to 1)
        // When remaining <= 3s: Result reveal phase (result_seconds: remaining, counts 3 down to 1)
        $bettingDuration = max(1, $duration - self::LOCK_COUNTDOWN); // 20s
        $isLocked = ($remainingSeconds <= self::LOCK_COUNTDOWN);

        $bettingSeconds = max(0, $remainingSeconds - self::LOCK_COUNTDOWN);
        $spinningSeconds = ($remainingSeconds <= self::LOCK_COUNTDOWN && $remainingSeconds > self::RESULT_DURATION)
            ? ($remainingSeconds - self::RESULT_DURATION)
            : 0;
        $resultSeconds = min(self::RESULT_DURATION, $remainingSeconds);

        $currentPhase = 'betting';
        if ($remainingSeconds <= self::RESULT_DURATION) {
            $currentPhase = 'result';
        } elseif ($isLocked) {
            $currentPhase = 'spinning';
        }

        $roundStatus = $isLocked ? 'spinning' : 'betting';

        // 1. Fetch or create active round with provably fair server seed
        $serverSeed = bin2hex(random_bytes(16));
        $seedHash = hash('sha256', $serverSeed);

        $hasManual = ($setting->roulette_manual_number !== null);
        $manualNum = $hasManual ? (int) $setting->roulette_manual_number : null;

        $round = RouletteRound::firstOrCreate(
            ['round_id' => $roundId],
            [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $roundStatus,
                'seed_hash' => $seedHash,
                'server_seed' => $serverSeed,
                'is_manual' => $hasManual,
                'manual_number' => $manualNum,
            ]
        );

        if ($hasManual && ! $round->is_manual) {
            $round->is_manual = true;
            $round->manual_number = $manualNum;
            $round->save();
        }

        // 2. Determine or settle current round when it enters spinning/result phase
        if ($isLocked && $round->status !== 'completed') {
            self::settleRound($round);
        }

        // 3. Settle any past pending rounds atomically
        self::settlePreviousPendingRounds($roundId);

        if ($round->status !== $roundStatus && $round->status !== 'completed') {
            $round->status = $roundStatus;
            $round->save();
        }

        // 3. Get recent history of completed rounds (last 20)
        $history = RouletteRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get(['round_id', 'winning_number', 'winning_color', 'is_even', 'is_high', 'dozen', 'column', 'created_at']);

        // 4. Calculate table bets summary for active round
        $betsSummary = RouletteBet::where('round_id', $roundId)
            ->selectRaw('bet_type, bet_value, COUNT(*) as count, SUM(bet_amount) as total_amount')
            ->groupBy('bet_type', 'bet_value')
            ->get();

        return [
            'round_id' => $roundId,
            'round_model' => $round,
            'duration' => $duration,
            'betting_duration' => $bettingDuration,
            'spin_duration' => self::SPIN_DURATION,
            'result_duration' => self::RESULT_DURATION,
            'remaining_seconds' => $remainingSeconds,
            'betting_seconds' => $bettingSeconds,
            'spinning_seconds' => $spinningSeconds,
            'result_seconds' => $resultSeconds,
            'phase' => $currentPhase,
            'is_locked' => $isLocked,
            'status' => $round->status,
            'start_time' => $startTime->toIso8601String(),
            'end_time' => $endTime->toIso8601String(),
            'seed_hash' => $round->seed_hash,
            'winning_number' => $round->winning_number,
            'winning_color' => $round->winning_color,
            'history' => $history,
            'bets_summary' => $betsSummary,
            'wheel_order' => self::WHEEL_ORDER,
            'red_numbers' => self::RED_NUMBERS,
            'black_numbers' => self::BLACK_NUMBERS,
        ];
    }

    /**
     * Settle any older rounds that have expired and need determination.
     */
    public static function settlePreviousPendingRounds(string $currentRoundId): void
    {
        $pendingRounds = RouletteRound::where('round_id', '!=', $currentRoundId)
            ->whereIn('status', ['betting', 'spinning'])
            ->lockForUpdate()
            ->get();

        foreach ($pendingRounds as $lockedRound) {
            self::settleRound($lockedRound);
        }
    }

    /**
     * Settle a specific roulette round, pick winning number, credit winning bets.
     */
    public static function settleRound(RouletteRound $round): void
    {
        if ($round->status === 'completed') {
            return;
        }

        DB::transaction(function () use ($round) {
            $setting = AdminSetting::current();
            $mode = $setting->roulette_control_mode ?: 'fair';

            // 1. Determine winning number
            $winningNumber = null;

            $hasBets = RouletteBet::where('round_id', $round->round_id)->where('status', 'pending')->exists();
            $shouldHouseWin = $hasBets && $setting->shouldHouseWin();

            if ($round->is_manual && $round->manual_number !== null) {
                $winningNumber = (int) $round->manual_number;
            } elseif ($setting->roulette_manual_number !== null) {
                $winningNumber = (int) $setting->roulette_manual_number;
            } elseif ($mode === 'admin_profit' || ($hasBets && $shouldHouseWin)) {
                // 75% - 80% House Profit: Pocket with lowest house liability
                $winningNumber = self::determineLeastLiabilityNumber($round->round_id);
            } else {
                // 20% - 25% Choti Winning / Natural: Biased Pattern (70% Red, 29% Black, 1% Green)
                $winningNumber = self::generateBiasedWinningNumber($setting);
            }

            // Once manual override is applied to settle this manual round, clear it so subsequent rounds run naturally
            if ($round->is_manual && $setting->roulette_manual_number !== null) {
                $setting->update([
                    'roulette_manual_number' => null,
                    'roulette_control_mode' => 'fair',
                ]);
            }

            $details = self::getNumberDetails($winningNumber);

            $round->winning_number = $winningNumber;
            $round->winning_color = $details['color'];
            $round->is_even = $details['is_even'];
            $round->is_high = $details['is_high'];
            $round->dozen = $details['dozen'];
            $round->column = $details['column'];
            $round->status = 'completed';

            // 2. Settle all pending bets on this round
            $bets = RouletteBet::where('round_id', $round->round_id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->get();

            $totalPayout = 0.00;
            $oddsTable = self::getBetOdds($setting);

            foreach ($bets as $bet) {
                $betWon = self::evaluateBetOutcome($bet->bet_type, $bet->bet_value, $winningNumber);

                if ($betWon) {
                    $multiplier = (float) ($bet->odds ?: self::getOddsForBet($bet->bet_type, $bet->bet_value, $setting));
                    $payoutDetails = GamePayoutService::calculate($bet->bet_amount, $multiplier);
                    $payout = (float) $payoutDetails['player_payout'];
                    $netProfit = (float) $payoutDetails['net_profit'];
                    $commissionAmount = (float) $payoutDetails['commission_amount'];

                    $bet->status = 'won';
                    $bet->payout_amount = $payout;
                    $bet->net_profit = $netProfit;
                    $bet->commission_amount = $commissionAmount;
                    $bet->save();

                    $user = User::where('id', $bet->user_id)->lockForUpdate()->first();
                    if ($user) {
                        $balBefore = (float) $user->balance;
                        $balAfter = round($balBefore + $payout, 2);
                        $user->balance = $balAfter;
                        $user->save();

                        $feeNote = ($commissionAmount > 0) ? " (Fee: ₹{$commissionAmount})" : '';
                        Transaction::create([
                            'user_id' => $user->id,
                            'type' => 'bet_won',
                            'amount' => $payout,
                            'balance_before' => $balBefore,
                            'balance_after' => $balAfter,
                            'status' => 'approved',
                            'admin_notes' => "Roulette Won Round: {$round->round_id} | #{$winningNumber} {$details['color']} ({$bet->bet_type}){$feeNote}",
                        ]);
                    }

                    $totalPayout += $payout;
                } else {
                    $bet->status = 'lost';
                    $bet->payout_amount = 0.00;
                    $bet->net_profit = -1 * (float) $bet->bet_amount;
                    $bet->commission_amount = 0.00;
                    $bet->save();
                }
            }

            $round->total_bets_count = $bets->count();
            $round->total_bet_amount = (float) $bets->sum('bet_amount');
            $round->total_payout_amount = $totalPayout;
            $round->save();
        });
    }

    /**
     * Check if a specific bet wins given the winning number.
     */
    public static function evaluateBetOutcome(string $betType, string $betValue, int $winningNumber): bool
    {
        // On 0 (Green House Edge): Only straight bet on 0 wins. All outside bets lose.
        if ($winningNumber === 0) {
            return $betType === 'straight' && (int) $betValue === 0;
        }

        switch ($betType) {
            case 'straight':
                return (int) $betValue === $winningNumber;

            case 'red':
                return in_array($winningNumber, self::RED_NUMBERS, true);

            case 'black':
                return in_array($winningNumber, self::BLACK_NUMBERS, true);

            case 'even':
                return $winningNumber % 2 === 0;

            case 'odd':
                return $winningNumber % 2 !== 0;

            case 'low': // 1-18
                return $winningNumber >= 1 && $winningNumber <= 18;

            case 'high': // 19-36
                return $winningNumber >= 19 && $winningNumber <= 36;

            case 'dozen1': // 1-12
                return $winningNumber >= 1 && $winningNumber <= 12;

            case 'dozen2': // 13-24
                return $winningNumber >= 13 && $winningNumber <= 24;

            case 'dozen3': // 25-36
                return $winningNumber >= 25 && $winningNumber <= 36;

            case 'col1': // Column 1: 1, 4, 7, 10, 13, 16, 19, 22, 25, 28, 31, 34
                return ($winningNumber - 1) % 3 === 0;

            case 'col2': // Column 2: 2, 5, 8, 11, 14, 17, 20, 23, 26, 29, 32, 35
                return ($winningNumber - 1) % 3 === 1;

            case 'col3': // Column 3: 3, 6, 9, 12, 15, 18, 21, 24, 27, 30, 33, 36
                return ($winningNumber - 1) % 3 === 2;

            case 'split':
            case 'street':
            case 'corner':
            case 'line':
                $numbers = array_map('trim', explode(',', $betValue));

                return in_array((string) $winningNumber, $numbers, true);

            default:
                return false;
        }
    }

    /**
     * Calculate least liability winning number for admin house protection mode.
     */
    public static function determineLeastLiabilityNumber(string $roundId): int
    {
        $bets = RouletteBet::where('round_id', $roundId)
            ->where('status', 'pending')
            ->get();

        if ($bets->isEmpty()) {
            return self::generateBiasedWinningNumber();
        }

        $oddsTable = self::getBetOdds();
        $minLiability = PHP_FLOAT_MAX;
        $bestNumbers = [];

        for ($candidate = 0; $candidate <= 36; $candidate++) {
            $liability = 0.0;
            foreach ($bets as $b) {
                if (self::evaluateBetOutcome($b->bet_type, $b->bet_value, $candidate)) {
                    $multiplier = (float) ($b->odds ?: self::getOddsForBet($b->bet_type, $b->bet_value, $setting));
                    $liability += ((float) $b->bet_amount * $multiplier);
                }
            }

            if ($liability < $minLiability) {
                $minLiability = $liability;
                $bestNumbers = [$candidate];
            } elseif ($liability == $minLiability) {
                $bestNumbers[] = $candidate;
            }
        }

        return self::pickBiasedCandidate($bestNumbers);
    }

    /**
     * Place batch bets on the active roulette round with atomic wallet locking.
     */
    public static function placeBets(User $user, array $betsList): array
    {
        if (empty($betsList)) {
            return ['success' => false, 'message' => 'No bets selected. Please place chips on the table.'];
        }

        $roundInfo = self::getCurrentRound();
        if ($roundInfo['is_locked'] || $roundInfo['status'] !== 'betting') {
            return ['success' => false, 'message' => 'Bets are closed for this round. Wheel is spinning!'];
        }

        $setting = AdminSetting::current();
        $minBet = (float) ($setting->roulette_min_bet ?: 10.00);
        $maxBet = (float) ($setting->roulette_max_bet ?: 25000.00);
        $oddsTable = self::getBetOdds($setting);

        // Calculate total stake required
        $totalStake = 0.00;
        $validatedBets = [];

        foreach ($betsList as $bet) {
            $type = trim((string) ($bet['type'] ?? ''));
            $value = trim((string) ($bet['value'] ?? ''));
            $amount = round((float) ($bet['amount'] ?? 0), 2);

            if (! isset($oddsTable[$type])) {
                return ['success' => false, 'message' => "Invalid bet type: {$type}"];
            }

            if ($amount < $minBet) {
                return ['success' => false, 'message' => "Minimum bet per spot is ₹{$minBet}."];
            }

            if ($amount > $maxBet) {
                return ['success' => false, 'message' => "Maximum bet per spot is ₹{$maxBet}."];
            }

            $totalStake += $amount;
            $validatedBets[] = [
                'type' => $type,
                'value' => $value,
                'amount' => $amount,
                'odds' => self::getOddsForBet($type, $value, $setting),
            ];
        }

        return DB::transaction(function () use ($user, $totalStake, $validatedBets, $roundInfo) {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            if (! $lockedUser || (float) $lockedUser->balance < $totalStake) {
                return [
                    'success' => false,
                    'message' => 'Insufficient wallet balance. Please deposit funds.',
                    'balance' => (float) ($lockedUser?->balance ?? 0),
                ];
            }

            $balBefore = (float) $lockedUser->balance;
            $balAfter = round($balBefore - $totalStake, 2);
            $lockedUser->balance = $balAfter;
            $lockedUser->save();

            Transaction::create([
                'user_id' => $lockedUser->id,
                'type' => 'bet_placed',
                'amount' => $totalStake,
                'balance_before' => $balBefore,
                'balance_after' => $balAfter,
                'status' => 'approved',
                'admin_notes' => 'Placed '.count($validatedBets)." Roulette bets on round {$roundInfo['round_id']}",
            ]);

            $createdBets = [];
            foreach ($validatedBets as $vb) {
                $betModel = RouletteBet::create([
                    'bet_id' => 'ROUB_'.strtoupper(Str::random(12)),
                    'round_id' => $roundInfo['round_id'],
                    'user_id' => $lockedUser->id,
                    'bet_type' => $vb['type'],
                    'bet_value' => $vb['value'],
                    'bet_amount' => $vb['amount'],
                    'odds' => $vb['odds'],
                    'payout_amount' => 0.00,
                    'net_profit' => 0.00,
                    'status' => 'pending',
                ]);
                $createdBets[] = $betModel;
            }

            return [
                'success' => true,
                'message' => 'Bets placed successfully!',
                'new_balance' => $balAfter,
                'total_stake' => $totalStake,
                'round_id' => $roundInfo['round_id'],
                'bets' => $createdBets,
            ];
        });
    }

    /**
     * Get user recent roulette bets with win/loss status.
     */
    public static function getUserBets(User $user, int $limit = 25): array
    {
        return RouletteBet::where('user_id', $user->id)
            ->with('round')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
