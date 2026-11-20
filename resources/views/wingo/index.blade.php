@extends('layouts.app')

@section('title', 'WinGo Color Slot Game - rupeerush')

@section('styles')
<style>
    /* Theme CSS Variables: Dark Mode (Default) */
    :root, body.wingo-dark-mode {
        --wingo-bg: #090e1a;
        --wingo-topbar-bg: linear-gradient(135deg, #131d31 0%, #0c1424 100%);
        --wingo-card: #111a2e;
        --wingo-card-hover: #16223b;
        --wingo-border: rgba(255, 255, 255, 0.08);
        --wingo-text: #f3f4f6;
        --wingo-text-muted: #94a3b8;
        --wingo-period-id-text: #38bdf8;
        --wingo-period-badge-bg: rgba(56, 189, 248, 0.15);
        --wingo-period-badge-border: rgba(56, 189, 248, 0.4);
        --wingo-period-badge-text: #38bdf8;
        --wingo-table-th-color: #94a3b8;
        --wingo-table-border: rgba(255, 255, 255, 0.08);
        --wingo-tab-bg: linear-gradient(135deg, #131d31 0%, #0d1525 100%);
        --wingo-tab-color: #94a3b8;
        --wingo-timer-card: linear-gradient(135deg, #16223b 0%, #0c1424 100%);
        --wingo-digit-bg: #070b14;
        --wingo-digit-color: #ffffff;
        --wingo-digit-border: rgba(56, 189, 248, 0.4);
        --wingo-chip-bg: #17233c;
        --wingo-chip-color: #cbd5e1;
        --wingo-chip-border: rgba(255, 255, 255, 0.12);
        --wingo-subcard-bg: #0d1525;
        --wingo-subcard-border: rgba(255, 255, 255, 0.1);
        --wingo-wallet-bg: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        --wingo-theme-btn-bg: #1e293b;
        --wingo-theme-btn-color: #f59e0b;
        --wingo-theme-btn-border: rgba(255, 255, 255, 0.15);
        --wingo-card-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
    }

    /* Light Mode Theme Overrides */
    body.wingo-light-mode {
        --wingo-bg: #f8fafc;
        --wingo-topbar-bg: #ffffff;
        --wingo-card: #ffffff;
        --wingo-card-hover: #f1f5f9;
        --wingo-border: #e2e8f0;
        --wingo-text: #0f172a;
        --wingo-text-muted: #64748b;
        --wingo-period-id-text: #0284c7;
        --wingo-period-badge-bg: #e0f2fe;
        --wingo-period-badge-border: #7dd3fc;
        --wingo-period-badge-text: #0369a1;
        --wingo-table-th-color: #475569;
        --wingo-table-border: #e2e8f0;
        --wingo-tab-bg: #ffffff;
        --wingo-tab-color: #475569;
        --wingo-timer-card: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
        --wingo-digit-bg: #0f172a;
        --wingo-digit-color: #38bdf8;
        --wingo-digit-border: #0284c7;
        --wingo-chip-bg: #f1f5f9;
        --wingo-chip-color: #1e293b;
        --wingo-chip-border: #cbd5e1;
        --wingo-subcard-bg: #f8fafc;
        --wingo-subcard-border: #e2e8f0;
        --wingo-wallet-bg: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        --wingo-theme-btn-bg: #e2e8f0;
        --wingo-theme-btn-color: #0f172a;
        --wingo-theme-btn-border: #cbd5e1;
        --wingo-card-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
    }

    body {
        background-color: var(--wingo-bg);
        color: var(--wingo-text);
        transition: background-color 0.25s ease, color 0.25s ease;
    }

    /* Responsive Container: Fluid on Desktop up to 1420px, Compact on Mobile */
    .wingo-container {
        width: 100%;
        max-width: 1420px;
        margin: 0 auto;
        padding: 16px 14px 80px;
    }
    @media (min-width: 992px) {
        .wingo-container {
            padding: 24px 28px 80px;
        }
    }

    /* Top Bar Header */
    .wingo-topbar {
        background: var(--wingo-topbar-bg);
        border: 1px solid var(--wingo-border);
        border-radius: 18px;
        padding: 14px 20px;
        box-shadow: var(--wingo-card-shadow);
        margin-bottom: 20px;
        transition: all 0.25s ease;
    }

    /* Mode Navigation Tabs (30s, 1m, 3m, 5m) */
    .mode-tabs-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }
    .mode-tab-btn {
        background: var(--wingo-tab-bg);
        border: 1px solid var(--wingo-border);
        border-radius: 16px;
        padding: 14px 10px;
        text-align: center;
        color: var(--wingo-tab-color);
        font-weight: 700;
        font-size: 0.9rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        position: relative;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
    }
    .mode-tab-btn:hover {
        background: var(--wingo-card-hover);
        color: var(--wingo-text);
        transform: translateY(-2px);
    }
    .mode-tab-btn.active {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        border-color: #38bdf8;
        box-shadow: 0 8px 24px rgba(2, 132, 199, 0.45);
        transform: translateY(-2px);
    }
    .mode-tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        width: 24px;
        height: 4px;
        background: #38bdf8;
        border-radius: 4px;
        box-shadow: 0 0 10px #38bdf8;
    }
    .mode-tab-subtitle {
        font-size: 0.7rem;
        font-weight: 600;
        opacity: 0.85;
    }

    /* Timer & Info Studio Card */
    .timer-card {
        background: var(--wingo-timer-card);
        border: 1px solid var(--wingo-border);
        border-radius: 20px;
        padding: 20px 24px;
        margin-bottom: 20px;
        box-shadow: var(--wingo-card-shadow);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        transition: all 0.25s ease;
    }

    /* Period ID Badge with High Contrast */
    .period-badge {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.95rem;
        font-weight: 800;
        background: var(--wingo-period-badge-bg);
        border: 1px solid var(--wingo-period-badge-border);
        padding: 6px 14px;
        border-radius: 12px;
        color: var(--wingo-period-badge-text);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        letter-spacing: 0.5px;
    }
    .period-badge #labelPeriodId {
        color: var(--wingo-period-badge-text);
        font-weight: 800;
    }

    /* High-contrast Period ID in Tables */
    .period-id-cell {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.92rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: var(--wingo-period-id-text) !important;
    }

    .recent-balls-strip {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 12px;
        flex-wrap: wrap;
    }

    /* Flip Countdown Clock */
    .clock-container {
        text-align: right;
    }
    .clock-label {
        font-size: 0.78rem;
        text-transform: uppercase;
        font-weight: 800;
        letter-spacing: 1px;
        color: #0284c7;
        margin-bottom: 6px;
    }
    body.wingo-dark-mode .clock-label {
        color: #38bdf8;
    }
    .clock-digits {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 2.2rem;
        font-weight: 900;
        color: var(--wingo-digit-color);
    }
    .digit-box {
        background: var(--wingo-digit-bg);
        border: 2px solid var(--wingo-digit-border);
        border-radius: 12px;
        padding: 4px 12px;
        min-width: 50px;
        text-align: center;
        box-shadow: inset 0 3px 8px rgba(0,0,0,0.6);
        color: var(--wingo-digit-color);
        line-height: 1.1;
    }
    .clock-separator {
        color: #38bdf8;
        font-weight: 900;
        animation: pulseHeart 1s infinite alternate;
    }

    /* Betting Board Container & 5-Second Lock Overlay */
    .board-wrapper {
        position: relative;
        background: var(--wingo-card);
        border: 1px solid var(--wingo-border);
        border-radius: 22px;
        padding: 22px;
        box-shadow: var(--wingo-card-shadow);
        margin-bottom: 24px;
        overflow: hidden;
        transition: all 0.25s ease;
    }

    /* Lock Screen Overlay */
    .lock-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(9, 14, 26, 0.95);
        backdrop-filter: blur(10px);
        z-index: 50;
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 22px;
        text-align: center;
        animation: fadeIn 0.2s ease-in-out;
    }
    .lock-overlay.active {
        display: flex;
    }
    .lock-countdown-giant {
        font-family: 'JetBrains Mono', monospace;
        font-size: 6.5rem;
        font-weight: 900;
        color: #ef4444;
        text-shadow: 0 0 45px rgba(239, 68, 68, 0.8);
        line-height: 1;
        margin-bottom: 8px;
        animation: pulseHeart 1s infinite alternate;
    }
    .lock-text {
        font-size: 1.25rem;
        font-weight: 900;
        color: #f8fafc;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }
    .lock-subtext {
        font-size: 0.9rem;
        color: #cbd5e1;
        margin-top: 6px;
    }

    /* Color Prediction Primary Buttons (Green, Violet, Red) */
    .colors-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 14px;
        margin-bottom: 22px;
    }
    .btn-color-bet {
        border: none;
        border-radius: 16px;
        padding: 16px 12px;
        font-weight: 800;
        font-size: 1.15rem;
        color: #fff;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .btn-color-bet:hover {
        transform: translateY(-3px);
        filter: brightness(1.12);
    }
    .btn-color-bet:active {
        transform: scale(0.97);
    }
    .btn-color-green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 8px 22px rgba(16, 185, 129, 0.45);
    }
    .btn-color-violet {
        background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        box-shadow: 0 8px 22px rgba(139, 92, 246, 0.45);
    }
    .btn-color-red {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        box-shadow: 0 8px 22px rgba(239, 68, 68, 0.45);
    }
    .color-payout-tag {
        font-size: 0.78rem;
        font-weight: 700;
        opacity: 0.95;
        letter-spacing: 0.5px;
        background: rgba(0, 0, 0, 0.25);
        padding: 2px 10px;
        border-radius: 20px;
    }

    /* Number Balls Grid (0 to 9) */
    .numbers-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 14px;
        margin-bottom: 22px;
    }
    @media (min-width: 1200px) {
        .numbers-grid {
            grid-template-columns: repeat(10, 1fr);
            gap: 10px;
        }
    }
    .num-ball-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }
    .num-ball-btn {
        width: 100%;
        max-width: 68px;
        aspect-ratio: 1;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Outfit', sans-serif;
        font-size: 1.6rem;
        font-weight: 900;
        color: #ffffff;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        text-shadow: 0 2px 4px rgba(0,0,0,0.6);
    }
    .num-ball-btn:hover {
        transform: scale(1.12);
        filter: brightness(1.2);
    }
    .num-ball-btn:active {
        transform: scale(0.94);
    }

    /* Ball Colors matching WinGo Rules */
    .ball-green {
        background: radial-gradient(circle at 35% 35%, #34d399, #059669);
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.5);
    }
    .ball-red {
        background: radial-gradient(circle at 35% 35%, #f87171, #dc2626);
        box-shadow: 0 6px 18px rgba(239, 68, 68, 0.5);
    }
    /* Ball 0: Dual Red + Violet */
    .ball-dual-0 {
        background: linear-gradient(135deg, #ef4444 50%, #8b5cf6 50%);
        box-shadow: 0 6px 18px rgba(239, 68, 68, 0.45);
    }
    /* Ball 5: Dual Green + Violet */
    .ball-dual-5 {
        background: linear-gradient(135deg, #10b981 50%, #8b5cf6 50%);
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45);
    }
    .ball-multiplier-tag {
        font-size: 0.74rem;
        font-weight: 800;
        color: var(--wingo-text-muted);
    }

    /* Multiplier Chips Quick Row */
    .chips-row {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 8px;
        margin-bottom: 22px;
        scrollbar-width: none;
    }
    .chips-row::-webkit-scrollbar {
        display: none;
    }
    .multiplier-chip {
        background: var(--wingo-chip-bg);
        border: 1px solid var(--wingo-chip-border);
        color: var(--wingo-chip-color);
        font-weight: 700;
        font-size: 0.85rem;
        padding: 7px 16px;
        border-radius: 20px;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s;
    }
    .multiplier-chip:hover {
        background: var(--wingo-card-hover);
        color: var(--wingo-text);
    }
    .multiplier-chip.active {
        background: linear-gradient(135deg, #ec4899, #8b5cf6);
        border-color: #f472b6;
        color: #fff;
        box-shadow: 0 3px 12px rgba(236, 72, 153, 0.45);
    }

    /* Big / Small Choice Buttons */
    .big-small-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .btn-big-small {
        border: none;
        border-radius: 16px;
        padding: 16px 20px;
        font-weight: 900;
        font-size: 1.2rem;
        color: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        transition: all 0.2s;
    }
    .btn-big-small:hover {
        transform: translateY(-2px);
        filter: brightness(1.12);
    }
    .btn-big-small:active {
        transform: scale(0.97);
    }
    .btn-big {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 8px 24px rgba(245, 158, 11, 0.45);
    }
    .btn-small {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        box-shadow: 0 8px 24px rgba(59, 130, 246, 0.45);
    }

    /* Combination (Color + Size) Choice Buttons */
    .combos-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    @media (min-width: 768px) {
        .combos-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
        }
    }
    .btn-combo-card {
        border: none;
        border-radius: 16px;
        padding: 14px 10px;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        color: #fff;
        text-align: center;
    }
    .btn-combo-card:hover {
        transform: translateY(-2px);
        filter: brightness(1.12);
    }
    .btn-combo-card:active {
        transform: scale(0.97);
    }
    .combo-green-big, .combo-green-small {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.35);
    }
    .combo-red-big, .combo-red-small {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        box-shadow: 0 6px 18px rgba(239, 68, 68, 0.35);
    }
    .combo-violet-big, .combo-violet-small {
        background: linear-gradient(135deg, #6d28d9 0%, #8b5cf6 100%);
        box-shadow: 0 6px 18px rgba(139, 92, 246, 0.35);
    }
    .combo-subtext {
        font-size: 0.72rem;
        font-weight: 600;
        opacity: 0.9;
    }
    .combo-badge {
        font-size: 0.8rem;
        font-weight: 800;
        background: rgba(0, 0, 0, 0.3);
        padding: 2px 10px;
        border-radius: 20px;
        margin-top: 2px;
    }

    /* Desktop Sidebar Cards */
    .sidebar-widget-card {
        background: var(--wingo-card);
        border: 1px solid var(--wingo-border);
        border-radius: 22px;
        padding: 22px;
        box-shadow: var(--wingo-card-shadow);
        margin-bottom: 20px;
        transition: all 0.25s ease;
    }
    .wallet-hero-box {
        background: var(--wingo-wallet-bg);
        border: 1px solid var(--wingo-border);
        border-radius: 18px;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }
    .wallet-hero-box::after {
        content: '';
        position: absolute;
        top: -30px;
        right: -30px;
        width: 100px;
        height: 100px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.2) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Desktop Bet Slip Console */
    .bet-slip-panel {
        background: var(--wingo-subcard-bg);
        border: 1px solid var(--wingo-subcard-border);
        border-radius: 18px;
        padding: 20px;
    }
    .contract-pill {
        background: var(--wingo-chip-bg);
        border: 1px solid var(--wingo-chip-border);
        color: var(--wingo-chip-color);
        font-weight: 700;
        font-size: 0.9rem;
        padding: 8px 16px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .contract-pill.active {
        background: #0284c7;
        border-color: #38bdf8;
        color: #fff;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4);
    }
    .qty-btn {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--wingo-chip-bg);
        border: 1px solid var(--wingo-chip-border);
        color: var(--wingo-text);
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .qty-btn:hover {
        background: var(--wingo-card-hover);
    }

    /* Theme Toggle Button */
    .btn-theme-toggle {
        background: var(--wingo-theme-btn-bg);
        color: var(--wingo-theme-btn-color);
        border: 1px solid var(--wingo-theme-btn-border);
        transition: all 0.2s;
    }
    .btn-theme-toggle:hover {
        filter: brightness(1.15);
    }

    /* Live Analytics Stats */
    .stat-progress-bar {
        height: 10px;
        border-radius: 50px;
        overflow: hidden;
        background: var(--wingo-chip-bg);
        display: flex;
    }

    /* Bottom Info & Records Tabs */
    .tabs-header-wrap {
        display: flex;
        border-bottom: 1px solid var(--wingo-border);
        margin-bottom: 18px;
    }
    .record-tab-btn {
        flex: 1;
        background: transparent;
        border: none;
        border-bottom: 3px solid transparent;
        color: var(--wingo-text-muted);
        font-weight: 700;
        font-size: 0.95rem;
        padding: 12px 10px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
    }
    .record-tab-btn.active {
        color: #0284c7;
        border-bottom-color: #0284c7;
    }
    body.wingo-dark-mode .record-tab-btn.active {
        color: #38bdf8;
        border-bottom-color: #38bdf8;
    }

    /* Custom Tables inside dark/light theme */
    .table-dark-custom {
        width: 100%;
        color: var(--wingo-text);
        font-size: 0.88rem;
    }
    .table-dark-custom th {
        color: var(--wingo-table-th-color);
        font-weight: 800;
        font-size: 0.78rem;
        text-transform: uppercase;
        border-bottom: 2px solid var(--wingo-table-border);
        padding: 12px 10px;
    }
    .table-dark-custom td {
        padding: 12px 10px;
        border-bottom: 1px solid var(--wingo-table-border);
        vertical-align: middle;
    }

    /* Ball badge for records */
    .ball-mini {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        font-weight: 800;
        font-size: 0.88rem;
        color: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }

    /* Mobile Sheet modal */
    .bet-modal-sheet .modal-content {
        background: var(--wingo-card);
        border: 1px solid var(--wingo-border);
        border-radius: 24px;
        color: var(--wingo-text);
        box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.4);
    }

    /* Result Celebration Modal */
    .celebration-modal .modal-content {
        background: radial-gradient(circle at center, #1e1b4b 0%, #090e1a 100%);
        border: 2px solid #f59e0b;
        border-radius: 24px;
        text-align: center;
        padding: 28px;
        color: #fff;
    }

    @keyframes pulseHeart {
        from { transform: scale(0.96); }
        to { transform: scale(1.04); }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @media (max-width: 768px) {
        .wingo-container {
            padding: 4px 4px 60px !important;
        }
        .wingo-topbar {
            padding: 6px 8px !important;
            margin-bottom: 6px !important;
            border-radius: 12px !important;
        }
        .mode-tabs-grid {
            gap: 4px !important;
            margin-bottom: 6px !important;
        }
        .mode-tab-btn {
            padding: 6px 2px !important;
            border-radius: 8px !important;
            font-size: 0.72rem !important;
            gap: 2px !important;
        }
        .mode-tab-subtitle {
            display: none !important;
        }
        .timer-card {
            padding: 6px 8px !important;
            margin-bottom: 6px !important;
            border-radius: 12px !important;
            gap: 4px !important;
        }
        .period-badge {
            padding: 2px 6px !important;
            font-size: 0.72rem !important;
            border-radius: 8px !important;
        }
        .recent-balls-strip {
            gap: 3px !important;
            margin-top: 2px !important;
        }
        .recent-balls-strip .ball-mini {
            width: 22px !important;
            height: 22px !important;
            font-size: 0.70rem !important;
        }
        .clock-container {
            text-align: right !important;
        }
        .clock-label {
            font-size: 0.58rem !important;
            margin-bottom: 1px !important;
        }
        .clock-digits {
            font-size: 1.25rem !important;
            gap: 2px !important;
        }
        .digit-box {
            padding: 1px 5px !important;
            border-radius: 6px !important;
            min-width: 28px !important;
        }

        /* Board Wrapper & Buttons Compact on Mobile */
        .board-wrapper {
            padding: 8px 8px !important;
            border-radius: 14px !important;
            margin-bottom: 8px !important;
        }

        /* Primary Colors: Green, Violet, Red */
        .colors-row {
            grid-template-columns: 1fr 1fr 1fr !important;
            gap: 5px !important;
            margin-bottom: 6px !important;
        }
        .btn-color-bet {
            padding: 6px 2px !important;
            border-radius: 10px !important;
            min-height: 46px !important;
            gap: 2px !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25) !important;
        }
        .btn-color-bet span {
            font-size: 0.82rem !important;
            font-weight: 800 !important;
        }
        .btn-color-bet .color-payout-tag {
            font-size: 0.62rem !important;
            padding: 1px 5px !important;
        }

        /* Big / Small Row */
        .big-small-row {
            grid-template-columns: 1fr 1fr !important;
            gap: 5px !important;
            margin-top: 4px !important;
            margin-bottom: 6px !important;
        }
        .btn-big-small {
            padding: 6px 8px !important;
            border-radius: 10px !important;
            min-height: 42px !important;
            gap: 6px !important;
            white-space: nowrap !important;
        }
        .btn-big-small span {
            font-size: 0.85rem !important;
            white-space: nowrap !important;
        }
        .btn-big-small .color-payout-tag {
            font-size: 0.65rem !important;
            padding: 1px 6px !important;
        }

        /* Number Balls (0 to 9) - 1 SINGLE ROW OF 10 COLUMNS */
        .numbers-grid {
            display: grid !important;
            grid-template-columns: repeat(10, 1fr) !important;
            gap: 2px !important;
            margin-bottom: 6px !important;
        }
        .num-ball-wrap {
            gap: 2px !important;
        }
        .num-ball-btn {
            width: 100% !important;
            max-width: 32px !important;
            aspect-ratio: 1 !important;
            font-size: 0.82rem !important;
            font-weight: 900 !important;
            border-width: 1.5px !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3) !important;
        }
        .ball-multiplier-tag {
            font-size: 0.55rem !important;
            line-height: 1 !important;
        }

        /* Multiplier Chips */
        .chips-row {
            gap: 4px !important;
            margin-bottom: 6px !important;
            padding-bottom: 2px !important;
        }
        .multiplier-chip {
            padding: 3px 8px !important;
            font-size: 0.70rem !important;
            border-radius: 12px !important;
        }

        /* Combinations Grid */
        .combos-grid {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 4px !important;
        }
        .btn-combo-card {
            padding: 5px 2px !important;
            border-radius: 8px !important;
            gap: 1px !important;
        }
        .btn-combo-card span {
            font-size: 0.70rem !important;
        }
        .combo-badge {
            font-size: 0.62rem !important;
            padding: 1px 5px !important;
        }

        /* Sidebar widget cards */
        .sidebar-widget-card {
            padding: 10px 8px !important;
            border-radius: 14px !important;
            margin-bottom: 6px !important;
        }
    }
</style>
@endsection

@section('content')
<div class="wingo-container">

    <!-- Top Navigation & Brand Strip -->
    <div class="wingo-topbar d-flex align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger rounded-pill px-2 py-1 fw-bold" style="font-size: 0.7rem;">🔥 LIVE ARENA</span>
            <span class="fw-bold fs-6 text-white">WinGo Color Slot</span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Sound FX Toggle -->
            <button type="button" class="btn btn-sm btn-dark rounded-circle px-2 py-1" id="soundToggleBtn" title="Toggle Sound FX">
                <i class="fa-solid fa-volume-high text-info" id="soundIcon"></i>
            </button>

            <!-- Rules Modal Button -->
            <a href="#howToPlayModal" data-bs-toggle="modal" class="btn btn-outline-info btn-sm rounded-pill px-3 py-1 fw-bold" style="font-size: 0.78rem;">
                <i class="fa-solid fa-circle-question me-1"></i> Rules
            </a>


        </div>
    </div>

    <!-- 4 Game Mode Switcher Navigation Tabs -->
    <div class="mode-tabs-grid">
        <a href="?type=30s" class="mode-tab-btn {{ $gameType === '30s' ? 'active' : '' }}" data-gametype="30s">
            <i class="fa-solid fa-stopwatch fs-4"></i>
            <span>Win Go 30s</span>
            <span class="mode-tab-subtitle">Rapid 30 Sec</span>
        </a>
        <a href="?type=1m" class="mode-tab-btn {{ $gameType === '1m' ? 'active' : '' }}" data-gametype="1m">
            <i class="fa-solid fa-clock fs-4"></i>
            <span>Win Go 1Min</span>
            <span class="mode-tab-subtitle">Classic 1 Min</span>
        </a>
        <a href="?type=3m" class="mode-tab-btn {{ $gameType === '3m' ? 'active' : '' }}" data-gametype="3m">
            <i class="fa-regular fa-clock fs-4"></i>
            <span>Win Go 3Min</span>
            <span class="mode-tab-subtitle">Strategic 3 Min</span>
        </a>
        <a href="?type=5m" class="mode-tab-btn {{ $gameType === '5m' ? 'active' : '' }}" data-gametype="5m">
            <i class="fa-solid fa-hourglass-half fs-4"></i>
            <span>Win Go 5Min</span>
            <span class="mode-tab-subtitle">Mega 5 Min</span>
        </a>
    </div>

    <!-- MAIN DESKTOP 2-COLUMN STUDIO LAYOUT -->
    <div class="row g-4">

        <!-- LEFT COLUMN: Main Game Arena & Board (Col-lg-8) -->
        <div class="col-lg-8">

            <!-- Timer & Current Period Info Card -->
            <div class="timer-card">
                <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-1 gap-sm-2 flex-wrap">
                        <span class="badge bg-primary text-uppercase fw-bold px-2 py-1" id="badgeGameMode" style="font-size: 0.72rem;">Win Go {{ $gameType }}</span>
                        <span class="period-badge">
                            <i class="fa-solid fa-hashtag text-info"></i>
                            <span id="labelPeriodId">{{ $periodData['period_id'] }}</span>
                            <button type="button" class="btn btn-link btn-sm text-info p-0 ms-1" onclick="copyPeriodId()" title="Copy Period ID">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </span>
                    </div>

                    <!-- Digital Flip Clock -->
                    <div class="clock-container">
                        <div class="clock-label"><i class="fa-solid fa-stopwatch me-1"></i> Time Remaining</div>
                        <div class="clock-digits">
                            <div class="digit-box" id="clockMin">00</div>
                            <span class="clock-separator">:</span>
                            <div class="digit-box" id="clockSec">{{ str_pad($periodData['remaining_seconds'], 2, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Mini Strip of Last 10 Winning Balls -->
                <div class="d-flex align-items-center justify-content-between w-100 gap-1 gap-sm-2 mt-1 pt-1 border-top" style="border-color: rgba(255, 255, 255, 0.08) !important;">
                    <div class="small fw-bold text-nowrap d-none d-sm-block" style="color: var(--wingo-text-muted); font-size: 0.72rem;">Recent Balls:</div>
                    <div class="recent-balls-strip mt-0 w-100 justify-content-between" id="recentWinningBallsMini">
                        @foreach($recentHistory->take(10) as $rh)
                            @php
                                $num = $rh->winning_number;
                                $ballClass = 'ball-green';
                                if ($num === 0) $ballClass = 'ball-dual-0';
                                elseif ($num === 5) $ballClass = 'ball-dual-5';
                                elseif (in_array($num, [2, 4, 6, 8], true)) $ballClass = 'ball-red';
                            @endphp
                            <span class="ball-mini {{ $ballClass }}" title="Period {{ $rh->period_id }}">{{ $num }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Main Betting Board Container -->
            <div class="board-wrapper">

                <!-- 5-Second Lock Overlay -->
                <div class="lock-overlay {{ $periodData['is_locked'] ? 'active' : '' }}" id="lockOverlay">
                    <div class="lock-countdown-giant" id="lockCountdownNum">5</div>
                    <div class="lock-text"><i class="fa-solid fa-lock me-2 text-danger"></i> Round Locked</div>
                    <div class="lock-subtext">Generating winning ball & calculating payouts...</div>
                </div>

                <!-- Section Title -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fw-bold text-uppercase small letter-spacing-1" style="color: var(--wingo-text-muted);">
                        <i class="fa-solid fa-palette text-info me-1"></i> 1. Color Prediction
                    </span>
                    <span class="small" style="color: var(--wingo-text-muted);">Win up to 4.5X</span>
                </div>

                <!-- 3 Primary Colors Row -->
                <div class="colors-row">
                    <button type="button" class="btn-color-bet btn-color-green" onclick="handleChoiceSelect('color', 'Green', 2.0)">
                        <span><i class="fa-solid fa-circle-check me-1"></i> Green</span>
                        <span class="color-payout-tag">2X / 1.5X</span>
                    </button>
                    <button type="button" class="btn-color-bet btn-color-violet" onclick="handleChoiceSelect('color', 'Violet', 4.5)">
                        <span><i class="fa-solid fa-gem me-1"></i> Violet</span>
                        <span class="color-payout-tag">4.5X</span>
                    </button>
                    <button type="button" class="btn-color-bet btn-color-red" onclick="handleChoiceSelect('color', 'Red', 2.0)">
                        <span><i class="fa-solid fa-fire me-1"></i> Red</span>
                        <span class="color-payout-tag">2X / 1.5X</span>
                    </button>
                </div>

                <!-- 1b. Big / Small Prediction -->
                <div class="big-small-row my-3">
                    <button type="button" class="btn-big-small btn-big" onclick="handleChoiceSelect('size', 'Big', 2.0)">
                        <span><i class="fa-solid fa-arrow-trend-up me-1"></i> Big (5–9)</span>
                        <span class="color-payout-tag">2.0X</span>
                    </button>
                    <button type="button" class="btn-big-small btn-small" onclick="handleChoiceSelect('size', 'Small', 2.0)">
                        <span><i class="fa-solid fa-arrow-trend-down me-1"></i> Small (0–4)</span>
                        <span class="color-payout-tag">2.0X</span>
                    </button>
                </div>

                <!-- Section Title -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fw-bold text-uppercase small letter-spacing-1" style="color: var(--wingo-text-muted);">
                        <i class="fa-solid fa-dice text-warning me-1"></i> 2. Number Prediction (0 - 9)
                    </span>
                    <span class="small text-warning fw-bold">9X Jackpot Multiplier</span>
                </div>

                <!-- 10 Number Balls Grid (0 to 9) -->
                <div class="numbers-grid">
                    <!-- 0 (Dual Red + Violet) -->
                    <div class="num-ball-wrap">
                        <button type="button" class="num-ball-btn ball-dual-0" onclick="handleChoiceSelect('number', '0', 9.0)">0</button>
                        <span class="ball-multiplier-tag">9X</span>
                    </div>
                    <!-- 1 (Green) -->
                    <div class="num-ball-wrap">
                        <button type="button" class="num-ball-btn ball-green" onclick="handleChoiceSelect('number', '1', 9.0)">1</button>
                        <span class="ball-multiplier-tag">9X</span>
                    </div>
                    <!-- 2 (Red) -->
                    <div class="num-ball-wrap">
                        <button type="button" class="num-ball-btn ball-red" onclick="handleChoiceSelect('number', '2', 9.0)">2</button>
                        <span class="ball-multiplier-tag">9X</span>
                    </div>
                    <!-- 3 (Green) -->
                    <div class="num-ball-wrap">
                        <button type="button" class="num-ball-btn ball-green" onclick="handleChoiceSelect('number', '3', 9.0)">3</button>
                        <span class="ball-multiplier-tag">9X</span>
                    </div>
                    <!-- 4 (Red) -->
                    <div class="num-ball-wrap">
                        <button type="button" class="num-ball-btn ball-red" onclick="handleChoiceSelect('number', '4', 9.0)">4</button>
                        <span class="ball-multiplier-tag">9X</span>
                    </div>
                    <!-- 5 (Dual Green + Violet) -->
                    <div class="num-ball-wrap">
                        <button type="button" class="num-ball-btn ball-dual-5" onclick="handleChoiceSelect('number', '5', 9.0)">5</button>
                        <span class="ball-multiplier-tag">9X</span>
                    </div>
                    <!-- 6 (Red) -->
                    <div class="num-ball-wrap">
                        <button type="button" class="num-ball-btn ball-red" onclick="handleChoiceSelect('number', '6', 9.0)">6</button>
                        <span class="ball-multiplier-tag">9X</span>
                    </div>
                    <!-- 7 (Green) -->
                    <div class="num-ball-wrap">
                        <button type="button" class="num-ball-btn ball-green" onclick="handleChoiceSelect('number', '7', 9.0)">7</button>
                        <span class="ball-multiplier-tag">9X</span>
                    </div>
                    <!-- 8 (Red) -->
                    <div class="num-ball-wrap">
                        <button type="button" class="num-ball-btn ball-red" onclick="handleChoiceSelect('number', '8', 9.0)">8</button>
                        <span class="ball-multiplier-tag">9X</span>
                    </div>
                    <!-- 9 (Green) -->
                    <div class="num-ball-wrap">
                        <button type="button" class="num-ball-btn ball-green" onclick="handleChoiceSelect('number', '9', 9.0)">9</button>
                        <span class="ball-multiplier-tag">9X</span>
                    </div>
                </div>

                <!-- Quick Multiplier Chips Row -->
                <div class="chips-row">
                    <span class="multiplier-chip" onclick="pickRandomBet()">
                        <i class="fa-solid fa-shuffle me-1"></i> Random Pick
                    </span>
                    <span class="multiplier-chip active" onclick="setQuickMultiplier(1, this)">X1</span>
                    <span class="multiplier-chip" onclick="setQuickMultiplier(5, this)">X5</span>
                    <span class="multiplier-chip" onclick="setQuickMultiplier(10, this)">X10</span>
                    <span class="multiplier-chip" onclick="setQuickMultiplier(20, this)">X20</span>
                    <span class="multiplier-chip" onclick="setQuickMultiplier(50, this)">X50</span>
                    <span class="multiplier-chip" onclick="setQuickMultiplier(100, this)">X100</span>
                </div>

                <!-- Section Title -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fw-bold text-uppercase small letter-spacing-1" style="color: var(--wingo-text-muted);">
                        <i class="fa-solid fa-code-merge text-success me-1"></i> 3. Combination Prediction (Color + Size)
                    </span>
                    <span class="small text-success fw-bold">Up to 9X Jackpot</span>
                </div>

                <!-- 6 Color + Size Combinations Grid -->
                <div class="combos-grid">
                    <!-- Green + Big -->
                    <button type="button" class="btn-combo-card combo-green-big" onclick="handleChoiceSelect('combination', 'Green + Big', 4.5)">
                        <span class="fw-bold fs-6">🟢 Green + Big</span>
                        <span class="combo-badge">4.5X</span>
                    </button>
                    <!-- Green + Small -->
                    <button type="button" class="btn-combo-card combo-green-small" onclick="handleChoiceSelect('combination', 'Green + Small', 4.5)">
                        <span class="fw-bold fs-6">🟢 Green + Small</span>
                        <span class="combo-badge">4.5X</span>
                    </button>
                    <!-- Red + Big -->
                    <button type="button" class="btn-combo-card combo-red-big" onclick="handleChoiceSelect('combination', 'Red + Big', 4.5)">
                        <span class="fw-bold fs-6">🔴 Red + Big</span>
                        <span class="combo-badge">4.5X</span>
                    </button>
                    <!-- Red + Small -->
                    <button type="button" class="btn-combo-card combo-red-small" onclick="handleChoiceSelect('combination', 'Red + Small', 4.5)">
                        <span class="fw-bold fs-6">🔴 Red + Small</span>
                        <span class="combo-badge">4.5X</span>
                    </button>
                    <!-- Violet + Big -->
                    <button type="button" class="btn-combo-card combo-violet-big" onclick="handleChoiceSelect('combination', 'Violet + Big', 9.0)">
                        <span class="fw-bold fs-6">🟣 Violet + Big</span>
                        <span class="combo-badge">9.0X</span>
                    </button>
                    <!-- Violet + Small -->
                    <button type="button" class="btn-combo-card combo-violet-small" onclick="handleChoiceSelect('combination', 'Violet + Small', 9.0)">
                        <span class="fw-bold fs-6">🟣 Violet + Small</span>
                        <span class="combo-badge">9.0X</span>
                    </button>
                </div>
            </div>

            <!-- History, Trend Chart & My Bets Section -->
            <div class="sidebar-widget-card p-4">
                <div class="tabs-header-wrap">
                    <button type="button" class="record-tab-btn active" id="tabBtnHistory" onclick="switchRecordTab('history')">
                        <i class="fa-solid fa-clock-rotate-left me-1"></i> Game History
                    </button>
                    <button type="button" class="record-tab-btn" id="tabBtnChart" onclick="switchRecordTab('chart')">
                        <i class="fa-solid fa-chart-line me-1"></i> Trend Chart
                    </button>
                    <button type="button" class="record-tab-btn" id="tabBtnMyBets" onclick="switchRecordTab('myBets')">
                        <i class="fa-solid fa-user-check me-1"></i> My Bets
                    </button>
                </div>

                <!-- TAB 1: Game History -->
                <div id="tabContentHistory">
                    <div class="table-responsive">
                        <table class="table-dark-custom">
                            <thead>
                                <tr>
                                    <th>Period ID</th>
                                    <th class="text-center">Drawn Ball</th>
                                    <th class="text-center">Big / Small</th>
                                    <th class="text-center">Color Combination</th>
                                </tr>
                            </thead>
                            <tbody id="gameHistoryTableBody">
                                @forelse($recentHistory as $item)
                                    @php
                                        $num = $item->winning_number;
                                        $ballClass = 'ball-green';
                                        if ($num === 0) $ballClass = 'ball-dual-0';
                                        elseif ($num === 5) $ballClass = 'ball-dual-5';
                                        elseif (in_array($num, [2, 4, 6, 8], true)) $ballClass = 'ball-red';

                                        $wc = strtolower($item->winning_color ?? '');
                                        $sizeStr = strtoupper($item->winning_size ?? 'SMALL');
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="period-id-cell">{{ $item->period_id }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="ball-mini {{ $ballClass }}">{{ $num }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $sizeStr === 'BIG' ? 'bg-warning text-dark' : 'bg-primary' }}">
                                                {{ $sizeStr }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if(str_contains($wc, 'green'))
                                                <span class="badge bg-success me-1">Green</span>
                                            @endif
                                            @if(str_contains($wc, 'violet'))
                                                <span class="badge me-1" style="background: #8b5cf6; color:#fff;">Violet</span>
                                            @endif
                                            @if(str_contains($wc, 'red'))
                                                <span class="badge bg-danger">Red</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No completed periods yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 2: Trend Chart -->
                <div id="tabContentChart" style="display: none;">
                    <div class="p-2">
                        <div class="small mb-3" style="color: var(--wingo-text-muted);">Live distribution matrix of balls (0–9) across recent rounds:</div>
                        <div class="table-responsive">
                            <table class="table-dark-custom text-center">
                                <thead>
                                    <tr>
                                        <th style="width: 130px;">Period</th>
                                        <th>0</th><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th><th>7</th><th>8</th><th>9</th>
                                    </tr>
                                </thead>
                                <tbody id="trendChartBody">
                                    @foreach($recentHistory->take(15) as $rh)
                                        <tr>
                                            <td>
                                                <span class="period-id-cell small">{{ substr($rh->period_id, -5) }}</span>
                                            </td>
                                            @for($i=0; $i<=9; $i++)
                                                <td>
                                                    @if($rh->winning_number === $i)
                                                        @php
                                                            $ballClass = 'ball-green';
                                                            if ($i === 0) $ballClass = 'ball-dual-0';
                                                            elseif ($i === 5) $ballClass = 'ball-dual-5';
                                                            elseif (in_array($i, [2, 4, 6, 8], true)) $ballClass = 'ball-red';
                                                        @endphp
                                                        <span class="ball-mini {{ $ballClass }}" style="width:26px; height:26px; font-size:0.8rem;">{{ $i }}</span>
                                                    @else
                                                        <span class="opacity-50" style="color: var(--wingo-text-muted);">•</span>
                                                    @endif
                                                </td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: My History -->
                <div id="tabContentMyBets" style="display: none;">
                    @auth
                        <div class="table-responsive">
                            <table class="table-dark-custom">
                                <thead>
                                    <tr>
                                        <th>Period</th>
                                        <th>Selection</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-end">Bet Amount</th>
                                        <th class="text-end">Win Payout</th>
                                    </tr>
                                </thead>
                                <tbody id="myBetsTableBody">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Loading your bets...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="small mb-2" style="color: var(--wingo-text-muted);">Please login to view your WinGo bet history.</p>
                            <a href="{{ route('login') }}" class="btn btn-outline-info btn-sm rounded-pill px-4">Login Now</a>
                        </div>
                    @endauth
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: Desktop Stake Console & Studio Sidebar (Col-lg-4) -->
        <div class="col-lg-4">

            <!-- Card 1: Wallet & Cashier -->
            <div class="sidebar-widget-card p-4">
                <div class="wallet-hero-box mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="small text-uppercase fw-bold text-white opacity-90">Available Balance</span>
                        <i class="fa-solid fa-wallet text-white"></i>
                    </div>
                    <div class="fs-2 fw-bold text-white font-monospace" id="displayWalletBalance">
                        ₹{{ Auth::check() ? number_format(Auth::user()->balance, 2) : '0.00' }}
                    </div>
                    <div class="small text-white opacity-85 mt-1">Unified across all lottery & color games</div>
                </div>

                <div class="d-grid gap-2 d-flex">
                    <a href="{{ route('wallet.deposit') }}" class="btn btn-deposit-nav flex-fill justify-content-center py-2" style="font-size: 0.95rem;">
                        <i class="fa-solid fa-plus-circle me-1"></i> Deposit
                    </a>
                    <a href="{{ route('wallet.withdraw') }}" class="btn btn-withdraw-nav flex-fill justify-content-center py-2" style="font-size: 0.95rem;">
                        <i class="fa-solid fa-arrow-up-from-bracket me-1"></i> Withdraw
                    </a>
                </div>
            </div>

            <!-- Card 2: Desktop Bet Slip / Stake Console -->
            <div class="sidebar-widget-card p-4" id="desktopStakeConsole">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: var(--wingo-border) !important;">
                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-ticket text-warning me-2"></i> Quick Stake Console
                    </h5>
                    <span class="badge bg-success" id="sideSelectionBadge">Green (2X)</span>
                </div>

                <!-- Selected Item Header -->
                <div class="p-3 rounded-3 mb-3 d-flex align-items-center justify-content-between" style="background: var(--wingo-subcard-bg); border: 1px solid var(--wingo-subcard-border);">
                    <div>
                        <div class="small" style="color: var(--wingo-text-muted);">Selected Bet</div>
                        <div class="fw-bold fs-5" id="sideSelectionText">Green</div>
                    </div>
                    <div class="text-end">
                        <div class="small" style="color: var(--wingo-text-muted);">Multiplier</div>
                        <div class="fw-bold fs-5 text-warning font-monospace" id="sideExpectedMult">2.0X</div>
                    </div>
                </div>

                <!-- Balance Unit Contract Money -->
                <div class="mb-3">
                    <div class="small fw-bold mb-2" style="color: var(--wingo-text-muted);">Contract Unit Price</div>
                    <div class="d-flex gap-2">
                        <span class="contract-pill active" onclick="setUnitPrice(1, this)">₹1</span>
                        <span class="contract-pill" onclick="setUnitPrice(10, this)">₹10</span>
                        <span class="contract-pill" onclick="setUnitPrice(100, this)">₹100</span>
                        <span class="contract-pill" onclick="setUnitPrice(1000, this)">₹1000</span>
                    </div>
                </div>

                <!-- Multiplier Stepper -->
                <div class="mb-3">
                    <div class="small fw-bold mb-2" style="color: var(--wingo-text-muted);">Quantity / Multiplier</div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="qty-btn" onclick="stepMultiplier(-1)"><i class="fa-solid fa-minus"></i></div>
                        <input type="number" id="sideInputMultiplier" class="form-control text-center fw-bold" style="width: 100px; background: var(--wingo-subcard-bg); color: var(--wingo-text); border: 1px solid var(--wingo-subcard-border);" value="1" min="1" max="1000" oninput="onSideMultiplierInput(this.value)">
                        <div class="qty-btn" onclick="stepMultiplier(1)"><i class="fa-solid fa-plus"></i></div>
                    </div>
                </div>

                <!-- Quick Multiplier Chips in Sidebar -->
                <div class="mb-3">
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="multiplier-chip active" onclick="setQuickMultiplier(1, this)">X1</span>
                        <span class="multiplier-chip" onclick="setQuickMultiplier(5, this)">X5</span>
                        <span class="multiplier-chip" onclick="setQuickMultiplier(10, this)">X10</span>
                        <span class="multiplier-chip" onclick="setQuickMultiplier(20, this)">X20</span>
                        <span class="multiplier-chip" onclick="setQuickMultiplier(50, this)">X50</span>
                        <span class="multiplier-chip" onclick="setQuickMultiplier(100, this)">X100</span>
                    </div>
                </div>

                <!-- Total Amount Summary -->
                <div class="p-3 rounded-3 mb-3" style="background: var(--wingo-subcard-bg); border: 1px solid var(--wingo-subcard-border);">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small" style="color: var(--wingo-text-muted);">Total Stake Amount:</span>
                        <span class="fs-4 fw-bold text-success font-monospace" id="sideTotalBetAmt">₹1.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small" style="color: var(--wingo-text-muted);">Estimated Return:</span>
                        <span class="small fw-bold text-info font-monospace" id="sideEstimatedReturn">₹2.00</span>
                    </div>
                </div>

                <!-- Place Bet Button -->
                <button type="button" class="btn btn-primary w-100 rounded-pill py-3 fw-bold fs-6 shadow" id="sideBtnConfirmBet" onclick="submitBetOrder()">
                    <i class="fa-solid fa-bolt me-1"></i> Place Bet Now
                </button>
            </div>

            <!-- Card 3: Live Round Insights & Trend Stats -->
            <div class="sidebar-widget-card p-4">
                <h6 class="fw-bold mb-3">
                    <i class="fa-solid fa-chart-pie text-info me-2"></i> Recent 15 Rounds Analytics
                </h6>

                @php
                    $colorsCount = ['green' => 0, 'red' => 0, 'violet' => 0];
                    $sizeCount = ['Big' => 0, 'Small' => 0];
                    $sample = $recentHistory->take(15);
                    $totalSample = max(1, $sample->count());
                    foreach($sample as $s) {
                        $c = strtolower($s->winning_color ?? '');
                        if (str_contains($c, 'green')) $colorsCount['green']++;
                        if (str_contains($c, 'red')) $colorsCount['red']++;
                        if (str_contains($c, 'violet')) $colorsCount['violet']++;
                        if (strtolower($s->winning_size ?? '') === 'big') $sizeCount['Big']++;
                        else $sizeCount['Small']++;
                    }
                    $greenPct = round(($colorsCount['green'] / $totalSample) * 100);
                    $redPct = round(($colorsCount['red'] / $totalSample) * 100);
                    $violetPct = round(($colorsCount['violet'] / $totalSample) * 100);
                    $bigPct = round(($sizeCount['Big'] / $totalSample) * 100);
                    $smallPct = 100 - $bigPct;
                @endphp

                <!-- Color Distribution Bar -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1 font-monospace" style="color: var(--wingo-text-muted);">
                        <span class="text-success fw-bold">Green: {{ $greenPct }}%</span>
                        <span style="color:#8b5cf6;" class="fw-bold">Violet: {{ $violetPct }}%</span>
                        <span class="text-danger fw-bold">Red: {{ $redPct }}%</span>
                    </div>
                    <div class="stat-progress-bar">
                        <div style="width: {{ $greenPct }}%; background: #10b981;"></div>
                        <div style="width: {{ $violetPct }}%; background: #8b5cf6;"></div>
                        <div style="width: {{ $redPct }}%; background: #ef4444;"></div>
                    </div>
                </div>

                <!-- Big vs Small Bar -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1 font-monospace" style="color: var(--wingo-text-muted);">
                        <span class="text-warning fw-bold">Big: {{ $bigPct }}%</span>
                        <span class="text-primary fw-bold">Small: {{ $smallPct }}%</span>
                    </div>
                    <div class="stat-progress-bar">
                        <div style="width: {{ $bigPct }}%; background: #f59e0b;"></div>
                        <div style="width: {{ $smallPct }}%; background: #3b82f6;"></div>
                    </div>
                </div>

                <div class="p-2 rounded border small" style="background: var(--wingo-subcard-bg); border-color: var(--wingo-subcard-border) !important; color: var(--wingo-text-muted);">
                    <i class="fa-solid fa-circle-info text-info me-1"></i>
                    Stats update dynamically with every completed period.
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Mobile Responsive Bet Modal (Only triggers on small screens) -->
<div class="modal fade bet-modal-sheet" id="betSheetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-1">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="modal-title fw-bold" id="betModalTitle">Place Bet</h5>
                    <span class="badge" id="betModalSelectionBadge">Green</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-1">
                <div class="mb-3">
                    <div class="small fw-bold mb-2" style="color: var(--wingo-text-muted);">Contract Unit Price</div>
                    <div class="d-flex gap-2">
                        <span class="contract-pill active" onclick="setUnitPrice(1, this)">₹1</span>
                        <span class="contract-pill" onclick="setUnitPrice(10, this)">₹10</span>
                        <span class="contract-pill" onclick="setUnitPrice(100, this)">₹100</span>
                        <span class="contract-pill" onclick="setUnitPrice(1000, this)">₹1000</span>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="small fw-bold mb-2" style="color: var(--wingo-text-muted);">Multiplier</div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="qty-btn" onclick="stepMultiplier(-1)"><i class="fa-solid fa-minus"></i></div>
                        <input type="number" id="inputMultiplier" class="form-control text-center fw-bold" style="width: 100px; background: var(--wingo-subcard-bg); color: var(--wingo-text); border: 1px solid var(--wingo-subcard-border);" value="1" min="1" max="1000" oninput="onModalMultiplierInput(this.value)">
                        <div class="qty-btn" onclick="stepMultiplier(1)"><i class="fa-solid fa-plus"></i></div>
                    </div>
                </div>

                <div class="p-3 rounded-3 mb-3 d-flex justify-content-between align-items-center" style="background: var(--wingo-subcard-bg); border: 1px solid var(--wingo-subcard-border);">
                    <div>
                        <div class="small" style="color: var(--wingo-text-muted);">Total Stake</div>
                        <div class="fs-4 fw-bold text-success font-monospace" id="labelTotalBetAmount">₹1.00</div>
                    </div>
                    <div class="text-end">
                        <div class="small" style="color: var(--wingo-text-muted);">Multiplier</div>
                        <div class="fw-bold text-info" id="labelExpectedMultiplier">2.0X</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary rounded-pill px-5 fw-bold" id="btnConfirmBet" onclick="submitBetOrder()">
                    Confirm Bet
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Round Result Popup Modal -->
<div class="modal fade celebration-modal" id="resultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <h4 class="fw-bold mb-1 text-warning"><i class="fa-solid fa-trophy me-2"></i> Round Result</h4>
            <div class="text-muted small mb-3">Period <span id="resPeriodId" class="text-white font-monospace"></span> Drawn</div>
            
            <div class="d-flex justify-content-center my-3">
                <div id="resBallCircle" class="num-ball-btn ball-green" style="width: 80px; height: 80px; font-size: 2.3rem;">
                    7
                </div>
            </div>

            <div class="d-flex justify-content-center gap-2 mb-3">
                <span class="badge bg-success fs-6" id="resColorBadge">Green</span>
                <span class="badge bg-warning text-dark fs-6" id="resSizeBadge">Big</span>
            </div>

            <div id="resWinAnnouncement" class="p-3 bg-success bg-opacity-25 border border-success rounded-3 my-2" style="display:none;">
                <div class="fw-bold text-success fs-5">🎉 Congratulations!</div>
                <div class="small text-white">You won in this round!</div>
            </div>

            <div class="mt-3">
                <button type="button" class="btn btn-warning rounded-pill px-5 fw-bold" data-bs-dismiss="modal">Continue Playing</button>
            </div>
        </div>
    </div>
</div>

<!-- Rules Modal -->
<div class="modal fade" id="howToPlayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--wingo-card); color: var(--wingo-text); border: 1px solid var(--wingo-border);">
            <div class="modal-header" style="border-color: var(--wingo-border);">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-book-open text-info me-2"></i> WinGo Game Rules</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body small" style="color: var(--wingo-text-muted);">
                <h6 class="fw-bold mb-2" style="color: var(--wingo-text);">1. Game Cycle & Locking</h6>
                <p>Available in 30 seconds, 1 minute, 3 minute and 5 minute modes. The last 5 seconds are locked for settlement.</p>

                <h6 class="fw-bold mb-2" style="color: var(--wingo-text);">2. Color Payouts</h6>
                <ul>
                    <li><strong class="text-success">Green (1, 3, 7, 9):</strong> 2X payout. If 5 appears, pays 1.5X.</li>
                    <li><strong style="color: #8b5cf6;">Violet (0, 5):</strong> 4.5X payout.</li>
                    <li><strong class="text-danger">Red (2, 4, 6, 8):</strong> 2X payout. If 0 appears, pays 1.5X.</li>
                </ul>

                <h6 class="fw-bold mb-2" style="color: var(--wingo-text);">3. Number Payouts (0 - 9)</h6>
                <p>Pick the exact number correctly to win <strong>9X jackpot payout</strong>!</p>

                <h6 class="fw-bold mb-2" style="color: var(--wingo-text);">4. Color + Size Combination Payouts</h6>
                <ul>
                    <li><strong class="text-success">Green + Big (#7, #9):</strong> 4.5X payout</li>
                    <li><strong class="text-success">Green + Small (#1, #3):</strong> 4.5X payout</li>
                    <li><strong class="text-danger">Red + Big (#6, #8):</strong> 4.5X payout</li>
                    <li><strong class="text-danger">Red + Small (#2, #4):</strong> 4.5X payout</li>
                    <li><strong style="color: #8b5cf6;">Violet + Big (#5):</strong> 9.0X payout</li>
                    <li><strong style="color: #8b5cf6;">Violet + Small (#0):</strong> 9.0X payout</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Theme: Standardized Dark Theme across games
    document.body.classList.remove('wingo-light-mode');
    document.body.classList.add('wingo-dark-mode');

    // Global State
    let currentGameType = '{{ $gameType }}';
    let currentPeriodId = '{{ $periodData['period_id'] }}';
    let remainingSeconds = {{ $periodData['remaining_seconds'] }};
    let isLocked = {{ $periodData['is_locked'] ? 'true' : 'false' }};
    let soundEnabled = true;

    // Bet Form State
    let selectedBetType = 'color';
    let selectedBetSelection = 'Green';
    let selectedUnitPrice = 1;
    let selectedMultiplier = 1;
    let selectedExpectedMultiplier = 2.0;

    // Web Audio Synthesizer for High-End Arcade Sounds
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    function playBeep(freq = 600, duration = 0.1) {
        if (!soundEnabled) return;
        try {
            if (audioCtx.state === 'suspended') audioCtx.resume();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'sine';
            osc.frequency.value = freq;
            gain.gain.setValueAtTime(0.15, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + duration);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + duration);
        } catch (e) {}
    }

    function playWinChime() {
        if (!soundEnabled) return;
        [523.25, 659.25, 783.99, 1046.50].forEach((freq, i) => {
            setTimeout(() => playBeep(freq, 0.25), i * 120);
        });
    }

    // Sound toggle
    document.getElementById('soundToggleBtn').addEventListener('click', function () {
        soundEnabled = !soundEnabled;
        const icon = document.getElementById('soundIcon');
        if (soundEnabled) {
            icon.className = 'fa-solid fa-volume-high text-info';
            playBeep(800, 0.1);
        } else {
            icon.className = 'fa-solid fa-volume-xmark text-secondary';
        }
    });

    function copyPeriodId() {
        navigator.clipboard.writeText(currentPeriodId).then(() => {
            alert('Period ID copied: ' + currentPeriodId);
        });
    }

    // Client-side timer tick loop
    setInterval(() => {
        if (remainingSeconds > 0) {
            remainingSeconds--;
            updateClockUI(remainingSeconds);

            // Beep on last 3, 2, 1 seconds
            if (remainingSeconds <= 3 && remainingSeconds > 0) {
                playBeep(440, 0.12);
            }
        }
    }, 1000);

    function updateClockUI(sec) {
        const mins = Math.floor(sec / 60);
        const s = sec % 60;
        document.getElementById('clockMin').textContent = String(mins).padStart(2, '0');
        document.getElementById('clockSec').textContent = String(s).padStart(2, '0');

        const lockOverlay = document.getElementById('lockOverlay');
        const lockNum = document.getElementById('lockCountdownNum');

        if (sec <= 5) {
            lockOverlay.classList.add('active');
            lockNum.textContent = sec;
        } else {
            lockOverlay.classList.remove('active');
        }
    }

    // Realtime Synchronization Polling (every 1 second)
    async function syncLiveStatus() {
        try {
            const res = await fetch(`/api/wingo/status?type=${currentGameType}`);
            const data = await res.json();
            if (data.success) {
                // If period changed, handle transition
                if (data.period_id !== currentPeriodId) {
                    onPeriodChanged(data);
                }

                currentPeriodId = data.period_id;
                remainingSeconds = data.remaining_seconds;
                isLocked = data.is_locked;

                document.getElementById('labelPeriodId').textContent = currentPeriodId;
                if (data.balance !== undefined) {
                    document.getElementById('displayWalletBalance').textContent = '₹' + parseFloat(data.balance).toFixed(2);
                }

                updateClockUI(remainingSeconds);
                renderMiniRecentBalls(data.recent_history);
            }
        } catch (e) {
            console.error('WinGo sync error:', e);
        }
    }
    setInterval(syncLiveStatus, 1000);

    // Period Finish Handler
    let lastShownWinPeriod = null;

    function onPeriodChanged(data) {
        refreshGameHistory();
        refreshMyBets();

        // If player won in this period, trigger victory celebration popup
        if (data.latest_win && data.latest_win.period_id !== lastShownWinPeriod) {
            lastShownWinPeriod = data.latest_win.period_id;
            showWinCelebration(data.latest_win);
        } else {
            // Subtle notification beep on period switch
            playBeep(850, 0.12);
        }
    }

    function showWinCelebration(win) {
        document.getElementById('resPeriodId').textContent = win.period_id;
        const ball = document.getElementById('resBallCircle');
        const num = parseInt(win.winning_number);
        ball.textContent = num;

        let ballClass = 'num-ball-btn ball-green';
        if (num === 0) ballClass = 'num-ball-btn ball-dual-0';
        else if (num === 5) ballClass = 'num-ball-btn ball-dual-5';
        else if ([2, 4, 6, 8].includes(num)) ballClass = 'num-ball-btn ball-red';
        ball.className = ballClass;

        let displayColor = (win.winning_color || '').replace('_', ' + ').toUpperCase();
        document.getElementById('resColorBadge').textContent = displayColor;
        document.getElementById('resSizeBadge').textContent = (win.winning_size || '').toUpperCase();

        const winBox = document.getElementById('resWinAnnouncement');
        winBox.style.display = 'block';
        winBox.innerHTML = `<div class="fw-bold text-success fs-5">🎉 Congratulations!</div>
            <div class="small text-white">You won <span class="fs-4 fw-bold text-warning font-monospace">₹${parseFloat(win.win_amount).toFixed(2)}</span> in Period #${win.period_id}!</div>`;

        const resModal = new bootstrap.Modal(document.getElementById('resultModal'));
        resModal.show();
        playWinChime();
    }

    function renderMiniRecentBalls(history) {
        if (!history || history.length === 0) return;
        const container = document.getElementById('recentWinningBallsMini');
        let html = '';
        history.slice(0, 10).forEach(h => {
            const num = parseInt(h.winning_number);
            let ballClass = 'ball-green';
            if (num === 0) ballClass = 'ball-dual-0';
            else if (num === 5) ballClass = 'ball-dual-5';
            else if ([2, 4, 6, 8].includes(num)) ballClass = 'ball-red';
            html += `<span class="ball-mini ${ballClass}" title="Period ${h.period_id}">${num}</span>`;
        });
        container.innerHTML = html;
    }

    // Unified Choice Selector (Handles Desktop Sidebar Console + Mobile Modal)
    function handleChoiceSelect(type, selection, expectedMultiplier) {
        if (remainingSeconds <= 5) {
            alert('This round is locked! Please wait for the next period to start.');
            return;
        }

        selectedBetType = type;
        selectedBetSelection = selection;
        selectedExpectedMultiplier = expectedMultiplier;

        // Update Desktop Sidebar Stake Console
        document.getElementById('sideSelectionText').textContent = `${type.toUpperCase()}: ${selection}`;
        document.getElementById('sideSelectionBadge').textContent = `${selection} (${expectedMultiplier}X)`;
        document.getElementById('sideExpectedMult').textContent = `${expectedMultiplier}X`;

        // Update Mobile Modal elements
        document.getElementById('betModalTitle').textContent = `Select ${selection}`;
        const modalBadge = document.getElementById('betModalSelectionBadge');
        modalBadge.textContent = `${type.toUpperCase()}: ${selection}`;
        document.getElementById('labelExpectedMultiplier').textContent = `${expectedMultiplier}X`;

        calculateTotalAmount();
        playBeep(700, 0.08);

        // If screen is mobile/tablet (< 992px), open bottom sheet modal
        if (window.innerWidth < 992) {
            const modal = new bootstrap.Modal(document.getElementById('betSheetModal'));
            modal.show();
        } else {
            // On desktop, highlight the sidebar stake console smoothly
            const consoleCard = document.getElementById('desktopStakeConsole');
            consoleCard.style.outline = '2px solid #38bdf8';
            setTimeout(() => { consoleCard.style.outline = 'none'; }, 600);
        }
    }

    function setUnitPrice(price, el) {
        selectedUnitPrice = price;
        document.querySelectorAll('.contract-pill').forEach(p => p.classList.remove('active'));
        if (el) el.classList.add('active');
        calculateTotalAmount();
        playBeep(650, 0.05);
    }

    function stepMultiplier(delta) {
        let val = selectedMultiplier + delta;
        val = Math.max(1, Math.min(1000, val));
        selectedMultiplier = val;
        syncMultiplierInputs(val);
        calculateTotalAmount();
    }

    function setModalMultiplier(mult) {
        selectedMultiplier = mult;
        syncMultiplierInputs(mult);
        calculateTotalAmount();
    }

    function setQuickMultiplier(mult, el) {
        document.querySelectorAll('.multiplier-chip').forEach(c => c.classList.remove('active'));
        if (el) el.classList.add('active');
        setModalMultiplier(mult);
    }

    function onSideMultiplierInput(val) {
        selectedMultiplier = Math.max(1, parseInt(val) || 1);
        syncMultiplierInputs(selectedMultiplier);
        calculateTotalAmount();
    }

    function onModalMultiplierInput(val) {
        selectedMultiplier = Math.max(1, parseInt(val) || 1);
        syncMultiplierInputs(selectedMultiplier);
        calculateTotalAmount();
    }

    function syncMultiplierInputs(val) {
        const sideInput = document.getElementById('sideInputMultiplier');
        const modalInput = document.getElementById('inputMultiplier');
        if (sideInput) sideInput.value = val;
        if (modalInput) modalInput.value = val;
    }

    function pickRandomBet() {
        const randNum = Math.floor(Math.random() * 10);
        handleChoiceSelect('number', String(randNum), 9.0);
    }

    function calculateTotalAmount() {
        const total = selectedUnitPrice * selectedMultiplier;
        const estReturn = total * selectedExpectedMultiplier;

        // Desktop sidebar labels
        document.getElementById('sideTotalBetAmt').textContent = '₹' + total.toFixed(2);
        document.getElementById('sideEstimatedReturn').textContent = '₹' + estReturn.toFixed(2);

        // Mobile modal labels
        document.getElementById('labelTotalBetAmount').textContent = '₹' + total.toFixed(2);
    }

    // Submit Bet Order via AJAX
    async function submitBetOrder() {
        const total = selectedUnitPrice * selectedMultiplier;
        const minBet = {{ (float) ($setting->wingo_min_bet ?? 1.00) }};
        const maxBet = {{ (float) ($setting->wingo_max_bet ?? 50000.00) }};

        if (total < minBet) {
            alert(`Minimum bet amount for WinGo is ₹${minBet.toFixed(2)}.`);
            return;
        }
        if (total > maxBet) {
            alert(`Maximum bet amount for WinGo is ₹${maxBet.toFixed(2)}.`);
            return;
        }

        const sideBtn = document.getElementById('sideBtnConfirmBet');
        const modalBtn = document.getElementById('btnConfirmBet');

        const originalText = '<i class="fa-solid fa-bolt me-1"></i> Place Bet Now';
        sideBtn.disabled = true;
        sideBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Placing Bet...';
        if (modalBtn) modalBtn.disabled = true;

        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch('/api/wingo/place-bet', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    game_type: currentGameType,
                    bet_type: selectedBetType,
                    bet_selection: selectedBetSelection,
                    unit_price: selectedUnitPrice,
                    multiplier: selectedMultiplier
                })
            });

            const data = await res.json();
            if (data.success) {
                playWinChime();
                const modalInstance = bootstrap.Modal.getInstance(document.getElementById('betSheetModal'));
                if (modalInstance) modalInstance.hide();

                document.getElementById('displayWalletBalance').textContent = '₹' + parseFloat(data.new_balance).toFixed(2);
                alert(`Bet placed successfully! Period #${data.period_id}`);
                refreshMyBets();
            } else {
                alert(data.message || 'Failed to place bet.');
            }
        } catch (e) {
            alert('Error placing bet. Please check your network.');
        } finally {
            sideBtn.disabled = false;
            sideBtn.innerHTML = originalText;
            if (modalBtn) modalBtn.disabled = false;
        }
    }

    // Tab Switching for History / Chart / My Bets
    function switchRecordTab(tab) {
        document.querySelectorAll('.record-tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tabContentHistory').style.display = 'none';
        document.getElementById('tabContentChart').style.display = 'none';
        document.getElementById('tabContentMyBets').style.display = 'none';

        if (tab === 'history') {
            document.getElementById('tabBtnHistory').classList.add('active');
            document.getElementById('tabContentHistory').style.display = 'block';
            refreshGameHistory();
        } else if (tab === 'chart') {
            document.getElementById('tabBtnChart').classList.add('active');
            document.getElementById('tabContentChart').style.display = 'block';
        } else if (tab === 'myBets') {
            document.getElementById('tabBtnMyBets').classList.add('active');
            document.getElementById('tabContentMyBets').style.display = 'block';
            refreshMyBets();
        }
    }

    async function refreshGameHistory() {
        try {
            const res = await fetch(`/api/wingo/history?type=${currentGameType}`);
            const data = await res.json();
            if (data.data) {
                const tbody = document.getElementById('gameHistoryTableBody');
                let html = '';
                data.data.forEach(item => {
                    const num = parseInt(item.winning_number);
                    let ballClass = 'ball-green';
                    if (num === 0) ballClass = 'ball-dual-0';
                    else if (num === 5) ballClass = 'ball-dual-5';
                    else if ([2, 4, 6, 8].includes(num)) ballClass = 'ball-red';

                    const wc = (item.winning_color || '').toLowerCase();
                    let colors = '';
                    if (wc.includes('green')) colors += '<span class="badge bg-success me-1">Green</span>';
                    if (wc.includes('violet')) colors += '<span class="badge me-1" style="background:#8b5cf6; color:#fff;">Violet</span>';
                    if (wc.includes('red')) colors += '<span class="badge bg-danger">Red</span>';

                    const sizeStr = (item.winning_size || 'SMALL').toUpperCase();
                    const sizeBadge = sizeStr === 'BIG' 
                        ? '<span class="badge bg-warning text-dark">BIG</span>' 
                        : '<span class="badge bg-primary">SMALL</span>';

                    html += `<tr>
                        <td><span class="period-id-cell">${item.period_id}</span></td>
                        <td class="text-center"><span class="ball-mini ${ballClass}">${num}</span></td>
                        <td class="text-center">${sizeBadge}</td>
                        <td class="text-center">${colors}</td>
                    </tr>`;
                });
                tbody.innerHTML = html;

                // Dynamically update Trend Chart matrix as well
                const chartBody = document.getElementById('trendChartBody');
                if (chartBody) {
                    let chartHtml = '';
                    data.data.slice(0, 15).forEach(rh => {
                        const winNum = parseInt(rh.winning_number);
                        chartHtml += `<tr><td><span class="period-id-cell small">${String(rh.period_id).slice(-5)}</span></td>`;
                        for (let i = 0; i <= 9; i++) {
                            if (winNum === i) {
                                let bc = 'ball-green';
                                if (i === 0) bc = 'ball-dual-0';
                                else if (i === 5) bc = 'ball-dual-5';
                                else if ([2, 4, 6, 8].includes(i)) bc = 'ball-red';
                                chartHtml += `<td><span class="ball-mini ${bc}" style="width:26px; height:26px; font-size:0.8rem;">${i}</span></td>`;
                            } else {
                                chartHtml += `<td><span class="opacity-50" style="color: var(--wingo-text-muted);">•</span></td>`;
                            }
                        }
                        chartHtml += '</tr>';
                    });
                    chartBody.innerHTML = chartHtml;
                }
            }
        } catch (e) {}
    }

    async function refreshMyBets() {
        try {
            const res = await fetch(`/api/wingo/my-bets?type=${currentGameType}`);
            const data = await res.json();
            const tbody = document.getElementById('myBetsTableBody');
            if (data.data && data.data.length > 0) {
                let html = '';
                data.data.forEach(b => {
                    let statusBadge = '<span class="badge bg-secondary">Pending</span>';
                    if (b.status === 'won') statusBadge = '<span class="badge bg-success">Won</span>';
                    else if (b.status === 'lost') statusBadge = '<span class="badge bg-danger">Lost</span>';

                    html += `<tr>
                        <td><span class="period-id-cell small">${b.period_id}</span></td>
                        <td><span class="badge bg-dark">${b.bet_selection}</span></td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="text-end font-monospace">₹${parseFloat(b.total_amount).toFixed(2)}</td>
                        <td class="text-end font-monospace text-success fw-bold">₹${parseFloat(b.win_amount).toFixed(2)}</td>
                    </tr>`;
                });
                tbody.innerHTML = html;
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No bets found for this game.</td></tr>';
            }
        } catch (e) {}
    }
</script>
@endsection
