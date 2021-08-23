<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reseller\PayController;
use App\Models\ResellerDataPlans;

class SellDataController extends Controller
{

    public function server1($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        $netcode = "0";

        switch ($net) {
            case "mtn-data":
                $netcode = "MTN";
                break;
            case "glo-data":
                $netcode = "GLO";
                break;
            default:
                $netcode = strtoupper($net);
        }

        $rac = ResellerDataPlans::where("code", strtolower($input['coded']))->first();

        if (env('FAKE_TRANSACTION', 1) == 0) {

            $url = env('SERVER1N') . "data" . env('SERVER1N_AUTH') . "&network=" . $netcode . "&phoneNumber=" . $phone . "&product_code=" . $code . "&price=" . $rac->price . "&trans_id=" . $transid . "&return_url=https://mcd.com";
            // Perform transaction/initialize on our server to buy
            $response = file_get_contents($url);

        } else {
            $response = '{"trans_id":"R16287146091881817015","details":{"network":"MTN","data_volume":"1GB","phone_number":"08166939205","price":"255","status":"Pending","balance":"15805"}}';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();

        $dada['server_response'] = $response;

        if (isset($rep['trans_id'])) {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }
    }

    public function server3($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        $netcode = "0";

        switch ($net) {
            case "mtn-data":
                $netcode = "MTN";
                break;
            case "glo-data":
                $netcode = "GLO";
                break;
            default:
                $netcode = strtoupper($net);
        }

        if (env('FAKE_TRANSACTION', 1) == 0) {

            $url = env('SERVER3N') . "data" . env('SERVER3N_AUTH') . "&number=" . $phone . "&plan=" . $code . "&transaction_id=" . $transid;
            // Perform transaction/initialize on our server to buy
            $response = file_get_contents($url);

        } else {
            $response = '{"network":"MTN","order":"1GB","number":"08060426915","price":"350","status":"success","ref":"agdh166363heh6366"';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();

        $dada['server_response'] = $response;

        if ($rep['status'] == "success") {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }
    }

    public function server6($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

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
                CURLOPT_POSTFIELDS => '{"request_id": "' . $transid . '", "serviceID": "' . $net . '","variation_code": "' . $code . '","phone": "' . $phone . '","billersCode": "' . $phone . '"}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('SERVER6_AUTH'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

        } else {
            $response = '{"code":"000","content":{"transactions":{"status":"delivered","product_name":"MTNData","unique_element":"08166939205","unit_price":100,"quantity":1,"service_verification":null,"channel":"api","commission":3,"total_amount":97,"discount":null,"type":"DataServices","email":"odejinmisamuel@gmail.com","phone":"08166939205","name":null,"convinience_fee":0,"amount":100,"platform":"api","method":"api","transactionId":"16287015152955612203232964"}},"response_description":"TRANSACTIONSUCCESSFUL","requestId":"R16287015121692605289","amount":"100.00","transaction_date":{"date":"2021-08-1118:05:15.000000","timezone_type":3,"timezone":"Africa\/Lagos"},"purchased_code":""}';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();

        $dada['server_response'] = $response;

        if ($rep['code'] == '000') {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }
    }
}
