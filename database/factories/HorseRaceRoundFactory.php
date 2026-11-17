<?php

namespace Database\Factories;

use App\Models\HorseRaceRound;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<HorseRaceRound>
 */
class HorseRaceRoundFactory extends Factory
{
    protected $model = HorseRaceRound::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $serverSeed = bin2hex(random_bytes(16));

        return [
            'round_id' => 'HR'.now()->format('ymd').Str::padLeft(fake()->unique()->numberBetween(1, 9999), 5, '0'),
            'start_time' => now(),
            'end_time' => now()->addSeconds(60),
            'status' => 'completed',
            'winner_horse_id' => 1,
            'second_horse_id' => 2,
            'third_horse_id' => 3,
            'horses_data' => [
                ['id' => 1, 'name' => 'Thunder Bolt', 'silk' => 'red', 'odds_win' => 2.50, 'odds_place' => 1.50, 'odds_show' => 1.20],
                ['id' => 2, 'name' => 'Golden Arrow', 'silk' => 'gold', 'odds_win' => 3.80, 'odds_place' => 1.90, 'odds_show' => 1.35],
                ['id' => 3, 'name' => 'Shadow Runner', 'silk' => 'black', 'odds_win' => 5.50, 'odds_place' => 2.50, 'odds_show' => 1.60],
                ['id' => 4, 'name' => 'Royal Spirit', 'silk' => 'purple', 'odds_win' => 8.00, 'odds_place' => 3.20, 'odds_show' => 1.90],
                ['id' => 5, 'name' => 'Blazing Wind', 'silk' => 'orange', 'odds_win' => 4.50, 'odds_place' => 2.10, 'odds_show' => 1.45],
                ['id' => 6, 'name' => 'Emerald King', 'silk' => 'green', 'odds_win' => 12.00, 'odds_place' => 4.50, 'odds_show' => 2.30],
            ],
            'race_distance' => 1000,
            'is_manual' => false,
            'manual_winner_id' => null,
            'seed_hash' => hash('sha256', $serverSeed),
            'server_seed' => $serverSeed,
            'total_bets_count' => 0,
            'total_bet_amount' => 0.00,
            'total_payout_amount' => 0.00,
        ];
    }
}
