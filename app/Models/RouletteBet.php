<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouletteBet extends Model
{
    use HasFactory;

    protected $fillable = [
        'bet_id',
        'round_id',
        'user_id',
        'bet_type',
        'bet_value',
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

    public function getAmountAttribute(): float
    {
        return (float) ($this->bet_amount ?? 0);
    }

    public function getSelectionLabelAttribute(): string
    {
        $typeLabelMap = [
            'straight' => 'Straight #'.$this->bet_value,
            'red' => 'RED (🔴)',
            'black' => 'BLACK (⚫)',
            'even' => 'EVEN',
            'odd' => 'ODD',
            'low' => 'LOW (1-18)',
            'high' => 'HIGH (19-36)',
            'dozen1' => '1st 12 (1-12)',
            'dozen2' => '2nd 12 (13-24)',
            'dozen3' => '3rd 12 (25-36)',
            'col1' => '1st Column',
            'col2' => '2nd Column',
            'col3' => '3rd Column',
            'split' => 'Split ('.$this->bet_value.')',
            'street' => 'Street ('.$this->bet_value.')',
            'corner' => 'Corner ('.$this->bet_value.')',
            'line' => 'Line ('.$this->bet_value.')',
        ];

        return $typeLabelMap[$this->bet_type] ?? (ucfirst($this->bet_type ?: 'Bet').': '.$this->bet_value);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(RouletteRound::class, 'round_id', 'round_id');
    }
}
