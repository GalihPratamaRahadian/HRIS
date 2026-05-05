<?php

namespace App\Http\Controllers\HrisApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
	public function index(Request $request)
	{
		$departments = Department::select([ 'departments.*' ]);
		$results = [];

		$departments = $departments->get();
		foreach($departments as $department) {
			$results[] = $this->departmentData($department);
		}

		return \Res::success([
			'results' => [
				'departments' => $results,
			]
		]);
	}


	public function departmentData($department)
	{
		return (object) [
			'id' => $department->id,
			'department_name' => $department->department_name,
		];
	}
}
