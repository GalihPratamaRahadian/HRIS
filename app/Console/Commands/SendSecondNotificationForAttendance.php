<?php

namespace App\Console\Commands;

use App\MyClass\Helper;
use App\MyClass\WhatsappNew;
use Illuminate\Console\Command;

class SendSecondNotificationForAttendance extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'attendance:send_second_notification';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Kirim Notification Kedua Untuk Karyawan Yang Belum Isi Kehadiran';

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
	 * @return int
	 */
	public function handle()
	{
		$diffMinutes = $this->getDifferentMinutes();

		$time = now()->addMinutes($diffMinutes);
		$time = date('H:i:00', strtotime($time));
		$date = date('Y-m-d');

		if(\App\Models\OffDay::offDayCheck($date)) return;


		$shifts = \App\Models\Shift::where('clock_start', $time)->get();

		foreach($shifts as $shift)
		{
			if($shift->offDayCheck($date)) continue;

			foreach($shift->activeEmployees as $employee)
			{
				if(!$employee->todayAttendance && !empty($employee->phone_number))
				{
					$message = $diffMinutes." menit lagi akan memasuki jam masuk. Jangan lupa mengisi kehadiran hari ini ya.";
					$message .= "\n\n*Adiva Attendance System*";

					$EndPointWa = WhatsappNew::END_POINT_WA;
					if($EndPointWa == 'WA Baru'){
						// wa Baru
						$send = Helper::sendNotificationWhatsapp($phoneNumber = $employee->phone_number, $message);
					}else{
						$send = \App\MyClass\Whatsapp::sendChat([
							'to'	=> $employee->phone_number,
							'text'	=> $message,
						]);
					}
				}
			}
		}

	}


	public function getDifferentMinutes()
	{
		// Selisih Menit
		return 5;
	}
}
