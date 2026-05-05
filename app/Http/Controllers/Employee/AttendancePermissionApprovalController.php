<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendancePermissionSubmissionApproval;
use DB;

class AttendancePermissionApprovalController extends Controller
{
	
	public function index(Request $request)
	{
		if($request->ajax()) {
			return AttendancePermissionSubmissionApproval::dt($request);
		}

		return view('employee.attendance_permission_approval.index', [
			'title'         => 'Penyetujuan Izin Terlambat / Pulang Cepat',
			'breadcrumbs'   => [
				[
					'title' => 'Penyetujuan Izin Terlambat / Pulang Cepat',
					'link'  => route('employee.attendance_permission_approval')
				],
			]
		]);
	}

	public function detail(AttendancePermissionSubmissionApproval $attendancePermissionApproval)
	{
		$attendancePermissionApproval->load('attendancePermissionSubmission');
		if(!$attendancePermissionApproval->attendancePermissionSubmission) abort(404);

		return view('employee.attendance_permission_approval.detail', [
			'title'         => 'Detail Penyetujuan Izin Terlambat / Pulang Cepat',
			'approval'      => $attendancePermissionApproval,
			'breadcrumbs'   => [
				[
					'title' => 'Penyetujuan Izin Terlambat / Pulang Cepat',
					'link'  => route('employee.attendance_permission_approval')
				],
				[
					'title' => 'Detail',
					'link'  => route('employee.attendance_permission_approval.detail', $attendancePermissionApproval->id)
				],
			]
		]);
	}

	public function approve(AttendancePermissionSubmissionApproval $attendancePermissionApproval)
	{
		DB::beginTransaction();

		try {
			$attendancePermissionApproval->approve();
			DB::commit();

			return \Res::success([
				'message'   => 'Berhasil disetujui'
			]);
		} catch (\Exception $e) {
			DB::commit();

			return \Res::error($e);
		}
	}

	public function reject(AttendancePermissionSubmissionApproval $attendancePermissionApproval)
	{
		DB::beginTransaction();

		try {
			$attendancePermissionApproval->reject();
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
