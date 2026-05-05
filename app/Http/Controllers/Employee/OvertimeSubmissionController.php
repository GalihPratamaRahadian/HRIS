<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OvertimeSubmission;
use Validations;
use DB;

class OvertimeSubmissionController extends Controller
{
	public function index(Request $request)
	{
		if($request->ajax()) {
			return OvertimeSubmission::dataTable($request);
		}

		return view('employee.overtime_submission.index', [
			'title'         => 'Pengajuan Lembur',
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Lembur',
					'link'  => route('employee.overtime_submission')
				],
			]
		]);
	}

	public function create()
	{
		return view('employee.overtime_submission.create', [
			'title'         => 'Buat Pengajuan Lembur',
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Lembur',
					'link'  => route('employee.overtime_submission')
				],
				[
					'title' => 'Buat',
					'link'  => route('employee.overtime_submission.create')
				],
			]
		]);
	}

	public function store(Request $request)
	{
		Validations::validateOvertimeSubmission($request);
		$startDate = date('Y-m-d H:i:s', strtotime($request->start_date.' '.$request->clock_start.':00'));
		$endDate = date('Y-m-d H:i:s', strtotime($request->end_date.' '.$request->clock_end.':00'));
		$minDate = date('Y-m-d H:i:s');
		if($startDate > $endDate) {
			return \Res::invalid([
				'errors' => [
					'end_date' => 'Tanggal & jam selesai harus diatas tanggal & jam mulai',
					'clock_end' => 'Tanggal & jam selesai harus diatas tanggal & jam mulai',
				]
			]);
		}

		if($minDate > $startDate) {
			return \Res::invalid([
				'errors' => [
					'start_date' => 'Tanggal & jam harus diatas '.(new \Carbon\Carbon($minDate))->format('d M Y H:i'),
					'clock_start' => 'Tanggal & jam harus diatas '.(new \Carbon\Carbon($minDate))->format('d M Y H:i'),
				]
			]);
		}

		if($minDate > $endDate) {
			return \Res::invalid([
				'errors' => [
					'end_date' => 'Tanggal & jam harus diatas '.(new \Carbon\Carbon($minDate))->format('d M Y H:i'),
					'clock_end' => 'Tanggal & jam harus diatas '.(new \Carbon\Carbon($minDate))->format('d M Y H:i'),
				]
			]);
		}

		DB::beginTransaction();

		try {

			$idEmployee = user()->isEmployee() ? auth()->user()->employee->id : $request->id_employee;
			$overtimeSubmission = OvertimeSubmission::where('id_employee', $idEmployee)->where('start_date', $request->start_date)->where('end_date', $request->end_date)->first();
			if (!$overtimeSubmission) {
				OvertimeSubmission::createOvertimeSubmission($request);
				DB::commit();
			}

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function detail(OvertimeSubmission $overtimeSubmission)
	{
		return view('employee.overtime_submission.detail', [
			'title'         => 'Detail Pengajuan Lembur',
			'overtimeSubmission' => $overtimeSubmission,
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Lembur',
					'link'  => route('employee.overtime_submission')
				],
				[
					'title' => 'Detail',
					'link'  => route('employee.overtime_submission.detail', $overtimeSubmission->id)
				],
			]
		]);
	}
}
