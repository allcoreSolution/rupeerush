<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
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
        $referralCode = strtoupper(trim((string) $request->query('ref', '')));

        return redirect()->away($setting->getRegistrationWhatsappUrl($referralCode));
    }

    public function register(Request $request)
    {
        $setting = AdminSetting::current();
        $referralCode = strtoupper(trim((string) $request->input('referral_code', '')));

        return redirect()->away($setting->getRegistrationWhatsappUrl($referralCode));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
