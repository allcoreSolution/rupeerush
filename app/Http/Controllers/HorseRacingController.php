<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\HorseRaceBet;
use App\Models\HorseRaceRound;
use App\Services\HorseRacingEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HorseRacingController extends Controller
{
    /**
     * Show Horse Racing Live Track & Betting Arena.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $roundData = HorseRacingEngine::getCurrentRound();
        $setting = AdminSetting::current();

        $recentHistory = HorseRaceRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(30)
            ->get();

        // Calculate gate win statistics for runner form
        $totalPast = max(1, $recentHistory->count());
        $gateStats = [];
        for ($gate = 1; $gate <= 6; $gate++) {
            $wins = $recentHistory->where('winner_horse_id', $gate)->count();
            $gateStats[$gate] = [
                'wins' => $wins,
                'percent' => round(($wins / $totalPast) * 100),
            ];
        }

        $myActiveBets = [];
        $myRecentBets = [];

        if ($user) {
            $myActiveBets = HorseRaceBet::where('user_id', $user->id)
                ->where('round_id', $roundData['round_id'])
                ->get();

            $myRecentBets = HorseRacingEngine::getUserBets($user, 20);
        }

        return view('games.horse_racing', [
            'user' => $user,
            'roundData' => $roundData,
            'recentHistory' => $recentHistory,
            'gateStats' => $gateStats,
            'myActiveBets' => $myActiveBets,
            'myRecentBets' => $myRecentBets,
            'setting' => $setting,
            'horses' => $roundData['horses'],
        ]);
    }

    /**
     * Realtime JSON status for live race sync, countdown timer, and settlement reveal.
     */
    public function getLiveStatus(Request $request): JsonResponse
    {
        $roundData = HorseRacingEngine::getCurrentRound();
        $user = Auth::user();

        // Previous completed round for sprint animation reveal
        $prevRound = HorseRaceRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->first();

        $lastRoundData = null;
        if ($prevRound) {
            $horses = $prevRound->horses_data ?: HorseRacingEngine::getHorsesRoster($prevRound->server_seed ?: '');
            $winnerId = (int) ($prevRound->winner_horse_id ?: 1);
            $secondId = (int) ($prevRound->second_horse_id ?: 2);
            $thirdId = (int) ($prevRound->third_horse_id ?: 3);

            $lastRoundData = [
                'round_id' => $prevRound->round_id,
                'winner_horse_id' => $winnerId,
                'second_horse_id' => $secondId,
                'third_horse_id' => $thirdId,
                'waypoints' => HorseRacingEngine::simulateRaceWaypoints(
                    $winnerId,
                    $secondId,
                    $thirdId,
                    $horses,
                    $prevRound->server_seed ?: $prevRound->round_id
                ),
            ];
        }

        // Recent 20 rounds history
        $history = HorseRaceRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get(['round_id', 'winner_horse_id', 'second_horse_id', 'third_horse_id', 'total_bets_count', 'total_bet_amount', 'total_payout_amount', 'created_at'])
            ->map(function ($r) {
                return [
                    'round_id' => $r->round_id,
                    'winner_horse_id' => $r->winner_horse_id,
                    'second_horse_id' => $r->second_horse_id,
                    'third_horse_id' => $r->third_horse_id,
                    'total_bets' => $r->total_bets_count,
                    'total_amount' => (float) $r->total_bet_amount,
                    'total_payout' => (float) $r->total_payout_amount,
                ];
            });

        $myActiveBets = [];
        $balance = 0.00;

        if ($user) {
            $myActiveBets = HorseRaceBet::where('user_id', $user->id)
                ->where('round_id', $roundData['round_id'])
                ->get(['id', 'bet_id', 'horse_id', 'horse_name', 'bet_type', 'bet_amount', 'odds']);

            $balance = (float) $user->fresh()->balance;
        }

        return response()->json([
            'success' => true,
            'round_id' => $roundData['round_id'],
            'status' => $roundData['status'],
            'remaining_seconds' => $roundData['remaining_seconds'],
            'racing_countdown' => $roundData['racing_countdown'],
            'is_locked' => $roundData['is_locked'],
            'duration' => $roundData['duration'],
            'seed_hash' => $roundData['seed_hash'],
            'horses' => $roundData['horses'],
            'active_race' => $roundData['active_race'] ?? null,
            'last_round' => $lastRoundData,
            'history' => $history,
            'my_bets' => $myActiveBets,
            'balance' => $balance,
        ]);
    }

    /**
     * Place batch or single bets on the active horse race round.
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

        // Support both batch { bets: [...] } and single { horse_id, bet_type, amount } payloads
        $bets = $request->input('bets');
        if (empty($bets)) {
            if ($request->has(['horse_id', 'bet_type', 'amount'])) {
                $bets = [[
                    'horse_id' => $request->input('horse_id'),
                    'bet_type' => $request->input('bet_type'),
                    'amount' => $request->input('amount'),
                ]];
            }
        }

        if (empty($bets) || ! is_array($bets)) {
            return response()->json([
                'success' => false,
                'message' => 'No runners selected. Please select a horse and bet type.',
            ], 422);
        }

        $result = HorseRacingEngine::placeBets($user, $bets);

        return response()->json($result);
    }

    /**
     * Get user recent horse racing bets.
     */
    public function getMyBets(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'bets' => []]);
        }

        $bets = HorseRacingEngine::getUserBets($user, 30);

        return response()->json([
            'success' => true,
            'bets' => $bets,
        ]);
    }

    /**
     * Get recent completed horse racing rounds history.
     */
    public function getHistory(Request $request): JsonResponse
    {
        $history = HorseRaceRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'history' => $history,
        ]);
    }
}
