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
            $table->decimal('roulette_red_multiplier', 6, 2)->default(5.00)->after('roulette_manual_number');
            $table->decimal('roulette_black_multiplier', 6, 2)->default(7.00)->after('roulette_red_multiplier');
            $table->decimal('roulette_red_weight', 5, 2)->default(70.00)->after('roulette_black_multiplier');
            $table->decimal('roulette_black_weight', 5, 2)->default(29.00)->after('roulette_red_weight');
            $table->decimal('roulette_green_weight', 5, 2)->default(1.00)->after('roulette_black_weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            $table->dropColumn([
                'roulette_red_multiplier',
                'roulette_black_multiplier',
                'roulette_red_weight',
                'roulette_black_weight',
                'roulette_green_weight',
            ]);
        });
    }
};
