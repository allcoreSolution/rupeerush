<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VortexGame;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VortexControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_vortex_arena_page_renders_successfully(): void
    {
        $response = $this->get('/vortex');
        $response->assertStatus(200);
        $response->assertSee('VORTEX');
        $response->assertSee('OFFICIAL GAME');
    }

    public function test_vortex_status_api_returns_success_and_rings_config(): void
    {
        $response = $this->getJson(route('api.vortex.status'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'balance',
            'active_game',
            'min_bet',
            'max_bet',
            'rings_config',
        ]);
    }

    public function test_unauthorized_user_cannot_start_game(): void
    {
        $response = $this->postJson(route('api.vortex.start'), ['amount' => 10.00]);
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_start_spin_and_cashout_via_api(): void
    {
        $user = User::factory()->create([
            'balance' => 500.00,
        ]);

        // 1. Start game
        $startRes = $this->actingAs($user)->postJson(route('api.vortex.start'), ['amount' => 50.00]);
        $startRes->assertStatus(200);
        $startRes->assertJson([
            'success' => true,
            'new_balance' => 450.00,
        ]);

        // 2. Spin active ring
        $spinRes = $this->actingAs($user)->postJson(route('api.vortex.spin'));
        $spinRes->assertStatus(200);
        $spinRes->assertJsonStructure([
            'success',
            'result',
            'status',
            'spin',
            'game',
        ]);

        $game = VortexGame::where('user_id', $user->id)->first();

        // If game is still active, test cashout
        if ($game->status === 'active') {
            $cashRes = $this->actingAs($user)->postJson(route('api.vortex.cashout'));
            $cashRes->assertStatus(200);
            $cashRes->assertJson([
                'success' => true,
                'status' => 'cashed_out',
            ]);
        }
    }

    public function test_authenticated_user_can_fetch_history_via_api(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('api.vortex.history'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'history',
        ]);
    }

    public function test_user_can_view_vortex_bets_in_my_bets_hub(): void
    {
        $user = User::factory()->create([
            'balance' => 1000.00,
        ]);

        // Create a winning Vortex game
        $wonGame = VortexGame::create([
            'game_id' => 'VTX-WIN-999',
            'user_id' => $user->id,
            'bet_amount' => 100.00,
            'payout_amount' => 350.00,
            'win_amount' => 350.00,
            'current_ring' => 2,
            'current_multiplier' => 3.50,
            'status' => 'cashed_out',
            'part_cashed_out' => true,
            'part_cashout_amount' => 50.00,
            'spin_count' => 4,
            'history' => [],
        ]);

        // 1. Unified My-Bets page
        $resUnified = $this->actingAs($user)->get(route('bets.history'));
        $resUnified->assertOk()
            ->assertSee('VTX-WIN-999')
            ->assertSee('Vortex Wheel')
            ->assertSee('3.50x');

        // 2. Filtered by game=vortex
        $resFiltered = $this->actingAs($user)->get(route('bets.history', ['game' => 'vortex']));
        $resFiltered->assertOk()
            ->assertSee('VTX-WIN-999')
            ->assertSee('Vortex Wheel')
            ->assertSee('3.50x');

        // 3. Status filter won
        $resStatusWon = $this->actingAs($user)->get(route('bets.history', ['game' => 'vortex', 'status' => 'won']));
        $resStatusWon->assertOk()
            ->assertSee('VTX-WIN-999');

        // 4. Status filter lost (should not see won game)
        $resStatusLost = $this->actingAs($user)->get(route('bets.history', ['game' => 'vortex', 'status' => 'lost']));
        $resStatusLost->assertOk()
            ->assertDontSee('VTX-WIN-999');
    }
}
