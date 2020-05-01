<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServeRequestController extends Controller
{
    public function paytv(Request $request)
    {

        $input = $request->all();
        $rules = array(
            'username' => 'required',
            'api' => 'required',
            'coded' => 'required',
            'phone' => 'required',
            'transid' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {

            /*httpParams . put("api", "mcd_app_9876234875356148750");
            httpParams . put("coded", getIntent() . getStringExtra("code"));
            httpParams . put("phone", phone . getText() . toString());
            httpParams . put("service", "paytv");
            httpParams . put("user_name", sharedpreferences . getString(myJson . user_name, "nt/p"));
            httpParams . put("wallet", sharedpreferences . getString(myJson . wallet, "nt/p"));
            httpParams . put("deviceid", ansJson . androidId);
            httpParams . put("version", NavigationDrawerConstants . version);
            httpParams . put("transid", ref);*/

//            try {
                $api = $input['api'];
                $coded = $input['coded'];
                $phone = $input['phone'];
                $service = $input['service'];
                $username = $input['username'];
                $wallet = $input['wallet'];
                $deviceid = $input['deviceid'];
                $version = $input['version'];
                $transid = $input['transid'];

                if ($api != "mcd_app_9876234875356148750") {
                    return response()->json(['status'=> 0, 'message'=>'Error, invalid request']);
                }

            switch ($coded) {
                case "d_access":
                    $tv_type = "DSTV";
                    $tv_package = "ACSSE36";
                    $link = "dstv";
                    $amount = "2000";
                    $tv_type_code = "14";
                    $tv_package_code = "01";
                    $service_id = "14";
                    break;

                case "d_family":
                    $tv_type = "DSTV";
                    $tv_package = "COFAME36";
                    $link = "dstv";
                    $amount = "4000";
                    $tv_type_code = "01";
                    $tv_package_code = "02";
                    $service_id = "14";
                    break;

                case "d_compact":
                    $tv_type = "DSTV";
                    $tv_package = "COMPE36";
                    $link = "dstv";
                    $amount = "6800";
                    $tv_type_code = "01";
                    $tv_package_code = "03";
                    $service_id = "14";
                    break;

                case "d_compactplus":
                    $tv_type = "DSTV";
                    $tv_package = "COMPLE36";
                    $link = "dstv";
                    $amount = "10650";
                    $tv_type_code = "01";
                    $tv_package_code = "04";
                    $service_id = "14";
                    break;

                case "g_lite":
                    $tv_type = "GOTV";
                    $tv_package = "GOLITE";
                    $link = "gotv";
                    $amount = "400";
                    $tv_type_code = "02";
                    $tv_package_code = "01";
                    $service_id = "15";
                    break;

                case "g_jinja":
                    $tv_type = "GOTV";
                    $tv_package = "GOTVNJ1";
                    $amount = "1600";
                    $link = "gotv";
                    $tv_type_code = "02";
//                $tv_package_code="02";
                    $service_id = "15";
                    break;

                case "g_jolli":
                    $tv_type = "GOTV";
                    $tv_package = "GOTVNJ2";
                    $amount = "2400";
                    $link = "gotv";
                    $tv_type_code = "02";
//                $tv_package_code="02";
                    $service_id = "15";
                    break;

                case "g_value":
                    $tv_type = "GOTV";
                    $tv_package = "GOTV";
                    $amount = "1250";
                    $link = "gotv";
                    $tv_type_code = "02";
                    $tv_package_code = "02";
                    $service_id = "15";
                    break;

                case "g_plus":
                    $tv_type = "GOTV";
                    $tv_package = "GOTVPLS";
                    $link = "gotv";
                    $amount = "1900";
                    $tv_type_code = "02";
                    $tv_package_code = "03";
                    $service_id = "15";
                    break;

                case "g_max":
                    $tv_type = "GOTV";
                    $tv_package = "GOTVMAX";
                    $link = "gotv";
                    $amount = "3200";
                    $tv_type_code = "02";
                    $tv_package_code = "04";
                    $service_id = "15";
                    break;


                case "s_nova":
                    $tv_type = "STARTIMES";
                    $tv_package = "STARN";
                    $link = "startimes";
                    $amount = "900";
                    $tv_type_code = "03";
                    $tv_package_code = "01";
                    $service_id = "16";
                    break;

                case "s_basic":
                    $tv_type = "STARTIMES";
                    $tv_package = "STARB";
                    $link = "startimes";
                    $amount = "1300";
                    $tv_type_code = "03";
                    $tv_package_code = "02";
                    $service_id = "16";
                    break;

                case "s_smart":
                    $tv_type = "STARTIMES";
                    $tv_package = "STARS";
                    $link = "startimes";
                    $amount = "1900";
                    $tv_type_code = "03";
                    $tv_package_code = "03";
                    $service_id = "16";
                    break;

                case "s_classic":
                    $tv_type = "STARTIMES";
                    $tv_package = "STARC";
                    $link = "startimes";
                    $amount = "2600";
                    $tv_type_code = "03";
                    $tv_package_code = "04";
                    $service_id = "16";
                    break;

                case "s_unique":
                    $tv_type = "STARTIMES";
                    $tv_package = "STARU";
                    $link = "startimes";
                    $amount = "3800";
                    $tv_type_code = "03";
                    $tv_package_code = "05";
                    $service_id = "16";
                    break;

                default:
                    $tv_type = "";
                    // required field is missing
                    return response()->json(['status'=> 0, 'message'=>'Error, Invalid coded Type. Contact info@5starcompany.com.ng for help']);
            }

            if ($tv_type == "") {
                return response()->json(['status'=> 0, 'message'=>'Error, invalid request check and try again']);

//                paytvProcess($amount, $tv_package, $link, $tv_type);
                //paytvProcess2($tv_type_code, $tv_package_code, $tv_type);
                //paytvProcess3($amount, $tv_type);
            }


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.myflex.ng/users/account/authenticate",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{\n  \"phone\": \"+2348166939205\",\n  \"password\": \"Emmanuel@10\"\n}",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Content-Type: text/plain"
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            echo $response;
            $response = json_decode($response, true);
            $token = $response['token'];

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.myflex.ng/services/category/".$service_id."/verify",
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
            echo $response;
            $response = json_decode($response, true);
            $name = $response['data']['name'];

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.myflex.ng/bills/pay/tv",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{\n\t\"service_category_id\": \"" . $service_id . "\",\n\t\"smartcard\": \"" . $phone . "\",\n\t\"bundleCode\": \"" . $tv_package . "\",\n\t\"amount\": \"" . $amount . "\",\n\t\"name\": \"" . $name . "\",\n\t\"invoicePeriod\": \"1\",\n\t\"phone\": \"08000000000\"\n}",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: " . $token,
                    "Content-Type: text/plain"
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            echo $response;
            $response = json_decode($response, true);
            $status = $response['status'];

            if ($status == "success") {
                $tran_stat = "1";
                $tran_msg = "Package " . $_REQUEST['coded'] . " Delivered on "
                    . $phone;

                echo '{"success":' . $tran_stat . ',"message":"' . $tran_msg . '", "service":"' . $tv_type . '","number":"' . $phone . '","order_code":"' . $_REQUEST['coded'] . '", "server":"server 3"}';

            } else {
                $tran_stat = "0";
                $tran_msg = "Unsuccessful Order " . $_REQUEST['coded'] . " for " . $phone;

                echo '{"success":' . $tran_stat . ',"message":"' . $tran_msg . '", "service":"' . $tv_type . '","number":"' . $phone . '","order_code":"' . $_REQUEST['coded'] . '", "server":"server 3"}';
            }

//            }catch(\Exception $e){
                //dd($e);
//                return response()->json(['status'=> 0, 'message'=>'Error processing transaction','error' => $e]);
//            }
        }else{
            return response()->json(['status'=> 0, 'message'=>'Error processing transaction', 'error' => $validator->errors()]);
        }

    }



    public function buyairtime(Request $request){
        $input = $request->all();
        $rules = array(
            'username' => 'required',
            'api' => 'required',
            'coded' => 'required',
            'phone' => 'required',
            'amount' => 'required',
            'transid' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {

            /*httpParams . put("api", "mcd_app_9876234875356148750");
            httpParams . put("coded", getIntent() . getStringExtra("code"));
            httpParams . put("phone", phone . getText() . toString());
            httpParams . put("service", "paytv");
            httpParams . put("user_name", sharedpreferences . getString(myJson . user_name, "nt/p"));
            httpParams . put("wallet", sharedpreferences . getString(myJson . wallet, "nt/p"));
            httpParams . put("deviceid", ansJson . androidId);
            httpParams . put("version", NavigationDrawerConstants . version);
            httpParams . put("transid", ref);*/

//            try {
                $api = $input['api'];
                $coded = $input['coded'];
                $phone = $input['phone'];
                $amnt = $input['amount'];
                $service = $input['service'];
                $username = $input['username'];
                $wallet = $input['wallet'];
                $deviceid = $input['deviceid'];
                $version = $input['version'];
                $transid = $input['transid'];

                if ($api != "mcd_app_9876234875356148750") {
                    return response()->json(['status'=> 0, 'message'=>'Error, invalid request']);
                }

        switch ($coded){
            case "m":
                $network="MTN";
                $network_code="01";
                $service_id="7";
                break;

            case "M":
                $network="MTN";
                $network_code="01";
                $service_id="7";
                break;

            case "e":
                $network="9MOBILE";
                $network_code="03";
                $service_id="9";
                break;

            case "E":
                $network="9MOBILE";
                $network_code="03";
                $service_id="9";
                break;

            case "9":
                $network="9MOBILE";
                $network_code="03";
                $service_id="9";
                break;

            case "g":
                $network="GLO";
                $network_code="02";
                $service_id="8";
                break;

            case "G":
                $network="GLO";
                $network_code="02";
                $service_id="8";
                break;

            case "a":
                $network="AIRTEL";
                $network_code="04";
                $service_id="6";
                break;

            case "A":
                $network="AIRTEL";
                $network_code="04";
                $service_id="6";
                break;

            default:
                $network="";
                // required field is missing
                return response()->json(['status'=> 0, 'message'=>'Invalid Network. Available are m for MTN, 9 for 9MOBILE, g for GLO, a for AIRTEL.']);
        }

        if(!is_numeric($amnt)){

// required field is missing
// echoing JSON response
            return response()->json(['status'=> 0, 'message'=>'Invalid amount, retry with valid amount.']);
        }


        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.myflex.ng/users/account/authenticate",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\n  \"phone\": \"+2348166939205\",\n  \"password\": \"Emmanuel@10\"\n}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
        $response = json_decode($response, true);
        $token = $response['token'];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.myflex.ng/bills/pay/airtime",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\n  \"amount\": \"1\",\n  \"service_category_id\": \"".$service_id."\",\n  \"phonenumber\": \"".$phone."\",\n  \"status_url\": \"http://api.mydomain.com/airtime_callback\"\n}",
            CURLOPT_HTTPHEADER => array(
                "Authorization: ".$token,
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));

        $response = curl_exec($curl);
        echo $response;

        curl_close($curl);
        $response = json_decode($response, true);
        $status = $response['status'];

//        if ($status == "success") {
//            $tran_stat="1";
//            $tran_msg="Package ".$_REQUEST['coded']." Delivered on "
//                .$phone;
//
//            echo '{"success":'.$tran_stat.',"message":"'.$tran_msg.'", "service":"'.$tv_type.'","number":"'.$phone.'","order_code":"'.$_REQUEST['coded'].'", "server":"server 3"}';
//
//        }else {
//            $tran_stat="0";
//            $tran_msg="Unsuccessful Order ".$_REQUEST['coded']." for ".$phone;
//
//            echo '{"success":'.$tran_stat.',"message":"'.$tran_msg.'", "service":"'.$tv_type.'","number":"'.$phone.'","order_code":"'.$_REQUEST['coded'].'", "server":"server 3"}';
//        }

//            }catch(\Exception $e){
                //dd($e);
//                return response()->json(['status'=> 0, 'message'=>'Error processing transaction','error' => $e]);
//            }
        }else{
            return response()->json(['status'=> 0, 'message'=>'Error processing transaction', 'error' => $validator->errors()]);
        }

    }
}
