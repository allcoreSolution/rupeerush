@extends('layouts.admin')

@section('title', 'Vortex Concentric Wheel Live Control & Risk Hub')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-arrows-spin text-info me-2"></i> Vortex Concentric Wheel Live Control Hub
        </h2>
        <p class="text-muted small mb-0">Live player sessions, concentric ring state inspection, house edge & profit manipulation settings.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.vortex.winners') }}" class="btn btn-outline-info fw-bold rounded-pill px-3">
            <i class="fa-solid fa-trophy me-1"></i> Winner List
        </a>
        <a href="{{ route('vortex.index') }}" target="_blank" class="btn btn-info text-white fw-bold rounded-pill px-3">
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
                <div class="stat-value text-info">{{ $activeCount }}</div>
                <div class="text-muted small mt-1">
                    Liability: <span class="fw-bold text-danger">₹{{ number_format($activeLiability, 2) }}</span>
                </div>
            </div>
            <div class="stat-icon bg-info-subtle text-info">
                <i class="fa-solid fa-arrows-spin"></i>
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
            <div class="stat-icon bg-primary-subtle text-primary">
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
                <div class="stat-label">Total Paid Out</div>
                <div class="stat-value text-warning">₹{{ number_format($totalPaidOut, 2) }}</div>
                <div class="text-muted small mt-1">{{ $cashedOutCount }} won / {{ $bustedCount }} busted</div>
            </div>
            <div class="stat-icon bg-warning-subtle text-warning">
                <i class="fa-solid fa-trophy"></i>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Settings & Mechanics Info -->
