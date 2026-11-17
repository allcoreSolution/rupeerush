<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VortexGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'user_id',
        'bet_amount',
        'current_ring',
        'current_multiplier',
        'payout_amount',
        'win_amount',
        'commission_amount',
        'part_cashed_out',
        'part_cashout_amount',
        'remaining_bet_amount',
        'spin_history',
        'status',
        'server_seed',
        'seed_hash',
        'cashed_out_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'bet_amount' => 'decimal:2',
            'current_ring' => 'integer',
            'current_multiplier' => 'decimal:2',
            'payout_amount' => 'decimal:2',
            'win_amount' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'part_cashed_out' => 'boolean',
            'part_cashout_amount' => 'decimal:2',
            'remaining_bet_amount' => 'decimal:2',
            'spin_history' => 'array',
            'cashed_out_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isFinished(): bool
    {
        return $this->status !== 'active';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if player can take 50% partial cashout.
     */
    public function canPartCashout(): bool
    {
        return $this->isActive() && ! $this->part_cashed_out && (float) $this->current_multiplier > 1.00;
    }
}
