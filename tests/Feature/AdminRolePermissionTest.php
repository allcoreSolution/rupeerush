<?php

namespace Tests\Feature;

use App\Models\AdminRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminRolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected AdminRole $superAdminRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdminRole = AdminRole::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Administrator',
                'description' => 'Unrestricted master access',
                'permissions' => array_keys(AdminRole::permissionCatalog()),
                'is_system' => true,
            ]
        );

        $this->superAdmin = User::create([
            'name' => 'Root Admin',
            'username' => 'root_admin',
            'phone' => '9999999999',
            'email' => 'root@example.com',
            'password' => Hash::make('admin1234'),
            'role' => 'admin',
            'admin_role_id' => $this->superAdminRole->id,
            'status' => 'active',
            'balance' => 0.00,
        ]);
    }

    /**
     * Test super admin can access roles and staff management page.
     */
    public function test_super_admin_can_access_roles_and_permissions_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.roles.index'));

        $response->assertStatus(200);
        $response->assertSee('Staff & Access Control (RBAC)', false);
        $response->assertSee('Create New Role');
        $response->assertSee('Add Staff Member');
    }

    /**
     * Test super admin can create a custom role with granular permissions.
     */
    public function test_super_admin_can_create_custom_role_with_specific_permissions(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.roles.store'), [
            'name' => 'Payment Operator',
            'description' => 'Can only manage deposits and withdrawals',
            'permissions' => ['deposits_view', 'deposits_manage', 'withdrawals_view'],
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('admin_roles', [
            'name' => 'Payment Operator',
            'slug' => 'payment-operator',
            'is_system' => false,
        ]);

        $role = AdminRole::where('slug', 'payment-operator')->first();
        $this->assertNotNull($role);
        $this->assertEquals(['deposits_view', 'deposits_manage', 'withdrawals_view'], $role->permissions);
    }

    /**
     * Test super admin can provision a staff account with custom User ID and password.
     */
    public function test_super_admin_can_create_staff_account_with_custom_username_and_password(): void
    {
        $role = AdminRole::create([
            'name' => 'Game Moderator',
            'slug' => 'game-moderator',
            'description' => 'Moderator role',
            'permissions' => ['game_aviator', 'winners_aviator'],
            'is_system' => false,
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('admin.staff.store'), [
            'name' => 'Rohan Staff',
            'username' => 'rohan_mod_01',
            'phone' => '9876543200',
            'email' => 'rohan@example.com',
            'password' => 'moderatorPass123',
            'admin_role_id' => $role->id,
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Rohan Staff',
            'username' => 'rohan_mod_01',
            'phone' => '9876543200',
            'role' => 'admin',
            'admin_role_id' => $role->id,
            'status' => 'active',
        ]);
    }

    /**
     * Test staff member can log into the admin hub using their custom User ID (username).
     */
    public function test_staff_can_login_using_custom_user_id_username(): void
    {
        $role = AdminRole::create([
            'name' => 'Cashier',
            'slug' => 'cashier',
            'permissions' => ['deposits_view'],
            'is_system' => false,
        ]);

        User::create([
            'name' => 'Pooja Cashier',
            'username' => 'pooja_cashier',
            'phone' => '9876543201',
            'email' => 'pooja@example.com',
            'password' => Hash::make('cashierSecret123'),
            'role' => 'admin',
            'admin_role_id' => $role->id,
            'status' => 'active',
            'balance' => 0.00,
        ]);

        $response = $this->post(route('admin.login.post'), [
            'login' => 'pooja_cashier',
            'password' => 'cashierSecret123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('pooja_cashier', auth()->user()->username);
    }

    /**
     * Test staff with specific permission can access that feature.
     */
    public function test_staff_with_permission_can_access_authorized_route(): void
    {
        $role = AdminRole::create([
            'name' => 'Deposit Staff',
            'slug' => 'deposit-staff',
            'permissions' => ['dashboard', 'deposits_view'],
            'is_system' => false,
        ]);

        $staff = User::create([
            'name' => 'Aman Deposit',
            'username' => 'aman_deposit',
            'phone' => '9876543202',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'admin_role_id' => $role->id,
            'status' => 'active',
            'balance' => 0.00,
        ]);

        // Allowed to view deposits
        $response = $this->actingAs($staff)->get(route('admin.payments.deposits'));
        $response->assertStatus(200);
    }

    /**
     * Test staff without permission is blocked with 403 Forbidden.
     */
    public function test_staff_without_permission_is_blocked_with_403_forbidden(): void
    {
        $role = AdminRole::create([
            'name' => 'Deposit Staff Only',
            'slug' => 'deposit-staff-only',
            'permissions' => ['dashboard', 'deposits_view'],
            'is_system' => false,
        ]);

        $staff = User::create([
            'name' => 'Sunil Deposit Only',
            'username' => 'sunil_dep',
            'phone' => '9876543203',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'admin_role_id' => $role->id,
            'status' => 'active',
            'balance' => 0.00,
        ]);

        // Forbidden from Aviator game control
        $response = $this->actingAs($staff)->get(route('admin.aviator.index'));
        $response->assertStatus(403);

        // Forbidden from Roles & Permissions management
        $responseRoles = $this->actingAs($staff)->get(route('admin.roles.index'));
        $responseRoles->assertStatus(403);
    }

    /**
     * Test super admin can update custom role permissions.
     */
    public function test_super_admin_can_update_role_permissions(): void
    {
        $role = AdminRole::create([
            'name' => 'Support Agent Custom',
            'slug' => 'support-agent-custom',
            'permissions' => ['support_settings'],
            'is_system' => false,
        ]);

        $response = $this->actingAs($this->superAdmin)->put(route('admin.roles.update', $role->id), [
            'name' => 'Senior Support Agent',
            'description' => 'Now handles support and views users',
            'permissions' => ['support_settings', 'users_view'],
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $role->refresh();
        $this->assertEquals('Senior Support Agent', $role->name);
        $this->assertEquals(['support_settings', 'users_view'], $role->permissions);
    }

    /**
     * Test toggling staff status and blocking suspended staff from logging in.
     */
    public function test_toggle_staff_status_and_suspended_staff_cannot_login(): void
    {
        $role = AdminRole::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
            'permissions' => ['dashboard'],
            'is_system' => false,
        ]);

        $staff = User::create([
            'name' => 'Dev Suspended',
            'username' => 'dev_suspended',
            'phone' => '9876543204',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'admin_role_id' => $role->id,
            'status' => 'active',
            'balance' => 0.00,
        ]);

        // Toggle status to suspended
        $this->actingAs($this->superAdmin)->post(route('admin.staff.toggle-status', $staff->id));
        $staff->refresh();
        $this->assertEquals('suspended', $staff->status);

        // Suspended staff attempts login
        $this->post(route('admin.logout'));
        $response = $this->post(route('admin.login.post'), [
            'login' => 'dev_suspended',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['login']);
        $this->assertGuest();
    }

    /**
     * Test system role cannot be deleted.
     */
    public function test_system_role_cannot_be_deleted(): void
    {
        $response = $this->actingAs($this->superAdmin)->delete(route('admin.roles.destroy', $this->superAdminRole->id));

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('admin_roles', ['id' => $this->superAdminRole->id]);
    }
}
