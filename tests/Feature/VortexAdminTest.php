<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\GameSlot;
use App\Models\User;
use App\Models\VortexGame;
use App\Services\VortexEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VortexAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (GameSlot::count() === 0) {
            GameSlot::create([
                'slot_time' => '10:00 AM',
                'display_order' => 1,
                'interval_minutes' => 20,
                'is_active' => true,
            ]);
        }
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
            'email' => 'player@example.com',
            'password' => bcrypt('password123'),
            'balance' => 500.00,
            'role' => 'user',
            'status' => 'active',
        ]);
    }

    public function test_non_admin_cannot_access_vortex_admin_panel(): void
    {
        $player = $this->createPlayer();

        $response = $this->actingAs($player)->get(route('admin.vortex.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_vortex_control_hub(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.vortex.index'));
        $response->assertOk()
            ->assertSee('Vortex Concentric Wheel Live Control Hub')
            ->assertSee('Active Sessions')
            ->assertSee('Total Wagered');
    }

    public function test_admin_can_update_vortex_settings(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.vortex.settings'), [
            'vortex_min_bet' => 2.00,
            'vortex_max_bet' => 15000.00,
            'vortex_house_edge' => 5.50,
            'vortex_control_mode' => 'house_trap',
            'vortex_rigged_rate' => 45.00,
            'vortex_max_multiplier_cap' => 300.00,
        ]);

        $response->assertRedirect(route('admin.vortex.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('admin_settings', [
            'vortex_min_bet' => 2.00,
            'vortex_max_bet' => 15000.00,
            'vortex_house_edge' => 5.50,
            'vortex_control_mode' => 'house_trap',
            'vortex_rigged_rate' => 45.00,
            'vortex_max_multiplier_cap' => 300.00,
        ]);
    }

    public function test_admin_can_force_bust_active_game(): void
    {
        $admin = $this->createAdmin();
        $player = $this->createPlayer();

        $start = VortexEngine::startGame($player, 100.00);
        $gameId = $start['game']['id'];

        $response = $this->actingAs($admin)->post(route('admin.vortex.force-bust', $gameId));
        $response->assertRedirect(route('admin.vortex.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('vortex_games', [
            'id' => $gameId,
            'status' => 'busted',
        ]);
    }

    public function test_admin_can_force_cashout_active_game(): void
    {
        AdminSetting::current()->update(['win_commission_percent' => 0.00]);

        $admin = $this->createAdmin();
        $player = $this->createPlayer();

        $start = VortexEngine::startGame($player, 100.00);
        $game = VortexGame::find($start['game']['id']);
        $game->update([
            'current_multiplier' => 2.50,
            'payout_amount' => 250.00,
            'spin_history' => [
                ['spin_number' => 1, 'ring' => 1, 'sector_multiplier' => 2.50],
            ],
        ]);

        $response = $this->actingAs($admin)->post(route('admin.vortex.force-cashout', $game->id));
        $response->assertRedirect(route('admin.vortex.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('vortex_games', [
            'id' => $game->id,
            'status' => 'cashed_out',
            'win_amount' => 250.00,
        ]);

        $player->refresh();
        $this->assertEquals(650.00, $player->balance); // 500 - 100 + 250
    }

    public function test_admin_can_view_vortex_winners_page(): void
    {
        $admin = $this->createAdmin();
        $player = $this->createPlayer();

        // Create a cashed out winning game
        VortexGame::create([
            'game_id' => 'VTX-TESTWIN',
            'user_id' => $player->id,
            'bet_amount' => 50.00,
            'payout_amount' => 500.00,
            'win_amount' => 500.00,
            'current_ring' => 3,
            'current_multiplier' => 10.00,
            'status' => 'cashed_out',
            'part_cashed_out' => false,
            'part_cashout_amount' => 0.00,
            'spin_count' => 5,
            'history' => [],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.vortex.winners'));
        $response->assertOk()
            ->assertSee('Vortex Concentric Wheel Winner List')
            ->assertSee('VTX-TESTWIN')
            ->assertSee('10.00x');
    }
}
