<?php

use Illuminate\Support\Facades\Route;
use Modules\RewardAndPromotions\Http\Controllers\LoyaltyPointsController;

Route::group(['as' => 'reward-promotions.', 'prefix' => 'reward-promotions'], function () {

    Route::get('/points', [LoyaltyPointsController::class, 'index']);
});

Route::get('/rewards/point-leaders/{location}', [LoyaltyPointsController::class, 'index'])
    ->name('rewards.point-leaders');

Route::get('/blowwfg', function () {
    dd('ffff');
});
