<?php

namespace App\Jobs;

use App\Mail\TransactionNotificationMail;
use App\Models\PndL;
use App\Models\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ServeRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $input, $status, $tr, $userid;

    public function __construct($input, $status, $tr, $userid)
    {
        $this->input = $input;
        $this->status = $status;
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
        $status = $this->status;
        $input = $this->input;
        $tr = $this->tr;
        $user = User::find($this->userid);

        if($status==1) {
            Mail::to($user->email)->send(new TransactionNotificationMail($tr));
        }

        if ($input['payment_method'] == "general_market") {
            return;
        }

        if ($status != 1) {
            echo "status not success";
            return;
        }

        if ($input['service'] == "data") {
            if($input['payment_method']=="wallet" ) {
                $input["type"] = "income";
                $input["gl"] = "Data";
                $input["amount"] = 20;
                $input["narration"] = "Being wallet data charges on " . $input['transid'];
                $input["date"] = Carbon::now();

                PndL::create($input);
            }else{
                $input["type"] = "income";
                $input["gl"] = "Data";
                $input["amount"] = 50;
                $input["narration"] = "Being atm data charges on " . $input['transid'];
                $input["date"] = Carbon::now();

                PndL::create($input);
            }
        }

        if ($user->referral == "") {
            echo "no referral";
            return;
        }

        $ruser = User::where('user_name', $user->referral)->first();

        if($ruser->wallet < 100){
            return;
        }

        $job = (new PayReferralJob($input, $tr, $ruser->id))
            ->delay(Carbon::now()->addSeconds(1));
        dispatch($job);

    }
}
