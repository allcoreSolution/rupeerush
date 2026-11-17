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
        Schema::create('vortex_games', function (Blueprint $table) {
            $table->id();
            $table->string('game_id', 30)->unique()->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('bet_amount', 10, 2);
            $table->unsignedTinyInteger('current_ring')->default(1)->index(); // 1 = Inner (Water), 2 = Middle (Earth), 3 = Outer (Fire)
            $table->decimal('current_multiplier', 10, 2)->default(1.00);
            $table->decimal('payout_amount', 10, 2)->default(0.00);
            $table->decimal('win_amount', 10, 2)->default(0.00);
            $table->decimal('commission_amount', 10, 2)->default(0.00);
            $table->boolean('part_cashed_out')->default(false);
            $table->decimal('part_cashout_amount', 10, 2)->default(0.00);
            $table->decimal('remaining_bet_amount', 10, 2)->default(0.00);
            $table->json('spin_history')->nullable();
            $table->enum('status', ['active', 'cashed_out', 'busted'])->default('active')->index();
            $table->string('server_seed', 64)->nullable();
            $table->string('seed_hash', 64)->nullable();
            $table->dateTime('cashed_out_at')->nullable();
            $table->timestamps();
        });

        Schema::table('admin_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('admin_settings', 'vortex_min_bet')) {
                $table->decimal('vortex_min_bet', 10, 2)->default(1.00);
            }
            if (! Schema::hasColumn('admin_settings', 'vortex_max_bet')) {
                $table->decimal('vortex_max_bet', 10, 2)->default(25000.00);
            }
            if (! Schema::hasColumn('admin_settings', 'vortex_house_edge')) {
                $table->decimal('vortex_house_edge', 5, 2)->default(3.50);
            }
            if (! Schema::hasColumn('admin_settings', 'vortex_control_mode')) {
                $table->string('vortex_control_mode', 30)->default('fair');
            }
            if (! Schema::hasColumn('admin_settings', 'vortex_rigged_rate')) {
                $table->decimal('vortex_rigged_rate', 5, 2)->default(0.00);
            }
            if (! Schema::hasColumn('admin_settings', 'vortex_max_multiplier_cap')) {
                $table->decimal('vortex_max_multiplier_cap', 10, 2)->default(0.00);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vortex_games');

        Schema::table('admin_settings', function (Blueprint $table) {
            $cols = [
                'vortex_min_bet',
                'vortex_max_bet',
                'vortex_house_edge',
                'vortex_control_mode',
                'vortex_rigged_rate',
                'vortex_max_multiplier_cap',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('admin_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
