@extends('layouts.admin')

@section('title', 'Mines 5x5 Live Control & Risk Hub')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-bomb text-success me-2"></i> Mines 5x5 Live Control Hub
        </h2>
        <p class="text-muted small mb-0">Live player sessions, real-time X-Ray tile inspection, house edge & profit manipulation settings.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.mines.winners') }}" class="btn btn-outline-success fw-bold rounded-pill px-3">
            <i class="fa-solid fa-trophy me-1"></i> Winner List
        </a>
        <a href="{{ route('mines.index') }}" target="_blank" class="btn btn-success text-white fw-bold rounded-pill px-3">
            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Player Game
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

<!-- Row 1: KPI Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Active Sessions</div>
                <div class="stat-value text-primary">{{ $activeCount }}</div>
                <div class="text-muted small mt-1">
                    Liability: <span class="fw-bold text-danger">₹{{ number_format($activeLiability, 2) }}</span>
                </div>
            </div>
            <div class="stat-icon bg-primary-subtle text-primary">
                <i class="fa-solid fa-gamepad"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Wagered</div>
                <div class="stat-value text-dark">₹{{ number_format($totalWagered, 2) }}</div>
                <div class="text-muted small mt-1">{{ number_format($totalGamesCount) }} total games</div>
            </div>
            <div class="stat-icon bg-info-subtle text-info">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Casino Net Profit (GGR)</div>
                <div class="stat-value {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                    ₹{{ number_format($netProfit, 2) }}
                </div>
                <div class="text-muted small mt-1">
                    Margin: <span class="fw-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ $houseMargin }}%</span>
                </div>
            </div>
            <div class="stat-icon bg-success-subtle text-success">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Jackpot Pool</div>
                <div class="stat-value text-warning">₹{{ number_format($setting->mines_jackpot_pool ?? 0, 2) }}</div>
                <div class="text-muted small mt-1">{{ $jackpotHitsCount }} lifetime winners</div>
            </div>
            <div class="stat-icon bg-warning-subtle text-warning">
                <i class="fa-solid fa-gift"></i>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Live Monitor & Manipulation Settings -->
