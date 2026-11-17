@extends('layouts.app')

@section('title', 'My Bets & Activity Hub - Lottery Portal')

@section('content')
<div class="container-xl py-4">

    <!-- Header & Action Row -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-white d-flex align-items-center gap-2">
                <span class="rounded-circle d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-20 text-info" style="width: 44px; height: 44px; font-size: 1.25rem; border: 1px solid rgba(56, 189, 248, 0.3);">
                    <i class="fa-solid fa-receipt"></i>
                </span>
                <span>My Bets & Activity Hub</span>
            </h3>
            <p class="text-light opacity-75 small mb-0">Live centralized tracking of your bets across <strong class="text-white">2-Digit Lottery</strong>, <strong class="text-white">WinGo Color</strong>, <strong class="text-white">Andar Bahar</strong>, <strong class="text-white">Aviator Crash</strong> & <strong class="text-white">Roulette 36x</strong>, plus Debit & Credit ledger.</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Place New Bet Dropdown Selector -->
            <div class="dropdown">
                <button class="btn btn-primary rounded-pill px-3 py-2 fw-bold shadow-sm dropdown-toggle d-inline-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none;">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Place New Bet</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2 p-2" style="min-width: 250px;">
                    <li><span class="dropdown-header text-uppercase small fw-bold text-muted">Select Game to Play</span></li>
                    <li>
                        <a class="dropdown-item py-2 rounded-2 d-flex align-items-center gap-3 mb-1" href="{{ route('lottery.index') }}#betSlipForm">
                            <span class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 36px; height: 36px;">
                                <i class="fa-solid fa-dice"></i>
                            </span>
                            <div>
                                <div class="fw-bold text-dark">2-Digit Lottery</div>
                                <div class="small text-muted" style="font-size: 0.75rem;">Hourly Slot Draws (XA–XJ) • 90X</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 rounded-2 d-flex align-items-center gap-3 mb-1" href="{{ route('wingo.index') }}">
                            <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background: linear-gradient(135deg, #ec4899, #8b5cf6);">
                                <i class="fa-solid fa-gamepad"></i>
                            </span>
                            <div>
                                <div class="fw-bold text-dark">WinGo Color Game</div>
                                <div class="small text-muted" style="font-size: 0.75rem;">30s, 1m, 3m, 5m • Colors & Numbers</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 rounded-2 d-flex align-items-center gap-3 mb-1" href="{{ route('andar-bahar.index') }}">
                            <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background: linear-gradient(135deg, #f59e0b, #b45309);">
                                <i class="fa-solid fa-diamond"></i>
                            </span>
                            <div>
                                <div class="fw-bold text-dark">Andar Bahar Live</div>
                                <div class="small text-muted" style="font-size: 0.75rem;">50:50 Card Battle • 1.95X Payout</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 rounded-2 d-flex align-items-center gap-3 mb-1" href="{{ route('aviator.index') }}">
                            <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background: linear-gradient(135deg, #e11d48, #f97316);">
                                <i class="fa-solid fa-plane-departure"></i>
                            </span>
                            <div>
                                <div class="fw-bold text-dark">Aviator Crash Live</div>
                                <div class="small text-muted" style="font-size: 0.75rem;">Next-Gen Multiplier • Cashout anytime</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 rounded-2 d-flex align-items-center gap-3" href="{{ route('mines.index') }}">
                            <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background: linear-gradient(135deg, #10b981, #059669);">
                                <i class="fa-solid fa-bomb"></i>
                            </span>
                            <div>
                                <div class="fw-bold text-dark">Mines 5x5 Live</div>
                                <div class="small text-muted" style="font-size: 0.75rem;">Hypergeometric Multipliers • Instant Cashout</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 rounded-2 d-flex align-items-center gap-3" href="{{ route('vortex.index') }}">
                            <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background: linear-gradient(135deg, #06b6d4, #0284c7);">
                                <i class="fa-solid fa-arrows-spin"></i>
                            </span>
                            <div>
                                <div class="fw-bold text-dark">Vortex Concentric Wheel</div>
                                <div class="small text-muted" style="font-size: 0.75rem;">3-Tier Rings • Up to 200X & 50% Part Cashout</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 rounded-2 d-flex align-items-center gap-3 mb-1" href="{{ route('teen-patti.index') }}">
                            <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background: linear-gradient(135deg, #059669, #064e3b);">
                                <i class="fa-solid fa-heart"></i>
                            </span>
                            <div>
                                <div class="fw-bold text-dark">20-20 Teen Patti</div>
                                <div class="small text-muted" style="font-size: 0.75rem;">3-Card Indian Poker • Player A vs B • 25s Action</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 rounded-2 d-flex align-items-center gap-3" href="{{ route('roulette.index') }}">
                            <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background: linear-gradient(135deg, #f59e0b, #dc2626);">
                                <i class="fa-solid fa-dharmachakra"></i>
                            </span>
                            <div>
                                <div class="fw-bold text-dark">European Roulette 36x</div>
                                <div class="small text-muted" style="font-size: 0.75rem;">Single Zero • Numbers & Outside Bets • 36X Payout</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 rounded-2 d-flex align-items-center gap-3" href="{{ route('horse-racing.index') }}">
                            <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background: linear-gradient(135deg, #10b981, #047857);">
                                <i class="fa-solid fa-horse-head"></i>
                            </span>
                            <div>
                                <div class="fw-bold text-dark">Live Horse Racing Derby</div>
                                <div class="small text-muted" style="font-size: 0.75rem;">6 Thoroughbreds • Real-time Turf Sprint • Live Odds</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 rounded-2 d-flex align-items-center gap-3" href="{{ route('dragon-tiger.index') }}">
                            <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background: linear-gradient(135deg, #dc2626, #2563eb);">
                                <i class="fa-solid fa-dragon"></i>
                            </span>
                            <div>
                                <div class="fw-bold text-dark">Dragon Tiger Live</div>
                                <div class="small text-muted" style="font-size: 0.75rem;">Dragon vs Tiger • Highest Card Wins • 25s Action</div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Quick Wallet Shortcuts -->
            <a href="{{ route('wallet.deposit') }}" class="btn btn-success text-white rounded-pill px-3 py-2 fw-bold btn-sm shadow-sm" style="background: #10b981; border: none;">
                <i class="fa-solid fa-arrow-down me-1"></i> Deposit
            </a>
            <a href="{{ route('wallet.withdraw') }}" class="btn rounded-pill px-3 py-2 fw-bold btn-sm shadow-sm" style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.5); color: #fbbf24;">
                <i class="fa-solid fa-arrow-up me-1"></i> Withdraw
            </a>
        </div>
    </div>

    <!-- Personal Overview Stats Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Bets Placed -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #0284c7 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Total Bets Placed</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($stats['total_bets'] ?? 0) }}</div>
                        <div class="small text-muted mt-1" style="font-size: 0.78rem;">
                            Lottery: <strong>{{ number_format($stats['lottery_count'] ?? 0) }}</strong> | WinGo: <strong>{{ number_format($stats['wingo_count'] ?? 0) }}</strong> | AB: <strong>{{ number_format($stats['andar_count'] ?? 0) }}</strong> | Aviator: <strong>{{ number_format($stats['aviator_count'] ?? 0) }}</strong> | Mines: <strong>{{ number_format($stats['mines_count'] ?? 0) }}</strong> | Vortex: <strong>{{ number_format($stats['vortex_count'] ?? 0) }}</strong> | TP: <strong>{{ number_format($stats['teen_patti_count'] ?? 0) }}</strong> | Roulette: <strong>{{ number_format($stats['roulette_count'] ?? 0) }}</strong> | Derby: <strong>{{ number_format($stats['horse_racing_count'] ?? 0) }}</strong>
                        </div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(2, 132, 199, 0.12); color: #0284c7; font-size: 1.3rem;">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Total Wagered -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #3b82f6 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Total Wagered</div>
                        <div class="fs-4 fw-bold text-primary mt-1 font-monospace">₹{{ number_format($stats['total_wagered'] ?? 0, 2) }}</div>
                        <div class="small text-muted mt-1" style="font-size: 0.78rem;">
                            Debited on bets across all rounds
                        </div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(59, 130, 246, 0.12); color: #3b82f6; font-size: 1.3rem;">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Total Winnings Won -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff; border-left: 4px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Total Winnings Won</div>
                        <div class="fs-4 fw-bold text-success mt-1 font-monospace">+₹{{ number_format($stats['total_won'] ?? 0, 2) }}</div>
                        <div class="small text-muted mt-1" style="font-size: 0.78rem;">
                            Credited directly to wallet
                        </div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(16, 185, 129, 0.12); color: #10b981; font-size: 1.3rem;">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Available Wallet Balance -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-light opacity-75 small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Current Balance</div>
                        <div class="fs-4 fw-bold text-warning mt-1 font-monospace">₹{{ number_format($user->balance, 2) }}</div>
                        <div class="small text-white-50 mt-1" style="font-size: 0.78rem;">
                            Ready to wager or withdraw
                        </div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(245, 158, 11, 0.2); color: #f59e0b; font-size: 1.3rem;">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Hub Tabs: All Games, 2-Digit, WinGo, Andar Bahar, Aviator, Debit & Credit -->
    <div class="card border-0 shadow-sm rounded-4 p-2 mb-4 bg-white">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <!-- Game Switcher Tabs -->
            <ul class="nav nav-pills gap-1 flex-wrap">
                <!-- All Games -->
                <li class="nav-item">
                    <a href="{{ route('bets.history', ['game' => 'all', 'tab' => 'bets', 'status' => 'all']) }}" class="nav-link rounded-pill fw-bold px-3 py-2 {{ ($tab ?? 'bets') === 'bets' && ($game ?? 'all') === 'all' ? 'active bg-primary text-white shadow-sm' : 'text-secondary' }}">
                        <i class="fa-solid fa-layer-group me-1"></i> All Games Bets
                    </a>
                </li>

                <!-- 2-Digit Lottery -->
                <li class="nav-item">
                    <a href="{{ route('bets.history', ['game' => 'lottery', 'tab' => 'bets', 'status' => 'all']) }}" class="nav-link rounded-pill fw-bold px-3 py-2 {{ ($tab ?? 'bets') === 'bets' && ($game ?? '') === 'lottery' ? 'active bg-primary text-white shadow-sm' : 'text-secondary' }}">
                        <i class="fa-solid fa-dice me-1 text-info"></i> 2-Digit Lottery
                        <span class="badge bg-light text-dark ms-1 rounded-pill">{{ $stats['lottery_count'] ?? 0 }}</span>
                    </a>
                </li>

                <!-- WinGo Color Game -->
                <li class="nav-item">
                    <a href="{{ route('bets.history', ['game' => 'wingo', 'tab' => 'bets', 'status' => 'all']) }}" class="nav-link rounded-pill fw-bold px-3 py-2 {{ ($tab ?? 'bets') === 'bets' && str_starts_with($game ?? '', 'wingo') ? 'active text-white shadow-sm' : 'text-secondary' }}" style="{{ ($tab ?? 'bets') === 'bets' && str_starts_with($game ?? '', 'wingo') ? 'background: linear-gradient(135deg, #ec4899, #8b5cf6);' : '' }}">
                        <i class="fa-solid fa-gamepad me-1"></i> WinGo Color Game
                        <span class="badge bg-light text-dark ms-1 rounded-pill">{{ $stats['wingo_count'] ?? 0 }}</span>
                    </a>
                </li>

                <!-- Andar Bahar -->
                <li class="nav-item">
                    <a href="{{ route('bets.history', ['game' => 'andar_bahar', 'tab' => 'bets', 'status' => 'all']) }}" class="nav-link rounded-pill fw-bold px-3 py-2 {{ ($tab ?? 'bets') === 'bets' && ($game ?? '') === 'andar_bahar' ? 'active text-white shadow-sm' : 'text-secondary' }}" style="{{ ($tab ?? 'bets') === 'bets' && ($game ?? '') === 'andar_bahar' ? 'background: linear-gradient(135deg, #f59e0b, #b45309);' : '' }}">
                        <i class="fa-solid fa-diamond me-1 text-warning"></i> Andar Bahar
                        <span class="badge bg-light text-dark ms-1 rounded-pill">{{ $stats['andar_count'] ?? 0 }}</span>
                    </a>
                </li>

                <!-- Aviator Crash -->
                <li class="nav-item">
                    <a href="{{ route('bets.history', ['game' => 'aviator', 'tab' => 'bets', 'status' => 'all']) }}" class="nav-link rounded-pill fw-bold px-3 py-2 {{ ($tab ?? 'bets') === 'bets' && ($game ?? '') === 'aviator' ? 'active text-white shadow-sm' : 'text-secondary' }}" style="{{ ($tab ?? 'bets') === 'bets' && ($game ?? '') === 'aviator' ? 'background: linear-gradient(135deg, #e11d48, #f97316);' : '' }}">
                        <i class="fa-solid fa-plane-departure me-1 text-danger"></i> Aviator Crash
                        <span class="badge bg-light text-dark ms-1 rounded-pill">{{ $stats['aviator_count'] ?? 0 }}</span>
                    </a>
                </li>

                <!-- Mines 5x5 Arcade -->
                <li class="nav-item">
                    <a href="{{ route('bets.history', ['game' => 'mines', 'tab' => 'bets', 'status' => 'all']) }}" class="nav-link rounded-pill fw-bold px-3 py-2 {{ ($tab ?? 'bets') === 'bets' && ($game ?? '') === 'mines' ? 'active text-white shadow-sm' : 'text-secondary' }}" style="{{ ($tab ?? 'bets') === 'bets' && ($game ?? '') === 'mines' ? 'background: linear-gradient(135deg, #10b981, #059669);' : '' }}">
                        <i class="fa-solid fa-bomb me-1 text-success"></i> Mines 5x5
                        <span class="badge bg-light text-dark ms-1 rounded-pill">{{ $stats['mines_count'] ?? 0 }}</span>
                    </a>
                </li>

                <!-- Vortex Concentric Wheel -->
                <li class="nav-item">
                    <a href="{{ route('bets.history', ['game' => 'vortex', 'tab' => 'bets', 'status' => 'all']) }}" class="nav-link rounded-pill fw-bold px-3 py-2 {{ ($tab ?? 'bets') === 'bets' && ($game ?? '') === 'vortex' ? 'active text-white shadow-sm' : 'text-secondary' }}" style="{{ ($tab ?? 'bets') === 'bets' && ($game ?? '') === 'vortex' ? 'background: linear-gradient(135deg, #06b6d4, #0284c7);' : '' }}">
                        <i class="fa-solid fa-arrows-spin me-1 text-info"></i> Vortex Wheel
                        <span class="badge bg-light text-dark ms-1 rounded-pill">{{ $stats['vortex_count'] ?? 0 }}</span>
                    </a>
                </li>

                <!-- 20-20 Teen Patti -->
                <li class="nav-item">
                    <a href="{{ route('bets.history', ['game' => 'teen_patti', 'tab' => 'bets', 'status' => 'all']) }}" class="nav-link rounded-pill fw-bold px-3 py-2 {{ ($tab ?? 'bets') === 'bets' && ($game ?? '') === 'teen_patti' ? 'active text-white shadow-sm' : 'text-secondary' }}" style="{{ ($tab ?? 'bets') === 'bets' && ($game ?? '') === 'teen_patti' ? 'background: linear-gradient(135deg, #059669, #064e3b);' : '' }}">
                        <i class="fa-solid fa-heart me-1 text-danger"></i> Teen Patti
                        <span class="badge bg-light text-dark ms-1 rounded-pill">{{ $stats['teen_patti_count'] ?? 0 }}</span>
                    </a>
                </li>

                <!-- European Roulette 36x -->
                <li class="nav-item">
                    <a href="{{ route('bets.history', ['game' => 'roulette', 'tab' => 'bets', 'status' => 'all']) }}" class="nav-link rounded-pill fw-bold px-3 py-2 {{ ($tab ?? 'bets') === 'bets' && ($game ?? '') === 'roulette' ? 'active text-white shadow-sm' : 'text-secondary' }}" style="{{ ($tab ?? 'bets') === 'bets' && ($game ?? '') === 'roulette' ? 'background: linear-gradient(135deg, #f59e0b, #dc2626);' : '' }}">
                        <i class="fa-solid fa-dharmachakra me-1 text-warning"></i> Roulette 36x
                        <span class="badge bg-light text-dark ms-1 rounded-pill">{{ $stats['roulette_count'] ?? 0 }}</span>
                    </a>
                </li>

                <!-- Live Horse Racing Derby -->
                <li class="nav-item">
                    <a href="{{ route('bets.history', ['game' => 'horse_racing', 'tab' => 'bets', 'status' => 'all']) }}" class="nav-link rounded-pill fw-bold px-3 py-2 {{ ($tab ?? 'bets') === 'bets' && ($game ?? '') === 'horse_racing' ? 'active text-white shadow-sm' : 'text-secondary' }}" style="{{ ($tab ?? 'bets') === 'bets' && ($game ?? '') === 'horse_racing' ? 'background: linear-gradient(135deg, #10b981, #047857);' : '' }}">
                        <i class="fa-solid fa-horse-head me-1 text-success"></i> Derby Racing
                        <span class="badge bg-light text-dark ms-1 rounded-pill">{{ $stats['horse_racing_count'] ?? 0 }}</span>
                    </a>
                </li>

                <!-- Dragon Tiger Live -->
                <li class="nav-item">
                    <a href="{{ route('bets.history', ['game' => 'dragon_tiger', 'tab' => 'bets', 'status' => 'all']) }}" class="nav-link rounded-pill fw-bold px-3 py-2 {{ ($tab ?? 'bets') === 'bets' && ($game ?? '') === 'dragon_tiger' ? 'active text-white shadow-sm' : 'text-secondary' }}" style="{{ ($tab ?? 'bets') === 'bets' && ($game ?? '') === 'dragon_tiger' ? 'background: linear-gradient(135deg, #dc2626, #2563eb);' : '' }}">
                        <i class="fa-solid fa-dragon me-1 text-danger"></i> Dragon Tiger
                        <span class="badge bg-light text-dark ms-1 rounded-pill">{{ $stats['dragon_tiger_count'] ?? 0 }}</span>
                    </a>
                </li>

                <!-- Debit & Credit History (Passbook Ledger) -->
                <li class="nav-item">
                    <a href="{{ route('bets.history', ['tab' => 'transactions', 'flow' => 'all']) }}" class="nav-link rounded-pill fw-bold px-3 py-2 {{ ($tab ?? '') === 'transactions' ? 'active bg-dark text-white shadow-sm' : 'text-secondary' }}">
                        <i class="fa-solid fa-clock-rotate-left me-1 text-warning"></i> Debit & Credit History
                    </a>
                </li>
            </ul>

            <!-- Play Game Quick Direct Button -->
            <div>
                @if(str_starts_with($game ?? '', 'wingo'))
                    <a href="{{ route('wingo.index') }}" class="btn btn-sm rounded-pill px-3 fw-bold text-white shadow-sm" style="background: linear-gradient(135deg, #ec4899, #8b5cf6);">
                        <i class="fa-solid fa-play me-1"></i> Play WinGo Now
                    </a>
                @elseif(($game ?? '') === 'andar_bahar')
                    <a href="{{ route('andar-bahar.index') }}" class="btn btn-sm rounded-pill px-3 fw-bold text-white shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #b45309);">
                        <i class="fa-solid fa-play me-1"></i> Play Andar Bahar
                    </a>
                @elseif(($game ?? '') === 'aviator')
                    <a href="{{ route('aviator.index') }}" class="btn btn-sm rounded-pill px-3 fw-bold text-white shadow-sm" style="background: linear-gradient(135deg, #e11d48, #f97316);">
                        <i class="fa-solid fa-plane-departure me-1"></i> Play Aviator Now
                    </a>
                @elseif(($game ?? '') === 'mines')
                    <a href="{{ route('mines.index') }}" class="btn btn-sm rounded-pill px-3 fw-bold text-white shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fa-solid fa-bomb me-1"></i> Play Mines Now
                    </a>
                @elseif(($game ?? '') === 'vortex')
                    <a href="{{ route('vortex.index') }}" class="btn btn-sm rounded-pill px-3 fw-bold text-white shadow-sm" style="background: linear-gradient(135deg, #06b6d4, #0284c7);">
                        <i class="fa-solid fa-arrows-spin me-1"></i> Play Vortex Now
                    </a>
                @elseif(($game ?? '') === 'teen_patti')
                    <a href="{{ route('teen-patti.index') }}" class="btn btn-sm rounded-pill px-3 fw-bold text-white shadow-sm" style="background: linear-gradient(135deg, #059669, #064e3b);">
                        <i class="fa-solid fa-heart me-1"></i> Play Teen Patti Now
                    </a>
                @elseif(($game ?? '') === 'roulette')
                    <a href="{{ route('roulette.index') }}" class="btn btn-sm rounded-pill px-3 fw-bold text-white shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #dc2626);">
                        <i class="fa-solid fa-dharmachakra me-1"></i> Play Roulette Now
                    </a>
                @elseif(($game ?? '') === 'horse_racing')
                    <a href="{{ route('horse-racing.index') }}" class="btn btn-sm rounded-pill px-3 fw-bold text-white shadow-sm" style="background: linear-gradient(135deg, #10b981, #047857);">
                        <i class="fa-solid fa-horse-head me-1"></i> Play Derby Now
                    </a>
                @elseif(($game ?? '') === 'dragon_tiger')
                    <a href="{{ route('dragon-tiger.index') }}" class="btn btn-sm rounded-pill px-3 fw-bold text-white shadow-sm" style="background: linear-gradient(135deg, #dc2626, #2563eb);">
                        <i class="fa-solid fa-dragon me-1"></i> Play Dragon Tiger
                    </a>
                @else
                    <a href="{{ route('lottery.index') }}#betSlipForm" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                        <i class="fa-solid fa-play me-1"></i> Play 2-Digit Slot
                    </a>
                @endif
            </div>
        </div>

        <!-- Sub-Mode Filter for WinGo (30s, 1m, 3m, 5m) if on WinGo -->
        @if(($tab ?? 'bets') === 'bets' && str_starts_with($game ?? '', 'wingo'))
            <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top px-2">
                <span class="small text-muted fw-bold">Duration Mode:</span>
                <a href="{{ route('bets.history', ['game' => 'wingo', 'tab' => 'bets', 'status' => $status ?? 'all']) }}" class="btn btn-sm rounded-pill {{ ($game ?? '') === 'wingo' ? 'btn-dark' : 'btn-outline-secondary' }} py-0 px-2" style="font-size: 0.78rem;">All Modes</a>
                <a href="{{ route('bets.history', ['game' => 'wingo_30s', 'tab' => 'bets', 'status' => $status ?? 'all']) }}" class="btn btn-sm rounded-pill {{ ($game ?? '') === 'wingo_30s' ? 'btn-danger' : 'btn-outline-danger' }} py-0 px-2" style="font-size: 0.78rem;">30s</a>
                <a href="{{ route('bets.history', ['game' => 'wingo_1m', 'tab' => 'bets', 'status' => $status ?? 'all']) }}" class="btn btn-sm rounded-pill {{ ($game ?? '') === 'wingo_1m' ? 'btn-primary' : 'btn-outline-primary' }} py-0 px-2" style="font-size: 0.78rem;">1 Min</a>
                <a href="{{ route('bets.history', ['game' => 'wingo_3m', 'tab' => 'bets', 'status' => $status ?? 'all']) }}" class="btn btn-sm rounded-pill {{ ($game ?? '') === 'wingo_3m' ? 'btn-info text-dark' : 'btn-outline-info text-dark' }} py-0 px-2" style="font-size: 0.78rem;">3 Min</a>
                <a href="{{ route('bets.history', ['game' => 'wingo_5m', 'tab' => 'bets', 'status' => $status ?? 'all']) }}" class="btn btn-sm rounded-pill {{ ($game ?? '') === 'wingo_5m' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }} py-0 px-2" style="font-size: 0.78rem;">5 Min</a>
            </div>
        @endif
    </div>

    <!-- TAB 1: BETS LISTING (Unified or Game Specific) -->
    @if(($tab ?? 'bets') === 'bets')
        <!-- Status Filter Pills -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('bets.history', array_merge(request()->query(), ['status' => 'all'])) }}" class="btn btn-sm rounded-pill fw-bold {{ ($currentFilter ?? 'all') === 'all' ? 'btn-light text-dark' : 'btn-outline-light text-white' }}">
                    All Status
                </a>
                <a href="{{ route('bets.history', array_merge(request()->query(), ['status' => 'pending'])) }}" class="btn btn-sm rounded-pill fw-bold {{ ($currentFilter ?? '') === 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning text-warning' }}">
                    <i class="fa-solid fa-hourglass-half me-1"></i> Pending
                </a>
                <a href="{{ route('bets.history', array_merge(request()->query(), ['status' => 'won'])) }}" class="btn btn-sm rounded-pill fw-bold {{ ($currentFilter ?? '') === 'won' ? 'btn-success text-white' : 'btn-outline-success text-success' }}">
                    <i class="fa-solid fa-trophy me-1"></i> Won
                </a>
                <a href="{{ route('bets.history', array_merge(request()->query(), ['status' => 'lost'])) }}" class="btn btn-sm rounded-pill fw-bold {{ ($currentFilter ?? '') === 'lost' ? 'btn-danger text-white' : 'btn-outline-danger text-danger' }}">
                    <i class="fa-solid fa-xmark me-1"></i> Lost
                </a>
            </div>

            <div class="text-light opacity-80 small">
                Showing <strong class="text-white">{{ $bets->count() }}</strong> of <strong class="text-white">{{ $bets->total() }}</strong> total bets
            </div>
        </div>

        <!-- Bets Table Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th class="ps-3">Game</th>
                            <th>Bet ID</th>
                            <th>Draw / Period</th>
                            <th>Chosen Selection</th>
                            <th>Stake Amount</th>
                            <th>Multiplier</th>
                            <th>Winnings</th>
                            <th>Status</th>
                            <th>Placed Time</th>
                            <th class="pe-3 text-end">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bets as $b)
                            <tr>
                                <!-- Game Indicator Badge -->
                                <td class="ps-3">
                                    @if(isset($b->game_code))
                                        @if($b->game_code === 'lottery')
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill fw-bold">
                                                <i class="fa-solid fa-dice me-1"></i> 2-Digit Lottery
                                            </span>
                                        @elseif($b->game_code === 'andar_bahar')
                                            <span class="badge text-white px-2 py-1 rounded-pill fw-bold" style="background: linear-gradient(135deg, #f59e0b, #b45309); font-size: 0.75rem;">
                                                <i class="fa-solid fa-diamond me-1"></i> Andar Bahar
                                            </span>
                                        @elseif($b->game_code === 'aviator')
                                            <span class="badge text-white px-2 py-1 rounded-pill fw-bold" style="background: linear-gradient(135deg, #e11d48, #f97316); font-size: 0.75rem;">
                                                <i class="fa-solid fa-plane-departure me-1"></i> Aviator Crash
                                            </span>
                                        @elseif($b->game_code === 'mines')
                                            <span class="badge text-white px-2 py-1 rounded-pill fw-bold" style="background: linear-gradient(135deg, #10b981, #059669); font-size: 0.75rem;">
                                                <i class="fa-solid fa-bomb me-1"></i> Mines 5x5
                                            </span>
                                        @elseif($b->game_code === 'vortex')
                                            <span class="badge text-white px-2 py-1 rounded-pill fw-bold" style="background: linear-gradient(135deg, #06b6d4, #0284c7); font-size: 0.75rem;">
                                                <i class="fa-solid fa-arrows-spin me-1"></i> Vortex Wheel
                                            </span>
                                        @elseif($b->game_code === 'teen_patti')
                                            <span class="badge text-white px-2 py-1 rounded-pill fw-bold" style="background: linear-gradient(135deg, #059669, #064e3b); font-size: 0.75rem;">
                                                <i class="fa-solid fa-heart me-1"></i> Teen Patti
                                            </span>
                                        @elseif($b->game_code === 'roulette')
                                            <span class="badge text-white px-2 py-1 rounded-pill fw-bold" style="background: linear-gradient(135deg, #f59e0b, #dc2626); font-size: 0.75rem;">
                                                <i class="fa-solid fa-dharmachakra me-1"></i> Roulette 36x
                                            </span>
                                        @elseif($b->game_code === 'horse_racing')
                                            <span class="badge text-white px-2 py-1 rounded-pill fw-bold" style="background: linear-gradient(135deg, #10b981, #047857); font-size: 0.75rem;">
                                                <i class="fa-solid fa-horse-head me-1"></i> Derby Racing
                                            </span>
                                        @else
                                            <span class="badge text-white px-2 py-1 rounded-pill fw-bold" style="background: linear-gradient(135deg, #ec4899, #8b5cf6); font-size: 0.75rem;">
                                                <i class="fa-solid fa-gamepad me-1"></i> {{ $b->game_name }}
                                            </span>
                                        @endif
                                    @elseif(($game ?? '') === 'lottery')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill fw-bold">
                                            <i class="fa-solid fa-dice me-1"></i> 2-Digit Lottery
                                        </span>
                                    @elseif(($game ?? '') === 'andar_bahar')
                                        <span class="badge text-white px-2 py-1 rounded-pill fw-bold" style="background: linear-gradient(135deg, #f59e0b, #b45309); font-size: 0.75rem;">
                                            <i class="fa-solid fa-diamond me-1"></i> Andar Bahar
                                        </span>
                                    @elseif(($game ?? '') === 'aviator')
                                        <span class="badge text-white px-2 py-1 rounded-pill fw-bold" style="background: linear-gradient(135deg, #e11d48, #f97316); font-size: 0.75rem;">
                                            <i class="fa-solid fa-plane-departure me-1"></i> Aviator Crash
                                        </span>
                                    @elseif(($game ?? '') === 'mines')
                                        <span class="badge text-white px-2 py-1 rounded-pill fw-bold" style="background: linear-gradient(135deg, #10b981, #059669); font-size: 0.75rem;">
                                            <i class="fa-solid fa-bomb me-1"></i> Mines 5x5
                                        </span>
                                    @elseif(($game ?? '') === 'vortex')
                                        <span class="badge text-white px-2 py-1 rounded-pill fw-bold" style="background: linear-gradient(135deg, #06b6d4, #0284c7); font-size: 0.75rem;">
                                            <i class="fa-solid fa-arrows-spin me-1"></i> Vortex Wheel
                                        </span>
                                    @elseif(($game ?? '') === 'teen_patti')
                                        <span class="badge text-white px-2 py-1 rounded-pill fw-bold" style="background: linear-gradient(135deg, #059669, #064e3b); font-size: 0.75rem;">
                                            <i class="fa-solid fa-heart me-1"></i> Teen Patti
                                        </span>
                                    @elseif(($game ?? '') === 'roulette')
                                        <span class="badge text-white px-2 py-1 rounded-pill fw-bold" style="background: linear-gradient(135deg, #f59e0b, #dc2626); font-size: 0.75rem;">
                                            <i class="fa-solid fa-dharmachakra me-1"></i> Roulette 36x
                                        </span>
                                    @elseif(($game ?? '') === 'horse_racing')
                                        <span class="badge text-white px-2 py-1 rounded-pill fw-bold" style="background: linear-gradient(135deg, #10b981, #047857); font-size: 0.75rem;">
                                            <i class="fa-solid fa-horse-head me-1"></i> Derby Racing
                                        </span>
                                    @elseif(($game ?? '') === 'dragon_tiger')
                                        <span class="badge text-white px-2 py-1 rounded-pill fw-bold" style="background: linear-gradient(135deg, #dc2626, #2563eb); font-size: 0.75rem;">
                                            <i class="fa-solid fa-dragon me-1"></i> Dragon Tiger
                                        </span>
                                    @else
                                        <span class="badge text-white px-2 py-1 rounded-pill fw-bold" style="background: linear-gradient(135deg, #ec4899, #8b5cf6); font-size: 0.75rem;">
                                            <i class="fa-solid fa-gamepad me-1"></i> WinGo {{ strtoupper($b->game_type ?? '') }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Bet ID -->
                                <td class="font-monospace small text-muted">
                                    #{{ $b->id }}
                                </td>

                                <!-- Round / Period Info -->
                                <td>
                                    @if(isset($b->game_code) && $b->game_code === 'aviator')
                                        <div class="fw-bold text-dark"><i class="fa-solid fa-hashtag text-danger me-1"></i>{{ $b->round_id }}</div>
                                        @if(!empty($b->round_crash_multiplier))
                                            <span class="badge bg-light text-secondary border font-monospace mt-1" style="font-size: 0.72rem;">
                                                <i class="fa-solid fa-burst text-danger me-1"></i>Crashed: {{ number_format((float) $b->round_crash_multiplier, 2) }}x
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border font-monospace mt-1" style="font-size: 0.72rem;">Crash Arena</span>
                                        @endif
                                    @elseif(($game ?? '') === 'aviator')
                                        <div class="fw-bold text-dark"><i class="fa-solid fa-hashtag text-danger me-1"></i>{{ $b->round_id }}</div>
                                        @if(!empty($b->round?->crash_multiplier))
                                            <span class="badge bg-light text-secondary border font-monospace mt-1" style="font-size: 0.72rem;">
                                                <i class="fa-solid fa-burst text-danger me-1"></i>Crashed: {{ number_format((float) $b->round->crash_multiplier, 2) }}x
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border font-monospace mt-1" style="font-size: 0.72rem;">Crash Arena</span>
                                        @endif
                                    @elseif((isset($b->game_code) && $b->game_code === 'mines') || (($game ?? '') === 'mines'))
                                        <div class="fw-bold text-dark"><i class="fa-solid fa-hashtag text-success me-1"></i>{{ substr($b->game_id ?? (string)$b->id, -8) }}</div>
                                        <span class="badge bg-light text-secondary border font-monospace mt-1" style="font-size: 0.72rem;">
                                            <i class="fa-solid fa-bomb text-danger me-1"></i>{{ $b->mines_count ?? 3 }} Mines
                                        </span>
                                    @elseif((isset($b->game_code) && $b->game_code === 'vortex') || (($game ?? '') === 'vortex'))
                                        <div class="fw-bold text-dark"><i class="fa-solid fa-hashtag text-info me-1"></i>{{ substr($b->game_id ?? (string)$b->id, -8) }}</div>
                                        <span class="badge bg-light text-secondary border font-monospace mt-1" style="font-size: 0.72rem;">
                                            @if(($b->current_ring ?? 1) == 1)
                                                <i class="fa-solid fa-droplet text-info me-1"></i>Ring 1 (Water)
                                            @elseif(($b->current_ring ?? 1) == 2)
                                                <i class="fa-solid fa-leaf text-success me-1"></i>Ring 2 (Earth)
                                            @else
                                                <i class="fa-solid fa-fire text-danger me-1"></i>Ring 3 (Fire)
                                            @endif
                                        </span>
                                    @elseif((isset($b->game_code) && $b->game_code === 'teen_patti') || (($game ?? '') === 'teen_patti'))
                                        <div class="fw-bold text-dark"><i class="fa-solid fa-hashtag text-danger me-1"></i>{{ $b->round_id ?? ($b->round_info ?? 'Live') }}</div>
                                        <span class="badge bg-light text-secondary border font-monospace mt-1" style="font-size: 0.72rem;">
                                            @if(!empty($b->round->winning_rank_title ?? null))
                                                <i class="fa-solid fa-crown text-warning me-1"></i>{{ $b->round->winning_rank_title }}
                                            @elseif(!empty($b->round_info ?? null))
                                                {{ $b->round_info }}
                                            @else
                                                <i class="fa-solid fa-heart text-danger me-1"></i>3-Card Arena
                                            @endif
                                        </span>
                                    @elseif((isset($b->game_code) && $b->game_code === 'roulette') || (($game ?? '') === 'roulette'))
                                        <div class="fw-bold text-dark"><i class="fa-solid fa-hashtag text-warning me-1"></i>{{ $b->round_id }}</div>
                                        <span class="badge bg-light text-secondary border font-monospace mt-1" style="font-size: 0.72rem;">
                                            @if(!is_null($b->round?->winning_number ?? null))
                                                <i class="fa-solid fa-dharmachakra text-danger me-1"></i>#{{ $b->round->winning_number }} ({{ strtoupper($b->round->winning_color ?? '') }})
                                            @elseif(!empty($b->round_info ?? null))
                                                {{ $b->round_info }}
                                            @else
                                                <i class="fa-solid fa-dharmachakra text-warning me-1"></i>Live Wheel
                                            @endif
                                        </span>
                                    @elseif((isset($b->game_code) && $b->game_code === 'horse_racing') || (($game ?? '') === 'horse_racing'))
                                        <div class="fw-bold text-dark"><i class="fa-solid fa-hashtag text-success me-1"></i>{{ $b->round_id }}</div>
                                        <span class="badge bg-light text-secondary border font-monospace mt-1" style="font-size: 0.72rem;">
                                            @if(!is_null($b->round?->winner_horse_id ?? null))
                                                <i class="fa-solid fa-trophy text-warning me-1"></i>Winner: #{{ $b->round->winner_horse_id }} {{ $b->round->winner_horse_name }}
                                            @elseif(!empty($b->round_info ?? null))
                                                {{ $b->round_info }}
                                            @else
                                                <i class="fa-solid fa-horse-head text-success me-1"></i>Derby Turf
                                            @endif
                                        </span>
                                    @elseif(isset($b->round_info))
                                        <div class="fw-bold text-dark">{{ $b->round_info }}</div>
                                    @elseif(isset($b->slot_time))
                                        <div class="fw-bold text-dark">{{ $b->slot_time }}</div>
                                        <div class="text-muted small">{{ $b->result_date ? $b->result_date->format('d M Y') : '' }}</div>
                                    @elseif(isset($b->round_id))
                                        <div class="fw-bold text-dark">Round #{{ $b->round_id }}</div>
                                        <div class="text-muted small">Live Casino</div>
                                    @else
                                        <div class="fw-bold text-dark">Period #{{ $b->period_id }}</div>
                                        <div class="text-muted small">{{ strtoupper($b->game_type) }}</div>
                                    @endif
                                </td>

                                <!-- Chosen Selection -->
                                <td>
                                    @if((isset($b->game_code) && $b->game_code === 'aviator') || (($game ?? '') === 'aviator'))
                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                            <span class="badge px-2 py-1 rounded-2 text-white font-monospace" style="background: #e11d48;">
                                                <i class="fa-solid fa-gamepad me-1"></i>Panel #{{ $b->bet_slot }}
                                            </span>
                                            @if(!empty($b->auto_cashout))
                                                <span class="badge bg-dark px-2 py-1 rounded-2 font-monospace" style="font-size: 0.75rem;">
                                                    <i class="fa-solid fa-robot text-warning me-1"></i>Auto: {{ number_format((float) $b->auto_cashout, 2) }}x
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted border px-2 py-1 rounded-2" style="font-size: 0.75rem;">
                                                    Manual Cashout
                                                </span>
                                            @endif
                                        </div>
                                    @elseif((isset($b->game_code) && $b->game_code === 'mines') || (($game ?? '') === 'mines'))
                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                            <span class="badge px-2 py-1 rounded-2 text-white font-monospace" style="background: #059669;">
                                                <i class="fa-solid fa-gem me-1"></i>{{ $b->current_step ?? count($b->opened_tiles ?? []) }} Safe Coins
                                            </span>
                                            @if(!empty($b->is_jackpot_win))
                                                <span class="badge bg-warning text-dark px-2 py-1 rounded-2 fw-bold" style="font-size: 0.75rem;">
                                                    <i class="fa-solid fa-crown me-1 text-danger"></i>JACKPOT!
                                                </span>
                                            @endif
                                        </div>
                                    @elseif((isset($b->game_code) && $b->game_code === 'vortex') || (($game ?? '') === 'vortex'))
                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                            <span class="badge px-2 py-1 rounded-2 text-white font-monospace" style="background: #0284c7;">
                                                <i class="fa-solid fa-arrows-spin me-1"></i>Ring {{ $b->current_ring ?? 1 }}
                                            </span>
                                            @if(!empty($b->part_cashed_out))
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-2 fw-bold" style="font-size: 0.75rem;">
                                                    <i class="fa-solid fa-check me-1"></i>50% Part PayOut
                                                </span>
                                            @endif
                                        </div>
                                    @elseif((isset($b->game_code) && $b->game_code === 'roulette') || (($game ?? '') === 'roulette'))
                                        @php
                                            $rType = $b->bet_type ?? '';
                                            $rVal = (string) ($b->bet_value ?? '');
                                        @endphp
                                        @if($rType === 'straight')
                                            @php
                                                $num = (int)$rVal;
                                                $isZero = ($num === 0);
                                                $isRed = in_array($num, [1, 3, 5, 7, 9, 12, 14, 16, 18, 19, 21, 23, 25, 27, 30, 32, 34, 36], true);
                                                $badgeBg = $isZero ? 'background: #059669;' : ($isRed ? 'background: #dc2626;' : 'background: #1e293b;');
                                            @endphp
                                            <span class="badge px-3 py-2 rounded-3 fs-6 text-white" style="{{ $badgeBg }}">
                                                <i class="fa-solid fa-dharmachakra me-1 text-warning"></i> Straight #{{ $rVal }}
                                            </span>
                                        @elseif($rType === 'red')
                                            <span class="badge px-3 py-2 rounded-3 fs-6 text-white" style="background: #dc2626;">
                                                <i class="fa-solid fa-circle me-1"></i> RED (🔴)
                                            </span>
                                        @elseif($rType === 'black')
                                            <span class="badge px-3 py-2 rounded-3 fs-6 text-white" style="background: #1e293b; border: 1px solid #475569;">
                                                <i class="fa-solid fa-circle me-1 text-secondary"></i> BLACK (⚫)
                                            </span>
                                        @elseif(in_array($rType, ['even', 'odd', 'low', 'high']))
                                            <span class="badge bg-dark px-3 py-2 rounded-3 fs-6 text-white">
                                                {{ strtoupper($rType) }}
                                            </span>
                                        @elseif(in_array($rType, ['dozen1', 'dozen2', 'dozen3']))
                                            <span class="badge bg-warning text-dark px-3 py-2 rounded-3 fs-6 fw-bold">
                                                {{ $rType === 'dozen1' ? '1st 12' : ($rType === 'dozen2' ? '2nd 12' : '3rd 12') }}
                                            </span>
                                        @elseif(in_array($rType, ['col1', 'col2', 'col3']))
                                            <span class="badge bg-info text-dark px-3 py-2 rounded-3 fs-6 fw-bold">
                                                {{ $rType === 'col1' ? '1st Col' : ($rType === 'col2' ? '2nd Col' : '3rd Col') }}
                                            </span>
                                        @else
                                            <span class="badge bg-dark px-3 py-2 rounded-3 font-monospace fs-6">
                                                {{ $b->selection_label ?? (ucfirst($rType).': '.$rVal) }}
                                            </span>
                                        @endif
                                    @elseif((isset($b->game_code) && $b->game_code === 'horse_racing') || (($game ?? '') === 'horse_racing'))
                                        <span class="badge px-3 py-2 rounded-3 fs-6 text-white" style="background: linear-gradient(135deg, #10b981, #047857);">
                                            <i class="fa-solid fa-horse me-1 text-warning"></i> #{{ $b->horse_id ?? '' }} {{ $b->horse_name ?? '' }} ({{ strtoupper($b->bet_type ?? 'WIN') }})
                                        </span>
                                    @elseif((isset($b->game_code) && $b->game_code === 'dragon_tiger') || (($game ?? '') === 'dragon_tiger'))
                                        @php
                                            $dtType = $b->bet_type ?? '';
                                        @endphp
                                        @if($dtType === 'dragon')
                                            <span class="badge px-3 py-2 rounded-3 fs-6 text-white" style="background: linear-gradient(135deg, #dc2626, #b91c1c);"><i class="fa-solid fa-dragon me-1"></i> DRAGON (1.95X)</span>
                                        @elseif($dtType === 'tiger')
                                            <span class="badge px-3 py-2 rounded-3 fs-6 text-white" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);"><i class="fa-solid fa-paw me-1"></i> TIGER (1.95X)</span>
                                        @elseif($dtType === 'tie')
                                            <span class="badge px-3 py-2 rounded-3 fs-6 text-white" style="background: linear-gradient(135deg, #10b981, #047857);"><i class="fa-solid fa-handshake me-1"></i> TIE (9.00X)</span>
                                        @elseif($dtType === 'suited_tie')
                                            <span class="badge px-3 py-2 rounded-3 fs-6 text-dark" style="background: linear-gradient(135deg, #fbbf24, #d97706);"><i class="fa-solid fa-crown me-1"></i> SUITED TIE (50.0X)</span>
                                        @else
                                            <span class="badge bg-dark px-3 py-2 rounded-3 fs-6">{{ strtoupper(str_replace('_', ' ', $dtType)) }} (1.95X)</span>
                                        @endif
                                    @elseif(isset($b->selection_label))
                                        <span class="badge bg-dark px-3 py-2 rounded-3 font-monospace fs-6">
                                            {{ $b->selection_label }}
                                        </span>
                                    @elseif(isset($b->slot_key))
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-primary px-2 py-1 rounded-2 font-monospace">{{ $b->slot_key }}</span>
                                            <span class="badge bg-dark px-3 py-2 rounded-3 font-monospace fs-6">#{{ $b->chosen_number }}</span>
                                        </div>
                                    @elseif((isset($b->game_code) && $b->game_code === 'teen_patti') || (($game ?? '') === 'teen_patti'))
                                        @php
                                            $tpType = $b->bet_type ?? '';
                                        @endphp
                                        @if($tpType === 'player_a')
                                            <span class="badge px-3 py-2 rounded-3 fs-6 text-white" style="background: linear-gradient(135deg, #06b6d4, #0284c7);"><i class="fa-solid fa-user me-1"></i> PLAYER A (1.95X)</span>
                                        @elseif($tpType === 'player_b')
                                            <span class="badge px-3 py-2 rounded-3 fs-6 text-white" style="background: linear-gradient(135deg, #e11d48, #be123c);"><i class="fa-solid fa-user me-1"></i> PLAYER B (1.95X)</span>
                                        @elseif($tpType === 'tie')
                                            <span class="badge px-3 py-2 rounded-3 fs-6 text-dark" style="background: linear-gradient(135deg, #f59e0b, #d97706);"><i class="fa-solid fa-handshake me-1"></i> TIE (9.00X)</span>
                                        @elseif($tpType === 'pair_plus_a')
                                            <span class="badge px-3 py-2 rounded-3 fs-6 text-white" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);"><i class="fa-solid fa-crown me-1"></i> PAIR+ (A)</span>
                                        @elseif($tpType === 'pair_plus_b')
                                            <span class="badge px-3 py-2 rounded-3 fs-6 text-white" style="background: linear-gradient(135deg, #ec4899, #db2777);"><i class="fa-solid fa-crown me-1"></i> PAIR+ (B)</span>
                                        @else
                                            <span class="badge bg-dark px-3 py-2 rounded-3 fs-6">{{ strtoupper($tpType) }}</span>
                                        @endif
                                    @elseif(isset($b->round_id))
                                        @php
                                            $abSel = strtolower($b->bet_selection ?? '');
                                        @endphp
                                        @if($abSel === 'andar')
                                            <span class="badge bg-danger px-3 py-2 rounded-3 fs-6"><i class="fa-solid fa-arrow-left me-1"></i> ANDAR (अंदर)</span>
                                        @elseif($abSel === 'bahar')
                                            <span class="badge bg-primary px-3 py-2 rounded-3 fs-6"><i class="fa-solid fa-arrow-right me-1"></i> BAHAR (बाहर)</span>
                                        @else
                                            <span class="badge bg-dark px-3 py-2 rounded-3 fs-6">Range: {{ $b->bet_selection }}</span>
                                        @endif
                                    @else
                                        @php
                                            $sel = strtolower($b->bet_selection ?? '');
                                        @endphp
                                        @if($b->bet_type === 'color')
                                            @if($sel === 'green')
                                                <span class="badge bg-success px-3 py-2 rounded-3 fs-6"><i class="fa-solid fa-circle me-1"></i> Green</span>
                                            @elseif($sel === 'red')
                                                <span class="badge bg-danger px-3 py-2 rounded-3 fs-6"><i class="fa-solid fa-circle me-1"></i> Red</span>
                                            @elseif($sel === 'violet')
                                                <span class="badge px-3 py-2 rounded-3 fs-6" style="background: #8b5cf6;"><i class="fa-solid fa-circle me-1"></i> Violet</span>
                                            @endif
                                        @elseif($b->bet_type === 'size')
                                            <span class="badge {{ $sel === 'big' ? 'bg-warning text-dark' : 'bg-info text-dark' }} px-3 py-2 rounded-3 fs-6">
                                                {{ strtoupper($sel) }}
                                            </span>
                                        @else
                                            <span class="badge bg-dark px-3 py-2 rounded-3 font-monospace fs-6">
                                                Number: #{{ $b->bet_selection }}
                                            </span>
                                        @endif
                                    @endif
                                </td>

                                <!-- Stake Amount -->
                                <td class="font-monospace fw-bold text-dark">
                                    ₹{{ number_format($b->amount ?? $b->total_amount ?? $b->bet_amount ?? 0, 2) }}
                                </td>

                                <!-- Multiplier -->
                                <td>
                                    @if((isset($b->game_code) && $b->game_code === 'aviator') || (($game ?? '') === 'aviator'))
                                        @if(in_array($b->status, ['won', 'cashed_out']))
                                            <span class="badge bg-success text-white font-monospace px-2 py-1 rounded-2 fw-bold" style="font-size: 0.85rem;">
                                                <i class="fa-solid fa-bolt me-1"></i>{{ number_format((float) ($b->cashout_multiplier ?? $b->win_multiplier ?? 0), 2) }}x
                                            </span>
                                        @elseif(in_array($b->status, ['lost', 'crashed']))
                                            <span class="text-danger small font-monospace"><i class="fa-solid fa-burst me-1"></i>Crashed</span>
                                        @elseif($b->status === 'cancelled')
                                            <span class="text-muted small">Refunded</span>
                                        @else
                                            <span class="text-warning small font-monospace"><i class="fa-solid fa-plane-departure me-1"></i>In Flight</span>
                                        @endif
                                    @elseif((isset($b->game_code) && $b->game_code === 'mines') || (($game ?? '') === 'mines'))
                                        @if(in_array($b->status, ['won', 'cashed_out']))
                                            <span class="badge bg-success text-white font-monospace px-2 py-1 rounded-2 fw-bold" style="font-size: 0.85rem;">
                                                <i class="fa-solid fa-bolt me-1"></i>{{ number_format((float) ($b->current_multiplier ?? $b->win_multiplier ?? 0), 2) }}x
                                            </span>
                                        @elseif(in_array($b->status, ['lost', 'exploded']))
                                            <span class="text-danger small font-monospace"><i class="fa-solid fa-burst me-1"></i>Exploded</span>
                                        @elseif($b->status === 'active')
                                            <span class="text-warning small font-monospace"><i class="fa-solid fa-spinner fa-spin me-1"></i>Playing</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    @elseif((isset($b->game_code) && $b->game_code === 'vortex') || (($game ?? '') === 'vortex'))
                                        @if(in_array($b->status, ['won', 'cashed_out']))
                                            <span class="badge bg-success text-white font-monospace px-2 py-1 rounded-2 fw-bold" style="font-size: 0.85rem;">
                                                <i class="fa-solid fa-bolt me-1"></i>{{ number_format((float) ($b->current_multiplier ?? $b->win_multiplier ?? 1), 2) }}x
                                            </span>
                                        @elseif(in_array($b->status, ['lost', 'busted']))
                                            <span class="text-danger small font-monospace"><i class="fa-solid fa-burst me-1"></i>Busted</span>
                                        @elseif(in_array($b->status, ['pending', 'active']))
                                            <span class="text-warning small font-monospace"><i class="fa-solid fa-spinner fa-spin me-1"></i>Spinning</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    @elseif((isset($b->game_code) && $b->game_code === 'teen_patti') || (($game ?? '') === 'teen_patti'))
                                        <span class="badge bg-light text-dark border font-monospace px-2 py-1 rounded-2 fw-bold" style="font-size: 0.85rem;">
                                            <i class="fa-solid fa-bolt text-warning me-1"></i>{{ number_format((float) ($b->odds ?? $b->win_multiplier ?? 1.95), 2) }}x
                                        </span>
                                    @elseif((isset($b->game_code) && $b->game_code === 'roulette') || (($game ?? '') === 'roulette'))
                                        <span class="badge bg-light text-dark border font-monospace px-2 py-1 rounded-2 fw-bold" style="font-size: 0.85rem;">
                                            <i class="fa-solid fa-bolt text-warning me-1"></i>{{ number_format((float) ($b->odds ?? $b->win_multiplier ?? 36.00), 2) }}x
                                        </span>
                                    @elseif((isset($b->game_code) && $b->game_code === 'horse_racing') || (($game ?? '') === 'horse_racing'))
                                        <span class="badge bg-light text-dark border font-monospace px-2 py-1 rounded-2 fw-bold" style="font-size: 0.85rem;">
                                            <i class="fa-solid fa-bolt text-success me-1"></i>{{ number_format((float) ($b->odds ?? $b->win_multiplier ?? 1.00), 2) }}x
                                        </span>
                                    @else
                                        <span class="text-muted small">
                                            {{ number_format($b->win_multiplier ?? 0, 1) }}x
                                        </span>
                                    @endif
                                </td>

                                <!-- Winnings -->
                                <td class="font-monospace fw-bold {{ in_array($b->status, ['won', 'cashed_out']) ? 'text-success fs-6' : 'text-muted' }}">
                                    @if(in_array($b->status, ['won', 'cashed_out']))
                                        <div>+₹{{ number_format($b->win_amount ?? $b->payout_amount ?? 0, 2) }}</div>
                                        @if(($b->commission_amount ?? 0) > 0)
                                            <div class="text-muted small fw-normal" style="font-size: 0.72rem;">(-₹{{ number_format($b->commission_amount, 2) }} fee)</div>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>

                                <!-- Status Badge -->
                                <td>
                                    @if(in_array($b->status, ['won', 'cashed_out']))
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold">
                                            <i class="fa-solid fa-trophy me-1"></i> WON
                                        </span>
                                    @elseif(in_array($b->status, ['lost', 'crashed', 'exploded', 'busted']))
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">
                                            <i class="fa-solid fa-xmark me-1"></i> {{ (isset($b->game_code) && $b->game_code === 'vortex') || (($game ?? '') === 'vortex') ? 'BUSTED' : (((isset($b->game_code) && $b->game_code === 'aviator') || (($game ?? '') === 'aviator')) ? 'CRASHED' : (((isset($b->game_code) && $b->game_code === 'mines') || (($game ?? '') === 'mines')) ? 'EXPLODED' : 'Lost')) }}
                                        </span>
                                    @elseif($b->status === 'cancelled')
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded-pill">
                                            <i class="fa-solid fa-ban me-1"></i> Cancelled
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill">
                                            <i class="fa-solid fa-hourglass-half me-1"></i> {{ (isset($b->game_code) && $b->game_code === 'vortex') || (($game ?? '') === 'vortex') ? 'Active' : (((isset($b->game_code) && $b->game_code === 'aviator') || (($game ?? '') === 'aviator')) ? 'In Flight' : (((isset($b->game_code) && $b->game_code === 'mines') || (($game ?? '') === 'mines')) ? 'Active' : 'Pending')) }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Placed Time -->
                                <td class="small text-muted">
                                    {{ $b->created_at ? $b->created_at->timezone('Asia/Kolkata')->format('d M, h:i A') : '-' }}
                                </td>

                                <!-- Action: Bet Details Modal Trigger -->
                                <td class="pe-3 text-end">
                                    @php
                                        $isMines = (isset($b->game_code) && $b->game_code === 'mines') || (($game ?? '') === 'mines');
                                        $isAviator = (isset($b->game_code) && $b->game_code === 'aviator') || (($game ?? '') === 'aviator');
                                        $isVortex = (isset($b->game_code) && $b->game_code === 'vortex') || (($game ?? '') === 'vortex');
                                        $isTeenPatti = (isset($b->game_code) && $b->game_code === 'teen_patti') || (($game ?? '') === 'teen_patti');
                                        $isRoulette = (isset($b->game_code) && $b->game_code === 'roulette') || (($game ?? '') === 'roulette');
                                        $isHorseRacing = (isset($b->game_code) && $b->game_code === 'horse_racing') || (($game ?? '') === 'horse_racing');
                                        $gameName = $b->game_name ?? ($isHorseRacing ? 'Live Horse Racing Derby' : ($isRoulette ? 'European Roulette 36x' : ($isTeenPatti ? '20-20 Teen Patti' : ($isVortex ? 'Vortex Wheel' : ($isMines ? 'Mines 5x5' : ($isAviator ? 'Aviator Crash' : (($game ?? '') === 'andar_bahar' ? 'Andar Bahar' : (($game ?? '') === 'lottery' ? '2-Digit Lottery' : 'WinGo Color'))))))));
                                        $roundIdStr = ($isVortex || $isMines) ? ($b->game_id ?? '#'.$b->id) : ($b->round_id ?? ($b->period_id ?? ($b->slot_time ?? '-')));
                                        $crashPtStr = $isAviator ? ($b->round_crash_multiplier ?? $b->round?->crash_multiplier ?? '-') : '-';
                                        $autoCashoutStr = $isAviator && !empty($b->auto_cashout) ? number_format((float)$b->auto_cashout, 2).'x' : 'Manual';
                                        $cashoutMultiplierStr = ($isMines || $isVortex) ? number_format((float)($b->current_multiplier ?? $b->win_multiplier ?? 1), 2).'x' : ($isAviator && !empty($b->cashout_multiplier) ? number_format((float)$b->cashout_multiplier, 2).'x' : ($isTeenPatti ? number_format((float)($b->odds ?? $b->win_multiplier ?? 1.95), 2).'x' : ($isRoulette ? number_format((float)($b->odds ?? $b->win_multiplier ?? 36.00), 2).'x' : ($isHorseRacing ? number_format((float)($b->odds ?? $b->win_multiplier ?? 1.00), 2).'x' : (in_array($b->status, ['won', 'cashed_out']) ? number_format((float)($b->win_multiplier ?? 0), 2).'x' : '-')))));
                                        $tpTypeMap = ['player_a' => 'Player A', 'player_b' => 'Player B', 'tie' => 'Tie', 'pair_plus_a' => 'Pair Plus A', 'pair_plus_b' => 'Pair Plus B'];
                                        $selectionStr = $isHorseRacing ? ($b->selection_label ?? ('#'.($b->horse_id ?? '').' '.($b->horse_name ?? '').' ('.strtoupper($b->bet_type ?? 'WIN').')')) : ($isRoulette ? ($b->selection_label ?? (ucfirst($b->bet_type ?? 'Bet').': '.($b->bet_value ?? ''))) : ($isTeenPatti ? ($tpTypeMap[$b->bet_type ?? ''] ?? strtoupper($b->bet_type ?? '')) : ($isVortex ? ('Ring '.($b->current_ring ?? 1).' ('.(($b->current_ring ?? 1) == 1 ? 'Water' : ((($b->current_ring ?? 1) == 2 ? 'Earth' : 'Fire'))).')'.(!empty($b->part_cashed_out) ? ' • 50% Part Cashout' : '')) : ($isMines ? (($b->mines_count ?? 3).' Mines • '.($b->current_step ?? count($b->opened_tiles ?? [])).' Safe Coins') : ($isAviator ? 'Panel #'.$b->bet_slot : ($b->selection_label ?? ($b->chosen_number ?? ($b->bet_selection ?? '-'))))))));
                                    @endphp
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1 shadow-sm view-bet-detail-btn"
                                        style="font-size: 0.76rem;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#betDetailModal"
                                        data-id="{{ $b->id }}"
                                        data-game="{{ $gameName }}"
                                        data-is-aviator="{{ $isAviator ? '1' : '0' }}"
                                        data-is-mines="{{ $isMines ? '1' : '0' }}"
                                        data-is-vortex="{{ $isVortex ? '1' : '0' }}"
                                        data-is-roulette="{{ $isRoulette ? '1' : '0' }}"
                                        data-is-horse-racing="{{ $isHorseRacing ? '1' : '0' }}"
                                        data-round="{{ $roundIdStr }}"
                                        data-selection="{{ $selectionStr }}"
                                        data-stake="₹{{ number_format($b->amount ?? $b->total_amount ?? $b->bet_amount ?? 0, 2) }}"
                                        data-multiplier="{{ $cashoutMultiplierStr }}"
                                        data-winnings="{{ in_array($b->status, ['won', 'cashed_out']) ? '+₹'.number_format($b->win_amount ?? $b->payout_amount ?? 0, 2) : '₹0.00' }}"
                                        data-fee="₹{{ number_format($b->commission_amount ?? 0, 2) }}"
                                        data-status="{{ in_array($b->status, ['won', 'cashed_out']) ? 'WON' : (in_array($b->status, ['lost', 'crashed', 'exploded', 'busted']) ? 'LOST / BUSTED' : ($b->status === 'cancelled' ? 'CANCELLED' : 'ACTIVE / PENDING')) }}"
                                        data-status-badge="{{ in_array($b->status, ['won', 'cashed_out']) ? 'bg-success' : (in_array($b->status, ['lost', 'crashed', 'exploded', 'busted']) ? 'bg-danger' : 'bg-warning text-dark') }}"
                                        data-time="{{ $b->created_at ? $b->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i:s A') : '-' }}"
                                        data-cashed-time="{{ !empty($b->cashed_out_at) ? $b->cashed_out_at->timezone('Asia/Kolkata')->format('d M Y, h:i:s A') : '-' }}"
                                        data-auto="{{ $autoCashoutStr }}"
                                        data-crash-point="{{ $crashPtStr !== '-' ? $crashPtStr.'x' : '-' }}"
                                        data-slot="{{ $isAviator ? 'Slot '.$b->bet_slot : '-' }}"
                                        data-mines-count="{{ $b->mines_count ?? 3 }}"
                                        data-mines-safe="{{ $b->current_step ?? count($b->opened_tiles ?? []) }}"
                                        data-mines-jackpot="{{ !empty($b->is_jackpot_win) ? '1' : '0' }}"
                                        data-vortex-ring="{{ $b->current_ring ?? 1 }}"
                                        data-vortex-spins="{{ $b->spin_count ?? 0 }}"
                                        data-vortex-part="{{ !empty($b->part_cashed_out) ? '1' : '0' }}"
                                        data-vortex-part-amount="₹{{ number_format((float)($b->part_cashout_amount ?? 0), 2) }}"
                                        data-roulette-number="{{ !is_null($b->round?->winning_number ?? null) ? $b->round->winning_number : '-' }}"
                                        data-roulette-color="{{ strtoupper($b->round?->winning_color ?? '-') }}"
                                        data-horse-name="{{ $b->horse_name ?? '' }}"
                                        data-horse-id="{{ $b->horse_id ?? '' }}"
                                        data-horse-bet-type="{{ strtoupper($b->bet_type ?? '') }}"
                                        data-horse-winner="{{ !empty($b->round?->winner_horse_name) ? '#'.$b->round->winner_horse_id.' '.$b->round->winner_horse_name : '-' }}">
                                        <i class="fa-solid fa-eye"></i>
                                        <span>Details</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-receipt fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                    <div class="fw-bold fs-6">No bets found in this section.</div>
                                    <p class="small text-muted mb-3">You haven't placed any bets under this filter yet.</p>
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        <a href="{{ route('home') }}#betSlipForm" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                            <i class="fa-solid fa-dice me-1"></i> Play 2-Digit Lottery
                                        </a>
                                        <a href="{{ route('wingo.index') }}" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold">
                                            <i class="fa-solid fa-gamepad me-1"></i> Play WinGo Color
                                        </a>
                                        <a href="{{ route('andar-bahar.index') }}" class="btn btn-sm text-white rounded-pill px-3 fw-bold" style="background: linear-gradient(135deg, #f59e0b, #b45309);">
                                            <i class="fa-solid fa-diamond me-1"></i> Play Andar Bahar
                                        </a>
                                        <a href="{{ route('aviator.index') }}" class="btn btn-sm text-white rounded-pill px-3 fw-bold" style="background: linear-gradient(135deg, #e11d48, #f97316);">
                                            <i class="fa-solid fa-plane-departure me-1"></i> Play Aviator Crash
                                        </a>
                                        <a href="{{ route('mines.index') }}" class="btn btn-sm text-white rounded-pill px-3 fw-bold" style="background: linear-gradient(135deg, #10b981, #059669);">
                                            <i class="fa-solid fa-bomb me-1"></i> Play Mines 5x5
                                        </a>
                                        <a href="{{ route('vortex.index') }}" class="btn btn-sm text-white rounded-pill px-3 fw-bold" style="background: linear-gradient(135deg, #06b6d4, #0284c7);">
                                            <i class="fa-solid fa-arrows-spin me-1"></i> Play Vortex Wheel
                                        </a>
                                        <a href="{{ route('teen-patti.index') }}" class="btn btn-sm text-white rounded-pill px-3 fw-bold" style="background: linear-gradient(135deg, #059669, #064e3b);">
                                            <i class="fa-solid fa-heart me-1"></i> Play 20-20 Teen Patti
                                        </a>
                                        <a href="{{ route('roulette.index') }}" class="btn btn-sm text-white rounded-pill px-3 fw-bold" style="background: linear-gradient(135deg, #f59e0b, #dc2626);">
                                            <i class="fa-solid fa-dharmachakra me-1"></i> Play Roulette 36x
                                        </a>
                                        <a href="{{ route('horse-racing.index') }}" class="btn btn-sm text-white rounded-pill px-3 fw-bold" style="background: linear-gradient(135deg, #10b981, #047857);">
                                            <i class="fa-solid fa-horse-head me-1"></i> Play Derby Racing
                                        </a>
                                        <a href="{{ route('dragon-tiger.index') }}" class="btn btn-sm text-white rounded-pill px-3 fw-bold" style="background: linear-gradient(135deg, #dc2626, #2563eb);">
                                            <i class="fa-solid fa-dragon me-1"></i> Play Dragon Tiger
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($bets->hasPages())
                <div class="p-3 border-top bg-light">
                    {{ $bets->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 2: DEBIT & CREDIT HISTORY (Passbook Ledger) -->
    @if(($tab ?? '') === 'transactions')
        <!-- Debit & Credit Filter Pills -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('bets.history', ['tab' => 'transactions', 'flow' => 'all']) }}" class="btn btn-sm rounded-pill fw-bold {{ ($flow ?? 'all') === 'all' && empty($type) ? 'btn-dark' : 'btn-outline-secondary' }}">
                    All Transactions
                </a>
                <a href="{{ route('bets.history', ['tab' => 'transactions', 'flow' => 'credit']) }}" class="btn btn-sm rounded-pill fw-bold {{ ($flow ?? '') === 'credit' ? 'btn-success text-white' : 'btn-outline-success' }}">
                    <i class="fa-solid fa-arrow-down me-1"></i> 🟢 Credits (+ Winnings & Deposits)
                </a>
                <a href="{{ route('bets.history', ['tab' => 'transactions', 'flow' => 'debit']) }}" class="btn btn-sm rounded-pill fw-bold {{ ($flow ?? '') === 'debit' ? 'btn-danger text-white' : 'btn-outline-danger' }}">
                    <i class="fa-solid fa-arrow-up me-1"></i> 🔴 Debits (- Bets & Withdrawals)
                </a>
                <a href="{{ route('bets.history', ['tab' => 'transactions', 'type' => 'bet_placed']) }}" class="btn btn-sm rounded-pill fw-bold {{ ($type ?? '') === 'bet_placed' ? 'btn-secondary text-white' : 'btn-outline-light text-white' }}">
                    Bets Placed
                </a>
                <a href="{{ route('bets.history', ['tab' => 'transactions', 'type' => 'bet_won']) }}" class="btn btn-sm rounded-pill fw-bold {{ ($type ?? '') === 'bet_won' ? 'btn-warning text-dark' : 'btn-outline-warning text-warning' }}">
                    Payouts Won
                </a>
                <a href="{{ route('bets.history', ['tab' => 'transactions', 'type' => 'deposit']) }}" class="btn btn-sm rounded-pill fw-bold {{ ($type ?? '') === 'deposit' ? 'btn-primary text-white' : 'btn-outline-primary text-white' }}">
                    Deposits
                </a>
                <a href="{{ route('bets.history', ['tab' => 'transactions', 'type' => 'withdrawal']) }}" class="btn btn-sm rounded-pill fw-bold {{ ($type ?? '') === 'withdrawal' ? 'btn-info text-white' : 'btn-outline-info text-info' }}">
                    Withdrawals
                </a>
            </div>

            <div class="text-light opacity-80 small">
                Showing <strong class="text-white">{{ $transactions->count() }}</strong> of <strong class="text-white">{{ $transactions->total() }}</strong> entries
            </div>
        </div>

        <!-- Transactions Table Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th class="ps-3">Txn ID</th>
                            <th>Date & Time</th>
                            <th>Flow</th>
                            <th>Transaction Type</th>
                            <th>Amount</th>
                            <th>Balance After</th>
                            <th>Status</th>
                            <th class="pe-3">Description / Game Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $txn)
                            @php
                                $isCredit = in_array($txn->type, ['deposit', 'bet_won'], true);
                            @endphp
                            <tr>
                                <td class="ps-3 font-monospace small text-muted">#{{ $txn->id }}</td>
                                <td class="small">{{ $txn->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}</td>
                                
                                <!-- Debit / Credit Tag -->
                                <td>
                                    @if($isCredit)
                                        <span class="badge bg-success text-white px-2 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                                            <i class="fa-solid fa-arrow-down me-1"></i> CREDIT (+)
                                        </span>
                                    @else
                                        <span class="badge bg-danger text-white px-2 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                                            <i class="fa-solid fa-arrow-up me-1"></i> DEBIT (-)
                                        </span>
                                    @endif
                                </td>

                                <!-- Transaction Type Badge -->
                                <td>
                                    @if($txn->type === 'deposit')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                            <i class="fa-solid fa-qrcode me-1"></i> Deposit
                                        </span>
                                    @elseif($txn->type === 'bet_placed')
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded-pill">
                                            <i class="fa-solid fa-dice me-1"></i> Bet Placed
                                        </span>
                                    @elseif($txn->type === 'bet_won')
                                        <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1 rounded-pill fw-bold">
                                            <i class="fa-solid fa-trophy me-1 text-warning"></i> Won Payout
                                        </span>
                                    @elseif($txn->type === 'withdrawal')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill fw-bold">
                                            <i class="fa-solid fa-money-bill-transfer me-1"></i> Withdrawal
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                            Adjustment
                                        </span>
                                    @endif
                                </td>

                                <!-- Amount (+/-) -->
                                <td class="font-monospace fw-bold {{ $isCredit ? 'text-success fs-6' : 'text-danger' }}">
                                    {{ $isCredit ? '+' : '-' }}₹{{ number_format($txn->amount, 2) }}
                                </td>

                                <!-- Balance After -->
                                <td class="font-monospace fw-bold text-dark">
                                    ₹{{ number_format($txn->balance_after, 2) }}
                                </td>

                                <!-- Status -->
                                <td>
                                    @if($txn->status === 'approved' || $txn->status === 'completed')
                                        <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill small fw-bold">
                                            <i class="fa-solid fa-check me-1"></i> Success
                                        </span>
                                    @elseif($txn->status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1 rounded-pill small fw-bold">
                                            <i class="fa-solid fa-hourglass-half me-1"></i> Pending
                                        </span>
                                    @elseif($txn->status === 'rejected')
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill small fw-bold">
                                            <i class="fa-solid fa-xmark me-1"></i> Rejected
                                        </span>
                                    @endif
                                </td>

                                <!-- Notes & Details -->
                                <td class="pe-3 small text-muted">
                                    {{ $txn->admin_notes ?: ($txn->account_details ?: ($txn->utr_number ? 'UTR: ' . $txn->utr_number : '-')) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-clock-rotate-left fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                    No debit or credit transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="p-3 border-top bg-light">
                    {{ $transactions->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    @endif

</div>

<!-- Unified Bet Details Modal (Displays Aviator, Lottery, WinGo & Andar Bahar Full Specs) -->
<div class="modal fade" id="betDetailModal" tabindex="-1" aria-labelledby="betDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff;">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle d-flex align-items-center justify-content-center text-white" id="modalGameIcon" style="width: 40px; height: 40px; background: rgba(255,255,255,0.15); font-size: 1.1rem;">
                        <i class="fa-solid fa-receipt"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="modalGameTitle">Bet Details</h5>
                        <div class="text-white-50 small" id="modalBetIdSub">Ticket Breakdown</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4 bg-light">
                <!-- Status & Payout Hero Banner -->
                <div class="card border-0 rounded-3 p-3 mb-3 text-center shadow-sm bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small text-uppercase fw-bold">Settlement Status</span>
                        <span class="badge rounded-pill px-3 py-1 fs-6 fw-bold" id="modalStatusBadge">WON</span>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-center gap-2">
                        <span class="text-muted small">Payout:</span>
                        <span class="fs-3 fw-bold font-monospace text-success" id="modalWinningsText">+₹0.00</span>
                    </div>
                    <div class="small text-muted" id="modalFeeText" style="font-size: 0.78rem;">Platform fee: ₹0.00</div>
                </div>

                <!-- Structured Details Grid -->
                <div class="card border-0 rounded-3 p-3 shadow-sm bg-white">
                    <div class="row g-3 small">
                        <div class="col-6">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Game Type</div>
                            <div class="fw-bold text-dark mt-1" id="modalGameName">-</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Ticket ID</div>
                            <div class="fw-bold font-monospace text-dark mt-1" id="modalTicketId">-</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Round / Draw / Period</div>
                            <div class="fw-bold font-monospace text-dark mt-1" id="modalRoundId">-</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Stake Amount</div>
                            <div class="fw-bold text-primary font-monospace mt-1" id="modalStakeAmount">-</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Chosen Selection</div>
                            <div class="fw-bold text-dark mt-1" id="modalSelection">-</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Multiplier</div>
                            <div class="fw-bold text-success font-monospace mt-1" id="modalMultiplier">-</div>
                        </div>

                        <!-- Aviator Specific Rows -->
                        <div class="col-6 aviator-detail-field" style="display: none;">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Betting Panel</div>
                            <div class="fw-bold text-danger font-monospace mt-1" id="modalAviatorSlot">-</div>
                        </div>
                        <div class="col-6 aviator-detail-field" style="display: none;">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Auto Cashout Target</div>
                            <div class="fw-bold text-dark font-monospace mt-1" id="modalAviatorAuto">-</div>
                        </div>
                        <div class="col-6 aviator-detail-field" style="display: none;">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Round Crash Multiplier</div>
                            <div class="fw-bold text-dark font-monospace mt-1" id="modalAviatorCrashPt">-</div>
                        </div>
                        <div class="col-6 aviator-detail-field" style="display: none;">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Cashed Out Timestamp</div>
                            <div class="fw-bold text-dark mt-1" id="modalAviatorCashedTime" style="font-size: 0.78rem;">-</div>
                        </div>

                        <!-- Mines Specific Rows -->
                        <div class="col-6 mines-detail-field" style="display: none;">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Board Mines</div>
                            <div class="fw-bold text-danger font-monospace mt-1" id="modalMinesCount">-</div>
                        </div>
                        <div class="col-6 mines-detail-field" style="display: none;">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Safe Coins Found</div>
                            <div class="fw-bold text-success font-monospace mt-1" id="modalMinesSafe">-</div>
                        </div>
                        <div class="col-6 mines-detail-field" style="display: none;">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Jackpot Status</div>
                            <div class="fw-bold text-warning font-monospace mt-1" id="modalMinesJackpot">-</div>
                        </div>

                        <!-- Vortex Specific Rows -->
                        <div class="col-6 vortex-detail-field" style="display: none;">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Ring Tier Reached</div>
                            <div class="fw-bold text-info font-monospace mt-1" id="modalVortexRing">-</div>
                        </div>
                        <div class="col-6 vortex-detail-field" style="display: none;">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Spins Completed</div>
                            <div class="fw-bold text-dark font-monospace mt-1" id="modalVortexSpins">-</div>
                        </div>
                        <div class="col-6 vortex-detail-field" style="display: none;">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">50% Part Cashout</div>
                            <div class="fw-bold text-success font-monospace mt-1" id="modalVortexPart">-</div>
                        </div>

                        <!-- Roulette Specific Rows -->
                        <div class="col-6 roulette-detail-field" style="display: none;">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Winning Number</div>
                            <div class="fw-bold font-monospace mt-1" id="modalRouletteNumber">-</div>
                        </div>
                        <div class="col-6 roulette-detail-field" style="display: none;">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Winning Color</div>
                            <div class="fw-bold font-monospace mt-1" id="modalRouletteColor">-</div>
                        </div>

                        <!-- Horse Racing Specific Rows -->
                        <div class="col-6 horse-racing-detail-field" style="display: none;">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Runner Chosen</div>
                            <div class="fw-bold text-success font-monospace mt-1" id="modalHorseRunner">-</div>
                        </div>
                        <div class="col-6 horse-racing-detail-field" style="display: none;">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Race Winner</div>
                            <div class="fw-bold text-dark font-monospace mt-1" id="modalHorseWinner">-</div>
                        </div>

                        <div class="col-12 pt-2 border-top">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Placed At</div>
                            <div class="fw-bold text-dark mt-1" id="modalPlacedTime">-</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 bg-light pt-0">
                <a href="{{ route('wallet.transactions') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i> Passbook
                </a>
                <button type="button" class="btn btn-sm btn-dark rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const detailButtons = document.querySelectorAll('.view-bet-detail-btn');
    detailButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const game = this.getAttribute('data-game');
            const isAviator = this.getAttribute('data-is-aviator') === '1';
            const isMines = this.getAttribute('data-is-mines') === '1';
            const isVortex = this.getAttribute('data-is-vortex') === '1';
            const isRoulette = this.getAttribute('data-is-roulette') === '1';
            const round = this.getAttribute('data-round');
            const selection = this.getAttribute('data-selection');
            const stake = this.getAttribute('data-stake');
            const multiplier = this.getAttribute('data-multiplier');
            const winnings = this.getAttribute('data-winnings');
            const fee = this.getAttribute('data-fee');
            const status = this.getAttribute('data-status');
            const statusBadgeClass = this.getAttribute('data-status-badge');
            const time = this.getAttribute('data-time');
            const cashedTime = this.getAttribute('data-cashed-time');
            const auto = this.getAttribute('data-auto');
            const crashPoint = this.getAttribute('data-crash-point');
            const slot = this.getAttribute('data-slot');
            const minesCount = this.getAttribute('data-mines-count');
            const minesSafe = this.getAttribute('data-mines-safe');
            const minesJackpot = this.getAttribute('data-mines-jackpot') === '1';
            const vortexRing = this.getAttribute('data-vortex-ring');
            const vortexSpins = this.getAttribute('data-vortex-spins');
            const vortexPart = this.getAttribute('data-vortex-part') === '1';
            const vortexPartAmount = this.getAttribute('data-vortex-part-amount');
            const rouletteNumber = this.getAttribute('data-roulette-number');
            const rouletteColor = this.getAttribute('data-roulette-color');
            const isHorseRacing = this.getAttribute('data-is-horse-racing') === '1';
            const horseName = this.getAttribute('data-horse-name');
            const horseId = this.getAttribute('data-horse-id');
            const horseBetType = this.getAttribute('data-horse-bet-type');
            const horseWinner = this.getAttribute('data-horse-winner');

            document.getElementById('modalGameTitle').textContent = game;
            document.getElementById('modalBetIdSub').textContent = 'Bet Ticket #' + id;
            document.getElementById('modalTicketId').textContent = '#' + id;
            document.getElementById('modalGameName').textContent = game;
            document.getElementById('modalRoundId').textContent = round;
            document.getElementById('modalSelection').textContent = selection;
            document.getElementById('modalStakeAmount').textContent = stake;
            document.getElementById('modalMultiplier').textContent = multiplier;
            
            const winElem = document.getElementById('modalWinningsText');
            winElem.textContent = winnings;
            if (status.includes('WON')) {
                winElem.className = 'fs-3 fw-bold font-monospace text-success';
            } else if (status.includes('LOST') || status.includes('CRASHED') || status.includes('EXPLODED') || status.includes('BUSTED')) {
                winElem.className = 'fs-3 fw-bold font-monospace text-muted';
            } else {
                winElem.className = 'fs-3 fw-bold font-monospace text-warning';
            }

            document.getElementById('modalFeeText').textContent = 'Platform fee: ' + fee;
            document.getElementById('modalPlacedTime').textContent = time;

            const badgeElem = document.getElementById('modalStatusBadge');
            badgeElem.textContent = status;
            badgeElem.className = 'badge rounded-pill px-3 py-1 fs-6 fw-bold ' + statusBadgeClass;

            const aviatorFields = document.querySelectorAll('.aviator-detail-field');
            if (isAviator) {
                aviatorFields.forEach(el => el.style.display = 'block');
                document.getElementById('modalAviatorSlot').textContent = slot;
                document.getElementById('modalAviatorAuto').textContent = auto;
                document.getElementById('modalAviatorCrashPt').textContent = crashPoint;
                document.getElementById('modalAviatorCashedTime').textContent = cashedTime;
                document.getElementById('modalGameIcon').innerHTML = '<i class="fa-solid fa-plane-departure text-warning"></i>';
            } else {
                aviatorFields.forEach(el => el.style.display = 'none');
            }

            const minesFields = document.querySelectorAll('.mines-detail-field');
            if (isMines) {
                minesFields.forEach(el => el.style.display = 'block');
                document.getElementById('modalMinesCount').textContent = (minesCount || '3') + ' Mines';
                document.getElementById('modalMinesSafe').textContent = (minesSafe || '0') + ' Coins';
                document.getElementById('modalMinesJackpot').innerHTML = minesJackpot ? '<span class="text-warning fw-bold"><i class="fa-solid fa-crown me-1 text-danger"></i>JACKPOT WON!</span>' : 'Standard Win';
                document.getElementById('modalGameIcon').innerHTML = '<i class="fa-solid fa-bomb text-success"></i>';
            } else {
                minesFields.forEach(el => el.style.display = 'none');
            }

            const vortexFields = document.querySelectorAll('.vortex-detail-field');
            if (isVortex) {
                vortexFields.forEach(el => el.style.display = 'block');
                const ringNum = parseInt(vortexRing) || 1;
                const ringLabel = ringNum === 1 ? 'Ring 1 (Water 🌊)' : (ringNum === 2 ? 'Ring 2 (Earth 🦠)' : 'Ring 3 (Fire 🔥)');
                document.getElementById('modalVortexRing').textContent = ringLabel;
                document.getElementById('modalVortexSpins').textContent = (vortexSpins || '0') + ' Spins';
                document.getElementById('modalVortexPart').innerHTML = vortexPart ? '<span class="text-success fw-bold"><i class="fa-solid fa-check me-1"></i>Yes (' + vortexPartAmount + ')</span>' : 'Not Taken';
                document.getElementById('modalGameIcon').innerHTML = '<i class="fa-solid fa-arrows-spin text-info"></i>';
            } else {
                vortexFields.forEach(el => el.style.display = 'none');
            }

            const rouletteFields = document.querySelectorAll('.roulette-detail-field');
            if (isRoulette) {
                rouletteFields.forEach(el => el.style.display = 'block');
                document.getElementById('modalRouletteNumber').textContent = (rouletteNumber && rouletteNumber !== '-') ? '#' + rouletteNumber : 'Pending';
                document.getElementById('modalRouletteColor').textContent = (rouletteColor && rouletteColor !== '-') ? rouletteColor : '-';
                document.getElementById('modalGameIcon').innerHTML = '<i class="fa-solid fa-dharmachakra text-warning"></i>';
            } else {
                rouletteFields.forEach(el => el.style.display = 'none');
            }

            const horseRacingFields = document.querySelectorAll('.horse-racing-detail-field');
            if (isHorseRacing) {
                horseRacingFields.forEach(el => el.style.display = 'block');
                document.getElementById('modalHorseRunner').textContent = horseName ? ('#' + horseId + ' ' + horseName + ' (' + horseBetType + ')') : '-';
                document.getElementById('modalHorseWinner').textContent = (horseWinner && horseWinner !== '-') ? horseWinner : 'Pending Finish';
                document.getElementById('modalGameIcon').innerHTML = '<i class="fa-solid fa-horse-head text-success"></i>';
            } else {
                horseRacingFields.forEach(el => el.style.display = 'none');
            }

            if (!isAviator && !isMines && !isVortex && !isRoulette && !isHorseRacing) {
                document.getElementById('modalGameIcon').innerHTML = '<i class="fa-solid fa-receipt text-info"></i>';
            }
        });
    });
});
</script>
@endsection
