@extends('layouts.admin')

@section('title', 'Deposit Approval Queue')
@section('page_title', 'Deposit Approval & Payment History')

@section('content')
<div class="card-custom">
    <div class="card-custom-header">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <h6 class="card-custom-title">
                <i class="fa-solid fa-money-bill-transfer text-primary"></i>
                <span>Deposit Requests Queue</span>
            </h6>

            <!-- Status Filter Pills -->
            <div class="d-flex gap-1">
                <a href="{{ route('admin.payments.deposits') }}" class="btn btn-sm rounded-pill {{ $currentStatus === '' ? 'btn-dark' : 'btn-outline-secondary' }}">All</a>
                <a href="{{ route('admin.payments.deposits', ['status' => 'pending']) }}" class="btn btn-sm rounded-pill {{ $currentStatus === 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }}">
                    Pending <span class="badge bg-danger ms-1">{{ \App\Models\Transaction::where('type', 'deposit')->where('status', 'pending')->count() }}</span>
                </a>
                <a href="{{ route('admin.payments.deposits', ['status' => 'approved']) }}" class="btn btn-sm rounded-pill {{ $currentStatus === 'approved' ? 'btn-success' : 'btn-outline-success' }}">Approved</a>
                <a href="{{ route('admin.payments.deposits', ['status' => 'rejected']) }}" class="btn btn-sm rounded-pill {{ $currentStatus === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">Rejected</a>
            </div>
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('admin.payments.deposits') }}" class="d-flex gap-2">
            @if($currentStatus)
                <input type="hidden" name="status" value="{{ $currentStatus }}">
            @endif
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" name="search" placeholder="Search UTR, phone, name..." value="{{ $search }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted">
                <tr>
                    <th>Txn ID</th>
                    <th>Player</th>
                    <th>Amount</th>
                    <th>UTR / Ref Number</th>
                    <th>QR / Method</th>
                    <th>Payment Proof</th>
                    <th>Submitted At</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deposits as $dep)
                    <tr>
                        <td class="font-monospace small text-muted">#{{ $dep->id }}</td>
                        <td>
                            <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                <span>{{ $dep->user->name ?? 'User Deleted' }}</span>
                                @if(($dep->user->status ?? '') === 'pending')
                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-0 fw-bold" style="font-size: 0.65rem;" title="Account Pending Activation">New Reg</span>
                                @endif
                            </div>
                            <div class="text-muted small"><i class="fa-solid fa-phone me-1"></i>{{ $dep->user->phone ?? 'N/A' }}</div>
                            <div class="text-success small fw-bold">Current Bal: ₹{{ number_format($dep->user->balance ?? 0, 2) }}</div>
                        </td>
                        <td class="font-monospace fw-bold text-success fs-6">
                            +₹{{ number_format($dep->amount, 2) }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace px-2 py-1 fs-6">{{ $dep->utr_number }}</span>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill small">
                                <i class="fa-solid fa-qrcode me-1"></i> {{ $dep->payment_method ?: 'UPI' }}
                            </span>
                            @if($dep->account_details)
                                <div class="text-muted small mt-1 font-monospace" style="font-size: 0.68rem;">
                                    {{ Str::limit($dep->account_details, 26) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($dep->screenshot_path)
                                <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold" onclick="showProofModal('{{ $dep->screenshot_url }}', '{{ $dep->utr_number }}', '{{ number_format($dep->amount, 2) }}')">
                                    <i class="fa-solid fa-image me-1"></i> View Proof
                                </button>
                            @else
                                <span class="text-muted small">No Proof</span>
                            @endif
                        </td>
                        <td class="small text-muted">
                            {{ $dep->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}
                        </td>
                        <td>
                            @if($dep->status === 'approved')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">
                                    <i class="fa-solid fa-check me-1"></i> Approved
                                </span>
                            @elseif($dep->status === 'rejected')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill" title="{{ $dep->admin_notes }}">
                                    <i class="fa-solid fa-xmark me-1"></i> Rejected
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill fw-bold">
                                    <i class="fa-solid fa-hourglass-half me-1"></i> Pending Review
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($dep->status === 'pending')
                                <div class="d-flex align-items-center gap-2">
                                    <!-- Approve Button Form -->
                                    <form method="POST" action="{{ route('admin.payments.deposits.approve', $dep->id) }}" onsubmit="return confirm('Approve this deposit of ₹{{ number_format($dep->amount, 2) }} for {{ $dep->user->name ?? 'User' }}? This will instantly credit their wallet balance.');">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                            <i class="fa-solid fa-check me-1"></i> Approve
                                        </button>
                                    </form>

                                    <!-- Reject Button (Modal Trigger) -->
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" onclick="openRejectModal('{{ $dep->id }}', '{{ $dep->utr_number }}')">
                                        <i class="fa-solid fa-xmark me-1"></i> Reject
                                    </button>
                                </div>
                            @else
                                <small class="text-muted">Reviewed</small>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No deposits found for this filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($deposits->hasPages())
        <div class="p-3 border-top bg-light">
            {{ $deposits->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Modal: Screenshot Proof Preview -->
<div class="modal fade" id="proofModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 overflow-hidden">
            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title fw-bold" id="proofModalTitle">Payment Receipt Proof</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 text-center bg-light">
                <img id="proofModalImg" src="" alt="Payment Receipt" class="img-fluid rounded-3 shadow-sm" style="max-height: 550px;" />
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-secondary rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Reject Deposit with Reason -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Reject Deposit Request</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3" id="rejectModalDesc">Please provide a reason for rejecting this deposit.</p>
                    <div class="mb-3">
                        <label for="rejectReason" class="form-label fw-bold small text-muted">Rejection Reason</label>
                        <select class="form-select mb-2" onchange="document.getElementById('rejectReason').value = this.value">
                            <option value="">-- Choose Common Reason --</option>
                            <option value="Payment not received in bank statement.">Payment not received in bank statement.</option>
                            <option value="Invalid / Fake UTR reference number.">Invalid / Fake UTR reference number.</option>
                            <option value="Screenshot proof unreadable or blurred.">Screenshot proof unreadable or blurred.</option>
                            <option value="Duplicate transaction reference.">Duplicate transaction reference.</option>
                        </select>
                        <textarea class="form-control" id="rejectReason" name="reason" rows="3" placeholder="Explain why payment was rejected..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill btn-sm px-3 fw-bold">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showProofModal(imgUrl, utr, amount) {
        document.getElementById('proofModalImg').src = imgUrl;
        document.getElementById('proofModalTitle').innerText = `Payment Proof - UTR: ${utr} (₹${amount})`;
        new bootstrap.Modal(document.getElementById('proofModal')).show();
    }

    function openRejectModal(id, utr) {
        document.getElementById('rejectForm').action = `{{ url('admin/payments/deposits') }}/${id}/reject`;
        document.getElementById('rejectModalDesc').innerText = `Rejecting Deposit #${id} (UTR: ${utr}).`;
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    }
</script>
@endsection
