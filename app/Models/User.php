<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'phone',
        'email',
        'password',
        'balance',
        'role',
        'admin_role_id',
        'status',
        'referral_code',
        'referred_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->referral_code)) {
                $user->referral_code = static::generateReferralCode();
            }
        });
    }

    public static function generateReferralCode(): string
    {
        do {
            $code = 'REF'.strtoupper(Str::random(6));
        } while (static::where('referral_code', $code)->exists());

        return $code;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDemo(): bool
    {
        return $this->username === 'demoplayer' || $this->email === 'demo@rupeerush.com';
    }

    public function adminRole()
    {
        return $this->belongsTo(AdminRole::class, 'admin_role_id');
    }

    public function isSuperAdmin(): bool
    {
        if (! $this->isAdmin()) {
            return false;
        }

        // If no role assigned, or role is super-admin / system, treat as full Super Admin
        if ($this->admin_role_id === null) {
            return true;
        }

        $role = $this->adminRole;

        return $role ? ($role->slug === 'super-admin' || $role->is_system) : true;
    }

    public function hasPermission(string $permission): bool
    {
        if (! $this->isAdmin()) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->adminRole?->hasPermission($permission) ?? false;
    }

    public function bets()
    {
        return $this->hasMany(Bet::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function minesGames()
    {
        return $this->hasMany(MinesGame::class);
    }

    public function sportsBets()
    {
        return $this->hasMany(SportsBet::class);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function referralCommissions()
    {
        return $this->hasMany(ReferralCommission::class, 'parent_user_id');
    }

    public function paidReferralCommission()
    {
        return $this->hasOne(ReferralCommission::class, 'child_user_id');
    }
}
