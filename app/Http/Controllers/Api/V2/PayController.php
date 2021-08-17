<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Airtime2Cash;
use App\Models\Airtime2CashSettings;
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

        if ($validator->passes()) {
            try {
                $input['ip'] = $_SERVER['REMOTE_ADDR'];

                $input['version'] = $request->header('version');

                $input['device_details'] = $_SERVER['HTTP_USER_AGENT'];

                $input['phoneno'] = $input['number'];

                $input['user_name'] = Auth::user()->user_name;

                Airtime2Cash::create($input);

                $number = Airtime2CashSettings::where('network', '=', $input['network'])->first();

                return response()->json(['status' => 1, 'message' => 'Transfer #' . $input['amount'] . ' to ' . $number->number . ' and get your value instantly. Reference: ' . $input['ref'] . '. By doing so, you acknowledge that you are the legitimate owner of this airtime and you have permission to send it to us and to take possession of the airtime.']);
            } catch (Exception $e) {
                return response()->json(['status' => 0, 'message' => 'An error occured', 'error' => $e]);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'Some forms are left out', 'error' => $validator->errors()]);
        }

    }
}
