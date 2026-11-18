<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\PaymentQr;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->route('home');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Please enter your mobile number or email address.',
            'password.required' => 'Please enter your password.',
        ]);

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (Auth::attempt([$field => $credentials['login'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $user = Auth::user();

            // Self-Registration Deposit Pending Verification Check
            if ($user->status === 'pending') {
                Auth::logout();

                $latestDeposit = Transaction::where('user_id', $user->id)
                    ->where('type', 'deposit')
                    ->latest('id')
                    ->first();

                if ($latestDeposit && $latestDeposit->status === 'rejected') {
                    $rejectNote = $latestDeposit->admin_notes ?: 'Invalid payment receipt or UTR.';

                    return back()->withErrors([
                        'login' => 'Your ₹100 registration deposit was rejected by admin (Reason: "'.$rejectNote.'"). Please contact WhatsApp support to re-verify.',
                    ])->onlyInput('login');
                }

                return back()->withErrors([
                    'login' => 'Your account registration is under verification. Admin has not approved your ₹100 deposit yet. You will be able to log in once admin approves your payment.',
                ])->onlyInput('login');
            }

            if ($user->status !== 'active') {
                Auth::logout();

                return back()->withErrors(['login' => 'Your account has been suspended or banned. Please contact support.'])->onlyInput('login');
            }

            $request->session()->regenerate();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('home'))->with('success', 'Welcome back, '.$user->name.'!');
        }

        return back()->withErrors([
            'login' => 'Invalid mobile number/email or password.',
        ])->onlyInput('login');
    }

    public function showAdminLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('home')->with('error', 'You are currently logged in as a player. Please logout to access the Admin Panel.');
        }

        return view('auth.admin-login');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Please enter Administrator ID or Email.',
            'password.required' => 'Please enter Administrator Password.',
        ]);

        $loginInput = trim($credentials['login']);
        $user = User::where(function ($query) use ($loginInput) {
            $query->where('phone', $loginInput)
                ->orWhere('email', $loginInput)
                ->orWhere('username', $loginInput);
        })->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Strict Role Check: Must be Admin!
            if (! $user->isAdmin()) {
                return back()->withErrors([
                    'login' => 'Access Denied: This portal is strictly for administrators. Regular players must login via the Player Login page.',
                ])->onlyInput('login');
            }

            if ($user->status !== 'active') {
                return back()->withErrors(['login' => 'Your administrator account has been suspended.'])->onlyInput('login');
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))->with('success', 'Authenticated successfully as Administrator.');
        }

        return back()->withErrors([
            'login' => 'Invalid administrator credentials. Access restricted.',
        ])->onlyInput('login');
    }

    public function adminLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Administrator signed out safely.');
    }

    public function showRegister(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $setting = AdminSetting::current();
        $paymentQrs = PaymentQr::where('is_active', true)->orderBy('display_order')->orderBy('id', 'asc')->get();
        $referralCode = strtoupper(trim((string) $request->query('ref', '')));
        $whatsappUrl = $setting->getRegistrationWhatsappUrl($referralCode);

        return view('auth.register', [
            'setting' => $setting,
            'paymentQrs' => $paymentQrs,
            'referralCode' => $referralCode,
            'whatsappUrl' => $whatsappUrl,
        ]);
    }

    public function register(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $setting = AdminSetting::current();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'phone' => ['required', 'string', 'regex:/^[6-9]\d{9}$/', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'referral_code' => ['nullable', 'string', 'max:20'],
            // Minimum initial deposit of ₹100 is strictly required
            'amount' => ['required', 'numeric', 'min:100'],
            'utr_number' => ['required', 'string', 'min:8', 'max:30', 'unique:transactions,utr_number'],
            'screenshot' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'payment_qr_id' => ['nullable', 'integer'],
        ], [
            'phone.regex' => 'Please enter a valid 10-digit Indian mobile number.',
            'phone.unique' => 'An account with this mobile number already exists. Please login.',
            'amount.min' => 'Minimum ₹100 deposit is required to complete self-registration.',
            'utr_number.unique' => 'This UTR / Reference number has already been submitted.',
            'screenshot.max' => 'Screenshot file size cannot exceed 4MB.',
        ]);

        // Referral tracking
        $referrerId = null;
        if (! empty($validated['referral_code'])) {
            $referrer = User::where('referral_code', strtoupper(trim($validated['referral_code'])))->first();
            if ($referrer) {
                $referrerId = $referrer->id;
            }
        }

        // Generate unique referral code for this new user
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        $newReferralCode = 'RR'.strtoupper(substr(md5($cleanPhone.time()), 0, 6));
        while (User::where('referral_code', $newReferralCode)->exists()) {
            $newReferralCode = 'RR'.rand(100000, 999999);
        }

        $username = 'player_'.$cleanPhone;

        // Create the user (Status: pending until admin approves initial deposit)
        $user = User::create([
            'name' => trim($validated['name']),
            'username' => $username,
            'phone' => $cleanPhone,
            'email' => $cleanPhone.'@rupeerush.player',
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'status' => 'pending',
            'balance' => 0.00,
            'referral_code' => $newReferralCode,
            'referred_by' => $referrerId,
        ]);

        // Save Screenshot Proof with public & storage redundancy
        $file = $request->file('screenshot');
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = 'proof_reg_'.time().'_'.uniqid().'.'.$ext;

        $publicDir = public_path('uploads/proofs');
        if (! file_exists($publicDir)) {
            mkdir($publicDir, 0755, true);
        }
        $file->move($publicDir, $filename);

        $storageDir = storage_path('app/public/proofs');
        if (! file_exists($storageDir)) {
            mkdir($storageDir, 0755, true);
        }
        @copy($publicDir.DIRECTORY_SEPARATOR.$filename, $storageDir.DIRECTORY_SEPARATOR.$filename);

        $savedPath = 'uploads/proofs/'.$filename;

        // Payment QR & Method Details
        $paymentMethod = 'UPI';
        $accountDetails = 'Self-Registration Deposit (Min ₹100)';
        if (! empty($validated['payment_qr_id'])) {
            $qr = PaymentQr::find($validated['payment_qr_id']);
            if ($qr) {
                $paymentMethod = $qr->platform_name.' QR';
                $upi = $qr->upi_id ?: ($setting->upi_id ?? 'N/A');
                $accountDetails .= " | Platform: {$qr->platform_name} | UPI: {$upi}";
            }
        }

        // Create Deposit Transaction (Pending admin approval)
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => $validated['amount'],
            'balance_before' => 0.00,
            'balance_after' => 0.00,
            'utr_number' => trim($validated['utr_number']),
            'screenshot_path' => $savedPath,
            'payment_method' => $paymentMethod,
            'account_details' => $accountDetails,
            'status' => 'pending',
        ]);

        // Account is created with status 'pending'. Player cannot log in until admin verifies & approves deposit!
        return redirect()->route('login')->with('success', 'Registration submitted successfully! Your account is currently pending admin verification of your ₹'.number_format($validated['amount'], 2).' deposit (UTR: '.$validated['utr_number'].'). You will be able to log in as soon as admin verifies your payment screenshot.');
    }

    /**
     * Instant 1-Click Demo Player Login for testing games.
     */
    public function demoLogin(Request $request)
    {
        $demoUser = User::firstOrCreate(
            ['username' => 'demoplayer'],
            [
                'name' => 'Demo Player',
                'phone' => '9876543210',
                'email' => 'demo@rupeerush.com',
                'password' => Hash::make('Demo@RupeeRush2026!'),
                'role' => 'user',
                'status' => 'active',
                'balance' => 10000.00,
            ]
        );

        // Ensure demo player has ample funds (₹10,000) to test all 11 games
        if ($demoUser->balance < 500.00) {
            $demoUser->balance = 10000.00;
            $demoUser->save();
        }

        Auth::login($demoUser);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Welcome, Demo Player! ₹10,000 free testing chips are active. You can now try all 11 games freely!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
