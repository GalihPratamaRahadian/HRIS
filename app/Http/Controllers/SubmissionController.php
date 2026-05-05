<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveSubmission;
use App\Models\OvertimeSubmission;
use DB;

class SubmissionController extends Controller
{
	
	/**
	 * 	Leave Submission
	 * */
	public function leaveIndex(Request $request)
	{
		if(auth()->user()->isAdmin())
		{
			// ADMIN
			if($request->ajax()) {
				return LeaveSubmission::dataTable($request);
			}

			return view('admin.leave_submission.index', [
				'title'			=> 'Pengajuan Cuti',
				'breadcrumbs'	=> [
					[
						'title'	=> 'Pengajuan Cuti',
						'link'	=> route('admin.leave_submission')
					]
				]
			]);
		}
		else
		{
			// EMPLOYEE
			$limit = 5;
			$page = $request->page ?? 1;
			$skip = ($page - 1) * $limit;
			$amount = LeaveSubmission::where('id_employee', auth()->user()->employee->id)
									->count();

			$leaveSubmissions = LeaveSubmission::where('id_employee', auth()->user()->employee->id)
									->take($limit)
									->skip($skip)
									->orderBy('created_at', 'desc')
									->get();
			$amountPage = ceil($amount / $limit);
			$startPage = $page - 1;
			$startPage = $startPage >= 1 ? $startPage : 1;
			$endPage = $page + 1;
			$endPage = $endPage <= $amountPage ? $endPage : $amountPage;

			return view('submission.submission_leave_staff_index', [
				'title'			=> 'Pengajuan Cuti',
				'amountPage'	=> $amountPage,
				'activePage'	=> $page,
				'startPage'		=> $startPage,
				'endPage'		=> $endPage,
				'submissions'	=> $leaveSubmissions,
				'breadcrumbs'	=> [
					[
						'title'	=> 'Pengajuan Cuti',
						'link'	=> route('submission.leave')
					]
				]
			]);
		}
	}


	public function leaveCreate(Request $request)
	{
		return view('submission.submission_leave_staff_create', [
			'title'			=> 'Buat Pengajuan Cuti',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Pengajuan Cuti',
					'link'	=> route('submission.leave')
				],
				[
					'title'	=> 'Buat Pengajuan Cuti',
					'link'	=> route('submission.leave.create')
				]
			]
		]);
	}


	public function leaveStore(Request $request)
	{
		$request->validate([
			'leave_reason'	=> 'required|min:1|max:3',
			'start_date'	=> 'required',
			'end_date'		=> 'required',
			'description'	=> 'required',
		]);

		try {
			if(!auth()->user()->employee->isHasLeaveQuota($request->start_date)
				&& !auth()->user()->employee->isHasLeaveQuota($request->end_date) 
				&& $request->leave_reason == LeaveSubmission::REASON_LEAVE) {
				return \Setting::invalidResponse([
					'message'	=> 'Tidak memiliki jatah cuti',
					'errors'	=> [
						'leave_reason'	=> 'Tidak memiliki jatah cuti'
					]
				]);
			}

			LeaveSubmission::createLeaveSubmission($request);

			return \Setting::successResponse([
				'message'	=> 'Berhasil diajukan'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Setting::errorResponse($e);
		}
	}


	public function leaveDetail(LeaveSubmission $leaveSubmission)
	{
		if(auth()->user()->isAdmin())
		{
			// ADMIN
			return view('admin.leave_submission.detail', [
				'title'			=> 'Detail Pengajuan Cuti',
				'leaveSubmission' => $leaveSubmission,
				'breadcrumbs'	=> [
					[
						'title'	=> 'Pengajuan Cuti',
						'link'	=> route('admin.leave_submission')
					],
					[
						'title'	=> 'Detail',
						'link'	=> route('admin.leave_submission.detail', $leaveSubmission->id)
					]
				]
			]);
		}
		else
		{
			// EMPLOYEE
			return view('admin.leave_submission.detail', [
				'title'			=> 'Detail Pengajuan Cuti',
				'submission'	=> $leaveSubmission,
				'breadcrumbs'	=> [
					[
						'title'	=> 'Pengajuan Cuti',
						'link'	=> route('submission.leave')
					],
					[
						'title'	=> 'Detail',
						'link'	=> route('submission.leave.detail', $leaveSubmission->id)
					]
				]
			]);
		}
	}


	public function leaveDestroy(LeaveSubmission $leaveSubmission)
	{
		DB::beginTransaction();

		try {
			$leaveSubmission->deleteLeaveSubmission();
			DB::commit();

			return \Setting::deleteResponse();
		} catch (\Exception $e) {
			DB::rollback();

			return \Setting::errorResponse($e);
		}
	}


	public function leaveApprove(LeaveSubmission $leaveSubmission, Request $request)
	{

		try {
			if(empty($leaveSubmission->employee)) {
				return \Res::invalid([
					'message' => 'Karyawan sudah dihapus'
				]);
			}
			
			if(!$leaveSubmission->employee->isHasLeaveQuota($leaveSubmission->start_date)
				&& !$leaveSubmission->employee->isHasLeaveQuota($leaveSubmission->end_date) 
				&& $request->leave_reason == LeaveSubmission::REASON_LEAVE) {
				return \Res::invalid([
					'message'	=> 'Tidak memiliki jatah cuti',
					'errors'	=> [
						'leave_reason'	=> 'Tidak memiliki jatah cuti'
					]
				]);
			}

			DB::beginTransaction();
			$leaveSubmission->approve($request);
			DB::commit();

			return \Res::success([
				'message'	=> 'Berhasil disetujui'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function leaveReject(LeaveSubmission $leaveSubmission, Request $request)
	{
		DB::beginTransaction();

		try {
			$leaveSubmission->reject();
			DB::commit();

			return \Setting::successResponse([
				'message'	=> 'Berhasil ditolak'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Setting::errorResponse($e);
		}
	}



	/**
	 * 	Overtime Submission
	 * */
	public function overtimeIndex(Request $request)
	{
		if($request->ajax()) {
			return OvertimeSubmission::dt($request);
		}

		return view('admin.overtime_submission.index', [
			'title'			=> 'Pengajuan Lembur',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Pengajuan Lembur',
					'link'	=> route('submission.overtime')
				]
			]
		]);
	}


	public function overtimeDetail(OvertimeSubmission $overtimeSubmission)
	{
		return view('admin.overtime_submission.detail', [
			'title'				=> 'Detail Pengajuan Lembur',
			'overtimeSubmission'=> $overtimeSubmission,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Pengajuan Lembur',
					'link'	=> route('submission.overtime')
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('submission.overtime.detail', $overtimeSubmission->id)
				]
			]
		]);
	}

}
