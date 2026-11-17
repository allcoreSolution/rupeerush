<?php

namespace App\Services;

use App\Models\AdminSetting;
use App\Models\HorseRaceBet;
use App\Models\HorseRaceRound;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HorseRacingEngine
{
    public const DEFAULT_ROUND_DURATION = 60; // 60-second rapid race rounds

    public const RACING_COUNTDOWN = 15; // Last 15 seconds locked for 3D/2D sprint animation

    public const DEFAULT_RACE_DISTANCE = 1000; // 1000m Sprint

    public const BET_TYPES = ['win', 'place', 'show'];

    /**
     * Default roster of 6 champion horses with jockey silk colors and morning line odds.
     *
     * @return array<int, array{id: int, name: string, jockey: string, gate: int, silk_color: string, silk_pattern: string, rating: int, win_odds: float, place_odds: float, show_odds: float}>
     */
    public static function getHorsesRoster(string $seed = ''): array
    {
        $baseHorses = [
            [
                'id' => 1,
                'name' => 'Thunder Bolt',
                'jockey' => 'R. Hughes',
                'gate' => 1,
                'silk_color' => '#EF4444', // Crimson Red
                'silk_pattern' => 'stripes',
                'coat_color' => '#18181b', // Midnight Black
                'coat_name' => 'Midnight Black',
                'form' => '1-1-2',
                'weight' => '54kg',
                'rating' => 96,
                'win_odds' => 2.60,
                'place_odds' => 1.65,
                'show_odds' => 1.25,
            ],
            [
                'id' => 2,
                'name' => 'Golden Mane',
                'jockey' => 'L. Dettori',
                'gate' => 2,
                'silk_color' => '#F59E0B', // Amber Gold
                'silk_pattern' => 'stars',
                'coat_color' => '#b45309', // Palomino Gold
                'coat_name' => 'Palomino Gold',
                'form' => '2-1-1',
                'weight' => '55kg',
                'rating' => 92,
                'win_odds' => 3.40,
                'place_odds' => 1.90,
                'show_odds' => 1.35,
            ],
            [
                'id' => 3,
                'name' => 'Shadow Runner',
                'jockey' => 'M. Kinane',
                'gate' => 3,
                'silk_color' => '#3B82F6', // Royal Blue
                'silk_pattern' => 'sash',
                'coat_color' => '#475569', // Dapple Grey
                'coat_name' => 'Dapple Grey',
                'form' => '1-3-2',
                'weight' => '53.5kg',
                'rating' => 88,
                'win_odds' => 4.50,
                'place_odds' => 2.25,
                'show_odds' => 1.45,
            ],
            [
                'id' => 4,
                'name' => 'Desert King',
                'jockey' => 'S. De Sousa',
                'gate' => 4,
                'silk_color' => '#10B981', // Emerald Green
                'silk_pattern' => 'diamonds',
                'coat_color' => '#78350f', // Mahogany Bay
                'coat_name' => 'Mahogany Bay',
                'form' => '3-2-1',
                'weight' => '56kg',
                'rating' => 84,
                'win_odds' => 6.00,
                'place_odds' => 2.80,
                'show_odds' => 1.70,
            ],
            [
                'id' => 5,
                'name' => 'Silver Blaze',
                'jockey' => 'K. Fallon',
                'gate' => 5,
                'silk_color' => '#8B5CF6', // Purple
                'silk_pattern' => 'halves',
                'coat_color' => '#3b1808', // Dark Chestnut
                'coat_name' => 'Dark Chestnut',
                'form' => '2-4-2',
                'weight' => '54.5kg',
                'rating' => 80,
                'win_odds' => 8.50,
                'place_odds' => 3.60,
                'show_odds' => 2.10,
            ],
            [
                'id' => 6,
                'name' => 'Royal Challenger',
                'jockey' => 'P. Dobbs',
                'gate' => 6,
                'silk_color' => '#EC4899', // Neon Pink
                'silk_pattern' => 'chevrons',
                'coat_color' => '#854d0e', // Sandy Dun
                'coat_name' => 'Sandy Dun',
                'form' => '4-2-3',
                'weight' => '53kg',
                'rating' => 76,
                'win_odds' => 12.00,
                'place_odds' => 4.80,
                'show_odds' => 2.80,
            ],
        ];

        // Seed-based micro variation to keep odds realistic and dynamic per race
        if ($seed !== '') {
            $hash = crc32($seed);
            foreach ($baseHorses as $index => &$horse) {
                $factor = 1.0 + ((($hash >> ($index * 4)) & 0xF) - 7.5) * 0.015; // +/- 10%
                $horse['win_odds'] = round(max(1.80, $horse['win_odds'] * $factor), 2);
                $horse['place_odds'] = round(max(1.20, 1.0 + ($horse['win_odds'] - 1.0) * 0.40), 2);
                $horse['show_odds'] = round(max(1.10, 1.0 + ($horse['win_odds'] - 1.0) * 0.20), 2);
            }
            unset($horse);
        }

        return $baseHorses;
    }

    /**
     * Get or initialize the currently active round synchronized across all users.
     *
     * @return array<string, mixed>
     */
    public static function getCurrentRound(): array
    {
        $setting = AdminSetting::current();
        $duration = (int) ($setting->horse_racing_round_duration ?: self::DEFAULT_ROUND_DURATION);

        $now = Carbon::now('Asia/Kolkata');
        $todayStart = $now->copy()->startOfDay();
        $secondsSinceMidnight = ($now->hour * 3600) + ($now->minute * 60) + $now->second;

        $slotIndex = (int) floor($secondsSinceMidnight / $duration) + 1;
        $roundId = 'HR'.$now->format('ymd').str_pad((string) $slotIndex, 5, '0', STR_PAD_LEFT);

        $startSeconds = ($slotIndex - 1) * $duration;
        $endSeconds = $slotIndex * $duration;
        $startTime = $todayStart->copy()->addSeconds($startSeconds);
        $endTime = $todayStart->copy()->addSeconds($endSeconds);

        $remainingSeconds = max(0, $endSeconds - $secondsSinceMidnight);
        $isLocked = ($remainingSeconds <= self::RACING_COUNTDOWN);

        // 1. Settle any past pending rounds atomically
        self::settlePreviousPendingRounds($roundId);

        // 2. Fetch or create active round with provably fair server seed
        $serverSeed = bin2hex(random_bytes(16));
        $seedHash = hash('sha256', $serverSeed);
        $roster = self::getHorsesRoster($serverSeed);

        $hasManual = ($setting->horse_racing_manual_winner !== null);
        $manualWinnerId = $hasManual ? (int) $setting->horse_racing_manual_winner : null;

        $round = HorseRaceRound::firstOrCreate(
            ['round_id' => $roundId],
            [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $isLocked ? 'racing' : 'betting',
                'horses_data' => $roster,
                'race_distance' => self::DEFAULT_RACE_DISTANCE,
                'seed_hash' => $seedHash,
                'server_seed' => $serverSeed,
                'is_manual' => $hasManual,
                'manual_winner_id' => $manualWinnerId,
            ]
        );

        if ($hasManual && ! $round->is_manual) {
            $round->is_manual = true;
            $round->manual_winner_id = $manualWinnerId;
            $round->save();
        }

        if ($isLocked && $round->status === 'betting') {
            $round->status = 'racing';
            $round->save();
        }

        if ($round->status === 'racing' || $round->status === 'completed') {
            $isLocked = true;
        }

        // Active race data with locked-in winner and deterministic waypoints
        $activeRace = null;
        if ($isLocked || $round->status === 'racing' || $round->status === 'completed') {
            $podium = self::determineRoundPodium($round);
            $activeRace = [
                'round_id' => $round->round_id,
                'winner_horse_id' => $podium['winner_id'],
                'second_horse_id' => $podium['second_id'],
                'third_horse_id' => $podium['third_id'],
                'waypoints' => $podium['waypoints'],
            ];
        }

        // 3. Get recent history of completed rounds (last 20)
        $history = HorseRaceRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get(['round_id', 'winner_horse_id', 'second_horse_id', 'third_horse_id', 'total_bets_count', 'total_bet_amount', 'total_payout_amount', 'created_at']);

        // 4. Calculate bets summary for active round
        $betsSummary = HorseRaceBet::where('round_id', $roundId)
            ->selectRaw('horse_id, bet_type, COUNT(*) as count, SUM(bet_amount) as total_amount')
            ->groupBy('horse_id', 'bet_type')
            ->get();

        return [
            'round_id' => $roundId,
            'round_model' => $round,
            'duration' => $duration,
            'racing_countdown' => self::RACING_COUNTDOWN,
            'remaining_seconds' => $remainingSeconds,
            'is_locked' => $isLocked,
            'status' => $round->status,
            'start_time' => $startTime->toIso8601String(),
            'end_time' => $endTime->toIso8601String(),
            'seed_hash' => $round->seed_hash,
            'horses' => $round->horses_data ?: $roster,
            'race_distance' => $round->race_distance ?: self::DEFAULT_RACE_DISTANCE,
            'active_race' => $activeRace,
            'history' => $history,
            'bets_summary' => $betsSummary,
        ];
    }

    /**
     * Determine and persist the podium (1st, 2nd, 3rd) for a round deterministically.
     * If the round already has a winner determined, returns the existing podium.
     *
     * @return array{winner_id: int, second_id: int, third_id: int, waypoints: array<int, array{id: int, waypoints: array<int, float>}>}
     */
    public static function determineRoundPodium(HorseRaceRound $round): array
    {
        $horses = $round->horses_data ?: self::getHorsesRoster($round->server_seed ?: '');
        $seed = $round->server_seed ?: $round->round_id;

        // If podium already locked in this round, return it
        if ($round->winner_horse_id !== null && $round->second_horse_id !== null && $round->third_horse_id !== null) {
            $waypoints = self::simulateRaceWaypoints(
                (int) $round->winner_horse_id,
                (int) $round->second_horse_id,
                (int) $round->third_horse_id,
                $horses,
                $seed
            );

            return [
                'winner_id' => (int) $round->winner_horse_id,
                'second_id' => (int) $round->second_horse_id,
                'third_id' => (int) $round->third_horse_id,
                'waypoints' => $waypoints,
            ];
        }

        $setting = AdminSetting::current();
        $mode = $setting->horse_racing_control_mode ?: 'fair';
        $hasBets = HorseRaceBet::where('round_id', $round->round_id)->where('status', 'pending')->exists();
        $shouldHouseWin = $hasBets && $setting->shouldHouseWin();

        // 1. Determine Winner (1st Place)
        $winnerId = null;
        if ($round->is_manual && $round->manual_winner_id !== null && $round->manual_winner_id >= 1 && $round->manual_winner_id <= 6) {
            $winnerId = (int) $round->manual_winner_id;
        } elseif ($setting->horse_racing_manual_winner !== null && $setting->horse_racing_manual_winner >= 1 && $setting->horse_racing_manual_winner <= 6) {
            $winnerId = (int) $setting->horse_racing_manual_winner;
        } elseif ($mode === 'admin_profit' || ($hasBets && $shouldHouseWin)) {
            // 75% - 80% House Profit: Pick horse with lowest payout liability
            $winnerId = self::determineLeastLiabilityWinner($round->round_id, $horses);
        } else {
            // 20% - 25% Choti Winning / Natural: Weighted pick by horse odds
            $winnerId = self::pickWeightedHorse($horses);
        }

        // 2. Determine 2nd and 3rd Place
        $remainingHorses = array_values(array_filter($horses, fn ($h) => (int) $h['id'] !== $winnerId));
        $secondId = self::pickWeightedHorse($remainingHorses);

        $thirdCandidates = array_values(array_filter($remainingHorses, fn ($h) => (int) $h['id'] !== $secondId));
        $thirdId = self::pickWeightedHorse($thirdCandidates);

        // Save into round model
        $round->winner_horse_id = $winnerId;
        $round->second_horse_id = $secondId;
        $round->third_horse_id = $thirdId;
        $round->save();

        $waypoints = self::simulateRaceWaypoints($winnerId, $secondId, $thirdId, $horses, $seed);

        return [
            'winner_id' => $winnerId,
            'second_id' => $secondId,
            'third_id' => $thirdId,
            'waypoints' => $waypoints,
        ];
    }

    /**
     * Settle any older rounds that have expired and need determination.
     */
    public static function settlePreviousPendingRounds(string $currentRoundId): void
    {
        $pendingRounds = HorseRaceRound::where('round_id', '!=', $currentRoundId)
            ->whereIn('status', ['betting', 'racing'])
            ->lockForUpdate()
            ->get();

        foreach ($pendingRounds as $lockedRound) {
            self::settleRound($lockedRound);
        }
    }

    /**
     * Settle a specific horse race round, pick podium winners, and credit winning bets.
     */
    public static function settleRound(HorseRaceRound $round): void
    {
        if ($round->status === 'completed') {
            return;
        }

        DB::transaction(function () use ($round) {
            $setting = AdminSetting::current();

            // Ensure podium is determined (matches active_race if already locked during racing phase)
            $podium = self::determineRoundPodium($round);
            $winnerId = $podium['winner_id'];
            $secondId = $podium['second_id'];
            $thirdId = $podium['third_id'];

            // Once manual override is applied to settle this round, clear it on admin settings
            if (($round->is_manual || $setting->horse_racing_manual_winner !== null) && $setting->horse_racing_manual_winner !== null) {
                $setting->update([
                    'horse_racing_manual_winner' => null,
                    'horse_racing_control_mode' => 'fair',
                ]);
            }

            // Update round record status
            $round->status = 'completed';

            // 3. Settle all pending bets on this round
            $bets = HorseRaceBet::where('round_id', $round->round_id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->get();

            $totalPayout = 0.00;

            foreach ($bets as $bet) {
                $betWon = self::evaluateBetOutcome(
                    $bet->bet_type,
                    (int) $bet->horse_id,
                    $winnerId,
                    $secondId,
                    $thirdId
                );

                if ($betWon) {
                    $multiplier = (float) $bet->odds;
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
                            'admin_notes' => "Horse Racing Won Round: {$round->round_id} | #{$bet->horse_id} {$bet->horse_name} ({$bet->bet_type}){$feeNote}",
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
     * Pick a horse using weighted random selection based on ratings and inverse odds.
     *
     * @param  array<int, array{id: int, rating: int, win_odds: float}>  $horsesList
     */
    public static function pickWeightedHorse(array $horsesList): int
    {
        if (empty($horsesList)) {
            return 1;
        }

        $totalWeight = 0;
        $weightedMap = [];

        foreach ($horsesList as $horse) {
            // Lower odds / higher rating = higher chance to win
            $weight = max(10, (int) round(1000.0 / (float) ($horse['win_odds'] ?? 3.0)));
            $totalWeight += $weight;
            $weightedMap[] = [
                'id' => (int) $horse['id'],
                'cum_weight' => $totalWeight,
            ];
        }

        $rand = random_int(1, $totalWeight);

        foreach ($weightedMap as $item) {
            if ($rand <= $item['cum_weight']) {
                return $item['id'];
            }
        }

        return (int) $horsesList[0]['id'];
    }

    /**
     * Check if a specific bet wins given the final podium (1st, 2nd, 3rd).
     */
    public static function evaluateBetOutcome(string $betType, int $betHorseId, int $winnerId, int $secondId, int $thirdId): bool
    {
        return match ($betType) {
            'win' => ($betHorseId === $winnerId),
            'place' => ($betHorseId === $winnerId || $betHorseId === $secondId),
            'show' => ($betHorseId === $winnerId || $betHorseId === $secondId || $betHorseId === $thirdId),
            default => false,
        };
    }

    /**
     * Calculate least liability winning horse for admin house protection mode.
     *
     * @param  array<int, array{id: int}>  $horses
     */
    public static function determineLeastLiabilityWinner(string $roundId, array $horses): int
    {
        $bets = HorseRaceBet::where('round_id', $roundId)
            ->where('status', 'pending')
            ->get();

        if ($bets->isEmpty()) {
            return self::pickWeightedHorse($horses);
        }

        $minLiability = PHP_FLOAT_MAX;
        $bestCandidates = [];

        foreach ($horses as $candidate) {
            $candidateId = (int) $candidate['id'];
            $liability = 0.0;

            foreach ($bets as $bet) {
                // If this candidate wins 1st place, evaluate guaranteed win/place/show payouts
                if (self::evaluateBetOutcome($bet->bet_type, (int) $bet->horse_id, $candidateId, 0, 0)) {
                    $liability += ((float) $bet->bet_amount * (float) $bet->odds);
                }
            }

            if ($liability < $minLiability) {
                $minLiability = $liability;
                $bestCandidates = [$candidateId];
            } elseif ($liability === $minLiability) {
                $bestCandidates[] = $candidateId;
            }
        }

        return $bestCandidates[array_rand($bestCandidates)];
    }

    /**
     * Place batch bets on the active horse race round with atomic wallet locking.
     *
     * @param  array<int, array{horse_id: int|string, bet_type: string, amount: int|float}>  $betsList
     * @return array<string, mixed>
     */
    public static function placeBets(User $user, array $betsList): array
    {
        if (empty($betsList)) {
            return ['success' => false, 'message' => 'No bets selected. Please select a horse and bet type.'];
        }

        $roundInfo = self::getCurrentRound();
        if ($roundInfo['is_locked'] || $roundInfo['status'] !== 'betting') {
            return ['success' => false, 'message' => 'Bets are closed for this race. Horses are at the starting gates!'];
        }

        $setting = AdminSetting::current();
        $minBet = (float) ($setting->horse_racing_min_bet ?: 10.00);
        $maxBet = (float) ($setting->horse_racing_max_bet ?: 25000.00);

        $horsesRoster = $roundInfo['horses'];
        $rosterById = [];
        foreach ($horsesRoster as $h) {
            $rosterById[(int) $h['id']] = $h;
        }

        // Calculate total stake required and validate each bet
        $totalStake = 0.00;
        $validatedBets = [];

        foreach ($betsList as $bet) {
            $horseId = (int) ($bet['horse_id'] ?? 0);
            $type = strtolower(trim((string) ($bet['bet_type'] ?? '')));
            $amount = round((float) ($bet['amount'] ?? 0), 2);

            if (! isset($rosterById[$horseId])) {
                return ['success' => false, 'message' => "Invalid horse selection: #{$horseId}"];
            }

            if (! in_array($type, self::BET_TYPES, true)) {
                return ['success' => false, 'message' => "Invalid bet type: {$type}. Must be win, place, or show."];
            }

            if ($amount < $minBet) {
                return ['success' => false, 'message' => "Minimum bet per runner is ₹{$minBet}."];
            }

            if ($amount > $maxBet) {
                return ['success' => false, 'message' => "Maximum bet per runner is ₹{$maxBet}."];
            }

            $horse = $rosterById[$horseId];
            $odds = match ($type) {
                'win' => (float) $horse['win_odds'],
                'place' => (float) $horse['place_odds'],
                'show' => (float) $horse['show_odds'],
            };

            $totalStake += $amount;
            $validatedBets[] = [
                'horse_id' => $horseId,
                'horse_name' => $horse['name'],
                'bet_type' => $type,
                'amount' => $amount,
                'odds' => $odds,
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
                'admin_notes' => 'Placed '.count($validatedBets)." Horse Racing bets on round {$roundInfo['round_id']}",
            ]);

            $createdBets = [];
            foreach ($validatedBets as $vb) {
                $betModel = HorseRaceBet::create([
                    'bet_id' => 'HRB_'.strtoupper(Str::random(12)),
                    'round_id' => $roundInfo['round_id'],
                    'user_id' => $lockedUser->id,
                    'horse_id' => $vb['horse_id'],
                    'horse_name' => $vb['horse_name'],
                    'bet_type' => $vb['bet_type'],
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
     * Get user recent horse racing bets with win/loss status.
     *
     * @return array<int, mixed>
     */
    public static function getUserBets(User $user, int $limit = 25): array
    {
        return HorseRaceBet::where('user_id', $user->id)
            ->with('round')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Generate deterministic race progress waypoints for frontend 3D/2D animation.
     *
     * @param  array<int, array{id: int}>  $horses
     * @return array<int, array{id: int, waypoints: array<int, float>}>
     */
    public static function simulateRaceWaypoints(int $winnerId, int $secondId, int $thirdId, array $horses, string $seed): array
    {
        $hash = crc32($seed);
        $result = [];

        foreach ($horses as $idx => $horse) {
            $id = (int) $horse['id'];
            $noise1 = ((($hash >> ($idx * 3)) & 0x7) - 3) * 0.15;
            $noise2 = ((($hash >> (($idx + 2) * 3)) & 0x7) - 3) * 0.15;

            if ($id === $winnerId) {
                // Winner is strictly ahead: starts strong, surges past 500m, dominates the home stretch, finishes first at 100.0%
                $wp = [
                    0.0,
                    round(26.5 + max(0.0, $noise1), 1),
                    round(53.5 + max(0.0, $noise2), 1),
                    round(78.5 + max(0.0, $noise1), 1),
                    100.0,
                ];
            } elseif ($id === $secondId) {
                // 2nd place challenges closely behind, finishes at 98.4%
                $wp = [
                    0.0,
                    round(24.2 + min(0.0, $noise2), 1),
                    round(49.5 + min(0.0, $noise1), 1),
                    round(73.5 + min(0.0, $noise2), 1),
                    98.4,
                ];
            } elseif ($id === $thirdId) {
                // 3rd place stays competitive, finishes at 96.8%
                $wp = [
                    0.0,
                    round(22.5 + min(0.0, $noise1), 1),
                    round(46.5 + min(0.0, $noise2), 1),
                    round(69.5 + min(0.0, $noise1), 1),
                    96.8,
                ];
            } else {
                // 4th to 6th places trail behind
                $rankOffset = ($id % 3);
                $finalPct = round(91.0 - ($rankOffset * 3.0), 1);
                $wp = [
                    0.0,
                    round(20.5 - ($rankOffset * 0.8) + min(0.0, $noise1), 1),
                    round(42.5 - ($rankOffset * 1.0) + min(0.0, $noise2), 1),
                    round(64.0 - ($rankOffset * 1.2) + min(0.0, $noise2), 1),
                    $finalPct,
                ];
            }

            $result[] = [
                'id' => $id,
                'waypoints' => $wp,
            ];
        }

        return $result;
    }
}
