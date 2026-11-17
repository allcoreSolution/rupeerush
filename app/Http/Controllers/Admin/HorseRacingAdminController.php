<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\HorseRaceBet;
use App\Models\HorseRaceRound;
use App\Services\HorseRacingEngine;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HorseRacingAdminController extends Controller
{
    /**
     * Admin Live Horse Racing Derby Control & Risk Exposure Panel.
     */
    public function index(Request $request): View
    {
        $roundData = HorseRacingEngine::getCurrentRound();
        /** @var HorseRaceRound $round */
        $round = $roundData['round_model'];

        // Bets placed on active round
        $activeBets = HorseRaceBet::with('user')
            ->where('round_id', $round->round_id)
            ->orderBy('id', 'desc')
            ->get();

        $horses = $round->horses_data ?: HorseRacingEngine::getHorsesRoster();

        // Calculate real-time house liability per runner (1 to 6)
        $totalPool = (float) $activeBets->sum('bet_amount');
        $runnerLiabilities = [];

        foreach ($horses as $horse) {
            $hId = (int) $horse['id'];
            $liability = 0.00;

            foreach ($activeBets as $b) {
                // Evaluate guaranteed payout if this horse crosses 1st
                if (HorseRacingEngine::evaluateBetOutcome($b->bet_type, (int) $b->horse_id, $hId, 0, 0)) {
                    $liability += ((float) $b->bet_amount * (float) $b->odds);
                }
            }

            $profit = round($totalPool - $liability, 2);

            $runnerLiabilities[$hId] = [
                'id' => $hId,
                'name' => $horse['name'],
                'gate' => $horse['gate'] ?? $hId,
                'jockey' => $horse['jockey'] ?? '',
                'silk_color' => $horse['silk_color'] ?? '#EF4444',
                'win_odds' => (float) ($horse['win_odds'] ?? 2.50),
                'place_odds' => (float) ($horse['place_odds'] ?? 1.60),
                'show_odds' => (float) ($horse['show_odds'] ?? 1.25),
                'bets_count' => $activeBets->where('horse_id', $hId)->count(),
                'bets_amount' => (float) $activeBets->where('horse_id', $hId)->sum('bet_amount'),
                'liability' => round($liability, 2),
                'projected_profit' => $profit,
            ];
        }

        // Recent completed rounds (up to 50) with optional round search or winner filter
        $recentQuery = HorseRaceRound::where('status', 'completed')
            ->orderBy('id', 'desc');

        if ($request->filled('history_search')) {
            $hSearch = trim((string) $request->history_search);
            $recentQuery->where('round_id', 'like', "%{$hSearch}%");
        }

        if ($request->filled('history_winner') && is_numeric($request->history_winner)) {
            $recentQuery->where('winner_horse_id', (int) $request->history_winner);
        }

        $recentRounds = $recentQuery->limit(50)->get();

        $setting = AdminSetting::current();

        return view('admin.horse_racing.index', [
            'roundData' => $roundData,
            'round' => $round,
            'horses' => $horses,
            'activeBets' => $activeBets,
            'runnerLiabilities' => $runnerLiabilities,
            'totalPool' => $totalPool,
            'recentRounds' => $recentRounds,
            'setting' => $setting,
            'historySearch' => $request->history_search ?? '',
            'historyWinner' => $request->history_winner ?? '',
        ]);
    }

    /**
     * Set 1-click manual winning runner for active round (1 to 6).
     */
    public function setManualWinner(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'round_id' => ['nullable', 'string'],
            'manual_winner_id' => ['required', 'integer', 'min:1', 'max:6'],
        ]);

        $winnerId = (int) $validated['manual_winner_id'];

        // 1. Get currently active round from engine
        $roundData = HorseRacingEngine::getCurrentRound();
        /** @var HorseRaceRound $currentRound */
        $currentRound = $roundData['round_model'];

        $targetRound = null;
        if (! empty($validated['round_id'])) {
            $submittedRound = HorseRaceRound::where('round_id', $validated['round_id'])->first();
            if ($submittedRound && in_array($submittedRound->status, ['betting', 'racing'], true)) {
                $targetRound = $submittedRound;
            }
        }

        if (! $targetRound) {
            $targetRound = $currentRound;
        }

        // Lock manual winner on target round
        $targetRound->is_manual = true;
        $targetRound->manual_winner_id = $winnerId;
        $targetRound->save();

        if ($currentRound->id !== $targetRound->id && in_array($currentRound->status, ['betting', 'racing'], true)) {
            $currentRound->is_manual = true;
            $currentRound->manual_winner_id = $winnerId;
            $currentRound->save();
        }

        // 2. Also update AdminSetting with control mode = 'manual' for engine fallback
        $setting = AdminSetting::current();
        $setting->update([
            'horse_racing_manual_winner' => $winnerId,
            'horse_racing_control_mode' => 'manual',
        ]);

        $horses = $targetRound->horses_data ?: HorseRacingEngine::getHorsesRoster();
        $selectedHorse = collect($horses)->firstWhere('id', $winnerId);
        $horseName = $selectedHorse['name'] ?? "Runner #{$winnerId}";

        return back()->with('success', "Target locked to #{$winnerId} ({$horseName}) for Round #{$targetRound->round_id}! It will cross the finish line 1st.");
    }

    /**
     * Clear manual override and restore natural control mode.
     */
    public function clearManualWinner(Request $request): RedirectResponse
    {
        $roundData = HorseRacingEngine::getCurrentRound();
        /** @var HorseRaceRound $currentRound */
        $currentRound = $roundData['round_model'];
        $currentRound->is_manual = false;
        $currentRound->manual_winner_id = null;
        $currentRound->save();

        if (! empty($request->input('round_id'))) {
            HorseRaceRound::where('round_id', $request->input('round_id'))->update([
                'is_manual' => false,
                'manual_winner_id' => null,
            ]);
        }

        $setting = AdminSetting::current();
        $setting->update([
            'horse_racing_manual_winner' => null,
            'horse_racing_control_mode' => 'fair',
        ]);

        return back()->with('success', 'Manual winner override cleared. Returned to Fair mode.');
    }

    /**
     * Update global Horse Racing admin settings (Min/Max bets, Control Mode, Duration).
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'horse_racing_min_bet' => ['required', 'numeric', 'min:1'],
            'horse_racing_max_bet' => ['required', 'numeric', 'gte:horse_racing_min_bet'],
            'horse_racing_round_duration' => ['required', 'integer', 'min:30', 'max:300'],
            'horse_racing_control_mode' => ['required', 'in:fair,admin_profit,manual'],
        ]);

        $setting = AdminSetting::current();
        $setting->update($validated);

        return back()->with('success', 'Horse Racing game parameters and control mode successfully updated.');
    }

    /**
     * View history of winning players and credited payouts with Day and Date filters.
     */
    public function winners(Request $request): View
    {
        $query = HorseRaceBet::with(['user', 'round'])
            ->where('status', 'won')
            ->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('round_id', 'like', "%{$search}%")
                    ->orWhere('horse_name', 'like', "%{$search}%")
                    ->orWhere('bet_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('id', $search);
                    });
            });
        }

        // Runner / Horse filter (1 to 6)
        if ($request->filled('horse_id') && is_numeric($request->horse_id)) {
            $query->where('horse_id', (int) $request->horse_id);
        }

        // Bet type filter (win, place, show)
        if ($request->filled('bet_type')) {
            $query->where('bet_type', $request->bet_type);
        }

        // Quick date filter presets (all, today, yesterday, last_7_days, this_month)
        $quickFilter = (string) $request->query('quick_filter', '');
        if ($quickFilter === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($quickFilter === 'yesterday') {
            $query->whereDate('created_at', Carbon::yesterday());
        } elseif ($quickFilter === 'last_7_days') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7)->startOfDay());
        } elseif ($quickFilter === 'this_month') {
            $query->where('created_at', '>=', Carbon::now()->startOfMonth());
        }

        // Specific single day filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Aggregated statistics for the filtered query
        $totalWinners = (clone $query)->count();
        $totalPayout = (float) (clone $query)->sum('payout_amount');
        $totalWagered = (float) (clone $query)->sum('bet_amount');
        $totalNetProfit = (float) (clone $query)->sum('net_profit');

        // Today specific stats
        $todayWinners = HorseRaceBet::where('status', 'won')->whereDate('created_at', Carbon::today())->count();
        $todayPayout = (float) HorseRaceBet::where('status', 'won')->whereDate('created_at', Carbon::today())->sum('payout_amount');

        // All-time totals for reference
        $allTimeWinners = HorseRaceBet::where('status', 'won')->count();
        $allTimePayout = (float) HorseRaceBet::where('status', 'won')->sum('payout_amount');

        $perPage = in_array((int) $request->input('per_page'), [25, 50, 100], true) ? (int) $request->input('per_page') : 25;
        $winners = $query->paginate($perPage)->withQueryString();

        return view('admin.horse_racing.winners', [
            'winners' => $winners,
            'totalWinners' => $totalWinners,
            'totalPayout' => $totalPayout,
            'totalWagered' => $totalWagered,
            'totalNetProfit' => $totalNetProfit,
            'todayWinners' => $todayWinners,
            'todayPayout' => $todayPayout,
            'allTimeWinners' => $allTimeWinners,
            'allTimePayout' => $allTimePayout,
            'search' => $request->search ?? '',
            'date' => $request->date ?? '',
            'date_from' => $request->date_from ?? '',
            'date_to' => $request->date_to ?? '',
            'quick_filter' => $quickFilter,
            'quickFilter' => $quickFilter,
            'horse_id' => $request->horse_id ?? '',
            'bet_type' => $request->bet_type ?? '',
            'per_page' => $perPage,
        ]);
    }
}
