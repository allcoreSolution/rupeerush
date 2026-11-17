<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WingoPeriod extends Model
{
    protected $fillable = [
        'game_type',
        'period_id',
        'start_time',
        'end_time',
        'winning_number',
        'winning_color',
        'winning_size',
        'status',
        'is_manual',
        'manual_number',
        'manual_color',
        'manual_size',
        'total_bets_count',
        'total_bet_amount',
        'total_payout_amount',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'winning_number' => 'integer',
            'manual_number' => 'integer',
            'is_manual' => 'boolean',
            'total_bet_amount' => 'decimal:2',
            'total_payout_amount' => 'decimal:2',
        ];
    }

    public function bets()
    {
        return $this->hasMany(WingoBet::class, 'period_id', 'period_id');
    }

    /**
     * Map a number (0-9) to its standard color(s) and size.
     */
    public static function getAttributesForNumber(int $num): array
    {
        $colors = match ($num) {
            0 => ['red', 'violet'],
            5 => ['green', 'violet'],
            1, 3, 7, 9 => ['green'],
            2, 4, 6, 8 => ['red'],
            default => ['green'],
        };

        $colorStr = match ($num) {
            0 => 'red_violet',
            5 => 'green_violet',
            1, 3, 7, 9 => 'green',
            2, 4, 6, 8 => 'red',
            default => 'green',
        };

        $size = ($num >= 5) ? 'big' : 'small';

        return [
            'number' => $num,
            'colors' => $colors,
            'color_string' => $colorStr,
            'size' => $size,
        ];
    }
}
