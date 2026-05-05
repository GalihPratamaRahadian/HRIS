<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FaceTerminalLog;
use App\Models\Employee;
use App\Models\Attendance;

class FaceTerminalLogController extends Controller
{

	public function index(Request $request)
	{
		$logs = [];

		if(!empty($request->id_employee) && !empty($request->start_date) && !empty($request->end_date)) {
			$employee = Employee::find($request->id_employee);
			$logs = FaceTerminalLog::where('name', $employee->employee_name)
								   ->whereBetween('date', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59'])
								   ->orderBy('date', 'asc')
								   ->get();
		}

		return view('developer.set_log_to_attendance', [
			'title'			=> 'Set FT Log To Attendance',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Set FT Log To Attendanceggajian',
					'link'	=> url('developer/face-terminal-log')
				]
			],
			'logs'			=> $logs,
		]);
	}

	public function setLogToAttendance(Employee $employee, $clockInLog, $clockOutLog)
	{
		$clockInLog = FaceTerminalLog::find($clockInLog);
		$clockOutLog = FaceTerminalLog::find($clockOutLog);
		$attendance = null;
		$clockInSuccess = false;
		$clockOutSuccess = false;

		if(!empty($employee) && !empty($clockInLog)) {
			$attendance = Attendance::storeAttendanceFromFaceTerminal($employee, $clockInLog, false);
			$attendance->load('employee');
			$clockInSuccess = true;
		}

		if(!empty($attendance) && !empty($clockOutLog))
		{
			$attendance->clockOut(Attendance::METHOD_FACETERMINAL);
			$attendance->setClockOutFaceTerminalLog($clockOutLog->id);
			if($location = $clockOutLog->getLocation()) {
				$attendance->setClockOutLocation($location->latitude, $location->longitude);
			}
			$clockOutSuccess = true;
		}

		return \Res::success([
			'clockInSuccess' => $clockInSuccess,
			'clockOutSuccess' => $clockOutSuccess,
			'attendance' => $attendance,
			'clockInLog' => $clockInLog,
			'clockOutLog' => $clockOutLog,
		]);
	}


	public function getWorkTimeByDateRange(Employee $employee, $start, $end)
	{
		return \Res::success([
			'result' => $employee->getWorkTimeByDateRange($start, $end),
		]);
	}


	public function setManually()
	{
		\Artisan::call('app:manually');
		return \Res::success();
	}
}
