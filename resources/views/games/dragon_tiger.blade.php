@extends('layouts.app')

@section('title', 'Dragon Tiger Live Casino - ' . ($setting->site_name ?? 'rupeerush'))

@section('styles')
<style>
    /* ----------------------------------------------------
       DRAGON TIGER LIVE CASINO SUITE
       Royal Red Dragon vs Electric Azure Tiger
       ---------------------------------------------------- */
    :root {
        --dt-dragon-red: #ef4444;
        --dt-dragon-glow: rgba(239, 68, 68, 0.55);
        --dt-dragon-gradient: linear-gradient(135deg, #dc2626 0%, #7f1d1d 100%);
        --dt-tiger-blue: #3b82f6;
        --dt-tiger-glow: rgba(59, 130, 246, 0.55);
        --dt-tiger-gradient: linear-gradient(135deg, #2563eb 0%, #1e3a8a 100%);
        --dt-tie-green: #10b981;
        --dt-tie-glow: rgba(16, 185, 129, 0.55);
        --dt-gold: #f59e0b;
        --dt-gold-light: #fef08a;
    }

    body {
        background-color: #030712;
        color: #f8fafc;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .dt-arena-container {
        max-width: 1240px;
        margin: 0 auto;
        padding-bottom: 60px;
    }

    /* Top Navigation Bar */
    .dt-topbar {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(3, 7, 18, 0.98) 100%);
        border: 1px solid rgba(245, 158, 11, 0.25);
        border-radius: 18px;
        backdrop-filter: blur(12px);
        padding: 10px 18px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7);
    }

    .dt-round-pill {
        background: rgba(0, 0, 0, 0.6);
        border: 1px solid rgba(245, 158, 11, 0.4);
        border-radius: 50px;
        padding: 4px 14px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.85rem;
        color: var(--dt-gold-light);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Velvet Casino Battle Table */
    .dt-felt-table {
        background: radial-gradient(ellipse at 50% 30%, #1e1b4b 0%, #0f172a 55%, #030712 100%);
        border: 5px solid #2e1065;
        outline: 2px solid var(--dt-gold);
        border-radius: 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.85), inset 0 0 80px rgba(0, 0, 0, 0.8);
        padding: 14px 18px 18px;
        margin-top: 10px;
    }

    .dt-felt-table::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: radial-gradient(circle at center, rgba(255, 255, 255, 0.03) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Dealer Stage & Central Timer */
    .dt-dealer-stage {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 5;
        margin-bottom: 12px;
    }

    .dt-timer-wrap {
        position: relative;
        width: 76px;
        height: 76px;
        margin: 4px 0;
    }

    .dt-timer-svg {
        transform: rotate(-90deg);
        width: 76px;
        height: 76px;
    }

    .dt-timer-circle-bg {
        fill: rgba(0, 0, 0, 0.55);
        stroke: rgba(255, 255, 255, 0.12);
        stroke-width: 7;
    }

    .dt-timer-circle-progress {
        fill: none;
        stroke: #10b981;
        stroke-width: 7;
        stroke-linecap: round;
        stroke-dasharray: 283;
        stroke-dashoffset: 0;
        transition: stroke-dashoffset 0.9s linear, stroke 0.4s ease;
    }

    .dt-timer-center-text {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .dt-timer-seconds {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.45rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.9);
    }

    .dt-timer-label {
        font-size: 0.58rem;
        font-weight: 700;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.7);
        letter-spacing: 0.5px;
    }

    .dt-phase-banner {
        background: rgba(0, 0, 0, 0.65);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 30px;
        padding: 4px 16px;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        transition: all 0.3s ease;
    }

    /* ----------------------------------------------------
       BATTLE ARENA: DRAGON (LEFT) VS TIGER (RIGHT)
       ---------------------------------------------------- */
    .dt-battle-arena {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 16px;
        align-items: stretch;
        position: relative;
        z-index: 4;
        margin-bottom: 16px;
    }

    .dt-team-zone {
        background: rgba(0, 0, 0, 0.45);
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        transition: all 0.35s ease;
    }

    .dt-zone-dragon {
        border-color: rgba(239, 68, 68, 0.45);
        box-shadow: inset 0 0 45px rgba(239, 68, 68, 0.15);
    }
    .dt-zone-dragon.winner-pulse {
        border-color: #f87171;
        box-shadow: 0 0 35px var(--dt-dragon-glow), inset 0 0 60px rgba(239, 68, 68, 0.35);
        animation: pulseWinner 1.5s infinite;
    }

    .dt-zone-tiger {
        border-color: rgba(59, 130, 246, 0.45);
        box-shadow: inset 0 0 45px rgba(59, 130, 246, 0.15);
    }
    .dt-zone-tiger.winner-pulse {
        border-color: #60a5fa;
        box-shadow: 0 0 35px var(--dt-tiger-glow), inset 0 0 60px rgba(59, 130, 246, 0.35);
        animation: pulseWinner 1.5s infinite;
    }

    @keyframes pulseWinner {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); }
    }

    .dt-zone-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        margin-bottom: 10px;
        padding: 0 6px;
    }

    .dt-zone-title {
        font-size: 1.15rem;
        font-weight: 800;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dt-vs-badge-col {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
    }

    .dt-vs-circle {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f59e0b, #b45309);
        color: #000;
        font-weight: 900;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 18px rgba(245, 158, 11, 0.5);
        border: 2px solid #fff;
    }

    /* ----------------------------------------------------
       3D PLAYING CARD REVEAL ANIMATIONS
       ---------------------------------------------------- */
    .dt-card-slot {
        width: 100px;
        height: 145px;
        perspective: 1000px;
        margin: 8px 0;
    }

    .dt-card-inner {
        width: 100%;
        height: 100%;
        position: relative;
        transform-style: preserve-3d;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 12px;
    }

    .dt-card-inner.flipped {
        transform: rotateY(180deg);
    }

    .dt-card-face {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6);
        overflow: hidden;
    }

    .dt-card-back {
        background: linear-gradient(135deg, #7f1d1d 0%, #311010 100%);
        border: 3px solid var(--dt-gold);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: var(--dt-gold);
    }

    .dt-card-front {
        background: #ffffff;
        color: #0f172a;
        transform: rotateY(180deg);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 8px 10px;
        border: 2px solid #e2e8f0;
    }

    .dt-card-corner-top {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        line-height: 1;
        font-family: 'JetBrains Mono', monospace;
    }
    .dt-card-corner-bottom {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        line-height: 1;
        font-family: 'JetBrains Mono', monospace;
        transform: rotate(180deg);
    }
    .dt-card-val {
        font-size: 1.25rem;
        font-weight: 900;
    }
    .dt-card-sym {
        font-size: 1rem;
    }
    .dt-card-center-sym {
        font-size: 2.2rem;
        text-align: center;
        line-height: 1;
    }

    /* ----------------------------------------------------
       BETTING BOARD & CHIPS
       ---------------------------------------------------- */
    .dt-bet-grid-main {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr 1.5fr;
        gap: 12px;
        margin-bottom: 12px;
    }

    .dt-bet-box {
        border-radius: 16px;
        padding: 16px 12px;
        text-align: center;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: all 0.25s ease;
        border: 2px solid transparent;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 105px;
    }

    .dt-bet-box:hover:not(.locked) {
        transform: translateY(-3px);
        filter: brightness(1.15);
    }

    .dt-bet-box.selected {
        border-color: #fff !important;
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.4) !important;
    }

    .dt-box-dragon {
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.25) 0%, rgba(127, 29, 29, 0.45) 100%);
        border-color: rgba(239, 68, 68, 0.5);
    }
    .dt-box-dragon:hover {
        border-color: var(--dt-dragon-red);
        box-shadow: 0 8px 25px var(--dt-dragon-glow);
    }

    .dt-box-tiger {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.25) 0%, rgba(30, 58, 138, 0.45) 100%);
        border-color: rgba(59, 130, 246, 0.5);
    }
    .dt-box-tiger:hover {
        border-color: var(--dt-tiger-blue);
        box-shadow: 0 8px 25px var(--dt-tiger-glow);
    }

    .dt-box-tie {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.25) 0%, rgba(4, 120, 87, 0.45) 100%);
        border-color: rgba(16, 185, 129, 0.5);
    }
    .dt-box-tie:hover {
        border-color: var(--dt-tie-green);
        box-shadow: 0 8px 25px var(--dt-tie-glow);
    }

    .dt-box-suited {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.25) 0%, rgba(180, 83, 9, 0.45) 100%);
        border-color: rgba(245, 158, 11, 0.5);
    }
    .dt-box-suited:hover {
        border-color: var(--dt-gold);
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.5);
    }

    .dt-bet-title {
        font-size: 1.1rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }

    .dt-bet-odds {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--dt-gold-light);
    }

    .dt-bet-amount-badge {
        background: rgba(0, 0, 0, 0.7);
        border: 1px solid var(--dt-gold);
        border-radius: 50px;
        padding: 2px 10px;
        font-size: 0.75rem;
        font-weight: 800;
        color: #fff;
        margin-top: 6px;
    }

    /* Side Bets Grid */
    .dt-side-bets-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 14px;
    }

    .dt-side-subgrid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }

    .dt-side-chip-btn {
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        padding: 8px 4px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .dt-side-chip-btn:hover:not(.locked) {
        border-color: var(--dt-gold);
        background: rgba(255, 255, 255, 0.08);
    }
    .dt-side-chip-title {
        font-size: 0.75rem;
        font-weight: 800;
        color: #f1f5f9;
        display: block;
    }
    .dt-side-chip-mult {
        font-size: 0.65rem;
        color: #cbd5e1;
        font-family: 'JetBrains Mono', monospace;
    }

    /* Chip Selector Bar */
    .dt-chip-tray {
        background: rgba(0, 0, 0, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
    }

    .dt-chips-list {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .dt-casino-chip {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.72rem;
        cursor: pointer;
        border: 3px dashed #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        color: #fff;
        user-select: none;
    }
    .dt-casino-chip:hover {
        transform: scale(1.15) translateY(-3px);
    }
    .dt-casino-chip.active {
        transform: scale(1.2) translateY(-4px);
        box-shadow: 0 0 16px var(--dt-gold);
        border-style: solid;
    }

    .chip-10 { background: radial-gradient(circle, #3b82f6, #1d4ed8); }
    .chip-50 { background: radial-gradient(circle, #10b981, #047857); }
    .chip-100 { background: radial-gradient(circle, #ef4444, #991b1b); }
    .chip-500 { background: radial-gradient(circle, #8b5cf6, #5b21b6); }
    .chip-1k { background: radial-gradient(circle, #f59e0b, #b45309); color: #000 !important; }
    .chip-5k { background: radial-gradient(circle, #ec4899, #9d174d); }

    /* ----------------------------------------------------
       BEAD PLATE / ROAD MAP
       ---------------------------------------------------- */
    .dt-bead-plate-card {
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 16px;
        margin-top: 16px;
    }

    .dt-bead-grid {
        display: grid;
        grid-auto-flow: column;
        grid-template-rows: repeat(5, 28px);
        gap: 5px;
        overflow-x: auto;
        padding: 6px 0;
        scrollbar-width: thin;
    }

    .dt-bead-cell {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.72rem;
        color: #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
    }

    .bead-dragon { background: #dc2626; border: 1px solid #f87171; }
    .bead-tiger { background: #2563eb; border: 1px solid #60a5fa; }
    .bead-tie { background: #10b981; border: 1px solid #34d399; }
    .bead-suited { background: #f59e0b; border: 2px solid #fff; color: #000; }

    /* ----------------------------------------------------
       BOTTOM TABBED DATA: MY BETS, BEAD PLATE, ROUND HISTORY, RULES
       ---------------------------------------------------- */
    .dt-bottom-tabs-card {
        background: rgba(10, 16, 28, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-radius: 22px;
        padding: 16px;
        margin-top: 18px;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.7);
    }

    .dt-nav-tabs {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        gap: 8px;
    }

    .dt-nav-tabs .nav-link {
        color: #e2e8f0;
        border: none;
        border-bottom: 2px solid transparent;
        border-radius: 0;
        font-weight: 700;
        font-size: 0.88rem;
        padding: 9px 18px;
        transition: all 0.25s ease;
        background: transparent;
    }

    .dt-nav-tabs .nav-link:hover {
        color: #f1f5f9;
        border-bottom-color: rgba(245, 158, 11, 0.5);
    }

    .dt-nav-tabs .nav-link.active {
        color: #f59e0b;
        background: transparent;
        border-bottom: 2px solid #f59e0b;
    }

    .dt-table-card {
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .dt-table-scroll-wrapper {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        touch-action: pan-x !important;
        width: 100% !important;
        display: block !important;
        scrollbar-width: thin;
        scrollbar-color: #f59e0b #f1f5f9;
    }

    .dt-table-scroll-wrapper::-webkit-scrollbar {
        height: 6px;
    }
    .dt-table-scroll-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .dt-table-scroll-wrapper::-webkit-scrollbar-thumb {
        background: #f59e0b;
        border-radius: 4px;
    }
    .dt-table-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        background: #d97706;
    }

    .dt-table {
        --bs-table-bg: #ffffff !important;
        background-color: #ffffff !important;
        color: #1e293b !important;
        font-size: 0.84rem;
        border-color: #e2e8f0 !important;
        margin-bottom: 0;
        min-width: 760px;
        width: 100%;
    }

    .dt-table thead th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0 !important;
        padding: 12px 14px;
        white-space: nowrap;
    }

    .dt-table tbody td {
        background: #ffffff !important;
        color: #1e293b !important;
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9 !important;
        vertical-align: middle;
        white-space: nowrap;
    }

    .dt-table tbody tr:hover td {
        background: #f8fafc !important;
    }

    .badge-dragon-bet {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        color: #fff;
    }
    .badge-tiger-bet {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
    }
    .badge-tie-bet {
        background: linear-gradient(135deg, #10b981, #047857);
        color: #fff;
    }
    .badge-suited-tie-bet {
        background: linear-gradient(135deg, #fbbf24, #d97706);
        color: #000;
        font-weight: 700;
    }
    .badge-side-bet {
        background: #e2e8f0;
        color: #1e293b;
        border: 1px solid #cbd5e1;
        font-weight: 700;
    }

    .dt-rules-table {
        background: rgba(0, 0, 0, 0.45) !important;
        color: #e2e8f0 !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    .dt-rules-table th {
        background: rgba(0, 0, 0, 0.7) !important;
        color: #f59e0b !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15) !important;
        padding: 8px 12px !important;
    }
    .dt-rules-table td {
        background: transparent !important;
        color: #f1f5f9 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        padding: 8px 12px !important;
    }

    /* ========================================================
       MOBILE-FIRST STREAMLINED VIEW (< 768px)
       ALL-IN-ONE SCREEN CASINO EXPERIENCE
       ======================================================== */
    @media (max-width: 767.98px) {
        .dt-arena-container {
            padding: 2px 2px 50px !important;
            max-width: 100% !important;
        }

        /* Compact 1-Row Header */
        .dt-topbar {
            padding: 6px 8px !important;
            margin-bottom: 5px !important;
            border-radius: 12px !important;
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 4px !important;
        }
        .dt-round-pill {
            padding: 3px 6px !important;
            font-size: 0.68rem !important;
            gap: 3px !important;
        }
        .dt-round-pill #activeRoundId {
            max-width: 82px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
            vertical-align: middle;
        }
        .dt-top-btn {
            font-size: 0.72rem !important;
            padding: 3px 8px !important;
        }
        .dt-phase-banner {
            padding: 3px 6px !important;
            font-size: 0.62rem !important;
            letter-spacing: 0.5px !important;
        }

        /* Compact Table */
        .dt-felt-table {
            padding: 8px 6px !important;
            border-radius: 16px !important;
            border-width: 3px !important;
            margin-top: 4px !important;
        }

        /* Compact Dealer Stage & Timer */
        .dt-dealer-stage {
            margin-bottom: 4px !important;
        }
        .dt-timer-wrap,
        .dt-timer-svg {
            width: 48px !important;
            height: 48px !important;
            margin: 0 !important;
        }
        .dt-timer-seconds {
            font-size: 1.1rem !important;
        }
        .dt-timer-label {
            font-size: 0.48rem !important;
        }

        /* BATTLE ARENA: SIDE-BY-SIDE DRAGON & TIGER (NEVER STACKED) */
        .dt-battle-arena {
            display: grid !important;
            grid-template-columns: 1fr auto 1fr !important;
            gap: 6px !important;
            align-items: center !important;
            margin-bottom: 8px !important;
        }
        .dt-team-zone {
            padding: 6px 4px !important;
            border-radius: 14px !important;
        }
        .dt-zone-header {
            margin-bottom: 3px !important;
            padding: 0 2px !important;
        }
        .dt-zone-title {
            font-size: 0.82rem !important;
            gap: 4px !important;
        }
        .dt-zone-title i {
            font-size: 0.95rem !important;
        }
        .badge#dragonCardTitle,
        .badge#tigerCardTitle {
            font-size: 0.58rem !important;
            padding: 2px 4px !important;
            max-width: 65px !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }
        .dt-vs-badge-col {
            display: flex !important;
            align-self: center !important;
            padding: 0 !important;
        }
        .dt-vs-circle {
            width: 32px !important;
            height: 32px !important;
            font-size: 0.75rem !important;
            border-width: 1.5px !important;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.4) !important;
        }

        /* 3D Playing Card Slots on Mobile */
        .dt-card-slot {
            width: 52px !important;
            height: 74px !important;
            margin: 2px 0 !important;
        }
        .dt-card-front {
            padding: 3px 4px !important;
            border-radius: 8px !important;
            border-width: 1.5px !important;
        }
        .dt-card-back {
            border-radius: 8px !important;
            border-width: 2px !important;
        }
        .dt-card-val {
            font-size: 0.78rem !important;
            line-height: 1 !important;
        }
        .dt-card-sym {
            font-size: 0.65rem !important;
            line-height: 1 !important;
        }
        .dt-card-center-sym {
            font-size: 1.25rem !important;
            line-height: 1 !important;
        }
        .dt-card-back i {
            font-size: 1.2rem !important;
        }
        .dt-card-back .small {
            font-size: 0.52rem !important;
            margin-top: 2px !important;
        }

        /* 4 Main Bet Boxes (Dragon, Tie, Suited Tie, Tiger) in 1 Row */
        .dt-bet-grid-main {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 4px !important;
            margin-bottom: 6px !important;
        }
        .dt-bet-box {
            min-height: 56px !important;
            padding: 5px 2px !important;
            border-radius: 10px !important;
        }
        .dt-bet-box i {
            font-size: 1rem !important;
            margin-bottom: 1px !important;
        }
        .dt-bet-title {
            font-size: 0.68rem !important;
            margin-bottom: 0 !important;
            line-height: 1.1 !important;
        }
        .dt-bet-odds {
            font-size: 0.62rem !important;
            line-height: 1.1 !important;
        }
        .dt-bet-amount-badge {
            font-size: 0.58rem !important;
            padding: 1px 4px !important;
            margin-top: 2px !important;
        }

        /* Collapsible Side Bets on Mobile */
        .dt-side-toggle-btn {
            background: rgba(0, 0, 0, 0.45) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #f1f5f9 !important;
        }
        .dt-side-bets-grid {
            grid-template-columns: 1fr 1fr !important;
            gap: 6px !important;
            margin-bottom: 6px !important;
        }
        .dt-side-subgrid {
            gap: 4px !important;
        }
        .dt-side-chip-btn {
            padding: 4px 2px !important;
            border-radius: 8px !important;
        }
        .dt-side-chip-title {
            font-size: 0.65rem !important;
        }
        .dt-side-chip-mult {
            font-size: 0.58rem !important;
        }

        /* Chip Selector Tray */
        .dt-chip-tray {
            padding: 5px 8px !important;
            border-radius: 12px !important;
            gap: 6px !important;
            flex-wrap: nowrap !important;
            justify-content: space-between !important;
        }
        .dt-chips-list {
            gap: 4px !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            scrollbar-width: none !important;
        }
        .dt-chips-list::-webkit-scrollbar {
            display: none !important;
        }
        .dt-casino-chip {
            width: 32px !important;
            height: 32px !important;
            font-size: 0.65rem !important;
            border-width: 1.5px !important;
            flex-shrink: 0 !important;
        }
        .custom-bet-wrap {
            flex-shrink: 0 !important;
        }
        .custom-bet-wrap .input-group {
            width: 85px !important;
        }
        .custom-bet-wrap input {
            font-size: 0.72rem !important;
            padding: 2px 4px !important;
            height: 28px !important;
        }
        .custom-bet-wrap button {
            font-size: 0.68rem !important;
            padding: 2px 8px !important;
            height: 28px !important;
        }

        /* Bottom Tabs Compact */
        .dt-bottom-tabs-card {
            padding: 10px 8px !important;
            border-radius: 14px !important;
            margin-top: 10px !important;
        }
        .dt-nav-tabs .nav-link {
            font-size: 0.74rem !important;
            padding: 6px 10px !important;
        }
        .dt-table thead th {
            padding: 8px 10px !important;
            font-size: 0.68rem !important;
        }
        .dt-table tbody td {
            padding: 8px 10px !important;
            font-size: 0.76rem !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid dt-arena-container py-3">

    <!-- Top Status Bar -->
    <div class="dt-topbar mb-2 d-flex align-items-center justify-content-between gap-1 gap-md-2">
        <div class="d-flex align-items-center gap-1 gap-md-2 overflow-hidden">
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-light rounded-pill px-2 py-1 dt-top-btn" title="Back to Lobby">
                <i class="fa-solid fa-arrow-left"></i> <span class="d-none d-sm-inline ms-1">Lobby</span>
            </a>
            <div class="dt-round-pill">
                <i class="fa-solid fa-gamepad text-warning"></i>
                <span class="d-none d-sm-inline">Round:</span>
                <span id="activeRoundId" class="fw-bold">{{ $roundData['round_id'] }}</span>
            </div>
            <div class="dt-phase-banner" id="phaseBanner">
                <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
                <span id="phaseText">BETTING OPEN</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <div class="text-end">
                <div class="small text-light d-none d-sm-block fw-bold" style="font-size: 0.68rem; line-height: 1; color: #cbd5e1 !important;">WALLET BALANCE</div>
                <div class="fw-bold font-monospace text-warning dt-wallet-val" id="userWalletBalance" style="font-size: 0.85rem;">
                    @auth
                        ₹{{ number_format(Auth::user()->balance, 2) }}
                    @else
                        ₹0.00
                    @endauth
                </div>
            </div>
            @auth
                <a href="{{ route('wallet.deposit') }}" class="btn btn-sm btn-success rounded-pill px-2 px-md-3 py-1 fw-bold shadow-sm" style="font-size: 0.72rem;">
                    <i class="fa-solid fa-plus-circle me-1"></i> <span class="d-none d-sm-inline">Deposit</span><span class="d-sm-none">+</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-warning text-dark rounded-pill px-2 px-md-3 py-1 fw-bold shadow-sm" style="font-size: 0.72rem;">
                    Login
                </a>
            @endauth
        </div>
    </div>

    <!-- Velvet Gaming Table -->
    <div class="dt-felt-table">

        <!-- Dealer & Central Circular Timer -->
        <div class="dt-dealer-stage">
            <div class="dt-timer-wrap">
                <svg class="dt-timer-svg" viewBox="0 0 100 100">
                    <circle class="dt-timer-circle-bg" cx="50" cy="50" r="45"></circle>
                    <circle class="dt-timer-circle-progress" id="timerProgressCircle" cx="50" cy="50" r="45"></circle>
                </svg>
                <div class="dt-timer-center-text">
                    <div class="dt-timer-seconds" id="timerSecondsDisplay">{{ $roundData['remaining_seconds'] }}</div>
                    <div class="dt-timer-label" id="timerStatusLabel">SEC</div>
                </div>
            </div>
        </div>

        <!-- Battle Arena: Dragon (Red) vs Tiger (Blue) -->
        <div class="dt-battle-arena">

            <!-- DRAGON ZONE -->
            <div class="dt-team-zone dt-zone-dragon" id="zoneDragon">
                <div class="dt-zone-header">
                    <div class="dt-zone-title text-danger">
                        <i class="fa-solid fa-dragon fs-4"></i>
                        <span>DRAGON</span>
                    </div>
                    <span class="badge bg-danger rounded-pill px-2 py-1 font-monospace" id="dragonCardTitle">-</span>
                </div>

                <!-- 3D Dragon Card Slot -->
                <div class="dt-card-slot">
                    <div class="dt-card-inner" id="cardDragonInner">
                        <div class="dt-card-face dt-card-back">
                            <i class="fa-solid fa-dragon fs-1 opacity-75"></i>
                            <div class="small fw-bold mt-2">DRAGON</div>
                        </div>
                        <div class="dt-card-face dt-card-front" id="cardDragonFront">
                            <div class="dt-card-corner-top">
                                <span class="dt-card-val" id="dCardVal">K</span>
                                <span class="dt-card-sym" id="dCardSymTop">♦</span>
                            </div>
                            <div class="dt-card-center-sym" id="dCardSymCenter">♦</div>
                            <div class="dt-card-corner-bottom">
                                <span class="dt-card-val" id="dCardValBot">K</span>
                                <span class="dt-card-sym" id="dCardSymBot">♦</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VS CENTER CIRCLE -->
            <div class="dt-vs-badge-col">
                <div class="dt-vs-circle">VS</div>
            </div>

            <!-- TIGER ZONE -->
            <div class="dt-team-zone dt-zone-tiger" id="zoneTiger">
                <div class="dt-zone-header">
                    <div class="dt-zone-title text-primary">
                        <i class="fa-solid fa-paw fs-4"></i>
                        <span>TIGER</span>
                    </div>
                    <span class="badge bg-primary rounded-pill px-2 py-1 font-monospace" id="tigerCardTitle">-</span>
                </div>

                <!-- 3D Tiger Card Slot -->
                <div class="dt-card-slot">
                    <div class="dt-card-inner" id="cardTigerInner">
                        <div class="dt-card-face dt-card-back">
                            <i class="fa-solid fa-paw fs-1 opacity-75"></i>
                            <div class="small fw-bold mt-2">TIGER</div>
                        </div>
                        <div class="dt-card-face dt-card-front" id="cardTigerFront">
                            <div class="dt-card-corner-top">
                                <span class="dt-card-val" id="tCardVal">9</span>
                                <span class="dt-card-sym" id="tCardSymTop">♠</span>
                            </div>
                            <div class="dt-card-center-sym" id="tCardSymCenter">♠</div>
                            <div class="dt-card-corner-bottom">
                                <span class="dt-card-val" id="tCardValBot">9</span>
                                <span class="dt-card-sym" id="tCardSymBot">♠</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN BETTING BOXES (Dragon, Tie, Suited Tie, Tiger) -->
        <div class="dt-bet-grid-main">
            <!-- 1. DRAGON -->
            <div class="dt-bet-box dt-box-dragon" onclick="placeBetOn('dragon')">
                <i class="fa-solid fa-dragon text-danger fs-3 mb-1"></i>
                <div class="dt-bet-title text-white">DRAGON</div>
                <div class="dt-bet-odds">{{ $roundData['odds_dragon'] }}X</div>
                <div class="dt-bet-amount-badge" id="betBadge_dragon" style="display:none;">₹0</div>
            </div>

            <!-- 2. TIE -->
            <div class="dt-bet-box dt-box-tie" onclick="placeBetOn('tie')">
                <i class="fa-solid fa-handshake text-success fs-3 mb-1"></i>
                <div class="dt-bet-title text-white">TIE</div>
                <div class="dt-bet-odds">{{ $roundData['odds_tie'] }}X</div>
                <div class="dt-bet-amount-badge" id="betBadge_tie" style="display:none;">₹0</div>
            </div>

            <!-- 3. SUITED TIE -->
            <div class="dt-bet-box dt-box-suited" onclick="placeBetOn('suited_tie')">
                <i class="fa-solid fa-crown text-warning fs-3 mb-1"></i>
                <div class="dt-bet-title text-warning">SUITED TIE</div>
                <div class="dt-bet-odds">{{ $roundData['odds_suited_tie'] }}X</div>
                <div class="dt-bet-amount-badge" id="betBadge_suited_tie" style="display:none;">₹0</div>
            </div>

            <!-- 4. TIGER -->
            <div class="dt-bet-box dt-box-tiger" onclick="placeBetOn('tiger')">
                <i class="fa-solid fa-paw text-primary fs-3 mb-1"></i>
                <div class="dt-bet-title text-white">TIGER</div>
                <div class="dt-bet-odds">{{ $roundData['odds_tiger'] }}X</div>
                <div class="dt-bet-amount-badge" id="betBadge_tiger" style="display:none;">₹0</div>
            </div>
        </div>

        <!-- SIDE BETS ACCORDION / TOGGLE -->
        <div class="dt-side-bets-wrap mb-2">
            <button class="btn btn-sm dt-side-toggle-btn w-100 d-flex align-items-center justify-content-between px-2 py-1 rounded-3 mb-1 d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#dtSideBetsCollapse" aria-expanded="false" aria-controls="dtSideBetsCollapse">
                <span class="small fw-bold text-warning" style="font-size: 0.72rem;">
                    <i class="fa-solid fa-sparkles me-1"></i> Side Bets (1.95X)
                </span>
                <span class="small text-white-50" style="font-size: 0.68rem;">
                    Tap to Toggle <i class="fa-solid fa-chevron-down ms-1"></i>
                </span>
            </button>
            <div class="collapse d-md-block" id="dtSideBetsCollapse">
                <div class="dt-side-bets-grid">
                    <!-- Dragon Side Bets -->
                    <div>
                        <div class="small fw-bold text-danger text-uppercase mb-1 d-none d-md-block"><i class="fa-solid fa-dragon me-1"></i> Dragon Side Bets (1.95x)</div>
                        <div class="dt-side-subgrid">
                            <div class="dt-side-chip-btn" onclick="placeBetOn('dragon_big')">
                                <span class="dt-side-chip-title">Big (8-K)</span>
                                <span class="dt-side-chip-mult">1.95X</span>
                            </div>
                            <div class="dt-side-chip-btn" onclick="placeBetOn('dragon_small')">
                                <span class="dt-side-chip-title">Small (A-6)</span>
                                <span class="dt-side-chip-mult">1.95X</span>
                            </div>
                            <div class="dt-side-chip-btn" onclick="placeBetOn('dragon_red')">
                                <span class="dt-side-chip-title text-danger">Red ♥♦</span>
                                <span class="dt-side-chip-mult">1.95X</span>
                            </div>
                            <div class="dt-side-chip-btn" onclick="placeBetOn('dragon_black')">
                                <span class="dt-side-chip-title">Black ♠♣</span>
                                <span class="dt-side-chip-mult">1.95X</span>
                            </div>
                            <div class="dt-side-chip-btn" onclick="placeBetOn('dragon_odd')">
                                <span class="dt-side-chip-title">Odd</span>
                                <span class="dt-side-chip-mult">1.95X</span>
                            </div>
                            <div class="dt-side-chip-btn" onclick="placeBetOn('dragon_even')">
                                <span class="dt-side-chip-title">Even</span>
                                <span class="dt-side-chip-mult">1.95X</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tiger Side Bets -->
                    <div>
                        <div class="small fw-bold text-primary text-uppercase mb-1 d-none d-md-block"><i class="fa-solid fa-paw me-1"></i> Tiger Side Bets (1.95x)</div>
                        <div class="dt-side-subgrid">
                            <div class="dt-side-chip-btn" onclick="placeBetOn('tiger_big')">
                                <span class="dt-side-chip-title">Big (8-K)</span>
                                <span class="dt-side-chip-mult">1.95X</span>
                            </div>
                            <div class="dt-side-chip-btn" onclick="placeBetOn('tiger_small')">
                                <span class="dt-side-chip-title">Small (A-6)</span>
                                <span class="dt-side-chip-mult">1.95X</span>
                            </div>
                            <div class="dt-side-chip-btn" onclick="placeBetOn('tiger_red')">
                                <span class="dt-side-chip-title text-danger">Red ♥♦</span>
                                <span class="dt-side-chip-mult">1.95X</span>
                            </div>
                            <div class="dt-side-chip-btn" onclick="placeBetOn('tiger_black')">
                                <span class="dt-side-chip-title">Black ♠♣</span>
                                <span class="dt-side-chip-mult">1.95X</span>
                            </div>
                            <div class="dt-side-chip-btn" onclick="placeBetOn('tiger_odd')">
                                <span class="dt-side-chip-title">Odd</span>
                                <span class="dt-side-chip-mult">1.95X</span>
                            </div>
                            <div class="dt-side-chip-btn" onclick="placeBetOn('tiger_even')">
                                <span class="dt-side-chip-title">Even</span>
                                <span class="dt-side-chip-mult">1.95X</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHIP SELECTOR TRAY -->
        <div class="dt-chip-tray">
            <div class="d-flex align-items-center gap-2 overflow-hidden flex-grow-1">
                <span class="small fw-bold text-light text-uppercase d-none d-sm-inline">CHIP:</span>
                <div class="dt-chips-list">
                    <div class="dt-casino-chip chip-10 active" onclick="selectChip(10, this)">₹10</div>
                    <div class="dt-casino-chip chip-50" onclick="selectChip(50, this)">₹50</div>
                    <div class="dt-casino-chip chip-100" onclick="selectChip(100, this)">₹100</div>
                    <div class="dt-casino-chip chip-500" onclick="selectChip(500, this)">₹500</div>
                    <div class="dt-casino-chip chip-1k" onclick="selectChip(1000, this)">₹1K</div>
                    <div class="dt-casino-chip chip-5k" onclick="selectChip(5000, this)">₹5K</div>
                </div>
            </div>

            <div class="custom-bet-wrap d-flex align-items-center gap-1 gap-md-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-dark border-secondary text-warning px-1">₹</span>
                    <input type="number" id="customBetInput" class="form-control bg-dark border-secondary text-white font-monospace" placeholder="Custom" min="5" max="25000">
                </div>
                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill" onclick="applyCustomBet()">Set</button>
            </div>
        </div>
    </div>

    <!-- BOTTOM TABBED SECTION: MY BETS, BEAD PLATE, ROUND HISTORY, RULES -->
    <div class="dt-bottom-tabs-card">
        <ul class="nav nav-tabs dt-nav-tabs mb-3" id="dtBottomTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="tab-my-bets-btn" data-bs-toggle="tab" data-bs-target="#tabMyBets" type="button" role="tab">
                    <i class="fa-solid fa-clock-rotate-left me-1 text-warning"></i> My Bets
                    <span class="badge bg-warning text-dark ms-1 rounded-pill" id="myBetsCountBadge">{{ count($myRecentBets ?? []) }}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-bead-plate-btn" data-bs-toggle="tab" data-bs-target="#tabBeadPlate" type="button" role="tab">
                    <i class="fa-solid fa-chart-simple me-1 text-info"></i> Bead Plate & Trends
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-round-history-btn" data-bs-toggle="tab" data-bs-target="#tabRoundHistory" type="button" role="tab">
                    <i class="fa-solid fa-list-ol me-1 text-primary"></i> Round History
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-rules-btn" data-bs-toggle="tab" data-bs-target="#tabRules" type="button" role="tab">
                    <i class="fa-solid fa-book-open me-1 text-success"></i> Rules & Paytable
                </button>
            </li>
        </ul>

        <div class="tab-content" id="dtBottomTabsContent">

            <!-- TAB 1: MY BETS -->
            <div class="tab-pane fade show active" id="tabMyBets" role="tabpanel">
                <div class="dt-table-card">
                    <div class="d-md-none px-3 py-1 bg-light border-bottom d-flex align-items-center justify-content-between text-muted" style="font-size: 0.68rem;">
                        <span><i class="fa-solid fa-receipt me-1 text-warning"></i> My Bets</span>
                        <span><i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally</span>
                    </div>
                    <div class="dt-table-scroll-wrapper table-responsive">
                        <table class="table dt-table mb-0">
                            <thead>
                                <tr>
                                    <th>Round ID</th>
                                    <th>Bet Type</th>
                                    <th>Stake</th>
                                    <th>Odds</th>
                                    <th>Payout</th>
                                    <th>Status</th>
                                    <th>Outcome Details</th>
                                    <th class="text-end">Time</th>
                                </tr>
                            </thead>
                            <tbody id="myBetsTableBody">
                            @auth
                                @forelse($myRecentBets ?? [] as $mb)
                                    @php
                                        $typeCls = match($mb->bet_type) {
                                            'dragon' => 'badge-dragon-bet',
                                            'tiger' => 'badge-tiger-bet',
                                            'tie' => 'badge-tie-bet',
                                            'suited_tie' => 'badge-suited-tie-bet',
                                            default => 'badge-side-bet',
                                        };
                                        $typeIcon = match($mb->bet_type) {
                                            'dragon' => 'fa-dragon',
                                            'tiger' => 'fa-paw',
                                            'tie' => 'fa-handshake',
                                            'suited_tie' => 'fa-crown',
                                            default => 'fa-dice',
                                        };
                                        $statusCls = match($mb->status) {
                                            'won' => 'bg-success text-white',
                                            'lost' => 'bg-danger text-white',
                                            'refunded' => 'bg-info text-dark',
                                            default => 'bg-warning text-dark border border-warning',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="font-monospace text-dark fw-bold">#{{ $mb->round_id }}</td>
                                        <td>
                                            <span class="badge {{ $typeCls }} px-2 py-1 rounded-pill">
                                                <i class="fa-solid {{ $typeIcon }} me-1"></i> {{ strtoupper(str_replace('_', ' ', $mb->bet_type)) }}
                                            </span>
                                        </td>
                                        <td class="font-monospace fw-bold text-dark fs-6">₹{{ number_format($mb->bet_amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-warning bg-opacity-25 text-dark border border-warning border-opacity-50 font-monospace fw-bold">
                                                {{ number_format($mb->odds, 2) }}X
                                            </span>
                                        </td>
                                        <td>
                                            @if($mb->status === 'won')
                                                <span class="text-success fw-bold font-monospace fs-6">+₹{{ number_format($mb->payout_amount, 2) }}</span>
                                            @elseif($mb->status === 'refunded')
                                                <span class="text-primary fw-bold font-monospace">₹{{ number_format($mb->payout_amount, 2) }} <span class="badge bg-info text-dark">50% Push</span></span>
                                            @elseif($mb->status === 'lost')
                                                <span class="text-secondary font-monospace fw-semibold">₹0.00</span>
                                            @else
                                                <span class="badge bg-warning text-dark border border-warning">PENDING</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $statusCls }} rounded-pill px-2 py-1 text-uppercase">
                                                {{ $mb->status }}
                                            </span>
                                        </td>
                                        <td class="small text-secondary font-monospace fw-semibold">
                                            {{ $mb->hand_outcome ?: ($mb->round?->winner ? 'Winner: '.strtoupper($mb->round->winner) : '-') }}
                                        </td>
                                        <td class="text-end text-secondary small font-monospace fw-medium">
                                            {{ $mb->created_at?->format('h:i:s A') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fa-solid fa-coins fs-3 mb-2 text-warning d-block"></i>
                                            <span class="text-secondary fw-bold">No bets placed yet. Place your bet on Dragon, Tiger or Tie to win up to 50X!</span>
                                        </td>
                                    </tr>
                                @endforelse
                            @else
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fa-solid fa-lock fs-3 mb-2 text-warning d-block"></i>
                                        <span class="text-secondary fw-bold">Please <a href="{{ route('login') }}" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 ms-2">Login</a> to place bets and view your betting history.</span>
                                    </td>
                                </tr>
                            @endauth
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <!-- TAB 2: BEAD PLATE & TRENDS -->
            <div class="tab-pane fade" id="tabBeadPlate" role="tabpanel">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-white fs-6">
                            Live Game Trends (50 Rounds)
                        </span>
                        <div class="d-flex align-items-center gap-2 ms-3 font-monospace small">
                            <span class="badge bg-danger">Dragon: <span id="statDragonPct">{{ $dragonPercent }}%</span></span>
                            <span class="badge bg-primary">Tiger: <span id="statTigerPct">{{ $tigerPercent }}%</span></span>
                            <span class="badge bg-success">Tie: <span id="statTiePct">{{ $tiePercent }}%</span></span>
                        </div>
                    </div>
                    <div class="small text-light font-monospace opacity-75">Red (D) • Blue (T) • Green (X) • Gold (S)</div>
                </div>

                <div class="dt-bead-grid" id="beadGridContainer">
                    @foreach($recentHistory as $r)
                        @php
                            $w = $r->winner;
                            $class = match($w) {
                                'dragon' => 'bead-dragon',
                                'tiger' => 'bead-tiger',
                                'suited_tie' => 'bead-suited',
                                default => 'bead-tie',
                            };
                            $label = match($w) {
                                'dragon' => 'D',
                                'tiger' => 'T',
                                'suited_tie' => 'S',
                                default => 'X',
                            };
                        @endphp
                        <div class="dt-bead-cell {{ $class }}" title="Round #{{ $r->round_id }}: {{ strtoupper($w) }}">
                            {{ $label }}
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- TAB 3: ROUND HISTORY -->
            <div class="tab-pane fade" id="tabRoundHistory" role="tabpanel">
                <div class="dt-table-card">
                    <div class="d-md-none px-3 py-1 bg-light border-bottom d-flex align-items-center justify-content-between text-muted" style="font-size: 0.68rem;">
                        <span><i class="fa-solid fa-list-ol me-1 text-warning"></i> Round History</span>
                        <span><i class="fa-solid fa-arrows-left-right me-1 text-primary"></i> Scroll horizontally</span>
                    </div>
                    <div class="dt-table-scroll-wrapper table-responsive">
                        <table class="table dt-table mb-0" style="min-width: 620px;">
                            <thead>
                                <tr>
                                    <th>Round ID</th>
                                    <th>Winner</th>
                                    <th>Dragon Card</th>
                                    <th>Tiger Card</th>
                                    <th>Winning Hand Details</th>
                                    <th class="text-end">Settled Time</th>
                                </tr>
                            </thead>
                            <tbody id="roundHistoryTableBody">
                                @forelse($recentHistory as $rh)
                                    <tr>
                                        <td class="font-monospace text-dark fw-bold">#{{ $rh->round_id }}</td>
                                        <td>
                                            <span class="badge {{ $rh->winner === 'dragon' ? 'bg-danger text-white' : ($rh->winner === 'tiger' ? 'bg-primary text-white' : 'bg-success text-white') }} px-2 py-1 rounded-pill text-uppercase">
                                                {{ strtoupper($rh->winner) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if(!empty($rh->dragon_card))
                                                <span class="badge bg-white border border-secondary border-opacity-25 font-monospace fs-6 shadow-sm" style="color: {{ $rh->dragon_card['color'] ?? '#000' }} !important;">
                                                    {{ $rh->dragon_card['rank'] }}{{ $rh->dragon_card['symbol'] }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($rh->tiger_card))
                                                <span class="badge bg-white border border-secondary border-opacity-25 font-monospace fs-6 shadow-sm" style="color: {{ $rh->tiger_card['color'] ?? '#000' }} !important;">
                                                    {{ $rh->tiger_card['rank'] }}{{ $rh->tiger_card['symbol'] }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="small text-secondary font-monospace fw-semibold">
                                            @if(!empty($rh->dragon_card) && !empty($rh->tiger_card))
                                                {{ $rh->dragon_card['rank'] }} vs {{ $rh->tiger_card['rank'] }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-end text-secondary small font-monospace fw-medium">
                                            {{ $rh->created_at?->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-secondary fw-bold">No completed rounds yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 4: RULES & PAYTABLE -->
            <div class="tab-pane fade" id="tabRules" role="tabpanel">
                <div class="row g-4 p-2">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background: rgba(0, 0, 0, 0.55); border: 1px solid rgba(255, 255, 255, 0.15);">
                            <h6 class="fw-bold text-warning mb-3">
                                <i class="fa-solid fa-crown me-2"></i> Card Ranking Hierarchy
                            </h6>
                            <p class="small mb-2" style="color: #f8fafc !important;">In Dragon Tiger, 1 single card is dealt to Dragon and 1 card to Tiger. The hand with the highest rank card wins!</p>
                            <div class="p-2 bg-dark rounded border border-warning border-opacity-50 font-monospace small text-center text-warning mb-3 fw-bold">
                                K &gt; Q &gt; J &gt; 10 &gt; 9 &gt; 8 &gt; 7 &gt; 6 &gt; 5 &gt; 4 &gt; 3 &gt; 2 &gt; A
                            </div>
                            <ul class="small mb-0 ps-3" style="color: #f1f5f9 !important; line-height: 1.8;">
                                <li><strong class="text-warning">King (K)</strong> is the highest card (Value 13).</li>
                                <li><strong class="text-warning">Ace (A)</strong> is the lowest card (Value 1).</li>
                                <li><span style="color: #f8fafc !important;">Suit does not affect Dragon vs Tiger comparison (only ranks compare).</span></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background: rgba(0, 0, 0, 0.55); border: 1px solid rgba(255, 255, 255, 0.15);">
                            <h6 class="fw-bold text-warning mb-3">
                                <i class="fa-solid fa-coins me-2"></i> Payout Ratios & House Edge
                            </h6>
                            <table class="table table-sm dt-rules-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Bet Choice</th>
                                        <th>Odds</th>
                                        <th>Win Condition</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-danger">DRAGON</span></td>
                                        <td class="text-warning font-monospace fw-bold">1.95X</td>
                                        <td class="small" style="color: #f1f5f9 !important;">Dragon card rank &gt; Tiger. (Tie gives 50% refund push!)</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-primary">TIGER</span></td>
                                        <td class="text-warning font-monospace fw-bold">1.95X</td>
                                        <td class="small" style="color: #f1f5f9 !important;">Tiger card rank &gt; Dragon. (Tie gives 50% refund push!)</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-success">TIE</span></td>
                                        <td class="text-warning font-monospace fw-bold">9.00X</td>
                                        <td class="small" style="color: #f1f5f9 !important;">Dragon rank equals Tiger rank (e.g. 8 vs 8).</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-warning text-dark fw-bold">SUITED TIE</span></td>
                                        <td class="text-warning font-monospace fw-bold">50.00X</td>
                                        <td class="small" style="color: #f1f5f9 !important;">Exact identical card and suit (e.g. 8♥ vs 8♥).</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-secondary">SIDE BETS</span></td>
                                        <td class="text-warning font-monospace fw-bold">1.95X</td>
                                        <td class="small" style="color: #f1f5f9 !important;">Big (8-K), Small (A-6), Red, Black, Odd, Even. (Rank 7 loses!)</td>
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

<!-- Win Notification Toast / Popup -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div id="winToast" class="toast align-items-center text-white bg-success border-0 shadow-lg" role="alert">
        <div class="d-flex">
            <div class="toast-body fs-6 fw-bold">
                <i class="fa-solid fa-trophy text-warning me-2 fs-5"></i>
                <span id="winToastMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Dragon Tiger Live Arena State Controller
    let selectedChipAmount = 10;
    let currentRoundId = "{{ $roundData['round_id'] }}";
    let isLocked = {{ $roundData['is_locked'] ? 'true' : 'false' }};
    let remainingSeconds = {{ $roundData['remaining_seconds'] }};
    let roundDuration = {{ $roundData['duration'] }};
    let pollInterval = null;
    let localTimerInterval = null;
    let lastSettledRoundId = null;

    const urls = {
        status: "{{ route('api.dragon-tiger.status') }}",
        placeBet: "{{ route('api.dragon-tiger.place-bet') }}",
        history: "{{ route('api.dragon-tiger.history') }}",
        myBets: "{{ route('api.dragon-tiger.my-bets') }}"
    };

    function selectChip(amount, element) {
        selectedChipAmount = amount;
        document.querySelectorAll('.dt-casino-chip').forEach(c => c.classList.remove('active'));
        if (element) element.classList.add('active');
        document.getElementById('customBetInput').value = '';
    }

    function applyCustomBet() {
        const val = parseFloat(document.getElementById('customBetInput').value);
        if (val && val >= 5) {
            selectedChipAmount = val;
            document.querySelectorAll('.dt-casino-chip').forEach(c => c.classList.remove('active'));
        }
    }

    // Place bet handler
    function placeBetOn(betType) {
        @guest
            window.location.href = "{{ route('login') }}";
            return;
        @endguest

        if (isLocked) {
            showToast('Betting is locked for this round! Wait for next round.', 'danger');
            return;
        }

        const amount = selectedChipAmount;
        if (!amount || amount < 5) {
            showToast('Please select a valid chip amount (Min ₹5).', 'warning');
            return;
        }

        fetch(urls.placeBet, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                bet_type: betType,
                amount: amount
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                if (data.new_balance !== undefined) {
                    document.getElementById('userWalletBalance').textContent = '₹' + Number(data.new_balance).toFixed(2);
                }
                // Update bet badge
                const badge = document.getElementById('betBadge_' + betType);
                if (badge) {
                    badge.style.display = 'inline-block';
                    badge.textContent = '₹' + amount;
                }
                // Instantly refresh player's bet history table
                fetchMyBets();
            } else {
                showToast(data.message || 'Error placing bet.', 'danger');
            }
        })
        .catch(() => {
            showToast('Network error while placing bet.', 'danger');
        });
    }

    // Timer visual update
    function updateTimerUI(sec) {
        const timerSeconds = document.getElementById('timerSecondsDisplay');
        const timerLabel = document.getElementById('timerStatusLabel');
        const circle = document.getElementById('timerProgressCircle');
        const phaseBanner = document.getElementById('phaseBanner');
        const phaseText = document.getElementById('phaseText');

        timerSeconds.textContent = sec;

        const maxDash = 283;
        const progress = Math.max(0, Math.min(1, sec / roundDuration));
        circle.style.strokeDashoffset = maxDash * (1 - progress);

        if (sec <= 5) {
            isLocked = true;
            circle.style.stroke = '#ef4444';
            timerSeconds.style.color = '#ef4444';
            timerLabel.textContent = 'LOCKED';
            phaseBanner.className = 'dt-phase-banner bg-danger text-white';
            phaseText.textContent = 'BETS CLOSED • DEALING';
        } else {
            isLocked = false;
            circle.style.stroke = '#10b981';
            timerSeconds.style.color = '#ffffff';
            timerLabel.textContent = 'SEC';
            phaseBanner.className = 'dt-phase-banner';
            phaseText.textContent = 'BETTING OPEN';
        }
    }

    // Reveal cards animation
    function revealCards(dragonCard, tigerCard, winner) {
        if (!dragonCard || !tigerCard) return;

        // Populate Dragon Card
        document.getElementById('dCardVal').textContent = dragonCard.rank;
        document.getElementById('dCardValBot').textContent = dragonCard.rank;
        document.getElementById('dCardSymTop').textContent = dragonCard.symbol;
        document.getElementById('dCardSymBot').textContent = dragonCard.symbol;
        document.getElementById('dCardSymCenter').textContent = dragonCard.symbol;
        document.getElementById('cardDragonFront').style.color = dragonCard.color;
        document.getElementById('dragonCardTitle').textContent = dragonCard.title;

        // Populate Tiger Card
        document.getElementById('tCardVal').textContent = tigerCard.rank;
        document.getElementById('tCardValBot').textContent = tigerCard.rank;
        document.getElementById('tCardSymTop').textContent = tigerCard.symbol;
        document.getElementById('tCardSymBot').textContent = tigerCard.symbol;
        document.getElementById('tCardSymCenter').textContent = tigerCard.symbol;
        document.getElementById('cardTigerFront').style.color = tigerCard.color;
        document.getElementById('tigerCardTitle').textContent = tigerCard.title;

        // Flip both cards
        document.getElementById('cardDragonInner').classList.add('flipped');
        setTimeout(() => {
            document.getElementById('cardTigerInner').classList.add('flipped');
        }, 300);

        // Highlight winner
        setTimeout(() => {
            const zD = document.getElementById('zoneDragon');
            const zT = document.getElementById('zoneTiger');
            zD.classList.remove('winner-pulse');
            zT.classList.remove('winner-pulse');

            if (winner === 'dragon') {
                zD.classList.add('winner-pulse');
            } else if (winner === 'tiger') {
                zT.classList.add('winner-pulse');
            } else {
                zD.classList.add('winner-pulse');
                zT.classList.add('winner-pulse');
            }
        }, 700);
    }

    function resetCards() {
        document.getElementById('cardDragonInner').classList.remove('flipped');
        document.getElementById('cardTigerInner').classList.remove('flipped');
        document.getElementById('zoneDragon').classList.remove('winner-pulse');
        document.getElementById('zoneTiger').classList.remove('winner-pulse');
        document.getElementById('dragonCardTitle').textContent = '-';
        document.getElementById('tigerCardTitle').textContent = '-';
        document.querySelectorAll('.dt-bet-amount-badge').forEach(b => b.style.display = 'none');
    }

    // Realtime polling
    function syncStatus() {
        fetch(urls.status)
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;

            // Check round transition
            if (data.round_id !== currentRoundId) {
                currentRoundId = data.round_id;
                document.getElementById('activeRoundId').textContent = currentRoundId;
                resetCards();
                fetchMyBets();
            }

            remainingSeconds = data.remaining_seconds;
            isLocked = data.is_locked;
            roundDuration = data.duration;
            updateTimerUI(remainingSeconds);

            if (data.balance !== undefined && document.getElementById('userWalletBalance')) {
                document.getElementById('userWalletBalance').textContent = '₹' + Number(data.balance).toFixed(2);
            }

            // Sync active round bets on board
            if (data.my_active_bets && data.my_active_bets.length > 0) {
                data.my_active_bets.forEach(b => {
                    const badge = document.getElementById('betBadge_' + b.bet_type);
                    if (badge) {
                        badge.style.display = 'inline-block';
                        badge.textContent = '₹' + parseFloat(b.bet_amount).toFixed(0);
                    }
                });
            }

            // If last round ended, reveal cards & refresh tables
            if (data.last_round && data.last_round.round_id !== lastSettledRoundId) {
                lastSettledRoundId = data.last_round.round_id;
                revealCards(data.last_round.dragon_card, data.last_round.tiger_card, data.last_round.winner);
                updateRoadmap(data.roadmap);
                fetchMyBets();
                fetchHistory();
            }

            // Winning notification
            if (data.latest_win) {
                showToast(data.latest_win.message, 'success');
            }
        })
        .catch(() => {});
    }

    // Fetch and render authenticated player's personal bet history
    async function fetchMyBets() {
        try {
            const resp = await fetch(urls.myBets);
            if (resp.status === 401) return;
            const data = await resp.json();
            if (!data.success || !data.bets) return;

            const badge = document.getElementById('myBetsCountBadge');
            if (badge) badge.textContent = data.bets.length;

            const tbody = document.getElementById('myBetsTableBody');
            if (!tbody) return;

            if (data.bets.length === 0) {
                tbody.innerHTML = `<tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="fa-solid fa-coins fs-3 mb-2 text-warning d-block"></i>
                        <span class="text-secondary fw-bold">No bets placed yet. Place your bet on Dragon, Tiger or Tie to win up to 50X!</span>
                    </td>
                </tr>`;
                return;
            }

            let html = '';
            data.bets.forEach(b => {
                let typeCls = 'badge-side-bet', typeIcon = 'fa-dice';
                if (b.bet_type === 'dragon') { typeCls = 'badge-dragon-bet'; typeIcon = 'fa-dragon'; }
                else if (b.bet_type === 'tiger') { typeCls = 'badge-tiger-bet'; typeIcon = 'fa-paw'; }
                else if (b.bet_type === 'tie') { typeCls = 'badge-tie-bet'; typeIcon = 'fa-handshake'; }
                else if (b.bet_type === 'suited_tie') { typeCls = 'badge-suited-tie-bet'; typeIcon = 'fa-crown'; }

                let statusCls = 'bg-warning text-dark border border-warning';
                let payoutText = '<span class="badge bg-warning text-dark border border-warning">PENDING</span>';

                if (b.status === 'won') {
                    statusCls = 'bg-success text-white';
                    payoutText = `<span class="text-success fw-bold font-monospace fs-6">+₹${parseFloat(b.payout_amount).toFixed(2)}</span>`;
                } else if (b.status === 'refunded') {
                    statusCls = 'bg-info text-dark';
                    payoutText = `<span class="text-primary fw-bold font-monospace">₹${parseFloat(b.payout_amount).toFixed(2)} <span class="badge bg-info text-dark">50% Push</span></span>`;
                } else if (b.status === 'lost') {
                    statusCls = 'bg-danger text-white';
                    payoutText = '<span class="text-secondary font-monospace fw-semibold">₹0.00</span>';
                }

                let timeStr = '-';
                if (b.created_at) {
                    const dt = new Date(b.created_at);
                    timeStr = dt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                }

                const typeLabel = b.bet_type.replace(/_/g, ' ').toUpperCase();
                const outcome = b.hand_outcome || (b.round?.winner ? 'Winner: ' + b.round.winner.toUpperCase() : '-');

                html += `<tr>
                    <td class="font-monospace text-dark fw-bold">#${b.round_id}</td>
                    <td>
                        <span class="badge ${typeCls} px-2 py-1 rounded-pill">
                            <i class="fa-solid ${typeIcon} me-1"></i> ${typeLabel}
                        </span>
                    </td>
                    <td class="font-monospace fw-bold text-dark fs-6">₹${parseFloat(b.bet_amount).toFixed(2)}</td>
                    <td><span class="badge bg-warning bg-opacity-25 text-dark border border-warning border-opacity-50 font-monospace fw-bold">${parseFloat(b.odds).toFixed(2)}X</span></td>
                    <td>${payoutText}</td>
                    <td>
                        <span class="badge ${statusCls} rounded-pill px-2 py-1 text-uppercase">${b.status}</span>
                    </td>
                    <td class="small text-secondary font-monospace fw-semibold">${outcome}</td>
                    <td class="text-end text-secondary small font-monospace fw-medium">${timeStr}</td>
                </tr>`;
            });
            tbody.innerHTML = html;
        } catch (e) {
            console.error('Error fetching my bets:', e);
        }
    }

    // Fetch and render recent completed rounds history
    async function fetchHistory() {
        try {
            const resp = await fetch(urls.history);
            const data = await resp.json();
            if (!data.success || !data.history) return;

            const tbody = document.getElementById('roundHistoryTableBody');
            if (!tbody) return;

            if (data.history.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-secondary fw-bold">No completed rounds yet.</td></tr>';
                return;
            }

            let html = '';
            data.history.forEach(rh => {
                let winBadgeCls = 'bg-success text-white';
                if (rh.winner === 'dragon') winBadgeCls = 'bg-danger text-white';
                else if (rh.winner === 'tiger') winBadgeCls = 'bg-primary text-white';

                let dCardHtml = '-';
                if (rh.dragon_card && rh.dragon_card.rank) {
                    dCardHtml = `<span class="badge bg-white border border-secondary border-opacity-25 font-monospace fs-6 shadow-sm" style="color: ${rh.dragon_card.color || '#000'} !important;">${rh.dragon_card.rank}${rh.dragon_card.symbol || ''}</span>`;
                }

                let tCardHtml = '-';
                if (rh.tiger_card && rh.tiger_card.rank) {
                    tCardHtml = `<span class="badge bg-white border border-secondary border-opacity-25 font-monospace fs-6 shadow-sm" style="color: ${rh.tiger_card.color || '#000'} !important;">${rh.tiger_card.rank}${rh.tiger_card.symbol || ''}</span>`;
                }

                let handDetails = '-';
                if (rh.dragon_card?.rank && rh.tiger_card?.rank) {
                    handDetails = `${rh.dragon_card.rank} vs ${rh.tiger_card.rank}`;
                }

                let timeStr = '-';
                if (rh.created_at) {
                    const dt = new Date(rh.created_at);
                    timeStr = dt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                }

                html += `<tr>
                    <td class="font-monospace text-dark fw-bold">#${rh.round_id}</td>
                    <td>
                        <span class="badge ${winBadgeCls} px-2 py-1 rounded-pill text-uppercase">
                            ${rh.winner.toUpperCase()}
                        </span>
                    </td>
                    <td>${dCardHtml}</td>
                    <td>${tCardHtml}</td>
                    <td class="small text-secondary font-monospace fw-semibold">${handDetails}</td>
                    <td class="text-end text-secondary small font-monospace fw-medium">${timeStr}</td>
                </tr>`;
            });
            tbody.innerHTML = html;
        } catch (e) {
            console.error('Error fetching history:', e);
        }
    }

    function updateRoadmap(roadmap) {
        if (!roadmap || !roadmap.length) return;
        const grid = document.getElementById('beadGridContainer');
        if (!grid) return;

        let html = '';
        let dCount = 0, tCount = 0, tieCount = 0;

        roadmap.forEach(r => {
            let cls = 'bead-dragon';
            let label = 'D';
            if (r.winner === 'tiger') {
                cls = 'bead-tiger';
                label = 'T';
                tCount++;
            } else if (r.winner === 'tie') {
                cls = 'bead-tie';
                label = 'X';
                tieCount++;
            } else if (r.winner === 'suited_tie') {
                cls = 'bead-suited';
                label = 'S';
                tieCount++;
            } else {
                dCount++;
            }
            html += `<div class="dt-bead-cell ${cls}" title="Round #${r.round_id}: ${r.winner}">${label}</div>`;
        });

        grid.innerHTML = html;

        const total = Math.max(1, roadmap.length);
        document.getElementById('statDragonPct').textContent = Math.round((dCount / total) * 100) + '%';
        document.getElementById('statTigerPct').textContent = Math.round((tCount / total) * 100) + '%';
        document.getElementById('statTiePct').textContent = Math.round((tieCount / total) * 100) + '%';
    }

    function showToast(msg, type = 'success') {
        const toastEl = document.getElementById('winToast');
        const msgEl = document.getElementById('winToastMessage');
        if (!toastEl || !msgEl) return;

        toastEl.className = `toast align-items-center text-white bg-${type} border-0 shadow-lg`;
        msgEl.textContent = msg;

        const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
        toast.show();
    }

    // Start local countdown tick every 1s & AJAX sync every 1.5s
    document.addEventListener('DOMContentLoaded', () => {
        updateTimerUI(remainingSeconds);

        localTimerInterval = setInterval(() => {
            if (remainingSeconds > 0) {
                remainingSeconds--;
                updateTimerUI(remainingSeconds);
            }
        }, 1000);

        pollInterval = setInterval(syncStatus, 1500);

        // Initial fetch of tables
        fetchMyBets();
        fetchHistory();
    });
</script>
@endsection

