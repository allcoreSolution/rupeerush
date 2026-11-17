@extends('layouts.admin')

@section('title', 'User Management')
@section('page_title', 'User & Wallet Balance Management')

@section('content')

@if(session('new_player_credentials'))
    @php $cred = session('new_player_credentials'); @endphp
    <div class="alert alert-success border-success-subtle shadow-sm rounded-4 p-4 mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h5 class="fw-bold text-success mb-1 d-flex align-items-center gap-2">
                    <i class="fa-brands fa-whatsapp text-success fs-4"></i>
                    <span>Player Account Created - WhatsApp Login Credentials</span>
                </h5>
                <p class="text-dark small mb-2">Account is ready! You can copy and share these credentials with the player on WhatsApp:</p>
                <div class="bg-white p-3 rounded-3 border font-monospace small text-dark d-inline-block shadow-sm">
                    <div><strong>Player Name:</strong> {{ $cred['name'] }}</div>
                    <div><strong>Login Mobile:</strong> <span class="text-primary fw-bold">{{ $cred['phone'] }}</span></div>
                    <div><strong>Password:</strong> <span class="text-danger fw-bold">{{ $cred['password'] }}</span></div>
                    <div><strong>Player ID:</strong> #{{ $cred['id'] }} &bull; <strong>Referral Code:</strong> {{ $cred['referral_code'] }}</div>
                    <div><strong>Starting Balance:</strong> ₹{{ number_format($cred['balance'], 2) }}</div>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm" onclick="copyWhatsAppCredentials('{{ $cred['name'] }}', '{{ $cred['phone'] }}', '{{ $cred['password'] }}')">
                    <i class="fa-regular fa-copy me-1"></i> Copy for WhatsApp
                </button>
            </div>
        </div>
    </div>
@endif

<div class="card-custom">
    <div class="card-custom-header flex-wrap gap-2">
        <h6 class="card-custom-title mb-0">
            <i class="fa-solid fa-users text-primary"></i>
            <span>Registered Players</span>
        </h6>

        <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
            <!-- Create New Player Button -->
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createPlayerModal" style="background: #0077b6; border: none;">
                <i class="fa-solid fa-user-plus me-1"></i> + Create Player Account
            </button>

            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="search" placeholder="Search name, phone..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted">
                <tr>
                    <th>S.No.</th>
                    <th>Name</th>
                    <th>Mobile Phone</th>
                    <th>Current Balance</th>
                    <th>Total Bets</th>
                    <th>Status</th>
                    <th>Joined At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td class="font-monospace fw-semibold text-secondary">{{ ($users->firstItem() ?? 1) + $loop->index }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $u->name }} <span class="badge bg-light text-muted border ms-1 font-monospace" style="font-size: 0.72rem;">ID: #{{ $u->id }}</span></div>
                            <small class="text-muted">{{ $u->email ?: 'No email' }}</small>
                        </td>
                        <td class="font-monospace fw-bold text-dark">{{ $u->phone }}</td>
                        <td class="font-monospace fw-bold text-success fs-6">
                            ₹{{ number_format($u->balance, 2) }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $u->bets_count }} Bets</span>
                        </td>
                        <td>
                            @if($u->status === 'active')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">Banned</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $u->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <!-- Statement / Ledger -->
                                <a href="{{ route('admin.ledger.show', $u->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold" title="View Full Passbook & Statement">
                                    <i class="fa-solid fa-file-invoice-dollar me-1"></i> Ledger
                                </a>

                                <!-- Adjust Balance Button -->
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" onclick="openBalanceModal('{{ $u->id }}', '{{ $u->name }}', '{{ $u->balance }}')">
                                    <i class="fa-solid fa-wallet me-1"></i> Adjust Balance
                                </button>

                                <!-- Toggle Status Form -->
                                <form method="POST" action="{{ route('admin.users.toggle-status', $u->id) }}" onsubmit="return confirm('Change status for {{ $u->name }}?');">
                                    @csrf
                                    @if($u->status === 'active')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Ban User">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2" title="Activate User">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="p-3 border-top bg-light">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Modal: Create New Player Account -->
