@extends('layouts.admin')

@section('title', 'European Roulette Live Control & Risk Hub')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-dharmachakra text-warning me-2"></i> European Roulette Live Control Hub
        </h2>
        <p class="text-muted small mb-0">Real-time synchronized round monitor, house liability heatmap, 1-click manual number lock & payout rules.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('roulette.index') }}" target="_blank" class="btn btn-warning text-dark fw-bold rounded-pill px-3">
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

<!-- Row: Live Round Overview & Result Control -->
<div class="row g-4 mb-4">
    
    <!-- Active Round Control Card -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark px-3 py-2 fs-6 fw-bold">EUROPEAN ROULETTE</span>
                    <span class="badge bg-light text-dark border px-3 py-2 fs-6 font-monospace">
                        Round #{{ $round->round_id }}
                    </span>
                </div>
                <div>
                    @if($roundData['is_locked'])
                        <span class="badge bg-danger px-3 py-2 fs-6">
                            <i class="fa-solid fa-arrows-spin fa-spin me-1"></i> Wheel Spinning
                        </span>
                    @else
                        <span class="badge bg-success px-3 py-2 fs-6">
                            <i class="fa-solid fa-spinner fa-spin me-1"></i> Betting Open
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
                            @if($round->is_manual || $setting->roulette_control_mode === 'manual')
                                <span class="badge bg-danger fs-6 mt-1 px-2 py-1">
                                    <i class="fa-solid fa-crosshairs me-1"></i> MANUAL
                                </span>
                            @elseif($setting->roulette_control_mode === 'admin_profit')
                                <span class="badge bg-warning text-dark fs-6 mt-1 px-2 py-1">
                                    <i class="fa-solid fa-shield-halved me-1"></i> LEAST PAYOUT
                                </span>
                            @else
                                <span class="badge bg-success fs-6 mt-1 px-2 py-1">
                                    <i class="fa-solid fa-dice me-1"></i> FAIR RNG
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 1-Click Manual Number Target Grid -->
                <div class="p-3 rounded-4 mb-3" style="background: #0b1329; border: 1px solid #1e2e5c;">
                    @php
                        $targetLockedNum = $round->manual_number ?? $setting->roulette_manual_number;
                    @endphp
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="text-warning fw-bold small text-uppercase">
                            <i class="fa-solid fa-bullseye me-1"></i> 1-Click Number Override (0 to 36):
                        </div>
                        @if($targetLockedNum !== null)
                            <form method="POST" action="{{ route('admin.roulette.clear-manual') }}">
                                @csrf
                                <input type="hidden" name="round_id" value="{{ $round->round_id }}">
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2 fw-bold">
                                    <i class="fa-solid fa-rotate-left me-1"></i> Clear Override (#{{ $targetLockedNum }})
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Number Buttons (0 to 36) -->
                    <div class="d-flex flex-wrap gap-1">
                        <!-- 0 -->
                        <form method="POST" action="{{ route('admin.roulette.set-number') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="round_id" value="{{ $round->round_id }}">
                            <input type="hidden" name="manual_number" value="0">
                            <button type="submit" class="btn btn-sm btn-success fw-bold font-monospace px-2 py-1 {{ $targetLockedNum === 0 ? 'border border-warning border-3 shadow' : '' }}" title="Force #0 Green">
                                0
                            </button>
                        </form>
                        <!-- 1 to 36 -->
                        @for($n = 1; $n <= 36; $n++)
                            @php
                                $details = \App\Services\RouletteEngine::getNumberDetails($n);
                                $isRed = ($details['color'] === 'red');
                            @endphp
                            <form method="POST" action="{{ route('admin.roulette.set-number') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="round_id" value="{{ $round->round_id }}">
                                <input type="hidden" name="manual_number" value="{{ $n }}">
                                <button type="submit" class="btn btn-sm {{ $isRed ? 'btn-danger' : 'btn-dark' }} fw-bold font-monospace px-2 py-1 {{ $targetLockedNum === $n ? 'border border-warning border-3 shadow' : '' }}" title="Force #{{ $n }} ({{ $details['color'] }})">
                                    {{ $n }}
                                </button>
                            </form>
                        @endfor
                    </div>
                </div>

                @if($targetLockedNum !== null)
                    <div class="alert alert-warning py-2 small mb-0 d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-lock me-1"></i> Forced Target: <strong>#{{ $targetLockedNum }}</strong> locked for Next Result.</span>
                        <span class="badge bg-dark text-warning">MANUAL LOCKED</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Game Settings Card -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-sliders text-primary me-2"></i> Roulette Global Parameters
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.roulette.settings') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Control Mode</label>
                        <select name="roulette_control_mode" class="form-select fw-bold">
                            <option value="fair" {{ $setting->roulette_control_mode === 'fair' ? 'selected' : '' }}>
                                🎲 100% Fair Cryptographic RNG (Natural)
                            </option>
                            <option value="admin_profit" {{ $setting->roulette_control_mode === 'admin_profit' ? 'selected' : '' }}>
                                🛡️ Admin Profit Protection (Least House Liability)
                            </option>
                            <option value="manual" {{ $setting->roulette_control_mode === 'manual' ? 'selected' : '' }}>
                                🎯 Manual Override (Always use Admin Selected Number)
                            </option>
                        </select>
                        <div class="form-text small">Admin profit mode automatically selects the pocket that owes the least money to players.</div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-danger">Red Multiplier (x)</label>
                            <input type="number" name="roulette_red_multiplier" class="form-control fw-bold border-danger border-opacity-50" value="{{ (float)($setting->roulette_red_multiplier ?? 5.00) }}" min="1" max="100" step="0.1">
                            <div class="form-text small text-muted">User requested: 5x</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Black Multiplier (x)</label>
                            <input type="number" name="roulette_black_multiplier" class="form-control fw-bold border-dark border-opacity-50" value="{{ (float)($setting->roulette_black_multiplier ?? 7.00) }}" min="1" max="100" step="0.1">
                            <div class="form-text small text-muted">User requested: 7x</div>
                        </div>
                    </div>

                    <div class="p-2 mb-3 rounded-3 bg-light border">
                        <label class="form-label small fw-bold text-primary mb-1">
                            <i class="fa-solid fa-chart-pie me-1"></i> Probability Pattern Weights (%)
                        </label>
                        <div class="row g-1">
                            <div class="col-4">
                                <label class="small text-danger fw-bold">Red %</label>
                                <input type="number" name="roulette_red_weight" class="form-control form-control-sm fw-bold text-danger" value="{{ (float)($setting->roulette_red_weight ?? 70.00) }}" min="0" max="100" step="1">
                            </div>
                            <div class="col-4">
                                <label class="small text-dark fw-bold">Black %</label>
                                <input type="number" name="roulette_black_weight" class="form-control form-control-sm fw-bold text-dark" value="{{ (float)($setting->roulette_black_weight ?? 29.00) }}" min="0" max="100" step="1">
                            </div>
                            <div class="col-4">
                                <label class="small text-success fw-bold">0 Green %</label>
                                <input type="number" name="roulette_green_weight" class="form-control form-control-sm fw-bold text-success" value="{{ (float)($setting->roulette_green_weight ?? 1.00) }}" min="0" max="100" step="0.1">
                            </div>
                        </div>
                        <div class="form-text small mt-1 text-muted" style="font-size: 0.75rem;">
                            Pattern: Maximum Red (70%), Minimum Black (29%), Ultra-rare 0 Green (1%).
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Min Bet (₹)</label>
                            <input type="number" name="roulette_min_bet" class="form-control fw-bold" value="{{ (float)$setting->roulette_min_bet }}" min="1" step="1">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Max Bet (₹)</label>
                            <input type="number" name="roulette_max_bet" class="form-control fw-bold" value="{{ (float)$setting->roulette_max_bet }}" min="10" step="10">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Round Duration (Seconds)</label>
                        <input type="number" name="roulette_round_duration" class="form-control fw-bold" value="{{ $setting->roulette_round_duration ?: 30 }}" min="15" max="120">
                        <div class="form-text small">Standard rapid casino live timer: 30 seconds.</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Game Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Row: Active Bets on This Round & Recent Completed Rounds -->
<div class="row g-4">
    <!-- Active Bets Table -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-receipt text-success me-2"></i> Active Bets on Round #{{ $round->round_id }}
                </h5>
                <span class="badge bg-light text-dark border">{{ $activeBets->count() }} Bets</span>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Player</th>
                                <th>Bet Type</th>
                                <th>Value</th>
                                <th>Stake</th>
                                <th class="text-end">Potential Payout</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeBets as $b)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $b->user?->name ?? 'User #'.$b->user_id }}</div>
                                        <div class="text-muted small">{{ $b->user?->phone }}</div>
                                    </td>
                                    <td><span class="badge bg-secondary text-uppercase">{{ $b->bet_type }}</span></td>
                                    <td class="font-monospace fw-bold text-primary">{{ $b->bet_value }}</td>
                                    <td class="font-monospace fw-bold text-success">₹{{ number_format($b->bet_amount, 2) }}</td>
                                    <td class="text-end font-monospace fw-bold text-warning">
                                        ₹{{ number_format($b->bet_amount * $b->odds, 2) }} ({{ $b->odds }}x)
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No player bets placed yet on this round.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Completed Rounds -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-history text-secondary me-2"></i> Recent Completed Rounds
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Round</th>
                                <th>Result</th>
                                <th>Total Bets</th>
                                <th>Payout</th>
                                <th class="text-end">House Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRounds as $rr)
                                @php
                                    $profit = (float)$rr->total_bet_amount - (float)$rr->total_payout_amount;
                                @endphp
                                <tr>
                                    <td class="font-monospace fw-bold">{{ substr($rr->round_id, -6) }}</td>
                                    <td>
                                        <span class="badge {{ $rr->winning_color === 'red' ? 'bg-danger' : ($rr->winning_color === 'black' ? 'bg-dark' : 'bg-success') }} px-2 py-1 fs-6 font-monospace">
                                            #{{ $rr->winning_number }} ({{ strtoupper($rr->winning_color) }})
                                        </span>
                                    </td>
                                    <td class="font-monospace">₹{{ number_format($rr->total_bet_amount, 2) }}</td>
                                    <td class="font-monospace text-danger">₹{{ number_format($rr->total_payout_amount, 2) }}</td>
                                    <td class="text-end font-monospace fw-bold {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $profit >= 0 ? '+₹'.number_format($profit, 2) : '-₹'.number_format(abs($profit), 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No completed rounds found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Live admin countdown & auto-sync
    let adminSeconds = {{ $roundData['remaining_seconds'] }};
    setInterval(() => {
        if (adminSeconds > 0) {
            adminSeconds--;
            const timerEl = document.getElementById('adminTimer');
            if (timerEl) timerEl.innerText = adminSeconds + 's';
        } else {
            // When round reaches 0, reload to sync with new round
            window.location.reload();
        }
    }, 1000);
</script>
@endsection
