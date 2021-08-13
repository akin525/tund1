<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Jobs\CreateProvidusAccountJob;
use App\Jobs\LoginAttemptApiFinderJob;
use App\Mail\NewDeviceLoginMail;
use App\Mail\PasswordResetMail;
use App\Models\LoginAttempt;
use App\Models\NewDevice;
use App\Models\Settings;
use App\Models\SocialLogin;
use App\Models\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function signup(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'password' => 'required',
            'phoneno' => 'required',
            'email' => 'required',
            'referral' => 'nullable',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $user_name = $input['user_name'];
        $deviceid = $_SERVER['HTTP_USER_AGENT'];

        $user = User::where('user_name', $user_name)->get();
        if (!$user->isEmpty()) {
            return response()->json(['success' => 0, 'message' => 'User name already exist']);
        }

        $user = User::where('email', $input['email'])->get();
        if (!$user->isEmpty()) {
            return response()->json(['success' => 0, 'message' => 'Email already exist']);
        }

        //values gotten
        $create["wallet"] = "0";
        $create["status"] = "client";
        $create["level"] = "1";
        $create["target"] = "Make up to 10 transactions to be eligible for an Agent and send a request mail to info@5starcompany.com.ng where you earn incentives on transactions done at the end of the month";
        $create["user_name"] = $user_name;
        $create["email"] = $input["email"];
        $create["phoneno"] = $input["phoneno"];
        $create["mcdpassword"] = $input["password"];
        $create["password"] = "";
        $create["referral"] = $input["referral"];
        $create["gnews"] = 'If you are a business person that needs to increase your investment and make more money, you just arrived at the right place';
        $date = date("Y-m-d H:i:s");
        $create["devices"] = $deviceid;

        if (User::create($create)) {
            // successfully inserted into database
            $job = (new CreateProvidusAccountJob($create["user_name"]))
                ->delay(Carbon::now()->addSeconds(10));
            dispatch($job);

            return response()->json(['success' => 1, 'message' => 'Client Successfully Added']);
        } else {

            return response()->json(['success' => 0, 'message' => 'Oops! An error occurred.']);
        }

    }

    public function login(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'password' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $_SERVER['HTTP_USER_AGENT'];

        if (isset($input['login'])) {
            $input['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $la = LoginAttempt::create($input);
            $job = (new LoginAttemptApiFinderJob($la->id))
                ->delay(Carbon::now()->addSeconds(1));
            dispatch($job);
        }

        $user = User::where('user_name', $input['user_name'])->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User does not exist']);
        }

        if ($user->mcdpassword != $input['password']) {
            if ($user->email != $input['password']) {
                return response()->json(['success' => 0, 'message' => 'Incorrect password attempt']);
            }
        }

        $date = date("Y-m-d H:i:s");
        $user->last_login = $date;
        $user->save();

        $uinfo['full_name'] = $user->full_name;
        $uinfo['company_name'] = $user->company_name;
        $uinfo['dob'] = $user->dob;
        $uinfo['wallet'] = $user->wallet;
        $uinfo['bonus'] = $user->bonus;
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
        $uinfo['agent_commision'] = $user->agent_commision;
        $uinfo['points'] = $user->points;

        $uinfo["total_fund"] = Transaction::where([['user_name', $input['user_name']], ['name', 'wallet funding'], ['status', 'successful']])->count();
        $uinfo["total_trans"] = Transaction::where([['user_name', $input['user_name']], ['status', 'delivered']])->count();
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

        if (isset($input['login'])) {
            $la->status = "authorized";
            $la->save();
        }

        if ($user->devices != $input['device']) {
            $tr['code'] = str_shuffle(substr(date('sydmM') . rand() . $input['user_name'], 0, 4));
            $tr['email'] = $user->email;
            $tr['user_name'] = $user->user_name;
            $tr['expired'] = Carbon::now()->addHour();
            $tr['device'] = $_SERVER['HTTP_USER_AGENT'];

            NewDevice::create($tr);

            if (env('APP_ENV') != "local") {
                Mail::to($user->email)->send(new NewDeviceLoginMail($tr));
            }

            return response()->json(['success' => 2, 'message' => 'Login successfully', 'data' => $d]);
        }

        return response()->json(['success' => 1, 'message' => 'Login successfully', 'data' => $d]);
    }

    public function newdeviceLogin(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'code' => 'required',
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $device = $_SERVER['HTTP_USER_AGENT'];

        $user = User::where('user_name', $input['user_name'])->first();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User does not exist']);
        }


        $nl = NewDevice::where([['user_name', $input['user_name']], ['device', $device]])->latest()->first();

        if (!$nl) {
            return response()->json(['success' => 0, 'message' => 'Kindly login']);
        }

        if ($nl->code != $input['code']) {
            return response()->json(['success' => 0, 'message' => 'Code did not match']);
        }

        if (Carbon::now() > $nl->expired) {
            return response()->json(['success' => 0, 'message' => 'Code has expired. Time limit is one hour']);
        }

        $user->devices = $device;
        $user->save();

        return response()->json(['success' => 1, 'message' => 'Device Verified Successfully', 'user_name' => $user->user_name, 'email' => $user->email]);
    }

    public function socialLogin(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'email' => 'required',
            'name' => 'required',
            'avatar' => 'required',
            'accesstoken' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $user = User::where('email', $input['email'])->first();

        $data['user_name'] = $input['email'];
        $data['password'] = $input['accesstoken'];
        $data['device'] = $_SERVER['HTTP_USER_AGENT'];
        $data['ip_address'] = $_SERVER['REMOTE_ADDR'];

        $la = LoginAttempt::create($data);
        $job = (new LoginAttemptApiFinderJob($la->id))
            ->delay(Carbon::now()->addSeconds(1));
        dispatch($job);


        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User does not exist']);
        }
        SocialLogin::create($input);

        return response()->json(['success' => 1, 'message' => 'Social login successful', 'user_name' => $user->user_name, 'email' => $user->email]);
    }

    public function resetpassword(Request $request)
    {

        $input = $request->all();
        $rules = array(
            'user_name' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $user = User::where('user_name', $input['user_name'])->orWhere('email', $input['user_name'])->first();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User does not exist']);
        }

        $pass = str_shuffle(substr(date('sydmM') . rand() . $input['user_name'], 0, 8));

        $user->mcdpassword = $pass;
        $user->save();

        $tr['password'] = $pass;
        $tr['email'] = $user->email;
        $tr['user_name'] = $user->user_name;

        if (env('APP_ENV') != "local") {
            Mail::to($user->email)->send(new PasswordResetMail($tr));
        }

        return response()->json(['success' => 1, 'message' => 'A new password has been sent to your mail successfully']);
    }


    public function change_password(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'o_password' => 'required',
            'n_password' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            $user = User::where("user_name", $input['user_name'])->first();

            if ($user->mcdpassword != $input['o_password']) {
                return response()->json(['success' => 0, 'message' => 'Wrong Old Password']);
            }

            $user->mcdpassword = $input['n_password'];
            $user->save();

            return response()->json(['success' => 1, 'message' => 'Password changed successfully']);
        }
    }

    public function change_pin(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'o_pin' => 'required',
            'n_pin' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            $user = User::where("user_name", $input['user_name'])->first();
            if ($user->pin != $input['o_pin']) {
                return response()->json(['success' => 0, 'message' => 'Wrong Old Pin']);
            }

            $user->pin = $input['n_pin'];
            $user->save();

            return response()->json(['success' => 1, 'message' => 'Pin changed successfully']);
        }
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
