<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoPlayerTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_player_can_login_via_demo_login_route(): void
    {
        $response = $this->get(route('demo.login'));

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals('demoplayer', $user->username);
        $this->assertTrue($user->isDemo());
        $this->assertGreaterThanOrEqual(10000.00, (float) $user->balance);
    }

    public function test_demo_player_cannot_access_deposit_page(): void
    {
        $demoUser = User::factory()->create([
            'username' => 'demoplayer',
            'email' => 'demo@rupeerush.com',
            'role' => 'user',
        ]);

        $response = $this->actingAs($demoUser)->get(route('wallet.deposit'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');
    }

    public function test_demo_player_cannot_access_withdraw_page(): void
    {
        $demoUser = User::factory()->create([
            'username' => 'demoplayer',
            'email' => 'demo@rupeerush.com',
            'role' => 'user',
        ]);

        $response = $this->actingAs($demoUser)->get(route('wallet.withdraw'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');
    }

    public function test_demo_player_cannot_submit_deposit(): void
    {
        $demoUser = User::factory()->create([
            'username' => 'demoplayer',
            'email' => 'demo@rupeerush.com',
            'role' => 'user',
        ]);

        $response = $this->actingAs($demoUser)->post(route('wallet.deposit.submit'), [
            'amount' => 500,
        ]);

        $response->assertSessionHas('error');
    }

    public function test_demo_player_cannot_submit_withdrawal(): void
    {
        $demoUser = User::factory()->create([
            'username' => 'demoplayer',
            'email' => 'demo@rupeerush.com',
            'role' => 'user',
            'balance' => 5000.00,
        ]);

        $response = $this->actingAs($demoUser)->post(route('wallet.withdraw.submit'), [
            'amount' => 500,
        ]);

        $response->assertSessionHas('error');
    }
}
