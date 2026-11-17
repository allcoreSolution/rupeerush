<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinesGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'user_id',
        'bet_amount',
        'mines_count',
        'grid_size',
        'mine_positions',
        'jackpot_position',
        'opened_tiles',
        'current_step',
        'current_multiplier',
        'win_amount',
        'commission_amount',
        'is_jackpot_win',
        'jackpot_win_amount',
        'status',
        'server_seed',
        'seed_hash',
        'cashed_out_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'bet_amount' => 'decimal:2',
            'mines_count' => 'integer',
            'grid_size' => 'integer',
            'mine_positions' => 'array',
            'jackpot_position' => 'integer',
            'opened_tiles' => 'array',
            'current_step' => 'integer',
            'current_multiplier' => 'decimal:2',
            'win_amount' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'is_jackpot_win' => 'boolean',
            'jackpot_win_amount' => 'decimal:2',
            'cashed_out_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isFinished(): bool
    {
        return $this->status !== 'active';
    }

    public function isMine(int $index): bool
    {
        $mines = $this->mine_positions ?? [];

        return in_array($index, $mines, true);
    }

    public function isJackpot(int $index): bool
    {
        return $this->jackpot_position !== null && $this->jackpot_position === $index;
    }

    public function isOpened(int $index): bool
    {
        $opened = $this->opened_tiles ?? [];

        return in_array($index, $opened, true);
    }

    public function safeTilesCount(): int
    {
        return max(1, ($this->grid_size ?? 25) - ($this->mines_count ?? 3));
    }

    public function remainingSafeTilesCount(): int
    {
        return max(0, $this->safeTilesCount() - ($this->current_step ?? 0));
    }
}
