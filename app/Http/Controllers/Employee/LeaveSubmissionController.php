<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveSubmission;
use App\Models\LeaveReason;
use Validations;
use DB;

class LeaveSubmissionController extends Controller
{
	
	public function index(Request $request)
	{
		if($request->ajax()) {
			return LeaveSubmission::dataTable($request);
		}

		return view('employee.leave_submission.index', [
			'title'         => 'Pengajuan Cuti',
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Cuti',
					'link'  => route('employee.leave_submission')
				],
			]
		]);
	}

	public function create()
	{
		return view('employee.leave_submission.create', [
			'title'         => 'Buat Pengajuan Cuti',
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Cuti',
					'link'  => route('employee.leave_submission')
				],
				[
					'title' => 'Buat',
					'link'  => route('employee.leave_submission.create')
				],
			]
		]);
	}

	public function store(Request $request)
	{
		Validations::validateLeaveSubmission($request);

		$duration = \App\MyClass\Date::diffInDays($request->start_date, $request->end_date) + 1;
		
		$leaveReason = LeaveReason::find($request->id_leave_reason);
		if($leaveReason->isUsingMaxDuration())
		{
			if($duration > $leaveReason->max_duration) {
				$message = 'Durasi cuti hanya boleh maksimal '. $leaveReason->max_duration .' hari';
				return \Res::invalid([
					'message'   => $message,
					'errors'    => [
						'start_date' => $message.'. Harap ubah tanggal akhir/awal.',
						'end_date' => $message.'. Harap ubah tanggal akhir/awal.',
					]
				]);
			}
		}

		if($leaveReason->isCutLeaveQuota()) {
			if(employee()->leaveQuotaAvailable() < $duration) {
				$message = 'Kamu hanya punya jatah cuti '. employee()->leaveQuotaAvailable() .' hari';
				return \Res::invalid([
					'message'   => $message,
					'errors'    => [
						'start_date' => $message.'. Harap ubah tanggal akhir/awal.',
						'end_date' => $message.'. Harap ubah tanggal akhir/awal.',
					]
				]);
			}
		}

		if($leaveReason->isRequiredFile()) {
			if(empty($request->file_attachment)) {
				$message = 'Pengajuan '. $leaveReason->reason .' wajib melampirkan file';
				return \Res::invalid([
					'message'   => $message,
					'errors'    => [
						'file_attachment' => $message,
					]
				]);
			}
		}

		try {
			DB::beginTransaction();
			$idEmployee = user()->isEmployee() ? auth()->user()->employee->id : $request->id_employee;
			$overtimeSubmission = LeaveSubmission::where('id_employee', $idEmployee)->where('start_date', $request->start_date)->where('end_date', $request->end_date)->first();
			if (!$overtimeSubmission) {
				LeaveSubmission::createLeaveSubmission($request);
				DB::commit();
			}

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function detail(LeaveSubmission $leaveSubmission)
	{
		return view('employee.leave_submission.detail', [
			'title'         => 'Detail Pengajuan Cuti',
			'leaveSubmission' => $leaveSubmission,
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Cuti',
					'link'  => route('employee.leave_submission')
				],
				[
					'title' => 'Detail',
					'link'  => route('employee.leave_submission.detail', $leaveSubmission->id)
				],
			]
		]);
	}
}
