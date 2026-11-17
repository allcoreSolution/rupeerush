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
        Schema::table('wingo_periods', function (Blueprint $table) {
            $table->string('manual_color', 20)->nullable()->after('manual_number');
            $table->string('manual_size', 10)->nullable()->after('manual_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wingo_periods', function (Blueprint $table) {
            $table->dropColumn(['manual_color', 'manual_size']);
        });
    }
};
