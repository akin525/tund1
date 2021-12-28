<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Api\SellAirtimeController;
use App\Http\Controllers\Api\SellDataController;
use App\Http\Controllers\Api\SellElectricityController;
use App\Http\Controllers\Api\SellTVController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Models\ResellerAirtimeControl;
use App\Models\ResellerBetting;
use App\Models\ResellerCableTV;
use App\Models\ResellerDataPlans;
use App\Models\ResellerElecticity;
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
        $discount = $input['amount'] * ($dis[0] / 100);
        $debitAmount = $input['amount'];


        return $this->debitReseller($request, $rac->network, $debitAmount, $discount, $rac->server, "airtime");
    }

    public function buyAirtimeCTD(Request $request, $ref, $net, $dada, $server){
        $input=$request->all();

        $air=new SellAirtimeController();

        switch (strtolower($server)) {
            case "6":
                return $air->server6($request, $input['amount'], $input['phone'], $ref, $net, $request, $dada, "reseller");
            case "5":
                return $air->server5($request, $input['amount'], $input['phone'], $ref, $net, $request, $dada, "reseller");
            case "4":
                return $air->server4($request, $input['amount'], $input['phone'], $ref, $net, $request, $dada, "reseller");
            case "3":
                return $air->server3($request, $input['amount'], $input['phone'], $ref, $net, $request, $dada, "reseller");
            case "2":
                return $air->server2($request, $input['amount'], $input['phone'], $ref, $net, $request, $dada, "reseller");
            case "1b":
                return $air->server1b($request, $input['amount'], $input['phone'], $ref, $net, $request, $dada, "reseller");
            case "1":
                return $air->server1($request, $input['amount'], $input['phone'], $ref, $net, $request, $dada, "reseller");
            default:
                return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
        }
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
        $discount = $rac->amount * ($dis[0] / 100);
        $debitAmount = $rac->amount;


        return $this->debitReseller($request, $rac->type, $debitAmount, $discount, $rac->server, "data");
    }

    public function buyDataCTD(Request $request, $ref, $net, $dada, $server){
        $input=$request->all();

        $air=new SellDataController();

        switch (strtolower($server)) {
            case "8":
                return $air->server8($request, $input['coded'], $input['phone'], $ref, $net, $request, $dada, "reseller");
            case "6":
                return $air->server6($request, $input['coded'], $input['phone'], $ref, $net, $request, $dada, "reseller");
            case "3":
                return $air->server3($request, $input['coded'], $input['phone'], $ref, $net, $request, $dada, "reseller");
            case "1":
                return $air->server1($request, $input['coded'], $input['phone'], $ref, $net, $request, $dada, "reseller");
            default:
                return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
        }
    }


    public function buyTV(Request $request)
    {
        $input = $request->all();

        $rac = ResellerCableTV::where("code", strtolower($input['coded']))->first();

        if ($rac == "") {
            return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
        }

        if ($rac->status == 0) {
            return response()->json(['success' => 0, 'message' => $rac->name . ' currently unavailable']);
        }

        $dis = explode("%", $rac->discount);
        $discount = $rac->amount * ($dis[0] / 100);
        $debitAmount = $rac->amount;


        return $this->debitReseller($request, $rac->type, $debitAmount, $discount, $rac->server, "tv");
    }

    public function buyTvCTD(Request $request, $ref, $net, $dada, $server)
    {
        $input = $request->all();

        $air = new SellTVController();

        switch (strtolower($server)) {
            case "6":
                return $air->server6($request, $input['coded'], $input['phone'], $ref, $net, $request, $dada, "reseller");
            default:
                return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
        }
    }


    public function buyElectricity(Request $request)
    {
        $input = $request->all();

        $rac = ResellerElecticity::where("code", strtolower($input['coded']))->first();

        if ($rac == "") {
            return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
        }

        if ($rac->status == 0) {
            return response()->json(['success' => 0, 'message' => $rac->name . ' currently unavailable']);
        }


        if ($input['amount'] < 100) {
            return response()->json(['success' => 0, 'message' => 'Minimum amount is #100']);
        }

        if ($input['amount'] > 20000) {
            return response()->json(['success' => 0, 'message' => 'Maximum amount is #20,000']);
        }


        $dis = explode("%", $rac->discount);
        $discount = $input['amount'] * ($dis[0] / 100);
        $debitAmount = $input['amount'];


        return $this->debitReseller($request, $rac->type, $debitAmount, $discount, $rac->server, "electricity");
    }

    public function buyElectricityCTD(Request $request, $ref, $net, $dada, $server)
    {
        $input = $request->all();

        $air = new SellElectricityController();

        switch (strtolower($server)) {
            case "6":
                return $air->server6($request, $input['coded'], $input['phone'], $ref, $net, $request, $dada, "reseller");
            default:
                return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
        }
    }

    public function buyBetting(Request $request)
    {
        $input = $request->all();

        $rac = ResellerBetting::where("code", strtolower($input['coded']))->first();

        if ($rac == "") {
            return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
        }

        if ($rac->status == 0) {
            return response()->json(['success' => 0, 'message' => $rac->name . ' currently unavailable']);
        }


        if ($input['amount'] < 100) {
            return response()->json(['success' => 0, 'message' => 'Minimum amount is #100']);
        }

        if ($input['amount'] > 20000) {
            return response()->json(['success' => 0, 'message' => 'Maximum amount is #20,000']);
        }


        $dis = explode("%", $rac->discount);
        $discount = $input['amount'] * ($dis[0] / 100);
        $debitAmount = $input['amount'];


        return $this->debitReseller($request, "Betting TopUp", $debitAmount, $discount, $rac->server, "betting");
    }

    public function buyBettingCTD(Request $request, $ref, $net, $dada, $server)
    {
        $input = $request->all();

        $message = "Betting: " . $input['coded'] . "|#" . $input['amount'] . "|" . $input['phone'];

        $push = new PushNotificationController();
        $push->PushNotiAdmin($message, "Reseller Notification");

        $dada['server_response'] = "manual";

        return $this->outputResponse($request, $ref, 0, $dada);
    }


    public function debitReseller(Request $request, $provider, $amount, $discount, $server, $requester)
    {
        $input = $request->all();

        $key = $request->header('Authorization');

        $user = User::where("api_key", $key)->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'Invalid API key. Kindly contact us on whatsapp@07011223737']);
        }

        if ($amount > $user->wallet) {
            return response()->json(['success' => 0, 'message' => 'Insufficient balance to handle request']);
        }

        if (isset($input['reseller_price'])) {
            $discount += (floatval($input['reseller_price']) - floatval($amount));
            $amount += (floatval($input['reseller_price']) - floatval($amount));
        }

        $ref = "R" . Carbon::now()->timestamp . rand();

        if ($requester == "airtime") {
            $tr['name'] = strtoupper($provider) . $input['service'];
            $tr['description'] = "Resell " . strtoupper($provider) . $input['service'] . " of " . $input['amount'] . " on " . $input['phone'];
        } else {
            $tr['name'] = strtoupper($provider);
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
        $tr['transid'] = $ref;
        $tr['status'] = "pending";
        $tr['extra'] = $discount;
        $t=Transaction::create($tr);

        $user->wallet -= $amount;
        $user->agent_commision += $discount;
        $user->save();

        $dada['tid']=$t->id;
        $dada['amount']=$amount;
        $dada['discount']=$discount;

        switch ($requester) {
            case "airtime":
                return $this->buyAirtimeCTD($request, $ref, $provider, $dada, $server);
            case "data":
                return $this->buyDataCTD($request, $ref, $provider, $dada, $server);
            case "tv":
                return $this->buyTvCTD($request, $ref, $provider, $dada, $server);
            case "electricity":
                return $this->buyElectricityCTD($request, $ref, $provider, $dada, $server);
            case "betting":
                return $this->buyBettingCTD($request, $ref, $provider, $dada, $server);
        }
    }

    public function outputResponse(Request $request, $ref, $status, $dada)
    {

        if ($status == 1) {
            $t = Transaction::find($dada['tid']);
            $t->status = "delivered";
            $t->server_response = $dada['server_response'];
            $t->save();

            if (isset($dada['token'])) {
                $t->description .= " - " . $dada['token'];
                $t->save();
                return response()->json(['success' => 1, 'message' => 'Transaction Successful instantly', 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount'], 'token' => $dada['token']]);
            }
            return response()->json(['success' => 1, 'message' => 'Transaction Successful instantly', 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount']]);
        }

        $t = Transaction::find($dada['tid']);
        $t->server_response = $dada['server_response'];
        $t->save();

        if (isset($dada['token'])) {
            return response()->json(['success' => 1, 'message' => 'Transaction Successful instantly', 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount'], 'token' => $dada['token']]);
        }

        return response()->json(['success' => 1, 'message' => 'Transaction is pending', 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount']]);
    }

}
