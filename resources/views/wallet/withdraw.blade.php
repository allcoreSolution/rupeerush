@extends('layouts.app')

@section('title', 'Withdraw Funds - Lottery Portal')

@section('content')
<div class="container py-4">
    <!-- Top Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 d-flex align-items-center gap-3 p-3 mb-4" role="alert">
            <i class="fa-solid fa-circle-check fs-4 text-success"></i>
            <div>
                <strong>Success!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 d-flex align-items-center gap-3 p-3 mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation fs-4 text-danger"></i>
            <div>
                <strong>Error!</strong> {{ session('error') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        
        <!-- Left Column: Balance, Guidelines & Policy -->
        <div class="col-lg-5">
            <!-- Balance Overview Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="p-4 text-white" style="background: linear-gradient(135deg, #09192f 0%, #173256 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">
                            <i class="fa-solid fa-wallet me-1"></i> My Wallet
                        </span>
                        <a href="{{ route('wallet.deposit') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                            <i class="fa-solid fa-plus-circle me-1"></i> Deposit Instead
                        </a>
                    </div>
                    <div class="text-white-50 small text-uppercase fw-bold letter-spacing-1">Available For Withdrawal</div>
                    <div class="display-5 fw-bold text-success font-monospace my-1">
                        ₹{{ number_format($user->balance, 2) }}
                    </div>
                    <div class="small text-white-50">Winnings from won bets are automatically added here.</div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-2 text-center small mb-3">
                        <div class="col-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <div class="text-muted" style="font-size: 0.72rem; font-weight: 700;">MIN WITHDRAWAL</div>
                                <div class="fw-bold text-primary fs-6 mt-1">₹{{ number_format($setting->min_withdrawal ?? 200, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <div class="text-muted" style="font-size: 0.72rem; font-weight: 700;">PAYOUT SPEED</div>
                                <div class="fw-bold text-success fs-6 mt-1"><i class="fa-solid fa-bolt me-1"></i> Fast UPI</div>
                            </div>
                        </div>
                    </div>

                    <!-- Step-by-Step Instructions -->
                    <div class="alert alert-info border-0 rounded-3 p-3 small mb-0">
                        <div class="fw-bold mb-2 text-dark"><i class="fa-solid fa-circle-info me-1 text-info"></i> How to Withdraw:</div>
                        <ol class="ps-3 mb-0 text-secondary" style="line-height: 1.6;">
                            <li>Enter the amount you wish to withdraw (Min ₹{{ number_format($setting->min_withdrawal ?? 200, 0) }}).</li>
                            <li>Open your UPI App (<strong>PhonePe, Google Pay, Paytm</strong>), go to <em>My QR / Receive Money</em> and take a screenshot.</li>
                            <li>Upload your <strong>Payment QR Code</strong> in the form.</li>
                            <li>Click <strong>Submit Withdrawal Request</strong>.</li>
                            <li>Admin will scan your QR code, send the funds directly to your account, and attach the payment screenshot receipt as proof.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Submission Form & Recent Withdrawals -->
        <div class="col-lg-7">
            <!-- Form Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="fa-solid fa-money-bill-wave text-warning"></i>
                        <span>Want to withdraw rupees into account</span>
                    </h5>
                    <span class="badge bg-light text-muted border">Instant Request</span>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('wallet.withdraw.submit') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Quick Amount Buttons -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Quick Amount Selection:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="setWithdrawAmount(200)">₹200</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="setWithdrawAmount(500)">₹500</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="setWithdrawAmount(1000)">₹1,000</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="setWithdrawAmount(2000)">₹2,000</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="setWithdrawAmount(5000)">₹5,000</button>
                                @if((float)$user->balance >= ($setting->min_withdrawal ?? 200))
                                    <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold" onclick="setWithdrawAmount({{ floor((float)$user->balance) }})">
                                        <i class="fa-solid fa-coins me-1"></i> All Balance (₹{{ number_format($user->balance, 2) }})
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Withdrawal Amount -->
                            <div class="col-md-6">
                                <label for="withdrawAmount" class="form-label fw-bold small text-muted">
                                    Withdrawal Amount (₹) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold text-muted">₹</span>
                                    <input type="number" 
                                           step="1" 
                                           min="{{ $setting->min_withdrawal ?? 200 }}" 
                                           max="{{ $user->balance }}" 
                                           class="form-control form-control-lg bg-light @error('amount') is-invalid @enderror" 
                                           id="withdrawAmount" 
                                           name="amount" 
                                           placeholder="e.g. 500" 
                                           value="{{ old('amount') }}" 
                                           required>
                                </div>
                                <div class="form-text small">Min: ₹{{ number_format($setting->min_withdrawal ?? 200, 2) }} | Max: ₹{{ number_format($user->balance, 2) }}</div>
                                @error('amount')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- UPI ID / Phone (Optional / Recommended) -->
                            <div class="col-md-6">
                                <label for="upiId" class="form-label fw-bold small text-muted">
                                    Your UPI ID / Mobile Number <span class="text-muted">(Optional)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-at text-muted"></i></span>
                                    <input type="text" 
                                           class="form-control form-control-lg bg-light font-monospace @error('upi_id') is-invalid @enderror" 
                                           id="upiId" 
                                           name="upi_id" 
                                           placeholder="e.g. 9876543210@paytm" 
                                           value="{{ old('upi_id', $user->phone ? $user->phone . '@upi' : '') }}">
                                </div>
                                <div class="form-text small">Helps admin verify your account name.</div>
                                @error('upi_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Upload Payment QR Code -->
                            <div class="col-12">
                                <label for="qrInput" class="form-label fw-bold small text-muted">
                                    Upload Your Payment QR Code <span class="text-danger">*</span>
                                </label>
                                <div class="p-3 border-2 border-dashed rounded-3 bg-light text-center" style="border: 2px dashed #cbd5e1;">
                                    <input type="file" 
                                           class="form-control @error('qr_code') is-invalid @enderror" 
                                           id="qrInput" 
                                           name="qr_code" 
                                           accept="image/*" 
                                           onchange="previewUserQR(this)" 
                                           required>
                                    <div class="form-text small mt-2">
                                        <i class="fa-solid fa-qrcode me-1 text-primary"></i> Upload screenshot of your PhonePe, Google Pay, or Paytm QR code (PNG, JPG, WEBP - Max 4MB).
                                    </div>
                                    @error('qr_code')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror

                                    <!-- QR Image Preview Container -->
                                    <div id="qrPreviewContainer" class="mt-3 d-none">
                                        <p class="small text-muted fw-bold mb-1">Uploaded QR Preview:</p>
                                        <img id="qrImagePreview" src="" alt="QR Code Preview" class="img-thumbnail rounded-3 shadow-sm" style="max-height: 220px; width: auto;">
                                    </div>
                                </div>
                            </div>

                            <!-- Bank Account Details (Optional) -->
                            <div class="col-12">
                                <div class="border rounded-3 p-3 bg-light bg-opacity-75">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fa-solid fa-building-columns text-primary me-2"></i>
                                        <span class="fw-bold small text-dark">Bank Account Details <span class="text-muted fw-normal">(Optional)</span></span>
                                    </div>
                                    <div class="row g-2">
                                        <!-- Account Holder Name -->
                                        <div class="col-md-4">
                                            <label for="accountHolderName" class="form-label small text-muted mb-1">
                                                Account Holder Name
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white"><i class="fa-regular fa-user text-muted"></i></span>
                                                <input type="text" 
                                                       class="form-control bg-white @error('account_holder_name') is-invalid @enderror" 
                                                       id="accountHolderName" 
                                                       name="account_holder_name" 
                                                       placeholder="e.g. Rahul Sharma" 
                                                       value="{{ old('account_holder_name') }}">
                                            </div>
                                            @error('account_holder_name')
                                                <div class="text-danger small mt-1" style="font-size: 0.75rem;">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Account Number -->
                                        <div class="col-md-4">
                                            <label for="accountNumber" class="form-label small text-muted mb-1">
                                                Account Number
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white"><i class="fa-solid fa-hashtag text-muted"></i></span>
                                                <input type="text" 
                                                       class="form-control bg-white font-monospace @error('account_number') is-invalid @enderror" 
                                                       id="accountNumber" 
                                                       name="account_number" 
                                                       placeholder="e.g. 123456789012" 
                                                       value="{{ old('account_number') }}">
                                            </div>
                                            @error('account_number')
                                                <div class="text-danger small mt-1" style="font-size: 0.75rem;">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- IFSC Code -->
                                        <div class="col-md-4">
                                            <label for="ifscCode" class="form-label small text-muted mb-1">
                                                IFSC Code
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white"><i class="fa-solid fa-code text-muted"></i></span>
                                                <input type="text" 
                                                       class="form-control bg-white font-monospace text-uppercase @error('ifsc_code') is-invalid @enderror" 
                                                       id="ifscCode" 
                                                       name="ifsc_code" 
                                                       placeholder="e.g. SBIN0001234" 
                                                       value="{{ old('ifsc_code') }}"
                                                       style="text-transform: uppercase;">
                                            </div>
                                            @error('ifsc_code')
                                                <div class="text-danger small mt-1" style="font-size: 0.75rem;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-text small mt-2" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-circle-info me-1 text-primary"></i> Agar aap direct bank account me withdrawal chahte hain toh details fill karein.
                                    </div>
                                </div>
                            </div>

                            <!-- Account / Note Remarks -->
                            <div class="col-12">
                                <label for="accountDetails" class="form-label fw-bold small text-muted">
                                    Additional Remarks / Instructions <span class="text-muted">(Optional)</span>
                                </label>
                                <input type="text" 
                                       class="form-control bg-light @error('account_details') is-invalid @enderror" 
                                       id="accountDetails" 
                                       name="account_details" 
                                       placeholder="e.g. Bank name or any special transfer instructions" 
                                       value="{{ old('account_details') }}">
                                @error('account_details')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            @if((float)$user->balance < ($setting->min_withdrawal ?? 200))
                                <button type="button" class="btn btn-secondary w-100 py-3 rounded-pill fw-bold" disabled>
                                    <i class="fa-solid fa-lock me-2"></i> Insufficient Balance (Min ₹{{ number_format($setting->min_withdrawal ?? 200, 2) }})
                                </button>
                            @else
                                <button type="submit" class="btn btn-warning text-dark w-100 py-3 rounded-pill fw-bold shadow-sm" style="font-size: 1.05rem;">
                                    <i class="fa-solid fa-arrow-up-from-bracket me-2"></i> Submit Withdrawal Request
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Withdrawals Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-clock-rotate-left me-1 text-warning"></i> Recent Withdrawal Requests
                    </h6>
                    <a href="{{ route('wallet.transactions') }}" class="small text-decoration-none fw-bold text-primary">View Passbook</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-muted">
                            <tr>
                                <th>Date & Time</th>
                                <th>Amount</th>
                                <th>My QR</th>
                                <th>Status</th>
                                <th>Admin Proof</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentWithdrawals as $with)
                                <tr>
                                    <td class="small">{{ $with->created_at->timezone('Asia/Kolkata')->format('d M, h:i A') }}</td>
                                    <td class="fw-bold text-danger font-monospace">-₹{{ number_format($with->amount, 2) }}</td>
                                    <td>
                                        @if($with->user_qr_path)
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0" style="font-size: 0.75rem;" onclick="viewImageModal('{{ $with->user_qr_url }}', 'My Uploaded QR Code')">
                                                <i class="fa-solid fa-qrcode me-1"></i> View QR
                                            </button>
                                        @else
                                            <span class="text-muted small">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($with->status === 'approved')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                                <i class="fa-solid fa-check me-1"></i> Paid & Approved
                                            </span>
                                        @elseif($with->status === 'rejected')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill" title="{{ $with->admin_notes }}">
                                                <i class="fa-solid fa-xmark me-1"></i> Rejected (Refunded)
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill">
                                                <i class="fa-solid fa-hourglass-half me-1"></i> Awaiting Admin
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($with->admin_proof_path)
                                            <button type="button" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold" onclick="viewImageModal('{{ $with->admin_proof_url }}', 'Admin Payment Transfer Proof (₹{{ number_format($with->amount, 2) }})')">
                                                <i class="fa-solid fa-receipt me-1"></i> Payment Proof
                                            </button>
                                        @elseif($with->status === 'approved')
                                            <span class="text-success small fw-bold"><i class="fa-solid fa-check-double me-1"></i> Completed</span>
                                        @elseif($with->status === 'rejected')
                                            <span class="text-danger small" title="{{ $with->admin_notes }}">See note</span>
                                        @else
                                            <span class="text-muted small">Pending...</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted small">
                                        No recent withdrawals requested yet.
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

<!-- Modal for Viewing Images (QR or Payment Proof) -->
<div class="modal fade" id="imageViewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold" id="imageViewerTitle">Image Preview</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <img id="imageViewerSrc" src="" alt="Proof Preview" class="img-fluid rounded-3 shadow-sm mb-3" style="max-height: 70vh; width: auto;">
                <div>
                    <a id="imageDownloadBtn" href="#" target="_blank" download class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        <i class="fa-solid fa-arrow-down me-1"></i> Open Original
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function setWithdrawAmount(amount) {
        document.getElementById('withdrawAmount').value = amount;
    }

    function previewUserQR(input) {
        const container = document.getElementById('qrPreviewContainer');
        const preview = document.getElementById('qrImagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            container.classList.add('d-none');
        }
    }

    function viewImageModal(url, title) {
        document.getElementById('imageViewerTitle').innerText = title;
        document.getElementById('imageViewerSrc').src = url;
        document.getElementById('imageDownloadBtn').href = url;
        const modal = new bootstrap.Modal(document.getElementById('imageViewerModal'));
        modal.show();
    }
</script>
@endsection
