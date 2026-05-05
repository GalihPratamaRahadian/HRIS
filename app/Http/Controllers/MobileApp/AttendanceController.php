<?php

namespace App\Http\Controllers\MobileApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileAppToken;
use App\Models\Attendance;
use DB;

class AttendanceController extends Controller
{
	public function setClockIn(Request $request)
	{
		$request->validate([
			'photo'         => 'required',
			'latitude'      => 'required',
			'longitude'     => 'required',
		], [
			'photo.required' => 'Foto Dibutuhkan',
			'latitude.required' => 'Koordinat Latitude Dibutuhkan',
			'longitude.required' => 'Koordinat Longitude Dibutuhkan',
		]);

		DB::beginTransaction();

		try {
			$token = MobileAppToken::getByToken($request->token);
			$employee = $token->employee;

			if($employee->isAllowForClockIn()) {
				$result = Attendance::createAttendanceViaMobileApp($request);
				DB::commit();
				
				return $result;
			} else {
				return \Res::invalid([
					'message'   => 'Kamu belum boleh mengisi jam masuk',
				]);
			}

		} catch (\Exception $e) {
			DB::rollback();
			return \Res::error($e);
		}
	}


	public function setClockOut(Request $request)
	{
		$request->validate([
			'photo'         => 'required',
			'latitude'      => 'required',
			'longitude'     => 'required',
		], [
			'photo.required' => 'Foto Dibutuhkan',
			'latitude.required' => 'Koordinat Latitude Dibutuhkan',
			'longitude.required' => 'Koordinat Longitude Dibutuhkan',
		]);

		DB::beginTransaction();

		try {
			$token = MobileAppToken::getByToken($request->token);
			$employee = $token->employee;

			if($employee->isAllowForClockOut()) {
				$result = Attendance::clockOutViaMobileApp($request);
				DB::commit();
				
				return $result;
			} else {
				return \Res::invalid([
					'message'   => 'Kamu belum boleh mengisi jam keluar',
				]);
			}

		} catch (\Exception $e) {
			DB::rollback();
			return \Res::error($e);
		}
	}


	public function list(Request $request)
	{
		try {
			$token = MobileAppToken::getByToken($request->token);
			$employee = $token->employee;

			$attendances = Attendance::where('id_employee', $employee->id)
									 ->orderBy('date', 'desc');
			$page = $request->page ?? 1;
			$limit = $request->limit ?? 5;

			$attendances = $attendances->take($limit)
									   ->skip(($page - 1) * $limit)
									   ->get();
				
			return \Res::success([
				'result' => [
					'attendances'   => Attendance::fetchAttendances($attendances),
				]
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function detail(Request $request)
	{
		$request->validate([
			'id_attendance' => 'required|exists:attendances,id',
		], [
			'id_attendance.required'=> 'ID Kehadiran diperlukan',
			'id_attendance.exists'  => 'Data kehadiran tidak terdaftar'
		]);

		try {
			$attendance = Attendance::find($request->id_attendance);
				
			return \Res::success([
				'result' => [
					'attendance'    => $attendance->fetchData(),
				]
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
