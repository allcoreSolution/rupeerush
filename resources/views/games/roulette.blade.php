@extends('layouts.app')

@section('title', 'European Live Roulette - ' . ($setting->site_name ?? 'rupeerush'))

@section('styles')
<style>
    /* ----------------------------------------------------
       EUROPEAN ROULETTE CASINO SUITE
       Deep Velvet Emerald Felt, Mahogany Wood & Gold Bevels
       ---------------------------------------------------- */
    :root {
        --rl-bg: #060b17;
        --rl-felt: #0c3d26;
        --rl-felt-dark: #072417;
        --rl-felt-light: #125435;
        --rl-gold: #f59e0b;
        --rl-gold-glow: rgba(245, 158, 11, 0.45);
        --rl-red: #dc2626;
        --rl-black: #111827;
        --rl-green: #10b981;
        --rl-border: #1e2e5c;
    }

    body {
        background-color: var(--rl-bg);
        color: #f1f5f9;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .rl-container {
        max-width: 1280px;
        margin: 0 auto;
        padding-bottom: 70px;
    }

    /* Top Live Status Bar */
    .rl-topbar {
        background: linear-gradient(135deg, rgba(13, 22, 48, 0.95) 0%, rgba(7, 12, 28, 0.98) 100%);
        border: 1px solid rgba(245, 158, 11, 0.25);
        border-radius: 18px;
        padding: 10px 18px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
    }

    .rl-round-badge {
        background: rgba(0, 0, 0, 0.6);
        border: 1px solid rgba(245, 158, 11, 0.4);
        border-radius: 50px;
        padding: 4px 14px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.85rem;
        color: #fde047;
    }

    /* Circular Live Timer */
    .rl-timer-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #090f22;
        border: 1px solid var(--rl-border);
        border-radius: 50px;
        padding: 4px 14px;
        font-weight: 800;
        font-size: 0.92rem;
    }
    .rl-timer-val {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.15rem;
        color: #38bdf8;
    }
    .rl-timer-val.warning {
        color: #ef4444;
        animation: timerBlink 0.6s infinite alternate;
    }
    @keyframes timerBlink {
        from { opacity: 1; transform: scale(1); }
        to { opacity: 0.5; transform: scale(1.1); }
    }

    /* Recent Winning Numbers Strip */
    .recent-strip {
        display: flex;
        align-items: center;
        gap: 6px;
        overflow-x: auto;
        padding: 4px 0;
        scrollbar-width: none;
    }
    .recent-strip::-webkit-scrollbar { display: none; }

    .num-pill {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        font-size: 0.82rem;
        color: #ffffff;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.5);
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }
    .num-pill:hover { transform: scale(1.15); }
    .num-pill.red { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); border: 1px solid #f87171; }
    .num-pill.black { background: linear-gradient(135deg, #374151 0%, #111827 100%); border: 1px solid #6b7280; }
    .num-pill.green { background: linear-gradient(135deg, #10b981 0%, #047857 100%); border: 1px solid #34d399; }

    /* Main Arena Layout */
    .rl-cabinet {
        background: linear-gradient(180deg, #0d1630 0%, #070c1b 100%);
        border: 1px solid var(--rl-border);
        border-radius: 24px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.65), inset 0 1px 0 rgba(255, 255, 255, 0.08);
        padding: 20px;
        position: relative;
        overflow: hidden;
    }

    /* 3D Wheel Stage */
    .wheel-stage-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .wheel-outer-wood {
        width: 320px;
        height: 320px;
        max-width: 100%;
        border-radius: 50%;
        background: radial-gradient(circle, #3d1c0a 0%, #200d04 70%, #120601 100%);
        border: 8px solid #4a230d;
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.9), inset 0 0 25px rgba(0, 0, 0, 0.8), 0 0 0 2px var(--rl-gold);
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    #rouletteCanvas {
        border-radius: 50%;
        display: block;
        box-shadow: inset 0 0 15px rgba(0, 0, 0, 0.8);
    }

    /* Winning Number Reveal Banner */
    .win-reveal-card {
        display: none;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 40;
        background: rgba(7, 12, 28, 0.92);
        border: 2px solid var(--rl-gold);
        border-radius: 20px;
        padding: 16px 24px;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.9), 0 0 30px var(--rl-gold-glow);
        animation: zoomWin 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    @keyframes zoomWin {
        from { transform: translate(-50%, -50%) scale(0.6); opacity: 0; }
        to { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    }

    /* Green Felt Betting Board */
    .felt-board {
        background: radial-gradient(ellipse at 50% 50%, var(--rl-felt-light) 0%, var(--rl-felt) 60%, var(--rl-felt-dark) 100%);
        border: 4px solid #2b1408;
        outline: 2px solid var(--rl-gold);
        border-radius: 18px;
        padding: 14px;
        box-shadow: inset 0 0 50px rgba(0, 0, 0, 0.7), 0 12px 30px rgba(0, 0, 0, 0.6);
        user-select: none;
        overflow-x: auto;
    }

    /* Numbers Grid Table */
    .roulette-grid {
        display: grid;
        grid-template-columns: 48px repeat(12, 1fr) 52px;
        grid-template-rows: repeat(3, 44px);
        gap: 3px;
        min-width: 580px;
    }

    .zero-spot {
        grid-row: 1 / span 3;
        grid-column: 1;
        background: linear-gradient(135deg, #10b981 0%, #047857 100%);
        border: 1.5px solid #34d399;
        border-radius: 10px 0 0 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        transition: all 0.15s ease;
    }

    .num-spot {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        font-size: 1.05rem;
        cursor: pointer;
        position: relative;
        border-radius: 6px;
        transition: transform 0.1s ease, box-shadow 0.1s ease;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
    .num-spot:hover, .zero-spot:hover, .outside-spot:hover {
        transform: scale(1.05);
        z-index: 10;
        box-shadow: 0 0 12px rgba(245, 158, 11, 0.8);
    }
    .num-spot.red {
        background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        color: #ffffff;
    }
    .num-spot.black {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        color: #ffffff;
    }

    .col-spot {
        background: rgba(15, 23, 42, 0.7);
        border: 1.5px solid rgba(255, 255, 255, 0.2);
        border-radius: 0 8px 8px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 800;
        color: #fde047;
        cursor: pointer;
        position: relative;
    }

    /* Outside Bets (Dozens & 1:1) */
    .outside-grid-dozens {
        display: grid;
        grid-template-columns: 48px repeat(3, 1fr) 52px;
        gap: 3px;
        margin-top: 4px;
        min-width: 580px;
    }
    .outside-grid-simple {
        display: grid;
        grid-template-columns: 48px repeat(6, 1fr) 52px;
        gap: 3px;
        margin-top: 4px;
        min-width: 580px;
    }

    .outside-spot {
        background: rgba(15, 23, 42, 0.75);
        border: 1.5px solid rgba(255, 255, 255, 0.2);
        border-radius: 6px;
        height: 38px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 0.78rem;
        font-weight: 800;
        cursor: pointer;
        position: relative;
        transition: all 0.15s ease;
        text-transform: uppercase;
        color: #f1f5f9;
    }
    .outside-spot.red-box {
        background: linear-gradient(135deg, #dc2626 0%, #7f1d1d 100%);
        color: #fff;
    }
    .outside-spot.black-box {
        background: linear-gradient(135deg, #1f2937 0%, #030712 100%);
        color: #fff;
    }

    /* Placed Chip Badge on Table Spot */
    .chip-badge {
        position: absolute;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: radial-gradient(circle, #fde047 0%, #d97706 100%);
        color: #000;
        border: 2px dashed #ffffff;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 900;
        font-size: 0.65rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.7);
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
        z-index: 15;
        animation: chipDrop 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    @keyframes chipDrop {
        from { transform: translate(-50%, -80%) scale(1.4); opacity: 0; }
        to { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    }

    /* Chips Tray & Action Deck */
    .chip-tray {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 10px 0;
        flex-wrap: wrap;
    }

    .casino-chip {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 900;
        font-size: 0.75rem;
        cursor: pointer;
        position: relative;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.6);
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        border: 3px dashed rgba(255, 255, 255, 0.6);
    }
    .casino-chip:hover { transform: translateY(-4px) scale(1.08); }
    .casino-chip.active {
        transform: translateY(-6px) scale(1.15);
        box-shadow: 0 0 16px var(--rl-gold-glow);
        border-color: #ffffff;
    }

    .chip-10 { background: radial-gradient(circle, #38bdf8 0%, #0369a1 100%); color: #fff; }
    .chip-50 { background: radial-gradient(circle, #fb923c 0%, #c2410c 100%); color: #fff; }
    .chip-100 { background: radial-gradient(circle, #c084fc 0%, #7e22ce 100%); color: #fff; }
    .chip-500 { background: radial-gradient(circle, #2dd4bf 0%, #0f766e 100%); color: #fff; }
    .chip-1000 { background: radial-gradient(circle, #fde047 0%, #b45309 100%); color: #000; }
    .chip-5000 { background: radial-gradient(circle, #f43f5e 0%, #881337 100%); color: #fff; }

    /* Action Buttons Bar */
    .actions-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-action-custom {
        border-radius: 12px;
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 0.88rem;
        padding: 9px 18px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-confirm-bets {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.45);
    }
    .btn-confirm-bets:hover:not(:disabled) {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-2px);
    }

    @media (max-width: 991px) {
        .rl-container { padding-bottom: 90px; }
        .wheel-outer-wood { width: 270px; height: 270px; }
        #rouletteCanvas { width: 250px; height: 250px; }
    }

    @media (max-width: 768px) {
        .container-xl { padding-left: 4px !important; padding-right: 4px !important; }
        .rl-container { padding: 4px 2px 40px !important; }
        .rl-topbar { padding: 6px 10px !important; margin-bottom: 6px !important; }
        .rl-cabinet { padding: 8px 6px !important; border-radius: 14px !important; }
        .wheel-outer-wood { width: 165px !important; height: 165px !important; border-width: 4px !important; }
        #rouletteCanvas { width: 152px !important; height: 152px !important; }
        .wheel-stage-container { margin-bottom: 4px !important; }
        .recent-strip .num-pill { width: 26px !important; height: 26px !important; font-size: 0.72rem !important; }
        .rl-timer-pill { padding: 2px 10px !important; font-size: 0.82rem !important; }
        .rl-timer-val { font-size: 0.95rem !important; }
        .num-spot { font-size: 0.85rem !important; }
        .zero-spot { font-size: 0.85rem !important; }
        .outside-spot { height: 32px !important; font-size: 0.72rem !important; }
        .col-spot { font-size: 0.7rem !important; }
        .chip-tray { gap: 6px !important; padding: 6px 0 !important; }
        .casino-chip { width: 38px !important; height: 38px !important; font-size: 0.7rem !important; }
        .actions-bar { gap: 4px !important; }
        .btn-action-custom { padding: 6px 10px !important; font-size: 0.78rem !important; }
    }
</style>
@endsection

@section('content')
<div class="container-xl py-3">
    <div class="rl-container">

        <!-- Top Status Bar -->
        <div class="rl-topbar d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-dark border border-secondary text-warning">
                    <i class="fa-solid fa-dharmachakra me-1"></i> European Roulette
                </span>
                <span class="rl-round-badge" id="roundIdDisplay">{{ $roundData['round_id'] }}</span>
                <span class="badge bg-dark border border-secondary text-secondary small d-none d-sm-inline-block">
                    <i class="fa-solid fa-shield-halved text-success me-1"></i> Provably Fair SHA-256
                </span>
            </div>

            <!-- Live Timer Indicator -->
            <div class="d-flex align-items-center gap-3">
                <div class="rl-timer-pill" id="timerContainer">
                    <span class="text-secondary small text-uppercase" id="timerLabel">
                        {{ ($roundData['phase'] ?? 'betting') === 'betting' ? 'BETS OPEN:' : (($roundData['phase'] ?? 'betting') === 'spinning' ? 'BETS CLOSED:' : 'RESULT OUT:') }}
                    </span>
                    <span class="rl-timer-val {{ ($roundData['phase'] ?? 'betting') === 'spinning' ? 'warning' : '' }}" id="countdownTimerVal">
                        {{ ($roundData['phase'] ?? 'betting') === 'betting' ? max(1, (int)($roundData['betting_seconds'] ?? 20)) : (($roundData['phase'] ?? 'betting') === 'spinning' ? max(1, (int)($roundData['spinning_seconds'] ?? 7)) : max(1, (int)($roundData['result_seconds'] ?? 3))) }}s
                    </span>
                </div>
                <button class="btn btn-sm text-secondary p-0" id="soundToggleBtn" onclick="toggleSound()" title="Sound On/Off">
                    <i class="fa-solid fa-volume-high fs-5 text-warning" id="soundIcon"></i>
                </button>
            </div>
        </div>

        <!-- Recent Winning Numbers Strip -->
        <div class="p-2 mb-3 rounded-3 d-flex align-items-center gap-3" style="background: rgba(13, 22, 48, 0.7); border: 1px solid rgba(255,255,255,0.06);">
            <div class="text-secondary small fw-bold text-uppercase d-none d-md-block" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                <i class="fa-solid fa-clock-rotate-left me-1 text-warning"></i> Recent:
            </div>
            <div class="recent-strip flex-fill" id="recentNumbersStrip">
                @forelse($recentHistory as $rh)
                    <div class="num-pill {{ $rh->winning_color }}" title="Round {{ $rh->round_id }}">
                        {{ $rh->winning_number }}
                    </div>
                @empty
                    <span class="text-secondary small">Waiting for first spin result...</span>
                @endforelse
            </div>
            <!-- Statistics Pct -->
            <div class="d-none d-lg-flex align-items-center gap-2 text-secondary small font-monospace">
                <span class="text-danger fw-bold">🔴 {{ $redPercent }}%</span>
                <span class="text-light fw-bold">⚫ {{ $blackPercent }}%</span>
                <span class="text-success fw-bold">🟢 {{ $greenPercent }}%</span>
            </div>
        </div>

        <!-- MAIN ARENA GRID (Left: 3D Wheel | Right: Felt Table & Chips) -->
        <div class="row g-4 align-items-start">
            
            <!-- LEFT COLUMN: 3D Wheel Stage & HUD -->
            <div class="col-12 col-lg-5 col-xl-4 text-center">
                <div class="rl-cabinet p-4">
                    <div class="wheel-stage-container">
                        <!-- Outer Mahogany Wood Rim -->
                        <div class="wheel-outer-wood">
                            <canvas id="rouletteCanvas" width="290" height="290"></canvas>
                        </div>

                        <!-- Win Overlay Announcement -->
                        <div class="win-reveal-card" id="winRevealCard">
                            <div class="text-uppercase small fw-bold text-secondary" id="winRevealTitle">WINNING NUMBER</div>
                            <div class="display-3 fw-bold font-monospace my-1" id="winRevealNumber">0</div>
                            <div class="badge fs-6 px-3 py-1" id="winRevealColorBadge">GREEN</div>
                        </div>
                    </div>

                    <!-- User Balance & Total Stake HUD -->
                    <div class="d-flex align-items-center justify-content-between p-2 mt-4 rounded-3" style="background: #090f20; border: 1px solid var(--rl-border);">
                        <div>
                            <span class="text-secondary small">Balance:</span>
                            <span class="fw-bold font-monospace text-info fs-6" id="userBalanceDisplay">
                                ₹{{ number_format((float)($user?->balance ?? 0), 2) }}
                            </span>
                        </div>
                        <div>
                            <span class="text-secondary small">Current Stake:</span>
                            <span class="fw-bold font-monospace text-warning fs-6" id="totalStakeDisplay">₹0</span>
                        </div>
                    </div>

                    <!-- Status Text Alert -->
                    <div class="mt-3 py-2 px-3 rounded-pill text-center font-monospace fw-bold" id="statusMessageAlert" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399; font-size: 0.85rem;">
                        <i class="fa-solid fa-circle-check me-1"></i> PLACE YOUR BETS
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Green Felt Betting Grid & Chips Tray -->
            <div class="col-12 col-lg-7 col-xl-8">
                <div class="rl-cabinet p-3">

                    <!-- Live Payout Rates Banner -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2 p-2 rounded-3" style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(255,255,255,0.08);">
                        <div class="small fw-bold text-white-50" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-calculator text-warning me-1"></i> PAYOUT MULTIPLIERS:
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2 py-1" style="font-size: 0.72rem;">
                                🟢 0 (Green): <strong class="text-white">36x</strong>
                            </span>
                            <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 px-2 py-1" style="font-size: 0.72rem;">
                                🔴 Red Numbers: <strong class="text-white">{{ (float)($betOdds['red'] ?? 5) }}x</strong>
                            </span>
                            <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-50 px-2 py-1" style="font-size: 0.72rem;">
                                ⚫ Black Numbers: <strong class="text-white">{{ (float)($betOdds['black'] ?? 7) }}x</strong>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Felt Board Table -->
                    <div class="felt-board">
                        <!-- Numbers Grid (0 on left, 1-36, 2:1 Col on right) -->
                        <div class="roulette-grid" id="rouletteGrid">
                            <!-- Zero Spot -->
                            <div class="zero-spot" onclick="handleSpotClick('straight', '0', this)" data-type="straight" data-value="0" title="Green Zero (36x)">
                                <span class="fw-bold text-white fs-4 font-monospace">0</span>
                                <span class="badge bg-dark text-warning p-1" style="font-size: 0.6rem;">36x</span>
                            </div>

                            <!-- Row 1: 3, 6, 9, 12, 15, 18, 21, 24, 27, 30, 33, 36 -->
                            @foreach([3, 6, 9, 12, 15, 18, 21, 24, 27, 30, 33, 36] as $n)
                                @php $isRed = in_array($n, $redNumbers); @endphp
                                <div class="num-spot {{ $isRed ? 'red' : 'black' }}" onclick="handleSpotClick('straight', '{{ $n }}', this)" data-type="straight" data-value="{{ $n }}" title="{{ $isRed ? 'Red #'.$n.' ('.((float)($betOdds['red'] ?? 5)).'x)' : 'Black #'.$n.' ('.((float)($betOdds['black'] ?? 7)).'x)' }}">
                                    <span>{{ $n }}</span>
                                    <small class="spot-mult-tag text-warning font-monospace" style="font-size: 0.58rem; line-height: 1; opacity: 0.9; font-weight: 700;">{{ $isRed ? ((float)($betOdds['red'] ?? 5)).'x' : ((float)($betOdds['black'] ?? 7)).'x' }}</small>
                                </div>
                            @endforeach
                            <div class="col-spot" onclick="handleSpotClick('col3', 'col3', this)" data-type="col3" data-value="col3">
                                <span>2:1</span>
                            </div>

                            <!-- Row 2: 2, 5, 8, 11, 14, 17, 20, 23, 26, 29, 32, 35 -->
                            @foreach([2, 5, 8, 11, 14, 17, 20, 23, 26, 29, 32, 35] as $n)
                                @php $isRed = in_array($n, $redNumbers); @endphp
                                <div class="num-spot {{ $isRed ? 'red' : 'black' }}" onclick="handleSpotClick('straight', '{{ $n }}', this)" data-type="straight" data-value="{{ $n }}" title="{{ $isRed ? 'Red #'.$n.' ('.((float)($betOdds['red'] ?? 5)).'x)' : 'Black #'.$n.' ('.((float)($betOdds['black'] ?? 7)).'x)' }}">
                                    <span>{{ $n }}</span>
                                    <small class="spot-mult-tag text-warning font-monospace" style="font-size: 0.58rem; line-height: 1; opacity: 0.9; font-weight: 700;">{{ $isRed ? ((float)($betOdds['red'] ?? 5)).'x' : ((float)($betOdds['black'] ?? 7)).'x' }}</small>
                                </div>
                            @endforeach
                            <div class="col-spot" onclick="handleSpotClick('col2', 'col2', this)" data-type="col2" data-value="col2">
                                <span>2:1</span>
                            </div>

                            <!-- Row 3: 1, 4, 7, 10, 13, 16, 19, 22, 25, 28, 31, 34 -->
                            @foreach([1, 4, 7, 10, 13, 16, 19, 22, 25, 28, 31, 34] as $n)
                                @php $isRed = in_array($n, $redNumbers); @endphp
                                <div class="num-spot {{ $isRed ? 'red' : 'black' }}" onclick="handleSpotClick('straight', '{{ $n }}', this)" data-type="straight" data-value="{{ $n }}" title="{{ $isRed ? 'Red #'.$n.' ('.((float)($betOdds['red'] ?? 5)).'x)' : 'Black #'.$n.' ('.((float)($betOdds['black'] ?? 7)).'x)' }}">
                                    <span>{{ $n }}</span>
                                    <small class="spot-mult-tag text-warning font-monospace" style="font-size: 0.58rem; line-height: 1; opacity: 0.9; font-weight: 700;">{{ $isRed ? ((float)($betOdds['red'] ?? 5)).'x' : ((float)($betOdds['black'] ?? 7)).'x' }}</small>
                                </div>
                            @endforeach
                            <div class="col-spot" onclick="handleSpotClick('col1', 'col1', this)" data-type="col1" data-value="col1">
                                <span>2:1</span>
                            </div>
                        </div>

                        <!-- Dozens Row (1st 12, 2nd 12, 3rd 12) -->
                        <div class="outside-grid-dozens">
                            <div></div>
                            <div class="outside-spot" onclick="handleSpotClick('dozen1', 'dozen1', this)" data-type="dozen1" data-value="dozen1">
                                <span>1st 12 (3x)</span>
                            </div>
                            <div class="outside-spot" onclick="handleSpotClick('dozen2', 'dozen2', this)" data-type="dozen2" data-value="dozen2">
                                <span>2nd 12 (3x)</span>
                            </div>
                            <div class="outside-spot" onclick="handleSpotClick('dozen3', 'dozen3', this)" data-type="dozen3" data-value="dozen3">
                                <span>3rd 12 (3x)</span>
                            </div>
                            <div></div>
                        </div>

                        <!-- 1:1 Outside Bets (1-18, Even, Red, Black, Odd, 19-36) -->
                        <div class="outside-grid-simple">
                            <div></div>
                            <div class="outside-spot" onclick="handleSpotClick('low', 'low', this)" data-type="low" data-value="low">
                                <span>1 to 18</span>
                            </div>
                            <div class="outside-spot" onclick="handleSpotClick('even', 'even', this)" data-type="even" data-value="even">
                                <span>EVEN</span>
                            </div>
                            <div class="outside-spot red-box" onclick="handleSpotClick('red', 'red', this)" data-type="red" data-value="red">
                                <span>RED 🔴 ({{ (float)($betOdds['red'] ?? 5) }}x)</span>
                            </div>
                            <div class="outside-spot black-box" onclick="handleSpotClick('black', 'black', this)" data-type="black" data-value="black">
                                <span>BLACK ⚫ ({{ (float)($betOdds['black'] ?? 7) }}x)</span>
                            </div>
                            <div class="outside-spot" onclick="handleSpotClick('odd', 'odd', this)" data-type="odd" data-value="odd">
                                <span>ODD</span>
                            </div>
                            <div class="outside-spot" onclick="handleSpotClick('high', 'high', this)" data-type="high" data-value="high">
                                <span>19 to 36</span>
                            </div>
                            <div></div>
                        </div>
                    </div>

                    <!-- Casino Chips Tray -->
                    <div class="chip-tray mt-3">
                        <span class="text-secondary small fw-bold me-2">CHIP VALUE:</span>
                        <div class="casino-chip chip-10 active" onclick="selectChip(10)">10</div>
                        <div class="casino-chip chip-50" onclick="selectChip(50)">50</div>
                        <div class="casino-chip chip-100" onclick="selectChip(100)">100</div>
                        <div class="casino-chip chip-500" onclick="selectChip(500)">500</div>
                        <div class="casino-chip chip-1000" onclick="selectChip(1000)">1K</div>
                        <div class="casino-chip chip-5000" onclick="selectChip(5000)">5K</div>
                    </div>

                    <!-- Actions Bar (Clear, Double, Confirm) -->
                    <div class="actions-bar mt-2 pt-2 border-top border-secondary border-opacity-25">
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-outline-danger btn-action-custom" onclick="clearAllBets()">
                                <i class="fa-solid fa-trash-can"></i> Clear
                            </button>
                            <button class="btn btn-outline-warning btn-action-custom" onclick="doubleAllBets()">
                                <i class="fa-solid fa-angles-up"></i> 2X Double
                            </button>
                        </div>
                        <button class="btn btn-confirm-bets btn-action-custom px-4" id="btnConfirmBets" onclick="submitBets()">
                            <i class="fa-solid fa-check-double"></i> CONFIRM BETS (<span id="btnStakeCounter">₹0</span>)
                        </button>
                    </div>

                </div>

                <!-- History & Rules Card -->
                <div class="rl-cabinet mt-3 p-3">
                    <ul class="nav nav-pills p-1 bg-dark bg-opacity-75 rounded-pill border border-secondary border-opacity-50 mb-3" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active text-light fw-bold py-1 px-3 rounded-pill" data-bs-toggle="tab" data-bs-target="#myBetsTab">
                                <i class="fa-solid fa-receipt me-1"></i> My Bets History
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-secondary fw-bold py-1 px-3 rounded-pill" data-bs-toggle="tab" data-bs-target="#rulesTab">
                                <i class="fa-solid fa-book-open me-1"></i> Paytable & Rules
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- My Bets Tab -->
                        <div class="tab-pane fade show active" id="myBetsTab">
                            <div class="table-responsive rounded-3" style="max-height: 260px; overflow-y: auto;">
                                <table class="table table-dark table-hover table-sm align-middle mb-0" style="font-size: 0.85rem;">
                                    <thead class="sticky-top bg-dark">
                                        <tr class="text-secondary small text-uppercase">
                                            <th>Round</th>
                                            <th>Type</th>
                                            <th>Value</th>
                                            <th>Bet</th>
                                            <th>Odds</th>
                                            <th class="text-end">Payout</th>
                                        </tr>
                                    </thead>
                                    <tbody id="myBetsHistoryBody">
                                        @forelse($myRecentBets as $mb)
                                            <tr>
                                                <td class="font-monospace text-secondary">{{ substr($mb['round_id'], -6) }}</td>
                                                <td><span class="badge bg-secondary text-uppercase">{{ $mb['bet_type'] }}</span></td>
                                                <td class="font-monospace text-warning fw-bold">{{ $mb['bet_value'] }}</td>
                                                <td class="font-monospace text-light">₹{{ number_format($mb['bet_amount'], 2) }}</td>
                                                <td class="font-monospace">{{ number_format($mb['odds'], 2) }}x</td>
                                                <td class="text-end font-monospace fw-bold {{ $mb['status'] === 'won' ? 'text-success' : ($mb['status'] === 'lost' ? 'text-danger' : 'text-warning') }}">
                                                    {{ $mb['status'] === 'won' ? '+₹'.number_format($mb['payout_amount'], 2) : ($mb['status'] === 'lost' ? '₹0.00' : 'PENDING') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-secondary py-3">No bets placed yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Rules Tab -->
                        <div class="tab-pane fade" id="rulesTab">
                            <div class="row g-2 text-secondary small">
                                <div class="col-md-6">
                                    <div class="p-2 rounded bg-dark border border-secondary border-opacity-25">
                                        <div class="fw-bold text-warning mb-1">Inside Bets:</div>
                                        <div>• <strong>Green 0 (Single Number)</strong>: <span class="text-warning fw-bold">36x Payout</span></div>
                                        <div>• <strong>Red Numbers (Any Red #)</strong>: <span class="text-danger fw-bold">{{ (float)($betOdds['red'] ?? 5) }}x Payout</span></div>
                                        <div>• <strong>Black Numbers (Any Black #)</strong>: <span class="text-light fw-bold">{{ (float)($betOdds['black'] ?? 7) }}x Payout</span></div>
                                        <div>• <strong>Split (2 Numbers)</strong>: 18x Payout</div>
                                        <div>• <strong>Corner (4 Numbers)</strong>: 9x Payout</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-2 rounded bg-dark border border-secondary border-opacity-25">
                                        <div class="fw-bold text-success mb-1">Outside Bets:</div>
                                        <div>• <strong>Red 🔴</strong>: <span class="text-danger fw-bold">{{ (float)($betOdds['red'] ?? 5) }}x Payout</span></div>
                                        <div>• <strong>Black ⚫</strong>: <span class="text-light fw-bold">{{ (float)($betOdds['black'] ?? 7) }}x Payout</span></div>
                                        <div>• <strong>Even / Odd</strong>: 2x Payout</div>
                                        <div>• <strong>1-18 (Low) / 19-36 (High)</strong>: 2x Payout</div>
                                        <div>• <strong>1st 12 / 2nd 12 / 3rd 12</strong>: 3x Payout</div>
                                        <div>• <strong>Columns (1st, 2nd, 3rd)</strong>: 3x Payout</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    /* =========================================================================
     * EUROPEAN ROULETTE CLIENT ENGINE
     * HTML5 3D Wheel Physics, Ball Orbit, Sound Synthesizer & Realtime Sync
     * ========================================================================= */
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const IS_AUTHENTICATED = {{ Auth::check() ? 'true' : 'false' }};

    const WHEEL_ORDER = @json($wheelOrder);
    const RED_NUMBERS = @json($redNumbers);
    const BLACK_NUMBERS = @json($blackNumbers);

    let activeRoundId = "{{ $roundData['round_id'] }}";
    let remainingSeconds = {{ $roundData['remaining_seconds'] }};
    let roundStatus = "{{ $roundData['status'] }}";
    let isLocked = {{ $roundData['is_locked'] ? 'true' : 'false' }};
    let duration = {{ $roundData['duration'] }};

    // Duration & Phase parameters:
    // 10s Betting (10, 9, 8... 1) -> 5s Wheel Spinning (Bets Closed) -> 3s Result Announcement
    const BETTING_DURATION = {{ (int) ($roundData['betting_duration'] ?? 10) }};
    const SPIN_DURATION = {{ (int) ($roundData['spin_duration'] ?? 5) }};
    const RESULT_DURATION = {{ (int) ($roundData['result_duration'] ?? 3) }};

    let localRemainingSeconds = {{ (int) $roundData['remaining_seconds'] }};
    let localBettingSeconds = {{ (int) ($roundData['betting_seconds'] ?? max(0, $roundData['remaining_seconds'] - 8)) }};
    let localPhase = "{{ $roundData['phase'] ?? ($roundData['is_locked'] ? 'spinning' : 'betting') }}";

    let selectedChipValue = 10;
    let placedBets = {}; // Key: "type_value" -> { type, value, amount, element }
    let lastSettledRoundId = @json($recentHistory->first()?->round_id ?? null);

    // Web Audio Synthesizer
    let audioCtx = null;
    let isSoundEnabled = localStorage.getItem('rl_sound') !== 'false';

    function initAudio() {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
    }
    ['click', 'touchstart'].forEach(evt => document.addEventListener(evt, () => initAudio(), { once: true }));

    function toggleSound() {
        isSoundEnabled = !isSoundEnabled;
        localStorage.setItem('rl_sound', isSoundEnabled ? 'true' : 'false');
        document.getElementById('soundIcon').className = isSoundEnabled ? 'fa-solid fa-volume-high fs-5 text-warning' : 'fa-solid fa-volume-xmark fs-5 text-secondary';
    }

    function playChipClick() {
        if (!isSoundEnabled) return;
        try {
            initAudio();
            const now = audioCtx.currentTime;
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.frequency.setValueAtTime(1400, now);
            osc.frequency.exponentialRampToValueAtTime(800, now + 0.05);
            gain.gain.setValueAtTime(0.2, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.05);
            osc.start(now);
            osc.stop(now + 0.05);
        } catch(e) {}
    }

    function playBallFretTick() {
        if (!isSoundEnabled) return;
        try {
            initAudio();
            const now = audioCtx.currentTime;
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'triangle';
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.frequency.setValueAtTime(950 + Math.random() * 200, now);
            gain.gain.setValueAtTime(0.12, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.035);
            osc.start(now);
            osc.stop(now + 0.035);
        } catch(e) {}
    }

    function playWinChimes() {
        if (!isSoundEnabled) return;
        try {
            initAudio();
            const now = audioCtx.currentTime;
            [523.25, 659.25, 783.99, 1046.50].forEach((freq, idx) => {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'triangle';
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                const st = now + (idx * 0.08);
                osc.frequency.setValueAtTime(freq, st);
                gain.gain.setValueAtTime(0.25, st);
                gain.gain.exponentialRampToValueAtTime(0.001, st + 0.5);
                osc.start(st);
                osc.stop(st + 0.5);
            });
        } catch(e) {}
    }

    /* =========================================================================
     * HTML5 ROULETTE CANVAS WHEEL RENDERER & ANIMATOR
     * ========================================================================= */
    const canvas = document.getElementById('rouletteCanvas');
    const ctx = canvas.getContext('2d');
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const outerRadius = 140;
    const innerRadius = 88;
    const hubRadius = 45;

    let wheelAngle = 0; // In radians
    let wheelSpeed = 0.005; // Idle rotation
    let ballAngle = 0; // Ball in radians
    let ballRadiusPos = 120; // Distance of ball from center
    let isSpinningSequence = false;

    function drawWheel() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        const totalPockets = WHEEL_ORDER.length; // 37
        const arc = (2 * Math.PI) / totalPockets;

        // 1. Draw Pockets
        for (let i = 0; i < totalPockets; i++) {
            const num = WHEEL_ORDER[i];
            const angle = wheelAngle + (i * arc);

            ctx.beginPath();
            ctx.arc(centerX, centerY, outerRadius, angle, angle + arc);
            ctx.arc(centerX, centerY, innerRadius, angle + arc, angle, true);
            ctx.closePath();

            if (num === 0) {
                ctx.fillStyle = '#059669';
            } else if (RED_NUMBERS.includes(num)) {
                ctx.fillStyle = '#b91c1c';
            } else {
                ctx.fillStyle = '#111827';
            }
            ctx.fill();

            // Fret separator lines
            ctx.strokeStyle = '#d4af37';
            ctx.lineWidth = 1.2;
            ctx.stroke();

            // Draw Number Text placed towards outer rim so the resting ball in the pocket does not cover it
            ctx.save();
            ctx.translate(centerX, centerY);
            ctx.rotate(angle + arc / 2);
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 9.5px "JetBrains Mono", monospace';
            ctx.shadowColor = 'rgba(0,0,0,0.85)';
            ctx.shadowBlur = 3;
            // Placed at 126px radius (near outer rim 140px), keeping numbers clearly visible
            ctx.fillText(num.toString(), 126, 0);
            ctx.restore();
        }

        // 2. Inner Track Ring
        ctx.beginPath();
        ctx.arc(centerX, centerY, innerRadius, 0, 2 * Math.PI);
        ctx.fillStyle = '#1c0a02';
        ctx.fill();
        ctx.strokeStyle = '#f59e0b';
        ctx.lineWidth = 2.5;
        ctx.stroke();

        // 3. Center Gold Turret / Hub
        const hubGrad = ctx.createRadialGradient(centerX, centerY, 5, centerX, centerY, hubRadius);
        hubGrad.addColorStop(0, '#fef08a');
        hubGrad.addColorStop(0.4, '#f59e0b');
        hubGrad.addColorStop(0.8, '#b45309');
        hubGrad.addColorStop(1, '#78350f');

        ctx.beginPath();
        ctx.arc(centerX, centerY, hubRadius, 0, 2 * Math.PI);
        ctx.fillStyle = hubGrad;
        ctx.fill();
        ctx.strokeStyle = '#fde047';
        ctx.lineWidth = 2;
        ctx.stroke();

        // 4-Point Silver Spindle
        for (let j = 0; j < 4; j++) {
            const spindleAngle = wheelAngle + (j * (Math.PI / 2));
            ctx.save();
            ctx.translate(centerX, centerY);
            ctx.rotate(spindleAngle);
            ctx.beginPath();
            ctx.moveTo(0, -3);
            ctx.lineTo(hubRadius - 5, 0);
            ctx.lineTo(0, 3);
            ctx.closePath();
            ctx.fillStyle = '#e2e8f0';
            ctx.fill();
            ctx.restore();
        }

        // Center Pin
        ctx.beginPath();
        ctx.arc(centerX, centerY, 7, 0, 2 * Math.PI);
        ctx.fillStyle = '#f8fafc';
        ctx.fill();

        // 4. Draw Ball if spinning or landed
        if (ballRadiusPos > 0) {
            const bx = centerX + ballRadiusPos * Math.cos(ballAngle);
            const by = centerY + ballRadiusPos * Math.sin(ballAngle);

            // Ball Outer Soft Shadow
            ctx.beginPath();
            ctx.arc(bx, by, 4.5, 0, 2 * Math.PI);
            ctx.fillStyle = 'rgba(0, 0, 0, 0.4)';
            ctx.shadowColor = '#000000';
            ctx.shadowBlur = 4;
            ctx.fill();
            ctx.shadowBlur = 0;

            // Shiny White Ivory Ball
            const ballGrad = ctx.createRadialGradient(bx - 1.2, by - 1.2, 0.8, bx, by, 4.2);
            ballGrad.addColorStop(0, '#ffffff');
            ballGrad.addColorStop(0.65, '#f1f5f9');
            ballGrad.addColorStop(1, '#94a3b8');

            ctx.beginPath();
            ctx.arc(bx, by, 4.2, 0, 2 * Math.PI);
            ctx.fillStyle = ballGrad;
            ctx.fill();

            // Crisp perimeter stroke so ball stands out on red/black/green
            ctx.strokeStyle = '#334155';
            ctx.lineWidth = 0.8;
            ctx.stroke();
        }
    }

    // Rest radius for ball inside pocket: 104px (sits nestled in pocket near inner ring 88px, leaving the number text at 126px completely visible)
    const pocketRestRadius = 104;

    function animateWheelLoop() {
        if (!isSpinningSequence) {
            wheelAngle += wheelSpeed;
            // Keep ball nestled in pocket if landed
            if (lastWinningNumber !== null) {
                const targetIdx = WHEEL_ORDER.indexOf(lastWinningNumber);
                const arc = (2 * Math.PI) / 37;
                ballAngle = wheelAngle + (targetIdx * arc) + (arc / 2);
                ballRadiusPos = pocketRestRadius;
            }
        }
        drawWheel();
        requestAnimationFrame(animateWheelLoop);
    }
    let lastWinningNumber = @json($recentHistory->first()?->winning_number ?? null);
    requestAnimationFrame(animateWheelLoop);

    function triggerSpinAnimation(winningNumber, onFinished) {
        isSpinningSequence = true;
        const targetNumber = parseInt(winningNumber, 10);
        const targetIdx = WHEEL_ORDER.indexOf(targetNumber);
        const arc = (2 * Math.PI) / 37;

        // Duration of spin sequence (approx 6.5s to comfortably fill the 7s spinning phase)
        const totalDuration = 6500;
        const startTime = performance.now();

        // Capture the starting wheel angle at the moment spin begins
        const startWheelAngle = wheelAngle;

        // Wheel spins 3 full revolutions forward and stops at a deterministic final angle
        const wheelTotalSpin = Math.PI * 6; // 3 full revolutions
        const finalWheelAngle = startWheelAngle + wheelTotalSpin;

        // Ball spins opposite direction (counter-clockwise) for visual realism,
        // then lands exactly in the target pocket relative to the wheel's final position.
        // Final ball angle = finalWheelAngle + pocket center offset on wheel
        const pocketCenterOnWheel = (targetIdx * arc) + (arc / 2);
        const finalBallAngle = finalWheelAngle + pocketCenterOnWheel;

        // Ball spins faster than wheel initially: 6 full counter-revolutions before settling
        const ballTotalPreSpin = Math.PI * 12;

        let lastTickTime = 0;

        function step(now) {
            const elapsed = now - startTime;
            const progress = Math.min(1, elapsed / totalDuration);
            // Cubic ease out for smooth deceleration
            const ease = 1 - Math.pow(1 - progress, 3.2);

            // CRITICAL: Both wheel and ball use deterministic interpolation from
            // fixed start → fixed end, so they are ALWAYS perfectly synchronized.
            wheelAngle = startWheelAngle + (wheelTotalSpin * ease);
            ballAngle = finalBallAngle - (ballTotalPreSpin * (1 - ease));

            // Ball decelerates and falls into pocket
            if (progress < 0.65) {
                ballRadiusPos = 132; // Outer track orbit
            } else {
                // Drop smoothly into pocket rest position
                const dropProgress = (progress - 0.65) / 0.35;
                ballRadiusPos = 132 - (dropProgress * (132 - pocketRestRadius));
            }

            // Audio tick — decreasing frequency as ball slows down
            if (now - lastTickTime > (50 + progress * 200)) {
                playBallFretTick();
                lastTickTime = now;
            }

            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                // Ensure exact final positions (no floating point drift)
                wheelAngle = finalWheelAngle;
                ballAngle = finalBallAngle;
                ballRadiusPos = pocketRestRadius;

                isSpinningSequence = false;
                lastWinningNumber = targetNumber;
                if (onFinished) onFinished();
            }
        }
        requestAnimationFrame(step);
    }

    /* =========================================================================
     * CHIP & TABLE INTERACTIONS
     * ========================================================================= */
    function selectChip(val) {
        selectedChipValue = parseInt(val);
        document.querySelectorAll('.casino-chip').forEach(c => c.classList.remove('active'));
        document.querySelector(`.chip-${val}`)?.classList.add('active');
        playChipClick();
    }

    function handleSpotClick(type, value, element) {
        if (isLocked || roundStatus !== 'betting' || localBettingSeconds <= 0) {
            const alertBox = document.getElementById('statusMessageAlert');
            if (alertBox) {
                alertBox.innerHTML = '<i class="fa-solid fa-lock me-1"></i> BETS CLOSED! Wheel is spinning!';
                alertBox.style.color = '#f87171';
            }
            return;
        }

        playChipClick();
        const key = `${type}_${value}`;

        if (!placedBets[key]) {
            placedBets[key] = {
                type: type,
                value: value,
                amount: 0,
                element: element
            };
        }

        placedBets[key].amount += selectedChipValue;
        updateSpotUI(key);
        updateTotalStakeDisplay();
    }

    function updateSpotUI(key) {
        const bet = placedBets[key];
        let badge = bet.element.querySelector('.chip-badge');
        if (!badge) {
            badge = document.createElement('div');
            badge.className = 'chip-badge';
            bet.element.appendChild(badge);
        }
        badge.innerText = bet.amount >= 1000 ? (bet.amount / 1000).toFixed(1) + 'k' : bet.amount;
    }

    function updateTotalStakeDisplay() {
        let total = 0;
        for (const k in placedBets) {
            total += placedBets[k].amount;
        }
        document.getElementById('totalStakeDisplay').innerText = '₹' + total.toFixed(0);
        document.getElementById('btnStakeCounter').innerText = '₹' + total.toFixed(0);
    }

    function clearAllBets() {
        for (const k in placedBets) {
            const badge = placedBets[k].element.querySelector('.chip-badge');
            if (badge) badge.remove();
        }
        placedBets = {};
        updateTotalStakeDisplay();
        playChipClick();
    }

    function doubleAllBets() {
        for (const k in placedBets) {
            placedBets[k].amount *= 2;
            updateSpotUI(k);
        }
        updateTotalStakeDisplay();
        playChipClick();
    }

    async function submitBets() {
        if (!IS_AUTHENTICATED) {
            window.location.href = "{{ route('login') }}";
            return;
        }

        if (isLocked || roundStatus !== 'betting' || localBettingSeconds <= 0) {
            const alertBox = document.getElementById('statusMessageAlert');
            if (alertBox) {
                alertBox.innerHTML = '<i class="fa-solid fa-lock me-1"></i> BETS CLOSED for current spin! Please wait for next round.';
                alertBox.style.color = '#f87171';
            }
            return;
        }

        const betsList = [];
        for (const k in placedBets) {
            betsList.push({
                type: placedBets[k].type,
                value: placedBets[k].value,
                amount: placedBets[k].amount
            });
        }

        if (betsList.length === 0) {
            const alertBox = document.getElementById('statusMessageAlert');
            if (alertBox) {
                alertBox.innerHTML = '<i class="fa-solid fa-hand-pointer me-1"></i> Please place chips on the table first.';
                alertBox.style.color = '#fbbf24';
            }
            return;
        }

        const btn = document.getElementById('btnConfirmBets');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> PLACING...';

        try {
            const res = await fetch("{{ route('api.roulette.place-bet') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ bets: betsList })
            });

            const data = await res.json();
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check-double"></i> CONFIRM BETS (<span id="btnStakeCounter">₹0</span>)';

            const alertBox = document.getElementById('statusMessageAlert');

            if (!data.success) {
                if (alertBox) {
                    alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i> ' + (data.message || 'Failed to place bets.');
                    alertBox.style.color = '#f87171';
                }
                return;
            }

            // Update user balance display
            if (data.new_balance !== undefined) {
                document.getElementById('userBalanceDisplay').innerText = '₹' + parseFloat(data.new_balance).toFixed(2);
                const navBal = document.getElementById('userWalletBalance');
                if (navBal) navBal.innerText = '₹' + parseFloat(data.new_balance).toFixed(2);
            }

            if (alertBox) {
                alertBox.innerHTML = '<i class="fa-solid fa-check-circle me-1"></i> BETS CONFIRMED! GOOD LUCK!';
                alertBox.style.color = '#38bdf8';
            }

            clearAllBets();
            refreshMyBetsTable();

        } catch(e) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check-double"></i> CONFIRM BETS (<span id="btnStakeCounter">₹0</span>)';
            const alertBox = document.getElementById('statusMessageAlert');
            if (alertBox) {
                alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i> Network error placing bets.';
                alertBox.style.color = '#f87171';
            }
        }
    }

    /* =========================================================================
     * REALTIME COUNTDOWN & POLLING SYNC
     * Phase cycle: 10s BETTING (10,9,8...1) -> 5s SPINNING (BETS CLOSED) -> 3s RESULT OUT
     * ========================================================================= */
    let localPhaseState = localPhase; // 'betting', 'spinning', 'result'
    let spinTriggeredForRound = null;

    setInterval(() => {
        if (localRemainingSeconds > 0) {
            localRemainingSeconds--;
        } else {
            localRemainingSeconds = duration; // Rollover fallback until server poll arrives
        }

        // Calculate phase-specific seconds
        if (localRemainingSeconds > (SPIN_DURATION + RESULT_DURATION)) {
            // Betting phase (countdown 10 down to 1)
            localPhaseState = 'betting';
            isLocked = false;
            roundStatus = 'betting';
            localBettingSeconds = localRemainingSeconds - (SPIN_DURATION + RESULT_DURATION);
        } else if (localRemainingSeconds > RESULT_DURATION) {
            // Spinning phase (countdown 5 down to 1)
            localPhaseState = 'spinning';
            isLocked = true;
            roundStatus = 'spinning';
            localBettingSeconds = 0;
        } else {
            // Result announcement phase (3 down to 1)
            localPhaseState = 'result';
            isLocked = true;
            roundStatus = 'spinning';
            localBettingSeconds = 0;
        }

        updatePhaseUI();
    }, 1000);

    function updatePhaseUI() {
        const timerVal = document.getElementById('countdownTimerVal');
        const timerLabel = document.getElementById('timerLabel');
        const alertBox = document.getElementById('statusMessageAlert');
        const btnConfirm = document.getElementById('btnConfirmBets');

        if (localPhaseState === 'betting') {
            timerVal.className = 'rl-timer-val';
            timerLabel.innerText = 'BETS OPEN:';
            timerVal.innerText = Math.max(1, localBettingSeconds) + 's';
            if (btnConfirm) btnConfirm.disabled = false;
            if (alertBox && !alertBox.innerText.includes('BETS CONFIRMED')) {
                alertBox.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> PLACE YOUR BETS (' + Math.max(1, localBettingSeconds) + 's left)';
                alertBox.style.color = '#34d399';
            }
        } else if (localPhaseState === 'spinning') {
            const spinSec = Math.max(1, localRemainingSeconds - RESULT_DURATION);
            timerVal.className = 'rl-timer-val warning';
            timerLabel.innerText = 'BETS CLOSED:';
            timerVal.innerText = spinSec + 's';
            if (btnConfirm) btnConfirm.disabled = true;
            if (alertBox && !alertBox.innerText.includes('BETS CONFIRMED')) {
                alertBox.innerHTML = '<i class="fa-solid fa-arrows-spin fa-spin me-1"></i> BETS CLOSED - WHEEL SPINNING (' + spinSec + 's)';
                alertBox.style.color = '#fbbf24';
            }
        } else {
            // Result out phase
            const resSec = Math.max(1, localRemainingSeconds);
            timerVal.className = 'rl-timer-val text-success';
            timerLabel.innerText = 'RESULT OUT:';
            timerVal.innerText = resSec + 's';
            if (btnConfirm) btnConfirm.disabled = true;
            if (alertBox) {
                alertBox.innerHTML = '<i class="fa-solid fa-trophy me-1 text-warning"></i> RESULT OUT! NEXT ROUND IN ' + resSec + 's';
                alertBox.style.color = '#38bdf8';
            }
        }
    }

    async function pollLiveStatus() {
        try {
            const res = await fetch("{{ route('api.roulette.status') }}");
            const data = await res.json();
            if (!data.success) return;

            // Sync timer and server status state
            localRemainingSeconds = Math.max(0, parseInt(data.remaining_seconds || 0));
            isLocked = !!data.is_locked;
            roundStatus = data.status || (isLocked ? 'spinning' : 'betting');
            duration = data.duration || 30;
            localPhaseState = data.phase || (isLocked ? 'spinning' : 'betting');

            if (data.betting_seconds !== undefined) {
                localBettingSeconds = parseInt(data.betting_seconds);
            }

            updatePhaseUI();

            // If round changed
            if (data.round_id !== activeRoundId) {
                activeRoundId = data.round_id;
                document.getElementById('roundIdDisplay').innerText = activeRoundId;
                clearAllBets();
                refreshMyBetsTable();
            }

            // Sync balance
            if (data.balance !== undefined && IS_AUTHENTICATED) {
                document.getElementById('userBalanceDisplay').innerText = '₹' + parseFloat(data.balance).toFixed(2);
                const navBal = document.getElementById('userWalletBalance');
                if (navBal) navBal.innerText = '₹' + parseFloat(data.balance).toFixed(2);
            }

            // Only trigger spin animation when round enters spinning or result phase (NEVER while betting phase is running)
            const targetWinning = (data.current_winning && data.current_winning.winning_number !== undefined)
                ? data.current_winning
                : (data.last_round || null);

            if (targetWinning && targetWinning.round_id !== lastSettledRoundId) {
                // If round is currently in spinning or result phase, start wheel spin!
                if (data.is_locked || data.phase === 'spinning' || data.phase === 'result') {
                    lastSettledRoundId = targetWinning.round_id;
                    triggerSpinAnimation(targetWinning.winning_number, () => {
                        // Reveal winning result overlay exactly when ball lands in pocket!
                        showWinOverlay(targetWinning);
                        playWinChimes();
                        prependRecentNumber(targetWinning.winning_number, targetWinning.winning_color);
                        refreshMyBetsTable();
                    });
                }
            }

        } catch(e) {}
    }

    setInterval(pollLiveStatus, 1000);

    function showWinOverlay(round) {
        const card = document.getElementById('winRevealCard');
        const numEl = document.getElementById('winRevealNumber');
        const badge = document.getElementById('winRevealColorBadge');

        numEl.innerText = round.winning_number;
        badge.innerText = round.winning_color.toUpperCase();
        badge.className = `badge fs-6 px-3 py-1 ${round.winning_color === 'red' ? 'bg-danger' : (round.winning_color === 'black' ? 'bg-dark border border-secondary' : 'bg-success')}`;

        card.style.display = 'block';
        setTimeout(() => { card.style.display = 'none'; }, 3800);
    }

    function prependRecentNumber(num, color) {
        const strip = document.getElementById('recentNumbersStrip');
        const pill = document.createElement('div');
        pill.className = `num-pill ${color}`;
        pill.innerText = num;
        strip.insertBefore(pill, strip.firstChild);
    }

    async function refreshMyBetsTable() {
        if (!IS_AUTHENTICATED) return;
        try {
            const res = await fetch("{{ route('api.roulette.my-bets') }}");
            const data = await res.json();
            if (!data.success || !data.bets) return;

            const tbody = document.getElementById('myBetsHistoryBody');
            if (data.bets.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-secondary py-3">No bets placed yet.</td></tr>';
                return;
            }

            let html = '';
            data.bets.forEach(b => {
                const won = (b.status === 'won');
                const lost = (b.status === 'lost');
                html += `
                    <tr>
                        <td class="font-monospace text-secondary">${b.round_id.substring(b.round_id.length - 6)}</td>
                        <td><span class="badge bg-secondary text-uppercase">${b.bet_type}</span></td>
                        <td class="font-monospace text-warning fw-bold">${b.bet_value}</td>
                        <td class="font-monospace text-light">₹${parseFloat(b.bet_amount).toFixed(2)}</td>
                        <td class="font-monospace">${parseFloat(b.odds).toFixed(2)}x</td>
                        <td class="text-end font-monospace fw-bold ${won ? 'text-success' : (lost ? 'text-danger' : 'text-warning')}">
                            ${won ? '+₹' + parseFloat(b.payout_amount).toFixed(2) : (lost ? '₹0.00' : 'PENDING')}
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } catch(e) {}
    }
</script>
@endsection
