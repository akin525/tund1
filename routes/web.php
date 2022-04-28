<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\WalletController;
use App\Jobs\Airtime2CashNotificationJob;
use App\Jobs\NewAccountGiveaway;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::get('/', function () {
    return redirect('login');
});

Route::get('/testgi', function () {
    NewAccountGiveaway::dispatchNow('samji');
});

Route::get('/a2cn', function () {
    Airtime2CashNotificationJob::dispatch("mcd_ewquweqd")->delay(now()->addSeconds());
});


Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [HomeController::class, 'index'])->name('home');
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/ayomi', 'passwordhash@update');
//Route::post('/login', 'passwordhash@login');
//    Route::get('/addpassword/{id}/{password}', function ($id, $password) {
//        $u = \App\User::find($id);
//        $u->password = Hash::make($password);
//        $u->save();
//        echo "success";
//    });

    Route::get('/logout', function () {
        Auth::logout();
        return redirect('/login')->with('success', 'You have successfully logout');
    });
    Route::get('/users', 'UsersController@index')->name('users');
    Route::get('/agents', 'UsersController@agents')->name('agents');
    Route::get('/resellers', 'UsersController@resellers')->name('agents');
    Route::get('/gmblocked', 'UsersController@gmblocked')->name('gmblocked');
    Route::get('/dormantusers', 'UsersController@dormant')->name('dormant');
    Route::get('/loginattempts', 'UsersController@loginattempt')->name('loginattempt');
    Route::get('/pending_request', 'UsersController@pending')->name('pendingrequest');
    Route::post('/request_approve', 'UsersController@approve')->name('user approval');
    Route::get('/profile/{id}', 'UsersController@profile')->name('profile');
    Route::get('/wallet', 'WalletController@index')->name('wallet');

    Route::get('/virtual-accounts', [UsersController::class, 'vaccounts'])->name('virtual-accounts');
    Route::get('/payment-links', [UsersController::class, 'paymentLinks'])->name('payment-links');

    Route::get('/withdrawal', 'WalletController@withdrawal_list')->name('withdrawal_list');
    Route::post('/withdrawal', 'WalletController@withdrawal_submit')->name('withdrawal_submit');
    Route::post('/reject-withdrawal', [WalletController::class, 'withdrawal_reject'])->name('withdrawal_reject');

    Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction');
    Route::get('/transaction_server8', [TransactionController::class, 'server8'])->name('transaction8');

    Route::get('/transactions-pending', [TransactionController::class, 'pending'])->name('trans_pending');
    Route::post('/trans-resubmit', [TransactionController::class, 'trans_resubmit'])->name('trans_resubmit');

    Route::get('/generalmarket', 'TransactionController@gmhistory')->name('generalmarket');
    Route::get('/plcharges', 'TransactionController@plcharges')->name('plcharges');
    Route::post('/rechargecard', 'TransactionController@rechargecard')->name('rechargecard');
    Route::get('/rechargecards', 'TransactionController@rechargemanual')->name('manualrechargecard');
    Route::post('/monnify', 'TransactionController@monnify')->name('monnify');
//Route::get('/addfund', 'WalletController@addfund')->name('addfund');
    Route::view('/profile', 'email_agent');
    Route::view('/cc', 'mail.passwordreset');
    Route::view('/finduser', 'find_user');
    Route::POST('/finduser', 'UsersController@finduser')->name('finduser');

    Route::view('/findtransaction', 'find_transaction')->name('findtransaction');
    Route::post('/findtransaction', [TransactionController::class, 'finduser'])->name('findtransactionsubmit');

    Route::view('/gnews', 'addgnews');
    Route::post('/gnews', 'UsersController@addgnews')->name('addgnews');
    Route::post('/user-sms', 'UsersController@sendsms')->name('user.sms');
    Route::post('/user-email', 'UsersController@sendemail')->name('user.email');
    Route::post('/user-pushnotif', 'UsersController@sendpushnotif')->name('user.pushnotif');
    Route::get('/agentpayment', 'UsersController@agent_list')->name('agent.payment.list');
    Route::post('/agentpayment-confirm', 'UsersController@agent_confirm')->name('agent.payment.confirmation');
    Route::post('/agentpayment', 'UsersController@agent_payment')->name('agent.payment');

    Route::view('/verification_server10', 'verification_s10')->name('verification_s10');
    Route::view('/verification_server6', 'verification_s6')->name('verification_s6');
    Route::view('/verification_server5', 'verification_s5');
    Route::view('/verification_server4', 'verification_s4');
    Route::view('/verification_server3', 'verification_s3');
    Route::view('/verification_server2', 'verification_s2');
    Route::view('/verification_server1b', 'verification_s1b');
    Route::view('/verification_server1', 'verification_s1');
    Route::view('/verification_server1dt', 'verification_s1dt');
    Route::post('/verification_server3', 'VerificationController@server3')->name('verification_server3');
    Route::post('/verification_server2', 'VerificationController@server2')->name('verification_server2');
    Route::post('/verification_server1b', 'VerificationController@server1b')->name('verification_server1b');
    Route::post('/verification_server1', 'VerificationController@server1')->name('verification_server1');
    Route::post('/verification_server1dt', 'VerificationController@server1dt')->name('verification_server1dt');
    Route::post('/verification_server4', 'VerificationController@server4')->name('verification_server4');
    Route::post('/verification_server5', 'VerificationController@server5')->name('verification_server5');
    Route::post('/verification_server6', 'VerificationController@server6')->name('verification_server6');
    Route::post('/verification_server10', [VerificationController::class, 'server10'])->name('verification_server10');

    Route::middleware(['authCheck'])->group(function () {
        Route::POST('/referral_upgrade', 'UsersController@referral_upgrade')->name('referral.upgrade');
        Route::view('/referral_upgrade', 'referral_upgrade');

        Route::get('/airtime2cash', [TransactionController::class, 'airtime2cash'])->name('transaction.airtime2cash');
        Route::post('/airtime2cash', 'TransactionController@airtime2cashpayment')->name('transaction.airtime2cash.payment');

        Route::view('/addfund', 'addfund');
        Route::view('/rechargecard', 'rechargecard');
        Route::post('/addfund', 'WalletController@addfund')->name('addfund')->middleware('authCheck');
        Route::view('/addtransaction', 'addtransaction');
        Route::post('/addtransaction', 'TransactionController@addtransaction')->name('addtransaction');
        Route::view('/adddatatransaction', 'addtransaction_data');
        Route::post('/adddatatransaction', 'TransactionController@addtransaction_data')->name('adddatatransaction');
        Route::view('/reversal', 'reversal')->name('reversal');
        Route::post('/reversal-confirm', [TransactionController::class, 'reversal_confirm'])->name('reversal.confirm');
        Route::get('/reverse-transaction/{id}', 'TransactionController@reverse')->name('reverse');
        Route::any('/report_pnl', [ReportsController::class, 'pnl'])->name('report_pnl');
        Route::get('/cryptorequest', 'TransactionController@cryptos')->name('cryptos');
    });

});

require __DIR__ . '/storages.php';





