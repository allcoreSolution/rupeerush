@extends('layouts.admin')

@section('title', 'Dragon Tiger - Winner List & Payouts')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-trophy text-danger me-2"></i> Dragon Tiger Winner List
        </h2>
        <p class="text-muted small mb-0">Complete record of all winning players, multipliers, and credited payouts across Dragon Tiger rounds.</p>
    </div>
    <div>
        <a href="{{ route('admin.dragon-tiger.index') }}" class="btn btn-outline-primary fw-bold rounded-pill px-4">
            <i class="fa-solid fa-dragon me-1"></i> Live Dragon Tiger Control
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
        <form method="GET" action="{{ route('admin.dragon-tiger.winners') }}" class="row g-2 align-items-center">
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
                    <a href="{{ route('admin.dragon-tiger.winners') }}" class="btn btn-outline-secondary rounded-pill w-100 fw-bold">
                        Clear Filter
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- Winners Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        @if($winners->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fa-solid fa-trophy fs-1 mb-2 opacity-50"></i>
                <p class="mb-0">No winning records found.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Bet ID</th>
                            <th>Round ID</th>
                            <th>Player</th>
                            <th>Bet Choice</th>
                            <th>Wager</th>
                            <th>Multiplier</th>
                            <th>Payout Credited</th>
                            <th>Outcome Details</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($winners as $w)
                            <tr>
                                <td class="font-monospace fw-bold small">{{ $w->bet_id }}</td>
                                <td class="font-monospace text-primary fw-bold">{{ $w->round_id }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $w->user->name ?? 'User #'.$w->user_id }}</div>
                                    <div class="text-muted small">{{ $w->user->phone ?? '' }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ str_contains($w->bet_type, 'dragon') ? 'bg-danger' : (str_contains($w->bet_type, 'tiger') ? 'bg-primary' : 'bg-success') }} text-uppercase">
                                        {{ str_replace('_', ' ', $w->bet_type) }}
                                    </span>
                                </td>
                                <td class="font-monospace fw-bold">₹{{ number_format($w->bet_amount, 2) }}</td>
                                <td class="font-monospace text-muted">{{ $w->odds }}x</td>
                                <td>
                                    <span class="badge bg-success-subtle text-success fs-6 font-monospace px-3 py-1 fw-bold">
                                        +₹{{ number_format($w->payout_amount, 2) }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $w->hand_outcome ?? 'Round Won' }}</td>
                                <td class="small text-muted">{{ $w->created_at?->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $winners->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection

