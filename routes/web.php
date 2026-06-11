<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardRedirectController;

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminGameController;
use App\Http\Controllers\Admin\AdminKycController;
use App\Http\Controllers\Admin\AdminAgentController;
use App\Http\Controllers\Admin\AdminPlayerController;
use App\Http\Controllers\Admin\AdminMoneyRequestController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminMonitoringController;
use App\Http\Controllers\Admin\AdminCommissionWithdrawalController;
use App\Http\Controllers\Admin\AdminCommissionReportController;

use App\Http\Controllers\Agent\AgentDashboardController;
use App\Http\Controllers\Agent\AgentRequestController;
use App\Http\Controllers\Agent\AgentWalletController;
use App\Http\Controllers\Agent\AgentCommissionController;
use App\Http\Controllers\Agent\AgentPlayerController;

use App\Http\Controllers\Player\PlayerDashboardController;
use App\Http\Controllers\Player\PlayerGameController;
use App\Http\Controllers\Player\PlayerKycController;
use App\Http\Controllers\Player\PlayerWalletController;
use App\Http\Controllers\Player\PlayerAccountStatusController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardRedirectController::class)
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/agents', [AdminAgentController::class, 'index'])
            ->name('agents.index');

       Route::post('/agents/{agent}/activate', [AdminAgentController::class, 'activate'])
    ->name('agents.activate');

Route::post('/agents/{agent}/deactivate', [AdminAgentController::class, 'deactivate'])
    ->name('agents.deactivate');

Route::post('/agents/{agent}/password', [AdminAgentController::class, 'updatePassword'])
    ->name('agents.password.update');

        Route::get('/players', [AdminPlayerController::class, 'index'])
            ->name('players.index');

        Route::post('/players/{player}/activate', [AdminPlayerController::class, 'activate'])
            ->name('players.activate');
