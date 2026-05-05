<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterOTPMail extends Mailable
{
	use Queueable, SerializesModels;

	public $registrant;
	public $username;
	public $password;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct($registrant, $username, $password)
	{
		$this->registrant = $registrant;
		$this->username = $username;
		$this->password = $password;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->subject('Pendaftaran karyawan baru atas nama '.$this->registrant->employee_name)
					->view('mail.register_otp_mail', [
						'registrant'	=> $this->registrant,
						'username'		=> $this->username,
						'password'		=> $this->password,
					]);
	}
}
