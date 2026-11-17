@extends('layouts.app')

@section('title', 'Refer & Earn - Invite Friends & Earn ' . $commissionPercent . '% First Deposit Bonus')

@section('styles')
<style>
    .referral-hero-card {
        background: linear-gradient(135deg, #0b1a30 0%, #0d274c 50%, #153b6d 100%);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 20px;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
        position: relative;
        overflow: hidden;
    }
    .referral-hero-card::before {
        content: "";
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.25) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .referral-code-box {
        background: rgba(15, 23, 42, 0.85);
        border: 2px dashed rgba(245, 158, 11, 0.6);
        border-radius: 14px;
        padding: 12px 20px;
        display: inline-flex;
        align-items: center;
        gap: 16px;
    }
    .referral-code-text {
        font-family: 'Courier New', Courier, monospace;
        font-size: 1.65rem;
        font-weight: 900;
        letter-spacing: 3px;
        color: #fbbf24;
        text-shadow: 0 0 12px rgba(251, 191, 36, 0.5);
    }
    .stat-card-referral {
        background: #0d1e38;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 20px;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
    }
    .stat-card-referral:hover {
        transform: translateY(-3px);
        border-color: rgba(245, 158, 11, 0.4);
        box-shadow: 0 10px 25px rgba(245, 158, 11, 0.15);
    }
    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    .how-it-works-step {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 14px;
        padding: 18px;
        height: 100%;
        position: relative;
    }
    .step-number-pill {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        margin-bottom: 10px;
    }
    .referral-table {
        color: #ffffff;
    }
    .referral-table thead th {
        background: #0b1a30;
        color: #ffffff;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 2px solid rgba(255, 255, 255, 0.15);
        padding: 14px 16px;
    }
    .referral-table tbody td {
        background: #071224;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 0.88rem;
        color: #ffffff;
    }
    .referral-table tbody tr:hover td {
        background: #0d2242;
    }
    /* White text across all cards */
    .stat-card-referral,
    .how-it-works-step,
    .referral-hero-card {
        color: #ffffff;
    }
    .stat-card-referral .card-subtext,
    .stat-card-referral .card-label,
    .how-it-works-step p,
    .referral-hero-card .hero-desc,
    .referral-hero-card .text-muted,
    .stat-card-referral .text-muted,
    .how-it-works-step .text-muted,
    .card .text-muted,
    .referral-table .text-muted {
        color: #ffffff !important;
    }
    .btn-copy-code {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
        font-weight: 700;
        border: none;
        border-radius: 10px;
        padding: 8px 18px;
        transition: all 0.2s;
    }
    .btn-copy-code:hover {
        background: linear-gradient(135deg, #fbbf24, #b45309);
        color: #fff;
        transform: scale(1.03);
    }
    .btn-whatsapp-share {
        background: #25d366;
        color: #fff;
        font-weight: 700;
        border-radius: 12px;
        padding: 12px 24px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 16px rgba(37, 211, 102, 0.35);
        transition: all 0.2s;
        border: none;
    }
    .btn-whatsapp-share:hover {
        background: #20bd5a;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(37, 211, 102, 0.45);
    }
</style>
@endsection

@section('content')
<div class="container-xl py-4">

    <!-- Hero Referral Invite Banner -->
    <div class="referral-hero-card p-4 p-md-5 mb-4 text-white">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.4);">
                    <i class="fa-solid fa-gift text-warning"></i>
                    <span class="text-warning fw-bold small text-uppercase tracking-wider">Official Partner Program</span>
                </div>
                <h1 class="h2 fw-bolder mb-2 text-white">
                    Invite Friends & Earn <span class="text-warning">{{ $commissionPercent }}% First Deposit Bonus</span>
                </h1>
                <p class="text-white mb-4" style="max-width: 620px; font-size: 0.95rem; line-height: 1.6;">
                    Share your unique referral link with your friends. Whenever a friend registers and completes their <strong>first deposit</strong>, you automatically get <strong>{{ $commissionPercent }}% real cash bonus</strong> credited directly to your playable balance!
                </p>

                <!-- Referral Code Box & Copy -->
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="referral-code-box">
                        <div>
                            <div class="text-white small text-uppercase fw-bold" style="font-size: 0.7rem;">Your Referral Code</div>
                            <div class="referral-code-text" id="referralCodeText">{{ $user->referral_code }}</div>
                        </div>
                        <button type="button" class="btn-copy-code" onclick="copyReferralCode()" id="copyCodeBtn" title="Copy Referral Code">
                            <i class="fa-regular fa-copy me-1"></i> <span id="copyCodeBtnText">Copy Code</span>
                        </button>
                    </div>

                    <a href="https://api.whatsapp.com/send?text={{ rawurlencode('🔥 Join RUPEERUSH using my Referral Code: ' . $user->referral_code . ' and get instant cash bonus! Play 2-Digit Lottery, WinGo, Teen Patti, Aviator & Mines. Register here: ' . $referralLink) }}" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="btn-whatsapp-share">
                        <i class="fa-brands fa-whatsapp fs-5"></i>
                        <span>Share on WhatsApp</span>
                    </a>
                </div>

                <!-- Shareable Link Copy Input -->
                <div class="mt-4 pt-2">
                    <label class="text-white small fw-bold mb-1 d-block text-uppercase" style="font-size: 0.72rem;">Shareable Invitation Link</label>
                    <div class="input-group" style="max-width: 580px;">
                        <input type="text" class="form-control font-monospace border-0 bg-dark text-warning px-3 py-2" id="referralLinkInput" value="{{ $referralLink }}" readonly style="font-size: 0.85rem; border-radius: 10px 0 0 10px;">
                        <button class="btn btn-warning px-3 fw-bold" type="button" onclick="copyReferralLink()" id="copyLinkBtn" style="border-radius: 0 10px 10px 0;">
                            <i class="fa-regular fa-copy me-1"></i> <span id="copyLinkBtnText">Copy Link</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Visual Mascot / Hero Badge Column -->
            <div class="col-lg-5 text-center d-none d-lg-block">
                <div class="p-4 rounded-4" style="background: rgba(15, 23, 42, 0.45); border: 1px solid rgba(245, 158, 11, 0.2);">
                    <div class="display-3 text-warning mb-2 animate__animated animate__pulse animate__infinite">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-1">Instant Cash Rewards</h5>
                    <p class="text-white small mb-3">No limits on how many friends you can invite. Unlimited earning potential!</p>
                    <div class="d-flex justify-content-center gap-3">
                        <div class="badge bg-success text-white px-3 py-2 rounded-pill" style="color: #ffffff !important;">
                            <i class="fa-solid fa-bolt me-1"></i> Instant Credit
                        </div>
                        <div class="badge bg-warning text-white px-3 py-2 rounded-pill" style="color: #ffffff !important;">
                            <i class="fa-solid fa-shield-halved me-1"></i> 100% Guaranteed
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 Stats Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- Stat 1: Total Referrals -->
        <div class="col-6 col-md-3">
            <div class="stat-card-referral">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-white small fw-bold text-uppercase">Total Referrals</span>
                    <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <div class="h3 fw-bolder text-white mb-0 font-monospace">{{ number_format($totalReferrals) }}</div>
                <div class="text-white small mt-1">Friends joined</div>
            </div>
        </div>

        <!-- Stat 2: Active / Deposited Referrals -->
        <div class="col-6 col-md-3">
            <div class="stat-card-referral">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-white small fw-bold text-uppercase">Funded Friends</span>
                    <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
                <div class="h3 fw-bolder text-success mb-0 font-monospace">{{ number_format($depositedReferrals) }}</div>
                <div class="text-white small mt-1">Completed 1st deposit</div>
            </div>
        </div>

        <!-- Stat 3: Total Commission Earned -->
        <div class="col-6 col-md-3">
            <div class="stat-card-referral">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-white small fw-bold text-uppercase">Total Earned</span>
                    <div class="stat-icon-wrapper bg-warning bg-opacity-10 text-warning">
                        <i class="fa-solid fa-indian-rupee-sign"></i>
                    </div>
                </div>
                <div class="h3 fw-bolder text-warning mb-0 font-monospace">₹{{ number_format($totalCommissionEarned, 2) }}</div>
                <div class="text-white small mt-1">Cash credited to balance</div>
            </div>
        </div>

        <!-- Stat 4: Commission Rate -->
        <div class="col-6 col-md-3">
            <div class="stat-card-referral">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-white small fw-bold text-uppercase">Bonus Rate</span>
                    <div class="stat-icon-wrapper bg-info bg-opacity-10 text-info">
                        <i class="fa-solid fa-percent"></i>
                    </div>
                </div>
                <div class="h3 fw-bolder text-info mb-0 font-monospace">{{ $commissionPercent }}%</div>
                <div class="text-white small mt-1">Per friend's 1st deposit</div>
            </div>
        </div>
    </div>

    <!-- How It Works 3-Step Banner -->
    <div class="card border-0 rounded-4 mb-4" style="background: #09172c; border: 1px solid rgba(255, 255, 255, 0.08) !important;">
        <div class="card-body p-4">
            <h5 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-question text-warning"></i>
                <span>How Referral Bonus Works</span>
            </h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="how-it-works-step">
                        <div class="step-number-pill">1</div>
                        <h6 class="fw-bold text-white mb-1">Share Your Code or Link</h6>
                        <p class="text-white small mb-0">Send your unique code or link to friends on WhatsApp, Telegram, or social media.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="how-it-works-step">
                        <div class="step-number-pill">2</div>
                        <h6 class="fw-bold text-white mb-1">Friend Registers & Deposits</h6>
                        <p class="text-white small mb-0">Your friend creates an account with your code and makes their first successful deposit.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="how-it-works-step">
                        <div class="step-number-pill">3</div>
                        <h6 class="fw-bold text-white mb-1">Receive Instant {{ $commissionPercent }}% Cash</h6>
                        <p class="text-white small mb-0">The moment their deposit is approved, {{ $commissionPercent }}% of the amount is automatically added to your wallet!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Referred Users Table (Child Users List) -->
    <div class="card border-0 rounded-4 overflow-hidden shadow-sm" style="background: #09172c; border: 1px solid rgba(255, 255, 255, 0.08) !important;">
        <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: #0b1a30; border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;">
            <div>
                <h5 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-users text-warning"></i>
                    <span>Your Referred Friends List</span>
                </h5>
                <div class="text-white small">All members who registered using your referral code ({{ $user->referral_code }})</div>
            </div>
            <div class="badge bg-warning text-white px-3 py-2 rounded-pill font-monospace fw-bold" style="color: #ffffff !important;">
                {{ $totalReferrals }} Joined
            </div>
        </div>

        <div class="table-responsive">
            <table class="table referral-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Player Name</th>
                        <th>Mobile Number</th>
                        <th>Joined Date</th>
                        <th>First Deposit Status</th>
                        <th class="text-end">Bonus Earned</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referrals as $index => $ref)
                        @php
                            // Mask phone: 9876543210 -> 98******10
                            $phone = $ref->phone;
                            $maskedPhone = strlen($phone) >= 6 
                                ? substr($phone, 0, 2) . str_repeat('*', max(4, strlen($phone) - 4)) . substr($phone, -2) 
                                : $phone;
                            $hasPaidCommission = $ref->paidReferralCommission !== null;
                        @endphp
                        <tr>
                            <td class="font-monospace text-white">{{ $referrals->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem; color: #ffffff !important;">
                                        {{ strtoupper(substr($ref->name ?: 'U', 0, 1)) }}
                                    </div>
                                    <div class="fw-bold text-white">{{ $ref->name }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="font-monospace text-white">{{ $maskedPhone }}</span>
                            </td>
                            <td>
                                <span class="text-white small">{{ $ref->created_at->format('d M Y, h:i A') }}</span>
                            </td>
                            <td>
                                @if($hasPaidCommission)
                                    <span class="badge bg-success text-white px-2 py-1 rounded-pill" style="color: #ffffff !important;">
                                        <i class="fa-solid fa-circle-check me-1"></i> ₹{{ number_format($ref->paidReferralCommission->deposit_amount, 2) }} Deposited
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-25 text-white px-2 py-1 rounded-pill" style="color: #ffffff !important;">
                                        <i class="fa-solid fa-clock me-1"></i> Deposit Pending
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($hasPaidCommission)
                                    <span class="fw-bolder text-success font-monospace" style="font-size: 0.95rem;">
                                        +₹{{ number_format($ref->paidReferralCommission->commission_amount, 2) }}
                                    </span>
                                    <div class="small text-white" style="font-size: 0.7rem;">({{ (float) $ref->paidReferralCommission->commission_percent }}%)</div>
                                @else
                                    <span class="text-white font-monospace">₹0.00</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-white mb-3" style="font-size: 2.8rem;">
                                    <i class="fa-solid fa-user-plus text-secondary opacity-50"></i>
                                </div>
                                <h6 class="fw-bold text-white mb-1">No Friends Have Joined Yet</h6>
                                <p class="text-white small mb-3" style="max-width: 420px; margin: 0 auto;">
                                    Share your referral link or code with your friends now. When they sign up and make their first deposit, you will see them here and earn {{ $commissionPercent }}% bonus!
                                </p>
                                <button type="button" class="btn btn-warning text-white btn-sm rounded-pill px-4 fw-bold shadow-sm" onclick="copyReferralLink()" style="color: #ffffff !important;">
                                    <i class="fa-regular fa-copy me-1"></i> Copy Referral Link
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($referrals->hasPages())
            <div class="card-footer border-0 p-3 d-flex justify-content-center" style="background: #0b1a30;">
                {{ $referrals->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
    function copyReferralCode() {
        const code = document.getElementById('referralCodeText').innerText.trim();
        navigator.clipboard.writeText(code).then(() => {
            const btn = document.getElementById('copyCodeBtn');
            const btnText = document.getElementById('copyCodeBtnText');
            btn.classList.add('btn-success');
            btn.classList.remove('btn-copy-code');
            btnText.innerHTML = '<i class="fa-solid fa-check"></i> Copied!';
            setTimeout(() => {
                btn.classList.remove('btn-success');
                btn.classList.add('btn-copy-code');
                btnText.innerHTML = 'Copy Code';
            }, 2500);
        }).catch(err => {
            alert('Referral Code: ' + code);
        });
    }

    function copyReferralLink() {
        const input = document.getElementById('referralLinkInput');
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(() => {
            const btnText = document.getElementById('copyLinkBtnText');
            btnText.innerHTML = '<i class="fa-solid fa-check"></i> Copied!';
            setTimeout(() => {
                btnText.innerHTML = 'Copy Link';
            }, 2500);
        }).catch(err => {
            alert('Referral Link: ' + input.value);
        });
    }
</script>
@endsection
