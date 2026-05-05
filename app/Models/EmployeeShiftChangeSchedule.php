<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeShiftChangeSchedule extends Model
{
	protected $fillable = [ 'id_employee', 'id_shift', 'date', 'change_at' ];


	/**
	 * 	Relationship
	 * */
	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee');
	}

	public function employeeName()
	{
		return $this->employee->employee_name ?? '-';
	}

	public function shift()
	{
		return $this->belongsTo('App\Models\Shift', 'id_shift');
	}

	public function shiftName()
	{
		return $this->shift->shift_name ?? '-';
	}



	/**
	 * 	Static methods
	 * */
	public static function dt()
	{
		$data = self::select([ 'employee_shift_change_schedules.*' ])
					->has('employee')
					->has('shift')
					->with([ 'employee', 'shift' ])
					->join('employees', 'employee_shift_change_schedules.id_employee', '=', 'employees.id')
					->join('shifts', 'employee_shift_change_schedules.id_shift', '=', 'shifts.id');

		return \DataTables::eloquent($data)
			->editColumn('employee.employee_name', function($data){
				return '<a href="'. route('employee.detail', $data->id_employee) .'">'. $data->employeeName() .'</a>';
			})
			->editColumn('shift.shift_name', function($data){
				return $data->shiftName();
			})
			->editColumn('change_at', function($data){
				return $data->changeAtText();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">';

				if(UserPermission::check('employee_shift_change_schedule', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('employee_shift_change_schedule.edit', $data->id).'" title="Edit Jadwal Perubahan Jam Kerja">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(UserPermission::check('employee_shift_change_schedule', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('employee_shift_change_schedule.destroy', $data->id).'" title="Hapus Jadwal Perubahan Jam Kerja">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!UserPermission::check('employee_shift_change_schedule', 'u') && !UserPermission::check('employee_shift_change_schedule', 'd')) {
					$button .= '
						<a class="dropdown-item" href="javascript:void(0);">
							Tidak Ada Aksi
						</a>';
				}

				return $button;
			})
			->rawColumns([ 'employee.employee_name', 'shift.shift_name', 'action' ])
			->make(true);
	}

	public static function changeEmployeeShiftSchedule()
	{
		$schedules = self::where('change_at', '<=', date('Y-m-d H:i:s'))
						 ->has('employee')
						 ->has('shift')
						 ->get();

		foreach($schedules as $schedule) {
			$schedule->changeShift();
		}

		return true;
	}



	/**
	 * 	CRUD methods
	 * */
	public static function createShiftChangeSchedule($request)
	{
		$idEmployees = $request->id_employees;

		foreach($idEmployees as $idEmployee)
		{
			try {
				$time = $request->time;
				if(empty($time)) $time = '00:00';
				$time .= ':00';
				$shiftChange = self::create([
					'id_employee'	=> $idEmployee,
					'id_shift'		=> $request->id_shift,
					'date'			=> $request->date,
					'change_at'		=> $request->date.' '.$time,
				]);
			} catch (\Exception $e) {
				$shiftChange = self::create([
					'id_employee'	=> $idEmployee,
					'id_shift'		=> $request->id_shift,
					'date'			=> $request->date,
					'change_at'		=> $request->date.' 00:00:00',
				]);
			}
		}

		self::changeEmployeeShiftSchedule();
	}

	public function updateShiftChangeSchedule($request)
	{
		try {
			$time = $request->time;
			if(empty($time)) $time = '00:00';
			$time .= ':00';
			$this->update([
				'id_employee'	=> $request->id_employee,
				'id_shift'		=> $request->id_shift,
				'date'			=> $request->date,
				'change_at'		=> $request->date.' '.$time,
			]);
		} catch (\Exception $e) {
			$this->update([
				'id_employee'	=> $request->id_employee,
				'id_shift'		=> $request->id_shift,
				'date'			=> $request->date,
				'change_at'		=> $request->date.' 00:00:00',
			]);
		}

		self::changeEmployeeShiftSchedule();

		return $this;
	}

	public function deleteShiftChangeSchedule()
	{
		return $this->delete();
	}

	public function changeShift()
	{
		if($this->employee && $this->shift) {
			if($this->employee->isShiftTypeRoutine()) {
				$this->employee->update([
					'id_shift'	=> $this->id_shift
				]);
			}
		}

		$this->delete();
	}



	/**
	 * 	Helper methods
	 * */
	public function changeAtText($format = 'd M Y H:i')
	{
		return date($format, strtotime($this->change_at));
	}
}
