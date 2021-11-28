<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Models\GeneralMarket;
use App\Models\ReferralPlans;
use App\Models\Settings;
use App\Models\Wallet;
use App\Models\Withdraw;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OtherController extends Controller
{
    public function paymentcheckout()
    {
        $settings = Settings::all();
        foreach ($settings as $setting) {
            $sett[$setting->name] = $setting->value;
        }

        $data['rave'] = $sett['fund_rave'];
        $data['paystack'] = $sett['fund_paystack'];
        $data['payant'] = $sett['fund_payant'];
        $data['bank'] = $sett['fund_bank'];
        $data['monnify'] = $sett['fund_monnify'];
        $data['korapay'] = $sett['fund_korapay'];
        $data['wallet'] = $sett['fund_bank'];

        $d['paystack_public'] = $sett['fund_paystack_details'];
        $d['paystack_secret'] = $sett['secret_paystack_details'];
        $d['rave_public'] = $sett['fund_rave_details'];
        $d['rave_enckey'] = $sett['fund_rave_key'];
        $d['monnify_apikey'] = $sett['fund_monnify_apikey'];
        $d['monnify_contractcode'] = $sett['fund_monnify_contractcode'];

        return response()->json(['success' => 1, 'message' => 'Fetched successful', 'data' => ['status' => $data, 'details' => $d]]);
    }

    public function referralPlans()
    {
        $data = ReferralPlans::get();

        return response()->json(['success' => 1, 'message' => 'Fetched successful', 'data' => $data]);
    }

    public function fundWallet(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'amount' => 'required',
            'medium' => 'required',
            'ref' => 'required',
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['deviceid'] = $_SERVER['HTTP_USER_AGENT'];

        $input['user_name'] = Auth::user()->user_name;

        $input['o_wallet'] = Auth::user()->wallet;

        $input['n_wallet'] = $input['o_wallet'] + $input['amount'];

        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User not found']);
        }

        $input['date'] = Carbon::now();
        $input['status'] = "pending";

        Wallet::create($input);

        return response()->json(['success' => 1, 'message' => 'Fund Logged for further processing']);
    }

    public function withdraw(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'wallet' => 'required',
            'amount' => 'required',
            'ref' => 'required',
            'account_number' => 'required',
            'bank' => 'required',
            'bank_code' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['user_name'] = Auth::user()->user_name;

        $input['version'] = $request->header('version');

        $input['device_details'] = $_SERVER['HTTP_USER_AGENT'];

        $u = User::where("user_name", $input['user_name'])->first();


        if ($input['wallet'] == "Mega Bonus") {
            if ($u->bonus < $input['amount']) {
                return response()->json(['success' => 0, 'message' => 'Low wallet balance']);
            }
            $u->bonus -= $input['amount'];
        }

        if ($input['wallet'] == "Commission") {
            if ($u->agent_commision < $input['amount']) {
                return response()->json(['success' => 0, 'message' => 'Low wallet balance']);
            }
            $u->agent_commision -= $input['amount'];
        }

        $u->save();

        Withdraw::create($input);

        $noti = new PushNotificationController();
        $noti->PushNoti('Izormor2019', "There is a pending withdrawal request, kindly approve on the dashboard.", "Withdrawal Request");

        return response()->json(['success' => 1, 'message' => 'Withdrawal logged successfully']);

    }

    function banklist()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.paystack.co/bank',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . env("PAYSTACK_SECRET_KEY"),
                "Cache-Control: no-cache",
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        $rep = json_decode($response, true);


        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $rep['data']]);
    }

    function verifyBank(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'accountnumber' => 'required',
            'code' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['status' => 0, 'message' => 'Incomplete request', 'error' => $validator->errors()]);
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.paystack.co/bank/resolve?account_number=' . $input['accountnumber'] . '&bank_code=' . $input['code'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . env("PAYSTACK_SECRET_KEY"),
                "Cache-Control: no-cache",
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        $rep = json_decode($response, true);

        if ($rep['status']) {
            return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data' => $rep['data']['account_name']]);
        } else {
            return response()->json(['success' => 0, 'message' => 'Unable to resolve account details']);
        }


    }

    public function getGmTrans(Request $request)
    {
        $set = Settings::where('name', 'general_market')->first();

        $trans = GeneralMarket::OrderBy('id', 'desc')->limit(300)->get();
        if ($trans->isEmpty()) {
            return response()->json(['success' => 1, 'message' => 'No transactions found', 'wallet' => $set->value]);
        }
        return response()->json(['success' => 1, 'message' => 'General Market Transactions Fetched', 'data' => $trans, 'wallet' => $set->value]);
    }

    public function getPoints()
    {
        $us = User::orderBy('points', 'desc')->limit(10)->get(['full_name', 'user_name', 'points', 'photo']);
        $use = User::orderBy('points', 'desc')->get(['full_name', 'user_name', 'points', 'photo']);
        $settings = Settings::where('name', 'leaderboard_banner')->first();
        $rank = 1;

        foreach ($use as $item) {
            if ($item->user_name == Auth::user()->user_name) {
                break;
            } else {
                $rank++;
            }
        }

        return response()->json(['success' => 1, 'message' => 'Leaderboard Fetched successfully', 'rank' => $rank, 'data' => $us, 'banner' => $settings->value]);
    }

}
