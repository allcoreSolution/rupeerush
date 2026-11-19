<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Services\VortexEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VortexController extends Controller
{
    /**
     * Show Vortex Official Game Arena.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $setting = AdminSetting::current();

        $activeGame = null;
        if ($user) {
            $activeModel = VortexEngine::getActiveGame($user);
            if ($activeModel) {
                $activeGame = VortexEngine::formatGameState($activeModel);
            }
        }

        $ringsConfig = VortexEngine::getRingsConfig();
        $recentGames = $user ? VortexEngine::getRecentGames($user, 25) : [];

        return view('games.vortex', [
            'user' => $user,
            'activeGame' => $activeGame,
            'setting' => $setting,
            'ringsConfig' => $ringsConfig,
            'recentGames' => $recentGames,
        ]);
    }

    /**
     * Get real-time status of current player's session & limits.
     */
    public function getStatus(Request $request): JsonResponse
    {
        $user = Auth::user();
        $setting = AdminSetting::current();

        $activeGame = null;
        $balance = 0.00;

        if ($user) {
            $balance = (float) $user->balance;
            $activeModel = VortexEngine::getActiveGame($user);
            if ($activeModel) {
                $activeGame = VortexEngine::formatGameState($activeModel);
            }
        }

        return response()->json([
            'success' => true,
            'balance' => $balance,
            'active_game' => $activeGame,
            'min_bet' => (float) ($setting->vortex_min_bet ?? 1.00),
            'max_bet' => (float) ($setting->vortex_max_bet ?? 25000.00),
            'rings_config' => VortexEngine::getRingsConfig(),
        ]);
    }

    /**
     * Start a new Vortex session.
     */
    public function startGame(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Please log in to play Vortex.'], 401);
        }

        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.1'],
        ]);

        $amount = round((float) $request->input('amount'), 2);
        $result = VortexEngine::startGame($user, $amount);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Spin the current concentric ring.
     */
    public function spin(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $result = VortexEngine::spin($user);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * 50% Partial Cashout ("Part PayOut").
     */
    public function partCashout(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $result = VortexEngine::partCashout($user);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Full Cashout current active session.
     */
    public function cashOut(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $result = VortexEngine::cashOut($user);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Get user's recent Vortex games history.
     */
    public function getHistory(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => true, 'history' => []]);
        }

        $history = VortexEngine::getRecentGames($user, 25);

        return response()->json([
            'success' => true,
            'history' => $history,
        ]);
    }
}
