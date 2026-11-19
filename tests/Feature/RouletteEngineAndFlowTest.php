<?php

namespace Tests\Feature;

use App\Models\AdminRole;
use App\Models\AdminSetting;
use App\Models\RouletteBet;
use App\Models\RouletteRound;
use App\Models\User;
use App\Services\RouletteEngine;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RouletteEngineAndFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $player;

    protected function setUp(): void
    {
        parent::setUp();

        $role = AdminRole::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Administrator',
                'description' => 'Unrestricted master access',
                'permissions' => array_keys(AdminRole::permissionCatalog()),
                'is_system' => true,
            ]
        );

        $this->admin = User::create([
            'name' => 'Root Admin',
            'username' => 'root_admin',
            'phone' => '9999999999',
            'email' => 'root@example.com',
            'password' => Hash::make('admin1234'),
            'role' => 'admin',
            'admin_role_id' => $role->id,
        ]);

        $this->player = User::factory()->create([
            'name' => 'Amit Player',
            'phone' => '9812345678',
            'balance' => 10000.00,
        ]);
    }

    public function test_player_can_load_roulette_game_page_with_mobile_ui(): void
    {
        $response = $this->actingAs($this->player)->get(route('roulette.index'));

        $response->assertStatus(200);
        $response->assertSee('European Live Roulette');
        $response->assertSee('rouletteCanvas');
        $response->assertSee('felt-board');
        $response->assertSee('CONFIRM BETS');
        $response->assertSee('₹10,000.00');
        // Mobile bottom sheet
        $response->assertSee('mobileGamesModal');
    }

    public function test_live_status_api_returns_sync_data(): void
    {
        $response = $this->actingAs($this->player)->getJson(route('api.roulette.status'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'round_id',
            'status',
            'remaining_seconds',
            'is_locked',
            'duration',
            'seed_hash',
            'history',
            'balance',
        ]);
        $this->assertTrue($response->json('success'));
    }

    public function test_player_can_place_bets_and_balance_is_deducted(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 12, 12, 0, 0, 'Asia/Kolkata'));

        $roundData = RouletteEngine::getCurrentRound();
        $round = $roundData['round_model'];

        // Ensure round is in betting status for test
        $round->status = 'betting';
        $round->save();

        $betsPayload = [
            ['type' => 'straight', 'value' => '25', 'amount' => 100],
            ['type' => 'red', 'value' => 'red', 'amount' => 200],
            ['type' => 'black', 'value' => 'black', 'amount' => 100],
            ['type' => 'dozen3', 'value' => 'dozen3', 'amount' => 150],
        ];

        $response = $this->actingAs($this->player)->postJson(route('api.roulette.place-bet'), [
            'bets' => $betsPayload,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Total stake = 100 + 200 + 100 + 150 = 550
        $this->assertEquals(9450.00, (float) $this->player->fresh()->balance);

        $this->assertDatabaseHas('roulette_bets', [
            'user_id' => $this->player->id,
            'round_id' => $round->round_id,
            'bet_type' => 'straight',
            'bet_value' => '25',
            'bet_amount' => 100.00,
            'status' => 'pending',
        ]);

        Carbon::setTestNow();
    }

    public function test_admin_can_override_and_force_number_25(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 12, 12, 0, 0, 'Asia/Kolkata'));

        $roundData = RouletteEngine::getCurrentRound();
        $round = $roundData['round_model'];
        $round->status = 'betting';
        $round->save();

        // Admin locks manual number = 25
        $response = $this->actingAs($this->admin)->post(route('admin.roulette.set-number'), [
            'round_id' => $round->round_id,
            'manual_number' => 25,
        ]);

        $response->assertSessionHas('success');

        // Verify round and admin setting
        $round->refresh();
        $this->assertTrue($round->is_manual);
        $this->assertEquals(25, $round->manual_number);

        $setting = AdminSetting::current();
        $this->assertEquals(25, (int) $setting->roulette_manual_number);
        $this->assertEquals('manual', $setting->roulette_control_mode);

        Carbon::setTestNow();
    }

    public function test_when_admin_forces_25_round_settles_on_25_and_pays_correctly(): void
    {
        $roundData = RouletteEngine::getCurrentRound();
        $round = $roundData['round_model'];
        $round->is_manual = true;
        $round->manual_number = 25;
        $round->status = 'spinning';
        $round->save();

        $setting = AdminSetting::current();
        $setting->update([
            'roulette_manual_number' => 25,
            'roulette_control_mode' => 'manual',
        ]);

        // Place bets:
        // 1. Straight on 25 (Stake 100, Odds 36x -> Win 3600)
        $betStraight25 = RouletteBet::create([
            'bet_id' => 'BET_S25',
            'round_id' => $round->round_id,
            'user_id' => $this->player->id,
            'bet_type' => 'straight',
            'bet_value' => '25',
            'bet_amount' => 100.00,
            'odds' => 36.00,
            'status' => 'pending',
        ]);

        // 2. Red bet (25 is RED! Stake 200, Odds 2x -> Win 400)
        $betRed = RouletteBet::create([
            'bet_id' => 'BET_RED',
            'round_id' => $round->round_id,
            'user_id' => $this->player->id,
            'bet_type' => 'red',
            'bet_value' => 'red',
            'bet_amount' => 200.00,
            'odds' => 2.00,
            'status' => 'pending',
        ]);

        // 3. Black bet (Stake 100, 25 is RED -> Lost)
        $betBlack = RouletteBet::create([
            'bet_id' => 'BET_BLK',
            'round_id' => $round->round_id,
            'user_id' => $this->player->id,
            'bet_type' => 'black',
            'bet_value' => 'black',
            'bet_amount' => 100.00,
            'odds' => 2.00,
            'status' => 'pending',
        ]);

        // 4. Dozen3 (25-36, 25 is in Dozen 3! Stake 100, Odds 3x -> Win 300)
        $betDozen3 = RouletteBet::create([
            'bet_id' => 'BET_D3',
            'round_id' => $round->round_id,
            'user_id' => $this->player->id,
            'bet_type' => 'dozen3',
            'bet_value' => 'dozen3',
            'bet_amount' => 100.00,
            'odds' => 3.00,
            'status' => 'pending',
        ]);

        // 5. Col1 (Column 1 contains: 1, 4, 7, 10, 13, 16, 19, 22, 25 -> Win 3x! Stake 50 -> Win 150)
        $betCol1 = RouletteBet::create([
            'bet_id' => 'BET_C1',
            'round_id' => $round->round_id,
            'user_id' => $this->player->id,
            'bet_type' => 'col1',
            'bet_value' => 'col1',
            'bet_amount' => 50.00,
            'odds' => 3.00,
            'status' => 'pending',
        ]);

        $initialBalance = (float) $this->player->balance;

        // Now settle the round
        RouletteEngine::settleRound($round);

        $round->refresh();
        $this->assertEquals('completed', $round->status);
        $this->assertEquals(25, $round->winning_number);
        $this->assertEquals('red', $round->winning_color);
        $this->assertEquals(3, $round->dozen);
        $this->assertEquals(1, $round->column);
        $this->assertFalse($round->is_even);
        $this->assertTrue($round->is_high);

        // Check bets statuses & payouts
        $betStraight25->refresh();
        $this->assertEquals('won', $betStraight25->status);
        // Gross 3600. Profit 3500 - 10% fee (350) = 3250 net payout
        $this->assertEquals(3250.00, (float) $betStraight25->payout_amount);
        $this->assertEquals(350.00, (float) $betStraight25->commission_amount);

        $betRed->refresh();
        $this->assertEquals('won', $betRed->status);
        // Gross 400. Profit 200 - 10% fee (20) = 380 net payout
        $this->assertEquals(380.00, (float) $betRed->payout_amount);
        $this->assertEquals(20.00, (float) $betRed->commission_amount);

        $betBlack->refresh();
        $this->assertEquals('lost', $betBlack->status);
        $this->assertEquals(0.00, (float) $betBlack->payout_amount);

        $betDozen3->refresh();
        $this->assertEquals('won', $betDozen3->status);
        // Gross 300. Profit 200 - 10% fee (20) = 280 net payout
        $this->assertEquals(280.00, (float) $betDozen3->payout_amount);
        $this->assertEquals(20.00, (float) $betDozen3->commission_amount);

        $betCol1->refresh();
        $this->assertEquals('won', $betCol1->status);
        // Gross 150. Profit 100 - 10% fee (10) = 140 net payout
        $this->assertEquals(140.00, (float) $betCol1->payout_amount);
        $this->assertEquals(10.00, (float) $betCol1->commission_amount);

        // Total win credit = 3250 + 380 + 280 + 140 = 4050.00
        $expectedBalance = $initialBalance + 4050.00;
        $this->assertEquals($expectedBalance, (float) $this->player->fresh()->balance);
    }

    public function test_admin_can_clear_manual_override(): void
    {
        $roundData = RouletteEngine::getCurrentRound();
        $round = $roundData['round_model'];
        $round->is_manual = true;
        $round->manual_number = 25;
        $round->save();

        $response = $this->actingAs($this->admin)->post(route('admin.roulette.clear-manual'), [
            'round_id' => $round->round_id,
        ]);

        $response->assertSessionHas('success');

        $round->refresh();
        $this->assertFalse($round->is_manual);
        $this->assertNull($round->manual_number);

        $setting = AdminSetting::current();
        $this->assertNull($setting->roulette_manual_number);
        $this->assertEquals('fair', $setting->roulette_control_mode);
    }

    public function test_admin_roulette_hub_displays_live_exposure_and_pool(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.roulette.index'));

        $response->assertStatus(200);
        $response->assertSee('European Roulette Live Control');
        $response->assertSee('1-Click Number Override');
        $response->assertSee('Recent Completed Rounds');
        $response->assertSee('Active Bets on Round');
    }

    public function test_red_pays_5x_and_black_pays_7x_and_zero_pays_36x(): void
    {
        $roundData = RouletteEngine::getCurrentRound();
        $round = $roundData['round_model'];
        $round->status = 'spinning';
        $round->save();

        // 1. Red bet (Placed without explicit odds so it inherits BET_ODDS['red'] = 5.00)
        $betRed = RouletteBet::create([
            'bet_id' => 'BET_CUSTOM_RED',
            'round_id' => $round->round_id,
            'user_id' => $this->player->id,
            'bet_type' => 'red',
            'bet_value' => 'red',
            'bet_amount' => 100.00,
            'odds' => RouletteEngine::getBetOdds()['red'],
            'status' => 'pending',
        ]);
        $this->assertEquals(5.00, (float) $betRed->odds);

        // Force winning number to 7 (Red)
        $round->is_manual = true;
        $round->manual_number = 7;
        $round->save();

        RouletteEngine::settleRound($round);

        $betRed->refresh();
        $this->assertEquals('won', $betRed->status);
        // 100 stake * 5.00 = 500 gross. Profit = 400 - 10% fee (40) = 360 net profit. Net payout = 460
        $this->assertEquals(460.00, (float) $betRed->payout_amount);
        $this->assertEquals(40.00, (float) $betRed->commission_amount);

        // 2. Black bet on a new round (Black = 7x)
        $round2 = RouletteRound::create([
            'round_id' => 'RLT_TEST_BLK_01',
            'round_number' => 9992,
            'start_time' => now(),
            'end_time' => now()->addSeconds(30),
            'status' => 'spinning',
            'is_manual' => true,
            'manual_number' => 8, // 8 is Black
        ]);

        $betBlack = RouletteBet::create([
            'bet_id' => 'BET_CUSTOM_BLK',
            'round_id' => $round2->round_id,
            'user_id' => $this->player->id,
            'bet_type' => 'black',
            'bet_value' => 'black',
            'bet_amount' => 100.00,
            'odds' => RouletteEngine::getBetOdds()['black'],
            'status' => 'pending',
        ]);
        $this->assertEquals(7.00, (float) $betBlack->odds);

        RouletteEngine::settleRound($round2);

        $betBlack->refresh();
        $this->assertEquals('won', $betBlack->status);
        // 100 stake * 7.00 = 700 gross. Profit = 600 - 10% fee (60) = 540 net profit. Net payout = 640
        $this->assertEquals(640.00, (float) $betBlack->payout_amount);
        $this->assertEquals(60.00, (float) $betBlack->commission_amount);

        // 3. Zero bet on a new round (0 Green = 36x)
        $round3 = RouletteRound::create([
            'round_id' => 'RLT_TEST_ZERO_01',
            'round_number' => 9993,
            'start_time' => now(),
            'end_time' => now()->addSeconds(30),
            'status' => 'spinning',
            'is_manual' => true,
            'manual_number' => 0, // 0 Green
        ]);

        $betZero = RouletteBet::create([
            'bet_id' => 'BET_CUSTOM_ZERO',
            'round_id' => $round3->round_id,
            'user_id' => $this->player->id,
            'bet_type' => 'straight',
            'bet_value' => '0',
            'bet_amount' => 50.00,
            'odds' => RouletteEngine::getBetOdds()['straight'],
            'status' => 'pending',
        ]);
        $this->assertEquals(36.00, (float) $betZero->odds);

        RouletteEngine::settleRound($round3);

        $betZero->refresh();
        $this->assertEquals('won', $betZero->status);
        // 50 stake * 36.00 = 1800 gross. Profit = 1750 - 10% fee (175) = 1575 net profit. Net payout = 1625
        $this->assertEquals(1625.00, (float) $betZero->payout_amount);
        $this->assertEquals(175.00, (float) $betZero->commission_amount);
    }

    public function test_biased_winning_number_generator_follows_pattern_red_max_green_rare(): void
    {
        $setting = AdminSetting::current();
        $setting->update([
            'roulette_red_weight' => 70.00,
            'roulette_black_weight' => 29.00,
            'roulette_green_weight' => 1.00,
        ]);

        $redCount = 0;
        $blackCount = 0;
        $greenCount = 0;
        $iterations = 500;

        for ($i = 0; $i < $iterations; $i++) {
            $num = RouletteEngine::generateBiasedWinningNumber($setting);
            $details = RouletteEngine::getNumberDetails($num);

            if ($details['color'] === 'red') {
                $redCount++;
            } elseif ($details['color'] === 'black') {
                $blackCount++;
            } else {
                $greenCount++;
            }
        }

        // Red must be maximum (more than black)
        $this->assertGreaterThan($blackCount, $redCount, "Red count ($redCount) should be greater than Black count ($blackCount)");
        // Green must be rarest of all (at most ~5% even with random variance over 500 rolls)
        $this->assertLessThan(25, $greenCount, "Green count ($greenCount) should be very rare (< 5% of 500)");
    }

    public function test_admin_can_update_roulette_multipliers_and_weights(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.roulette.settings'), [
            'roulette_min_bet' => 20.00,
            'roulette_max_bet' => 50000.00,
            'roulette_round_duration' => 35,
            'roulette_control_mode' => 'fair',
            'roulette_red_multiplier' => 5.00,
            'roulette_black_multiplier' => 7.00,
            'roulette_red_weight' => 70.00,
            'roulette_black_weight' => 29.00,
            'roulette_green_weight' => 1.00,
        ]);

        $response->assertSessionHas('success');

        $setting = AdminSetting::current()->fresh();
        $this->assertEquals(5.00, (float) $setting->roulette_red_multiplier);
        $this->assertEquals(7.00, (float) $setting->roulette_black_multiplier);
        $this->assertEquals(70.00, (float) $setting->roulette_red_weight);
        $this->assertEquals(29.00, (float) $setting->roulette_black_weight);
        $this->assertEquals(1.00, (float) $setting->roulette_green_weight);
    }

    public function test_straight_bets_have_correct_multipliers_zero_is_36x_red_is_5x_black_is_7x(): void
    {
        // 1. Direct Engine verification
        $this->assertEquals(36.00, RouletteEngine::getOddsForBet('straight', '0'));
        $this->assertEquals(5.00, RouletteEngine::getOddsForBet('straight', '5')); // 5 is Red
        $this->assertEquals(7.00, RouletteEngine::getOddsForBet('straight', '11')); // 11 is Black
        $this->assertEquals(5.00, RouletteEngine::getOddsForBet('straight', '14')); // 14 is Red
        $this->assertEquals(7.00, RouletteEngine::getOddsForBet('straight', '2')); // 2 is Black

        // 2. Placing bets via API assigns correct odds in DB
        Carbon::setTestNow(Carbon::create(2026, 11, 19, 12, 0, 0, 'Asia/Kolkata'));
        $roundData = RouletteEngine::getCurrentRound();
        $round = $roundData['round_model'];
        $round->status = 'betting';
        $round->save();

        $response = $this->actingAs($this->player)->postJson(route('api.roulette.place-bet'), [
            'bets' => [
                ['type' => 'straight', 'value' => '0', 'amount' => 10],
                ['type' => 'straight', 'value' => '5', 'amount' => 10],
                ['type' => 'straight', 'value' => '11', 'amount' => 10],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('roulette_bets', [
            'round_id' => $round->round_id,
            'bet_type' => 'straight',
            'bet_value' => '0',
            'odds' => 36.00,
        ]);

        $this->assertDatabaseHas('roulette_bets', [
            'round_id' => $round->round_id,
            'bet_type' => 'straight',
            'bet_value' => '5',
            'odds' => 5.00,
        ]);

        $this->assertDatabaseHas('roulette_bets', [
            'round_id' => $round->round_id,
            'bet_type' => 'straight',
            'bet_value' => '11',
            'odds' => 7.00,
        ]);

        Carbon::setTestNow();
    }
}
