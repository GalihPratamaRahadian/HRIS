<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingEmployee extends Model
{
	protected $fillable = [ 'id_employee' ];


	/**
	 * 	Relationship methods
	 * */
	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee');
	}



	/**
	 * 	CRUD methods
	 * */
	public static function createTrackingEmployee(array $request)
	{
		return self::create($request);
	}

	public function deleteTrackingEmployee()
	{
		return $this->delete();
	}


	/**
	 * 	Helper methods
	 * */
	public function employeeName()
	{
		return $this->employee->employee_name ?? '-';
	}


	/**
	 * 	Static methods
	 * */
	public static function dataTable($request)
	{
		$data = self::select([ 'tracking_employees.*' ])
					->leftJoin('employees', 'tracking_employees.id_employee', '=', 'employees.id');

		return \DataTables::eloquent($data)
			->editColumn('employees.employee_name', function($data){
				return "<a src='".route('employee.detail', $data->id_employee)."'>". $data->employeeName() ."</a>";
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">';

				if(UserPermission::check('tracking_employee', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('admin.tracking_employee.destroy', $data->id).'" title="Hapus">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!UserPermission::check('tracking_employee', 'd')) {
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
			->rawColumns([ 'employees.employee_name', 'action' ])
			->make(true);
	}
}
