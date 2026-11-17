<?php

namespace Tests\Feature;

use App\Models\AdminRole;
use App\Models\AdminSetting;
use App\Models\HorseRaceBet;
use App\Models\User;
use App\Services\HorseRacingEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HorseRacingAdminTest extends TestCase
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
                'description' => 'Master access',
                'permissions' => array_keys(AdminRole::permissionCatalog()),
                'is_system' => true,
            ]
        );

        $this->admin = User::create([
            'name' => 'Master Admin',
            'username' => 'master_admin',
            'phone' => '9999900000',
            'email' => 'admin@derby.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'admin_role_id' => $role->id,
        ]);

        $this->player = User::factory()->create([
            'name' => 'Regular Bettor',
            'phone' => '9876540000',
            'balance' => 5000.00,
        ]);
    }

    public function test_admin_can_access_horse_racing_control_hub(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.horse-racing.index'));

        $response->assertStatus(200);
        $response->assertSee('Horse Racing Derby Live Control Hub');
        $response->assertSee('Realtime House Liability Breakdown');
        $response->assertSee('1-Click Forced Winner');
        $response->assertSee('Thunder Bolt');
        $response->assertSee('Golden Mane');
    }

    public function test_admin_can_set_one_click_manual_winner(): void
    {
        $roundData = HorseRacingEngine::getCurrentRound();
        $round = $roundData['round_model'];

        $payload = [
            'round_id' => $round->round_id,
            'manual_winner_id' => 4, // Desert King
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.horse-racing.set-winner'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $round->refresh();
        $this->assertTrue($round->is_manual);
        $this->assertEquals(4, $round->manual_winner_id);

        $setting = AdminSetting::current();
        $this->assertEquals(4, $setting->horse_racing_manual_winner);
        $this->assertEquals('manual', $setting->horse_racing_control_mode);
    }

    public function test_admin_can_clear_manual_winner(): void
    {
        $roundData = HorseRacingEngine::getCurrentRound();
        $round = $roundData['round_model'];
        $round->update([
            'is_manual' => true,
            'manual_winner_id' => 2,
        ]);

        $setting = AdminSetting::current();
        $setting->update([
            'horse_racing_manual_winner' => 2,
            'horse_racing_control_mode' => 'manual',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.horse-racing.clear-manual'), [
            'round_id' => $round->round_id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $round->refresh();
        $this->assertFalse($round->is_manual);
        $this->assertNull($round->manual_winner_id);

        $setting->refresh();
        $this->assertNull($setting->horse_racing_manual_winner);
        $this->assertEquals('fair', $setting->horse_racing_control_mode);
    }

    public function test_admin_can_update_horse_racing_parameters(): void
    {
        $payload = [
            'horse_racing_min_bet' => 20.00,
            'horse_racing_max_bet' => 50000.00,
            'horse_racing_round_duration' => 90,
            'horse_racing_control_mode' => 'admin_profit',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.horse-racing.settings'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $setting = AdminSetting::current();
        $this->assertEquals(20.00, (float) $setting->horse_racing_min_bet);
        $this->assertEquals(50000.00, (float) $setting->horse_racing_max_bet);
        $this->assertEquals(90, $setting->horse_racing_round_duration);
        $this->assertEquals('admin_profit', $setting->horse_racing_control_mode);
    }

    public function test_admin_can_view_winners_report(): void
    {
        $todayWinner = HorseRaceBet::create([
            'bet_id' => 'HRB_WIN_001',
            'round_id' => 'HR26102900010',
            'user_id' => $this->player->id,
            'horse_id' => 1,
            'horse_name' => 'Thunder Bolt',
            'bet_type' => 'win',
            'bet_amount' => 500.00,
            'odds' => 2.60,
            'payout_amount' => 1300.00,
            'net_profit' => 800.00,
            'status' => 'won',
        ]);

        $yesterdayWinner = new HorseRaceBet([
            'bet_id' => 'HRB_WIN_002',
            'round_id' => 'HR26102800005',
            'user_id' => $this->player->id,
            'horse_id' => 4,
            'horse_name' => 'Desert King',
            'bet_type' => 'win',
            'bet_amount' => 300.00,
            'odds' => 6.00,
            'payout_amount' => 1800.00,
            'net_profit' => 1500.00,
            'status' => 'won',
        ]);
        $yesterdayWinner->created_at = now()->subDays(2);
        $yesterdayWinner->saveQuietly();

        // 1. All-time winners
        $response = $this->actingAs($this->admin)->get(route('admin.horse-racing.winners'));

        $response->assertStatus(200);
        $response->assertSee('Horse Racing Derby Winner List');
        $response->assertSee('HRB_WIN_001');
        $response->assertSee('HRB_WIN_002');
        $response->assertSee('1,300.00');

        // 2. Day-specific filter (today only)
        $todayResponse = $this->actingAs($this->admin)->get(route('admin.horse-racing.winners', ['quick_filter' => 'today']));
        $todayResponse->assertStatus(200);
        $todayResponse->assertSee('HRB_WIN_001');
        $todayResponse->assertDontSee('HRB_WIN_002');

        // 3. Runner-specific filter (horse_id = 4)
        $runnerResponse = $this->actingAs($this->admin)->get(route('admin.horse-racing.winners', ['horse_id' => 4]));
        $runnerResponse->assertStatus(200);
        $runnerResponse->assertSee('HRB_WIN_002');
        $runnerResponse->assertDontSee('HRB_WIN_001');
    }

    public function test_regular_player_cannot_access_admin_horse_racing_control(): void
    {
        $response = $this->actingAs($this->player)->get(route('admin.horse-racing.index'));

        // Non-admin player should be denied access
        $this->assertTrue(in_array($response->status(), [403, 302], true));
    }
}
