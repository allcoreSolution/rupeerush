<?php

namespace App\Services\Sports;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CricketApiService
{
    protected ?string $apiKey;

    protected string $baseUrl = 'https://api.cricapi.com/v1/';

    public function __construct()
    {
        $this->apiKey = config('services.cricapi.key', env('CRICAPI_KEY', ''));
    }

    /**
     * Check if CricAPI is configured with a valid key.
     */
    public function isLiveApiConfigured(): bool
    {
        return ! empty($this->apiKey) && strlen($this->apiKey) > 10;
    }

    /**
     * Test CricAPI connectivity and return diagnostic info (status, quota, latency).
     */
    public function testApiConnection(): array
    {
        if (! $this->isLiveApiConfigured()) {
            return [
                'success' => false,
                'message' => 'CRICAPI_KEY is not configured in .env',
                'api_key' => $this->apiKey ? substr($this->apiKey, 0, 8).'...' : 'None',
            ];
        }

        $startTime = microtime(true);
        try {
            $response = Http::withoutVerifying()
                ->timeout(25)
                ->get("{$this->baseUrl}currentMatches", [
                    'apikey' => $this->apiKey,
                    'offset' => 0,
                ]);

            $elapsed = round(microtime(true) - $startTime, 2);

            if ($response->successful()) {
                $json = $response->json();
                $status = $json['status'] ?? 'unknown';
                $info = $json['info'] ?? [];

                if ($status === 'success') {
                    $totalMatches = count($json['data'] ?? []);
                    $hitsToday = $info['hitsToday'] ?? 0;
                    $hitsLimit = $info['hitsLimit'] ?? 100;

                    return [
                        'success' => true,
                        'message' => 'CricAPI connected successfully!',
                        'latency_seconds' => $elapsed,
                        'hits_today' => $hitsToday,
                        'hits_limit' => $hitsLimit,
                        'hits_remaining' => max(0, $hitsLimit - $hitsToday),
                        'total_matches_found' => $totalMatches,
                        'sample_match' => $json['data'][0]['name'] ?? 'N/A',
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'CricAPI error: '.($json['reason'] ?? $json['status'] ?? 'Unknown error'),
                    'latency_seconds' => $elapsed,
                    'raw_response' => $json,
                ];
            }

            return [
                'success' => false,
                'message' => 'HTTP error '.$response->status().': '.$response->body(),
                'latency_seconds' => $elapsed,
            ];
        } catch (\Throwable $e) {
            $elapsed = round(microtime(true) - $startTime, 2);

            return [
                'success' => false,
                'message' => 'Connection failed: '.$e->getMessage(),
                'latency_seconds' => $elapsed,
                'suggestion' => 'Check internet connection or verify cURL SSL configuration.',
            ];
        }
    }

    /**
     * Fetch current cricket matches (live & upcoming)
     */
    public function getMatches(bool $forceRefresh = false): array
    {
        if (! $this->isLiveApiConfigured()) {
            return $this->getSimulatedMatches();
        }

        if ($forceRefresh) {
            Cache::forget('cricapi_current_matches');
        }

        // Cache for 15 minutes (900s) to strictly respect 100 hits/day free limit
        return Cache::remember('cricapi_current_matches', 900, function () {
            try {
                $response = Http::withoutVerifying()
                    ->timeout(25)
                    ->get("{$this->baseUrl}currentMatches", [
                        'apikey' => $this->apiKey,
                        'offset' => 0,
                    ]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (($json['status'] ?? '') === 'success' && ! empty($json['data'])) {
                        $parsed = $this->parseLiveMatches($json['data']);
                        if (! empty($parsed)) {
                            // Merge with interactive simulated live matches so users always have active in-play betting
                            $simulated = $this->getSimulatedMatches();
                            $hasActiveLive = collect($parsed)->contains('is_live', true);

                            if (! $hasActiveLive) {
                                // Prepend active simulated live matches to upcoming CricAPI matches
                                return array_merge(
                                    array_filter($simulated, fn ($m) => ! empty($m['is_live'])),
                                    $parsed
                                );
                            }

                            return array_merge($parsed, array_filter($simulated, fn ($m) => ! empty($m['is_live'])));
                        }
                    }
                }

                Log::warning('CricAPI returned non-success or empty, using interactive simulator', [
                    'body' => $response->body() ?? 'empty',
                ]);
            } catch (\Throwable $e) {
                Log::error('CricAPI fetch failed: '.$e->getMessage());
            }

            return $this->getSimulatedMatches();
        });
    }

    /**
     * Parse CricAPI JSON match array into normalized platform structure.
     */
    protected function parseLiveMatches(array $apiMatches): array
    {
        $normalized = [];

        foreach ($apiMatches as $m) {
            $teamA = $m['teams'][0] ?? 'Team A';
            $teamB = $m['teams'][1] ?? 'Team B';
            $teamAInfo = $m['teamInfo'][0] ?? [];
            $teamBInfo = $m['teamInfo'][1] ?? [];

            $isMatchEnded = (bool) ($m['matchEnded'] ?? false);
            $isLive = (bool) ($m['matchStarted'] ?? false) && ! $isMatchEnded;

            $status = 'upcoming';
            if ($isMatchEnded) {
                $status = 'completed';
            } elseif ($isLive) {
                $status = 'live';
            }

            // Extract scores if available
            $scoreA = null;
            $scoreB = null;
            if (! empty($m['score'])) {
                foreach ($m['score'] as $sc) {
                    $scoreStr = "{$sc['r']}/{$sc['w']} ({$sc['o']} ov)";
                    if (stripos($sc['inning'], $teamA) !== false || stripos($teamA, $sc['inning']) !== false) {
                        $scoreA = $scoreStr;
                    } else {
                        $scoreB = $scoreStr;
                    }
                }
            }

            $winner = $m['winner'] ?? null;
            if (! $winner && $isMatchEnded && ! empty($m['status'])) {
                if (stripos($m['status'], $teamA) !== false) {
                    $winner = $teamA;
                } elseif (stripos($m['status'], $teamB) !== false) {
                    $winner = $teamB;
                }
            }

            $normalized[] = [
                'external_id' => (string) ($m['id'] ?? uniqid('cric_')),
                'sport' => 'cricket',
                'series_name' => $m['series_name'] ?? 'International Cricket',
                'title' => $m['name'] ?? "{$teamA} vs {$teamB}",
                'match_type' => strtolower($m['matchType'] ?? 't20'),
                'status' => $status,
                'team_a' => $teamA,
                'team_a_short' => $teamAInfo['shortname'] ?? substr($teamA, 0, 3),
                'team_a_img' => $teamAInfo['img'] ?? null,
                'team_b' => $teamB,
                'team_b_short' => $teamBInfo['shortname'] ?? substr($teamB, 0, 3),
                'team_b_img' => $teamBInfo['img'] ?? null,
                'venue' => $m['venue'] ?? 'International Stadium',
                'score_team_a' => $scoreA,
                'score_team_b' => $scoreB,
                'current_summary' => $m['status'] ?? 'Match in progress',
                'recent_balls' => ['1', '4', '0', 'W', '6', '1'],
                'winner_team' => $isMatchEnded ? $winner : null,
                'is_live' => $isLive,
                'is_featured' => true,
                'starts_at' => $m['dateTimeGMT'] ?? now()->toIso8601String(),
                'meta_data' => [
                    'batsman_striker' => ['name' => $teamA.' Captain', 'runs' => 45, 'balls' => 28, 'fours' => 4, 'sixes' => 2, 'sr' => 160.7],
                    'batsman_non_striker' => ['name' => $teamA.' All-Rounder', 'runs' => 22, 'balls' => 14, 'fours' => 2, 'sixes' => 1, 'sr' => 157.1],
                    'current_bowler' => ['name' => $teamB.' Strike Bowler', 'overs' => '3.2', 'maidens' => 0, 'runs' => 26, 'wickets' => 1, 'econ' => 7.8],
                    'last_ball_outcome' => '1',
                    'last_ball_commentary' => 'Pushed into the gap for a quick single.',
                    'last_event' => 'SINGLE',
                    'ball_phase' => 'betting_open',
                    'ball_countdown' => 10,
                    'current_ball_num' => '18.3',
                ],
                'markets' => $this->generateDefaultMarkets($teamA, $teamB, $isLive),
            ];
        }

        return ! empty($normalized) ? $normalized : $this->getSimulatedMatches();
    }

    /**
     * Highly realistic simulation engine:
     * Generates active live matches with dynamic real-time scores,
     * over counts, batsman/bowler stats, recent balls timeline,
     * and fluctuating Back/Lay odds for Ball, Over, and Match Winner markets.
     */
    public function getSimulatedMatches(): array
    {
        $time = time();

        // Match 1: India vs Australia (T20 International)
        // Paced at 12 seconds per delivery
        $ballDuration1 = 12;
        $secondInCycle1 = $time % $ballDuration1;
        $isBallInPlay1 = $secondInCycle1 >= 9; // Last 3s of cycle is bowler delivery
        $countdown1 = max(0, 9 - $secondInCycle1);

        $totalBalls1 = (int) floor($time / $ballDuration1) % 120; // 0 to 119 balls
        $over1 = (int) floor($totalBalls1 / 6);
        $ballInOver1 = $totalBalls1 % 6;
        $runs1 = 142 + (int) floor($totalBalls1 * 1.38);
        $wickets1 = min(9, 2 + (int) floor($totalBalls1 / 26));

        // Dynamic ball outcomes sequence
        $ballsPool = ['1', '4', '0', '2', '6', '1', 'W', '1', '4', '0', '2', '6', '1', '4', '0', '2'];
        $lastIdx1 = ($totalBalls1 - 1 + 120) % count($ballsPool);
        $lastBall1 = $ballsPool[$lastIdx1];

        $recent1 = [];
        for ($i = 5; $i >= 0; $i--) {
            $idx = ($totalBalls1 - $i + 120) % count($ballsPool);
            $recent1[] = $ballsPool[$idx];
        }

        $commentaries = [
            '0' => 'Dot ball! Solid forward defensive stroke towards mid-off.',
            '1' => 'Worked off the pads into deep midwicket for a sharp single.',
            '2' => 'Guided past backward point with soft hands, great hustle for two runs!',
            '4' => 'FOUR! Cracking shot! Pierces extra cover and sweeper boundary like a rocket!',
            '6' => 'SIX! Out of the ground! Clean strike directly into the VIP upper tier!',
            'W' => 'WICKET! Massive appeal and GONE! Skied straight down the throat of long-on!',
        ];

        $eventText = match ($lastBall1) {
            '0' => 'DOT BALL',
            '1' => 'SINGLE (1 RUN)',
            '2' => 'DOUBLE (2 RUNS)',
            '4' => 'BOUNDARY (FOUR 4)',
            '6' => 'MAXIMUM (SIX 6)',
            'W' => 'WICKET OUT!',
            default => 'PLAY'
        };

        // Dynamic Odds
        $indOdds = round(1.65 + (sin($time / 15) * 0.15), 2);
        $indOdds = max(1.20, min(3.50, $indOdds));
        $ausOdds = round(2.35 - (sin($time / 15) * 0.18), 2);
        $ausOdds = max(1.25, min(4.20, $ausOdds));

        $nextBallNum1 = "{$over1}.".($ballInOver1 + 1);
        $nextOverNum1 = $over1 + 1;

        // Match 2: CSK vs MI (IPL Blockbuster)
        $ballDuration2 = 14;
        $secondInCycle2 = ($time + 5) % $ballDuration2;
        $isBallInPlay2 = $secondInCycle2 >= 10;
        $countdown2 = max(0, 10 - $secondInCycle2);

        $totalBalls2 = (int) floor(($time + 40) / $ballDuration2) % 120;
        $over2 = (int) floor($totalBalls2 / 6);
        $ballInOver2 = $totalBalls2 % 6;
        $runs2 = 124 + (int) floor($totalBalls2 * 1.5);
        $wickets2 = min(9, 3 + (int) floor($totalBalls2 / 30));

        $cskOdds = round(1.86 + (cos($time / 14) * 0.12), 2);
        $miOdds = round(1.96 - (cos($time / 14) * 0.12), 2);
        $recent2 = ['2', '0', '4', '1', '6', 'W'];

        // Match 3: England vs South Africa
        $totalBalls3 = (int) floor(($time + 85) / 15) % 120;
        $over3 = (int) floor($totalBalls3 / 6);
        $ballInOver3 = $totalBalls3 % 6;
        $runs3 = 98 + (int) floor($totalBalls3 * 1.3);
        $wickets3 = min(9, 1 + (int) floor($totalBalls3 / 35));

        $engOdds = round(1.92 + (sin($time / 18) * 0.10), 2);
        $saOdds = round(1.94 - (sin($time / 18) * 0.10), 2);

        return [
            [
                'external_id' => 'sim_ind_aus_live',
                'sport' => 'cricket',
                'series_name' => 'ICC T20 World Super Series 2026',
                'title' => 'India vs Australia',
                'match_type' => 't20',
                'status' => 'live',
                'team_a' => 'India',
                'team_a_short' => 'IND',
                'team_a_img' => 'https://flagcdn.com/w80/in.png',
                'team_b' => 'Australia',
                'team_b_short' => 'AUS',
                'team_b_img' => 'https://flagcdn.com/w80/au.png',
                'venue' => 'Wankhede Stadium, Mumbai',
                'score_team_a' => "{$runs1}/{$wickets1} ({$over1}.{$ballInOver1} ov)",
                'score_team_b' => '186/6 (20.0 ov)',
                'current_summary' => 'India need '.max(1, 187 - $runs1).' runs in '.max(1, 120 - $totalBalls1).' balls',
                'recent_balls' => $recent1,
                'winner_team' => null,
                'is_live' => true,
                'is_featured' => true,
                'starts_at' => now()->subMinutes(65)->toIso8601String(),
                'meta_data' => [
                    'batsman_striker' => [
                        'name' => 'Virat Kohli',
                        'runs' => 64 + (int) floor($totalBalls1 * 0.45),
                        'balls' => 38 + (int) floor($totalBalls1 * 0.35),
                        'fours' => 6 + (int) floor($totalBalls1 / 20),
                        'sixes' => 2 + (int) floor($totalBalls1 / 35),
                        'sr' => round((64 + (int) floor($totalBalls1 * 0.45)) / max(1, 38 + (int) floor($totalBalls1 * 0.35)) * 100, 1),
                    ],
                    'batsman_non_striker' => [
                        'name' => 'Hardik Pandya',
                        'runs' => 32 + (int) floor($totalBalls1 * 0.35),
                        'balls' => 19 + (int) floor($totalBalls1 * 0.25),
                        'fours' => 3,
                        'sixes' => 2,
                        'sr' => 168.4,
                    ],
                    'current_bowler' => [
                        'name' => 'Pat Cummins',
                        'overs' => "{$over1}.{$ballInOver1}",
                        'maidens' => 0,
                        'runs' => 28 + (int) floor($totalBalls1 * 0.4),
                        'wickets' => 1 + (int) floor($wickets1 / 4),
                        'econ' => round((28 + (int) floor($totalBalls1 * 0.4)) / max(0.1, $over1 + ($ballInOver1 / 6)), 1),
                    ],
                    'last_ball_outcome' => $lastBall1,
                    'last_ball_commentary' => $commentaries[$lastBall1] ?? 'Delivery bowled.',
                    'last_event' => $eventText,
                    'ball_phase' => $isBallInPlay1 ? 'ball_in_play' : 'betting_open',
                    'ball_countdown' => $countdown1,
                    'current_ball_num' => $nextBallNum1,
                    'target' => 187,
                    'crr' => round($runs1 / max(0.1, $over1 + ($ballInOver1 / 6)), 2),
                    'rrr' => round(max(0, 187 - $runs1) / max(0.1, (120 - $totalBalls1) / 6), 2),
                    'partnership' => [
                        'runs' => 54 + (int) floor($totalBalls1 * 0.75),
                        'balls' => 32 + (int) floor($totalBalls1 * 0.55),
                    ],
                ],
                'markets' => [
                    // 1. MATCH WINNER (WIN / LOSS)
                    [
                        'market_type' => 'match_winner',
                        'market_name' => 'Match Winner (Inc. Super Over)',
                        'status' => 'active',
                        'runners' => [
                            [
                                'runner_name' => 'India',
                                'back_odds' => $indOdds,
                                'lay_odds' => round($indOdds + 0.04, 2),
                                'back_volume' => 384500,
                                'lay_volume' => 312000,
                            ],
                            [
                                'runner_name' => 'Australia',
                                'back_odds' => $ausOdds,
                                'lay_odds' => round($ausOdds + 0.05, 2),
                                'back_volume' => 294000,
                                'lay_volume' => 258000,
                            ],
                        ],
                    ],
                    // 2. BALL-BY-BALL MARKET
                    [
                        'market_type' => 'next_ball',
                        'market_name' => "Next Ball Outcome (Ball {$nextBallNum1})",
                        'status' => $isBallInPlay1 ? 'suspended' : 'active',
                        'runners' => [
                            [
                                'runner_name' => 'Dot Ball (0 runs)',
                                'back_odds' => 1.85,
                                'lay_odds' => 1.95,
                                'back_volume' => 84000,
                                'lay_volume' => 62000,
                            ],
                            [
                                'runner_name' => '1 Run',
                                'back_odds' => 2.25,
                                'lay_odds' => 2.38,
                                'back_volume' => 95000,
                                'lay_volume' => 71000,
                            ],
                            [
                                'runner_name' => '2 Runs',
                                'back_odds' => 4.80,
                                'lay_odds' => 5.20,
                                'back_volume' => 42000,
                                'lay_volume' => 29000,
                            ],
                            [
                                'runner_name' => 'Boundary 4',
                                'back_odds' => 4.20,
                                'lay_odds' => 4.60,
                                'back_volume' => 110000,
                                'lay_volume' => 88000,
                            ],
                            [
                                'runner_name' => 'Six 6',
                                'back_odds' => 7.50,
                                'lay_odds' => 8.20,
                                'back_volume' => 135000,
                                'lay_volume' => 95000,
                            ],
                            [
                                'runner_name' => 'Wicket (Out)',
                                'back_odds' => 6.20,
                                'lay_odds' => 6.90,
                                'back_volume' => 78000,
                                'lay_volume' => 52000,
                            ],
                            [
                                'runner_name' => 'Extra (Wide/NB)',
                                'back_odds' => 8.50,
                                'lay_odds' => 9.50,
                                'back_volume' => 31000,
                                'lay_volume' => 18000,
                            ],
                        ],
                    ],
                    // 3. OVER RUNS OVER / UNDER
                    [
                        'market_type' => 'over_runs',
                        'market_name' => "Next Over Runs (Over {$nextOverNum1})",
                        'status' => 'active',
                        'runners' => [
                            [
                                'runner_name' => 'Over 8.5 Runs',
                                'back_odds' => 1.85,
                                'lay_odds' => 1.92,
                                'back_volume' => 125000,
                                'lay_volume' => 98000,
                            ],
                            [
                                'runner_name' => 'Under 8.5 Runs',
                                'back_odds' => 1.95,
                                'lay_odds' => 2.02,
                                'back_volume' => 110000,
                                'lay_volume' => 87000,
                            ],
                        ],
                    ],
                    // 4. OVER ODD / EVEN
                    [
                        'market_type' => 'over_odd_even',
                        'market_name' => "Over {$nextOverNum1} Runs (Odd/Even)",
                        'status' => 'active',
                        'runners' => [
                            [
                                'runner_name' => 'Odd Total Runs',
                                'back_odds' => 1.92,
                                'lay_odds' => 1.98,
                                'back_volume' => 65000,
                                'lay_volume' => 49000,
                            ],
                            [
                                'runner_name' => 'Even Total Runs',
                                'back_odds' => 1.92,
                                'lay_odds' => 1.98,
                                'back_volume' => 62000,
                                'lay_volume' => 47000,
                            ],
                        ],
                    ],
                    // 5. SESSION FANCY
                    [
                        'market_type' => 'session_runs',
                        'market_name' => '6 Over Powerplay Session Score',
                        'status' => 'active',
                        'runners' => [
                            [
                                'runner_name' => 'Over 52.5 Runs',
                                'back_odds' => 1.88,
                                'lay_odds' => 1.96,
                                'back_volume' => 180000,
                                'lay_volume' => 140000,
                            ],
                            [
                                'runner_name' => 'Under 52.5 Runs',
                                'back_odds' => 1.90,
                                'lay_odds' => 1.98,
                                'back_volume' => 170000,
                                'lay_volume' => 135000,
                            ],
                        ],
                    ],
                    // 6. TIED MATCH
                    [
                        'market_type' => 'tied_match',
                        'market_name' => 'Tied Match (Inc. Super Over)',
                        'status' => 'active',
                        'runners' => [
                            [
                                'runner_name' => 'Yes (Super Over)',
                                'back_odds' => 21.00,
                                'lay_odds' => 25.00,
                                'back_volume' => 32000,
                                'lay_volume' => 19000,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'external_id' => 'sim_csk_mi_live',
                'sport' => 'cricket',
                'series_name' => 'Indian Premier League 2026',
                'title' => 'Chennai Super Kings vs Mumbai Indians',
                'match_type' => 't20',
                'status' => 'live',
                'team_a' => 'Chennai Super Kings',
                'team_a_short' => 'CSK',
                'team_a_img' => null,
                'team_b' => 'Mumbai Indians',
                'team_b_short' => 'MI',
                'team_b_img' => null,
                'venue' => 'M. A. Chidambaram Stadium, Chennai',
                'score_team_a' => "{$runs2}/{$wickets2} ({$over2}.{$ballInOver2} ov)",
                'score_team_b' => '174/7 (20.0 ov)',
                'current_summary' => 'CSK need '.max(1, 175 - $runs2).' runs to win',
                'recent_balls' => $recent2,
                'winner_team' => null,
                'is_live' => true,
                'is_featured' => true,
                'starts_at' => now()->subMinutes(45)->toIso8601String(),
                'meta_data' => [
                    'batsman_striker' => ['name' => 'MS Dhoni', 'runs' => 28, 'balls' => 12, 'fours' => 2, 'sixes' => 2, 'sr' => 233.3],
                    'batsman_non_striker' => ['name' => 'Ruturaj Gaikwad', 'runs' => 52, 'balls' => 36, 'fours' => 5, 'sixes' => 1, 'sr' => 144.4],
                    'current_bowler' => ['name' => 'Jasprit Bumrah', 'overs' => "{$over2}.{$ballInOver2}", 'maidens' => 0, 'runs' => 22, 'wickets' => 2, 'econ' => 6.8],
                    'last_ball_outcome' => '4',
                    'last_ball_commentary' => 'Slapped through covers for a blazing boundary!',
                    'last_event' => 'BOUNDARY (FOUR 4)',
                    'ball_phase' => $isBallInPlay2 ? 'ball_in_play' : 'betting_open',
                    'ball_countdown' => $countdown2,
                    'current_ball_num' => "{$over2}.".($ballInOver2 + 1),
                    'target' => 175,
                    'crr' => round($runs2 / max(0.1, $over2 + ($ballInOver2 / 6)), 2),
                    'rrr' => round(max(0, 175 - $runs2) / max(0.1, (120 - $totalBalls2) / 6), 2),
                ],
                'markets' => [
                    [
                        'market_type' => 'match_winner',
                        'market_name' => 'Match Winner',
                        'status' => 'active',
                        'runners' => [
                            [
                                'runner_name' => 'Chennai Super Kings',
                                'back_odds' => $cskOdds,
                                'lay_odds' => round($cskOdds + 0.04, 2),
                                'back_volume' => 450000,
                                'lay_volume' => 390000,
                            ],
                            [
                                'runner_name' => 'Mumbai Indians',
                                'back_odds' => $miOdds,
                                'lay_odds' => round($miOdds + 0.04, 2),
                                'back_volume' => 420000,
                                'lay_volume' => 380000,
                            ],
                        ],
                    ],
                    [
                        'market_type' => 'next_ball',
                        'market_name' => "Next Ball Outcome (Ball {$over2}.".($ballInOver2 + 1).')',
                        'status' => $isBallInPlay2 ? 'suspended' : 'active',
                        'runners' => [
                            ['runner_name' => 'Dot Ball (0 runs)', 'back_odds' => 1.85, 'lay_odds' => 1.95, 'back_volume' => 50000, 'lay_volume' => 40000],
                            ['runner_name' => '1 Run', 'back_odds' => 2.25, 'lay_odds' => 2.38, 'back_volume' => 60000, 'lay_volume' => 45000],
                            ['runner_name' => '2 Runs', 'back_odds' => 4.80, 'lay_odds' => 5.20, 'back_volume' => 30000, 'lay_volume' => 20000],
                            ['runner_name' => 'Boundary 4', 'back_odds' => 4.20, 'lay_odds' => 4.60, 'back_volume' => 70000, 'lay_volume' => 55000],
                            ['runner_name' => 'Six 6', 'back_odds' => 7.50, 'lay_odds' => 8.20, 'back_volume' => 90000, 'lay_volume' => 65000],
                            ['runner_name' => 'Wicket (Out)', 'back_odds' => 6.20, 'lay_odds' => 6.90, 'back_volume' => 45000, 'lay_volume' => 32000],
                        ],
                    ],
                    [
                        'market_type' => 'over_runs',
                        'market_name' => 'Next Over Runs (Over '.($over2 + 1).')',
                        'status' => 'active',
                        'runners' => [
                            ['runner_name' => 'Over 8.5 Runs', 'back_odds' => 1.85, 'lay_odds' => 1.92, 'back_volume' => 95000, 'lay_volume' => 74000],
                            ['runner_name' => 'Under 8.5 Runs', 'back_odds' => 1.92, 'lay_odds' => 1.98, 'back_volume' => 89000, 'lay_volume' => 68000],
                        ],
                    ],
                ],
            ],
            [
                'external_id' => 'sim_eng_sa_live',
                'sport' => 'cricket',
                'series_name' => 'England Tour of South Africa',
                'title' => 'England vs South Africa',
                'match_type' => 't20',
                'status' => 'live',
                'team_a' => 'England',
                'team_a_short' => 'ENG',
                'team_a_img' => 'https://flagcdn.com/w80/gb-eng.png',
                'team_b' => 'South Africa',
                'team_b_short' => 'SA',
                'team_b_img' => 'https://flagcdn.com/w80/za.png',
                'venue' => 'Newlands, Cape Town',
                'score_team_a' => "{$runs3}/{$wickets3} ({$over3}.{$ballInOver3} ov)",
                'score_team_b' => '1st Inning',
                'current_summary' => 'ENG 1st Inning batting at RR: '.round($runs3 / max(1, $over3 + 0.1), 2),
                'recent_balls' => ['1', '0', '4', '1', '2', '0'],
                'winner_team' => null,
                'is_live' => true,
                'is_featured' => false,
                'starts_at' => now()->subMinutes(30)->toIso8601String(),
                'markets' => [
                    [
                        'market_type' => 'match_winner',
                        'market_name' => 'Match Winner',
                        'status' => 'active',
                        'runners' => [
                            ['runner_name' => 'England', 'back_odds' => $engOdds, 'lay_odds' => round($engOdds + 0.04, 2), 'back_volume' => 210000, 'lay_volume' => 180000],
                            ['runner_name' => 'South Africa', 'back_odds' => $saOdds, 'lay_odds' => round($saOdds + 0.04, 2), 'back_volume' => 220000, 'lay_volume' => 175000],
                        ],
                    ],
                ],
            ],
            [
                'external_id' => 'sim_kkr_rcb_upcoming',
                'sport' => 'cricket',
                'series_name' => 'Indian Premier League 2026',
                'title' => 'Kolkata Knight Riders vs Royal Challengers Bengaluru',
                'match_type' => 't20',
                'status' => 'upcoming',
                'team_a' => 'Kolkata Knight Riders',
                'team_a_short' => 'KKR',
                'team_a_img' => null,
                'team_b' => 'Royal Challengers Bengaluru',
                'team_b_short' => 'RCB',
                'team_b_img' => null,
                'venue' => 'Eden Gardens, Kolkata',
                'score_team_a' => null,
                'score_team_b' => null,
                'current_summary' => 'Match starts today at 7:30 PM IST',
                'recent_balls' => [],
                'winner_team' => null,
                'is_live' => false,
                'is_featured' => true,
                'starts_at' => now()->addMinutes(45)->toIso8601String(),
                'markets' => [
                    [
                        'market_type' => 'match_winner',
                        'market_name' => 'Match Winner',
                        'status' => 'active',
                        'runners' => [
                            ['runner_name' => 'Kolkata Knight Riders', 'back_odds' => 1.85, 'lay_odds' => 1.89, 'back_volume' => 310000, 'lay_volume' => 280000],
                            ['runner_name' => 'Royal Challengers Bengaluru', 'back_odds' => 1.95, 'lay_odds' => 1.99, 'back_volume' => 340000, 'lay_volume' => 300000],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Generate default Back/Lay markets for real matches from CricAPI
     */
    protected function generateDefaultMarkets(string $teamA, string $teamB, bool $isLive): array
    {
        $markets = [
            [
                'market_type' => 'match_winner',
                'market_name' => 'Match Winner',
                'status' => 'active',
                'runners' => [
                    ['runner_name' => $teamA, 'back_odds' => 1.90, 'lay_odds' => 1.95, 'back_volume' => 150000, 'lay_volume' => 120000],
                    ['runner_name' => $teamB, 'back_odds' => 1.90, 'lay_odds' => 1.95, 'back_volume' => 150000, 'lay_volume' => 120000],
                ],
            ],
        ];

        if ($isLive) {
            $markets[] = [
                'market_type' => 'next_ball',
                'market_name' => 'Next Ball Outcome',
                'status' => 'active',
                'runners' => [
                    ['runner_name' => 'Dot Ball (0 runs)', 'back_odds' => 1.85, 'lay_odds' => 1.95, 'back_volume' => 70000, 'lay_volume' => 50000],
                    ['runner_name' => '1 Run', 'back_odds' => 2.25, 'lay_odds' => 2.38, 'back_volume' => 80000, 'lay_volume' => 60000],
                    ['runner_name' => '2 Runs', 'back_odds' => 4.80, 'lay_odds' => 5.20, 'back_volume' => 30000, 'lay_volume' => 20000],
                    ['runner_name' => 'Boundary 4', 'back_odds' => 4.20, 'lay_odds' => 4.60, 'back_volume' => 90000, 'lay_volume' => 70000],
                    ['runner_name' => 'Six 6', 'back_odds' => 7.50, 'lay_odds' => 8.20, 'back_volume' => 120000, 'lay_volume' => 85000],
                    ['runner_name' => 'Wicket (Out)', 'back_odds' => 6.20, 'lay_odds' => 6.90, 'back_volume' => 65000, 'lay_volume' => 45000],
                ],
            ];

            $markets[] = [
                'market_type' => 'over_runs',
                'market_name' => 'Next Over Total Runs',
                'status' => 'active',
                'runners' => [
                    ['runner_name' => 'Over 8.5 Runs', 'back_odds' => 1.85, 'lay_odds' => 1.92, 'back_volume' => 110000, 'lay_volume' => 85000],
                    ['runner_name' => 'Under 8.5 Runs', 'back_odds' => 1.95, 'lay_odds' => 2.02, 'back_volume' => 105000, 'lay_volume' => 80000],
                ],
            ];
        }

        return $markets;
    }
}
