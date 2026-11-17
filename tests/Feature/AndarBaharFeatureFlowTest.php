<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\AndarBaharBet;
use App\Models\AndarBaharRound;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AndarBaharEngine;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AndarBaharFeatureFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // 12:00:10 (35s cycle: slot ends at 12:00:35, remaining = 25s, so betting is OPEN)
        Carbon::setTestNow(Carbon::parse('2026-10-24 12:00:10', 'Asia/Kolkata'));

        AdminSetting::current()->update([
            'andar_multiplier' => 1.95,
            'bahar_multiplier' => 1.95,
            'andar_bahar_min_bet' => 10.00,
            'andar_bahar_max_bet' => 50000.00,
            'andar_bahar_range_1_5' => 3.50,
            'andar_bahar_range_6_10' => 4.00,
            'andar_bahar_range_11_15' => 5.00,
            'andar_bahar_range_16_25' => 4.50,
            'andar_bahar_range_26_plus' => 8.00,
            'win_commission_percent' => 10.00,
            'auto_result_mode' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private static int $playerCounter = 0;

    private function createPlayer(float $balance = 1000.00, string $status = 'active'): User
    {
        self::$playerCounter++;

        return User::create([
            'name' => 'AB Player '.self::$playerCounter,
            'phone' => '9765432'.str_pad((string) self::$playerCounter, 3, '0', STR_PAD_LEFT),
            'email' => 'ab_player'.self::$playerCounter.'@example.com',
            'password' => bcrypt('secret123'),
            'balance' => $balance,
            'role' => 'user',
            'status' => $status,
        ]);
    }

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin Boss',
            'phone' => '9999911111',
            'email' => 'admin_ab@example.com',
            'password' => bcrypt('admin123'),
            'balance' => 0.00,
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    /**
     * Step 1: User can view the game page and poll live status.
     */
    public function test_user_can_view_andar_bahar_game_and_poll_status(): void
    {
        $player = $this->createPlayer(1500.00);

        // 1. Visit main Andar Bahar game interface
        $res = $this->actingAs($player)->get(route('andar-bahar.index'));
        $res->assertStatus(200);
        $res->assertSee('Andar Bahar Live');
        $res->assertSee('1,500.00');

        // 2. Poll live status API
        $apiRes = $this->actingAs($player)->getJson(route('api.andar-bahar.status'));
        $apiRes->assertStatus(200);
        $apiRes->assertJsonStructure([
            'success',
            'round_id',
            'remaining_seconds',
            'is_locked',
            'status',
            'joker_card' => ['rank', 'suit', 'color', 'symbol'],
            'balance',
            'my_active_bets',
            'recent_history',
            'multipliers' => [
                'andar',
                'bahar',
                'min_bet',
                'max_bet',
                'range_1_5',
                'range_6_10',
                'range_11_15',
                'range_16_25',
                'range_26_plus',
            ],
        ]);

        $this->assertEquals(1500.00, $apiRes->json('balance'));
        $this->assertEquals(1.95, $apiRes->json('multipliers.andar'));
        $this->assertEquals(1.95, $apiRes->json('multipliers.bahar'));
        $this->assertFalse($apiRes->json('is_locked'));

        // 3. Check history and my-bets endpoints
        $historyRes = $this->actingAs($player)->getJson(route('api.andar-bahar.history'));
        $historyRes->assertStatus(200);
        $historyRes->assertJsonStructure(['success', 'rounds']);

        $myBetsRes = $this->actingAs($player)->getJson(route('api.andar-bahar.my-bets'));
        $myBetsRes->assertStatus(200);
        $myBetsRes->assertJsonStructure(['success', 'bets', 'stats']);
    }

    /**
     * Step 2: Placing Main bets and Range bets with accurate wallet deductions & transaction entries.
     */
    public function test_user_can_place_main_and_range_bets_with_wallet_deduction(): void
    {
        $player = $this->createPlayer(1000.00);

        // Place Main Bet: ₹200 on Andar
        $resMain = $this->actingAs($player)->postJson(route('api.andar-bahar.place-bet'), [
            'bet_type' => 'main',
            'bet_selection' => 'andar',
            'amount' => 200,
        ]);

        $resMain->assertStatus(200);
        $resMain->assertJson([
            'success' => true,
            'new_balance' => 800.00,
            'selection' => 'andar',
            'amount' => 200,
        ]);

        $player->refresh();
        $this->assertEquals(800.00, (float) $player->balance);

        // Verify Bet DB Record
        $this->assertDatabaseHas('andar_bahar_bets', [
            'user_id' => $player->id,
            'bet_type' => 'main',
            'bet_selection' => 'andar',
            'amount' => 200.00,
            'win_multiplier' => 1.95,
            'status' => 'pending',
        ]);

        // Verify Transaction Ledger Entry
        $this->assertDatabaseHas('transactions', [
            'user_id' => $player->id,
            'type' => 'bet_placed',
            'amount' => 200.00,
            'balance_before' => 1000.00,
            'balance_after' => 800.00,
            'status' => 'completed',
        ]);

        // Place Side Bet: ₹100 on 1_5 range
        $resRange = $this->actingAs($player)->postJson(route('api.andar-bahar.place-bet'), [
            'bet_type' => 'cards_range',
            'bet_selection' => '1_5',
            'amount' => 100,
        ]);

        $resRange->assertStatus(200);
        $resRange->assertJson([
            'success' => true,
            'new_balance' => 700.00,
            'selection' => '1_5',
            'amount' => 100,
        ]);

        $player->refresh();
        $this->assertEquals(700.00, (float) $player->balance);

        $this->assertDatabaseHas('andar_bahar_bets', [
            'user_id' => $player->id,
            'bet_type' => 'cards_range',
            'bet_selection' => '1_5',
            'amount' => 100.00,
            'win_multiplier' => 3.50,
            'status' => 'pending',
        ]);

        // Check Round aggregated stats
        $round = AndarBaharRound::first();
        $this->assertEquals(2, $round->total_bets_count);
        $this->assertEquals(300.00, (float) $round->total_bet_amount);
    }

    /**
     * Step 3: Strict validation guards and security controls.
     */
    public function test_bet_validation_guards_and_security_controls(): void
    {
        // 1. Unauthenticated request
        $resUnauth = $this->postJson(route('api.andar-bahar.place-bet'), [
            'bet_type' => 'main',
            'bet_selection' => 'andar',
            'amount' => 50,
        ]);
        $resUnauth->assertStatus(401);

        // 2. Suspended player
        $suspendedPlayer = $this->createPlayer(500.00, 'suspended');
        $resSuspended = $this->actingAs($suspendedPlayer)->postJson(route('api.andar-bahar.place-bet'), [
            'bet_type' => 'main',
            'bet_selection' => 'andar',
            'amount' => 50,
        ]);
        $resSuspended->assertStatus(403);
        $resSuspended->assertJson(['success' => false, 'message' => 'Your account is suspended.']);

        // 3. Insufficient balance
        $poorPlayer = $this->createPlayer(50.00);
        $resPoor = $this->actingAs($poorPlayer)->postJson(route('api.andar-bahar.place-bet'), [
            'bet_type' => 'main',
            'bet_selection' => 'andar',
            'amount' => 200,
        ]);
        $resPoor->assertStatus(422);
        $resPoor->assertJson(['success' => false, 'message' => 'Insufficient wallet balance. Please recharge your wallet.']);

        // 4. Below minimum bet (min = ₹10)
        $player = $this->createPlayer(1000.00);
        $resBelowMin = $this->actingAs($player)->postJson(route('api.andar-bahar.place-bet'), [
            'bet_type' => 'main',
            'bet_selection' => 'andar',
            'amount' => 5,
        ]);
        $resBelowMin->assertStatus(422);
        $resBelowMin->assertJson(['success' => false, 'message' => 'Minimum bet amount is ₹10.']);

        // 5. Above maximum bet (max = ₹50,000)
        $richPlayer = $this->createPlayer(100000.00);
        $resAboveMax = $this->actingAs($richPlayer)->postJson(route('api.andar-bahar.place-bet'), [
            'bet_type' => 'main',
            'bet_selection' => 'andar',
            'amount' => 60000,
        ]);
        $resAboveMax->assertStatus(422);
        $resAboveMax->assertJson(['success' => false, 'message' => 'Maximum bet amount is ₹50000.']);

        // 6. Invalid side selection
        $resInvalidSide = $this->actingAs($player)->postJson(route('api.andar-bahar.place-bet'), [
            'bet_type' => 'main',
            'bet_selection' => 'middle',
            'amount' => 50,
        ]);
        $resInvalidSide->assertStatus(422);
        $resInvalidSide->assertJson(['success' => false, 'message' => 'Invalid side selection. Choose Andar or Bahar.']);

        // 7. Invalid range selection
        $resInvalidRange = $this->actingAs($player)->postJson(route('api.andar-bahar.place-bet'), [
            'bet_type' => 'cards_range',
            'bet_selection' => '50_plus',
            'amount' => 50,
        ]);
        $resInvalidRange->assertStatus(422);
        $resInvalidRange->assertJson(['success' => false, 'message' => 'Invalid cards count range selected.']);

        // 8. Betting during locked window (last 10 seconds of round)
        // Advance time to 12:00:20 (5 seconds remaining in 35s cycle -> is_locked is true!)
        Carbon::setTestNow(Carbon::parse('2026-10-24 12:00:20', 'Asia/Kolkata'));
        $resLocked = $this->actingAs($player)->postJson(route('api.andar-bahar.place-bet'), [
            'bet_type' => 'main',
            'bet_selection' => 'andar',
            'amount' => 50,
        ]);
        $resLocked->assertStatus(422);
        $resLocked->assertJson(['success' => false, 'message' => 'Betting is currently closed for this round. Please wait for the next deal.']);
    }

    /**
     * Step 4: Admin manual winner control, re-deal generation, and mode reset.
     */
    public function test_admin_manual_winner_override_and_reset(): void
    {
        $admin = $this->createAdmin();

        // Initialize active round
        $roundData = AndarBaharEngine::getCurrentRound();
        $round = $roundData['round_model'];

        // Admin visits Andar Bahar control panel
        $resAdmin = $this->actingAs($admin)->get(route('admin.andar-bahar.index'));
        $resAdmin->assertStatus(200);
        $resAdmin->assertSee('Andar Bahar Live Control Hub');

        // Admin forces ANDAR win
        $resForceAndar = $this->actingAs($admin)->post(route('admin.andar-bahar.manual-winner'), [
            'round_id' => $round->round_id,
            'winner' => 'andar',
        ]);
        $resForceAndar->assertRedirect();

        $round->refresh();
        $this->assertTrue((bool) $round->is_manual);
        $this->assertEquals('andar', $round->manual_winning_side);
        $this->assertEquals('andar', $round->winning_side);
        $this->assertEquals($round->joker_card['rank'], $round->winning_card['rank']);

        // Admin forces BAHAR win
        $resForceBahar = $this->actingAs($admin)->post(route('admin.andar-bahar.manual-winner'), [
            'round_id' => $round->round_id,
            'winner' => 'bahar',
        ]);
        $resForceBahar->assertRedirect();

        $round->refresh();
        $this->assertTrue((bool) $round->is_manual);
        $this->assertEquals('bahar', $round->manual_winning_side);
        $this->assertEquals('bahar', $round->winning_side);
        $this->assertEquals($round->joker_card['rank'], $round->winning_card['rank']);

        // Admin resets back to Auto Mode
        $resClear = $this->actingAs($admin)->post(route('admin.andar-bahar.clear-manual'), [
            'round_id' => $round->round_id,
        ]);
        $resClear->assertRedirect();

        $round->refresh();
        $this->assertFalse((bool) $round->is_manual);
        $this->assertNull($round->manual_winning_side);
        $this->assertContains($round->winning_side, ['andar', 'bahar']);
    }

    /**
     * Step 5: Full period settlement, commission deduction, and winning payouts.
     */
    public function test_round_settlement_and_payout_distribution_with_commission(): void
    {
        $roundData = AndarBaharEngine::getCurrentRound();
        $round = $roundData['round_model'];

        $player1 = $this->createPlayer(1000.00); // Bets on Andar
        $player2 = $this->createPlayer(1000.00); // Bets on Bahar
        $player3 = $this->createPlayer(1000.00); // Bets on Range 1_5

        // Place bets
        AndarBaharEngine::placeBet($player1, 'main', 'andar', 500.00);
        AndarBaharEngine::placeBet($player2, 'main', 'bahar', 500.00);
        AndarBaharEngine::placeBet($player3, 'cards_range', '1_5', 200.00);

        // Force manual outcome: ANDAR wins, with 3 cards dealt (falls in 1_5 range!)
        $round->is_manual = true;
        $round->manual_winning_side = 'andar';
        $round->winning_side = 'andar';

        // Deal cards specifically with 3 cards: Andar, Bahar, Andar (winner)
        $joker = $round->joker_card;
        $matchingCard = ['rank' => $joker['rank'], 'suit' => ($joker['suit'] === 'H' ? 'D' : 'H'), 'color' => 'red', 'symbol' => '♥'];
        $dummyCard1 = ['rank' => ($joker['rank'] === 'A' ? '2' : 'A'), 'suit' => 'C', 'color' => 'black', 'symbol' => '♣'];
        $dummyCard2 = ['rank' => ($joker['rank'] === 'K' ? '3' : 'K'), 'suit' => 'S', 'color' => 'black', 'symbol' => '♠'];

        $round->dealt_cards = [
            ['side' => 'andar', 'card' => $dummyCard1],
            ['side' => 'bahar', 'card' => $dummyCard2],
            ['side' => 'andar', 'card' => $matchingCard],
        ];
        $round->winning_card = $matchingCard;
        $round->total_cards_dealt = 3;
        $round->save();

        // Settle round
        $settled = AndarBaharEngine::settleRound($round);
        $this->assertEquals('completed', $settled->status);

        // Verify Player 1 (Won on Andar)
        // Multiplier: 1.95, Stake: 500.
        // Gross: 500 * 1.95 = 975. Profit: 475.
        // Commission (10%): 47.50. Net payout: 500 + (475 - 47.50) = 927.50.
        $player1Bet = AndarBaharBet::where('user_id', $player1->id)->first();
        $this->assertEquals('won', $player1Bet->status);
        $this->assertEquals(927.50, (float) $player1Bet->win_amount);
        $this->assertEquals(47.50, (float) $player1Bet->commission_amount);

        $player1->refresh();
        // Balance: 1000 - 500 (stake) + 927.50 (payout) = 1427.50
        $this->assertEquals(1427.50, (float) $player1->balance);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $player1->id,
            'type' => 'bet_won',
            'amount' => 927.50,
            'balance_before' => 500.00,
            'balance_after' => 1427.50,
            'status' => 'completed',
        ]);

        // Verify Player 2 (Lost on Bahar)
        $player2Bet = AndarBaharBet::where('user_id', $player2->id)->first();
        $this->assertEquals('lost', $player2Bet->status);
        $this->assertEquals(0.00, (float) $player2Bet->win_amount);

        $player2->refresh();
        $this->assertEquals(500.00, (float) $player2->balance);

        // Verify Player 3 (Won on Range 1_5)
        // Multiplier: 3.50, Stake: 200.
        // Gross: 200 * 3.50 = 700. Profit: 500.
        // Commission (10%): 50.00. Net payout: 200 + (500 - 50) = 650.00.
        $player3Bet = AndarBaharBet::where('user_id', $player3->id)->first();
        $this->assertEquals('won', $player3Bet->status);
        $this->assertEquals(650.00, (float) $player3Bet->win_amount);
        $this->assertEquals(50.00, (float) $player3Bet->commission_amount);

        $player3->refresh();
        // Balance: 1000 - 200 + 650 = 1450.00
        $this->assertEquals(1450.00, (float) $player3->balance);
    }

    /**
     * Step 6: Auto-pilot profit optimization automatically picks least liability outcome.
     */
    public function test_auto_pilot_house_profit_optimization_chooses_safest_side(): void
    {
        $roundData = AndarBaharEngine::getCurrentRound();
        $round = $roundData['round_model'];

        // Player bets heavily on ANDAR (₹5,000), BAHAR has ₹0 bets
        $player = $this->createPlayer(10000.00);
        AndarBaharEngine::placeBet($player, 'main', 'andar', 5000.00);

        // Force initial random deal to have Andar winning to test that auto-pilot overrides it
        $sim = AndarBaharEngine::simulateGame('andar', $round->joker_card);
        $round->dealt_cards = $sim['dealt_cards'];
        $round->winning_side = 'andar';
        $round->winning_card = $sim['winning_card'];
        $round->total_cards_dealt = $sim['total_cards_dealt'];
        $round->is_manual = false;
        $round->save();

        // Calculate exposure: Safest side MUST be 'bahar' because Andar projected payout is ₹9,750 while Bahar is ₹0
        $exposure = AndarBaharEngine::calculateExposure($round);
        $this->assertEquals('bahar', $exposure['safest_side']);

        // Settle round without admin intervention (is_manual = false)
        $settled = AndarBaharEngine::settleRound($round);

        // Auto-pilot MUST have pivoted winning side to BAHAR to protect house profit!
        $this->assertEquals('bahar', $settled->winning_side);
        $this->assertEquals(0.00, (float) $settled->total_payout_amount);

        // Player's bet is marked lost
        $bet = AndarBaharBet::where('user_id', $player->id)->first();
        $this->assertEquals('lost', $bet->status);
        $this->assertEquals(0.00, (float) $bet->win_amount);

        $player->refresh();
        $this->assertEquals(5000.00, (float) $player->balance);
    }

    /**
     * Step 7: Admin settings update and winners list query.
     */
    public function test_admin_settings_update_and_winners_dashboard(): void
    {
        $admin = $this->createAdmin();

        // Admin updates multipliers & limits
        $resUpdate = $this->actingAs($admin)->post(route('admin.andar-bahar.settings'), [
            'andar_multiplier' => 1.98,
            'bahar_multiplier' => 1.92,
            'andar_bahar_min_bet' => 20,
            'andar_bahar_max_bet' => 100000,
            'andar_bahar_range_1_5' => 3.80,
            'andar_bahar_range_6_10' => 4.20,
            'andar_bahar_range_11_15' => 5.20,
            'andar_bahar_range_16_25' => 4.80,
            'andar_bahar_range_26_plus' => 9.00,
        ]);

        $resUpdate->assertRedirect();
        $setting = AdminSetting::current();
        $this->assertEquals(1.98, (float) $setting->andar_multiplier);
        $this->assertEquals(1.92, (float) $setting->bahar_multiplier);
        $this->assertEquals(20.00, (float) $setting->andar_bahar_min_bet);

        // Create a won bet for winners view
        $player = $this->createPlayer(1000.00);
        $round = AndarBaharRound::create([
            'round_id' => 'AB2026102400999',
            'start_time' => Carbon::now()->subMinutes(2),
            'end_time' => Carbon::now()->subMinute(),
            'joker_card' => ['rank' => 'K', 'suit' => 'H', 'color' => 'red', 'symbol' => '♥'],
            'dealt_cards' => [],
            'winning_side' => 'andar',
            'winning_card' => ['rank' => 'K', 'suit' => 'D', 'color' => 'red', 'symbol' => '♦'],
            'total_cards_dealt' => 5,
            'status' => 'completed',
            'is_manual' => false,
        ]);

        AndarBaharBet::create([
            'user_id' => $player->id,
            'round_id' => $round->round_id,
            'bet_type' => 'main',
            'bet_selection' => 'andar',
            'amount' => 500.00,
            'win_multiplier' => 1.95,
            'win_amount' => 927.50,
            'commission_amount' => 47.50,
            'status' => 'won',
            'evaluated_at' => Carbon::now(),
        ]);

        // Admin checks winners list
        $resWinners = $this->actingAs($admin)->get(route('admin.andar-bahar.winners', ['search' => $player->name]));
        $resWinners->assertStatus(200);
        $resWinners->assertSee($player->name);
        $resWinners->assertSee('927.50');
        $resWinners->assertSee('#'.$round->round_id);
    }
}
