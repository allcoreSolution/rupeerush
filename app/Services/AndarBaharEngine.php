<?php

namespace App\Services;

use App\Models\AdminSetting;
use App\Models\AndarBaharBet;
use App\Models\AndarBaharRound;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AndarBaharEngine
{
    public const ROUND_DURATION = 35; // 35 seconds per round (25s betting, 10s dealing/result)

    public const LOCK_SECONDS = 10;   // last 10 seconds betting is locked, dealing starts

    public const MAIN_MULTIPLIER = 1.95;

    public const RANGE_MULTIPLIERS = [
        '1_5' => 3.50,
        '6_10' => 4.00,
        '11_15' => 5.00,
        '16_25' => 4.50,
        '26_plus' => 8.00,
    ];

    /**
     * Build standard 52 card deck.
     */
    public static function createDeck(): array
    {
        $suits = [
            ['code' => 'H', 'name' => 'hearts', 'color' => 'red', 'symbol' => '♥'],
            ['code' => 'D', 'name' => 'diamonds', 'color' => 'red', 'symbol' => '♦'],
            ['code' => 'C', 'name' => 'clubs', 'color' => 'black', 'symbol' => '♣'],
            ['code' => 'S', 'name' => 'spades', 'color' => 'black', 'symbol' => '♠'],
        ];

        $ranks = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];

        $deck = [];
        foreach ($suits as $suit) {
            foreach ($ranks as $rank) {
                $deck[] = [
                    'rank' => $rank,
                    'suit' => $suit['code'],
                    'suit_name' => $suit['name'],
                    'color' => $suit['color'],
                    'symbol' => $suit['symbol'],
                ];
            }
        }

        return $deck;
    }

    /**
     * Simulate dealing for a round.
     * Optionally force winning side for admin manual control.
     */
    public static function simulateGame(?string $targetWinningSide = null, ?array $fixedJoker = null): array
    {
        $targetSide = $targetWinningSide ? strtolower($targetWinningSide) : null;
        $maxAttempts = 50;
        $attempts = 0;

        do {
            $attempts++;
            $deck = self::createDeck();
            shuffle($deck);

            // First card is the Joker (or preserve existing Joker if fixed)
            if ($fixedJoker) {
                $jokerCard = $fixedJoker;
                $deck = array_values(array_filter($deck, function ($c) use ($fixedJoker) {
                    return ! ($c['rank'] === $fixedJoker['rank'] && $c['suit'] === $fixedJoker['suit']);
                }));
                shuffle($deck);
            } else {
                $jokerCard = array_shift($deck);
            }

            $dealtCards = [];
            $winningSide = null;
            $winningCard = null;
            $side = 'andar'; // First card always dealt to Andar

            while (! empty($deck)) {
                $card = array_shift($deck);
                $dealtCards[] = [
                    'side' => $side,
                    'card' => $card,
                ];

                // Check if card rank matches Joker rank
                if ($card['rank'] === $jokerCard['rank']) {
                    $winningSide = $side;
                    $winningCard = $card;
                    break;
                }

                // Alternate sides
                $side = ($side === 'andar') ? 'bahar' : 'andar';
            }

            if (! $targetSide || $winningSide === $targetSide || $attempts >= $maxAttempts) {
                break;
            }
        } while (true);

        return [
            'joker_card' => $jokerCard,
            'dealt_cards' => $dealtCards,
            'winning_side' => $winningSide,
            'winning_card' => $winningCard,
            'total_cards_dealt' => count($dealtCards),
        ];
    }

    /**
     * Get or initialize current active round.
     */
    public static function getCurrentRound(): array
    {
        $duration = self::ROUND_DURATION;
        $now = Carbon::now('Asia/Kolkata');
        $todayStart = $now->copy()->startOfDay();
        $secondsSinceMidnight = ($now->hour * 3600) + ($now->minute * 60) + $now->second;

        $slotIndex = (int) floor($secondsSinceMidnight / $duration) + 1;
        $roundId = 'AB'.$now->format('Ymd').str_pad((string) $slotIndex, 5, '0', STR_PAD_LEFT);

        $startSeconds = ($slotIndex - 1) * $duration;
        $endSeconds = $slotIndex * $duration;
        $startTime = $todayStart->copy()->addSeconds($startSeconds);
        $endTime = $todayStart->copy()->addSeconds($endSeconds);

        $remainingSeconds = max(0, $endSeconds - $secondsSinceMidnight);
        $isLocked = ($remainingSeconds <= self::LOCK_SECONDS);

        // Settle any older pending rounds
        self::settlePreviousPendingRounds($roundId);

        // Fetch or create round
        $round = AndarBaharRound::where('round_id', $roundId)->first();

        if (! $round) {
            $gameSim = self::simulateGame();

            $round = AndarBaharRound::create([
                'round_id' => $roundId,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'joker_card' => $gameSim['joker_card'],
                'dealt_cards' => $gameSim['dealt_cards'],
                'winning_side' => $gameSim['winning_side'],
                'winning_card' => $gameSim['winning_card'],
                'total_cards_dealt' => $gameSim['total_cards_dealt'],
                'status' => $isLocked ? 'dealing' : 'betting',
                'is_manual' => false,
                'manual_winning_side' => null,
            ]);
        } else {
            if ($isLocked && $round->status === 'betting') {
                $round->status = 'dealing';
                $round->save();
            }
        }

        return [
            'duration' => $duration,
            'round_id' => $roundId,
            'round_model' => $round,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'remaining_seconds' => $remainingSeconds,
            'is_locked' => $isLocked,
        ];
    }

    /**
     * Settle any older round that has ended.
     */
    public static function settlePreviousPendingRounds(string $currentRoundId): void
    {
        $unsettled = AndarBaharRound::where('round_id', '<', $currentRoundId)
            ->where('status', '!=', 'completed')
            ->orderBy('id', 'asc')
            ->limit(5)
            ->get();

        foreach ($unsettled as $round) {
            self::settleRound($round);
        }
    }

    /**
     * Settle a round: evaluate bets and credit winnings atomically.
     */
    public static function settleRound(AndarBaharRound $round): AndarBaharRound
    {
        if ($round->status === 'completed') {
            return $round;
        }

        return DB::transaction(function () use ($round) {
            $lockedRound = AndarBaharRound::where('id', $round->id)->lockForUpdate()->first();
            if ($lockedRound->status === 'completed') {
                return $lockedRound;
            }

            $setting = AdminSetting::current();

            // If manual result set by admin, enforce that side
            if ($lockedRound->is_manual && $lockedRound->manual_winning_side && $lockedRound->winning_side !== $lockedRound->manual_winning_side) {
                $sim = self::simulateGame($lockedRound->manual_winning_side, $lockedRound->joker_card);
                $lockedRound->dealt_cards = $sim['dealt_cards'];
                $lockedRound->winning_side = $sim['winning_side'];
                $lockedRound->winning_card = $sim['winning_card'];
                $lockedRound->total_cards_dealt = $sim['total_cards_dealt'];
            } elseif (! $lockedRound->is_manual && ($setting->auto_result_mode ?? true)) {
                // Auto-pilot profit optimization: pick side with least payout liability
                $hasPendingBets = AndarBaharBet::where('round_id', $lockedRound->round_id)
                    ->where('status', 'pending')
                    ->exists();

                if ($hasPendingBets) {
                    $exposure = self::calculateExposure($lockedRound);
                    $safestSide = $exposure['safest_side'];
                    if ($lockedRound->winning_side !== $safestSide) {
                        $sim = self::simulateGame($safestSide, $lockedRound->joker_card);
                        $lockedRound->dealt_cards = $sim['dealt_cards'];
                        $lockedRound->winning_side = $sim['winning_side'];
                        $lockedRound->winning_card = $sim['winning_card'];
                        $lockedRound->total_cards_dealt = $sim['total_cards_dealt'];
                    }
                }
            }

            // If game attributes missing, generate
            if (! $lockedRound->winning_side || empty($lockedRound->dealt_cards)) {
                $target = $lockedRound->is_manual ? $lockedRound->manual_winning_side : null;
                $sim = self::simulateGame($target, $lockedRound->joker_card);
                $lockedRound->joker_card = $sim['joker_card'];
                $lockedRound->dealt_cards = $sim['dealt_cards'];
                $lockedRound->winning_side = $sim['winning_side'];
                $lockedRound->winning_card = $sim['winning_card'];
                $lockedRound->total_cards_dealt = $sim['total_cards_dealt'];
            }

            $winningSide = $lockedRound->winning_side;
            $totalCards = $lockedRound->total_cards_dealt;

            // Determine card range tag
            $cardRangeTag = null;
            if ($totalCards >= 1 && $totalCards <= 5) {
                $cardRangeTag = '1_5';
            } elseif ($totalCards >= 6 && $totalCards <= 10) {
                $cardRangeTag = '6_10';
            } elseif ($totalCards >= 11 && $totalCards <= 15) {
                $cardRangeTag = '11_15';
            } elseif ($totalCards >= 16 && $totalCards <= 25) {
                $cardRangeTag = '16_25';
            } else {
                $cardRangeTag = '26_plus';
            }

            $andarMultiplier = (float) ($setting->andar_multiplier ?? self::MAIN_MULTIPLIER);
            $baharMultiplier = (float) ($setting->bahar_multiplier ?? self::MAIN_MULTIPLIER);

            $rangeMultipliers = [
                '1_5' => (float) ($setting->andar_bahar_range_1_5 ?? 3.50),
                '6_10' => (float) ($setting->andar_bahar_range_6_10 ?? 4.00),
                '11_15' => (float) ($setting->andar_bahar_range_11_15 ?? 5.00),
                '16_25' => (float) ($setting->andar_bahar_range_16_25 ?? 4.50),
                '26_plus' => (float) ($setting->andar_bahar_range_26_plus ?? 8.00),
            ];

            $pendingBets = AndarBaharBet::where('round_id', $lockedRound->round_id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->get();

            $totalPayout = 0.00;

            foreach ($pendingBets as $bet) {
                $isWinner = false;
                $multiplier = (float) $bet->win_multiplier;

                if ($bet->bet_type === 'main') {
                    if (strtolower($bet->bet_selection) === strtolower($winningSide)) {
                        $isWinner = true;
                        $multiplier = (strtolower($bet->bet_selection) === 'andar') ? $andarMultiplier : $baharMultiplier;
                    }
                } elseif ($bet->bet_type === 'cards_range') {
                    if ($bet->bet_selection === $cardRangeTag) {
                        $isWinner = true;
                        $multiplier = $rangeMultipliers[$cardRangeTag] ?? 3.50;
                    }
                }

                if ($isWinner) {
                    $payoutDetails = GamePayoutService::calculate($bet->amount, $multiplier);
                    $winAmount = $payoutDetails['player_payout'];
                    $commissionAmount = $payoutDetails['commission_amount'];

                    $bet->win_amount = $winAmount;
                    $bet->commission_amount = $commissionAmount;
                    $bet->win_multiplier = $multiplier;
                    $bet->status = 'won';
                    $bet->evaluated_at = Carbon::now();
                    $bet->save();

                    // Credit user balance atomically
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
                            'status' => 'completed',
                            'admin_notes' => "Won ₹{$winAmount} on Andar Bahar Round #{$lockedRound->round_id} [{$bet->bet_selection}]{$feeNote}",
                        ]);
                    }

                    $totalPayout += $winAmount;
                } else {
                    $bet->win_amount = 0.00;
                    $bet->commission_amount = 0.00;
                    $bet->status = 'lost';
                    $bet->evaluated_at = Carbon::now();
                    $bet->save();
                }
            }

            $lockedRound->total_payout_amount = $totalPayout;
            $lockedRound->status = 'completed';
            $lockedRound->save();

            return $lockedRound;
        });
    }

    /**
     * Calculate house exposure & risk for active round.
     *
     * @return array<string, mixed>
     */
    public static function calculateExposure(AndarBaharRound $round): array
    {
        $setting = AdminSetting::current();
        $andarMult = (float) ($setting->andar_multiplier ?? self::MAIN_MULTIPLIER);
        $baharMult = (float) ($setting->bahar_multiplier ?? self::MAIN_MULTIPLIER);

        $bets = AndarBaharBet::where('round_id', $round->round_id)->get();

        $totalPool = (float) $bets->sum('amount');
        $andarBets = $bets->where('bet_type', 'main')->filter(fn ($b) => strtolower($b->bet_selection) === 'andar');
        $baharBets = $bets->where('bet_type', 'main')->filter(fn ($b) => strtolower($b->bet_selection) === 'bahar');

        $andarTotal = (float) $andarBets->sum('amount');
        $baharTotal = (float) $baharBets->sum('amount');

        $andarPayout = (float) $andarBets->sum(fn ($b) => $b->amount * ($b->win_multiplier ?: $andarMult));
        $baharPayout = (float) $baharBets->sum(fn ($b) => $b->amount * ($b->win_multiplier ?: $baharMult));

        $andarProfit = $totalPool - $andarPayout;
        $baharProfit = $totalPool - $baharPayout;

        $safestSide = ($andarPayout <= $baharPayout) ? 'andar' : 'bahar';

        $rangeBets = $bets->where('bet_type', 'cards_range');
        $rangeTotal = (float) $rangeBets->sum('amount');

        return [
            'total_pool' => $totalPool,
            'total_bets' => $bets->count(),
            'total_bets_count' => $bets->count(),
            'andar_total' => $andarTotal,
            'andar_amount' => $andarTotal,
            'andar_bets_count' => $andarBets->count(),
            'bahar_total' => $baharTotal,
            'bahar_amount' => $baharTotal,
            'bahar_bets_count' => $baharBets->count(),
            'range_total' => $rangeTotal,
            'range_bets_count' => $rangeBets->count(),
            'andar_payout' => $andarPayout,
            'andar_projected_payout' => $andarPayout,
            'bahar_payout' => $baharPayout,
            'bahar_projected_payout' => $baharPayout,
            'andar_profit' => $andarProfit,
            'house_profit_if_andar' => $andarProfit,
            'bahar_profit' => $baharProfit,
            'house_profit_if_bahar' => $baharProfit,
            'safest_side' => $safestSide,
        ];
    }

    /**
     * Place bet on active round.
     */
    public static function placeBet(User $user, string $betType, string $betSelection, float $amount): array
    {
        if ($user->status !== 'active') {
            return [
                'success' => false,
                'message' => 'Your account is suspended. Please contact customer support.',
            ];
        }

        $current = self::getCurrentRound();
        $round = $current['round_model'];

        if ($current['is_locked'] || $round->status !== 'betting') {
            return [
                'success' => false,
                'message' => 'Betting is currently closed for this round. Please wait for the next deal.',
            ];
        }

        $validMain = ['andar', 'bahar'];
        $setting = AdminSetting::current();

        $rangeMultipliers = [
            '1_5' => (float) ($setting->andar_bahar_range_1_5 ?? 3.50),
            '6_10' => (float) ($setting->andar_bahar_range_6_10 ?? 4.00),
            '11_15' => (float) ($setting->andar_bahar_range_11_15 ?? 5.00),
            '16_25' => (float) ($setting->andar_bahar_range_16_25 ?? 4.50),
            '26_plus' => (float) ($setting->andar_bahar_range_26_plus ?? 8.00),
        ];

        if ($betType === 'main' && ! in_array(strtolower($betSelection), $validMain, true)) {
            return [
                'success' => false,
                'message' => 'Invalid side selection. Choose Andar or Bahar.',
            ];
        }

        if ($betType === 'cards_range' && ! array_key_exists($betSelection, $rangeMultipliers)) {
            return [
                'success' => false,
                'message' => 'Invalid cards count range selected.',
            ];
        }

        $minBet = (float) ($setting->andar_bahar_min_bet ?? 10.00);
        $maxBet = (float) ($setting->andar_bahar_max_bet ?? 50000.00);

        if ($amount < $minBet) {
            return [
                'success' => false,
                'message' => "Minimum bet amount is ₹{$minBet}.",
            ];
        }

        if ($amount > $maxBet) {
            return [
                'success' => false,
                'message' => "Maximum bet amount is ₹{$maxBet}.",
            ];
        }

        return DB::transaction(function () use ($user, $round, $betType, $betSelection, $amount, $setting, $rangeMultipliers) {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            if (! $lockedUser || $lockedUser->status !== 'active') {
                return [
                    'success' => false,
                    'message' => 'Account is inactive or suspended.',
                ];
            }

            if ((float) $lockedUser->balance < $amount) {
                return [
                    'success' => false,
                    'message' => 'Insufficient wallet balance. Please recharge your wallet.',
                    'current_balance' => (float) $lockedUser->balance,
                ];
            }

            if ($betType === 'main') {
                $multiplier = (strtolower($betSelection) === 'andar')
                    ? (float) ($setting->andar_multiplier ?? self::MAIN_MULTIPLIER)
                    : (float) ($setting->bahar_multiplier ?? self::MAIN_MULTIPLIER);
            } else {
                $multiplier = $rangeMultipliers[$betSelection] ?? 3.50;
            }

            // Deduct balance
            $balBefore = (float) $lockedUser->balance;
            $balAfter = $balBefore - $amount;
            $lockedUser->balance = $balAfter;
            $lockedUser->save();

            // Create Bet
            $bet = AndarBaharBet::create([
                'user_id' => $lockedUser->id,
                'round_id' => $round->round_id,
                'bet_type' => $betType,
                'bet_selection' => strtolower($betSelection),
                'amount' => $amount,
                'win_multiplier' => $multiplier,
                'win_amount' => 0.00,
                'status' => 'pending',
            ]);

            // Create transaction ledger entry
            Transaction::create([
                'user_id' => $lockedUser->id,
                'type' => 'bet_placed',
                'amount' => $amount,
                'balance_before' => $balBefore,
                'balance_after' => $balAfter,
                'status' => 'completed',
                'admin_notes' => "Placed Bet on Andar Bahar Round #{$round->round_id} [{$betSelection}] for ₹{$amount}",
            ]);

            // Update round aggregate stats
            $round->increment('total_bets_count');
            $round->increment('total_bet_amount', $amount);

            return [
                'success' => true,
                'message' => 'Bet placed successfully!',
                'new_balance' => (float) $balAfter,
                'bet_id' => $bet->id,
                'round_id' => $round->round_id,
                'selection' => $betSelection,
                'amount' => $amount,
            ];
        });
    }
}
