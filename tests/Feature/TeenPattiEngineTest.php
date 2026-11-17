<?php

namespace Tests\Feature;

use App\Models\TeenPattiBet;
use App\Models\TeenPattiRound;
use App\Models\User;
use App\Services\TeenPattiEngine;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeenPattiEngineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2026, 10, 27, 12, 0, 10, 'Asia/Kolkata'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_deck_generation_has_52_unique_cards(): void
    {
        $deck = TeenPattiEngine::getFreshDeck();
        $this->assertCount(52, $deck);
        $this->assertCount(52, array_unique($deck));
        $this->assertContains('AS', $deck);
        $this->assertContains('2C', $deck);
    }

    public function test_card_parser(): void
    {
        $card = TeenPattiEngine::parseCard('AH');
        $this->assertEquals('AH', $card['code']);
        $this->assertEquals('A', $card['rank']);
        $this->assertEquals(14, $card['value']);
        $this->assertEquals('H', $card['suit']);
        $this->assertEquals('♥', $card['symbol']);
        $this->assertTrue($card['is_red']);

        $spade = TeenPattiEngine::parseCard('10S');
        $this->assertEquals('10S', $spade['code']);
        $this->assertEquals(10, $spade['value']);
        $this->assertEquals('♠', $spade['symbol']);
        $this->assertFalse($spade['is_red']);
    }

    public function test_hand_evaluator_ranks(): void
    {
        // 1. Trio
        $trio = TeenPattiEngine::evaluateHand(['AS', 'AH', 'AD']);
        $this->assertEquals('trio', $trio['rank_type']);
        $this->assertEquals(6, $trio['rank_tier']);

        // 2. Pure Sequence
        $pureSeq = TeenPattiEngine::evaluateHand(['KS', 'QS', 'JS']);
        $this->assertEquals('pure_sequence', $pureSeq['rank_type']);
        $this->assertEquals(5, $pureSeq['rank_tier']);

        // 3. Normal Sequence (A-2-3 standard Indian Teen Patti rule)
        $a23 = TeenPattiEngine::evaluateHand(['AH', '2S', '3D']);
        $this->assertEquals('sequence', $a23['rank_type']);
        $this->assertEquals(4, $a23['rank_tier']);

        $kqj = TeenPattiEngine::evaluateHand(['KD', 'QS', 'JH']);
        $this->assertEquals('sequence', $kqj['rank_type']);
        $this->assertTrue($a23['score'] > $kqj['score']); // A-2-3 beats K-Q-J in Teen Patti

        // 4. Color / Flush
        $color = TeenPattiEngine::evaluateHand(['AS', '9S', '4S']);
        $this->assertEquals('color', $color['rank_type']);
        $this->assertEquals(3, $color['rank_tier']);

        // 5. Pair
        $pair = TeenPattiEngine::evaluateHand(['KH', 'KD', '7C']);
        $this->assertEquals('pair', $pair['rank_type']);
        $this->assertEquals(2, $pair['rank_tier']);

        // 6. High Card
        $high = TeenPattiEngine::evaluateHand(['AH', '9D', '4S']);
        $this->assertEquals('high_card', $high['rank_type']);
        $this->assertEquals(1, $high['rank_tier']);

        // Verify Tier Hierarchy: Trio > Pure Sequence > Sequence > Color > Pair > High Card
        $this->assertTrue($trio['score'] > $pureSeq['score']);
        $this->assertTrue($pureSeq['score'] > $kqj['score']);
        $this->assertTrue($kqj['score'] > $color['score']);
        $this->assertTrue($color['score'] > $pair['score']);
        $this->assertTrue($pair['score'] > $high['score']);
    }

    public function test_hand_comparator(): void
    {
        $handA = TeenPattiEngine::evaluateHand(['AS', 'AH', 'AD']); // Trio AAA
        $handB = TeenPattiEngine::evaluateHand(['KS', 'KH', 'KD']); // Trio KKK
        $this->assertEquals('player_a', TeenPattiEngine::compareHands($handA, $handB));

        // Equal hands tie
        $handC = TeenPattiEngine::evaluateHand(['AS', 'KD', '9C']);
        $handD = TeenPattiEngine::evaluateHand(['AH', 'KC', '9D']);
        $this->assertEquals('tie', TeenPattiEngine::compareHands($handC, $handD));
    }

    public function test_get_current_round(): void
    {
        $data = TeenPattiEngine::getCurrentRound();
        $this->assertNotEmpty($data['round_id']);
        $this->assertNotNull($data['round_model']);
        $this->assertIsInt($data['remaining_seconds']);
        $this->assertIsBool($data['is_locked']);
        $this->assertContains($data['status'], ['betting', 'dealing']);
    }

    public function test_user_can_place_bet(): void
    {
        $user = User::factory()->create([
            'balance' => 500.00,
        ]);

        $res = TeenPattiEngine::placeBet($user, 'player_a', 50.00);

        $this->assertTrue($res['success']);
        $this->assertEquals(450.00, $res['new_balance']);
        $this->assertNotNull($res['bet']);
        $this->assertEquals('player_a', $res['bet']['bet_type']);
        $this->assertEquals(50.00, (float) $res['bet']['bet_amount']);

        $user->refresh();
        $this->assertEquals(450.00, (float) $user->balance);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'bet_placed',
            'amount' => 50.00,
        ]);
    }

    public function test_deal_and_settle_round_settles_and_credits_winners(): void
    {
        $userA = User::factory()->create(['balance' => 100.00]);
        $userB = User::factory()->create(['balance' => 100.00]);

        $round = TeenPattiRound::create([
            'round_id' => 'TPTEST99999',
            'status' => 'betting',
            'is_manual' => true,
            'manual_winner' => 'player_a',
        ]);

        $betA = TeenPattiBet::create([
            'bet_id' => 'BET_A_1',
            'round_id' => $round->round_id,
            'user_id' => $userA->id,
            'bet_type' => 'player_a',
            'bet_amount' => 50.00,
            'odds' => 1.95,
            'status' => 'pending',
        ]);

        $betB = TeenPattiBet::create([
            'bet_id' => 'BET_B_1',
            'round_id' => $round->round_id,
            'user_id' => $userB->id,
            'bet_type' => 'player_b',
            'bet_amount' => 50.00,
            'odds' => 1.95,
            'status' => 'pending',
        ]);

        // Settle the round
        TeenPattiEngine::dealAndSettleRound($round);

        $round->refresh();
        $this->assertEquals('completed', $round->status);
        $this->assertEquals('player_a', $round->winner);

        $betA->refresh();
        $this->assertEquals('won', $betA->status);
        // Gross win = 50 * 1.95 = 97.50. Profit = 47.50. 10% Fee = 4.75. Net payout = 92.75
        $this->assertEquals(92.75, (float) $betA->payout_amount);
        $this->assertEquals(4.75, (float) $betA->commission_amount);

        $betB->refresh();
        $this->assertEquals('lost', $betB->status);
        $this->assertEquals(0.00, (float) $betB->payout_amount);

        $userA->refresh();
        $this->assertEquals(192.75, (float) $userA->balance); // 100 + 92.75

        $userB->refresh();
        $this->assertEquals(100.00, (float) $userB->balance);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $userA->id,
            'type' => 'bet_won',
            'amount' => 92.75,
        ]);
    }

    public function test_pair_plus_side_bet_payout(): void
    {
        $user = User::factory()->create(['balance' => 100.00]);

        $round = TeenPattiRound::create([
            'round_id' => 'TPTEST_PAIR',
            'status' => 'betting',
        ]);

        $bet = TeenPattiBet::create([
            'bet_id' => 'BET_PAIR_1',
            'round_id' => $round->round_id,
            'user_id' => $user->id,
            'bet_type' => 'pair_plus_a',
            'bet_amount' => 20.00,
            'odds' => 40.00,
            'status' => 'pending',
        ]);

        // Settle round
        TeenPattiEngine::dealAndSettleRound($round);

        $bet->refresh();
        $this->assertContains($bet->status, ['won', 'lost']);
        if ($bet->status === 'won') {
            $this->assertGreaterThan(0, $bet->payout_amount);
        }
    }
}
