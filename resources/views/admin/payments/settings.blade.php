@extends('layouts.admin')

@section('title', 'System Settings & Support Contacts')
@section('page_title', 'System Settings, Payment Gateway & Support Contacts')

@section('content')
<div class="row g-4">
    <!-- Multi-Platform UPI QR Management Section -->
    <div class="col-12">
        <div class="card-custom shadow-sm mb-2">
            <div class="card-custom-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h6 class="card-custom-title mb-0">
                    <i class="fa-solid fa-qrcode text-primary"></i>
                    <span>Multi-Platform Deposit QR Codes (GPay, PhonePe, Paytm, BHIM, Navi & Other)</span>
                </h6>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill">
                    {{ $paymentQrs->count() }} Configured
                </span>
            </div>
            <div class="card-custom-body p-4">
                <div class="row g-4">
                    <!-- Form: Upload New Platform QR -->
                    <div class="col-lg-5 pe-lg-4 border-end-lg">
                        <div class="p-3 rounded-4 bg-light border">
                            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                <i class="fa-solid fa-cloud-arrow-up text-success"></i>
                                <span>Upload New Platform QR</span>
                            </h6>

                            <form method="POST" action="{{ route('admin.payments.qrs.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="platformSelect" class="form-label fw-bold small text-muted">Select Platform / UPI Provider <span class="text-danger">*</span></label>
                                    <select class="form-select fw-semibold" id="platformSelect" name="platform" required onchange="handlePlatformChange(this.value)">
                                        <option value="" disabled selected>-- Choose Platform --</option>
                                        <option value="gpay">Google Pay (GPay QR)</option>
                                        <option value="phonepe">PhonePe QR</option>
                                        <option value="paytm">Paytm QR</option>
                                        <option value="bhim">BHIM Pay QR</option>
                                        <option value="navi">Navi QR</option>
                                        <option value="other">Other (Custom UPI Platform)</option>
                                    </select>
                                </div>

                                <!-- Custom Platform Text Input (Shown dynamically when 'other' is selected) -->
                                <div class="mb-3 d-none" id="customPlatformBox">
                                    <label for="customPlatformInput" class="form-label fw-bold small text-muted">
                                        Specify Platform Name <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fa-solid fa-pen-nib text-primary"></i></span>
                                        <input type="text" class="form-control" id="customPlatformInput" name="custom_platform_name" placeholder="e.g. Amazon Pay, Cred, Super.money">
                                    </div>
                                    <div class="form-text small">Enter the exact name of the payment app or provider.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="qrUpiId" class="form-label fw-bold small text-muted">UPI ID for this QR <span class="text-muted">(Optional)</span></label>
                                    <input type="text" class="form-control font-monospace" id="qrUpiId" name="upi_id" placeholder="e.g. merchant@ybl or leave empty for default">
                                    <div class="form-text small">If empty, will use default UPI ID ({{ $setting->upi_id }}).</div>
                                </div>

                                <div class="mb-3">
                                    <label for="qrUpiName" class="form-label fw-bold small text-muted">Payee / Account Name <span class="text-muted">(Optional)</span></label>
                                    <input type="text" class="form-control" id="qrUpiName" name="upi_name" placeholder="e.g. RupeeRush Online Entertainment">
                                </div>

                                <div class="mb-3">
                                    <label for="newQrFile" class="form-label fw-bold small text-muted">Upload QR Image File <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="newQrFile" name="qr_code" accept="image/*" required onchange="previewNewPlatformQr(this)">
                                    <div class="form-text small">PNG, JPG, WEBP (Max 4MB). High-resolution image recommended.</div>

                                    <!-- Image Preview Container -->
                                    <div id="newQrPreviewBox" class="mt-2 text-center d-none p-2 bg-white rounded border">
                                        <img id="newQrPreviewImg" src="" alt="Preview" class="img-fluid rounded" style="max-height: 140px;">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm" style="background: #0077b6; border: none;">
                                    <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload & Add QR
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Existing QRs Grid -->
                    <div class="col-lg-7">
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center justify-content-between">
                            <span><i class="fa-solid fa-list-check text-primary me-2"></i>Configured Platform QRs</span>
                            <span class="small text-muted">Shown to players on the Deposit page</span>
                        </h6>

                        @if($paymentQrs->isEmpty())
                            <div class="text-center py-5 bg-light rounded-4 border">
                                <i class="fa-solid fa-qrcode fs-1 text-muted opacity-50 mb-2"></i>
                                <h6 class="fw-bold text-dark">No Platform QRs Added Yet</h6>
                                <p class="small text-muted mb-0">Upload GPay, PhonePe, Paytm, BHIM, Navi, or other QRs above so players can choose their platform.</p>
                            </div>
                        @else
                            <div class="row g-3">
                                @foreach($paymentQrs as $qr)
                                    <div class="col-md-6">
                                        <div class="card h-100 border {{ $qr->is_active ? 'border-primary-subtle shadow-sm' : 'border-secondary-subtle opacity-75' }} rounded-4 overflow-hidden">
                                            <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                                                <span class="badge px-2 py-1 rounded-pill fw-bold d-flex align-items-center gap-1" style="background-color: {{ $qr->badge_color }}; color: #fff;">
                                                    <i class="{{ $qr->icon_class }}"></i>
                                                    <span>{{ $qr->platform_name }}</span>
                                                </span>
                                                @if($qr->is_active)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle small rounded-pill">Active</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle small rounded-pill">Inactive</span>
                                                @endif
                                            </div>
                                            <div class="card-body p-3 text-center">
                                                <div class="p-2 bg-light rounded-3 d-inline-block border mb-2">
                                                    <img src="{{ $qr->qr_code_url }}" alt="{{ $qr->platform_name }}" class="img-fluid rounded" style="max-height: 120px; width: auto;">
                                                </div>
                                                <div class="small font-monospace fw-bold text-dark text-truncate">{{ $qr->upi_id ?: ($setting->upi_id ?? 'Default UPI') }}</div>
                                                <div class="small text-muted text-truncate">{{ $qr->upi_name ?: ($setting->upi_name ?? 'Default Payee') }}</div>
                                            </div>
                                            <div class="card-footer bg-light py-2 px-3 border-top d-flex align-items-center justify-content-between">
                                                <form method="POST" action="{{ route('admin.payments.qrs.toggle', $qr->id) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm {{ $qr->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                                                        <i class="fa-solid {{ $qr->is_active ? 'fa-eye-slash' : 'fa-eye' }} me-1"></i>
                                                        {{ $qr->is_active ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.payments.qrs.delete', $qr->id) }}" onsubmit="return confirm('Permanently delete {{ $qr->platform_name }} QR code?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                                                        <i class="fa-solid fa-trash me-1"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main System & Game Settings Column -->
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-custom-header">
                <h6 class="card-custom-title">
                    <i class="fa-solid fa-sliders text-primary"></i>
                    <span>Configure Site, UPI & Floating Support Contacts</span>
                </h6>
            </div>
            <div class="card-custom-body">
                <form method="POST" action="{{ route('admin.payments.settings.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="siteName" class="form-label fw-bold small text-muted">Portal / Site Name</label>
                            <input type="text" class="form-control" id="siteName" name="site_name" value="{{ old('site_name', $setting->site_name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="upiId" class="form-label fw-bold small text-muted">Official Merchant UPI ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace" id="upiId" name="upi_id" value="{{ old('upi_id', $setting->upi_id) }}" placeholder="e.g. merchant@upi" required>
                        </div>

                        <div class="col-md-6">
                            <label for="upiName" class="form-label fw-bold small text-muted">Payee / Merchant Business Name</label>
                            <input type="text" class="form-control" id="upiName" name="upi_name" value="{{ old('upi_name', $setting->upi_name) }}" placeholder="e.g. Official Lottery Payments">
                        </div>

                        <div class="col-md-6">
                            <label for="winMultiplier" class="form-label fw-bold small text-muted">Winning Multiplier (Slot Payout Rate)</label>
                            <div class="input-group">
                                <input type="number" step="0.5" min="1" class="form-control fw-bold" id="winMultiplier" name="win_multiplier" value="{{ old('win_multiplier', $setting->win_multiplier) }}" required>
                                <span class="input-group-text bg-light">x (e.g. 90x)</span>
                            </div>
                            <div class="form-text small">E.g., 90x means ₹10 bet pays gross ₹900 on winning.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="winCommissionPercent" class="form-label fw-bold small text-muted">
                                <i class="fa-solid fa-percent text-success me-1"></i> Admin Win Commission Deduction
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.5" min="0" max="100" class="form-control fw-bold text-success font-monospace" id="winCommissionPercent" name="win_commission_percent" value="{{ old('win_commission_percent', $setting->win_commission_percent ?? 10.00) }}" required>
                                <span class="input-group-text bg-success bg-opacity-10 text-success fw-bold">% Deduction</span>
                            </div>
                            <div class="form-text small">Deducted from winning profit across all games. E.g. 10% on 2x win (₹100 bet + ₹100 profit) gives ₹90 profit to player (₹190 total payout) and ₹10 to admin.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="houseProfitRate" class="form-label fw-bold small text-muted">
                                <i class="fa-solid fa-shield-halved text-primary me-1"></i> House Profit / Minimum Payout Target Ratio (%)
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.5" min="0" max="100" class="form-control fw-bold text-primary font-monospace" id="houseProfitRate" name="house_profit_rate" value="{{ old('house_profit_rate', $setting->house_profit_rate ?? 78.00) }}" required>
                                <span class="input-group-text bg-primary bg-opacity-10 text-primary fw-bold">% House Win</span>
                            </div>
                            <div class="form-text small">Standard 75% - 80% (default 78%). House forces minimum payout in 78% of rounds, leaving 22% natural wins to maintain player trust and retention.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="referralCommissionPercent" class="form-label fw-bold small text-muted">
                                <i class="fa-solid fa-users text-warning me-1"></i> First Deposit Referral Bonus to Parent
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.1" min="0" max="100" class="form-control fw-bold text-dark font-monospace" id="referralCommissionPercent" name="referral_commission_percent" value="{{ old('referral_commission_percent', $setting->referral_commission_percent ?? 1.00) }}" required>
                                <span class="input-group-text bg-warning bg-opacity-10 text-dark fw-bold">% Bonus</span>
                            </div>
                            <div class="form-text small">Credited to the parent user when a referred child makes their 1st approved deposit. (Default: 1.00%)</div>
                        </div>

                        <div class="col-md-3">
                            <label for="minDeposit" class="form-label fw-bold small text-muted">Minimum Deposit (₹)</label>
                            <input type="number" step="1" min="1" class="form-control" id="minDeposit" name="min_deposit" value="{{ old('min_deposit', $setting->min_deposit) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label for="maxDeposit" class="form-label fw-bold small text-muted">Maximum Deposit (₹)</label>
                            <input type="number" step="1" min="1" class="form-control" id="maxDeposit" name="max_deposit" value="{{ old('max_deposit', $setting->max_deposit) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label for="minWithdrawal" class="form-label fw-bold small text-muted">Minimum Withdrawal (₹)</label>
                            <input type="number" step="1" min="1" class="form-control" id="minWithdrawal" name="min_withdrawal" value="{{ old('min_withdrawal', $setting->min_withdrawal) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label for="lockMins" class="form-label fw-bold small text-muted">Betting Cutoff (Lock Mins)</label>
                            <div class="input-group">
                                <input type="number" min="1" max="10" class="form-control" id="lockMins" name="betting_lock_minutes" value="{{ old('betting_lock_minutes', $setting->betting_lock_minutes) }}" required>
                                <span class="input-group-text bg-light">Mins</span>
                            </div>
                        </div>

                        <!-- Direct Bank Account Transfer for Player Deposit (Optional) -->
                        <div class="col-12 mt-3 pt-3 border-top">
                            <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                                <i class="fa-solid fa-building-columns text-primary"></i>
                                <span>Official Bank Account Transfer Details (Optional)</span>
                            </h6>
                            <p class="small text-muted mb-3">If filled, these bank details will appear on the player Deposit page so players can also pay via direct Bank Transfer (IMPS/NEFT) in addition to UPI.</p>
                        </div>

                        <div class="col-md-6">
                            <label for="businessName" class="form-label fw-bold small text-muted">
                                Business / Account Holder Name <span class="text-muted">(Optional)</span>
                            </label>
                            <input type="text" class="form-control" id="businessName" name="business_name" value="{{ old('business_name', $setting->business_name ?? $setting->upi_name) }}" placeholder="e.g. RupeeRush Online Entertainment">
                            <div class="form-text small">Name registered with the bank account / business.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="bankName" class="form-label fw-bold small text-muted">
                                Bank Name <span class="text-muted">(Optional)</span>
                            </label>
                            <input type="text" class="form-control" id="bankName" name="bank_name" value="{{ old('bank_name', $setting->bank_name) }}" placeholder="e.g. State Bank of India / HDFC Bank">
                            <div class="form-text small">Name of the bank.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="bankAccountNumber" class="form-label fw-bold small text-muted">
                                Bank Account Number <span class="text-muted">(Optional)</span>
                            </label>
                            <input type="text" class="form-control font-monospace" id="bankAccountNumber" name="bank_account_number" value="{{ old('bank_account_number', $setting->bank_account_number) }}" placeholder="e.g. 50100234567890">
                            <div class="form-text small">Player will transfer deposit amount to this account number.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="bankIfsc" class="form-label fw-bold small text-muted">
                                Bank IFSC Code <span class="text-muted">(Optional)</span>
                            </label>
                            <input type="text" class="form-control font-monospace text-uppercase" id="bankIfsc" name="bank_ifsc" value="{{ old('bank_ifsc', $setting->bank_ifsc) }}" placeholder="e.g. HDFC0001234" style="text-transform: uppercase;">
                            <div class="form-text small">11-character Indian Financial System Code.</div>
                        </div>

                        <!-- Betting Amount Controlling For Both Games -->
                        <div class="col-12 mt-3 pt-3 border-top">
                            <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-2">
                                <i class="fa-solid fa-gamepad text-primary"></i>
                                <span>Betting Amount Controlling (Both Games)</span>
                            </h6>
                            <p class="small text-muted mb-3">Control real-time minimum and maximum allowed bet stake values for players on both games.</p>
                        </div>

                        <!-- 2-Digit Lottery Bet Controls -->
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border bg-light h-100">
                                <div class="fw-bold text-primary small text-uppercase mb-2">
                                    <i class="fa-solid fa-ticket me-1"></i> 2-Digit Slot Game Limits
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label for="lotteryMinBet" class="form-label fw-bold small text-muted">Min Bet (₹)</label>
                                        <input type="number" step="1" min="1" class="form-control fw-bold font-monospace" id="lotteryMinBet" name="lottery_min_bet" value="{{ old('lottery_min_bet', $setting->lottery_min_bet ?? 10) }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="lotteryMaxBet" class="form-label fw-bold small text-muted">Max Bet (₹)</label>
                                        <input type="number" step="10" min="1" class="form-control fw-bold font-monospace" id="lotteryMaxBet" name="lottery_max_bet" value="{{ old('lottery_max_bet', $setting->lottery_max_bet ?? 10000) }}" required>
                                    </div>
                                </div>
                                <div class="form-text small mt-2">Applies to all slots (XA–XJ). Players cannot bet below min or above max.</div>
                            </div>
                        </div>

                        <!-- WinGo Color Game Bet Controls -->
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border bg-light h-100">
                                <div class="fw-bold text-danger small text-uppercase mb-2">
                                    <i class="fa-solid fa-palette me-1"></i> WinGo Color Game Limits
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label for="wingoMinBet" class="form-label fw-bold small text-muted">Min Bet (₹)</label>
                                        <input type="number" step="1" min="1" class="form-control fw-bold font-monospace" id="wingoMinBet" name="wingo_min_bet" value="{{ old('wingo_min_bet', $setting->wingo_min_bet ?? 1) }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="wingoMaxBet" class="form-label fw-bold small text-muted">Max Bet (₹)</label>
                                        <input type="number" step="50" min="1" class="form-control fw-bold font-monospace" id="wingoMaxBet" name="wingo_max_bet" value="{{ old('wingo_max_bet', $setting->wingo_max_bet ?? 50000) }}" required>
                                    </div>
                                </div>
                                <div class="form-text small mt-2">Applies to WinGo rounds (30s, 1m, 3m, 5m for colors, numbers, size).</div>
                            </div>
                        </div>

                        <!-- Quick Link to Dedicated Support Contacts Page -->
                        <div class="col-12 mt-4" id="support-settings">
                            <div class="p-3 rounded-4 border d-flex flex-wrap align-items-center justify-content-between gap-3 shadow-sm" style="background: rgba(34, 197, 94, 0.08); border-color: rgba(34, 197, 94, 0.3) !important;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.25rem;">
                                        <i class="fa-solid fa-headset"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">Website Floating Support Settings (WhatsApp & Email)</h6>
                                        <div class="small text-muted">
                                            Current WhatsApp: <strong class="text-success font-monospace">{{ $setting->support_whatsapp ?? '9026217223' }}</strong> &bull; Email: <strong class="text-primary">{{ $setting->support_email ?? 'red247.online@gmail.com' }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('admin.support.index') }}" class="btn btn-success rounded-pill fw-bold px-4 py-2 shadow-sm">
                                    <i class="fa-solid fa-headset me-2"></i> Manage Support Contacts Page &rarr;
                                </a>
                            </div>
                        </div>

                        <!-- Hero Banners Section (Swiper 3 Images) -->
                        <div class="col-12 mt-4 pt-3 border-top">
                            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-images text-warning"></i>
                                        <span>Gaming Lobby Hero Banner Swiper (3 Carousel Images)</span>
                                    </h6>
                                    <p class="small text-muted mb-0">Manage the 3 banners displayed in the top Swiper carousel on the main lobby (<a href="{{ url('/') }}" target="_blank" class="fw-bold text-decoration-none">View Lobby <i class="fa-solid fa-arrow-up-right-from-square"></i></a>). Recommended ratio: <strong>3:1</strong> (e.g. 1200x400 px or 1920x640 px).</p>
                                </div>
                                <span class="badge bg-warning-subtle text-dark border border-warning px-3 py-1 rounded-pill">
                                    <i class="fa-solid fa-sliders me-1"></i> 3 Slides Carousel
                                </span>
                            </div>

                            <div class="row g-3">
                                @for($i = 1; $i <= 3; $i++)
                                    @php
                                        $bannerProp = "hero_banner_{$i}";
                                        $linkProp = "hero_banner_{$i}_link";
                                        $bannerUrl = $setting->getHeroBannerUrl($i);
                                        $isCustom = $setting->hasCustomBanner($i);
                                        $defaultLinks = [
                                            1 => route('lottery.index'),
                                            2 => route('wingo.index'),
                                            3 => route('wallet.deposit'),
                                        ];
                                        $defaultLabels = [
                                            1 => 'Slide 1: 2-Digit Lottery (XA–XJ 90X)',
                                            2 => 'Slide 2: WinGo Color Slot Game (30s Live)',
                                            3 => 'Slide 3: Instant 30s UPI Deposit & Cashout',
                                        ];
                                    @endphp
                                    <div class="col-12">
                                        <div class="p-3 bg-light rounded-4 border">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                                    <span class="badge bg-primary rounded-pill px-2 py-1">Banner #{{ $i }}</span>
                                                    <span>{{ $defaultLabels[$i] }}</span>
                                                </div>
                                                @if($isCustom)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small">
                                                        <i class="fa-solid fa-check-circle me-1"></i> Custom Image Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-muted border px-2 py-1 rounded-pill small">
                                                        <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Default Banner Active
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="row g-3 align-items-center">
                                                <!-- Preview -->
                                                <div class="col-md-5">
                                                    <div class="position-relative rounded-3 overflow-hidden border shadow-sm bg-dark" style="aspect-ratio: 3/1; max-height: 140px;">
                                                        <img id="bannerPreviewImg_{{ $i }}" src="{{ $bannerUrl }}" alt="Banner {{ $i }}" class="w-100 h-100 object-fit-cover">
                                                    </div>
                                                </div>

                                                <!-- Controls -->
                                                <div class="col-md-7">
                                                    <div class="mb-2">
                                                        <label class="form-label fw-bold small text-muted mb-1">
                                                            Upload New Image (JPG, PNG, WEBP - Max 5MB)
                                                        </label>
                                                        <input type="file" class="form-control form-control-sm" name="hero_banner_{{ $i }}" accept="image/*" onchange="previewHeroBanner(this, {{ $i }})">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label fw-bold small text-muted mb-1">
                                                            Target Click Link (URL / Route)
                                                        </label>
                                                        <input type="text" class="form-control form-control-sm font-monospace" name="hero_banner_{{ $i }}_link" value="{{ old($linkProp, $setting->{$linkProp} ?? $defaultLinks[$i]) }}" placeholder="e.g. {{ $defaultLinks[$i] }}">
                                                    </div>
                                                    @if($isCustom)
                                                        <div class="form-check form-check-inline mt-1">
                                                            <input class="form-check-input text-danger" type="checkbox" name="remove_banner_{{ $i }}" id="removeBanner_{{ $i }}" value="1">
                                                            <label class="form-check-label small text-danger fw-bold" for="removeBanner_{{ $i }}">
                                                                <i class="fa-solid fa-trash-can me-1"></i> Remove custom image &amp; reset to default
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Upload QR Code File -->
                        <div class="col-12 mt-4 pt-3 border-top">
                            <label for="qrCodeInput" class="form-label fw-bold text-dark">
                                <i class="fa-solid fa-cloud-arrow-up text-primary me-1"></i> Upload UPI QR Code Image
                            </label>
                            <input type="file" class="form-control" id="qrCodeInput" name="qr_code" accept="image/*" onchange="previewQR(this)">
                            <div class="form-text small">Upload your PhonePe, Google Pay, or Paytm merchant QR image (PNG, JPG, WEBP - Max 4MB). Users will scan this QR to deposit.</div>
                        </div>

                        <div class="col-12">
                            <label for="instructions" class="form-label fw-bold small text-muted">Deposit Instructions for Players</label>
                            <textarea class="form-control" id="instructions" name="instructions" rows="3">{{ old('instructions', $setting->instructions) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm" style="background: #0077b6; border: none;">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Save & Update Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Live QR Preview & Floating Support Preview -->
    <div class="col-lg-4">
        <!-- Live QR Preview Card -->
        <div class="card-custom text-center mb-4">
            <div class="card-custom-header justify-content-center">
                <h6 class="card-custom-title">
                    <i class="fa-solid fa-eye text-info"></i>
                    <span>Player Deposit QR Preview</span>
                </h6>
            </div>
            <div class="card-custom-body">
                <div class="p-3 bg-light rounded-4 border d-inline-block shadow-sm mb-3">
                    <img id="qrPreviewImg" src="{{ $setting->qr_code_url }}" alt="QR Preview" class="img-fluid rounded-3" style="max-height: 200px; width: auto;" />
                </div>

                <div class="fw-bold text-dark font-monospace mb-1">{{ $setting->upi_id ?? 'Not set' }}</div>
                <div class="text-muted small mb-2">{{ $setting->upi_name ?? 'Lottery' }}</div>

                <div class="alert alert-light border small text-muted text-start mb-0">
                    <i class="fa-solid fa-circle-check text-success me-1"></i> Players see this QR code immediately when they open the Deposit screen.
                </div>
            </div>
        </div>

        <!-- Link to Dedicated Support Contacts Page -->
        <div class="card-custom">
            <div class="card-custom-body text-center p-4">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; font-size: 1.5rem;">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">WhatsApp & Email Support</h6>
                <p class="small text-muted mb-3">Manage floating WhatsApp number and support email on the dedicated settings page.</p>
                <a href="{{ route('admin.support.index') }}" class="btn btn-outline-success rounded-pill fw-bold px-4 py-2 w-100">
                    <i class="fa-solid fa-gear me-1"></i> Open Support Settings
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function handlePlatformChange(val) {
        const box = document.getElementById('customPlatformBox');
        const input = document.getElementById('customPlatformInput');
        if (val === 'other') {
            box.classList.remove('d-none');
            input.setAttribute('required', 'required');
            input.focus();
        } else {
            box.classList.add('d-none');
            input.removeAttribute('required');
            input.value = '';
        }
    }

    function previewNewPlatformQr(input) {
        const container = document.getElementById('newQrPreviewBox');
        const img = document.getElementById('newQrPreviewImg');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                container.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewQR(input) {
        const preview = document.getElementById('qrPreviewImg');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewHeroBanner(input, index) {
        const preview = document.getElementById('bannerPreviewImg_' + index);
        if (input.files && input.files[0] && preview) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
