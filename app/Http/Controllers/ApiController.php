<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\Result;
use App\Services\LotteryEngine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function getLiveStatus(Request $request)
    {
        $now = Carbon::now('Asia/Kolkata');
        $timingStatus = LotteryEngine::getSlotScheduleStatus($now);
        $setting = AdminSetting::current();

        $curr = $timingStatus['currentPublishedSlot'];
        $currDate = $curr['slot_datetime']->format('Y-m-d');

        // Fetch or auto-publish current slot result if generated
        $latestResult = Result::where('result_date', $currDate)
            ->where('slot_time', $curr['slot_time'])
            ->first();

        if (! $latestResult && $curr['is_generated']) {
            $latestResult = LotteryEngine::publishResultAndProcessPayouts($curr['slot_time'], $currDate);
        }

        $next = $timingStatus['nextUpcomingSlot'];
        $nextGenTime = $next['gen_datetime'];
        $secondsUntilGen = max(0, $now->diffInSeconds($nextGenTime, false));

        $user = Auth::user();

        return response()->json([
            'currentTime' => $now->format('h:i:s A'),
            'currentDate' => $now->format('d F Y'),
            'currentPublishedSlot' => [
                'slot_time' => $curr['slot_time'],
                'date' => $curr['slot_datetime']->format('d F Y'),
                'date_dash' => $curr['slot_datetime']->format('d-m-Y'),
                'numbers' => $latestResult ? $latestResult->getNumbersArray() : LotteryEngine::generate10Numbers($currDate, $curr['slot_time']),
            ],
            'nextUpcomingSlot' => [
                'slot_time' => $next['slot_time'],
                'gen_time' => $nextGenTime->format('h:i A'),
                'secondsRemaining' => (int) $secondsUntilGen,
                'is_betting_open' => $secondsUntilGen > 0,
            ],
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'balance' => (float) $user->balance,
            ] : null,
            'lockMinutes' => $setting->betting_lock_minutes ?? 2,
        ]);
    }

    public function getHistory(Request $request)
    {
        $dateStr = $request->query('date', Carbon::now('Asia/Kolkata')->format('Y-m-d'));
        $date = Carbon::parse($dateStr);
        $dateFormatted = $date->format('Y-m-d');

        $results = Result::where('result_date', $dateFormatted)
            ->orderBy('slot_datetime')
            ->get();

        $scheduleStatus = LotteryEngine::getSlotScheduleStatus();
        $allSlots = $scheduleStatus['allSlotsToday'];

        $historyRows = [];
        $now = Carbon::now('Asia/Kolkata');

        foreach ($allSlots as $slot) {
            $existing = $results->firstWhere('slot_time', $slot['slot_time']);
            $isPastGen = $date->isPast() || ($date->isToday() && $now->greaterThanOrEqualTo($slot['gen_datetime']));

            if ($existing) {
                $numbers = $existing->getNumbersArray();
                $status = 'Published';
            } elseif ($isPastGen) {
                // Auto generate if past
                $numbers = LotteryEngine::generate10Numbers($dateFormatted, $slot['slot_time']);
                $status = 'Published';
            } else {
                $numbers = array_fill_keys(LotteryEngine::SLOT_KEYS, '-');
                $status = 'Pending';
            }

            $historyRows[] = [
                'date' => $date->format('d-m-Y'),
                'fullDate' => $date->format('d F Y'),
                'time' => $slot['slot_time'],
                'numbers' => $numbers,
                'status' => $status,
            ];
        }

        return response()->json([
            'date' => $date->format('d F Y'),
            'rows' => $historyRows,
        ]);
    }
}
