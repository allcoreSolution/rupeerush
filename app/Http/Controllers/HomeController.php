<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\AviatorBet;
use App\Models\Bet;
use App\Models\GameSlot;
use App\Models\HorseRaceBet;
use App\Models\MinesGame;
use App\Models\Result;
use App\Models\WingoBet;
use App\Services\LotteryEngine;
use App\Services\Sports\CricketEngine;
use App\Services\WingoEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Show Clean Multi-Game Lobby (Portal Dashboard)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $setting = AdminSetting::current();

        // WinGo Live Status for Quick Lobby Card
        $wingoPeriodData = WingoEngine::getCurrentPeriod('30s');
        $timingStatus = LotteryEngine::getSlotScheduleStatus();

        // Comprehensive Multi-Game Catalog (Strictly the 11 real games in project)
        $games = [
            // 1. Lottery Category
            [
                'id' => 'lottery',
                'code' => 'lottery',
                'title' => '2 Digit Slot',
                'subtitle' => 'Hourly slot draws (XA–XJ) with 90X payout',
                'category' => 'lottery',
                'category_name' => 'Lottery & Slots',
                'route' => route('lottery.index'),
                'badge' => 'LIVE NOW',
                'badge_class' => 'bg-success',
                'payout' => '90X Payout',
                'active_players' => 1240,
                'status' => 'active',
                'icon' => 'fa-dice',
                'bg_gradient' => 'linear-gradient(135deg, #0284c7 0%, #0369a1 100%)',
                'accent_color' => '#38bdf8',
                'tags' => ['Hourly Draws', '90X Jackpot'],
            ],
            [
                'id' => 'wingo',
                'code' => 'wingo',
                'title' => 'WinGo Color',
                'subtitle' => '30s, 1m, 3m, 5m • Colors, Numbers & Big/Small',
                'category' => 'lottery',
                'category_name' => 'Lottery & Slots',
                'route' => route('wingo.index'),
                'badge' => '🔥 HOT LIVE',
                'badge_class' => 'bg-danger',
                'payout' => 'Up to 9X',
                'active_players' => 3890,
                'status' => 'active',
                'icon' => 'fa-gamepad',
                'bg_gradient' => 'linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%)',
                'accent_color' => '#f472b6',
                'tags' => ['30s Rounds', '9X Multipliers'],
            ],

            // 2. Card & Casino Category
            [
                'id' => 'andar_bahar',
                'code' => 'andar_bahar',
                'title' => 'Andar Bahar',
                'subtitle' => 'Classic Indian 50:50 Card Battle with Joker Card',
                'category' => 'casino',
                'category_name' => 'Cards & Casino',
                'route' => route('andar-bahar.index'),
                'badge' => '🔥 LIVE CASINO',
                'badge_class' => 'bg-danger',
                'payout' => '1.95X Payout',
                'active_players' => 2410,
                'status' => 'active',
                'icon' => 'fa-diamond',
                'bg_gradient' => 'linear-gradient(135deg, #f59e0b 0%, #b45309 100%)',
                'accent_color' => '#fbbf24',
                'tags' => ['50:50 Cards', '1.95X Payout'],
            ],
            [
                'id' => 'teen_patti',
                'code' => 'teen_patti',
                'title' => 'Teen Patti',
                'subtitle' => 'Live 3-Card Battle • Player A vs B • 25s Fast Action',
                'category' => 'casino',
                'category_name' => 'Cards & Casino',
                'route' => route('teen-patti.index'),
                'badge' => '🔥 LIVE CASINO',
                'badge_class' => 'bg-danger',
                'payout' => 'Up to 40X',
                'active_players' => 2850,
                'status' => 'active',
                'icon' => 'fa-heart',
                'bg_gradient' => 'linear-gradient(135deg, #059669 0%, #064e3b 100%)',
                'accent_color' => '#34d399',
                'tags' => ['Player A vs B', 'Up to 40X'],
            ],
            [
                'id' => 'dragon_tiger',
                'code' => 'dragon_tiger',
                'title' => 'Dragon Tiger',
                'subtitle' => 'Dragon vs Tiger • Highest Card Wins • 25s High Speed Action',
                'category' => 'casino',
                'category_name' => 'Cards & Casino',
                'route' => route('dragon-tiger.index'),
                'badge' => '🔥 NEW CASINO',
                'badge_class' => 'bg-danger',
                'payout' => 'Up to 50X',
                'active_players' => 3190,
                'status' => 'active',
                'icon' => 'fa-dragon',
                'bg_gradient' => 'linear-gradient(135deg, #b91c1c 0%, #1e1b4b 50%, #1d4ed8 100%)',
                'accent_color' => '#f59e0b',
                'tags' => ['Dragon vs Tiger', 'Suited Tie 50X'],
            ],
            [
                'id' => 'roulette',
                'code' => 'roulette',
                'title' => 'Roulette',
                'subtitle' => 'Authentic European 37-Pocket Wheel • Straight-up 36X, Splits & Red/Black',
                'category' => 'casino',
                'category_name' => 'Cards & Casino',
                'route' => route('roulette.index'),
                'badge' => '🔥 LIVE CASINO',
                'badge_class' => 'bg-danger',
                'payout' => 'Up to 36X',
                'active_players' => 3420,
                'status' => 'active',
                'icon' => 'fa-dharmachakra',
                'bg_gradient' => 'linear-gradient(135deg, #1e1b4b 0%, #064e3b 50%, #022c22 100%)',
                'accent_color' => '#10b981',
                'tags' => ['37 Pockets (0-36)', 'Straight 36X'],
            ],

            // 3. Crash & Arcade Category
            [
                'id' => 'aviator',
                'code' => 'aviator',
                'title' => 'Aviator',
                'subtitle' => 'Cash out before the plane flies away • Unlimited multiplier',
                'category' => 'crash',
                'category_name' => 'Crash & Arcade',
                'route' => route('aviator.index'),
                'badge' => '🔥 LIVE NOW',
                'badge_class' => 'bg-danger',
                'payout' => 'Up to 1000X',
                'active_players' => 5840,
                'status' => 'active',
                'icon' => 'fa-plane-departure',
                'bg_gradient' => 'linear-gradient(135deg, #e11d48 0%, #991b1b 100%)',
                'accent_color' => '#fda4af',
                'tags' => ['Unlimited Mult.', 'Instant Cashout'],
            ],
            [
                'id' => 'mines',
                'code' => 'mines',
                'title' => 'Mines',
                'subtitle' => 'Uncover safe coins, dodge mines & cash out with huge multipliers',
                'category' => 'crash',
                'category_name' => 'Crash & Arcade',
                'route' => route('mines.index'),
                'badge' => '🔥 NEW LIVE',
                'badge_class' => 'bg-success',
                'payout' => 'Dynamic Multiplier',
                'active_players' => 4150,
                'status' => 'active',
                'icon' => 'fa-bomb',
                'bg_gradient' => 'linear-gradient(135deg, #182346 0%, #0d152d 100%)',
                'accent_color' => '#fbbf24',
                'tags' => ['5x5 Minefield', 'Cashout Anytime'],
            ],
            [
                'id' => 'vortex',
                'code' => 'vortex',
                'title' => 'Vortex Wheel',
                'subtitle' => '3 Concentric Rings • Level Up & Cash Out up to 200X',
                'category' => 'crash',
                'category_name' => 'Crash & Arcade',
                'route' => route('vortex.index'),
                'badge' => '🔥 NEW 200X',
                'badge_class' => 'bg-info text-dark',
                'payout' => 'Up to 200X',
                'active_players' => 3240,
                'status' => 'active',
                'icon' => 'fa-arrows-spin',
                'bg_gradient' => 'linear-gradient(135deg, #091a28 0%, #033c4d 100%)',
                'accent_color' => '#38bdf8',
                'tags' => ['3 Ring Tiers', '200X Multiplier'],
            ],

            // 4. Sports & Racing Category
            [
                'id' => 'horse_racing',
                'code' => 'horse_racing',
                'title' => 'Horse Racing',
                'subtitle' => 'Live 60s rounds • 6 thoroughbreds • Real-time turf sprint & live odds',
                'category' => 'sports',
                'category_name' => 'Sports & Racing',
                'route' => route('horse-racing.index'),
                'badge' => '🔥 NEW LIVE',
                'badge_class' => 'bg-success',
                'payout' => 'Up to 26X Odds',
                'active_players' => 3890,
                'status' => 'active',
                'icon' => 'fa-horse-head',
                'bg_gradient' => 'linear-gradient(135deg, #065f46 0%, #022c22 100%)',
                'accent_color' => '#34d399',
                'tags' => ['6 Thoroughbreds', '60s Photo Finish'],
            ],
            [
                'id' => 'cricket',
                'code' => 'cricket',
                'title' => 'Cricket Sports',
                'subtitle' => 'IPL, World Cup & T20 live ball-by-ball score & Back/Lay odds',
                'category' => 'sports',
                'category_name' => 'Sports & Racing',
                'route' => route('sports.cricket'),
                'badge' => '🔥 LIVE NOW',
                'badge_class' => 'bg-danger',
                'payout' => 'Live Back & Lay',
                'active_players' => 6480,
                'status' => 'active',
                'icon' => 'fa-baseball-bat-ball',
                'bg_gradient' => 'linear-gradient(135deg, #065f46 0%, #047857 50%, #064e3b 100%)',
                'accent_color' => '#10b981',
                'tags' => ['Live Ball-by-Ball', 'Back / Lay Odds', 'Instant Settlement'],
            ],
        ];

        // Sync live active / inactive status from AdminSetting
        foreach ($games as $key => $g) {
            $isActive = $setting->isGameActive($g['id']);
            $games[$key]['is_active'] = $isActive;
            $games[$key]['status'] = $isActive ? 'active' : 'inactive';
        }

        // Categories metadata for quick filters
        $catCounts = collect($games)->groupBy('category')->map->count();
        $categories = [
            ['id' => 'all', 'name' => 'All Games', 'icon' => 'fa-border-all', 'count' => count($games)],
            ['id' => 'sports', 'name' => 'Cricket & Sports', 'icon' => 'fa-baseball-bat-ball', 'count' => $catCounts->get('sports', 0)],
            ['id' => 'lottery', 'name' => 'Lottery & Slots', 'icon' => 'fa-dice', 'count' => $catCounts->get('lottery', 0)],
            ['id' => 'casino', 'name' => 'Cards & Casino', 'icon' => 'fa-diamond', 'count' => $catCounts->get('casino', 0)],
            ['id' => 'crash', 'name' => 'Crash & Arcade', 'icon' => 'fa-plane-departure', 'count' => $catCounts->get('crash', 0)],
        ];

        // Recent Big Winners Ticker (Dynamic real winners + realistic randomized pool)
        $recentWinners = $this->getRecentWinners();

        // Lotus365-Style Live Cricket In-Play Matches for Mobile & Desktop Live Score Cards
        $cricketLiveMatches = [];
        try {
            $cricketEngine = app(CricketEngine::class);
            $allCricketMatches = $cricketEngine->getMatchesList('all');
            $cricketLiveMatches = array_values(array_filter($allCricketMatches, function ($m) {
                return ! empty($m['is_live']) || ($m['status'] ?? '') === 'live';
            }));
            if (empty($cricketLiveMatches)) {
                $cricketLiveMatches = array_slice($allCricketMatches, 0, 4);
            }
        } catch (\Throwable $e) {
            $cricketLiveMatches = [];
        }

        return view('lobby', [
            'user' => $user,
            'setting' => $setting,
            'games' => $games,
            'categories' => $categories,
            'recentWinners' => $recentWinners,
            'wingoPeriodData' => $wingoPeriodData,
            'timingStatus' => $timingStatus,
            'cricketLiveMatches' => $cricketLiveMatches,
        ]);
    }

    /**
     * Show Dedicated 2-Digit Lottery Arena Page
     */
    public function lottery(Request $request)
    {
        $setting = AdminSetting::current();
        $timingStatus = LotteryEngine::getSlotScheduleStatus();
        $user = Auth::user();

        // Get or generate latest published slot result
        $curr = $timingStatus['currentPublishedSlot'];
        $currDate = $curr['slot_datetime']->format('Y-m-d');

        $latestResult = Result::where('result_date', $currDate)
            ->where('slot_time', $curr['slot_time'])
            ->first();

        if (! $latestResult && $curr['is_generated']) {
            $latestResult = LotteryEngine::publishResultAndProcessPayouts($curr['slot_time'], $currDate);
        }

        // Available slots for betting today
        $activeSlots = GameSlot::where('is_active', true)->orderBy('display_order')->get();

        // Today's results keyed by slot_time
        $todayResults = Result::where('result_date', $currDate)->get()->keyBy('slot_time');

        return view('lottery', [
            'setting' => $setting,
            'timingStatus' => $timingStatus,
            'latestResult' => $latestResult,
            'activeSlots' => $activeSlots,
            'todayResults' => $todayResults,
            'user' => $user,
        ]);
    }

    /**
     * Get dynamic list of recent real winners combined with realistic randomized winners.
     *
     * @return array<int, array{user: string, game: string, amount: float, time: string}>
     */
    private function getRecentWinners(): array
    {
        $winners = [];

        try {
            // Real Aviator cashout winners
            $aviatorBets = AviatorBet::with('user')
                ->where('status', 'cashed_out')
                ->where('win_amount', '>', 0)
                ->latest('id')
                ->take(6)
                ->get();

            foreach ($aviatorBets as $bet) {
                $userName = $this->maskUserName($bet->user?->name, $bet->user?->phone);
                $mult = $bet->cashout_multiplier ? number_format((float) $bet->cashout_multiplier, 2).'x' : 'Cashed Out';
                $winners[] = [
                    'user' => $userName,
                    'game' => "Aviator Crash ({$mult})",
                    'amount' => (float) $bet->win_amount,
                    'time' => $bet->created_at ? $bet->created_at->diffForHumans(null, true) : 'Just now',
                ];
            }

            // Real WinGo winning bets
            $wingoBets = WingoBet::with('user')
                ->where('status', 'won')
                ->where('win_amount', '>', 0)
                ->latest('id')
                ->take(6)
                ->get();

            foreach ($wingoBets as $bet) {
                $userName = $this->maskUserName($bet->user?->name, $bet->user?->phone);
                $gameLabel = 'WinGo '.($bet->game_type ?? '30s');
                if ($bet->bet_selection) {
                    $gameLabel .= ' ('.ucfirst($bet->bet_selection).')';
                }
                $winners[] = [
                    'user' => $userName,
                    'game' => $gameLabel,
                    'amount' => (float) $bet->win_amount,
                    'time' => $bet->created_at ? $bet->created_at->diffForHumans(null, true) : 'Just now',
                ];
            }

            // Real 2-Digit Lottery winning bets
            $lotteryBets = Bet::with('user')
                ->where('status', 'won')
                ->where('win_amount', '>', 0)
                ->latest('id')
                ->take(6)
                ->get();

            foreach ($lotteryBets as $bet) {
                $userName = $this->maskUserName($bet->user?->name, $bet->user?->phone);
                $slotName = $bet->slot_key ? strtoupper($bet->slot_key) : 'Slot';
                $winners[] = [
                    'user' => $userName,
                    'game' => "2-Digit Lottery ({$slotName} 90X)",
                    'amount' => (float) $bet->win_amount,
                    'time' => $bet->created_at ? $bet->created_at->diffForHumans(null, true) : 'Just now',
                ];
            }

            // Real Mines winning games
            $minesGames = MinesGame::with('user')
                ->where('status', 'cashed_out')
                ->where('win_amount', '>', 0)
                ->latest('id')
                ->take(6)
                ->get();

            foreach ($minesGames as $game) {
                $userName = $this->maskUserName($game->user?->name, $game->user?->phone);
                $coins = $game->current_step ?? $game->mines_count ?? 5;
                $winners[] = [
                    'user' => $userName,
                    'game' => "Mines 5x5 ({$coins} Coins Safe)",
                    'amount' => (float) $game->win_amount,
                    'time' => $game->created_at ? $game->created_at->diffForHumans(null, true) : 'Just now',
                ];
            }

            // Real Horse Racing Derby winning bets
            $horseBets = HorseRaceBet::with('user')
                ->where('status', 'won')
                ->where('payout_amount', '>', 0)
                ->latest('id')
                ->take(6)
                ->get();

            foreach ($horseBets as $bet) {
                $userName = $this->maskUserName($bet->user?->name, $bet->user?->phone);
                $oddsStr = $bet->odds ? number_format((float) $bet->odds, 2).'X' : 'Win';
                $winners[] = [
                    'user' => $userName,
                    'game' => "Derby Racing (#{$bet->horse_id} {$bet->horse_name} {$oddsStr})",
                    'amount' => (float) $bet->payout_amount,
                    'time' => $bet->created_at ? $bet->created_at->diffForHumans(null, true) : 'Just now',
                ];
            }
        } catch (\Throwable $e) {
            // Silently fall back to randomized pool
        }

        // Fill remaining spots to guarantee at least 28 exciting, randomized winners every refresh
        $targetCount = 28;
        $needed = max(12, $targetCount - count($winners));

        $names = [
            'Aarav', 'Rohit', 'Suresh', 'Priya', 'Ankit', 'Amit', 'Neha', 'Deepak',
            'Pooja', 'Sanjay', 'Kunal', 'Ajay', 'Sunita', 'Vishal', 'Ritu', 'Ravi',
            'Manish', 'Vikram', 'Rahul', 'Rajesh', 'Mohit', 'Karan', 'Gaurav', 'Vikas',
            'Sachin', 'Pankaj', 'Dinesh', 'Manoj', 'Akash', 'Sunny', 'Harsh', 'Arjun',
            'Rakesh', 'Anil', 'Mukesh', 'Simran', 'Kavita', 'Sunil', 'Naveen', 'Devendra',
            'Ashok', 'Pradeep', 'Chetan', 'Yash', 'Abhishek', 'Kartik', 'Sumit', 'Alok',
            'Varun', 'Nitin', 'Vijay', 'Hemant', 'Tarun', 'Bhavna', 'Meena', 'Divya',
        ];

        $timeStrings = ['Just now', 'Just now', '1 min ago', '2 min ago', '3 min ago', '4 min ago', '5 min ago'];

        for ($i = 0; $i < $needed; $i++) {
            $randomName = $names[array_rand($names)].'***'.mt_rand(1, 9);
            $randomGameData = $this->generateRandomWinnerItem();
            $winners[] = [
                'user' => $randomName,
                'game' => $randomGameData['game'],
                'amount' => $randomGameData['amount'],
                'time' => $timeStrings[array_rand($timeStrings)],
            ];
        }

        shuffle($winners);

        return $winners;
    }

    /**
     * Mask user display name or phone for privacy.
     */
    private function maskUserName(?string $name, ?string $phone): string
    {
        if (! empty($name)) {
            $cleanName = preg_replace('/[^a-zA-Z]/', '', $name);
            $prefix = substr($cleanName, 0, min(5, max(3, strlen($cleanName) - 2)));

            return ($prefix ?: 'Player').'***'.mt_rand(1, 9);
        }

        if (! empty($phone) && strlen($phone) >= 6) {
            return substr($phone, 0, 2).'***'.substr($phone, -2);
        }

        return 'Player***'.mt_rand(1, 9);
    }

    /**
     * Generate a realistic random game and payout.
     *
     * @return array{game: string, amount: float}
     */
    private function generateRandomWinnerItem(): array
    {
        $types = [
            function (): array {
                $mult = number_format(mt_rand(180, 3500) / 100, 2);
                $amt = (float) (round(mt_rand(600, 28000) / 50) * 50);

                return ['game' => "Aviator Crash ({$mult}x)", 'amount' => $amt];
            },
            function (): array {
                $selections = ['Red 2X', 'Green 2X', 'Violet 4.5X', 'Number '.mt_rand(0, 9).' (9X)', 'Big 2X', 'Small 2X'];
                $sel = $selections[array_rand($selections)];
                $amt = (float) (round(mt_rand(400, 15000) / 50) * 50);

                return ['game' => "WinGo 30s ({$sel})", 'amount' => $amt];
            },
            function (): array {
                $selections = ['Green 2X', 'Red 2X', 'Big 2X', 'Small 2X', 'Number '.mt_rand(0, 9).' (9X)'];
                $sel = $selections[array_rand($selections)];
                $amt = (float) (round(mt_rand(500, 18000) / 50) * 50);

                return ['game' => "WinGo 1m ({$sel})", 'amount' => $amt];
            },
            function (): array {
                $slots = ['XA', 'XB', 'XC', 'XD', 'XE', 'XF', 'XG', 'XH', 'XI', 'XJ'];
                $slot = $slots[array_rand($slots)];
                $amt = (float) (round(mt_rand(900, 45000) / 100) * 100);

                return ['game' => "2-Digit Lottery ({$slot} 90X)", 'amount' => $amt];
            },
            function (): array {
                $coins = mt_rand(3, 16);
                $amt = (float) (round(mt_rand(750, 24000) / 50) * 50);

                return ['game' => "Mines 5x5 ({$coins} Coins)", 'amount' => $amt];
            },
            function (): array {
                $boosts = ['2.50x', '5.00x Boost', '12.50x Mega', '25.00x Ultra', '50.00x Super'];
                $boost = $boosts[array_rand($boosts)];
                $amt = (float) (round(mt_rand(1000, 36000) / 50) * 50);

                return ['game' => "Vortex Wheel ({$boost})", 'amount' => $amt];
            },
            function (): array {
                $hands = ['Pair', 'Color Flush', 'Straight Run', 'Pure Sequence', 'Trio AAA'];
                $hand = $hands[array_rand($hands)];
                $amt = (float) (round(mt_rand(1200, 32000) / 50) * 50);

                return ['game' => "20-20 Teen Patti ({$hand})", 'amount' => $amt];
            },
            function (): array {
                $sides = ['Andar 1.95X', 'Bahar 2.00X', 'First Card Match 15X'];
                $side = $sides[array_rand($sides)];
                $amt = (float) (round(mt_rand(600, 22000) / 50) * 50);

                return ['game' => "Andar Bahar Live ({$side})", 'amount' => $amt];
            },
            function (): array {
                $horses = ['Thunder Spirit (3.20X)', 'Lightning Bolt (4.50X)', 'Royal Gallop (6.00X)', 'Shadow Runner (8.50X)', 'Golden Stride (12.00X)', 'Desert Storm (18.00X)'];
                $horse = $horses[array_rand($horses)];
                $amt = (float) (round(mt_rand(800, 32000) / 50) * 50);

                return ['game' => "Derby Racing ({$horse})", 'amount' => $amt];
            },
            function (): array {
                $bets = ['Number 17 (36X)', 'Number 7 Red (36X)', 'Red 2X', 'Black 2X', '1st 12 (3X)', 'Corner 9X'];
                $b = $bets[array_rand($bets)];
                $amt = (float) (round(mt_rand(1200, 36000) / 50) * 50);

                return ['game' => "Roulette 36x ({$b})", 'amount' => $amt];
            },
        ];

        $fn = $types[array_rand($types)];

        return $fn();
    }
}
