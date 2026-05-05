<?php

namespace App\Console\Commands\AppCommands;

use Illuminate\Console\Command;
use Artisan;

class ResetDatabaseRecords extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:reset_database';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Reset data di database';

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
		// Reset database
		$this->resetRecords();

		// Membuat user bawaan
		Artisan::call('app:make_users');
	}

	private function resetRecords()
	{
		\App\Models\ActionHistory::truncate();
		\App\Models\Attendance::truncate();
		\App\Models\AttendanceMeta::truncate();
		\App\Models\Department::truncate();
		\App\Models\Employee::truncate();
		\App\Models\EmployeeContract::truncate();
		\App\Models\EmployeeLeave::truncate();
		\App\Models\EmployeeLeaveQuota::truncate();
		\App\Models\EmployeeSalary::truncate();
		\App\Models\EmployeeSalaryAllowance::truncate();
		\App\Models\EmployeeSalaryCut::truncate();
		\App\Models\EmployeeShiftChangeSchedule::truncate();
		\App\Models\FaceTerminalDevice::truncate();
		\App\Models\FaceTerminalLog::truncate();
		\App\Models\FaceTerminalUser::truncate();
		\App\Models\LeaveSubmission::truncate();
		\App\Models\OffDay::truncate();
		\App\Models\Payroll::truncate();
		\App\Models\PayrollAllowance::truncate();
		\App\Models\PayrollAttendance::truncate();
		\App\Models\PayrollCut::truncate();
		\App\Models\Position::truncate();
		\App\Models\Registrant::truncate();
		\App\Models\RegistrantLog::truncate();
		\App\Models\Setting::truncate();
		\App\Models\Shift::truncate();
		\App\Models\ShiftDetail::truncate();
		\App\User::truncate();
	}
}
