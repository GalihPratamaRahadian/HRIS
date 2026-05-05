<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use DB;
use File;
use Illuminate\Http\Request;
use Response;

class AttendanceController extends Controller
{

	public function index(Request $request)
	{
		if(auth()->user()->isStaff()) return $this->staffAttendanceDashboard($request);

		if($request->ajax())
		{
			return Attendance::dataTable($request);
		}

		return view('admin.attendance.index', [
			'title'         => 'Kehadiran',
			'breadcrumbs'   => [
				[
					'title' => 'Kehadiran',
					'link'  => route('attendance')
				],
			]
		]);
	}


	public function staffAttendanceDashboard($request)
	{
		$limit = 7;
		$page = $request->page ?? 1;
		$skip = ($page - 1) * $limit;
		$amount = Attendance::where('id_employee', auth()->user()->employee->id)
							->count();

		$attendances = Attendance::where('id_employee', auth()->user()->employee->id)
								->take($limit)
								->skip($skip)
								->orderBy('date', 'desc')
								->get();
		$amountPage = ceil($amount / $limit);
		$startPage = $page - 1;
		$startPage = $startPage >= 1 ? $startPage : 1;
		$endPage = $page + 1;
		$endPage = $endPage <= $amountPage ? $endPage : $amountPage;

		return view('admin.attendance.index_staff', [
			'title'         => 'Kehadiran',
			'amountPage'	=> $amountPage,
			'activePage'	=> $page,
			'startPage'		=> $startPage,
			'endPage'		=> $endPage,
			'attendances'	=> $attendances,
			'breadcrumbs'   => [
				[
					'title' => 'Kehadiran',
					'link'  => route('attendance')
				],
			]
		]);
	}


	public function xhrGetAttendanceData(Request $request)
	{
		try {
			if($request->start_date != $request->end_date) {
				$date = date('d M Y', strtotime($request->start_date)).' - '.date('d M Y', strtotime($request->end_date));
			} else {
				$date = date('d M Y', strtotime($request->start_date));
			}

			return \Setting::successResponse([
				'summary'	=> Attendance::getAttendanceSummary($request),
				'date'		=> $date,
			]);
		} catch (\Exception $e) {
			return \Setting::errorResponse($e);
		}
	}


	public function detail(Attendance $attendance)
	{
		if(auth()->user()->isEmployee()) {
			if(auth()->user()->employee->id != $attendance->id_employee) {
				abort(404);
			}
		}

		return view('admin.attendance.detail', [
			'title'			=> 'Detail Kehadiran',
			'attendance'	=> $attendance,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Kehadiran',
					'link'	=> route('attendance')
				],
				[
					'title'	=> 'Detail Kehadiran',
					'link'	=> route('attendance.detail', $attendance->id)
				]
			]
		]);
	}


	public function edit(Attendance $attendance)
	{
		return view('admin.attendance.edit', [
			'title'			=> 'Edit Kehadiran',
			'attendance'	=> $attendance,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Kehadiran',
					'link'	=> route('attendance')
				],
				[
					'title'	=> 'Edit Kehadiran',
					'link'	=> route('attendance.edit', $attendance->id)
				]
			]
		]);
	}


	public function update(Request $request, Attendance $attendance)
	{
		DB::beginTransaction();

		try {
			$attendance->updateAttendance($request);
			DB::commit();

			return \Setting::updateResponse();
		} catch (\Exception $e) {
			DB::rollback();

			return \Setting::errorResponse($e);
		}
	}


	public function exportToPdf(Request $request)
	{
		$data = Attendance::has('employee')
					->with([ 'employee.department', 'attendanceMeta' ]);

		if(!empty($request->start_date) && !empty($request->end_date)) {
			$data = $data->where('date', '>=', $request->start_date)
						->where('date', '<=', $request->end_date);
		}

		if(!empty($request->id_department)) {
			$department = $request->id_department;
			$data = $data->whereHas('employee', function($query) use ($department) {
				$query->where('id_department', $department);
			});
		}

		$data = $data->get();

		$pdf = \PDF::loadView('admin.attendance.export_to_pdf', [
			'attendances'	=> $data,
		]);

		return $pdf->stream('Kehadiran.pdf');
	}


	public function editClockInPhoto(Attendance $attendance)
	{
		return view('admin.attendance.edit_clock_in_photo', [
			'title'			=> 'Edit Foto Kehadiran',
			'attendance'	=> $attendance,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Kehadiran',
					'link'	=> route('attendance')
				],
				[
					'title'	=> 'Edit Foto Kehadiran',
					'link'	=> route('attendance.edit_clock_in_photo', $attendance->id)
				]
			]
		]);
	}


	public function updateClockInPhoto(Request $request, Attendance $attendance)
	{
		DB::beginTransaction();

		try {
			$attendance->saveClockInPhoto($request);
			DB::commit();

			return \Setting::updateResponse();
		} catch (\Exception $e) {
			DB::rollback();

			return \Setting::errorResponse($e);
		}
	}


	public function createAttendancesSummary()
	{
		return view('admin.attendance.create_attendances_summary', [
			'title'			=> 'Buat Rekapan Kehadiran',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Kehadiran',
					'link'	=> route('attendance')
				],
				[
					'title'	=> 'Buat Rekapan Kehadiran',
					'link'	=> route('attendance.create_summary')
				]
			]
		]);
	}


	public function generateAttendancesSummary(Request $request)
	{
		$request->validate([
			'id_department'	=> 'required',
			'start_date'	=> 'required',
			'end_date'		=> 'required'
		]);

		try {
			$result = Attendance::generateAttendancesSummary($request);

			return \Setting::successResponse($result);
		} catch (\Exception $e) {
			return \Setting::errorResponse($e);
		}
	}


	public function destroy(Attendance $attendance)
	{
		DB::beginTransaction();

		try {
			$attendance->deleteAttendance();
			DB::commit();

			return \Setting::deleteResponse();
		} catch (\Exception $e) {
			DB::rollback();

			return \Setting::errorResponse($e);
		}
	}

	public function sendClockInNotification(Attendance $attendance)
	{
		DB::beginTransaction();

		try {
			$attendance->sendClockInNotification();
			DB::commit();
			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function sendClockOutNotification(Attendance $attendance)
	{
		DB::beginTransaction();

		try {
			$attendance->sendClockOutNotification();
			DB::commit();

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
