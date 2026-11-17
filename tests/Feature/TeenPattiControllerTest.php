<?php

namespace Tests\Feature;

use App\Models\TeenPattiBet;
use App\Models\TeenPattiRound;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeenPattiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2026, 10, 27, 12, 0, 10, 'Asia/Kolkata'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_teen_patti_page_renders_successfully(): void
    {
        $res = $this->get('/teen-patti');
        $res->assertStatus(200);
        $res->assertSee('20-20 TEEN PATTI');
    }

    public function test_live_status_endpoint_returns_json(): void
    {
        $res = $this->getJson('/api/teen-patti/status');
        $res->assertStatus(200);
        $res->assertJsonStructure([
            'success',
            'round_id',
            'duration',
            'remaining_seconds',
            'is_locked',
            'status',
            'roadmap',
        ]);
        $this->assertTrue($res->json('success'));
    }

    public function test_guest_cannot_place_bet(): void
    {
        $res = $this->postJson('/api/teen-patti/place-bet', [
            'bet_type' => 'player_a',
            'amount' => 50.00,
        ]);

        $res->assertStatus(401);
    }

    public function test_authenticated_user_can_place_bet(): void
    {
        $user = User::factory()->create([
            'balance' => 300.00,
        ]);

        $res = $this->actingAs($user)->postJson('/api/teen-patti/place-bet', [
            'bet_type' => 'player_a',
            'amount' => 50.00,
        ]);

        $res->assertStatus(200);
        $res->assertJson([
            'success' => true,
            'new_balance' => 250.00,
        ]);

        $user->refresh();
        $this->assertEquals(250.00, (float) $user->balance);
        $this->assertDatabaseHas('teen_patti_bets', [
            'user_id' => $user->id,
            'bet_type' => 'player_a',
            'bet_amount' => 50.00,
        ]);
    }

    public function test_bet_fails_with_insufficient_balance(): void
    {
        $user = User::factory()->create([
            'balance' => 20.00,
        ]);

        $res = $this->actingAs($user)->postJson('/api/teen-patti/place-bet', [
            'bet_type' => 'player_b',
            'amount' => 100.00,
        ]);

        $res->assertStatus(422);
        $res->assertJson([
            'success' => false,
        ]);
    }

    public function test_bet_fails_with_invalid_bet_type(): void
    {
        $user = User::factory()->create([
            'balance' => 500.00,
        ]);

        $res = $this->actingAs($user)->postJson('/api/teen-patti/place-bet', [
            'bet_type' => 'invalid_side',
            'amount' => 50.00,
        ]);

        $res->assertStatus(422);
    }

    public function test_my_bets_returns_user_bet_history(): void
    {
        $user = User::factory()->create(['balance' => 500.00]);

        $round = TeenPattiRound::create([
            'round_id' => 'TPTEST_HISTORY_1',
            'status' => 'completed',
            'winner' => 'player_a',
        ]);

        TeenPattiBet::create([
            'bet_id' => 'BET_HIST_1',
            'round_id' => $round->round_id,
            'user_id' => $user->id,
            'bet_type' => 'player_a',
            'bet_amount' => 50.00,
            'odds' => 1.95,
            'status' => 'won',
            'payout_amount' => 97.50,
        ]);

        $res = $this->actingAs($user)->getJson('/api/teen-patti/my-bets');
        $res->assertStatus(200);
        $res->assertJsonStructure([
            'success',
            'bets',
            'balance',
        ]);
        $this->assertCount(1, $res->json('bets'));
        $this->assertEquals('BET_HIST_1', $res->json('bets.0.bet_id'));
    }

    public function test_history_endpoint_returns_completed_rounds(): void
    {
        TeenPattiRound::create([
            'round_id' => 'TPTEST_ROUNDS_1',
            'status' => 'completed',
            'winner' => 'player_b',
        ]);

        $res = $this->getJson('/api/teen-patti/history');
        $res->assertStatus(200);
        $res->assertJsonStructure([
            'success',
            'history',
        ]);
        $this->assertNotEmpty($res->json('history'));
    }

    public function test_teen_patti_bets_displayed_on_unified_and_filtered_my_bets_hub(): void
    {
        $user = User::factory()->create(['balance' => 500.00]);
        $round = TeenPattiRound::create([
            'round_id' => 'TP_HUB_9999',
            'status' => 'completed',
            'winner' => 'player_a',
            'winning_rank_title' => 'Trio AAA',
        ]);

        TeenPattiBet::create([
            'bet_id' => 'BET_HUB_123',
            'round_id' => $round->round_id,
            'user_id' => $user->id,
            'bet_type' => 'player_a',
            'bet_amount' => 200.00,
            'odds' => 1.95,
            'status' => 'won',
            'payout_amount' => 390.00,
        ]);

        // 1. Unified My Bets Hub
        $resUnified = $this->actingAs($user)->get(route('bets.history'));
        $resUnified->assertOk()
            ->assertSee('TP_HUB_9999')
            ->assertSee('Teen Patti')
            ->assertSee('Player A');

        // 2. Filtered by game=teen_patti
        $resFiltered = $this->actingAs($user)->get(route('bets.history', ['game' => 'teen_patti']));
        $resFiltered->assertOk()
            ->assertSee('TP_HUB_9999')
            ->assertSee('Teen Patti')
            ->assertSee('390.00');

        // 3. Status filter won
        $resStatusWon = $this->actingAs($user)->get(route('bets.history', ['game' => 'teen_patti', 'status' => 'won']));
        $resStatusWon->assertOk()
            ->assertSee('TP_HUB_9999');

        // 4. Status filter lost (should not see won bet)
        $resStatusLost = $this->actingAs($user)->get(route('bets.history', ['game' => 'teen_patti', 'status' => 'lost']));
        $resStatusLost->assertOk()
            ->assertDontSee('TP_HUB_9999');
    }
}
