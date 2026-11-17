@extends('layouts.app')

@section('title', 'Debit & Credit Passbook - rupeerush')

@section('content')
<div class="container-xl py-4">
    <!-- Header Row -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-white d-flex align-items-center gap-2">
                <span class="rounded-circle d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-20 text-info" style="width: 44px; height: 44px; font-size: 1.25rem; border: 1px solid rgba(56, 189, 248, 0.3);">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </span>
                <span>Debit & Credit Passbook</span>
            </h3>
            <p class="text-light opacity-75 small mb-0">Detailed transparent ledger of your credits (deposits, winnings) and debits (bets placed, withdrawals).</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('bets.history') }}" class="btn btn-outline-light text-white rounded-pill px-3 py-2 btn-sm fw-bold">
                <i class="fa-solid fa-receipt me-1 text-warning"></i> My Bets History
            </a>
            <a href="{{ route('wallet.withdraw') }}" class="btn-withdraw-nav">
                <i class="fa-solid fa-arrow-up-from-bracket"></i> Withdraw
            </a>
            <a href="{{ route('wallet.deposit') }}" class="btn-deposit-nav">
                <i class="fa-solid fa-plus-circle"></i> Deposit
            </a>
        </div>
    </div>

    <!-- Summary Stat Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Available Wallet Balance -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-light opacity-75 small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Current Balance</div>
                        <div class="fs-4 fw-bold text-warning mt-1 font-monospace">₹{{ number_format($user->balance, 2) }}</div>
                        <div class="small text-white-50 mt-1" style="font-size: 0.78rem;">Available in wallet</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(245, 158, 11, 0.2); color: #f59e0b; font-size: 1.3rem;">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Total Credits -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Total Credits (+)</div>
                        <div class="fs-4 fw-bold text-success mt-1 font-monospace">+₹{{ number_format($totalCredits ?? 0, 2) }}</div>
                        <div class="small text-muted mt-1" style="font-size: 0.78rem;">Deposits & Win Payouts</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(16, 185, 129, 0.12); color: #10b981; font-size: 1.3rem;">
                        <i class="fa-solid fa-arrow-down"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Total Debits -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #ef4444 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Total Debits (-)</div>
                        <div class="fs-4 fw-bold text-danger mt-1 font-monospace">-₹{{ number_format($totalDebits ?? 0, 2) }}</div>
                        <div class="small text-muted mt-1" style="font-size: 0.78rem;">Bets Placed & Withdrawals</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(239, 68, 68, 0.12); color: #ef4444; font-size: 1.3rem;">
                        <i class="fa-solid fa-arrow-up"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Net Flow -->
        <div class="col-sm-6 col-xl-3">
            @php
                $netFlow = ($totalCredits ?? 0) - ($totalDebits ?? 0);
            @endphp
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #3b82f6 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Net Ledger Flow</div>
                        <div class="fs-4 fw-bold {{ $netFlow >= 0 ? 'text-success' : 'text-danger' }} mt-1 font-monospace">
                            {{ $netFlow >= 0 ? '+' : '' }}₹{{ number_format($netFlow, 2) }}
                        </div>
                        <div class="small text-muted mt-1" style="font-size: 0.78rem;">Credits vs Debits overall</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(59, 130, 246, 0.12); color: #3b82f6; font-size: 1.3rem;">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Pills: Debit & Credit & Types -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('wallet.transactions', ['flow' => 'all']) }}" class="btn btn-sm rounded-pill fw-bold {{ ($flow ?? 'all') === 'all' && empty($type) ? 'btn-dark' : 'btn-outline-secondary' }}">
                All Transactions
            </a>
            <a href="{{ route('wallet.transactions', ['flow' => 'credit']) }}" class="btn btn-sm rounded-pill fw-bold {{ ($flow ?? '') === 'credit' ? 'btn-success text-white' : 'btn-outline-success' }}">
                <i class="fa-solid fa-arrow-down me-1"></i> 🟢 Credits (+ Winnings & Deposits)
            </a>
            <a href="{{ route('wallet.transactions', ['flow' => 'debit']) }}" class="btn btn-sm rounded-pill fw-bold {{ ($flow ?? '') === 'debit' ? 'btn-danger text-white' : 'btn-outline-danger' }}">
                <i class="fa-solid fa-arrow-up me-1"></i> 🔴 Debits (- Bets & Withdrawals)
            </a>
            <a href="{{ route('wallet.transactions', ['type' => 'deposit']) }}" class="btn btn-sm rounded-pill fw-bold {{ ($type ?? '') === 'deposit' ? 'btn-primary text-white' : 'btn-outline-primary' }}">
                Deposits
            </a>
            <a href="{{ route('wallet.transactions', ['type' => 'withdrawal']) }}" class="btn btn-sm rounded-pill fw-bold {{ ($type ?? '') === 'withdrawal' ? 'btn-info text-white' : 'btn-outline-info text-info' }}">
                Withdrawals
            </a>
            <a href="{{ route('wallet.transactions', ['type' => 'bet_placed']) }}" class="btn btn-sm rounded-pill fw-bold {{ ($type ?? '') === 'bet_placed' ? 'btn-secondary text-white' : 'btn-outline-light text-white' }}">
                Bets Placed
            </a>
            <a href="{{ route('wallet.transactions', ['type' => 'bet_won']) }}" class="btn btn-sm rounded-pill fw-bold {{ ($type ?? '') === 'bet_won' ? 'btn-warning text-dark' : 'btn-outline-warning text-warning' }}">
                Won Payouts
            </a>
        </div>

        <div class="text-light opacity-80 small">
            Showing <strong class="text-white">{{ $transactions->count() }}</strong> of <strong class="text-white">{{ $transactions->total() }}</strong> entries
        </div>
    </div>

    <!-- Passbook Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th class="ps-3">Txn ID</th>
                        <th>Date & Time</th>
                        <th>Flow</th>
                        <th>Transaction Type</th>
                        <th>Amount</th>
                        <th>Balance After</th>
                        <th>Status</th>
                        <th>Proof / Media</th>
                        <th class="pe-3">Description / Game Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $txn)
                        @php
                            $isCredit = in_array($txn->type, ['deposit', 'bet_won'], true);
                        @endphp
                        <tr>
                            <td class="ps-3 font-monospace small text-muted">#{{ $txn->id }}</td>
                            <td class="small">{{ $txn->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}</td>
                            
                            <!-- Debit / Credit Flow Badge -->
                            <td>
                                @if($isCredit)
                                    <span class="badge bg-success text-white px-2 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-arrow-down me-1"></i> CREDIT (+)
                                    </span>
                                @else
                                    <span class="badge bg-danger text-white px-2 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-arrow-up me-1"></i> DEBIT (-)
                                    </span>
                                @endif
                            </td>

                            <!-- Type -->
                            <td>
                                @if($txn->type === 'deposit')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                        <i class="fa-solid fa-qrcode me-1"></i> Deposit
                                    </span>
                                @elseif($txn->type === 'bet_placed')
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded-pill">
                                        <i class="fa-solid fa-dice me-1"></i> Bet Placed
                                    </span>
                                @elseif($txn->type === 'bet_won')
                                    <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1 rounded-pill fw-bold">
                                        <i class="fa-solid fa-trophy me-1 text-warning"></i> Won Payout
                                    </span>
                                @elseif($txn->type === 'withdrawal')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill fw-bold">
                                        <i class="fa-solid fa-money-bill-transfer me-1"></i> Withdrawal
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                        Adjustment
                                    </span>
                                @endif
                            </td>

                            <!-- Amount -->
                            <td class="font-monospace fw-bold {{ $isCredit ? 'text-success fs-6' : 'text-danger' }}">
                                {{ $isCredit ? '+' : '-' }}₹{{ number_format($txn->amount, 2) }}
                            </td>

                            <!-- Balance After -->
                            <td class="font-monospace fw-bold text-dark">
                                ₹{{ number_format($txn->balance_after, 2) }}
                            </td>

                            <!-- Status -->
                            <td>
                                @if($txn->status === 'approved' || $txn->status === 'completed')
                                    <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill small fw-bold">
                                        <i class="fa-solid fa-check me-1"></i> Success
                                    </span>
                                @elseif($txn->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1 rounded-pill small fw-bold">
                                        <i class="fa-solid fa-hourglass-half me-1"></i> Pending
                                    </span>
                                @elseif($txn->status === 'rejected')
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill small fw-bold" title="{{ $txn->admin_notes }}">
                                        <i class="fa-solid fa-xmark me-1"></i> Rejected
                                    </span>
                                @endif
                            </td>

                            <!-- Proof / Media -->
                            <td>
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    @if($txn->type === 'withdrawal')
                                        @if($txn->user_qr_path)
                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2 py-0" style="font-size: 0.72rem;" onclick="viewPassbookProof('{{ $txn->user_qr_url }}', 'My Uploaded QR Code')">
                                                <i class="fa-solid fa-qrcode me-1"></i> My QR
                                            </button>
                                        @endif
                                        @if($txn->admin_proof_path)
                                            <button type="button" class="btn btn-success btn-sm rounded-pill px-2 py-0 fw-bold" style="font-size: 0.72rem;" onclick="viewPassbookProof('{{ $txn->admin_proof_url }}', 'Admin Payment Transfer Proof (₹{{ number_format($txn->amount, 2) }})')">
                                                <i class="fa-solid fa-receipt me-1"></i> Admin Proof
                                            </button>
                                        @endif
                                    @elseif($txn->screenshot_path)
                                        <button type="button" class="btn btn-outline-info btn-sm rounded-pill px-2 py-0" style="font-size: 0.72rem;" onclick="viewPassbookProof('{{ $txn->screenshot_url }}', 'Deposit Slip Proof')">
                                            <i class="fa-solid fa-image me-1"></i> Deposit Slip
                                        </button>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Description / Notes -->
                            <td class="pe-3 small text-muted">
                                {{ $txn->admin_notes ?: ($txn->account_details ?: ($txn->utr_number ? 'UTR: ' . $txn->utr_number : '-')) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-receipt fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                No transactions found in this section.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-3 border-top bg-light">
                {{ $transactions->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

<!-- Modal for Viewing Images -->
<div class="modal fade" id="passbookProofModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold" id="proofModalTitle">Document Preview</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <img id="proofModalImg" src="" alt="Proof Preview" class="img-fluid rounded-3 shadow-sm mb-3" style="max-height: 70vh; width: auto;">
                <div>
                    <a id="proofModalDownload" href="#" target="_blank" download class="btn btn-outline-primary btn-sm rounded-pill px-3">
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
    function viewPassbookProof(url, title) {
        document.getElementById('proofModalTitle').innerText = title;
        document.getElementById('proofModalImg').src = url;
        document.getElementById('proofModalDownload').href = url;
        const modal = new bootstrap.Modal(document.getElementById('passbookProofModal'));
        modal.show();
    }
</script>
@endsection
