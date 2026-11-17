@extends('layouts.app')

@section('title', 'Vortex Official Game - rupeerush')

@section('styles')
<style>
    :root {
        --vtx-bg: #070913;
        --vtx-card: #0d1124;
        --vtx-border: #1b2342;
        --vtx-purple: #a855f7;
        --vtx-purple-glow: rgba(168, 85, 247, 0.55);
        --vtx-blue: #0284c7;
        --vtx-blue-glow: rgba(56, 189, 248, 0.55);
        --vtx-green: #22c55e;
        --vtx-green-glow: rgba(34, 197, 94, 0.55);
        --vtx-red: #ef4444;
        --vtx-red-glow: rgba(239, 68, 68, 0.55);
        --vtx-gold: #f59e0b;
    }

    body {
        background-color: var(--vtx-bg);
        color: #f1f5f9;
        font-family: 'Plus Jakarta Sans', sans-serif;
        user-select: none;
        -webkit-user-select: none;
    }

    .vortex-wrapper {
        max-width: 500px;
        margin: 0 auto;
        padding: 6px 12px 60px;
    }

    /* Cabinet Shell */
    .vortex-cabinet {
        background: radial-gradient(circle at 50% 20%, #151b36 0%, #0a0d1c 70%, #060813 100%);
        border: 1px solid var(--vtx-border);
        border-radius: 28px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.8), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        padding: 10px 14px;
        position: relative;
        overflow: hidden;
    }

    /* Background Swirl Pattern */
    .vortex-cabinet::before {
        content: '';
        position: absolute;
        top: -50px;
        left: -50px;
        right: -50px;
        bottom: -50px;
        background: radial-gradient(circle, rgba(168, 85, 247, 0.04) 0%, transparent 60%);
        pointer-events: none;
        z-index: 0;
    }

    /* Top Utility Bar */
    .vortex-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        position: relative;
        z-index: 2;
    }

    .vtx-btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .vtx-btn-icon:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
    }

    .vtx-wallet-pill {
        display: flex;
        align-items: center;
        gap: 6px;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(245, 158, 11, 0.4);
        padding: 5px 14px;
        border-radius: 50px;
        box-shadow: 0 0 14px rgba(245, 158, 11, 0.2);
    }
    .vtx-wallet-amount {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        font-size: 0.95rem;
        color: #fde047;
    }

    /* Logo Header */
    .vortex-logo-box {
        text-align: center;
        margin-bottom: 6px;
        position: relative;
        z-index: 2;
    }
    .vortex-logo-text {
        font-family: 'Outfit', sans-serif;
        font-size: 1.65rem;
        font-weight: 900;
        font-style: italic;
        letter-spacing: 3px;
        background: linear-gradient(180deg, #ffffff 0%, #e9d5ff 30%, #c084fc 70%, #7e22ce 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        filter: drop-shadow(0 4px 14px rgba(168, 85, 247, 0.6));
        line-height: 1;
        margin: 0;
    }
    .vortex-logo-sub {
        font-size: 0.62rem;
        font-weight: 800;
        letter-spacing: 2.5px;
        color: #a855f7;
        text-transform: uppercase;
        margin-top: 2px;
        opacity: 0.9;
    }

    /* Active Ring Status Bar */
    .rings-status-bar {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-bottom: 8px;
        position: relative;
        z-index: 2;
    }
    .ring-badge {
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        border: 1px solid transparent;
        transition: all 0.3s ease;
        opacity: 0.4;
    }
    .ring-badge.active {
        opacity: 1;
        transform: scale(1.05);
    }
    .ring-badge.ring-1 {
        background: rgba(2, 132, 199, 0.15);
        color: #38bdf8;
        border-color: rgba(56, 189, 248, 0.4);
    }
    .ring-badge.ring-1.active {
        box-shadow: 0 0 14px var(--vtx-blue-glow);
    }
    .ring-badge.ring-2 {
        background: rgba(34, 197, 94, 0.15);
        color: #4ade80;
        border-color: rgba(74, 222, 128, 0.4);
    }
    .ring-badge.ring-2.active {
        box-shadow: 0 0 14px var(--vtx-green-glow);
    }
    .ring-badge.ring-3 {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border-color: rgba(248, 113, 113, 0.4);
    }
    .ring-badge.ring-3.active {
        box-shadow: 0 0 14px var(--vtx-red-glow);
    }

    /* 3D Wheel Arena Stage */
    .wheel-stage {
        position: relative;
        width: 100%;
        max-width: 260px;
        aspect-ratio: 1;
        margin: 0 auto 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }

    .wheel-svg-container {
        width: 100%;
        height: 100%;
        filter: drop-shadow(0 12px 30px rgba(0, 0, 0, 0.9));
        overflow: visible;
    }

    /* Rotating Groups Transition - High Velocity Spring Snap */
    .vtx-ring-group {
        transform-origin: 230px 230px;
        transition: transform 1.75s cubic-bezier(0.14, 0.88, 0.24, 1.03);
        will-change: transform;
    }

    /* Top Pin / Pointer Marker */
    .wheel-pointer {
        position: absolute;
        top: 0px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 25;
        pointer-events: none;
        filter: drop-shadow(0 2px 8px rgba(253, 224, 71, 0.95)) drop-shadow(0 0 16px rgba(239, 68, 68, 0.6));
        animation: pointerPulse 1.6s ease-in-out infinite;
    }

    @keyframes pointerPulse {
        0%, 100% { transform: translateX(-50%) translateY(0) scale(1); }
        50% { transform: translateX(-50%) translateY(2px) scale(1.08); }
    }

    /* Center Inner Water Core Styling & Animations */
    .vtx-center-water-emoji {
        filter: drop-shadow(0 0 14px rgba(56, 189, 248, 0.95));
        animation: waterEmojiPulse 2.2s ease-in-out infinite;
        transform-origin: 230px 217px;
    }
    @keyframes waterEmojiPulse {
        0%, 100% {
            transform: scale(1);
            filter: drop-shadow(0 0 10px rgba(56, 189, 248, 0.8));
        }
        50% {
            transform: scale(1.14);
            filter: drop-shadow(0 0 22px rgba(56, 189, 248, 1)) drop-shadow(0 0 35px rgba(14, 165, 233, 0.8));
        }
    }

    .vtx-water-ripple-1 {
        animation: waterRipple 2.6s cubic-bezier(0.2, 0.8, 0.4, 1) infinite;
        transform-origin: 230px 230px;
    }
    .vtx-water-ripple-2 {
        animation: waterRipple 2.6s cubic-bezier(0.2, 0.8, 0.4, 1) infinite 1.3s;
        transform-origin: 230px 230px;
    }
    @keyframes waterRipple {
        0% {
            transform: scale(0.65);
            opacity: 0.85;
        }
        100% {
            transform: scale(1.18);
            opacity: 0;
        }
    }

    .vtx-water-swirl {
        animation: waterSwirl 10s linear infinite;
        transform-origin: 230px 230px;
    }
    @keyframes waterSwirl {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .vtx-outer-leds circle {
        animation: ledBlink 1.4s ease-in-out infinite alternate;
    }
    @keyframes ledBlink {
        0% { opacity: 0.4; filter: drop-shadow(0 0 1px rgba(253, 224, 71, 0.4)); }
        100% { opacity: 1; filter: drop-shadow(0 0 6px rgba(253, 224, 71, 0.9)); }
    }

    /* Live Win / Payout Banner */
    .payout-live-strip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(13, 17, 36, 0.85);
        border: 1px solid var(--vtx-border);
        border-radius: 14px;
        padding: 8px 14px;
        margin-bottom: 12px;
        position: relative;
        z-index: 2;
    }
    .payout-col-title {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.5px;
    }
    .payout-col-value {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        font-size: 1.1rem;
        color: #fde047;
    }
    .multiplier-badge-live {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 900;
        font-size: 1.15rem;
        background: linear-gradient(135deg, #a855f7, #ec4899);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Bet Control Box */
    .bet-control-box {
        background: rgba(15, 23, 42, 0.7);
        border: 1px solid var(--vtx-border);
        border-radius: 18px;
        padding: 10px 14px;
        margin-bottom: 14px;
        position: relative;
        z-index: 2;
    }
    .bet-label-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }
    .bet-label-text {
        font-size: 0.72rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .bet-input-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .bet-btn-step {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #1e293b;
        border: 1px solid #334155;
        color: #fff;
        font-size: 1.1rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s;
    }
    .bet-btn-step:hover:not(:disabled) {
        background: #334155;
    }
    .bet-btn-step:active:not(:disabled) {
        transform: scale(0.95);
    }
    .bet-display-wrap {
        flex: 1;
        position: relative;
    }
    .bet-display-input {
        width: 100%;
        background: #090e1d;
        border: 1px solid #2a3d75;
        border-radius: 10px;
        color: #fde047;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        font-size: 1.15rem;
        text-align: center;
        padding: 6px 10px;
    }

    /* Quick Presets */
    .quick-presets-row {
        display: flex;
        gap: 6px;
        margin-top: 8px;
        overflow-x: auto;
    }
    .btn-preset {
        flex: 1;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #cbd5e1;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 6px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .btn-preset:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    /* Primary Action Buttons Bar (Cashout, Hold to Spin, Part Cashout) */
    .actions-bar {
        display: grid;
        grid-template-columns: 1fr 1.5fr 1fr;
        align-items: center;
        gap: 10px;
        position: relative;
        z-index: 2;
    }

    /* Secondary Action Buttons (Cashout & Part Payout) */
    .btn-action-side {
        height: 60px;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        padding: 4px;
        text-decoration: none;
    }
    .btn-action-side:disabled {
        opacity: 0.35;
        cursor: not-allowed;
        filter: grayscale(1);
    }

    .btn-cashout-full {
        background: linear-gradient(145deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        box-shadow: 0 4px 18px rgba(16, 185, 129, 0.4);
    }
    .btn-cashout-full:not(:disabled):hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 22px rgba(16, 185, 129, 0.6);
    }

    .btn-cashout-part {
        background: linear-gradient(145deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        box-shadow: 0 4px 18px rgba(2, 132, 199, 0.4);
    }
    .btn-cashout-part:not(:disabled):hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 22px rgba(2, 132, 199, 0.6);
    }

    .side-btn-title {
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        line-height: 1.1;
    }
    .side-btn-amount {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.85rem;
        font-weight: 800;
        margin-top: 2px;
    }

    /* Giant Center "HOLD TO SPIN" Button */
    .spin-main-btn {
        width: 100%;
        height: 74px;
        border-radius: 20px;
        background: linear-gradient(145deg, #a855f7 0%, #7e22ce 60%, #581c87 100%);
        border: 2px solid #c084fc;
        color: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 25px rgba(168, 85, 247, 0.5), inset 0 2px 2px rgba(255, 255, 255, 0.3);
        transition: all 0.15s ease;
    }
    .spin-main-btn:not(:disabled):hover {
        box-shadow: 0 8px 32px rgba(168, 85, 247, 0.75);
        transform: translateY(-2px);
    }
    .spin-main-btn:active:not(:disabled), .spin-main-btn.holding {
        transform: scale(0.96);
        box-shadow: 0 2px 10px rgba(168, 85, 247, 0.4);
    }
    .spin-main-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .spin-btn-icon {
        font-size: 1.4rem;
        line-height: 1;
        transition: transform 0.5s ease;
    }
    .spin-main-btn:not(:disabled):hover .spin-btn-icon,
    .spin-main-btn.spinning .spin-btn-icon {
        animation: spinIcon 1s linear infinite;
    }

    @keyframes spinIcon {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .spin-btn-text {
        font-family: 'Outfit', sans-serif;
        font-size: 0.85rem;
        font-weight: 900;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-top: 3px;
        text-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
    }

    /* Radial Progress overlay on Hold */
    .hold-progress-fill {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 4px;
        background: #fde047;
        width: 0%;
        transition: width 0.05s linear;
        box-shadow: 0 0 10px #fde047;
    }

    /* Blast Explosion Overlay */
    .vortex-boom-overlay {
        position: absolute;
        inset: 0;
        background: radial-gradient(circle, rgba(239, 68, 68, 0.3) 0%, transparent 70%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 20;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s;
    }
    .vortex-boom-overlay.show {
        opacity: 1;
        pointer-events: auto;
    }

    @media (max-width: 768px) {
        .vortex-wrapper { padding: 4px 2px 40px !important; }
        .vortex-cabinet { padding: 8px 8px !important; border-radius: 14px !important; }
        .vortex-logo-box { margin-bottom: 2px !important; }
        .vortex-logo-text { font-size: 1.5rem !important; }
        .vortex-logo-sub { font-size: 0.55rem !important; }
        .rings-status-bar { gap: 4px !important; margin-bottom: 4px !important; }
        .ring-badge { padding: 2px 8px !important; font-size: 0.62rem !important; }
        .wheel-stage {
            max-width: 210px !important;
            margin: 0 auto 6px !important;
        }
        .payout-live-strip { padding: 4px 8px !important; margin-bottom: 6px !important; border-radius: 10px !important; }
        .payout-col-title { font-size: 0.58rem !important; }
        .payout-col-val { font-size: 0.95rem !important; }
        .bet-control-card { padding: 8px !important; margin-bottom: 6px !important; border-radius: 12px !important; }
        .btn-action-side { height: 46px !important; border-radius: 12px !important; font-size: 0.75rem !important; }
        .btn-spin-hold { height: 46px !important; border-radius: 12px !important; }
        .btn-spin-hold-title { font-size: 0.95rem !important; }
    }
</style>
@endsection

@section('content')
<div class="vortex-wrapper">
    <div class="vortex-cabinet">

        <!-- Top Utility Bar -->
        <div class="vortex-topbar">
            <a href="{{ route('home') }}" class="vtx-btn-icon" title="Exit to Lobby">
                <i class="fa-solid fa-chevron-left"></i>
            </a>

            @auth
                <div class="vtx-wallet-pill">
                    <i class="fa-solid fa-coins text-warning"></i>
                    <span class="vtx-wallet-amount" id="userWalletBalance">
                        ₹{{ number_format((float)($user->balance ?? 0), 2) }}
                    </span>
                    <a href="{{ route('wallet.deposit') }}" class="btn btn-sm btn-warning rounded-circle p-0 ms-1 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 0.7rem; font-weight: 800; color: #000;">
                        +
                    </a>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold">
                    Login
                </a>
            @endauth

            <div class="d-flex gap-2">
                <button type="button" class="vtx-btn-icon" id="soundToggleBtn" onclick="toggleSound()" title="Sound Toggle">
                    <i class="fa-solid fa-volume-high text-warning" id="soundIcon"></i>
                </button>
                <button type="button" class="vtx-btn-icon" data-bs-toggle="modal" data-bs-target="#rulesModal" title="How to Play">
                    <i class="fa-solid fa-circle-question"></i>
                </button>
            </div>
        </div>

        <!-- Logo Header -->
        <div class="vortex-logo-box">
            <h1 class="vortex-logo-text">VORTEX</h1>
            <div class="vortex-logo-sub">OFFICIAL GAME</div>
        </div>

        <!-- Concentric Ring Level Badges -->
        <div class="rings-status-bar">
            <div class="ring-badge ring-1 active" id="badgeRing1">
                <i class="fa-solid fa-water me-1"></i> Ring 1 • 🌊 Water
            </div>
            <div class="ring-badge ring-2" id="badgeRing2">
                <i class="fa-solid fa-leaf me-1"></i> Ring 2 • 🦠 Earth
            </div>
            <div class="ring-badge ring-3" id="badgeRing3">
                <i class="fa-solid fa-fire me-1"></i> Ring 3 • 🔥 Fire
            </div>
        </div>

        <!-- Concentric Wheel Arena -->
        <div class="wheel-stage">
            <!-- Top Pointer Marker -->
            <div class="wheel-pointer">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                    <defs>
                        <linearGradient id="pointerGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stop-color="#fef08a"/>
                            <stop offset="50%" stop-color="#eab308"/>
                            <stop offset="100%" stop-color="#ca8a04"/>
                        </linearGradient>
                    </defs>
                    <polygon points="14,26 3,5 25,5" fill="url(#pointerGrad)" stroke="#fde047" stroke-width="1.8" filter="drop-shadow(0 2px 8px rgba(253,224,71,0.9))"/>
                    <circle cx="14" cy="9" r="3.5" fill="#ef4444" stroke="#ffffff" stroke-width="1"/>
                </svg>
            </div>

            <!-- SVG 3-Ring Wheel Container -->
            <svg class="wheel-svg-container" viewBox="0 0 460 460">
                <defs>
                    <!-- Center Inner Water Radial Glow -->
                    <radialGradient id="waterCoreGrad" cx="50%" cy="50%" r="50%">
                        <stop offset="0%" stop-color="#38bdf8" stop-opacity="0.95"/>
                        <stop offset="45%" stop-color="#0284c7" stop-opacity="0.95"/>
                        <stop offset="80%" stop-color="#0369a1" stop-opacity="0.95"/>
                        <stop offset="100%" stop-color="#082f49" stop-opacity="1"/>
                    </radialGradient>

                    <linearGradient id="waterRimGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#7dd3fc"/>
                        <stop offset="50%" stop-color="#0284c7"/>
                        <stop offset="100%" stop-color="#0369a1"/>
                    </linearGradient>

                    <!-- Blast / Explosion Wedge Fill -->
                    <radialGradient id="blastWedgeGrad" cx="50%" cy="50%" r="60%">
                        <stop offset="0%" stop-color="#ef4444" stop-opacity="0.55"/>
                        <stop offset="70%" stop-color="#b91c1c" stop-opacity="0.35"/>
                        <stop offset="100%" stop-color="#7f1d1d" stop-opacity="0.2"/>
                    </radialGradient>

                    <!-- Water Level-Up Wedge Fill -->
                    <radialGradient id="waterAdvanceGrad" cx="50%" cy="50%" r="60%">
                        <stop offset="0%" stop-color="#38bdf8" stop-opacity="0.5"/>
                        <stop offset="70%" stop-color="#0284c7" stop-opacity="0.35"/>
                        <stop offset="100%" stop-color="#0c4a6e" stop-opacity="0.2"/>
                    </radialGradient>

                    <!-- Earth Level-Up Wedge Fill -->
                    <radialGradient id="earthAdvanceGrad" cx="50%" cy="50%" r="60%">
                        <stop offset="0%" stop-color="#4ade80" stop-opacity="0.5"/>
                        <stop offset="70%" stop-color="#16a34a" stop-opacity="0.35"/>
                        <stop offset="100%" stop-color="#14532d" stop-opacity="0.2"/>
                    </radialGradient>

                    <!-- Golden 200X Jackpot Wedge Fill -->
                    <radialGradient id="jackpotGrad" cx="50%" cy="50%" r="60%">
                        <stop offset="0%" stop-color="#fde047" stop-opacity="0.65"/>
                        <stop offset="70%" stop-color="#f59e0b" stop-opacity="0.4"/>
                        <stop offset="100%" stop-color="#b45309" stop-opacity="0.2"/>
                    </radialGradient>

                    <!-- Outer Ring Metallic Rim -->
                    <linearGradient id="rimGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#475569"/>
                        <stop offset="50%" stop-color="#0f172a"/>
                        <stop offset="100%" stop-color="#1e293b"/>
                    </linearGradient>
                </defs>

                <!-- Base Circular Backplate -->
                <circle cx="230" cy="230" r="224" fill="#070a18" stroke="#1e293b" stroke-width="4"/>

                <!-- Perimeter Casino LEDs -->
                <g class="vtx-outer-leds">
                    @for($l = 0; $l < 24; $l++)
                        @php
                            $ledAngle = deg2rad(($l * 15) - 90);
                            $lx = 230 + 221 * cos($ledAngle);
                            $ly = 230 + 221 * sin($ledAngle);
                        @endphp
                        <circle cx="{{ round($lx, 2) }}" cy="{{ round($ly, 2) }}" r="2.2" fill="{{ $l % 2 === 0 ? '#fde047' : '#38bdf8' }}"/>
                    @endfor
                </g>

                <!-- ================= RING 3: OUTER FIRE RING (r=172 to 214) ================= -->
                <g id="ringGroup3" class="vtx-ring-group">
                    <circle cx="230" cy="230" r="214" fill="none" stroke="url(#rimGradient)" stroke-width="3"/>
                    <circle cx="230" cy="230" r="172" fill="none" stroke="#1e293b" stroke-width="1.5"/>

                    <!-- Ring 3 Sectors (10 sectors = 36 deg each) -->
                    @php
                        $r3Sectors = $ringsConfig[3]['sectors'] ?? [];
                        $angle3 = 36;
                        $R3_out = 214;
                        $R3_in = 172;
                    @endphp
                    @foreach($r3Sectors as $i => $sec)
                        @php
                            $a1 = deg2rad(($i * $angle3) - 90);
                            $a2 = deg2rad((($i + 1) * $angle3) - 90);
                            $x1 = round(230 + $R3_out * cos($a1), 2);
                            $y1 = round(230 + $R3_out * sin($a1), 2);
                            $x2 = round(230 + $R3_out * cos($a2), 2);
                            $y2 = round(230 + $R3_out * sin($a2), 2);
                            $x3 = round(230 + $R3_in * cos($a2), 2);
                            $y3 = round(230 + $R3_in * sin($a2), 2);
                            $x4 = round(230 + $R3_in * cos($a1), 2);
                            $y4 = round(230 + $R3_in * sin($a1), 2);
                            $pathD = "M {$x1} {$y1} A {$R3_out} {$R3_out} 0 0 1 {$x2} {$y2} L {$x3} {$y3} A {$R3_in} {$R3_in} 0 0 0 {$x4} {$y4} Z";

                            $midA = ($i * $angle3) + ($angle3 / 2);
                            $rad = deg2rad($midA - 90);
                            $tx = round(230 + 193 * cos($rad), 2);
                            $ty = round(230 + 193 * sin($rad), 2);

                            $wedgeFill = ($sec['type'] === 'bust')
                                ? 'url(#blastWedgeGrad)'
                                : (str_contains($sec['label'], '200X') ? 'url(#jackpotGrad)' : (str_contains($sec['label'], 'BONUS') ? 'rgba(236,72,153,0.25)' : ($i % 2 === 0 ? 'rgba(255,255,255,0.03)' : 'rgba(15,23,42,0.65)')));
                            $wedgeStroke = ($sec['type'] === 'bust')
                                ? 'rgba(239, 68, 68, 0.75)'
                                : (str_contains($sec['label'], '200X') ? '#fde047' : (str_contains($sec['label'], 'BONUS') ? '#ec4899' : 'rgba(255,255,255,0.08)'));
                        @endphp
                        <!-- Sector Annular Wedge Slice -->
                        <path d="{{ $pathD }}" fill="{{ $wedgeFill }}" stroke="{{ $wedgeStroke }}" stroke-width="1.2"/>

                        <!-- Divider Line -->
                        <line x1="{{ $x1 }}" y1="{{ $y1 }}" x2="{{ $x4 }}" y2="{{ $y4 }}" stroke="rgba(255,255,255,0.18)" stroke-width="1"/>

                        <!-- Sector Label -->
                        <text x="{{ $tx }}" y="{{ $ty }}" 
                              text-anchor="middle" dominant-baseline="central" 
                              transform="rotate({{ $midA }}, {{ $tx }}, {{ $ty }})"
                              fill="{{ $sec['color'] }}" 
                              font-size="{{ str_contains($sec['label'], '200X') ? '10.5' : (str_contains($sec['label'], 'BLAST') || str_contains($sec['label'], 'BONUS') ? '9.5' : '10.5') }}" 
                              font-family="Outfit, sans-serif" 
                              font-weight="900" 
                              letter-spacing="0.5">
                            {{ $sec['label'] }}
                        </text>
                    @endforeach
                </g>

                <!-- ================= RING 2: MIDDLE EARTH RING (r=122 to 168) ================= -->
                <g id="ringGroup2" class="vtx-ring-group">
                    <circle cx="230" cy="230" r="168" fill="none" stroke="rgba(34, 197, 94, 0.35)" stroke-width="2"/>
                    <circle cx="230" cy="230" r="122" fill="none" stroke="#1e293b" stroke-width="1.5"/>

                    <!-- Ring 2 Sectors (8 sectors = 45 deg each) -->
                    @php
                        $r2Sectors = $ringsConfig[2]['sectors'] ?? [];
                        $angle2 = 45;
                        $R2_out = 168;
                        $R2_in = 122;
                    @endphp
                    @foreach($r2Sectors as $i => $sec)
                        @php
                            $a1 = deg2rad(($i * $angle2) - 90);
                            $a2 = deg2rad((($i + 1) * $angle2) - 90);
                            $x1 = round(230 + $R2_out * cos($a1), 2);
                            $y1 = round(230 + $R2_out * sin($a1), 2);
                            $x2 = round(230 + $R2_out * cos($a2), 2);
                            $y2 = round(230 + $R2_out * sin($a2), 2);
                            $x3 = round(230 + $R2_in * cos($a2), 2);
                            $y3 = round(230 + $R2_in * sin($a2), 2);
                            $x4 = round(230 + $R2_in * cos($a1), 2);
                            $y4 = round(230 + $R2_in * sin($a1), 2);
                            $pathD = "M {$x1} {$y1} A {$R2_out} {$R2_out} 0 0 1 {$x2} {$y2} L {$x3} {$y3} A {$R2_in} {$R2_in} 0 0 0 {$x4} {$y4} Z";

                            $midA = ($i * $angle2) + ($angle2 / 2);
                            $rad = deg2rad($midA - 90);
                            $tx = round(230 + 145 * cos($rad), 2);
                            $ty = round(230 + 145 * sin($rad), 2);

                            $wedgeFill = ($sec['type'] === 'bust')
                                ? 'url(#blastWedgeGrad)'
                                : ($sec['advance'] ? 'url(#earthAdvanceGrad)' : ($i % 2 === 0 ? 'rgba(34,197,94,0.06)' : 'rgba(15,23,42,0.65)'));
                            $wedgeStroke = ($sec['type'] === 'bust')
                                ? 'rgba(239, 68, 68, 0.75)'
                                : ($sec['advance'] ? 'rgba(74, 222, 128, 0.85)' : 'rgba(255,255,255,0.08)');
                        @endphp
                        <!-- Sector Annular Wedge Slice -->
                        <path d="{{ $pathD }}" fill="{{ $wedgeFill }}" stroke="{{ $wedgeStroke }}" stroke-width="1.2"/>

                        <!-- Divider Line -->
                        <line x1="{{ $x1 }}" y1="{{ $y1 }}" x2="{{ $x4 }}" y2="{{ $y4 }}" stroke="rgba(255,255,255,0.18)" stroke-width="1"/>

                        <text x="{{ $tx }}" y="{{ $ty }}" 
                              text-anchor="middle" dominant-baseline="central" 
                              transform="rotate({{ $midA }}, {{ $tx }}, {{ $ty }})"
                              fill="{{ $sec['color'] }}" 
                              font-size="{{ str_contains($sec['label'], 'BLAST') || str_contains($sec['label'], 'LEVEL UP') ? '10' : '11.5' }}" 
                              font-family="Outfit, sans-serif" 
                              font-weight="900" 
                              letter-spacing="0.5">
                            {{ $sec['label'] }}
                        </text>
                    @endforeach
                </g>

                <!-- ================= RING 1: INNER WATER RING (r=72 to 118) ================= -->
                <g id="ringGroup1" class="vtx-ring-group">
                    <circle cx="230" cy="230" r="118" fill="none" stroke="rgba(2, 132, 199, 0.4)" stroke-width="2"/>
                    <circle cx="230" cy="230" r="72" fill="none" stroke="#1e293b" stroke-width="1.5"/>

                    <!-- Ring 1 Sectors (8 sectors = 45 deg each) -->
                    @php
                        $r1Sectors = $ringsConfig[1]['sectors'] ?? [];
                        $angle1 = 45;
                        $R1_out = 118;
                        $R1_in = 72;
                    @endphp
                    @foreach($r1Sectors as $i => $sec)
                        @php
                            $a1 = deg2rad(($i * $angle1) - 90);
                            $a2 = deg2rad((($i + 1) * $angle1) - 90);
                            $x1 = round(230 + $R1_out * cos($a1), 2);
                            $y1 = round(230 + $R1_out * sin($a1), 2);
                            $x2 = round(230 + $R1_out * cos($a2), 2);
                            $y2 = round(230 + $R1_out * sin($a2), 2);
                            $x3 = round(230 + $R1_in * cos($a2), 2);
                            $y3 = round(230 + $R1_in * sin($a2), 2);
                            $x4 = round(230 + $R1_in * cos($a1), 2);
                            $y4 = round(230 + $R1_in * sin($a1), 2);
                            $pathD = "M {$x1} {$y1} A {$R1_out} {$R1_out} 0 0 1 {$x2} {$y2} L {$x3} {$y3} A {$R1_in} {$R1_in} 0 0 0 {$x4} {$y4} Z";

                            $midA = ($i * $angle1) + ($angle1 / 2);
                            $rad = deg2rad($midA - 90);
                            $tx = round(230 + 95 * cos($rad), 2);
                            $ty = round(230 + 95 * sin($rad), 2);

                            $wedgeFill = ($sec['type'] === 'bust')
                                ? 'url(#blastWedgeGrad)'
                                : ($sec['advance'] ? 'url(#waterAdvanceGrad)' : ($i % 2 === 0 ? 'rgba(2,132,199,0.08)' : 'rgba(15,23,42,0.65)'));
                            $wedgeStroke = ($sec['type'] === 'bust')
                                ? 'rgba(239, 68, 68, 0.75)'
                                : ($sec['advance'] ? 'rgba(56, 189, 248, 0.85)' : 'rgba(255,255,255,0.08)');
                        @endphp
                        <!-- Sector Annular Wedge Slice -->
                        <path d="{{ $pathD }}" fill="{{ $wedgeFill }}" stroke="{{ $wedgeStroke }}" stroke-width="1.2"/>

                        <!-- Divider Line -->
                        <line x1="{{ $x1 }}" y1="{{ $y1 }}" x2="{{ $x4 }}" y2="{{ $y4 }}" stroke="rgba(255,255,255,0.18)" stroke-width="1"/>

                        <text x="{{ $tx }}" y="{{ $ty }}" 
                              text-anchor="middle" dominant-baseline="central" 
                              transform="rotate({{ $midA }}, {{ $tx }}, {{ $ty }})"
                              fill="{{ $sec['color'] }}" 
                              font-size="{{ str_contains($sec['label'], 'BLAST') || str_contains($sec['label'], 'LEVEL UP') ? '9.5' : '11.5' }}" 
                              font-family="Outfit, sans-serif" 
                              font-weight="900" 
                              letter-spacing="0.5">
                            {{ $sec['label'] }}
                        </text>
                    @endforeach
                </g>

                <!-- ================= CENTER INNER WATER CORE (r=68) ================= -->
                <g id="centerWaterCore">
                    <!-- Base Aquatic Circle -->
                    <circle cx="230" cy="230" r="68" fill="url(#waterCoreGrad)" stroke="url(#waterRimGrad)" stroke-width="3" filter="drop-shadow(0 0 18px rgba(56, 189, 248, 0.75))"/>

                    <!-- Animated Water Ripple Rings -->
                    <circle cx="230" cy="230" r="48" fill="none" stroke="rgba(56, 189, 248, 0.45)" stroke-width="1.5" class="vtx-water-ripple-1"/>
                    <circle cx="230" cy="230" r="58" fill="none" stroke="rgba(14, 165, 233, 0.3)" stroke-width="1" class="vtx-water-ripple-2"/>

                    <!-- Rotating Ocean Vortex Whirlpool Stream -->
                    <g class="vtx-water-swirl" opacity="0.65">
                        <circle cx="230" cy="230" r="40" fill="none" stroke="#7dd3fc" stroke-width="1.2" stroke-dasharray="25 15 40 10"/>
                        <circle cx="230" cy="230" r="54" fill="none" stroke="#38bdf8" stroke-width="1" stroke-dasharray="35 25 20 20"/>
                    </g>

                    <!-- Center Inner Water Emoji -->
                    <text x="230" y="217" text-anchor="middle" dominant-baseline="central" font-size="28" class="vtx-center-water-emoji">🌊</text>

                    <!-- Inner Water Titles -->
                    <text x="230" y="244" text-anchor="middle" dominant-baseline="central" fill="#ffffff" font-family="'Outfit', sans-serif" font-weight="900" font-size="9" letter-spacing="1.5" class="vtx-center-water-label">INNER WATER</text>
                    <text x="230" y="257" text-anchor="middle" dominant-baseline="central" fill="#7dd3fc" font-family="'JetBrains Mono', monospace" font-weight="800" font-size="7.5" letter-spacing="1">VORTEX CORE</text>
                </g>
            </svg>
        </div>

        <!-- Live Payout & Current Multiplier Strip -->
        <div class="payout-live-strip">
            <div>
                <div class="payout-col-title">Current Payout</div>
                <div class="payout-col-value" id="displayPayout">
                    ₹{{ number_format((float)($activeGame['payout_amount'] ?? 0), 2) }}
                </div>
            </div>

            <div class="text-center">
                <div class="payout-col-title">Multiplier</div>
                <div class="multiplier-badge-live" id="displayMultiplier">
                    {{ number_format((float)($activeGame['current_multiplier'] ?? 1.00), 2) }}X
                </div>
            </div>

            <div class="text-end">
                <div class="payout-col-title">50% Part Cashout</div>
                <div class="payout-col-value text-info" id="displayPartCashout">
                    ₹{{ number_format((float)($activeGame['part_payout_available'] ?? 0), 2) }}
                </div>
            </div>
        </div>

        <!-- Bet Selector Section -->
        <div class="bet-control-box">
            <div class="bet-label-row">
                <span class="bet-label-text">
                    <i class="fa-solid fa-coins text-warning me-1"></i> Bet Amount (INR)
                </span>
                <span class="text-white-50 small" style="font-size: 0.7rem;">
                    Min: ₹{{ number_format((float)($setting->vortex_min_bet ?? 1), 0) }} • Max: ₹{{ number_format((float)($setting->vortex_max_bet ?? 25000), 0) }}
                </span>
            </div>

            <div class="bet-input-row">
                <button type="button" class="bet-btn-step" onclick="adjustBet(-5)">-</button>
                <div class="bet-display-wrap">
                    <input type="number" step="any" min="{{ $setting->vortex_min_bet ?? 1 }}" max="{{ $setting->vortex_max_bet ?? 25000 }}" class="bet-display-input" id="betAmountInput" value="{{ $activeGame['bet_amount'] ?? 5.00 }}" {{ $activeGame ? 'disabled' : '' }}>
                </div>
                <button type="button" class="bet-btn-step" onclick="adjustBet(5)">+</button>
            </div>

            <!-- Quick Presets -->
            <div class="quick-presets-row">
                <button type="button" class="btn-preset" onclick="setBet(5)">₹5</button>
                <button type="button" class="btn-preset" onclick="setBet(10)">₹10</button>
                <button type="button" class="btn-preset" onclick="setBet(50)">₹50</button>
                <button type="button" class="btn-preset" onclick="setBet(100)">₹100</button>
                <button type="button" class="btn-preset" onclick="multiplyBet(0.5)">1/2</button>
                <button type="button" class="btn-preset" onclick="multiplyBet(2)">2X</button>
            </div>
        </div>

        <!-- Primary Action Buttons (Cashout, Hold to Spin, Part Cashout) -->
        <div class="actions-bar">
            <!-- Full Cashout Button -->
            <button type="button" class="btn-action-side btn-cashout-full" id="cashoutBtn" onclick="handleCashout()" {{ empty($activeGame) || $activeGame['status'] !== 'active' ? 'disabled' : '' }}>
                <span class="side-btn-title">CASH OUT</span>
                <span class="side-btn-amount" id="btnCashoutAmt">
                    ₹{{ number_format((float)($activeGame['payout_amount'] ?? 0), 2) }}
                </span>
            </button>

            <!-- Giant Center "HOLD TO SPIN" Button -->
            <button type="button" class="spin-main-btn" id="spinMainBtn">
                <div class="hold-progress-fill" id="holdProgressBar"></div>
                <i class="fa-solid fa-arrows-spin spin-btn-icon" id="spinIconEl"></i>
                <span class="spin-btn-text" id="spinBtnLabel">
                    {{ $activeGame ? 'SPIN RING ' . $activeGame['current_ring'] : 'HOLD TO SPIN' }}
                </span>
            </button>

            <!-- 50% Part Cashout Button -->
            <button type="button" class="btn-action-side btn-cashout-part" id="partCashoutBtn" onclick="handlePartCashout()" {{ empty($activeGame) || !($activeGame['can_part_cashout'] ?? false) ? 'disabled' : '' }}>
                <span class="side-btn-title">PART PAYOUT</span>
                <span class="side-btn-amount" id="btnPartCashoutAmt">
                    ₹{{ number_format((float)($activeGame['part_payout_available'] ?? 0), 2) }}
                </span>
            </button>
        </div>

    </div>
</div>

<!-- How To Play Modal -->
<div class="modal fade" id="rulesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg text-white" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1) !important;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-circle-info text-warning me-2"></i> How to Play Vortex
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-secondary small p-4">
                <p><strong class="text-light">1. Concentric Wheel Mechanics:</strong> The Vortex contains 3 independent rotating rings: Water (Inner), Earth (Middle), and Fire (Outer).</p>
                <p><strong class="text-light">2. Hold to Spin:</strong> Tap or hold the center button to spin your active ring. When it stops on an upgrade arrow (⬆️) or multiplier, you advance towards the outer rings!</p>
                <p><strong class="text-light">3. Part PayOut (50% Cashout):</strong> At any winning point, click "PART PAYOUT" to instantly pocket 50% of your winnings directly into your wallet and continue spinning with the rest!</p>
                <p><strong class="text-light">4. Cash Out Anytime:</strong> Click CASH OUT to collect 100% of your accumulated payout and finish the round.</p>
                <p class="mb-0"><strong class="text-light">5. 200X Max Win:</strong> Reaching the 200X crown on the Fire Ring triggers an automatic instant jackpot cashout!</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Audio Synthesizer via Web Audio API (Guaranteed smooth sound on all devices)
    let audioCtx = null;
    let isSoundMuted = false;

    function getAudioContext() {
        if (!audioCtx) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            audioCtx = new AudioContext();
        }
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        return audioCtx;
    }

    function toggleSound() {
        isSoundMuted = !isSoundMuted;
        const icon = document.getElementById('soundIcon');
        if (isSoundMuted) {
            icon.className = 'fa-solid fa-volume-xmark text-secondary';
        } else {
            icon.className = 'fa-solid fa-volume-high text-warning';
            playWheelTick();
        }
    }

    function playWheelTick() {
        if (isSoundMuted) return;
        try {
            const ctx = getAudioContext();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(400, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(100, ctx.currentTime + 0.04);
            gain.gain.setValueAtTime(0.15, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.04);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.04);
        } catch(e) {}
    }

    function playLevelUp() {
        if (isSoundMuted) return;
        try {
            const ctx = getAudioContext();
            [523.25, 659.25, 783.99, 1046.50].forEach((freq, idx) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, ctx.currentTime + (idx * 0.08));
                gain.gain.setValueAtTime(0.2, ctx.currentTime + (idx * 0.08));
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + (idx * 0.08) + 0.25);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(ctx.currentTime + (idx * 0.08));
                osc.stop(ctx.currentTime + (idx * 0.08) + 0.25);
            });
        } catch(e) {}
    }

    function playWinCashout() {
        if (isSoundMuted) return;
        try {
            const ctx = getAudioContext();
            [587.33, 880, 1174.66].forEach((freq, idx) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, ctx.currentTime + (idx * 0.1));
                gain.gain.setValueAtTime(0.25, ctx.currentTime + (idx * 0.1));
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + (idx * 0.1) + 0.35);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(ctx.currentTime + (idx * 0.1));
                osc.stop(ctx.currentTime + (idx * 0.1) + 0.35);
            });
        } catch(e) {}
    }

    function playBustSound() {
        if (isSoundMuted) return;
        try {
            const ctx = getAudioContext();
            // Heavy blast boom
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(180, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(25, ctx.currentTime + 0.45);
            gain.gain.setValueAtTime(0.4, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.45);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.45);

            // Explosive blast noise impact
            const bufferSize = Math.floor(ctx.sampleRate * 0.25);
            const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
            const data = buffer.getChannelData(0);
            for (let i = 0; i < bufferSize; i++) {
                data[i] = (Math.random() * 2 - 1) * Math.exp(-i / (bufferSize * 0.3));
            }
            const noise = ctx.createBufferSource();
            noise.buffer = buffer;
            const noiseGain = ctx.createGain();
            noiseGain.gain.setValueAtTime(0.25, ctx.currentTime);
            noiseGain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.25);
            noise.connect(noiseGain);
            noiseGain.connect(ctx.destination);
            noise.start();
        } catch(e) {}
    }

    // Game Client State
    let activeGame = @json($activeGame);
    let isSpinning = false;
    let holdTimer = null;
    let holdProgress = 0;
    const ringRotations = { 1: 0, 2: 0, 3: 0 };

    const csrfToken = '{{ csrf_token() }}';

    function adjustBet(delta) {
        if (activeGame) return;
        const input = document.getElementById('betAmountInput');
        let val = Math.max(1, (parseFloat(input.value) || 0) + delta);
        input.value = val.toFixed(2);
    }

    function setBet(val) {
        if (activeGame) return;
        document.getElementById('betAmountInput').value = parseFloat(val).toFixed(2);
    }

    function multiplyBet(factor) {
        if (activeGame) return;
        const input = document.getElementById('betAmountInput');
        let val = Math.max(1, (parseFloat(input.value) || 0) * factor);
        input.value = val.toFixed(2);
    }

    // UI State Updater
    function updateUIState(game) {
        activeGame = game;

        const betInput = document.getElementById('betAmountInput');
        const spinBtn = document.getElementById('spinMainBtn');
        const spinLabel = document.getElementById('spinBtnLabel');
        const cashoutBtn = document.getElementById('cashoutBtn');
        const partCashoutBtn = document.getElementById('partCashoutBtn');
        const payoutEl = document.getElementById('displayPayout');
        const multiplierEl = document.getElementById('displayMultiplier');
        const partPayoutEl = document.getElementById('displayPartCashout');
        const cashoutAmtEl = document.getElementById('btnCashoutAmt');
        const partCashoutAmtEl = document.getElementById('btnPartCashoutAmt');

        // Ring badges
        const r1 = document.getElementById('badgeRing1');
        const r2 = document.getElementById('badgeRing2');
        const r3 = document.getElementById('badgeRing3');

        r1.className = 'ring-badge ring-1';
        r2.className = 'ring-badge ring-2';
        r3.className = 'ring-badge ring-3';

        if (!game || game.status !== 'active') {
            betInput.disabled = false;
            cashoutBtn.disabled = true;
            partCashoutBtn.disabled = true;
            spinLabel.textContent = 'HOLD TO SPIN';
            r1.classList.add('active');
            payoutEl.textContent = '₹0.00';
            multiplierEl.textContent = '1.00X';
            partPayoutEl.textContent = '₹0.00';
            cashoutAmtEl.textContent = '₹0.00';
            partCashoutAmtEl.textContent = '₹0.00';
            return;
        }

        // Active Game state
        betInput.disabled = true;
        const currentRing = game.current_ring || 1;
        if (currentRing === 1) r1.classList.add('active');
        if (currentRing === 2) r2.classList.add('active');
        if (currentRing === 3) r3.classList.add('active');

        spinLabel.textContent = `SPIN RING ${currentRing}`;

        const payout = parseFloat(game.payout_amount || 0);
        const partAvailable = parseFloat(game.part_payout_available || (payout / 2));
        const mult = parseFloat(game.current_multiplier || 1.00);

        payoutEl.textContent = `₹${payout.toFixed(2)}`;
        multiplierEl.textContent = `${mult.toFixed(2)}X`;
        partPayoutEl.textContent = `₹${partAvailable.toFixed(2)}`;
        cashoutAmtEl.textContent = `₹${payout.toFixed(2)}`;
        partCashoutAmtEl.textContent = `₹${partAvailable.toFixed(2)}`;

        cashoutBtn.disabled = (payout <= 0);
        partCashoutBtn.disabled = !game.can_part_cashout;
    }

    // Start Session or Spin
    async function triggerSpinOrStart() {
        if (isSpinning) return;
        getAudioContext();

        // 1. If no active game, start game first
        if (!activeGame || activeGame.status !== 'active') {
            const betAmount = parseFloat(document.getElementById('betAmountInput').value) || 5.00;
            try {
                isSpinning = true;
                const spinBtn = document.getElementById('spinMainBtn');
                spinBtn.disabled = true;

                const res = await fetch("{{ route('api.vortex.start') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ amount: betAmount })
                });

                const data = await res.json();
                if (!data.success) {
                    alert(data.message || 'Error starting Vortex session');
                    isSpinning = false;
                    spinBtn.disabled = false;
                    return;
                }

                if (data.new_balance !== undefined) {
                    document.getElementById('userWalletBalance').textContent = `₹${parseFloat(data.new_balance).toFixed(2)}`;
                }

                updateUIState(data.game);
                isSpinning = false;
                spinBtn.disabled = false;

                // Immediately trigger first spin
                executeSpin();
            } catch(e) {
                isSpinning = false;
                document.getElementById('spinMainBtn').disabled = false;
            }
        } else {
            // Already active, spin current ring
            executeSpin();
        }
    }

    // Execute Spin Animation & API Call
    async function executeSpin() {
        if (isSpinning) return;
        isSpinning = true;

        const spinBtn = document.getElementById('spinMainBtn');
        const iconEl = document.getElementById('spinIconEl');
        spinBtn.classList.add('spinning');
        spinBtn.disabled = true;

        const currentRing = activeGame.current_ring || 1;
        const ringGroup = document.getElementById(`ringGroup${currentRing}`);

        // Ticking audio interval during spin (rapid high-speed ticks)
        const tickInterval = setInterval(playWheelTick, 65);

        try {
            const res = await fetch("{{ route('api.vortex.spin') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });

            const data = await res.json();
            if (!data.success) {
                clearInterval(tickInterval);
                alert(data.message || 'Spin failed.');
                isSpinning = false;
                spinBtn.classList.remove('spinning');
                spinBtn.disabled = false;
                return;
            }

            const stopAngle = data.spin.stop_angle || 1440;
            ringRotations[currentRing] = (ringRotations[currentRing] || 0) + stopAngle;

            // Apply high-velocity spring rotation transform
            ringGroup.style.transform = `rotate(${ringRotations[currentRing]}deg)`;

            // Wait for 1.75s spring easing to complete (1800ms)
            setTimeout(() => {
                clearInterval(tickInterval);
                spinBtn.classList.remove('spinning');
                spinBtn.disabled = false;
                isSpinning = false;

                if (data.result === 'bust') {
                    playBustSound();
                    updateUIState(null);
                    alert("💥 BOOM! Blast sector hit! The Vortex collapsed! Better luck next spin.");
                } else {
                    if (data.is_advanced) {
                        playLevelUp();
                    } else {
                        playWheelTick();
                    }
                    updateUIState(data.game);

                    if (data.status === 'cashed_out') {
                        playWinCashout();
                        alert(`JACKPOT 200X REACHED! Auto-cashed out ₹${data.net_payout}!`);
                        if (data.new_balance !== undefined) {
                            document.getElementById('userWalletBalance').textContent = `₹${parseFloat(data.new_balance).toFixed(2)}`;
                        }
                        updateUIState(null);
                    }
                }
            }, 1800);

        } catch(e) {
            clearInterval(tickInterval);
            isSpinning = false;
            spinBtn.classList.remove('spinning');
            spinBtn.disabled = false;
        }
    }

    // 50% Partial Cashout
    async function handlePartCashout() {
        if (!activeGame || isSpinning) return;
        if (!confirm('Take 50% payout now into your wallet and continue spinning with the remaining half?')) return;

        try {
            const res = await fetch("{{ route('api.vortex.part-cashout') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });

            const data = await res.json();
            if (data.success) {
                playWinCashout();
                if (data.new_balance !== undefined) {
                    document.getElementById('userWalletBalance').textContent = `₹${parseFloat(data.new_balance).toFixed(2)}`;
                }
                updateUIState(data.game);
            } else {
                alert(data.message);
            }
        } catch(e) {}
    }

    // 100% Full Cashout
    async function handleCashout() {
        if (!activeGame || isSpinning) return;

        try {
            const res = await fetch("{{ route('api.vortex.cashout') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });

            const data = await res.json();
            if (data.success) {
                playWinCashout();
                if (data.new_balance !== undefined) {
                    document.getElementById('userWalletBalance').textContent = `₹${parseFloat(data.new_balance).toFixed(2)}`;
                }
                alert(data.message);
                updateUIState(null);
            } else {
                alert(data.message);
            }
        } catch(e) {}
    }

    // Setup Hold to Spin listeners
    const mainBtn = document.getElementById('spinMainBtn');
    const progressBar = document.getElementById('holdProgressBar');

    function startHold() {
        if (isSpinning) return;
        mainBtn.classList.add('holding');
        holdProgress = 0;
        progressBar.style.width = '0%';

        holdTimer = setInterval(() => {
            holdProgress += 10;
            progressBar.style.width = `${holdProgress}%`;
            if (holdProgress >= 100) {
                clearInterval(holdTimer);
                holdTimer = null;
                progressBar.style.width = '0%';
                triggerSpinOrStart();
            }
        }, 30);
    }

    function cancelHold() {
        mainBtn.classList.remove('holding');
        if (holdTimer) {
            clearInterval(holdTimer);
            holdTimer = null;
        }
        if (holdProgress > 0 && holdProgress < 100) {
            progressBar.style.width = '0%';
            // If tapped quickly without full hold, still trigger spin
            triggerSpinOrStart();
        }
        holdProgress = 0;
    }

    mainBtn.addEventListener('mousedown', startHold);
    mainBtn.addEventListener('mouseup', cancelHold);
    mainBtn.addEventListener('mouseleave', cancelHold);

    mainBtn.addEventListener('touchstart', (e) => { e.preventDefault(); startHold(); }, { passive: false });
    mainBtn.addEventListener('touchend', (e) => { e.preventDefault(); cancelHold(); });

    // Initialize UI on page load
    updateUIState(activeGame);
</script>
@endsection
