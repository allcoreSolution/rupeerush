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
        Schema::create('mines_games', function (Blueprint $table) {
            $table->id();
            $table->string('game_id', 30)->unique()->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('bet_amount', 10, 2);
            $table->unsignedTinyInteger('mines_count')->default(3)->index();
            $table->unsignedTinyInteger('grid_size')->default(25);
            $table->json('mine_positions'); // Array of 0-24 bomb indices
            $table->unsignedTinyInteger('jackpot_position')->nullable(); // Index of chest
            $table->json('opened_tiles')->nullable(); // Array of opened indices in order
            $table->unsignedTinyInteger('current_step')->default(0);
            $table->decimal('current_multiplier', 10, 2)->default(1.00);
            $table->decimal('win_amount', 10, 2)->default(0.00);
            $table->decimal('commission_amount', 10, 2)->default(0.00);
            $table->boolean('is_jackpot_win')->default(false);
            $table->decimal('jackpot_win_amount', 10, 2)->default(0.00);
            $table->enum('status', ['active', 'cashed_out', 'exploded'])->default('active')->index();
            $table->string('server_seed', 64)->nullable();
            $table->string('seed_hash', 64)->nullable();
            $table->dateTime('cashed_out_at')->nullable();
            $table->timestamps();
        });

        Schema::table('admin_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('admin_settings', 'mines_min_bet')) {
                $table->decimal('mines_min_bet', 10, 2)->default(1.00);
            }
            if (! Schema::hasColumn('admin_settings', 'mines_max_bet')) {
                $table->decimal('mines_max_bet', 10, 2)->default(25000.00);
            }
            if (! Schema::hasColumn('admin_settings', 'mines_house_edge')) {
                $table->decimal('mines_house_edge', 5, 2)->default(3.00);
            }
            if (! Schema::hasColumn('admin_settings', 'mines_jackpot_min_bet')) {
                $table->decimal('mines_jackpot_min_bet', 10, 2)->default(10.00);
            }
            if (! Schema::hasColumn('admin_settings', 'mines_jackpot_pool')) {
                $table->decimal('mines_jackpot_pool', 12, 2)->default(1417.61);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mines_games');

        Schema::table('admin_settings', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['mines_min_bet', 'mines_max_bet', 'mines_house_edge', 'mines_jackpot_min_bet', 'mines_jackpot_pool'] as $col) {
                if (Schema::hasColumn('admin_settings', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
