@extends('layouts.admin')

@section('title', 'All Game Bets Monitor - rupeerush Management')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-receipt text-primary me-2"></i> All Game Bets Monitor
        </h2>
        <p class="text-muted small mb-0">Live centralized tracking of every bet placed across all <strong>11 Games</strong> (Aviator, Mines, Roulette, Dragon Tiger, Teen Patti, Vortex, Horse Racing, Andar Bahar, 2-Digit Lottery, WinGo, and Cricket Sportsbook).</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.dragon-tiger.index') }}" class="btn btn-outline-danger fw-bold rounded-pill px-3">
            <i class="fa-solid fa-dragon me-1"></i> Dragon Tiger
        </a>
        <a href="{{ route('admin.teen-patti.index') }}" class="btn btn-outline-warning text-dark fw-bold rounded-pill px-3">
            <i class="fa-solid fa-crown me-1 text-danger"></i> Teen Patti
        </a>
        <a href="{{ route('admin.vortex.index') }}" class="btn btn-outline-info text-dark fw-bold rounded-pill px-3">
            <i class="fa-solid fa-compact-disc me-1"></i> Vortex Wheel
        </a>
        <a href="{{ route('admin.horse-racing.index') }}" class="btn btn-outline-success fw-bold rounded-pill px-3">
            <i class="fa-solid fa-horse-head me-1"></i> Horse Racing
        </a>
        <a href="{{ route('admin.roulette.index') }}" class="btn btn-outline-warning text-dark fw-bold rounded-pill px-3">
            <i class="fa-solid fa-dharmachakra me-1 text-danger"></i> Roulette
        </a>
        <a href="{{ route('admin.aviator.index') }}" class="btn btn-outline-danger fw-bold rounded-pill px-3">
            <i class="fa-solid fa-plane-departure me-1"></i> Aviator
        </a>
        <a href="{{ route('admin.andar-bahar.index') }}" class="btn btn-outline-warning text-dark fw-bold rounded-pill px-3">
            <i class="fa-solid fa-layer-group me-1"></i> Andar Bahar
        </a>
        <a href="{{ route('admin.wingo.index') }}" class="btn btn-outline-danger fw-bold rounded-pill px-3">
            <i class="fa-solid fa-gamepad me-1"></i> WinGo
        </a>
        <a href="{{ route('admin.results.index') }}" class="btn btn-outline-primary fw-bold rounded-pill px-3">
            <i class="fa-solid fa-sliders me-1"></i> 2-Digit Slot
        </a>
    </div>
</div>

