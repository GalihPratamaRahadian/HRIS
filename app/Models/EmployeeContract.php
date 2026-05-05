<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DataTables;

class EmployeeContract extends Model
{
	protected $fillable = [ 'id_employee', 'start_date', 'end_date' ];


	/**
	 * 	Relationship
	 * */
	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee');
	}

	public function employeeName()
	{
		return $this->employee ? $this->employee->employee_name : '-';
	}


	public function isValid()
	{
		return date('Y-m-d') >= $this->start_date && date('Y-m-d') <= $this->end_date;
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createEmployeeContract($request)
	{
		$contract = self::create([
			'id_employee'	=> $request->id_employee,
			'start_date'	=> $request->start_date,
			'end_date'		=> $request->end_date,
		]);

		return $contract;
	}


	public function updateEmployeeContract($request)
	{
		$this->update([
			'id_employee'	=> $request->id_employee,
			'start_date'	=> $request->start_date,
			'end_date'		=> $request->end_date,
		]);

		return $this;
	}


	public function deleteEmployeeContract()
	{
		return $this->delete();
	}


	/**
	 * 	Helper methods
	 * */
	public function contractDateText($format = 'd M Y', $splitter = '-')
	{
		$time = function($date) use ($format) {
			return date($format, strtotime($date));
		};

		return $time($this->start_date).' '.$splitter.' '.$time($this->end_date);
	}

	public function startDateText($format = 'd M Y')
	{
		return date($format, strtotime($this->start_date));
	}

	public function endDateText($format = 'd M Y')
	{
		return date($format, strtotime($this->end_date));
	}



	/**
	 * 	Static methods
	 * */
	public static function dt()
	{
		$data = self::select([ 'employee_contracts.*' ])
					->has('employee')
					->with([ 'employee' ])
					->join('employees', 'employees.id', '=', 'employee_contracts.id_employee');

		return DataTables::of($data)
			->addColumn('employee.employee_name', function($data){
				return '<a href="'. route('employee.detail', $data->id_employee) .'">'. $data->employeeName() .'</a>';
			})
			->addColumn('employee.job_status', function($data){
				return $data->employee->jobStatusText();
			})
			->editColumn('start_date', function($data){
				return $data->startDateText();
			})
			->editColumn('end_date', function($data){
				return $data->endDateText();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">';

				if(UserPermission::check('employee_contract', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('employee_contract.edit', $data->id).'" title="Edit Kontrak">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(UserPermission::check('employee_contract', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('employee_contract.destroy', $data->id).'" title="Hapus Kontrak">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!UserPermission::check('employee_contract', 'u') && !UserPermission::check('employee_contract', 'd')) {
					$button .= '
						<a class="dropdown-item" href="javascript:void(0);">
							Tidak Ada Aksi
						</a>';
				}

				$button .= '
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'employee.employee_name', 'action' ])
			->make(true);
	}
}
