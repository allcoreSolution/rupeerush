@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_title', 'Dashboard & Business Overview')

@section('styles')
<style>
    @media (min-width: 1400px) {
        .row-cols-xxl-6 > * {
            flex: 0 0 auto;
            width: 16.666667%;
        }
    }
</style>
@endsection

@section('content')
<!-- TOP 12 KPI STAT CARDS (COMPACT & SPACE EFFICIENT) -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xxl-6 g-2 mb-3">
    <!-- 1. Total Players -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-3 p-2.5 px-3 h-100 bg-white" style="border-left: 3px solid #0284c7 !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary mb-1.5" style="width: 30px; height: 30px; font-size: 0.85rem;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.3px;">Total Players</div>
                <div class="fs-5 fw-bold text-dark mt-0.5 font-monospace" style="line-height: 1.2;">{{ number_format($totalUsers) }}</div>
                <small class="text-success fw-semibold" style="font-size: 0.7rem;"><i class="fa-solid fa-circle-dot me-1"></i>{{ $activeUsers }} Active</small>
            </div>
        </div>
    </div>

    <!-- 2. Wallet Exposure -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-3 p-2.5 px-3 h-100 bg-white" style="border-left: 3px solid #0ea5e9 !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info mb-1.5" style="width: 30px; height: 30px; font-size: 0.85rem;">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.3px;">Wallet Exposure</div>
                <div class="fs-5 fw-bold text-primary mt-0.5 font-monospace" style="line-height: 1.2;">₹{{ number_format($totalUserBalances, 2) }}</div>
                <small class="text-muted" style="font-size: 0.7rem;">In player wallets</small>
            </div>
        </div>
    </div>

    <!-- 3. Total Deposits -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-3 p-2.5 px-3 h-100 bg-white" style="border-left: 3px solid #10b981 !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success mb-1.5" style="width: 30px; height: 30px; font-size: 0.85rem;">
                <i class="fa-solid fa-circle-arrow-down"></i>
            </div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.3px;">Total Deposits</div>
                <div class="fs-5 fw-bold text-success mt-0.5 font-monospace" style="line-height: 1.2;">₹{{ number_format($totalDeposits, 2) }}</div>
                <small class="text-success" style="font-size: 0.7rem;"><i class="fa-solid fa-check me-1"></i>{{ $approvedDepositsCount }} Approved</small>
            </div>
        </div>
    </div>

    <!-- 4. Pending Deposits -->
    <div class="col">
        <a href="{{ route('admin.payments.deposits') }}" class="text-decoration-none text-dark">
            <div class="card border-0 shadow-sm rounded-3 p-2.5 px-3 h-100 bg-white" style="border-left: 3px solid {{ $pendingDepositsCount > 0 ? '#ef4444' : '#cbd5e1' }} !important;">
                <div class="rounded-circle d-flex align-items-center justify-content-center mb-1.5 {{ $pendingDepositsCount > 0 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-light text-muted' }}" style="width: 30px; height: 30px; font-size: 0.85rem;">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </div>
                <div>
                    <div class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.3px;">Pending Deposits</div>
                    <div class="fs-5 fw-bold mt-0.5 font-monospace {{ $pendingDepositsCount > 0 ? 'text-danger' : 'text-dark' }}" style="line-height: 1.2;">
                        {{ $pendingDepositsCount }}
                    </div>
                    <small class="{{ $pendingDepositsCount > 0 ? 'text-danger fw-bold' : 'text-muted' }}" style="font-size: 0.7rem;">
                        ₹{{ number_format($pendingDepositsAmount, 2) }} {{ $pendingDepositsCount > 0 ? 'waiting' : 'pending' }}
                    </small>
                </div>
            </div>
        </a>
    </div>

    <!-- 5. Total Withdrawals -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-3 p-2.5 px-3 h-100 bg-white" style="border-left: 3px solid #f59e0b !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning mb-1.5" style="width: 30px; height: 30px; font-size: 0.85rem;">
                <i class="fa-solid fa-arrow-up-from-bracket"></i>
            </div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.3px;">Total Withdrawals</div>
                <div class="fs-5 fw-bold text-dark mt-0.5 font-monospace" style="line-height: 1.2;">₹{{ number_format($totalWithdrawals, 2) }}</div>
                <small class="text-muted" style="font-size: 0.7rem;">{{ $settledWithdrawalsCount }} Settled Payouts</small>
            </div>
        </div>
    </div>

    <!-- 6. Pending Withdrawals -->
    <div class="col">
        <a href="{{ route('admin.payments.withdrawals') }}" class="text-decoration-none text-dark">
            <div class="card border-0 shadow-sm rounded-3 p-2.5 px-3 h-100 bg-white" style="border-left: 3px solid {{ $pendingWithdrawalsCount > 0 ? '#f59e0b' : '#cbd5e1' }} !important;">
                <div class="rounded-circle d-flex align-items-center justify-content-center mb-1.5 {{ $pendingWithdrawalsCount > 0 ? 'bg-warning bg-opacity-10 text-warning' : 'bg-light text-muted' }}" style="width: 30px; height: 30px; font-size: 0.85rem;">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <div class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.3px;">Pending Withdrawals</div>
                    <div class="fs-5 fw-bold mt-0.5 font-monospace {{ $pendingWithdrawalsCount > 0 ? 'text-warning text-dark' : 'text-dark' }}" style="line-height: 1.2;">
                        {{ $pendingWithdrawalsCount }}
                    </div>
                    <small class="{{ $pendingWithdrawalsCount > 0 ? 'text-danger fw-bold' : 'text-muted' }}" style="font-size: 0.7rem;">
                        ₹{{ number_format($pendingWithdrawalsAmount, 2) }} {{ $pendingWithdrawalsCount > 0 ? 'to pay' : 'pending' }}
                    </small>
                </div>
            </div>
        </a>
    </div>

    <!-- 7. Adjusted Balance -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-3 p-2.5 px-3 h-100 bg-white" style="border-left: 3px solid #7c3aed !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center mb-1.5" style="width: 30px; height: 30px; background: rgba(124, 58, 237, 0.12); color: #7c3aed; font-size: 0.85rem;">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.3px; color: #7c3aed !important;">Adjusted Balance</div>
                <div class="fs-5 fw-bold mt-0.5 font-monospace" style="color: #7c3aed; line-height: 1.2;">
                    {{ $netAdjusted >= 0 ? '+' : '' }}₹{{ number_format($netAdjusted, 2) }}
                </div>
                <small class="text-muted" style="font-size: 0.68rem;">
                    +₹{{ number_format($adjustedCredits, 0) }} | -₹{{ number_format($adjustedDebits, 0) }}
                </small>
            </div>
        </div>
    </div>

    <!-- 8. Total Bets Wagered -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-3 p-2.5 px-3 h-100 bg-white" style="border-left: 3px solid #64748b !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 text-secondary mb-1.5" style="width: 30px; height: 30px; font-size: 0.85rem;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.3px;">Total Bets Wagered</div>
                <div class="fs-5 fw-bold text-dark mt-0.5 font-monospace" style="line-height: 1.2;">₹{{ number_format($totalBets, 2) }}</div>
                <small class="text-muted" style="font-size: 0.7rem;">{{ $totalBetsCount }} Bets Placed</small>
            </div>
        </div>
    </div>

    <!-- 9. Total Won (Payouts) -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-3 p-2.5 px-3 h-100 bg-white" style="border-left: 3px solid #059669 !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success mb-1.5" style="width: 30px; height: 30px; font-size: 0.85rem;">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.3px;">Total Won (Payouts)</div>
                <div class="fs-5 fw-bold text-success mt-0.5 font-monospace" style="line-height: 1.2;">₹{{ number_format($totalWon, 2) }}</div>
                <small class="text-success fw-semibold" style="font-size: 0.7rem;">{{ $totalWonCount }} Winning Bets</small>
            </div>
        </div>
    </div>

    <!-- 10. Platform GGR -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-3 p-2.5 px-3 h-100 bg-white" style="{{ $netGgr >= 0 ? 'border-left: 3px solid #10b981 !important;' : 'border-left: 3px solid #ef4444 !important;' }}">
            <div class="rounded-circle d-flex align-items-center justify-content-center mb-1.5 {{ $netGgr >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}" style="width: 30px; height: 30px; font-size: 0.85rem;">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.3px;">Platform GGR</div>
                <div class="fs-5 fw-bold {{ $netGgr >= 0 ? 'text-success' : 'text-danger' }} mt-0.5 font-monospace" style="line-height: 1.2;">
                    {{ $netGgr >= 0 ? '+' : '' }}₹{{ number_format($netGgr, 2) }}
                </div>
                <small class="{{ $netGgr >= 0 ? 'text-success' : 'text-danger' }} fw-semibold" style="font-size: 0.7rem;">
                    {{ $netGgr >= 0 ? 'House Profit' : 'Player Surplus' }}
                </small>
            </div>
        </div>
    </div>

    <!-- 11. Today's Turnover -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-3 p-2.5 px-3 h-100 bg-white" style="border-left: 3px solid #06b6d4 !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info mb-1.5" style="width: 30px; height: 30px; font-size: 0.85rem;">
                <i class="fa-solid fa-dice"></i>
            </div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.3px;">Today's Turnover</div>
                <div class="fs-5 fw-bold text-dark mt-0.5 font-monospace" style="line-height: 1.2;">₹{{ number_format($todayBetsAmount, 2) }}</div>
                <small class="text-muted" style="font-size: 0.7rem;">{{ $todayBetsCount }} Bets Today</small>
            </div>
        </div>
    </div>

    <!-- 12. Today's Net Profit / Loss -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-3 p-2.5 px-3 h-100 bg-white" style="{{ $todayNetProfit >= 0 ? 'border-left: 3px solid #10b981 !important;' : 'border-left: 3px solid #ef4444 !important;' }}">
            <div class="rounded-circle d-flex align-items-center justify-content-center mb-1.5 {{ $todayNetProfit >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}" style="width: 30px; height: 30px; font-size: 0.85rem;">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.3px;">Today's Profit / Loss</div>
                <div class="fs-5 fw-bold {{ $todayNetProfit >= 0 ? 'text-success' : 'text-danger' }} mt-0.5 font-monospace" style="line-height: 1.2;">
                    {{ $todayNetProfit >= 0 ? '+' : '' }}₹{{ number_format($todayNetProfit, 2) }}
                </div>
                <small class="{{ $todayNetProfit >= 0 ? 'text-success' : 'text-danger' }} fw-semibold" style="font-size: 0.7rem;">
                    Won: ₹{{ number_format($todayPayoutsAmount, 0) }}
                </small>
            </div>
        </div>
    </div>
