@extends('layouts.app')

@section('title', 'Player Registration - rupeerush')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-7">

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm p-3 mb-4 small" role="alert">
                    <i class="fa-solid fa-circle-check me-2 text-success"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm p-3 mb-4" role="alert">
                    <div class="fw-bold mb-1 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-danger"></i> Please check the form errors below:
                    </div>
                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <!-- Header Banner -->
                <div class="p-4 text-center text-white position-relative" style="background: linear-gradient(135deg, #09192f 0%, #153965 100%);">
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill mb-2 small fw-bold">
                        <i class="fa-solid fa-user-plus me-1"></i> NEW PLAYER ONBOARDING
                    </span>
                    <h3 class="fw-bold mb-1">Create Player Account</h3>
                    <p class="text-white-50 small mb-0">Self-register with ₹100 deposit or register directly on WhatsApp</p>
                </div>

                <!-- Registration Choice Nav Pills -->
                <div class="p-3 bg-light border-bottom">
                    <ul class="nav nav-pills nav-fill gap-2" id="regTypeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill fw-bold py-2 d-flex align-items-center justify-content-center gap-2" id="self-reg-tab" data-bs-toggle="pill" data-bs-target="#self-reg-pane" type="button" role="tab" aria-controls="self-reg-pane" aria-selected="true">
                                <i class="fa-solid fa-bolt text-warning"></i>
                                <span>Self Registration (Min ₹100)</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill fw-bold py-2 d-flex align-items-center justify-content-center gap-2" id="whatsapp-reg-tab" data-bs-toggle="pill" data-bs-target="#whatsapp-reg-pane" type="button" role="tab" aria-controls="whatsapp-reg-pane" aria-selected="false">
                                <i class="fa-brands fa-whatsapp text-success"></i>
                                <span>Register via WhatsApp</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">
                    <div class="tab-content" id="regTypeTabsContent">
                        <!-- ========================================================
                             OPTION 1: SELF REGISTRATION WITH MIN ₹100 DEPOSIT
                             ======================================================== -->
                        <div class="tab-pane fade show active" id="self-reg-pane" role="tabpanel" aria-labelledby="self-reg-tab" tabindex="0">
                            <!-- Min Deposit Policy Notice -->
                            <div class="alert alert-info border-0 rounded-4 p-3 mb-4 d-flex align-items-start gap-3 shadow-sm" style="background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; font-size: 1.1rem;">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <div class="small">
                                    <strong class="text-dark d-block mb-1">Minimum ₹100 Initial Deposit Policy:</strong>
                                    <span class="text-muted">
                                        To prevent automated spam accounts, self-registration requires an initial deposit of <strong>₹100 (or more)</strong>. 
                                        <strong>100% of this amount is credited to your player wallet</strong> to play all 11 games immediately upon verification!
                                    </span>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('register.post') }}" enctype="multipart/form-data" id="selfRegisterForm">
                                @csrf

                                <!-- STEP 1: PERSONAL INFORMATION -->
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                        <span class="badge bg-primary rounded-circle px-2 py-1">1</span>
                                        <span>Personal Account Details</span>
                                    </h6>

                                    <div class="row g-3">
                                        <!-- Full Name -->
                                        <div class="col-12 col-md-6">
                                            <label for="nameInput" class="form-label fw-bold small text-muted">Full Name <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-user"></i></span>
                                                <input type="text" 
                                                       class="form-control bg-light border-start-0 py-2 @error('name') is-invalid @enderror" 
                                                       id="nameInput" 
                                                       name="name" 
                                                       value="{{ old('name') }}" 
                                                       placeholder="Enter your full name" 
                                                       required>
                                            </div>
                                            @error('name')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Mobile Number -->
                                        <div class="col-12 col-md-6">
                                            <label for="phoneInput" class="form-label fw-bold small text-muted">Mobile Number (10 Digits) <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 fw-bold text-muted">+91</span>
                                                <input type="tel" 
                                                       class="form-control bg-light border-start-0 py-2 @error('phone') is-invalid @enderror" 
                                                       id="phoneInput" 
                                                       name="phone" 
                                                       value="{{ old('phone') }}" 
                                                       placeholder="98XXXXXXXX" 
                                                       maxlength="10" 
                                                       pattern="[6-9][0-9]{9}" 
                                                       required>
                                            </div>
                                            @error('phone')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Password -->
                                        <div class="col-12 col-md-6">
                                            <label for="regPassword" class="form-label fw-bold small text-muted">Create Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                                                <input type="password" 
                                                       class="form-control bg-light border-start-0 border-end-0 py-2 @error('password') is-invalid @enderror" 
                                                       id="regPassword" 
                                                       name="password" 
                                                       placeholder="Min 6 characters" 
                                                       required>
                                                <button type="button" class="input-group-text bg-light border-start-0 text-muted" onclick="togglePasswordVisibility('regPassword', this)">
                                                    <i class="fa-regular fa-eye"></i>
                                                </button>
                                            </div>
                                            @error('password')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="col-12 col-md-6">
                                            <label for="regPasswordConfirm" class="form-label fw-bold small text-muted">Confirm Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock-open"></i></span>
                                                <input type="password" 
                                                       class="form-control bg-light border-start-0 border-end-0 py-2" 
                                                       id="regPasswordConfirm" 
                                                       name="password_confirmation" 
                                                       placeholder="Re-enter password" 
                                                       required>
                                                <button type="button" class="input-group-text bg-light border-start-0 text-muted" onclick="togglePasswordVisibility('regPasswordConfirm', this)">
                                                    <i class="fa-regular fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Optional Referral Code -->
                                        <div class="col-12">
                                            <label for="referralCodeInput" class="form-label fw-bold small text-muted">
                                                Referral Code <span class="text-muted fw-normal">(Optional)</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 text-warning"><i class="fa-solid fa-gift"></i></span>
                                                <input type="text" 
                                                       class="form-control bg-light border-start-0 font-monospace text-uppercase py-2" 
                                                       id="referralCodeInput" 
                                                       name="referral_code" 
                                                       value="{{ old('referral_code', $referralCode) }}" 
                                                       placeholder="e.g. RR1234">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- STEP 2: INITIAL ₹100 DEPOSIT PAYMENT -->
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                        <span class="badge bg-success rounded-circle px-2 py-1">2</span>
                                        <span>Initial ₹100 Deposit Payment</span>
                                    </h6>
                                    <p class="text-muted small mb-3">Scan any official UPI QR below &amp; pay minimum ₹100 via Google Pay, PhonePe, Paytm, or BHIM.</p>

                                    <!-- QR Code Selection Pills -->
                                    @php
                                        $primaryQr = $paymentQrs->first();
                                    @endphp

                                    @if($paymentQrs->isNotEmpty())
                                        <div class="row g-2 mb-3">
                                            @foreach($paymentQrs as $qr)
                                                <div class="col-4">
                                                    <label class="d-block p-2 border rounded-3 text-center cursor-pointer qr-select-card {{ $loop->first ? 'border-primary bg-primary bg-opacity-10' : 'bg-light' }}" style="cursor: pointer;" onclick="selectPaymentQr({{ $qr->id }}, '{{ asset($qr->qr_code_path) }}', '{{ $qr->platform_name }}', '{{ $qr->upi_id ?? $setting->upi_id }}', '{{ $qr->upi_name ?? $setting->upi_name }}')">
                                                        <input type="radio" name="payment_qr_id" value="{{ $qr->id }}" class="d-none" {{ $loop->first ? 'checked' : '' }}>
                                                        <div class="fw-bold text-dark small text-truncate" style="font-size: 0.76rem;">{{ $qr->platform_name }}</div>
                                                        <span class="badge bg-success rounded-pill px-2 py-0" style="font-size: 0.65rem;">Active</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Active QR Card Mockup -->
                                    <div class="p-3 bg-light rounded-4 border text-center mb-3">
                                        <div class="d-inline-block bg-white p-2 rounded-3 border shadow-sm mb-2" style="max-width: 170px;">
                                            <img id="activeQrImage" src="{{ $primaryQr ? asset($primaryQr->qr_code_path) : ($setting->qr_code_path ? asset($setting->qr_code_path) : asset('images/qr-placeholder.png')) }}" alt="UPI QR" class="img-fluid rounded" style="max-height: 155px; aspect-ratio: 1/1; object-fit: contain;">
                                        </div>
                                        <div class="fw-bold text-dark" id="activeQrPlatform">{{ $primaryQr->platform_name ?? 'Official RupeeRush UPI' }}</div>
                                        <div class="small text-muted" id="activeQrPayee">Payee: {{ $primaryQr->upi_name ?? ($setting->upi_name ?? 'rupeerush') }}</div>

                                        <!-- UPI ID with Copy -->
                                        @php
                                            $upiString = $primaryQr->upi_id ?? ($setting->upi_id ?? 'rupeerush@upi');
                                        @endphp
                                        <div class="d-inline-flex align-items-center gap-2 bg-white px-3 py-1 rounded-pill border shadow-sm mt-2">
                                            <span class="font-monospace fw-bold text-dark small" id="activeQrUpi">{{ $upiString }}</span>
                                            <button type="button" class="btn btn-sm btn-link p-0 text-primary fw-bold" onclick="copyUpiId()" title="Copy UPI ID">
                                                <i class="fa-regular fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Deposit Amount -->
                                    <div class="mb-3">
                                        <label for="depositAmountInput" class="form-label fw-bold small text-muted">
                                            Deposit Amount (INR) <span class="text-danger">* (Minimum ₹100)</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold text-success">₹</span>
                                            <input type="number" 
                                                   class="form-control bg-light py-2 fw-bold text-dark @error('amount') is-invalid @enderror" 
                                                   id="depositAmountInput" 
                                                   name="amount" 
                                                   value="{{ old('amount', 100) }}" 
                                                   min="100" 
                                                   step="1" 
                                                   required>
                                        </div>
                                        <!-- Quick Amount Chips -->
                                        <div class="d-flex gap-2 mt-2">
                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-0 small fw-bold" onclick="setRegDepositAmount(100)">₹100</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-0 small fw-bold" onclick="setRegDepositAmount(200)">₹200</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-0 small fw-bold" onclick="setRegDepositAmount(500)">₹500</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-0 small fw-bold" onclick="setRegDepositAmount(1000)">₹1,000</button>
                                        </div>
                                        @error('amount')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- UTR / Reference Number -->
                                    <div class="mb-3">
                                        <label for="utrInput" class="form-label fw-bold small text-muted">
                                            12-Digit UPI UTR / Reference No. <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-hashtag"></i></span>
                                            <input type="text" 
                                                   class="form-control bg-light border-start-0 font-monospace py-2 @error('utr_number') is-invalid @enderror" 
                                                   id="utrInput" 
                                                   name="utr_number" 
                                                   value="{{ old('utr_number') }}" 
                                                   placeholder="e.g. 412356789012" 
                                                   maxlength="30" 
                                                   required>
                                        </div>
                                        <div class="form-text small" style="font-size: 0.72rem;">Enter the 12-digit transaction ID / UTR shown on your Google Pay / PhonePe / Paytm receipt.</div>
                                        @error('utr_number')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Screenshot Proof -->
                                    <div class="mb-3">
                                        <label for="screenshotInput" class="form-label fw-bold small text-muted">
                                            Payment Screenshot Proof <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" 
                                               class="form-control form-control-sm @error('screenshot') is-invalid @enderror" 
                                               id="screenshotInput" 
                                               name="screenshot" 
                                               accept="image/*" 
                                               onchange="previewProofImage(this)" 
                                               required>
                                        <div class="form-text small" style="font-size: 0.72rem;">Upload payment confirmation receipt (JPG, PNG, WEBP - Max 4MB).</div>
                                        @error('screenshot')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror

                                        <!-- Preview Mockup -->
                                        <div id="proofPreviewBox" class="mt-2 d-none">
                                            <img id="proofPreviewImg" src="" alt="Proof Preview" class="rounded-3 border shadow-sm" style="max-height: 120px; object-fit: contain;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, #0077b6 0%, #0096c7 100%); border: none; font-size: 1.05rem;">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span>Complete Registration &amp; Submit Deposit</span>
                                </button>
                            </form>
                        </div>

                        <!-- ========================================================
                             OPTION 2: WHATSAPP REGISTRATION (DIRECT CHAT WITH ADMIN)
                             ======================================================== -->
                        <div class="tab-pane fade" id="whatsapp-reg-pane" role="tabpanel" aria-labelledby="whatsapp-reg-tab" tabindex="0">
                            <div class="text-center py-4">
                                <div class="d-inline-flex p-3 rounded-circle bg-success bg-opacity-10 mb-3 shadow-sm">
                                    <i class="fa-brands fa-whatsapp text-success fs-1"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-1">WhatsApp Player Registration</h4>
                                <p class="text-muted small mb-4">Chat directly with our official RupeeRush Administrator to create and activate your account instantly.</p>

                                @if(!empty($referralCode))
                                    <div class="alert alert-warning border border-warning-subtle py-2 px-3 small rounded-3 mb-4 d-inline-block text-start">
                                        <i class="fa-solid fa-gift text-warning me-1"></i>
                                        <span class="text-muted">Referral Applied:</span>
                                        <strong class="text-dark font-monospace">{{ $referralCode }}</strong>
                                    </div>
                                @endif

                                <div class="d-grid gap-2 col-md-8 mx-auto mb-4">
                                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn py-3 rounded-pill fw-bold text-white shadow-sm d-flex align-items-center justify-content-center gap-2 fs-6" style="background: #25D366; border: none;">
                                        <i class="fa-brands fa-whatsapp fs-5"></i>
                                        <span>Open WhatsApp &amp; Register Now</span>
                                    </a>
                                </div>

                                <div class="text-muted small">
                                    <i class="fa-solid fa-headset me-1 text-success"></i> Official WhatsApp Care: 
                                    <strong class="text-dark font-monospace">+{{ $setting->formatted_whatsapp_number }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Options: Login & Demo Login -->
                    <div class="mt-4 pt-3 border-top text-center">
                        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                            <span class="text-muted small">Already have an account? <a href="{{ route('login') }}" class="text-decoration-none fw-bold text-primary">Login Here &rarr;</a></span>
                            <span class="text-muted small">•</span>
                            <form method="POST" action="{{ route('demo.login') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link p-0 text-decoration-none fw-bold small text-dark">
                                    <i class="fa-solid fa-gamepad text-warning me-1"></i> Try Demo Player
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    function setRegDepositAmount(amt) {
        const el = document.getElementById('depositAmountInput');
        if (el) {
            el.value = amt;
        }
    }

    function selectPaymentQr(id, imgUrl, name, upi, payee) {
        document.querySelectorAll('.qr-select-card').forEach(c => {
            c.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
            c.classList.add('bg-light');
        });
        const activeCard = event.currentTarget;
        if (activeCard) {
            activeCard.classList.remove('bg-light');
            activeCard.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
            const radio = activeCard.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        }

        const qrImg = document.getElementById('activeQrImage');
        if (qrImg && imgUrl) qrImg.src = imgUrl;

        const platEl = document.getElementById('activeQrPlatform');
        if (platEl) platEl.textContent = name;

        const payeeEl = document.getElementById('activeQrPayee');
        if (payeeEl) payeeEl.textContent = 'Payee: ' + payee;

        const upiEl = document.getElementById('activeQrUpi');
        if (upiEl) upiEl.textContent = upi;
    }

    function copyUpiId() {
        const upiEl = document.getElementById('activeQrUpi');
        if (upiEl) {
            navigator.clipboard.writeText(upiEl.textContent.trim()).then(() => {
                alert('UPI ID copied to clipboard: ' + upiEl.textContent.trim());
            });
        }
    }

    function previewProofImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const box = document.getElementById('proofPreviewBox');
                const img = document.getElementById('proofPreviewImg');
                if (img && box) {
                    img.src = e.target.result;
                    box.classList.remove('d-none');
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
