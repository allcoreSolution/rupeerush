@extends('layouts.admin')

@section('title', 'Game Result Controlling')
@section('page_title', 'Result Controlling & Manipulation Hub')

@section('content')
<!-- Success / Error Alerts -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 d-flex align-items-center gap-3 p-3 mb-4" role="alert">
        <i class="fa-solid fa-circle-check fs-4 text-success"></i>
        <div>
            <strong>Success!</strong> {{ session('success') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 d-flex align-items-center gap-3 p-3 mb-4" role="alert">
        <i class="fa-solid fa-triangle-exclamation fs-4 text-danger"></i>
        <div>
            <strong>Error!</strong> {{ session('error') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Top Action Strip: Next Slot & Quick Controls -->
<div class="card-custom mb-4 border-warning">
    <div class="card-custom-body bg-light py-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">
                    <i class="fa-solid fa-clock me-1"></i> NEXT UPCOMING SLOT
                </span>
                <span class="fs-5 fw-bold text-dark">{{ $nextSlot['slot_time'] }}</span>
                <span class="text-muted small">(Auto-generation at {{ $nextSlot['gen_datetime']->format('h:i:s A') }})</span>
            </div>
            <div class="text-muted small mt-1">
                You can preset numbers below for upcoming slots, or edit past slots. Changing results immediately re-evaluates all player bets and credits winners.
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.winners.index') }}" class="btn btn-outline-warning rounded-pill px-3 py-2 fw-bold text-dark shadow-sm">
                <i class="fa-solid fa-trophy text-warning me-1"></i> View Winner List
            </a>
            <form method="POST" action="{{ route('admin.results.instant-generate') }}" onsubmit="return confirm('Instantly generate results and execute payouts for {{ $nextSlot['slot_time'] }}?');">
                @csrf
                <button type="submit" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark shadow-sm">
                    <i class="fa-solid fa-bolt me-1"></i> Instant Generate & Pay
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Manual Number Override Card -->
    <div class="col-lg-7">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h6 class="card-custom-title">
                    <i class="fa-solid fa-sliders text-primary"></i>
                    <span>Manual Result Override (10 Slot Columns)</span>
                </h6>
                <span class="badge bg-light text-primary border font-monospace" id="slotStatusBadge">
                    Target: {{ $targetSlotTime }}
                </span>
            </div>
            <div class="card-custom-body">
                <form method="POST" action="{{ route('admin.results.manual') }}" id="manualResultForm">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="resDate" class="form-label fw-bold small text-muted">Result Date</label>
                            <input type="date" class="form-control" id="resDate" name="result_date" value="{{ $selectedDate->format('Y-m-d') }}" onchange="changeFilterDate(this.value)" required>
                        </div>
                        <div class="col-md-6">
                            <label for="slotSelect" class="form-label fw-bold small text-muted">Select Target Slot</label>
                            <select class="form-select fw-bold text-primary" id="slotSelect" name="slot_time" onchange="onSlotChange(this.value)" required>
                                @foreach($slots as $s)
                                    <option value="{{ $s->slot_time }}" {{ $s->slot_time === $targetSlotTime ? 'selected' : '' }}>
                                        {{ $s->slot_time }} 
                                        @if($s->slot_time === $nextSlot['slot_time'])
                                            (Upcoming Next)
                                        @elseif($results->has($s->slot_time))
                                            ({{ ucfirst($results->get($s->slot_time)->status) }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- 10 Slot Columns Input Grid (2 rows of 5) -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label fw-bold small text-muted mb-0">Enter 2-Digit Values for 10 Columns (00 to 99):</label>
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2 py-0" style="font-size: 0.75rem;" onclick="generateRandom10()">
                            <i class="fa-solid fa-shuffle me-1"></i> Randomize All
                        </button>
                    </div>

                    <div class="row g-2 mb-3">
                        @foreach($slotKeys as $k)
                            @php
                                $val = old($k);
                                if ($val === null && $targetSlotResult) {
                                    $val = $targetSlotResult->{$k};
                                }
                                if ($val === null) {
                                    $val = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);
                                }
                            @endphp
                            <div class="col-6 col-md-2" style="flex: 0 0 20%; max-width: 20%;">
                                <div class="p-2 border rounded-3 bg-light text-center transition-all" id="box_{{ $k }}">
                                    <label class="fw-bold text-primary small d-block mb-1">{{ $k }}</label>
                                    <input type="text" 
                                           class="form-control text-center font-monospace fw-bold fs-5 px-1 py-1 column-input" 
                                           id="input_{{ $k }}" 
                                           name="{{ $k }}" 
                                           maxlength="2" 
                                           pattern="[0-9]{2}" 
                                           placeholder="00" 
                                           value="{{ $val }}" 
                                           required>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="alert alert-info border-0 rounded-3 p-3 small mb-3">
                        <i class="fa-solid fa-circle-info me-1 text-info"></i>
                        <strong>Player-Bet Accurate:</strong> Click any player's bet from the right side to automatically set that winning number here. When you save, all winning bets are immediately credited to players' wallets!
                    </div>

                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-3 border-top">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="publish_now" id="pubNow" value="1" {{ ($targetSlotResult && $targetSlotResult->status === 'published') ? 'checked' : '' }}>
                            <label class="form-check-label small fw-bold text-dark" for="pubNow">
                                Publish Immediately & Credit Winning Wallets
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" style="background: #0077b6; border: none;">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save & Apply Result
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Active Bets & Winner Picking for Target Slot -->
    <div class="col-lg-5">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h6 class="card-custom-title">
                    <i class="fa-solid fa-bullseye text-danger"></i>
                    <span>Player Bets on {{ $targetSlotTime }}</span>
                </h6>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill small fw-bold">
                    {{ $betsOnTargetSlot->count() }} Bets Active
                </span>
            </div>
            <div class="card-custom-body p-0">
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center small text-muted">
                    <div>Total Wagers: <strong class="text-dark">₹{{ number_format($betsOnTargetSlot->sum('amount'), 2) }}</strong></div>
                    <div>Potential Payout: <strong class="text-danger">₹{{ number_format($betsOnTargetSlot->sum(fn($b) => $b->amount * $b->win_multiplier), 2) }}</strong></div>
                </div>

                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light small text-muted">
                            <tr>
                                <th>Player</th>
                                <th>Col</th>
                                <th>Chosen #</th>
                                <th>Bet Stake</th>
                                <th>Payout</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($betsOnTargetSlot as $b)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark small">{{ $b->user->name ?? 'User' }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $b->user->phone ?? '' }}</div>
                                    </td>
                                    <td><span class="badge bg-primary px-2 py-1 rounded-pill">{{ $b->slot_key }}</span></td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace fs-6 px-2 py-0 fw-bold">
                                            {{ $b->chosen_number }}
                                        </span>
                                    </td>
                                    <td class="font-monospace small">₹{{ number_format($b->amount, 0) }}</td>
                                    <td class="font-monospace small fw-bold text-danger">₹{{ number_format($b->amount * $b->win_multiplier, 0) }}</td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-outline-success btn-sm rounded-pill px-2 py-0 fw-bold" 
                                                style="font-size: 0.72rem;" 
                                                onclick="setWinningNumber('{{ $b->slot_key }}', '{{ $b->chosen_number }}', '{{ $b->user->name ?? 'Player' }}')">
                                            <i class="fa-solid fa-trophy me-1"></i> Make Winner
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted small">
                                        No bets placed on {{ $targetSlotTime }} yet.
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

<!-- Full Day Slot Result Schedule Table -->
<div class="card-custom mb-4">
    <div class="card-custom-header">
        <h6 class="card-custom-title">
            <i class="fa-solid fa-list-ol text-primary"></i>
            <span>Daily Slot Sequence ({{ $selectedDate->format('d F Y') }})</span>
        </h6>

        <!-- Date Filter -->
        <form method="GET" action="{{ route('admin.results.index') }}" class="d-flex align-items-center gap-2">
            <input type="date" class="form-control form-control-sm" name="date" value="{{ $selectedDate->format('Y-m-d') }}">
            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold">View Date</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-center">
            <thead class="table-light small text-muted">
                <tr>
                    <th class="text-start">Time Slot</th>
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
                    <th>Mode</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($slots as $s)
                    @php
                        $res = $results->get($s->slot_time);
                    @endphp
                    <tr class="{{ $s->slot_time === $targetSlotTime ? 'table-primary table-opacity-10' : '' }}">
                        <td class="text-start fw-bold text-dark font-monospace">{{ $s->slot_time }}</td>
                        @foreach($slotKeys as $k)
                            <td class="font-monospace fw-bold {{ $res ? 'text-primary fs-6' : 'text-muted' }}">
                                {{ $res ? $res->{$k} : '-' }}
                            </td>
                        @endforeach
                        <td>
                            @if($res && $res->is_manual)
                                <span class="badge bg-purple text-white px-2 py-1 rounded-pill small" style="background: #8b5cf6;">Manual</span>
                            @else
                                <span class="badge bg-light text-muted border px-2 py-1 rounded-pill small">Auto</span>
                            @endif
                        </td>
                        <td>
                            @if($res && $res->status === 'published')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill fw-bold">
                                    Published
                                </span>
                            @elseif($res && $res->status === 'scheduled')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill fw-bold">
                                    Scheduled
                                </span>
                            @else
                                <span class="badge bg-light text-muted border px-2 py-1 rounded-pill">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td>
                            <button type="button" 
                                    class="btn btn-sm {{ $s->slot_time === $targetSlotTime ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3 py-0 fw-bold" 
                                    style="font-size: 0.75rem;" 
                                    onclick="editSlotResult('{{ $s->slot_time }}')">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Winners for Selected Date -->
<div class="card-custom">
    <div class="card-custom-header">
        <div class="d-flex align-items-center gap-2">
            <h6 class="card-custom-title mb-0">
                <i class="fa-solid fa-trophy text-warning"></i>
                <span>Recent Winners for {{ $selectedDate->format('d M Y') }}</span>
            </h6>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill fw-bold">
                {{ $recentWinners->count() }} Listed
            </span>
        </div>
        <a href="{{ route('admin.winners.index', ['date' => $selectedDate->format('Y-m-d')]) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold text-dark">
            <i class="fa-solid fa-list-check me-1"></i> Full Winner List &rarr;
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted">
                <tr>
                    <th>Bet ID</th>
                    <th>Winner (Player)</th>
                    <th>Slot Time</th>
                    <th>Column</th>
                    <th>Winning Number</th>
                    <th>Stake</th>
                    <th>Prize Payout</th>
                    <th>Evaluated At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentWinners as $rw)
                    <tr>
                        <td class="font-monospace small text-muted">#{{ $rw->id }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $rw->user->name ?? 'User' }}</div>
                            <div class="text-muted small"><i class="fa-solid fa-phone me-1"></i>{{ $rw->user->phone ?? 'N/A' }}</div>
                        </td>
                        <td class="font-monospace fw-bold text-primary">{{ $rw->slot_time }}</td>
                        <td><span class="badge bg-primary px-2 py-1 rounded-pill">{{ $rw->slot_key }}</span></td>
                        <td>
                            <span class="badge bg-warning-subtle text-dark border border-warning font-monospace fs-6 px-3 py-1 rounded-3 fw-bold">
                                {{ $rw->chosen_number }}
                            </span>
                        </td>
                        <td class="font-monospace">₹{{ number_format($rw->amount, 2) }}</td>
                        <td class="font-monospace fw-bold text-success fs-6">+₹{{ number_format($rw->win_amount, 2) }}</td>
                        <td class="small text-muted">
                            {{ $rw->evaluated_at ? $rw->evaluated_at->timezone('Asia/Kolkata')->format('h:i A') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted small">
                            No winning bets recorded for this date yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // All known numbers for each slot from database
    const slotResultsData = @json($results->mapWithKeys(fn($r) => [$r->slot_time => $r->getNumbersArray()]));

    function onSlotChange(slotTime) {
        // Reload page with query param so bets on that slot are fetched
        const currentDate = document.getElementById('resDate').value;
        window.location.href = "{{ route('admin.results.index') }}?date=" + currentDate + "&slot_time=" + encodeURIComponent(slotTime);
    }

    function changeFilterDate(dateStr) {
        window.location.href = "{{ route('admin.results.index') }}?date=" + dateStr;
    }

    function editSlotResult(slotTime) {
        onSlotChange(slotTime);
    }

    function setWinningNumber(slotKey, number, playerName) {
        const input = document.getElementById('input_' + slotKey);
        const box = document.getElementById('box_' + slotKey);
        if (input) {
            input.value = number;
            box.classList.add('border-success', 'bg-success', 'bg-opacity-25');
            setTimeout(() => {
                box.classList.remove('border-success', 'bg-success', 'bg-opacity-25');
            }, 1500);

            // Also check the publish_now box
            document.getElementById('pubNow').checked = true;
        }
    }

    function generateRandom10() {
        const slotKeys = ['XA','XB','XC','XD','XE','XF','XG','XH','XI','XJ'];
        slotKeys.forEach(k => {
            const num = Math.floor(Math.random() * 100).toString().padStart(2, '0');
            const inp = document.getElementById('input_' + k);
            if (inp) inp.value = num;
        });
    }
</script>
@endsection
