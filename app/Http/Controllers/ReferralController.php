<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReferralController extends Controller
{
    /**
     * Display the user's referral dashboard & referred users list.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Safety fallback: Ensure user has a referral code
        if (empty($user->referral_code)) {
            $user->referral_code = User::generateReferralCode();
            $user->save();
        }

        $referrals = $user->referrals()
            ->with('paidReferralCommission')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $totalReferrals = $user->referrals()->count();
        $depositedReferrals = $user->referralCommissions()->count();
        $totalCommissionEarned = (float) $user->referralCommissions()->sum('commission_amount');

        $settings = AdminSetting::current();
        $commissionPercent = (float) ($settings->referral_commission_percent ?? 1.00);

        $referralLink = route('register', ['ref' => $user->referral_code]);

        return view('referral.index', compact(
            'user',
            'referrals',
            'totalReferrals',
            'depositedReferrals',
            'totalCommissionEarned',
            'commissionPercent',
            'referralLink'
        ));
    }
}
