<?php

namespace App\Jobs;

use App\Http\Controllers\Api\ServeRequestController;
use App\Http\Controllers\PushNotificationController;
use App\Model\Serverlog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ATMtransactionserveJob implements ShouldQueue
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
        $s=Serverlog::find($this->id);

        $input['user_name'] =$s->user_name;
        $input['api'] = $s->api;
        $input['coded'] = $s->coded;
        $input['phone'] = $s->phone;
        $input['amount'] = $s->amount;
        $input['transid'] = $s->transid;
        $input['service'] = $s->service;
        $input['network'] = $s->network;
        $input['payment_method'] = $s->payment_method;

        $r= new Request($input);
        if($s->service=="airtime"){
            $t=new ServeRequestController();
            $t->buyairtime($r);
        }

        if($s->service=="data"){
            $t=new ServeRequestController();
            $t->buydata($r);
        }

        if($s->service=="paytv"){
            $at=new PushNotificationController();
            $at->PushNoti($input['user_name'], "Your TV subscription request of ". $input['coded'] ." on ". $input['phone'] ." will be served soon.", "Paytv Transaction" );
        }
    }
}
