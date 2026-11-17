<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('Lottery');
            $table->string('upi_id')->nullable();
            $table->string('upi_name')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->decimal('min_deposit', 10, 2)->default(100.00);
            $table->decimal('max_deposit', 10, 2)->default(50000.00);
            $table->decimal('min_withdrawal', 10, 2)->default(200.00);
            $table->decimal('win_multiplier', 5, 2)->default(90.00);
            $table->integer('betting_lock_minutes')->default(2);
            $table->boolean('auto_result_mode')->default(true);
            $table->text('instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_settings');
    }
};
