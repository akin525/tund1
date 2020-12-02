<?php

namespace App\Jobs;

use App\model\PndL;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FundWalletPnLJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $ds;
    public function __construct($ds)
    {
        $this->ds=$ds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $in['type']="expense";
        $in['amount']="expense";
        PndL::create($in);
    }
}
