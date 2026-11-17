@extends('layouts.admin')

@section('title', 'WinGo Color Game - Live Risk & Override Control')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-gamepad text-primary me-2"></i> WinGo Live Control Hub
        </h2>
        <p class="text-muted small mb-0">Real-time risk monitor, exposure calculation, and 1-click winner manipulation.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.wingo.winners') }}" class="btn btn-outline-success fw-bold rounded-pill px-3">
            <i class="fa-solid fa-trophy me-1"></i> Winner List
        </a>
        <a href="{{ route('wingo.index') }}?type={{ $gameType }}" target="_blank" class="btn btn-primary fw-bold rounded-pill px-3">
            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Player Game
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Game Mode Switching Tabs -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-2">
        <div class="nav nav-pills nav-fill gap-2">
            <a href="?type=30s" class="nav-link py-2 fw-bold rounded-3 {{ $gameType === '30s' ? 'active bg-primary' : 'text-dark' }}">
                <i class="fa-solid fa-stopwatch me-1"></i> WinGo 30s
            </a>
            <a href="?type=1m" class="nav-link py-2 fw-bold rounded-3 {{ $gameType === '1m' ? 'active bg-primary' : 'text-dark' }}">
                <i class="fa-solid fa-clock me-1"></i> WinGo 1 Min
            </a>
            <a href="?type=3m" class="nav-link py-2 fw-bold rounded-3 {{ $gameType === '3m' ? 'active bg-primary' : 'text-dark' }}">
                <i class="fa-regular fa-clock me-1"></i> WinGo 3 Min
            </a>
            <a href="?type=5m" class="nav-link py-2 fw-bold rounded-3 {{ $gameType === '5m' ? 'active bg-primary' : 'text-dark' }}">
                <i class="fa-solid fa-hourglass-half me-1"></i> WinGo 5 Min
            </a>
        </div>
    </div>
</div>

