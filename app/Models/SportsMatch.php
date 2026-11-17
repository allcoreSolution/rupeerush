<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SportsMatch extends Model
{
    use HasFactory;

    protected $table = 'sports_matches';

    protected $fillable = [
        'external_id',
        'sport',
        'series_name',
        'title',
        'match_type',
        'status',
        'team_a',
        'team_a_short',
        'team_a_img',
        'team_b',
        'team_b_short',
        'team_b_img',
        'venue',
        'score_team_a',
        'score_team_b',
        'current_summary',
        'recent_balls',
        'winner_team',
        'is_live',
        'is_featured',
        'starts_at',
        'settled_at',
        'meta_data',
    ];

    protected $casts = [
        'recent_balls' => 'array',
        'meta_data' => 'array',
        'is_live' => 'boolean',
        'is_featured' => 'boolean',
        'starts_at' => 'datetime',
        'settled_at' => 'datetime',
    ];

    public function markets(): HasMany
    {
        return $this->hasMany(SportsMarket::class, 'match_id');
    }

    public function bets(): HasMany
    {
        return $this->hasMany(SportsBet::class, 'match_id');
    }

    public function matchWinnerMarket()
    {
        return $this->hasOne(SportsMarket::class, 'match_id')->where('market_type', 'match_winner');
    }
}

