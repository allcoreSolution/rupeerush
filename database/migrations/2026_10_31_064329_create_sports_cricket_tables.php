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
        Schema::create('sports_matches', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable()->index();
            $table->string('sport')->default('cricket')->index();
            $table->string('series_name')->nullable();
            $table->string('title');
            $table->string('match_type')->default('t20'); // t20, odi, test, league
            $table->string('status')->default('upcoming')->index(); // upcoming, live, completed, cancelled
            $table->string('team_a');
            $table->string('team_a_short')->nullable();
            $table->string('team_a_img')->nullable();
            $table->string('team_b');
            $table->string('team_b_short')->nullable();
            $table->string('team_b_img')->nullable();
            $table->string('venue')->nullable();
            $table->string('score_team_a')->nullable();
            $table->string('score_team_b')->nullable();
            $table->string('current_summary')->nullable();
            $table->json('recent_balls')->nullable();
            $table->string('winner_team')->nullable();
            $table->boolean('is_live')->default(false)->index();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->json('meta_data')->nullable();
            $table->timestamps();
        });

        Schema::create('sports_markets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('sports_matches')->onDelete('cascade');
            $table->string('market_type')->default('match_winner'); // match_winner, over_runs, tied_match, highest_opening
            $table->string('market_name');
            $table->string('status')->default('active')->index(); // active, suspended, settled, void
            $table->unsignedBigInteger('winning_runner_id')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sports_runners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_id')->constrained('sports_markets')->onDelete('cascade');
            $table->string('runner_name');
            $table->decimal('back_odds', 8, 2)->default(1.90);
            $table->decimal('lay_odds', 8, 2)->nullable()->default(1.95);
            $table->decimal('back_volume', 12, 2)->default(0);
            $table->decimal('lay_volume', 12, 2)->default(0);
            $table->string('status')->default('active')->index(); // active, winner, loser, suspended
            $table->timestamps();
        });

        Schema::create('sports_bets', function (Blueprint $table) {
            $table->id();
            $table->string('bet_id')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('match_id')->constrained('sports_matches')->onDelete('cascade');
            $table->foreignId('market_id')->constrained('sports_markets')->onDelete('cascade');
            $table->foreignId('runner_id')->constrained('sports_runners')->onDelete('cascade');
            $table->string('bet_type')->default('back'); // back, lay
            $table->string('runner_name');
            $table->decimal('stake', 12, 2);
            $table->decimal('odds', 8, 2);
            $table->decimal('potential_payout', 12, 2);
            $table->decimal('win_amount', 12, 2)->default(0);
            $table->string('status')->default('pending')->index(); // pending, won, lost, void, cancelled
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sports_bets');
        Schema::dropIfExists('sports_runners');
        Schema::dropIfExists('sports_markets');
        Schema::dropIfExists('sports_matches');
    }
};
