@extends('layouts.admin')

@section('title', 'Dragon Tiger Live Control & Risk Hub')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-dragon text-danger me-2"></i> Dragon Tiger Live Control Hub
        </h2>
        <p class="text-muted small mb-0">Real-time synchronized round monitor, house risk exposure calculation, 1-click winner override & game settings.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.dragon-tiger.winners') }}" class="btn btn-outline-success fw-bold rounded-pill px-3">
            <i class="fa-solid fa-trophy me-1"></i> Winner List
        </a>
        <a href="{{ route('dragon-tiger.index') }}" target="_blank" class="btn btn-warning text-dark fw-bold rounded-pill px-3">
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
                    <span class="badge bg-danger text-white px-3 py-2 fs-6 fw-bold">DRAGON TIGER</span>
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
                                {{ $activeBets->count() }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small fw-bold text-uppercase">Control Mode</div>
                            @if($round->is_manual || $setting->dragon_tiger_control_mode === 'manual_override')
                                <span class="badge bg-danger fs-6 mt-1 px-2 py-1">
                                    <i class="fa-solid fa-crosshairs me-1"></i> MANUAL
                                </span>
                            @elseif($setting->dragon_tiger_control_mode === 'least_liability')
                                <span class="badge bg-warning text-dark fs-6 mt-1 px-2 py-1">
                                    <i class="fa-solid fa-shield-halved me-1"></i> LEAST LIABILITY
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
                                $target = $round->manual_winner ?: $setting->dragon_tiger_manual_winner;
                            @endphp
                            @if($target)
                                <div class="fs-5 fw-bold font-monospace text-uppercase {{ $target === 'dragon' ? 'text-danger' : ($target === 'tiger' ? 'text-primary' : 'text-success') }} mt-1">
                                    {{ strtoupper(str_replace('_', ' ', $target)) }}
                                </div>
                            @else
                                <span class="badge bg-secondary fs-6 mt-1 px-2 py-1">AUTOMATIC</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 1-Click Winner Override Form -->
                <div class="p-3 border rounded-3 bg-light mb-3">
                    <h6 class="fw-bold mb-3 d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-hand-pointer text-primary me-2"></i> 1-Click Target Outcome Override (Round #{{ $round->round_id }})</span>
                        @if($target)
                            <form action="{{ route('admin.dragon-tiger.clear-manual') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="round_id" value="{{ $round->round_id }}">
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2 fw-bold">
                                    <i class="fa-solid fa-xmark me-1"></i> Clear Override
                                </button>
                            </form>
                        @endif
                    </h6>

                    <form action="{{ route('admin.dragon-tiger.set-winner') }}" method="POST">
                        @csrf
                        <input type="hidden" name="round_id" value="{{ $round->round_id }}">
                        <div class="row g-2">
                            <div class="col-md-3 col-6">
                                <button type="submit" name="winner" value="dragon" class="btn w-100 py-3 fw-bold text-white rounded-3 shadow-sm {{ $target === 'dragon' ? 'ring-2' : '' }}" style="background: linear-gradient(135deg, #dc2626, #991b1b);">
                                    <i class="fa-solid fa-dragon mb-1 d-block fs-4"></i>
                                    FORCE DRAGON
                                </button>
                            </div>
                            <div class="col-md-3 col-6">
                                <button type="submit" name="winner" value="tiger" class="btn w-100 py-3 fw-bold text-white rounded-3 shadow-sm {{ $target === 'tiger' ? 'ring-2' : '' }}" style="background: linear-gradient(135deg, #2563eb, #1e40af);">
                                    <i class="fa-solid fa-paw mb-1 d-block fs-4"></i>
                                    FORCE TIGER
                                </button>
                            </div>
                            <div class="col-md-3 col-6">
                                <button type="submit" name="winner" value="tie" class="btn w-100 py-3 fw-bold text-white rounded-3 shadow-sm {{ $target === 'tie' ? 'ring-2' : '' }}" style="background: linear-gradient(135deg, #10b981, #047857);">
                                    <i class="fa-solid fa-handshake mb-1 d-block fs-4"></i>
                                    FORCE TIE
                                </button>
                            </div>
                            <div class="col-md-3 col-6">
                                <button type="submit" name="winner" value="suited_tie" class="btn w-100 py-3 fw-bold text-dark rounded-3 shadow-sm {{ $target === 'suited_tie' ? 'ring-2' : '' }}" style="background: linear-gradient(135deg, #fbbf24, #d97706);">
                                    <i class="fa-solid fa-crown mb-1 d-block fs-4"></i>
                                    FORCE SUITED TIE
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Algorithm Control Mode Form -->
                <div class="p-3 border rounded-3 bg-white">
                    <h6 class="fw-bold mb-2">
                        <i class="fa-solid fa-sliders text-primary me-2"></i> Algorithm Mode Switch
                    </h6>
                    <form action="{{ route('admin.dragon-tiger.mode') }}" method="POST">
                        @csrf
                        <div class="row g-2 align-items-center">
                            <div class="col-sm-8">
                                <select name="mode" class="form-select">
                                    <option value="fair" {{ ($setting->dragon_tiger_control_mode ?? 'fair') === 'fair' ? 'selected' : '' }}>
                                        100% Fair / RNG Deal (Natural casino probability)
                                    </option>
                                    <option value="least_liability" {{ ($setting->dragon_tiger_control_mode ?? 'fair') === 'least_liability' ? 'selected' : '' }}>
                                        Smart House Protection (Auto-picks outcome with lowest payout)
                                    </option>
                                    <option value="manual_override" {{ ($setting->dragon_tiger_control_mode ?? 'fair') === 'manual_override' ? 'selected' : '' }}>
                                        Manual Outcome Override (Enforce Admin Target)
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <button type="submit" class="btn btn-primary w-100 fw-bold">
                                    Apply Mode
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Financial Liability & Exposure Analysis -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-chart-pie text-success me-2"></i> Financial Exposure & House Payouts
                </h5>
                <p class="text-muted small mb-0">Total wagers placed on current active round and projected bookmaker profit.</p>
            </div>
            <div class="card-body p-4">
                <div class="p-3 rounded-3 mb-3" style="background: #0f172a; color: #fff;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-white-50 small text-uppercase fw-bold">Total Wager Volume</span>
                        <span class="fs-4 fw-bold font-monospace text-warning">₹{{ number_format($exposure['total_volume'], 2) }}</span>
                    </div>
                </div>

                <!-- Dragon vs Tiger vs Tie Breakup -->
                <div class="list-group list-group-flush mb-3">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                        <div>
                            <span class="badge bg-danger me-2">DRAGON</span>
                            <span class="small text-muted">Bets Volume: ₹{{ number_format($exposure['dragon_volume'], 2) }}</span>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Payout Liability: ₹{{ number_format($exposure['dragon_payout_liability'], 2) }}</div>
                            <span class="badge {{ $exposure['dragon_profit'] >= 0 ? 'bg-success' : 'bg-danger' }}">
                                Profit: {{ $exposure['dragon_profit'] >= 0 ? '+' : '' }}₹{{ number_format($exposure['dragon_profit'], 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                        <div>
                            <span class="badge bg-primary me-2">TIGER</span>
                            <span class="small text-muted">Bets Volume: ₹{{ number_format($exposure['tiger_volume'], 2) }}</span>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Payout Liability: ₹{{ number_format($exposure['tiger_payout_liability'], 2) }}</div>
                            <span class="badge {{ $exposure['tiger_profit'] >= 0 ? 'bg-success' : 'bg-danger' }}">
                                Profit: {{ $exposure['tiger_profit'] >= 0 ? '+' : '' }}₹{{ number_format($exposure['tiger_profit'], 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                        <div>
                            <span class="badge bg-success me-2">TIE & SUITED TIE</span>
                            <span class="small text-muted">Tie: ₹{{ number_format($exposure['tie_volume'], 2) }} | Suited: ₹{{ number_format($exposure['suited_tie_volume'], 2) }}</span>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Payout Liability: ₹{{ number_format($exposure['tie_payout_liability'], 2) }}</div>
                            <span class="badge {{ $exposure['tie_profit'] >= 0 ? 'bg-success' : 'bg-danger' }}">
                                Profit: {{ $exposure['tie_profit'] >= 0 ? '+' : '' }}₹{{ number_format($exposure['tie_profit'], 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <div>
                            <span class="badge bg-dark me-2">SIDE BETS</span>
                            <span class="small text-muted">Red/Black, Big/Small, Odd/Even</span>
                        </div>
                        <div class="text-end font-monospace text-dark fw-bold">
                            ₹{{ number_format($exposure['side_volume'], 2) }}
                        </div>
                    </div>
                </div>

                <!-- House Least Liability Recommendation -->
                <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="fw-bold text-dark small text-uppercase">Recommended Winner for Maximum Profit:</div>
                            <div class="text-muted small">Auto-calculated lowest payout liability</div>
                        </div>
                        <span class="badge bg-success fs-6 text-uppercase px-3 py-2 font-monospace">
                            {{ $exposure['recommended_winner'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row: Game Betting Rules & Odds Settings Form -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-gear text-primary me-2"></i> Dragon Tiger Betting Limits & Odds Settings
        </h5>
        <p class="text-muted small mb-0">Customize round timer, betting caps, and payout multiplier ratios.</p>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.dragon-tiger.settings') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold">Min Bet (₹)</label>
                    <input type="number" step="1" name="dragon_tiger_min_bet" class="form-control" value="{{ $setting->dragon_tiger_min_bet ?? 5.00 }}" required>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold">Max Bet (₹)</label>
                    <input type="number" step="1" name="dragon_tiger_max_bet" class="form-control" value="{{ $setting->dragon_tiger_max_bet ?? 25000.00 }}" required>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold">Main Bet Odds (D/T)</label>
                    <input type="number" step="0.01" name="dragon_tiger_odds_main" class="form-control" value="{{ $setting->dragon_tiger_odds_main ?? 1.95 }}" required>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold">Tie Odds (9.0x)</label>
                    <input type="number" step="0.1" name="dragon_tiger_odds_tie" class="form-control" value="{{ $setting->dragon_tiger_odds_tie ?? 9.00 }}" required>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold">Suited Tie (50.0x)</label>
                    <input type="number" step="1" name="dragon_tiger_odds_suited_tie" class="form-control" value="{{ $setting->dragon_tiger_odds_suited_tie ?? 50.00 }}" required>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small fw-bold">Round Duration (sec)</label>
                    <input type="number" step="1" name="dragon_tiger_round_duration" class="form-control" value="{{ $setting->dragon_tiger_round_duration ?? 25 }}" required>
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary fw-bold px-4 rounded-pill">
                    <i class="fa-solid fa-save me-1"></i> Save Game Settings
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Row: Active Bets Feed on Current Round -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-list-check text-info me-2"></i> Bets Placed on Current Round (#{{ $round->round_id }})
            </h5>
            <p class="text-muted small mb-0">Real-time player stakes currently active on the table.</p>
        </div>
        <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">
            {{ $activeBets->count() }} Bets Placed
        </span>
    </div>
    <div class="card-body p-4">
        @if($activeBets->isEmpty())
            <div class="text-center py-4 text-muted">
                <i class="fa-solid fa-coins fs-1 mb-2 opacity-50"></i>
                <p class="mb-0">No bets placed on this round yet. Waiting for player wagers...</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Bet ID</th>
                            <th>Player</th>
                            <th>Bet Type</th>
                            <th>Wager</th>
                            <th>Odds</th>
                            <th>Potential Payout</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeBets as $b)
                            <tr>
                                <td class="font-monospace fw-bold small">{{ $b->bet_id }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $b->user->name ?? 'User #'.$b->user_id }}</div>
                                    <div class="text-muted small">{{ $b->user->phone ?? '' }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ str_contains($b->bet_type, 'dragon') ? 'bg-danger' : (str_contains($b->bet_type, 'tiger') ? 'bg-primary' : 'bg-success') }} text-uppercase">
                                        {{ str_replace('_', ' ', $b->bet_type) }}
                                    </span>
                                </td>
                                <td class="fw-bold text-dark font-monospace">₹{{ number_format($b->bet_amount, 2) }}</td>
                                <td class="font-monospace text-muted">{{ $b->odds }}x</td>
                                <td class="font-monospace text-success fw-bold">₹{{ number_format($b->bet_amount * $b->odds, 2) }}</td>
                                <td>
                                    <span class="badge bg-warning text-dark text-uppercase">Pending</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Row: Recent Completed Rounds History -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-clock-rotate-left text-secondary me-2"></i> Recent Completed Rounds History
            </h5>
            <p class="text-muted small mb-0">Past rounds, settled card hands, total wagers and net house profit.</p>
        </div>
        <span class="badge bg-secondary rounded-pill">Last {{ $recentRounds->count() }} Rounds</span>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>Round ID</th>
                        <th>Winner</th>
                        <th>Dragon Card</th>
                        <th>Tiger Card</th>
                        <th>Total Wagered</th>
                        <th>Total Payout</th>
                        <th>House Margin</th>
                        <th>Settled Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRounds as $r)
                        @php
                            $houseMargin = (float)$r->total_bet_amount - (float)$r->total_payout_amount;
                        @endphp
                        <tr>
                            <td class="font-monospace fw-bold">{{ $r->round_id }}</td>
                            <td>
                                <span class="badge {{ $r->winner === 'dragon' ? 'bg-danger' : ($r->winner === 'tiger' ? 'bg-primary' : 'bg-success') }} text-uppercase px-2 py-1 fs-6">
                                    {{ strtoupper($r->winner) }}
                                </span>
                            </td>
                            <td>
                                @if(!empty($r->dragon_card))
                                    <span class="badge bg-light border text-dark font-monospace fs-6" style="color: {{ $r->dragon_card['color'] ?? '#000' }} !important;">
                                        {{ $r->dragon_card['rank'] }}{{ $r->dragon_card['symbol'] }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if(!empty($r->tiger_card))
                                    <span class="badge bg-light border text-dark font-monospace fs-6" style="color: {{ $r->tiger_card['color'] ?? '#000' }} !important;">
                                        {{ $r->tiger_card['rank'] }}{{ $r->tiger_card['symbol'] }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="font-monospace fw-bold">₹{{ number_format($r->total_bet_amount, 2) }}</td>
                            <td class="font-monospace text-danger fw-bold">₹{{ number_format($r->total_payout_amount, 2) }}</td>
                            <td>
                                <span class="badge {{ $houseMargin >= 0 ? 'bg-success' : 'bg-danger' }} font-monospace fs-6">
                                    {{ $houseMargin >= 0 ? '+' : '' }}₹{{ number_format($houseMargin, 2) }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $r->created_at?->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No completed rounds yet.</td>
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
        let remaining = {{ (int) $roundData['remaining_seconds'] }};
        const timerEl = document.getElementById('adminTimerDisplay');
        let isSyncing = false;

        // 1. Local real-time 1-second countdown
        const intervalId = setInterval(() => {
            if (remaining > 0) {
                remaining--;
                if (timerEl) {
                    timerEl.textContent = remaining + 's';
                }
            } else {
                if (timerEl) {
                    timerEl.innerHTML = '<span class="text-danger fs-3"><i class="fa-solid fa-spinner fa-spin me-1"></i> Dealing</span>';
                }
                syncWithServer();
            }
        }, 1000);

        // 2. Periodic sync with server API to avoid clock drift and auto-reload on next round
        async function syncWithServer() {
            if (isSyncing) return;
            isSyncing = true;
            try {
                const res = await fetch("{{ route('api.dragon-tiger.status') }}");
                if (!res.ok) return;
                const data = await res.json();
                if (data && data.success) {
                    // If a new round has started or round_id changed, refresh admin control room
                    if (data.round_id && data.round_id !== currentRoundId) {
                        clearInterval(intervalId);
                        window.location.reload();
                        return;
                    }
                    if (typeof data.remaining_seconds === 'number') {
                        remaining = Math.max(0, parseInt(data.remaining_seconds));
                        if (timerEl && remaining > 0) {
                            timerEl.textContent = remaining + 's';
                        }
                    }
                }
            } catch (err) {
                console.error("Dragon Tiger admin sync error:", err);
            } finally {
                isSyncing = false;
            }
        }

        // Heartbeat sync every 5 seconds to keep timer accurate
        setInterval(syncWithServer, 5000);
    })();
</script>
@endsection

