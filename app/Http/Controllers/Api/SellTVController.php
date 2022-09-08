<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reseller\PayController;
use App\Models\AppCableTVControl;
use App\Models\ResellerCableTV;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SellTVController extends Controller
{
    public function paytvProcess4($service_id, $phone, $bundle_code, $amount, $transid, $input)
    {
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
        $token = $response['token'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env("SERVER4")."/services/category/" . $service_id . "/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n  \"account\": \"" . $phone . "\"\n}",
            CURLOPT_HTTPHEADER => array(
                "Authorization: " . $token,
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        $name = $response['data']['name'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env("SERVER4")."/bills/pay/tv",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n\t\"service_category_id\": \"" . $service_id . "\",\n\t\"smartcard\": \"" . $phone . "\",\n\t\"bundleCode\": \"" . $bundle_code . "\",\n\t\"amount\": \"" . $amount . "\",\n\t\"name\": \"" . $name . "\",\n\t\"invoicePeriod\": \"1\",\n\t\"phone\": \"08000000000\"\n}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: " . $token,
                "Content-Type: text/plain"
            ),
        ));

        $respons = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($respons, true);
        $status = $response['status'];

        if($status == "success"){
            $this->addtrans("server4",$respons,$amount,1,$response['transaction']['_id'],$input);
        }else {
            $this->addtrans("server4",$respons,$amount,0,$transid,$input);
        }
    }

    function paytvProcess($amnt, $tv_package, $link, $tv_type, $phone, $transid, $input){
        $url=env('SERVER1_TV')."user_check".env('SERVER1_CRED')."&service=".$tv_type."&number=".$phone;
        // Perform initialize to validate name on server
        $resul = file_get_contents($url);
        $findme   = 'accountStatus';
        $pos = strpos($resul, $findme);
        $arr = json_decode($resul, true);
        // Note our use of ===.  Simply == would not work as expected
        if ($pos === false) {
            $findme   = 'billAmount';
            $pos = strpos($resul, $findme);

            if ($pos === false) {
                $GLOBALS['success'] = 0;
                $response["message"] = "The device number supplied did not return any data.";
            }else{
                if($arr["details"]["returnCode"]==0){
                    // Print a single value
                    $GLOBALS['success'] = 1;
                    $GLOBALS['customer_name'] =$arr["details"]["customerName"];
                    $GLOBALS['customer_number'] = $arr["details"]["customerNumber"];
                }else{
                    $GLOBALS['success'] = 0;
                    $response["message"] = "The device number supplied did not return any data.";
                }
            }
        } else {
            // Print a single value
            $GLOBALS['success'] = 1;
            $GLOBALS['customer_name'] = $arr["details"]["lastName"];
            $GLOBALS['customer_number'] = $arr["details"]["customerNumber"];
        }

//begining of buying
        if($GLOBALS['success'] ==1){
            $url=env('SERVER1_TV').$link.env('SERVER1_CRED')."&smartno=".$phone."&product_code=".$tv_package."&customer_name=".trim($GLOBALS['customer_name'])."&customer_number=".$GLOBALS['customer_number']."&trans_id=".$transid."&price=".$amnt;
            $result = file_get_contents($url);

            $findme   = 'service';
            $pos = strpos($result, $findme);
            // Note our use of ===.  Simply == would not work as expected

            if ($pos !== false) {
                $this->addtrans("server1",$result,$amnt,1, $transid,$input);
            }else {
                $this->addtrans("server1",$result,$amnt,0, $transid,$input);
            }
        }else{
            $this->addtrans("server1",$resul,$amnt,0, $transid,$input);
        }
    }

    public function server1($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        if ($requester == "reseller") {
            $rac = ResellerCableTV::where("code", strtolower($input['coded']))->first();
        } else {
            $rac = AppCableTVControl::where("coded", strtolower($input['coded']))->first();
        }

        $reqid = Carbon::now()->format('YmdHi') . $transid;

        if (env('FAKE_TRANSACTION', 1) == 0) {

            $payload='{
    "type": "' . $rac->type . '",
    "smartCardNo": "' . $phone . '",
    "packagename": "' . $rac->name . '",
    "productsCode": "' . $rac->code . '",
    "amount": "' . $rac->price . '"
}';

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('HW_BASEURL') . "purchase/cabletv",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('HW_AUTH'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

            Log::info("HW Payload. - " . $payload);

        } else {
            $response = '{ "code": 200, "message": "Payment Successful", "reference": "HONOUR|WORLD|11|20220610234440|226156" }';
        }

        $rep = json_decode($response, true);

        Log::info("HW Transaction. - " . $transid);
        Log::info($response);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['code'] == 200) {
//            $dada['server_ref'] = $rep['content']['transactions']['transactionId'];
            $dada['server_ref'] = $reqid;
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
                return $ms->outputResp($request, $transid, 1, $dada);
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }
    }

    public function server6($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        if ($requester == "reseller") {
            $rac = ResellerCableTV::where("code", strtolower($input['coded']))->first();
        } else {
            $rac = AppCableTVControl::where("coded", strtolower($input['coded']))->first();
        }

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
                CURLOPT_POSTFIELDS => '{"request_id": "' . $reqid . '", "serviceID": "' . $rac->type . '","variation_code": "' . $rac->code . '","phone": "' . $phone . '","billersCode": "' . $phone . '"}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('SERVER6_AUTH'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

        } else {
            $response = '{ "code":"000", "response_description":"TRANSACTION SUCCESSFUL", "requestId":"SAND0192837465738253A1HSD", "transactionId":"1563873435424", "amount":"50.00", "transaction_date":{ "date":"2019-07-23 10:17:16.000000", "timezone_type":3, "timezone":"Africa/Lagos" }, "purchased_code":"" }';
        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $dada['server_response'] = $response;

        if ($rep['code'] == '000') {
//            $dada['server_ref'] = $rep['content']['transactions']['transactionId'];
            $dada['server_ref'] = $reqid;
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
                return $ms->outputResp($request, $transid, 1, $dada);
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }
    }

}
