<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyShiftResume extends Model
{
	protected $fillable = [ 'id_employee', 'type', 'date', 'clock_start_at', 'clock_end_at', 'late_tolerance' ];
	protected $dates = [ 'clock_start_at', 'clock_end_at', 'created_at', 'updated_at' ];


	/**
	 * 	Relationship methods
	 * */
	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee')->withTrashed();
	}


	/**
	 * 	Helper methods
	 * */
	public function employeeName()
	{
		return $this->employee->employee_name ?? '-';
	}

	public function employeeNumber()
	{
		return $this->employee->employee_number ?? '-';
	}

	public function departmentName()
	{
		return $this->employee->departmentName() ?? '-';
	}

	public function dateFormatted($format = 'd M Y')
	{
		return date($format, strtotime($this->date));
	}


	/**
	 * 	Static methods
	 * */
	public static function createTodayResume()
	{
		$employees = Employee::with('shift')
							 ->has('shift')
							 ->where('status', Employee::STATUS_ACTIVE)
							 ->get();
		foreach($employees as $employee)
		{
			$shift = $employee->todayShift();
			$type = 'Normal';
			$clockStart = null;
			$clockEnd = null;
			$lateTolerance = 0;

			if($employee->isOffday()) $type = 'Libur';

			if($type == 'Normal') {
				$clockStart = $shift->clockStartWithDate();
				$clockEnd = $shift->clockEndWithDate();
				$lateTolerance = $shift->late_tolerance;
			}

			self::create([
				'id_employee'	=> $employee->id,
				'type'			=> $type,
				'date'			=> date('Y-m-d'),
				'clock_start_at'=> $clockStart,
				'clock_end_at'	=> $clockEnd,
				'late_tolerance' => $lateTolerance,
			]);
		}
	}

	public static function dataTable($request)
	{
		$data = self::select([ 'daily_shift_resumes.*' ])
					->has('employee')
					->with('employee.department')
					->leftJoin('employees', 'daily_shift_resumes.id_employee', '=', 'employees.id')
					->leftJoin('departments', 'employees.id_department', '=', 'departments.id');

		if($request->id_department) {
			$data = $data->where('employees.id_department', $request->id_department);
		}

		if($request->start_date) {
			$data = $data->where('daily_shift_resumes.date', '>=', $request->start_date);
		}

		if($request->end_date) {
			$data = $data->where('daily_shift_resumes.date', '<=', $request->end_date);
		}

		return \DataTables::eloquent($data)
			->editColumn('employees.employee_name', function($data){
				return $data->employeeName();
			})
			->addColumn('departments.department_name', function($data){
				return $data->departmentName();
			})
			->editColumn('date', function($data){
				return $data->dateFormatted();
			})
			->editColumn('clock_start_at', function($data){
				return $data->clock_start_at->format('d M Y H:i');
			})
			->editColumn('clock_end_at', function($data){
				return $data->clock_end_at->format('d M Y H:i');
			})
			->rawColumns([ 'shift.shift_name', 'status', 'action' ])
			->make(true);
	}
}
