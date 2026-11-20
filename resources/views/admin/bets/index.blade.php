@extends('layouts.admin')

@section('title', 'All Game Bets Monitor - rupeerush Management')

@section('content')
<style>
    /* Ultra-Compact High-Density Bets Monitor Styles */
    .bets-compact-stat {
        background: #ffffff;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        height: 100%;
    }
    .bets-compact-stat .stat-label {
        font-size: 0.67rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #64748b;
        margin-bottom: 2px;
    }
    .bets-compact-stat .stat-value {
        font-family: 'Outfit', sans-serif;
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.1;
    }
    .bets-compact-stat .stat-icon-sm {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .scroll-tabs-bar {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        gap: 6px;
        padding-bottom: 4px;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }
    .scroll-tabs-bar::-webkit-scrollbar {
        height: 4px;
    }
    .scroll-tabs-bar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .game-tab-pill {
        white-space: nowrap;
        font-size: 0.76rem !important;
        padding: 4px 10px !important;
        font-weight: 700 !important;
        border-radius: 50rem !important;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.15s ease-in-out;
    }
    .game-tab-pill .badge {
        font-size: 0.66rem;
        padding: 2px 6px;
        margin-left: 5px;
    }
    .spotlight-micro-card {
        border-radius: 10px;
        padding: 8px 12px;
        color: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 70px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .spotlight-micro-card .badge-title {
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.4px;
        padding: 2px 6px;
        border-radius: 50rem;
    }
    .spotlight-micro-card .amount-val {
        font-size: 1.1rem;
        font-weight: 800;
        font-family: monospace;
        line-height: 1.1;
    }
    .spotlight-micro-card .user-val {
        font-size: 0.74rem;
        font-weight: 600;
    }
    .table-compact-bets {
        font-size: 0.82rem;
    }
    .table-compact-bets thead th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 8px 10px !important;
        background: #f8fafc;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-compact-bets tbody td {
        padding: 7px 10px !important;
        vertical-align: middle;
    }
</style>

<!-- Top Title & Navigation Bar (Ultra-Compact) -->
<div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
            <i class="fa-solid fa-receipt text-primary me-2"></i> All Game Bets Monitor
        </h5>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
            11 Platform Games Connected
        </span>
    </div>
    <!-- Quick Controls Direct Jump Links -->
    <div class="d-flex gap-1 flex-nowrap overflow-x-auto py-0.5" style="max-width: 100%;">
        <a href="{{ route('admin.dragon-tiger.index') }}" class="btn btn-outline-danger btn-sm py-0 px-2 rounded-pill fw-semibold text-nowrap" style="font-size: 0.72rem;">
            <i class="fa-solid fa-dragon me-1"></i>DT
        </a>
        <a href="{{ route('admin.teen-patti.index') }}" class="btn btn-outline-warning text-dark btn-sm py-0 px-2 rounded-pill fw-semibold text-nowrap" style="font-size: 0.72rem;">
            <i class="fa-solid fa-crown me-1 text-danger"></i>TP
        </a>
        <a href="{{ route('admin.vortex.index') }}" class="btn btn-outline-info text-dark btn-sm py-0 px-2 rounded-pill fw-semibold text-nowrap" style="font-size: 0.72rem;">
            <i class="fa-solid fa-compact-disc me-1"></i>Vortex
        </a>
        <a href="{{ route('admin.horse-racing.index') }}" class="btn btn-outline-success btn-sm py-0 px-2 rounded-pill fw-semibold text-nowrap" style="font-size: 0.72rem;">
            <i class="fa-solid fa-horse-head me-1"></i>Derby
        </a>
        <a href="{{ route('admin.roulette.index') }}" class="btn btn-outline-warning text-dark btn-sm py-0 px-2 rounded-pill fw-semibold text-nowrap" style="font-size: 0.72rem;">
            <i class="fa-solid fa-dharmachakra me-1 text-danger"></i>Roulette
        </a>
        <a href="{{ route('admin.aviator.index') }}" class="btn btn-outline-danger btn-sm py-0 px-2 rounded-pill fw-semibold text-nowrap" style="font-size: 0.72rem;">
            <i class="fa-solid fa-plane-departure me-1"></i>Aviator
        </a>
        <a href="{{ route('admin.andar-bahar.index') }}" class="btn btn-outline-warning text-dark btn-sm py-0 px-2 rounded-pill fw-semibold text-nowrap" style="font-size: 0.72rem;">
            <i class="fa-solid fa-layer-group me-1"></i>AB
        </a>
        <a href="{{ route('admin.wingo.index') }}" class="btn btn-outline-danger btn-sm py-0 px-2 rounded-pill fw-semibold text-nowrap" style="font-size: 0.72rem;">
            <i class="fa-solid fa-gamepad me-1"></i>WinGo
        </a>
        <a href="{{ route('admin.results.index') }}" class="btn btn-outline-primary btn-sm py-0 px-2 rounded-pill fw-semibold text-nowrap" style="font-size: 0.72rem;">
            <i class="fa-solid fa-sliders me-1"></i>Lottery
        </a>
    </div>
</div>

<!-- Financial & Volume Stat Cards (Ultra-Compact Row 1) -->
<div class="row g-2 mb-2">
    <div class="col-6 col-xl-3">
        <div class="bets-compact-stat">
            <div>
                <div class="stat-label">Total Bets Placed</div>
                <div class="stat-value text-dark">{{ number_format($stats['total_bets']) }}</div>
                <div class="text-muted" style="font-size: 0.65rem; line-height: 1.15;">
                    DT: <strong>{{ $stats['dragon_tiger_count'] ?? 0 }}</strong> | TP: <strong>{{ $stats['teen_patti_count'] ?? 0 }}</strong> | Vor: <strong>{{ $stats['vortex_count'] ?? 0 }}</strong> | Rou: <strong>{{ $stats['roulette_count'] ?? 0 }}</strong>
                </div>
            </div>
            <div class="stat-icon-sm" style="background: rgba(14, 165, 233, 0.15); color: #0284c7;">
                <i class="fa-solid fa-dice"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="bets-compact-stat">
            <div>
                <div class="stat-label">Total Wagered Pool</div>
                <div class="stat-value text-primary">₹{{ number_format($stats['total_volume'], 2) }}</div>
                <div class="text-muted" style="font-size: 0.65rem; line-height: 1.15;">
                    Av: ₹{{ number_format($stats['aviator_volume'] ?? 0, 0) }} | Rou: ₹{{ number_format($stats['roulette_volume'] ?? 0, 0) }} | Win: ₹{{ number_format($stats['wingo_volume'] ?? 0, 0) }}
                </div>
            </div>
            <div class="stat-icon-sm" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="bets-compact-stat">
            <div>
                <div class="stat-label">Total Payouts Won</div>
                <div class="stat-value text-danger">₹{{ number_format($stats['total_payout'], 2) }}</div>
                <div class="text-muted" style="font-size: 0.65rem; line-height: 1.15;">
                    Credited directly to player wallets
                </div>
            </div>
            <div class="stat-icon-sm" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="bets-compact-stat">
            <div>
                <div class="stat-label">Net House Profit</div>
                <div class="stat-value {{ $stats['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                    ₹{{ number_format($stats['net_profit'], 2) }}
                </div>
                <div class="text-muted" style="font-size: 0.65rem; line-height: 1.15;">
                    Margin: {{ $stats['total_volume'] > 0 ? number_format(($stats['net_profit'] / $stats['total_volume']) * 100, 1) : 0 }}%
                    @if(!empty($stats['total_commission']))
                        • Fee: ₹{{ number_format($stats['total_commission'], 0) }}
                    @endif
                </div>
            </div>
            <div class="stat-icon-sm" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
        </div>
    </div>
</div>

<!-- Spotlight: Top Bettors Highlights (Ultra-Compact Row 2) -->
<div class="row g-2 mb-2">
    <!-- 1. Highest Bet Amount -->
    <div class="col-md-4">
        <div class="spotlight-micro-card" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="badge bg-warning text-dark fw-bold badge-title">
                    <i class="fa-solid fa-fire me-1"></i> TOP WAGER TODAY
                </span>
                <a href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['filter' => 'higher_amount'])) }}" class="filter-preserve-scroll text-warning text-decoration-none fw-bold" style="font-size: 0.7rem;">
                    View High Rollers &rarr;
                </a>
            </div>
            @if(!empty($spotlight['topBet']))
                <div class="d-flex align-items-baseline justify-content-between flex-wrap gap-1">
                    <div class="amount-val text-warning">₹{{ number_format($spotlight['topBet']['amount'], 2) }}</div>
                    <div class="user-val text-white text-truncate" style="max-width: 65%;">
                        {{ $spotlight['topBet']['user']->name ?? 'Player' }} 
                        <span class="text-white-50 small font-monospace">({{ $spotlight['topBet']['game'] }})</span>
                    </div>
                </div>
            @else
                <div class="text-white-50" style="font-size: 0.72rem;">No bets recorded for this date yet.</div>
            @endif
        </div>
    </div>

    <!-- 2. Higher Color User -->
    <div class="col-md-4">
        <div class="spotlight-micro-card" style="background: linear-gradient(135deg, #4a044e 0%, #701a75 100%);">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="badge bg-danger text-white fw-bold badge-title">
                    <i class="fa-solid fa-palette me-1"></i> COLOR LEADER
                </span>
                <a href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['filter' => 'higher_color_user'])) }}" class="filter-preserve-scroll text-light text-decoration-none fw-bold" style="font-size: 0.7rem;">
                    View Color Bets &rarr;
                </a>
            </div>
            @if(!empty($spotlight['topColorUser']))
                <div class="d-flex align-items-baseline justify-content-between flex-wrap gap-1">
                    <div class="amount-val text-light">₹{{ number_format($spotlight['topColorUser']['amount'], 2) }}</div>
                    <div class="user-val text-white text-truncate" style="max-width: 65%;">
                        {{ $spotlight['topColorUser']['user']->name ?? 'Color Bettor' }}
                        <span class="badge bg-light text-dark fw-bold ms-1" style="font-size: 0.65rem;">{{ $spotlight['topColorUser']['color'] }}</span>
                    </div>
                </div>
            @else
                <div class="text-white-50" style="font-size: 0.72rem;">No color bets for this period yet.</div>
            @endif
        </div>
    </div>

    <!-- 3. Higher Number User -->
    <div class="col-md-4">
        <div class="spotlight-micro-card" style="background: linear-gradient(135deg, #0c4a6e 0%, #0369a1 100%);">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="badge bg-info text-dark fw-bold badge-title">
                    <i class="fa-solid fa-hashtag me-1"></i> NUMBER LEADER
                </span>
                <a href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['filter' => 'higher_no_user'])) }}" class="filter-preserve-scroll text-info text-decoration-none fw-bold" style="font-size: 0.7rem;">
                    View Number Bets &rarr;
                </a>
            </div>
            @if(!empty($spotlight['topNumberUser']))
                <div class="d-flex align-items-baseline justify-content-between flex-wrap gap-1">
                    <div class="amount-val text-white">₹{{ number_format($spotlight['topNumberUser']['amount'], 2) }}</div>
                    <div class="user-val text-white text-truncate" style="max-width: 65%;">
                        {{ $spotlight['topNumberUser']['user']->name ?? 'Number Bettor' }}
                        <span class="badge bg-warning text-dark fw-bold ms-1" style="font-size: 0.65rem;">{{ $spotlight['topNumberUser']['number'] }}</span>
                    </div>
                </div>
            @else
                <div class="text-white-50" style="font-size: 0.72rem;">No number bets placed for this date yet.</div>
            @endif
        </div>
    </div>
