<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Api\ValidateController;
use App\Http\Controllers\Controller;
use App\Models\Withdraw;
use App\User;
use Illuminate\Http\Request;

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

        switch ($input['service']){
            case "electricity":
                return $s->electricity_server6($input['phone'], $input['coded']);
            case "tv":
                return $s->tv_server6($input['phone'], $input['coded']);
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

        $key = $request->header('Authorization');

        $user = User::where("api_key", $key)->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'Invalid API key. Kindly contact us on whatsapp@07011223737']);
        }

        if ($user->agent_commision < $input['amount']) {
            return response()->json(['success' => 0, 'message' => 'Low commission balance']);
        }

        $user->agent_commision -= $input['amount'];
        $user->save();

        Withdraw::create($input);

        return response()->json(['success' => 1, 'message' => 'Withdrawal logged successfully']);
    }

}
