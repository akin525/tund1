<?php

use App\Http\Controllers\Api\V2\AuthenticationController;
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

});
