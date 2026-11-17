<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\PaymentQr;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PaymentQrTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        AdminSetting::current();
    }

    public function test_admin_can_store_standard_and_custom_other_platform_qr(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $file1 = UploadedFile::fake()->create('phonepe.png', 100, 'image/png');

        // Store PhonePe QR
        $response = $this->actingAs($admin)->post(route('admin.payments.qrs.store'), [
            'platform' => 'phonepe',
            'upi_id' => 'admin@ybl',
            'upi_name' => 'Red247 PhonePe',
            'qr_code' => $file1,
        ]);

        $response->assertRedirect(route('admin.payments.settings'));
        $this->assertDatabaseHas('payment_qrs', [
            'platform' => 'phonepe',
            'platform_name' => 'PhonePe',
            'upi_id' => 'admin@ybl',
        ]);

        // Store Other platform QR with custom name
        $file2 = UploadedFile::fake()->create('cred.png', 100, 'image/png');
        $responseOther = $this->actingAs($admin)->post(route('admin.payments.qrs.store'), [
            'platform' => 'other',
            'custom_platform_name' => 'Cred UPI',
            'upi_id' => 'admin@axis',
            'upi_name' => 'Red247 Cred',
            'qr_code' => $file2,
        ]);

        $responseOther->assertRedirect(route('admin.payments.settings'));
        $this->assertDatabaseHas('payment_qrs', [
            'platform' => 'other',
            'platform_name' => 'Cred UPI',
            'upi_id' => 'admin@axis',
        ]);
    }

    public function test_player_deposit_saves_selected_platform_in_transaction_and_ledger(): void
    {
        $player = User::factory()->create([
            'role' => 'user',
            'balance' => 500,
        ]);

        $qr = PaymentQr::create([
            'platform' => 'phonepe',
            'platform_name' => 'PhonePe',
            'upi_id' => 'admin@ybl',
            'upi_name' => 'Merchant PhonePe',
            'qr_code_path' => 'uploads/qr/dummy.png',
            'is_active' => true,
            'display_order' => 1,
        ]);

        // View deposit page
        $pageResponse = $this->actingAs($player)->get(route('wallet.deposit'));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('PhonePe');

        // Submit deposit with selected QR
        $screenshot = UploadedFile::fake()->create('receipt.jpg', 100, 'image/jpeg');
        $submitResponse = $this->actingAs($player)->post(route('wallet.deposit.submit'), [
            'amount' => 500,
            'utr_number' => 'TESTUTR12345678',
            'screenshot' => $screenshot,
            'payment_qr_id' => $qr->id,
            'payment_platform' => 'PhonePe',
        ]);

        $submitResponse->assertRedirect(route('wallet.deposit'));

        $this->assertDatabaseHas('transactions', [
            'user_id' => $player->id,
            'type' => 'deposit',
            'amount' => 500.00,
            'utr_number' => 'TESTUTR12345678',
            'payment_method' => 'PhonePe QR',
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        $ledgerResponse = $this->actingAs($admin)->get(route('admin.ledger.show', $player->id));
        $ledgerResponse->assertStatus(200);
        $ledgerResponse->assertSee('PhonePe QR');
    }
}
