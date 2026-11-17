<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\AndarBaharBet;
use App\Models\AviatorBet;
use App\Models\Bet;
use App\Models\DragonTigerBet;
use App\Models\GameSlot;
use App\Models\HorseRaceBet;
use App\Models\MinesGame;
use App\Models\RouletteBet;
use App\Models\TeenPattiBet;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VortexGame;
use App\Models\WingoBet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BetController extends Controller
{
    public function placeBet(Request $request)
    {
        $setting = AdminSetting::current();
        $minBet = (float) ($setting->lottery_min_bet ?? 10.00);
        $maxBet = (float) ($setting->lottery_max_bet ?? 10000.00);

        $validated = $request->validate([
            'slot_time' => ['required', 'string'],
            'slot_key' => ['required', 'in:XA,XB,XC,XD,XE,XF,XG,XH,XI,XJ'],
            'chosen_number' => ['required', 'string', 'regex:/^[0-9]{2}$/'],
            'amount' => ['required', 'numeric', 'min:'.$minBet, 'max:'.$maxBet],
        ], [
            'amount.min' => "Minimum bet amount is ₹{$minBet}.",
            'amount.max' => "Maximum bet amount is ₹{$maxBet}.",
        ]);

        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Please login to place bets.'], 401);
        }

        if ($user->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Your account is suspended.'], 403);
        }

        $lockMinutes = $setting->betting_lock_minutes ?? 2;
        $now = Carbon::now('Asia/Kolkata');
        $todayDate = $now->format('Y-m-d');

        // Check slot existence
        $slot = GameSlot::where('slot_time', $validated['slot_time'])->where('is_active', true)->first();
        if (! $slot) {
            return response()->json(['success' => false, 'message' => 'Invalid or inactive slot time.'], 422);
        }

        // Check if betting time is still open (must be before T - lockMinutes)
        $slotDateTime = Carbon::createFromFormat('Y-m-d h:i A', "$todayDate {$slot->slot_time}", 'Asia/Kolkata');
        $genDateTime = $slotDateTime->copy()->subMinutes($lockMinutes);

        if ($now->greaterThanOrEqualTo($genDateTime)) {
            return response()->json([
                'success' => false,
                'message' => "Betting for slot {$slot->slot_time} is CLOSED. Bets lock {$lockMinutes} minutes early.",
            ], 422);
        }

        $betAmount = (float) $validated['amount'];
        $winMultiplier = (float) ($setting->win_multiplier ?? 90.00);

        return DB::transaction(function () use ($user, $todayDate, $slot, $validated, $betAmount, $winMultiplier) {
            // Lock user row for balance check
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            if ($lockedUser->balance < $betAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient wallet balance. Please deposit funds first.',
                    'current_balance' => $lockedUser->balance,
                ], 422);
            }

            $balanceBefore = $lockedUser->balance;
            $balanceAfter = bcsub((string) $balanceBefore, (string) $betAmount, 2);
            $lockedUser->balance = $balanceAfter;
            $lockedUser->save();

            // Create Bet record
            $bet = Bet::create([
                'user_id' => $lockedUser->id,
                'result_date' => $todayDate,
                'slot_time' => $slot->slot_time,
                'slot_key' => $validated['slot_key'],
                'chosen_number' => $validated['chosen_number'],
                'amount' => $betAmount,
                'win_multiplier' => $winMultiplier,
                'win_amount' => 0.00,
                'status' => 'pending',
            ]);

            // Create Transaction record
            Transaction::create([
                'user_id' => $lockedUser->id,
                'type' => 'bet_placed',
                'amount' => $betAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => 'completed',
                'admin_notes' => "Placed Bet #{$bet->id} on {$validated['slot_key']} [{$validated['chosen_number']}] for {$slot->slot_time}",
            ]);

            return response()->json([
                'success' => true,
                'message' => "Bet placed successfully on {$validated['slot_key']} [{$validated['chosen_number']}] for ₹".number_format($betAmount, 2),
                'new_balance' => (float) $balanceAfter,
                'bet_id' => $bet->id,
            ]);
        });
    }

    public function myBets(Request $request)
    {
        $user = Auth::user();
        $game = $request->query('game', 'all');
        $status = $request->query('status', 'all');
        $tab = $request->query('tab', 'bets'); // 'bets' or 'transactions'
        $flow = $request->query('flow', 'all'); // 'all', 'credit', 'debit'

        // User stats across all games
        $lotteryBetsCount = Bet::where('user_id', $user->id)->count();
        $lotteryWagered = (float) Bet::where('user_id', $user->id)->sum('amount');
        $lotteryWon = (float) Bet::where('user_id', $user->id)->where('status', 'won')->sum('win_amount');

        $wingoBetsCount = WingoBet::where('user_id', $user->id)->count();
        $wingoWagered = (float) WingoBet::where('user_id', $user->id)->sum('total_amount');
        $wingoWon = (float) WingoBet::where('user_id', $user->id)->where('status', 'won')->sum('win_amount');

        $andarBetsCount = AndarBaharBet::where('user_id', $user->id)->count();
        $andarWagered = (float) AndarBaharBet::where('user_id', $user->id)->sum('amount');
        $andarWon = (float) AndarBaharBet::where('user_id', $user->id)->where('status', 'won')->sum('win_amount');

        $aviatorBetsCount = AviatorBet::where('user_id', $user->id)->count();
        $aviatorWagered = (float) AviatorBet::where('user_id', $user->id)->sum('amount');
        $aviatorWon = (float) AviatorBet::where('user_id', $user->id)->where('status', 'cashed_out')->sum('win_amount');

        $minesBetsCount = MinesGame::where('user_id', $user->id)->count();
        $minesWagered = (float) MinesGame::where('user_id', $user->id)->sum('bet_amount');
        $minesWon = (float) MinesGame::where('user_id', $user->id)->where('status', 'cashed_out')->sum('win_amount');

        $vortexBetsCount = VortexGame::where('user_id', $user->id)->count();
        $vortexWagered = (float) VortexGame::where('user_id', $user->id)->sum('bet_amount');
        $vortexWon = (float) VortexGame::where('user_id', $user->id)->where('status', 'cashed_out')->sum('win_amount');

        $teenPattiBetsCount = TeenPattiBet::where('user_id', $user->id)->count();
        $teenPattiWagered = (float) TeenPattiBet::where('user_id', $user->id)->sum('bet_amount');
        $teenPattiWon = (float) TeenPattiBet::where('user_id', $user->id)->where('status', 'won')->sum('payout_amount');

        $rouletteBetsCount = RouletteBet::where('user_id', $user->id)->count();
        $rouletteWagered = (float) RouletteBet::where('user_id', $user->id)->sum('bet_amount');
        $rouletteWon = (float) RouletteBet::where('user_id', $user->id)->where('status', 'won')->sum('payout_amount');

        $horseRacingBetsCount = HorseRaceBet::where('user_id', $user->id)->count();
        $horseRacingWagered = (float) HorseRaceBet::where('user_id', $user->id)->sum('bet_amount');
        $horseRacingWon = (float) HorseRaceBet::where('user_id', $user->id)->where('status', 'won')->sum('payout_amount');

        $dragonTigerBetsCount = DragonTigerBet::where('user_id', $user->id)->count();
        $dragonTigerWagered = (float) DragonTigerBet::where('user_id', $user->id)->sum('bet_amount');
        $dragonTigerWon = (float) DragonTigerBet::where('user_id', $user->id)->whereIn('status', ['won', 'refunded'])->sum('payout_amount');

        $totalCredits = (float) Transaction::where('user_id', $user->id)->whereIn('type', ['deposit', 'bet_won'])->sum('amount');
        $totalDebits = (float) Transaction::where('user_id', $user->id)->whereIn('type', ['withdrawal', 'bet_placed'])->sum('amount');

        $totalWagered = $lotteryWagered + $wingoWagered + $andarWagered + $aviatorWagered + $minesWagered + $vortexWagered + $teenPattiWagered + $rouletteWagered + $horseRacingWagered + $dragonTigerWagered;
        $totalWon = $lotteryWon + $wingoWon + $andarWon + $aviatorWon + $minesWon + $vortexWon + $teenPattiWon + $rouletteWon + $horseRacingWon + $dragonTigerWon;

        $stats = [
            'total_bets' => $lotteryBetsCount + $wingoBetsCount + $andarBetsCount + $aviatorBetsCount + $minesBetsCount + $vortexBetsCount + $teenPattiBetsCount + $rouletteBetsCount + $horseRacingBetsCount + $dragonTigerBetsCount,
            'total_wagered' => $totalWagered,
            'total_won' => $totalWon,
            'net_profit' => $totalWon - $totalWagered,
            'lottery_count' => $lotteryBetsCount,
            'lottery_wagered' => $lotteryWagered,
            'lottery_won' => $lotteryWon,
            'wingo_count' => $wingoBetsCount,
            'wingo_wagered' => $wingoWagered,
            'wingo_won' => $wingoWon,
            'andar_count' => $andarBetsCount,
            'andar_wagered' => $andarWagered,
            'andar_won' => $andarWon,
            'aviator_count' => $aviatorBetsCount,
            'aviator_wagered' => $aviatorWagered,
            'aviator_won' => $aviatorWon,
            'mines_count' => $minesBetsCount,
            'mines_wagered' => $minesWagered,
            'mines_won' => $minesWon,
            'vortex_count' => $vortexBetsCount,
            'vortex_wagered' => $vortexWagered,
            'vortex_won' => $vortexWon,
            'teen_patti_count' => $teenPattiBetsCount,
            'teen_patti_wagered' => $teenPattiWagered,
            'teen_patti_won' => $teenPattiWon,
            'roulette_count' => $rouletteBetsCount,
            'roulette_wagered' => $rouletteWagered,
            'roulette_won' => $rouletteWon,
            'horse_racing_count' => $horseRacingBetsCount,
            'horse_racing_wagered' => $horseRacingWagered,
            'horse_racing_won' => $horseRacingWon,
            'dragon_tiger_count' => $dragonTigerBetsCount,
            'dragon_tiger_wagered' => $dragonTigerWagered,
            'dragon_tiger_won' => $dragonTigerWon,
            'total_credits' => $totalCredits,
            'total_debits' => $totalDebits,
        ];

        // If user chose Debit & Credit tab
        if ($tab === 'transactions') {
            $txnQuery = Transaction::where('user_id', $user->id)->orderBy('id', 'desc');

            if ($flow === 'credit') {
                $txnQuery->whereIn('type', ['deposit', 'bet_won']);
            } elseif ($flow === 'debit') {
                $txnQuery->whereIn('type', ['withdrawal', 'bet_placed']);
            }

            if ($request->filled('type') && in_array($request->type, ['deposit', 'withdrawal', 'bet_placed', 'bet_won'], true)) {
                $txnQuery->where('type', $request->type);
            }

            $transactions = $txnQuery->paginate(20)->withQueryString();

            return view('bets.history', [
                'user' => $user,
                'stats' => $stats,
                'tab' => 'transactions',
                'flow' => $flow,
                'type' => $request->type ?? 'all',
                'transactions' => $transactions,
                'game' => $game,
                'status' => $status,
                'currentFilter' => $status,
            ]);
        }

        // 1. Lottery only
        if ($game === 'lottery') {
            $query = Bet::where('user_id', $user->id)->orderBy('id', 'desc');
            if ($status !== 'all' && in_array($status, ['pending', 'won', 'lost'], true)) {
                $query->where('status', $status);
            }
            $bets = $query->paginate(20)->withQueryString();

            return view('bets.history', [
                'user' => $user,
                'stats' => $stats,
                'tab' => 'bets',
                'game' => 'lottery',
                'status' => $status,
                'currentFilter' => $status,
                'bets' => $bets,
                'isUnified' => false,
            ]);
        }

        // 2. WinGo only
        if (str_starts_with($game, 'wingo')) {
            $query = WingoBet::where('user_id', $user->id)->orderBy('id', 'desc');
            if ($game !== 'wingo') {
                $subType = str_replace('wingo_', '', $game);
                $query->where('game_type', $subType);
            }
            if ($status !== 'all' && in_array($status, ['pending', 'won', 'lost'], true)) {
                $query->where('status', $status);
            }
            $bets = $query->paginate(20)->withQueryString();

            return view('bets.history', [
                'user' => $user,
                'stats' => $stats,
                'tab' => 'bets',
                'game' => $game,
                'status' => $status,
                'currentFilter' => $status,
                'bets' => $bets,
                'isUnified' => false,
            ]);
        }

        // 3. Andar Bahar only
        if ($game === 'andar_bahar') {
            $query = AndarBaharBet::where('user_id', $user->id)->orderBy('id', 'desc');
            if ($status !== 'all' && in_array($status, ['pending', 'won', 'lost'], true)) {
                $query->where('status', $status);
            }
            $bets = $query->paginate(20)->withQueryString();

            return view('bets.history', [
                'user' => $user,
                'stats' => $stats,
                'tab' => 'bets',
                'game' => 'andar_bahar',
                'status' => $status,
                'currentFilter' => $status,
                'bets' => $bets,
                'isUnified' => false,
            ]);
        }

        // 4. Aviator Crash only
        if ($game === 'aviator') {
            $query = AviatorBet::with('round')->where('user_id', $user->id)->orderBy('id', 'desc');
            if ($status !== 'all') {
                if ($status === 'won') {
                    $query->where('status', 'cashed_out');
                } elseif ($status === 'lost') {
                    $query->where('status', 'crashed');
                } elseif (in_array($status, ['pending', 'cancelled'], true)) {
                    $query->where('status', $status);
                }
            }
            $bets = $query->paginate(20)->withQueryString();

            return view('bets.history', [
                'user' => $user,
                'stats' => $stats,
                'tab' => 'bets',
                'game' => 'aviator',
                'status' => $status,
                'currentFilter' => $status,
                'bets' => $bets,
                'isUnified' => false,
            ]);
        }

        // 5. Mines Arcade only
        if ($game === 'mines') {
            $query = MinesGame::where('user_id', $user->id)->orderBy('id', 'desc');
            if ($status !== 'all') {
                if ($status === 'won') {
                    $query->where('status', 'cashed_out');
                } elseif ($status === 'lost') {
                    $query->where('status', 'exploded');
                } elseif (in_array($status, ['pending', 'active'], true)) {
                    $query->where('status', 'active');
                }
            }
            $bets = $query->paginate(20)->withQueryString();

            return view('bets.history', [
                'user' => $user,
                'stats' => $stats,
                'tab' => 'bets',
                'game' => 'mines',
                'status' => $status,
                'currentFilter' => $status,
                'bets' => $bets,
                'isUnified' => false,
            ]);
        }

        // 6. Vortex Concentric Wheel only
        if ($game === 'vortex') {
            $query = VortexGame::where('user_id', $user->id)->orderBy('id', 'desc');
            if ($status !== 'all') {
                if ($status === 'won') {
                    $query->where('status', 'cashed_out');
                } elseif ($status === 'lost') {
                    $query->where('status', 'busted');
                } elseif (in_array($status, ['pending', 'active'], true)) {
                    $query->where('status', 'active');
                }
            }
            $bets = $query->paginate(20)->withQueryString();

            return view('bets.history', [
                'user' => $user,
                'stats' => $stats,
                'tab' => 'bets',
                'game' => 'vortex',
                'status' => $status,
                'currentFilter' => $status,
                'bets' => $bets,
                'isUnified' => false,
            ]);
        }

        // 7. 20-20 Teen Patti only
        if ($game === 'teen_patti') {
            $query = TeenPattiBet::with('round')->where('user_id', $user->id)->orderBy('id', 'desc');
            if ($status !== 'all' && in_array($status, ['pending', 'won', 'lost', 'refunded'], true)) {
                $query->where('status', $status);
            }
            $bets = $query->paginate(20)->withQueryString();

            return view('bets.history', [
                'user' => $user,
                'stats' => $stats,
                'tab' => 'bets',
                'game' => 'teen_patti',
                'status' => $status,
                'currentFilter' => $status,
                'bets' => $bets,
                'isUnified' => false,
            ]);
        }

        // 8. European Roulette 36x only
        if ($game === 'roulette') {
            $query = RouletteBet::with('round')->where('user_id', $user->id)->orderBy('id', 'desc');
            if ($status !== 'all' && in_array($status, ['pending', 'won', 'lost'], true)) {
                $query->where('status', $status);
            }
            $bets = $query->paginate(20)->withQueryString();

            return view('bets.history', [
                'user' => $user,
                'stats' => $stats,
                'tab' => 'bets',
                'game' => 'roulette',
                'status' => $status,
                'currentFilter' => $status,
                'bets' => $bets,
                'isUnified' => false,
            ]);
        }

        // 9. Live Horse Racing Derby only
        if ($game === 'horse_racing') {
            $query = HorseRaceBet::with('round')->where('user_id', $user->id)->orderBy('id', 'desc');
            if ($status !== 'all' && in_array($status, ['pending', 'won', 'lost', 'refunded'], true)) {
                $query->where('status', $status);
            }
            $bets = $query->paginate(20)->withQueryString();

            return view('bets.history', [
                'user' => $user,
                'stats' => $stats,
                'tab' => 'bets',
                'game' => 'horse_racing',
                'status' => $status,
                'currentFilter' => $status,
                'bets' => $bets,
                'isUnified' => false,
            ]);
        }

        // 9b. Dragon Tiger Live only
        if ($game === 'dragon_tiger') {
            $query = DragonTigerBet::with('round')->where('user_id', $user->id)->orderBy('id', 'desc');
            if ($status !== 'all' && in_array($status, ['pending', 'won', 'lost', 'refunded'], true)) {
                $query->where('status', $status);
            }
            $bets = $query->paginate(20)->withQueryString();

            return view('bets.history', [
                'user' => $user,
                'stats' => $stats,
                'tab' => 'bets',
                'game' => 'dragon_tiger',
                'status' => $status,
                'currentFilter' => $status,
                'bets' => $bets,
                'isUnified' => false,
            ]);
        }

        // 10. All Games (Unified timeline)
        $lotteryQuery = Bet::where('user_id', $user->id);
        $wingoQuery = WingoBet::where('user_id', $user->id);
        $andarQuery = AndarBaharBet::where('user_id', $user->id);
        $aviatorQuery = AviatorBet::with('round')->where('user_id', $user->id);
        $minesQuery = MinesGame::where('user_id', $user->id);
        $vortexQuery = VortexGame::where('user_id', $user->id);
        $teenPattiQuery = TeenPattiBet::with('round')->where('user_id', $user->id);
        $rouletteQuery = RouletteBet::with('round')->where('user_id', $user->id);
        $horseRacingQuery = HorseRaceBet::with('round')->where('user_id', $user->id);
        $dragonTigerQuery = DragonTigerBet::with('round')->where('user_id', $user->id);

        if ($status !== 'all') {
            if ($status === 'won') {
                $lotteryQuery->where('status', 'won');
                $wingoQuery->where('status', 'won');
                $andarQuery->where('status', 'won');
                $aviatorQuery->where('status', 'cashed_out');
                $minesQuery->where('status', 'cashed_out');
                $vortexQuery->where('status', 'cashed_out');
                $teenPattiQuery->where('status', 'won');
                $rouletteQuery->where('status', 'won');
                $horseRacingQuery->where('status', 'won');
                $dragonTigerQuery->whereIn('status', ['won', 'refunded']);
            } elseif ($status === 'lost') {
                $lotteryQuery->where('status', 'lost');
                $wingoQuery->where('status', 'lost');
                $andarQuery->where('status', 'lost');
                $aviatorQuery->where('status', 'crashed');
                $minesQuery->where('status', 'exploded');
                $vortexQuery->where('status', 'busted');
                $teenPattiQuery->where('status', 'lost');
                $rouletteQuery->where('status', 'lost');
                $horseRacingQuery->where('status', 'lost');
                $dragonTigerQuery->where('status', 'lost');
            } elseif ($status === 'pending') {
                $lotteryQuery->where('status', 'pending');
                $wingoQuery->where('status', 'pending');
                $andarQuery->where('status', 'pending');
                $aviatorQuery->where('status', 'pending');
                $minesQuery->where('status', 'active');
                $vortexQuery->where('status', 'active');
                $teenPattiQuery->where('status', 'pending');
                $rouletteQuery->where('status', 'pending');
                $horseRacingQuery->where('status', 'pending');
                $dragonTigerQuery->where('status', 'pending');
            } elseif ($status === 'cancelled') {
                $lotteryQuery->whereRaw('1 = 0');
                $wingoQuery->whereRaw('1 = 0');
                $andarQuery->whereRaw('1 = 0');
                $aviatorQuery->where('status', 'cancelled');
                $minesQuery->whereRaw('1 = 0');
                $vortexQuery->whereRaw('1 = 0');
                $teenPattiQuery->whereRaw('1 = 0');
                $rouletteQuery->whereRaw('1 = 0');
                $horseRacingQuery->whereRaw('1 = 0');
                $dragonTigerQuery->whereRaw('1 = 0');
            }
        }

        $lotteryItems = $lotteryQuery->orderBy('id', 'desc')->limit(300)->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'game_name' => '2-Digit Lottery',
                'game_code' => 'lottery',
                'badge_class' => 'bg-primary',
                'icon' => 'fa-dice',
                'slot_time' => $item->slot_time,
                'slot_key' => $item->slot_key,
                'chosen_number' => $item->chosen_number,
                'round_info' => ($item->result_date ? $item->result_date->format('d M Y') : '').' • '.$item->slot_time,
                'selection_label' => 'Column '.$item->slot_key.' | #'.$item->chosen_number,
                'bet_type' => 'number',
                'amount' => (float) $item->amount,
                'win_multiplier' => (float) $item->win_multiplier,
                'win_amount' => (float) $item->win_amount,
                'commission_amount' => (float) ($item->commission_amount ?? 0),
                'status' => $item->status,
                'raw_status' => $item->status,
                'created_at' => $item->created_at,
            ];
        });

        $wingoItems = $wingoQuery->orderBy('id', 'desc')->limit(300)->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'game_name' => 'WinGo '.strtoupper($item->game_type),
                'game_code' => 'wingo_'.$item->game_type,
                'badge_class' => 'bg-danger',
                'icon' => 'fa-gamepad',
                'slot_time' => strtoupper($item->game_type),
                'slot_key' => ucfirst($item->bet_type),
                'chosen_number' => $item->bet_selection,
                'round_info' => 'Period #'.$item->period_id.' ('.strtoupper($item->game_type).')',
                'selection_label' => ucfirst($item->bet_type).': '.ucfirst($item->bet_selection),
                'bet_type' => $item->bet_type,
                'amount' => (float) $item->total_amount,
                'win_multiplier' => (float) $item->win_multiplier,
                'win_amount' => (float) $item->win_amount,
                'commission_amount' => (float) ($item->commission_amount ?? 0),
                'status' => $item->status,
                'raw_status' => $item->status,
                'created_at' => $item->created_at,
            ];
        });

        $andarItems = $andarQuery->orderBy('id', 'desc')->limit(300)->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'game_name' => 'Andar Bahar',
                'game_code' => 'andar_bahar',
                'badge_class' => 'bg-warning text-dark',
                'icon' => 'fa-diamond',
                'slot_time' => 'Round',
                'slot_key' => ucfirst($item->bet_type),
                'chosen_number' => strtoupper($item->bet_selection),
                'round_info' => 'Round #'.$item->round_id,
                'selection_label' => ucfirst($item->bet_type).': '.strtoupper($item->bet_selection),
                'bet_type' => $item->bet_type,
                'amount' => (float) $item->amount,
                'win_multiplier' => (float) ($item->multiplier ?? $item->win_multiplier ?? 1.90),
                'win_amount' => (float) $item->win_amount,
                'commission_amount' => (float) ($item->commission_amount ?? 0),
                'status' => $item->status,
                'raw_status' => $item->status,
                'created_at' => $item->created_at,
            ];
        });

        $aviatorItems = $aviatorQuery->orderBy('id', 'desc')->limit(300)->get()->map(function ($item) {
            $crashMultiplier = $item->round?->crash_multiplier ? number_format((float) $item->round->crash_multiplier, 2).'x' : null;
            $roundInfo = 'Round #'.$item->round_id.($crashMultiplier ? ' (Crashed @ '.$crashMultiplier.')' : '');
            $selectionLabel = 'Panel #'.$item->bet_slot.($item->auto_cashout ? ' • Auto: '.number_format((float) $item->auto_cashout, 2).'x' : ' • Manual');

            return (object) [
                'id' => $item->id,
                'game_name' => 'Aviator Crash',
                'game_code' => 'aviator',
                'badge_class' => 'bg-danger',
                'icon' => 'fa-plane-departure',
                'slot_time' => 'Crash Flight',
                'slot_key' => 'Panel #'.$item->bet_slot,
                'chosen_number' => $item->bet_slot,
                'round_info' => $roundInfo,
                'selection_label' => $selectionLabel,
                'bet_type' => 'crash',
                'amount' => (float) $item->amount,
                'win_multiplier' => (float) ($item->cashout_multiplier ?? 0),
                'win_amount' => (float) $item->win_amount,
                'commission_amount' => (float) ($item->commission_amount ?? 0),
                'status' => $item->status === 'cashed_out' ? 'won' : ($item->status === 'crashed' ? 'lost' : $item->status),
                'raw_status' => $item->status,
                'bet_slot' => $item->bet_slot,
                'round_id' => $item->round_id,
                'auto_cashout' => $item->auto_cashout,
                'cashout_multiplier' => $item->cashout_multiplier,
                'round_crash_multiplier' => $item->round?->crash_multiplier,
                'cashed_out_at' => $item->cashed_out_at,
                'created_at' => $item->created_at,
            ];
        });

        $minesItems = $minesQuery->orderBy('id', 'desc')->limit(300)->get()->map(function ($item) {
            $isWon = ($item->status === 'cashed_out');
            $statusNormalized = $isWon ? 'won' : ($item->status === 'exploded' ? 'lost' : 'pending');
            $selectionLabel = $item->mines_count.' Mines • '.$item->current_step.' Coins'.($item->is_jackpot_win ? ' [JACKPOT!]' : '');

            return (object) [
                'id' => $item->id,
                'game_name' => 'Mines 5x5',
                'game_code' => 'mines',
                'badge_class' => 'bg-success',
                'icon' => 'fa-bomb',
                'slot_time' => $item->mines_count.' Mines',
                'slot_key' => $item->current_step.' Coins',
                'chosen_number' => $item->current_multiplier.'x',
                'round_info' => 'Game #'.substr($item->game_id, -6),
                'selection_label' => $selectionLabel,
                'bet_type' => 'arcade',
                'amount' => (float) $item->bet_amount,
                'win_multiplier' => (float) $item->current_multiplier,
                'win_amount' => (float) $item->win_amount,
                'commission_amount' => (float) ($item->commission_amount ?? 0),
                'status' => $statusNormalized,
                'raw_status' => $item->status,
                'created_at' => $item->created_at,
            ];
        });

        $vortexItems = $vortexQuery->orderBy('id', 'desc')->limit(300)->get()->map(function ($item) {
            $isWon = ($item->status === 'cashed_out');
            $statusNormalized = $isWon ? 'won' : ($item->status === 'busted' ? 'lost' : 'pending');
            $ringName = $item->current_ring == 1 ? 'Water 🌊' : ($item->current_ring == 2 ? 'Earth 🦠' : 'Fire 🔥');
            $selectionLabel = 'Ring '.$item->current_ring.' ('.$ringName.')'.($item->part_cashed_out ? ' [50% Part Pay]' : '');

            return (object) [
                'id' => $item->id,
                'game_name' => 'Vortex Wheel',
                'game_code' => 'vortex',
                'game_id' => $item->game_id,
                'badge_class' => 'bg-info',
                'icon' => 'fa-arrows-spin',
                'slot_time' => 'Ring '.$item->current_ring,
                'slot_key' => $ringName,
                'chosen_number' => $item->current_multiplier.'x',
                'round_info' => 'Game #'.substr($item->game_id, -8),
                'selection_label' => $selectionLabel,
                'bet_type' => 'wheel',
                'amount' => (float) $item->bet_amount,
                'win_multiplier' => (float) $item->current_multiplier,
                'win_amount' => (float) $item->win_amount,
                'commission_amount' => (float) ($item->commission_amount ?? 0),
                'status' => $statusNormalized,
                'raw_status' => $item->status,
                'current_ring' => $item->current_ring,
                'spin_count' => $item->spin_count,
                'part_cashed_out' => $item->part_cashed_out,
                'part_cashout_amount' => (float) $item->part_cashout_amount,
                'cashed_out_at' => $item->cashed_out_at,
                'created_at' => $item->created_at,
            ];
        });

        $teenPattiItems = $teenPattiQuery->orderBy('id', 'desc')->limit(300)->get()->map(function ($item) {
            $typeLabelMap = [
                'player_a' => 'Player A',
                'player_b' => 'Player B',
                'tie' => 'Tie',
                'pair_plus_a' => 'Pair Plus A',
                'pair_plus_b' => 'Pair Plus B',
            ];
            $label = $typeLabelMap[$item->bet_type] ?? strtoupper($item->bet_type);

            return (object) [
                'id' => $item->id,
                'game_name' => '20-20 Teen Patti',
                'game_code' => 'teen_patti',
                'badge_class' => 'bg-danger',
                'icon' => 'fa-heart',
                'slot_time' => 'Round #'.$item->round_id,
                'slot_key' => $label,
                'chosen_number' => $item->odds.'X',
                'round_info' => 'Round #'.$item->round_id.($item->round?->winning_rank_title ? ' ('.$item->round->winning_rank_title.')' : ''),
                'selection_label' => $label.' ('.$item->odds.'X)',
                'bet_type' => $item->bet_type,
                'amount' => (float) $item->bet_amount,
                'win_multiplier' => (float) $item->odds,
                'win_amount' => (float) $item->payout_amount,
                'commission_amount' => 0.00,
                'status' => $item->status,
                'raw_status' => $item->status,
                'round_id' => $item->round_id,
                'round' => $item->round,
                'created_at' => $item->created_at,
            ];
        });

        $rouletteItems = $rouletteQuery->orderBy('id', 'desc')->limit(300)->get()->map(function ($item) {
            $typeLabelMap = [
                'straight' => 'Straight #'.$item->bet_value,
                'red' => 'RED (🔴)',
                'black' => 'BLACK (⚫)',
                'even' => 'EVEN',
                'odd' => 'ODD',
                'low' => 'LOW (1-18)',
                'high' => 'HIGH (19-36)',
                'dozen1' => '1st 12 (1-12)',
                'dozen2' => '2nd 12 (13-24)',
                'dozen3' => '3rd 12 (25-36)',
                'col1' => '1st Column',
                'col2' => '2nd Column',
                'col3' => '3rd Column',
                'split' => 'Split ('.$item->bet_value.')',
                'street' => 'Street ('.$item->bet_value.')',
                'corner' => 'Corner ('.$item->bet_value.')',
                'line' => 'Line ('.$item->bet_value.')',
            ];
            $selectionLabel = $typeLabelMap[$item->bet_type] ?? (ucfirst($item->bet_type ?: 'Bet').': '.$item->bet_value);

            return (object) [
                'id' => $item->id,
                'bet_id' => $item->bet_id,
                'game_name' => 'Roulette 36x',
                'game_code' => 'roulette',
                'badge_class' => 'bg-warning text-dark',
                'icon' => 'fa-dharmachakra',
                'slot_time' => 'Round #'.$item->round_id,
                'slot_key' => ucfirst($item->bet_type),
                'chosen_number' => $item->bet_value,
                'round_info' => 'Round #'.$item->round_id.(! is_null($item->round?->winning_number) ? ' (#'.$item->round->winning_number.' '.strtoupper($item->round->winning_color ?? '').')' : ''),
                'selection_label' => $selectionLabel,
                'bet_type' => $item->bet_type,
                'amount' => (float) $item->bet_amount,
                'win_multiplier' => (float) $item->odds,
                'win_amount' => (float) $item->payout_amount,
                'commission_amount' => 0.00,
                'status' => $item->status,
                'raw_status' => $item->status,
                'round_id' => $item->round_id,
                'round' => $item->round,
                'created_at' => $item->created_at,
            ];
        });

        $horseRacingItems = $horseRacingQuery->orderBy('id', 'desc')->limit(300)->get()->map(function ($item) {
            $betTypeLabel = strtoupper($item->bet_type);
            $selectionLabel = "{$betTypeLabel}: #{$item->horse_id} {$item->horse_name}";

            return (object) [
                'id' => $item->id,
                'bet_id' => $item->bet_id,
                'game_name' => 'Horse Racing Live',
                'game_code' => 'horse_racing',
                'badge_class' => 'bg-success',
                'icon' => 'fa-horse-racing',
                'slot_time' => 'Round #'.$item->round_id,
                'slot_key' => $betTypeLabel,
                'chosen_number' => '#'.$item->horse_id,
                'round_info' => 'Race #'.$item->round_id.(! is_null($item->round?->winner_horse_id) ? ' (Winner: #'.$item->round->winner_horse_id.')' : ''),
                'selection_label' => $selectionLabel,
                'bet_type' => $item->bet_type,
                'horse_id' => $item->horse_id,
                'horse_name' => $item->horse_name,
                'odds' => (float) $item->odds,
                'amount' => (float) $item->bet_amount,
                'win_multiplier' => (float) $item->odds,
                'win_amount' => (float) $item->payout_amount,
                'commission_amount' => 0.00,
                'status' => $item->status,
                'raw_status' => $item->status,
                'round_id' => $item->round_id,
                'round' => $item->round,
                'created_at' => $item->created_at,
            ];
        });

        $dragonTigerItems = $dragonTigerQuery->orderBy('id', 'desc')->limit(300)->get()->map(function ($item) {
            $typeLabel = strtoupper(str_replace('_', ' ', $item->bet_type));

            return (object) [
                'id' => $item->id,
                'bet_id' => $item->bet_id,
                'game_name' => 'Dragon Tiger Live',
                'game_code' => 'dragon_tiger',
                'badge_class' => 'bg-danger',
                'icon' => 'fa-dragon',
                'slot_time' => 'Round #'.$item->round_id,
                'slot_key' => $typeLabel,
                'chosen_number' => $item->odds.'X',
                'round_info' => 'Round #'.$item->round_id.(! empty($item->hand_outcome) ? ' ('.$item->hand_outcome.')' : ''),
                'selection_label' => $typeLabel.' ('.$item->odds.'X)',
                'bet_type' => $item->bet_type,
                'odds' => (float) $item->odds,
                'amount' => (float) $item->bet_amount,
                'win_multiplier' => (float) $item->odds,
                'win_amount' => (float) $item->payout_amount,
                'commission_amount' => 0.00,
                'status' => $item->status,
                'raw_status' => $item->status,
                'round_id' => $item->round_id,
                'round' => $item->round,
                'created_at' => $item->created_at,
            ];
        });

        $merged = $lotteryItems->concat($wingoItems)->concat($andarItems)->concat($aviatorItems)->concat($minesItems)->concat($vortexItems)->concat($teenPattiItems)->concat($rouletteItems)->concat($horseRacingItems)->concat($dragonTigerItems)->sortByDesc('created_at')->values();

        $page = (int) $request->query('page', 1);
        $perPage = 20;
        $sliced = $merged->slice(($page - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator(
            $sliced,
            $merged->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('bets.history', [
            'user' => $user,
            'stats' => $stats,
            'tab' => 'bets',
            'game' => 'all',
            'status' => $status,
            'currentFilter' => $status,
            'bets' => $paginated,
            'isUnified' => true,
        ]);
    }
}
