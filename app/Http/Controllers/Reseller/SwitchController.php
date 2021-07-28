<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Api\SellAirtimeController;
use App\Http\Controllers\Api\ValidateController;
use App\Http\Controllers\Controller;
use App\Models\ResellerAirtimeControl;
use App\Models\ResellerControl;
use App\Models\ResellerElecticity;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SwitchController extends Controller
{

    public function authenticate(Request $request){
        $key=$request->header('Authorization');

        if($key==null){
            return response()->json(['status' => 0, 'message' => 'Kindly add the APIKey you obtain in your header request. Kindly contact us on whatsapp@07011223737']);
        }

        $us=User::where("api_key", $key)->first();
        if(!$us){
            return response()->json(['status' => 0, 'message' => 'Invalid API key. Kindly contact us on whatsapp@07011223737']);
        }
    }

    public function junction(Request $request, $reseller){
        $input=$request->all();

        switch ($input['service']){
            case "status":
                $this->status();
                break;
            case "airtime":
                $this->buyairtime($request, $reseller);
        }

    }

    public function validateService(Request $request){
        $input=$request->all();

        if (!isset($input['service'])){
            return response()->json(['status' => 0, 'message' => 'Kindly add service to your request']);
        }

        if (!isset($input['phone'])){
            return response()->json(['status' => 0, 'message' => 'Kindly add phone to your request']);
        }

        if (!isset($input['coded'])){
            return response()->json(['status' => 0, 'message' => 'Kindly add coded to your request']);
        }


        $s=new ValidateController();

        switch ($input['service']){
            case "electricity":
                return $s->electricity_server6($input['phone'], $input['coded']);
            case "tv":
                return $s->tv_server6($input['phone'], $input['coded']);
            default:
                return response()->json(['status' => 0, 'message' => 'Invalid service provided']);
        }

    }


    public function listService(Request $request){
        $input=$request->all();

        if (!isset($input['service'])){
            return response()->json(['status' => 0, 'message' => 'Kindly add service to your request']);
        }

        $s=new ValidateController();

        switch ($input['service']){
            case "electricity":
                return $this->elecstatus();
            case "tv":
                return $s->tv_server6($input['phone'], $input['coded']);
            default:
                return response()->json(['status' => 0, 'message' => 'Invalid service provided']);
        }

    }

    public function status(){
        $st=ResellerControl::get();
        return response()->json(['status' => 1, 'message' => 'Fetched successfully', 'data' => $st]);
    }


    public function airtimestatus(){
        $st=ResellerAirtimeControl::get();
        return response()->json(['status' => 1, 'message' => 'Fetched successfully', 'data' => $st]);
    }

    public function datastatus(){
        $st=ResellerAirtimeControl::get();
        return response()->json(['status' => 1, 'message' => 'Fetched successfully', 'data' => $st]);
    }

    public function elecstatus(){
        $st=ResellerElecticity::get();
        return response()->json(['status' => 1, 'message' => 'Fetched successfully', 'data' => $st]);
    }

    public function buyairtime(Request $request, $reseller){
        $input=$request->all();

        if($input['amount'] < 100){
            return response()->json(['status' => 0, 'message' => 'Minimum amount is 100. Change amount field and try again']);
        }

        if($input['amount'] > 5000){
            return response()->json(['status' => 0, 'message' => 'Maximum amount is 5,000. Change amount field and try again']);
        }

        if($input['amount'] > $reseller->wallet){
            return response()->json(['status' => 0, 'message' => 'Insufficient balance, kindly topup your balance']);
        }

        $net="";
        $ref=Str::random(10);

        switch (strtolower($input['coded'])){
            case "m":
                $net="MTN";
                break;
            case "a":
                $net="AIRTEL";
                break;
        }

        $rac=ResellerAirtimeControl::where("network", $net)->first();

        $sr=new SellAirtimeController();

        return response()->json(['status' => 1, 'message' => 'Your order is successful', 'ref'=>$ref]);

//        if($rac->server==6){
//            $sr->server6();
//        }


    }

    public function buydata(Request $request, $reseller){
        $input=$request->all();

        $net="";
        $ref=Str::random(10);

        switch (strtolower($input['coded'])){
            case "m":
                $net="MTN";
                break;
            case "a":
                $net="AIRTEL";
                break;
        }

        $rac=ResellerAirtimeControl::where("network", $net)->first();


        if($input['amount'] < 100){
            return response()->json(['status' => 0, 'message' => 'Minimum amount is 100. Change amount field and try again']);
        }

        if($input['amount'] > 5000){
            return response()->json(['status' => 0, 'message' => 'Maximum amount is 5,000. Change amount field and try again']);
        }

        if($input['amount'] > $reseller->wallet){
            return response()->json(['status' => 0, 'message' => 'Insufficient balance, kindly topup your balance']);
        }


        $sr=new SellAirtimeController();

        return response()->json(['status' => 1, 'message' => 'Your order is successful', 'ref'=>$ref]);

//        if($rac->server==6){
//            $sr->server6();
//        }


    }
}
