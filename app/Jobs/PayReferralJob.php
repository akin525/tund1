<?php

namespace App\Jobs;

use App\Model\PndL;
use App\Model\ReferralPlans;
use App\Model\Transaction;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PayReferralJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $input, $tr, $userid;

    public function __construct($input, $tr, $userid)
    {
        $this->input = $input;
        $this->tr = $tr;
        $this->userid = $userid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $input = $this->input;
        $tr = $this->tr;
        $ruser = User::find($this->userid);

        $rp = ReferralPlans::where('name', $ruser->referral_plan)->first();

        $data = $rp->data_bonus;
        $airtime = $rp->airtime_bonus;
        $paytv = $rp->tv_bonus;

        $price = $input['amount'];


        if ($input['service'] == "airtime") {
            $am = $price * $airtime;
            $amount = round($am/100);
        } else if ($input['service'] == "data") {
            $amount = $data;
        } else if ($input['service'] == "paytv") {
            $am = $price * $paytv;
            $amount = round($am/100);
        }

        echo $amount;

        if ($amount > 0) {
            $tr['name'] = "Referral Bonus";
            $tr['description'] = "Being referral bonus on " . $tr['description'];
            $tr['code'] = "rc_" . $input['service'] . "_" . $input['coded'];
            $tr['amount'] = $amount;
            $tr['status'] = "successful";
            $tr['user_name'] = $ruser->user_name;
            $tr['i_wallet'] = $ruser->bonus;
            $tr['f_wallet'] = $ruser->bonus + $amount;
            Transaction::create($tr);

            $ruser->bonus = $tr['f_wallet'];
            $ruser->save();

            $input["type"]="expenses";
            $input["gl"]="Referral Bonus";
            $input["amount"]=$amount;
            $input["narration"]="Being referral bonus paid to ".$ruser->user_name. " on ".$input['transid'];

            PndL::create($input);
        }
    }
}
