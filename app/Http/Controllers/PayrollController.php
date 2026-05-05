<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\PayrollAttendance;
use App\Models\Attendance;
use DB;

class PayrollController extends Controller
{
	
	public function index(Request $request)
	{
		if(auth()->user()->isEmployee()) return $this->indexStaff($request);

		if($request->ajax()) {
			return Payroll::dataTable($request);
		}

		return view('payroll.index', [
			'title'			=> 'Penggajian',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Penggajian',
					'link'	=> route('payroll')
				]
			]
		]);
	}


	private function indexStaff($request)
	{
		$limit = 7;
		$page = $request->page ?? 1;
		$skip = ($page - 1) * $limit;
		$amount = Payroll::where('id_employee', auth()->user()->employee->id)
							->count();

		$payrolls = Payroll::where('id_employee', auth()->user()->employee->id)
								->take($limit)
								->skip($skip)
								->orderBy('created_at', 'desc')
								->get();
		$amountPage = ceil($amount / $limit);
		$startPage = $page - 1;
		$startPage = $startPage >= 1 ? $startPage : 1;
		$endPage = $page + 1;
		$endPage = $endPage <= $amountPage ? $endPage : $amountPage;

		return view('payroll.index_staff', [
			'title'			=> 'Slip Gaji',
			'amountPage'	=> $amountPage,
			'activePage'	=> $page,
			'startPage'		=> $startPage,
			'endPage'		=> $endPage,
			'payrolls'		=> $payrolls,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Slip Gaji',
					'link'	=> route('payroll')
				]
			]
		]);
	}


	public function create(Request $request)
	{
		$action = $request->action ?? 'choose-period-and-employees';

		if($action == 'choose-period-and-employees') {
			return $this->choosePeriodAndEmployeeForPayroll($request);
		}

		if($action == 'enter-nominal') {
			return $this->enterNominalPayroll($request);
		}

		if($action == 'review') {
			return $this->reviewPayroll($request);
		}

		if($action == 'approval') {
			return $this->approvePayroll($request);
		}

		abort(404);
	}


	private function choosePeriodAndEmployeeForPayroll(Request $request)
	{
		return view('payroll.choose_period_and_employees', [
			'title'			=> 'Membuat Penggajian',
			'subtitle'		=> 'Pilih periode dan karyawan',
			'step_wizard'	=> 'step-wizard-1',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Penggajian',
					'link'	=> route('payroll')
				],
				[
					'title'	=> 'Membuat Penggajian',
					'link'	=> route('payroll.create')
				]
			]
		]);
	}


	public function xhrChoosePeriodAndEmployeeForPayroll(Request $request)
	{
		$request->validate([
			'period_start'	=> 'required',
			'period_end'	=> 'required',
			'id_employees'	=> 'required',
			'id_employees.*'=> 'required|numeric'
		]);

		try {
			$result = Payroll::processingPeriodAndEmployees($request);

			return \Setting::successResponse([
				'message'	=> 'Lanjut ke proses berikut nya',
				'route'		=> $result['route'],
			]);

		} catch (\Exception $e) {
			return \Setting::errorResponse($e);
		}
	}


	private function enterNominalPayroll(Request $request)
	{
		$employeeIDs 	= explode(',', $request->employee_list);
		$employees 		= \App\Models\Employee::whereIn('id', $employeeIDs)->get();

		return view('payroll.enter_nominal', [
			'title'			=> 'Membuat Penggajian',
			'subtitle'		=> 'Menentukan Nominal',
			'step_wizard'	=> 'step-wizard-3',
			'employees'		=> $employees,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Penggajian',
					'link'	=> route('payroll')
				],
				[
					'title'	=> 'Membuat Penggajian',
					'link'	=> route('payroll.create')
				]
			]
		]);
	}


	public function xhrEnterNominalPayroll(Request $request)
	{
		try {
			$result = Payroll::processingNominal($request);

			return \Setting::successResponse([
				'message'	=> 'Lanjut ke proses berikut nya',
				'route'		=> $result['route'],
			]);
		} catch (\Exception $e) {
			return \Setting::errorResponse($e);
		}

	}


	private function approvePayroll(Request $request)
	{
		$data = \File::get(\Setting::temps($request->temp));
		$data = unserialize($data);
		$totalEmployee = count($data);
		$totalPayroll = 0;
		foreach($data as $employee)
		{
			$totalPayroll += (int) $employee['total_salary'];
		}

		return view('payroll.approval', [
			'title'			=> 'Membuat Penggajian',
			'subtitle'		=> 'Penyetujuan',
			'step_wizard'	=> 'step-wizard-7',
			'total_employee' => $totalEmployee,
			'total_salary'	=> $totalPayroll,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Penggajian',
					'link'	=> route('payroll')
				],
				[
					'title'	=> 'Membuat Penggajian',
					'link'	=> route('payroll.create')
				]
			]
		]);
	}


	public function xhrApprovePayroll(Request $request)
	{
		// DB::beginTransaction();

		try {
			Payroll::processingApprove($request);
			// DB::commit();

			return \Setting::successResponse([
				'route'		=> route('payroll'),
			]);
		} catch (\Exception $e) {
			// DB::rollback();

			return \Setting::errorResponse($e);
		}

	}


	public function detail(Payroll $payroll)
	{
		if(auth()->user()->isEmployee()) {
			if(auth()->user()->employee->id != $payroll->id_employee) {
				abort(404);
			}
		}
		
		return view('payroll.detail', [
			'title'			=> 'Detail Penggajian',
			'payroll'		=> $payroll,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Penggajian',
					'link'	=> route('payroll')
				],
				[
					'title'	=> 'Detail Penggajian',
					'link'	=> route('payroll.detail', $payroll->id)
				]
			]
		]);
	}


	public function destroy(Payroll $payroll)
	{
		DB::beginTransaction();

		try {
			$payroll->deletePayroll();
			DB::commit();

			return \Setting::deleteResponse();
		} catch (\Exception $e) {
			DB::rollback();

			return \Setting::errorResponse($e);
		}
	}


	public function slip(Payroll $payroll)
	{
		return $payroll->downloadSlip();
	}

	public function send(Payroll $payroll)
	{
		try {
			$payroll->update([
				'send_status'	=> 'Menunggu',
				'send_schedule' => now()->addMinutes(2),
			]);
				
			return \Res::success([
				'message' => 'Akan segera dikirim ulang'
			]);

		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
