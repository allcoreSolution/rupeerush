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
            $table->decimal('lottery_min_bet', 10, 2)->default(10.00)->after('win_multiplier');
            $table->decimal('lottery_max_bet', 10, 2)->default(10000.00)->after('lottery_min_bet');
            $table->decimal('wingo_min_bet', 10, 2)->default(1.00)->after('lottery_max_bet');
            $table->decimal('wingo_max_bet', 10, 2)->default(50000.00)->after('wingo_min_bet');
            $table->string('support_whatsapp', 50)->default('9026217223')->nullable()->after('instructions');
            $table->string('support_email', 100)->default('red247.online@gmail.com')->nullable()->after('support_whatsapp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            $table->dropColumn([
                'lottery_min_bet',
                'lottery_max_bet',
                'wingo_min_bet',
                'wingo_max_bet',
                'support_whatsapp',
                'support_email',
            ]);
        });
    }
};
