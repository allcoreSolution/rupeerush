<?php

namespace App\Services;

use App\Models\AdminSetting;
use App\Models\DragonTigerBet;
use App\Models\DragonTigerRound;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DragonTigerEngine
{
    public const DEFAULT_ROUND_DURATION = 25; // 25 seconds per round

    public const LOCK_COUNTDOWN = 5; // Last 5 seconds locked for card deal & reveal

    public const ODDS_DRAGON = 1.95;

    public const ODDS_TIGER = 1.95;

    public const ODDS_TIE = 9.00;

    public const ODDS_SUITED_TIE = 50.00;

    public const ODDS_SIDE_BET = 1.95;

    public const SUITS = ['S', 'H', 'D', 'C']; // Spades, Hearts, Diamonds, Clubs

    /**
     * Card ranks in Dragon Tiger (Ace is lowest = 1, King is highest = 13).
     */
    public const RANKS = [
        'A' => 1,
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
    ];

    /**
     * Generate standard 52-card deck.
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
     * Parse card string into structured properties.
     */
    public static function parseCard(string $card): array
    {
        $suit = substr($card, -1);
        $rankStr = substr($card, 0, -1);
        $rankVal = self::RANKS[$rankStr] ?? 1;

        $suitSymbols = [
            'S' => '♠',
            'H' => '♥',
            'D' => '♦',
            'C' => '♣',
        ];

        $isRed = in_array($suit, ['H', 'D'], true);
        $isSeven = ($rankVal === 7);

        return [
            'code' => $card,
            'rank' => $rankStr,
            'value' => $rankVal,
            'suit' => $suit,
            'symbol' => $suitSymbols[$suit] ?? '♠',
            'is_red' => $isRed,
            'color' => $isRed ? '#ef4444' : '#0f172a',
            'is_big' => $rankVal > 7,
            'is_small' => $rankVal < 7,
            'is_odd' => ($rankVal % 2 !== 0 && ! $isSeven),
            'is_even' => ($rankVal % 2 === 0),
            'is_seven' => $isSeven,
            'title' => self::getRankLabel($rankVal).' of '.self::getSuitName($suit),
        ];
    }

    public static function getRankLabel(int $val): string
    {
        return match ($val) {
            1 => 'Ace',
            11 => 'Jack',
            12 => 'Queen',
            13 => 'King',
            default => (string) $val,
        };
    }

    public static function getSuitName(string $suit): string
    {
        return match ($suit) {
            'S' => 'Spades',
            'H' => 'Hearts',
            'D' => 'Diamonds',
            'C' => 'Clubs',
            default => $suit,
        };
    }

    /**
     * Get or create active round synchronized with wall-clock epoch.
     */
    public static function getCurrentRound(): array
    {
        $setting = AdminSetting::current();
        $duration = max(15, (int) ($setting->dragon_tiger_round_duration ?? self::DEFAULT_ROUND_DURATION));

        $now = Carbon::now();
        $epoch = $now->timestamp;

        $roundNumber = (int) floor($epoch / $duration);
        $roundStartTime = Carbon::createFromTimestamp($roundNumber * $duration);
        $roundEndTime = $roundStartTime->copy()->addSeconds($duration);

        $elapsedSeconds = $epoch % $duration;
        $remainingSeconds = max(0, $duration - $elapsedSeconds);
        $isLocked = $remainingSeconds <= self::LOCK_COUNTDOWN;

        $roundId = 'DT'.$roundStartTime->format('YmdHi').str_pad($roundStartTime->second, 2, '0', STR_PAD_LEFT);

        // 1. Auto-settle any past rounds that should be completed
        self::settlePreviousPendingRounds($roundId);

        // 2. Fetch or initialize active round
        $round = DragonTigerRound::firstOrCreate(
            ['round_id' => $roundId],
            [
                'start_time' => $roundStartTime,
                'end_time' => $roundEndTime,
                'status' => $isLocked ? 'dealing' : 'betting',
            ]
        );

        if ($isLocked && $round->status === 'betting') {
            $round->status = 'dealing';
            $round->save();
        }

        // 3. Roadmap / Bead Plate history (last 50 completed rounds for full casino road map)
        $history = DragonTigerRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get(['round_id', 'winner', 'dragon_card', 'tiger_card', 'created_at']);

        // 4. Bets summary on active round
        $roundBetsSummary = DragonTigerBet::where('round_id', $roundId)
            ->selectRaw('bet_type, COUNT(*) as count, SUM(bet_amount) as total_amount')
            ->groupBy('bet_type')
            ->get()
            ->keyBy('bet_type')
            ->toArray();

        return [
            'round_id' => $roundId,
            'round_model' => $round,
            'start_time' => $roundStartTime->toIso8601String(),
            'end_time' => $roundEndTime->toIso8601String(),
            'duration' => $duration,
            'remaining_seconds' => $remainingSeconds,
            'is_locked' => $isLocked,
            'status' => $round->status,
            'min_bet' => (float) ($setting->dragon_tiger_min_bet ?? 5.00),
            'max_bet' => (float) ($setting->dragon_tiger_max_bet ?? 25000.00),
            'odds_dragon' => (float) ($setting->dragon_tiger_odds_main ?? self::ODDS_DRAGON),
            'odds_tiger' => (float) ($setting->dragon_tiger_odds_main ?? self::ODDS_TIGER),
            'odds_tie' => (float) ($setting->dragon_tiger_odds_tie ?? self::ODDS_TIE),
            'odds_suited_tie' => (float) ($setting->dragon_tiger_odds_suited_tie ?? self::ODDS_SUITED_TIE),
            'odds_side' => self::ODDS_SIDE_BET,
            'history' => $history,
            'bets_summary' => $roundBetsSummary,
        ];
    }

    /**
     * Settle pending previous rounds.
     */
    public static function settlePreviousPendingRounds(string $currentRoundId): void
    {
        $pendingRounds = DragonTigerRound::where('round_id', '<', $currentRoundId)
            ->where('status', '!=', 'completed')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($pendingRounds as $round) {
            self::dealAndSettleRound($round);
        }
    }

    /**
     * Generate 2 cards satisfying winner target if specified.
     */
    public static function generateValidDeal(?string $requestedWinner = null): array
    {
        $deck = self::getFreshDeck();
        shuffle($deck);

        $cardDStr = $deck[0];
        $cardTStr = $deck[1];

        $cardD = self::parseCard($cardDStr);
        $cardT = self::parseCard($cardTStr);

        $winner = self::calculateWinner($cardD, $cardT);

        if (! empty($requestedWinner)) {
            // Force cards to match requested winner
            if ($requestedWinner === 'dragon') {
                $rankD = mt_rand(2, 13);
                $rankT = mt_rand(1, $rankD - 1);
                $cardD = self::createCustomCard($rankD, self::SUITS[array_rand(self::SUITS)]);
                $cardT = self::createCustomCard($rankT, self::SUITS[array_rand(self::SUITS)]);
                $winner = 'dragon';
            } elseif ($requestedWinner === 'tiger') {
                $rankT = mt_rand(2, 13);
                $rankD = mt_rand(1, $rankT - 1);
                $cardD = self::createCustomCard($rankD, self::SUITS[array_rand(self::SUITS)]);
                $cardT = self::createCustomCard($rankT, self::SUITS[array_rand(self::SUITS)]);
                $winner = 'tiger';
            } elseif ($requestedWinner === 'tie') {
                $rank = mt_rand(1, 13);
                $suits = self::SUITS;
                shuffle($suits);
                $cardD = self::createCustomCard($rank, $suits[0]);
                $cardT = self::createCustomCard($rank, $suits[1]);
                $winner = 'tie';
            } elseif ($requestedWinner === 'suited_tie') {
                $rank = mt_rand(1, 13);
                $suit = self::SUITS[array_rand(self::SUITS)];
                $cardD = self::createCustomCard($rank, $suit);
                $cardT = self::createCustomCard($rank, $suit);
                $winner = 'suited_tie';
            }
        }

        return [$cardD, $cardT, $winner];
    }

    /**
     * Create a card structure for a given rank and suit.
     */
    public static function createCustomCard(int $rankVal, string $suit): array
    {
        $rankMap = array_flip(self::RANKS);
        $rankStr = $rankMap[$rankVal] ?? (string) $rankVal;

        return self::parseCard($rankStr.$suit);
    }

    /**
     * Compare Dragon and Tiger cards.
     */
    public static function calculateWinner(array $cardD, array $cardT): string
    {
        if ($cardD['value'] > $cardT['value']) {
            return 'dragon';
        }
        if ($cardT['value'] > $cardD['value']) {
            return 'tiger';
        }

        // Rank equal
        if ($cardD['suit'] === $cardT['suit']) {
            return 'suited_tie';
        }

        return 'tie';
    }

    /**
     * Deal cards, evaluate winner & side bets, settle bets atomically.
     */
    public static function dealAndSettleRound(DragonTigerRound $round): void
    {
        if ($round->status === 'completed') {
            return;
        }

        DB::transaction(function () use ($round) {
            $lockedRound = DragonTigerRound::where('id', $round->id)->lockForUpdate()->first();
            if (! $lockedRound || $lockedRound->status === 'completed') {
                return;
            }

            // Determine target outcome
            $setting = AdminSetting::current();
            $mode = $setting->dragon_tiger_control_mode ?? 'fair';

            $requestedWinner = null;
            $hasBets = DragonTigerBet::where('round_id', $lockedRound->round_id)->exists();
            $shouldHouseWin = $hasBets && $setting->shouldHouseWin();

            if ($lockedRound->is_manual && ! empty($lockedRound->manual_winner)) {
                $requestedWinner = $lockedRound->manual_winner;
            } elseif ($mode === 'manual_override' && ! empty($setting->dragon_tiger_manual_winner)) {
                $requestedWinner = $setting->dragon_tiger_manual_winner;
            } elseif ($mode === 'least_liability' || $mode === 'house_trap' || ($hasBets && $shouldHouseWin)) {
                // Calculate total payout liability for Dragon vs Tiger vs Tie
                $payoutDragon = (float) DragonTigerBet::where('round_id', $lockedRound->round_id)
                    ->where('bet_type', 'dragon')
                    ->sum(DB::raw('bet_amount * odds'));

                $payoutTiger = (float) DragonTigerBet::where('round_id', $lockedRound->round_id)
                    ->where('bet_type', 'tiger')
                    ->sum(DB::raw('bet_amount * odds'));

                $payoutTie = (float) DragonTigerBet::where('round_id', $lockedRound->round_id)
                    ->where('bet_type', 'tie')
                    ->sum(DB::raw('bet_amount * odds'));

                // Pick the outcome with minimum payout liability (75-80% house profit)
                if ($payoutDragon <= $payoutTiger && $payoutDragon <= $payoutTie) {
                    $requestedWinner = 'dragon';
                } elseif ($payoutTiger <= $payoutDragon && $payoutTiger <= $payoutTie) {
                    $requestedWinner = 'tiger';
                } else {
                    $requestedWinner = 'tie';
                }
            }

            // Deal cards
            [$cardD, $cardT, $winner] = self::generateValidDeal($requestedWinner);

            $lockedRound->dragon_card = $cardD;
            $lockedRound->tiger_card = $cardT;
            $lockedRound->winner = $winner;
            $lockedRound->status = 'completed';

            // Settle all user bets
            $bets = DragonTigerBet::where('round_id', $lockedRound->round_id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->get();

            $totalPayout = 0.00;

            foreach ($bets as $bet) {
                $isWin = false;
                $multiplier = 0.0;
                $outcomeText = '';

                switch ($bet->bet_type) {
                    case 'dragon':
                        if ($winner === 'dragon') {
                            $isWin = true;
                            $multiplier = (float) $bet->odds;
                            $outcomeText = "Dragon Won ({$cardD['rank']} vs {$cardT['rank']})";
                        } elseif ($winner === 'tie' || $winner === 'suited_tie') {
                            // Standard casino push rule: 50% refund on tie
                            $isWin = true;
                            $multiplier = 0.50;
                            $outcomeText = "Tie Push (50% Refund on {$cardD['rank']} vs {$cardT['rank']})";
                        } else {
                            $outcomeText = "Dragon Lost ({$cardD['rank']} vs {$cardT['rank']})";
                        }
                        break;

                    case 'tiger':
                        if ($winner === 'tiger') {
                            $isWin = true;
                            $multiplier = (float) $bet->odds;
                            $outcomeText = "Tiger Won ({$cardT['rank']} vs {$cardD['rank']})";
                        } elseif ($winner === 'tie' || $winner === 'suited_tie') {
                            // Standard casino push rule: 50% refund on tie
                            $isWin = true;
                            $multiplier = 0.50;
                            $outcomeText = "Tie Push (50% Refund on {$cardT['rank']} vs {$cardD['rank']})";
                        } else {
                            $outcomeText = "Tiger Lost ({$cardT['rank']} vs {$cardD['rank']})";
                        }
                        break;

                    case 'tie':
                        if ($winner === 'tie' || $winner === 'suited_tie') {
                            $isWin = true;
                            $multiplier = (float) $bet->odds;
                            $outcomeText = "Tie Hit! ({$cardD['rank']} = {$cardT['rank']})";
                        } else {
                            $outcomeText = "No Tie ({$cardD['rank']} vs {$cardT['rank']})";
                        }
                        break;

                    case 'suited_tie':
                        if ($winner === 'suited_tie') {
                            $isWin = true;
                            $multiplier = (float) $bet->odds;
                            $outcomeText = "SUITED TIE JACKPOT HIT! ({$cardD['code']} = {$cardT['code']})";
                        } else {
                            $outcomeText = 'No Suited Tie';
                        }
                        break;

                        // Dragon Side Bets
                    case 'dragon_big':
                        if ($cardD['is_big']) {
                            $isWin = true;
                            $multiplier = (float) $bet->odds;
                            $outcomeText = "Dragon Big Hit ({$cardD['rank']})";
                        } else {
                            $outcomeText = "Dragon Small/Seven ({$cardD['rank']})";
                        }
                        break;

                    case 'dragon_small':
                        if ($cardD['is_small']) {
                            $isWin = true;
                            $multiplier = (float) $bet->odds;
                            $outcomeText = "Dragon Small Hit ({$cardD['rank']})";
                        } else {
                            $outcomeText = "Dragon Big/Seven ({$cardD['rank']})";
                        }
                        break;

                    case 'dragon_red':
                        if ($cardD['is_red']) {
                            $isWin = true;
                            $multiplier = (float) $bet->odds;
                            $outcomeText = "Dragon Red Hit ({$cardD['symbol']})";
                        } else {
                            $outcomeText = "Dragon Black ({$cardD['symbol']})";
                        }
                        break;

                    case 'dragon_black':
                        if (! $cardD['is_red']) {
                            $isWin = true;
                            $multiplier = (float) $bet->odds;
                            $outcomeText = "Dragon Black Hit ({$cardD['symbol']})";
                        } else {
                            $outcomeText = "Dragon Red ({$cardD['symbol']})";
                        }
                        break;

                    case 'dragon_odd':
                        if ($cardD['is_odd']) {
                            $isWin = true;
                            $multiplier = (float) $bet->odds;
                            $outcomeText = "Dragon Odd Hit ({$cardD['rank']})";
                        } else {
                            $outcomeText = "Dragon Even/Seven ({$cardD['rank']})";
                        }
                        break;

                    case 'dragon_even':
                        if ($cardD['is_even']) {
                            $isWin = true;
                            $multiplier = (float) $bet->odds;
                            $outcomeText = "Dragon Even Hit ({$cardD['rank']})";
                        } else {
                            $outcomeText = "Dragon Odd ({$cardD['rank']})";
                        }
                        break;

                        // Tiger Side Bets
                    case 'tiger_big':
                        if ($cardT['is_big']) {
                            $isWin = true;
                            $multiplier = (float) $bet->odds;
                            $outcomeText = "Tiger Big Hit ({$cardT['rank']})";
                        } else {
                            $outcomeText = "Tiger Small/Seven ({$cardT['rank']})";
                        }
                        break;

                    case 'tiger_small':
                        if ($cardT['is_small']) {
                            $isWin = true;
                            $multiplier = (float) $bet->odds;
                            $outcomeText = "Tiger Small Hit ({$cardT['rank']})";
                        } else {
                            $outcomeText = "Tiger Big/Seven ({$cardT['rank']})";
                        }
                        break;

                    case 'tiger_red':
                        if ($cardT['is_red']) {
                            $isWin = true;
                            $multiplier = (float) $bet->odds;
                            $outcomeText = "Tiger Red Hit ({$cardT['symbol']})";
                        } else {
                            $outcomeText = "Tiger Black ({$cardT['symbol']})";
                        }
                        break;

                    case 'tiger_black':
                        if (! $cardT['is_red']) {
                            $isWin = true;
                            $multiplier = (float) $bet->odds;
                            $outcomeText = "Tiger Black Hit ({$cardT['symbol']})";
                        } else {
                            $outcomeText = "Tiger Red ({$cardT['symbol']})";
                        }
                        break;

                    case 'tiger_odd':
                        if ($cardT['is_odd']) {
                            $isWin = true;
                            $multiplier = (float) $bet->odds;
                            $outcomeText = "Tiger Odd Hit ({$cardT['rank']})";
                        } else {
                            $outcomeText = "Tiger Even/Seven ({$cardT['rank']})";
                        }
                        break;

                    case 'tiger_even':
                        if ($cardT['is_even']) {
                            $isWin = true;
                            $multiplier = (float) $bet->odds;
                            $outcomeText = "Tiger Even Hit ({$cardT['rank']})";
                        } else {
                            $outcomeText = "Tiger Odd ({$cardT['rank']})";
                        }
                        break;

                    default:
                        $outcomeText = 'Unknown Bet';
                        break;
                }

                if ($isWin) {
                    $payoutDetails = GamePayoutService::calculate($bet->bet_amount, $multiplier);
                    $payout = (float) $payoutDetails['player_payout'];
                    $netProfit = (float) $payoutDetails['net_profit'];
                    $commissionAmount = (float) $payoutDetails['commission_amount'];

                    $bet->status = ($multiplier == 0.50) ? 'refunded' : 'won';
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
                            'admin_notes' => "Dragon Tiger Round #{$lockedRound->round_id} ({$outcomeText}){$feeNote}",
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
     * Place bet on active Dragon Tiger round.
     */
    public static function placeBet(User $user, string $betType, float $amount): array
    {
        $setting = AdminSetting::current();
        $minBet = (float) ($setting->dragon_tiger_min_bet ?? 5.00);
        $maxBet = (float) ($setting->dragon_tiger_max_bet ?? 25000.00);

        if ($amount < $minBet || $amount > $maxBet) {
            return [
                'success' => false,
                'message' => "Bet amount must be between ₹{$minBet} and ₹{$maxBet}.",
            ];
        }

        $validTypes = [
            'dragon', 'tiger', 'tie', 'suited_tie',
            'dragon_big', 'dragon_small', 'dragon_red', 'dragon_black', 'dragon_odd', 'dragon_even',
            'tiger_big', 'tiger_small', 'tiger_red', 'tiger_black', 'tiger_odd', 'tiger_even',
        ];

        if (! in_array($betType, $validTypes, true)) {
            return [
                'success' => false,
                'message' => 'Invalid Dragon Tiger bet type selected.',
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

            // Determine odds
            $odds = match ($betType) {
                'dragon' => (float) ($setting->dragon_tiger_odds_main ?? self::ODDS_DRAGON),
                'tiger' => (float) ($setting->dragon_tiger_odds_main ?? self::ODDS_TIGER),
                'tie' => (float) ($setting->dragon_tiger_odds_tie ?? self::ODDS_TIE),
                'suited_tie' => (float) ($setting->dragon_tiger_odds_suited_tie ?? self::ODDS_SUITED_TIE),
                default => self::ODDS_SIDE_BET,
            };

            $betId = 'DTB'.Carbon::now()->format('ymdHis').mt_rand(10, 99);

            $bet = DragonTigerBet::create([
                'bet_id' => $betId,
                'round_id' => $roundId,
                'user_id' => $lockedUser->id,
                'bet_type' => $betType,
                'bet_amount' => $amount,
                'odds' => $odds,
                'status' => 'pending',
            ]);

            // Transaction log
            Transaction::create([
                'user_id' => $lockedUser->id,
                'type' => 'bet_placed',
                'amount' => $amount,
                'balance_before' => $balBefore,
                'balance_after' => $balAfter,
                'status' => 'completed',
                'admin_notes' => "Placed Dragon Tiger Bet on {$betType} (Round #{$roundId})",
            ]);

            // Increment round metrics
            $round = DragonTigerRound::where('round_id', $roundId)->first();
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
