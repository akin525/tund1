<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\Reseller\PayController;

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

        $at = new PushNotificationController();
        $at->PushNotiAdmin($response, "Ghana Airtime response");

        if ($rep['status'] == 'success') {
            $tran->addtrans("server5", $response, $amnt, 1, $transid, $input);
        } else {
            $tran->addtrans("server5", $response, $amnt, 0, $transid, $input);
        }
    }

    public function server4($request, $amnt, $phone, $transid, $net, $input, $dada, $requester)
    {

        $service_id = "0";

        switch ($net) {
            case "MTN":
                $service_id = "7";
                break;

            case "9MOBILE":
                $service_id = "9";
                break;

            case "GLO":
                $service_id = "8";
                break;

            case "AIRTEL":
                $service_id = "6";
                break;

            default:
                return response()->json(['success' => 0, 'message' => 'Invalid Network. Available are m for MTN, 9 for 9MOBILE, g for GLO, a for AIRTEL.']);
        }


        if (env('FAKE_TRANSACTION', 1) == 0) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => env("SERVER4") . "/users/account/authenticate",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => env("SERVER4_AUTH"),
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Content-Type: text/plain"
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response, true);
            echo $response;
            $token = $response['token'];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => env("SERVER4") . "/bills/pay/airtime",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{\n  \"amount\": \"" . $amnt . "\",\n  \"service_category_id\": \"" . $service_id . "\",\n  \"phonenumber\": \"" . $phone . "\",\n  \"status_url\": \"https://superadmin.mcd.5starcompany.com.ng/api/hook\"\n}",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: " . $token,
                    "Content-Type: application/json",
                    "Content-Type: text/plain"
                ),
            ));

            $response = curl_exec($curl);

            echo $response;

            curl_close($curl);

        } else {

            $response = '{"code":"000","content":{"transactions":{"status":"delivered","product_name":"MTN Airtime VTU","unique_element":"08166939205","unit_price":100,"quantity":1,"service_verification":null,"channel":"api","commission":3,"total_amount":97,"discount":null,"type":"Airtime Recharge","email":"odejinmisamuel@gmail.com","phone":"08166939205","name":null,"convinience_fee":0,"amount":100,"platform":"api","method":"api","transactionId":"16286982315467608027176693"}},"response_description":"TRANSACTION SUCCESSFUL","requestId":"R16286982281950119922","amount":"100.00","transaction_date":{"date":"2021-08-11 17:10:31.000000","timezone_type":3,"timezone":"Africa\/Lagos"},"purchased_code":""}';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();

        $dada['server_response'] = $response;

        if ($rep['status'] == 'success') {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $response['transaction']['_id'], 1, $dada);
            } else {
                $tran->addtrans("server4", $response, $amnt, 1, $response['transaction']['_id'], $input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $response['transaction']['_id'], 0, $dada);
            } else {
                $tran->addtrans("server4", $response, $amnt, 1, $response['transaction']['_id'], $input);
            }
        }
    }

    public function server6($request, $amnt, $phone, $transid, $net, $input, $dada, $requester)
    {

        $netcode = "0";

        switch ($net) {
            case "9MOBILE":
                $netcode = "etisalat";
            default:
                $netcode = strtolower($net);
        }


        if (env('FAKE_TRANSACTION', 1) == 0) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('SERVER6') . "pay",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{"request_id": "' . $transid . '", "serviceID": "' . $netcode . '","amount": "' . $amnt . '","phone": "' . $phone . '"}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('SERVER6_AUTH'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);
        } else {

            $response = '{"code":"000","content":{"transactions":{"status":"delivered","product_name":"MTN Airtime VTU","unique_element":"08166939205","unit_price":100,"quantity":1,"service_verification":null,"channel":"api","commission":3,"total_amount":97,"discount":null,"type":"Airtime Recharge","email":"odejinmisamuel@gmail.com","phone":"08166939205","name":null,"convinience_fee":0,"amount":100,"platform":"api","method":"api","transactionId":"16286982315467608027176693"}},"response_description":"TRANSACTION SUCCESSFUL","requestId":"R16286982281950119922","amount":"100.00","transaction_date":{"date":"2021-08-11 17:10:31.000000","timezone_type":3,"timezone":"Africa\/Lagos"},"purchased_code":""}';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();

        $dada['server_response'] = $response;

        if ($rep['code'] == '000') {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                $tran->addtrans("server6", $response, $amnt, 1, $transid, $input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            }else{
                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }
    }
}