Route::post('/players/{player}/password', [AdminPlayerController::class, 'updatePassword'])
    ->name('players.password.update');
        Route::post('/players/{player}/deactivate', [AdminPlayerController::class, 'deactivate'])
            ->name('players.deactivate');

        Route::post('/players/{player}/appeal/approve', [AdminPlayerController::class, 'approveAppeal'])
            ->name('players.appeal.approve');

        Route::post('/players/{player}/appeal/reject', [AdminPlayerController::class, 'rejectAppeal'])
            ->name('players.appeal.reject');

        Route::get('/games', [AdminGameController::class, 'index'])
            ->name('games.index');

        Route::get('/games/live', [AdminGameController::class, 'liveData'])
            ->name('games.live');

        Route::post('/games', [AdminGameController::class, 'store'])
            ->name('games.store');

        Route::post('/games/{game}/start', [AdminGameController::class, 'start'])
            ->name('games.start');

        Route::post('/games/{game}/close', [AdminGameController::class, 'close'])
            ->name('games.close');

        Route::post('/games/{game}/end', [AdminGameController::class, 'end'])
            ->name('games.end');

        Route::post('/games/{game}/declare', [AdminGameController::class, 'declare'])
            ->name('games.declare');

        Route::get('/kyc', [AdminKycController::class, 'index'])
            ->name('kyc.index');

        Route::post('/kyc/{kyc}/approve', [AdminKycController::class, 'approve'])
            ->name('kyc.approve');

        Route::post('/kyc/{kyc}/reject', [AdminKycController::class, 'reject'])
            ->name('kyc.reject');

        Route::get('/money-requests', [AdminMoneyRequestController::class, 'index'])
            ->name('money-requests.index');

        Route::post('/money-requests/{moneyRequest}/approve', [AdminMoneyRequestController::class, 'approveMoney'])
            ->name('money-requests.approve');

        Route::post('/money-requests/{moneyRequest}/reject', [AdminMoneyRequestController::class, 'rejectMoney'])
            ->name('money-requests.reject');

        Route::post('/withdrawals/{withdrawal}/approve', [AdminMoneyRequestController::class, 'approveWithdrawal'])
            ->name('withdrawals.approve');

        Route::post('/withdrawals/{withdrawal}/reject', [AdminMoneyRequestController::class, 'rejectWithdrawal'])
            ->name('withdrawals.reject');

        Route::get('/commission-withdrawals', [AdminCommissionWithdrawalController::class, 'index'])
            ->name('commission-withdrawals.index');

        Route::post('/commission-withdrawals/{commissionWithdrawal}/approve', [AdminCommissionWithdrawalController::class, 'approve'])
            ->name('commission-withdrawals.approve');

        Route::post('/commission-withdrawals/{commissionWithdrawal}/reject', [AdminCommissionWithdrawalController::class, 'reject'])
            ->name('commission-withdrawals.reject');

        Route::get('/commission-reports', [AdminCommissionReportController::class, 'index'])
            ->name('commission-reports.index');

        Route::get('/reports/games', [AdminReportController::class, 'games'])
            ->name('reports.games');

        Route::get('/reports/games/export', [AdminReportController::class, 'exportGames'])
            ->name('reports.games.export');

        Route::get('/reports/wallet', [AdminReportController::class, 'wallet'])
            ->name('reports.wallet');

        Route::get('/reports/wallet/export', [AdminReportController::class, 'exportWallet'])
            ->name('reports.wallet.export');

        Route::get('/monitoring', [AdminMonitoringController::class, 'overview'])
            ->name('monitoring.overview');

        Route::get('/activity-logs', [AdminMonitoringController::class, 'activityLogs'])
            ->name('activity-logs.index');

        Route::get('/agent-hierarchy', [AdminMonitoringController::class, 'agentHierarchy'])
            ->name('agent-hierarchy.index');

        Route::get('/agent-reports', [AdminMonitoringController::class, 'agentReports'])
            ->name('agent-reports.index');

        Route::get('/agent-reports/export', [AdminMonitoringController::class, 'exportAgentReports'])
            ->name('agent-reports.export');
    });

    /*
    |--------------------------------------------------------------------------
    | Agent Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('agent')->name('agent.')->group(function () {
        Route::get('/dashboard', [AgentDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/wallet', [AgentWalletController::class, 'index'])
            ->name('wallet.index');

        Route::post('/wallet/request-money', [AgentWalletController::class, 'requestMoney'])
            ->name('wallet.request-money');

        Route::post('/wallet/withdraw', [AgentWalletController::class, 'withdraw'])
            ->name('wallet.withdraw');

        Route::get('/requests', [AgentRequestController::class, 'index'])
            ->name('requests.index');

        Route::post('/requests/money/{moneyRequest}/approve', [AgentRequestController::class, 'approveMoney'])
            ->name('requests.money.approve');

        Route::post('/requests/money/{moneyRequest}/reject', [AgentRequestController::class, 'rejectMoney'])
            ->name('requests.money.reject');

        Route::post('/requests/withdrawals/{withdrawal}/approve', [AgentRequestController::class, 'approveWithdrawal'])
            ->name('requests.withdrawals.approve');

        Route::post('/requests/withdrawals/{withdrawal}/reject', [AgentRequestController::class, 'rejectWithdrawal'])
            ->name('requests.withdrawals.reject');

        Route::get('/commissions', [AgentCommissionController::class, 'index'])
            ->name('commissions.index');

        Route::post('/commissions/convert-to-load', [AgentCommissionController::class, 'convertToLoad'])
            ->name('commissions.convert-to-load');

        Route::post('/commissions/withdraw-cash', [AgentCommissionController::class, 'withdrawCash'])
            ->name('commissions.withdraw-cash');

        Route::get('/players', [AgentPlayerController::class, 'index'])
            ->name('players.index');
    });

    /*
    |--------------------------------------------------------------------------
    | Player Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('player')->name('player.')->group(function () {
        Route::get('/appeal', [PlayerAccountStatusController::class, 'inactive'])
            ->name('appeal.index');

        Route::post('/appeal', [PlayerAccountStatusController::class, 'submitAppeal'])
            ->name('appeal.store');

        Route::get('/account/inactive', [PlayerAccountStatusController::class, 'inactive'])
            ->name('account.inactive');

        Route::post('/account/appeal', [PlayerAccountStatusController::class, 'submitAppeal'])
            ->name('account.appeal');

        Route::get('/kyc', [PlayerKycController::class, 'index'])
            ->name('kyc.index');

        Route::post('/kyc', [PlayerKycController::class, 'store'])
            ->name('kyc.store');

        Route::middleware(['kyc.approved'])->group(function () {
            Route::get('/dashboard', [PlayerDashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('/game', [PlayerGameController::class, 'index'])
                ->name('game.index');

            Route::get('/game/live', [PlayerGameController::class, 'liveData'])
                ->name('game.live');

            Route::post('/game/bet', [PlayerGameController::class, 'bet'])
                ->name('game.bet');

            Route::get('/wallet', [PlayerWalletController::class, 'index'])
                ->name('wallet.index');

            Route::post('/wallet/request-money', [PlayerWalletController::class, 'requestMoney'])
                ->name('wallet.request-money');

            Route::post('/wallet/withdraw', [PlayerWalletController::class, 'withdraw'])
                ->name('wallet.withdraw');
        });
    });
});

if (file_exists(__DIR__ . '/auth.php')) {
    require __DIR__ . '/auth.php';
}