@extends('layouts.admin')

@section('title', '20-20 Teen Patti - Winner List & Payouts')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-trophy text-danger me-2"></i> 20-20 Teen Patti Winner List
        </h2>
        <p class="text-muted small mb-0">Complete record of all winning players, multipliers, and credited payouts across 20-20 Teen Patti rounds.</p>
    </div>
    <div>
        <a href="{{ route('admin.teen-patti.index') }}" class="btn btn-outline-primary fw-bold rounded-pill px-4">
            <i class="fa-solid fa-heart me-1"></i> Live Teen Patti Control
        </a>
    </div>
</div>

<!-- Stats Counter Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Winning Bets</div>
                <div class="stat-value text-dark">{{ number_format($totalWinners) }}</div>
            </div>
            <div class="stat-icon" style="background: rgba(220, 38, 38, 0.15); color: #dc2626;">
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
        <form method="GET" action="{{ route('admin.teen-patti.winners') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control rounded-pill" placeholder="Search player name, phone, round ID..." value="{{ $search }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Filter
                </button>
            </div>
            @if(!empty($search))
                <div class="col-md-2">
                    <a href="{{ route('admin.teen-patti.winners') }}" class="btn btn-outline-secondary rounded-pill w-100 fw-bold">
                        <i class="fa-solid fa-xmark me-1"></i> Reset
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- Winners Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th class="ps-4">Bet ID</th>
                        <th>Round ID</th>
                        <th>Player</th>
                        <th>Bet Type</th>
                        <th>Stake</th>
                        <th>Odds</th>
                        <th>Payout Amount</th>
                        <th>Winning Hand / Rank</th>
                        <th class="pe-4 text-end">Settled Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($winners as $w)
                        <tr>
                            <td class="ps-4 font-monospace fw-bold text-dark">#{{ $w->id }}</td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                    {{ $w->round_id }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $w->user->name ?? 'User #'.$w->user_id }}</div>
                                <div class="text-muted small font-monospace">{{ $w->user->phone ?? '-' }}</div>
                            </td>
                            <td>
                                @php
                                    $map = [
                                        'player_a' => '<span class="badge bg-primary px-2 py-1">PLAYER A</span>',
                                        'player_b' => '<span class="badge bg-danger px-2 py-1">PLAYER B</span>',
                                        'tie' => '<span class="badge bg-success px-2 py-1">TIE</span>',
                                        'pair_plus_a' => '<span class="badge bg-warning text-dark px-2 py-1">PAIR+ A</span>',
                                        'pair_plus_b' => '<span class="badge bg-warning text-dark px-2 py-1">PAIR+ B</span>',
                                    ];
                                @endphp
                                {!! $map[$w->bet_type] ?? $w->bet_type !!}
                            </td>
                            <td class="font-monospace fw-bold">₹{{ number_format($w->amount, 2) }}</td>
                            <td class="font-monospace text-warning fw-bold">{{ number_format($w->odds, 2) }}X</td>
                            <td class="font-monospace text-success fw-bold fs-6">
                                +₹{{ number_format($w->payout_amount, 2) }}
                            </td>
                            <td>
                                <span class="badge bg-secondary font-monospace">
                                    {{ $w->round->winning_rank_title ?? 'Won' }}
                                </span>
                            </td>
                            <td class="pe-4 text-end text-muted small font-monospace">
                                {{ $w->created_at->format('d M Y, h:i:s A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-trophy fa-2x text-muted mb-2 d-block opacity-50"></i>
                                No winning bets found.
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
