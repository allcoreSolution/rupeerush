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
        // 1. Horse Racing Rounds Table
        Schema::create('horse_race_rounds', function (Blueprint $table) {
            $table->id();
            $table->string('round_id', 32)->unique();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->enum('status', ['betting', 'racing', 'completed', 'cancelled'])->default('betting');
            $table->unsignedTinyInteger('winner_horse_id')->nullable(); // 1 to 6 (1st Place)
            $table->unsignedTinyInteger('second_horse_id')->nullable(); // 2nd Place
            $table->unsignedTinyInteger('third_horse_id')->nullable();  // 3rd Place
            $table->json('horses_data')->nullable(); // metadata of horses in race
            $table->unsignedInteger('race_distance')->default(1000); // 1000 meters sprint
            $table->string('seed_hash', 64)->nullable(); // SHA-256 provably fair
            $table->string('server_seed', 64)->nullable();
            $table->boolean('is_manual')->default(false);
            $table->unsignedTinyInteger('manual_winner_id')->nullable();
            $table->unsignedInteger('total_bets_count')->default(0);
            $table->decimal('total_bet_amount', 12, 2)->default(0.00);
            $table->decimal('total_payout_amount', 12, 2)->default(0.00);
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        // 2. Horse Racing User Bets Table
        Schema::create('horse_race_bets', function (Blueprint $table) {
            $table->id();
            $table->string('bet_id', 32)->unique();
            $table->string('round_id', 32)->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('horse_id'); // 1 to 6
            $table->string('horse_name', 50); // e.g. 'Thunder Bolt'
            $table->string('bet_type', 20); // 'win' (1st), 'place' (top 2), 'show' (top 3)
            $table->decimal('bet_amount', 12, 2);
            $table->decimal('odds', 8, 2)->default(2.50); // payout multiplier
            $table->decimal('payout_amount', 12, 2)->default(0.00);
            $table->decimal('net_profit', 12, 2)->default(0.00);
            $table->enum('status', ['pending', 'won', 'lost', 'refunded'])->default('pending');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['round_id', 'status']);
        });

        // 3. Add Horse Racing global settings to admin_settings table if missing
        Schema::table('admin_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('admin_settings', 'horse_racing_min_bet')) {
                $table->decimal('horse_racing_min_bet', 12, 2)->default(10.00)->after('roulette_manual_number');
                $table->decimal('horse_racing_max_bet', 12, 2)->default(25000.00)->after('horse_racing_min_bet');
                $table->unsignedInteger('horse_racing_round_duration')->default(60)->after('horse_racing_max_bet');
                $table->string('horse_racing_control_mode', 24)->default('fair')->after('horse_racing_round_duration');
                $table->unsignedTinyInteger('horse_racing_manual_winner')->nullable()->after('horse_racing_control_mode');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horse_race_bets');
        Schema::dropIfExists('horse_race_rounds');

        Schema::table('admin_settings', function (Blueprint $table) {
            $cols = [
                'horse_racing_min_bet',
                'horse_racing_max_bet',
                'horse_racing_round_duration',
                'horse_racing_control_mode',
                'horse_racing_manual_winner',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('admin_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
