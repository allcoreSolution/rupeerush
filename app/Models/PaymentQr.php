<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentQr extends Model
{
    protected $fillable = [
        'platform',
        'platform_name',
        'upi_id',
        'upi_name',
        'qr_code_path',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    protected $appends = [
        'qr_code_url',
        'icon_class',
        'badge_color',
    ];

    /**
     * Get accessible public URL for QR Code.
     */
    public function getQrCodeUrlAttribute(): string
    {
        if (! empty($this->qr_code_path)) {
            $baseName = basename($this->qr_code_path);

            if (file_exists(public_path('uploads/qr/'.$baseName))) {
                return asset('uploads/qr/'.$baseName);
            }

            if (file_exists(public_path($this->qr_code_path))) {
                return asset($this->qr_code_path);
            }

            if (file_exists(public_path('storage/'.$this->qr_code_path))) {
                return asset('storage/'.$this->qr_code_path);
            }

            if (file_exists(storage_path('app/public/'.$this->qr_code_path))) {
                return url('/storage/'.$this->qr_code_path);
            }

            return asset('storage/'.$this->qr_code_path);
        }

        $upiId = urlencode($this->upi_id ?? 'rupeerush@upi');
        $upiName = urlencode($this->upi_name ?? 'rupeerush');

        return "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=upi://pay?pa={$upiId}%26pn={$upiName}";
    }

    /**
     * Get FontAwesome icon class matching the platform.
     */
    public function getIconClassAttribute(): string
    {
        return match (strtolower($this->platform)) {
            'gpay', 'googlepay' => 'fa-brands fa-google-pay',
            'phonepe' => 'fa-solid fa-mobile-screen-button',
            'paytm' => 'fa-solid fa-wallet',
            'bhim' => 'fa-solid fa-building-columns',
            'navi' => 'fa-solid fa-compass',
            default => 'fa-solid fa-qrcode',
        };
    }

    /**
     * Get brand color / badge class matching the platform.
     */
    public function getBadgeColorAttribute(): string
    {
        return match (strtolower($this->platform)) {
            'gpay', 'googlepay' => '#4285F4',
            'phonepe' => '#5f259f',
            'paytm' => '#00b9f5',
            'bhim' => '#005f73',
            'navi' => '#0a9396',
            default => '#495057',
        };
    }
}
