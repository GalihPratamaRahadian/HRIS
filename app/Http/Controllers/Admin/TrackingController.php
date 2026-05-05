<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrackingLocation;
use App\Models\TrackingEmployee;
use App\Models\Tracking;
use DB;

class TrackingController extends Controller
{
	/**
	 * 	Tracking Location
	 * */
	public function trackingLocationIndex(Request $request)
	{
		if($request->ajax()) {
			return TrackingLocation::dataTable($request);
		}

		return view('admin.tracking_location.index', [
			'title'			=> 'Lokasi Tracking',
			'breadcrumbs'	=> [
				[
					'title' => 'Lokasi Tracking',
					'link'  => route('admin.tracking_location')
				],
			]
		]);
	}

	public function trackingLocationCreate()
	{
		return view('admin.tracking_location.create', [
			'title'			=> 'Tambah Lokasi Tracking',
			'breadcrumbs'	=> [
				[
					'title' => 'Lokasi Tracking',
					'link'  => route('admin.tracking_location')
				],
				[
					'title' => 'Tambah',
					'link'  => route('admin.tracking_location.create')
				],
			]
		]);
	}

	public function trackingLocationStore(Request $request)
	{
		DB::beginTransaction();

		try {
			TrackingLocation::createTrackingLocation($request);
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function trackingLocationEdit(TrackingLocation $trackingLocation)
	{
		return view('admin.tracking_location.edit', [
			'title'			=> 'Edit Lokasi Tracking',
			'trackingLocation' => $trackingLocation,
			'breadcrumbs'	=> [
				[
					'title' => 'Lokasi Tracking',
					'link'  => route('admin.tracking_location')
				],
				[
					'title' => 'Edit',
					'link'  => route('admin.tracking_location.edit', $trackingLocation->id)
				],
			]
		]);
	}

	public function trackingLocationUpdate(Request $request, TrackingLocation $trackingLocation)
	{
		DB::beginTransaction();

		try {
			$trackingLocation->updateTrackingLocation($request);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function trackingLocationDestroy(TrackingLocation $trackingLocation)
	{
		DB::beginTransaction();

		try {
			$trackingLocation->deleteTrackingLocation();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function trackingLocationImport(Request $request)
	{
		try {
			$result = TrackingLocation::importFromExcel($request);

			return \Res::success([
				'message'	=> 'Berhasil mengimport '.$result.' data',
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}



	/**
	 * 	Tracking Employee
	 * */
	public function trackingEmployeeIndex(Request $request)
	{
		if($request->ajax()) {
			return TrackingEmployee::dataTable($request);
		}

		return view('admin.tracking_employee.index', [
			'title'			=> 'Karyawan Yg Di Tracking',
			'breadcrumbs'	=> [
				[
					'title' => 'Karyawan Yg Di Tracking',
					'link'  => route('admin.tracking_employee')
				],
			]
		]);
	}

	public function trackingEmployeeCreate()
	{
		return view('admin.tracking_employee.create', [
			'title'			=> 'Tambah Karyawan Yg Di Tracking',
			'breadcrumbs'	=> [
				[
					'title' => 'Karyawan Yg Di Tracking',
					'link'  => route('admin.tracking_employee')
				],
				[
					'title' => 'Tambah',
					'link'  => route('admin.tracking_employee.create')
				],
			]
		]);
	}

	public function trackingEmployeeStore(Request $request)
	{
		DB::beginTransaction();

		try {
			$employeeIds = $request->id_employees;
			foreach($employeeIds as $employeeId) {
				$trackingEmployee = TrackingEmployee::where('id_employee', $employeeId)
													->first();
				if(!$trackingEmployee) {
					TrackingEmployee::createTrackingEmployee([
						'id_employee'	=> $employeeId
					]);
				}
			}
			DB::commit();

			return \Res::save([
				'ids' => $employeeIds
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function trackingEmployeeDestroy(TrackingEmployee $trackingEmployee)
	{
		DB::beginTransaction();

		try {
			$trackingEmployee->deleteTrackingEmployee();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function trackingIndex(Request $request)
	{
		if($request->ajax()) {
			return Tracking::dataTable($request);
		}

		return view('admin.tracking.index', [
			'title'			=> 'Hasil Tracking',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Hasil Tracking',
					'link'	=> route('admin.tracking')
				]
			]
		]);
	}


	public function trackingDetail(Tracking $tracking)
	{
		return view('admin.tracking.detail', [
			'title'			=> 'Detail Hasil Tracking',
			'tracking'		=> $tracking,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Hasil Tracking',
					'link'	=> route('admin.tracking')
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('admin.tracking.detail', $tracking->id)
				]
			]
		]);
	}
}
