<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AndarBaharRound;
use App\Models\AviatorRound;
use App\Models\DragonTigerRound;
use App\Models\HorseRaceRound;
use App\Models\MinesGame;
use App\Models\Result;
use App\Models\RouletteRound;
use App\Models\SportsMatch;
use App\Models\TeenPattiRound;
use App\Models\VortexGame;
use App\Models\WingoPeriod;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLiveResultController extends Controller
{
    /**
     * Display live round results and real-time state across all 11 platform games.
     */
    public function index(Request $request): View|JsonResponse
    {
        $selectedGame = $request->query('game', 'all');

        // 1. Dragon Tiger Live
        $dtCurrent = DragonTigerRound::latest('id')->first();
        $dtRecent = DragonTigerRound::latest('id')->limit(12)->get();

        // 2. Teen Patti 20-20
        $tpCurrent = TeenPattiRound::latest('id')->first();
        $tpRecent = TeenPattiRound::latest('id')->limit(12)->get();

        // 3. European Roulette 36x
        $rouletteCurrent = RouletteRound::latest('id')->first();
        $rouletteRecent = RouletteRound::latest('id')->limit(12)->get();

        // 4. Horse Racing Derby
        $horseCurrent = HorseRaceRound::latest('id')->first();
        $horseRecent = HorseRaceRound::latest('id')->limit(12)->get();

        // 5. Aviator Crash
        $aviatorCurrent = AviatorRound::latest('id')->first();
        $aviatorRecent = AviatorRound::latest('id')->limit(12)->get();

        // 6. Andar Bahar Live
        $abCurrent = AndarBaharRound::latest('id')->first();
        $abRecent = AndarBaharRound::latest('id')->limit(12)->get();

        // 7. WinGo Color Prediction (Latest for each mode: 30s, 1m, 3m, 5m)
        $wingoCurrent30s = WingoPeriod::where('game_type', '30s')->latest('id')->first();
        $wingoCurrent1m = WingoPeriod::where('game_type', '1m')->latest('id')->first();
        $wingoCurrent3m = WingoPeriod::where('game_type', '3m')->latest('id')->first();
        $wingoCurrent5m = WingoPeriod::where('game_type', '5m')->latest('id')->first();
        $wingoRecent = WingoPeriod::latest('id')->limit(12)->get();

        // 8. 2-Digit Lottery (Today's / Latest slot results)
        $lotteryLatest = Result::latest('result_date')->latest('id')->first();
        $lotteryRecent = Result::latest('result_date')->latest('id')->limit(8)->get();

        // 9. Vortex Wheel
        $vortexRecent = VortexGame::with('user')->latest('id')->limit(12)->get();
        $vortexActive = VortexGame::with('user')->where('status', 'active')->latest('id')->first();

        // 10. Mines 5x5 Live
        $minesRecent = MinesGame::with('user')->latest('id')->limit(12)->get();
        $minesActive = MinesGame::with('user')->where('status', 'active')->latest('id')->first();

        // 11. Cricket Sportsbook
        $sportsLive = SportsMatch::where('is_live', true)->orWhere('status', 'live')->latest('id')->get();
        $sportsRecent = SportsMatch::latest('id')->limit(8)->get();

        // Master snapshot matrix of all 11 games
        $liveGamesMatrix = [
            'dragon_tiger' => [
                'name' => 'Dragon Tiger Live',
                'badge_color' => 'danger',
                'icon' => 'fa-dragon',
                'route' => route('admin.dragon-tiger.index'),
                'round_id' => $dtCurrent ? '#'.$dtCurrent->round_id : 'N/A',
                'status' => $dtCurrent ? $dtCurrent->status : 'idle',
                'outcome' => $dtCurrent && $dtCurrent->winner ? strtoupper($dtCurrent->winner) : 'Pending...',
                'outcome_sub' => $dtCurrent ? ('D: '.($dtCurrent->dragon_card['code'] ?? '--').' vs T: '.($dtCurrent->tiger_card['code'] ?? '--')) : '',
                'is_manual' => $dtCurrent ? (bool) $dtCurrent->is_manual : false,
                'manual_target' => $dtCurrent ? $dtCurrent->manual_winner : null,
                'bets_count' => $dtCurrent ? ($dtCurrent->total_bets_count ?? 0) : 0,
                'bet_amount' => $dtCurrent ? (float) ($dtCurrent->total_bet_amount ?? 0) : 0,
                'updated_at' => $dtCurrent ? $dtCurrent->updated_at?->diffForHumans() : '-',
            ],
            'teen_patti' => [
                'name' => 'Teen Patti 20-20',
                'badge_color' => 'warning text-dark',
                'icon' => 'fa-crown',
                'route' => route('admin.teen-patti.index'),
                'round_id' => $tpCurrent ? '#'.$tpCurrent->round_id : 'N/A',
                'status' => $tpCurrent ? $tpCurrent->status : 'idle',
                'outcome' => $tpCurrent && $tpCurrent->winner ? str_replace('_', ' ', strtoupper($tpCurrent->winner)) : 'Pending...',
                'outcome_sub' => $tpCurrent ? ($tpCurrent->player_a_rank_title ?? $tpCurrent->player_b_rank_title ?? '') : '',
                'is_manual' => $tpCurrent ? (bool) $tpCurrent->is_manual : false,
                'manual_target' => $tpCurrent ? $tpCurrent->manual_winner : null,
                'bets_count' => $tpCurrent ? ($tpCurrent->total_bets_count ?? 0) : 0,
                'bet_amount' => $tpCurrent ? (float) ($tpCurrent->total_bet_amount ?? 0) : 0,
                'updated_at' => $tpCurrent ? $tpCurrent->updated_at?->diffForHumans() : '-',
            ],
            'roulette' => [
                'name' => 'European Roulette 36x',
                'badge_color' => 'danger',
                'icon' => 'fa-dharmachakra',
                'route' => route('admin.roulette.index'),
                'round_id' => $rouletteCurrent ? '#'.$rouletteCurrent->round_id : 'N/A',
                'status' => $rouletteCurrent ? $rouletteCurrent->status : 'idle',
                'outcome' => $rouletteCurrent && $rouletteCurrent->winning_number !== null ? ($rouletteCurrent->winning_number.' '.strtoupper($rouletteCurrent->winning_color ?? '')) : 'Spinning...',
                'outcome_sub' => $rouletteCurrent ? ($rouletteCurrent->is_even ? 'EVEN' : 'ODD').' | Dozen '.$rouletteCurrent->dozen : '',
                'is_manual' => $rouletteCurrent ? (bool) $rouletteCurrent->is_manual : false,
                'manual_target' => $rouletteCurrent ? $rouletteCurrent->manual_number : null,
                'bets_count' => $rouletteCurrent ? ($rouletteCurrent->total_bets_count ?? 0) : 0,
                'bet_amount' => $rouletteCurrent ? (float) ($rouletteCurrent->total_bet_amount ?? 0) : 0,
                'updated_at' => $rouletteCurrent ? $rouletteCurrent->updated_at?->diffForHumans() : '-',
            ],
            'horse_racing' => [
                'name' => 'Horse Racing Derby',
                'badge_color' => 'success',
                'icon' => 'fa-horse-head',
                'route' => route('admin.horse-racing.index'),
                'round_id' => $horseCurrent ? '#'.$horseCurrent->round_id : 'N/A',
                'status' => $horseCurrent ? $horseCurrent->status : 'idle',
                'outcome' => $horseCurrent && $horseCurrent->winner_horse_id ? ('Horse #'.$horseCurrent->winner_horse_id.' WIN') : 'In Gate / Racing...',
                'outcome_sub' => $horseCurrent ? ('Dist: '.($horseCurrent->race_distance ?? 1000).'m') : '',
                'is_manual' => $horseCurrent ? (bool) $horseCurrent->is_manual : false,
                'manual_target' => $horseCurrent ? $horseCurrent->manual_winner_id : null,
                'bets_count' => $horseCurrent ? ($horseCurrent->total_bets_count ?? 0) : 0,
                'bet_amount' => $horseCurrent ? (float) ($horseCurrent->total_bet_amount ?? 0) : 0,
                'updated_at' => $horseCurrent ? $horseCurrent->updated_at?->diffForHumans() : '-',
            ],
            'aviator' => [
                'name' => 'Aviator Crash',
                'badge_color' => 'danger',
                'icon' => 'fa-plane-departure',
                'route' => route('admin.aviator.index'),
                'round_id' => $aviatorCurrent ? '#'.$aviatorCurrent->round_id : 'N/A',
                'status' => $aviatorCurrent ? $aviatorCurrent->status : 'idle',
                'outcome' => $aviatorCurrent && $aviatorCurrent->crash_multiplier ? ($aviatorCurrent->crash_multiplier.'x CRASH') : 'Taking Off...',
                'outcome_sub' => $aviatorCurrent ? 'Status: '.strtoupper($aviatorCurrent->status) : '',
                'is_manual' => $aviatorCurrent ? (bool) $aviatorCurrent->is_manual : false,
                'manual_target' => $aviatorCurrent ? $aviatorCurrent->manual_crash_multiplier.'x' : null,
                'bets_count' => $aviatorCurrent ? ($aviatorCurrent->total_bets_count ?? 0) : 0,
                'bet_amount' => $aviatorCurrent ? (float) ($aviatorCurrent->total_bet_amount ?? 0) : 0,
                'updated_at' => $aviatorCurrent ? $aviatorCurrent->updated_at?->diffForHumans() : '-',
            ],
            'andar_bahar' => [
                'name' => 'Andar Bahar Live',
                'badge_color' => 'warning text-dark',
                'icon' => 'fa-layer-group',
                'route' => route('admin.andar-bahar.index'),
                'round_id' => $abCurrent ? '#'.$abCurrent->round_id : 'N/A',
                'status' => $abCurrent ? $abCurrent->status : 'idle',
                'outcome' => $abCurrent && $abCurrent->winning_side ? strtoupper($abCurrent->winning_side) : 'Dealing...',
                'outcome_sub' => $abCurrent ? ('Joker: '.($abCurrent->joker_card['code'] ?? '--').' | Cards: '.($abCurrent->total_cards_dealt ?? 0)) : '',
                'is_manual' => $abCurrent ? (bool) $abCurrent->is_manual : false,
                'manual_target' => $abCurrent ? strtoupper($abCurrent->manual_winning_side ?? '') : null,
                'bets_count' => $abCurrent ? ($abCurrent->total_bets_count ?? 0) : 0,
                'bet_amount' => $abCurrent ? (float) ($abCurrent->total_bet_amount ?? 0) : 0,
                'updated_at' => $abCurrent ? $abCurrent->updated_at?->diffForHumans() : '-',
            ],
            'wingo' => [
                'name' => 'WinGo Color Game',
                'badge_color' => 'danger',
                'icon' => 'fa-palette',
                'route' => route('admin.wingo.index'),
                'round_id' => $wingoCurrent30s ? '#'.$wingoCurrent30s->period_id.' (30s)' : 'N/A',
                'status' => $wingoCurrent30s ? $wingoCurrent30s->status : 'active',
                'outcome' => $wingoCurrent30s && $wingoCurrent30s->winning_number !== null ? ('#'.$wingoCurrent30s->winning_number.' '.strtoupper($wingoCurrent30s->winning_color).' '.strtoupper($wingoCurrent30s->winning_size)) : 'Active Round',
                'outcome_sub' => '1M: #'.($wingoCurrent1m->period_id ?? '-').' | 3M: #'.($wingoCurrent3m->period_id ?? '-').' | 5M: #'.($wingoCurrent5m->period_id ?? '-'),
                'is_manual' => $wingoCurrent30s ? (bool) $wingoCurrent30s->is_manual : false,
                'manual_target' => $wingoCurrent30s ? $wingoCurrent30s->manual_number : null,
                'bets_count' => $wingoCurrent30s ? ($wingoCurrent30s->total_bets_count ?? 0) : 0,
                'bet_amount' => $wingoCurrent30s ? (float) ($wingoCurrent30s->total_bet_amount ?? 0) : 0,
                'updated_at' => $wingoCurrent30s ? $wingoCurrent30s->updated_at?->diffForHumans() : '-',
            ],
            'lottery' => [
                'name' => '2-Digit Lottery (Slots)',
                'badge_color' => 'info text-dark',
                'icon' => 'fa-ticket',
                'route' => route('admin.results.index'),
                'round_id' => $lotteryLatest ? ($lotteryLatest->result_date?->format('Y-m-d').' '.$lotteryLatest->slot_time) : 'N/A',
                'status' => $lotteryLatest ? $lotteryLatest->status : 'completed',
                'outcome' => $lotteryLatest ? ('XA:'.$lotteryLatest->XA.' XB:'.$lotteryLatest->XB.' XC:'.$lotteryLatest->XC) : 'No results',
                'outcome_sub' => $lotteryLatest ? ('XD:'.$lotteryLatest->XD.' XE:'.$lotteryLatest->XE.' XF:'.$lotteryLatest->XF) : '',
                'is_manual' => $lotteryLatest ? (bool) $lotteryLatest->is_manual : false,
                'manual_target' => null,
                'bets_count' => '-',
                'bet_amount' => 0,
                'updated_at' => $lotteryLatest ? $lotteryLatest->updated_at?->diffForHumans() : '-',
            ],
            'vortex' => [
                'name' => 'Vortex Wheel',
                'badge_color' => 'info text-dark',
                'icon' => 'fa-compact-disc',
                'route' => route('admin.vortex.index'),
                'round_id' => $vortexActive ? '#'.substr($vortexActive->game_id, -8) : ($vortexRecent->first() ? '#'.substr($vortexRecent->first()->game_id, -8) : 'N/A'),
                'status' => $vortexActive ? 'active' : ($vortexRecent->first()?->status ?? 'idle'),
                'outcome' => $vortexActive ? ('Spinning Ring '.$vortexActive->current_ring.' ('.$vortexActive->current_multiplier.'x)') : ($vortexRecent->first() ? strtoupper($vortexRecent->first()->status).' ('.$vortexRecent->first()->current_multiplier.'x)' : 'Idle'),
                'outcome_sub' => $vortexRecent->first() ? ('Latest Player: '.($vortexRecent->first()->user->name ?? 'Guest')) : '',
                'is_manual' => false,
                'manual_target' => null,
                'bets_count' => $vortexRecent->count(),
                'bet_amount' => (float) ($vortexRecent->first()?->bet_amount ?? 0),
                'updated_at' => $vortexRecent->first() ? $vortexRecent->first()->created_at?->diffForHumans() : '-',
            ],
            'mines' => [
                'name' => 'Mines 5x5 Live',
                'badge_color' => 'success',
                'icon' => 'fa-bomb',
                'route' => route('admin.mines.index'),
                'round_id' => $minesActive ? '#'.substr($minesActive->game_id, -8) : ($minesRecent->first() ? '#'.substr($minesRecent->first()->game_id, -8) : 'N/A'),
                'status' => $minesActive ? 'active' : ($minesRecent->first()?->status ?? 'idle'),
                'outcome' => $minesActive ? ($minesActive->current_step.' Safe Coins ('.$minesActive->current_multiplier.'x)') : ($minesRecent->first() ? strtoupper($minesRecent->first()->status).' ('.$minesRecent->first()->current_multiplier.'x)' : 'Idle'),
                'outcome_sub' => $minesRecent->first() ? ($minesRecent->first()->mines_count.' Mines | '.($minesRecent->first()->user->name ?? 'User')) : '',
                'is_manual' => false,
                'manual_target' => null,
                'bets_count' => $minesRecent->count(),
                'bet_amount' => (float) ($minesRecent->first()?->bet_amount ?? 0),
                'updated_at' => $minesRecent->first() ? $minesRecent->first()->created_at?->diffForHumans() : '-',
            ],
            'sports' => [
                'name' => 'Cricket Sportsbook',
                'badge_color' => 'primary',
                'icon' => 'fa-baseball-bat-ball',
                'route' => route('sports.cricket'),
                'round_id' => $sportsLive->first() ? $sportsLive->first()->name : ($sportsRecent->first()?->name ?? 'N/A'),
                'status' => $sportsLive->first() ? 'live' : ($sportsRecent->first()?->status ?? 'upcoming'),
                'outcome' => $sportsLive->first() ? ($sportsLive->first()->score_team_a ?? 'Live Match') : ($sportsRecent->first()?->winner_team ? ($sportsRecent->first()->winner_team.' Won') : 'Scheduled'),
                'outcome_sub' => $sportsRecent->first() ? ($sportsRecent->first()->team_a.' vs '.$sportsRecent->first()->team_b) : '',
                'is_manual' => false,
                'manual_target' => null,
                'bets_count' => $sportsRecent->first()?->bets()->count() ?? 0,
                'bet_amount' => (float) ($sportsRecent->first()?->bets()->sum('stake') ?? 0),
                'updated_at' => $sportsRecent->first() ? $sportsRecent->first()->updated_at?->diffForHumans() : '-',
            ],
        ];

        // Return JSON for AJAX polling auto-refresh
        if ($request->wantsJson() || $request->query('ajax') == '1') {
            return response()->json([
                'success' => true,
                'timestamp' => Carbon::now('Asia/Kolkata')->format('h:i:s A'),
                'matrix' => $liveGamesMatrix,
            ]);
        }

        return view('admin.live_results.index', [
            'matrix' => $liveGamesMatrix,
            'dtRecent' => $dtRecent,
            'tpRecent' => $tpRecent,
            'rouletteRecent' => $rouletteRecent,
            'horseRecent' => $horseRecent,
            'aviatorRecent' => $aviatorRecent,
            'abRecent' => $abRecent,
            'wingoRecent' => $wingoRecent,
            'lotteryRecent' => $lotteryRecent,
            'vortexRecent' => $vortexRecent,
            'minesRecent' => $minesRecent,
            'sportsRecent' => $sportsRecent,
            'selectedGame' => $selectedGame,
        ]);
    }
}
