<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function server3(Request $request){
        $input = $request->all();
        $ref=$input['ref'];

        $url=env("SERVER3_QUERY") ."&reference=".$ref;

        $result = file_get_contents($url);
        // Convert JSON string to Array
        $someArray = json_decode($result, true);

        return view('verification_s3', ['status' => $someArray["status"], 'description' => $someArray["description"], 'response'=>true]);
    }

    public function server2(Request $request){
        $input = $request->all();
        $ref=$input['ref'];

        $url=env("SERVER2_QUERY") ."&OrderID=".$ref;

        $result = file_get_contents($url);
        // Convert JSON string to Array
        $someArray = json_decode($result, true);

        if($someArray["status"]=="MISSING_ORDERID"){
            $status="Error";
            $d="Invalid reference number";
        }else{
            $status=$someArray["remark"];
            $d=$someArray["mobilenetwork"] ." " .$someArray["ordertype"]." " .$someArray["mobilenumber"];
        }

        return view('verification_s2', ['status' => $status, 'description' => $d, 'response'=>true]);
    }

    public function server1b(Request $request){
        $input = $request->all();
        $ref=$input['ref'];

        $url=env("SERVER1B_QUERY") ."&trans_id=".$ref;

        $result = file_get_contents($url);
        // Convert JSON string to Array
        $someArray = json_decode($result, true);

        $findme='trans_id';
        $pos = strpos($result, $findme);
        // Note our use of ===.  Simply == would not work as expected

        if ($pos !== false) {
            $status=$someArray["details"]["status"];
            $d=$someArray["details"]["network"] ." " .$someArray["details"]["amount"]." " .$someArray["details"]["phone_number"];
        }else {
            $status="Error";
            $d="Can not find transaction";
        }

        return view('verification_s1b', ['status' => $status, 'description' => $d, 'response'=>true]);
    }

    public function server1(Request $request){
        $input = $request->all();
        $ref=$input['ref'];

        $url=env("SERVER1_QUERY") ."&trans_id=".$ref;

        $result = file_get_contents($url);
        // Convert JSON string to Array
        $someArray = json_decode($result, true);

        $findme='trans_id';
        $pos = strpos($result, $findme);
        // Note our use of ===.  Simply == would not work as expected

        if ($pos !== false) {
            $status=$someArray["details"]["status"];
            $d=$someArray["details"]["network"] ." " .$someArray["details"]["amount"]." " .$someArray["details"]["phone_number"];
        }else {
            $status="Error";
            $d="Can not find transaction";
        }

        return view('verification_s1', ['status' => $status, 'description' => $d, 'response'=>true]);
    }

    public function server1dt(Request $request){
        $input = $request->all();
        $ref=$input['ref'];

        $url=env("SERVER1DT_QUERY") ."&trans_id=".$ref;

        $result = file_get_contents($url);
        // Convert JSON string to Array
        $someArray = json_decode($result, true);

        $findme='trans_id';
        $pos = strpos($result, $findme);
        // Note our use of ===.  Simply == would not work as expected

        if ($pos !== false) {
            $status=$someArray["details"]["status"];
            $d=$someArray["details"]["network"] ." " .$someArray["details"]["data_volume"]." " .$someArray["details"]["phone_number"];
        }else {
            $status="Error";
            $d="Can not find transaction";
        }

        return view('verification_s1dt', ['status' => $status, 'description' => $d, 'response'=>true]);
    }

    public function server4(Request $request){
        $input = $request->all();
        $ref=$input['ref'];


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env("SERVER4")."/users/account/authenticate",
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
            CURLOPT_URL => 'https://api.myflex.ng/transactions/'.$ref,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        // Convert JSON string to Array
        $res = json_decode($response, true);

        if($res["status"]=="error"){
            $status="Error";
            $d="Invalid reference number";
        }else{
            $status=$res["data"]["status"];
            if(array_key_exists($res['data']['request_payload']['smartcard'])){
                $d=$res['data']['_service_category']['name'] ." " .$res['data']['request_payload']['bundleCode']." " .$res['data']['request_payload']['smartcard']."-" .$res['data']['request_payload']['name'];
            }else{
                $d=$res['data']['_service_category']['name'] ." " .$res['data']['request_payload']['amount']." " .$res['data']['request_payload']['phonenumber'];
            }

        }

        return view('verification_s1dt', ['status' => $status, 'description' => $d, 'response'=>true]);
    }
}
