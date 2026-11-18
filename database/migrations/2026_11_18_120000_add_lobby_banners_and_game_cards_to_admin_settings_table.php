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
            if (! Schema::hasColumn('admin_settings', 'hero_banner_4')) {
                $table->string('hero_banner_4', 255)->nullable()->after('hero_banner_3');
            }
            if (! Schema::hasColumn('admin_settings', 'hero_banner_5')) {
                $table->string('hero_banner_5', 255)->nullable()->after('hero_banner_4');
            }
            if (! Schema::hasColumn('admin_settings', 'hero_banner_6')) {
                $table->string('hero_banner_6', 255)->nullable()->after('hero_banner_5');
            }
            if (! Schema::hasColumn('admin_settings', 'hero_banner_4_link')) {
                $table->string('hero_banner_4_link', 255)->nullable()->after('hero_banner_3_link');
            }
            if (! Schema::hasColumn('admin_settings', 'hero_banner_5_link')) {
                $table->string('hero_banner_5_link', 255)->nullable()->after('hero_banner_4_link');
            }
            if (! Schema::hasColumn('admin_settings', 'hero_banner_6_link')) {
                $table->string('hero_banner_6_link', 255)->nullable()->after('hero_banner_5_link');
            }
            if (! Schema::hasColumn('admin_settings', 'hero_banner_titles')) {
                $table->json('hero_banner_titles')->nullable()->after('hero_banner_6_link');
            }
            if (! Schema::hasColumn('admin_settings', 'game_cards_config')) {
                $table->json('game_cards_config')->nullable()->after('hero_banner_titles');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            $cols = [];
            foreach (['hero_banner_4', 'hero_banner_5', 'hero_banner_6', 'hero_banner_4_link', 'hero_banner_5_link', 'hero_banner_6_link', 'hero_banner_titles', 'game_cards_config'] as $col) {
                if (Schema::hasColumn('admin_settings', $col)) {
                    $cols[] = $col;
                }
            }
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
