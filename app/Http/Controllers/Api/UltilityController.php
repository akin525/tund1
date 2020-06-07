<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\logvoice;
use App\model\PndL;
use App\model\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UltilityController extends Controller
{
    public function mcd_logvoice(Request $request){

        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'name' => 'required',
            'voice' => 'required',
            'page' => 'required',
            'code' => 'required',
            'version' => 'required',
            'device_details' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            try {
               logvoice::create($input);
                return response()->json(['status'=> 1, 'message'=>'Voice logged Successfully']);
            }catch(\Exception $e){
                return response()->json(['status'=> 0, 'message'=>'Error logging voice','error' => $e]);
            }
        }else{
            return response()->json(['status'=> 0, 'message'=>'Error logging voice', 'error' => $validator->errors()]);
        }

    }

    public function monnifyRA($id){

        $u=User::find($id);

        if(!$u){
            return "invalid account";
        }


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env("MONNIFY_URL")."/auth/login",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic ". env("MONNIFY_AUTH")
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

//        $response='{"requestSuccessful":true,"responseMessage":"success","responseCode":"0","responseBody":{"accessToken":"eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOlsibW9ubmlmeS1wYXltZW50LWVuZ2luZSJdLCJzY29wZSI6WyJwcm9maWxlIl0sImV4cCI6MTU5MTQ5Nzc5OSwiYXV0aG9yaXRpZXMiOlsiTVBFX01BTkFHRV9MSU1JVF9QUk9GSUxFIiwiTVBFX1VQREFURV9SRVNFUlZFRF9BQ0NPVU5UIiwiTVBFX0lOSVRJQUxJWkVfUEFZTUVOVCIsIk1QRV9SRVNFUlZFX0FDQ09VTlQiLCJNUEVfQ0FOX1JFVFJJRVZFX1RSQU5TQUNUSU9OIiwiTVBFX1JFVFJJRVZFX1JFU0VSVkVEX0FDQ09VTlQiLCJNUEVfREVMRVRFX1JFU0VSVkVEX0FDQ09VTlQiLCJNUEVfUkVUUklFVkVfUkVTRVJWRURfQUNDT1VOVF9UUkFOU0FDVElPTlMiXSwianRpIjoiOTYyNTA5NzctMmZkOS00ZDM4LTliYzEtNTMyMTMwYmFiODc0IiwiY2xpZW50X2lkIjoiTUtfVEVTVF9LUFoyQjJUQ1hLIn0.iTOX9RWwA0zcLh3OsTtuFD-ehAbW1FrUcAZLM73V66_oTuV2jJ5wBjWNvyQToZKl2Rf5TH2UgiJyaapAZR6yU9Y4Di_oz97kq0CwpoFoe_rLmfgWgh-jrYEsrkj751jiQQm_vZ6BEw9OJhYtMBb1wEXtY4rFMC7I2CLmCnwpJaMWgrWnTRcoLZlPTcWGMBLeggaY9oLfIIorV9OTVkB2kihA9QHX-8oUGkYpvKyC9ERNYMURcK01LnPgSBWI7lXrjf8Ct2BjHi6RKdlFRPNpp3OAbN9Oautvwy09WS3XOhA8eycA0CNBh8o7jekVLCLjXgz6YrcMH0j9ahb3mPBr7Q","expiresIn":368}}';

        $response=json_decode($response, true);

        $token=$response['responseBody']['accessToken'];
        echo $token;


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env("MONNIFY_URL")."/bank-transfer/reserved-accounts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"accountReference\": \"".$u->user_name."\", \"accountName\": \"".$u->user_name."\",  \"currencyCode\": \"NGN\",  \"contractCode\": \"".env('MONNIFY_CONTRACTCODE')."\",  \"customerEmail\": \"".$u->email."\",  \"customerName\": \"".$u->user_name."\"}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " .$token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

        $response=json_decode($response, true);

        $contract_code=$response['responseBody']['contractCode'];
        $account_reference=$response['responseBody']['accountReference'];
        $currency_code=$response['responseBody']['currencyCode'];
        $customer_email=$response['responseBody']['customerEmail'];
        $customer_name=$response['responseBody']['customerName'];
        $account_number=$response['responseBody']['accountNumber'];
        $bank_name=$response['responseBody']['bankName'];
        $collection_channel=$response['responseBody']['collectionChannel'];
        $status=$response['responseBody']['status'];
        $created_on=$response['responseBody']['createdOn'];
        $reservation_reference=$response['responseBody']['reservationReference'];
        $extra=json_decode($response);

        DB::table('tbl_reserveaccount_monnify')->insert(['	contract_code'=> $contract_code, 'account_reference'=>$account_reference, 'currency_code'=>$currency_code, 'customer_email'=> $customer_email, 'customer_name'=>$customer_name, 'account_number'=>$account_number, 'bank_name'=> $bank_name, 'collection_channel'=>$collection_channel, 'status'=>$status, 'reservation_reference'=> $reservation_reference, 'created_on'=>$created_on, 'extra'=>$extra]);

    }

    function monnifyhook(Request $request){
        $input = $request->all();

        $data2= json_encode($input);

        $paymentstatus= $input['paymentStatus'];
        $transactionreference= $input['transactionReference'];
        $paymentreference= $input['paymentReference'];
        $paymentamount= $input['amountPaid'];
        $paymentmethod= $input['paymentMethod'];
        $paymentdesc =$input['paymentDescription'];
        $paidon= $input['paidOn'];
        $product_type= $input['product']['type'];
        $product_reference= $input['product']['reference'];
        $transactionhash= $input['transactionHash'];
        $transactionhashME= hash('SHA512', env("MONNIFY_CLIENTSECRET")."|". $paymentreference."|". $paymentamount ."|".$paidon."|".$transactionreference);
        $paymentamount= (int)$input['amountPaid'];

//        echo $transactionhashME;

        DB::table('tbl_webhook_monnify')->insert(['payment_reference'=> $paymentreference, 'transaction_reference'=>$transactionreference, 'amount'=>$paymentamount, 'payment_method'=> $paymentmethod, 'product_type'=>$product_type, 'product_reference'=>$paymentreference, 'transaction_hash'=> $transactionhash, 'transaction_hashME'=>$transactionhashME, 'payment_desc'=>$paymentdesc, 'extra'=>$data2]);


        if($paymentstatus !== "PAID"){
            return "!Paid transaction";
        }

        if($transactionhash != $transactionhashME){
            return "Invalid transaction signature";
        }

        if($product_type === "RESERVED_ACCOUNT"){
            $acctd_name= $input['accountDetails']['accountName'];
            if ($product_reference === "Mcdat"){
                $this->MCDatfundwallet($acctd_name,$paymentamount);
            }else{
                $this->RAfundwallet($acctd_name,$paymentamount,$product_reference);
            }
        }


        DB::table('test')->insert(['name'=> 'webhook', 'request'=>$request, 'data2'=>$data2]);

        echo "success";
    }

    public function MCDatfundwallet($name, $amount){
        $charge_treshold=2000;
        $charges=50;
        $u=User::where('full_name', '=', $name)->first();

        if($u){
            $input['name']="wallet funding";
            $input['amount']=$amount;
            $input['status']='successful';
            $input['description']= $u->user_name .' wallet funded using Bank Transfer with the sum of #'.$amount;
            $input['user_name']=$u->user_name;
            $input['code']='afund_Bank Transfer';
            $input['i_wallet']=$u->wallet;
            $wallet=$u->wallet;
            $input['f_wallet']=$u->wallet + $amount;
            $input["ip_address"]="127.0.0.1:A";
            $input["date"]=date("y-m-d H:i:s");

            Transaction::create($input);

            if($amount<$charge_treshold){
                $input["type"]="income";
                $input["amount"]=$charges;
                $input["narration"]="Being amount charged for funding less than #".$charge_treshold." from ".$u->user_name;

                PndL::create($input);

                $input["description"]="Being amount charged for funding less than #".$charge_treshold;
                $input["name"]="Auto Charge";
                $input["code"]="ac50";
                $input['status']='successful';
                $input["i_wallet"]=$wallet;
                $input["f_wallet"]=$input["i_wallet"] - $charges;

                Transaction::create($input);

                $wallet-=$charges;
            }

            $u->wallet=$wallet;
            $u->save();
        }

    }

    public function RAfundwallet($name, $amount, $reference){
        $charge_treshold=2000;
        $charges=50;
        $u=User::where('user_name', '=', $reference)->first();

        if($u){
            $input['name']="wallet funding";
            $input['amount']=$amount;
            $input['status']='successful';
            $input['description']= $u->user_name .' wallet funded using Account Transfer('.$u->account_number .') with the sum of #'.$amount. ' from '. $name;
            $input['user_name']=$u->user_name;
            $input['code']='afund_Bank Transfer';
            $input['i_wallet']=$u->wallet;
            $wallet=$u->wallet;
            $input['f_wallet']=$u->wallet + $amount;
            $input["ip_address"]="127.0.0.1:A";
            $input["date"]=date("y-m-d H:i:s");

            Transaction::create($input);

            $input["type"]="income";
            $input["amount"]=$charges;
            $input['status']='successful';
            $input["narration"]="Being amount charged for using automated funding from ".$input["user_name"];

            PndL::create($input);

            $input["description"]="Being amount charged for using automated funding";
            $input["name"]="Auto Charge";
            $input["code"]="af50";
            $input["i_wallet"]=$wallet;
            $input["f_wallet"]=$input["i_wallet"] - $charges;
            $wallet=$input["f_wallet"];

            Transaction::create($input);

            if($amount<$charge_treshold){
                $input["type"]="income";
                $input["amount"]=$charges;
                $input["narration"]="Being amount charged for funding less than #".$charge_treshold." from ".$input["user_name"];

                PndL::create($input);

                $input["description"]="Being amount charged for funding less than #".$charge_treshold;
                $input["name"]="Auto Charge";
                $input["code"]="ac50";
                $input["i_wallet"]=$wallet;
                $input["f_wallet"]=$input["i_wallet"] - $charges;

                Transaction::create($input);

                $wallet-=$charges;
            }

            $u->wallet=$wallet;
            $u->save();
        }

    }

    function hook(Request $request){
        $input = $request->all();

//        $data1= implode($input);
        $data2= json_encode($input);

        DB::table('test')->insert(['name'=> 'webhook', 'request'=>$request, 'data2'=>$data2]);

        echo "success";
    }
}



