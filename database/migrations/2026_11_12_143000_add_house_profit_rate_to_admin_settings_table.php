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
        if (Schema::hasTable('admin_settings') && ! Schema::hasColumn('admin_settings', 'house_profit_rate')) {
            Schema::table('admin_settings', function (Blueprint $table) {
                $table->decimal('house_profit_rate', 5, 2)->default(78.00)->after('auto_result_mode');
            });
        }

        // Update existing admin_settings rows with the 78% default and active house profit modes
        if (Schema::hasTable('admin_settings')) {
            DB::table('admin_settings')->update([
                'house_profit_rate' => 78.00,
                'mines_rigged_rate' => 78.00,
                'mines_control_mode' => 'house_trap',
                'vortex_rigged_rate' => 78.00,
                'vortex_control_mode' => 'house_trap',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('admin_settings') && Schema::hasColumn('admin_settings', 'house_profit_rate')) {
            Schema::table('admin_settings', function (Blueprint $table) {
                $table->dropColumn('house_profit_rate');
            });
        }
    }
};
