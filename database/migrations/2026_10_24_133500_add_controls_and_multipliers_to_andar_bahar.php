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
        Schema::table('andar_bahar_rounds', function (Blueprint $table) {
            $table->boolean('is_manual')->default(false)->after('status');
            $table->enum('manual_winning_side', ['andar', 'bahar'])->nullable()->after('is_manual');
        });

        Schema::table('admin_settings', function (Blueprint $table) {
            $table->decimal('andar_multiplier', 5, 2)->default(1.95)->after('wingo_max_bet');
            $table->decimal('bahar_multiplier', 5, 2)->default(1.95)->after('andar_multiplier');
            $table->decimal('andar_bahar_min_bet', 10, 2)->default(10.00)->after('bahar_multiplier');
            $table->decimal('andar_bahar_max_bet', 10, 2)->default(50000.00)->after('andar_bahar_min_bet');
            $table->decimal('andar_bahar_range_1_5', 5, 2)->default(3.50)->after('andar_bahar_max_bet');
            $table->decimal('andar_bahar_range_6_10', 5, 2)->default(4.00)->after('andar_bahar_range_1_5');
            $table->decimal('andar_bahar_range_11_15', 5, 2)->default(5.00)->after('andar_bahar_range_6_10');
            $table->decimal('andar_bahar_range_16_25', 5, 2)->default(4.50)->after('andar_bahar_range_11_15');
            $table->decimal('andar_bahar_range_26_plus', 5, 2)->default(8.00)->after('andar_bahar_range_16_25');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('andar_bahar_rounds', function (Blueprint $table) {
            $table->dropColumn(['is_manual', 'manual_winning_side']);
        });

        Schema::table('admin_settings', function (Blueprint $table) {
            $table->dropColumn([
                'andar_multiplier',
                'bahar_multiplier',
                'andar_bahar_min_bet',
                'andar_bahar_max_bet',
                'andar_bahar_range_1_5',
                'andar_bahar_range_6_10',
                'andar_bahar_range_11_15',
                'andar_bahar_range_16_25',
                'andar_bahar_range_26_plus',
            ]);
        });
    }
};
