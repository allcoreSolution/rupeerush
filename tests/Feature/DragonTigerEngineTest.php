<?php

namespace Tests\Feature;

use App\Models\DragonTigerBet;
use App\Models\DragonTigerRound;
use App\Models\User;
use App\Services\DragonTigerEngine;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DragonTigerEngineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2026, 11, 2, 12, 0, 10, 'Asia/Kolkata'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_deck_generation_has_52_cards(): void
    {
        $deck = DragonTigerEngine::getFreshDeck();
        $this->assertCount(52, $deck);
        $this->assertCount(52, array_unique($deck));
        $this->assertContains('AS', $deck);
        $this->assertContains('KC', $deck);
    }

    public function test_card_parsing_ace_lowest_king_highest(): void
    {
        // In Dragon Tiger, Ace = 1 (lowest)
        $ace = DragonTigerEngine::parseCard('AH');
        $this->assertEquals(1, $ace['value']);
        $this->assertEquals('A', $ace['rank']);
        $this->assertTrue($ace['is_red']);
        $this->assertTrue($ace['is_small']);
        $this->assertFalse($ace['is_big']);

        // 7 is neutral house advantage
        $seven = DragonTigerEngine::parseCard('7S');
        $this->assertEquals(7, $seven['value']);
        $this->assertTrue($seven['is_seven']);
        $this->assertFalse($seven['is_big']);
        $this->assertFalse($seven['is_small']);

        // King = 13 (highest)
        $king = DragonTigerEngine::parseCard('KD');
        $this->assertEquals(13, $king['value']);
        $this->assertEquals('K', $king['rank']);
        $this->assertTrue($king['is_big']);
        $this->assertFalse($king['is_small']);
    }

    public function test_calculate_winner_comparison(): void
    {
        $cardDragon = DragonTigerEngine::parseCard('KD'); // 13
        $cardTiger = DragonTigerEngine::parseCard('10S'); // 10
        $this->assertEquals('dragon', DragonTigerEngine::calculateWinner($cardDragon, $cardTiger));

        $cardDragon2 = DragonTigerEngine::parseCard('3C'); // 3
        $cardTiger2 = DragonTigerEngine::parseCard('JH');  // 11
        $this->assertEquals('tiger', DragonTigerEngine::calculateWinner($cardDragon2, $cardTiger2));

        $cardTieD = DragonTigerEngine::parseCard('8H');
        $cardTieT = DragonTigerEngine::parseCard('8S');
        $this->assertEquals('tie', DragonTigerEngine::calculateWinner($cardTieD, $cardTieT));

        $cardSuitedTieD = DragonTigerEngine::parseCard('8H');
        $cardSuitedTieT = DragonTigerEngine::parseCard('8H');
        $this->assertEquals('suited_tie', DragonTigerEngine::calculateWinner($cardSuitedTieD, $cardSuitedTieT));
    }

    public function test_bet_placement_deducts_balance(): void
    {
        $user = User::factory()->create(['balance' => 1000.00]);

        $res = DragonTigerEngine::placeBet($user, 'dragon', 100.00);

        $this->assertTrue($res['success']);
        $this->assertEquals(900.00, $user->fresh()->balance);
        $this->assertDatabaseHas('dragon_tiger_bets', [
            'user_id' => $user->id,
            'bet_type' => 'dragon',
            'bet_amount' => 100.00,
            'status' => 'pending',
        ]);
    }

    public function test_settlement_dragon_win_credits_user(): void
    {
        $user = User::factory()->create(['balance' => 1000.00]);

        DragonTigerEngine::placeBet($user, 'dragon', 100.00);
        $this->assertEquals(900.00, $user->fresh()->balance);

        $roundData = DragonTigerEngine::getCurrentRound();
        /** @var DragonTigerRound $round */
        $round = $roundData['round_model'];

        // Force Dragon win
        $round->is_manual = true;
        $round->manual_winner = 'dragon';
        $round->save();

        DragonTigerEngine::dealAndSettleRound($round);

        $round->refresh();
        $this->assertEquals('completed', $round->status);
        $this->assertEquals('dragon', $round->winner);

        // 100 * 1.95 = 195 gross. Profit = 95. 10% Fee = 9.50. Net payout = 185.50
        $this->assertEquals(1085.50, (float) $user->fresh()->balance);

        $bet = DragonTigerBet::where('user_id', $user->id)->first();
        $this->assertEquals('won', $bet->status);
        $this->assertEquals(185.50, (float) $bet->payout_amount);
        $this->assertEquals(9.50, (float) $bet->commission_amount);
    }

    public function test_settlement_tie_gives_half_refund_to_dragon_and_tiger(): void
    {
        $user = User::factory()->create(['balance' => 1000.00]);

        DragonTigerEngine::placeBet($user, 'dragon', 100.00);
        $this->assertEquals(900.00, $user->fresh()->balance);

        $roundData = DragonTigerEngine::getCurrentRound();
        /** @var DragonTigerRound $round */
        $round = $roundData['round_model'];

        // Force Tie
        $round->is_manual = true;
        $round->manual_winner = 'tie';
        $round->save();

        DragonTigerEngine::dealAndSettleRound($round);

        $round->refresh();
        $this->assertEquals('completed', $round->status);
        $this->assertEquals('tie', $round->winner);

        // 50% refund = 50.00
        $this->assertEquals(950.00, $user->fresh()->balance);

        $bet = DragonTigerBet::where('user_id', $user->id)->first();
        $this->assertEquals('refunded', $bet->status);
        $this->assertEquals(50.00, (float) $bet->payout_amount);
    }
}
