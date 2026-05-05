<?php

namespace App\Http\Controllers\HrisApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalarySlip;

class SalarySlipController extends Controller
{
	public function index(Request $request)
	{
		$salarySlips = SalarySlip::select([ 'salary_slips.*' ])
								 ->with([ 'employee.department' ])
								 ->leftJoin('employees', 'salary_slips.id_employee', '=', 'employees.id')
								 ->leftJoin('departments', 'employees.id_department', '=', 'departments.id');

		if(!empty($request->id_employee)) {
			$salarySlips = $salarySlips->where('salary_slips.id_employee', $request->id_employee);
		}

		if(!empty($request->id_department)) {
			$salarySlips = $salarySlips->where('employees.id_department', $request->id_department);
		}

		if(!empty($request->year)) {
			$salarySlips = $salarySlips->where('salary_slips.year', $request->year);
		}

		if(!empty($request->month)) {
			$salarySlips = $salarySlips->where('salary_slips.month', $request->month);
		}

		$results = [];
		$salarySlips = $salarySlips->get();
		foreach($salarySlips as $salarySlip) {
			$results[] = $this->salarySlipData($salarySlip);
		}

		return \Res::success([
			'results' => [
				'salarySlips' => $results,
			]
		]);
	}


	public function salarySlipData($salarySlip)
	{
		return (object) [
			'id' => $salarySlip->id,
			'title' => $salarySlip->title,
			'employee_name' => $salarySlip->employeeName(),
			'department_name' => $salarySlip->departmentName(),
			'month' => $salarySlip->month,
			'month_name' => $salarySlip->month_name,
			'year' => $salarySlip->year,
			'total' => $salarySlip->total,
			'total_formatted' => $salarySlip->totalFormatted(),
			'file_link' => $salarySlip->fileLink(),
		];
	}


	public function save(Request $request)
	{
		$request->validate([
			'title'			=> 'required',
			'id_employee'	=> 'required|exists:employees,id',
			'month'			=> 'required|min:1|max:12',
			'year'			=> 'required|min:0|max:'.date('Y'),
			'total'			=> 'required|min:0',
			'file'			=> 'required|file|mimes:pdf,jpeg,png,jpg',
		], [
			'title.required'	=> 'Judul wajib diisi',
			'id_employee.required'	=> 'Id employee wajib diisi',
			'id_employee.exists'	=> 'Karyawan tidak terdaftar',
			'month.required'		=> 'Bulan wajib diisi',
			'month.min'				=> 'Bulan tidak valid',
			'month.max'				=> 'Bulan tidak valid',
			'year.required'			=> 'Tahun wajib diisi',
			'year.min'				=> 'Tahun tidak valid',
			'year.max'				=> 'Tahun tidak valid',
			'total.required'		=> 'Total wajib diisi',
			'file.required'			=> 'File wajib diisi',
			'file.mimes'			=> 'Hanya mendukung file pdf, jpeg, png'
		]);

		try {
			\DB::beginTransaction();
			$salarySlip = SalarySlip::create([
				'title'			=> $request->title,
				'id_employee'	=> $request->id_employee,
				'month'			=> $request->month,
				'month_name'	=> \App\MyClass\Date::monthName($request->month),
				'year'			=> $request->year,
				'total'			=> $request->total,
			]);
			$salarySlip->saveFile($request->file('file'));
			\DB::commit();

			return \Res::save([
				'salarySlip' => $this->salarySlipData($salarySlip),
			]);
		} catch (\Exception $e) {
			\DB::rollback();

			return \Res::error($e);
		}
	}
}
