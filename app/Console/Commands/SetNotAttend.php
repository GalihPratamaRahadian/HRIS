<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Shift;
use App\Models\OffDay;

class SetNotAttend extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'attendance:set_not_attend';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Set tidak hadir';

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
		$date = today()->setDay(1)->setMonth(8);
		$validDate = date('Y-m-d', strtotime($date));
		$dateNow = date('Y-m-d');
		$employees = Employee::getActiveEmployees();

		while($validDate != $dateNow) {
			$validDate = date('Y-m-d', strtotime($date));
			$isOffDay = OffDay::offDayCheck($validDate);

			foreach($employees as $employee)
			{
				$attendance = Attendance::where('date', $validDate)
										->where('id_employee', $employee->id)
										->first();

				if(!$attendance) {

					if($employee->isHasShift() && !$isOffDay) {
						$isOffDayShift = $employee->shift->offDayCheck($validDate);
						$isOffDay = $isOffDayShift;
					}

					if($isOffDay) {
						Attendance::create([
							'id_employee'			=> $employee->id,
							'date'					=> $validDate,
							'shift_clock_in'		=> $employee->isHasShift() ? $employee->shift->clock_start : null,
							'shift_clock_out'		=> $employee->isHasShift() ? $employee->shift->clock_end : null,
							'clock_in'				=> null,
							'clock_out'				=> null,
							'clock_in_method'		=> Attendance::METHOD_SYSTEM,
							'clock_out_method'		=> Attendance::METHOD_SYSTEM,
							'type'					=> Attendance::TYPE_LIBUR,
							'description'			=> null,
							'late'					=> 0,
							'overtime'				=> 0,
							'is_overtime'			=> Attendance::NOT_OVERTIME
						]);
					} else {
						Attendance::create([
							'id_employee'			=> $employee->id,
							'date'					=> $validDate,
							'shift_clock_in'		=> $employee->isHasShift() ? $employee->shift->clock_start : null,
							'shift_clock_out'		=> $employee->isHasShift() ? $employee->shift->clock_end : null,
							'clock_in'				=> null,
							'clock_out'				=> null,
							'clock_in_method'		=> Attendance::METHOD_SYSTEM,
							'clock_out_method'		=> Attendance::METHOD_SYSTEM,
							'type'					=> Attendance::TYPE_TANPA_KETERANGAN,
							'description'			=> null,
							'late'					=> 0,
						]);
					}

				}

			}

			$date = $date->addDays(1);
		}
	}
}
