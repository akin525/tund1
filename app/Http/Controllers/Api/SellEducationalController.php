<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reseller\PayController;
use App\Jobs\SendEducationtoEmailJob;
use App\Models\Educational;
use Carbon\Carbon;

class SellEducationalController extends Controller
{
    public function server1($transid, $input, $requester)
    {

        $rac = Educational::where([["exam", strtoupper($input['type'])], ['qty', $input['quantity']]])->first();

        if (!$rac) {
            return null;
        }

        if (env('FAKE_TRANSACTION', 1) == 0) {

            $url = env('SERVER1N') . 'bills/' . strtolower($input['type']) . env('SERVER1N_AUTH') . "&product_code=" . $rac->code . "&price=" . $rac->price . "&trans_id=" . $transid;
            // Perform transaction/initialize on our server to buy
            $response = file_get_contents($url);

        } else {
            if (strtolower($input['type']) == "neco") {
                $response = '{"trans_id":"1282211217008803","details":{"service":"neco","package":"One piece of neco result checker","tokens":[{"token":"1274927349283"}],"price":"650","status":"SUCCESSFUL","balance":"13816"}}';
            } else {
                $response = '{"trans_id":"1282211217008803","details":{"service":"WAEC","package":"One piece of waec result checker","pins":[{"serial_number":"WRC11102189209","pin":"1274927349283"},{"serial_number":"WRC11102189209","pin":"1274927349283"}],"price":"1700","status":"SUCCESSFUL","balance":"13816"}}';
            }

        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $input['server_response'] = $response;

        if (strtolower($input['type']) == "neco") {
            $input['token'] = $rep['details']['tokens'];
        } else {
            $input['token'] = $rep['details']['pins'];
        }

        $input['transid'] = $transid;

        if (isset($rep['trans_id'])) {
            if ($requester == "mcd") {
                $job = (new SendEducationtoEmailJob($input))
                    ->delay(Carbon::now()->addSeconds(1));
                dispatch($job);
            }
        }

        return null;
    }

    public function server6_utme($request, $code, $phone, $transid, $input, $dada, $requester)
    {
        $reqid = Carbon::now()->format('YmdHi') . $transid;

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
                CURLOPT_POSTFIELDS => '{"request_id": "' . $reqid . '", "serviceID": "jamb","variation_code": "' . $code . '","phone": "08166939205","billersCode": "' . $phone . '","amount": "' . $request->get('amount') . '"}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('SERVER6_AUTH'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

        } else {
            $response = '{ "code": "000", "content": { "transactions": { "status": "delivered", "product_name": "Jamb", "unique_element": "0123456789", "unit_price": 4700, "quantity": 1, "service_verification": null, "channel": "api", "commission": 0, "total_amount": 4700, "discount": null, "type": "Education", "email": "sandbox@vtpass.com", "phone": "07061933309", "name": null, "convinience_fee": 0, "amount": 4700, "platform": "api", "method": "api", "transactionId": "16457951913329637534894519" } }, "response_description": "TRANSACTION SUCCESSFUL", "requestId": "20220225kseeoqytisffmfkd45jkfdjdjjeodlkuwowjswiwoqidkpwfiokl", "amount": "4700.00", "transaction_date": { "date": "2022-02-25 14:19:51.000000", "timezone_type": 3, "timezone": "Africa/Lagos" }, "purchased_code": "Pin : 367574683050773", "Pin": "Pin : 367574683050773" }';
        }

        $rep = json_decode($response, true);

        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['code'] == '000') {
            $dada['token'] = $rep['purchased_code'];
            $dada['server_ref'] = $rep['content']['transactions']['transactionId'];

            if ($requester == "reseller") {
                $dada['server_ref'] = $rep['content']['transactions']['transactionId'];
                return $rs->outputResponse($request, $transid, 1, $dada);
            } else {
                return $ms->outputResp($request, $transid, 1, $dada);
            }
        } else {
            $dada['token'] = "Pin: pending";
            if ($requester == "reseller") {
                return $rs->outputResponse($request, $transid, 0, $dada);
            } else {
                return $ms->outputResp($request, $transid, 0, $dada);
            }
        }
    }
}
