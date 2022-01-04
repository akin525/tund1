<?php

namespace App\Jobs;

use App\Http\Controllers\Api\SellDataController;
use App\Models\PndL;
use App\Models\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewAccountGiveaway implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $jobi;

    public function __construct($jobi)
    {
        $this->jobi = $jobi;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->jobi;

        if (env('NYG') != 1) {
            echo "NYG has been deactivated";
            return;
        }

        $u = User::where([['user_name', $user], ['reg_date', '>', env('NYG_START')]])->first();

        if (!$u) {
            echo $user . " user not seen or not new user";
            return;
        }

        $ac = Transaction::where([['name', 'Newyear Giveaway'], ['user_name', $user]])->latest()->first();

        if ($ac) {
            echo "you have been served";
            return;
        }


        $qtr = Transaction::where([['user_name', $user], ['name', 'wallet funding'], ['amount', '>=', '100']])->latest()->first();

        echo $qtr;
        if (!$qtr) {
            echo $user . " you did not qualify";
            return;
        }

        $ref = "MCD_NYG" . Carbon::now()->timestamp . rand();

        $tr['name'] = "Newyear Giveaway";
        $tr['description'] = $u->user_name . " get " . $tr['name'] . " of 500MB data on " . $u->phoneno;
        $tr['code'] = "NYG";
        $tr['amount'] = 0;
        $tr['date'] = Carbon::now();
        $tr['device_details'] = "auto";
        $tr['ip_address'] = '127.0.0.1';
        $tr['user_name'] = $u->user_name;
        $tr['ref'] = $ref;
        $tr['server'] = "server8";
        $tr['server_response'] = "";
        $tr['payment_method'] = '';
        $tr['transid'] = $ref;
        $tr['status'] = "pending";
        $tr['extra'] = '0';
        $tr['i_wallet'] = $u->wallet;
        $tr['f_wallet'] = $tr['i_wallet'];
        $t = Transaction::create($tr);


        $input["type"] = "expenses";
        $input["gl"] = $tr['name'];
        $input["amount"] = '105';
        $input['date'] = Carbon::now();
        $input["narration"] = "Being " . $tr['name'] . " used by " . $user . " on " . $ref;

        PndL::create($input);


        $dada['tid'] = $t->id;
        $dada['amount'] = '0';
        $dada['discount'] = '0';

        $request = new Request();
        $request['coded'] = 'm500';

        $air = new SellDataController();
        if ($tr) {
            echo $air->server8($request, 'm500', $u->phoneno, $ref, "MTN", $request, $dada, "mcd");
        }

    }
}
