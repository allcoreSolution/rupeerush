<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\GameSlot;
use App\Models\TeenPattiBet;
use App\Models\TeenPattiRound;
use App\Models\User;
use App\Services\TeenPattiEngine;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeenPattiAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Deterministic clock: 12:00:10 (remaining: 15s in a 25s round)
        Carbon::setTestNow(Carbon::create(2026, 10, 27, 12, 0, 10, 'Asia/Kolkata'));

        if (GameSlot::count() === 0) {
            GameSlot::create([
                'slot_time' => '10:00 AM',
                'display_order' => 1,
                'interval_minutes' => 20,
                'is_active' => true,
            ]);
        }
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin Boss',
            'phone' => '9999999999',
            'email' => 'admin_tp@example.com',
            'password' => bcrypt('password123'),
            'balance' => 100000.00,
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    private function createPlayer(): User
    {
        return User::create([
            'name' => 'Player One',
            'phone' => '9888888888',
            'email' => 'player_tp@example.com',
            'password' => bcrypt('password123'),
            'balance' => 5000.00,
            'role' => 'user',
            'status' => 'active',
        ]);
    }

    public function test_guest_cannot_access_teen_patti_admin_panel(): void
    {
        $response = $this->get('/admin/teen-patti');
        $response->assertRedirect('/login');
    }

    public function test_regular_user_forbidden_from_teen_patti_admin_panel(): void
    {
        $player = $this->createPlayer();
        $response = $this->actingAs($player)->get('/admin/teen-patti');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_access_teen_patti_admin_panel(): void
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get('/admin/teen-patti');

        $response->assertStatus(200);
        $response->assertSee('20-20 Teen Patti Live Control Hub');
        $response->assertSee('FORCE PLAYER A');
        $response->assertSee('FORCE PLAYER B');
        $response->assertSee('FORCE TIE');
    }

    public function test_admin_can_set_manual_winner_for_active_round(): void
    {
        $admin = $this->createAdmin();
        $roundData = TeenPattiEngine::getCurrentRound();
        $roundId = $roundData['round_id'];

        $response = $this->actingAs($admin)->post('/admin/teen-patti/set-winner', [
            'round_id' => $roundId,
            'winner' => 'player_a',
        ]);

        $response->assertSessionHas('success');

        $round = TeenPattiRound::where('round_id', $roundId)->first();
        $this->assertTrue((bool) $round->is_manual);
        $this->assertEquals('player_a', $round->manual_winner);

        $setting = AdminSetting::current();
        $this->assertEquals('player_a', $setting->teen_patti_manual_winner);
    }

    public function test_admin_can_clear_manual_winner(): void
    {
        $admin = $this->createAdmin();
        $roundData = TeenPattiEngine::getCurrentRound();
        $roundId = $roundData['round_id'];

        // First set
        $this->actingAs($admin)->post('/admin/teen-patti/set-winner', [
            'round_id' => $roundId,
            'winner' => 'player_b',
        ]);

        // Then clear
        $response = $this->actingAs($admin)->post('/admin/teen-patti/clear-manual', [
            'round_id' => $roundId,
        ]);

        $response->assertSessionHas('success');

        $round = TeenPattiRound::where('round_id', $roundId)->first();
        $this->assertFalse((bool) $round->is_manual);
        $this->assertNull($round->manual_winner);

        $setting = AdminSetting::current();
        $this->assertNull($setting->teen_patti_manual_winner);
    }

    public function test_admin_can_change_control_mode(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/teen-patti/mode', [
            'mode' => 'house_trap',
        ]);

        $response->assertSessionHas('success');

        $setting = AdminSetting::current();
        $this->assertEquals('house_trap', $setting->teen_patti_control_mode);
    }

    public function test_admin_can_update_settings(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/teen-patti/settings', [
            'teen_patti_min_bet' => 10,
            'teen_patti_max_bet' => 50000,
            'teen_patti_odds_player' => 1.96,
            'teen_patti_odds_tie' => 9.50,
            'teen_patti_round_duration' => 30,
            'teen_patti_control_mode' => 'fair',
        ]);

        $response->assertSessionHas('success');

        $setting = AdminSetting::current();
        $this->assertEquals(10.00, (float) $setting->teen_patti_min_bet);
        $this->assertEquals(50000.00, (float) $setting->teen_patti_max_bet);
        $this->assertEquals(1.96, (float) $setting->teen_patti_odds_player);
        $this->assertEquals(9.50, (float) $setting->teen_patti_odds_tie);
        $this->assertEquals(30, (int) $setting->teen_patti_round_duration);
    }

    public function test_admin_can_view_winners_list(): void
    {
        $admin = $this->createAdmin();
        $player = $this->createPlayer();

        // Create dummy round and won bet
        $round = TeenPattiRound::create([
            'round_id' => 'TP26102799999',
            'start_time' => now(),
            'end_time' => now()->addSeconds(25),
            'status' => 'completed',
            'winner' => 'player_a',
            'winning_rank_title' => 'Trio AAA',
        ]);

        TeenPattiBet::create([
            'bet_id' => 'TPBTEST123456',
            'user_id' => $player->id,
            'round_id' => $round->round_id,
            'bet_type' => 'player_a',
            'bet_amount' => 1000.00,
            'odds' => 1.95,
            'status' => 'won',
            'payout_amount' => 1950.00,
        ]);

        $response = $this->actingAs($admin)->get('/admin/teen-patti/winners');
        $response->assertStatus(200);
        $response->assertSee('20-20 Teen Patti Winner List');
        $response->assertSee('TP26102799999');
        $response->assertSee('Player One');
        $response->assertSee('1,950.00');
    }
}
