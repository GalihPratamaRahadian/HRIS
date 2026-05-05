<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ClockInMail extends Mailable
{
	use Queueable, SerializesModels;

    public $data;
    public $title;

    public function __construct($data, $title)
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function build()
    {
        return $this->subject($this->title)
                ->view('mail.clock_in_mail');
    }
}
