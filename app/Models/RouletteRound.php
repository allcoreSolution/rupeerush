<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RouletteRound extends Model
{
    use HasFactory;

    protected $fillable = [
        'round_id',
        'start_time',
        'end_time',
        'status',
        'winning_number',
        'winning_color',
        'is_even',
        'is_high',
        'dozen',
        'column',
        'seed_hash',
        'server_seed',
        'is_manual',
        'manual_number',
        'total_bets_count',
        'total_bet_amount',
        'total_payout_amount',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'is_even' => 'boolean',
            'is_high' => 'boolean',
            'is_manual' => 'boolean',
            'winning_number' => 'integer',
            'manual_number' => 'integer',
            'dozen' => 'integer',
            'column' => 'integer',
            'total_bet_amount' => 'decimal:2',
            'total_payout_amount' => 'decimal:2',
        ];
    }

    public function bets(): HasMany
    {
        return $this->hasMany(RouletteBet::class, 'round_id', 'round_id');
    }
}
