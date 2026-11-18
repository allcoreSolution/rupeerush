<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\PaymentQr;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ReferralService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function settings()
    {
        $setting = AdminSetting::current();
        $paymentQrs = PaymentQr::orderBy('display_order')->orderBy('id', 'asc')->get();

        return view('admin.payments.settings', [
            'setting' => $setting,
            'paymentQrs' => $paymentQrs,
        ]);
    }

    public function storeQr(Request $request)
    {
        $request->validate([
            'platform' => ['required', 'string', 'in:gpay,phonepe,paytm,bhim,navi,other'],
            'custom_platform_name' => ['nullable', 'required_if:platform,other', 'string', 'max:100'],
            'upi_id' => ['nullable', 'string', 'max:150'],
            'upi_name' => ['nullable', 'string', 'max:150'],
            'qr_code' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
        ], [
            'custom_platform_name.required_if' => 'Please enter the custom platform name for this QR code.',
            'qr_code.required' => 'Please select a QR code image to upload.',
            'qr_code.image' => 'The uploaded file must be an image (PNG, JPG, WEBP).',
            'qr_code.max' => 'QR image size must not exceed 4MB.',
        ]);

        $platform = $request->input('platform');
        $platformName = match ($platform) {
            'gpay' => 'Google Pay',
            'phonepe' => 'PhonePe',
            'paytm' => 'Paytm',
            'bhim' => 'BHIM UPI',
            'navi' => 'Navi UPI',
            'other' => trim((string) $request->input('custom_platform_name')),
            default => ucfirst($platform),
        };

        $file = $request->file('qr_code');
        $extension = $file->getClientOriginalExtension() ?: 'png';
        $filename = 'platform_qr_'.strtolower($platform).'_'.time().'_'.uniqid().'.'.$extension;

        // 1. Direct Public upload
        $publicUploadDir = public_path('uploads/qr');
        if (! file_exists($publicUploadDir)) {
            mkdir($publicUploadDir, 0755, true);
        }
        $file->move($publicUploadDir, $filename);

        // 2. Dual redundancy: copy to storage/app/public/qr
        $storageDir = storage_path('app/public/qr');
        if (! file_exists($storageDir)) {
            mkdir($storageDir, 0755, true);
        }
        @copy($publicUploadDir.DIRECTORY_SEPARATOR.$filename, $storageDir.DIRECTORY_SEPARATOR.$filename);

        PaymentQr::create([
            'platform' => $platform,
            'platform_name' => $platformName,
            'upi_id' => $request->input('upi_id'),
            'upi_name' => $request->input('upi_name'),
            'qr_code_path' => 'uploads/qr/'.$filename,
            'is_active' => true,
            'display_order' => PaymentQr::count(),
        ]);

        return redirect()->route('admin.payments.settings')
            ->with('success', "{$platformName} QR code uploaded and activated successfully!");
    }

    public function toggleQr(int $id)
    {
        $qr = PaymentQr::findOrFail($id);
        $qr->is_active = ! $qr->is_active;
        $qr->save();

        $statusText = $qr->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.payments.settings')
            ->with('success', "{$qr->platform_name} QR code has been {$statusText}.");
    }

    public function deleteQr(int $id)
    {
        $qr = PaymentQr::findOrFail($id);
        $platformName = $qr->platform_name;

        if ($qr->qr_code_path) {
            $oldBase = basename($qr->qr_code_path);
            @unlink(public_path('uploads/qr/'.$oldBase));
            @unlink(storage_path('app/public/qr/'.$oldBase));
            @unlink(public_path('storage/qr/'.$oldBase));
        }

        $qr->delete();

        return redirect()->route('admin.payments.settings')
            ->with('success', "{$platformName} QR code has been permanently deleted.");
    }

    public function updateSettings(Request $request)
    {
        $setting = AdminSetting::current();

        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:50'],
            'upi_id' => ['required', 'string', 'max:100'],
            'upi_name' => ['nullable', 'string', 'max:100'],
            'business_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_ifsc' => ['nullable', 'string', 'max:20'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'qr_code' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'min_deposit' => ['required', 'numeric', 'min:1'],
            'max_deposit' => ['required', 'numeric', 'gte:min_deposit'],
            'min_withdrawal' => ['nullable', 'numeric', 'min:1'],
            'win_multiplier' => ['required', 'numeric', 'min:1'],
            'win_commission_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'house_profit_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'referral_commission_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'lottery_min_bet' => ['required', 'numeric', 'min:1'],
            'lottery_max_bet' => ['required', 'numeric', 'gte:lottery_min_bet'],
            'wingo_min_bet' => ['required', 'numeric', 'min:1'],
            'wingo_max_bet' => ['required', 'numeric', 'gte:wingo_min_bet'],
            'mines_min_bet' => ['nullable', 'numeric', 'min:1'],
            'mines_max_bet' => ['nullable', 'numeric', 'gte:mines_min_bet'],
            'mines_house_edge' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'mines_jackpot_min_bet' => ['nullable', 'numeric', 'min:1'],
            'mines_jackpot_pool' => ['nullable', 'numeric', 'min:0'],
            'support_whatsapp' => ['nullable', 'string', 'max:50'],
            'support_email' => ['nullable', 'email', 'max:100'],
            'betting_lock_minutes' => ['required', 'integer', 'min:1', 'max:10'],
            'instructions' => ['nullable', 'string'],
            'hero_banner_1' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'hero_banner_2' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'hero_banner_3' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'hero_banner_1_link' => ['nullable', 'string', 'max:255'],
            'hero_banner_2_link' => ['nullable', 'string', 'max:255'],
            'hero_banner_3_link' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('qr_code')) {
            $file = $request->file('qr_code');
            $extension = $file->getClientOriginalExtension() ?: 'png';
            $filename = 'qr_'.time().'_'.uniqid().'.'.$extension;

            // 1. Direct Public upload (Guaranteed 100% to work on any live shared hosting/cPanel without symlink)
            $publicUploadDir = public_path('uploads/qr');
            if (! file_exists($publicUploadDir)) {
                mkdir($publicUploadDir, 0755, true);
            }
            $file->move($publicUploadDir, $filename);

            // 2. Dual redundancy: copy to storage/app/public/qr as well
            $storageDir = storage_path('app/public/qr');
            if (! file_exists($storageDir)) {
                mkdir($storageDir, 0755, true);
            }
            @copy($publicUploadDir.DIRECTORY_SEPARATOR.$filename, $storageDir.DIRECTORY_SEPARATOR.$filename);

            // 3. Delete old QR files if any
            if ($setting->qr_code_path) {
                $oldBase = basename($setting->qr_code_path);
                @unlink(public_path('uploads/qr/'.$oldBase));
                @unlink(storage_path('app/public/qr/'.$oldBase));
                @unlink(public_path('storage/qr/'.$oldBase));
            }

            $setting->qr_code_path = 'uploads/qr/'.$filename;
        }

        $setting->site_name = $validated['site_name'];
        $setting->upi_id = $validated['upi_id'];
        $setting->upi_name = $validated['upi_name'] ?? null;
        $setting->business_name = $validated['business_name'] ?? $validated['upi_name'] ?? $setting->business_name;
        $setting->bank_account_number = $validated['bank_account_number'] ?? null;
        $setting->bank_ifsc = ! empty($validated['bank_ifsc']) ? strtoupper(trim($validated['bank_ifsc'])) : null;
        $setting->bank_name = $validated['bank_name'] ?? null;
        $setting->min_deposit = $validated['min_deposit'];
        $setting->max_deposit = $validated['max_deposit'];
        $setting->min_withdrawal = $validated['min_withdrawal'] ?? $setting->min_withdrawal ?? 200.00;
        $setting->win_multiplier = $validated['win_multiplier'];
        $setting->win_commission_percent = $validated['win_commission_percent'] ?? 10.00;
        $setting->house_profit_rate = $validated['house_profit_rate'] ?? $setting->house_profit_rate ?? 78.00;
        $setting->referral_commission_percent = $validated['referral_commission_percent'] ?? $setting->referral_commission_percent ?? 1.00;
        $setting->lottery_min_bet = $validated['lottery_min_bet'];
        $setting->lottery_max_bet = $validated['lottery_max_bet'];
        $setting->wingo_min_bet = $validated['wingo_min_bet'];
        $setting->wingo_max_bet = $validated['wingo_max_bet'];
        if ($request->filled('mines_min_bet')) {
            $setting->mines_min_bet = (float) $request->mines_min_bet;
        }
        if ($request->filled('mines_max_bet')) {
            $setting->mines_max_bet = (float) $request->mines_max_bet;
        }
        if ($request->filled('mines_house_edge')) {
            $setting->mines_house_edge = (float) $request->mines_house_edge;
        }
        if ($request->filled('mines_jackpot_min_bet')) {
            $setting->mines_jackpot_min_bet = (float) $request->mines_jackpot_min_bet;
        }
        if ($request->filled('mines_jackpot_pool')) {
            $setting->mines_jackpot_pool = (float) $request->mines_jackpot_pool;
        }
        if (isset($validated['support_whatsapp'])) {
            $setting->support_whatsapp = $validated['support_whatsapp'];
        }
        if (isset($validated['support_email'])) {
            $setting->support_email = $validated['support_email'];
        }
        $setting->betting_lock_minutes = $validated['betting_lock_minutes'];
        $setting->instructions = $validated['instructions'] ?? $setting->instructions;

        // Hero Banners (Swiper 3 Images) Management
        $bannersDir = public_path('uploads/banners');
        if (! file_exists($bannersDir)) {
            mkdir($bannersDir, 0755, true);
        }
        $storageBannersDir = storage_path('app/public/banners');
        if (! file_exists($storageBannersDir)) {
            mkdir($storageBannersDir, 0755, true);
        }

        for ($i = 1; $i <= 3; $i++) {
            $bannerFileKey = "hero_banner_{$i}";
            $bannerLinkKey = "hero_banner_{$i}_link";
            $removeKey = "remove_banner_{$i}";

            if ($request->boolean($removeKey)) {
                if (! empty($setting->{$bannerFileKey})) {
                    @unlink(public_path($setting->{$bannerFileKey}));
                    @unlink($storageBannersDir.DIRECTORY_SEPARATOR.basename($setting->{$bannerFileKey}));
                }
                $setting->{$bannerFileKey} = null;
            } elseif ($request->hasFile($bannerFileKey)) {
                $file = $request->file($bannerFileKey);
                $extension = $file->getClientOriginalExtension() ?: 'jpg';
                $filename = "hero_banner_{$i}_".time().'_'.uniqid().'.'.$extension;

                $file->move($bannersDir, $filename);
                @copy($bannersDir.DIRECTORY_SEPARATOR.$filename, $storageBannersDir.DIRECTORY_SEPARATOR.$filename);

                if (! empty($setting->{$bannerFileKey})) {
                    @unlink(public_path($setting->{$bannerFileKey}));
                    @unlink($storageBannersDir.DIRECTORY_SEPARATOR.basename($setting->{$bannerFileKey}));
                }

                $setting->{$bannerFileKey} = 'uploads/banners/'.$filename;
            }

            if ($request->has($bannerLinkKey)) {
                $setting->{$bannerLinkKey} = $request->input($bannerLinkKey) ?: null;
            }
        }

        $setting->save();

        return redirect()->route('admin.payments.settings')->with('success', 'System settings, payment gateway & hero banners updated successfully!');
    }

    /**
     * Daily-to-daily transaction detailed report and ledger for admin.
     */
    public function dailyTransactions(Request $request)
    {
        $today = Carbon::now('Asia/Kolkata')->format('Y-m-d');
        $selectedDate = $request->query('date', $today);
        $type = $request->query('type', 'all');
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        // 1. Daily Metrics for the selected date
        $dayQuery = Transaction::whereDate('created_at', $selectedDate);

        $depositsQuery = (clone $dayQuery)->where('type', 'deposit');
        $totalApprovedDeposits = (clone $depositsQuery)->whereIn('status', ['approved', 'completed'])->sum('amount');
        $totalApprovedDepositsCount = (clone $depositsQuery)->whereIn('status', ['approved', 'completed'])->count();
        $pendingDepositsCount = (clone $depositsQuery)->where('status', 'pending')->count();

        $withdrawalsQuery = (clone $dayQuery)->where('type', 'withdrawal');
        $totalApprovedWithdrawals = (clone $withdrawalsQuery)->whereIn('status', ['approved', 'completed'])->sum('amount');
        $totalApprovedWithdrawalsCount = (clone $withdrawalsQuery)->whereIn('status', ['approved', 'completed'])->count();
        $pendingWithdrawalsCount = (clone $withdrawalsQuery)->where('status', 'pending')->count();

        $betsPlacedQuery = (clone $dayQuery)->where('type', 'bet_placed');
        $totalBetsPlacedAmount = (clone $betsPlacedQuery)->sum('amount');
        $totalBetsPlacedCount = (clone $betsPlacedQuery)->count();

        $betsWonQuery = (clone $dayQuery)->where('type', 'bet_won');
        $totalBetsWonAmount = (clone $betsWonQuery)->sum('amount');
        $totalBetsWonCount = (clone $betsWonQuery)->count();

        $netCashFlow = $totalApprovedDeposits - $totalApprovedWithdrawals;
        $netGamingRevenue = $totalBetsPlacedAmount - $totalBetsWonAmount;

        $dailyStats = [
            'date' => $selectedDate,
            'approved_deposits' => $totalApprovedDeposits,
            'approved_deposits_count' => $totalApprovedDepositsCount,
            'pending_deposits_count' => $pendingDepositsCount,
            'approved_withdrawals' => $totalApprovedWithdrawals,
            'approved_withdrawals_count' => $totalApprovedWithdrawalsCount,
            'pending_withdrawals_count' => $pendingWithdrawalsCount,
            'bets_placed_amount' => $totalBetsPlacedAmount,
            'bets_placed_count' => $totalBetsPlacedCount,
            'bets_won_amount' => $totalBetsWonAmount,
            'bets_won_count' => $totalBetsWonCount,
            'net_cash_flow' => $netCashFlow,
            'net_gaming_revenue' => $netGamingRevenue,
        ];

        // 2. Day-by-Day Aggregation summary for the last 14 days
        $recentDays = [];
        for ($i = 0; $i < 14; $i++) {
            $d = Carbon::now('Asia/Kolkata')->subDays($i)->format('Y-m-d');
            $dep = Transaction::whereDate('created_at', $d)->where('type', 'deposit')->whereIn('status', ['approved', 'completed'])->sum('amount');
            $wth = Transaction::whereDate('created_at', $d)->where('type', 'withdrawal')->whereIn('status', ['approved', 'completed'])->sum('amount');
            $bet = Transaction::whereDate('created_at', $d)->where('type', 'bet_placed')->sum('amount');
            $won = Transaction::whereDate('created_at', $d)->where('type', 'bet_won')->sum('amount');
            $txCount = Transaction::whereDate('created_at', $d)->count();

            $recentDays[] = [
                'date' => $d,
                'deposits' => (float) $dep,
                'withdrawals' => (float) $wth,
                'bets' => (float) $bet,
                'payouts' => (float) $won,
                'net_profit' => (float) ($bet - $won),
                'tx_count' => $txCount,
            ];
        }

        // 3. Detailed transactions list for the selected date
        $listQuery = Transaction::with(['user', 'reviewer'])
            ->whereDate('created_at', $selectedDate)
            ->orderBy('id', 'desc');

        if ($type !== 'all') {
            $listQuery->where('type', $type);
        }

        if ($status !== 'all') {
            $listQuery->where('status', $status);
        }

        if ($request->filled('search')) {
            $listQuery->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('utr_number', 'like', "%{$search}%")
                    ->orWhere('admin_notes', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $transactions = $listQuery->paginate(25)->withQueryString();

        return view('admin.payments.daily', [
            'transactions' => $transactions,
            'dailyStats' => $dailyStats,
            'recentDays' => $recentDays,
            'selectedDate' => $selectedDate,
            'type' => $type,
            'status' => $status,
            'search' => $search,
        ]);
    }

    public function deposits(Request $request)
    {
        $query = Transaction::with(['user', 'reviewer'])->where('type', 'deposit')->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('utr_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('phone', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        $deposits = $query->paginate(20)->withQueryString();

        return view('admin.payments.deposits', [
            'deposits' => $deposits,
            'currentStatus' => $request->status ?? '',
            'search' => $request->search ?? '',
        ]);
    }

    public function approveDeposit(Request $request, $id)
    {
        return DB::transaction(function () use ($id) {
            $deposit = Transaction::where('id', $id)->where('type', 'deposit')->lockForUpdate()->firstOrFail();

            if ($deposit->status !== 'pending') {
                return back()->with('error', "Deposit #{$id} is already {$deposit->status}.");
            }

            $user = User::where('id', $deposit->user_id)->lockForUpdate()->firstOrFail();
            $balanceBefore = $user->balance;
            $balanceAfter = bcadd((string) $balanceBefore, (string) $deposit->amount, 2);

            $user->balance = $balanceAfter;
            // Activate account if this was a pending self-registration
            if ($user->status === 'pending') {
                $user->status = 'active';
            }
            $user->save();

            $deposit->status = 'approved';
            $deposit->balance_before = $balanceBefore;
            $deposit->balance_after = $balanceAfter;
            $deposit->reviewed_by = Auth::id();
            $deposit->admin_notes = 'Verified & approved by admin.';
            $deposit->save();

            $refCommission = ReferralService::processFirstDepositCommission($deposit);
            $refNote = '';
            if ($refCommission) {
                $refNote = ' Referral bonus of ₹'.number_format($refCommission->commission_amount, 2)." ({$refCommission->commission_percent}%) credited to parent {$refCommission->parentUser->name}!";
            }

            return back()->with('success', "Deposit #{$id} of ₹".number_format($deposit->amount, 2).' approved! ₹'.number_format($deposit->amount, 2)." credited to {$user->name}. Player account is active for login.{$refNote}");
        });
    }

    public function rejectDeposit(Request $request, $id)
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $deposit = Transaction::where('id', $id)->where('type', 'deposit')->firstOrFail();

        if ($deposit->status !== 'pending') {
            return back()->with('error', "Deposit #{$id} is already {$deposit->status}.");
        }

        $deposit->status = 'rejected';
        $deposit->reviewed_by = Auth::id();
        $deposit->admin_notes = $request->reason ?: 'Rejected by admin: Invalid UTR or Payment not received.';
        $deposit->save();

        return back()->with('success', "Deposit #{$id} rejected.");
    }

    public function withdrawals(Request $request)
    {
        $query = Transaction::with(['user', 'reviewer'])->where('type', 'withdrawal')->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('account_details', 'like', "%{$search}%")
                    ->orWhere('utr_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('phone', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        $withdrawals = $query->paginate(20)->withQueryString();

        return view('admin.payments.withdrawals', [
            'withdrawals' => $withdrawals,
            'currentStatus' => $request->status ?? '',
            'search' => $request->search ?? '',
        ]);
    }

    public function approveWithdrawal(Request $request, $id)
    {
        $request->validate([
            'screenshot' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'utr_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'screenshot.required' => 'Payment screenshot is required as proof of transfer to the player.',
            'screenshot.image' => 'Uploaded payment proof must be an image (PNG, JPG, WEBP).',
            'screenshot.max' => 'Payment screenshot cannot exceed 4MB.',
        ]);

        return DB::transaction(function () use ($id, $request) {
            $withdrawal = Transaction::where('id', $id)->where('type', 'withdrawal')->lockForUpdate()->firstOrFail();

            if ($withdrawal->status !== 'pending') {
                return back()->with('error', "Withdrawal #{$id} is already {$withdrawal->status}.");
            }

            // 1. Direct Public upload of admin's payment proof screenshot
            $adminProofFile = $request->file('screenshot');
            $adminProofExt = $adminProofFile->getClientOriginalExtension() ?: 'jpg';
            $adminProofFilename = 'admin_proof_'.time().'_'.uniqid().'.'.$adminProofExt;

            $publicAdminProofDir = public_path('uploads/proofs/admin_proofs');
            if (! file_exists($publicAdminProofDir)) {
                mkdir($publicAdminProofDir, 0755, true);
            }
            $adminProofFile->move($publicAdminProofDir, $adminProofFilename);

            // 2. Dual redundancy: copy to storage/app/public/proofs/admin_proofs as well
            $storageDir = storage_path('app/public/proofs/admin_proofs');
            if (! file_exists($storageDir)) {
                mkdir($storageDir, 0755, true);
            }
            @copy($publicAdminProofDir.DIRECTORY_SEPARATOR.$adminProofFilename, $storageDir.DIRECTORY_SEPARATOR.$adminProofFilename);

            $savedAdminProofPath = 'uploads/proofs/admin_proofs/'.$adminProofFilename;

            $withdrawal->status = 'approved';
            $withdrawal->admin_proof_path = $savedAdminProofPath;
            $withdrawal->screenshot_path = $savedAdminProofPath;
            $withdrawal->utr_number = $request->utr_number ?: $withdrawal->utr_number;
            $withdrawal->reviewed_by = Auth::id();
            $withdrawal->admin_notes = $request->notes ?: 'Payment approved and sent by admin with attached transfer screenshot.';
            $withdrawal->save();

            return back()->with('success', "Withdrawal #{$id} of ₹".number_format($withdrawal->amount, 2).' approved! Payment screenshot proof has been uploaded.');
        });
    }

    public function rejectWithdrawal(Request $request, $id)
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        return DB::transaction(function () use ($id, $request) {
            $withdrawal = Transaction::where('id', $id)->where('type', 'withdrawal')->lockForUpdate()->firstOrFail();

            if ($withdrawal->status !== 'pending') {
                return back()->with('error', "Withdrawal #{$id} is already {$withdrawal->status}.");
            }

            // Refund amount back to user's wallet atomically
            $user = User::where('id', $withdrawal->user_id)->lockForUpdate()->first();
            if ($user) {
                $balanceBefore = $user->balance;
                $balanceAfter = bcadd((string) $balanceBefore, (string) $withdrawal->amount, 2);
                $user->balance = $balanceAfter;
                $user->save();
            }

            $withdrawal->status = 'rejected';
            $withdrawal->reviewed_by = Auth::id();
            $withdrawal->admin_notes = $request->reason ?: 'Rejected by admin: Invalid QR code or account details. (Amount refunded to user wallet)';
            $withdrawal->save();

            return back()->with('success', "Withdrawal #{$id} rejected and ₹".number_format($withdrawal->amount, 2).' has been refunded back to '.($user->name ?? 'player')."'s wallet.");
        });
    }
}
