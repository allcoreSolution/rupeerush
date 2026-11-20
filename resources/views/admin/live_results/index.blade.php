@extends('layouts.admin')

@section('title', 'All Games Live Round Results - rupeerush Management')

@section('content')
<style>
    .live-pulse-dot {
        width: 9px;
        height: 9px;
        background-color: #22c55e;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        animation: pulse-green 1.8s infinite;
    }
    @keyframes pulse-green {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 7px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
    .game-live-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 14px;
        transition: all 0.2s ease-in-out;
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .game-live-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }
    .game-live-card.active-dealing {
        border-color: #3b82f6;
        background: linear-gradient(180deg, #ffffff 0%, #f0f9ff 100%);
    }
    .outcome-highlight {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 1.15rem;
        line-height: 1.2;
    }
    .scroll-tabs-bar {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        gap: 6px;
        padding-bottom: 4px;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }
    .scroll-tabs-bar::-webkit-scrollbar {
        height: 4px;
    }
    .scroll-tabs-bar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .game-tab-pill {
        white-space: nowrap;
        font-size: 0.78rem !important;
        padding: 5px 12px !important;
        font-weight: 700 !important;
        border-radius: 50rem !important;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
        border: 1px solid transparent;
    }
    .table-compact-results {
        font-size: 0.82rem;
    }
    .table-compact-results thead th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 8px 12px !important;
        background: #f8fafc;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-compact-results tbody td {
        padding: 8px 12px !important;
        vertical-align: middle;
    }
</style>

<!-- Top Header & Live Auto-Refresh Controller -->
<div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
    <div>
        <div class="d-flex align-items-center gap-2">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="fa-solid fa-satellite-dish text-danger me-2"></i> Live Round Results Monitor
            </h5>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5 d-flex align-items-center gap-1.5" style="font-size: 0.72rem;">
                <span class="live-pulse-dot"></span> 11 GAMES REAL-TIME
            </span>
        </div>
        <div class="text-muted small mt-0.5" style="font-size: 0.75rem;">
            Centralized live tracking of ongoing rounds, active card dealing, multiplier outcomes, and game results.
        </div>
    </div>

    <!-- Live Polling Controls -->
    <div class="d-flex align-items-center gap-2">
        <div class="form-check form-switch mb-0 d-flex align-items-center gap-1.5 bg-white border px-3 py-1.5 rounded-pill shadow-sm">
            <input class="form-check-input mt-0" type="checkbox" role="switch" id="autoRefreshToggle" checked>
            <label class="form-check-label fw-bold text-dark small" for="autoRefreshToggle" style="font-size: 0.75rem;">
                Auto-Refresh (<span id="refreshTimer">5s</span>)
            </label>
        </div>
        <button id="manualRefreshBtn" class="btn btn-sm btn-outline-primary rounded-pill fw-bold px-3 py-1" title="Refresh Live State Now">
            <i class="fa-solid fa-rotate me-1" id="refreshIcon"></i> Refresh Now
        </button>
        <a href="{{ route('admin.bets.index') }}" class="btn btn-sm btn-primary rounded-pill fw-bold px-3 py-1">
            <i class="fa-solid fa-receipt me-1"></i> Bets Monitor
        </a>
    </div>
</div>