</div>

<!-- Filters & Game Tabs Workspace (Row 3 - Ultra Compact) -->
<div class="card border-0 shadow-sm rounded-3 mb-2" id="bets-monitor-workspace">
    <div class="card-body p-2 px-3">
        <!-- Game Type Quick Navigation Tabs (Horizontal Scrollable Strip) -->
        <div class="scroll-tabs-bar mb-2 border-bottom pb-2">
            <a class="filter-preserve-scroll game-tab-pill {{ $game === 'all' ? 'active bg-primary text-white shadow-sm' : 'bg-light text-dark' }}" 
               href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'all'])) }}">
               <i class="fa-solid fa-layer-group me-1"></i> All Games
               <span class="badge {{ $game === 'all' ? 'bg-white text-primary' : 'bg-primary text-white' }} rounded-pill">{{ $stats['total_bets'] }}</span>
            </a>
            <a class="filter-preserve-scroll game-tab-pill {{ $game === 'dragon_tiger' ? 'active bg-danger text-white shadow-sm' : 'bg-light text-dark' }}" 
               href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'dragon_tiger'])) }}">
               <i class="fa-solid fa-dragon me-1"></i> Dragon Tiger
               <span class="badge bg-white text-dark rounded-pill">{{ $stats['dragon_tiger_count'] ?? 0 }}</span>
            </a>
            <a class="filter-preserve-scroll game-tab-pill {{ $game === 'teen_patti' ? 'active bg-warning text-dark shadow-sm' : 'bg-light text-dark' }}" 
               href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'teen_patti'])) }}">
               <i class="fa-solid fa-crown me-1 text-danger"></i> Teen Patti
               <span class="badge bg-white text-dark rounded-pill">{{ $stats['teen_patti_count'] ?? 0 }}</span>
            </a>
            <a class="filter-preserve-scroll game-tab-pill {{ $game === 'vortex' ? 'active bg-info text-dark shadow-sm' : 'bg-light text-dark' }}" 
               href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'vortex'])) }}">
               <i class="fa-solid fa-compact-disc me-1"></i> Vortex Wheel
               <span class="badge bg-white text-dark rounded-pill">{{ $stats['vortex_count'] ?? 0 }}</span>
            </a>
            <a class="filter-preserve-scroll game-tab-pill {{ $game === 'sports' ? 'active bg-primary text-white shadow-sm' : 'bg-light text-dark' }}" 
               href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'sports'])) }}">
               <i class="fa-solid fa-baseball-bat-ball me-1"></i> Cricket
               <span class="badge bg-white text-dark rounded-pill">{{ $stats['sports_count'] ?? 0 }}</span>
            </a>
            <a class="filter-preserve-scroll game-tab-pill {{ $game === 'horse_racing' ? 'active bg-success text-white shadow-sm' : 'bg-light text-dark' }}" 
               href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'horse_racing'])) }}">
               <i class="fa-solid fa-horse-head me-1"></i> Horse Racing
               <span class="badge bg-white text-dark rounded-pill">{{ $stats['horse_racing_count'] ?? 0 }}</span>
            </a>
            <a class="filter-preserve-scroll game-tab-pill {{ $game === 'roulette' ? 'active bg-warning text-dark shadow-sm' : 'bg-light text-dark' }}" 
               href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'roulette'])) }}">
               <i class="fa-solid fa-dharmachakra me-1 text-danger"></i> Roulette 36x
               <span class="badge bg-white text-dark rounded-pill">{{ $stats['roulette_count'] ?? 0 }}</span>
            </a>
            <a class="filter-preserve-scroll game-tab-pill {{ $game === 'aviator' ? 'active bg-danger text-white shadow-sm' : 'bg-light text-dark' }}" 
               href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'aviator'])) }}">
               <i class="fa-solid fa-plane-departure me-1"></i> Aviator
               <span class="badge bg-white text-dark rounded-pill">{{ $stats['aviator_count'] ?? 0 }}</span>
            </a>
            <a class="filter-preserve-scroll game-tab-pill {{ $game === 'mines' ? 'active bg-success text-white shadow-sm' : 'bg-light text-dark' }}" 
               href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'mines'])) }}">
               <i class="fa-solid fa-bomb me-1"></i> Mines 5x5
               <span class="badge bg-white text-dark rounded-pill">{{ $stats['mines_count'] ?? 0 }}</span>
            </a>
            <a class="filter-preserve-scroll game-tab-pill {{ $game === 'andar_bahar' ? 'active bg-warning text-dark shadow-sm' : 'bg-light text-dark' }}" 
               href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'andar_bahar'])) }}">
               <i class="fa-solid fa-cards-blank me-1"></i> Andar Bahar
               <span class="badge bg-white text-dark rounded-pill">{{ $stats['andar_count'] ?? 0 }}</span>
            </a>
            <a class="filter-preserve-scroll game-tab-pill {{ $game === 'lottery' ? 'active bg-info text-white shadow-sm' : 'bg-light text-dark' }}" 
               href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'lottery'])) }}">
               <i class="fa-solid fa-ticket me-1"></i> 2-Digit Lottery
               <span class="badge bg-white text-dark rounded-pill">{{ $stats['lottery_count'] ?? 0 }}</span>
            </a>
            <a class="filter-preserve-scroll game-tab-pill {{ $game === 'wingo' ? 'active bg-danger text-white shadow-sm' : 'bg-light text-dark' }}" 
               href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'wingo'])) }}">
               <i class="fa-solid fa-palette me-1"></i> WinGo All
               <span class="badge bg-white text-dark rounded-pill">{{ $stats['wingo_count'] ?? 0 }}</span>
            </a>
            <a class="filter-preserve-scroll game-tab-pill {{ $game === 'wingo_30s' ? 'active bg-dark text-white shadow-sm' : 'bg-light text-dark' }}" 
               href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'wingo_30s'])) }}">
               WinGo 30s
            </a>
            <a class="filter-preserve-scroll game-tab-pill {{ $game === 'wingo_1m' ? 'active bg-dark text-white shadow-sm' : 'bg-light text-dark' }}" 
               href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'wingo_1m'])) }}">
               WinGo 1M
            </a>
            <a class="filter-preserve-scroll game-tab-pill {{ $game === 'wingo_3m' ? 'active bg-dark text-white shadow-sm' : 'bg-light text-dark' }}" 
               href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'wingo_3m'])) }}">
               WinGo 3M
            </a>
            <a class="filter-preserve-scroll game-tab-pill {{ $game === 'wingo_5m' ? 'active bg-dark text-white shadow-sm' : 'bg-light text-dark' }}" 
               href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'wingo_5m'])) }}">
               WinGo 5M
            </a>
        </div>

        <!-- Filter Presets & Form in a Compact Row -->
        <div class="row g-2 align-items-center">
            <!-- Left: Preset Pills -->
            <div class="col-xl-5 col-lg-12">
                <div class="d-flex align-items-center gap-1 flex-wrap">
                    <span class="text-muted fw-bold me-1" style="font-size: 0.72rem;">Presets:</span>
                    <a href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['filter' => 'all'])) }}" 
                       class="filter-preserve-scroll btn btn-sm py-0.5 px-2 rounded-pill fw-bold {{ $filter === 'all' ? 'btn-primary' : 'btn-light border text-dark' }}" style="font-size: 0.73rem;">
                        <i class="fa-solid fa-clock-rotate-left me-1"></i> All Latest
                    </a>
                    <a href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['filter' => 'higher_amount'])) }}" 
                       class="filter-preserve-scroll btn btn-sm py-0.5 px-2 rounded-pill fw-bold {{ $filter === 'higher_amount' ? 'btn-warning text-dark' : 'btn-light border text-dark' }}" style="font-size: 0.73rem;">
                        <i class="fa-solid fa-fire me-1 text-danger"></i> Higher Amount
                    </a>
                    <a href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['filter' => 'higher_color_user'])) }}" 
                       class="filter-preserve-scroll btn btn-sm py-0.5 px-2 rounded-pill fw-bold {{ $filter === 'higher_color_user' ? 'btn-danger text-white' : 'btn-light border text-dark' }}" style="font-size: 0.73rem;">
                        <i class="fa-solid fa-palette me-1"></i> Higher Color
                    </a>
                    <a href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['filter' => 'higher_no_user'])) }}" 
                       class="filter-preserve-scroll btn btn-sm py-0.5 px-2 rounded-pill fw-bold {{ $filter === 'higher_no_user' ? 'btn-info text-dark' : 'btn-light border text-dark' }}" style="font-size: 0.73rem;">
                        <i class="fa-solid fa-hashtag me-1"></i> Higher Number
                    </a>
                </div>
            </div>

            <!-- Right: Search, Status, Date & Submit Form -->
            <div class="col-xl-7 col-lg-12">
                <form method="GET" action="{{ route('admin.bets.index') }}" id="bets-filter-form" class="row g-1 align-items-center justify-content-xl-end">
                    <input type="hidden" name="game" value="{{ $game }}">
                    <input type="hidden" name="filter" value="{{ $filter }}">

                    <div class="col-sm-4 col-12">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0 rounded-start-pill py-0 px-2 text-muted"><i class="fa-solid fa-magnifying-glass" style="font-size: 0.72rem;"></i></span>
                            <input type="text" name="search" class="form-control form-control-sm border-start-0 rounded-end-pill py-1" style="font-size: 0.76rem;" placeholder="Player, phone, bet ID..." value="{{ $search }}">
                        </div>
                    </div>

                    <div class="col-sm-3 col-6">
                        <select name="status" class="form-select form-select-sm rounded-pill py-1" style="font-size: 0.76rem;">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="won" {{ $status === 'won' ? 'selected' : '' }}>Won Only</option>
                            <option value="lost" {{ $status === 'lost' ? 'selected' : '' }}>Lost Only</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending Only</option>
                        </select>
                    </div>

                    <div class="col-sm-3 col-6">
                        <input type="date" name="date" class="form-control form-control-sm rounded-pill py-1" style="font-size: 0.76rem;" value="{{ $date }}">
                    </div>

                    <div class="col-sm-2 col-12 d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill fw-bold py-1 px-2 flex-grow-1" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-filter me-1"></i> Go
                        </button>
                        <a href="{{ route('admin.bets.index', ['game' => $game]) }}" class="filter-preserve-scroll btn btn-outline-secondary btn-sm rounded-pill py-1 px-2" title="Reset Filters" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bets Table (Row 4 - High Density) -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <h6 class="fw-bold text-dark mb-0">
                <i class="fa-solid fa-list-check text-primary me-1.5"></i> Bet Transactions List
            </h6>
            <span class="badge bg-light border text-muted px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;">
                {{ number_format($bets->total()) }} Records Found
            </span>
        </div>
        <span class="text-muted small" style="font-size: 0.75rem;">
            Showing {{ $bets->firstItem() ?? 0 }}-{{ $bets->lastItem() ?? 0 }} of {{ number_format($bets->total()) }}
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-compact-bets align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">ID & Date</th>
                        <th>Player Details</th>
                        <th>Game</th>
                        <th>Slot / Round</th>
                        <th>Selection</th>
                        <th class="text-end">Bet Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-3">Win / Payout</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bets as $bet)
                        @php
                            if ($isUnified) {
                                $betId = $bet->id;
                                $createdAt = $bet->created_at;
                                $user = $bet->user;
                                $gameBadge = $bet->game_name;
                                $badgeColor = $bet->badge_color;
                                $periodIdent = $bet->period_identifier;
                                $betTarget = $bet->bet_target;
                                $betType = $bet->bet_type;
                                $amount = $bet->amount;
                                $status = $bet->status;
                                $winAmount = $bet->win_amount;
                                $multiplier = $bet->win_multiplier;
                            } elseif ($activeGameType === 'lottery') {
                                $betId = $bet->id;
                                $createdAt = $bet->created_at;
                                $user = $bet->user;
                                $gameBadge = '2-Digit Lottery';
                                $badgeColor = 'primary';
                                $periodIdent = ($bet->result_date ? $bet->result_date->format('Y-m-d') : '').' ('.$bet->slot_time.')';
                                $betTarget = 'Number: #'.$bet->chosen_number;
                                $betType = 'number';
                                $amount = (float) $bet->amount;
                                $status = $bet->status;
                                $winAmount = (float) $bet->win_amount;
                                $multiplier = (float) $bet->win_multiplier;
                            } elseif ($activeGameType === 'andar_bahar') {
                                $betId = $bet->id;
                                $createdAt = $bet->created_at;
                                $user = $bet->user;
                                $gameBadge = 'Andar Bahar Live';
                                $badgeColor = 'warning text-dark';
                                $periodIdent = '#'.$bet->round_id;
                                $betTarget = in_array(strtolower($bet->bet_selection), ['andar', 'bahar'])
                                    ? strtoupper($bet->bet_selection)
                                    : $bet->bet_selection.' Cards';
                                $betType = $bet->bet_type;
                                $amount = (float) $bet->amount;
                                $status = $bet->status;
                                $winAmount = (float) $bet->win_amount;
                                $multiplier = (float) ($bet->multiplier ?? $bet->win_multiplier ?? 1.90);
                            } elseif ($activeGameType === 'aviator') {
                                $betId = $bet->id;
                                $createdAt = $bet->created_at;
                                $user = $bet->user;
                                $gameBadge = 'Aviator Crash';
                                $badgeColor = 'danger';
                                $periodIdent = '#'.$bet->round_id;
                                $betTarget = 'Slot '.$bet->bet_slot.($bet->auto_cashout ? ' (Auto: '.$bet->auto_cashout.'x)' : '');
                                $betType = 'crash';
                                $amount = (float) $bet->amount;
                                $status = ($bet->status === 'cashed_out' ? 'won' : $bet->status);
                                $winAmount = (float) $bet->win_amount;
                                $multiplier = (float) ($bet->cashout_multiplier ?? 0);
                            } elseif ($activeGameType === 'mines') {
                                $betId = $bet->id;
                                $createdAt = $bet->created_at;
                                $user = $bet->user;
                                $gameBadge = 'Mines 5x5';
                                $badgeColor = 'success';
                                $periodIdent = '#'.substr($bet->game_id, -8);
                                $betTarget = $bet->mines_count.' Mines ('.$bet->current_step.' Coins)'.($bet->is_jackpot_win ? ' [JP]' : '');
                                $betType = 'arcade';
                                $amount = (float) $bet->bet_amount;
                                $status = ($bet->status === 'cashed_out' ? 'won' : ($bet->status === 'exploded' ? 'lost' : 'pending'));
                                $winAmount = (float) $bet->win_amount;
                                $multiplier = (float) $bet->current_multiplier;
                            } elseif ($activeGameType === 'roulette') {
                                $betId = $bet->id;
                                $createdAt = $bet->created_at;
                                $user = $bet->user;
                                $gameBadge = 'Roulette 36x';
                                $badgeColor = 'warning text-dark';
                                $periodIdent = 'Round #' . $bet->round_id;
                                $betTarget = $bet->selection_label ?? (ucfirst($bet->bet_type) . ': ' . $bet->bet_value);
                                $betType = $bet->bet_type;
                                $amount = (float) $bet->bet_amount;
                                $status = $bet->status;
                                $winAmount = (float) $bet->payout_amount;
                                $multiplier = (float) $bet->odds;
                            } elseif ($activeGameType === 'horse_racing') {
                                $betId = $bet->id;
                                $createdAt = $bet->created_at;
                                $user = $bet->user;
                                $gameBadge = 'Horse Racing Live';
                                $badgeColor = 'success';
                                $periodIdent = 'Race #' . $bet->round_id;
                                $betTarget = strtoupper($bet->bet_type) . ': #' . $bet->horse_id . ' ' . $bet->horse_name;
                                $betType = $bet->bet_type;
                                $amount = (float) $bet->bet_amount;
                                $status = $bet->status;
                                $winAmount = (float) $bet->payout_amount;
                                $multiplier = (float) $bet->odds;
                            } elseif ($activeGameType === 'dragon_tiger') {
                                $betId = $bet->id;
                                $createdAt = $bet->created_at;
                                $user = $bet->user;
                                $gameBadge = 'Dragon Tiger Live';
                                $badgeColor = 'danger text-white';
                                $periodIdent = 'Round #' . $bet->round_id;
                                $betTarget = strtoupper($bet->bet_type);
                                $betType = $bet->bet_type;
                                $amount = (float) $bet->bet_amount;
                                $status = $bet->status;
                                $winAmount = (float) $bet->payout_amount;
                                $multiplier = (float) $bet->odds;
                            } elseif ($activeGameType === 'teen_patti') {
                                $betId = $bet->id;
                                $createdAt = $bet->created_at;
                                $user = $bet->user;
                                $gameBadge = 'Teen Patti 20-20';
                                $badgeColor = 'warning text-dark';
                                $periodIdent = 'Round #' . $bet->round_id;
                                $betTarget = str_replace('_', ' ', strtoupper($bet->bet_type));
                                $betType = $bet->bet_type;
                                $amount = (float) $bet->bet_amount;
                                $status = $bet->status;
                                $winAmount = (float) $bet->payout_amount;
                                $multiplier = (float) $bet->odds;
                            } elseif ($activeGameType === 'vortex') {
                                $betId = $bet->id;
                                $createdAt = $bet->created_at;
                                $user = $bet->user;
                                $gameBadge = 'Vortex Wheel';
                                $badgeColor = 'info text-dark';
                                $periodIdent = '#' . substr($bet->game_id, -8);
                                $betTarget = 'Ring ' . $bet->current_ring . ' (' . ((float) $bet->current_multiplier) . 'x)';
                                $betType = 'wheel';
                                $amount = (float) $bet->bet_amount;
                                $status = ($bet->status === 'cashed_out' ? 'won' : ($bet->status === 'busted' ? 'lost' : 'pending'));
                                $winAmount = (float) ($bet->win_amount ?? $bet->payout_amount ?? 0);
                                $multiplier = (float) $bet->current_multiplier;
                            } elseif ($activeGameType === 'sports') {
                                $betId = $bet->id;
                                $createdAt = $bet->created_at;
                                $user = $bet->user;
                                $gameBadge = 'Cricket Sportsbook';
                                $badgeColor = 'primary';
                                $periodIdent = $bet->match ? ($bet->match->team_a . ' vs ' . $bet->match->team_b) : ('Match #' . $bet->match_id);
                                $betTarget = strtoupper($bet->bet_type) . ': ' . $bet->runner_name;
                                $betType = $bet->bet_type;
                                $amount = (float) $bet->stake;
                                $status = $bet->status;
                                $winAmount = (float) ($bet->win_amount ?? 0);
                                $multiplier = (float) $bet->odds;
                            } else {
                                $betId = $bet->id;
                                $createdAt = $bet->created_at;
                                $user = $bet->user;
                                $gameBadge = 'WinGo '.strtoupper($bet->game_type);
                                $badgeColor = 'danger';
                                $periodIdent = '#'.$bet->period_id;
                                $betTarget = ucfirst($bet->bet_type).': '.$bet->bet_selection;
                                $betType = $bet->bet_type;
                                $amount = (float) $bet->total_amount;
                                $status = $bet->status;
                                $winAmount = (float) $bet->win_amount;
                                $multiplier = (float) $bet->win_multiplier;
                            }
                            $commission = (float) ($bet->commission_amount ?? 0);
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <span class="fw-bold text-dark font-monospace" style="font-size: 0.8rem;">#{{ $betId }}</span>
                                <div class="text-muted" style="font-size: 0.7rem;">
                                    {{ $createdAt ? $createdAt->timezone('Asia/Kolkata')->format('d M, h:i A') : '-' }}
                                </div>
                            </td>
                            <td>
                                @if($user)
                                    <div class="d-flex align-items-center gap-1.5">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 26px; height: 26px; font-size: 0.75rem;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="text-truncate" style="max-width: 140px;">
                                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.8rem;">{{ $user->name }}</div>
                                            <div class="text-muted text-truncate font-monospace" style="font-size: 0.68rem;">{{ $user->phone ?? $user->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted" style="font-size: 0.75rem;">Guest / Deleted</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $badgeColor }} border rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                                    {{ $gameBadge }}
                                </span>
                            </td>
                            <td>
                                <span class="font-monospace fw-semibold text-secondary" style="font-size: 0.75rem;">
                                    {{ $periodIdent }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $targetLower = strtolower($betTarget);
                                @endphp
                                @if(str_contains($targetLower, 'straight'))
                                    <span class="badge px-2 py-0.5 rounded-pill text-white fw-bold shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #dc2626); font-family: monospace; font-size: 0.72rem;"><i class="fa-solid fa-dharmachakra me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'black'))
                                    <span class="badge px-2 py-0.5 rounded-pill text-white fw-bold" style="background: #0f172a; border: 1px solid #475569; font-size: 0.72rem;"><i class="fa-solid fa-circle me-1" style="font-size:0.5rem; color:#cbd5e1;"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'dozen') || str_contains($targetLower, 'column'))
                                    <span class="badge bg-warning text-dark px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.72rem;"><i class="fa-solid fa-table-cells-large me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'slot'))
                                    <span class="badge bg-danger px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;"><i class="fa-solid fa-plane me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'mines'))
                                    <span class="badge bg-success px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;"><i class="fa-solid fa-bomb me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'andar'))
                                    <span class="badge bg-danger px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;"><i class="fa-solid fa-arrow-left me-1"></i> ANDAR</span>
                                @elseif(str_contains($targetLower, 'bahar'))
                                    <span class="badge bg-primary px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;"><i class="fa-solid fa-arrow-right me-1"></i> BAHAR</span>
                                @elseif(str_contains($targetLower, 'green'))
                                    <span class="badge bg-success px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;"><i class="fa-solid fa-circle me-1" style="font-size:0.5rem;"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'red'))
                                    <span class="badge bg-danger px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;"><i class="fa-solid fa-circle me-1" style="font-size:0.5rem;"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'violet'))
                                    <span class="badge px-2 py-0.5 rounded-pill" style="background: #8b5cf6; color: #fff; font-size: 0.72rem;"><i class="fa-solid fa-circle me-1" style="font-size:0.5rem;"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'big'))
                                    <span class="badge bg-warning text-dark px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;"><i class="fa-solid fa-up-long me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'small'))
                                    <span class="badge bg-info text-dark px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;"><i class="fa-solid fa-down-long me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'cards'))
                                    <span class="badge bg-warning text-dark px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;"><i class="fa-solid fa-layer-group me-1"></i> {{ $betTarget }}</span>
                                @elseif($targetLower === 'dragon' || str_starts_with($targetLower, 'dragon'))
                                    <span class="badge bg-danger text-white px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.72rem;"><i class="fa-solid fa-dragon me-1"></i> {{ $betTarget }}</span>
                                @elseif($targetLower === 'tiger' || str_starts_with($targetLower, 'tiger'))
                                    <span class="badge bg-warning text-dark px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.72rem;"><i class="fa-solid fa-fire me-1"></i> {{ $betTarget }}</span>
                                @elseif($targetLower === 'tie' || str_starts_with($targetLower, 'tie'))
                                    <span class="badge bg-success text-white px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.72rem;"><i class="fa-solid fa-equals me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'player a') || str_contains($targetLower, 'player_a'))
                                    <span class="badge bg-primary text-white px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.72rem;"><i class="fa-solid fa-crown me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'player b') || str_contains($targetLower, 'player_b'))
                                    <span class="badge bg-danger text-white px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.72rem;"><i class="fa-solid fa-crown me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'pair plus') || str_contains($targetLower, 'pair_plus'))
                                    <span class="badge bg-warning text-dark px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.72rem;"><i class="fa-solid fa-star me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'ring'))
                                    <span class="badge bg-info text-dark px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.72rem;"><i class="fa-solid fa-compact-disc me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'back') || str_contains($targetLower, 'lay'))
                                    <span class="badge bg-primary text-white px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.72rem;"><i class="fa-solid fa-baseball-bat-ball me-1"></i> {{ $betTarget }}</span>
                                @else
                                    <span class="badge bg-dark px-2 py-0.5 rounded-pill font-monospace" style="font-size: 0.72rem;"><i class="fa-solid fa-hashtag me-1"></i> {{ $betTarget }}</span>
                                @endif
                            </td>
                            <td class="text-end font-monospace fw-bold text-dark" style="font-size: 0.85rem;">
                                ₹{{ number_format($amount, 2) }}
                            </td>
                            <td class="text-center">
                                @if($status === 'won')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-circle-check me-1"></i> Won ({{ $multiplier }}X)
                                    </span>
                                @elseif($status === 'lost')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> Lost
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-clock me-1"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-3 font-monospace fw-bold {{ $winAmount > 0 ? 'text-success' : 'text-muted' }}" style="font-size: 0.85rem;">
                                @if($winAmount > 0)
                                    <div>+₹{{ number_format($winAmount, 2) }}</div>
                                    @if($commission > 0)
                                        <div class="text-muted small fw-normal" style="font-size: 0.67rem;">(-₹{{ number_format($commission, 2) }} fee)</div>
                                    @endif
                                @else
                                    ₹0.00
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-inbox fs-3 mb-1 d-block text-secondary opacity-50"></i>
                                <span style="font-size: 0.82rem;">No bet records found matching the selected filters.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bets->hasPages())
            <div class="card-footer bg-light bg-opacity-50 border-top py-2 px-3 d-flex justify-content-end">
                {{ $bets->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

{{-- Scroll Preservation & Immediate Focus Script --}}
<script>
    (function() {
        // Prevent browser from automatically jumping to 0,0 on navigation reloads
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }

        // Restore scroll position if previously saved before click/submit
        var savedY = sessionStorage.getItem('admin_bets_scroll_pos');
        if (savedY !== null) {
            sessionStorage.removeItem('admin_bets_scroll_pos');
            window.scrollTo({
                top: parseInt(savedY, 10),
                behavior: 'instant'
            });
        }

        // Function to capture exact scroll coordinate
        var captureScroll = function() {
            sessionStorage.setItem('admin_bets_scroll_pos', window.scrollY || window.pageYOffset || document.documentElement.scrollTop);
        };

        // Attach to all filter buttons, tabs, pagination links, and filter form
        document.querySelectorAll('.filter-preserve-scroll, .scroll-tabs-bar a, .pagination a').forEach(function(link) {
            link.addEventListener('click', captureScroll);
        });

        var filterForm = document.getElementById('bets-filter-form');
        if (filterForm) {
            filterForm.addEventListener('submit', captureScroll);
        }
    })();
</script>
@endsection
