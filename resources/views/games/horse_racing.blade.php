@extends('layouts.app')

@section('title', 'Live Horse Racing Derby - ' . ($setting->site_name ?? 'rupeerush'))

@section('styles')
<style>
    /* ----------------------------------------------------
       CHAMPION HORSE RACING DERBY SUITE
       Emerald Turf, Mahogany Wood Rails, Gold Accents
       ---------------------------------------------------- */
    :root {
        --hr-bg: #050d08;
        --hr-card-bg: rgba(9, 23, 15, 0.92);
        --hr-turf-dark: #072213;
        --hr-turf-mid: #0d381f;
        --hr-turf-light: #155e34;
        --hr-gold: #f59e0b;
        --hr-gold-glow: rgba(245, 158, 11, 0.45);
        --hr-border: rgba(245, 158, 11, 0.22);
    }

    html {
        background-color: #050d08;
        scroll-behavior: smooth;
    }

    body {
        background-color: #050d08 !important;
        background: radial-gradient(circle at 50% 0%, #0d2b1a 0%, #050d08 100%) fixed !important;
        background-attachment: fixed !important;
        color: #f8fafc;
        font-family: 'Plus Jakarta Sans', sans-serif;
        display: block !important;
        min-height: 100vh;
        overflow-x: clip !important;
        margin: 0;
        padding: 0;
    }

    /* Seamless Sticky Header on Derby Turf */
    .site-header {
        position: sticky !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 1020 !important;
        background: rgba(5, 13, 8, 0.94) !important;
        backdrop-filter: blur(14px) !important;
        border-bottom: 1px solid var(--hr-border) !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.6) !important;
    }
    .site-header .brand-title .brand-vijay { color: #38bdf8 !important; }
    .site-header .brand-title .brand-bhagya { color: #f59e0b !important; }
    .site-header .brand-tagline { color: rgba(255, 255, 255, 0.45) !important; }
    .site-header .nav-link-custom { color: #f1f5f9 !important; }
    .site-header .nav-link-custom:hover { color: #f59e0b !important; }
    .site-header .btn-login-nav {
        color: #f8fafc !important;
        border-color: rgba(255, 255, 255, 0.25) !important;
    }
    .site-header .btn-login-nav:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
    }
    .site-header .user-dropdown-btn {
        background: rgba(255, 255, 255, 0.08) !important;
        border-color: rgba(255, 255, 255, 0.15) !important;
        color: #f8fafc !important;
    }

    .derby-container {
        max-width: 1360px;
        margin: 0 auto;
    }

    /* Glass Panels */
    .derby-card {
        background: var(--hr-card-bg);
        border: 1px solid var(--hr-border);
        border-radius: 20px;
        backdrop-filter: blur(14px);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.6);
    }

    /* Track Viewport Frame */
    .track-viewport-card {
        position: relative;
        background: #06150c;
        border: 3px solid #3b220e;
        outline: 1.5px solid var(--hr-gold);
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.8), inset 0 0 25px rgba(0, 0, 0, 0.8);
        overflow: hidden;
    }

    #raceCanvas {
        display: block;
        width: 100%;
        height: auto;
        border-radius: 16px;
    }

    /* Live Marquee Ticker */
    .marquee-wrapper {
        background: rgba(0, 0, 0, 0.45);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 50px;
        overflow: hidden;
        white-space: nowrap;
    }
    .marquee-inner {
        display: inline-flex;
        animation: marqueeScroll 25s linear infinite;
    }
    @keyframes marqueeScroll {
        from { transform: translateX(0%); }
        to { transform: translateX(-50%); }
    }

    /* Runner Betting Cards */
    .runner-card {
        background: linear-gradient(145deg, rgba(16, 37, 26, 0.85) 0%, rgba(9, 21, 15, 0.95) 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        padding: 8px 10px;
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        position: relative;
    }
    .runner-card:hover {
        transform: translateY(-3px);
        border-color: var(--hr-gold);
        box-shadow: 0 8px 24px rgba(245, 158, 11, 0.25);
    }
    .runner-card.selected-runner {
        border-color: var(--hr-gold);
        box-shadow: 0 0 20px var(--hr-gold-glow);
    }

    .silk-pill {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 1rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.5);
    }

    /* Bet Buttons */
    .bet-spot-btn {
        background: rgba(13, 27, 20, 0.7);
        border: 1.5px solid rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        padding: 8px 4px;
        text-align: center;
        cursor: pointer;
        transition: all 0.15s ease;
        position: relative;
        flex: 1;
        min-width: 0;
    }
    .bet-spot-btn:hover {
        transform: scale(1.04);
        border-color: var(--hr-gold);
        box-shadow: 0 0 12px var(--hr-gold-glow);
    }
    .bet-spot-btn.has-bet {
        border-color: #f59e0b;
        background: rgba(245, 158, 11, 0.18);
        box-shadow: 0 0 15px rgba(245, 158, 11, 0.4);
    }

    /* Chip Floating Badge on Spot */
    .chip-badge {
        position: absolute;
        width: 26px;
        height: 26px;
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
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.8);
        top: -10px;
        right: -6px;
        pointer-events: none;
        z-index: 10;
        animation: chipBounce 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    @keyframes chipBounce {
        from { transform: scale(0.3); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    /* Casino Chips Selector */
    .chip-tray {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .casino-chip {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 900;
        font-size: 0.78rem;
        cursor: pointer;
        position: relative;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.7);
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        border: 3px dashed rgba(255, 255, 255, 0.6);
        user-select: none;
    }
    .casino-chip:hover { transform: translateY(-5px) scale(1.1); }
    .casino-chip.active {
        transform: translateY(-8px) scale(1.15);
        box-shadow: 0 10px 25px rgba(245, 158, 11, 0.8), 0 0 0 3px #f59e0b;
    }

    .chip-10 { background: radial-gradient(circle, #38bdf8 0%, #0284c7 100%); color: #fff; }
    .chip-50 { background: radial-gradient(circle, #fb923c 0%, #c2410c 100%); color: #fff; }
    .chip-100 { background: radial-gradient(circle, #f87171 0%, #b91c1c 100%); color: #fff; }
    .chip-500 { background: radial-gradient(circle, #c084fc 0%, #7e22ce 100%); color: #fff; }
    .chip-1000 { background: radial-gradient(circle, #fde047 0%, #ca8a04 100%); color: #000; }
    .chip-5000 { background: radial-gradient(circle, #334155 0%, #0f172a 100%); color: #f8fafc; border-color: #f59e0b; }

    /* Winner Podium Overlay */
    .podium-modal-overlay {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(3, 10, 6, 0.92);
        z-index: 50;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(8px);
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .podium-box {
        background: linear-gradient(180deg, #122f1f 0%, #08170e 100%);
        border: 2px solid var(--hr-gold);
        border-radius: 24px;
        padding: 28px;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,0.9), 0 0 40px var(--hr-gold-glow);
        max-width: 520px;
        width: 90%;
        animation: zoomPodium 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    @keyframes zoomPodium {
        from { transform: scale(0.6); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    /* Tabs Styling */
    .derby-tab-btn {
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.6);
        font-weight: 700;
        padding: 10px 18px;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }
    .derby-tab-btn.active {
        color: #f59e0b;
        border-bottom-color: #f59e0b;
    }

    @media (max-width: 768px) {
        /* ── MOBILE RESPONSIVE (≤ 768px) ── */
        .derby-container { padding: 4px 2px 130px !important; }
        .derby-card { padding: 8px 10px !important; margin-bottom: 6px !important; border-radius: 12px !important; }
        .marquee-wrapper { margin-bottom: 6px !important; padding: 4px !important; }
        .track-viewport-card { margin-bottom: 6px !important; border-radius: 12px !important; }

        /* Top Header — stack brand + right into column on xs */
        .derby-top-header {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 8px !important;
        }
        .derby-top-header .header-brand h1 { font-size: 1rem !important; }
        .derby-top-header .header-brand small { font-size: 0.68rem !important; }
        .derby-top-header .header-brand .brand-icon {
            width: 36px !important; height: 36px !important; font-size: 1.1rem !important;
        }
        .derby-top-header .header-right {
            width: 100% !important;
            gap: 6px !important;
            justify-content: space-between !important;
        }
        .derby-top-header .header-right .wallet-info {
            border-left: none !important;
            padding-left: 0 !important;
            border-top: 1px solid rgba(255,255,255,0.1) !important;
            padding-top: 4px !important;
        }
        #roundIdBadge { font-size: 0.65rem !important; padding: 2px 5px !important; }
        #roundTimerBadge { font-size: 0.8rem !important; padding: 3px 8px !important; }

        /* Runner cards */
        .runner-card { padding: 8px !important; margin-bottom: 6px !important; border-radius: 12px !important; }
        .silk-pill { width: 26px !important; height: 26px !important; font-size: 0.85rem !important; }
        .bet-spot-btn { padding: 4px 2px !important; font-size: 0.72rem !important; }

        /* Sticky footer above mobile bottom nav */
        .mobile-sticky-footer {
            position: fixed !important;
            bottom: calc(64px + env(safe-area-inset-bottom, 0px)) !important;
            left: 0 !important;
            right: 0 !important;
            background: rgba(5, 13, 8, 0.98) !important;
            border-top: 1px solid var(--hr-border) !important;
            padding: 6px 10px 8px !important;
            z-index: 1040 !important;
            box-shadow: 0 -8px 25px rgba(0, 0, 0, 0.85) !important;
            border-radius: 0 !important;
            backdrop-filter: blur(12px) !important;
        }
        .mobile-sticky-footer .chip-tray {
            gap: 6px !important;
            margin-bottom: 5px !important;
            justify-content: center !important;
            flex-wrap: nowrap !important;
        }
        .mobile-sticky-footer .chip-tray .casino-chip {
            width: 36px !important;
            height: 36px !important;
            font-size: 0.68rem !important;
            border-width: 2px !important;
            flex-shrink: 0 !important;
        }

        .derby-container { padding-bottom: 240px !important; }
        body { padding-bottom: 240px !important; }
    }
</style>
@endsection

@section('content')
<div class="derby-container px-2 px-md-3 py-3">

    <!-- Top Bar: Game Title, Live Round Sync, Audio Toggle & Wallet Balance -->
    <div class="derby-card p-3 mb-3 d-flex align-items-center justify-content-between gap-2 shadow-lg derby-top-header">
        <div class="d-flex align-items-center gap-3 header-brand">
            <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 fs-4 brand-icon" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                🏇
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h1 class="h4 mb-0 fw-black text-warning">CHAMPION DERBY LIVE</h1>
                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 small d-none d-sm-inline">1000m Turf</span>
                </div>
                <small class="text-white-50">Synchronized Rapid Derby • Provably Fair</small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 header-right">
            <!-- Audio Sound Toggle -->
            <button class="btn btn-dark border border-secondary rounded-circle p-1" id="soundToggleBtn" onclick="toggleSound()" title="Toggle Sound" style="width:34px;height:34px;flex-shrink:0;">
                <i class="fa-solid fa-volume-high text-warning" id="soundIcon"></i>
            </button>

            <!-- Active Round & Timer -->
            <div class="text-end round-info">
                <span class="text-white-50 d-block" style="font-size:0.6rem;">ROUND</span>
                <span class="badge bg-dark border border-warning text-warning fw-bold font-monospace" id="roundIdBadge" style="font-size:0.72rem;">
                    {{ $roundData['round_id'] }}
                </span>
            </div>

            <div class="text-end round-info">
                <span class="text-white-50 d-block" style="font-size:0.6rem;">TIME</span>
                <span class="badge bg-danger fw-bold px-2 py-1 font-monospace shadow" id="roundTimerBadge" style="font-size:0.85rem;">
                    {{ $roundData['remaining_seconds'] }}s
                </span>
            </div>

            <!-- User Balance -->
            <div class="text-end ps-2 border-start border-secondary wallet-info">
                <span class="text-white-50 d-block" style="font-size:0.6rem;">BALANCE</span>
                <div class="d-flex align-items-center gap-1">
                    <span class="fw-black text-warning font-monospace" id="userWalletBalance" style="font-size:0.9rem;white-space:nowrap;">
                        ₹{{ number_format((float)($user?->balance ?? 0), 2) }}
                    </span>
                    <a href="{{ route('wallet.deposit') }}" class="btn btn-warning btn-sm rounded-pill px-2 py-0 fw-bold d-none d-sm-inline" style="font-size: 0.7rem;">+ADD</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Recent Winners Marquee Ticker -->
    <div class="marquee-wrapper p-2 mb-3">
        <div class="marquee-inner">
            @foreach($recentHistory->take(10) as $rh)
                <div class="d-inline-flex align-items-center gap-2 mx-3 font-monospace small">
                    <span class="text-white-50">#{{ $rh->round_id }}:</span>
                    <span class="badge bg-warning text-dark fw-bold">1st: Runner #{{ $rh->winner_horse_id }}</span>
                    @if($rh->second_horse_id)
                        <span class="badge bg-secondary text-white">2nd: #{{ $rh->second_horse_id }}</span>
                    @endif
                    @if($rh->third_horse_id)
                        <span class="badge bg-dark text-white-50 border">3rd: #{{ $rh->third_horse_id }}</span>
                    @endif
                    <span class="text-white-50 ms-1">•</span>
                </div>
            @endforeach
            @foreach($recentHistory->take(10) as $rh)
                <div class="d-inline-flex align-items-center gap-2 mx-3 font-monospace small">
                    <span class="text-white-50">#{{ $rh->round_id }}:</span>
                    <span class="badge bg-warning text-dark fw-bold">1st: Runner #{{ $rh->winner_horse_id }}</span>
                    @if($rh->second_horse_id)
                        <span class="badge bg-secondary text-white">2nd: #{{ $rh->second_horse_id }}</span>
                    @endif
                    @if($rh->third_horse_id)
                        <span class="badge bg-dark text-white-50 border">3rd: #{{ $rh->third_horse_id }}</span>
                    @endif
                    <span class="text-white-50 ms-1">•</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Royal Derby Hero Championship Header (Hidden to preserve viewport) -->
    <div class="rounded-4 overflow-hidden mb-2 shadow-lg position-relative d-none" style="max-height: 125px; border: 1px solid rgba(245, 158, 11, 0.35);">
        <img src="{{ asset('images/games/derby_hero.jpg') }}" alt="Royal Derby" style="width: 100%; height: 125px; object-fit: cover; object-position: center 30%; filter: brightness(0.85);">
        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-between px-3 px-md-4" style="background: linear-gradient(90deg, rgba(3,10,6,0.92) 0%, rgba(3,10,6,0.3) 60%, rgba(3,10,6,0.85) 100%);">
            <div>
                <span class="badge bg-warning text-dark fw-black px-2 py-1 mb-1 font-monospace"><i class="fa-solid fa-trophy me-1"></i>ROYAL DERBY LIVE</span>
                <h4 class="fw-black text-white mb-0 text-uppercase tracking-wider">CHAMPION THOROUGHBRED DERBY</h4>
                <small class="text-white-50">6 Elite Thoroughbreds • Provably Fair Multipliers • 1000m Sprint</small>
            </div>
            <div class="d-none d-md-block text-end font-monospace">
                <span class="badge bg-black bg-opacity-70 border border-warning text-warning fs-6 px-3 py-2"><i class="fa-solid fa-signal me-1"></i>LIVE TRACK</span>
            </div>
        </div>
    </div>

    <!-- Live Racetrack Viewport Canvas & Podium Modal -->
    <div class="track-viewport-card mb-3 shadow-2xl">
        <canvas id="raceCanvas" width="1200" height="420"></canvas>

        <!-- Podium Finish Reveal Overlay Modal -->
        <div class="podium-modal-overlay" id="podiumModal">
            <div class="podium-box">
                <div class="fs-1 mb-2">🏆</div>
                <h3 class="fw-black text-warning mb-1">RACE FINISHED!</h3>
                <div class="text-white-50 small mb-3" id="podiumRoundId">Round #HR26102900000</div>

                <div class="d-flex justify-content-center align-items-end gap-3 mb-4">
                    <!-- 2nd Place -->
                    <div class="text-center" style="width: 90px;">
                        <div class="p-2 rounded-top bg-secondary text-white fw-bold mb-1" style="height: 60px; display: flex; flex-direction: column; justify-content: center;">
                            <div class="small text-uppercase">2nd Place</div>
                            <div class="fs-5 fw-black" id="podiumSecond">#2</div>
                        </div>
                        <div class="small text-truncate text-white-50" id="podiumSecondName">Runner</div>
                    </div>

                    <!-- 1st Place Winner -->
                    <div class="text-center" style="width: 120px;">
                        <div class="p-2 rounded-top bg-warning text-dark fw-bold mb-1 shadow-lg" style="height: 85px; display: flex; flex-direction: column; justify-content: center;">
                            <div class="small fw-black text-uppercase">👑 WINNER</div>
                            <div class="fs-3 fw-black" id="podiumWinner">#1</div>
                        </div>
                        <div class="fw-bold text-warning text-truncate" id="podiumWinnerName">Winner</div>
                    </div>

                    <!-- 3rd Place -->
                    <div class="text-center" style="width: 90px;">
                        <div class="p-2 rounded-top bg-dark text-white-50 border border-secondary fw-bold mb-1" style="height: 45px; display: flex; flex-direction: column; justify-content: center;">
                            <div class="small text-uppercase" style="font-size: 0.7rem;">3rd Place</div>
                            <div class="fs-6 fw-black text-white" id="podiumThird">#3</div>
                        </div>
                        <div class="small text-truncate text-white-50" id="podiumThirdName">Runner</div>
                    </div>
                </div>

                <div id="podiumUserWinnings" class="p-2 rounded-3 bg-success bg-opacity-20 text-success fw-bold mb-3 d-none">
                    🎉 Congratulations! You won ₹0.00!
                </div>

                <button class="btn btn-warning text-dark fw-bold px-4 rounded-pill" onclick="closePodiumModal()">
                    NEXT RACE NOW (<span id="podiumCountdown">5</span>s)
                </button>
            </div>
        </div>
    </div>

    <!-- 6 Runners Betting Board -->
    <div class="derby-card p-3 mb-3 shadow-lg">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-warning mb-0">
                    <i class="fa-solid fa-flag-checkered me-2"></i> SELECT RUNNER & BET TYPE
                </h5>
                <small class="text-white-50">Win: 1st Place • Place: Top 2 • Show: Top 3 Finishers</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-dark border border-secondary text-white-50">Min: ₹{{ number_format((float)($setting->horse_racing_min_bet ?? 10), 2) }}</span>
                <span class="badge bg-dark border border-secondary text-white-50">Max: ₹{{ number_format((float)($setting->horse_racing_max_bet ?? 25000), 2) }}</span>
            </div>
        </div>

        <div class="row g-3 mb-2" id="runnersContainer">
            @foreach($horses as $horse)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="runner-card h-100" id="runnerCard_{{ $horse['id'] }}" style="border-top: 3px solid {{ $horse['silk_color'] }};">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="silk-pill text-white shadow-sm" style="background-color: {{ $horse['silk_color'] }}; border: 1.5px solid rgba(255,255,255,0.4);">
                                {{ $horse['gate'] }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-white">{{ $horse['name'] }}</h6>
                                <small class="text-white-50"><i class="fa-solid fa-user-ninja me-1" style="color: {{ $horse['silk_color'] }};"></i>{{ $horse['jockey'] }} • Gate {{ $horse['gate'] }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-black bg-opacity-60 text-warning border border-warning border-opacity-25 font-monospace small">⭐ {{ $horse['rating'] }}</span>
                            <div class="text-white-50 small mt-1 font-monospace" style="font-size: 0.68rem;">{{ $horse['weight'] ?? '54kg' }}</div>
                        </div>
                    </div>

                    <!-- Runner Breed & Form Badge Line -->
                    <div class="d-flex align-items-center justify-content-between gap-1 mb-2 pt-1 pb-1 border-top border-bottom border-white border-opacity-10" style="font-size: 0.72rem;">
                        <div class="d-flex align-items-center gap-1 text-white-50">
                            <span class="d-inline-block rounded-circle" style="width: 8px; height: 8px; background-color: {{ $horse['coat_color'] ?? '#78350f' }}; border: 1px solid rgba(255,255,255,0.5);"></span>
                            <span>{{ $horse['coat_name'] ?? 'Thoroughbred' }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1 font-monospace text-white-50">
                            <span>FORM:</span>
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-1 py-0">{{ $horse['form'] ?? '1-2-1' }}</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between gap-2 mt-2">
                        <!-- WIN SPOT -->
                        <div class="bet-spot-btn" id="spot_{{ $horse['id'] }}_win" onclick="handleSpotClick({{ $horse['id'] }}, 'win', '{{ $horse['name'] }}', {{ $horse['win_odds'] }})">
                            <div class="text-uppercase small fw-bold text-warning" style="font-size: 0.72rem;">WIN</div>
                            <div class="fw-bold text-warning font-monospace fs-6">{{ $horse['win_odds'] }}x</div>
                            <div class="chip-badge d-none" id="chip_{{ $horse['id'] }}_win">₹0</div>
                        </div>

                        <!-- PLACE SPOT -->
                        <div class="bet-spot-btn" id="spot_{{ $horse['id'] }}_place" onclick="handleSpotClick({{ $horse['id'] }}, 'place', '{{ $horse['name'] }}', {{ $horse['place_odds'] }})">
                            <div class="text-uppercase small fw-bold text-info" style="font-size: 0.72rem;">PLACE</div>
                            <div class="fw-bold text-info font-monospace fs-6">{{ $horse['place_odds'] }}x</div>
                            <div class="chip-badge d-none" id="chip_{{ $horse['id'] }}_place">₹0</div>
                        </div>

                        <!-- SHOW SPOT -->
                        <div class="bet-spot-btn" id="spot_{{ $horse['id'] }}_show" onclick="handleSpotClick({{ $horse['id'] }}, 'show', '{{ $horse['name'] }}', {{ $horse['show_odds'] }})">
                            <div class="text-uppercase small fw-bold text-light" style="font-size: 0.72rem;">SHOW</div>
                            <div class="fw-bold text-light font-monospace fs-6">{{ $horse['show_odds'] }}x</div>
                            <div class="chip-badge d-none" id="chip_{{ $horse['id'] }}_show">₹0</div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Casino Chip Selector & Confirm Action Deck -->
    <div class="derby-card shadow-lg mobile-sticky-footer" style="padding: 8px 10px;">
        <!-- Row 1: Chip tray -->
        <div class="chip-tray mb-2">
            <div class="casino-chip chip-10 active" onclick="selectChip(10, this)">10</div>
            <div class="casino-chip chip-50" onclick="selectChip(50, this)">50</div>
            <div class="casino-chip chip-100" onclick="selectChip(100, this)">100</div>
            <div class="casino-chip chip-500" onclick="selectChip(500, this)">500</div>
            <div class="casino-chip chip-1000" onclick="selectChip(1000, this)">1K</div>
            <div class="casino-chip chip-5000" onclick="selectChip(5000, this)">5K</div>
        </div>
        <!-- Row 2: Stake + Actions -->
        <div class="d-flex align-items-center justify-content-between gap-1 gap-sm-2 w-100 flex-nowrap">
            <div class="text-end me-1 flex-shrink-0" style="min-width:50px;">
                <span class="text-white-50 d-block" style="font-size:0.58rem; line-height: 1;">STAKE</span>
                <span class="fw-black text-warning font-monospace" id="totalStakeDisplay" style="font-size:0.88rem;">₹0.00</span>
            </div>

            <button class="btn btn-outline-danger btn-sm rounded-pill px-2 py-1 fw-bold flex-shrink-0" onclick="clearSelectedBets()" style="font-size:0.75rem;">
                <i class="fa-solid fa-trash me-1"></i>CLR
            </button>

            <button class="btn btn-outline-info btn-sm rounded-pill px-2 py-1 fw-bold flex-shrink-0" onclick="doubleSelectedBets()" style="font-size:0.75rem;">
                <i class="fa-solid fa-angles-up me-1"></i>2X
            </button>

            <button class="btn btn-warning text-dark fw-black rounded-pill px-2 px-sm-3 py-1 shadow-lg flex-grow-1 text-center" id="confirmBetsBtn" onclick="submitBets()" style="font-size:0.82rem; white-space: nowrap;">
                <i class="fa-solid fa-check me-1"></i> CONFIRM BETS
            </button>
        </div>
    </div>

    <!-- Tabs: My Bets & History & Gate Statistics -->
    <div class="derby-card p-3 shadow-lg">
        <div class="d-flex border-bottom border-secondary mb-3">
            <button class="derby-tab-btn active" id="tabBtnMyBets" onclick="switchTab('myBets')">
                <i class="fa-solid fa-ticket me-1"></i> MY BETS (<span id="myBetsCount">0</span>)
            </button>
            <button class="derby-tab-btn" id="tabBtnHistory" onclick="switchTab('history')">
                <i class="fa-solid fa-clock-rotate-left me-1"></i> RECENT DERBY RESULTS
            </button>
            <button class="derby-tab-btn" id="tabBtnGateStats" onclick="switchTab('gateStats')">
                <i class="fa-solid fa-chart-simple me-1"></i> GATE WIN %
            </button>
        </div>

        <!-- Tab Content: My Bets -->
        <div id="tabContentMyBets">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead class="text-white-50 small">
                        <tr>
                            <th>Bet ID</th>
                            <th>Round</th>
                            <th>Runner</th>
                            <th>Type</th>
                            <th class="text-end">Stake</th>
                            <th class="text-center">Odds</th>
                            <th class="text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody id="myBetsTableBody">
                        <tr>
                            <td colspan="7" class="text-center py-4 text-white-50">No bets placed yet for active round. Place chips to play!</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Content: History -->
        <div id="tabContentHistory" class="d-none">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead class="text-white-50 small">
                        <tr>
                            <th>Round ID</th>
                            <th>1st Place (Winner)</th>
                            <th>2nd Place</th>
                            <th>3rd Place</th>
                            <th class="text-center">Total Bets</th>
                            <th class="text-end">Pool Wagered</th>
                            <th class="text-end">Payouts</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        @foreach($recentHistory as $rh)
                        <tr>
                            <td class="font-monospace small">#{{ $rh->round_id }}</td>
                            <td><span class="badge bg-warning text-dark font-monospace fw-bold">#{{ $rh->winner_horse_id }}</span></td>
                            <td><span class="badge bg-secondary text-white font-monospace">#{{ $rh->second_horse_id ?? '-' }}</span></td>
                            <td><span class="badge bg-dark border text-white-50 font-monospace">#{{ $rh->third_horse_id ?? '-' }}</span></td>
                            <td class="text-center font-monospace">{{ $rh->total_bets_count }}</td>
                            <td class="text-end font-monospace text-white-50">₹{{ number_format((float)$rh->total_bet_amount, 2) }}</td>
                            <td class="text-end font-monospace text-success fw-bold">₹{{ number_format((float)$rh->total_payout_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Content: Gate Stats -->
        <div id="tabContentGateStats" class="d-none">
            <div class="row g-3">
                @foreach($gateStats as $gateNum => $stats)
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3 rounded-3 text-center" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1);">
                        <div class="badge px-3 py-1 fs-6 mb-2" style="background: {{ $horses[$gateNum-1]['silk_color'] ?? '#f59e0b' }}">Gate {{ $gateNum }}</div>
                        <div class="fs-4 fw-black text-warning font-monospace">{{ $stats['percent'] }}%</div>
                        <small class="text-white-50">{{ $stats['wins'] }} Wins in 30</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    /* =========================================================================
     * REALTIME CONFIGURATION & CSRF
     * ========================================================================= */
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const IS_AUTHENTICATED = {{ Auth::check() ? 'true' : 'false' }};

    let activeRoundId = "{{ $roundData['round_id'] }}";
    let remainingSeconds = {{ $roundData['remaining_seconds'] }};
    let roundStatus = "{{ $roundData['status'] }}";
    let isLocked = {{ $roundData['is_locked'] ? 'true' : 'false' }};
    let racingCountdown = {{ $roundData['racing_countdown'] }};
    let duration = {{ $roundData['duration'] }};
    let horsesRoster = @json($horses);

    let selectedChipValue = 10;
    let selectedBets = {}; // Key: "horseId_betType" => { horse_id, bet_type, horse_name, odds, amount }

    /* =========================================================================
     * WEB AUDIO SYNTHESIZER (Fanfare bugle, Hoofbeats, Chip click, Cheer)
     * ========================================================================= */
    let audioCtx = null;
    let isSoundEnabled = localStorage.getItem('hr_sound') !== 'false';

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
        localStorage.setItem('hr_sound', isSoundEnabled ? 'true' : 'false');
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
            osc.frequency.setValueAtTime(1200, now);
            osc.frequency.exponentialRampToValueAtTime(700, now + 0.04);
            gain.gain.setValueAtTime(0.2, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.04);
            osc.start(now);
            osc.stop(now + 0.04);
        } catch(e) {}
    }

    function playBugleFanfare() {
        if (!isSoundEnabled) return;
        try {
            initAudio();
            const now = audioCtx.currentTime;
            // Classic race start bugle notes: G4, C5, E5, G5
            const notes = [392, 523.25, 659.25, 783.99];
            notes.forEach((freq, idx) => {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sawtooth';
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                const st = now + (idx * 0.15);
                osc.frequency.setValueAtTime(freq, st);
                gain.gain.setValueAtTime(0.18, st);
                gain.gain.exponentialRampToValueAtTime(0.001, st + 0.35);
                osc.start(st);
                osc.stop(st + 0.35);
            });
        } catch(e) {}
    }

    function playHoofbeatTick() {
        if (!isSoundEnabled) return;
        try {
            initAudio();
            const now = audioCtx.currentTime;
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'triangle';
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.frequency.setValueAtTime(140 + Math.random() * 40, now);
            gain.gain.setValueAtTime(0.15, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.06);
            osc.start(now);
            osc.stop(now + 0.06);
        } catch(e) {}
    }

    function playWinFanfare() {
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
                const st = now + (idx * 0.09);
                osc.frequency.setValueAtTime(freq, st);
                gain.gain.setValueAtTime(0.25, st);
                gain.gain.exponentialRampToValueAtTime(0.001, st + 0.5);
                osc.start(st);
                osc.stop(st + 0.5);
            });
        } catch(e) {}
    }

    /* =========================================================================
     * 2D/3D CANVAS RACETRACK ANIMATOR & SPRINT ENGINE
     * ========================================================================= */
    const canvas = document.getElementById('raceCanvas');
    const ctx = canvas.getContext('2d');

    // 6 Horses positions (0% to 100%)
    let horsesProgress = [0, 0, 0, 0, 0, 0];
    let sprintWaypoints = null; // Interpolated waypoints from status
    let isSprinting = false;
    let sprintStartTime = null;
    let gallopFrame = 0;
    let lastHoofTickTime = 0;
    const particles = [];

    function resizeCanvas() {
        const rect = canvas.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;
        // Use rect.width when available; fall back to window.innerWidth for early calls (mobile)
        const availW = rect.width > 0 ? rect.width : (canvas.parentElement ? canvas.parentElement.clientWidth : window.innerWidth);
        const isMobile = availW < 600;
        const aspectRatio = isMobile ? 0.72 : 0.48;
        const drawH = Math.round(availW * aspectRatio);
        canvas.width  = Math.round(availW * dpr);
        canvas.height = drawH * dpr;
        // Sync the CSS display size so the element takes up the right space
        canvas.style.width  = availW + 'px';
        canvas.style.height = drawH + 'px';
    }
    window.addEventListener('resize', resizeCanvas);
    // Re-run on load to ensure correct size after layout is complete (fixes mobile first-render)
    window.addEventListener('load', resizeCanvas);
    resizeCanvas();

    function adjustColorBrightness(hex, percent) {
        if (!hex) return '#3b1808';
        hex = hex.replace(/^#/, '');
        if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
        const num = parseInt(hex, 16);
        let r = (num >> 16) + percent;
        let g = ((num >> 8) & 0x00FF) + percent;
        let b = (num & 0x0000FF) + percent;
        r = Math.min(255, Math.max(0, r));
        g = Math.min(255, Math.max(0, g));
        b = Math.min(255, Math.max(0, b));
        return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    }

    /**
     * Generate anatomically authentic thoroughbred racehorse SVG with realistic rotary gallop kinematics,
     * sculpted equine musculature, distinct runner coats, facial markings, and aerodynamic jockey.
     */
    function generateThoroughbredSVG(horse, frame) {
        const gate = horse.gate || horse.id || 1;
        const silk = horse.silk_color || '#EF4444';
        const coat = horse.coat_color || '#78350f';
        const coatLight = adjustColorBrightness(coat, 38);
        const coatDark = adjustColorBrightness(coat, -18);
        const coatShadow = adjustColorBrightness(coat, -48);

        // Runner Mane & Tail characteristics
        const isBlonde = horse.id === 2;
        const isGrey = horse.id === 3;
        const maneLight = isBlonde ? '#fef08a' : (isGrey ? '#cbd5e1' : coatDark);
        const maneDark = isBlonde ? '#ca8a04' : (isGrey ? '#64748b' : '#09090b');

        // 4 Biologically authentic rotary gallop kinematic gait frames
        let fHind, nHind, fFore, nFore, tail;

        if (frame === 0) {
            // Frame 0: Reach / Maximum Airborne Forward & Backward Extension
            fHind = `
                <polygon points="44,42 34,50 20,68 15,66 26,48 40,38" fill="${coatShadow}"/>
                <polygon points="20,68 15,66 8,78 12,80" fill="${coatShadow}"/>
                <polygon points="8,78 12,80 5,86 2,84" fill="#09090b"/>
            `;
            nHind = `
                <polygon points="48,40 38,52 24,70 19,68 30,50 44,36" fill="url(#nearLegGrad_${gate})"/>
                <polygon points="24,70 19,68 10,82 14,84" fill="url(#nearLegGrad_${gate})"/>
                <polygon points="10,82 14,84 6,88 3,86" fill="#0f172a"/>
                <polygon points="17,75 13,81 16,82 20,77" fill="#ffffff" opacity="0.9"/>
            `;
            fFore = `
                <polygon points="102,46 114,56 128,70 124,72 108,58 98,50" fill="${coatShadow}"/>
                <polygon points="128,70 124,72 136,80 139,78" fill="${coatShadow}"/>
                <polygon points="136,80 139,78 146,84 143,86" fill="#09090b"/>
            `;
            nFore = `
                <polygon points="106,46 118,58 134,72 130,74 112,60 102,50" fill="url(#nearLegGrad_${gate})"/>
                <polygon points="134,72 130,74 144,82 147,80" fill="url(#nearLegGrad_${gate})"/>
                <polygon points="144,82 147,80 154,86 151,88" fill="#0f172a"/>
                <polygon points="138,76 143,81 146,80 141,74" fill="#ffffff" opacity="0.9"/>
            `;
            tail = `
                <path d="M 28,40 C 18,36 6,38 0,44 C 6,48 18,46 28,45 Z" fill="url(#tailGrad_${gate})"/>
                <path d="M 26,41 C 16,39 6,43 2,48 C 10,50 18,48 26,45 Z" fill="${maneDark}" opacity="0.8"/>
                <path d="M 24,42 C 14,42 4,46 0,52 C 8,53 16,49 24,45 Z" fill="${maneLight}" opacity="0.6"/>
            `;
        } else if (frame === 1) {
            // Frame 1: Diagonal Impact / Ground Strike Stance
            fHind = `
                <polygon points="44,42 38,54 28,68 24,66 32,52 40,38" fill="${coatShadow}"/>
                <polygon points="28,68 24,66 18,78 22,80" fill="${coatShadow}"/>
                <polygon points="18,78 22,80 16,84 13,82" fill="#09090b"/>
            `;
            nHind = `
                <polygon points="48,40 44,56 36,72 31,70 38,54 44,36" fill="url(#nearLegGrad_${gate})"/>
                <polygon points="36,72 31,70 26,82 30,84" fill="url(#nearLegGrad_${gate})"/>
                <polygon points="26,82 30,84 24,88 21,86" fill="#0f172a"/>
                <polygon points="31,76 27,81 30,82 34,77" fill="#ffffff" opacity="0.9"/>
            `;
            fFore = `
                <polygon points="102,46 100,58 92,70 88,68 94,56 98,50" fill="${coatShadow}"/>
                <polygon points="92,70 88,68 82,78 85,80" fill="${coatShadow}"/>
                <polygon points="82,78 85,80 80,84 77,82" fill="#09090b"/>
            `;
            nFore = `
                <polygon points="106,46 108,60 110,76 105,76 102,60 102,50" fill="url(#nearLegGrad_${gate})"/>
                <polygon points="110,76 105,76 106,84 110,84" fill="url(#nearLegGrad_${gate})"/>
                <polygon points="106,84 110,84 112,88 104,88" fill="#0f172a"/>
                <polygon points="106,78 105,83 109,83 109,78" fill="#ffffff" opacity="0.9"/>
            `;
            tail = `
                <path d="M 28,40 C 18,38 8,44 0,52 C 8,54 20,48 28,45 Z" fill="url(#tailGrad_${gate})"/>
                <path d="M 26,41 C 16,42 6,48 2,56 C 10,57 18,50 26,45 Z" fill="${maneDark}" opacity="0.8"/>
            `;
        } else if (frame === 2) {
            // Frame 2: Airborne Collection / Compact Spring Suspension
            fHind = `
                <polygon points="44,42 48,52 38,64 34,62 42,50 40,38" fill="${coatShadow}"/>
                <polygon points="38,64 34,62 26,72 29,74" fill="${coatShadow}"/>
                <polygon points="26,72 29,74 24,78 21,76" fill="#09090b"/>
            `;
            nHind = `
                <polygon points="48,40 54,54 44,66 39,64 48,52 44,36" fill="url(#nearLegGrad_${gate})"/>
                <polygon points="44,66 39,64 32,74 35,76" fill="url(#nearLegGrad_${gate})"/>
                <polygon points="32,74 35,76 29,80 26,78" fill="#0f172a"/>
                <polygon points="39,69 34,74 37,75 42,70" fill="#ffffff" opacity="0.9"/>
            `;
            fFore = `
                <polygon points="102,46 96,56 86,66 82,64 90,54 98,50" fill="${coatShadow}"/>
                <polygon points="86,66 82,64 74,72 77,74" fill="${coatShadow}"/>
                <polygon points="74,72 77,74 72,78 69,76" fill="#09090b"/>
            `;
            nFore = `
                <polygon points="106,46 100,58 88,68 84,66 94,56 102,50" fill="url(#nearLegGrad_${gate})"/>
                <polygon points="88,68 84,66 76,74 79,76" fill="url(#nearLegGrad_${gate})"/>
                <polygon points="76,74 79,76 74,80 71,78" fill="#0f172a"/>
                <polygon points="84,70 79,75 82,76 87,71" fill="#ffffff" opacity="0.9"/>
            `;
            tail = `
                <path d="M 28,40 C 18,36 8,40 1,46 C 9,48 19,45 28,45 Z" fill="url(#tailGrad_${gate})"/>
                <path d="M 26,41 C 16,39 6,43 3,50 C 11,51 18,47 26,45 Z" fill="${maneDark}" opacity="0.8"/>
            `;
        } else {
            // Frame 3: Thrust / Explosive Backward Turf Propulsion
            fHind = `
                <polygon points="44,42 36,52 24,70 19,68 30,50 40,38" fill="${coatShadow}"/>
                <polygon points="24,70 19,68 12,82 16,84" fill="${coatShadow}"/>
                <polygon points="12,82 16,84 10,88 7,86" fill="#09090b"/>
            `;
            nHind = `
                <polygon points="48,40 38,54 26,74 21,72 32,52 44,36" fill="url(#nearLegGrad_${gate})"/>
                <polygon points="26,74 21,72 15,86 19,88" fill="url(#nearLegGrad_${gate})"/>
                <polygon points="15,86 19,88 13,92 10,90" fill="#0f172a"/>
                <polygon points="21,79 17,85 20,86 24,80" fill="#ffffff" opacity="0.9"/>
            `;
            fFore = `
                <polygon points="102,46 110,56 122,68 118,70 104,58 98,50" fill="${coatShadow}"/>
                <polygon points="122,68 118,70 128,78 131,76" fill="${coatShadow}"/>
                <polygon points="128,78 131,76 136,82 133,84" fill="#09090b"/>
            `;
            nFore = `
                <polygon points="106,46 114,58 128,70 124,72 108,60 102,50" fill="url(#nearLegGrad_${gate})"/>
                <polygon points="128,70 124,72 136,80 139,78" fill="url(#nearLegGrad_${gate})"/>
                <polygon points="136,80 139,78 146,84 143,86" fill="#0f172a"/>
                <polygon points="132,74 137,79 140,78 135,72" fill="#ffffff" opacity="0.9"/>
            `;
            tail = `
                <path d="M 28,40 C 17,35 5,37 0,42 C 6,46 17,45 28,45 Z" fill="url(#tailGrad_${gate})"/>
                <path d="M 26,41 C 15,38 4,42 0,48 C 8,49 17,47 26,45 Z" fill="${maneDark}" opacity="0.8"/>
            `;
        }

        // Facial Markings (Star or Blaze)
        const faceMarking = (horse.id === 2 || horse.id === 5)
            ? `<polygon points="138,20 146,24 150,28 147,30 142,26 136,22" fill="#ffffff" opacity="0.95"/>`
            : `<polygon points="135,21 138,20 139,23 136,24" fill="#ffffff" opacity="0.95"/>`;

        return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 175 100" width="175" height="100">
            <defs>
                <linearGradient id="bodyGrad_${gate}" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="${coatLight}"/>
                    <stop offset="45%" stop-color="${coat}"/>
                    <stop offset="100%" stop-color="${coatDark}"/>
                </linearGradient>
                <linearGradient id="nearLegGrad_${gate}" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="${coat}"/>
                    <stop offset="100%" stop-color="${coatDark}"/>
                </linearGradient>
                <linearGradient id="tailGrad_${gate}" x1="100%" y1="0%" x2="0%" y2="0%">
                    <stop offset="0%" stop-color="${maneDark}"/>
                    <stop offset="100%" stop-color="${maneLight}"/>
                </linearGradient>
            </defs>

            <!-- 1. Far Shadow Legs (Natural Depth) -->
            ${fHind}
            ${fFore}

            <!-- 2. Wind-swept Flowing Plume Tail -->
            ${tail}

            <!-- 3. Sculpted Thoroughbred Torso, Deep Chest & Arched Neck Crest -->
            <path d="
                M 88,32
                C 78,35 62,35 48,31
                C 34,27 26,34 24,40
                C 24,46 32,54 44,52
                C 54,50 66,48 78,54
                C 90,60 102,58 108,50
                C 114,42 118,36 126,34
                L 142,32
                C 148,31 154,29 152,26
                C 150,23 144,21 136,19
                L 128,15
                C 118,20 102,26 88,32
                Z"
                fill="url(#bodyGrad_${gate})"
                stroke="${coatShadow}"
                stroke-width="1.2"
            />

            <!-- Muscular Shoulder & Flank Highlights -->
            <ellipse cx="102" cy="46" rx="9" ry="6" transform="rotate(-28 102 46)" fill="${coatLight}" opacity="0.38"/>
            <ellipse cx="44" cy="40" rx="12" ry="8" transform="rotate(-12 44 40)" fill="${coatLight}" opacity="0.35"/>
            <path d="M 64,48 C 72,50 82,52 92,51" stroke="${coatDark}" stroke-width="1.5" fill="none" opacity="0.4"/>

            <!-- Alert Thoroughbred Ears -->
            <polygon points="124,15 126,6 129,14" fill="${coatShadow}"/>
            <polygon points="127,14 130,5 132,13" fill="${coatDark}"/>
            <line x1="128" y1="13" x2="129.5" y2="7" stroke="#000" stroke-width="0.8" opacity="0.6"/>

            <!-- White Facial Marking -->
            ${faceMarking}

            <!-- Expressive Thoroughbred Eye with Specular Glint -->
            <ellipse cx="134" cy="21" rx="2.8" ry="2.2" fill="#09090b"/>
            <ellipse cx="134" cy="21" rx="2" ry="1.6" fill="#3b1d11"/>
            <circle cx="134.8" cy="20.4" r="0.8" fill="#ffffff"/>

            <!-- Refined Muzzle & Flared Nostril -->
            <ellipse cx="149" cy="28.5" rx="3.5" ry="2" fill="${coatDark}"/>
            <ellipse cx="150" cy="28" rx="1.5" ry="0.9" fill="#000000"/>
            <path d="M 147,30 L 151,29" stroke="#09090b" stroke-width="0.8"/>

            <!-- Bridle Headstall & Silver Snaffle Bit Ring -->
            <path d="M 128,15 L 134,23 L 145,29" stroke="rgba(255,255,255,0.7)" stroke-width="0.9" fill="none"/>
            <path d="M 130,17 L 124,28" stroke="rgba(255,255,255,0.5)" stroke-width="0.8" fill="none"/>
            <path d="M 140,26 L 144,30" stroke="rgba(255,255,255,0.7)" stroke-width="0.9" fill="none"/>
            <circle cx="145" cy="29" r="2.4" stroke="#e2e8f0" stroke-width="1.3" fill="none"/>

            <!-- Streamlined Wind-blown Mane Locks -->
            <path d="M 126,15 C 118,17 114,21 110,23" stroke="${maneDark}" stroke-width="3" stroke-linecap="round" fill="none"/>
            <path d="M 116,19 C 108,22 102,26 98,28" stroke="${maneLight}" stroke-width="2.5" stroke-linecap="round" fill="none"/>
            <path d="M 106,23 C 98,27 92,30 88,32" stroke="${maneDark}" stroke-width="2.8" stroke-linecap="round" fill="none"/>

            <!-- 4. Near Fore & Hind Legs (Foreground Articulated Musculature) -->
            ${nHind}
            ${nFore}

            <!-- 5. Saddlecloth with Runner Silk Color & Bold Gate Number -->
            <rect x="68" y="28" width="18" height="13" rx="2.5" fill="${silk}" stroke="#ffffff" stroke-width="1.2"/>
            <text x="77" y="38" font-family="'JetBrains Mono', monospace" font-size="9" font-weight="900" fill="#ffffff" text-anchor="middle">${gate}</text>

            <!-- 6. Racing Jockey in Aerodynamic Low Aero Tuck -->
            <ellipse cx="73" cy="31" rx="7" ry="4.5" transform="rotate(-32 73 31)" fill="#f8fafc"/>
            <rect x="68" y="32" width="6" height="4.5" rx="1" fill="#09090b"/>
            <ellipse cx="82" cy="24" rx="10" ry="6.5" transform="rotate(-15 82 24)" fill="${silk}" stroke="rgba(255,255,255,0.4)" stroke-width="1"/>
            <circle cx="94" cy="16" r="5.5" fill="${silk}" stroke="#ffffff" stroke-width="1"/>
            <polygon points="98,16 103,17 99,19" fill="#09090b"/>
            <rect x="96" y="15" width="4.5" height="2.8" rx="1" fill="#38bdf8"/>
            <ellipse cx="98" cy="25" rx="7" ry="2.2" transform="rotate(25 98 25)" fill="${silk}"/>
            <circle cx="106" cy="27" r="2.2" fill="#fbcfe8"/>
            <line x1="106" y1="27" x2="145" y2="29" stroke="rgba(255,255,255,0.95)" stroke-width="1.2"/>
        </svg>`;
    }

    // Pre-cache SVG sprites for all 6 runners across all 4 frames
    const horseSpriteCache = {};

    function initHorseSprites() {
        horsesRoster.forEach(horse => {
            horseSpriteCache[horse.id] = [];
            for (let f = 0; f < 4; f++) {
                const img = new Image();
                img.src = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(generateThoroughbredSVG(horse, f));
                horseSpriteCache[horse.id][f] = img;
            }
        });
    }
    initHorseSprites();

    let activeRaceData = null;
    let latestUserBets = [];

    function drawRacetrack() {
        const W = canvas.width;
        const H = canvas.height;
        ctx.clearRect(0, 0, W, H);

        // 1. Lush Green Turf Background
        const turfGrad = ctx.createLinearGradient(0, 0, 0, H);
        turfGrad.addColorStop(0, '#062010');
        turfGrad.addColorStop(0.5, '#0b381d');
        turfGrad.addColorStop(1, '#05180c');
        ctx.fillStyle = turfGrad;
        ctx.fillRect(0, 0, W, H);

        // 2. Dirt Running Track Lanes (6 Lanes)
        const laneTop = H * 0.13;
        const laneHeight = (H * 0.82) / 6;

        // Calculate live rankings of all 6 contenders based on horsesProgress
        const rankedContenders = horsesProgress.map((prog, idx) => ({
            idx,
            horse: horsesRoster[idx] || { id: idx + 1, name: `Horse ${idx+1}`, gate: idx + 1, silk_color: '#EF4444' },
            prog
        })).sort((a, b) => b.prog - a.prog);

        const rankMap = {};
        rankedContenders.forEach((item, r) => {
            rankMap[item.idx] = r + 1;
        });

        // Top Broadcast HUD Bar (Dark Glassmorphism with Live Order)
        const headerH = Math.max(34, H * 0.10);
        ctx.fillStyle = 'rgba(2, 10, 6, 0.92)';
        ctx.fillRect(0, 0, W, headerH);
        ctx.strokeStyle = 'rgba(245, 158, 11, 0.4)';
        ctx.lineWidth = 1.5;
        ctx.beginPath();
        ctx.moveTo(0, headerH);
        ctx.lineTo(W, headerH);
        ctx.stroke();

        // Broadcast Live Status Indicator
        ctx.fillStyle = isSprinting ? '#ef4444' : '#10b981';
        ctx.beginPath();
        ctx.arc(W * 0.02, headerH * 0.5, 4.5, 0, Math.PI * 2);
        ctx.fill();

        ctx.fillStyle = '#ffffff';
        ctx.font = `bold ${Math.max(10, headerH * 0.38)}px "Plus Jakarta Sans", sans-serif`;
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';
        ctx.fillText(isSprinting ? 'LIVE DERBY 1000M SPRINT' : 'STARTING GATES (PRE-RACE)', W * 0.032, headerH * 0.5);

        // Live Order Chips on Header
        if (isSprinting && rankedContenders[0].prog > 0.5) {
            const leader = rankedContenders[0].horse;
            const second = rankedContenders[1].horse;
            const third = rankedContenders[2].horse;

            ctx.font = `bold ${Math.max(9, headerH * 0.34)}px "JetBrains Mono", monospace`;
            ctx.textAlign = 'right';
            ctx.fillStyle = '#f59e0b';
            ctx.fillText(`👑 1st: #${leader.gate} ${leader.name}   🥈 2nd: #${second.gate} ${second.name}   🥉 3rd: #${third.gate} ${third.name}`, W * 0.98, headerH * 0.5);
        }

        // 3. Draw Track Lanes & Starting Gate Stalls
        const stallW = Math.max(122, W * 0.125);
        const finishX = W * 0.92;
        const startX = stallW + 56;
        const raceSpan = finishX - startX;

        for (let i = 0; i < 6; i++) {
            const y = laneTop + (i * laneHeight);
            const runner = horsesRoster[i] || { id: i + 1, name: `Horse ${i + 1}`, silk_color: '#EF4444', gate: i + 1 };

            // Alternating lane turf shade
            ctx.fillStyle = (i % 2 === 0) ? 'rgba(255, 255, 255, 0.03)' : 'rgba(0, 0, 0, 0.08)';
            ctx.fillRect(0, y, W, laneHeight);

            // Highlight leader's lane during sprint with golden glow
            if (isSprinting && rankMap[i] === 1 && horsesProgress[i] > 1) {
                ctx.fillStyle = 'rgba(245, 158, 11, 0.09)';
                ctx.fillRect(0, y, W, laneHeight);
            }

            // White turf boundary line
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.15)';
            ctx.lineWidth = 1.5;
            ctx.setLineDash([8, 8]);
            ctx.beginPath();
            ctx.moveTo(stallW, y);
            ctx.lineTo(W, y);
            ctx.stroke();
            ctx.setLineDash([]);

            // Dedicated Metallic Starting Gate Stall (No Text Clutter on Turf)
            ctx.fillStyle = 'rgba(8, 18, 12, 0.94)';
            ctx.fillRect(0, y + 2, stallW - 4, laneHeight - 4);
            ctx.strokeStyle = 'rgba(245, 158, 11, 0.45)';
            ctx.lineWidth = 1.2;
            ctx.strokeRect(0, y + 2, stallW - 4, laneHeight - 4);

            // Gate Number Pill
            ctx.fillStyle = runner.silk_color || '#EF4444';
            ctx.beginPath();
            ctx.roundRect(8, y + (laneHeight * 0.18), 30, laneHeight * 0.64, 5);
            ctx.fill();
            ctx.fillStyle = '#ffffff';
            ctx.font = `bold ${Math.max(10, laneHeight * 0.28)}px "JetBrains Mono", monospace`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(`G${i + 1}`, 23, y + laneHeight * 0.50);

            // Runner Name inside stall box
            ctx.fillStyle = '#f8fafc';
            ctx.font = `bold ${Math.max(10, laneHeight * 0.22)}px "Plus Jakarta Sans", sans-serif`;
            ctx.textAlign = 'left';
            ctx.fillText(runner.name, 44, y + laneHeight * 0.50);

            // Steel Starting Gate Post Barrier Line
            ctx.strokeStyle = isSprinting ? 'rgba(16, 185, 129, 0.8)' : 'rgba(245, 158, 11, 0.8)';
            ctx.lineWidth = 3;
            ctx.beginPath();
            ctx.moveTo(stallW - 2, y);
            ctx.lineTo(stallW - 2, y + laneHeight);
            ctx.stroke();

            // Faint Turf Watermark Ahead in Lane (Premier Ascot Style)
            ctx.fillStyle = 'rgba(255, 255, 255, 0.05)';
            ctx.font = `bold ${Math.max(11, laneHeight * 0.24)}px "JetBrains Mono", monospace`;
            ctx.textAlign = 'left';
            ctx.fillText(`#${i + 1}  ${runner.name.toUpperCase()}`, stallW + (W * 0.15), y + laneHeight * 0.52);
        }

        // 4. Distance Markers (250m, 500m, 750m)
        const distances = [
            { pct: 0.25, label: '250m' },
            { pct: 0.50, label: '500m' },
            { pct: 0.75, label: '750m' },
        ];
        distances.forEach(d => {
            const x = startX + (d.pct * raceSpan);
            ctx.strokeStyle = 'rgba(245, 158, 11, 0.35)';
            ctx.lineWidth = 2;
            ctx.setLineDash([4, 6]);
            ctx.beginPath();
            ctx.moveTo(x, laneTop - 8);
            ctx.lineTo(x, laneTop + (6 * laneHeight));
            ctx.stroke();
            ctx.setLineDash([]);

            ctx.fillStyle = '#f59e0b';
            ctx.font = `bold ${Math.max(10, H * 0.03)}px "JetBrains Mono", monospace`;
            ctx.textAlign = 'center';
            ctx.fillText(d.label, x, laneTop - 12);
        });

        // 5. Checkered Finish Line
        const squareSize = 10;
        for (let y = laneTop - 8; y < laneTop + (6 * laneHeight); y += squareSize) {
            for (let c = 0; c < 2; c++) {
                ctx.fillStyle = ((Math.floor(y / squareSize) + c) % 2 === 0) ? '#ffffff' : '#000000';
                ctx.fillRect(finishX + (c * squareSize), y, squareSize, squareSize);
            }
        }

        // Red Finish Banner Header
        ctx.fillStyle = '#ef4444';
        ctx.fillRect(finishX - 10, laneTop - 28, squareSize * 4, 18);
        ctx.fillStyle = '#ffffff';
        ctx.font = `bold ${Math.max(10, H * 0.028)}px sans-serif`;
        ctx.textAlign = 'center';
        ctx.fillText('FINISH', finishX + 10, laneTop - 15);

        // 6. Draw Dust Particles
        for (let p = particles.length - 1; p >= 0; p--) {
            const pt = particles[p];
            ctx.fillStyle = `rgba(217, 119, 6, ${pt.alpha})`;
            ctx.beginPath();
            ctx.arc(pt.x, pt.y, pt.size, 0, Math.PI * 2);
            ctx.fill();
            pt.x -= pt.vx;
            pt.y += pt.vy;
            pt.alpha -= 0.03;
            if (pt.alpha <= 0) particles.splice(p, 1);
        }

        // 7. Draw 6 Realistic Thoroughbred Horses
        gallopFrame += 0.24;

        for (let h = 0; h < 6; h++) {
            const horse = horsesRoster[h] || { id: h + 1, name: `Horse ${h+1}`, silk_color: '#EF4444', gate: h + 1 };
            const progress = horsesProgress[h] / 100.0;
            const horseX = startX + (progress * raceSpan);
            const horseY = laneTop + (h * laneHeight) + (laneHeight * 0.5);

            // Emit dynamic dust particles while galloping
            if (isSprinting && progress > 0.01 && Math.random() < 0.65) {
                particles.push({
                    x: horseX - 22,
                    y: horseY + 14 + (Math.random() * 8),
                    size: 2 + Math.random() * 4,
                    vx: 1.5 + Math.random() * 2,
                    vy: (Math.random() - 0.5) * 1.5,
                    alpha: 0.85,
                });
            }

            drawChampionHorseSprite(ctx, horseX, horseY, horse, gallopFrame + (h * 1.3), laneHeight, rankMap[h], isSprinting);
        }
    }

    /**
     * Render high-fidelity thoroughbred racehorse SVG sprite with realistic proportions,
     * natural equine rotary gallop kinematics, and floating real-time rank badges (👑 1st, 🥈 2nd, 🥉 3rd).
     */
    function drawChampionHorseSprite(ctx, x, y, horse, frame, laneH, rank, sprinting) {
        ctx.save();
        ctx.translate(x, y);

        const frameIdx = Math.floor(frame) % 4;
        const spriteImg = (horseSpriteCache[horse.id] && horseSpriteCache[horse.id][frameIdx]) ? horseSpriteCache[horse.id][frameIdx] : null;

        const spriteH = Math.min(74, Math.max(54, laneH * 0.88));
        const spriteW = spriteH * 1.75;

        // Realistic Turf Ground Shadow
        const bobY = Math.abs(Math.sin(frame * 1.6)) * 3;
        ctx.fillStyle = 'rgba(0, 0, 0, 0.42)';
        ctx.beginPath();
        ctx.ellipse(2, spriteH * 0.40, spriteW * 0.28, 5, 0, 0, Math.PI * 2);
        ctx.fill();

        // Draw Ultra-Realistic Thoroughbred SVG Sprite
        if (spriteImg && spriteImg.complete) {
            ctx.drawImage(spriteImg, -spriteW * 0.48, -spriteH * 0.52 + bobY, spriteW, spriteH);
        }

        // Live Floating Rank Badge (Crown for 1st, Silver for 2nd, Bronze for 3rd)
        if (sprinting) {
            const badgeY = -spriteH * 0.56 + bobY;
            if (rank === 1) {
                // 👑 1st (Pulsing Golden Crown Badge)
                ctx.save();
                ctx.shadowColor = '#f59e0b';
                ctx.shadowBlur = 14;
                ctx.fillStyle = '#f59e0b';
                ctx.beginPath();
                ctx.roundRect(-24, badgeY - 14, 48, 17, 8);
                ctx.fill();
                ctx.restore();

                ctx.fillStyle = '#000000';
                ctx.font = '900 10px "Plus Jakarta Sans", sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('👑 1st', 0, badgeY - 5.5);
            } else if (rank === 2) {
                ctx.fillStyle = 'rgba(226, 232, 240, 0.95)';
                ctx.beginPath();
                ctx.roundRect(-20, badgeY - 13, 40, 15, 7);
                ctx.fill();

                ctx.fillStyle = '#0f172a';
                ctx.font = 'bold 9px "Plus Jakarta Sans", sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('🥈 2nd', 0, badgeY - 5.5);
            } else if (rank === 3) {
                ctx.fillStyle = 'rgba(217, 119, 6, 0.95)';
                ctx.beginPath();
                ctx.roundRect(-20, badgeY - 13, 40, 15, 7);
                ctx.fill();

                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 9px "Plus Jakarta Sans", sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('🥉 3rd', 0, badgeY - 5.5);
            }
        }
        ctx.restore();
    }

    function animateTrackLoop() {
        if (isSprinting && sprintStartTime) {
            const elapsedMs = performance.now() - sprintStartTime;
            const totalSprintMs = racingCountdown * 1000;
            const t = Math.min(1.0, elapsedMs / totalSprintMs);

            // Interpolate progress along deterministic waypoints
            if (sprintWaypoints) {
                sprintWaypoints.forEach((wpData) => {
                    const hIdx = (wpData.id - 1);
                    if (hIdx >= 0 && hIdx < 6) {
                        const wp = wpData.waypoints; // [0, 26.5, 53.5, 78.5, 100]
                        const stepCount = wp.length - 1;
                        const stepT = t * stepCount;
                        const stepIdx = Math.min(stepCount - 1, Math.floor(stepT));
                        const subT = stepT - stepIdx;

                        const curWp = wp[stepIdx];
                        const nextWp = wp[stepIdx + 1];
                        horsesProgress[hIdx] = curWp + ((nextWp - curWp) * subT);
                    }
                });
            } else {
                // Natural fallback sprint
                for (let i = 0; i < 6; i++) {
                    horsesProgress[i] = t * 100.0;
                }
            }

            // Hoof sound loop during sprint
            if (performance.now() - lastHoofTickTime > 180) {
                playHoofbeatTick();
                lastHoofTickTime = performance.now();
            }

            if (t >= 1.0) {
                isSprinting = false;
                onSprintComplete(activeRaceData);
            }
        } else if (!isSprinting && roundStatus === 'betting') {
            // Keep horses lined up at the starting gates (0% progress)
            horsesProgress = [0, 0, 0, 0, 0, 0];
        }

        drawRacetrack();
        requestAnimationFrame(animateTrackLoop);
    }
    requestAnimationFrame(animateTrackLoop);

    /* =========================================================================
     * CHIPS SELECTION & WAGER MANAGEMENT
     * ========================================================================= */
    function selectChip(value, el) {
        selectedChipValue = value;
        document.querySelectorAll('.casino-chip').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
        playChipClick();
    }

    function handleSpotClick(horseId, betType, horseName, odds) {
        if (isLocked || roundStatus === 'racing') {
            showToast('Bets are closed! The race sprint is active.', 'danger');
            return;
        }

        const key = `${horseId}_${betType}`;
        if (!selectedBets[key]) {
            selectedBets[key] = {
                horse_id: horseId,
                bet_type: betType,
                horse_name: horseName,
                odds: odds,
                amount: 0,
            };
        }

        selectedBets[key].amount += selectedChipValue;
        playChipClick();

        updateBetSpotsUI();
    }

    function updateBetSpotsUI() {
        let totalStake = 0;

        // Reset all spot badges
        document.querySelectorAll('.chip-badge').forEach(b => b.classList.add('d-none'));
        document.querySelectorAll('.bet-spot-btn').forEach(b => b.classList.remove('has-bet'));

        Object.keys(selectedBets).forEach(key => {
            const bet = selectedBets[key];
            if (bet.amount > 0) {
                totalStake += bet.amount;
                const spotEl = document.getElementById(`spot_${bet.horse_id}_${bet.bet_type}`);
                const chipEl = document.getElementById(`chip_${bet.horse_id}_${bet.bet_type}`);

                if (spotEl && chipEl) {
                    spotEl.classList.add('has-bet');
                    chipEl.classList.remove('d-none');
                    chipEl.innerText = bet.amount >= 1000 ? `${(bet.amount/1000).toFixed(1)}k` : `₹${bet.amount}`;
                }
            }
        });

        document.getElementById('totalStakeDisplay').innerText = `₹${totalStake.toFixed(2)}`;
    }

    function clearSelectedBets() {
        selectedBets = {};
        updateBetSpotsUI();
        playChipClick();
    }

    function doubleSelectedBets() {
        Object.keys(selectedBets).forEach(k => {
            selectedBets[k].amount *= 2;
        });
        updateBetSpotsUI();
        playChipClick();
    }

    /* =========================================================================
     * SUBMIT BETS VIA AJAX
     * ========================================================================= */
    async function submitBets() {
        if (!IS_AUTHENTICATED) {
            window.location.href = "{{ route('login') }}";
            return;
        }

        const betsPayload = Object.values(selectedBets).filter(b => b.amount > 0);
        if (betsPayload.length === 0) {
            showToast('Please select a runner and place chips on Win, Place, or Show!', 'warning');
            return;
        }

        const btn = document.getElementById('confirmBetsBtn');
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-1"></i> PLACING...`;

        try {
            const res = await fetch("{{ route('api.horse-racing.place-bet') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify({ bets: betsPayload }),
            });

            const data = await res.json();

            if (data.success) {
                showToast(data.message || 'Bets placed successfully!', 'success');
                document.getElementById('userWalletBalance').innerText = `₹${parseFloat(data.new_balance).toFixed(2)}`;
                clearSelectedBets();
                fetchLiveStatus(); // Refresh active bets feed
            } else {
                showToast(data.message || 'Bet placement failed.', 'danger');
            }
        } catch(err) {
            showToast('Network error while placing bets.', 'danger');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-check me-1"></i> CONFIRM BETS`;
        }
    }

    /* =========================================================================
     * REALTIME STATUS POLLER & RACE SYNCHRONIZATION
     * ========================================================================= */
    let lastHandledRoundId = activeRoundId;
    let sprintAlreadyTriggered = false;

    async function fetchLiveStatus() {
        try {
            const res = await fetch("{{ route('api.horse-racing.status') }}");
            const data = await res.json();

            if (!data.success) return;

            remainingSeconds = data.remaining_seconds;
            isLocked = data.is_locked;
            roundStatus = data.status;
            horsesRoster = data.horses;

            // Update UI elements
            document.getElementById('roundIdBadge').innerText = data.round_id;
            document.getElementById('roundTimerBadge').innerText = `${remainingSeconds}s`;

            if (data.balance !== undefined && IS_AUTHENTICATED) {
                document.getElementById('userWalletBalance').innerText = `₹${parseFloat(data.balance).toFixed(2)}`;
            }

            // Update active race data when locked or racing
            if (data.active_race && data.active_race.waypoints) {
                activeRaceData = data.active_race;
            }

            // Detect new round
            if (data.round_id !== activeRoundId) {
                activeRoundId = data.round_id;
                sprintAlreadyTriggered = false;
                isSprinting = false;
                activeRaceData = null;
                sprintWaypoints = null;
                horsesProgress = [0, 0, 0, 0, 0, 0];
                clearSelectedBets();
                updateRecentHistoryTable(data.history);
            }

            // Trigger sprint when countdown enters locked racing window (last 15s)
            if (isLocked && !sprintAlreadyTriggered) {
                sprintAlreadyTriggered = true;
                isSprinting = true;
                sprintStartTime = performance.now();

                // Compute trajectories from active round
                if (data.active_race && data.active_race.waypoints) {
                    sprintWaypoints = data.active_race.waypoints;
                    activeRaceData = data.active_race;
                } else if (data.last_round && data.last_round.waypoints) {
                    sprintWaypoints = data.last_round.waypoints;
                    activeRaceData = data.last_round;
                }
                playBugleFanfare();
            }

            // If user enters during an ongoing sprint
            if (isLocked && !isSprinting && !sprintAlreadyTriggered) {
                sprintAlreadyTriggered = true;
                isSprinting = true;
                const elapsedSec = Math.max(0, racingCountdown - remainingSeconds);
                sprintStartTime = performance.now() - (elapsedSec * 1000);

                if (data.active_race && data.active_race.waypoints) {
                    sprintWaypoints = data.active_race.waypoints;
                    activeRaceData = data.active_race;
                } else if (data.last_round && data.last_round.waypoints) {
                    sprintWaypoints = data.last_round.waypoints;
                    activeRaceData = data.last_round;
                }
            }

            // Update My Bets table
            latestUserBets = data.my_bets || [];
            updateMyBetsTable(data.my_bets);

        } catch(e) {}
    }

    // Local 1s ticker countdown
    setInterval(() => {
        if (remainingSeconds > 0) {
            remainingSeconds--;
            document.getElementById('roundTimerBadge').innerText = `${remainingSeconds}s`;

            if (remainingSeconds <= racingCountdown && !isLocked) {
                isLocked = true;
            }
        }
    }, 1000);

    // 1.5s Server Status Poller
    setInterval(fetchLiveStatus, 1500);

    function updateMyBetsTable(bets) {
        const tbody = document.getElementById('myBetsTableBody');
        const countBadge = document.getElementById('myBetsCount');

        if (!bets || bets.length === 0) {
            countBadge.innerText = '0';
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-white-50">No bets placed for active round. Place chips to play!</td></tr>`;
            return;
        }

        countBadge.innerText = bets.length;
        let html = '';
        bets.forEach(b => {
            html += `
                <tr>
                    <td class="font-monospace small">${b.bet_id}</td>
                    <td class="font-monospace small">#${activeRoundId}</td>
                    <td><span class="badge bg-warning text-dark font-monospace">#${b.horse_id}</span> <strong>${b.horse_name}</strong></td>
                    <td><span class="badge bg-dark border text-uppercase">${b.bet_type}</span></td>
                    <td class="text-end font-monospace">₹${parseFloat(b.bet_amount).toFixed(2)}</td>
                    <td class="text-center font-monospace">${b.odds}x</td>
                    <td class="text-end"><span class="badge bg-info text-dark">PENDING</span></td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function updateRecentHistoryTable(history) {
        if (!history || history.length === 0) return;
        const tbody = document.getElementById('historyTableBody');
        let html = '';
        history.slice(0, 15).forEach(rh => {
            html += `
                <tr>
                    <td class="font-monospace small">#${rh.round_id}</td>
                    <td><span class="badge bg-warning text-dark font-monospace fw-bold">#${rh.winner_horse_id}</span></td>
                    <td><span class="badge bg-secondary text-white font-monospace">#${rh.second_horse_id || '-'}</span></td>
                    <td><span class="badge bg-dark border text-white-50 font-monospace">#${rh.third_horse_id || '-'}</span></td>
                    <td class="text-center font-monospace">${rh.total_bets}</td>
                    <td class="text-end font-monospace text-white-50">₹${parseFloat(rh.total_amount).toFixed(2)}</td>
                    <td class="text-end font-monospace text-success fw-bold">₹${parseFloat(rh.total_payout).toFixed(2)}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function onSprintComplete(activeRace) {
        playWinFanfare();
        if (activeRace) {
            showPodiumModal(activeRace);
        }
        setTimeout(fetchLiveStatus, 1000);
    }

    /* =========================================================================
     * PODIUM WINNER MODAL
     * ========================================================================= */
    function showPodiumModal(racePodium) {
        if (!racePodium) return;
        document.getElementById('podiumRoundId').innerText = `Round #${racePodium.round_id}`;
        document.getElementById('podiumWinner').innerText = `#${racePodium.winner_horse_id}`;
        document.getElementById('podiumSecond').innerText = `#${racePodium.second_horse_id}`;
        document.getElementById('podiumThird').innerText = `#${racePodium.third_horse_id}`;

        const h1 = horsesRoster.find(h => h.id === racePodium.winner_horse_id);
        const h2 = horsesRoster.find(h => h.id === racePodium.second_horse_id);
        const h3 = horsesRoster.find(h => h.id === racePodium.third_horse_id);

        document.getElementById('podiumWinnerName').innerText = h1 ? h1.name : `Runner #${racePodium.winner_horse_id}`;
        document.getElementById('podiumSecondName').innerText = h2 ? h2.name : `Runner #${racePodium.second_horse_id}`;
        document.getElementById('podiumThirdName').innerText = h3 ? h3.name : `Runner #${racePodium.third_horse_id}`;

        // Check if user won on active round bets
        const winEl = document.getElementById('podiumUserWinnings');
        let userWonAmount = 0;
        if (latestUserBets && latestUserBets.length > 0) {
            latestUserBets.forEach(b => {
                const bHorse = parseInt(b.horse_id);
                const bType = b.bet_type;
                const bOdds = parseFloat(b.odds);
                const bStake = parseFloat(b.bet_amount);
                let won = false;
                if (bType === 'win' && bHorse === racePodium.winner_horse_id) won = true;
                if (bType === 'place' && (bHorse === racePodium.winner_horse_id || bHorse === racePodium.second_horse_id)) won = true;
                if (bType === 'show' && (bHorse === racePodium.winner_horse_id || bHorse === racePodium.second_horse_id || bHorse === racePodium.third_horse_id)) won = true;
                if (won) {
                    userWonAmount += (bStake * bOdds);
                }
            });
        }

        if (userWonAmount > 0) {
            winEl.innerText = `🎉 Congratulations! You won ₹${userWonAmount.toFixed(2)}!`;
            winEl.classList.remove('d-none');
        } else {
            winEl.classList.add('d-none');
        }

        const modal = document.getElementById('podiumModal');
        modal.style.display = 'flex';

        // Auto close after 6 seconds
        let seconds = 6;
        const countdownEl = document.getElementById('podiumCountdown');
        countdownEl.innerText = seconds;

        if (window.podiumInterval) clearInterval(window.podiumInterval);
        window.podiumInterval = setInterval(() => {
            seconds--;
            countdownEl.innerText = seconds;
            if (seconds <= 0) {
                clearInterval(window.podiumInterval);
                closePodiumModal();
            }
        }, 1000);
    }

    function closePodiumModal() {
        if (window.podiumInterval) clearInterval(window.podiumInterval);
        document.getElementById('podiumModal').style.display = 'none';
    }

    /* =========================================================================
     * TABS SWITCHER & TOAST
     * ========================================================================= */
    function switchTab(tab) {
        document.getElementById('tabContentMyBets').classList.add('d-none');
        document.getElementById('tabContentHistory').classList.add('d-none');
        document.getElementById('tabContentGateStats').classList.add('d-none');

        document.getElementById('tabBtnMyBets').classList.remove('active');
        document.getElementById('tabBtnHistory').classList.remove('active');
        document.getElementById('tabBtnGateStats').classList.remove('active');

        if (tab === 'myBets') {
            document.getElementById('tabContentMyBets').classList.remove('d-none');
            document.getElementById('tabBtnMyBets').classList.add('active');
        } else if (tab === 'history') {
            document.getElementById('tabContentHistory').classList.remove('d-none');
            document.getElementById('tabBtnHistory').classList.add('active');
        } else if (tab === 'gateStats') {
            document.getElementById('tabContentGateStats').classList.remove('d-none');
            document.getElementById('tabBtnGateStats').classList.add('active');
        }
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} shadow-lg position-fixed top-0 start-50 translate-middle-x mt-3 py-2 px-4 rounded-pill fw-bold`;
        toast.style.zIndex = '9999';
        toast.innerHTML = `<i class="fa-solid fa-circle-info me-2"></i> ${message}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3500);
    }
</script>
@endsection
