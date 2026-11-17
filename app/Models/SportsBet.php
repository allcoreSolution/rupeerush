<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SportsBet extends Model
{
    use HasFactory;

    protected $table = 'sports_bets';

    protected $fillable = [
        'bet_id',
        'user_id',
        'match_id',
        'market_id',
        'runner_id',
        'bet_type',
        'runner_name',
        'stake',
        'odds',
        'potential_payout',
        'win_amount',
        'commission_amount',
        'status',
        'settled_at',
    ];

    protected $casts = [
        'stake' => 'float',
        'odds' => 'float',
        'potential_payout' => 'float',
        'win_amount' => 'float',
        'commission_amount' => 'float',
        'settled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(SportsMatch::class, 'match_id');
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(SportsMarket::class, 'market_id');
    }

    public function runner(): BelongsTo
    {
        return $this->belongsTo(SportsRunner::class, 'runner_id');
    }
}
