<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - rupeerush Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --admin-dark: #09192f;
            --admin-sidebar: #0e2440;
            --admin-accent: #00b4d8;
            --admin-card-bg: #ffffff;
            --admin-body-bg: #f1f5f9;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--admin-body-bg);
            color: #1e293b;
            min-height: 100vh;
            margin: 0;
            display: flex;
        }
        .admin-sidebar {
            width: 260px;
            background: linear-gradient(180deg, #09192f 0%, #0e223d 100%);
            color: #94a3b8;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: sticky;
            top: 0;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }
        .sidebar-brand {
            padding: 22px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-decoration: none;
        }
        .sidebar-brand-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.25rem;
            color: #ffffff;
            line-height: 1.1;
        }
        .sidebar-brand-title span {
            color: #fbbf24;
        }
        .sidebar-nav {
            padding: 16px 12px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .nav-section-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #64748b;
            padding: 12px 14px 4px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            color: #cbd5e1;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #38bdf8;
        }
        .sidebar-link.active {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.4);
            border-left: 3px solid #fbbf24;
        }
        .sidebar-link i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }
        .sidebar-badge {
            margin-left: auto;
            background: #ef4444;
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 50px;
        }
        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }
        .admin-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .admin-topbar {
            background: #ffffff;
            padding: 14px 28px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .admin-content {
            padding: 28px;
            flex: 1;
        }
        .card-custom {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            margin-bottom: 24px;
        }
        .card-custom-header {
            padding: 18px 22px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .card-custom-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.15rem;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card-custom-body {
            padding: 22px;
        }
        .stat-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .stat-value {
            font-family: 'Outfit', sans-serif;
            font-size: 1.7rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
            margin-top: 4px;
        }
        .stat-label {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }

        /* Pagination Styling */
        .pagination {
            margin-bottom: 0;
            gap: 4px;
        }
        .pagination .page-item .page-link {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 6px 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .pagination .page-item.active .page-link {
            background-color: #0284c7;
            border-color: #0284c7;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.35);
        }
        .pagination .page-item .page-link:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
            color: #0284c7;
        }
        .pagination .page-item.disabled .page-link {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            color: #94a3b8;
        }
        .pagination svg,
        nav[aria-label="Pagination Navigation"] svg,
        nav svg,
        svg.w-5,
        svg.h-5,
        div[role="navigation"] svg {
            width: 1.25rem !important;
            height: 1.25rem !important;
            max-width: 1.25rem !important;
            max-height: 1.25rem !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <i class="fa-solid fa-gem text-warning fs-3"></i>
            <div>
                <div class="sidebar-brand-title">RUPEERUSH <span>ADMIN</span></div>
                <div style="font-size: 0.65rem; color: #fbbf24; font-weight: 700; letter-spacing: 1px;">RUPEERUSH CONTROL HUB</div>
            </div>
        </a>

        <div class="sidebar-nav">
            @if(Auth::user()->hasPermission('dashboard') || Auth::user()->hasPermission('users_view'))
                <div class="nav-section-title">Overview</div>
                @if(Auth::user()->hasPermission('dashboard'))
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.live-results.index') }}" class="sidebar-link {{ request()->routeIs('admin.live-results.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-satellite-dish text-danger"></i>
                        <span>Live Round Results</span>
                        <span class="badge bg-danger rounded-pill ms-auto" style="font-size: 0.65rem; letter-spacing: 0.5px;">LIVE</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('users_view'))
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i>
                        <span>Users</span>
                    </a>
                    <a href="{{ route('admin.ledger.index') }}" class="sidebar-link {{ request()->routeIs('admin.ledger.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-invoice-dollar text-success"></i>
                        <span>Player Ledger</span>
                    </a>
                @endif
            @endif

            @if(Auth::user()->hasPermission('roles_management'))
                <div class="nav-section-title">Access Control</div>
                <a href="{{ route('admin.roles.index') }}" class="sidebar-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-shield text-warning"></i>
                    <span>Roles & Staff</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('deposits_view') || Auth::user()->hasPermission('withdrawals_view') || Auth::user()->hasPermission('daily_transactions') || Auth::user()->hasPermission('payment_settings'))
                <div class="nav-section-title">Payment and Settings</div>
                @if(Auth::user()->hasPermission('deposits_view'))
                    <a href="{{ route('admin.payments.deposits') }}" class="sidebar-link {{ request()->routeIs('admin.payments.deposits') ? 'active' : '' }}">
                        <i class="fa-solid fa-money-bill-transfer"></i>
                        <span>Deposit Request</span>
                        @php
                            $pendingCount = \App\Models\Transaction::where('type', 'deposit')->where('status', 'pending')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="sidebar-badge">{{ $pendingCount }}</span>
                        @endif
                    </a>
                @endif
                @if(Auth::user()->hasPermission('withdrawals_view'))
                    <a href="{{ route('admin.payments.withdrawals') }}" class="sidebar-link {{ request()->routeIs('admin.payments.withdrawals') ? 'active' : '' }}">
                        <i class="fa-solid fa-arrow-up-from-bracket"></i>
                        <span>Withdraw request</span>
                        @php
                            $pendingWithdrawalsCount = \App\Models\Transaction::where('type', 'withdrawal')->where('status', 'pending')->count();
                        @endphp
                        @if($pendingWithdrawalsCount > 0)
                            <span class="sidebar-badge" style="background: #f59e0b; color: #000;">{{ $pendingWithdrawalsCount }}</span>
                        @endif
                    </a>
                @endif
                @if(Auth::user()->hasPermission('daily_transactions'))
                    <a href="{{ route('admin.payments.daily') }}" class="sidebar-link {{ request()->routeIs('admin.payments.daily') ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-days text-info"></i>
                        <span>Daily transactions</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('payment_settings'))
                    <a href="{{ route('admin.payments.settings') }}" class="sidebar-link {{ request()->routeIs('admin.payments.settings') ? 'active' : '' }}">
                        <i class="fa-solid fa-sliders text-primary"></i>
                        <span>QR,limits and commission</span>
                    </a>
                    <a href="{{ route('admin.lobby.index') }}" class="sidebar-link {{ request()->routeIs('admin.lobby.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-photo-film text-warning"></i>
                        <span>Lobby Banners & Cards</span>
                    </a>
                @endif
            @endif

            @if(Auth::user()->hasPermission('bets_view'))
                <div class="nav-section-title">Bets</div>
                <a href="{{ route('admin.bets.index') }}" class="sidebar-link {{ request()->routeIs('admin.bets.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-receipt text-warning"></i>
                    <span>All bets</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('games_toggle') || Auth::user()->hasPermission('game_lottery') || Auth::user()->hasPermission('game_wingo') || Auth::user()->hasPermission('game_andar_bahar') || Auth::user()->hasPermission('game_aviator') || Auth::user()->hasPermission('game_mines') || Auth::user()->hasPermission('game_vortex') || Auth::user()->hasPermission('game_teen_patti') || Auth::user()->hasPermission('game_roulette') || Auth::user()->hasPermission('game_horse_racing'))
                <div class="nav-section-title">Game Controls</div>
                @if(Auth::user()->hasPermission('games_toggle'))
                    <a href="{{ route('admin.games.status') }}" class="sidebar-link {{ request()->routeIs('admin.games.status*') ? 'active' : '' }}">
                        <i class="fa-solid fa-power-off text-warning"></i>
                        <span>Game Active / Inactive</span>
                        <span class="badge bg-warning text-dark px-1.5 py-0 ms-auto font-monospace" style="font-size: 0.65rem;">ON/OFF</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('game_lottery'))
                    <a href="{{ route('admin.results.index') }}" class="sidebar-link {{ request()->routeIs('admin.results.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-sliders text-primary"></i>
                        <span>2digit slot</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('game_wingo'))
                    <a href="{{ route('admin.wingo.index') }}" class="sidebar-link {{ request()->routeIs('admin.wingo.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-gamepad text-info"></i>
                        <span>wingo color</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('game_andar_bahar'))
                    <a href="{{ route('admin.andar-bahar.index') }}" class="sidebar-link {{ request()->routeIs('admin.andar-bahar.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-layer-group text-warning"></i>
                        <span>andar bahar</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('game_aviator'))
                    <a href="{{ route('admin.aviator.index') }}" class="sidebar-link {{ request()->routeIs('admin.aviator.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-plane-departure text-danger"></i>
                        <span>aviator</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('game_mines'))
                    <a href="{{ route('admin.mines.index') }}" class="sidebar-link {{ request()->routeIs('admin.mines.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-bomb text-success"></i>
                        <span>mines 5x5</span>
                        @php
                            $activeMinesCount = \App\Models\MinesGame::where('status', 'active')->count();
                        @endphp
                        @if($activeMinesCount > 0)
                            <span class="sidebar-badge" style="background: #10b981; color: #fff;">{{ $activeMinesCount }}</span>
                        @endif
                    </a>
                @endif
                @if(Auth::user()->hasPermission('game_vortex'))
                    <a href="{{ route('admin.vortex.index') }}" class="sidebar-link {{ request()->routeIs('admin.vortex.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-arrows-spin text-info"></i>
                        <span>vortex wheel</span>
                        @php
                            $activeVortexCount = \App\Models\VortexGame::where('status', 'active')->count();
                        @endphp
                        @if($activeVortexCount > 0)
                            <span class="sidebar-badge" style="background: #06b6d4; color: #fff;">{{ $activeVortexCount }}</span>
                        @endif
                    </a>
                @endif
                @if(Auth::user()->hasPermission('game_teen_patti'))
                    <a href="{{ route('admin.teen-patti.index') }}" class="sidebar-link {{ request()->routeIs('admin.teen-patti.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-heart text-danger"></i>
                        <span>teen patti</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('game_dragon_tiger'))
                    <a href="{{ route('admin.dragon-tiger.index') }}" class="sidebar-link {{ request()->routeIs('admin.dragon-tiger.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-dragon text-danger"></i>
                        <span>dragon tiger</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('game_roulette'))
                    <a href="{{ route('admin.roulette.index') }}" class="sidebar-link {{ request()->routeIs('admin.roulette.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-dharmachakra text-warning"></i>
                        <span>roulette 36x</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('game_horse_racing'))
                    <a href="{{ route('admin.horse-racing.index') }}" class="sidebar-link {{ request()->routeIs('admin.horse-racing.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-horse-head text-warning"></i>
                        <span>horse racing</span>
                    </a>
                @endif
            @endif

            @if(Auth::user()->hasPermission('winners_lottery') || Auth::user()->hasPermission('winners_wingo') || Auth::user()->hasPermission('winners_andar_bahar') || Auth::user()->hasPermission('winners_aviator') || Auth::user()->hasPermission('winners_mines') || Auth::user()->hasPermission('winners_vortex') || Auth::user()->hasPermission('winners_teen_patti') || Auth::user()->hasPermission('winners_dragon_tiger') || Auth::user()->hasPermission('winners_horse_racing'))
                <div class="nav-section-title">Winner Lists</div>
                @if(Auth::user()->hasPermission('winners_lottery'))
                    <a href="{{ route('admin.winners.index') }}" class="sidebar-link {{ request()->routeIs('admin.winners.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-trophy text-warning"></i>
                        <span>2 digit slot</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('winners_wingo'))
                    <a href="{{ route('admin.wingo.winners') }}" class="sidebar-link {{ request()->routeIs('admin.wingo.winners') ? 'active' : '' }}">
                        <i class="fa-solid fa-award text-success"></i>
                        <span>wingo color</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('winners_andar_bahar'))
                    <a href="{{ route('admin.andar-bahar.winners') }}" class="sidebar-link {{ request()->routeIs('admin.andar-bahar.winners') ? 'active' : '' }}">
                        <i class="fa-solid fa-trophy text-warning"></i>
                        <span>andar bahar</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('winners_aviator'))
                    <a href="{{ route('admin.aviator.winners') }}" class="sidebar-link {{ request()->routeIs('admin.aviator.winners') ? 'active' : '' }}">
                        <i class="fa-solid fa-trophy text-danger"></i>
                        <span>aviator</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('winners_mines'))
                    <a href="{{ route('admin.mines.winners') }}" class="sidebar-link {{ request()->routeIs('admin.mines.winners') ? 'active' : '' }}">
                        <i class="fa-solid fa-trophy text-success"></i>
                        <span>mines 5x5</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('winners_vortex'))
                    <a href="{{ route('admin.vortex.winners') }}" class="sidebar-link {{ request()->routeIs('admin.vortex.winners') ? 'active' : '' }}">
                        <i class="fa-solid fa-trophy text-info"></i>
                        <span>vortex wheel</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('winners_teen_patti'))
                    <a href="{{ route('admin.teen-patti.winners') }}" class="sidebar-link {{ request()->routeIs('admin.teen-patti.winners') ? 'active' : '' }}">
                        <i class="fa-solid fa-trophy text-danger"></i>
                        <span>teen patti</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('winners_dragon_tiger'))
                    <a href="{{ route('admin.dragon-tiger.winners') }}" class="sidebar-link {{ request()->routeIs('admin.dragon-tiger.winners') ? 'active' : '' }}">
                        <i class="fa-solid fa-trophy text-danger"></i>
                        <span>dragon tiger</span>
                    </a>
                @endif
                @if(Auth::user()->hasPermission('winners_horse_racing'))
                    <a href="{{ route('admin.horse-racing.winners') }}" class="sidebar-link {{ request()->routeIs('admin.horse-racing.winners') ? 'active' : '' }}">
                        <i class="fa-solid fa-trophy text-warning"></i>
                        <span>horse racing</span>
                    </a>
                @endif
            @endif

            <div class="nav-section-title">Support and System</div>
            @if(Auth::user()->hasPermission('support_settings'))
                <a href="{{ route('admin.support.index') }}" class="sidebar-link {{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-headset text-success"></i>
                    <span>Support (WhatsApp & Email)</span>
                </a>
            @endif
            <a href="{{ route('home') }}" target="_blank" class="sidebar-link">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>View Live Game</span>
            </a>
        </div>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 rounded-pill fw-bold btn-sm py-2">
                    <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
        <!-- Top Bar -->
        <header class="admin-topbar">
            <div>
                <h5 class="mb-0 fw-bold text-dark">@yield('page_title', 'Dashboard')</h5>
                <small class="text-muted">Welcome, {{ Auth::user()->name }} ({{ Auth::user()->adminRole ? Auth::user()->adminRole->name : 'Super Administrator' }})</small>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-bold">
                    <i class="fa-regular fa-clock me-1"></i> {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d M Y, h:i A') }} IST
                </span>
                <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" target="_blank">
                    <i class="fa-solid fa-gamepad me-1"></i> Live Portal
                </a>
            </div>
        </header>

        <!-- Page Content -->
        <main class="admin-content">
            <!-- Flash Notifications -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="fa-solid fa-circle-check fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2 mb-2 fw-bold">
                        <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                        <span>Validation Errors:</span>
                    </div>
                    <ul class="mb-0 ps-4 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
