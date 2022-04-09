<?php

namespace App\Jobs;

use App\Http\Controllers\Api\SellAirtimeController;
use App\Http\Controllers\Api\SellDataController;
use App\Http\Controllers\Api\SellElectricityController;
use App\Http\Controllers\Api\SellTVController;
use App\Models\AppCableTVControl;
use App\Models\ResellerElecticity;
use App\Models\Serverlog;
use App\Models\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ATMtransactionserveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $id;
    public function __construct($id)
    {
        $this->id=$id;
    }

    /**
     * Execute the job.
     *
     * @return JsonResponse
     */
    public function handle()
    {
        $s=Serverlog::find($this->id);

        $input['user_name'] = $s->user_name;
        $input['api'] = $s->api;
        $input['coded'] = $s->coded;
        $input['phone'] = $s->phone;
        $input['amount'] = $s->amount;
        $input['transid'] = $s->transid;
        $input['service'] = $s->service;
        $input['network'] = $s->network;
        $input['payment_method'] = $s->payment_method;
        $input['version'] = $s->version;
        $input['ip_address'] = $s->ip_address;

        $discount = 0;
        $server = 0;

        $user = User::where("user_name", $input['user_name'])->first();

        if ($input['service'] == "airtime") {
            $tr['name'] = strtoupper($input['network']) . " " . $input['service'];
            $tr['description'] = $user->user_name . " purchase " . $input['network'] . " " . $input['amount'] . " airtime on " . $input['phone'] . " using " . $input['payment_method'];
            $tr['code'] = $input['service'];
        } elseif ($input['service'] == "electricity" || $input['service'] == "betting") {
            $tr['name'] = strtoupper($input['service']);
            $tr['description'] = $user->user_name . " pay " . $input['amount'] . " on " . $input['phone'] . " using " . $input['payment_method'];
            $tr['code'] = $input['service'];
        } else {
            $tr['name'] = $input['service'];
            $tr['description'] = $user->user_name . " purchase " . " " . $input['coded'] . " on " . $input['phone'] . " using " . $input['payment_method'];
            $tr['code'] = $input['service'] . "_" . $input['coded'];
        }


        $tr['amount'] = $input['amount'];
        $tr['date'] = Carbon::now();
        $tr['device_details'] = "api";
        $tr['ip_address'] = $input['ip_address'];
        $tr['i_wallet'] = $user->wallet;
        $tr['f_wallet'] = $tr['i_wallet'];
        $tr['user_name'] = $user->user_name;
        $tr['ref'] = $input['transid'];
        $tr['code'] = $input['service'] . "_" . $input['coded'];
        $tr['server'] = "server" . $server;
        $tr['server_response'] = "";
        $tr['payment_method'] = $input['payment_method'];
        $tr['status'] = "pending";
        $tr['extra'] = $discount;
        $t = Transaction::create($tr);

        $dada['tid'] = $t->id;
        $dada['amount'] = $input['amount'];
        $dada['discount'] = $discount;

        $r = new Request($input);
        if ($s->service == "airtime") {

            $sys = DB::table("tbl_serverconfig_airtime")->where('name', '=', 'airtime')->first();

            switch ($input['network']) {
                case "MTN":
                    $server = $sys->mtn;
                    break;

                case "9MOBILE":
                    $server = $sys->etisalat;
                    break;

                case "ETISALAT":
                    $server = $sys->etisalat;
                    break;

                case "GLO":
                    $server = $sys->glo;
                    break;

                case "AIRTEL":
                    $server = $sys->airtel;
                    break;

                default:
                    // required field is missing
                    return response()->json(['success' => 0, 'message' => 'Invalid Network. Available are  MTN, 9MOBILE, GLO, AIRTEL.']);
            }

            $t->server = "server" . $server;
            $t->save();


            $air = new SellAirtimeController();

            switch (strtolower($server)) {
                case "9":
                    return $air->server9($r, $input['amount'], $input['phone'], $input['transid'], $input['network'], $input, $dada, "mcd");
                case "6":
                    return $air->server6($r, $input['amount'], $input['phone'], $input['transid'], $input['network'], $input, $dada, "mcd");
                case "5":
                    return $air->server5($r, $input['amount'], $input['phone'], $input['transid'], $input['network'], $input, $dada, "mcd");
                case "4":
                    return $air->server4($r, $input['amount'], $input['phone'], $input['transid'], $input['network'], $input, $dada, "mcd");
                case "3":
                    return $air->server3($r, $input['amount'], $input['phone'], $input['transid'], $input['network'], $input, $dada, "mcd");
                case "2":
                    return $air->server2($r, $input['amount'], $input['phone'], $input['transid'], $input['network'], $input, $dada, "mcd");
                case "1b":
                    return $air->server1b($r, $input['amount'], $input['phone'], $input['transid'], $input['network'], $input, $dada, "mcd");
                case "1":
                    return $air->server1($r, $input['amount'], $input['phone'], $input['transid'], $input['network'], $input, $dada, "mcd");
                default:
                    return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
            }
        }

        if ($s->service == "data") {

            $rac = DB::table("tbl_serverconfig_data")->where("coded", strtolower($input['coded']))->first();

            if ($rac == "") {
                return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
            }

            $t->server = "server" . $rac->server;
            $t->save();

            $air = new SellDataController();

            switch (strtolower($rac->server)) {
                case "10":
                    return $air->server10($r, $input['coded'], $input['phone'], $input['transid'], $rac->network, $input, $dada, "mcd");
                case "8":
                    return $air->server8($r, $input['coded'], $input['phone'], $input['transid'], $rac->network, $input, $dada, "mcd");
                case "7":
                    return $air->server7($r, $input['coded'], $input['phone'], $input['transid'], $rac->network, $input, $dada, "mcd");
                case "6":
                    return $air->server6($r, $input['coded'], $input['phone'], $input['transid'], $rac->network, $input, $dada, "mcd");
                case "3":
                    return $air->server3($r, $input['coded'], $input['phone'], $input['transid'], $rac->network, $input, $dada, "mcd");
                case "2":
                    return $air->server2($r, $input['coded'], $input['phone'], $input['transid'], $rac->network, $input, $dada, "mcd");
                case "1":
                    return $air->server1($r, $input['coded'], $input['phone'], $input['transid'], $rac->network, $input, $dada, "mcd");
                default:
                    return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
            }
        }

        if ($s->service == "tv") {

            $rac = AppCableTVControl::where("coded", strtolower($input['coded']))->first();

            if ($rac == "") {
                return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
            }

            $t->server = "server" . $rac->server;
            $t->save();

            $air = new SellTVController();

            switch (strtolower($rac->server)) {
                case "6":
                    return $air->server6($r, $input['coded'], $input['phone'], $input['transid'], $rac->network, $input, $dada, "mcd");
                default:
                    return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
            }

        }

        if ($s->service == "electricity") {

            $rac = ResellerElecticity::where("code", strtolower($input['network']))->first();

            if ($rac == "") {
                return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
            }

            $t->server = "server" . $rac->server;
            $t->save();

            $air = new SellElectricityController();

            switch (strtolower($rac->server)) {
                case "6":
                    return $air->server6($r, $input['network'], $input['phone'], $input['transid'], $input['network'], $input, $dada, "mcd");
                default:
                    return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
            }

        }

    }
}
