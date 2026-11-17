<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DragonTigerBet extends Model
{
    use HasFactory;

    protected $fillable = [
        'bet_id',
        'round_id',
        'user_id',
        'bet_type',
        'bet_amount',
        'odds',
        'payout_amount',
        'net_profit',
        'commission_amount',
        'status',
        'hand_outcome',
    ];

    protected function casts(): array
    {
        return [
            'bet_amount' => 'decimal:2',
            'odds' => 'decimal:2',
            'payout_amount' => 'decimal:2',
            'net_profit' => 'decimal:2',
            'commission_amount' => 'decimal:2',
        ];
    }

    public function getWinAmountAttribute(): float
    {
        return (float) ($this->payout_amount ?? 0);
    }

    public function getWinMultiplierAttribute(): float
    {
        return (float) ($this->odds ?? 0);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(DragonTigerRound::class, 'round_id', 'round_id');
    }
}
