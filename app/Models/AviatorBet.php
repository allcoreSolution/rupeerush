<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AviatorBet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'round_id',
        'bet_slot',
        'amount',
        'auto_cashout',
        'cashout_multiplier',
        'commission_amount',
        'win_amount',
        'status',
        'cashed_out_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'bet_slot' => 'integer',
            'amount' => 'decimal:2',
            'auto_cashout' => 'decimal:2',
            'cashout_multiplier' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'win_amount' => 'decimal:2',
            'cashed_out_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(AviatorRound::class, 'round_id', 'round_id');
    }
}
