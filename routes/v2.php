<?php

use App\Http\Controllers\Api\V2\AuthenticationController;
use App\Http\Controllers\Api\V2\ListController;
use App\Http\Controllers\Api\V2\OtherController;
use App\Http\Controllers\Api\V2\UserController;
use App\Http\Controllers\Api\V2\ValidationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v2')->middleware("version")->group(function () {
    Route::post('login', [AuthenticationController::class, 'login']);
    Route::post('newdevice', [AuthenticationController::class, 'newdeviceLogin']);
    Route::post('sociallogin', [AuthenticationController::class, 'sociallogin']);
    Route::post('resetpassword', [AuthenticationController::class, 'resetpassword']);
    Route::post('signup', [AuthenticationController::class, 'signup']);

    Route::post('validate', [ValidationController::class, 'index']);

    Route::get('referralPlans', [OtherController::class, 'referralPlans']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('biometriclogin', [AuthenticationController::class, 'biometricLogin']);
        Route::get('dashboard', [UserController::class, 'dashboard']);
        Route::post('changepin', [UserController::class, 'change_pin']);
        Route::get('referrals', [UserController::class, 'referrals']);
        Route::get('transactions', [UserController::class, 'transactions']);
        Route::post('changepassword', [UserController::class, 'change_password']);
        Route::get('paymentcheckout', [OtherController::class, 'paymentcheckout']);

        Route::get('airtime', [ListController::class, 'airtime']);
        Route::get('data/{network}', [ListController::class, 'data']);
        Route::get('tv/{network}', [ListController::class, 'cabletv']);
    });

});
