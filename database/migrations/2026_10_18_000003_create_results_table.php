<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->date('result_date')->index();
            $table->string('slot_time', 20)->index();
            $table->dateTime('slot_datetime')->nullable()->index();
            $table->dateTime('gen_datetime')->nullable()->index();
            $table->char('XA', 2)->nullable();
            $table->char('XB', 2)->nullable();
            $table->char('XC', 2)->nullable();
            $table->char('XD', 2)->nullable();
            $table->char('XE', 2)->nullable();
            $table->char('XF', 2)->nullable();
            $table->char('XG', 2)->nullable();
            $table->char('XH', 2)->nullable();
            $table->char('XI', 2)->nullable();
            $table->char('XJ', 2)->nullable();
            $table->enum('status', ['scheduled', 'published', 'cancelled'])->default('scheduled');
            $table->boolean('is_manual')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['result_date', 'slot_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
