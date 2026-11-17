@extends('layouts.admin')

@section('title', 'Withdrawal Requests Queue')
@section('page_title', 'Player Withdrawal Requests Queue')

@section('content')
<!-- Success / Error Alerts -->
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

<div class="card-custom">
    <div class="card-custom-header">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <h6 class="card-custom-title">
                <i class="fa-solid fa-arrow-up-from-bracket text-warning"></i>
                <span>Withdrawal Requests Queue</span>
            </h6>

            <!-- Status Filter Pills -->
            <div class="d-flex gap-1">
                <a href="{{ route('admin.payments.withdrawals') }}" class="btn btn-sm rounded-pill {{ $currentStatus === '' ? 'btn-dark' : 'btn-outline-secondary' }}">All</a>
                <a href="{{ route('admin.payments.withdrawals', ['status' => 'pending']) }}" class="btn btn-sm rounded-pill {{ $currentStatus === 'pending' ? 'btn-warning text-dark fw-bold' : 'btn-outline-warning text-dark' }}">
                    Pending <span class="badge bg-danger ms-1">{{ \App\Models\Transaction::where('type', 'withdrawal')->where('status', 'pending')->count() }}</span>
                </a>
                <a href="{{ route('admin.payments.withdrawals', ['status' => 'approved']) }}" class="btn btn-sm rounded-pill {{ $currentStatus === 'approved' ? 'btn-success fw-bold' : 'btn-outline-success' }}">Approved</a>
                <a href="{{ route('admin.payments.withdrawals', ['status' => 'rejected']) }}" class="btn btn-sm rounded-pill {{ $currentStatus === 'rejected' ? 'btn-danger fw-bold' : 'btn-outline-danger' }}">Rejected</a>
            </div>
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('admin.payments.withdrawals') }}" class="d-flex gap-2">
            @if($currentStatus)
                <input type="hidden" name="status" value="{{ $currentStatus }}">
            @endif
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" name="search" placeholder="Search phone, name, UTR, ID..." value="{{ $search }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted">
                <tr>
                    <th>Txn ID</th>
                    <th>Player Details</th>
                    <th>Amount to Pay</th>
                    <th>Player QR Code</th>
                    <th>UPI / Account</th>
                    <th>Requested At</th>
                    <th>Status</th>
                    <th>Payment Proof / Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $with)
                    <tr>
                        <td class="font-monospace small text-muted">#{{ $with->id }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $with->user->name ?? 'User Deleted' }}</div>
                            <div class="text-muted small"><i class="fa-solid fa-phone me-1"></i>{{ $with->user->phone ?? 'N/A' }}</div>
                            <div class="text-secondary small">Bal: ₹{{ number_format($with->user->balance ?? 0, 2) }}</div>
                        </td>
                        <td class="font-monospace fw-bold text-danger fs-6">
                            ₹{{ number_format($with->amount, 2) }}
                        </td>
                        <td>
                            @if($with->user_qr_path)
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" onclick="showPlayerQrModal('{{ $with->user_qr_url }}', '{{ $with->user->name ?? 'Player' }}', '{{ number_format($with->amount, 2) }}', '{{ $with->account_details ?? $with->user->phone }}')">
                                    <i class="fa-solid fa-qrcode me-1"></i> Scan Player QR
                                </button>
                            @else
                                <span class="text-muted small">No QR Uploaded</span>
                            @endif
                        </td>
                        <td>
                            @if($with->account_number || $with->account_holder_name)
                                <div class="font-monospace small fw-bold text-dark">
                                    <i class="fa-solid fa-building-columns text-primary me-1"></i>{{ $with->account_number ?? 'Bank A/C' }}
                                </div>
                                <div class="small text-muted" style="font-size: 0.75rem;">
                                    {{ $with->account_holder_name }}{{ $with->ifsc_code ? ' | IFSC: '.$with->ifsc_code : '' }}
                                </div>
                            @else
                                <span class="badge bg-light text-dark border px-2 py-1 font-monospace" style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; display: inline-block;">
                                    {{ $with->account_details ?: 'UPI QR' }}
                                </span>
                            @endif
                        </td>
                        <td class="small text-muted">
                            {{ $with->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}
                        </td>
                        <td>
                            @if($with->status === 'approved')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">
                                    <i class="fa-solid fa-check me-1"></i> Paid & Approved
                                </span>
                                @if($with->reviewer)
                                    <div class="text-muted" style="font-size: 0.7rem;">by {{ $with->reviewer->name }}</div>
                                @endif
                            @elseif($with->status === 'rejected')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-bold" title="{{ $with->admin_notes }}">
                                    <i class="fa-solid fa-xmark me-1"></i> Rejected (Refunded)
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill fw-bold">
                                    <i class="fa-solid fa-hourglass-half me-1"></i> Pending Payment
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($with->status === 'pending')
                                <div class="d-flex align-items-center gap-2">
                                    <!-- Approve & Upload Proof Button -->
                                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm" onclick="openApproveModal('{{ $with->id }}', '{{ $with->user->name ?? 'Player' }}', '{{ number_format($with->amount, 2) }}', '{{ $with->user_qr_url ?? '' }}', '{{ addslashes($with->account_details ?? '') }}')">
                                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Approve & Upload Proof
                                    </button>

                                    <!-- Reject Button -->
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" onclick="openRejectModal('{{ $with->id }}', '{{ $with->user->name ?? 'Player' }}', '{{ number_format($with->amount, 2) }}')">
                                        <i class="fa-solid fa-xmark me-1"></i> Reject
                                    </button>
                                </div>
                            @elseif($with->status === 'approved')
                                @if($with->admin_proof_path)
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold" onclick="showProofModal('{{ $with->admin_proof_url }}', '{{ $with->utr_number ?? 'N/A' }}', '{{ number_format($with->amount, 2) }}')">
                                        <i class="fa-solid fa-image me-1"></i> View Payment Proof
                                    </button>
                                @else
                                    <span class="text-success small fw-bold"><i class="fa-solid fa-check-double me-1"></i> Completed</span>
                                @endif
                            @else
                                <span class="text-muted small" title="{{ $with->admin_notes }}">
                                    {{ Str::limit($with->admin_notes, 25) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No withdrawal requests found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($withdrawals->hasPages())
        <div class="p-3 border-top bg-light">
            {{ $withdrawals->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Modal 1: Scan Player QR Code -->
<div class="modal fade" id="playerQrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h6 class="modal-title fw-bold text-dark" id="playerQrTitle">Player Payment QR Code</h6>
                    <small class="text-muted" id="playerQrSubtitle">Scan to transfer funds</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="p-3 bg-light rounded-4 d-inline-block border shadow-sm mb-3">
                    <img id="playerQrImg" src="" alt="Player QR" class="img-fluid rounded-3" style="max-height: 300px; width: auto;">
                </div>
                <div class="alert alert-warning border-0 small text-dark mb-3">
                    <strong>Amount to Pay:</strong> <span class="fs-5 fw-bold text-danger font-monospace" id="playerQrAmount">₹0.00</span>
                    <div id="playerQrDetails" class="small text-muted mt-1"></div>
                </div>
                <div class="d-flex justify-content-center gap-2">
                    <a id="playerQrDownload" href="#" target="_blank" download class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        <i class="fa-solid fa-download me-1"></i> Download QR
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Approve & Upload Payment Screenshot -->
<div class="modal fade" id="approveWithdrawModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form id="approveWithdrawForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h6 class="modal-title fw-bold text-success d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Approve Withdrawal & Upload Proof</span>
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="alert alert-light border rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Player:</span>
                            <strong id="approvePlayerName" class="text-dark">User</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Payout Amount:</span>
                            <strong id="approveAmount" class="text-danger font-monospace fs-5">₹0.00</strong>
                        </div>
                        <div id="approveBankDetailsBox" class="mt-2 pt-2 border-top small text-muted d-none">
                            <span class="fw-bold text-dark d-block"><i class="fa-solid fa-building-columns text-primary me-1"></i> Transfer Details:</span>
                            <span id="approveBankDetails" class="font-monospace text-dark"></span>
                        </div>
                    </div>

                    <!-- Optional Mini QR Display for Admin Reference -->
                    <div id="modalQrPreviewBox" class="text-center p-2 bg-light rounded-3 border mb-3 d-none">
                        <span class="small text-muted fw-bold d-block mb-1">Player's Uploaded QR:</span>
                        <img id="modalQrImg" src="" alt="Player QR" class="rounded-2" style="max-height: 120px; width: auto;">
                    </div>

                    <!-- Payment Screenshot Upload (Required) -->
                    <div class="mb-3">
                        <label for="adminScreenshot" class="form-label fw-bold small text-dark">
                            <i class="fa-solid fa-image text-success me-1"></i> Upload Payment Transfer Screenshot <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control" id="adminScreenshot" name="screenshot" accept="image/*" onchange="previewAdminProof(this)" required>
                        <div class="form-text small">Take a screenshot of the successful transfer receipt in PhonePe/GPay/Paytm and upload here (Max 4MB).</div>

                        <!-- Live preview of admin upload -->
                        <div id="adminProofPreviewBox" class="mt-2 text-center d-none">
                            <img id="adminProofPreviewImg" src="" alt="Proof Preview" class="img-thumbnail rounded-3 shadow-sm" style="max-height: 160px;">
                        </div>
                    </div>

                    <!-- UTR / Reference Number -->
                    <div class="mb-3">
                        <label for="adminUtr" class="form-label fw-bold small text-muted">Bank / UPI Ref / UTR Number (Optional)</label>
                        <input type="text" class="form-control font-monospace" id="adminUtr" name="utr_number" placeholder="e.g. 429188219012">
                    </div>

                    <!-- Admin Remarks -->
                    <div class="mb-2">
                        <label for="adminNotes" class="form-label fw-bold small text-muted">Notes / Remarks for Player (Optional)</label>
                        <input type="text" class="form-control" id="adminNotes" name="notes" placeholder="e.g. Paid successfully via UPI">
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" style="background: #10b981; border: none;">
                        <i class="fa-solid fa-check me-1"></i> Confirm & Approve Transfer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 3: Reject Withdrawal Request (Refunds balance) -->
<div class="modal fade" id="rejectWithdrawModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form id="rejectWithdrawForm" method="POST" action="">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h6 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Reject Withdrawal Request</span>
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="alert alert-danger-subtle text-danger border border-danger-subtle rounded-3 p-3 small mb-3">
                        <i class="fa-solid fa-circle-exclamation me-1"></i>
                        Rejecting this request will <strong>instantly refund</strong> <span id="rejectAmount" class="fw-bold">₹0.00</span> back to <strong id="rejectPlayerName">User</strong>'s wallet balance.
                    </div>

                    <div class="mb-3">
                        <label for="rejectReason" class="form-label fw-bold small text-muted">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejectReason" name="reason" rows="3" placeholder="e.g. QR code unreadable, UPI ID inactive, or invalid details..." required></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-arrow-rotate-left me-1"></i> Reject & Refund Balance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 4: View Admin Payment Proof -->
<div class="modal fade" id="proofViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h6 class="modal-title fw-bold">Admin Payment Transfer Proof</h6>
                    <small class="text-muted font-monospace" id="proofUtrText"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <img id="proofModalImage" src="" alt="Proof Screenshot" class="img-fluid rounded-3 shadow-sm mb-3" style="max-height: 70vh; width: auto;">
                <div>
                    <a id="proofDownloadBtn" href="#" target="_blank" download class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        <i class="fa-solid fa-arrow-down me-1"></i> Open Full Image
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // 1. Show Player QR Modal
    function showPlayerQrModal(qrUrl, playerName, amount, details) {
        document.getElementById('playerQrImg').src = qrUrl;
        document.getElementById('playerQrTitle').innerText = playerName + "'s QR Code";
        document.getElementById('playerQrSubtitle').innerText = "Scan on PhonePe / GPay / Paytm to pay";
        document.getElementById('playerQrAmount').innerText = "₹" + amount;
        document.getElementById('playerQrDetails').innerText = details ? "Account / UPI: " + details : "";
        document.getElementById('playerQrDownload').href = qrUrl;

        const modal = new bootstrap.Modal(document.getElementById('playerQrModal'));
        modal.show();
    }

    // 2. Open Approve Modal
    function openApproveModal(id, playerName, amount, qrUrl, details) {
        const form = document.getElementById('approveWithdrawForm');
        form.action = "{{ url('admin/payments/withdrawals') }}/" + id + "/approve";
        document.getElementById('approvePlayerName').innerText = playerName;
        document.getElementById('approveAmount').innerText = "₹" + amount;

        const detailsBox = document.getElementById('approveBankDetailsBox');
        if (details) {
            document.getElementById('approveBankDetails').innerText = details;
            detailsBox.classList.remove('d-none');
        } else {
            detailsBox.classList.add('d-none');
        }

        const qrBox = document.getElementById('modalQrPreviewBox');
        if (qrUrl) {
            document.getElementById('modalQrImg').src = qrUrl;
            qrBox.classList.remove('d-none');
        } else {
            qrBox.classList.add('d-none');
        }

        // Reset file input & preview
        document.getElementById('adminScreenshot').value = "";
        document.getElementById('adminProofPreviewBox').classList.add('d-none');

        const modal = new bootstrap.Modal(document.getElementById('approveWithdrawModal'));
        modal.show();
    }

    // 3. Preview Admin Screenshot in Modal
    function previewAdminProof(input) {
        const box = document.getElementById('adminProofPreviewBox');
        const img = document.getElementById('adminProofPreviewImg');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                box.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            box.classList.add('d-none');
        }
    }

    // 4. Open Reject Modal
    function openRejectModal(id, playerName, amount) {
        const form = document.getElementById('rejectWithdrawForm');
        form.action = "{{ url('admin/payments/withdrawals') }}/" + id + "/reject";
        document.getElementById('rejectPlayerName').innerText = playerName;
        document.getElementById('rejectAmount').innerText = "₹" + amount;
        document.getElementById('rejectReason').value = "";

        const modal = new bootstrap.Modal(document.getElementById('rejectWithdrawModal'));
        modal.show();
    }

    // 5. View Admin Proof Modal
    function showProofModal(url, utr, amount) {
        document.getElementById('proofModalImage').src = url;
        document.getElementById('proofUtrText').innerText = "UTR / Ref: " + utr + " (₹" + amount + ")";
        document.getElementById('proofDownloadBtn').href = url;

        const modal = new bootstrap.Modal(document.getElementById('proofViewModal'));
        modal.show();
    }
</script>
@endsection
