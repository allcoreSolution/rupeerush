<?php

namespace App\Http\Controllers;

use App\Models\SportsBet;
use App\Services\Sports\CricketEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CricketController extends Controller
{
    protected CricketEngine $engine;

    public function __construct(CricketEngine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Show Cricket Sportsbook & Exchange Arena
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $matches = $this->engine->getMatchesList('all');

        $liveMatches = array_values(array_filter($matches, function ($m) {
            return ! empty($m['is_live']) || ($m['status'] ?? '') === 'live';
        }));

        $upcomingMatches = array_values(array_filter($matches, function ($m) {
            return ($m['status'] ?? '') === 'upcoming' && empty($m['is_live']);
        }));

        $completedMatches = array_values(array_filter($matches, function ($m) {
            return ($m['status'] ?? '') === 'completed';
        }));

        $myBets = [];
        if ($user) {
            $myBets = $this->engine->getUserBets($user->id, 15);
        }

        return view('sports.cricket', [
            'user' => $user,
            'liveMatches' => $liveMatches,
            'upcomingMatches' => $upcomingMatches,
            'completedMatches' => $completedMatches,
            'myBets' => $myBets,
        ]);
    }

    /**
     * Show detailed match page with live scorecard, ball commentary, and Back/Lay markets.
     */
    public function showMatch(Request $request, int|string $id): View
    {
        $user = Auth::user();
        $match = $this->engine->getMatch($id);

        if (! $match) {
            abort(404, 'Cricket match not found.');
        }

        $myBets = [];
        if ($user) {
            $myBets = SportsBet::where('user_id', $user->id)
                ->where('match_id', $match->id)
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('sports.cricket_match', [
            'user' => $user,
            'match' => $match,
            'myBets' => $myBets,
        ]);
    }

    /**
     * Real-time API for live cricket matches list.
     */
    public function getLiveMatchesApi(Request $request): JsonResponse
    {
        $matches = $this->engine->getMatchesList($request->query('filter', 'all'));

        return response()->json([
            'success' => true,
            'matches' => $matches,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    /**
     * Real-time API for single match live details.
     */
    public function getMatchDetailsApi(Request $request, int|string $id): JsonResponse
    {
        $match = $this->engine->getMatch($id);

        if (! $match) {
            return response()->json(['success' => false, 'message' => 'Match not found'], 404);
        }

        $user = Auth::user();
        $myBets = [];
        if ($user) {
            $myBets = SportsBet::where('user_id', $user->id)
                ->where('match_id', $match->id)
                ->orderBy('id', 'desc')
                ->get();
        }

        return response()->json([
            'success' => true,
            'match' => $match,
            'user_balance' => $user ? (float) $user->balance : null,
            'my_bets' => $myBets,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    /**
     * Place real-money cricket bet with Back / Lay options.
     */
    public function placeBet(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Please login to place bets.'], 401);
        }

        $validated = $request->validate([
            'runner_id' => 'required|integer',
            'stake' => 'required|numeric|min:10|max:100000',
            'bet_type' => 'nullable|string|in:back,lay',
        ]);

        $result = $this->engine->placeBet(
            $user,
            (int) $validated['runner_id'],
            (float) $validated['stake'],
            $validated['bet_type'] ?? 'back'
        );

        $status = $result['success'] ? 200 : 422;

        return response()->json($result, $status);
    }

    /**
     * Fetch user's cricket betting history.
     */
    public function getMyBets(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'bets' => []]);
        }

        $bets = $this->engine->getUserBets($user->id, 25);

        return response()->json([
            'success' => true,
            'bets' => $bets,
            'balance' => (float) $user->balance,
        ]);
    }

    /**
     * Admin diagnostic test for CricAPI key and connection.
     */
    public function adminTestApi(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $result = $this->engine->getApiService()->testApiConnection();

        return response()->json($result);
    }

    /**
     * Admin force-sync matches from CricAPI into local database.
     */
    public function adminSyncApi(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $synced = $this->engine->syncMatches(true);

        return response()->json([
            'success' => true,
            'message' => 'Cricket matches synchronized successfully!',
            'synced_count' => count($synced),
        ]);
    }

    /**
     * Admin manual market settlement.
     */
    public function adminSettleMarket(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $validated = $request->validate([
            'market_id' => 'required|integer',
            'winning_runner_id' => 'required|integer',
        ]);

        $result = $this->engine->settleMarket(
            (int) $validated['market_id'],
            (int) $validated['winning_runner_id']
        );

        return response()->json($result);
    }
}
