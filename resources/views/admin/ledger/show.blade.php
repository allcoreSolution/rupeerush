@extends('layouts.admin')

@section('title', 'Player Statement - ' . $user->name)
@section('page_title', 'Player Account Statement & Passbook')

@section('styles')
<style>
    @media (min-width: 1400px) {
        .row-cols-xxl-7 > * {
            flex: 0 0 auto;
            width: 14.285714%;
        }
    }
    @media print {
        .admin-sidebar, .admin-topbar, .no-print, .btn, .pagination, .tab-pills {
            display: none !important;
        }
        .admin-content {
            padding: 0 !important;
        }
        .card-custom {
            border: none !important;
            box-shadow: none !important;
        }
        body {
            background: #fff !important;
            color: #000 !important;
        }
        .print-only {
            display: block !important;
        }
    }
    .print-only {
        display: none;
    }
    .btn-purple {
        background: #7c3aed;
        color: #ffffff;
        border-color: #7c3aed;
    }
    .btn-purple:hover {
        background: #6d28d9;
        color: #ffffff;
        border-color: #6d28d9;
    }
    .btn-outline-purple {
        color: #7c3aed;
        border-color: #7c3aed;
        background: transparent;
    }
    .btn-outline-purple:hover {
        background: #7c3aed;
        color: #ffffff;
    }
</style>
@endsection

@section('content')
<!-- BREADCRUMB & ACTION ROW -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 no-print">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.ledger.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Ledger Master
        </a>
        <span class="text-muted">/</span>
        <span class="fw-bold text-dark fs-5">{{ $user->name }}</span>
        <span class="badge bg-light text-muted border font-monospace">User ID: #{{ $user->id }}</span>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <!-- Direct Adjust Balance Button -->
        <button type="button" class="btn btn-sm btn-purple rounded-pill px-3 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2" onclick="openBalanceModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->balance }}')">
            <i class="fa-solid fa-sliders"></i>
            <span>Adjust Balance (+ / -)</span>
        </button>

        <!-- Export CSV Button -->
        <a href="{{ route('admin.ledger.export', ['user' => $user->id, 'type' => $type, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
            <i class="fa-solid fa-file-csv"></i>
            <span>Export CSV</span>
        </a>

        <!-- Print Statement Button -->
        <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2" onclick="window.print()">
            <i class="fa-solid fa-print"></i>
            <span>Print Passbook</span>
        </button>
    </div>
</div>

<!-- PRINT HEADER (ONLY VISIBLE ON PRINT) -->
<div class="print-only mb-4 border-bottom pb-3">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold mb-0">RUPEERUSH GAMING HUB</h2>
            <h5 class="text-muted">Official Player Account Statement / Ledger</h5>
        </div>
        <div class="text-end">
            <div><strong>Date:</strong> {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}</div>
            <div><strong>Statement Ref:</strong> STMT-USR-{{ $user->id }}-{{ time() }}</div>
        </div>
    </div>
</div>

<!-- PLAYER PROFILE SUMMARY BANNER -->
<div class="card-custom p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white fw-bold font-monospace shadow-sm" style="width: 56px; height: 56px; font-size: 1.4rem;">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                    <span>{{ $user->name }}</span>
                    @if($user->status === 'active')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill fs-6 fw-bold">Active</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill fs-6 fw-bold">Banned</span>
                    @endif
                </h4>
                <div class="d-flex align-items-center gap-3 flex-wrap small text-muted">
                    <span><i class="fa-solid fa-phone me-1 text-primary"></i> <strong>{{ $user->phone }}</strong></span>
                    <span>&bull;</span>
                    <span><i class="fa-solid fa-envelope me-1 text-info"></i> {{ $user->email ?: 'No email registered' }}</span>
                    <span>&bull;</span>
                    <span><i class="fa-solid fa-ticket me-1 text-warning"></i> Ref: <strong class="font-monospace text-dark">{{ $user->referral_code }}</strong></span>
                    <span>&bull;</span>
                    <span><i class="fa-solid fa-calendar me-1"></i> Registered: {{ $user->created_at?->format('d M Y, h:i A') }}</span>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 no-print">
            <form method="POST" action="{{ route('admin.users.toggle-status', $user->id) }}" onsubmit="return confirm('Change status for {{ $user->name }}?');">
                @csrf
                @if($user->status === 'active')
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                        <i class="fa-solid fa-ban me-1"></i> Suspend Player
                    </button>
                @else
                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3">
                        <i class="fa-solid fa-check me-1"></i> Activate Player
                    </button>
                @endif
            </form>
        </div>
    </div>
