<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    protected $fillable = [
        'user_id',
        'result_date',
        'slot_time',
        'slot_key',
        'chosen_number',
        'amount',
        'win_multiplier',
        'commission_amount',
        'win_amount',
        'status',
        'evaluated_at',
    ];

    protected function casts(): array
    {
        return [
            'result_date' => 'date',
            'amount' => 'decimal:2',
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
}
