<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\Reseller\PayController;
use Illuminate\Http\Request;

class SellAirtimeController extends Controller
{
    public function server5($amnt, $phone, $transid, $input){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('SERVER5'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
   "country": "NG",
   "customer": "'.$phone.'",
   "amount": '.$amnt.',
   "recurrence": "ONCE",
   "type": "AIRTIME",
   "reference": "'.$transid.'"
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' .env('RAVE_SECRET_KEY'),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $rep=json_decode($response, true);

        $tran=new ServeRequestController();

        if($rep['status']=='success'){
            $tran->addtrans("server5",$response,$amnt,1,$transid,$input);
        }else {
            $tran->addtrans("server5",$response,$amnt,0,$transid,$input);
        }
    }

    public function ghanaAirtime($amnt, $phone, $transid, $input){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('SERVER5'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
   "country": "GH",
   "customer": "'.$phone.'",
   "amount": '.$amnt.',
   "recurrence": "ONCE",
   "type": "AIRTIME",
   "reference": "'.$transid.'"
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' .env('RAVE_SECRET_KEY'),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $rep=json_decode($response, true);

        $tran=new ServeRequestController();

        $at=new PushNotificationController();
        $at->PushNotiAdmin($response, "Ghana Airtime response");

        if($rep['status']=='success'){
            $tran->addtrans("server5",$response,$amnt,1,$transid,$input);
        }else {
            $tran->addtrans("server5",$response,$amnt,0,$transid,$input);
        }
    }

    public function server6($request, $amnt, $phone, $transid, $net, $input, $dada, $requester){

//        $curl = curl_init();
//
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => env('SERVER6')."pay",
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_POSTFIELDS =>'{
//   "request_id": "'.$transid.'",
//   "serviceID": "'.$net.'",
//   "amount": '.$amnt.',
//   "phone": "'.$phone.'"
//}',
//            CURLOPT_HTTPHEADER => array(
//                'Authorization: Bearer ' .env('SERVER6_AUTH'),
//                'Content-Type: application/json'
//            ),
//        ));
//
//        $response = curl_exec($curl);
//
//        curl_close($curl);

        $response='{ "code":"001", "response_description":"TRANSACTION SUCCESSFUL", "requestId":"SAND0192837465738253A1HSD", "transactionId":"1563873435424", "amount":"50.00", "transaction_date":{ "date":"2019-07-23 10:17:16.000000", "timezone_type":3, "timezone":"Africa/Lagos" }, "purchased_code":"" }';

        $rep=json_decode($response, true);

        $tran=new ServeRequestController();
        $rs=new PayController();

        if($rep['code']=='000'){
            if($requester == "reseller"){
                return $rs->buyAirtimeOutput($request, $transid, 1, $dada);
            }else{
                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }else {
            if($requester == "reseller"){
                return $rs->buyAirtimeOutput($request, $transid, 0, $dada);
            }else{
                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }
    }
}
