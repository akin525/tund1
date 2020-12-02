<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Serverlog;
use App\Model\Settings;
use App\Model\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaystackHookController extends Controller
{
    public function index(Request $request){
        $input = $request->all();

        $data2= json_encode($input);

        echo "52.31.139.75, 52.49.173.169, 52.214.14.220<br/>";

        DB::table('tbl_webhook_paystack')->insert(['payment_reference'=> $input['data']['reference'], 'payment_id'=>$input['data']['id'], 'status'=>$input['data']['status'], 'amount'=>$input['data']['amount'], 'fees'=> $input['data']['fees'], 'customer_code'=>$input['data']['customer']['customer_code'], 'email'=>$input['data']['customer']['email'], 'paystack_signature'=> $request->header('X-Paystack-Signature'), 'paid_at'=>$input['data']['paidAt'], 'channel'=>$input['data']['channel'], 'remote_address'=>$_SERVER['REMOTE_ADDR'], 'extra'=>$data2]);

        // only a post with paystack signature header gets our attention
        if (!$request->headers->has('X-Paystack-Signature')) {
            return "invalid request";
        }

        if($input['event']!="charge.success"){
            return "charge->success expected";
        }
        $domain=$input['data']['domain'];
        $status=$input['data']['status'];
        $reference=$input['data']['reference'];
        $amount=$input['data']['amount']/100;

        if($domain!="live"){
            return "demo env";
        }

        if($status!="success"){
            return "Success status expected";
        }

        $tra=Serverlog::where('transid',$reference)->first();
        if($tra){
            if ($tra->status!="completed") {
                $tra->status = 'completed';
                $tra->save();

                $atm=new ATMmanagerController();
                $atm->atmtransactionserve($tra->id);
            }
        }

        $fun=Wallet::where('ref',$reference)->first();
        if($fun){
            if ($fun->status!="completed") {
                $fun->status='completed';
                $fun->save();

                $at=new ATMmanagerController();
                $at->atmfundwallet($fun, $amount, $reference, "Paystack", $input['data']['fees']/100);
            }
        }

        return "success";
    }
}
