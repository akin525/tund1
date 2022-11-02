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

use App\Http\Controllers\CGBundleController;
use App\Http\Controllers\FAQsController;
use App\Http\Controllers\GatewayControl;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\Reseller\BlockReseller;
use App\Http\Controllers\ResellerServiceController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\SliderController;
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
//    return view('welcome');
    return redirect()->route('login');
})->name('welcome');


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
    Route::get('/resellers', 'UsersController@resellers')->name('resellers');

    Route::get('/regenerateKey/{id}', [UsersController::class, 'regenerateKey'])->name('regenerateKey');

    Route::get('/gmblocked', 'UsersController@gmblocked')->name('gmblocked');
    Route::get('/dormantusers', 'UsersController@dormant')->name('dormant');
    Route::get('/loginattempts', 'UsersController@loginattempt')->name('loginattempt');
    Route::get('/pending_request', 'UsersController@pending')->name('pendingrequest');
    Route::post('/request_approve', 'UsersController@approve')->name('user approval');
    Route::get('/profile/{id}', 'UsersController@profile')->name('profile');
    Route::post('/update-profile', [UsersController::class, 'updateProfile'])->name('updateProfile');
    Route::post('/user-password-reset', [UsersController::class, 'passwordReset'])->name('userPasswordReset');
    Route::get('/admin-password-reset/{id}', [UsersController::class, 'passwordResetAdmin'])->name('adminPasswordReset');
    Route::get('/admin-bann-user/{id}', [UsersController::class, 'bannUnbann'])->name('adminBannUnbann');
    Route::get('/wallet', 'WalletController@index')->name('wallet');

    Route::get('/virtual-accounts', [UsersController::class, 'vaccounts'])->name('virtual-accounts');
    Route::get('/block/{id}', [BlockReseller::class, 'updatereseller'])->name('block');
    Route::get('/apikey/{id}', [BlockReseller::class, 'apireseller'])->name('apikey');
    Route::get('/payment-links', [UsersController::class, 'paymentLinks'])->name('payment-links');
    Route::get('/seller', [BlockReseller::class, 'listreseller'])->name('seller');

    Route::get('/withdrawal', 'WalletController@withdrawal_list')->name('withdrawal_list');
    Route::post('/withdrawal', 'WalletController@withdrawal_submit')->name('withdrawal_submit');
    Route::post('/reject-withdrawal', [WalletController::class, 'withdrawal_reject'])->name('withdrawal_reject');

    Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction');
    Route::get('/transaction_server8', [TransactionController::class, 'server8'])->name('transaction8');

    Route::get('/transactions-pending', [TransactionController::class, 'pending'])->name('trans_pending');
    Route::post('/trans-resubmit', [TransactionController::class, 'trans_resubmit'])->name('trans_resubmit');
    Route::get('/trans_delivered/{id}', [TransactionController::class, 'trans_delivered'])->name('trans_delivered');

    Route::get('/payment-gateway', [GatewayControl::class, 'gateway'])->name('paymentgateway');
    Route::get('/editpayment/{id}', [GatewayControl::class, 'editgateway'])->name('paymentgateway_edit');
    Route::post('/payment-gateway', [GatewayControl::class, 'updategateway'])->name('paymentgateway_update');


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
        Route::get('/airtime2cash-settings', [TransactionController::class, 'airtime2cashSettings'])->name('transaction.airtime2cashSettings');
        Route::get('/airtime2cash-settings-edit/{id}', [TransactionController::class, 'airtime2cashSettingsEdit'])->name('transaction.airtime2cashSettings.edit');
        Route::post('/airtime2cash-settings-modify', [TransactionController::class, 'airtime2cashSettingsModify'])->name('transaction.airtime2cashSettings.modify');
        Route::get('/airtime2cash-settings-ed/{id}', [TransactionController::class, 'airtime2cashSettingsED'])->name('transaction.airtime2cashSettings.ed');

        Route::get('/otherservices', [ServerController::class, 'others'])->name('otherservices');
        Route::get('/otherservices_add', [ServerController::class, 'others_add'])->name('otherservices_add');
        Route::post('/otherservices_add', [ServerController::class, 'others_addPost'])->name('otherservices_add');
        Route::get('/otherservices/{id}', [ServerController::class, 'othersedit'])->name('otherservicesEdit');
        Route::get('/otherservices-delete/{id}', [ServerController::class, 'Servicedestroy'])->name('otherservicesDelete');
        Route::post('/otherservices-update', [ServerController::class, 'othersUpdate'])->name('otherservicesUpdate');

        Route::get('/datalist/{network}', [ServerController::class, 'dataserve2'])->name('dataplans');
        Route::get('/datacontrol/{id}', [ServerController::class, 'dataserveedit'])->name('datacontrolEdit');
        Route::get('/datacontrol-multiple/{network}/{type}/{status}', [ServerController::class, 'dataserveMultipleedit'])->name('dataserveMultipleedit');
        Route::get('/dataserveED/{id}', [ServerController::class, 'dataserveED'])->name('dataserveED');
        Route::post('/datacontrol', [ServerController::class, 'dataserveUpdate'])->name('datacontrolUpdate');
        Route::view('/datanew', 'datacontrol_new')->name('datanew');
        Route::post('/datanew', [ServerController::class, 'datanew'])->name('datanew');

        Route::get('/airtimecontrol', [ServerController::class, 'airtime'])->name('airtimecontrol');
        Route::get('/airtimecontrol/{id}', [ServerController::class, 'airtimeEdit'])->name('airtimecontrolEdit');
        Route::get('/airtimecontrolED/{id}', [ServerController::class, 'airtimecontrolED'])->name('airtimecontrolED');
        Route::post('/airtimecontrol', [ServerController::class, 'airtimeUpdate'])->name('airtimecontrolUpdate');

        Route::get('/tvcontrol', [ServerController::class, 'tvserver'])->name('tvcontrol');
        Route::get('/tvcontrol/{id}', [ServerController::class, 'tvEdit'])->name('tvcontrolEdit');
        Route::get('/tvcontrolED/{id}', [ServerController::class, 'tvcontrolED'])->name('tvcontrolED');
        Route::post('/tvcontrol', [ServerController::class, 'tvUpdate'])->name('tvcontrolUpdate');

        Route::get('/electricitycontrol', [ServerController::class, 'electricityserver'])->name('electricitycontrol');
        Route::get('/electricitycontrol/{id}', [ServerController::class, 'electricityEdit'])->name('electricitycontrolEdit');
        Route::get('/electricitycontrolED/{id}', [ServerController::class, 'electricityED'])->name('electricitycontrolED');
        Route::post('/electricitycontrol', [ServerController::class, 'electricityUpdate'])->name('electricitycontrolUpdate');

        Route::prefix('reseller')->name('reseller.')->group(function () {
            Route::get('/datacontrol', [ResellerServiceController::class, 'dataserve2'])->name('dataplans');
            Route::get('/datacontrol/{id}', [ResellerServiceController::class, 'dataserveedit'])->name('datacontrolEdit');
            Route::get('/datacontrolED/{id}', [ResellerServiceController::class, 'datacontrolED'])->name('datacontrolED');
            Route::post('/datacontrol', [ResellerServiceController::class, 'dataserveUpdate'])->name('datacontrolUpdate');

            Route::get('/airtimecontrol', [ResellerServiceController::class, 'airtime'])->name('airtimecontrol');
            Route::get('/airtimecontrol/{id}', [ResellerServiceController::class, 'airtimeEdit'])->name('airtimecontrolEdit');
            Route::get('/airtimecontrolED/{id}', [ResellerServiceController::class, 'airtimecontrolED'])->name('airtimecontrolED');
            Route::post('/airtimecontrol', [ResellerServiceController::class, 'airtimeUpdate'])->name('airtimecontrolUpdate');

            Route::get('/tvcontrol', [ResellerServiceController::class, 'tvserver'])->name('tvcontrol');
            Route::get('/tvcontrol/{id}', [ResellerServiceController::class, 'tvEdit'])->name('tvcontrolEdit');
            Route::get('/tvcontrolED/{id}', [ResellerServiceController::class, 'tvcontrolED'])->name('tvcontrolED');
            Route::post('/tvcontrol', [ResellerServiceController::class, 'tvUpdate'])->name('tvcontrolUpdate');

            Route::get('/electricitycontrol', [ResellerServiceController::class, 'electricityserver'])->name('electricitycontrol');
            Route::get('/electricitycontrol/{id}', [ResellerServiceController::class, 'electricityEdit'])->name('electricitycontrolEdit');
            Route::post('/electricitycontrol', [ResellerServiceController::class, 'electricityUpdate'])->name('electricitycontrolUpdate');
        });

        Route::get('sliders', [SliderController::class, 'index'])->name('sliders.index');
        Route::get('addsliders', [SliderController::class, 'create'])->name('sliders.create');
        Route::post('addsliders', [SliderController::class, 'store'])->name('sliders.store');
        Route::get('modify-slider/{id}', [SliderController::class, 'update'])->name('sliders.update');
        Route::get('remove-slider/{id}', [SliderController::class, 'destroy'])->name('sliders.delete');

        Route::get('cg-bundle', [CGBundleController::class, 'index'])->name('cgbundle.index');
        Route::post('cg-bundle', [CGBundleController::class, 'create'])->name('cgbundle.create');
        Route::get('cg-bundle-list', [CGBundleController::class, 'lists'])->name('cgbundle.list');
        Route::get('cg-transactions-list', [CGBundleController::class, 'cgtrans'])->name('cgbundle.trans');
        Route::get('cg-bundle-modify/{id}', [CGBundleController::class, 'modify'])->name('cgbundle.modify');
        Route::get('cg-bundle-apply-credit/{id}', [CGBundleController::class, 'apply_credit'])->name('cgbundle.apply_credit');

        Route::get('cg-bundle-apply', [CGBundleController::class, 'applyView'])->name('cgbundle.apply');
        Route::post('cg-bundle-apply', [CGBundleController::class, 'apply'])->name('cgbundle.apply');

        Route::get('faqs', [FAQsController::class, 'index'])->name('faqs.index');
        Route::post('faqs', [FAQsController::class, 'store'])->name('faqs.store');
        Route::view('faq/create', 'faq_add')->name('faqs.create');
        Route::get('edit-faq/{id}', [FAQsController::class, 'edit'])->name('faqs.edit');
        Route::post('update-faq', [FAQsController::class, 'update'])->name('faqs.update');
        Route::get('modify-faq/{id}', [FAQsController::class, 'modify'])->name('faqs.modify');
        Route::get('remove-faq/{id}', [FAQsController::class, 'destroy'])->name('faqs.delete');

        Route::get('allsettings', [HomeController::class, 'allsettings'])->name('allsettings');
        Route::get('allsettings-edit/{id}', [HomeController::class, 'allsettingsEdit'])->name('allsettingsEdit');
        Route::post('allsettings-update', [HomeController::class, 'allsettingsUpdate'])->name('allsettingsUpdate');

        Route::get('/role', [ServerController::class, 'userole'])->name('role');
        Route::post('/updaterole', [ServerController::class, 'updateuserole'])->name('updaterole');
        Route::post('/updateLevel', [UsersController::class, 'updateLevel'])->name('updateLevel');
        Route::post('/datacontrol1', [ServerController::class, 'updatedataserve'])->name('datacontrol1');
        Route::post('/airtime2cash', 'TransactionController@airtime2cashpayment')->name('transaction.airtime2cash.payment');

        Route::view('/addfund', 'addfund')->name("addfund");
        Route::post('/addfund', 'WalletController@addfund')->name('addfund')->middleware('authCheck');
        Route::view('/servercontrol', 'servercontrol');
        Route::view('/rechargecard', 'rechargecard');
        Route::view('/addtransaction', 'addtransaction');
        Route::post('/addtransaction', 'TransactionController@addtransaction')->name('addtransaction');
        Route::view('/adddatatransaction', 'addtransaction_data');
        Route::post('/adddatatransaction', 'TransactionController@addtransaction_data')->name('adddatatransaction');
        Route::view('/reversal', 'reversal')->name('reversal');
        Route::post('/reversal-confirm', [TransactionController::class, 'reversal_confirm'])->name('reversal.confirm');
        Route::post('/updateairtimeserver', [ServerController::class, 'changeserver'])->name('updateairtimeserver');
        Route::get('/reverse-transaction/{id}', 'TransactionController@reverse')->name('reverse');
        Route::get('/reverse-transaction2/{id}', [TransactionController::class, 'reverse2'])->name('reverse2');
        Route::any('/report_pnl', [ReportsController::class, 'pnl'])->name('report_pnl');
        Route::any('/report_yearly', [ReportsController::class, 'yearly'])->name('report_yearly');
        Route::any('/report_monthly', [ReportsController::class, 'monthly'])->name('report_monthly');
        Route::any('/report_daily', [ReportsController::class, 'daily'])->name('report_daily');
        Route::get('/cryptorequest', 'TransactionController@cryptos')->name('cryptos');
    });

});

require __DIR__ . '/storages.php';





