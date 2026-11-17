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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PlayerLedgerController extends Controller
{
    /**
     * Display a master list of all players with their aggregated financial statistics.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $sortBy = (string) $request->query('sort', 'id_desc');

        $query = User::where('role', 'user')
            ->withSum(['transactions as total_deposited' => function ($q) {
                $q->where('type', 'deposit')->where('status', 'approved');
            }], 'amount')
            ->withSum(['transactions as total_withdrawn' => function ($q) {
                $q->where('type', 'withdrawal')->whereIn('status', ['approved', 'completed']);
            }], 'amount')
            ->withSum(['transactions as total_adjusted_credits' => function ($q) {
                $q->where('type', 'admin_adjustment')->where(function ($sq) {
                    $sq->whereColumn('balance_after', '>', 'balance_before')
                        ->orWhere('admin_notes', 'like', 'Admin credit%');
                });
            }], 'amount')
            ->withSum(['transactions as total_adjusted_debits' => function ($q) {
                $q->where('type', 'admin_adjustment')->where(function ($sq) {
                    $sq->whereColumn('balance_after', '<', 'balance_before')
                        ->orWhere('admin_notes', 'like', 'Admin debit%');
                });
            }], 'amount')
            ->withSum(['transactions as total_bet' => function ($q) {
                $q->where('type', 'bet_placed');
            }], 'amount')
            ->withSum(['transactions as total_won' => function ($q) {
                $q->where('type', 'bet_won');
            }], 'amount')
            ->withCount('transactions');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        // Apply sorting
        match ($sortBy) {
            'balance_desc' => $query->orderBy('balance', 'desc'),
            'balance_asc' => $query->orderBy('balance', 'asc'),
            'deposited_desc' => $query->orderBy('total_deposited', 'desc'),
            'withdrawn_desc' => $query->orderBy('total_withdrawn', 'desc'),
            'bet_desc' => $query->orderBy('total_bet', 'desc'),
            'won_desc' => $query->orderBy('total_won', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            default => $query->orderBy('id', 'desc'),
        };

        $players = $query->paginate(20)->withQueryString();

        // Platform-wide player summary metrics
        $globalStats = [
            'total_players' => User::where('role', 'user')->count(),
            'active_players' => User::where('role', 'user')->where('status', 'active')->count(),
            'total_player_balance' => (float) User::where('role', 'user')->sum('balance'),
            'total_deposits' => (float) Transaction::where('type', 'deposit')->where('status', 'approved')->sum('amount'),
            'approved_deposits_count' => Transaction::where('type', 'deposit')->where('status', 'approved')->count(),
            'total_withdrawals' => (float) Transaction::where('type', 'withdrawal')->whereIn('status', ['approved', 'completed'])->sum('amount'),
            'settled_withdrawals_count' => Transaction::where('type', 'withdrawal')->whereIn('status', ['approved', 'completed'])->count(),
            'total_adjusted_credits' => (float) Transaction::where('type', 'admin_adjustment')->where(function ($sq) {
                $sq->whereColumn('balance_after', '>', 'balance_before')->orWhere('admin_notes', 'like', 'Admin credit%');
            })->sum('amount'),
            'total_adjusted_debits' => (float) Transaction::where('type', 'admin_adjustment')->where(function ($sq) {
                $sq->whereColumn('balance_after', '<', 'balance_before')->orWhere('admin_notes', 'like', 'Admin debit%');
            })->sum('amount'),
            'total_adjustments_count' => Transaction::where('type', 'admin_adjustment')->count(),
            'total_bets' => (float) Transaction::where('type', 'bet_placed')->sum('amount'),
            'total_bets_count' => Transaction::where('type', 'bet_placed')->count(),
            'total_wins' => (float) Transaction::where('type', 'bet_won')->sum('amount'),
            'total_wins_count' => Transaction::where('type', 'bet_won')->count(),
        ];
        $globalStats['net_adjusted'] = $globalStats['total_adjusted_credits'] - $globalStats['total_adjusted_debits'];
        $globalStats['net_ggr'] = $globalStats['total_bets'] - $globalStats['total_wins'];

        return view('admin.ledger.index', [
            'players' => $players,
            'search' => $search,
            'sortBy' => $sortBy,
            'globalStats' => $globalStats,
        ]);
    }

    /**
     * Display a detailed bank-statement style ledger for a single player.
     */
    public function show(Request $request, $id)
    {
        $user = User::where('role', 'user')->findOrFail($id);

        // Core Financial Summaries
        $depositApproved = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'deposit')
            ->where('status', 'approved')
            ->sum('amount');
        $depositCount = Transaction::where('user_id', $user->id)
            ->where('type', 'deposit')
            ->where('status', 'approved')
            ->count();
        $depositPending = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'deposit')
            ->where('status', 'pending')
            ->sum('amount');
        $depositPendingCount = Transaction::where('user_id', $user->id)
            ->where('type', 'deposit')
            ->where('status', 'pending')
            ->count();

        $withdrawalApproved = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->whereIn('status', ['approved', 'completed'])
            ->sum('amount');
        $withdrawalCount = Transaction::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->whereIn('status', ['approved', 'completed'])
            ->count();
        $withdrawalPending = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->sum('amount');
        $withdrawalPendingCount = Transaction::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->count();

        $totalBet = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'bet_placed')
            ->sum('amount');
        $betCount = Transaction::where('user_id', $user->id)
            ->where('type', 'bet_placed')
            ->count();

        $totalWon = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'bet_won')
            ->sum('amount');
        $wonCount = Transaction::where('user_id', $user->id)
            ->where('type', 'bet_won')
            ->count();

        $adminCredits = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'admin_adjustment')
            ->where(function ($q) {
                $q->whereColumn('balance_after', '>', 'balance_before')
                    ->orWhere('admin_notes', 'like', 'Admin credit%');
            })
            ->sum('amount');
        $adminCreditCount = Transaction::where('user_id', $user->id)
            ->where('type', 'admin_adjustment')
            ->where(function ($q) {
                $q->whereColumn('balance_after', '>', 'balance_before')
                    ->orWhere('admin_notes', 'like', 'Admin credit%');
            })
            ->count();

        $adminDebits = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'admin_adjustment')
            ->where(function ($q) {
                $q->whereColumn('balance_after', '<', 'balance_before')
                    ->orWhere('admin_notes', 'like', 'Admin debit%');
            })
            ->sum('amount');
        $adminDebitCount = Transaction::where('user_id', $user->id)
            ->where('type', 'admin_adjustment')
            ->where(function ($q) {
                $q->whereColumn('balance_after', '<', 'balance_before')
                    ->orWhere('admin_notes', 'like', 'Admin debit%');
            })
            ->count();

        $adminNetAdjusted = $adminCredits - $adminDebits;
        $adminAdjustmentCount = $adminCreditCount + $adminDebitCount;
        $totalRawAdjustments = Transaction::where('user_id', $user->id)->where('type', 'admin_adjustment')->count();
        if ($adminAdjustmentCount === 0 && $totalRawAdjustments > 0) {
            $adminAdjustmentCount = $totalRawAdjustments;
        }

        // Net P/L: Positive means Player won more than wagered; Negative means Player lost
        $netPL = $totalWon - $totalBet;
        $netCashflow = $depositApproved - $withdrawalApproved;

        // Breakdown by Game
        $gameStats = [
            'lottery' => [
                'name' => '2-Digit Lottery',
                'icon' => 'fa-dice',
                'color' => '#0284c7',
                'bets' => Bet::where('user_id', $user->id)->count(),
                'wagered' => (float) Bet::where('user_id', $user->id)->sum('amount'),
                'won' => (float) Bet::where('user_id', $user->id)->where('status', 'won')->sum('win_amount'),
            ],
            'wingo' => [
                'name' => 'WinGo Color',
                'icon' => 'fa-gamepad',
                'color' => '#8b5cf6',
                'bets' => WingoBet::where('user_id', $user->id)->count(),
                'wagered' => (float) WingoBet::where('user_id', $user->id)->sum('total_amount'),
                'won' => (float) WingoBet::where('user_id', $user->id)->where('status', 'won')->sum('win_amount'),
            ],
            'andar_bahar' => [
                'name' => 'Andar Bahar',
                'icon' => 'fa-diamond',
                'color' => '#f59e0b',
                'bets' => AndarBaharBet::where('user_id', $user->id)->count(),
                'wagered' => (float) AndarBaharBet::where('user_id', $user->id)->sum('amount'),
                'won' => (float) AndarBaharBet::where('user_id', $user->id)->where('status', 'won')->sum('win_amount'),
            ],
            'aviator' => [
                'name' => 'Aviator Crash',
                'icon' => 'fa-plane-departure',
                'color' => '#e11d48',
                'bets' => AviatorBet::where('user_id', $user->id)->count(),
                'wagered' => (float) AviatorBet::where('user_id', $user->id)->sum('amount'),
                'won' => (float) AviatorBet::where('user_id', $user->id)->where('status', 'cashed_out')->sum('win_amount'),
            ],
            'mines' => [
                'name' => 'Mines 5x5',
                'icon' => 'fa-bomb',
                'color' => '#10b981',
                'bets' => MinesGame::where('user_id', $user->id)->count(),
                'wagered' => (float) MinesGame::where('user_id', $user->id)->sum('bet_amount'),
                'won' => (float) MinesGame::where('user_id', $user->id)->where('status', 'cashed_out')->sum('win_amount'),
            ],
            'vortex' => [
                'name' => 'Vortex Wheel',
                'icon' => 'fa-arrows-spin',
                'color' => '#06b6d4',
                'bets' => VortexGame::where('user_id', $user->id)->count(),
                'wagered' => (float) VortexGame::where('user_id', $user->id)->sum('bet_amount'),
                'won' => (float) VortexGame::where('user_id', $user->id)->where('status', 'cashed_out')->sum('win_amount'),
            ],
            'teen_patti' => [
                'name' => '20-20 Teen Patti',
                'icon' => 'fa-heart',
                'color' => '#059669',
                'bets' => TeenPattiBet::where('user_id', $user->id)->count(),
                'wagered' => (float) TeenPattiBet::where('user_id', $user->id)->sum('bet_amount'),
                'won' => (float) TeenPattiBet::where('user_id', $user->id)->where('status', 'won')->sum('payout_amount'),
            ],
            'roulette' => [
                'name' => 'Roulette 36x',
                'icon' => 'fa-dharmachakra',
                'color' => '#dc2626',
                'bets' => RouletteBet::where('user_id', $user->id)->count(),
                'wagered' => (float) RouletteBet::where('user_id', $user->id)->sum('bet_amount'),
                'won' => (float) RouletteBet::where('user_id', $user->id)->where('status', 'won')->sum('payout_amount'),
            ],
            'horse_racing' => [
                'name' => 'Derby Racing',
                'icon' => 'fa-horse-head',
                'color' => '#15803d',
                'bets' => HorseRaceBet::where('user_id', $user->id)->count(),
                'wagered' => (float) HorseRaceBet::where('user_id', $user->id)->sum('bet_amount'),
                'won' => (float) HorseRaceBet::where('user_id', $user->id)->where('status', 'won')->sum('payout_amount'),
            ],
            'dragon_tiger' => [
                'name' => 'Dragon Tiger',
                'icon' => 'fa-dragon',
                'color' => '#b91c1c',
                'bets' => DragonTigerBet::where('user_id', $user->id)->count(),
                'wagered' => (float) DragonTigerBet::where('user_id', $user->id)->sum('bet_amount'),
                'won' => (float) DragonTigerBet::where('user_id', $user->id)->whereIn('status', ['won', 'refunded'])->sum('payout_amount'),
            ],
            'sports' => [
                'name' => 'Sports & Cricket',
                'icon' => 'fa-baseball-bat-ball',
                'color' => '#ea580c',
                'bets' => SportsBet::where('user_id', $user->id)->count(),
                'wagered' => (float) SportsBet::where('user_id', $user->id)->sum('stake'),
                'won' => (float) SportsBet::where('user_id', $user->id)->where('status', 'won')->sum('win_amount'),
            ],
        ];

        // Statement Query with Filters
        $type = (string) $request->query('type', 'all');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $search = trim((string) $request->query('search', ''));

        $statementQuery = Transaction::where('user_id', $user->id)
            ->with('reviewer:id,name')
            ->orderBy('id', 'desc');

        if ($type !== 'all' && in_array($type, ['deposit', 'withdrawal', 'bet_placed', 'bet_won', 'admin_adjustment'], true)) {
            $statementQuery->where('type', $type);
        }

        if ($dateFrom) {
            $statementQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $statementQuery->whereDate('created_at', '<=', $dateTo);
        }

        if ($search !== '') {
            $statementQuery->where(function ($q) use ($search) {
                $q->where('admin_notes', 'like', "%{$search}%")
                    ->orWhere('utr_number', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        $transactions = $statementQuery->paginate(30)->withQueryString();

        // Type counts for quick-filter tabs
        $typeCounts = [
            'all' => Transaction::where('user_id', $user->id)->count(),
            'deposit' => Transaction::where('user_id', $user->id)->where('type', 'deposit')->count(),
            'withdrawal' => Transaction::where('user_id', $user->id)->where('type', 'withdrawal')->count(),
            'admin_adjustment' => $adminAdjustmentCount,
            'bet_placed' => $betCount,
            'bet_won' => $wonCount,
        ];

        $stats = [
            'deposit_approved' => $depositApproved,
            'deposit_count' => $depositCount,
            'deposit_pending' => $depositPending,
            'deposit_pending_count' => $depositPendingCount,
            'withdrawal_approved' => $withdrawalApproved,
            'withdrawal_count' => $withdrawalCount,
            'withdrawal_pending' => $withdrawalPending,
            'withdrawal_pending_count' => $withdrawalPendingCount,
            'total_bet' => $totalBet,
            'bet_count' => $betCount,
            'total_won' => $totalWon,
            'won_count' => $wonCount,
            'admin_credits' => $adminCredits,
            'admin_credit_count' => $adminCreditCount,
            'admin_debits' => $adminDebits,
            'admin_debit_count' => $adminDebitCount,
            'admin_net_adjusted' => $adminNetAdjusted,
            'admin_adjustment_count' => $adminAdjustmentCount,
            'net_pl' => $netPL,
            'net_cashflow' => $netCashflow,
        ];

        return view('admin.ledger.show', [
            'user' => $user,
            'stats' => $stats,
            'typeCounts' => $typeCounts,
            'gameStats' => $gameStats,
            'transactions' => $transactions,
            'type' => $type,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'search' => $search,
        ]);
    }

    /**
     * Direct atomic wallet adjustment right from the player's statement ledger.
     */
    public function adjustBalance(Request $request, $id)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:credit,debit'],
            'amount' => ['required', 'numeric', 'min:1'],
            'notes' => ['required', 'string', 'max:255'],
        ]);

        return DB::transaction(function () use ($id, $validated) {
            $user = User::where('id', $id)->lockForUpdate()->firstOrFail();
            $amount = (float) $validated['amount'];
            $before = $user->balance;

            if ($validated['action'] === 'credit') {
                $after = bcadd((string) $before, (string) $amount, 2);
            } else {
                if ($before < $amount) {
                    return back()->with('error', "Cannot debit ₹{$amount}. Current user balance is only ₹{$before}.");
                }
                $after = bcsub((string) $before, (string) $amount, 2);
            }

            $user->balance = $after;
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'admin_adjustment',
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'status' => 'completed',
                'reviewed_by' => Auth::id(),
                'admin_notes' => "Admin {$validated['action']}: {$validated['notes']}",
            ]);

            return back()->with('success', "Wallet balance for {$user->name} adjusted successfully: ".strtoupper($validated['action']).' ₹'.number_format($amount, 2).'. New Balance: ₹'.number_format($after, 2));
        });
    }

    /**
     * Export the player's bank-statement ledger as a CSV file.
     */
    public function exportCsv(Request $request, $id): StreamedResponse
    {
        $user = User::where('role', 'user')->findOrFail($id);

        $query = Transaction::with('reviewer:id,name')
            ->where('user_id', $user->id)
            ->orderBy('id', 'asc');

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $filename = 'Statement_User_'.$user->id.'_'.date('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($query, $user) {
            $handle = fopen('php://output', 'w');

            // Header info
            fputcsv($handle, ['ACCOUNT STATEMENT / PASSBOOK LEDGER']);
            fputcsv($handle, ['Player Name', $user->name, 'User ID', '#'.$user->id]);
            fputcsv($handle, ['Phone', $user->phone, 'Email', $user->email ?: 'N/A']);
            fputcsv($handle, ['Current Balance', 'INR '.number_format($user->balance, 2), 'Generated At', date('d M Y h:i A')]);
            fputcsv($handle, []);

            // Column Headings
            fputcsv($handle, [
                'Txn ID',
                'Date & Time',
                'Type',
                'Payment Mode / QR',
                'Narration / Description',
                'Debit (-)',
                'Credit (+)',
                'Balance Before',
                'Balance After',
                'Status',
                'UTR / Reference',
                'Handled By / Reviewer',
            ]);

            $query->chunk(200, function ($transactions) use ($handle) {
                foreach ($transactions as $t) {
                    $isDebit = in_array($t->type, ['withdrawal', 'bet_placed']) ||
                        ($t->type === 'admin_adjustment' && $t->balance_after < $t->balance_before);
                    $isCredit = in_array($t->type, ['deposit', 'bet_won']) ||
                        ($t->type === 'admin_adjustment' && $t->balance_after > $t->balance_before);

                    $debit = $isDebit ? number_format($t->amount, 2, '.', '') : '';
                    $credit = $isCredit ? number_format($t->amount, 2, '.', '') : '';

                    fputcsv($handle, [
                        '#TXN-'.$t->id,
                        $t->created_at ? $t->created_at->format('d M Y h:i:s A') : '',
                        strtoupper(str_replace('_', ' ', $t->type)),
                        $t->payment_method ?: ($t->type === 'deposit' ? 'UPI' : '-'),
                        $t->admin_notes ?: ucfirst($t->type),
                        $debit,
                        $credit,
                        number_format($t->balance_before, 2, '.', ''),
                        number_format($t->balance_after, 2, '.', ''),
                        strtoupper($t->status),
                        $t->utr_number ?: '-',
                        $t->reviewer?->name ?: 'System',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
