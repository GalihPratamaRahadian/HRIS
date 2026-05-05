<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaceTerminalLog;
use App\Models\Attendance;

class HelperController extends Controller
{

	/**
	 * 	Recent
	 * */
	public function recentInfo()
	{
		$latestLogAt = null;
		$latestLog = FaceTerminalLog::orderBy('created_at', 'desc')->first();
		if($latestLog) $latestLogAt = date('Y-m-d H:i:s', strtotime($latestLog->created_at));

		return \Res::success([
			'latest_log_at'	=> $latestLogAt,
		]);
	}


	public function mapGenerator(Request $request)
	{
		try {
			$latitude = $request->latitude;
			$longitude = $request->longitude;

			$location = location($latitude, $longitude);

			return \Res::success([
				'embedded_map_html'	=> $location->embeddedMap(),
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function getPositions(Request $request)
	{
		try {
			$positions = \App\Models\Position::with([ 'department' ]);

			if(!empty($request->id_department)) {
				$positions = $positions->where('id_department', $request->id_department);
			}

			$positions = $positions->get();
			$positions->map(function($position){
				$position['department_name'] = $position->departmentName();
				return $position;
			});

			return \Res::success([
				'positions'	=> $positions
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function getEmployees(Request $request)
	{
		try {
			$employees = \App\Models\Employee::with([ 'department', 'position', 'employeeGroup' ])
											 ->where('status', \App\Models\Employee::STATUS_ACTIVE);

			if(!empty($request->id_department)) {
				if($request->id_department != 'all') {
					$employees = $employees->where('id_department', $request->id_department);
				}
			}

			if(!empty($request->id_position)) {
				if($request->id_position != 'all') {
					$employees = $employees->where('id_position', $request->id_position);
				}
			}

			if(!empty($request->id_employee_group)) {
				if($request->id_employee_group != 'all') {
					$employees = $employees->where('id_employee_group', $request->id_employee_group);
				}
			}

			$employees = $employees->get();
			$employees->map(function($employee){
				$employee['department_name'] = $employee->departmentName();
				$employee['position_name'] = $employee->positionName();
                $employee['text'] = $employee->employee_name . ' - ' . $employee->departmentName();
				return $employee;
			});

			return \Res::success([
				'employees'	=> $employees
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function resume(\App\Models\Employee $employee)
	{
		$attendance = Attendance::find(51297);
		return response()->json([
			'data'	=> $employee->getWorkTimeByDateRange('2022-08-01', '2022-08-31'),
			// 'attendance'	=> $attendance,
			// 'percentage'	=> $attendance->percentageOfAttend(),
			// 'work'	=> $attendance->getWorkTimeInMinutes(),
			// 'attend'	=> $attendance->getAttendTimeInMinutes(),
			// 'daily_salary' => $attendance->employee->dailySalary($attendance->date, 2400000)
		]);
	}


	public function fixing()
	{
		$attendances = Attendance::where('created_at', '>=', '2022-08-01')
								 // ->where('created_at', '<=', '2022-08-31')
								 ->where('type', Attendance::TYPE_HADIR)
								 ->get();
		foreach($attendances as $attendance)
		{
			$attendance->update([
				'shift_clock_in'	=> $attendance->date.' 08:00:00',
				'shift_clock_out'	=> $attendance->date.' 17:00:00',
				'clock_in_at'		=> $attendance->date.' '.$attendance->clock_in,
				'late_tolerance'	=> 30,
			]);
		}
		$attendance = Attendance::find(51001);
		$attendance->update([
			'shift_clock_in'	=> '2022-08-18 10:00:00',
		]);
	}


	public function unserialize($filename)
	{
		if(\File::exists(storage_path('app/public/temps/'.$filename))) {
			return unserialize(\File::get(storage_path('app/public/temps/'.$filename)));
		} else {
			return 'no';
		}
	}


	public function importTemplates($filename)
	{
		$path = storage_path('import_templates/'.$filename);
		return response()->download($path);
	}
}