<!-- All 11 Games Live Matrix (Ultra-Compact High Density Cards) -->
<div class="row g-2 mb-3" id="liveMatrixContainer">
    @foreach($matrix as $key => $g)
        @php
            $isLiveActive = in_array(strtolower($g['status']), ['betting', 'dealing', 'spinning', 'flying', 'racing', 'active', 'live'], true);
        @endphp
        <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="game-live-card {{ $isLiveActive ? 'active-dealing' : '' }}" id="card-{{ $key }}">
                <div>
                    <!-- Header: Game name & status badge -->
                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <div class="d-flex align-items-center gap-1.5">
                            <i class="fa-solid {{ $g['icon'] }} text-{{ explode(' ', $g['badge_color'])[0] }}" style="font-size: 0.95rem;"></i>
                            <span class="fw-bold text-dark" style="font-size: 0.84rem;">{{ $g['name'] }}</span>
                        </div>
                        <span class="badge {{ $isLiveActive ? 'bg-success text-white' : 'bg-secondary-subtle text-secondary' }} rounded-pill px-2 py-0.5" style="font-size: 0.65rem;" id="badge-{{ $key }}">
                            @if($isLiveActive)
                                <span class="live-pulse-dot me-1" style="width: 6px; height: 6px;"></span>
                            @endif
                            {{ strtoupper($g['status']) }}
                        </span>
                    </div>

                    <!-- Round / Period Identifier -->
                    <div class="d-flex align-items-center justify-content-between text-muted mb-1" style="font-size: 0.72rem;">
                        <span class="font-monospace fw-semibold" id="round-{{ $key }}">
                            {{ $g['round_id'] }}
                        </span>
                        @if($g['is_manual'])
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-1.5 py-0" style="font-size: 0.62rem;" title="Manual Admin Override Active">
                                <i class="fa-solid fa-hand me-0.5"></i> MANUAL ({{ $g['manual_target'] }})
                            </span>
                        @else
                            <span class="badge bg-light border text-muted rounded-pill px-1.5 py-0" style="font-size: 0.62rem;">
                                AUTO RNG
                            </span>
                        @endif
                    </div>

                    <!-- Current Winning Outcome / Dealing State -->
                    <div class="my-1.5 p-2 rounded-2 bg-light border text-center">
                        <div class="outcome-highlight text-dark text-truncate" id="outcome-{{ $key }}">
                            {{ $g['outcome'] }}
                        </div>
                        @if(!empty($g['outcome_sub']))
                            <div class="text-muted small text-truncate" style="font-size: 0.7rem;" id="sub-{{ $key }}">
                                {{ $g['outcome_sub'] }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer: Volume + Direct Control Link -->
                <div class="d-flex align-items-center justify-content-between pt-1 border-top mt-1" style="font-size: 0.72rem;">
                    <div class="text-muted">
                        @if($g['bets_count'] !== '-')
                            Bets: <strong class="text-dark">{{ $g['bets_count'] }}</strong> (₹{{ number_format($g['bet_amount'], 0) }})
                        @else
                            Updated: <strong class="text-dark">{{ $g['updated_at'] }}</strong>
                        @endif
                    </div>
                    <a href="{{ $g['route'] }}" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill fw-semibold" style="font-size: 0.68rem;">
                        Control <i class="fa-solid fa-arrow-up-right-from-square ms-0.5" style="font-size: 0.6rem;"></i>
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Round History & Recent Results Feed Card -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom py-2 px-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <div class="d-flex align-items-center gap-2">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-clock-rotate-left text-primary me-1.5"></i> Recent Rounds & Winning Outcomes
                </h6>
                <span class="badge bg-light border text-muted rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                    Select a game to inspect history
                </span>
            </div>
            <div class="text-muted small" style="font-size: 0.75rem;">
                Last synced at <span class="fw-bold font-monospace" id="lastSyncTime">{{ now('Asia/Kolkata')->format('h:i:s A') }}</span>
            </div>
        </div>

        <!-- Game History Filter Tabs (Horizontal Scrollable Strip) -->
        <div class="scroll-tabs-bar">
            <button class="game-tab-pill bg-primary text-white shadow-sm" data-target="tab-all">
                <i class="fa-solid fa-layer-group me-1"></i> All Games
            </button>
            <button class="game-tab-pill bg-light text-dark" data-target="tab-dragon_tiger">
                <i class="fa-solid fa-dragon me-1 text-danger"></i> Dragon Tiger
            </button>
            <button class="game-tab-pill bg-light text-dark" data-target="tab-teen_patti">
                <i class="fa-solid fa-crown me-1 text-warning"></i> Teen Patti
            </button>
            <button class="game-tab-pill bg-light text-dark" data-target="tab-roulette">
                <i class="fa-solid fa-dharmachakra me-1 text-danger"></i> Roulette 36x
            </button>
            <button class="game-tab-pill bg-light text-dark" data-target="tab-horse_racing">
                <i class="fa-solid fa-horse-head me-1 text-success"></i> Horse Racing
            </button>
            <button class="game-tab-pill bg-light text-dark" data-target="tab-aviator">
                <i class="fa-solid fa-plane-departure me-1 text-danger"></i> Aviator Crash
            </button>
            <button class="game-tab-pill bg-light text-dark" data-target="tab-andar_bahar">
                <i class="fa-solid fa-layer-group me-1 text-warning"></i> Andar Bahar
            </button>
            <button class="game-tab-pill bg-light text-dark" data-target="tab-wingo">
                <i class="fa-solid fa-palette me-1 text-danger"></i> WinGo Color
            </button>
            <button class="game-tab-pill bg-light text-dark" data-target="tab-lottery">
                <i class="fa-solid fa-ticket me-1 text-info"></i> 2-Digit Lottery
            </button>
            <button class="game-tab-pill bg-light text-dark" data-target="tab-vortex">
                <i class="fa-solid fa-compact-disc me-1 text-info"></i> Vortex Wheel
            </button>
            <button class="game-tab-pill bg-light text-dark" data-target="tab-mines">
                <i class="fa-solid fa-bomb me-1 text-success"></i> Mines 5x5
            </button>
            <button class="game-tab-pill bg-light text-dark" data-target="tab-sports">
                <i class="fa-solid fa-baseball-bat-ball me-1 text-primary"></i> Cricket
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <!-- 1. DRAGON TIGER RECENT TABLE -->
        <div class="history-table-section" id="tab-dragon_tiger" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover table-compact-results align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Round ID</th>
                            <th>Dragon Card</th>
                            <th>Tiger Card</th>
                            <th class="text-center">Winner Outcome</th>
                            <th>Result Mode</th>
                            <th class="text-end">Bets / Volume</th>
                            <th class="text-end pe-3">Round Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dtRecent as $r)
                            <tr>
                                <td class="ps-3 font-monospace fw-bold">#{{ $r->round_id }}</td>
                                <td>
                                    @if(!empty($r->dragon_card['code']))
                                        <span class="badge bg-light text-dark border font-monospace px-2 py-1">{{ $r->dragon_card['code'] }}</span>
                                    @else
                                        <span class="text-muted small">--</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($r->tiger_card['code']))
                                        <span class="badge bg-light text-dark border font-monospace px-2 py-1">{{ $r->tiger_card['code'] }}</span>
                                    @else
                                        <span class="text-muted small">--</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($r->winner === 'dragon')
                                        <span class="badge bg-danger text-white rounded-pill px-3 py-1 fw-bold"><i class="fa-solid fa-dragon me-1"></i> DRAGON</span>
                                    @elseif($r->winner === 'tiger')
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold"><i class="fa-solid fa-fire me-1"></i> TIGER</span>
                                    @elseif($r->winner === 'tie')
                                        <span class="badge bg-success text-white rounded-pill px-3 py-1 fw-bold"><i class="fa-solid fa-equals me-1"></i> TIE</span>
                                    @else
                                        <span class="badge bg-secondary text-white rounded-pill px-2 py-0.5">{{ strtoupper($r->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($r->is_manual)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5">Manual ({{ $r->manual_winner }})</span>
                                    @else
                                        <span class="badge bg-light border text-muted rounded-pill px-2 py-0.5">RNG Auto</span>
                                    @endif
                                </td>
                                <td class="text-end font-monospace">
                                    {{ $r->total_bets_count ?? 0 }} bets <span class="text-muted">(₹{{ number_format((float) ($r->total_bet_amount ?? 0), 2) }})</span>
                                </td>
                                <td class="text-end pe-3 text-muted small">
                                    {{ $r->created_at?->timezone('Asia/Kolkata')->format('d M, h:i:s A') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">No rounds recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. TEEN PATTI RECENT TABLE -->
        <div class="history-table-section" id="tab-teen_patti" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover table-compact-results align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Round ID</th>
                            <th>Player A Cards</th>
                            <th>Player B Cards</th>
                            <th class="text-center">Winner Outcome</th>
                            <th>Rank Title</th>
                            <th>Mode</th>
                            <th class="text-end">Bets / Volume</th>
                            <th class="text-end pe-3">Round Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tpRecent as $r)
                            <tr>
                                <td class="ps-3 font-monospace fw-bold">#{{ $r->round_id }}</td>
                                <td>
                                    @if(is_array($r->player_a_cards))
                                        @foreach($r->player_a_cards as $c)
                                            <span class="badge bg-light border text-dark font-monospace px-1.5 py-0.5">{{ $c['code'] ?? $c }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">--</span>
                                    @endif
                                </td>
                                <td>
                                    @if(is_array($r->player_b_cards))
                                        @foreach($r->player_b_cards as $c)
                                            <span class="badge bg-light border text-dark font-monospace px-1.5 py-0.5">{{ $c['code'] ?? $c }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">--</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($r->winner === 'player_a')
                                        <span class="badge bg-primary text-white rounded-pill px-3 py-1 fw-bold"><i class="fa-solid fa-crown me-1"></i> PLAYER A</span>
                                    @elseif($r->winner === 'player_b')
                                        <span class="badge bg-danger text-white rounded-pill px-3 py-1 fw-bold"><i class="fa-solid fa-crown me-1"></i> PLAYER B</span>
                                    @elseif($r->winner === 'tie')
                                        <span class="badge bg-success text-white rounded-pill px-3 py-1 fw-bold">SPLIT TIE</span>
                                    @else
                                        <span class="badge bg-secondary text-white rounded-pill px-2 py-0.5">{{ strtoupper($r->status) }}</span>
                                    @endif
                                </td>
                                <td><span class="small text-muted">{{ $r->player_a_rank_title ?? $r->player_b_rank_title ?? '-' }}</span></td>
                                <td>
                                    @if($r->is_manual)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5">Manual ({{ $r->manual_winner }})</span>
                                    @else
                                        <span class="badge bg-light border text-muted rounded-pill px-2 py-0.5">RNG Auto</span>
                                    @endif
                                </td>
                                <td class="text-end font-monospace">
                                    {{ $r->total_bets_count ?? 0 }} bets <span class="text-muted">(₹{{ number_format((float) ($r->total_bet_amount ?? 0), 2) }})</span>
                                </td>
                                <td class="text-end pe-3 text-muted small">
                                    {{ $r->created_at?->timezone('Asia/Kolkata')->format('d M, h:i:s A') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-4 text-muted">No rounds recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. ROULETTE RECENT TABLE -->
        <div class="history-table-section" id="tab-roulette" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover table-compact-results align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Round ID</th>
                            <th class="text-center">Winning Number</th>
                            <th>Color</th>
                            <th>Even / Odd</th>
                            <th>Dozen & Column</th>
                            <th>Mode</th>
                            <th class="text-end">Bets / Volume</th>
                            <th class="text-end pe-3">Round Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rouletteRecent as $r)
                            <tr>
                                <td class="ps-3 font-monospace fw-bold">#{{ $r->round_id }}</td>
                                <td class="text-center">
                                    @if($r->winning_number !== null)
                                        <span class="badge rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold" style="width: 32px; height: 32px; background: {{ $r->winning_color === 'red' ? '#dc2626' : ($r->winning_color === 'black' ? '#0f172a' : '#16a34a') }}; font-size: 0.9rem;">
                                            {{ $r->winning_number }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary text-white">{{ strtoupper($r->status) }}</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-light border text-dark text-uppercase px-2 py-0.5">{{ $r->winning_color ?? '-' }}</span></td>
                                <td><span class="small text-muted">{{ $r->is_even ? 'Even' : 'Odd' }} | {{ $r->is_high ? 'High (19-36)' : 'Low (1-18)' }}</span></td>
                                <td><span class="small text-muted">Dozen {{ $r->dozen ?? '-' }} | Col {{ $r->column ?? '-' }}</span></td>
                                <td>
                                    @if($r->is_manual)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5">Manual ({{ $r->manual_number }})</span>
                                    @else
                                        <span class="badge bg-light border text-muted rounded-pill px-2 py-0.5">RNG Auto</span>
                                    @endif
                                </td>
                                <td class="text-end font-monospace">
                                    {{ $r->total_bets_count ?? 0 }} bets <span class="text-muted">(₹{{ number_format((float) ($r->total_bet_amount ?? 0), 2) }})</span>
                                </td>
                                <td class="text-end pe-3 text-muted small">
                                    {{ $r->created_at?->timezone('Asia/Kolkata')->format('d M, h:i:s A') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-4 text-muted">No rounds recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. HORSE RACING RECENT TABLE -->
        <div class="history-table-section" id="tab-horse_racing" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover table-compact-results align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Race ID</th>
                            <th>Winner Horse</th>
                            <th>Second Place</th>
                            <th>Third Place</th>
                            <th>Mode</th>
                            <th class="text-end">Bets / Volume</th>
                            <th class="text-end pe-3">Race Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($horseRecent as $r)
                            <tr>
                                <td class="ps-3 font-monospace fw-bold">Race #{{ $r->round_id }}</td>
                                <td>
                                    @if($r->winner_horse_id)
                                        <span class="badge bg-success text-white px-2.5 py-1 rounded-pill fw-bold">
                                            <i class="fa-solid fa-trophy me-1 text-warning"></i> Horse #{{ $r->winner_horse_id }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary text-white">{{ strtoupper($r->status) }}</span>
                                    @endif
                                </td>
                                <td><span class="small text-muted">#{{ $r->second_horse_id ?? '-' }}</span></td>
                                <td><span class="small text-muted">#{{ $r->third_horse_id ?? '-' }}</span></td>
                                <td>
                                    @if($r->is_manual)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5">Manual (#{{ $r->manual_winner_id }})</span>
                                    @else
                                        <span class="badge bg-light border text-muted rounded-pill px-2 py-0.5">RNG Auto</span>
                                    @endif
                                </td>
                                <td class="text-end font-monospace">
                                    {{ $r->total_bets_count ?? 0 }} bets <span class="text-muted">(₹{{ number_format((float) ($r->total_bet_amount ?? 0), 2) }})</span>
                                </td>
                                <td class="text-end pe-3 text-muted small">
                                    {{ $r->created_at?->timezone('Asia/Kolkata')->format('d M, h:i:s A') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">No races recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. AVIATOR RECENT TABLE -->
        <div class="history-table-section" id="tab-aviator" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover table-compact-results align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Flight Round</th>
                            <th class="text-center">Crash Multiplier</th>
                            <th>Status</th>
                            <th>Mode</th>
                            <th class="text-end">Bets / Volume</th>
                            <th class="text-end pe-3">Flight Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aviatorRecent as $r)
                            <tr>
                                <td class="ps-3 font-monospace fw-bold">#{{ $r->round_id }}</td>
                                <td class="text-center">
                                    <span class="badge {{ (float)$r->crash_multiplier >= 2.0 ? 'bg-success' : ((float)$r->crash_multiplier >= 1.5 ? 'bg-primary' : 'bg-danger') }} px-3 py-1 rounded-pill font-monospace fw-bold fs-6">
                                        {{ number_format((float)$r->crash_multiplier, 2) }}x
                                    </span>
                                </td>
                                <td><span class="badge bg-light border text-dark">{{ strtoupper($r->status) }}</span></td>
                                <td>
                                    @if($r->is_manual)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5">Manual ({{ $r->manual_crash_multiplier }}x)</span>
                                    @else
                                        <span class="badge bg-light border text-muted rounded-pill px-2 py-0.5">RNG Curve</span>
                                    @endif
                                </td>
                                <td class="text-end font-monospace">
                                    {{ $r->total_bets_count ?? 0 }} bets <span class="text-muted">(₹{{ number_format((float) ($r->total_bet_amount ?? 0), 2) }})</span>
                                </td>
                                <td class="text-end pe-3 text-muted small">
                                    {{ $r->created_at?->timezone('Asia/Kolkata')->format('d M, h:i:s A') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No flights recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 6. ANDAR BAHAR RECENT TABLE -->
        <div class="history-table-section" id="tab-andar_bahar" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover table-compact-results align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Round ID</th>
                            <th>Joker Card</th>
                            <th class="text-center">Winning Side</th>
                            <th>Total Cards Dealt</th>
                            <th>Mode</th>
                            <th class="text-end">Bets / Volume</th>
                            <th class="text-end pe-3">Round Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($abRecent as $r)
                            <tr>
                                <td class="ps-3 font-monospace fw-bold">#{{ $r->round_id }}</td>
                                <td>
                                    @if(!empty($r->joker_card['code']))
                                        <span class="badge bg-warning text-dark border px-2 py-1 font-monospace fw-bold">{{ $r->joker_card['code'] }}</span>
                                    @else
                                        <span class="text-muted small">--</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($r->winning_side === 'andar')
                                        <span class="badge bg-danger text-white rounded-pill px-3 py-1 fw-bold"><i class="fa-solid fa-arrow-left me-1"></i> ANDAR</span>
                                    @elseif($r->winning_side === 'bahar')
                                        <span class="badge bg-primary text-white rounded-pill px-3 py-1 fw-bold"><i class="fa-solid fa-arrow-right me-1"></i> BAHAR</span>
                                    @else
                                        <span class="badge bg-secondary text-white">{{ strtoupper($r->status) }}</span>
                                    @endif
                                </td>
                                <td><span class="font-monospace">{{ $r->total_cards_dealt ?? 0 }} Cards</span></td>
                                <td>
                                    @if($r->is_manual)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5">Manual ({{ strtoupper($r->manual_winning_side) }})</span>
                                    @else
                                        <span class="badge bg-light border text-muted rounded-pill px-2 py-0.5">RNG Auto</span>
                                    @endif
                                </td>
                                <td class="text-end font-monospace">
                                    {{ $r->total_bets_count ?? 0 }} bets <span class="text-muted">(₹{{ number_format((float) ($r->total_bet_amount ?? 0), 2) }})</span>
                                </td>
                                <td class="text-end pe-3 text-muted small">
                                    {{ $r->created_at?->timezone('Asia/Kolkata')->format('d M, h:i:s A') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">No rounds recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 7. WINGO RECENT TABLE -->
        <div class="history-table-section" id="tab-wingo" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover table-compact-results align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Period ID</th>
                            <th>Game Mode</th>
                            <th class="text-center">Number</th>
                            <th>Color</th>
                            <th>Big / Small</th>
                            <th>Mode</th>
                            <th class="text-end">Bets / Volume</th>
                            <th class="text-end pe-3">Period Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wingoRecent as $r)
                            <tr>
                                <td class="ps-3 font-monospace fw-bold">#{{ $r->period_id }}</td>
                                <td><span class="badge bg-dark text-white rounded-pill px-2 py-0.5">{{ strtoupper($r->game_type) }}</span></td>
                                <td class="text-center">
                                    @if($r->winning_number !== null)
                                        <span class="badge bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold" style="width: 28px; height: 28px; font-size: 0.85rem;">
                                            {{ $r->winning_number }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary text-white">{{ strtoupper($r->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ str_contains($r->winning_color ?? '', 'green') ? 'bg-success' : (str_contains($r->winning_color ?? '', 'red') ? 'bg-danger' : 'bg-primary') }} text-white px-2 py-0.5 rounded-pill">
                                        {{ ucfirst($r->winning_color ?? '-') }}
                                    </span>
                                </td>
                                <td><span class="badge bg-light border text-dark px-2 py-0.5">{{ strtoupper($r->winning_size ?? '-') }}</span></td>
                                <td>
                                    @if($r->is_manual)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5">Manual (#{{ $r->manual_number }})</span>
                                    @else
                                        <span class="badge bg-light border text-muted rounded-pill px-2 py-0.5">RNG Auto</span>
                                    @endif
                                </td>
                                <td class="text-end font-monospace">
                                    {{ $r->total_bets_count ?? 0 }} bets <span class="text-muted">(₹{{ number_format((float) ($r->total_bet_amount ?? 0), 2) }})</span>
                                </td>
                                <td class="text-end pe-3 text-muted small">
                                    {{ $r->created_at?->timezone('Asia/Kolkata')->format('d M, h:i:s A') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-4 text-muted">No WinGo periods recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 8. LOTTERY RECENT TABLE -->
        <div class="history-table-section" id="tab-lottery" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover table-compact-results align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Date & Slot</th>
                            <th>XA - XC (1-3)</th>
                            <th>XD - XF (4-6)</th>
                            <th>XG - XJ (7-10)</th>
                            <th>Mode</th>
                            <th class="text-end pe-3">Generated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lotteryRecent as $r)
                            <tr>
                                <td class="ps-3">
                                    <span class="fw-bold text-dark">{{ $r->result_date?->format('d M Y') }}</span>
                                    <div class="text-primary font-monospace fw-semibold" style="font-size: 0.75rem;">Slot: {{ $r->slot_time }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-dark font-monospace px-1.5 py-0.5 me-1">XA: {{ $r->XA }}</span>
                                    <span class="badge bg-dark font-monospace px-1.5 py-0.5 me-1">XB: {{ $r->XB }}</span>
                                    <span class="badge bg-dark font-monospace px-1.5 py-0.5">XC: {{ $r->XC }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary font-monospace px-1.5 py-0.5 me-1">XD: {{ $r->XD }}</span>
                                    <span class="badge bg-secondary font-monospace px-1.5 py-0.5 me-1">XE: {{ $r->XE }}</span>
                                    <span class="badge bg-secondary font-monospace px-1.5 py-0.5">XF: {{ $r->XF }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light border text-dark font-monospace px-1.5 py-0.5 me-1">XG: {{ $r->XG }}</span>
                                    <span class="badge bg-light border text-dark font-monospace px-1.5 py-0.5 me-1">XH: {{ $r->XH }}</span>
                                    <span class="badge bg-light border text-dark font-monospace px-1.5 py-0.5 me-1">XI: {{ $r->XI }}</span>
                                    <span class="badge bg-light border text-dark font-monospace px-1.5 py-0.5">XJ: {{ $r->XJ }}</span>
                                </td>
                                <td>
                                    @if($r->is_manual)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5">Manual Slot</span>
                                    @else
                                        <span class="badge bg-light border text-muted rounded-pill px-2 py-0.5">Cron Generated</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3 text-muted small">
                                    {{ $r->created_at?->timezone('Asia/Kolkata')->format('d M, h:i:s A') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No lottery results recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 9. VORTEX RECENT TABLE -->
        <div class="history-table-section" id="tab-vortex" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover table-compact-results align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Game ID</th>
                            <th>Player</th>
                            <th>Ring Reached</th>
                            <th class="text-center">Multiplier</th>
                            <th>Status</th>
                            <th class="text-end">Bet Stake</th>
                            <th class="text-end">Win Amount</th>
                            <th class="text-end pe-3">Played At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vortexRecent as $r)
                            <tr>
                                <td class="ps-3 font-monospace fw-bold">#{{ substr($r->game_id, -8) }}</td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $r->user->name ?? 'Player' }}</span>
                                </td>
                                <td><span class="badge bg-info text-dark px-2 py-0.5 rounded-pill font-monospace">Ring {{ $r->current_ring }}</span></td>
                                <td class="text-center font-monospace fw-bold text-primary">{{ number_format((float)$r->current_multiplier, 2) }}x</td>
                                <td>
                                    <span class="badge {{ $r->status === 'cashed_out' ? 'bg-success' : ($r->status === 'busted' ? 'bg-danger' : 'bg-warning text-dark') }} rounded-pill px-2 py-0.5">
                                        {{ strtoupper($r->status) }}
                                    </span>
                                </td>
                                <td class="text-end font-monospace">₹{{ number_format((float)$r->bet_amount, 2) }}</td>
                                <td class="text-end font-monospace fw-bold text-success">
                                    {{ (float)($r->win_amount ?? $r->payout_amount) > 0 ? '+₹'.number_format((float)($r->win_amount ?? $r->payout_amount), 2) : '₹0.00' }}
                                </td>
                                <td class="text-end pe-3 text-muted small">
                                    {{ $r->created_at?->timezone('Asia/Kolkata')->format('d M, h:i:s A') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-4 text-muted">No vortex games recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 10. MINES RECENT TABLE -->
        <div class="history-table-section" id="tab-mines" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover table-compact-results align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Game ID</th>
                            <th>Player</th>
                            <th>Mines Grid</th>
                            <th>Safe Steps</th>
                            <th class="text-center">Multiplier</th>
                            <th>Status</th>
                            <th class="text-end">Bet Stake</th>
                            <th class="text-end">Win Amount</th>
                            <th class="text-end pe-3">Played At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($minesRecent as $r)
                            <tr>
                                <td class="ps-3 font-monospace fw-bold">#{{ substr($r->game_id, -8) }}</td>
                                <td><span class="fw-semibold text-dark">{{ $r->user->name ?? 'Player' }}</span></td>
                                <td><span class="badge bg-danger text-white rounded-pill px-2 py-0.5"><i class="fa-solid fa-bomb me-0.5"></i> {{ $r->mines_count }} Mines</span></td>
                                <td><span class="badge bg-success text-white rounded-pill px-2 py-0.5"><i class="fa-solid fa-coins me-0.5"></i> {{ $r->current_step }} Safe</span></td>
                                <td class="text-center font-monospace fw-bold text-primary">{{ number_format((float)$r->current_multiplier, 2) }}x</td>
                                <td>
                                    <span class="badge {{ $r->status === 'cashed_out' ? 'bg-success' : ($r->status === 'exploded' ? 'bg-danger' : 'bg-warning text-dark') }} rounded-pill px-2 py-0.5">
                                        {{ strtoupper($r->status) }}
                                    </span>
                                </td>
                                <td class="text-end font-monospace">₹{{ number_format((float)$r->bet_amount, 2) }}</td>
                                <td class="text-end font-monospace fw-bold text-success">
                                    {{ (float)$r->win_amount > 0 ? '+₹'.number_format((float)$r->win_amount, 2) : '₹0.00' }}
                                </td>
                                <td class="text-end pe-3 text-muted small">
                                    {{ $r->created_at?->timezone('Asia/Kolkata')->format('d M, h:i:s A') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center py-4 text-muted">No mines games recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 11. SPORTS RECENT TABLE -->
        <div class="history-table-section" id="tab-sports" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover table-compact-results align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Match</th>
                            <th>Format</th>
                            <th>Teams</th>
                            <th>Scores</th>
                            <th>Status</th>
                            <th>Winner</th>
                            <th class="text-end pe-3">Schedule</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sportsRecent as $r)
                            <tr>
                                <td class="ps-3 font-monospace fw-bold">{{ $r->title ?? $r->name }}</td>
                                <td><span class="badge bg-dark rounded-pill px-2 py-0.5">{{ strtoupper($r->match_type ?? 'Cricket') }}</span></td>
                                <td><span class="fw-bold text-dark">{{ $r->team_a }} vs {{ $r->team_b }}</span></td>
                                <td><span class="small font-monospace">{{ $r->score_team_a ?? '-' }} / {{ $r->score_team_b ?? '-' }}</span></td>
                                <td>
                                    <span class="badge {{ $r->is_live || $r->status === 'live' ? 'bg-danger text-white' : 'bg-secondary text-white' }} rounded-pill px-2 py-0.5">
                                        {{ strtoupper($r->status ?? 'upcoming') }}
                                    </span>
                                </td>
                                <td><span class="fw-bold text-success">{{ $r->winner_team ? $r->winner_team.' Won' : 'Pending' }}</span></td>
                                <td class="text-end pe-3 text-muted small">
                                    {{ $r->starts_at?->timezone('Asia/Kolkata')->format('d M, h:i A') ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">No cricket matches found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MASTER UNIFIED FEED (ALL GAMES) -->
        <div class="history-table-section" id="tab-all">
            <div class="table-responsive">
                <table class="table table-hover table-compact-results align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Game Module</th>
                            <th>Current Round / Identifier</th>
                            <th>Live Status</th>
                            <th>Latest Winning Outcome</th>
                            <th>Control Mode</th>
                            <th class="text-end">Volume / Activity</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($matrix as $key => $g)
                            @php
                                $isLive = in_array(strtolower($g['status']), ['betting', 'dealing', 'spinning', 'flying', 'racing', 'active', 'live'], true);
                            @endphp
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <i class="fa-solid {{ $g['icon'] }} text-{{ explode(' ', $g['badge_color'])[0] }}" style="font-size: 0.95rem;"></i>
                                        <strong class="text-dark">{{ $g['name'] }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-monospace fw-semibold">{{ $g['round_id'] }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $isLive ? 'bg-success text-white' : 'bg-secondary-subtle text-secondary' }} rounded-pill px-2 py-0.5">
                                        @if($isLive)
                                            <span class="live-pulse-dot me-1" style="width: 5px; height: 5px;"></span>
                                        @endif
                                        {{ strtoupper($g['status']) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $g['outcome'] }}</span>
                                    @if(!empty($g['outcome_sub']))
                                        <span class="text-muted small ms-1">({{ $g['outcome_sub'] }})</span>
                                    @endif
                                </td>
                                <td>
                                    @if($g['is_manual'])
                                        <span class="badge bg-danger text-white rounded-pill px-2 py-0.5">
                                            <i class="fa-solid fa-hand me-0.5"></i> MANUAL ({{ $g['manual_target'] }})
                                        </span>
                                    @else
                                        <span class="badge bg-light border text-muted rounded-pill px-2 py-0.5">
                                            AUTO RNG
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end font-monospace">
                                    @if($g['bets_count'] !== '-')
                                        {{ $g['bets_count'] }} bets <span class="text-muted">(₹{{ number_format($g['bet_amount'], 0) }})</span>
                                    @else
                                        <span class="text-muted">{{ $g['updated_at'] }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ $g['route'] }}" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill fw-semibold" style="font-size: 0.72rem;">
                                        Manage & Control <i class="fa-solid fa-arrow-right ms-0.5"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Real-Time Polling & Tab Switching Script --}}
<script>
    (function() {
        // Tab Switching for History Section
        const tabs = document.querySelectorAll('.game-tab-pill');
        const sections = document.querySelectorAll('.history-table-section');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                tabs.forEach(t => {
                    t.classList.remove('bg-primary', 'text-white', 'shadow-sm');
                    t.classList.add('bg-light', 'text-dark');
                });
                this.classList.remove('bg-light', 'text-dark');
                this.classList.add('bg-primary', 'text-white', 'shadow-sm');

                sections.forEach(s => s.style.display = 'none');
                const targetSec = document.getElementById(targetId);
                if (targetSec) {
                    targetSec.style.display = 'block';
                }
            });
        });

        // Real-Time AJAX Auto-Refresh Polling
        let countdown = 5;
        let pollInterval = null;
        const timerEl = document.getElementById('refreshTimer');
        const toggleEl = document.getElementById('autoRefreshToggle');
        const refreshBtn = document.getElementById('manualRefreshBtn');
        const refreshIcon = document.getElementById('refreshIcon');
        const lastSyncTimeEl = document.getElementById('lastSyncTime');

        function fetchLiveState() {
            if (refreshIcon) refreshIcon.classList.add('fa-spin');

            fetch('{{ route("admin.live-results.index") }}?ajax=1', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.matrix) {
                    if (lastSyncTimeEl) lastSyncTimeEl.textContent = data.timestamp;

                    // Update live matrix DOM elements
                    Object.keys(data.matrix).forEach(key => {
                        const item = data.matrix[key];
                        const roundEl = document.getElementById('round-' + key);
                        const outcomeEl = document.getElementById('outcome-' + key);
                        const subEl = document.getElementById('sub-' + key);
                        const badgeEl = document.getElementById('badge-' + key);
                        const cardEl = document.getElementById('card-' + key);

                        if (roundEl) roundEl.textContent = item.round_id;
                        if (outcomeEl) outcomeEl.textContent = item.outcome;
                        if (subEl && item.outcome_sub) subEl.textContent = item.outcome_sub;

                        const isLive = ['betting', 'dealing', 'spinning', 'flying', 'racing', 'active', 'live'].includes((item.status || '').toLowerCase());

                        if (badgeEl) {
                            badgeEl.className = 'badge rounded-pill px-2 py-0.5 ' + (isLive ? 'bg-success text-white' : 'bg-secondary-subtle text-secondary');
                            badgeEl.innerHTML = (isLive ? '<span class="live-pulse-dot me-1" style="width: 6px; height: 6px;"></span>' : '') + (item.status || '').toUpperCase();
                        }
                        if (cardEl) {
                            if (isLive) cardEl.classList.add('active-dealing');
                            else cardEl.classList.remove('active-dealing');
                        }
                    });
                }
            })
            .catch(err => console.log('Polling error:', err))
            .finally(() => {
                if (refreshIcon) refreshIcon.classList.remove('fa-spin');
                countdown = 5;
                if (timerEl) timerEl.textContent = countdown + 's';
            });
        }

        // Timer Tick
        function tick() {
            if (!toggleEl.checked) return;
            countdown--;
            if (countdown <= 0) {
                fetchLiveState();
            } else {
                if (timerEl) timerEl.textContent = countdown + 's';
            }
        }

        pollInterval = setInterval(tick, 1000);

        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                fetchLiveState();
            });
        }
    })();
</script>
@endsection
