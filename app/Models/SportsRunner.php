<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SportsRunner extends Model
{
    use HasFactory;

    protected $table = 'sports_runners';

    protected $fillable = [
        'market_id',
        'runner_name',
        'back_odds',
        'lay_odds',
        'back_volume',
        'lay_volume',
        'status',
    ];

    protected $casts = [
        'back_odds' => 'float',
        'lay_odds' => 'float',
        'back_volume' => 'float',
        'lay_volume' => 'float',
    ];

    public function market(): BelongsTo
    {
        return $this->belongsTo(SportsMarket::class, 'market_id');
    }

    public function bets(): HasMany
    {
        return $this->hasMany(SportsBet::class, 'runner_id');
    }
}