<!-- Financial & Volume Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Bets Placed</div>
                <div class="stat-value text-dark">{{ number_format($stats['total_bets']) }}</div>
                <div class="text-muted small mt-1" style="font-size: 0.72rem; line-height: 1.35;">
                    DT: <strong>{{ number_format($stats['dragon_tiger_count'] ?? 0) }}</strong> | TP: <strong>{{ number_format($stats['teen_patti_count'] ?? 0) }}</strong> | Vor: <strong>{{ number_format($stats['vortex_count'] ?? 0) }}</strong> | Spo: <strong>{{ number_format($stats['sports_count'] ?? 0) }}</strong> | Derby: <strong>{{ number_format($stats['horse_racing_count'] ?? 0) }}</strong> | Rou: <strong>{{ number_format($stats['roulette_count'] ?? 0) }}</strong> | Av: <strong>{{ number_format($stats['aviator_count'] ?? 0) }}</strong> | Min: <strong>{{ number_format($stats['mines_count'] ?? 0) }}</strong> | AB: <strong>{{ number_format($stats['andar_count'] ?? 0) }}</strong> | Lot: <strong>{{ number_format($stats['lottery_count'] ?? 0) }}</strong> | Win: <strong>{{ number_format($stats['wingo_count'] ?? 0) }}</strong>
                </div>
            </div>
            <div class="stat-icon" style="background: rgba(14, 165, 233, 0.15); color: #0284c7;">
                <i class="fa-solid fa-dice"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Wagered Pool</div>
                <div class="stat-value text-primary">₹{{ number_format($stats['total_volume'], 2) }}</div>
                <div class="text-muted small mt-1" style="font-size: 0.72rem; line-height: 1.35;">
                    DT: ₹{{ number_format($stats['dragon_tiger_volume'] ?? 0, 0) }} | TP: ₹{{ number_format($stats['teen_patti_volume'] ?? 0, 0) }} | Vor: ₹{{ number_format($stats['vortex_volume'] ?? 0, 0) }} | Spo: ₹{{ number_format($stats['sports_volume'] ?? 0, 0) }} | Derby: ₹{{ number_format($stats['horse_racing_volume'] ?? 0, 0) }} | Rou: ₹{{ number_format($stats['roulette_volume'] ?? 0, 0) }} | Av: ₹{{ number_format($stats['aviator_volume'] ?? 0, 0) }} | Min: ₹{{ number_format($stats['mines_volume'] ?? 0, 0) }} | AB: ₹{{ number_format($stats['andar_volume'] ?? 0, 0) }} | Lot: ₹{{ number_format($stats['lottery_volume'] ?? 0, 0) }} | Win: ₹{{ number_format($stats['wingo_volume'] ?? 0, 0) }}
                </div>
            </div>
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Payouts Won</div>
                <div class="stat-value text-danger">₹{{ number_format($stats['total_payout'], 2) }}</div>
                <div class="text-muted small mt-1">Credited directly to player wallets</div>
            </div>
            <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Net House Profit</div>
                <div class="stat-value {{ $stats['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                    ₹{{ number_format($stats['net_profit'], 2) }}
                </div>
                <div class="text-muted small mt-1 d-flex align-items-center justify-content-between flex-wrap gap-1">
                    <span>Margin: {{ $stats['total_volume'] > 0 ? number_format(($stats['net_profit'] / $stats['total_volume']) * 100, 1) : 0 }}%</span>
                    @if(!empty($stats['total_commission']))
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" title="Total commission deducted from winning payouts">
                            ₹{{ number_format($stats['total_commission'], 2) }} Cut
                        </span>
                    @endif
                </div>
            </div>
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
        </div>
    </div>
</div>

