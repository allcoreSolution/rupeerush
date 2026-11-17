<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('admin_settings', 'mines_control_mode')) {
                $table->string('mines_control_mode', 30)->default('fair');
            }
            if (! Schema::hasColumn('admin_settings', 'mines_rigged_rate')) {
                $table->decimal('mines_rigged_rate', 5, 2)->default(0.00);
            }
            if (! Schema::hasColumn('admin_settings', 'mines_force_blast_step')) {
                $table->unsignedTinyInteger('mines_force_blast_step')->default(0);
            }
            if (! Schema::hasColumn('admin_settings', 'mines_max_win_multiplier')) {
                $table->decimal('mines_max_win_multiplier', 10, 2)->default(0.00);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            $cols = ['mines_control_mode', 'mines_rigged_rate', 'mines_force_blast_step', 'mines_max_win_multiplier'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('admin_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
