<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MyClass\Validations;
use DB;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveReason;
use App\Models\LeaveSubmission;
use App\Models\LeaveSubmissionApproval;
use App\Models\OvertimeReason;
use App\Models\OvertimeSubmission;
use App\Models\OvertimeSubmissionApproval;
use App\Models\AttendancePermissionSubmission;
use App\Models\AttendancePermissionSubmissionApproval;
use App\Models\Store;
use App\Models\StoreVisit;
use App\Models\CheckDay;

class EmployeePageController extends Controller
{

	/**
	*	Clock In
	*/
	public function clockIn(Request $request)
	{
		$employee = auth()->user()->employee;
		if(!$employee->isAllowCreateAttendanceViaWeb() || !$employee->isAllowForClockIn()) {
			return redirect()->route('dashboard');
		}

		$forOvertime = $request->for_overtime ? true : false;

		// Kehadiran Aktif
		$attendance = $employee->latestAttendance;

		if(!$attendance) {
			return $this->clockInView();
		}

		if(!$attendance->isAlreadyClockOut())
		{
			return $this->cantClockIn([
				'message'			=> 'Anda telah mengisi jam masuk',
				'is_allow_overtime'	=> false,
			]);
		}
		else
		{
			$isOffDay = $employee->isOffday();

			if($isOffDay) 
			{
				if($forOvertime) 
				{
					return $this->clockInView();
				} 
				else 
				{
					return $this->cantClockIn([
						'message'			=> 'Hari ini libur, apakah anda ingin ambil jam lembur?',
						'is_allow_overtime'	=> true,
					]);
				}
			} 
			else
			{
				return $this->clockInView();
			}
		}
	}


