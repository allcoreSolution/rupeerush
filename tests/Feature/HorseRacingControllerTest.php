<?php

namespace Tests\Feature;

use App\Models\HorseRaceBet;
use App\Models\User;
use App\Services\HorseRacingEngine;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HorseRacingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $player;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2026, 10, 29, 12, 0, 10, 'Asia/Kolkata'));

        $this->player = User::factory()->create([
            'name' => 'Derby Player',
            'phone' => '9876501234',
            'balance' => 8000.00,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_player_can_load_horse_racing_arena_page(): void
    {
        $response = $this->actingAs($this->player)->get(route('horse-racing.index'));

        $response->assertStatus(200);
        $response->assertSee('CHAMPION DERBY LIVE');
        $response->assertSee('raceCanvas');
        $response->assertSee('Thunder Bolt');
        $response->assertSee('Golden Mane');
        $response->assertSee('Gate 1');
    }

    public function test_live_status_api_returns_sync_data_and_waypoints(): void
    {
        $response = $this->actingAs($this->player)->getJson(route('api.horse-racing.status'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'round_id',
            'status',
            'remaining_seconds',
            'racing_countdown',
            'is_locked',
            'duration',
            'seed_hash',
            'horses',
            'active_race',
            'last_round',
            'history',
            'my_bets',
            'balance',
        ]);
        $this->assertTrue($response->json('success'));
        $this->assertCount(6, $response->json('horses'));
    }

    public function test_authenticated_player_can_place_batch_bets_via_api(): void
    {
        $roundData = HorseRacingEngine::getCurrentRound();
        $round = $roundData['round_model'];
        $round->status = 'betting';
        $round->save();

        $payload = [
            'bets' => [
                ['horse_id' => 1, 'bet_type' => 'win', 'amount' => 100],
                ['horse_id' => 2, 'bet_type' => 'place', 'amount' => 200],
            ],
        ];

        $response = $this->actingAs($this->player)->postJson(route('api.horse-racing.place-bet'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Bets placed successfully!',
            'new_balance' => 7700.00,
            'total_stake' => 300.00,
        ]);

        $this->assertDatabaseHas('horse_race_bets', [
            'user_id' => $this->player->id,
            'horse_id' => 1,
            'bet_type' => 'win',
            'bet_amount' => 100.00,
        ]);
    }

    public function test_authenticated_player_can_place_single_bet_shortcut(): void
    {
        $roundData = HorseRacingEngine::getCurrentRound();
        $round = $roundData['round_model'];
        $round->status = 'betting';
        $round->save();

        $payload = [
            'horse_id' => 3,
            'bet_type' => 'show',
            'amount' => 150,
        ];

        $response = $this->actingAs($this->player)->postJson(route('api.horse-racing.place-bet'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Bets placed successfully!',
            'new_balance' => 7850.00,
        ]);
    }

    public function test_unauthenticated_user_cannot_place_bets(): void
    {
        $payload = [
            'bets' => [
                ['horse_id' => 1, 'bet_type' => 'win', 'amount' => 100],
            ],
        ];

        $response = $this->postJson(route('api.horse-racing.place-bet'), $payload);

        $response->assertStatus(401);
        $response->assertJson(['success' => false]);
    }

    public function test_player_can_fetch_their_recent_bets_via_api(): void
    {
        HorseRaceBet::create([
            'bet_id' => 'HRB_TEST_RECENT_01',
            'round_id' => 'HR26102900001',
            'user_id' => $this->player->id,
            'horse_id' => 1,
            'horse_name' => 'Thunder Bolt',
            'bet_type' => 'win',
            'bet_amount' => 250.00,
            'odds' => 2.60,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->player)->getJson(route('api.horse-racing.my-bets'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertNotEmpty($response->json('bets'));
        $this->assertEquals('HRB_TEST_RECENT_01', $response->json('bets.0.bet_id'));
    }

    public function test_horse_racing_bets_appear_in_unified_my_bets_page(): void
    {
        HorseRaceBet::create([
            'bet_id' => 'HRB_TEST_UNIFIED_01',
            'round_id' => 'HR26102900002',
            'user_id' => $this->player->id,
            'horse_id' => 2,
            'horse_name' => 'Golden Mane',
            'bet_type' => 'win',
            'bet_amount' => 500.00,
            'odds' => 3.40,
            'status' => 'won',
            'payout_amount' => 1700.00,
        ]);

        // Load /my-bets with horse_racing filter
        $response = $this->actingAs($this->player)->get(route('bets.history', ['game' => 'horse_racing']));
        $response->assertStatus(200);
        $response->assertSee('Golden Mane');

        // Load /my-bets with unified all games
        $responseUnified = $this->actingAs($this->player)->get(route('bets.history'));
        $responseUnified->assertStatus(200);
        $responseUnified->assertSee('Horse Racing Live');
    }

    public function test_lobby_showcases_horse_racing_as_active_live_game(): void
    {
        $response = $this->actingAs($this->player)->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Horse Racing Derby');
        $response->assertSee(route('horse-racing.index'));
    }
}
