<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\MinesGame;
use App\Models\User;
use App\Services\MinesEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MinesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_multiplier_ladder_calculation_matches_expected_values(): void
    {
        // 3 Mines ladder
        $ladder = MinesEngine::getMultiplierLadder(3, 0.03);

        $this->assertCount(22, $ladder);
        $this->assertEquals(1.10, $ladder[0]['multiplier']); // Step 1
        $this->assertEquals(1.26, $ladder[1]['multiplier']); // Step 2
        $this->assertEquals(1.45, $ladder[2]['multiplier']); // Step 3
        $this->assertEquals(1.68, $ladder[3]['multiplier']); // Step 4
        $this->assertEquals(1.96, $ladder[4]['multiplier']); // Step 5
    }

    public function test_user_can_start_game_and_reveal_and_cashout(): void
    {
        $user = User::factory()->create([
            'balance' => 500.00,
        ]);

        // Start game with ₹50 and 3 mines
        $start = MinesEngine::startGame($user, 50.00, 3);
        $this->assertTrue($start['success']);
        $this->assertEquals(450.00, $start['new_balance']);
        $this->assertNull($start['game']['mine_positions']); // Hidden while active

        $game = MinesGame::where('user_id', $user->id)->where('status', 'active')->first();
        $this->assertNotNull($game);

        // Pick a guaranteed safe tile
        $safeIndices = array_diff(range(0, 24), $game->mine_positions);
        $safeTile = reset($safeIndices);

        $rev = MinesEngine::revealTile($user, $safeTile);
        $this->assertTrue($rev['success']);
        $this->assertFalse($rev['hit_mine']);
        $this->assertEquals(1, $rev['step']);
        $this->assertEquals(1.10, $rev['current_multiplier']);

        // Cash out
        $cash = MinesEngine::cashOut($user);
        $this->assertTrue($cash['success']);
        $this->assertEquals('cashed_out', $cash['status']);
        $this->assertNotNull($cash['game']['mine_positions']); // Revealed after cashout

        $user->refresh();
        $this->assertGreaterThan(450.00, (float) $user->balance);
    }

    public function test_hitting_mine_explodes_game(): void
    {
        $user = User::factory()->create([
            'balance' => 200.00,
        ]);

        $start = MinesEngine::startGame($user, 20.00, 5);
        $this->assertTrue($start['success']);

        $game = MinesGame::where('user_id', $user->id)->where('status', 'active')->first();
        $mineTile = $game->mine_positions[0];

        $rev = MinesEngine::revealTile($user, $mineTile);
        $this->assertTrue($rev['success']);
        $this->assertTrue($rev['hit_mine']);
        $this->assertEquals('exploded', $rev['status']);

        $game->refresh();
        $this->assertEquals('exploded', $game->status);
    }

    public function test_mines_page_renders_successfully(): void
    {
        $response = $this->get('/mines');
        $response->assertStatus(200);
        $response->assertSee('MINES');
        $response->assertSee('START BET');
    }

    public function test_mines_api_endpoints_work(): void
    {
        $response = $this->get('/api/mines/ladder?mines=3');
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'mines_count' => 3]);

        $statusRes = $this->get('/api/mines/status');
        $statusRes->assertStatus(200);
        $statusRes->assertJson(['success' => true]);
    }

    public function test_admin_can_access_mines_hub_and_update_settings(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.mines.index'));
        $response->assertStatus(200);
        $response->assertSee('Mines 5x5 Live Control Hub');

        $updateResponse = $this->actingAs($admin)->post(route('admin.mines.settings'), [
            'mines_min_bet' => 2.00,
            'mines_max_bet' => 30000.00,
            'mines_house_edge' => 5.00,
            'mines_jackpot_min_bet' => 20.00,
            'mines_jackpot_pool' => 2500.00,
            'mines_control_mode' => 'house_trap',
            'mines_rigged_rate' => 35.00,
            'mines_force_blast_step' => 4,
            'mines_max_win_multiplier' => 8.00,
        ]);

        $updateResponse->assertRedirect(route('admin.mines.index'));
        $setting = AdminSetting::current();
        $this->assertEquals('house_trap', $setting->mines_control_mode);
        $this->assertEquals(5.00, (float) $setting->mines_house_edge);
        $this->assertEquals(35.00, (float) $setting->mines_rigged_rate);
    }

    public function test_admin_can_force_bust_active_game(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $player = User::factory()->create(['balance' => 200.00]);

        $start = MinesEngine::startGame($player, 50.00, 3);
        $this->assertTrue($start['success']);
        $game = MinesGame::where('user_id', $player->id)->where('status', 'active')->first();

        $response = $this->actingAs($admin)->post(route('admin.mines.force-bust', $game->id));
        $response->assertRedirect(route('admin.mines.index'));

        $game->refresh();
        $this->assertEquals('exploded', $game->status);
    }

    public function test_step_limit_control_mode_triggers_blast(): void
    {
        AdminSetting::current()->update([
            'mines_control_mode' => 'step_limit',
            'mines_force_blast_step' => 1, // Max 1 safe coin allowed, 2nd click forces blast
        ]);

        $player = User::factory()->create(['balance' => 200.00]);
        $start = MinesEngine::startGame($player, 50.00, 3);
        $game = MinesGame::where('user_id', $player->id)->first();

        // 1st click safe
        $safeTiles = array_values(array_diff(range(0, 24), $game->mine_positions));
        $rev1 = MinesEngine::revealTile($player, $safeTiles[0]);
        $this->assertFalse($rev1['hit_mine']);

        // 2nd click: even if tile was originally safe, step_limit mode forces blast!
        $rev2 = MinesEngine::revealTile($player, $safeTiles[1]);
        $this->assertTrue($rev2['hit_mine']);
        $this->assertEquals('exploded', $rev2['status']);
    }
}
