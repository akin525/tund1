<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Reloadly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'samji:reloadly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get things started on reloadly';

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

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://topups.reloadly.com/countries',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer eyJraWQiOiIwMDA1YzFmMC0xMjQ3LTRmNmUtYjU2ZC1jM2ZkZDVmMzhhOTIiLCJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIxMTQwNyIsImlzcyI6Imh0dHBzOi8vcmVsb2FkbHkuYXV0aDAuY29tLyIsImh0dHBzOi8vcmVsb2FkbHkuY29tL3NhbmRib3giOmZhbHNlLCJodHRwczovL3JlbG9hZGx5LmNvbS9wcmVwYWlkVXNlcklkIjoiMTE0MDciLCJndHkiOiJjbGllbnQtY3JlZGVudGlhbHMiLCJhdWQiOiJodHRwczovL3RvcHVwcy1oczI1Ni5yZWxvYWRseS5jb20iLCJuYmYiOjE2NDA5MjA3NjcsImF6cCI6IjExNDA3Iiwic2NvcGUiOiJzZW5kLXRvcHVwcyByZWFkLW9wZXJhdG9ycyByZWFkLXByb21vdGlvbnMgcmVhZC10b3B1cHMtaGlzdG9yeSByZWFkLXByZXBhaWQtYmFsYW5jZSByZWFkLXByZXBhaWQtY29tbWlzc2lvbnMiLCJleHAiOjE2NDYxMDQ3NjcsImh0dHBzOi8vcmVsb2FkbHkuY29tL2p0aSI6IjRiMjBlYzgzLTljYWQtNGMzMS05YmU2LTFkNmZkZWNiNDAwMCIsImlhdCI6MTY0MDkyMDc2NywianRpIjoiZGExMzk2YTctOWI0OS00ZmM2LWJkNzEtMzVmNThiMTBmNzhhIn0.z-50LgZ15qR6iskekitueaNi95UoQdsFgRItfy8EsSw',
                'Accept: application/com.reloadly.topups-v1+json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
//        echo $response;

        $rep = json_decode($response, true);

    }
}
