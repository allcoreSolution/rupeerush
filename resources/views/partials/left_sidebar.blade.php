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

<!-- RupeeRush Left Sidebar -->
<aside class="rupeerush-left-sidebar" id="rupeeRushSidebar" aria-label="Game Lobby & Navigation Sidebar">
    <div class="sidebar-inner-content d-flex flex-column h-100">
        
        <!-- 1. Sidebar Brand & Controls Header -->
        <div class="sidebar-header-box d-flex align-items-center justify-content-between px-3 py-2 border-bottom border-white border-opacity-10">
            <div class="d-flex align-items-center gap-2">
                <div class="sidebar-brand-pill d-flex align-items-center gap-2">
                    <span class="sidebar-pulse-dot"></span>
                    <span class="sidebar-brand-name">RUPEE<span class="text-warning">RUSH</span></span>
                </div>
                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-2 py-0" style="font-size: 0.65rem;">LOBBY</span>
            </div>
            
            <div class="d-flex align-items-center gap-1">
                <!-- Desktop Collapse Button -->
                <button type="button" class="btn-sidebar-collapse d-none d-xl-inline-flex align-items-center justify-content-center" onclick="toggleRupeeRushSidebar()" title="Collapse Sidebar">
                    <i class="fa-solid fa-angles-left"></i>
                </button>
                <!-- Mobile / Tablet Close Button -->
                <button type="button" class="btn-sidebar-close d-xl-none align-items-center justify-content-center" onclick="closeRupeeRushSidebar()" aria-label="Close Sidebar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- 2. Scrollable Body -->
        <div class="sidebar-scrollable-body flex-grow-1 px-2 py-2">
            
            <!-- User Status & Quick Banking Pill -->
            @auth
                <div class="sidebar-user-card mb-2 p-2 rounded-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="sidebar-user-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="overflow-hidden flex-grow-1">
                            <div class="sidebar-user-name text-truncate text-white fw-bold">{{ Auth::user()->name }}</div>
                            <div class="sidebar-user-sub text-muted font-monospace">{{ Auth::user()->phone }}</div>
                        </div>
                    </div>

                    <!-- Balance Row -->
                    <div class="sidebar-balance-row d-flex align-items-center justify-content-between p-2 rounded-2 mb-2">
                        <span class="text-secondary small fw-semibold">Balance:</span>
                        <span class="sidebar-balance-val font-monospace fw-bold text-success fs-6">₹{{ number_format(Auth::user()->balance, 2) }}</span>
                    </div>

                    <!-- Deposit & Withdraw Quick Actions -->
                    <div class="row g-1">
                        <div class="col-6">
                            <a href="{{ route('wallet.deposit') }}" class="btn btn-sidebar-deposit w-100 btn-sm py-1 fw-bold text-white d-flex align-items-center justify-content-center gap-1">
                                <i class="fa-solid fa-plus-circle"></i>
                                <span>Deposit</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('wallet.withdraw') }}" class="btn btn-sidebar-withdraw w-100 btn-sm py-1 fw-bold text-white d-flex align-items-center justify-content-center gap-1">
                                <i class="fa-solid fa-arrow-up-from-bracket"></i>
                                <span>Withdraw</span>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="sidebar-guest-card mb-2 p-2 rounded-3 text-center">
                    <div class="small text-warning fw-bold mb-1">Welcome to RupeeRush!</div>
                    <div class="text-secondary small mb-2" style="font-size: 0.72rem;">Play 11+ Real Games & Win Instant Cash</div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('login') }}" class="btn btn-sm btn-success w-100 fw-bold py-1">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-outline-warning w-100 fw-bold py-1">
                            <i class="fa-solid fa-user-plus me-1"></i> Register
                        </a>
                    </div>
                </div>
            @endauth

            <!-- Quick Search Input for Sidebar Games -->
            <div class="sidebar-search-box mb-2 px-1">
                <div class="position-relative">
                    <i class="fa-solid fa-magnifying-glass sidebar-search-icon"></i>
                    <input type="text" id="sidebarGameSearch" class="form-control sidebar-search-input" placeholder="Search games & sports..." oninput="filterSidebarGames(this.value)" autocomplete="off">
                    <button type="button" id="sidebarSearchClearBtn" class="btn-sidebar-search-clear d-none" onclick="clearSidebarSearch()" title="Clear">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Section 1: MAIN NAVIGATION -->
            <div class="sidebar-nav-section mb-2">
                <div class="sidebar-section-title px-2 py-1 text-uppercase">
                    <span>Main Menu</span>
                </div>
                
                <ul class="sidebar-menu-list list-unstyled mb-0">
                    <!-- Home -->
                    <li>
                        <a href="{{ route('home') }}" class="sidebar-menu-item {{ request()->routeIs('home') ? 'active' : '' }}" title="Gaming Lobby Home">
                            <div class="sidebar-item-icon icon-home">
                                <i class="fa-solid fa-house"></i>
                            </div>
                            <span class="sidebar-item-label">Home Lobby</span>
                        </a>
                    </li>

                    <!-- Cricket Live Exchange -->
                    <li>
                        <a href="{{ route('sports.cricket') }}" class="sidebar-menu-item {{ request()->routeIs('sports.cricket*') ? 'active' : '' }}" title="Cricket Sports & Live Exchange">
                            <div class="sidebar-item-icon icon-cricket">
                                <i class="fa-solid fa-baseball-bat-ball"></i>
                            </div>
                            <span class="sidebar-item-label">Cricket Exchange</span>
                            <span class="sidebar-item-badge badge-live">LIVE</span>
                        </a>
                    </li>

                    <!-- My Bets -->
                    <li>
                        <a href="{{ Auth::check() ? route('bets.history') : route('login') }}" class="sidebar-menu-item {{ request()->routeIs('bets.history') ? 'active' : '' }}" title="My Bet Slips & Results">
                            <div class="sidebar-item-icon icon-bets">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <span class="sidebar-item-label">My Bets</span>
                        </a>
                    </li>

                    <!-- Passbook -->
                    <li>
                        <a href="{{ Auth::check() ? route('wallet.transactions') : route('login') }}" class="sidebar-menu-item {{ request()->routeIs('wallet.transactions') ? 'active' : '' }}" title="Passbook & Transactions">
                            <div class="sidebar-item-icon icon-passbook">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <span class="sidebar-item-label">Passbook</span>
                        </a>
                    </li>

                    <!-- Refer & Earn -->
                    <li>
                        <a href="{{ Auth::check() ? route('referral.index') : route('login') }}" class="sidebar-menu-item {{ request()->routeIs('referral.*') ? 'active' : '' }}" title="Refer Friends & Earn Bonus">
                            <div class="sidebar-item-icon icon-refer">
                                <i class="fa-solid fa-gift"></i>
                            </div>
                            <span class="sidebar-item-label">Refer &amp; Earn</span>
                            <span class="sidebar-item-badge badge-bonus">BONUS</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Section 2: ALL GAMES (11 REAL ACTIVE GAMES) -->
            <div class="sidebar-nav-section mb-2" id="sidebarGamesContainer">
                <div class="sidebar-section-title px-2 py-1 text-uppercase d-flex align-items-center justify-content-between">
                    <span>All Games</span>
                    <span class="badge bg-warning bg-opacity-20 text-warning px-1 py-0 rounded" style="font-size: 0.65rem;">11 Games</span>
                </div>

                <ul class="sidebar-menu-list list-unstyled mb-0" id="sidebarGamesList">
                    
                    <!-- 1. Cricket Live Sports -->
                    <li class="sidebar-game-entry" data-name="cricket sports exchange live ball by ball back lay">
                        <a href="{{ route('sports.cricket') }}" class="sidebar-menu-item {{ request()->routeIs('sports.cricket*') ? 'active' : '' }}">
                            <div class="sidebar-item-icon icon-cricket">
                                <i class="fa-solid fa-baseball-bat-ball"></i>
                            </div>
                            <div class="sidebar-item-content">
                                <span class="sidebar-item-label">Cricket Sports</span>
                                <span class="sidebar-item-sub">In-Play &amp; Back/Lay</span>
                            </div>
                            <span class="sidebar-item-badge badge-live">LIVE</span>
                        </a>
                    </li>

                    <!-- 2. Aviator Crash -->
                    <li class="sidebar-game-entry" data-name="aviator crash flight plane multiplier 1000x">
                        <a href="{{ route('aviator.index') }}" class="sidebar-menu-item {{ request()->routeIs('aviator.*') ? 'active' : '' }}">
                            <div class="sidebar-item-icon icon-aviator">
                                <i class="fa-solid fa-plane-departure"></i>
                            </div>
                            <div class="sidebar-item-content">
                                <span class="sidebar-item-label">Aviator</span>
                                <span class="sidebar-item-sub">Unlimited Multiplier</span>
                            </div>
                            <span class="sidebar-item-badge badge-payout">1000X</span>
                        </a>
                    </li>

                    <!-- 3. WinGo Color 30s -->
                    <li class="sidebar-game-entry" data-name="wingo color prediction lottery 30s green violet red">
                        <a href="{{ route('wingo.index') }}" class="sidebar-menu-item {{ request()->routeIs('wingo.*') ? 'active' : '' }}">
                            <div class="sidebar-item-icon icon-wingo">
                                <i class="fa-solid fa-gamepad"></i>
                            </div>
                            <div class="sidebar-item-content">
                                <span class="sidebar-item-label">WinGo Color</span>
                                <span class="sidebar-item-sub">30s Fast Prediction</span>
                            </div>
                            <span class="sidebar-item-badge badge-tag">30s</span>
                        </a>
                    </li>

                    <!-- 4. 2 Digit Slot (Lottery) -->
                    <li class="sidebar-game-entry" data-name="2 digit slot lottery hourly draw 90x xa xj numbers">
                        <a href="{{ route('lottery.index') }}" class="sidebar-menu-item {{ request()->routeIs('lottery.*') ? 'active' : '' }}">
                            <div class="sidebar-item-icon icon-lottery">
                                <i class="fa-solid fa-dice"></i>
                            </div>
                            <div class="sidebar-item-content">
                                <span class="sidebar-item-label">2 Digit Slot</span>
                                <span class="sidebar-item-sub">Hourly Draws XA–XJ</span>
                            </div>
                            <span class="sidebar-item-badge badge-payout">90X</span>
                        </a>
                    </li>

                    <!-- 5. Mines 5x5 -->
                    <li class="sidebar-game-entry" data-name="mines 5x5 arcade bomb cashout coins">
                        <a href="{{ route('mines.index') }}" class="sidebar-menu-item {{ request()->routeIs('mines.*') ? 'active' : '' }}">
                            <div class="sidebar-item-icon icon-mines">
                                <i class="fa-solid fa-bomb"></i>
                            </div>
                            <div class="sidebar-item-content">
                                <span class="sidebar-item-label">Mines 5x5</span>
                                <span class="sidebar-item-sub">Uncover &amp; Cashout</span>
                            </div>
                            <span class="sidebar-item-badge badge-hot">HOT</span>
                        </a>
                    </li>

                    <!-- 6. Andar Bahar -->
                    <li class="sidebar-game-entry" data-name="andar bahar indian card game live dealer 1.95x">
                        <a href="{{ route('andar-bahar.index') }}" class="sidebar-menu-item {{ request()->routeIs('andar-bahar.*') ? 'active' : '' }}">
                            <div class="sidebar-item-icon icon-andar">
                                <i class="fa-solid fa-diamond"></i>
                            </div>
                            <div class="sidebar-item-content">
                                <span class="sidebar-item-label">Andar Bahar</span>
                                <span class="sidebar-item-sub">Live Card Dealer</span>
                            </div>
                            <span class="sidebar-item-badge badge-payout">1.95X</span>
                        </a>
                    </li>

                    <!-- 7. Teen Patti 20-20 -->
                    <li class="sidebar-game-entry" data-name="teen patti 20-20 3 card poker flush royal 40x">
                        <a href="{{ route('teen-patti.index') }}" class="sidebar-menu-item {{ request()->routeIs('teen-patti.*') ? 'active' : '' }}">
                            <div class="sidebar-item-icon icon-teenpatti">
                                <i class="fa-solid fa-heart"></i>
                            </div>
                            <div class="sidebar-item-content">
                                <span class="sidebar-item-label">Teen Patti</span>
                                <span class="sidebar-item-sub">3 Card Poker 20-20</span>
                            </div>
                            <span class="sidebar-item-badge badge-payout">40X</span>
                        </a>
                    </li>

                    <!-- 8. Dragon Tiger -->
                    <li class="sidebar-game-entry" data-name="dragon tiger 1 card battle suited tie 80x">
                        <a href="{{ route('dragon-tiger.index') }}" class="sidebar-menu-item {{ request()->routeIs('dragon-tiger.*') ? 'active' : '' }}">
                            <div class="sidebar-item-icon icon-dragontiger">
                                <i class="fa-solid fa-dragon"></i>
                            </div>
                            <div class="sidebar-item-content">
                                <span class="sidebar-item-label">Dragon Tiger</span>
                                <span class="sidebar-item-sub">1 Card Fast Battle</span>
                            </div>
                            <span class="sidebar-item-badge badge-payout">80X</span>
                        </a>
                    </li>

                    <!-- 9. Roulette 36x -->
                    <li class="sidebar-game-entry" data-name="european roulette 36x wheel live casino table">
                        <a href="{{ route('roulette.index') }}" class="sidebar-menu-item {{ request()->routeIs('roulette.*') ? 'active' : '' }}">
                            <div class="sidebar-item-icon icon-roulette">
                                <i class="fa-solid fa-dharmachakra"></i>
                            </div>
                            <div class="sidebar-item-content">
                                <span class="sidebar-item-label">Roulette 36x</span>
                                <span class="sidebar-item-sub">European Table</span>
                            </div>
                            <span class="sidebar-item-badge badge-payout">36X</span>
                        </a>
                    </li>

                    <!-- 10. Vortex Wheel -->
                    <li class="sidebar-game-entry" data-name="vortex wheel 3 ring tiers level up 200x multiplier">
                        <a href="{{ route('vortex.index') }}" class="sidebar-menu-item {{ request()->routeIs('vortex.*') ? 'active' : '' }}">
                            <div class="sidebar-item-icon icon-vortex">
                                <i class="fa-solid fa-arrows-spin"></i>
                            </div>
                            <div class="sidebar-item-content">
                                <span class="sidebar-item-label">Vortex Wheel</span>
                                <span class="sidebar-item-sub">3 Concentric Rings</span>
                            </div>
                            <span class="sidebar-item-badge badge-payout">200X</span>
                        </a>
                    </li>

                    <!-- 11. Horse Racing Derby -->
                    <li class="sidebar-game-entry" data-name="horse racing derby turf sprint 60s rounds thoroughbreds">
                        <a href="{{ route('horse-racing.index') }}" class="sidebar-menu-item {{ request()->routeIs('horse-racing.*') ? 'active' : '' }}">
                            <div class="sidebar-item-icon icon-horse">
                                <i class="fa-solid fa-horse-head"></i>
                            </div>
                            <div class="sidebar-item-content">
                                <span class="sidebar-item-label">Horse Racing</span>
                                <span class="sidebar-item-sub">60s Turf Sprint</span>
                            </div>
                            <span class="sidebar-item-badge badge-live">DERBY</span>
                        </a>
                    </li>
                </ul>
                <div id="sidebarNoGamesFound" class="text-center text-muted small py-3 d-none">
                    No matching games found
                </div>
            </div>

            <!-- Section 3: WALLET & BANKING -->
            <div class="sidebar-nav-section mb-2">
                <div class="sidebar-section-title px-2 py-1 text-uppercase">
                    <span>Banking &amp; Wallet</span>
                </div>
                <ul class="sidebar-menu-list list-unstyled mb-0">
                    <li>
                        <a href="{{ Auth::check() ? route('wallet.deposit') : route('login') }}" class="sidebar-menu-item {{ request()->routeIs('wallet.deposit') ? 'active' : '' }}">
                            <div class="sidebar-item-icon icon-deposit">
                                <i class="fa-solid fa-qrcode"></i>
                            </div>
                            <div class="sidebar-item-content">
                                <span class="sidebar-item-label">Deposit Money</span>
                                <span class="sidebar-item-sub">Instant 30s UPI QR</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ Auth::check() ? route('wallet.withdraw') : route('login') }}" class="sidebar-menu-item {{ request()->routeIs('wallet.withdraw') ? 'active' : '' }}">
                            <div class="sidebar-item-icon icon-withdraw">
                                <i class="fa-solid fa-money-bill-transfer"></i>
                            </div>
                            <div class="sidebar-item-content">
                                <span class="sidebar-item-label">Withdraw Money</span>
                                <span class="sidebar-item-sub">Direct Bank / UPI</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ Auth::check() ? route('wallet.transactions') : route('login') }}" class="sidebar-menu-item {{ request()->routeIs('wallet.transactions') ? 'active' : '' }}">
                            <div class="sidebar-item-icon icon-passbook">
                                <i class="fa-solid fa-file-invoice"></i>
                            </div>
                            <div class="sidebar-item-content">
                                <span class="sidebar-item-label">Transaction History</span>
                                <span class="sidebar-item-sub">All Deposits &amp; Cashouts</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Section 4: 24/7 CUSTOMER SUPPORT -->
            <div class="sidebar-nav-section mb-2">
                <div class="sidebar-section-title px-2 py-1 text-uppercase">
                    <span>24x7 Customer Care</span>
                </div>
                <ul class="sidebar-menu-list list-unstyled mb-0">
                    <li>
                        <a href="https://wa.me/{{ $supportWa }}?text={{ rawurlencode('Hello RupeeRush Support, I need assistance.') }}" target="_blank" rel="noopener noreferrer" class="sidebar-menu-item" title="WhatsApp 24x7 Helpdesk">
                            <div class="sidebar-item-icon icon-wa">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                            <div class="sidebar-item-content">
                                <span class="sidebar-item-label">WhatsApp Support</span>
                                <span class="sidebar-item-sub text-success font-monospace">+91 {{ $rawWa }}</span>
                            </div>
                            <span class="sidebar-item-badge badge-online">24/7</span>
                        </a>
                    </li>
                    <li>
                        <a href="mailto:{{ $supportEmail }}?subject={{ rawurlencode('RupeeRush Support Request') }}" target="_blank" rel="noopener noreferrer" class="sidebar-menu-item" title="Email Official Helpdesk">
                            <div class="sidebar-item-icon icon-mail">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div class="sidebar-item-content">
                                <span class="sidebar-item-label">Email Support</span>
                                <span class="sidebar-item-sub text-truncate">{{ $supportEmail }}</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Section 5: ACCOUNT / ADMIN & LOGOUT -->
            <div class="sidebar-nav-section mb-3">
                <div class="sidebar-section-title px-2 py-1 text-uppercase">
                    <span>Account</span>
                </div>
                <ul class="sidebar-menu-list list-unstyled mb-0">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="sidebar-menu-item sidebar-admin-item">
                                    <div class="sidebar-item-icon icon-admin">
                                        <i class="fa-solid fa-gauge-high"></i>
                                    </div>
                                    <div class="sidebar-item-content">
                                        <span class="sidebar-item-label text-danger">Admin Dashboard</span>
                                        <span class="sidebar-item-sub">System Management</span>
                                    </div>
                                </a>
                            </li>
                        @endif

                        <li>
                            <form method="POST" action="{{ route('logout') }}" id="sidebarLogoutForm">
                                @csrf
                                <button type="submit" class="sidebar-menu-item sidebar-logout-btn w-100 border-0 text-start">
                                    <div class="sidebar-item-icon icon-logout">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                    </div>
                                    <span class="sidebar-item-label text-danger">Logout Account</span>
                                </button>
                            </form>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('login') }}" class="sidebar-menu-item">
                                <div class="sidebar-item-icon icon-login">
                                    <i class="fa-solid fa-right-to-bracket"></i>
                                </div>
                                <span class="sidebar-item-label text-success">Player Login</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('register') }}" class="sidebar-menu-item">
                                <div class="sidebar-item-icon icon-register">
                                    <i class="fa-solid fa-user-plus"></i>
                                </div>
                                <span class="sidebar-item-label text-warning">Create Account</span>
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>

        </div>

        <!-- 3. Sidebar Footer: Version / Status -->
        <div class="sidebar-footer-box px-3 py-2 border-top border-white border-opacity-10 text-center">
            <div class="d-flex align-items-center justify-content-between small text-secondary" style="font-size: 0.68rem;">
                <span><i class="fa-solid fa-shield-halved text-success me-1"></i> Provably Fair</span>
                <span class="font-monospace">v2.5 • IST</span>
            </div>
        </div>

    </div>
</aside>
