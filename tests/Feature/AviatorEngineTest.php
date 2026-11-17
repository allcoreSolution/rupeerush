<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\AviatorRound;
use App\Models\User;
use App\Services\AviatorEngine;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AviatorEngineTest extends TestCase
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
            'name' => 'Aviator Player '.$counter,
            'phone' => '987000'.str_pad((string) $counter, 4, '0', STR_PAD_LEFT),
            'email' => 'aviator_player'.$counter.'@example.com',
            'password' => bcrypt('password123'),
            'balance' => $balance,
            'role' => 'user',
            'status' => 'active',
        ]);
    }

    public function test_it_creates_and_returns_active_betting_round(): void
    {
        $roundData = AviatorEngine::getCurrentRound();

        $this->assertEquals('betting', $roundData['status']);
        $this->assertEquals(1.00, $roundData['current_multiplier']);
        $this->assertGreaterThan(0, $roundData['remaining_betting_seconds']);
        $this->assertDatabaseHas('aviator_rounds', [
            'round_id' => $roundData['round_id'],
            'status' => 'betting',
        ]);
    }

    public function test_it_places_bet_successfully_and_deducts_balance(): void
    {
        $player = $this->createPlayer(500.00);

        $res = AviatorEngine::placeBet($player, 1, 100.00, 2.50);

        $this->assertTrue($res['success']);
        $this->assertEquals(400.00, $res['new_balance']);
        $this->assertDatabaseHas('aviator_bets', [
            'user_id' => $player->id,
            'bet_slot' => 1,
            'amount' => 100.00,
            'auto_cashout' => 2.50,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $player->id,
            'type' => 'bet_placed',
            'amount' => 100.00,
            'balance_after' => 400.00,
        ]);
    }

    public function test_it_rejects_bet_when_balance_is_insufficient(): void
    {
        $player = $this->createPlayer(50.00);

        $res = AviatorEngine::placeBet($player, 1, 100.00);

        $this->assertFalse($res['success']);
        $this->assertStringContainsString('Insufficient wallet balance', $res['message']);
    }

    public function test_it_cancels_pending_bet_and_refunds_balance(): void
    {
        $player = $this->createPlayer(500.00);
        $placeRes = AviatorEngine::placeBet($player, 1, 100.00);
        $betId = $placeRes['bet']->id;

        $cancelRes = AviatorEngine::cancelBet($player, $betId);

        $this->assertTrue($cancelRes['success']);
        $this->assertEquals(500.00, $cancelRes['new_balance']);
        $this->assertDatabaseHas('aviator_bets', [
            'id' => $betId,
            'status' => 'cancelled',
        ]);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $player->id,
            'type' => 'admin_adjustment',
            'amount' => 100.00,
            'balance_after' => 500.00,
        ]);
    }

    public function test_it_cashes_out_during_flight_and_credits_payout(): void
    {
        $player = $this->createPlayer(1000.00);
        $placeRes = AviatorEngine::placeBet($player, 1, 100.00);
        $this->assertTrue($placeRes['success']);

        // Set safe crash point for test determinism
        $round = AviatorRound::where('round_id', $placeRes['bet']->round_id)->first();
        $round->update([
            'is_manual' => true,
            'crash_multiplier' => 5.00,
            'crash_time' => $round->flight_start_time->copy()->addSeconds(25),
            'end_time' => $round->flight_start_time->copy()->addSeconds(28),
        ]);

        // Advance time into flight phase: betting duration is 6s, let's advance 10s (4s flight)
        Carbon::setTestNow(Carbon::now()->addSeconds(10));

        $roundData = AviatorEngine::getCurrentRound();
        $this->assertEquals('flying', $roundData['status']);
        $currentMultiplier = $roundData['current_multiplier'];
        $this->assertGreaterThan(1.00, $currentMultiplier);

        $cashoutRes = AviatorEngine::cashOut($player, 1);
        $this->assertTrue($cashoutRes['success']);
        $this->assertGreaterThan(0, $cashoutRes['win_amount']);

        $this->assertDatabaseHas('aviator_bets', [
            'id' => $placeRes['bet']->id,
            'status' => 'cashed_out',
        ]);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $player->id,
            'type' => 'bet_won',
        ]);
    }

    public function test_it_marks_uncashed_bets_as_lost_on_crash(): void
    {
        $player = $this->createPlayer(1000.00);
        AviatorEngine::placeBet($player, 1, 100.00);

        // Advance time past flight and crash: 60s ahead
        Carbon::setTestNow(Carbon::now()->addSeconds(60));

        $roundData = AviatorEngine::getCurrentRound();

        $this->assertDatabaseHas('aviator_bets', [
            'user_id' => $player->id,
            'status' => 'lost',
            'win_amount' => 0.00,
        ]);
    }

    public function test_it_calculates_flight_duration_and_multiplier_curves_accurately(): void
    {
        $duration = AviatorEngine::calculateFlightDuration(2.00);
        $this->assertGreaterThan(5.0, $duration);
        $this->assertLessThan(15.0, $duration);

        $multiplier = AviatorEngine::calculateMultiplierAtTime(0.0, 5.00);
        $this->assertEquals(1.00, $multiplier);

        $multiplier5s = AviatorEngine::calculateMultiplierAtTime(5.0, 5.00);
        $this->assertGreaterThan(1.20, $multiplier5s);
    }
}
