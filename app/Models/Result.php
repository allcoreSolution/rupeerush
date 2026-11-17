<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    public const SLOT_KEYS = ['XA', 'XB', 'XC', 'XD', 'XE', 'XF', 'XG', 'XH', 'XI', 'XJ'];

    protected $fillable = [
        'result_date',
        'slot_time',
        'slot_datetime',
        'gen_datetime',
        'XA', 'XB', 'XC', 'XD', 'XE', 'XF', 'XG', 'XH', 'XI', 'XJ',
        'status',
        'is_manual',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'result_date' => 'date',
            'slot_datetime' => 'datetime',
            'gen_datetime' => 'datetime',
            'is_manual' => 'boolean',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getNumbersArray(): array
    {
        $numbers = [];
        foreach (self::SLOT_KEYS as $key) {
            $numbers[$key] = $this->{$key} ?? '--';
        }

        return $numbers;
    }
}
