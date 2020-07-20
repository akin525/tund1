<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function signup(Request $request){
        /*Updated on 12/08/2019 by Samji

         * Following code will get single product details

         * A product is identified by product id (user_name)

         */

// check for post data
        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'password'      => 'required',
            'phoneno'      => 'required',
            'deviceid'      => 'required',
            'email' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->passes())
        {
            $user_name = $input['user_name'];
            $deviceid = $input['deviceid'];

            // get all user device first
            $users=User::all();
            foreach ($users as $key => $user) {
                // Assign JSON encoded string to a PHP variable
                $json = $user->devices;

                if ($json != "") {
                    // Decode JSON data to PHP associative array
                    $arr = json_decode($json, true);

                    // Loop through the associative array
                    foreach ($arr as $value) {
                        if ($value == $deviceid) {
                            $GLOBALS['found'] = 1;

                            $response["success"] = 0;
                            $response["message"] = "Device already linked to a user";
//                            echo json_encode($response);
                            return response()->json(['success'=> 0, 'message'=>'Device already linked to a user']);
                        }
                    }
                }//looping through the database and also checking for device id match
            }

            $user=User::where('user_name', $user_name)->get();
            if(!$user->isEmpty()){
                $response["success"] = 0;
                $response["message"] = "User name already exist";
                //values gotten
                // echoing JSON response
//                echo json_encode($response);
                return response()->json(['success'=> 0, 'message'=>'User name already exist']);

            }else{
                //values gotten
                $create["wallet"] = "0";
                $create["status"] = "client";
                $create["level"] = "1";
                $create["target"] = "Make up to 10 transactions to be eligible for an Agent and send a request mail to info@5starcompany.com.ng where you earn incentives on transactions done at the end of the month";
                $create["user_name"] = $user_name;
                $create["email"] = $input["email"];
                $create["phoneno"] = $input["phoneno"];
                $create["mcdpassword"] = $input["password"];
                $create["password"] = "";
                $create["gnews"] = 'If you are a business person that needs to increase your investment and make more money, you just arrived at the right place';
                $date = date("Y-m-d H:i:s");
                $dev = array($date => $deviceid);
                $create["devices"] = json_encode($dev);

                if (User::create($create)) {
                    // successfully inserted into database
                    $response["success"] = 1;
                    $response["message"] = "Client Successfully Added";

                    // echoing JSON response
//                    echo json_encode($response);
                    return response()->json(['success'=> 1, 'message'=>'Client Successfully Added']);
                } else {
                    // failed to insert row
                    $response["success"] = 0;
                    $response["message"] = "Oops! An error occurred.";
                    // echoing JSON response
//                    echo json_encode($response);
                    return response()->json(['success'=> 0, 'message'=>'Oops! An error occurred.']);
                }
            }

        }else{
            // required field is missing
            $response["success"] = 0;
            $response["message"] = "Required field(s) is missing";
            // echoing JSON response
//            echo json_encode($response);
            return response()->json(['success'=> 0, 'message'=>'Required field(s) is missing']);
        }
    }

    public function login(Request $request){
        /* updated on 11/10/2019 by samji

         * Following code will get single product details

         * A product is identified by product id (uid)

         */

        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'password'      => 'required',
            'phoneno'      => 'required',
            'deviceid'      => 'required',
            'email' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->passes())
        {
            $user_name = $input['user_name'];
            $user=User::where('user_name', $user_name)->get();
        }else{
            // required field is missing
            $response["success"] = 0;
            $response["message"] = "Required field(s) is missing";
            // echoing JSON response
            echo json_encode($response);
            return response()->json(['success'=> 0, 'message'=>'Required field(s) is missing']);
        }

        if (isset($_GET["uid"]) && isset($_GET["deviceid"])) {

            $uid = $_GET['uid'];
            $deviceid = $_GET["deviceid"];

            function getAgent_config($uid, $con, $msgcode)
            {
                $result = mysqli_query($con, "SELECT * FROM tbl_agents WHERE user_name = '$uid'");
                if (!empty($result)) {
                    // check for empty result
                    if (mysqli_num_rows($result) > 0) {

                        $result = mysqli_fetch_array($result);

                        //populating values now
                        // success
                        $response["success"] = 1;

                        if ($msgcode == 101) {
                            $response["message"] = "Device linked to this user";
                        } elseif ($msgcode == 102) {
                            $response["message"] = "Device does not exist and has been linked to this user";
                        } elseif ($msgcode == 103) {
                            $response["message"] = "Deviceid Found successfully";
                        }

                        //values gotten
                        $response["id"] = $result["id"];
                        $response["full_name"] = $result["full_name"];
                        $response["company_name"] = $result["company_name"];
                        $response["dob"] = $result["dob"];
                        $response["bvn"] = $result["bvn"];
                        $response["wallet"] = $result["wallet"];
                        $response["bonus"] = $result["bonus"];
                        $response["status"] = $result["status"];
                        $response["level"] = $result["level"];
                        $response["photo"] = $result["photo"];
                        $response["reg_date"] = $result["reg_date"];
                        $response["user_name"] = $result["user_name"];
                        $response["email"] = $result["email"];
                        $response["phoneno"] = $result["phoneno"];
                        $response["password"] = $result["mcdpassword"];
                        $response["target"] = $result["target"];
                        $response["gnews"] = $result["gnews"];
                        $response["fraud"] = $result["fraud"];
                        $response["referral"] = $result["referral"];
                        $response["last_login"] = $result["last_login"];
                        $response["account_number"] = $result["account_number"];
                        $response["wallet_msg"] = "For funding greater than #10,000, make use of Bank Transfer and send a mail referencing it. \nFor funding less than #2,000, there is #50 charges. \nFor funding to personal account, there is #50 charge.";
                        $response["data"] = 1;
                        $response["airtime"] = 1;
                        $response["paytv"] = 0;
                        $response["paytv2"] = 1;
                        $response["simswap"] = 1;
                        $response["freemoney"] = 1;
                        $response["resultchecker"] = 1;
                        $response["fund_rave"] = 0;
                        $response["fund_paystack"] = 1;
                        $response["fund_payant"] = 0;
                        $response["fund_carbon"] = 0;
                        $response["fund_bank"] = 1;
                        $response["fund_opay"] = 0;
                        $response["fund_monnify"] = 1;
                        $response["otp"] = 0;
                        $response["fund_carbon_details"] = "Phone No: 08166939205 \n Acct Name: Odejinmi Samuel A.";
                        $response["fund_opay_details"] = "Acct No: 8166939205 \n Acct Name: Odejinmi Samuel A. \n \t\t\t OR \n Acct No: 7064257276 \n Acct Name: Odejinmi Tolulopel A.";
                        $response["fund_bank_details"] = "Acct No: 9908105699 \n Acct Name: 5Star Company-Mega Cheap Data \n Bank: Providus Bank";
                        $response["fund_paystack_details"] = "pk_live_a4778c0a34672ca39da110fdc469e414b24faa47";
                        $response["fund_rave_details"] = "FLWPUBK-e7d86e7e856918baef8eab63b7b3dd81-X";
                        $response["fund_rave_key"] = "ebabe9a4d15eaba412d410eb";
                        $response["fund_monnify_apikey"] = "MK_PROD_3XFH7GR9D3";
                        $response["fund_monnify_contractcode"] = "256769172942";

                        $response["googleadvtid"] = "ca-app-pub-2803475602414413~4313083984";
                        $response["interpawonadvtid"] = "ca-app-pub-2803475602414413/8682492261";
                        $response["rewardadvtid"] = "ca-app-pub-2803475602414413/2117083919";
                        $response["bawonadvtid"] = "ca-app-pub-2803475602414413/7560982282";


                        if (isset($_GET["trans_amount"]) && $_GET["trans_amount"] != 0) {
                            if ($result["wallet"] >= $_GET["trans_amount"]) {
                                $response["trans_success"] = 1;
                            } else {
                                $response["trans_success"] = 0;
                            }

                        } else {
                            // mysql update row with matched user name
                            $date = date("Y-m-d H:i:s");
                            mysqli_query($con, "UPDATE tbl_agents SET last_login = '$date' WHERE user_name = '$uid'");
                        }

                        // get user transactions report from transactions table

                        $resulttf = mysqli_query($con, "SELECT count(*) FROM tbl_transactions WHERE user_name = '$uid' AND name = 'wallet funding' AND status = 'successful'");
                        $resulttf = mysqli_fetch_array($resulttf);

                        $resulttt = mysqli_query($con, "SELECT count(*) FROM tbl_transactions WHERE user_name = '$uid' AND status = 'delivered'");
                        $resulttt = mysqli_fetch_array($resulttt);


                        $response["total_fund"] = $resulttf["count(*)"];
                        $response["total_trans"] = $resulttt["count(*)"];

                        // echoing JSON response

                        //payment gateway-"pk_live_a4778c0a34672ca39da110fdc469e414b24faa47" 5star comp-"pk_live_bf9ad0c818ede7986e1f93198a1eb02eef57c7d9";
                        // "Acct No: 0248215384 \n Acct Name: Odejinmi Samuel A. \n Bank: GTBank \n \t\t\t OR \n Acct No: 3076302098 \n Acct Name: Odejinmi Tolulopel A. \n Bank: First Bank";

                        echo json_encode($response);

                    } else {

                        // no product found
                        $response["success"] = 0;

                        $response["message"] = "No agent found with that id";

                        // echo no users JSON

                        echo json_encode($response);
                    }

                }

            }


            //starting line for device id check
            if ($deviceid != "" && $uid == "") {
                $GLOBALS['found'] = 0;
                // get all user device first
                $resultd = mysqli_query($con, "SELECT `devices`, `user_name` FROM tbl_agents");
                // check for empty result
                if (mysqli_num_rows($resultd) > 0) {
                    // looping through all results
                    // products node
                    while ($row = mysqli_fetch_array($resultd)) {
                        // user devices per user
                        $r_device = $row["devices"];
                        $r_username = $row["user_name"];

                        // Assign JSON encoded string to a PHP variable
                        $json = $r_device;

                        if ($json != "") {
                            // Decode JSON data to PHP associative array
                            $arr = json_decode($json, true);

                            // Loop through the associative array
                            foreach ($arr as $key => $value) {
                                if ($value == $deviceid) {
                                    $f_username = $r_username;
                                    $GLOBALS['found'] = 1;
                                }
                            }
                        }//looping through the database and also checking for device id match for username
                    }

                    if ($GLOBALS['found'] != 1) {
                        $response["success"] = 0;
                        $response["message"] = "Device ID not found";
                        echo json_encode($response);
                    } else {
                        //Deviceid Found successfully
                        getAgent_config($f_username, $con, 103);
                    }
                }//end if mysql
                return false;
            }//end if deviceid & uid
            //lst line for d imported deviceid check

            //begin of line for normal login
            if ($deviceid != "" && $uid != "") {
                $GLOBALS['found'] = 0;
                // get all user device first
                $resultd = mysqli_query($con, "SELECT * FROM tbl_agents");
                // check for empty result
                if (mysqli_num_rows($resultd) > 0) {
                    // looping through all results
                    // products node
                    while ($row = mysqli_fetch_array($resultd)) {
                        // user devices per user
                        $r_device = $row["devices"];
                        $r_username = $row["user_name"];

                        // Assign JSON encoded string to a PHP variable
                        $json = $r_device;

                        if ($json != "") {
                            // Decode JSON data to PHP associative array
                            $arr = json_decode($json, true);

                            // Loop through the associative array
                            foreach ($arr as $key => $value) {
                                if ($value == $deviceid) {
                                    $GLOBALS['found'] = 1;
                                    $f_username = $r_username;
                                }
                            }
                        }
                    }//looping through the database and also checking for device id match

                    if ($GLOBALS['found'] != 1) {
                        $result = mysqli_query($con, "SELECT * FROM tbl_agents WHERE user_name = '$uid'");
                        if (!empty($result)) {
                            // check for empty result
                            if (mysqli_num_rows($result) > 0) {

                                $result = mysqli_fetch_array($result);

                                $e_device = $result["devices"];
                                $e_arr = json_decode($e_device, true);

                                //Device does not exist and has been linked to this user
                                getAgent_config($uid, $con, 102);

                                $date = date("Y-m-d H:i:s");
                                $array = array($date => $deviceid);

                                if ($e_device != "") {
                                    $arr = array_merge($e_arr, $array);
                                } else {
                                    $arr = $array;
                                }

                                $ar = json_encode($arr);

                                mysqli_query($con, "UPDATE tbl_agents SET devices = '$ar' WHERE user_name = '$uid'");
                            } else {

                                $response["success"] = 0;
                                $response["message"] = "No user found with that I.D";
                                // echo no users JSON
                                echo json_encode($response);
                            }
                        }
                    } else {
                        if ($f_username == $uid) {

                            //Device linked to this user
                            getAgent_config($uid, $con, 101);
                        } else {
                            $response["success"] = 0;
                            $response["message"] = "This device belongs to another user";
                            echo json_encode($response);
                        }
                    }
                }
            }

            //end of login code


        } else {

            // required field is missing

            $response["success"] = 0;

            $response["message"] = "Required field(s) is missing";


            // echoing JSON response

            echo json_encode($response);

        }


    }

    public function updateAgent(){

        ini_set('display_errors', 0);
        error_reporting(E_ALL);
        /* updated on 11/08/2019 by samji

         * Following code will get single product details

         * A product is identified by product id (uid)

         */
// array for JSON response

        date_default_timezone_set('Africa/Lagos');

        $response = array();

// include db connect class

        require_once __DIR__ . '/db_connect.php';

// connecting to db

        $con = connect();

        if (isset($_POST["uid"]) && isset($_POST['dob']) && $_POST["uid"] != "") {
            $uid = $_POST['uid'];

            $result = mysqli_query($con, "SELECT * FROM tbl_agents WHERE user_name = '$uid'");
            if (!empty($result)) {
                // check for empty result
                if (mysqli_num_rows($result) > 0) {
                    $result = mysqli_fetch_array($result);
                    //values gotten
                    $r_dob = $result["dob"];

                    if ($r_dob == "") {
                        $fullname = $_POST['full_name'];
                        $biz_name = $_POST["company_name"];
                        $dob = $_POST['dob'];
                        $bvn = $_POST["bvn"];
                        $address = $_POST["address"];
                        $email = $_POST["email"];
                        $target = $_POST["request"] . " in progress...";
                        $image = $_POST["image"];
                        $photo = $uid . ".JPG";

                        $decodedImage = base64_decode("$image");
                        $resultt = file_put_contents("avatar/" . $photo, $decodedImage);


                        mysqli_query($con, "UPDATE tbl_agents SET full_name = '$fullname', company_name = '$biz_name', dob= '$dob' , bvn= '$bvn', address= '$address', target='$target', photo='$photo' WHERE user_name = '$uid'");


                        $response["success"] = 1;
                        $response["user_name"] = $uid;
                        $response["message"] = "Data submitted successfully, kindly check your mail for progress";
                        echo json_encode($response);
                    } else {
                        $response["success"] = 0;
                        $response["user_name"] = $uid;
                        $response["message"] = "Data can only be submitted once.";
                        echo json_encode($response);
                    }
                } else {
                    // no produc t found
                    $response["success"] = 0;
                    $response["message"] = "No agent found with that id";
                    // echo no users JSON
                    echo json_encode($response);
                }
            }

        } else {
            // required field is missing
            $response["success"] = 0;
            $response["message"] = "Required field(s) is missing";
            // echoing JSON response
            echo json_encode($response);
        }
    }

    public function resetpassword(){

        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        // include db connect class

        require_once __DIR__ . '/db_connect.php';

        // connecting to db

        $con = connect();

        // mysql update row with matched user name

        $from = "5StarCompany info@5starcompany.com.ng";
        $to = $_GET['email'];
        $subject = $_GET['user_name'] . " request change of password";
        $message = "A new password was requested by user (" . $_GET['user_name'] . ") on Mega Cheap Data Platform. \n If this was done by you, then you can safely ignore this email while your request is under review but if it wasn't done by you, your account has been compromised. Please send a mail across to cancel the request immediately.";
        $message2 = "A new password was requested by user (" . $_GET['user_name'] . ") with email address " . $_GET['email'] . " on Mega Cheap Data Platform with password (" . $_GET['r_password'] . ") \n Following is the prove of ownership \n" . $_GET['description'];

        $pass = $_GET['r_password'];
        $user = $_GET['user_name'];

        mysqli_query($con, "UPDATE tbl_agents SET mcdpassword = '$pass' WHERE user_name = '$user'");

        $headers = "From:" . $from;

        // Sending email
        if (mail($to, $subject, $message, $headers) && mail("odejinmisamuel@gmail.com", $subject, $message2, $headers) && mail("odejinmiabraham@gmail.com", $subject, $message2, $headers)) {

            $response["success"] = 1;
            $response["message"] = "Your mail has been sent successfully.";
            // echoing JSON response
            echo json_encode($response);
        } else {
            $response["success"] = 1;
            $response["message"] = "Unable to send email. Please try again.";
            // echoing JSON response
            echo json_encode($response);
        }
    }

    public function social_login(){

        /*

         * Following code will get single product details

         * A product is identified by product id (uid)

         */

// array for JSON response

        $response = array();

// include db connect class

        require_once __DIR__ . '/db_connect.php';

// connecting to db

        $con = connect();

// check for post data

        if (isset($_GET["uid"])) {

            $uid = $_GET['uid'];

            // get a product from products table

            $result = mysqli_query($con, "SELECT * FROM tbl_agents WHERE email = '$uid'");

            if (!empty($result)) {

                // check for empty result

                if (mysqli_num_rows($result) > 0) {

                    $result = mysqli_fetch_array($result);

                    //populating values now
                    // success
                    $response["success"] = 1;
                    $response["message"] = "Social login successful";
                    //values gotten
                    $response["id"] = $result["id"];
                    $response["full_name"] = $result["full_name"];
                    $response["wallet"] = $result["wallet"];
                    $response["status"] = $result["status"];
                    $response["user_name"] = $result["user_name"];
                    $response["email"] = $result["email"];
                    $response["phoneno"] = $result["phoneno"];
                    $response["password"] = $result["password"];

                    echo json_encode($response);

                } else {

                    // no product found

                    $response["success"] = 0;

                    $response["message"] = "No agent found with that account";

                    // echo no users JSON

                    echo json_encode($response);

                }

            } else {

                // no product found

                $response["success"] = 0;

                $response["message"] = "No agent found";


                // echo no users JSON

                echo json_encode($response);

            }

        } else {

            // required field is missing

            $response["success"] = 0;

            $response["message"] = "Required field(s) is missing";


            // echoing JSON response

            echo json_encode($response);

        }
    }

    public function CreateMonnifyAcct($id){


            $u = User::find($id);

            if (!$u) {
                echo "invalid account";
            }

            if ($u->account_number != '0') {
                echo "Account created already";
            }

            try {

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => env("MONNIFY_URL") . "/auth/login",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Basic " . env("MONNIFY_AUTH")
                    ),
                ));

                $response = curl_exec($curl);
                $respons = $response;

                curl_close($curl);

