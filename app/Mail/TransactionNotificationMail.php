<?php

namespace App\Mail;

use App\Models\Settings;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $data;
    public function __construct($data)
    {
        $this->data=$data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user=User::where('user_name', $this->data['user_name'])->first();
        $set=Settings::where('name', 'email_note')->first();
        return $this->view('mail.transaction')
            ->bcc('odejinmisamuel@gmail.com')
            ->subject($this->data['user_name'] . "| Transactional Email |".$this->data['transid'])
            ->with(['data'=>$this->data, 'email_note'=>$set->value, 'email'=>$user->email]);
    }
}
