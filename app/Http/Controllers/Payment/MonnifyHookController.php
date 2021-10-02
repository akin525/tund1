<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Jobs\SendoutMonnifyHookJob;
use App\Models\PndL;
use App\Models\Serverlog;
use App\Models\Transaction;
use App\Models\Wallet;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonnifyHookController extends Controller
{
    public function index(Request $request){
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

        $cfee=$input['totalPayable']-$input['settlementAmount'];

        if($product_type === "MOBILE_SDK"){
            $this->SDK($paymentamount, $paymentreference, $cfee);
        }

        if($product_type === "RESERVED_ACCOUNT"){
            if($input['accountDetails']== null){
                $acctd_name=$product_reference;
            }else{
                $acctd_name= $input['accountDetails']['accountName'];
            }
            if ($product_reference === "Mcdat"){
                $this->MCDatfundwallet($acctd_name,$paymentamount,$transactionreference, $cfee);
            }else{
                $this->RAfundwallet($acctd_name, $paymentamount, $product_reference, $transactionreference, $cfee, $input);
            }
        }

        return "success";
    }

    private function MCDatfundwallet($name, $amount, $transactionreference, $cfee){
        $charge_treshold=2000;
        $charges=50;
        $u=User::where('full_name', 'LIKE', '%'.$name.'%')->first();

        if($u){
            $input['name']="wallet funding";
            $input['amount']=$amount;
            $input['status']='successful';
            $input['description']= $u->user_name .' wallet funded using Bank Transfer with the sum of #'.$amount;
            $input['user_name']=$u->user_name;
            $input['code']='afund_Bank Transfer';
            $input['i_wallet']=$u->wallet;
            $wallet=$u->wallet + $amount;
            $input['f_wallet']=$wallet;
            $input["ip_address"]="127.0.0.1:A";
            $input["ref"]=$transactionreference;
            $input["date"]=date("y-m-d H:i:s");

            Transaction::create($input);

            if($amount<$charge_treshold){
                $input["type"]="income";

                $input["gl"]="MCD Account";
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


            if($cfee!=0){
                $input["type"]="expenses";
                $input["amount"]=$cfee;
                $input["narration"]="Payment gateway charges on MCD account with ref ".$transactionreference;

                PndL::create($input);
            }

            $u->wallet=$wallet;
            $u->save();
        }

    }

    private function RAfundwallet($name, $amount, $reference, $transactionreference, $cfee, $input)
    {
        $charges = 50;
        $u = User::where('user_name', '=', $reference)->first();
        $w = Wallet::where('ref', $transactionreference)->first();

        if ($reference == "emmext") {
            //send out notification  to efe mobile money

            echo "na efe mobile money get am";

            $job = (new SendoutMonnifyHookJob($input))
                ->delay(Carbon::now()->addSeconds(1));
            dispatch($job);

            return;
        }

        if (!$w) {
            if ($u) {
                $input['name'] = "wallet funding";
                $input['amount'] = $amount;
                $input['status'] = 'successful';
                $input['description'] = $u->user_name . ' wallet funded using Account Transfer(' . $u->account_number . ') with the sum of #' . $amount . ' from ' . $name;
                $notimssg = $u->user_name . ' wallet funded using Account Transfer(' . $u->account_number . ') with the sum of #' . $amount . ' from ' . $name;
                $input['user_name'] = $u->user_name;
                $input['code'] = 'afund_Personal Account';
                $input['i_wallet'] = $u->wallet;
                $wallet = $u->wallet + $amount;
                $input['f_wallet'] = $wallet;
                $input["ip_address"] = "127.0.0.1:A";
                $input["ref"] = $transactionreference;
                $input["date"] = Carbon::now();

                Transaction::create($input);

                $input["type"] = "income";
                $input["gl"]="Personal Account";
                $input["amount"] = $charges;
                $input['status'] = 'successful';
                $input["narration"] = "Being amount charged for using automated funding from " . $input["user_name"];

                PndL::create($input);

                $input["description"] = "Being amount charged for using automated funding";
                $input["name"] = "Auto Charge";
                $input["code"] = "af50";
                $input["i_wallet"] = $wallet;
                $input["f_wallet"] = $input["i_wallet"] - $charges;
                $wallet = $input["f_wallet"];

                Transaction::create($input);

                $u->wallet = $wallet;
                $u->save();

                $input['user_name'] = $u->user_name;
                $input['amount'] = $amount;
                $input['medium'] = "Personal Account";
                $input['o_wallet'] = $input["f_wallet"] - $amount - 50;
                $input['n_wallet'] = $input["f_wallet"];
                $input['ref'] = $transactionreference;
                $input['version'] = "2";
                $input['status'] = "completed";
                $input['deviceid'] = $input['code'];
                Wallet::create($input);

                if($cfee!=0){
                    $input["type"]="expenses";
                    $input["amount"]=$cfee;
                    $input["narration"]="Payment gateway charges on personal account with ref ".$transactionreference;

                    PndL::create($input);
                }

                $noti = new PushNotificationController();
                $noti->PushNoti($input['user_name'], $notimssg, "Account Transfer Successful");
            }
        }else{
            echo "Already created ";
        }
    }

    private function SDK($amount, $reference, $cfee){

        $tra=Serverlog::where('transid',$reference)->first();
        if($tra){
            if ($tra->status!="completed") {
                $tra->status = 'completed';
                $tra->save();

                $atm=new ATMmanagerController();
                $atm->atmtransactionserve($tra->id);
            }
        }

        $fun=Wallet::where('ref',$reference)->first();
        if($fun){
            if ($fun->status!="completed") {
                $fun->status='completed';
                $fun->save();

                $at=new ATMmanagerController();
                $at->atmfundwallet($fun, $amount, $reference, "Monnify", $cfee);
            }
        }
        echo "no way forward";
    }


    public function monnifyRA($id){

        $last=$id+1000;

        for ($i=$id; $i<=$last; $i++){
            echo "<br />";
            echo $i . "-";

            $u = User::find($i);

            if (!$u) {
                echo "invalid account";
                continue;
            }

            if ($u->account_number != '0') {
                echo "Account created already";
                continue;
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
            } catch (Exception $e) {
                echo "Error encountered ";
                continue;
            }

        }
        return "success";

    }

}
