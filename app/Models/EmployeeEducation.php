<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
	protected $table = 'employee_educations';
	protected $fillable = [ 'id_employee', 'education_level', 'school_name', 'major_name', 'year_start', 'year_end' ];


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


	/**
	 * 	CRUD methods
	 * */
	public static function createEmployeeEducation(array $request)
	{
		return self::create($request);
	}

	public function updateEmployeeEducation(array $request)
	{
		return $this->update($request);
	}

	public function deleteEmployeeEducation()
	{
		return $this->delete();
	}


	/**
	 * 	Helper methods
	 * */
	public static function dt($request, $employee)
	{
		$data = self::where('id_employee', $employee->id);

		return \DataTables::eloquent($data)
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('employee_education.edit', [$data->id_employee, $data->id]).'" title="Edit Pendidikan">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('employee_education.destroy', [$data->id_employee, $data->id]).'" title="Hapus Pendidikan">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'action' ])
			->make(true);
	}
}
