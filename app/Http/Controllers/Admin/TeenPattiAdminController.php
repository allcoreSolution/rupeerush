<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\TeenPattiBet;
use App\Models\TeenPattiRound;
use App\Services\TeenPattiEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeenPattiAdminController extends Controller
{
    /**
     * Admin Live 20-20 Teen Patti Control & Risk Exposure Panel.
     */
    public function index(Request $request): View
    {
        $roundData = TeenPattiEngine::getCurrentRound();
        /** @var TeenPattiRound $round */
        $round = $roundData['round_model'];

        // Bets placed on active round
        $activeBets = TeenPattiBet::with('user')
            ->where('round_id', $round->round_id)
            ->orderBy('id', 'desc')
            ->get();

        // Calculate real-time house exposure & risk
        $exposure = $this->calculateExposure($round);

        // Recent completed rounds (up to 50)
        $recentRounds = TeenPattiRound::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        $setting = AdminSetting::current();

        return view('admin.teen_patti.index', [
            'roundData' => $roundData,
            'round' => $round,
            'activeBets' => $activeBets,
            'exposure' => $exposure,
            'recentRounds' => $recentRounds,
            'setting' => $setting,
        ]);
    }

    /**
     * Set manual winner for current round (Force Player A, Player B, or Tie).
     */
    public function setManualWinner(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'round_id' => ['required', 'string'],
            'winner' => ['required', 'in:player_a,player_b,tie'],
        ]);

        $round = TeenPattiRound::where('round_id', $validated['round_id'])->firstOrFail();

        $round->is_manual = true;
        $round->manual_winner = $validated['winner'];
        $round->save();

        $setting = AdminSetting::current();
        $setting->update([
            'teen_patti_manual_winner' => $validated['winner'],
        ]);

        $targetName = strtoupper(str_replace('_', ' ', $validated['winner']));

        return back()->with('success', "Target locked to [{$targetName}] for Round #{$round->round_id}! The winner will be forced to {$targetName}.");
    }

    /**
     * Clear manual override and restore natural mode.
     */
    public function clearManualWinner(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'round_id' => ['required', 'string'],
        ]);

        $round = TeenPattiRound::where('round_id', $validated['round_id'])->firstOrFail();

        $round->is_manual = false;
        $round->manual_winner = null;
        $round->save();

        $setting = AdminSetting::current();
        $setting->update([
            'teen_patti_manual_winner' => null,
        ]);

        return back()->with('success', "Manual winner override cleared for Round #{$round->round_id}. Returned to [{$setting->teen_patti_control_mode}] mode.");
    }

    /**
     * Set Teen Patti automated risk control mode.
     */
    public function setControlMode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mode' => ['required', 'in:fair,house_trap,manual_override'],
        ]);

        $setting = AdminSetting::current();
        $setting->update([
            'teen_patti_control_mode' => $validated['mode'],
        ]);

        $modeNames = [
            'fair' => '100% Provably Fair (Natural Shuffle)',
            'house_trap' => 'House Protection Trap (Auto-picks lowest liability side)',
            'manual_override' => 'Manual Override (Follows admin preset winner)',
        ];

        return back()->with('success', "Teen Patti risk control mode set to: {$modeNames[$validated['mode']]}.");
    }

    /**
     * Update Teen Patti betting parameters and odds.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'teen_patti_min_bet' => ['required', 'numeric', 'min:1', 'max:50000'],
            'teen_patti_max_bet' => ['required', 'numeric', 'gte:teen_patti_min_bet', 'max:500000'],
            'teen_patti_odds_player' => ['required', 'numeric', 'min:1.01', 'max:10.00'],
            'teen_patti_odds_tie' => ['required', 'numeric', 'min:1.01', 'max:50.00'],
            'teen_patti_round_duration' => ['required', 'integer', 'min:15', 'max:120'],
            'teen_patti_control_mode' => ['required', 'in:fair,house_trap,manual_override'],
        ]);

        $setting = AdminSetting::current();
        $setting->update([
            'teen_patti_min_bet' => round((float) $validated['teen_patti_min_bet'], 2),
            'teen_patti_max_bet' => round((float) $validated['teen_patti_max_bet'], 2),
            'teen_patti_odds_player' => round((float) $validated['teen_patti_odds_player'], 2),
            'teen_patti_odds_tie' => round((float) $validated['teen_patti_odds_tie'], 2),
            'teen_patti_round_duration' => (int) $validated['teen_patti_round_duration'],
            'teen_patti_control_mode' => $validated['teen_patti_control_mode'],
        ]);

        return back()->with('success', 'Teen Patti casino settings updated successfully.');
    }

    /**
     * View history of winning players and credited payouts.
     */
    public function winners(Request $request): View
    {
        $query = TeenPattiBet::with(['user', 'round'])
            ->where('status', 'won')
            ->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('round_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $totalWinners = (clone $query)->count();
        $totalPayout = (float) (clone $query)->sum('payout_amount');

        $winners = $query->paginate(25)->withQueryString();

        return view('admin.teen_patti.winners', [
            'winners' => $winners,
            'totalWinners' => $totalWinners,
            'totalPayout' => $totalPayout,
            'search' => $request->search ?? '',
        ]);
    }

    /**
     * Calculate financial risk exposure for the house on current active round.
     *
     * @return array<string, mixed>
     */
    private function calculateExposure(TeenPattiRound $round): array
    {
        $bets = TeenPattiBet::where('round_id', $round->round_id)->get();

        $totalWagered = (float) $bets->sum('bet_amount');

        $betA = (float) $bets->where('bet_type', 'player_a')->sum('bet_amount');
        $betB = (float) $bets->where('bet_type', 'player_b')->sum('bet_amount');
        $betTie = (float) $bets->where('bet_type', 'tie')->sum('bet_amount');
        $betPPA = (float) $bets->where('bet_type', 'pair_plus_a')->sum('bet_amount');
        $betPPB = (float) $bets->where('bet_type', 'pair_plus_b')->sum('bet_amount');

        $setting = AdminSetting::current();
        $oddsPlayer = (float) ($setting->teen_patti_odds_player ?? 1.95);
        $oddsTie = (float) ($setting->teen_patti_odds_tie ?? 9.00);

        // Liability if Player A Wins
        $payoutIfA = $betA * $oddsPlayer;
        $netIfA = $totalWagered - $payoutIfA;

        // Liability if Player B Wins
        $payoutIfB = $betB * $oddsPlayer;
        $netIfB = $totalWagered - $payoutIfB;

        // Liability if Tie Wins (Tie bet pays 9X, main A & B bets are refunded)
        $payoutIfTie = ($betTie * $oddsTie) + $betA + $betB;
        $netIfTie = $totalWagered - $payoutIfTie;

        // Determine safest outcome for the house
        $safest = 'player_a';
        $maxNet = $netIfA;

        if ($netIfB > $maxNet) {
            $safest = 'player_b';
            $maxNet = $netIfB;
        }

        if ($netIfTie > $maxNet && $betTie == 0) {
            $safest = 'tie';
        }

        return [
            'total_wagered' => $totalWagered,
            'total_bets_count' => $bets->count(),
            'bet_a' => $betA,
            'bet_b' => $betB,
            'bet_tie' => $betTie,
            'bet_ppa' => $betPPA,
            'bet_ppb' => $betPPB,
            'payout_if_a' => $payoutIfA,
            'payout_if_b' => $payoutIfB,
            'payout_if_tie' => $payoutIfTie,
            'net_if_a' => $netIfA,
            'net_if_b' => $netIfB,
            'net_if_tie' => $netIfTie,
            'safest_winner' => $safest,
        ];
    }
}
