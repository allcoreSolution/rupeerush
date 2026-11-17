<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\RouletteBet;
use App\Models\RouletteRound;
use App\Services\RouletteEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RouletteController extends Controller
{
    /**
     * Show European Live Roulette Casino Arena.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $roundData = RouletteEngine::getCurrentRound();
        $setting = AdminSetting::current();

        $recentHistory = RouletteRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(30)
            ->get();

        $redWins = $recentHistory->where('winning_color', 'red')->count();
        $blackWins = $recentHistory->where('winning_color', 'black')->count();
        $greenWins = $recentHistory->where('winning_color', 'green')->count();
        $totalPast = max(1, $recentHistory->count());

        $redPercent = round(($redWins / $totalPast) * 100);
        $blackPercent = round(($blackWins / $totalPast) * 100);
        $greenPercent = 100 - ($redPercent + $blackPercent);

        $myActiveBets = [];
        $myRecentBets = [];

        if ($user) {
            $myActiveBets = RouletteBet::where('user_id', $user->id)
                ->where('round_id', $roundData['round_id'])
                ->get();

            $myRecentBets = RouletteEngine::getUserBets($user, 20);
        }

        return view('games.roulette', [
            'user' => $user,
            'roundData' => $roundData,
            'recentHistory' => $recentHistory,
            'redPercent' => $redPercent,
            'blackPercent' => $blackPercent,
            'greenPercent' => $greenPercent,
            'myActiveBets' => $myActiveBets,
            'myRecentBets' => $myRecentBets,
            'setting' => $setting,
            'betOdds' => RouletteEngine::getBetOdds($setting),
            'wheelOrder' => RouletteEngine::WHEEL_ORDER,
            'redNumbers' => RouletteEngine::RED_NUMBERS,
            'blackNumbers' => RouletteEngine::BLACK_NUMBERS,
        ]);
    }

    /**
     * Realtime JSON status for live wheel sync, countdown timer and settlement reveal.
     */
    public function getLiveStatus(Request $request): JsonResponse
    {
        $roundData = RouletteEngine::getCurrentRound();
        $user = Auth::user();

        // Current round winning details if already determined (e.g. spinning or result phase)
        $currentWinningData = null;
        if (! empty($roundData['winning_number']) !== null) {
            $details = RouletteEngine::getNumberDetails((int) $roundData['winning_number']);
            $currentWinningData = [
                'round_id' => $roundData['round_id'],
                'winning_number' => $roundData['winning_number'],
                'winning_color' => $roundData['winning_color'],
                'wheel_index' => $details['wheel_index'],
                'angle' => $details['angle'],
            ];
        }

        // Previous completed round for history fallback
        $prevRound = RouletteRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->first();

        $lastRoundData = null;
        if ($prevRound) {
            $details = RouletteEngine::getNumberDetails((int) $prevRound->winning_number);
            $lastRoundData = [
                'round_id' => $prevRound->round_id,
                'winning_number' => $prevRound->winning_number,
                'winning_color' => $prevRound->winning_color,
                'is_even' => $prevRound->is_even,
                'is_high' => $prevRound->is_high,
                'dozen' => $prevRound->dozen,
                'column' => $prevRound->column,
                'wheel_index' => $details['wheel_index'],
                'angle' => $details['angle'],
            ];
        }

        // Recent 20 numbers history
        $history = RouletteRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get(['round_id', 'winning_number', 'winning_color', 'is_even', 'is_high', 'dozen', 'column'])
            ->map(function ($r) {
                return [
                    'round_id' => $r->round_id,
                    'number' => $r->winning_number,
                    'color' => $r->winning_color,
                    'is_even' => $r->is_even,
                    'is_high' => $r->is_high,
                    'dozen' => $r->dozen,
                    'column' => $r->column,
                ];
            });

        $myActiveBets = [];
        $balance = 0.00;

        if ($user) {
            $myActiveBets = RouletteBet::where('user_id', $user->id)
                ->where('round_id', $roundData['round_id'])
                ->get(['id', 'bet_id', 'bet_type', 'bet_value', 'bet_amount', 'odds']);

            $balance = (float) $user->fresh()->balance;
        }

        return response()->json([
            'success' => true,
            'round_id' => $roundData['round_id'],
            'status' => $roundData['status'],
            'phase' => $roundData['phase'],
            'remaining_seconds' => $roundData['remaining_seconds'],
            'betting_seconds' => $roundData['betting_seconds'],
            'spinning_seconds' => $roundData['spinning_seconds'],
            'result_seconds' => $roundData['result_seconds'],
            'betting_duration' => $roundData['betting_duration'],
            'spin_duration' => $roundData['spin_duration'],
            'result_duration' => $roundData['result_duration'],
            'is_locked' => $roundData['is_locked'],
            'duration' => $roundData['duration'],
            'seed_hash' => $roundData['seed_hash'],
            'last_round' => $lastRoundData,
            'current_winning' => $currentWinningData,
            'history' => $history,
            'my_bets' => $myActiveBets,
            'balance' => $balance,
        ]);
    }

    /**
     * Place batch bets on the active roulette round.
     */
    public function placeBet(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to place bets.',
            ], 401);
        }

        $bets = $request->input('bets', []);
        if (empty($bets) || ! is_array($bets)) {
            return response()->json([
                'success' => false,
                'message' => 'No chips placed on the table.',
            ], 422);
        }

        $result = RouletteEngine::placeBets($user, $bets);

        return response()->json($result);
    }

    /**
     * Get user recent roulette bets.
     */
    public function getMyBets(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'bets' => []]);
        }

        $bets = RouletteEngine::getUserBets($user, 30);

        return response()->json([
            'success' => true,
            'bets' => $bets,
        ]);
    }

    /**
     * Get recent completed roulette rounds history.
     */
    public function getHistory(Request $request): JsonResponse
    {
        $history = RouletteRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'history' => $history,
        ]);
    }
}
