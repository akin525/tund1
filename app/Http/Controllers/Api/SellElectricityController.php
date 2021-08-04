<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reseller\PayController;

class SellElectricityController extends Controller
{
    public function server6($request, $code, $phone, $transid, $net, $input, $dada, $requester)
    {

        $response = '{ "code":"00", "response_description":"TRANSACTION SUCCESSFUL", "requestId":"SAND0192837465738253A1HSD", "transactionId":"1563873435424", "amount":"50.00", "transaction_date":{ "date":"2019-07-23 10:17:16.000000", "timezone_type":3, "timezone":"Africa/Lagos" }, "purchased_code":"" }';

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();

        if ($rep['code'] == '000') {
            if ($requester == "reseller") {
                return $rs->buyElectricityOutput($request, $transid, 1, $dada);
            } else {
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }else {
            if ($requester == "reseller") {
                return $rs->buyElectricityOutput($request, $transid, 0, $dada);
            } else {
//                $tran->addtrans("server6",$response,$amnt,1,$transid,$input);
            }
        }
    }
}
