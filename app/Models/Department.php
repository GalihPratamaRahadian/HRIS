<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DataTables;

class Department extends Model
{
	use SoftDeletes;

	protected $fillable = [ 'department_name' ];


	/**
	 * 	Relationship methods
	 * */
	public function activeEmployees()
	{
		return $this->hasMany('App\Models\Employee', 'id_department')
					->where('status', Employee::STATUS_ACTIVE)
					->orderBy('employee_name')
					->with([ 'department', 'position' ]);
	}



	/**
	 * 	CRUD methods
	 * */
	public static function createDepartment(array $request)
	{
		$department = self::create($request);
		return $department;
	}

	public function updateDepartment(array $request)
	{
		$this->update($request);
		return $this;
	}


	public function deleteDepartment()
	{
		return $this->delete();
	}



	/**
	 * 	Static methods
	 * */
	public static function dataTable($request)
	{
		$data = self::all();

		return DataTables::of($data)
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('admin.department.detail', $data->id).'" title="Detail Departemen">
							<i class="mdi mdi-magnify"></i> Detail 
						</a>';

				if(UserPermission::check('department', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('admin.department.edit', $data->id).'" title="Edit Departemen">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(UserPermission::check('department', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('admin.department.destroy', $data->id).'" title="Hapus Departemen">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!UserPermission::check('department', 'u') && !UserPermission::check('department', 'd')) {
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
			->rawColumns([ 'action' ])
			->make(true);
	}
}
