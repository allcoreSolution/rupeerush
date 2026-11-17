<?php

namespace Tests\Feature;

use App\Models\AdminSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $setting = AdminSetting::current();
        $setting->update([
            'support_whatsapp' => '9026217223',
            'site_name' => 'red247.online',
        ]);
    }

    public function test_register_route_redirects_directly_to_whatsapp(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(302);
        $targetUrl = $response->headers->get('Location');
        $this->assertStringContainsString('https://wa.me/919026217223', $targetUrl);
        $this->assertStringContainsString('register', strtolower($targetUrl));
    }

    public function test_register_route_preserves_referral_code_in_whatsapp_message(): void
    {
        $response = $this->get(route('register', ['ref' => 'REFTEST99']));

        $response->assertStatus(302);
        $targetUrl = $response->headers->get('Location');
        $this->assertStringContainsString('https://wa.me/919026217223', $targetUrl);
        $this->assertStringContainsString('REFTEST99', $targetUrl);
    }

    public function test_admin_can_create_player_and_player_can_login(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Admin creates player account
        $createResponse = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Aakash Verma',
            'phone' => '9123456789',
            'password' => 'secretPass123',
            'email' => 'aakash@example.com',
            'balance' => 250,
        ]);

        $createResponse->assertSessionHas('new_player_credentials');
        $this->assertDatabaseHas('users', [
            'name' => 'Aakash Verma',
            'phone' => '9123456789',
            'role' => 'user',
            'balance' => 250.00,
        ]);

        // Player logs in with credentials provided by admin
        $this->post(route('logout'));

        $loginResponse = $this->post(route('login.post'), [
            'login' => '9123456789',
            'password' => 'secretPass123',
        ]);

        $loginResponse->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertEquals('9123456789', auth()->user()->phone);
    }
}
