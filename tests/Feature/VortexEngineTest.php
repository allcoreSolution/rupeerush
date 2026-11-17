<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VortexGame;
use App\Services\VortexEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VortexEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_start_vortex_game(): void
    {
        $user = User::factory()->create([
            'balance' => 500.00,
        ]);

        $res = VortexEngine::startGame($user, 50.00);

        $this->assertTrue($res['success']);
        $this->assertEquals(450.00, $res['new_balance']);
        $this->assertEquals(1, $res['game']['current_ring']);
        $this->assertEquals(50.00, $res['game']['payout_amount']);

        $game = VortexGame::where('user_id', $user->id)->first();
        $this->assertNotNull($game);
        $this->assertEquals('active', $game->status);
    }

    public function test_spin_mechanics_work(): void
    {
        $user = User::factory()->create([
            'balance' => 500.00,
        ]);

        VortexEngine::startGame($user, 50.00);

        $spinRes = VortexEngine::spin($user);
        $this->assertTrue($spinRes['success']);
        $this->assertArrayHasKey('result', $spinRes);
        $this->assertArrayHasKey('stop_angle', $spinRes['spin']);

        $game = VortexGame::where('user_id', $user->id)->first();
        $this->assertContains($game->status, ['active', 'busted']);
    }

    public function test_partial_cashout_credits_half_wallet(): void
    {
        $user = User::factory()->create([
            'balance' => 500.00,
        ]);

        VortexEngine::startGame($user, 50.00);
        $game = VortexGame::where('user_id', $user->id)->first();

        // Simulate game has reached 3.0x multiplier (Payout ₹150)
        $game->current_multiplier = 3.00;
        $game->payout_amount = 150.00;
        $game->save();

        $partRes = VortexEngine::partCashout($user);

        $this->assertTrue($partRes['success']);
        $this->assertEquals(75.00, $partRes['cashed_out_half']);
        $this->assertEquals(75.00, $partRes['remaining_payout']);

        $user->refresh();
        $this->assertEquals(525.00, (float) $user->balance); // 450 + 75 = 525

        $game->refresh();
        $this->assertTrue($game->part_cashed_out);
        $this->assertEquals(75.00, (float) $game->part_cashout_amount);
    }

    public function test_cashout_credits_remaining_and_completes_game(): void
    {
        $user = User::factory()->create([
            'balance' => 500.00,
        ]);

        VortexEngine::startGame($user, 50.00);
        $game = VortexGame::where('user_id', $user->id)->first();

        // Simulate 1 spin and 2.0x multiplier
        $game->spin_history = [['ring' => 1, 'multiplier' => 2.0]];
        $game->current_multiplier = 2.00;
        $game->payout_amount = 100.00;
        $game->save();

        $cashRes = VortexEngine::cashOut($user);

        $this->assertTrue($cashRes['success']);
        $this->assertEquals('cashed_out', $cashRes['status']);

        $game->refresh();
        $this->assertEquals('cashed_out', $game->status);
        $this->assertGreaterThan(50.00, (float) $game->win_amount);
    }
}
