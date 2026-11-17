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
        Schema::create('wingo_periods', function (Blueprint $table) {
            $table->id();
            $table->enum('game_type', ['30s', '1m', '3m', '5m'])->default('30s')->index();
            $table->string('period_id', 30)->index();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->index();
            $table->unsignedTinyInteger('winning_number')->nullable();
            $table->string('winning_color', 20)->nullable();
            $table->string('winning_size', 10)->nullable();
            $table->enum('status', ['betting', 'locked', 'completed'])->default('betting')->index();
            $table->boolean('is_manual')->default(false);
            $table->unsignedTinyInteger('manual_number')->nullable();
            $table->unsignedInteger('total_bets_count')->default(0);
            $table->decimal('total_bet_amount', 12, 2)->default(0.00);
            $table->decimal('total_payout_amount', 12, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['game_type', 'period_id']);
        });

        Schema::create('wingo_bets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('game_type', ['30s', '1m', '3m', '5m'])->default('30s')->index();
            $table->string('period_id', 30)->index();
            $table->enum('bet_type', ['color', 'number', 'size'])->index();
            $table->string('bet_selection', 20)->index();
            $table->decimal('unit_price', 10, 2);
            $table->unsignedInteger('multiplier')->default(1);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('win_multiplier', 5, 2)->default(0.00);
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
        Schema::dropIfExists('wingo_bets');
        Schema::dropIfExists('wingo_periods');
    }
};
