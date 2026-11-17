<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AdminRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_system',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system' => 'boolean',
    ];

    /**
     * Users assigned to this administrative role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'admin_role_id');
    }

    /**
     * Check if this role grants a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->slug === 'super-admin' || $this->is_system) {
            return true;
        }

        $perms = $this->permissions ?? [];

        return in_array($permission, $perms, true);
    }

    /**
     * Master catalog of all administrative permissions grouped by functional area.
     *
     * @return array<string, array<string, string>>
     */
    public static function permissionCatalog(): array
    {
        return [
            'Overview & Users' => [
                'dashboard' => 'Dashboard & Realtime Analytics Overview',
                'users_view' => 'View Registered Players List & Details',
                'users_manage' => 'Manual Balance Adjustment & Account Ban/Unban',
            ],
            'Payments & Banking' => [
                'deposits_view' => 'View Deposit Requests & Proofs',
                'deposits_manage' => 'Approve or Reject Player Deposits',
                'withdrawals_view' => 'View Player Withdrawal Requests',
                'withdrawals_manage' => 'Approve or Reject Withdrawal Requests',
                'daily_transactions' => 'View Daily Transaction Ledger & Revenue Metrics',
                'payment_settings' => 'Payment Gateway, UPI QR, Bank & Betting Limits',
            ],
            'Bets Monitoring' => [
                'bets_view' => 'Live Bets Feed & Complete Wager Logs',
            ],
            'Game Results & Controls' => [
                'game_lottery' => '2-Digit Lottery (Manual Results & Instant Generation)',
                'game_wingo' => 'WinGo Color Game (Manual Result Override & Win Targets)',
                'game_andar_bahar' => 'Andar Bahar Live (Manual Winner & Payout Odds)',
                'game_aviator' => 'Aviator Crash (Manual Crash Multiplier & House Edge)',
                'game_mines' => 'Mines 5x5 (Settings & Force Bust / Cashout)',
                'game_vortex' => 'Vortex Wheel (Settings & Force Bust / Cashout)',
                'game_teen_patti' => '20-20 Teen Patti (Manual Winner Override & Control Mode)',
                'game_roulette' => 'European Roulette (Manual Number Override & Control Mode)',
                'game_horse_racing' => 'Horse Racing Derby (Manual Winner Override & Control Mode)',
                'game_dragon_tiger' => 'Dragon Tiger Live (Manual Winner Override & Control Mode)',
            ],
            'Winner Reports' => [
                'winners_lottery' => '2-Digit Slot Winners List',
                'winners_wingo' => 'WinGo Color Winners List',
                'winners_andar_bahar' => 'Andar Bahar Winners List',
                'winners_aviator' => 'Aviator Winners List',
                'winners_mines' => 'Mines 5x5 Winners List',
                'winners_vortex' => 'Vortex Wheel Winners List',
                'winners_teen_patti' => 'Teen Patti Winners List',
                'winners_roulette' => 'Roulette 36x Winners List',
                'winners_horse_racing' => 'Horse Racing Derby Winners List',
                'winners_dragon_tiger' => 'Dragon Tiger Winners List',
            ],
            'System & Administration' => [
                'support_settings' => 'Manage Floating Support Contacts (WhatsApp & Email)',
                'roles_management' => 'Manage Roles, Permissions & Staff Accounts',
            ],
        ];
    }

    /**
     * Generate unique slug from name.
     */
    public static function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $count = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$count}";
            $count++;
        }

        return $slug;
    }
}
