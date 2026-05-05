<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Models\SickReason;
use Illuminate\Http\Request;
use App\Models\NecessityReason;
use App\Http\Controllers\Controller;
use App\Models\SickNecessitySubmission;
use App\Models\SickNecessitySubmissionApproval;

class SickNecessitySubmissionController extends Controller
{
	public function index(Request $request)
	{
		if($request->ajax()) {
			return SickNecessitySubmission::dataTable($request);
		}

		return view('admin.sick_necessity_submission.index', [
			'title'         => 'Pengajuan Sakit/Izin',
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Sakit/Izin',
					'link'  => route('admin.sick_necessity_submission')
				]
			]
		]);
	}

	public function create()
	{
		return view('admin.sick_necessity_submission.create', [
			'title'         => 'Tambah Pengajuan Sakit/Izin',
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Sakit/Izin',
					'link'  => route('admin.sick_necessity_submission')
				],
				[
					'title' => 'Tambah',
					'link'  => route('admin.sick_necessity_submission.create')
				]
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
			\DB::beginTransaction();
			$sickNecessitySubmission = SickNecessitySubmission::create([
				'id_employee'	=> $request->id_employee,
				'type' 			=> $request->type,
				'id_sick_reason' => $request->id_sick_reason,
				'id_necessity_reason' => $request->id_necessity_reason,
				'reason'		=> $request->reason,
				'start_date'	=> $request->start_date,
				'end_date'		=> $request->end_date,
				'description'	=> $request->description,
				'status'		=> SickNecessitySubmission::STATUS_WAIT,
			]);
			$sickNecessitySubmission->saveFile($request);
			\DB::commit();

			if($request->submission_approval_status == 'Approve') {
				$notification = $request->send_notification == 'Ya';
				$sickNecessitySubmission->approveSickNecessitySubmissionByAdmin($notification);
			} else {
				$sickNecessitySubmission->createSickNecessitySubmissionApprovals();
				$sickNecessitySubmission->sendNotificationToAdmin();
			}

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function detail(SickNecessitySubmission $sickNecessitySubmission)
	{
		return view('admin.sick_necessity_submission.detail', [
			'title'         => 'Detail Pengajuan Sakit/Izin',
			'sickNecessitySubmission' => $sickNecessitySubmission,
			'breadcrumbs'   => [
				[
					'title' => 'Pengajuan Sakit/Izin',
					'link'  => route('admin.sick_necessity_submission')
				],
				[
					'title' => 'Detail',
					'link'  => route('admin.sick_necessity_submission.detail', $sickNecessitySubmission->id)
				]
			]
		]);
	}

    public function resendBroadcastToApproval(SickNecessitySubmissionApproval $approval)
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


	public function destroy(SickNecessitySubmission $sickNecessitySubmission)
	{
		try {
			DB::beginTransaction();
			$sickNecessitySubmission->deleteSickNecessitySubmission();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function approve(Request $request, SickNecessitySubmission $sickNecessitySubmission)
	{
		try {
			$employee = $sickNecessitySubmission->employee;

			if(empty($employee)) {
				return \Res::invalid([
					'message' => 'Karyawan sudah dihapus'
				]);
			}

			DB::beginTransaction();
			$sickNecessitySubmission->approveSickNecessitySubmissionByAdmin($request);
			DB::commit();

			return \Res::success([
				'message'   => 'Berhasil disetujui'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function reject(Request $request, SickNecessitySubmission $sickNecessitySubmission)
	{
		try {
			DB::beginTransaction();
			$sickNecessitySubmission->rejectSickNecessitySubmissionByAdmin();
			DB::commit();

			return \Res::success([
				'message'   => 'Berhasil ditolak'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function cancel(Request $request, SickNecessitySubmission $sickNecessitySubmission)
	{
		try {
			DB::beginTransaction();
			$sickNecessitySubmission->cancelSickNecessitySubmissionByAdmin();
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
