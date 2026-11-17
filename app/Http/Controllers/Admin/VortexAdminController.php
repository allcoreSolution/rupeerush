<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\VortexGame;
use App\Services\VortexEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VortexAdminController extends Controller
{
    /**
     * Vortex Live Control Hub, Risk Management & Stats Dashboard.
     */
    public function index(Request $request): View
    {
        $setting = AdminSetting::current();

        // 1. Live Active Sessions
        $activeGames = VortexGame::with('user')
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->get();

        $activeCount = $activeGames->count();
        $activeWagered = (float) $activeGames->sum('bet_amount');
        $activeLiability = (float) $activeGames->sum('payout_amount');

        // 2. Performance & Financial Stats
        $totalGamesCount = VortexGame::count();
        $totalWagered = (float) VortexGame::sum('bet_amount');
        $totalPaidOut = (float) VortexGame::whereIn('status', ['cashed_out', 'busted'])->sum('win_amount');
        $netProfit = $totalWagered - $totalPaidOut;
        $houseMargin = $totalWagered > 0 ? round(($netProfit / $totalWagered) * 100, 2) : 0.00;

        $bustedCount = VortexGame::where('status', 'busted')->count();
        $cashedOutCount = VortexGame::where('status', 'cashed_out')->count();

        // 3. Filtered Recent Games Log
        $query = VortexGame::with('user')->orderBy('id', 'desc');

        if ($request->filled('status') && in_array($request->status, ['active', 'cashed_out', 'busted'])) {
            $query->where('status', $request->status);
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

        return view('admin.vortex.index', [
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
            'bustedCount' => $bustedCount,
            'cashedOutCount' => $cashedOutCount,
            'recentGames' => $recentGames,
        ]);
    }

    /**
     * Update Vortex game settings and manipulation parameters.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vortex_min_bet' => ['required', 'numeric', 'min:0.10', 'max:50000'],
            'vortex_max_bet' => ['required', 'numeric', 'gte:vortex_min_bet', 'max:200000'],
            'vortex_house_edge' => ['required', 'numeric', 'min:0.00', 'max:50.00'],
            'vortex_control_mode' => ['required', 'string', 'in:fair,house_trap'],
            'vortex_rigged_rate' => ['required', 'numeric', 'min:0.00', 'max:100.00'],
            'vortex_max_multiplier_cap' => ['required', 'numeric', 'min:0.00', 'max:1000.00'],
        ]);

        $setting = AdminSetting::current();
        $setting->update([
            'vortex_min_bet' => round((float) $validated['vortex_min_bet'], 2),
            'vortex_max_bet' => round((float) $validated['vortex_max_bet'], 2),
            'vortex_house_edge' => round((float) $validated['vortex_house_edge'], 2),
            'vortex_control_mode' => $validated['vortex_control_mode'],
            'vortex_rigged_rate' => round((float) $validated['vortex_rigged_rate'], 2),
            'vortex_max_multiplier_cap' => round((float) $validated['vortex_max_multiplier_cap'], 2),
        ]);

        return redirect()->route('admin.vortex.index')
            ->with('success', 'Vortex Game settings & risk control parameters saved successfully!');
    }

    /**
     * Force bust active player session.
     */
    public function forceBust(int $id): RedirectResponse
    {
        $result = VortexEngine::forceBust($id);

        if ($result['success']) {
            return redirect()->route('admin.vortex.index')->with('success', $result['message']);
        }

        return redirect()->route('admin.vortex.index')->with('error', $result['message']);
    }

    /**
     * Force cash out active player session.
     */
    public function forceCashout(int $id): RedirectResponse
    {
        $result = VortexEngine::forceCashout($id);

        if ($result['success']) {
            return redirect()->route('admin.vortex.index')->with('success', $result['message']);
        }

        return redirect()->route('admin.vortex.index')->with('error', $result['message']);
    }

    /**
     * Vortex Winners Leaderboard & Cashout Logs.
     */
    public function winners(Request $request): View
    {
        $query = VortexGame::with('user')
            ->where('status', 'cashed_out')
            ->where('win_amount', '>', 0);

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

        $topMultiplier = (float) VortexGame::where('status', 'cashed_out')->max('current_multiplier') ?: 1.00;
        $highestPayout = (float) VortexGame::where('status', 'cashed_out')->max('win_amount') ?: 0.00;
        $totalPaidOut = (float) VortexGame::where('status', 'cashed_out')->sum('win_amount');

        $winners = $query->orderBy('win_amount', 'desc')->paginate(25)->withQueryString();

        return view('admin.vortex.winners', [
            'winners' => $winners,
            'topMultiplier' => $topMultiplier,
            'highestPayout' => $highestPayout,
            'totalPaidOut' => $totalPaidOut,
        ]);
    }
}