<div class="row g-4 mb-4">
    <!-- Left: Settings & Win/Loss Manipulation Mode -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-sliders text-success me-2"></i> Game Settings & House Advantage
                </h5>
                <p class="text-muted small mb-0">Control win probability, auto-traps, RTP, bet limits, and jackpot pool.</p>
            </div>

            <div class="card-body p-4 pt-2">
                <form action="{{ route('admin.mines.settings') }}" method="POST" novalidate>
                    @csrf

                    <!-- Control Mode Selection -->
                    <div class="mb-4 p-3 rounded-3 border bg-light">
                        <label class="form-label fw-bold text-dark mb-2">
                            <i class="fa-solid fa-shield-halved text-primary me-1"></i> Result Control Mode:
                        </label>

                        <div class="d-flex flex-column gap-2">
                            <div class="form-check p-2 rounded-2 bg-white border">
                                <input class="form-check-input ms-1 me-2" type="radio" name="mines_control_mode" id="modeFair" value="fair" {{ ($setting->mines_control_mode ?? 'fair') === 'fair' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-dark" for="modeFair">
                                    🟢 100% Probably Fair (Natural RNG)
                                    <small class="d-block text-muted fw-normal" style="font-size: 0.78rem;">
                                        Tiles and bombs stay exactly where server-seed generated them. No manipulation.
                                    </small>
                                </label>
                            </div>

                            <div class="form-check p-2 rounded-2 bg-white border">
                                <input class="form-check-input ms-1 me-2" type="radio" name="mines_control_mode" id="modeHouseTrap" value="house_trap" {{ ($setting->mines_control_mode ?? '') === 'house_trap' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-danger" for="modeHouseTrap">
                                    🔴 High House Profit / Smart Trap Mode
                                    <small class="d-block text-muted fw-normal" style="font-size: 0.78rem;">
                                        Automatically converts safe clicks to a bomb if multiplier cap is reached or based on rigged percentage.
                                    </small>
                                </label>
                            </div>

                            <div class="form-check p-2 rounded-2 bg-white border">
                                <input class="form-check-input ms-1 me-2" type="radio" name="mines_control_mode" id="modeStepLimit" value="step_limit" {{ ($setting->mines_control_mode ?? '') === 'step_limit' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-warning" for="modeStepLimit">
                                    🎯 Strict Safe Steps Limit
                                    <small class="d-block text-muted fw-normal" style="font-size: 0.78rem;">
                                        Player can uncover a maximum number of safe coins; the subsequent click is guaranteed to blast.
                                    </small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Mode Specific Inputs -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">
                                Rigged Trap Rate (%)
                                <i class="fa-solid fa-circle-info text-info" title="Extra chance to force bomb during house_trap mode"></i>
                            </label>
                            <div class="input-group">
                                <input type="number" step="any" min="0" max="100" name="mines_rigged_rate" class="form-control font-monospace" value="{{ $setting->mines_rigged_rate ?? 0.00 }}">
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">0% = Disabled, 100% = Guaranteed blast</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">
                                Max Safe Steps (Step Limit Mode)
                            </label>
                            <input type="number" step="1" min="0" max="24" name="mines_force_blast_step" class="form-control font-monospace" value="{{ $setting->mines_force_blast_step ?? 0 }}">
                            <small class="text-muted" style="font-size: 0.72rem;">e.g. 3 = 4th click will always blast</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">
                                Max Multiplier Cap (Auto-Bust)
                            </label>
                            <div class="input-group">
                                <input type="number" step="any" min="0" max="1000" name="mines_max_win_multiplier" class="form-control font-monospace" value="{{ $setting->mines_max_win_multiplier ?? 0.00 }}">
                                <span class="input-group-text">x</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">0 = No limit, e.g. 10x cap</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">
                                House Edge / Casino Commission (%)
                            </label>
                            <div class="input-group">
                                <input type="number" step="any" min="0" max="50" name="mines_house_edge" class="form-control font-monospace" value="{{ $setting->mines_house_edge ?? 3.00 }}" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">Standard: 3% (97% RTP)</small>
                        </div>
                    </div>

                    <hr class="my-3 text-secondary-subtle">

                    <!-- Bet Limits & Jackpot -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Minimum Bet (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="any" min="0.1" name="mines_min_bet" class="form-control font-monospace" value="{{ $setting->mines_min_bet ?? 1.00 }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Maximum Bet (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="any" min="0.1" name="mines_max_bet" class="form-control font-monospace" value="{{ $setting->mines_max_bet ?? 25000.00 }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Jackpot Pool Balance (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="any" min="0" name="mines_jackpot_pool" class="form-control font-monospace" value="{{ $setting->mines_jackpot_pool ?? 1417.61 }}" required>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">Auto-increments 1% per eligible bet</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Jackpot Min Bet (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="any" min="1" name="mines_jackpot_min_bet" class="form-control font-monospace" value="{{ $setting->mines_jackpot_min_bet ?? 10.00 }}" required>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">Min bet needed for chest placement</small>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success fw-bold w-100 py-2 rounded-3 shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Mines Settings
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right: Real-time Active Sessions & Live X-Ray Control -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-satellite-dish text-primary me-2"></i> Live Active Sessions
                    </h5>
                    <p class="text-muted small mb-0">Currently ongoing player rounds. Inspect tiles & take action.</p>
                </div>
                <span class="badge bg-primary rounded-pill px-3 py-2">
                    {{ $activeCount }} Live
                </span>
            </div>

            <div class="card-body p-4 pt-2">
                @if($activeGames->isEmpty())
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fa-solid fa-gamepad text-muted" style="font-size: 3.5rem; opacity: 0.35;"></i>
                        </div>
                        <h6 class="fw-bold text-muted">No Active Games Right Now</h6>
                        <p class="text-muted small mb-0">When players start a round on Mines 5x5, their live cards, opened steps, and X-Ray tile maps will appear here.</p>
                    </div>
                @else
                    <div class="d-flex flex-column gap-3" style="max-height: 600px; overflow-y: auto;">
                        @foreach($activeGames as $game)
                            @php
                                $potentialPayout = round((float) $game->bet_amount * (float) $game->current_multiplier, 2) + (float) $game->jackpot_win_amount;
                                $minesPositions = $game->mine_positions ?? [];
                                $openedTiles = $game->opened_tiles ?? [];
                            @endphp
                            <div class="border rounded-3 p-3 bg-white shadow-sm position-relative">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-light text-dark border font-monospace me-1">#{{ $game->game_id }}</span>
                                        <strong class="text-dark">{{ $game->user->name ?? 'User' }}</strong>
                                        <small class="text-muted">({{ $game->user->phone ?? 'N/A' }})</small>
                                    </div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="fa-solid fa-circle fa-beat text-success me-1" style="font-size: 0.5rem;"></i> Active Round
                                    </span>
                                </div>

                                <div class="row g-2 text-center my-2">
                                    <div class="col-3">
                                        <div class="p-2 bg-light rounded-2">
                                            <small class="text-muted d-block" style="font-size: 0.7rem;">Wagered</small>
                                            <strong class="text-dark">₹{{ number_format($game->bet_amount, 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="p-2 bg-light rounded-2">
                                            <small class="text-muted d-block" style="font-size: 0.7rem;">Mines</small>
                                            <strong class="text-danger"><i class="fa-solid fa-bomb"></i> {{ $game->mines_count }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="p-2 bg-light rounded-2">
                                            <small class="text-muted d-block" style="font-size: 0.7rem;">Steps</small>
                                            <strong class="text-primary">{{ $game->current_step }} / {{ 25 - $game->mines_count }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="p-2 bg-light rounded-2">
                                            <small class="text-muted d-block" style="font-size: 0.7rem;">Multiplier</small>
                                            <strong class="text-success">{{ number_format($game->current_multiplier, 2) }}x</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                    <div class="small">
                                        <span class="text-muted">Potential Win:</span>
                                        <strong class="text-danger fs-6">₹{{ number_format($potentialPayout, 2) }}</strong>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <!-- X-Ray Reveal Modal Button -->
                                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-2 py-1 fw-bold" data-bs-toggle="modal" data-bs-target="#xrayModal{{ $game->id }}">
                                            <i class="fa-solid fa-eye me-1"></i> X-Ray
                                        </button>

                                        <!-- Force Cashout Button -->
                                        <form action="{{ route('admin.mines.force-cashout', $game->id) }}" method="POST" onsubmit="return confirm('Force cash out Game #{{ $game->game_id }} now?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2 py-1 fw-bold">
                                                <i class="fa-solid fa-hand-holding-dollar me-1"></i> Cashout
                                            </button>
                                        </form>

                                        <!-- Force Bust Button -->
                                        <form action="{{ route('admin.mines.force-bust', $game->id) }}" method="POST" onsubmit="return confirm('Force BOOM/Explode Game #{{ $game->game_id }}? Player will lose this round.');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger text-white rounded-pill px-2 py-1 fw-bold">
                                                <i class="fa-solid fa-burst me-1"></i> Bust
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- X-Ray Modal for this active game -->
                            <div class="modal fade" id="xrayModal{{ $game->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-header border-0 bg-dark text-white pb-0">
                                            <h6 class="modal-title fw-bold">
                                                <i class="fa-solid fa-eye text-info me-2"></i> X-Ray Tile Map #{{ $game->game_id }}
                                            </h6>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body bg-dark text-white p-4">
                                            <div class="d-flex justify-content-between mb-3 text-muted small">
                                                <span>Player: <strong class="text-white">{{ $game->user->name ?? 'User' }}</strong></span>
                                                <span>Mines: <strong class="text-danger">{{ $game->mines_count }}</strong></span>
                                            </div>

                                            <!-- 5x5 Board Grid -->
                                            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; max-width: 300px; margin: 0 auto;">
                                                @for($t = 0; $t < 25; $t++)
                                                    @php
                                                        $isMine = in_array($t, $minesPositions, true);
                                                        $isOpened = in_array($t, $openedTiles, true);
                                                        $isJackpot = ($game->jackpot_position !== null && $game->jackpot_position === $t);
                                                    @endphp
                                                    <div style="
                                                        aspect-ratio: 1;
                                                        border-radius: 8px;
                                                        display: flex;
                                                        align-items: center;
                                                        justify-content: center;
                                                        font-size: 1.1rem;
                                                        font-weight: 700;
                                                        border: 1px solid rgba(255,255,255,0.1);
                                                        @if($isOpened && $isMine)
                                                            background: #ef4444; color: #fff;
                                                        @elseif($isOpened)
                                                            background: #10b981; color: #fff; box-shadow: 0 0 10px rgba(16,185,129,0.5);
                                                        @elseif($isMine)
                                                            background: rgba(239, 68, 68, 0.25); color: #f87171; border: 1px dashed #ef4444;
                                                        @elseif($isJackpot)
                                                            background: rgba(245, 158, 11, 0.25); color: #fbbf24; border: 1px dashed #f59e0b;
                                                        @else
                                                            background: #1e293b; color: #64748b;
                                                        @endif
                                                    ">
                                                        @if($isOpened && $isMine)
                                                            💥
                                                        @elseif($isOpened)
                                                            🪙
                                                        @elseif($isMine)
                                                            💣
                                                        @elseif($isJackpot)
                                                            🎁
                                                        @else
                                                            <small style="font-size: 0.65rem; opacity: 0.5;">{{ $t }}</small>
                                                        @endif
                                                    </div>
                                                @endfor
                                            </div>

                                            <div class="d-flex justify-content-center gap-3 mt-3 pt-2 text-muted small" style="font-size: 0.75rem;">
                                                <span>🪙 Opened Coin</span>
                                                <span class="text-danger">💣 Hidden Mine</span>
                                                <span class="text-warning">🎁 Jackpot</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Row 3: All Games Log & History -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> Mines Games History & Bet Log
                </h5>
                <p class="text-muted small mb-0">Audit completed, cashed-out and exploded player sessions.</p>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('admin.mines.index') }}" class="d-flex gap-2 flex-wrap">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search Game ID / User" value="{{ request('search') }}" style="max-width: 200px;">
                
                <select name="status" class="form-select form-select-sm" style="max-width: 140px;">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="cashed_out" {{ request('status') === 'cashed_out' ? 'selected' : '' }}>Cashed Out</option>
                    <option value="exploded" {{ request('status') === 'exploded' ? 'selected' : '' }}>Exploded (Boom)</option>
                </select>

                <div class="form-check form-switch d-flex align-items-center gap-1 my-auto">
                    <input class="form-check-input" type="checkbox" name="jackpot" value="1" id="jackpotFilter" {{ request('jackpot') == '1' ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="form-check-label small fw-bold text-warning" for="jackpotFilter">Jackpot Only</label>
                </div>

                <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'jackpot']))
                    <a href="{{ route('admin.mines.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card-body p-4 pt-2">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Game ID / Date</th>
                        <th>Player</th>
                        <th>Bet Amount</th>
                        <th>Mines</th>
                        <th>Steps Cleared</th>
                        <th>Multiplier</th>
                        <th>Win Payout</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Probably Fair</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentGames as $g)
                        <tr>
                            <td class="ps-3">
                                <span class="font-monospace fw-bold text-dark">#{{ $g->game_id }}</span>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $g->created_at->format('d M Y, h:i A') }}</div>
                            </td>
                            <td>
                                <strong class="text-dark">{{ $g->user->name ?? 'User' }}</strong>
                                <div class="text-muted small" style="font-size: 0.72rem;">{{ $g->user->phone ?? 'N/A' }}</div>
                            </td>
                            <td class="fw-bold font-monospace">₹{{ number_format($g->bet_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    <i class="fa-solid fa-bomb me-1"></i> {{ $g->mines_count }}
                                </span>
                            </td>
                            <td class="fw-bold font-monospace text-primary">
                                {{ $g->current_step }} / {{ 25 - $g->mines_count }}
                            </td>
                            <td class="fw-bold font-monospace text-success">
                                {{ number_format($g->current_multiplier, 2) }}x
                            </td>
                            <td>
                                @if($g->status === 'cashed_out')
                                    <span class="fw-bold font-monospace text-success">₹{{ number_format($g->win_amount, 2) }}</span>
                                    @if($g->is_jackpot_win)
                                        <span class="badge bg-warning text-dark ms-1">🎁 Jackpot</span>
                                    @endif
                                @else
                                    <span class="text-muted font-monospace">₹0.00</span>
                                @endif
                            </td>
                            <td>
                                @if($g->status === 'cashed_out')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="fa-solid fa-check me-1"></i> Cashed Out
                                    </span>
                                @elseif($g->status === 'exploded')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="fa-solid fa-burst me-1"></i> Exploded
                                    </span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                        <i class="fa-solid fa-spinner fa-spin me-1"></i> Active
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <span class="font-monospace text-muted small" style="font-size: 0.68rem;" title="Hash: {{ $g->seed_hash }}">
                                    {{ substr($g->seed_hash ?? 'N/A', 0, 10) }}...
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                No games found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recentGames->hasPages())
            <div class="mt-3">
                {{ $recentGames->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
