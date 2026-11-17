<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\GameSlot;
use App\Models\Result;
use App\Services\LotteryEngine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $dateStr = $request->query('date', Carbon::now('Asia/Kolkata')->format('Y-m-d'));
        $selectedDate = Carbon::parse($dateStr);
        $dateFormatted = $selectedDate->format('Y-m-d');

        $slots = GameSlot::where('is_active', true)->orderBy('display_order')->get();
        $results = Result::where('result_date', $dateFormatted)->get()->keyBy('slot_time');

        $timingStatus = LotteryEngine::getSlotScheduleStatus();
        $nextSlot = $timingStatus['nextUpcomingSlot'];

        $targetSlotTime = $request->query('slot_time', $nextSlot['slot_time'] ?? ($slots->first()->slot_time ?? '10:00 AM'));

        // All bets placed on this target slot
        $betsOnTargetSlot = Bet::with('user')
            ->where('result_date', $dateFormatted)
            ->where('slot_time', $targetSlotTime)
            ->orderBy('id', 'desc')
            ->get();

        // Recent winners for this selected date
        $recentWinners = Bet::with('user')
            ->where('result_date', $dateFormatted)
            ->where('status', 'won')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // Existing numbers for the target slot if available
        $targetSlotResult = $results->get($targetSlotTime);

        $riskBySlotKey = [];
        foreach (LotteryEngine::SLOT_KEYS as $key) {
            $riskBySlotKey[$key] = [];
        }

        foreach ($betsOnTargetSlot as $bet) {
            $k = $bet->slot_key;
            $n = $bet->chosen_number;
            if (! isset($riskBySlotKey[$k][$n])) {
                $riskBySlotKey[$k][$n] = ['count' => 0, 'amount' => 0, 'exposure' => 0, 'player_names' => []];
            }
            $riskBySlotKey[$k][$n]['count'] += 1;
            $riskBySlotKey[$k][$n]['amount'] += $bet->amount;
            $riskBySlotKey[$k][$n]['exposure'] += ($bet->amount * $bet->win_multiplier);
            if ($bet->user && ! in_array($bet->user->name, $riskBySlotKey[$k][$n]['player_names'])) {
                $riskBySlotKey[$k][$n]['player_names'][] = $bet->user->name;
            }
        }

        return view('admin.results.index', [
            'selectedDate' => $selectedDate,
            'slots' => $slots,
            'results' => $results,
            'nextSlot' => $nextSlot,
            'targetSlotTime' => $targetSlotTime,
            'targetSlotResult' => $targetSlotResult,
            'timingStatus' => $timingStatus,
            'betsOnTargetSlot' => $betsOnTargetSlot,
            'recentWinners' => $recentWinners,
            'riskBySlotKey' => $riskBySlotKey,
            'slotKeys' => LotteryEngine::SLOT_KEYS,
        ]);
    }

    public function saveManualResult(Request $request)
    {
        $validated = $request->validate([
            'result_date' => ['required', 'date'],
            'slot_time' => ['required', 'string'],
            'XA' => ['required', 'digits:2'],
            'XB' => ['required', 'digits:2'],
            'XC' => ['required', 'digits:2'],
            'XD' => ['required', 'digits:2'],
            'XE' => ['required', 'digits:2'],
            'XF' => ['required', 'digits:2'],
            'XG' => ['required', 'digits:2'],
            'XH' => ['required', 'digits:2'],
            'XI' => ['required', 'digits:2'],
            'XJ' => ['required', 'digits:2'],
            'publish_now' => ['nullable', 'boolean'],
        ]);

        $overrideNumbers = [];
        foreach (LotteryEngine::SLOT_KEYS as $k) {
            $overrideNumbers[$k] = $validated[$k];
        }

        $existing = Result::where('result_date', $validated['result_date'])
            ->where('slot_time', $validated['slot_time'])
            ->first();

        // If publish_now is checked OR if the result was already published before, immediately publish & recalculate all bets
        if ($request->boolean('publish_now') || ($existing && $existing->status === 'published')) {
            LotteryEngine::publishResultAndProcessPayouts(
                $validated['slot_time'],
                $validated['result_date'],
                $overrideNumbers,
                Auth::id()
            );

            return back()->with('success', "Result for {$validated['slot_time']} updated & player payouts re-calculated and credited successfully!");
        } else {
            // Preset for future slot
            Result::updateOrCreate(
                [
                    'result_date' => $validated['result_date'],
                    'slot_time' => $validated['slot_time'],
                ],
                array_merge($overrideNumbers, [
                    'status' => 'scheduled',
                    'is_manual' => true,
                    'created_by' => Auth::id(),
                ])
            );

            return back()->with('success', "Manual preset saved for {$validated['slot_time']}. It will be officially published and winners credited at generation time.");
        }
    }

    public function instantGenerate(Request $request)
    {
        $timingStatus = LotteryEngine::getSlotScheduleStatus();
        $nextSlot = $timingStatus['nextUpcomingSlot'];
        $date = $nextSlot['slot_datetime']->format('Y-m-d');

        $result = LotteryEngine::publishResultAndProcessPayouts($nextSlot['slot_time'], $date, null, Auth::id());

        return back()->with('success', "Instantly generated and published result for {$nextSlot['slot_time']}! Winning bets have been automatically credited.");
    }

    public function winners(Request $request)
    {
        $dateStr = $request->query('date', Carbon::now('Asia/Kolkata')->format('Y-m-d'));
        $slotTime = $request->query('slot_time');
        $search = $request->query('search');

        $query = Bet::with(['user'])->where('status', 'won')->orderBy('id', 'desc');

        if ($request->filled('date')) {
            $query->where('result_date', $dateStr);
        }

        if ($request->filled('slot_time')) {
            $query->where('slot_time', $slotTime);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('chosen_number', 'like', "%{$search}%")
                    ->orWhere('slot_key', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $totalWinnersCount = (clone $query)->count();
        $totalPayoutAmount = (clone $query)->sum('win_amount');
        $totalWagerAmount = (clone $query)->sum('amount');

        $winners = $query->paginate(25)->withQueryString();
        $slots = GameSlot::where('is_active', true)->orderBy('display_order')->get();

        return view('admin.results.winners', [
            'winners' => $winners,
            'selectedDate' => $dateStr,
            'selectedSlot' => $slotTime,
            'search' => $search,
            'slots' => $slots,
            'totalWinnersCount' => $totalWinnersCount,
            'totalPayoutAmount' => $totalPayoutAmount,
            'totalWagerAmount' => $totalWagerAmount,
        ]);
    }
}
