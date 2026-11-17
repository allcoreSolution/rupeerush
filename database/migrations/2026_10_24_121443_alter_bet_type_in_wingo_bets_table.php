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
        if (Schema::hasTable('wingo_bets')) {
            Schema::table('wingo_bets', function (Blueprint $table) {
                $table->string('bet_type', 30)->default('color')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('wingo_bets')) {
            Schema::table('wingo_bets', function (Blueprint $table) {
                $table->enum('bet_type', ['color', 'number', 'size'])->default('color')->change();
            });
        }
    }
};
