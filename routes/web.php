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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/ayomi', 'passwordhash@update');
Route::get('/users', 'UsersController@index')->name('users');
Route::get('/agents', 'UsersController@agents')->name('agents');
Route::get('/resellers', 'UsersController@resellers')->name('agents');
Route::get('/pending_request', 'UsersController@pending')->name('pendingrequest');
Route::post('/request_approve', 'UsersController@approve')->name('user approval');
Route::get('/profile/{id}', 'UsersController@profile')->name('profile');
Route::get('/wallet', 'WalletController@index')->name('wallet');
Route::get('/transaction', 'TransactionController@index')->name('transaction');
Route::get('/rechargecard', 'TransactionController@rechargecard')->name('rechargecard');
Route::get('/monnify', 'TransactionController@monnify')->name('monnify');
//Route::get('/addfund', 'WalletController@addfund')->name('addfund');
//Route::view('/profile', 'profile');
Route::view('/addfund', 'addfund');
Route::post('/addfund', 'WalletController@addfund')->name('addfund');

