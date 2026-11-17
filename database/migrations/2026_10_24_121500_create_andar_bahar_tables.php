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
        Schema::create('andar_bahar_rounds', function (Blueprint $table) {
            $table->id();
            $table->string('round_id', 30)->unique()->index();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->index();
            $table->json('joker_card')->nullable();
            $table->json('dealt_cards')->nullable();
            $table->enum('winning_side', ['andar', 'bahar'])->nullable()->index();
            $table->json('winning_card')->nullable();
            $table->unsignedInteger('total_cards_dealt')->default(0);
            $table->enum('status', ['betting', 'dealing', 'completed'])->default('betting')->index();
            $table->unsignedInteger('total_bets_count')->default(0);
            $table->decimal('total_bet_amount', 12, 2)->default(0.00);
            $table->decimal('total_payout_amount', 12, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('andar_bahar_bets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('round_id', 30)->index();
            $table->string('bet_type', 30)->default('main')->index();
            $table->string('bet_selection', 20)->index(); // 'andar', 'bahar', range tags
            $table->decimal('amount', 10, 2);
            $table->decimal('win_multiplier', 5, 2)->default(1.95);
            $table->decimal('commission_amount', 10, 2)->default(0.00);
            $table->decimal('win_amount', 10, 2)->default(0.00);
            $table->enum('status', ['pending', 'won', 'lost'])->default('pending')->index();
            $table->dateTime('evaluated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('andar_bahar_bets');
        Schema::dropIfExists('andar_bahar_rounds');
    }
};
