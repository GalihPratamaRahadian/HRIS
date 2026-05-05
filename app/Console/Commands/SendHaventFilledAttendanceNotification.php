<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendHaventFilledAttendanceNotification extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'attendance:send_havent_filled_attendance_notification';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Kirim notifikasi untuk yang belum isi kehadiran';

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
		$setting = \App\Models\Setting::getValue('send_havent_filled_attendance_notification', 'no');

		if($setting == 'yes') {
			$employees = \App\Models\Employee::getActiveEmployees();
			foreach($employees as $employee)
			{
				$employeeCustom = \App\MyClass\EmployeeCustom::make($employee->id);
				$employeeCustom->sendHaventFilledAttendanceNotification();
			}
		}
	}
}
