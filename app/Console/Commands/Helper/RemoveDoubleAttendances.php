<?php

namespace App\Console\Commands\Helper;

use Illuminate\Console\Command;
use App\Models\Attendance;

class RemoveDoubleAttendances extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'helper:remove_double_attendances';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Untuk ngehapus presensi yang double';

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
		$date = now()->addDays(-7);
		$dateNow = date('Y-m-d');

		$results = [];
		while($date->format('Y-m-d') <= $dateNow)
		{
			$multipleAttendances = Attendance::whereDate('date', $date)
									->with([ 'employee' ])
									->get()
									->groupBy('id_employee');
			foreach($multipleAttendances as $employeeId => $attendancesByEmployeeId)
			{
				if(count($attendancesByEmployeeId) > 1) {
					$res = (object) [
						'id'			=> $employeeId,
						'employee_name'	=> null,
						'valid' 		=> null,
						'invalid' 		=> [],
					];

					/**
					 * 6 -> hadir
					 * 5 -> libur
					 * 4 -> cuti
					 * 3 -> izin
					 * 2 -> sakit
					 * 1 -> alpa
					 * */
					$point = 0;

					foreach($attendancesByEmployeeId as $att) {
						if(empty($res->employee_name)) {
							$res->employee_name = $att->employeeName();
						}

						if(empty($res->valid)) {
							$res->valid = $att;
						} else {
							if($res->valid->isTypeHadir() && $res->valid->isAlreadyClockOut()) {
								$res->invalid[] = $att;
								continue;
							}
							$switch = false;

							if($att->isTypeHadir() && $point == 6) {
								if($att->isAlreadyClockOut()) {
									$switch = true;
									$point = 6;
								}
							}

							if($att->isTypeHadir() && $point < 6) {
								$switch = true;
								$point = 6;
							}

							if($att->isTypeCuti() && $point < 5) {
								$switch = true;
								$point = 5;
							}

							if($att->isTypeLibur() && $point < 4) {
								$switch = true;
								$point = 4;
							}

							if($att->isTypeIzin() && $point < 3) {
								$switch = true;
								$point = 3;
							}

							if($att->isTypeSakit() && $point < 2) {
								$switch = true;
								$point = 2;
							}

							if($att->isTypeTanpaKeterangan() && $point < 1) {
								$switch = true;
								$point = 1;
							}

							if($switch) {
								$res->invalid[] = $res->valid;
								$res->valid = $att;
							}
						}
					}
					$results[] = $res;
				}
			}
			$date = $date->addDays(1);
		}

		foreach($results as $employee)
		{
			foreach($employee->invalid as $invalid) {
				$invalid->deleteAttendance();
			}
		}
	}
}
