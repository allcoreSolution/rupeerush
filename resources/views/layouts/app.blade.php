<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'rupeerush - Live Results & Real Time Play')</title>
    <meta name="description" content="Official rupeerush Gaming Portal. Clear, accurate and time-wise live updates with online play." />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />

    <style>
        /* Global Full-Width Foundation & Prevent Horizontal Scroll */
        html, body {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
            margin: 0;
            padding: 0;
        }

        /* ----------------------------------------------------
           SITE HEADER - LOTUS365 DEEP GREEN ARCHITECTURE
           ---------------------------------------------------- */
        .site-header {
            width: 100% !important;
            max-width: 100% !important;
            background: #0c5e3d !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.45);
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1030 !important;
            padding: 0 !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.25);
        }

        /* Row 1: Brand & User Navigation */
        .header-top-row {
            width: 100% !important;
            max-width: 100% !important;
            padding: 6px 0;
            background: #0c5e3d !important;
            border-bottom: none !important;
        }

        /* Lotus365 Header Components */
        .btn-lotus-hamburger {
            background: transparent;
            border: none;
            padding: 4px;
            display: inline-flex;
            flex-direction: column;
            justify-content: center;
            gap: 4px;
            cursor: pointer;
            width: 32px;
            height: 32px;
            flex-shrink: 0;
        }
        .btn-lotus-hamburger span {
            display: block;
            width: 22px;
            height: 3px;
            background: #ffffff;
            border-radius: 2px;
        }
        .lotus-brand-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 900;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            line-height: 1;
        }
        .lotus-brand-title .lotus-brand-rush {
            color: #f5c518;
            margin-left: 1px;
        }
        .btn-lotus-header-search {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 1.2rem;
            padding: 6px 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.15s ease, color 0.15s ease;
        }
        .btn-lotus-header-search:hover {
            transform: scale(1.1);
            color: #f5c518;
        }
        .lotus-profile-badge-card {
            background: #ffffff !important;
            border: 1px solid #d1d5db !important;
            border-radius: 6px !important;
            padding: 2px 9px 3px !important;
            display: inline-flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 44px !important;
            cursor: pointer;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2) !important;
            text-decoration: none !important;
            line-height: 1 !important;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .lotus-profile-badge-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.25) !important;
        }
        .lotus-profile-avatar-circle {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #0284c7;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            margin-bottom: 2px;
        }
        .lotus-profile-balance-text {
            font-size: 0.72rem;
            font-weight: 800;
            color: #0f172a !important;
            font-family: 'JetBrains Mono', monospace, sans-serif;
            line-height: 1;
        }
        .lotus-header-search-overlay {
            background: #08402a;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            animation: slideDownSearch 0.2s ease-out;
        }
        @keyframes slideDownSearch {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .top-nav-link {
            font-size: 0.85rem;
            font-weight: 700;
            color: #f1f5f9;
            padding: 6px 14px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: all 0.22s ease;
            white-space: nowrap;
        }
        .top-nav-link i {
            font-size: 0.95rem;
            color: #e2e8f0;
            transition: color 0.2s ease;
        }
        .top-nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            transform: translateY(-1px);
        }
        .top-nav-link:hover i {
            color: #f5c518;
        }
        .top-nav-link.active {
            background: #ffffff;
            color: #0c5e3d;
            border: 1px solid #f5c518;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
            font-weight: 800;
        }
        .top-nav-link.active i {
            color: #0c5e3d;
        }

        .user-avatar-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #090e1a;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.35);
        }

        /* Mobile Row 1 Profile Trigger */
        .btn-mobile-profile-trigger {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 50px;
            padding: 5px 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #ffffff;
        }
        .btn-mobile-profile-trigger:hover,
        .btn-mobile-profile-trigger:active {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.25);
        }
        .user-avatar-mobile {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #090e1a;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            flex-shrink: 0;
        }
        .user-name-mobile {
            font-size: 0.82rem;
            font-weight: 800;
            color: #ffffff;
            max-width: 95px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .wallet-pill-mobile {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.35);
            border-radius: 50px;
            padding: 5px 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            color: #10b981;
            font-weight: 800;
            font-family: 'JetBrains Mono', monospace;
        }

        /* Row 2: Premium Dark Quick Games Bar */
        .header-games-bar {
            width: 100% !important;
            max-width: 100% !important;
            background: #060911;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            padding: 8px 0;
            position: relative;
            overflow: hidden;
        }
        .games-scroll-strip {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
            padding: 2px 0;
        }
        .games-scroll-strip::-webkit-scrollbar {
            display: none;
        }

        .game-nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 16px;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.09);
            color: #cbd5e1 !important;
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 700;
            white-space: nowrap;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }
        .game-nav-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.22);
            color: #ffffff !important;
            transform: translateY(-1px);
        }
        .game-nav-btn.active {
            background: #ffffff !important;
            border-color: #f59e0b !important;
            color: #070b14 !important;
            font-weight: 800;
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.25), 0 0 0 1px rgba(251, 191, 36, 0.5) !important;
        }
        .game-nav-btn.active .game-btn-icon {
            color: #d97706 !important;
        }
        .game-nav-btn.active .game-btn-tag {
            background: #070b14;
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.35);
        }

        /* Subtle icon accent colors */
        .game-nav-lottery .game-btn-icon { color: #38bdf8; }
        .game-nav-wingo .game-btn-icon { color: #f472b6; }
        .game-nav-andar .game-btn-icon { color: #fbbf24; }
        .game-nav-teenpatti .game-btn-icon { color: #34d399; }
        .game-nav-roulette .game-btn-icon { color: #f87171; }
        .game-nav-aviator .game-btn-icon { color: #fb7185; }
        .game-nav-mines .game-btn-icon { color: #fbbf24; }
        .game-nav-vortex .game-btn-icon { color: #22d3ee; }
        .game-nav-horse .game-btn-icon { color: #4ade80; }

        .game-btn-icon {
            font-size: 0.92rem;
            display: inline-flex;
            align-items: center;
        }
        .game-btn-tag {
            font-size: 0.65rem;
            font-weight: 800;
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.25);
            color: #fbbf24;
            padding: 1px 7px;
            border-radius: 20px;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: 0.3px;
        }

        /* Mobile Profile Offcanvas */
        .mobile-profile-offcanvas {
            max-width: 380px;
            background: linear-gradient(180deg, #0d1630 0%, #060b18 100%) !important;
            border-left: 1px solid rgba(56, 189, 248, 0.25) !important;
            box-shadow: -10px 0 40px rgba(0, 0, 0, 0.8) !important;
            color: #f1f5f9;
        }
        .profile-drawer-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: rgba(56, 189, 248, 0.15);
            border: 1px solid rgba(56, 189, 248, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
        }
        .profile-offcanvas-usercard {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            padding: 16px;
            position: relative;
            overflow: hidden;
        }
        .profile-offcanvas-usercard::before {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 120px;
            height: 120px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .user-big-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border: 2px solid rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.4);
        }
        .profile-menu-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 14px;
            border-radius: 14px;
            text-decoration: none;
            color: #e2e8f0;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.2s ease;
            margin-bottom: 8px;
        }
        .profile-menu-item:hover, .profile-menu-item.active {
            background: rgba(56, 189, 248, 0.1);
            border-color: rgba(56, 189, 248, 0.4);
            color: #ffffff;
            transform: translateX(3px);
        }
        .profile-menu-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .profile-menu-title {
            font-weight: 700;
            font-size: 0.92rem;
            color: #f8fafc;
            line-height: 1.2;
        }
        .profile-menu-desc {
            font-size: 0.72rem;
            color: #94a3b8;
            line-height: 1.2;
        }

        .wallet-pill {
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.35);
            color: #ffffff;
            padding: 6px 14px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 0.88rem;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }
        .wallet-pill:hover {
            background: rgba(245, 158, 11, 0.16);
            border-color: rgba(251, 191, 36, 0.55);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.25);
            transform: translateY(-1px);
        }
        .wallet-amt {
            color: #10b981;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.95rem;
            font-weight: 800;
        }
        .btn-deposit-nav {
            background: #10b981;
            color: #ffffff;
            font-weight: 800;
            padding: 7px 18px;
            border-radius: 50px;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
            white-space: nowrap;
        }
        .btn-deposit-nav:hover {
            background: #059669;
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45);
            transform: translateY(-1px);
        }
        .btn-withdraw-nav {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #e2e8f0;
            font-weight: 700;
            padding: 7px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        .btn-withdraw-nav:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.3);
            color: #ffffff;
            transform: translateY(-1px);
        }
        .btn-login-nav {
            background: #ffffff;
            color: #070b14;
            border: 1px solid rgba(245, 158, 11, 0.4);
            font-weight: 800;
            padding: 7px 18px;
            border-radius: 50px;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 10px rgba(245, 158, 11, 0.2);
        }
        .btn-login-nav:hover {
            background: #fef08a;
            color: #000000;
            border-color: #f59e0b;
            box-shadow: 0 4px 16px rgba(245, 158, 11, 0.35);
            transform: translateY(-1px);
        }
        .user-dropdown-btn {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 50px;
            padding: 5px 14px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #ffffff;
            transition: all 0.2s ease;
        }
        .user-dropdown-btn:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* Floating Support Buttons (WhatsApp & Email) */
        .floating-support-container {
            position: fixed;
            bottom: 24px;
            right: 22px;
            z-index: 1060;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 12px;
        }
        .floating-support-btn {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            border-radius: 50px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            padding: 4px;
        }
        .floating-support-btn:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        }
        .floating-support-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.45rem;
            color: #ffffff;
            flex-shrink: 0;
        }
        .floating-support-label {
            max-width: 0;
            overflow: hidden;
            white-space: nowrap;
            transition: max-width 0.35s ease, padding 0.35s ease;
            padding: 0;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.3px;
        }
        .floating-support-btn:hover .floating-support-label {
            max-width: 240px;
            padding: 0 14px 0 16px;
        }
        .btn-floating-whatsapp {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        }
        .btn-floating-whatsapp .floating-support-icon {
            background: rgba(255, 255, 255, 0.2);
        }
        .btn-floating-email {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        }
        .btn-floating-email .floating-support-icon {
            background: rgba(255, 255, 255, 0.2);
        }
        .floating-support-pulse {
            position: absolute;
            inset: 0;
            border-radius: 50px;
            background: inherit;
            opacity: 0.6;
            animation: floating-ripple 2.2s infinite ease-out;
            pointer-events: none;
            z-index: -1;
        }
        @keyframes floating-ripple {
            0% {
                transform: scale(0.96);
                opacity: 0.6;
            }
            100% {
                transform: scale(1.35);
                opacity: 0;
            }
        }
        /* Mobile & Tablet App-like Bottom Navigation Bar */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            background: #090e1a;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 6px 4px 8px;
            z-index: 1050;
            box-shadow: 0 -4px 25px rgba(0, 0, 0, 0.7);
        }

        /* Site Footer: Sticky / Fixed at Bottom */
        .site-footer {
            width: 100% !important;
            max-width: 100% !important;
            background: #060911 !important;
            border-top: 1px solid rgba(255, 255, 255, 0.12) !important;
            color: #94a3b8;
            padding: 8px 0 !important;
            font-size: 0.8rem;
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1025 !important;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.6) !important;
            box-sizing: border-box !important;
        }

        .footer-content-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: nowrap;
            gap: 12px;
        }
        .footer-links {
            margin-bottom: 0 !important;
            padding-left: 0 !important;
            list-style: none;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.78rem;
        }
        .footer-links li a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .footer-links li a:hover {
            color: #f59e0b;
        }
        .footer-divider {
            color: rgba(255, 255, 255, 0.2);
            font-size: 0.72rem;
        }
        .footer-tz {
            font-size: 0.7rem;
            font-family: 'JetBrains Mono', monospace;
            color: #64748b;
            white-space: nowrap;
        }

        @media (max-width: 991.98px) {
            .site-footer {
                display: none !important;
            }
        }
        .mobile-nav-item {
            color: #94a3b8;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            font-size: 0.72rem;
            font-weight: 700;
            transition: all 0.2s ease;
            flex: 1;
            text-align: center;
            background: none;
            border: none;
            padding: 4px 0;
            position: relative;
        }
        .mobile-nav-item i {
            font-size: 1.25rem;
            transition: transform 0.2s ease, color 0.2s ease;
        }
        .mobile-nav-item:hover, .mobile-nav-item.active {
            color: #ffffff;
        }
        .mobile-nav-item.active i {
            transform: translateY(-2px);
            color: #f59e0b;
            filter: drop-shadow(0 2px 8px rgba(245, 158, 11, 0.6));
        }

        /* Center Elevated Games Trigger Button */
        .btn-mobile-games {
            position: relative;
        }
        .btn-mobile-games .games-center-pill {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            margin-top: -18px;
            box-shadow: 0 4px 16px rgba(245, 158, 11, 0.55), 0 0 0 4px #070e1c;
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .btn-mobile-games .games-center-pill i {
            font-size: 1.3rem;
            color: #ffffff !important;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.4));
        }
        .btn-mobile-games:hover .games-center-pill,
        .btn-mobile-games.active .games-center-pill {
            transform: translateY(-3px) scale(1.08);
            box-shadow: 0 8px 22px rgba(245, 158, 11, 0.8), 0 0 0 4px #070e1c;
        }

        /* Mobile & Tablet Games Selection Modal */
        .mobile-games-modal .modal-content {
            background: linear-gradient(180deg, #0d1630 0%, #060b18 100%);
            border: 1px solid rgba(56, 189, 248, 0.25);
            border-radius: 24px;
            box-shadow: 0 16px 50px rgba(0, 0, 0, 0.85);
            color: #f1f5f9;
        }
        .game-sheet-header-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
        }
        .game-sheet-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 16px;
            text-decoration: none;
            color: #f1f5f9;
            border: 1px solid rgba(255, 255, 255, 0.08);
            position: relative;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        .game-sheet-card:hover {
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.45);
        }
        .game-sheet-card.is-active-game {
            border-color: #38bdf8;
            box-shadow: 0 0 16px rgba(56, 189, 248, 0.35);
        }
        .game-sheet-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        .game-sheet-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 0.96rem;
            line-height: 1.2;
            letter-spacing: -0.2px;
        }
        .game-sheet-tag {
            font-size: 0.72rem;
            font-weight: 700;
            margin-top: 2px;
        }
        .game-sheet-badge {
            position: absolute;
            top: 7px;
            right: 8px;
            font-size: 0.62rem;
            font-weight: 800;
            text-transform: uppercase;
            padding: 2px 6px;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.12);
            color: #f1f5f9;
            letter-spacing: 0.5px;
        }
        .game-sheet-badge.live {
            background: #ef4444;
            color: #ffffff;
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);
        }

        /* Specific Game Card Gradients */
        .card-mines { background: linear-gradient(135deg, rgba(16, 185, 129, 0.14) 0%, rgba(5, 150, 105, 0.04) 100%); }
        .card-mines .game-sheet-icon { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; }

        .card-aviator { background: linear-gradient(135deg, rgba(225, 29, 72, 0.14) 0%, rgba(234, 88, 12, 0.04) 100%); }
        .card-aviator .game-sheet-icon { background: linear-gradient(135deg, #e11d48 0%, #ea580c 100%); color: #fff; }

        .card-wingo { background: linear-gradient(135deg, rgba(236, 72, 153, 0.14) 0%, rgba(139, 92, 246, 0.04) 100%); }
        .card-wingo .game-sheet-icon { background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%); color: #fff; }

        .card-lottery { background: linear-gradient(135deg, rgba(245, 158, 11, 0.14) 0%, rgba(180, 83, 9, 0.04) 100%); }
        .card-lottery .game-sheet-icon { background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%); color: #fff; }

        .card-vortex { background: linear-gradient(135deg, rgba(6, 182, 212, 0.14) 0%, rgba(2, 132, 199, 0.04) 100%); }
        .card-vortex .game-sheet-icon { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); color: #fff; }

        .card-andar { background: linear-gradient(135deg, rgba(245, 158, 11, 0.14) 0%, rgba(194, 65, 12, 0.04) 100%); }
        .card-andar .game-sheet-icon { background: linear-gradient(135deg, #f59e0b 0%, #c2410c 100%); color: #fff; }

        .card-teenpatti { background: linear-gradient(135deg, rgba(5, 150, 105, 0.14) 0%, rgba(6, 78, 59, 0.04) 100%); }
        .card-teenpatti .game-sheet-icon { background: linear-gradient(135deg, #059669 0%, #064e3b 100%); color: #fff; }

        .card-roulette { background: linear-gradient(135deg, rgba(245, 158, 11, 0.14) 0%, rgba(220, 38, 38, 0.04) 100%); }
        .card-roulette .game-sheet-icon { background: linear-gradient(135deg, #f59e0b 0%, #dc2626 100%); color: #fff; }

        .card-cricket { background: linear-gradient(135deg, rgba(14, 165, 233, 0.14) 0%, rgba(2, 132, 199, 0.04) 100%); }
        .card-cricket .game-sheet-icon { background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #fff; }

        @media (max-width: 991px) {
            body {
                padding-bottom: 74px;
            }

            /* Mobile View: Balanced Left & Right Screen Margins across Whole Screen */
            .site-header .header-top-row > .container-fluid {
                padding-left: 8px !important;
                padding-right: 8px !important;
            }

            .rupeerush-page-body {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .rupeerush-page-body > .container-fluid,
            .rupeerush-page-body .container-fluid {
                padding-left: 8px !important;
                padding-right: 8px !important;
            }

            .floating-support-container {
                bottom: 84px !important;
                right: 12px !important;
                gap: 8px !important;
            }
            .floating-support-icon {
                width: 44px;
                height: 44px;
                font-size: 1.3rem;
            }

            /* Dedicated In-Game Mobile Viewport Optimization */
            body.is-game-active {
                padding-bottom: 56px !important;
            }
            body.is-game-active .header-top-row {
                padding: 5px 0 !important;
            }
            body.is-game-active .brand-title {
                font-size: 1.05rem !important;
            }
            body.is-game-active .brand-tagline {
                display: none !important;
            }
            body.is-game-active .logo-bars {
                height: 18px !important;
            }
            body.is-game-active .btn-mobile-profile-trigger {
                padding: 3px 8px !important;
                font-size: 0.74rem !important;
            }
            body.is-game-active .header-games-bar {
                display: none !important;
            }
            body.is-game-active .floating-support-container {
                display: none !important;
            }
            body.is-game-active .container-xl,
            body.is-game-active .container-fluid,
            body.is-game-active .container {
                padding-left: 6px !important;
                padding-right: 6px !important;
                padding-top: 4px !important;
            }
            body.is-game-active .mobile-bottom-nav {
                padding: 4px 2px 4px !important;
            }
            body.is-game-active .mobile-nav-item span {
                font-size: 0.65rem !important;
            }
            body.is-game-active .btn-mobile-games .games-center-pill {
                width: 36px !important;
                height: 36px !important;
                margin-top: -12px !important;
            }
        }

        /* ========================================================
           RUPEERUSH MODERN LEFT SIDEBAR ARCHITECTURE
           ======================================================== */
        :root {
            --site-header-height: 48px;
            --site-footer-height: 42px;
        }

        .rupeerush-app-shell {
            display: flex;
            width: 100% !important;
            max-width: 100% !important;
            margin-top: var(--site-header-height, 48px) !important;
            margin-bottom: var(--site-footer-height, 42px) !important;
            min-height: calc(100vh - var(--site-header-height, 48px) - var(--site-footer-height, 42px));
            position: relative;
            background: #070e1c;
        }

        @media (max-width: 991.98px) {
            .rupeerush-app-shell {
                margin-bottom: 56px !important;
                min-height: calc(100vh - var(--site-header-height, 48px) - 56px);
            }
        }

        .rupeerush-main-column {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            flex-direction: column;
            width: 100%;
            padding-bottom: 16px;
            transition: margin-left 0.28s cubic-bezier(0.4, 0, 0.2, 1), width 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .rupeerush-page-body {
            flex: 1 0 auto;
            width: 100%;
            min-width: 0;
        }

        /* Sidebar Container on Desktop (>= 1200px) */
        @media (min-width: 1200px) {
            .rupeerush-left-sidebar {
                position: fixed !important;
                top: var(--site-header-height, 48px) !important;
                bottom: var(--site-footer-height, 42px) !important;
                left: 0 !important;
                width: 275px !important;
                height: calc(100vh - var(--site-header-height, 48px) - var(--site-footer-height, 42px)) !important;
                max-height: calc(100vh - var(--site-header-height, 48px) - var(--site-footer-height, 42px)) !important;
                background: #091222;
                border-right: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: 4px 0 20px rgba(0, 0, 0, 0.35);
                overflow-y: auto !important;
                overflow-x: hidden !important;
                z-index: 1020 !important;
                transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease !important;
                transform: translateX(0);
            }

            .rupeerush-main-column {
                margin-left: 275px !important;
                width: calc(100% - 275px) !important;
                min-width: 0 !important;
            }

            /* Collapsed Desktop State */
            body.sidebar-collapsed .rupeerush-left-sidebar {
                transform: translateX(-100%) !important;
                opacity: 0 !important;
                pointer-events: none !important;
            }

            body.sidebar-collapsed .rupeerush-main-column {
                margin-left: 0 !important;
                width: 100% !important;
            }

            .floating-support-container {
                bottom: calc(var(--site-footer-height, 42px) + 16px) !important;
                right: 22px !important;
                z-index: 1028 !important;
            }
        }

        /* Custom Scrollbar for Left Sidebar */
        .rupeerush-left-sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .rupeerush-left-sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
        }
        .rupeerush-left-sidebar::-webkit-scrollbar-thumb {
            background: rgba(245, 158, 11, 0.3);
            border-radius: 4px;
        }
        .rupeerush-left-sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(245, 158, 11, 0.6);
        }

        /* Sidebar Brand Header */
        .sidebar-header-box {
            background: #08101d;
            height: 48px;
            flex-shrink: 0;
        }
        .sidebar-brand-pill {
            font-family: 'Outfit', sans-serif;
            font-weight: 900;
            font-size: 1.05rem;
            letter-spacing: 0.5px;
            color: #ffffff;
        }
        .sidebar-pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
            display: inline-block;
            animation: sidebarPulse 1.8s infinite;
        }
        @keyframes sidebarPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); opacity: 0.6; }
        }

        .btn-sidebar-collapse,
        .btn-sidebar-close {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #94a3b8;
            cursor: pointer;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .btn-sidebar-collapse:hover,
        .btn-sidebar-close:hover {
            background: rgba(245, 158, 11, 0.15);
            border-color: #f59e0b;
            color: #fbbf24;
        }

        /* User Card in Sidebar */
        .sidebar-user-card {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(12, 94, 61, 0.2) 100%);
            border: 1px solid rgba(16, 185, 129, 0.25);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
        }
        .sidebar-user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #070e1c;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 800;
            flex-shrink: 0;
        }
        .sidebar-user-name {
            font-size: 0.82rem;
            line-height: 1.2;
        }
        .sidebar-user-sub {
            font-size: 0.7rem;
        }
        .sidebar-balance-row {
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        .sidebar-balance-val {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 800;
        }
        .btn-sidebar-deposit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            font-size: 0.75rem;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
            transition: transform 0.15s ease, filter 0.15s ease;
        }
        .btn-sidebar-deposit:hover {
            transform: translateY(-1px);
            filter: brightness(1.1);
            color: #ffffff;
        }
        .btn-sidebar-withdraw {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            font-size: 0.75rem;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
            transition: transform 0.15s ease, filter 0.15s ease;
        }
        .btn-sidebar-withdraw:hover {
            transform: translateY(-1px);
            filter: brightness(1.1);
            color: #ffffff;
        }

        /* Guest Card */
        .sidebar-guest-card {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        /* Search Box */
        .sidebar-search-box {
            position: relative;
        }
        .sidebar-search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.75rem;
            color: #94a3b8;
            pointer-events: none;
        }
        .sidebar-search-input {
            background: #0c172a !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 8px !important;
            color: #ffffff !important;
            font-size: 0.78rem !important;
            font-weight: 600 !important;
            padding: 5px 26px 5px 28px !important;
            box-shadow: none !important;
            transition: all 0.2s ease !important;
        }
        .sidebar-search-input:focus {
            border-color: #f59e0b !important;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2) !important;
            background: #101e38 !important;
        }
        .sidebar-search-input::placeholder {
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .btn-sidebar-search-clear {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 0.72rem;
            cursor: pointer;
            padding: 0;
        }
        .btn-sidebar-search-clear:hover {
            color: #ffffff;
        }

        /* Section Title */
        .sidebar-section-title {
            font-size: 0.66rem;
            font-weight: 800;
            letter-spacing: 0.6px;
            color: #64748b;
        }

        /* Menu List */
        .sidebar-menu-list {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .sidebar-menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 9px;
            border-radius: 8px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 700;
            transition: all 0.18s ease;
            border: 1px solid transparent;
            position: relative;
            background: transparent;
        }
        .sidebar-menu-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.08);
            transform: translateX(2px);
        }
        .sidebar-menu-item.active {
            background: linear-gradient(90deg, rgba(16, 185, 129, 0.2) 0%, rgba(16, 185, 129, 0.04) 100%);
            color: #ffffff;
            border-color: rgba(16, 185, 129, 0.35);
            border-left: 3px solid #10b981;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.35);
        }

        /* Icon Box inside Menu */
        .sidebar-item-icon {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }
        .sidebar-menu-item:hover .sidebar-item-icon {
            transform: scale(1.1);
        }

        /* Specific item icon styling */
        .icon-home { background: rgba(56, 189, 248, 0.15); color: #38bdf8; }
        .icon-cricket { background: linear-gradient(135deg, #059669 0%, #064e3b 100%); color: #ffffff; }
        .icon-bets { background: rgba(168, 85, 247, 0.15); color: #a855f7; }
        .icon-passbook { background: rgba(6, 182, 212, 0.15); color: #06b6d4; }
        .icon-refer { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
        .icon-aviator { background: linear-gradient(135deg, #e11d48 0%, #991b1b 100%); color: #ffffff; }
        .icon-wingo { background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%); color: #ffffff; }
        .icon-lottery { background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; }
        .icon-mines { background: linear-gradient(135deg, #182346 0%, #0d152d 100%); color: #fbbf24; }
        .icon-andar { background: linear-gradient(135deg, #c2410c 0%, #9a3412 100%); color: #ffffff; }
        .icon-teenpatti { background: linear-gradient(135deg, #059669 0%, #065f46 100%); color: #ffffff; }
        .icon-dragontiger { background: linear-gradient(135deg, #dc2626 0%, #2563eb 100%); color: #fbbf24; }
        .icon-roulette { background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%); color: #facc15; }
        .icon-vortex { background: linear-gradient(135deg, #091a28 0%, #033c4d 100%); color: #38bdf8; }
        .icon-horse { background: linear-gradient(135deg, #065f46 0%, #022c22 100%); color: #34d399; }
        .icon-deposit { background: rgba(16, 185, 129, 0.15); color: #10b981; }
        .icon-withdraw { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
        .icon-wa { background: rgba(37, 211, 102, 0.15); color: #25d366; }
        .icon-mail { background: rgba(56, 189, 248, 0.15); color: #38bdf8; }
        .icon-admin { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
        .icon-logout { background: rgba(239, 68, 68, 0.15); color: #f87171; }
        .icon-login { background: rgba(16, 185, 129, 0.15); color: #10b981; }
        .icon-register { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }

        /* Item Labels */
        .sidebar-item-content {
            display: flex;
            flex-direction: column;
            min-width: 0;
            flex-grow: 1;
        }
        .sidebar-item-label {
            font-size: 0.82rem;
            font-weight: 700;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-item-sub {
            font-size: 0.65rem;
            color: #64748b;
            font-weight: 500;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Badges */
        .sidebar-item-badge {
            font-size: 0.62rem;
            font-weight: 800;
            padding: 1px 6px;
            border-radius: 50px;
            letter-spacing: 0.3px;
            margin-left: auto;
            flex-shrink: 0;
            font-family: 'JetBrains Mono', monospace;
        }
        .badge-live {
            background: #ef4444;
            color: #ffffff;
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);
        }
        .badge-payout {
            background: rgba(245, 158, 11, 0.18);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.35);
        }
        .badge-hot {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            color: #ffffff;
        }
        .badge-bonus {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.35);
        }
        .badge-tag {
            background: rgba(236, 72, 153, 0.2);
            color: #f472b6;
            border: 1px solid rgba(236, 72, 153, 0.3);
        }
        .badge-online {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.35);
        }

        /* Tablet & Mobile Slide-Out Drawer Mode (< 1200px) */
        @media (max-width: 1199.98px) {
            .rupeerush-left-sidebar {
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                left: 0 !important;
                height: 100vh !important;
                width: 300px !important;
                max-width: 86vw !important;
                z-index: 1070 !important;
                transform: translateX(-100%) !important;
                margin-left: 0 !important;
                opacity: 1 !important;
                box-shadow: 10px 0 35px rgba(0, 0, 0, 0.8) !important;
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }

            body.sidebar-mobile-open .rupeerush-left-sidebar {
                transform: translateX(0) !important;
            }

            .rupeerush-sidebar-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(4px);
                z-index: 1065;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.25s ease, visibility 0.25s ease;
            }

            body.sidebar-mobile-open .rupeerush-sidebar-backdrop {
                opacity: 1;
                visibility: visible;
            }
        }
    </style>
    @yield('styles')
</head>
@php
    $isGameActive = request()->routeIs('aviator.*') || request()->routeIs('wingo.*') || request()->routeIs('mines.*') || request()->routeIs('roulette.*') || request()->routeIs('andar-bahar.*') || request()->routeIs('teen-patti.*') || request()->routeIs('dragon-tiger.*') || request()->routeIs('vortex.*') || request()->routeIs('horse-racing.*') || request()->routeIs('sports.cricket*') || request()->routeIs('lottery.*');
@endphp
<body class="{{ $isGameActive ? 'is-game-active' : '' }}">

    <!-- Main Navigation Header -->
    <!-- Main Navigation Header (2-Row Architecture) -->
    <header class="site-header">
        <!-- Row 1: Brand & Top Main Options (home, my bets, passbook, wallet, withdraw, deposit, my profile) -->
        <div class="header-top-row">
            <div class="container-fluid px-3 px-md-4 px-xl-5">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    
                    <!-- Left: Hamburger & Brand Logo (Lotus365 Architecture) -->
                    <div class="d-flex align-items-center gap-2">
                        <!-- Hamburger Button (Toggles Left All-Games & Nav Sidebar) -->
                        <button type="button" class="btn-lotus-hamburger" id="rupeeRushSidebarToggle" onclick="toggleRupeeRushSidebar()" aria-label="Toggle All Games Sidebar" title="All Games & Navigation Menu">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>

                        <!-- Brand Logo -->
                        <a href="{{ route('home') }}" class="brand-wrapper text-decoration-none d-flex align-items-center">
                            <div class="brand-title lotus-brand-title">
                                <span class="text-white">RUPEE</span><span class="lotus-brand-rush">RUSH</span>
                            </div>
                        </a>
                    </div>

                    <!-- Right Desktop: search, home, my bets, passbook, wallet, withdraw, deposit, my profile -->
                    <div class="d-none d-lg-flex align-items-center gap-2 flex-wrap">
                        <!-- Desktop Search Trigger -->
                        <button type="button" class="btn-lotus-header-search" onclick="toggleHeaderSearchOverlay()" title="Search Games & Sports">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>

                        <!-- 1. Home -->
                        <a href="{{ route('home') }}" class="top-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                            <i class="fa-solid fa-house"></i>
                            <span>Home</span>
                        </a>

                        @auth
                            <!-- 2. My Bets -->
                            <a href="{{ route('bets.history') }}" class="top-nav-link {{ request()->routeIs('bets.history') ? 'active' : '' }}">
                                <i class="fa-solid fa-receipt"></i>
                                <span>My Bets</span>
                            </a>

                            <!-- 3. Passbook -->
                            <a href="{{ route('wallet.transactions') }}" class="top-nav-link {{ request()->routeIs('wallet.transactions') ? 'active' : '' }}">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <span>Passbook</span>
                            </a>

                            <!-- 4. Wallet Balance -->
                            <a href="{{ Auth::user()->isDemo() ? 'javascript:void(0)' : route('wallet.withdraw') }}" class="wallet-pill" title="{{ Auth::user()->isDemo() ? 'Demo Testing Balance' : 'Wallet Balance' }}">
                                <i class="fa-solid fa-coins text-warning"></i>
                                <span class="wallet-amt" id="userWalletBalance">₹{{ number_format(Auth::user()->balance, 2) }}</span>
                                @if(Auth::user()->isDemo())
                                    <span class="badge bg-warning text-dark ms-1 py-0 px-1" style="font-size: 0.62rem; font-weight: 700;">DEMO</span>
                                @endif
                            </a>

                            <!-- 5. Withdraw -->
                            @if(Auth::user()->isDemo())
                                <button type="button" class="btn-withdraw-nav opacity-50" style="cursor: not-allowed;" onclick="alert('Withdraw is disabled in Demo Mode. Virtual demo credits are for game testing only.')" title="Disabled in Demo Mode">
                                    <i class="fa-solid fa-arrow-up-from-bracket"></i>
                                    <span>Withdraw</span>
                                </button>
                            @else
                                <a href="{{ route('wallet.withdraw') }}" class="btn-withdraw-nav" title="Withdraw Funds">
                                    <i class="fa-solid fa-arrow-up-from-bracket"></i>
                                    <span>Withdraw</span>
                                </a>
                            @endif

                            <!-- 6. Deposit -->
                            @if(Auth::user()->isDemo())
                                <button type="button" class="btn-deposit-nav opacity-50" style="cursor: not-allowed;" onclick="alert('Deposit is disabled in Demo Mode. You already have ₹10,000 demo testing chips!')" title="Disabled in Demo Mode">
                                    <i class="fa-solid fa-plus-circle"></i>
                                    <span>Deposit</span>
                                </button>
                            @else
                                <a href="{{ route('wallet.deposit') }}" class="btn-deposit-nav" title="Deposit Balance">
                                    <i class="fa-solid fa-plus-circle"></i>
                                    <span>Deposit</span>
                                </a>
                            @endif

                            <!-- 7. My Profile Dropdown -->
                            <div class="dropdown">
                                <button class="btn user-dropdown-btn dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="user-avatar-circle">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <span>{{ Str::limit(Auth::user()->name, 12) }}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-2" style="min-width: 250px;">
                                    <li class="px-3 py-2 border-bottom mb-1">
                                        <div class="fw-bold text-dark fs-6">{{ Auth::user()->name }} @if(Auth::user()->isDemo()) <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">DEMO</span> @endif</div>
                                        <div class="text-muted small">{{ Auth::user()->phone }}</div>
                                        <div class="text-success small fw-bold mt-1">Balance: ₹{{ number_format(Auth::user()->balance, 2) }}</div>
                                    </li>
                                    @if(Auth::user()->isAdmin())
                                        <li><a class="dropdown-item py-2 rounded-2 text-danger fw-bold" href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-gauge-high me-2"></i>Admin Panel</a></li>
                                    @endif
                                    <li><a class="dropdown-item py-2 rounded-2 fw-semibold text-dark" href="{{ route('referral.index') }}"><i class="fa-solid fa-gift me-2 text-warning"></i>Refer & Earn (Bonus)</a></li>
                                    @if(Auth::user()->isDemo())
                                        <li><a class="dropdown-item py-2 rounded-2 text-muted" href="javascript:void(0)" onclick="alert('Wallet is disabled in Demo Mode.')"><i class="fa-solid fa-wallet me-2 text-secondary"></i>My Wallet <small class="badge bg-secondary ms-1">Disabled</small></a></li>
                                        <li><a class="dropdown-item py-2 rounded-2 text-muted" href="javascript:void(0)" onclick="alert('Withdraw is disabled in Demo Mode.')"><i class="fa-solid fa-money-bill-wave me-2 text-secondary"></i>Withdraw Funds <small class="badge bg-secondary ms-1">Disabled</small></a></li>
                                        <li><a class="dropdown-item py-2 rounded-2 text-muted" href="javascript:void(0)" onclick="alert('Deposit is disabled in Demo Mode.')"><i class="fa-solid fa-qrcode me-2 text-secondary"></i>Deposit via QR <small class="badge bg-secondary ms-1">Disabled</small></a></li>
                                    @else
                                        <li><a class="dropdown-item py-2 rounded-2" href="{{ route('wallet.withdraw') }}"><i class="fa-solid fa-wallet me-2 text-info"></i>My Wallet</a></li>
                                        <li><a class="dropdown-item py-2 rounded-2" href="{{ route('wallet.withdraw') }}"><i class="fa-solid fa-money-bill-wave me-2 text-warning"></i>Withdraw Funds</a></li>
                                        <li><a class="dropdown-item py-2 rounded-2" href="{{ route('wallet.deposit') }}"><i class="fa-solid fa-qrcode me-2 text-success"></i>Deposit via QR</a></li>
                                    @endif
                                    <li><a class="dropdown-item py-2 rounded-2" href="{{ route('bets.history') }}"><i class="fa-solid fa-receipt me-2 text-primary"></i>My Bets History</a></li>
                                    <li><a class="dropdown-item py-2 rounded-2" href="{{ route('wallet.transactions') }}"><i class="fa-solid fa-clock-rotate-left me-2 text-secondary"></i>Passbook / Transactions</a></li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                             @csrf
                                            <button type="submit" class="dropdown-item py-2 rounded-2 text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <!-- Guest State: Links lead to Login / Action -->
                            <a href="{{ route('login') }}" class="top-nav-link">
                                <i class="fa-solid fa-receipt"></i>
                                <span>My Bets</span>
                            </a>
                            <a href="{{ route('login') }}" class="top-nav-link">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <span>Passbook</span>
                            </a>
                            <a href="{{ route('login') }}" class="top-nav-link">
                                <i class="fa-solid fa-wallet"></i>
                                <span>Wallet</span>
                            </a>
                            <a href="{{ route('login') }}" class="btn-withdraw-nav">
                                <i class="fa-solid fa-arrow-up-from-bracket"></i>
                                <span>Withdraw</span>
                            </a>
                            <a href="{{ route('login') }}" class="btn-deposit-nav">
                                <i class="fa-solid fa-plus-circle"></i>
                                <span>Deposit</span>
                            </a>
                            <a href="{{ route('login') }}" class="btn-login-nav">
                                <i class="fa-solid fa-user me-1"></i>
                                <span>My Profile</span>
                            </a>
                            <a href="{{ route('register') }}" class="btn-deposit-nav" style="background: linear-gradient(135deg, #10b981, #059669);" title="Register Account">
                                <i class="fa-solid fa-user-plus"></i>
                                <span>Register</span>
                            </a>
                        @endauth
                    </div>

                    <!-- Right Mobile: Search Trigger & Lotus365 White Profile Badge Card -->
                    <div class="d-flex d-lg-none align-items-center gap-2">
                        <!-- Search Trigger Button -->
                        <button type="button" class="btn-lotus-header-search" onclick="toggleHeaderSearchOverlay()" title="Search Games & Sports">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>

                        <!-- Profile Badge Card: Avatar on Top, Balance/0 on Bottom (Lotus365 exact layout) -->
                        <button type="button" class="lotus-profile-badge-card" data-bs-toggle="offcanvas" data-bs-target="#mobileProfileOffcanvas" aria-controls="mobileProfileOffcanvas" title="My Profile & Options">
                            <div class="lotus-profile-avatar-circle">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div class="lotus-profile-balance-text">
                                @auth {{ number_format(Auth::user()->balance, 0) }} @else 0 @endauth
                            </div>
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Expandable Header Search Overlay -->
        <div class="lotus-header-search-overlay" id="lotusHeaderSearchOverlay" style="display: none;">
            <div class="container-fluid px-3 px-md-4 py-2">
                <div class="d-flex align-items-center bg-white rounded-3 px-3 py-1 shadow-sm">
                    <i class="fa-solid fa-magnifying-glass text-muted me-2"></i>
                    <input type="text" id="headerGameSearchInput" class="form-control border-0 shadow-none py-1 text-dark fw-bold" placeholder="Search events, live cricket, casino tables..." oninput="handleHeaderSearchInput(this.value)">
                    <button type="button" class="btn btn-sm btn-link text-muted p-0 ms-2 text-decoration-none" onclick="toggleHeaderSearchOverlay()" aria-label="Close search">
                        <i class="fa-solid fa-xmark fs-5"></i>
                    </button>
                </div>
            </div>
        </div>

        @if(!request()->routeIs('home'))
        <!-- Row 2: All Games in Premium Colored Buttons (Desktop & Mobile - Not on Home) -->
        <div class="header-games-bar">
            <div class="container-fluid px-3 px-md-4 px-xl-5">
                <div class="games-scroll-strip">
                    <!-- 1. Andar Bahar (A) -->
                    <a href="{{ route('andar-bahar.index') }}" class="game-nav-btn game-nav-andar {{ request()->routeIs('andar-bahar.*') ? 'active' : '' }}">
                        <span class="game-btn-icon"><i class="fa-solid fa-diamond"></i></span>
                        <span>Andar Bahar</span>
                        <span class="game-btn-tag">1.95X</span>
                    </a>

                    <!-- 2. Aviator Crash (A) -->
                    <a href="{{ route('aviator.index') }}" class="game-nav-btn game-nav-aviator {{ request()->routeIs('aviator.*') ? 'active' : '' }}">
                        <span class="game-btn-icon"><i class="fa-solid fa-plane-departure"></i></span>
                        <span>Aviator Crash</span>
                        <span class="game-btn-tag">1000X</span>
                    </a>


                    <!-- 4. Dragon Tiger (D) -->
                    <a href="{{ route('dragon-tiger.index') }}" class="game-nav-btn game-nav-dragontiger {{ request()->routeIs('dragon-tiger.*') ? 'active' : '' }}" style="background: linear-gradient(135deg, rgba(225, 29, 72, 0.15), rgba(30, 58, 138, 0.15)); border: 1px solid rgba(244, 63, 94, 0.4);">
                        <span class="game-btn-icon" style="color: #f59e0b;"><i class="fa-solid fa-dragon"></i></span>
                        <span>Dragon Tiger</span>
                        <span class="game-btn-tag" style="background: linear-gradient(135deg, #ef4444, #3b82f6); color: #fff;">50X</span>
                    </a>

                    <!-- 6. Lottery 2D (L) -->
                    <a href="{{ route('lottery.index') }}" class="game-nav-btn game-nav-lottery {{ request()->routeIs('lottery.*') ? 'active' : '' }}">
                        <span class="game-btn-icon"><i class="fa-solid fa-dice"></i></span>
                        <span>Lottery 2D</span>
                        <span class="game-btn-tag">90X</span>
                    </a>

                    <!-- 7. Mines 5x5 (M) -->
                    <a href="{{ route('mines.index') }}" class="game-nav-btn game-nav-mines {{ request()->routeIs('mines.*') ? 'active' : '' }}">
                        <span class="game-btn-icon"><i class="fa-solid fa-bomb"></i></span>
                        <span>Mines 5x5</span>
                        <span class="game-btn-tag">Arcade</span>
                    </a>

                    <!-- 8. Roulette 36x (R) -->
                    <a href="{{ route('roulette.index') }}" class="game-nav-btn game-nav-roulette {{ request()->routeIs('roulette.*') ? 'active' : '' }}">
                        <span class="game-btn-icon"><i class="fa-solid fa-dharmachakra"></i></span>
                        <span>Roulette 36x</span>
                        <span class="game-btn-tag">Live</span>
                    </a>

                    <!-- 9. Teen Patti 20-20 (T) -->
                    <a href="{{ route('teen-patti.index') }}" class="game-nav-btn game-nav-teenpatti {{ request()->routeIs('teen-patti.*') ? 'active' : '' }}">
                        <span class="game-btn-icon"><i class="fa-solid fa-heart"></i></span>
                        <span>Teen Patti</span>
                        <span class="game-btn-tag">40X</span>
                    </a>

                    <!-- 10. Vortex Wheel (V) -->
                    <a href="{{ route('vortex.index') }}" class="game-nav-btn game-nav-vortex {{ request()->routeIs('vortex.*') ? 'active' : '' }}">
                        <span class="game-btn-icon"><i class="fa-solid fa-arrows-spin"></i></span>
                        <span>Vortex Wheel</span>
                        <span class="game-btn-tag">200X</span>
                    </a>

                    <!-- 11. WinGo Color (W) -->
                    <a href="{{ route('wingo.index') }}" class="game-nav-btn game-nav-wingo {{ request()->routeIs('wingo.*') ? 'active' : '' }}">
                        <span class="game-btn-icon"><i class="fa-solid fa-gamepad"></i></span>
                        <span>WinGo Color</span>
                        <span class="game-btn-tag">30s</span>
                    </a>
                    <!-- 5. Horse Racing Derby (H) -->
                    <a href="{{ route('horse-racing.index') }}" class="game-nav-btn game-nav-horse {{ request()->routeIs('horse-racing.*') ? 'active' : '' }}">
                        <span class="game-btn-icon"><i class="fa-solid fa-horse-head"></i></span>
                        <span>Horse Racing</span>
                        <span class="game-btn-tag">Derby</span>
                    </a>

                    <!-- 3. Cricket Live Sports (C) -->
                    <a href="{{ route('sports.cricket') }}" class="game-nav-btn game-nav-cricket {{ request()->routeIs('sports.cricket*') ? 'active' : '' }}" style="--btn-accent: #0284c7;">
                        <span class="game-btn-icon" style="color: #38bdf8;"><i class="fa-solid fa-baseball-bat-ball"></i></span>
                        <span>Cricket Sports</span>
                        <span class="game-btn-tag" style="background: #0284c7; color: #fff;">LIVE</span>
                    </a>
                </div>
            </div>
        </div>
        @endif
    </header>

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div class="rupeerush-sidebar-backdrop" id="sidebarBackdrop" onclick="closeRupeeRushSidebar()"></div>

    <!-- RupeeRush Application Layout Shell -->
    <div class="rupeerush-app-shell" id="appShellWrapper">
        <!-- Modern Left Sidebar Component (All Games & All Options) -->
        @include('partials.left_sidebar')

        <!-- Main Content Column -->
        <div class="rupeerush-main-column" id="appMainColumn">
            <!-- Flash Notifications -->
            <div class="container-fluid px-3 px-md-4 px-xl-5 mt-2">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
                        <i class="fa-solid fa-circle-check fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
                        <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            <!-- Page Main Content -->
            <div class="rupeerush-page-body">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Site Footer (Sticky / Fixed at Bottom) -->
    <footer class="site-footer" id="siteFooter">
        <div class="container-fluid px-3 px-md-4 px-xl-5">
            <div class="footer-content-row">
                <div class="footer-brand-group d-flex align-items-center gap-2">
                    <div class="logo-bars" style="height: 18px;">
                        <div class="logo-bar b1" style="height: 10px;"></div>
                        <div class="logo-bar b2" style="height: 14px;"></div>
                        <div class="logo-bar b3" style="height: 18px;"></div>
                    </div>
                    <div>
                        <div class="footer-brand-title">RUPEE<span>RUSH</span></div>
                    </div>
                </div>

                <ul class="footer-links">
                    <li><a href="#privacy">Privacy Policy</a></li>
                    <li class="footer-divider">•</li>
                    <li><a href="#terms">Terms & Conditions</a></li>
                    <li class="footer-divider">•</li>
                    <li><a href="#disclaimer">Disclaimer</a></li>
                    <li class="footer-divider">•</li>
                    <li><a href="#contact">Contact Us</a></li>
                </ul>

                <div class="footer-tz">
                    — ALL TIMES IN IST
                </div>
            </div>
        </div>
    </footer>

    @php
        $siteSetting = \App\Models\AdminSetting::current();
        $rawWa = $siteSetting->support_whatsapp ?: '9026217223';
        $supportWa = preg_replace('/[^0-9]/', '', $rawWa);
        if (str_starts_with($supportWa, '0') && strlen($supportWa) === 11) {
            $supportWa = substr($supportWa, 1);
        }
        if (strlen($supportWa) === 10) {
            $supportWa = '91' . $supportWa;
        }
        $supportEmail = $siteSetting->support_email ?: 'rupeerushonline@gmail.com';
    @endphp

    <!-- Floating Support Widget (WhatsApp & Email) -->
    <div class="floating-support-container" id="floatingSupportWidget">
        <!-- Email Floating Button -->
        <a href="mailto:{{ $supportEmail }}?subject={{ rawurlencode('Support Request - ' . ($siteSetting->site_name ?? 'rupeerush')) }}" 
           class="floating-support-btn btn-floating-email" 
           title="Email: {{ $supportEmail }}" 
           target="_blank" 
           rel="noopener noreferrer"
           id="floatingEmailBtn">
            <span class="floating-support-label">{{ $supportEmail }}</span>
            <span class="floating-support-icon">
                <i class="fa-solid fa-envelope"></i>
            </span>
        </a>

        <!-- WhatsApp Floating Button -->
        <a href="https://wa.me/{{ $supportWa }}?text={{ rawurlencode('Hello ' . ($siteSetting->site_name ?? 'rupeerush') . ' Support, I have a query.') }}" 
           class="floating-support-btn btn-floating-whatsapp" 
           title="WhatsApp: {{ $rawWa }}" 
           target="_blank" 
           rel="noopener noreferrer"
           id="floatingWhatsAppBtn">
            <span class="floating-support-label">WhatsApp: {{ $rawWa }}</span>
            <span class="floating-support-icon">
                <i class="fa-brands fa-whatsapp"></i>
            </span>
            <span class="floating-support-pulse"></span>
        </a>
    </div>

    <!-- Mobile & Tablet App-like Sticky Bottom Nav Bar (Home, Games, My Bets, Refer, Wallet) -->
    <div class="mobile-bottom-nav d-lg-none" id="mobileBottomNav">
        <!-- 1. Home -->
        <a href="{{ route('home') }}" class="mobile-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>

        <!-- 2. Games (Popup Drawer Trigger) -->
        <button type="button" class="mobile-nav-item btn-mobile-games {{ request()->routeIs(['mines.*', 'aviator.*', 'wingo.*', 'lottery.*', 'vortex.*', 'andar-bahar.*', 'teen-patti.*', 'roulette.*']) ? 'active' : '' }}" data-bs-toggle="modal" data-bs-target="#mobileGamesModal" title="Browse All Games">
            <div class="games-center-pill">
                <i class="fa-solid fa-gamepad"></i>
            </div>
            <span>Games</span>
        </button>

        <!-- 3. My Bets -->
        @auth
            <a href="{{ route('bets.history') }}" class="mobile-nav-item {{ request()->routeIs('bets.history') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i>
                <span>My Bets</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="mobile-nav-item">
                <i class="fa-solid fa-receipt"></i>
                <span>My Bets</span>
            </a>
        @endauth

        <!-- 4. Refer -->
        @auth
            <a href="{{ route('referral.index') }}" class="mobile-nav-item {{ request()->routeIs('referral.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gift text-warning"></i>
                <span>Refer</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="mobile-nav-item">
                <i class="fa-solid fa-gift text-warning"></i>
                <span>Refer</span>
            </a>
        @endauth

        <!-- 5. Wallet -->
        @auth
            @if(Auth::user()->isDemo())
                <a href="javascript:void(0)" onclick="alert('Deposit & Withdraw are disabled in Demo Mode. Virtual demo credits are for game testing only.')" class="mobile-nav-item opacity-75">
                    <i class="fa-solid fa-wallet text-secondary"></i>
                    <span>Wallet</span>
                </a>
            @else
                <a href="{{ route('wallet.withdraw') }}" class="mobile-nav-item {{ request()->routeIs('wallet.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-wallet text-success"></i>
                    <span>Wallet</span>
                </a>
            @endif
        @else
            <a href="{{ route('login') }}" class="mobile-nav-item">
                <i class="fa-solid fa-wallet text-success"></i>
                <span>Wallet</span>
            </a>
        @endauth
    </div>

    <!-- Mobile Profile & Navigation Offcanvas Drawer (Opens on top-row 'Profile' click) -->
    <div class="offcanvas offcanvas-end mobile-profile-offcanvas" tabindex="-1" id="mobileProfileOffcanvas" aria-labelledby="mobileProfileOffcanvasLabel">
        <div class="offcanvas-header border-bottom border-secondary border-opacity-25 py-3 px-3">
            <div class="d-flex align-items-center gap-2">
                <div class="profile-drawer-icon">
                    <i class="fa-solid fa-user text-primary"></i>
                </div>
                <div>
                    <h5 class="offcanvas-title fw-bold text-white mb-0" id="mobileProfileOffcanvasLabel" style="font-family: 'Outfit', sans-serif;">
                        @auth My Account @else Welcome Player @endauth
                    </h5>
                    <div class="text-secondary small" style="font-size: 0.72rem;">
                        rupeerush • Quick Menu
                    </div>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body p-3">
            @auth
                <!-- User Profile & Wallet Card -->
                <div class="profile-offcanvas-usercard mb-3">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="user-big-avatar">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="fw-bold text-white fs-6 text-truncate">{{ Auth::user()->name }}</div>
                            <div class="text-secondary small font-monospace">{{ Auth::user()->phone }}</div>
                            @if(Auth::user()->isDemo())
                                <span class="badge bg-warning text-dark border border-warning rounded-pill px-2 py-0 mt-1 fw-bold" style="font-size: 0.68rem;">
                                    DEMO PLAYER
                                </span>
                            @else
                                <span class="badge bg-primary bg-opacity-25 text-info border border-info border-opacity-25 rounded-pill px-2 py-0 mt-1" style="font-size: 0.68rem;">
                                    VIP PLAYER
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Balance Box -->
                    <div class="p-2 px-3 rounded-3 mb-3" style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(56, 189, 248, 0.2);">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-secondary small">{{ Auth::user()->isDemo() ? 'Demo Chips' : 'Wallet Balance' }}</span>
                            <span class="font-monospace fw-bold text-success fs-5">₹{{ number_format(Auth::user()->balance, 2) }}</span>
                        </div>
                    </div>

                    <!-- Quick Action Buttons -->
                    <div class="row g-2">
                        <div class="col-6">
                            @if(Auth::user()->isDemo())
                                <button type="button" onclick="alert('Deposit is disabled in Demo Mode. You have ₹10,000 demo testing chips!')" class="btn w-100 btn-sm fw-bold text-white py-2 rounded-3 d-flex align-items-center justify-content-center gap-2 opacity-50" style="background: #334155; cursor: not-allowed;">
                                    <i class="fa-solid fa-ban"></i>
                                    <span>Deposit</span>
                                </button>
                            @else
                                <a href="{{ route('wallet.deposit') }}" class="btn w-100 btn-sm fw-bold text-white py-2 rounded-3 d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);">
                                    <i class="fa-solid fa-plus-circle"></i>
                                    <span>Deposit</span>
                                </a>
                            @endif
                        </div>
                        <div class="col-6">
                            @if(Auth::user()->isDemo())
                                <button type="button" onclick="alert('Withdraw is disabled in Demo Mode. Virtual demo credits are for testing only.')" class="btn w-100 btn-sm fw-bold text-white py-2 rounded-3 d-flex align-items-center justify-content-center gap-2 opacity-50" style="background: #334155; cursor: not-allowed;">
                                    <i class="fa-solid fa-ban"></i>
                                    <span>Withdraw</span>
                                </button>
                            @else
                                <a href="{{ route('wallet.withdraw') }}" class="btn w-100 btn-sm fw-bold text-white py-2 rounded-3 d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.35);">
                                    <i class="fa-solid fa-arrow-up-from-bracket"></i>
                                    <span>Withdraw</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Navigation Options List -->
                <div class="profile-menu-section mb-2">
                    <div class="text-uppercase text-secondary fw-bold px-2 mb-2" style="font-size: 0.68rem; letter-spacing: 0.5px;">Navigation Options</div>

                    <!-- 1. Home -->
                    <a href="{{ route('home') }}" class="profile-menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
                        <div class="d-flex align-items-center gap-3">
                            <div class="profile-menu-icon" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                                <i class="fa-solid fa-house"></i>
                            </div>
                            <div>
                                <div class="profile-menu-title">Home</div>
                                <div class="profile-menu-desc">Return to casino lobby</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-secondary small"></i>
                    </a>

                    <!-- 2. My Bets -->
                    <a href="{{ route('bets.history') }}" class="profile-menu-item {{ request()->routeIs('bets.history') ? 'active' : '' }}">
                        <div class="d-flex align-items-center gap-3">
                            <div class="profile-menu-icon" style="background: rgba(168, 85, 247, 0.15); color: #a855f7;">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div>
                                <div class="profile-menu-title">My Bets</div>
                                <div class="profile-menu-desc">Active & settled bet slips</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-secondary small"></i>
                    </a>

                    <!-- 3. Passbook -->
                    <a href="{{ route('wallet.transactions') }}" class="profile-menu-item {{ request()->routeIs('wallet.transactions') ? 'active' : '' }}">
                        <div class="d-flex align-items-center gap-3">
                            <div class="profile-menu-icon" style="background: rgba(6, 182, 212, 0.15); color: #06b6d4;">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <div>
                                <div class="profile-menu-title">Passbook</div>
                                <div class="profile-menu-desc">Credit, debit & win history</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-secondary small"></i>
                    </a>

                    @if(Auth::user()->isDemo())
                        <!-- 4. Wallet (Disabled for Demo) -->
                        <a href="javascript:void(0)" onclick="alert('Wallet Deposit & Withdraw are disabled in Demo Mode.')" class="profile-menu-item opacity-50">
                            <div class="d-flex align-items-center gap-3">
                                <div class="profile-menu-icon" style="background: rgba(100, 116, 139, 0.15); color: #94a3b8;">
                                    <i class="fa-solid fa-wallet"></i>
                                </div>
                                <div>
                                    <div class="profile-menu-title text-secondary">Wallet (Demo)</div>
                                    <div class="profile-menu-desc">Disabled for demo player</div>
                                </div>
                            </div>
                            <span class="badge bg-secondary" style="font-size: 0.65rem;">Disabled</span>
                        </a>
                    @else
                        <!-- 4. Wallet -->
                        <a href="{{ route('wallet.withdraw') }}" class="profile-menu-item {{ request()->routeIs('wallet.*') ? 'active' : '' }}">
                            <div class="d-flex align-items-center gap-3">
                                <div class="profile-menu-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                                    <i class="fa-solid fa-wallet"></i>
                                </div>
                                <div>
                                    <div class="profile-menu-title">Wallet</div>
                                    <div class="profile-menu-desc">Balance & account funds</div>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-secondary small"></i>
                        </a>

                        <!-- 5. Withdraw -->
                        <a href="{{ route('wallet.withdraw') }}" class="profile-menu-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="profile-menu-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                                    <i class="fa-solid fa-arrow-up-from-bracket"></i>
                                </div>
                                <div>
                                    <div class="profile-menu-title">Withdraw</div>
                                    <div class="profile-menu-desc">Instant bank & UPI payout</div>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-secondary small"></i>
                        </a>

                        <!-- 6. Deposit -->
                        <a href="{{ route('wallet.deposit') }}" class="profile-menu-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="profile-menu-icon" style="background: rgba(34, 197, 94, 0.15); color: #22c55e;">
                                    <i class="fa-solid fa-plus-circle"></i>
                                </div>
                                <div>
                                    <div class="profile-menu-title">Deposit</div>
                                    <div class="profile-menu-desc">Fast UPI QR deposit</div>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-secondary small"></i>
                        </a>
                    @endif

                    <!-- 7. Refer & Earn -->
                    <a href="{{ route('referral.index') }}" class="profile-menu-item {{ request()->routeIs('referral.index') ? 'active' : '' }}">
                        <div class="d-flex align-items-center gap-3">
                            <div class="profile-menu-icon" style="background: rgba(234, 179, 8, 0.15); color: #eab308;">
                                <i class="fa-solid fa-gift"></i>
                            </div>
                            <div>
                                <div class="profile-menu-title">Refer & Earn</div>
                                <div class="profile-menu-desc">Invite friends for bonus</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-secondary small"></i>
                    </a>

                    <!-- Admin Panel Link (if admin) -->
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="profile-menu-item" style="border-color: rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.08);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="profile-menu-icon" style="background: rgba(239, 68, 68, 0.2); color: #ef4444;">
                                    <i class="fa-solid fa-gauge-high"></i>
                                </div>
                                <div>
                                    <div class="profile-menu-title text-danger">Admin Panel</div>
                                    <div class="profile-menu-desc">Management dashboard</div>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-danger small"></i>
                        </a>
                    @endif
                </div>

                <!-- Logout Button -->
                <div class="pt-2 border-top border-secondary border-opacity-25 mt-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn w-100 py-2 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2" style="background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25);">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Logout Account</span>
                        </button>
                    </form>
                </div>
            @else
                <!-- Guest State -->
                <div class="profile-offcanvas-usercard mb-3 text-center py-4">
                    <div class="logo-bars mx-auto mb-2" style="height: 24px; justify-content: center;">
                        <div class="logo-bar b1" style="height: 14px;"></div>
                        <div class="logo-bar b2" style="height: 19px;"></div>
                        <div class="logo-bar b3" style="height: 24px;"></div>
                    </div>
                    <h5 class="fw-bold text-white mb-1" style="font-family: 'Outfit', sans-serif;">RUPEERUSH</h5>
                    <p class="text-secondary small mb-3">Join India's premier online gaming & casino platform</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn fw-bold text-white py-2 rounded-3" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Player Login
                        </a>
                        <a href="{{ route('register') }}" class="btn fw-bold text-white py-2 rounded-3" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                            <i class="fa-solid fa-user-plus me-1"></i> Self-Register (Min ₹100)
                        </a>
                        <a href="{{ \App\Models\AdminSetting::current()->getRegistrationWhatsappUrl() }}" class="btn fw-bold text-white py-2 rounded-3" style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); box-shadow: 0 4px 14px rgba(37, 211, 102, 0.4);" target="_blank" rel="noopener noreferrer">
                            <i class="fa-brands fa-whatsapp me-1"></i> Register on WhatsApp
                        </a>
                    </div>
                </div>

                <!-- Guest Navigation Options -->
                <div class="profile-menu-section mb-2">
                    <div class="text-uppercase text-secondary fw-bold px-2 mb-2" style="font-size: 0.68rem; letter-spacing: 0.5px;">Navigation Options</div>

                    <!-- 1. Home -->
                    <a href="{{ route('home') }}" class="profile-menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
                        <div class="d-flex align-items-center gap-3">
                            <div class="profile-menu-icon" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                                <i class="fa-solid fa-house"></i>
                            </div>
                            <div>
                                <div class="profile-menu-title">Home</div>
                                <div class="profile-menu-desc">Return to casino lobby</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-secondary small"></i>
                    </a>

                    <!-- 2. My Bets -->
                    <a href="{{ route('login') }}" class="profile-menu-item">
                        <div class="d-flex align-items-center gap-3">
                            <div class="profile-menu-icon" style="background: rgba(168, 85, 247, 0.15); color: #a855f7;">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div>
                                <div class="profile-menu-title">My Bets</div>
                                <div class="profile-menu-desc">Login to view bets</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-secondary small"></i>
                    </a>

                    <!-- 3. Passbook -->
                    <a href="{{ route('login') }}" class="profile-menu-item">
                        <div class="d-flex align-items-center gap-3">
                            <div class="profile-menu-icon" style="background: rgba(6, 182, 212, 0.15); color: #06b6d4;">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <div>
                                <div class="profile-menu-title">Passbook</div>
                                <div class="profile-menu-desc">Login to view history</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-secondary small"></i>
                    </a>

                    <!-- 4. Wallet -->
                    <a href="{{ route('login') }}" class="profile-menu-item">
                        <div class="d-flex align-items-center gap-3">
                            <div class="profile-menu-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                            <div>
                                <div class="profile-menu-title">Wallet</div>
                                <div class="profile-menu-desc">Login to manage wallet</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-secondary small"></i>
                    </a>

                    <!-- 5. Withdraw -->
                    <a href="{{ route('login') }}" class="profile-menu-item">
                        <div class="d-flex align-items-center gap-3">
                            <div class="profile-menu-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                                <i class="fa-solid fa-arrow-up-from-bracket"></i>
                            </div>
                            <div>
                                <div class="profile-menu-title">Withdraw</div>
                                <div class="profile-menu-desc">Login to request payout</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-secondary small"></i>
                    </a>

                    <!-- 6. Deposit -->
                    <a href="{{ route('login') }}" class="profile-menu-item">
                        <div class="d-flex align-items-center gap-3">
                            <div class="profile-menu-icon" style="background: rgba(34, 197, 94, 0.15); color: #22c55e;">
                                <i class="fa-solid fa-plus-circle"></i>
                            </div>
                            <div>
                                <div class="profile-menu-title">Deposit</div>
                                <div class="profile-menu-desc">Login to deposit balance</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-secondary small"></i>
                    </a>

                    <!-- 7. My Profile -->
                    <a href="{{ route('login') }}" class="profile-menu-item">
                        <div class="d-flex align-items-center gap-3">
                            <div class="profile-menu-icon" style="background: rgba(14, 165, 233, 0.15); color: #0ea5e9;">
                                <i class="fa-solid fa-user-circle"></i>
                            </div>
                            <div>
                                <div class="profile-menu-title">My Profile</div>
                                <div class="profile-menu-desc">Login to your account</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-secondary small"></i>
                    </a>
                </div>
            @endauth

            <!-- 24/7 Support Info -->
            <div class="mt-3 p-3 rounded-3 text-center" style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05);">
                <div class="small text-secondary mb-1">Need help? 24/7 Customer Care</div>
                <div class="d-flex justify-content-center gap-2">
                    <a href="https://wa.me/{{ $supportWa }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1" style="font-size: 0.76rem;">
                        <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp
                    </a>
                    <a href="mailto:{{ $supportEmail }}" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1" style="font-size: 0.76rem;">
                        <i class="fa-solid fa-envelope me-1"></i> Email
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile & Tablet Games Bottom Sheet Modal (Opens on 'Games' click) -->
    <div class="modal fade mobile-games-modal" id="mobileGamesModal" tabindex="-1" aria-labelledby="mobileGamesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 480px;">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header border-0 pb-1 pt-3 px-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="game-sheet-header-icon">
                            <i class="fa-solid fa-gamepad text-warning"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0" id="mobileGamesModalLabel" style="font-family: 'Outfit', sans-serif;">
                                Casino & Arcade Games
                            </h5>
                            <div class="text-secondary small" style="font-size: 0.72rem;">
                                Select any game to play instantly
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body (Grid of Game Cards - Alphabetical A to Z) -->
                <div class="modal-body p-3">
                    <div class="row g-2">
                        <!-- 1. Andar Bahar (A) -->
                        <div class="col-6">
                            <a href="{{ route('andar-bahar.index') }}" class="game-sheet-card card-andar {{ request()->routeIs('andar-bahar.*') ? 'is-active-game' : '' }}">
                                <div class="game-sheet-icon">
                                    <i class="fa-solid fa-diamond"></i>
                                </div>
                                <div class="game-sheet-info">
                                    <div class="game-sheet-title">Andar Bahar</div>
                                    <div class="game-sheet-tag text-warning">1.95x Payout</div>
                                </div>
                            </a>
                        </div>

                        <!-- 2. Aviator (A) -->
                        <div class="col-6">
                            <a href="{{ route('aviator.index') }}" class="game-sheet-card card-aviator {{ request()->routeIs('aviator.*') ? 'is-active-game' : '' }}">
                                <div class="game-sheet-icon">
                                    <i class="fa-solid fa-plane-departure"></i>
                                </div>
                                <div class="game-sheet-info">
                                    <div class="game-sheet-title">Aviator</div>
                                    <div class="game-sheet-tag text-danger">Crash Flight</div>
                                </div>
                                <span class="game-sheet-badge live">Live</span>
                            </a>
                        </div>

                        <!-- 3. Cricket Live Sports (C) -->
                        <div class="col-6">
                            <a href="{{ route('sports.cricket') }}" class="game-sheet-card card-cricket {{ request()->routeIs('sports.cricket*') ? 'is-active-game' : '' }}">
                                <div class="game-sheet-icon">
                                    <i class="fa-solid fa-baseball-bat-ball"></i>
                                </div>
                                <div class="game-sheet-info">
                                    <div class="game-sheet-title">Cricket Sports</div>
                                    <div class="game-sheet-tag text-info">Live Matches</div>
                                </div>
                                <span class="game-sheet-badge live">Live</span>
                            </a>
                        </div>

                        <!-- 4. Dragon Tiger (D) -->
                        <div class="col-6">
                            <a href="{{ route('dragon-tiger.index') }}" class="game-sheet-card card-dragontiger {{ request()->routeIs('dragon-tiger.*') ? 'is-active-game' : '' }}" style="background: linear-gradient(135deg, rgba(220, 38, 38, 0.15), rgba(37, 99, 235, 0.15)); border: 1px solid rgba(239, 68, 68, 0.35);">
                                <div class="game-sheet-icon" style="color: #f59e0b;">
                                    <i class="fa-solid fa-dragon"></i>
                                </div>
                                <div class="game-sheet-info">
                                    <div class="game-sheet-title">Dragon Tiger</div>
                                    <div class="game-sheet-tag text-warning">50x Suited Tie</div>
                                </div>
                                <span class="game-sheet-badge live">Live</span>
                            </a>
                        </div>

                        <!-- 5. Horse Racing (H) -->
                        <div class="col-6">
                            <a href="{{ route('horse-racing.index') }}" class="game-sheet-card card-horse-racing {{ request()->routeIs('horse-racing.*') ? 'is-active-game' : '' }}">
                                <div class="game-sheet-icon" style="color: #f59e0b;">
                                    <i class="fa-solid fa-horse-head"></i>
                                </div>
                                <div class="game-sheet-info">
                                    <div class="game-sheet-title">Horse Racing</div>
                                    <div class="game-sheet-tag text-success">Derby Live</div>
                                </div>
                                <span class="game-sheet-badge live">Live</span>
                            </a>
                        </div>

                        <!-- 6. 2-Digit Lottery (L) -->
                        <div class="col-6">
                            <a href="{{ route('lottery.index') }}" class="game-sheet-card card-lottery {{ request()->routeIs('lottery.*') ? 'is-active-game' : '' }}">
                                <div class="game-sheet-icon">
                                    <i class="fa-solid fa-dice"></i>
                                </div>
                                <div class="game-sheet-info">
                                    <div class="game-sheet-title">Lottery 2D</div>
                                    <div class="game-sheet-tag text-warning">90x Payout</div>
                                </div>
                            </a>
                        </div>

                        <!-- 7. Mines 5x5 (M) -->
                        <div class="col-6">
                            <a href="{{ route('mines.index') }}" class="game-sheet-card card-mines {{ request()->routeIs('mines.*') ? 'is-active-game' : '' }}">
                                <div class="game-sheet-icon">
                                    <i class="fa-solid fa-bomb"></i>
                                </div>
                                <div class="game-sheet-info">
                                    <div class="game-sheet-title">Mines 5x5</div>
                                    <div class="game-sheet-tag text-success">10,000x Win</div>
                                </div>
                                <span class="game-sheet-badge">Hot</span>
                            </a>
                        </div>

                        <!-- 8. Roulette (R) -->
                        <div class="col-6">
                            <a href="{{ route('roulette.index') }}" class="game-sheet-card card-roulette {{ request()->routeIs('roulette.*') ? 'is-active-game' : '' }}">
                                <div class="game-sheet-icon">
                                    <i class="fa-solid fa-dharmachakra"></i>
                                </div>
                                <div class="game-sheet-info">
                                    <div class="game-sheet-title">Roulette 36x</div>
                                    <div class="game-sheet-tag text-warning">36x Payout</div>
                                </div>
                                <span class="game-sheet-badge live">Live</span>
                            </a>
                        </div>

                        <!-- 9. Teen Patti (T) -->
                        <div class="col-6">
                            <a href="{{ route('teen-patti.index') }}" class="game-sheet-card card-teenpatti {{ request()->routeIs('teen-patti.*') ? 'is-active-game' : '' }}">
                                <div class="game-sheet-icon">
                                    <i class="fa-solid fa-heart"></i>
                                </div>
                                <div class="game-sheet-info">
                                    <div class="game-sheet-title">Teen Patti</div>
                                    <div class="game-sheet-tag text-success">40x Royal</div>
                                </div>
                                <span class="game-sheet-badge">Live</span>
                            </a>
                        </div>

                        <!-- 10. Vortex Wheel (V) -->
                        <div class="col-6">
                            <a href="{{ route('vortex.index') }}" class="game-sheet-card card-vortex {{ request()->routeIs('vortex.*') ? 'is-active-game' : '' }}">
                                <div class="game-sheet-icon">
                                    <i class="fa-solid fa-arrows-spin"></i>
                                </div>
                                <div class="game-sheet-info">
                                    <div class="game-sheet-title">Vortex Wheel</div>
                                    <div class="game-sheet-tag text-info">200x Win</div>
                                </div>
                            </a>
                        </div>

                        <!-- 11. WinGo Color (W) -->
                        <div class="col-6">
                            <a href="{{ route('wingo.index') }}" class="game-sheet-card card-wingo {{ request()->routeIs('wingo.*') ? 'is-active-game' : '' }}">
                                <div class="game-sheet-icon">
                                    <i class="fa-solid fa-dice-d6"></i>
                                </div>
                                <div class="game-sheet-info">
                                    <div class="game-sheet-title">WinGo Color</div>
                                    <div class="game-sheet-tag text-info">30s Rounds</div>
                                </div>
                                <span class="game-sheet-badge">Fast</span>
                            </a>
                        </div>
                        </div>
                    </div>

                    @auth
                        <!-- Quick Wallet Balance in Games Sheet -->
                        <div class="d-flex align-items-center justify-content-between p-2 mt-3 rounded-3" style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(255,255,255,0.08);">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-wallet text-info"></i>
                                <span class="text-secondary small">Balance:</span>
                                <span class="fw-bold text-success font-monospace">₹{{ number_format(Auth::user()->balance, 2) }}</span>
                            </div>
                            @if(Auth::user()->isDemo())
                                <button type="button" onclick="alert('Deposit is disabled in Demo Mode.')" class="btn btn-sm btn-secondary rounded-pill px-3 py-1 fw-bold opacity-50" style="font-size: 0.78rem; cursor: not-allowed;">
                                    <i class="fa-solid fa-ban me-1"></i> Deposit
                                </button>
                            @else
                                <a href="{{ route('wallet.deposit') }}" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold" style="font-size: 0.78rem;">
                                    <i class="fa-solid fa-plus-circle me-1"></i> Deposit
                                </a>
                            @endif
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleHeaderSearchOverlay() {
            const overlay = document.getElementById('lotusHeaderSearchOverlay');
            if (!overlay) return;
            if (overlay.style.display === 'none' || !overlay.style.display) {
                overlay.style.display = 'block';
                const input = document.getElementById('headerGameSearchInput');
                if (input) {
                    input.focus();
                }
            } else {
                overlay.style.display = 'none';
            }
            if (typeof updateHeaderAndFooterHeights === 'function') {
                updateHeaderAndFooterHeights();
            }
        }

        function handleHeaderSearchInput(val) {
            const gameSearch = document.getElementById('gameSearchInput');
            if (gameSearch) {
                gameSearch.value = val;
            }
            if (typeof filterGamesCatalog === 'function') {
                filterGamesCatalog();
            }
        }

        // ==========================================
        // RUPEERUSH LEFT SIDEBAR CONTROLLER
        // ==========================================
        function toggleRupeeRushSidebar() {
            const isDesktop = window.innerWidth >= 1200;
            if (isDesktop) {
                document.body.classList.toggle('sidebar-collapsed');
                const isCollapsed = document.body.classList.contains('sidebar-collapsed');
                localStorage.setItem('rupeerush_sidebar_desktop', isCollapsed ? 'collapsed' : 'expanded');
            } else {
                document.body.classList.toggle('sidebar-mobile-open');
            }
        }
        window.toggleRupeerushSidebar = toggleRupeeRushSidebar;

        function closeRupeeRushSidebar() {
            document.body.classList.remove('sidebar-mobile-open');
        }
        window.closeRupeerushSidebar = closeRupeeRushSidebar;

        function filterSidebarGames(query) {
            const q = (query || '').trim().toLowerCase();
            const items = document.querySelectorAll('.sidebar-game-entry');
            const clearBtn = document.getElementById('sidebarSearchClearBtn');
            const noMatch = document.getElementById('sidebarNoGamesFound');
            
            if (clearBtn) {
                if (q.length > 0) {
                    clearBtn.classList.remove('d-none');
                } else {
                    clearBtn.classList.add('d-none');
                }
            }

            let matchCount = 0;
            items.forEach(el => {
                const name = (el.getAttribute('data-name') || '').toLowerCase();
                const text = el.textContent.toLowerCase();
                if (!q || name.includes(q) || text.includes(q)) {
                    el.classList.remove('d-none');
                    matchCount++;
                } else {
                    el.classList.add('d-none');
                }
            });

            if (noMatch) {
                if (matchCount === 0 && q.length > 0) {
                    noMatch.classList.remove('d-none');
                } else {
                    noMatch.classList.add('d-none');
                }
            }
        }

        function clearSidebarSearch() {
            const input = document.getElementById('sidebarGameSearch');
            if (input) {
                input.value = '';
                input.focus();
                filterSidebarGames('');
            }
        }

        // Measure exact header and footer heights dynamically
        function updateHeaderAndFooterHeights() {
            const header = document.querySelector('.site-header');
            if (header) {
                document.documentElement.style.setProperty('--site-header-height', header.offsetHeight + 'px');
            }
            const footer = document.querySelector('.site-footer');
            if (footer && window.innerWidth >= 992) {
                document.documentElement.style.setProperty('--site-footer-height', footer.offsetHeight + 'px');
            } else {
                document.documentElement.style.setProperty('--site-footer-height', '0px');
            }
        }
        window.addEventListener('resize', updateHeaderAndFooterHeights);
        window.addEventListener('orientationchange', updateHeaderAndFooterHeights);

        // Initialize Left Sidebar state on DOM load
        document.addEventListener('DOMContentLoaded', function() {
            updateHeaderAndFooterHeights();
            const isGameActive = {{ $isGameActive ? 'true' : 'false' }};
            const isDesktop = window.innerWidth >= 1200;

            if (isGameActive) {
                // Keep collapsed by default in active game rooms to maximize betting canvas
                document.body.classList.add('sidebar-collapsed');
            } else if (isDesktop) {
                const savedPref = localStorage.getItem('rupeerush_sidebar_desktop');
                if (savedPref === 'collapsed') {
                    document.body.classList.add('sidebar-collapsed');
                } else {
                    // Default open on lobby
                    document.body.classList.remove('sidebar-collapsed');
                }
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
