<?php

namespace App\Models;

use App\Services\AndarBaharEngine;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AndarBaharBet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'round_id',
        'bet_type',
        'bet_selection',
        'amount',
        'win_multiplier',
        'commission_amount',
        'win_amount',
        'status',
        'evaluated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'win_multiplier' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'win_amount' => 'decimal:2',
            'evaluated_at' => 'datetime',
        ];
    }

    protected $appends = ['multiplier'];

    public function getMultiplierAttribute(): float
    {
        return (float) ($this->attributes['win_multiplier'] ?? AndarBaharEngine::MAIN_MULTIPLIER);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(AndarBaharRound::class, 'round_id', 'round_id');
    }
}
