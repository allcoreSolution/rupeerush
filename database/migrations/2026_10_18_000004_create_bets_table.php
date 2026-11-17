<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('result_date')->index();
            $table->string('slot_time', 20)->index();
            $table->enum('slot_key', ['XA', 'XB', 'XC', 'XD', 'XE', 'XF', 'XG', 'XH', 'XI', 'XJ']);
            $table->char('chosen_number', 2);
            $table->decimal('amount', 10, 2);
            $table->decimal('win_multiplier', 5, 2)->default(90.00);
            $table->decimal('win_amount', 10, 2)->default(0.00);
            $table->enum('status', ['pending', 'won', 'lost', 'cancelled'])->default('pending')->index();
            $table->dateTime('evaluated_at')->nullable();
            $table->timestamps();

            $table->index(['result_date', 'slot_time', 'slot_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bets');
    }
};
