@extends('layouts.app')

@section('title', $match->title . ' - Cricket Live Arena')

@section('styles')
<style>
    .match-arena-wrapper {
        color: #f1f5f9;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    /* 2D Digital Pitch & Live Scoreboard Arena */
    .arena-hero-banner {
        background: radial-gradient(circle at 50% 0%, rgba(16, 185, 129, 0.18), transparent 75%),
                    linear-gradient(135deg, #060e17 0%, #0c1a29 50%, #050d18 100%);
        border: 1px solid rgba(16, 185, 129, 0.35);
        border-radius: 20px;
        padding: 22px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6);
        position: relative;
        overflow: hidden;
    }

    /* 2D Cricket Pitch Field Graphic */
    .pitch-visual-box {
        background: radial-gradient(ellipse at center, #1b4332 0%, #081c15 80%);
        border: 1px solid rgba(74, 222, 128, 0.3);
        border-radius: 16px;
        padding: 16px;
        position: relative;
        overflow: hidden;
        min-height: 140px;
        box-shadow: inset 0 0 30px rgba(0, 0, 0, 0.6);
    }

    .pitch-strip {
        position: absolute;
        top: 20px;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 65px;
        background: linear-gradient(180deg, #d4a373 0%, #faedcd 50%, #d4a373 100%);
        border-radius: 4px;
        opacity: 0.85;
        box-shadow: 0 0 15px rgba(212, 163, 115, 0.4);
    }

    .crease-line {
        position: absolute;
        left: 0;
        right: 0;
        height: 2px;
        background: #ffffff;
        opacity: 0.9;
    }

    .crease-top { top: 12px; }
    .crease-bottom { bottom: 12px; }

    /* Animated Ball in Flight */
    .live-ball-dot {
        position: absolute;
        width: 12px;
        height: 12px;
        background: #ef4444;
        border: 2px solid #ffffff;
        border-radius: 50%;
        left: 50%;
        transform: translateX(-50%);
        box-shadow: 0 0 12px #ef4444;
        transition: all 0.5s ease-out;
    }

    .ball-at-bowler { top: 22px; }
    .ball-at-batsman { bottom: 22px; transform: translateX(-50%) scale(1.3); }

    /* Live Status Radar */
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

    /* Scoreboard Stats Cards */
    .stats-card-box {
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        padding: 12px 16px;
    }

    /* Ball Badges */
    .ball-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        font-size: 0.78rem;
        font-weight: 800;
        font-family: monospace;
    }

    .ball-dot { background: #334155; color: #94a3b8; }
    .ball-run { background: #1e293b; color: #f8fafc; border: 1px solid #475569; }
    .ball-four { background: #16a34a; color: #ffffff; box-shadow: 0 2px 8px rgba(22, 163, 74, 0.5); }
    .ball-six { background: #9333ea; color: #ffffff; box-shadow: 0 2px 8px rgba(147, 51, 234, 0.5); }
    .ball-wicket { background: #dc2626; color: #ffffff; animation: pulseWicket 1s infinite alternate; }

    @keyframes pulseWicket {
        from { transform: scale(1); }
        to { transform: scale(1.15); box-shadow: 0 0 10px #dc2626; }
    }

    /* Market Cards */
    .market-box {
        background: #0f172a;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 20px;
        position: relative;
        transition: border-color 0.2s ease;
    }

    .market-box:hover {
        border-color: rgba(34, 197, 94, 0.3);
    }

    .market-header {
        background: rgba(255, 255, 255, 0.03);
        padding: 12px 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .market-suspended-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(2px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 5;
        border-radius: 16px;
    }

    .runner-row {
        padding: 12px 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: background 0.15s ease;
    }

    .runner-row:hover {
        background: rgba(255, 255, 255, 0.02);
    }

    /* Odds Back / Lay Buttons */
    .odds-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 72px;
        height: 50px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: transform 0.12s ease, background 0.15s ease, box-shadow 0.15s ease;
        padding: 4px 8px;
    }

    .odds-btn:active {
        transform: scale(0.95);
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
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .odds-vol {
        font-size: 0.68rem;
        font-weight: 600;
        opacity: 0.75;
    }

    /* Odds Flashing Animations */
    .odds-flash-up {
        animation: flashGreen 1s ease-in-out;
    }

    .odds-flash-down {
        animation: flashRed 1s ease-in-out;
    }

    @keyframes flashGreen {
        0% { box-shadow: 0 0 14px #22c55e; transform: scale(1.05); }
        100% { box-shadow: none; transform: scale(1); }
    }

    @keyframes flashRed {
        0% { box-shadow: 0 0 14px #ef4444; transform: scale(1.05); }
        100% { box-shadow: none; transform: scale(1); }
    }

    /* Market Category Navigation */
    .market-nav-pill {
        color: #94a3b8;
        background: #0f172a;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 30px;
        padding: 8px 18px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .market-nav-pill:hover, .market-nav-pill.active {
        color: #ffffff;
        background: #16a34a;
        border-color: #22c55e;
        box-shadow: 0 4px 14px rgba(22, 163, 74, 0.35);
    }

    /* Sticky Sidebar Bet Slip (Desktop) */
    .sticky-bet-slip {
        position: sticky;
        top: 85px;
        background: #1e293b;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.5);
    }

    .bet-chip-btn {
        background: #334155;
        color: #f8fafc;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 0.8rem;
        font-weight: 700;
        transition: all 0.15s ease;
    }

    .bet-chip-btn:hover {
        background: #475569;
        color: #ffffff;
    }

    /* Mobile Floating Drawer (Bottom Sheet) */
    @media (max-width: 991.98px) {
        .sticky-bet-slip {
            position: fixed;
            top: auto;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            border-radius: 24px 24px 0 0;
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.7);
            max-height: 85vh;
            overflow-y: auto;
            transform: translateY(105%);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .sticky-bet-slip.is-open {
            transform: translateY(0);
        }

        .mobile-drawer-backdrop {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.65);
            z-index: 1040;
            display: none;
        }

        .mobile-drawer-backdrop.is-open {
            display: block;
        }
    }
</style>
@endsection

@section('content')
<div class="match-arena-wrapper py-3">
    <!-- Breadcrumb back link -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ route('sports.cricket') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 font-monospace">
            &larr; Back to Cricket Lobby
        </a>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 px-3 py-1 rounded-pill small font-monospace">
                <span class="radar-pulse-dot me-1"></span> LIVE TICKER: <span id="tickerStatus">SYNCED</span>
            </span>
        </div>
    </div>

    <!-- Match Live Scoreboard Arena Banner -->
    <div class="arena-hero-banner mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between pb-3 mb-3 border-bottom border-white border-opacity-10 gap-2">
            <div>
                @if($match->status === 'completed')
                    <span class="badge bg-secondary rounded-pill px-3 py-1 fw-bold">
                        <i class="fa-solid fa-flag-checkered me-1"></i> FINAL RESULT
                    </span>
                    @if(!empty($match->winner_team))
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 ms-2 px-3 py-1 rounded-pill fw-bold">
                            <i class="fa-solid fa-trophy text-warning me-1"></i> WINNER: {{ $match->winner_team }}
                        </span>
                    @endif
                @elseif($match->is_live || $match->status === 'live')
                    <span class="badge bg-danger rounded-pill px-3 py-1 fw-bold">
                        <i class="fa-solid fa-circle-dot fa-fade me-1"></i> LIVE IN-PLAY
                    </span>
                @else
                    <span class="badge bg-info text-dark rounded-pill px-3 py-1 fw-bold">
                        <i class="fa-solid fa-calendar-days me-1"></i> UPCOMING SCHEDULED
                    </span>
                @endif
                <span class="text-white-50 ms-2 small fw-bold">{{ $match->series_name ?? 'Tournament Series' }}</span>
                <span class="badge bg-dark border border-secondary text-white-50 ms-1 px-2 py-0 font-monospace text-uppercase" style="font-size: 0.68rem;">
                    {{ $match->match_type }}
                </span>
            </div>
            <div class="text-white-50 small">
                <i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $match->venue ?? 'International Stadium' }}
            </div>
        </div>

        <!-- Team Scores Display -->
        <div class="row align-items-center py-2 g-3">
            <!-- Team A -->
            <div class="col-md-5">
                <div class="d-flex align-items-center justify-content-between p-3 rounded-4" style="background: rgba(15, 23, 42, 0.75); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="d-flex align-items-center gap-3">
                        @if($match->team_a_img)
                            <img src="{{ $match->team_a_img }}" alt="{{ $match->team_a }}" style="width: 32px; height: 22px; object-fit: cover; border-radius: 4px;">
                        @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">
                                {{ substr($match->team_a, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <span class="h5 fw-bold text-white mb-0">{{ $match->team_a }}</span>
                            <span class="badge bg-dark text-white-50 ms-1 font-monospace small">{{ $match->team_a_short ?? 'T1' }}</span>
                        </div>
                    </div>
                    <div class="h4 fw-bold font-monospace text-success mb-0" id="liveScoreA">
                        {{ $match->score_team_a ?? 'Yet to bat' }}
                    </div>
                </div>
            </div>

            <!-- VS Badge -->
            <div class="col-md-2 text-center">
                <span class="badge bg-dark border border-secondary text-white-50 px-3 py-1 rounded-pill fw-bold">VS</span>
            </div>

            <!-- Team B -->
            <div class="col-md-5">
                <div class="d-flex align-items-center justify-content-between p-3 rounded-4" style="background: rgba(15, 23, 42, 0.75); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="d-flex align-items-center gap-3">
                        @if($match->team_b_img)
                            <img src="{{ $match->team_b_img }}" alt="{{ $match->team_b }}" style="width: 32px; height: 22px; object-fit: cover; border-radius: 4px;">
                        @else
                            <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">
                                {{ substr($match->team_b, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <span class="h5 fw-bold text-white mb-0">{{ $match->team_b }}</span>
                            <span class="badge bg-dark text-white-50 ms-1 font-monospace small">{{ $match->team_b_short ?? 'T2' }}</span>
                        </div>
                    </div>
                    <div class="h4 fw-bold font-monospace text-white-50 mb-0" id="liveScoreB">
                        {{ $match->score_team_b ?? 'Yet to bat' }}
                    </div>
                </div>
            </div>
        </div>

        @php
            $meta = $match->meta_data ?? [];
            $striker = $meta['batsman_striker'] ?? ['name' => 'Striker', 'runs' => 45, 'balls' => 28, 'fours' => 4, 'sixes' => 2, 'sr' => 160.7];
            $nonStriker = $meta['batsman_non_striker'] ?? ['name' => 'Non-Striker', 'runs' => 22, 'balls' => 14, 'fours' => 2, 'sixes' => 1, 'sr' => 157.1];
            $bowler = $meta['current_bowler'] ?? ['name' => 'Bowler', 'overs' => '3.2', 'maidens' => 0, 'runs' => 26, 'wickets' => 1, 'econ' => 7.8];
            $lastEvent = $meta['last_event'] ?? 'PLAY';
            $countdown = $meta['ball_countdown'] ?? 8;
            $phase = $meta['ball_phase'] ?? 'betting_open';
        @endphp

        <!-- 2D Pitch Animation & Commentary Box -->
        <div class="row g-3 mt-1">
            <!-- Left: 2D Interactive Pitch & Action -->
            <div class="col-lg-5">
                <div class="pitch-visual-box d-flex flex-column justify-content-between h-100">
                    <div class="pitch-strip">
                        <div class="crease-line crease-top"></div>
                        <div class="crease-line crease-bottom"></div>
                        <div class="live-ball-dot {{ $phase === 'ball_in_play' ? 'ball-at-batsman' : 'ball-at-bowler' }}" id="liveBallDot"></div>
                    </div>

                    <div class="d-flex justify-content-between position-relative" style="z-index: 2;">
                        <span class="badge bg-dark bg-opacity-75 border border-success border-opacity-50 text-success small font-monospace">
                            <i class="fa-solid fa-baseball me-1"></i> <span id="pitchBowlerName">{{ $bowler['name'] }}</span>
                        </span>
                        <span class="badge {{ $phase === 'ball_in_play' ? 'bg-danger' : 'bg-success' }} px-3 py-1 font-monospace fw-bold" id="ballPhaseBadge">
                            {{ $phase === 'ball_in_play' ? 'BALL IN PLAY' : 'BETTING OPEN: ' . $countdown . 's' }}
                        </span>
                    </div>

                    <div class="text-center position-relative my-2" style="z-index: 2;">
                        <span class="badge bg-dark bg-opacity-90 border border-warning text-warning fs-6 px-3 py-1 fw-bold tracking-wider" id="lastEventBanner">
                            {{ $lastEvent }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between position-relative" style="z-index: 2;">
                        <span class="badge bg-dark bg-opacity-75 text-white-50 small font-monospace">
                            <i class="fa-solid fa-person-running me-1"></i> <span id="pitchStrikerName">{{ $striker['name'] }}*</span>
                        </span>
                        <span class="badge bg-dark bg-opacity-75 text-info small font-monospace" id="matchEquationText">
                            {{ $match->current_summary ?? 'Match in progress' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right: Live Batsmen & Bowler Stats Cards -->
            <div class="col-lg-7">
                <div class="stats-card-box h-100 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Batsmen Table -->
                        <div class="table-responsive mb-2">
                            <table class="table table-sm table-dark table-borderless align-middle mb-0" style="background: transparent;">
                                <thead>
                                    <tr class="text-white-50 border-bottom border-secondary small" style="font-size: 0.72rem;">
                                        <th>BATSMAN</th>
                                        <th class="text-end">R</th>
                                        <th class="text-end">B</th>
                                        <th class="text-end">4s</th>
                                        <th class="text-end">6s</th>
                                        <th class="text-end">SR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="rowStriker">
                                        <td class="fw-bold text-success">
                                            <i class="fa-solid fa-bat-ball me-1"></i> <span id="statStrikerName">{{ $striker['name'] }}</span>*
                                        </td>
                                        <td class="text-end font-monospace fw-bold" id="statStrikerR">{{ $striker['runs'] }}</td>
                                        <td class="text-end font-monospace text-white-50" id="statStrikerB">{{ $striker['balls'] }}</td>
                                        <td class="text-end font-monospace text-white-50" id="statStriker4s">{{ $striker['fours'] }}</td>
                                        <td class="text-end font-monospace text-white-50" id="statStriker6s">{{ $striker['sixes'] }}</td>
                                        <td class="text-end font-monospace text-warning fw-bold" id="statStrikerSR">{{ $striker['sr'] }}</td>
                                    </tr>
                                    <tr id="rowNonStriker">
                                        <td class="text-light">
                                            <span id="statNonStrikerName">{{ $nonStriker['name'] }}</span>
                                        </td>
                                        <td class="text-end font-monospace fw-bold" id="statNonStrikerR">{{ $nonStriker['runs'] }}</td>
                                        <td class="text-end font-monospace text-white-50" id="statNonStrikerB">{{ $nonStriker['balls'] }}</td>
                                        <td class="text-end font-monospace text-white-50" id="statNonStriker4s">{{ $nonStriker['fours'] }}</td>
                                        <td class="text-end font-monospace text-white-50" id="statNonStriker6s">{{ $nonStriker['sixes'] }}</td>
                                        <td class="text-end font-monospace text-white-50" id="statNonStrikerSR">{{ $nonStriker['sr'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Bowler Strip -->
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 small font-monospace" style="background: rgba(255,255,255,0.04);">
                            <div class="fw-bold text-info">
                                <i class="fa-solid fa-baseball me-1"></i> <span id="statBowlerName">{{ $bowler['name'] }}</span>
                            </div>
                            <div class="d-flex gap-3 text-white-50">
                                <span>O: <strong class="text-light" id="statBowlerO">{{ $bowler['overs'] }}</strong></span>
                                <span>M: <strong class="text-light" id="statBowlerM">{{ $bowler['maidens'] }}</strong></span>
                                <span>R: <strong class="text-light" id="statBowlerR">{{ $bowler['runs'] }}</strong></span>
                                <span>W: <strong class="text-danger fw-bold" id="statBowlerW">{{ $bowler['wickets'] }}</strong></span>
                                <span>ECON: <strong class="text-warning" id="statBowlerEcon">{{ $bowler['econ'] }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Live Commentary Line -->
                    <div class="small text-white-50 fst-italic mt-2 border-top border-secondary pt-2" id="liveCommentaryText">
                        <i class="fa-solid fa-quote-left text-secondary me-1"></i> {{ $meta['last_ball_commentary'] ?? 'Match in progress.' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Balls Timeline Bar -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 pt-3 border-top border-white border-opacity-10">
            <div class="d-flex align-items-center gap-2">
                <span class="text-white-50 small fw-bold font-monospace">RECENT BALLS:</span>
                <div class="d-inline-flex align-items-center gap-1" id="liveRecentBalls">
                    @if(!empty($match->recent_balls) && is_array($match->recent_balls))
                        @foreach($match->recent_balls as $ball)
                            @php
                                $ballClass = 'ball-run';
                                if ($ball === '0') $ballClass = 'ball-dot';
                                elseif ($ball === '4') $ballClass = 'ball-four';
                                elseif ($ball === '6') $ballClass = 'ball-six';
                                elseif ($ball === 'W') $ballClass = 'ball-wicket';
                            @endphp
                            <span class="ball-badge {{ $ballClass }}">{{ $ball }}</span>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 font-monospace small text-white-50">
                <span>CRR: <strong class="text-light" id="statCRR">{{ $meta['crr'] ?? '8.4' }}</strong></span>
                <span>RRR: <strong class="text-warning" id="statRRR">{{ $meta['rrr'] ?? '9.2' }}</strong></span>
                <span>TARGET: <strong class="text-success" id="statTarget">{{ $meta['target'] ?? '187' }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Markets Filter Tabs -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-2 flex-wrap" id="marketFilterTabs">
            <button type="button" class="market-nav-pill active" onclick="filterMarkets('all')">
                <i class="fa-solid fa-layer-group me-1"></i> All Markets ({{ count($match->markets) }})
            </button>
            <button type="button" class="market-nav-pill" onclick="filterMarkets('match_winner')">
                <i class="fa-solid fa-trophy text-warning me-1"></i> Match Winner (Win/Loss)
            </button>
            <button type="button" class="market-nav-pill" onclick="filterMarkets('next_ball')">
                <i class="fa-solid fa-baseball text-danger me-1"></i> Next Ball (Ball by Ball)
            </button>
            <button type="button" class="market-nav-pill" onclick="filterMarkets('over_runs')">
                <i class="fa-solid fa-clock-rotate-left text-info me-1"></i> Over Session Runs
            </button>
            <button type="button" class="market-nav-pill" onclick="filterMarkets('fancy')">
                <i class="fa-solid fa-star text-warning me-1"></i> Specials & Fancy
            </button>
        </div>

        <div class="small text-white-50 font-monospace">
            Back = <span style="color: #72bbef; font-weight: bold;">LAGAI</span> | Lay = <span style="color: #faa9ba; font-weight: bold;">KHAI</span>
        </div>
    </div>

    <!-- Main Content Grid: Left Markets, Right Sticky Bet Slip -->
    <div class="row g-4">
        <!-- Left Column: Betting Markets -->
        <div class="col-lg-8" id="marketsContainer">
            @forelse($match->markets as $mkt)
                @php
                    $isSuspended = ($mkt->status === 'suspended');
                @endphp
                <div class="market-box market-item" data-market-type="{{ $mkt->market_type }}" id="market-box-{{ $mkt->id }}">
                    <!-- Suspended Overlay if in play -->
                    <div class="market-suspended-overlay {{ $isSuspended ? '' : 'd-none' }}" id="suspended-overlay-{{ $mkt->id }}">
                        <span class="badge bg-danger fs-6 px-3 py-2 font-monospace fw-bold">
                            <i class="fa-solid fa-lock me-1"></i> SUSPENDED / BALL IN PLAY
                        </span>
                    </div>

                    <div class="market-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid {{ $mkt->market_type === 'next_ball' ? 'fa-baseball text-danger' : ($mkt->market_type === 'match_winner' ? 'fa-trophy text-warning' : 'fa-chart-line text-success') }}"></i>
                            <span class="fw-bold text-white fs-6" id="market-name-{{ $mkt->id }}">{{ $mkt->market_name }}</span>
                            <span class="badge {{ $isSuspended ? 'bg-danger' : 'bg-success bg-opacity-25 text-success border border-success border-opacity-50' }} px-2 py-0 small font-monospace" id="market-status-badge-{{ $mkt->id }}">
                                {{ strtoupper($mkt->status) }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-4 text-white-50 small fw-bold font-monospace pe-2">
                            <span style="color: #72bbef;">BACK</span>
                            <span style="color: #faa9ba;">LAY</span>
                        </div>
                    </div>

                    <div class="market-runners-list" id="runners-list-{{ $mkt->id }}">
                        @foreach($mkt->runners as $r)
                            <div class="runner-row" id="runner-row-{{ $r->id }}">
                                <div>
                                    <div class="fw-bold text-white fs-6" id="runner-name-{{ $r->id }}">{{ $r->runner_name }}</div>
                                    <div class="text-white-50 small font-monospace">
                                        Vol: ₹{{ number_format($r->back_volume + $r->lay_volume) }}
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <!-- BACK -->
                                    <button type="button" class="odds-btn btn-back" id="btn-back-{{ $r->id }}"
                                        onclick="openInteractiveSlip({{ $r->id }}, '{{ addslashes($r->runner_name) }}', {{ $r->back_odds }}, 'back', '{{ addslashes($mkt->market_name) }}')">
                                        <span class="odds-val" id="back-odds-{{ $r->id }}">{{ number_format($r->back_odds, 2) }}</span>
                                        <span class="odds-vol">Back</span>
                                    </button>

                                    <!-- LAY -->
                                    <button type="button" class="odds-btn btn-lay" id="btn-lay-{{ $r->id }}"
                                        onclick="openInteractiveSlip({{ $r->id }}, '{{ addslashes($r->runner_name) }}', {{ $r->lay_odds ?? $r->back_odds }}, 'lay', '{{ addslashes($mkt->market_name) }}')">
                                        <span class="odds-val" id="lay-odds-{{ $r->id }}">{{ number_format($r->lay_odds ?? $r->back_odds, 2) }}</span>
                                        <span class="odds-vol">Lay</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="p-4 rounded-4 text-center text-white-50" style="background: #0f172a;">
                    Markets are opening shortly.
                </div>
            @endforelse

            <!-- Match Active & Settled Bets -->
            @auth
            <div class="card border-0 rounded-4 shadow-sm mt-4" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.08) !important;">
                <div class="card-body p-3 p-md-4">
                    <h5 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-receipt text-warning"></i> My Bets on this Match
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                            <thead>
                                <tr class="text-white-50 small border-secondary">
                                    <th>BET ID</th>
                                    <th>MARKET</th>
                                    <th>SELECTION</th>
                                    <th>TYPE</th>
                                    <th>ODDS</th>
                                    <th>STAKE</th>
                                    <th>RETURN</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody id="matchBetsTbody">
                                @forelse($myBets as $b)
                                    <tr>
                                        <td class="font-monospace small text-white-50">{{ $b->bet_id }}</td>
                                        <td class="small text-white-50">{{ $b->market ? $b->market->market_name : 'Market' }}</td>
                                        <td class="fw-bold text-light">{{ $b->runner_name }}</td>
                                        <td>
                                            <span class="badge {{ $b->bet_type === 'lay' ? 'bg-danger' : 'bg-primary' }} text-uppercase">
                                                {{ $b->bet_type }}
                                            </span>
                                        </td>
                                        <td class="font-monospace fw-bold text-warning">{{ number_format($b->odds, 2) }}</td>
                                        <td class="font-monospace text-light">₹{{ number_format($b->stake, 2) }}</td>
                                        <td class="font-monospace fw-bold text-success">₹{{ number_format($b->potential_payout, 2) }}</td>
                                        <td>
                                            @if($b->status === 'won')
                                                <span class="badge bg-success rounded-pill px-2 py-1">WON (+₹{{ number_format($b->win_amount, 2) }})</span>
                                            @elseif($b->status === 'lost')
                                                <span class="badge bg-secondary rounded-pill px-2 py-1">LOST</span>
                                            @else
                                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1">IN PLAY</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptyBetsRow">
                                        <td colspan="8" class="text-center text-white-50 py-3">No bets placed on this match yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endauth
        </div>

        <!-- Right Column: Interactive Bet Slip (Sticky on Desktop, Bottom Sheet on Mobile) -->
        <div class="col-lg-4">
            <div class="mobile-drawer-backdrop" id="drawerBackdrop" onclick="closeInteractiveSlip()"></div>

            <div class="sticky-bet-slip" id="mainBetSlip">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom border-white border-opacity-10">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-ticket text-warning"></i>
                        <h5 class="fw-bold text-white mb-0">Cricket Bet Slip</h5>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @auth
                            <div class="small font-monospace text-success fw-bold" id="slipUserBalance">
                                Bal: ₹{{ number_format(Auth::user()->balance, 2) }}
                            </div>
                        @endauth
                        <button type="button" class="btn btn-sm btn-dark text-white-50 d-lg-none" onclick="closeInteractiveSlip()">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                @if($match->status === 'completed')
                    <!-- Match Completed Notice -->
                    <div class="text-center py-4">
                        <i class="fa-solid fa-flag-checkered fa-2x text-warning mb-2"></i>
                        <h6 class="text-white fw-bold mb-1">Match Concluded</h6>
                        <p class="small text-white-50 mb-0">All markets for this match are settled and closed.</p>
                        @if(!empty($match->winner_team))
                            <div class="badge bg-success bg-opacity-25 text-success border border-success mt-2 px-3 py-1 font-monospace">
                                Winner: {{ $match->winner_team }}
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Empty State -->
                    <div id="betSlipEmptyState" class="text-center py-4 text-white-50">
                        <i class="fa-solid fa-hand-pointer fa-2x mb-2 opacity-50"></i>
                        <p class="small mb-0">Click any <span style="color: #72bbef; font-weight: bold;">BACK</span> or <span style="color: #faa9ba; font-weight: bold;">LAY</span> odds button to place your bet.</p>
                    </div>

                    <!-- Active Bet Form -->
                    <div id="betSlipActiveForm" class="d-none">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge rounded-pill px-3 py-1 fw-bold font-monospace" id="slipTypeBadge">BACK</span>
                            <span class="text-white-50 small text-truncate" style="max-width: 180px;" id="slipMarketName">Match Winner</span>
                        </div>

                        <div class="h5 fw-bold text-white mb-2" id="slipRunnerName">India</div>

                        <!-- Odds input -->
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 mb-3" style="background: rgba(255,255,255,0.06);">
                            <span class="text-white-50 small">Odds:</span>
                            <span class="h5 fw-bold font-monospace text-warning mb-0" id="slipOdds">1.90</span>
                        </div>

                        <!-- Stake Input -->
                        <div class="mb-3">
                            <label class="form-label small text-white-50 fw-semibold mb-1">STAKE AMOUNT (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-white-50">₹</span>
                                <input type="number" class="form-control bg-dark border-secondary text-white fw-bold font-monospace" id="slipStake" value="100" min="10" max="100000" oninput="updatePayoutCalc()">
                            </div>
                        </div>

                        <!-- Quick Chips -->
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            <button type="button" class="bet-chip-btn flex-fill" onclick="addStake(100)">+100</button>
                            <button type="button" class="bet-chip-btn flex-fill" onclick="addStake(500)">+500</button>
                            <button type="button" class="bet-chip-btn flex-fill" onclick="addStake(1000)">+1K</button>
                            <button type="button" class="bet-chip-btn flex-fill" onclick="addStake(5000)">+5K</button>
                            <button type="button" class="bet-chip-btn flex-fill" onclick="addStake(10000)">+10K</button>
                            <button type="button" class="bet-chip-btn flex-fill" onclick="addStake(25000)">+25K</button>
                        </div>

                        <!-- Return calculation -->
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 mb-3 border border-success border-opacity-25" style="background: rgba(34, 197, 94, 0.08);">
                            <div>
                                <div class="text-white-50 small" style="font-size: 0.72rem;">NET PROFIT</div>
                                <div class="font-monospace text-success fw-bold" id="slipNetProfit">₹90.00</div>
                            </div>
                            <div class="text-end">
                                <div class="text-white-50 small" style="font-size: 0.72rem;">TOTAL RETURN</div>
                                <div class="h6 fw-bold font-monospace text-success mb-0" id="slipReturnText">₹190.00</div>
                            </div>
                        </div>

                        <div class="alert alert-danger py-1 px-2 small d-none" id="slipErrorBox"></div>

                        @auth
                            <button type="button" class="btn btn-success w-100 rounded-pill py-2 fw-bold" id="btnSubmitBet" onclick="executeBet()">
                                <i class="fa-solid fa-check-circle me-1"></i> PLACE BET NOW
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-warning w-100 rounded-pill py-2 fw-bold">
                                Login to Place Bet
                            </a>
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const matchId = {{ $match->id }};
    let currentSelection = {
        runner_id: null,
        runner_name: '',
        odds: 1.0,
        bet_type: 'back',
        market_name: ''
    };

    let previousOddsCache = {};

    document.addEventListener('DOMContentLoaded', function () {
        // Poll this match specifically every 2.5 seconds for instant scores, odds, and settlement
        setInterval(pollMatchLive, 2500);
    });

    function filterMarkets(type) {
        const pills = document.querySelectorAll('.market-nav-pill');
        pills.forEach(p => p.classList.remove('active'));
        if (event && event.target) {
            const btn = event.target.closest('.market-nav-pill');
            if (btn) btn.classList.add('active');
        }

        const boxes = document.querySelectorAll('.market-item');
        boxes.forEach(box => {
            const mType = box.getAttribute('data-market-type');
            if (type === 'all') {
                box.classList.remove('d-none');
            } else if (type === 'fancy') {
                if (mType === 'session_runs' || mType === 'over_odd_even' || mType === 'tied_match') {
                    box.classList.remove('d-none');
                } else {
                    box.classList.add('d-none');
                }
            } else if (mType === type) {
                box.classList.remove('d-none');
            } else {
                box.classList.add('d-none');
            }
        });
    }

    function openInteractiveSlip(runnerId, runnerName, odds, betType, marketName) {
        if ("{{ $match->status }}" === 'completed') {
            alert('This match has completed and all markets are settled. Betting is closed.');
            return;
        }

        currentSelection = {
            runner_id: runnerId,
            runner_name: runnerName,
            odds: parseFloat(odds),
            bet_type: betType,
            market_name: marketName
        };

        document.getElementById('betSlipEmptyState').classList.add('d-none');
        document.getElementById('betSlipActiveForm').classList.remove('d-none');

        const badge = document.getElementById('slipTypeBadge');
        if (betType === 'lay') {
            badge.className = 'badge bg-danger rounded-pill px-3 py-1 fw-bold font-monospace';
            badge.textContent = 'LAY (KHAI)';
        } else {
            badge.className = 'badge bg-primary rounded-pill px-3 py-1 fw-bold font-monospace';
            badge.textContent = 'BACK (LAGAI)';
        }

        document.getElementById('slipMarketName').textContent = marketName;
        document.getElementById('slipRunnerName').textContent = runnerName;
        document.getElementById('slipOdds').textContent = currentSelection.odds.toFixed(2);
        document.getElementById('slipErrorBox').classList.add('d-none');

        updatePayoutCalc();

        // Open mobile bottom sheet if on mobile screen
        const slip = document.getElementById('mainBetSlip');
        const backdrop = document.getElementById('drawerBackdrop');
        if (window.innerWidth < 992) {
            slip.classList.add('is-open');
            backdrop.classList.add('is-open');
        }
    }

    function closeInteractiveSlip() {
        const slip = document.getElementById('mainBetSlip');
        const backdrop = document.getElementById('drawerBackdrop');
        slip.classList.remove('is-open');
        backdrop.classList.remove('is-open');
    }

    function addStake(amount) {
        const input = document.getElementById('slipStake');
        const curr = parseFloat(input.value) || 0;
        input.value = curr + amount;
        updatePayoutCalc();
    }

    function updatePayoutCalc() {
        const stake = parseFloat(document.getElementById('slipStake').value) || 0;
        const total = (stake * currentSelection.odds).toFixed(2);
        const profit = Math.max(0, (stake * (currentSelection.odds - 1))).toFixed(2);
        document.getElementById('slipReturnText').textContent = '₹' + total;
        document.getElementById('slipNetProfit').textContent = '₹' + profit;
    }

    async function executeBet() {
        const stake = parseFloat(document.getElementById('slipStake').value) || 0;
        const errBox = document.getElementById('slipErrorBox');
        const btn = document.getElementById('btnSubmitBet');

        if (stake < 10) {
            errBox.textContent = 'Minimum bet is ₹10.';
            errBox.classList.remove('d-none');
            return;
        }

        errBox.classList.add('d-none');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Placing Bet...';

        try {
            const res = await fetch("{{ route('api.sports.cricket.place-bet') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    runner_id: currentSelection.runner_id,
                    stake: stake,
                    bet_type: currentSelection.bet_type
                })
            });

            const data = await res.json();

            if (data.success) {
                if (data.new_balance !== undefined) {
                    const balFormatted = '₹' + parseFloat(data.new_balance).toLocaleString('en-IN', { minimumFractionDigits: 2 });
                    const slipBal = document.getElementById('slipUserBalance');
                    if (slipBal) slipBal.textContent = 'Bal: ' + balFormatted;
                    const headerBal = document.getElementById('headerWalletBalance');
                    if (headerBal) headerBal.textContent = balFormatted;
                }

                alert('Cricket bet placed successfully on ' + currentSelection.runner_name + ' @ ' + currentSelection.odds.toFixed(2) + '!');
                closeInteractiveSlip();

                // Trigger immediate poll to update bets table
                pollMatchLive();
            } else {
                errBox.textContent = data.message || 'Bet placement failed.';
                errBox.classList.remove('d-none');
            }
        } catch (e) {
            errBox.textContent = 'Network error. Please try again.';
            errBox.classList.remove('d-none');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check-circle me-1"></i> PLACE BET NOW';
        }
    }

    async function pollMatchLive() {
        try {
            const res = await fetch(`/api/sports/cricket/match/${matchId}`);
            if (!res.ok) return;
            const data = await res.json();

            if (data.success && data.match) {
                const m = data.match;
                const meta = m.meta_data || {};

                // Update scores
                const scoreA = document.getElementById('liveScoreA');
                const scoreB = document.getElementById('liveScoreB');
                const equation = document.getElementById('matchEquationText');

                if (scoreA && m.score_team_a) scoreA.textContent = m.score_team_a;
                if (scoreB && m.score_team_b) scoreB.textContent = m.score_team_b;
                if (equation && m.current_summary) equation.textContent = m.current_summary;

                // Update 2D Pitch & Phase
                const ballDot = document.getElementById('liveBallDot');
                const phaseBadge = document.getElementById('ballPhaseBadge');
                const eventBanner = document.getElementById('lastEventBanner');
                const commText = document.getElementById('liveCommentaryText');

                if (meta.ball_phase === 'ball_in_play') {
                    if (ballDot) {
                        ballDot.className = 'live-ball-dot ball-at-batsman';
                    }
                    if (phaseBadge) {
                        phaseBadge.className = 'badge bg-danger px-3 py-1 font-monospace fw-bold';
                        phaseBadge.textContent = 'BALL IN PLAY';
                    }
                } else {
                    if (ballDot) {
                        ballDot.className = 'live-ball-dot ball-at-bowler';
                    }
                    if (phaseBadge) {
                        phaseBadge.className = 'badge bg-success px-3 py-1 font-monospace fw-bold';
                        phaseBadge.textContent = `BETTING OPEN: ${meta.ball_countdown || 8}s`;
                    }
                }

                if (eventBanner && meta.last_event) eventBanner.textContent = meta.last_event;
                if (commText && meta.last_ball_commentary) commText.innerHTML = `<i class="fa-solid fa-quote-left text-secondary me-1"></i> ${meta.last_ball_commentary}`;

                // Update Batsmen & Bowler Table
                if (meta.batsman_striker) {
                    const st = meta.batsman_striker;
                    setText('statStrikerName', st.name);
                    setText('pitchStrikerName', st.name + '*');
                    setText('statStrikerR', st.runs);
                    setText('statStrikerB', st.balls);
                    setText('statStriker4s', st.fours);
                    setText('statStriker6s', st.sixes);
                    setText('statStrikerSR', st.sr);
                }

                if (meta.batsman_non_striker) {
                    const nst = meta.batsman_non_striker;
                    setText('statNonStrikerName', nst.name);
                    setText('statNonStrikerR', nst.runs);
                    setText('statNonStrikerB', nst.balls);
                    setText('statNonStriker4s', nst.fours);
                    setText('statNonStriker6s', nst.sixes);
                    setText('statNonStrikerSR', nst.sr);
                }

                if (meta.current_bowler) {
                    const bw = meta.current_bowler;
                    setText('statBowlerName', bw.name);
                    setText('pitchBowlerName', bw.name);
                    setText('statBowlerO', bw.overs);
                    setText('statBowlerM', bw.maidens);
                    setText('statBowlerR', bw.runs);
                    setText('statBowlerW', bw.wickets);
                    setText('statBowlerEcon', bw.econ);
                }

                setText('statCRR', meta.crr || '8.4');
                setText('statRRR', meta.rrr || '9.2');
                setText('statTarget', meta.target || '187');

                // Update Recent Balls
                const ballsContainer = document.getElementById('liveRecentBalls');
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

                // Update Markets & Runners (with flash animation on odds change)
                if (Array.isArray(m.markets)) {
                    m.markets.forEach(mkt => {
                        const overlay = document.getElementById(`suspended-overlay-${mkt.id}`);
                        const badge = document.getElementById(`market-status-badge-${mkt.id}`);
                        const nameEl = document.getElementById(`market-name-${mkt.id}`);

                        if (nameEl) nameEl.textContent = mkt.market_name;

                        if (mkt.status === 'suspended') {
                            if (overlay) overlay.classList.remove('d-none');
                            if (badge) {
                                badge.className = 'badge bg-danger px-2 py-0 small font-monospace';
                                badge.textContent = 'SUSPENDED';
                            }
                        } else {
                            if (overlay) overlay.classList.add('d-none');
                            if (badge) {
                                badge.className = 'badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 px-2 py-0 small font-monospace';
                                badge.textContent = 'ACTIVE';
                            }
                        }

                        if (Array.isArray(mkt.runners)) {
                            mkt.runners.forEach(r => {
                                const backEl = document.getElementById(`back-odds-${r.id}`);
                                const layEl = document.getElementById(`lay-odds-${r.id}`);
                                const btnBack = document.getElementById(`btn-back-${r.id}`);
                                const btnLay = document.getElementById(`btn-lay-${r.id}`);

                                const newBack = parseFloat(r.back_odds).toFixed(2);
                                const newLay = parseFloat(r.lay_odds || r.back_odds).toFixed(2);

                                if (backEl) {
                                    const oldBack = previousOddsCache[`back_${r.id}`];
                                    if (oldBack && oldBack !== newBack) {
                                        if (btnBack) {
                                            btnBack.classList.remove('odds-flash-up', 'odds-flash-down');
                                            void btnBack.offsetWidth; // re-trigger animation
                                            btnBack.classList.add(newBack > oldBack ? 'odds-flash-up' : 'odds-flash-down');
                                        }
                                    }
                                    backEl.textContent = newBack;
                                    previousOddsCache[`back_${r.id}`] = newBack;
                                }

                                if (layEl) {
                                    layEl.textContent = newLay;
                                }
                            });
                        }
                    });
                }

                // Update User Balance if returned
                if (data.user_balance !== null && data.user_balance !== undefined) {
                    const balFormatted = 'Bal: ₹' + parseFloat(data.user_balance).toLocaleString('en-IN', { minimumFractionDigits: 2 });
                    const slipBal = document.getElementById('slipUserBalance');
                    if (slipBal) slipBal.textContent = balFormatted;
                }

                // Update My Bets on this Match Table
                if (Array.isArray(data.my_bets)) {
                    renderMatchBets(data.my_bets);
                }
            }
        } catch (e) {}
    }

    function renderMatchBets(bets) {
        const tbody = document.getElementById('matchBetsTbody');
        if (!tbody) return;

        if (bets.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-white-50 py-3">No bets placed on this match yet.</td></tr>';
            return;
        }

        let rows = '';
        bets.forEach(b => {
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
                    <td class="small text-white-50">${b.market ? b.market.market_name : 'Market'}</td>
                    <td class="fw-bold text-light">${b.runner_name}</td>
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

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el && value !== undefined && value !== null) {
            el.textContent = value;
        }
    }
</script>
@endsection
