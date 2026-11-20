@extends('layouts.app')

@section('title', '2-Digit Lottery - Hourly Slot Draws (XA–XJ) Live Results & Play Online')

@section('styles')
<style>
    /* 2-Digit Lottery Dedicated Arena Styles */
    .lottery-hero-nav {
        background: linear-gradient(135deg, #09192f 0%, #0e2a50 50%, #153965 100%);
        border-radius: 20px;
        padding: 16px 24px;
        color: #fff;
        border: 1px solid rgba(56, 189, 248, 0.2);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }

    /* Today's Hourly Slots Schedule Board */
    .slots-board-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .slots-board-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 20px;
    }

    .slot-cell-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 12px;
        text-align: center;
        background: #ffffff;
        transition: all 0.25s ease;
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .slot-cell-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        border-color: #38bdf8;
    }
    .slot-cell-card.slot-active {
        border: 2px solid #0284c7;
        background: linear-gradient(180deg, #f0f9ff 0%, #e0f2fe 100%);
        box-shadow: 0 8px 24px rgba(2, 132, 199, 0.2);
    }
    .slot-cell-card.slot-completed {
        background: #fafafa;
        border-color: #e5e7eb;
    }

    .slot-time-title {
        font-size: 1.05rem;
        font-weight: 800;
        font-family: 'JetBrains Mono', monospace;
        color: #0f172a;
    }
    .slot-active .slot-time-title {
        color: #0369a1;
    }

    .slot-status-pill {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 50px;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Slot Preview Numbers Pill Strip */
    .slot-preview-nums {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 3px;
        flex-wrap: wrap;
        margin: 6px 0;
    }
    .slot-mini-num {
        background: #0f172a;
        color: #fff;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        font-size: 0.72rem;
        padding: 2px 5px;
        border-radius: 4px;
    }

    /* Other Games Fast Switch Banner */
    .arena-switch-bar {
        background: #f1f5f9;
        border-radius: 16px;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    /* Circular Countdown Timer Widget */
    .circle-progress-container {
        position: relative;
        width: 76px;
        height: 76px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .progress-ring {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }
    .hourglass-icon-center {
        position: relative;
        z-index: 2;
        color: #0b2545;
        font-size: 1.35rem;
        line-height: 1;
        margin: 0;
        animation: pulseHourglass 2s ease-in-out infinite;
    }

    @media (max-width: 767.98px) {
        .main-wrapper {
            padding-top: 4px !important;
            padding-bottom: 70px !important;
        }
        .lottery-hero-nav {
            padding: 8px 10px !important;
            margin-bottom: 8px !important;
            border-radius: 14px !important;
        }
        .lottery-title-text {
            font-size: 0.92rem !important;
        }
        .hero-live-card {
            padding: 10px 8px !important;
            margin-bottom: 8px !important;
            border-radius: 14px !important;
        }
        .hero-heading {
            font-size: 1.05rem !important;
            margin-bottom: 0 !important;
        }
        .hero-subtitle {
            display: none !important;
        }
        .hero-content-grid {
            grid-template-columns: 1fr !important;
            gap: 8px !important;
        }
        .latest-result-card {
            padding: 8px 6px !important;
            border-radius: 12px !important;
        }
        .latest-result-header {
            margin-bottom: 6px !important;
            padding-bottom: 4px !important;
        }
        .latest-result-title {
            font-size: 0.85rem !important;
        }

        /* 10 SLOTS GRID: 5 COLUMNS X 2 ROWS ON MOBILE */
        .slots-grid {
            display: grid !important;
            grid-template-columns: repeat(5, 1fr) !important;
            gap: 4px !important;
        }
        .slot-box {
            padding: 4px 1px !important;
            border-radius: 8px !important;
            min-height: 48px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
        }
        .slot-label {
            font-size: 0.62rem !important;
            margin-bottom: 1px !important;
            font-weight: 800 !important;
        }
        .slot-value {
            font-size: 1.15rem !important;
            line-height: 1 !important;
            font-weight: 900 !important;
        }

        /* Next update card compact */
        .next-update-card {
            padding: 8px 10px !important;
            border-radius: 12px !important;
        }
        .next-update-header {
            font-size: 0.75rem !important;
            margin-bottom: 4px !important;
        }
        .next-slot-time {
            font-size: 1rem !important;
        }
        .countdown-timer-display {
            font-size: 1.3rem !important;
        }
        .circle-progress-container {
            width: 62px !important;
            height: 62px !important;
            min-width: 62px !important;
            min-height: 62px !important;
            flex-shrink: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .progress-ring {
            width: 100% !important;
            height: 100% !important;
        }
        .hourglass-icon-center {
            font-size: 1.15rem !important;
            color: #0096c7 !important;
        }
        .next-update-footer {
            margin-top: 6px !important;
            padding-top: 6px !important;
            font-size: 0.68rem !important;
        }

        /* Slots Board schedule */
        .slots-board-card {
            border-radius: 14px !important;
            margin-bottom: 12px !important;
        }
        .slots-board-header {
            padding: 8px 10px !important;
        }
        .slots-board-header h3 {
            font-size: 0.95rem !important;
            color: #0f172a !important;
        }
        .slot-cell-card {
            padding: 6px 4px !important;
            border-radius: 10px !important;
        }
        .slot-time-title {
            font-size: 0.82rem !important;
        }
        .slot-status-pill {
            font-size: 0.62rem !important;
            padding: 2px 5px !important;
        }
    }
</style>
@endsection

@section('content')
<!-- Interactive Demo & Simulation Control Strip -->
<div class="demo-bar d-none d-md-flex">
    <div class="demo-title">
        <i class="fa-solid fa-server"></i>
        <span>2-DIGIT LOTTERY ENGINE: 2-MIN EARLY GENERATION & AUTO PAYOUT</span>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span>Server Clock (IST):</span>
        <span class="demo-time" id="currentSimTime">{{ \Carbon\Carbon::now('Asia/Kolkata')->format('h:i:s A') }}</span>
        
        @auth
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="btn-demo text-decoration-none">
                    <i class="fa-solid fa-gauge-high me-1"></i> Admin Dashboard
                </a>
                <a href="{{ route('admin.results.index') }}" class="btn-demo text-decoration-none">
                    <i class="fa-solid fa-sliders me-1"></i> Result Controlling
                </a>
            @endif
        @endauth

        <button class="btn-demo" onclick="voiceAnnouncer.testVoice()" title="Test 3x voice announcement">
            <i class="fa-solid fa-headphones me-1"></i> Voice Test
        </button>
    </div>
</div>

<main class="main-wrapper">
    <div class="container-xl">

        <!-- Header Banner & Quick Arena Switcher -->
        <div class="lottery-hero-nav mb-3">
            <div class="d-flex align-items-center justify-content-between gap-2 flex-nowrap">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px;" title="Back to Lobby">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div class="overflow-hidden">
                        <div class="d-flex align-items-center gap-2 flex-nowrap">
                            <h1 class="h5 mb-0 fw-bold text-white text-truncate lottery-title-text d-flex align-items-center gap-1">
                                <i class="fa-solid fa-dice text-info"></i> <span>2-Digit Lottery</span>
                            </h1>
                            <span class="badge bg-success rounded-pill px-2 py-1 small flex-shrink-0" style="font-size: 0.68rem;">
                                90X PAYOUT
                            </span>
                        </div>
                        <div class="text-white-50 small mt-0 text-truncate d-none d-md-block" style="font-size: 0.72rem;">
                            Hourly official slot draws (XA–XJ) • 100% Provably Fair Automated Settlement
                        </div>
                    </div>
                </div>

                <!-- Quick Switch & Place Bet Slip Button -->
                <div class="d-flex align-items-center gap-1 gap-md-2 flex-shrink-0">
                    <a href="{{ route('wingo.index') }}" class="btn btn-sm text-white rounded-pill px-2 py-1 fw-bold shadow-sm d-none d-md-inline-flex" style="background: linear-gradient(135deg, #ec4899, #8b5cf6); font-size: 0.75rem;">
                        <i class="fa-solid fa-gamepad me-1"></i> WinGo
                    </a>
                    <a href="{{ route('andar-bahar.index') }}" class="btn btn-sm text-white rounded-pill px-2 py-1 fw-bold shadow-sm d-none d-md-inline-flex" style="background: linear-gradient(135deg, #f59e0b, #b45309); font-size: 0.75rem;">
                        <i class="fa-solid fa-diamond me-1"></i> Andar Bahar
                    </a>
                    <a href="#betSlipForm" class="btn btn-sm btn-info rounded-pill px-2 px-md-3 py-1 fw-bold text-dark shadow-sm" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-ticket me-1"></i> <span class="d-none d-sm-inline">Place Bet Slip</span><span class="d-sm-none">Bet Slip &darr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 1. Hero Section: Live Results & Next Slot Countdown -->
        <section class="hero-live-card mb-3" id="liveSection">
            <div class="hero-header-row mb-2">
                <div class="hero-title-group">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-primary px-2 py-1 rounded-pill fw-bold text-uppercase" style="font-size: 0.68rem;">
                            <i class="fa-solid fa-dice me-1"></i> HOURLY DRAWS
                        </span>
                        <span class="badge-live-pulse" style="font-size: 0.68rem; padding: 2px 8px;">
                            <span class="live-dot"></span>
                            LIVE RESULT
                        </span>
                    </div>
                    <h2 class="hero-heading" style="font-size: 1.15rem;">
                        Today's Published Numbers (XA – XJ)
                    </h2>
                    <p class="hero-subtitle d-none d-md-block">Official 2-digit lottery numbers published in real time according to Indian Standard Time (IST).</p>
                </div>
                
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- Speaking Equalizer Banner -->
                    <div class="speaking-banner" id="speakingBanner">
                        <div class="sound-wave speaking">
                            <div class="bar"></div>
                            <div class="bar"></div>
                            <div class="bar"></div>
                            <div class="bar"></div>
                        </div>
                        <span id="speakingText">Announcing Results 3x...</span>
                        <button class="btn btn-sm text-info p-0 ms-1" onclick="voiceAnnouncer.stop()" title="Mute/Stop Speech">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </button>
                    </div>

                    <!-- Voice Controls -->
                    <div class="d-flex align-items-center gap-1">
                        <button class="voice-control-pill" id="voiceToggleBtn" onclick="voiceAnnouncer.toggle()" title="Toggle AI Voice Announcements">
                            <i class="fa-solid fa-volume-xmark" id="voiceIcon"></i>
                            <span id="voiceToggleLabel" class="d-none d-sm-inline">AI Voice: OFF</span>
                        </button>

                        <select class="voice-lang-select d-none d-sm-inline-block" id="voiceLangSelect" onchange="voiceAnnouncer.setLanguage(this.value)">
                            <option value="hi-IN">🇮🇳 हिन्दी (Hindi)</option>
                            <option value="en-IN" selected>🇮🇳 English (India)</option>
                        </select>
                    </div>

                    <div class="hero-last-updated" style="font-size: 0.72rem; padding: 3px 8px;">
                        <i class="fa-regular fa-clock text-info me-1"></i>
                        <span>Updated <strong id="lastUpdatedTime">{{ $timingStatus['currentPublishedSlot']['slot_time'] }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Content Grid: Latest Result (Left) + Next Update (Right) -->
            <div class="hero-content-grid">
                
                <!-- Latest Result Card -->
                <div class="latest-result-card">
                    <div class="latest-result-header">
                        <h2 class="latest-result-title">
                            <i class="fa-solid fa-chart-simple"></i>
                            Latest Result
                        </h2>
                        <div class="latest-meta-pills">
                            <div class="meta-pill">
                                <i class="fa-regular fa-calendar"></i>
                                <span id="heroDate">{{ $timingStatus['currentPublishedSlot']['slot_datetime']->format('d F Y') }}</span>
                            </div>
                            <div class="meta-pill" id="heroTimePill">
                                <i class="fa-regular fa-clock"></i>
                                <span id="heroTime">{{ $timingStatus['currentPublishedSlot']['slot_time'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 10 Slot Boxes (2 rows of 5) -->
                    <div class="slots-grid">
                        @php
                            $slotKeys = ['XA', 'XB', 'XC', 'XD', 'XE', 'XF', 'XG', 'XH', 'XI', 'XJ'];
                            $nums = $latestResult ? $latestResult->getNumbersArray() : \App\Services\LotteryEngine::generate10Numbers($timingStatus['currentPublishedSlot']['slot_datetime']->format('Y-m-d'), $timingStatus['currentPublishedSlot']['slot_time']);
                        @endphp
                        @foreach($slotKeys as $k)
                            <div class="slot-box" id="slot-{{ $k }}">
                                <div class="slot-label">{{ $k }}</div>
                                <div class="slot-value" id="val-{{ $k }}">{{ $nums[$k] ?? '--' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Next Update Card -->
                <div class="next-update-card">
                    <div>
                        <div class="next-update-header">
                            <i class="fa-regular fa-clock"></i>
                            <span>Next Slot Draw</span>
                        </div>
                        <div class="next-update-main">
                            <div class="next-time-col">
                                <div class="next-slot-time" id="nextSlotTime">{{ $timingStatus['nextUpcomingSlot']['slot_time'] }}</div>
                                <div class="updates-in-label">Closes in</div>
                                <div class="countdown-timer-display" id="countdownTimer">--:--</div>
                            </div>
                            
                            <!-- Circular progress ring -->
                            <div class="circle-progress-container">
                                <svg class="progress-ring" viewBox="0 0 76 76" width="76" height="76">
                                    <circle class="progress-ring-bg" stroke-width="4.5" fill="transparent" r="32" cx="38" cy="38" />
                                    <circle class="progress-ring-circle" id="progressCircle" stroke-width="4.5" fill="transparent" r="32" cx="38" cy="38" />
                                </svg>
                                <i class="fa-solid fa-hourglass-half hourglass-icon-center"></i>
                            </div>
                        </div>
                    </div>

                    <div class="next-update-footer">
                        <div class="auto-update-text">
                            <i class="fa-regular fa-calendar-check text-primary"></i>
                            <span>Hourly draws scheduled throughout the day.</span>
                        </div>
                        <div class="early-generation-badge">
                            <i class="fa-solid fa-bolt text-warning"></i>
                            <span id="earlyBadgeText">Generates 2 min early at {{ $timingStatus['nextUpcomingSlot']['gen_datetime']->format('h:i A') }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- 2. Today's Hourly Slots Schedule Board -->
        <section class="slots-board-card mb-5">
            <div class="slots-board-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h3 class="h5 fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                        <i class="fa-solid fa-calendar-day text-primary"></i> Today's Time Slots Schedule
                    </h3>
                    <div class="text-muted small">Real-time status of all hourly slot draws for today. Click any open slot to pre-fill the bet slip.</div>
                </div>
                <div class="small fw-bold text-muted">
                    Target Slot: <span class="badge bg-primary rounded-pill px-3 py-1 font-monospace">{{ $timingStatus['nextUpcomingSlot']['slot_time'] }}</span>
                </div>
            </div>

            <div class="p-3">
                <div class="row g-3">
                    @foreach($timingStatus['allSlotsToday'] as $slotItem)
                        @php
                            $slotTime = $slotItem['slot_time'];
                            $isGen = $slotItem['is_generated'];
                            $isOpen = $slotItem['is_betting_open'];
                            $isNext = ($slotTime === $timingStatus['nextUpcomingSlot']['slot_time']);
                            $res = $todayResults[$slotTime] ?? null;
                        @endphp
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <div class="slot-cell-card {{ $isNext ? 'slot-active' : ($isGen ? 'slot-completed' : '') }}">
                                <div>
                                    <div class="slot-time-title mb-1">{{ $slotTime }}</div>
                                    
                                    @if($isGen)
                                        <span class="slot-status-pill bg-success-subtle text-success border border-success-subtle">
                                            <i class="fa-solid fa-check me-1"></i> Completed
                                        </span>
                                    @elseif($isNext && $isOpen)
                                        <span class="slot-status-pill bg-primary text-white">
                                            <i class="fa-solid fa-bolt me-1"></i> Betting Open
                                        </span>
                                    @elseif($isOpen)
                                        <span class="slot-status-pill bg-info-subtle text-info-emphasis border border-info-subtle">
                                            <i class="fa-solid fa-clock me-1"></i> Open
                                        </span>
                                    @else
                                        <span class="slot-status-pill bg-light text-muted border">
                                            Locked
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-2">
                                    @if($res)
                                        <div class="slot-preview-nums" title="XA: {{ $res->XA }}, XB: {{ $res->XB }}">
                                            <span class="slot-mini-num">{{ $res->XA ?? '--' }}</span>
                                            <span class="slot-mini-num">{{ $res->XB ?? '--' }}</span>
                                            <span class="slot-mini-num">{{ $res->XC ?? '--' }}</span>
                                        </div>
                                    @endif

                                    @if($isOpen)
                                        <button type="button" class="btn btn-sm {{ $isNext ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill w-100 py-1 fw-bold mt-1" style="font-size: 0.75rem;" onclick="selectSlotForBet('{{ $slotTime }}')">
                                            Bet This &darr;
                                        </button>
                                    @else
                                        <div class="small text-muted py-1" style="font-size: 0.72rem;">Draw Done</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- 3. Interactive Play / Bet Slip Section -->
        <section class="card-custom mb-4 overflow-hidden shadow-sm border-0">
            <div class="p-3 p-md-4 text-white" style="background: linear-gradient(135deg, #09192f 0%, #0e2a50 50%, #153965 100%);">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <span class="badge bg-success px-2 py-1 rounded-pill fw-bold text-uppercase mb-1" style="font-size: 0.68rem;">
                            <i class="fa-solid fa-gamepad me-1"></i> Interactive Betting Slip
                        </span>
                        <h3 class="fw-bold mb-1 text-white fs-5">Place Your 2-Digit Wager</h3>
                        <p class="text-white-50 small mb-0 d-none d-md-block">Select upcoming slot, choose column (`XA`–`XJ`) & pick 2-digit number (00–99). Win {{ number_format($setting->win_multiplier, 0) }}X payout!</p>
                    </div>
                    
                    @auth
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <a href="{{ route('wallet.withdraw') }}" class="text-decoration-none text-end p-2 px-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10 text-white" title="Click to Withdraw Funds">
                                <div class="text-white-50" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase;">Wallet Balance</div>
                                <div class="fs-5 fw-bold text-success font-monospace" id="liveSlipBalance">₹{{ number_format(Auth::user()->balance, 2) }}</div>
                            </a>
                            <a href="{{ route('wallet.deposit') }}" class="btn btn-sm btn-success rounded-pill px-3 py-2 fw-bold shadow-sm">
                                <i class="fa-solid fa-plus-circle me-1"></i> Deposit
                            </a>
                        </div>
                    @else
                        <div>
                            <a href="{{ route('login') }}" class="btn btn-sm btn-warning rounded-pill px-3 py-2 fw-bold text-dark shadow-sm">
                                <i class="fa-solid fa-right-to-bracket me-1"></i> Login to Play
                            </a>
                        </div>
                    @endauth
                </div>
            </div>

            <div class="card-custom-body p-3 p-md-4 bg-white">
                <form id="betSlipForm" onsubmit="submitBetForm(event)">
                    @csrf
                    <div class="row g-3 align-items-end">
                        
                        <!-- 1. Select Slot Time -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">1. Target Slot Time</label>
                            <select class="form-select form-select-lg fw-bold text-primary" id="betSlotTime" name="slot_time" required>
                                @foreach($activeSlots as $s)
                                    <option value="{{ $s->slot_time }}" {{ $s->slot_time === $timingStatus['nextUpcomingSlot']['slot_time'] ? 'selected' : '' }}>
                                        {{ $s->slot_time }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small text-danger" id="betLockWarning"><i class="fa-solid fa-lock me-1"></i> Closes 2 min before slot</div>
                        </div>

                        <!-- 2. Select Column (XA - XJ) -->
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-muted">2. Column</label>
                            <select class="form-select form-select-lg fw-bold" id="betSlotKey" name="slot_key" required>
                                @foreach($slotKeys as $k)
                                    <option value="{{ $k }}">{{ $k }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 3. Choose Number (00 - 99) -->
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-muted">3. Number (00-99)</label>
                            <input type="text" class="form-control form-control-lg text-center font-monospace fw-bold fs-4" id="betChosenNumber" name="chosen_number" placeholder="45" maxlength="2" pattern="[0-9]{2}" required>
                        </div>

                        <!-- 4. Amount -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">4. Wager Amount (₹)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light fw-bold">₹</span>
                                <input type="number" step="1" min="{{ (int) ($setting->lottery_min_bet ?? 10) }}" max="{{ (int) ($setting->lottery_max_bet ?? 10000) }}" class="form-control fw-bold font-monospace" id="betAmountInput" name="amount" value="{{ (int) ($setting->lottery_min_bet ?? 10) }}" oninput="updatePotentialWin(this.value)" required>
                            </div>
                            <div class="form-text small text-muted">Min: ₹{{ number_format($setting->lottery_min_bet ?? 10, 0) }} • Max: ₹{{ number_format($setting->lottery_max_bet ?? 10000, 0) }}</div>
                        </div>

                        <!-- 5. Submit Button -->
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm" style="background: #0077b6; border: none;" id="btnPlaceBet">
                                <i class="fa-solid fa-dice me-1"></i> Place Bet
                            </button>
                        </div>

                    </div>

                    <!-- Quick Amount Chips & Potential Win Calculation -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 pt-3 border-top">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="small text-muted fw-bold">Quick Chips:</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" onclick="setQuickAmount(10)">₹10</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" onclick="setQuickAmount(50)">₹50</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" onclick="setQuickAmount(100)">₹100</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" onclick="setQuickAmount(500)">₹500</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" onclick="setQuickAmount(1000)">₹1000</button>
                        </div>

                        <div class="text-end">
                            <span class="text-muted small">Potential Win ({{ number_format($setting->win_multiplier, 0) }}x): </span>
                            <span class="fs-5 fw-bold text-success font-monospace" id="potentialWinDisplay">₹{{ number_format(10 * $setting->win_multiplier, 2) }}</span>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <!-- 4. Official 2-Digit Lottery Result History Archive -->
        <section class="history-card" id="historySection">
            <div class="history-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="history-title mb-0">
                        <i class="fa-solid fa-clock-rotate-left text-primary"></i>
                        2-Digit Lottery Draw Results Archive
                    </h2>
                </div>
                <div class="text-muted small">
                    All 10 column draws (XA through XJ)
                </div>
            </div>

            <div class="p-3">
                <form class="search-form-row mb-3" id="searchFilterForm" onsubmit="searchDateHistory(event)">
                    <div class="date-picker-group">
                        <label for="startDateInput">Pick Date</label>
                        <div class="date-input-wrapper">
                            <input type="date" id="startDateInput" name="startDate" value="{{ \Carbon\Carbon::now('Asia/Kolkata')->format('Y-m-d') }}" />
                        </div>
                    </div>

                    <button type="submit" class="btn-search-results" id="searchBtn">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Search Results</span>
                    </button>
                </form>

                <div class="table-responsive-custom">
                    <table class="table-history">
                        <thead>
                            <tr>
                                <th class="th-date">Date</th>
                                <th class="th-time">Time</th>
                                <th>XA</th>
                                <th>XB</th>
                                <th>XC</th>
                                <th>XD</th>
                                <th>XE</th>
                                <th>XF</th>
                                <th>XG</th>
                                <th>XH</th>
                                <th>XI</th>
                                <th>XJ</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <!-- Populated dynamically via JS API -->
                        </tbody>
                    </table>
                </div>

                <div class="history-note-box mt-3">
                    <i class="fa-solid fa-circle-info"></i>
                    <div>
                        <strong>Please Note:</strong> All 2-Digit Lottery results are officially generated and published online in Indian Standard Time (IST). Draws generate 2 minutes prior to slot time.
                    </div>
                </div>
            </div>
        </section>

    </div>
</main>

<!-- Floating Live Toast Notification -->
<div class="live-toast" id="liveToast">
    <i class="fa-solid fa-circle-check"></i>
    <div>
        <div class="fw-bold" style="font-size: 0.9rem;" id="toastTitle">Live Number Generated!</div>
        <div id="toastMsg" style="font-size: 0.8rem; color: #cbd5e1;">Slot result is now published.</div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const WIN_MULTIPLIER = {{ (float) ($setting->win_multiplier ?? 90.00) }};
    const SLOT_KEYS = ['XA', 'XB', 'XC', 'XD', 'XE', 'XF', 'XG', 'XH', 'XI', 'XJ'];
    let lastPublishedSlotId = null;
    let localSecondsRemaining = 0;

    // Toast helper
    function showToast(msg, title = "Notification") {
        const toast = document.getElementById('liveToast');
        document.getElementById('toastTitle').textContent = title;
        document.getElementById('toastMsg').textContent = msg;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 4500);
    }

    // Quick chips
    function setQuickAmount(amt) {
        document.getElementById('betAmountInput').value = amt;
        updatePotentialWin(amt);
    }

    function updatePotentialWin(amt) {
        const val = parseFloat(amt) || 0;
        const win = (val * WIN_MULTIPLIER).toFixed(2);
        document.getElementById('potentialWinDisplay').textContent = `₹${parseFloat(win).toLocaleString()}`;
    }

    // Pre-select slot from schedule board
    function selectSlotForBet(slotTime) {
        const sel = document.getElementById('betSlotTime');
        if (sel) {
            sel.value = slotTime;
            document.getElementById('betSlipForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
            showToast(`Selected slot ${slotTime} for betting`, "Slot Selected");
        }
    }

    // Place bet AJAX
    function submitBetForm(e) {
        e.preventDefault();
        @guest
            window.location.href = "{{ route('login') }}";
            return;
        @endguest

        const btn = document.getElementById('btnPlaceBet');
        const form = document.getElementById('betSlipForm');
        const formData = new FormData(form);

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Placing...';

        fetch("{{ route('bets.place') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                "Accept": "application/json"
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-dice me-1"></i> Place Bet';

            if (data.success) {
                showToast(data.message, "Bet Confirmed! 🎉");
                // Update live balance displays
                const balFormatted = `₹${data.new_balance.toFixed(2)}`;
                const navBal = document.getElementById('userWalletBalance');
                const slipBal = document.getElementById('liveSlipBalance');
                if (navBal) navBal.textContent = balFormatted;
                if (slipBal) slipBal.textContent = balFormatted;
            } else {
                showToast(data.message, "Betting Failed ❌");
                if (data.message.includes('Insufficient')) {
                    setTimeout(() => window.location.href = "{{ route('wallet.deposit') }}", 1800);
                }
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-dice me-1"></i> Place Bet';
            showToast("Network error placing bet. Please retry.", "Error");
        });
    }

    // ---------------- AI Voice Announcer ----------------
    const voiceAnnouncer = {
        isEnabled: false,
        lang: "en-IN",
        synth: window.speechSynthesis,

        init() {
            const saved = localStorage.getItem("vbl_voice_enabled");
            if (saved === "true") this.isEnabled = true;
            const savedLang = localStorage.getItem("vbl_voice_lang");
            if (savedLang) {
                this.lang = savedLang;
                const langSel = document.getElementById("voiceLangSelect");
                if (langSel) langSel.value = savedLang;
            }
            this.updateUI();
        },

        toggle() {
            this.isEnabled = !this.isEnabled;
            localStorage.setItem("vbl_voice_enabled", this.isEnabled ? "true" : "false");
            this.updateUI();

            if (this.isEnabled) {
                showToast("AI Voice Announcements Active!", "Audio Enabled 🔊");
                this.speak("AI Voice announcement is now active.", true);
            } else {
                this.stop();
                showToast("Voice announcements muted.", "Audio Muted 🔇");
            }
        },

        setLanguage(lang) {
            this.lang = lang;
            localStorage.setItem("vbl_voice_lang", lang);
            showToast(`Voice language set to ${lang === 'hi-IN' ? 'हिन्दी (Hindi)' : 'English (India)'}`);
        },

        updateUI() {
            const btn = document.getElementById("voiceToggleBtn");
            const label = document.getElementById("voiceToggleLabel");
            const icon = document.getElementById("voiceIcon");

            if (btn && label && icon) {
                if (this.isEnabled) {
                    btn.classList.add("active");
                    label.textContent = "AI Voice: ON";
                    icon.className = "fa-solid fa-volume-high";
                } else {
                    btn.classList.remove("active");
                    label.textContent = "AI Voice: OFF";
                    icon.className = "fa-solid fa-volume-xmark";
                }
            }
        },

        setSpeakingBanner(isSpeaking, text = "") {
            const banner = document.getElementById("speakingBanner");
            const txtEl = document.getElementById("speakingText");
            if (banner) {
                if (isSpeaking) {
                    banner.classList.add("active");
                    if (txtEl) txtEl.textContent = text || "Announcing Results 3x...";
                } else {
                    banner.classList.remove("active");
                }
            }
        },

        highlightSlotBox(key) {
            this.clearHighlights();
            document.getElementById(`slot-${key}`)?.classList.add("speaking-active-box");
        },

        highlightTime() {
            this.clearHighlights();
            document.getElementById("heroTimePill")?.classList.add("speaking-active-time");
        },

        clearHighlights() {
            SLOT_KEYS.forEach(k => document.getElementById(`slot-${k}`)?.classList.remove("speaking-active-box"));
            document.getElementById("heroTimePill")?.classList.remove("speaking-active-time");
        },

        stop() {
            if (this.synth) this.synth.cancel();
            this.clearHighlights();
            this.setSpeakingBanner(false);
        },

        speak(text, priority = false) {
            if (!this.synth) return;
            if (priority) this.stop();

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = this.lang;
            utterance.rate = 0.92;
            utterance.pitch = 1.0;

            const voices = this.synth.getVoices();
            const voice = voices.find(v => v.lang === this.lang || v.lang.startsWith(this.lang.substring(0, 2)));
            if (voice) utterance.voice = voice;

            utterance.onstart = () => this.setSpeakingBanner(true, text);
            utterance.onend = () => { if (!this.synth.speaking) this.setSpeakingBanner(false); };
            utterance.onerror = () => this.setSpeakingBanner(false);

            this.synth.speak(utterance);
        },

        speakUtterance(text, onStartCb, onEndCb) {
            if (!this.synth) return;
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = this.lang;
            utterance.rate = 0.90;
            utterance.pitch = 1.0;

            const voices = this.synth.getVoices();
            const voice = voices.find(v => v.lang === this.lang || v.lang.startsWith(this.lang.substring(0, 2)));
            if (voice) utterance.voice = voice;

            utterance.onstart = () => { if (onStartCb) onStartCb(); };
            utterance.onend = () => {
                if (onEndCb) onEndCb();
                if (!this.synth.speaking) {
                    this.setSpeakingBanner(false);
                    this.clearHighlights();
                }
            };
            utterance.onerror = () => {
                if (onEndCb) onEndCb();
                this.setSpeakingBanner(false);
                this.clearHighlights();
            };

            this.synth.speak(utterance);
        },

        announceResults3x(slotTime, numbers) {
            if (!this.isEnabled || !this.synth) return;
            this.stop();

            const isHindi = this.lang === "hi-IN";
            let introText = isHindi 
                ? `ध्यान दीजिए! लॉटरी। टाइम स्लॉट ${slotTime}। ${slotTime}। ${slotTime}।`
                : `Attention please! Lottery. Time slot ${slotTime}... ${slotTime}... ${slotTime}.`;

            this.speakUtterance(introText, () => {
                this.highlightTime();
                this.setSpeakingBanner(true, `Time Slot: ${slotTime} (3x)`);
            }, () => {
                this.clearHighlights();
            });

            SLOT_KEYS.forEach(key => {
                const val = numbers[key] || "00";
                let itemSpeech = `${key}, ${val} ... ${val} ... ${val}.`;
                this.speakUtterance(itemSpeech, () => {
                    this.highlightSlotBox(key);
                    this.setSpeakingBanner(true, `Speaking: ${key} = ${val} (3x)`);
                }, () => {
                    this.clearHighlights();
                });
            });
        },

        testVoice() {
            if (!this.isEnabled) {
                this.isEnabled = true;
                localStorage.setItem("vbl_voice_enabled", "true");
                this.updateUI();
            }

            const currentSlot = document.getElementById("heroTime").textContent.trim();
            const numbers = {};
            SLOT_KEYS.forEach(k => {
                numbers[k] = document.getElementById(`val-${k}`)?.textContent.trim() || "55";
            });

            showToast(`Playing 3x voice announcement sample for slot ${currentSlot}!`, "Audio Test");
            this.announceResults3x(currentSlot, numbers);
        }
    };

    // ---------------- API Sync Engine ----------------
    const CIRCLE_RADIUS = 32;
    const CIRCLE_CIRCUMFERENCE = 2 * Math.PI * CIRCLE_RADIUS;
    const elCircle = document.getElementById('progressCircle');
    if (elCircle) {
        elCircle.style.strokeDasharray = `${CIRCLE_CIRCUMFERENCE} ${CIRCLE_CIRCUMFERENCE}`;
    }

    function syncLiveStatus() {
        fetch("{{ route('api.live.status') }}")
            .then(res => res.json())
            .then(data => {
                // Update server clock
                document.getElementById('currentSimTime').textContent = data.currentTime;

                // Check for newly published numbers
                const pub = data.currentPublishedSlot;
                const slotId = `${pub.date_dash}_${pub.slot_time}`;

                if (lastPublishedSlotId !== null && lastPublishedSlotId !== slotId) {
                    showToast(`⚡ Numbers for ${pub.slot_time} generated early! Winners auto-credited.`, "New Result Published");
                    voiceAnnouncer.announceResults3x(pub.slot_time, pub.numbers);
                }
                lastPublishedSlotId = slotId;

                // Update Hero Numbers
                document.getElementById('heroDate').textContent = pub.date;
                document.getElementById('heroTime').textContent = pub.slot_time;
                document.getElementById('lastUpdatedTime').textContent = pub.slot_time;

                SLOT_KEYS.forEach(k => {
                    const el = document.getElementById(`val-${k}`);
                    if (el) el.textContent = pub.numbers[k] || '--';
                });

                // Update Next Slot
                const nxt = data.nextUpcomingSlot;
                document.getElementById('nextSlotTime').textContent = nxt.slot_time;
                document.getElementById('earlyBadgeText').textContent = `Generates 2 min early at ${nxt.gen_time}`;
                localSecondsRemaining = nxt.secondsRemaining;

                // Update user wallet balance if authenticated
                if (data.user) {
                    const balStr = `₹${data.user.balance.toFixed(2)}`;
                    const navBal = document.getElementById('userWalletBalance');
                    const slipBal = document.getElementById('liveSlipBalance');
                    if (navBal) navBal.textContent = balStr;
                    if (slipBal) slipBal.textContent = balStr;
                }
            })
            .catch(err => console.error("API sync error:", err));
    }

    // Smooth countdown tick (every 1s)
    setInterval(() => {
        if (localSecondsRemaining > 0) {
            localSecondsRemaining--;
            const mm = String(Math.floor(localSecondsRemaining / 60)).padStart(2, '0');
            const ss = String(localSecondsRemaining % 60).padStart(2, '0');
            document.getElementById('countdownTimer').textContent = `${mm}:${ss}`;

            // Total 20m interval progress
            const totalSpan = 20 * 60;
            const elapsed = totalSpan - localSecondsRemaining;
            const pct = Math.min(Math.max(elapsed / totalSpan, 0), 1);
            if (elCircle) {
                elCircle.style.strokeDashoffset = CIRCLE_CIRCUMFERENCE - pct * CIRCLE_CIRCUMFERENCE;
            }
        } else {
            document.getElementById('countdownTimer').textContent = "00:00";
            if (elCircle) elCircle.style.strokeDashoffset = "0";
        }
    }, 1000);

    // Load 2-Digit Lottery History Table
    function loadHistory(dateStr) {
        fetch(`{{ route('api.results.history') }}?date=${dateStr}`)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('historyTableBody');
                let html = "";
                data.rows.forEach(r => {
                    const isPub = r.status === 'Published';
                    const badge = isPub 
                        ? `<span class="badge-status published"><span class="status-dot"></span> Published</span>`
                        : `<span class="badge-status pending"><span class="status-dot"></span> Pending</span>`;

                    html += `
                        <tr>
                            <td class="td-date">${r.fullDate}</td>
                            <td class="td-time">${r.time}</td>
                            ${SLOT_KEYS.map(k => `<td class="td-slot-val">${r.numbers[k] || '-'}</td>`).join('')}
                            <td>${badge}</td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            });
    }

    function searchDateHistory(e) {
        e.preventDefault();
        const dateVal = document.getElementById('startDateInput').value;
        if (dateVal) {
            loadHistory(dateVal);
            showToast(`Loading results for ${dateVal}...`, "History Search");
        }
    }

    // Initialize 2-Digit Lottery
    voiceAnnouncer.init();
    syncLiveStatus();
    loadHistory("{{ \Carbon\Carbon::now('Asia/Kolkata')->format('Y-m-d') }}");
    setInterval(syncLiveStatus, 5000); // Polling every 5 seconds for lottery live sync
</script>
@endsection
