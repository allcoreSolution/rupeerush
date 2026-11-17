<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSlot extends Model
{
    protected $fillable = [
        'slot_time',
        'display_order',
        'interval_minutes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
