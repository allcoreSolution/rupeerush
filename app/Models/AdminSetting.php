<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    protected $fillable = [
        'site_name',
        'upi_id',
        'upi_name',
        'business_name',
        'bank_account_number',
        'bank_ifsc',
        'bank_name',
        'qr_code_path',
        'min_deposit',
        'max_deposit',
        'min_withdrawal',
        'win_multiplier',
        'win_commission_percent',
        'referral_commission_percent',
        'lottery_min_bet',
        'lottery_max_bet',
        'wingo_min_bet',
        'wingo_max_bet',
        'andar_multiplier',
        'bahar_multiplier',
        'andar_bahar_min_bet',
        'andar_bahar_max_bet',
        'andar_bahar_range_1_5',
        'andar_bahar_range_6_10',
        'andar_bahar_range_11_15',
        'andar_bahar_range_16_25',
        'andar_bahar_range_26_plus',
        'aviator_min_bet',
        'aviator_max_bet',
        'aviator_manual_multiplier',
        'mines_min_bet',
        'mines_max_bet',
        'mines_house_edge',
        'mines_jackpot_min_bet',
        'mines_jackpot_pool',
        'mines_control_mode',
        'mines_rigged_rate',
        'mines_force_blast_step',
        'mines_max_win_multiplier',
        'vortex_min_bet',
        'vortex_max_bet',
        'vortex_house_edge',
        'vortex_control_mode',
        'vortex_rigged_rate',
        'vortex_max_multiplier_cap',
        'teen_patti_min_bet',
        'teen_patti_max_bet',
        'teen_patti_odds_player',
        'teen_patti_odds_tie',
        'teen_patti_round_duration',
        'teen_patti_control_mode',
        'teen_patti_manual_winner',
        'dragon_tiger_min_bet',
        'dragon_tiger_max_bet',
        'dragon_tiger_odds_main',
        'dragon_tiger_odds_tie',
        'dragon_tiger_odds_suited_tie',
        'dragon_tiger_round_duration',
        'dragon_tiger_control_mode',
        'dragon_tiger_manual_winner',
        'roulette_min_bet',
        'roulette_max_bet',
        'roulette_round_duration',
        'roulette_control_mode',
        'roulette_manual_number',
        'roulette_red_multiplier',
        'roulette_black_multiplier',
        'roulette_red_weight',
        'roulette_black_weight',
        'roulette_green_weight',
        'horse_racing_min_bet',
        'horse_racing_max_bet',
        'horse_racing_round_duration',
        'horse_racing_control_mode',
        'horse_racing_manual_winner',
        'support_whatsapp',
        'support_email',
        'betting_lock_minutes',
        'auto_result_mode',
        'house_profit_rate',
        'instructions',
        'hero_banner_1',
        'hero_banner_2',
        'hero_banner_3',
        'hero_banner_4',
        'hero_banner_5',
        'hero_banner_6',
        'hero_banner_1_link',
        'hero_banner_2_link',
        'hero_banner_3_link',
        'hero_banner_4_link',
        'hero_banner_5_link',
        'hero_banner_6_link',
        'hero_banner_titles',
        'game_cards_config',
    ];

    protected function casts(): array
    {
        return [
            'hero_banner_titles' => 'array',
            'game_cards_config' => 'array',
            'min_deposit' => 'decimal:2',
            'max_deposit' => 'decimal:2',
            'min_withdrawal' => 'decimal:2',
            'win_multiplier' => 'decimal:2',
            'win_commission_percent' => 'decimal:2',
            'referral_commission_percent' => 'decimal:2',
            'lottery_min_bet' => 'decimal:2',
            'lottery_max_bet' => 'decimal:2',
            'wingo_min_bet' => 'decimal:2',
            'wingo_max_bet' => 'decimal:2',
            'andar_multiplier' => 'decimal:2',
            'bahar_multiplier' => 'decimal:2',
            'andar_bahar_min_bet' => 'decimal:2',
            'andar_bahar_max_bet' => 'decimal:2',
            'andar_bahar_range_1_5' => 'decimal:2',
            'andar_bahar_range_6_10' => 'decimal:2',
            'andar_bahar_range_11_15' => 'decimal:2',
            'andar_bahar_range_16_25' => 'decimal:2',
            'andar_bahar_range_26_plus' => 'decimal:2',
            'aviator_min_bet' => 'decimal:2',
            'aviator_max_bet' => 'decimal:2',
            'aviator_manual_multiplier' => 'decimal:2',
            'mines_min_bet' => 'decimal:2',
            'mines_max_bet' => 'decimal:2',
            'mines_house_edge' => 'decimal:2',
            'mines_jackpot_min_bet' => 'decimal:2',
            'mines_jackpot_pool' => 'decimal:2',
            'mines_control_mode' => 'string',
            'mines_rigged_rate' => 'decimal:2',
            'mines_force_blast_step' => 'integer',
            'mines_max_win_multiplier' => 'decimal:2',
            'vortex_min_bet' => 'decimal:2',
            'vortex_max_bet' => 'decimal:2',
            'vortex_house_edge' => 'decimal:2',
            'vortex_control_mode' => 'string',
            'vortex_rigged_rate' => 'decimal:2',
            'vortex_max_multiplier_cap' => 'decimal:2',
            'teen_patti_min_bet' => 'decimal:2',
            'teen_patti_max_bet' => 'decimal:2',
            'teen_patti_odds_player' => 'decimal:2',
            'teen_patti_odds_tie' => 'decimal:2',
            'teen_patti_round_duration' => 'integer',
            'dragon_tiger_min_bet' => 'decimal:2',
            'dragon_tiger_max_bet' => 'decimal:2',
            'dragon_tiger_odds_main' => 'decimal:2',
            'dragon_tiger_odds_tie' => 'decimal:2',
            'dragon_tiger_odds_suited_tie' => 'decimal:2',
            'dragon_tiger_round_duration' => 'integer',
            'roulette_min_bet' => 'decimal:2',
            'roulette_max_bet' => 'decimal:2',
            'roulette_round_duration' => 'integer',
            'roulette_manual_number' => 'integer',
            'roulette_red_multiplier' => 'decimal:2',
            'roulette_black_multiplier' => 'decimal:2',
            'roulette_red_weight' => 'decimal:2',
            'roulette_black_weight' => 'decimal:2',
            'roulette_green_weight' => 'decimal:2',
            'horse_racing_min_bet' => 'decimal:2',
            'horse_racing_max_bet' => 'decimal:2',
            'horse_racing_round_duration' => 'integer',
            'horse_racing_manual_winner' => 'integer',
            'auto_result_mode' => 'boolean',
            'house_profit_rate' => 'decimal:2',
        ];
    }

    public static function current(): self
    {
        $defaults = [
            'site_name' => 'rupeerush',
            'upi_id' => 'admin@upi',
            'upi_name' => 'rupeerush Official',
            'min_deposit' => 100.00,
            'max_deposit' => 50000.00,
            'min_withdrawal' => 200.00,
            'win_multiplier' => 90.00,
            'win_commission_percent' => 10.00,
            'referral_commission_percent' => 1.00,
            'lottery_min_bet' => 10.00,
            'lottery_max_bet' => 10000.00,
            'wingo_min_bet' => 1.00,
            'wingo_max_bet' => 50000.00,
            'andar_multiplier' => 1.95,
            'bahar_multiplier' => 1.95,
            'andar_bahar_min_bet' => 10.00,
            'andar_bahar_max_bet' => 50000.00,
            'andar_bahar_range_1_5' => 3.50,
            'andar_bahar_range_6_10' => 4.00,
            'andar_bahar_range_11_15' => 5.00,
            'andar_bahar_range_16_25' => 4.50,
            'andar_bahar_range_26_plus' => 8.00,
            'aviator_min_bet' => 10.00,
            'aviator_max_bet' => 50000.00,
            'mines_min_bet' => 1.00,
            'mines_max_bet' => 25000.00,
            'mines_house_edge' => 3.00,
            'mines_jackpot_min_bet' => 10.00,
            'mines_jackpot_pool' => 1417.61,
            'mines_control_mode' => 'fair',
            'mines_rigged_rate' => 0.00,
            'mines_force_blast_step' => 0,
            'mines_max_win_multiplier' => 0.00,
            'vortex_min_bet' => 1.00,
            'vortex_max_bet' => 25000.00,
            'vortex_house_edge' => 3.50,
            'vortex_control_mode' => 'fair',
            'vortex_rigged_rate' => 0.00,
            'vortex_max_multiplier_cap' => 0.00,
            'teen_patti_min_bet' => 5.00,
            'teen_patti_max_bet' => 25000.00,
            'teen_patti_odds_player' => 1.95,
            'teen_patti_odds_tie' => 9.00,
            'teen_patti_round_duration' => 25,
            'teen_patti_control_mode' => 'fair',
            'teen_patti_manual_winner' => null,
            'dragon_tiger_min_bet' => 5.00,
            'dragon_tiger_max_bet' => 25000.00,
            'dragon_tiger_odds_main' => 1.95,
            'dragon_tiger_odds_tie' => 9.00,
            'dragon_tiger_odds_suited_tie' => 50.00,
            'dragon_tiger_round_duration' => 25,
            'dragon_tiger_control_mode' => 'fair',
            'dragon_tiger_manual_winner' => null,
            'roulette_min_bet' => 10.00,
            'roulette_max_bet' => 25000.00,
            'roulette_round_duration' => 30,
            'roulette_control_mode' => 'fair',
            'roulette_manual_number' => null,
            'roulette_red_multiplier' => 5.00,
            'roulette_black_multiplier' => 7.00,
            'roulette_red_weight' => 70.00,
            'roulette_black_weight' => 29.00,
            'roulette_green_weight' => 1.00,
            'horse_racing_min_bet' => 10.00,
            'horse_racing_max_bet' => 25000.00,
            'horse_racing_round_duration' => 60,
            'horse_racing_control_mode' => 'fair',
            'horse_racing_manual_winner' => null,
            'support_whatsapp' => '9026217223',
            'support_email' => 'rupeerushonline@gmail.com',
            'betting_lock_minutes' => 2,
            'auto_result_mode' => true,
            'house_profit_rate' => 78.00,
        ];

        try {
            return static::firstOrCreate([], $defaults);
        } catch (\Throwable) {
            return (new static)->forceFill($defaults);
        }
    }

    /**
     * Determine if current round should trigger house profit protection (75% - 80% rate, default 78%).
     */
    public function shouldHouseWin(): bool
    {
        $rate = (float) ($this->house_profit_rate ?? 78.00);
        if ($rate <= 0.0) {
            return false;
        }
        if ($rate >= 100.0) {
            return true;
        }

        return mt_rand(1, 10000) <= (int) round($rate * 100);
    }

    /**
     * Get resolved accessible URL for QR Code with live hosting fallbacks.
     */
    public function getQrCodeUrlAttribute(): string
    {
        if (! empty($this->qr_code_path)) {
            $baseName = basename($this->qr_code_path);

            // 1. Direct public folder: public/uploads/qr/... (works on any shared hosting without symlink)
            if (file_exists(public_path('uploads/qr/'.$baseName))) {
                return asset('uploads/qr/'.$baseName);
            }

            // 2. Direct exact path matching in public_path
            if (file_exists(public_path($this->qr_code_path))) {
                return asset($this->qr_code_path);
            }

            // 3. Standard symlinked storage: public/storage/...
            if (file_exists(public_path('storage/'.$this->qr_code_path))) {
                return asset('storage/'.$this->qr_code_path);
            }

            // 4. File in storage/app/public/...: stream via fallback route
            if (file_exists(storage_path('app/public/'.$this->qr_code_path))) {
                return url('/storage/'.$this->qr_code_path);
            }

            return asset('storage/'.$this->qr_code_path);
        }

        // Dynamic fallback generator using configured UPI ID & Name
        $upiId = urlencode($this->upi_id ?? 'rupeerush@upi');
        $upiName = urlencode($this->upi_name ?? 'rupeerush');

        return "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=upi://pay?pa={$upiId}%26pn={$upiName}";
    }

    /**
     * Check if custom QR file is present on disk.
     */
    public function hasCustomQr(): bool
    {
        if (empty($this->qr_code_path)) {
            return false;
        }

        return file_exists(public_path($this->qr_code_path))
            || file_exists(public_path('storage/'.$this->qr_code_path))
            || file_exists(storage_path('app/public/'.$this->qr_code_path));
    }

    /**
     * Determine if admin has configured bank account details for direct deposit.
     */
    public function hasBankDetails(): bool
    {
        return ! empty($this->bank_account_number) || ! empty($this->bank_ifsc);
    }

    /**
     * Get effective business or payee name.
     */
    public function getEffectiveBusinessNameAttribute(): string
    {
        return $this->business_name ?: ($this->upi_name ?: $this->site_name);
    }

    /**
     * Get clean 12-digit Indian formatted phone number for WhatsApp URLs.
     */
    public function getFormattedWhatsappNumberAttribute(): string
    {
        $rawWa = $this->support_whatsapp ?: '9026217223';
        $supportWa = preg_replace('/[^0-9]/', '', (string) $rawWa);
        if (str_starts_with($supportWa, '0') && strlen($supportWa) === 11) {
            $supportWa = substr($supportWa, 1);
        }
        if (strlen($supportWa) === 10) {
            $supportWa = '91'.$supportWa;
        }

        return $supportWa ?: '919026217223';
    }

    /**
     * Generate pre-filled WhatsApp Registration URL for players.
     */
    public function getRegistrationWhatsappUrl(?string $referralCode = null): string
    {
        $siteName = $this->site_name ?? 'rupeerush';
        $message = "Hello Admin, I want to create a new player account on {$siteName}.";
        if (! empty($referralCode)) {
            $message .= ' My Referral Code is: '.trim($referralCode).'.';
        }
        $message .= ' Please register my account and provide login credentials.';

        return 'https://wa.me/'.$this->formatted_whatsapp_number.'?text='.rawurlencode($message);
    }

    /**
     * Get URL for Hero Banner by index (1, 2, or 3).
     */
    public function getHeroBannerUrl(int $index): string
    {
        $field = "hero_banner_{$index}";
        $path = $this->{$field} ?? null;

        if ($path) {
            if (file_exists(public_path($path))) {
                return asset($path);
            }
            if (file_exists(storage_path('app/public/'.$path))) {
                return asset('storage/'.$path);
            }
        }

        return asset("images/banners/banner_{$index}.svg");
    }

    /**
     * Check if a custom banner image is uploaded.
     */
    public function hasCustomBanner(int $index): bool
    {
        $field = "hero_banner_{$index}";
        $path = $this->{$field} ?? null;

        return ! empty($path) && (file_exists(public_path($path)) || file_exists(storage_path('app/public/'.$path)));
    }

    /**
     * Get configured Hero Banners list with fallback metadata (supports all 6 banners).
     *
     * @return array<int, array{index: int, image_url: string, is_custom: bool, link: string, title: string, subtitle: string}>
     */
    public function getHeroBannersList(): array
    {
        $titles = $this->hero_banner_titles ?? [];

        $defaultData = [
            1 => [
                'link' => route('sports.cricket'),
                'title' => 'Live Cricket Exchange (IPL & T20)',
                'subtitle' => 'Live Ball-by-Ball Score & Back/Lay Odds',
                'default_svg' => 'images/banners/banner_4.svg',
            ],
            2 => [
                'link' => route('lottery.index'),
                'title' => '2-Digit Lottery (XA–XJ)',
                'subtitle' => 'Official 90X Hourly Slot Draws',
                'default_svg' => 'images/banners/banner_1.svg',
            ],
            3 => [
                'link' => route('wingo.index'),
                'title' => 'WinGo Color Slot Game',
                'subtitle' => 'Live 30s Multiplier • Fast Payouts',
                'default_svg' => 'images/banners/banner_2.svg',
            ],
            4 => [
                'link' => route('aviator.index'),
                'title' => 'Aviator Crash (1,000X)',
                'subtitle' => 'Cashout Before Plane Flies Away',
                'default_svg' => 'images/banners/banner_5.svg',
            ],
            5 => [
                'link' => route('teen-patti.index'),
                'title' => 'Live Indian Casino',
                'subtitle' => 'Teen Patti, Andar Bahar & Roulette',
                'default_svg' => 'images/banners/banner_6.svg',
            ],
            6 => [
                'link' => route('wallet.deposit'),
                'title' => 'Instant 30s UPI Deposit',
                'subtitle' => 'Instant Approval & 24/7 Withdrawals',
                'default_svg' => 'images/banners/banner_3.svg',
            ],
        ];

        $list = [];
        for ($i = 1; $i <= 6; $i++) {
            $linkField = "hero_banner_{$i}_link";
            $hasCustom = $this->hasCustomBanner($i);
            $imageUrl = $hasCustom ? $this->getHeroBannerUrl($i) : asset($defaultData[$i]['default_svg']);
            $customTitle = $titles[$i]['title'] ?? null;
            $customSubtitle = $titles[$i]['subtitle'] ?? null;

            $list[] = [
                'index' => $i,
                'image_url' => $imageUrl,
                'is_custom' => $hasCustom,
                'link' => $this->{$linkField} ?: $defaultData[$i]['link'],
                'title' => $customTitle ?: $defaultData[$i]['title'],
                'subtitle' => $customSubtitle ?: $defaultData[$i]['subtitle'],
            ];
        }

        return $list;
    }

    /**
     * Get custom card thumbnail image URL for a game by ID (e.g. 'lottery', 'wingo', 'aviator', etc.).
     */
    public function getGameCardImage(string $gameId): ?string
    {
        $cardsConfig = $this->game_cards_config ?? [];
        $customPath = $cardsConfig[$gameId]['image'] ?? null;

        if ($customPath) {
            if (file_exists(public_path($customPath))) {
                return asset($customPath);
            }
            if (file_exists(storage_path('app/public/'.$customPath))) {
                return asset('storage/'.$customPath);
            }
        }

        // Static folder fallback: public/images/games/{$gameId}.png, .jpg, .webp
        foreach (['webp', 'png', 'jpg', 'jpeg'] as $ext) {
            $staticRel = "images/games/{$gameId}.{$ext}";
            if (file_exists(public_path($staticRel))) {
                return asset($staticRel);
            }
        }

        return null;
    }

    /**
     * Get custom metadata (badge, title) for a game card by ID.
     *
     * @return array{image?: string, badge?: string, title?: string}
     */
    public function getGameCardMeta(string $gameId): array
    {
        $cardsConfig = $this->game_cards_config ?? [];

        return $cardsConfig[$gameId] ?? [];
    }

    /**
     * Map aliases (e.g. cricket <-> sports).
     */
    public static function normalizeGameKey(string $gameId): string
    {
        return match ($gameId) {
            'sports' => 'cricket',
            default => $gameId,
        };
    }

    /**
     * Check if a specific game is active (default is true).
     */
    public function isGameActive(string $gameId): bool
    {
        $norm = self::normalizeGameKey($gameId);
        $cardsConfig = $this->game_cards_config ?? [];

        if (isset($cardsConfig[$norm]['is_active'])) {
            return (bool) $cardsConfig[$norm]['is_active'];
        }

        // Check alternate alias if present
        if ($norm === 'cricket' && isset($cardsConfig['sports']['is_active'])) {
            return (bool) $cardsConfig['sports']['is_active'];
        }

        return true;
    }

    /**
     * Set a game's active/inactive status.
     */
    public function setGameActive(string $gameId, bool $isActive): void
    {
        $norm = self::normalizeGameKey($gameId);
        $cardsConfig = $this->game_cards_config ?? [];

        if (! isset($cardsConfig[$norm])) {
            $cardsConfig[$norm] = [];
        }
        $cardsConfig[$norm]['is_active'] = $isActive;

        if ($norm === 'cricket') {
            if (! isset($cardsConfig['sports'])) {
                $cardsConfig['sports'] = [];
            }
            $cardsConfig['sports']['is_active'] = $isActive;
        }

        $this->game_cards_config = $cardsConfig;
        $this->save();
    }

    /**
     * Get user-friendly name for a game key.
     */
    public static function getGameName(string $gameId): string
    {
        return match (self::normalizeGameKey($gameId)) {
            'lottery' => '2 Digit Slot (Lottery)',
            'wingo' => 'WinGo Color Game',
            'andar_bahar' => 'Andar Bahar Live',
            'aviator' => 'Aviator Crash',
            'mines' => 'Mines 5x5',
            'vortex' => 'Vortex Wheel',
            'teen_patti' => '20-20 Teen Patti',
            'dragon_tiger' => 'Dragon Tiger Live',
            'roulette' => 'European Roulette 36x',
            'horse_racing' => 'Horse Racing Derby',
            'cricket', 'sports' => 'Cricket Live Sportsbook',
            default => ucfirst(str_replace('_', ' ', $gameId)),
        };
    }
}
