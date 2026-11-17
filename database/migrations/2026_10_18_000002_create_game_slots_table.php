<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_slots', function (Blueprint $table) {
            $table->id();
            $table->string('slot_time', 20)->unique();
            $table->integer('display_order')->default(0);
            $table->integer('interval_minutes')->default(20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_slots');
    }
};
