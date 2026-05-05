<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\EmployeeSalary;
use App\Models\EmployeeShiftChangeSchedule;
use App\Models\EmployeeLeaveQuota;
use App\Models\EmployeeFamily;
use App\Models\EmployeeTraining;
use App\Models\EmployeeEducation;
use App\Models\UnroutineShift;
use App\MyClass\Validations;
use DB;

class EmployeeController extends Controller
{


	/**
	*	@see Employee
	*/
	public function employeeIndex(Request $request)
	{
		if($request->ajax()) {
			return Employee::dt($request);
		}

		return view('admin.employee.index', [
			'title'			=> 'Karyawan',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Karyawan',
					'link'	=> route('employee')
				],
			]
		]);
	}

	public function employeeCreate()
	{
		return view('admin.employee.create', [
			'title'			=> 'Tambah Karyawan',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Karyawan',
					'link'	=> route('employee')
				],
				[
					'title'	=> 'Tambah',
					'link'	=> route('employee.create')
				],
			]
		]);
	}

	public function employeeStore(Request $request)
	{
		Validations::validateEmployee($request);
		Validations::validateEmployeePhoto($request);
		DB::beginTransaction();

		try {
			if(Employee::isEmployeeReachLimit()) {
				return \Res::invalid([
					'message'	=> 'Jumlah karyawan telah mencapai batas'
				]);
			}

			Employee::createEmployeeFromAdmin($request);
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function employeeEdit(Employee $employee)
	{
		return view('admin.employee.edit', [
			'title'			=> 'Edit Karyawan',
			'employee'		=> $employee,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Karyawan',
					'link'	=> route('employee')
				],
				[
					'title'	=> 'Edit',
					'link'	=> route('employee.edit', $employee->id)
				],
			]
		]);
	}

	public function employeeDetail(Employee $employee)
	{
		return view('admin.employee.detail', [
			'title'			=> 'Detail Karyawan',
			'employee'		=> $employee,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Karyawan',
					'link'	=> route('employee')
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('employee.detail', $employee->id)
				],
			]
		]);
	}

	public function employeeOptionsGet(Request $request)
	{
		$results = [];
		$employees = Employee::where('status', Employee::STATUS_ACTIVE)
							 ->where('employee_name', 'like', '%'.$request->search.'%');

		if(!empty($request->except)) {
			$employees = $employees->whereNotIn('id', $request->except);
		}

		$employees = $employees->get();
		
		foreach($employees as $employee) {
			$results[] = [
				'id'	=> $employee->id,
				'text'	=> $employee->employee_name
			];
		}

		return response()->json([
			'results' => $results,
		]);
	}

	public function employeeEditUser(Employee $employee)
	{
		$employee->createUserIfDoesntExist();
		
		return view('admin.employee.edit_user', [
			'title'			=> 'Edit Pengguna',
			'employee'		=> $employee,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Karyawan',
					'link'	=> route('employee')
				],
				[
					'title'	=> 'Edit Pengguna',
					'link'	=> route('employee.edit_user', $employee->id)
				],
			]
		]);
	}

	public function employeeUpdate(Request $request, Employee $employee)
	{
		Validations::validateEmployee($request);
		DB::beginTransaction();

		try {
			$employee->updateEmployee($request);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function employeeSetActive(Employee $employee)
	{
		DB::beginTransaction();

		try {
			$employee->setActive();
			DB::commit();

			return \Res::success([
				'message'	=> 'Berhasil diaktifkan'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function employeeSetInactive(Employee $employee)
	{
		DB::beginTransaction();

		try {
			$employee->setInactive();
			DB::commit();

			return \Res::success([
				'message'	=> 'Berhasil dinonaktifkan'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function employeeUpdateUser(Request $request, Employee $employee)
	{
		DB::beginTransaction();

		try {
			$employee->user->update([
				'username'	=> $request->username,
			]);
			if(!empty($request->username)) {
				$employee->user->update([
					'password'	=> \Hash::make($request->password),
				]);
			}
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function employeeChangePhoto(Employee $employee)
	{
		return view('admin.employee.change_photo', [
			'title'			=> 'Ganti Foto Karyawan',
			'employee'		=> $employee,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Karyawan',
					'link'	=> route('employee')
				],
				[
					'title'	=> 'Detail Karyawan',
					'link'	=> route('employee.detail', $employee->id)
				],
				[
					'title'	=> 'Ganti Foto',
					'link'	=> route('employee.change_photo', $employee->id)
				],
			]
		]);
	}

	public function employeeSavePhoto(Request $request, Employee $employee)
	{
		DB::beginTransaction();

		try {
			$file = $request->file('photo');
			$filename = $employee->employee_name.'_'.rand(1000,9999).'.'.$file->getClientOriginalExtension();
			$path = \Setting::temps('');
			$filepath = \Setting::temps($filename);
			$file->move($path, $filename);
			$base64 = base64_encode(\File::get($filepath));

			$employee->setPhotoFromBase64($base64);
			$employee->pushToFaceTerminalDevice();
			\File::delete($filepath);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function employeeDestroy(Employee $employee)
	{
		DB::beginTransaction();

		try {
			$employee->deleteEmployee();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function employeeCurriculumVitae(Employee $employee)
	{
		try {
			return $employee->downloadCurriculumVitae();
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}

	public function xhrPushEmployeeToFaceTerminal(Employee $employee)
	{
		DB::beginTransaction();

		try {
			if($employee->pushToFaceTerminalDevice()) {
				DB::commit();
				return \Res::success([
					'message'	=> 'Berhasil di push ke device',
				]);
			} else {
				DB::rollback();
				return \Res::invalid([
					'message'	=> 'Gagal',
				]);
			}
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function xhrPushAllEmployeeToFaceTerminal()
	{
		set_time_limit(0);

		try {
			Employee::pushActiveEmployeesToFaceTerminalDevice();

			return \Res::success([
				'message'	=> 'Berhasil di push ke device',
			]);
		} catch (\Exception $e) {

			return \Res::error($e);
		}
	}

	public function employeeExport(Request $request)
	{
		try {
			$path = \App\Models\Employee::exportToExcel($request);

			return response()->download($path)->deleteFileAfterSend();
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	/**
	 * 	@see Employee Contract
	 * */
	public function contractIndex(Request $request)
	{
		if($request->ajax()) {
			return EmployeeContract::dt();
		}

		return view('admin.employee_setting.contract.index', [
			'title'			=> 'Kontrak Karyawan',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Kontrak Karyawan',
					'link'	=> route('employee_contract')
				],
			]
		]);
	}

	public function contractCreate()
	{
		$employees = Employee::where(function($query){
								$query->where('job_status', Employee::JOBSTATUS_KONTRAK)
									  ->orWhere('job_status', Employee::JOBSTATUS_PROBATION);
							 })
							 ->where('status', Employee::STATUS_ACTIVE)
							 ->doesntHave('employeeContract')
							 ->get();

		return view('admin.employee_setting.contract.create', [
			'title'			=> 'Buat Kontrak Karyawan',
			'employees'		=> $employees,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Kontrak Karyawan',
					'link'	=> route('employee_contract')
				],
				[
					'title'	=> 'Buat',
					'link'	=> route('employee_contract.create')
				],
			]
		]);
	}

	public function contractStore(Request $request)
	{
		Validations::validateEmployeeContract($request);
		DB::beginTransaction();

		try {
			$contract = EmployeeContract::where('id_employee', $request->id_employee)->first();
			if($contract) {
				return \Res::invalid([
					'message'	=> 'Karyawan sudah punya kontrak',
					'errors'	=> [
						'id_employee'	=> 'Karyawan sudah punya kontrak'
					]
				]);
			}

			EmployeeContract::createEmployeeContract($request);
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function contractEdit(EmployeeContract $employeeContract)
	{
		$employees = Employee::where(function($query){
								$query->where('job_status', Employee::JOBSTATUS_KONTRAK)
									  ->orWhere('job_status', Employee::JOBSTATUS_PROBATION);
							 })
							 ->where('status', Employee::STATUS_ACTIVE)
							 ->doesntHave('employeeContract')
							 ->get();
		if($employeeContract->employee) {
			$employees[] = $employeeContract->employee;
		}

		return view('admin.employee_setting.contract.edit', [
			'title'				=> 'Edit Kontrak Karyawan',
			'employees'			=> $employees,
			'employeeContract'	=> $employeeContract,
			'breadcrumbs'		=> [
				[
					'title'	=> 'Kontrak Karyawan',
					'link'	=> route('employee_contract')
				],
				[
					'title'	=> 'Edit',
					'link'	=> route('employee_contract.create')
				],
			]
		]);
	}

	public function contractUpdate(Request $request, EmployeeContract $employeeContract)
	{
		Validations::validateEmployeeContract($request);
		DB::beginTransaction();

		try {
			$contract = EmployeeContract::where('id_employee', $request->id_employee)
										->where('id', '!=', $employeeContract->id)->first();
			if($contract) {
				return \Res::invalid([
					'message'	=> 'Karyawan sudah punya kontrak',
					'errors'	=> [
						'id_employee'	=> 'Karyawan sudah punya kontrak'
					]
				]);
			}

			$employeeContract->updateEmployeeContract($request);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function contractDestroy(EmployeeContract $employeeContract)
	{
		DB::beginTransaction();

		try {
			$employeeContract->deleteEmployeeContract();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}




	/**
	*	@see Employee Salary
	*/
	public function salaryIndex(Request $request)
	{
		if($request->ajax()) {
			return EmployeeSalary::dt();
		}

		return view('admin.employee_setting.salary.index', [
			'title'			=> 'Gaji Karyawan',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Gaji Karyawan',
					'link'	=> route('employee_contract')
				],
			]
		]);
	}

	public function salaryCreate()
	{
		$employees = Employee::doesntHave('employeeSalary')
							 ->where('status', Employee::STATUS_ACTIVE)
							 ->get();

		return view('admin.employee_setting.salary.create', [
			'title'			=> 'Buat Gaji Karyawan',
			'employees'		=> $employees,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Gaji Karyawan',
					'link'	=> route('employee_salary')
				],
				[
					'title'	=> 'Buat',
					'link'	=> route('employee_salary.create')
				],
			]
		]);
	}


	public function salaryStore(Request $request)
	{
		Validations::validateEmployeeSalary($request);
		DB::beginTransaction();

		try {
			EmployeeSalary::createEmployeeSalary($request);
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function salaryEdit(EmployeeSalary $employeeSalary)
	{
		return view('admin.employee_setting.salary.edit', [
			'title'				=> 'Edit Gaji Karyawan',
			'employeeSalary'	=> $employeeSalary,
			'breadcrumbs'		=> [
				[
					'title'	=> 'Gaji Karyawan',
					'link'	=> route('employee_salary')
				],
				[
					'title'	=> 'Edit',
					'link'	=> route('employee_salary.create')
				],
			]
		]);
	}


	public function salaryUpdate(Request $request, EmployeeSalary $employeeSalary)
	{
		DB::beginTransaction();

		try {
			$employeeSalary->updateEmployeeSalary($request);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function salaryDestroy(EmployeeSalary $employeeSalary)
	{
		DB::beginTransaction();

		try {
			$employeeSalary->deleteEmployeeSalary();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function salaryDetail(EmployeeSalary $employeeSalary)
	{
		return view('admin.employee_setting.salary.detail', [
			'title'				=> 'Detail Gaji Karyawan',
			'employeeSalary'	=> $employeeSalary,
			'breadcrumbs'		=> [
				[
					'title'	=> 'Gaji Karyawan',
					'link'	=> route('employee_salary')
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('employee_salary.create')
				],
			]
		]);
	}




	/**
	*	@see Shift Change Schedule
	*/
	public function shiftChangeScheduleIndex(Request $request)
	{
		if($request->ajax()) {
			return EmployeeShiftChangeSchedule::dt();
		}

		return view('admin.employee_setting.shift_change.index', [
			'title'				=> 'Jadwal Perubahan Shift',
			'breadcrumbs'		=> [
				[
					'title'	=> 'Jadwal Perubahan Shift',
					'link'	=> route('employee_shift_change_schedule')
				],
			]
		]);
	}

	public function shiftChangeScheduleCreate()
	{
		return view('admin.employee_setting.shift_change.create', [
			'title'				=> 'Buat Jadwal Perubahan Shift',
			'breadcrumbs'		=> [
				[
					'title'	=> 'Jadwal Perubahan Shift',
					'link'	=> route('employee_shift_change_schedule')
				],
				[
					'title'	=> 'Buat',
					'link'	=> route('employee_shift_change_schedule.create')
				],
			]
		]);
	}

	public function shiftChangeScheduleStore(Request $request)
	{
		// Validations::validateEmployeeShiftChangeSchedule($request);
		DB::beginTransaction();

		try {
			if(empty($request->id_employees)) {
				return \Res::invalid([
					'message'	=> 'Harap untuk tambahkan minimal 1 karyawan'
				]);
			}
			
			EmployeeShiftChangeSchedule::createShiftChangeSchedule($request);
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function shiftChangeScheduleEdit(EmployeeShiftChangeSchedule $employeeShiftChangeSchedule)
	{
		return view('admin.employee_setting.shift_change.edit', [
			'title'					=> 'Edit Jadwal Perubahan Shift',
			'shiftChangeSchedule'	=> $employeeShiftChangeSchedule,
			'breadcrumbs'			=> [
				[
					'title'	=> 'Jadwal Perubahan Shift',
					'link'	=> route('employee_shift_change_schedule')
				],
				[
					'title'	=> 'Edit',
					'link'	=> route('employee_shift_change_schedule.create')
				],
			]
		]);
	}

	public function shiftChangeScheduleUpdate(Request $request, EmployeeShiftChangeSchedule $employeeShiftChangeSchedule)
	{
		Validations::validateEmployeeShiftChangeSchedule($request);
		DB::beginTransaction();

		try {
			$employeeShiftChangeSchedule->updateShiftChangeSchedule($request);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function shiftChangeScheduleDestroy(EmployeeShiftChangeSchedule $employeeShiftChangeSchedule)
	{
		DB::beginTransaction();

		try {
			$employeeShiftChangeSchedule->deleteShiftChangeSchedule();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}



	/**
	* 	@see Leave Quota
	*/
	public function leaveQuotaIndex(Request $request)
	{
		if($request->ajax()) {
			return EmployeeLeaveQuota::dt();
		}

		return view('admin.employee_setting.leave_quota.index', [
			'title'				=> 'Jatah Cuti',
			'breadcrumbs'		=> [
				[
					'title'	=> 'Jatah Cuti',
					'link'	=> route('employee_leave_quota')
				],
			]
		]);
	}

	public function leaveQuotaCreate()
	{
		$employees = Employee::where('status', Employee::STATUS_ACTIVE)
							 ->doesntHave('employeeLeaveQuota')
							 ->get();

		return view('admin.employee_setting.leave_quota.create', [
			'title'				=> 'Buat Jatah Cuti',
			'breadcrumbs'		=> [
				[
					'title'	=> 'Jatah Cuti',
					'link'	=> route('employee_leave_quota')
				],
				[
					'title'	=> 'Buat',
					'link'	=> route('employee_leave_quota.create')
				],
			]
		]);
	}

	public function leaveQuotaStore(Request $request)
	{
		Validations::validateEmployeeLeaveQuota($request);
		DB::beginTransaction();

		try {
			$leaveQuota = EmployeeLeaveQuota::where('id_employee', $request->id_employee)->first();
			if($leaveQuota) {
				return \Res::invalid([
					'message'	=> 'Karyawan tersebut telah memiliki jatah cuti',
					'errors'	=> [
						'id_employee'	=> 'Karyawan tersebut telah memiliki jatah cuti',
					]
				]);
			}

			EmployeeLeaveQuota::createEmployeeLeaveQuota($request);
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function leaveQuotaExport(Request $request)
	{
		try {
			$action = $request->action;

			if ($action == 'pdf_stream') {
				return EmployeeLeaveQuota::streamPdfReport($request);
			} elseif ($action == 'pdf_download') {
				return EmployeeLeaveQuota::downloadPdfReport($request);
			} elseif ($action == 'xlsx_download') {
				$path = EmployeeLeaveQuota::downloadExcelReport($request);

				return response()->download($path)->deleteFileAfterSend();
			} else {
				return EmployeeLeaveQuota::streamPdfReport($request);
			}
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}

	public function leaveQuotaEdit(EmployeeLeaveQuota $employeeLeaveQuota)
	{
		return view('admin.employee_setting.leave_quota.edit', [
			'title'			=> 'Edit Jatah Cuti',
			'leaveQuota'	=> $employeeLeaveQuota,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Jatah Cuti',
					'link'	=> route('employee_leave_quota')
				],
				[
					'title'	=> 'Edit',
					'link'	=> route('employee_leave_quota.create')
				],
			]
		]);
	}

	public function leaveQuotaUpdate(Request $request, EmployeeLeaveQuota $employeeLeaveQuota)
	{
		Validations::validateEmployeeLeaveQuota($request);
		DB::beginTransaction();

		try {
			$employeeLeaveQuota->updateEmployeeLeaveQuota($request);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function leaveQuotaDestroy(EmployeeLeaveQuota $employeeLeaveQuota)
	{
		DB::beginTransaction();

		try {
			$employeeLeaveQuota->deleteEmployeeLeaveQuota();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}



	/**
	 * 	Employee Family
	 * */
	public function employeeFamilyIndex(Request $request, Employee $employee)
	{
		if($request->ajax()) {
			return EmployeeFamily::dt($request, $employee);
		}
	}

	public function employeeFamilyCreate(Employee $employee)
	{
		return view('admin.employee.employee_family.create', [
			'title'			=> 'Tambah Keluarga - '.$employee->employee_name,
			'ajaxRoute'		=> route('employee_family.store', $employee->id),
			'breadcrumbs'	=> [
				[
					'title'	=> 'Karyawan',
					'link'	=> route('employee')
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('employee.detail', $employee->id).'?tab=family'
				],
				[
					'title'	=> 'Tambah Keluarga',
					'link'	=> route('employee_family.create', $employee->id),
				]
			]
		]);
	}

	public function employeeFamilyStore(Request $request, Employee $employee)
	{
		DB::beginTransaction();

		try {
			$data = $request->all();
			$data['id_employee'] = $employee->id;
			EmployeeFamily::createEmployeeFamily($data);
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function employeeFamilyEdit(Employee $employee, EmployeeFamily $employeeFamily)
	{
		return view('admin.employee.employee_family.edit', [
			'title'			=> 'Edit Keluarga - '.$employee->employee_name,
			'ajaxRoute'		=> route('employee_family.update', [$employee->id, $employeeFamily->id]),
			'employeeFamily'=> $employeeFamily,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Karyawan',
					'link'	=> route('employee')
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('employee.detail', $employee->id).'?tab=family'
				],
				[
					'title'	=> 'Edit Keluarga',
					'link'	=> route('employee_family.edit', [$employee->id, $employeeFamily->id]),
				]
			]
		]);
	}

	public function employeeFamilyUpdate(Request $request, Employee $employee, EmployeeFamily $employeeFamily)
	{
		DB::beginTransaction();

		try {
			$employeeFamily->updateEmployeeFamily($request->all());
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function employeeFamilyDestroy(Employee $employee, EmployeeFamily $employeeFamily)
	{
		DB::beginTransaction();

		try {
			$employeeFamily->deleteEmployeeFamily();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}



	/**
	 * 	Employee Training
	 * */
	public function employeeTrainingIndex(Request $request, Employee $employee)
	{
		if($request->ajax()) {
			return EmployeeTraining::dataTable($request, $employee);
		}
	}

	public function employeeTrainingCreate(Employee $employee)
	{
		return view('admin.employee.employee_training.create', [
			'title'			=> 'Tambah Pelatihan - '.$employee->employee_name,
			'ajaxRoute'		=> route('employee_training.store', $employee->id),
			'breadcrumbs'	=> [
				[
					'title'	=> 'Karyawan',
					'link'	=> route('employee')
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('employee.detail', $employee->id).'?tab=training'
				],
				[
					'title'	=> 'Tambah Pelatihan',
					'link'	=> route('employee_training.create', $employee->id),
				]
			]
		]);
	}

	public function employeeTrainingStore(Request $request, Employee $employee)
	{
		try {
			EmployeeTraining::createEmployeeTraining($request, $employee);

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function employeeTrainingEdit(Employee $employee, EmployeeTraining $employeeTraining)
	{
		return view('admin.employee.employee_training.edit', [
			'title'			=> 'Edit Pelatihan - '.$employee->employee_name,
			'employeeTraining'=> $employeeTraining,
			'ajaxRoute'		=> route('employee_training.update', [$employee->id, $employeeTraining->id]),
			'breadcrumbs'	=> [
				[
					'title'	=> 'Karyawan',
					'link'	=> route('employee')
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('employee.detail', $employee->id).'?tab=training'
				],
				[
					'title'	=> 'Edit Pelatihan',
					'link'	=> route('employee_training.edit', [$employee->id, $employeeTraining->id]),
				]
			]
		]);
	}

	public function employeeTrainingUpdate(Request $request, Employee $employee, EmployeeTraining $employeeTraining)
	{
		try {
			$employeeTraining->updateEmployeeTraining($request, $employee);

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function employeeTrainingDestroy(Employee $employee, EmployeeTraining $employeeTraining)
	{
		DB::beginTransaction();

		try {
			$employeeTraining->deleteEmployeeTraining();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	/**
	 * 	Employee Education
	 * */
	public function employeeEducationIndex(Request $request, Employee $employee)
	{
		if($request->ajax()) {
			return EmployeeEducation::dt($request, $employee);
		}
	}

	public function employeeEducationCreate(Employee $employee)
	{
		return view('admin.employee.employee_education.create', [
			'title'			=> 'Tambah Pendidikan - '.$employee->employee_name,
			'ajaxRoute'		=> route('employee_education.store', $employee->id),
			'breadcrumbs'	=> [
				[
					'title'	=> 'Karyawan',
					'link'	=> route('employee')
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('employee.detail', $employee->id).'?tab=education'
				],
				[
					'title'	=> 'Tambah Pendidikan',
					'link'	=> route('employee_education.create', $employee->id),
				]
			]
		]);
	}

	public function employeeEducationStore(Request $request, Employee $employee)
	{
		DB::beginTransaction();

		try {
			$data = $request->all();
			$data['id_employee'] = $employee->id;
			EmployeeEducation::createEmployeeEducation($data);
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function employeeEducationEdit(Employee $employee, EmployeeEducation $employeeEducation)
	{
		return view('admin.employee.employee_education.edit', [
			'title'			=> 'Edit Pendidikan - '.$employee->employee_name,
			'employeeEducation'=> $employeeEducation,
			'ajaxRoute'		=> route('employee_education.update', [$employee->id, $employeeEducation->id]),
			'breadcrumbs'	=> [
				[
					'title'	=> 'Karyawan',
					'link'	=> route('employee')
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('employee.detail', $employee->id).'?tab=education'
				],
				[
					'title'	=> 'Edit Pendidikan',
					'link'	=> route('employee_education.edit', [$employee->id, $employeeEducation->id]),
				]
			]
		]);
	}

	public function employeeEducationUpdate(Request $request, Employee $employee, EmployeeEducation $employeeEducation)
	{
		DB::beginTransaction();

		try {
			$employeeEducation->updateEmployeeEducation($request->all());
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function employeeEducationDestroy(Employee $employee, EmployeeEducation $employeeEducation)
	{
		DB::beginTransaction();

		try {
			$employeeEducation->deleteEmployeeEducation();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	/**
	 * 	Unroutine Shift
	 * */
	public function unroutineShiftIndex(Request $request)
	{
		if($request->ajax()) {
			return UnroutineShift::dt($request);
		}

		return view('admin.unroutine_shift.index', [
			'title'			=> 'Shift Harian',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Shift Harian',
					'link'	=> route('unroutine_shift')
				]
			]
		]);
	}

	public function unroutineShiftEmployeeDetail(Request $request, Employee $employee)
	{
		if($request->ajax()) {
			return UnroutineShift::unroutineShiftDt($request, $employee);
		}

		return view('admin.unroutine_shift.employee_detail', [
			'title'			=> 'Shift Harian',
			'employee'		=> $employee,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Shift Harian',
					'link'	=> route('unroutine_shift')
				],
				[
					'title'	=> 'Karyawan ['.$employee->employee_name.']',
					'link'	=> route('unroutine_shift.employee_detail', $employee->id)
				],
			]
		]);
	}

	public function unroutineShiftImport(Request $request, Employee $employee)
	{
		try {
			$result = UnroutineShift::importFromExcel($request, $employee);

			return \Res::success([
				'message'	=> 'Berhasil mengimport '.$result.' data',
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