<!-- Live Period Status Card -->
<div class="row g-3 mb-4">
    <!-- Active Period Summary -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary text-uppercase px-3 py-2 fs-6">WinGo {{ $gameType }}</span>
                        <span class="badge bg-light text-dark border px-3 py-2 fs-6 font-monospace">
                            Period #<span id="adminPeriodId">{{ $periodData['period_id'] }}</span>
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge {{ $periodData['is_locked'] ? 'bg-danger' : 'bg-success' }} px-3 py-2 fs-6" id="adminStatusBadge">
                            <i class="fa-solid {{ $periodData['is_locked'] ? 'fa-lock' : 'fa-spinner fa-spin' }} me-1"></i>
                            {{ $periodData['is_locked'] ? 'Locked (Drawing)' : 'Betting Open' }}
                        </span>
                    </div>
                </div>

                <div class="row g-3 text-center my-2">
                    <div class="col-md-3 col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small fw-bold text-uppercase">Time Left</div>
                            <div class="fs-2 fw-bold font-monospace text-primary" id="adminCountdownDisplay">
                                {{ $periodData['remaining_seconds'] }}s
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small fw-bold text-uppercase">Total Bets</div>
                            <div class="fs-2 fw-bold font-monospace text-dark">
                                {{ $activeBets->count() }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small fw-bold text-uppercase">Pool Collected</div>
                            <div class="fs-2 fw-bold font-monospace text-success">
                                ₹{{ number_format($period->total_bet_amount, 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small fw-bold text-uppercase">Best Profit No.</div>
                            <div class="fs-2 fw-bold font-monospace text-warning">
                                #{{ $safestNumber }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Control Status -->
                <div class="mt-4 p-3 rounded-3 border {{ $period->is_manual ? 'bg-warning bg-opacity-10 border-warning' : 'bg-light' }} d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <div class="fw-bold text-dark">
                            Current Draw Strategy:
                            @if($period->is_manual)
                                @if($period->manual_number !== null)
                                    <span class="badge bg-warning text-dark fs-6 ms-1">
                                        <i class="fa-solid fa-lock me-1"></i> Exact Ball: #{{ $period->manual_number }}
                                        @if($period->manual_color) ({{ strtoupper($period->manual_color) }} - {{ strtoupper($period->manual_size) }}) @endif
                                    </span>
                                @elseif($period->manual_color && $period->manual_size)
                                    <span class="badge bg-warning text-dark fs-6 ms-1">
                                        <i class="fa-solid fa-lock me-1"></i> Target: {{ strtoupper($period->manual_color) }} + {{ strtoupper($period->manual_size) }}
                                    </span>
                                @elseif($period->manual_color)
                                    <span class="badge {{ $period->manual_color === 'green' ? 'bg-success' : ($period->manual_color === 'red' ? 'bg-danger' : 'bg-secondary') }} text-white fs-6 ms-1">
                                        <i class="fa-solid fa-lock me-1"></i> Force Color: {{ strtoupper($period->manual_color) }}
                                    </span>
                                @elseif($period->manual_size)
                                    <span class="badge {{ $period->manual_size === 'big' ? 'bg-warning text-dark' : 'bg-info text-dark' }} fs-6 ms-1">
                                        <i class="fa-solid fa-lock me-1"></i> Force Size: {{ strtoupper($period->manual_size) }}
                                    </span>
                                @endif
                            @else
                                <span class="badge bg-info text-dark fs-6 ms-1">
                                    <i class="fa-solid fa-robot me-1"></i> Automated (Least-Payout Algorithm)
                                </span>
                            @endif
                        </div>
                        <div class="small text-muted mt-1">
                            @if($period->is_manual)
                                @if($period->manual_number !== null)
                                    Period will draw ball #{{ $period->manual_number }} ({{ strtoupper($period->manual_color) }} - {{ strtoupper($period->manual_size) }}) precisely.
                                @else
                                    Period will draw from matching numbers using Least-Payout to maximize house profit.
                                @endif
                            @else
                                Algorithm will automatically pick the number giving maximum house profit (Lowest Payout).
                            @endif
                        </div>
                    </div>

                    @if($period->is_manual)
                        <form method="POST" action="{{ route('admin.wingo.clear-manual') }}">
                            @csrf
                            <input type="hidden" name="game_type" value="{{ $gameType }}">
                            <input type="hidden" name="period_id" value="{{ $periodData['period_id'] }}">
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill fw-bold px-3">
                                <i class="fa-solid fa-rotate-left me-1"></i> Reset to Auto Mode
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Master Full Control Override Card (Color, Size, Combination, & Number) -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-sliders text-danger me-2"></i> Master Result Controller
                    </h5>
                    <p class="text-muted small mb-0">Full control: Color, Big/Small, Combos, or Exact Ball # for Period #{{ $periodData['period_id'] }}</p>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Active Lock Status Bar -->
                <div class="p-3 mb-3 rounded-3 {{ $period->is_manual ? 'bg-warning bg-opacity-10 border border-warning' : 'bg-light border' }} d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem;">Current Locked Target:</div>
                        <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
                            @if($period->is_manual)
                                @if($period->manual_number !== null)
                                    <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill shadow-sm">
                                        <i class="fa-solid fa-hashtag me-1"></i> Ball #{{ $period->manual_number }} LOCKED (Only Number)
                                    </span>
                                @elseif($period->manual_color && $period->manual_size)
                                    <span class="badge bg-info text-dark fs-6 px-3 py-2 rounded-pill shadow-sm">
                                        <i class="fa-solid fa-code-merge me-1"></i> Combo: {{ strtoupper($period->manual_color) }} + {{ strtoupper($period->manual_size) }} LOCKED
                                    </span>
                                @elseif($period->manual_color)
                                    @php
                                        $colorBg = match($period->manual_color) {
                                            'green' => 'bg-success',
                                            'red' => 'bg-danger',
                                            'violet' => 'bg-dark',
                                            default => 'bg-primary'
                                        };
                                        $violetStyle = ($period->manual_color === 'violet') ? 'background: #8b5cf6 !important;' : '';
                                    @endphp
                                    <span class="badge {{ $colorBg }} fs-6 px-3 py-2 rounded-pill shadow-sm" style="{{ $violetStyle }}">
                                        <i class="fa-solid fa-palette me-1"></i> Color: {{ strtoupper($period->manual_color) }} LOCKED (Only Color)
                                    </span>
                                @elseif($period->manual_size)
                                    <span class="badge {{ $period->manual_size === 'big' ? 'bg-warning text-dark' : 'bg-primary' }} fs-6 px-3 py-2 rounded-pill shadow-sm">
                                        <i class="fa-solid fa-arrows-up-down me-1"></i> Size: {{ strtoupper($period->manual_size) }} LOCKED (Only Size)
                                    </span>
                                @endif
                                @if($period->manual_number === null && ($period->manual_color || $period->manual_size))
                                    <span class="badge bg-white text-muted border small">Auto Safest Ball Draw</span>
                                @endif
                            @else
                                <span class="badge bg-secondary fs-6 px-3 py-2 rounded-pill"><i class="fa-solid fa-robot me-1"></i> Auto Pilot Active (Least House Payout)</span>
                            @endif
                        </div>
                    </div>
                    @if($period->is_manual)
                        <form method="POST" action="{{ route('admin.wingo.clear-manual') }}" class="m-0">
                            @csrf
                            <input type="hidden" name="game_type" value="{{ $gameType }}">
                            <input type="hidden" name="period_id" value="{{ $periodData['period_id'] }}">
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">
                                <i class="fa-solid fa-rotate-left me-1"></i> Reset Auto
                            </button>
                        </form>
                    @endif
                </div>

                <!-- 1. Color Control Section (Sirf Color Lock Hoga) -->
                <div class="mb-3">
                    <div class="text-uppercase fw-bold text-muted small mb-2 d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-palette text-primary me-1"></i> 1. Force Color Winner <small class="text-secondary text-lowercase fw-normal">(sirf color lock hoga, number nahi)</small></span>
                        <span class="badge bg-light text-muted border">Auto Safest Number</span>
                    </div>
                    <form method="POST" action="{{ route('admin.wingo.manual-target') }}" class="d-flex gap-2">
                        @csrf
                        <input type="hidden" name="game_type" value="{{ $gameType }}">
                        <input type="hidden" name="period_id" value="{{ $periodData['period_id'] }}">
                        <input type="hidden" name="target_mode" value="color">

                        @php $isColorGreen = ($period->is_manual && $period->manual_color === 'green' && $period->manual_size === null && $period->manual_number === null); @endphp
                        <button type="submit" name="manual_color" value="green" class="btn {{ $isColorGreen ? 'btn-success fw-bold shadow ring-2' : 'btn-outline-success' }} flex-grow-1 py-2 rounded-3 fw-bold">
                            <i class="fa-solid fa-circle me-1"></i> Green
                            @if($isColorGreen)
                                <span class="badge bg-white text-success ms-1">LOCKED</span>
                            @endif
                        </button>

                        @php $isColorViolet = ($period->is_manual && $period->manual_color === 'violet' && $period->manual_size === null && $period->manual_number === null); @endphp
                        <button type="submit" name="manual_color" value="violet" class="btn {{ $isColorViolet ? 'btn-dark fw-bold shadow' : 'btn-outline-dark' }} py-2 px-3 rounded-3 fw-bold" style="background: {{ $isColorViolet ? '#8b5cf6' : 'transparent' }}; border-color: #8b5cf6; color: {{ $isColorViolet ? '#fff' : '#8b5cf6' }};">
                            <i class="fa-solid fa-circle me-1"></i> Violet
                            @if($isColorViolet)
                                <span class="badge bg-white text-dark ms-1">LOCKED</span>
                            @endif
                        </button>

                        @php $isColorRed = ($period->is_manual && $period->manual_color === 'red' && $period->manual_size === null && $period->manual_number === null); @endphp
                        <button type="submit" name="manual_color" value="red" class="btn {{ $isColorRed ? 'btn-danger fw-bold shadow' : 'btn-outline-danger' }} flex-grow-1 py-2 rounded-3 fw-bold">
                            <i class="fa-solid fa-circle me-1"></i> Red
                            @if($isColorRed)
                                <span class="badge bg-white text-danger ms-1">LOCKED</span>
                            @endif
                        </button>
                    </form>
                </div>

                <!-- 1b. Size Control Section (Sirf Big / Small Lock Hoga) -->
                <div class="mb-3">
                    <div class="text-uppercase fw-bold text-muted small mb-2 d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-arrows-up-down text-warning me-1"></i> 1b. Force Size Winner <small class="text-secondary text-lowercase fw-normal">(sirf big/small lock hoga, number nahi)</small></span>
                        <span class="badge bg-light text-muted border">Auto Safest Number</span>
                    </div>
                    <form method="POST" action="{{ route('admin.wingo.manual-target') }}" class="d-flex gap-2">
                        @csrf
                        <input type="hidden" name="game_type" value="{{ $gameType }}">
                        <input type="hidden" name="period_id" value="{{ $periodData['period_id'] }}">
                        <input type="hidden" name="target_mode" value="size">

                        @php $isSizeBig = ($period->is_manual && $period->manual_size === 'big' && $period->manual_color === null && $period->manual_number === null); @endphp
                        <button type="submit" name="manual_size" value="big" class="btn {{ $isSizeBig ? 'btn-warning text-dark fw-bold shadow ring-2' : 'btn-outline-warning text-dark' }} flex-grow-1 py-2 rounded-3 fw-bold">
                            <i class="fa-solid fa-arrow-trend-up me-1"></i> Big (5–9)
                            @if($isSizeBig)
                                <span class="badge bg-dark text-warning ms-1">LOCKED</span>
                            @endif
                        </button>

                        @php $isSizeSmall = ($period->is_manual && $period->manual_size === 'small' && $period->manual_color === null && $period->manual_number === null); @endphp
                        <button type="submit" name="manual_size" value="small" class="btn {{ $isSizeSmall ? 'btn-primary fw-bold shadow ring-2' : 'btn-outline-primary' }} flex-grow-1 py-2 rounded-3 fw-bold">
                            <i class="fa-solid fa-arrow-trend-down me-1"></i> Small (0–4)
                            @if($isSizeSmall)
                                <span class="badge bg-white text-primary ms-1">LOCKED</span>
                            @endif
                        </button>
                    </form>
                </div>

                <!-- 2. Color + Size Combinations (Includes Violet + Big & Violet + Small) -->
                <div class="mb-3">
                    <div class="text-uppercase fw-bold text-muted small mb-2 d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-code-merge text-success me-1"></i> 2. Force Color + Size Combination <small class="text-secondary text-lowercase fw-normal">(color + size dono lock honge)</small></span>
                        <span class="badge bg-light text-muted border">6 Combinations</span>
                    </div>
                    <div class="row g-2">
                        <!-- Green + Big -->
                        <div class="col-md-4 col-6">
                            <form method="POST" action="{{ route('admin.wingo.manual-target') }}">
                                @csrf
                                <input type="hidden" name="game_type" value="{{ $gameType }}">
                                <input type="hidden" name="period_id" value="{{ $periodData['period_id'] }}">
                                <input type="hidden" name="target_mode" value="combination">
                                <input type="hidden" name="manual_color" value="green">
                                <input type="hidden" name="manual_size" value="big">
                                @php $isGB = ($period->is_manual && $period->manual_color === 'green' && $period->manual_size === 'big' && $period->manual_number === null); @endphp
                                <button type="submit" class="btn {{ $isGB ? 'btn-success fw-bold shadow ring-2' : 'btn-outline-success' }} w-100 py-2 rounded-3 text-start d-flex align-items-center justify-content-between">
                                    <span>🟢 Green + Big</span>
                                    @if($isGB) <span class="badge bg-white text-success">LOCKED</span> @endif
                                </button>
                            </form>
                        </div>
                        <!-- Green + Small -->
                        <div class="col-md-4 col-6">
                            <form method="POST" action="{{ route('admin.wingo.manual-target') }}">
                                @csrf
                                <input type="hidden" name="game_type" value="{{ $gameType }}">
                                <input type="hidden" name="period_id" value="{{ $periodData['period_id'] }}">
                                <input type="hidden" name="target_mode" value="combination">
                                <input type="hidden" name="manual_color" value="green">
                                <input type="hidden" name="manual_size" value="small">
                                @php $isGS = ($period->is_manual && $period->manual_color === 'green' && $period->manual_size === 'small' && $period->manual_number === null); @endphp
                                <button type="submit" class="btn {{ $isGS ? 'btn-success fw-bold shadow ring-2' : 'btn-outline-success' }} w-100 py-2 rounded-3 text-start d-flex align-items-center justify-content-between">
                                    <span>🟢 Green + Small</span>
                                    @if($isGS) <span class="badge bg-white text-success">LOCKED</span> @endif
                                </button>
                            </form>
                        </div>
                        <!-- Red + Big -->
                        <div class="col-md-4 col-6">
                            <form method="POST" action="{{ route('admin.wingo.manual-target') }}">
                                @csrf
                                <input type="hidden" name="game_type" value="{{ $gameType }}">
                                <input type="hidden" name="period_id" value="{{ $periodData['period_id'] }}">
                                <input type="hidden" name="target_mode" value="combination">
                                <input type="hidden" name="manual_color" value="red">
                                <input type="hidden" name="manual_size" value="big">
                                @php $isRB = ($period->is_manual && $period->manual_color === 'red' && $period->manual_size === 'big' && $period->manual_number === null); @endphp
                                <button type="submit" class="btn {{ $isRB ? 'btn-danger fw-bold shadow ring-2' : 'btn-outline-danger' }} w-100 py-2 rounded-3 text-start d-flex align-items-center justify-content-between">
                                    <span>🔴 Red + Big</span>
                                    @if($isRB) <span class="badge bg-white text-danger">LOCKED</span> @endif
                                </button>
                            </form>
                        </div>
                        <!-- Red + Small -->
                        <div class="col-md-4 col-6">
                            <form method="POST" action="{{ route('admin.wingo.manual-target') }}">
                                @csrf
                                <input type="hidden" name="game_type" value="{{ $gameType }}">
                                <input type="hidden" name="period_id" value="{{ $periodData['period_id'] }}">
                                <input type="hidden" name="target_mode" value="combination">
                                <input type="hidden" name="manual_color" value="red">
                                <input type="hidden" name="manual_size" value="small">
                                @php $isRS = ($period->is_manual && $period->manual_color === 'red' && $period->manual_size === 'small' && $period->manual_number === null); @endphp
                                <button type="submit" class="btn {{ $isRS ? 'btn-danger fw-bold shadow ring-2' : 'btn-outline-danger' }} w-100 py-2 rounded-3 text-start d-flex align-items-center justify-content-between">
                                    <span>🔴 Red + Small</span>
                                    @if($isRS) <span class="badge bg-white text-danger">LOCKED</span> @endif
                                </button>
                            </form>
                        </div>
                        <!-- Violet + Big -->
                        <div class="col-md-4 col-6">
                            <form method="POST" action="{{ route('admin.wingo.manual-target') }}">
                                @csrf
                                <input type="hidden" name="game_type" value="{{ $gameType }}">
                                <input type="hidden" name="period_id" value="{{ $periodData['period_id'] }}">
                                <input type="hidden" name="target_mode" value="combination">
                                <input type="hidden" name="manual_color" value="violet">
                                <input type="hidden" name="manual_size" value="big">
                                @php $isVB = ($period->is_manual && $period->manual_color === 'violet' && $period->manual_size === 'big' && $period->manual_number === null); @endphp
                                <button type="submit" class="btn w-100 py-2 rounded-3 text-start d-flex align-items-center justify-content-between fw-bold" style="background: {{ $isVB ? '#8b5cf6' : 'transparent' }}; border: 1px solid #8b5cf6; color: {{ $isVB ? '#fff' : '#8b5cf6' }};">
                                    <span>🟣 Violet + Big</span>
                                    @if($isVB) <span class="badge bg-white text-dark">LOCKED</span> @endif
                                </button>
                            </form>
                        </div>
                        <!-- Violet + Small -->
                        <div class="col-md-4 col-6">
                            <form method="POST" action="{{ route('admin.wingo.manual-target') }}">
                                @csrf
                                <input type="hidden" name="game_type" value="{{ $gameType }}">
                                <input type="hidden" name="period_id" value="{{ $periodData['period_id'] }}">
                                <input type="hidden" name="target_mode" value="combination">
                                <input type="hidden" name="manual_color" value="violet">
                                <input type="hidden" name="manual_size" value="small">
                                @php $isVS = ($period->is_manual && $period->manual_color === 'violet' && $period->manual_size === 'small' && $period->manual_number === null); @endphp
                                <button type="submit" class="btn w-100 py-2 rounded-3 text-start d-flex align-items-center justify-content-between fw-bold" style="background: {{ $isVS ? '#8b5cf6' : 'transparent' }}; border: 1px solid #8b5cf6; color: {{ $isVS ? '#fff' : '#8b5cf6' }};">
                                    <span>🟣 Violet + Small</span>
                                    @if($isVS) <span class="badge bg-white text-dark">LOCKED</span> @endif
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- 3. Direct Exact Number Lock (0 to 9) (Sirf Number Lock Hoga, Neutral Dark Boxes) -->
                <div>
                    <div class="text-uppercase fw-bold text-muted small mb-2 d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-hashtag text-dark me-1"></i> 3. Lock Exact Ball Number <small class="text-secondary text-lowercase fw-normal">(sirf ball number lock hoga, color nahi)</small></span>
                        <span class="badge bg-light text-muted border">Direct Winner</span>
                    </div>
                    <form method="POST" action="{{ route('admin.wingo.manual-target') }}">
                        @csrf
                        <input type="hidden" name="game_type" value="{{ $gameType }}">
                        <input type="hidden" name="period_id" value="{{ $periodData['period_id'] }}">
                        <input type="hidden" name="target_mode" value="number">

                        <div class="d-grid grid-template-columns-5 gap-2 mb-2" style="display: grid; grid-template-columns: repeat(5, 1fr);">
                            @for($i = 0; $i <= 9; $i++)
                                @php
                                    $isSelected = ($period->is_manual && $period->manual_number === $i);
                                @endphp
                                <button type="submit" name="manual_number" value="{{ $i }}" class="btn {{ $isSelected ? 'btn-dark active fw-bolder shadow ring-2 text-white' : 'btn-outline-secondary text-dark' }} py-2 rounded-3 fs-4 fw-bold position-relative" style="{{ $isSelected ? 'background:#1e293b; border-color:#0f172a;' : 'border-color:#cbd5e1;' }}">
                                    {{ $i }}
                                    @if($isSelected)
                                        <div style="font-size: 0.55rem; font-weight: 900;" class="text-warning">LOCKED</div>
                                    @endif
                                </button>
                            @endfor
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 10 Numbers Risk & Exposure Matrix Table -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
        <div>
            <h5 class="fw-bold text-dark mb-0">
                <i class="fa-solid fa-table-list text-primary me-2"></i> Numbers 0–9 Risk & Exposure Matrix
            </h5>
            <span class="text-muted small">Calculated liability for Period #{{ $periodData['period_id'] }}</span>
        </div>
        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2">
            Recommended: Ball #{{ $safestNumber }} (Min Payout: ₹{{ number_format($exposure[$safestNumber]['payout'] ?? 0, 2) }})
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Number</th>
                        <th>Color</th>
                        <th>Size</th>
                        <th class="text-center">Winning Bets Count</th>
                        <th class="text-end">Total Payout Liability</th>
                        <th class="text-end">Net House Profit</th>
                        <th class="text-center pe-4">Manual Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exposure as $num => $exp)
                        @php
                            $isSafest = ($num === $safestNumber);
                            $isTargetManual = ($period->is_manual && $period->manual_number === $num);
                            $netProfit = $exp['net_profit'];
                        @endphp
                        <tr class="{{ $isTargetManual ? 'table-warning' : ($isSafest ? 'table-success bg-opacity-25' : '') }}">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-dark rounded-circle fs-6 p-2" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                                        {{ $num }}
                                    </span>
                                    @if($isTargetManual)
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-lock me-1"></i> Targeted</span>
                                    @endif
                                    @if($isSafest)
                                        <span class="badge bg-success"><i class="fa-solid fa-crown me-1"></i> Best Margin</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @php $expColor = strtolower($exp['color'] ?? ''); @endphp
                                @if(str_contains($expColor, 'green'))
                                    <span class="badge bg-success">Green</span>
                                @endif
                                @if(str_contains($expColor, 'violet'))
                                    <span class="badge" style="background:#8b5cf6; color:white;">Violet</span>
                                @endif
                                @if(str_contains($expColor, 'red'))
                                    <span class="badge bg-danger">Red</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ strtolower($exp['size']) === 'big' ? 'bg-warning text-dark' : 'bg-primary' }}">
                                    {{ strtoupper($exp['size']) }}
                                </span>
                            </td>
                            <td class="text-center fw-bold font-monospace">
                                {{ $exp['count'] }}
                            </td>
                            <td class="text-end font-monospace fw-bold text-danger">
                                ₹{{ number_format($exp['payout'], 2) }}
                            </td>
                            <td class="text-end font-monospace fw-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $netProfit >= 0 ? '+' : '' }}₹{{ number_format($netProfit, 2) }}
                            </td>
                            <td class="text-center pe-4">
                                <form method="POST" action="{{ route('admin.wingo.manual-number') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="game_type" value="{{ $gameType }}">
                                    <input type="hidden" name="period_id" value="{{ $periodData['period_id'] }}">
                                    <input type="hidden" name="manual_number" value="{{ $num }}">
                                    @if($isTargetManual)
                                        <button type="button" class="btn btn-warning btn-sm fw-bold rounded-pill px-3" disabled>
                                            <i class="fa-solid fa-check me-1"></i> Active Winner
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-outline-primary btn-sm fw-bold rounded-pill px-3">
                                            Lock as Winner
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Active Bets Placed in this Current Round -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-ticket text-info me-2"></i> Current Round Live Bets ({{ $activeBets->count() }})
                </h5>
                <span class="badge bg-light text-dark border">Period #{{ $periodData['period_id'] }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 380px;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Player</th>
                                <th>Selection</th>
                                <th>Unit × Mult</th>
                                <th class="text-end pe-3">Bet Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeBets as $b)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold text-dark">{{ $b->user->name ?? 'User' }}</div>
                                        <div class="text-muted small">{{ $b->user->phone ?? '' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-dark">{{ strtoupper($b->bet_type) }}: {{ $b->bet_selection }}</span>
                                    </td>
                                    <td class="font-monospace small">
                                        ₹{{ number_format($b->unit_price, 2) }} × {{ $b->multiplier }}
                                    </td>
                                    <td class="text-end pe-3 font-monospace fw-bold text-primary">
                                        ₹{{ number_format($b->total_amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No bets placed yet for this round.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Completed Rounds History -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-clock-rotate-left text-secondary me-2"></i> Recent {{ $recentRounds->count() }} Completed Rounds
                </h5>
                <span class="badge bg-light text-dark border">WinGo {{ $gameType }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-3">Period</th>
                                <th class="text-center">Winner</th>
                                <th class="text-center">Mode</th>
                                <th class="text-end">Pool</th>
                                <th class="text-end pe-3">Payout</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRounds as $rr)
                                <tr>
                                    <td class="ps-3 font-monospace text-muted small">
                                        {{ $rr->period_id }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-dark px-2 py-1 fs-6">
                                            #{{ $rr->winning_number }}
                                        </span>
                                        <div class="small text-muted">{{ $rr->winning_color }} • {{ $rr->winning_size }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($rr->is_manual)
                                            <span class="badge bg-warning text-dark">Manual</span>
                                        @else
                                            <span class="badge bg-light text-secondary border">Auto</span>
                                        @endif
                                    </td>
                                    <td class="text-end font-monospace text-success fw-bold">
                                        ₹{{ number_format($rr->total_bet_amount, 2) }}
                                    </td>
                                    <td class="text-end pe-3 font-monospace text-danger fw-bold">
                                        ₹{{ number_format($rr->total_payout_amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No completed rounds recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Live polling for admin page timer
    let remaining = {{ $periodData['remaining_seconds'] }};
    const gameType = '{{ $gameType }}';
    let currentPeriod = '{{ $periodData['period_id'] }}';

    setInterval(() => {
        if (remaining > 0) {
            remaining--;
            document.getElementById('adminCountdownDisplay').textContent = remaining + 's';
        }
    }, 1000);

    // Auto-refresh data when round finishes
    setInterval(async () => {
        try {
            const res = await fetch(`/api/wingo/status?type=${gameType}`);
            const data = await res.json();
            if (data.success) {
                if (data.period_id !== currentPeriod) {
                    // New round started -> reload admin page to show fresh bets & exposure
                    window.location.reload();
                }
                remaining = data.remaining_seconds;
                document.getElementById('adminCountdownDisplay').textContent = remaining + 's';
                
                const badge = document.getElementById('adminStatusBadge');
                if (data.is_locked) {
                    badge.className = 'badge bg-danger px-3 py-2 fs-6';
                    badge.innerHTML = '<i class="fa-solid fa-lock me-1"></i> Locked (Drawing)';
                } else {
                    badge.className = 'badge bg-success px-3 py-2 fs-6';
                    badge.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Betting Open';
                }
            }
        } catch(e) {}
    }, 1500);
</script>
@endsection
