<?php

namespace Tests\Feature;

use App\Models\GameSlot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        GameSlot::create([
            'slot_time' => '10:00 AM',
            'display_name' => '10:00 AM',
            'interval_minutes' => 20,
            'display_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
