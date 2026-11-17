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
        // 1. Roulette Live Game Rounds Table
        Schema::create('roulette_rounds', function (Blueprint $table) {
            $table->id();
            $table->string('round_id', 32)->unique();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->enum('status', ['betting', 'spinning', 'completed', 'cancelled'])->default('betting');
            $table->unsignedTinyInteger('winning_number')->nullable(); // 0 to 36
            $table->string('winning_color', 10)->nullable(); // 'green', 'red', 'black'
            $table->boolean('is_even')->nullable();
            $table->boolean('is_high')->nullable(); // false: 1-18, true: 19-36
            $table->unsignedTinyInteger('dozen')->nullable(); // 1, 2, 3
            $table->unsignedTinyInteger('column')->nullable(); // 1, 2, 3
            $table->string('seed_hash', 64)->nullable(); // SHA-256 provably fair
            $table->string('server_seed', 64)->nullable();
            $table->boolean('is_manual')->default(false);
            $table->unsignedTinyInteger('manual_number')->nullable();
            $table->unsignedInteger('total_bets_count')->default(0);
            $table->decimal('total_bet_amount', 12, 2)->default(0.00);
            $table->decimal('total_payout_amount', 12, 2)->default(0.00);
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        // 2. Roulette User Bets Table
        Schema::create('roulette_bets', function (Blueprint $table) {
            $table->id();
            $table->string('bet_id', 32)->unique();
            $table->string('round_id', 32)->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('bet_type', 24); // 'straight', 'red', 'black', 'even', 'odd', 'low', 'high', 'dozen1', 'dozen2', 'dozen3', 'col1', 'col2', 'col3', 'split', 'corner'
            $table->string('bet_value', 32); // e.g. '17', 'red', 'even', 'dozen1', '7,8'
            $table->decimal('bet_amount', 12, 2);
            $table->decimal('odds', 6, 2)->default(36.00); // multiplier
            $table->decimal('payout_amount', 12, 2)->default(0.00);
            $table->decimal('net_profit', 12, 2)->default(0.00);
            $table->enum('status', ['pending', 'won', 'lost', 'refunded'])->default('pending');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['round_id', 'status']);
        });

        // 3. Add Roulette settings to admin_settings table if missing
        Schema::table('admin_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('admin_settings', 'roulette_min_bet')) {
                $table->decimal('roulette_min_bet', 12, 2)->default(10.00)->after('teen_patti_manual_winner');
                $table->decimal('roulette_max_bet', 12, 2)->default(25000.00)->after('roulette_min_bet');
                $table->unsignedInteger('roulette_round_duration')->default(30)->after('roulette_max_bet');
                $table->string('roulette_control_mode', 24)->default('fair')->after('roulette_round_duration');
                $table->unsignedTinyInteger('roulette_manual_number')->nullable()->after('roulette_control_mode');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roulette_bets');
        Schema::dropIfExists('roulette_rounds');

        Schema::table('admin_settings', function (Blueprint $table) {
            $cols = [
                'roulette_min_bet',
                'roulette_max_bet',
                'roulette_round_duration',
                'roulette_control_mode',
                'roulette_manual_number',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('admin_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
