@extends('layouts.admin')

@section('page_title', 'Staff & Role Permissions')

@section('styles')
<style>
    .modal-dialog-scrollable .modal-content {
        max-height: 90vh !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
    }
    .modal-dialog-scrollable form {
        display: flex !important;
        flex-direction: column !important;
        flex: 1 1 auto !important;
        min-height: 0 !important;
        overflow: hidden !important;
    }
    .modal-dialog-scrollable .modal-body {
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
        flex: 1 1 auto !important;
        min-height: 0 !important;
    }
    /* Modern sleek scrollbar for modal content */
    .modal-dialog-scrollable .modal-body::-webkit-scrollbar {
        width: 8px;
    }
    .modal-dialog-scrollable .modal-body::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 4px;
    }
    .modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif; color: #0f172a;">
                <i class="fa-solid fa-user-shield text-primary me-2"></i>Staff & Access Control (RBAC)
            </h3>
            <p class="text-muted small mb-0">
                Create custom administrative roles, define granular permissions for every panel section, and issue staff credentials with custom User IDs and passwords.
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                <i class="fa-solid fa-shield-plus me-1"></i> Create New Role
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createStaffModal">
                <i class="fa-solid fa-user-plus me-1"></i> Add Staff Member
            </button>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total Staff Accounts</div>
                    <div class="fs-3 fw-bold text-dark mt-1">{{ $staff->total() }}</div>
                </div>
                <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-users-gear fs-4"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Defined Roles</div>
                    <div class="fs-3 fw-bold text-dark mt-1">{{ $roles->count() }}</div>
                </div>
                <div class="rounded-circle bg-info-subtle text-info p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-id-badge fs-4"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Permission Keys</div>
                    @php
                        $totalPermsCount = 0;
                        foreach($allPermissions as $group) {
                            $totalPermsCount += count($group);
                        }
                    @endphp
                    <div class="fs-3 fw-bold text-dark mt-1">{{ $totalPermsCount }}</div>
                </div>
                <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-key fs-4"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Active Staff</div>
                    <div class="fs-3 fw-bold text-dark mt-1">{{ \App\Models\User::where('role', 'admin')->where('status', 'active')->count() }}</div>
                </div>
                <div class="rounded-circle bg-warning-subtle text-warning p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-user-check fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4 p-1 bg-white rounded-4 border shadow-sm d-inline-flex" id="rbacTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill fw-bold px-4 py-2" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staffTabContent" type="button" role="tab" aria-selected="true">
                <i class="fa-solid fa-users me-2"></i>Staff Accounts ({{ $staff->total() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-bold px-4 py-2" id="roles-tab" data-bs-toggle="tab" data-bs-target="#rolesTabContent" type="button" role="tab" aria-selected="false">
                <i class="fa-solid fa-shield-halved me-2"></i>Roles & Permissions ({{ $roles->count() }})
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="rbacTabsContent">
        <!-- ======================= TAB 1: STAFF ACCOUNTS ======================= -->
        <div class="tab-pane fade show active" id="staffTabContent" role="tabpanel">
            <div class="card-custom">
                <div class="card-custom-header">
                    <h5 class="card-custom-title">
                        <i class="fa-solid fa-address-book text-primary"></i> Administrative Staff Users
                    </h5>
                    <div class="text-muted small">
                        Staff can log in to admin panel using their <strong>User ID (Username)</strong>, <strong>Mobile Number</strong>, or <strong>Email</strong>.
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase small" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <tr>
                                <th class="ps-4">Staff Member</th>
                                <th>User ID / Username</th>
                                <th>Contact Details</th>
                                <th>Assigned Role</th>
                                <th>Account Status</th>
                                <th>Registered</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staff as $member)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" 
                                                 style="width: 40px; height: 40px; background: {{ $member->isSuperAdmin() ? 'linear-gradient(135deg, #f59e0b, #d97706)' : 'linear-gradient(135deg, #0284c7, #0369a1)' }};">
                                                {{ strtoupper(substr($member->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $member->name }}</div>
                                                @if(Auth::id() === $member->id)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0 px-2" style="font-size: 0.7rem;">You (Logged In)</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-inline-flex align-items-center gap-1 bg-light border px-2 py-1 rounded font-monospace fw-bold text-primary small">
                                            <i class="fa-solid fa-at text-muted" style="font-size: 0.75rem;"></i>
                                            <span>{{ $member->username ?? 'admin_' . $member->id }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div class="text-dark fw-semibold"><i class="fa-solid fa-phone text-muted me-1 small"></i>{{ $member->phone }}</div>
                                            @if($member->email)
                                                <div class="text-muted"><i class="fa-regular fa-envelope text-muted me-1 small"></i>{{ $member->email }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($member->isSuperAdmin())
                                            <span class="badge bg-warning text-dark border border-warning-subtle rounded-pill px-3 py-1 fw-bold">
                                                <i class="fa-solid fa-crown text-danger me-1"></i> Super Administrator
                                            </span>
                                        @elseif($member->adminRole)
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-1 fw-bold">
                                                <i class="fa-solid fa-shield-halved me-1"></i> {{ $member->adminRole->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
                                                No Role Assigned
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($member->status === 'active')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 fw-semibold">
                                                <i class="fa-solid fa-circle-check me-1"></i> Active
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 fw-semibold">
                                                <i class="fa-solid fa-circle-xmark me-1"></i> Suspended
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="small text-muted">{{ $member->created_at->format('d M Y') }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border rounded-pill px-2" type="button" data-bs-toggle="dropdown">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2">
                                                <li>
                                                    <button class="dropdown-item edit-staff-btn py-2" 
                                                            data-id="{{ $member->id }}"
                                                            data-name="{{ $member->name }}"
                                                            data-username="{{ $member->username }}"
                                                            data-phone="{{ $member->phone }}"
                                                            data-email="{{ $member->email }}"
                                                            data-role="{{ $member->admin_role_id }}"
                                                            data-status="{{ $member->status }}"
                                                            data-issuper="{{ $member->isSuperAdmin() ? '1' : '0' }}">
                                                        <i class="fa-solid fa-user-pen text-primary me-2"></i> Edit Details / Password
                                                    </button>
                                                </li>
                                                @if(Auth::id() !== $member->id)
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.staff.toggle-status', $member->id) }}" onsubmit="return confirm('Toggle status for this staff member?');">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item py-2">
                                                                @if($member->status === 'active')
                                                                    <i class="fa-solid fa-ban text-warning me-2"></i> Suspend Account
                                                                @else
                                                                    <i class="fa-solid fa-check text-success me-2"></i> Activate Account
                                                                @endif
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.staff.destroy', $member->id) }}" onsubmit="return confirm('Are you sure you want to delete this staff user permanently?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger py-2">
                                                                <i class="fa-solid fa-trash-can me-2"></i> Delete Staff Account
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-user-shield fs-1 text-muted opacity-50 mb-3 d-block"></i>
                                        No staff accounts found. Click <strong>"Add Staff Member"</strong> to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($staff->hasPages())
                    <div class="card-footer bg-white border-0 py-3">
                        {{ $staff->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ======================= TAB 2: ROLES & PERMISSIONS ======================= -->
        <div class="tab-pane fade" id="rolesTabContent" role="tabpanel">
            <div class="row g-4">
                @foreach($roles as $role)
                    <div class="col-md-6 col-xl-4">
                        <div class="card-custom h-100 d-flex flex-column mb-0 shadow-sm">
                            <div class="card-custom-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded p-2 {{ $role->is_system ? 'bg-warning-subtle text-warning' : 'bg-primary-subtle text-primary' }}">
                                        <i class="fa-solid {{ $role->is_system ? 'fa-crown' : 'fa-shield-halved' }} fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $role->name }}</h6>
                                        <code class="small text-muted">{{ $role->slug }}</code>
                                    </div>
                                </div>
                                @if($role->is_system)
                                    <span class="badge bg-warning text-dark border border-warning-subtle rounded-pill fw-bold">System Default</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fw-bold">Custom Role</span>
                                @endif
                            </div>
                            <div class="card-custom-body flex-grow-1">
                                <p class="text-muted small mb-3">
                                    {{ $role->description ?? 'No specific description provided for this administrative role.' }}
                                </p>

                                <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border mb-3">
                                    <span class="small text-muted fw-semibold">Staff Assigned:</span>
                                    <span class="badge bg-dark rounded-pill">{{ $role->users_count }} Users</span>
                                </div>

                                <div class="mb-3">
                                    <div class="small fw-bold text-dark mb-2 d-flex justify-content-between">
                                        <span>Granted Permissions:</span>
                                        <span class="text-primary">{{ $role->slug === 'super-admin' ? 'All (Unrestricted)' : count($role->permissions ?? []) . ' Active' }}</span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-1" style="max-height: 120px; overflow-y: auto;">
                                        @if($role->slug === 'super-admin')
                                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle py-1 px-2 rounded">
                                                <i class="fa-solid fa-infinity me-1"></i> Full Super Admin Privileges
                                            </span>
                                        @else
                                            @forelse($role->permissions ?? [] as $permSlug)
                                                <span class="badge bg-light text-dark border py-1 px-2 rounded font-monospace" style="font-size: 0.72rem;">
                                                    <i class="fa-solid fa-check text-success me-1"></i>{{ $permSlug }}
                                                </span>
                                            @empty
                                                <span class="text-muted small italic">No permissions assigned yet.</span>
                                            @endforelse
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top p-3 d-flex align-items-center justify-content-between">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold edit-role-btn"
                                        data-id="{{ $role->id }}"
                                        data-name="{{ $role->name }}"
                                        data-description="{{ $role->description }}"
                                        data-issystem="{{ $role->is_system ? '1' : '0' }}"
                                        data-permissions='@json($role->permissions ?? [])'>
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit Permissions
                                </button>

                                @if(!$role->is_system && $role->users_count === 0)
                                    <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}" onsubmit="return confirm('Delete this custom role?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">
                                            <i class="fa-solid fa-trash-can me-1"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: ADD STAFF MEMBER                                                   -->
<!-- ========================================================================= -->
<div class="modal fade" id="createStaffModal" tabindex="-1" aria-labelledby="createStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white text-primary p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fa-solid fa-user-plus fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="createStaffModalLabel">Add New Staff Member</h5>
                        <small class="text-white-50">Create staff credentials for admin panel login</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.staff.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Ramesh Kumar" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small">
                                User ID / Username <span class="text-danger">*</span>
                                <span class="text-muted fw-normal">(Unique handle for login)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted font-monospace">@</span>
                                <input type="text" name="username" class="form-control rounded-end-3 font-monospace" placeholder="e.g. ramesh_ops" required pattern="[A-Za-z0-9_]{3,30}" title="Letters, numbers and underscores only (3-30 chars)">
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">Staff can type this User ID in the login screen directly.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Mobile Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control rounded-3" placeholder="10-digit mobile" required pattern="[0-9]{10}" maxlength="10">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Email Address <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="email" name="email" class="form-control rounded-3" placeholder="staff@example.com">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small">Login Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="staffCreatePassword" class="form-control rounded-start-3" placeholder="Min. 6 characters" minlength="6" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('staffCreatePassword', this)">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small">Assigned Role & Permissions <span class="text-danger">*</span></label>
                            <select name="admin_role_id" class="form-select rounded-3" required>
                                <option value="" disabled selected>-- Select a Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }} ({{ $role->is_system ? 'System Default' : count($role->permissions ?? []) . ' perms' }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 0.72rem;">The selected role dictates which sections and controls this user can access in the admin panel.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-check me-1"></i> Create Staff Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: EDIT STAFF MEMBER                                                  -->
<!-- ========================================================================= -->
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white text-dark p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fa-solid fa-user-pen fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="editStaffModalLabel">Edit Staff Credentials</h5>
                        <small class="text-white-50">Update details, reset password, or change assigned role</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editStaffForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editStaffName" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small">User ID / Username <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted font-monospace">@</span>
                                <input type="text" name="username" id="editStaffUsername" class="form-control rounded-end-3 font-monospace" required pattern="[A-Za-z0-9_]{3,30}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Mobile Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="editStaffPhone" class="form-control rounded-3" required pattern="[0-9]{10}" maxlength="10">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Email Address</label>
                            <input type="email" name="email" id="editStaffEmail" class="form-control rounded-3">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small">
                                Reset Password 
                                <span class="text-muted fw-normal">(Leave blank to keep existing password)</span>
                            </label>
                            <div class="input-group">
                                <input type="password" name="password" id="editStaffPassword" class="form-control rounded-start-3" placeholder="Enter new password (optional)" minlength="6">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('editStaffPassword', this)">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6" id="editStaffRoleContainer">
                            <label class="form-label fw-bold text-dark small">Assigned Role</label>
                            <select name="admin_role_id" id="editStaffRole" class="form-select rounded-3">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Account Status</label>
                            <select name="status" id="editStaffStatus" class="form-select rounded-3">
                                <option value="active">Active (Access Allowed)</option>
                                <option value="suspended">Suspended (Access Blocked)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-save me-1"></i> Update Staff Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: CREATE CUSTOM ROLE                                                 -->
<!-- ========================================================================= -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden d-flex flex-column" style="max-height: 90vh;">
            <div class="modal-header bg-primary text-white p-4 flex-shrink-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white text-primary p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fa-solid fa-shield-plus fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="createRoleModalLabel">Create Custom Administrative Role</h5>
                        <small class="text-white-50">Specify role details and select which admin features this role can access</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.roles.store') }}" class="d-flex flex-column flex-grow-1 overflow-hidden m-0">
                @csrf
                <div class="modal-body p-4 flex-grow-1" style="max-height: calc(85vh - 160px); overflow-y: auto;">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Night Shift Operator" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Role Description <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="text" name="description" class="form-control rounded-3" placeholder="Brief summary of duties and permissions">
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border mb-4">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list-check text-primary me-2"></i>Select Access Permissions</h6>
                            <small class="text-muted">Unchecked options will be completely hidden from staff navigation and blocked at the route level.</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" onclick="toggleAllCheckboxes('#createRoleModal', true)">
                                <i class="fa-solid fa-check-double me-1"></i> Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" onclick="toggleAllCheckboxes('#createRoleModal', false)">
                                <i class="fa-solid fa-xmark me-1"></i> Clear All
                            </button>
                        </div>
                    </div>

                    <!-- Grouped Permission Cards -->
                    <div class="row g-4">
                        @foreach($allPermissions as $categoryName => $perms)
                            <div class="col-md-6">
                                <div class="border rounded-4 p-3 bg-white h-100 shadow-xs">
                                    <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                                        <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">
                                            <i class="fa-solid fa-folder-tree text-info me-1"></i> {{ $categoryName }}
                                        </span>
                                        <button type="button" class="btn btn-link btn-sm text-primary p-0 text-decoration-none small fw-semibold" onclick="toggleCategoryCheckboxes(this, true)">
                                            Select Group
                                        </button>
                                    </div>
                                    <div class="d-flex flex-column gap-2 category-perms-group">
                                        @foreach($perms as $permKey => $permLabel)
                                            <label class="d-flex align-items-start gap-2 p-2 rounded border bg-light-subtle cursor-pointer hover-shadow transition">
                                                <input class="form-check-input mt-1 perm-checkbox" type="checkbox" name="permissions[]" value="{{ $permKey }}">
                                                <div class="small">
                                                    <div class="fw-bold text-dark">{{ $permLabel }}</div>
                                                    <code class="text-muted" style="font-size: 0.7rem;">{{ $permKey }}</code>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer bg-light p-3 flex-shrink-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-check me-1"></i> Save Role & Permissions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: EDIT ROLE & PERMISSIONS                                            -->
<!-- ========================================================================= -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden d-flex flex-column" style="max-height: 90vh;">
            <div class="modal-header bg-dark text-white p-4 flex-shrink-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white text-dark p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fa-solid fa-shield-halved fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="editRoleModalLabel">Edit Role & Permissions</h5>
                        <small class="text-white-50">Modify assigned permissions for this administrative role</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRoleForm" method="POST" action="" class="d-flex flex-column flex-grow-1 overflow-hidden m-0">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 flex-grow-1" style="max-height: calc(85vh - 160px); overflow-y: auto;">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editRoleName" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Role Description</label>
                            <input type="text" name="description" id="editRoleDescription" class="form-control rounded-3">
                        </div>
                    </div>

                    <div id="systemRoleNotice" class="alert alert-warning border-0 rounded-3 d-none mb-4">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        <strong>System Role Notice:</strong> This is a default system role. Super Administrator inherently has unrestricted access to all endpoints.
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border mb-4">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list-check text-primary me-2"></i>Assigned Permissions</h6>
                            <small class="text-muted">Toggle checkboxes to grant or revoke specific admin panel capabilities.</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" onclick="toggleAllCheckboxes('#editRoleModal', true)">
                                <i class="fa-solid fa-check-double me-1"></i> Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" onclick="toggleAllCheckboxes('#editRoleModal', false)">
                                <i class="fa-solid fa-xmark me-1"></i> Clear All
                            </button>
                        </div>
                    </div>

                    <!-- Grouped Permission Cards -->
                    <div class="row g-4">
                        @foreach($allPermissions as $categoryName => $perms)
                            <div class="col-md-6">
                                <div class="border rounded-4 p-3 bg-white h-100 shadow-xs">
                                    <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                                        <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">
                                            <i class="fa-solid fa-folder-tree text-info me-1"></i> {{ $categoryName }}
                                        </span>
                                        <button type="button" class="btn btn-link btn-sm text-primary p-0 text-decoration-none small fw-semibold" onclick="toggleCategoryCheckboxes(this, true)">
                                            Select Group
                                        </button>
                                    </div>
                                    <div class="d-flex flex-column gap-2 category-perms-group">
                                        @foreach($perms as $permKey => $permLabel)
                                            <label class="d-flex align-items-start gap-2 p-2 rounded border bg-light-subtle cursor-pointer hover-shadow transition">
                                                <input class="form-check-input mt-1 edit-perm-checkbox" type="checkbox" name="permissions[]" value="{{ $permKey }}" id="edit_perm_{{ $permKey }}">
                                                <div class="small">
                                                    <div class="fw-bold text-dark">{{ $permLabel }}</div>
                                                    <code class="text-muted" style="font-size: 0.7rem;">{{ $permKey }}</code>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer bg-light p-3 flex-shrink-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-save me-1"></i> Update Role & Permissions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Password visibility toggle
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Toggle all checkboxes in a container
    function toggleAllCheckboxes(modalSelector, state) {
        const modal = document.querySelector(modalSelector);
        if (!modal) return;
        const checkboxes = modal.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
        checkboxes.forEach(cb => {
            cb.checked = state;
        });
    }

    // Toggle group checkboxes
    function toggleCategoryCheckboxes(btn, defaultState) {
        const groupContainer = btn.closest('.border').querySelector('.category-perms-group');
        if (!groupContainer) return;
        const checkboxes = groupContainer.querySelectorAll('input[type="checkbox"]');
        
        // If all are checked, uncheck all. Otherwise check all.
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
        btn.textContent = allChecked ? 'Select Group' : 'Deselect Group';
    }

    // Populate Edit Staff Modal
    document.querySelectorAll('.edit-staff-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const username = this.dataset.username;
            const phone = this.dataset.phone;
            const email = this.dataset.email;
            const role = this.dataset.role;
            const status = this.dataset.status;
            const isSuper = this.dataset.issuper === '1';

            document.getElementById('editStaffForm').action = `/admin/staff/${id}`;
            document.getElementById('editStaffName').value = name || '';
            document.getElementById('editStaffUsername').value = username || '';
            document.getElementById('editStaffPhone').value = phone || '';
            document.getElementById('editStaffEmail').value = email || '';
            document.getElementById('editStaffPassword').value = '';
            document.getElementById('editStaffStatus').value = status || 'active';

            const roleSelect = document.getElementById('editStaffRole');
            if (roleSelect) {
                roleSelect.value = role || '';
            }

            const modal = new bootstrap.Modal(document.getElementById('editStaffModal'));
            modal.show();
        });
    });

    // Populate Edit Role Modal
    document.querySelectorAll('.edit-role-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const description = this.dataset.description;
            const isSystem = this.dataset.issystem === '1';
            const permissions = JSON.parse(this.dataset.permissions || '[]');

            document.getElementById('editRoleForm').action = `/admin/roles/${id}`;
            document.getElementById('editRoleName').value = name || '';
            document.getElementById('editRoleDescription').value = description || '';

            const systemNotice = document.getElementById('systemRoleNotice');
            if (isSystem) {
                systemNotice.classList.remove('d-none');
            } else {
                systemNotice.classList.add('d-none');
            }

            // Uncheck all first
            document.querySelectorAll('#editRoleModal .edit-perm-checkbox').forEach(cb => {
                cb.checked = false;
            });

            // Check permissions
            permissions.forEach(perm => {
                const cb = document.getElementById('edit_perm_' + perm);
                if (cb) {
                    cb.checked = true;
                }
            });

            const modal = new bootstrap.Modal(document.getElementById('editRoleModal'));
            modal.show();
        });
    });
</script>
@endsection
