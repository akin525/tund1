<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reseller\PayController;
use App\Models\AppDataControl;
use App\Models\ResellerDataPlans;
use Exception;

class SellDataController extends Controller
{

    public function server1($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {
        if ($requester == "reseller") {
            $rac = ResellerDataPlans::where("code", strtolower($input['coded']))->first();
        } else {
            $rac = AppDataControl::where("coded", strtolower($input['coded']))->first();
        }

        if (env('FAKE_TRANSACTION', 1) == 0) {

            $url = env('SERVER1N') . "data" . env('SERVER1N_AUTH') . "&network=" . $rac->network . "&phoneNumber=" . $phone . "&product_code=" . $rac->product_code . "&price=" . $rac->price . "&trans_id=" . $transid . "&return_url=https://mcd.com";
            // Perform transaction/initialize on our server to buy
            $response = file_get_contents($url);

        } else {
            $response = '{"trans_id":"R16287146091881817015","details":{"network":"MTN","data_volume":"1GB","phone_number":"08166939205","price":"255","status":"Pending","balance":"15805"}}';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if (isset($rep['trans_id'])) {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }
    }

    public function server2($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        $service_id = "0";

        switch ($net) {
            case "MTN":
                $service_id = "01";
                break;

            case "9MOBILE":
                $service_id = "03";
                break;

            case "GLO":
                $service_id = "02";
                break;

            case "AIRTEL":
                $service_id = "04";
                break;

            default:
                return response()->json(['success' => 0, 'message' => 'Invalid Network. Available are m for MTN, 9 for 9MOBILE, g for GLO, a for AIRTEL.']);
        }


        if ($requester == "reseller") {
            $rac = ResellerDataPlans::where("code", strtolower($input['coded']))->first();
        } else {
            $rac = AppDataControl::where("coded", strtolower($input['coded']))->first();
        }

        if (env('FAKE_TRANSACTION', 1) == 0) {

            $url = env("SERVER2N") . "APIDatabundleV1.asp" . env("SERVER2N_AUTH") . "&MobileNetwork=" . $service_id . "&DataPlan=" . $rac->dataplan . "&MobileNumber=" . $phone . "&RequestID=" . $transid . "&CallBackURL=https://www.5starcompany.com.ng";
            // Perform transaction/initialize on our server to buy
            $response = file_get_contents($url);

        } else {
            $response = '{"orderid":"789","statuscode":"100","status":"ORDER_RECEIVED"}';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['status'] == "ORDER_COMPLETED" || $rep['status'] == "ORDER_RECEIVED") {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $rep['orderid'], 1, $dada);
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }
    }

    public function server3($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {
        if ($requester == "reseller") {
            $rac = ResellerDataPlans::where("code", strtolower($input['coded']))->first();
        } else {
            $rac = AppDataControl::where("coded", strtolower($input['coded']))->first();
        }

        if (env('FAKE_TRANSACTION', 1) == 0) {

            $url = env('SERVER3N') . "data" . env('SERVER3N_AUTH') . "&number=" . $phone . "&plan=" . $rac->product_code . "&transaction_id=" . $transid;
            // Perform transaction/initialize on our server to buy
            $response = file_get_contents($url);

        } else {
            $response = '{"network":"MTN","order":"1GB","number":"08060426915","price":"350","status":"success","ref":"agdh166363heh6366"}';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['status'] == "success") {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }
    }

    public function server6($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        if ($requester == "reseller") {
            $rac = ResellerDataPlans::where("code", strtolower($input['coded']))->first();
        } else {
            $rac = AppDataControl::where("coded", strtolower($input['coded']))->first();
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
                CURLOPT_POSTFIELDS => '{"request_id": "' . $transid . '", "serviceID": "' . $net . '","variation_code": "' . $rac->code . '","phone": "' . $phone . '","billersCode": "' . $phone . '"}',
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
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['code'] == '000') {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }
    }

    public function server8($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        if ($requester == "reseller") {
            $rac = ResellerDataPlans::where("code", strtolower($input['coded']))->first();
        } else {
            $rac = AppDataControl::where("coded", strtolower($input['coded']))->first();
        }

        echo $rac;
        echo "here";

        if (env('FAKE_TRANSACTION', 1) == 0) {


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://honourworld.ng/datatopup',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('action' => 'data-topup', 'category_id' => '12', 'plan_id' => $rac->plan_id, 'contact_opt' => '2', 'phone_num' => $phone),
                CURLOPT_HTTPHEADER => array(
                    env('SERVER8_AUTH'),
                    'referer: https://honourworld.ng/products/data-top-up',
                    'user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Mobile Safari/537.36'
                ),
            ));

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

        } else {
            $response = '{"reset":true,"result":1,"url":"https:\/\/honourworld.ng\/products\/data-top-up","msg":"Data top-up request has been received and will be processed shortly! "}';
        }

        try {
            $rep = json_decode($response, true);
        } catch (Exception $e) {
            $response = '{"reset":true,"result":0,"url":"https:\/\/honourworld.ng\/products\/data-top-up","msg":"Data top-up request has been received and will be processed shortly! "}';
        }


        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if (isset($rep['result'])) {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }
    }
}
