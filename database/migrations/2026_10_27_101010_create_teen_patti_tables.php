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
        // 1. Teen Patti Live Rounds Table
        Schema::create('teen_patti_rounds', function (Blueprint $table) {
            $table->id();
            $table->string('round_id', 32)->unique();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->enum('status', ['betting', 'dealing', 'completed', 'cancelled'])->default('betting');
            $table->json('player_a_cards')->nullable();
            $table->json('player_b_cards')->nullable();
            $table->string('player_a_rank_type', 32)->nullable();
            $table->string('player_a_rank_title', 64)->nullable();
            $table->string('player_b_rank_type', 32)->nullable();
            $table->string('player_b_rank_title', 64)->nullable();
            $table->string('winner', 16)->nullable(); // 'player_a', 'player_b', 'tie'
            $table->boolean('is_manual')->default(false);
            $table->string('manual_winner', 16)->nullable();
            $table->unsignedInteger('total_bets_count')->default(0);
            $table->decimal('total_bet_amount', 12, 2)->default(0.00);
            $table->decimal('total_payout_amount', 12, 2)->default(0.00);
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        // 2. Teen Patti User Bets Table
        Schema::create('teen_patti_bets', function (Blueprint $table) {
            $table->id();
            $table->string('bet_id', 32)->unique();
            $table->string('round_id', 32)->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('bet_type', 24); // 'player_a', 'player_b', 'pair_plus_a', 'pair_plus_b', 'tie'
            $table->decimal('bet_amount', 12, 2);
            $table->decimal('odds', 6, 2)->default(1.95);
            $table->decimal('payout_amount', 12, 2)->default(0.00);
            $table->decimal('net_profit', 12, 2)->default(0.00);
            $table->enum('status', ['pending', 'won', 'lost', 'refunded'])->default('pending');
            $table->string('hand_outcome', 128)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['round_id', 'status']);
        });

        // 3. Add Teen Patti settings to admin_settings table if missing
        Schema::table('admin_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('admin_settings', 'teen_patti_min_bet')) {
                $table->decimal('teen_patti_min_bet', 12, 2)->default(5.00)->after('vortex_max_multiplier_cap');
                $table->decimal('teen_patti_max_bet', 12, 2)->default(25000.00)->after('teen_patti_min_bet');
                $table->decimal('teen_patti_odds_player', 5, 2)->default(1.95)->after('teen_patti_max_bet');
                $table->decimal('teen_patti_odds_tie', 5, 2)->default(9.00)->after('teen_patti_odds_player');
                $table->unsignedInteger('teen_patti_round_duration')->default(25)->after('teen_patti_odds_tie');
                $table->string('teen_patti_control_mode', 24)->default('fair')->after('teen_patti_round_duration');
                $table->string('teen_patti_manual_winner', 16)->nullable()->after('teen_patti_control_mode');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teen_patti_bets');
        Schema::dropIfExists('teen_patti_rounds');

        Schema::table('admin_settings', function (Blueprint $table) {
            $cols = [
                'teen_patti_min_bet',
                'teen_patti_max_bet',
                'teen_patti_odds_player',
                'teen_patti_odds_tie',
                'teen_patti_round_duration',
                'teen_patti_control_mode',
                'teen_patti_manual_winner',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('admin_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
