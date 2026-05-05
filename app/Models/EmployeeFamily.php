<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeFamily extends Model
{
	protected $fillable = [ 'id_employee', 'name', 'relationship_status', 'place_of_birth', 'date_of_birth' ];


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
	public static function createEmployeeFamily(array $request)
	{
		return self::create($request);
	}

	public function updateEmployeeFamily(array $request)
	{
		return $this->update($request);
	}

	public function deleteEmployeeFamily()
	{
		return $this->delete();
	}


	/**
	 * 	Helper methods
	 * */
	public function dateOfBirthText($format = 'd M Y')
	{
		return date($format, strtotime($this->date_of_birth));
	}

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
						<a class="dropdown-item" href="'.route('employee_family.edit', [$data->id_employee, $data->id]).'" title="Edit Keluarga">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('employee_family.destroy', [$data->id_employee, $data->id]).'" title="Hapus Keluarga">
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
