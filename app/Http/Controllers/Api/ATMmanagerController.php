<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Model\PndL;
use App\Model\Serverlog;
use App\Model\Transaction;
use App\User;
use Illuminate\Http\Request;

class ATMmanagerController extends Controller
{
    public function atmfundwallet($fun, $amount, $reference, $payment_method){
        $charge_treshold=2000;
        $charges=50;

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

        Transaction::create($input);

        if($amount<$charge_treshold){
            $input["type"]="income";
            $input["amount"]=$charges;
            $input["narration"]="Being amount charged for funding less than #".$charge_treshold." from ".$u->user_name;

            PndL::create($input);

            $input["description"]="Being amount charged for funding less than #".$charge_treshold;
            $input["name"]="Auto Charge";
            $input["code"]="ac50";
            $input['status']='successful';
            $input["i_wallet"]=$wallet;
            $input["f_wallet"]=$input["i_wallet"] - $charges;

            Transaction::create($input);

            $wallet-=$charges;
        }

        $u->wallet=$wallet;
        $u->save();

        $at=new PushNotificationController();
        $at->PushNoti($u->user_name,"Hi ".$u->user_name.", your wallet has been credited with the sum of ".$amount." via ".$payment_method, "Payment Notification");
    }


    public function atmtransactionserve($id){
        $s=Serverlog::find($id);

        $input['user_name'] =$s->user_name;
        $input['api'] = $s->api;
        $input['coded'] = $s->coded;
        $input['phone'] = $s->phone;
        $input['amount'] = $s->amount;
        $input['transid'] = $s->transid;
        $input['service'] = $s->service;
        $input['network'] = $s->network;
        $input['payment_method'] = $s->payment_method;

        $r= new Request($input);
        if($s->service=="airtime"){
            $t=new ServeRequestController();
            $t->buyairtime($r);
        }

        if($s->service=="data"){
            $t=new ServeRequestController();
            $t->buydata($r);
        }

        if($s->service=="paytv"){
            $at=new PushNotificationController();
            $at->PushNoti($input['user_name'], "Your TV subscription request of ". $input['coded'] ." on ". $input['phone'] ." will be served soon.", "Paytv Transaction" );
        }
    }

}
