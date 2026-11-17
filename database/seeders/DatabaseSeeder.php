<?php

namespace Database\Seeders;

use App\Models\AdminSetting;
use App\Models\GameSlot;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Administrator
        User::updateOrCreate(
            ['email' => 'rupeerushonline@gmail.com'],
            [
                'name' => 'Rupeerush Administrator',
                'phone' => '9026217223',
                'password' => Hash::make('Rush@2026'),
                'role' => 'admin',
                'balance' => 10000.00,
                'status' => 'active',
            ]
        );

        // 2. Seed Admin Settings
        AdminSetting::firstOrCreate(
            ['id' => 1],
            [
                'site_name' => 'Lottery',
                'upi_id' => 'lottery@upi',
                'upi_name' => 'Official Lottery Payments',
                'qr_code_path' => null,
                'min_deposit' => 100.00,
                'max_deposit' => 50000.00,
                'min_withdrawal' => 200.00,
                'win_multiplier' => 90.00,
                'betting_lock_minutes' => 2,
                'auto_result_mode' => true,
                'instructions' => 'Scan the QR Code using any UPI App (GPay, PhonePe, Paytm). After payment, enter your 12-digit UTR/Ref No. and upload the screenshot receipt.',
            ]
        );

        // 4. Seed Official Slots
        $slots = [
            // Morning 15-min intervals
            ['time' => '09:00 AM', 'interval' => 15],
            ['time' => '09:15 AM', 'interval' => 15],
            ['time' => '09:30 AM', 'interval' => 15],
            ['time' => '09:45 AM', 'interval' => 15],
            ['time' => '10:00 AM', 'interval' => 15],
            ['time' => '10:15 AM', 'interval' => 15],
            ['time' => '10:30 AM', 'interval' => 15],
            ['time' => '10:45 AM', 'interval' => 15],
            ['time' => '11:00 AM', 'interval' => 15],
            // Day & Evening 20-min intervals
            ['time' => '11:20 AM', 'interval' => 20],
            ['time' => '11:40 AM', 'interval' => 20],
            ['time' => '12:00 PM', 'interval' => 20],
            ['time' => '12:20 PM', 'interval' => 20],
            ['time' => '12:40 PM', 'interval' => 20],
            ['time' => '01:00 PM', 'interval' => 20],
            ['time' => '01:20 PM', 'interval' => 20],
            ['time' => '01:40 PM', 'interval' => 20],
            ['time' => '02:00 PM', 'interval' => 20],
            ['time' => '02:20 PM', 'interval' => 20],
            ['time' => '02:40 PM', 'interval' => 20],
            ['time' => '03:00 PM', 'interval' => 20],
            ['time' => '03:20 PM', 'interval' => 20],
            ['time' => '03:40 PM', 'interval' => 20],
            ['time' => '04:00 PM', 'interval' => 20],
            ['time' => '04:20 PM', 'interval' => 20],
            ['time' => '04:40 PM', 'interval' => 20],
            ['time' => '05:00 PM', 'interval' => 20],
            ['time' => '05:20 PM', 'interval' => 20],
            ['time' => '05:40 PM', 'interval' => 20],
            ['time' => '06:00 PM', 'interval' => 20],
            ['time' => '06:20 PM', 'interval' => 20],
            ['time' => '06:40 PM', 'interval' => 20],
            ['time' => '07:00 PM', 'interval' => 20],
            ['time' => '07:20 PM', 'interval' => 20],
            ['time' => '07:40 PM', 'interval' => 20],
            ['time' => '08:00 PM', 'interval' => 20],
            ['time' => '08:20 PM', 'interval' => 20],
            ['time' => '08:40 PM', 'interval' => 20],
            ['time' => '09:00 PM', 'interval' => 20],
            ['time' => '09:20 PM', 'interval' => 20],
            ['time' => '09:40 PM', 'interval' => 20],
        ];

        foreach ($slots as $order => $s) {
            GameSlot::updateOrCreate(
                ['slot_time' => $s['time']],
                [
                    'display_order' => $order + 1,
                    'interval_minutes' => $s['interval'],
                    'is_active' => true,
                ]
            );
        }
    }
}