<div class="modal fade" id="createPlayerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i class="fa-solid fa-user-plus text-primary"></i>
                        <span>Register New Player (WhatsApp Onboarding)</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">Fill player's personal details collected via WhatsApp. Once saved, you can copy the login credentials and share them with the player.</p>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Player Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="e.g. Rahul Sharma" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">10-Digit Mobile Phone <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">+91</span>
                            <input type="tel" class="form-control font-monospace fw-bold" name="phone" placeholder="9876543210" pattern="[0-9]{10}" maxlength="10" value="{{ old('phone') }}" required>
                        </div>
                        <div class="form-text small">This is the player's primary login ID.</div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label fw-bold small text-muted mb-0">Player Password <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small text-primary" onclick="generatePlayerPassword()">
                                <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Generate Password
                            </button>
                        </div>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace" id="newPlayerPassword" name="password" placeholder="At least 6 characters" minlength="6" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassVisibility()" title="Toggle Visibility">
                                <i class="fa-solid fa-eye" id="passToggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Email Address <span class="text-muted">(Optional)</span></label>
                        <input type="email" class="form-control" name="email" placeholder="e.g. rahul@example.com" value="{{ old('email') }}">
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">Starting Balance (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">₹</span>
                                <input type="number" step="1" min="0" class="form-control font-monospace" name="balance" placeholder="0" value="{{ old('balance', 0) }}">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">Referred By (Ref Code)</label>
                            <input type="text" class="form-control text-uppercase font-monospace" name="referral_code" placeholder="e.g. REF123456" value="{{ old('referral_code') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold" style="background: #0077b6; border: none;">
                        <i class="fa-solid fa-user-check me-1"></i> Create Player Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Adjust Balance -->
<div class="modal fade" id="balanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <form id="balanceForm" method="POST" action="">
                @csrf
                <div class="modal-header bg-dark text-white">
                    <h6 class="modal-title fw-bold" id="balanceModalTitle">Adjust User Wallet Balance</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Player Name</label>
                        <input type="text" class="form-control bg-light" id="modalUserName" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Action Type</label>
                        <select class="form-select fw-bold" name="action" required>
                            <option value="credit">Credit (+) Add to Wallet</option>
                            <option value="debit">Debit (-) Deduct from Wallet</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Amount (₹)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">₹</span>
                            <input type="number" step="1" min="1" class="form-control font-monospace fw-bold" name="amount" placeholder="e.g. 500" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Admin Note / Audit Reason</label>
                        <input type="text" class="form-control" name="notes" placeholder="e.g. Manual bank deposit, bonus, error fix" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold">Update Balance</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openBalanceModal(id, name, balance) {
        document.getElementById('balanceForm').action = `{{ url('admin/users') }}/${id}/balance`;
        document.getElementById('modalUserName').value = `${name} (Current: ₹${balance})`;
        new bootstrap.Modal(document.getElementById('balanceModal')).show();
    }

    function generatePlayerPassword() {
        const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$';
        let pass = '';
        for (let i = 0; i < 8; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('newPlayerPassword').value = pass;
    }

    function togglePassVisibility() {
        const input = document.getElementById('newPlayerPassword');
        const icon = document.getElementById('passToggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        }
    }

    function copyWhatsAppCredentials(name, phone, pass) {
        const loginUrl = window.location.origin + '/login';
        const msg = `🎉 *Welcome to RUPEERUSH!*\\n\\nHello ${name}, your player account is ready!\\n\\n📱 *Login Phone:* ${phone}\\n🔑 *Password:* ${pass}\\n🌐 *Login Link:* ${loginUrl}\\n\\nLog in now to play 2-Digit Lottery, WinGo, Aviator, Teen Patti & more!`;
        
        const cleanMsg = msg.replace(/\\\\n/g, '\n');
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(cleanMsg).then(() => {
                alert('WhatsApp Credentials copied to clipboard! You can paste them in WhatsApp.');
            });
        } else {
            const ta = document.createElement('textarea');
            ta.value = cleanMsg;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            alert('WhatsApp Credentials copied to clipboard! You can paste them in WhatsApp.');
        }
    }
</script>
@endsection
