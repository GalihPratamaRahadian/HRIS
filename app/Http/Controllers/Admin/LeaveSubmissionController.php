<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveSubmission;
use App\Models\LeaveReason;
use App\Models\Employee;
use App\Models\LeaveSubmissionApproval;
use Illuminate\Support\Facades\DB;

class LeaveSubmissionController extends Controller
{

	public function index(Request $request)
	{
		if($request->ajax()) {
			return LeaveSubmission::dataTable($request);
		}

		return view('admin.leave_submission.index', [
			'title'         => 'Pengajuan Cuti',
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Cuti',
					'link'  => route('admin.leave_submission')
				]
			]
		]);
	}


	public function create()
	{
		return view('admin.leave_submission.create', [
			'title'         => 'Tambah Pengajuan Cuti',
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Cuti',
					'link'  => route('admin.leave_submission')
				],
				[
					'title' => 'Tambah',
					'link'  => route('admin.leave_submission.create')
				]
			]
		]);
	}


	public function store(Request $request)
	{
		\Validations::validateLeaveSubmission($request);

		$duration = \App\MyClass\Date::diffInDays($request->start_date, $request->end_date) + 1;

		$leaveReason = LeaveReason::find($request->id_leave_reason);
        $employeeIds = [];

        if ($request->target == 'selected') {
            $employeeIds = $request->id_employees;
        } else {
            $employeeIds = Employee::getActiveEmployees()->pluck('id')->toArray();
        }

        if (empty($employeeIds)) {
            return \Res::invalid([
                'message' => 'Tidak ada karyawan yang dipilih.',
                'errors' => [
                    'id_employees' => 'Pilih minimal satu karyawan.',
                ]
            ]);
        }

        foreach ($employeeIds as $employeeId) {
            $employee = Employee::find($employeeId);
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
                if($employee->leaveQuotaAvailable() < $duration) {
                    $message = $employee->employee_name.' hanya punya jatah cuti '. $employee->leaveQuotaAvailable() .' hari';
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

                $leaveSubmission = LeaveSubmission::create([
                    'id_employee'     => $employeeId,
                    'id_leave_reason' => $request->id_leave_reason,
                    'leave_reason'    => $request->leave_reason,
                    'start_date'      => $request->start_date,
                    'end_date'        => $request->end_date,
                    'description'     => $request->description,
                    'status'          => LeaveSubmission::STATUS_WAIT,
                ]);

                $leaveSubmission->takeLeaveQuota();
                \DB::commit();
                $leaveSubmission->saveFile($request);

                if($request->submission_approval_status == 'Approve') {
                    $notification = $request->send_notification == 'Ya';
                    $leaveSubmission->approveLeaveSubmissionByAdmin($notification);
                } else {
                    $leaveSubmission->createLeaveSubmissionApprovals();
                    $leaveSubmission->sendNotificationToAdmin();
                }

            } catch (\Exception $e) {
                DB::rollback();

                return \Res::error($e);
            }
        }
        return \Res::success();
	}

    public function resendBroadcastToApproval(LeaveSubmissionApproval $approval)
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


	public function detail(LeaveSubmission $leaveSubmission)
	{
		return view('admin.leave_submission.detail', [
			'title'         => 'Detail Pengajuan Cuti',
			'leaveSubmission' => $leaveSubmission,
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Cuti',
					'link'  => route('admin.leave_submission')
				],
				[
					'title' => 'Detail',
					'link'  => route('admin.leave_submission.detail', $leaveSubmission->id)
				]
			]
		]);
	}


	public function destroy(LeaveSubmission $leaveSubmission)
	{
		try {
			DB::beginTransaction();
			$leaveSubmission->deleteLeaveSubmission();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function approve(Request $request, LeaveSubmission $leaveSubmission)
	{
		try {
			$leaveReason = $leaveSubmission->leaveReason;
			$employee = $leaveSubmission->employee;

			if(empty($employee)) {
				return \Res::invalid([
					'message' => 'Karyawan sudah dihapus'
				]);
			}

			if($leaveReason->isCutLeaveQuota() && !$employee->isHasLeaveQuota()) {
				return \Res::invalid([
					'message'   => 'Tidak memiliki jatah cuti',
					'errors'    => [
						'leave_reason'  => 'Tidak memiliki jatah cuti'
					]
				]);
			}

			DB::beginTransaction();
			$leaveSubmission->approveLeaveSubmissionByAdmin($request);
			DB::commit();

			return \Res::success([
				'message'   => 'Berhasil disetujui'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function reject(Request $request, LeaveSubmission $leaveSubmission)
	{
		try {
			DB::beginTransaction();
			$leaveSubmission->rejectLeaveSubmissionByAdmin();
			DB::commit();

			return \Res::success([
				'message'   => 'Berhasil ditolak'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function cancel(Request $request, LeaveSubmission $leaveSubmission)
	{
		try {
			DB::beginTransaction();
			$leaveSubmission->cancelLeaveSubmissionByAdmin();
			DB::commit();

			return \Res::success([
				'message'   => 'Berhasil dibatalkan'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	// public function create(Request $request)
	// {
	// 	return view('submission.submission_leave_staff_create', [
	// 		'title'         => 'Buat Pengajuan Cuti',
	// 		'breadcrumbs'   => [
	// 			[
	// 				'title' => 'Pengajuan Cuti',
	// 				'link'  => route('submission.leave')
	// 			],
	// 			[
	// 				'title' => 'Buat Pengajuan Cuti',
	// 				'link'  => route('submission.leave.create')
	// 			]
	// 		]
	// 	]);
	// }


	// public function leaveStore(Request $request)
	// {
	// 	$request->validate([
	// 		'leave_reason'  => 'required|min:1|max:3',
	// 		'start_date'    => 'required',
	// 		'end_date'      => 'required',
	// 		'description'   => 'required',
	// 	]);
	// 	DB::beginTransaction();

	// 	try {
	// 		if(!auth()->user()->employee->isHasLeaveQuota($request->start_date)
	// 			&& !auth()->user()->employee->isHasLeaveQuota($request->end_date)
	// 			&& $request->leave_reason == LeaveSubmission::REASON_LEAVE) {
	// 			return \Setting::invalidResponse([
	// 				'message'   => 'Tidak memiliki jatah cuti',
	// 				'errors'    => [
	// 					'leave_reason'  => 'Tidak memiliki jatah cuti'
	// 				]
	// 			]);
	// 		}

	// 		LeaveSubmission::createLeaveSubmission($request);
	// 		DB::commit();

	// 		return \Setting::successResponse([
	// 			'message'   => 'Berhasil diajukan'
	// 		]);
	// 	} catch (\Exception $e) {
	// 		DB::rollback();

	// 		return \Setting::errorResponse($e);
	// 	}
	// }






	/**
	 *  Overtime Submission
	 * */
	public function overtimeIndex(Request $request)
	{
		if($request->ajax()) {
			return OvertimeSubmission::dt($request);
		}

		return view('admin.overtime_submission.index', [
			'title'         => 'Pengajuan Lembur',
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Lembur',
					'link'  => route('submission.overtime')
				]
			]
		]);
	}


	public function overtimeDetail(OvertimeSubmission $overtimeSubmission)
	{
		return view('admin.overtime_submission.detail', [
			'title'             => 'Detail Pengajuan Lembur',
			'overtimeSubmission'=> $overtimeSubmission,
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Lembur',
					'link'  => route('submission.overtime')
				],
				[
					'title' => 'Detail',
					'link'  => route('submission.overtime.detail', $overtimeSubmission->id)
				]
			]
		]);
	}
}
