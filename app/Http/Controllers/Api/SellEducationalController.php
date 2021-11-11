<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reseller\PayController;
use App\Jobs\SendEducationtoEmailJob;
use App\Models\Educational;
use Carbon\Carbon;

class SellEducationalController extends Controller
{
    public function server1($transid, $input, $requester)
    {

        $rac = Educational::where([["exam", strtoupper($input['type'])], ['qty', $input['quantity']]])->first();

        if (!$rac) {
            return null;
        }

        if (env('FAKE_TRANSACTION', 1) == 0) {

            $url = env('SERVER1N') . strtolower($input['type']) . env('SERVER1N_AUTH') . "&product_code=" . $rac->code . "&price=" . $rac->price . "&trans_id=" . $transid;
            // Perform transaction/initialize on our server to buy
            $response = file_get_contents($url);

        } else {
            if (strtolower($input['type']) == "neco") {
                $response = '{"trans_id":"1282211217008803","details":{"service":"neco","package":"One piece of neco result checker","tokens":[{"token":"1274927349283"}],"price":"650","status":"SUCCESSFUL","balance":"13816"}}';
            } else {
                $response = '{"trans_id":"1282211217008803","details":{"service":"WAEC","package":"One piece of waec result checker","pins":[{"serial_number":"WRC11102189209","pin":"1274927349283"},{"serial_number":"WRC11102189209","pin":"1274927349283"}],"price":"1700","status":"SUCCESSFUL","balance":"13816"}}';
            }

        }

        $rep = json_decode($response, true);

        $tran = new ServeRequestController();
        $rs = new PayController();
        $ms = new V2\PayController();

        $input['server_response'] = $response;

        if (strtolower($input['type']) == "neco") {
            $input['token'] = $rep['details']['tokens'];
        } else {
            $input['token'] = $rep['details']['pins'];
        }

        $input['transid'] = $transid;

        if (isset($rep['trans_id'])) {
            if ($requester == "mcd") {
                $job = (new SendEducationtoEmailJob($input))
                    ->delay(Carbon::now()->addSeconds(1));
                dispatch($job);
            }
        }

        return null;
    }
}
