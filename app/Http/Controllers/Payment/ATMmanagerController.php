<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Jobs\ATMtransactionserveJob;
use App\Jobs\NewAccountGiveaway;
use App\Jobs\PushNotificationJob;
use App\Mail\TransactionNotificationMail;
use App\Models\PndL;
use App\Models\Settings;
use App\Models\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ATMmanagerController extends Controller
{
    public function atmfundwallet($fun, $amount, $reference, $payment_method, $cfee){
        $data=Settings::where('name','funding_charges')->first();
        $charges=$data->value;

        $u=User::where('user_name', '=', $fun->user_name)->first();

        $input['name']="wallet funding";
        $input['amount']=$amount;
        $input['status']='successful';
        $input['description']= $u->user_name .' wallet funded using '.$payment_method.' with the sum of #'.$amount .' with ref->'.$reference;
        $input['user_name']=$u->user_name;
        $input['code']='afund_'.$payment_method;
        $input['i_wallet']=$u->wallet;
        $wallet=$u->wallet + $amount;
        $input['f_wallet']=$wallet;
        $input['ref']=$reference;
        $input["ip_address"]=$_SERVER['REMOTE_ADDR'].":A";
        $input["date"]=date("y-m-d H:i:s");

        $tr=Transaction::create($input);

        if($charges > 0){
            $input["type"]="income";
//            $input["gl"]=$payment_method;
            $input["gl"]="funding_charges";
            $input["amount"]=$charges;
            $input["narration"]="Being amount charged for funding on ".$reference." from ".$u->user_name;

            PndL::create($input);

            $input["description"]="Being amount charged for funding on ".$reference;
            $input["name"]="Auto Charge";
            $input["code"]="ac50";
            $input['status']='successful';
            $input["i_wallet"]=$wallet;
            $input["f_wallet"]=$input["i_wallet"] - $charges;

            Transaction::create($input);

            $wallet-=$charges;
        }

        if($cfee!=0){
            $input["type"] = "expenses";
            $input["gl"] = $payment_method;
            $input["amount"] = $cfee;
            $input["narration"] = "Payment gateway charges on " . $reference;

            PndL::create($input);
        }

        $u->wallet = $wallet;
        $u->save();

        PushNotificationJob::dispatch($u->user_name, "Hi " . $u->user_name . ", your wallet has been credited with the sum of " . $amount . " via " . $payment_method, "Payment Notification");

        Mail::to($u->email)->send(new TransactionNotificationMail($tr));

    }


    public function atmtransactionserve($id){
        echo "launching ATM transaction job";
        $job = (new ATMtransactionserveJob($id))
            ->delay(Carbon::now()->addSeconds(2));
        dispatch($job);
    }

}
