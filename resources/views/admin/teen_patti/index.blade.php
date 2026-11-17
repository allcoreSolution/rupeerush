@extends('layouts.admin')

@section('title', '20-20 Teen Patti Live Control & Risk Hub')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-heart text-danger me-2"></i> 20-20 Teen Patti Live Control Hub
        </h2>
        <p class="text-muted small mb-0">Real-time synchronized round monitor, house risk exposure calculation, 1-click winner override & game settings.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.teen-patti.winners') }}" class="btn btn-outline-success fw-bold rounded-pill px-3">
            <i class="fa-solid fa-trophy me-1"></i> Winner List
        </a>
        <a href="{{ route('teen-patti.index') }}" target="_blank" class="btn btn-warning text-dark fw-bold rounded-pill px-3">
            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Player Arena
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Row: Live Round Overview & Realtime Exposure -->
<div class="row g-4 mb-4">

    <!-- Active Round & Result Control -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger text-white px-3 py-2 fs-6 fw-bold">20-20 TEEN PATTI</span>
                    <span class="badge bg-light text-dark border px-3 py-2 fs-6 font-monospace">
                        Round #{{ $round->round_id }}
                    </span>
                </div>
                <div>
                    @if($roundData['is_locked'])
                        <span class="badge bg-danger px-3 py-2 fs-6">
                            <i class="fa-solid fa-lock me-1"></i> Cards Dealing in Progress
                        </span>
                    @else
                        <span class="badge bg-success px-3 py-2 fs-6">
                            <i class="fa-solid fa-spinner fa-spin me-1"></i> Betting Open
                        </span>
                    @endif
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Round Metrics -->
                <div class="row g-3 text-center mb-4">
                    <div class="col-sm-3 col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small fw-bold text-uppercase">Time Left</div>
                            <div class="fs-2 fw-bold font-monospace text-primary" id="adminTimerDisplay">
                                {{ $roundData['remaining_seconds'] }}s
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small fw-bold text-uppercase">Active Bets</div>
                            <div class="fs-2 fw-bold font-monospace text-dark">
                                {{ $exposure['total_bets_count'] }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small fw-bold text-uppercase">Control Mode</div>
                            @if($round->is_manual || $setting->teen_patti_control_mode === 'manual_override')
                                <span class="badge bg-danger fs-6 mt-1 px-2 py-1">
                                    <i class="fa-solid fa-crosshairs me-1"></i> MANUAL
                                </span>
                            @elseif($setting->teen_patti_control_mode === 'house_trap')
                                <span class="badge bg-warning text-dark fs-6 mt-1 px-2 py-1">
                                    <i class="fa-solid fa-shield-halved me-1"></i> HOUSE TRAP
                                </span>
                            @else
                                <span class="badge bg-success fs-6 mt-1 px-2 py-1">
                                    <i class="fa-solid fa-shuffle me-1"></i> FAIR
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small fw-bold text-uppercase">Target Winner</div>
                            @php
                                $target = $round->manual_winner ?: $setting->teen_patti_manual_winner;
                            @endphp
                            @if($target)
                                <div class="fs-5 fw-bold font-monospace text-uppercase {{ $target === 'player_a' ? 'text-primary' : ($target === 'player_b' ? 'text-danger' : 'text-success') }} mt-1">
                                    {{ strtoupper(str_replace('_', ' ', $target)) }}
                                </div>
                            @else
                                <div class="fs-6 fw-bold text-muted mt-2">Natural Deal</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 1-Click Winner Override Buttons -->
                <div class="p-3 rounded-3 border bg-light-subtle mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-crosshairs text-danger me-2"></i> 1-Click Winner Result Override
                        </h6>
                        <small class="text-muted">Force outcome before round locks</small>
                    </div>

                    <div class="row g-2 mt-1">
                        <!-- Force Player A -->
                        <div class="col-md-4">
                            <form action="{{ route('admin.teen-patti.set-winner') }}" method="POST">
                                @csrf
                                <input type="hidden" name="round_id" value="{{ $round->round_id }}">
                                <input type="hidden" name="winner" value="player_a">
                                <button type="submit" class="btn btn-outline-primary w-100 py-2 fw-bold {{ ($round->manual_winner === 'player_a') ? 'active bg-primary text-white shadow-sm' : '' }}">
                                    <i class="fa-solid fa-user me-1"></i> FORCE PLAYER A
                                </button>
                            </form>
                        </div>

                        <!-- Force Tie -->
                        <div class="col-md-4">
                            <form action="{{ route('admin.teen-patti.set-winner') }}" method="POST">
                                @csrf
                                <input type="hidden" name="round_id" value="{{ $round->round_id }}">
                                <input type="hidden" name="winner" value="tie">
                                <button type="submit" class="btn btn-outline-success w-100 py-2 fw-bold {{ ($round->manual_winner === 'tie') ? 'active bg-success text-white shadow-sm' : '' }}">
                                    <i class="fa-solid fa-handshake me-1"></i> FORCE TIE
                                </button>
                            </form>
                        </div>

                        <!-- Force Player B -->
                        <div class="col-md-4">
                            <form action="{{ route('admin.teen-patti.set-winner') }}" method="POST">
                                @csrf
                                <input type="hidden" name="round_id" value="{{ $round->round_id }}">
                                <input type="hidden" name="winner" value="player_b">
                                <button type="submit" class="btn btn-outline-danger w-100 py-2 fw-bold {{ ($round->manual_winner === 'player_b') ? 'active bg-danger text-white shadow-sm' : '' }}">
                                    <i class="fa-solid fa-user me-1"></i> FORCE PLAYER B
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($round->is_manual || !empty($setting->teen_patti_manual_winner))
                        <div class="mt-3 text-center">
                            <form action="{{ route('admin.teen-patti.clear-manual') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="round_id" value="{{ $round->round_id }}">
                                <button type="submit" class="btn btn-sm btn-secondary rounded-pill px-4">
                                    <i class="fa-solid fa-rotate-left me-1"></i> Clear Override & Return to Natural Deal
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Risk Mode Selector -->
                <div class="p-3 rounded-3 border bg-light-subtle">
                    <h6 class="fw-bold mb-2 text-dark">
                        <i class="fa-solid fa-shield-halved text-primary me-2"></i> Automatic House Risk Protection Mode
                    </h6>
                    <form action="{{ route('admin.teen-patti.mode') }}" method="POST">
                        @csrf
                        <div class="row g-2 align-items-center">
                            <div class="col-sm-8">
                                <select name="mode" class="form-select">
                                    <option value="fair" {{ ($setting->teen_patti_control_mode === 'fair') ? 'selected' : '' }}>
                                        Provably Fair (100% Random Standard 52-Card Deck)
                                    </option>
                                    <option value="house_trap" {{ ($setting->teen_patti_control_mode === 'house_trap') ? 'selected' : '' }}>
                                        House Protection Trap (Auto-selects outcome that owes the least money)
                                    </option>
                                    <option value="manual_override" {{ ($setting->teen_patti_control_mode === 'manual_override') ? 'selected' : '' }}>
                                        Manual Override Mode (Follows admin force winner)
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <button type="submit" class="btn btn-dark w-100 fw-bold">
                                    <i class="fa-solid fa-check me-1"></i> Apply Mode
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Real-time Financial Liability & Exposure Matrix -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="fa-solid fa-chart-pie text-info me-2"></i> Real-time House Liability Exposure
                </h5>
                <p class="text-muted small mb-0">Total wagered vs projected house profit/loss for Round #{{ $round->round_id }}.</p>
            </div>

            <div class="card-body p-4">
                <!-- Total Pool Card -->
                <div class="p-3 bg-dark text-white rounded-3 mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small fw-bold text-uppercase">Total Pool (Active Round)</div>
                        <div class="fs-3 fw-bold font-monospace text-warning">₹{{ number_format($exposure['total_wagered'], 2) }}</div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-secondary font-monospace">{{ $exposure['total_bets_count'] }} bets</span>
                    </div>
                </div>

                <!-- Breakdown of Bets Placed -->
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered text-center align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Player A</th>
                                <th>Player B</th>
                                <th>Tie</th>
                                <th>Pair+ A</th>
                                <th>Pair+ B</th>
                            </tr>
                        </thead>
                        <tbody class="font-monospace fw-bold">
                            <tr>
                                <td class="text-primary">₹{{ number_format($exposure['bet_a'], 0) }}</td>
                                <td class="text-danger">₹{{ number_format($exposure['bet_b'], 0) }}</td>
                                <td class="text-success">₹{{ number_format($exposure['bet_tie'], 0) }}</td>
                                <td class="text-warning">₹{{ number_format($exposure['bet_ppa'], 0) }}</td>
                                <td class="text-warning">₹{{ number_format($exposure['bet_ppb'], 0) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Outcome Simulations -->
                <div class="border rounded-3 p-3 bg-light mb-3">
                    <div class="fw-bold text-dark small text-uppercase mb-2">Outcome Profit / Loss Projections:</div>

                    <!-- If Player A Wins -->
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <span class="badge bg-primary me-2">IF PLAYER A WINS</span>
                            <span class="text-muted small">Payout: ₹{{ number_format($exposure['payout_if_a'], 2) }}</span>
                        </div>
                        <div class="font-monospace fw-bold {{ $exposure['net_if_a'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $exposure['net_if_a'] >= 0 ? '+' : '' }}₹{{ number_format($exposure['net_if_a'], 2) }}
                        </div>
                    </div>

                    <!-- If Player B Wins -->
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <span class="badge bg-danger me-2">IF PLAYER B WINS</span>
                            <span class="text-muted small">Payout: ₹{{ number_format($exposure['payout_if_b'], 2) }}</span>
                        </div>
                        <div class="font-monospace fw-bold {{ $exposure['net_if_b'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $exposure['net_if_b'] >= 0 ? '+' : '' }}₹{{ number_format($exposure['net_if_b'], 2) }}
                        </div>
                    </div>

                    <!-- If Tie Wins -->
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <div>
                            <span class="badge bg-success me-2">IF TIE WINS</span>
                            <span class="text-muted small">Payout: ₹{{ number_format($exposure['payout_if_tie'], 2) }}</span>
                        </div>
                        <div class="font-monospace fw-bold {{ $exposure['net_if_tie'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $exposure['net_if_tie'] >= 0 ? '+' : '' }}₹{{ number_format($exposure['net_if_tie'], 2) }}
                        </div>
                    </div>
                </div>

                <!-- Safest Recommendation -->
                <div class="alert alert-info py-2 px-3 mb-0 rounded-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-lightbulb text-warning fs-5"></i>
                    <div class="small">
                        <strong>Recommended House Advantage:</strong> Outcome with minimum payout is <span class="badge bg-dark text-uppercase font-monospace">{{ strtoupper(str_replace('_', ' ', $exposure['safest_winner'])) }}</span>.
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- Multipliers & Parameters Form -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
        <h5 class="fw-bold mb-1 text-dark">
            <i class="fa-solid fa-sliders text-warning me-2"></i> Teen Patti Betting Multipliers & Timing Configuration
        </h5>
        <p class="text-muted small mb-0">Set game stakes, odds for Player A/B and Tie, and round interval duration.</p>
    </div>

    <div class="card-body p-4">
        <form action="{{ route('admin.teen-patti.settings') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold text-muted">Min Bet (₹)</label>
                    <input type="number" step="1" name="teen_patti_min_bet" class="form-control" value="{{ $setting->teen_patti_min_bet ?? 5 }}" required>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold text-muted">Max Bet (₹)</label>
                    <input type="number" step="1" name="teen_patti_max_bet" class="form-control" value="{{ $setting->teen_patti_max_bet ?? 25000 }}" required>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold text-muted">Player A/B Odds (X)</label>
                    <input type="number" step="0.01" name="teen_patti_odds_player" class="form-control" value="{{ $setting->teen_patti_odds_player ?? 1.95 }}" required>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold text-muted">Tie Odds (X)</label>
                    <input type="number" step="0.01" name="teen_patti_odds_tie" class="form-control" value="{{ $setting->teen_patti_odds_tie ?? 9.00 }}" required>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold text-muted">Duration (Seconds)</label>
                    <input type="number" step="1" min="15" max="120" name="teen_patti_round_duration" class="form-control" value="{{ $setting->teen_patti_round_duration ?? 25 }}" required>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold text-muted">Control Mode</label>
                    <select name="teen_patti_control_mode" class="form-select">
                        <option value="fair" {{ ($setting->teen_patti_control_mode ?? 'fair') === 'fair' ? 'selected' : '' }}>Fair</option>
                        <option value="house_trap" {{ ($setting->teen_patti_control_mode ?? '') === 'house_trap' ? 'selected' : '' }}>House Trap</option>
                        <option value="manual_override" {{ ($setting->teen_patti_control_mode ?? '') === 'manual_override' ? 'selected' : '' }}>Manual Override</option>
                    </select>
                </div>
                <div class="col-12 text-end mt-3">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Configuration
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Active Bets Table on Current Round -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-list-check text-primary me-2"></i> Live Active Bets (Round #{{ $round->round_id }})
            </h5>
            <p class="text-muted small mb-0">Real-time player wagers placed on the current countdown window.</p>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 font-monospace">{{ $activeBets->count() }} active bets</span>
    </div>

    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Player</th>
                        <th>Bet Selection</th>
                        <th>Amount</th>
                        <th>Odds</th>
                        <th>Potential Win</th>
                        <th>Status</th>
                        <th class="text-end">Placed At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeBets as $b)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $b->user->name ?? 'User #'.$b->user_id }}</div>
                                <div class="text-muted small font-monospace">{{ $b->user->phone ?? '-' }}</div>
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
                                {!! $map[$b->bet_type] ?? $b->bet_type !!}
                            </td>
                            <td class="font-monospace fw-bold">₹{{ number_format($b->amount, 2) }}</td>
                            <td class="font-monospace text-warning fw-bold">{{ number_format($b->odds, 2) }}X</td>
                            <td class="font-monospace text-success fw-bold">₹{{ number_format($b->amount * $b->odds, 2) }}</td>
                            <td><span class="badge bg-warning text-dark rounded-pill px-2 py-1">Pending</span></td>
                            <td class="text-end text-muted small font-monospace">{{ $b->created_at->format('H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                No bets placed yet for Round #{{ $round->round_id }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Completed Rounds -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-clock-rotate-left text-success me-2"></i> Recent Completed Rounds History
            </h5>
            <p class="text-muted small mb-0">Audit log of dealt hands, cards, and declared winners.</p>
        </div>
        <span class="badge bg-secondary rounded-pill">Last {{ $recentRounds->count() }} Rounds</span>
    </div>

    <div class="card-body p-4">
        <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small sticky-top">
                    <tr>
                        <th>Round ID</th>
                        <th>Winner</th>
                        <th>Player A Cards</th>
                        <th>Player B Cards</th>
                        <th>Winning Hand Rank</th>
                        <th class="text-end">Settled At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRounds as $r)
                        <tr>
                            <td class="font-monospace fw-bold text-dark">#{{ $r->round_id }}</td>
                            <td>
                                @if($r->winner === 'player_a')
                                    <span class="badge bg-primary px-2 py-1">PLAYER A</span>
                                @elseif($r->winner === 'player_b')
                                    <span class="badge bg-danger px-2 py-1">PLAYER B</span>
                                @else
                                    <span class="badge bg-success px-2 py-1">TIE</span>
                                @endif
                            </td>
                            <td>
                                @if(is_array($r->player_a_cards))
                                    @foreach($r->player_a_cards as $c)
                                        @php
                                            $isRed = in_array($c['symbol'] ?? ($c['suit'] ?? ''), ['♥', '♦']) || ($c['color'] ?? '') === 'red';
                                        @endphp
                                        <span class="badge bg-dark border {{ $isRed ? 'text-danger' : 'text-light' }} font-monospace me-1">
                                            {{ $c['rank'] ?? '' }}{{ $c['symbol'] ?? ($c['suit'] ?? '') }}
                                        </span>
                                    @endforeach
                                @endif
                                <small class="text-muted ms-1">({{ $r->player_a_rank_title }})</small>
                            </td>
                            <td>
                                @if(is_array($r->player_b_cards))
                                    @foreach($r->player_b_cards as $c)
                                        @php
                                            $isRed = in_array($c['symbol'] ?? ($c['suit'] ?? ''), ['♥', '♦']) || ($c['color'] ?? '') === 'red';
                                        @endphp
                                        <span class="badge bg-dark border {{ $isRed ? 'text-danger' : 'text-light' }} font-monospace me-1">
                                            {{ $c['rank'] ?? '' }}{{ $c['symbol'] ?? ($c['suit'] ?? '') }}
                                        </span>
                                    @endforeach
                                @endif
                                <small class="text-muted ms-1">({{ $r->player_b_rank_title }})</small>
                            </td>
                            <td class="fw-bold text-dark">{{ $r->winning_rank_title ?: ($r->player_a_rank_title.' vs '.$r->player_b_rank_title) }}</td>
                            <td class="text-end text-muted small font-monospace">{{ $r->settled_at ? $r->settled_at->format('d M H:i:s') : $r->created_at->format('H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No completed rounds recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    (function() {
        let currentRoundId = "{{ $round->round_id }}";
        let adminRemaining = {{ (int) $roundData['remaining_seconds'] }};
        const el = document.getElementById('adminTimerDisplay');
        let isSyncing = false;

        const intervalId = setInterval(() => {
            if (adminRemaining > 0) {
                adminRemaining--;
                if (el) el.innerText = `${adminRemaining}s`;
            } else {
                if (el) el.innerHTML = '<span class="text-danger fs-3"><i class="fa-solid fa-spinner fa-spin me-1"></i> Dealing</span>';
                syncWithServer();
            }
        }, 1000);

        async function syncWithServer() {
            if (isSyncing) return;
            isSyncing = true;
            try {
                const res = await fetch("{{ route('api.teen-patti.status') }}");
                if (!res.ok) return;
                const data = await res.json();
                if (data && data.success) {
                    if (data.round_id && data.round_id !== currentRoundId) {
                        clearInterval(intervalId);
                        window.location.reload();
                        return;
                    }
                    if (typeof data.remaining_seconds === 'number') {
                        adminRemaining = Math.max(0, parseInt(data.remaining_seconds));
                        if (el && adminRemaining > 0) {
                            el.innerText = `${adminRemaining}s`;
                        }
                    }
                }
            } catch (err) {
                console.error("Teen Patti admin sync error:", err);
            } finally {
                isSyncing = false;
            }
        }

        setInterval(syncWithServer, 5000);
    })();
</script>
@endsection
