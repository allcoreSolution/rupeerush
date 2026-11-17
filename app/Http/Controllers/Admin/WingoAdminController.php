<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WingoBet;
use App\Models\WingoPeriod;
use App\Services\WingoEngine;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WingoAdminController extends Controller
{
    /**
     * Admin Live WinGo Control & Risk Monitor.
     */
    public function index(Request $request)
    {
        $gameType = $request->query('type', '30s');
        if (! in_array($gameType, ['30s', '1m', '3m', '5m'], true)) {
            $gameType = '30s';
        }

        $periodData = WingoEngine::getCurrentPeriod($gameType);
        $period = $periodData['period_model'];

        // Bets placed on this active period
        $activeBets = WingoBet::with('user')
            ->where('game_type', $gameType)
            ->where('period_id', $period->period_id)
            ->orderBy('id', 'desc')
            ->get();

        // Calculate exposure for each number 0-9
        $exposure = WingoEngine::calculateExposure($period);

        // Find best house profit number
        $minPayout = PHP_FLOAT_MAX;
        $safestNumber = 0;
        foreach ($exposure as $num => $data) {
            if ($data['payout'] < $minPayout) {
                $minPayout = $data['payout'];
                $safestNumber = $num;
            }
        }

        // Recent 50 rounds
        $recentRounds = WingoPeriod::where('game_type', $gameType)
            ->where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        return view('admin.wingo.index', [
            'gameType' => $gameType,
            'periodData' => $periodData,
            'period' => $period,
            'activeBets' => $activeBets,
            'exposure' => $exposure,
            'safestNumber' => $safestNumber,
            'recentRounds' => $recentRounds,
        ]);
    }

    /**
     * Set manual winning number for the current or upcoming period.
     */
    public function setManualNumber(Request $request)
    {
        return $this->setManualTarget($request);
    }

    /**
     * Set manual target (Number, Color, Size, or Color+Size) for the current active period.
     */
    public function setManualTarget(Request $request)
    {
        $validated = $request->validate([
            'game_type' => ['required', 'in:30s,1m,3m,5m'],
            'period_id' => ['required', 'string'],
            'target_mode' => ['nullable', 'string', 'in:color,number,combination,size'],
            'manual_number' => ['nullable', 'integer', 'min:0', 'max:9'],
            'manual_color' => ['nullable', 'string', 'in:green,red,violet'],
            'manual_size' => ['nullable', 'string', 'in:big,small'],
        ]);

        $period = WingoPeriod::where('game_type', $validated['game_type'])
            ->where('period_id', $validated['period_id'])
            ->firstOrFail();

        $period->is_manual = true;

        $mode = $validated['target_mode'] ?? null;
        if (! $mode) {
            if ($request->filled('manual_number')) {
                $mode = 'number';
            } elseif ($request->filled('manual_color') && $request->filled('manual_size')) {
                $mode = 'combination';
            } elseif ($request->filled('manual_color')) {
                $mode = 'color';
            } elseif ($request->filled('manual_size')) {
                $mode = 'size';
            }
        }

        // Case 1: Exact Number Lock - ONLY number is locked (Color & Size remain unlocked)
        if ($mode === 'number' && $request->filled('manual_number')) {
            $number = (int) $validated['manual_number'];
            $period->manual_number = $number;
            $period->manual_color = null;
            $period->manual_size = null;
            $period->save();

            return back()->with('success', "Ball Number [#{$number}] locked for Period #{$period->period_id}. (Color & Size remain unlocked).");
        }

        // Case 2: Combination Lock - Color + Size locked (Number is unlocked and drawn from combo candidates)
        if ($mode === 'combination' || ($request->filled('manual_color') && $request->filled('manual_size'))) {
            $color = $validated['manual_color'] ?? $request->input('manual_color');
            $size = $validated['manual_size'] ?? $request->input('manual_size');

            $period->manual_color = $color;
            $period->manual_size = $size;
            $period->manual_number = null;
            $period->save();

            $matchingNumbers = WingoEngine::getMatchingNumbers($color, $size);
            $candidatesList = implode(', ', array_map(fn ($n) => "#$n", $matchingNumbers));

            return back()->with('success', 'Target Combination locked to ['.strtoupper($color).' + '.strtoupper($size)."] for Period #{$period->period_id}. Engine will draw from: [{$candidatesList}] (Least House Payout).");
        }

        // Case 3: Color Lock - ONLY Color is locked (Number and Size remain unlocked)
        if ($mode === 'color' || ($request->filled('manual_color') && ! $request->filled('manual_size'))) {
            $color = $validated['manual_color'] ?? $request->input('manual_color');

            $period->manual_color = $color;
            $period->manual_number = null;
            $period->manual_size = null;
            $period->save();

            $matchingNumbers = WingoEngine::getMatchingNumbers($color, null);
            $candidatesList = implode(', ', array_map(fn ($n) => "#$n", $matchingNumbers));

            return back()->with('success', 'Color locked to ['.strtoupper($color)."] for Period #{$period->period_id}. Engine will draw from: [{$candidatesList}] (Least House Payout).");
        }

        // Case 4: Size Lock - ONLY Size is locked (Number and Color remain unlocked)
        if ($mode === 'size' || ($request->filled('manual_size') && ! $request->filled('manual_color'))) {
            $size = $validated['manual_size'] ?? $request->input('manual_size');

            $period->manual_size = $size;
            $period->manual_number = null;
            $period->manual_color = null;
            $period->save();

            $matchingNumbers = WingoEngine::getMatchingNumbers(null, $size);
            $candidatesList = implode(', ', array_map(fn ($n) => "#$n", $matchingNumbers));

            return back()->with('success', 'Size locked to ['.strtoupper($size)."] for Period #{$period->period_id}. Engine will draw from: [{$candidatesList}] (Least House Payout).");
        }

        return back()->with('error', 'Please choose a target Color, Size, Combination, or Number to lock.');
    }

    /**
     * Clear manual override and revert to auto mode.
     */
    public function clearManual(Request $request)
    {
        $validated = $request->validate([
            'game_type' => ['required', 'in:30s,1m,3m,5m'],
            'period_id' => ['required', 'string'],
        ]);

        $period = WingoPeriod::where('game_type', $validated['game_type'])
            ->where('period_id', $validated['period_id'])
            ->firstOrFail();

        $period->is_manual = false;
        $period->manual_number = null;
        $period->manual_color = null;
        $period->manual_size = null;
        $period->save();

        return back()->with('success', "Manual override cleared. Period #{$validated['period_id']} will draw automatically using Least-Payout / Random mode.");
    }

    /**
     * WinGo Winners & Payouts History.
     */
    public function winners(Request $request)
    {
        $gameType = $request->query('type');
        $dateStr = $request->query('date', Carbon::now('Asia/Kolkata')->format('Y-m-d'));
        $search = $request->query('search');

        $query = WingoBet::with(['user', 'period'])->where('status', 'won')->orderBy('id', 'desc');

        if ($gameType && in_array($gameType, ['30s', '1m', '3m', '5m'], true)) {
            $query->where('game_type', $gameType);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $dateStr);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('period_id', 'like', "%{$search}%")
                    ->orWhere('bet_selection', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $totalWinners = (clone $query)->count();
        $totalPayout = (clone $query)->sum('win_amount');

        $winners = $query->paginate(25)->withQueryString();

        return view('admin.wingo.winners', [
            'winners' => $winners,
            'selectedType' => $gameType,
            'selectedDate' => $dateStr,
            'search' => $search,
            'totalWinners' => $totalWinners,
            'totalPayout' => $totalPayout,
        ]);
    }
}
