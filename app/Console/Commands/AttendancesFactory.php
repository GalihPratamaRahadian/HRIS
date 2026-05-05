<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\Employee;

class AttendancesFactory extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'factory:attendances';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Factory for attendance';

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
		// Karyawan
		$continue = false;
		while($continue == false)
		{
			$employeeID = $this->ask('Masukkan ID Karyawan');

			$employee = Employee::find($employeeID);

			if($employee) {
				if($this->confirm("Lanjut dengan karyawan bernama {$employee->employee_name}?", true)) {
					$continue = true;
				}
			} else {
				$this->error('Karyawan tidak ditemukan');
			}
		}


		// Bulan Kerja
		$continue = false;
		while($continue == false)
		{
			$month = $this->ask('Masukkan angka bulan (1-12)', date('n'));

			if($month >= 1 && $month <= 12) {
				$continue = true;
			} else {
				$this->error('Angka bulan tidak valid');
			}
		}


		// Tahun Kerja
		$year = $this->ask('Masukkan tahun', date('Y'));


		// Lanjut
		$confirmText = "Lanjut membuat kehadiran untuk {$employee->employee_name} bulan ".\Date::monthName($month)." tahun {$year}";
		if($this->confirm($confirmText, true)) {
			$this->generateAttendances($employee, $month, $year);
		}

	}


	public function generateAttendances($employee, $month, $year)
	{
		$this->info('Membuat kehadiran...');

		$date = today()->setYear($year)->setMonth($month)->setDay(1);
		$endDate = today()->setYear($year)->setMonth($month)->setDay( \Date::tt("{$year}-{$month}-01", 't') );

		$this->info("Rentang tanggal ".\Date::tt($date)." hingga ".\Date::tt($endDate));

		while(strtotime($date) <= strtotime($endDate))
		{
			if(!$employee->isOffDay($date))
			{
				Attendance::create([
					'id_employee'		=> $employee->id,
					'date'				=> \Date::tt($date),
					'shift_clock_in'	=> $employee->shift ? $employee->shift->clock_start : null,
					'shift_clock_out'	=> $employee->shift ? $employee->shift->clock_end : null,
					'clock_in'			=> $employee->shift ? $employee->shift->clock_start : '08:00:00',
					'clock_out'			=> $employee->shift ? $employee->shift->clock_end : '17:00:00',
					'clock_in_method'	=> Attendance::METHOD_SYSTEM,
					'clock_out_method'	=> Attendance::METHOD_SYSTEM,
					'type'				=> Attendance::TYPE_HADIR,
					'clock_in_at'		=> \Date::tt($date).' '.($employee->shift ? $employee->shift->clock_start : '08:00:00'),
					'clock_out_at'		=> \Date::tt($date).' '.($employee->shift ? $employee->shift->clock_end : '17:00:00'),
				]);
			}
			else
			{
				Attendance::create([
					'id_employee'		=> $employee->id,
					'date'				=> \Date::tt($date),
					'shift_clock_in'	=> null,
					'shift_clock_out'	=> null,
					'clock_in'			=> null,
					'clock_out'			=> null,
					'clock_in_method'	=> Attendance::METHOD_SYSTEM,
					'clock_out_method'	=> Attendance::METHOD_SYSTEM,
					'type'				=> Attendance::TYPE_LIBUR,
					'clock_in_at'		=> null,
					'clock_out_at'		=> null,
				]);
			}
			$date->addDays(1);
		}

		$this->info('Berhasil membuat kehadiran');
	}
}
