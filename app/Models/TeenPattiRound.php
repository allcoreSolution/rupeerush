<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeenPattiRound extends Model
{
    use HasFactory;

    protected $fillable = [
        'round_id',
        'start_time',
        'end_time',
        'status',
        'player_a_cards',
        'player_b_cards',
        'player_a_rank_type',
        'player_a_rank_title',
        'player_b_rank_type',
        'player_b_rank_title',
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
            'player_a_cards' => 'array',
            'player_b_cards' => 'array',
            'is_manual' => 'boolean',
            'total_bet_amount' => 'decimal:2',
            'total_payout_amount' => 'decimal:2',
        ];
    }

    public function bets(): HasMany
    {
        return $this->hasMany(TeenPattiBet::class, 'round_id', 'round_id');
    }
}
