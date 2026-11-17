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
            if (! Schema::hasColumn('admin_settings', 'aviator_manual_multiplier')) {
                $table->decimal('aviator_manual_multiplier', 8, 2)->nullable()->after('aviator_max_bet');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            if (Schema::hasColumn('admin_settings', 'aviator_manual_multiplier')) {
                $table->dropColumn('aviator_manual_multiplier');
            }
        });
    }
};
