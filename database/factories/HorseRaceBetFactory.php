<?php

namespace Database\Factories;

use App\Models\HorseRaceBet;
use App\Models\HorseRaceRound;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<HorseRaceBet>
 */
class HorseRaceBetFactory extends Factory
{
    protected $model = HorseRaceBet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bet_id' => 'HRB_'.strtoupper(Str::random(12)),
            'round_id' => HorseRaceRound::factory(),
            'user_id' => User::factory(),
            'horse_id' => 1,
            'horse_name' => 'Thunder Bolt',
            'bet_type' => 'win',
            'bet_amount' => 100.00,
            'odds' => 2.50,
            'payout_amount' => 0.00,
            'net_profit' => 0.00,
            'status' => 'pending',
        ];
    }
}
