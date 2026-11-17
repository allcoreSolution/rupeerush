<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AndarBaharRound extends Model
{
    use HasFactory;

    protected $fillable = [
        'round_id',
        'start_time',
        'end_time',
        'joker_card',
        'dealt_cards',
        'winning_side',
        'winning_card',
        'total_cards_dealt',
        'status',
        'is_manual',
        'manual_winning_side',
        'total_bets_count',
        'total_bet_amount',
        'total_payout_amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'joker_card' => 'array',
            'dealt_cards' => 'array',
            'winning_card' => 'array',
            'is_manual' => 'boolean',
            'total_cards_dealt' => 'integer',
            'total_bets_count' => 'integer',
            'total_bet_amount' => 'decimal:2',
            'total_payout_amount' => 'decimal:2',
        ];
    }

    public function bets(): HasMany
    {
        return $this->hasMany(AndarBaharBet::class, 'round_id', 'round_id');
    }
}
