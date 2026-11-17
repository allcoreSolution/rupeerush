<?php

namespace App\Models;

use Database\Factories\HorseRaceRoundFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HorseRaceRound extends Model
{
    /** @use HasFactory<HorseRaceRoundFactory> */
    use HasFactory;

    protected $fillable = [
        'round_id',
        'start_time',
        'end_time',
        'status',
        'winner_horse_id',
        'second_horse_id',
        'third_horse_id',
        'horses_data',
        'race_distance',
        'is_manual',
        'manual_winner_id',
        'seed_hash',
        'server_seed',
        'total_bets_count',
        'total_bet_amount',
        'total_payout_amount',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'horses_data' => 'array',
            'is_manual' => 'boolean',
            'winner_horse_id' => 'integer',
            'second_horse_id' => 'integer',
            'third_horse_id' => 'integer',
            'manual_winner_id' => 'integer',
            'race_distance' => 'integer',
            'total_bet_amount' => 'decimal:2',
            'total_payout_amount' => 'decimal:2',
        ];
    }

    public function bets(): HasMany
    {
        return $this->hasMany(HorseRaceBet::class, 'round_id', 'round_id');
    }
}