<div class="row g-4 mb-4">
    <!-- Left: Settings & Manipulation Mode -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-sliders text-info me-2"></i> Vortex Risk & House Control
                    </h5>
                    <p class="text-muted small mb-0">Configure betting limits, house edge, and outcome manipulation mode.</p>
                </div>
                <span class="badge {{ ($setting->vortex_control_mode ?? 'fair') === 'house_trap' ? 'bg-danger' : 'bg-success' }} px-3 py-2 rounded-pill">
                    {{ ($setting->vortex_control_mode ?? 'fair') === 'house_trap' ? 'TRAP ACTIVE' : 'PROBABLY FAIR' }}
                </span>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.vortex.settings') }}" novalidate>
                    @csrf

                    <!-- Control Mode -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Engine Control Mode</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="p-3 border rounded-3 w-100 cursor-pointer d-flex align-items-center gap-2 {{ ($setting->vortex_control_mode ?? 'fair') === 'fair' ? 'border-success bg-success-subtle' : 'border-light-subtle' }}">
                                    <input type="radio" name="vortex_control_mode" value="fair" {{ ($setting->vortex_control_mode ?? 'fair') === 'fair' ? 'checked' : '' }}>
                                    <div>
                                        <div class="fw-bold small text-dark">Probably Fair</div>
                                        <div class="text-muted" style="font-size: 11px;">RNG wheel physics with standard RTP</div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="p-3 border rounded-3 w-100 cursor-pointer d-flex align-items-center gap-2 {{ ($setting->vortex_control_mode ?? 'fair') === 'house_trap' ? 'border-danger bg-danger-subtle' : 'border-light-subtle' }}">
                                    <input type="radio" name="vortex_control_mode" value="house_trap" {{ ($setting->vortex_control_mode ?? 'fair') === 'house_trap' ? 'checked' : '' }}>
                                    <div>
                                        <div class="fw-bold small text-danger">House Trap Mode</div>
                                        <div class="text-muted" style="font-size: 11px;">Forces bust sector based on rigged rate</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Rigged Rate Slider -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold text-dark small mb-0">House Trap Rigged Force Rate (%)</label>
                            <span class="badge bg-danger text-white rounded-pill px-2 py-1" id="riggedRateBadge">{{ $setting->vortex_rigged_rate ?? 30.0 }}%</span>
                        </div>
                        <input type="range" class="form-range" name="vortex_rigged_rate" id="riggedRateRange" min="0" max="100" step="any" value="{{ $setting->vortex_rigged_rate ?? 30.0 }}" oninput="document.getElementById('riggedRateBadge').innerText = parseFloat(this.value).toFixed(1) + '%'">
                        <div class="d-flex justify-content-between text-muted" style="font-size: 10px;">
                            <span>0% (Soft)</span>
                            <span>50% (Standard Casino)</span>
                            <span>100% (Aggressive Trap)</span>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small">House Edge (%)</label>
                            <input type="number" step="any" name="vortex_house_edge" class="form-control rounded-3" value="{{ $setting->vortex_house_edge ?? 4.00 }}" required>
                            <div class="text-muted" style="font-size: 11px;">Platform margin deducted from multipliers</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small">Max Multiplier Cap (X)</label>
                            <input type="number" step="any" name="vortex_max_multiplier_cap" class="form-control rounded-3" value="{{ $setting->vortex_max_multiplier_cap ?? 200.00 }}" required>
                            <div class="text-muted" style="font-size: 11px;">Max win cap per session (e.g. 200X)</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small">Minimum Bet (₹)</label>
                            <input type="number" step="any" name="vortex_min_bet" class="form-control rounded-3" value="{{ $setting->vortex_min_bet ?? 1.00 }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small">Maximum Bet (₹)</label>
                            <input type="number" step="any" name="vortex_max_bet" class="form-control rounded-3" value="{{ $setting->vortex_max_bet ?? 10000.00 }}" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-info text-white fw-bold rounded-pill w-100 py-2">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Vortex Settings
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right: Wheel Ring Architecture & Mechanics -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold text-dark mb-1">
                    <i class="fa-solid fa-circle-nodes text-info me-2"></i> Concentric Wheel Architecture
                </h5>
                <p class="text-muted small mb-0">3 concentric tiered rings with real-time level upgrades.</p>
            </div>
            <div class="card-body p-4">
                <div class="p-3 mb-3 rounded-3" style="background: rgba(6, 182, 212, 0.08); border-left: 4px solid #06b6d4;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark"><i class="fa-solid fa-droplet text-info me-1"></i> Ring 1: Inner (Water Tier)</span>
                        <span class="badge bg-info-subtle text-info fw-bold">8 Sectors (45°)</span>
                    </div>
                    <div class="small text-muted mb-2">Starting ring for every game round. Multipliers range from 0.5X to 2.0X.</div>
                    <div class="d-flex flex-wrap gap-1">
                        <span class="badge bg-danger">0.0X (Bust)</span>
                        <span class="badge bg-secondary">0.5X</span>
                        <span class="badge bg-info text-dark">1.2X</span>
                        <span class="badge bg-info text-dark">1.5X</span>
                        <span class="badge bg-info text-dark">1.8X</span>
                        <span class="badge bg-info text-dark">2.0X</span>
                        <span class="badge bg-warning text-dark">+7X Boost</span>
                        <span class="badge bg-success">⬆ LEVEL UP</span>
                    </div>
                </div>

                <div class="p-3 mb-3 rounded-3" style="background: rgba(16, 185, 129, 0.08); border-left: 4px solid #10b981;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark"><i class="fa-solid fa-leaf text-success me-1"></i> Ring 2: Middle (Earth Tier)</span>
                        <span class="badge bg-success-subtle text-success fw-bold">8 Sectors (45°)</span>
                    </div>
                    <div class="small text-muted mb-2">Unlocked upon Level Up from Ring 1. Multipliers range from 2.5X to 8.0X.</div>
                    <div class="d-flex flex-wrap gap-1">
                        <span class="badge bg-danger">0.0X (Bust)</span>
                        <span class="badge bg-secondary">0.5X</span>
                        <span class="badge bg-success">2.5X</span>
                        <span class="badge bg-success">3.5X</span>
                        <span class="badge bg-success">5.0X</span>
                        <span class="badge bg-success">8.0X</span>
                        <span class="badge bg-warning text-dark">+20.5X Boost</span>
                        <span class="badge bg-warning text-dark">⬆ LEVEL UP</span>
                    </div>
                </div>

                <div class="p-3 rounded-3" style="background: rgba(239, 68, 68, 0.08); border-left: 4px solid #ef4444;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark"><i class="fa-solid fa-fire text-danger me-1"></i> Ring 3: Outer (Fire Tier)</span>
                        <span class="badge bg-danger-subtle text-danger fw-bold">10 Sectors (36°)</span>
                    </div>
                    <div class="small text-muted mb-2">Ultimate jackpot tier with mega multipliers up to 200X.</div>
                    <div class="d-flex flex-wrap gap-1">
                        <span class="badge bg-danger">0.0X (Bust)</span>
                        <span class="badge bg-warning text-dark">10X</span>
                        <span class="badge bg-warning text-dark">15X</span>
                        <span class="badge bg-warning text-dark">25X</span>
                        <span class="badge bg-warning text-dark">40X</span>
                        <span class="badge bg-danger">60X</span>
                        <span class="badge bg-danger">80X</span>
                        <span class="badge bg-danger">100X</span>
                        <span class="badge bg-danger">150X</span>
                        <span class="badge bg-danger fw-bold">🔥 200X</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Live Active Sessions (Force Bust & Force Cashout) -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h5 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-satellite-dish text-info me-2"></i> Live Active Player Sessions
                @if($activeCount > 0)
                    <span class="badge bg-info text-white rounded-pill ms-2">{{ $activeCount }} Active</span>
                @endif
            </h5>
            <p class="text-muted small mb-0">Monitor players currently spinning the concentric wheel and intervene in real-time.</p>
        </div>
        <div class="text-muted small">
            Total Active Liability: <span class="fw-bold text-danger fs-6">₹{{ number_format($activeLiability, 2) }}</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Game ID</th>
                        <th>Player</th>
                        <th>Bet (₹)</th>
                        <th>Ring Tier</th>
                        <th>Multiplier</th>
                        <th>Current Payout</th>
                        <th>50% Part Cashout</th>
                        <th>Spins</th>
                        <th class="text-end pe-4">Risk Interventions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeGames as $game)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">
                                <span class="font-monospace text-info">#{{ $game->game_id }}</span>
                                <div class="text-muted small" style="font-size: 11px;">{{ $game->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $game->user->name ?? 'User #' . $game->user_id }}</div>
                                <div class="text-muted small">{{ $game->user->phone ?? 'N/A' }}</div>
                            </td>
                            <td class="fw-bold text-dark">₹{{ number_format($game->bet_amount, 2) }}</td>
                            <td>
                                @if($game->current_ring == 1)
                                    <span class="badge bg-info-subtle text-info px-2 py-1">Ring 1 (Water 🌊)</span>
                                @elseif($game->current_ring == 2)
                                    <span class="badge bg-success-subtle text-success px-2 py-1">Ring 2 (Earth 🦠)</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1">Ring 3 (Fire 🔥)</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark fw-bold px-2 py-1 fs-6">
                                    {{ number_format($game->current_multiplier, 2) }}x
                                </span>
                            </td>
                            <td class="fw-bold text-success fs-6">
                                ₹{{ number_format($game->payout_amount, 2) }}
                            </td>
                            <td>
                                @if($game->part_cashed_out)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="fa-solid fa-check me-1"></i> ₹{{ number_format($game->part_cashout_amount, 2) }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted">Not Taken</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $game->spin_count }} spins</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- Force Bust Button -->
                                    <form method="POST" action="{{ route('admin.vortex.force-bust', $game->id) }}" onsubmit="return confirm('Force BUST for Game #{{ $game->game_id }}? The game will immediately terminate with 0 win!')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-2 py-1" title="Force Bust">
                                            <i class="fa-solid fa-skull me-1"></i> Force Bust
                                        </button>
                                    </form>

                                    <!-- Force Cashout Button -->
                                    <form method="POST" action="{{ route('admin.vortex.force-cashout', $game->id) }}" onsubmit="return confirm('Force CASHOUT for Game #{{ $game->game_id }}? ₹{{ number_format($game->payout_amount, 2) }} will be credited to player wallet!')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success fw-bold rounded-pill px-2 py-1" title="Force Cashout">
                                            <i class="fa-solid fa-hand-holding-dollar me-1"></i> Cashout
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-circle-check text-success fa-2x mb-2 d-block"></i>
                                No active player sessions right now. Platform is idle.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Row 4: Recent Games History Log -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h5 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-clock-rotate-left text-info me-2"></i> Vortex Games History & Audit Log
            </h5>
            <p class="text-muted small mb-0">Search and audit all completed and past rounds across the platform.</p>
        </div>
        <form method="GET" action="{{ route('admin.vortex.index') }}" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                <option value="cashed_out" {{ request('status') === 'cashed_out' ? 'selected' : '' }}>Cashed Out Only</option>
                <option value="busted" {{ request('status') === 'busted' ? 'selected' : '' }}>Busted Only</option>
            </select>
            <input type="text" name="search" class="form-control form-control-sm rounded-pill" placeholder="Search Game ID, Player..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sm btn-info text-white rounded-pill px-3">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Game ID</th>
                        <th>Player</th>
                        <th>Bet Amount</th>
                        <th>Ring Reached</th>
                        <th>Final Multiplier</th>
                        <th>Total Payout</th>
                        <th>Part Payout</th>
                        <th>Net Profit (GGR)</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentGames as $game)
                        @php
                            $userTotalWin = (float) $game->win_amount;
                            $roundProfit = (float) $game->bet_amount - $userTotalWin;
                        @endphp
                        <tr>
                            <td class="ps-4 font-monospace fw-bold text-dark">
                                #{{ $game->game_id }}
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $game->user->name ?? 'User #' . $game->user_id }}</div>
                                <div class="text-muted small">{{ $game->user->phone ?? 'N/A' }}</div>
                            </td>
                            <td class="fw-bold text-dark">₹{{ number_format($game->bet_amount, 2) }}</td>
                            <td>
                                @if($game->current_ring == 1)
                                    <span class="badge bg-info-subtle text-info">Ring 1 (Water)</span>
                                @elseif($game->current_ring == 2)
                                    <span class="badge bg-success-subtle text-success">Ring 2 (Earth)</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Ring 3 (Fire)</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold {{ $game->status === 'cashed_out' ? 'text-warning' : 'text-muted' }}">
                                    {{ number_format($game->current_multiplier, 2) }}x
                                </span>
                            </td>
                            <td class="fw-bold {{ $userTotalWin > 0 ? 'text-success' : 'text-muted' }}">
                                ₹{{ number_format($userTotalWin, 2) }}
                            </td>
                            <td>
                                @if($game->part_cashed_out)
                                    <span class="badge bg-success-subtle text-success">
                                        ₹{{ number_format($game->part_cashout_amount, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="fw-bold {{ $roundProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $roundProfit >= 0 ? '+' : '' }}₹{{ number_format($roundProfit, 2) }}
                            </td>
                            <td>
                                @if($game->status === 'active')
                                    <span class="badge bg-primary">Active</span>
                                @elseif($game->status === 'cashed_out')
                                    <span class="badge bg-success">Cashed Out</span>
                                @else
                                    <span class="badge bg-danger">Busted</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 text-muted small">
                                {{ $game->created_at->format('d M Y, h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                No games found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recentGames->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $recentGames->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
