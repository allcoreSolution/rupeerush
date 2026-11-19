@extends('layouts.admin')

@section('title', 'Game Active / Inactive Status Management')

@section('content')
<div class="container-fluid px-0">

    <!-- PAGE HEADER (COMPACT) -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2.5">
        <div>
            <div class="d-flex align-items-center gap-2 mb-0.5">
                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                    <i class="fa-solid fa-power-off me-1"></i> MASTER GAME CONTROLS
                </span>
                <span class="text-muted small" style="font-size: 0.72rem;">Live Game Activation Switch</span>
            </div>
            <h5 class="mb-0 fw-bold text-dark font-outfit" style="font-size: 1.15rem;">Platform Games On / Off Management</h5>
        </div>

        <!-- BULK EMERGENCY CONTROLS -->
        <div class="d-flex align-items-center gap-1.5">
            <form action="{{ route('admin.games.bulk-status') }}" method="POST" onsubmit="return confirm('Are you sure you want to ACTIVATE all 11 games? All games will be accessible to players.');" class="d-inline">
                @csrf
                <input type="hidden" name="action" value="activate_all">
                <button type="submit" class="btn btn-sm btn-success px-2.5 py-1.5 rounded-2 fw-bold d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 0.78rem;">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Activate All</span>
                </button>
            </form>

            <form action="{{ route('admin.games.bulk-status') }}" method="POST" onsubmit="return confirm('⚠️ WARNING: Are you sure you want to PAUSE all games? Players will not be able to place any bets until re-enabled.');" class="d-inline">
                @csrf
                <input type="hidden" name="action" value="deactivate_all">
                <button type="submit" class="btn btn-sm btn-outline-danger px-2.5 py-1.5 rounded-2 fw-bold d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 0.78rem;">
                    <i class="fa-solid fa-circle-pause"></i>
                    <span>Pause All</span>
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 py-2 px-3 mb-2.5 d-flex align-items-center" role="alert" style="font-size: 0.85rem;">
            <i class="fa-solid fa-circle-check fs-6 me-2 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 py-2 px-3 mb-2.5 d-flex align-items-center" role="alert" style="font-size: 0.85rem;">
            <i class="fa-solid fa-triangle-exclamation fs-6 me-2 text-danger"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- TOP KPI STAT CARDS (COMPACT & SPACE EFFICIENT) -->
    <div class="row g-2 mb-2.5">
        <!-- 1. Total Platform Games -->
        <div class="col-4">
            <div class="card border-0 shadow-sm rounded-3 p-2 px-2.5 bg-white h-100" style="border-left: 3px solid #0284c7 !important;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.3px;">Total Games</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 26px; height: 26px; font-size: 0.75rem;">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                </div>
                <div class="fs-5 fw-bold text-dark font-monospace" style="line-height: 1.1;">{{ $totalGames }}</div>
                <small class="text-muted" style="font-size: 0.68rem;">11 Project Modules</small>
            </div>
        </div>

        <!-- 2. Active & Accepting Bets -->
        <div class="col-4">
            <div class="card border-0 shadow-sm rounded-3 p-2 px-2.5 bg-white h-100" style="border-left: 3px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.3px;">Active &amp; Live</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success" style="width: 26px; height: 26px; font-size: 0.75rem;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <div class="fs-5 fw-bold text-success font-monospace" id="statActiveCount" style="line-height: 1.1;">{{ $activeGames }} / {{ $totalGames }}</div>
                <small class="text-success fw-semibold" style="font-size: 0.68rem;">Accepting bets</small>
            </div>
        </div>

        <!-- 3. Inactive / Under Maintenance -->
        <div class="col-4">
            <div class="card border-0 shadow-sm rounded-3 p-2 px-2.5 bg-white h-100" style="border-left: 3px solid {{ $inactiveGames > 0 ? '#ef4444' : '#64748b' }} !important;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.3px;">Inactive / Paused</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center {{ $inactiveGames > 0 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-light text-muted' }}" style="width: 26px; height: 26px; font-size: 0.75rem;">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                    </div>
                </div>
                <div class="fs-5 fw-bold {{ $inactiveGames > 0 ? 'text-danger' : 'text-dark' }} font-monospace" id="statInactiveCount" style="line-height: 1.1;">{{ $inactiveGames }}</div>
                <small class="{{ $inactiveGames > 0 ? 'text-danger fw-semibold' : 'text-muted' }}" style="font-size: 0.68rem;">
                    {{ $inactiveGames > 0 ? 'Maintenance active' : 'All games live' }}
                </small>
            </div>
        </div>
    </div>

    <!-- GAMES STATUS GRID (COMPACT 4-COLUMN CARDS) -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-3">
        <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-toggle-on text-primary"></i>
                <h6 class="mb-0 fw-bold text-dark font-outfit" style="font-size: 0.92rem;">
                    All 11 Platform Games Status &amp; Switches
                </h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-muted border px-2 py-1 rounded-pill" style="font-size: 0.68rem;">
                    <i class="fa-solid fa-bolt text-warning me-1"></i> Instant Live Toggle
                </span>
            </div>
        </div>

        <div class="p-2.5 bg-light-subtle">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xxl-4 g-2">
                @foreach($games as $game)
                    @php
                        $isActive = (bool) ($game['is_active'] ?? true);
                        $gameId = $game['id'];
                    @endphp
                    <div class="col" id="gameCol_{{ $gameId }}">
                        <div class="card rounded-3 p-2.5 h-100 bg-white shadow-sm transition-all game-control-card d-flex flex-column justify-content-between"
                             id="gameCard_{{ $gameId }}"
                             style="border: 2px solid {{ $isActive ? '#10b981' : '#ef4444' }} !important; transition: all 0.2s ease; min-height: 125px;">
                            
                            <!-- Card Header: Icon, Titles & Status Badge -->
                            <div class="d-flex align-items-center justify-content-between gap-1.5 mb-2">
                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0"
                                         style="width: 30px; height: 30px; background: {{ $game['color'] }}; font-size: 0.85rem;">
                                        <i class="fa-solid {{ $game['icon'] }}"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.84rem; line-height: 1.2;" title="{{ $game['name'] }}">
                                            {{ $game['name'] }}
                                        </div>
                                        <span class="text-muted" style="font-size: 0.65rem;">{{ $game['category'] }}</span>
                                    </div>
                                </div>

                                <!-- Dynamic Status Badge -->
                                <div id="statusBadgeWrap_{{ $gameId }}" class="flex-shrink-0">
                                    @if($isActive)
                                        <span class="badge rounded-pill px-2 py-0.5 fw-bold text-success" style="background-color: #ecfdf5; border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.66rem;">
                                            <i class="fa-solid fa-circle text-success me-1 animate-pulse" style="font-size: 6px;"></i> LIVE
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-2 py-0.5 fw-bold text-danger" style="background-color: #fef2f2; border: 1px solid rgba(239, 68, 68, 0.3); font-size: 0.66rem;">
                                            <i class="fa-solid fa-pause me-0.5"></i> PAUSED
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Middle Control Box: Status Text + Bet Count + Switch -->
                            <div class="d-flex align-items-center justify-content-between p-1.5 px-2 rounded-2 mb-2"
                                 id="toggleBar_{{ $gameId }}"
                                 style="background: {{ $isActive ? 'rgba(16, 185, 129, 0.07)' : 'rgba(239, 68, 68, 0.07)' }}; border: 1px solid {{ $isActive ? 'rgba(16, 185, 129, 0.25)' : 'rgba(239, 68, 68, 0.25)' }};">
                                
                                <div style="line-height: 1.15;">
                                    <span class="fw-bold d-block" id="toggleLabel_{{ $gameId }}" style="font-size: 0.74rem; color: {{ $isActive ? '#047857' : '#b91c1c' }};">
                                        {{ $isActive ? 'Accepting Bets' : 'Under Maintenance' }}
                                    </span>
                                    <small class="text-muted font-monospace" style="font-size: 0.65rem;">{{ number_format($game['total_bets']) }} Total Bets</small>
                                </div>

                                <!-- iOS-Style Toggle Switch -->
                                <div class="form-check form-switch m-0 p-0 d-flex align-items-center">
                                    <input class="form-check-input game-status-switch m-0"
                                           type="checkbox"
                                           role="switch"
                                           id="switch_{{ $gameId }}"
                                           data-game-id="{{ $gameId }}"
                                           data-game-name="{{ $game['name'] }}"
                                           style="width: 2.3em; height: 1.25em; cursor: pointer;"
                                           {{ $isActive ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- Bottom Action Links (Sleek Mini Buttons) -->
                            <div class="d-flex align-items-center justify-content-between gap-1 pt-1 border-top" style="font-size: 0.7rem;">
                                <a href="{{ $game['route'] }}" target="_blank" class="text-decoration-none text-muted d-inline-flex align-items-center gap-1" style="font-size: 0.7rem;" title="Test player view">
                                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.65rem;"></i>
                                    <span>Preview</span>
                                </a>

                                <a href="{{ $game['admin_route'] }}" class="text-decoration-none text-primary fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 0.7rem;" title="Manage game engine">
                                    <i class="fa-solid fa-gear" style="font-size: 0.65rem;"></i>
                                    <span>Settings</span>
                                </a>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

<!-- TOAST CONTAINER FOR REAL-TIME FEEDBACK -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;">
    <div id="statusToast" class="toast align-items-center text-white border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2 fw-semibold" id="toastMessage" style="font-size: 0.85rem;">
                <i class="fa-solid fa-circle-check fs-6"></i>
                <span>Status updated successfully.</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto btn-sm" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<style>
    .animate-pulse {
        animation: pulseDot 1.5s infinite;
    }
    @keyframes pulseDot {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.85); }
        100% { opacity: 1; transform: scale(1); }
    }
    .game-control-card:hover {
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08) !important;
        transform: translateY(-1px);
    }
    .form-switch .form-check-input:checked {
        background-color: #10b981;
        border-color: #10b981;
    }
    .form-switch .form-check-input:not(:checked) {
        background-color: #ef4444;
        border-color: #ef4444;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const switches = document.querySelectorAll('.game-status-switch');
    const toastEl = document.getElementById('statusToast');
    const toastMessage = document.getElementById('toastMessage');
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });

    switches.forEach(function (sw) {
        sw.addEventListener('change', function () {
            const gameId = this.dataset.gameId;
            const gameName = this.dataset.gameName;
            const isChecked = this.checked;

            // Optimistic UI updates
            updateCardVisuals(gameId, isChecked);

            // Send AJAX Request
            fetch("{{ url('admin/games') }}/" + encodeURIComponent(gameId) + "/toggle-status", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: isChecked
                })
            })
            .then(res => {
                if (!res.ok) throw new Error('Network error or server exception');
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    showToast(data.message, isChecked ? 'bg-success' : 'bg-danger');
                    recalculateSummaryCounts();
                } else {
                    throw new Error(data.message || 'Operation failed');
                }
            })
            .catch(err => {
                // Revert switch on error
                sw.checked = !isChecked;
                updateCardVisuals(gameId, !isChecked);
                showToast('Failed to update status: ' + err.message, 'bg-danger');
            });
        });
    });

    function updateCardVisuals(gameId, isActive) {
        const card = document.getElementById('gameCard_' + gameId);
        const badgeWrap = document.getElementById('statusBadgeWrap_' + gameId);
        const toggleBar = document.getElementById('toggleBar_' + gameId);
        const toggleLabel = document.getElementById('toggleLabel_' + gameId);

        if (isActive) {
            if (card) card.style.border = '2px solid #10b981 !important';
            if (badgeWrap) {
                badgeWrap.innerHTML = `
                    <span class="badge rounded-pill px-2 py-0.5 fw-bold text-success" style="background-color: #ecfdf5; border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.66rem;">
                        <i class="fa-solid fa-circle text-success me-1 animate-pulse" style="font-size: 6px;"></i> LIVE
                    </span>
                `;
            }
            if (toggleBar) {
                toggleBar.style.background = 'rgba(16, 185, 129, 0.07)';
                toggleBar.style.borderColor = 'rgba(16, 185, 129, 0.25)';
            }
            if (toggleLabel) {
                toggleLabel.textContent = 'Accepting Bets';
                toggleLabel.style.color = '#047857';
            }
        } else {
            if (card) card.style.border = '2px solid #ef4444 !important';
            if (badgeWrap) {
                badgeWrap.innerHTML = `
                    <span class="badge rounded-pill px-2 py-0.5 fw-bold text-danger" style="background-color: #fef2f2; border: 1px solid rgba(239, 68, 68, 0.3); font-size: 0.66rem;">
                        <i class="fa-solid fa-pause me-0.5"></i> PAUSED
                    </span>
                `;
            }
            if (toggleBar) {
                toggleBar.style.background = 'rgba(239, 68, 68, 0.07)';
                toggleBar.style.borderColor = 'rgba(239, 68, 68, 0.25)';
            }
            if (toggleLabel) {
                toggleLabel.textContent = 'Under Maintenance';
                toggleLabel.style.color = '#b91c1c';
            }
        }
    }

    function recalculateSummaryCounts() {
        const total = switches.length;
        let active = 0;
        switches.forEach(s => { if (s.checked) active++; });
        const inactive = total - active;

        const statActive = document.getElementById('statActiveCount');
        const statInactive = document.getElementById('statInactiveCount');

        if (statActive) statActive.textContent = `${active} / ${total}`;
        if (statInactive) {
            statInactive.textContent = inactive;
            statInactive.className = inactive > 0 ? 'fs-5 fw-bold text-danger font-monospace' : 'fs-5 fw-bold text-dark font-monospace';
        }
    }

    function showToast(message, bgClass) {
        toastEl.className = `toast align-items-center text-white border-0 shadow-lg rounded-3 ${bgClass}`;
        toastMessage.innerHTML = `<i class="fa-solid fa-info-circle me-1.5 fs-6"></i><span>${message}</span>`;
        toast.show();
    }
});
</script>
@endsection
