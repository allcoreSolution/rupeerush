@extends('layouts.admin')

@section('title', 'Mines 5x5 - Winners Leaderboard & Payouts')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-trophy text-success me-2"></i> Mines 5x5 Winner List
        </h2>
        <p class="text-muted small mb-0">Record of all players who successfully cashed out and unlocked jackpots in Mines 5x5.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.mines.index') }}" class="btn btn-outline-success fw-bold rounded-pill px-4">
            <i class="fa-solid fa-bomb me-1"></i> Live Mines Control Hub
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
                <i class="fa-solid fa-rocket"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Jackpot Hits</div>
                <div class="stat-value text-danger">{{ number_format($jackpotWinnersCount) }}</div>
                <div class="text-muted small mt-1">Progressive pool won</div>
            </div>
            <div class="stat-icon bg-danger-subtle text-danger">
                <i class="fa-solid fa-gift"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.mines.winners') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control rounded-pill" placeholder="Search player name, phone, Game ID..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <div class="form-check form-switch d-flex align-items-center gap-2 h-100 my-auto">
                    <input class="form-check-input" type="checkbox" name="jackpot" value="1" id="jackpotFilterWinners" {{ request('jackpot') == '1' ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="form-check-label fw-bold text-warning small" for="jackpotFilterWinners">
                        🎁 Show Jackpot Wins Only
                    </label>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success rounded-pill w-100 fw-bold">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Filter
                </button>
            </div>
            @if(request()->hasAny(['search', 'jackpot']))
                <div class="col-md-2">
                    <a href="{{ route('admin.mines.winners') }}" class="btn btn-outline-secondary rounded-pill w-100 fw-bold">
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
                        <th class="ps-3">Game ID</th>
                        <th>Player</th>
                        <th>Phone</th>
                        <th>Bet Amount</th>
                        <th>Mines Count</th>
                        <th>Steps Cleared</th>
                        <th>Cashout Multiplier</th>
                        <th>Net Profit</th>
                        <th>Total Payout</th>
                        <th class="text-end pe-3">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($winners as $win)
                        @php
                            $profit = max(0, (float) $win->win_amount - (float) $win->bet_amount);
                        @endphp
                        <tr>
                            <td class="ps-3 font-monospace fw-bold text-dark">
                                #{{ $win->game_id }}
                                @if($win->is_jackpot_win)
                                    <span class="badge bg-warning text-dark ms-1">🎁 Jackpot</span>
                                @endif
                            </td>
                            <td class="fw-bold text-dark">{{ $win->user->name ?? 'User #'.$win->user_id }}</td>
                            <td class="font-monospace text-muted small">{{ $win->user->phone ?? '-' }}</td>
                            <td class="fw-bold font-monospace">₹{{ number_format($win->bet_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    <i class="fa-solid fa-bomb me-1"></i> {{ $win->mines_count }}
                                </span>
                            </td>
                            <td class="fw-bold font-monospace text-primary">
                                {{ $win->current_step }} / {{ 25 - $win->mines_count }}
                            </td>
                            <td>
                                <span class="badge font-monospace {{ $win->current_multiplier >= 10.0 ? 'bg-warning text-dark' : ($win->current_multiplier >= 3.0 ? 'bg-primary' : 'bg-success') }} px-2 py-1">
                                    {{ number_format($win->current_multiplier, 2) }}x
                                </span>
                            </td>
                            <td class="fw-bold font-monospace text-success">+₹{{ number_format($profit, 2) }}</td>
                            <td class="fw-bold font-monospace text-dark fs-6">₹{{ number_format($win->win_amount, 2) }}</td>
                            <td class="text-end pe-3 text-muted small" style="font-size: 0.75rem;">
                                {{ $win->created_at->format('d M Y, h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-trophy text-muted fs-2 d-block mb-2" style="opacity: 0.4;"></i>
                                No winning games recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($winners->hasPages())
            <div class="mt-3">
                {{ $winners->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
