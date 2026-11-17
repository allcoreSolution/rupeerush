<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'user')->withCount('bets')->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $request->search ?? '',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'digits:10', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6'],
            'email' => ['nullable', 'email', 'max:100', 'unique:users,email'],
            'balance' => ['nullable', 'numeric', 'min:0'],
            'referral_code' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'Player full name is required.',
            'phone.required' => '10-digit mobile phone number is required.',
            'phone.digits' => 'Phone number must be exactly 10 digits.',
            'phone.unique' => 'A player with this phone number already exists.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'email.unique' => 'This email is already associated with another user.',
        ]);

        $parentId = null;
        if (! empty($validated['referral_code'])) {
            $inputCode = strtoupper(trim($validated['referral_code']));
            $parent = User::where('referral_code', $inputCode)->first();
            if ($parent) {
                $parentId = $parent->id;
            } else {
                return back()->withInput()->with('error', "Referral code '{$inputCode}' was not found. Please verify or leave blank.");
            }
        }

        $initialBalance = ! empty($validated['balance']) ? (float) $validated['balance'] : 0.00;

        $user = User::create([
            'name' => trim($validated['name']),
            'phone' => trim($validated['phone']),
            'email' => ! empty($validated['email']) ? trim($validated['email']) : null,
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'balance' => $initialBalance,
            'status' => 'active',
            'referred_by' => $parentId,
        ]);

        if ($initialBalance > 0) {
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'admin_adjustment',
                'amount' => $initialBalance,
                'balance_before' => 0.00,
                'balance_after' => $initialBalance,
                'status' => 'completed',
                'admin_notes' => 'Admin credited initial wallet balance on registration.',
            ]);
        }

        return back()
            ->with('success', "Player account for {$user->name} created successfully!")
            ->with('new_player_credentials', [
                'name' => $user->name,
                'phone' => $user->phone,
                'password' => $validated['password'],
                'id' => $user->id,
                'balance' => $user->balance,
                'referral_code' => $user->referral_code,
            ]);
    }

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
                    return back()->with('error', "Cannot debit ₹{$amount}. User balance is only ₹{$before}.");
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
                'admin_notes' => "Admin {$validated['action']}: {$validated['notes']}",
            ]);

            return back()->with('success', "Wallet balance for {$user->name} successfully adjusted ({$validated['action']} ₹".number_format($amount, 2).').');
        });
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'active' ? 'banned' : 'active';
        $user->save();

        return back()->with('success', "User {$user->name} is now {$user->status}.");
    }
}
