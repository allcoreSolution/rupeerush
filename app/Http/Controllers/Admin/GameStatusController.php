<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\AndarBaharBet;
use App\Models\AviatorBet;
use App\Models\Bet;
use App\Models\DragonTigerBet;
use App\Models\HorseRaceBet;
use App\Models\MinesGame;
use App\Models\RouletteBet;
use App\Models\SportsBet;
use App\Models\TeenPattiBet;
use App\Models\VortexGame;
use App\Models\WingoBet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GameStatusController extends Controller
{
    /**
     * Master catalog of 11 official games in RupeeRush with their metadata & bet query counters.
     *
     * @return array<string, array{id: string, name: string, category: string, icon: string, color: string, route: string, total_bets: int, is_active: bool}>
     */
    protected function getGamesList(AdminSetting $setting): array
    {
        $games = [
            'lottery' => [
                'id' => 'lottery',
                'name' => '2 Digit Slot (Lottery)',
                'subtitle' => 'Hourly slot draws (XA–XJ) with 90X payout',
                'category' => 'Lottery & Slots',
                'icon' => 'fa-dice',
                'color' => '#0284c7',
                'route' => route('lottery.index'),
                'admin_route' => route('admin.results.index'),
                'total_bets' => (int) Bet::count(),
            ],
            'wingo' => [
                'id' => 'wingo',
                'name' => 'WinGo Color Prediction',
                'subtitle' => '30s, 1m, 3m, 5m • Colors, Numbers & Big/Small',
                'category' => 'Lottery & Slots',
                'icon' => 'fa-gamepad',
                'color' => '#8b5cf6',
                'route' => route('wingo.index'),
                'admin_route' => route('admin.wingo.index'),
                'total_bets' => (int) WingoBet::count(),
            ],
            'andar_bahar' => [
                'id' => 'andar_bahar',
                'name' => 'Andar Bahar Live',
                'subtitle' => 'Classic Indian 50:50 Card Battle with Joker Card',
                'category' => 'Live Casino',
                'icon' => 'fa-diamond',
                'color' => '#f59e0b',
                'route' => route('andar-bahar.index'),
                'admin_route' => route('admin.andar-bahar.index'),
                'total_bets' => (int) AndarBaharBet::count(),
            ],
            'aviator' => [
                'id' => 'aviator',
                'name' => 'Aviator Crash',
                'subtitle' => 'Unlimited Multiplier • Instant Cashout Curve',
                'category' => 'Crash & Arcade',
                'icon' => 'fa-plane-departure',
                'color' => '#e11d48',
                'route' => route('aviator.index'),
                'admin_route' => route('admin.aviator.index'),
                'total_bets' => (int) AviatorBet::count(),
            ],
            'mines' => [
                'id' => 'mines',
                'name' => 'Mines 5x5 Arcade',
                'subtitle' => 'Uncover safe diamonds, dodge mines & cash out',
                'category' => 'Crash & Arcade',
                'icon' => 'fa-bomb',
                'color' => '#10b981',
                'route' => route('mines.index'),
                'admin_route' => route('admin.mines.index'),
                'total_bets' => (int) MinesGame::count(),
            ],
            'vortex' => [
                'id' => 'vortex',
                'name' => 'Vortex Wheel',
                'subtitle' => '3 Concentric Rings • Level Up up to 200X',
                'category' => 'Crash & Arcade',
                'icon' => 'fa-arrows-spin',
                'color' => '#06b6d4',
                'route' => route('vortex.index'),
                'admin_route' => route('admin.vortex.index'),
                'total_bets' => (int) VortexGame::count(),
            ],
            'teen_patti' => [
                'id' => 'teen_patti',
                'name' => '20-20 Teen Patti',
                'subtitle' => 'Live 3-Card Battle • Player A vs B • 25s Fast Action',
                'category' => 'Live Casino',
                'icon' => 'fa-heart',
                'color' => '#059669',
                'route' => route('teen-patti.index'),
                'admin_route' => route('admin.teen-patti.index'),
                'total_bets' => (int) TeenPattiBet::count(),
            ],
            'dragon_tiger' => [
                'id' => 'dragon_tiger',
                'name' => 'Dragon Tiger Live',
                'subtitle' => 'Highest Card Wins • Suited Tie 50X',
                'category' => 'Live Casino',
                'icon' => 'fa-dragon',
                'color' => '#b91c1c',
                'route' => route('dragon-tiger.index'),
                'admin_route' => route('admin.dragon-tiger.index'),
                'total_bets' => (int) DragonTigerBet::count(),
            ],
            'roulette' => [
                'id' => 'roulette',
                'name' => 'European Roulette 36x',
                'subtitle' => 'Authentic European 37-Pocket Wheel (0–36)',
                'category' => 'Live Casino',
                'icon' => 'fa-dharmachakra',
                'color' => '#dc2626',
                'route' => route('roulette.index'),
                'admin_route' => route('admin.roulette.index'),
                'total_bets' => (int) RouletteBet::count(),
            ],
            'horse_racing' => [
                'id' => 'horse_racing',
                'name' => 'Derby Horse Racing',
                'subtitle' => '60s live derby • 6 thoroughbreds & photo finish',
                'category' => 'Sports & Racing',
                'icon' => 'fa-horse-head',
                'color' => '#15803d',
                'route' => route('horse-racing.index'),
                'admin_route' => route('admin.horse-racing.index'),
                'total_bets' => (int) HorseRaceBet::count(),
            ],
            'cricket' => [
                'id' => 'cricket',
                'name' => 'Cricket Live Sportsbook',
                'subtitle' => 'Live ball-by-ball score, Back & Lay exchange odds',
                'category' => 'Sports & Racing',
                'icon' => 'fa-baseball-bat-ball',
                'color' => '#ea580c',
                'route' => route('sports.cricket'),
                'admin_route' => route('admin.dashboard'),
                'total_bets' => (int) SportsBet::count(),
            ],
        ];

        foreach ($games as $key => $g) {
            $games[$key]['is_active'] = $setting->isGameActive($key);
        }

        return $games;
    }

    /**
     * Display the Dedicated Game Status Management Hub.
     */
    public function index(): View
    {
        $setting = AdminSetting::current();
        $games = $this->getGamesList($setting);

        $totalGames = count($games);
        $activeGames = count(array_filter($games, fn ($g) => $g['is_active']));
        $inactiveGames = $totalGames - $activeGames;

        return view('admin.games.status', compact('games', 'totalGames', 'activeGames', 'inactiveGames'));
    }

    /**
     * Toggle a specific game's active or inactive status.
     */
    public function toggle(Request $request, string $gameId): JsonResponse|RedirectResponse
    {
        $setting = AdminSetting::current();
        $normalizedId = AdminSetting::normalizeGameKey($gameId);

        $validKeys = [
            'lottery', 'wingo', 'andar_bahar', 'aviator', 'mines',
            'vortex', 'teen_patti', 'dragon_tiger', 'roulette', 'horse_racing', 'cricket',
        ];

        if (! in_array($normalizedId, $validKeys, true)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid game identifier specified.',
                ], 422);
            }

            return redirect()->back()->with('error', 'Invalid game specified.');
        }

        $currentStatus = $setting->isGameActive($normalizedId);
        $newStatus = $request->has('status') ? (bool) $request->boolean('status') : ! $currentStatus;

        $setting->setGameActive($normalizedId, $newStatus);

        $gameName = AdminSetting::getGameName($normalizedId);
        $statusText = $newStatus ? 'ACTIVE' : 'INACTIVE (MAINTENANCE)';
        $message = "Game '{$gameName}' has been successfully switched to {$statusText}.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'game_id' => $normalizedId,
                'game_name' => $gameName,
                'is_active' => $newStatus,
                'status_text' => $statusText,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Bulk activate or deactivate all platform games at once.
     */
    public function bulkUpdate(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:activate_all,deactivate_all',
        ]);

        $setting = AdminSetting::current();
        $isActive = ($request->input('action') === 'activate_all');

        $games = [
            'lottery', 'wingo', 'andar_bahar', 'aviator', 'mines',
            'vortex', 'teen_patti', 'dragon_tiger', 'roulette', 'horse_racing', 'cricket',
        ];

        foreach ($games as $key) {
            $setting->setGameActive($key, $isActive);
        }

        $msg = $isActive
            ? 'All 11 platform games have been ENABLED and are now LIVE for all players!'
            : 'All platform games have been PAUSED and put into Scheduled Maintenance.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => $isActive,
                'message' => $msg,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }
}
