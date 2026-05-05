<?php

namespace App\Http\Controllers\MobileApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileAppToken;
use App\Models\LeaveSubmission;
use App\Models\LeaveReason;
use Validations;
use DB;

class LeaveSubmissionController extends Controller
{
	public function list(Request $request)
	{
		try {
			$token = MobileAppToken::getByToken($request->token);
			$employee = $token->employee;

			$page = $request->page ?? 1;
			$limit = $request->limit ?? 5;

			$leaveSubmissions = LeaveSubmission::with([ 'employee' ])
								->where('id_employee', $employee->id)
								->orderBy('created_at', 'desc')
								->take($limit)
								->skip(($page - 1) * $limit)
								->get();
				
			return \Res::success([
				'result' => [
					'leaveSubmissions'   => LeaveSubmission::fetchLeaveSubmissions($leaveSubmissions),
				]
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function detail(Request $request)
	{
		$request->validate([
			'id_leave_submission' => 'required|exists:leave_submissions,id',
		], [
			'id_leave_submission.required'=> 'ID Pengajuan diperlukan',
			'id_leave_submission.exists'  => 'Data Pengajuan tidak ditemukan'
		]);

		try {
			$leaveSubmission = LeaveSubmission::find($request->id_leave_submission);
				
			return \Res::success([
				'result' => [
					'leaveSubmission'    => $leaveSubmission->fetchData(),
				]
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function save(Request $request)
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

		// if($leaveReason->isCutLeaveQuota()) {
		// 	if(employee()->leaveQuotaAvailable() < $duration) {
		// 		$message = 'Kamu hanya punya jatah cuti '. employee()->leaveQuotaAvailable() .' hari';
		// 		return \Res::invalid([
		// 			'message'   => $message,
		// 			'errors'    => [
		// 				'start_date' => $message.'. Harap ubah tanggal akhir/awal.',
		// 				'end_date' => $message.'. Harap ubah tanggal akhir/awal.',
		// 			]
		// 		]);
		// 	}
		// }

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
			$token = MobileAppToken::getByToken($request->token);
			$employee = $token->employee;

			DB::beginTransaction();
			$leaveSubmission = LeaveSubmission::create([
				'id_employee'	=> $employee->id,
				'id_leave_reason' => $request->id_leave_reason,
				'leave_reason'	=> $leaveReason->reason,
				'start_date'	=> $request->start_date,
				'end_date'		=> $request->end_date,
				'description'	=> $request->description,
				'status'		=> LeaveSubmission::STATUS_WAIT,
			]);
			$leaveSubmission->createLeaveSubmissionApprovals();
			// $leaveSubmission->takeLeaveQuota();
			DB::commit();
			$leaveSubmission->saveFile($request);

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
