<?php

namespace App\Jobs;

use App\Http\Controllers\PushNotificationController;
use App\Models\VirtualAccount;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BudpayVirtualAccountJob implements ShouldQueue
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

        if ($u->account_number != '0') {
            echo "Account created already";
        }

        try {

            $payload='{ "email": "zero@budpay.com",
      "first_name": "Zero",
      "last_name": "Sum",
      "phone": "+2348123456789"
    }';
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env("BUDPAY_URL") . "/customer",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $payload,
                    CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer " . env("BUDPAY_SECRET")
                ),
            ));
            $response = curl_exec($curl);
            $respons = $response;

            curl_close($curl);

            $response = json_decode($response, true);

            if($response['status']){

                $payload='{ "customer": "'.$response['data']['customer_code'].'"}';

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => env("BUDPAY_URL") . "/dedicated_virtual_account",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $payload,
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Bearer " . env("BUDPAY_SECRET")
                    ),
                ));
                $response = curl_exec($curl);
                $respons = $response;

                curl_close($curl);

                $response = json_decode($response, true);

                $customer_name = $response['data']['account_name'];
                $account_number = $response['data']['account_number'];
                $bank_name = $response['responseBody']['bankName'];
                $reservation_reference = $response['data']['reference'];


                VirtualAccount::create([
                    "user_id" =>$u->id,
                    "provider" =>"budpay",
                    "account_name" =>$customer_name,
                    "account_number" => $account_number,
                    "bank_name" =>$bank_name,
                    "reference" =>$reservation_reference,
                ]);

            }

            echo $account_number . "|| ";
        }catch (\Exception $e){
            echo "Error encountered ";
            $at=new PushNotificationController();
            $at->PushNotiAdmin("Unable to create providus account for ".$this->user_name,"Error on Account Generation");
        }
    }
}
