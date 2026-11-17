<?php

namespace App\Services;

use App\Models\AdminSetting;
use App\Models\ReferralCommission;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    /**
     * Process referral commission on child user's first approved deposit.
     * Guaranteed idempotent: child_user_id is unique in referral_commissions table.
     */
    public static function processFirstDepositCommission(Transaction $deposit): ?ReferralCommission
    {
        if ($deposit->type !== 'deposit' || $deposit->status !== 'approved') {
            return null;
        }

        // Must run inside transaction
        return DB::transaction(function () use ($deposit) {
            $child = User::where('id', $deposit->user_id)->lockForUpdate()->first();
            if (! $child || ! $child->referred_by) {
                return null;
            }

            // Check if referral commission already paid for this child user
            $alreadyAwarded = ReferralCommission::where('child_user_id', $child->id)->exists();
            if ($alreadyAwarded) {
                return null;
            }

            // Check approved deposit count for child
            $approvedCount = Transaction::where('user_id', $child->id)
                ->where('type', 'deposit')
                ->where('status', 'approved')
                ->count();

            // Only first approved deposit qualifies
            if ($approvedCount > 1) {
                return null;
            }

            $parent = User::where('id', $child->referred_by)->lockForUpdate()->first();
            if (! $parent || $parent->status === 'banned') {
                return null;
            }

            $setting = AdminSetting::current();
            $percent = (float) ($setting->referral_commission_percent ?? 1.00);
            if ($percent <= 0) {
                return null;
            }

            $depositAmount = (float) $deposit->amount;
            $commissionAmount = round(($depositAmount * $percent) / 100, 2);

            if ($commissionAmount <= 0) {
                return null;
            }

            // Credit Parent Balance
            $parentBefore = $parent->balance;
            $parentAfter = bcadd((string) $parentBefore, (string) $commissionAmount, 2);
            $parent->balance = $parentAfter;
            $parent->save();

            // Record Parent Transaction Ledger
            Transaction::create([
                'user_id' => $parent->id,
                'type' => 'admin_adjustment',
                'amount' => $commissionAmount,
                'balance_before' => $parentBefore,
                'balance_after' => $parentAfter,
                'payment_method' => 'REFERRAL_BONUS',
                'status' => 'approved',
                'reviewed_by' => Auth::id() ?: null,
                'admin_notes' => "Referral Bonus ({$percent}%) on 1st deposit of {$child->name} (₹".number_format($depositAmount, 2).')',
            ]);

            // Save Commission Record
            $commission = ReferralCommission::create([
                'parent_user_id' => $parent->id,
                'child_user_id' => $child->id,
                'deposit_transaction_id' => $deposit->id,
                'deposit_amount' => $depositAmount,
                'commission_percent' => $percent,
                'commission_amount' => $commissionAmount,
                'status' => 'credited',
            ]);

            Log::info("Referral Commission Awarded: Parent #{$parent->id} earned ₹{$commissionAmount} ({$percent}%) from Child #{$child->id} First Deposit #{$deposit->id}");

            return $commission;
        });
    }
}
