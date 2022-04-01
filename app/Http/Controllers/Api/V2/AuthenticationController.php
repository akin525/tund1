<?php

namespace App\Http\Controllers\Api\V2;

use App\Events\NewDeviceEvent;
use App\Http\Controllers\Controller;
use App\Jobs\CreateProvidusAccountJob;
use App\Jobs\LoginAttemptApiFinderJob;
use App\Mail\PasswordResetMail;
use App\Models\LoginAttempt;
use App\Models\NewDevice;
use App\Models\SocialLogin;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $deviceid = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

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
        $create["target"] = env('NEW_ACCOUNT_MESSAGE');
        $create["user_name"] = $user_name;
        $create["email"] = $input["email"];
        $create["phoneno"] = $input["phoneno"];
        $create["mcdpassword"] = $input["password"];
        $create["password"] = "";
        $create["referral"] = $input["referral"];
        $create["gnews"] = 'Are you looking forward to spending less money on data subscriptions? Or Pay Electricity bills, and even top up your betting platforms conveniently without moving a finger, you just arrived at the right place';
        $date = date("Y-m-d H:i:s");
        $create["devices"] = $deviceid;

        if (User::create($create)) {
            // successfully inserted into database
            $job = (new CreateProvidusAccountJob($create["user_name"]))
                ->delay(Carbon::now()->addSeconds(10));
            dispatch($job);

            return response()->json(['success' => 1, 'message' => 'Account created successfully']);
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

        $input['device'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];


        $input['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $la = LoginAttempt::create($input);
        $job = (new LoginAttemptApiFinderJob($la->id))
            ->delay(Carbon::now()->addSeconds(1));
        dispatch($job);


        $user = User::where('user_name', $input['user_name'])->orWhere('email', $input['user_name'])->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User does not exist']);
        }

        if ($user->mcdpassword != $input['password']) {
            return response()->json(['success' => 0, 'message' => 'Incorrect password attempt']);
        }

        if ($user->devices != $input['device']) {
            NewDeviceEvent::dispatch($user, $input['device']);

            $la->status = "new_device";
            $la->save();

            return response()->json(['success' => 2, 'message' => 'Login successfully']);
        }

        $la->status = "authorized";
        $la->save();

        // Revoke all tokens...
        $user->tokens()->delete();

        $token = $user->createToken($input['device'])->plainTextToken;


        return response()->json(['success' => 1, 'message' => 'Login successfully', 'token' => $token]);
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

        $device = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

        $user = User::where('user_name', $input['user_name'])->orWhere('email', $input['user_name'])->first();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User does not exist']);
        }


        $nl = NewDevice::where([['user_name', $user->user_name], ['device', $device]])->latest()->first();

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

        // Revoke all tokens...
        $user->tokens()->delete();

        $token = $user->createToken($device)->plainTextToken;

        return response()->json(['success' => 1, 'message' => 'Device Verified Successfully', 'data' => $token]);
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
        $data['device'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];
        $data['ip_address'] = $_SERVER['REMOTE_ADDR'];

        $la = LoginAttempt::create($data);
        $job = (new LoginAttemptApiFinderJob($la->id))
            ->delay(Carbon::now()->addSeconds(1));
        dispatch($job);


        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User does not exist']);
        }
        SocialLogin::create($input);

        $token = $user->createToken($data['device'])->plainTextToken;

        return response()->json(['success' => 1, 'message' => 'Social login successful', 'token' => $token]);
    }

    public function biometricLogin(Request $request)
    {
        $input['device'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];
        $input['version'] = $request->header('version');

        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User does not exist']);
        }

        // Revoke all tokens...
        $user->tokens()->delete();

        $token = $user->createToken($input['device'])->plainTextToken;

        return response()->json(['success' => 1, 'message' => 'Login successfully', 'token' => $token]);
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

}
