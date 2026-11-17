@extends('layouts.app')

@section('title', 'Gaming Lobby - rupeerush | Casino, Crash, Lottery & Sports')

@section('styles')
<!-- Swiper 11 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    /* ========================================================
       LOTUS365 SUITE: TOP SEARCH BAR
       ======================================================== */
    .lotus-search-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        position: relative;
    }
    .lotus-search-box {
        position: relative;
        width: 100%;
        display: flex;
        align-items: center;
        background: #0f172a;
        border: 1px solid rgba(245, 158, 11, 0.35);
        border-radius: 12px;
        padding: 2px 14px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.45);
        transition: all 0.25s ease;
    }
    .lotus-search-box:focus-within {
        border-color: #fbbf24;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25), 0 6px 18px rgba(0, 0, 0, 0.6);
        background: #131c31;
    }
    .lotus-search-box .search-icon {
        color: #f59e0b;
        font-size: 0.95rem;
        margin-right: 10px;
        flex-shrink: 0;
    }
    .lotus-search-box input {
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        color: #ffffff !important;
        font-size: 0.88rem;
        font-weight: 600;
        padding: 8px 0;
        width: 100%;
    }
    .lotus-search-box input::placeholder {
        color: #94a3b8;
        font-size: 0.84rem;
        font-weight: 500;
    }
    .lotus-search-box .search-clear-btn {
        background: none;
        border: none;
        color: #94a3b8;
        padding: 0 4px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
    }
    .lotus-search-box .search-clear-btn:hover {
        color: #ffffff;
    }

    /* ========================================================
       HERO BANNER SWIPER (3 SLIDES ON DESKTOP, 1 ON MOBILE)
       ======================================================== */
    .lobby-hero-swiper-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        position: relative;
    }
    .lobbyHeroSwiper {
        width: 100%;
        overflow: hidden;
        position: relative;
        padding-bottom: 22px;
    }
    .lobbyHeroSwiper .swiper-slide {
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-radius: 12px;
        aspect-ratio: 2.7 / 1;
        background: #090e1a;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.45);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .lobbyHeroSwiper .swiper-slide:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.65);
    }
    .hero-slide-link {
        display: block;
        width: 100%;
        height: 100%;
        position: relative;
        overflow: hidden;
        text-decoration: none;
    }
    .hero-banner-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border-radius: 12px;
        transition: transform 0.45s ease;
    }
    .hero-slide-link:hover .hero-banner-img {
        transform: scale(1.02);
    }
    .lobby-swiper-pagination {
        bottom: 4px !important;
        z-index: 10;
    }
    .lobby-swiper-pagination .swiper-pagination-bullet {
        width: 8px;
        height: 8px;
        background: #ffffff;
        opacity: 0.5;
        margin: 0 4px !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .lobby-swiper-pagination .swiper-pagination-bullet-active {
        width: 26px;
        border-radius: 6px;
        background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
        opacity: 1 !important;
        box-shadow: 0 0 10px rgba(245, 158, 11, 0.85);
    }
    .lobby-swiper-prev,
    .lobby-swiper-next {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(9, 14, 26, 0.85);
        border: 1px solid rgba(245, 158, 11, 0.45);
        color: #fbbf24;
        backdrop-filter: blur(8px);
        transition: all 0.22s ease;
        opacity: 0.9;
    }
    .lobby-swiper-prev:hover,
    .lobby-swiper-next:hover {
        background: #fbbf24;
        color: #070b14;
        opacity: 1;
        transform: scale(1.08);
        box-shadow: 0 0 14px rgba(245, 158, 11, 0.6);
    }
    .lobby-swiper-prev::after,
    .lobby-swiper-next::after {
        font-size: 13px;
        font-weight: 900;
    }

    /* ========================================================
       GAME FILTERS STRIP (LOTUS365 SPORT & CASINO TABS)
       ======================================================== */
    .lotus-filters-strip {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
        padding: 2px 0 6px;
    }
    .lotus-filters-strip::-webkit-scrollbar {
        display: none;
    }
    .lotus-filter-tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 0.88rem;
        color: #ffffff;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        border: 1px solid rgba(255, 255, 255, 0.16);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        transition: all 0.22s ease;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
    }
    .lotus-filter-tab:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.5);
        filter: brightness(1.15);
        color: #ffffff;
    }
    .lotus-filter-tab.active {
        box-shadow: 0 0 0 2px #ffffff, 0 6px 18px rgba(0, 0, 0, 0.7);
        filter: brightness(1.25);
    }
    .lotus-tab-all { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); }
    .lotus-tab-cricket { background: linear-gradient(135deg, #451a1a 0%, #701a1a 100%); }
    .lotus-tab-casino { background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%); }
    .lotus-tab-crash { background: linear-gradient(135deg, #be123c 0%, #9f1239 100%); }
    .lotus-tab-lottery { background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%); }
    .lotus-tab-horse { background: linear-gradient(135deg, #c2410c 0%, #ea580c 100%); }

    /* ========================================================
       LOTUS365 4-COLUMN WIDESCREEN BANNER GAME CARDS (4 CARDS PER ROW)
       ======================================================== */
    #gamesCatalogGrid {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 5px !important;
    }
    @media (min-width: 768px) {
        #gamesCatalogGrid {
            gap: 10px !important;
        }
    }
    @media (min-width: 1200px) {
        #gamesCatalogGrid {
            gap: 14px !important;
        }
    }
    .game-card-wrapper {
        width: 100% !important;
        min-width: 0 !important;
        transition: transform 0.25s ease, opacity 0.2s ease;
    }
    .lotus-banner-game-card {
        display: block;
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        text-decoration: none;
        aspect-ratio: 1.35 / 1;
        min-height: 72px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.45);
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        background-size: cover;
        background-position: center;
    }
    @media (min-width: 768px) {
        .lotus-banner-game-card {
            border-radius: 10px;
            min-height: 105px;
            aspect-ratio: 1.55 / 1;
        }
    }
    .lotus-banner-game-card:hover {
        transform: translateY(-3px) scale(1.02);
        border-color: #fbbf24;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.65), 0 0 16px rgba(245, 158, 11, 0.35);
    }
    .lotus-banner-game-card:active {
        transform: scale(0.97);
    }
    .lotus-corner-badge {
        position: absolute;
        top: 4px;
        right: 4px;
        font-size: 0.58rem;
        font-weight: 900;
        letter-spacing: 0.4px;
        padding: 1px 5px;
        border-radius: 3px;
        z-index: 3;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.5);
    }
    @media (min-width: 768px) {
        .lotus-corner-badge {
            top: 6px;
            right: 6px;
            font-size: 0.62rem;
            padding: 2px 7px;
        }
    }
    .lotus-card-art {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
        overflow: hidden;
    }
    .lotus-card-art::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.05) 0%, rgba(9, 14, 26, 0.88) 100%);
        pointer-events: none;
    }
    .lotus-card-3d-icon {
        font-size: 1.8rem;
        opacity: 0.88;
        transition: transform 0.3s ease, opacity 0.3s ease;
        filter: drop-shadow(0 3px 8px rgba(0, 0, 0, 0.6));
    }
    @media (min-width: 768px) {
        .lotus-card-3d-icon {
            font-size: 2.8rem;
        }
    }
    .lotus-banner-game-card:hover .lotus-card-3d-icon {
        transform: scale(1.15) rotate(4deg);
        opacity: 1;
    }
    .lotus-card-banner-strip {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 4px 6px 5px;
        background: linear-gradient(to top, rgba(9, 14, 26, 0.98) 0%, rgba(9, 14, 26, 0.72) 75%, transparent 100%);
        z-index: 2;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 4px;
    }
    @media (min-width: 768px) {
        .lotus-card-banner-strip {
            padding: 6px 10px 8px;
        }
    }
    .lotus-card-title-text {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 0.72rem;
        color: #ffffff;
        letter-spacing: -0.2px;
        line-height: 1.15;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.9);
    }
    @media (min-width: 768px) {
        .lotus-card-title-text {
            font-size: 0.92rem;
        }
    }
    .lotus-card-payout-text {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.58rem;
        font-weight: 800;
        color: #fbbf24;
        white-space: nowrap;
        background: rgba(0, 0, 0, 0.5);
        padding: 1px 4px;
        border-radius: 3px;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }
    @media (min-width: 768px) {
        .lotus-card-payout-text {
            font-size: 0.68rem;
            padding: 1px 6px;
        }
    }

    /* ========================================================
       LOTUS365 FEATURED 2 WIDE CARDS (CASINO LOBBY & SPORTS BOOK)
       ======================================================== */
    .lotus-featured-cards-row {
        width: 100% !important;
    }
    .lotus-feature-wide-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        border-radius: 8px;
        position: relative;
        overflow: hidden;
        height: 62px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    @media (min-width: 768px) {
        .lotus-feature-wide-card {
            height: 80px;
            padding: 12px 18px;
            border-radius: 10px;
        }
    }
    .lotus-feature-wide-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
    }
    .card-casino-lobby {
        background: linear-gradient(135deg, #701a75 0%, #4a044e 50%, #2e1065 100%);
    }
    .card-sports-book {
        background: linear-gradient(135deg, #065f46 0%, #064e3b 50%, #022c22 100%);
    }
    .feat-card-left {
        display: flex;
        flex-direction: column;
        justify-content: center;
        z-index: 2;
    }
    .feat-badge-tag {
        font-size: 0.6rem;
        font-weight: 900;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        color: #facc15;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 2px;
    }
    .feat-main-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 0.92rem;
        color: #ffffff;
        line-height: 1.1;
    }
    @media (min-width: 768px) {
        .feat-main-title {
            font-size: 1.15rem;
        }
    }
    .feat-sub-title {
        font-size: 0.66rem;
        color: rgba(255, 255, 255, 0.7);
        font-weight: 500;
    }
    .feat-art-visual {
        font-size: 1.6rem;
        opacity: 0.9;
        z-index: 2;
        filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.5));
    }
    @media (min-width: 768px) {
        .feat-art-visual {
            font-size: 2.2rem;
        }
    }

    /* ========================================================
       LOTUS365 NEW LAUNCH SECTION
       ======================================================== */
    .lotus-new-launch-section {
        position: relative;
        width: 100% !important;
        margin: 12px 0 16px;
    }
    .lotus-new-launch-header {
        background: #004b98;
        color: #ffffff;
        font-family: 'Outfit', sans-serif;
        font-weight: 900;
        font-size: 0.96rem;
        letter-spacing: 1px;
        text-align: center;
        padding: 5px 12px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
        margin-bottom: 8px;
    }
    .lotus-slot-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        aspect-ratio: 1 / 1.38;
        border-radius: 8px;
        overflow: hidden;
        padding: 8px 6px 10px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.5);
        transition: transform 0.22s ease;
        text-align: center;
    }
    .lotus-slot-card:hover {
        transform: translateY(-3px) scale(1.02);
    }
    .slot-card-garuda {
        background: radial-gradient(circle at top, #78350f 0%, #451a03 50%, #1c0a00 100%);
    }
    .slot-card-rooster {
        background: radial-gradient(circle at top, #881337 0%, #4c0519 50%, #1a0208 100%);
    }
    .slot-card-moneyheist {
        background: radial-gradient(circle at top, #14532d 0%, #064e3b 50%, #022c22 100%);
    }
    .slot-badge-top {
        position: absolute;
        top: 4px;
        left: 4px;
        background: #f59e0b;
        color: #000000;
        font-weight: 900;
        font-size: 0.58rem;
        padding: 1px 5px;
        border-radius: 3px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
    }
    .slot-art-graphic {
        font-size: 2.1rem;
        margin-top: 8px;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.7));
    }
    @media (min-width: 768px) {
        .slot-art-graphic {
            font-size: 3.2rem;
        }
    }
    .slot-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 900;
        font-size: 0.74rem;
        line-height: 1.15;
        color: #ffffff;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.9);
        letter-spacing: -0.2px;
    }
    @media (min-width: 768px) {
        .slot-title {
            font-size: 0.95rem;
        }
    }
    .floating-casino-chip {
        position: absolute;
        right: -4px;
        bottom: -10px;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: radial-gradient(circle, #dc2626 0%, #991b1b 100%);
        border: 2px dashed #ffffff;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 1.25rem;
        z-index: 5;
        pointer-events: none;
    }

    /* ========================================================
       LOTUS365 IN PLAY SECTION
       ======================================================== */
    .lotus-in-play-bar {
        background: #008040;
        padding: 7px 14px;
        border-radius: 6px;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        margin-bottom: 8px;
    }
    .in-play-icon-circle {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #ffffff;
        color: #008040;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        padding-left: 2px;
    }
    .in-play-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 1.05rem;
        letter-spacing: -0.2px;
    }
    .open-bets-pill {
        background: #f59e0b;
        color: #ffffff;
        font-weight: 800;
        font-size: 0.74rem;
        padding: 3px 12px;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
    }

    /* ========================================================
       LOTUS365 CRICKET LIVE SCORES EXCHANGE SECTION
       ======================================================== */
    .lotus-cricket-section {
        width: 100% !important;
        margin-top: 8px;
    }
    .lotus-cricket-box {
        background: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.25);
    }
    .lotus-cricket-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 18px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        flex-wrap: wrap;
        gap: 8px;
    }
    .lotus-cricket-main-title {
        font-size: 1.45rem;
        font-weight: 900;
        color: #0f172a;
        letter-spacing: -0.4px;
        font-family: 'Outfit', sans-serif;
    }
    .lotus-cricket-badge-pill {
        display: inline-flex;
        align-items: center;
        font-size: 0.72rem;
        font-weight: 800;
        color: #0d9488;
        background: #ffffff;
        border: 1.5px solid #0d9488;
        padding: 2px 10px;
        border-radius: 50px;
        letter-spacing: 0.3px;
    }
    .lotus-betting-notice {
        font-size: 0.8rem;
        color: #475569;
        font-weight: 600;
    }
    .lotus-cricket-table-container {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .lotus-cricket-exchange-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
        min-width: 660px;
    }
    .lotus-cricket-exchange-table thead th {
        background: #f1f5f9;
        color: #0f172a;
        font-weight: 800;
        font-size: 0.78rem;
        padding: 8px 12px;
        border-bottom: 2px solid #e2e8f0;
    }
    .th-match {
        width: 48%;
        text-align: left;
    }
    .th-odds {
        width: 17%;
        font-weight: 900 !important;
        font-size: 0.88rem !important;
    }
    .lotus-match-row {
        border-bottom: 1px solid #e2e8f0;
        transition: background 0.15s ease;
    }
    .lotus-match-row:hover {
        background: #f8fafc;
    }
    .td-match-info {
        padding: 10px 14px;
        vertical-align: middle;
    }
    .lotus-match-link {
        font-size: 0.92rem;
        font-weight: 800;
        color: #0f172a;
        text-decoration: none;
    }
    .lotus-match-link:hover {
        color: #0284c7;
    }
    .lotus-match-subscore {
        font-size: 0.76rem;
        margin-top: 2px;
        font-family: 'JetBrains Mono', monospace;
    }
    .btn-pin-match {
        background: none;
        border: none;
        color: #94a3b8;
        padding: 2px 4px;
        cursor: pointer;
        font-size: 0.82rem;
        transition: color 0.2s;
    }
    .btn-pin-match:hover, .btn-pin-match.pinned {
        color: #f59e0b;
    }
    .lotus-live-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 8px #22c55e;
        display: inline-block;
        animation: pulseGreen 1.4s infinite;
    }
    @keyframes pulseGreen {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.3); opacity: 0.6; }
    }
    .lotus-upcoming-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #0284c7;
        display: inline-block;
    }
    .td-odds-cell {
        width: 8.5%;
        padding: 4px !important;
        vertical-align: middle;
        text-align: center;
    }
    .lotus-box-back,
    .lotus-box-lay {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 42px;
        border-radius: 6px;
        font-weight: 800;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.88rem;
        text-decoration: none;
        transition: transform 0.15s ease, filter 0.15s ease;
    }
    .lotus-box-back {
        background: #a5f3fc;
        color: #0e7490;
        border: 1px solid #67e8f9;
    }
    .lotus-box-back:hover {
        background: #06b6d4;
        color: #ffffff;
        transform: scale(1.04);
    }
    .lotus-box-lay {
        background: #fbcfe8;
        color: #be185d;
        border: 1px solid #f472b6;
    }
    .lotus-box-lay:hover {
        background: #ec4899;
        color: #ffffff;
        transform: scale(1.04);
    }
    .lotus-box-dash {
        background: #f1f5f9;
        color: #94a3b8;
        border: 1px solid #e2e8f0;
        pointer-events: none;
    }

    .lotus-content-wrapper {
        width: 100%;
        max-width: 1420px;
        margin: 0 auto;
    }

    @media (min-width: 768px) {
        .lobbyHeroSwiper .swiper-slide {
            aspect-ratio: 2.8 / 1 !important;
            min-height: 155px !important;
            max-height: 200px !important;
        }
        .lotus-filters-strip {
            display: flex;
            gap: 8px;
            overflow-x: visible;
            padding: 2px 0 10px;
        }
        .lotus-filter-tab {
            flex: 1 1 0;
            padding: 9px 12px;
            font-size: 0.86rem;
            font-weight: 800;
            border-radius: 8px;
            justify-content: center;
            white-space: nowrap;
        }
        #gamesCatalogGrid {
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 12px !important;
            margin-bottom: 22px !important;
        }
        .lotus-banner-game-card {
            border-radius: 10px;
            min-height: 125px !important;
            max-height: 165px !important;
            aspect-ratio: 1.85 / 1 !important;
        }
        .lotus-card-3d-icon {
            font-size: 3.2rem !important;
        }
        .lotus-card-title-text {
            font-size: 1rem !important;
            font-weight: 900 !important;
            letter-spacing: 0.3px !important;
        }
        .lotus-card-banner-strip {
            padding: 8px 12px !important;
        }
        .lotus-cricket-box {
            border-radius: 10px;
        }
        .lotus-cricket-bar {
            padding: 12px 20px;
        }
    }

    /* ========================================================
       RESPONSIVE ADJUSTMENTS (< 768px)
       ======================================================== */
    @media (max-width: 767.98px) {
        .lobbyHeroSwiper .swiper-slide {
            aspect-ratio: 2.7 / 1;
            min-height: 125px;
            max-height: 155px;
        }
        .lotus-filters-strip {
            gap: 6px;
            padding: 2px 0 6px;
        }
        .lotus-filter-tab {
            padding: 7px 12px;
            font-size: 0.78rem;
            border-radius: 7px;
            gap: 6px;
        }
        .lotus-banner-game-card {
            min-height: 85px;
            border-radius: 10px;
        }
        .lotus-card-3d-icon {
            font-size: 2rem;
        }
        .lotus-card-title-text {
            font-size: 0.76rem;
        }
        .lotus-card-payout-text {
            font-size: 0.62rem;
            padding: 1px 4px;
        }
        .lotus-cricket-main-title {
            font-size: 1.15rem;
        }
        .lotus-cricket-bar {
            padding: 8px 12px;
        }
        .lotus-box-back,
        .lotus-box-lay {
            height: 36px;
            font-size: 0.78rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-2 px-md-4 px-xl-5 py-2 py-md-3">
<div class="lotus-content-wrapper">

    <!-- Hidden Search Input for header search synchronization -->
    <input type="hidden" id="gameSearchInput" value="">

    <!-- 1. HERO BANNER SLIDER (3 IMAGES SIDE-BY-SIDE ON DESKTOP, 1 ON MOBILE) -->
    <div class="lobby-hero-swiper-wrapper mb-2 mb-md-3">
        <div class="swiper lobbyHeroSwiper">
            <div class="swiper-wrapper">
                @foreach($setting->getHeroBannersList() as $banner)
                    <div class="swiper-slide">
                        <a href="{{ $banner['link'] }}" class="hero-slide-link" title="{{ $banner['title'] }}">
                            <img src="{{ $banner['image_url'] }}" alt="{{ $banner['title'] }}" class="hero-banner-img" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                        </a>
                    </div>
                @endforeach
            </div>
            <!-- Pagination Dots -->
            <div class="swiper-pagination lobby-swiper-pagination"></div>
            <!-- Navigation Arrows (Desktop) -->
            <div class="swiper-button-prev lobby-swiper-prev d-none d-md-flex"></div>
            <div class="swiper-button-next lobby-swiper-next d-none d-md-flex"></div>
        </div>
    </div>

    <!-- 2. GAME FILTERS STRIP (REAL ACTIVE GAME CATEGORIES) -->
    <div class="lotus-filters-strip mb-2 mb-md-3">
        <button type="button" class="lotus-filter-tab lotus-tab-all active" data-filter="all" onclick="selectLotusFilter('all', this)">
            <i class="fa-solid fa-border-all"></i>
            <span>All Games</span>
        </button>
        <button type="button" class="lotus-filter-tab lotus-tab-cricket" data-filter="cricket" onclick="selectLotusFilter('cricket', this)">
            <i class="fa-solid fa-baseball-bat-ball"></i>
            <span>Cricket</span>
        </button>
        <button type="button" class="lotus-filter-tab lotus-tab-casino" data-filter="casino" onclick="selectLotusFilter('casino', this)">
            <i class="fa-solid fa-diamond"></i>
            <span>Live Casino</span>
        </button>
        <button type="button" class="lotus-filter-tab lotus-tab-crash" data-filter="crash" onclick="selectLotusFilter('crash', this)">
            <i class="fa-solid fa-plane-departure"></i>
            <span>Crash &amp; Arcade</span>
        </button>
        <button type="button" class="lotus-filter-tab lotus-tab-lottery" data-filter="lottery" onclick="selectLotusFilter('lottery', this)">
            <i class="fa-solid fa-dice"></i>
            <span>2 Digit &amp; WinGo</span>
        </button>
        <button type="button" class="lotus-filter-tab lotus-tab-horse" data-filter="horse_racing" onclick="selectLotusFilter('horse_racing', this)">
            <i class="fa-solid fa-horse-head"></i>
            <span>Horse Racing</span>
        </button>
    </div>

    <!-- 3. PROJECT GAME CARDS (11 REAL ACTIVE GAMES IN ORDER - 4 COLUMNS ON ALL SCREENS) -->
    @php
        $orderMap = [
            'lottery' => 1,
            'wingo' => 2,
            'andar_bahar' => 3,
            'aviator' => 4,
            'mines' => 5,
            'vortex' => 6,
            'teen_patti' => 7,
            'dragon_tiger' => 8,
            'roulette' => 9,
            'horse_racing' => 10,
            'cricket' => 11,
        ];
        $sortedGames = collect($games)->sortBy(fn($g) => $orderMap[$g['id'] ?? ''] ?? 99);
    @endphp
    <div id="gamesCatalogGrid" class="mb-3 mb-md-4">
        @foreach($sortedGames as $game)
            @php
                $gId = $game['id'] ?? '';
                $cleanTitle = match($gId) {
                    'lottery' => '2 Digit Slot',
                    'wingo' => 'WinGo Color',
                    'andar_bahar' => 'Andar Bahar',
                    'aviator' => 'Aviator',
                    'mines' => 'Mines',
                    'vortex' => 'Vortex Wheel',
                    'teen_patti' => 'Teen Patti',
                    'dragon_tiger' => 'Dragon Tiger',
                    'roulette' => 'Roulette',
                    'horse_racing' => 'Horse Racing',
                    'cricket' => 'Cricket Sports',
                    default => $game['title'],
                };
                $shortBadge = match($gId) {
                    'lottery' => '90X',
                    'wingo' => '9X',
                    'andar_bahar' => '1.95X',
                    'teen_patti' => '40X',
                    'dragon_tiger' => '50X',
                    'roulette' => '36X',
                    'aviator' => '1000X',
                    'mines' => 'ARCADE',
                    'vortex' => '200X',
                    'horse_racing' => 'DERBY',
                    'cricket' => 'LIVE',
                    default => 'HOT',
                };
                $artIcon = match($gId) {
                    'lottery' => 'fa-solid fa-dice',
                    'wingo' => 'fa-solid fa-gamepad',
                    'andar_bahar' => 'fa-solid fa-diamond',
                    'aviator' => 'fa-solid fa-plane-departure',
                    'mines' => 'fa-solid fa-bomb',
                    'vortex' => 'fa-solid fa-arrows-spin',
                    'teen_patti' => 'fa-solid fa-heart',
                    'dragon_tiger' => 'fa-solid fa-dragon',
                    'roulette' => 'fa-solid fa-dharmachakra',
                    'horse_racing' => 'fa-solid fa-horse-head',
                    'cricket' => 'fa-solid fa-baseball-bat-ball',
                    default => 'fa-solid ' . ($game['icon'] ?? 'fa-gamepad'),
                };
            @endphp
            <div class="game-card-wrapper" data-category="{{ $game['category'] }}" data-id="{{ $game['id'] }}" data-name="{{ strtolower($cleanTitle) }}">
                <a href="{{ $game['route'] }}"
                   class="lotus-banner-game-card"
                   style="background: {{ $game['bg_gradient'] ?? 'linear-gradient(135deg, #111a2d 0%, #0a101d 100%)' }};">

                    <!-- Top Corner Badge -->
                    <span class="lotus-corner-badge {{ $game['badge_class'] ?? 'bg-danger' }}">
                        {{ $shortBadge }}
                    </span>

                    <!-- Card Body Artwork / 3D Icon -->
                    <div class="lotus-card-art">
                        <div class="lotus-card-3d-icon" style="color: {{ $game['accent_color'] ?? '#fbbf24' }};">
                            <i class="{{ $artIcon }}"></i>
                        </div>
                    </div>

                    <!-- Bottom Overlay Banner (Title + Multiplier) -->
                    <div class="lotus-card-banner-strip">
                        <div class="lotus-card-title-text text-truncate">{{ $cleanTitle }}</div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <!-- 4. CRICKET LIVE SCORES SECTION (POSITIONED DIRECTLY BELOW GAME CARDS) -->
    <div class="lotus-cricket-section mb-4" id="cricketSection">
        <div class="lotus-cricket-box">
            <!-- Header Row: Title, Pills, Notice -->
            <div class="lotus-cricket-bar">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h3 class="lotus-cricket-main-title mb-0">Cricket</h3>
                    <span class="lotus-cricket-badge-pill"><i class="fa-solid fa-plus me-1"></i>LIVE</span>
                    <span class="lotus-cricket-badge-pill"><i class="fa-solid fa-plus me-1"></i>VIRTUAL</span>
                    <span class="lotus-cricket-badge-pill"><i class="fa-solid fa-plus me-1"></i>PREMIUM</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="lotus-betting-notice">Betting from 1min before start</span>
                    <a href="{{ route('sports.cricket') }}" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-0 fw-bold d-none d-sm-inline-flex" style="font-size: 0.72rem;">
                        View All
                    </a>
                </div>
            </div>

            <!-- Lotus365 Match Odds Table -->
            <div class="lotus-cricket-table-container">
                <table class="table lotus-cricket-exchange-table mb-0">
                    <thead>
                        <tr>
                            <th class="th-match">MATCH</th>
                            <th colspan="2" class="th-odds text-center">1</th>
                            <th colspan="2" class="th-odds text-center">X</th>
                            <th colspan="2" class="th-odds text-center">2</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cricketLiveMatches as $match)
                            @php
                                $matchWinnerMkt = collect($match['markets'] ?? [])->firstWhere('market_type', 'match_winner');
                                $runners = $matchWinnerMkt['runners'] ?? [];
                                $runnerA = $runners[0] ?? null;
                                $runnerB = $runners[1] ?? null;
                                $matchUrl = route('sports.cricket.match', $match['id']);
                                $isLive = !empty($match['is_live']) || ($match['status'] ?? '') === 'live';
                            @endphp
                            <tr class="lotus-match-row">
                                <!-- Match Title & Pin & Live Indicator -->
                                <td class="td-match-info">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                                            <span class="text-danger fw-bold font-monospace" style="font-size: 0.72rem; white-space: nowrap;">
                                                {{ !empty($match['start_time']) ? \Carbon\Carbon::parse($match['start_time'])->format('d/m/Y H:i') : date('d/m/Y H:i') }}
                                            </span>
                                            @if($isLive)
                                                <span class="lotus-live-dot flex-shrink-0" title="Live Now"></span>
                                            @else
                                                <span class="lotus-upcoming-dot flex-shrink-0" title="Upcoming"></span>
                                            @endif
                                            <a href="{{ $matchUrl }}" class="lotus-match-link text-truncate fw-bold">
                                                <span>{{ $match['series_name'] ?? ($match['team_a'] . ' vs ' . $match['team_b']) }}</span>
                                            </a>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            <button type="button" class="btn-pin-match" onclick="togglePinMatch(this)" title="Pin Match">
                                                <i class="fa-solid fa-thumbtack"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @if(!empty($match['team_a']) && !empty($match['team_b']))
                                        <div class="d-flex align-items-center gap-2 mt-1 small text-muted text-truncate" style="font-size: 0.74rem;">
                                            <span class="text-dark fw-semibold text-truncate">{{ $match['team_a'] }} vs {{ $match['team_b'] }}</span>
                                            @if(!empty($match['score_team_a']))
                                                <span class="text-primary fw-bold">({{ $match['score_team_a'] }})</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <!-- 1 Back / Lay -->
                                <td class="td-odds-cell">
                                    <a href="{{ $matchUrl }}" class="lotus-box-back" title="Back {{ $match['team_a'] }}">
                                        {{ isset($runnerA['back_odds']) ? number_format($runnerA['back_odds'], 2) : '-' }}
                                    </a>
                                </td>
                                <td class="td-odds-cell">
                                    <a href="{{ $matchUrl }}" class="lotus-box-lay" title="Lay {{ $match['team_a'] }}">
                                        {{ isset($runnerA['lay_odds']) ? number_format($runnerA['lay_odds'], 2) : '-' }}
                                    </a>
                                </td>

                                <!-- X Back / Lay -->
                                <td class="td-odds-cell">
                                    <a href="{{ $matchUrl }}" class="lotus-box-back lotus-box-dash">
                                        -
                                    </a>
                                </td>
                                <td class="td-odds-cell">
                                    <a href="{{ $matchUrl }}" class="lotus-box-lay lotus-box-dash">
                                        -
                                    </a>
                                </td>

                                <!-- 2 Back / Lay -->
                                <td class="td-odds-cell">
                                    <a href="{{ $matchUrl }}" class="lotus-box-back" title="Back {{ $match['team_b'] }}">
                                        {{ isset($runnerB['back_odds']) ? number_format($runnerB['back_odds'], 2) : '-' }}
                                    </a>
                                </td>
                                <td class="td-odds-cell">
                                    <a href="{{ $matchUrl }}" class="lotus-box-lay" title="Lay {{ $match['team_b'] }}">
                                        {{ isset($runnerB['lay_odds']) ? number_format($runnerB['lay_odds'], 2) : '-' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted fw-bold">
                                    No live cricket matches at this moment. Check back soon!
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

<!-- Modal for Starting Next Notice -->
<div class="modal fade" id="nextGameModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="p-4 text-white text-center" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%);">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-white text-warning mb-2" style="width: 56px; height: 56px; font-size: 1.8rem;">
                    <i class="fa-solid fa-gamepad"></i>
                </div>
                <h4 class="fw-bold mb-1" id="nextGameTitle">Game Arena</h4>
                <div class="badge bg-black bg-opacity-30 rounded-pill px-3 py-1">Arena in Active Development</div>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="text-muted mb-3">
                    We are currently building this game arena with provably fair math, live multipliers, and instant UPI payouts!
                </p>
                <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Got It!</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Swiper 11 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    // Initialize Hero Banner Carousel Swiper: 3 slides per view on desktop!
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swiper !== 'undefined') {
            new Swiper('.lobbyHeroSwiper', {
                loop: true,
                speed: 650,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },
                slidesPerView: 1,
                spaceBetween: 8,
                breakpoints: {
                    576: {
                        slidesPerView: 2,
                        spaceBetween: 12,
                    },
                    992: {
                        slidesPerView: 3,
                        spaceBetween: 16,
                    },
                },
                pagination: {
                    el: '.lobby-swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.lobby-swiper-next',
                    prevEl: '.lobby-swiper-prev',
                },
                grabCursor: true,
                keyboard: {
                    enabled: true,
                },
            });
        }
    });

    let currentFilter = 'all';

    function selectLotusFilter(filterKey, btn) {
        currentFilter = filterKey;
        document.querySelectorAll('.lotus-filter-tab').forEach(b => b.classList.remove('active'));
        if (btn) {
            btn.classList.add('active');
        }

        // If cricket clicked, scroll smoothly to cricket section
        if (filterKey === 'cricket') {
            const sec = document.getElementById('cricketSection');
            if (sec) {
                sec.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        filterGamesCatalog();
    }

    function filterGamesCatalog() {
        const input = document.getElementById('gameSearchInput');
        const query = (input?.value || '').trim().toLowerCase();
        const clearBtn = document.getElementById('searchClearBtn');
        if (clearBtn) {
            if (query.length > 0) {
                clearBtn.classList.remove('d-none');
            } else {
                clearBtn.classList.add('d-none');
            }
        }

        const cards = document.querySelectorAll('.game-card-wrapper');
        cards.forEach(card => {
            const cardCat = card.getAttribute('data-category') || '';
            const cardId = card.getAttribute('data-id') || '';
            const cardName = card.getAttribute('data-name') || '';

            let matchesFilter = false;
            if (query) {
                matchesFilter = true;
            } else if (currentFilter === 'all') {
                matchesFilter = true;
            } else if (currentFilter === 'cricket') {
                matchesFilter = (cardId === 'cricket');
            } else if (currentFilter === 'casino') {
                matchesFilter = (cardCat === 'casino');
            } else if (currentFilter === 'crash') {
                matchesFilter = (cardCat === 'crash');
            } else if (currentFilter === 'lottery') {
                matchesFilter = (cardCat === 'lottery' || cardId === 'lottery' || cardId === 'wingo');
            } else if (currentFilter === 'horse_racing') {
                matchesFilter = (cardId === 'horse_racing');
            } else {
                matchesFilter = (cardCat === currentFilter);
            }

            const matchesQuery = (!query || cardName.includes(query));

            if (matchesFilter && matchesQuery) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Run on page load so initial desktop & mobile views show the 8 featured cards
    document.addEventListener('DOMContentLoaded', function () {
        filterGamesCatalog();
    });

    function clearSearchInput() {
        const input = document.getElementById('gameSearchInput');
        const clearBtn = document.getElementById('searchClearBtn');
        if (input) {
            input.value = '';
            input.focus();
            filterGamesCatalog();
        }
        if (clearBtn) {
            clearBtn.classList.add('d-none');
        }
    }

    function togglePinMatch(btn) {
        btn.classList.toggle('pinned');
    }

    function showNextGameNotice(title) {
        document.getElementById('nextGameTitle').textContent = title;
        const modal = new bootstrap.Modal(document.getElementById('nextGameModal'));
        modal.show();
    }
</script>
@endsection
