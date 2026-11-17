@extends('layouts.admin')

@section('title', 'Player Winner List & Payout History')
@section('page_title', 'Player Winner List & Payouts')

@section('content')
<!-- KPI Stat Cards Row -->
<div class="row g-3 mb-4">
    <!-- Total Winners Count -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Winners</div>
                <div class="stat-value text-success">
                    <i class="fa-solid fa-trophy text-warning me-1"></i> {{ number_format($totalWinnersCount) }}
                </div>
                <div class="small text-muted mt-1">Winning bets matching official results</div>
            </div>
            <div class="stat-icon bg-warning-subtle text-warning">
                <i class="fa-solid fa-crown"></i>
            </div>
        </div>
    </div>

    <!-- Total Win Payouts Amount -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Prize Payouts</div>
                <div class="stat-value text-success font-monospace">
                    ₹{{ number_format($totalPayoutAmount, 2) }}
                </div>
                <div class="small text-muted mt-1">Distributed into player wallets</div>
            </div>
            <div class="stat-icon bg-success-subtle text-success">
                <i class="fa-solid fa-coins"></i>
            </div>
        </div>
    </div>

    <!-- Total Wagers behind winning bets -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Wagered (Winning Bets)</div>
                <div class="stat-value text-primary font-monospace">
                    ₹{{ number_format($totalWagerAmount, 2) }}
                </div>
                <div class="small text-muted mt-1">Original bet stakes by winners</div>
            </div>
            <div class="stat-icon bg-primary-subtle text-primary">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card-custom">
    <div class="card-custom-header">
        <div class="d-flex align-items-center gap-2">
            <h6 class="card-custom-title mb-0">
                <i class="fa-solid fa-award text-warning"></i>
                <span>Winner History</span>
            </h6>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">
                {{ $totalWinnersCount }} Winners
            </span>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.winners.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Date Filter -->
            <div class="input-group input-group-sm" style="width: auto;">
                <span class="input-group-text bg-light"><i class="fa-regular fa-calendar"></i></span>
                <input type="date" class="form-control" name="date" value="{{ $selectedDate }}" title="Filter by Date">
            </div>

            <!-- Slot Time Filter -->
            <select class="form-select form-select-sm" name="slot_time" style="width: auto;">
                <option value="">All Time Slots</option>
                @foreach($slots as $s)
                    <option value="{{ $s->slot_time }}" {{ $selectedSlot === $s->slot_time ? 'selected' : '' }}>
                        {{ $s->slot_time }}
                    </option>
                @endforeach
            </select>

            <!-- Search input -->
            <div class="input-group input-group-sm" style="min-width: 200px;">
                <input type="text" class="form-control" name="search" placeholder="Search player, phone, bet ID..." value="{{ $search }}">
                <button class="btn btn-outline-secondary" type="submit" title="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>

            @if($search || $selectedSlot || $selectedDate !== \Carbon\Carbon::now('Asia/Kolkata')->format('Y-m-d'))
                <a href="{{ route('admin.winners.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Clear Filters">
                    <i class="fa-solid fa-xmark me-1"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted">
                <tr>
                    <th>Bet ID</th>
                    <th>Winner (Player)</th>
                    <th>Slot & Date</th>
                    <th>Column</th>
                    <th>Winning Number</th>
                    <th>Bet Stake</th>
                    <th>Multiplier</th>
                    <th>Prize Won</th>
                    <th>Evaluated At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($winners as $w)
                    <tr>
                        <td class="font-monospace small text-muted">#{{ $w->id }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $w->user->name ?? 'User Deleted' }}</div>
                            <div class="text-muted small"><i class="fa-solid fa-phone me-1"></i>{{ $w->user->phone ?? 'N/A' }}</div>
                            <div class="text-secondary small">Wallet: ₹{{ number_format($w->user->balance ?? 0, 2) }}</div>
                        </td>
                        <td>
                            <div class="fw-bold font-monospace text-primary">{{ $w->slot_time }}</div>
                            <div class="small text-muted">{{ $w->result_date ? $w->result_date->format('d M Y') : 'Today' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-primary px-3 py-1 rounded-pill fw-bold">{{ $w->slot_key }}</span>
                        </td>
                        <td>
                            <span class="badge bg-warning-subtle text-dark border border-warning font-monospace fs-6 px-3 py-1 rounded-3 fw-bold">
                                {{ $w->chosen_number }}
                            </span>
                        </td>
                        <td class="font-monospace fw-bold text-dark">
                            ₹{{ number_format($w->amount, 2) }}
                        </td>
                        <td class="small text-muted font-monospace">
                            {{ number_format($w->win_multiplier, 0) }}x
                        </td>
                        <td class="font-monospace fw-bold text-success fs-6">
                            +₹{{ number_format($w->win_amount, 2) }}
                        </td>
                        <td class="small text-muted">
                            {{ $w->evaluated_at ? $w->evaluated_at->timezone('Asia/Kolkata')->format('d M, h:i A') : $w->updated_at->timezone('Asia/Kolkata')->format('d M, h:i A') }}
                        </td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">
                                <i class="fa-solid fa-circle-check me-1"></i> Wallet Credited
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-trophy fs-1 d-block mb-2 text-warning opacity-50"></i>
                            <div class="fw-bold fs-6">No Winning Bets Found</div>
                            <small class="text-muted">No winning bets match the selected filter criteria.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($winners->hasPages())
        <div class="p-3 border-top bg-light">
            {{ $winners->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
