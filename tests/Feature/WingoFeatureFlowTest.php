<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WingoBet;
use App\Models\WingoPeriod;
use App\Services\WingoEngine;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WingoFeatureFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Fix test time to 12:00:10 (20 seconds remaining in 30s round, so NOT locked)
        Carbon::setTestNow(Carbon::parse('2026-10-24 12:00:10', 'Asia/Kolkata'));

        AdminSetting::current()->update([
            'wingo_min_bet' => 1.00,
            'wingo_max_bet' => 50000.00,
            'win_commission_percent' => 10.00,
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
            'name' => 'Player '.self::$playerCounter,
            'phone' => '9876543'.str_pad((string) self::$playerCounter, 3, '0', STR_PAD_LEFT),
            'email' => 'player'.self::$playerCounter.'@example.com',
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
            'phone' => '9999900000',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'balance' => 0.00,
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    /**
     * Step 1: Test user can view the game page and fetch live status.
     */
    public function test_user_can_view_wingo_game_and_fetch_live_status(): void
    {
        $player = $this->createPlayer(500.00);

        // 1. Visit main WinGo page
        $res = $this->actingAs($player)->get(route('wingo.index', ['type' => '30s']));
        $res->assertStatus(200);
        $res->assertSee('Win Go 30s');
        $res->assertSee('500.00');

        // 2. Poll live status API
        $apiRes = $this->actingAs($player)->getJson(route('api.wingo.status', ['type' => '30s']));
        $apiRes->assertStatus(200);
        $apiRes->assertJsonStructure([
            'success',
            'game_type',
            'period_id',
            'remaining_seconds',
            'is_locked',
            'recent_history',
            'balance',
            'my_active_bets',
        ]);
        $apiRes->assertJson([
            'success' => true,
            'game_type' => '30s',
            'balance' => 500.00,
        ]);
    }

    /**
     * Step 2: Test user can place bets across all supported types (Color, Number, Size, Combination).
     */
    public function test_user_can_place_bets_across_all_supported_types(): void
    {
        $player = $this->createPlayer(1000.00);

        // Ensure current period is not in locked 5-second window for testing
        $current = WingoEngine::getCurrentPeriod('30s');
        if ($current['is_locked']) {
            Carbon::setTestNow(Carbon::now('Asia/Kolkata')->subSeconds(10));
        }

        // 1. Color Bet: Green (₹10 x 1)
        $resColor = $this->actingAs($player)->postJson(route('api.wingo.place-bet'), [
            'game_type' => '30s',
            'bet_type' => 'color',
            'bet_selection' => 'Green',
            'unit_price' => 10,
            'multiplier' => 1,
        ]);
        $resColor->assertStatus(200);
        $resColor->assertJson(['success' => true, 'new_balance' => 990.00]);

        // 2. Number Bet: #7 (₹10 x 2 = ₹20)
        $resNumber = $this->actingAs($player)->postJson(route('api.wingo.place-bet'), [
            'game_type' => '30s',
            'bet_type' => 'number',
            'bet_selection' => '7',
            'unit_price' => 10,
            'multiplier' => 2,
        ]);
        $resNumber->assertStatus(200);
        $resNumber->assertJson(['success' => true, 'new_balance' => 970.00]);

        // 3. Size Bet: Big (₹10 x 3 = ₹30)
        $resSize = $this->actingAs($player)->postJson(route('api.wingo.place-bet'), [
            'game_type' => '30s',
            'bet_type' => 'size',
            'bet_selection' => 'Big',
            'unit_price' => 10,
            'multiplier' => 3,
        ]);
        $resSize->assertStatus(200);
        $resSize->assertJson(['success' => true, 'new_balance' => 940.00]);

        // 4. Combination Bet: Green + Big (₹10 x 4 = ₹40)
        $resCombo = $this->actingAs($player)->postJson(route('api.wingo.place-bet'), [
            'game_type' => '30s',
            'bet_type' => 'combination',
            'bet_selection' => 'Green + Big',
            'unit_price' => 10,
            'multiplier' => 4,
        ]);
        $resCombo->assertStatus(200);
        $resCombo->assertJson(['success' => true, 'new_balance' => 900.00]);

        // Verify player balance in DB
        $player->refresh();
        $this->assertEquals('900.00', $player->balance);

        // Verify bets created
        $this->assertEquals(4, WingoBet::where('user_id', $player->id)->count());

        // Verify transactions created
        $this->assertEquals(4, Transaction::where('user_id', $player->id)->where('type', 'bet_placed')->count());
    }

    /**
     * Step 3: Test validation guards: guest, suspended user, insufficient funds, and locked period.
     */
    public function test_betting_validation_and_lock_guards(): void
    {
        // 1. Guest cannot bet
        $guestRes = $this->postJson(route('api.wingo.place-bet'), [
            'game_type' => '30s',
            'bet_type' => 'color',
            'bet_selection' => 'Green',
            'unit_price' => 10,
            'multiplier' => 1,
        ]);
        $guestRes->assertStatus(401);

        // 2. Suspended player cannot bet
        $suspended = $this->createPlayer(500.00, 'suspended');
        $suspendedRes = $this->actingAs($suspended)->postJson(route('api.wingo.place-bet'), [
            'game_type' => '30s',
            'bet_type' => 'color',
            'bet_selection' => 'Green',
            'unit_price' => 10,
            'multiplier' => 1,
        ]);
        $suspendedRes->assertStatus(403);

        // 3. Insufficient balance cannot bet
        $poorPlayer = $this->createPlayer(5.00);
        $poorRes = $this->actingAs($poorPlayer)->postJson(route('api.wingo.place-bet'), [
            'game_type' => '30s',
            'bet_type' => 'color',
            'bet_selection' => 'Green',
            'unit_price' => 10,
            'multiplier' => 1,
        ]);
        $poorRes->assertStatus(422);
        $poorRes->assertJson(['success' => false]);
    }

    /**
     * Step 4: Test Admin Master Controls (Exact Number, Color, Size, Combination, and Reset).
     */
    public function test_admin_can_view_risk_matrix_and_lock_different_targets(): void
    {
        $admin = $this->createAdmin();
        $periodData = WingoEngine::getCurrentPeriod('30s');
        $periodId = $periodData['period_id'];

        // 1. Admin views live dashboard
        $viewRes = $this->actingAs($admin)->get(route('admin.wingo.index', ['type' => '30s']));
        $viewRes->assertStatus(200);
        $viewRes->assertSee('Master Result Controller');

        // 2. Admin locks Exact Number #7
        $lockNumRes = $this->actingAs($admin)->post(route('admin.wingo.manual-target'), [
            'game_type' => '30s',
            'period_id' => $periodId,
            'target_mode' => 'number',
            'manual_number' => 7,
        ]);
        $lockNumRes->assertRedirect();
        $period = WingoPeriod::where('game_type', '30s')->where('period_id', $periodId)->first();
        $this->assertTrue((bool) $period->is_manual);
        $this->assertEquals(7, $period->manual_number);
        $this->assertNull($period->manual_color);
        $this->assertNull($period->manual_size);

        // 3. Admin clears manual
        $clearRes = $this->actingAs($admin)->post(route('admin.wingo.clear-manual'), [
            'game_type' => '30s',
            'period_id' => $periodId,
        ]);
        $clearRes->assertRedirect();
        $period->refresh();
        $this->assertFalse((bool) $period->is_manual);
        $this->assertNull($period->manual_number);

        // 4. Admin locks Color: Red
        $lockColorRes = $this->actingAs($admin)->post(route('admin.wingo.manual-target'), [
            'game_type' => '30s',
            'period_id' => $periodId,
            'target_mode' => 'color',
            'manual_color' => 'red',
        ]);
        $lockColorRes->assertRedirect();
        $period->refresh();
        $this->assertTrue((bool) $period->is_manual);
        $this->assertEquals('red', $period->manual_color);
        $this->assertNull($period->manual_number);
        $this->assertNull($period->manual_size);

        // 5. Admin locks Size: Big
        $lockSizeRes = $this->actingAs($admin)->post(route('admin.wingo.manual-target'), [
            'game_type' => '30s',
            'period_id' => $periodId,
            'target_mode' => 'size',
            'manual_size' => 'big',
        ]);
        $lockSizeRes->assertRedirect();
        $period->refresh();
        $this->assertTrue((bool) $period->is_manual);
        $this->assertEquals('big', $period->manual_size);
        $this->assertNull($period->manual_color);
        $this->assertNull($period->manual_number);

        // 6. Admin locks Combination: Violet + Big
        $lockComboRes = $this->actingAs($admin)->post(route('admin.wingo.manual-target'), [
            'game_type' => '30s',
            'period_id' => $periodId,
            'target_mode' => 'combination',
            'manual_color' => 'violet',
            'manual_size' => 'big',
        ]);
        $lockComboRes->assertRedirect();
        $period->refresh();
        $this->assertTrue((bool) $period->is_manual);
        $this->assertEquals('violet', $period->manual_color);
        $this->assertEquals('big', $period->manual_size);
        $this->assertNull($period->manual_number);
    }

    /**
     * Step 5: Test Period Settlement, Winnings Payout, Commission deduction, and History records.
     */
    public function test_period_settlement_and_payout_distribution(): void
    {
        $player = $this->createPlayer(1000.00);
        $periodData = WingoEngine::getCurrentPeriod('30s');
        $period = $periodData['period_model'];

        // Player places 3 bets on this period:
        // Bet 1: Number 7 (₹100) -> 9X -> Gross: ₹900, Profit: ₹800, 10% fee: ₹80, Payout: ₹820
        $bet1Res = $this->actingAs($player)->postJson(route('api.wingo.place-bet'), [
            'game_type' => '30s',
            'bet_type' => 'number',
            'bet_selection' => '7',
            'unit_price' => 100,
            'multiplier' => 1,
        ]);
        $bet1Id = $bet1Res->json('bet_id');

        // Bet 2: Color Green (₹100) -> 2X -> Gross: ₹200, Profit: ₹100, 10% fee: ₹10, Payout: ₹190
        $bet2Res = $this->actingAs($player)->postJson(route('api.wingo.place-bet'), [
            'game_type' => '30s',
            'bet_type' => 'color',
            'bet_selection' => 'Green',
            'unit_price' => 100,
            'multiplier' => 1,
        ]);
        $bet2Id = $bet2Res->json('bet_id');

        // Bet 3: Color Red (₹100) -> Lost on Ball 7
        $bet3Res = $this->actingAs($player)->postJson(route('api.wingo.place-bet'), [
            'game_type' => '30s',
            'bet_type' => 'color',
            'bet_selection' => 'Red',
            'unit_price' => 100,
            'multiplier' => 1,
        ]);
        $bet3Id = $bet3Res->json('bet_id');

        // Initial balance after 3 x ₹100 bets: ₹700
        $player->refresh();
        $this->assertEquals('700.00', $player->balance);

        // Admin locks winning ball as #7
        $period->is_manual = true;
        $period->manual_number = 7;
        $period->save();

        // Settle Period
        $settledPeriod = WingoEngine::settlePeriod($period);
        $this->assertEquals('completed', $settledPeriod->status);
        $this->assertEquals(7, $settledPeriod->winning_number);
        $this->assertEquals('green', $settledPeriod->winning_color);
        $this->assertEquals('big', $settledPeriod->winning_size);

        // Check Bet 1 (Number 7) -> WON
        $b1 = WingoBet::find($bet1Id);
        $this->assertEquals('won', $b1->status);
        $this->assertEquals('820.00', $b1->win_amount);
        $this->assertEquals('80.00', $b1->commission_amount);

        // Check Bet 2 (Green) -> WON
        $b2 = WingoBet::find($bet2Id);
        $this->assertEquals('won', $b2->status);
        $this->assertEquals('190.00', $b2->win_amount);
        $this->assertEquals('10.00', $b2->commission_amount);

        // Check Bet 3 (Red) -> LOST
        $b3 = WingoBet::find($bet3Id);
        $this->assertEquals('lost', $b3->status);
        $this->assertEquals('0.00', $b3->win_amount);

        // Expected final player wallet: ₹700 + ₹820 + ₹190 = ₹1710.00
        $player->refresh();
        $this->assertEquals('1710.00', $player->balance);

        // Verify Transaction records for bet_won
        $wonTx = Transaction::where('user_id', $player->id)->where('type', 'bet_won')->get();
        $this->assertEquals(2, $wonTx->count());

        // Check Player My Bets API
        $myBetsRes = $this->actingAs($player)->getJson(route('api.wingo.my-bets', ['type' => '30s']));
        $myBetsRes->assertStatus(200);
        $this->assertCount(3, $myBetsRes->json('data'));

        // Check Completed History API
        $historyRes = $this->actingAs($player)->getJson(route('api.wingo.history', ['type' => '30s']));
        $historyRes->assertStatus(200);
        $this->assertEquals(7, $historyRes->json('data.0.winning_number'));

        // Check Admin Winners List
        $admin = $this->createAdmin();
        $winnersRes = $this->actingAs($admin)->get(route('admin.wingo.winners'));
        $winnersRes->assertStatus(200);
        $winnersRes->assertSee($player->name);
        $winnersRes->assertSee('820.00');
    }

    /**
     * Step 6: Test that betting is locked during the final 5 seconds of a period.
     */
    public function test_betting_is_locked_during_final_5_seconds(): void
    {
        $player = $this->createPlayer(500.00);

        // Move clock to 12:00:27 (only 3 seconds left in 30s cycle -> LOCKED)
        Carbon::setTestNow(Carbon::parse('2026-10-24 12:00:27', 'Asia/Kolkata'));

        $res = $this->actingAs($player)->postJson(route('api.wingo.place-bet'), [
            'game_type' => '30s',
            'bet_type' => 'color',
            'bet_selection' => 'Green',
            'unit_price' => 10,
            'multiplier' => 1,
        ]);

        $res->assertStatus(422);
        $res->assertJson([
            'success' => false,
            'message' => 'Betting is locked for the final 5 seconds. Please wait for the next round!',
        ]);

        // Balance must not change
        $player->refresh();
        $this->assertEquals('500.00', $player->balance);
    }

    /**
     * Step 7: Test auto-pilot algorithm automatically draws the number with minimum payout for maximum house profit.
     */
    public function test_auto_pilot_algorithm_picks_minimum_payout_for_maximum_house_profit(): void
    {
        $playerA = $this->createPlayer(2000.00);
        $playerB = $this->createPlayer(2000.00);

        // Period is not locked (12:00:10)
        Carbon::setTestNow(Carbon::parse('2026-10-24 12:00:10', 'Asia/Kolkata'));
        $periodData = WingoEngine::getCurrentPeriod('30s');
        $period = $periodData['period_model'];

        // Player A puts a massive bet on Green (₹1000)
        $this->actingAs($playerA)->postJson(route('api.wingo.place-bet'), [
            'game_type' => '30s',
            'bet_type' => 'color',
            'bet_selection' => 'Green',
            'unit_price' => 1000,
            'multiplier' => 1,
        ]);

        // Player B puts a small bet on Red (₹50)
        $this->actingAs($playerB)->postJson(route('api.wingo.place-bet'), [
            'game_type' => '30s',
            'bet_type' => 'color',
            'bet_selection' => 'Red',
            'unit_price' => 50,
            'multiplier' => 1,
        ]);

        // No manual override: is_manual = false
        $this->assertFalse((bool) $period->is_manual);

        // Settle Period
        $settled = WingoEngine::settlePeriod($period);

        // Green payout would be ₹1900. Red payout is only ₹71.25 on #0 or ₹95 on #2, #4, #6, #8.
        // Therefore, house algorithm picked #0 (red_violet, pays only 1.5X) to minimize house liability!
        $this->assertContains((int) $settled->winning_number, [0, 2, 4, 6, 8]);
        $this->assertStringContainsString('red', $settled->winning_color);

        // Player B should win, Player A should lose
        $playerA->refresh();
        $playerB->refresh();
        $this->assertEquals('1000.00', $playerA->balance); // Lost ₹1000
        $this->assertGreaterThan(2000.00, (float) $playerB->balance); // Won payout
    }

    /**
     * Step 8: Test all 6 color + size combinations.
     */
    public function test_combinations_evaluate_correctly(): void
    {
        $player = $this->createPlayer(600.00);
        Carbon::setTestNow(Carbon::parse('2026-10-24 12:00:10', 'Asia/Kolkata'));
        $periodData = WingoEngine::getCurrentPeriod('30s');
        $period = $periodData['period_model'];

        // Place all 6 combinations (₹100 each)
        $combos = [
            'green_big' => 'Green + Big',
            'green_small' => 'Green + Small',
            'red_big' => 'Red + Big',
            'red_small' => 'Red + Small',
            'violet_big' => 'Violet + Big',
            'violet_small' => 'Violet + Small',
        ];

        $betIds = [];
        foreach ($combos as $key => $selection) {
            $res = $this->actingAs($player)->postJson(route('api.wingo.place-bet'), [
                'game_type' => '30s',
                'bet_type' => 'combination',
                'bet_selection' => $selection,
                'unit_price' => 100,
                'multiplier' => 1,
            ]);
            $betIds[$key] = $res->json('bet_id');
        }

        // Lock ball as 5 (Violet + Big)
        $period->is_manual = true;
        $period->manual_number = 5;
        $period->save();

        WingoEngine::settlePeriod($period);

        // Only violet_big should win, all other 5 must lose
        $this->assertEquals('won', WingoBet::find($betIds['violet_big'])->status);
        $this->assertEquals('lost', WingoBet::find($betIds['green_big'])->status);
        $this->assertEquals('lost', WingoBet::find($betIds['green_small'])->status);
        $this->assertEquals('lost', WingoBet::find($betIds['red_big'])->status);
        $this->assertEquals('lost', WingoBet::find($betIds['red_small'])->status);
        $this->assertEquals('lost', WingoBet::find($betIds['violet_small'])->status);
    }
}
