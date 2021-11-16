<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Api\ValidateController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Models\Withdraw;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SwitchController extends Controller
{

    public function junction(Request $request)
    {
        $input = $request->all();


        if (!isset($input['service'])) {
            return response()->json(['success' => 0, 'message' => 'Kindly add service to your request']);
        }

        switch ($input['service']) {
            case "balance":
                return $this->myBalance($request);
            case "withdraw_commission":
                return $this->withdrawCommision($request);
            default:
                return response()->json(['success' => 0, 'message' => 'Invalid service provided']);
        }

    }

    public function validateService(Request $request){
        $input=$request->all();

        if (!isset($input['service'])){
            return response()->json(['success' => 0, 'message' => 'Kindly add service to your request']);
        }

        if (!isset($input['phone'])){
            return response()->json(['success' => 0, 'message' => 'Kindly add phone to your request']);
        }

        if (!isset($input['coded'])){
            return response()->json(['success' => 0, 'message' => 'Kindly add coded to your request']);
        }


        $s=new ValidateController();

        switch ($input['service']) {
            case "electricity":
                return $s->electricity_server6($input['phone'], $input['coded']);
            case "tv":
                return $s->tv_server6($input['phone'], $input['coded']);
            case "betting":
                return $s->betting_server7($input['phone'], strtoupper($input['coded']));
            case "smile":
                return $s->tv_server6($input['phone'], strtolower($input['coded']));
            default:
                return response()->json(['success' => 0, 'message' => 'Invalid service provided']);
        }
    }


    public function payService(Request $request){
        $input=$request->all();

        if (!isset($input['service'])){
            return response()->json(['success' => 0, 'message' => 'Kindly add service to your request']);
        }

        if (!isset($input['phone'])){
            return response()->json(['success' => 0, 'message' => 'Kindly add phone to your request']);
        }

        if (!isset($input['coded'])){
            return response()->json(['success' => 0, 'message' => 'Kindly add coded to your request']);
        }


        $s=new PayController();

        switch ($input['service']) {
            case "airtime":
                return $s->buyAirtime($request);
            case "data":
                return $s->buyData($request);
            case "tv":
                return $s->buyTV($request);
            case "electricity":
                return $s->buyElectricity($request);
            case "betting":
                return $s->buyBetting($request);
            default:
                return response()->json(['success' => 0, 'message' => 'Invalid service provided']);
        }

    }


    public function listService(Request $request)
    {
        $input = $request->all();

        if (!isset($input['service'])) {
            return response()->json(['success' => 0, 'message' => 'Kindly add service to your request']);
        }

        $lc = new ListController();

        switch ($input['service']) {
            case "all":
                return $lc->all();
            case "electricity":
                return $lc->electricity();
            case "tv":
                return $lc->tv();
            case "airtime":
                return $lc->airtime();
            case "data":
                return $lc->data($request);
            default:
                return response()->json(['success' => 0, 'message' => 'Invalid service provided']);
        }

    }

    public function myBalance(Request $request)
    {

        $key = $request->header('Authorization');

        $user = User::where("api_key", $key)->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'Invalid API key. Kindly contact us on whatsapp@07011223737']);
        }

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => ['wallet' => $user->wallet, 'commission' => $user->bonus]]);
    }

    public function withdrawCommision(Request $request)
    {
        $input = $request->all();

        $rules = array(
            'account_number' => 'required|max:10',
            'bank' => 'required',
            'bank_code' => 'required',
            'wallet' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Incomplete request', 'error' => $validator->errors()]);
        }

        $key = $request->header('Authorization');

        $user = User::where("api_key", $key)->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'Invalid API key. Kindly contact us on whatsapp@07011223737']);
        }

        $valid_wallets = ['Commission', 'Wallet'];
        $valid = false;

        foreach ($valid_wallets as $valid) {
            if ($input['wallet'] == $valid) {
                $valid = true;
            }
        }

        if (!$valid) {
            return response()->json(['success' => 0, 'message' => 'Invalid wallet type supplied. Valid wallets are Commission, Wallet']);
        }

        $input['user_name'] = $user->user_name;
        $input['ref'] = "RW" . Carbon::now()->timestamp . rand();
        $input['device_details'] = "api";
        $input['version'] = "2.0";

        if ($input['wallet'] == "Commission") {
            if ($user->agent_commision < $input['amount']) {
                return response()->json(['success' => 0, 'message' => 'Low commission balance']);
            }
            $user->agent_commision -= $input['amount'];
        }

        if ($input['wallet'] == "Wallet") {
            if ($user->wallet < $input['amount']) {
                return response()->json(['success' => 0, 'message' => 'Low wallet balance']);
            }
            $user->wallet -= $input['amount'];
        }

        $user->save();

        Withdraw::create($input);

        $noti = new PushNotificationController();
        $noti->PushNoti('Izormor2019', "There is a pending withdrawal request, kindly approve on the dashboard.", "Withdrawal Request");


        return response()->json(['success' => 1, 'message' => 'Withdrawal logged successfully', 'ref' => $input['ref']]);
    }

}
