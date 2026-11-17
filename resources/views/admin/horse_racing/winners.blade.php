@extends('layouts.admin')

@section('title', 'Horse Racing Derby - Winner List & Payouts')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-trophy text-warning me-2"></i> Horse Racing Derby Winner List
        </h2>
        <p class="text-muted small mb-0">Complete historical day-by-day record of all winning bets, odds multipliers, and credited payouts across race rounds.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.horse-racing.index') }}" class="btn btn-outline-primary fw-bold rounded-pill px-4">
            <i class="fa-solid fa-horse-head me-1"></i> Live Derby Control Hub
        </a>
    </div>
</div>

<!-- Stats Counter Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card h-100">
            <div>
                <div class="stat-label">Total Winning Bets</div>
                <div class="stat-value text-dark">{{ number_format($totalWinners) }}</div>
                <div class="small text-muted mt-1">All-Time: <strong>{{ number_format($allTimeWinners) }}</strong></div>
            </div>
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                <i class="fa-solid fa-trophy"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card h-100">
            <div>
                <div class="stat-label">Total Payouts Credited</div>
                <div class="stat-value text-success">₹{{ number_format($totalPayout, 2) }}</div>
                <div class="small text-muted mt-1">All-Time: <strong>₹{{ number_format($allTimePayout, 2) }}</strong></div>
            </div>
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card h-100">
            <div>
                <div class="stat-label">Total Stakes Wagered</div>
                <div class="stat-value text-primary">₹{{ number_format($totalWagered, 2) }}</div>
                <div class="small text-muted mt-1">On Winning Bets</div>
            </div>
            <div class="stat-icon" style="background: rgba(2, 132, 199, 0.15); color: #0284c7;">
                <i class="fa-solid fa-ticket"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card h-100">
            <div>
                <div class="stat-label">Today's Winners</div>
                <div class="stat-value text-warning">{{ number_format($todayWinners) }}</div>
                <div class="small text-muted mt-1">Today Payout: <strong class="text-success">₹{{ number_format($todayPayout, 2) }}</strong></div>
            </div>
            <div class="stat-icon" style="background: rgba(234, 88, 12, 0.15); color: #ea580c;">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
        </div>
    </div>
</div>

<!-- Day & Date Quick Filter Pills -->
<div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
    <span class="small fw-bold text-muted text-uppercase me-1"><i class="fa-solid fa-calendar-days me-1"></i> Quick Filter:</span>
    <a href="{{ route('admin.horse-racing.winners') }}" 
       class="btn btn-sm rounded-pill fw-semibold {{ empty($quickFilter) && empty($date) && empty($date_from) ? 'btn-primary' : 'btn-outline-secondary' }}">
       All Winners (From Day 1)
    </a>
    <a href="{{ route('admin.horse-racing.winners', array_merge(request()->query(), ['quick_filter' => 'today', 'date' => '', 'date_from' => '', 'date_to' => ''])) }}" 
       class="btn btn-sm rounded-pill fw-semibold {{ $quickFilter === 'today' ? 'btn-primary' : 'btn-outline-secondary' }}">
       Today
    </a>
    <a href="{{ route('admin.horse-racing.winners', array_merge(request()->query(), ['quick_filter' => 'yesterday', 'date' => '', 'date_from' => '', 'date_to' => ''])) }}" 
       class="btn btn-sm rounded-pill fw-semibold {{ $quickFilter === 'yesterday' ? 'btn-primary' : 'btn-outline-secondary' }}">
       Yesterday
    </a>
    <a href="{{ route('admin.horse-racing.winners', array_merge(request()->query(), ['quick_filter' => 'last_7_days', 'date' => '', 'date_from' => '', 'date_to' => ''])) }}" 
       class="btn btn-sm rounded-pill fw-semibold {{ $quickFilter === 'last_7_days' ? 'btn-primary' : 'btn-outline-secondary' }}">
       Last 7 Days
    </a>
    <a href="{{ route('admin.horse-racing.winners', array_merge(request()->query(), ['quick_filter' => 'this_month', 'date' => '', 'date_from' => '', 'date_to' => ''])) }}" 
       class="btn btn-sm rounded-pill fw-semibold {{ $quickFilter === 'this_month' ? 'btn-primary' : 'btn-outline-secondary' }}">
       This Month
    </a>
</div>

