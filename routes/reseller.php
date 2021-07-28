<?php

use App\Http\Controllers\Reseller\SwitchController;
use Illuminate\Http\Request;
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

Route::prefix('reseller')->group(function () {
    Route::post('pay', [SwitchController::class, 'authenticate'])->name('reseller');
    Route::post('validate', [SwitchController::class, 'validateService']);
    Route::post('list', [SwitchController::class, 'listService']);
});
