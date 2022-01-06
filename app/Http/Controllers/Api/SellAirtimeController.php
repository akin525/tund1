<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\Reseller\PayController;

class SellAirtimeController extends Controller
{

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

    public function server1($request, $amnt, $phone, $transid, $net, $input, $dada, $requester)
    {

        if (env('FAKE_TRANSACTION', 1) == 0) {

            $url = env("SERVER1N") . "airtime" . env("SERVER1N_AUTH") . "&network=" . $net . "&phoneNumber=" . $phone . "&amount=" . $amnt . "&trans_id=" . $transid;
            // Perform transaction/initialize on our server to buy
            $response = file_get_contents($url);

        } else {

            $response = '{"trans_id":"R16297328791102474957","details":{"network":"MTN","phone_number":"07064257276","amount":"100","price":98,"status":"SUCCESSFUL","balance":"31692"}}';
        }

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        $findme = 'trans_id';
        $pos = strpos($response, $findme);
        // Note our use of ===.  Simply == would not work as expected

        if ($pos !== false) {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
//                $tran->addtrans("server4", $response, $amnt, 1, $transid, $input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
//                $tran->addtrans("server4", $response, $amnt, 1, $transid, $input);
            }
        }
    }

    public function server1b($request, $amnt, $phone, $transid, $net, $input, $dada, $requester)
    {

        if (env('FAKE_TRANSACTION', 1) == 0) {

            $url = env("SERVER1N") . "airtime_premium" . env("SERVER1N_AUTH") . "&network=" . $net . "&phoneNumber=" . $phone . "&amount=" . $amnt . "&trans_id=" . $transid . "&return_url=https://5starcompany.com.ng";
            // Perform transaction/initialize on our server to buy
            $response = file_get_contents($url);

        } else {

            $response = '{"trans_id":"R1629733519965879373","details":{"network":"MTN","phone_number":"07064257276","amount":"100","price":97,"status":"Pending","balance":"31301"}}';
        }

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        $findme = 'trans_id';
        $pos = strpos($response, $findme);
        // Note our use of ===.  Simply == would not work as expected

        if ($pos !== false) {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
//                $tran->addtrans("server4", $response, $amnt, 1, $transid, $input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
//                $tran->addtrans("server4", $response, $amnt, 1, $transid, $input);
            }
        }
    }

    public function server2($request, $amnt, $phone, $transid, $net, $input, $dada, $requester)
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


        if (env('FAKE_TRANSACTION', 1) == 0) {

            $url = env("SERVER2N") . "APIAirtimeV1.asp" . env("SERVER2N_AUTH") . "&MobileNetwork=" . $service_id . "&Amount=" . $amnt . "&MobileNumber=" . $phone . "&RequestID=" . $transid . "&CallBackURL=https://www.5starcompany.com.ng";
            // Perform transaction/initialize on our server to buy
            $response = file_get_contents($url);

        } else {

            $response = '{"orderid":"6410310486","statuscode":"100","status":"ORDER_RECEIVED"}';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['status'] == "ORDER_COMPLETED" || $rep['status'] == "ORDER_RECEIVED") {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $rep['orderid'], 1, $dada);
            } else {
                return $ms->outputResp($request, $rep['orderid'], 1, $dada);
//                $tran->addtrans("server4", $response, $amnt, 1, $rep['orderid'], $input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
//                $tran->addtrans("server4", $response, $amnt, 1, $transid, $input);
            }
        }
    }

    public function server3($request, $amnt, $phone, $transid, $net, $input, $dada, $requester)
    {

        if (env('FAKE_TRANSACTION', 1) == 0) {

            $url = env("SERVER3N") . "airtime" . env("SERVER3N_AUTH") . "&network=" . $net . "&number=" . $phone . "&amount=" . $amnt . "&transaction_id=" . $transid;
            $response = file_get_contents($url);

        } else {

            $response = '{"network":"MTN","order":"AIRTIME VTU","number":"08166939205","price":"123","status":"success","ref":"484641629653902"}';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['status'] == "success") {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $rep['ref'], 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
//                $tran->addtrans("server4", $response, $amnt, 1, $rep['ref'], $input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
//                $tran->addtrans("server4", $response, $amnt, 1, $transid, $input);
            }
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
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);
            curl_close($curl);

            $resp = json_decode($response, true);
            $token = $resp['token'];

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
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

        } else {

            $response = '{ "status": "success", "message": "Transaction is processing.", "transaction": { "response_payload": { "data": { "wallet_id": 667090, "id": 38393, "created_at": "2021-08-22 14:14:27", "updated_at": "2021-08-22 14:14:27", "user_id": 59, "status": "processing", "price": 121, "network": "MTN", "referrence": "59-W5TuV1MEfWS7z7GJuduz", "number": "08166939205" }, "message": "Transaction is processing.", "status": "pending" }, "__v": 0, "status_url": "https://superadmin.mcd.5starcompany.com.ng/api/hook", "status": "pending", "is_complete": false, "request_payload": { "amount": "121", "phonenumber": "08166939205" }, "_service_category": 7, "_service": 4, "amount": 121, "_user": "5e6b7d098dabef0b8466834d", "reference": "120515132", "_id": "61224db392a38d3444044f70", "deleted_at": null, "updated_at": null, "created_at": "2021-08-22T13:14:27.029Z", "is_ended": true } }';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['status'] == 'success') {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $rep['transaction']['reference'], 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
//                $tran->addtrans("server4", $response, $amnt, 1, $rep['transaction']['reference'], $input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
//                $tran->addtrans("server4", $response, $amnt, 1, $rep['transaction']['reference'], $input);
            }
        }
    }

    public function server5($request, $amnt, $phone, $transid, $net, $input, $dada, $requester)
    {

        if (env('FAKE_TRANSACTION', 1) == 0) {
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
                CURLOPT_POSTFIELDS => '{
   "country": "NG",
   "customer": "' . $phone . '",
   "amount": ' . $amnt . ',
   "recurrence": "ONCE",
   "type": "AIRTIME",
   "reference": "' . $transid . '"
}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('RAVE_SECRET_KEY'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

        } else {

            $response = '{"status":"success","message":"Bill payment Successful","data":{"phone_number":"+2348166939205","amount":101,"network":"MTN","flw_ref":"CF-FLYAPI-20210822051137450241","reference":"BPUSSD16296522980070875117"}}';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['status'] == 'success') {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
//                $tran->addtrans("server5", $response, $amnt, 1, $transid, $input);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
//                $tran->addtrans("server5", $response, $amnt, 1, $transid, $input);
            }
        }
    }

    public function server6($request, $amnt, $phone, $transid, $net, $input, $dada, $requester)
    {

        $netcode = "0";

        switch ($net) {
            case "9MOBILE":
                $netcode = "etisalat";
                break;
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
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['code'] == '000') {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
//                $tran->addtrans("server6", $response, $amnt, 1, $transid, $input);
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

    public function server9($request, $amnt, $phone, $transid, $net, $input, $dada, $requester)
    {

        if (env('FAKE_TRANSACTION', 1) == 0) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://topups.reloadly.com/topups',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POSTFIELDS => '{
	"operatorId":"' . $input['operatorID'] . '",
	"amount":"' . $amnt . '",
	"useLocalAmount": false,
	"customIdentifier": "' . $transid . '",
	"recipientPhone": {
		"countryCode": "' . $input['country'] . '",
		"number": "' . $phone . '"
	},
	"senderPhone": {
		"countryCode": "NG",
		"number": "08166939205"
	}
}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer eyJraWQiOiIwMDA1YzFmMC0xMjQ3LTRmNmUtYjU2ZC1jM2ZkZDVmMzhhOTIiLCJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIxMTQwNyIsImlzcyI6Imh0dHBzOi8vcmVsb2FkbHkuYXV0aDAuY29tLyIsImh0dHBzOi8vcmVsb2FkbHkuY29tL3NhbmRib3giOmZhbHNlLCJodHRwczovL3JlbG9hZGx5LmNvbS9wcmVwYWlkVXNlcklkIjoiMTE0MDciLCJndHkiOiJjbGllbnQtY3JlZGVudGlhbHMiLCJhdWQiOiJodHRwczovL3RvcHVwcy1oczI1Ni5yZWxvYWRseS5jb20iLCJuYmYiOjE2NDA5MjA3NjcsImF6cCI6IjExNDA3Iiwic2NvcGUiOiJzZW5kLXRvcHVwcyByZWFkLW9wZXJhdG9ycyByZWFkLXByb21vdGlvbnMgcmVhZC10b3B1cHMtaGlzdG9yeSByZWFkLXByZXBhaWQtYmFsYW5jZSByZWFkLXByZXBhaWQtY29tbWlzc2lvbnMiLCJleHAiOjE2NDYxMDQ3NjcsImh0dHBzOi8vcmVsb2FkbHkuY29tL2p0aSI6IjRiMjBlYzgzLTljYWQtNGMzMS05YmU2LTFkNmZkZWNiNDAwMCIsImlhdCI6MTY0MDkyMDc2NywianRpIjoiZGExMzk2YTctOWI0OS00ZmM2LWJkNzEtMzVmNThiMTBmNzhhIn0.z-50LgZ15qR6iskekitueaNi95UoQdsFgRItfy8EsSw',
                    'Accept: application/com.reloadly.topups-v1+json',
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
//            echo $response;
        } else {

            $response = '{"transactionId":5006650,"status":"SUCCESSFUL","operatorTransactionId":"2022010618581735701299205","customIdentifier":"732843","recipientPhone":"2348166939205","recipientEmail":null,"senderPhone":"2348166939205","countryCode":"NG","operatorId":341,"operatorName":"MTN Nigeria","discount":0,"discountCurrencyCode":"NGN","requestedAmount":5,"requestedAmountCurrencyCode":"NGN","deliveredAmount":5,"deliveredAmountCurrencyCode":"NGN","transactionDate":"2022-01-06 12:58:16","pinDetail":null,"balanceInfo":{"oldBalance":8980.00,"newBalance":8975.00,"currencyCode":"NGN","currencyName":"Nigerian Naira","updatedAt":"2022-01-06 17:58:16"}}';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if (isset($rep['transactionId'])) {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
            }
        } else {
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
            }
        }
    }
}
