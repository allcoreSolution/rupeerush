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
use App\Models\VortexGame;
use App\Models\WingoBet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminBetController extends Controller
{
    /**
     * Display live bets list across all games (2-Digit Lottery, WinGo Color Game, Andar Bahar Live, Aviator Crash, Mines 5x5, Roulette 36x).
     */
    public function index(Request $request)
    {
        $game = $request->query('game', 'all');
        $status = $request->query('status', 'all');
        $date = $request->query('date', Carbon::now('Asia/Kolkata')->format('Y-m-d'));
        $search = $request->query('search');
        $filter = $request->query('filter', 'all');

        // Overall Stats for all 11 games
        $lotteryTotalBets = Bet::count();
        $lotteryTotalVolume = (float) Bet::sum('amount');
        $lotteryTotalPayout = (float) Bet::where('status', 'won')->sum('win_amount');

        $wingoTotalBets = WingoBet::count();
        $wingoTotalVolume = (float) WingoBet::sum('total_amount');
        $wingoTotalPayout = (float) WingoBet::where('status', 'won')->sum('win_amount');

        $andarTotalBets = AndarBaharBet::count();
        $andarTotalVolume = (float) AndarBaharBet::sum('amount');
        $andarTotalPayout = (float) AndarBaharBet::where('status', 'won')->sum('win_amount');

        $aviatorTotalBets = AviatorBet::count();
        $aviatorTotalVolume = (float) AviatorBet::sum('amount');
        $aviatorTotalPayout = (float) AviatorBet::where('status', 'cashed_out')->sum('win_amount');

        $minesTotalBets = MinesGame::count();
        $minesTotalVolume = (float) MinesGame::sum('bet_amount');
        $minesTotalPayout = (float) MinesGame::where('status', 'cashed_out')->sum('win_amount');

        $rouletteTotalBets = RouletteBet::count();
        $rouletteTotalVolume = (float) RouletteBet::sum('bet_amount');
        $rouletteTotalPayout = (float) RouletteBet::where('status', 'won')->sum('payout_amount');

        $horseRacingTotalBets = HorseRaceBet::count();
        $horseRacingTotalVolume = (float) HorseRaceBet::sum('bet_amount');
        $horseRacingTotalPayout = (float) HorseRaceBet::where('status', 'won')->sum('payout_amount');

        $dragonTigerTotalBets = DragonTigerBet::count();
        $dragonTigerTotalVolume = (float) DragonTigerBet::sum('bet_amount');
        $dragonTigerTotalPayout = (float) DragonTigerBet::where('status', 'won')->sum('payout_amount');

        $teenPattiTotalBets = TeenPattiBet::count();
        $teenPattiTotalVolume = (float) TeenPattiBet::sum('bet_amount');
        $teenPattiTotalPayout = (float) TeenPattiBet::where('status', 'won')->sum('payout_amount');

        $vortexTotalBets = VortexGame::count();
        $vortexTotalVolume = (float) VortexGame::sum('bet_amount');
        $vortexTotalPayout = (float) VortexGame::where('status', 'cashed_out')->sum('win_amount');

        $sportsTotalBets = SportsBet::count();
        $sportsTotalVolume = (float) SportsBet::sum('stake');
        $sportsTotalPayout = (float) SportsBet::where('status', 'won')->sum('win_amount');

        $lotteryCommission = (float) Bet::where('status', 'won')->sum('commission_amount');
        $wingoCommission = (float) WingoBet::where('status', 'won')->sum('commission_amount');
        $andarCommission = (float) AndarBaharBet::where('status', 'won')->sum('commission_amount');
        $aviatorCommission = (float) AviatorBet::where('status', 'cashed_out')->sum('commission_amount');
        $minesCommission = (float) MinesGame::where('status', 'cashed_out')->sum('commission_amount');
        $dragonTigerCommission = (float) DragonTigerBet::where('status', 'won')->sum('commission_amount');
        $teenPattiCommission = (float) TeenPattiBet::where('status', 'won')->sum('commission_amount');
        $vortexCommission = (float) VortexGame::where('status', 'cashed_out')->sum('commission_amount');
        $sportsCommission = (float) SportsBet::where('status', 'won')->sum('commission_amount');
        $totalCommission = $lotteryCommission + $wingoCommission + $andarCommission + $aviatorCommission + $minesCommission + $dragonTigerCommission + $teenPattiCommission + $vortexCommission + $sportsCommission;

        $totalBets = $lotteryTotalBets + $wingoTotalBets + $andarTotalBets + $aviatorTotalBets + $minesTotalBets + $rouletteTotalBets + $horseRacingTotalBets + $dragonTigerTotalBets + $teenPattiTotalBets + $vortexTotalBets + $sportsTotalBets;
        $totalVolume = $lotteryTotalVolume + $wingoTotalVolume + $andarTotalVolume + $aviatorTotalVolume + $minesTotalVolume + $rouletteTotalVolume + $horseRacingTotalVolume + $dragonTigerTotalVolume + $teenPattiTotalVolume + $vortexTotalVolume + $sportsTotalVolume;
        $totalPayout = $lotteryTotalPayout + $wingoTotalPayout + $andarTotalPayout + $aviatorTotalPayout + $minesTotalPayout + $rouletteTotalPayout + $horseRacingTotalPayout + $dragonTigerTotalPayout + $teenPattiTotalPayout + $vortexTotalPayout + $sportsTotalPayout;

        $stats = [
            'total_bets' => $totalBets,
            'total_volume' => $totalVolume,
            'total_payout' => $totalPayout,
            'total_commission' => $totalCommission,
            'net_profit' => $totalVolume - $totalPayout,
            'lottery_count' => $lotteryTotalBets,
            'lottery_volume' => $lotteryTotalVolume,
            'wingo_count' => $wingoTotalBets,
            'wingo_volume' => $wingoTotalVolume,
            'andar_count' => $andarTotalBets,
            'andar_volume' => $andarTotalVolume,
            'aviator_count' => $aviatorTotalBets,
            'aviator_volume' => $aviatorTotalVolume,
            'mines_count' => $minesTotalBets,
            'mines_volume' => $minesTotalVolume,
            'mines_payout' => $minesTotalPayout,
            'roulette_count' => $rouletteTotalBets,
            'roulette_volume' => $rouletteTotalVolume,
            'roulette_payout' => $rouletteTotalPayout,
            'horse_racing_count' => $horseRacingTotalBets,
            'horse_racing_volume' => $horseRacingTotalVolume,
            'horse_racing_payout' => $horseRacingTotalPayout,
            'dragon_tiger_count' => $dragonTigerTotalBets,
            'dragon_tiger_volume' => $dragonTigerTotalVolume,
            'dragon_tiger_payout' => $dragonTigerTotalPayout,
            'teen_patti_count' => $teenPattiTotalBets,
            'teen_patti_volume' => $teenPattiTotalVolume,
            'teen_patti_payout' => $teenPattiTotalPayout,
            'vortex_count' => $vortexTotalBets,
            'vortex_volume' => $vortexTotalVolume,
            'vortex_payout' => $vortexTotalPayout,
            'sports_count' => $sportsTotalBets,
            'sports_volume' => $sportsTotalVolume,
            'sports_payout' => $sportsTotalPayout,
        ];

        // Spotlight Calculations: Top Higher Amount, Top Color User, Top Number User
        $dateCondition = $request->filled('date') ? $date : null;

        $topLotteryBet = Bet::with('user')
            ->when($dateCondition, fn ($q) => $q->whereDate('created_at', $dateCondition))
            ->orderBy('amount', 'desc')
            ->first();

        $topWingoBet = WingoBet::with('user')
            ->when($dateCondition, fn ($q) => $q->whereDate('created_at', $dateCondition))
            ->orderBy('total_amount', 'desc')
            ->first();

        $topAndarBet = AndarBaharBet::with('user')
            ->when($dateCondition, fn ($q) => $q->whereDate('created_at', $dateCondition))
            ->orderBy('amount', 'desc')
            ->first();

        $topAviatorBet = AviatorBet::with('user')
            ->when($dateCondition, fn ($q) => $q->whereDate('created_at', $dateCondition))
            ->orderBy('amount', 'desc')
            ->first();

        $topMinesBet = MinesGame::with('user')
            ->when($dateCondition, fn ($q) => $q->whereDate('created_at', $dateCondition))
            ->orderBy('bet_amount', 'desc')
            ->first();

        $topRouletteBet = RouletteBet::with('user')
            ->when($dateCondition, fn ($q) => $q->whereDate('created_at', $dateCondition))
            ->orderBy('bet_amount', 'desc')
            ->first();

        $topHorseBet = HorseRaceBet::with('user')
            ->when($dateCondition, fn ($q) => $q->whereDate('created_at', $dateCondition))
            ->orderBy('bet_amount', 'desc')
            ->first();

        $topDragonTigerBet = DragonTigerBet::with('user')
            ->when($dateCondition, fn ($q) => $q->whereDate('created_at', $dateCondition))
            ->orderBy('bet_amount', 'desc')
            ->first();

        $topTeenPattiBet = TeenPattiBet::with('user')
            ->when($dateCondition, fn ($q) => $q->whereDate('created_at', $dateCondition))
            ->orderBy('bet_amount', 'desc')
            ->first();

        $topVortexBet = VortexGame::with('user')
            ->when($dateCondition, fn ($q) => $q->whereDate('created_at', $dateCondition))
            ->orderBy('bet_amount', 'desc')
            ->first();

        $topSportsBet = SportsBet::with('user')
            ->when($dateCondition, fn ($q) => $q->whereDate('created_at', $dateCondition))
            ->orderBy('stake', 'desc')
            ->first();

        $candidates = [];
        if ($topLotteryBet) {
            $candidates[] = [
                'amount' => (float) $topLotteryBet->amount,
                'user' => $topLotteryBet->user,
                'game' => '2-Digit Lottery ('.$topLotteryBet->slot_key.')',
                'selection' => '#'.$topLotteryBet->chosen_number,
            ];
        }
        if ($topWingoBet) {
            $candidates[] = [
                'amount' => (float) $topWingoBet->total_amount,
                'user' => $topWingoBet->user,
                'game' => 'WinGo '.strtoupper($topWingoBet->game_type),
                'selection' => ucfirst($topWingoBet->bet_type).': '.$topWingoBet->bet_selection,
            ];
        }
        if ($topAndarBet) {
            $candidates[] = [
                'amount' => (float) $topAndarBet->amount,
                'user' => $topAndarBet->user,
                'game' => 'Andar Bahar Live',
                'selection' => strtoupper($topAndarBet->bet_selection),
            ];
        }
        if ($topAviatorBet) {
            $candidates[] = [
                'amount' => (float) $topAviatorBet->amount,
                'user' => $topAviatorBet->user,
                'game' => 'Aviator Crash',
                'selection' => 'Slot '.$topAviatorBet->bet_slot,
            ];
        }
        if ($topMinesBet) {
            $candidates[] = [
                'amount' => (float) $topMinesBet->bet_amount,
                'user' => $topMinesBet->user,
                'game' => 'Mines 5x5 Live',
                'selection' => $topMinesBet->mines_count.' Mines ('.$topMinesBet->current_step.' Safe Coins)',
            ];
        }
        if ($topRouletteBet) {
            $candidates[] = [
                'amount' => (float) $topRouletteBet->bet_amount,
                'user' => $topRouletteBet->user,
                'game' => 'European Roulette 36x',
                'selection' => $topRouletteBet->selection_label ?? (ucfirst($topRouletteBet->bet_type).': '.$topRouletteBet->bet_value),
            ];
        }
        if ($topHorseBet) {
            $candidates[] = [
                'amount' => (float) $topHorseBet->bet_amount,
                'user' => $topHorseBet->user,
                'game' => 'Horse Racing Live',
                'selection' => strtoupper($topHorseBet->bet_type).': #'.$topHorseBet->horse_id.' '.$topHorseBet->horse_name,
            ];
        }
        if ($topDragonTigerBet) {
            $candidates[] = [
                'amount' => (float) $topDragonTigerBet->bet_amount,
                'user' => $topDragonTigerBet->user,
                'game' => 'Dragon Tiger Live',
                'selection' => strtoupper($topDragonTigerBet->bet_type),
            ];
        }
        if ($topTeenPattiBet) {
            $candidates[] = [
                'amount' => (float) $topTeenPattiBet->bet_amount,
                'user' => $topTeenPattiBet->user,
                'game' => 'Teen Patti 20-20',
                'selection' => str_replace('_', ' ', strtoupper($topTeenPattiBet->bet_type)),
            ];
        }
        if ($topVortexBet) {
            $candidates[] = [
                'amount' => (float) $topVortexBet->bet_amount,
                'user' => $topVortexBet->user,
                'game' => 'Vortex Wheel',
                'selection' => 'Ring '.$topVortexBet->current_ring.' ('.((float) $topVortexBet->current_multiplier).'x)',
            ];
        }
        if ($topSportsBet) {
            $candidates[] = [
                'amount' => (float) $topSportsBet->stake,
                'user' => $topSportsBet->user,
                'game' => 'Cricket Sportsbook',
                'selection' => strtoupper($topSportsBet->bet_type).': '.$topSportsBet->runner_name,
            ];
        }
        usort($candidates, fn ($a, $b) => $b['amount'] <=> $a['amount']);
        $topOverallBet = $candidates[0] ?? null;

        // Top Color User (Highest stake on WinGo Color)
        $topColorBet = WingoBet::with('user')
            ->where('bet_type', 'color')
            ->when($dateCondition, fn ($q) => $q->whereDate('created_at', $dateCondition))
            ->orderBy('total_amount', 'desc')
            ->first();

        $topColorUser = $topColorBet ? [
            'user' => $topColorBet->user,
            'amount' => $topColorBet->total_amount,
            'color' => ucfirst($topColorBet->bet_selection),
            'game' => 'WinGo '.strtoupper($topColorBet->game_type),
            'period' => $topColorBet->period_id,
        ] : null;

        // Top Number User (Highest stake on a specific Number across Lottery & WinGo)
        $topWingoNum = WingoBet::with('user')
            ->where('bet_type', 'number')
            ->when($dateCondition, fn ($q) => $q->whereDate('created_at', $dateCondition))
            ->orderBy('total_amount', 'desc')
            ->first();

        $topNumberUser = null;
        if ($topLotteryBet && $topWingoNum) {
            $topNumberUser = ($topLotteryBet->amount >= $topWingoNum->total_amount)
                ? ['user' => $topLotteryBet->user, 'amount' => $topLotteryBet->amount, 'number' => '#'.$topLotteryBet->chosen_number, 'game' => '2-Digit Lottery ('.$topLotteryBet->slot_key.')']
                : ['user' => $topWingoNum->user, 'amount' => $topWingoNum->total_amount, 'number' => '#'.$topWingoNum->bet_selection, 'game' => 'WinGo '.strtoupper($topWingoNum->game_type)];
        } elseif ($topLotteryBet) {
            $topNumberUser = ['user' => $topLotteryBet->user, 'amount' => $topLotteryBet->amount, 'number' => '#'.$topLotteryBet->chosen_number, 'game' => '2-Digit Lottery ('.$topLotteryBet->slot_key.')'];
        } elseif ($topWingoNum) {
            $topNumberUser = ['user' => $topWingoNum->user, 'amount' => $topWingoNum->total_amount, 'number' => '#'.$topWingoNum->bet_selection, 'game' => 'WinGo '.strtoupper($topWingoNum->game_type)];
        }

        $spotlight = [
            'topBet' => $topOverallBet,
            'topColorUser' => $topColorUser,
            'topNumberUser' => $topNumberUser,
        ];

        // 1. If only 2-Digit Lottery requested
        if ($game === 'lottery') {
            $query = Bet::with('user');

            if ($status !== 'all') {
                $query->where('status', $status);
            }
            if ($request->filled('date')) {
                $query->whereDate('created_at', $date);
            }
            if ($request->filled('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('chosen_number', 'like', "%{$search}%")
                        ->orWhere('slot_time', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            }

            if ($filter === 'higher_amount' || $filter === 'higher_no_user') {
                $query->orderBy('amount', 'desc');
            } elseif ($filter === 'higher_color_user') {
                $query->whereRaw('1 = 0');
            } else {
                $query->orderBy('id', 'desc');
            }

            $bets = $query->paginate(20)->withQueryString();

            return view('admin.bets.index', [
                'bets' => $bets,
                'stats' => $stats,
                'spotlight' => $spotlight,
                'game' => $game,
                'status' => $status,
                'filter' => $filter,
                'date' => $date,
                'search' => $search,
                'isUnified' => false,
                'activeGameType' => 'lottery',
            ]);
        }

        // 2. If only WinGo (or specific WinGo sub-mode) requested
        if (str_starts_with($game, 'wingo')) {
            $query = WingoBet::with('user');

            if ($game !== 'wingo') {
                $subType = str_replace('wingo_', '', $game);
                $query->where('game_type', $subType);
            }

            if ($status !== 'all') {
                $query->where('status', $status);
            }
            if ($request->filled('date')) {
                $query->whereDate('created_at', $date);
            }
            if ($request->filled('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('period_id', 'like', "%{$search}%")
                        ->orWhere('bet_selection', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            }

            if ($filter === 'higher_amount') {
                $query->orderBy('total_amount', 'desc');
            } elseif ($filter === 'higher_color_user') {
                $query->where('bet_type', 'color')->orderBy('total_amount', 'desc');
            } elseif ($filter === 'higher_no_user') {
                $query->where('bet_type', 'number')->orderBy('total_amount', 'desc');
            } else {
                $query->orderBy('id', 'desc');
            }

            $bets = $query->paginate(20)->withQueryString();

            return view('admin.bets.index', [
                'bets' => $bets,
                'stats' => $stats,
                'spotlight' => $spotlight,
                'game' => $game,
                'status' => $status,
                'filter' => $filter,
                'date' => $date,
                'search' => $search,
                'isUnified' => false,
                'activeGameType' => 'wingo',
            ]);
        }

        // 3. If only Andar Bahar requested
        if ($game === 'andar_bahar') {
            $query = AndarBaharBet::with('user');

            if ($status !== 'all') {
                $query->where('status', $status);
            }
            if ($request->filled('date')) {
                $query->whereDate('created_at', $date);
            }
            if ($request->filled('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('round_id', 'like', "%{$search}%")
                        ->orWhere('bet_selection', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            }

            if ($filter === 'higher_amount') {
                $query->orderBy('amount', 'desc');
            } elseif ($filter === 'higher_color_user' || $filter === 'higher_no_user') {
                $query->whereRaw('1 = 0');
            } else {
                $query->orderBy('id', 'desc');
            }

            $bets = $query->paginate(20)->withQueryString();

            return view('admin.bets.index', [
                'bets' => $bets,
                'stats' => $stats,
                'spotlight' => $spotlight,
                'game' => $game,
                'status' => $status,
                'filter' => $filter,
                'date' => $date,
                'search' => $search,
                'isUnified' => false,
                'activeGameType' => 'andar_bahar',
            ]);
        }

        // 4. If only Aviator requested
        if ($game === 'aviator') {
            $query = AviatorBet::with('user');

            if ($status !== 'all') {
                if ($status === 'won') {
                    $query->where('status', 'cashed_out');
                } else {
                    $query->where('status', $status);
                }
            }
            if ($request->filled('date')) {
                $query->whereDate('created_at', $date);
            }
            if ($request->filled('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('round_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            }

            if ($filter === 'higher_amount') {
                $query->orderBy('amount', 'desc');
            } elseif ($filter === 'higher_color_user' || $filter === 'higher_no_user') {
                $query->whereRaw('1 = 0');
            } else {
                $query->orderBy('id', 'desc');
            }

            $bets = $query->paginate(20)->withQueryString();

            return view('admin.bets.index', [
                'bets' => $bets,
                'stats' => $stats,
                'spotlight' => $spotlight,
                'game' => $game,
                'status' => $status,
                'filter' => $filter,
                'date' => $date,
                'search' => $search,
                'isUnified' => false,
                'activeGameType' => 'aviator',
            ]);
        }

        // 5. If only Mines requested
        if ($game === 'mines') {
            $query = MinesGame::with('user');

            if ($status !== 'all') {
                if ($status === 'won') {
                    $query->where('status', 'cashed_out');
                } elseif ($status === 'lost') {
                    $query->where('status', 'exploded');
                } elseif ($status === 'pending') {
                    $query->where('status', 'active');
                } else {
                    $query->where('status', $status);
                }
            }
            if ($request->filled('date')) {
                $query->whereDate('created_at', $date);
            }
            if ($request->filled('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('game_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            }

            if ($filter === 'higher_amount') {
                $query->orderBy('bet_amount', 'desc');
            } elseif ($filter === 'higher_color_user' || $filter === 'higher_no_user') {
                $query->whereRaw('1 = 0');
            } else {
                $query->orderBy('id', 'desc');
            }

            $bets = $query->paginate(20)->withQueryString();

            return view('admin.bets.index', [
                'bets' => $bets,
                'stats' => $stats,
                'spotlight' => $spotlight,
                'game' => $game,
                'status' => $status,
                'filter' => $filter,
                'date' => $date,
                'search' => $search,
                'isUnified' => false,
                'activeGameType' => 'mines',
            ]);
        }

        // 6. If only Roulette requested
        if ($game === 'roulette') {
            $query = RouletteBet::with('user', 'round');

            if ($status !== 'all') {
                $query->where('status', $status);
            }
            if ($request->filled('date')) {
                $query->whereDate('created_at', $date);
            }
            if ($request->filled('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('round_id', 'like', "%{$search}%")
                        ->orWhere('bet_value', 'like', "%{$search}%")
                        ->orWhere('bet_type', 'like', "%{$search}%")
                        ->orWhere('bet_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            }

            if ($filter === 'higher_amount') {
                $query->orderBy('bet_amount', 'desc');
            } elseif ($filter === 'higher_color_user') {
                $query->whereIn('bet_type', ['red', 'black'])->orderBy('bet_amount', 'desc');
            } elseif ($filter === 'higher_no_user') {
                $query->where('bet_type', 'straight')->orderBy('bet_amount', 'desc');
            } else {
                $query->orderBy('id', 'desc');
            }

            $bets = $query->paginate(20)->withQueryString();

            return view('admin.bets.index', [
                'bets' => $bets,
                'stats' => $stats,
                'spotlight' => $spotlight,
                'game' => $game,
                'status' => $status,
                'filter' => $filter,
                'date' => $date,
                'search' => $search,
                'isUnified' => false,
                'activeGameType' => 'roulette',
            ]);
        }

        // 7. If only Horse Racing requested
        if ($game === 'horse_racing') {
            $query = HorseRaceBet::with('user', 'round');

            if ($status !== 'all') {
                $query->where('status', $status);
            }
            if ($request->filled('date')) {
                $query->whereDate('created_at', $date);
            }
            if ($request->filled('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('round_id', 'like', "%{$search}%")
                        ->orWhere('horse_name', 'like', "%{$search}%")
                        ->orWhere('bet_type', 'like', "%{$search}%")
                        ->orWhere('bet_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            }

            if ($filter === 'higher_amount') {
                $query->orderBy('bet_amount', 'desc');
            } else {
                $query->orderBy('id', 'desc');
            }

            $bets = $query->paginate(20)->withQueryString();

            return view('admin.bets.index', [
                'bets' => $bets,
                'stats' => $stats,
                'spotlight' => $spotlight,
                'game' => $game,
                'status' => $status,
                'filter' => $filter,
                'date' => $date,
                'search' => $search,
                'isUnified' => false,
                'activeGameType' => 'horse_racing',
            ]);
        }

        // 8. If only Dragon Tiger requested
        if ($game === 'dragon_tiger') {
            $query = DragonTigerBet::with('user', 'round');

            if ($status !== 'all') {
                $query->where('status', $status);
            }
            if ($request->filled('date')) {
                $query->whereDate('created_at', $date);
            }
            if ($request->filled('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('round_id', 'like', "%{$search}%")
                        ->orWhere('bet_type', 'like', "%{$search}%")
                        ->orWhere('bet_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            }

            if ($filter === 'higher_amount') {
                $query->orderBy('bet_amount', 'desc');
            } else {
                $query->orderBy('id', 'desc');
            }

            $bets = $query->paginate(20)->withQueryString();

            return view('admin.bets.index', [
                'bets' => $bets,
                'stats' => $stats,
                'spotlight' => $spotlight,
                'game' => $game,
                'status' => $status,
                'filter' => $filter,
                'date' => $date,
                'search' => $search,
                'isUnified' => false,
                'activeGameType' => 'dragon_tiger',
            ]);
        }

        // 9. If only Teen Patti requested
        if ($game === 'teen_patti') {
            $query = TeenPattiBet::with('user', 'round');

            if ($status !== 'all') {
                $query->where('status', $status);
            }
            if ($request->filled('date')) {
                $query->whereDate('created_at', $date);
            }
            if ($request->filled('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('round_id', 'like', "%{$search}%")
                        ->orWhere('bet_type', 'like', "%{$search}%")
                        ->orWhere('bet_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            }

            if ($filter === 'higher_amount') {
                $query->orderBy('bet_amount', 'desc');
            } else {
                $query->orderBy('id', 'desc');
            }

            $bets = $query->paginate(20)->withQueryString();

            return view('admin.bets.index', [
                'bets' => $bets,
                'stats' => $stats,
                'spotlight' => $spotlight,
                'game' => $game,
                'status' => $status,
                'filter' => $filter,
                'date' => $date,
                'search' => $search,
                'isUnified' => false,
                'activeGameType' => 'teen_patti',
            ]);
        }

        // 10. If only Vortex requested
        if ($game === 'vortex') {
            $query = VortexGame::with('user');

            if ($status !== 'all') {
                if ($status === 'won') {
                    $query->where('status', 'cashed_out');
                } elseif ($status === 'lost') {
                    $query->where('status', 'busted');
                } elseif ($status === 'pending') {
                    $query->where('status', 'active');
                } else {
                    $query->where('status', $status);
                }
            }
            if ($request->filled('date')) {
                $query->whereDate('created_at', $date);
            }
            if ($request->filled('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('game_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            }

            if ($filter === 'higher_amount') {
                $query->orderBy('bet_amount', 'desc');
            } else {
                $query->orderBy('id', 'desc');
            }

            $bets = $query->paginate(20)->withQueryString();

            return view('admin.bets.index', [
                'bets' => $bets,
                'stats' => $stats,
                'spotlight' => $spotlight,
                'game' => $game,
                'status' => $status,
                'filter' => $filter,
                'date' => $date,
                'search' => $search,
                'isUnified' => false,
                'activeGameType' => 'vortex',
            ]);
        }

        // 11. If only Sports / Cricket requested
        if ($game === 'sports') {
            $query = SportsBet::with('user', 'match', 'market');

            if ($status !== 'all') {
                $query->where('status', $status);
            }
            if ($request->filled('date')) {
                $query->whereDate('created_at', $date);
            }
            if ($request->filled('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('bet_id', 'like', "%{$search}%")
                        ->orWhere('runner_name', 'like', "%{$search}%")
                        ->orWhere('bet_type', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            }

            if ($filter === 'higher_amount') {
                $query->orderBy('stake', 'desc');
            } else {
                $query->orderBy('id', 'desc');
            }

            $bets = $query->paginate(20)->withQueryString();

            return view('admin.bets.index', [
                'bets' => $bets,
                'stats' => $stats,
                'spotlight' => $spotlight,
                'game' => $game,
                'status' => $status,
                'filter' => $filter,
                'date' => $date,
                'search' => $search,
                'isUnified' => false,
                'activeGameType' => 'sports',
            ]);
        }

        // 12. Game is 'all' - Unified timeline across All 11 Games
        $lotteryQuery = Bet::with('user');
        $wingoQuery = WingoBet::with('user');
        $andarQuery = AndarBaharBet::with('user');
        $aviatorQuery = AviatorBet::with('user');
        $minesQuery = MinesGame::with('user');
        $rouletteQuery = RouletteBet::with('user', 'round');
        $horseRacingQuery = HorseRaceBet::with('user', 'round');
        $dragonTigerQuery = DragonTigerBet::with('user', 'round');
        $teenPattiQuery = TeenPattiBet::with('user', 'round');
        $vortexQuery = VortexGame::with('user');
        $sportsQuery = SportsBet::with('user', 'match', 'market');

        if ($status !== 'all') {
            $lotteryQuery->where('status', $status);
            $wingoQuery->where('status', $status);
            $andarQuery->where('status', $status);
            $rouletteQuery->where('status', $status);
            $horseRacingQuery->where('status', $status);
            $dragonTigerQuery->where('status', $status);
            $teenPattiQuery->where('status', $status);
            $sportsQuery->where('status', $status);
            if ($status === 'won') {
                $aviatorQuery->where('status', 'cashed_out');
                $minesQuery->where('status', 'cashed_out');
                $vortexQuery->where('status', 'cashed_out');
            } elseif ($status === 'lost') {
                $aviatorQuery->where('status', 'crashed');
                $minesQuery->where('status', 'exploded');
                $vortexQuery->where('status', 'busted');
            } elseif ($status === 'pending') {
                $aviatorQuery->where('status', 'pending');
                $minesQuery->where('status', 'active');
                $vortexQuery->where('status', 'active');
            } else {
                $aviatorQuery->where('status', $status);
                $minesQuery->where('status', $status);
                $vortexQuery->where('status', $status);
            }
        }
        if ($request->filled('date')) {
            $lotteryQuery->whereDate('created_at', $date);
            $wingoQuery->whereDate('created_at', $date);
            $andarQuery->whereDate('created_at', $date);
            $aviatorQuery->whereDate('created_at', $date);
            $minesQuery->whereDate('created_at', $date);
            $rouletteQuery->whereDate('created_at', $date);
            $horseRacingQuery->whereDate('created_at', $date);
            $dragonTigerQuery->whereDate('created_at', $date);
            $teenPattiQuery->whereDate('created_at', $date);
            $vortexQuery->whereDate('created_at', $date);
            $sportsQuery->whereDate('created_at', $date);
        }
        if ($request->filled('search')) {
            $lotteryQuery->where(function ($q) use ($search) {
                $q->where('chosen_number', 'like', "%{$search}%")
                    ->orWhere('slot_time', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
            $wingoQuery->where(function ($q) use ($search) {
                $q->where('period_id', 'like', "%{$search}%")
                    ->orWhere('bet_selection', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
            $andarQuery->where(function ($q) use ($search) {
                $q->where('round_id', 'like', "%{$search}%")
                    ->orWhere('bet_selection', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
            $aviatorQuery->where(function ($q) use ($search) {
                $q->where('round_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
            $minesQuery->where(function ($q) use ($search) {
                $q->where('game_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
            $rouletteQuery->where(function ($q) use ($search) {
                $q->where('round_id', 'like', "%{$search}%")
                    ->orWhere('bet_value', 'like', "%{$search}%")
                    ->orWhere('bet_type', 'like', "%{$search}%")
                    ->orWhere('bet_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
            $horseRacingQuery->where(function ($q) use ($search) {
                $q->where('round_id', 'like', "%{$search}%")
                    ->orWhere('horse_name', 'like', "%{$search}%")
                    ->orWhere('bet_type', 'like', "%{$search}%")
                    ->orWhere('bet_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
            $dragonTigerQuery->where(function ($q) use ($search) {
                $q->where('round_id', 'like', "%{$search}%")
                    ->orWhere('bet_type', 'like', "%{$search}%")
                    ->orWhere('bet_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
            $teenPattiQuery->where(function ($q) use ($search) {
                $q->where('round_id', 'like', "%{$search}%")
                    ->orWhere('bet_type', 'like', "%{$search}%")
                    ->orWhere('bet_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
            $vortexQuery->where(function ($q) use ($search) {
                $q->where('game_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
            $sportsQuery->where(function ($q) use ($search) {
                $q->where('bet_id', 'like', "%{$search}%")
                    ->orWhere('runner_name', 'like', "%{$search}%")
                    ->orWhere('bet_type', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        // Apply filters in unified view
        if ($filter === 'higher_amount') {
            $lotteryQuery->orderBy('amount', 'desc');
            $wingoQuery->orderBy('total_amount', 'desc');
            $andarQuery->orderBy('amount', 'desc');
            $aviatorQuery->orderBy('amount', 'desc');
            $minesQuery->orderBy('bet_amount', 'desc');
            $rouletteQuery->orderBy('bet_amount', 'desc');
            $horseRacingQuery->orderBy('bet_amount', 'desc');
            $dragonTigerQuery->orderBy('bet_amount', 'desc');
            $teenPattiQuery->orderBy('bet_amount', 'desc');
            $vortexQuery->orderBy('bet_amount', 'desc');
            $sportsQuery->orderBy('stake', 'desc');
        } elseif ($filter === 'higher_color_user') {
            $lotteryQuery->whereRaw('1 = 0');
            $andarQuery->whereRaw('1 = 0');
            $aviatorQuery->whereRaw('1 = 0');
            $minesQuery->whereRaw('1 = 0');
            $horseRacingQuery->whereRaw('1 = 0');
            $dragonTigerQuery->whereRaw('1 = 0');
            $teenPattiQuery->whereRaw('1 = 0');
            $vortexQuery->whereRaw('1 = 0');
            $sportsQuery->whereRaw('1 = 0');
            $wingoQuery->where('bet_type', 'color')->orderBy('total_amount', 'desc');
            $rouletteQuery->whereIn('bet_type', ['red', 'black'])->orderBy('bet_amount', 'desc');
        } elseif ($filter === 'higher_no_user') {
            $lotteryQuery->orderBy('amount', 'desc');
            $wingoQuery->where('bet_type', 'number')->orderBy('total_amount', 'desc');
            $andarQuery->whereRaw('1 = 0');
            $aviatorQuery->whereRaw('1 = 0');
            $minesQuery->whereRaw('1 = 0');
            $horseRacingQuery->whereRaw('1 = 0');
            $dragonTigerQuery->whereRaw('1 = 0');
            $teenPattiQuery->whereRaw('1 = 0');
            $vortexQuery->whereRaw('1 = 0');
            $sportsQuery->whereRaw('1 = 0');
            $rouletteQuery->where('bet_type', 'straight')->orderBy('bet_amount', 'desc');
        } else {
            $lotteryQuery->orderBy('id', 'desc');
            $wingoQuery->orderBy('id', 'desc');
            $andarQuery->orderBy('id', 'desc');
            $aviatorQuery->orderBy('id', 'desc');
            $minesQuery->orderBy('id', 'desc');
            $rouletteQuery->orderBy('id', 'desc');
            $horseRacingQuery->orderBy('id', 'desc');
            $dragonTigerQuery->orderBy('id', 'desc');
            $teenPattiQuery->orderBy('id', 'desc');
            $vortexQuery->orderBy('id', 'desc');
            $sportsQuery->orderBy('id', 'desc');
        }

        // Fetch top items from all games and sort uniformly
        $lotteryItems = $lotteryQuery->limit(250)->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'game_name' => '2-Digit Lottery',
                'game_code' => 'lottery',
                'badge_color' => 'primary',
                'user' => $item->user,
                'user_id' => $item->user_id,
                'period_identifier' => ($item->result_date ? $item->result_date->format('Y-m-d') : '').' ('.$item->slot_time.')',
                'bet_target' => 'Number: #'.$item->chosen_number,
                'bet_type' => 'number',
                'amount' => (float) $item->amount,
                'win_multiplier' => (float) $item->win_multiplier,
                'commission_amount' => (float) ($item->commission_amount ?? 0),
                'win_amount' => (float) $item->win_amount,
                'status' => $item->status,
                'created_at' => $item->created_at,
            ];
        });

        $wingoItems = $wingoQuery->limit(250)->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'game_name' => 'WinGo '.strtoupper($item->game_type),
                'game_code' => 'wingo_'.$item->game_type,
                'badge_color' => 'danger',
                'user' => $item->user,
                'user_id' => $item->user_id,
                'period_identifier' => '#'.$item->period_id,
                'bet_target' => ucfirst($item->bet_type).': '.$item->bet_selection,
                'bet_type' => $item->bet_type,
                'amount' => (float) $item->total_amount,
                'win_multiplier' => (float) $item->win_multiplier,
                'commission_amount' => (float) ($item->commission_amount ?? 0),
                'win_amount' => (float) $item->win_amount,
                'status' => $item->status,
                'created_at' => $item->created_at,
            ];
        });

        $andarItems = $andarQuery->limit(250)->get()->map(function ($item) {
            $targetLabel = in_array(strtolower($item->bet_selection), ['andar', 'bahar'])
                ? strtoupper($item->bet_selection)
                : $item->bet_selection.' Cards';

            return (object) [
                'id' => $item->id,
                'game_name' => 'Andar Bahar Live',
                'game_code' => 'andar_bahar',
                'badge_color' => 'warning text-dark',
                'user' => $item->user,
                'user_id' => $item->user_id,
                'period_identifier' => '#'.$item->round_id,
                'bet_target' => $targetLabel,
                'bet_type' => $item->bet_type,
                'amount' => (float) $item->amount,
                'win_multiplier' => (float) ($item->multiplier ?? $item->win_multiplier ?? 1.90),
                'commission_amount' => (float) ($item->commission_amount ?? 0),
                'win_amount' => (float) $item->win_amount,
                'status' => $item->status,
                'created_at' => $item->created_at,
            ];
        });

        $aviatorItems = $aviatorQuery->limit(250)->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'game_name' => 'Aviator Crash',
                'game_code' => 'aviator',
                'badge_color' => 'danger',
                'user' => $item->user,
                'user_id' => $item->user_id,
                'period_identifier' => '#'.$item->round_id,
                'bet_target' => 'Slot '.$item->bet_slot.($item->auto_cashout ? ' (Auto: '.$item->auto_cashout.'x)' : ''),
                'bet_type' => 'crash',
                'amount' => (float) $item->amount,
                'win_multiplier' => (float) ($item->cashout_multiplier ?? 0),
                'commission_amount' => (float) ($item->commission_amount ?? 0),
                'win_amount' => (float) $item->win_amount,
                'status' => ($item->status === 'cashed_out' ? 'won' : $item->status),
                'created_at' => $item->created_at,
            ];
        });

        $minesItems = $minesQuery->limit(250)->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'game_name' => 'Mines 5x5',
                'game_code' => 'mines',
                'badge_color' => 'success',
                'user' => $item->user,
                'user_id' => $item->user_id,
                'period_identifier' => '#'.substr($item->game_id, -8),
                'bet_target' => $item->mines_count.' Mines ('.$item->current_step.' Safe Coins)'.($item->is_jackpot_win ? ' [JACKPOT]' : ''),
                'bet_type' => 'arcade',
                'amount' => (float) $item->bet_amount,
                'win_multiplier' => (float) $item->current_multiplier,
                'commission_amount' => (float) ($item->commission_amount ?? 0),
                'win_amount' => (float) $item->win_amount,
                'status' => ($item->status === 'cashed_out' ? 'won' : ($item->status === 'exploded' ? 'lost' : 'pending')),
                'created_at' => $item->created_at,
            ];
        });

        $rouletteItems = $rouletteQuery->limit(250)->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'bet_id' => $item->bet_id,
                'game_name' => 'Roulette 36x',
                'game_code' => 'roulette',
                'badge_color' => 'warning text-dark',
                'user' => $item->user,
                'user_id' => $item->user_id,
                'period_identifier' => 'Round #'.$item->round_id,
                'bet_target' => $item->selection_label ?? (ucfirst($item->bet_type).': '.$item->bet_value),
                'bet_type' => $item->bet_type,
                'amount' => (float) $item->bet_amount,
                'win_multiplier' => (float) $item->odds,
                'commission_amount' => 0.00,
                'win_amount' => (float) $item->payout_amount,
                'status' => $item->status,
                'created_at' => $item->created_at,
            ];
        });

        $horseRacingItems = $horseRacingQuery->limit(250)->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'bet_id' => $item->bet_id,
                'game_name' => 'Horse Racing Live',
                'game_code' => 'horse_racing',
                'badge_color' => 'success text-white',
                'user' => $item->user,
                'user_id' => $item->user_id,
                'period_identifier' => 'Race #'.$item->round_id,
                'bet_target' => $item->selection_label ?? (strtoupper($item->bet_type).': #'.$item->horse_id.' '.$item->horse_name),
                'bet_type' => $item->bet_type,
                'amount' => (float) $item->bet_amount,
                'win_multiplier' => (float) $item->odds,
                'commission_amount' => 0.00,
                'win_amount' => (float) $item->payout_amount,
                'status' => $item->status,
                'created_at' => $item->created_at,
            ];
        });

        $dragonTigerItems = $dragonTigerQuery->limit(250)->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'bet_id' => $item->bet_id,
                'game_name' => 'Dragon Tiger Live',
                'game_code' => 'dragon_tiger',
                'badge_color' => 'danger text-white',
                'user' => $item->user,
                'user_id' => $item->user_id,
                'period_identifier' => 'Round #'.$item->round_id,
                'bet_target' => strtoupper($item->bet_type),
                'bet_type' => $item->bet_type,
                'amount' => (float) $item->bet_amount,
                'win_multiplier' => (float) $item->odds,
                'commission_amount' => (float) ($item->commission_amount ?? 0),
                'win_amount' => (float) $item->payout_amount,
                'status' => $item->status,
                'created_at' => $item->created_at,
            ];
        });

        $teenPattiItems = $teenPattiQuery->limit(250)->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'bet_id' => $item->bet_id,
                'game_name' => 'Teen Patti 20-20',
                'game_code' => 'teen_patti',
                'badge_color' => 'warning text-dark',
                'user' => $item->user,
                'user_id' => $item->user_id,
                'period_identifier' => 'Round #'.$item->round_id,
                'bet_target' => str_replace('_', ' ', strtoupper($item->bet_type)),
                'bet_type' => $item->bet_type,
                'amount' => (float) $item->bet_amount,
                'win_multiplier' => (float) $item->odds,
                'commission_amount' => (float) ($item->commission_amount ?? 0),
                'win_amount' => (float) $item->payout_amount,
                'status' => $item->status,
                'created_at' => $item->created_at,
            ];
        });

        $vortexItems = $vortexQuery->limit(250)->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'bet_id' => $item->game_id,
                'game_name' => 'Vortex Wheel',
                'game_code' => 'vortex',
                'badge_color' => 'info text-dark',
                'user' => $item->user,
                'user_id' => $item->user_id,
                'period_identifier' => '#'.substr($item->game_id, -8),
                'bet_target' => 'Ring '.$item->current_ring.' ('.((float) $item->current_multiplier).'x)',
                'bet_type' => 'wheel',
                'amount' => (float) $item->bet_amount,
                'win_multiplier' => (float) $item->current_multiplier,
                'commission_amount' => (float) ($item->commission_amount ?? 0),
                'win_amount' => (float) ($item->win_amount ?? $item->payout_amount ?? 0),
                'status' => ($item->status === 'cashed_out' ? 'won' : ($item->status === 'busted' ? 'lost' : 'pending')),
                'created_at' => $item->created_at,
            ];
        });

        $sportsItems = $sportsQuery->limit(250)->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'bet_id' => $item->bet_id,
                'game_name' => 'Cricket Sportsbook',
                'game_code' => 'sports',
                'badge_color' => 'primary',
                'user' => $item->user,
                'user_id' => $item->user_id,
                'period_identifier' => $item->match ? ($item->match->team_a.' vs '.$item->match->team_b) : ('Match #'.$item->match_id),
                'bet_target' => strtoupper($item->bet_type).': '.$item->runner_name,
                'bet_type' => $item->bet_type,
                'amount' => (float) $item->stake,
                'win_multiplier' => (float) $item->odds,
                'commission_amount' => (float) ($item->commission_amount ?? 0),
                'win_amount' => (float) ($item->win_amount ?? 0),
                'status' => $item->status,
                'created_at' => $item->created_at,
            ];
        });

        $merged = $lotteryItems->concat($wingoItems)->concat($andarItems)->concat($aviatorItems)->concat($minesItems)->concat($rouletteItems)->concat($horseRacingItems)->concat($dragonTigerItems)->concat($teenPattiItems)->concat($vortexItems)->concat($sportsItems);

        if (in_array($filter, ['higher_amount', 'higher_color_user', 'higher_no_user'], true)) {
            $merged = $merged->sortByDesc('amount')->values();
        } else {
            $merged = $merged->sortByDesc('created_at')->values();
        }

        $page = $request->query('page', 1);
        $perPage = 25;
        $sliced = $merged->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator($sliced, $merged->count(), $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return view('admin.bets.index', [
            'bets' => $paginator,
            'stats' => $stats,
            'spotlight' => $spotlight,
            'game' => $game,
            'status' => $status,
            'filter' => $filter,
            'date' => $date,
            'search' => $search,
            'isUnified' => true,
            'activeGameType' => 'all',
        ]);
    }
}
