<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'utr_number',
        'screenshot_path',
        'user_qr_path',
        'admin_proof_path',
        'payment_method',
        'account_details',
        'account_holder_name',
        'account_number',
        'ifsc_code',
        'status',
        'reviewed_by',
        'admin_notes',
    ];

    protected $appends = [
        'screenshot_url',
        'user_qr_url',
        'admin_proof_url',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Resolve a bulletproof accessible public URL for any uploaded file (new or legacy).
     */
    public static function resolveFileUrl(?string $path, string $subfolder = 'proofs'): ?string
    {
        if (empty($path)) {
            return null;
        }

        // Already absolute URL
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $cleanPath = ltrim(str_replace(['\\'], ['/'], $path), '/');
        $baseName = basename($cleanPath);

        // 1. Direct public folder: public/{cleanPath} (e.g. public/uploads/proofs/...)
        if (file_exists(public_path($cleanPath)) && is_file(public_path($cleanPath))) {
            return asset($cleanPath);
        }

        // 2. Direct public folder under uploads/: public/uploads/{cleanPath}
        if (file_exists(public_path('uploads/'.$cleanPath)) && is_file(public_path('uploads/'.$cleanPath))) {
            return asset('uploads/'.$cleanPath);
        }

        // 3. Direct public folder with subfolder + basename: public/uploads/{subfolder}/{basename}
        if (file_exists(public_path('uploads/'.$subfolder.'/'.$baseName)) && is_file(public_path('uploads/'.$subfolder.'/'.$baseName))) {
            return asset('uploads/'.$subfolder.'/'.$baseName);
        }

        // 4. In symlinked or copied public/storage/...
        if (file_exists(public_path('storage/'.$cleanPath)) && is_file(public_path('storage/'.$cleanPath))) {
            return asset('storage/'.$cleanPath);
        }

        // 5. In storage/app/public/... -> Stream via media.stream route
        if (file_exists(storage_path('app/public/'.$cleanPath)) && is_file(storage_path('app/public/'.$cleanPath))) {
            return route('media.stream', ['path' => $cleanPath]);
        }

        // 6. In storage/app/public/{subfolder}/{basename}
        if (file_exists(storage_path('app/public/'.$subfolder.'/'.$baseName)) && is_file(storage_path('app/public/'.$subfolder.'/'.$baseName))) {
            return route('media.stream', ['path' => $subfolder.'/'.$baseName]);
        }

        // 7. If path starts with uploads/, return asset URL
        if (str_starts_with($cleanPath, 'uploads/')) {
            return asset($cleanPath);
        }

        // Fallback: Default to media stream route which searches all folders
        return route('media.stream', ['path' => $cleanPath]);
    }

    public function getScreenshotUrlAttribute(): ?string
    {
        return self::resolveFileUrl($this->screenshot_path, 'proofs');
    }

    public function getUserQrUrlAttribute(): ?string
    {
        return self::resolveFileUrl($this->user_qr_path, 'proofs/user_qr');
    }

    public function getAdminProofUrlAttribute(): ?string
    {
        return self::resolveFileUrl($this->admin_proof_path, 'proofs/admin_proofs');
    }
}
