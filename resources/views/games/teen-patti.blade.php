@extends('layouts.app')

@section('title', '20-20 Teen Patti Live Casino - ' . ($setting->site_name ?? 'rupeerush'))

@section('styles')
<style>
    /* ----------------------------------------------------
       20-20 TEEN PATTI CASINO SUITE
       Luxury Emerald Green Felt Table & Gold Trims
       ---------------------------------------------------- */
    :root {
        --tp-felt: #0c432d;
        --tp-felt-dark: #062619;
        --tp-felt-light: #125e3f;
        --tp-gold: #d4af37;
        --tp-gold-light: #fbe277;
        --tp-gold-dark: #aa820a;
        --tp-player-a: #2563eb;
        --tp-player-a-glow: rgba(37, 99, 235, 0.55);
        --tp-player-b: #dc2626;
        --tp-player-b-glow: rgba(220, 38, 38, 0.55);
        --tp-tie: #059669;
        --tp-tie-glow: rgba(5, 150, 105, 0.55);
    }

    body {
        background-color: #030811;
        color: #f1f5f9;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .tp-arena-container {
        max-width: 1200px;
        margin: 0 auto;
        padding-bottom: 50px;
    }

    /* Top Navigation Glass Bar */
    .tp-topbar {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(7, 12, 24, 0.98) 100%);
        border: 1px solid rgba(212, 175, 55, 0.25);
        border-radius: 18px;
        backdrop-filter: blur(12px);
        padding: 10px 18px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
    }

    .tp-round-pill {
        background: rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(212, 175, 55, 0.4);
        border-radius: 50px;
        padding: 5px 14px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.85rem;
        color: var(--tp-gold-light);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Green Felt Gaming Table */
    .tp-felt-table {
        background: radial-gradient(ellipse at 50% 40%, var(--tp-felt-light) 0%, var(--tp-felt) 55%, var(--tp-felt-dark) 100%);
        border: 6px solid #2e1a0d;
        outline: 2px solid var(--tp-gold);
        border-radius: 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.7), inset 0 0 60px rgba(0, 0, 0, 0.6);
        padding: 12px 16px 14px;
        margin-top: 8px;
    }

    .tp-felt-table::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: radial-gradient(circle at center, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Dealer Box & Shoe */
    .tp-dealer-stage {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 5;
        margin-bottom: 8px;
    }

    .tp-shoe-box {
        background: linear-gradient(145deg, #1e293b, #0f172a);
        border: 1px solid var(--tp-gold);
        border-radius: 8px;
        padding: 3px 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 1px;
        color: var(--tp-gold-light);
        text-transform: uppercase;
    }

    /* Circular SVG Timer */
    .tp-timer-wrap {
        position: relative;
        width: 68px;
        height: 68px;
        margin: 4px 0;
    }

    .tp-timer-svg {
        transform: rotate(-90deg);
        width: 68px;
        height: 68px;
    }

    .tp-timer-circle-bg {
        fill: rgba(0, 0, 0, 0.45);
        stroke: rgba(255, 255, 255, 0.12);
        stroke-width: 7;
    }

    .tp-timer-circle-progress {
        fill: none;
        stroke: #10b981;
        stroke-width: 7;
        stroke-linecap: round;
        stroke-dasharray: 283;
        stroke-dashoffset: 0;
        transition: stroke-dashoffset 0.9s linear, stroke 0.4s ease;
    }

    .tp-timer-center-text {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .tp-timer-seconds {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.35rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.8);
    }

    .tp-timer-label {
        font-size: 0.55rem;
        font-weight: 700;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.7);
        letter-spacing: 0.5px;
    }

    /* Live Phase Banner */
    .tp-phase-banner {
        background: rgba(0, 0, 0, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 30px;
        padding: 3px 14px;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        transition: all 0.3s ease;
    }

    /* ====================================================
       3D PLAYING CARDS & DEALING ARENA
       ==================================================== */
    .tp-battle-arena {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 14px;
        align-items: stretch;
        position: relative;
        z-index: 4;
    }

    /* Team Zone Card (Player A / Player B) */
    .tp-team-zone {
        background: rgba(0, 0, 0, 0.4);
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 22px;
        padding: 18px 14px;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        transition: all 0.35s ease;
    }

    .tp-zone-a {
        border-color: rgba(37, 99, 235, 0.45);
        box-shadow: inset 0 0 40px rgba(37, 99, 235, 0.15);
    }
    .tp-zone-a.winner-pulse {
        border-color: #60a5fa;
        box-shadow: 0 0 35px var(--tp-player-a-glow), inset 0 0 50px rgba(37, 99, 235, 0.3);
    }

    .tp-zone-b {
        border-color: rgba(220, 38, 38, 0.45);
        box-shadow: inset 0 0 40px rgba(220, 38, 38, 0.15);
    }
    .tp-zone-b.winner-pulse {
        border-color: #f87171;
        box-shadow: 0 0 35px var(--tp-player-b-glow), inset 0 0 50px rgba(220, 38, 38, 0.3);
    }

    .tp-zone-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        margin-bottom: 8px;
        padding: 0 6px;
        min-height: 26px;
        height: 26px;
    }

    .tp-zone-title {
        font-size: 1.05rem;
        font-weight: 800;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }

    .tp-rank-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        background: rgba(0, 0, 0, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #f8fafc;
        height: 22px;
        line-height: 18px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s;
        flex-shrink: 0;
    }

    /* 3-Card Rack Container */
    .tp-cards-rack {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        height: 94px;
        min-height: 94px;
        max-height: 94px;
        margin-bottom: 8px;
        position: relative;
    }

    /* 3D Flip Card Container */
    .tp-card-3d {
        width: 64px;
        height: 94px;
        position: relative;
        transform-style: preserve-3d;
        -webkit-transform-style: preserve-3d;
        transition: transform 0.5s ease-in-out;
        transform-origin: 50% 50%;
        border-radius: 8px;
        flex-shrink: 0;
        perspective: 800px;
        -webkit-perspective: 800px;
    }

    .tp-card-3d.flipped {
        transform: rotateY(180deg);
    }

    .tp-card-face {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100% !important;
        height: 100% !important;
        -webkit-backface-visibility: hidden !important;
        backface-visibility: hidden !important;
        border-radius: 8px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.5);
        overflow: hidden;
        box-sizing: border-box !important;
        margin: 0 !important;
    }

    /* Card Back (Classic Royal Red / Pattern) */
    .tp-card-back {
        background: linear-gradient(135deg, #7f1d1d 0%, #450a0a 100%);
        border: 2px solid var(--tp-gold);
        display: flex;
        align-items: center;
        justify-content: center;
        transform: rotateY(0deg);
        -webkit-transform: rotateY(0deg);
        z-index: 2;
    }

    .tp-card-back::before {
        content: '';
        position: absolute;
        width: 84%;
        height: 88%;
        border: 1px dashed rgba(212, 175, 55, 0.5);
        border-radius: 6px;
    }

    .tp-card-back-icon {
        font-size: 1.15rem;
        color: var(--tp-gold-light);
        opacity: 0.9;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6);
    }

    /* Card Front */
    .tp-card-front {
        background: #ffffff;
        color: #0f172a;
        transform: rotateY(180deg);
        -webkit-transform: rotateY(180deg);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 4px 5px;
        border: 2px solid #cbd5e1;
        user-select: none;
        z-index: 1;
    }

    .tp-card-front.card-red {
        color: #dc2626;
    }
    .tp-card-front.card-black {
        color: #0f172a;
    }

    .tp-card-corner-top, .tp-card-corner-bottom {
        line-height: 1;
        font-weight: 800;
        font-family: 'JetBrains Mono', 'Courier New', monospace;
        font-size: 0.8rem;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .tp-card-corner-bottom {
        transform: rotate(180deg);
    }

    .tp-card-suit-center {
        font-size: 1.45rem;
        line-height: 1;
        text-align: center;
    }

    /* Bet Buttons on Zones */
    .tp-bet-slot-btn {
        width: 100%;
        background: linear-gradient(145deg, rgba(15, 23, 42, 0.8), rgba(7, 12, 24, 0.95));
        border: 2px solid rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        padding: 8px 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: left;
        position: relative;
        overflow: hidden;
        user-select: none;
    }

    .tp-bet-slot-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        border-color: var(--tp-gold);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
    }

    .tp-bet-slot-btn.active-target {
        border-color: var(--tp-gold);
        background: linear-gradient(145deg, rgba(212, 175, 55, 0.18), rgba(7, 12, 24, 0.95));
        box-shadow: 0 0 20px rgba(212, 175, 55, 0.35);
    }

    .tp-bet-slot-btn:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    .tp-bet-btn-a {
        border-left: 5px solid var(--tp-player-a);
    }
    .tp-bet-btn-b {
        border-left: 5px solid var(--tp-player-b);
    }
    .tp-bet-btn-tie {
        border-left: 5px solid var(--tp-tie);
    }

    .tp-bet-title {
        font-size: 0.95rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: #ffffff;
    }

    .tp-bet-odds {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.85rem;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 20px;
    }

    /* Center Tie Zone Column */
    .tp-tie-zone-col {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-width: 140px;
        gap: 8px;
        padding: 0 4px;
    }

    .tp-tie-box {
        background: radial-gradient(circle, rgba(5, 150, 105, 0.25) 0%, rgba(0, 0, 0, 0.6) 80%);
        border: 2px dashed rgba(5, 150, 105, 0.6);
        border-radius: 14px;
        padding: 10px 10px;
        width: 100%;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .tp-tie-box:hover:not(.locked) {
        border-color: #34d399;
        transform: scale(1.03);
        box-shadow: 0 0 25px rgba(5, 150, 105, 0.4);
    }

    .tp-tie-box.active-target {
        border-color: var(--tp-gold);
        border-style: solid;
        box-shadow: 0 0 25px rgba(212, 175, 55, 0.5);
    }

    /* Pair Plus Side Bet Cards */
    .tp-pair-plus-card {
        background: rgba(0, 0, 0, 0.45);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 10px;
        padding: 6px 10px;
        width: 100%;
        margin-top: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .tp-pair-plus-card:hover:not(.locked) {
        border-color: var(--tp-gold);
        background: rgba(212, 175, 55, 0.08);
    }

    .tp-pair-plus-card.active-target {
        border-color: var(--tp-gold);
        background: rgba(212, 175, 55, 0.2);
    }

    /* Active Chips On Bet Slot */
    .tp-chip-badge {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.75rem;
        font-weight: 700;
        background: var(--tp-gold);
        color: #0f172a;
        padding: 2px 7px;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.4);
    }

    /* ====================================================
       CASINO CHIPS TRAY & BETTING CONTROLS
       ==================================================== */
    .tp-controls-panel {
        background: linear-gradient(135deg, #0e1726 0%, #060a12 100%);
        border: 1px solid rgba(212, 175, 55, 0.25);
        border-radius: 18px;
        padding: 10px 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7);
        margin-top: 10px;
    }

    /* 3D Casino Chips */
    .tp-chips-tray {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        padding: 3px 2px 6px;
    }

    .tp-casino-chip {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        font-size: 0.76rem;
        cursor: pointer;
        position: relative;
        user-select: none;
        flex-shrink: 0;
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease;
        border: 3px dashed rgba(255, 255, 255, 0.35);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.6), inset 0 2px 4px rgba(255, 255, 255, 0.3), inset 0 -2px 6px rgba(0, 0, 0, 0.6);
    }

    .tp-casino-chip:hover {
        transform: translateY(-4px) scale(1.05);
    }

    .tp-casino-chip.selected {
        transform: translateY(-6px) scale(1.1);
        box-shadow: 0 10px 25px rgba(212, 175, 55, 0.7), 0 0 15px var(--tp-gold-light);
        border-color: #ffffff;
    }

    /* Chip Color Themes */
    .chip-10 { background: radial-gradient(circle, #e2e8f0 30%, #94a3b8 100%); color: #0f172a; }
    .chip-50 { background: radial-gradient(circle, #f87171 30%, #b91c1c 100%); color: #ffffff; }
    .chip-100 { background: radial-gradient(circle, #60a5fa 30%, #1d4ed8 100%); color: #ffffff; }
    .chip-500 { background: radial-gradient(circle, #c084fc 30%, #7e22ce 100%); color: #ffffff; }
    .chip-1000 { background: radial-gradient(circle, #34d399 30%, #047857 100%); color: #ffffff; }
    .chip-5000 { background: radial-gradient(circle, #f59e0b 30%, #78350f 100%); color: #ffffff; border-color: var(--tp-gold-light); }

    /* Action Bet Input & Submit */
    .tp-amount-input {
        background: #090d16;
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #ffffff;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        font-size: 1rem;
        border-radius: 10px;
        padding: 6px 10px;
    }
    .tp-amount-input:focus {
        background: #090d16;
        color: #ffffff;
        border-color: var(--tp-gold);
        box-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
    }

    .btn-tp-place-bet {
        background: linear-gradient(135deg, var(--tp-gold) 0%, var(--tp-gold-dark) 100%);
        color: #0f172a;
        font-weight: 800;
        font-size: 1rem;
        letter-spacing: 0.5px;
        border: none;
        border-radius: 12px;
        padding: 8px 18px;
        box-shadow: 0 6px 20px rgba(212, 175, 55, 0.45);
        transition: all 0.25s ease;
        width: 100%;
        height: 42px;
    }
    .btn-tp-place-bet:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(212, 175, 55, 0.65);
        color: #000000;
    }
    .btn-tp-place-bet:disabled {
        background: #334155;
        color: #94a3b8;
        box-shadow: none;
    }

    /* ====================================================
       ROADMAP & BEAD PLATE
       ==================================================== */
    .tp-roadmap-card {
        background: rgba(10, 16, 28, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 14px 18px;
        margin-top: 16px;
    }

    .tp-beads-rail {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 6px;
    }

    .tp-bead-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.85rem;
        font-family: 'JetBrains Mono', monospace;
        flex-shrink: 0;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.5);
    }

    .tp-bead-a {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: #ffffff;
        border: 1px solid #93c5fd;
    }
    .tp-bead-b {
        background: linear-gradient(135deg, #ef4444, #b91c1c);
        color: #ffffff;
        border: 1px solid #fca5a5;
    }
    .tp-bead-tie {
        background: linear-gradient(135deg, #10b981, #047857);
        color: #ffffff;
        border: 1px solid #6ee7b7;
    }

    /* Percentage Bar */
    .tp-win-ratio-bar {
        height: 8px;
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        margin-top: 10px;
    }

    /* ====================================================
       BOTTOM TABS: MY BETS, HISTORY, RULES
       ==================================================== */
    .tp-bottom-data-card {
        color: #94a3b8 !important;
    }
    .tp-nav-tabs .nav-link {
        color: #94a3b8 !important;
        border: none;
        border-bottom: 3px solid transparent;
        font-weight: 700;
        padding: 12px 18px;
        background: transparent !important;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }
    .tp-nav-tabs .nav-link:hover {
        color: #cbd5e1 !important;
    }
    .tp-nav-tabs .nav-link.active {
        color: var(--tp-gold-light) !important;
        border-bottom-color: var(--tp-gold) !important;
        background: transparent !important;
    }

    .tp-table {
        color: #94a3b8 !important;
        vertical-align: middle;
        font-size: 0.9rem;
    }
    .tp-table th {
        background: rgba(15, 23, 42, 0.7);
        color: #94a3b8 !important;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 12px;
    }
    .tp-table td {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding: 12px;
        color: #94a3b8 !important;
    }
    .tp-bottom-data-card .text-white,
    .tp-bottom-data-card .text-light,
    .tp-bottom-data-card .text-white-50 {
        color: #94a3b8 !important;
    }
    .tp-bottom-data-card p {
        color: #94a3b8 !important;
    }

    /* Hand Rank Guide Cards */
    .tp-rule-row {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        padding: 12px 16px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #94a3b8 !important;
    }
    /* ====================================================
       MOBILE RESPONSIVE SUITE (MAX 768px)
       Guarantees single screen fit with zero horizontal overflow
       ==================================================== */
    @media (max-width: 768px) {
        .tp-arena-container {
            padding: 0 4px 75px !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
        }
        .tp-topbar {
            padding: 4px 8px !important;
            border-radius: 10px !important;
            margin-bottom: 4px !important;
        }
        .tp-topbar .fs-5 {
            display: none !important;
        }
        .tp-round-pill {
            padding: 2px 8px !important;
            font-size: 0.72rem !important;
        }
        .wallet-pill {
            padding: 2px 8px !important;
            font-size: 0.75rem !important;
        }

        /* Compact Felt Table */
        .tp-felt-table {
            border: 2px solid #2e1a0d !important;
            outline: 1px solid var(--tp-gold) !important;
            border-radius: 12px !important;
            padding: 5px 4px !important;
            margin-top: 4px !important;
            box-shadow: none !important;
            width: 100% !important;
            overflow: hidden !important;
        }
        .tp-dealer-stage {
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 2px 4px !important;
            margin-bottom: 4px !important;
            gap: 6px !important;
            width: 100% !important;
        }
        .tp-shoe-box {
            display: none !important;
        }
        .tp-timer-wrap {
            width: 32px !important;
            height: 32px !important;
            margin: 0 !important;
            flex-shrink: 0 !important;
        }
        .tp-timer-svg {
            width: 32px !important;
            height: 32px !important;
        }
        .tp-timer-seconds {
            font-size: 0.95rem !important;
        }
        .tp-timer-label {
            display: none !important;
        }
        .tp-phase-banner {
            padding: 3px 8px !important;
            font-size: 0.68rem !important;
            border-radius: 20px !important;
            white-space: nowrap !important;
        }

        /* 2-Column Battle Arena (Minmax prevents grid blowout) */
        .tp-battle-arena {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
            gap: 4px !important;
            width: 100% !important;
            align-items: stretch !important;
        }

        .tp-team-zone {
            min-width: 0 !important;
            width: 100% !important;
            padding: 5px 3px !important;
            border-radius: 10px !important;
            border-width: 1.5px !important;
            box-shadow: none !important;
        }

        .tp-zone-header {
            padding: 0 2px !important;
            margin-bottom: 3px !important;
            height: 20px !important;
            min-height: 20px !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100% !important;
        }
        .tp-zone-title {
            font-size: 0.74rem !important;
            gap: 3px !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
        }
        .tp-rank-badge {
            font-size: 0.56rem !important;
            padding: 1px 4px !important;
            height: 18px !important;
            line-height: 16px !important;
            max-width: 68px !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }

        .tp-cards-rack {
            gap: 2px !important;
            height: 56px !important;
            min-height: 56px !important;
            max-height: 56px !important;
            margin-bottom: 5px !important;
            justify-content: center !important;
        }
        .tp-card-3d {
            width: 36px !important;
            height: 54px !important;
            border-radius: 4px !important;
            flex-shrink: 0 !important;
        }
        .tp-card-face {
            border-radius: 4px !important;
            padding: 2px 2px !important;
            border-width: 1px !important;
        }
        .tp-card-corner-top, .tp-card-corner-bottom {
            font-size: 0.55rem !important;
            line-height: 1 !important;
        }
        .tp-card-suit-center {
            font-size: 0.88rem !important;
        }
        .tp-card-back-icon {
            font-size: 0.75rem !important;
        }

        .tp-bet-slot-btn {
            padding: 4px 5px !important;
            border-radius: 8px !important;
            margin-bottom: 3px !important;
            width: 100% !important;
        }
        .tp-bet-title {
            font-size: 0.74rem !important;
        }
        .tp-bet-odds {
            font-size: 0.68rem !important;
            padding: 1px 4px !important;
            border-radius: 5px !important;
        }
        .tp-chip-badge {
            font-size: 0.62rem !important;
            padding: 1px 4px !important;
        }

        .tp-pair-plus-card {
            padding: 3px 4px !important;
            margin-top: 2px !important;
            border-radius: 6px !important;
            font-size: 0.66rem !important;
            width: 100% !important;
        }
        .tp-pair-plus-card .text-white-50 {
            display: none !important;
        }
        .tp-pair-plus-card .badge {
            font-size: 0.58rem !important;
            padding: 1px 3px !important;
        }

        /* Horizontal Slim TIE Bar */
        .tp-tie-zone-col {
            grid-column: 1 / -1 !important;
            min-width: 0 !important;
            width: 100% !important;
            order: 2 !important;
            padding: 0 !important;
            margin-top: 2px !important;
            gap: 0 !important;
        }
        .tp-tie-zone-col .badge.bg-dark {
            display: none !important;
        }
        .tp-tie-box {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 3px 8px !important;
            border-radius: 8px !important;
            width: 100% !important;
        }
        .tp-tie-box > div:first-child {
            display: none !important;
        }
        .tp-tie-box .fs-6 {
            font-size: 0.78rem !important;
            margin: 0 !important;
        }
        .tp-tie-box #oddsTie {
            margin: 0 !important;
            padding: 1px 6px !important;
            font-size: 0.72rem !important;
        }
        .tp-tie-box .text-white-50 {
            display: none !important;
        }
        .tp-tie-box .mt-2 {
            margin: 0 !important;
        }

        /* Controls Panel */
        .tp-controls-panel {
            padding: 6px 8px !important;
            border-radius: 12px !important;
            margin-top: 5px !important;
            width: 100% !important;
        }
        .tp-chips-tray {
            display: flex !important;
            justify-content: space-between !important;
            gap: 4px !important;
            padding: 2px 0 3px !important;
            overflow-x: visible !important;
            width: 100% !important;
        }
        .tp-casino-chip {
            width: 36px !important;
            height: 36px !important;
            font-size: 0.68rem !important;
            border-width: 2px !important;
            flex: 1 1 0 !important;
            max-width: 48px !important;
        }
        .tp-casino-chip.selected {
            transform: translateY(-2px) scale(1.05) !important;
        }
        .tp-amount-input {
            padding: 3px 6px !important;
            font-size: 0.88rem !important;
            height: 34px !important;
        }
        .btn-tp-place-bet {
            height: 40px !important;
            font-size: 0.92rem !important;
            border-radius: 10px !important;
            padding: 5px 12px !important;
            margin-top: 3px !important;
        }
    }
</style>
@endsection

@section('content')
<div class="tp-arena-container py-1 py-md-2 px-1 px-md-3">

    <!-- TOP BAR NAVIGATION -->
    <div class="tp-topbar d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1">
                <i class="fa-solid fa-arrow-left me-1"></i> Lobby
            </a>
            <div class="d-none d-sm-flex align-items-center gap-2">
                <span class="fs-5 fw-bold text-white tracking-wide">20-20 TEEN PATTI</span>
                <span class="badge bg-danger rounded-pill px-2 py-1 animate__animated animate__pulse animate__infinite">
                    <i class="fa-solid fa-circle-dot me-1"></i> LIVE
                </span>
            </div>
        </div>

        <!-- Center: Round Pill -->
        <div class="tp-round-pill">
            <i class="fa-solid fa-shield-halved text-warning"></i>
            <span>ROUND #<span id="liveRoundId">{{ $roundData['round_id'] }}</span></span>
        </div>

        <!-- Right Side: Wallet, Sound & Rules -->
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-secondary rounded-circle" id="soundToggleBtn" onclick="toggleAudio()" title="Sound Toggle" style="width: 36px; height: 36px;">
                <i class="fa-solid fa-volume-high" id="soundIcon"></i>
            </button>
            <button class="btn btn-sm btn-outline-info rounded-circle" data-bs-toggle="modal" data-bs-target="#rulesModal" title="Rules & Hand Ranks" style="width: 36px; height: 36px;">
                <i class="fa-solid fa-circle-question"></i>
            </button>

            @auth
            <a href="{{ route('wallet.deposit') }}" class="wallet-pill" title="My Balance">
                <i class="fa-solid fa-wallet text-info"></i>
                <span class="wallet-amt" id="headerUserBalance">₹{{ number_format(Auth::user()->balance, 2) }}</span>
                <span class="badge bg-success rounded-circle p-1"><i class="fa-solid fa-plus text-white"></i></span>
            </a>
            @else
            <a href="{{ route('login') }}" class="btn btn-sm btn-warning rounded-pill px-3 py-1 fw-bold">
                <i class="fa-solid fa-right-to-bracket me-1"></i> Login
            </a>
            @endauth
        </div>
    </div>

    <!-- MAIN CASINO FELT TABLE -->
    <div class="tp-felt-table">

        <!-- DEALER STAGE & TIME-SYNCED CIRCULAR TIMER -->
        <div class="tp-dealer-stage">
            <div class="tp-shoe-box">
                <i class="fa-solid fa-diamond text-warning"></i>
                <span>VIP LIVE DEALER SHOE</span>
                <i class="fa-solid fa-diamond text-warning"></i>
            </div>

            <!-- SVG 25-Second Countdown Ring -->
            <div class="tp-timer-wrap">
                <svg class="tp-timer-svg" viewBox="0 0 100 100">
                    <circle class="tp-timer-circle-bg" cx="50" cy="50" r="45"></circle>
                    <circle class="tp-timer-circle-progress" id="timerCircleProgress" cx="50" cy="50" r="45"></circle>
                </svg>
                <div class="tp-timer-center-text">
                    <div class="tp-timer-seconds" id="liveCountdown">
                        {{ sprintf('%02d', $roundData['remaining_seconds']) }}
                    </div>
                    <div class="tp-timer-label" id="timerLabel">SECONDS</div>
                </div>
            </div>

            <!-- Status Banner -->
            <div class="tp-phase-banner" id="phaseBanner">
                @if($roundData['is_locked'])
                    <span class="text-danger"><i class="fa-solid fa-lock me-1"></i> BETS CLOSED • DEALING CARDS</span>
                @else
                    <span class="text-success"><i class="fa-solid fa-circle-check me-1"></i> BETTING OPEN • PLACE CHIPS</span>
                @endif
            </div>
        </div>

        <!-- 3D CARDS DEALING BATTLE ARENA -->
        <div class="tp-battle-arena">

            <!-- ================= PLAYER A ZONE ================= -->
            <div class="tp-team-zone tp-zone-a" id="zonePlayerA">
                <div class="tp-zone-header">
                    <div class="tp-zone-title text-primary">
                        <i class="fa-solid fa-shield-cat"></i>
                        <span>PLAYER A</span>
                    </div>
                    <div class="tp-rank-badge" id="playerARankBadge">
                        Waiting for deal...
                    </div>
                </div>

                <!-- 3 Cards for Player A -->
                <div class="tp-cards-rack" id="playerACardsRack">
                    <!-- Card 1 -->
                    <div class="tp-card-3d" id="cardA_0">
                        <div class="tp-card-face tp-card-back">
                            <i class="fa-solid fa-crown tp-card-back-icon"></i>
                        </div>
                        <div class="tp-card-face tp-card-front" id="cardAFront_0">
                            <div class="tp-card-corner-top"><span class="r">A</span><span class="s">♠</span></div>
                            <div class="tp-card-suit-center">♠</div>
                            <div class="tp-card-corner-bottom"><span class="r">A</span><span class="s">♠</span></div>
                        </div>
                    </div>
                    <!-- Card 2 -->
                    <div class="tp-card-3d" id="cardA_1">
                        <div class="tp-card-face tp-card-back">
                            <i class="fa-solid fa-crown tp-card-back-icon"></i>
                        </div>
                        <div class="tp-card-face tp-card-front" id="cardAFront_1">
                            <div class="tp-card-corner-top"><span class="r">A</span><span class="s">♥</span></div>
                            <div class="tp-card-suit-center">♥</div>
                            <div class="tp-card-corner-bottom"><span class="r">A</span><span class="s">♥</span></div>
                        </div>
                    </div>
                    <!-- Card 3 -->
                    <div class="tp-card-3d" id="cardA_2">
                        <div class="tp-card-face tp-card-back">
                            <i class="fa-solid fa-crown tp-card-back-icon"></i>
                        </div>
                        <div class="tp-card-face tp-card-front" id="cardAFront_2">
                            <div class="tp-card-corner-top"><span class="r">A</span><span class="s">♦</span></div>
                            <div class="tp-card-suit-center">♦</div>
                            <div class="tp-card-corner-bottom"><span class="r">A</span><span class="s">♦</span></div>
                        </div>
                    </div>
                </div>

                <!-- Main Bet: Player A Button -->
                <button type="button" class="tp-bet-slot-btn tp-bet-btn-a active-target mb-2" id="btnBetPlayerA" onclick="selectBetTarget('player_a')">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="tp-bet-title text-primary"><i class="fa-solid fa-user me-1"></i> PLAYER A</span>
                        <span class="tp-bet-odds bg-primary text-white" id="oddsPlayerA">{{ number_format($setting->teen_patti_odds_player ?? 1.95, 2) }}X</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="small text-white-50">Pool: ₹<span id="poolPlayerA">{{ number_format($roundData['bets_summary']['player_a']['total_amount'] ?? 0, 0) }}</span></span>
                        <span class="tp-chip-badge" id="myBetBadgeA" style="display: none;">My Bet: ₹<span id="myBetAmtA">0</span></span>
                    </div>
                </button>

                <!-- Side Bet: Pair Plus A -->
                <div class="tp-pair-plus-card" id="btnBetPairPlusA" onclick="selectBetTarget('pair_plus_a')">
                    <div>
                        <div class="small fw-bold text-light"><i class="fa-solid fa-sparkles text-warning me-1"></i> PAIR PLUS A</div>
                        <div class="text-white-50" style="font-size: 0.72rem;">Pair or better • Up to 40X</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark font-monospace fw-bold">UP TO 40X</span>
                        <span class="tp-chip-badge" id="myBetBadgePPA" style="display: none;">₹<span id="myBetAmtPPA">0</span></span>
                    </div>
                </div>
            </div>

            <!-- ================= CENTER TIE ZONE ================= -->
            <div class="tp-tie-zone-col">
                <div class="tp-tie-box" id="btnBetTie" onclick="selectBetTarget('tie')">
                    <div class="text-warning mb-1" style="font-size: 1.4rem;">
                        <i class="fa-solid fa-handshake"></i>
                    </div>
                    <div class="fw-bold text-white fs-6">TIE</div>
                    <div class="badge bg-success font-monospace fw-bold px-3 py-1 my-1" id="oddsTie">
                        {{ number_format($setting->teen_patti_odds_tie ?? 9.00, 2) }}X
                    </div>
                    <div class="small text-white-50 mt-1" style="font-size: 0.72rem;">
                        If Tie, A & B bets refunded!
                    </div>
                    <div class="mt-2">
                        <span class="tp-chip-badge" id="myBetBadgeTie" style="display: none;">My Bet: ₹<span id="myBetAmtTie">0</span></span>
                    </div>
                </div>

                <div class="text-center">
                    <span class="badge bg-dark border border-secondary text-white-50 px-3 py-2 rounded-pill" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-bolt text-warning me-1"></i> Fast 25s Game
                    </span>
                </div>
            </div>

            <!-- ================= PLAYER B ZONE ================= -->
            <div class="tp-team-zone tp-zone-b" id="zonePlayerB">
                <div class="tp-zone-header">
                    <div class="tp-zone-title text-danger">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>PLAYER B</span>
                    </div>
                    <div class="tp-rank-badge" id="playerBRankBadge">
                        Waiting for deal...
                    </div>
                </div>

                <!-- 3 Cards for Player B -->
                <div class="tp-cards-rack" id="playerBCardsRack">
                    <!-- Card 1 -->
                    <div class="tp-card-3d" id="cardB_0">
                        <div class="tp-card-face tp-card-back">
                            <i class="fa-solid fa-crown tp-card-back-icon"></i>
                        </div>
                        <div class="tp-card-face tp-card-front" id="cardBFront_0">
                            <div class="tp-card-corner-top"><span class="r">K</span><span class="s">♠</span></div>
                            <div class="tp-card-suit-center">♠</div>
                            <div class="tp-card-corner-bottom"><span class="r">K</span><span class="s">♠</span></div>
                        </div>
                    </div>
                    <!-- Card 2 -->
                    <div class="tp-card-3d" id="cardB_1">
                        <div class="tp-card-face tp-card-back">
                            <i class="fa-solid fa-crown tp-card-back-icon"></i>
                        </div>
                        <div class="tp-card-face tp-card-front" id="cardBFront_1">
                            <div class="tp-card-corner-top"><span class="r">K</span><span class="s">♥</span></div>
                            <div class="tp-card-suit-center">♥</div>
                            <div class="tp-card-corner-bottom"><span class="r">K</span><span class="s">♥</span></div>
                        </div>
                    </div>
                    <!-- Card 3 -->
                    <div class="tp-card-3d" id="cardB_2">
                        <div class="tp-card-face tp-card-back">
                            <i class="fa-solid fa-crown tp-card-back-icon"></i>
                        </div>
                        <div class="tp-card-face tp-card-front" id="cardBFront_2">
                            <div class="tp-card-corner-top"><span class="r">K</span><span class="s">♦</span></div>
                            <div class="tp-card-suit-center">♦</div>
                            <div class="tp-card-corner-bottom"><span class="r">K</span><span class="s">♦</span></div>
                        </div>
                    </div>
                </div>

                <!-- Main Bet: Player B Button -->
                <button type="button" class="tp-bet-slot-btn tp-bet-btn-b mb-2" id="btnBetPlayerB" onclick="selectBetTarget('player_b')">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="tp-bet-title text-danger"><i class="fa-solid fa-user me-1"></i> PLAYER B</span>
                        <span class="tp-bet-odds bg-danger text-white" id="oddsPlayerB">{{ number_format($setting->teen_patti_odds_player ?? 1.95, 2) }}X</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="small text-white-50">Pool: ₹<span id="poolPlayerB">{{ number_format($roundData['bets_summary']['player_b']['total_amount'] ?? 0, 0) }}</span></span>
                        <span class="tp-chip-badge" id="myBetBadgeB" style="display: none;">My Bet: ₹<span id="myBetAmtB">0</span></span>
                    </div>
                </button>

                <!-- Side Bet: Pair Plus B -->
                <div class="tp-pair-plus-card" id="btnBetPairPlusB" onclick="selectBetTarget('pair_plus_b')">
                    <div>
                        <div class="small fw-bold text-light"><i class="fa-solid fa-sparkles text-warning me-1"></i> PAIR PLUS B</div>
                        <div class="text-white-50" style="font-size: 0.72rem;">Pair or better • Up to 40X</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark font-monospace fw-bold">UP TO 40X</span>
                        <span class="tp-chip-badge" id="myBetBadgePPB" style="display: none;">₹<span id="myBetAmtPPB">0</span></span>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- BET CONTROLS & CASINO CHIP TRAY PANEL -->
    <div class="tp-controls-panel">
        <div class="row g-3 align-items-center">

            <!-- Chips Selector Column -->
            <div class="col-12 col-lg-7">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-bold text-white-50 text-uppercase" style="letter-spacing: 0.5px;">
                        <i class="fa-solid fa-coins text-warning me-1"></i> Select Chip:
                    </span>
                    <span class="small text-warning fw-bold" id="selectedTargetDisplay">Target: PLAYER A (1.95X)</span>
                </div>

                <div class="tp-chips-tray">
                    <div class="tp-casino-chip chip-10" onclick="selectChip(10)">₹10</div>
                    <div class="tp-casino-chip chip-50" onclick="selectChip(50)">₹50</div>
                    <div class="tp-casino-chip chip-100 selected" onclick="selectChip(100)">₹100</div>
                    <div class="tp-casino-chip chip-500" onclick="selectChip(500)">₹500</div>
                    <div class="tp-casino-chip chip-1000" onclick="selectChip(1000)">₹1K</div>
                    <div class="tp-casino-chip chip-5000" onclick="selectChip(5000)">₹5K</div>
                </div>
            </div>

            <!-- Custom Amount, Quick Modifiers & Bet Button Column -->
            <div class="col-12 col-lg-5">
                <div class="row g-2 align-items-center">
                    <div class="col-6">
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-warning fw-bold">₹</span>
                            <input type="number" class="form-control tp-amount-input" id="customAmountInput" value="100" min="{{ $setting->teen_patti_min_bet ?? 5 }}" max="{{ $setting->teen_patti_max_bet ?? 25000 }}" oninput="onCustomAmountChange(this.value)">
                        </div>
                    </div>
                    <div class="col-3">
                        <button type="button" class="btn btn-outline-secondary w-100 fw-bold" onclick="multiplyAmount(2)">2X</button>
                    </div>
                    <div class="col-3">
                        <button type="button" class="btn btn-outline-danger w-100 fw-bold" onclick="clearAmount()">Clear</button>
                    </div>
                    <div class="col-12 mt-2">
                        <button type="button" class="btn-tp-place-bet" id="placeBetSubmitBtn" onclick="submitBet()">
                            <i class="fa-solid fa-bolt me-1"></i> PLACE BET
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ROADMAP & BEAD PLATE -->
    <div class="tp-roadmap-card">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="small fw-bold text-white-50 text-uppercase">
                <i class="fa-solid fa-chart-simple text-info me-1"></i> Live Streak Roadmap (Last 20 Rounds)
            </div>
            <div class="small text-white-50">
                <span class="text-primary fw-bold me-2">A: <span id="statPercentA">{{ $aPercent }}</span>%</span>
                <span class="text-success fw-bold me-2">T: <span id="statPercentTie">{{ $tiePercent }}</span>%</span>
                <span class="text-danger fw-bold">B: <span id="statPercentB">{{ $bPercent }}</span>%</span>
            </div>
        </div>

        <div class="tp-beads-rail" id="beadsRailContainer">
            @forelse($recentHistory as $rh)
                @php
                    $isA = $rh->winner === 'player_a';
                    $isB = $rh->winner === 'player_b';
                    $cls = $isA ? 'tp-bead-a' : ($isB ? 'tp-bead-b' : 'tp-bead-tie');
                    $letter = $isA ? 'A' : ($isB ? 'B' : 'T');
                @endphp
                <div class="tp-bead-circle {{ $cls }}" title="Round #{{ $rh->round_id }}: {{ strtoupper(str_replace('_', ' ', $rh->winner)) }} ({{ $rh->winning_rank_title ?? 'Win' }})">
                    {{ $letter }}
                </div>
            @empty
                <div class="text-white-50 small py-2">No completed rounds recorded yet.</div>
            @endforelse
        </div>

        <!-- Proportional Win Ratio Bar -->
        <div class="tp-win-ratio-bar">
            <div class="bg-primary" id="ratioBarA" style="width: {{ $aPercent }}%;"></div>
            <div class="bg-success" id="ratioBarTie" style="width: {{ $tiePercent }}%;"></div>
            <div class="bg-danger" id="ratioBarB" style="width: {{ $bPercent }}%;"></div>
        </div>
    </div>

    <!-- BOTTOM TABBED DATA: MY BETS, HISTORY, RULES -->
    <div class="card tp-bottom-data-card border-0 shadow-lg rounded-4 p-3 mt-4" style="background: rgba(10, 16, 28, 0.95); border: 1px solid rgba(255, 255, 255, 0.08) !important;">
        <ul class="nav nav-tabs tp-nav-tabs border-bottom border-secondary border-opacity-25" id="tpTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="my-bets-tab" data-bs-toggle="tab" data-bs-target="#tabMyBets" type="button">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i> My Bets
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#tabHistory" type="button">
                    <i class="fa-solid fa-list-ol me-1"></i> Round History
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="paytable-tab" data-bs-toggle="tab" data-bs-target="#tabPaytable" type="button">
                    <i class="fa-solid fa-trophy text-warning me-1"></i> Hand Rankings & Rules
                </button>
            </li>
        </ul>

        <div class="tab-content pt-3" id="tpTabsContent">

            <!-- TAB 1: MY BETS -->
            <div class="tab-pane fade show active" id="tabMyBets">
                <div class="table-responsive">
                    <table class="table tp-table mb-0">
                        <thead>
                            <tr>
                                <th>Round ID</th>
                                <th>Bet Type</th>
                                <th>Stake</th>
                                <th>Odds</th>
                                <th>Payout</th>
                                <th>Status</th>
                                <th class="text-end">Time</th>
                            </tr>
                        </thead>
                        <tbody id="myBetsTableBody">
                            @auth
                                <tr><td colspan="7" class="text-center py-4 text-secondary"><span class="spinner-border spinner-border-sm me-1"></span> Loading your bets...</td></tr>
                            @else
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-secondary">
                                        Please <a href="{{ route('login') }}" class="text-warning fw-bold text-decoration-none">Login</a> to view your active and past bets.
                                    </td>
                                </tr>
                            @endauth
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: ROUND HISTORY -->
            <div class="tab-pane fade" id="tabHistory">
                <div class="table-responsive">
                    <table class="table tp-table mb-0">
                        <thead>
                            <tr>
                                <th>Round ID</th>
                                <th>Winner</th>
                                <th>Player A Hand</th>
                                <th>Player B Hand</th>
                                <th>Winning Hand Rank</th>
                                <th class="text-end">Settled At</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <tr><td colspan="6" class="text-center py-4 text-secondary"><span class="spinner-border spinner-border-sm me-1"></span> Loading round history...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: HAND RANKINGS & RULES -->
            <div class="tab-pane fade" id="tabPaytable">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <h6 class="fw-bold text-warning mb-3">
                            <i class="fa-solid fa-layer-group me-1"></i> Hand Rankings (Highest to Lowest)
                        </h6>

                        <div class="tp-rule-row">
                            <div>
                                <div class="fw-bold text-secondary">1. Trail / Trio / Set (तीन)</div>
                                <div class="small text-secondary">Three cards of the same rank (e.g. A-A-A, K-K-K)</div>
                            </div>
                            <span class="badge bg-warning text-dark font-monospace fw-bold fs-6">40X Pair Plus</span>
                        </div>

                        <div class="tp-rule-row">
                            <div>
                                <div class="fw-bold text-secondary">2. Pure Sequence / Straight Flush (शुद्ध)</div>
                                <div class="small text-secondary">Three consecutive cards of the same suit (e.g. A-K-Q♠, A-2-3♥)</div>
                            </div>
                            <span class="badge bg-warning text-dark font-monospace fw-bold fs-6">30X Pair Plus</span>
                        </div>

                        <div class="tp-rule-row">
                            <div>
                                <div class="fw-bold text-secondary">3. Sequence / Straight (सीक्वेंस)</div>
                                <div class="small text-secondary">Three consecutive cards of mixed suits (e.g. 9-8-7)</div>
                            </div>
                            <span class="badge bg-warning text-dark font-monospace fw-bold fs-6">10X Pair Plus</span>
                        </div>

                        <div class="tp-rule-row">
                            <div>
                                <div class="fw-bold text-secondary">4. Color / Flush (रंग)</div>
                                <div class="small text-secondary">Three cards of the same suit, not in sequence (e.g. K-9-4♦)</div>
                            </div>
                            <span class="badge bg-warning text-dark font-monospace fw-bold fs-6">5X Pair Plus</span>
                        </div>

                        <div class="tp-rule-row">
                            <div>
                                <div class="fw-bold text-secondary">5. Pair (जोड़ी)</div>
                                <div class="small text-secondary">Two cards of the same rank (e.g. Q-Q-4)</div>
                            </div>
                            <span class="badge bg-warning text-dark font-monospace fw-bold fs-6">2X Pair Plus</span>
                        </div>

                        <div class="tp-rule-row">
                            <div>
                                <div class="fw-bold text-secondary">6. High Card (हाई कार्ड)</div>
                                <div class="small text-secondary">Highest single card breaks ties (A > K > Q > ... > 2)</div>
                            </div>
                            <span class="badge bg-secondary font-monospace">Rank 1</span>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <h6 class="fw-bold text-info mb-3">
                            <i class="fa-solid fa-circle-info me-1"></i> Game Rules & Multipliers
                        </h6>

                        <div class="p-3 rounded-3 mb-3" style="background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.08);">
                            <div class="fw-bold text-secondary mb-1"><i class="fa-solid fa-clock text-warning me-1"></i> Round Timing:</div>
                            <p class="small text-secondary mb-0">
                                Each round lasts <strong>25 seconds</strong>. Betting is open for the first 20 seconds. The last 5 seconds are locked for live cards dealing & reveal.
                            </p>
                        </div>

                        <div class="p-3 rounded-3 mb-3" style="background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.08);">
                            <div class="fw-bold text-secondary mb-1"><i class="fa-solid fa-handshake text-success me-1"></i> Tie Push Policy:</div>
                            <p class="small text-secondary mb-0">
                                If both Player A and Player B receive the exact same hand strength, bets placed on Player A and Player B are <strong>100% REFUNDED (Push)</strong> without any loss. Bets on <strong>Tie</strong> win <strong>{{ number_format($setting->teen_patti_odds_tie ?? 9.00, 2) }}X</strong>!
                            </p>
                        </div>

                        <div class="p-3 rounded-3" style="background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.08);">
                            <div class="fw-bold text-secondary mb-1"><i class="fa-solid fa-shield-halved text-primary me-1"></i> Provably Fair:</div>
                            <p class="small text-secondary mb-0">
                                Standard 52-card international deck is shuffled and dealt deterministically.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- WIN CELEBRATION MODAL -->
<div class="modal fade" id="winCelebrationModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center text-white border-0 shadow-lg" style="background: radial-gradient(circle, #0f2c1d 0%, #06150e 100%); border: 2px solid var(--tp-gold) !important; border-radius: 28px;">
            <div class="modal-body py-4 px-4">
                <div class="mb-2">
                    <i class="fa-solid fa-trophy text-warning fa-3x animate__animated animate__bounceIn"></i>
                </div>
                <h3 class="fw-bold text-warning mb-1">YOU WON!</h3>
                <p class="text-white-50 small mb-2">Round #<span id="winModalRoundId">---</span> Result</p>
                
                <div class="display-5 fw-bold text-success font-monospace my-3" id="winModalAmount">
                    +₹0.00
                </div>

                <p class="small text-light mb-4" id="winModalMessage">
                    Payout credited directly to your gaming wallet!
                </p>

                <button type="button" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark" data-bs-dismiss="modal">
                    CONTINUE PLAYING
                </button>
            </div>
        </div>
    </div>
</div>

<!-- RULES MODAL -->
<div class="modal fade" id="rulesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content text-white border-0 shadow-lg" style="background: #0b1322; border: 1px solid rgba(212, 175, 55, 0.3) !important; border-radius: 24px;">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold text-warning"><i class="fa-solid fa-circle-question me-2"></i> How to Play 20-20 Teen Patti</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3 px-4">
                <h6 class="fw-bold text-light">Game Objective:</h6>
                <p class="small text-white-50">
                    In 20-20 Teen Patti, 3 cards are dealt to <strong>Player A</strong> and 3 cards to <strong>Player B</strong>. Predict which player will make the higher 3-card poker hand!
                </p>
                <div class="table-responsive">
                    <table class="table table-dark table-sm small">
                        <thead>
                            <tr class="text-warning">
                                <th>Bet Type</th>
                                <th>Payout Odds</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold text-primary">PLAYER A</td>
                                <td class="font-monospace text-warning fw-bold">{{ number_format($setting->teen_patti_odds_player ?? 1.95, 2) }}X</td>
                                <td>Player A hand is higher than Player B</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-danger">PLAYER B</td>
                                <td class="font-monospace text-warning fw-bold">{{ number_format($setting->teen_patti_odds_player ?? 1.95, 2) }}X</td>
                                <td>Player B hand is higher than Player A</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-success">TIE</td>
                                <td class="font-monospace text-warning fw-bold">{{ number_format($setting->teen_patti_odds_tie ?? 9.00, 2) }}X</td>
                                <td>Both hands have identical strength (A & B bets push/refunded)</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-light">PAIR PLUS A / B</td>
                                <td class="font-monospace text-warning fw-bold">Up to 40X</td>
                                <td>Side bet: Selected hand achieves a Pair (2X), Color (5X), Sequence (10X), Pure Sequence (30X), or Trio (40X)</td>
                            </tr>
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
    // Configuration & State
    const CONFIG = {
        minBet: {{ (float) ($setting->teen_patti_min_bet ?? 5) }},
        maxBet: {{ (float) ($setting->teen_patti_max_bet ?? 25000) }},
        oddsPlayer: {{ (float) ($setting->teen_patti_odds_player ?? 1.95) }},
        oddsTie: {{ (float) ($setting->teen_patti_odds_tie ?? 9.00) }},
        statusUrl: "{{ route('api.teen-patti.status') }}",
        placeBetUrl: "{{ route('api.teen-patti.place-bet') }}",
        historyUrl: "{{ route('api.teen-patti.history') }}",
        myBetsUrl: "{{ route('api.teen-patti.my-bets') }}",
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    };

    let currentRoundId = "{{ $roundData['round_id'] }}";
    let remainingSeconds = {{ (int) $roundData['remaining_seconds'] }};
    let roundDuration = {{ (int) ($roundData['duration'] ?? 25) }};
    let isLocked = {{ $roundData['is_locked'] ? 'true' : 'false' }};
    let currentSelectedChip = 100;
    let currentBetTarget = 'player_a';
    let audioEnabled = true;
    let audioCtx = null;
    let lastNotifiedWinRound = null;
    let dealAnimationTriggeredForRound = null;

    // Web Audio Synthesizer
    function getAudioContext() {
        if (!audioCtx) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) audioCtx = new AudioContext();
        }
        return audioCtx;
    }

    function playSound(type) {
        if (!audioEnabled) return;
        try {
            const ctx = getAudioContext();
            if (!ctx) return;
            if (ctx.state === 'suspended') ctx.resume();

            const now = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);

            if (type === 'chip') {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(800, now);
                osc.frequency.exponentialRampToValueAtTime(350, now + 0.08);
                gain.gain.setValueAtTime(0.25, now);
                gain.gain.linearRampToValueAtTime(0.01, now + 0.08);
                osc.start(now);
                osc.stop(now + 0.08);
            } else if (type === 'deal') {
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(440, now);
                osc.frequency.exponentialRampToValueAtTime(220, now + 0.07);
                gain.gain.setValueAtTime(0.2, now);
                gain.gain.linearRampToValueAtTime(0.01, now + 0.07);
                osc.start(now);
                osc.stop(now + 0.07);
            } else if (type === 'flip') {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(600, now);
                osc.frequency.exponentialRampToValueAtTime(1200, now + 0.09);
                gain.gain.setValueAtTime(0.18, now);
                gain.gain.linearRampToValueAtTime(0.01, now + 0.09);
                osc.start(now);
                osc.stop(now + 0.09);
            } else if (type === 'tick') {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(900, now);
                gain.gain.setValueAtTime(0.12, now);
                gain.gain.linearRampToValueAtTime(0.01, now + 0.04);
                osc.start(now);
                osc.stop(now + 0.04);
            } else if (type === 'win') {
                [523.25, 659.25, 783.99, 1046.50].forEach((freq, i) => {
                    const o = ctx.createOscillator();
                    const g = ctx.createGain();
                    o.type = 'triangle';
                    o.frequency.value = freq;
                    o.connect(g);
                    g.connect(ctx.destination);
                    g.gain.setValueAtTime(0.25, now + (i * 0.12));
                    g.gain.exponentialRampToValueAtTime(0.01, now + (i * 0.12) + 0.4);
                    o.start(now + (i * 0.12));
                    o.stop(now + (i * 0.12) + 0.4);
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
            btn.classList.replace('btn-outline-danger', 'btn-outline-secondary');
            playSound('chip');
        } else {
            icon.className = 'fa-solid fa-volume-xmark';
            btn.classList.replace('btn-outline-secondary', 'btn-outline-danger');
        }
    }

    // Chip & Target Selection
    function selectChip(amt) {
        currentSelectedChip = amt;
        document.querySelectorAll('.tp-casino-chip').forEach(c => c.classList.remove('selected'));
        const activeChipEl = document.querySelector(`.chip-${amt}`);
        if (activeChipEl) activeChipEl.classList.add('selected');

        document.getElementById('customAmountInput').value = amt;
        playSound('chip');
    }

    function onCustomAmountChange(val) {
        currentSelectedChip = Math.max(1, parseFloat(val) || 0);
        document.querySelectorAll('.tp-casino-chip').forEach(c => c.classList.remove('selected'));
    }

    function multiplyAmount(factor) {
        const input = document.getElementById('customAmountInput');
        let current = parseFloat(input.value) || 100;
        current = Math.min(CONFIG.maxBet, Math.max(CONFIG.minBet, Math.round(current * factor)));
        input.value = current;
        onCustomAmountChange(current);
        playSound('chip');
    }

    function clearAmount() {
        selectChip(100);
    }

    function selectBetTarget(target) {
        if (isLocked) return;
        currentBetTarget = target;

        // Reset visual active classes
        document.getElementById('btnBetPlayerA').classList.remove('active-target');
        document.getElementById('btnBetPlayerB').classList.remove('active-target');
        document.getElementById('btnBetTie').classList.remove('active-target');
        document.getElementById('btnBetPairPlusA').classList.remove('active-target');
        document.getElementById('btnBetPairPlusB').classList.remove('active-target');

        const labelMap = {
            'player_a': `Target: PLAYER A (${CONFIG.oddsPlayer.toFixed(2)}X)`,
            'player_b': `Target: PLAYER B (${CONFIG.oddsPlayer.toFixed(2)}X)`,
            'tie': `Target: TIE (${CONFIG.oddsTie.toFixed(2)}X)`,
            'pair_plus_a': 'Target: PAIR PLUS A (Up to 40X)',
            'pair_plus_b': 'Target: PAIR PLUS B (Up to 40X)',
        };

        if (target === 'player_a') document.getElementById('btnBetPlayerA').classList.add('active-target');
        else if (target === 'player_b') document.getElementById('btnBetPlayerB').classList.add('active-target');
        else if (target === 'tie') document.getElementById('btnBetTie').classList.add('active-target');
        else if (target === 'pair_plus_a') document.getElementById('btnBetPairPlusA').classList.add('active-target');
        else if (target === 'pair_plus_b') document.getElementById('btnBetPairPlusB').classList.add('active-target');

        document.getElementById('selectedTargetDisplay').innerText = labelMap[target] || 'Target Selected';
        playSound('chip');
    }

    // Submit Bet API
    async function submitBet() {
        if (isLocked) {
            alert('Betting is closed for this round! Cards are being dealt.');
            return;
        }

        const amt = parseFloat(document.getElementById('customAmountInput').value) || 0;
        if (amt < CONFIG.minBet) {
            alert(`Minimum bet amount is ₹${CONFIG.minBet}`);
            return;
        }
        if (amt > CONFIG.maxBet) {
            alert(`Maximum bet amount is ₹${CONFIG.maxBet}`);
            return;
        }

        const btn = document.getElementById('placeBetSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> PLACING...';

        try {
            const resp = await fetch(CONFIG.placeBetUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CONFIG.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    bet_type: currentBetTarget,
                    amount: amt
                })
            });

            const data = await resp.json();

            if (data.success) {
                playSound('chip');
                if (data.new_balance !== undefined) {
                    const balanceEl = document.getElementById('headerUserBalance');
                    if (balanceEl) balanceEl.innerText = `₹${parseFloat(data.new_balance).toFixed(2)}`;
                }
                syncGameStatus();
                fetchMyBets();
            } else {
                alert(data.message || 'Failed to place bet.');
            }
        } catch (e) {
            console.error('Bet submission error:', e);
            alert('Network error placing bet. Please check your connection.');
        } finally {
            btn.disabled = isLocked;
            btn.innerHTML = isLocked ? '<i class="fa-solid fa-lock me-1"></i> BETS LOCKED' : '<i class="fa-solid fa-bolt me-1"></i> PLACE BET';
        }
    }

    // Parse card whether string code (e.g. 'AS', '10H') or object
    function parseCardData(c) {
        if (typeof c === 'object' && c !== null) {
            const sym = c.symbol || (['H', '♥'].includes(c.suit) ? '♥' : (['D', '♦'].includes(c.suit) ? '♦' : (['C', '♣'].includes(c.suit) ? '♣' : '♠')));
            const isRed = ['H', 'D', '♥', '♦'].includes(c.suit || c.symbol) || c.color === 'red' || c.is_red;
            return {
                rank: c.rank || 'A',
                suit: c.suit || 'S',
                symbol: sym,
                isRed: isRed
            };
        }
        if (typeof c === 'string') {
            const suitChar = c.slice(-1).toUpperCase();
            const rankStr = c.slice(0, -1).toUpperCase();
            const symbolMap = { 'S': '♠', 'H': '♥', 'D': '♦', 'C': '♣' };
            const isRed = (suitChar === 'H' || suitChar === 'D');
            return {
                rank: rankStr || 'A',
                suit: suitChar,
                symbol: symbolMap[suitChar] || '♠',
                isRed: isRed
            };
        }
        return { rank: 'A', suit: 'S', symbol: '♠', isRed: false };
    }

    // Render 3 Playing Cards
    function renderCards(zone, cards) {
        if (!cards || cards.length < 3) return;

        cards.forEach((rawCard, idx) => {
            const cardEl = document.getElementById(`card${zone}_${idx}`);
            const frontEl = document.getElementById(`card${zone}Front_${idx}`);
            if (!cardEl || !frontEl) return;

            const c = parseCardData(rawCard);
            frontEl.className = `tp-card-face tp-card-front ${c.isRed ? 'card-red' : 'card-black'}`;

            frontEl.innerHTML = `
                <div class="tp-card-corner-top"><span class="r">${c.rank}</span><span class="s">${c.symbol}</span></div>
                <div class="tp-card-suit-center">${c.symbol}</div>
                <div class="tp-card-corner-bottom"><span class="r">${c.rank}</span><span class="s">${c.symbol}</span></div>
            `;
        });
    }

    // Flip Animations
    function flipCards(zone, shouldFlip) {
        for (let i = 0; i < 3; i++) {
            const cardEl = document.getElementById(`card${zone}_${i}`);
            if (cardEl) {
                if (shouldFlip) {
                    setTimeout(() => {
                        cardEl.classList.add('flipped');
                        playSound('flip');
                    }, i * 200);
                } else {
                    cardEl.classList.remove('flipped');
                }
            }
        }
    }

    // Reset Table for New Round
    function resetTableForNewRound() {
        flipCards('A', false);
        flipCards('B', false);

        document.getElementById('playerARankBadge').innerText = 'Waiting for deal...';
        document.getElementById('playerBRankBadge').innerText = 'Waiting for deal...';

        document.getElementById('zonePlayerA').classList.remove('winner-pulse');
        document.getElementById('zonePlayerB').classList.remove('winner-pulse');

        // Reset Bet Badges
        ['myBetBadgeA', 'myBetBadgeB', 'myBetBadgeTie', 'myBetBadgePPA', 'myBetBadgePPB'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });
    }

    // Trigger Dealing Animation Sequence in Last 5 Seconds
    function runDealingSequence(lastRound) {
        if (!lastRound) return;

        dealAnimationTriggeredForRound = currentRoundId;

        // Render card faces
        renderCards('A', lastRound.player_a_cards);
        renderCards('B', lastRound.player_b_cards);

        // Sequence Dealing Slide Sounds
        setTimeout(() => playSound('deal'), 100);
        setTimeout(() => playSound('deal'), 350);
        setTimeout(() => playSound('deal'), 600);

        // Flip Player A Cards
        setTimeout(() => {
            flipCards('A', true);
            const badgeA = document.getElementById('playerARankBadge');
            if (badgeA) badgeA.innerText = lastRound.player_a_rank_title || 'Player A Hand';
        }, 1000);

        // Flip Player B Cards
        setTimeout(() => {
            flipCards('B', true);
            const badgeB = document.getElementById('playerBRankBadge');
            if (badgeB) badgeB.innerText = lastRound.player_b_rank_title || 'Player B Hand';
        }, 1800);

        // Announce Winner & Highlight Winning Zone
        setTimeout(() => {
            const winner = lastRound.winner;
            if (winner === 'player_a') {
                document.getElementById('zonePlayerA').classList.add('winner-pulse');
                document.getElementById('phaseBanner').innerHTML = '<span class="text-primary fw-bold"><i class="fa-solid fa-trophy me-1"></i> PLAYER A WINS!</span>';
            } else if (winner === 'player_b') {
                document.getElementById('zonePlayerB').classList.add('winner-pulse');
                document.getElementById('phaseBanner').innerHTML = '<span class="text-danger fw-bold"><i class="fa-solid fa-trophy me-1"></i> PLAYER B WINS!</span>';
            } else if (winner === 'tie') {
                document.getElementById('phaseBanner').innerHTML = '<span class="text-success fw-bold"><i class="fa-solid fa-handshake me-1"></i> TIE RESULT!</span>';
            }
        }, 2600);
    }

    // Live Status Synchronization
    async function syncGameStatus() {
        try {
            const resp = await fetch(CONFIG.statusUrl);
            if (!resp.ok) return;

            const data = await resp.json();
            if (!data.success) return;

            // Handle Round ID Change
            if (data.round_id !== currentRoundId) {
                currentRoundId = data.round_id;
                document.getElementById('liveRoundId').innerText = currentRoundId;
                dealAnimationTriggeredForRound = null;
                resetTableForNewRound();
                fetchHistory();
                fetchMyBets();
            }

            remainingSeconds = data.remaining_seconds;
            isLocked = data.is_locked;
            roundDuration = data.duration || 25;

            // Update Countdown UI & Progress Ring
            document.getElementById('liveCountdown').innerText = String(remainingSeconds).padStart(2, '0');
            const progressCircle = document.getElementById('timerCircleProgress');
            if (progressCircle) {
                const totalDash = 283;
                const ratio = Math.max(0, remainingSeconds / roundDuration);
                const offset = totalDash * (1 - ratio);
                progressCircle.style.strokeDashoffset = offset;

                if (remainingSeconds <= 5) {
                    progressCircle.style.stroke = '#ef4444';
                } else if (remainingSeconds <= 10) {
                    progressCircle.style.stroke = '#f59e0b';
                } else {
                    progressCircle.style.stroke = '#10b981';
                }
            }

            // Play Tick Sound in Last 5 Seconds
            if (remainingSeconds <= 5 && remainingSeconds > 0) {
                playSound('tick');
            }

            // Update Phase Banner & Submit Button
            const submitBtn = document.getElementById('placeBetSubmitBtn');
            const phaseBanner = document.getElementById('phaseBanner');

            if (isLocked) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-lock me-1"></i> BETS LOCKED';
                if (!dealAnimationTriggeredForRound) {
                    phaseBanner.innerHTML = '<span class="text-danger"><i class="fa-solid fa-lock me-1"></i> BETS CLOSED • DEALING CARDS</span>';
                    if (data.last_round) {
                        runDealingSequence(data.last_round);
                    }
                }
            } else {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-bolt me-1"></i> PLACE BET';
                phaseBanner.innerHTML = '<span class="text-success"><i class="fa-solid fa-circle-check me-1"></i> BETTING OPEN • PLACE CHIPS</span>';
            }

            // Update Pools
            if (data.bets_summary) {
                if (document.getElementById('poolPlayerA')) {
                    document.getElementById('poolPlayerA').innerText = Math.round(data.bets_summary.player_a?.total_amount || 0);
                }
                if (document.getElementById('poolPlayerB')) {
                    document.getElementById('poolPlayerB').innerText = Math.round(data.bets_summary.player_b?.total_amount || 0);
                }
            }

            // Update My Active Bet Badges for Current Round
            if (data.my_active_bets && data.my_active_bets.length > 0) {
                data.my_active_bets.forEach(b => {
                    const amt = Math.round(b.amount);
                    if (b.bet_type === 'player_a') {
                        document.getElementById('myBetBadgeA').style.display = 'inline-flex';
                        document.getElementById('myBetAmtA').innerText = amt;
                    } else if (b.bet_type === 'player_b') {
                        document.getElementById('myBetBadgeB').style.display = 'inline-flex';
                        document.getElementById('myBetAmtB').innerText = amt;
                    } else if (b.bet_type === 'tie') {
                        document.getElementById('myBetBadgeTie').style.display = 'inline-flex';
                        document.getElementById('myBetAmtTie').innerText = amt;
                    } else if (b.bet_type === 'pair_plus_a') {
                        document.getElementById('myBetBadgePPA').style.display = 'inline-flex';
                        document.getElementById('myBetAmtPPA').innerText = amt;
                    } else if (b.bet_type === 'pair_plus_b') {
                        document.getElementById('myBetBadgePPB').style.display = 'inline-flex';
                        document.getElementById('myBetAmtPPB').innerText = amt;
                    }
                });
            }

            // Update Wallet Balance
            if (data.balance !== undefined) {
                const bEl = document.getElementById('headerUserBalance');
                if (bEl) bEl.innerText = `₹${parseFloat(data.balance).toFixed(2)}`;
            }

            // Win Celebration Notification
            if (data.latest_win && data.latest_win.round_id !== lastNotifiedWinRound) {
                lastNotifiedWinRound = data.latest_win.round_id;
                document.getElementById('winModalRoundId').innerText = data.latest_win.round_id;
                document.getElementById('winModalAmount').innerText = `+₹${parseFloat(data.latest_win.total_won).toFixed(2)}`;
                document.getElementById('winModalMessage').innerText = data.latest_win.message;

                const winModal = new bootstrap.Modal(document.getElementById('winCelebrationModal'));
                winModal.show();
                playSound('win');
                fetchMyBets();
            }

            // Update Roadmap Beads & Bar
            if (data.roadmap && data.roadmap.length > 0) {
                updateRoadmap(data.roadmap);
            }

        } catch (e) {
            console.error('Status sync error:', e);
        }
    }

    // Update Roadmap Beads & Ratio
    function updateRoadmap(roadmap) {
        const rail = document.getElementById('beadsRailContainer');
        if (!rail) return;

        let aCount = 0, bCount = 0, tieCount = 0;
        let html = '';

        roadmap.forEach(r => {
            const isA = r.winner === 'player_a';
            const isB = r.winner === 'player_b';
            if (isA) aCount++;
            else if (isB) bCount++;
            else tieCount++;

            const cls = isA ? 'tp-bead-a' : (isB ? 'tp-bead-b' : 'tp-bead-tie');
            const letter = isA ? 'A' : (isB ? 'B' : 'T');
            html += `<div class="tp-bead-circle ${cls}" title="Round #${r.round_id}: ${r.winner}">${letter}</div>`;
        });

        rail.innerHTML = html;

        const total = Math.max(1, roadmap.length);
        const aPct = Math.round((aCount / total) * 100);
        const bPct = Math.round((bCount / total) * 100);
        const tiePct = 100 - (aPct + bPct);

        document.getElementById('statPercentA').innerText = aPct;
        document.getElementById('statPercentB').innerText = bPct;
        document.getElementById('statPercentTie').innerText = tiePct;

        document.getElementById('ratioBarA').style.width = `${aPct}%`;
        document.getElementById('ratioBarB').style.width = `${bPct}%`;
        document.getElementById('ratioBarTie').style.width = `${tiePct}%`;
    }

    // Fetch My Bets Tab Data
    async function fetchMyBets() {
        try {
            const resp = await fetch(CONFIG.myBetsUrl);
            if (resp.status === 401) return; // Guest
            const data = await resp.json();
            if (!data.success) return;

            const tbody = document.getElementById('myBetsTableBody');
            if (!tbody) return;

            if (!data.bets || data.bets.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-secondary">No bets placed yet. Join the action!</td></tr>';
                return;
            }

            let html = '';
            data.bets.forEach(b => {
                let badgeCls = 'bg-secondary';
                let statusText = b.status.toUpperCase();
                let payout = '-';

                if (b.status === 'won') {
                    badgeCls = 'bg-success';
                    payout = `<span class="text-success fw-bold font-monospace">+₹${parseFloat(b.payout_amount).toFixed(2)}</span>`;
                } else if (b.status === 'lost') {
                    badgeCls = 'bg-danger';
                    payout = '<span class="text-secondary font-monospace">₹0.00</span>';
                } else if (b.status === 'pushed') {
                    badgeCls = 'bg-info text-dark';
                    payout = `<span class="text-info font-monospace">₹${parseFloat(b.amount).toFixed(2)} (Push)</span>`;
                } else {
                    badgeCls = 'bg-warning text-dark';
                }

                const typeMap = {
                    'player_a': '<span class="badge bg-primary px-2 py-1">PLAYER A</span>',
                    'player_b': '<span class="badge bg-danger px-2 py-1">PLAYER B</span>',
                    'tie': '<span class="badge bg-success px-2 py-1">TIE</span>',
                    'pair_plus_a': '<span class="badge bg-warning text-dark px-2 py-1">PAIR+ A</span>',
                    'pair_plus_b': '<span class="badge bg-warning text-dark px-2 py-1">PAIR+ B</span>'
                };

                const timeStr = b.created_at ? new Date(b.created_at).toLocaleTimeString() : '-';

                html += `
                    <tr>
                        <td class="font-monospace text-warning fw-bold">#${b.round_id}</td>
                        <td>${typeMap[b.bet_type] || b.bet_type}</td>
                        <td class="font-monospace fw-bold text-secondary">₹${parseFloat(b.amount).toFixed(2)}</td>
                        <td class="font-monospace text-warning">${parseFloat(b.odds).toFixed(2)}X</td>
                        <td>${payout}</td>
                        <td><span class="badge ${badgeCls} rounded-pill px-2 py-1">${statusText}</span></td>
                        <td class="text-end text-secondary small font-monospace">${timeStr}</td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        } catch (e) {
            console.error('Fetch my bets error:', e);
        }
    }

    // Fetch Completed Rounds History Tab Data
    async function fetchHistory() {
        try {
            const resp = await fetch(CONFIG.historyUrl);
            const data = await resp.json();
            if (!data.success) return;

            const tbody = document.getElementById('historyTableBody');
            if (!tbody) return;

            if (!data.history || data.history.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-secondary">No completed rounds yet.</td></tr>';
                return;
            }

            let html = '';
            data.history.forEach(h => {
                let winBadge = '<span class="badge bg-secondary">TIE</span>';
                if (h.winner === 'player_a') winBadge = '<span class="badge bg-primary px-2 py-1">PLAYER A</span>';
                else if (h.winner === 'player_b') winBadge = '<span class="badge bg-danger px-2 py-1">PLAYER B</span>';
                else if (h.winner === 'tie') winBadge = '<span class="badge bg-success px-2 py-1">TIE</span>';

                const formatMiniCards = (cards) => {
                    if (!cards || !Array.isArray(cards)) return '-';
                    return cards.map(c => {
                        const isRed = ['♥', '♦'].includes(c.symbol || c.suit) || c.color === 'red';
                        const colorCls = isRed ? 'text-danger' : 'text-secondary';
                        return `<span class="badge bg-dark border border-secondary ${colorCls} font-monospace me-1">${c.rank}${c.symbol || c.suit}</span>`;
                    }).join('');
                };

                const cardsA = formatMiniCards(h.player_a_cards);
                const cardsB = formatMiniCards(h.player_b_cards);
                const rankTitle = h.winning_rank_title || `${h.player_a_rank_title || 'A'} vs ${h.player_b_rank_title || 'B'}`;
                const settledTime = h.settled_at ? new Date(h.settled_at).toLocaleTimeString() : '-';

                html += `
                    <tr>
                        <td class="font-monospace text-warning fw-bold">#${h.round_id}</td>
                        <td>${winBadge}</td>
                        <td>${cardsA} <small class="text-secondary">(${h.player_a_rank_title || '-'})</small></td>
                        <td>${cardsB} <small class="text-secondary">(${h.player_b_rank_title || '-'})</small></td>
                        <td class="fw-bold text-secondary">${rankTitle}</td>
                        <td class="text-end text-secondary small font-monospace">${settledTime}</td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        } catch (e) {
            console.error('Fetch history error:', e);
        }
    }

    // Client 1-second countdown interpolator
    setInterval(() => {
        if (remainingSeconds > 0) {
            remainingSeconds--;
            document.getElementById('liveCountdown').innerText = String(remainingSeconds).padStart(2, '0');

            if (remainingSeconds <= 5 && !isLocked) {
                isLocked = true;
                syncGameStatus();
            }
        }
    }, 1000);

    // Initializations
    document.addEventListener('DOMContentLoaded', () => {
        syncGameStatus();
        fetchMyBets();
        fetchHistory();
        setInterval(syncGameStatus, 1500); // Poll status every 1.5 seconds
    });
</script>
@endsection
