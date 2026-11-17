@extends('layouts.admin')

@section('title', 'Aviator Live Control & Crash Point Hub')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-plane-departure text-danger me-2"></i> Aviator Live Control Hub
        </h2>
        <p class="text-muted small mb-0">Live round monitoring, real-time risk exposure, manual crash multiplier manipulation & bet limits.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.aviator.winners') }}" class="btn btn-outline-danger fw-bold rounded-pill px-3">
            <i class="fa-solid fa-trophy me-1"></i> Winner List
        </a>
        <a href="{{ route('aviator.index') }}" target="_blank" class="btn btn-danger text-white fw-bold rounded-pill px-3">
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

<!-- Row: Live Round Overview & Realtime Exposure -->
<div class="row g-4 mb-4">
    <!-- Active Round & Result Control -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger px-3 py-2 fs-6 fw-bold">AVIATOR LIVE</span>
                    <span class="badge bg-light text-dark border px-3 py-2 fs-6 font-monospace" id="adminActiveRoundId">
                        Round #{{ $round->round_id }}
                    </span>
                </div>
                <div id="adminActiveRoundStatus">
                    @if($roundData['status'] === 'flying')
                        <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                            <i class="fa-solid fa-plane text-danger me-1"></i> Plane in Flight ({{ number_format($roundData['current_multiplier'], 2) }}x)
                        </span>
                    @elseif($roundData['status'] === 'crashed')
                        <span class="badge bg-danger px-3 py-2 fs-6">
                            <i class="fa-solid fa-burst me-1"></i> Crashed @ {{ number_format($round->crash_multiplier, 2) }}x
                        </span>
                    @else
                        <span class="badge bg-success px-3 py-2 fs-6">
                            <i class="fa-solid fa-spinner fa-spin me-1"></i> Betting Open ({{ $roundData['remaining_betting_seconds'] }}s left)
                        </span>
                    @endif
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Standing Mode & Target Info -->
                @php
                    $isStandingLocked = ($setting->aviator_manual_multiplier !== null);
                @endphp
                <div class="p-3 mb-4 rounded-3 border {{ $isStandingLocked ? 'bg-danger-subtle border-danger' : ($round->is_manual ? 'bg-warning-subtle border-warning' : 'bg-light border-secondary-subtle') }}">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">
                                {{ $isStandingLocked ? 'Standing Crash Lock Active:' : 'Target Crash Multiplier:' }}
                            </span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-2 fw-bold font-monospace {{ $isStandingLocked ? 'text-danger' : 'text-dark' }}" id="adminActiveTargetMultiplier">
                                    {{ number_format($setting->aviator_manual_multiplier ?? $round->crash_multiplier, 2) }}x
                                </span>
                                @if($isStandingLocked)
                                    <span class="badge bg-danger px-2 py-1">🔥 STANDING LOCK ACTIVE</span>
                                @elseif($round->is_manual)
                                    <span class="badge bg-warning text-dark px-2 py-1">SINGLE ROUND OVERRIDE</span>
                                @else
                                    <span class="badge bg-success px-2 py-1">AUTO RANDOM MODE</span>
                                @endif
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">
                                @if($isStandingLocked)
                                    Ye point active round aur sabhi upcoming rounds par tab tak lock rahega jab tak aap "Reset to Auto Mode" nahi dabate.
                                @else
                                    Game probably fair random distribution ke hisab se automatically fly aur crash ho raha hai.
                                @endif
                            </small>
                        </div>

                        @if($isStandingLocked || $round->is_manual)
                            <form action="{{ route('admin.aviator.clear-manual') }}" method="POST">
                                @csrf
                                <input type="hidden" name="round_id" value="{{ $round->round_id }}">
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">
                                    <i class="fa-solid fa-rotate-left me-1"></i> Reset to Auto Mode
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Manual Crash Control Form -->
                <h6 class="fw-bold text-dark mb-1">
                    <i class="fa-solid fa-bullseye text-danger me-1"></i> Set Manual Crash Point (Fixed Multiplier)
                </h6>
                <p class="text-muted small mb-3">Multiplier set karein jahan plane fly away/crash hoga (Active round & aane wale sabhi rounds par turant lagu hoga):</p>

                <form action="{{ route('admin.aviator.set-manual') }}" method="POST">
                    @csrf
                    <input type="hidden" name="round_id" id="formRoundId" value="{{ $round->round_id }}">

                    <div class="input-group mb-3">
                        <span class="input-group-text bg-white border-end-0 fw-bold">Target Multiplier:</span>
                        <input type="number" step="0.01" min="1.00" max="500.00" name="crash_multiplier" id="crashMultiplierInput"
                               class="form-control form-control-lg font-monospace fw-bold text-center border-start-0 border-end-0"
                               placeholder="e.g. 2.50" value="{{ $setting->aviator_manual_multiplier ?? $round->manual_crash_multiplier ?? $round->crash_multiplier }}" required>
                        <span class="input-group-text bg-white border-start-0 font-monospace fw-bold">X</span>
                        <button type="submit" class="btn btn-danger fw-bold px-4">
                            <i class="fa-solid fa-lock me-1"></i> LOCK POINT
                        </button>
                    </div>

                    <!-- Quick Preset Buttons -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="text-muted small fw-bold">Quick Presets:</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="setPreset(1.05)">1.05x (Instant Crash)</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="setPreset(1.20)">1.20x</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="setPreset(1.50)">1.50x</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="setPreset(2.00)">2.00x</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="setPreset(3.00)">3.00x</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="setPreset(5.00)">5.00x</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="setPreset(10.00)">10.00x</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- House Exposure & Live Risk -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-chart-pie text-primary me-2"></i> Real-time Round Risk Exposure
                </h5>
                <p class="text-muted small mb-0">Real-time total bets, payouts, and liability.</p>
            </div>

            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Total Bets Count</span>
                            <span class="fs-4 fw-bold font-monospace text-dark">{{ $activeBets->count() }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Total Wagered</span>
                            <span class="fs-4 fw-bold font-monospace text-warning">₹{{ number_format($totalWagered, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Pending Exposure</span>
                            <span class="fs-4 fw-bold font-monospace text-primary">₹{{ number_format($pendingExposure, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Paid Out Winnings</span>
                            <span class="fs-4 fw-bold font-monospace text-success">₹{{ number_format($totalCashedOut, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Aviator Betting Limits Form -->
                <div class="p-3 bg-light rounded-3 border">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="fa-solid fa-sliders text-secondary me-1"></i> Betting Limits Settings
                    </h6>
                    <form action="{{ route('admin.aviator.update-settings') }}" method="POST">
                        @csrf
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted mb-1">Min Bet (₹)</label>
                                <input type="number" step="1" min="1" name="aviator_min_bet" class="form-control form-control-sm font-monospace" value="{{ $setting->aviator_min_bet ?? 10 }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted mb-1">Max Bet (₹)</label>
                                <input type="number" step="1" min="10" name="aviator_max_bet" class="form-control form-control-sm font-monospace" value="{{ $setting->aviator_max_bet ?? 50000 }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark btn-sm rounded-pill w-100 mt-2 fw-bold">
                            <i class="fa-solid fa-check me-1"></i> Update Limits
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Active Bets Table in Current Round -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold text-dark mb-0">
                <i class="fa-solid fa-receipt text-warning me-2"></i> Live Bets in Round #{{ $round->round_id }}
            </h5>
            <p class="text-muted small mb-0">Real-time bets placed by players for current round.</p>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 font-monospace">{{ $activeBets->count() }} Bets Active</span>
    </div>

    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Player</th>
                        <th>Phone</th>
                        <th>Slot</th>
                        <th>Bet Amount</th>
                        <th>Auto Cashout</th>
                        <th>Cashed Out At</th>
                        <th>Payout</th>
                        <th class="text-end">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeBets as $bet)
                        <tr>
                            <td class="fw-bold text-dark">{{ $bet->user->name ?? 'User #'.$bet->user_id }}</td>
                            <td class="font-monospace text-muted">{{ $bet->user->phone ?? '-' }}</td>
                            <td><span class="badge bg-secondary font-monospace">Slot {{ $bet->bet_slot }}</span></td>
                            <td class="fw-bold font-monospace text-warning">₹{{ number_format($bet->amount, 2) }}</td>
                            <td class="font-monospace">{{ $bet->auto_cashout ? number_format($bet->auto_cashout, 2).'x' : '-' }}</td>
                            <td class="font-monospace">{{ $bet->cashout_multiplier ? number_format($bet->cashout_multiplier, 2).'x' : '-' }}</td>
                            <td class="fw-bold font-monospace text-success">
                                {{ $bet->win_amount > 0 ? '+₹'.number_format($bet->win_amount, 2) : '-' }}
                            </td>
                            <td class="text-end">
                                @if($bet->status === 'cashed_out')
                                    <span class="badge bg-success">CASHED OUT</span>
                                @elseif($bet->status === 'lost')
                                    <span class="badge bg-danger">LOST</span>
                                @elseif($bet->status === 'cancelled')
                                    <span class="badge bg-secondary">CANCELLED</span>
                                @else
                                    <span class="badge bg-warning text-dark">IN FLIGHT</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No bets placed in this round yet.</td>
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
            <h5 class="fw-bold text-dark mb-0">
                <i class="fa-solid fa-clock-rotate-left text-info me-2"></i> Recent Completed Aviator Rounds
            </h5>
            <p class="text-muted small mb-0">Historical log of past crash multipliers.</p>
        </div>
        <span class="badge bg-secondary rounded-pill">Last {{ $recentRounds->count() }} Rounds</span>
    </div>

    <div class="card-body p-4">
        <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>Round ID</th>
                        <th>Crash Point</th>
                        <th>Total Bets</th>
                        <th>Wagered (₹)</th>
                        <th>Paid Out (₹)</th>
                        <th>Mode</th>
                        <th class="text-end">Ended At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRounds as $r)
                        <tr>
                            <td class="font-monospace fw-bold text-dark">{{ $r->round_id }}</td>
                            <td>
                                <span class="badge font-monospace {{ $r->crash_multiplier >= 10.0 ? 'bg-warning text-dark' : ($r->crash_multiplier >= 2.0 ? 'bg-primary' : 'bg-secondary') }}">
                                    {{ number_format($r->crash_multiplier, 2) }}x
                                </span>
                            </td>
                            <td class="font-monospace">{{ $r->total_bets_count }}</td>
                            <td class="font-monospace text-warning">₹{{ number_format($r->total_bet_amount, 2) }}</td>
                            <td class="font-monospace text-success">₹{{ number_format($r->total_payout_amount, 2) }}</td>
                            <td>
                                @if($r->is_manual)
                                    <span class="badge bg-danger">MANUAL</span>
                                @else
                                    <span class="badge bg-light text-dark border">AUTO</span>
                                @endif
                            </td>
                            <td class="text-end text-muted font-monospace small">
                                {{ $r->end_time ? $r->end_time->format('d M H:i:s') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No completed rounds yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function setPreset(multiplier) {
        document.getElementById('crashMultiplierInput').value = multiplier.toFixed(2);
    }

    // Real-time synchronization loop so the admin panel always targets the fresh round
    async function refreshAdminLiveStatus() {
        try {
            const res = await fetch('{{ route('api.aviator.status') }}');
            if (!res.ok) return;
            const data = await res.json();
            if (!data.success) return;

            // Update Round ID
            const roundBadge = document.getElementById('adminActiveRoundId');
            if (roundBadge) roundBadge.innerText = 'Round #' + data.round_id;

            const hiddenInput = document.getElementById('formRoundId');
            if (hiddenInput) hiddenInput.value = data.round_id;

            // Update Status Badge
            const statusEl = document.getElementById('adminActiveRoundStatus');
            if (statusEl) {
                if (data.status === 'flying') {
                    statusEl.innerHTML = `<span class="badge bg-warning text-dark px-3 py-2 fs-6"><i class="fa-solid fa-plane text-danger me-1"></i> Plane in Flight (${data.current_multiplier.toFixed(2)}x)</span>`;
                } else if (data.status === 'crashed') {
                    statusEl.innerHTML = `<span class="badge bg-danger px-3 py-2 fs-6"><i class="fa-solid fa-burst me-1"></i> Crashed @ ${(data.crash_multiplier || data.current_multiplier).toFixed(2)}x</span>`;
                } else {
                    statusEl.innerHTML = `<span class="badge bg-success px-3 py-2 fs-6"><i class="fa-solid fa-spinner fa-spin me-1"></i> Betting Open (${data.remaining_betting_seconds.toFixed(1)}s left)</span>`;
                }
            }

            // If in auto mode and no standing lock, show the current round's target
            if (!data.standing_manual_multiplier && data.crash_multiplier) {
                const targetEl = document.getElementById('adminActiveTargetMultiplier');
                if (targetEl) targetEl.innerText = data.crash_multiplier.toFixed(2) + 'x';
            }
        } catch (e) {
            // Silently ignore background polling glitches
        }
    }

    setInterval(refreshAdminLiveStatus, 1500);
</script>
@endsection
