@extends('layouts.app')

@section('title', 'Live Cricket Sportsbook & Exchange - rupeerush')

@section('styles')
<style>
    /* Cricket Arena Luxury Dark Theme */
    .cricket-hub-wrapper {
        color: #f1f5f9;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    
    .cricket-hero-banner {
        background: radial-gradient(circle at 80% 20%, rgba(34, 197, 94, 0.25), transparent 45%),
                    radial-gradient(circle at 10% 80%, rgba(56, 189, 248, 0.2), transparent 40%),
                    linear-gradient(135deg, #09131e 0%, #0d1b2a 50%, #06101c 100%);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 20px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.5);
    }

    .cricket-hero-banner::after {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background-image: 
            linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        background-size: 28px 28px;
        pointer-events: none;
    }

    /* Live radar pulse */
    .radar-pulse-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #22c55e;
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        animation: radarPulse 1.8s infinite;
    }

    @keyframes radarPulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1.3); box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    /* Match Card */
    .match-card {
        background: #0f172a;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 16px;
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        position: relative;
    }

    .match-card:hover {
        transform: translateY(-2px);
        border-color: rgba(34, 197, 94, 0.4);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.45);
    }

    .match-card.is-live {
        border-left: 4px solid #22c55e;
    }

    /* Ball Badges */
    .ball-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        font-size: 0.75rem;
        font-weight: 700;
        font-family: monospace;
    }

    .ball-dot { background: #334155; color: #94a3b8; }
    .ball-run { background: #1e293b; color: #f8fafc; border: 1px solid #475569; }
    .ball-four { background: #15803d; color: #ffffff; box-shadow: 0 2px 6px rgba(21, 128, 61, 0.4); }
    .ball-six { background: #7e22ce; color: #ffffff; box-shadow: 0 2px 6px rgba(126, 34, 206, 0.4); }
    .ball-wicket { background: #dc2626; color: #ffffff; animation: bounce 1s infinite alternate; }

    /* Odds Back and Lay Buttons */
    .odds-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 68px;
        height: 48px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
        padding: 4px 8px;
    }

    .odds-btn:active {
        transform: scale(0.96);
    }

    .odds-btn.btn-back {
        background: #72bbef;
        color: #0f172a;
    }

    .odds-btn.btn-back:hover {
        background: #50a8e8;
    }

    .odds-btn.btn-lay {
        background: #faa9ba;
        color: #0f172a;
    }

    .odds-btn.btn-lay:hover {
        background: #f78da4;
    }

    .odds-val {
        font-size: 0.95rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .odds-vol {
        font-size: 0.65rem;
        font-weight: 600;
        opacity: 0.75;
    }

    /* Bet Slip Modal & Panel */
    .bet-slip-card {
        background: #1e293b;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 16px;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6);
    }

    .bet-chip-btn {
        background: #334155;
        color: #f8fafc;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.8rem;
        font-weight: 700;
        transition: all 0.15s ease;
    }

    .bet-chip-btn:hover {
        background: #475569;
        color: #ffffff;
    }

    .nav-pills-sports .nav-link {
        color: #94a3b8;
        background: #0f172a;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 30px;
        padding: 8px 18px;
        font-weight: 700;
        font-size: 0.85rem;
        margin-right: 8px;
    }

    .nav-pills-sports .nav-link.active {
        color: #ffffff;
        background: #16a34a;
        border-color: #22c55e;
        box-shadow: 0 4px 14px rgba(22, 163, 74, 0.35);
    }
</style>
@endsection

@section('content')
<div class="cricket-hub-wrapper py-3">
    <!-- Top Hero Header -->
    <div class="cricket-hero-banner mb-4">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 px-3 py-1 rounded-pill fw-bold">
                        <i class="fa-solid fa-satellite-dish me-1"></i> LIVE CRICAPI EXCHANGE
                    </span>
                    <span class="badge bg-dark text-white-50 px-2 py-1 rounded-pill small font-monospace">
                        <span class="radar-pulse-dot me-1"></span> IN-PLAY FEED
                    </span>
                </div>
                <h1 class="h2 fw-black text-white mb-2 tracking-tight">Cricket Live Sports Arena</h1>
                <p class="text-white-50 mb-0 small">
                    Real-time ball-by-ball updates, live scoreboard, and instant Back & Lay market odds with automated wallet settlement.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                @auth
                    <div class="d-inline-flex flex-column align-items-lg-end p-3 rounded-4" style="background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.1);">
                        <span class="text-white-50 small mb-1">Available Sports Wallet</span>
                        <div class="h4 text-success fw-bold font-monospace mb-0" id="userWalletBalance">
                            ₹{{ number_format(Auth::user()->balance, 2) }}
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-warning rounded-pill px-4 py-2 fw-bold shadow-sm">
                        <i class="fa-solid fa-right-to-bracket me-1"></i> Login to Bet
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Category Tabs Filter -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <ul class="nav nav-pills nav-pills-sports" id="sportsTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="tab-live-btn" data-bs-toggle="pill" data-bs-target="#tab-live" type="button">
                    <i class="fa-solid fa-circle-play text-danger me-1"></i> In-Play Live ({{ count($liveMatches) }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-upcoming-btn" data-bs-toggle="pill" data-bs-target="#tab-upcoming" type="button">
                    <i class="fa-solid fa-calendar-days text-info me-1"></i> Upcoming Matches ({{ count($upcomingMatches) }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-history-btn" data-bs-toggle="pill" data-bs-target="#tab-history" type="button">
                    <i class="fa-solid fa-square-poll-vertical text-warning me-1"></i> Results & History ({{ count($completedMatches) }})
                </button>
            </li>
            @auth
            <li class="nav-item">
                <button class="nav-link" id="tab-mybets-btn" data-bs-toggle="pill" data-bs-target="#tab-mybets" type="button">
                    <i class="fa-solid fa-receipt text-success me-1"></i> My Cricket Bets
                </button>
            </li>
            @endauth
        </ul>

        <div class="d-flex align-items-center gap-2 text-white-50 small">
            <span class="radar-pulse-dot"></span>
            <span>Live Ticker: <span class="text-white fw-bold font-monospace" id="tickerTimer">Active</span></span>
        </div>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content" id="sportsTabContent">
        <!-- 1. LIVE IN-PLAY MATCHES -->
        <div class="tab-pane fade show active" id="tab-live" role="tabpanel">
            <div class="row g-3" id="liveMatchesContainer">
                @forelse($liveMatches as $match)
                    @php
                        $mkt = collect($match['markets'] ?? [])->firstWhere('market_type', 'match_winner') ?? ($match['markets'][0] ?? null);
                        $runners = $mkt['runners'] ?? [];
                        $runnerA = $runners[0] ?? null;
                        $runnerB = $runners[1] ?? null;
                    @endphp
                    <div class="col-12" id="match-card-{{ $match['id'] }}">
                        <div class="match-card is-live">
                            <div class="d-flex flex-wrap align-items-center justify-content-between pb-2 mb-3 border-bottom border-white border-opacity-10 gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-danger text-white rounded-pill px-2 py-1 fw-bold" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-circle-dot fa-fade me-1"></i> LIVE
                                    </span>
                                    <span class="text-white-50 small fw-bold text-uppercase">{{ $match['series_name'] ?? 'International T20' }}</span>
                                    <span class="badge bg-dark border border-secondary text-white-50 px-2 py-0 font-monospace text-uppercase" style="font-size: 0.65rem;">
                                        {{ $match['match_type'] ?? 'T20' }}
                                    </span>
                                </div>
                                <div class="text-white-50 small">
                                    <i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $match['venue'] ?? 'Stadium' }}
                                </div>
                            </div>

                            <div class="row align-items-center g-3">
                                <!-- Team Scores and Names -->
                                <div class="col-lg-5">
                                    <!-- Team A -->
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            @if(!empty($match['team_a_img']))
                                                <img src="{{ $match['team_a_img'] }}" alt="{{ $match['team_a'] }}" style="width: 22px; height: 16px; object-fit: cover; border-radius: 2px;">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 24px; height: 24px; font-size: 0.75rem;">
                                                    {{ substr($match['team_a'], 0, 1) }}
                                                </div>
                                            @endif
                                            <span class="fw-bold fs-6 text-white">{{ $match['team_a'] }}</span>
                                        </div>
                                        <div class="fw-bold font-monospace text-success fs-6" id="score-a-{{ $match['id'] }}">
                                            {{ $match['score_team_a'] ?? 'Yet to bat' }}
                                        </div>
                                    </div>

                                    <!-- Team B -->
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            @if(!empty($match['team_b_img']))
                                                <img src="{{ $match['team_b_img'] }}" alt="{{ $match['team_b'] }}" style="width: 22px; height: 16px; object-fit: cover; border-radius: 2px;">
                                            @else
                                                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 24px; height: 24px; font-size: 0.75rem;">
                                                    {{ substr($match['team_b'], 0, 1) }}
                                                </div>
                                            @endif
                                            <span class="fw-bold fs-6 text-white">{{ $match['team_b'] }}</span>
                                        </div>
                                        <div class="fw-bold font-monospace text-white-50 fs-6" id="score-b-{{ $match['id'] }}">
                                            {{ $match['score_team_b'] ?? 'Yet to bat' }}
                                        </div>
                                    </div>

                                    <!-- Status Summary -->
                                    <div class="small text-info fw-semibold mt-1" id="summary-{{ $match['id'] }}">
                                        <i class="fa-solid fa-comment-dots me-1"></i> {{ $match['current_summary'] ?? 'Match in progress' }}
                                    </div>
                                </div>

                                <!-- Recent Balls Timeline -->
                                <div class="col-lg-3 text-lg-center">
                                    <div class="text-white-50 small mb-2 fw-semibold">RECENT OVER BALLS</div>
                                    <div class="d-flex align-items-center justify-content-lg-center gap-1" id="balls-{{ $match['id'] }}">
                                        @if(!empty($match['recent_balls']) && is_array($match['recent_balls']))
                                            @foreach($match['recent_balls'] as $ball)
                                                @php
                                                    $ballClass = 'ball-run';
                                                    if ($ball === '0') $ballClass = 'ball-dot';
                                                    elseif ($ball === '4') $ballClass = 'ball-four';
                                                    elseif ($ball === '6') $ballClass = 'ball-six';
                                                    elseif ($ball === 'W') $ballClass = 'ball-wicket';
                                                @endphp
                                                <span class="ball-badge {{ $ballClass }}">{{ $ball }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-white-50 small">-</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Back / Lay Market Quick Odds Grid -->
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <!-- Runner A Odds -->
                                        @if($runnerA)
                                            <div class="text-center">
                                                <div class="text-white-50 small mb-1 fw-bold text-truncate" style="max-width: 80px;">
                                                    {{ $runnerA['runner_name'] }}
                                                </div>
                                                <div class="d-flex gap-1">
                                                    <button type="button" class="odds-btn btn-back" 
                                                        onclick="openBetSlip({{ $runnerA['id'] }}, '{{ addslashes($runnerA['runner_name']) }}', {{ $runnerA['back_odds'] }}, 'back', '{{ addslashes($match['title']) }}')">
                                                        <span class="odds-val">{{ number_format($runnerA['back_odds'], 2) }}</span>
                                                        <span class="odds-vol">Back</span>
                                                    </button>
                                                    <button type="button" class="odds-btn btn-lay" 
                                                        onclick="openBetSlip({{ $runnerA['id'] }}, '{{ addslashes($runnerA['runner_name']) }}', {{ $runnerA['lay_odds'] ?? $runnerA['back_odds'] }}, 'lay', '{{ addslashes($match['title']) }}')">
                                                        <span class="odds-val">{{ number_format($runnerA['lay_odds'] ?? $runnerA['back_odds'], 2) }}</span>
                                                        <span class="odds-vol">Lay</span>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Runner B Odds -->
                                        @if($runnerB)
                                            <div class="text-center">
                                                <div class="text-white-50 small mb-1 fw-bold text-truncate" style="max-width: 80px;">
                                                    {{ $runnerB['runner_name'] }}
                                                </div>
                                                <div class="d-flex gap-1">
                                                    <button type="button" class="odds-btn btn-back" 
                                                        onclick="openBetSlip({{ $runnerB['id'] }}, '{{ addslashes($runnerB['runner_name']) }}', {{ $runnerB['back_odds'] }}, 'back', '{{ addslashes($match['title']) }}')">
                                                        <span class="odds-val">{{ number_format($runnerB['back_odds'], 2) }}</span>
                                                        <span class="odds-vol">Back</span>
                                                    </button>
                                                    <button type="button" class="odds-btn btn-lay" 
                                                        onclick="openBetSlip({{ $runnerB['id'] }}, '{{ addslashes($runnerB['runner_name']) }}', {{ $runnerB['lay_odds'] ?? $runnerB['back_odds'] }}, 'lay', '{{ addslashes($match['title']) }}')">
                                                        <span class="odds-val">{{ number_format($runnerB['lay_odds'] ?? $runnerB['back_odds'], 2) }}</span>
                                                        <span class="odds-vol">Lay</span>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Link to Match Full View -->
                                    <div class="text-end mt-2">
                                        <a href="{{ route('sports.cricket.match', $match['id']) }}" class="btn btn-sm btn-success rounded-pill px-3 py-1 font-monospace fw-bold shadow-sm" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-bolt me-1"></i> Bet on Ball, Over & Match &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="text-white-50 mb-3"><i class="fa-solid fa-cricket-bat-ball fa-3x"></i></div>
                        <h5 class="text-white">No Live Matches at this moment</h5>
                        <p class="text-white-50 small">Check back soon or explore our upcoming schedule!</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 2. UPCOMING MATCHES -->
        <div class="tab-pane fade" id="tab-upcoming" role="tabpanel">
            <div class="row g-3">
                @forelse($upcomingMatches as $match)
                    <div class="col-md-6 col-lg-4">
                        <div class="match-card h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-secondary rounded-pill px-2 py-1 font-monospace small">UPCOMING</span>
                                    <span class="text-white-50 small">{{ $match['starts_at'] ? date('d M, h:i A', strtotime($match['starts_at'])) : 'Today' }}</span>
                                </div>
                                <h5 class="text-white fw-bold mb-1">{{ $match['title'] }}</h5>
                                <div class="text-white-50 small mb-3">
                                    <i class="fa-solid fa-trophy text-warning me-1"></i> {{ $match['series_name'] ?? 'Tournament' }}
                                </div>
                            </div>

                            <div>
                                <div class="p-2 rounded-3 mb-3" style="background: rgba(255,255,255,0.03);">
                                    <div class="d-flex justify-content-between small text-white-50 mb-1">
                                        <span>Venue:</span>
                                        <span class="text-light fw-semibold">{{ $match['venue'] ?? 'Stadium' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between small text-white-50">
                                        <span>Status:</span>
                                        <span class="text-warning fw-semibold">{{ $match['current_summary'] ?? 'Scheduled' }}</span>
                                    </div>
                                </div>

                                <a href="{{ route('sports.cricket.match', $match['id']) }}" class="btn btn-primary w-100 rounded-pill fw-bold" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none;">
                                    View Pre-Match Odds &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-white-50">No upcoming matches scheduled.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 3. COMPLETED MATCHES & HISTORY -->
        <div class="tab-pane fade" id="tab-history" role="tabpanel">
            <div class="row g-3">
                @forelse($completedMatches as $match)
                    <div class="col-md-6 col-lg-4">
                        <div class="match-card h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #64748b;">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom border-white border-opacity-10">
                                    <span class="badge bg-secondary rounded-pill px-2 py-1 font-monospace small">
                                        <i class="fa-solid fa-flag-checkered me-1"></i> FINAL RESULT
                                    </span>
                                    <span class="text-white-50 small">{{ $match['starts_at'] ? date('d M, h:i A', strtotime($match['starts_at'])) : 'Recent' }}</span>
                                </div>
                                <h6 class="text-white fw-bold mb-1">{{ $match['title'] }}</h6>
                                <div class="text-white-50 small mb-2">
                                    <i class="fa-solid fa-trophy text-warning me-1"></i> {{ $match['series_name'] ?? 'International Tournament' }}
                                </div>

                                <!-- Scores of both teams -->
                                <div class="p-2 rounded-3 mb-2" style="background: rgba(255,255,255,0.03);">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-bold {{ (!empty($match['winner_team']) && stripos($match['winner_team'], $match['team_a']) !== false) ? 'text-success' : 'text-light' }}">
                                            @if(!empty($match['winner_team']) && stripos($match['winner_team'], $match['team_a']) !== false)
                                                <i class="fa-solid fa-crown text-warning me-1"></i>
                                            @endif
                                            {{ $match['team_a'] }}
                                        </span>
                                        <span class="font-monospace fw-bold text-light small">{{ $match['score_team_a'] ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small fw-bold {{ (!empty($match['winner_team']) && stripos($match['winner_team'], $match['team_b']) !== false) ? 'text-success' : 'text-light' }}">
                                            @if(!empty($match['winner_team']) && stripos($match['winner_team'], $match['team_b']) !== false)
                                                <i class="fa-solid fa-crown text-warning me-1"></i>
                                            @endif
                                            {{ $match['team_b'] }}
                                        </span>
                                        <span class="font-monospace fw-bold text-light small">{{ $match['score_team_b'] ?? '-' }}</span>
                                    </div>
                                </div>

                                <!-- Result Statement Banner -->
                                <div class="p-2 rounded-3 mb-3 border border-warning border-opacity-25" style="background: rgba(234, 179, 8, 0.08);">
                                    <div class="small fw-bold text-warning text-truncate">
                                        <i class="fa-solid fa-medal me-1"></i> {{ $match['current_summary'] ?? ($match['winner_team'] ? $match['winner_team'].' won' : 'Match Concluded') }}
                                    </div>
                                </div>
                            </div>

                            <div>
                                <a href="{{ route('sports.cricket.match', $match['id']) }}" class="btn btn-outline-secondary w-100 rounded-pill font-monospace small fw-bold">
                                    View Scorecard & Results &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-white-50">No completed match history found.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 4. MY BETS -->
        @auth
        <div class="tab-pane fade" id="tab-mybets" role="tabpanel">
            <div class="card border-0 rounded-4 shadow-sm" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.08) !important;">
                <div class="card-body p-3 p-md-4">
                    <h5 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-list-check text-success"></i> My Cricket Bets
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                            <thead>
                                <tr class="text-white-50 small border-secondary">
                                    <th>BET ID</th>
                                    <th>MATCH & MARKET</th>
                                    <th>SELECTION</th>
                                    <th>TYPE</th>
                                    <th>ODDS</th>
                                    <th>STAKE</th>
                                    <th>POTENTIAL WIN</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody id="myBetsTableBody">
                                @forelse($myBets as $b)
                                    <tr>
                                        <td class="font-monospace small text-white-50">{{ $b['bet_id'] }}</td>
                                        <td>
                                            <div class="fw-bold text-white">{{ $b['match']['title'] ?? 'Cricket Match' }}</div>
                                            <div class="small text-white-50">{{ $b['market']['market_name'] ?? 'Market' }}</div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-light">{{ $b['runner_name'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $b['bet_type'] === 'lay' ? 'bg-danger' : 'bg-primary' }} text-uppercase">
                                                {{ $b['bet_type'] }}
                                            </span>
                                        </td>
                                        <td class="font-monospace fw-bold text-warning">{{ number_format($b['odds'], 2) }}</td>
                                        <td class="font-monospace text-light">₹{{ number_format($b['stake'], 2) }}</td>
                                        <td class="font-monospace fw-bold text-success">₹{{ number_format($b['potential_payout'], 2) }}</td>
                                        <td>
                                            @if($b['status'] === 'won')
                                                <span class="badge bg-success rounded-pill px-2 py-1">WON (+₹{{ number_format($b['win_amount'], 2) }})</span>
                                            @elseif($b['status'] === 'lost')
                                                <span class="badge bg-secondary rounded-pill px-2 py-1">LOST</span>
                                            @else
                                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1">IN PLAY</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-white-50 py-4">No active cricket bets placed yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endauth
    </div>
</div>

<!-- Quick Interactive Bet Slip Modal -->
<div class="modal fade" id="betSlipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content bet-slip-card text-white">
            <div class="modal-header border-bottom border-white border-opacity-10 py-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill px-2 py-1 fw-bold" id="slipBetTypeBadge">BACK</span>
                    <h6 class="modal-title fw-bold mb-0" id="slipSelectionName">India</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="small text-white-50 mb-2" id="slipMatchTitle">India vs Australia</div>
                
                <!-- Odds Display -->
                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 mb-3" style="background: rgba(255,255,255,0.05);">
                    <span class="text-white-50 small">Odds:</span>
                    <span class="h5 fw-bold font-monospace text-warning mb-0" id="slipOddsValue">1.90</span>
                </div>

                <!-- Stake Input -->
                <div class="mb-3">
                    <label class="form-label small text-white-50 fw-semibold mb-1">STAKE AMOUNT (₹)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-white-50">₹</span>
                        <input type="number" class="form-control bg-dark border-secondary text-white fw-bold font-monospace" id="slipStakeInput" value="100" min="10" max="100000" oninput="calculatePayout()">
                    </div>
                </div>

                <!-- Quick Stake Chips -->
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <button type="button" class="bet-chip-btn flex-fill" onclick="setStake(100)">+100</button>
                    <button type="button" class="bet-chip-btn flex-fill" onclick="setStake(500)">+500</button>
                    <button type="button" class="bet-chip-btn flex-fill" onclick="setStake(1000)">+1K</button>
                    <button type="button" class="bet-chip-btn flex-fill" onclick="setStake(5000)">+5K</button>
                    <button type="button" class="bet-chip-btn flex-fill" onclick="setStake(10000)">+10K</button>
                </div>

                <!-- Payout Calculation Box -->
                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 mb-3 border border-success border-opacity-25" style="background: rgba(34, 197, 94, 0.08);">
                    <div>
                        <div class="text-white-50 small" style="font-size: 0.72rem;">NET PROFIT</div>
                        <div class="font-monospace text-success fw-bold" id="slipNetProfit">₹90.00</div>
                    </div>
                    <div class="text-end">
                        <div class="text-white-50 small" style="font-size: 0.72rem;">TOTAL RETURN</div>
                        <span class="h6 fw-bold font-monospace text-success mb-0" id="slipPayoutText">₹190.00</span>
                    </div>
                </div>

                <!-- Error / Success Notice -->
                <div class="alert alert-danger py-1 px-2 small d-none" id="slipAlertBox"></div>

                <!-- Submit Button -->
                <button type="button" class="btn btn-success w-100 rounded-pill py-2 fw-bold" id="btnPlaceBet" onclick="submitCricketBet()">
                    <i class="fa-solid fa-check-circle me-1"></i> PLACE BET NOW
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let activeSlipData = {
        runner_id: null,
        runner_name: '',
        odds: 1.0,
        bet_type: 'back',
        match_title: ''
    };

    let betModal = null;

    document.addEventListener('DOMContentLoaded', function () {
        betModal = new bootstrap.Modal(document.getElementById('betSlipModal'));
        
        // Start polling matches every 4 seconds for live scores & odds
        setInterval(pollLiveMatches, 4000);
    });

    function openBetSlip(runnerId, runnerName, odds, betType, matchTitle) {
        @guest
            window.location.href = "{{ route('login') }}";
            return;
        @endguest

        activeSlipData = {
            runner_id: runnerId,
            runner_name: runnerName,
            odds: parseFloat(odds),
            bet_type: betType,
            match_title: matchTitle
        };

        const badge = document.getElementById('slipBetTypeBadge');
        if (betType === 'lay') {
            badge.className = 'badge bg-danger rounded-pill px-2 py-1 fw-bold';
            badge.textContent = 'LAY';
        } else {
            badge.className = 'badge bg-primary rounded-pill px-2 py-1 fw-bold';
            badge.textContent = 'BACK';
        }

        document.getElementById('slipSelectionName').textContent = runnerName;
        document.getElementById('slipMatchTitle').textContent = matchTitle;
        document.getElementById('slipOddsValue').textContent = activeSlipData.odds.toFixed(2);
        document.getElementById('slipAlertBox').classList.add('d-none');
        
        calculatePayout();
        betModal.show();
    }

    function setStake(amount) {
        const input = document.getElementById('slipStakeInput');
        const curr = parseFloat(input.value) || 0;
        input.value = curr + amount;
        calculatePayout();
    }

    function calculatePayout() {
        const stake = parseFloat(document.getElementById('slipStakeInput').value) || 0;
        const potential = (stake * activeSlipData.odds).toFixed(2);
        const profit = Math.max(0, (stake * (activeSlipData.odds - 1))).toFixed(2);
        document.getElementById('slipPayoutText').textContent = '₹' + potential;
        const profitEl = document.getElementById('slipNetProfit');
        if (profitEl) profitEl.textContent = '₹' + profit;
    }

    async function submitCricketBet() {
        const stake = parseFloat(document.getElementById('slipStakeInput').value) || 0;
        const alertBox = document.getElementById('slipAlertBox');
        const btn = document.getElementById('btnPlaceBet');

        if (stake < 10) {
            alertBox.textContent = 'Minimum stake amount is ₹10.';
            alertBox.classList.remove('d-none');
            return;
        }

        alertBox.classList.add('d-none');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Placing Bet...';

        try {
            const response = await fetch("{{ route('api.sports.cricket.place-bet') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    runner_id: activeSlipData.runner_id,
                    stake: stake,
                    bet_type: activeSlipData.bet_type
                })
            });

            const data = await response.json();

            if (data.success) {
                // Update wallet balance in header and hero
                if (data.new_balance !== undefined) {
                    const balFormatted = '₹' + parseFloat(data.new_balance).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    const walletEl = document.getElementById('userWalletBalance');
                    if (walletEl) walletEl.textContent = balFormatted;
                    
                    const headerBal = document.getElementById('headerWalletBalance');
                    if (headerBal) headerBal.textContent = balFormatted;
                }

                betModal.hide();
                alert('Cricket bet placed successfully on ' + activeSlipData.runner_name + ' @ ' + activeSlipData.odds.toFixed(2) + '!');
                
                // Refresh bets
                fetchMyBets();
            } else {
                alertBox.textContent = data.message || 'Failed to place bet.';
                alertBox.classList.remove('d-none');
            }
        } catch (e) {
            alertBox.textContent = 'Network error. Please try again.';
            alertBox.classList.remove('d-none');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check-circle me-1"></i> PLACE BET NOW';
        }
    }

    async function pollLiveMatches() {
        try {
            const res = await fetch("{{ route('api.sports.cricket.matches') }}?filter=live");
            if (!res.ok) return;
            const data = await res.json();
            
            if (data.success && Array.isArray(data.matches)) {
                data.matches.forEach(m => {
                    const scoreA = document.getElementById(`score-a-${m.id}`);
                    const scoreB = document.getElementById(`score-b-${m.id}`);
                    const summary = document.getElementById(`summary-${m.id}`);
                    const ballsContainer = document.getElementById(`balls-${m.id}`);

                    if (scoreA && m.score_team_a) scoreA.textContent = m.score_team_a;
                    if (scoreB && m.score_team_b) scoreB.textContent = m.score_team_b;
                    if (summary && m.current_summary) summary.innerHTML = `<i class="fa-solid fa-comment-dots me-1"></i> ${m.current_summary}`;

                    if (ballsContainer && Array.isArray(m.recent_balls)) {
                        let html = '';
                        m.recent_balls.forEach(b => {
                            let cls = 'ball-run';
                            if (b === '0') cls = 'ball-dot';
                            else if (b === '4') cls = 'ball-four';
                            else if (b === '6') cls = 'ball-six';
                            else if (b === 'W') cls = 'ball-wicket';
                            html += `<span class="ball-badge ${cls}">${b}</span>`;
                        });
                        ballsContainer.innerHTML = html;
                    }
                });
            }
        } catch (e) {
            // silent catch on network glitch
        }
    }

    async function fetchMyBets() {
        @auth
        try {
            const res = await fetch("{{ route('api.sports.cricket.my-bets') }}");
            if (!res.ok) return;
            const data = await res.json();
            
            if (data.success && Array.isArray(data.bets)) {
                const tbody = document.getElementById('myBetsTableBody');
                if (!tbody) return;

                if (data.bets.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-white-50 py-4">No active cricket bets placed yet.</td></tr>';
                    return;
                }

                let rows = '';
                data.bets.forEach(b => {
                    const badgeType = b.bet_type === 'lay' ? 'bg-danger' : 'bg-primary';
                    let statusBadge = '<span class="badge bg-warning text-dark rounded-pill px-2 py-1">IN PLAY</span>';
                    if (b.status === 'won') {
                        statusBadge = `<span class="badge bg-success rounded-pill px-2 py-1">WON (+₹${parseFloat(b.win_amount).toFixed(2)})</span>`;
                    } else if (b.status === 'lost') {
                        statusBadge = '<span class="badge bg-secondary rounded-pill px-2 py-1">LOST</span>';
                    }

                    rows += `
                        <tr>
                            <td class="font-monospace small text-white-50">${b.bet_id}</td>
                            <td>
                                <div class="fw-bold text-white">${b.match ? b.match.title : 'Cricket Match'}</div>
                                <div class="small text-white-50">${b.market ? b.market.market_name : 'Market'}</div>
                            </td>
                            <td><span class="fw-bold text-light">${b.runner_name}</span></td>
                            <td><span class="badge ${badgeType} text-uppercase">${b.bet_type}</span></td>
                            <td class="font-monospace fw-bold text-warning">${parseFloat(b.odds).toFixed(2)}</td>
                            <td class="font-monospace text-light">₹${parseFloat(b.stake).toFixed(2)}</td>
                            <td class="font-monospace fw-bold text-success">₹${parseFloat(b.potential_payout).toFixed(2)}</td>
                            <td>${statusBadge}</td>
                        </tr>
                    `;
                });
                tbody.innerHTML = rows;
            }
        } catch (e) {}
        @endauth
    }
</script>
@endsection