//        $response='{"requestSuccessful":true,"responseMessage":"success","responseCode":"0","responseBody":{"accessToken":"eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOlsibW9ubmlmeS1wYXltZW50LWVuZ2luZSJdLCJzY29wZSI6WyJwcm9maWxlIl0sImV4cCI6MTU5MTQ5Nzc5OSwiYXV0aG9yaXRpZXMiOlsiTVBFX01BTkFHRV9MSU1JVF9QUk9GSUxFIiwiTVBFX1VQREFURV9SRVNFUlZFRF9BQ0NPVU5UIiwiTVBFX0lOSVRJQUxJWkVfUEFZTUVOVCIsIk1QRV9SRVNFUlZFX0FDQ09VTlQiLCJNUEVfQ0FOX1JFVFJJRVZFX1RSQU5TQUNUSU9OIiwiTVBFX1JFVFJJRVZFX1JFU0VSVkVEX0FDQ09VTlQiLCJNUEVfREVMRVRFX1JFU0VSVkVEX0FDQ09VTlQiLCJNUEVfUkVUUklFVkVfUkVTRVJWRURfQUNDT1VOVF9UUkFOU0FDVElPTlMiXSwianRpIjoiOTYyNTA5NzctMmZkOS00ZDM4LTliYzEtNTMyMTMwYmFiODc0IiwiY2xpZW50X2lkIjoiTUtfVEVTVF9LUFoyQjJUQ1hLIn0.iTOX9RWwA0zcLh3OsTtuFD-ehAbW1FrUcAZLM73V66_oTuV2jJ5wBjWNvyQToZKl2Rf5TH2UgiJyaapAZR6yU9Y4Di_oz97kq0CwpoFoe_rLmfgWgh-jrYEsrkj751jiQQm_vZ6BEw9OJhYtMBb1wEXtY4rFMC7I2CLmCnwpJaMWgrWnTRcoLZlPTcWGMBLeggaY9oLfIIorV9OTVkB2kihA9QHX-8oUGkYpvKyC9ERNYMURcK01LnPgSBWI7lXrjf8Ct2BjHi6RKdlFRPNpp3OAbN9Oautvwy09WS3XOhA8eycA0CNBh8o7jekVLCLjXgz6YrcMH0j9ahb3mPBr7Q","expiresIn":368}}';

                $response = json_decode($response, true);

                $token = $response['responseBody']['accessToken'];

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => env("MONNIFY_URL") . "/bank-transfer/reserved-accounts",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{\"accountReference\": \"" . $u->user_name . "\", \"accountName\": \"MCD-" . $u->user_name . "\",  \"currencyCode\": \"NGN\",  \"contractCode\": \"" . env('MONNIFY_CONTRACTCODE') . "\",  \"customerEmail\": \"" . $u->email . "\",  \"customerName\": \"MCD-" . $u->user_name . "\"}",
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                        "Authorization: Bearer " . $token
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $response = json_decode($response, true);

                $contract_code = $response['responseBody']['contractCode'];
                $account_reference = $response['responseBody']['accountReference'];
                $currency_code = $response['responseBody']['currencyCode'];
                $customer_email = $response['responseBody']['customerEmail'];
                $customer_name = $response['responseBody']['customerName'];
                $account_number = $response['responseBody']['accountNumber'];
                $bank_name = $response['responseBody']['bankName'];
                $collection_channel = $response['responseBody']['collectionChannel'];
                $status = $response['responseBody']['status'];
                $created_on = $response['responseBody']['createdOn'];
                $reservation_reference = $response['responseBody']['reservationReference'];
                $extra = $respons;

                DB::table('tbl_reserveaccount_monnify')->insert(['contract_code' => $contract_code, 'account_reference' => $account_reference, 'currency_code' => $currency_code, 'customer_email' => $customer_email, 'customer_name' => $customer_name, 'account_number' => $account_number, 'bank_name' => $bank_name, 'collection_channel' => $collection_channel, 'status' => $status, 'reservation_reference' => $reservation_reference, 'created_on' => $created_on, 'extra' => $extra]);

                $u->account_number = $account_number;
                $u->save();

                echo $account_number . "|| ";
            }catch (\Exception $e){
                echo "Error encountered ";
            }
    }

}
