<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\HorseRaceBet;
use App\Models\HorseRaceRound;
use App\Models\Transaction;
use App\Models\User;
use App\Services\HorseRacingEngine;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HorseRacingEngineTest extends TestCase
{
    use RefreshDatabase;

    protected User $player;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2026, 10, 29, 12, 0, 10, 'Asia/Kolkata'));

        $this->player = User::factory()->create([
            'name' => 'Race Better',
            'phone' => '9876543210',
            'balance' => 5000.00,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_get_current_round_initializes_active_round_with_six_horses(): void
    {
        $roundData = HorseRacingEngine::getCurrentRound();

        $this->assertNotEmpty($roundData['round_id']);
        $this->assertEquals(60, $roundData['duration']);
        $this->assertCount(6, $roundData['horses']);
        $this->assertNotEmpty($roundData['seed_hash']);
        $this->assertArrayHasKey('remaining_seconds', $roundData);

        // Verify Horse 1 metadata
        $horse1 = $roundData['horses'][0];
        $this->assertEquals(1, $horse1['id']);
        $this->assertEquals('Thunder Bolt', $horse1['name']);
        $this->assertGreaterThan(1.0, $horse1['win_odds']);
        $this->assertGreaterThan(1.0, $horse1['place_odds']);
        $this->assertGreaterThan(1.0, $horse1['show_odds']);

        $dbRound = HorseRaceRound::where('round_id', $roundData['round_id'])->first();
        $this->assertNotNull($dbRound);
        $this->assertCount(6, $dbRound->horses_data);
    }

    public function test_user_can_place_win_place_and_show_bets_and_balance_deducted(): void
    {
        $roundData = HorseRacingEngine::getCurrentRound();
        $round = $roundData['round_model'];
        $round->status = 'betting';
        $round->save();

        $betsPayload = [
            ['horse_id' => 1, 'bet_type' => 'win', 'amount' => 100],
            ['horse_id' => 2, 'bet_type' => 'place', 'amount' => 150],
            ['horse_id' => 3, 'bet_type' => 'show', 'amount' => 50],
        ];

        $res = HorseRacingEngine::placeBets($this->player, $betsPayload);

        $this->assertTrue($res['success']);
        $this->assertEquals(4700.00, $res['new_balance']);
        $this->assertEquals(300.00, $res['total_stake']);

        $this->player->refresh();
        $this->assertEquals(4700.00, (float) $this->player->balance);

        // Verify Bet records created
        $bets = HorseRaceBet::where('user_id', $this->player->id)->get();
        $this->assertCount(3, $bets);

        // Verify Transaction record created
        $tx = Transaction::where('user_id', $this->player->id)
            ->where('type', 'bet_placed')
            ->first();
        $this->assertNotNull($tx);
        $this->assertEquals(300.00, (float) $tx->amount);
        $this->assertEquals(5000.00, (float) $tx->balance_before);
        $this->assertEquals(4700.00, (float) $tx->balance_after);
    }

    public function test_bet_placement_fails_if_insufficient_wallet_balance(): void
    {
        $roundData = HorseRacingEngine::getCurrentRound();
        $round = $roundData['round_model'];
        $round->status = 'betting';
        $round->save();

        $betsPayload = [
            ['horse_id' => 1, 'bet_type' => 'win', 'amount' => 10000.00],
        ];

        $res = HorseRacingEngine::placeBets($this->player, $betsPayload);

        $this->assertFalse($res['success']);
        $this->assertStringContainsString('Insufficient wallet balance', $res['message']);
        $this->assertEquals(5000.00, (float) $this->player->fresh()->balance);
    }

    public function test_bet_placement_rejected_when_round_is_racing(): void
    {
        $roundData = HorseRacingEngine::getCurrentRound();
        $round = $roundData['round_model'];
        $round->status = 'racing';
        $round->save();

        $betsPayload = [
            ['horse_id' => 1, 'bet_type' => 'win', 'amount' => 100],
        ];

        $res = HorseRacingEngine::placeBets($this->player, $betsPayload);

        $this->assertFalse($res['success']);
        $this->assertStringContainsString('Bets are closed', $res['message']);
    }

    public function test_settle_round_evaluates_payouts_and_credits_winners(): void
    {
        $round = HorseRaceRound::create([
            'round_id' => 'HRTEST_SETTLE_01',
            'start_time' => now()->subMinute(),
            'end_time' => now(),
            'status' => 'racing',
            'horses_data' => HorseRacingEngine::getHorsesRoster('testseed123'),
            'seed_hash' => hash('sha256', 'testseed123'),
            'server_seed' => 'testseed123',
            'is_manual' => true,
            'manual_winner_id' => 2, // Force Horse 2 as winner
        ]);

        // Place bets:
        // 1. Win on Horse 2 (WON)
        $bet1 = HorseRaceBet::create([
            'bet_id' => 'HRB_TEST_01',
            'round_id' => $round->round_id,
            'user_id' => $this->player->id,
            'horse_id' => 2,
            'horse_name' => 'Golden Mane',
            'bet_type' => 'win',
            'bet_amount' => 100.00,
            'odds' => 3.40,
            'status' => 'pending',
        ]);

        // 2. Win on Horse 1 (LOST)
        $bet2 = HorseRaceBet::create([
            'bet_id' => 'HRB_TEST_02',
            'round_id' => $round->round_id,
            'user_id' => $this->player->id,
            'horse_id' => 1,
            'horse_name' => 'Thunder Bolt',
            'bet_type' => 'win',
            'bet_amount' => 100.00,
            'odds' => 2.60,
            'status' => 'pending',
        ]);

        // 3. Place on Horse 2 (WON because Horse 2 is in top 2)
        $bet3 = HorseRaceBet::create([
            'bet_id' => 'HRB_TEST_03',
            'round_id' => $round->round_id,
            'user_id' => $this->player->id,
            'horse_id' => 2,
            'horse_name' => 'Golden Mane',
            'bet_type' => 'place',
            'bet_amount' => 100.00,
            'odds' => 1.90,
            'status' => 'pending',
        ]);

        $initialBalance = (float) $this->player->balance;

        // Settle round
        HorseRacingEngine::settleRound($round);

        $round->refresh();
        $this->assertEquals('completed', $round->status);
        $this->assertEquals(2, $round->winner_horse_id);
        $this->assertNotNull($round->second_horse_id);
        $this->assertNotNull($round->third_horse_id);

        $bet1->refresh();
        $this->assertEquals('won', $bet1->status);
        // Gross win = 340. Profit = 240. 10% Fee = 24. Net payout = 316
        $this->assertEquals(316.00, (float) $bet1->payout_amount);
        $this->assertEquals(216.00, (float) $bet1->net_profit);
        $this->assertEquals(24.00, (float) $bet1->commission_amount);

        $bet2->refresh();
        $this->assertEquals('lost', $bet2->status);
        $this->assertEquals(0.00, (float) $bet2->payout_amount);
        $this->assertEquals(-100.00, (float) $bet2->net_profit);

        $bet3->refresh();
        $this->assertEquals('won', $bet3->status);
        // Gross win = 190. Profit = 90. 10% Fee = 9. Net payout = 181
        $this->assertEquals(181.00, (float) $bet3->payout_amount);
        $this->assertEquals(81.00, (float) $bet3->net_profit);
        $this->assertEquals(9.00, (float) $bet3->commission_amount);

        // Player received 316 + 181 = 497 credited
        $this->player->refresh();
        $this->assertEquals($initialBalance + 497.00, (float) $this->player->balance);

        // Total payouts on round
        $this->assertEquals(497.00, (float) $round->total_payout_amount);
    }

    public function test_manual_admin_winner_setting_is_applied_and_cleared(): void
    {
        $setting = AdminSetting::current();
        $setting->update([
            'horse_racing_control_mode' => 'manual',
            'horse_racing_manual_winner' => 5,
        ]);

        $round = HorseRaceRound::create([
            'round_id' => 'HRTEST_MANUAL_01',
            'start_time' => now()->subMinute(),
            'end_time' => now(),
            'status' => 'racing',
            'horses_data' => HorseRacingEngine::getHorsesRoster(),
            'seed_hash' => hash('sha256', 'seed1'),
            'server_seed' => 'seed1',
            'is_manual' => false,
            'manual_winner_id' => null,
        ]);

        HorseRacingEngine::settleRound($round);

        $round->refresh();
        $this->assertEquals(5, $round->winner_horse_id);

        // Setting cleared after consumption
        $setting->refresh();
        $this->assertNull($setting->horse_racing_manual_winner);
        $this->assertEquals('fair', $setting->horse_racing_control_mode);
    }

    public function test_least_liability_mode_selects_lowest_payout_horse(): void
    {
        $setting = AdminSetting::current();
        $setting->update([
            'horse_racing_control_mode' => 'admin_profit',
        ]);

        $round = HorseRaceRound::create([
            'round_id' => 'HRTEST_LIABILITY_01',
            'start_time' => now()->subMinute(),
            'end_time' => now(),
            'status' => 'racing',
            'horses_data' => HorseRacingEngine::getHorsesRoster(),
            'seed_hash' => hash('sha256', 'seed2'),
            'server_seed' => 'seed2',
            'is_manual' => false,
        ]);

        // Place heavy bets on Horse 1 (liability: ₹10,000 * 2.60 = ₹26,000)
        HorseRaceBet::create([
            'bet_id' => 'HRB_HEAVY_01',
            'round_id' => $round->round_id,
            'user_id' => $this->player->id,
            'horse_id' => 1,
            'horse_name' => 'Thunder Bolt',
            'bet_type' => 'win',
            'bet_amount' => 10000.00,
            'odds' => 2.60,
            'status' => 'pending',
        ]);

        // Place small bet on Horse 4 (liability: ₹100 * 6.00 = ₹600)
        HorseRaceBet::create([
            'bet_id' => 'HRB_SMALL_04',
            'round_id' => $round->round_id,
            'user_id' => $this->player->id,
            'horse_id' => 4,
            'horse_name' => 'Desert King',
            'bet_type' => 'win',
            'bet_amount' => 100.00,
            'odds' => 6.00,
            'status' => 'pending',
        ]);

        HorseRacingEngine::settleRound($round);

        $round->refresh();
        // Horse 1 should NOT win because it has massive ₹26,000 liability!
        $this->assertNotEquals(1, $round->winner_horse_id);
    }

    public function test_simulate_race_waypoints_produces_valid_trajectories(): void
    {
        $horses = HorseRacingEngine::getHorsesRoster();
        $waypoints = HorseRacingEngine::simulateRaceWaypoints(3, 1, 5, $horses, 'anyseed');

        $this->assertCount(6, $waypoints);

        $winnerWp = collect($waypoints)->firstWhere('id', 3);
        $this->assertNotNull($winnerWp);
        $this->assertEquals(100.0, end($winnerWp['waypoints']));

        $secondWp = collect($waypoints)->firstWhere('id', 1);
        $this->assertNotNull($secondWp);
        $this->assertEquals(98.4, end($secondWp['waypoints']));

        $thirdWp = collect($waypoints)->firstWhere('id', 5);
        $this->assertNotNull($thirdWp);
        $this->assertEquals(96.8, end($thirdWp['waypoints']));
    }
}
