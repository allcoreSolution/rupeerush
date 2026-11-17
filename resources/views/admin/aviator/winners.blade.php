@extends('layouts.admin')

@section('title', 'Aviator Live - Winner List & Payouts')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-trophy text-danger me-2"></i> Aviator Winner List
        </h2>
        <p class="text-muted small mb-0">Record of all players who successfully cashed out in Aviator rounds.</p>
    </div>
    <div>
        <a href="{{ route('admin.aviator.index') }}" class="btn btn-outline-danger fw-bold rounded-pill px-4">
            <i class="fa-solid fa-plane-departure me-1"></i> Live Aviator Control
        </a>
    </div>
</div>

<!-- Stats Counter Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Winners</div>
                <div class="stat-value text-dark">{{ number_format($totalWinners) }}</div>
            </div>
            <div class="stat-icon" style="background: rgba(225, 29, 72, 0.15); color: #e11d48;">
                <i class="fa-solid fa-trophy"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Payouts Credited</div>
                <div class="stat-value text-success">₹{{ number_format($totalPayout, 2) }}</div>
            </div>
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Form -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.aviator.winners') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control rounded-pill" placeholder="Search player name, phone, round ID..." value="{{ $search }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-danger rounded-pill w-100 fw-bold">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Filter
                </button>
            </div>
            @if(!empty($search))
                <div class="col-md-2">
                    <a href="{{ route('admin.aviator.winners') }}" class="btn btn-outline-secondary rounded-pill w-100 fw-bold">
                        <i class="fa-solid fa-xmark me-1"></i> Reset
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- Winners Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Round ID</th>
                        <th>Player</th>
                        <th>Phone</th>
                        <th>Bet Slot</th>
                        <th>Bet Amount</th>
                        <th>Cashed Out At</th>
                        <th>Net Profit</th>
                        <th>Total Payout</th>
                        <th class="text-end">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wonBets as $bet)
                        @php
                            $profit = max(0, $bet->win_amount - $bet->amount);
                        @endphp
                        <tr>
                            <td class="font-monospace fw-bold text-dark">{{ $bet->round_id }}</td>
                            <td class="fw-bold">{{ $bet->user->name ?? 'User #'.$bet->user_id }}</td>
                            <td class="font-monospace text-muted">{{ $bet->user->phone ?? '-' }}</td>
                            <td><span class="badge bg-secondary font-monospace">Slot {{ $bet->bet_slot }}</span></td>
                            <td class="fw-bold font-monospace text-warning">₹{{ number_format($bet->amount, 2) }}</td>
                            <td>
                                <span class="badge font-monospace {{ $bet->cashout_multiplier >= 10.0 ? 'bg-warning text-dark' : ($bet->cashout_multiplier >= 2.0 ? 'bg-primary' : 'bg-info text-dark') }}">
                                    {{ number_format($bet->cashout_multiplier, 2) }}x
                                </span>
                            </td>
                            <td class="font-monospace text-success fw-bold">+₹{{ number_format($profit, 2) }}</td>
                            <td class="font-monospace text-success fw-bold">₹{{ number_format($bet->win_amount, 2) }}</td>
                            <td class="text-end text-muted font-monospace small">
                                {{ $bet->cashed_out_at ? $bet->cashed_out_at->format('d M Y, h:i A') : $bet->created_at->format('d M Y, h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="fa-solid fa-trophy fs-2 text-secondary mb-2 d-block"></i>
                                No winning Aviator bets found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($wonBets->hasPages())
            <div class="mt-3">
                {{ $wonBets->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
