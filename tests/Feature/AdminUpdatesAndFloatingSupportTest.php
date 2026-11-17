<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\GameSlot;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUpdatesAndFloatingSupportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed slots if not present
        if (GameSlot::count() === 0) {
            $slots = [];
            $times = ['09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '01:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM', '06:00 PM', '07:00 PM', '08:00 PM', '09:00 PM'];
            foreach ($times as $i => $time) {
                $slots[] = [
                    'slot_time' => $time,
                    'display_order' => $i + 1,
                    'interval_minutes' => 20,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            GameSlot::insert($slots);
        }

        // Ensure settings exist
        $setting = AdminSetting::current();
        $setting->update([
            'lottery_min_bet' => 10.00,
            'lottery_max_bet' => 10000.00,
            'wingo_min_bet' => 1.00,
            'wingo_max_bet' => 50000.00,
            'support_whatsapp' => '9026217223',
            'support_email' => 'red247.online@gmail.com',
        ]);
    }

    /**
     * Test WhatsApp and Email floating buttons are visible on the website home page.
     */
    public function test_home_page_renders_floating_whatsapp_and_email_support_buttons(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('9026217223');
        $response->assertSee('red247.online@gmail.com');
        $response->assertSee('floating-support-container', false);
        $response->assertSee('btn-floating-whatsapp', false);
        $response->assertSee('btn-floating-email', false);
    }

    /**
     * Test admin daily transactions detail page renders metrics and ledger.
     */
    public function test_admin_daily_transactions_page_renders_successfully(): void
    {
        $admin = User::firstOrCreate(
            ['phone' => '9999999999'],
            ['name' => 'Admin Test', 'email' => 'admintest@example.com', 'password' => bcrypt('password'), 'role' => 'admin', 'balance' => 1000.00, 'status' => 'active']
        );

        $today = Carbon::now('Asia/Kolkata')->format('Y-m-d');

        // Create sample transactions for today
        Transaction::create([
            'user_id' => $admin->id,
            'type' => 'deposit',
            'amount' => 500.00,
            'balance_before' => 1000.00,
            'balance_after' => 1500.00,
            'status' => 'approved',
            'utr_number' => 'TESTUTR123456',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.payments.daily', ['date' => $today]));

        $response->assertStatus(200);
        $response->assertSee('Daily-to-Daily Transaction Detail');
        $response->assertSee('TESTUTR123456');
        $response->assertSee('Approved Deposits');
        $response->assertSee('Paid Withdrawals');
    }

    /**
     * Test admin bets monitor filtering by higher amount, higher color user, and higher no user.
     */
    public function test_admin_bets_filtering_options(): void
    {
        $admin = User::firstOrCreate(
            ['phone' => '9999999999'],
            ['name' => 'Admin Test', 'email' => 'admintest@example.com', 'password' => bcrypt('password'), 'role' => 'admin', 'balance' => 1000.00, 'status' => 'active']
        );

        // 1. Higher amount filter
        $resHigherAmount = $this->actingAs($admin)->get(route('admin.bets.index', ['filter' => 'higher_amount']));
        $resHigherAmount->assertStatus(200);
        $resHigherAmount->assertSee('HIGHEST BET AMOUNT');

        // 2. Higher color user filter
        $resHigherColor = $this->actingAs($admin)->get(route('admin.bets.index', ['filter' => 'higher_color_user']));
        $resHigherColor->assertStatus(200);
        $resHigherColor->assertSee('HIGHER COLOR USER');

        // 3. Higher no user filter
        $resHigherNo = $this->actingAs($admin)->get(route('admin.bets.index', ['filter' => 'higher_no_user']));
        $resHigherNo->assertStatus(200);
        $resHigherNo->assertSee('HIGHER NUMBER USER');
    }

    /**
     * Test admin can update betting amount controlling for both games.
     */
    public function test_admin_can_update_betting_amount_controlling_for_both_games(): void
    {
        $admin = User::firstOrCreate(
            ['phone' => '9999999999'],
            ['name' => 'Admin Test', 'email' => 'admintest@example.com', 'password' => bcrypt('password'), 'role' => 'admin', 'balance' => 1000.00, 'status' => 'active']
        );

        $response = $this->actingAs($admin)->post(route('admin.payments.settings.update'), [
            'site_name' => 'red247.online',
            'upi_id' => 'admin@upi',
            'upi_name' => 'Official Admin',
            'min_deposit' => 100.00,
            'max_deposit' => 50000.00,
            'min_withdrawal' => 200.00,
            'win_multiplier' => 90.00,
            'lottery_min_bet' => 20.00,
            'lottery_max_bet' => 5000.00,
            'wingo_min_bet' => 5.00,
            'wingo_max_bet' => 25000.00,
            'support_whatsapp' => '9026217223',
            'support_email' => 'red247.online@gmail.com',
            'betting_lock_minutes' => 2,
        ]);

        $response->assertRedirect(route('admin.payments.settings'));

        $setting = AdminSetting::current();
        $this->assertEquals('20.00', $setting->lottery_min_bet);
        $this->assertEquals('5000.00', $setting->lottery_max_bet);
        $this->assertEquals('5.00', $setting->wingo_min_bet);
        $this->assertEquals('25000.00', $setting->wingo_max_bet);
        $this->assertEquals('9026217223', $setting->support_whatsapp);
        $this->assertEquals('red247.online@gmail.com', $setting->support_email);
    }

    /**
     * Test admin login page has no demo credentials box and authenticates with new email/password.
     */
    public function test_admin_login_page_has_no_demo_credentials_and_authenticates_with_new_email(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'red247.online@gmail.com'],
            [
                'name' => 'Red247 Administrator',
                'phone' => '9026217223',
                'password' => bcrypt('Red247@Admin2026'),
                'role' => 'admin',
                'balance' => 1000.00,
                'status' => 'active',
            ]
        );

        // 1. Check login page has no demo helper
        $pageResponse = $this->get(route('admin.login'));
        $pageResponse->assertStatus(200);
        $pageResponse->assertDontSee('Demo Admin Credentials');
        $pageResponse->assertDontSee('admin123');
        $pageResponse->assertDontSee('9999999999');

        // 2. Authenticate with new email
        $loginResponse = $this->post(route('admin.login.post'), [
            'login' => 'red247.online@gmail.com',
            'password' => 'Red247@Admin2026',
        ]);

        $loginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);

        // 3. Logout and Authenticate with phone
        $this->post(route('admin.logout'));
        $this->assertGuest();

        $phoneLoginResponse = $this->post(route('admin.login.post'), [
            'login' => '9026217223',
            'password' => 'Red247@Admin2026',
        ]);

        $phoneLoginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }
}