</div>

<!-- 7 KEY FINANCIAL METRIC CARDS (ALL EQUAL SIZES) -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 row-cols-xxl-7 g-3 mb-4">
    <!-- 1. Current Wallet Balance -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #0284c7 !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary mb-2" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-wallet fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Wallet Balance</div>
                <div class="fs-4 fw-bold text-primary mt-1 font-monospace">₹{{ number_format($user->balance, 2) }}</div>
                <small class="text-muted">Real-time Available</small>
            </div>
        </div>
    </div>

    <!-- 2. Total Deposits -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #10b981 !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success mb-2" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-circle-arrow-down fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Total Deposits</div>
                <div class="fs-4 fw-bold text-success mt-1 font-monospace">₹{{ number_format($stats['deposit_approved'], 2) }}</div>
                <small class="text-success fw-semibold">{{ $stats['deposit_count'] }} Approved Credits</small>
            </div>
        </div>
    </div>

    <!-- 3. Total Withdrawals -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #f59e0b !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning mb-2" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-arrow-up-from-bracket fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Total Withdrawals</div>
                <div class="fs-4 fw-bold text-dark mt-1 font-monospace">₹{{ number_format($stats['withdrawal_approved'], 2) }}</div>
                @if($stats['withdrawal_pending_count'] > 0)
                    <small class="text-danger fw-bold"><i class="fa-solid fa-clock me-1"></i>₹{{ number_format($stats['withdrawal_pending'], 2) }} Pending</small>
                @else
                    <small class="text-muted">{{ $stats['withdrawal_count'] }} Settled Payouts</small>
                @endif
            </div>
        </div>
    </div>

    <!-- 4. Adjusted Balance Card -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #7c3aed !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px; background: rgba(124, 58, 237, 0.12); color: #7c3aed;">
                <i class="fa-solid fa-sliders fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem; color: #7c3aed !important;">
                    Adjusted Balance
                </div>
                <div class="fs-4 fw-bold mt-1 font-monospace" style="color: #7c3aed;">
                    {{ $stats['admin_net_adjusted'] >= 0 ? '+' : '' }}₹{{ number_format($stats['admin_net_adjusted'], 2) }}
                </div>
                <small class="text-muted" style="font-size: 0.72rem;">
                    +₹{{ number_format($stats['admin_credits'], 0) }} Cr | -₹{{ number_format($stats['admin_debits'], 0) }} Dr
                </small>
            </div>
        </div>
    </div>

    <!-- 5. Total Turnover (Bets Placed) -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #64748b !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 text-secondary mb-2" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-coins fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Turnover (Wagered)</div>
                <div class="fs-4 fw-bold text-dark mt-1 font-monospace">₹{{ number_format($stats['total_bet'], 2) }}</div>
                <small class="text-muted">{{ $stats['bet_count'] }} Total Bets Placed</small>
            </div>
        </div>
    </div>

    <!-- 6. Total Won (Payouts) -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #059669 !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success mb-2" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-trophy fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Total Won (Payouts)</div>
                <div class="fs-4 fw-bold text-success mt-1 font-monospace">₹{{ number_format($stats['total_won'], 2) }}</div>
                <small class="text-success fw-semibold">{{ $stats['won_count'] }} Winning Bets</small>
            </div>
        </div>
    </div>

    <!-- 7. Net Gaming P/L -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="{{ $stats['net_pl'] > 0 ? 'border-left: 4px solid #10b981 !important;' : 'border-left: 4px solid #ef4444 !important;' }}">
            <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 {{ $stats['net_pl'] > 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-scale-balanced fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Player Net P/L</div>
                <div class="fs-4 fw-bold {{ $stats['net_pl'] > 0 ? 'text-success' : 'text-danger' }} mt-1 font-monospace">
                    {{ $stats['net_pl'] > 0 ? '+' : '' }}₹{{ number_format($stats['net_pl'], 2) }}
                </div>
                <small class="{{ $stats['net_pl'] > 0 ? 'text-success' : 'text-danger' }} fw-bold" style="font-size: 0.72rem;">
                    {{ $stats['net_pl'] > 0 ? 'Player in Profit' : 'Player in Loss' }}
                </small>
            </div>
        </div>
    </div>
</div>

<!-- BANK PASSBOOK TRANSACTION STATEMENT -->
<div class="card-custom mb-4">
    <div class="card-custom-header">
        <div>
            <h5 class="card-custom-title mb-1">
                <i class="fa-solid fa-file-invoice-dollar text-primary"></i>
                <span>Official Account Statement & Passbook</span>
            </h5>
            <small class="text-muted">Complete audit trail of all Deposits, Withdrawals, Manual Balance Adjustments, Bets Placed & Bets Won</small>
        </div>
    </div>

    <!-- QUICK-FILTER PILL TABS -->
    <div class="p-3 border-bottom bg-light no-print">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <!-- Tabs -->
            <div class="d-flex align-items-center gap-2 flex-wrap tab-pills">
                <!-- All -->
                <a href="{{ route('admin.ledger.show', array_merge(request()->except('type', 'page'), ['user' => $user->id, 'type' => 'all'])) }}" 
                   class="btn btn-sm rounded-pill px-3 py-1 fw-bold {{ $type === 'all' ? 'btn-dark' : 'btn-outline-secondary bg-white' }}">
                    <i class="fa-solid fa-list me-1"></i> All Transactions ({{ $typeCounts['all'] }})
                </a>

                <!-- Deposits -->
                <a href="{{ route('admin.ledger.show', array_merge(request()->except('type', 'page'), ['user' => $user->id, 'type' => 'deposit'])) }}" 
                   class="btn btn-sm rounded-pill px-3 py-1 fw-bold {{ $type === 'deposit' ? 'btn-success text-white' : 'btn-outline-success bg-white' }}">
                    <i class="fa-solid fa-arrow-down me-1"></i> Deposits ({{ $typeCounts['deposit'] }})
                </a>

                <!-- Withdrawals -->
                <a href="{{ route('admin.ledger.show', array_merge(request()->except('type', 'page'), ['user' => $user->id, 'type' => 'withdrawal'])) }}" 
                   class="btn btn-sm rounded-pill px-3 py-1 fw-bold {{ $type === 'withdrawal' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark bg-white' }}">
                    <i class="fa-solid fa-arrow-up me-1"></i> Withdrawals ({{ $typeCounts['withdrawal'] }})
                </a>

                <!-- Admin Adjustments -->
                <a href="{{ route('admin.ledger.show', array_merge(request()->except('type', 'page'), ['user' => $user->id, 'type' => 'admin_adjustment'])) }}" 
                   class="btn btn-sm rounded-pill px-3 py-1 fw-bold {{ $type === 'admin_adjustment' ? 'btn-purple' : 'btn-outline-purple bg-white' }}">
                    <i class="fa-solid fa-sliders me-1"></i> Manual Adjustments ({{ $typeCounts['admin_adjustment'] }})
                </a>

                <!-- Bets Placed -->
                <a href="{{ route('admin.ledger.show', array_merge(request()->except('type', 'page'), ['user' => $user->id, 'type' => 'bet_placed'])) }}" 
                   class="btn btn-sm rounded-pill px-3 py-1 fw-bold {{ $type === 'bet_placed' ? 'btn-secondary text-white' : 'btn-outline-secondary bg-white' }}">
                    <i class="fa-solid fa-dice me-1"></i> Bets Placed ({{ $typeCounts['bet_placed'] }})
                </a>

                <!-- Bets Won -->
                <a href="{{ route('admin.ledger.show', array_merge(request()->except('type', 'page'), ['user' => $user->id, 'type' => 'bet_won'])) }}" 
                   class="btn btn-sm rounded-pill px-3 py-1 fw-bold {{ $type === 'bet_won' ? 'btn-info text-white' : 'btn-outline-info bg-white' }}">
                    <i class="fa-solid fa-trophy me-1"></i> Bets Won ({{ $typeCounts['bet_won'] }})
                </a>
            </div>

            <!-- Date Range & Search Form -->
            <form method="GET" action="{{ route('admin.ledger.show', $user->id) }}" class="d-flex align-items-center gap-2 flex-wrap">
                <input type="hidden" name="type" value="{{ $type }}">
                
                <!-- Date From -->
                <input type="date" name="date_from" class="form-control form-control-sm font-monospace bg-white" value="{{ $dateFrom }}" style="width: 140px;" title="From Date">
                
                <!-- Date To -->
                <input type="date" name="date_to" class="form-control form-control-sm font-monospace bg-white" value="{{ $dateTo }}" style="width: 140px;" title="To Date">

                <!-- Search text -->
                <div class="input-group input-group-sm" style="width: 190px;">
                    <input type="text" name="search" class="form-control bg-white" placeholder="Search ref, notes..." value="{{ $search }}">
                    <button class="btn btn-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>

                @if($type !== 'all' || $dateFrom || $dateTo || $search !== '')
                    <a href="{{ route('admin.ledger.show', $user->id) }}" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Reset Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- STATEMENT TABLE -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="statementTable">
            <thead class="table-light small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                <tr>
                    <th class="ps-3" style="width: 150px;">Date & Time</th>
                    <th style="width: 120px;">Txn ID / Ref</th>
                    <th>Type & Narration Details</th>
                    <th class="text-end" style="width: 130px;">Debit (-)</th>
                    <th class="text-end" style="width: 130px;">Credit (+)</th>
                    <th class="text-end" style="width: 150px;">Running Balance</th>
                    <th class="pe-3 text-center" style="width: 110px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                    @php
                        // Determine Debit vs Credit
                        $isDebit = in_array($t->type, ['withdrawal', 'bet_placed']) || 
                                   ($t->type === 'admin_adjustment' && $t->balance_after < $t->balance_before) ||
                                   ($t->type === 'admin_adjustment' && str_starts_with(strtolower($t->admin_notes ?? ''), 'admin debit'));
                        $isCredit = in_array($t->type, ['deposit', 'bet_won']) || 
                                    ($t->type === 'admin_adjustment' && $t->balance_after > $t->balance_before) ||
                                    ($t->type === 'admin_adjustment' && str_starts_with(strtolower($t->admin_notes ?? ''), 'admin credit'));

                        $badgeInfo = match($t->type) {
                            'deposit' => ['bg' => 'bg-success text-white', 'icon' => 'fa-circle-arrow-down', 'label' => 'DEPOSIT'],
                            'withdrawal' => ['bg' => 'bg-danger text-white', 'icon' => 'fa-arrow-up-from-bracket', 'label' => 'WITHDRAWAL'],
                            'bet_placed' => ['bg' => 'bg-secondary text-white', 'icon' => 'fa-dice', 'label' => 'BET PLACED'],
                            'bet_won' => ['bg' => 'bg-warning text-dark', 'icon' => 'fa-trophy', 'label' => 'BET WON'],
                            'admin_adjustment' => ['bg' => 'btn-purple text-white', 'icon' => 'fa-sliders', 'label' => 'MANUAL ADJUSTMENT'],
                            default => ['bg' => 'bg-light text-dark', 'icon' => 'fa-circle-info', 'label' => strtoupper($t->type)],
                        };

                        $statusBadge = match($t->status) {
                            'approved', 'completed' => 'bg-success text-white',
                            'pending' => 'bg-warning text-dark',
                            'rejected' => 'bg-danger text-white',
                            default => 'bg-secondary text-white',
                        };
                    @endphp
                    <tr>
                        <!-- 1. Date & Time -->
                        <td class="ps-3">
                            <div class="fw-bold text-dark font-monospace" style="font-size: 0.85rem;">
                                {{ $t->created_at?->format('d M Y') }}
                            </div>
                            <small class="text-muted font-monospace" style="font-size: 0.73rem;">
                                {{ $t->created_at?->format('h:i:s A') }}
                            </small>
                            <div class="text-muted" style="font-size: 0.68rem;">
                                {{ $t->created_at?->diffForHumans() }}
                            </div>
                        </td>

                        <!-- 2. Txn ID / Ref -->
                        <td>
                            <div class="font-monospace fw-bold text-dark">#TXN-{{ $t->id }}</div>
                            @if(!empty($t->utr_number))
                                <div class="font-monospace text-dark fw-semibold" style="font-size: 0.72rem;">
                                    UTR: {{ $t->utr_number }}
                                </div>
                            @endif
                        </td>

                        <!-- 3. Narration / Description with Rich Details -->
                        <td>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <!-- Type Badge -->
                                <span class="badge {{ $badgeInfo['bg'] }} px-2 py-1 rounded-pill fw-bold" style="font-size: 0.68rem;">
                                    <i class="fa-solid {{ $badgeInfo['icon'] }} me-1"></i> {{ $badgeInfo['label'] }}
                                </span>

                                <!-- If Deposit: show payment method & QR Platform -->
                                @if($t->type === 'deposit')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold" style="font-size: 0.68rem;">
                                        <i class="fa-solid fa-qrcode me-1"></i> QR: {{ $t->payment_method ?: 'UPI' }}
                                    </span>
                                @endif

                                <!-- If Admin Adjustment: show Credit/Debit direction tag -->
                                @if($t->type === 'admin_adjustment')
                                    @if($isCredit)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold" style="font-size: 0.68rem;">
                                            <i class="fa-solid fa-plus-circle me-1"></i> CREDIT (+) ADDED
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold" style="font-size: 0.68rem;">
                                            <i class="fa-solid fa-minus-circle me-1"></i> DEBIT (-) DEDUCTED
                                        </span>
                                    @endif
                                @endif

                                <!-- Reviewer Admin -->
                                @if($t->reviewer)
                                    <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.68rem;">
                                        <i class="fa-solid fa-user-shield me-1"></i> By: {{ $t->reviewer->name }}
                                    </span>
                                @endif
                            </div>

                            <!-- Narration text -->
                            <div class="small text-dark fw-semibold">
                                {{ $t->admin_notes ?: 'Account transaction processed.' }}
                            </div>

                            <!-- If Deposit: show Platform QR details if recorded -->
                            @if($t->type === 'deposit' && $t->account_details)
                                <div class="text-muted small mt-1 font-monospace" style="font-size: 0.72rem;">
                                    <i class="fa-solid fa-circle-info text-success me-1"></i> {{ $t->account_details }}
                                </div>
                            @endif

                            <!-- If Withdrawal: show Bank details if present -->
                            @if($t->type === 'withdrawal' && ($t->account_details || $t->account_number || $t->account_holder_name))
                                <div class="text-muted small mt-1 font-monospace" style="font-size: 0.72rem;">
                                    @if($t->account_holder_name)<span>Name: <strong>{{ $t->account_holder_name }}</strong> &bull; </span>@endif
                                    @if($t->account_number)<span>A/C: <strong>{{ $t->account_number }}</strong> &bull; </span>@endif
                                    @if($t->ifsc_code)<span>IFSC: <strong>{{ $t->ifsc_code }}</strong> &bull; </span>@endif
                                    @if($t->account_details)<span>{{ $t->account_details }}</span>@endif
                                </div>
                            @endif

                            <!-- Attachments / Screenshots -->
                            <div class="d-flex align-items-center gap-2 mt-1 flex-wrap no-print">
                                @if($t->screenshot_path)
                                    <a href="{{ asset($t->screenshot_path) }}" target="_blank" class="badge bg-light text-primary border text-decoration-none">
                                        <i class="fa-solid fa-image me-1"></i> View Payment Receipt
                                    </a>
                                @endif
                                @if($t->admin_proof_path)
                                    <a href="{{ asset($t->admin_proof_path) }}" target="_blank" class="badge bg-light text-success border text-decoration-none">
                                        <i class="fa-solid fa-receipt me-1"></i> View Admin Payout Proof
                                    </a>
                                @endif
                                @if($t->user_qr_path)
                                    <a href="{{ asset($t->user_qr_path) }}" target="_blank" class="badge bg-light text-dark border text-decoration-none">
                                        <i class="fa-solid fa-qrcode me-1"></i> User QR Code
                                    </a>
                                @endif
                            </div>
                        </td>

                        <!-- 4. Debit (-) -->
                        <td class="text-end">
                            @if($isDebit)
                                <span class="font-monospace fw-bold text-danger fs-6">
                                    -₹{{ number_format($t->amount, 2) }}
                                </span>
                            @else
                                <span class="text-muted font-monospace">-</span>
                            @endif
                        </td>

                        <!-- 5. Credit (+) -->
                        <td class="text-end">
                            @if($isCredit)
                                <span class="font-monospace fw-bold text-success fs-6">
                                    +₹{{ number_format($t->amount, 2) }}
                                </span>
                            @else
                                <span class="text-muted font-monospace">-</span>
                            @endif
                        </td>

                        <!-- 6. Running Balance -->
                        <td class="text-end">
                            <div class="font-monospace fw-bold text-dark fs-6">
                                ₹{{ number_format($t->balance_after, 2) }}
                            </div>
                            <small class="text-muted font-monospace" style="font-size: 0.72rem;">
                                (Was: ₹{{ number_format($t->balance_before, 2) }})
                            </small>
                        </td>

                        <!-- 7. Status -->
                        <td class="pe-3 text-center">
                            <span class="badge {{ $statusBadge }} rounded-pill px-2 py-1 text-uppercase fw-bold" style="font-size: 0.68rem;">
                                {{ $t->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-receipt fs-2 mb-2 d-block text-secondary opacity-50"></i>
                            <div>No transaction entries found for the selected filter.</div>
                            @if($type !== 'all' || $dateFrom || $dateTo || $search !== '')
                                <a href="{{ route('admin.ledger.show', $user->id) }}" class="btn btn-sm btn-outline-primary rounded-pill mt-2">
                                    Clear Filters
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    @if($transactions->hasPages())
        <div class="p-3 border-top bg-light no-print">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <span class="small text-muted">
                    Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} transactions
                </span>
                {{ $transactions->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<!-- GAME-BY-GAME ACTIVITY SUMMARY (COLLAPSIBLE ACCORDION) -->
<div class="card-custom mb-4 no-print">
    <div class="card-custom-header cursor-pointer" data-bs-toggle="collapse" data-bs-target="#gameBreakdownCollapse" style="cursor: pointer;">
        <div>
            <h6 class="card-custom-title mb-0">
                <i class="fa-solid fa-gamepad text-primary"></i>
                <span>Game Turnover & Winnings Breakdown (11 Game Modules)</span>
            </h6>
            <small class="text-muted">Click to view game-wise bets, wagered amount, and payout statistics</small>
        </div>
        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" type="button">
            <i class="fa-solid fa-chevron-down"></i> Toggle Games
        </button>
    </div>

    <div class="collapse" id="gameBreakdownCollapse">
        <div class="p-3">
            <div class="row g-3">
                @foreach($gameStats as $key => $g)
                    @php
                        $gameNet = $g['won'] - $g['wagered'];
                    @endphp
                    <div class="col-sm-6 col-md-4 col-xl-3">
                        <div class="card border rounded-3 p-3 h-100 bg-light-subtle">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 28px; height: 28px; background: {{ $g['color'] }}; font-size: 0.8rem;">
                                        <i class="fa-solid {{ $g['icon'] }}"></i>
                                    </div>
                                    <span>{{ $g['name'] }}</span>
                                </div>
                                <span class="badge bg-light text-muted border font-monospace">{{ $g['bets'] }} Bets</span>
                            </div>
                            
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Wagered:</span>
                                <span class="fw-bold font-monospace text-dark">₹{{ number_format($g['wagered'], 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Won:</span>
                                <span class="fw-bold font-monospace text-success">₹{{ number_format($g['won'], 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between small border-top pt-1 mt-1">
                                <span class="fw-semibold">Player P/L:</span>
                                <span class="fw-bold font-monospace {{ $gameNet >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $gameNet >= 0 ? '+' : '' }}₹{{ number_format($gameNet, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- MODAL: DIRECT WALLET BALANCE ADJUSTMENT -->
<div class="modal fade no-print" id="balanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <form id="balanceForm" method="POST" action="">
                @csrf
                <div class="modal-header bg-dark text-white p-3">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i class="fa-solid fa-sliders text-warning"></i>
                        <span>Adjust Player Wallet Balance</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Player Account</label>
                        <input type="text" class="form-control bg-light font-monospace fw-bold" id="modalUserName" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Action Type</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="action" id="actionCredit" value="credit" checked>
                                <label class="btn btn-outline-success w-100 fw-bold py-2" for="actionCredit">
                                    <i class="fa-solid fa-plus-circle me-1"></i> Credit (+) Add
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="action" id="actionDebit" value="debit">
                                <label class="btn btn-outline-danger w-100 fw-bold py-2" for="actionDebit">
                                    <i class="fa-solid fa-minus-circle me-1"></i> Debit (-) Deduct
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Amount (₹)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">₹</span>
                            <input type="number" step="0.01" min="1" class="form-control font-monospace fw-bold fs-5" name="amount" placeholder="e.g. 500" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Audit Reason / Narration Note</label>
                        <input type="text" class="form-control" name="notes" placeholder="e.g. Manual bank deposit, bonus reward, dispute correction" required>
                        <small class="text-muted" style="font-size: 0.72rem;">This note will appear directly on the player's bank statement & ledger audit log.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-purple rounded-pill btn-sm px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-check me-1"></i> Execute Adjustment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openBalanceModal(id, name, balance) {
        document.getElementById('balanceForm').action = `{{ url('admin/ledger') }}/${id}/balance`;
        document.getElementById('modalUserName').value = `${name} (Current: ₹${Number(balance).toFixed(2)})`;
        const modal = new bootstrap.Modal(document.getElementById('balanceModal'));
        modal.show();
    }
</script>
@endsection
