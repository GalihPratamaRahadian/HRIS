<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveSubmissionApproval;
use DB;

class LeaveApprovalController extends Controller
{

	public function index(Request $request)
	{
		if($request->ajax()) {
			return LeaveSubmissionApproval::dataTable($request);
		}

		return view('employee.leave_approval.index', [
			'title'         => 'Penyetujuan Cuti',
			'breadcrumbs'   => [
				[
					'title' => 'Penyetujuan Cuti',
					'link'  => route('employee.leave_approval')
				],
			]
		]);
	}

	public function detail(LeaveSubmissionApproval $leaveSubmissionApproval)
	{
		$leaveSubmissionApproval->load('leaveSubmission');
		if(!$leaveSubmissionApproval->leaveSubmission) abort(404);

		return view('employee.leave_approval.detail', [
			'title'         => 'Detail Penyetujuan Cuti',
			'approval'      => $leaveSubmissionApproval,
			'breadcrumbs'   => [
				[
					'title' => 'Penyetujuan Cuti',
					'link'  => route('employee.leave_approval')
				],
				[
					'title' => 'Detail',
					'link'  => route('employee.leave_approval.detail', $leaveSubmissionApproval->id)
				],
			]
		]);
	}

	public function approve(LeaveSubmissionApproval $leaveSubmissionApproval)
	{
		try {
			DB::beginTransaction();
			$leaveSubmissionApproval->approve();
			DB::commit();

			return \Res::success([
				'message'   => 'Berhasil disetujui'
			]);
		} catch (\Exception $e) {
			DB::commit();

			return \Res::error($e);
		}
	}

	public function reject(LeaveSubmissionApproval $leaveSubmissionApproval)
	{
		try {
			DB::beginTransaction();
			$leaveSubmissionApproval->reject();
			DB::commit();

			return \Res::success([
				'message'   => 'Berhasil ditolak'
			]);
		} catch (\Exception $e) {
			DB::commit();

			return \Res::error($e);
		}
	}
}
