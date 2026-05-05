<?php

namespace App\Http\Controllers\HrisApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeGroup;

class EmployeeGroupController extends Controller
{
	public function index(Request $request)
	{
		$employeeGroups = EmployeeGroup::select([ 'employee_groups.*' ]);
		$results = [];

		$employeeGroups = $employeeGroups->get();
		foreach($employeeGroups as $employeeGroup) {
			$results[] = $this->employeeGroupData($employeeGroup);
		}

		return \Res::success([
			'results' => [
				'employeeGroups' => $results,
			]
		]);
	}


	public function employeeGroupData($employeeGroup)
	{
		return (object) [
			'id' => $employeeGroup->id,
			'employee_group_name' => $employeeGroup->group_name,
		];
	}
}
