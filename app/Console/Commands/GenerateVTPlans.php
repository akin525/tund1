<?php

namespace App\Console\Commands;

use App\Models\ResellerElecticity;
use Illuminate\Console\Command;

class GenerateVTPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'samji:generatevt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate VTpass plans';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Add electricity");

        ResellerElecticity::create([
            'name' => 'IKEDC',
            'code' => 'ikeja-electric',
        ]);

        ResellerElecticity::create([
            'name' => 'EKEDC',
            'code' => 'eko-electric',
        ]);

        ResellerElecticity::create([
            'name' => 'KEDCO',
            'code' => 'kano-electric',
        ]);

        ResellerElecticity::create([
            'name' => 'PHED',
            'code' => 'portharcourt-electric',
        ]);

        ResellerElecticity::create([
            'name' => 'JED',
            'code' => 'jos-electric',
        ]);

        ResellerElecticity::create([
            'name' => 'IBEDC',
            'code' => 'ibadan-electric',
        ]);

        ResellerElecticity::create([
            'name' => 'KAEDCO',
            'code' => 'kaduna-electric',
        ]);

        ResellerElecticity::create([
            'name' => 'AEDC',
            'code' => 'abuja-electric',
        ]);

//
//        $elec= ResellerElecticity::create([
//            'name' => 'shipping',
//            'code' => '123 Example Street',
//            'city' => 'Victorville',
//            'state' => 'CA',
//            'postcode' => '90001',
//        ]);
//
//        $billing = $elec->replicate()->fill([
//            'type' => 'billing'
//        ]);
//
//        $billing->save();
//
//        $this->info("Fetching data plans");
//
//        $vds=['mtn-data','airtel-data', 'glo-data', 'etisalat-data', 'smile-direct'];
//        $inters=['MTN', 'AIRTEL', 'GLO', '9MOBILE', "SMILE DATA BUNDLES"];
//
//        $curl = curl_init();
//
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => env('SERVER6')."pay",
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'GET',
//            CURLOPT_HTTPHEADER => array(
//                'Authorization: Bearer ' .env('SERVER6_AUTH'),
//                'Content-Type: application/json'
//            ),
//        ));
//
//        $response = curl_exec($curl);
//
//        curl_close($curl);
//
//        $rep=json_decode($response, true);
//


    }
}
