@extends('layouts.admin')

@section('title', 'Andar Bahar Live - Winner List & Payouts')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-trophy text-warning me-2"></i> Andar Bahar Winner List
        </h2>
        <p class="text-muted small mb-0">Complete record of all winning players, multipliers, and credited payouts across Andar Bahar Live rounds.</p>
    </div>
    <div>
        <a href="{{ route('admin.andar-bahar.index') }}" class="btn btn-outline-primary fw-bold rounded-pill px-4">
            <i class="fa-solid fa-layer-group me-1"></i> Live Andar Bahar Control
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
        <form method="GET" action="{{ route('admin.andar-bahar.winners') }}" class="row g-2 align-items-center">
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
                    <a href="{{ route('admin.andar-bahar.winners') }}" class="btn btn-outline-secondary rounded-pill w-100 fw-bold">
                        Clear
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- Winners Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-award text-success me-2"></i> Payout Records
        </h5>
        <span class="badge bg-light text-dark border">Showing {{ $wonBets->count() }} of {{ $wonBets->total() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th class="ps-4">Winner Player</th>
                        <th>Round ID</th>
                        <th>Joker Card</th>
                        <th>Winning Side</th>
                        <th>Wagered Side</th>
                        <th>Stake Amount</th>
                        <th>Multiplier (Guna)</th>
                        <th class="text-end">Payout Won</th>
                        <th class="pe-4 text-end">Settled Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wonBets as $bet)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $bet->user->name ?? 'User #'.$bet->user_id }}</div>
                                <div class="small text-muted">{{ $bet->user->phone ?? '-' }}</div>
                            </td>
                            <td class="font-monospace fw-bold text-dark">#{{ $bet->round_id }}</td>
                            <td>
                                @php
                                    $joker = $bet->round->joker_card ?? null;
                                    $symJ = $joker ? ($joker['symbol'] ?? (match($joker['suit'] ?? '') { 'H' => '♥', 'D' => '♦', 'C' => '♣', default => '♠' })) : '';
                                    $isRedJ = $joker ? (($joker['color'] ?? '') === 'red' || in_array($joker['suit'] ?? '', ['H', 'D', '♥', '♦'])) : false;
                                @endphp
                                @if($joker)
                                    <span class="badge bg-light border fw-bold {{ $isRedJ ? 'text-danger' : 'text-dark' }}">
                                        {{ $joker['rank'] ?? '?' }}{{ $symJ }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ ($bet->round->winning_side ?? '') === 'andar' ? 'bg-primary' : 'bg-danger' }} text-uppercase px-2 py-1">
                                    {{ strtoupper($bet->round->winning_side ?? '-') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ strtolower($bet->bet_selection) === 'andar' ? 'bg-primary' : (strtolower($bet->bet_selection) === 'bahar' ? 'bg-danger' : 'bg-info text-dark') }} text-uppercase px-2 py-1">
                                    {{ strtoupper($bet->bet_selection) }}
                                </span>
                            </td>
                            <td class="fw-bold font-monospace">₹{{ number_format($bet->amount, 2) }}</td>
                            <td>
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle font-monospace fw-bold">
                                    {{ $bet->multiplier }}x
                                </span>
                            </td>
                            <td class="text-end fw-black text-success font-monospace fs-6">
                                +₹{{ number_format($bet->win_amount, 2) }}
                            </td>
                            <td class="pe-4 text-end text-muted small">
                                {{ $bet->created_at ? $bet->created_at->format('d M, h:i A') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-trophy fs-1 mb-2 d-block text-secondary opacity-50"></i>
                                No winner records found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($wonBets->hasPages())
            <div class="card-footer bg-light bg-opacity-50 border-top py-3 px-4">
                {{ $wonBets->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
