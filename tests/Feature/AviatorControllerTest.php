<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\AviatorBet;
use App\Models\AviatorRound;
use App\Models\User;
use App\Services\AviatorEngine;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AviatorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-10-26 12:00:00'));

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

    private function createPlayer(float $balance = 1000.00): User
    {
        static $counter = 0;
        $counter++;

        return User::create([
            'name' => 'Controller Test Player '.$counter,
            'phone' => '981111'.str_pad((string) $counter, 4, '0', STR_PAD_LEFT),
            'email' => 'ctrl_player'.$counter.'@example.com',
            'password' => bcrypt('password123'),
            'balance' => $balance,
            'role' => 'user',
            'status' => 'active',
        ]);
    }

    public function test_aviator_page_renders_successfully(): void
    {
        $response = $this->get(route('aviator.index'));
        $response->assertOk()
            ->assertSee('AVIATOR')
            ->assertSee('flightCanvas');
    }

    public function test_get_live_status_returns_sync_data(): void
    {
        $response = $this->getJson(route('api.aviator.status'));

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'round_id',
                'status',
                'current_multiplier',
                'remaining_betting_seconds',
                'recent_multipliers',
                'balance',
            ]);
    }

    public function test_guest_cannot_place_bet(): void
    {
        $response = $this->postJson(route('api.aviator.place-bet'), [
            'bet_slot' => 1,
            'amount' => 100.00,
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_player_can_place_bet(): void
    {
        $player = $this->createPlayer(500.00);

        $response = $this->actingAs($player)->postJson(route('api.aviator.place-bet'), [
            'bet_slot' => 1,
            'amount' => 100.00,
            'auto_cashout' => 2.00,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'new_balance' => 400.00,
            ]);
    }

    public function test_player_can_cancel_bet_during_betting_phase(): void
    {
        $player = $this->createPlayer(500.00);
        $placeRes = AviatorEngine::placeBet($player, 1, 100.00);
        $betId = $placeRes['bet']->id;

        $response = $this->actingAs($player)->postJson(route('api.aviator.cancel-bet'), [
            'bet_id' => $betId,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'new_balance' => 500.00,
            ]);
    }

    public function test_player_can_cash_out_during_flight(): void
    {
        $player = $this->createPlayer(1000.00);
        $placeRes = AviatorEngine::placeBet($player, 1, 100.00);

        // Set safe crash point for test determinism
        $round = AviatorRound::where('round_id', $placeRes['bet']->round_id)->first();
        $round->update([
            'crash_multiplier' => 5.00,
            'crash_time' => $round->flight_start_time->copy()->addSeconds(25),
            'end_time' => $round->flight_start_time->copy()->addSeconds(28),
        ]);

        // Advance 10s into flight
        Carbon::setTestNow(Carbon::now()->addSeconds(10));

        $response = $this->actingAs($player)->postJson(route('api.aviator.cashout'), [
            'bet_slot' => 1,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);
        $this->assertGreaterThan(0, $response->json('win_amount'));
    }

    public function test_player_can_view_my_bets_and_history(): void
    {
        $player = $this->createPlayer(1000.00);
        AviatorEngine::placeBet($player, 1, 100.00);

        $response = $this->actingAs($player)->getJson(route('api.aviator.my-bets'));
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'bets',
                'stats' => ['total_bets', 'total_wagered', 'total_won', 'net_profit'],
            ]);

        $historyRes = $this->getJson(route('api.aviator.history'));
        $historyRes->assertOk()->assertJsonStructure(['success', 'rounds']);
    }

    public function test_my_bets_page_displays_aviator_bets_in_unified_view(): void
    {
        $player = $this->createPlayer(1000.00);
        $betRes = AviatorEngine::placeBet($player, 1, 150.00, 2.50);

        $response = $this->actingAs($player)->get(route('bets.history'));
        $response->assertOk()
            ->assertSee('Aviator Crash')
            ->assertSee('Panel #1')
            ->assertSee('150.00')
            ->assertSee('Details')
            ->assertSee('betDetailModal');
    }

    public function test_my_bets_page_filters_by_aviator_game_and_status(): void
    {
        $player = $this->createPlayer(1000.00);
        $round = AviatorRound::create([
            'round_id' => 'AVTEST99999',
            'start_time' => Carbon::now()->subMinutes(5),
            'flight_start_time' => Carbon::now()->subMinutes(5)->addSeconds(5),
            'crash_time' => Carbon::now()->subMinutes(4),
            'end_time' => Carbon::now()->subMinutes(4)->addSeconds(3),
            'crash_multiplier' => 4.50,
            'status' => 'completed',
            'is_manual' => false,
            'total_bets_count' => 1,
            'total_bet_amount' => 200.00,
            'total_payout_amount' => 380.00,
        ]);

        AviatorBet::create([
            'user_id' => $player->id,
            'round_id' => $round->round_id,
            'bet_slot' => 2,
            'amount' => 200.00,
            'auto_cashout' => 2.00,
            'cashout_multiplier' => 2.00,
            'commission_amount' => 20.00,
            'win_amount' => 380.00,
            'status' => 'cashed_out',
            'cashed_out_at' => Carbon::now()->subMinutes(4)->subSeconds(10),
        ]);

        $response = $this->actingAs($player)->get(route('bets.history', ['game' => 'aviator', 'status' => 'won']));
        $response->assertOk()
            ->assertSee('Aviator Crash')
            ->assertSee('Panel #2')
            ->assertSee('WON')
            ->assertSee('200.00');
    }
}
