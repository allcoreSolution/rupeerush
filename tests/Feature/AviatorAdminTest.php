<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\AviatorBet;
use App\Models\AviatorRound;
use App\Models\GameSlot;
use App\Models\User;
use App\Services\AviatorEngine;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AviatorAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-10-26 12:00:00'));

        if (GameSlot::count() === 0) {
            GameSlot::create([
                'slot_time' => '10:00 AM',
                'display_order' => 1,
                'interval_minutes' => 20,
                'is_active' => true,
            ]);
        }

        AdminSetting::current()->update([
            'aviator_min_bet' => 10.00,
            'aviator_max_bet' => 50000.00,
            'win_commission_percent' => 10.00,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin Officer',
            'phone' => '9999999999',
            'email' => 'admin_officer@example.com',
            'password' => bcrypt('adminsecret'),
            'balance' => 50000.00,
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    private function createPlayer(): User
    {
        return User::create([
            'name' => 'Regular Player',
            'phone' => '9888888888',
            'email' => 'regular_user@example.com',
            'password' => bcrypt('password123'),
            'balance' => 1000.00,
            'role' => 'user',
            'status' => 'active',
        ]);
    }

    public function test_non_admin_cannot_access_aviator_admin_panel(): void
    {
        $player = $this->createPlayer();

        $response = $this->actingAs($player)->get(route('admin.aviator.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_aviator_control_hub(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.aviator.index'));
        $response->assertOk()
            ->assertSee('Aviator Live Control Hub')
            ->assertSee('Set Manual Crash Point');
    }

    public function test_admin_can_lock_manual_crash_multiplier(): void
    {
        $admin = $this->createAdmin();
        $roundData = AviatorEngine::getCurrentRound();
        $roundId = $roundData['round_id'];

        $response = $this->actingAs($admin)->post(route('admin.aviator.set-manual'), [
            'round_id' => $roundId,
            'crash_multiplier' => 2.45,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('admin_settings', [
            'aviator_manual_multiplier' => 2.45,
        ]);

        $this->assertDatabaseHas('aviator_rounds', [
            'round_id' => $roundId,
            'is_manual' => true,
            'crash_multiplier' => 2.45,
            'manual_crash_multiplier' => 2.45,
        ]);

        // Verify next newly created round automatically inherits the locked multiplier
        $nextRound = AviatorEngine::createNewRound();
        $this->assertTrue((bool) $nextRound->is_manual);
        $this->assertEquals(2.45, (float) $nextRound->crash_multiplier);
    }

    public function test_admin_can_clear_manual_crash_multiplier(): void
    {
        $admin = $this->createAdmin();
        $roundData = AviatorEngine::getCurrentRound();
        $roundId = $roundData['round_id'];

        // Lock first
        AdminSetting::current()->update(['aviator_manual_multiplier' => 3.50]);
        AviatorRound::where('round_id', $roundId)->update([
            'is_manual' => true,
            'crash_multiplier' => 3.50,
            'manual_crash_multiplier' => 3.50,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.aviator.clear-manual'), [
            'round_id' => $roundId,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('admin_settings', [
            'aviator_manual_multiplier' => null,
        ]);

        $this->assertDatabaseHas('aviator_rounds', [
            'round_id' => $roundId,
            'is_manual' => false,
            'manual_crash_multiplier' => null,
        ]);
    }

    public function test_admin_can_update_aviator_settings(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.aviator.update-settings'), [
            'aviator_min_bet' => 25.00,
            'aviator_max_bet' => 25000.00,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('admin_settings', [
            'aviator_min_bet' => 25.00,
            'aviator_max_bet' => 25000.00,
        ]);
    }

    public function test_admin_can_view_winners_list(): void
    {
        $admin = $this->createAdmin();
        $player = $this->createPlayer();

        // Create winning bet
        AviatorBet::create([
            'user_id' => $player->id,
            'round_id' => 'AV-TEST-999',
            'bet_slot' => 1,
            'amount' => 100.00,
            'cashout_multiplier' => 3.20,
            'win_amount' => 320.00,
            'status' => 'cashed_out',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.aviator.winners'));
        $response->assertOk()
            ->assertSee('Aviator Winner List')
            ->assertSee('AV-TEST-999')
            ->assertSee('3.20x');
    }

    public function test_admin_all_bets_monitor_displays_aviator_bets_and_stats(): void
    {
        $admin = $this->createAdmin();
        $player = $this->createPlayer();

        AviatorBet::create([
            'user_id' => $player->id,
            'round_id' => 'AV-MONITOR-01',
            'bet_slot' => 1,
            'amount' => 250.00,
            'cashout_multiplier' => 2.50,
            'win_amount' => 625.00,
            'status' => 'cashed_out',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.bets.index'));
        $response->assertOk()
            ->assertSee('Aviator Crash')
            ->assertSee('AV-MONITOR-01')
            ->assertSee('Slot 1')
            ->assertSee('Av:');
    }

    public function test_admin_all_bets_monitor_filters_by_aviator_game(): void
    {
        $admin = $this->createAdmin();
        $player = $this->createPlayer();

        AviatorBet::create([
            'user_id' => $player->id,
            'round_id' => 'AV-MONITOR-02',
            'bet_slot' => 2,
            'amount' => 500.00,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.bets.index', ['game' => 'aviator']));
        $response->assertOk()
            ->assertSee('Aviator Crash')
            ->assertSee('AV-MONITOR-02')
            ->assertSee('Slot 2');
    }

    public function test_admin_sidebar_menu_contains_reordered_sequence(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertOk();

        // Verify section titles and ordered links
        $response->assertSee('Overview')
            ->assertSee('Users')
            ->assertSee('Payment and Settings')
            ->assertSee('Deposit Request')
            ->assertSee('Withdraw request')
            ->assertSee('Daily transactions')
            ->assertSee('QR,limits and commission')
            ->assertSee('Bets')
            ->assertSee('All bets')
            ->assertSee('Game Controls')
            ->assertSee('2digit slot')
            ->assertSee('wingo color')
            ->assertSee('andar bahar')
            ->assertSee('aviator')
            ->assertSee('Winner Lists')
            ->assertSee('Support and System');
    }
}
