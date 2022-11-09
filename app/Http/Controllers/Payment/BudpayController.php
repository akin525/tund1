<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PushNotificationController;
use App\Jobs\KorapayHookJob;
use App\Jobs\NewAccountGiveaway;
use App\Jobs\SendoutMonnifyHookJob;
use App\Models\PndL;
use App\Models\Serverlog;
use App\Models\Transaction;
use App\Models\VirtualAccountClient;
use App\Models\Wallet;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BudpayController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();

        $data2 = json_encode($input);

        DB::table('tbl_webhook_budpay')->insert(['payment_reference' => $input['data']['reference'] ?? '', 'payment_id' => $input['data']['id'], 'status' => $input['data']['status'], 'amount' => $input['data']['amount'], 'fees' => $input['data']['fees'], 'remote_address' => $_SERVER['REMOTE_ADDR'], 'extra' => $data2]);


        if ($input['notify'] != "transaction") {
            return "transaction expected";
        }


        if ($input['notifyType'] != "successful") {
            return "successful expected";
        }

        $email=$input['data']['customer']['email'];

        $u=User::where('email', $email)->first();

        if(!$u){
            return "User not found";
        }

        $status = $input['data']['status'];
        $reference = $input['data']['reference'];
        $amount = $input['data']['amount'];
        $fee = $input['data']['fees'];

        if ($status != "success") {
            return "Success status expected";
        }

        $tra = Serverlog::where('transid', $reference)->first();
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
                $at->atmfundwallet($fun, $amount, $reference, "Budpay", $input['data']['fees']);
            }
        }


        $this->RAfundwallet($u,$amount,$reference,$fee);

        return "success";
    }

    private function RAfundwallet($u, $amount, $transactionreference, $cfee)
    {
        $charges = 0;
        $w = Wallet::where('ref', $transactionreference)->first();

        if (!$w) {
            $input['name'] = "wallet funding";
            $input['amount'] = $amount;
            $input['status'] = 'successful';
            $input['description'] = $u->user_name . ' wallet funded using Account Transfer(' . $u->account_number . ') with the sum of #' . $amount;
            $notimssg = $u->user_name . ' wallet funded using Account Transfer(' . $u->account_number . ') with the sum of #' . $amount;
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

            if($charges != 0) {
                $input["description"] = "Being amount charged for using automated funding";
                $input["name"] = "Auto Charge";
                $input["code"] = "af50";
                $input["i_wallet"] = $wallet;
                $input["f_wallet"] = $input["i_wallet"] - $charges;
                $wallet = $input["f_wallet"];

                Transaction::create($input);
            }

            $u->wallet = $wallet;
            $u->save();

            $input['user_name'] = $u->user_name;
            $input['amount'] = $amount;
            $input['medium'] = "Personal Account";
            $input['o_wallet'] = $input["f_wallet"] - $amount - $charges;
            $input['n_wallet'] = $input["f_wallet"];
            $input['ref'] = $transactionreference;
            $input['version'] = "2";
            $input['status'] = "completed";
            $input['deviceid'] = $input['code'];
            Wallet::create($input);

            if ($cfee != 0) {
                $input["type"] = "expenses";
                $input["amount"] = $cfee;
                $input["narration"] = "Payment gateway charges on personal account with ref " . $transactionreference;

                PndL::create($input);
            }

            $noti = new PushNotificationController();
            $noti->PushPersonal($u->user_name, $notimssg, "Account Transfer Successful");

        }else{
            echo "Already credited ";
        }
    }
}
