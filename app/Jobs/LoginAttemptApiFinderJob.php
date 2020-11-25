<?php

namespace App\Jobs;

use App\Model\LoginAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LoginAttemptApiFinderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $id;
    public function __construct($id)
    {
        $this->id=$id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $la=LoginAttempt::find($this->id);

        $loc1 = file_get_contents('https://ipapi.co/'.$la->ip_address.'/json/');
        echo $loc1;
        $obj = json_decode($loc1);

        if(!isset($obj->error)) {
            $la->city = $obj->city;
            $la->region = $obj->region;
            $la->country = $obj->country_name;
            $la->provider = $obj->org;
            $la->response = $loc1;
            $la->save();
        }

        $loc2 = file_get_contents('http://ip-api.com/json/'.$la->ip_address);
        echo $loc2;
        $obj = json_decode($loc2);

        if(!isset($obj->status)) {
            $la->city = $obj->city;
            $la->region = $obj->regionName;
            $la->country = $obj->country;
            $la->provider = $obj->isp;
            $la->response = $loc2;
            $la->save();
        }

        if(isset($obj->error) || isset($obj->status)) {
            $la->response = "ipapi.co: ".$loc1 . " | ip-api.com: ".$loc2 ;
            $la->save();
        }

    }
}
