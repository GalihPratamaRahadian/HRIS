<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarningLetter extends Model
{
	protected $fillable = [ 'id_employee', 'type', 'start_date', 'end_date', 'message' ];


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
	public static function createWarningLetter(array $request)
	{
		$warningLetter = self::create($request);
		return $warningLetter;
	}

	public function updateWarningLetter(array $request)
	{
		$this->update($request);
		return $this;
	}

	public function deleteWarningLetter()
	{
		return $this->delete();
	}


	/**
	 * 	Helper methods
	 * */
	public static function dt()
	{
		$data = self::select([ 'warning_letters.*' ])
					->with([ 'employee' ])
					->has('employee')
					->leftJoin('employees', 'warning_letters.id_employee', '=', 'employees.id');

		if(user()->isEmployee()) {
			$data = $data->where('id_employee', employee()->id)
						 ->where('end_date', '>=', now()->format('Y-m-d'));
		}

		return \DataTables::eloquent($data)
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">';

				if(UserPermission::check('warning_letter', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('warning_letter.edit', $data->id).'" title="Edit Surat Peringatan">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(UserPermission::check('warning_letter', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('warning_letter.destroy', $data->id).'" title="Hapus Surat Peringatan">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!UserPermission::check('warning_letter', 'u') && !UserPermission::check('warning_letter', 'd')) {
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
