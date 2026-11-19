<?php

namespace App\Http\Middleware;

use App\Models\AdminSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGameActive
{
    /**
     * Handle an incoming request and ensure target game is currently active.
     */
    public function handle(Request $request, Closure $next, string $gameKey): Response
    {
        $setting = AdminSetting::current();
        $normalizedKey = AdminSetting::normalizeGameKey($gameKey);

        if (! $setting->isGameActive($normalizedKey)) {
            $gameName = AdminSetting::getGameName($normalizedKey);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'game_inactive',
                    'message' => "{$gameName} is currently inactive for scheduled maintenance. Please check back shortly.",
                ], 503);
            }

            return response()->view('games.maintenance', [
                'gameKey' => $normalizedKey,
                'gameName' => $gameName,
            ], 503);
        }

        return $next($request);
    }
}
