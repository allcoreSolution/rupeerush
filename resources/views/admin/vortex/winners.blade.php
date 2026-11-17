@extends('layouts.admin')

@section('title', 'Vortex Concentric Wheel - Winners Leaderboard & Payouts')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-trophy text-info me-2"></i> Vortex Concentric Wheel Winner List
        </h2>
        <p class="text-muted small mb-0">Record of all players who conquered the concentric rings and cashed out high multipliers in Vortex.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.vortex.index') }}" class="btn btn-outline-info fw-bold rounded-pill px-4">
            <i class="fa-solid fa-arrows-spin me-1"></i> Live Vortex Control Hub
        </a>
    </div>
</div>

<!-- Stats Counter Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Payouts Credited</div>
                <div class="stat-value text-success">₹{{ number_format($totalPaidOut, 2) }}</div>
                <div class="text-muted small mt-1">To all winning players</div>
            </div>
            <div class="stat-icon bg-success-subtle text-success">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Highest Single Payout</div>
                <div class="stat-value text-dark">₹{{ number_format($highestPayout, 2) }}</div>
                <div class="text-muted small mt-1">Single round record</div>
            </div>
            <div class="stat-icon bg-primary-subtle text-primary">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Highest Multiplier</div>
                <div class="stat-value text-warning">{{ number_format($topMultiplier, 2) }}x</div>
                <div class="text-muted small mt-1">Record multiplier cashed out</div>
            </div>
            <div class="stat-icon bg-warning-subtle text-warning">
                <i class="fa-solid fa-fire"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Winning Rounds</div>
                <div class="stat-value text-info">{{ number_format($winners->total()) }}</div>
                <div class="text-muted small mt-1">Successful cashouts logged</div>
            </div>
            <div class="stat-icon bg-info-subtle text-info">
                <i class="fa-solid fa-trophy"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.vortex.winners') }}" class="row g-2 align-items-center">
            <div class="col-md-9">
                <input type="text" name="search" class="form-control rounded-pill" placeholder="Search player name, phone, Game ID..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-info text-white rounded-pill px-4 fw-bold w-100">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Search
                </button>
                @if(request()->hasAny(['search']))
                    <a href="{{ route('admin.vortex.winners') }}" class="btn btn-light rounded-pill px-3">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Winners Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Rank</th>
                        <th>Player</th>
                        <th>Game ID</th>
                        <th>Bet Amount</th>
                        <th>Ring Reached</th>
                        <th>Cashed Out Multiplier</th>
                        <th>Player Net Profit</th>
                        <th>Total Payout</th>
                        <th>50% Part Cashout</th>
                        <th class="text-end pe-4">Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($winners as $index => $winner)
                        @php
                            $rank = $winners->firstItem() + $index;
                            $userProfit = (float) $winner->win_amount - (float) $winner->bet_amount;
                        @endphp
                        <tr>
                            <td class="ps-4">
                                @if($rank == 1)
                                    <span class="badge bg-warning text-dark fs-6 rounded-pill px-3 py-1">🥇 1st</span>
                                @elseif($rank == 2)
                                    <span class="badge bg-secondary text-white fs-6 rounded-pill px-3 py-1">🥈 2nd</span>
                                @elseif($rank == 3)
                                    <span class="badge bg-danger text-white fs-6 rounded-pill px-3 py-1">🥉 3rd</span>
                                @else
                                    <span class="text-muted fw-bold">#{{ $rank }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $winner->user->name ?? 'User #' . $winner->user_id }}</div>
                                <div class="text-muted small">{{ $winner->user->phone ?? 'N/A' }}</div>
                            </td>
                            <td class="font-monospace text-info fw-bold">
                                #{{ $winner->game_id }}
                            </td>
                            <td class="fw-bold text-dark">
                                ₹{{ number_format($winner->bet_amount, 2) }}
                            </td>
                            <td>
                                @if($winner->current_ring == 1)
                                    <span class="badge bg-info-subtle text-info">Ring 1 (Water 🌊)</span>
                                @elseif($winner->current_ring == 2)
                                    <span class="badge bg-success-subtle text-success">Ring 2 (Earth 🦠)</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Ring 3 (Fire 🔥)</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark fw-bold px-2 py-1 fs-6">
                                    {{ number_format($winner->current_multiplier, 2) }}x
                                </span>
                            </td>
                            <td class="fw-bold {{ $userProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $userProfit >= 0 ? '+' : '' }}₹{{ number_format($userProfit, 2) }}
                            </td>
                            <td class="fw-bold text-success fs-6">
                                ₹{{ number_format($winner->win_amount, 2) }}
                            </td>
                            <td>
                                @if($winner->part_cashed_out)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="fa-solid fa-check me-1"></i> ₹{{ number_format($winner->part_cashout_amount, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted small">No</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 text-muted small">
                                <div>{{ $winner->updated_at->format('d M Y') }}</div>
                                <div>{{ $winner->updated_at->format('h:i:s A') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-trophy text-muted fa-3x mb-3 d-block opacity-50"></i>
                                No Vortex winning games recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($winners->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $winners->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
