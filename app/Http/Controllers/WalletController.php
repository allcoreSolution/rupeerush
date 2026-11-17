<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\PaymentQr;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function depositPage()
    {
        $user = Auth::user();
        $setting = AdminSetting::current();
        $paymentQrs = PaymentQr::where('is_active', true)->orderBy('display_order')->orderBy('id', 'asc')->get();
        $recentDeposits = Transaction::where('user_id', $user->id)
            ->where('type', 'deposit')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return view('wallet.deposit', [
            'user' => $user,
            'setting' => $setting,
            'paymentQrs' => $paymentQrs,
            'recentDeposits' => $recentDeposits,
        ]);
    }

    public function submitDeposit(Request $request)
    {
        $setting = AdminSetting::current();

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:'.$setting->min_deposit, 'max:'.$setting->max_deposit],
            'utr_number' => ['required', 'string', 'min:8', 'max:30', 'unique:transactions,utr_number'],
            'screenshot' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'payment_qr_id' => ['nullable', 'integer'],
            'payment_platform' => ['nullable', 'string', 'max:100'],
        ], [
            'amount.min' => "Minimum deposit amount is ₹{$setting->min_deposit}.",
            'amount.max' => "Maximum deposit amount is ₹{$setting->max_deposit}.",
            'utr_number.unique' => 'This UTR / Reference number has already been submitted.',
            'screenshot.max' => 'Screenshot file size cannot exceed 4MB.',
        ]);

        $user = Auth::user();

        // Determine payment platform / method and QR details
        $paymentMethod = 'UPI';
        $accountDetails = null;

        if (! empty($validated['payment_qr_id'])) {
            $qr = PaymentQr::find($validated['payment_qr_id']);
            if ($qr) {
                $paymentMethod = $qr->platform_name.' QR';
                $upi = $qr->upi_id ?: ($setting->upi_id ?? 'N/A');
                $payee = $qr->upi_name ?: ($setting->upi_name ?? 'N/A');
                $accountDetails = "Platform QR: {$qr->platform_name} | Payee: {$payee} | UPI: {$upi}";
            }
        } elseif (! empty($validated['payment_platform'])) {
            $paymentMethod = trim($validated['payment_platform']).' QR';
            $accountDetails = "Platform: {$paymentMethod}";
        }

        // 1. Direct Public upload (Guaranteed 100% to work on any live shared hosting/cPanel without symlink)
        $file = $request->file('screenshot');
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = 'proof_'.time().'_'.uniqid().'.'.$ext;

        $publicDir = public_path('uploads/proofs');
        if (! file_exists($publicDir)) {
            mkdir($publicDir, 0755, true);
        }
        $file->move($publicDir, $filename);

        // 2. Dual redundancy: copy to storage/app/public/proofs as well
        $storageDir = storage_path('app/public/proofs');
        if (! file_exists($storageDir)) {
            mkdir($storageDir, 0755, true);
        }
        @copy($publicDir.DIRECTORY_SEPARATOR.$filename, $storageDir.DIRECTORY_SEPARATOR.$filename);

        $savedPath = 'uploads/proofs/'.$filename;

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => $validated['amount'],
            'balance_before' => $user->balance,
            'balance_after' => $user->balance,
            'utr_number' => $validated['utr_number'],
            'screenshot_path' => $savedPath,
            'payment_method' => $paymentMethod,
            'account_details' => $accountDetails,
            'status' => 'pending',
            'admin_notes' => 'Awaiting admin verification.',
        ]);

        return redirect()->route('wallet.deposit')->with('success', 'Deposit request of ₹'.number_format($validated['amount'], 2).' via '.$paymentMethod.' submitted successfully! Admin will verify and credit your balance shortly.');
    }

    public function withdrawPage()
    {
        $user = Auth::user();
        $setting = AdminSetting::current();
        $recentWithdrawals = Transaction::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return view('wallet.withdraw', [
            'user' => $user,
            'setting' => $setting,
            'recentWithdrawals' => $recentWithdrawals,
        ]);
    }

    public function submitWithdrawal(Request $request)
    {
        $user = Auth::user();

        if ($user->status !== 'active') {
            return back()->with('error', 'Your account is currently suspended.');
        }

        $setting = AdminSetting::current();
        $minWithdrawal = (float) ($setting->min_withdrawal ?? 200.00);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:'.$minWithdrawal],
            'qr_code' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'upi_id' => ['nullable', 'string', 'max:100'],
            'account_holder_name' => ['nullable', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'ifsc_code' => ['nullable', 'string', 'max:20'],
            'account_details' => ['nullable', 'string', 'max:255'],
        ], [
            'amount.min' => 'Minimum withdrawal amount is ₹'.number_format($minWithdrawal, 2).'.',
            'qr_code.required' => 'Please upload your UPI QR code so admin can transfer your funds.',
            'qr_code.image' => 'The uploaded QR file must be an image (PNG, JPG, WEBP).',
            'qr_code.max' => 'QR code file size cannot exceed 4MB.',
        ]);

        $amount = (float) $validated['amount'];

        return DB::transaction(function () use ($user, $amount, $request) {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            if ((float) $lockedUser->balance < $amount) {
                return back()->withInput()->with('error', 'Insufficient balance. You have ₹'.number_format($lockedUser->balance, 2).' in your wallet.');
            }

            // 1. Direct Public upload of user's QR code
            $qrFile = $request->file('qr_code');
            $qrExt = $qrFile->getClientOriginalExtension() ?: 'png';
            $qrFilename = 'user_qr_'.time().'_'.uniqid().'.'.$qrExt;

            $publicQrDir = public_path('uploads/proofs/user_qr');
            if (! file_exists($publicQrDir)) {
                mkdir($publicQrDir, 0755, true);
            }
            $qrFile->move($publicQrDir, $qrFilename);

            // 2. Dual redundancy: copy to storage/app/public/proofs/user_qr as well
            $storageDir = storage_path('app/public/proofs/user_qr');
            if (! file_exists($storageDir)) {
                mkdir($storageDir, 0755, true);
            }
            @copy($publicQrDir.DIRECTORY_SEPARATOR.$qrFilename, $storageDir.DIRECTORY_SEPARATOR.$qrFilename);

            $savedQrPath = 'uploads/proofs/user_qr/'.$qrFilename;

            // Deduct requested amount from user's balance immediately
            $balanceBefore = $lockedUser->balance;
            $balanceAfter = bcsub((string) $balanceBefore, (string) $amount, 2);
            $lockedUser->balance = $balanceAfter;
            $lockedUser->save();

            // Build human-readable account details
            $detailsParts = [];
            if ($request->filled('upi_id')) {
                $detailsParts[] = 'UPI: '.$request->upi_id;
            }
            if ($request->filled('account_holder_name')) {
                $detailsParts[] = 'A/C Name: '.$request->account_holder_name;
            }
            if ($request->filled('account_number')) {
                $detailsParts[] = 'A/C No: '.$request->account_number;
            }
            if ($request->filled('ifsc_code')) {
                $detailsParts[] = 'IFSC: '.strtoupper(trim($request->ifsc_code));
            }
            if ($request->filled('account_details')) {
                $detailsParts[] = $request->account_details;
            }

            $details = ! empty($detailsParts) ? implode(' | ', $detailsParts) : 'UPI QR Transfer';

            Transaction::create([
                'user_id' => $lockedUser->id,
                'type' => 'withdrawal',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'user_qr_path' => $savedQrPath,
                'payment_method' => 'UPI_QR',
                'account_details' => $details,
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code ? strtoupper(trim($request->ifsc_code)) : null,
                'status' => 'pending',
                'admin_notes' => 'Withdrawal requested. Awaiting admin transfer and payment proof screenshot.',
            ]);

            return redirect()->route('wallet.withdraw')->with('success', 'Withdrawal request of ₹'.number_format($amount, 2).' submitted successfully! Admin will scan your QR code, send payment, and upload screenshot proof.');
        });
    }

    public function transactions(Request $request)
    {
        $user = Auth::user();
        $flow = $request->query('flow', 'all');
        $type = $request->query('type', 'all');

        $totalCredits = (float) Transaction::where('user_id', $user->id)
            ->whereIn('type', ['deposit', 'bet_won'])
            ->where('status', '!=', 'rejected')
            ->sum('amount');

        $totalDebits = (float) Transaction::where('user_id', $user->id)
            ->whereIn('type', ['withdrawal', 'bet_placed'])
            ->where('status', '!=', 'rejected')
            ->sum('amount');

        $query = Transaction::where('user_id', $user->id)->orderBy('id', 'desc');

        if ($flow === 'credit') {
            $query->whereIn('type', ['deposit', 'bet_won']);
        } elseif ($flow === 'debit') {
            $query->whereIn('type', ['withdrawal', 'bet_placed']);
        }

        if ($type !== 'all' && in_array($type, ['deposit', 'withdrawal', 'bet_placed', 'bet_won', 'admin_adjustment'], true)) {
            $query->where('type', $type);
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('wallet.transactions', [
            'user' => $user,
            'transactions' => $transactions,
            'flow' => $flow,
            'type' => $type,
            'totalCredits' => $totalCredits,
            'totalDebits' => $totalDebits,
        ]);
    }
}
