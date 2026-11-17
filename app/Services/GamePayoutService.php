<?php

namespace App\Services;

use App\Models\AdminSetting;

class GamePayoutService
{
    /**
     * Calculate payout details when a bet is won.
     *
     * Business Logic:
     * - Bet Amount: B
     * - Multiplier: M
     * - Gross Payout: B * M
     * - Winning Profit: max(0, Gross Payout - B)
     * - Admin Commission (10%): Winning Profit * 10%
     * - Player Net Profit (90%): Winning Profit * 90%
     * - Player Total Wallet Payout: B + Player Net Profit = Gross Payout - Admin Commission
     *
     * @return array{
     *     bet_amount: float,
     *     multiplier: float,
     *     gross_payout: float,
     *     profit: float,
     *     commission_percent: float,
     *     commission_amount: float,
     *     net_profit: float,
     *     player_payout: float
     * }
     */
    public static function calculate(float|string $betAmount, float|string $multiplier, ?float $customCommissionPercent = null): array
    {
        $b = (float) $betAmount;
        $m = (float) $multiplier;

        if ($customCommissionPercent !== null) {
            $commissionPercent = max(0.0, min(100.0, $customCommissionPercent));
        } else {
            $setting = AdminSetting::current();
            $commissionPercent = (float) ($setting->win_commission_percent ?? 10.00);
        }

        $grossPayout = round($b * $m, 2);
        $profit = max(0.0, round($grossPayout - $b, 2));

        if ($profit > 0 && $commissionPercent > 0) {
            $commissionAmount = round($profit * ($commissionPercent / 100.0), 2);
            $netProfit = round($profit - $commissionAmount, 2);
            $playerPayout = round($b + $netProfit, 2);
        } else {
            $commissionAmount = 0.00;
            $netProfit = $profit;
            $playerPayout = $grossPayout;
        }

        return [
            'bet_amount' => $b,
            'multiplier' => $m,
            'gross_payout' => $grossPayout,
            'profit' => $profit,
            'commission_percent' => $commissionPercent,
            'commission_amount' => $commissionAmount,
            'net_profit' => $netProfit,
            'player_payout' => $playerPayout,
        ];
    }
}