	private function clockInView()
	{
		return view('employee.attendance.clock_in', [
			'title'			=> 'Check In',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Check In',
					'link'	=> route('attendance.clock_in')
				]
			]
		]);
	}


	public function clockInProcess(Request $request)
	{
		$request->validate([
			'blobImage' => 'required',
			'latitude'  => 'required',
			'longitude' => 'required',
		]);
		DB::beginTransaction();

		try {
			$response = Attendance::createAttendanceViaWebApp($request);
			DB::commit();

			return $response;
		} catch (Exception $e) {
			DB::rollback();

			return \Setting::errorResponse($e);
		}
	}


	public function clockOut(Request $request)
	{
		$employee = auth()->user()->employee;
		if(!$employee->isAllowCreateAttendanceViaWeb()) return redirect()->route('dashboard');

		$attendance = $employee->latestAttendance;
		if(!$attendance) abort(404);

		if($employee->isAllowForClockOut()) {

			return view('employee.attendance.clock_out', [
				'title'			=> 'Check Out',
				'attendance'	=> $attendance,
				'breadcrumbs'	=> [
					[
						'title'	=> 'Dashboard',
						'link'	=> route('dashboard')
					],
					[
						'title'	=> 'Check Out',
						'link'	=> route('attendance.clock_out')
					]
				]
			]);
		}
		else
		{
			return $this->cantClockOut();
		}
	}


	public function clockOutProcess(Request $request)
	{
		$request->validate([
			'blobImage' => 'required',
			'latitude'  => 'required',
			'longitude' => 'required',
		]);
		DB::beginTransaction();

		try {
			$attendance = auth()->user()->employee->latestAttendance;
			$response = $attendance->clockOutViaWebApp($request);
			DB::commit();

			return $response;
			
		} catch (Exception $e) {
			DB::rollback();

			return \Setting::errorResponse($e);
		}
	}


	/**
	*	Check Day
	*/
	public function checkDay(Request $request)
	{
		$employee = auth()->user()->employee;
		if($employee->isAlreadyClockOut()) {
			return redirect()->route('dashboard');
		}

		return view('employee.attendance.check_day', [
			'title'			=> 'Check Day',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Check Day',
					'link'	=> route('attendance.check_day')
				]
			]
		]);
	}


	public function checkDayProcess(Request $request)
	{
		$request->validate([
			'blobImage' => 'required',
			'latitude'  => 'required',
			'longitude' => 'required',
		]);

		try {
			$response = CheckDay::createCheckDay($request);

			return $response;
		} catch (Exception $e) {
			DB::rollback();

			return \Setting::errorResponse($e);
		}
	}


	public function cantClockIn($data)
	{
		return view('employee.attendance.cant_clock_in', [
			'title'			=> 'Check In',
			'data'  		=> $data,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Check In',
					'link'	=> route('attendance.clock_in')
				]
			]
		]);
	}


	public function cantClockOut($message = null)
	{
		if(empty($message)) $message = 'Kamu belum dapat mengisi jam keluar';

		return view('employee.attendance.cant_clock_out', [
			'title'			=> 'Check Out',
			'message'  		=> $message,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Check Out',
					'link'	=> route('attendance.clock_out')
				]
			]
		]);
	}


	public function employeeFamily(Request $request)
	{
		return view('employee.employee_family.index', [
			'title'			=> 'Keluarga',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Kelengkapan Data',
					'link'	=> 'javascript:void(0);'
				],
				[
					'title'	=> 'Keluarga',
					'link'	=> route('employee.employee_family')
				]
			]
		]);
	}


	public function employeeEducation(Request $request)
	{
		return view('employee.employee_education.index', [
			'title'			=> 'Pendidikan',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Kelengkapan Data',
					'link'	=> 'javascript:void(0);'
				],
				[
					'title'	=> 'Pendidikan',
					'link'	=> route('employee.employee_education')
				]
			]
		]);
	}


	public function employeeTraining(Request $request)
	{
		return view('employee.employee_training.index', [
			'title'			=> 'Pelatihan',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Kelengkapan Data',
					'link'	=> 'javascript:void(0);'
				],
				[
					'title'	=> 'Pelatihan',
					'link'	=> route('employee.employee_family')
				]
			]
		]);
	}




	



	/**
	 * 	Overtime Approval
	 * */
	public function overtimeApprovalIndex(Request $request)
	{
		if($request->ajax()) {
			return OvertimeSubmissionApproval::dt($request);
		}

		return view('employee.overtime_approval.index', [
			'title'			=> 'Penyetujuan Lembur',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Penyetujuan Lembur',
					'link'	=> route('employee.overtime_approval')
				],
			]
		]);
	}

	public function overtimeApprovalDetail(OvertimeSubmissionApproval $overtimeSubmissionApproval)
	{
		$overtimeSubmissionApproval->load('overtimeSubmission');
		if(!$overtimeSubmissionApproval->overtimeSubmission) abort(404);

		return view('employee.overtime_approval.detail', [
			'title'			=> 'Detail Penyetujuan Lembur',
			'approval' 		=> $overtimeSubmissionApproval,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Penyetujuan Lembur',
					'link'	=> route('employee.overtime_approval')
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('employee.overtime_approval.detail', $overtimeSubmissionApproval->id)
				],
			]
		]);
	}

	public function overtimeApprovalApprove(OvertimeSubmissionApproval $overtimeSubmissionApproval)
	{
		DB::beginTransaction();

		try {
			$overtimeSubmissionApproval->approve();
			DB::commit();

			return \Res::success([
				'message'	=> 'Berhasil disetujui'
			]);
		} catch (\Exception $e) {
			DB::commit();

			return \Res::error($e);
		}
	}

	public function overtimeApprovalReject(OvertimeSubmissionApproval $overtimeSubmissionApproval)
	{
		DB::beginTransaction();

		try {
			$overtimeSubmissionApproval->reject();
			DB::commit();

			return \Res::success([
				'message'	=> 'Berhasil ditolak'
			]);
		} catch (\Exception $e) {
			DB::commit();

			return \Res::error($e);
		}
	}


	/**
	 * 	Profile
	 * */
	public function personalProfile()
	{
		return view('employee.personal_profile.index', [
			'title'			=> 'Data Diri',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Data Diri',
					'link'	=> route('emp.personal_profile')
				],
			]
		]);
	}

	public function personalProfileDownload()
	{
		try {
			return employee()->downloadCurriculumVitae();
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}



	/**
	 * 	Sales Tracking
	 * */
	public function salesTrackingIndex()
	{
		return view('employee.sales_tracking.index', [
			'title'			=> 'Kunjungan Toko',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Kunjungan Toko',
					'link'	=> route('emp.sales_tracking')
				],
			]
		]);
	}

	public function salesTrackingCreateStore()
	{
		return view('employee.sales_tracking.create_store', [
			'title'			=> 'Daftarkan Toko Baru',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Kunjungan Toko',
					'link'	=> route('emp.sales_tracking')
				],
				[
					'title'	=> 'Daftarkan Toko Baru',
					'link'	=> route('emp.sales_tracking.create_store')
				]
			]
		]);
	}

	public function salesTrackingSaveStore(Request $request)
	{
		$request->validate([
			'store_name'	=> 'required',
			// 'phone_number'	=> 'required',
			'address'		=> 'required',
			'latitude'		=> 'required',
			'longitude'		=> 'required',
		]);
		DB::beginTransaction();

		try {
			Store::createStore($request);
			DB::commit();

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function salesTrackingGetStore(Request $request)
	{
		try {
			$stores = Store::where('handled_by', employee()->id);
			$search = $request->search;
			
			if(!empty($search)) {
				$stores = $stores->where(function($query) use ($search) {
					$query->where('store_name', 'like', '%'.$search.'%')
						  ->orWhere('address', 'like', '%'.$search.'%');
				});
			}

			$stores = $stores->get();

			$results = [];

			foreach($stores as $store) {
				$distanceText = '0 Meter';
				$distance = 0;
				if(!empty($request->latitude) && !empty($request->longitude)) {
					$distance = $store->distanceInMeters($request->latitude, $request->longitude);
					$distanceText = $store->distanceText($request->latitude, $request->longitude);
				}

				$link = route('emp.sales_tracking.create_store_check_in', $store->id);

				$results[] = (object) [
					'store_name'	=> $store->store_name,
					'address'		=> $store->address,
					'distance'		=> $distance,
					'distance_text'	=> $distanceText,
					'checkin_link'	=> $link,
					'is_visited_today' => $store->isVisitedToday(),
				];
			}

			return  \Res::success([
				'stores' => $results
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function salesTrackingStoreCheckIn(Store $store)
	{
		return view('employee.sales_tracking.store_checkin', [
			'title'			=> 'Check In Toko',
			'store'			=> $store,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Kunjungan Toko',
					'link'	=> route('emp.sales_tracking')
				],
				[
					'title'	=> 'Check In Toko',
					'link'	=> route('emp.sales_tracking.create_store_check_in', $store->id)
				]
			]
		]);
	}


	public function salesTrackingStoreCheckInSave(Request $request, Store $store)
	{
		DB::beginTransaction();

		try {
			if($store->isLocationValid($request->latitude, $request->longitude)) {
				StoreVisit::createStoreVisit($request);
				DB::commit();

				return  \Res::success();
			} else {
				return \Res::invalid([
					'message'	=> 'Harap Lakukan Check In di Area Toko'
				]);
			}
		} catch (\Exception $e) {
			DB::rollback();
			return \Res::error($e);
		}
	}
}

