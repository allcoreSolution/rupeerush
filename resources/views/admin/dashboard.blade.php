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
<!-- TOP 12 KPI STAT CARDS (ALL EQUAL PROPORTIONS & SIZES) -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xxl-6 g-3 mb-4">
    <!-- 1. Total Players -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #0284c7 !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary mb-2" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-users fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Total Players</div>
                <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($totalUsers) }}</div>
                <small class="text-success fw-semibold"><i class="fa-solid fa-circle-dot me-1"></i>{{ $activeUsers }} Active</small>
            </div>
        </div>
    </div>

    <!-- 2. Wallet Exposure -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #0ea5e9 !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info mb-2" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-wallet fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Wallet Exposure</div>
                <div class="fs-4 fw-bold text-primary mt-1 font-monospace">₹{{ number_format($totalUserBalances, 2) }}</div>
                <small class="text-muted">Total in player wallets</small>
            </div>
        </div>
    </div>

    <!-- 3. Total Deposits -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #10b981 !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success mb-2" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-circle-arrow-down fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Total Deposits</div>
                <div class="fs-4 fw-bold text-success mt-1 font-monospace">₹{{ number_format($totalDeposits, 2) }}</div>
                <small class="text-success"><i class="fa-solid fa-check me-1"></i>{{ $approvedDepositsCount }} Approved</small>
            </div>
        </div>
    </div>

    <!-- 4. Pending Deposits -->
    <div class="col">
        <a href="{{ route('admin.payments.deposits') }}" class="text-decoration-none text-dark">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid {{ $pendingDepositsCount > 0 ? '#ef4444' : '#cbd5e1' }} !important;">
                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 {{ $pendingDepositsCount > 0 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-light text-muted' }}" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-money-bill-transfer fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Pending Deposits</div>
                    <div class="fs-4 fw-bold mt-1 font-monospace {{ $pendingDepositsCount > 0 ? 'text-danger' : 'text-dark' }}">
                        {{ $pendingDepositsCount }}
                    </div>
                    <small class="{{ $pendingDepositsCount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                        ₹{{ number_format($pendingDepositsAmount, 2) }} {{ $pendingDepositsCount > 0 ? 'waiting' : 'pending' }}
                    </small>
                </div>
            </div>
        </a>
    </div>

    <!-- 5. Total Withdrawals -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #f59e0b !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning mb-2" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-arrow-up-from-bracket fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Total Withdrawals</div>
                <div class="fs-4 fw-bold text-dark mt-1 font-monospace">₹{{ number_format($totalWithdrawals, 2) }}</div>
                <small class="text-muted">{{ $settledWithdrawalsCount }} Settled Payouts</small>
            </div>
        </div>
    </div>

    <!-- 6. Pending Withdrawals -->
    <div class="col">
        <a href="{{ route('admin.payments.withdrawals') }}" class="text-decoration-none text-dark">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid {{ $pendingWithdrawalsCount > 0 ? '#f59e0b' : '#cbd5e1' }} !important;">
                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 {{ $pendingWithdrawalsCount > 0 ? 'bg-warning bg-opacity-10 text-warning' : 'bg-light text-muted' }}" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-clock fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Pending Withdrawals</div>
                    <div class="fs-4 fw-bold mt-1 font-monospace {{ $pendingWithdrawalsCount > 0 ? 'text-warning text-dark' : 'text-dark' }}">
                        {{ $pendingWithdrawalsCount }}
                    </div>
                    <small class="{{ $pendingWithdrawalsCount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                        ₹{{ number_format($pendingWithdrawalsAmount, 2) }} {{ $pendingWithdrawalsCount > 0 ? 'to pay' : 'pending' }}
                    </small>
                </div>
            </div>
        </a>
    </div>

    <!-- 7. Adjusted Balance -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #7c3aed !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px; background: rgba(124, 58, 237, 0.12); color: #7c3aed;">
                <i class="fa-solid fa-sliders fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem; color: #7c3aed !important;">Adjusted Balance</div>
                <div class="fs-4 fw-bold mt-1 font-monospace" style="color: #7c3aed;">
                    {{ $netAdjusted >= 0 ? '+' : '' }}₹{{ number_format($netAdjusted, 2) }}
                </div>
                <small class="text-muted" style="font-size: 0.72rem;">
                    +₹{{ number_format($adjustedCredits, 0) }} Cr | -₹{{ number_format($adjustedDebits, 0) }} Dr
                </small>
            </div>
        </div>
    </div>

    <!-- 8. Total Bets Wagered -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #64748b !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 text-secondary mb-2" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-coins fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Total Bets Wagered</div>
                <div class="fs-4 fw-bold text-dark mt-1 font-monospace">₹{{ number_format($totalBets, 2) }}</div>
                <small class="text-muted">{{ $totalBetsCount }} Total Bets Placed</small>
            </div>
        </div>
    </div>

    <!-- 9. Total Won (Payouts) -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #059669 !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success mb-2" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-trophy fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Total Won (Payouts)</div>
                <div class="fs-4 fw-bold text-success mt-1 font-monospace">₹{{ number_format($totalWon, 2) }}</div>
                <small class="text-success fw-semibold">{{ $totalWonCount }} Winning Bets</small>
            </div>
        </div>
    </div>

    <!-- 10. Platform GGR -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="{{ $netGgr >= 0 ? 'border-left: 4px solid #10b981 !important;' : 'border-left: 4px solid #ef4444 !important;' }}">
            <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 {{ $netGgr >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-chart-line fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Platform GGR</div>
                <div class="fs-4 fw-bold {{ $netGgr >= 0 ? 'text-success' : 'text-danger' }} mt-1 font-monospace">
                    {{ $netGgr >= 0 ? '+' : '' }}₹{{ number_format($netGgr, 2) }}
                </div>
                <small class="{{ $netGgr >= 0 ? 'text-success' : 'text-danger' }} fw-semibold" style="font-size: 0.72rem;">
                    {{ $netGgr >= 0 ? 'House Profit' : 'Player Surplus' }}
                </small>
            </div>
        </div>
    </div>

    <!-- 11. Today's Turnover -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #06b6d4 !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info mb-2" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-dice fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Today's Turnover</div>
                <div class="fs-4 fw-bold text-dark mt-1 font-monospace">₹{{ number_format($todayBetsAmount, 2) }}</div>
                <small class="text-muted">{{ $todayBetsCount }} Bets Today</small>
            </div>
        </div>
    </div>

    <!-- 12. Today's Net Profit / Loss -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="{{ $todayNetProfit >= 0 ? 'border-left: 4px solid #10b981 !important;' : 'border-left: 4px solid #ef4444 !important;' }}">
            <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 {{ $todayNetProfit >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-scale-balanced fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Today's Profit / Loss</div>
                <div class="fs-4 fw-bold {{ $todayNetProfit >= 0 ? 'text-success' : 'text-danger' }} mt-1 font-monospace">
                    {{ $todayNetProfit >= 0 ? '+' : '' }}₹{{ number_format($todayNetProfit, 2) }}
                </div>
                <small class="{{ $todayNetProfit >= 0 ? 'text-success' : 'text-danger' }} fw-semibold" style="font-size: 0.72rem;">
                    Won: ₹{{ number_format($todayPayoutsAmount, 0) }}
                </small>
            </div>
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

<!-- LOWER SECTION 3: PLATFORM GAMES PERFORMANCE MATRIX (FETCHED LIVE FROM DATABASE) -->
<div class="card-custom mb-4">
    <div class="card-custom-header">
        <h6 class="card-custom-title">
            <i class="fa-solid fa-layer-group text-primary"></i>
            <span>Platform Games Volume & Revenue Matrix (11 Active Modules)</span>
        </h6>
        <span class="small text-muted">Aggregated live from database betting engines</span>
    </div>
    <div class="p-3">
        <div class="row g-3">
            @foreach($gamePerformance as $key => $g)
                @php
                    $housePL = $g['total_wagered'] - $g['total_payout'];
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
                            <span class="badge bg-light text-muted border font-monospace">{{ $g['total_bets'] }} Bets</span>
                        </div>
                        
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Turnover:</span>
                            <span class="fw-bold font-monospace text-dark">₹{{ number_format($g['total_wagered'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Payouts:</span>
                            <span class="fw-bold font-monospace text-success">₹{{ number_format($g['total_payout'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between small border-top pt-1 mt-1">
                            <span class="fw-semibold">House Profit:</span>
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
@endsection
