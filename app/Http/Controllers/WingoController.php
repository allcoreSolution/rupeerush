<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\WingoBet;
use App\Models\WingoPeriod;
use App\Services\WingoEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WingoController extends Controller
{
    /**
     * Show WinGo Color Slot Game Interface.
     */
    public function index(Request $request)
    {
        $gameType = $request->query('type', '30s');
        if (! in_array($gameType, ['30s', '1m', '3m', '5m'], true)) {
            $gameType = '30s';
        }

        $user = Auth::user();
        $periodData = WingoEngine::getCurrentPeriod($gameType);
        $setting = AdminSetting::current();

        $recentHistory = WingoPeriod::where('game_type', $gameType)
            ->where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(15)
            ->get();

        return view('wingo.index', [
            'user' => $user,
            'gameType' => $gameType,
            'periodData' => $periodData,
            'recentHistory' => $recentHistory,
            'setting' => $setting,
        ]);
    }

    /**
     * Realtime JSON status for live synchronization and countdown.
     */
    public function getLiveStatus(Request $request)
    {
        $gameType = $request->query('type', '30s');
        if (! in_array($gameType, ['30s', '1m', '3m', '5m'], true)) {
            $gameType = '30s';
        }

        $periodData = WingoEngine::getCurrentPeriod($gameType);

        $recentHistory = WingoPeriod::where('game_type', $gameType)
            ->where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($p) {
                return [
                    'period_id' => $p->period_id,
                    'winning_number' => $p->winning_number,
                    'winning_color' => $p->winning_color,
                    'winning_size' => $p->winning_size,
                ];
            });

        $user = Auth::user();
        $myActiveBets = [];
        $balance = 0.00;
        $latestWin = null;

        if ($user) {
            $balance = (float) $user->balance;
            $myActiveBets = WingoBet::where('user_id', $user->id)
                ->where('game_type', $gameType)
                ->where('period_id', $periodData['period_id'])
                ->get();

            $prevPeriod = WingoPeriod::where('game_type', $gameType)
                ->where('status', 'completed')
                ->orderBy('id', 'desc')
                ->first();

            if ($prevPeriod) {
                $wonBets = WingoBet::where('user_id', $user->id)
                    ->where('game_type', $gameType)
                    ->where('period_id', $prevPeriod->period_id)
                    ->where('status', 'won')
                    ->get();
                if ($wonBets->isNotEmpty()) {
                    $latestWin = [
                        'period_id' => $prevPeriod->period_id,
                        'win_amount' => (float) $wonBets->sum('win_amount'),
                        'winning_number' => $prevPeriod->winning_number,
                        'winning_color' => $prevPeriod->winning_color,
                        'winning_size' => $prevPeriod->winning_size,
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'game_type' => $gameType,
            'period_id' => $periodData['period_id'],
            'remaining_seconds' => $periodData['remaining_seconds'],
            'is_locked' => $periodData['is_locked'],
            'recent_history' => $recentHistory,
            'balance' => $balance,
            'my_active_bets' => $myActiveBets,
            'latest_win' => $latestWin,
        ]);
    }

    /**
     * Handle bet placement via AJAX.
     */
    public function placeBet(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Please login to place bets.'], 401);
        }

        if ($user->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Your account is suspended.'], 403);
        }

        $validated = $request->validate([
            'game_type' => ['required', 'in:30s,1m,3m,5m'],
            'bet_type' => ['required', 'in:color,number,size,combination'],
            'bet_selection' => ['required', 'string', 'max:20'],
            'unit_price' => ['required', 'numeric', 'min:1'],
            'multiplier' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        $res = WingoEngine::placeBet($user, $validated);

        return response()->json($res, $res['success'] ? 200 : 422);
    }

    /**
     * Get paginated completed history for Game History and Chart.
     */
    public function getHistory(Request $request)
    {
        $gameType = $request->query('type', '30s');
        $history = WingoPeriod::where('game_type', $gameType)
            ->where('status', 'completed')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return response()->json($history);
    }

    /**
     * Get player's own bets history on WinGo.
     */
    public function getMyBets(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['data' => []]);
        }

        $gameType = $request->query('type', '30s');
        $bets = WingoBet::where('user_id', $user->id)
            ->where('game_type', $gameType)
            ->orderBy('id', 'desc')
            ->paginate(15);

        return response()->json($bets);
    }
}
