@extends('layouts.admin')

@section('title', 'Horse Racing Live Control & Risk Hub')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-horse-head text-warning me-2"></i> Horse Racing Derby Live Control Hub
        </h2>
        <p class="text-muted small mb-0">Synchronized race sprint monitor, runner liability analysis, 1-click manual winner lock, live bets feed & 50-round history.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.horse-racing.winners') }}" class="btn btn-outline-primary rounded-pill px-3 fw-bold">
            <i class="fa-solid fa-trophy me-1"></i> Winners Report (Day-wise)
        </a>
        <a href="{{ route('horse-racing.index') }}" target="_blank" class="btn btn-warning text-dark fw-bold rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Player Turf
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

<!-- Row: Live Round Overview & Result Control -->
<div class="row g-4 mb-4">
    <!-- Active Round Control Card -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark px-3 py-2 fs-6 fw-bold">1000m DERBY SPRINT</span>
                    <span class="badge bg-light text-dark border px-3 py-2 fs-6 font-monospace">
                        Round #{{ $round->round_id }}
                    </span>
                </div>
                <div>
                    @if($roundData['is_locked'] || $round->status === 'racing')
                        <span class="badge bg-danger px-3 py-2 fs-6 animate-pulse">
                            <i class="fa-solid fa-flag-checkered me-1"></i> Visual Sprint Active
                        </span>
                    @else
                        <span class="badge bg-success px-3 py-2 fs-6">
                            <i class="fa-solid fa-stopwatch me-1"></i> Betting Open
                        </span>
                    @endif
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Metrics -->
                <div class="row g-3 text-center mb-4">
                    <div class="col-sm-3 col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small fw-bold text-uppercase">Time Left</div>
                            <div class="fs-2 fw-bold font-monospace text-primary" id="adminTimer">
                                {{ $roundData['remaining_seconds'] }}s
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small fw-bold text-uppercase">Total Pool</div>
                            <div class="fs-2 fw-bold font-monospace text-success">
                                ₹{{ number_format($totalPool, 2) }}
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
                            @if($round->is_manual || $setting->horse_racing_control_mode === 'manual')
                                <span class="badge bg-danger fs-6 mt-1 px-2 py-1">
                                    <i class="fa-solid fa-crosshairs me-1"></i> MANUAL
                                </span>
                            @elseif($setting->horse_racing_control_mode === 'admin_profit')
                                <span class="badge bg-warning text-dark fs-6 mt-1 px-2 py-1">
                                    <i class="fa-solid fa-shield-halved me-1"></i> LEAST PAYOUT
                                </span>
                            @else
                                <span class="badge bg-success fs-6 mt-1 px-2 py-1">
                                    <i class="fa-solid fa-dice me-1"></i> FAIR MODE
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 1-Click Forced Winner Grid -->
                <div class="p-3 rounded-4 mb-3" style="background: #0b1329; border: 1px solid #1e2e5c;">
                    @php
                        $targetLockedWinner = $round->manual_winner_id ?? $setting->horse_racing_manual_winner;
                    @endphp
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div class="text-warning fw-bold small text-uppercase d-flex align-items-center gap-1">
                            <i class="fa-solid fa-bullseye"></i>
                            <span>1-Click Forced Winner (Crosses Finish Line 1st):</span>
                        </div>
                        @if($targetLockedWinner !== null)
                            <form method="POST" action="{{ route('admin.horse-racing.clear-manual') }}">
                                @csrf
                                <input type="hidden" name="round_id" value="{{ $round->round_id }}">
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2 fw-bold">
                                    <i class="fa-solid fa-rotate-left me-1"></i> Clear Override (#{{ $targetLockedWinner }})
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- 6 Runner Quick Force Cards -->
                    <div class="row g-2">
                        @foreach($horses as $h)
                            @php
                                $hId = (int) $h['id'];
                                $isTarget = ($targetLockedWinner === $hId);
                            @endphp
                            <div class="col-sm-4 col-6">
                                <form method="POST" action="{{ route('admin.horse-racing.set-winner') }}">
                                    @csrf
                                    <input type="hidden" name="round_id" value="{{ $round->round_id }}">
                                    <input type="hidden" name="manual_winner_id" value="{{ $hId }}">
                                    <button type="submit" class="btn w-100 p-2 text-start rounded-3 position-relative {{ $isTarget ? 'btn-warning text-dark border-3 shadow' : 'btn-outline-light' }}" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.15);">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="badge px-2 py-1 text-white" style="background: {{ $h['silk_color'] ?? '#dc2626' }}">Gate {{ $h['gate'] ?? $hId }}</span>
                                            <span class="small fw-bold {{ $isTarget ? 'text-dark' : 'text-warning' }}">WIN {{ $h['win_odds'] ?? '2.5' }}x</span>
                                        </div>
                                        <div class="fw-bold mt-1 text-truncate {{ $isTarget ? 'text-dark' : 'text-white' }}">{{ $h['name'] }}</div>
                                        <div class="small {{ $isTarget ? 'text-dark text-opacity-75' : 'text-white-50' }}">Jockey: {{ $h['jockey'] }}</div>
                                        @if($isTarget)
                                            <span class="badge bg-dark text-warning position-absolute top-0 end-0 m-1 small">LOCKED</span>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($targetLockedWinner !== null)
                    <div class="alert alert-warning py-2 small mb-0 d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-lock me-1"></i> Target Locked: Runner <strong>#{{ $targetLockedWinner }}</strong> will finish 1st in Round #{{ $round->round_id }}.</span>
                        <span class="badge bg-dark text-warning">MANUAL LOCKED</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Game Settings & Risk Control Parameters Card -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-sliders text-primary me-2"></i> Horse Racing Parameters
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.horse-racing.settings') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Risk Control Mode</label>
                        <select name="horse_racing_control_mode" class="form-select fw-bold">
                            <option value="fair" {{ $setting->horse_racing_control_mode === 'fair' ? 'selected' : '' }}>
                                🎲 100% Fair Mode (Natural Physics & Odds Weighted)
                            </option>
                            <option value="admin_profit" {{ $setting->horse_racing_control_mode === 'admin_profit' ? 'selected' : '' }}>
                                🛡️ Admin Profit Mode (Lowest House Payout Wins)
                            </option>
                            <option value="manual" {{ $setting->horse_racing_control_mode === 'manual' ? 'selected' : '' }}>
                                🎯 Manual Control (Strictly Follow Forced Runner)
                            </option>
                        </select>
                        <div class="form-text small">Admin Profit mode automatically inspects live bets and guarantees minimum house liability.</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Min Bet per Wager (₹)</label>
                            <input type="number" step="0.01" name="horse_racing_min_bet" class="form-control" value="{{ old('horse_racing_min_bet', $setting->horse_racing_min_bet ?? 10.00) }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Max Bet per Wager (₹)</label>
                            <input type="number" step="0.01" name="horse_racing_max_bet" class="form-control" value="{{ old('horse_racing_max_bet', $setting->horse_racing_max_bet ?? 25000.00) }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Round Duration (Seconds)</label>
                        <input type="number" name="horse_racing_round_duration" class="form-control" value="{{ old('horse_racing_round_duration', $setting->horse_racing_round_duration ?? 60) }}" min="30" max="300" required>
                        <div class="form-text small">Default: 60s (45s betting + 15s visual sprint).</div>
                    </div>

                    <button type="submit" class="btn btn-primary fw-bold w-100 py-2 rounded-pill shadow-sm" style="background: #0077b6; border: none;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Parameters & Apply Mode
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- House Risk & Liability Exposure Table -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-chart-pie text-success me-2"></i> Realtime House Liability Breakdown
            </h5>
            <p class="text-muted small mb-0">Projected payouts & house profit/loss for all 6 runners if they finish in 1st place.</p>
        </div>
        <span class="badge bg-light text-muted border px-3 py-2 font-monospace">
            Active Pool: ₹{{ number_format($totalPool, 2) }}
        </span>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Runner</th>
                        <th>Jockey</th>
                        <th>Win Odds</th>
                        <th>Place Odds</th>
                        <th>Show Odds</th>
                        <th class="text-center">Bets Placed</th>
                        <th class="text-end">Total Wagered</th>
                        <th class="text-end">Projected Liability</th>
                        <th class="text-end">House Profit / Loss</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($runnerLiabilities as $rl)
                        @php
                            $isProfitable = ($rl['projected_profit'] >= 0);
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge px-2 py-1 text-white" style="background: {{ $rl['silk_color'] }}">Gate {{ $rl['gate'] }}</span>
                                    <span class="fw-bold">{{ $rl['name'] }}</span>
                                </div>
                            </td>
                            <td class="text-muted small">{{ $rl['jockey'] }}</td>
                            <td><span class="badge bg-warning text-dark font-monospace">{{ $rl['win_odds'] }}x</span></td>
                            <td><span class="badge bg-info text-dark font-monospace">{{ $rl['place_odds'] }}x</span></td>
                            <td><span class="badge bg-light text-dark border font-monospace">{{ $rl['show_odds'] }}x</span></td>
                            <td class="text-center fw-bold">{{ $rl['bets_count'] }}</td>
                            <td class="text-end font-monospace">₹{{ number_format($rl['bets_amount'], 2) }}</td>
                            <td class="text-end font-monospace text-danger fw-bold">₹{{ number_format($rl['liability'], 2) }}</td>
                            <td class="text-end font-monospace fw-bold {{ $isProfitable ? 'text-success' : 'text-danger' }}">
                                {{ $isProfitable ? '+' : '' }}₹{{ number_format($rl['projected_profit'], 2) }}
                            </td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('admin.horse-racing.set-winner') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="round_id" value="{{ $round->round_id }}">
                                    <input type="hidden" name="manual_winner_id" value="{{ $rl['id'] }}">
                                    <button type="submit" class="btn btn-sm btn-outline-warning text-dark fw-bold py-1 px-2" title="Force this runner as winner">
                                        Force 1st
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Active Round Bets Feed & Recent Completed Races -->
<div class="row g-4">
    <!-- Active Bets Feed -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> Current Round Wagers
                </h5>
                <span class="badge bg-primary rounded-pill px-3 py-1">{{ $activeBets->count() }} Live Bets</span>
            </div>
            <div class="card-body p-4">
                @if($activeBets->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-ticket-simple fs-1 text-muted opacity-50 mb-2 d-block"></i>
                        <h6 class="fw-bold text-dark">No Active Bets Placed</h6>
                        <p class="mb-0 small text-muted">Wagers placed by players for Round #{{ $round->round_id }} will show here in real-time.</p>
                    </div>
                @else
                    <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Player</th>
                                    <th>Runner / Bet</th>
                                    <th class="text-end">Stake</th>
                                    <th class="text-center">Odds</th>
                                    <th class="text-end">Projected Payout</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeBets as $b)
                                    <tr>
                                        <td>
                                            @if($b->user)
                                                <div class="fw-bold small">
                                                    <a href="{{ route('admin.ledger.show', $b->user->id) }}" class="text-decoration-none text-dark hover-primary">
                                                        {{ $b->user->name }}
                                                    </a>
                                                </div>
                                                <div class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $b->user->phone ?? '' }}</div>
                                            @else
                                                <span class="text-muted small">User #{{ $b->user_id }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-dark font-monospace text-uppercase" style="font-size: 0.7rem;">{{ $b->bet_type }}</span>
                                            <span class="small fw-bold ms-1">#{{ $b->horse_id }} {{ $b->horse_name }}</span>
                                        </td>
                                        <td class="text-end font-monospace small">₹{{ number_format((float)$b->bet_amount, 2) }}</td>
                                        <td class="text-center font-monospace small fw-bold text-primary">{{ $b->odds }}x</td>
                                        <td class="text-end font-monospace text-success fw-bold small">₹{{ number_format((float)$b->bet_amount * (float)$b->odds, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Races History (50 Rounds) -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-clock-rotate-left text-secondary me-2"></i> Recent Completed Rounds
                    </h5>
                    <div class="small text-muted">Recent 50 completed derby race results & profit margin</div>
                </div>
                <span class="badge bg-secondary rounded-pill px-3 py-1">{{ $recentRounds->count() }} Races</span>
            </div>
            <div class="card-body p-4">
                <!-- Search & Filter Form for History -->
                <form method="GET" action="{{ route('admin.horse-racing.index') }}" class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <input type="text" name="history_search" class="form-control form-control-sm rounded-pill" placeholder="Filter by Round ID..." value="{{ $historySearch }}">
                    </div>
                    <div class="col-sm-4">
                        <select name="history_winner" class="form-select form-select-sm rounded-pill">
                            <option value="">-- All Winners --</option>
                            <option value="1" {{ (string)$historyWinner === '1' ? 'selected' : '' }}>Runner #1 Winner</option>
                            <option value="2" {{ (string)$historyWinner === '2' ? 'selected' : '' }}>Runner #2 Winner</option>
                            <option value="3" {{ (string)$historyWinner === '3' ? 'selected' : '' }}>Runner #3 Winner</option>
                            <option value="4" {{ (string)$historyWinner === '4' ? 'selected' : '' }}>Runner #4 Winner</option>
                            <option value="5" {{ (string)$historyWinner === '5' ? 'selected' : '' }}>Runner #5 Winner</option>
                            <option value="6" {{ (string)$historyWinner === '6' ? 'selected' : '' }}>Runner #6 Winner</option>
                        </select>
                    </div>
                    <div class="col-sm-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill w-100 fw-bold">
                            <i class="fa-solid fa-filter"></i>
                        </button>
                        @if(!empty($historySearch) || !empty($historyWinner))
                            <a href="{{ route('admin.horse-racing.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </form>

                @if($recentRounds->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-trophy fs-1 text-muted opacity-50 mb-2 d-block"></i>
                        <h6 class="fw-bold text-dark">No Completed Races Found</h6>
                        <p class="mb-0 small text-muted">Completed race rounds will appear here.</p>
                    </div>
                @else
                    <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Round ID</th>
                                    <th>Podium Finishers</th>
                                    <th class="text-center">Bets</th>
                                    <th class="text-end">Pool</th>
                                    <th class="text-end">Payout</th>
                                    <th class="text-end">House Margin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentRounds as $rr)
                                    @php
                                        $margin = (float)$rr->total_bet_amount - (float)$rr->total_payout_amount;
                                        $isMarginPos = ($margin >= 0);
                                    @endphp
                                    <tr>
                                        <td class="font-monospace small fw-bold">#{{ $rr->round_id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="badge bg-warning text-dark font-monospace" title="1st Place">🥇 #{{ $rr->winner_horse_id }}</span>
                                                <span class="badge bg-secondary text-white font-monospace" title="2nd Place">🥈 #{{ $rr->second_horse_id }}</span>
                                                <span class="badge bg-dark text-white font-monospace" title="3rd Place">🥉 #{{ $rr->third_horse_id }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center font-monospace small">{{ $rr->total_bets_count }}</td>
                                        <td class="text-end font-monospace small">₹{{ number_format((float)$rr->total_bet_amount, 2) }}</td>
                                        <td class="text-end font-monospace text-danger fw-bold small">₹{{ number_format((float)$rr->total_payout_amount, 2) }}</td>
                                        <td class="text-end font-monospace fw-bold small {{ $isMarginPos ? 'text-success' : 'text-danger' }}">
                                            {{ $isMarginPos ? '+' : '' }}₹{{ number_format($margin, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        let currentRoundId = "{{ $round->round_id }}";
        let remaining = {{ (int) $roundData['remaining_seconds'] }};
        const timerEl = document.getElementById('adminTimer');
        let isSyncing = false;

        const intervalId = setInterval(() => {
            if (remaining > 0) {
                remaining--;
                if (timerEl) {
                    timerEl.textContent = remaining + 's';
                }
            } else {
                if (timerEl) {
                    timerEl.innerHTML = '<span class="text-danger fs-3"><i class="fa-solid fa-flag-checkered me-1"></i> Racing</span>';
                }
                syncWithServer();
            }
        }, 1000);

        async function syncWithServer() {
            if (isSyncing) return;
            isSyncing = true;
            try {
                const res = await fetch("{{ route('api.horse-racing.status') }}");
                if (!res.ok) return;
                const data = await res.json();
                if (data && data.success) {
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
                console.error("Horse racing admin sync error:", err);
            } finally {
                isSyncing = false;
            }
        }

        setInterval(syncWithServer, 5000);
    })();
</script>
@endsection
