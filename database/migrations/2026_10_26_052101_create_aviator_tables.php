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
        Schema::create('aviator_rounds', function (Blueprint $table) {
            $table->id();
            $table->string('round_id', 30)->unique()->index();
            $table->dateTime('start_time');
            $table->dateTime('flight_start_time');
            $table->dateTime('crash_time');
            $table->dateTime('end_time')->index();
            $table->decimal('crash_multiplier', 8, 2)->default(1.00);
            $table->enum('status', ['betting', 'flying', 'crashed', 'completed'])->default('betting')->index();
            $table->boolean('is_manual')->default(false);
            $table->decimal('manual_crash_multiplier', 8, 2)->nullable();
            $table->string('seed_hash', 64)->nullable();
            $table->unsignedInteger('total_bets_count')->default(0);
            $table->decimal('total_bet_amount', 12, 2)->default(0.00);
            $table->decimal('total_payout_amount', 12, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('aviator_bets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('round_id', 30)->index();
            $table->unsignedTinyInteger('bet_slot')->default(1)->index(); // Slot 1 or Slot 2
            $table->decimal('amount', 10, 2);
            $table->decimal('auto_cashout', 8, 2)->nullable();
            $table->decimal('cashout_multiplier', 8, 2)->nullable();
            $table->decimal('commission_amount', 10, 2)->default(0.00);
            $table->decimal('win_amount', 10, 2)->default(0.00);
            $table->enum('status', ['pending', 'cashed_out', 'lost', 'cancelled'])->default('pending')->index();
            $table->dateTime('cashed_out_at')->nullable();
            $table->timestamps();
        });

        Schema::table('admin_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('admin_settings', 'aviator_min_bet')) {
                $table->decimal('aviator_min_bet', 10, 2)->default(10.00);
            }
            if (! Schema::hasColumn('admin_settings', 'aviator_max_bet')) {
                $table->decimal('aviator_max_bet', 10, 2)->default(50000.00);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            if (Schema::hasColumn('admin_settings', 'aviator_min_bet')) {
                $table->dropColumn('aviator_min_bet');
            }
            if (Schema::hasColumn('admin_settings', 'aviator_max_bet')) {
                $table->dropColumn('aviator_max_bet');
            }
        });

        Schema::dropIfExists('aviator_bets');
        Schema::dropIfExists('aviator_rounds');
    }
};
