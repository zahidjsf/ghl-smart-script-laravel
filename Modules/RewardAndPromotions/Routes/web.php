<?php

use Illuminate\Support\Facades\Route;
use Modules\RewardAndPromotions\Http\Controllers\DashboardController;
use Modules\RewardAndPromotions\Http\Controllers\LeaderboardController;
use Modules\RewardAndPromotions\Http\Controllers\LoginController;
use Modules\RewardAndPromotions\Http\Controllers\LoyaltyPointsController;

Route::group(['as' => 'reward-promotions.', 'prefix' => 'reward-promotions'], function () {

    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/loginpost', [LoginController::class, 'loginpost'])->name('loginpost');

    Route::middleware('location')->group(function () {
        Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/referrals', [LeaderboardController::class, 'index'])->name('referrals');
        Route::get('/contact-points', [LoyaltyPointsController::class, 'index'])->name('contact_points');
    });

});

Route::get('/rewards/point-leaders/{location}', [LoyaltyPointsController::class, 'index'])->name('rewards.point-leaders');
