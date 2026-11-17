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
        if (Schema::hasTable('admin_settings') && ! Schema::hasColumn('admin_settings', 'win_commission_percent')) {
            Schema::table('admin_settings', function (Blueprint $table) {
                $table->decimal('win_commission_percent', 5, 2)->default(10.00)->after('win_multiplier');
            });
        }

        if (Schema::hasTable('wingo_bets') && ! Schema::hasColumn('wingo_bets', 'commission_amount')) {
            Schema::table('wingo_bets', function (Blueprint $table) {
                $table->decimal('commission_amount', 10, 2)->default(0.00)->after('win_multiplier');
            });
        }

        if (Schema::hasTable('andar_bahar_bets') && ! Schema::hasColumn('andar_bahar_bets', 'commission_amount')) {
            Schema::table('andar_bahar_bets', function (Blueprint $table) {
                $table->decimal('commission_amount', 10, 2)->default(0.00)->after('win_multiplier');
            });
        }

        if (Schema::hasTable('bets') && ! Schema::hasColumn('bets', 'commission_amount')) {
            Schema::table('bets', function (Blueprint $table) {
                $table->decimal('commission_amount', 10, 2)->default(0.00)->after('win_multiplier');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('admin_settings') && Schema::hasColumn('admin_settings', 'win_commission_percent')) {
            Schema::table('admin_settings', function (Blueprint $table) {
                $table->dropColumn('win_commission_percent');
            });
        }

        if (Schema::hasTable('wingo_bets') && Schema::hasColumn('wingo_bets', 'commission_amount')) {
            Schema::table('wingo_bets', function (Blueprint $table) {
                $table->dropColumn('commission_amount');
            });
        }

        if (Schema::hasTable('andar_bahar_bets') && Schema::hasColumn('andar_bahar_bets', 'commission_amount')) {
            Schema::table('andar_bahar_bets', function (Blueprint $table) {
                $table->dropColumn('commission_amount');
            });
        }

        if (Schema::hasTable('bets') && Schema::hasColumn('bets', 'commission_amount')) {
            Schema::table('bets', function (Blueprint $table) {
                $table->dropColumn('commission_amount');
            });
        }
    }
};
