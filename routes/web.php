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

use App\Http\Controllers\Agent\AgentDashboardController;
use App\Http\Controllers\Agent\AgentRequestController;
use App\Http\Controllers\Agent\AgentWalletController;

use App\Http\Controllers\Player\PlayerDashboardController;
use App\Http\Controllers\Player\PlayerGameController;
use App\Http\Controllers\Player\PlayerKycController;
use App\Http\Controllers\Player\PlayerWalletController;

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

        Route::get('/players', [AdminPlayerController::class, 'index'])
            ->name('players.index');

        Route::post('/players/{player}/activate', [AdminPlayerController::class, 'activate'])
            ->name('players.activate');

        Route::post('/players/{player}/deactivate', [AdminPlayerController::class, 'deactivate'])
            ->name('players.deactivate');

        /*
        |--------------------------------------------------------------------------
        | Admin Game Control
        |--------------------------------------------------------------------------
        */

        Route::get('/games', [AdminGameController::class, 'index'])
            ->name('games.index');

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

        /*
        |--------------------------------------------------------------------------
        | Admin KYC
        |--------------------------------------------------------------------------
        */

        Route::get('/kyc', [AdminKycController::class, 'index'])
            ->name('kyc.index');

        Route::post('/kyc/{kyc}/approve', [AdminKycController::class, 'approve'])
            ->name('kyc.approve');

        Route::post('/kyc/{kyc}/reject', [AdminKycController::class, 'reject'])
            ->name('kyc.reject');

        /*
        |--------------------------------------------------------------------------
        | Admin Money Requests / Agent Wallet Requests
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Admin Reports
        |--------------------------------------------------------------------------
        */

        Route::get('/reports/games', [AdminReportController::class, 'games'])
            ->name('reports.games');

        Route::get('/reports/games/export', [AdminReportController::class, 'exportGames'])
            ->name('reports.games.export');

        Route::get('/reports/wallet', [AdminReportController::class, 'wallet'])
            ->name('reports.wallet');

        Route::get('/reports/wallet/export', [AdminReportController::class, 'exportWallet'])
            ->name('reports.wallet.export');
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
    });

    /*
    |--------------------------------------------------------------------------
    | Player Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('player')->name('player.')->group(function () {
        Route::get('/dashboard', [PlayerDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/game', [PlayerGameController::class, 'index'])
            ->name('game.index');

        Route::post('/game/bet', [PlayerGameController::class, 'bet'])
            ->name('game.bet');

        Route::get('/kyc', [PlayerKycController::class, 'index'])
            ->name('kyc.index');

        Route::post('/kyc', [PlayerKycController::class, 'store'])
            ->name('kyc.store');

        Route::get('/wallet', [PlayerWalletController::class, 'index'])
            ->name('wallet.index');

        Route::post('/wallet/request-money', [PlayerWalletController::class, 'requestMoney'])
            ->name('wallet.request-money');

        Route::post('/wallet/withdraw', [PlayerWalletController::class, 'withdraw'])
            ->name('wallet.withdraw');
    });
});