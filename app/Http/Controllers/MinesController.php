<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Services\MinesEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MinesController extends Controller
{
    /**
     * Show Mines Casino Game Arena.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $setting = AdminSetting::current();

        $activeGame = null;
        if ($user) {
            $activeGameModel = MinesEngine::getActiveGame($user);
            if ($activeGameModel) {
                $activeGame = MinesEngine::formatGameState($activeGameModel, false);
            }
        }

        $defaultMines = 3;
        $defaultLadder = MinesEngine::getMultiplierLadder($defaultMines);
        $recentGames = $user ? MinesEngine::getRecentGames($user, 25) : [];

        return view('games.mines', [
            'user' => $user,
            'activeGame' => $activeGame,
            'setting' => $setting,
            'defaultMines' => $defaultMines,
            'defaultLadder' => $defaultLadder,
            'recentGames' => $recentGames,
        ]);
    }

    /**
     * Get multiplier ladder for a specific mines count (1-24).
     */
    public function getLadder(Request $request): JsonResponse
    {
        $minesCount = (int) $request->query('mines', 3);
        $minesCount = max(MinesEngine::MIN_MINES, min(MinesEngine::MAX_MINES, $minesCount));
        $ladder = MinesEngine::getMultiplierLadder($minesCount);

        return response()->json([
            'success' => true,
            'mines_count' => $minesCount,
            'safe_count' => MinesEngine::GRID_SIZE - $minesCount,
            'ladder' => $ladder,
        ]);
    }

    /**
     * Start a new Mines game session.
     */
    public function startGame(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Please log in to play Mines.'], 401);
        }

        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.1'],
            'mines_count' => ['required', 'integer', 'between:3,24'],
        ]);

        $amount = round((float) $request->input('amount'), 2);
        $minesCount = (int) $request->input('mines_count');

        $result = MinesEngine::startGame($user, $amount, $minesCount);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Reveal a tile on the 5x5 board.
     */
    public function revealTile(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'tile_index' => ['required', 'integer', 'between:0,24'],
        ]);

        $tileIndex = (int) $request->input('tile_index');
        $result = MinesEngine::revealTile($user, $tileIndex);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Cash out current active game session.
     */
    public function cashOut(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $result = MinesEngine::cashOut($user);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Get real-time status of current player's game & jackpot balance.
     */
    public function getStatus(Request $request): JsonResponse
    {
        $user = Auth::user();
        $setting = AdminSetting::current();

        $activeGame = null;
        $balance = 0.00;

        if ($user) {
            $balance = (float) $user->balance;
            $activeModel = MinesEngine::getActiveGame($user);
            if ($activeModel) {
                $activeGame = MinesEngine::formatGameState($activeModel, false);
            }
        }

        return response()->json([
            'success' => true,
            'balance' => $balance,
            'active_game' => $activeGame,
            'min_bet' => (float) ($setting->mines_min_bet ?? 1.00),
            'max_bet' => (float) ($setting->mines_max_bet ?? 25000.00),
        ]);
    }

    /**
     * Get user's recent Mines bets history.
     */
    public function getHistory(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => true, 'history' => []]);
        }

        $history = MinesEngine::getRecentGames($user, 20);

        return response()->json([
            'success' => true,
            'history' => $history,
        ]);
    }
}
