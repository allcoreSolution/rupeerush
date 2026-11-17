<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\DragonTigerBet;
use App\Models\DragonTigerRound;
use App\Services\DragonTigerEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DragonTigerAdminController extends Controller
{
    /**
     * Admin Live Dragon Tiger Control & Risk Exposure Panel.
     */
    public function index(Request $request): View
    {
        $roundData = DragonTigerEngine::getCurrentRound();
        /** @var DragonTigerRound $round */
        $round = $roundData['round_model'];

        // Bets placed on active round
        $activeBets = DragonTigerBet::with('user')
            ->where('round_id', $round->round_id)
            ->orderBy('id', 'desc')
            ->get();

        // Calculate real-time house exposure & risk
        $exposure = $this->calculateExposure($round);

        // Recent completed rounds (up to 50)
        $recentRounds = DragonTigerRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        $setting = AdminSetting::current();

        return view('admin.dragon_tiger.index', [
            'roundData' => $roundData,
            'round' => $round,
            'activeBets' => $activeBets,
            'exposure' => $exposure,
            'recentRounds' => $recentRounds,
            'setting' => $setting,
        ]);
    }

    /**
     * Set manual winner for current round.
     */
    public function setManualWinner(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'round_id' => ['required', 'string'],
            'winner' => ['required', 'in:dragon,tiger,tie,suited_tie'],
        ]);

        $round = DragonTigerRound::where('round_id', $validated['round_id'])->firstOrFail();

        $round->is_manual = true;
        $round->manual_winner = $validated['winner'];
        $round->save();

        $setting = AdminSetting::current();
        $setting->update([
            'dragon_tiger_manual_winner' => $validated['winner'],
        ]);

        $targetName = strtoupper(str_replace('_', ' ', $validated['winner']));

        return back()->with('success', "Target locked to [{$targetName}] for Round #{$round->round_id}! The winner will be forced to {$targetName}.");
    }

    /**
     * Clear manual override and restore automatic mode.
     */
    public function clearManualWinner(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'round_id' => ['required', 'string'],
        ]);

        $round = DragonTigerRound::where('round_id', $validated['round_id'])->firstOrFail();

        $round->is_manual = false;
        $round->manual_winner = null;
        $round->save();

        $setting = AdminSetting::current();
        $setting->update([
            'dragon_tiger_manual_winner' => null,
        ]);

        return back()->with('success', "Manual winner override cleared for Round #{$round->round_id}. Returned to [{$setting->dragon_tiger_control_mode}] mode.");
    }

    /**
     * Switch Dragon Tiger Game Control Mode (fair vs least_liability vs manual_override).
     */
    public function setControlMode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mode' => ['required', 'in:fair,least_liability,manual_override'],
        ]);

        $setting = AdminSetting::current();
        $setting->update([
            'dragon_tiger_control_mode' => $validated['mode'],
        ]);

        $names = [
            'fair' => '100% Fair / RNG Deal',
            'least_liability' => 'Smart House Protection (Least Liability / Guaranteed House Edge)',
            'manual_override' => 'Manual Outcome Override',
        ];

        return back()->with('success', 'Dragon Tiger algorithm updated to: '.($names[$validated['mode']] ?? $validated['mode']));
    }

    /**
     * Update Dragon Tiger betting limits and odds settings.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dragon_tiger_min_bet' => ['required', 'numeric', 'min:1'],
            'dragon_tiger_max_bet' => ['required', 'numeric', 'gte:dragon_tiger_min_bet'],
            'dragon_tiger_odds_main' => ['required', 'numeric', 'min:1', 'max:5'],
            'dragon_tiger_odds_tie' => ['required', 'numeric', 'min:2', 'max:20'],
            'dragon_tiger_odds_suited_tie' => ['required', 'numeric', 'min:10', 'max:100'],
            'dragon_tiger_round_duration' => ['required', 'integer', 'min:15', 'max:60'],
        ]);

        $setting = AdminSetting::current();
        $setting->update($validated);

        return back()->with('success', 'Dragon Tiger game rules & odds updated successfully!');
    }

    /**
     * View history of winning players and credited payouts.
     */
    public function winners(Request $request): View
    {
        $query = DragonTigerBet::with(['user', 'round'])
            ->whereIn('status', ['won', 'refunded'])
            ->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('round_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $totalWinners = (clone $query)->count();
        $totalPayout = (float) (clone $query)->sum('payout_amount');

        $winners = $query->paginate(25)->withQueryString();

        return view('admin.dragon_tiger.winners', [
            'winners' => $winners,
            'totalWinners' => $totalWinners,
            'totalPayout' => $totalPayout,
            'search' => $request->search ?? '',
        ]);
    }

    /**
     * Calculate house exposure and liabilities on active round.
     */
    private function calculateExposure(DragonTigerRound $round): array
    {
        $bets = DragonTigerBet::where('round_id', $round->round_id)->get();

        $totalVolume = (float) $bets->sum('bet_amount');
        $dragonBets = $bets->where('bet_type', 'dragon');
        $tigerBets = $bets->where('bet_type', 'tiger');
        $tieBets = $bets->where('bet_type', 'tie');
        $suitedTieBets = $bets->where('bet_type', 'suited_tie');
        $sideBets = $bets->whereNotIn('bet_type', ['dragon', 'tiger', 'tie', 'suited_tie']);

        $dragonVol = (float) $dragonBets->sum('bet_amount');
        $tigerVol = (float) $tigerBets->sum('bet_amount');
        $tieVol = (float) $tieBets->sum('bet_amount');
        $suitedTieVol = (float) $suitedTieBets->sum('bet_amount');
        $sideVol = (float) $sideBets->sum('bet_amount');

        // Liability if Dragon wins
        $dragonPayout = (float) $dragonBets->sum(fn ($b) => $b->bet_amount * $b->odds);
        // Liability if Tiger wins
        $tigerPayout = (float) $tigerBets->sum(fn ($b) => $b->bet_amount * $b->odds);
        // Liability if Tie wins (pays tie bets, refunds 50% dragon and tiger bets)
        $tiePayout = (float) $tieBets->sum(fn ($b) => $b->bet_amount * $b->odds)
            + (float) $dragonBets->sum(fn ($b) => $b->bet_amount * 0.5)
            + (float) $tigerBets->sum(fn ($b) => $b->bet_amount * 0.5);

        return [
            'total_volume' => $totalVolume,
            'dragon_volume' => $dragonVol,
            'tiger_volume' => $tigerVol,
            'tie_volume' => $tieVol,
            'suited_tie_volume' => $suitedTieVol,
            'side_volume' => $sideVol,
            'dragon_payout_liability' => $dragonPayout,
            'tiger_payout_liability' => $tigerPayout,
            'tie_payout_liability' => $tiePayout,
            'dragon_profit' => round($totalVolume - $dragonPayout, 2),
            'tiger_profit' => round($totalVolume - $tigerPayout, 2),
            'tie_profit' => round($totalVolume - $tiePayout, 2),
            'recommended_winner' => ($dragonPayout < $tigerPayout && $dragonPayout < $tiePayout) ? 'dragon' : (($tigerPayout < $dragonPayout && $tigerPayout < $tiePayout) ? 'tiger' : 'tie'),
        ];
    }
}
