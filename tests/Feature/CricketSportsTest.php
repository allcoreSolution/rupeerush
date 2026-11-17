<?php

namespace Tests\Feature;

use App\Models\SportsBet;
use App\Models\SportsMarket;
use App\Models\SportsMatch;
use App\Models\SportsRunner;
use App\Models\User;
use App\Services\Sports\CricketEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CricketSportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_cricket_lobby_page_is_accessible(): void
    {
        $response = $this->get(route('sports.cricket'));
        $response->assertStatus(200);
        $response->assertSee('Cricket Live Sports Arena');
    }

    public function test_cricket_api_returns_matches_and_markets(): void
    {
        $response = $this->getJson(route('api.sports.cricket.matches'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'matches',
            'server_time',
        ]);
        $response->assertJson([
            'success' => true,
        ]);
    }

    public function test_user_can_place_ball_or_match_bet(): void
    {
        $user = User::factory()->create(['balance' => 1000]);

        // Sync matches
        $engine = app(CricketEngine::class);
        $engine->syncMatches();

        $runner = SportsRunner::where('status', 'active')
            ->whereHas('market', function ($q) {
                $q->where('status', 'active')
                    ->whereHas('match', function ($m) {
                        $m->whereNotIn('status', ['completed', 'cancelled']);
                    });
            })
            ->first();
        $this->assertNotNull($runner, 'Active sports runner not found in database.');

        $response = $this->actingAs($user)->postJson(route('api.sports.cricket.place-bet'), [
            'runner_id' => $runner->id,
            'stake' => 100,
            'bet_type' => 'back',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify balance deducted
        $user->refresh();
        $this->assertEquals(900, (float) $user->balance);

        // Verify SportsBet record
        $this->assertDatabaseHas('sports_bets', [
            'user_id' => $user->id,
            'runner_id' => $runner->id,
            'stake' => 100,
            'status' => 'pending',
        ]);
    }

    public function test_market_settlement_pays_winning_bets(): void
    {
        $user = User::factory()->create(['balance' => 500]);
        $engine = app(CricketEngine::class);

        $match = SportsMatch::create([
            'title' => 'Test IND vs AUS',
            'team_a' => 'India',
            'team_b' => 'Australia',
            'status' => 'live',
            'is_live' => true,
        ]);

        $market = SportsMarket::create([
            'match_id' => $match->id,
            'market_type' => 'next_ball',
            'market_name' => 'Next Ball Outcome (Ball 1.1)',
            'status' => 'active',
        ]);

        $runnerWinner = SportsRunner::create([
            'market_id' => $market->id,
            'runner_name' => 'Boundary 4',
            'back_odds' => 4.00,
            'lay_odds' => 4.20,
            'status' => 'active',
        ]);

        $runnerLoser = SportsRunner::create([
            'market_id' => $market->id,
            'runner_name' => '1 Run',
            'back_odds' => 2.20,
            'lay_odds' => 2.40,
            'status' => 'active',
        ]);

        // Place bet on winning runner
        $engine->placeBet($user, $runnerWinner->id, 100, 'back');
        $user->refresh();
        $this->assertEquals(400, (float) $user->balance);

        // Settle market with winning runner
        $settleResult = $engine->settleMarket($market->id, $runnerWinner->id);
        $this->assertTrue($settleResult['success']);

        // Check user won: Stake 100 * 4.00 = 400 gross. Profit 300 - 10% fee (30) = 270 net profit. Total payout 370.
        // Balance before: 400 + 370 = 770
        $user->refresh();
        $this->assertEquals(770.00, (float) $user->balance);

        $this->assertDatabaseHas('sports_bets', [
            'user_id' => $user->id,
            'runner_id' => $runnerWinner->id,
            'status' => 'won',
            'win_amount' => 370.00,
            'commission_amount' => 30.00,
        ]);
    }
}
