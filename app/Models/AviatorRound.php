<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AviatorRound extends Model
{
    use HasFactory;

    protected $fillable = [
        'round_id',
        'start_time',
        'flight_start_time',
        'crash_time',
        'end_time',
        'crash_multiplier',
        'status',
        'is_manual',
        'manual_crash_multiplier',
        'seed_hash',
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
            'flight_start_time' => 'datetime',
            'crash_time' => 'datetime',
            'end_time' => 'datetime',
            'crash_multiplier' => 'decimal:2',
            'is_manual' => 'boolean',
            'manual_crash_multiplier' => 'decimal:2',
            'total_bets_count' => 'integer',
            'total_bet_amount' => 'decimal:2',
            'total_payout_amount' => 'decimal:2',
        ];
    }

    public function bets(): HasMany
    {
        return $this->hasMany(AviatorBet::class, 'round_id', 'round_id');
    }
}
