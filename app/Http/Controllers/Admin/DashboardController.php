<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AndarBaharBet;
use App\Models\AviatorBet;
use App\Models\Bet;
use App\Models\DragonTigerBet;
use App\Models\HorseRaceBet;
use App\Models\MinesGame;
use App\Models\RouletteBet;
use App\Models\SportsBet;
use App\Models\TeenPattiBet;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VortexGame;
use App\Models\WingoBet;
use App\Services\LotteryEngine;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::now('Asia/Kolkata')->format('Y-m-d');

        // 1. Core Platform Statistics (Lifetime)
        $totalUsers = User::where('role', 'user')->count();
        $activeUsers = User::where('role', 'user')->where('status', 'active')->count();
        $totalUserBalances = (float) User::where('role', 'user')->sum('balance');

        $totalDeposits = (float) Transaction::where('type', 'deposit')->where('status', 'approved')->sum('amount');
        $approvedDepositsCount = Transaction::where('type', 'deposit')->where('status', 'approved')->count();
        $pendingDepositsCount = Transaction::where('type', 'deposit')->where('status', 'pending')->count();
        $pendingDepositsAmount = (float) Transaction::where('type', 'deposit')->where('status', 'pending')->sum('amount');

        $totalWithdrawals = (float) Transaction::where('type', 'withdrawal')->whereIn('status', ['approved', 'completed'])->sum('amount');
        $settledWithdrawalsCount = Transaction::where('type', 'withdrawal')->whereIn('status', ['approved', 'completed'])->count();
        $pendingWithdrawalsCount = Transaction::where('type', 'withdrawal')->where('status', 'pending')->count();
        $pendingWithdrawalsAmount = (float) Transaction::where('type', 'withdrawal')->where('status', 'pending')->sum('amount');

        $adjustedCredits = (float) Transaction::where('type', 'admin_adjustment')->where(function ($q) {
            $q->whereColumn('balance_after', '>', 'balance_before')->orWhere('admin_notes', 'like', 'Admin credit%');
        })->sum('amount');
        $adjustedDebits = (float) Transaction::where('type', 'admin_adjustment')->where(function ($q) {
            $q->whereColumn('balance_after', '<', 'balance_before')->orWhere('admin_notes', 'like', 'Admin debit%');
        })->sum('amount');
        $netAdjusted = $adjustedCredits - $adjustedDebits;
        $totalAdjustmentsCount = Transaction::where('type', 'admin_adjustment')->count();

        $totalBets = (float) Transaction::where('type', 'bet_placed')->sum('amount');
        $totalBetsCount = Transaction::where('type', 'bet_placed')->count();
        $totalWon = (float) Transaction::where('type', 'bet_won')->sum('amount');
        $totalWonCount = Transaction::where('type', 'bet_won')->count();
        $netGgr = $totalBets - $totalWon;
        $netCashflow = $totalDeposits - $totalWithdrawals;

        // 2. Today's Statistics
        $todayDeposits = (float) Transaction::whereDate('created_at', $today)->where('type', 'deposit')->where('status', 'approved')->sum('amount');
        $todayWithdrawals = (float) Transaction::whereDate('created_at', $today)->where('type', 'withdrawal')->whereIn('status', ['approved', 'completed'])->sum('amount');
        $todayBetsAmount = (float) Transaction::whereDate('created_at', $today)->where('type', 'bet_placed')->sum('amount');
        $todayBetsCount = Transaction::whereDate('created_at', $today)->where('type', 'bet_placed')->count();
        $todayPayoutsAmount = (float) Transaction::whereDate('created_at', $today)->where('type', 'bet_won')->sum('amount');
        $todayPayoutsCount = Transaction::whereDate('created_at', $today)->where('type', 'bet_won')->count();
        $todayNetProfit = $todayBetsAmount - $todayPayoutsAmount;

        // 3. Lower Sections Real Database Queries
        $recentDeposits = Transaction::with(['user:id,name,phone'])
            ->where('type', 'deposit')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get();

        $recentWithdrawals = Transaction::with(['user:id,name,phone'])
            ->where('type', 'withdrawal')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get();

        $recentWagers = Transaction::with(['user:id,name,phone'])
            ->where('type', 'bet_placed')
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();

        $recentWins = Transaction::with(['user:id,name,phone'])
            ->where('type', 'bet_won')
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get();

        // 4. Game-by-Game Live Performance Matrix
        $gamePerformance = [
            'lottery' => [
                'name' => '2-Digit Lottery',
                'icon' => 'fa-dice',
                'color' => '#0284c7',
                'total_bets' => Bet::count(),
                'total_wagered' => (float) Bet::sum('amount'),
                'total_payout' => (float) Bet::where('status', 'won')->sum('win_amount'),
            ],
            'wingo' => [
                'name' => 'WinGo Color',
                'icon' => 'fa-gamepad',
                'color' => '#8b5cf6',
                'total_bets' => WingoBet::count(),
                'total_wagered' => (float) WingoBet::sum('total_amount'),
                'total_payout' => (float) WingoBet::where('status', 'won')->sum('win_amount'),
            ],
            'dragon_tiger' => [
                'name' => 'Dragon Tiger',
                'icon' => 'fa-dragon',
                'color' => '#b91c1c',
                'total_bets' => DragonTigerBet::count(),
                'total_wagered' => (float) DragonTigerBet::sum('bet_amount'),
                'total_payout' => (float) DragonTigerBet::whereIn('status', ['won', 'refunded'])->sum('payout_amount'),
            ],
            'andar_bahar' => [
                'name' => 'Andar Bahar',
                'icon' => 'fa-diamond',
                'color' => '#f59e0b',
                'total_bets' => AndarBaharBet::count(),
                'total_wagered' => (float) AndarBaharBet::sum('amount'),
                'total_payout' => (float) AndarBaharBet::where('status', 'won')->sum('win_amount'),
            ],
            'aviator' => [
                'name' => 'Aviator Crash',
                'icon' => 'fa-plane-departure',
                'color' => '#e11d48',
                'total_bets' => AviatorBet::count(),
                'total_wagered' => (float) AviatorBet::sum('amount'),
                'total_payout' => (float) AviatorBet::where('status', 'cashed_out')->sum('win_amount'),
            ],
            'mines' => [
                'name' => 'Mines 5x5',
                'icon' => 'fa-bomb',
                'color' => '#10b981',
                'total_bets' => MinesGame::count(),
                'total_wagered' => (float) MinesGame::sum('bet_amount'),
                'total_payout' => (float) MinesGame::where('status', 'cashed_out')->sum('win_amount'),
            ],
            'vortex' => [
                'name' => 'Vortex Wheel',
                'icon' => 'fa-arrows-spin',
                'color' => '#06b6d4',
                'total_bets' => VortexGame::count(),
                'total_wagered' => (float) VortexGame::sum('bet_amount'),
                'total_payout' => (float) VortexGame::where('status', 'cashed_out')->sum('win_amount'),
            ],
            'teen_patti' => [
                'name' => '20-20 Teen Patti',
                'icon' => 'fa-heart',
                'color' => '#059669',
                'total_bets' => TeenPattiBet::count(),
                'total_wagered' => (float) TeenPattiBet::sum('bet_amount'),
                'total_payout' => (float) TeenPattiBet::where('status', 'won')->sum('payout_amount'),
            ],
            'roulette' => [
                'name' => 'Roulette 36x',
                'icon' => 'fa-dharmachakra',
                'color' => '#dc2626',
                'total_bets' => RouletteBet::count(),
                'total_wagered' => (float) RouletteBet::sum('bet_amount'),
                'total_payout' => (float) RouletteBet::where('status', 'won')->sum('payout_amount'),
            ],
            'horse_racing' => [
                'name' => 'Derby Racing',
                'icon' => 'fa-horse-head',
                'color' => '#15803d',
                'total_bets' => HorseRaceBet::count(),
                'total_wagered' => (float) HorseRaceBet::sum('bet_amount'),
                'total_payout' => (float) HorseRaceBet::where('status', 'won')->sum('payout_amount'),
            ],
            'sports' => [
                'name' => 'Sports & Cricket',
                'icon' => 'fa-baseball-bat-ball',
                'color' => '#ea580c',
                'total_bets' => SportsBet::count(),
                'total_wagered' => (float) SportsBet::sum('stake'),
                'total_payout' => (float) SportsBet::where('status', 'won')->sum('win_amount'),
            ],
        ];

        // Identify highest and lowest volume games for dynamic highlighting
        $maxBets = collect($gamePerformance)->max('total_bets');
        $minBets = collect($gamePerformance)->min('total_bets');

        foreach ($gamePerformance as $key => $g) {
            $gamePerformance[$key]['is_highest'] = ($maxBets > 0 && $g['total_bets'] === $maxBets);
            $gamePerformance[$key]['is_lowest'] = ($maxBets > $minBets && $g['total_bets'] === $minBets);
        }

        $timingStatus = LotteryEngine::getSlotScheduleStatus();

        return view('admin.dashboard', [
            // Platform totals
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'totalUserBalances' => $totalUserBalances,
            'totalDeposits' => $totalDeposits,
            'approvedDepositsCount' => $approvedDepositsCount,
            'pendingDepositsCount' => $pendingDepositsCount,
            'pendingDepositsAmount' => $pendingDepositsAmount,
            'totalWithdrawals' => $totalWithdrawals,
            'settledWithdrawalsCount' => $settledWithdrawalsCount,
            'pendingWithdrawalsCount' => $pendingWithdrawalsCount,
            'pendingWithdrawalsAmount' => $pendingWithdrawalsAmount,
            'adjustedCredits' => $adjustedCredits,
            'adjustedDebits' => $adjustedDebits,
            'netAdjusted' => $netAdjusted,
            'totalAdjustmentsCount' => $totalAdjustmentsCount,
            'totalBets' => $totalBets,
            'totalBetsCount' => $totalBetsCount,
            'totalWon' => $totalWon,
            'totalWonCount' => $totalWonCount,
            'netGgr' => $netGgr,
            'netCashflow' => $netCashflow,

            // Today's stats
            'todayDeposits' => $todayDeposits,
            'todayWithdrawals' => $todayWithdrawals,
            'todayBetsAmount' => $todayBetsAmount,
            'todayBetsCount' => $todayBetsCount,
            'todayPayoutsAmount' => $todayPayoutsAmount,
            'todayPayoutsCount' => $todayPayoutsCount,
            'todayNetProfit' => $todayNetProfit,

            // Lower Section Tables
            'recentDeposits' => $recentDeposits,
            'recentWithdrawals' => $recentWithdrawals,
            'recentWagers' => $recentWagers,
            'recentWins' => $recentWins,
            'gamePerformance' => $gamePerformance,
            'timingStatus' => $timingStatus,
        ]);
    }
}
