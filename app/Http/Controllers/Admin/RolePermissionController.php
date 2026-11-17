<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RolePermissionController extends Controller
{
    /**
     * Display Roles, Permissions, and Sub-Admin/Staff management.
     */
    public function index(): View
    {
        $roles = AdminRole::withCount('users')->orderBy('id', 'asc')->get();
        $staff = User::where('role', 'admin')
            ->with('adminRole')
            ->orderBy('id', 'desc')
            ->paginate(15);
        $allPermissions = AdminRole::permissionCatalog();
        $catalog = $allPermissions;

        return view('admin.roles.index', compact('roles', 'staff', 'allPermissions', 'catalog'));
    }

    /**
     * Store a newly created role.
     */
    public function storeRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['string'],
        ], [
            'name.required' => 'Role name is required.',
            'permissions.required' => 'Please select at least one permission for this role.',
            'permissions.min' => 'Please select at least one permission for this role.',
        ]);

        $slug = AdminRole::generateSlug($validated['name']);

        AdminRole::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'permissions' => array_values(array_unique($validated['permissions'])),
            'is_system' => false,
        ]);

        return redirect()->route('admin.roles.index')->with('success', "Role '{$validated['name']}' created successfully with customized permissions.");
    }

    /**
     * Update an existing role.
     */
    public function updateRole(Request $request, int $id): RedirectResponse
    {
        /** @var AdminRole $role */
        $role = AdminRole::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['string'],
        ], [
            'name.required' => 'Role name is required.',
            'permissions.required' => 'Please select at least one permission for this role.',
        ]);

        if (! $role->is_system) {
            $role->name = $validated['name'];
            $role->slug = AdminRole::generateSlug($validated['name'], $role->id);
        }

        $role->description = $validated['description'] ?? null;
        $role->permissions = array_values(array_unique($validated['permissions']));
        $role->save();

        return redirect()->route('admin.roles.index')->with('success', "Role '{$role->name}' updated successfully.");
    }

    /**
     * Delete a custom role.
     */
    public function destroyRole(int $id): RedirectResponse
    {
        /** @var AdminRole $role */
        $role = AdminRole::findOrFail($id);

        if ($role->is_system || $role->slug === 'super-admin') {
            return redirect()->route('admin.roles.index')->with('error', 'System roles cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', "Cannot delete role '{$role->name}' because {$role->users()->count()} staff member(s) are currently assigned to it. Reassign them first.");
        }

        $name = $role->name;
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', "Role '{$name}' deleted successfully.");
    }

    /**
     * Create a new Sub-Admin / Staff member with User ID and Password.
     */
    public function storeStaff(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'alpha_dash', 'min:3', 'max:30', 'unique:users,username'],
            'phone' => ['required', 'digits:10', 'unique:users,phone'],
            'email' => ['nullable', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'admin_role_id' => ['required', 'exists:admin_roles,id'],
        ], [
            'username.required' => 'Please enter a unique Staff User ID.',
            'username.alpha_dash' => 'User ID may only contain letters, numbers, dashes and underscores.',
            'phone.required' => 'Mobile number is required for staff verification.',
            'password.required' => 'Staff password is required.',
            'admin_role_id.required' => 'Please select an administrative role.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => strtolower(trim($validated['username'])),
            'phone' => trim($validated['phone']),
            'email' => ! empty($validated['email']) ? strtolower(trim($validated['email'])) : null,
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'admin_role_id' => $validated['admin_role_id'],
            'balance' => 0.00,
            'status' => 'active',
        ]);

        return redirect()->route('admin.roles.index')->with('success', "Staff account for '{$user->name}' (User ID: {$user->username}) created successfully.");
    }

    /**
     * Update an existing staff member.
     */
    public function updateStaff(Request $request, int $id): RedirectResponse
    {
        /** @var User $staffUser */
        $staffUser = User::where('role', 'admin')->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'alpha_dash', 'min:3', 'max:30', Rule::unique('users', 'username')->ignore($staffUser->id)],
            'phone' => ['required', 'digits:10', Rule::unique('users', 'phone')->ignore($staffUser->id)],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('users', 'email')->ignore($staffUser->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'admin_role_id' => ['required', 'exists:admin_roles,id'],
            'status' => ['required', 'in:active,suspended'],
        ]);

        $staffUser->name = $validated['name'];
        $staffUser->username = strtolower(trim($validated['username']));
        $staffUser->phone = trim($validated['phone']);
        $staffUser->email = ! empty($validated['email']) ? strtolower(trim($validated['email'])) : null;
        $staffUser->admin_role_id = $validated['admin_role_id'];
        $staffUser->status = $validated['status'];

        if (! empty($validated['password'])) {
            $staffUser->password = Hash::make($validated['password']);
        }

        $staffUser->save();

        return redirect()->route('admin.roles.index')->with('success', "Staff account '{$staffUser->name}' updated successfully.");
    }

    /**
     * Toggle active/suspended status of a staff member.
     */
    public function toggleStaffStatus(int $id): RedirectResponse
    {
        if ($id === (int) Auth::id()) {
            return redirect()->route('admin.roles.index')->with('error', 'You cannot suspend your own administrative account.');
        }

        /** @var User $staffUser */
        $staffUser = User::where('role', 'admin')->findOrFail($id);

        $staffUser->status = $staffUser->status === 'active' ? 'suspended' : 'active';
        $staffUser->save();

        $statusText = $staffUser->status === 'active' ? 'activated' : 'suspended';

        return redirect()->route('admin.roles.index')->with('success', "Staff account '{$staffUser->name}' has been {$statusText}.");
    }

    /**
     * Delete a staff member.
     */
    public function destroyStaff(int $id): RedirectResponse
    {
        if ($id === (int) Auth::id()) {
            return redirect()->route('admin.roles.index')->with('error', 'You cannot delete your own administrative account.');
        }

        /** @var User $staffUser */
        $staffUser = User::where('role', 'admin')->findOrFail($id);

        $name = $staffUser->name;
        $staffUser->delete();

        return redirect()->route('admin.roles.index')->with('success', "Staff account '{$name}' deleted successfully.");
    }
}
