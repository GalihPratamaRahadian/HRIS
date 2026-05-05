<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalarySlip;
use App\MyClass\Validations;
use DB;

class SalarySlipController extends Controller
{
	public function index(Request $request)
	{
		if($request->ajax()) {
			return SalarySlip::dt($request);
		}

		return view('admin.salary_slip.index', [
			'title'			=> 'Slip Gaji',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Slip Gaji',
					'link'	=> route('salary_slip')
				]
			]
		]);
	}


	public function create()
	{
		return view('admin.salary_slip.create', [
			'title'			=> 'Buat Slip Gaji',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Slip Gaji',
					'link'	=> route('salary_slip')
				],
				[
					'title'	=> 'Buat',
					'link'	=> route('salary_slip.create')
				]
			]
		]);
	}


	public function store(Request $request)
	{
		Validations::validateSalarySlipMultiple($request);

		try {
			SalarySlip::createSalarySlipMultiple($request);

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function get(EmployeeGroup $employeeGroup)
	{
		try {
			return \Res::success([
				'employeeGroup'	=> $employeeGroup
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function edit(SalarySlip $salarySlip)
	{
		return view('admin.salary_slip.edit', [
			'title'			=> 'Edit Slip Gaji',
			'salarySlip'	=> $salarySlip,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Slip Gaji',
					'link'	=> route('salary_slip')
				],
				[
					'title'	=> 'Edit',
					'link'	=> route('salary_slip.edit', $salarySlip->id)
				]
			]
		]);
	}


	public function update(Request $request, SalarySlip $salarySlip)
	{
		DB::beginTransaction();

		try {
			$salarySlip->updateSalarySlip($request);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function destroy(SalarySlip $salarySlip)
	{
		DB::beginTransaction();

		try {
			$salarySlip->deleteSalarySlip();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
