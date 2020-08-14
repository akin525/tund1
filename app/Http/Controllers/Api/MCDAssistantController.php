<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\model\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MCDAssistantController extends Controller
{
    function assistantHook(Request $request){
        $input = $request->all();

//        $data1= '{"responseId":"30e2cf1d-c429-4335-9201-b97da2d2f617-0820055c","queryResult":{"queryText":"the reference is mcd_transaction_13263839484757","action":"helptransaction.helptransaction-custom","parameters":{"transaction_reference":"mcd_transaction_13263839484757"},"allRequiredParamsPresent":true,"fulfillmentText":"Am checking it, i will give you feedback soon","fulfillmentMessages":[{"text":{"text":["Am checking it, i will give you feedback soon"]}}],"outputContexts":[{"name":"projects\/mcdvoice-kign\/agent\/sessions\/84e6e039-e696-487c-ede1-e21bdb9a50df\/contexts\/helptransaction-followup","lifespanCount":1,"parameters":{"transaction_reference":"mcd_transaction_13263839484757","transaction_reference.original":"mcd_transaction_13263839484757"}},{"name":"projects\/mcdvoice-kign\/agent\/sessions\/84e6e039-e696-487c-ede1-e21bdb9a50df\/contexts\/__system_counters__","parameters":{"no-input":0,"no-match":0,"transaction_reference":"mcd_transaction_13263839484757","transaction_reference.original":"mcd_transaction_13263839484757"}}],"intent":{"name":"projects\/mcdvoice-kign\/agent\/intents\/2ff5c48e-d989-4882-90cd-3535b6563143","displayName":"help.transaction - reference"},"intentDetectionConfidence":1,"languageCode":"en"},"originalDetectIntentRequest":{"payload":[]},"session":"projects\/mcdvoice-kign\/agent\/sessions\/84e6e039-e696-487c-ede1-e21bdb9a50df"}';
        $data1= json_encode($input);
        $data2= json_decode($input);

        DB::table('test')->insert(['name'=> 'webhook', 'request'=>$request, 'data2'=>$data1]);

        if($data2->queryResult->action == "helptransaction.helptransaction-custom"){
            $ref=$data2->queryResult->parameters->transaction_reference;

            $tran=Transaction::where('ref', $ref)->first();

            if (!$tran){
                $rep="Transaction reference does not exit in our system";
            }else{
                $rep=$tran->description;
            }

        }else{
            $rep="Not configure to handle this request yet";
        }

        return '{
  "fulfillmentMessages": [
    {
      "text": {
        "text": [
          '.$rep.'
        ]
      }
    }
  ]
}';
    }
}
