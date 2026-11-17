@extends('layouts.admin')

@section('title', 'Andar Bahar Live Control & Risk Hub')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-layer-group text-warning me-2"></i> Andar Bahar Live Control Hub
        </h2>
        <p class="text-muted small mb-0">Live round monitoring, real-time house risk exposure, manual result manipulation & payout multipliers.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.andar-bahar.winners') }}" class="btn btn-outline-success fw-bold rounded-pill px-3">
            <i class="fa-solid fa-trophy me-1"></i> Winner List
        </a>
        <a href="{{ route('andar-bahar.index') }}" target="_blank" class="btn btn-warning text-dark fw-bold rounded-pill px-3">
            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Player Arena
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Row: Live Round Overview & Realtime Exposure -->
<div class="row g-4 mb-4">
    <!-- Active Round & Result Control -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark px-3 py-2 fs-6 fw-bold">ANDAR BAHAR LIVE</span>
                    <span class="badge bg-light text-dark border px-3 py-2 fs-6 font-monospace">
                        Round #{{ $round->round_id }}
                    </span>
                </div>
                <div>
                    @if($roundData['is_locked'])
                        <span class="badge bg-danger px-3 py-2 fs-6">
                            <i class="fa-solid fa-lock me-1"></i> Dealing in Progress
                        </span>
                    @else
                        <span class="badge bg-success px-3 py-2 fs-6">
                            <i class="fa-solid fa-spinner fa-spin me-1"></i> Betting Open
                        </span>
                    @endif
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Round Metrics & Joker -->
                <div class="row g-3 text-center mb-4">
                    <div class="col-sm-3 col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small fw-bold text-uppercase">Time Remaining</div>
                            <div class="fs-2 fw-bold font-monospace text-primary" id="adminTimerDisplay">
                                {{ $roundData['remaining_seconds'] }}s
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small fw-bold text-uppercase">Current Joker</div>
                            @php
                                $joker = $round->joker_card ?? ['rank' => '?', 'suit' => 'S', 'symbol' => '♠', 'color' => 'black'];
                                $symbol = $joker['symbol'] ?? (match($joker['suit'] ?? '') { 'H' => '♥', 'D' => '♦', 'C' => '♣', default => '♠' });
                                $isRed = ($joker['color'] ?? '') === 'red' || in_array($joker['suit'] ?? '', ['H', 'D', '♥', '♦']);
                            @endphp
                            <div class="fs-2 fw-bold font-monospace {{ $isRed ? 'text-danger' : 'text-dark' }}">
                                {{ $joker['rank'] ?? '?' }}{{ $symbol }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small fw-bold text-uppercase">Control Mode</div>
                            @if($round->is_manual)
                                <span class="badge bg-danger fs-6 mt-1 px-2 py-1">
                                    <i class="fa-solid fa-crosshairs me-1"></i> MANUAL
                                </span>
                            @else
                                <span class="badge bg-success fs-6 mt-1 px-2 py-1">
                                    <i class="fa-solid fa-shuffle me-1"></i> AUTO RANDOM
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small fw-bold text-uppercase">Target Winner</div>
                            @if($round->is_manual)
                                <div class="fs-4 fw-black font-monospace text-uppercase {{ $round->manual_winning_side === 'andar' ? 'text-primary' : 'text-danger' }}">
                                    {{ strtoupper($round->manual_winning_side) }}
                                </div>
                            @else
                                <div class="fs-6 fw-bold text-muted mt-2">Natural Deal</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 1-Click Winner Control Buttons -->
                <div class="p-3 rounded-3 border bg-light-subtle">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-sliders text-primary me-2"></i> 1-Click Winner Result Override
                        </h6>
                        <small class="text-muted">Choose outcome before deal locks</small>
                    </div>
                    <p class="small text-muted mb-3">
                        Selecting <strong>ANDAR</strong> or <strong>BAHAR</strong> will immediately re-deal the remaining deck so that the matching joker card lands on that specific side.
                    </p>

                    <div class="row g-2">
                        <div class="col-md-4">
                            <form method="POST" action="{{ route('admin.andar-bahar.manual-winner') }}">
                                @csrf
                                <input type="hidden" name="round_id" value="{{ $round->round_id }}">
                                <input type="hidden" name="winner" value="andar">
                                <button type="submit" class="btn w-100 py-3 fw-bold rounded-3 shadow-sm {{ $round->is_manual && $round->manual_winning_side === 'andar' ? 'btn-primary border-3 border-dark' : 'btn-outline-primary' }}"
                                    {{ $roundData['is_locked'] ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-circle-dot me-1"></i> FORCE ANDAR WIN
                                    @if($round->is_manual && $round->manual_winning_side === 'andar')
                                        <div class="small fw-normal text-white">● ACTIVE TARGET</div>
                                    @else
                                        <div class="small fw-normal text-muted">Payout: {{ $setting->andar_multiplier ?? '1.95' }}x</div>
                                    @endif
                                </button>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <form method="POST" action="{{ route('admin.andar-bahar.manual-winner') }}">
                                @csrf
                                <input type="hidden" name="round_id" value="{{ $round->round_id }}">
                                <input type="hidden" name="winner" value="bahar">
                                <button type="submit" class="btn w-100 py-3 fw-bold rounded-3 shadow-sm {{ $round->is_manual && $round->manual_winning_side === 'bahar' ? 'btn-danger border-3 border-dark' : 'btn-outline-danger' }}"
                                    {{ $roundData['is_locked'] ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-circle-dot me-1"></i> FORCE BAHAR WIN
                                    @if($round->is_manual && $round->manual_winning_side === 'bahar')
                                        <div class="small fw-normal text-white">● ACTIVE TARGET</div>
                                    @else
                                        <div class="small fw-normal text-muted">Payout: {{ $setting->bahar_multiplier ?? '1.95' }}x</div>
                                    @endif
                                </button>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <form method="POST" action="{{ route('admin.andar-bahar.clear-manual') }}">
                                @csrf
                                <input type="hidden" name="round_id" value="{{ $round->round_id }}">
                                <button type="submit" class="btn w-100 py-3 fw-bold rounded-3 shadow-sm {{ ! $round->is_manual ? 'btn-dark' : 'btn-outline-dark' }}"
                                    {{ $roundData['is_locked'] ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-shuffle me-1"></i> AUTO RANDOM
                                    <div class="small fw-normal {{ ! $round->is_manual ? 'text-white' : 'text-muted' }}">
                                        {{ ! $round->is_manual ? '● SYSTEM NATURAL' : 'Reset to RNG' }}
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Realtime House Exposure & Safest Recommendation -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-scale-balanced text-success me-2"></i> Live House Exposure
                </h5>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                    Live Pool: ₹{{ number_format($exposure['total_pool'], 2) }}
                </span>
            </div>
            <div class="card-body p-4">
                <!-- Total Pool & Bet Counts -->
                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-3 border">
                    <div>
                        <div class="small text-muted fw-bold">TOTAL BETS IN PLAY</div>
                        <div class="fs-4 fw-bold text-dark">{{ $exposure['total_bets_count'] }} Bets</div>
                    </div>
                    <div class="text-end">
                        <div class="small text-muted fw-bold">COLLECTED VOLUME</div>
                        <div class="fs-4 fw-bold text-success font-monospace">₹{{ number_format($exposure['total_pool'], 2) }}</div>
                    </div>
                </div>

                <!-- Exposure Breakdown by Side -->
                <div class="border rounded-3 p-3 mb-3">
                    <div class="row g-2 text-center">
                        <div class="col-6 border-end">
                            <span class="badge bg-primary px-3 py-1 mb-1">ANDAR POOL</span>
                            <div class="fs-5 fw-bold text-dark font-monospace">₹{{ number_format($exposure['andar_amount'], 2) }}</div>
                            <small class="text-muted">{{ $exposure['andar_bets_count'] }} bets placed</small>
                            <hr class="my-2">
                            <div class="small text-muted">Projected Payout:</div>
                            <div class="fw-bold text-danger font-monospace">₹{{ number_format($exposure['andar_projected_payout'], 2) }}</div>
                            <div class="small mt-1">
                                House Profit:
                                <strong class="{{ $exposure['house_profit_if_andar'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $exposure['house_profit_if_andar'] >= 0 ? '+' : '' }}₹{{ number_format($exposure['house_profit_if_andar'], 2) }}
                                </strong>
                            </div>
                        </div>

                        <div class="col-6">
                            <span class="badge bg-danger px-3 py-1 mb-1">BAHAR POOL</span>
                            <div class="fs-5 fw-bold text-dark font-monospace">₹{{ number_format($exposure['bahar_amount'], 2) }}</div>
                            <small class="text-muted">{{ $exposure['bahar_bets_count'] }} bets placed</small>
                            <hr class="my-2">
                            <div class="small text-muted">Projected Payout:</div>
                            <div class="fw-bold text-danger font-monospace">₹{{ number_format($exposure['bahar_projected_payout'], 2) }}</div>
                            <div class="small mt-1">
                                House Profit:
                                <strong class="{{ $exposure['house_profit_if_bahar'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $exposure['house_profit_if_bahar'] >= 0 ? '+' : '' }}₹{{ number_format($exposure['house_profit_if_bahar'], 2) }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Safest Side Recommendation -->
                <div class="p-3 rounded-3 bg-primary-subtle border border-primary-subtle">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="fa-solid fa-shield-halved text-primary fs-5"></i>
                        <span class="fw-bold text-primary">AI Risk Recommendation:</span>
                    </div>
                    <div class="small text-dark">
                        Safest outcome for maximum House Margin:
                        <span class="badge {{ $exposure['safest_side'] === 'andar' ? 'bg-primary' : ($exposure['safest_side'] === 'bahar' ? 'bg-danger' : 'bg-secondary') }} text-uppercase px-2 py-1 fs-6">
                            {{ $exposure['safest_side'] }}
                        </span>
                        <div class="text-muted mt-1" style="font-size: 0.8rem;">
                            (Calculated based on currently placed bets and configured multipliers)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row: Multiplier & Limits Configuration ("winner ko kitna guna milega") -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-coins text-warning me-2"></i> Payout Multiplier & Limit Configuration
                    </h5>
                    <small class="text-muted">Set exactly how much winners earn (kitna guna milega) for Andar, Bahar, and Side Bet Ranges.</small>
                </div>
                <span class="badge bg-primary px-3 py-2 rounded-pill">Real-time Global Settings</span>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.andar-bahar.settings') }}">
                    @csrf
                    <div class="row g-3">
                        <!-- Main Multipliers -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Andar Multiplier (Guna)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white fw-bold"><i class="fa-solid fa-circle-dot"></i></span>
                                <input type="number" step="0.01" min="1.01" max="10.00" name="andar_multiplier"
                                       value="{{ old('andar_multiplier', $setting->andar_multiplier ?? '1.95') }}"
                                       class="form-control fw-bold" required>
                                <span class="input-group-text fw-bold">x</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">Standard: 1.95x (₹100 bet pays ₹195)</small>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Bahar Multiplier (Guna)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-danger text-white fw-bold"><i class="fa-solid fa-circle-dot"></i></span>
                                <input type="number" step="0.01" min="1.01" max="10.00" name="bahar_multiplier"
                                       value="{{ old('bahar_multiplier', $setting->bahar_multiplier ?? '1.95') }}"
                                       class="form-control fw-bold" required>
                                <span class="input-group-text fw-bold">x</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">Standard: 1.95x (₹100 bet pays ₹195)</small>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Minimum Bet (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">₹</span>
                                <input type="number" step="1" min="1" max="50000" name="andar_bahar_min_bet"
                                       value="{{ old('andar_bahar_min_bet', $setting->andar_bahar_min_bet ?? '10.00') }}"
                                       class="form-control fw-bold" required>
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">Min allowed chip value</small>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Maximum Bet (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">₹</span>
                                <input type="number" step="10" min="10" max="500000" name="andar_bahar_max_bet"
                                       value="{{ old('andar_bahar_max_bet', $setting->andar_bahar_max_bet ?? '50000.00') }}"
                                       class="form-control fw-bold" required>
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">Max single wager limit</small>
                        </div>

                        <div class="col-12"><hr class="my-2"></div>
                        <div class="col-12">
                            <div class="fw-bold text-dark small text-uppercase mb-2">
                                <i class="fa-solid fa-chart-simple text-info me-1"></i> Side Bet: Total Cards Dealt Range Multipliers (Optional side wagers)
                            </div>
                        </div>

                        <div class="col-md-2 col-sm-4 col-6">
                            <label class="form-label small text-muted">1 to 5 Cards</label>
                            <div class="input-group input-group-sm">
                                <input type="number" step="0.05" min="1.1" max="50" name="andar_bahar_range_1_5"
                                       value="{{ old('andar_bahar_range_1_5', $setting->andar_bahar_range_1_5 ?? '3.50') }}"
                                       class="form-control fw-bold" required>
                                <span class="input-group-text">x</span>
                            </div>
                        </div>

                        <div class="col-md-2 col-sm-4 col-6">
                            <label class="form-label small text-muted">6 to 10 Cards</label>
                            <div class="input-group input-group-sm">
                                <input type="number" step="0.05" min="1.1" max="50" name="andar_bahar_range_6_10"
                                       value="{{ old('andar_bahar_range_6_10', $setting->andar_bahar_range_6_10 ?? '4.00') }}"
                                       class="form-control fw-bold" required>
                                <span class="input-group-text">x</span>
                            </div>
                        </div>

                        <div class="col-md-2 col-sm-4 col-6">
                            <label class="form-label small text-muted">11 to 15 Cards</label>
                            <div class="input-group input-group-sm">
                                <input type="number" step="0.05" min="1.1" max="50" name="andar_bahar_range_11_15"
                                       value="{{ old('andar_bahar_range_11_15', $setting->andar_bahar_range_11_15 ?? '5.00') }}"
                                       class="form-control fw-bold" required>
                                <span class="input-group-text">x</span>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6 col-6">
                            <label class="form-label small text-muted">16 to 25 Cards</label>
                            <div class="input-group input-group-sm">
                                <input type="number" step="0.05" min="1.1" max="50" name="andar_bahar_range_16_25"
                                       value="{{ old('andar_bahar_range_16_25', $setting->andar_bahar_range_16_25 ?? '4.50') }}"
                                       class="form-control fw-bold" required>
                                <span class="input-group-text">x</span>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6 col-6">
                            <label class="form-label small text-muted">26+ Cards</label>
                            <div class="input-group input-group-sm">
                                <input type="number" step="0.05" min="1.1" max="50" name="andar_bahar_range_26_plus"
                                       value="{{ old('andar_bahar_range_26_plus', $setting->andar_bahar_range_26_plus ?? '8.00') }}"
                                       class="form-control fw-bold" required>
                                <span class="input-group-text">x</span>
                            </div>
                        </div>

                        <div class="col-12 text-end mt-3">
                            <button type="submit" class="btn btn-primary fw-bold px-4 py-2 rounded-pill shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save Payout Multipliers & Limits
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Row: Active Bets Table & Recent Rounds History -->
<div class="row g-4 mb-4">
    <!-- Active Bets on this Round -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-receipt text-primary me-2"></i> Active Bets (Round #{{ $round->round_id }})
                </h5>
                <span class="badge bg-secondary rounded-pill">{{ $activeBets->count() }} Bets</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted text-uppercase">
                                <th class="ps-4">User</th>
                                <th>Side / Bet</th>
                                <th>Stake</th>
                                <th>Multiplier</th>
                                <th class="pe-4 text-end">Est. Payout</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeBets as $bet)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $bet->user->name ?? 'User #'.$bet->user_id }}</div>
                                        <div class="small text-muted">{{ $bet->user->phone ?? '' }}</div>
                                    </td>
                                    <td>
                                        @if(in_array(strtolower($bet->bet_selection), ['andar', 'bahar']))
                                            <span class="badge {{ strtolower($bet->bet_selection) === 'andar' ? 'bg-primary' : 'bg-danger' }} text-uppercase px-2 py-1">
                                                {{ strtoupper($bet->bet_selection) }}
                                            </span>
                                        @else
                                            <span class="badge bg-info text-dark px-2 py-1">
                                                {{ $bet->bet_selection }} cards
                                            </span>
                                        @endif
                                    </td>
                                    <td class="fw-bold font-monospace">₹{{ number_format($bet->amount, 2) }}</td>
                                    <td class="text-muted font-monospace">{{ $bet->multiplier }}x</td>
                                    <td class="pe-4 text-end fw-bold text-success font-monospace">
                                        ₹{{ number_format($bet->amount * $bet->multiplier, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-inbox fs-3 mb-2 d-block text-secondary"></i>
                                        No bets placed on this round yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Completed Rounds -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-clock-rotate-left text-warning me-2"></i> Recent Rounds History
                </h5>
                <span class="badge bg-light text-dark border">Last {{ $recentRounds->count() }} Rounds</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted text-uppercase">
                                <th class="ps-4">Round ID</th>
                                <th>Joker</th>
                                <th>Winner</th>
                                <th>Matching Card</th>
                                <th>Cards Dealt</th>
                                <th class="pe-4 text-end">Mode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRounds as $r)
                                <tr>
                                    <td class="ps-4 font-monospace fw-bold text-dark">#{{ $r->round_id }}</td>
                                    <td>
                                        @php
                                            $j = $r->joker_card;
                                            $symJ = $j ? ($j['symbol'] ?? (match($j['suit'] ?? '') { 'H' => '♥', 'D' => '♦', 'C' => '♣', default => '♠' })) : '';
                                            $isRedJ = $j ? (($j['color'] ?? '') === 'red' || in_array($j['suit'] ?? '', ['H', 'D', '♥', '♦'])) : false;
                                        @endphp
                                        <span class="badge bg-light border fw-bold {{ $isRedJ ? 'text-danger' : 'text-dark' }}">
                                            {{ $j['rank'] ?? '?' }}{{ $symJ }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $r->winning_side === 'andar' ? 'bg-primary' : 'bg-danger' }} text-uppercase px-2 py-1">
                                            {{ strtoupper($r->winning_side) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $wc = $r->winning_card;
                                            $symW = $wc ? ($wc['symbol'] ?? (match($wc['suit'] ?? '') { 'H' => '♥', 'D' => '♦', 'C' => '♣', default => '♠' })) : '';
                                            $isRedW = $wc ? (($wc['color'] ?? '') === 'red' || in_array($wc['suit'] ?? '', ['H', 'D', '♥', '♦'])) : false;
                                        @endphp
                                        @if($wc)
                                            <span class="badge bg-light border fw-bold {{ $isRedW ? 'text-danger' : 'text-dark' }}">
                                                {{ $wc['rank'] ?? '' }}{{ $symW }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="font-monospace">{{ $r->total_cards_dealt ?? '-' }} cards</td>
                                    <td class="pe-4 text-end">
                                        @if($r->is_manual)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">MANUAL</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">AUTO</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No completed rounds yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Live countdown timer synchronization on admin page
    let remaining = {{ (int) $roundData['remaining_seconds'] }};
    const timerEl = document.getElementById('adminTimerDisplay');

    setInterval(() => {
        if (remaining > 0) {
            remaining--;
            if (timerEl) {
                timerEl.textContent = remaining + 's';
            }
        } else {
            // Auto reload when round completes to fetch the fresh round
            location.reload();
        }
    }, 1000);
</script>
@endsection
