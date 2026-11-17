<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create admin_roles table
        if (! Schema::hasTable('admin_roles')) {
            Schema::create('admin_roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->json('permissions')->nullable();
                $table->boolean('is_system')->default(false);
                $table->timestamps();
            });
        }

        // 2. Add username and admin_role_id to users table
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('name');
            }
            if (! Schema::hasColumn('users', 'admin_role_id')) {
                $table->foreignId('admin_role_id')
                    ->nullable()
                    ->after('role')
                    ->constrained('admin_roles')
                    ->nullOnDelete();
            }
        });

        // 3. Seed initial default roles
        $allPermissionKeys = [
            'dashboard',
            'users_view',
            'users_manage',
            'deposits_view',
            'deposits_manage',
            'withdrawals_view',
            'withdrawals_manage',
            'daily_transactions',
            'payment_settings',
            'bets_view',
            'game_lottery',
            'game_wingo',
            'game_andar_bahar',
            'game_aviator',
            'game_mines',
            'game_vortex',
            'game_teen_patti',
            'winners_lottery',
            'winners_wingo',
            'winners_andar_bahar',
            'winners_aviator',
            'winners_mines',
            'winners_vortex',
            'winners_teen_patti',
            'support_settings',
            'roles_management',
        ];

        $superAdminRoleId = DB::table('admin_roles')->insertGetId([
            'name' => 'Super Administrator',
            'slug' => 'super-admin',
            'description' => 'Full unrestricted access to all admin panel options, settings, and staff management.',
            'permissions' => json_encode($allPermissionKeys),
            'is_system' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('admin_roles')->insert([
            [
                'name' => 'Finance & Cashier Manager',
                'slug' => 'finance-manager',
                'description' => 'Manage user deposits, withdrawals, daily transactions ledger, and payment QR settings.',
                'permissions' => json_encode([
                    'dashboard',
                    'deposits_view',
                    'deposits_manage',
                    'withdrawals_view',
                    'withdrawals_manage',
                    'daily_transactions',
                    'payment_settings',
                ]),
                'is_system' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Game Operations Controller',
                'slug' => 'game-controller',
                'description' => 'Full control over game results, live manual manipulation, and winner report lists.',
                'permissions' => json_encode([
                    'dashboard',
                    'game_lottery',
                    'game_wingo',
                    'game_andar_bahar',
                    'game_aviator',
                    'game_mines',
                    'game_vortex',
                    'game_teen_patti',
                    'winners_lottery',
                    'winners_wingo',
                    'winners_andar_bahar',
                    'winners_aviator',
                    'winners_mines',
                    'winners_vortex',
                    'winners_teen_patti',
                ]),
                'is_system' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Support & Moderation Agent',
                'slug' => 'support-agent',
                'description' => 'View players list, bets monitoring, and manage website support contacts.',
                'permissions' => json_encode([
                    'dashboard',
                    'users_view',
                    'bets_view',
                    'support_settings',
                ]),
                'is_system' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 4. Attach super-admin role to existing administrator users
        DB::table('users')
            ->where('role', 'admin')
            ->whereNull('admin_role_id')
            ->update(['admin_role_id' => $superAdminRoleId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'admin_role_id')) {
                $table->dropForeign(['admin_role_id']);
                $table->dropColumn('admin_role_id');
            }
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
        });

        Schema::dropIfExists('admin_roles');
    }
};
