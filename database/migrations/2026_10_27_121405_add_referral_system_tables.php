<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add referral_code and referred_by to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 20)->nullable()->unique()->after('status');
            $table->foreignId('referred_by')->nullable()->after('referral_code')->constrained('users')->nullOnDelete();
        });

        // 2. Add referral_commission_percent to admin_settings table
        Schema::table('admin_settings', function (Blueprint $table) {
            $table->decimal('referral_commission_percent', 5, 2)->default(1.00)->after('win_commission_percent');
        });

        // 3. Create referral_commissions ledger table (child_user_id is unique: 1st deposit commission only)
        Schema::create('referral_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('child_user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('deposit_transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->decimal('deposit_amount', 12, 2);
            $table->decimal('commission_percent', 5, 2);
            $table->decimal('commission_amount', 12, 2);
            $table->string('status', 20)->default('credited');
            $table->timestamps();
        });

        // 4. Backfill existing users with unique referral codes
        $users = DB::table('users')->whereNull('referral_code')->get(['id']);
        foreach ($users as $user) {
            do {
                $code = 'REF'.strtoupper(Str::random(6));
            } while (DB::table('users')->where('referral_code', $code)->exists());

            DB::table('users')->where('id', $user->id)->update(['referral_code' => $code]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_commissions');

        Schema::table('admin_settings', function (Blueprint $table) {
            $table->dropColumn('referral_commission_percent');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referred_by');
            $table->dropColumn('referral_code');
        });
    }
};
