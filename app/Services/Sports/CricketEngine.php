<?php

namespace App\Services\Sports;

use App\Models\SportsBet;
use App\Models\SportsMarket;
use App\Models\SportsMatch;
use App\Models\SportsRunner;
use App\Models\Transaction;
use App\Models\User;
use App\Services\GamePayoutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CricketEngine
{
    protected CricketApiService $apiService;

    public function __construct(?CricketApiService $apiService = null)
    {
        $this->apiService = $apiService ?? new CricketApiService;
    }

    public function getApiService(): CricketApiService
    {
        return $this->apiService;
    }

    /**
     * Synchronize matches from CricAPI / Simulation into the database.
     */
    public function syncMatches(bool $forceRefresh = false): array
    {
        $feedMatches = $this->apiService->getMatches($forceRefresh);
        $syncedMatches = [];

        foreach ($feedMatches as $m) {
            $match = SportsMatch::updateOrCreate(
                ['external_id' => $m['external_id']],
                [
                    'sport' => $m['sport'] ?? 'cricket',
                    'series_name' => $m['series_name'] ?? null,
                    'title' => $m['title'],
                    'match_type' => $m['match_type'] ?? 't20',
                    'status' => $m['status'] ?? 'upcoming',
                    'team_a' => $m['team_a'],
                    'team_a_short' => $m['team_a_short'] ?? null,
                    'team_a_img' => $m['team_a_img'] ?? null,
                    'team_b' => $m['team_b'],
                    'team_b_short' => $m['team_b_short'] ?? null,
                    'team_b_img' => $m['team_b_img'] ?? null,
                    'venue' => $m['venue'] ?? null,
                    'score_team_a' => $m['score_team_a'] ?? null,
                    'score_team_b' => $m['score_team_b'] ?? null,
                    'current_summary' => $m['current_summary'] ?? null,
                    'recent_balls' => $m['recent_balls'] ?? [],
                    'winner_team' => $m['winner_team'] ?? null,
                    'is_live' => $m['is_live'] ?? false,
                    'is_featured' => $m['is_featured'] ?? false,
                    'starts_at' => ! empty($m['starts_at']) ? date('Y-m-d H:i:s', strtotime($m['starts_at'])) : null,
                    'meta_data' => $m['meta_data'] ?? null,
                ]
            );

            // 1. Auto-settle ball-by-ball markets when delivery completes
            $lastBall = $m['meta_data']['last_ball_outcome'] ?? null;
            if ($lastBall) {
                $incomingBallMkt = collect($m['markets'] ?? [])->firstWhere('market_type', 'next_ball');
                if ($incomingBallMkt) {
                    $activeBallMarkets = SportsMarket::where('match_id', $match->id)
                        ->where('market_type', 'next_ball')
                        ->where('status', '!=', 'settled')
                        ->where('market_name', '!=', $incomingBallMkt['market_name'])
                        ->get();

                    foreach ($activeBallMarkets as $oldBallMkt) {
                        $runners = $oldBallMkt->runners;
                        $winnerRunner = null;

                        foreach ($runners as $runner) {
                            $name = strtolower($runner->runner_name);
                            if ($lastBall === '0' && (str_contains($name, '0') || str_contains($name, 'dot'))) {
                                $winnerRunner = $runner;
                                break;
                            } elseif ($lastBall === '1' && str_contains($name, '1 run')) {
                                $winnerRunner = $runner;
                                break;
                            } elseif ($lastBall === '2' && str_contains($name, '2 run')) {
                                $winnerRunner = $runner;
                                break;
                            } elseif ($lastBall === '4' && (str_contains($name, '4') || str_contains($name, 'boundary'))) {
                                $winnerRunner = $runner;
                                break;
                            } elseif ($lastBall === '6' && (str_contains($name, '6') || str_contains($name, 'six'))) {
                                $winnerRunner = $runner;
                                break;
                            } elseif ($lastBall === 'W' && (str_contains($name, 'wicket') || str_contains($name, 'out'))) {
                                $winnerRunner = $runner;
                                break;
                            }
                        }

                        if (! $winnerRunner && $runners->isNotEmpty()) {
                            $winnerRunner = $runners->first();
                        }

                        if ($winnerRunner) {
                            $this->settleMarket($oldBallMkt->id, $winnerRunner->id);
                        }
                    }
                }
            }

            // 2. Auto-settle over-by-over markets when over completes
            $incomingOverMkt = collect($m['markets'] ?? [])->firstWhere('market_type', 'over_runs');
            if ($incomingOverMkt) {
                $activeOverMarkets = SportsMarket::where('match_id', $match->id)
                    ->where('market_type', 'over_runs')
                    ->where('status', '!=', 'settled')
                    ->where('market_name', '!=', $incomingOverMkt['market_name'])
                    ->get();

                foreach ($activeOverMarkets as $oldOverMkt) {
                    $runners = $oldOverMkt->runners;
                    $winnerRunner = $runners->firstWhere('runner_name', 'Over 8.5 Runs') ?: $runners->first();
                    if ($winnerRunner) {
                        $this->settleMarket($oldOverMkt->id, $winnerRunner->id);
                    }
                }
            }

            // 3. Auto-settle match winner when match finishes
            if ($match->status === 'completed' && ! empty($match->winner_team)) {
                $winnerMarket = SportsMarket::where('match_id', $match->id)
                    ->where('market_type', 'match_winner')
                    ->where('status', '!=', 'settled')
                    ->first();
                if ($winnerMarket) {
                    $winningRunner = $winnerMarket->runners()->where('runner_name', $match->winner_team)->first();
                    if ($winningRunner) {
                        $this->settleMarket($winnerMarket->id, $winningRunner->id);
                    }
                }
            }

            // 4. Sync active markets and runners
            if (! empty($m['markets'])) {
                foreach ($m['markets'] as $mkt) {
                    $market = SportsMarket::updateOrCreate(
                        [
                            'match_id' => $match->id,
                            'market_type' => $mkt['market_type'],
                            'market_name' => $mkt['market_name'],
                        ],
                        [
                            'status' => $mkt['status'] ?? 'active',
                        ]
                    );

                    if (! empty($mkt['runners'])) {
                        foreach ($mkt['runners'] as $r) {
                            SportsRunner::updateOrCreate(
                                [
                                    'market_id' => $market->id,
                                    'runner_name' => $r['runner_name'],
                                ],
                                [
                                    'back_odds' => $r['back_odds'] ?? 1.90,
                                    'lay_odds' => $r['lay_odds'] ?? 1.95,
                                    'back_volume' => $r['back_volume'] ?? 10000,
                                    'lay_volume' => $r['lay_volume'] ?? 10000,
                                    'status' => ($market->status === 'suspended') ? 'suspended' : 'active',
                                ]
                            );
                        }
                    }
                }
            }

            $syncedMatches[] = $match->load(['markets.runners']);
        }

        return $syncedMatches;
    }

    /**
     * Get all active and upcoming matches with markets and runners.
     */
    public function getMatchesList(string $filter = 'all'): array
    {
        // Re-sync with latest live feed
        $this->syncMatches();

        $query = SportsMatch::with(['markets' => function ($q) {
            $q->whereIn('status', ['active', 'suspended', 'settled'])->with('runners');
        }])
            ->orderBy('is_live', 'desc')
            ->orderBy('starts_at', 'asc');

        if ($filter === 'live') {
            $query->where(function ($q) {
                $q->where('is_live', true)->orWhere('status', 'live');
            });
        } elseif ($filter === 'upcoming') {
            $query->where('status', 'upcoming')->where('is_live', false);
        } elseif ($filter === 'completed' || $filter === 'history') {
            $query->where('status', 'completed')->orderBy('updated_at', 'desc');
        }

        return $query->get()->toArray();
    }

    /**
     * Get a specific match by ID or external ID.
     */
    public function getMatch(int|string $id): ?SportsMatch
    {
        $this->syncMatches();

        return SportsMatch::with(['markets' => function ($q) {
            $q->orderByRaw("FIELD(status, 'active', 'suspended', 'settled')")
                ->with('runners');
        }])
            ->where('id', $id)
            ->orWhere('external_id', $id)
            ->first();
    }

    /**
     * Place a real-money cricket bet with atomic wallet balance check and deduction.
     */
    public function placeBet(User $user, int $runnerId, float $stake, string $betType = 'back'): array
    {
        if ($stake <= 0) {
            return ['success' => false, 'message' => 'Stake amount must be greater than 0.'];
        }

        if ($stake < 10) {
            return ['success' => false, 'message' => 'Minimum bet amount is ₹10.'];
        }

        if ($stake > 100000) {
            return ['success' => false, 'message' => 'Maximum bet limit is ₹1,00,000.'];
        }

        $betType = strtolower($betType) === 'lay' ? 'lay' : 'back';

        return DB::transaction(function () use ($user, $runnerId, $stake, $betType) {
            $runner = SportsRunner::with(['market.match'])->find($runnerId);

            if (! $runner) {
                return ['success' => false, 'message' => 'Selected runner not found.'];
            }

            $market = $runner->market;
            $match = $market->match;

            if ($market->status !== 'active') {
                return ['success' => false, 'message' => 'This betting market is currently suspended.'];
            }

            if (in_array($match->status, ['completed', 'cancelled'])) {
                return ['success' => false, 'message' => 'Match is already completed or cancelled.'];
            }

            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            if (! $lockedUser || (float) $lockedUser->balance < $stake) {
                return [
                    'success' => false,
                    'message' => 'Insufficient wallet balance. Please recharge.',
                    'balance' => (float) ($lockedUser?->balance ?? 0),
                ];
            }

            $odds = $betType === 'lay' ? (float) ($runner->lay_odds ?: $runner->back_odds) : (float) $runner->back_odds;
            $potentialPayout = round($stake * $odds, 2);

            $balBefore = (float) $lockedUser->balance;
            $balAfter = round($balBefore - $stake, 2);
            $lockedUser->balance = $balAfter;
            $lockedUser->save();

            // Unique Bet ID
            $betId = 'CRIC_'.strtoupper(Str::random(10));

            // Log Transaction
            Transaction::create([
                'user_id' => $lockedUser->id,
                'type' => 'bet_placed',
                'amount' => $stake,
                'balance_before' => $balBefore,
                'balance_after' => $balAfter,
                'status' => 'approved',
                'admin_notes' => "Placed Cricket Bet: {$betType} {$runner->runner_name} @ {$odds} ({$match->title})",
            ]);

            // Create SportsBet record
            $bet = SportsBet::create([
                'bet_id' => $betId,
                'user_id' => $lockedUser->id,
                'match_id' => $match->id,
                'market_id' => $market->id,
                'runner_id' => $runner->id,
                'bet_type' => $betType,
                'runner_name' => $runner->runner_name,
                'stake' => $stake,
                'odds' => $odds,
                'potential_payout' => $potentialPayout,
                'win_amount' => 0.00,
                'status' => 'pending',
            ]);

            // Update runner volume
            if ($betType === 'back') {
                $runner->increment('back_volume', $stake);
            } else {
                $runner->increment('lay_volume', $stake);
            }

            return [
                'success' => true,
                'message' => 'Cricket bet placed successfully!',
                'bet' => [
                    'bet_id' => $bet->bet_id,
                    'runner_name' => $bet->runner_name,
                    'bet_type' => $bet->bet_type,
                    'stake' => $bet->stake,
                    'odds' => $bet->odds,
                    'potential_payout' => $bet->potential_payout,
                    'match_title' => $match->title,
                ],
                'new_balance' => $balAfter,
            ];
        });
    }

    /**
     * Settle a market, paying out winners and deducting losses.
     */
    public function settleMarket(int $marketId, int $winningRunnerId): array
    {
        return DB::transaction(function () use ($marketId, $winningRunnerId) {
            $market = SportsMarket::with(['match', 'runners'])->lockForUpdate()->find($marketId);
            if (! $market || $market->status === 'settled') {
                return ['success' => false, 'message' => 'Market already settled or not found.'];
            }

            $winningRunner = SportsRunner::find($winningRunnerId);
            if (! $winningRunner || $winningRunner->market_id !== $market->id) {
                return ['success' => false, 'message' => 'Invalid winning runner selected.'];
            }

            // Mark runners status
            foreach ($market->runners as $runner) {
                $runner->status = ($runner->id === $winningRunnerId) ? 'winner' : 'loser';
                $runner->save();
            }

            // Settle all pending bets
            $bets = SportsBet::where('market_id', $market->id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->get();

            $totalPaid = 0.00;

            foreach ($bets as $bet) {
                $isWinner = false;
                if ($bet->bet_type === 'back') {
                    $isWinner = ($bet->runner_id === $winningRunnerId);
                } else {
                    // Lay bet wins if the selection loses
                    $isWinner = ($bet->runner_id !== $winningRunnerId);
                }

                if ($isWinner) {
                    $multiplier = (float) $bet->odds;
                    $payoutDetails = GamePayoutService::calculate($bet->stake, $multiplier);
                    $winAmount = (float) $payoutDetails['player_payout'];
                    $commissionAmount = (float) $payoutDetails['commission_amount'];

                    $bet->status = 'won';
                    $bet->win_amount = $winAmount;
                    $bet->commission_amount = $commissionAmount;
                    $bet->settled_at = now();
                    $bet->save();

                    // Credit user balance
                    $user = User::where('id', $bet->user_id)->lockForUpdate()->first();
                    if ($user) {
                        $balBefore = (float) $user->balance;
                        $balAfter = round($balBefore + $winAmount, 2);
                        $user->balance = $balAfter;
                        $user->save();

                        $feeNote = ($commissionAmount > 0) ? " (Fee: ₹{$commissionAmount})" : '';
                        Transaction::create([
                            'user_id' => $user->id,
                            'type' => 'bet_won',
                            'amount' => $winAmount,
                            'balance_before' => $balBefore,
                            'balance_after' => $balAfter,
                            'status' => 'approved',
                            'admin_notes' => "Cricket Bet Won: {$bet->bet_id} | {$bet->runner_name} ({$market->market_name}){$feeNote}",
                        ]);
                    }

                    $totalPaid += $winAmount;
                } else {
                    $bet->status = 'lost';
                    $bet->win_amount = 0.00;
                    $bet->commission_amount = 0.00;
                    $bet->settled_at = now();
                    $bet->save();
                }
            }

            $market->status = 'settled';
            $market->winning_runner_id = $winningRunnerId;
            $market->settled_at = now();
            $market->save();

            return [
                'success' => true,
                'message' => 'Market settled successfully.',
                'settled_bets_count' => count($bets),
                'total_paid' => $totalPaid,
            ];
        });
    }

    /**
     * Get user active and settled bets.
     */
    public function getUserBets(int $userId, int $limit = 25): array
    {
        return SportsBet::with(['match', 'market'])
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
