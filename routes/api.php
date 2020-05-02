<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('monnify', 'TransactionController@monnify')->name('monnify');


Route::group(['namespace' => 'Api'], function () {
    Route::post('paytv', 'ServeRequestController@paytv')->name('paytv')->middleware("server_log");
    Route::post('buyairtime', 'ServeRequestController@buyairtime')->name('buyairtime')->middleware("server_log");
    Route::post('log_mcdvoice', 'UltilityController@mcd_logvoice')->name('logvoice');
    Route::post('/hook', 'UltilityController@hook')->name('hook');
});
