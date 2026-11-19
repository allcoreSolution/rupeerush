<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameStatusManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Reset settings
        $setting = AdminSetting::current();
        foreach (['lottery', 'wingo', 'andar_bahar', 'aviator', 'mines', 'vortex', 'teen_patti', 'dragon_tiger', 'roulette', 'horse_racing', 'cricket'] as $g) {
            $setting->setGameActive($g, true);
        }
    }

    protected function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'phone' => '9999999999',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_view_game_status_management_hub(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.games.status'));

        $response->assertStatus(200);
        $response->assertSee('Platform Games On / Off Management');
        $response->assertSee('Aviator Crash');
        $response->assertSee('Mines 5x5 Arcade');
        $response->assertSee('European Roulette 36x');
    }

    public function test_admin_can_toggle_game_to_inactive_and_active(): void
    {
        $admin = $this->createAdmin();

        // Toggle Aviator to INACTIVE
        $response = $this->actingAs($admin)->postJson(route('admin.games.toggle-status', ['gameId' => 'aviator']), [
            'status' => false,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'game_id' => 'aviator',
            'is_active' => false,
        ]);

        $this->assertFalse(AdminSetting::current()->isGameActive('aviator'));

        // Toggle back to ACTIVE
        $response2 = $this->actingAs($admin)->postJson(route('admin.games.toggle-status', ['gameId' => 'aviator']), [
            'status' => true,
        ]);

        $response2->assertStatus(200);
        $response2->assertJson([
            'success' => true,
            'is_active' => true,
        ]);

        $this->assertTrue(AdminSetting::current()->isGameActive('aviator'));
    }

    public function test_inactive_game_blocks_player_page_and_shows_maintenance(): void
    {
        $setting = AdminSetting::current();
        $setting->setGameActive('aviator', false);

        $response = $this->get('/aviator');

        $response->assertStatus(503);
        $response->assertSee('Under Maintenance');
        $response->assertSee('Aviator Crash');

        // Restore
        $setting->setGameActive('aviator', true);
        $responseActive = $this->get('/aviator');
        $responseActive->assertStatus(200);
    }

    public function test_inactive_game_blocks_betting_api(): void
    {
        $setting = AdminSetting::current();
        $setting->setGameActive('aviator', false);

        $user = User::factory()->create([
            'role' => 'user',
            'phone' => '8888888888',
            'status' => 'active',
            'balance' => 500.00,
        ]);

        $response = $this->actingAs($user)->postJson(route('api.aviator.place-bet'), [
            'amount' => 10,
        ]);

        $response->assertStatus(503);
        $response->assertJson([
            'success' => false,
            'error' => 'game_inactive',
        ]);

        // Restore
        $setting->setGameActive('aviator', true);
    }

    public function test_bulk_activation_and_deactivation(): void
    {
        $admin = $this->createAdmin();

        // Bulk deactivate all
        $response = $this->actingAs($admin)->postJson(route('admin.games.bulk-status'), [
            'action' => 'deactivate_all',
        ]);

        $response->assertStatus(200);
        $this->assertFalse(AdminSetting::current()->isGameActive('mines'));
        $this->assertFalse(AdminSetting::current()->isGameActive('roulette'));

        // Bulk activate all
        $response2 = $this->actingAs($admin)->postJson(route('admin.games.bulk-status'), [
            'action' => 'activate_all',
        ]);

        $response2->assertStatus(200);
        $this->assertTrue(AdminSetting::current()->isGameActive('mines'));
        $this->assertTrue(AdminSetting::current()->isGameActive('roulette'));
    }
}
