<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OvertimeSubmission;
use App\Models\OvertimeReason;
use App\Models\Employee;
use App\Models\OvertimeSubmissionApproval;
use DB;

class OvertimeSubmissionController extends Controller
{
	public function index(Request $request)
	{
		if($request->ajax()) {
			return OvertimeSubmission::dataTable($request);
		}

		return view('admin.overtime_submission.index', [
			'title'         => 'Pengajuan Lembur',
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Lembur',
					'link'  => route('admin.overtime_submission')
				]
			]
		]);
	}


	public function create()
	{
		return view('admin.overtime_submission.create', [
			'title'         => 'Tambah Pengajuan Lembur',
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Lembur',
					'link'  => route('admin.overtime_submission')
				],
				[
					'title' => 'Tambah',
					'link'  => route('admin.overtime_submission.create')
				]
			]
		]);
	}


	public function store(Request $request)
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
			DB::commit();

			if($request->submission_approval_status == 'Approve') {
				$notification = $request->send_notification == 'Ya';
				$submission->approveOvertimeSubmissionByAdmin($notification);
			} else {
				$submission->createOvertimeSubmissionApprovals();
				$submission->sendNotificationToAdmin();
			}

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function detail(OvertimeSubmission $overtimeSubmission)
	{
		return view('admin.overtime_submission.detail', [
			'title'         => 'Detail Pengajuan Lembur',
			'overtimeSubmission' => $overtimeSubmission,
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Lembur',
					'link'  => route('admin.overtime_submission')
				],
				[
					'title' => 'Detail',
					'link'  => route('admin.overtime_submission.detail', $overtimeSubmission->id)
				]
			]
		]);
	}

    public function resendBroadcastToApproval(OvertimeSubmissionApproval $approval)
    {
        try {
            DB::beginTransaction();
            $approval->resendBroadcastToApproval();
            DB::commit();
            return \Res::success([
                'message' => 'Berhasil mengirim ulang broadcast ke penyetuju'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return \Res::error($e);
        }
    }


	public function destroy(OvertimeSubmission $overtimeSubmission)
	{
		try {
			DB::beginTransaction();
			$overtimeSubmission->deleteOvertimeSubmission();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function approve(Request $request, OvertimeSubmission $overtimeSubmission)
	{
		try {
			$employee = $overtimeSubmission->employee;

			if(empty($employee)) {
				return \Res::invalid([
					'message' => 'Karyawan sudah dihapus'
				]);
			}

			DB::beginTransaction();
			$overtimeSubmission->approveOvertimeSubmissionByAdmin($request);
			DB::commit();

			return \Res::success([
				'message'   => 'Berhasil disetujui'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function reject(Request $request, OvertimeSubmission $overtimeSubmission)
	{
		try {
			DB::beginTransaction();
			$overtimeSubmission->rejectOvertimeSubmissionByAdmin();
			DB::commit();

			return \Res::success([
				'message'   => 'Berhasil ditolak'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function cancel(Request $request, OvertimeSubmission $overtimeSubmission)
	{
		try {
			DB::beginTransaction();
			$overtimeSubmission->cancelOvertimeSubmissionByAdmin();
			DB::commit();

			return \Res::success([
				'message'   => 'Berhasil dibatalkan'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
