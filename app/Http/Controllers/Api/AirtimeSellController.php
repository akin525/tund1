<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Serverlog;
use App\Model\Wallet;
use http\Env;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AirtimeSellController extends Controller
{
    public function server5($amnt, $phone, $transid, $input){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('SERVER5'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
   "country": "NG",
   "customer": "'.$phone.'",
   "amount": '.$amnt.',
   "recurrence": "ONCE",
   "type": "AIRTIME",
   "reference": "'.$transid.'"
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' .env('RAVE_SECRET_KEY'),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $rep=json_decode($response);

        $tran=new ServeRequestController();

        if($rep['status']=='success'){
            $tran->addtrans("server5",$response,$amnt,1,$transid,$input);
        }else {
            $tran->addtrans("server5",$response,$amnt,0,$transid,$input);
        }
    }
}
