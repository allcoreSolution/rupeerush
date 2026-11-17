<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\ReferralCommission;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReferralSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $setting = AdminSetting::current();
        $setting->update([
            'referral_commission_percent' => 1.00,
        ]);
    }

    /**
     * Test that registering a user automatically assigns a unique referral code.
     */
    public function test_user_registration_automatically_generates_unique_referral_code(): void
    {
        $user = User::create([
            'name' => 'Vijay Player',
            'phone' => '9876543210',
            'password' => Hash::make('secret123'),
            'role' => 'user',
            'balance' => 0.00,
            'status' => 'active',
        ]);

        $this->assertNotNull($user);
        $this->assertNotEmpty($user->referral_code);
        $this->assertStringStartsWith('REF', $user->referral_code);
    }

    /**
     * Test registering with an existing user's referral code sets referred_by.
     */
    public function test_child_user_registers_with_parent_referral_code_and_links_to_parent(): void
    {
        $parent = User::create([
            'name' => 'Parent User',
            'phone' => '9876543211',
            'password' => Hash::make('secret123'),
            'role' => 'user',
            'balance' => 0.00,
            'status' => 'active',
            'referral_code' => 'REFPARENT1',
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Child User',
            'phone' => '9876543212',
            'password' => 'secret123',
            'referral_code' => 'REFPARENT1',
        ]);

        $response->assertSessionHas('new_player_credentials');

        $child = User::where('phone', '9876543212')->first();
        $this->assertNotNull($child);
        $this->assertEquals($parent->id, $child->referred_by);
        $this->assertTrue($parent->referrals()->where('id', $child->id)->exists());

        $child = User::where('phone', '9876543212')->first();
        $this->assertNotNull($child);
        $this->assertEquals($parent->id, $child->referred_by);
        $this->assertTrue($parent->referrals()->where('id', $child->id)->exists());
    }

    /**
     * Test parent earns 1% commission on child's first approved deposit.
     */
    public function test_parent_earns_commission_on_child_first_approved_deposit(): void
    {
        $admin = User::create([
            'name' => 'Admin Boss',
            'phone' => '9999999999',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'balance' => 0.00,
            'status' => 'active',
        ]);

        $parent = User::create([
            'name' => 'Ramesh Sharma',
            'phone' => '9876543213',
            'password' => Hash::make('secret123'),
            'role' => 'user',
            'balance' => 100.00,
            'status' => 'active',
            'referral_code' => 'REFRAMESH1',
        ]);

        $child = User::create([
            'name' => 'Suresh Kumar',
            'phone' => '9876543214',
            'password' => Hash::make('secret123'),
            'role' => 'user',
            'balance' => 0.00,
            'status' => 'active',
            'referred_by' => $parent->id,
            'referral_code' => 'REFSURESH1',
        ]);

        // Child initiates a deposit of ₹2,000
        $deposit = Transaction::create([
            'user_id' => $child->id,
            'type' => 'deposit',
            'amount' => 2000.00,
            'balance_before' => 0.00,
            'balance_after' => 0.00,
            'utr_number' => 'UTR123456789012',
            'payment_method' => 'UPI',
            'status' => 'pending',
        ]);

        // Admin approves deposit
        $response = $this->actingAs($admin)->post(route('admin.payments.deposits.approve', $deposit->id));
        $response->assertSessionHas('success');

        // Check child balance
        $child->refresh();
        $this->assertEquals(2000.00, (float) $child->balance);

        // Check parent balance: Initial 100 + (1% of 2000 = 20) = 120.00
        $parent->refresh();
        $this->assertEquals(120.00, (float) $parent->balance);

        // Check referral commission record
        $commission = ReferralCommission::where('child_user_id', $child->id)->first();
        $this->assertNotNull($commission);
        $this->assertEquals($parent->id, $commission->parent_user_id);
        $this->assertEquals(20.00, (float) $commission->commission_amount);
        $this->assertEquals(1.00, (float) $commission->commission_percent);

        // Check parent ledger transaction
        $ledger = Transaction::where('user_id', $parent->id)
            ->where('payment_method', 'REFERRAL_BONUS')
            ->first();
        $this->assertNotNull($ledger);
        $this->assertEquals(20.00, (float) $ledger->amount);
        $this->assertEquals('approved', $ledger->status);
    }

    /**
     * Test parent does NOT receive commission on child's second deposit.
     */
    public function test_child_second_deposit_does_not_trigger_referral_commission(): void
    {
        $admin = User::create([
            'name' => 'Admin Boss',
            'phone' => '9999999998',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'balance' => 0.00,
            'status' => 'active',
        ]);

        $parent = User::create([
            'name' => 'Parent Lucky',
            'phone' => '9876543215',
            'password' => Hash::make('secret123'),
            'role' => 'user',
            'balance' => 0.00,
            'status' => 'active',
            'referral_code' => 'REFLUCKY01',
        ]);

        $child = User::create([
            'name' => 'Child Lucky',
            'phone' => '9876543216',
            'password' => Hash::make('secret123'),
            'role' => 'user',
            'balance' => 0.00,
            'status' => 'active',
            'referred_by' => $parent->id,
            'referral_code' => 'REFLUCKY02',
        ]);

        // First deposit: ₹1,000
        $dep1 = Transaction::create([
            'user_id' => $child->id,
            'type' => 'deposit',
            'amount' => 1000.00,
            'balance_before' => 0.00,
            'balance_after' => 0.00,
            'status' => 'pending',
            'payment_method' => 'UPI',
        ]);
        $this->actingAs($admin)->post(route('admin.payments.deposits.approve', $dep1->id));

        $parent->refresh();
        $this->assertEquals(10.00, (float) $parent->balance); // 1% of 1000

        // Second deposit: ₹5,000
        $dep2 = Transaction::create([
            'user_id' => $child->id,
            'type' => 'deposit',
            'amount' => 5000.00,
            'balance_before' => 1000.00,
            'balance_after' => 1000.00,
            'status' => 'pending',
            'payment_method' => 'UPI',
        ]);
        $this->actingAs($admin)->post(route('admin.payments.deposits.approve', $dep2->id));

        // Parent balance must STILL be 10.00 (not increased!)
        $parent->refresh();
        $this->assertEquals(10.00, (float) $parent->balance);

        // Only 1 commission record
        $this->assertEquals(1, ReferralCommission::where('child_user_id', $child->id)->count());
    }

    /**
     * Test parent can view their referrals list on the referral dashboard.
     */
    public function test_parent_user_can_view_referral_dashboard_and_list(): void
    {
        $parent = User::create([
            'name' => 'Dashboard Parent',
            'phone' => '9876543217',
            'password' => Hash::make('secret123'),
            'role' => 'user',
            'balance' => 50.00,
            'status' => 'active',
            'referral_code' => 'REFDASH01',
        ]);

        $child1 = User::create([
            'name' => 'Friend One',
            'phone' => '9876543218',
            'password' => Hash::make('secret123'),
            'role' => 'user',
            'balance' => 0.00,
            'status' => 'active',
            'referred_by' => $parent->id,
            'referral_code' => 'REFFRIEND1',
        ]);

        $child2 = User::create([
            'name' => 'Friend Two',
            'phone' => '9876543219',
            'password' => Hash::make('secret123'),
            'role' => 'user',
            'balance' => 0.00,
            'status' => 'active',
            'referred_by' => $parent->id,
            'referral_code' => 'REFFRIEND2',
        ]);

        $response = $this->actingAs($parent)->get(route('referral.index'));

        $response->assertStatus(200);
        $response->assertSee('REFDASH01');
        $response->assertSee('Friend One');
        $response->assertSee('Friend Two');
        $response->assertSee('Your Referred Friends List');
    }

    /**
     * Test admin can update referral commission percentage and it takes effect.
     */
    public function test_admin_can_update_referral_commission_percent(): void
    {
        $admin = User::create([
            'name' => 'Admin Master',
            'phone' => '9999999997',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'balance' => 0.00,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.payments.settings.update'), [
            'site_name' => 'red247.online',
            'upi_id' => 'admin@upi',
            'min_deposit' => 100,
            'max_deposit' => 100000,
            'min_withdrawal' => 200,
            'win_multiplier' => 90,
            'win_commission_percent' => 10.0,
            'referral_commission_percent' => 3.5, // 3.5%
            'lottery_min_bet' => 10,
            'lottery_max_bet' => 10000,
            'wingo_min_bet' => 1,
            'wingo_max_bet' => 50000,
            'betting_lock_minutes' => 5,
        ]);

        $response->assertRedirect(route('admin.payments.settings'));

        $setting = AdminSetting::current();
        $this->assertEquals(3.50, (float) $setting->referral_commission_percent);
    }
}
