<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Api\AirtimeSellController;
use App\Http\Controllers\Api\SellAirtimeController;
use App\Http\Controllers\Api\SellDataController;
use App\Http\Controllers\Controller;
use App\Models\ResellerAirtimeControl;
use App\Models\ResellerDataPlans;
use App\Models\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayController extends Controller
{
    public function buyAirtime(Request $request){
        $input=$request->all();

        switch(strtolower($input['coded'])) {
            case "m":
                $rac = ResellerAirtimeControl::where("network", "MTN")->first();
                break;
            case "a":
                $rac = ResellerAirtimeControl::where("network", "AIRTEL")->first();
                break;
            case "9":
                $rac = ResellerAirtimeControl::where("network", "9MOBILE")->first();
                break;
            case "g":
                $rac = ResellerAirtimeControl::where("network", "GLO")->first();
                break;
            default:
                $rac = "";
        }

        if ($rac == "") {
            return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
        }

        if ($rac->status == 0) {
            return response()->json(['success' => 0, 'message' => $rac->network.' currently unavailable']);
        }

        if ($input['amount'] < 100) {
            return response()->json(['success' => 0, 'message' => 'Minimum amount is #100']);
        }

        if ($input['amount'] > 5000) {
            return response()->json(['success' => 0, 'message' => 'Maximum amount is #5000']);
        }

        $dis=explode("%", $rac->discount);
        $discount=$input['amount'] * ($dis[0]/100);
        $debitAmount=$input['amount'] - $discount;


        return $this->debitReseller($request, $rac->network, $debitAmount, $discount, $rac->server, "airtime");
    }

    public function buyAirtimeCTD(Request $request, $ref, $net, $dada, $server){
        $input=$request->all();

        $air=new SellAirtimeController();

        switch(strtolower($server)) {
            case "6":
                return $air->server6($request, $input['amount'], $input['phone'], $ref, $net, $request, $dada, "reseller");
            case "a":
                $rac = ResellerAirtimeControl::where("network", "AIRTEL")->first();
                break;
            case "9":
                $rac = ResellerAirtimeControl::where("network", "9MOBILE")->first();
                break;
            case "g":
                $rac = ResellerAirtimeControl::where("network", "GLO")->first();
                break;
            default:
                $rac = "";
        }
    }

    public function buyAirtimeOutput(Request $request, $ref, $status, $dada){

        if($status==1){
            return response()->json(['status' => 1, 'message' => 'Transaction Successful instantly', 'ref'=> $ref, 'debitAmount' =>$dada['amount'], 'discountAmount'=>$dada['discount']]);
        }

        return response()->json(['status' => 0, 'message' => 'Transaction is pending', 'ref'=> $ref, 'debitAmount' =>$dada['amount'], 'discountAmount'=>$dada['discount']]);
    }

    public function buyData(Request $request){
        $input=$request->all();

        $rac=ResellerDataPlans::where("code", strtolower($input['coded']))->first();

        if ($rac == "") {
            return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
        }

        if ($rac->status == 0) {
            return response()->json(['success' => 0, 'message' => $rac->name.' currently unavailable']);
        }

        $dis=explode("%", $rac->discount);
        $discount=$rac->amount * ($dis[0]/100);
        $debitAmount=$rac->amount - $discount;


        return $this->debitReseller($request, $rac->type, $debitAmount, $discount, $rac->server, "data");
    }

    public function buyDataCTD(Request $request, $ref, $net, $dada, $server){
        $input=$request->all();

        $air=new SellDataController();

        switch(strtolower($server)) {
            case "6":
                return $air->server6($request, $input['coded'], $input['phone'], $ref, $net, $request, $dada, "reseller");
            case "a":
                $rac = ResellerAirtimeControl::where("network", "AIRTEL")->first();
                break;
            case "9":
                $rac = ResellerAirtimeControl::where("network", "9MOBILE")->first();
                break;
            case "g":
                $rac = ResellerAirtimeControl::where("network", "GLO")->first();
                break;
            default:
                $rac = "";
        }
    }

    public function buyDataOutput(Request $request, $ref, $status, $dada){

        if($status==1){
            return response()->json(['status' => 1, 'message' => 'Transaction Successful instantly', 'ref'=> $ref, 'debitAmount' =>$dada['amount'], 'discountAmount'=>$dada['discount']]);
        }

        return response()->json(['status' => 0, 'message' => 'Transaction is pending', 'ref'=> $ref, 'debitAmount' =>$dada['amount'], 'discountAmount'=>$dada['discount']]);
    }


    public function debitReseller(Request $request, $provider, $amount, $discount, $server, $requester){
        $input=$request->all();

        $key=$request->header('Authorization');

        $user=User::where("api_key", $key)->first();
        if(!$user){
            return response()->json(['status' => 0, 'message' => 'Invalid API key. Kindly contact us on whatsapp@07011223737']);
        }

        if($amount > $user->wallet){
            return response()->json(['status' => 0, 'message' => 'Insufficient balance to handle request']);
        }

        $ref="R".Carbon::now()->timestamp.rand();

        if($requester=="airtime") {
            $tr['name']=strtoupper($provider).$input['service'];
            $tr['description'] = "Resell " . strtoupper($provider) . $input['service'] . " of " . $input['amount'] . " on " . $input['phone'];
        }else{
            $tr['name']=strtoupper($provider);
            $tr['description'] = "Resell " . strtoupper($provider) . " of " . $input['coded'] . " on " . $input['phone'];
        }
        $tr['amount']=$amount;
        $tr['date']=Carbon::now();
        $tr['device_details']="api";
        $tr['ip_address']=$_SERVER['REMOTE_ADDR'];
        $tr['i_wallet']=$user->wallet;
        $tr['f_wallet']=$tr['i_wallet'] - $amount;
        $tr['user_name']=$user->user_name;
        $tr['ref']=$ref;
        $tr['code']=$input['service']."_".$input['coded'];
        $tr['server']="server".$server;
        $tr['server_response']="";
        $tr['payment_method']="wallet";
        $tr['transid']=$ref;
        $tr['status']="pending";
        $tr['extra']=$discount;
        $t=Transaction::create($tr);

        $user->wallet -= $amount;
        $user->save();

        $dada['tid']=$t->id;
        $dada['amount']=$amount;
        $dada['discount']=$discount;

        switch ($requester){
            case "airtime":
                return $this->buyAirtimeCTD($request, $ref, $provider, $dada, $server);
            case "data":
                return $this->buyDataCTD($request, $ref, $provider, $dada, $server);
        }
    }

}
