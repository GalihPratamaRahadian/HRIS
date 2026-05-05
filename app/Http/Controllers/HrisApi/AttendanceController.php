<?php

namespace App\Http\Controllers\HrisApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\MyClass\HrisApiValidation;

class AttendanceController extends Controller
{
	public function index(Request $request)
	{
		HrisApiValidation::validateAttendance($request);

		$attendances = Attendance::select([ 'attendances.*' ])
								 ->with([ 'employee.department', 'employee.position', 'employee.employeeGroup' ])
								 ->leftJoin('employees', 'employees.id', '=', 'attendances.id_employee')
								 ->leftJoin('departments', 'departments.id', '=', 'employees.id_department')
								 ->leftJoin('employee_groups', 'employee_groups.id', '=', 'employees.id_employee_group')
								 ->where('date', '>=', $request->start_date)
								 ->where('date', '<=', $request->end_date);

		if(!empty($request->id_employee)) {
			$attendances = $attendances->where('attendances.id_employee', $request->id_employee);
		}

		if(!empty($request->id_department)) {
			$attendances = $attendances->where('employees.id_department', $request->id_department);
		}

		if(!empty($request->id_employee_group)) {
			$attendances = $attendances->where('employees.id_employee_group', $request->id_employee_group);
		}

		$results = [];

		$attendances = $attendances->get();
		foreach($attendances as $attendance) {
			$results[] = $this->attendanceData($attendance);
		}

		return \Res::success([
			'results' => [
				'attendances' => $results,
			]
		]);
	}


	public function get($attendance)
	{
		$attendance = Attendance::find($attendance);

		if($attendance) {
			return \Res::success([
				'results' => [
					'attendance' => $this->attendanceData($attendance),
				]
			]);
		}

		abort(404, 'Tidak ditemukan');
	}


	public function attendanceData($attendance)
	{
		return (object) [
			'id'			=> $attendance->id,
			'id_employee'	=> $attendance->id_employee,
			'employee_name'	=> $attendance->employeeName(),
			'department_name' => $attendance->departmentName(),
			'position_name'	=> $attendance->positionName(),
			'date'			=> $attendance->date,
			'shift_clock_in'=> $attendance->shift_clock_in,
			'shift_clock_out' => $attendance->shift_clock_out,
			'late_tolerance'=> $attendance->late_tolerance,
			'type'			=> $attendance->typeText(),
			'clock_in_at'	=> $attendance->clock_in_at,
			'clock_out_at'	=> $attendance->clock_out_at,
			'late'			=> $attendance->late,
		];
	}
}
