<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\MCDAIServeTransJob;
use App\Models\Serverlog;
use App\Models\Transaction;
use App\Models\Wallet;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MCDAssistantController extends Controller
{
    function index(Request $request){
        $input = $request->all();

//        $data1= '{"responseId":"30e2cf1d-c429-4335-9201-b97da2d2f617-0820055c","queryResult":{"queryText":"the reference is mcd_transaction_13263839484757","action":"helptransaction.helptransaction-custom","parameters":{"transaction_reference":"mcd_transaction_13263839484757"},"allRequiredParamsPresent":true,"fulfillmentText":"Am checking it, i will give you feedback soon","fulfillmentMessages":[{"text":{"text":["Am checking it, i will give you feedback soon"]}}],"outputContexts":[{"name":"projects\/mcdvoice-kign\/agent\/sessions\/84e6e039-e696-487c-ede1-e21bdb9a50df\/contexts\/helptransaction-followup","lifespanCount":1,"parameters":{"transaction_reference":"mcd_transaction_13263839484757","transaction_reference.original":"mcd_transaction_13263839484757"}},{"name":"projects\/mcdvoice-kign\/agent\/sessions\/84e6e039-e696-487c-ede1-e21bdb9a50df\/contexts\/__system_counters__","parameters":{"no-input":0,"no-match":0,"transaction_reference":"mcd_transaction_13263839484757","transaction_reference.original":"mcd_transaction_13263839484757"}}],"intent":{"name":"projects\/mcdvoice-kign\/agent\/intents\/2ff5c48e-d989-4882-90cd-3535b6563143","displayName":"help.transaction - reference"},"intentDetectionConfidence":1,"languageCode":"en"},"originalDetectIntentRequest":{"payload":[]},"session":"projects\/mcdvoice-kign\/agent\/sessions\/84e6e039-e696-487c-ede1-e21bdb9a50df"}';
        $data1= json_encode($input);
        $data2= json_decode($data1);

        DB::table('test')->insert(['name'=> 'webhook', 'request'=>$request, 'data2'=>$data1]);

        if($data2->queryResult->action == "helptransaction.helptransaction-custom"){
            $this->transactionverify($data2);
        }else if($data2->queryResult->action == "helppaymentmethodreference.helppaymentmethodreference-yes"){
            $this->fundwalletverify($data2);
        }else if($data2->queryResult->action == "service_buydata.service_buydata-custom.service_buydata-details-yes"){
            $this->buydata($data2);
        }else if($data2->queryResult->action == "service_buyairtime.service_buyairtime-custom.service_buyairtime-details-yes"){
            $this->buyairtime($data2);
        }else if($data2->queryResult->action == "helppersonal-account.helppersonal-account-custom"){
            $this->account_details($data2);
        }else{
            $rep="Not configure to handle this request yet";
            echo '{"fulfillmentMessages": [{"text": {"text": ["'.$rep.'"]}}]}';
        }
    }

    private function transactionverify($data2){
        $ref=$data2->queryResult->parameters->transaction_reference;

        $tran=Transaction::where('ref', $ref)->first();

        if (!$tran){
            $rep="Transaction reference does not exit in our system";
        }else{
            $rep=$tran->description. " and it's status is ".$tran->status;
        }

        echo '{"fulfillmentMessages": [{"text": {"text": ["'.$rep.'"]}}]}';
    }

    private function fundwalletverify($data2){
        $ref=$data2->queryResult->outputContexts[0]->parameters->paymentreference;

        $tran=Transaction::where('ref', $ref)->first();

        if (!$tran){
            $wallet=Wallet::where('ref', $ref)->first();
            if($wallet) {
                $rep=$wallet->user_name . ", your funding reference is currently ". $wallet->status. ". Is there any other thing you want me to do for you?";
            }else{
                $rep = "Funding reference does not exit in our system. Kindly check and revert back or contact support @ 07011223737";
            }
        }else{
            $rep=$tran->user_name . ", your payment is successful and your wallet has been credited with the sum of ". $tran->amount .". Is there any other thing you want me to do for you?";
        }
        echo '{"fulfillmentMessages": [{"text": {"text": ["'.$rep.'"]}}]}';
    }

    private function buydata($data2){
        $user_name=$data2->queryResult->outputContexts[0]->parameters->username;
        $network=$data2->queryResult->outputContexts[0]->parameters->network;
        $dataplan=$data2->queryResult->outputContexts[0]->parameters->dataplan;
        $phoneno=$data2->queryResult->outputContexts[0]->parameters->{"phone-number"};
        $pin=$data2->queryResult->outputContexts[0]->parameters->pin;

        $u=User::where('user_name', $user_name)->first();

        if ($u){//checking if user exist
            if($u->pin=="1234") {//checking if pin is correct
                if ($u->pin == $pin) {//checking if pin is correct
                    if (strtolower(substr($network, 0, 1)) == strtolower(substr($dataplan, 0, 1))) {//checking if dataplan belongs to network
                        $p = DB::table("tbl_serverconfig_data")->where("coded", $dataplan)->first();
                        if ($p) {//checking if plan exist
                            if (strlen($phoneno) == 11 && is_numeric($phoneno)) {//checking if phone number is valid
                                if ($u->wallet >= $p->pricing) {
                                    $input['service'] = "data";
                                    $input['amount'] = $p->pricing;
                                    $input['phone'] = $phoneno;
                                    $input['network'] = $network;
                                    $input['date'] = Carbon::now();
                                    $input['user_name'] = $user_name;
                                    $input['transid'] = "mcd_ai_" . time();
                                    $input['ip_address'] = $_SERVER['REMOTE_ADDR'];
                                    $input['device_details'] = "MCD AI_" . $data2->responseId;
                                    $input['coded'] = $dataplan;
                                    $input['api'] = "mcd_app_9876234875356148750";
                                    $input['payment_method'] = "wallet";
                                    $input['wallet'] = $u->wallet;
                                    $input['version'] = "1.0";

                                    $s = Serverlog::create($input);
                                    $job = (new MCDAIServeTransJob($s->id))
                                        ->delay(Carbon::now()->addSeconds(1));
                                    dispatch($job);
                                    $rep = $user_name . ", your transaction is successful. Thanks for using Mega Cheap Data";
                                } else {
                                    if ($u->account_number != 0) {
                                        $rep = $user_name . ", insufficient amount. You can fund your wallet by transferring to " . $u->account_number . " Providus Bank. Kindly check and revert back or contact support @ 07011223737";
                                    } else {
                                        $rep = $user_name . ", insufficient amount. Visit the App to fund your wallet. Kindly check and revert back or contact support @ 07011223737";
                                    }
                                }
                            } else {
                                $rep = $user_name . ", invalid phone number supplied, expecting e.g 081433800X. Kindly check and revert back or contact support @ 07011223737";
                            }
                        } else {
                            $rep = $user_name . ", the plan is invalid, expecting m1, m2, m5, a1_5, n250, n1. Kindly choose the available plan and revert back or contact support @ 07011223737";
                        }
                    } else {
                        $rep = $user_name . ", the plan and network provided are not compatible. Kindly check and revert back or contact support @ 07011223737";
                    }
                } else {
                    $rep = $user_name . ", pin is incorrect, have you set your pin on the App. Kindly check and revert back or contact support @ 07011223737";
                }
            }else{
                $rep = $user_name . ", kindly change your pin on the app to continue your request.";
            }
        }else{
            $rep=$user_name . ", doesn't exist in our system, kindly get our app on playstore and registered with us at https://play.google.com/store/apps/details?id=a5starcompany.com.megacheapdata";
        }
        echo '{"fulfillmentMessages": [{"text": {"text": ["'.$rep.'"]}}]}';
    }

    private function buyairtime($data2){
        $user_name=$data2->queryResult->outputContexts[0]->parameters->username;
        $network=$data2->queryResult->outputContexts[0]->parameters->network;
        $amount=$data2->queryResult->outputContexts[0]->parameters->airtime_available;
        $phoneno=$data2->queryResult->outputContexts[0]->parameters->{"phone-number"};
        $pin=$data2->queryResult->outputContexts[0]->parameters->pin;

        $u=User::where('user_name', $user_name)->first();

        if ($u){//checking if user exist
            if($u->pin=="1234") {//checking if pin is correct
                if ($u->pin == $pin) {//checking if pin is correct
                    if (strlen($phoneno) == 11 && is_numeric($phoneno)) {//checking if phone number is valid
                        if ($u->wallet >= $amount) {
                            $input['service'] = "airtime";
                            $input['amount'] = $amount;
                            $input['phone'] = $phoneno;
                            $input['network'] = $network;
                            $input['date'] = Carbon::now();
                            $input['user_name'] = $user_name;
                            $input['transid'] = "mcd_ai_" . $user_name . time();
                            $input['ip_address'] = $_SERVER['REMOTE_ADDR'];
                            $input['device_details'] = "MCD AI_" . $data2->responseId;
                            $input['coded'] = strtoupper(substr($network, 0, 1));
                            $input['api'] = "mcd_app_9876234875356148750";
                            $input['payment_method'] = "wallet";
                            $input['wallet'] = $u->wallet;
                            $input['version'] = "1.0";

                            $s = Serverlog::create($input);
                            $job = (new MCDAIServeTransJob($s->id))
                                ->delay(Carbon::now()->addSeconds(1));
                            dispatch($job);
                            $rep = $user_name . ", your transaction is successful. Thanks for using Mega Cheap Data";
                        } else {
                            if ($u->account_number != 0) {
                                $rep = $user_name . ", insufficient amount. You can fund your wallet by transferring to " . $u->account_number . " Providus Bank. Kindly check and revert back or contact support @ 07011223737";
                            } else {
                                $rep = $user_name . ", insufficient amount. Visit the App to fund your wallet. Kindly check and revert back or contact support @ 07011223737";
                            }
                        }
                    } else {
                        $rep = $user_name . ", invalid phone number supplied, expecting e.g 081433800X. Kindly check and revert back or contact support @ 07011223737";
                    }
                } else {
                    $rep = $user_name . ", pin is incorrect, have you set your pin on the App. Kindly check and revert back or contact support @ 07011223737";
                }
            }else{
                $rep = $user_name . ", kindly change your pin on the app to continue your request.";
            }
        }else{
            $rep=$user_name . ", doesn't exist in our system, kindly get our app on playstore and registered with us at https://play.google.com/store/apps/details?id=a5starcompany.com.megacheapdata";
        }
        echo '{"fulfillmentMessages": [{"text": {"text": ["'.$rep.'"]}}]}';
    }

    private function account_details($data2){
        $user_name=$data2->queryResult->outputContexts[0]->parameters->username;

        $u=User::where('user_name', $user_name)->first();

        if ($u){//checking if user exist
            if($u->account_number!=0){
                $rep = $user_name . ", your personal MCD Account number is " . $u->account_number . ". Is there any other thing you will like me to do for you?";
            }else{
                $rep=$user_name . ", personal account has not been created for you. Kindly contact support @ 07011223737";
            }
        }else{
            $rep=$user_name . ", doesn't exist in our system, kindly get our app on playstore and registered with us at https://play.google.com/store/apps/details?id=a5starcompany.com.megacheapdata";
        }
        echo '{"fulfillmentMessages": [{"text": {"text": ["'.$rep.'"]}}]}';
    }
}
