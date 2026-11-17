<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PlayerLedgerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $player;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@red247.online',
            'phone' => '9000000001',
            'password' => Hash::make('Secret123!'),
            'role' => 'admin',
            'status' => 'active',
            'balance' => 0,
        ]);

        $this->player = User::create([
            'name' => 'Rahul Sharma',
            'email' => 'rahul@example.com',
            'phone' => '9876543210',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'status' => 'active',
            'balance' => 2500,
        ]);

        // Seed some transactions for the player
        Transaction::create([
            'user_id' => $this->player->id,
            'type' => 'deposit',
            'amount' => 5000,
            'balance_before' => 0,
            'balance_after' => 5000,
            'status' => 'approved',
            'payment_method' => 'UPI',
            'admin_notes' => 'UPI Deposit Verified',
        ]);

        Transaction::create([
            'user_id' => $this->player->id,
            'type' => 'withdrawal',
            'amount' => 2000,
            'balance_before' => 5000,
            'balance_after' => 3000,
            'status' => 'approved',
            'payment_method' => 'IMPS',
            'admin_notes' => 'Bank Payout Processed',
        ]);

        Transaction::create([
            'user_id' => $this->player->id,
            'type' => 'bet_placed',
            'amount' => 1000,
            'balance_before' => 3000,
            'balance_after' => 2000,
            'status' => 'approved',
            'admin_notes' => 'Dragon Tiger Bet',
        ]);

        Transaction::create([
            'user_id' => $this->player->id,
            'type' => 'bet_won',
            'amount' => 1500,
            'balance_before' => 2000,
            'balance_after' => 3500,
            'status' => 'approved',
            'admin_notes' => 'Dragon Tiger Win Payout',
        ]);
    }

    public function test_guest_is_redirected_from_ledger(): void
    {
        $response = $this->get(route('admin.ledger.index'));
        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_ledger(): void
    {
        $response = $this->actingAs($this->player)->get(route('admin.ledger.index'));
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_ledger_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.ledger.index'));

        $response->assertStatus(200);
        $response->assertSee('Wallet Exposure');
        $response->assertSee('Rahul Sharma');
        $response->assertSee('9876543210');
    }

    public function test_admin_can_view_player_statement_passbook(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.ledger.show', $this->player->id));

        $response->assertStatus(200);
        $response->assertSee('Account Statement &amp; Passbook', false);
        $response->assertSee('Rahul Sharma');
        $response->assertSee('Adjusted Balance');
        $response->assertSee('Deposits (');
        $response->assertSee('Withdrawals (');
        $response->assertSee('Manual Adjustments (');
        $response->assertSee('UPI Deposit Verified');
        $response->assertSee('Dragon Tiger Bet');
    }

    public function test_admin_can_adjust_player_balance_credit(): void
    {
        $initialBalance = (float) $this->player->fresh()->balance;

        $response = $this->actingAs($this->admin)->post(route('admin.ledger.balance', $this->player->id), [
            'action' => 'credit',
            'amount' => 500,
            'notes' => 'Loyalty Reward Bonus',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $updatedPlayer = $this->player->fresh();
        $this->assertEquals($initialBalance + 500, (float) $updatedPlayer->balance);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->player->id,
            'type' => 'admin_adjustment',
            'amount' => 500,
            'status' => 'completed',
            'admin_notes' => 'Admin credit: Loyalty Reward Bonus',
        ]);
    }

    public function test_admin_can_adjust_player_balance_debit(): void
    {
        $initialBalance = (float) $this->player->fresh()->balance;

        $response = $this->actingAs($this->admin)->post(route('admin.ledger.balance', $this->player->id), [
            'action' => 'debit',
            'amount' => 300,
            'notes' => 'Chargeback reversal',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $updatedPlayer = $this->player->fresh();
        $this->assertEquals($initialBalance - 300, (float) $updatedPlayer->balance);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->player->id,
            'type' => 'admin_adjustment',
            'amount' => 300,
            'status' => 'completed',
            'admin_notes' => 'Admin debit: Chargeback reversal',
        ]);
    }

    public function test_admin_can_export_player_statement_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.ledger.export', $this->player->id));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('ACCOUNT STATEMENT / PASSBOOK LEDGER', $response->streamedContent());
        $this->assertStringContainsString('Narration / Description', $response->streamedContent());
    }
}
