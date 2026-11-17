@extends('layouts.admin')

@section('title', 'WinGo Color Game - Winner List & Payouts')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-trophy text-warning me-2"></i> WinGo Winner List
        </h2>
        <p class="text-muted small mb-0">Complete record of all winning players, multipliers, and credited payouts across WinGo color games.</p>
    </div>
    <div>
        <a href="{{ route('admin.wingo.index') }}" class="btn btn-outline-primary fw-bold rounded-pill px-4">
            <i class="fa-solid fa-gamepad me-1"></i> Live WinGo Control
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
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                <i class="fa-solid fa-award"></i>
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
        <form method="GET" action="{{ route('admin.wingo.winners') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control rounded-pill" placeholder="Search player name, phone, period..." value="{{ $search }}">
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select rounded-pill">
                    <option value="">All Game Modes (30s, 1m, 3m, 5m)</option>
                    <option value="30s" {{ $selectedType === '30s' ? 'selected' : '' }}>WinGo 30s</option>
                    <option value="1m" {{ $selectedType === '1m' ? 'selected' : '' }}>WinGo 1 Min</option>
                    <option value="3m" {{ $selectedType === '3m' ? 'selected' : '' }}>WinGo 3 Min</option>
                    <option value="5m" {{ $selectedType === '5m' ? 'selected' : '' }}>WinGo 5 Min</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control rounded-pill" value="{{ $selectedDate }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill fw-bold w-100">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                <a href="{{ route('admin.wingo.winners') }}" class="btn btn-outline-secondary rounded-pill" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Winners Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Player Info</th>
                        <th>Game & Period</th>
                        <th>Bet Selection</th>
                        <th class="text-end">Bet Amount</th>
                        <th class="text-center">Multiplier</th>
                        <th class="text-end">Winning Payout</th>
                        <th class="pe-4 text-end">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($winners as $w)
                        <tr>
                            <td class="ps-4 font-monospace text-muted small">#{{ $w->id }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $w->user->name ?? 'User Deleted' }}</div>
                                <div class="text-muted small">{{ $w->user->phone ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-primary text-uppercase">{{ $w->game_type }}</span>
                                <span class="font-monospace text-muted small ms-1">#{{ $w->period_id }}</span>
                            </td>
                            <td>
                                @php
                                    $badgeClass = 'bg-dark';
                                    if ($w->bet_selection === 'Green') $badgeClass = 'bg-success';
                                    elseif ($w->bet_selection === 'Violet') $badgeClass = 'bg-purple" style="background:#8b5cf6;';
                                    elseif ($w->bet_selection === 'Red') $badgeClass = 'bg-danger';
                                    elseif ($w->bet_selection === 'Big') $badgeClass = 'bg-warning text-dark';
                                    elseif ($w->bet_selection === 'Small') $badgeClass = 'bg-primary';
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ strtoupper($w->bet_type) }}: {{ $w->bet_selection }}
                                </span>
                            </td>
                            <td class="text-end font-monospace">
                                ₹{{ number_format($w->total_amount, 2) }}
                            </td>
                            <td class="text-center font-monospace fw-bold text-info">
                                {{ $w->win_multiplier }}X
                            </td>
                            <td class="text-end font-monospace fw-bold text-success">
                                +₹{{ number_format($w->win_amount, 2) }}
                            </td>
                            <td class="pe-4 text-end text-muted small">
                                {{ $w->evaluated_at ? \Carbon\Carbon::parse($w->evaluated_at)->format('d M, h:i A') : $w->created_at->format('d M, h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fa-solid fa-award fs-1 text-muted opacity-50 mb-3 d-block"></i>
                                No winning bets found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($winners->hasPages())
        <div class="card-footer bg-white border-0 py-3 px-4">
            {{ $winners->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
