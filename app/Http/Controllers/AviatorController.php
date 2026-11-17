<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\AviatorBet;
use App\Models\AviatorRound;
use App\Services\AviatorEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AviatorController extends Controller
{
    /**
     * Show Aviator Crash Game Arena.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $roundData = AviatorEngine::getCurrentRound();
        $setting = AdminSetting::current();
        $recentMultipliers = AviatorEngine::getRecentMultipliers(25);
        $initialRoomBets = $this->getRoomBets($roundData, $roundData['round_model']);

        return view('games.aviator', [
            'user' => $user,
            'roundData' => $roundData,
            'setting' => $setting,
            'recentMultipliers' => $recentMultipliers,
            'initialRoomBets' => $initialRoomBets,
        ]);
    }

    /**
     * Realtime JSON status for high-speed synchronization and countdown.
     */
    public function getLiveStatus(Request $request): JsonResponse
    {
        $roundData = AviatorEngine::getCurrentRound();
        $round = $roundData['round_model'];
        $setting = AdminSetting::current();

        $user = Auth::user();
        $balance = 0.00;
        $myActiveBets = [];
        $latestWin = null;

        if ($user) {
            $balance = (float) $user->balance;

            $myActiveBets = AviatorBet::where('user_id', $user->id)
                ->where('round_id', $roundData['round_id'])
                ->get()
                ->map(function ($bet) {
                    return [
                        'id' => $bet->id,
                        'bet_slot' => $bet->bet_slot,
                        'amount' => (float) $bet->amount,
                        'auto_cashout' => $bet->auto_cashout ? (float) $bet->auto_cashout : null,
                        'cashout_multiplier' => $bet->cashout_multiplier ? (float) $bet->cashout_multiplier : null,
                        'win_amount' => (float) $bet->win_amount,
                        'status' => $bet->status,
                    ];
                });

            // Check if player won in the immediately preceding completed round
            $prevRound = AviatorRound::where('status', 'completed')
                ->where('round_id', '!=', $roundData['round_id'])
                ->orderBy('id', 'desc')
                ->first();

            if ($prevRound) {
                $wonBets = AviatorBet::where('user_id', $user->id)
                    ->where('round_id', $prevRound->round_id)
                    ->where('status', 'cashed_out')
                    ->get();

                if ($wonBets->isNotEmpty()) {
                    $latestWin = [
                        'round_id' => $prevRound->round_id,
                        'win_amount' => (float) $wonBets->sum('win_amount'),
                        'cashout_multiplier' => (float) $wonBets->first()->cashout_multiplier,
                    ];
                }
            }
        }

        // Live room players active bets (real bets + round-seeded random players)
        $roomBets = $this->getRoomBets($roundData, $round);

        // Always send exact crash multiplier so client animation and crash point are 100% synchronized with zero overshoot
        $publicCrashMultiplier = (float) $round->crash_multiplier;

        return response()->json([
            'success' => true,
            'round_id' => $roundData['round_id'],
            'status' => $roundData['status'],
            'current_multiplier' => (float) $roundData['current_multiplier'],
            'crash_multiplier' => $publicCrashMultiplier,
            'is_manual' => (bool) $round->is_manual,
            'standing_manual_multiplier' => $setting->aviator_manual_multiplier ? (float) $setting->aviator_manual_multiplier : null,
            'flight_elapsed_seconds' => (float) $roundData['flight_elapsed_seconds'],
            'remaining_betting_seconds' => (float) $roundData['remaining_betting_seconds'],
            'betting_duration' => AviatorEngine::BETTING_DURATION,
            'remaining_cooldown_seconds' => (float) ($roundData['remaining_cooldown_seconds'] ?? 0.0),
            'crash_cooldown' => AviatorEngine::CRASH_COOLDOWN,
            'server_timestamp' => $roundData['server_timestamp'],
            'recent_multipliers' => AviatorEngine::getRecentMultipliers(20),
            'balance' => $balance,
            'my_active_bets' => $myActiveBets,
            'room_bets' => $roomBets,
            'latest_win' => $latestWin,
            'min_bet' => (float) ($setting->aviator_min_bet ?? 10.00),
            'max_bet' => (float) ($setting->aviator_max_bet ?? 50000.00),
        ]);
    }

    /**
     * Place bet on Aviator Slot 1 or Slot 2.
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
            'bet_slot' => ['required', 'integer', 'in:1,2'],
            'amount' => ['required', 'numeric', 'min:1'],
            'auto_cashout' => ['nullable', 'numeric', 'min:1.01'],
        ]);

        $betSlot = (int) $validated['bet_slot'];
        $amount = (float) $validated['amount'];
        $autoCashout = isset($validated['auto_cashout']) ? (float) $validated['auto_cashout'] : null;

        $result = AviatorEngine::placeBet($user, $betSlot, $amount, $autoCashout);

        if (! $result['success']) {
            return response()->json($result, 422);
        }

        return response()->json($result);
    }

    /**
     * Cancel an active bet before flight begins.
     */
    public function cancelBet(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'bet_id' => ['required', 'integer'],
        ]);

        $result = AviatorEngine::cancelBet($user, (int) $validated['bet_id']);

        if (! $result['success']) {
            return response()->json($result, 422);
        }

        return response()->json($result);
    }

    /**
     * Cash out during active flight.
     */
    public function cashOut(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to cash out.',
            ], 401);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is suspended.',
            ], 403);
        }

        $validated = $request->validate([
            'bet_slot' => ['required', 'integer', 'in:1,2'],
            'client_multiplier' => ['nullable', 'numeric', 'min:1.01'],
        ]);

        $betSlot = (int) $validated['bet_slot'];
        $clientMultiplier = isset($validated['client_multiplier']) ? (float) $validated['client_multiplier'] : null;
        $result = AviatorEngine::cashOut($user, $betSlot, $clientMultiplier);

        if (! $result['success']) {
            return response()->json($result, 422);
        }

        return response()->json($result);
    }

    /**
     * Get user's personal Aviator bets history (optimized fast query).
     */
    public function getMyBets(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'bets' => []], 401);
        }

        // Single-query lightning fast aggregate calculation
        $statsRaw = AviatorBet::where('user_id', $user->id)
            ->selectRaw('
                COUNT(*) as total_bets,
                COALESCE(SUM(amount), 0) as total_wagered,
                COALESCE(SUM(CASE WHEN status = "cashed_out" THEN win_amount ELSE 0 END), 0) as total_won
            ')
            ->first();

        $totalWagered = (float) ($statsRaw->total_wagered ?? 0);
        $totalWon = (float) ($statsRaw->total_won ?? 0);

        // Fetch user bets without unnecessary round join
        $bets = AviatorBet::where('user_id', $user->id)
            ->select(['id', 'round_id', 'bet_slot', 'amount', 'auto_cashout', 'cashout_multiplier', 'win_amount', 'status', 'created_at'])
            ->orderBy('id', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'success' => true,
            'bets' => $bets,
            'stats' => [
                'total_bets' => (int) ($statsRaw->total_bets ?? 0),
                'total_wagered' => $totalWagered,
                'total_won' => $totalWon,
                'net_profit' => round($totalWon - $totalWagered, 2),
            ],
        ]);
    }

    /**
     * Get recent completed rounds history.
     */
    public function getHistory(Request $request): JsonResponse
    {
        $rounds = AviatorRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(30)
            ->get(['id', 'round_id', 'crash_multiplier', 'start_time', 'end_time', 'created_at']);

        return response()->json([
            'success' => true,
            'rounds' => $rounds,
        ]);
    }

    /**
     * Get real active room bets combined with realistic round-seeded simulated players.
     *
     * @param  array<string, mixed>  $roundData
     * @param  AviatorRound  $round
     * @return array<int, array{id: int|string, player: string, bet_slot: int, amount: float, multiplier: float|null, win_amount: float, status: string}>
     */
    private function getRoomBets(array $roundData, $round): array
    {
        $roundId = (string) $roundData['round_id'];
        $roundStatus = (string) ($roundData['status'] ?? 'betting');
        $currentMultiplier = (float) ($roundData['current_multiplier'] ?? 1.00);
        $crashMultiplier = (float) ($round->crash_multiplier ?? 1.00);

        // 1. Real bets from database
        $realBets = AviatorBet::where('round_id', $roundId)
            ->with('user:id,name,phone')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($b) {
                $name = $b->user?->name ?? 'Player';
                if ($b->user?->phone) {
                    $name = substr($b->user->phone, 0, 3).'***'.substr($b->user->phone, -2);
                }

                return [
                    'id' => $b->id,
                    'player' => $name,
                    'bet_slot' => (int) $b->bet_slot,
                    'amount' => (float) $b->amount,
                    'multiplier' => $b->cashout_multiplier ? (float) $b->cashout_multiplier : null,
                    'win_amount' => (float) $b->win_amount,
                    'status' => $b->status,
                ];
            })
            ->toArray();

        // 2. Deterministically seed simulated players with round_id
        $simulatedCount = 24;
        $seed = crc32($roundId);
        mt_srand($seed);

        $names = [
            'Aarav', 'Rohit', 'Suresh', 'Priya', 'Ankit', 'Amit', 'Neha', 'Deepak',
            'Pooja', 'Sanjay', 'Kunal', 'Ajay', 'Sunita', 'Vishal', 'Ritu', 'Ravi',
            'Manish', 'Vikram', 'Rahul', 'Rajesh', 'Mohit', 'Karan', 'Gaurav', 'Vikas',
            'Sachin', 'Pankaj', 'Dinesh', 'Manoj', 'Akash', 'Sunny', 'Harsh', 'Arjun',
            'Rakesh', 'Anil', 'Mukesh', 'Simran', 'Kavita', 'Sunil', 'Naveen', 'Devendra',
            'Ashok', 'Pradeep', 'Chetan', 'Yash', 'Abhishek', 'Kartik', 'Sumit', 'Alok',
            'Varun', 'Nitin', 'Vijay', 'Hemant', 'Tarun', 'Bhavna', 'Meena', 'Divya',
        ];

        $commonAmounts = [20, 50, 100, 100, 150, 200, 200, 250, 300, 500, 500, 800, 1000, 1200, 1500, 2000, 2500, 5000];

        $simulatedBets = [];
        for ($i = 0; $i < $simulatedCount; $i++) {
            if ($i % 2 === 0) {
                $playerName = $names[mt_rand(0, count($names) - 1)].'***'.mt_rand(1, 9);
            } else {
                $prefixes = ['98', '97', '96', '94', '91', '88', '70', '85', '99'];
                $prefix = $prefixes[mt_rand(0, count($prefixes) - 1)];
                $playerName = $prefix.mt_rand(10, 99).'***'.mt_rand(10, 99);
            }

            $amount = (float) $commonAmounts[mt_rand(0, count($commonAmounts) - 1)];

            // Realistic target cashout distribution:
            // 40% between 1.12x and 1.85x
            // 35% between 1.85x and 3.50x
            // 15% between 3.50x and 8.00x
            // 10% between 8.00x and 25.00x
            $roll = mt_rand(1, 100);
            if ($roll <= 40) {
                $targetMult = round(mt_rand(112, 185) / 100, 2);
            } elseif ($roll <= 75) {
                $targetMult = round(mt_rand(185, 350) / 100, 2);
            } elseif ($roll <= 90) {
                $targetMult = round(mt_rand(350, 800) / 100, 2);
            } else {
                $targetMult = round(mt_rand(800, 2500) / 100, 2);
            }

            // Determine status based on current round phase
            if ($roundStatus === 'betting') {
                $status = 'waiting';
                $mult = null;
                $winAmt = 0.00;
            } elseif ($roundStatus === 'flying') {
                if ($currentMultiplier >= $targetMult) {
                    $status = 'cashed_out';
                    $mult = $targetMult;
                    $winAmt = round($amount * $targetMult, 2);
                } else {
                    $status = 'in_flight';
                    $mult = null;
                    $winAmt = 0.00;
                }
            } else {
                // crashed or completed
                if ($crashMultiplier >= $targetMult) {
                    $status = 'cashed_out';
                    $mult = $targetMult;
                    $winAmt = round($amount * $targetMult, 2);
                } else {
                    $status = 'lost';
                    $mult = null;
                    $winAmt = 0.00;
                }
            }

            $simulatedBets[] = [
                'id' => 'sim_'.$i,
                'player' => $playerName,
                'bet_slot' => 1,
                'amount' => $amount,
                'multiplier' => $mult,
                'win_amount' => $winAmt,
                'status' => $status,
            ];
        }

        // Reset global RNG seed
        mt_srand();

        return array_merge($realBets, $simulatedBets);
    }
}
