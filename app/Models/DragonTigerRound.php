<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DragonTigerRound extends Model
{
    use HasFactory;

    protected $fillable = [
        'round_id',
        'start_time',
        'end_time',
        'status',
        'dragon_card',
        'tiger_card',
        'winner',
        'is_manual',
        'manual_winner',
        'total_bets_count',
        'total_bet_amount',
        'total_payout_amount',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'dragon_card' => 'array',
            'tiger_card' => 'array',
            'is_manual' => 'boolean',
            'total_bet_amount' => 'decimal:2',
            'total_payout_amount' => 'decimal:2',
        ];
    }

    public function bets(): HasMany
    {
        return $this->hasMany(DragonTigerBet::class, 'round_id', 'round_id');
    }
}