<!-- Spotlight: Top Bettors Highlights (Real-Time DB Analytics) -->
<div class="row g-3 mb-4">
    <!-- 1. Highest Bet Amount -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge bg-warning text-dark fw-bold px-2 py-1 rounded-pill small">
                        <i class="fa-solid fa-fire me-1"></i> HIGHEST BET AMOUNT
                    </span>
                    <a href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['filter' => 'higher_amount'])) }}" class="btn btn-sm btn-outline-warning rounded-pill py-0 px-2 fw-bold text-nowrap" style="font-size: 0.75rem;">
                        View All High
                    </a>
                </div>
                @if(!empty($spotlight['topBet']))
                    <div>
                        <div class="fs-4 fw-bold text-warning font-monospace">₹{{ number_format($spotlight['topBet']['amount'], 2) }}</div>
                        <div class="fw-bold text-white text-truncate">{{ $spotlight['topBet']['user']->name ?? 'Player' }} <span class="text-white-50 small font-monospace">({{ $spotlight['topBet']['user']->phone ?? '' }})</span></div>
                        <div class="small text-white-50 mt-1">
                            <span class="badge bg-white bg-opacity-10 text-white">{{ $spotlight['topBet']['game'] }}</span>
                            <span class="text-info fw-bold ms-1">{{ $spotlight['topBet']['selection'] }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-white-50 small py-2">No bets recorded for this date yet.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- 2. Higher Color User -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #4a044e 0%, #701a75 100%); color: #fff;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge bg-danger text-white fw-bold px-2 py-1 rounded-pill small">
                        <i class="fa-solid fa-palette me-1"></i> HIGHER COLOR USER
                    </span>
                    <a href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['filter' => 'higher_color_user'])) }}" class="btn btn-sm btn-outline-light rounded-pill py-0 px-2 fw-bold text-nowrap" style="font-size: 0.75rem;">
                        View Color Bets
                    </a>
                </div>
                @if(!empty($spotlight['topColorUser']))
                    <div>
                        <div class="fs-4 fw-bold text-light font-monospace">₹{{ number_format($spotlight['topColorUser']['amount'], 2) }}</div>
                        <div class="fw-bold text-white text-truncate">{{ $spotlight['topColorUser']['user']->name ?? 'Color Bettor' }} <span class="text-white-50 small font-monospace">({{ $spotlight['topColorUser']['user']->phone ?? '' }})</span></div>
                        <div class="small text-white-50 mt-1">
                            Color: <span class="badge bg-light text-dark fw-bold">{{ $spotlight['topColorUser']['color'] }}</span>
                            <span class="ms-1">{{ $spotlight['topColorUser']['game'] }} (Round #{{ $spotlight['topColorUser']['period'] }})</span>
                        </div>
                    </div>
                @else
                    <div class="text-white-50 small py-2">No WinGo color bets for this period yet.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- 3. Higher Number User -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #0c4a6e 0%, #0369a1 100%); color: #fff;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge bg-info text-dark fw-bold px-2 py-1 rounded-pill small">
                        <i class="fa-solid fa-hashtag me-1"></i> HIGHER NUMBER USER
                    </span>
                    <a href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['filter' => 'higher_no_user'])) }}" class="btn btn-sm btn-outline-light rounded-pill py-0 px-2 fw-bold text-nowrap" style="font-size: 0.75rem;">
                        View Number Bets
                    </a>
                </div>
                @if(!empty($spotlight['topNumberUser']))
                    <div>
                        <div class="fs-4 fw-bold text-white font-monospace">₹{{ number_format($spotlight['topNumberUser']['amount'], 2) }}</div>
                        <div class="fw-bold text-white text-truncate">{{ $spotlight['topNumberUser']['user']->name ?? 'Number Bettor' }} <span class="text-white-50 small font-monospace">({{ $spotlight['topNumberUser']['user']->phone ?? '' }})</span></div>
                        <div class="small text-white-50 mt-1">
                            Target: <span class="badge bg-warning text-dark fw-bold font-monospace">{{ $spotlight['topNumberUser']['number'] }}</span>
                            <span class="ms-1">{{ $spotlight['topNumberUser']['game'] }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-white-50 small py-2">No number bets placed for this date yet.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Filters & Game Tabs Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <!-- Game Type Quick Navigation Tabs -->
        <ul class="nav nav-pills mb-3 gap-2 border-bottom pb-3 flex-wrap">
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-bold {{ $game === 'all' ? 'active bg-primary' : 'bg-light text-dark' }}" 
                   href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'all'])) }}">
                   <i class="fa-solid fa-layer-group me-1"></i> Sabhi Games (All)
                   <span class="badge {{ $game === 'all' ? 'bg-white text-primary' : 'bg-primary text-white' }} ms-1 rounded-pill">{{ $stats['total_bets'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-bold {{ $game === 'dragon_tiger' ? 'active bg-danger text-white' : 'bg-light text-dark' }}" 
                   href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'dragon_tiger'])) }}">
                   <i class="fa-solid fa-dragon me-1"></i> Dragon Tiger Live
                   <span class="badge bg-white text-dark ms-1 rounded-pill">{{ $stats['dragon_tiger_count'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-bold {{ $game === 'teen_patti' ? 'active bg-warning text-dark' : 'bg-light text-dark' }}" 
                   href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'teen_patti'])) }}">
                   <i class="fa-solid fa-crown me-1 text-danger"></i> Teen Patti 20-20
                   <span class="badge bg-white text-dark ms-1 rounded-pill">{{ $stats['teen_patti_count'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-bold {{ $game === 'vortex' ? 'active bg-info text-dark' : 'bg-light text-dark' }}" 
                   href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'vortex'])) }}">
                   <i class="fa-solid fa-compact-disc me-1"></i> Vortex Wheel
                   <span class="badge bg-white text-dark ms-1 rounded-pill">{{ $stats['vortex_count'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-bold {{ $game === 'sports' ? 'active bg-primary text-white' : 'bg-light text-dark' }}" 
                   href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'sports'])) }}">
                   <i class="fa-solid fa-baseball-bat-ball me-1"></i> Cricket Sportsbook
                   <span class="badge bg-white text-dark ms-1 rounded-pill">{{ $stats['sports_count'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-bold {{ $game === 'horse_racing' ? 'active bg-success text-white' : 'bg-light text-dark' }}" 
                   href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'horse_racing'])) }}">
                   <i class="fa-solid fa-horse-head me-1"></i> Horse Racing Live
                   <span class="badge bg-white text-dark ms-1 rounded-pill">{{ $stats['horse_racing_count'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-bold {{ $game === 'roulette' ? 'active bg-warning text-dark' : 'bg-light text-dark' }}" 
                   href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'roulette'])) }}">
                   <i class="fa-solid fa-dharmachakra me-1 text-danger"></i> Roulette 36x
                   <span class="badge bg-white text-dark ms-1 rounded-pill">{{ $stats['roulette_count'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-bold {{ $game === 'aviator' ? 'active bg-danger text-white' : 'bg-light text-dark' }}" 
                   href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'aviator'])) }}">
                   <i class="fa-solid fa-plane-departure me-1"></i> Aviator Crash
                   <span class="badge bg-white text-dark ms-1 rounded-pill">{{ $stats['aviator_count'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-bold {{ $game === 'mines' ? 'active bg-success text-white' : 'bg-light text-dark' }}" 
                   href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'mines'])) }}">
                   <i class="fa-solid fa-bomb me-1"></i> Mines 5x5
                   <span class="badge bg-white text-dark ms-1 rounded-pill">{{ $stats['mines_count'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-bold {{ $game === 'andar_bahar' ? 'active bg-warning text-dark' : 'bg-light text-dark' }}" 
                   href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'andar_bahar'])) }}">
                   <i class="fa-solid fa-cards-blank me-1"></i> Andar Bahar Live
                   <span class="badge bg-white text-dark ms-1 rounded-pill">{{ $stats['andar_count'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-bold {{ $game === 'lottery' ? 'active bg-info text-white' : 'bg-light text-dark' }}" 
                   href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'lottery'])) }}">
                   <i class="fa-solid fa-ticket me-1"></i> 2-Digit Lottery
                   <span class="badge bg-white text-dark ms-1 rounded-pill">{{ $stats['lottery_count'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-bold {{ $game === 'wingo' ? 'active bg-danger text-white' : 'bg-light text-dark' }}" 
                   href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'wingo'])) }}">
                   <i class="fa-solid fa-palette me-1"></i> WinGo All
                   <span class="badge bg-white text-dark ms-1 rounded-pill">{{ $stats['wingo_count'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-bold {{ $game === 'wingo_30s' ? 'active bg-dark text-white' : 'bg-light text-dark' }}" 
                   href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'wingo_30s'])) }}">
                   WinGo 30s
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-bold {{ $game === 'wingo_1m' ? 'active bg-dark text-white' : 'bg-light text-dark' }}" 
                   href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'wingo_1m'])) }}">
                   WinGo 1M
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-bold {{ $game === 'wingo_3m' ? 'active bg-dark text-white' : 'bg-light text-dark' }}" 
                   href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'wingo_3m'])) }}">
                   WinGo 3M
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-bold {{ $game === 'wingo_5m' ? 'active bg-dark text-white' : 'bg-light text-dark' }}" 
                   href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['game' => 'wingo_5m'])) }}">
                   WinGo 5M
                </a>
            </li>
        </ul>

        <!-- Quick Filter Pills: All / Higher Amount / Higher Color User / Higher No User -->
        <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
            <span class="small fw-bold text-muted me-1">Filter Preset:</span>
            <a href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['filter' => 'all'])) }}" 
               class="btn btn-sm rounded-pill px-3 fw-bold {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-clock-rotate-left me-1"></i> All Bets (Latest First)
            </a>
            <a href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['filter' => 'higher_amount'])) }}" 
               class="btn btn-sm rounded-pill px-3 fw-bold {{ $filter === 'higher_amount' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }}">
                <i class="fa-solid fa-arrow-down-wide-short me-1"></i> 🔥 Higher Amount (High Rollers)
            </a>
            <a href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['filter' => 'higher_color_user'])) }}" 
               class="btn btn-sm rounded-pill px-3 fw-bold {{ $filter === 'higher_color_user' ? 'btn-danger' : 'btn-outline-danger' }}">
                <i class="fa-solid fa-palette me-1"></i> 🎨 Higher Color User Bets
            </a>
            <a href="{{ route('admin.bets.index', array_merge(request()->except('page'), ['filter' => 'higher_no_user'])) }}" 
               class="btn btn-sm rounded-pill px-3 fw-bold {{ $filter === 'higher_no_user' ? 'btn-info text-white' : 'btn-outline-info text-dark' }}">
                <i class="fa-solid fa-hashtag me-1"></i> 🔢 Higher Number User Bets
            </a>
        </div>

        <form method="GET" action="{{ route('admin.bets.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="game" value="{{ $game }}">

            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 rounded-start-pill"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 rounded-end-pill" placeholder="Player name, phone, selection..." value="{{ $search }}">
                </div>
            </div>

            <div class="col-md-3">
                <select name="filter" class="form-select rounded-pill">
                    <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>Filter: All Bets (Newest First)</option>
                    <option value="higher_amount" {{ $filter === 'higher_amount' ? 'selected' : '' }}>Filter: 🔥 Higher Amount First</option>
                    <option value="higher_color_user" {{ $filter === 'higher_color_user' ? 'selected' : '' }}>Filter: 🎨 Higher Color User</option>
                    <option value="higher_no_user" {{ $filter === 'higher_no_user' ? 'selected' : '' }}>Filter: 🔢 Higher Number User</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="status" class="form-select rounded-pill">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending Only</option>
                    <option value="won" {{ $status === 'won' ? 'selected' : '' }}>Won Only</option>
                    <option value="lost" {{ $status === 'lost' ? 'selected' : '' }}>Lost Only</option>
                </select>
            </div>

            <div class="col-md-2">
                <input type="date" name="date" class="form-control rounded-pill" value="{{ $date }}">
            </div>

            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary rounded-pill fw-bold flex-grow-1">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                <a href="{{ route('admin.bets.index', ['game' => $game]) }}" class="btn btn-outline-secondary rounded-pill" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Bets Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-list-check text-primary me-2"></i> Bet Transactions List
        </h5>
        <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
            Showing {{ $bets->firstItem() ?? 0 }} - {{ $bets->lastItem() ?? 0 }} of {{ $bets->total() }} bets
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID & Date</th>
                        <th>Player Details</th>
                        <th>Game Name</th>
                        <th>Slot / Round</th>
                        <th>Bet Selection</th>
                        <th class="text-end">Bet Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Win / Payout</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bets as $bet)
                        @php
                            // Handle Unified object and specific Model instances
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
                                $betTarget = $bet->mines_count.' Mines ('.$bet->current_step.' Safe Coins)'.($bet->is_jackpot_win ? ' [JACKPOT]' : '');
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
                            <td class="ps-4">
                                <span class="fw-bold text-dark font-monospace">#{{ $betId }}</span>
                                <div class="text-muted small" style="font-size: 0.75rem;">
                                    {{ $createdAt ? $createdAt->timezone('Asia/Kolkata')->format('d M Y, h:i:s A') : '-' }}
                                </div>
                            </td>
                            <td>
                                @if($user)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px; font-size: 0.85rem;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $user->phone ?? $user->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">Guest / Deleted</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $badgeColor }} border rounded-pill px-3 py-1 fw-bold">
                                    {{ $gameBadge }}
                                </span>
                            </td>
                            <td>
                                <span class="font-monospace fw-bold text-secondary" style="font-size: 0.85rem;">
                                    {{ $periodIdent }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $targetLower = strtolower($betTarget);
                                @endphp
                                @if(str_contains($targetLower, 'straight'))
                                    <span class="badge px-3 py-1 rounded-pill text-white fw-bold shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #dc2626); font-family: monospace;"><i class="fa-solid fa-dharmachakra me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'black'))
                                    <span class="badge px-3 py-1 rounded-pill text-white fw-bold" style="background: #0f172a; border: 1px solid #475569;"><i class="fa-solid fa-circle me-1" style="font-size:0.6rem; color:#cbd5e1;"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'dozen') || str_contains($targetLower, 'column'))
                                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold"><i class="fa-solid fa-table-cells-large me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'slot'))
                                    <span class="badge bg-danger px-3 py-1 rounded-pill"><i class="fa-solid fa-plane me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'mines'))
                                    <span class="badge bg-success px-3 py-1 rounded-pill"><i class="fa-solid fa-bomb me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'andar'))
                                    <span class="badge bg-danger px-3 py-1 rounded-pill"><i class="fa-solid fa-arrow-left me-1"></i> ANDAR</span>
                                @elseif(str_contains($targetLower, 'bahar'))
                                    <span class="badge bg-primary px-3 py-1 rounded-pill"><i class="fa-solid fa-arrow-right me-1"></i> BAHAR</span>
                                @elseif(str_contains($targetLower, 'green'))
                                    <span class="badge bg-success px-3 py-1 rounded-pill"><i class="fa-solid fa-circle me-1" style="font-size:0.6rem;"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'red'))
                                    <span class="badge bg-danger px-3 py-1 rounded-pill"><i class="fa-solid fa-circle me-1" style="font-size:0.6rem;"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'violet'))
                                    <span class="badge px-3 py-1 rounded-pill" style="background: #8b5cf6; color: #fff;"><i class="fa-solid fa-circle me-1" style="font-size:0.6rem;"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'big'))
                                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill"><i class="fa-solid fa-up-long me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'small'))
                                    <span class="badge bg-info text-dark px-3 py-1 rounded-pill"><i class="fa-solid fa-down-long me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'cards'))
                                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill"><i class="fa-solid fa-layer-group me-1"></i> {{ $betTarget }}</span>
                                @elseif($targetLower === 'dragon' || str_starts_with($targetLower, 'dragon'))
                                    <span class="badge bg-danger text-white px-3 py-1 rounded-pill fw-bold"><i class="fa-solid fa-dragon me-1"></i> {{ $betTarget }}</span>
                                @elseif($targetLower === 'tiger' || str_starts_with($targetLower, 'tiger'))
                                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold"><i class="fa-solid fa-fire me-1"></i> {{ $betTarget }}</span>
                                @elseif($targetLower === 'tie' || str_starts_with($targetLower, 'tie'))
                                    <span class="badge bg-success text-white px-3 py-1 rounded-pill fw-bold"><i class="fa-solid fa-equals me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'player a') || str_contains($targetLower, 'player_a'))
                                    <span class="badge bg-primary text-white px-3 py-1 rounded-pill fw-bold"><i class="fa-solid fa-crown me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'player b') || str_contains($targetLower, 'player_b'))
                                    <span class="badge bg-danger text-white px-3 py-1 rounded-pill fw-bold"><i class="fa-solid fa-crown me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'pair plus') || str_contains($targetLower, 'pair_plus'))
                                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold"><i class="fa-solid fa-star me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'ring'))
                                    <span class="badge bg-info text-dark px-3 py-1 rounded-pill fw-bold"><i class="fa-solid fa-compact-disc me-1"></i> {{ $betTarget }}</span>
                                @elseif(str_contains($targetLower, 'back') || str_contains($targetLower, 'lay'))
                                    <span class="badge bg-primary text-white px-3 py-1 rounded-pill fw-bold"><i class="fa-solid fa-baseball-bat-ball me-1"></i> {{ $betTarget }}</span>
                                @else
                                    <span class="badge bg-dark px-3 py-1 rounded-pill font-monospace"><i class="fa-solid fa-hashtag me-1"></i> {{ $betTarget }}</span>
                                @endif
                            </td>
                            <td class="text-end font-monospace fw-bold text-dark fs-6">
                                ₹{{ number_format($amount, 2) }}
                            </td>
                            <td class="text-center">
                                @if($status === 'won')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                                        <i class="fa-solid fa-circle-check me-1"></i> Won ({{ $multiplier }}X)
                                    </span>
                                @elseif($status === 'lost')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-bold">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> Lost
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1 fw-bold">
                                        <i class="fa-solid fa-clock me-1"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4 font-monospace fw-bold {{ $winAmount > 0 ? 'text-success fs-6' : 'text-muted' }}">
                                @if($winAmount > 0)
                                    <div>+₹{{ number_format($winAmount, 2) }}</div>
                                    @if($commission > 0)
                                        <div class="text-muted small fw-normal" style="font-size: 0.72rem;">(-₹{{ number_format($commission, 2) }} fee)</div>
                                    @endif
                                @else
                                    ₹0.00
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-inbox fs-1 mb-2 d-block text-secondary opacity-50"></i>
                                No bet records found matching the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bets->hasPages())
            <div class="card-footer bg-light bg-opacity-50 border-top py-3 px-4">
                {{ $bets->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
