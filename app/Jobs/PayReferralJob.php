<?php

namespace App\Jobs;

use App\Model\ReferralPlans;
use App\model\Transaction;
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
        $input=$this->input;
        $tr = $this->tr;
        $ruser = User::where('user_name', $this->userid)->first();

        $rp=ReferralPlans::where('name', referral_plan)->first();

        $data = $rp->data_bonus;
        $airtime = $rp->airtime_bonus;
        $paytv = $rp->tv_bonus;


                    if ($input['service'] == "airtime") {
                        $am = $price * $airtime;
                        $amount = round($am);
                    } else if ($input['service'] == "data") {
                        $amount = $data;
                    } else if ($input['service'] == "paytv") {
                        $am = $price * $paytv;
                        $amount = roud($am);
                    }

                    if ($amount > 0) {
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
                    }
    }
}
