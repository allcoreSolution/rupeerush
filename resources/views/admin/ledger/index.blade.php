@extends('layouts.admin')

@section('title', 'Player Ledger & Financial Hub')
@section('page_title', 'Player Account Ledger & Statements')

@section('styles')
<style>
    @media (min-width: 1400px) {
        .row-cols-xxl-7 > * {
            flex: 0 0 auto;
            width: 14.285714%;
        }
    }
</style>
@endsection

@section('content')
<!-- TOP KPI METRIC CARDS -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 row-cols-xxl-7 g-3 mb-4">
    <!-- 1. Total Players -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #0284c7 !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary mb-2" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-users fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Total Players</div>
                <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($globalStats['total_players']) }}</div>
                <small class="text-success fw-semibold"><i class="fa-solid fa-circle-dot me-1"></i>{{ $globalStats['active_players'] }} Active</small>
            </div>
        </div>
    </div>

    <!-- 2. Total Current Player Balances -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #0ea5e9 !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info mb-2" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-wallet fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Wallet Exposure</div>
                <div class="fs-4 fw-bold text-primary mt-1 font-monospace">₹{{ number_format($globalStats['total_player_balance'], 2) }}</div>
                <small class="text-muted">Total in player wallets</small>
            </div>
        </div>
    </div>

    <!-- 3. Total Deposits -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #10b981 !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success mb-2" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-circle-arrow-down fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Total Deposits</div>
                <div class="fs-4 fw-bold text-success mt-1 font-monospace">₹{{ number_format($globalStats['total_deposits'], 2) }}</div>
                <small class="text-success"><i class="fa-solid fa-arrow-down me-1"></i>{{ $globalStats['approved_deposits_count'] }} Approved</small>
            </div>
        </div>
    </div>

    <!-- 4. Total Withdrawals -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #f59e0b !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning mb-2" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-arrow-up-from-bracket fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Total Withdrawals</div>
                <div class="fs-4 fw-bold text-dark mt-1 font-monospace">₹{{ number_format($globalStats['total_withdrawals'], 2) }}</div>
                <small class="text-danger"><i class="fa-solid fa-arrow-up me-1"></i>{{ $globalStats['settled_withdrawals_count'] }} Settled</small>
            </div>
        </div>
    </div>

    <!-- 5. Total Adjusted Balance -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #7c3aed !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px; background: rgba(124, 58, 237, 0.12); color: #7c3aed;">
                <i class="fa-solid fa-sliders fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem; color: #7c3aed !important;">Adjusted Balance</div>
                <div class="fs-4 fw-bold mt-1 font-monospace" style="color: #7c3aed;">
                    {{ $globalStats['net_adjusted'] >= 0 ? '+' : '' }}₹{{ number_format($globalStats['net_adjusted'], 2) }}
                </div>
                <small class="text-muted" style="font-size: 0.72rem;">
                    +₹{{ number_format($globalStats['total_adjusted_credits'], 0) }} Cr | -₹{{ number_format($globalStats['total_adjusted_debits'], 0) }} Dr
                </small>
            </div>
        </div>
    </div>

    <!-- 6. Total Turnover (Wagered) -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border-left: 4px solid #64748b !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 text-secondary mb-2" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-coins fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Total Bets Wagered</div>
                <div class="fs-4 fw-bold text-dark mt-1 font-monospace">₹{{ number_format($globalStats['total_bets'], 2) }}</div>
                <small class="text-muted">{{ $globalStats['total_bets_count'] }} Total Bets</small>
            </div>
        </div>
    </div>

    <!-- 7. Gross Gaming Revenue (Platform P/L) -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="{{ $globalStats['net_ggr'] >= 0 ? 'border-left: 4px solid #10b981 !important;' : 'border-left: 4px solid #ef4444 !important;' }}">
            <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 {{ $globalStats['net_ggr'] >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-chart-line fs-5"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Platform GGR</div>
                <div class="fs-4 fw-bold {{ $globalStats['net_ggr'] >= 0 ? 'text-success' : 'text-danger' }} mt-1 font-monospace">
                    {{ $globalStats['net_ggr'] >= 0 ? '+' : '' }}₹{{ number_format($globalStats['net_ggr'], 2) }}
                </div>
                <small class="{{ $globalStats['net_ggr'] >= 0 ? 'text-success' : 'text-danger' }} fw-semibold" style="font-size: 0.72rem;">
                    {{ $globalStats['net_ggr'] >= 0 ? 'House Profit' : 'Player Surplus' }}
                </small>
            </div>
        </div>
    </div>
</div>

<!-- PLAYERS MASTER LEDGER CARD -->
<div class="card-custom">
    <div class="card-custom-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <h6 class="card-custom-title mb-0">
                <i class="fa-solid fa-file-invoice-dollar text-primary"></i>
                <span>Players Ledger Master</span>
            </h6>
            <span class="badge bg-light text-dark border font-monospace ms-2">{{ $players->total() }} Total Players</span>
        </div>

        <!-- Search & Sort Filter -->
        <form method="GET" action="{{ route('admin.ledger.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
            <div class="input-group input-group-sm" style="min-width: 250px;">
                <input type="text" class="form-control" name="search" placeholder="Search by name, phone, email, ID..." value="{{ $search }}">
                @if($search !== '')
                    <a href="{{ route('admin.ledger.index') }}" class="btn btn-outline-secondary" title="Clear Search"><i class="fa-solid fa-xmark"></i></a>
                @endif
                <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Search</button>
            </div>

            <!-- Sort By -->
            <select name="sort" class="form-select form-select-sm" style="width: 170px;" onchange="this.form.submit()">
                <option value="id_desc" {{ $sortBy === 'id_desc' ? 'selected' : '' }}>Newest Players First</option>
                <option value="balance_desc" {{ $sortBy === 'balance_desc' ? 'selected' : '' }}>Highest Balance</option>
                <option value="balance_asc" {{ $sortBy === 'balance_asc' ? 'selected' : '' }}>Lowest Balance</option>
                <option value="deposited_desc" {{ $sortBy === 'deposited_desc' ? 'selected' : '' }}>Top Depositors</option>
                <option value="withdrawn_desc" {{ $sortBy === 'withdrawn_desc' ? 'selected' : '' }}>Top Withdrawals</option>
                <option value="bet_desc" {{ $sortBy === 'bet_desc' ? 'selected' : '' }}>Top Turnover (Wagered)</option>
                <option value="won_desc" {{ $sortBy === 'won_desc' ? 'selected' : '' }}>Top Winnings</option>
                <option value="name_asc" {{ $sortBy === 'name_asc' ? 'selected' : '' }}>Name (A to Z)</option>
            </select>
        </form>
    </div>

    <!-- Players Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                <tr>
                    <th class="ps-3">Player</th>
                    <th>Mobile Phone</th>
                    <th>Current Balance</th>
                    <th>Total Deposited</th>
                    <th>Total Withdrawn</th>
                    <th>Adjusted (+ / -)</th>
                    <th>Turnover (Bets)</th>
                    <th>Total Won</th>
                    <th>Player Net P/L</th>
                    <th>Status</th>
                    <th class="pe-3 text-end">Ledger Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($players as $u)
                    @php
                        $dep = (float) ($u->total_deposited ?? 0);
                        $with = (float) ($u->total_withdrawn ?? 0);
                        $adjCr = (float) ($u->total_adjusted_credits ?? 0);
                        $adjDr = (float) ($u->total_adjusted_debits ?? 0);
                        $netAdj = $adjCr - $adjDr;
                        $bet = (float) ($u->total_bet ?? 0);
                        $won = (float) ($u->total_won ?? 0);
                        $netPlayerPL = $won - $bet;
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold font-monospace" style="width: 38px; height: 38px; font-size: 0.85rem;">
                                    {{ strtoupper(substr($u->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">
                                        <a href="{{ route('admin.ledger.show', $u->id) }}" class="text-decoration-none text-dark hover-primary">
                                            {{ $u->name }}
                                        </a>
                                        <span class="badge bg-light text-muted border ms-1 font-monospace" style="font-size: 0.7rem;">#{{ $u->id }}</span>
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                        {{ $u->email ?: 'No email registered' }} &bull; Joined {{ $u->created_at?->format('d M Y') }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="font-monospace fw-bold text-dark">{{ $u->phone }}</span>
                        </td>
                        <td>
                            <span class="font-monospace fw-bold text-primary fs-6">
                                ₹{{ number_format($u->balance, 2) }}
                            </span>
                        </td>
                        <td>
                            <div class="font-monospace fw-bold text-success">
                                ₹{{ number_format($dep, 2) }}
                            </div>
                        </td>
                        <td>
                            <div class="font-monospace fw-bold text-secondary">
                                ₹{{ number_format($with, 2) }}
                            </div>
                        </td>
                        <td>
                            @if($netAdj != 0 || $adjCr > 0 || $adjDr > 0)
                                <div class="font-monospace fw-bold" style="color: #7c3aed;">
                                    {{ $netAdj >= 0 ? '+' : '' }}₹{{ number_format($netAdj, 2) }}
                                </div>
                                <small class="text-muted font-monospace" style="font-size: 0.68rem;">
                                    +{{ number_format($adjCr, 0) }} | -{{ number_format($adjDr, 0) }}
                                </small>
                            @else
                                <span class="text-muted font-monospace">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="font-monospace fw-bold text-dark">
                                ₹{{ number_format($bet, 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="font-monospace fw-bold text-success">
                                ₹{{ number_format($won, 2) }}
                            </span>
                        </td>
                        <td>
                            @if($netPlayerPL > 0)
                                <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace fw-bold px-2 py-1">
                                    +₹{{ number_format($netPlayerPL, 2) }}
                                </span>
                            @elseif($netPlayerPL < 0)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-monospace fw-bold px-2 py-1">
                                    -₹{{ number_format(abs($netPlayerPL), 2) }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted border font-monospace px-2 py-1">₹0.00</span>
                            @endif
                        </td>
                        <td>
                            @if($u->status === 'active')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">Banned</span>
                            @endif
                        </td>
                        <td class="pe-3 text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <!-- View Statement / Ledger Button -->
                                <a href="{{ route('admin.ledger.show', $u->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm d-inline-flex align-items-center gap-1">
                                    <i class="fa-solid fa-receipt"></i>
                                    <span>Statement</span>
                                </a>

                                <!-- Quick Adjust Balance Button -->
                                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2" title="Adjust Balance" onclick="openBalanceModal('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ $u->balance }}')">
                                    <i class="fa-solid fa-wallet text-dark"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-users fs-2 mb-2 d-block text-secondary opacity-50"></i>
                            No player records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($players->hasPages())
        <div class="p-3 border-top bg-light">
            {{ $players->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Modal: Adjust Balance -->
<div class="modal fade" id="balanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <form id="balanceForm" method="POST" action="">
                @csrf
                <div class="modal-header bg-dark text-white p-3">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i class="fa-solid fa-wallet text-warning"></i>
                        <span>Adjust Player Wallet Balance</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Player Account</label>
                        <input type="text" class="form-control bg-light font-monospace fw-bold" id="modalUserName" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Action Type</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="action" id="actionCredit" value="credit" checked>
                                <label class="btn btn-outline-success w-100 fw-bold py-2" for="actionCredit">
                                    <i class="fa-solid fa-plus-circle me-1"></i> Credit (+) Add
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="action" id="actionDebit" value="debit">
                                <label class="btn btn-outline-danger w-100 fw-bold py-2" for="actionDebit">
                                    <i class="fa-solid fa-minus-circle me-1"></i> Debit (-) Deduct
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Amount (₹)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">₹</span>
                            <input type="number" step="0.01" min="1" class="form-control font-monospace fw-bold fs-5" name="amount" placeholder="e.g. 500" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Audit Reason / Narration Note</label>
                        <input type="text" class="form-control" name="notes" placeholder="e.g. Manual bank deposit, bonus reward, dispute correction" required>
                        <small class="text-muted" style="font-size: 0.72rem;">This note will appear directly on the player's bank statement & ledger audit log.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-check me-1"></i> Execute Adjustment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openBalanceModal(id, name, balance) {
        document.getElementById('balanceForm').action = `{{ url('admin/ledger') }}/${id}/balance`;
        document.getElementById('modalUserName').value = `${name} (Current: ₹${Number(balance).toFixed(2)})`;
        const modal = new bootstrap.Modal(document.getElementById('balanceModal'));
        modal.show();
    }
</script>
@endsection
