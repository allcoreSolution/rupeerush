<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\TeenPattiBet;
use App\Models\TeenPattiRound;
use App\Services\TeenPattiEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeenPattiController extends Controller
{
    /**
     * Show 20-20 Teen Patti Casino Arena.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $roundData = TeenPattiEngine::getCurrentRound();
        $setting = AdminSetting::current();

        $recentHistory = TeenPattiRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(25)
            ->get();

        $aWins = $recentHistory->where('winner', 'player_a')->count();
        $bWins = $recentHistory->where('winner', 'player_b')->count();
        $tieWins = $recentHistory->where('winner', 'tie')->count();
        $totalPast = max(1, $recentHistory->count());

        $aPercent = round(($aWins / $totalPast) * 100);
        $bPercent = round(($bWins / $totalPast) * 100);
        $tiePercent = 100 - ($aPercent + $bPercent);

        $myActiveBets = [];
        if ($user) {
            $myActiveBets = TeenPattiBet::where('user_id', $user->id)
                ->where('round_id', $roundData['round_id'])
                ->get();
        }

        return view('games.teen-patti', [
            'user' => $user,
            'roundData' => $roundData,
            'recentHistory' => $recentHistory,
            'aPercent' => $aPercent,
            'bPercent' => $bPercent,
            'tiePercent' => $tiePercent,
            'myActiveBets' => $myActiveBets,
            'setting' => $setting,
        ]);
    }

    /**
     * Realtime JSON status for live countdown, card dealing, bets and sync.
     */
    public function getLiveStatus(Request $request): JsonResponse
    {
        $roundData = TeenPattiEngine::getCurrentRound();
        $user = Auth::user();

        // Previous completed round for dealing reveal sync
        $prevRound = TeenPattiRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->first();

        $lastRoundData = null;
        if ($prevRound) {
            $lastRoundData = [
                'round_id' => $prevRound->round_id,
                'winner' => $prevRound->winner,
                'player_a_cards' => $prevRound->player_a_cards,
                'player_b_cards' => $prevRound->player_b_cards,
                'player_a_rank_type' => $prevRound->player_a_rank_type,
                'player_a_rank_title' => $prevRound->player_a_rank_title,
                'player_b_rank_type' => $prevRound->player_b_rank_type,
                'player_b_rank_title' => $prevRound->player_b_rank_title,
            ];
        }

        // Recent 20 roadmap history
        $roadmap = TeenPattiRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get(['round_id', 'winner', 'player_a_rank_type', 'player_b_rank_type'])
            ->map(function ($r) {
                return [
                    'round_id' => $r->round_id,
                    'winner' => $r->winner,
                    'a_type' => $r->player_a_rank_type,
                    'b_type' => $r->player_b_rank_type,
                ];
            });

        $myActiveBets = [];
        $balance = 0.00;
        $latestWin = null;

        if ($user) {
            $balance = (float) $user->balance;
            $myActiveBets = TeenPattiBet::where('user_id', $user->id)
                ->where('round_id', $roundData['round_id'])
                ->get();

            // Check if user won in the latest completed round
            if ($prevRound) {
                $wonBets = TeenPattiBet::where('user_id', $user->id)
                    ->where('round_id', $prevRound->round_id)
                    ->where('status', 'won')
                    ->get();

                if ($wonBets->isNotEmpty()) {
                    $totalWon = (float) $wonBets->sum('payout_amount');
                    $latestWin = [
                        'round_id' => $prevRound->round_id,
                        'total_won' => $totalWon,
                        'message' => "Congratulations! You won ₹{$totalWon} in Round #{$prevRound->round_id}!",
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'round_id' => $roundData['round_id'],
            'duration' => $roundData['duration'],
            'remaining_seconds' => $roundData['remaining_seconds'],
            'is_locked' => $roundData['is_locked'],
            'status' => $roundData['status'],
            'bets_summary' => $roundData['bets_summary'],
            'last_round' => $lastRoundData,
            'roadmap' => $roadmap,
            'my_active_bets' => $myActiveBets,
            'balance' => $balance,
            'latest_win' => $latestWin,
        ]);
    }

    /**
     * Recent completed rounds history.
     */
    public function getHistory(Request $request): JsonResponse
    {
        $history = TeenPattiRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'history' => $history,
        ]);
    }

    /**
     * Get logged in user's Teen Patti bet history.
     */
    public function getMyBets(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $bets = TeenPattiBet::where('user_id', $user->id)
            ->with('round:round_id,winner,player_a_cards,player_b_cards,player_a_rank_title,player_b_rank_title')
            ->orderBy('id', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'success' => true,
            'bets' => $bets,
            'balance' => (float) $user->balance,
        ]);
    }

    /**
     * Place a 20-20 Teen Patti bet.
     */
    public function placeBet(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Please login to place bets!'], 401);
        }

        $request->validate([
            'bet_type' => ['required', 'string', 'in:player_a,player_b,pair_plus_a,pair_plus_b,tie'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $res = TeenPattiEngine::placeBet(
            $user,
            (string) $request->bet_type,
            (float) $request->amount
        );

        return response()->json($res, $res['success'] ? 200 : 422);
    }
}
