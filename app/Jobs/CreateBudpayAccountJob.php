<?php

namespace App\Jobs;

use App\Models\VirtualAccount;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateBudpayAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $user_name;
    public function __construct($user_name)
    {
        $this->user_name=$user_name;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $u = User::where('user_name', $this->user_name)->first();

        if (!$u) {
            echo "invalid account";
        }


        $w=VirtualAccount::where(["user_id" =>$u->id, "provider" =>"monnify",])->exists();

        if (!$w){
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env("BUDPAY_URL") . "v2/customer",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => '{
    "email": "'.$u->email.'",
    "first_name": "'.$u->user_name.'",
    "last_name": "PlatnetF",
    "phone": "'.$u->phone.'"
}',
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer " . env("BUDPAY_SECRET")
                ),
            ));
            $response = curl_exec($curl);
            $respons = $response;

            curl_close($curl);


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env("BUDPAY_URL") . "v2/customer",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => '{
    "email": "'.$u->email.'",
    "first_name": "'.$u->user_name.'",
    "last_name": "PlatnetF",
    "phone": "'.$u->phone.'"
}',
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer " . env("BUDPAY_SECRET")
                ),
            ));
            $response = curl_exec($curl);
            $respons = $response;

            curl_close($curl);
        }

    }
}
