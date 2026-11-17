<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\AndarBaharBet;
use App\Models\AndarBaharRound;
use App\Services\AndarBaharEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AndarBaharAdminController extends Controller
{
    /**
     * Admin Live Andar Bahar Control & Risk Exposure Panel.
     */
    public function index(Request $request): View
    {
        $roundData = AndarBaharEngine::getCurrentRound();
        $round = $roundData['round_model'];

        // Bets placed on active round
        $activeBets = AndarBaharBet::with('user')
            ->where('round_id', $round->round_id)
            ->orderBy('id', 'desc')
            ->get();

        // Calculate real-time house exposure & risk
        $exposure = AndarBaharEngine::calculateExposure($round);

        // Recent completed rounds (up to 50)
        $recentRounds = AndarBaharRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        $setting = AdminSetting::current();

        return view('admin.andar_bahar.index', [
            'roundData' => $roundData,
            'round' => $round,
            'activeBets' => $activeBets,
            'exposure' => $exposure,
            'recentRounds' => $recentRounds,
            'setting' => $setting,
        ]);
    }

    /**
     * Set manual winner for current round (Force ANDAR or Force BAHAR).
     */
    public function setManualWinner(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'round_id' => ['required', 'string'],
            'winner' => ['required', 'in:andar,bahar'],
        ]);

        $round = AndarBaharRound::where('round_id', $validated['round_id'])->firstOrFail();

        $round->is_manual = true;
        $round->manual_winning_side = $validated['winner'];

        // Re-simulate game deal so matching card lands on the chosen side
        $sim = AndarBaharEngine::simulateGame($validated['winner'], $round->joker_card);
        $round->dealt_cards = $sim['dealt_cards'];
        $round->winning_side = $sim['winning_side'];
        $round->winning_card = $sim['winning_card'];
        $round->total_cards_dealt = $sim['total_cards_dealt'];
        $round->save();

        $targetName = strtoupper($validated['winner']);

        return back()->with('success', "Target locked to [{$targetName}] for Round #{$round->round_id}! The matching card will land on {$targetName}.");
    }

    /**
     * Clear manual override and restore Auto Random Mode.
     */
    public function clearManualWinner(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'round_id' => ['required', 'string'],
        ]);

        $round = AndarBaharRound::where('round_id', $validated['round_id'])->firstOrFail();

        $round->is_manual = false;
        $round->manual_winning_side = null;

        // Re-simulate probably fair random deal
        $sim = AndarBaharEngine::simulateGame(null, $round->joker_card);
        $round->dealt_cards = $sim['dealt_cards'];
        $round->winning_side = $sim['winning_side'];
        $round->winning_card = $sim['winning_card'];
        $round->total_cards_dealt = $sim['total_cards_dealt'];
        $round->save();

        return back()->with('success', "Round #{$round->round_id} reset to Auto Random Mode.");
    }

    /**
     * Update Andar Bahar multipliers & betting limits.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'andar_multiplier' => ['required', 'numeric', 'min:1.01', 'max:10.00'],
            'bahar_multiplier' => ['required', 'numeric', 'min:1.01', 'max:10.00'],
            'andar_bahar_min_bet' => ['required', 'numeric', 'min:1', 'max:50000'],
            'andar_bahar_max_bet' => ['required', 'numeric', 'min:10', 'max:500000'],
            'andar_bahar_range_1_5' => ['required', 'numeric', 'min:1.1', 'max:50'],
            'andar_bahar_range_6_10' => ['required', 'numeric', 'min:1.1', 'max:50'],
            'andar_bahar_range_11_15' => ['required', 'numeric', 'min:1.1', 'max:50'],
            'andar_bahar_range_16_25' => ['required', 'numeric', 'min:1.1', 'max:50'],
            'andar_bahar_range_26_plus' => ['required', 'numeric', 'min:1.1', 'max:50'],
        ]);

        $setting = AdminSetting::current();
        $setting->update($validated);

        return back()->with('success', 'Andar Bahar multipliers and limits successfully updated!');
    }

    /**
     * View recent Andar Bahar winners.
     */
    public function winners(Request $request): View
    {
        $query = AndarBaharBet::with(['user', 'round'])
            ->where('status', 'won');

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
        $totalPayout = (clone $query)->sum('win_amount');

        $wonBets = $query->orderBy('id', 'desc')->paginate(25)->withQueryString();

        return view('admin.andar_bahar.winners', [
            'wonBets' => $wonBets,
            'totalWinners' => $totalWinners,
            'totalPayout' => (float) $totalPayout,
            'search' => $request->search ?? '',
        ]);
    }
}
