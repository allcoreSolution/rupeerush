<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SportsMarket extends Model
{
    use HasFactory;

    protected $table = 'sports_markets';

    protected $fillable = [
        'match_id',
        'market_type',
        'market_name',
        'status',
        'winning_runner_id',
        'settled_at',
    ];

    protected $casts = [
        'settled_at' => 'datetime',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(SportsMatch::class, 'match_id');
    }

    public function runners(): HasMany
    {
        return $this->hasMany(SportsRunner::class, 'market_id');
    }

    public function bets(): HasMany
    {
        return $this->hasMany(SportsBet::class, 'market_id');
    }

    public function winningRunner(): BelongsTo
    {
        return $this->belongsTo(SportsRunner::class, 'winning_runner_id');
    }
}

