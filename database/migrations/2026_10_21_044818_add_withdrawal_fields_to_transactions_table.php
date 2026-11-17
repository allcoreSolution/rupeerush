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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('user_qr_path')->nullable()->after('screenshot_path');
            $table->string('admin_proof_path')->nullable()->after('user_qr_path');
            $table->text('account_details')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['user_qr_path', 'admin_proof_path', 'account_details']);
        });
    }
};
