<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function server3(){
        $url=env("SERVER3_QUERY") ."&reference=546881600445125";

        $result = file_get_contents($url);

        // Convert JSON string to Array
        $someArray = json_decode($result, true);
        // Dump all data of the Array
        $status=$someArray["status"]; // Access Array data

        if ($status  == "success") {
            $ref=$someArray["ref"]; // Access Array data
            $this->addtrans("server3",$result,$price,1,$ref,$input);
        }else {
            $this->addtrans("server3",$result,$price,0,$transid,$input);
        }

    }
}
