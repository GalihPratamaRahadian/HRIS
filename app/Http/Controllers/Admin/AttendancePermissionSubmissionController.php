<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendancePermissionSubmission;
use App\Models\AttendancePermissionSubmissionApproval;
use App\MyClass\Helper;
use App\MyClass\Validations;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendancePermissionSubmissionController extends Controller
{
	public function index(Request $request)
	{
		if ($request->ajax()) {
			return AttendancePermissionSubmission::dataTable($request);
		}

		return view('admin.attendance_permission_submission.index', [
			'title'         => 'Pengajuan Izin Terlambat / Pulang Cepat',
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Izin Terlambat / Pulang Cepat',
					'link'  => route('admin.attendance_permission_submission')
				]
			]
		]);
	}


	public function create()
	{
		return view('admin.attendance_permission_submission.create', [
			'title'         => 'Tambah Pengajuan Izin Terlambat / Pulang Cepat',
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Izin Terlambat / Pulang Cepat',
					'link'  => route('admin.attendance_permission_submission')
				],
				[
					'title' => 'Tambah',
					'link'  => route('admin.attendance_permission_submission.create')
				]
			]
		]);
	}

    public function store(Request $request)
    {
        Validations::validateAttendanceSubmission($request);

        $requestedDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->time);

        if ($requestedDateTime <= Carbon::now()) {
            return \Res::invalid([
                'message' => 'Waktu yang diajukan harus lebih dari waktu saat ini!',
            ]);
        }

         $attendancePermission = AttendancePermissionSubmission::where('id_employee', $request->id_employee)
            ->where('date', $request->date)
            ->where('type', $request->type)
            ->exists();

        if ($attendancePermission) {
            return \Res::invalid([
                'message' => 'Pengajuan ' . strtolower($request->type) . ' untuk tanggal tersebut sudah ada. Tidak bisa mengajukan lebih dari satu kali.',
            ]);
        }

        $latestSubmission = AttendancePermissionSubmission::where('id_employee', $request->id_employee)
            ->where('date', $request->date)
            ->where('type', $request->type)
            ->latest()
            ->first();

        if ($latestSubmission && $requestedDateTime <= $latestSubmission->created_at) {
            return \Res::invalid([
                'message' => 'Waktu yang diajukan harus di atas waktu diajukan sebelumnya!',
            ]);
        }

        try {
            DB::beginTransaction();
            $attendancePermissionSubmission = AttendancePermissionSubmission::create($request->all());
            DB::commit();

            if ($request->submission_approval_status == 'Approve') {
                $notification = $request->send_notification == 'Ya';
                $attendancePermissionSubmission->approveAttendancePermissionSubmissionByAdmin($notification);
            } else {
                $attendancePermissionSubmission->createAttendancePermissionSubmissionApprovals();
                $attendancePermissionSubmission->sendNotificationToAdmin();
            }

            return \Res::success();
        } catch (\Exception $e) {
            DB::rollback();
            return \Res::error($e);
        }
    }


   public function resendBroadcastToApproval(AttendancePermissionSubmissionApproval $approval)
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





	public function detail(AttendancePermissionSubmission $attendancePermissionSubmission)
	{
		return view('admin.attendance_permission_submission.detail', [
			'title'         => 'Detail Pengajuan Izin Terlambat / Pulang Cepat',
			'attendancePermissionSubmission' => $attendancePermissionSubmission,
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Izin Terlambat / Pulang Cepat',
					'link'  => route('admin.attendance_permission_submission')
				],
				[
					'title' => 'Detail',
					'link'  => route('admin.attendance_permission_submission.detail', $attendancePermissionSubmission->id)
				]
			]
		]);
	}


	public function destroy(AttendancePermissionSubmission $attendancePermissionSubmission)
	{
		try {
			DB::beginTransaction();
			$attendancePermissionSubmission->deleteAttendancePermissionSubmission();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function approve(Request $request, AttendancePermissionSubmission $attendancePermissionSubmission)
	{
		try {
			$employee = $attendancePermissionSubmission->employee;

			if (empty($employee)) {
				return \Res::invalid([
					'message' => 'Karyawan sudah dihapus'
				]);
			}

			DB::beginTransaction();
			$attendancePermissionSubmission->approveAttendancePermissionSubmissionByAdmin($request);
			DB::commit();

			return \Res::success([
				'message'   => 'Berhasil disetujui'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function reject(Request $request, AttendancePermissionSubmission $attendancePermissionSubmission)
	{
		try {
			DB::beginTransaction();
			$attendancePermissionSubmission->rejectLeaveSubmissionByAdmin();
			DB::commit();

			return \Res::success([
				'message'   => 'Berhasil ditolak'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function cancel(Request $request, AttendancePermissionSubmission $attendancePermissionSubmission)
	{
		try {
			DB::beginTransaction();
			$attendancePermissionSubmission->cancelLeaveSubmissionByAdmin();
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
