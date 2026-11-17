<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\MinesGame;
use App\Services\MinesEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MinesAdminController extends Controller
{
    /**
     * Mines Live Control Hub, Risk Management & Stats Dashboard.
     */
    public function index(Request $request): View
    {
        $setting = AdminSetting::current();

        // 1. Live Active Games
        $activeGames = MinesGame::with('user')
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->get();

        $activeCount = $activeGames->count();
        $activeWagered = (float) $activeGames->sum('bet_amount');
        $activeLiability = (float) $activeGames->sum(function ($game) {
            return round((float) $game->bet_amount * (float) $game->current_multiplier, 2) + ((float) $game->jackpot_win_amount);
        });

        // 2. Performance & Financial Stats
        $totalGamesCount = MinesGame::count();
        $totalWagered = (float) MinesGame::sum('bet_amount');
        $totalPaidOut = (float) MinesGame::where('status', 'cashed_out')->sum('win_amount');
        $netProfit = $totalWagered - $totalPaidOut;
        $houseMargin = $totalWagered > 0 ? round(($netProfit / $totalWagered) * 100, 2) : 0.00;

        $explodedCount = MinesGame::where('status', 'exploded')->count();
        $cashedOutCount = MinesGame::where('status', 'cashed_out')->count();
        $jackpotHitsCount = MinesGame::where('is_jackpot_win', true)->count();

        // 3. Filtered Recent Games Log
        $query = MinesGame::with('user')->orderBy('id', 'desc');

        if ($request->filled('status') && in_array($request->status, ['active', 'cashed_out', 'exploded'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jackpot') && $request->jackpot == '1') {
            $query->where('is_jackpot_win', true);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('game_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $recentGames = $query->paginate(50)->withQueryString();

        return view('admin.mines.index', [
            'setting' => $setting,
            'activeGames' => $activeGames,
            'activeCount' => $activeCount,
            'activeWagered' => $activeWagered,
            'activeLiability' => $activeLiability,
            'totalGamesCount' => $totalGamesCount,
            'totalWagered' => $totalWagered,
            'totalPaidOut' => $totalPaidOut,
            'netProfit' => $netProfit,
            'houseMargin' => $houseMargin,
            'explodedCount' => $explodedCount,
            'cashedOutCount' => $cashedOutCount,
            'jackpotHitsCount' => $jackpotHitsCount,
            'recentGames' => $recentGames,
        ]);
    }

    /**
     * Update Mines settings and manipulation control parameters.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mines_min_bet' => ['required', 'numeric', 'min:0.10', 'max:50000'],
            'mines_max_bet' => ['required', 'numeric', 'gte:mines_min_bet', 'max:200000'],
            'mines_house_edge' => ['required', 'numeric', 'min:0.00', 'max:50.00'],
            'mines_jackpot_min_bet' => ['required', 'numeric', 'min:1.00'],
            'mines_jackpot_pool' => ['required', 'numeric', 'min:0.00'],
            'mines_control_mode' => ['required', 'string', 'in:fair,house_trap,step_limit'],
            'mines_rigged_rate' => ['required', 'numeric', 'min:0.00', 'max:100.00'],
            'mines_force_blast_step' => ['required', 'integer', 'min:0', 'max:24'],
            'mines_max_win_multiplier' => ['required', 'numeric', 'min:0.00', 'max:1000.00'],
        ]);

        $setting = AdminSetting::current();
        $setting->update([
            'mines_min_bet' => round((float) $validated['mines_min_bet'], 2),
            'mines_max_bet' => round((float) $validated['mines_max_bet'], 2),
            'mines_house_edge' => round((float) $validated['mines_house_edge'], 2),
            'mines_jackpot_min_bet' => round((float) $validated['mines_jackpot_min_bet'], 2),
            'mines_jackpot_pool' => round((float) $validated['mines_jackpot_pool'], 2),
            'mines_control_mode' => $validated['mines_control_mode'],
            'mines_rigged_rate' => round((float) $validated['mines_rigged_rate'], 2),
            'mines_force_blast_step' => (int) $validated['mines_force_blast_step'],
            'mines_max_win_multiplier' => round((float) $validated['mines_max_win_multiplier'], 2),
        ]);

        return redirect()->route('admin.mines.index')
            ->with('success', 'Mines Game settings & control parameters saved successfully!');
    }

    /**
     * Force bust (explode) an active player session.
     */
    public function forceBust(int $id): RedirectResponse
    {
        $result = MinesEngine::forceBust($id);

        if ($result['success']) {
            return redirect()->route('admin.mines.index')->with('success', $result['message']);
        }

        return redirect()->route('admin.mines.index')->with('error', $result['message']);
    }

    /**
     * Force cash out an active player session.
     */
    public function forceCashout(int $id): RedirectResponse
    {
        $result = MinesEngine::forceCashout($id);

        if ($result['success']) {
            return redirect()->route('admin.mines.index')->with('success', $result['message']);
        }

        return redirect()->route('admin.mines.index')->with('error', $result['message']);
    }

    /**
     * Mines Winners Leaderboard & Cashout Logs.
     */
    public function winners(Request $request): View
    {
        $query = MinesGame::with('user')
            ->where('status', 'cashed_out')
            ->where('win_amount', '>', 0);

        if ($request->filled('jackpot') && $request->jackpot == '1') {
            $query->where('is_jackpot_win', true);
        }

        $topMultiplier = (float) MinesGame::where('status', 'cashed_out')->max('current_multiplier') ?: 1.00;
        $highestPayout = (float) MinesGame::where('status', 'cashed_out')->max('win_amount') ?: 0.00;
        $totalPaidOut = (float) MinesGame::where('status', 'cashed_out')->sum('win_amount');
        $jackpotWinnersCount = MinesGame::where('is_jackpot_win', true)->count();

        $winners = $query->orderBy('win_amount', 'desc')->paginate(25)->withQueryString();

        return view('admin.mines.winners', [
            'winners' => $winners,
            'topMultiplier' => $topMultiplier,
            'highestPayout' => $highestPayout,
            'totalPaidOut' => $totalPaidOut,
            'jackpotWinnersCount' => $jackpotWinnersCount,
        ]);
    }
}
