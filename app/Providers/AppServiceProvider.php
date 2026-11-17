<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        $dirs = [
            public_path('uploads'),
            public_path('uploads/qr'),
            public_path('uploads/proofs'),
            public_path('uploads/proofs/user_qr'),
            public_path('uploads/proofs/admin_proofs'),
        ];

        foreach ($dirs as $dir) {
            if (! file_exists($dir)) {
                @mkdir($dir, 0755, true);
            }
        }
    }
}
