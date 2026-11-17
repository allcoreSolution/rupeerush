<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\RouletteBet;
use App\Models\RouletteRound;
use App\Services\RouletteEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RouletteAdminController extends Controller
{
    /**
     * Admin Live European Roulette Control & Risk Exposure Panel.
     */
    public function index(Request $request): View
    {
        $roundData = RouletteEngine::getCurrentRound();
        /** @var RouletteRound $round */
        $round = $roundData['round_model'];

        $setting = AdminSetting::current();
        $oddsTable = RouletteEngine::getBetOdds($setting);

        // Bets placed on active round
        $activeBets = RouletteBet::with('user')
            ->where('round_id', $round->round_id)
            ->orderBy('id', 'desc')
            ->get();

        // Calculate real-time house liability per outcome (0 to 36)
        $numberLiabilities = [];
        for ($i = 0; $i <= 36; $i++) {
            $liability = 0.00;
            foreach ($activeBets as $b) {
                if (RouletteEngine::evaluateBetOutcome($b->bet_type, $b->bet_value, $i)) {
                    $multiplier = (float) ($b->odds ?: ($oddsTable[$b->bet_type] ?? (RouletteEngine::BET_ODDS[$b->bet_type] ?? 1.0)));
                    $liability += ((float) $b->bet_amount * $multiplier);
                }
            }
            $details = RouletteEngine::getNumberDetails($i);
            $numberLiabilities[$i] = [
                'number' => $i,
                'color' => $details['color'],
                'liability' => $liability,
            ];
        }

        // Recent completed rounds (up to 50)
        $recentRounds = RouletteRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        $totalPool = (float) $activeBets->sum('bet_amount');

        return view('admin.roulette.index', [
            'roundData' => $roundData,
            'round' => $round,
            'activeBets' => $activeBets,
            'numberLiabilities' => $numberLiabilities,
            'totalPool' => $totalPool,
            'recentRounds' => $recentRounds,
            'setting' => $setting,
        ]);
    }

    /**
     * Set manual winning number for current round (0-36).
     */
    public function setManualNumber(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'round_id' => ['nullable', 'string'],
            'manual_number' => ['required', 'integer', 'min:0', 'max:36'],
        ]);

        $manualNumber = (int) $validated['manual_number'];

        // 1. Get currently active round from engine
        $roundData = RouletteEngine::getCurrentRound();
        /** @var RouletteRound $currentRound */
        $currentRound = $roundData['round_model'];

        // If the submitted round_id exists and is not yet completed, use it; otherwise use active round
        $targetRound = null;
        if (! empty($validated['round_id'])) {
            $submittedRound = RouletteRound::where('round_id', $validated['round_id'])->first();
            if ($submittedRound && in_array($submittedRound->status, ['betting', 'spinning'], true)) {
                $targetRound = $submittedRound;
            }
        }

        if (! $targetRound) {
            $targetRound = $currentRound;
        }

        // Lock manual number on target round
        $targetRound->is_manual = true;
        $targetRound->manual_number = $manualNumber;
        $targetRound->save();

        if ($currentRound->id !== $targetRound->id && in_array($currentRound->status, ['betting', 'spinning'], true)) {
            $currentRound->is_manual = true;
            $currentRound->manual_number = $manualNumber;
            $currentRound->save();
        }

        // 2. Also update AdminSetting with control mode = 'manual' for engine fallback
        $setting = AdminSetting::current();
        $setting->update([
            'roulette_manual_number' => $manualNumber,
            'roulette_control_mode' => 'manual',
        ]);

        $details = RouletteEngine::getNumberDetails($manualNumber);

        return back()->with('success', "Target locked to #{$details['number']} (".strtoupper($details['color']).") for Round #{$targetRound->round_id}! The next spin result will land on #{$details['number']}.");
    }

    /**
     * Clear manual override and restore natural control mode.
     */
    public function clearManualNumber(Request $request): RedirectResponse
    {
        $roundData = RouletteEngine::getCurrentRound();
        /** @var RouletteRound $currentRound */
        $currentRound = $roundData['round_model'];
        $currentRound->is_manual = false;
        $currentRound->manual_number = null;
        $currentRound->save();

        if (! empty($request->input('round_id'))) {
            RouletteRound::where('round_id', $request->input('round_id'))->update([
                'is_manual' => false,
                'manual_number' => null,
            ]);
        }

        $setting = AdminSetting::current();
        $setting->update([
            'roulette_manual_number' => null,
            'roulette_control_mode' => 'fair',
        ]);

        return back()->with('success', 'Manual number override cleared. Returned to Fair RNG mode.');
    }

    /**
     * Update global Roulette admin settings (Min/Max bets, Control Mode, Duration).
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'roulette_min_bet' => ['required', 'numeric', 'min:1'],
            'roulette_max_bet' => ['required', 'numeric', 'gte:roulette_min_bet'],
            'roulette_round_duration' => ['required', 'integer', 'min:10', 'max:120'],
            'roulette_control_mode' => ['required', 'in:fair,admin_profit,manual'],
            'roulette_red_multiplier' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'roulette_black_multiplier' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'roulette_red_weight' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'roulette_black_weight' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'roulette_green_weight' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $setting = AdminSetting::current();
        $setting->update($validated);

        return back()->with('success', 'Roulette game parameters and control mode successfully updated.');
    }
}