/*{
    "transactionReference": "MNFY|20200606202911|015526",
    "paymentReference": "MNFY|20200606202911|015526",
    "amountPaid": "1700.00",
    "totalPayable": "1700.00",
    "settlementAmount": "1686.29",
    "paidOn": "06/06/2020 08:29:17 PM",
    "paymentStatus": "PAID",
    "paymentDescription": "Mega Cheap Data",
    "transactionHash": "ea4017c38add1cf770a15ec6977267051751531694058339e1b14e4977a1daeb0dcecbbef846fb045c841ca5e3edeaf1f44e06ec9db3f0b24aab79870c6ef96d",
    "currency": "NGN",
    "paymentMethod": "ACCOUNT_TRANSFER",
    "product": {
        "type": "RESERVED_ACCOUNT",
        "reference": "Mcdat"
    },
    "cardDetails": null,
    "accountDetails": {
        "accountName": "SOHENOU SEWAN ESTHER",
        "accountNumber": "******5166",
        "bankCode": "000016",
        "amountPaid": "1700.00"
    },
    "accountPayments": [
        {
            "accountName": "SOHENOU SEWAN ESTHER",
            "accountNumber": "******5166",
            "bankCode": "000016",
            "amountPaid": "1700.00"
        }
    ],
    "customer": {
        "email": "info@5starcompany.com.ng",
        "name": "5Star Inn Company"
    },
    "metaData": []
}


{
    "transactionReference": "MNFY|20200606175538|014351",
    "paymentReference": "MegaCheapData_fundwallet_1591462450234",
    "amountPaid": "2500.00",
    "totalPayable": "2500.00",
    "settlementAmount": "2467.75",
    "paidOn": "06/06/2020 05:58:18 PM",
    "paymentStatus": "PAID",
    "paymentDescription": "Grasun Telecoms fund wallet with this user email - grasun83@gmail.com",
    "transactionHash": "e579b0ab2ef55623a8c70dd90cdb2f45fee9b304b61d75cae2b33d794fcf6e5e0bcb91eb281ea16544a51d19a7efbcd97a74587152c65ef2ffdd0cab2a5973b3",
    "currency": "NGN",
    "paymentMethod": "CARD",
    "product": {
    "type": "MOBILE_SDK",
        "reference": "MegaCheapData_fundwallet_1591462450234"
    },
    "cardDetails": {
    "cardType": null,
        "authorizationCode": null,
        "last4": "9229",
        "expMonth": "10",
        "expYear": "22",
        "bin": "418745",
        "bankCode": null,
        "bankName": null,
        "reusable": false,
        "countryCode": null,
        "cardToken": null
    },
    "accountDetails": null,
    "accountPayments": [],
    "customer": {
    "email": "grasun83@gmail.com",
        "name": "Grasun Telecoms MegaCheapData"
    },
    "metaData": []
}*/
