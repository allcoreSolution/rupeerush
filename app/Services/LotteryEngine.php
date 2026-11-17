<?php

namespace App\Services;

use App\Models\AdminSetting;
use App\Models\Bet;
use App\Models\GameSlot;
use App\Models\Result;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LotteryEngine
{
    public const SLOT_KEYS = ['XA', 'XB', 'XC', 'XD', 'XE', 'XF', 'XG', 'XH', 'XI', 'XJ'];

    /**
     * Get or calculate slot timing info for a given timestamp.
     */
    public static function getSlotScheduleStatus(?Carbon $now = null): array
    {
        $now = $now ? $now->copy() : Carbon::now('Asia/Kolkata');
        $todayDate = $now->format('Y-m-d');
        $setting = AdminSetting::current();
        $lockMinutes = $setting->betting_lock_minutes ?? 2;

        $slots = GameSlot::where('is_active', true)->orderBy('display_order')->get();

        if ($slots->isEmpty()) {
            return [
                'current' => null,
                'upcoming' => null,
                'prev_gen_time' => null,
                'slots_today' => [],
            ];
        }

        $slotsToday = [];
        $currentPublishedSlot = null;
        $nextUpcomingSlot = null;
        $prevGenTime = null;

        foreach ($slots as $idx => $slot) {
            $slotDateTime = Carbon::createFromFormat('Y-m-d h:i A', "$todayDate {$slot->slot_time}", 'Asia/Kolkata');
            $genDateTime = $slotDateTime->copy()->subMinutes($lockMinutes); // T - 2 min
            $isGenerated = $now->greaterThanOrEqualTo($genDateTime);

            $slotInfo = [
                'slot_time' => $slot->slot_time,
                'slot_datetime' => $slotDateTime,
                'gen_datetime' => $genDateTime,
                'is_generated' => $isGenerated,
                'is_betting_open' => $now->lessThan($genDateTime),
            ];

            $slotsToday[] = $slotInfo;
        }

        // Find last generated slot today
        $generated = array_filter($slotsToday, fn ($s) => $s['is_generated']);
        $upcoming = array_filter($slotsToday, fn ($s) => ! $s['is_generated']);

        if (! empty($generated)) {
            $currentPublishedSlot = end($generated);
        } else {
            // Before morning first slot generation: show yesterday's last slot
            $lastSlot = $slots->last();
            $yesterday = $now->copy()->subDay()->format('Y-m-d');
            $slotDateTime = Carbon::createFromFormat('Y-m-d h:i A', "$yesterday {$lastSlot->slot_time}", 'Asia/Kolkata');
            $currentPublishedSlot = [
                'slot_time' => $lastSlot->slot_time,
                'slot_datetime' => $slotDateTime,
                'gen_datetime' => $slotDateTime->copy()->subMinutes($lockMinutes),
                'is_generated' => true,
                'is_betting_open' => false,
            ];
        }

        if (! empty($upcoming)) {
            $nextUpcomingSlot = reset($upcoming);
            $idx = array_search($nextUpcomingSlot, $slotsToday);
            $prevGenTime = $idx > 0 ? $slotsToday[$idx - 1]['gen_datetime'] : $nextUpcomingSlot['gen_datetime']->copy()->subMinutes(20);
        } else {
            // After all slots today: target tomorrow morning first slot
            $firstSlot = $slots->first();
            $tomorrow = $now->copy()->addDay()->format('Y-m-d');
            $tomSlotDateTime = Carbon::createFromFormat('Y-m-d h:i A', "$tomorrow {$firstSlot->slot_time}", 'Asia/Kolkata');
            $nextUpcomingSlot = [
                'slot_time' => $firstSlot->slot_time,
                'slot_datetime' => $tomSlotDateTime,
                'gen_datetime' => $tomSlotDateTime->copy()->subMinutes($lockMinutes),
                'is_generated' => false,
                'is_betting_open' => true,
            ];
            $prevGenTime = end($slotsToday)['gen_datetime'];
        }

        return [
            'now' => $now,
            'currentPublishedSlot' => $currentPublishedSlot,
            'nextUpcomingSlot' => $nextUpcomingSlot,
            'prevGenTime' => $prevGenTime,
            'allSlotsToday' => $slotsToday,
        ];
    }

    /**
     * Generate deterministic or random numbers for 10 slots.
     */
    public static function generate10Numbers(string $dateStr, string $slotTime): array
    {
        $seedStr = "{$dateStr}_{$slotTime}";
        $hash = crc32($seedStr);

        $numbers = [];
        foreach (self::SLOT_KEYS as $idx => $key) {
            $val = abs(sin($hash + $idx * 97) * 100);
            $num = (int) floor($val) % 100;
            $numbers[$key] = str_pad((string) $num, 2, '0', STR_PAD_LEFT);
        }

        return $numbers;
    }

    /**
     * Generate numbers optimizing for house profit (75-80% minimum liability) while allowing occasional wins (20-25%).
     */
    public static function generateOptimized10Numbers(string $dateStr, string $slotTime, bool $forceHouseProfit = true): array
    {
        $bets = Bet::where('result_date', $dateStr)
            ->where('slot_time', $slotTime)
            ->get();

        if ($bets->isEmpty()) {
            return self::generate10Numbers($dateStr, $slotTime);
        }

        $betsBySlot = $bets->groupBy('slot_key');
        $numbers = [];

        foreach (self::SLOT_KEYS as $key) {
            $slotBets = $betsBySlot->get($key, collect());

            if ($slotBets->isEmpty()) {
                // If no bets on this slot, use natural generation
                $defaultNums = self::generate10Numbers($dateStr, $slotTime);
                $numbers[$key] = $defaultNums[$key] ?? '00';

                continue;
            }

            // Calculate liability for each candidate number 00-99
            $liabilities = [];
            for ($i = 0; $i < 100; $i++) {
                $numStr = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
                $liabilities[$numStr] = 0.00;
            }

            foreach ($slotBets as $b) {
                $chosen = str_pad((string) $b->chosen_number, 2, '0', STR_PAD_LEFT);
                if (isset($liabilities[$chosen])) {
                    $liabilities[$chosen] += ((float) $b->amount * (float) $b->win_multiplier);
                }
            }

            if ($forceHouseProfit) {
                // 75-80% of rounds: Find numbers with zero or absolute minimum liability
                $zeroLiability = array_keys(array_filter($liabilities, fn ($l) => $l == 0.0));
                if (! empty($zeroLiability)) {
                    $numbers[$key] = $zeroLiability[array_rand($zeroLiability)];
                } else {
                    asort($liabilities);
                    $numbers[$key] = (string) array_key_first($liabilities);
                }
            } else {
                // 20-25% of rounds: Allow a small win or natural number
                $positiveLiabilities = array_filter($liabilities, fn ($l) => $l > 0.0);
                if (! empty($positiveLiabilities)) {
                    asort($positiveLiabilities);
                    // Pick among the smallest winning liabilities so players win small without hurting house pool
                    $smallestWinning = array_slice($positiveLiabilities, 0, 3, true);
                    $keys = array_keys($smallestWinning);
                    $numbers[$key] = (string) $keys[array_rand($keys)];
                } else {
                    $numbers[$key] = str_pad((string) mt_rand(0, 99), 2, '0', STR_PAD_LEFT);
                }
            }
        }

        return $numbers;
    }

    /**
     * Generates or fetches result for a slot, saves to DB, and processes payouts.
     */
    public static function publishResultAndProcessPayouts(string $slotTime, string $resultDate, ?array $overrideNumbers = null, ?int $adminId = null): Result
    {
        return DB::transaction(function () use ($slotTime, $resultDate, $overrideNumbers, $adminId) {
            $setting = AdminSetting::current();
            $lockMinutes = $setting->betting_lock_minutes ?? 2;
            $slotDateTime = Carbon::createFromFormat('Y-m-d h:i A', "$resultDate $slotTime", 'Asia/Kolkata');
            $genDateTime = $slotDateTime->copy()->subMinutes($lockMinutes);

            $result = Result::where('result_date', $resultDate)
                ->where('slot_time', $slotTime)
                ->first();

            $numbers = $overrideNumbers;

            if (! $numbers) {
                if ($result && $result->is_manual) {
                    $numbers = $result->getNumbersArray();
                } else {
                    $shouldHouseWin = $setting->shouldHouseWin();
                    $numbers = self::generateOptimized10Numbers($resultDate, $slotTime, $shouldHouseWin);
                }
            }

            if (! $result) {
                $result = new Result;
                $result->result_date = $resultDate;
                $result->slot_time = $slotTime;
                $result->slot_datetime = $slotDateTime;
                $result->gen_datetime = $genDateTime;
            }

            foreach (self::SLOT_KEYS as $key) {
                $result->{$key} = $numbers[$key] ?? '00';
            }

            $result->status = 'published';
            if ($overrideNumbers) {
                $result->is_manual = true;
                $result->created_by = $adminId;
            }
            $result->save();

            // Evaluate all pending bets for this slot
            self::evaluateBetsForSlot($result);

            return $result;
        });
    }

    /**
     * Evaluates winning and losing bets and credits winners atomically.
     * Also supports retroactive re-evaluation if admin edits an existing result.
     */
    public static function evaluateBetsForSlot(Result $result): void
    {
        $bets = Bet::where('result_date', $result->result_date->format('Y-m-d'))
            ->where('slot_time', $result->slot_time)
            ->lockForUpdate()
            ->get();

        $now = Carbon::now('Asia/Kolkata');

        foreach ($bets as $bet) {
            $winningNumber = $result->{$bet->slot_key};
            $isWinner = ($winningNumber !== null && (string) $bet->chosen_number === (string) $winningNumber);

            if ($isWinner) {
                // Should be a winner
                if ($bet->status !== 'won') {
                    // Newly winning bet (was pending or lost)
                    $payoutDetails = GamePayoutService::calculate($bet->amount, $bet->win_multiplier);
                    $winAmount = (string) $payoutDetails['player_payout'];
                    $commissionAmount = (string) $payoutDetails['commission_amount'];

                    $bet->status = 'won';
                    $bet->commission_amount = $commissionAmount;
                    $bet->win_amount = $winAmount;
                    $bet->evaluated_at = $now;
                    $bet->save();

                    // Credit user wallet atomically
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
                            'admin_notes' => "Won Bet #{$bet->id} on {$bet->slot_key} [{$bet->chosen_number}] ({$result->slot_time}){$feeNote}",
                        ]);
                    }
                }
            } else {
                // Not a winning number
                if ($bet->status === 'won') {
                    // Payout reversal: bet was previously marked won, but admin edited the result to a different number
                    $previousWinAmount = $bet->win_amount;
                    $bet->status = 'lost';
                    $bet->win_amount = 0.00;
                    $bet->evaluated_at = $now;
                    $bet->save();

                    $user = User::where('id', $bet->user_id)->lockForUpdate()->first();
                    if ($user) {
                        $balanceBefore = $user->balance;
                        $balanceAfter = bcsub((string) $balanceBefore, (string) $previousWinAmount, 2);
                        $user->balance = $balanceAfter;
                        $user->save();

                        Transaction::create([
                            'user_id' => $user->id,
                            'type' => 'admin_adjustment',
                            'amount' => $previousWinAmount,
                            'balance_before' => $balanceBefore,
                            'balance_after' => $balanceAfter,
                            'status' => 'completed',
                            'admin_notes' => "Payout reversed for Bet #{$bet->id} due to result correction on {$result->slot_time}",
                        ]);
                    }
                } elseif ($bet->status === 'pending') {
                    // Normal loss
                    $bet->status = 'lost';
                    $bet->win_amount = 0.00;
                    $bet->evaluated_at = $now;
                    $bet->save();
                }
            }
        }
    }
}
