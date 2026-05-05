<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeGroup extends Model
{
	protected $fillable = [ 'group_name' ];


	/**
	 * 	CRUD methods
	 * */
	public static function createEmployeeGroup(array $request)
	{
		return self::create($request);
	}

	public function updateEmployeeGroup(array $request)
	{
		$this->update($request);
		return $this;
	}

	public function deleteEmployeeGroup()
	{
		return $this->delete();
	}



	/**
	 * 	Static
	 * */
	public static function dataTable($request)
	{
		$data = self::select([ 'employee_groups.*' ]);

		return \DataTables::eloquent($data)
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">';

				if(UserPermission::check('employee_group', 'u')) {
					$button .= '
						<button class="dropdown-item edit" data-get-href="'.route('admin.employee_group.get', $data->id).'" data-edit-href="'.route('admin.employee_group.update', $data->id).'" title="Edit Grup Karyawan">
							<i class="mdi mdi-pencil"></i> Edit 
						</button>';
				}

				if(UserPermission::check('employee_group', 'd')) {
					$button .= '
						<button class="dropdown-item delete" data-href="'.route('admin.employee_group.destroy', $data->id).'" title="Hapus Grup Karyawan">
							<i class="mdi mdi-trash-can"></i> Hapus
						</button>';
				}

				if(!UserPermission::check('employee_group', 'u') && !UserPermission::check('employee_group', 'd')) {
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
