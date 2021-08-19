<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Airtime2Cash;
use App\Models\Airtime2CashSettings;
use App\Models\Transaction;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PayController extends Controller
{
    function buyairtime(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'provider' => 'required',
            'amount' => 'required',
            'number' => 'required',
            'country' => 'required',
            'payment' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $_SERVER['HTTP_USER_AGENT'];

        return response()->json(['success' => 1, 'message' => 'Airtime Sent Successfully']);

    }

    function buydata(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'coded' => 'required',
            'number' => 'required',
            'payment' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $_SERVER['HTTP_USER_AGENT'];

        return response()->json(['success' => 1, 'message' => 'Data Sent Successfully']);

    }

    function buytv(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'coded' => 'required',
            'number' => 'required',
            'payment' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $_SERVER['HTTP_USER_AGENT'];

        return response()->json(['success' => 1, 'message' => 'TV Subscribe Successfully']);

    }

    function buyelectricity(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'provider' => 'required',
            'number' => 'required',
            'amount' => 'required',
            'payment' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $_SERVER['HTTP_USER_AGENT'];

        return response()->json(['success' => 1, 'message' => 'Electricity Token Generated Successfully', 'token' => 'hfhfwufwf743uewfj48ui']);

    }

    function buybetting(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'provider' => 'required',
            'number' => 'required',
            'amount' => 'required',
            'payment' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $_SERVER['HTTP_USER_AGENT'];

        return response()->json(['success' => 1, 'message' => 'Betting topup will reflect soon']);

    }

    public function a2ca2b(Request $request)
    {

        $input = $request->all();
        $rules = array(
            'network' => 'required',
            'number' => 'required',
            'amount' => 'required',
            'receiver' => 'required',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['status' => 0, 'message' => 'Some forms are left out', 'error' => $validator->errors()]);
        }
        try {
            $input['ip'] = $_SERVER['REMOTE_ADDR'];

            $input['version'] = $request->header('version');

            $input['device_details'] = $_SERVER['HTTP_USER_AGENT'];

            $input['phoneno'] = $input['number'];

            $input['user_name'] = Auth::user()->user_name;

                Airtime2Cash::create($input);

            $number = Airtime2CashSettings::where('network', '=', $input['network'])->first();

            return response()->json(['success' => 1, 'message' => 'Transfer #' . $input['amount'] . ' to ' . $number->number . ' and get your value instantly. Reference: ' . $input['ref'] . '. By doing so, you acknowledge that you are the legitimate owner of this airtime and you have permission to send it to us and to take possession of the airtime.']);
        } catch (Exception $e) {
            return response()->json(['success' => 0, 'message' => 'An error occured', 'error' => $e]);
        }


    }

    public function resultchecker(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'type' => 'required',
            'quantity' => 'required',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input["user_name"] = Auth::user()->user_name;
        $input['price'] = 700;

        $user = User::where('user_name', $input["user_name"])->first();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User not found']);
        }

        $uid = $input['user_name'];
        $net = $input['type'];
        $qty = $input['quantity'];
        $price = $input['price'];
        $p = $price * $qty;
        $ref = $input["ref"];
        $input["i_wallet"] = $user->wallet;
        $input['f_wallet'] = $input["i_wallet"] - $p;
        $input['amount'] = $p;

        if ($p > $user->wallet) {
            echo $p;
            echo "<br/>";
            echo $user->wallet;
            return response()->json(['success' => 0, 'message' => 'Insufficient Balance']);
        }

        $input['date'] = Carbon::now();
        $input['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $input['description'] = $uid . " order " . $net . " result checker of " . $qty . " quantity with ref " . $ref;
        $input['extra'] = "qty-" . $qty . ", net-" . $net . ", ref-" . $ref;
        $input['name'] = 'Result Checker';
        $input['status'] = 'submitted';
        $input['code'] = 'rch';

        // mysql inserting a new row
        Transaction::create($input);

        $user->wallet = $input['f_wallet'];
        $user->save();


//            $at = new PushNotificationController();
//            $at->PushNoti($input['user_name'], "Hi " . $input['user_name'] . ", you will receive your " . $net . " request in your mail soon. Thanks", "Result Checker");

        return response()->json(['success' => 1, 'message' => 'You will receive your request soon']);

    }
}
