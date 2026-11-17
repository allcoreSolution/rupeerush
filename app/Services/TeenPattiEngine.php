<?php

namespace App\Services;

use App\Models\AdminSetting;
use App\Models\TeenPattiBet;
use App\Models\TeenPattiRound;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TeenPattiEngine
{
    public const DEFAULT_ROUND_DURATION = 25; // 25 seconds per round

    public const LOCK_COUNTDOWN = 5; // Last 5 seconds locked for dealing

    public const ODDS_PLAYER_A = 1.95;

    public const ODDS_PLAYER_B = 1.95;

    public const ODDS_TIE = 9.00;

    /**
     * Pair Plus Side Bet Pay Table.
     */
    public const PAIR_PLUS_MULTIPLIERS = [
        'trio' => 40.0,
        'pure_sequence' => 30.0,
        'sequence' => 10.0,
        'color' => 5.0,
        'pair' => 2.0,
        'high_card' => 0.0,
    ];

    /**
     * Standard 52-Card Deck definitions.
     */
    public const SUITS = ['S', 'H', 'D', 'C']; // Spades, Hearts, Diamonds, Clubs

    public const RANKS = [
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        '10' => 10,
        'J' => 11,
        'Q' => 12,
        'K' => 13,
        'A' => 14,
    ];

    /**
     * Generate a full fresh 52-card standard deck.
     */
    public static function getFreshDeck(): array
    {
        $deck = [];
        foreach (self::SUITS as $suit) {
            foreach (array_keys(self::RANKS) as $rank) {
                $deck[] = $rank.$suit;
            }
        }

        return $deck;
    }

    /**
     * Parse card string (e.g. 'AS', '10H', '7D') into rank, suit, value, symbol, and display color.
     */
    public static function parseCard(string $card): array
    {
        $suit = substr($card, -1);
        $rankStr = substr($card, 0, -1);
        $rankVal = self::RANKS[$rankStr] ?? 2;

        $suitSymbols = [
            'S' => '♠',
            'H' => '♥',
            'D' => '♦',
            'C' => '♣',
        ];

        $isRed = in_array($suit, ['H', 'D'], true);

        return [
            'code' => $card,
            'rank' => $rankStr,
            'value' => $rankVal,
            'suit' => $suit,
            'symbol' => $suitSymbols[$suit] ?? '♠',
            'is_red' => $isRed,
            'color' => $isRed ? '#ef4444' : '#1e293b',
        ];
    }

    /**
     * Evaluate a 3-card Teen Patti hand.
     * Returns:
     * - rank_tier: 6 (Trio), 5 (Pure Sequence), 4 (Sequence), 3 (Color), 2 (Pair), 1 (High Card)
     * - rank_type: 'trio' | 'pure_sequence' | 'sequence' | 'color' | 'pair' | 'high_card'
     * - rank_title: Human-readable title
     * - score: Mathematical comparable integer score
     * - cards_parsed: Array of parsed card objects
     */
    public static function evaluateHand(array $cards): array
    {
        $parsed = array_map([self::class, 'parseCard'], $cards);

        // Sort parsed cards descending by rank value
        usort($parsed, fn ($a, $b) => $b['value'] <=> $a['value']);

        $v1 = $parsed[0]['value'];
        $v2 = $parsed[1]['value'];
        $v3 = $parsed[2]['value'];

        $s1 = $parsed[0]['suit'];
        $s2 = $parsed[1]['suit'];
        $s3 = $parsed[2]['suit'];

        $isFlush = ($s1 === $s2 && $s2 === $s3);

        // Check Sequence / Run
        // In Indian Teen Patti:
        // A-K-Q (14, 13, 12) is Highest (score high: 14)
        // A-2-3 (14, 3, 2) is 2nd Highest (score high: 13.5)
        // K-Q-J (13, 12, 11) is 3rd Highest (score high: 13) ... down to 4-3-2 (score high: 4)
        $isSequence = false;
        $seqHigh = 0;

        if ($v1 === 14 && $v2 === 13 && $v3 === 12) {
            $isSequence = true;
            $seqHigh = 14.0;
        } elseif ($v1 === 14 && $v2 === 3 && $v3 === 2) {
            $isSequence = true;
            $seqHigh = 13.5; // Traditional Teen Patti rule: A-2-3 beats K-Q-J
        } elseif (($v1 - $v2 === 1) && ($v2 - $v3 === 1)) {
            $isSequence = true;
            $seqHigh = (float) $v1;
        }

        // Check Trio (Three of a Kind)
        $isTrio = ($v1 === $v2 && $v2 === $v3);

        // Check Pair
        $isPair = false;
        $pairRank = 0;
        $kickerRank = 0;

        if (! $isTrio) {
            if ($v1 === $v2) {
                $isPair = true;
                $pairRank = $v1;
                $kickerRank = $v3;
            } elseif ($v2 === $v3) {
                $isPair = true;
                $pairRank = $v2;
                $kickerRank = $v1;
            } elseif ($v1 === $v3) {
                $isPair = true;
                $pairRank = $v1;
                $kickerRank = $v2;
            }
        }

        // Determine Hand Category & Score
        if ($isTrio) {
            $tier = 6;
            $type = 'trio';
            $title = 'Trio of '.self::getRankLabel($v1).'s';
            $score = 600000000 + ($v1 * 10000);
        } elseif ($isFlush && $isSequence) {
            $tier = 5;
            $type = 'pure_sequence';
            $title = 'Pure Sequence ('.self::getSequenceLabel($seqHigh).')';
            $score = 500000000 + (int) ($seqHigh * 10000);
        } elseif ($isSequence) {
            $tier = 4;
            $type = 'sequence';
            $title = 'Sequence ('.self::getSequenceLabel($seqHigh).')';
            $score = 400000000 + (int) ($seqHigh * 10000);
        } elseif ($isFlush) {
            $tier = 3;
            $type = 'color';
            $title = 'Color ('.self::getRankLabel($v1).' High)';
            $score = 300000000 + ($v1 * 10000) + ($v2 * 100) + $v3;
        } elseif ($isPair) {
            $tier = 2;
            $type = 'pair';
            $title = 'Pair of '.self::getRankLabel($pairRank).'s';
            $score = 200000000 + ($pairRank * 10000) + ($kickerRank * 100);
        } else {
            $tier = 1;
            $type = 'high_card';
            $title = 'High Card ('.self::getRankLabel($v1).')';
            $score = 100000000 + ($v1 * 10000) + ($v2 * 100) + $v3;
        }

        return [
            'rank_tier' => $tier,
            'rank_type' => $type,
            'rank_title' => $title,
            'score' => $score,
            'cards' => $cards,
            'cards_parsed' => $parsed,
        ];
    }

    /**
     * Compare two 3-card hands.
     * Returns: 'player_a', 'player_b', or 'tie'.
     */
    public static function compareHands(array $evalA, array $evalB): string
    {
        if ($evalA['score'] > $evalB['score']) {
            return 'player_a';
        }
        if ($evalB['score'] > $evalA['score']) {
            return 'player_b';
        }

        return 'tie';
    }

    /**
     * Human-friendly label for a rank value.
     */
    public static function getRankLabel(int $val): string
    {
        return match ($val) {
            14 => 'Ace',
            13 => 'King',
            12 => 'Queen',
            11 => 'Jack',
            default => (string) $val,
        };
    }

    /**
     * Human-friendly sequence description.
     */
    public static function getSequenceLabel(float $seqHigh): string
    {
        if ($seqHigh == 14.0) {
            return 'A-K-Q';
        }
        if ($seqHigh == 13.5) {
            return 'A-2-3';
        }
        if ($seqHigh == 13.0) {
            return 'K-Q-J';
        }
        if ($seqHigh == 12.0) {
            return 'Q-J-10';
        }
        if ($seqHigh == 11.0) {
            return 'J-10-9';
        }

        $top = (int) $seqHigh;

        return "{$top}-".($top - 1).'-'.($top - 2);
    }

    /**
     * Get or initialize the currently active round synchronized across all users.
     */
    public static function getCurrentRound(): array
    {
        $setting = AdminSetting::current();
        $duration = (int) ($setting->teen_patti_round_duration ?: self::DEFAULT_ROUND_DURATION);

        $now = Carbon::now('Asia/Kolkata');
        $todayStart = $now->copy()->startOfDay();
        $secondsSinceMidnight = ($now->hour * 3600) + ($now->minute * 60) + $now->second;

        $slotIndex = (int) floor($secondsSinceMidnight / $duration) + 1;
        $roundId = 'TP'.$now->format('ymd').str_pad((string) $slotIndex, 5, '0', STR_PAD_LEFT);

        $startSeconds = ($slotIndex - 1) * $duration;
        $endSeconds = $slotIndex * $duration;
        $startTime = $todayStart->copy()->addSeconds($startSeconds);
        $endTime = $todayStart->copy()->addSeconds($endSeconds);

        $remainingSeconds = max(0, $endSeconds - $secondsSinceMidnight);
        $isLocked = ($remainingSeconds <= self::LOCK_COUNTDOWN);

        // 1. Settle any past pending rounds atomically
        self::settlePreviousPendingRounds($roundId);

        // 2. Fetch or create the active round
        $round = TeenPattiRound::firstOrCreate(
            ['round_id' => $roundId],
            [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $isLocked ? 'dealing' : 'betting',
            ]
        );

        if ($isLocked && $round->status === 'betting') {
            $round->status = 'dealing';
            $round->save();
        }

        // 3. Get recent roadmap / results history (last 20 completed rounds)
        $history = TeenPattiRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get(['round_id', 'winner', 'player_a_rank_type', 'player_b_rank_type', 'player_a_rank_title', 'player_b_rank_title', 'player_a_cards', 'player_b_cards', 'created_at']);

        // 4. Calculate total bets on active round for Player A vs Player B vs Tie
        $roundBetsSummary = TeenPattiBet::where('round_id', $roundId)
            ->selectRaw('bet_type, COUNT(*) as count, SUM(bet_amount) as total_amount')
            ->groupBy('bet_type')
            ->get()
            ->keyBy('bet_type')
            ->toArray();

        return [
            'round_id' => $roundId,
            'round_model' => $round,
            'start_time' => $startTime->toIso8601String(),
            'end_time' => $endTime->toIso8601String(),
            'duration' => $duration,
            'remaining_seconds' => $remainingSeconds,
            'is_locked' => $isLocked,
            'status' => $round->status,
            'min_bet' => (float) ($setting->teen_patti_min_bet ?? 5.00),
            'max_bet' => (float) ($setting->teen_patti_max_bet ?? 25000.00),
            'odds_player' => (float) ($setting->teen_patti_odds_player ?? self::ODDS_PLAYER_A),
            'odds_tie' => (float) ($setting->teen_patti_odds_tie ?? self::ODDS_TIE),
            'pair_plus_odds' => self::PAIR_PLUS_MULTIPLIERS,
            'history' => $history,
            'bets_summary' => $roundBetsSummary,
        ];
    }

    /**
     * Settle past pending rounds that have elapsed.
     */
    public static function settlePreviousPendingRounds(string $currentRoundId): void
    {
        $pendingRounds = TeenPattiRound::where('round_id', '<', $currentRoundId)
            ->where('status', '!=', 'completed')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($pendingRounds as $round) {
            self::dealAndSettleRound($round);
        }
    }

    /**
     * Deal cards, determine winner, settle bets, and complete the round.
     */
    public static function dealAndSettleRound(TeenPattiRound $round): void
    {
        if ($round->status === 'completed') {
            return;
        }

        DB::transaction(function () use ($round) {
            $lockedRound = TeenPattiRound::where('id', $round->id)->lockForUpdate()->first();
            if (! $lockedRound || $lockedRound->status === 'completed') {
                return;
            }

            // 1. Determine cards based on Admin Settings (Fair / House Trap / Manual Override)
            $setting = AdminSetting::current();
            $mode = $setting->teen_patti_control_mode ?? 'fair';

            $requestedWinner = null;
            $hasBets = TeenPattiBet::where('round_id', $lockedRound->round_id)->exists();
            $shouldHouseWin = $hasBets && $setting->shouldHouseWin();

            if ($lockedRound->is_manual && ! empty($lockedRound->manual_winner)) {
                $requestedWinner = $lockedRound->manual_winner;
            } elseif ($mode === 'manual_override' && ! empty($setting->teen_patti_manual_winner)) {
                $requestedWinner = $setting->teen_patti_manual_winner;
            } elseif ($mode === 'house_trap' || ($hasBets && $shouldHouseWin)) {
                // Calculate total payout liability for Player A vs Player B
                $payoutA = (float) TeenPattiBet::where('round_id', $lockedRound->round_id)
                    ->where('bet_type', 'player_a')
                    ->sum(DB::raw('bet_amount * odds'));

                $payoutB = (float) TeenPattiBet::where('round_id', $lockedRound->round_id)
                    ->where('bet_type', 'player_b')
                    ->sum(DB::raw('bet_amount * odds'));

                // House picks the side that owes less money (75-80% house profit)
                $requestedWinner = ($payoutA < $payoutB) ? 'player_a' : 'player_b';
            }

            // 2. Deal distinct cards for Player A and Player B
            [$cardsA, $cardsB, $evalA, $evalB, $winner] = self::generateValidDeal($requestedWinner);

            $lockedRound->player_a_cards = $cardsA;
            $lockedRound->player_b_cards = $cardsB;
            $lockedRound->player_a_rank_type = $evalA['rank_type'];
            $lockedRound->player_a_rank_title = $evalA['rank_title'];
            $lockedRound->player_b_rank_type = $evalB['rank_type'];
            $lockedRound->player_b_rank_title = $evalB['rank_title'];
            $lockedRound->winner = $winner;
            $lockedRound->status = 'completed';

            // 3. Settle all user bets for this round
            $bets = TeenPattiBet::where('round_id', $lockedRound->round_id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->get();

            $totalPayout = 0.00;

            foreach ($bets as $bet) {
                $betWon = false;
                $multiplier = 0.0;
                $outcomeText = '';

                // Handle bet types
                if ($bet->bet_type === 'player_a') {
                    if ($winner === 'player_a') {
                        $betWon = true;
                        $multiplier = (float) $bet->odds;
                        $outcomeText = 'Player A Won ('.$evalA['rank_title'].')';
                    } elseif ($winner === 'tie') {
                        // Push: Refund bet amount on Tie
                        $betWon = true;
                        $multiplier = 1.0;
                        $outcomeText = 'Push / Tie Refund';
                    } else {
                        $outcomeText = 'Player B Won';
                    }
                } elseif ($bet->bet_type === 'player_b') {
                    if ($winner === 'player_b') {
                        $betWon = true;
                        $multiplier = (float) $bet->odds;
                        $outcomeText = 'Player B Won ('.$evalB['rank_title'].')';
                    } elseif ($winner === 'tie') {
                        // Push: Refund bet amount on Tie
                        $betWon = true;
                        $multiplier = 1.0;
                        $outcomeText = 'Push / Tie Refund';
                    } else {
                        $outcomeText = 'Player A Won';
                    }
                } elseif ($bet->bet_type === 'tie') {
                    if ($winner === 'tie') {
                        $betWon = true;
                        $multiplier = (float) $bet->odds;
                        $outcomeText = 'TIE Match!';
                    } else {
                        $outcomeText = ucfirst(str_replace('_', ' ', $winner)).' Won';
                    }
                } elseif ($bet->bet_type === 'pair_plus_a') {
                    $pairMult = self::PAIR_PLUS_MULTIPLIERS[$evalA['rank_type']] ?? 0.0;
                    if ($pairMult > 0) {
                        $betWon = true;
                        $multiplier = $pairMult;
                        $outcomeText = 'Player A Pair Plus Hit: '.$evalA['rank_title'].' ('.$pairMult.'X)';
                    } else {
                        $outcomeText = 'Player A High Card (No Pair)';
                    }
                } elseif ($bet->bet_type === 'pair_plus_b') {
                    $pairMult = self::PAIR_PLUS_MULTIPLIERS[$evalB['rank_type']] ?? 0.0;
                    if ($pairMult > 0) {
                        $betWon = true;
                        $multiplier = $pairMult;
                        $outcomeText = 'Player B Pair Plus Hit: '.$evalB['rank_title'].' ('.$pairMult.'X)';
                    } else {
                        $outcomeText = 'Player B High Card (No Pair)';
                    }
                }

                // Credit user wallet if won
                if ($betWon) {
                    $payoutDetails = GamePayoutService::calculate($bet->bet_amount, $multiplier);
                    $payout = (float) $payoutDetails['player_payout'];
                    $netProfit = (float) $payoutDetails['net_profit'];
                    $commissionAmount = (float) $payoutDetails['commission_amount'];

                    $bet->status = 'won';
                    $bet->payout_amount = $payout;
                    $bet->net_profit = $netProfit;
                    $bet->commission_amount = $commissionAmount;
                    $bet->hand_outcome = $outcomeText;
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
                            'status' => 'completed',
                            'admin_notes' => "Won 20-20 Teen Patti Round #{$lockedRound->round_id} ({$outcomeText}){$feeNote}",
                        ]);
                    }

                    $totalPayout += $payout;
                } else {
                    $bet->status = 'lost';
                    $bet->payout_amount = 0.00;
                    $bet->net_profit = -(float) $bet->bet_amount;
                    $bet->commission_amount = 0.00;
                    $bet->hand_outcome = $outcomeText;
                    $bet->save();
                }
            }

            $lockedRound->total_payout_amount = $totalPayout;
            $lockedRound->save();
        });
    }

    /**
     * Generate 6 distinct random cards (3 for A, 3 for B) satisfying requested winner if set.
     */
    public static function generateValidDeal(?string $requestedWinner = null): array
    {
        $deck = self::getFreshDeck();
        shuffle($deck);

        $cardsA = array_slice($deck, 0, 3);
        $cardsB = array_slice($deck, 3, 3);

        $evalA = self::evaluateHand($cardsA);
        $evalB = self::evaluateHand($cardsB);
        $winner = self::compareHands($evalA, $evalB);

        if ($requestedWinner === 'player_a' && $winner !== 'player_a') {
            // Swap if B was higher
            if ($evalB['score'] > $evalA['score']) {
                return [$cardsB, $cardsA, $evalB, $evalA, 'player_a'];
            }
            // If it was a tie, re-roll a few times until A wins
            for ($attempt = 0; $attempt < 20; $attempt++) {
                shuffle($deck);
                $cA = array_slice($deck, 0, 3);
                $cB = array_slice($deck, 3, 3);
                $eA = self::evaluateHand($cA);
                $eB = self::evaluateHand($cB);
                if ($eA['score'] > $eB['score']) {
                    return [$cA, $cB, $eA, $eB, 'player_a'];
                }
            }
        } elseif ($requestedWinner === 'player_b' && $winner !== 'player_b') {
            // Swap if A was higher
            if ($evalA['score'] > $evalB['score']) {
                return [$cardsB, $cardsA, $evalB, $evalA, 'player_b'];
            }
            // If it was a tie, re-roll a few times until B wins
            for ($attempt = 0; $attempt < 20; $attempt++) {
                shuffle($deck);
                $cA = array_slice($deck, 0, 3);
                $cB = array_slice($deck, 3, 3);
                $eA = self::evaluateHand($cA);
                $eB = self::evaluateHand($cB);
                if ($eB['score'] > $eA['score']) {
                    return [$cA, $cB, $eA, $eB, 'player_b'];
                }
            }
        }

        return [$cardsA, $cardsB, $evalA, $evalB, $winner];
    }

    /**
     * Place a bet on the currently active Teen Patti round.
     */
    public static function placeBet(User $user, string $betType, float $amount): array
    {
        $setting = AdminSetting::current();
        $minBet = (float) ($setting->teen_patti_min_bet ?? 5.00);
        $maxBet = (float) ($setting->teen_patti_max_bet ?? 25000.00);

        if ($amount < $minBet || $amount > $maxBet) {
            return [
                'success' => false,
                'message' => "Bet amount must be between ₹{$minBet} and ₹{$maxBet}.",
            ];
        }

        $validTypes = ['player_a', 'player_b', 'pair_plus_a', 'pair_plus_b', 'tie'];
        if (! in_array($betType, $validTypes, true)) {
            return [
                'success' => false,
                'message' => 'Invalid Teen Patti bet type selected.',
            ];
        }

        $currentRound = self::getCurrentRound();
        if ($currentRound['is_locked'] || $currentRound['status'] !== 'betting') {
            return [
                'success' => false,
                'message' => 'Betting is currently closed for this round! Please wait for the next round.',
            ];
        }

        $roundId = $currentRound['round_id'];

        return DB::transaction(function () use ($user, $betType, $amount, $roundId, $setting) {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            if (! $lockedUser || (float) $lockedUser->balance < $amount) {
                return [
                    'success' => false,
                    'message' => 'Insufficient wallet balance. Please recharge your wallet.',
                ];
            }

            // Deduct balance
            $balBefore = (float) $lockedUser->balance;
            $balAfter = round($balBefore - $amount, 2);
            $lockedUser->balance = $balAfter;
            $lockedUser->save();

            // Determine default odds
            $odds = match ($betType) {
                'player_a' => (float) ($setting->teen_patti_odds_player ?? self::ODDS_PLAYER_A),
                'player_b' => (float) ($setting->teen_patti_odds_player ?? self::ODDS_PLAYER_B),
                'tie' => (float) ($setting->teen_patti_odds_tie ?? self::ODDS_TIE),
                'pair_plus_a', 'pair_plus_b' => 40.00, // Max potential multiplier for Pair Plus
                default => 1.95,
            };

            $betId = 'TPB'.Carbon::now()->format('ymdHis').mt_rand(10, 99);

            $bet = TeenPattiBet::create([
                'bet_id' => $betId,
                'round_id' => $roundId,
                'user_id' => $lockedUser->id,
                'bet_type' => $betType,
                'bet_amount' => $amount,
                'odds' => $odds,
                'status' => 'pending',
            ]);

            // Create Transaction record
            Transaction::create([
                'user_id' => $lockedUser->id,
                'type' => 'bet_placed',
                'amount' => $amount,
                'balance_before' => $balBefore,
                'balance_after' => $balAfter,
                'status' => 'completed',
                'admin_notes' => "Placed 20-20 Teen Patti Bet on {$betType} (Round #{$roundId})",
            ]);

            // Increment round total bet metrics
            $round = TeenPattiRound::where('round_id', $roundId)->first();
            if ($round) {
                $round->increment('total_bets_count');
                $round->increment('total_bet_amount', $amount);
            }

            return [
                'success' => true,
                'message' => 'Bet placed successfully!',
                'bet' => $bet,
                'new_balance' => $balAfter,
            ];
        });
    }
}
