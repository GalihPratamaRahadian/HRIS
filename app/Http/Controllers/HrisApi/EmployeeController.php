<?php

namespace App\Http\Controllers\HrisApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
	public function index(Request $request)
	{
		$employees = Employee::select([ 'employees.*' ])
							 ->with([ 'department', 'position', 'shift', 'employeeGroup' ])
							 ->where('status', Employee::STATUS_ACTIVE);
		$results = [];

		if(!empty($request->id_department)) {
			$employees = $employees->where('id_department', $request->id_department);
		}

		if(!empty($request->id_employee_group)) {
			$employees = $employees->where('id_employee_group', $request->id_employee_group);
		}

		$employees = $employees->get();
		foreach($employees as $employee) {
			$results[] = $this->employeeData($employee);
		}

		return \Res::success([
			'results' => [
				'employees' => $results,
			]
		]);
	}


	public function get($employee)
	{
		$employee = Employee::find($employee);

		if($employee) {
			return \Res::success([
				'results' => [
					'employee' => $this->employeeData($employee),
				]
			]);
		}

		abort(404, 'Tidak ditemukan');
	}


	public function employeeData($employee)
	{
		return (object) [
			'id' => $employee->id,
			'employee_number' => $employee->employee_number,
			'employee_name' => $employee->employee_name,
			'gender' => $employee->genderText(),
			'email' => $employee->email,
			'phone_number' => $employee->phone_number,
			'jamsostek' => $employee->jamsostek,
			'job_status' => $employee->jobStatusText(),
			'department_name' => $employee->departmentName(),
			'position_name' => $employee->positionName(),
			'employee_group_name' => $employee->employeeGroupName(),
			'shift_name' => $employee->shiftName(),
			'id_department' => $employee->id_department,
			'id_employee_group' => $employee->id_employee_group,
            'place_of_birth' => $employee->place_of_birth ?? '-',
            'date_of_birth' => $employee->date_of_birth ?? '-',
            'join_date' => $employee->start_working_date ?? '-',
            'profile_picture' => $employee->photoPath(),
            'address' => $employee->address ?? '-',
            'last_education' => $employee->last_education ?? '-',
            'last_education_major' => $employee->last_education_major ?? '-',
            'marital_status' => $employee->marital_status ?? '-',
            'blood_type' => $employee->blood_type ?? '-',
            'ktp_number' => $employee->ktp_number ?? '-',
            'npwp_number' => $employee->npwp_number ?? '-',
            'bank_name' => $employee->bank_name ?? '-',
            'bank_account_number' => $employee->bank_account_number ?? '-',
		];
	}
}
