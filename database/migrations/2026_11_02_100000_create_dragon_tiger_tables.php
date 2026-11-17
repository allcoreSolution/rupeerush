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
        // 1. Dragon Tiger Live Rounds Table
        Schema::create('dragon_tiger_rounds', function (Blueprint $table) {
            $table->id();
            $table->string('round_id', 32)->unique();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->enum('status', ['betting', 'dealing', 'completed', 'cancelled'])->default('betting');
            $table->json('dragon_card')->nullable();
            $table->json('tiger_card')->nullable();
            $table->string('winner', 20)->nullable(); // 'dragon', 'tiger', 'tie', 'suited_tie'
            $table->boolean('is_manual')->default(false);
            $table->string('manual_winner', 20)->nullable();
            $table->unsignedInteger('total_bets_count')->default(0);
            $table->decimal('total_bet_amount', 12, 2)->default(0.00);
            $table->decimal('total_payout_amount', 12, 2)->default(0.00);
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        // 2. Dragon Tiger User Bets Table
        Schema::create('dragon_tiger_bets', function (Blueprint $table) {
            $table->id();
            $table->string('bet_id', 32)->unique();
            $table->string('round_id', 32)->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('bet_type', 32); // 'dragon', 'tiger', 'tie', 'suited_tie', side bets...
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

        // 3. Add Dragon Tiger settings to admin_settings table if missing
        Schema::table('admin_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('admin_settings', 'dragon_tiger_min_bet')) {
                $table->decimal('dragon_tiger_min_bet', 12, 2)->default(5.00)->nullable();
                $table->decimal('dragon_tiger_max_bet', 12, 2)->default(25000.00)->nullable();
                $table->decimal('dragon_tiger_odds_main', 5, 2)->default(1.95)->nullable();
                $table->decimal('dragon_tiger_odds_tie', 5, 2)->default(9.00)->nullable();
                $table->decimal('dragon_tiger_odds_suited_tie', 5, 2)->default(50.00)->nullable();
                $table->unsignedInteger('dragon_tiger_round_duration')->default(25)->nullable();
                $table->string('dragon_tiger_control_mode', 24)->default('fair')->nullable();
                $table->string('dragon_tiger_manual_winner', 20)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dragon_tiger_bets');
        Schema::dropIfExists('dragon_tiger_rounds');

        Schema::table('admin_settings', function (Blueprint $table) {
            $cols = [
                'dragon_tiger_min_bet',
                'dragon_tiger_max_bet',
                'dragon_tiger_odds_main',
                'dragon_tiger_odds_tie',
                'dragon_tiger_odds_suited_tie',
                'dragon_tiger_round_duration',
                'dragon_tiger_control_mode',
                'dragon_tiger_manual_winner',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('admin_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

