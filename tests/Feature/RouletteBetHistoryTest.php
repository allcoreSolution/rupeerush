<?php

namespace Tests\Feature;

use App\Models\RouletteBet;
use App\Models\RouletteRound;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouletteBetHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_my_bets_displays_roulette_bets_in_unified_and_filtered_views(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'balance' => 5000.00,
        ]);

        $round = RouletteRound::create([
            'round_id' => 'ROU_TEST_'.time(),
            'start_time' => now(),
            'end_time' => now()->addSeconds(20),
            'status' => 'completed',
            'winning_number' => 17,
            'winning_color' => 'black',
            'is_even' => false,
            'is_high' => false,
            'dozen' => 2,
            'column' => 2,
            'seed_hash' => hash('sha256', 'seed'),
            'server_seed' => 'seed',
            'is_manual' => false,
            'total_bets_count' => 1,
            'total_bet_amount' => 100.00,
            'total_payout_amount' => 3600.00,
        ]);

        $rouletteBet = RouletteBet::create([
            'bet_id' => 'ROUB_TEST123',
            'round_id' => $round->round_id,
            'user_id' => $user->id,
            'bet_type' => 'straight',
            'bet_value' => '17',
            'bet_amount' => 100.00,
            'odds' => 36.00,
            'payout_amount' => 3600.00,
            'net_profit' => 3500.00,
            'status' => 'won',
        ]);

        // 1. Unified /my-bets test
        $response = $this->actingAs($user)->get(route('bets.history'));
        $response->assertStatus(200);
        $response->assertSee('Roulette 36x');
        $response->assertSee('Straight #17');
        $response->assertSee($round->round_id);
        $response->assertSee('3,600.00');

        // 2. Filtered /my-bets?game=roulette test
        $responseFiltered = $this->actingAs($user)->get(route('bets.history', ['game' => 'roulette']));
        $responseFiltered->assertStatus(200);
        $responseFiltered->assertSee('Roulette 36x');
        $responseFiltered->assertSee('Straight #17');
        $responseFiltered->assertSee($round->round_id);

        // 3. Status filtered /my-bets?game=roulette&status=won test
        $responseWon = $this->actingAs($user)->get(route('bets.history', ['game' => 'roulette', 'status' => 'won']));
        $responseWon->assertStatus(200);
        $responseWon->assertSee('Straight #17');

        // 4. Status filtered /my-bets?game=roulette&status=lost (should not see won bet in rows)
        $responseLost = $this->actingAs($user)->get(route('bets.history', ['game' => 'roulette', 'status' => 'lost']));
        $responseLost->assertStatus(200);
        $responseLost->assertSee('No bets found in this section');
    }
}
