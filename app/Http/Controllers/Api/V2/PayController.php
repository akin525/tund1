<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\SellAirtimeController;
use App\Http\Controllers\Api\SellBettingTopup;
use App\Http\Controllers\Api\SellDataController;
use App\Http\Controllers\Api\SellEducationalController;
use App\Http\Controllers\Api\SellElectricityController;
use App\Http\Controllers\Api\SellTVController;
use App\Http\Controllers\Controller;
use App\Jobs\ServeRequestJob;
use App\Models\Airtime2Cash;
use App\Models\Airtime2CashSettings;
use App\Models\AppCableTVControl;
use App\Models\GeneralMarket;
use App\Models\PndL;
use App\Models\PromoCode;
use App\Models\ResellerBetting;
use App\Models\ResellerElecticity;
use App\Models\Serverlog;
use App\Models\Settings;
use App\Models\Transaction;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'payment' => 'required',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];


        if (strtoupper($input['country']) == "NG" || strtoupper($input['country']) == "NIGERIA") {
            $sys = DB::table("tbl_serverconfig_airtime")->where('name', '=', 'airtime')->first();

            $sysD = DB::table("tbl_serverconfig_airtime")->where('name', '=', 'discount')->first();

            switch ($input['provider']) {
                case "MTN":
                    $server = $sys->mtn;
                    $discount = $sysD->mtn;
                    break;

                case "9MOBILE":
                    $server = $sys->etisalat;
                    $discount = $sysD->etisalat;
                    break;

                case "ETISALAT":
                    $server = $sys->etisalat;
                    $discount = $sysD->etisalat;
                    break;

                case "GLO":
                    $server = $sys->glo;
                    $discount = $sysD->glo;
                    break;

                case "AIRTEL":
                    $server = $sys->airtel;
                    $discount = $sysD->airtel;
                    break;

                default:
                    // required field is missing
                    return response()->json(['success' => 0, 'message' => 'Invalid Network. Available are  MTN, 9MOBILE, GLO, AIRTEL.']);
            }


            if ($input['amount'] < 100) {
                return response()->json(['success' => 0, 'message' => 'Minimum amount is #100']);
            }

            if ($input['amount'] > 5000) {
                return response()->json(['success' => 0, 'message' => 'Maximum amount is #5000']);
            }

            $dis = explode("%", $discount);
            $discount = $input['amount'] * ($dis[0] / 100);

        } else {
            $server = 9;
            $discount = 0;
        }

        $debitAmount = $input['amount'];

        $proceed['1'] = $input['provider'];
        $proceed['2'] = $debitAmount;
        $proceed['3'] = $discount;
        $proceed['4'] = $server;
        $proceed['5'] = "airtime";

        return $this->handlePassage($request, $proceed);

//        return $this->debitUser($request, $input['provider'], $debitAmount, $discount, $server, "airtime");

//        return response()->json(['success' => 1, 'message' => 'Airtime Sent Successfully']);

    }

    function buydata(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'coded' => 'required',
            'number' => 'required',
            'payment' => 'required',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];


        $rac = DB::table("tbl_serverconfig_data")->where("coded", strtolower($input['coded']))->first();

        if ($rac == "") {
            return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
        }

        if ($rac->status == 0) {
            return response()->json(['success' => 0, 'message' => $rac->name . ' currently unavailable']);
        }

        $discount = 0;
        $debitAmount = $rac->pricing;

        $proceed['1'] = $rac->network;
        $proceed['2'] = $debitAmount;
        $proceed['3'] = $discount;
        $proceed['4'] = $rac->server;
        $proceed['5'] = "data";

        return $this->handlePassage($request, $proceed);


