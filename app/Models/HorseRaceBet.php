<?php

namespace App\Models;

use Database\Factories\HorseRaceBetFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HorseRaceBet extends Model
{
    /** @use HasFactory<HorseRaceBetFactory> */
    use HasFactory;

    protected $fillable = [
        'bet_id',
        'round_id',
        'user_id',
        'horse_id',
        'horse_name',
        'bet_type',
        'bet_amount',
        'odds',
        'payout_amount',
        'net_profit',
        'commission_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'horse_id' => 'integer',
            'bet_amount' => 'decimal:2',
            'odds' => 'decimal:2',
            'payout_amount' => 'decimal:2',
            'net_profit' => 'decimal:2',
            'commission_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(HorseRaceRound::class, 'round_id', 'round_id');
    }

    /**
     * Alias for bet_amount used in unified betting feeds.
     */
    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->bet_amount,
        );
    }

    /**
     * Human-friendly formatted selection label (e.g., "WIN: #1 Thunder Bolt").
     */
    protected function selectionLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $type = strtoupper($this->bet_type);

                return "{$type}: #{$this->horse_id} {$this->horse_name}";
            }
        );
    }
}
