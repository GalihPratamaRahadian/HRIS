<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OvertimeSubmission;
use App\Models\OvertimeSubmissionApproval;
use Validations;
use DB;

class OvertimeApprovalController extends Controller
{

	public function index(Request $request)
	{
		if($request->ajax()) {
			return OvertimeSubmissionApproval::dataTable($request);
		}

		return view('employee.overtime_approval.index', [
			'title'         => 'Penyetujuan Lembur',
			'breadcrumbs'   => [
				[
					'title' => 'Penyetujuan Lembur',
					'link'  => route('employee.overtime_approval')
				],
			]
		]);
	}

	public function detail(OvertimeSubmissionApproval $overtimeSubmissionApproval)
	{
		$overtimeSubmissionApproval->load('overtimeSubmission');
		if(!$overtimeSubmissionApproval->overtimeSubmission) abort(404);
		$overtimeSubmission = $overtimeSubmissionApproval->overtimeSubmission;

		return view('employee.overtime_approval.detail', [
			'title'         => 'Detail Penyetujuan Lembur',
			'approval'      => $overtimeSubmissionApproval,
			'overtimeSubmission' => $overtimeSubmission,
			'breadcrumbs'   => [
				[
					'title' => 'Penyetujuan Lembur',
					'link'  => route('employee.overtime_approval')
				],
				[
					'title' => 'Detail',
					'link'  => route('employee.overtime_approval.detail', $overtimeSubmissionApproval->id)
				],
			]
		]);
	}

	public function approve(OvertimeSubmissionApproval $overtimeSubmissionApproval)
	{
		DB::beginTransaction();

		try {
			$overtimeSubmissionApproval->approve();
			DB::commit();

			return \Res::success([
				'message'   => 'Berhasil disetujui'
			]);
		} catch (\Exception $e) {
			DB::commit();

			return \Res::error($e);
		}
	}

	public function reject(OvertimeSubmissionApproval $overtimeSubmissionApproval)
	{
		DB::beginTransaction();

		try {
			$overtimeSubmissionApproval->reject();
			DB::commit();

			return \Res::success([
				'message'   => 'Berhasil ditolak'
			]);
		} catch (\Exception $e) {
			DB::commit();

			return \Res::error($e);
		}
	}


	public function createSubmission(Request $request)
	{
		return view('employee.overtime_approval.create_submission', [
			'title'         => 'Membuat Pengajuan Untuk Staff',
			'breadcrumbs'   => [
				[
					'title' => 'Penyetujuan Lembur',
					'link'  => route('employee.overtime_approval')
				],
				[
					'title' => 'Membuat Pengajuan Untuk Staff',
					'link'  => route('employee.overtime_approval.create_submission')
				],
			]
		]);
	}

	public function storeSubmission(Request $request)
	{
		\Validations::validateOvertimeSubmission($request);

		try {
			DB::beginTransaction();
			$submission = OvertimeSubmission::create([
				'id_employee'	=> $request->id_employee,
				'start_date'	=> $request->start_date,
				'end_date'		=> $request->end_date,
				'clock_start'	=> $request->clock_start,
				'clock_end'		=> $request->clock_end,
				'id_overtime_reason' => $request->id_overtime_reason,
				'description'	=> $request->description,
				'status'		=> OvertimeSubmission::STATUS_WAIT,
			]);

			$submission->createOvertimeSubmissionApprovals();
			$submission->load('overtimeSubmissionApprovals');
			foreach($submission->overtimeSubmissionApprovals as $approval) {
				if($approval->id_approver_position == auth()->user()->employee->id_position) {
					$approval->approve();
				}
			}
			$submission->fresh();

			if($submission->isStatusWaiting()) {
				$submission->sendNotificationToAdmin();
			} elseif ($submission->isStatusApproved()) {
				$submission->sendApprovedNotification();
			}

			DB::commit();

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
