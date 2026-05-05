<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use Validations;
use DB;

class DepartmentController extends Controller
{
	/**
	*   Department
	*
	*/
	public function index(Request $request)
	{
		if($request->ajax()) {
			return Department::dataTable($request);
		}

		return view('admin.department.index', [
			'title'         => 'Departemen',
			'breadcrumbs'   => [
				[
					'title' => 'Departemen',
					'link'  => route('admin.department')
				],
			]
		]);
	}

	public function create()
	{
		return view('admin.department.create', [
			'title'         => 'Tambah Departemen',
			'breadcrumbs'   => [
				[
					'title' => 'Departemen',
					'link'  => route('admin.department')
				],
				[
					'title' => 'Tambah',
					'link'  => route('admin.department.create')
				],
			]
		]);
	}

	public function store(Request $request)
	{
		Validations::validateDepartment($request);
		DB::beginTransaction();

		try {
			Department::createDepartment($request->all());
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function detail(Department $department)
	{
		return view('admin.department.detail', [
			'title'         => 'Detail Departemen',
			'department'    => $department,
			'breadcrumbs'   => [
				[
					'title' => 'Departemen',
					'link'  => route('admin.department')
				],
				[
					'title' => 'Detail',
					'link'  => route('admin.department.detail', $department->id)
				],
			]
		]);
	}

	public function edit(Department $department)
	{
		return view('admin.department.edit', [
			'title'         => 'Edit Departemen',
			'department'    => $department,
			'breadcrumbs'   => [
				[
					'title' => 'Departemen',
					'link'  => route('admin.department')
				],
				[
					'title' => 'Edit',
					'link'  => route('admin.department.edit', $department->id)
				],
			]
		]);
	}

	public function update(Request $request, Department $department)
	{
		Validations::validateDepartment($request);
		DB::beginTransaction();

		try {
			$department->updateDepartment($request->all());
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function destroy(Department $department)
	{
		DB::beginTransaction();

		try {
			$department->deleteDepartment();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
