<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\SystemSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServeRequestController extends Controller
{
    public function paytv(Request $request)
    {

        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
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

            try {
            $api = $input['api'];
            $coded = $input['coded'];
            $phone = $input['phone'];
            $service = $input['service'];
            $user_name = $input['user_name'];
            $wallet = $input['wallet'];
            $deviceid = $input['deviceid'];
            $version = $input['version'];
            $transid = $input['transid'];

            if ($api != "mcd_app_9876234875356148750") {
                return response()->json(['status' => 0, 'message' => 'Error, invalid request']);
            }

            switch ($coded) {
                case "d_access":
                    $tv_type = "DSTV";
                    $tv_package = "ACSSE36";
                    $bundle_code = "ACSSW4";
                    $link = "dstv";
                    $amount = "2000";
                    $tv_type_code = "14";
                    $tv_package_code = "01";
                    $service_id = "14";
                    break;

                case "d_family":
                    $tv_type = "DSTV";
                    $tv_package = "COFAME36";
                    $bundle_code = "COFAMW4";
                    $link = "dstv";
                    $amount = "4000";
                    $tv_type_code = "01";
                    $tv_package_code = "02";
                    $service_id = "14";
                    break;

                case "d_compact":
                    $tv_type = "DSTV";
                    $tv_package = "COMPE36";
                    $bundle_code = "MINIBW4";
                    $link = "dstv";
                    $amount = "6800";
                    $tv_type_code = "01";
                    $tv_package_code = "03";
                    $service_id = "14";
                    break;

                case "d_compactplus":
                    $tv_type = "DSTV";
                    $tv_package = "COMPLE36";
                    $bundle_code = "COMPLW7";
                    $link = "dstv";
                    $amount = "10650";
                    $tv_type_code = "01";
                    $tv_package_code = "04";
                    $service_id = "14";
                    break;

                case "g_lite":
                    $tv_type = "GOTV";
                    $tv_package = "GOLITE";
                    $bundle_code = "GOLITE";
                    $link = "gotv";
                    $amount = "400";
                    $tv_type_code = "02";
                    $tv_package_code = "01";
                    $service_id = "15";
                    break;

                case "g_jinja":
                    $tv_type = "GOTV";
                    $tv_package = "GOTVNJ1";
                    $bundle_code = "GOTVNJ1";
                    $amount = "1600";
                    $link = "gotv";
                    $tv_type_code = "02";
//                $tv_package_code="02";
                    $service_id = "15";
                    break;

                case "g_jolli":
                    $tv_type = "GOTV";
                    $tv_package = "GOTVNJ2";
                    $bundle_code = "GOTVNJ2";
                    $amount = "2400";
                    $link = "gotv";
                    $tv_type_code = "02";
//                $tv_package_code="02";
                    $service_id = "15";
                    break;

                case "g_value":
                    $tv_type = "GOTV";
                    $tv_package = "GOTV";
                    $bundle_code = "GOTV";
                    $amount = "1250";
                    $link = "gotv";
                    $tv_type_code = "02";
                    $tv_package_code = "02";
                    $service_id = "15";
                    break;

                case "g_plus":
                    $tv_type = "GOTV";
                    $tv_package = "GOTVPLS";
//                    $bundle_code = "GOTVPLS";
                    $link = "gotv";
                    $amount = "1900";
                    $tv_type_code = "02";
                    $tv_package_code = "03";
                    $service_id = "15";
                    break;

                case "g_max":
                    $tv_type = "GOTV";
                    $tv_package = "GOTVMAX";
                    $bundle_code = "GOMAX";
                    $link = "gotv";
                    $amount = "3200";
                    $tv_type_code = "02";
                    $tv_package_code = "04";
                    $service_id = "15";
                    break;


                case "s_nova":
                    $tv_type = "STARTIMES";
                    $tv_package = "STARN";
                    $bundle_code = "900";
                    $link = "startimes";
                    $amount = "900";
                    $tv_type_code = "03";
                    $tv_package_code = "01";
                    $service_id = "16";
                    break;

                case "s_basic":
                    $tv_type = "STARTIMES";
                    $tv_package = "STARB";
                    $bundle_code = "1300";
                    $link = "startimes";
                    $amount = "1300";
                    $tv_type_code = "03";
                    $tv_package_code = "02";
                    $service_id = "16";
                    break;

                case "s_smart":
                    $tv_type = "STARTIMES";
                    $tv_package = "STARS";
                    $bundle_code = "1900";
                    $link = "startimes";
                    $amount = "1900";
                    $tv_type_code = "03";
                    $tv_package_code = "03";
                    $service_id = "16";
                    break;

                case "s_classic":
                    $tv_type = "STARTIMES";
                    $tv_package = "STARC";
                    $bundle_code = "2600";
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
                    return response()->json(['status' => 0, 'message' => 'Error, Invalid coded Type. Contact info@5starcompany.com.ng for help']);
            }

            if ($tv_type == "") {
                return response()->json(['status' => 0, 'message' => 'Error, invalid request check and try again']);
            }

            if ($tv_type == "DSTV") {
                $this->paytvProcess4($service_id, $phone, $bundle_code, $amount, $coded, $tv_type);
            }

            if ($tv_type == "STARTIMES") {
                $this->paytvProcess4($service_id, $phone, $bundle_code, $amount, $coded, $tv_type);
            }

            if ($tv_package == "GOTVNJ1" || $tv_package == "GOTVNJ2" || $tv_package == "GOTVMAX") {
                $this->paytvProcess4($service_id, $phone, $bundle_code, $amount, $coded, $tv_type);
            } else {
//                $this->paytvProcess($amount, $tv_package, $link, $tv_type, $coded, $phone);
                return response()->json(['success' => 1, 'message' => "pending", 'service'=> $tv_type, 'number'=> $phone, 'order_code'=> $coded, 'server'=> "server 0"]);

            }

            }catch(\Exception $e){
                dd($e);
                return response()->json(['status'=> 0, 'message'=>'Error processing transaction','error' => $e]);
            }

        }else{
            return response()->json(['status'=> 0, 'message'=>'Error processing transaction', 'error' => $validator->errors()]);
        }

    }



    public function buyairtime(Request $request){
        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
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

            try {
                $api = $input['api'];
                $coded = $input['coded'];
                $phone = $input['phone'];
                $amnt = $input['amount'];
                $service = $input['service'];
                $user_name = $input['user_name'];
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
            }else{
                $sys=SystemSettings::where('name','=','control')->first();
                if($sys->airtime==1){
                    $this->airtimeProcess($amnt, $network, $coded, $phone);
                }elseif ($sys->airtime==2){
                    $this->airtimeProcess2($amnt, $network_code, $network, $phone, $coded);
                }elseif ($sys->airtime==3){
                    $this->airtimeProcess3($amnt, $network, $coded, $phone);
                }elseif ($sys->airtime==4){
                    $this->airtimeProcess4($amnt, $service_id, $phone, $network, $coded);
                }

            }

            }catch(\Exception $e){
                dd($e);
                return response()->json(['status'=> 0, 'message'=>'Error processing transaction','error' => $e]);
            }

        }else{
            return response()->json(['status'=> 0, 'message'=>'Error processing transaction', 'error' => $validator->errors()]);
        }

    }

    public function paytvProcess4($service_id, $phone, $bundle_code, $amount, $coded, $tv_type)
    {
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
        $response = json_decode($response, true);
        $token = $response['token'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.myflex.ng/services/category/" . $service_id . "/verify",
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
            CURLOPT_URL => "https://api.myflex.ng/bills/pay/tv",
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

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        $status = $response['status'];

        if ($status == "success") {
            $tran_stat = "1";
            $tran_msg = "Package " . $coded . " Delivered on " . $phone;

            echo json_encode(['success' => $tran_stat, 'message' => $tran_msg, 'service'=> $tv_type, 'number'=> $phone, 'order_code'=> $coded, 'server'=> "server 4"]);
        } else {
            $tran_stat = "0";
            $tran_msg = "Unsuccessful Order " . $coded . " for " . $phone;

            echo json_encode(['success' => $tran_stat, 'message' => $tran_msg, 'service'=> $tv_type, 'number'=> $phone, 'order_code'=> $coded, 'server'=> "server 4"]);
        }
    }

    function paytvProcess($amnt, $tv_package, $link, $tv_type, $coded, $phone){
        $ref=date('ymdhis');
//start of checking
        $url="https://mobilenig.com/api/bills/user_check?username=samji10&password=Emmanuel@10&service=".$tv_type."&number=".$phone;
        // Perform initialize to validate name on server

        $result = file_get_contents($url);
        echo $result;
        $findme   = 'accountStatus';
        $pos = strpos($result, $findme);
        $arr = json_decode($result, true);
        // Note our use of ===.  Simply == would not work as expected
        if ($pos === false) {
            $findme   = 'billAmount';
            $pos = strpos($result, $findme);

            if ($pos === false) {
                $GLOBALS['success'] = 0;
                $response["message"] = "The device number supplied did not return any data.";
            }else{
                if($arr["details"]["returnCode"]==0){
                    // Print a single value
                    $GLOBALS['success'] = 1;
                    $GLOBALS['customer_name'] ="samji";
                    $GLOBALS['customer_number'] = $arr["details"]["customerNumber"];
                }else{
                    $GLOBALS['success'] = 0;
                    $response["message"] = "The device number supplied did not return any data.";
                }
            }
        } else {
            // Print a single value
            $GLOBALS['success'] = 1;
            $GLOBALS['customer_name'] = "samji";
            $GLOBALS['customer_number'] = $arr["details"]["customerNumber"];
        }

//begining of buying
        if($GLOBALS['success'] ==1){
            $url="https://mobilenig.com/api/bills/".$link."?username=samji10&password=Emmanuel@10&smartno=".$phone."&product_code=".$tv_package."&customer_name=".$GLOBALS['customer_name']."&customer_number=".$GLOBALS['customer_number']."&ref=".$ref."&amount=".$amnt;

            $result = file_get_contents($url);

            if ($result == "00") {
                $tran_stat="1";
                $tran_msg="Package ".$coded." Delivered on ".$phone;

                echo json_encode(['success' => $tran_stat, 'message' => $tran_msg, 'service'=> $tv_type, 'number'=> $phone, 'order_code'=> $coded, 'server'=> "server 1"]);
            }else {

                $tran_stat="0";
                $tran_msg="Unsuccessful Order ".$coded." for ".$phone;

                echo json_encode(['success' => $tran_stat, 'message' => $tran_msg, 'service'=> $tv_type, 'number'=> $phone, 'order_code'=> $coded, 'server'=> "server 1"]);
            }
        }else{
            $tran_stat="0";
            $tran_msg="Unsuccessful Order ".$coded." for ".$phone;

                    echo json_encode(['success' => $tran_stat, 'message' => $tran_msg, 'service'=> $tv_type, 'number'=> $phone, 'order_code'=> $coded, 'server'=> "server 4"]);


//            echo '{"success":'.$tran_stat.',"message":"'.$tran_msg.'", "service":"'.$tv_type.'","number":"'.$phone.'","order_code":"'.$coded.'", "server":"server 1"}';
        }
    }

    public function airtimeProcess($amnt, $network, $coded, $phone){
        $ref=date('ymdhis');

        $url="https://mobilenig.com/api/airtime.php/?user_name=samji10&password=Emmanuel@10&network=".$network."&phoneNumber=".$phone."&amount=".$amnt;

        $result = file_get_contents($url);

        if ($result == "00") {
            $tran_stat="1";
            $tran_msg=$network." Airtime ".$amnt." Delivered on ".$phone;

            echo json_encode(['success' => $tran_stat, 'message' => $tran_msg, 'network'=> $network, 'number'=> $phone, 'order_code'=> $coded, 'server'=> "server 1"]);

        }else {

            $tran_stat="0";
            $tran_msg="Unsuccessful ".$network." Airtime ".$amnt." for ".$phone;

            echo json_encode(['success' => $tran_stat, 'message' => $tran_msg, 'network'=> $network, 'number'=> $phone, 'order_code'=> $coded, 'server'=> "server 1"]);
        }
    }

    public function airtimeProcess2($amnt, $network_code, $network, $phone, $coded){
//01 for MTN, 02 for Glo, 03 for Etisalat, 04 for Airtel

        $url="https://www.nellobytesystems.com/APIAirtimeV1.asp?UserID=CK10123847&APIKey=W5352Q23GDS924D7UA1B84YYY506178I69DDE4JR1ZRAR80FCBQF819D4T7HKI85&MobileNetwork=".$network_code."&Amount=".$amnt."&MobileNumber=".$phone."&CallBackURL=https://www.5starcompany.com.ng";

        // Perform transaction/initialize on our server to buy

        $result = file_get_contents($url);

        // Convert JSON string to Array
        $someArray = json_decode($result, true);
        // Dump all data of the Array
        $result=$someArray["status"]; // Access Array data

        if ($result == "ORDER_RECEIVED" || $result == "ORDER_COMPLETED") {
            $tran_stat="1";
            $tran_msg="Data ".$coded." Delivered on ".$phone;

            echo json_encode(['success' => $tran_stat, 'message' => $tran_msg, 'network'=> $network, 'number'=> $phone, 'order_code'=> $coded, 'server'=> "server 2"]);

        }else if ($result == "INVALID_RECIPIENT") {
            $tran_stat="0";
            $tran_msg="An invalid mobile phone number was entered (".$phone. ")";

            echo json_encode(['success' => $tran_stat, 'message' => $tran_msg, 'network'=> $network, 'number'=> $phone, 'order_code'=> $coded, 'server'=> "server 2"]);
        }else {

            $tran_stat="0";
            $tran_msg="Unsuccessful Order ".$coded." for ".$phone;

            echo json_encode(['success' => $tran_stat, 'message' => $tran_msg, 'network'=> $network, 'number'=> $phone, 'order_code'=> $coded, 'server'=> "server 2"]);
        }
    } //ending function

    public function airtimeProcess3($amnt, $network, $coded, $phone)
    {
        $url = "https://minitechs.com.ng/api/vtu.php?username=08166939205&password=Emmanuel@10&network=" . $network . "&number=" . $phone . "&amount=" . $amnt;

        $result = file_get_contents($url);
        if ($result == "00") {
            $tran_stat = "1";
            $tran_msg = $network . " Airtime " . $amnt . " Delivered on " . $phone;

            echo json_encode(['success' => $tran_stat, 'message' => $tran_msg, 'network'=> $network, 'number'=> $phone, 'order_code'=> $coded, 'server'=> "server 3"]);
        } else {

            $tran_stat = "0";
            $tran_msg = "Unsuccessful " . $network . " Airtime " . $amnt . " for " . $phone;

            echo json_encode(['success' => $tran_stat, 'message' => $tran_msg, 'network'=> $network, 'number'=> $phone, 'order_code'=> $coded, 'server'=> "server 3"]);
        }
    }



    public function airtimeProcess4($amnt, $service_id, $phone, $network, $coded)
    {
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
            CURLOPT_POSTFIELDS => "{\n  \"amount\": \"".$amnt."\",\n  \"service_category_id\": \"" . $service_id . "\",\n  \"phonenumber\": \"" . $phone . "\",\n  \"status_url\": \"https://admin-mcd.5starcompany.com.ng/api/hook\"\n}",
            CURLOPT_HTTPHEADER => array(
                "Authorization: " . $token,
                "Content-Type: application/json",
                "Content-Type: text/plain"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response, true);
        $status = $response['status'];

        if($status == "success"){
            $tran_stat = 1;
            $tran_msg = $network . " Airtime " . $amnt . " Delivered on " . $phone;

            echo json_encode(['success' => $tran_stat, 'message' => $tran_msg, 'network'=> $network, 'number'=> $phone, 'order_code'=> $coded, 'server'=> 'server 4']);
        }else {

            $tran_stat = 0;
            $tran_msg = "Unsuccessful " . $network . " Airtime " . $amnt . " for " . $phone;

            echo json_encode(['success' => $tran_stat, 'message' => $tran_msg, 'network'=> $network, 'number'=> $phone, 'order_code'=> $coded, 'server'=> 'server 4']);
        }

    }

}
