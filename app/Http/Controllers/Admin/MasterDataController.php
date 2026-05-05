<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OvertimeReason;
use App\Models\LeaveReason;
use App\Models\SickReason;
use App\Models\NecessityReason;
use Validations;
use DB;

class MasterDataController extends Controller
{
	/**
	*   Leave Reason
	*/
	public function leaveReasonIndex(Request $request)
	{
		if($request->ajax()) {
			return LeaveReason::dataTable($request);
		}

		return view('admin.leave_reason.index', [
			'title'         => 'Alasan Cuti',
			'breadcrumbs'   => [
				[
					'title' => 'Alasan Cuti',
					'link'  => route('admin.leave_reason')
				],
			]
		]);
	}

	public function leaveReasonCreate()
	{
		return view('admin.leave_reason.create', [
			'title'         => 'Tambah Alasan Cuti',
			'breadcrumbs'   => [
				[
					'title' => 'Alasan Cuti',
					'link'  => route('admin.leave_reason')
				],
				[
					'title' => 'Tambah',
					'link'  => route('admin.leave_reason.create')
				],
			]
		]);
	}

	public function leaveReasonStore(Request $request)
	{
		DB::beginTransaction();

		try {
			LeaveReason::createLeaveReason($request->all());
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function leaveReasonEdit(LeaveReason $leaveReason)
	{
		return view('admin.leave_reason.edit', [
			'title'         => 'Edit Alasan Cuti',
			'leaveReason'   => $leaveReason,
			'breadcrumbs'   => [
				[
					'title' => 'Alasan Cuti',
					'link'  => route('admin.leave_reason')
				],
				[
					'title' => 'Edit',
					'link'  => route('admin.leave_reason.edit', $leaveReason->id)
				],
			]
		]);
	}

	public function leaveReasonUpdate(Request $request, LeaveReason $leaveReason)
	{
		DB::beginTransaction();

		try {
			$leaveReason->updateLeaveReason($request->all());
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function leaveReasonDestroy(LeaveReason $leaveReason)
	{
		DB::beginTransaction();

		try {
			$leaveReason->deleteLeaveReason();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}



	/**
	*   Sick Reason
	*/
	public function sickReasonIndex(Request $request)
	{
		if($request->ajax()) {
			return SickReason::dataTable($request);
		}

		return view('admin.sick_reason.index', [
			'title'         => 'Alasan Sakit',
			'breadcrumbs'   => [
				[
					'title' => 'Alasan Sakit',
					'link'  => route('admin.sick_reason')
				],
			]
		]);
	}

	public function sickReasonCreate()
	{
		return view('admin.sick_reason.create', [
			'title'         => 'Tambah Alasan Sakit',
			'breadcrumbs'   => [
				[
					'title' => 'Alasan Sakit',
					'link'  => route('admin.sick_reason')
				],
				[
					'title' => 'Tambah',
					'link'  => route('admin.sick_reason.create')
				],
			]
		]);
	}

	public function sickReasonStore(Request $request)
	{
		DB::beginTransaction();

		try {
			SickReason::createSickReason($request->all());
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function sickReasonEdit(SickReason $sickReason)
	{
		return view('admin.sick_reason.edit', [
			'title'         => 'Edit Alasan Sakit',
			'sickReason'    => $sickReason,
			'breadcrumbs'   => [
				[
					'title' => 'Alasan Sakit',
					'link'  => route('admin.sick_reason')
				],
				[
					'title' => 'Edit',
					'link'  => route('admin.sick_reason.edit', $sickReason->id)
				],
			]
		]);
	}

	public function sickReasonUpdate(Request $request, SickReason $sickReason)
	{
		DB::beginTransaction();

		try {
			$sickReason->updateSickReason($request->all());
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function sickReasonDestroy(SickReason $sickReason)
	{
		DB::beginTransaction();

		try {
			$sickReason->deleteSickReason();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}



	/**
	*   Necessity Reason
	*/
	public function necessityReasonIndex(Request $request)
	{
		if($request->ajax()) {
			return NecessityReason::dataTable($request);
		}

		return view('admin.necessity_reason.index', [
			'title'         => 'Alasan Izin',
			'breadcrumbs'   => [
				[
					'title' => 'Alasan Izin',
					'link'  => route('admin.necessity_reason')
				],
			]
		]);
	}

	public function necessityReasonCreate()
	{
		return view('admin.necessity_reason.create', [
			'title'         => 'Tambah Alasan Izin',
			'breadcrumbs'   => [
				[
					'title' => 'Alasan Izin',
					'link'  => route('admin.necessity_reason')
				],
				[
					'title' => 'Tambah',
					'link'  => route('admin.necessity_reason.create')
				],
			]
		]);
	}

	public function necessityReasonStore(Request $request)
	{
		DB::beginTransaction();

		try {
			NecessityReason::createNecessityReason($request->all());
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function necessityReasonEdit(NecessityReason $necessityReason)
	{
		return view('admin.necessity_reason.edit', [
			'title'         => 'Edit Alasan Izin',
			'necessityReason' => $necessityReason,
			'breadcrumbs'   => [
				[
					'title' => 'Alasan Izin',
					'link'  => route('admin.necessity_reason')
				],
				[
					'title' => 'Edit',
					'link'  => route('admin.necessity_reason.edit', $necessityReason->id)
				],
			]
		]);
	}

	public function necessityReasonUpdate(Request $request, NecessityReason $necessityReason)
	{
		DB::beginTransaction();

		try {
			$necessityReason->updateNecessityReason($request->all());
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function necessityReasonDestroy(NecessityReason $necessityReason)
	{
		DB::beginTransaction();

		try {
			$necessityReason->deleteNecessityReason();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}



	/**
	*   Overtime Reason
	*/
	public function overtimeReasonIndex(Request $request)
	{
		if($request->ajax()) {
			return OvertimeReason::dataTable($request);
		}

		return view('admin.overtime_reason.index', [
			'title'         => 'Alasan Lembur',
			'breadcrumbs'   => [
				[
					'title' => 'Alasan Lembur',
					'link'  => route('admin.overtime_reason')
				],
			]
		]);
	}

	public function overtimeReasonCreate()
	{
		return view('admin.overtime_reason.create', [
			'title'         => 'Tambah Alasan Lembur',
			'breadcrumbs'   => [
				[
					'title' => 'Alasan Lembur',
					'link'  => route('admin.overtime_reason')
				],
				[
					'title' => 'Tambah',
					'link'  => route('admin.overtime_reason.create')
				],
			]
		]);
	}

	public function overtimeReasonStore(Request $request)
	{
		DB::beginTransaction();

		try {
			OvertimeReason::createOvertimeReason($request->all());
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function overtimeReasonEdit(OvertimeReason $overtimeReason)
	{
		return view('admin.overtime_reason.edit', [
			'title'         => 'Edit Alasan Lembur',
			'overtimeReason'    => $overtimeReason,
			'breadcrumbs'   => [
				[
					'title' => 'Alasan Lembur',
					'link'  => route('admin.overtime_reason')
				],
				[
					'title' => 'Edit',
					'link'  => route('admin.overtime_reason.edit', $overtimeReason->id)
				],
			]
		]);
	}

	public function overtimeReasonUpdate(Request $request, OvertimeReason $overtimeReason)
	{
		DB::beginTransaction();

		try {
			$overtimeReason->updateOvertimeReason($request->all());
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function overtimeReasonDestroy(OvertimeReason $overtimeReason)
	{
		DB::beginTransaction();

		try {
			$overtimeReason->deleteOvertimeReason();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

}
