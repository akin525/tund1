<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Serverlog;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayantHookController extends Controller
{
    public function index(Request $request)
    {

        $input = $request->all();

        $data2 = json_encode($input);


        DB::table('test')->insert(['name' => 'webhook', 'request' => $request, 'data2' => $data2]);

        return "success";


        DB::table('tbl_webhook_rave')->insert(['payment_reference' => $input['data']['tx_ref'], 'rave_reference' => $input['data']['flw_ref'], 'status' => $input['data']['status'], 'amount' => $input['data']['amount'], 'fees' => $input['data']['app_fee'], 'charged_amount' => $input['data']['charged_amount'], 'customer_id' => $input['data']['customer']['id'], 'email' => $input['data']['customer']['email'], 'rave_signature' => $request->header('Verif-Hash'), 'paid_at' => $input['data']['created_at'], 'type' => $input['data']['payment_type'], 'remote_address' => $_SERVER['REMOTE_ADDR'], 'extra' => $data2]);


// retrieve the signature sent in the reques header's.
        $signature = (isset($_SERVER['HTTP_VERIF_HASH']) ? $_SERVER['HTTP_VERIF_HASH'] : '');

        /* It is a good idea to log all events received. Add code *
         * here to log the signature and body to db or file       */

        if (!$signature) {
            // only a post with Flutterwave signature header gets our attention
            echo "does not have signature";
            exit();
        }

// Store the same signature on your server as an env variable and check against what was sent in the headers
        $local_signature = env('RAVE_SECRET_HASH');

// confirm the event's signature
        if ($signature !== $local_signature) {
            // silently forget this ever happened
            echo "signature does not match";
            exit();
        }


        if ($input['event'] != "charge.completed") {
            return "charge->success expected";
        }
        $status = $input['data']['status'];
        $reference = $input['data']['tx_ref'];
        $amount = $input['data']['amount'];

        if ($status != "successful") {
            return "Success status expected";
        }

        $fee = $input['data']['charged_amount'] - $input['data']['app_fee'] - $input['data']['merchant_fee'];
        $cfee = $input['data']['amount'] - $fee;

        $tra = Serverlog::where('transid', $reference)->first();
        if ($tra) {
            if ($tra->status != "completed") {
                $tra->status = 'completed';
                $tra->save();

                $atm = new ATMmanagerController();
                $atm->atmtransactionserve($tra->id);
            }
        }

        $fun = Wallet::where('ref', $reference)->first();
        if ($fun) {
            if ($fun->status != "completed") {
                $fun->status = 'completed';
                $fun->save();

                $at = new ATMmanagerController();
                $at->atmfundwallet($fun, $amount, $reference, "Rave", $cfee);
            }
        }

        return "success";
    }

    public function verify(Request $request)
    {

        $input = $request->all();

        $data2 = json_encode($input);

        DB::table('test')->insert(['name' => 'webhook', 'request' => $request, 'data2' => $data2]);

        if ($input['type'] === 'subscribe' && $input['verify_token'] === env("PAYANT_VERIFY_TOKEN")) {
            return $input['challenge'];
        } else {
            return "Token validation failed. Make sure the validation tokens match.";
        }
    }
}