</div>

<!-- PLATFORM GAMES VOLUME & REVENUE MATRIX (11 ACTIVE MODULES) - PLACED DIRECTLY BELOW KPI CARDS -->
<div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden">
    <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 30px; height: 30px; font-size: 0.85rem;">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.92rem;">
                    Platform Games Volume & Revenue Matrix (11 Active Modules)
                </h6>
                <div class="text-muted" style="font-size: 0.7rem;">Live betting volume, turnover & profitability comparison across platform modules</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge rounded-pill px-2.5 py-1 text-success border border-success border-opacity-25" style="background-color: #ecfdf5; font-size: 0.72rem;">
                <i class="fa-solid fa-crown me-1 text-warning"></i> Green: Top Volume Leader
            </span>
            <span class="badge rounded-pill px-2.5 py-1 text-danger border border-danger border-opacity-25" style="background-color: #fef2f2; font-size: 0.72rem;">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> Red: Lowest Volume
            </span>
        </div>
    </div>
    <div class="p-3 bg-light bg-opacity-25">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xxl-6 g-2">
            @foreach($gamePerformance as $key => $g)
                @php
                    $housePL = $g['total_wagered'] - $g['total_payout'];
                    $isHighest = $g['is_highest'] ?? false;
                    $isLowest = $g['is_lowest'] ?? false;
                @endphp
                <div class="col">
                    <div class="card rounded-3 p-2.5 h-100 position-relative"
                        style="
                            @if($isHighest)
                                background: linear-gradient(180deg, #ecfdf5 0%, #f0fdf4 100%) !important;
                                border: 2px solid #10b981 !important;
                                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2) !important;
                            @elseif($isLowest)
                                background: linear-gradient(180deg, #fef2f2 0%, #fff1f2 100%) !important;
                                border: 2px solid #ef4444 !important;
                                box-shadow: 0 4px 12px rgba(239, 68, 68, 0.16) !important;
                            @else
                                background: #ffffff !important;
                                border: 1px solid #e2e8f0 !important;
                            @endif
                        ">
                        
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-bold text-dark d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 24px; height: 24px; background: {{ $g['color'] }}; font-size: 0.72rem; flex-shrink: 0;">
                                    <i class="fa-solid {{ $g['icon'] }}"></i>
                                </div>
                                <span class="text-truncate" style="max-width: 95px;" title="{{ $g['name'] }}">{{ $g['name'] }}</span>
                            </div>
                            
                            @if($isHighest)
                                <span class="badge text-white px-2 py-0.5 rounded-pill shadow-sm" style="background: #10b981; font-size: 0.67rem;" title="Most Bets Placed">
                                    <i class="fa-solid fa-crown me-0.5 text-warning"></i> #1 Top
                                </span>
                            @elseif($isLowest)
                                <span class="badge text-white px-2 py-0.5 rounded-pill shadow-sm" style="background: #ef4444; font-size: 0.67rem;" title="Least Bets Placed">
                                    <i class="fa-solid fa-arrow-down me-0.5"></i> Min
                                </span>
                            @else
                                <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.67rem;">
                                    {{ $g['total_bets'] }} Bets
                                </span>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.72rem;">
                            <span class="text-muted">Total Bets:</span>
                            <span class="fw-bold font-monospace text-dark">{{ number_format($g['total_bets']) }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.72rem;">
                            <span class="text-muted">Turnover:</span>
                            <span class="fw-bold font-monospace text-dark">₹{{ number_format($g['total_wagered'], 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.72rem;">
                            <span class="text-muted">Payouts:</span>
                            <span class="fw-bold font-monospace text-success">₹{{ number_format($g['total_payout'], 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center border-top pt-1 mt-1" style="font-size: 0.73rem;">
                            <span class="fw-semibold text-muted">House P/L:</span>
                            <span class="fw-bold font-monospace {{ $housePL >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $housePL >= 0 ? '+' : '' }}₹{{ number_format($housePL, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- LIVE LOTTERY TIMING STATUS BANNER -->
<div class="card-custom mb-4 border-info">
    <div class="card-custom-body bg-light py-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-danger text-white px-3 py-2 rounded-pill fw-bold">
                <i class="fa-solid fa-circle text-white me-1" style="font-size: 8px;"></i> LIVE ENGINE
            </span>
            <div>
                <strong>Next Slot: {{ $timingStatus['nextUpcomingSlot']['slot_time'] }}</strong>
                <span class="text-muted ms-2">(Auto-generates 2 min early at {{ $timingStatus['nextUpcomingSlot']['gen_datetime']->format('h:i A') }})</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.results.index') }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
                <i class="fa-solid fa-sliders me-1"></i> Control / Preset Numbers
            </a>
            <form method="POST" action="{{ route('admin.results.instant-generate') }}" onsubmit="return confirm('Instantly generate results and execute payouts for the upcoming slot?');">
                @csrf
                <button type="submit" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold text-dark">
                    <i class="fa-solid fa-bolt me-1"></i> Instant Generate
                </button>
            </form>
        </div>
    </div>
</div>

<!-- LOWER SECTION 1: RECENT DEPOSITS & RECENT WITHDRAWALS (FROM DATABASE) -->
<div class="row g-4 mb-4">
    <!-- Left Column: Recent Deposit Requests -->
    <div class="col-lg-6">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h6 class="card-custom-title">
                    <i class="fa-solid fa-money-bill-transfer text-primary"></i>
                    <span>Recent Deposit Requests</span>
                </h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.payments.daily') }}" class="btn btn-sm btn-info text-white rounded-pill px-3 fw-bold">
                        <i class="fa-solid fa-calendar-days me-1"></i> Daily Detail
                    </a>
                    <a href="{{ route('admin.payments.deposits') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">View All</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted text-uppercase" style="font-size: 0.72rem;">
                        <tr>
                            <th class="ps-3">Player</th>
                            <th>Amount</th>
                            <th>UTR / Ref</th>
                            <th>Status</th>
                            <th class="pe-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentDeposits as $dep)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">
                                        @if($dep->user)
                                            <a href="{{ route('admin.ledger.show', $dep->user->id) }}" class="text-decoration-none text-dark hover-primary">
                                                {{ $dep->user->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">User #{{ $dep->user_id }}</span>
                                        @endif
                                    </div>
                                    <small class="text-muted font-monospace">{{ $dep->user->phone ?? '' }}</small>
                                </td>
                                <td class="font-monospace fw-bold text-success">+₹{{ number_format($dep->amount, 2) }}</td>
                                <td class="font-monospace small text-muted">
                                    <div>{{ $dep->utr_number ?: '-' }}</div>
                                    @if($dep->payment_method)
                                        <span class="badge bg-light text-primary border border-primary-subtle rounded-pill font-monospace" style="font-size: 0.65rem;">
                                            {{ $dep->payment_method }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($dep->status === 'approved')
                                        <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Approved</span>
                                    @elseif($dep->status === 'rejected')
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">Rejected</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1 rounded-pill">Pending</span>
                                    @endif
                                </td>
                                <td class="pe-3 text-end">
                                    @if($dep->status === 'pending')
                                        <form method="POST" action="{{ route('admin.payments.deposits.approve', $dep->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-2 py-0 fw-bold" title="Approve Deposit">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.payments.deposits') }}?search={{ $dep->utr_number }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">No deposit records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Recent Withdrawal Requests -->
    <div class="col-lg-6">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h6 class="card-custom-title">
                    <i class="fa-solid fa-arrow-up-from-bracket text-warning"></i>
                    <span>Recent Withdrawal Requests</span>
                </h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.payments.withdrawals') }}" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3 fw-bold">View All</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted text-uppercase" style="font-size: 0.72rem;">
                        <tr>
                            <th class="ps-3">Player</th>
                            <th>Amount</th>
                            <th>Bank / UPI</th>
                            <th>Status</th>
                            <th class="pe-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentWithdrawals as $wth)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">
                                        @if($wth->user)
                                            <a href="{{ route('admin.ledger.show', $wth->user->id) }}" class="text-decoration-none text-dark hover-primary">
                                                {{ $wth->user->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">User #{{ $wth->user_id }}</span>
                                        @endif
                                    </div>
                                    <small class="text-muted font-monospace">{{ $wth->user->phone ?? '' }}</small>
                                </td>
                                <td class="font-monospace fw-bold text-danger">-₹{{ number_format($wth->amount, 2) }}</td>
                                <td class="small text-muted font-monospace">
                                    {{ Str::limit($wth->account_details ?: ($wth->account_number ?: 'UPI/Bank'), 20) }}
                                </td>
                                <td>
                                    @if(in_array($wth->status, ['approved', 'completed']))
                                        <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Paid</span>
                                    @elseif($wth->status === 'rejected')
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">Rejected</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1 rounded-pill">Pending</span>
                                    @endif
                                </td>
                                <td class="pe-3 text-end">
                                    <a href="{{ route('admin.payments.withdrawals') }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">No withdrawal requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- LOWER SECTION 2: LIVE WAGERS ACROSS ALL GAMES & WINNING PAYOUTS -->
<div class="row g-4 mb-4">
    <!-- Left: Live Wagers Placed (All Games) -->
    <div class="col-lg-6">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h6 class="card-custom-title">
                    <i class="fa-solid fa-gamepad text-success"></i>
                    <span>Latest Wagers Placed (All Games)</span>
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border font-monospace">{{ $totalBetsCount }} Total Bets</span>
                    <a href="{{ route('admin.bets.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">View Bets</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted text-uppercase" style="font-size: 0.72rem;">
                        <tr>
                            <th class="ps-3">Player</th>
                            <th>Game & Description</th>
                            <th class="text-end">Bet Amount</th>
                            <th class="pe-3 text-end">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentWagers as $bet)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">
                                        @if($bet->user)
                                            <a href="{{ route('admin.ledger.show', $bet->user->id) }}" class="text-decoration-none text-dark hover-primary">
                                                {{ $bet->user->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">User #{{ $bet->user_id }}</span>
                                        @endif
                                    </div>
                                    <small class="text-muted font-monospace">{{ $bet->user->phone ?? '' }}</small>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">
                                        {{ $bet->admin_notes ?: 'Wager Placed' }}
                                    </div>
                                    <small class="text-muted font-monospace" style="font-size: 0.68rem;">#TXN-{{ $bet->id }}</small>
                                </td>
                                <td class="text-end font-monospace fw-bold text-danger">
                                    -₹{{ number_format($bet->amount, 2) }}
                                </td>
                                <td class="pe-3 text-end small text-muted font-monospace">
                                    {{ $bet->created_at?->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted small">No wagers recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right: Recent Winning Payouts -->
    <div class="col-lg-6">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h6 class="card-custom-title">
                    <i class="fa-solid fa-trophy text-warning"></i>
                    <span>Latest Winning Payouts</span>
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border font-monospace">{{ $totalWonCount }} Total Wins</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted text-uppercase" style="font-size: 0.72rem;">
                        <tr>
                            <th class="ps-3">Player</th>
                            <th>Game & Payout Description</th>
                            <th class="text-end">Won Payout</th>
                            <th class="pe-3 text-end">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentWins as $win)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">
                                        @if($win->user)
                                            <a href="{{ route('admin.ledger.show', $win->user->id) }}" class="text-decoration-none text-dark hover-primary">
                                                {{ $win->user->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">User #{{ $win->user_id }}</span>
                                        @endif
                                    </div>
                                    <small class="text-muted font-monospace">{{ $win->user->phone ?? '' }}</small>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-success">
                                        {{ $win->admin_notes ?: 'Winning Payout Credited' }}
                                    </div>
                                    <small class="text-muted font-monospace" style="font-size: 0.68rem;">#TXN-{{ $win->id }}</small>
                                </td>
                                <td class="text-end font-monospace fw-bold text-success">
                                    +₹{{ number_format($win->amount, 2) }}
                                </td>
                                <td class="pe-3 text-end small text-muted font-monospace">
                                    {{ $win->created_at?->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted small">No win payouts recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
