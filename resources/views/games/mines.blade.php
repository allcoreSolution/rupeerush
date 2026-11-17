@extends('layouts.app')

@section('title', 'Mines Live Arcade - rupeerush')

@section('styles')
<style>
    :root {
        --mn-bg: #090e1d;
        --mn-card-bg: #111a33;
        --mn-border: #1e2e5c;
        --mn-gold: #f59e0b;
        --mn-gold-glow: rgba(245, 158, 11, 0.45);
        --mn-yellow: #fbbf24;
        --mn-green: #10b981;
        --mn-green-glow: rgba(16, 185, 129, 0.45);
        --mn-red: #ef4444;
        --mn-red-glow: rgba(239, 68, 68, 0.45);
        --mn-tile-bg: linear-gradient(145deg, #182346 0%, #111833 100%);
        --mn-tile-hover: linear-gradient(145deg, #202e5c 0%, #162044 100%);
        --mn-tile-border: #2a3d75;
    }

    body {
        background-color: var(--mn-bg);
        color: #f1f5f9;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .mines-page-container {
        max-width: 1280px;
        margin: 0 auto;
        padding-bottom: 60px;
    }

    /* Cabinet Shell */
    .mines-cabinet {
        background: linear-gradient(180deg, #0d152d 0%, #080d1c 100%);
        border: 1px solid var(--mn-border);
        border-radius: 24px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.08);
        padding: 12px 14px;
        position: relative;
        overflow: hidden;
        max-width: 440px;
        width: 100%;
    }

    /* Top Logo & Jackpot Banner */
    .mines-header {
        text-align: center;
        margin-bottom: 12px;
        position: relative;
    }

    .mines-logo-text {
        font-family: 'Outfit', sans-serif;
        font-size: 1.65rem;
        font-weight: 900;
        letter-spacing: 2px;
        background: linear-gradient(180deg, #fef08a 0%, #f59e0b 60%, #b45309 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 4px 18px rgba(245, 158, 11, 0.35);
        line-height: 1;
        font-style: italic;
    }

    .jackpot-banner {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.15) 0%, rgba(13, 21, 45, 0.8) 100%);
        border: 1px solid rgba(245, 158, 11, 0.35);
        padding: 4px 14px;
        border-radius: 50px;
        margin-top: 4px;
    }

    .jackpot-tag {
        font-size: 0.72rem;
        font-weight: 800;
        color: #38bdf8;
        letter-spacing: 1px;
    }

    .jackpot-amount {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        font-size: 1.05rem;
        color: #fde047;
        text-shadow: 0 0 10px rgba(253, 224, 71, 0.5);
    }

    /* Header Stat Badges */
    .counters-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        padding: 0 4px;
    }

    .counter-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #0f1830;
        border: 1px solid var(--mn-border);
        border-radius: 50px;
        padding: 4px 14px 4px 8px;
        font-size: 0.95rem;
        font-weight: 800;
    }

    .counter-pill.coins {
        color: #fbbf24;
        box-shadow: 0 0 14px rgba(251, 191, 36, 0.25);
    }

    .counter-pill.mines {
        color: #a855f7;
        box-shadow: 0 0 14px rgba(168, 85, 247, 0.25);
    }

    .pill-icon-coin, .pill-icon-bomb {
        width: 22px;
        height: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Multiplier Ladder Strip */
    .ladder-strip-container {
        position: relative;
        margin-bottom: 14px;
        overflow: hidden;
    }

    .ladder-strip {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        padding: 4px 2px;
        scrollbar-width: none;
        scroll-behavior: smooth;
    }
    .ladder-strip::-webkit-scrollbar {
        display: none;
    }

    .ladder-pill {
        flex: 0 0 auto;
        background: #111a33;
        border: 1px solid #1e2c54;
        border-radius: 8px;
        padding: 5px 10px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.8rem;
        font-weight: 700;
        color: #94a3b8;
        transition: all 0.2s ease;
        text-align: center;
        min-width: 58px;
    }

    .ladder-pill.passed {
        background: rgba(16, 185, 129, 0.15);
        border-color: #10b981;
        color: #34d399;
    }

    .ladder-pill.current {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-color: #fde047;
        color: #000;
        font-weight: 800;
        box-shadow: 0 0 14px rgba(245, 158, 11, 0.6);
        transform: scale(1.08);
    }

    .ladder-pill.next {
        border-color: #38bdf8;
        color: #38bdf8;
        background: rgba(56, 189, 248, 0.12);
    }

    /* 5x5 MINES BOARD */
    .mines-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 6px;
        width: 100%;
        max-width: 290px;
        aspect-ratio: 1;
        margin: 0 auto 8px auto;
        perspective: 1000px;
    }

    .mine-tile {
        position: relative;
        width: 100%;
        height: 100%;
        border-radius: 10px;
        cursor: pointer;
        transform-style: preserve-3d;
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s ease;
        user-select: none;
    }

    .mine-tile:hover:not(.revealed):not(.disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(56, 189, 248, 0.25);
    }

    .mine-tile.revealed {
        transform: rotateY(180deg);
        cursor: default;
    }

    .mine-tile.revealed .tile-front {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    .mine-tile.revealed .tile-back {
        opacity: 1;
        visibility: visible;
        z-index: 5;
    }

    .mine-tile.disabled {
        cursor: not-allowed;
    }

    /* Tile Faces */
    .tile-face {
        position: absolute;
        inset: 0;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        transition: opacity 0.2s ease;
    }

    /* Tile Front (Hidden State) */
    .tile-front {
        background: var(--mn-tile-bg);
        border: 1.5px solid var(--mn-tile-border);
        box-shadow: inset 0 1px 3px rgba(255, 255, 255, 0.12), 0 3px 6px rgba(0, 0, 0, 0.4);
    }

    .tile-front::after {
        content: '';
        width: 10px;
        height: 10px;
        border-radius: 3px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px dashed rgba(255, 255, 255, 0.18);
    }

    /* Tile Back (Revealed State) */
    .tile-back {
        transform: rotateY(180deg);
        border-radius: 10px;
        overflow: hidden;
        padding: 2px;
    }

    /* State Types */
    .tile-back.is-coin {
        background: linear-gradient(145deg, #b45309 0%, #78350f 50%, #451a03 100%);
        border: 1.5px solid #fde047;
        box-shadow: 0 0 12px rgba(245, 158, 11, 0.45), inset 0 1px 3px rgba(255, 255, 255, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .tile-back.is-bomb {
        background: linear-gradient(145deg, #4c0519 0%, #1a050a 100%);
        border: 1.5px solid #f43f5e;
        box-shadow: 0 0 14px rgba(244, 63, 94, 0.55);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .tile-back.is-jackpot {
        background: radial-gradient(circle, #78350f 0%, #1c0a00 100%);
        border: 1.5px solid #fde047;
        box-shadow: 0 0 16px rgba(234, 179, 8, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .tile-back.is-dimmed {
        opacity: 0.45;
        filter: grayscale(20%);
    }

    /* 3D SVG Coin & Bomb Containers (Scaled to fit tiles cleanly) */
    .coin-wrapper {
        width: 74%;
        height: 74%;
        max-width: 44px;
        max-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.5));
        animation: coinPop 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .gold-coin-svg {
        width: 100%;
        height: 100%;
        display: block;
    }

    .bomb-wrapper {
        width: 72%;
        height: 72%;
        max-width: 42px;
        max-height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.5));
        animation: bombWiggle 0.4s ease;
    }

    .bomb-svg {
        width: 100%;
        height: 100%;
        display: block;
    }

    .chest-wrapper {
        width: 72%;
        height: 72%;
        max-width: 42px;
        max-height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: chestGlow 1s infinite alternate;
    }

    .chest-icon {
        font-size: 1.5rem;
        color: #fde047;
        filter: drop-shadow(0 0 10px rgba(251, 191, 36, 0.9));
    }

    @media (max-width: 768px) {
        .mines-container {
            padding: 4px 6px 40px !important;
        }
        .mines-logo-box {
            margin-bottom: 4px !important;
        }
        .mines-logo-text {
            font-size: 1.6rem !important;
        }
        .jackpot-banner {
            padding: 2px 10px !important;
            font-size: 0.72rem !important;
            margin-top: 2px !important;
        }
        .counters-bar {
            margin-bottom: 6px !important;
        }
        .counter-pill {
            padding: 2px 10px 2px 6px !important;
            font-size: 0.82rem !important;
        }
        .ladder-strip-container {
            margin-bottom: 8px !important;
        }
        .ladder-pill {
            padding: 3px 6px !important;
            font-size: 0.72rem !important;
            min-width: 48px !important;
        }
        .mines-grid {
            max-width: 255px !important;
            gap: 4px !important;
            margin: 0 auto 6px auto !important;
        }
        .mine-tile, .tile-face, .tile-back {
            border-radius: 8px !important;
        }
        .coin-wrapper, .bomb-wrapper, .chest-wrapper {
            width: 68% !important;
            height: 68% !important;
        }
        .deck-controls-card {
            padding: 6px !important;
            margin-bottom: 6px !important;
            border-radius: 12px !important;
        }
        .btn-deck-trigger {
            height: 44px !important;
            border-radius: 10px !important;
        }
        .btn-main-action {
            height: 44px !important;
            font-size: 1.02rem !important;
            border-radius: 10px !important;
        }
        .btn-icon-square {
            height: 44px !important;
            border-radius: 10px !important;
        }
    }

    @keyframes coinPop {
        0% { transform: scale(0.35) rotate(-25deg); opacity: 0; }
        70% { transform: scale(1.12) rotate(8deg); opacity: 1; }
        100% { transform: scale(1) rotate(0deg); opacity: 1; }
    }

    @keyframes bombWiggle {
        0%, 100% { transform: scale(1); }
        30% { transform: scale(1.2) rotate(-15deg); }
        60% { transform: scale(1.1) rotate(15deg); }
    }

    @keyframes chestGlow {
        from { transform: scale(1); filter: drop-shadow(0 0 8px rgba(251, 191, 36, 0.6)); }
        to { transform: scale(1.1); filter: drop-shadow(0 0 18px rgba(251, 191, 36, 1)); }
    }

    /* Screen Shake on Bomb Hit */
    .shake-board {
        animation: boardShake 0.45s cubic-bezier(.36,.07,.19,.97) both;
    }

    @keyframes boardShake {
        10%, 90% { transform: translate3d(-3px, 0, 0); }
        20%, 80% { transform: translate3d(5px, 0, 0); }
        30%, 50%, 70% { transform: translate3d(-6px, 0, 0); }
        40%, 60% { transform: translate3d(6px, 0, 0); }
    }

    /* HUD Status Bar */
    .hud-status-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #090f20;
        border: 1px solid var(--mn-border);
        border-radius: 12px;
        padding: 8px 14px;
        margin-bottom: 12px;
        font-size: 0.88rem;
    }

    .hud-label {
        color: #94a3b8;
        font-weight: 600;
        margin-right: 4px;
    }

    .hud-value {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
    }

    /* Controls Deck */
    .controls-deck {
        display: grid;
        grid-template-columns: 1fr 1fr 2fr 48px;
        gap: 8px;
        align-items: center;
    }

    .btn-deck-trigger {
        background: linear-gradient(180deg, #182346 0%, #111a33 100%);
        border: 1px solid #2a3d75;
        border-radius: 12px;
        padding: 9px 8px;
        color: #f1f5f9;
        font-weight: 700;
        font-size: 0.85rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2px;
        cursor: pointer;
        transition: all 0.2s ease;
        height: 52px;
    }

    .btn-deck-trigger:hover:not(:disabled) {
        border-color: #38bdf8;
        background: #1c2a54;
    }

    .btn-deck-trigger:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-deck-trigger .trigger-sub {
        font-size: 0.7rem;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
    }

    .btn-deck-trigger .trigger-val {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.95rem;
        color: #fde047;
    }

    /* Big Action Button (Play / Cashout) */
    .btn-main-action {
        height: 52px;
        border-radius: 12px;
        border: none;
        font-family: 'Outfit', sans-serif;
        font-size: 1.15rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-transform: uppercase;
    }

    .btn-main-action.btn-start {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
        box-shadow: 0 4px 18px rgba(16, 185, 129, 0.45);
    }
    .btn-main-action.btn-start:hover:not(:disabled) {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 22px rgba(16, 185, 129, 0.6);
    }

    .btn-main-action.btn-cashout {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #000;
        box-shadow: 0 4px 18px rgba(245, 158, 11, 0.5);
        animation: pulseCashout 1.2s infinite alternate;
    }
    .btn-main-action.btn-cashout:hover:not(:disabled) {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        transform: translateY(-1px);
    }

    @keyframes pulseCashout {
        from { box-shadow: 0 0 10px rgba(245, 158, 11, 0.5); }
        to { box-shadow: 0 0 20px rgba(245, 158, 11, 0.9); }
    }

    .btn-main-action:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none !important;
        box-shadow: none !important;
    }

    .btn-icon-square {
        width: 48px;
        height: 52px;
        background: #111a33;
        border: 1px solid var(--mn-border);
        border-radius: 12px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-icon-square:hover:not(:disabled) {
        color: #f1f5f9;
        border-color: #38bdf8;
    }
    .btn-icon-square:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* Modal / Popups for Quick Bet & Mines Selection */
    .mn-picker-popup {
        display: none;
        position: absolute;
        bottom: 74px;
        left: 18px;
        right: 18px;
        background: #0d1630;
        border: 1px solid #2a3d75;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.7);
        z-index: 50;
        animation: slideUp 0.2s ease;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .chip-btn {
        background: #162040;
        border: 1px solid #253565;
        border-radius: 8px;
        color: #e2e8f0;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 7px 4px;
        text-align: center;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .chip-btn:hover {
        border-color: #38bdf8;
        color: #38bdf8;
        background: #1c2a54;
    }
    .chip-btn.active {
        background: #38bdf8;
        color: #000;
        border-color: #38bdf8;
    }

    /* Win Announcement Overlay */
    .win-overlay-popup {
        display: none;
        position: absolute;
        top: 45%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.95) 0%, rgba(5, 150, 105, 0.95) 100%);
        border: 2px solid #a7f3d0;
        border-radius: 18px;
        padding: 16px 28px;
        text-align: center;
        z-index: 60;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.8), 0 0 30px rgba(16, 185, 129, 0.6);
        animation: winZoom 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes winZoom {
        0% { transform: translate(-50%, -50%) scale(0.6); opacity: 0; }
        100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    }

    /* History & Info Tabs */
    .info-card {
        background: linear-gradient(180deg, #0d152d 0%, #080d1c 100%);
        border: 1px solid var(--mn-border);
        border-radius: 24px;
        padding: 20px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.08);
        width: 100%;
    }

    .nav-tabs-mn .nav-link {
        color: #94a3b8;
        border: none;
        font-weight: 700;
        font-size: 0.88rem;
        padding: 8px 18px;
        border-radius: 50px;
        background: transparent;
        transition: all 0.2s ease;
    }
    .nav-tabs-mn .nav-link.active {
        color: #f1f5f9;
        background: linear-gradient(135deg, #182346 0%, #202e5c 100%);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
    }

    @media (max-width: 991px) {
        .mines-page-container {
            padding-bottom: 90px;
        }
        .mines-cabinet {
            max-width: 480px;
            margin: 0 auto;
        }
        .info-card {
            max-width: 580px;
            margin: 0 auto;
        }
    }

    @media (max-width: 576px) {
        .mines-cabinet {
            padding: 14px 12px;
            border-radius: 18px;
        }
        .info-card {
            padding: 14px 12px;
            border-radius: 18px;
        }
        .mines-logo-text {
            font-size: 1.85rem;
        }
        .counters-bar {
            padding: 0;
        }
        .counter-pill {
            padding: 3px 10px;
            font-size: 0.85rem;
        }
        .jackpot-banner {
            padding: 3px 10px;
        }
        .jackpot-tag {
            font-size: 0.65rem;
        }
        .jackpot-amount {
            font-size: 0.95rem;
        }
    }

    @media (max-width: 420px) {
        .controls-deck {
            gap: 5px;
        }
        .btn-deck-trigger {
            padding: 6px 3px;
            height: 48px;
        }
        .btn-deck-trigger .trigger-val {
            font-size: 0.82rem;
        }
        .btn-main-action {
            height: 48px;
            font-size: 0.92rem;
            padding: 0 6px;
        }
        .btn-icon-square {
            width: 42px;
            height: 48px;
        }
    }
</style>
@endsection

@section('content')
<div class="container-xl py-3">
    <div class="mines-page-container">
        <div class="row g-4 justify-content-center align-items-start">
            
            <!-- LEFT COLUMN: Mines Game Cabinet -->
            <div class="col-12 col-lg-6 col-xl-5 d-flex justify-content-center">
                <div class="mines-cabinet" id="minesCabinet">
                    
                    <!-- Win Toast / Overlay -->
                    <div class="win-overlay-popup" id="winOverlay">
                        <div class="small text-uppercase fw-bold text-white-50" id="winOverlayTitle">YOU CASHED OUT</div>
                        <div class="display-6 fw-bold font-monospace text-white my-1" id="winOverlayAmount">₹0.00</div>
                        <div class="badge bg-dark text-warning fs-6" id="winOverlayMult">1.00x</div>
                    </div>

                    <!-- Header: Title -->
                    <div class="mines-header">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="badge bg-dark border border-secondary text-secondary small">
                                <i class="fa-solid fa-shield-halved text-success me-1"></i> Probably Fair
                            </span>
                            <button class="btn btn-sm text-secondary p-0" id="soundToggleBtn" onclick="toggleSound()" title="Sound On/Off">
                                <i class="fa-solid fa-volume-high fs-5 text-warning" id="soundIcon"></i>
                            </button>
                        </div>
                        
                        <div class="mines-logo-text">MINES</div>
                    </div>

                    <!-- Top Counters Bar (Safe Coins vs Bombs) -->
                    <div class="counters-bar">
                        <div class="counter-pill coins" title="Safe Gold Coins">
                            <span class="pill-icon-coin">
                                <svg viewBox="0 0 100 100" width="22" height="22">
                                    <ellipse cx="50" cy="53" rx="42" ry="42" fill="#78350f" />
                                    <circle cx="50" cy="48" r="42" fill="#f59e0b" />
                                    <circle cx="50" cy="48" r="37" fill="#fef08a" />
                                    <circle cx="50" cy="48" r="33" fill="#eab308" />
                                    <polygon points="50,28 50,48 44,42" fill="#ffffff" />
                                    <polygon points="50,28 50,48 56,42" fill="#ca8a04" />
                                    <polygon points="69,42 50,48 56,42" fill="#ffffff" />
                                    <polygon points="69,42 50,48 62,54" fill="#ca8a04" />
                                    <polygon points="62,67 50,48 62,54" fill="#ffffff" />
                                    <polygon points="62,67 50,48 50,56" fill="#ca8a04" />
                                    <polygon points="38,67 50,48 50,56" fill="#ffffff" />
                                    <polygon points="38,67 50,48 38,54" fill="#ca8a04" />
                                    <polygon points="31,42 50,48 38,54" fill="#ffffff" />
                                    <polygon points="31,42 50,48 44,42" fill="#ca8a04" />
                                </svg>
                            </span>
                            <span id="safeCountBadge">{{ $defaultLadder ? (25 - $defaultMines) : 22 }}</span>
                        </div>

                        <div class="counter-pill mines" title="Hidden Mines">
                            <span class="pill-icon-bomb">
                                <svg viewBox="0 0 100 100" width="20" height="20">
                                    <path d="M 50 28 Q 62 18 68 12" fill="none" stroke="#e2e8f0" stroke-width="4" stroke-linecap="round" />
                                    <circle cx="68" cy="12" r="4" fill="#f43f5e" />
                                    <rect x="44" y="24" width="12" height="6" rx="2" fill="#475569" />
                                    <circle cx="50" cy="56" r="36" fill="#3b82f6" />
                                    <ellipse cx="38" cy="44" rx="8" ry="5" fill="#ffffff" opacity="0.6" />
                                </svg>
                            </span>
                            <span id="minesCountBadge">{{ $defaultMines }}</span>
                        </div>
                    </div>

                    <!-- Multiplier Step Ladder Strip -->
                    <div class="ladder-strip-container">
                        <div class="ladder-strip" id="ladderStrip">
                            @foreach($defaultLadder as $idx => $item)
                                <div class="ladder-pill {{ $idx === 0 ? 'next' : '' }}" id="ladderStep{{ $item['step'] }}">
                                    {{ number_format($item['multiplier'], 2) }}x
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- 5x5 MINES INTERACTIVE BOARD -->
                    <div class="mines-grid" id="minesGrid">
                        @for($i = 0; $i < 25; $i++)
                            <div class="mine-tile disabled" id="tile{{ $i }}" onclick="handleTileClick({{ $i }})">
                                <div class="tile-face tile-front"></div>
                                <div class="tile-face tile-back" id="tileBack{{ $i }}"></div>
                            </div>
                        @endfor
                    </div>

                    <!-- HUD Status Bar (Balance & Live Win) -->
                    <div class="hud-status-bar">
                        <div>
                            <span class="hud-label">Balance:</span>
                            <span class="hud-value text-info" id="userBalanceDisplay">₹{{ number_format((float)($user?->balance ?? 0), 2) }}</span>
                        </div>
                        <div>
                            <span class="hud-label" id="winLabelText">WIN:</span>
                            <span class="hud-value text-warning" id="liveWinDisplay">₹0.00</span>
                        </div>
                    </div>

                    <!-- Controls Deck -->
                    <div class="controls-deck position-relative">
                        <!-- Bet Amount Trigger -->
                        <button class="btn-deck-trigger" id="btnBetTrigger" onclick="togglePopup('betPopup')">
                            <span class="trigger-sub">Bet</span>
                            <span class="trigger-val" id="betDisplayVal">₹50</span>
                        </button>

                        <!-- Mines Count Trigger -->
                        <button class="btn-deck-trigger" id="btnMinesTrigger" onclick="togglePopup('minesPopup')">
                            <span class="trigger-sub">Mines</span>
                            <span class="trigger-val" id="minesDisplayVal">3</span>
                        </button>

                        <!-- Big Main Action Button -->
                        <button class="btn-main-action btn-start flex-fill" id="btnMainAction" onclick="handleMainAction()">
                            <i class="fa-solid fa-play" id="actionIcon"></i>
                            <span id="actionBtnText">START BET</span>
                        </button>

                        <!-- Random Pick / Auto Tile Button -->
                        <button class="btn-icon-square" id="btnRandomPick" onclick="pickRandomTile()" title="Pick Random Tile" disabled>
                            <i class="fa-solid fa-shuffle"></i>
                        </button>

                        <!-- BET AMOUNT PICKER POPUP -->
                        <div class="mn-picker-popup" id="betPopup">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-light small text-uppercase">Select Bet Amount</span>
                                <button class="btn btn-sm text-secondary p-0" onclick="togglePopup('betPopup')">&times;</button>
                            </div>
                            <div class="input-group input-group-sm mb-3">
                                <span class="input-group-text bg-dark border-secondary text-warning">₹</span>
                                <input type="number" class="form-control bg-dark border-secondary text-light font-monospace fw-bold" id="betInput" value="50" min="1" max="25000" step="10">
                            </div>
                            <div class="d-grid grid-3 gap-1 mb-2" style="grid-template-columns: repeat(4, 1fr);">
                                <button class="chip-btn" onclick="setBet(10)">10</button>
                                <button class="chip-btn active" onclick="setBet(50)">50</button>
                                <button class="chip-btn" onclick="setBet(100)">100</button>
                                <button class="chip-btn" onclick="setBet(500)">500</button>
                            </div>
                            <div class="d-flex gap-1">
                                <button class="chip-btn flex-fill" onclick="multiplyBet(2)">2X</button>
                                <button class="chip-btn flex-fill" onclick="multiplyBet(0.5)">1/2</button>
                                <button class="chip-btn flex-fill" onclick="setBet(1000)">1,000</button>
                                <button class="chip-btn flex-fill" onclick="togglePopup('betPopup')">DONE</button>
                            </div>
                        </div>

                        <!-- MINES COUNT PICKER POPUP -->
                        <div class="mn-picker-popup" id="minesPopup">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-light small text-uppercase">Select Number of Mines</span>
                                <button class="btn btn-sm text-secondary p-0" onclick="togglePopup('minesPopup')">&times;</button>
                            </div>
                            <div class="d-grid gap-1 mb-2" style="grid-template-columns: repeat(4, 1fr);">
                                @foreach([3, 5, 8, 10, 15, 20, 24] as $m)
                                    <button class="chip-btn {{ $m === 3 ? 'active' : '' }}" id="mineChip{{ $m }}" onclick="selectMinesCount({{ $m }})">
                                        <i class="fa-solid fa-bomb text-danger me-1"></i>{{ $m }}
                                    </button>
                                @endforeach
                            </div>
                            <button class="btn btn-sm btn-primary w-100 fw-bold mt-1" onclick="togglePopup('minesPopup')">APPLY MINES</button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- RIGHT COLUMN: History & Rules Card -->
            <div class="col-12 col-lg-6 col-xl-7">
                <div class="info-card mt-0" id="minesInfoCard">
                    <!-- Tab Navigation & Action -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                        <ul class="nav nav-pills nav-tabs-mn p-1 bg-dark bg-opacity-75 rounded-pill border border-secondary border-opacity-50" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#historyTab">
                                    <i class="fa-solid fa-clock-rotate-left me-1"></i> My Bets History
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#rulesTab">
                                    <i class="fa-solid fa-book-open me-1"></i> Rules & Fair
                                </button>
                            </li>
                        </ul>
                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="reloadHistory()" title="Refresh History">
                            <i class="fa-solid fa-rotate me-1"></i> Refresh
                        </button>
                    </div>

                    <div class="tab-content">
                        <!-- History Table Tab -->
                        <div class="tab-pane fade show active" id="historyTab">
                            @php
                                $totalPlayed = count($recentGames);
                                $totalWon = collect($recentGames)->where('status', 'cashed_out')->sum('win_amount');
                                $maxMult = collect($recentGames)->max('multiplier') ?? 1.0;
                            @endphp
                            <!-- Quick Stats Summary -->
                            <div class="row g-2 mb-3">
                                <div class="col-4">
                                    <div class="p-2 rounded-3 text-center" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255,255,255,0.07);">
                                        <div class="text-secondary small" style="font-size: 0.72rem;">GAMES PLAYED</div>
                                        <div class="fw-bold text-light font-monospace">{{ $totalPlayed }}</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 rounded-3 text-center" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255,255,255,0.07);">
                                        <div class="text-secondary small" style="font-size: 0.72rem;">TOTAL WON</div>
                                        <div class="fw-bold text-success font-monospace">₹{{ number_format($totalWon, 2) }}</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 rounded-3 text-center" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255,255,255,0.07);">
                                        <div class="text-secondary small" style="font-size: 0.72rem;">BEST MULTIPLIER</div>
                                        <div class="fw-bold text-warning font-monospace">{{ number_format($maxMult, 2) }}x</div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive rounded-3" style="max-height: 520px; overflow-y: auto;">
                                <table class="table table-dark table-hover table-sm align-middle mb-0" style="font-size: 0.85rem;">
                                    <thead class="sticky-top" style="background: #090f20; z-index: 2;">
                                        <tr class="text-secondary text-uppercase small" style="letter-spacing: 0.5px;">
                                            <th>Game ID</th>
                                            <th>Mines</th>
                                            <th>Bet</th>
                                            <th>Multiplier</th>
                                            <th class="text-end">Payout</th>
                                        </tr>
                                    </thead>
                                    <tbody id="minesHistoryBody">
                                        @forelse($recentGames as $rg)
                                            <tr>
                                                <td class="font-monospace text-secondary small">{{ substr($rg['game_id'], -6) }}</td>
                                                <td><span class="badge bg-secondary">{{ $rg['mines_count'] }} 💣</span></td>
                                                <td class="font-monospace text-warning">₹{{ number_format($rg['bet_amount'], 2) }}</td>
                                                <td class="font-monospace fw-bold {{ $rg['status'] === 'cashed_out' ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($rg['multiplier'], 2) }}x
                                                </td>
                                                <td class="text-end font-monospace {{ $rg['status'] === 'cashed_out' ? 'text-success fw-bold' : 'text-secondary' }}">
                                                    {{ $rg['status'] === 'cashed_out' ? '+₹'.number_format($rg['win_amount'], 2) : '₹0.00' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-secondary py-4">No Mines games played yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Rules Tab -->
                        <div class="tab-pane fade" id="rulesTab">
                            <div class="d-flex flex-column gap-3 text-secondary small">
                                <div class="p-3 rounded-3" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255,255,255,0.07);">
                                    <div class="fw-bold text-light mb-1 d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-gamepad text-primary"></i>
                                        <span>How to Play & Objective</span>
                                    </div>
                                    <p class="mb-0">Select your bet amount and number of hidden mines (3 to 24). Click <strong>START BET</strong>, then tap any tile on the 5x5 grid to uncover safe Gold Coins.</p>
                                </div>

                                <div class="p-3 rounded-3" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255,255,255,0.07);">
                                    <div class="fw-bold text-light mb-1 d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-sack-dollar text-success"></i>
                                        <span>Cashout Anytime & Dynamic Multipliers</span>
                                    </div>
                                    <p class="mb-0">Every safe coin revealed increases your multiplier ladder. You can click <strong>CASHOUT</strong> at any step to bank your winnings immediately!</p>
                                </div>

                                <div class="p-3 rounded-3" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255,255,255,0.07);">
                                    <div class="fw-bold text-light mb-1 d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-shield-halved text-info"></i>
                                        <span>100% Cryptographic Provably Fair</span>
                                    </div>
                                    <p class="mb-0">Every single game round generates a SHA-256 hash of the server seed before you make any click, guaranteeing completely fair, immutable, and tamper-proof outcomes.</p>
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
     * MINES ARCADE CLIENT ENGINE
     * Web Audio Procedural FX, 3D Flip Card Board, Live Combinatorics Sync
     * ========================================================================= */

    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const IS_AUTHENTICATED = {{ Auth::check() ? 'true' : 'false' }};

    // Game Client State
    let gameState = 'idle'; // 'idle', 'active', 'finished'
    let selectedBet = 50.00;
    let selectedMines = {{ $defaultMines }};
    let currentStep = 0;
    let currentMultiplier = 1.00;
    let potentialWin = 0.00;
    let activeLadder = @json($defaultLadder);
    let activeGameData = @json($activeGame);

    // Audio Context (Procedural zero-dependency synthesizer)
    let audioCtx = null;
    let isSoundEnabled = localStorage.getItem('mines_sound') !== 'false';

    function initAudio() {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
    }

    ['click', 'touchstart', 'pointerdown'].forEach(evt => {
        document.addEventListener(evt, () => initAudio(), { once: true });
    });

    function toggleSound() {
        isSoundEnabled = !isSoundEnabled;
        localStorage.setItem('mines_sound', isSoundEnabled ? 'true' : 'false');
        updateSoundIcon();
    }

    function updateSoundIcon() {
        const icon = document.getElementById('soundIcon');
        if (isSoundEnabled) {
            icon.className = 'fa-solid fa-volume-high fs-5 text-warning';
        } else {
            icon.className = 'fa-solid fa-volume-xmark fs-5 text-secondary';
        }
    }

    // Audio Synthesizers
    function playCoinChime(step = 1) {
        if (!isSoundEnabled) return;
        try {
            initAudio();
            const now = audioCtx.currentTime;
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            
            // Ascending pitch with each step
            const baseFreq = 780 + Math.min(600, step * 40);
            osc.frequency.setValueAtTime(baseFreq, now);
            osc.frequency.exponentialRampToValueAtTime(baseFreq * 1.35, now + 0.12);
            
            gain.gain.setValueAtTime(0.18, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.22);
            osc.start(now);
            osc.stop(now + 0.22);
        } catch(e) {}
    }

    function playBombExplosion() {
        if (!isSoundEnabled) return;
        try {
            initAudio();
            const now = audioCtx.currentTime;
            
            // Noise blast
            const bufSize = Math.floor(audioCtx.sampleRate * 0.55);
            const buf = audioCtx.createBuffer(1, bufSize, audioCtx.sampleRate);
            const data = buf.getChannelData(0);
            for (let i = 0; i < bufSize; i++) {
                data[i] = (Math.random() * 2 - 1) * Math.exp(-4 * (i / bufSize));
            }
            const noise = audioCtx.createBufferSource();
            noise.buffer = buf;

            const filter = audioCtx.createBiquadFilter();
            filter.type = 'lowpass';
            filter.frequency.setValueAtTime(1000, now);
            filter.frequency.exponentialRampToValueAtTime(50, now + 0.5);

            const gain = audioCtx.createGain();
            gain.gain.setValueAtTime(0.5, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.55);

            noise.connect(filter);
            filter.connect(gain);
            gain.connect(audioCtx.destination);
            noise.start(now);
            noise.stop(now + 0.55);

            // Sub bass boom
            const osc = audioCtx.createOscillator();
            const subGain = audioCtx.createGain();
            osc.connect(subGain);
            subGain.connect(audioCtx.destination);
            osc.frequency.setValueAtTime(130, now);
            osc.frequency.exponentialRampToValueAtTime(30, now + 0.45);
            subGain.gain.setValueAtTime(0.4, now);
            subGain.gain.exponentialRampToValueAtTime(0.001, now + 0.45);
            osc.start(now);
            osc.stop(now + 0.45);
        } catch(e) {}
    }

    function playCashoutFanfare() {
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
                const startTime = now + (idx * 0.06);
                osc.frequency.setValueAtTime(freq, startTime);
                gain.gain.setValueAtTime(0.2, startTime);
                gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.4);
                osc.start(startTime);
                osc.stop(startTime + 0.4);
            });
        } catch(e) {}
    }

    /* =========================================================================
     * UI POPUP & PICKER HELPERS
     * ========================================================================= */
    function togglePopup(id) {
        if (gameState === 'active') return; // Locked while in game
        const el = document.getElementById(id);
        const isShown = el.style.display === 'block';
        document.querySelectorAll('.mn-picker-popup').forEach(p => p.style.display = 'none');
        el.style.display = isShown ? 'none' : 'block';
    }

    function setBet(amt) {
        selectedBet = Math.max(1, parseFloat(amt));
        document.getElementById('betInput').value = selectedBet;
        document.getElementById('betDisplayVal').innerText = '₹' + selectedBet.toFixed(0);
        document.querySelectorAll('#betPopup .chip-btn').forEach(btn => btn.classList.remove('active'));
    }

    function multiplyBet(factor) {
        selectedBet = Math.max(1, Math.round(selectedBet * factor));
        document.getElementById('betInput').value = selectedBet;
        document.getElementById('betDisplayVal').innerText = '₹' + selectedBet.toFixed(0);
    }

    document.getElementById('betInput')?.addEventListener('input', (e) => {
        selectedBet = Math.max(1, parseFloat(e.target.value) || 1);
        document.getElementById('betDisplayVal').innerText = '₹' + selectedBet.toFixed(0);
    });

    async function selectMinesCount(count) {
        if (gameState === 'active') return;
        selectedMines = parseInt(count);
        document.getElementById('minesDisplayVal').innerText = selectedMines;
        document.getElementById('minesCountBadge').innerText = selectedMines;
        document.getElementById('safeCountBadge').innerText = (25 - selectedMines);

        document.querySelectorAll('#minesPopup .chip-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('mineChip' + count)?.classList.add('active');
        document.getElementById('minesPopup').style.display = 'none';

        // Fetch new ladder for this mines count
        try {
            const res = await fetch(`{{ route('api.mines.ladder') }}?mines=${selectedMines}`);
            const data = await res.json();
            if (data.success) {
                activeLadder = data.ladder;
                renderLadderStrip();
            }
        } catch(e) {}
    }

    function renderLadderStrip() {
        const strip = document.getElementById('ladderStrip');
        let html = '';
        activeLadder.forEach((item, idx) => {
            let cls = 'ladder-pill';
            if (item.step < currentStep) {
                cls += ' passed';
            } else if (item.step === currentStep) {
                cls += ' current';
            } else if (item.step === currentStep + 1) {
                cls += ' next';
            }
            html += `<div class="${cls}" id="ladderStep${item.step}">${parseFloat(item.multiplier).toFixed(2)}x</div>`;
        });
        strip.innerHTML = html;

        // Auto scroll to current
        if (currentStep > 0) {
            const curEl = document.getElementById(`ladderStep${currentStep}`);
            if (curEl) {
                curEl.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            }
        }
    }

    /* =========================================================================
     * CORE GAME ACTIONS
     * ========================================================================= */
    async function handleMainAction() {
        if (!IS_AUTHENTICATED) {
            window.location.href = "{{ route('login') }}";
            return;
        }

        if (gameState === 'idle' || gameState === 'finished') {
            await startNewGame();
        } else if (gameState === 'active') {
            await cashOutGame();
        }
    }

    async function startNewGame() {
        const btn = document.getElementById('btnMainAction');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> STARTING...';

        try {
            const res = await fetch("{{ route('api.mines.start') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    amount: selectedBet,
                    mines_count: selectedMines
                })
            });

            const data = await res.json();
            btn.disabled = false;

            if (!data.success) {
                alert(data.message || 'Failed to start game');
                renderActionBtn('start');
                return;
            }

            // Sync wallet balance
            if (data.new_balance !== undefined) {
                document.getElementById('userWalletBalance').innerText = '₹' + parseFloat(data.new_balance).toFixed(2);
                document.getElementById('userBalanceDisplay').innerText = '₹' + parseFloat(data.new_balance).toFixed(2);
            }

            // Reset board & init active state
            resetBoardUI();
            gameState = 'active';
            currentStep = 0;
            currentMultiplier = 1.00;
            potentialWin = selectedBet;

            activeGameData = data.game;
            renderActionBtn('cashout', 0);
            renderLadderStrip();

            // Enable random pick button
            document.getElementById('btnRandomPick').disabled = false;
            document.getElementById('btnBetTrigger').disabled = true;
            document.getElementById('btnMinesTrigger').disabled = true;

        } catch(e) {
            btn.disabled = false;
            renderActionBtn('start');
            alert('Network error. Please try again.');
        }
    }

    /* =========================================================================
     * REALISTIC 3D SVG ASSETS (Gold Coin, Bomb, Chest)
     * ========================================================================= */
    function getGoldCoinSVG() {
        return `
            <div class="coin-wrapper">
                <svg class="gold-coin-svg" viewBox="0 0 100 100" width="100%" height="100%">
                    <defs>
                        <linearGradient id="coinEdgeGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stop-color="#b45309" />
                            <stop offset="100%" stop-color="#78350f" />
                        </linearGradient>
                        <linearGradient id="coinRimBevel" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#fef08a" />
                            <stop offset="35%" stop-color="#f59e0b" />
                            <stop offset="70%" stop-color="#d97706" />
                            <stop offset="100%" stop-color="#92400e" />
                        </linearGradient>
                        <radialGradient id="coinBodyGrad" cx="35%" cy="30%" r="68%">
                            <stop offset="0%" stop-color="#fffde7" />
                            <stop offset="20%" stop-color="#fef08a" />
                            <stop offset="55%" stop-color="#eab308" />
                            <stop offset="85%" stop-color="#ca8a04" />
                            <stop offset="100%" stop-color="#854d0e" />
                        </radialGradient>
                        <linearGradient id="starLightFacet" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#ffffff" />
                            <stop offset="50%" stop-color="#fef9c3" />
                            <stop offset="100%" stop-color="#fde047" />
                        </linearGradient>
                        <linearGradient id="starDarkFacet" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#d97706" />
                            <stop offset="100%" stop-color="#78350f" />
                        </linearGradient>
                    </defs>
                    <g>
                        <!-- 3D Lower Edge Thickness for Coin Depth -->
                        <ellipse cx="50" cy="54" rx="42" ry="42" fill="url(#coinEdgeGrad)" />
                        <!-- Outer Golden Bevel Rim -->
                        <circle cx="50" cy="48" r="42" fill="url(#coinRimBevel)" />
                        <circle cx="50" cy="48" r="38" fill="#a16207" />
                        <!-- Shiny Golden Face -->
                        <circle cx="50" cy="48" r="36" fill="url(#coinBodyGrad)" />
                        <!-- Inner Milled Groove Rings -->
                        <circle cx="50" cy="48" r="32" fill="none" stroke="#b45309" stroke-width="1.2" stroke-dasharray="3,2" opacity="0.65" />
                        <circle cx="50" cy="48" r="30" fill="none" stroke="#fffde7" stroke-width="1" opacity="0.45" />
                        <!-- 3D 10-Faceted Embossed Star Crest -->
                        <polygon points="50,28 50,48 44,42" fill="url(#starLightFacet)" />
                        <polygon points="50,28 50,48 56,42" fill="url(#starDarkFacet)" />
                        <polygon points="69,42 50,48 56,42" fill="url(#starLightFacet)" />
                        <polygon points="69,42 50,48 62,54" fill="url(#starDarkFacet)" />
                        <polygon points="62,67 50,48 62,54" fill="url(#starLightFacet)" />
                        <polygon points="62,67 50,48 50,56" fill="url(#starDarkFacet)" />
                        <polygon points="38,67 50,48 50,56" fill="url(#starLightFacet)" />
                        <polygon points="38,67 50,48 38,54" fill="url(#starDarkFacet)" />
                        <polygon points="31,42 50,48 38,54" fill="url(#starLightFacet)" />
                        <polygon points="31,42 50,48 44,42" fill="url(#starDarkFacet)" />
                        <!-- Specular Glint Highlight Arc -->
                        <path d="M 20 40 A 34 34 0 0 1 50 14" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" opacity="0.85" />
                    </g>
                </svg>
            </div>
        `;
    }

    function getBombSVG() {
        return `
            <div class="bomb-wrapper">
                <svg class="bomb-svg" viewBox="0 0 100 100" width="100%" height="100%">
                    <defs>
                        <radialGradient id="bombSphereGrad" cx="38%" cy="32%" r="65%">
                            <stop offset="0%" stop-color="#93c5fd" />
                            <stop offset="25%" stop-color="#3b82f6" />
                            <stop offset="55%" stop-color="#1e1b4b" />
                            <stop offset="85%" stop-color="#0f172a" />
                            <stop offset="100%" stop-color="#020617" />
                        </radialGradient>
                    </defs>
                    <g>
                        <!-- Spark & Fuse -->
                        <path d="M 50 28 Q 62 18 68 12" fill="none" stroke="#e2e8f0" stroke-width="3.5" stroke-linecap="round" />
                        <polygon points="68,12 73,10 75,5 72,12 77,15 70,16 68,22 66,16 60,15 65,12 63,5 66,10" fill="#f43f5e" />
                        <circle cx="68" cy="12" r="3" fill="#fef08a" />
                        <!-- Neck Collar -->
                        <rect x="44" y="24" width="12" height="6" rx="2" fill="#475569" stroke="#94a3b8" stroke-width="1" />
                        <!-- Main 3D Sphere -->
                        <circle cx="50" cy="56" r="36" fill="url(#bombSphereGrad)" />
                        <!-- Shiny Specular Reflection -->
                        <ellipse cx="38" cy="43" rx="9" ry="5.5" transform="rotate(-30 38 43)" fill="#ffffff" opacity="0.7" />
                    </g>
                </svg>
            </div>
        `;
    }

    function getJackpotSVG() {
        return `
            <div class="chest-wrapper">
                <i class="fa-solid fa-gem chest-icon"></i>
            </div>
        `;
    }

    async function handleTileClick(tileIndex) {
        if (gameState !== 'active') return;

        const tile = document.getElementById(`tile${tileIndex}`);
        if (!tile || tile.classList.contains('revealed') || tile.classList.contains('disabled')) return;

        tile.classList.add('disabled');

        try {
            const res = await fetch("{{ route('api.mines.reveal') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ tile_index: tileIndex })
            });

            const data = await res.json();

            if (!data.success) {
                tile.classList.remove('disabled');
                alert(data.message || 'Tile reveal failed');
                return;
            }

            if (data.hit_mine) {
                // EXPLOSION!
                handleExplosion(data, tileIndex);
            } else {
                // SAFE TILE!
                handleSafeTile(data, tileIndex);
            }

        } catch(e) {
            tile.classList.remove('disabled');
        }
    }

    function handleSafeTile(data, tileIndex) {
        currentStep = data.step;
        currentMultiplier = data.current_multiplier;
        potentialWin = data.potential_win;

        // Play chime sound
        playCoinChime(currentStep);

        // Reveal tile face
        const tile = document.getElementById(`tile${tileIndex}`);
        const back = document.getElementById(`tileBack${tileIndex}`);

        back.className = 'tile-face tile-back is-coin';
        back.innerHTML = getGoldCoinSVG();
        tile.classList.add('revealed');

        // Update live win display
        document.getElementById('liveWinDisplay').innerText = '₹' + potentialWin.toFixed(2);
        document.getElementById('liveWinDisplay').className = 'hud-value text-success';
        document.getElementById('winLabelText').innerText = 'PROFIT:';

        // Update cashout button
        renderActionBtn('cashout', potentialWin);

        // Update ladder strip
        renderLadderStrip();

        // Check if auto-won (cleared all tiles)
        if (data.status === 'cashed_out' && data.is_auto_win) {
            showWinCelebration(data.win_amount, currentMultiplier, true);
            finishGame(data.game);
        }
    }

    function handleExplosion(data, tileIndex) {
        gameState = 'finished';
        playBombExplosion();

        // Screen Shake
        const cabinet = document.getElementById('minesCabinet');
        cabinet.classList.add('shake-board');
        setTimeout(() => cabinet.classList.remove('shake-board'), 450);

        // Reveal the hit bomb
        const tile = document.getElementById(`tile${tileIndex}`);
        const back = document.getElementById(`tileBack${tileIndex}`);
        back.className = 'tile-face tile-back is-bomb';
        back.innerHTML = getBombSVG();
        tile.classList.add('revealed');

        // Reveal all other remaining bombs & safe tiles dimly
        const allMines = data.game.mine_positions || [];

        for (let i = 0; i < 25; i++) {
            if (i === tileIndex) continue;
            const t = document.getElementById(`tile${i}`);
            const b = document.getElementById(`tileBack${i}`);
            if (!t.classList.contains('revealed')) {
                if (allMines.includes(i)) {
                    b.className = 'tile-face tile-back is-bomb is-dimmed';
                    b.innerHTML = getBombSVG();
                } else {
                    b.className = 'tile-face tile-back is-coin is-dimmed';
                    b.innerHTML = getGoldCoinSVG();
                }
                t.classList.add('revealed');
            }
        }

        // Reset HUD
        document.getElementById('liveWinDisplay').innerText = '₹0.00';
        document.getElementById('liveWinDisplay').className = 'hud-value text-danger';
        document.getElementById('winLabelText').innerText = 'LOST:';

        // Action button boom state then start
        const btn = document.getElementById('btnMainAction');
        btn.className = 'btn-main-action flex-fill';
        btn.style.background = '#e11d48';
        btn.innerHTML = '<i class="fa-solid fa-skull"></i> EXPLODED';
        btn.disabled = true;

        setTimeout(() => {
            renderActionBtn('start');
            btn.disabled = false;
            document.getElementById('btnBetTrigger').disabled = false;
            document.getElementById('btnMinesTrigger').disabled = false;
            document.getElementById('btnRandomPick').disabled = true;
        }, 1800);

        // Refresh History Table
        reloadHistory();
    }

    async function cashOutGame() {
        const btn = document.getElementById('btnMainAction');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> CASHING OUT...';

        try {
            const res = await fetch("{{ route('api.mines.cashout') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();
            btn.disabled = false;

            if (!data.success) {
                alert(data.message || 'Cashout failed');
                renderActionBtn('cashout', potentialWin);
                return;
            }

            // Play victory chime
            playCashoutFanfare();

            // Sync wallet
            if (data.new_balance !== undefined) {
                document.getElementById('userWalletBalance').innerText = '₹' + parseFloat(data.new_balance).toFixed(2);
                document.getElementById('userBalanceDisplay').innerText = '₹' + parseFloat(data.new_balance).toFixed(2);
            }

            // Show big popup win
            showWinCelebration(data.win_amount, currentMultiplier);

            // Finish game and reveal remaining bombs
            finishGame(data.game);

        } catch(e) {
            btn.disabled = false;
            renderActionBtn('cashout', potentialWin);
            alert('Network error on cashout');
        }
    }

    function finishGame(game) {
        gameState = 'finished';

        // Reveal remaining bombs dimly
        const allMines = game.mine_positions || [];

        for (let i = 0; i < 25; i++) {
            const t = document.getElementById(`tile${i}`);
            const b = document.getElementById(`tileBack${i}`);
            if (!t.classList.contains('revealed')) {
                if (allMines.includes(i)) {
                    b.className = 'tile-face tile-back is-bomb is-dimmed';
                    b.innerHTML = getBombSVG();
                } else {
                    b.className = 'tile-face tile-back is-coin is-dimmed';
                    b.innerHTML = getGoldCoinSVG();
                }
                t.classList.add('revealed');
            }
        }

        renderActionBtn('start');
        document.getElementById('btnBetTrigger').disabled = false;
        document.getElementById('btnMinesTrigger').disabled = false;
        document.getElementById('btnRandomPick').disabled = true;

        // Reload history table
        reloadHistory();
    }

    function showWinCelebration(amount, mult, isGrand = false) {
        const popup = document.getElementById('winOverlay');
        document.getElementById('winOverlayTitle').innerText = isGrand ? 'GRAND BOARD CLEAR!' : 'YOU CASHED OUT';
        document.getElementById('winOverlayAmount').innerText = '+₹' + parseFloat(amount).toFixed(2);
        document.getElementById('winOverlayMult').innerText = parseFloat(mult).toFixed(2) + 'x';

        popup.style.display = 'block';
        setTimeout(() => {
            popup.style.display = 'none';
        }, 2800);
    }

    function renderActionBtn(type, cashoutAmt = 0) {
        const btn = document.getElementById('btnMainAction');
        btn.style.background = '';
        if (type === 'start') {
            btn.className = 'btn-main-action btn-start flex-fill';
            btn.innerHTML = '<i class="fa-solid fa-play"></i> START BET';
        } else if (type === 'cashout') {
            btn.className = 'btn-main-action btn-cashout flex-fill';
            if (cashoutAmt > 0) {
                btn.innerHTML = `<i class="fa-solid fa-sack-dollar"></i> CASHOUT ₹${parseFloat(cashoutAmt).toFixed(2)}`;
            } else {
                btn.innerHTML = `<i class="fa-solid fa-sack-dollar"></i> CASHOUT`;
            }
        }
    }

    function resetBoardUI() {
        for (let i = 0; i < 25; i++) {
            const tile = document.getElementById(`tile${i}`);
            const back = document.getElementById(`tileBack${i}`);
            tile.className = 'mine-tile';
            back.className = 'tile-face tile-back';
            back.innerHTML = '';
        }
        document.getElementById('liveWinDisplay').innerText = '₹0.00';
        document.getElementById('liveWinDisplay').className = 'hud-value text-warning';
        document.getElementById('winLabelText').innerText = 'WIN:';
    }

    function pickRandomTile() {
        if (gameState !== 'active') return;
        const unopened = [];
        for (let i = 0; i < 25; i++) {
            const tile = document.getElementById(`tile${i}`);
            if (!tile.classList.contains('revealed') && !tile.classList.contains('disabled')) {
                unopened.push(i);
            }
        }
        if (unopened.length === 0) return;
        const randomTile = unopened[Math.floor(Math.random() * unopened.length)];
        handleTileClick(randomTile);
    }

    async function reloadHistory() {
        try {
            const res = await fetch("{{ route('api.mines.history') }}");
            const data = await res.json();
            if (!data.success || !data.history) return;

            const tbody = document.getElementById('minesHistoryBody');
            if (data.history.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-secondary py-3">No Mines games played yet.</td></tr>';
                return;
            }

            let html = '';
            data.history.forEach(h => {
                const won = (h.status === 'cashed_out');
                html += `
                    <tr>
                        <td class="font-monospace text-secondary small">${h.game_id.substring(h.game_id.length - 6)}</td>
                        <td><span class="badge bg-secondary">${h.mines_count} 💣</span></td>
                        <td class="font-monospace text-warning">₹${parseFloat(h.bet_amount).toFixed(2)}</td>
                        <td class="font-monospace fw-bold ${won ? 'text-success' : 'text-danger'}">${parseFloat(h.multiplier).toFixed(2)}x</td>
                        <td class="text-end font-monospace ${won ? 'text-success fw-bold' : 'text-secondary'}">
                            ${won ? '+₹' + parseFloat(h.win_amount).toFixed(2) : '₹0.00'}
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } catch(e) {}
    }

    // Restore active game if exists on page load
    window.addEventListener('DOMContentLoaded', () => {
        updateSoundIcon();

        if (activeGameData && activeGameData.status === 'active') {
            gameState = 'active';
            selectedBet = parseFloat(activeGameData.bet_amount);
            selectedMines = parseInt(activeGameData.mines_count);
            currentStep = parseInt(activeGameData.current_step);
            currentMultiplier = parseFloat(activeGameData.current_multiplier);
            potentialWin = parseFloat(activeGameData.potential_win);
            activeLadder = activeGameData.ladder;

            document.getElementById('betDisplayVal').innerText = '₹' + selectedBet.toFixed(0);
            document.getElementById('minesDisplayVal').innerText = selectedMines;
            document.getElementById('minesCountBadge').innerText = selectedMines;
            document.getElementById('safeCountBadge').innerText = (25 - selectedMines);

            resetBoardUI();

            // Re-reveal opened safe tiles
            (activeGameData.opened_tiles || []).forEach(idx => {
                const tile = document.getElementById(`tile${idx}`);
                const back = document.getElementById(`tileBack${idx}`);
                back.className = 'tile-face tile-back is-coin';
                back.innerHTML = getGoldCoinSVG();
                tile.classList.add('revealed');
            });

            document.getElementById('liveWinDisplay').innerText = '₹' + potentialWin.toFixed(2);
            document.getElementById('liveWinDisplay').className = 'hud-value text-success';
            document.getElementById('winLabelText').innerText = 'PROFIT:';

            renderActionBtn('cashout', potentialWin);
            renderLadderStrip();

            document.getElementById('btnRandomPick').disabled = false;
            document.getElementById('btnBetTrigger').disabled = true;
            document.getElementById('btnMinesTrigger').disabled = true;
        }
    });
</script>
@endsection
