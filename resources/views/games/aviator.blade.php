@extends('layouts.app')

@section('title', 'Aviator Live Crash Arena - rupeerush')

@section('styles')
<style>
    :root {
        --av-bg: #090c15;
        --av-card-bg: #111625;
        --av-card-border: #1e293b;
        --av-red: #e11d48;
        --av-red-glow: rgba(225, 29, 72, 0.45);
        --av-orange: #f97316;
        --av-orange-glow: rgba(249, 115, 22, 0.5);
        --av-green: #10b981;
        --av-green-glow: rgba(16, 185, 129, 0.45);
        --av-blue: #3b82f6;
        --av-purple: #a855f7;
        --av-gold: #eab308;
    }

    body {
        background-color: var(--av-bg);
        color: #f1f5f9;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .aviator-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding-bottom: 50px;
    }

    /* Top Bar Header */
    .aviator-nav-bar {
        background: linear-gradient(135deg, #0e1424 0%, #151d33 100%);
        border: 1px solid var(--av-card-border);
        border-radius: 16px;
        padding: 12px 20px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
    }

    .logo-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 800;
        font-size: 1.25rem;
        letter-spacing: 0.5px;
    }

    .plane-icon-glow {
        color: var(--av-red);
        filter: drop-shadow(0 0 10px var(--av-red-glow));
        animation: planeFloat 2.5s ease-in-out infinite;
    }

    @keyframes planeFloat {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-4px) rotate(3deg); }
    }

    /* Multipliers Strip */
    .history-strip-container {
        background: #0d1220;
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        scrollbar-width: thin;
        scrollbar-color: #334155 transparent;
    }

    .history-strip-container::-webkit-scrollbar {
        height: 4px;
    }
    .history-strip-container::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 4px;
    }

    .mult-pill {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 3px 10px;
        border-radius: 20px;
        white-space: nowrap;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .mult-pill:hover {
        transform: translateY(-2px);
    }
    .mult-pill.pill-low {
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.35);
    }
    .mult-pill.pill-mid {
        background: rgba(168, 85, 247, 0.15);
        color: #c084fc;
        border: 1px solid rgba(168, 85, 247, 0.35);
    }
    .mult-pill.pill-high {
        background: rgba(234, 179, 8, 0.18);
        color: #fde047;
        border: 1px solid rgba(234, 179, 8, 0.45);
        box-shadow: 0 0 10px rgba(234, 179, 8, 0.2);
    }

    /* Main Flight Arena Canvas Area */
    .flight-stage-card {
        background: radial-gradient(circle at 60% 40%, #151f38 0%, #0a0e1c 70%, #050812 100%);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 22px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 16px 50px rgba(0, 0, 0, 0.7), inset 0 0 60px rgba(0, 0, 0, 0.6);
        transition: border-color 0.3s;
    }
    .flight-stage-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: radial-gradient(circle at 50% 50%, rgba(225, 29, 72, 0.04), transparent 60%);
        pointer-events: none;
    }
    @keyframes stageShake {
        0% { transform: translate(0, 0); }
        15% { transform: translate(-5px, 3px); }
        30% { transform: translate(6px, -4px); }
        45% { transform: translate(-5px, -3px); }
        60% { transform: translate(4px, 3px); }
        75% { transform: translate(-3px, -1px); }
        100% { transform: translate(0, 0); }
    }
    .flight-stage-card.shake {
        animation: stageShake 0.45s ease-out;
    }

    #flightCanvas {
        display: block;
        width: 100%;
        height: 380px;
        background: transparent;
    }

    @media (max-width: 768px) {
        .aviator-wrapper {
            padding: 2px 2px 75px !important;
        }
        .aviator-nav-bar {
            padding: 6px 8px !important;
            margin-bottom: 6px !important;
            border-radius: 12px !important;
            gap: 4px !important;
        }
        .logo-badge {
            font-size: 0.95rem !important;
            gap: 6px !important;
        }
        .btn-rules-nav {
            font-size: 0.75rem !important;
            padding: 3px 8px !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
        }
        .wallet-pill {
            padding: 3px 7px !important;
            font-size: 0.78rem !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
        }
        .wallet-amt {
            font-size: 0.82rem !important;
        }
        .btn-deposit-nav {
            padding: 4px 10px !important;
            font-size: 0.78rem !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
        }
        .history-strip-container {
            padding: 3px 6px !important;
            margin-bottom: 6px !important;
            border-radius: 8px !important;
        }
        .mult-pill {
            padding: 2px 7px !important;
            font-size: 0.72rem !important;
        }
        .flight-stage-card {
            border-radius: 14px !important;
            margin-bottom: 6px !important;
        }
        #flightCanvas {
            height: 165px !important;
        }
        .live-multiplier-text {
            font-size: 2.8rem !important;
        }
        .waiting-countdown-box {
            padding: 6px 14px !important;
            border-radius: 12px !important;
        }
        #countdownDigits {
            font-size: 2rem !important;
        }
        .flew-away-text {
            font-size: 1.6rem !important;
        }
        .bet-control-box {
            padding: 6px 6px !important;
            border-radius: 12px !important;
            margin-bottom: 4px !important;
        }
        .bet-input-field {
            font-size: 0.82rem !important;
            padding: 2px 3px !important;
            height: 26px !important;
            border-radius: 6px !important;
        }
        .input-group-sm > .btn, .input-group-sm > .input-group-text {
            padding: 2px 5px !important;
            font-size: 0.7rem !important;
            height: 26px !important;
        }
        .bet-chip-btn {
            padding: 2px 2px !important;
            font-size: 0.65rem !important;
            border-radius: 6px !important;
        }
        .btn-action-bet {
            height: 38px !important;
            font-size: 0.85rem !important;
            border-radius: 10px !important;
            padding: 2px 4px !important;
            line-height: 1.1 !important;
        }
        .sub-amt-label {
            font-size: 0.72rem !important;
            margin-top: 1px !important;
            line-height: 1 !important;
            white-space: nowrap !important;
        }
    }

    /* Floating Win Celebration Banner */
    .win-celebration-banner {
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.95) 0%, rgba(5, 150, 105, 0.95) 100%);
        border: 1.5px solid #34d399;
        box-shadow: 0 8px 32px rgba(16, 185, 129, 0.55), 0 0 20px rgba(52, 211, 153, 0.4);
        border-radius: 50px;
        padding: 10px 24px;
        color: #ffffff;
        z-index: 30;
        pointer-events: none;
        backdrop-filter: blur(12px);
        animation: bannerSlideIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes bannerSlideIn {
        0% { transform: translateX(-50%) translateY(-25px) scale(0.8); opacity: 0; }
        100% { transform: translateX(-50%) translateY(0) scale(1); opacity: 1; }
    }
    .win-banner-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: #fef08a;
    }
    .win-banner-sub {
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.8px;
        color: #d1fae5;
        text-transform: uppercase;
    }
    .win-banner-amount {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.25rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.1;
    }
    .win-banner-mult-badge {
        background: rgba(0, 0, 0, 0.35);
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.85rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 20px;
        color: #fef08a;
    }

    /* Canvas Center Overlay */
    .canvas-center-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        pointer-events: none;
        z-index: 10;
    }

    .live-multiplier-text {
        font-family: 'JetBrains Mono', monospace;
        font-size: 5.2rem;
        font-weight: 800;
        color: #ffffff;
        text-shadow: 0 0 35px rgba(56, 189, 248, 0.85), 0 0 70px rgba(56, 189, 248, 0.45);
        letter-spacing: -2px;
        line-height: 1;
        transition: text-shadow 0.25s ease, color 0.25s ease;
    }
    .live-multiplier-text.mult-tier-mid {
        color: #fdf4ff;
        text-shadow: 0 0 35px rgba(217, 70, 239, 0.85), 0 0 75px rgba(217, 70, 239, 0.45);
    }
    .live-multiplier-text.mult-tier-high {
        color: #fef08a;
        text-shadow: 0 0 40px rgba(245, 158, 11, 0.95), 0 0 80px rgba(245, 158, 11, 0.5);
    }

    @media (max-width: 768px) {
        .live-multiplier-text {
            font-size: 3.4rem;
        }
    }

    .flew-away-text {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 2.3rem;
        font-weight: 900;
        color: #f43f5e;
        text-transform: uppercase;
        letter-spacing: 2.5px;
        text-shadow: 0 0 30px rgba(244, 63, 94, 0.9);
        animation: crashPulse 0.4s ease-in-out infinite alternate;
    }

    @keyframes crashPulse {
        from { transform: scale(1); filter: drop-shadow(0 0 10px rgba(244, 63, 94, 0.5)); }
        to { transform: scale(1.05); filter: drop-shadow(0 0 25px rgba(244, 63, 94, 0.9)); }
    }

    .waiting-countdown-box {
        background: rgba(15, 23, 42, 0.9);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        padding: 18px 32px;
        box-shadow: 0 12px 35px rgba(0,0,0,0.6), 0 0 20px rgba(225, 29, 72, 0.15);
    }

    .progress-bar-takeoff {
        height: 7px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 10px;
        overflow: hidden;
        margin-top: 12px;
    }
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #e11d48, #f97316);
        transition: width 0.1s linear;
        box-shadow: 0 0 12px var(--av-red);
    }

    /* Dual Bet Cards */
    .bet-control-box {
        background: linear-gradient(145deg, #101627 0%, #0a0e1a 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 18px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.05);
        position: relative;
        transition: all 0.25s ease;
    }
    .bet-control-box:hover {
        border-color: rgba(255, 255, 255, 0.16);
    }
    .bet-control-box.slot-active {
        border-color: rgba(249, 115, 22, 0.6);
        box-shadow: 0 0 25px rgba(249, 115, 22, 0.2), inset 0 0 15px rgba(249, 115, 22, 0.05);
    }

    .bet-chip-btn {
        background: #182236;
        border: 1px solid #293854;
        color: #cbd5e1;
        font-size: 0.76rem;
        font-weight: 700;
        padding: 5px 8px;
        border-radius: 10px;
        transition: all 0.18s;
    }
    .bet-chip-btn:hover {
        background: #273654;
        color: #ffffff;
        border-color: #3b82f6;
        transform: translateY(-1px);
    }

    .bet-input-field {
        background: #080b15;
        border: 1px solid #293854;
        color: #fff;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        font-size: 1.25rem;
        text-align: center;
        border-radius: 12px;
        padding: 8px 12px;
        transition: all 0.2s;
    }
    .bet-input-field:focus {
        border-color: var(--av-orange);
        outline: none;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.25);
        background: #060912;
        color: #fff;
    }

    /* Giant Dynamic Action Buttons */
    .btn-action-bet {
        width: 100%;
        height: 84px;
        border-radius: 16px;
        font-weight: 800;
        font-size: 1.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        border: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .btn-action-bet::before {
        content: '';
        position: absolute;
        top: 0; left: -100%; width: 100%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
        transition: left 0.5s;
    }
    .btn-action-bet:hover::before {
        left: 100%;
    }

    /* Green Bet State */
    .btn-action-bet.btn-state-bet {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        box-shadow: 0 6px 22px var(--av-green-glow);
    }
    .btn-action-bet.btn-state-bet:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px var(--av-green-glow);
        background: linear-gradient(135deg, #34d399 0%, #059669 100%);
    }

    /* Red Cancel State */
    .btn-action-bet.btn-state-cancel {
        background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
        color: #ffffff;
        box-shadow: 0 6px 22px var(--av-red-glow);
    }
    .btn-action-bet.btn-state-cancel:hover:not(:disabled) {
        transform: translateY(-2px);
        background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%);
    }

    /* Orange Glowing Cash Out State */
    .btn-action-bet.btn-state-cashout {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: #ffffff;
        box-shadow: 0 8px 32px var(--av-orange-glow);
        animation: pulseCashout 1s infinite alternate;
    }
    @keyframes pulseCashout {
        from { transform: scale(1); box-shadow: 0 4px 20px var(--av-orange-glow); }
        to { transform: scale(1.02); box-shadow: 0 10px 38px rgba(249, 115, 22, 0.85); }
    }

    .btn-action-bet:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }

    .sub-amt-label {
        font-size: 0.95rem;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        margin-top: 2px;
    }

    /* Side Live Bets Room Table */
    .sidebar-panel-card {
        background: #111625;
        border: 1px solid var(--av-card-border);
        border-radius: 18px;
        overflow: hidden;
    }

    .table-dark-custom {
        --bs-table-bg: transparent;
        --bs-table-color: #cbd5e1;
        font-size: 0.85rem;
        margin-bottom: 0;
    }
    .table-dark-custom th {
        border-bottom: 1px solid #1e293b;
        color: #94a3b8;
        font-weight: 700;
        padding: 10px 12px;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .table-dark-custom td {
        padding: 8px 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        vertical-align: middle;
    }

    /* Modal Styling */
    .modal-content-dark {
        background: #0f172a;
        border: 1px solid #334155;
        color: #f8fafc;
        border-radius: 20px;
    }

    /* Toast Notification */
    .av-toast {
        position: fixed;
        bottom: 25px;
        right: 25px;
        z-index: 9999;
        min-width: 280px;
        padding: 14px 20px;
        border-radius: 12px;
        font-weight: 700;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        display: none;
        align-items: center;
        gap: 12px;
        animation: slideUp 0.3s ease;
    }
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid aviator-wrapper px-2 px-md-4 py-3">

    <!-- Top Navigation Bar -->
    <div class="aviator-nav-bar mb-2 d-flex align-items-center justify-content-between gap-1 gap-sm-2 flex-nowrap">
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <div class="logo-badge">
                <i class="fa-solid fa-plane-departure plane-icon-glow"></i>
                <span class="d-none d-sm-inline" style="color: #fff;">AVIATOR</span>
                <span class="badge bg-danger rounded-pill px-2 py-0.5 d-none d-sm-inline" style="font-size: 0.65rem;">LIVE CRASH</span>
            </div>
            <div class="d-none d-md-flex align-items-center gap-2 text-secondary" style="font-size: 0.8rem;">
                <span>ROUND:</span>
                <span class="text-warning fw-bold font-monospace" id="headerRoundId">{{ $roundData['round_id'] }}</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-1 gap-sm-2 flex-nowrap flex-shrink-0">
            <!-- Audio Toggle Button -->
            <button class="btn btn-sm btn-dark border-secondary rounded-pill p-0 d-flex align-items-center justify-content-center flex-shrink-0" id="btnSoundToggle" title="Toggle Sound" style="width: 32px; height: 32px; min-width: 32px;">
                <i class="fa-solid fa-volume-high text-warning" id="soundIcon" style="font-size: 0.85rem;"></i>
            </button>

            <!-- Rules Modal Trigger -->
            <button class="btn btn-sm btn-dark border-secondary rounded-pill px-2 py-1 btn-rules-nav d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#rulesModal" title="How To Play">
                <i class="fa-solid fa-circle-question text-info"></i>
                <span class="d-none d-sm-inline">How To Play</span>
                <span class="d-inline d-sm-none">Rules</span>
            </button>

            <!-- User Wallet & Deposit Shortcut -->
            @auth
                <div class="wallet-pill">
                    <i class="fa-solid fa-wallet text-info"></i>
                    <span class="wallet-amt" id="userWalletBalance">₹{{ number_format(Auth::user()->balance, 2) }}</span>
                </div>
                <a href="{{ route('wallet.deposit') }}" class="btn btn-sm btn-deposit-nav">
                    <i class="fa-solid fa-plus-circle"></i> Deposit
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1 flex-shrink-0 text-nowrap" style="font-size: 0.8rem;">
                    <i class="fa-solid fa-right-to-bracket me-1"></i> Login
                </a>
            @endauth
        </div>
    </div>

    <!-- Multiplier Past History Pills Strip -->
    <div class="history-strip-container mb-3" id="historyStrip">
        <span class="text-secondary small fw-bold me-2"><i class="fa-solid fa-clock-rotate-left"></i> HISTORY:</span>
        @forelse($recentMultipliers as $mult)
            @php
                $pillClass = ($mult >= 10.0) ? 'pill-high' : (($mult >= 2.0) ? 'pill-mid' : 'pill-low');
            @endphp
            <span class="mult-pill {{ $pillClass }}">{{ number_format($mult, 2) }}x</span>
        @empty
            <span class="mult-pill pill-low">1.15x</span>
            <span class="mult-pill pill-mid">2.40x</span>
            <span class="mult-pill pill-high">12.50x</span>
        @endforelse
    </div>

    <!-- Main Grid: Flight Canvas Stage + Live Room Bets -->
    <div class="row g-3">
        <!-- Center Arena Column -->
        <div class="col-lg-8 col-xl-8">
            <!-- Flight Stage Card -->
            <div class="flight-stage-card mb-3" id="flightStageCard">
                <canvas id="flightCanvas"></canvas>

                <!-- Floating Celebratory Win Banner -->
                <div id="winBannerCelebration" class="win-celebration-banner" style="display: none;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="win-banner-icon">
                            <i class="fa-solid fa-trophy"></i>
                        </div>
                        <div class="text-start">
                            <div class="win-banner-sub">CASHOUT SUCCESSFUL</div>
                            <div class="win-banner-amount" id="winBannerAmount">+₹0.00</div>
                        </div>
                        <div class="win-banner-mult-badge" id="winBannerMult">@ 0.00x</div>
                    </div>
                </div>

                <!-- Center Dynamic Overlay -->
                <div class="canvas-center-overlay" id="canvasOverlay">
                    <!-- Flying Multiplier -->
                    <div id="flyingMultiplierBox" style="display: {{ ($roundData['status'] ?? 'betting') === 'flying' ? 'block' : 'none' }};">
                        <div class="live-multiplier-text" id="liveMultiplierDigits">{{ number_format((float)($roundData['current_multiplier'] ?? 1.00), 2) }}x</div>
                    </div>

                    <!-- Crashed State -->
                    <div id="crashedBox" style="display: {{ ($roundData['status'] ?? 'betting') === 'crashed' ? 'block' : 'none' }};">
                        <div class="flew-away-text">FLEW AWAY!</div>
                        <div class="text-white-50 fw-bold mt-1" style="font-size: 1.1rem;">
                            CRASHED AT <span class="text-danger font-monospace fs-4" id="crashMultiplierVal">{{ number_format((float)($roundData['crash_multiplier'] ?? 1.00), 2) }}x</span>
                        </div>
                        <div class="text-warning small mt-2 fw-semibold" id="crashedCountdown">
                            Next round in {{ number_format(max(0.5, (float)($roundData['remaining_cooldown_seconds'] ?? 3.0)), 1) }}s...
                        </div>
                    </div>

                    <!-- Betting Countdown State -->
                    <div id="waitingCountdownBox" class="waiting-countdown-box" style="display: {{ ($roundData['status'] ?? 'betting') === 'betting' ? 'block' : 'none' }};">
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                            <i class="fa-solid fa-plane text-danger fa-spin-pulse"></i>
                            <span class="fw-bold text-uppercase tracking-wider" style="font-size: 0.9rem; color: #cbd5e1;">NEXT ROUND IN</span>
                        </div>
                        <div class="text-warning font-monospace fw-bold" style="font-size: 2.8rem; line-height: 1;" id="countdownDigits">
                            {{ number_format(max(0.1, (float)($roundData['remaining_betting_seconds'] ?? 7.0)), 1) }}s
                        </div>
                        <div class="progress-bar-takeoff">
                            <div class="progress-bar-fill" id="countdownProgressBar" style="width: {{ min(100, max(0, ((float)($roundData['remaining_betting_seconds'] ?? 7.0) / max(1, (float)($roundData['betting_duration'] ?? 7))) * 100)) }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dual Bet Controls (Slot 1 & Slot 2) -->
            <div class="row g-1 g-md-3">
                <!-- Bet Slot #1 -->
                <div class="col-6 col-md-6" id="slotCol1">
                    <div class="bet-control-box" id="slotCard1">
                        <div class="d-flex align-items-center justify-content-between mb-1 mb-md-2">
                            <span class="badge bg-dark border border-secondary text-light px-1 px-sm-2 py-0.5 py-sm-1" style="font-size: 0.68rem;">
                                <i class="fa-solid fa-gamepad text-warning me-1"></i><span class="d-none d-sm-inline">BET PANEL </span>#1
                            </span>
                            <div class="form-check form-switch m-0" style="font-size: 0.72rem;">
                                <input class="form-check-input" type="checkbox" id="autoCashoutToggle1">
                                <label class="form-check-label text-secondary fw-semibold ms-1" for="autoCashoutToggle1" style="font-size: 0.68rem;"><span class="d-none d-sm-inline">Auto </span>Cash</label>
                            </div>
                        </div>

                        <div class="row g-1 g-md-2 align-items-center mb-1 mb-md-2">
                            <div class="col-12 col-xl-6">
                                <div class="input-group input-group-sm">
                                    <button class="btn btn-dark border-secondary text-light fw-bold px-1.5 py-0" type="button" onclick="adjustAmount(1, -10)">-</button>
                                    <input type="number" class="form-control bet-input-field px-1 py-0 text-center" id="betAmount1" value="50" min="10" max="50000" step="10">
                                    <button class="btn btn-dark border-secondary text-light fw-bold px-1.5 py-0" type="button" onclick="adjustAmount(1, 10)">+</button>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6">
                                <div class="input-group input-group-sm" id="autoCashoutGroup1" style="opacity: 0.45; pointer-events: none;">
                                    <span class="input-group-text bg-dark border-secondary text-secondary px-1 py-0" style="font-size: 0.68rem;">AUTO</span>
                                    <input type="number" class="form-control bet-input-field px-1 py-0 text-center" id="autoCashoutVal1" value="2.00" min="1.01" step="0.10">
                                    <span class="input-group-text bg-dark border-secondary text-secondary px-1 py-0" style="font-size: 0.68rem;">X</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Chips -->
                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1 mb-md-3">
                            <button class="bet-chip-btn flex-fill" onclick="setAmount(1, 20)">20</button>
                            <button class="bet-chip-btn flex-fill" onclick="setAmount(1, 50)">50</button>
                            <button class="bet-chip-btn flex-fill" onclick="setAmount(1, 100)">100</button>
                            <button class="bet-chip-btn flex-fill" onclick="setAmount(1, 500)">500</button>
                            <button class="bet-chip-btn flex-fill" onclick="doubleAmount(1)">2X</button>
                            <button class="bet-chip-btn flex-fill" onclick="halfAmount(1)">1/2</button>
                        </div>

                        <!-- Action Button Slot 1 -->
                        <button class="btn-action-bet btn-state-bet" id="btnAction1" onclick="handleSlotAction(1)">
                            <span id="btnText1">BET</span>
                            <span class="sub-amt-label" id="btnSubAmt1">₹50.00</span>
                        </button>
                    </div>
                </div>

                <!-- Bet Slot #2 -->
                <div class="col-6 col-md-6" id="slotCol2">
                    <div class="bet-control-box" id="slotCard2">
                        <div class="d-flex align-items-center justify-content-between mb-1 mb-md-2">
                            <span class="badge bg-dark border border-secondary text-light px-1 px-sm-2 py-0.5 py-sm-1" style="font-size: 0.68rem;">
                                <i class="fa-solid fa-gamepad text-info me-1"></i><span class="d-none d-sm-inline">BET PANEL </span>#2
                            </span>
                            <div class="form-check form-switch m-0" style="font-size: 0.72rem;">
                                <input class="form-check-input" type="checkbox" id="autoCashoutToggle2">
                                <label class="form-check-label text-secondary fw-semibold ms-1" for="autoCashoutToggle2" style="font-size: 0.68rem;"><span class="d-none d-sm-inline">Auto </span>Cash</label>
                            </div>
                        </div>

                        <div class="row g-1 g-md-2 align-items-center mb-1 mb-md-2">
                            <div class="col-12 col-xl-6">
                                <div class="input-group input-group-sm">
                                    <button class="btn btn-dark border-secondary text-light fw-bold px-1.5 py-0" type="button" onclick="adjustAmount(2, -10)">-</button>
                                    <input type="number" class="form-control bet-input-field px-1 py-0 text-center" id="betAmount2" value="100" min="10" max="50000" step="10">
                                    <button class="btn btn-dark border-secondary text-light fw-bold px-1.5 py-0" type="button" onclick="adjustAmount(2, 10)">+</button>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6">
                                <div class="input-group input-group-sm" id="autoCashoutGroup2" style="opacity: 0.45; pointer-events: none;">
                                    <span class="input-group-text bg-dark border-secondary text-secondary px-1 py-0" style="font-size: 0.68rem;">AUTO</span>
                                    <input type="number" class="form-control bet-input-field px-1 py-0 text-center" id="autoCashoutVal2" value="3.00" min="1.01" step="0.10">
                                    <span class="input-group-text bg-dark border-secondary text-secondary px-1 py-0" style="font-size: 0.68rem;">X</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Chips -->
                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1 mb-md-3">
                            <button class="bet-chip-btn flex-fill" onclick="setAmount(2, 50)">50</button>
                            <button class="bet-chip-btn flex-fill" onclick="setAmount(2, 100)">100</button>
                            <button class="bet-chip-btn flex-fill" onclick="setAmount(2, 200)">200</button>
                            <button class="bet-chip-btn flex-fill" onclick="setAmount(2, 1000)">1000</button>
                            <button class="bet-chip-btn flex-fill" onclick="doubleAmount(2)">2X</button>
                            <button class="bet-chip-btn flex-fill" onclick="halfAmount(2)">1/2</button>
                        </div>

                        <!-- Action Button Slot 2 -->
                        <button class="btn-action-bet btn-state-bet" id="btnAction2" onclick="handleSlotAction(2)">
                            <span id="btnText2">BET</span>
                            <span class="sub-amt-label" id="btnSubAmt2">₹100.00</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side Panel: Live Room Bets & Player History Tabs -->
        <div class="col-lg-4 col-xl-4">
            <div class="sidebar-panel-card p-3 h-100">
                <!-- Nav Tabs -->
                <ul class="nav nav-pills nav-fill mb-3 bg-dark p-1 rounded-pill border border-secondary" id="roomTabs">
                    <li class="nav-item">
                        <button class="nav-link active rounded-pill py-1 small fw-bold" data-bs-toggle="pill" data-bs-target="#tabAllBets">
                            <i class="fa-solid fa-users me-1"></i> All Bets
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-pill py-1 small fw-bold" data-bs-toggle="pill" data-bs-target="#tabMyBets" onclick="loadMyBets()">
                            <i class="fa-solid fa-user me-1"></i> My Bets
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="roomTabContent">
                    <!-- Tab 1: All Room Players Bets -->
                    <div class="tab-pane fade show active" id="tabAllBets">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-secondary small fw-bold">ACTIVE PLAYERS</span>
                            <span class="badge bg-secondary rounded-pill font-monospace" id="roomBetsCount">{{ count($initialRoomBets ?? []) }} Bets</span>
                        </div>
                        <div style="max-height: 480px; overflow-y: auto;">
                            <table class="table table-dark-custom">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Bet</th>
                                        <th class="text-end">Cash Out</th>
                                    </tr>
                                </thead>
                                <tbody id="roomBetsBody">
                                    @forelse($initialRoomBets ?? [] as $b)
                                        <tr>
                                            <td class="text-light fw-bold">{{ $b['player'] }}</td>
                                            <td class="text-warning font-monospace">₹{{ number_format($b['amount'], 2) }}</td>
                                            <td class="text-end">
                                                @if($b['status'] === 'cashed_out')
                                                    <span class="badge bg-success font-monospace">{{ number_format($b['multiplier'], 2) }}x (+₹{{ number_format($b['win_amount'], 2) }})</span>
                                                @elseif($b['status'] === 'lost')
                                                    <span class="badge bg-danger">Lost</span>
                                                @elseif(($roundData['status'] ?? 'betting') === 'betting' || $b['status'] === 'waiting')
                                                    <span class="badge bg-secondary">Waiting</span>
                                                @else
                                                    <span class="badge bg-secondary">In Flight</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-secondary py-4">Waiting for players to place bets...</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 2: User's Personal Bets -->
                    <div class="tab-pane fade" id="tabMyBets">
                        <div class="card bg-dark border-secondary p-2 mb-3 text-center">
                            <div class="row g-1">
                                <div class="col-4">
                                    <div class="text-secondary" style="font-size: 0.7rem;">WAGERED</div>
                                    <div class="fw-bold text-light font-monospace" id="statWagered">₹0.00</div>
                                </div>
                                <div class="col-4">
                                    <div class="text-secondary" style="font-size: 0.7rem;">WON</div>
                                    <div class="fw-bold text-success font-monospace" id="statWon">₹0.00</div>
                                </div>
                                <div class="col-4">
                                    <div class="text-secondary" style="font-size: 0.7rem;">NET P/L</div>
                                    <div class="fw-bold font-monospace" id="statNet">₹0.00</div>
                                </div>
                            </div>
                        </div>

                        <div style="max-height: 420px; overflow-y: auto;">
                            <table class="table table-dark-custom">
                                <thead>
                                    <tr>
                                        <th>Round</th>
                                        <th>Bet</th>
                                        <th>X</th>
                                        <th class="text-end">Win</th>
                                    </tr>
                                </thead>
                                <tbody id="myBetsBody">
                                    <tr>
                                        <td colspan="4" class="text-center text-secondary py-4">Loading your bets...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- How To Play Rules Modal -->
<div class="modal fade" id="rulesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold text-warning"><i class="fa-solid fa-circle-info me-2"></i> How to Play Aviator</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="font-size: 0.9rem; line-height: 1.6;">
                <ol class="ps-3">
                    <li class="mb-2"><strong>Place your Bet:</strong> Select your bet amount in Panel 1 (and optionally Panel 2) before the 7-second countdown timer expires.</li>
                    <li class="mb-2"><strong>Watch the Plane Take Off:</strong> As the plane takes off, the multiplier curve starts ascending from <strong>1.00x</strong> upwards.</li>
                    <li class="mb-2"><strong>Cash Out in Time:</strong> Click the giant <span class="text-warning fw-bold">CASH OUT</span> button before the plane flies away! Your win is your bet multiplied by the cash out multiplier.</li>
                    <li class="mb-2"><strong>Flew Away:</strong> If the plane flies away before you cash out, the bet is lost.</li>
                    <li class="mb-2"><strong>Auto Cashout:</strong> Turn on Auto Cashout to automatically claim your winnings when your target multiplier is hit.</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Toast Popup Notification -->
<div class="av-toast" id="avToast">
    <i class="fa-solid fa-circle-info" id="toastIcon"></i>
    <span id="toastMsg">Notification message</span>
</div>

@endsection

@section('scripts')
<script>
    /* =========================================================================
     * AVIATOR ENGINE CLIENT SCRIPT
     * HTML5 Canvas 60 FPS, Web Audio Synthesizer, Dynamic Double Bet State
     * ========================================================================= */

    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const IS_AUTHENTICATED = {{ Auth::check() ? 'true' : 'false' }};

    // State Machine
    let currentRoundId = '{{ $roundData['round_id'] }}';
    let gameState = '{{ $roundData['status'] }}'; // 'betting', 'flying', 'crashed'
    let currentMultiplier = {{ (float) ($roundData['current_multiplier'] ?? 1.00) }};
    let remainingBettingSeconds = {{ (float) ($roundData['remaining_betting_seconds'] ?? 7.0) }};
    let remainingCooldownSeconds = {{ (float) ($roundData['remaining_cooldown_seconds'] ?? 0.0) }};
    let bettingDuration = {{ (int) ($roundData['betting_duration'] ?? 7) }};
    let crashCooldown = {{ (int) ($roundData['crash_cooldown'] ?? 3) }};
    let flightElapsedSeconds = {{ (float) ($roundData['flight_elapsed_seconds'] ?? 0.0) }};
    let crashMultiplier = {{ $roundData['crash_multiplier'] ? (float)$roundData['crash_multiplier'] : 'null' }};

    // Player Bet Slots State
    // slot: { betId: null, amount: 50, autoCashout: null, isBetPlaced: false, isCashedOut: false }
    const playerSlots = {
        1: { betId: null, amount: 50, autoCashout: null, isBetPlaced: false, isCashedOut: false },
        2: { betId: null, amount: 100, autoCashout: null, isBetPlaced: false, isCashedOut: false }
    };

    // Canvas Setup
    const canvas = document.getElementById('flightCanvas');
    const ctx = canvas.getContext('2d');
    let canvasWidth = 0;
    let canvasHeight = 0;

    function resizeCanvas() {
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * window.devicePixelRatio;
        canvas.height = rect.height * window.devicePixelRatio;
        canvasWidth = canvas.width;
        canvasHeight = canvas.height;
        ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
    }
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    /* =========================================================================
     * WEB AUDIO SYNTHESIZER (Cinema-grade procedural FX, zero latency)
     * ========================================================================= */
    let audioCtx = null;
    let isSoundEnabled = localStorage.getItem('aviator_sound') !== 'false';

    function initAudio() {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
    }

    // Auto-unlock audio context on first user interaction
    ['click', 'touchstart', 'keydown', 'pointerdown'].forEach(evt => {
        document.addEventListener(evt, () => {
            initAudio();
        }, { once: true });
    });

    function playBeep(freq = 600, duration = 0.08) {
        if (!isSoundEnabled) return;
        try {
            initAudio();
            const now = audioCtx.currentTime;
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.frequency.setValueAtTime(freq, now);
            gain.gain.setValueAtTime(0.12, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + duration);
            osc.start(now);
            osc.stop(now + duration);
        } catch(e) {}
    }

    function playCashoutSound() {
        if (!isSoundEnabled) return;
        try {
            initAudio();
            const now = audioCtx.currentTime;
            // High-octave victory chime chord
            [523.25, 659.25, 783.99, 1046.50].forEach((freq, idx) => {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'triangle';
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                const startTime = now + (idx * 0.05);
                osc.frequency.setValueAtTime(freq, startTime);
                gain.gain.setValueAtTime(0.2, startTime);
                gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.4);
                osc.start(startTime);
                osc.stop(startTime + 0.4);
            });
        } catch(e) {}
    }

    /**
     * Authentic Aviator Crash & Sonic Shockwave Sound:
     * Layer 1: Filtered White Noise Explosion Blast (KABOOM)
     * Layer 2: Deep Sub-Bass Rumble Punch (120Hz -> 25Hz)
     * Layer 3: Jet Engine Stall & Supersonic Fly-Away Whoosh
     * Layer 4: Sharp Transient Impact Click
     */
    function playCrashSound() {
        if (!isSoundEnabled) return;
        try {
            initAudio();
            const now = audioCtx.currentTime;

            // --- LAYER 1: CINEMATIC EXPLOSION NOISE BLAST ---
            const bufferSize = Math.floor(audioCtx.sampleRate * 0.85);
            const noiseBuffer = audioCtx.createBuffer(1, bufferSize, audioCtx.sampleRate);
            const channelData = noiseBuffer.getChannelData(0);
            for (let i = 0; i < bufferSize; i++) {
                channelData[i] = (Math.random() * 2 - 1) * Math.exp(-3.2 * (i / bufferSize));
            }

            const noiseSource = audioCtx.createBufferSource();
            noiseSource.buffer = noiseBuffer;

            // Lowpass sweep to give deep volcanic explosion rumble
            const noiseFilter = audioCtx.createBiquadFilter();
            noiseFilter.type = 'lowpass';
            noiseFilter.Q.setValueAtTime(3.0, now);
            noiseFilter.frequency.setValueAtTime(1400, now);
            noiseFilter.frequency.exponentialRampToValueAtTime(70, now + 0.8);

            const noiseGain = audioCtx.createGain();
            noiseGain.gain.setValueAtTime(0.55, now);
            noiseGain.gain.exponentialRampToValueAtTime(0.001, now + 0.85);

            noiseSource.connect(noiseFilter);
            noiseFilter.connect(noiseGain);
            noiseGain.connect(audioCtx.destination);

            noiseSource.start(now);
            noiseSource.stop(now + 0.85);

            // --- LAYER 2: VISCERAL SUB-BASS BOOM ---
            const subOsc = audioCtx.createOscillator();
            const subGain = audioCtx.createGain();
            subOsc.type = 'sine';
            subOsc.frequency.setValueAtTime(120, now);
            subOsc.frequency.exponentialRampToValueAtTime(25, now + 0.7);

            subGain.gain.setValueAtTime(0.65, now);
            subGain.gain.exponentialRampToValueAtTime(0.001, now + 0.7);

            subOsc.connect(subGain);
            subGain.connect(audioCtx.destination);
            subOsc.start(now);
            subOsc.stop(now + 0.7);

            // --- LAYER 3: JET FLYAWAY WHOOSH & TURBINE STALL ---
            const jetOsc = audioCtx.createOscillator();
            const jetGain = audioCtx.createGain();
            const jetFilter = audioCtx.createBiquadFilter();

            jetOsc.type = 'sawtooth';
            jetOsc.frequency.setValueAtTime(520, now);
            jetOsc.frequency.exponentialRampToValueAtTime(50, now + 0.55);

            jetFilter.type = 'bandpass';
            jetFilter.Q.setValueAtTime(2.0, now);
            jetFilter.frequency.setValueAtTime(800, now);
            jetFilter.frequency.exponentialRampToValueAtTime(120, now + 0.55);

            jetGain.gain.setValueAtTime(0.25, now);
            jetGain.gain.exponentialRampToValueAtTime(0.001, now + 0.55);

            jetOsc.connect(jetFilter);
            jetFilter.connect(jetGain);
            jetGain.connect(audioCtx.destination);
            jetOsc.start(now);
            jetOsc.stop(now + 0.55);

            // --- LAYER 4: SHARP TRANSIENT IMPACT CLICK ---
            const clickOsc = audioCtx.createOscillator();
            const clickGain = audioCtx.createGain();
            clickOsc.type = 'triangle';
            clickOsc.frequency.setValueAtTime(260, now);
            clickOsc.frequency.exponentialRampToValueAtTime(30, now + 0.08);

            clickGain.gain.setValueAtTime(0.4, now);
            clickGain.gain.exponentialRampToValueAtTime(0.001, now + 0.08);

            clickOsc.connect(clickGain);
            clickGain.connect(audioCtx.destination);
            clickOsc.start(now);
            clickOsc.stop(now + 0.08);

        } catch(e) {
            console.warn('Crash audio error:', e);
        }
    }

    function playTakeoffSound() {
        if (!isSoundEnabled) return;
        try {
            initAudio();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'sine';
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.frequency.setValueAtTime(140, audioCtx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(540, audioCtx.currentTime + 1.0);
            gain.gain.setValueAtTime(0.01, audioCtx.currentTime);
            gain.gain.linearRampToValueAtTime(0.12, audioCtx.currentTime + 0.3);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 1.0);
            osc.start();
            osc.stop(audioCtx.currentTime + 1.0);
        } catch(e) {}
    }

    // Sound toggle button
    document.getElementById('btnSoundToggle').addEventListener('click', () => {
        isSoundEnabled = !isSoundEnabled;
        localStorage.setItem('aviator_sound', isSoundEnabled);
        updateSoundIcon();
    });

    function updateSoundIcon() {
        const icon = document.getElementById('soundIcon');
        if (isSoundEnabled) {
            icon.className = 'fa-solid fa-volume-high text-warning';
        } else {
            icon.className = 'fa-solid fa-volume-xmark text-secondary';
        }
    }
    updateSoundIcon();

    /* =========================================================================
     * CANVAS RENDER LOOP (60 FPS) & PARTICLE ENGINES
     * ========================================================================= */
    let animationFrameId = null;
    let planeAnimTime = 0;
    let lastPlaneX = 60;
    let lastPlaneY = 280;

    // Background Atmosphere Starfield & Speed Particles
    const starParticles = [];
    const TOTAL_STARS = 65;
    for (let i = 0; i < TOTAL_STARS; i++) {
        starParticles.push({
            x: Math.random() * 1200,
            y: Math.random() * 450,
            size: Math.random() * 1.8 + 0.6,
            speed: Math.random() * 1.5 + 0.5,
            alpha: Math.random() * 0.7 + 0.3
        });
    }

    // Afterburner Flame & Explosion Debris Particles
    let flameParticles = [];
    let explosionParticles = [];

    function renderFrame() {
        const w = canvas.getBoundingClientRect().width;
        const h = canvas.getBoundingClientRect().height;

        ctx.clearRect(0, 0, w, h);

        // 1. Atmosphere starfield & speed streaks
        drawAtmosphere(w, h);

        // 2. High-tech radar coordinate grid with altitude labels
        drawRadarGrid(w, h);

        // 3. Render state
        if (gameState === 'flying') {
            planeAnimTime += 0.016;
            drawFlightCurve(w, h);
            updateAndDrawFlames();
        } else if (gameState === 'betting') {
            drawRunway(w, h);
        }

        // 4. Explosion debris sparks (if active)
        if (explosionParticles.length > 0) {
            updateAndDrawExplosion();
        }

        animationFrameId = requestAnimationFrame(renderFrame);
    }

    function drawAtmosphere(w, h) {
        // Particles drift and streak faster as multiplier rises
        const speedFactor = (gameState === 'flying') ? (1.5 + Math.min(8.0, currentMultiplier * 0.35)) : 0.6;

        ctx.save();
        for (let i = 0; i < starParticles.length; i++) {
            const p = starParticles[i];
            p.x -= p.speed * speedFactor;
            if (p.x < -20) {
                p.x = w + Math.random() * 20;
                p.y = Math.random() * h;
            }

            ctx.fillStyle = `rgba(255, 255, 255, ${p.alpha})`;
            if (gameState === 'flying' && speedFactor > 2.5) {
                // Supersonic light streak
                ctx.strokeStyle = `rgba(255, 255, 255, ${p.alpha * 0.7})`;
                ctx.lineWidth = p.size;
                ctx.beginPath();
                ctx.moveTo(p.x, p.y);
                ctx.lineTo(p.x + p.speed * speedFactor * 1.8, p.y);
                ctx.stroke();
            } else {
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }
        ctx.restore();
    }

    function drawRadarGrid(w, h) {
        ctx.save();
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.04)';
        ctx.lineWidth = 1;

        const numLinesX = 7;
        const numLinesY = 5;

        // Vertical Time Lines
        for (let i = 1; i <= numLinesX; i++) {
            const x = (w / (numLinesX + 1)) * i;
            ctx.beginPath();
            ctx.moveTo(x, 0);
            ctx.lineTo(x, h - 25);
            ctx.stroke();

            // Time label at bottom
            ctx.fillStyle = 'rgba(148, 163, 184, 0.4)';
            ctx.font = '10px "JetBrains Mono", monospace';
            ctx.fillText(`${i * 3}s`, x - 8, h - 10);
        }

        // Horizontal Altitude Lines
        const altitudeBenchmarks = ['1.2x', '1.5x', '2.0x', '5.0x', '10.0x'];
        for (let j = 1; j <= numLinesY; j++) {
            const y = (h / (numLinesY + 1)) * j;
            ctx.beginPath();
            ctx.moveTo(35, y);
            ctx.lineTo(w, y);
            ctx.stroke();

            // Altitude benchmark on left
            ctx.fillStyle = 'rgba(148, 163, 184, 0.45)';
            ctx.font = '10px "JetBrains Mono", monospace';
            const mark = altitudeBenchmarks[numLinesY - j] || `${j}x`;
            ctx.fillText(mark, 6, y + 3);
        }

        // Radar corner axis borders
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.12)';
        ctx.beginPath();
        ctx.moveTo(35, 0);
        ctx.lineTo(35, h - 25);
        ctx.lineTo(w, h - 25);
        ctx.stroke();

        ctx.restore();
    }

    function drawFlightCurve(w, h) {
        const startX = 40;
        const startY = h - 35;

        // Smooth flight path curve
        const progress = Math.min(1.0, flightElapsedSeconds / 18.0);
        const endX = startX + (w - startX - 75) * Math.min(1.0, 0.2 + progress * 0.8);
        const endY = startY - (h - 95) * Math.min(1.0, 0.15 + Math.pow(progress, 0.82) * 0.85);

        lastPlaneX = endX;
        lastPlaneY = endY;

        const cpX = startX + (endX - startX) * 0.42;
        const cpY = startY - 8;

        // Dynamic Chromatic Color Tiers:
        // Tier 1 (< 2.00x): Electric Cyan
        // Tier 2 (2.00x - 9.99x): Neon Magenta / Violet
        // Tier 3 (10.00x+): Blazing Sun Gold
        let primaryColor, glowColor, fillAlpha;
        if (currentMultiplier >= 10.0) {
            primaryColor = '#fbbf24';
            glowColor = 'rgba(245, 158, 11, 0.75)';
            fillAlpha = 0.35;
        } else if (currentMultiplier >= 2.0) {
            primaryColor = '#d946ef';
            glowColor = 'rgba(217, 70, 239, 0.65)';
            fillAlpha = 0.28;
        } else {
            primaryColor = '#38bdf8';
            glowColor = 'rgba(56, 189, 248, 0.55)';
            fillAlpha = 0.22;
        }

        // 1. Glowing gradient under curve
        const fillGrad = ctx.createLinearGradient(0, endY, 0, startY);
        fillGrad.addColorStop(0, glowColor);
        fillGrad.addColorStop(1, 'rgba(0, 0, 0, 0)');

        ctx.beginPath();
        ctx.moveTo(startX, startY);
        ctx.quadraticCurveTo(cpX, cpY, endX, endY);
        ctx.lineTo(endX, startY);
        ctx.closePath();
        ctx.fillStyle = fillGrad;
        ctx.fill();

        // 2. Thick Soft Bloom Curve
        ctx.save();
        ctx.beginPath();
        ctx.moveTo(startX, startY);
        ctx.quadraticCurveTo(cpX, cpY, endX, endY);
        ctx.strokeStyle = glowColor;
        ctx.lineWidth = 8;
        ctx.shadowColor = primaryColor;
        ctx.shadowBlur = 18;
        ctx.stroke();

        // 3. Crisp Core Line
        ctx.lineWidth = 3.5;
        ctx.strokeStyle = primaryColor;
        ctx.shadowBlur = 0;
        ctx.stroke();
        ctx.restore();

        // 4. Emit Afterburner Flame Particles from Engine Nozzle
        emitEngineFlames(endX, endY);

        // 5. Draw Supersonic Airplane Graphic
        drawAirplane(endX, endY, progress);
    }

    function emitEngineFlames(x, y) {
        if (gameState !== 'flying') return;
        const nozzleX = x - 24;
        const nozzleY = y + 3;

        // Spawn 3 flame particles per frame
        for (let i = 0; i < 3; i++) {
            flameParticles.push({
                x: nozzleX,
                y: nozzleY + (Math.random() - 0.5) * 6,
                vx: -(Math.random() * 4 + 3.5) * (1 + Math.min(2.0, currentMultiplier * 0.12)),
                vy: (Math.random() - 0.5) * 2,
                size: Math.random() * 4 + 3,
                life: 1.0,
                decay: Math.random() * 0.07 + 0.05
            });
        }
    }

    function updateAndDrawFlames() {
        ctx.save();
        for (let i = flameParticles.length - 1; i >= 0; i--) {
            const p = flameParticles[i];
            p.x += p.vx;
            p.y += p.vy;
            p.life -= p.decay;

            if (p.life <= 0) {
                flameParticles.splice(i, 1);
                continue;
            }

            // Fiery gradient: White core -> Yellow -> Orange -> Smoke
            let color;
            if (p.life > 0.7) {
                color = `rgba(255, 255, 255, ${p.life})`;
            } else if (p.life > 0.4) {
                color = `rgba(254, 240, 138, ${p.life * 0.9})`;
            } else {
                color = `rgba(249, 115, 22, ${p.life * 0.75})`;
            }

            ctx.fillStyle = color;
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.size * p.life, 0, Math.PI * 2);
            ctx.fill();
        }
        ctx.restore();
    }

    function drawAirplane(x, y, progress) {
        ctx.save();
        ctx.translate(x, y);

        // Dynamic pitch angle tilt
        const angle = -0.26 - (Math.sin(planeAnimTime * 3) * 0.04);
        ctx.rotate(angle);

        // Shadow & Glow
        ctx.shadowColor = 'rgba(225, 29, 72, 0.85)';
        ctx.shadowBlur = 18;

        // 1. Supersonic Jet Fuselage (Aerodynamic Crimson Metallic)
        const bodyGrad = ctx.createLinearGradient(-26, -5, 26, 5);
        bodyGrad.addColorStop(0, '#be123c');
        bodyGrad.addColorStop(0.5, '#f43f5e');
        bodyGrad.addColorStop(1, '#e11d48');

        ctx.fillStyle = bodyGrad;
        ctx.beginPath();
        ctx.moveTo(-26, 0);
        ctx.lineTo(16, -5);
        ctx.lineTo(26, 0);
        ctx.lineTo(16, 5);
        ctx.closePath();
        ctx.fill();

        // 2. Cockpit Canopy Glass (Ice Cyan Specular Glow)
        ctx.fillStyle = 'rgba(186, 230, 253, 0.85)';
        ctx.beginPath();
        ctx.moveTo(3, -3);
        ctx.lineTo(15, -2);
        ctx.lineTo(18, 0);
        ctx.lineTo(6, 0);
        ctx.closePath();
        ctx.fill();

        // 3. Swept Delta Wings
        ctx.fillStyle = '#be123c';
        // Top Wing
        ctx.beginPath();
        ctx.moveTo(-6, 0);
        ctx.lineTo(-14, -24);
        ctx.lineTo(2, -24);
        ctx.lineTo(8, 0);
        ctx.closePath();
        ctx.fill();

        // Bottom Wing
        ctx.beginPath();
        ctx.moveTo(-6, 0);
        ctx.lineTo(-14, 20);
        ctx.lineTo(2, 20);
        ctx.lineTo(8, 0);
        ctx.closePath();
        ctx.fill();

        // 4. Wingtip Navigation Beacons
        // Starboard (Green Light)
        ctx.fillStyle = '#10b981';
        ctx.beginPath();
        ctx.arc(-13, -24, 2.5, 0, Math.PI * 2);
        ctx.fill();

        // Port (Red Light)
        ctx.fillStyle = '#ef4444';
        ctx.beginPath();
        ctx.arc(-13, 20, 2.5, 0, Math.PI * 2);
        ctx.fill();

        // 5. Tail Fin
        ctx.fillStyle = '#fda4af';
        ctx.beginPath();
        ctx.moveTo(-24, 0);
        ctx.lineTo(-30, -14);
        ctx.lineTo(-20, -14);
        ctx.closePath();
        ctx.fill();

        // 6. Afterburner Engine Throat Glow at rear
        ctx.fillStyle = 'rgba(254, 240, 138, 0.95)';
        ctx.beginPath();
        ctx.arc(-26, 0, 4, 0, Math.PI * 2);
        ctx.fill();

        // 7. Spinning Nose Pitot Arc
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.8)';
        ctx.lineWidth = 1.5;
        const propRadius = 8 + (Math.sin(planeAnimTime * 35) * 1.5);
        ctx.beginPath();
        ctx.arc(26, 0, propRadius, -Math.PI / 3, Math.PI / 3);
        ctx.stroke();

        ctx.restore();
    }

    function drawRunway(w, h) {
        // High-tech runway track
        ctx.save();
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.15)';
        ctx.lineWidth = 2;
        ctx.setLineDash([14, 10]);
        ctx.beginPath();
        ctx.moveTo(35, h - 35);
        ctx.lineTo(w - 30, h - 35);
        ctx.stroke();
        ctx.setLineDash([]);

        // Ready-to-launch idle airplane
        drawAirplane(70, h - 45, 0);
        ctx.restore();
    }

    // Crash Explosion Effect
    function triggerCrashExplosion(x, y) {
        const card = document.getElementById('flightStageCard');
        if (card) {
            card.classList.remove('shake');
            void card.offsetWidth; // force reflow
            card.classList.add('shake');
            setTimeout(() => card.classList.remove('shake'), 450);
        }

        explosionParticles = [];
        const colors = ['#f43f5e', '#f97316', '#fbbf24', '#ffffff', '#e11d48'];
        for (let i = 0; i < 45; i++) {
            const angle = Math.random() * Math.PI * 2;
            const speed = Math.random() * 8 + 2;
            explosionParticles.push({
                x: x,
                y: y,
                vx: Math.cos(angle) * speed,
                vy: Math.sin(angle) * speed,
                color: colors[Math.floor(Math.random() * colors.length)],
                size: Math.random() * 4 + 2,
                alpha: 1.0,
                decay: Math.random() * 0.04 + 0.02
            });
        }
    }

    function updateAndDrawExplosion() {
        ctx.save();
        for (let i = explosionParticles.length - 1; i >= 0; i--) {
            const p = explosionParticles[i];
            p.x += p.vx;
            p.y += p.vy;
            p.vy += 0.12; // gravity
            p.alpha -= p.decay;

            if (p.alpha <= 0) {
                explosionParticles.splice(i, 1);
                continue;
            }

            ctx.fillStyle = p.color;
            ctx.globalAlpha = p.alpha;
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
            ctx.fill();
        }
        ctx.globalAlpha = 1.0;
        ctx.restore();
    }

    // Win Celebration Notification Banner
    let winCelebrationTimeout = null;
    function showWinCelebration(amount, mult) {
        const banner = document.getElementById('winBannerCelebration');
        if (!banner) return;
        document.getElementById('winBannerAmount').innerText = '+₹' + parseFloat(amount).toFixed(2);
        document.getElementById('winBannerMult').innerText = '@ ' + parseFloat(mult).toFixed(2) + 'x';
        banner.style.display = 'block';

        if (winCelebrationTimeout) clearTimeout(winCelebrationTimeout);
        winCelebrationTimeout = setTimeout(() => {
            banner.style.display = 'none';
        }, 3600);
    }

    animationFrameId = requestAnimationFrame(renderFrame);

    /* =========================================================================
     * STATE SYNCHRONIZATION & HIGH SPEED POLLING
     * ========================================================================= */
    let pollInterval = null;
    let localFrameTicker = null;

    function startLiveSync() {
        fetchLiveStatus();
        pollInterval = setInterval(fetchLiveStatus, 1000);

        // High frequency frame ticker (every 50ms) for smooth multiplier ascent and countdown
        localFrameTicker = setInterval(() => {
            if (gameState === 'flying') {
                flightElapsedSeconds += 0.05;
                // M(t) = e^(0.065 * t)
                const computed = Math.round(Math.exp(0.065 * flightElapsedSeconds) * 100) / 100;
                currentMultiplier = Math.max(1.00, computed);

                // STRICT SYNC: Flight must stop and crash at EXACT crash multiplier (zero overshoot)
                if (crashMultiplier && currentMultiplier >= crashMultiplier) {
                    currentMultiplier = crashMultiplier;
                    const multEl = document.getElementById('liveMultiplierDigits');
                    if (multEl) multEl.innerText = currentMultiplier.toFixed(2) + 'x';

                    gameState = 'crashed';
                    handleStateChange('flying', 'crashed', {
                        crash_multiplier: crashMultiplier,
                        remaining_cooldown_seconds: crashCooldown || 3.0
                    });
                    updateStageOverlay({ status: 'crashed', crash_multiplier: crashMultiplier });
                    return;
                }

                const multEl = document.getElementById('liveMultiplierDigits');
                multEl.innerText = currentMultiplier.toFixed(2) + 'x';
                if (currentMultiplier >= 10.0) {
                    multEl.className = 'live-multiplier-text mult-tier-high';
                } else if (currentMultiplier >= 2.0) {
                    multEl.className = 'live-multiplier-text mult-tier-mid';
                } else {
                    multEl.className = 'live-multiplier-text';
                }

                // Check auto-cashouts locally
                checkAutoCashout(1);
                checkAutoCashout(2);

                // Update cashout button live amounts
                updateCashoutButtonLabels();
            } else if (gameState === 'betting') {
                if (remainingBettingSeconds > 0) {
                    remainingBettingSeconds = Math.max(0, remainingBettingSeconds - 0.05);
                    const displaySec = remainingBettingSeconds > 0.05 ? remainingBettingSeconds.toFixed(1) : '0.0';
                    document.getElementById('countdownDigits').innerText = displaySec + 's';
                    const pct = Math.min(100, Math.max(0, (remainingBettingSeconds / (bettingDuration || 7)) * 100));
                    document.getElementById('countdownProgressBar').style.width = pct + '%';
                } else {
                    document.getElementById('countdownDigits').innerText = '0.0s';
                    document.getElementById('countdownProgressBar').style.width = '0%';
                }
            } else if (gameState === 'crashed') {
                if (remainingCooldownSeconds > 0) {
                    remainingCooldownSeconds = Math.max(0, remainingCooldownSeconds - 0.05);
                    const coolEl = document.getElementById('crashedCountdown');
                    if (coolEl) {
                        coolEl.innerText = `Next round in ${remainingCooldownSeconds.toFixed(1)}s...`;
                    }
                }
            }
        }, 50);
    }

    async function fetchLiveStatus() {
        try {
            const res = await fetch('{{ route('api.aviator.status') }}');
            if (!res.ok) return;
            const data = await res.json();
            if (!data.success) return;

            // Always sync exact crashMultiplier from server
            if (data.crash_multiplier) {
                crashMultiplier = parseFloat(data.crash_multiplier);
            }

            // Detect new round rollover
            if (data.round_id !== currentRoundId) {
                currentRoundId = data.round_id;
                document.getElementById('headerRoundId').innerText = currentRoundId;
                crashMultiplier = data.crash_multiplier ? parseFloat(data.crash_multiplier) : null;
                flightElapsedSeconds = 0;
                currentMultiplier = 1.00;
                if (data.status === 'betting') {
                    remainingBettingSeconds = (typeof data.remaining_betting_seconds === 'number' && data.remaining_betting_seconds > 0)
                        ? data.remaining_betting_seconds
                        : (data.betting_duration || 7.0);
                    document.getElementById('countdownDigits').innerText = remainingBettingSeconds.toFixed(1) + 's';
                    document.getElementById('countdownProgressBar').style.width = '100%';
                }
                resetSlotsForNewRound();
            }

            // Sync Game State
            const prevState = gameState;
            gameState = data.status;
            bettingDuration = data.betting_duration || 7;
            flightElapsedSeconds = data.flight_elapsed_seconds || 0;

            // Continually sync countdown timers with server
            if (gameState === 'betting') {
                if (typeof data.remaining_betting_seconds === 'number') {
                    if (remainingBettingSeconds <= 0 || Math.abs(remainingBettingSeconds - data.remaining_betting_seconds) > 0.35) {
                        remainingBettingSeconds = data.remaining_betting_seconds;
                        document.getElementById('countdownDigits').innerText = remainingBettingSeconds.toFixed(1) + 's';
                        const pct = Math.min(100, Math.max(0, (remainingBettingSeconds / bettingDuration) * 100));
                        document.getElementById('countdownProgressBar').style.width = pct + '%';
                    }
                }
            } else if (gameState === 'crashed') {
                if (typeof data.remaining_cooldown_seconds === 'number') {
                    remainingCooldownSeconds = data.remaining_cooldown_seconds;
                    const coolEl = document.getElementById('crashedCountdown');
                    if (coolEl) {
                        coolEl.innerText = `Next round in ${remainingCooldownSeconds.toFixed(1)}s...`;
                    }
                }
            }

            if (data.balance !== undefined && IS_AUTHENTICATED) {
                document.getElementById('userWalletBalance').innerText = '₹' + parseFloat(data.balance).toFixed(2);
            }

            // Sync Multipliers with strict cap matching crashMultiplier
            if (gameState === 'flying') {
                currentMultiplier = Math.max(currentMultiplier, data.current_multiplier);
                if (crashMultiplier && currentMultiplier >= crashMultiplier) {
                    currentMultiplier = crashMultiplier;
                }
            } else if (gameState === 'crashed') {
                if (data.crash_multiplier) {
                    crashMultiplier = parseFloat(data.crash_multiplier);
                }
                currentMultiplier = crashMultiplier || currentMultiplier;
            }

            // State changes audio & UI triggers
            if (prevState !== gameState) {
                handleStateChange(prevState, gameState, data);
            }

            // Update UI elements visibility
            updateStageOverlay(data);

            // Sync Active Bets from server
            if (data.my_active_bets && data.my_active_bets.length > 0) {
                syncMyActiveBets(data.my_active_bets);
            }

            // Update room active players table
            if (data.room_bets) {
                renderRoomBets(data.room_bets);
            }

            // Multiplier history strip
            if (data.recent_multipliers) {
                updateHistoryStrip(data.recent_multipliers);
            }

        } catch (err) {
            console.error('Status sync error:', err);
        }
    }

    function handleStateChange(oldState, newState, data) {
        if (newState === 'flying') {
            // Flight takeoff start
            playTakeoffSound();
        } else if (newState === 'crashed') {
            // Crashed!
            playCrashSound();
            triggerCrashExplosion(lastPlaneX, lastPlaneY);
            document.getElementById('crashMultiplierVal').innerText = (data.crash_multiplier || currentMultiplier).toFixed(2) + 'x';
            remainingCooldownSeconds = (typeof data.remaining_cooldown_seconds === 'number')
                ? data.remaining_cooldown_seconds
                : 3.0;

            // Mark uncashed player slots as lost
            [1, 2].forEach(slot => {
                if (playerSlots[slot].isBetPlaced && !playerSlots[slot].isCashedOut) {
                    playerSlots[slot].isBetPlaced = false;
                    renderSlotButton(slot, 'lost');
                }
            });
            loadMyBets(true);
        } else if (newState === 'betting') {
            // New betting round countdown start
            remainingBettingSeconds = (typeof data.remaining_betting_seconds === 'number' && data.remaining_betting_seconds > 0)
                ? data.remaining_betting_seconds
                : (data.betting_duration || 7.0);
            document.getElementById('countdownDigits').innerText = remainingBettingSeconds.toFixed(1) + 's';
            document.getElementById('countdownProgressBar').style.width = '100%';
            resetSlotsForNewRound();
        }
    }

    function updateStageOverlay(data) {
        const flyingBox = document.getElementById('flyingMultiplierBox');
        const crashedBox = document.getElementById('crashedBox');
        const waitingBox = document.getElementById('waitingCountdownBox');

        if (gameState === 'flying') {
            flyingBox.style.display = 'block';
            crashedBox.style.display = 'none';
            waitingBox.style.display = 'none';
            document.getElementById('liveMultiplierDigits').innerText = currentMultiplier.toFixed(2) + 'x';
        } else if (gameState === 'crashed') {
            flyingBox.style.display = 'none';
            crashedBox.style.display = 'block';
            waitingBox.style.display = 'none';
        } else {
            // betting
            flyingBox.style.display = 'none';
            crashedBox.style.display = 'none';
            waitingBox.style.display = 'block';
        }
    }

    function updateHistoryStrip(multipliers) {
        const strip = document.getElementById('historyStrip');
        let html = '<span class="text-secondary small fw-bold me-2"><i class="fa-solid fa-clock-rotate-left"></i> HISTORY:</span>';
        multipliers.forEach(m => {
            const val = parseFloat(m);
            const pillClass = (val >= 10.0) ? 'pill-high' : ((val >= 2.0) ? 'pill-mid' : 'pill-low');
            html += `<span class="mult-pill ${pillClass}">${val.toFixed(2)}x</span>`;
        });
        strip.innerHTML = html;
    }

    function renderRoomBets(roomBets) {
        const tbody = document.getElementById('roomBetsBody');
        document.getElementById('roomBetsCount').innerText = roomBets.length + ' Bets';

        if (roomBets.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-secondary py-4">Waiting for players to place bets...</td></tr>';
            return;
        }

        let html = '';
        roomBets.forEach(b => {
            let statusBadge = '<span class="badge bg-secondary">In Flight</span>';
            if (b.status === 'cashed_out') {
                statusBadge = `<span class="badge bg-success font-monospace">${b.multiplier.toFixed(2)}x (+₹${b.win_amount.toFixed(2)})</span>`;
            } else if (b.status === 'lost') {
                statusBadge = '<span class="badge bg-danger">Lost</span>';
            } else if (b.status === 'waiting' || gameState === 'betting') {
                statusBadge = '<span class="badge bg-secondary">Waiting</span>';
            }

            html += `
                <tr>
                    <td class="text-light fw-bold">${b.player}</td>
                    <td class="text-warning font-monospace">₹${b.amount.toFixed(2)}</td>
                    <td class="text-end">${statusBadge}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function syncMyActiveBets(activeBets) {
        activeBets.forEach(bet => {
            const slot = bet.bet_slot;
            if (playerSlots[slot]) {
                playerSlots[slot].betId = bet.id;
                playerSlots[slot].amount = bet.amount;

                if (bet.status === 'pending') {
                    playerSlots[slot].isBetPlaced = true;
                    playerSlots[slot].isCashedOut = false;

                    if (gameState === 'flying') {
                        renderSlotButton(slot, 'cashout');
                    } else if (gameState === 'betting') {
                        renderSlotButton(slot, 'cancel');
                    }
                } else if (bet.status === 'cashed_out') {
                    playerSlots[slot].isBetPlaced = true;
                    playerSlots[slot].isCashedOut = true;
                    renderSlotButton(slot, 'cashed_out', bet.win_amount, bet.cashout_multiplier);
                }
            }
        });
    }

    function resetSlotsForNewRound() {
        [1, 2].forEach(slot => {
            playerSlots[slot].betId = null;
            playerSlots[slot].isBetPlaced = false;
            playerSlots[slot].isCashedOut = false;
            renderSlotButton(slot, 'bet');
        });
    }

    /* =========================================================================
     * BET CONTROLS & AMOUNT ADJUSTMENTS
     * ========================================================================= */
    function adjustAmount(slot, delta) {
        const input = document.getElementById(`betAmount${slot}`);
        let current = parseFloat(input.value) || 10;
        let next = Math.max(10, Math.min(50000, current + delta));
        input.value = next;
        playerSlots[slot].amount = next;
        updateSlotButtonSubAmt(slot);
    }

    function setAmount(slot, amt) {
        const input = document.getElementById(`betAmount${slot}`);
        input.value = amt;
        playerSlots[slot].amount = amt;
        updateSlotButtonSubAmt(slot);
    }

    function doubleAmount(slot) {
        const input = document.getElementById(`betAmount${slot}`);
        let current = parseFloat(input.value) || 10;
        let next = Math.min(50000, current * 2);
        input.value = next;
        playerSlots[slot].amount = next;
        updateSlotButtonSubAmt(slot);
    }

    function halfAmount(slot) {
        const input = document.getElementById(`betAmount${slot}`);
        let current = parseFloat(input.value) || 10;
        let next = Math.max(10, Math.floor(current / 2));
        input.value = next;
        playerSlots[slot].amount = next;
        updateSlotButtonSubAmt(slot);
    }

    // Auto cashout toggles
    [1, 2].forEach(slot => {
        document.getElementById(`autoCashoutToggle${slot}`).addEventListener('change', function() {
            const group = document.getElementById(`autoCashoutGroup${slot}`);
            if (this.checked) {
                group.style.opacity = '1';
                group.style.pointerEvents = 'auto';
            } else {
                group.style.opacity = '0.45';
                group.style.pointerEvents = 'none';
            }
        });

        document.getElementById(`betAmount${slot}`).addEventListener('input', function() {
            playerSlots[slot].amount = parseFloat(this.value) || 10;
            updateSlotButtonSubAmt(slot);
        });
    });

    function updateSlotButtonSubAmt(slot) {
        if (!playerSlots[slot].isBetPlaced) {
            document.getElementById(`btnSubAmt${slot}`).innerText = '₹' + (parseFloat(playerSlots[slot].amount) || 0).toFixed(2);
        }
    }

    function updateCashoutButtonLabels() {
        [1, 2].forEach(slot => {
            if (gameState === 'flying' && playerSlots[slot].isBetPlaced && !playerSlots[slot].isCashedOut) {
                const winAmount = (playerSlots[slot].amount * currentMultiplier).toFixed(2);
                document.getElementById(`btnText${slot}`).innerText = 'CASH OUT';
                document.getElementById(`btnSubAmt${slot}`).innerText = `₹${winAmount} (${currentMultiplier.toFixed(2)}x)`;
            }
        });
    }

    function renderSlotButton(slot, state, winAmt = 0, mult = 0) {
        const btn = document.getElementById(`btnAction${slot}`);
        const card = document.getElementById(`slotCard${slot}`);
        const textSpan = document.getElementById(`btnText${slot}`);
        const subSpan = document.getElementById(`btnSubAmt${slot}`);

        btn.className = 'btn-action-bet';
        btn.disabled = false;
        card.classList.remove('slot-active');

        if (state === 'bet') {
            btn.classList.add('btn-state-bet');
            textSpan.innerText = 'BET';
            subSpan.innerText = '₹' + parseFloat(playerSlots[slot].amount).toFixed(2);
        } else if (state === 'cancel') {
            btn.classList.add('btn-state-cancel');
            card.classList.add('slot-active');
            textSpan.innerText = 'CANCEL';
            subSpan.innerText = 'Waiting for flight...';
        } else if (state === 'cashout') {
            btn.classList.add('btn-state-cashout');
            card.classList.add('slot-active');
            textSpan.innerText = 'CASH OUT';
            const curWin = (playerSlots[slot].amount * currentMultiplier).toFixed(2);
            subSpan.innerText = `₹${curWin} (${currentMultiplier.toFixed(2)}x)`;
        } else if (state === 'cashed_out') {
            btn.classList.add('btn-state-bet');
            btn.disabled = true;
            textSpan.innerText = 'WON!';
            subSpan.innerText = `₹${parseFloat(winAmt).toFixed(2)} (${parseFloat(mult).toFixed(2)}x)`;
        } else if (state === 'lost') {
            btn.classList.add('btn-state-cancel');
            btn.disabled = true;
            textSpan.innerText = 'FLEW AWAY';
            subSpan.innerText = 'Better luck next round!';
        }
    }

    /* =========================================================================
     * USER ACTION HANDLERS (BET / CANCEL / CASHOUT)
     * ========================================================================= */
    async function handleSlotAction(slot) {
        if (!IS_AUTHENTICATED) {
            window.location.href = '{{ route('login') }}';
            return;
        }

        const slotData = playerSlots[slot];

        // 1. If currently in flight and bet is active -> Execute CASHOUT
        if (gameState === 'flying' && slotData.isBetPlaced && !slotData.isCashedOut) {
            executeCashout(slot);
            return;
        }

        // 2. If betting phase and bet is active -> CANCEL BET
        if (gameState === 'betting' && slotData.isBetPlaced && slotData.betId) {
            executeCancelBet(slot);
            return;
        }

        // 3. If betting phase and no bet placed -> PLACE BET
        if (gameState === 'betting' && !slotData.isBetPlaced) {
            executePlaceBet(slot);
            return;
        }

        showToast('Please wait for the current round to end.', 'warning');
    }

    async function executePlaceBet(slot) {
        const amtInput = document.getElementById(`betAmount${slot}`);
        const amount = parseFloat(amtInput.value) || 0;

        let autoCashout = null;
        if (document.getElementById(`autoCashoutToggle${slot}`).checked) {
            autoCashout = parseFloat(document.getElementById(`autoCashoutVal${slot}`).value) || null;
        }

        if (amount < 10) {
            showToast('Minimum bet is ₹10.00', 'warning');
            return;
        }

        const btn = document.getElementById(`btnAction${slot}`);
        btn.disabled = true;

        try {
            const res = await fetch('{{ route('api.aviator.place-bet') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify({
                    bet_slot: slot,
                    amount: amount,
                    auto_cashout: autoCashout,
                })
            });

            const data = await res.json();
            btn.disabled = false;

            if (data.success) {
                playBeep(880, 0.1);
                playerSlots[slot].betId = data.bet.id;
                playerSlots[slot].amount = amount;
                playerSlots[slot].autoCashout = autoCashout;
                playerSlots[slot].isBetPlaced = true;
                playerSlots[slot].isCashedOut = false;

                document.getElementById('userWalletBalance').innerText = '₹' + parseFloat(data.new_balance).toFixed(2);
                renderSlotButton(slot, 'cancel');
                showToast(data.message, 'success');
                loadMyBets(true);
            } else {
                showToast(data.message || 'Failed to place bet', 'error');
            }
        } catch (err) {
            btn.disabled = false;
            showToast('Network error while placing bet', 'error');
        }
    }

    async function executeCancelBet(slot) {
        const betId = playerSlots[slot].betId;
        if (!betId) return;

        const btn = document.getElementById(`btnAction${slot}`);
        btn.disabled = true;

        try {
            const res = await fetch('{{ route('api.aviator.cancel-bet') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify({ bet_id: betId })
            });

            const data = await res.json();
            btn.disabled = false;

            if (data.success) {
                playerSlots[slot].betId = null;
                playerSlots[slot].isBetPlaced = false;
                document.getElementById('userWalletBalance').innerText = '₹' + parseFloat(data.new_balance).toFixed(2);
                renderSlotButton(slot, 'bet');
                showToast('Bet cancelled and refunded!', 'success');
                loadMyBets(true);
            } else {
                showToast(data.message || 'Could not cancel bet', 'error');
            }
        } catch (err) {
            btn.disabled = false;
            showToast('Network error while cancelling bet', 'error');
        }
    }

    async function executeCashout(slot, overrideMultiplier = null) {
        const btn = document.getElementById(`btnAction${slot}`);
        if (!btn || playerSlots[slot].isCashedOut) return;
        btn.disabled = true;

        const multToSend = overrideMultiplier || currentMultiplier;

        try {
            const res = await fetch('{{ route('api.aviator.cashout') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify({
                    bet_slot: slot,
                    client_multiplier: multToSend
                })
            });

            const data = await res.json();
            btn.disabled = false;

            if (data.success) {
                playCashoutSound();
                playerSlots[slot].isCashedOut = true;
                document.getElementById('userWalletBalance').innerText = '₹' + parseFloat(data.new_balance).toFixed(2);
                renderSlotButton(slot, 'cashed_out', data.win_amount, data.multiplier);
                showWinCelebration(data.win_amount, data.multiplier);
                showToast(`🎉 WON ₹${data.win_amount.toFixed(2)} at ${data.multiplier.toFixed(2)}x!`, 'success');
                loadMyBets(true);
            } else {
                showToast(data.message || 'Could not cash out', 'error');
            }
        } catch (err) {
            btn.disabled = false;
            showToast('Cash out request failed', 'error');
        }
    }

    function checkAutoCashout(slot) {
        const slotData = playerSlots[slot];
        if (slotData.isBetPlaced && !slotData.isCashedOut && slotData.autoCashout) {
            if (currentMultiplier >= slotData.autoCashout) {
                playerSlots[slot].isCashedOut = true;
                executeCashout(slot, slotData.autoCashout);
            }
        }
    }

    /* =========================================================================
     * PERSONAL BETS HISTORY TAB (Optimized Instant Render & Preloading)
     * ========================================================================= */
    let cachedMyBetsData = null;

    async function loadMyBets(isBackground = false) {
        if (!IS_AUTHENTICATED) return;
        const tbody = document.getElementById('myBetsBody');

        // If we haven't loaded yet and user clicked, show sleek loader
        if (!cachedMyBetsData && !isBackground && tbody) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-secondary py-3"><i class="fa-solid fa-spinner fa-spin me-1 text-warning"></i> Loading bets...</td></tr>';
        }

        try {
            const res = await fetch('{{ route('api.aviator.my-bets') }}');
            if (!res.ok) return;
            const data = await res.json();
            if (!data.success) return;

            cachedMyBetsData = data;
            renderMyBetsUI(data);
        } catch (e) {
            if (!cachedMyBetsData && tbody) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-3">Failed to load history</td></tr>';
            }
        }
    }

    function renderMyBetsUI(data) {
        const tbody = document.getElementById('myBetsBody');
        if (!tbody || !data || !data.stats) return;

        const statWagered = document.getElementById('statWagered');
        const statWon = document.getElementById('statWon');
        const statNet = document.getElementById('statNet');

        if (statWagered) statWagered.innerText = '₹' + parseFloat(data.stats.total_wagered || 0).toFixed(2);
        if (statWon) statWon.innerText = '₹' + parseFloat(data.stats.total_won || 0).toFixed(2);

        if (statNet) {
            const net = parseFloat(data.stats.net_profit || 0);
            statNet.innerText = (net >= 0 ? '+₹' : '-₹') + Math.abs(net).toFixed(2);
            statNet.className = 'fw-bold font-monospace ' + (net >= 0 ? 'text-success' : 'text-danger');
        }

        if (!data.bets || data.bets.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-secondary py-4">No Aviator bets placed yet.</td></tr>';
            return;
        }

        let html = '';
        data.bets.forEach(b => {
            let badge = '<span class="badge bg-danger">LOST</span>';
            let winText = '₹0.00';
            if (b.status === 'cashed_out') {
                badge = `<span class="badge bg-success font-monospace">${parseFloat(b.cashout_multiplier || 1).toFixed(2)}x</span>`;
                winText = `<span class="text-success fw-bold font-monospace">+₹${parseFloat(b.win_amount || 0).toFixed(2)}</span>`;
            } else if (b.status === 'cancelled') {
                badge = '<span class="badge bg-secondary">CANCELLED</span>';
                winText = '<span class="text-muted">Refund</span>';
            }

            const rId = b.round_id ? b.round_id.substring(b.round_id.length - 6) : '--';
            html += `
                <tr>
                    <td class="font-monospace text-secondary small">${rId}</td>
                    <td class="font-monospace text-warning">₹${parseFloat(b.amount || 0).toFixed(2)}</td>
                    <td>${badge}</td>
                    <td class="text-end">${winText}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    /* =========================================================================
     * TOAST HELPER
     * ========================================================================= */
    let toastTimeout = null;
    function showToast(msg, type = 'info') {
        const toast = document.getElementById('avToast');
        const icon = document.getElementById('toastIcon');
        const text = document.getElementById('toastMsg');

        text.innerText = msg;
        toast.style.display = 'flex';

        if (type === 'success') {
            toast.style.background = '#065f46';
            toast.style.color = '#a7f3d0';
            icon.className = 'fa-solid fa-circle-check text-success';
        } else if (type === 'error') {
            toast.style.background = '#881337';
            toast.style.color = '#fecdd3';
            icon.className = 'fa-solid fa-circle-xmark text-danger';
        } else {
            toast.style.background = '#1e293b';
            toast.style.color = '#f1f5f9';
            icon.className = 'fa-solid fa-circle-info text-info';
        }

        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            toast.style.display = 'none';
        }, 3500);
    }

    // Launch Engine on Window Load with Instant Background Preload
    window.addEventListener('DOMContentLoaded', () => {
        startLiveSync();
        if (IS_AUTHENTICATED) {
            loadMyBets(true);
        }
    });
</script>
@endsection
