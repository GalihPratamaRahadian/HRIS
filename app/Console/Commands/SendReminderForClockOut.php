<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Attendance;
use App\MyClass\Helper;
use App\MyClass\WhatsappNew;

class SendReminderForClockOut extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'attendance:send_reminder_for_clock_out';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Kirim pengingat untuk isi jam keluar';

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
		$this->sendReminderForClockOut();
		$this->sendReminderForClockOut(30);
		$this->sendReminderForClockOut(60);
	}

	
	public function sendReminderForClockOut($missedMinutes = 0)
	{
		$attendances = Attendance::where('shift_clock_out', 'like', '%'.now()->addMinutes(-$missedMinutes)->format('Y-m-d H:i').'%')
								 ->where('clock_out', null)
								 ->where('clock_out_at', null)
								 ->with([ 'employee' ])
								 ->get();

		foreach($attendances as $attendance)
		{
			$employee = $attendance->employee;
			if($employee) {
				if(!empty($employee->phone_number)) {
					if($missedMinutes > 0) {
						$message = "Halo *{$employee->employee_name}*, sudah {$missedMinutes} menit terlewat diharapkan untuk tidak lupa mengisi jam keluar kehadiranmu ya.";
					} else {
						$message = "Halo *{$employee->employee_name}* jangan lupa untuk mengisi jam keluar kehadiranmu ya.";
					}

					$message .= "\nTerima kasih.";
					$message .= "\n\n*Attendance System*.";

					$EndPointWa = WhatsappNew::END_POINT_WA;
					if($EndPointWa == 'WA Baru'){
						// wa Baru
						Helper::sendNotificationWhatsapp($phoneNumber = $employee->phone_number, $message);
					}else{
						\App\MyClass\Whatsapp::sendChat([
							'to'	=> $employee->phone_number,
							'text'	=> $message,
						]);
					}
				}
			}
		}
	}
}
