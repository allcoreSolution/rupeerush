@extends('layouts.app')

@section('title', 'Andar Bahar Live Casino - rupeerush')

@section('styles')
<style>
    /* Luxury Casino Theme */
    :root {
        --casino-felt: #0b3c29;
        --casino-felt-dark: #062418;
        --gold-primary: #d4af37;
        --gold-light: #f6e27a;
        --andar-red: #ef4444;
        --andar-red-glow: rgba(239, 68, 68, 0.45);
        --bahar-blue: #3b82f6;
        --bahar-blue-glow: rgba(59, 130, 246, 0.45);
    }

    body {
        background-color: #050b14;
        color: #f8fafc;
    }

    .casino-container {
        max-width: 1140px;
        margin: 0 auto;
    }

    /* Top Bar Header */
    .casino-header-bar {
        background: linear-gradient(135deg, #0d1a2d 0%, #070e1c 100%);
        border: 1px solid rgba(212, 175, 55, 0.25);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
    }

    /* Green Felt Gaming Table */
    .casino-table {
        background: radial-gradient(circle at 50% 35%, #135338 0%, var(--casino-felt) 60%, var(--casino-felt-dark) 100%);
        border: 5px solid #3d2314;
        outline: 2px solid var(--gold-primary);
        border-radius: 24px;
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.7), inset 0 0 60px rgba(0, 0, 0, 0.6);
        position: relative;
        overflow: hidden;
    }

    .casino-table::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: radial-gradient(ellipse at center, rgba(255, 255, 255, 0.04) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Playing Card Graphics */
    .playing-card {
        width: 46px;
        height: 66px;
        background: #ffffff;
        border-radius: 6px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.45);
        display: inline-flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 3px 4px;
        position: relative;
        user-select: none;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        border: 1px solid #cbd5e1;
    }

    .playing-card.card-red {
        color: #dc2626;
    }
    .playing-card.card-black {
        color: #0f172a;
    }

    .card-corner-top, .card-corner-bottom {
        line-height: 1;
        font-weight: 800;
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.85rem;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .card-corner-bottom {
        transform: rotate(180deg);
    }
    .card-center-suit {
        font-size: 1.4rem;
        line-height: 1;
        text-align: center;
    }

    .playing-card.highlight-winner {
        transform: scale(1.1);
        border: 3px solid var(--gold-light);
        box-shadow: 0 0 24px var(--gold-light), 0 0 40px rgba(245, 158, 11, 0.6);
        animation: cardPulse 1s infinite alternate;
        z-index: 10;
    }

    @keyframes cardPulse {
        from { transform: scale(1.08); }
        to { transform: scale(1.15); }
    }

    /* Joker Spotlight Display */
    .joker-spotlight {
        background: rgba(0, 0, 0, 0.45);
        border: 2px dashed rgba(212, 175, 55, 0.6);
        border-radius: 14px;
        padding: 6px 16px;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
    }

    .joker-card-lg {
        width: 58px;
        height: 82px;
        border-radius: 8px;
        border: 2px solid var(--gold-primary);
        box-shadow: 0 6px 20px rgba(0,0,0,0.5), 0 0 16px rgba(212, 175, 55, 0.4);
    }
    .joker-card-lg .card-corner-top, .joker-card-lg .card-corner-bottom {
        font-size: 0.8rem;
    }
    .joker-card-lg .card-center-suit {
        font-size: 1.4rem;
    }

    /* Andar & Bahar Betting Boxes */
    .bet-box {
        background: rgba(10, 20, 32, 0.75);
        backdrop-filter: blur(8px);
        border-radius: 16px;
        padding: 10px 12px;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .bet-box-andar {
        border: 2px solid var(--andar-red);
        box-shadow: 0 8px 24px rgba(239, 68, 68, 0.2);
    }
    .bet-box-andar:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px var(--andar-red-glow);
        background: rgba(239, 68, 68, 0.15);
    }
    .bet-box-andar.selected {
        border-width: 3px;
        background: linear-gradient(180deg, rgba(239, 68, 68, 0.35) 0%, rgba(185, 28, 28, 0.45) 100%);
        box-shadow: 0 0 30px var(--andar-red-glow);
    }

    .bet-box-bahar {
        border: 2px solid var(--bahar-blue);
        box-shadow: 0 8px 24px rgba(59, 130, 246, 0.2);
    }
    .bet-box-bahar:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px var(--bahar-blue-glow);
        background: rgba(59, 130, 246, 0.15);
    }
    .bet-box-bahar.selected {
        border-width: 3px;
        background: linear-gradient(180deg, rgba(59, 130, 246, 0.35) 0%, rgba(29, 78, 216, 0.45) 100%);
        box-shadow: 0 0 30px var(--bahar-blue-glow);
    }

    .bet-box-title {
        font-size: 1.45rem;
        font-weight: 900;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .bet-box-title .hindi {
        font-size: 1.05rem;
        opacity: 0.85;
    }
    .bet-box-multiplier {
        font-size: 0.85rem;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 50px;
        display: inline-block;
        margin-top: 6px;
    }

    /* Cards Tray Rail */
    .cards-rail-wrap {
        min-height: 64px;
        background: rgba(0, 0, 0, 0.35);
        border-radius: 10px;
        padding: 4px 6px;
        display: flex;
        align-items: center;
        gap: 6px;
        overflow-x: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--gold-primary) rgba(0,0,0,0.5);
    }
    .cards-rail-wrap::-webkit-scrollbar {
        height: 6px;
    }
    .cards-rail-wrap::-webkit-scrollbar-thumb {
        background: var(--gold-primary);
        border-radius: 10px;
    }

    /* Chip Rack Selector */
    .chip-rack-btn {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        font-weight: 800;
        font-size: 0.78rem;
        cursor: pointer;
        border: 2px dashed rgba(255, 255, 255, 0.6);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5), inset 0 0 8px rgba(0, 0, 0, 0.4);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s;
        user-select: none;
        color: #ffffff;
    }
    .chip-rack-btn:hover {
        transform: translateY(-4px) scale(1.08);
    }
    .chip-rack-btn.active {
        transform: translateY(-6px) scale(1.15);
        border-style: solid;
        border-color: #ffffff;
        box-shadow: 0 0 20px var(--gold-light), 0 8px 18px rgba(0, 0, 0, 0.6);
    }

    .chip-10 { background: radial-gradient(circle, #3b82f6 30%, #1d4ed8 100%); }
    .chip-50 { background: radial-gradient(circle, #10b981 30%, #047857 100%); }
    .chip-100 { background: radial-gradient(circle, #ef4444 30%, #b91c1c 100%); }
    .chip-500 { background: radial-gradient(circle, #8b5cf6 30%, #6d28d9 100%); }
    .chip-1000 { background: radial-gradient(circle, #f59e0b 30%, #b45309 100%); color: #000; }
    .chip-5000 { background: radial-gradient(circle, #ec4899 30%, #be185d 100%); }

    /* Side Bets Range Cards */
    .side-bet-card {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 14px;
        padding: 8px 4px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .side-bet-card:hover {
        border-color: var(--gold-primary);
        background: rgba(212, 175, 55, 0.1);
        transform: translateY(-2px);
    }
    .side-bet-card.selected {
        border-color: var(--gold-light);
        background: rgba(212, 175, 55, 0.25);
        box-shadow: 0 0 16px rgba(246, 226, 122, 0.3);
    }

    /* History Road Capsules & Pills */
    .road-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 8px 3px 4px;
        border-radius: 50px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.12);
        transition: all 0.2s ease;
        user-select: none;
        cursor: default;
    }
    .road-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    }
    .road-pill-andar {
        background: rgba(239, 68, 68, 0.12);
        border-color: rgba(239, 68, 68, 0.35);
    }
    .road-pill-andar:hover {
        border-color: #ef4444;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
    }
    .road-pill-bahar {
        background: rgba(59, 130, 246, 0.12);
        border-color: rgba(59, 130, 246, 0.35);
    }
    .road-pill-bahar:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.35);
    }
    .road-circle {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.78rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.4);
        flex-shrink: 0;
    }
    .road-circle-andar {
        background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        color: #ffffff;
        border: 1.5px solid #fca5a5;
    }
    .road-circle-bahar {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: #ffffff;
        border: 1.5px solid #93c5fd;
    }
    .road-round-num {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.74rem;
        font-weight: 700;
        color: #e2e8f0;
        letter-spacing: 0.2px;
    }

    /* Dealing Animated Overlay Banner */
    .dealing-banner {
        position: absolute;
        top: 14px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.88);
        border: 1px solid var(--gold-primary);
        padding: 6px 20px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 800;
        color: var(--gold-light);
        z-index: 20;
        letter-spacing: 1px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Tabs Styling */
    .nav-andar-tabs .nav-link {
        color: #94a3b8;
        background: transparent;
        border: 1px solid transparent;
        transition: all 0.2s;
        font-weight: 700;
    }
    .nav-andar-tabs .nav-link:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.05);
    }
    .nav-andar-tabs .nav-link.active {
        color: #ffffff;
        background: #0284c7;
        border-color: #0284c7;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4);
    }

    /* Mini Playing Card for History */
    .mini-card {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 800;
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.85rem;
        background: #ffffff;
        box-shadow: 0 1px 4px rgba(0,0,0,0.3);
        border: 1px solid #cbd5e1;
    }
    .mini-card.mini-red { color: #dc2626; }
    .mini-card.mini-black { color: #0f172a; }

    /* Summary Metric Card */
    .metric-card {
        background: rgba(15, 23, 42, 0.65);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 12px;
        text-align: center;
    }

    /* Side Bets Grid Layout */
    .side-bets-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 6px;
    }
    .side-bet-range-text {
        font-size: 0.76rem;
        font-weight: 700;
        color: #f1f5f9;
        line-height: 1.1;
    }
    .side-bet-multiplier-badge {
        font-size: 0.68rem;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        color: #fbbf24;
        line-height: 1.1;
        margin-top: 2px;
    }

    /* Bet Dock Styles */
    .bet-dock-card {
        background: linear-gradient(135deg, #0d1a2d 0%, #070e1c 100%);
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    .btn-quick-calc {
        font-size: 0.72rem;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 6px;
        line-height: 1.1;
        color: #e2e8f0;
    }

    /* ========================================================
       MOBILE-FIRST STREAMLINED VIEW (< 768px)
       ALL-IN-ONE SCREEN CASINO EXPERIENCE
       ======================================================== */
    @media (max-width: 767.98px) {
        .casino-container {
            padding: 2px 2px 50px !important;
            max-width: 100% !important;
        }

        /* Compact 1-Row Header */
        .casino-header-bar {
            padding: 6px 8px !important;
            margin-bottom: 5px !important;
            border-radius: 12px !important;
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 6px !important;
        }
        .game-main-title {
            font-size: 0.82rem !important;
            letter-spacing: -0.2px;
        }
        .header-round-sub {
            font-size: 0.62rem !important;
            line-height: 1;
        }
        .timer-inner {
            background: rgba(0, 0, 0, 0.55);
            border: 1px solid rgba(245, 158, 11, 0.4);
            padding: 3px 8px !important;
        }
        .timer-digits {
            font-size: 0.85rem !important;
            line-height: 1;
        }

        /* Compact Green Table */
        .casino-table {
            padding: 6px 6px !important;
            border-radius: 14px !important;
            border-width: 3px !important;
            margin-bottom: 5px !important;
        }

        /* Compact Dealing Status Banner */
        .dealing-banner {
            top: 4px !important;
            padding: 2px 10px !important;
            font-size: 0.68rem !important;
            border-radius: 20px !important;
            max-width: 90% !important;
            white-space: nowrap !important;
            z-index: 25 !important;
        }

        /* Horizontal Streamlined Joker Strip */
        .joker-spotlight-wrap {
            margin-bottom: 4px !important;
        }
        .joker-spotlight {
            padding: 4px 10px !important;
            border-radius: 10px !important;
            border-width: 1.5px !important;
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 6px !important;
            width: 100% !important;
        }
        .joker-card-lg {
            width: 34px !important;
            height: 48px !important;
            border-radius: 4px !important;
            border-width: 1.5px !important;
            padding: 2px !important;
        }
        .joker-card-lg .card-corner-top,
        .joker-card-lg .card-corner-bottom {
            font-size: 0.58rem !important;
            line-height: 0.9 !important;
        }
        .joker-card-lg .card-center-suit {
            font-size: 0.9rem !important;
            line-height: 1 !important;
        }

        /* Andar vs Bahar Compact Battle Arena */
        .bet-box {
            padding: 6px 6px 4px !important;
            border-radius: 10px !important;
        }
        .bet-box-title {
            font-size: 0.88rem !important;
            letter-spacing: 0.5px !important;
            gap: 4px !important;
        }
        .bet-box-multiplier {
            font-size: 0.68rem !important;
            padding: 1px 5px !important;
            margin-top: 0 !important;
        }

        /* Cards Rail Compact */
        .cards-rail-wrap {
            min-height: 44px !important;
            height: 44px !important;
            padding: 2px 3px !important;
            border-radius: 6px !important;
            gap: 3px !important;
            margin-bottom: 2px !important;
        }

        /* Playing Card Graphics on Mobile */
        .playing-card {
            width: 28px !important;
            height: 40px !important;
            padding: 1px 2px !important;
            border-radius: 3px !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4) !important;
            flex-shrink: 0 !important;
        }
        .playing-card .card-corner-top,
        .playing-card .card-corner-bottom {
            font-size: 0.55rem !important;
            line-height: 0.9 !important;
        }
        .playing-card .card-center-suit {
            font-size: 0.78rem !important;
            line-height: 1 !important;
        }

        /* Side Bets Compact Grid */
        .side-bets-container {
            padding: 4px 6px !important;
            margin-top: 4px !important;
            border-radius: 10px !important;
        }
        .side-bets-grid {
            gap: 3px !important;
        }
        .side-bet-card {
            padding: 3px 1px !important;
            border-radius: 6px !important;
        }
        .side-bet-range-text {
            font-size: 0.64rem !important;
        }
        .side-bet-multiplier-badge {
            font-size: 0.58rem !important;
        }

        /* Bet Dock & Controls Compact */
        .bet-dock-card {
            padding: 6px 8px !important;
            margin-bottom: 5px !important;
            border-radius: 12px !important;
        }
        .chip-rack-btn {
            width: 32px !important;
            height: 32px !important;
            font-size: 0.68rem !important;
            border-width: 1.5px !important;
            flex-shrink: 0 !important;
        }
        .chip-rack-scroll {
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
        }
        .chip-rack-scroll::-webkit-scrollbar {
            display: none;
        }
        .btn-quick-calc {
            font-size: 0.68rem !important;
            padding: 2px 6px !important;
            height: 28px !important;
        }
        .target-indicator-pill {
            font-size: 0.70rem !important;
            padding: 3px 8px !important;
            max-width: 120px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .place-bet-btn {
            height: 34px !important;
            font-size: 0.80rem !important;
            padding: 4px 12px !important;
        }

        /* Tabs Navigation Compact */
        .nav-andar-tabs .nav-link {
            font-size: 0.72rem !important;
            padding: 5px 8px !important;
        }
    }
</style>
@endsection

@section('content')
<div class="casino-container py-3">

    <!-- Header Navigation & Stats Bar (Compact Streamlined) -->
    <div class="casino-header-bar p-2 p-md-3 mb-2 d-flex align-items-center justify-content-between gap-2">
        <!-- Game Title & Round ID -->
        <div class="d-flex align-items-center gap-2 min-w-0">
            <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;" title="Back to Lobby">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div class="overflow-hidden">
                <div class="d-flex align-items-center gap-1">
                    <span class="game-main-title fw-bold text-white text-nowrap"><i class="fa-solid fa-diamond text-warning me-1"></i>Andar Bahar</span>
                    <span class="badge bg-danger rounded-pill px-1 py-0 small d-none d-sm-inline" style="font-size: 0.62rem;">LIVE</span>
                </div>
                <div class="header-round-sub text-white-50 font-monospace text-nowrap">
                    #<span id="liveRoundId" class="text-warning fw-bold">{{ $roundData['round_id'] }}</span>
                    <span class="d-none d-md-inline ms-1">• A: <span class="text-danger fw-bold" id="headerAndarMultiplier">{{ $setting->andar_multiplier ?? '1.95' }}x</span> | B: <span class="text-primary fw-bold" id="headerBaharMultiplier">{{ $setting->bahar_multiplier ?? '1.95' }}x</span></span>
                </div>
            </div>
        </div>

        <!-- Center Countdown Timer Pill -->
        <div class="header-timer-pill text-center flex-shrink-0">
            <div class="timer-inner d-flex align-items-center gap-1 rounded-pill">
                <i class="fa-regular fa-clock text-warning" style="font-size: 0.72rem;"></i>
                <span class="font-monospace fw-bold text-warning timer-digits" id="liveCountdown">{{ str_pad($roundData['remaining_seconds'], 2, '0', STR_PAD_LEFT) }}s</span>
            </div>
        </div>

        <!-- Right User Balance & Controls -->
        <div class="d-flex align-items-center gap-1 gap-sm-2 flex-shrink-0">
            <button class="btn btn-outline-secondary btn-sm text-light rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;" id="soundToggleBtn" onclick="toggleAudio()" title="Sound On/Off">
                <i class="fa-solid fa-volume-high" id="soundIcon" style="font-size: 0.78rem;"></i>
            </button>
            <a href="{{ route('wallet.deposit') }}" class="btn btn-sm btn-success rounded-pill px-2 py-1 fw-bold d-flex align-items-center gap-1 shadow-sm font-monospace text-nowrap" style="font-size: 0.75rem;">
                <i class="fa-solid fa-wallet text-warning" style="font-size: 0.72rem;"></i>
                <span id="headerUserBalance">₹{{ number_format(Auth::user()->balance ?? 0, 2) }}</span>
                <i class="fa-solid fa-plus-circle" style="font-size: 0.68rem;"></i>
            </a>
        </div>
    </div>

    <!-- MAIN CASINO GREEN FELT ARENA -->
    <div class="casino-table p-2 p-md-3 mb-2">
        
        <!-- Live Dealing Status Banner -->
        <div class="dealing-banner" id="dealingBanner" style="{{ $roundData['is_locked'] ? '' : 'display: none;' }}">
            <div class="spinner-grow spinner-grow-sm text-warning" role="status" style="width: 12px; height: 12px;"></div>
            <span id="dealingBannerText">BETS CLOSED • DEALING CARDS LIVE</span>
        </div>

        <!-- CENTER SPOTLIGHT: JOKER CARD (Horizontal Compact Bar) -->
        <div class="joker-spotlight-wrap mb-2 text-center">
            <div class="joker-spotlight">
                <div class="joker-title-tag text-uppercase fw-bold text-start">
                    <div style="font-size: 0.65rem; color: #fbbf24;"><i class="fa-solid fa-crown text-warning me-1"></i> THE JOKER</div>
                    <div class="small text-white-50" style="font-size: 0.60rem;">ट्रम्प कार्ड</div>
                </div>

                @php
                    $joker = $roundData['round_model']->joker_card ?? ['rank' => 'K', 'symbol' => '♥', 'color' => 'red'];
                    $isRed = ($joker['color'] ?? 'red') === 'red';
                @endphp
                <div class="playing-card joker-card-lg {{ $isRed ? 'card-red' : 'card-black' }} flex-shrink-0" id="jokerCardElement">
                    <div class="card-corner-top">
                        <span id="jokerRankTop">{{ $joker['rank'] ?? 'K' }}</span>
                        <span id="jokerSuitTop">{{ $joker['symbol'] ?? '♥' }}</span>
                    </div>
                    <div class="card-center-suit" id="jokerSuitCenter">
                        {{ $joker['symbol'] ?? '♥' }}
                    </div>
                    <div class="card-corner-bottom">
                        <span id="jokerRankBottom">{{ $joker['rank'] ?? 'K' }}</span>
                        <span id="jokerSuitBottom">{{ $joker['symbol'] ?? '♥' }}</span>
                    </div>
                </div>

                <div class="joker-hint-tag text-end" id="jokerHint">
                    <div class="text-white-50" style="font-size: 0.60rem;">TARGET RANK</div>
                    <div class="fw-bold font-monospace text-warning" style="font-size: 0.85rem;">
                        Rank <span id="jokerTargetRank">{{ $joker['rank'] ?? 'K' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 50:50 SPLIT ARENA: ANDAR VS BAHAR -->
        <div class="row g-2 mb-1">
            
            <!-- LEFT BOX: ANDAR (अंदर) -->
            <div class="col-6">
                <div class="bet-box bet-box-andar h-100" id="betBoxAndar" onclick="selectSide('andar')">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="bet-box-title text-danger">
                            <i class="fa-solid fa-arrow-left"></i>
                            <span>ANDAR</span>
                            <span class="hindi d-none d-sm-inline">(अंदर)</span>
                        </div>
                        <span class="bet-box-multiplier bg-danger text-white" id="andarMultiplierBadge">
                            {{ $setting->andar_multiplier ?? '1.95' }}X
                        </span>
                    </div>

                    <!-- Dealt Cards Rail for Andar -->
                    <div class="d-flex align-items-center justify-content-between text-start text-white-50 mb-1 fw-bold" style="font-size: 0.65rem;">
                        <span>Cards: <span id="andarCardCount" class="text-white">0</span></span>
                        <div class="badge bg-danger rounded-pill px-2 py-0 font-monospace" id="myAndarBetBadge" style="display: none; font-size: 0.65rem;">
                            ₹<span id="myAndarBetAmount">0</span>
                        </div>
                    </div>
                    <div class="cards-rail-wrap" id="andarCardsRail">
                        <div class="text-white-50 small w-100 text-center py-1" id="andarEmptyMsg" style="font-size: 0.68rem;">
                            Waiting deal
                        </div>
                    </div>

                    <!-- Hidden status hook for script -->
                    <span class="d-none" id="andarStatusText">Bet</span>
                </div>
            </div>

            <!-- RIGHT BOX: BAHAR (बाहर) -->
            <div class="col-6">
                <div class="bet-box bet-box-bahar h-100" id="betBoxBahar" onclick="selectSide('bahar')">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="bet-box-title text-primary">
                            <span>BAHAR</span>
                            <span class="hindi d-none d-sm-inline">(बाहर)</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </div>
                        <span class="bet-box-multiplier bg-primary text-white" id="baharMultiplierBadge">
                            {{ $setting->bahar_multiplier ?? '1.95' }}X
                        </span>
                    </div>

                    <!-- Dealt Cards Rail for Bahar -->
                    <div class="d-flex align-items-center justify-content-between text-start text-white-50 mb-1 fw-bold" style="font-size: 0.65rem;">
                        <span>Cards: <span id="baharCardCount" class="text-white">0</span></span>
                        <div class="badge bg-primary rounded-pill px-2 py-0 font-monospace" id="myBaharBetBadge" style="display: none; font-size: 0.65rem;">
                            ₹<span id="myBaharBetAmount">0</span>
                        </div>
                    </div>
                    <div class="cards-rail-wrap" id="baharCardsRail">
                        <div class="text-white-50 small w-100 text-center py-1" id="baharEmptyMsg" style="font-size: 0.68rem;">
                            Waiting deal
                        </div>
                    </div>

                    <!-- Hidden status hook for script -->
                    <span class="d-none" id="baharStatusText">Bet</span>
                </div>
            </div>

        </div>

        <!-- SIDE BETS: TOTAL CARDS RANGE (Compact 5-Col Grid) -->
        <div class="side-bets-container p-2 rounded-3 mt-1" style="background: rgba(0, 0, 0, 0.45); border: 1px solid rgba(255, 255, 255, 0.08);">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="small fw-bold text-warning text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-sparkles me-1"></i> Side Bets (Total Cards)
                </span>
                <span class="small text-white-50" style="font-size: 0.62rem;">High Multipliers</span>
            </div>
            <div class="side-bets-grid">
                <div class="side-bet-col">
                    <div class="side-bet-card" id="sideRange_1_5" onclick="selectRange('1_5', {{ $setting->andar_bahar_range_1_5 ?? 3.50 }})">
                        <div class="side-bet-range-text">1–5</div>
                        <div class="side-bet-multiplier-badge" id="sideBadge_1_5">
                            {{ $setting->andar_bahar_range_1_5 ?? '3.50' }}X
                        </div>
                    </div>
                </div>
                <div class="side-bet-col">
                    <div class="side-bet-card" id="sideRange_6_10" onclick="selectRange('6_10', {{ $setting->andar_bahar_range_6_10 ?? 4.00 }})">
                        <div class="side-bet-range-text">6–10</div>
                        <div class="side-bet-multiplier-badge" id="sideBadge_6_10">
                            {{ $setting->andar_bahar_range_6_10 ?? '4.00' }}X
                        </div>
                    </div>
                </div>
                <div class="side-bet-col">
                    <div class="side-bet-card" id="sideRange_11_15" onclick="selectRange('11_15', {{ $setting->andar_bahar_range_11_15 ?? 5.00 }})">
                        <div class="side-bet-range-text">11–15</div>
                        <div class="side-bet-multiplier-badge" id="sideBadge_11_15">
                            {{ $setting->andar_bahar_range_11_15 ?? '5.00' }}X
                        </div>
                    </div>
                </div>
                <div class="side-bet-col">
                    <div class="side-bet-card" id="sideRange_16_25" onclick="selectRange('16_25', {{ $setting->andar_bahar_range_16_25 ?? 4.50 }})">
                        <div class="side-bet-range-text">16–25</div>
                        <div class="side-bet-multiplier-badge" id="sideBadge_16_25">
                            {{ $setting->andar_bahar_range_16_25 ?? '4.50' }}X
                        </div>
                    </div>
                </div>
                <div class="side-bet-col">
                    <div class="side-bet-card" id="sideRange_26_plus" onclick="selectRange('26_plus', {{ $setting->andar_bahar_range_26_plus ?? 8.00 }})">
                        <div class="side-bet-range-text">26+</div>
                        <div class="side-bet-multiplier-badge" id="sideBadge_26_plus">
                            {{ $setting->andar_bahar_range_26_plus ?? '8.00' }}X
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- BET CONTROLS & CHIP RACK PANEL (All-in-One Compact Betting Dock) -->
    <div class="bet-dock-card card border-0 shadow-lg rounded-3 p-2 mb-2">
        <!-- Row 1: Chip Selector + Quick Multipliers -->
        <div class="d-flex align-items-center justify-content-between gap-1 mb-2">
            <div class="chip-rack-scroll d-flex align-items-center gap-1 flex-grow-1 overflow-x-auto">
                <div class="chip-rack-btn chip-10" onclick="setChipValue(10)">10</div>
                <div class="chip-rack-btn chip-50" onclick="setChipValue(50)">50</div>
                <div class="chip-rack-btn chip-100 active" onclick="setChipValue(100)">100</div>
                <div class="chip-rack-btn chip-500" onclick="setChipValue(500)">500</div>
                <div class="chip-rack-btn chip-1000" onclick="setChipValue(1000)">1K</div>
                <div class="chip-rack-btn chip-5000" onclick="setChipValue(5000)">5K</div>
            </div>
            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                <button type="button" class="btn btn-sm btn-outline-secondary btn-quick-calc" onclick="adjustAmount(0.5)">½</button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-quick-calc" onclick="adjustAmount(2)">2X</button>
                <button type="button" class="btn btn-sm btn-outline-danger btn-quick-calc" onclick="clearSelection()" title="Reset">C</button>
            </div>
        </div>

        <!-- Row 2: Selected Target + Amount Input + Place Bet Action Button -->
        <div class="d-flex align-items-center gap-1 gap-sm-2">
            <!-- Target Indicator Pill -->
            <div class="target-indicator-pill px-2 py-1 rounded-pill flex-shrink-0" style="background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.12);">
                <span class="text-white-50 small">Target:</span>
                <strong class="text-danger fw-bold text-uppercase ms-1" id="selectedTargetLabel">ANDAR ({{ $setting->andar_multiplier ?? '1.95' }}X)</strong>
            </div>

            <!-- Amount Input -->
            <div class="input-group input-group-sm flex-grow-1" style="min-width: 75px; max-width: 110px;">
                <span class="input-group-text bg-dark border-secondary text-white-50 px-1" style="font-size: 0.72rem;">₹</span>
                <input type="number" class="form-control bg-dark text-white border-secondary fw-bold font-monospace px-1 text-center" id="customAmountInput" value="100" min="{{ $setting->andar_bahar_min_bet ?? 10 }}" max="{{ $setting->andar_bahar_max_bet ?? 50000 }}" oninput="onCustomAmountChange(this.value)" style="font-size: 0.8rem;">
            </div>

            <!-- Hidden chip value label hook for JS -->
            <span class="d-none" id="currentSelectedChipLabel">Selected: ₹100</span>

            <!-- Place Bet Button -->
            @auth
                <button class="btn btn-warning btn-sm rounded-pill px-3 py-2 fw-bold shadow-lg flex-shrink-0 place-bet-btn" id="placeBetSubmitBtn" onclick="submitBet()">
                    <i class="fa-solid fa-bolt me-1"></i> PLACE BET
                </button>
            @else
                <a href="{{ route('login') }}" class="btn btn-warning btn-sm rounded-pill px-3 py-2 fw-bold shadow-lg flex-shrink-0 place-bet-btn">
                    LOGIN TO PLAY
                </a>
            @endauth
        </div>
    </div>

    <!-- TABS NAVIGATION: RESULTS, HISTORY & MY BETS -->
    <div class="card border-0 shadow-lg rounded-4 p-2 mb-3" style="background: #0d1a2d; border: 1px solid rgba(255,255,255,0.1) !important;">
        <ul class="nav nav-pills nav-fill nav-andar-tabs gap-2" id="andarBaharTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active py-2 rounded-3" id="tab-results-btn" data-bs-toggle="pill" data-bs-target="#tab-results" type="button" role="tab">
                    <i class="fa-solid fa-chart-simple me-2 text-warning"></i> Results & Bead Road
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-2 rounded-3" id="tab-history-btn" data-bs-toggle="pill" data-bs-target="#tab-history" type="button" role="tab" onclick="fetchHistory()">
                    <i class="fa-solid fa-clock-rotate-left me-2 text-info"></i> Rounds History
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-2 rounded-3" id="tab-mybets-btn" data-bs-toggle="pill" data-bs-target="#tab-mybets" type="button" role="tab" onclick="fetchMyBets()">
                    <i class="fa-solid fa-receipt me-2 text-success"></i> My Bets
                    @auth
                        <span class="badge bg-danger rounded-pill ms-1" id="myBetsCountBadge">0</span>
                    @endauth
                </button>
            </li>
        </ul>
    </div>

    <!-- TAB CONTENTS -->
    <div class="tab-content" id="andarBaharTabsContent">
        
        <!-- TAB 1: RESULTS & BEAD ROAD -->
        <div class="tab-pane fade show active" id="tab-results" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 p-3 mb-4" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.08) !important;">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0 fw-bold text-white d-flex align-items-center gap-2">
                            <i class="fa-solid fa-chart-pie text-warning"></i> Recent Results Bead Road
                        </h5>
                        <div class="small text-white-50 mt-1">Live outcome road map from recent settled rounds</div>
                    </div>
                    <!-- Win Ratio Indicator -->
                    <div class="d-flex align-items-center gap-3">
                        <div class="small text-white">
                            <span class="badge bg-danger rounded-circle p-1 me-1">A</span> Andar: <strong class="text-danger" id="andarPercentVal">{{ $andarPercent }}%</strong>
                        </div>
                        <div class="small text-white">
                            <span class="badge bg-primary rounded-circle p-1 me-1">B</span> Bahar: <strong class="text-primary" id="baharPercentVal">{{ $baharPercent }}%</strong>
                        </div>
                    </div>
                </div>

                <!-- Win Distribution Bar -->
                <div class="progress mb-3" style="height: 10px; background: rgba(255,255,255,0.1); border-radius: 10px;">
                    <div class="progress-bar bg-danger" id="andarProgressBar" style="width: {{ $andarPercent }}%;"></div>
                    <div class="progress-bar bg-primary" id="baharProgressBar" style="width: {{ $baharPercent }}%;"></div>
                </div>

                <!-- Bead Road Grid Circles with Round Number -->
                <div class="d-flex align-items-center gap-2 flex-wrap" id="beadRoadContainer">
                    @forelse($recentHistory as $rh)
                        @php
                            $isAndar = $rh->winning_side === 'andar';
                            $roundNum = ltrim(substr($rh->round_id, -5), '0') ?: substr($rh->round_id, -4);
                        @endphp
                        <div class="road-pill {{ $isAndar ? 'road-pill-andar' : 'road-pill-bahar' }}" title="Round #{{ $rh->round_id }} - Won: {{ strtoupper($rh->winning_side) }} in {{ $rh->total_cards_dealt ?? 0 }} cards">
                            <span class="road-circle {{ $isAndar ? 'road-circle-andar' : 'road-circle-bahar' }}">{{ $isAndar ? 'A' : 'B' }}</span>
                            <span class="road-round-num">#{{ $roundNum }}</span>
                        </div>
                    @empty
                        <div class="text-white-50 small py-2">No completed rounds recorded yet. Dealing in progress!</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TAB 2: ROUNDS HISTORY -->
        <div class="tab-pane fade" id="tab-history" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 p-3 mb-4" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.08) !important;">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0 fw-bold text-white d-flex align-items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-info"></i> All Rounds Game History
                        </h5>
                        <div class="small text-white-50 mt-1">Probably fair round outcomes with joker and winning match cards</div>
                    </div>
                    <button class="btn btn-sm btn-outline-info rounded-pill px-3" onclick="fetchHistory()">
                        <i class="fa-solid fa-rotate me-1"></i> Refresh History
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                        <thead class="text-white-50 small text-uppercase">
                            <tr>
                                <th class="ps-3">Round ID</th>
                                <th>Joker Card</th>
                                <th>Winner</th>
                                <th>Matching Card</th>
                                <th>Cards Dealt</th>
                                <th class="pe-3 text-end">Settled Time</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            @forelse($recentHistory as $rh)
                                <tr>
                                    <td class="ps-3 font-monospace text-warning fw-bold">#{{ $rh->round_id }}</td>
                                    <td>
                                        @php
                                            $jk = $rh->joker_card;
                                            $isRedJk = in_array($jk['suit'] ?? '', ['♥', '♦']) || ($jk['color'] ?? '') === 'red';
                                        @endphp
                                        <span class="mini-card {{ $isRedJk ? 'mini-red' : 'mini-black' }}">
                                            {{ $jk['rank'] ?? '?' }}{{ $jk['suit'] ?? ($jk['symbol'] ?? '') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $rh->winning_side === 'andar' ? 'bg-danger' : 'bg-primary' }} text-uppercase px-2 py-1">
                                            {{ strtoupper($rh->winning_side) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $wc = $rh->winning_card;
                                            $isRedWc = in_array($wc['suit'] ?? '', ['♥', '♦']) || ($wc['color'] ?? '') === 'red';
                                        @endphp
                                        @if($wc)
                                            <span class="mini-card {{ $isRedWc ? 'mini-red' : 'mini-black' }}">
                                                {{ $wc['rank'] ?? '' }}{{ $wc['suit'] ?? ($wc['symbol'] ?? '') }}
                                            </span>
                                        @else
                                            <span class="text-white-50">-</span>
                                        @endif
                                    </td>
                                    <td class="font-monospace text-white-50">{{ $rh->total_cards_dealt ?? 0 }} cards</td>
                                    <td class="pe-3 text-end text-white-50 small font-monospace">
                                        {{ $rh->created_at ? $rh->created_at->format('h:i:s A') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-white-50">
                                        Loading game rounds history...
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 3: MY BETS -->
        <div class="tab-pane fade" id="tab-mybets" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 p-3 mb-4" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.08) !important;">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0 fw-bold text-white d-flex align-items-center gap-2">
                            <i class="fa-solid fa-receipt text-success"></i> My Andar Bahar Bets
                        </h5>
                        <div class="small text-white-50 mt-1">Live statement of your active and past wagers on Andar Bahar</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="fetchMyBets()">
                            <i class="fa-solid fa-rotate me-1"></i> Refresh
                        </button>
                        <a href="{{ route('bets.history', ['game' => 'andar_bahar']) }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                            Full Passbook <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Player Quick Stats Ribbon -->
                <div class="row g-2 mb-3" id="playerStatsRibbon">
                    <div class="col-6 col-md-3">
                        <div class="metric-card">
                            <div class="small text-white-50">TOTAL BETS</div>
                            <div class="fs-5 fw-bold text-white font-monospace" id="statTotalBets">0</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="metric-card">
                            <div class="small text-white-50">TOTAL WAGERED</div>
                            <div class="fs-5 fw-bold text-warning font-monospace" id="statTotalWagered">₹0.00</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="metric-card">
                            <div class="small text-white-50">TOTAL WON</div>
                            <div class="fs-5 fw-bold text-success font-monospace" id="statTotalWon">₹0.00</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="metric-card">
                            <div class="small text-white-50">NET PROFIT</div>
                            <div class="fs-5 fw-bold font-monospace" id="statNetProfit">₹0.00</div>
                        </div>
                    </div>
                </div>

                <!-- Bets History Table -->
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                        <thead class="text-white-50 small text-uppercase">
                            <tr>
                                <th class="ps-3">Round ID</th>
                                <th>Side / Selection</th>
                                <th>Stake Amount</th>
                                <th>Multiplier (Guna)</th>
                                <th>Winnings</th>
                                <th>Status</th>
                                <th class="pe-3 text-end">Time</th>
                            </tr>
                        </thead>
                        <tbody id="myBetsTableBody">
                            <tr>
                                <td colspan="7" class="text-center py-4 text-white-50">
                                    Loading your bets...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- CELEBRATORY WIN MODAL -->
<div class="modal fade" id="winCelebrationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 text-center p-4" style="background: linear-gradient(135deg, #062418 0%, #0b3c29 100%); border: 2px solid var(--gold-light) !important; color: #fff; box-shadow: 0 0 50px rgba(246, 226, 122, 0.4);">
            <div class="modal-body">
                <div class="fs-1 text-warning mb-2 animate__animated animate__bounceIn">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <h2 class="h3 fw-bold text-warning mb-1">CONGRATULATIONS!</h2>
                <div class="text-light small mb-3">You won on Andar Bahar Round <span class="font-monospace text-warning fw-bold" id="winModalRoundId">-</span>!</div>

                <div class="py-3 px-4 rounded-4 my-3 d-inline-block" style="background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.2);">
                    <div class="text-white-50 small text-uppercase">Total Payout Credited</div>
                    <div class="display-6 fw-bold text-success font-monospace" id="winModalAmount">+₹0.00</div>
                </div>

                <div class="mt-3">
                    <button type="button" class="btn btn-warning rounded-pill px-5 fw-bold" data-bs-dismiss="modal">
                        KEEP PLAYING
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // State Variables
    let currentRoundId = "{{ $roundData['round_id'] }}";
    let remainingSeconds = {{ (int) $roundData['remaining_seconds'] }};
    let isLocked = {{ $roundData['is_locked'] ? 'true' : 'false' }};
    let currentSide = 'andar'; // 'andar', 'bahar', or range tag
    let currentBetType = 'main'; // 'main' or 'cards_range'
    let currentChip = 100;
    let audioEnabled = true;
    let audioCtx = null;
    let lastWonRoundNotified = null;
    let dealingAnimationInProgress = false;

    // Multipliers state (live updated from server)
    let multipliers = {
        andar: {{ (float) ($setting->andar_multiplier ?? 1.95) }},
        bahar: {{ (float) ($setting->bahar_multiplier ?? 1.95) }},
        min_bet: {{ (float) ($setting->andar_bahar_min_bet ?? 10.00) }},
        max_bet: {{ (float) ($setting->andar_bahar_max_bet ?? 50000.00) }},
        range_1_5: {{ (float) ($setting->andar_bahar_range_1_5 ?? 3.50) }},
        range_6_10: {{ (float) ($setting->andar_bahar_range_6_10 ?? 4.00) }},
        range_11_15: {{ (float) ($setting->andar_bahar_range_11_15 ?? 5.00) }},
        range_16_25: {{ (float) ($setting->andar_bahar_range_16_25 ?? 4.50) }},
        range_26_plus: {{ (float) ($setting->andar_bahar_range_26_plus ?? 8.00) }},
    };

    // Web Audio Sound Synthesizer
    function initAudio() {
        if (!audioCtx) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                audioCtx = new AudioContext();
            }
        }
    }

    function playSound(type) {
        if (!audioEnabled) return;
        try {
            initAudio();
            if (!audioCtx) return;
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }

            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);

            const now = audioCtx.currentTime;

            if (type === 'chip') {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(800, now);
                osc.frequency.exponentialRampToValueAtTime(400, now + 0.08);
                gain.gain.setValueAtTime(0.3, now);
                gain.gain.linearRampToValueAtTime(0.01, now + 0.08);
                osc.start(now);
                osc.stop(now + 0.08);
            } else if (type === 'deal') {
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(320, now);
                osc.frequency.exponentialRampToValueAtTime(160, now + 0.06);
                gain.gain.setValueAtTime(0.2, now);
                gain.gain.linearRampToValueAtTime(0.01, now + 0.06);
                osc.start(now);
                osc.stop(now + 0.06);
            } else if (type === 'win') {
                [523.25, 659.25, 783.99, 1046.50].forEach((freq, i) => {
                    const o = audioCtx.createOscillator();
                    const g = audioCtx.createGain();
                    o.type = 'triangle';
                    o.frequency.value = freq;
                    o.connect(g);
                    g.connect(audioCtx.destination);
                    g.gain.setValueAtTime(0.2, now + (i * 0.1));
                    g.gain.exponentialRampToValueAtTime(0.01, now + (i * 0.1) + 0.35);
                    o.start(now + (i * 0.1));
                    o.stop(now + (i * 0.1) + 0.35);
                });
            }
        } catch (e) {
            console.warn('Audio play error:', e);
        }
    }

    function toggleAudio() {
        audioEnabled = !audioEnabled;
        const icon = document.getElementById('soundIcon');
        const btn = document.getElementById('soundToggleBtn');
        if (audioEnabled) {
            icon.className = 'fa-solid fa-volume-high';
            btn.classList.remove('btn-outline-danger');
            btn.classList.add('btn-outline-secondary');
            playSound('chip');
        } else {
            icon.className = 'fa-solid fa-volume-xmark';
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-outline-danger');
        }
    }

    // Chip & Selection Handlers
    function setChipValue(amt) {
        currentChip = amt;
        document.getElementById('customAmountInput').value = amt;
        document.getElementById('currentSelectedChipLabel').innerText = `Selected: ₹${amt}`;

        document.querySelectorAll('.chip-rack-btn').forEach(btn => btn.classList.remove('active'));
        const matchBtn = document.querySelector(`.chip-${amt}`);
        if (matchBtn) matchBtn.classList.add('active');

        playSound('chip');
    }

    function onCustomAmountChange(amt) {
        currentChip = Math.max(1, parseFloat(amt) || 0);
        document.getElementById('currentSelectedChipLabel').innerText = `Selected: ₹${currentChip}`;
        document.querySelectorAll('.chip-rack-btn').forEach(btn => btn.classList.remove('active'));
    }

    function adjustAmount(factor) {
        const input = document.getElementById('customAmountInput');
        let current = parseFloat(input.value) || 100;
        current = Math.max(multipliers.min_bet || 10, Math.round(current * factor));
        input.value = current;
        onCustomAmountChange(current);
        playSound('chip');
    }

    function selectSide(side) {
        currentBetType = 'main';
        currentSide = side;

        document.querySelectorAll('.side-bet-card').forEach(c => c.classList.remove('selected'));
        const andarBox = document.getElementById('betBoxAndar');
        const baharBox = document.getElementById('betBoxBahar');

        if (side === 'andar') {
            andarBox.classList.add('selected');
            baharBox.classList.remove('selected');
            document.getElementById('selectedTargetLabel').innerText = `ANDAR (${multipliers.andar.toFixed(2)}X)`;
            document.getElementById('selectedTargetLabel').className = 'text-danger fw-bold text-uppercase';
        } else {
            baharBox.classList.add('selected');
            andarBox.classList.remove('selected');
            document.getElementById('selectedTargetLabel').innerText = `BAHAR (${multipliers.bahar.toFixed(2)}X)`;
            document.getElementById('selectedTargetLabel').className = 'text-primary fw-bold text-uppercase';
        }

        playSound('chip');
    }

    function selectRange(rangeTag, mult) {
        currentBetType = 'cards_range';
        currentSide = rangeTag;

        document.getElementById('betBoxAndar').classList.remove('selected');
        document.getElementById('betBoxBahar').classList.remove('selected');

        document.querySelectorAll('.side-bet-card').forEach(c => c.classList.remove('selected'));
        const card = document.getElementById(`sideRange_${rangeTag}`);
        if (card) card.classList.add('selected');

        const labelMap = {
            '1_5': '1 – 5 Cards',
            '6_10': '6 – 10 Cards',
            '11_15': '11 – 15 Cards',
            '16_25': '16 – 25 Cards',
            '26_plus': '26+ Cards',
        };

        const currentMult = multipliers['range_' + rangeTag] || mult;
        document.getElementById('selectedTargetLabel').innerText = `${labelMap[rangeTag]} (${currentMult.toFixed(2)}X)`;
        document.getElementById('selectedTargetLabel').className = 'text-warning fw-bold text-uppercase';

        playSound('chip');
    }

    function clearSelection() {
        selectSide('andar');
    }

    // Submit Bet via Realtime API
    async function submitBet() {
        if (isLocked) {
            alert('Betting is locked for this round. Please wait for the next deal.');
            return;
        }

        const amt = parseFloat(document.getElementById('customAmountInput').value) || 0;
        if (amt < multipliers.min_bet) {
            alert(`Minimum bet amount is ₹${multipliers.min_bet}.`);
            return;
        }

        if (amt > multipliers.max_bet) {
            alert(`Maximum bet amount allowed is ₹${multipliers.max_bet}.`);
            return;
        }

        const submitBtn = document.getElementById('placeBetSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> PLACING...';

        try {
            const resp = await fetch("{{ route('api.andar-bahar.place-bet') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    bet_type: currentBetType,
                    bet_selection: currentSide,
                    amount: amt
                })
            });

            const data = await resp.json();

            if (data.success) {
                playSound('chip');
                if (data.new_balance !== undefined) {
                    document.getElementById('headerUserBalance').innerText = `₹${parseFloat(data.new_balance).toFixed(2)}`;
                }

                if (currentSide === 'andar') {
                    const badge = document.getElementById('myAndarBetBadge');
                    const amtSpan = document.getElementById('myAndarBetAmount');
                    badge.style.display = 'inline-block';
                    amtSpan.innerText = (parseFloat(amtSpan.innerText || 0) + amt).toFixed(0);
                } else if (currentSide === 'bahar') {
                    const badge = document.getElementById('myBaharBetBadge');
                    const amtSpan = document.getElementById('myBaharBetAmount');
                    badge.style.display = 'inline-block';
                    amtSpan.innerText = (parseFloat(amtSpan.innerText || 0) + amt).toFixed(0);
                }

                fetchMyBets();
            } else {
                alert(data.message || 'Could not place bet. Please try again.');
            }
        } catch (e) {
            console.error('Bet error:', e);
            alert('Failed to connect to game server. Please check your connection.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-bolt me-1"></i> PLACE BET';
        }
    }

    // Render Playing Card HTML
    function createCardHtml(card, isWinner = false) {
        const isRed = card.color === 'red' || ['♥', '♦'].includes(card.suit || card.symbol);
        const rank = card.rank;
        const sym = card.symbol || card.suit;
        const winClass = isWinner ? 'highlight-winner' : '';

        return `
            <div class="playing-card ${isRed ? 'card-red' : 'card-black'} ${winClass}">
                <div class="card-corner-top">
                    <span>${rank}</span>
                    <span>${sym}</span>
                </div>
                <div class="card-center-suit">${sym}</div>
                <div class="card-corner-bottom">
                    <span>${rank}</span>
                    <span>${sym}</span>
                </div>
            </div>
        `;
    }

    // Play dealing sequence animation
    function playDealingSequence(dealtCards, winningSide, winningCard) {
        if (dealingAnimationInProgress) return;
        dealingAnimationInProgress = true;

        const andarRail = document.getElementById('andarCardsRail');
        const baharRail = document.getElementById('baharCardsRail');

        andarRail.innerHTML = '';
        baharRail.innerHTML = '';

        let andarCount = 0;
        let baharCount = 0;

        dealtCards.forEach((item, index) => {
            setTimeout(() => {
                const isFinal = (index === dealtCards.length - 1);
                const cardHtml = createCardHtml(item.card, isFinal);

                if (item.side === 'andar') {
                    andarRail.insertAdjacentHTML('beforeend', cardHtml);
                    andarCount++;
                    document.getElementById('andarCardCount').innerText = andarCount;
                    andarRail.scrollLeft = andarRail.scrollWidth;
                } else {
                    baharRail.insertAdjacentHTML('beforeend', cardHtml);
                    baharCount++;
                    document.getElementById('baharCardCount').innerText = baharCount;
                    baharRail.scrollLeft = baharRail.scrollWidth;
                }

                playSound('deal');

                if (isFinal) {
                    dealingAnimationInProgress = false;
                    const banner = document.getElementById('dealingBannerText');
                    banner.innerText = `ROUND OVER • ${winningSide.toUpperCase()} WON!`;

                    // Auto refresh history and my bets
                    setTimeout(() => {
                        fetchHistory();
                        fetchMyBets();
                    }, 1000);
                }
            }, index * 260);
        });
    }

    // Realtime Polling Loop
    async function syncGameStatus() {
        try {
            const resp = await fetch("{{ route('api.andar-bahar.status') }}");
            const data = await resp.json();

            if (!data.success) return;

            // Update Multipliers from Server (Admin settings)
            if (data.multipliers) {
                multipliers = data.multipliers;

                document.getElementById('headerAndarMultiplier').innerText = `${multipliers.andar.toFixed(2)}x`;
                document.getElementById('headerBaharMultiplier').innerText = `${multipliers.bahar.toFixed(2)}x`;
                document.getElementById('andarMultiplierBadge').innerText = `${multipliers.andar.toFixed(2)}X`;
                document.getElementById('baharMultiplierBadge').innerText = `${multipliers.bahar.toFixed(2)}X`;

                // Update side bets badges
                if (document.getElementById('sideBadge_1_5')) document.getElementById('sideBadge_1_5').innerText = `${multipliers.range_1_5.toFixed(2)}X`;
                if (document.getElementById('sideBadge_6_10')) document.getElementById('sideBadge_6_10').innerText = `${multipliers.range_6_10.toFixed(2)}X`;
                if (document.getElementById('sideBadge_11_15')) document.getElementById('sideBadge_11_15').innerText = `${multipliers.range_11_15.toFixed(2)}X`;
                if (document.getElementById('sideBadge_16_25')) document.getElementById('sideBadge_16_25').innerText = `${multipliers.range_16_25.toFixed(2)}X`;
                if (document.getElementById('sideBadge_26_plus')) document.getElementById('sideBadge_26_plus').innerText = `${multipliers.range_26_plus.toFixed(2)}X`;

                // Target label refresh
                if (currentBetType === 'main') {
                    if (currentSide === 'andar') {
                        document.getElementById('selectedTargetLabel').innerText = `ANDAR (${multipliers.andar.toFixed(2)}X)`;
                    } else {
                        document.getElementById('selectedTargetLabel').innerText = `BAHAR (${multipliers.bahar.toFixed(2)}X)`;
                    }
                }
            }

            // Handle Round Change
            if (data.round_id !== currentRoundId) {
                currentRoundId = data.round_id;
                document.getElementById('liveRoundId').innerText = currentRoundId;

                // Reset cards rails
                document.getElementById('andarCardsRail').innerHTML = '<div class="text-white-50 small w-100 text-center py-3" id="andarEmptyMsg">Cards deal starts after betting closes</div>';
                document.getElementById('baharCardsRail').innerHTML = '<div class="text-white-50 small w-100 text-center py-3" id="baharEmptyMsg">Cards deal starts after betting closes</div>';
                document.getElementById('andarCardCount').innerText = '0';
                document.getElementById('baharCardCount').innerText = '0';

                // Reset my bet badges
                document.getElementById('myAndarBetBadge').style.display = 'none';
                document.getElementById('myAndarBetAmount').innerText = '0';
                document.getElementById('myBaharBetBadge').style.display = 'none';
                document.getElementById('myBaharBetAmount').innerText = '0';

                dealingAnimationInProgress = false;

                // Fetch new round history
                fetchHistory();
                fetchMyBets();
            }

            remainingSeconds = data.remaining_seconds;
            isLocked = data.is_locked;

            // Update countdown UI
            const countElem = document.getElementById('liveCountdown');
            countElem.innerText = `${String(remainingSeconds).padStart(2, '0')}s`;

            const banner = document.getElementById('dealingBanner');
            const submitBtn = document.getElementById('placeBetSubmitBtn');

            if (isLocked) {
                banner.style.display = 'flex';
                countElem.className = 'fs-3 fw-bold font-monospace text-danger animate__animated animate__pulse animate__infinite';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.replace('btn-warning', 'btn-secondary');
                    submitBtn.innerText = 'BETS LOCKED';
                }

                if (data.dealt_cards && data.dealt_cards.length > 0 && !dealingAnimationInProgress) {
                    const currentRendered = document.querySelectorAll('.cards-rail-wrap .playing-card').length;
                    if (currentRendered < data.dealt_cards.length) {
                        playDealingSequence(data.dealt_cards, data.winning_side, data.winning_card);
                    }
                }
            } else {
                banner.style.display = 'none';
                countElem.className = 'fs-3 fw-bold font-monospace text-warning';
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.replace('btn-secondary', 'btn-warning');
                    submitBtn.innerHTML = '<i class="fa-solid fa-bolt me-1"></i> PLACE BET';
                }
            }

            // Update Joker Card
            if (data.joker_card) {
                const j = data.joker_card;
                const isRed = j.color === 'red' || ['♥', '♦'].includes(j.suit || j.symbol);
                const el = document.getElementById('jokerCardElement');
                el.className = `playing-card joker-card-lg ${isRed ? 'card-red' : 'card-black'}`;
                const sym = j.symbol || j.suit;
                document.getElementById('jokerRankTop').innerText = j.rank;
                document.getElementById('jokerSuitTop').innerText = sym;
                document.getElementById('jokerSuitCenter').innerText = sym;
                document.getElementById('jokerRankBottom').innerText = j.rank;
                document.getElementById('jokerSuitBottom').innerText = sym;
                document.getElementById('jokerTargetRank').innerText = j.rank;
            }

            // Update Multipliers dynamically if changed by admin
            if (data.multipliers) {
                multipliers = data.multipliers;
                if (document.getElementById('andarMultiplierBadge')) {
                    document.getElementById('andarMultiplierBadge').innerText = `${parseFloat(data.multipliers.andar).toFixed(2)}X`;
                }
                if (document.getElementById('baharMultiplierBadge')) {
                    document.getElementById('baharMultiplierBadge').innerText = `${parseFloat(data.multipliers.bahar).toFixed(2)}X`;
                }
                if (document.getElementById('sideBadge_1_5')) {
                    document.getElementById('sideBadge_1_5').innerText = `${parseFloat(data.multipliers.range_1_5).toFixed(2)}X`;
                }
                if (document.getElementById('sideBadge_6_10')) {
                    document.getElementById('sideBadge_6_10').innerText = `${parseFloat(data.multipliers.range_6_10).toFixed(2)}X`;
                }
                if (document.getElementById('sideBadge_11_15')) {
                    document.getElementById('sideBadge_11_15').innerText = `${parseFloat(data.multipliers.range_11_15).toFixed(2)}X`;
                }
                if (document.getElementById('sideBadge_16_25')) {
                    document.getElementById('sideBadge_16_25').innerText = `${parseFloat(data.multipliers.range_16_25).toFixed(2)}X`;
                }
                if (document.getElementById('sideBadge_26_plus')) {
                    document.getElementById('sideBadge_26_plus').innerText = `${parseFloat(data.multipliers.range_26_plus).toFixed(2)}X`;
                }
            }

            // Update Balance
            if (data.balance !== undefined) {
                document.getElementById('headerUserBalance').innerText = `₹${parseFloat(data.balance).toFixed(2)}`;
            }

            // Check Win Notification
            if (data.latest_win && data.latest_win.round_id !== lastWonRoundNotified) {
                lastWonRoundNotified = data.latest_win.round_id;
                document.getElementById('winModalRoundId').innerText = data.latest_win.round_id;
                document.getElementById('winModalAmount').innerText = `+₹${parseFloat(data.latest_win.win_amount).toFixed(2)}`;

                const winModal = new bootstrap.Modal(document.getElementById('winCelebrationModal'));
                winModal.show();
                playSound('win');
                fetchMyBets();
            }

            // Update Bead Road
            if (data.recent_history && data.recent_history.length > 0) {
                renderBeadRoad(data.recent_history);
            }

        } catch (e) {
            console.error('Status sync error:', e);
        }
    }

    // Render Bead Road & Win Percentage Bar
    function renderBeadRoad(history) {
        const container = document.getElementById('beadRoadContainer');
        if (!container) return;

        let andarCount = 0;
        let baharCount = 0;

        let html = '';
        history.forEach(rh => {
            const isAndar = rh.winning_side === 'andar';
            if (isAndar) andarCount++; else baharCount++;

            let roundNum = rh.round_id || '';
            if (roundNum && roundNum.length >= 5) {
                const lastDigits = roundNum.slice(-5).replace(/^0+/, '');
                roundNum = lastDigits || roundNum.slice(-4);
            }

            html += `
                <div class="road-pill ${isAndar ? 'road-pill-andar' : 'road-pill-bahar'}" title="Round #${rh.round_id} - Won: ${rh.winning_side.toUpperCase()} in ${rh.total_cards || 0} cards">
                    <span class="road-circle ${isAndar ? 'road-circle-andar' : 'road-circle-bahar'}">${isAndar ? 'A' : 'B'}</span>
                    <span class="road-round-num">#${roundNum}</span>
                </div>
            `;
        });

        container.innerHTML = html;

        const total = Math.max(1, andarCount + baharCount);
        const andarP = Math.round((andarCount / total) * 100);
        const baharP = 100 - andarP;

        document.getElementById('andarPercentVal').innerText = `${andarP}%`;
        document.getElementById('baharPercentVal').innerText = `${baharP}%`;

        const andarBar = document.getElementById('andarProgressBar');
        const baharBar = document.getElementById('baharProgressBar');
        if (andarBar) andarBar.style.width = `${andarP}%`;
        if (baharBar) baharBar.style.width = `${baharP}%`;
    }

    // Fetch Full Rounds History
    async function fetchHistory() {
        try {
            const resp = await fetch("{{ route('api.andar-bahar.history') }}");
            const data = await resp.json();
            const tbody = document.getElementById('historyTableBody');

            if (!data.success || !data.rounds || data.rounds.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-white-50">No completed rounds recorded yet.</td></tr>';
                return;
            }

            let html = '';
            data.rounds.forEach(r => {
                const jk = r.joker_card || {};
                const isRedJk = (jk.color === 'red') || ['♥', '♦'].includes(jk.suit || jk.symbol);
                const symJk = jk.symbol || jk.suit || '';

                const wc = r.winning_card || {};
                const isRedWc = (wc.color === 'red') || ['♥', '♦'].includes(wc.suit || wc.symbol);
                const symWc = wc.symbol || wc.suit || '';

                const sideBadge = (r.winning_side === 'andar') 
                    ? '<span class="badge bg-danger text-uppercase px-2 py-1">ANDAR</span>' 
                    : '<span class="badge bg-primary text-uppercase px-2 py-1">BAHAR</span>';

                const timeStr = r.created_at ? new Date(r.created_at).toLocaleTimeString() : '-';

                html += `
                    <tr>
                        <td class="ps-3 font-monospace text-warning fw-bold">#${r.round_id}</td>
                        <td>
                            <span class="mini-card ${isRedJk ? 'mini-red' : 'mini-black'}">
                                ${jk.rank || '?'}${symJk}
                            </span>
                        </td>
                        <td>${sideBadge}</td>
                        <td>
                            ${wc.rank ? `
                                <span class="mini-card ${isRedWc ? 'mini-red' : 'mini-black'}">
                                    ${wc.rank}${symWc}
                                </span>
                            ` : '<span class="text-white-50">-</span>'}
                        </td>
                        <td class="font-monospace text-white-50">${r.total_cards_dealt || 0} cards</td>
                        <td class="pe-3 text-end text-white-50 small font-monospace">${timeStr}</td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        } catch (e) {
            console.error('Fetch history error:', e);
        }
    }

    // Fetch My Bets & Quick Summary Statistics
    async function fetchMyBets() {
        try {
            const resp = await fetch("{{ route('api.andar-bahar.my-bets') }}");
            if (resp.status === 401) return; // Guest user

            const data = await resp.json();
            const tbody = document.getElementById('myBetsTableBody');

            // Update Player Quick Stats
            if (data.stats) {
                document.getElementById('statTotalBets').innerText = data.stats.total_bets;
                document.getElementById('statTotalWagered').innerText = `₹${data.stats.total_wagered.toFixed(2)}`;
                document.getElementById('statTotalWon').innerText = `₹${data.stats.total_won.toFixed(2)}`;

                const netProfitEl = document.getElementById('statNetProfit');
                const net = data.stats.net_profit;
                netProfitEl.innerText = `${net >= 0 ? '+' : ''}₹${net.toFixed(2)}`;
                netProfitEl.className = `fs-5 fw-bold font-monospace ${net >= 0 ? 'text-success' : 'text-danger'}`;

                const badge = document.getElementById('myBetsCountBadge');
                if (badge) badge.innerText = data.stats.total_bets;
            }

            if (!data.success || !data.bets || data.bets.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-4 text-white-50">
                            No bets placed yet. Place your bet on Andar or Bahar to win!
                        </td>
                    </tr>
                `;
                return;
            }

            let html = '';
            data.bets.forEach(b => {
                let statusBadge = '<span class="badge bg-warning text-dark px-2 py-1 rounded-pill">Pending</span>';
                let winAmt = '<span class="text-white-50 font-monospace">-</span>';

                if (b.status === 'won') {
                    statusBadge = '<span class="badge bg-success px-2 py-1 rounded-pill"><i class="fa-solid fa-check me-1"></i> Won</span>';
                    const comm = parseFloat(b.commission_amount || 0);
                    const feeText = comm > 0 ? ` <small class="text-white-50 font-monospace" style="font-size:0.7rem;">(-₹${comm.toFixed(2)})</small>` : '';
                    winAmt = `<span class="text-success fw-bold font-monospace">+₹${parseFloat(b.win_amount).toFixed(2)}</span>${feeText}`;
                } else if (b.status === 'lost') {
                    statusBadge = '<span class="badge bg-danger px-2 py-1 rounded-pill">Lost</span>';
                    winAmt = '<span class="text-white-50 font-monospace">₹0.00</span>';
                }

                let selBadge = `<span class="badge bg-dark px-2 py-1 rounded-2">${b.bet_selection.toUpperCase()}</span>`;
                if (b.bet_selection === 'andar') {
                    selBadge = '<span class="badge bg-danger px-2 py-1 rounded-2">ANDAR</span>';
                } else if (b.bet_selection === 'bahar') {
                    selBadge = '<span class="badge bg-primary px-2 py-1 rounded-2">BAHAR</span>';
                } else {
                    selBadge = `<span class="badge bg-warning text-dark px-2 py-1 rounded-2">${b.bet_selection.replace('_', '-')} Cards</span>`;
                }

                const mult = parseFloat(b.multiplier || b.win_multiplier || 1.90).toFixed(2);
                const timeStr = b.created_at ? new Date(b.created_at).toLocaleTimeString() : '-';

                html += `
                    <tr>
                        <td class="ps-3 font-monospace text-warning fw-bold">#${b.round_id}</td>
                        <td>${selBadge}</td>
                        <td class="font-monospace fw-bold text-white">₹${parseFloat(b.amount).toFixed(2)}</td>
                        <td class="text-warning font-monospace fw-bold">${mult}x</td>
                        <td>${winAmt}</td>
                        <td>${statusBadge}</td>
                        <td class="pe-3 text-end text-white-50 small font-monospace">${timeStr}</td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        } catch (e) {
            console.error('Fetch my bets error:', e);
        }
    }

    // Client countdown decrementer
    setInterval(() => {
        if (remainingSeconds > 0) {
            remainingSeconds--;
            document.getElementById('liveCountdown').innerText = `${String(remainingSeconds).padStart(2, '0')}s`;
            if (remainingSeconds <= 10 && !isLocked) {
                isLocked = true;
                syncGameStatus();
            }
        }
    }, 1000);

    // Initializations
    document.addEventListener('DOMContentLoaded', () => {
        syncGameStatus();
        fetchMyBets();
        setInterval(syncGameStatus, 2000); // Poll status every 2 seconds
    });
</script>
@endsection
