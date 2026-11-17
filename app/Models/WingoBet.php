<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WingoBet extends Model
{
    protected $fillable = [
        'user_id',
        'game_type',
        'period_id',
        'bet_type',
        'bet_selection',
        'unit_price',
        'multiplier',
        'total_amount',
        'win_multiplier',
        'commission_amount',
        'win_amount',
        'status',
        'evaluated_at',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'multiplier' => 'integer',
            'total_amount' => 'decimal:2',
            'win_multiplier' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'win_amount' => 'decimal:2',
            'evaluated_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function period()
    {
        return $this->belongsTo(WingoPeriod::class, 'period_id', 'period_id');
    }
}
