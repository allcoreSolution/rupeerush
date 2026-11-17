<?php

namespace App\Services;

use App\Models\AdminSetting;
use App\Models\MinesGame;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MinesEngine
{
    public const GRID_SIZE = 25; // 5x5 board

    public const MIN_MINES = 3;

    public const MAX_MINES = 24;

    public const DEFAULT_HOUSE_EDGE = 0.03; // 3% house edge (97% RTP)

    /**
     * Calculate multiplier for given mines count and step number (1 to 25-mines).
     */
    public static function calculateMultiplier(int $minesCount, int $step, ?float $houseEdge = null): float
    {
        $minesCount = max(self::MIN_MINES, min(self::MAX_MINES, $minesCount));
        $grid = self::GRID_SIZE;
        $safe = $grid - $minesCount;

        if ($step < 1 || $step > $safe) {
            return 1.00;
        }

        if ($houseEdge === null) {
            $setting = AdminSetting::current();
            $houseEdge = (float) ($setting->mines_house_edge ?? (self::DEFAULT_HOUSE_EDGE * 100)) / 100.0;
        }

        $prob = 1.0;
        for ($i = 0; $i < $step; $i++) {
            $prob *= ($safe - $i) / ($grid - $i);
        }

        if ($prob <= 0) {
            return 1.00;
        }

        $raw = (1.0 / $prob) * (1.0 - $houseEdge);

        return max(1.01, round($raw, 2));
    }

    /**
     * Get multiplier ladder array for all steps up to (25 - minesCount).
     */
    public static function getMultiplierLadder(int $minesCount, ?float $houseEdge = null): array
    {
        $minesCount = max(self::MIN_MINES, min(self::MAX_MINES, $minesCount));
        $safe = self::GRID_SIZE - $minesCount;
        $ladder = [];

        for ($s = 1; $s <= $safe; $s++) {
            $ladder[] = [
                'step' => $s,
                'multiplier' => self::calculateMultiplier($minesCount, $s, $houseEdge),
            ];
        }

        return $ladder;
    }

    /**
     * Get active game for a user, or null.
     */
    public static function getActiveGame(User $user): ?MinesGame
    {
        return MinesGame::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Start a new Mines game.
     */
    public static function startGame(User $user, float $amount, int $minesCount): array
    {
        $setting = AdminSetting::current();
        $minBet = (float) ($setting->mines_min_bet ?? 1.00);
        $maxBet = (float) ($setting->mines_max_bet ?? 25000.00);

        if ($amount < $minBet || $amount > $maxBet) {
            return ['success' => false, 'message' => "Bet amount must be between ₹{$minBet} and ₹{$maxBet}."];
        }

        if ($minesCount < self::MIN_MINES || $minesCount > self::MAX_MINES) {
            return ['success' => false, 'message' => 'Mines count must be between '.self::MIN_MINES.' and '.self::MAX_MINES.'.'];
        }

        return DB::transaction(function () use ($user, $amount, $minesCount) {
            // Lock user
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            if (! $lockedUser || (float) $lockedUser->balance < $amount) {
                return ['success' => false, 'message' => 'Insufficient wallet balance. Please deposit funds.'];
            }

            // Check if there is already an active game for this user
            $existing = MinesGame::where('user_id', $lockedUser->id)
                ->where('status', 'active')
                ->first();

            if ($existing) {
                return [
                    'success' => false,
                    'message' => 'You already have an active game! Please cash out or complete it.',
                    'active_game' => self::formatGameState($existing, false),
                ];
            }

            // Deduct balance
            $balanceBefore = (float) $lockedUser->balance;
            $balanceAfter = round($balanceBefore - $amount, 2);
            $lockedUser->balance = $balanceAfter;
            $lockedUser->save();

            $gameId = 'MN'.Carbon::now()->format('ymdHis').mt_rand(10, 99);

            // Log bet transaction
            Transaction::create([
                'user_id' => $lockedUser->id,
                'type' => 'bet_placed',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => 'completed',
                'admin_notes' => "Placed ₹{$amount} on Mines Game #{$gameId} ({$minesCount} Mines)",
            ]);

            // Probably fair generation
            $serverSeed = bin2hex(random_bytes(32));
            $seedHash = hash('sha256', $serverSeed.':'.$gameId.':'.config('app.key'));

            // Generate mine positions (0 to 24 indices)
            $allIndices = range(0, self::GRID_SIZE - 1);
            shuffle($allIndices);
            $minePositions = array_slice($allIndices, 0, $minesCount);
            sort($minePositions);

            $game = MinesGame::create([
                'game_id' => $gameId,
                'user_id' => $lockedUser->id,
                'bet_amount' => $amount,
                'mines_count' => $minesCount,
                'grid_size' => self::GRID_SIZE,
                'mine_positions' => $minePositions,
                'jackpot_position' => null,
                'opened_tiles' => [],
                'current_step' => 0,
                'current_multiplier' => 1.00,
                'win_amount' => 0.00,
                'commission_amount' => 0.00,
                'is_jackpot_win' => false,
                'jackpot_win_amount' => 0.00,
                'status' => 'active',
                'server_seed' => $serverSeed,
                'seed_hash' => $seedHash,
            ]);

            return [
                'success' => true,
                'message' => 'Game started! Pick a tile.',
                'new_balance' => $balanceAfter,
                'game' => self::formatGameState($game, false),
            ];
        });
    }

    /**
     * Reveal a tile on the board.
     */
    public static function revealTile(User $user, int $tileIndex): array
    {
        if ($tileIndex < 0 || $tileIndex >= self::GRID_SIZE) {
            return ['success' => false, 'message' => 'Invalid tile selection.'];
        }

        return DB::transaction(function () use ($user, $tileIndex) {
            $game = MinesGame::where('user_id', $user->id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if (! $game) {
                return ['success' => false, 'message' => 'No active game found. Start a new game!'];
            }

            $opened = $game->opened_tiles ?? [];
            if (in_array($tileIndex, $opened, true)) {
                return ['success' => false, 'message' => 'This tile is already opened.'];
            }

            $opened[] = $tileIndex;
            $mines = $game->mine_positions ?? [];
            $hitMine = in_array($tileIndex, $mines, true);

            // Admin Control Interception (House advantage / Rigged traps / Step limit)
            if (! $hitMine) {
                $setting = AdminSetting::current();
                $mode = $setting->mines_control_mode ?? 'house_trap';
                $forceBlast = false;

                if ($mode === 'step_limit' && (int) $setting->mines_force_blast_step > 0) {
                    $nextStep = (int) $game->current_step + 1;
                    if ($nextStep > (int) $setting->mines_force_blast_step) {
                        $forceBlast = true;
                    }
                } elseif ($mode === 'house_trap' || $mode === 'fair') {
                    $nextStep = (int) $game->current_step + 1;
                    $nextMultiplier = self::calculateMultiplier((int) $game->mines_count, $nextStep);

                    // Check multiplier cap
                    if ((float) $setting->mines_max_win_multiplier > 0 && $nextMultiplier >= (float) $setting->mines_max_win_multiplier) {
                        $forceBlast = true;
                    }

                    // Check rigged chance (75-80% house profit rate)
                    $riggedRate = (float) ($setting->mines_rigged_rate ?: ($setting->house_profit_rate ?? 78.00));
                    if (! $forceBlast && $riggedRate > 0) {
                        $roll = mt_rand(1, 10000) / 100.0;
                        if ($roll <= $riggedRate) {
                            $forceBlast = true;
                        }
                    }
                }

                if ($forceBlast) {
                    // Turn this tile into a mine by swapping with an un-opened mine
                    $unopenedMines = array_values(array_diff($mines, $opened));
                    if (! empty($unopenedMines)) {
                        $mineToSwap = $unopenedMines[array_rand($unopenedMines)];
                        $mines = array_values(array_diff($mines, [$mineToSwap]));
                        $mines[] = $tileIndex;
                        sort($mines);
                        $game->mine_positions = $mines;
                        $hitMine = true;
                    }
                }
            }

            if ($hitMine) {
                // EXPLODED! Game Over!
                $game->opened_tiles = $opened;
                $game->status = 'exploded';
                $game->win_amount = 0.00;
                $game->save();

                return [
                    'success' => true,
                    'hit_mine' => true,
                    'status' => 'exploded',
                    'message' => 'BOOM! You hit a mine. Better luck next time!',
                    'tile_index' => $tileIndex,
                    'game' => self::formatGameState($game, true),
                ];
            }

            // SAFE TILE (Gold Coin)
            $newStep = (int) $game->current_step + 1;
            $multiplier = self::calculateMultiplier((int) $game->mines_count, $newStep);
            $potentialWin = round((float) $game->bet_amount * $multiplier, 2);

            $game->opened_tiles = $opened;
            $game->current_step = $newStep;
            $game->current_multiplier = $multiplier;
            $game->win_amount = $potentialWin;
            $game->save();

            // Check if player cleared ALL safe tiles (auto win & cashout)
            $totalSafe = self::GRID_SIZE - (int) $game->mines_count;
            if ($newStep >= $totalSafe) {
                return self::executeCashout($game, true);
            }

            return [
                'success' => true,
                'hit_mine' => false,
                'is_jackpot' => false,
                'status' => 'active',
                'message' => 'Safe tile revealed!',
                'tile_index' => $tileIndex,
                'step' => $newStep,
                'current_multiplier' => $multiplier,
                'potential_win' => $potentialWin,
                'next_multiplier' => self::calculateMultiplier((int) $game->mines_count, $newStep + 1),
                'game' => self::formatGameState($game, false),
            ];
        });
    }

    /**
     * Cash out current active game.
     */
    public static function cashOut(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $game = MinesGame::where('user_id', $user->id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if (! $game) {
                return ['success' => false, 'message' => 'No active game found to cash out.'];
            }

            if ((int) $game->current_step < 1) {
                return ['success' => false, 'message' => 'You must reveal at least 1 safe tile before cashing out.'];
            }

            return self::executeCashout($game, false);
        });
    }

    /**
     * Internal execution of cashout & wallet crediting.
     */
    protected static function executeCashout(MinesGame $game, bool $isAutoWin = false): array
    {
        $setting = AdminSetting::current();
        $user = User::where('id', $game->user_id)->lockForUpdate()->first();

        $grossWin = round((float) $game->bet_amount * (float) $game->current_multiplier, 2);

        // Commission on profit
        $profit = max(0.0, $grossWin - (float) $game->bet_amount);
        $commissionPercent = (float) ($setting->win_commission_percent ?? 0.0);
        $commissionAmount = round(($profit * $commissionPercent) / 100.0, 2);
        $netPayout = round($grossWin - $commissionAmount, 2);

        $game->status = 'cashed_out';
        $game->win_amount = $netPayout;
        $game->commission_amount = $commissionAmount;
        $game->cashed_out_at = Carbon::now();
        $game->save();

        // Credit user balance
        $balanceBefore = (float) $user->balance;
        $balanceAfter = round($balanceBefore + $netPayout, 2);
        $user->balance = $balanceAfter;
        $user->save();

        // Transaction log
        $notes = "Cashed out ₹{$netPayout} ({$game->current_multiplier}x) on Mines Game #{$game->game_id}";
        if ($commissionAmount > 0) {
            $notes .= " (Fee: ₹{$commissionAmount})";
        }

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'bet_won',
            'amount' => $netPayout,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'status' => 'completed',
            'admin_notes' => $notes,
        ]);

        return [
            'success' => true,
            'hit_mine' => false,
            'status' => 'cashed_out',
            'is_auto_win' => $isAutoWin,
            'message' => $isAutoWin
                ? "AMAZING! You cleared the entire board and won ₹{$netPayout}!"
                : "Successfully cashed out ₹{$netPayout} at {$game->current_multiplier}x!",
            'win_amount' => $netPayout,
            'gross_win' => $grossWin,
            'commission' => $commissionAmount,
            'new_balance' => $balanceAfter,
            'game' => self::formatGameState($game, true),
        ];
    }

    /**
     * Format game state for API response (safe vs revealed).
     */
    public static function formatGameState(MinesGame $game, bool $revealAll = false): array
    {
        $setting = AdminSetting::current();
        $isFinished = ($game->status !== 'active');
        $shouldReveal = $revealAll || $isFinished;

        $ladder = self::getMultiplierLadder((int) $game->mines_count);

        return [
            'id' => $game->id,
            'game_id' => $game->game_id,
            'status' => $game->status,
            'bet_amount' => (float) $game->bet_amount,
            'mines_count' => (int) $game->mines_count,
            'safe_count' => self::GRID_SIZE - (int) $game->mines_count,
            'current_step' => (int) $game->current_step,
            'current_multiplier' => (float) $game->current_multiplier,
            'potential_win' => round((float) $game->bet_amount * (float) $game->current_multiplier, 2),
            'win_amount' => (float) $game->win_amount,
            'opened_tiles' => $game->opened_tiles ?? [],
            'mine_positions' => $shouldReveal ? ($game->mine_positions ?? []) : null,
            'jackpot_position' => null,
            'is_jackpot_win' => false,
            'jackpot_win_amount' => 0.00,
            'jackpot_pool' => 0.00,
            'seed_hash' => $game->seed_hash,
            'server_seed' => $shouldReveal ? $game->server_seed : null,
            'ladder' => $ladder,
        ];
    }

    /**
     * Get player's recent Mines games history.
     */
    public static function getRecentGames(User $user, int $limit = 20): array
    {
        return MinesGame::where('user_id', $user->id)
            ->whereIn('status', ['cashed_out', 'exploded'])
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($g) {
                return [
                    'id' => $g->id,
                    'game_id' => $g->game_id,
                    'bet_amount' => (float) $g->bet_amount,
                    'mines_count' => (int) $g->mines_count,
                    'steps_cleared' => (int) $g->current_step,
                    'multiplier' => (float) $g->current_multiplier,
                    'win_amount' => (float) $g->win_amount,
                    'status' => $g->status,
                    'is_jackpot' => (bool) $g->is_jackpot_win,
                    'created_at' => $g->created_at->toISOString(),
                ];
            })
            ->toArray();
    }

    /**
     * Admin Force Bust an active game session.
     */
    public static function forceBust(int $gameId): array
    {
        return DB::transaction(function () use ($gameId) {
            $game = MinesGame::where('id', $gameId)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if (! $game) {
                return ['success' => false, 'message' => 'Active game not found or already completed.'];
            }

            $game->status = 'exploded';
            $game->win_amount = 0.00;
            $game->save();

            return ['success' => true, 'message' => "Game #{$game->game_id} has been forcefully busted (exploded)."];
        });
    }

    /**
     * Admin Force Cashout an active game session.
     */
    public static function forceCashout(int $gameId): array
    {
        return DB::transaction(function () use ($gameId) {
            $game = MinesGame::where('id', $gameId)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if (! $game) {
                return ['success' => false, 'message' => 'Active game not found or already completed.'];
            }

            if ((int) $game->current_step < 1) {
                // If 0 steps cleared, refund bet
                $user = User::where('id', $game->user_id)->lockForUpdate()->first();
                $refund = (float) $game->bet_amount;
                $balanceBefore = (float) $user->balance;
                $balanceAfter = round($balanceBefore + $refund, 2);
                $user->balance = $balanceAfter;
                $user->save();

                $game->status = 'cashed_out';
                $game->win_amount = $refund;
                $game->cashed_out_at = Carbon::now();
                $game->save();

                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'bet_won',
                    'amount' => $refund,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'status' => 'completed',
                    'admin_notes' => "Admin forcefully refunded ₹{$refund} on Mines Game #{$game->game_id}",
                ]);

                return ['success' => true, 'message' => "Game #{$game->game_id} had 0 steps opened; bet ₹{$refund} refunded to player wallet."];
            }

            return self::executeCashout($game, false);
        });
    }
}
