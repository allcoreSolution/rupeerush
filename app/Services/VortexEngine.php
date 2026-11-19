<?php

namespace App\Services;

use App\Models\AdminSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VortexGame;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VortexEngine
{
    public const RING_INNER = 1;

    public const RING_MIDDLE = 2;

    public const RING_OUTER = 3;

    public const DEFAULT_HOUSE_EDGE = 0.035; // 3.5% house edge

    /**
     * Ring sector configurations with multipliers, labels, colors, and types.
     */
    public static function getRingsConfig(): array
    {
        return [
            1 => [
                'name' => 'Water Ring (Inner)',
                'theme' => 'blue',
                'color' => '#0284c7',
                'glow' => 'rgba(2, 132, 199, 0.5)',
                'icon' => 'water',
                'total_sectors' => 8,
                'sector_angle' => 45.0, // 360 / 8
                'sectors' => [
                    ['id' => 0, 'label' => '1.55X', 'multiplier' => 1.55, 'type' => 'multiplier', 'advance' => false, 'icon' => '🌊', 'color' => '#38bdf8', 'weight' => 28],
                    ['id' => 1, 'label' => '+7X ⚡', 'multiplier' => 7.00, 'type' => 'advance', 'advance' => true, 'icon' => '⚡', 'color' => '#10b981', 'weight' => 14],
                    ['id' => 2, 'label' => '4.85X', 'multiplier' => 4.85, 'type' => 'multiplier', 'advance' => false, 'icon' => '🌊', 'color' => '#0284c7', 'weight' => 18],
                    ['id' => 3, 'label' => '10X ⭐', 'multiplier' => 10.00, 'type' => 'multiplier', 'advance' => false, 'icon' => '⭐', 'color' => '#f59e0b', 'weight' => 8],
                    ['id' => 4, 'label' => '💥 BLAST', 'multiplier' => 0.00, 'type' => 'bust', 'advance' => false, 'icon' => '💥', 'color' => '#ef4444', 'weight' => 20],
                    ['id' => 5, 'label' => '2.0X', 'multiplier' => 2.00, 'type' => 'multiplier', 'advance' => false, 'icon' => '🌊', 'color' => '#38bdf8', 'weight' => 22],
                    ['id' => 6, 'label' => '🌊 LEVEL UP', 'multiplier' => 3.00, 'type' => 'advance', 'advance' => true, 'icon' => '🌊', 'color' => '#06b6d4', 'weight' => 16],
                    ['id' => 7, 'label' => '💥 BLAST', 'multiplier' => 0.00, 'type' => 'bust', 'advance' => false, 'icon' => '💥', 'color' => '#ef4444', 'weight' => 18],
                ],
            ],
            2 => [
                'name' => 'Earth Ring (Middle)',
                'theme' => 'green',
                'color' => '#22c55e',
                'glow' => 'rgba(34, 197, 94, 0.5)',
                'icon' => 'leaf',
                'total_sectors' => 8,
                'sector_angle' => 45.0,
                'sectors' => [
                    ['id' => 0, 'label' => '2.5X', 'multiplier' => 2.50, 'type' => 'multiplier', 'advance' => false, 'icon' => '🦠', 'color' => '#4ade80', 'weight' => 26],
                    ['id' => 1, 'label' => '+20.5X 🔥', 'multiplier' => 20.50, 'type' => 'advance', 'advance' => true, 'icon' => '🔥', 'color' => '#f59e0b', 'weight' => 10],
                    ['id' => 2, 'label' => '7.7X', 'multiplier' => 7.70, 'type' => 'multiplier', 'advance' => false, 'icon' => '🦠', 'color' => '#22c55e', 'weight' => 18],
                    ['id' => 3, 'label' => '16X 💎', 'multiplier' => 16.00, 'type' => 'multiplier', 'advance' => false, 'icon' => '💎', 'color' => '#a855f7', 'weight' => 12],
                    ['id' => 4, 'label' => '💥 BLAST', 'multiplier' => 0.00, 'type' => 'bust', 'advance' => false, 'icon' => '💥', 'color' => '#ef4444', 'weight' => 24],
                    ['id' => 5, 'label' => '27.5X ⚡', 'multiplier' => 27.50, 'type' => 'multiplier', 'advance' => false, 'icon' => '⚡', 'color' => '#ec4899', 'weight' => 8],
                    ['id' => 6, 'label' => '🌿 LEVEL UP', 'multiplier' => 5.00, 'type' => 'advance', 'advance' => true, 'icon' => '🌿', 'color' => '#10b981', 'weight' => 14],
                    ['id' => 7, 'label' => '💥 BLAST', 'multiplier' => 0.00, 'type' => 'bust', 'advance' => false, 'icon' => '💥', 'color' => '#ef4444', 'weight' => 22],
                ],
            ],
            3 => [
                'name' => 'Fire Ring (Outer)',
                'theme' => 'red',
                'color' => '#ef4444',
                'glow' => 'rgba(239, 68, 68, 0.5)',
                'icon' => 'fire',
                'total_sectors' => 10,
                'sector_angle' => 36.0, // 360 / 10
                'sectors' => [
                    ['id' => 0, 'label' => '3.9X', 'multiplier' => 3.90, 'type' => 'multiplier', 'advance' => false, 'icon' => '🔥', 'color' => '#f87171', 'weight' => 24],
                    ['id' => 1, 'label' => '12.5X', 'multiplier' => 12.50, 'type' => 'multiplier', 'advance' => false, 'icon' => '🔥', 'color' => '#fb923c', 'weight' => 18],
                    ['id' => 2, 'label' => '28X', 'multiplier' => 28.00, 'type' => 'multiplier', 'advance' => false, 'icon' => '🔥', 'color' => '#fbbf24', 'weight' => 14],
                    ['id' => 3, 'label' => '52X ⚡', 'multiplier' => 52.00, 'type' => 'multiplier', 'advance' => false, 'icon' => '⚡', 'color' => '#e879f9', 'weight' => 10],
                    ['id' => 4, 'label' => '85X 💎', 'multiplier' => 85.00, 'type' => 'multiplier', 'advance' => false, 'icon' => '💎', 'color' => '#c084fc', 'weight' => 7],
                    ['id' => 5, 'label' => '133X 👑', 'multiplier' => 133.00, 'type' => 'multiplier', 'advance' => false, 'icon' => '👑', 'color' => '#38bdf8', 'weight' => 4],
                    ['id' => 6, 'label' => '👑 200X', 'multiplier' => 200.00, 'type' => 'multiplier', 'advance' => false, 'icon' => '🏆', 'color' => '#facc15', 'weight' => 2],
                    ['id' => 7, 'label' => '🎁 BONUS', 'multiplier' => 50.00, 'type' => 'bonus', 'advance' => false, 'icon' => '🎁', 'color' => '#ec4899', 'weight' => 6],
                    ['id' => 8, 'label' => '💥 BLAST', 'multiplier' => 0.00, 'type' => 'bust', 'advance' => false, 'icon' => '💥', 'color' => '#ef4444', 'weight' => 28],
                    ['id' => 9, 'label' => '💥 BLAST', 'multiplier' => 0.00, 'type' => 'bust', 'advance' => false, 'icon' => '💥', 'color' => '#ef4444', 'weight' => 26],
                ],
            ],
        ];
    }

    /**
     * Get active game for a user, or null.
     */
    public static function getActiveGame(User $user): ?VortexGame
    {
        return VortexGame::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Start a new Vortex game session.
     */
    public static function startGame(User $user, float $amount): array
    {
        $setting = AdminSetting::current();
        $minBet = (float) ($setting->vortex_min_bet ?? 1.00);
        $maxBet = (float) ($setting->vortex_max_bet ?? 25000.00);

        if ($amount < $minBet || $amount > $maxBet) {
            return ['success' => false, 'message' => "Bet amount must be between ₹{$minBet} and ₹{$maxBet}."];
        }

        return DB::transaction(function () use ($user, $amount) {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            if (! $lockedUser || (float) $lockedUser->balance < $amount) {
                return ['success' => false, 'message' => 'Insufficient wallet balance. Please deposit funds.'];
            }

            // Check if user already has an active session
            $existing = VortexGame::where('user_id', $lockedUser->id)
                ->where('status', 'active')
                ->first();

            if ($existing) {
                return [
                    'success' => false,
                    'message' => 'You already have an active Vortex session! Cash out or continue spinning.',
                    'active_game' => self::formatGameState($existing),
                ];
            }

            // Deduct balance
            $balanceBefore = (float) $lockedUser->balance;
            $balanceAfter = round($balanceBefore - $amount, 2);
            $lockedUser->balance = $balanceAfter;
            $lockedUser->save();

            $gameId = 'VTX'.Carbon::now()->format('ymdHis').mt_rand(10, 99);

            // Log bet transaction
            Transaction::create([
                'user_id' => $lockedUser->id,
                'type' => 'bet_placed',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => 'completed',
                'admin_notes' => "Placed ₹{$amount} on Vortex Official Game #{$gameId}",
            ]);

            // Provably / Probably fair seeds
            $serverSeed = bin2hex(random_bytes(32));
            $seedHash = hash('sha256', $serverSeed.':'.$gameId.':'.config('app.key'));

            $game = VortexGame::create([
                'game_id' => $gameId,
                'user_id' => $lockedUser->id,
                'bet_amount' => $amount,
                'current_ring' => self::RING_INNER,
                'current_multiplier' => 1.00,
                'payout_amount' => $amount,
                'win_amount' => 0.00,
                'commission_amount' => 0.00,
                'part_cashed_out' => false,
                'part_cashout_amount' => 0.00,
                'remaining_bet_amount' => $amount,
                'spin_history' => [],
                'status' => 'active',
                'server_seed' => $serverSeed,
                'seed_hash' => $seedHash,
            ]);

            return [
                'success' => true,
                'message' => 'Vortex started! Hold to spin the wheel.',
                'new_balance' => $balanceAfter,
                'game' => self::formatGameState($game),
            ];
        });
    }

    /**
     * Spin the current concentric ring.
     */
    public static function spin(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $game = VortexGame::where('user_id', $user->id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if (! $game) {
                return ['success' => false, 'message' => 'No active Vortex game found. Start a new game!'];
            }

            $currentRing = (int) $game->current_ring;
            $ringsConfig = self::getRingsConfig();
            $ringConfig = $ringsConfig[$currentRing] ?? $ringsConfig[1];

            // 1. Check Admin Control Settings (Force Bust / Trap / Max Cap)
            $setting = AdminSetting::current();
            $mode = $setting->vortex_control_mode ?? 'house_trap';
            $forceBust = false;

            if ($mode === 'house_trap' || $mode === 'fair') {
                if ((float) $setting->vortex_max_multiplier_cap > 0 && (float) $game->current_multiplier >= (float) $setting->vortex_max_multiplier_cap) {
                    $forceBust = true;
                }

                $riggedRate = (float) ($setting->vortex_rigged_rate ?: ($setting->house_profit_rate ?? 78.00));
                if (! $forceBust && $riggedRate > 0) {
                    $roll = mt_rand(1, 10000) / 100.0;
                    if ($roll <= $riggedRate) {
                        $forceBust = true;
                    }
                }
            }

            // 2. Select Sector based on weights or Force Bust
            $selectedSector = null;
            if ($forceBust) {
                // Pick a bust sector
                $bustSectors = array_filter($ringConfig['sectors'], fn ($s) => $s['type'] === 'bust');
                $selectedSector = ! empty($bustSectors) ? $bustSectors[array_rand($bustSectors)] : $ringConfig['sectors'][4];
            } else {
                // Weighted selection
                $totalWeight = array_sum(array_column($ringConfig['sectors'], 'weight'));
                $randWeight = mt_rand(1, (int) $totalWeight);
                $running = 0;
                foreach ($ringConfig['sectors'] as $sec) {
                    $running += $sec['weight'];
                    if ($randWeight <= $running) {
                        $selectedSector = $sec;
                        break;
                    }
                }
                $selectedSector = $selectedSector ?? $ringConfig['sectors'][0];
            }

            // Calculate stop angle
            // Target angle points to top marker (e.g. 0 deg) + full rotations (5 to 8 turns) for visual spin
            $sectorAngle = $ringConfig['sector_angle'];
            $sectorIndex = $selectedSector['id'];
            $sectorCenterAngle = ($sectorIndex * $sectorAngle) + ($sectorAngle / 2);
            $fullTurns = mt_rand(4, 6) * 360;
            $stopAngle = $fullTurns + (360 - $sectorCenterAngle);

            $spinRecord = [
                'spin_number' => count($game->spin_history ?? []) + 1,
                'ring' => $currentRing,
                'sector_index' => $sectorIndex,
                'sector_label' => $selectedSector['label'],
                'sector_type' => $selectedSector['type'],
                'sector_multiplier' => (float) $selectedSector['multiplier'],
                'advance' => (bool) $selectedSector['advance'],
                'stop_angle' => round($stopAngle, 2),
                'timestamp' => Carbon::now()->toIso8601String(),
            ];

            $history = $game->spin_history ?? [];
            $history[] = $spinRecord;
            $game->spin_history = $history;

            // 3. Handle Bust vs Multiplier/Advance
            if ($selectedSector['type'] === 'bust') {
                // BUST! Game Over!
                $game->status = 'busted';
                $game->payout_amount = 0.00;
                $game->win_amount = 0.00;
                $game->save();

                return [
                    'success' => true,
                    'result' => 'bust',
                    'status' => 'busted',
                    'message' => '💥 BOOM! Blast sector hit! The Vortex collapsed! Better luck next spin.',
                    'spin' => $spinRecord,
                    'game' => self::formatGameState($game),
                ];
            }

            // SUCCESSFUL SPIN!
            $multiplierWon = (float) $selectedSector['multiplier'];
            $newMultiplier = max((float) $game->current_multiplier, $multiplierWon);

            // If advance sector with addition (e.g. +7x or +20.5x), add to current
            if ($selectedSector['type'] === 'advance' && str_starts_with($selectedSector['label'], '+')) {
                $newMultiplier = round((float) $game->current_multiplier + $multiplierWon, 2);
            }

            $activeBet = (float) ($game->remaining_bet_amount ?: $game->bet_amount);
            $newPayout = round($activeBet * $newMultiplier, 2);

            $newRing = $currentRing;
            $isAdvanced = false;

            if ($selectedSector['advance']) {
                if ($currentRing < self::RING_OUTER) {
                    $newRing = $currentRing + 1;
                    $isAdvanced = true;
                }
            }

            $game->current_ring = $newRing;
            $game->current_multiplier = $newMultiplier;
            $game->payout_amount = $newPayout;
            $game->save();

            // Auto-win if player reached maximum 200X
            if ($newMultiplier >= 200.00) {
                return self::executeCashout($game, true);
            }

            return [
                'success' => true,
                'result' => 'win',
                'status' => 'active',
                'is_advanced' => $isAdvanced,
                'message' => $isAdvanced
                    ? "LEVEL UP! Ascended to Ring {$newRing} with {$newMultiplier}x!"
                    : "Hit {$selectedSector['label']}! Potential Payout: ₹{$newPayout}",
                'spin' => $spinRecord,
                'game' => self::formatGameState($game),
            ];
        });
    }

    /**
     * 50% Partial Cash Out ("Part PayOut"): Secures half winnings and allows player to keep spinning!
     */
    public static function partCashout(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $game = VortexGame::where('user_id', $user->id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if (! $game) {
                return ['success' => false, 'message' => 'No active Vortex game found.'];
            }

            if ($game->part_cashed_out) {
                return ['success' => false, 'message' => 'You have already taken partial payout for this round.'];
            }

            if ((float) $game->current_multiplier <= 1.00 || (float) $game->payout_amount <= 0) {
                return ['success' => false, 'message' => 'Spin at least once to accumulate winnings before taking partial payout.'];
            }

            $currentPayout = (float) $game->payout_amount;
            $halfPayout = round($currentPayout / 2.0, 2);

            // Credit half to user wallet
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            $balanceBefore = (float) $lockedUser->balance;
            $balanceAfter = round($balanceBefore + $halfPayout, 2);
            $lockedUser->balance = $balanceAfter;
            $lockedUser->save();

            // Log partial cashout transaction
            Transaction::create([
                'user_id' => $lockedUser->id,
                'type' => 'bet_won',
                'amount' => $halfPayout,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => 'completed',
                'admin_notes' => "50% Partial Cashout ₹{$halfPayout} on Vortex Game #{$game->game_id}",
            ]);

            // Update game state
            $remainingPayout = round($currentPayout - $halfPayout, 2);
            $remainingBet = round(((float) $game->remaining_bet_amount ?: (float) $game->bet_amount) / 2.0, 2);

            $game->part_cashed_out = true;
            $game->part_cashout_amount = $halfPayout;
            $game->payout_amount = $remainingPayout;
            $game->remaining_bet_amount = $remainingBet;
            $game->save();

            return [
                'success' => true,
                'message' => "Secured ₹{$halfPayout} into your wallet! Continue spinning risk-free with remaining ₹{$remainingPayout}.",
                'cashed_out_half' => $halfPayout,
                'remaining_payout' => $remainingPayout,
                'new_balance' => $balanceAfter,
                'game' => self::formatGameState($game),
            ];
        });
    }

    /**
     * Full Cash Out ("CASH OUT"): Takes full payout and finishes round.
     */
    public static function cashOut(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $game = VortexGame::where('user_id', $user->id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if (! $game) {
                return ['success' => false, 'message' => 'No active Vortex game found to cash out.'];
            }

            if (empty($game->spin_history)) {
                return ['success' => false, 'message' => 'Spin at least once before cashing out!'];
            }

            return self::executeCashout($game, false);
        });
    }

    /**
     * Internal execution of cashout and wallet crediting.
     */
    protected static function executeCashout(VortexGame $game, bool $isAutoWin = false): array
    {
        $setting = AdminSetting::current();
        $user = User::where('id', $game->user_id)->lockForUpdate()->first();

        $grossPayout = (float) $game->payout_amount;
        $activeBet = (float) ($game->remaining_bet_amount ?: $game->bet_amount);

        // Commission on net profit
        $profit = max(0.0, $grossPayout - $activeBet);
        $commissionPercent = (float) ($setting->win_commission_percent ?? 0.0);
        $commissionAmount = round(($profit * $commissionPercent) / 100.0, 2);
        $netPayout = round($grossPayout - $commissionAmount, 2);

        $game->status = 'cashed_out';
        $game->win_amount = $netPayout + (float) $game->part_cashout_amount;
        $game->commission_amount = $commissionAmount;
        $game->cashed_out_at = Carbon::now();
        $game->save();

        // Credit user wallet
        $balanceBefore = (float) $user->balance;
        $balanceAfter = round($balanceBefore + $netPayout, 2);
        $user->balance = $balanceAfter;
        $user->save();

        $notes = "Cashed out ₹{$netPayout} ({$game->current_multiplier}x) on Vortex Game #{$game->game_id}";
        if ($game->part_cashed_out) {
            $notes .= " (+₹{$game->part_cashout_amount} prior part cashout)";
        }
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
            'status' => 'cashed_out',
            'is_auto_win' => $isAutoWin,
            'message' => $isAutoWin
                ? "JACKPOT 200X REACHED! Auto-cashed out ₹{$netPayout}!"
                : "Successfully cashed out ₹{$netPayout} at {$game->current_multiplier}x!",
            'net_payout' => $netPayout,
            'total_won' => (float) $game->win_amount,
            'commission' => $commissionAmount,
            'new_balance' => $balanceAfter,
            'game' => self::formatGameState($game),
        ];
    }

    /**
     * Admin Force Bust active game.
     */
    public static function forceBust(int $gameId): array
    {
        return DB::transaction(function () use ($gameId) {
            $game = VortexGame::where('id', $gameId)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if (! $game) {
                return ['success' => false, 'message' => 'Active game not found or already completed.'];
            }

            $game->status = 'busted';
            $game->payout_amount = 0.00;
            $game->win_amount = (float) $game->part_cashout_amount;
            $game->save();

            return ['success' => true, 'message' => "Game #{$game->game_id} has been forcefully busted."];
        });
    }

    /**
     * Admin Force Cashout active game.
     */
    public static function forceCashout(int $gameId): array
    {
        return DB::transaction(function () use ($gameId) {
            $game = VortexGame::where('id', $gameId)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if (! $game) {
                return ['success' => false, 'message' => 'Active game not found or already completed.'];
            }

            if (empty($game->spin_history)) {
                // 0 spins, refund
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
                    'admin_notes' => "Admin forcefully refunded ₹{$refund} on Vortex Game #{$game->game_id}",
                ]);

                return ['success' => true, 'message' => "Game #{$game->game_id} had 0 spins; ₹{$refund} refunded to player wallet."];
            }

            return self::executeCashout($game, false);
        });
    }

    /**
     * Format game state for API response.
     */
    public static function formatGameState(VortexGame $game): array
    {
        $ringsConfig = self::getRingsConfig();
        $isFinished = ($game->status !== 'active');

        return [
            'id' => $game->id,
            'game_id' => $game->game_id,
            'status' => $game->status,
            'bet_amount' => (float) $game->bet_amount,
            'remaining_bet_amount' => (float) ($game->remaining_bet_amount ?: $game->bet_amount),
            'current_ring' => (int) $game->current_ring,
            'current_multiplier' => (float) $game->current_multiplier,
            'payout_amount' => (float) $game->payout_amount,
            'part_payout_available' => round((float) $game->payout_amount / 2.0, 2),
            'part_cashed_out' => (bool) $game->part_cashed_out,
            'part_cashout_amount' => (float) $game->part_cashout_amount,
            'can_part_cashout' => $game->canPartCashout(),
            'win_amount' => (float) $game->win_amount,
            'spin_history' => $game->spin_history ?? [],
            'rings_config' => $ringsConfig,
            'seed_hash' => $game->seed_hash,
            'server_seed' => $isFinished ? $game->server_seed : null,
        ];
    }

    /**
     * Get player's recent Vortex games.
     */
    public static function getRecentGames(User $user, int $limit = 25): array
    {
        return VortexGame::where('user_id', $user->id)
            ->whereIn('status', ['cashed_out', 'busted'])
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($g) {
                return [
                    'id' => $g->id,
                    'game_id' => $g->game_id,
                    'bet_amount' => (float) $g->bet_amount,
                    'ring_reached' => (int) $g->current_ring,
                    'multiplier' => (float) $g->current_multiplier,
                    'win_amount' => (float) $g->win_amount,
                    'part_cashed_out' => (bool) $g->part_cashed_out,
                    'part_cashout_amount' => (float) $g->part_cashout_amount,
                    'status' => $g->status,
                    'created_at' => $g->created_at ? $g->created_at->format('d M, h:i A') : '',
                    'time_iso' => $g->created_at ? $g->created_at->toISOString() : '',
                ];
            })
            ->toArray();
    }
}