//        return response()->json(['success' => 1, 'message' => 'Data Sent Successfully']);

    }

    function buytv(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'coded' => 'required',
            'number' => 'required',
            'payment' => 'required',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

        $rac = AppCableTVControl::where("coded", strtolower($input['coded']))->first();

        if ($rac == "") {
            return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
        }

        if ($rac->status == 0) {
            return response()->json(['success' => 0, 'message' => $rac->name . ' currently unavailable']);
        }

        $discount = 10;
        $debitAmount = $rac->price;

        $proceed['1'] = $rac->type;
        $proceed['2'] = $debitAmount * 1;
        $proceed['3'] = $discount;
        $proceed['4'] = $rac->server;
        $proceed['5'] = "tv";

        return $this->handlePassage($request, $proceed);

//        return response()->json(['success' => 1, 'message' => 'TV Subscribe Successfully']);

    }

    function buyelectricity(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'provider' => 'required',
            'number' => 'required',
            'amount' => 'required',
            'payment' => 'required',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

        $rac = ResellerElecticity::where("code", strtolower($input['provider']))->first();

        if ($rac == "") {
            return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
        }

        if ($rac->status == 0) {
            return response()->json(['success' => 0, 'message' => $rac->name . ' currently unavailable']);
        }

        if ($input['amount'] < 1000) {
            return response()->json(['success' => 0, 'message' => 'Minimum amount is #1,000']);
        }

        if ($input['amount'] > 20000) {
            return response()->json(['success' => 0, 'message' => 'Maximum amount is #20,000']);
        }


        $discount = 0;
        $debitAmount = $input['amount'];

        $proceed['1'] = $input['provider'];
        $proceed['2'] = $debitAmount;
        $proceed['3'] = $discount;
        $proceed['4'] = $rac->server;
        $proceed['5'] = "electricity";

        return $this->handlePassage($request, $proceed);

//        return response()->json(['success' => 1, 'message' => 'Electricity Token Generated Successfully', 'token' => 'hfhfwufwf743uewfj48ui']);

    }

    function buybetting(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'provider' => 'required',
            'number' => 'required',
            'amount' => 'required',
            'payment' => 'required',
            'ref' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

        $rac = ResellerBetting::where("code", strtoupper($input['provider']))->first();

        if ($rac == "") {
            return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
        }

        if ($rac->status == 0) {
            return response()->json(['success' => 0, 'message' => $rac->name . ' currently unavailable']);
        }

        if ($input['amount'] < 100) {
            return response()->json(['success' => 0, 'message' => 'Minimum amount is #100']);
        }

        if ($input['amount'] > 5000) {
            return response()->json(['success' => 0, 'message' => 'Maximum amount is #5,000']);
        }


        $discount = 0;
        $debitAmount = $input['amount'];

        $proceed['1'] = $input['provider'];
        $proceed['2'] = $debitAmount;
        $proceed['3'] = $discount;
        $proceed['4'] = $rac->server;
        $proceed['5'] = "betting";

        return $this->handlePassage($request, $proceed);

//        return response()->json(['success' => 1, 'message' => 'Betting topup will reflect soon']);

    }

    function buyJamb(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'provider' => 'required',
            'amount' => 'required',
            'number' => 'required',
            'payment' => 'required',
            'ref' => 'required',
            'coded' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

        $debitAmount = $input['amount'];
        $server = 6;
        $discount = 0;

        $proceed['1'] = $input['provider'];
        $proceed['2'] = $debitAmount;
        $proceed['3'] = $discount;
        $proceed['4'] = $server;
        $proceed['5'] = "jamb";

        return $this->handlePassage($request, $proceed);
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

            $input['device_details'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

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
        $net = $input['type'];

        if ($net == "WAEC") {
            $input['price'] = 1700;
        } else {
            $input['price'] = 800;
        }

        $user = User::where('user_name', $input["user_name"])->first();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User not found']);
        }

        $uid = $input['user_name'];
        $qty = $input['quantity'];
        $price = $input['price'];
        $p = $price * $qty;
        $ref = $input["ref"];
        $input["i_wallet"] = $user->wallet;
        $input['f_wallet'] = $input["i_wallet"] - $p;
        $input['amount'] = $p;

        if ($p > $user->wallet) {
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

        $edu = new SellEducationalController();
        $edu->server1($ref, $input, 'mcd');


//            $at = new PushNotificationController();
//            $at->PushNoti($input['user_name'], "Hi " . $input['user_name'] . ", you will receive your " . $net . " request in your mail soon. Thanks", "Result Checker");

        return response()->json(['success' => 1, 'message' => 'You will receive your request soon']);

    }


    public function buyAirtimeCTD(Request $request, $ref, $net, $dada, $server)
    {
        $input = $request->all();

        $air = new SellAirtimeController();

        if ($input['country'] == 'NG' || $input['country'] == 'Nigeria') {
            switch (strtolower($server)) {
                case "9":
                    return $air->server9($request, $input['amount'], $input['number'], $ref, $net, $request, $dada, "mcd");
                case "6":
                    return $air->server6($request, $input['amount'], $input['number'], $ref, $net, $request, $dada, "mcd");
                case "5":
                    return $air->server5($request, $input['amount'], $input['number'], $ref, $net, $request, $dada, "mcd");
                case "4":
                    return $air->server4($request, $input['amount'], $input['number'], $ref, $net, $request, $dada, "mcd");
                case "3":
                    return $air->server3($request, $input['amount'], $input['number'], $ref, $net, $request, $dada, "mcd");
                case "2":
                    return $air->server2($request, $input['amount'], $input['number'], $ref, $net, $request, $dada, "mcd");
                case "1b":
                    return $air->server1b($request, $input['amount'], $input['number'], $ref, $net, $request, $dada, "mcd");
                case "1":
                    return $air->server1($request, $input['amount'], $input['number'], $ref, $net, $request, $dada, "mcd");
                default:
                    return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
            }
        } else {
            return $air->server9($request, $input['amount'], $input['number'], $ref, $net, $request, $dada, "mcd");
        }
    }

    public function buyDataCTD(Request $request, $ref, $net, $dada, $server)
    {
        $input = $request->all();

        $air = new SellDataController();

        switch (strtolower($server)) {
            case "10":
                return $air->server10($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "8":
                return $air->server8($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "7":
                return $air->server7($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "6":
                return $air->server6($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "3":
                return $air->server3($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "2":
                return $air->server2($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            case "1":
                return $air->server1($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            default:
                return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
        }
    }

    public function buyTvCTD(Request $request, $ref, $net, $dada, $server)
    {
        $input = $request->all();

        $air = new SellTVController();

        switch (strtolower($server)) {
            case "6":
                return $air->server6($request, $input['coded'], $input['number'], $ref, $net, $request, $dada, "mcd");
            default:
                return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
        }
    }

    public function buyElectricityCTD(Request $request, $ref, $net, $dada, $server)
    {
        $input = $request->all();

        $air = new SellElectricityController();

        switch (strtolower($server)) {
            case "6":
                return $air->server6($request, $input['provider'], $input['number'], $ref, $net, $request, $dada, "mcd");
            default:
                return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
        }
    }

    public function buyBettingCTD(Request $request, $ref, $net, $dada, $server)
    {
        $input = $request->all();

        $air = new SellBettingTopup();

        switch (strtolower($server)) {
            case "7":
                return $air->server7($request, $input['provider'], $input['number'], $ref, $input['amount'], $request, $dada, "mcd");
            case "0":
                return $air->server0($request, $input['provider'], $input['number'], $ref, $input['amount'], $request, $dada, "mcd");
            default:
                return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
        }

    }


    public function buyJambCTD(Request $request, $ref, $net, $dada, $server)
    {
        $input = $request->all();

        $air = new SellEducationalController();

        return $air->server6_utme($request, $input['coded'], $input['number'], $ref, $request, $dada, "mcd");

    }


    public function debitUser(Request $request, $provider, $amount, $discount, $server, $requester, $ref)
    {
        $input = $request->all();

        $input['user_name'] = Auth::user()->user_name;
        $input['version'] = $request->header('version');

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'Invalid API key. Kindly contact us on whatsapp@07011223737']);
        }

        if ($amount > $user->wallet) {
            return response()->json(['success' => 0, 'message' => 'Insufficient balance to handle request']);
        }


        if ($requester == "airtime") {
            $tr['name'] = strtoupper($provider) . " " . $requester;
            $tr['description'] = $user->user_name . " purchase " . $input['provider'] . " " . $input['amount'] . " airtime on " . $input['number'] . " using " . $input['payment'];
            $tr['code'] = $requester;
        } elseif ($requester == "electricity" || $requester == "betting") {
            $tr['name'] = strtoupper($provider);
            $tr['description'] = $user->user_name . " pay " . $input['amount'] . " on " . $input['number'] . " using " . $input['payment'];
            $tr['code'] = $requester;
        } else {
            $tr['name'] = $requester;
            $tr['description'] = $user->user_name . " purchase " . " " . $input['coded'] . " on " . $input['number'] . " using " . $input['payment'];
            $tr['code'] = $requester . "_" . $input['coded'];
        }


        if ($input['promo'] != "0") {
            $pc = PromoCode::where('code', $input['promo'])->first();

            if ($pc) {

                $amount -= $pc->amount;

                $tr['description'] .= " with NGN" . $pc->amount . " promo code";

                $input["type"] = "expenses";
                $input["gl"] = "Promo Code";
                $input["amount"] = $pc->amount;
                $input['date'] = Carbon::now();
                $input["narration"] = "Being promo code used by " . $input['user_name'] . " on " . $ref;

                PndL::create($input);

                $pc->used = 1;
                $pc->usedby .= $input['user_name'] . " ";
                $pc->save();
            }
        }

        $tr['amount'] = $amount;
        $tr['date'] = Carbon::now();
        $tr['device_details'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];
        $tr['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $tr['user_name'] = $user->user_name;
        $tr['ref'] = $ref;
        $tr['server'] = "server" . $server;
        $tr['server_response'] = "";
        $tr['payment_method'] = $input['payment'];
        $tr['transid'] = $ref;
        $tr['status'] = "pending";
        $tr['extra'] = $discount;

        if ($input['payment'] == "wallet") {

            $tr['i_wallet'] = $user->wallet;

            if ($requester == "data") {
                $tr['f_wallet'] = $tr['i_wallet'] - $amount - 20;
            } else {
                $tr['f_wallet'] = $tr['i_wallet'] - $amount;
            }

            $user->wallet = $tr['f_wallet'];
            $user->save();

            if ($requester == "data") {
                if ($input['coded'] != "m1") {
                    $set = Settings::where('name', 'general_market')->first();
                    $tr['version'] = $input['version'];
                    $tr['o_wallet'] = $set->value;
                    $tr['n_wallet'] = $tr['o_wallet'] + 5;
                    $tr['type'] = 'credit';
                    GeneralMarket::create($tr);
                    $set->value = $tr['n_wallet'];
                    $set->save();
                }
            }

        } elseif ($input['payment'] == "general_market") {
            $set = Settings::where('name', 'general_market')->first();
            $tr['transid'] = $ref;
            $tr['version'] = $input['version'];
            $tr['i_wallet'] = $set->value;
            $tr['f_wallet'] = $tr['i_wallet'] - $amount;
            $tr['type'] = 'debit';
            GeneralMarket::create($tr);
            $set->value -= $amount;
            $set->save();

            $input["type"] = "expenses";
            $input["gl"] = "General Market";
            $input["amount"] = $amount;
            $input['date'] = Carbon::now();
            $input["narration"] = "Being general market used by " . $input['user_name'] . " on " . $ref;

            PndL::create($input);
        } else {
            $tr['i_wallet'] = $user->wallet;
            $tr['f_wallet'] = $tr['i_wallet'];
        }

        $t = Transaction::create($tr);

        if ($input['payment'] == "wallet") {
            if ($discount > 0) {
                $tr['name'] = "Commission";
                $tr['description'] = "MCD Commission on " . $ref;
                $tr['code'] = "tcommission";
                $tr['amount'] = $discount;
                $tr['status'] = "successful";
                $tr['i_wallet'] = $user->agent_commision;
                $tr['f_wallet'] = $tr['i_wallet'] + $discount;
                Transaction::create($tr);

                $user->agent_commision = $tr['f_wallet'];
                $user->save();
            }
        }

        $input["service"] = $requester;
        $job = (new ServeRequestJob($input, "1", $tr, $user->id))
            ->delay(Carbon::now()->addSeconds(1));
        dispatch($job);

        $dada['tid'] = $t->id;
        $dada['amount'] = $amount;
        $dada['discount'] = $discount;

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
            case "jamb":
                return $this->buyJambCTD($request, $ref, $provider, $dada, $server);
        }
    }

    public function handlePassage($request, $proceed)
    {
        $input = $request->all();
        $input['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $input['date'] = Carbon::now();
        $input['phone'] = $input['number'];
        $input['user_name'] = Auth::user()->user_name;
        $input['payment_method'] = $input['payment'];
//        $input['transid'] = "MCD_" . substr($input['user_name'], -3, 2) . "_" . Carbon::now()->timestamp . rand();
        $input['transid'] = $input['ref'];
        $input['version'] = $request->header('version');
        $input['device_details'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];
        $input['wallet'] = Auth::user()->wallet;
        $input['amount'] = $proceed['2'];
        $input["service"] = $proceed['5'];

        $re = Serverlog::where('transid', $input['transid'])->first();

        if ($re) {
            $input['status'] = 'Duplicate reference';
            $input['transid'] = $input['transid'] . '_dup';
            Serverlog::create($input);
            return response()->json(['success' => 0, 'message' => 'Error, duplicate transaction']);
        }


        if (isset($input['provider'])) {
            $input['network'] = $input['provider'];
        }

        $users = User::where("user_name", "=", $input['user_name'])->first();
        if (!$users) {
            $input['status'] = 'Username does not exist';
            Serverlog::create($input);
            return response()->json(['success' => 0, 'message' => 'Error, invalid user name']);
        }

        if ($input['payment'] == "general_market") {
            $set = Settings::where('name', 'general_market')->first();

            if ($set->value >= $input['amount']) {

                if ($input['amount'] > env("GMARKET_SINGLE_USAGE_LIMIT")) {
                    $input['status'] = 'Excessive usage';
                    Serverlog::create($input);
                    return response()->json(['success' => 0, 'message' => 'Excessive usage detected, kindly reduce purchase to ' . env("GMARKET_SINGLE_USAGE_LIMIT")]);
                }

                Serverlog::create($input);
                return $this->debitUser($request, $proceed['1'], $proceed['2'], $proceed['3'], $proceed['4'], $proceed['5'], $input['ref']);
            }

            $input['status'] = 'general market is low';
            Serverlog::create($input);
            return response()->json(['success' => 0, 'message' => 'General market balance is low']);
        }

        if ($input['payment'] != "wallet" && $input['payment'] != "general_market") {
            $input['status'] = 'pending';
            Serverlog::create($input);
            return response()->json(['success' => 1, 'message' => 'Transaction executed successfully', 'ref' => $input['transid']]);
        }

        $user = User::where('user_name', $input['user_name'])->first();

        if ($user->wallet <= 0) {
            $input['status'] = 'Balance too low';
            Serverlog::create($input);
            return response()->json(['success' => 0, 'message' => 'Error, wallet balance too low']);
        }
        if ($input['amount'] > $user->wallet) {
            $input['status'] = 'Balance too low';
            Serverlog::create($input);
            return response()->json(['success' => 0, 'message' => 'Error, wallet balance too low']);
        }

        $lasttime = Serverlog::where('user_name', $input['user_name'])->orderBy('id', 'desc')->first();

        if ($lasttime) {
            $t = Carbon::parse($lasttime->date)->diffInSeconds(Carbon::now(), false);

            if ($t <= 15 && !($t < 0)) {
                $input['status'] = 'Suspect Fraud';
                Serverlog::create($input);
                $user = User::where('user_name', $input['user_name'])->first();
                $user->wallet -= $input['amount'];
                $user->save();
                return response()->json(['success' => 0, 'message' => 'Suspect Fraud']);
            }
        }

        Serverlog::create($input);
        return $this->debitUser($request, $proceed['1'], $proceed['2'], $proceed['3'], $proceed['4'], $proceed['5'], $input['ref']);
//        return $next($request);
    }

    public function outputResp(Request $request, $ref, $status, $dada)
    {

        if ($status == 1) {
            $t = Transaction::find($dada['tid']);
            $t->status = "delivered";
            $t->server_response = $dada['server_response'];
            $t->server_ref = $dada['server_ref'] ?? '';
            $t->save();

            if (isset($dada['token'])) {
                $t->description .= " - " . $dada['token'];
                $t->save();
                return response()->json(['success' => 1, 'message' => 'Your transaction was successful', 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount'], 'token' => $dada['token']]);
            }
            return response()->json(['success' => 1, 'message' => 'Your transaction is in progress', 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount']]);
        }

        $t = Transaction::find($dada['tid']);
        $t->server_response = $dada['server_response'];
        $t->save();

        if (isset($dada['token'])) {
            return response()->json(['success' => 1, 'message' => 'Your transaction was successful', 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount'], 'token' => $dada['token']]);
        }

        return response()->json(['success' => 1, 'message' => 'Your transaction is in progress', 'ref' => $ref, 'debitAmount' => $dada['amount'], 'discountAmount' => $dada['discount']]);
    }


}
