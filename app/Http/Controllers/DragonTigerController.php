<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\DragonTigerBet;
use App\Models\DragonTigerRound;
use App\Services\DragonTigerEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DragonTigerController extends Controller
{
    /**
     * Show Dragon Tiger Live Casino Arena.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $roundData = DragonTigerEngine::getCurrentRound();
        $setting = AdminSetting::current();

        $recentHistory = DragonTigerRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        $dragonWins = $recentHistory->where('winner', 'dragon')->count();
        $tigerWins = $recentHistory->where('winner', 'tiger')->count();
        $tieWins = $recentHistory->whereIn('winner', ['tie', 'suited_tie'])->count();
        $totalPast = max(1, $recentHistory->count());

        $dragonPercent = round(($dragonWins / $totalPast) * 100);
        $tigerPercent = round(($tigerWins / $totalPast) * 100);
        $tiePercent = max(0, 100 - ($dragonPercent + $tigerPercent));

        $myActiveBets = [];
        $myRecentBets = [];
        if ($user) {
            $myActiveBets = DragonTigerBet::where('user_id', $user->id)
                ->where('round_id', $roundData['round_id'])
                ->get();

            $myRecentBets = DragonTigerBet::where('user_id', $user->id)
                ->with('round:round_id,winner,dragon_card,tiger_card')
                ->orderBy('id', 'desc')
                ->limit(30)
                ->get();
        }

        return view('games.dragon_tiger', [
            'user' => $user,
            'roundData' => $roundData,
            'recentHistory' => $recentHistory,
            'dragonWins' => $dragonWins,
            'tigerWins' => $tigerWins,
            'tieWins' => $tieWins,
            'dragonPercent' => $dragonPercent,
            'tigerPercent' => $tigerPercent,
            'tiePercent' => $tiePercent,
            'myActiveBets' => $myActiveBets,
            'myRecentBets' => $myRecentBets,
            'setting' => $setting,
        ]);
    }

    /**
     * Realtime JSON status for countdown, card reveal, active bets, and stats.
     */
    public function getLiveStatus(Request $request): JsonResponse
    {
        $roundData = DragonTigerEngine::getCurrentRound();
        $user = Auth::user();

        // Previous completed round for card reveal animations
        $prevRound = DragonTigerRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->first();

        $lastRoundData = null;
        if ($prevRound) {
            $lastRoundData = [
                'round_id' => $prevRound->round_id,
                'winner' => $prevRound->winner,
                'dragon_card' => $prevRound->dragon_card,
                'tiger_card' => $prevRound->tiger_card,
            ];
        }

        // Recent 50 rounds for Bead Plate / Road Map
        $roadmap = DragonTigerRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get(['round_id', 'winner', 'dragon_card', 'tiger_card'])
            ->map(function ($r) {
                return [
                    'round_id' => $r->round_id,
                    'winner' => $r->winner,
                    'd_rank' => $r->dragon_card['rank'] ?? '',
                    't_rank' => $r->tiger_card['rank'] ?? '',
                    'd_suit' => $r->dragon_card['suit'] ?? '',
                    't_suit' => $r->tiger_card['suit'] ?? '',
                ];
            });

        $myActiveBets = [];
        $balance = 0.00;
        $latestWin = null;

        if ($user) {
            $balance = (float) $user->balance;
            $myActiveBets = DragonTigerBet::where('user_id', $user->id)
                ->where('round_id', $roundData['round_id'])
                ->get();

            // Check if user won in the latest completed round
            if ($prevRound) {
                $wonBets = DragonTigerBet::where('user_id', $user->id)
                    ->where('round_id', $prevRound->round_id)
                    ->whereIn('status', ['won', 'refunded'])
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
        $history = DragonTigerRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'history' => $history,
        ]);
    }

    /**
     * Get logged-in user's Dragon Tiger bet history.
     */
    public function getMyBets(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $bets = DragonTigerBet::where('user_id', $user->id)
            ->with('round:round_id,winner,dragon_card,tiger_card')
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
     * Place a Dragon Tiger bet.
     */
    public function placeBet(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Please login to place bets!'], 401);
        }

        $validTypes = [
            'dragon', 'tiger', 'tie', 'suited_tie',
            'dragon_big', 'dragon_small', 'dragon_red', 'dragon_black', 'dragon_odd', 'dragon_even',
            'tiger_big', 'tiger_small', 'tiger_red', 'tiger_black', 'tiger_odd', 'tiger_even',
        ];

        $request->validate([
            'bet_type' => ['required', 'string', 'in:'.implode(',', $validTypes)],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $res = DragonTigerEngine::placeBet(
            $user,
            (string) $request->bet_type,
            (float) $request->amount
        );

        return response()->json($res, $res['success'] ? 200 : 422);
    }
}
