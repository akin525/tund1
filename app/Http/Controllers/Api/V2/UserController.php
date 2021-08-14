<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use App\Models\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $uinfo['full_name'] = $user->full_name;
        $uinfo['company_name'] = $user->company_name;
        $uinfo['dob'] = $user->dob;
        $uinfo['status'] = $user->status;
        $uinfo['level'] = $user->level;
        $uinfo['photo'] = $user->photo;
        $uinfo['reg_date'] = $user->reg_date;
        $uinfo['target'] = $user->target;
        $uinfo['user_name'] = $user->user_name;
        $uinfo['email'] = $user->email;
        $uinfo['phoneno'] = $user->phoneno;
        $uinfo['gnews'] = $user->gnews;
        $uinfo['fraud'] = $user->fraud;
        $uinfo['referral'] = $user->referral;
        $uinfo['referral_plan'] = $user->referral_plan;
        $uinfo['account_number'] = $user->account_number;
        $uinfo['account_number2'] = $user->account_number2;
        $uinfo['last_login'] = $user->last_login;
        $uinfo['points'] = $user->points;

        $uinfo["total_fund"] = Transaction::where([['user_name', $user->user_name], ['name', 'wallet funding'], ['status', 'successful']])->count();
        $uinfo["total_trans"] = Transaction::where([['user_name', $user->user_name], ['status', 'delivered']])->count();
        // get user transactions report from transactions table

        //get airtime discounts
        $airsets = DB::table("tbl_serverconfig_airtime")->where('name', '=', 'discount')->first();
        $uinfo['airtime_discount_mtn'] = $airsets->mtn;
        $uinfo['airtime_discount_glo'] = $airsets->glo;
        $uinfo['airtime_discount_etisalat'] = $airsets->etisalat;
        $uinfo['airtime_discount_airtel'] = $airsets->airtel;

        $settings = Settings::all();
        foreach ($settings as $setting) {
            $sett[$setting->name] = $setting->value;
        }
        $d = array_merge($uinfo, $sett);

        $me['user_name'] = $user->user_name;
        $me['account_details'] = $user->account_number;
        $me['referral_plan'] = $user->referral_plan;
        $me['photo'] = $user->photo;

        $balances['wallet'] = "$user->wallet";
        $balances['bonus'] = "$user->bonus";
        $balances['agent_commision'] = "$user->agent_commision";
        $balances['points'] = "$user->points";
        $balances['general_market'] = $sett['general_market'];


        $services['airtime'] = $sett['airtime'];
        $services['data'] = $sett['data'];
        $services['paytv'] = $sett['paytv'];
        $services['resultchecker'] = $sett['resultchecker'];
        $services['rechargecard'] = $sett['resultchecker'];
        $services['electricity'] = $sett['electricity'];
        $services['betting'] = $sett['betting'];
        $services['airtimeconverter'] = $sett['airtimeconverter'];


        $advts['unity_testmode'] = $sett['unity_testmode'];
        $advts['unity_gameid'] = $sett['unity_gameid'];

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => ['user' => $me, 'balances' => $balances, 'services' => $services, 'news' => $user->gnews, 'adverts' => $advts]]);
    }

    public function change_password(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'o_password' => 'required',
            'n_password' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $user = Auth::user();

        if ($user->mcdpassword != $input['o_password']) {
            return response()->json(['success' => 0, 'message' => 'Wrong Old Password']);
        }

        $user->mcdpassword = $input['n_password'];
        $user->save();

        return response()->json(['success' => 1, 'message' => 'Password changed successfully']);

    }

    public function change_pin(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'o_pin' => 'required',
            'n_pin' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $user = Auth::user();
        if ($user->pin != $input['o_pin']) {
            return response()->json(['success' => 0, 'message' => 'Wrong Old Pin']);
        }

        $user->pin = $input['n_pin'];
        $user->save();

        return response()->json(['success' => 1, 'message' => 'Pin changed successfully']);
    }

    public function referrals()
    {
        $user = Auth::user();
        $referrals = User::where('referral', $user->user_name)->select('user_name', 'photo', 'referral_plan', 'reg_date')->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $referrals]);
    }

    public function transactions()
    {
        $user = Auth::user();
        $trans = Transaction::where('user_name', $user->user_name)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function updateAgent(Request $request)
    {
        /* updated on 11/08/2019 by samji
         * Following code will get single product details
         * A product is identified by product id (uid)
         */

        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'dob' => 'required',
            'image' => 'required',
            'address' => 'required',
            'deviceid' => 'required');

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if ($validator->passes()) {

            $user = User::where('user_name', $input['user_name'])->first();
            if (!$user) {
                return response()->json(['success' => 0, 'message' => 'User not found']);
            }

            if ($user->dob == "") {
                $image = $input["image"];
                $photo = $input["user_name"] . ".JPG";

                $decodedImage = base64_decode("$image");
                file_put_contents(storage_path("app/public/avatar/" . $photo), $decodedImage);

                $user->full_name = $input['full_name'];
                $user->company_name = $input['company_name'];
                $user->dob = $input['dob'];
                $user->bvn = $input['bvn'];
                $user->address = $input['address'];
                $user->target = $input["request"] . " in progress...";
                $user->photo = $input["user_name"] . ".JPG";
                $user->note = $input["note"];
                $user->save();

                return response()->json(['success' => 1, 'message' => 'Data submitted successfully, kindly check your mail for progress']);
            } else {
                return response()->json(['success' => 0, 'message' => 'Data can only be submitted once']);
            }
        } else {
            // required field is missing
            // echoing JSON response
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }
    }

    public function update_referral(Request $request)
    {
        /*
         * Following code will get single product details
         * A product is identified by product id (uid)
         */
        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'referral' => 'required',
            'version' => 'required',
            'deviceid' => 'required');

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if ($validator->passes()) {

            $uid = User::where('user_name', $input['user_name'])->first();
            if (!$uid) {
                return response()->json(['success' => 0, 'message' => 'User does not exist']);
            }

            $referral = User::where('user_name', $input['referral'])->first();
            if (!$referral) {
                return response()->json(['success' => 0, 'message' => 'Referral does not exist']);
            }

            if ($uid == $referral) {
                return response()->json(['success' => 0, 'message' => 'You can not add your self as a referral']);
            }

            if ($uid->referral != "") {
                return response()->json(['success' => 0, 'message' => 'Referral has already been added']);
            }
            //values gotten
            $r_wallet = $referral->wallet;
            $r_email = $referral->email;
            $r_referralplan = $referral->referral_plan;
            $r_user_name = $referral->user_name;

            $referral_count = User::where('referral', $input['referral'])->count();

            if ($r_referralplan == "free") {
                $max = 20;
            } elseif ($r_referralplan == "larvae") {
                $max = 50;
            } elseif ($r_referralplan == "butterfly") {
                $max = 100;
            }

            if ($max == $referral_count) {
                return response()->json(['success' => 0, 'message' => $referral->user_name . " has reached referral limit. Kindly inform the user to upgrade referral plan"]);
            }

            $uid->referral = $input['referral'];
            $uid->save();

            $noti = new ATMmanagerController();
            $noti->PushNoti($input['referral'], "Hi " . $input['referral'] . ", " . $input['user_name'] . " has added you as a referral. You will start receiving atleast #5 on every data transaction, to earn more kindly upgrade. Thanks", "Referral");

            return response()->json(['success' => 1, 'message' => $referral->user_name . " has been added as your referral successfully", 'referral' => $input['referral']]);

        } else {
            // required field is missing
            // echoing JSON response
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }
    }

}
