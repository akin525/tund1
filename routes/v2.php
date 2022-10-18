<?php

use App\Http\Controllers\Api\OthersController;
use App\Http\Controllers\Api\V2\AuthenticationController;
use App\Http\Controllers\Api\V2\GiveAwayController;
use App\Http\Controllers\Api\V2\ListController;
use App\Http\Controllers\Api\V2\OtherController;
use App\Http\Controllers\Api\V2\PayController;
use App\Http\Controllers\Api\V2\UserController;
use App\Http\Controllers\Api\V2\ValidationController;
use App\Http\Controllers\Api\V2\WalletTransferController;
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
        Route::post('addreferral', [UserController::class, 'add_referral']);

        Route::get('transactions', [UserController::class, 'transactions']);
        Route::get('gmtransactions', [OtherController::class, 'getGmTrans']);

        Route::post('changepassword', [UserController::class, 'change_password']);
        Route::get('paymentcheckout', [OtherController::class, 'paymentcheckout']);

        Route::get('airtime', [ListController::class, 'airtime']);
        Route::get('airtime/countries', [ListController::class, 'airtimeInt']);
        Route::get('data/{network}', [ListController::class, 'data']);
        Route::get('tv/{network}', [ListController::class, 'cabletv']);
        Route::get('jamb', [ListController::class, 'jamb']);

        Route::post('airtime', [PayController::class, 'buyairtime']);
        Route::post('data', [PayController::class, 'buydata']);
        Route::post('tv', [PayController::class, 'buytv']);
        Route::post('electricity', [PayController::class, 'buyelectricity']);
        Route::post('betting', [PayController::class, 'buybetting']);
        Route::post('jamb', [PayController::class, 'buyJamb']);
        Route::post('bizverification', [PayController::class, 'bizverification']);

        Route::post('bulkairtime', [UserController::class, 'bulkAirtime']);

        Route::post('airtimeconverter', [PayController::class, 'a2ca2b']);
        Route::post('resultchecker', [PayController::class, 'resultchecker']);

        Route::post('epins', [OtherController::class, 'insertRechargecard']);

        Route::get('profile', [UserController::class, 'profile']);
        Route::get('agentstatus', [UserController::class, 'agentStatus']);
        Route::post('agent', [UserController::class, 'requestAgent']);
        Route::post('requestReseller', [UserController::class, 'requestReseller']);
        Route::get('request-agentdoc', [UserController::class, 'requestAgentDocument']);
        Route::post('agentdocument', [UserController::class, 'agentDocumentation']);
        Route::post('uploaddp', [UserController::class, 'uploaddp']);
        Route::get('vaccounts', [UserController::class, 'vaccounts']);

        Route::post('user-upgrade', [UserController::class, 'referral_upgrade']);

        Route::get('get-other-service', [OtherController::class, 'getOtherService']);

        Route::get('banklist', [OtherController::class, 'banklist']);
        Route::post('verifyBank', [OtherController::class, 'verifyBank']);
        Route::post('withdrawfund', [OtherController::class, 'withdraw']);


        Route::post('fundwallet', [OtherController::class, 'fundwallet']);

        Route::get('freemoney', [UserController::class, 'freemoney']);

        Route::get('leaderboard', [OtherController::class, 'getPoints']);

        Route::post('get-equivalent', [OtherController::class, 'getEqv']);
        Route::post('payment/flutterwave', [OtherController::class, 'flutterwavePayment']);

        Route::post('create-giveaway', [GiveAwayController::class, 'create']);
        Route::get('fetch-giveaways', [GiveAwayController::class, 'fetchs']);
        Route::get('fetch-giveaway/{id}', [GiveAwayController::class, 'fetch']);
        Route::post('request-giveaway', [GiveAwayController::class, 'request']);
        Route::get('sliders', [OthersController::class, 'sliders']);

        Route::post('username/validate', [WalletTransferController::class, 'validateUsername']);
        Route::post('w2w/transfer', [WalletTransferController::class, 'transfer']);

        Route::get('apikey/regenerate', [UserController::class, 'requestAPIkey']);
        Route::get('getfaqs', [OtherController::class, 'getFAQs']);

        Route::get('cg-wallets', [UserController::class, 'cgWallets']);
        Route::get('cg-bundles', [UserController::class, 'cgBundles']);
        Route::post('cg-bundles-buy', [UserController::class, 'cgBundleBuy']);
    });

});
