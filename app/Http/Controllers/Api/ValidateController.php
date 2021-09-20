<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Offline\SwitchController;

class ValidateController extends Controller
{
    public function electricity_server6($phone, $type, $requester = "nm", $sender = "nm")
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('SERVER6') . "merchant-verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('billersCode' => $phone,'serviceID' => $type,'type' => 'prepaid'),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' .env('SERVER6_AUTH'),
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        $rep=json_decode($response, true);

        $of = new SwitchController();

        if (isset($rep['content']['Customer_Name'])) {
            if ($requester == "offline") {
                return $of->returnSuccess('Validated successfully ' . $rep['content']['Customer_Name'], $sender);
            } else {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['content']['Customer_Name']]);
            }
        } else {
            if ($requester == "offline") {
                return $of->returnError('Unable to validate number', $sender);
            } else {
                return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
            }
        }

    }

    public function tv_server6($phone, $type, $requester = "nm", $sender = "nm")
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('SERVER6') . "merchant-verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('billersCode' => $phone,'serviceID' => $type),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' .env('SERVER6_AUTH'),
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        $rep=json_decode($response, true);

        $of = new SwitchController();

        if (isset($rep['content']['Customer_Name'])) {
            if ($requester == "offline") {
                return $of->returnSuccess('Validated successfully ' . $rep['content']['Customer_Name'], $sender);
            } else {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['content']['Customer_Name']]);
            }
        } else {
            if ($requester == "offline") {
                return $of->returnSuccess('Unable to validate number.', $sender);
            } else {
                return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
            }
        }


    }

    public function betting_server7($phone, $type, $requester = "nm", $sender = "nm")
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('SERVER7_URL') . "bills/validate",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "serviceType": "betting",
    "provider": "' . $type . '",
    "customerId": "' . $phone . '"
}',
            CURLOPT_HTTPHEADER => array(
                'MerchantId: ' . env('SERVER7_MERCHANTID'),
                'Authorization: Bearer ' . env('SERVER7_PUBLICKEY'),
                'Content-Type: application/json',
                'Cookie: sessionid=eyJhbGciOiJIUzI1NiJ9.eyJqdGkiOiJPcGF5LUFQSSIsImlzcyI6IjEiLCJleHAiOjE2Mjg5MjE3NDB9._xpy555vy_wMcwGScaOLCqM6L9Abia_EaisagXRswkM'
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

//        $response='{"code":"00000","message":"SUCCESSFUL","data":{"provider":"BETKING","customerId":"1740532","firstName":null,"lastName":null,"userName":"Pateay"}}';

        $rep = json_decode($response, true);

        $of = new SwitchController();

        if ($rep['code'] == "00000") {
            if ($requester == "offline") {
                return $of->returnSuccess('Validated successfully ' . $rep['data']['userName'], $sender);
            } else {
                return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['data']['userName']]);
            }
        } else {
            if ($requester == "offline") {
                return $of->returnSuccess('Unable to validate number.', $sender);
            } else {
                return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
            }
        }


    }
}
