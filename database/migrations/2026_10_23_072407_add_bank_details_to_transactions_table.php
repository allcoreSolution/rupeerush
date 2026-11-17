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
            $table->string('account_holder_name')->nullable()->after('account_details');
            $table->string('account_number')->nullable()->after('account_holder_name');
            $table->string('ifsc_code', 20)->nullable()->after('account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['account_holder_name', 'account_number', 'ifsc_code']);
        });
    }
};
