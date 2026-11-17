@extends('layouts.app')

@section('title', 'Deposit Funds via QR - Lottery Portal')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        
        <!-- Left Column: Admin QR Code & UPI Details -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="p-4 text-white text-center" style="background: linear-gradient(135deg, #09192f 0%, #153965 100%);">
                    <div class="d-inline-flex p-3 rounded-circle bg-white bg-opacity-10 mb-2">
                        <i class="fa-solid fa-qrcode text-info fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Scan & Pay via UPI</h5>
                    <p class="text-white-50 small mb-0">Use GPay, PhonePe, Paytm, or any UPI App</p>
                </div>

                <div class="card-body p-4 text-center">
                    
                    <!-- Choose Platform Section -->
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold text-dark small d-flex align-items-center justify-content-between mb-2">
                            <span><i class="fa-solid fa-layer-group text-primary me-1"></i> 1. Choose Payment Platform:</span>
                            <span class="badge bg-primary-subtle text-primary small px-2 py-1 rounded-pill" id="selectedPlatformBadge">
                                {{ $paymentQrs->first()?->platform_name ?? 'Default UPI' }}
                            </span>
                        </label>

                        @if($paymentQrs->isNotEmpty())
                            <div class="d-flex flex-wrap gap-2 justify-content-center" id="platformPillsContainer">
                                @foreach($paymentQrs as $idx => $qr)
                                    <button type="button" 
                                            class="btn btn-sm rounded-pill fw-semibold platform-btn px-3 py-2 d-flex align-items-center gap-2 {{ $idx === 0 ? 'btn-primary active shadow-sm' : 'btn-outline-secondary' }}" 
                                            data-id="{{ $qr->id }}"
                                            data-name="{{ $qr->platform_name }}"
                                            data-qr="{{ $qr->qr_code_url }}"
                                            data-upi="{{ $qr->upi_id ?: ($setting->upi_id ?? 'lottery@upi') }}"
                                            data-payee="{{ $qr->upi_name ?: ($setting->business_name ?? $setting->upi_name ?? 'Lottery') }}"
                                            onclick="selectPaymentPlatform(this)">
                                        <i class="{{ $qr->icon_class }}"></i>
                                        <span>{{ $qr->platform_name }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="fa-brands fa-google-pay text-primary me-1"></i> GPay</span>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="fa-solid fa-mobile-screen-button text-purple me-1"></i> PhonePe</span>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="fa-solid fa-wallet text-info me-1"></i> Paytm</span>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="fa-solid fa-building-columns text-success me-1"></i> BHIM</span>
                            </div>
                        @endif
                    </div>

                    <!-- QR Code Image Box -->
                    <div class="p-3 bg-light rounded-4 d-inline-block border shadow-sm mb-2">
                        <img id="activeQrImage" src="{{ $paymentQrs->first()?->qr_code_url ?? $setting->qr_code_url }}" alt="UPI QR Code" class="img-fluid rounded-3" style="max-height: 250px; width: auto;" />
                    </div>

                    <div class="small text-muted mb-3">
                        <span class="badge bg-light text-dark border px-2 py-1">
                            <i class="fa-solid fa-shield-halved text-success me-1"></i> Payee: <strong id="activePayeeText">{{ $paymentQrs->first()?->upi_name ?? ($setting->business_name ?? $setting->upi_name ?? 'Official Merchant') }}</strong>
                        </span>
                    </div>

                    <!-- UPI ID Display & Copy Button -->
                    <div class="bg-light p-3 rounded-3 border d-flex align-items-center justify-content-between mb-3">
                        <div class="text-start">
                            <div class="text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Official UPI ID</div>
                            <div class="fw-bold font-monospace text-dark" id="upiIdText">{{ $paymentQrs->first()?->upi_id ?? ($setting->upi_id ?? 'lottery@upi') }}</div>
                        </div>
                        <button class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="copyUpiId()" id="btnCopyUpi">
                            <i class="fa-regular fa-copy me-1"></i> Copy
                        </button>
                    </div>

                    <!-- Direct Bank Transfer Option (Shown only if admin configured Bank Details) -->
                    @if($setting->hasBankDetails())
                        <div class="card border border-primary-subtle rounded-3 text-start mb-3 bg-light shadow-sm">
                            <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                                <span class="fw-bold small text-primary">
                                    <i class="fa-solid fa-building-columns me-1"></i> Bank Account Transfer (IMPS/NEFT)
                                </span>
                                <span class="badge bg-success-subtle text-success small">Available</span>
                            </div>
                            <div class="p-3">
                                @if($setting->business_name || $setting->upi_name)
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted small">Business / Payee:</span>
                                        <div class="d-flex align-items-center">
                                            <strong class="text-dark small me-1">{{ $setting->business_name ?? $setting->upi_name }}</strong>
                                            <button type="button" class="btn btn-link btn-sm p-0 text-muted" onclick="copyText('{{ addslashes($setting->business_name ?? $setting->upi_name) }}', this)" title="Copy Name">
                                                <i class="fa-regular fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                @if($setting->bank_name)
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted small">Bank Name:</span>
                                        <strong class="text-dark small">{{ $setting->bank_name }}</strong>
                                    </div>
                                @endif

                                @if($setting->bank_account_number)
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted small">Account Number:</span>
                                        <div class="d-flex align-items-center">
                                            <strong class="text-dark font-monospace me-1">{{ $setting->bank_account_number }}</strong>
                                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill py-0 px-2" style="font-size: 0.72rem;" onclick="copyText('{{ $setting->bank_account_number }}', this)">
                                                <i class="fa-regular fa-copy me-1"></i> Copy
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                @if($setting->bank_ifsc)
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">IFSC Code:</span>
                                        <div class="d-flex align-items-center">
                                            <strong class="text-dark font-monospace text-uppercase me-1">{{ $setting->bank_ifsc }}</strong>
                                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill py-0 px-2" style="font-size: 0.72rem;" onclick="copyText('{{ $setting->bank_ifsc }}', this)">
                                                <i class="fa-regular fa-copy me-1"></i> Copy
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="row g-2 text-center small mb-3">
                        <div class="col-6">
                            <div class="p-2 border rounded-3 bg-light">
                                <div class="text-muted" style="font-size: 0.7rem;">MIN DEPOSIT</div>
                                <div class="fw-bold text-success">₹{{ number_format($setting->min_deposit, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded-3 bg-light">
                                <div class="text-muted" style="font-size: 0.7rem;">MAX DEPOSIT</div>
                                <div class="fw-bold text-primary">₹{{ number_format($setting->max_deposit, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Step-by-Step Instructions -->
                    <div class="text-start alert alert-info border-0 rounded-3 p-3 small mb-0">
                        <div class="fw-bold mb-1"><i class="fa-solid fa-list-check me-1"></i> How to Deposit:</div>
                        <ol class="ps-3 mb-0">
                            <li>Scan QR, copy UPI ID, or use Bank Transfer details above.</li>
                            <li>Pay using any UPI App (PhonePe, GPay, Paytm) or NetBanking (IMPS/NEFT).</li>
                            <li>Copy the <strong>12-digit UTR / Reference No.</strong> from your payment receipt.</li>
                            <li>Take a screenshot of the payment receipt.</li>
                            <li>Fill the form on the right and click <strong>Submit Deposit</strong>.</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        <!-- Right Column: Submission Form & Recent History -->
        <div class="col-lg-7">
            <!-- Form Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="fa-solid fa-receipt text-success"></i>
                        <span>Submit Deposit Details</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('wallet.deposit.submit') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Hidden fields storing selected platform QR -->
                        <input type="hidden" id="formPaymentQrId" name="payment_qr_id" value="{{ $paymentQrs->first()?->id }}">
                        <input type="hidden" id="formPaymentPlatform" name="payment_platform" value="{{ $paymentQrs->first()?->platform_name ?? 'UPI' }}">

                        <!-- Selected Platform Banner -->
                        <div class="alert alert-light border border-primary-subtle d-flex align-items-center justify-content-between p-2 mb-3 rounded-3 small">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary px-2 py-1 rounded-pill"><i class="fa-solid fa-qrcode me-1"></i> Mode</span>
                                <span class="text-muted">Selected QR:</span>
                                <strong class="text-dark" id="formSelectedPlatformDisplay">{{ ($paymentQrs->first()?->platform_name ?? 'Default UPI') . ' QR' }}</strong>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                <i class="fa-solid fa-shield-halved me-1"></i> Verified
                            </span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="depositAmount" class="form-label fw-bold small text-muted">Amount Paid (₹) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">₹</span>
                                    <input type="number" step="1" min="{{ $setting->min_deposit }}" max="{{ $setting->max_deposit }}" class="form-control form-control-lg bg-light @error('amount') is-invalid @enderror" id="depositAmount" name="amount" placeholder="e.g. 500" value="{{ old('amount') }}" required>
                                </div>
                                <div class="form-text small">Min: ₹{{ $setting->min_deposit }} | Max: ₹{{ $setting->max_deposit }}</div>
                                @error('amount')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="utrNumber" class="form-label fw-bold small text-muted">12-Digit UTR / Ref Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-barcode"></i></span>
                                    <input type="text" class="form-control form-control-lg bg-light font-monospace @error('utr_number') is-invalid @enderror" id="utrNumber" name="utr_number" placeholder="e.g. 429188219012" value="{{ old('utr_number') }}" required>
                                </div>
                                <div class="form-text small">Found in your UPI payment receipt.</div>
                                @error('utr_number')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="screenshotInput" class="form-label fw-bold small text-muted">Upload Payment Screenshot <span class="text-danger">*</span></label>
                                <input type="file" class="form-control bg-light @error('screenshot') is-invalid @enderror" id="screenshotInput" name="screenshot" accept="image/*" onchange="previewScreenshot(this)" required>
                                <div class="form-text small">Accepted formats: JPG, PNG, WEBP (Max 4MB).</div>
                                @error('screenshot')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror

                                <!-- Image Preview Container -->
                                <div id="previewContainer" class="mt-2 d-none">
                                    <img id="imagePreview" src="" alt="Screenshot Preview" class="img-thumbnail rounded-3" style="max-height: 180px;">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-bold shadow-sm" style="background: #10b981; border: none; font-size: 1rem;">
                                <i class="fa-solid fa-paper-plane me-2"></i> Submit Deposit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Deposits Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-clock-rotate-left me-1 text-primary"></i> Recent Deposit Requests
                    </h6>
                    <a href="{{ route('wallet.transactions') }}" class="small text-decoration-none fw-bold text-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-muted">
                            <tr>
                                <th>Date & Time</th>
                                <th>Amount</th>
                                <th>UTR Number</th>
                                <th>QR Platform</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentDeposits as $dep)
                                <tr>
                                    <td class="small">{{ $dep->created_at->timezone('Asia/Kolkata')->format('d M, h:i A') }}</td>
                                    <td class="fw-bold text-success font-monospace">+₹{{ number_format($dep->amount, 2) }}</td>
                                    <td class="font-monospace small text-muted">{{ $dep->utr_number }}</td>
                                    <td>
                                        <span class="badge bg-light text-primary border border-primary-subtle rounded-pill px-2 py-1 small">
                                            <i class="fa-solid fa-qrcode me-1"></i> {{ $dep->payment_method ?? 'UPI' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($dep->status === 'approved')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                                <i class="fa-solid fa-check me-1"></i> Approved
                                            </span>
                                        @elseif($dep->status === 'rejected')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill" title="{{ $dep->admin_notes }}">
                                                <i class="fa-solid fa-xmark me-1"></i> Rejected
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill">
                                                <i class="fa-solid fa-hourglass-half me-1"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted small">
                                        No recent deposits found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    function selectPaymentPlatform(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        const qrUrl = btn.getAttribute('data-qr');
        const upiId = btn.getAttribute('data-upi');
        const payee = btn.getAttribute('data-payee');

        // Update active button state
        document.querySelectorAll('.platform-btn').forEach(b => {
            b.classList.remove('btn-primary', 'active', 'shadow-sm');
            b.classList.add('btn-outline-secondary');
        });
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-primary', 'active', 'shadow-sm');

        // Update QR Preview, Payee, and UPI
        const qrImg = document.getElementById('activeQrImage');
        if (qrImg && qrUrl) {
            qrImg.src = qrUrl;
        }

        const upiElem = document.getElementById('upiIdText');
        if (upiElem && upiId) {
            upiElem.textContent = upiId;
        }

        const payeeElem = document.getElementById('activePayeeText');
        if (payeeElem && payee) {
            payeeElem.textContent = payee;
        }

        const badgeElem = document.getElementById('selectedPlatformBadge');
        if (badgeElem && name) {
            badgeElem.textContent = name;
        }

        const formDisplay = document.getElementById('formSelectedPlatformDisplay');
        if (formDisplay && name) {
            formDisplay.textContent = name + ' QR';
        }

        // Set form hidden input values
        const hiddenId = document.getElementById('formPaymentQrId');
        if (hiddenId) {
            hiddenId.value = id;
        }

        const hiddenPlatform = document.getElementById('formPaymentPlatform');
        if (hiddenPlatform) {
            hiddenPlatform.value = name;
        }
    }

    function copyUpiId() {
        const text = document.getElementById('upiIdText').innerText;
        navigator.clipboard.writeText(text).then(() => {
            const btn = document.getElementById('btnCopyUpi');
            btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Copied!';
            btn.classList.replace('btn-outline-primary', 'btn-success');
            setTimeout(() => {
                btn.innerHTML = '<i class="fa-regular fa-copy me-1"></i> Copy';
                btn.classList.replace('btn-success', 'btn-outline-primary');
            }, 2500);
        });
    }

    function copyText(text, btn) {
        if (!navigator.clipboard) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            showCopied(btn);
            return;
        }
        navigator.clipboard.writeText(text).then(() => {
            showCopied(btn);
        });
    }

    function showCopied(btn) {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check me-1 text-success"></i> Copied!';
        setTimeout(() => {
            btn.innerHTML = originalHtml;
        }, 2200);
    }

    function previewScreenshot(input) {
        const container = document.getElementById('previewContainer');
        const preview = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
