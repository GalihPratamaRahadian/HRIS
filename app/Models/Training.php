<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
	protected $fillable = [ 'title', 'trainer_name', 'start_date', 'end_date', 'is_published', 'id_department', 'id_position', 'id_employee_group' ];
	protected $dates = [ 'start_date', 'end_date', 'created_at', 'updated_at' ];


	/**
	 * 	Relationship methods
	 * */
	public function trainingMaterials()
	{
		return $this->hasMany('App\Models\TrainingMaterial', 'id_training');
	}

	public function trainingParticipants()
	{
		return $this->hasMany('App\Models\TrainingParticipant', 'id_training');
	}

	public function department()
	{
		return $this->belongsTo('App\Models\Department', 'id_department');
	}

	public function position()
	{
		return $this->belongsTo('App\Models\Position', 'id_position');
	}

	public function employeeGroup()
	{
		return $this->belongsTo('App\Models\EmployeeGroup', 'id_employee_group');
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createTraining($request)
	{
		$training = self::create([
			'title'			=> $request->title,
			'trainer_name'	=> $request->trainer_name,
			'start_date'	=> $request->start_date,
			'end_date'		=> $request->end_date,
			'is_published'	=> $request->is_published,
			'id_department'	=> $request->id_department != 'all'? $request->id_department : null,
			'id_position'	=> $request->id_position != 'all'? $request->id_position : null,
			'id_employee_group'	=> $request->id_employee_group != 'all'? $request->id_employee_group : null,
		]);
		$training->saveTrainingMaterials($request);

		return $training;
	}

	public function updateTraining($request)
	{
		$this->update([
			'title'			=> $request->title,
			'trainer_name'	=> $request->trainer_name,
			'start_date'	=> $request->start_date,
			'end_date'		=> $request->end_date,
			'is_published'	=> $request->is_published,
			'id_department'	=> $request->id_department != 'all'? $request->id_department : null,
			'id_position'	=> $request->id_position != 'all'? $request->id_position : null,
			'id_employee_group'	=> $request->id_employee_group != 'all'? $request->id_employee_group : null,
		]);

		return $this;
	}

	public function deleteTraining()
	{
		// $this->removeVideo();
		return $this->delete();
	}



	/**
	 * 	Helper methods
	 * */
	public function isPublished()
	{
		return strtolower($this->is_published) == 'ya';
	}

	public function isPublishedHtml()
	{
		return $this->isPublished() ? '<span class="text-success"> Terpublikasi </span>' : '<span class="text-danger"> Tidak Dipublikasi </span>';
	}

    public function checkIsPublished()
    {
        if (optional($this->end_date)->lessThan(Carbon::today())) {
            return '<span class="text-danger"> Tidak Dipublikasi </span>';
        }

        return '<span class="text-success"> Terpublikasi </span>';
    }

	public function departmentName()
	{
		return $this->department->department_name ?? 'Semua Departemen';
	}

	public function positionName()
	{
		return $this->position->position_name ?? 'Semua Jabatan';
	}

	public function employeeGroupName()
	{
		return $this->employeeGroup->group_name ?? 'Semua Grup Karyawan';
	}

	public function saveTrainingMaterials($request, $employeeIds)
	{
		if(!empty($request->upload_file_material)) {
			$fileMaterials = $request->upload_file_material;
			$titleMaterials = $request->title_material;
			$iter = 0;
			foreach($fileMaterials as $fileMaterial) {
				$filename = date('ymdhis_').rand(100,999).'_'.$fileMaterial->getClientOriginalName();
				$fileMaterial->move(storage_path('app/public/training_material'), $filename);
                foreach ($employeeIds as $employeeId) {
                    TrainingMaterial::create([
                        'id_training'   => $this->id,
                        'id_employee'   => $employeeId,
                        'title'         => $titleMaterials[$iter++],
                        'material_type' => 'File Upload',
                        'file_material' => $filename
                    ]);
                }
			}
		}

		if(!empty($request->upload_file_video)) {
			$fileVideos = $request->upload_file_video;
			$titleVideos = $request->title_video;
			$iter = 0;
			foreach($fileVideos as $fileVideo) {
				$filename = date('ymdhis_').rand(100,999).'_'.$fileVideo->getClientOriginalName();
				$fileVideo->move(storage_path('app/public/training_material'), $filename);
                foreach ($employeeIds as $employeeId) {
                    TrainingMaterial::create([
                        'id_training'   => $this->id,
                        'id_employee'   => $employeeId,
                        'title'         => $titleVideos[$iter++],
                        'material_type' => 'File Video',
                        'file_video'    => $filename
                    ]);
                }
			}
		}

		if(!empty($request->link_youtube)) {
			$linkYoutubes = $request->link_youtube;
			$titleYoutubes = $request->title_youtube;
			$iter = 0;
			foreach($linkYoutubes as $linkYoutube) {
				foreach ($employeeIds as $employeeId) {
                    TrainingMaterial::create([
                        'id_training'   => $this->id,
                        'id_employee'   => $employeeId,
                        'title'         => $titleYoutubes[$iter++],
                        'material_type' => 'Link Youtube',
                        'link_youtube'  => $linkYoutube
                    ]);
                }
			}
		}

		$this->load('trainingMaterials');
		return $this;
	}

    public static function checkPublishedEndDate()
    {
        return self::whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->update([
                'is_published' => 'Tidak',
            ]);
    }

	public function isEmployeeAllorForTraining($employee)
	{
		$isAllow = true;

		if($this->id_department) {
			if($employee->id_department != $this->id_department) $isAllow = false;
		}

		if($this->id_position) {
			if($employee->id_position != $this->id_position) $isAllow = false;
		}

		if($this->id_employee_group) {
			if($employee->id_employee_group != $this->id_employee_group) $isAllow = false;
		}

		return $isAllow;
	}



	/**
	 * 	Static
	 * */
	public static function dataTable($request)
	{
		$data = self::select([ 'trainings.*' ])
					->with([ 'department', 'position', 'employeeGroup' ])
					->leftJoin('departments', 'trainings.id_department', '=', 'departments.id')
					->leftJoin('positions', 'trainings.id_position', '=', 'positions.id')
					->leftJoin('employee_groups', 'trainings.id_employee_group', '=', 'employee_groups.id');

		if(auth()->user()->isEmployee()) {
			$employee = employee();
			$data->where(function($query) use($employee) {
				$query->where('trainings.id_department', $employee->id_department)
					  ->orWhere('trainings.id_department', null);
			})->where(function($query) use($employee){
				$query->where('trainings.id_employee_group', $employee->id_employee_group)
					  ->orWhere('trainings.id_employee_group', null);
			})->where('is_published', 'Ya');
		}

		return \DataTables::eloquent($data)
			->addColumn('title', function($data){
				$result = $data->title;
				$added = '';

				if($department = $data->department) {
					$added .= '<span class="badge badge-primary mr-1 mb-1"> '. $data->departmentName() .' </span>';
				}

				if($position = $data->position) {
					$added .= '<span class="badge badge-success mr-1 mb-1"> '. $data->positionName() .' </span>';
				}

				if($employeeGroup = $data->employeeGroup) {
					$added .= '<span class="badge badge-info mr-1 mb-1"> '. $data->employeeGroupName() .' </span>';
				}

				if(!empty($added)) {
					$result .= '<br>'.$added;
				}

				return $result;
			})
			->editColumn('created_at', function($data){
				return $data->created_at->format('d M Y H:i');
			})
			->editColumn('start_date', function($data){
				return $data->start_date->format('d M Y');
			})
			->editColumn('end_date', function($data){
				return $data->end_date->format('d M Y');
			})
			->editColumn('is_published', function($data){
				return $data->checkIsPublished();
			})
			->addColumn('admin_action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('admin.training.detail', $data->id).'" title="Detail Training">
							<i class="mdi mdi-magnify"></i> Detail
						</a>';

				if(UserPermission::check('training', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('admin.training.edit', $data->id).'" title="Edit Training">
							<i class="mdi mdi-pencil"></i> Edit
						</a>';
				}

				if(UserPermission::check('training', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('admin.training.destroy', $data->id).'" title="Hapus Training">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				$button .= '
					</div>
				</div>';

				return $button;
			})
			->addColumn('employee_action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('employee.training.learn', $data->id).'" title="Ikuti Training">
							<i class="mdi mdi-book-open-page-variant"></i> Ikuti Training
						</a>
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'title', 'is_published', 'admin_action', 'employee_action' ])
			->make(true);
	}
}
