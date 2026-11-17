<?php

use App\Http\Controllers\Admin\AdminBetController;
use App\Http\Controllers\Admin\AndarBaharAdminController;
use App\Http\Controllers\Admin\AviatorAdminController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DragonTigerAdminController;
use App\Http\Controllers\Admin\HorseRacingAdminController;
use App\Http\Controllers\Admin\MinesAdminController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\PlayerLedgerController;
use App\Http\Controllers\Admin\ResultController as AdminResultController;
use App\Http\Controllers\Admin\RolePermissionController as AdminRolePermissionController;
use App\Http\Controllers\Admin\RouletteAdminController;
use App\Http\Controllers\Admin\SupportSettingsController;
use App\Http\Controllers\Admin\TeenPattiAdminController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VortexAdminController;
use App\Http\Controllers\Admin\WingoAdminController;
use App\Http\Controllers\AndarBaharController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AviatorController;
use App\Http\Controllers\BetController;
use App\Http\Controllers\CricketController;
use App\Http\Controllers\DragonTigerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HorseRacingController;
use App\Http\Controllers\MinesController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\RouletteController;
use App\Http\Controllers\TeenPattiController;
use App\Http\Controllers\VortexController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WingoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/lottery', [HomeController::class, 'lottery'])->name('lottery.index');
Route::get('/wingo', [WingoController::class, 'index'])->name('wingo.index');
Route::get('/andar-bahar', [AndarBaharController::class, 'index'])->name('andar-bahar.index');
Route::get('/aviator', [AviatorController::class, 'index'])->name('aviator.index');
Route::get('/mines', [MinesController::class, 'index'])->name('mines.index');
Route::get('/vortex', [VortexController::class, 'index'])->name('vortex.index');
Route::get('/teen-patti', [TeenPattiController::class, 'index'])->name('teen-patti.index');
Route::get('/dragon-tiger', [DragonTigerController::class, 'index'])->name('dragon-tiger.index');
Route::get('/roulette', [RouletteController::class, 'index'])->name('roulette.index');
Route::get('/horse-racing', [HorseRacingController::class, 'index'])->name('horse-racing.index');
Route::get('/sports/cricket', [CricketController::class, 'index'])->name('sports.cricket');
Route::get('/sports/cricket/match/{id}', [CricketController::class, 'showMatch'])->name('sports.cricket.match');

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Real-time API Endpoints
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/live-status', [ApiController::class, 'getLiveStatus'])->name('live.status');
    Route::get('/results/history', [ApiController::class, 'getHistory'])->name('results.history');

    // WinGo Realtime APIs
    Route::get('/wingo/status', [WingoController::class, 'getLiveStatus'])->name('wingo.status');
    Route::get('/wingo/history', [WingoController::class, 'getHistory'])->name('wingo.history');
    Route::get('/wingo/my-bets', [WingoController::class, 'getMyBets'])->name('wingo.my-bets');
    Route::post('/wingo/place-bet', [WingoController::class, 'placeBet'])->name('wingo.place-bet');

    // Andar Bahar Realtime APIs
    Route::get('/andar-bahar/status', [AndarBaharController::class, 'getLiveStatus'])->name('andar-bahar.status');
    Route::get('/andar-bahar/history', [AndarBaharController::class, 'getHistory'])->name('andar-bahar.history');
    Route::get('/andar-bahar/my-bets', [AndarBaharController::class, 'getMyBets'])->name('andar-bahar.my-bets');
    Route::post('/andar-bahar/place-bet', [AndarBaharController::class, 'placeBet'])->name('andar-bahar.place-bet');

    // Aviator Realtime APIs
    Route::get('/aviator/status', [AviatorController::class, 'getLiveStatus'])->name('aviator.status');
    Route::get('/aviator/history', [AviatorController::class, 'getHistory'])->name('aviator.history');
    Route::get('/aviator/my-bets', [AviatorController::class, 'getMyBets'])->name('aviator.my-bets');
    Route::post('/aviator/place-bet', [AviatorController::class, 'placeBet'])->name('aviator.place-bet');
    Route::post('/aviator/cancel-bet', [AviatorController::class, 'cancelBet'])->name('aviator.cancel-bet');
    Route::post('/aviator/cashout', [AviatorController::class, 'cashOut'])->name('aviator.cashout');

    // Mines Realtime APIs
    Route::get('/mines/status', [MinesController::class, 'getStatus'])->name('mines.status');
    Route::get('/mines/ladder', [MinesController::class, 'getLadder'])->name('mines.ladder');
    Route::get('/mines/history', [MinesController::class, 'getHistory'])->name('mines.history');
    Route::post('/mines/start', [MinesController::class, 'startGame'])->name('mines.start');
    Route::post('/mines/reveal', [MinesController::class, 'revealTile'])->name('mines.reveal');
    Route::post('/mines/cashout', [MinesController::class, 'cashOut'])->name('mines.cashout');

    // Vortex Realtime APIs
    Route::get('/vortex/status', [VortexController::class, 'getStatus'])->name('vortex.status');
    Route::get('/vortex/history', [VortexController::class, 'getHistory'])->name('vortex.history');
    Route::post('/vortex/start', [VortexController::class, 'startGame'])->name('vortex.start');
    Route::post('/vortex/spin', [VortexController::class, 'spin'])->name('vortex.spin');
    Route::post('/vortex/part-cashout', [VortexController::class, 'partCashout'])->name('vortex.part-cashout');
    Route::post('/vortex/cashout', [VortexController::class, 'cashOut'])->name('vortex.cashout');

    // Teen Patti 20-20 Realtime APIs
    Route::get('/teen-patti/status', [TeenPattiController::class, 'getLiveStatus'])->name('teen-patti.status');
    Route::get('/teen-patti/history', [TeenPattiController::class, 'getHistory'])->name('teen-patti.history');
    Route::get('/teen-patti/my-bets', [TeenPattiController::class, 'getMyBets'])->name('teen-patti.my-bets');
    Route::post('/teen-patti/place-bet', [TeenPattiController::class, 'placeBet'])->name('teen-patti.place-bet');

    // Dragon Tiger Live Realtime APIs
    Route::get('/dragon-tiger/status', [DragonTigerController::class, 'getLiveStatus'])->name('dragon-tiger.status');
    Route::get('/dragon-tiger/history', [DragonTigerController::class, 'getHistory'])->name('dragon-tiger.history');
    Route::get('/dragon-tiger/my-bets', [DragonTigerController::class, 'getMyBets'])->name('dragon-tiger.my-bets');
    Route::post('/dragon-tiger/place-bet', [DragonTigerController::class, 'placeBet'])->name('dragon-tiger.place-bet');

    // European Roulette Realtime APIs
    Route::get('/roulette/status', [RouletteController::class, 'getLiveStatus'])->name('roulette.status');
    Route::get('/roulette/history', [RouletteController::class, 'getHistory'])->name('roulette.history');
    Route::get('/roulette/my-bets', [RouletteController::class, 'getMyBets'])->name('roulette.my-bets');
    Route::post('/roulette/place-bet', [RouletteController::class, 'placeBet'])->name('roulette.place-bet');

    // Horse Racing Realtime APIs
    Route::get('/horse-racing/status', [HorseRacingController::class, 'getLiveStatus'])->name('horse-racing.status');
    Route::get('/horse-racing/history', [HorseRacingController::class, 'getHistory'])->name('horse-racing.history');
    Route::get('/horse-racing/my-bets', [HorseRacingController::class, 'getMyBets'])->name('horse-racing.my-bets');
    Route::post('/horse-racing/place-bet', [HorseRacingController::class, 'placeBet'])->name('horse-racing.place-bet');

    // Cricket Sportsbook & Exchange Realtime APIs
    Route::get('/sports/cricket/matches', [CricketController::class, 'getLiveMatchesApi'])->name('sports.cricket.matches');
    Route::get('/sports/cricket/match/{id}', [CricketController::class, 'getMatchDetailsApi'])->name('sports.cricket.match.details');
    Route::get('/sports/cricket/my-bets', [CricketController::class, 'getMyBets'])->name('sports.cricket.my-bets');
    Route::post('/sports/cricket/place-bet', [CricketController::class, 'placeBet'])->name('sports.cricket.place-bet');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Wallet & Betting)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Betting
    Route::post('/bets/place', [BetController::class, 'placeBet'])->name('bets.place');
    Route::get('/my-bets', [BetController::class, 'myBets'])->name('bets.history');

    // Wallet & Payments
    Route::get('/wallet/deposit', [WalletController::class, 'depositPage'])->name('wallet.deposit');
    Route::post('/wallet/deposit', [WalletController::class, 'submitDeposit'])->name('wallet.deposit.submit');
    Route::get('/wallet/withdraw', [WalletController::class, 'withdrawPage'])->name('wallet.withdraw');
    Route::post('/wallet/withdraw', [WalletController::class, 'submitWithdrawal'])->name('wallet.withdraw.submit');
    Route::get('/wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');

    // Referral System (Invite & Earn)
    Route::get('/referral', [ReferralController::class, 'index'])->name('referral.index');
});

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes (Guest & Logout)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showAdminLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'adminLogin'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'adminLogout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Management Panel (Protected by Auth & Admin Role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard (accessible by all administrators and staff)
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Staff & Role Permissions Management
    Route::middleware('permission:roles_management')->group(function () {
        Route::get('/roles', [AdminRolePermissionController::class, 'index'])->name('roles.index');
        Route::post('/roles', [AdminRolePermissionController::class, 'storeRole'])->name('roles.store');
        Route::put('/roles/{id}', [AdminRolePermissionController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{id}', [AdminRolePermissionController::class, 'destroyRole'])->name('roles.destroy');
        Route::post('/staff', [AdminRolePermissionController::class, 'storeStaff'])->name('staff.store');
        Route::put('/staff/{id}', [AdminRolePermissionController::class, 'updateStaff'])->name('staff.update');
        Route::post('/staff/{id}/toggle', [AdminRolePermissionController::class, 'toggleStaffStatus'])->name('staff.toggle');
        Route::post('/staff/{id}/toggle-status', [AdminRolePermissionController::class, 'toggleStaffStatus'])->name('staff.toggle-status');
        Route::delete('/staff/{id}', [AdminRolePermissionController::class, 'destroyStaff'])->name('staff.destroy');
    });

    // Dedicated Support Contacts (WhatsApp & Email Floating Buttons)
    Route::middleware('permission:support_settings')->group(function () {
        Route::get('/support', [SupportSettingsController::class, 'index'])->name('support.index');
        Route::post('/support', [SupportSettingsController::class, 'update'])->name('support.update');
    });

    // Payment & General Settings Management
    Route::middleware('permission:payment_settings')->group(function () {
        Route::get('/payments/settings', [AdminPaymentController::class, 'settings'])->name('payments.settings');
        Route::post('/payments/settings', [AdminPaymentController::class, 'updateSettings'])->name('payments.settings.update');
        Route::post('/payments/qrs', [AdminPaymentController::class, 'storeQr'])->name('payments.qrs.store');
        Route::post('/payments/qrs/{id}/toggle', [AdminPaymentController::class, 'toggleQr'])->name('payments.qrs.toggle');
        Route::delete('/payments/qrs/{id}', [AdminPaymentController::class, 'deleteQr'])->name('payments.qrs.delete');
        Route::get('/settings', [AdminPaymentController::class, 'settings'])->name('settings');
    });

    // Daily Transactions Ledger
    Route::middleware('permission:daily_transactions')->group(function () {
        Route::get('/payments/daily', [AdminPaymentController::class, 'dailyTransactions'])->name('payments.daily');
    });

    // Deposit Requests Management
    Route::middleware('permission:deposits_view')->group(function () {
        Route::get('/payments/deposits', [AdminPaymentController::class, 'deposits'])->name('payments.deposits');
    });
    Route::middleware('permission:deposits_manage')->group(function () {
        Route::post('/payments/deposits/{id}/approve', [AdminPaymentController::class, 'approveDeposit'])->name('payments.deposits.approve');
        Route::post('/payments/deposits/{id}/reject', [AdminPaymentController::class, 'rejectDeposit'])->name('payments.deposits.reject');
    });

    // Withdrawal Requests Management
    Route::middleware('permission:withdrawals_view')->group(function () {
        Route::get('/payments/withdrawals', [AdminPaymentController::class, 'withdrawals'])->name('payments.withdrawals');
    });
    Route::middleware('permission:withdrawals_manage')->group(function () {
        Route::post('/payments/withdrawals/{id}/approve', [AdminPaymentController::class, 'approveWithdrawal'])->name('payments.withdrawals.approve');
        Route::post('/payments/withdrawals/{id}/reject', [AdminPaymentController::class, 'rejectWithdrawal'])->name('payments.withdrawals.reject');
    });

    // Game Results & Manipulation Control (2-Digit Lottery)
    Route::middleware('permission:game_lottery')->group(function () {
        Route::get('/results', [AdminResultController::class, 'index'])->name('results.index');
        Route::post('/results/manual', [AdminResultController::class, 'saveManualResult'])->name('results.manual');
        Route::post('/results/instant-generate', [AdminResultController::class, 'instantGenerate'])->name('results.instant-generate');
    });
    Route::middleware('permission:winners_lottery')->group(function () {
        Route::get('/winners', [AdminResultController::class, 'winners'])->name('winners.index');
    });

    // User Management
    Route::middleware('permission:users_view')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    });
    Route::middleware('permission:users_manage')->group(function () {
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::post('/users/{id}/balance', [AdminUserController::class, 'adjustBalance'])->name('users.balance');
        Route::post('/users/{id}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    });

    // Player Account Ledger & Statement Hub
    Route::middleware('permission:users_view')->group(function () {
        Route::get('/ledger', [PlayerLedgerController::class, 'index'])->name('ledger.index');
        Route::get('/ledger/{user}', [PlayerLedgerController::class, 'show'])->name('ledger.show');
        Route::get('/ledger/{user}/export', [PlayerLedgerController::class, 'exportCsv'])->name('ledger.export');
    });
    Route::middleware('permission:users_manage')->group(function () {
        Route::post('/ledger/{user}/balance', [PlayerLedgerController::class, 'adjustBalance'])->name('ledger.balance');
    });

    // All Game Bets Monitoring (2-Digit Lottery & WinGo Color Game)
    Route::middleware('permission:bets_view')->group(function () {
        Route::get('/bets', [AdminBetController::class, 'index'])->name('bets.index');
    });

    // WinGo Color Game Management
    Route::middleware('permission:game_wingo')->group(function () {
        Route::get('/wingo', [WingoAdminController::class, 'index'])->name('wingo.index');
        Route::post('/wingo/manual-number', [WingoAdminController::class, 'setManualNumber'])->name('wingo.manual-number');
        Route::post('/wingo/manual-target', [WingoAdminController::class, 'setManualTarget'])->name('wingo.manual-target');
        Route::post('/wingo/clear-manual', [WingoAdminController::class, 'clearManual'])->name('wingo.clear-manual');
    });
    Route::middleware('permission:winners_wingo')->group(function () {
        Route::get('/wingo/winners', [WingoAdminController::class, 'winners'])->name('wingo.winners');
    });

    // Andar Bahar Live Casino Management & Result Control
    Route::middleware('permission:game_andar_bahar')->group(function () {
        Route::get('/andar-bahar', [AndarBaharAdminController::class, 'index'])->name('andar-bahar.index');
        Route::post('/andar-bahar/manual-winner', [AndarBaharAdminController::class, 'setManualWinner'])->name('andar-bahar.manual-winner');
        Route::post('/andar-bahar/clear-manual', [AndarBaharAdminController::class, 'clearManualWinner'])->name('andar-bahar.clear-manual');
        Route::post('/andar-bahar/settings', [AndarBaharAdminController::class, 'updateSettings'])->name('andar-bahar.settings');
    });
    Route::middleware('permission:winners_andar_bahar')->group(function () {
        Route::get('/andar-bahar/winners', [AndarBaharAdminController::class, 'winners'])->name('andar-bahar.winners');
    });

    // Aviator Crash Game Management & Result Control
    Route::middleware('permission:game_aviator')->group(function () {
        Route::get('/aviator', [AviatorAdminController::class, 'index'])->name('aviator.index');
        Route::post('/aviator/set-manual', [AviatorAdminController::class, 'setManualCrashMultiplier'])->name('aviator.set-manual');
        Route::post('/aviator/clear-manual', [AviatorAdminController::class, 'clearManualCrashMultiplier'])->name('aviator.clear-manual');
        Route::post('/aviator/settings', [AviatorAdminController::class, 'updateSettings'])->name('aviator.update-settings');
    });
    Route::middleware('permission:winners_aviator')->group(function () {
        Route::get('/aviator/winners', [AviatorAdminController::class, 'winners'])->name('aviator.winners');
    });

    // Mines 5x5 Game Management & Result Control
    Route::middleware('permission:game_mines')->group(function () {
        Route::get('/mines', [MinesAdminController::class, 'index'])->name('mines.index');
        Route::post('/mines/settings', [MinesAdminController::class, 'updateSettings'])->name('mines.settings');
        Route::post('/mines/force-bust/{id}', [MinesAdminController::class, 'forceBust'])->name('mines.force-bust');
        Route::post('/mines/force-cashout/{id}', [MinesAdminController::class, 'forceCashout'])->name('mines.force-cashout');
    });
    Route::middleware('permission:winners_mines')->group(function () {
        Route::get('/mines/winners', [MinesAdminController::class, 'winners'])->name('mines.winners');
    });

    // Vortex Official Game Management & Result Control
    Route::middleware('permission:game_vortex')->group(function () {
        Route::get('/vortex', [VortexAdminController::class, 'index'])->name('vortex.index');
        Route::post('/vortex/settings', [VortexAdminController::class, 'updateSettings'])->name('vortex.settings');
        Route::post('/vortex/force-bust/{id}', [VortexAdminController::class, 'forceBust'])->name('vortex.force-bust');
        Route::post('/vortex/force-cashout/{id}', [VortexAdminController::class, 'forceCashout'])->name('vortex.force-cashout');
    });
    Route::middleware('permission:winners_vortex')->group(function () {
        Route::get('/vortex/winners', [VortexAdminController::class, 'winners'])->name('vortex.winners');
    });

    // 20-20 Teen Patti Live Casino Management & Result Control
    Route::middleware('permission:game_teen_patti')->group(function () {
        Route::get('/teen-patti', [TeenPattiAdminController::class, 'index'])->name('teen-patti.index');
        Route::post('/teen-patti/set-winner', [TeenPattiAdminController::class, 'setManualWinner'])->name('teen-patti.set-winner');
        Route::post('/teen-patti/clear-manual', [TeenPattiAdminController::class, 'clearManualWinner'])->name('teen-patti.clear-manual');
        Route::post('/teen-patti/mode', [TeenPattiAdminController::class, 'setControlMode'])->name('teen-patti.mode');
        Route::post('/teen-patti/settings', [TeenPattiAdminController::class, 'updateSettings'])->name('teen-patti.settings');
    });
    Route::middleware('permission:winners_teen_patti')->group(function () {
        Route::get('/teen-patti/winners', [TeenPattiAdminController::class, 'winners'])->name('teen-patti.winners');
    });

    // European Roulette Live Management & Result Control
    Route::middleware('permission:game_roulette')->group(function () {
        Route::get('/roulette', [RouletteAdminController::class, 'index'])->name('roulette.index');
        Route::post('/roulette/set-number', [RouletteAdminController::class, 'setManualNumber'])->name('roulette.set-number');
        Route::post('/roulette/clear-manual', [RouletteAdminController::class, 'clearManualNumber'])->name('roulette.clear-manual');
        Route::post('/roulette/settings', [RouletteAdminController::class, 'updateSettings'])->name('roulette.settings');
    });

    // Horse Racing Derby Live Management & Result Control
    Route::middleware('permission:game_horse_racing')->group(function () {
        Route::get('/horse-racing', [HorseRacingAdminController::class, 'index'])->name('horse-racing.index');
        Route::post('/horse-racing/set-winner', [HorseRacingAdminController::class, 'setManualWinner'])->name('horse-racing.set-winner');
        Route::post('/horse-racing/clear-manual', [HorseRacingAdminController::class, 'clearManualWinner'])->name('horse-racing.clear-manual');
        Route::post('/horse-racing/settings', [HorseRacingAdminController::class, 'updateSettings'])->name('horse-racing.settings');
    });
    Route::middleware('permission:winners_horse_racing')->group(function () {
        Route::get('/horse-racing/winners', [HorseRacingAdminController::class, 'winners'])->name('horse-racing.winners');
    });

    // Dragon Tiger Live Casino Management & Result Control
    Route::middleware('permission:game_dragon_tiger')->group(function () {
        Route::get('/dragon-tiger', [DragonTigerAdminController::class, 'index'])->name('dragon-tiger.index');
        Route::post('/dragon-tiger/set-winner', [DragonTigerAdminController::class, 'setManualWinner'])->name('dragon-tiger.set-winner');
        Route::post('/dragon-tiger/clear-manual', [DragonTigerAdminController::class, 'clearManualWinner'])->name('dragon-tiger.clear-manual');
        Route::post('/dragon-tiger/mode', [DragonTigerAdminController::class, 'setControlMode'])->name('dragon-tiger.mode');
        Route::post('/dragon-tiger/settings', [DragonTigerAdminController::class, 'updateSettings'])->name('dragon-tiger.settings');
    });
    Route::middleware('permission:winners_dragon_tiger')->group(function () {
        Route::get('/dragon-tiger/winners', [DragonTigerAdminController::class, 'winners'])->name('dragon-tiger.winners');
    });

    // Cricket Live Sportsbook & CricAPI Sync Controls
    Route::prefix('sports/cricket')->name('sports.cricket.')->group(function () {
        Route::post('/test-api', [CricketController::class, 'adminTestApi'])->name('test-api');
        Route::post('/sync', [CricketController::class, 'adminSyncApi'])->name('sync');
        Route::post('/settle', [CricketController::class, 'adminSettleMarket'])->name('settle');
    });
});

/*
|--------------------------------------------------------------------------
| Storage & Media Asset Stream Handlers (100% Guaranteed on all Live Servers & cPanel)
|--------------------------------------------------------------------------
*/
$streamMediaFile = function (string $path) {
    // Sanitize path against directory traversal
    $cleanPath = str_replace(['..', '\\'], ['', '/'], $path);
    $cleanPath = ltrim($cleanPath, '/');
    $baseName = basename($cleanPath);

    $candidates = [
        public_path('uploads/'.$cleanPath),
        public_path($cleanPath),
        public_path('uploads/proofs/'.$baseName),
        public_path('uploads/proofs/user_qr/'.$baseName),
        public_path('uploads/proofs/admin_proofs/'.$baseName),
        public_path('uploads/qr/'.$baseName),
        public_path('storage/'.$cleanPath),
        storage_path('app/public/'.$cleanPath),
        storage_path('app/public/proofs/'.$baseName),
        storage_path('app/public/proofs/user_qr/'.$baseName),
        storage_path('app/public/proofs/admin_proofs/'.$baseName),
        storage_path('app/public/qr/'.$baseName),
    ];

    foreach ($candidates as $candidate) {
        if (file_exists($candidate) && is_file($candidate)) {
            $ext = strtolower(pathinfo($candidate, PATHINFO_EXTENSION));
            $mime = match ($ext) {
                'png' => 'image/png',
                'jpg', 'jpeg' => 'image/jpeg',
                'webp' => 'image/webp',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'pdf' => 'application/pdf',
                default => @mime_content_type($candidate) ?: 'application/octet-stream',
            };

            return response()->file($candidate, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }
    }

    abort(404, 'File not found');
};

Route::get('/media-file/{path}', $streamMediaFile)->where('path', '.*')->name('media.stream');
Route::get('/storage/{path}', $streamMediaFile)->where('path', '.*')->name('storage.file');
