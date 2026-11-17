<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\AviatorBet;
use App\Models\AviatorRound;
use App\Services\AviatorEngine;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AviatorAdminController extends Controller
{
    /**
     * Admin Live Aviator Control Hub & Risk Monitoring.
     */
    public function index(Request $request): View
    {
        $roundData = AviatorEngine::getCurrentRound();
        $round = $roundData['round_model'];

        // Bets placed on active round
        $activeBets = AviatorBet::with('user')
            ->where('round_id', $round->round_id)
            ->orderBy('id', 'desc')
            ->get();

        $totalWagered = (float) $activeBets->sum('amount');
        $totalCashedOut = (float) $activeBets->where('status', 'cashed_out')->sum('win_amount');
        $pendingExposure = (float) $activeBets->where('status', 'pending')->sum('amount');

        // Recent completed rounds (up to 50)
        $recentRounds = AviatorRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        $setting = AdminSetting::current();

        return view('admin.aviator.index', [
            'roundData' => $roundData,
            'round' => $round,
            'activeBets' => $activeBets,
            'totalWagered' => $totalWagered,
            'totalCashedOut' => $totalCashedOut,
            'pendingExposure' => $pendingExposure,
            'recentRounds' => $recentRounds,
            'setting' => $setting,
        ]);
    }

    /**
     * Set manual crash multiplier for active and upcoming rounds.
     */
    public function setManualCrashMultiplier(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'round_id' => ['nullable', 'string'],
            'crash_multiplier' => ['required', 'numeric', 'min:1.00', 'max:500.00'],
        ]);

        $multiplier = round((float) $validated['crash_multiplier'], 2);

        // 1. Save standing lock point to Admin Settings so all future rounds inherit it
        $setting = AdminSetting::current();
        $setting->update([
            'aviator_manual_multiplier' => $multiplier,
        ]);

        // 2. Fetch current active round from engine
        $roundData = AviatorEngine::getCurrentRound();
        $round = $roundData['round_model'];

        // If a specific round_id was supplied and still uncompleted, also ensure it gets updated
        if (! empty($validated['round_id']) && $validated['round_id'] !== $round->round_id) {
            $specifiedRound = AviatorRound::where('round_id', $validated['round_id'])
                ->where('status', '!=', 'completed')
                ->first();
            if ($specifiedRound) {
                $specifiedRound->is_manual = true;
                $specifiedRound->manual_crash_multiplier = $multiplier;
                $specifiedRound->crash_multiplier = $multiplier;
                $flightDurationSeconds = AviatorEngine::calculateFlightDuration($multiplier);
                $specifiedRound->crash_time = $specifiedRound->flight_start_time->copy()->addMilliseconds((int) ($flightDurationSeconds * 1000));
                $specifiedRound->end_time = $specifiedRound->crash_time->copy()->addSeconds(AviatorEngine::CRASH_COOLDOWN);
                $specifiedRound->save();
            }
        }

        // 3. Update the active round
        $now = Carbon::now();
        $round->is_manual = true;
        $round->manual_crash_multiplier = $multiplier;

        if ($roundData['status'] === 'betting') {
            $round->crash_multiplier = $multiplier;
            $flightDurationSeconds = AviatorEngine::calculateFlightDuration($multiplier);
            $round->crash_time = $round->flight_start_time->copy()->addMilliseconds((int) ($flightDurationSeconds * 1000));
            $round->end_time = $round->crash_time->copy()->addSeconds(AviatorEngine::CRASH_COOLDOWN);
            $round->save();
        } elseif ($roundData['status'] === 'flying') {
            $elapsedSeconds = max(0.0, ($now->getTimestampMs() - $round->flight_start_time->getTimestampMs()) / 1000);
            $currentMultiplier = AviatorEngine::calculateMultiplierAtTime($elapsedSeconds, (float) $round->crash_multiplier);

            if ($multiplier <= $currentMultiplier) {
                // If requested multiplier already passed, crash immediately now
                $round->crash_multiplier = max(1.00, $currentMultiplier);
                $round->crash_time = $now->copy();
                $round->end_time = $now->copy()->addSeconds(AviatorEngine::CRASH_COOLDOWN);
                $round->save();
            } else {
                // Adjust flight end to reach the new target
                $round->crash_multiplier = $multiplier;
                $flightDurationSeconds = AviatorEngine::calculateFlightDuration($multiplier);
                $round->crash_time = $round->flight_start_time->copy()->addMilliseconds((int) ($flightDurationSeconds * 1000));
                $round->end_time = $round->crash_time->copy()->addSeconds(AviatorEngine::CRASH_COOLDOWN);
                $round->save();
            }
        } else {
            // Already crashed or ending, next round will automatically start with $multiplier
            $round->save();
        }

        return back()->with('success', "Crash point locked to [{$multiplier}x]! Active Round #{$round->round_id} aur sabhi upcoming rounds ab is point par crash honge.");
    }

    /**
     * Clear manual override and restore probably fair automatic distribution.
     */
    public function clearManualCrashMultiplier(Request $request): RedirectResponse
    {
        // 1. Clear standing manual multiplier from Admin Settings
        $setting = AdminSetting::current();
        $setting->update([
            'aviator_manual_multiplier' => null,
        ]);

        // 2. Clear on active round if still in betting
        $roundData = AviatorEngine::getCurrentRound();
        $round = $roundData['round_model'];

        if ($roundData['status'] === 'betting') {
            $freshMultiplier = AviatorEngine::generateCrashMultiplier();
            $round->is_manual = false;
            $round->manual_crash_multiplier = null;
            $round->crash_multiplier = $freshMultiplier;

            $flightDurationSeconds = AviatorEngine::calculateFlightDuration($freshMultiplier);
            $round->crash_time = $round->flight_start_time->copy()->addMilliseconds((int) ($flightDurationSeconds * 1000));
            $round->end_time = $round->crash_time->copy()->addSeconds(AviatorEngine::CRASH_COOLDOWN);
            $round->save();
        }

        // If a specific round_id was also submitted
        if ($request->filled('round_id')) {
            $specified = AviatorRound::where('round_id', $request->input('round_id'))
                ->where('status', '!=', 'completed')
                ->first();
            if ($specified && $specified->id !== $round->id) {
                $specified->is_manual = false;
                $specified->manual_crash_multiplier = null;
                $specified->save();
            }
        }

        return back()->with('success', 'Manual Lock hata diya gaya hai. Aviator game ab Probably Fair Auto Random Mode me chalega.');
    }

    /**
     * Update Aviator betting limits.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'aviator_min_bet' => ['required', 'numeric', 'min:1', 'max:50000'],
            'aviator_max_bet' => ['required', 'numeric', 'min:10', 'max:500000'],
        ]);

        $setting = AdminSetting::current();
        $setting->update($validated);

        return back()->with('success', 'Aviator betting limits updated successfully!');
    }

    /**
     * View recent Aviator winners list.
     */
    public function winners(Request $request): View
    {
        $query = AviatorBet::with(['user', 'round'])
            ->where('status', 'cashed_out');

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

        return view('admin.aviator.winners', [
            'wonBets' => $wonBets,
            'totalWinners' => $totalWinners,
            'totalPayout' => (float) $totalPayout,
            'search' => $request->search ?? '',
        ]);
    }
}
