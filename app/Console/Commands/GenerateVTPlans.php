<?php

namespace App\Console\Commands;

use App\Models\ResellerCableTV;
use App\Models\ResellerDataPlans;
use Illuminate\Console\Command;

class GenerateVTPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'samji:vtpass';

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
//        $this->info("Add electricity");
//
//        ResellerElecticity::create([
//            'name' => 'IKEDC',
//            'code' => 'ikeja-electric',
//            'discount' => '0.5%',
//        ]);
//
//        ResellerElecticity::create([
//            'name' => 'EKEDC',
//            'code' => 'eko-electric',
//            'discount' => '0.5%',
//        ]);
//
//        ResellerElecticity::create([
//            'name' => 'KEDCO',
//            'code' => 'kano-electric',
//            'discount' => '0.5%',
//        ]);
//
//        ResellerElecticity::create([
//            'name' => 'PHED',
//            'code' => 'portharcourt-electric',
//            'discount' => '0.5%',
//        ]);
//
//        ResellerElecticity::create([
//            'name' => 'JED',
//            'code' => 'jos-electric',
//            'discount' => '0.5%',
//        ]);
//
//        ResellerElecticity::create([
//            'name' => 'IBEDC',
//            'code' => 'ibadan-electric',
//            'discount' => '0.5%',
//        ]);
//
//        ResellerElecticity::create([
//            'name' => 'KAEDCO',
//            'code' => 'kaduna-electric',
//            'discount' => '0.5%',
//        ]);
//
//        ResellerElecticity::create([
//            'name' => 'AEDC',
//            'code' => 'abuja-electric',
//            'discount' => '0.5%',
//        ]);

//
        $this->info("Fetching tv plans");

        $inters = ['dstv', 'gotv', 'startimes'];

        foreach ($inters as $inte) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('SERVER6') . "service-variations?serviceID=" . $inte,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('SERVER6_AUTH'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            echo $response;

            curl_close($curl);

            $rep = json_decode($response, true);

            foreach ($rep['content']['varations'] as $plans) {
                ResellerCableTV::create([
                    'name' => $plans['name'],
                    'code' => $plans['variation_code'],
                    'amount' => $plans['variation_amount'],
                    'type' => $inte,
                    'discount' => '1%',
                    'status' => 1,
                ]);
            }
        }


        $this->info("Fetching data plans");
        $inters = ['mtn-data', 'airtel-data', 'glo-data', 'etisalat-data', 'smile-direct'];
        $vds = ['mtn', 'airtel', 'glo', '9mobile', "smile"];


        $dtp = ResellerDataPlans::create([
            'name' => "MTN 1gb - SME",
            'code' => "MTN1GB",
            'amount' => "290",
            'price' => "255",
            'type' => "mtn-data",
            'discount' => '2%',
            'status' => 1,
        ]);

        $billing = $dtp->replicate()->fill([
            'name' => "MTN 2gb - SME",
            'code' => "MTN2GB",
            'amount' => "580",
            'price' => "510",
        ]);


        $billing = $dtp->replicate()->fill([
            'name' => "MTN 5gb - SME",
            'code' => "MTN5GB",
            'amount' => "1300",
            'price' => "1275",
        ]);

        $billing->save();

        foreach ($inters as $inte) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('SERVER6') . "service-variations?serviceID=" . $inte,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('SERVER6_AUTH'),
                    'Content-Type: application/json'
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            echo $response;

            curl_close($curl);

            $rep = json_decode($response, true);

            foreach ($rep['content']['varations'] as $plans) {
                ResellerDataPlans::create([
                    'name' => $plans['name'],
                    'code' => $plans['variation_code'],
                    'amount' => $plans['variation_amount'],
                    'price' => $plans['variation_amount'],
                    'type' => $inte,
                    'discount' => '2%',
                    'status' => 1,
                ]);
            }
        }
    }
}
