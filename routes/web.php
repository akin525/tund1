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

use Illuminate\Support\Facades\Hash;

Auth::routes(['register' => false]);

Route::get('/', function () {
    return redirect('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', 'HomeController@index')->name('home');
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/ayomi', 'passwordhash@update');
//Route::post('/login', 'passwordhash@login');
    Route::get('/addpassword/{id}/{password}', function ($id, $password) {
        $u = \App\User::find($id);
        $u->password = Hash::make($password);
        $u->save();
        echo "success";
    });

    Route::get('/logout', function () {
        \Illuminate\Support\Facades\Auth::logout();
        return redirect('/login')->with('success', 'You have successfully logout');
    });
    Route::get('/users', 'UsersController@index')->name('users');
    Route::get('/agents', 'UsersController@agents')->name('agents');
    Route::get('/resellers', 'UsersController@resellers')->name('agents');
    Route::get('/pending_request', 'UsersController@pending')->name('pendingrequest');
    Route::post('/request_approve', 'UsersController@approve')->name('user approval');
    Route::get('/profile/{id}', 'UsersController@profile')->name('profile');
    Route::get('/wallet', 'WalletController@index')->name('wallet');
    Route::get('/transaction', 'TransactionController@index')->name('transaction');
    Route::post('/rechargecard', 'TransactionController@rechargecard')->name('rechargecard');
    Route::get('/rechargecards', 'TransactionController@rechargemanual')->name('manualrechargecard');
    Route::post('/monnify', 'TransactionController@monnify')->name('monnify');
//Route::get('/addfund', 'WalletController@addfund')->name('addfund');
    Route::view('/profile', 'email_agent');
    Route::view('/addfund', 'addfund');
    Route::view('/rechargecard', 'rechargecard');
    Route::post('/addfund', 'WalletController@addfund')->name('addfund');
    Route::view('/addtransaction', 'addtransaction');
    Route::post('/addtransaction', 'TransactionController@addtransaction')->name('addtransaction');
    Route::view('/adddatatransaction', 'addtransaction_data');
    Route::post('/adddatatransaction', 'TransactionController@addtransaction_data')->name('adddatatransaction');
    Route::view('/reversal', 'reversal');
    Route::post('/reversal-confirm', 'TransactionController@reversal_confirm')->name('reversal.confirm');
    Route::get('/reverse-transaction/{id}', 'TransactionController@reverse')->name('reverse');
    Route::view('/gnews', 'addgnews');
    Route::post('/gnews', 'UsersController@addgnews')->name('addgnews');
    Route::post('/user-sms', 'UsersController@sendsms')->name('user.sms');
    Route::post('/user-email', 'UsersController@sendemail')->name('user.email');
    Route::post('/user-pushnotif', 'UsersController@sendpushnotif')->name('user.pushnotif');
    Route::get('/agentpayment', 'UsersController@agent_list')->name('agent.payment.list');
    Route::post('/agentpayment-confirm', 'UsersController@agent_confirm')->name('agent.payment.confirmation');
    Route::post('/agentpayment', 'UsersController@agent_payment')->name('agent.payment');
    Route::get('/airtime2cash', 'TransactionController@airtime2cash')->name('transaction.airtime2cash');
    Route::post('/airtime2cash', 'TransactionController@airtime2cashpayment')->name('transaction.airtime2cash.payment');
});





