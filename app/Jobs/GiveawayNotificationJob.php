<?php

namespace App\Jobs;

use App\Http\Controllers\PushNotificationController;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GiveawayNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $input = $this->input;

        $message = substr($input['user_name'], 0, 3) . " just created Giveaway of " . $input['type'] . " #" . $input['amount'] . " come and claim it now.";
        $title = $input['type'] . " Giveaway 🎉 !!";

        $users = User::where("user_name", "!=", $input['user_name'])->get();

        foreach ($users as $user) {
            $u = new PushNotificationController();
            $u->PushPersonal($user->user_name, $message, $title);
        }
    }
}
