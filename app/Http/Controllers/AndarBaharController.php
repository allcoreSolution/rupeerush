<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\AndarBaharBet;
use App\Models\AndarBaharRound;
use App\Services\AndarBaharEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AndarBaharController extends Controller
{
    /**
     * Show Andar Bahar Casino Arena.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $roundData = AndarBaharEngine::getCurrentRound();
        $setting = AdminSetting::current();

        $recentHistory = AndarBaharRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        $andarCount = $recentHistory->where('winning_side', 'andar')->count();
        $baharCount = $recentHistory->where('winning_side', 'bahar')->count();
        $totalPast = max(1, $andarCount + $baharCount);
        $andarPercent = round(($andarCount / $totalPast) * 100);
        $baharPercent = 100 - $andarPercent;

        return view('games.andar_bahar', [
            'user' => $user,
            'roundData' => $roundData,
            'recentHistory' => $recentHistory,
            'andarPercent' => $andarPercent,
            'baharPercent' => $baharPercent,
            'setting' => $setting,
        ]);
    }

    /**
     * Realtime JSON status for live countdown, card dealing animation, and sync.
     */
    public function getLiveStatus(Request $request): JsonResponse
    {
        $roundData = AndarBaharEngine::getCurrentRound();
        $round = $roundData['round_model'];

        $recentHistory = AndarBaharRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(15)
            ->get()
            ->map(function ($r) {
                return [
                    'round_id' => $r->round_id,
                    'winning_side' => $r->winning_side,
                    'winning_card' => $r->winning_card,
                    'joker_card' => $r->joker_card,
                    'total_cards' => $r->total_cards_dealt,
                ];
            });

        $user = Auth::user();
        $myActiveBets = [];
        $balance = 0.00;
        $latestWin = null;

        if ($user) {
            $balance = (float) $user->balance;
            $myActiveBets = AndarBaharBet::where('user_id', $user->id)
                ->where('round_id', $roundData['round_id'])
                ->get();

            $prevRound = AndarBaharRound::where('status', 'completed')
                ->orderBy('id', 'desc')
                ->first();

            if ($prevRound) {
                $wonBets = AndarBaharBet::where('user_id', $user->id)
                    ->where('round_id', $prevRound->round_id)
                    ->where('status', 'won')
                    ->get();

                if ($wonBets->isNotEmpty()) {
                    $totalWon = $wonBets->sum('win_amount');
                    $latestWin = [
                        'round_id' => $prevRound->round_id,
                        'win_amount' => (float) $totalWon,
                        'winning_side' => $prevRound->winning_side,
                        'winning_card' => $prevRound->winning_card,
                    ];
                }
            }
        }

        // Only stream dealing cards array during locked/dealing phase or completed
        $dealtCards = [];
        $winningSide = null;
        $winningCard = null;

        if ($roundData['is_locked'] || $round->status === 'dealing' || $round->status === 'completed') {
            $dealtCards = $round->dealt_cards ?? [];
            $winningSide = $round->winning_side;
            $winningCard = $round->winning_card;
        }

        $setting = AdminSetting::current();

        return response()->json([
            'success' => true,
            'round_id' => $roundData['round_id'],
            'remaining_seconds' => $roundData['remaining_seconds'],
            'is_locked' => $roundData['is_locked'],
            'status' => $round->status,
            'joker_card' => $round->joker_card,
            'dealt_cards' => $dealtCards,
            'winning_side' => $winningSide,
            'winning_card' => $winningCard,
            'total_cards_dealt' => $round->total_cards_dealt,
            'balance' => $balance,
            'my_active_bets' => $myActiveBets,
            'recent_history' => $recentHistory,
            'latest_win' => $latestWin,
            'multipliers' => [
                'andar' => (float) ($setting->andar_multiplier ?? AndarBaharEngine::MAIN_MULTIPLIER),
                'bahar' => (float) ($setting->bahar_multiplier ?? AndarBaharEngine::MAIN_MULTIPLIER),
                'min_bet' => (float) ($setting->andar_bahar_min_bet ?? 10.00),
                'max_bet' => (float) ($setting->andar_bahar_max_bet ?? 50000.00),
                'range_1_5' => (float) ($setting->andar_bahar_range_1_5 ?? AndarBaharEngine::RANGE_MULTIPLIERS['1_5']),
                'range_6_10' => (float) ($setting->andar_bahar_range_6_10 ?? AndarBaharEngine::RANGE_MULTIPLIERS['6_10']),
                'range_11_15' => (float) ($setting->andar_bahar_range_11_15 ?? AndarBaharEngine::RANGE_MULTIPLIERS['11_15']),
                'range_16_25' => (float) ($setting->andar_bahar_range_16_25 ?? AndarBaharEngine::RANGE_MULTIPLIERS['16_25']),
                'range_26_plus' => (float) ($setting->andar_bahar_range_26_plus ?? AndarBaharEngine::RANGE_MULTIPLIERS['26_plus']),
            ],
        ]);
    }

    /**
     * Place bet on Andar Bahar.
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

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is suspended.',
            ], 403);
        }

        $validated = $request->validate([
            'bet_type' => ['nullable', 'string', 'in:main,cards_range'],
            'bet_selection' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $betType = $validated['bet_type'] ?? 'main';
        $selection = strtolower(trim($validated['bet_selection']));
        $amount = (float) $validated['amount'];

        $result = AndarBaharEngine::placeBet($user, $betType, $selection, $amount);

        if (! $result['success']) {
            return response()->json($result, 422);
        }

        return response()->json($result);
    }

    /**
     * Get user's past Andar Bahar bets history.
     */
    public function getMyBets(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'bets' => []], 401);
        }

        $bets = AndarBaharBet::where('user_id', $user->id)
            ->with('round')
            ->orderBy('id', 'desc')
            ->limit(30)
            ->get();

        $stats = [
            'total_bets' => AndarBaharBet::where('user_id', $user->id)->count(),
            'total_wagered' => (float) AndarBaharBet::where('user_id', $user->id)->sum('amount'),
            'total_won' => (float) AndarBaharBet::where('user_id', $user->id)->where('status', 'won')->sum('win_amount'),
        ];
        $stats['net_profit'] = round($stats['total_won'] - $stats['total_wagered'], 2);

        return response()->json([
            'success' => true,
            'bets' => $bets,
            'stats' => $stats,
        ]);
    }

    /**
     * Get rounds history road.
     */
    public function getHistory(Request $request): JsonResponse
    {
        $rounds = AndarBaharRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'success' => true,
            'rounds' => $rounds,
        ]);
    }
}
