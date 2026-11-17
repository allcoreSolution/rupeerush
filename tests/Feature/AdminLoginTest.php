<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::create([
            'name' => 'Rupeerush Administrator',
            'email' => 'rupeerushonline@gmail.com',
            'phone' => '9026217223',
            'password' => Hash::make('Rush@2026'),
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_login_with_email_and_password(): void
    {
        $response = $this->post('/admin/login', [
            'login' => 'rupeerushonline@gmail.com',
            'password' => 'Rush@2026',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isAdmin());
    }

    public function test_admin_can_login_with_phone_and_password(): void
    {
        $response = $this->post('/admin/login', [
            'login' => '9026217223',
            'password' => 'Rush@2026',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isAdmin());
    }

    public function test_regular_player_cannot_login_to_admin_portal(): void
    {
        User::create([
            'name' => 'Player Test',
            'email' => 'player@example.com',
            'phone' => '9876543210',
            'password' => Hash::make('Player@123'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $response = $this->post('/admin/login', [
            'login' => 'player@example.com',
            'password' => 'Player@123',
        ]);

        $response->assertSessionHasErrors(['login']);
        $this->assertGuest();
    }
}
