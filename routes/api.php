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
    Route::post('signup', 'AuthenticationController@signup')->name('signup');
    Route::post('login', 'AuthenticationController@login')->name('login');
    Route::post('sociallogin', 'AuthenticationController@social_login')->name('social_login');
    Route::post('updateagent', 'AuthenticationController@updateAgent')->name('updateAgent');
    Route::post('resetpassword', 'AuthenticationController@resetpassword')->name('resetpassword');
    Route::post('addreferral', 'AuthenticationController@update_referral')->name('update_referral');
    Route::post('changepassword', 'AuthenticationController@change_password')->name('change_password');
    Route::post('changepin', 'AuthenticationController@change_pin')->name('change_pin');

    Route::post('transactions', 'TransactionsController@getTrans')->name('getTrans');
    Route::post('gmtransactions', 'TransactionsController@getGmTrans')->name('getGmTrans');
    Route::post('portalstransactions', 'TransactionsController@getPortalTrans')->name('getPortalTrans');
    Route::post('getreferrals', 'TransactionsController@getReferrals')->name('getReferrals');
    Route::post('resultchecker', 'TransactionsController@insertResultchecker')->name('insertResultchecker');
    Route::post('rechargecard', 'TransactionsController@insertRechargecard')->name('insertRechargecard');
    Route::post('fundwallet', 'TransactionsController@fundWallet')->name('fundWallet');
    Route::post('freemoney', 'TransactionsController@insertFreemoney')->name('insertFreemoney');
    Route::post('verifytv', 'UltilityController@VerifyTV')->name('verifytv');
    Route::post('verifysmile', 'DataTransactionController@verifysmile')->name('verifysmile');

    Route::post('withdraw', 'UltilityController@withdraw')->name('verifysmile');

    Route::post('receivebtc', 'TransactionsController@btc4rmluno')->name('btc4rmluno');


    Route::post('paytv', 'ServeRequestController@paytv')->name('paytv')->middleware("server_log");
    Route::post('buyairtime', 'ServeRequestController@buyairtime')->name('buyairtime')->middleware("server_log");
    Route::post('buydata', 'ServeRequestController@buydata')->name('buydata')->middleware("server_log");
    Route::post('airtime2cash', 'UltilityController@mcd_a2ca2b')->name('mcd_a2ca2b');
    Route::post('log_mcdvoice', 'UltilityController@mcd_logvoice')->name('logvoice');

    Route::post('hook', 'UltilityController@hook')->name('hook');
    Route::get('ra/{id}', 'UltilityController@monnifyRA')->name('monnifyRA');
    Route::get('fra/{id}', 'UltilityController@fetchmonnifyRA')->name('fetchmonnifyRA');

    Route::post('sendemail', 'UltilityController@sendemail');
});

require __DIR__ . '/reseller.php';
require __DIR__ . '/v2.php';
require __DIR__ . '/hooks.php';
