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
        if (Schema::hasTable('teen_patti_bets') && ! Schema::hasColumn('teen_patti_bets', 'commission_amount')) {
            Schema::table('teen_patti_bets', function (Blueprint $table) {
                $table->decimal('commission_amount', 12, 2)->default(0.00)->after('payout_amount');
            });
        }

        if (Schema::hasTable('dragon_tiger_bets') && ! Schema::hasColumn('dragon_tiger_bets', 'commission_amount')) {
            Schema::table('dragon_tiger_bets', function (Blueprint $table) {
                $table->decimal('commission_amount', 12, 2)->default(0.00)->after('payout_amount');
            });
        }

        if (Schema::hasTable('roulette_bets') && ! Schema::hasColumn('roulette_bets', 'commission_amount')) {
            Schema::table('roulette_bets', function (Blueprint $table) {
                $table->decimal('commission_amount', 12, 2)->default(0.00)->after('payout_amount');
            });
        }

        if (Schema::hasTable('horse_race_bets') && ! Schema::hasColumn('horse_race_bets', 'commission_amount')) {
            Schema::table('horse_race_bets', function (Blueprint $table) {
                $table->decimal('commission_amount', 12, 2)->default(0.00)->after('payout_amount');
            });
        }

        if (Schema::hasTable('sports_bets') && ! Schema::hasColumn('sports_bets', 'commission_amount')) {
            Schema::table('sports_bets', function (Blueprint $table) {
                $table->decimal('commission_amount', 12, 2)->default(0.00)->after('win_amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('teen_patti_bets') && Schema::hasColumn('teen_patti_bets', 'commission_amount')) {
            Schema::table('teen_patti_bets', function (Blueprint $table) {
                $table->dropColumn('commission_amount');
            });
        }

        if (Schema::hasTable('dragon_tiger_bets') && Schema::hasColumn('dragon_tiger_bets', 'commission_amount')) {
            Schema::table('dragon_tiger_bets', function (Blueprint $table) {
                $table->dropColumn('commission_amount');
            });
        }

        if (Schema::hasTable('roulette_bets') && Schema::hasColumn('roulette_bets', 'commission_amount')) {
            Schema::table('roulette_bets', function (Blueprint $table) {
                $table->dropColumn('commission_amount');
            });
        }

        if (Schema::hasTable('horse_race_bets') && Schema::hasColumn('horse_race_bets', 'commission_amount')) {
            Schema::table('horse_race_bets', function (Blueprint $table) {
                $table->dropColumn('commission_amount');
            });
        }

        if (Schema::hasTable('sports_bets') && Schema::hasColumn('sports_bets', 'commission_amount')) {
            Schema::table('sports_bets', function (Blueprint $table) {
                $table->dropColumn('commission_amount');
            });
        }
    }
};
