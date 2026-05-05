<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SickNecessitySubmission;
use App\Models\NecessityReason;
use App\Models\SickReason;
use Validations;
use DB;

class SickNecessitySubmissionController extends Controller
{
	public function index(Request $request)
	{
		if($request->ajax()) {
			return SickNecessitySubmission::dataTable($request);
		}

		return view('employee.sick_necessity_submission.index', [
			'title'         => 'Pengajuan Sakit/Izin',
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Sakit/Izin',
					'link'  => route('employee.sick_necessity_submission')
				],
			]
		]);
	}

	public function create()
	{
		return view('employee.sick_necessity_submission.create', [
			'title'         => 'Buat Pengajuan Sakit/Izin',
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Sakit/Izin',
					'link'  => route('employee.sick_necessity_submission')
				],
				[
					'title' => 'Buat',
					'link'  => route('employee.sick_necessity_submission.create')
				],
			]
		]);
	}

	public function store(Request $request)
	{
		$duration = \App\MyClass\Date::diffInDays($request->start_date, $request->end_date) + 1;
		$type = $request->type;
		
		if($type == 'Sakit') {
			$sickReason = SickReason::find($request->id_sick_reason);
			if($sickReason->isUsingMaxDuration())
			{
				if($duration > $sickReason->max_duration) {
					$message = 'Durasi hanya boleh maksimal '. $sickReason->max_duration .' hari';
					return \Res::invalid([
						'message'   => $message,
						'errors'    => [
							'start_date' => $message.'. Harap ubah tanggal akhir/awal.',
							'end_date' => $message.'. Harap ubah tanggal akhir/awal.',
						]
					]);
				}
			}

			if($sickReason->isRequiredFile()) {
				if(empty($request->file_attachment)) {
					$message = 'Pengajuan '. $sickReason->reason .' wajib melampirkan file';
					return \Res::invalid([
						'message'   => $message,
						'errors'    => [
							'file_attachment' => $message,
						]
					]);
				}
			}
		} elseif ($type == 'Izin') {
			$necessityReason = NecessityReason::find($request->id_necessity_reason);
			if($necessityReason->isUsingMaxDuration())
			{
				if($duration > $necessityReason->max_duration) {
					$message = 'Durasi hanya boleh maksimal '. $necessityReason->max_duration .' hari';
					return \Res::invalid([
						'message'   => $message,
						'errors'    => [
							'start_date' => $message.'. Harap ubah tanggal akhir/awal.',
							'end_date' => $message.'. Harap ubah tanggal akhir/awal.',
						]
					]);
				}
			}

			if($necessityReason->isRequiredFile()) {
				if(empty($request->file_attachment)) {
					$message = 'Pengajuan '. $necessityReason->reason .' wajib melampirkan file';
					return \Res::invalid([
						'message'   => $message,
						'errors'    => [
							'file_attachment' => $message,
						]
					]);
				}
			}
		}

		try {
			DB::beginTransaction();
			$idEmployee = user()->isEmployee() ? auth()->user()->employee->id : $request->id_employee;
			$overtimeSubmission = SickNecessitySubmission::where('id_employee', $idEmployee)->where('start_date', $request->start_date)->where('end_date', $request->end_date)->first();
			if (!$overtimeSubmission) {
				SickNecessitySubmission::createSickNecessitySubmission($request);
				DB::commit();
			}

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function detail(SickNecessitySubmission $sickNecessitySubmission)
	{
		return view('employee.sick_necessity_submission.detail', [
			'title'         => 'Detail Pengajuan Sakit/Izin',
			'sickNecessitySubmission' => $sickNecessitySubmission,
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Sakit/Izin',
					'link'  => route('employee.sick_necessity_submission')
				],
				[
					'title' => 'Detail',
					'link'  => route('employee.sick_necessity_submission.detail', $sickNecessitySubmission->id)
				],
			]
		]);
	}
}
