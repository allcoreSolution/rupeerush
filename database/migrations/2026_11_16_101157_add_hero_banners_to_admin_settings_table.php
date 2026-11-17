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
        Schema::table('admin_settings', function (Blueprint $table) {
            $table->string('hero_banner_1', 255)->nullable()->after('instructions');
            $table->string('hero_banner_2', 255)->nullable()->after('hero_banner_1');
            $table->string('hero_banner_3', 255)->nullable()->after('hero_banner_2');
            $table->string('hero_banner_1_link', 255)->nullable()->after('hero_banner_3');
            $table->string('hero_banner_2_link', 255)->nullable()->after('hero_banner_1_link');
            $table->string('hero_banner_3_link', 255)->nullable()->after('hero_banner_2_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_banner_1',
                'hero_banner_2',
                'hero_banner_3',
                'hero_banner_1_link',
                'hero_banner_2_link',
                'hero_banner_3_link',
            ]);
        });
    }
};
