<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $input["ip_address"]="127.0.0.1:A";
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

        $this->PushNoti($u->user_name,"Hi ".$u->user_name.", your wallet has been credited with the sum of ".$amount." via ".$payment_method, "Payment Notification");
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
    }

    public function PushNoti($user_name,$message, $title){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\n\"to\": \"/topics/". $user_name."\",\n\"data\": {\n\t\"extra_information\": \"Mega Cheap Data\"\n},\n\"notification\":{\n\t\"title\": \"". $title."\",\n\t\"text\":\"". $message."\"\n\t}\n}\n",
            CURLOPT_HTTPHEADER => array(
                "Authorization: key=AAAAOW0II6E:APA91bHyum5pMhub2JVHcHnQghuWOdktOuhW9e4ZvmMDudjMZk9y1u71Nr7yl_FZLpsjuC6Hz1Fd49OrWfPYNKpAvahAZ5Rjv0y7IW24nqjYrPnMer8IvTkzZFB5W3hrOHAwbq2EOMOE",
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));
        curl_exec($curl);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\n\"to\": \"/topics/samji\",\n\"data\": {\n\t\"extra_information\": \"Mega Cheap Data\"\n},\n\"notification\":{\n\t\"title\": \"". $title."\",\n\t\"text\":\"". $message."\"\n\t}\n}\n",
            CURLOPT_HTTPHEADER => array(
                "Authorization: key=AAAAOW0II6E:APA91bHyum5pMhub2JVHcHnQghuWOdktOuhW9e4ZvmMDudjMZk9y1u71Nr7yl_FZLpsjuC6Hz1Fd49OrWfPYNKpAvahAZ5Rjv0y7IW24nqjYrPnMer8IvTkzZFB5W3hrOHAwbq2EOMOE",
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));
        curl_exec($curl);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\n\"to\": \"/topics/videx\",\n\"data\": {\n\t\"extra_information\": \"Mega Cheap Data\"\n},\n\"notification\":{\n\t\"title\": \"MCD Data Purchase Notification\",\n\t\"text\":\"". $message."\"\n\t}\n}\n",
            CURLOPT_HTTPHEADER => array(
                "Authorization: key=AAAAOW0II6E:APA91bHyum5pMhub2JVHcHnQghuWOdktOuhW9e4ZvmMDudjMZk9y1u71Nr7yl_FZLpsjuC6Hz1Fd49OrWfPYNKpAvahAZ5Rjv0y7IW24nqjYrPnMer8IvTkzZFB5W3hrOHAwbq2EOMOE",
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        echo $response;
    }
}
