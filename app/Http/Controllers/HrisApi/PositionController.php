<?php

namespace App\Http\Controllers\HrisApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Position;

class PositionController extends Controller
{
	public function index(Request $request)
	{
		$positions = Position::with([ 'department', 'approver1Position.department', 'approver2Position.department', 'approver1Position.employees.department', 'approver2Position.employees.department' ]);
		$results = [];

		if(!empty($request->id_department)) {
			$positions = $positions->where('id_department', $request->id_department);
		}

		$positions = $positions->get();
		foreach($positions as $position) {
			$results[] = $this->positionData($position);
		}

		return \Res::success([
			'results' => [
				'positions' => $results,
			]
		]);
	}

	public function detail(Position $position)
	{
		$position->with([ 'department', 'approver1Position.department', 'approver2Position.department', 'approver1Position.employees.department', 'approver2Position.employees.department' ]);

		return \Res::success([
			'results' => [
				'position' => $this->positionData($position),
			]
		]);
	}

	public function positionData($position)
	{
		$result = (object) [
			'id' => $position->id,
			'position_name' => $position->position_name,
			'department_name' => $position->departmentName(),
			'approvers' => [], 
		];

		if($position->approver1Position) {
			$approver = (object) [
				'id_position' => $position->approver1Position->id,
				'position_name' => $position->approver1Position->position_name,
				'department_name' => $position->approver1Position->departmentName(),
				'approver_level' => 1,
				'employee_list' => []
			];

			foreach($position->approver1Position->employees as $employee) {
				$approver->employee_list[] = $this->employeeData($employee);
			}

			$result->approver[] = $approver;
		}

		if($position->approver2Position) {
			$approver = (object) [
				'id_position' => $position->approver2Position->id,
				'position_name' => $position->approver2Position->position_name,
				'department_name' => $position->approver2Position->departmentName(),
				'approver_level' => 2,
				'employee_list' => []
			];

			foreach($position->approver2Position->employees as $employee) {
				$approver->employee_list[] = $this->employeeData($employee);
			}

			$result->approver[] = $approver;
		}

		return $result;
	}

	public function employeeData($employee)
	{
		return (object) [
			'id_employee' => $employee->id,
			'employee_number' => $employee->employee_number,
			'employee_name' => $employee->employee_name,
			'department_name' => $employee->departmentName(),
		];
	}
}
