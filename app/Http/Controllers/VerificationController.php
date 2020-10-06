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
}
