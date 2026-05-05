<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DataTables;

class Position extends Model
{
	use SoftDeletes;

    protected $fillable = [ 'position_name', 'id_department', 'job_description', 'performance_goals', 'competence', 'approver_1', 'approver_2', 'is_must_attend' ];


    /**
     * 	Relationship
     * */
    public function department()
    {
    	return $this->belongsTo('App\Models\Department', 'id_department');
    }

    public function departmentName()
    {
    	return $this->department ? $this->department->department_name : '-';
    }

    public function employees()
    {
    	return $this->hasMany(Employee::class, 'id_position')->where('status', Employee::STATUS_ACTIVE);
    }

    public function approver1Position()
    {
    	return $this->belongsTo('App\Models\Position', 'approver_1');
    }

    public function approver2Position()
    {
    	return $this->belongsTo('App\Models\Position', 'approver_2');
    }




    /**
     * 	CRUD methods
     * */
    public static function createPosition(array $request)
	{
		$position = self::create($request);

		return $position;
	}

	public function updatePosition(array $request)
	{
		$this->update($request);

		return $this;
	}

	public function deletePosition()
	{
		return $this->delete();
	}


	/**
	 * 	Static methods
	 * */
	public static function dataTable($request)
	{
		$data = self::select([ 'positions.*' ])
					->with([ 'department' ])
					->leftJoin('departments', 'positions.id_department', '=', 'departments.id');

		if(!empty($request->id_department)) {
			if($request->id_department != 'all') {
				$data = $data->where('id_department', $request->id_department);
			}
		}

		return DataTables::of($data)
			->editColumn('department.department_name', function($data){
				return $data->departmentName();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('admin.position.detail', $data->id).'" title="Detail Jabatan">
							<i class="mdi mdi-magnify"></i> Detail
						</a>';

				if(UserPermission::check('position', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('admin.position.edit', $data->id).'" title="Edit Jabatan">
							<i class="mdi mdi-pencil"></i> Edit
						</a>';
				}

				if(UserPermission::check('position', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('admin.position.destroy', $data->id).'" title="Hapus Jabatan">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!UserPermission::check('position', 'u') && !UserPermission::check('position', 'd')) {
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


	/**
	 * 	Helper methods
	 * */
	public function jobDescriptionHtml()
	{
		if(empty($this->job_description)) return '<p align="center"> Belum memiliki deskripsi pekerjaan </p>';

		$jobDescriptions = explode("\n", $this->job_description);
		$html = '<ol type="1">';
		foreach($jobDescriptions as $job) {
			$html .= "<li>". $job ."</li>";
		}
		$html .= '</ol>';

		return $html;
	}

	public function isMustAttend()
	{
		return $this->is_must_attend == 'Ya';
	}

	public function approver1PositionName()
    {
    	return $this->approver1Position->position_name ?? '-';
    }

    public function approver2PositionName()
    {
    	return $this->approver2Position->position_name ?? '-';
    }
}
