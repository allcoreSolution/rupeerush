@extends('layouts.admin')

@section('title', 'Website Support Settings')
@section('page_title', 'Website Support Contacts (WhatsApp & Email)')

@section('content')
<div class="row g-4">
    <!-- Left Column: Dedicated Support Form -->
    <div class="col-lg-7">
        <div class="card-custom">
            <div class="card-custom-header d-flex align-items-center justify-content-between">
                <h6 class="card-custom-title mb-0">
                    <i class="fa-solid fa-headset text-success"></i>
                    <span>Configure Floating Support Credentials</span>
                </h6>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                    <i class="fa-solid fa-bolt me-1"></i> Live Real-Time Sync
                </span>
            </div>
            <div class="card-custom-body p-4">
                <div class="alert alert-info border-0 rounded-3 d-flex align-items-center gap-3 mb-4" style="background: rgba(14, 165, 233, 0.08); color: #0284c7;">
                    <i class="fa-solid fa-circle-info fs-4 flex-shrink-0"></i>
                    <div class="small">
                        Yahan aap jo <strong>WhatsApp number</strong> aur <strong>Email address</strong> save karenge, player website ke sabhi pages (Lobby, Lottery, WinGo, Andar Bahar) par bottom-right corner me floating buttons usi second naye contact info ke saath update ho jayenge.
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.support.update') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="supportWhatsapp" class="form-label fw-bold text-dark d-flex align-items-center justify-content-between">
                            <span>
                                <i class="fa-brands fa-whatsapp text-success me-1 fs-5 align-middle"></i> Official WhatsApp Support Number <span class="text-danger">*</span>
                            </span>
                            <span class="badge bg-light text-secondary border font-monospace" style="font-size:0.75rem;">wa.me Link</span>
                        </label>
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-success text-white fw-bold border-0">
                                <i class="fa-brands fa-whatsapp fs-4"></i>
                            </span>
                            <input type="text" 
                                   class="form-control fw-bold font-monospace fs-5 @error('support_whatsapp') is-invalid @enderror" 
                                   id="supportWhatsapp" 
                                   name="support_whatsapp" 
                                   value="{{ old('support_whatsapp', $setting->support_whatsapp ?? '9026217223') }}" 
                                   placeholder="e.g. 9026217223 or 9876543210" 
                                   required>
                        </div>
                        @error('support_whatsapp')
                            <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                        @enderror

                        <div class="d-flex flex-wrap align-items-center justify-content-between mt-2 pt-1">
                            <div class="form-text small text-muted mb-0">
                                <i class="fa-regular fa-lightbulb text-warning me-1"></i> Player floating button par click karte hi direct WhatsApp chat open hogi.
                            </div>
                            @php
                                $rawWa = $setting->support_whatsapp ?? '9026217223';
                                $testDigits = preg_replace('/[^0-9]/', '', $rawWa);
                                if (str_starts_with($testDigits, '0') && strlen($testDigits) === 11) {
                                    $testDigits = substr($testDigits, 1);
                                }
                                if (strlen($testDigits) === 10) {
                                    $testDigits = '91' . $testDigits;
                                }
                            @endphp
                            <a href="https://wa.me/{{ $testDigits }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill fw-bold px-3 mt-1 mt-md-0" id="testWaBtn">
                                <i class="fa-brands fa-whatsapp me-1"></i> Test WhatsApp Chat
                            </a>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="supportEmail" class="form-label fw-bold text-dark d-flex align-items-center justify-content-between">
                            <span>
                                <i class="fa-solid fa-envelope text-primary me-1 fs-5 align-middle"></i> Official Support Email Address <span class="text-danger">*</span>
                            </span>
                            <span class="badge bg-light text-secondary border font-monospace" style="font-size:0.75rem;">mailto: Link</span>
                        </label>
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-primary text-white fw-bold border-0">
                                <i class="fa-solid fa-envelope fs-4"></i>
                            </span>
                            <input type="text" 
                                   class="form-control fw-bold fs-5 @error('support_email') is-invalid @enderror" 
                                   id="supportEmail" 
                                   name="support_email" 
                                   value="{{ old('support_email', $setting->support_email ?? 'rupeerushonline@gmail.com') }}" 
                                   placeholder="e.g. rupeerushonline@gmail.com" 
                                   required>
                        </div>
                        @error('support_email')
                            <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                        @enderror

                        <div class="d-flex flex-wrap align-items-center justify-content-between mt-2 pt-1">
                            <div class="form-text small text-muted mb-0">
                                <i class="fa-regular fa-lightbulb text-warning me-1"></i> Player email button click karne par is address par support mail draft hoga.
                            </div>
                            <a href="mailto:{{ $setting->support_email ?? 'rupeerushonline@gmail.com' }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill fw-bold px-3 mt-1 mt-md-0" id="testEmailBtn">
                                <i class="fa-solid fa-envelope me-1"></i> Test Email Compose
                            </a>
                        </div>
                    </div>

                    <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted">
                            <i class="fa-solid fa-shield-halved text-success me-1"></i> Changes apply immediately across all player devices.
                        </span>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm" style="background: linear-gradient(135deg, #0284c7, #0369a1); border: none;">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Save Support Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Live Interactive Preview -->
    <div class="col-lg-5">
        <div class="card-custom">
            <div class="card-custom-header d-flex align-items-center justify-content-between">
                <h6 class="card-custom-title mb-0">
                    <i class="fa-solid fa-mobile-screen text-primary"></i>
                    <span>Live Floating Buttons Preview</span>
                </h6>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 small">Player View</span>
            </div>
            <div class="card-custom-body p-4">
                <p class="small text-muted mb-3">Player screen par bottom-right me floating buttons is tarah live dikhai denge:</p>

                <!-- Phone Screen Simulation Box -->
                <div class="rounded-4 p-4 text-white position-relative shadow-lg border border-secondary" style="background: linear-gradient(180deg, #0b1329 0%, #172554 100%); min-height: 220px;">
                    <div class="d-flex justify-content-between align-items-center border-bottom border-white border-opacity-10 pb-2 mb-4">
                        <div class="small text-white-50 font-monospace" style="font-size:0.75rem;">
                            <i class="fa-solid fa-signal me-1"></i> RUPEERUSH LIVE
                        </div>
                        <div class="badge bg-white bg-opacity-10 text-white rounded-pill" style="font-size:0.7rem;">Screen Corner</div>
                    </div>

                    <div class="text-white-50 small mb-3 text-center" style="font-size:0.8rem;">
                        [ ... Website Content / Lobby / Games Area ... ]
                    </div>

                    <!-- Floating Buttons Simulation Area -->
                    <div class="d-flex flex-column align-items-end gap-3 mt-4">
                        <!-- Floating Email Button -->
                        <div class="d-flex align-items-center gap-2" id="previewEmailBadge">
                            <span class="text-white fw-bold small px-3 py-1 rounded-pill shadow-sm" style="background: rgba(2, 132, 199, 0.95); font-size: 0.8rem; letter-spacing: 0.2px;" id="previewEmailText">
                                {{ $setting->support_email ?? 'rupeerushonline@gmail.com' }}
                            </span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow" style="width: 44px; height: 44px; background: linear-gradient(135deg, #0284c7, #0369a1);">
                                <i class="fa-solid fa-envelope fs-5"></i>
                            </div>
                        </div>

                        <!-- Floating WhatsApp Button -->
                        <div class="d-flex align-items-center gap-2" id="previewWaBadge">
                            <span class="text-white fw-bold small px-3 py-1 rounded-pill shadow-sm" style="background: rgba(37, 211, 102, 0.95); font-size: 0.82rem; letter-spacing: 0.2px;" id="previewWaText">
                                WhatsApp: {{ $setting->support_whatsapp ?? '9026217223' }}
                            </span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow position-relative" style="width: 46px; height: 46px; background: linear-gradient(135deg, #25D366, #128C7E);">
                                <i class="fa-brands fa-whatsapp fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 bg-light rounded-3 p-3 mt-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="fw-bold text-dark small">Check Live on Website:</div>
                            <div class="text-muted small" style="font-size:0.75rem;">See how floating buttons open on the user portal.</div>
                        </div>
                        <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-dark rounded-pill fw-bold px-3">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Lobby
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Live update Support Contacts preview in real-time as admin types
    const waInput = document.getElementById('supportWhatsapp');
    const emailInput = document.getElementById('supportEmail');
    const waPreviewText = document.getElementById('previewWaText');
    const emailPreviewText = document.getElementById('previewEmailText');
    const testWaBtn = document.getElementById('testWaBtn');
    const testEmailBtn = document.getElementById('testEmailBtn');

    if (waInput) {
        waInput.addEventListener('input', function() {
            const rawVal = this.value.trim();
            const displayVal = rawVal || '9026217223';
            if (waPreviewText) waPreviewText.innerText = 'WhatsApp: ' + displayVal;

            let digits = rawVal.replace(/[^0-9]/g, '');
            if (digits.startsWith('0') && digits.length === 11) {
                digits = digits.substring(1);
            }
            if (digits.length === 10) {
                digits = '91' + digits;
            }
            if (testWaBtn) {
                testWaBtn.href = 'https://wa.me/' + (digits || '919026217223');
            }
        });
    }

    if (emailInput) {
        emailInput.addEventListener('input', function() {
            const rawVal = this.value.trim();
            const displayVal = rawVal || 'rupeerushonline@gmail.com';
            if (emailPreviewText) emailPreviewText.innerText = displayVal;
            if (testEmailBtn) {
                testEmailBtn.href = 'mailto:' + displayVal;
            }
        });
    }
</script>
@endsection
