<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reseller\PayController;

class SellElectricityController extends Controller
{
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
                CURLOPT_POSTFIELDS => '{"request_id": "' . $transid . '", "serviceID": "' . $code . '","variation_code": "prepaid","phone": "' . $phone . '","billersCode": "' . $phone . '","amount": "' . $request->get('amount') . '"}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('SERVER6_AUTH'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

        } else {
            $response = '{ "code": "000", "content": { "transactions": { "amount": 1000, "convinience_fee": 0, "status": "delivered", "name": null, "phone": "07061933309", "email": "sandbox@vtpass.com", "type": "Electricity Bill", "created_at": "2019-08-17 02:27:26", "discount": null, "giftcard_id": null, "total_amount": 992, "commission": 8, "channel": "api", "platform": "api", "service_verification": null, "quantity": 1, "unit_price": 1000, "unique_element": "1010101010101", "product_name": "Eko Electric Payment - EKEDC" } }, "response_description": "TRANSACTION SUCCESSFUL", "requestId": "hg3hgh3gdiud4w2wb33", "amount": "1000.00", "transaction_date": { "date": "2019-08-17 02:27:27.000000", "timezone_type": 3, "timezone": "Africa/Lagos" }, "purchased_code": "Token : 42167939781206619049 Bonus Token : 62881559799402440206", "mainToken": "42167939781206619049", "mainTokenDescription": "Normal Sale", "mainTokenUnits": 16666.666, "mainTokenTax": 442.11, "mainsTokenAmount": 3157.89, "bonusToken": "62881559799402440206", "bonusTokenDescription": "FBE Token", "bonusTokenUnits": 50, "bonusTokenTax": null, "bonusTokenAmount": null, "tariffIndex": "52", "debtDescription": "1122" }';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();

        $dada['server_response'] = $response;

        if ($rep['code'] == '000') {
            $dada['token'] = $rep['purchased_code'];

            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        } else {
            $dada['token'] = "Token: pending";
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }
    }
}
