@extends('layouts.admin')

@section('title', 'Daily-to-Daily Transaction Detail')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-calendar-days text-primary me-2"></i> Daily-to-Daily Transaction Detail
        </h2>
        <p class="text-muted small mb-0">Complete real-time breakdown of player deposits, withdrawals, wagers, and payouts day by day.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.payments.deposits') }}" class="btn btn-outline-success fw-bold rounded-pill px-3">
            <i class="fa-solid fa-money-bill-transfer me-1"></i> Deposits Queue
        </a>
        <a href="{{ route('admin.payments.withdrawals') }}" class="btn btn-outline-warning text-dark fw-bold rounded-pill px-3">
            <i class="fa-solid fa-arrow-up-from-bracket me-1"></i> Withdrawals Queue
        </a>
        <a href="{{ route('admin.payments.settings') }}" class="btn btn-outline-info fw-bold rounded-pill px-3">
            <i class="fa-solid fa-sliders me-1"></i> Limits & QR
        </a>
    </div>
</div>

<!-- Daily Performance Cards for Selected Date -->
<div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #09192f 0%, #0e2a50 100%);">
    <div class="card-body p-4 text-white">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3 border-bottom border-light border-opacity-10 pb-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-info text-dark px-3 py-2 rounded-pill fw-bold">
                    <i class="fa-regular fa-calendar-check me-1"></i> Active Day: {{ \Carbon\Carbon::parse($selectedDate)->format('d F Y (l)') }}
                </span>
                @if($selectedDate === \Carbon\Carbon::now('Asia/Kolkata')->format('Y-m-d'))
                    <span class="badge bg-success px-2 py-1 rounded-pill small">TODAY</span>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.payments.daily', ['date' => \Carbon\Carbon::now('Asia/Kolkata')->format('Y-m-d')]) }}" class="btn btn-sm btn-outline-light rounded-pill px-3 fw-bold">
                    Today
                </a>
                <a href="{{ route('admin.payments.daily', ['date' => \Carbon\Carbon::now('Asia/Kolkata')->subDay()->format('Y-m-d')]) }}" class="btn btn-sm btn-outline-light rounded-pill px-3 fw-bold">
                    Yesterday
                </a>
            </div>
        </div>

        <div class="row g-3">
            <!-- Approved Deposits -->
            <div class="col-sm-6 col-lg-3">
                <div class="p-3 rounded-3" style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3);">
                    <div class="small text-uppercase fw-bold text-success" style="letter-spacing: 0.5px;">Approved Deposits</div>
                    <div class="fs-3 fw-bold text-white mt-1">₹{{ number_format($dailyStats['approved_deposits'], 2) }}</div>
                    <div class="small text-white-50 mt-1">
                        {{ $dailyStats['approved_deposits_count'] }} successful
                        @if($dailyStats['pending_deposits_count'] > 0)
                            <span class="badge bg-warning text-dark ms-1">{{ $dailyStats['pending_deposits_count'] }} pending</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Approved Withdrawals -->
            <div class="col-sm-6 col-lg-3">
                <div class="p-3 rounded-3" style="background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3);">
                    <div class="small text-uppercase fw-bold text-warning" style="letter-spacing: 0.5px;">Paid Withdrawals</div>
                    <div class="fs-3 fw-bold text-white mt-1">₹{{ number_format($dailyStats['approved_withdrawals'], 2) }}</div>
                    <div class="small text-white-50 mt-1">
                        {{ $dailyStats['approved_withdrawals_count'] }} approved
                        @if($dailyStats['pending_withdrawals_count'] > 0)
                            <span class="badge bg-danger ms-1">{{ $dailyStats['pending_withdrawals_count'] }} pending</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bets Wagered Pool -->
            <div class="col-sm-6 col-lg-3">
                <div class="p-3 rounded-3" style="background: rgba(56, 189, 248, 0.12); border: 1px solid rgba(56, 189, 248, 0.3);">
                    <div class="small text-uppercase fw-bold text-info" style="letter-spacing: 0.5px;">Bets Placed (Wagered)</div>
                    <div class="fs-3 fw-bold text-white mt-1">₹{{ number_format($dailyStats['bets_placed_amount'], 2) }}</div>
                    <div class="small text-white-50 mt-1">{{ number_format($dailyStats['bets_placed_count']) }} wagers across games</div>
                </div>
            </div>

            <!-- Wins / Payouts -->
            <div class="col-sm-6 col-lg-3">
                <div class="p-3 rounded-3" style="background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3);">
                    <div class="small text-uppercase fw-bold text-danger" style="letter-spacing: 0.5px;">Player Wins (Payouts)</div>
                    <div class="fs-3 fw-bold text-white mt-1">₹{{ number_format($dailyStats['bets_won_amount'], 2) }}</div>
                    <div class="small text-white-50 mt-1">{{ number_format($dailyStats['bets_won_count']) }} winning tickets</div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-between p-2 px-3 rounded-3 bg-white bg-opacity-10">
                    <span class="small text-white-50 fw-bold">NET CASH INFLOW (Deposits - Withdrawals):</span>
                    <span class="fw-bold font-monospace {{ $dailyStats['net_cash_flow'] >= 0 ? 'text-success' : 'text-danger' }} fs-5">
                        {{ $dailyStats['net_cash_flow'] >= 0 ? '+' : '' }}₹{{ number_format($dailyStats['net_cash_flow'], 2) }}
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-between p-2 px-3 rounded-3 bg-white bg-opacity-10">
                    <span class="small text-white-50 fw-bold">GROSS GAMING REVENUE (Bets - Payouts):</span>
                    <span class="fw-bold font-monospace {{ $dailyStats['net_gaming_revenue'] >= 0 ? 'text-info' : 'text-warning' }} fs-5">
                        {{ $dailyStats['net_gaming_revenue'] >= 0 ? '+' : '' }}₹{{ number_format($dailyStats['net_gaming_revenue'], 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Past 14 Days Historical Breakdown Accordion/Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-chart-line text-primary me-2"></i> Past 14 Days Summary Breakdown
        </h5>
        <span class="text-muted small">Click any date to view that day's granular transactions</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th class="text-end">Approved Deposits</th>
                        <th class="text-end">Paid Withdrawals</th>
                        <th class="text-end">Bets Wagered</th>
                        <th class="text-end">Payouts Won</th>
                        <th class="text-end">Net Game Profit</th>
                        <th class="text-center">Total Activities</th>
                        <th class="text-center pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentDays as $day)
                        @php
                            $isSelected = $day['date'] === $selectedDate;
                        @endphp
                        <tr class="{{ $isSelected ? 'table-primary bg-opacity-25 fw-bold' : '' }}">
                            <td class="ps-4">
                                <span class="fw-bold">{{ \Carbon\Carbon::parse($day['date'])->format('d M Y') }}</span>
                                <span class="text-muted small">({{ \Carbon\Carbon::parse($day['date'])->format('D') }})</span>
                                @if($isSelected)
                                    <span class="badge bg-primary ms-1">Active</span>
                                @endif
                            </td>
                            <td class="text-end font-monospace text-success">₹{{ number_format($day['deposits'], 2) }}</td>
                            <td class="text-end font-monospace text-warning">₹{{ number_format($day['withdrawals'], 2) }}</td>
                            <td class="text-end font-monospace text-info">₹{{ number_format($day['bets'], 2) }}</td>
                            <td class="text-end font-monospace text-danger">₹{{ number_format($day['payouts'], 2) }}</td>
                            <td class="text-end font-monospace fw-bold {{ $day['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $day['net_profit'] >= 0 ? '+' : '' }}₹{{ number_format($day['net_profit'], 2) }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">{{ $day['tx_count'] }}</span>
                            </td>
                            <td class="text-center pe-4">
                                <a href="{{ route('admin.payments.daily', ['date' => $day['date']]) }}" class="btn btn-sm {{ $isSelected ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">
                                    <i class="fa-solid fa-eye me-1"></i> View Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.payments.daily') }}" class="row g-2 align-items-center">
            <!-- Date Filter -->
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Target Date</label>
                <input type="date" name="date" class="form-control rounded-pill" value="{{ $selectedDate }}">
            </div>

            <!-- Type Filter -->
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Transaction Type</label>
                <select name="type" class="form-select rounded-pill">
                    <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Transaction Types</option>
                    <option value="deposit" {{ $type === 'deposit' ? 'selected' : '' }}>Deposit (Add Funds)</option>
                    <option value="withdrawal" {{ $type === 'withdrawal' ? 'selected' : '' }}>Withdrawal (Payout)</option>
                    <option value="bet_placed" {{ $type === 'bet_placed' ? 'selected' : '' }}>Bet Placed (Wager)</option>
                    <option value="bet_won" {{ $type === 'bet_won' ? 'selected' : '' }}>Bet Won (Player Win)</option>
                    <option value="admin_credit" {{ $type === 'admin_credit' ? 'selected' : '' }}>Admin Credit</option>
                    <option value="admin_debit" {{ $type === 'admin_debit' ? 'selected' : '' }}>Admin Debit</option>
                    <option value="admin_adjustment" {{ $type === 'admin_adjustment' ? 'selected' : '' }}>Admin Adjustment</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted mb-1">Status</label>
                <select name="status" class="form-select rounded-pill">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Search -->
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Search Keyword</label>
                <input type="text" name="search" class="form-control rounded-pill" placeholder="Name, phone, UTR, note..." value="{{ $search }}">
            </div>

            <!-- Buttons -->
            <div class="col-md-1 d-flex gap-1 align-self-end">
                <button type="submit" class="btn btn-primary rounded-pill fw-bold w-100" title="Apply Filter">
                    <i class="fa-solid fa-filter"></i>
                </button>
                <a href="{{ route('admin.payments.daily', ['date' => $selectedDate]) }}" class="btn btn-outline-secondary rounded-pill" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Detailed Daily Transactions Ledger -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-receipt text-primary me-2"></i> Transactions Ledger for {{ \Carbon\Carbon::parse($selectedDate)->format('d F Y') }}
        </h5>
        <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
            Showing {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} transactions
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID & Time</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Reference / Description</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Balance Trail</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-4">Proof / Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr>
                            <!-- ID & Time -->
                            <td class="ps-4">
                                <div class="fw-bold text-dark">#{{ $tx->id }}</div>
                                <div class="text-muted small font-monospace">
                                    {{ $tx->created_at ? $tx->created_at->timezone('Asia/Kolkata')->format('h:i:s A') : '--' }}
                                </div>
                            </td>

                            <!-- User -->
                            <td>
                                @if($tx->user)
                                    <div class="fw-bold text-dark">{{ $tx->user->name }}</div>
                                    <div class="text-muted small font-monospace">{{ $tx->user->phone }}</div>
                                @else
                                    <span class="text-muted small">System / Deleted</span>
                                @endif
                            </td>

                            <!-- Type -->
                            <td>
                                @if($tx->type === 'deposit')
                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">
                                        <i class="fa-solid fa-arrow-down me-1"></i> Deposit
                                    </span>
                                @elseif($tx->type === 'withdrawal')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning border-opacity-25 px-2 py-1 rounded-pill">
                                        <i class="fa-solid fa-arrow-up me-1"></i> Withdrawal
                                    </span>
                                @elseif($tx->type === 'bet_placed')
                                    <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill">
                                        <i class="fa-solid fa-dice me-1"></i> Bet Placed
                                    </span>
                                @elseif($tx->type === 'bet_won')
                                    <span class="badge bg-success text-white px-2 py-1 rounded-pill">
                                        <i class="fa-solid fa-trophy me-1"></i> Bet Won
                                    </span>
                                @elseif(in_array($tx->type, ['admin_credit', 'manual_credit']))
                                    <span class="badge bg-info-subtle text-info-emphasis px-2 py-1 rounded-pill">
                                        <i class="fa-solid fa-user-shield me-1"></i> Admin Credit
                                    </span>
                                @elseif(in_array($tx->type, ['admin_debit', 'manual_debit']))
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">
                                        <i class="fa-solid fa-user-shield me-1"></i> Admin Debit
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">
                                        {{ ucfirst(str_replace('_', ' ', $tx->type)) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Reference / Description -->
                            <td style="max-width: 280px;">
                                @if($tx->utr_number)
                                    <div class="small fw-bold text-dark">UTR: <span class="font-monospace text-primary">{{ $tx->utr_number }}</span></div>
                                @endif
                                @if($tx->admin_notes)
                                    <div class="small text-muted text-truncate" title="{{ $tx->admin_notes }}">
                                        {{ $tx->admin_notes }}
                                    </div>
                                @endif
                                @if($tx->account_details)
                                    <div class="small text-secondary font-monospace text-truncate" title="{{ $tx->account_details }}">
                                        {{ $tx->account_details }}
                                    </div>
                                @endif
                            </td>

                            <!-- Amount -->
                            <td class="text-end">
                                @php
                                    $isCredit = in_array($tx->type, ['deposit', 'bet_won', 'admin_credit', 'manual_credit']);
                                @endphp
                                <div class="fw-bold font-monospace fs-6 {{ $isCredit ? 'text-success' : 'text-danger' }}">
                                    {{ $isCredit ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}
                                </div>
                            </td>

                            <!-- Balance Trail -->
                            <td class="text-end font-monospace small">
                                <div class="text-muted">₹{{ number_format($tx->balance_before, 2) }}</div>
                                <div class="fw-bold text-dark">→ ₹{{ number_format($tx->balance_after, 2) }}</div>
                            </td>

                            <!-- Status -->
                            <td class="text-center">
                                @if(in_array($tx->status, ['completed', 'approved']))
                                    <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">
                                        <i class="fa-solid fa-check me-1"></i> {{ ucfirst($tx->status) }}
                                    </span>
                                @elseif($tx->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1 rounded-pill">
                                        <i class="fa-solid fa-clock me-1"></i> Pending
                                    </span>
                                @elseif($tx->status === 'rejected')
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">
                                        <i class="fa-solid fa-xmark me-1"></i> Rejected
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">
                                        {{ ucfirst($tx->status) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Proof / Action -->
                            <td class="text-center pe-4">
                                @if($tx->screenshot_url)
                                    <a href="{{ $tx->screenshot_url }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-2" title="View Uploaded Proof">
                                        <i class="fa-solid fa-image"></i>
                                    </a>
                                @elseif($tx->user_qr_url)
                                    <a href="{{ $tx->user_qr_url }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2" title="View User QR">
                                        <i class="fa-solid fa-qrcode"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-inbox fs-1 d-block mb-3 opacity-50"></i>
                                    <div class="fw-bold">No transactions found for this date & filter criteria.</div>
                                    <div class="small">Try selecting another date or clearing the search filter.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($transactions->hasPages())
        <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-end">
            {{ $transactions->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
