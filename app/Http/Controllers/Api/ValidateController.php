<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ValidateController extends Controller
{
    public function electricity_server6($phone, $type){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('SERVER6')."merchant-verify",
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


        if(isset($rep['content']['Customer_Name'])) {
            return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['content']['Customer_Name']]);
        }else{
            return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
        }

    }

    public function tv_server6($phone, $type){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('SERVER6')."merchant-verify",
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


        if (isset($rep['content']['Customer_Name'])) {
            return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['content']['Customer_Name']]);
        } else {
            return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
        }


    }

    public function betting_server7($phone, $type)
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
            CURLOPT_POSTFIELDS => array('customerId' => 4094852, 'provider' => 'BETKING', 'serviceType' => 'betting'),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . env('SERVER7_PUBLICKEY'),
                'MerchantId: ' . env('SERVER7_MERCHANTID'),
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        $rep = json_decode($response, true);

        echo $response;
        return "samji";


        if (isset($rep['content']['Customer_Name'])) {
            return response()->json(['success' => 1, 'message' => 'Validated successfully', 'data' => $rep['content']['Customer_Name']]);
        } else {
            return response()->json(['success' => 0, 'message' => 'Unable to validate number']);
        }


    }
}