<!-- Search & Detailed Filter Form -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.horse-racing.winners') }}">
            <div class="row g-3">
                <!-- Search text -->
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-bold text-muted">Search Query</label>
                    <input type="text" name="search" class="form-control" placeholder="Player name, phone, bet ID, round ID..." value="{{ $search }}">
                </div>

                <!-- Specific Single Day -->
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label small fw-bold text-muted">Specific Day</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}">
                </div>

                <!-- Date Range From -->
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label small fw-bold text-muted">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $date_from }}">
                </div>

                <!-- Date Range To -->
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label small fw-bold text-muted">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $date_to }}">
                </div>

                <!-- Runner Filter -->
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label small fw-bold text-muted">Runner Selection</label>
                    <select name="horse_id" class="form-select">
                        <option value="">-- All Runners --</option>
                        <option value="1" {{ (string)$horse_id === '1' ? 'selected' : '' }}>#1 Thunder Bolt</option>
                        <option value="2" {{ (string)$horse_id === '2' ? 'selected' : '' }}>#2 Golden Mane</option>
                        <option value="3" {{ (string)$horse_id === '3' ? 'selected' : '' }}>#3 Shadow Runner</option>
                        <option value="4" {{ (string)$horse_id === '4' ? 'selected' : '' }}>#4 Desert King</option>
                        <option value="5" {{ (string)$horse_id === '5' ? 'selected' : '' }}>#5 Silver Blaze</option>
                        <option value="6" {{ (string)$horse_id === '6' ? 'selected' : '' }}>#6 Royal Challenger</option>
                    </select>
                </div>

                <!-- Bet Type Filter -->
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label small fw-bold text-muted">Bet Type</label>
                    <select name="bet_type" class="form-select">
                        <option value="">-- All Types --</option>
                        <option value="win" {{ $bet_type === 'win' ? 'selected' : '' }}>WIN (1st)</option>
                        <option value="place" {{ $bet_type === 'place' ? 'selected' : '' }}>PLACE (Top 2)</option>
                        <option value="show" {{ $bet_type === 'show' ? 'selected' : '' }}>SHOW (Top 3)</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label small fw-bold text-muted">Rows per Page</label>
                    <select name="per_page" class="form-select">
                        <option value="25" {{ $per_page == 25 ? 'selected' : '' }}>25 Records</option>
                        <option value="50" {{ $per_page == 50 ? 'selected' : '' }}>50 Records</option>
                        <option value="100" {{ $per_page == 100 ? 'selected' : '' }}>100 Records</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="col-lg-3 col-md-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill flex-fill fw-bold py-2" style="background: #0077b6; border: none;">
                        <i class="fa-solid fa-filter me-1"></i> Apply Filters
                    </button>
                    @if(!empty($search) || !empty($date) || !empty($date_from) || !empty($date_to) || !empty($horse_id) || !empty($bet_type) || !empty($quickFilter))
                        <a href="{{ route('admin.horse-racing.winners') }}" class="btn btn-outline-secondary rounded-pill fw-bold py-2 px-3">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Winners Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="fa-solid fa-list-check text-primary"></i>
            <span>Winners Registry</span>
        </h5>
        <div class="small text-muted">
            Showing <strong class="text-dark">{{ $winners->firstItem() ?? 0 }}-{{ $winners->lastItem() ?? 0 }}</strong> of <strong class="text-dark">{{ number_format($winners->total()) }}</strong> winning wagers
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th class="ps-4">Bet ID / Round</th>
                        <th>Player</th>
                        <th>Runner Selection</th>
                        <th>Bet Type</th>
                        <th class="text-end">Stake (₹)</th>
                        <th class="text-center">Odds</th>
                        <th class="text-end">Payout Amount</th>
                        <th class="text-end">Net Profit</th>
                        <th class="pe-4 text-end">Winning Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($winners as $win)
                        <tr>
                            <td class="ps-4 font-monospace small">
                                <div class="fw-bold text-dark">#{{ $win->bet_id }}</div>
                                <div class="text-muted">Round #{{ $win->round_id }}</div>
                            </td>
                            <td>
                                @if($win->user)
                                    <div class="fw-bold text-dark">
                                        <a href="{{ route('admin.ledger.show', $win->user->id) }}" class="text-decoration-none text-dark hover-primary">
                                            {{ $win->user->name }}
                                        </a>
                                        <span class="badge bg-light text-muted border ms-1 font-monospace" style="font-size: 0.68rem;">ID: #{{ $win->user->id }}</span>
                                    </div>
                                    <div class="text-muted small font-monospace"><i class="fa-solid fa-phone me-1 text-secondary" style="font-size: 0.7rem;"></i>{{ $win->user->phone }}</div>
                                @else
                                    <span class="text-muted small">User Deleted (#{{ $win->user_id }})</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-warning text-dark font-monospace px-2 py-1">Gate #{{ $win->horse_id }}</span>
                                    <span class="fw-bold text-dark">{{ $win->horse_name }}</span>
                                </div>
                            </td>
                            <td>
                                @if(strtoupper($win->bet_type) === 'WIN')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill font-monospace fw-bold">WIN (1st)</span>
                                @elseif(strtoupper($win->bet_type) === 'PLACE')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill font-monospace fw-bold">PLACE (Top 2)</span>
                                @else
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1 rounded-pill font-monospace fw-bold">SHOW (Top 3)</span>
                                @endif
                            </td>
                            <td class="text-end font-monospace text-dark">₹{{ number_format((float)$win->bet_amount, 2) }}</td>
                            <td class="text-center font-monospace fw-bold text-primary">{{ $win->odds }}x</td>
                            <td class="text-end font-monospace text-success fw-bold fs-6">₹{{ number_format((float)$win->payout_amount, 2) }}</td>
                            <td class="text-end font-monospace text-success fw-bold">+₹{{ number_format((float)$win->net_profit, 2) }}</td>
                            <td class="pe-4 text-end text-muted small font-monospace">
                                <div>{{ $win->created_at->timezone('Asia/Kolkata')->format('d M Y') }}</div>
                                <div class="text-secondary" style="font-size: 0.72rem;">{{ $win->created_at->timezone('Asia/Kolkata')->format('h:i:s A') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-trophy fs-1 opacity-25 mb-2 d-block"></i>
                                <h6 class="fw-bold text-dark">No Winning Records Found</h6>
                                <p class="mb-0 small text-muted">No winning bets match the selected day or search criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($winners->hasPages())
        <div class="card-footer bg-light border-0 py-3 px-4">
            {{ $winners->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
