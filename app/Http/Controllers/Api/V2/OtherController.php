<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\ReferralPlans;
use App\Models\Settings;
use App\Models\Wallet;
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
}
