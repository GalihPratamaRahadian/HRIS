<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
	use SoftDeletes;

	protected $fillable = [ 'course_title', 'id_department', 'id_position', 'id_employee_group', 'video_source', 'video_link', 'filename', 'is_published', 'deadline', 'pass_requirement' ];


	const PASS_EXAM		= 'pass_exam';
	const PASS_VIDEO	= 'pass_video';


	/**
	 * 	Directory Creation
	 * */
	public static function createDirectories()
	{
		$paths = [
			storage_path('app/public/course'),
			storage_path('app/public/course/video'),
			storage_path('app/public/course/certificate'),
		];
		\Helper::createDirectoryIfNotExists($paths);
	}


	/**
	 * 	Relationship methods
	 * */
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

	public function courseParticipants()
	{
		return $this->hasMany('App\Models\CourseParticipant', 'id_course')
					->with([ 'employee.department' ]);
	}

	public function courseParticipantsPassed()
	{
		return $this->hasMany('App\Models\CourseParticipant', 'id_course')
					->where('have_passed', 'yes');
	}

	public function courseExam()
	{
		return $this->hasOne('App\Models\CourseExam', 'id_course');
	}

	public function courseExamParticipants()
	{
		return $this->hasMany('App\Models\CourseExamParticipant', 'id_course');
	}

	public function courseExamParticipantsPassed()
	{
		return $this->hasMany('App\Models\CourseExamParticipant', 'id_course')
					->where('result', 'passed');
	}

	public function courseComments()
	{
		return $this->hasMany('App\Models\CourseComment', 'id_course')
					->where('id_comment_reply', null);
	}




	/**
	 * 	CRUD methods
	 * */
	public static function createCourse($request)
	{
		$course = self::create([
			'course_title'	=> $request->course_title,
			'id_department'	=> $request->id_department != 'all'? $request->id_department : null,
			'id_employee_group'	=> $request->id_employee_group != 'all'? $request->id_employee_group : null,
			'video_source'	=> $request->video_source,
			'video_link'	=> $request->video_link,
			'is_published'	=> $request->is_published,
			'deadline'		=> $request->deadline ?? null,
			'pass_requirement' => $request->pass_requirement,
		]);
		$course->saveVideo($request);
		self::checkCourseDeadline();

		return $course;
	}

	public function updateCourse($request)
	{
		$this->update([
			'course_title'	=> $request->course_title,
			'id_department'	=> $request->id_department != 'all'? $request->id_department : null,
			'id_employee_group'	=> $request->id_employee_group != 'all'? $request->id_employee_group : null,
			'video_source'	=> $request->video_source,
			'video_link'	=> $request->video_link,
			'is_published'	=> $request->is_published,
			'deadline'		=> $request->deadline ?? null,
			'pass_requirement' => $request->pass_requirement,
		]);
		$this->saveVideo($request);
		$this->updateParticipantPassedStatus();
		self::checkCourseDeadline();

		return $this;
	}

	public function deleteCourse()
	{
		$this->removeVideo();
		return $this->delete();
	}



	/**
	 * 	Helper methods
	 * */
	public function createdAtText($format = 'Y-m-d')
	{
		return $this->created_at->format($format);
	}

	public function deadlineText($format = 'Y-m-d')
	{
		if(empty($this->deadline)) return '-';
		return date($format, strtotime($this->deadline));
	}

	public function isPublished()
	{
		return $this->is_published == 'yes';
	}

	public function isPublishedHtml()
	{
		return $this->isPublished() ? '<span class="text-success"> Terpublikasi </span>' : '<span class="text-danger"> Tidak Dipublikasi </span>';
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

	public function videoSourceText()
	{
		if($this->video_source == 'link') return 'Link Video';
		if($this->video_source == 'file') return 'Upload File Video';
	}

	public function saveVideo($request)
	{
		self::createDirectories();
		if(!empty($request->file_video))
		{
			$this->removeVideo();
			$file = $request->file('file_video');
			$filename = date('YmdHis_').'course.'.$file->getClientOriginalExtension();
			$file->move(storage_path('app/public/course/video'), $filename);
			$this->update([
				'filename'	=> $filename,
			]);
		}

		return $this;
	}

	public function removeVideo()
	{
		if($this->isHasVideo()) {
			\File::delete($this->videoPath());
			$this->update([
				'filename'	=> null,
			]);
		}

		return $this;
	}

	public function isHasVideo()
	{
		if(empty($this->filename)) return false;
		return \File::exists($this->videoPath());
	}

	public function videoPath()
	{
		return storage_path('app/public/course/video/'.$this->filename);
	}

	public function videoLink()
	{
		if($this->isHasVideo()) {
			return url('storage/course/video/'.$this->filename);
		} else {
			return $this->video_link;
		}
	}

	public function videoSourceIsFromFile()
	{
		return $this->video_source == 'file';
	}

	public function videoSourceIsFromLink()
	{
		return $this->video_source == 'link';
	}

	public function isYoutubeVideo()
	{
		return \Str::contains($this->video_link, [ 'youtube', 'youtu.be' ]);  
	}

	public function getYoutubeId()
	{
		$id = null;
		if(\Str::contains($this->video_link, 'https://www.youtube.com/embed/')) {
			$id = str_replace('https://www.youtube.com/embed/', '', $this->video_link);
		} elseif(\Str::startsWith($this->video_link, 'https://youtu.be/')) {
			$id = str_replace('https://youtu.be/', '', $this->video_link);
		} else {
			parse_str( parse_url( $this->video_link, PHP_URL_QUERY ), $my_array_of_vars );
			$id = $my_array_of_vars['v'];
		}

		$id = explode('?', $id);
		return $id[0];
	}

	public function passRequirementIsPassVideo()
	{
		return $this->pass_requirement == self::PASS_VIDEO;
	}

	public function passRequirementIsPassExam()
	{
		return $this->pass_requirement == self::PASS_EXAM;
	}

	public function passRequirementFormatted()
	{
		if($this->passRequirementIsPassVideo()) {
			return 'Menyelesaikan Video Course';
		} elseif($this->passRequirementIsPassExam()) {
			return 'Lulus Exam';
		}
	}

	public function updateParticipantPassedStatus()
	{
		if($this->passRequirementIsPassExam()) {
			CourseParticipant::where('id_course', $this->id)
			->where('exam_passed', 'yes')
			->update([
				'have_passed'	=> 'yes',
				'passed_at'		=> now(),
			]);

			CourseParticipant::where('id_course', $this->id)
			->where('exam_passed', 'no')
			->update([
				'have_passed'	=> 'no',
				'passed_at'		=> null,
			]);

			if(count($this->courseExamParticipantsPassed) != count($this->courseParticipantsPassed)) {
				foreach($this->courseExamParticipantsPassed as $examParticipant) {
					CourseParticipant::where('id_course', $this->id)
					->where('id_employee', $examParticipant->id_employee)
					->update([
						'have_passed'	=> 'yes',
						'video_passed'	=> 'yes',
						'exam_passed'	=> 'yes',
						'video_passed_at' => $examParticipant->ended_at,
						'exam_passed_at' => $examParticipant->ended_at,
						'passed_at' 	=> $examParticipant->ended_at,
						'started_at'	=> $examParticipant->started_at,
					]);
				}
			}

		} elseif($this->passRequirementIsPassVideo()) {
			CourseParticipant::where('id_course', $this->id)
			->where('video_passed', 'yes')
			->update([
				'have_passed'	=> 'yes',
				'passed_at'		=> now(),
			]);

			CourseParticipant::where('id_course', $this->id)
			->where('video_passed', 'no')
			->update([
				'have_passed'	=> 'no',
				'passed_at'		=> null,
			]);
		}

		return $this;
	}

	public function getEmployees()
	{
		$employees = Employee::select([ 'employees.*' ])
							 ->where('status', Employee::STATUS_ACTIVE)
							 ->with([ 'department' ])
							 ->leftJoin('departments', 'employees.id_department', '=', 'departments.id');

		if(!empty($this->id_department)) {
			$employees = $employees->where('id_department', $this->id_department);
		}

		if(!empty($this->id_employee_group)) {
			$employees = $employees->where('id_employee_group', $this->id_employee_group);
		}

		$employees = $employees->orderBy('departments.department_name', 'asc')
							   ->orderBy('employees.employee_name', 'asc')
							   ->get();

		return $employees;
	}

	public function getEmployeesNotAccessed()
	{
		$results = [];
		$employeeIds = [];
		foreach($this->courseParticipants as $participant) {
			$employeeIds[] = $participant->id_employee;
		}

		foreach($this->getEmployees() as $employee) {
			if(!in_array($employee->id, $employeeIds)) {
				$results[] = $employee;
			}
		}

		return $results;
	}





	/**
	 * 	Static
	 * */
	public static function dataTable($request)
	{
		$data = self::select([ 'courses.*' ])
					->with([ 'department', 'position', 'employeeGroup' ])
					->leftJoin('departments', 'courses.id_department', '=', 'departments.id')
					->leftJoin('positions', 'courses.id_position', '=', 'positions.id')
					->leftJoin('employee_groups', 'courses.id_employee_group', '=', 'employee_groups.id');

		if(auth()->user()->isEmployee()) {
			$employee = employee();
			$data->where(function($query) use($employee) {
				$query->where('courses.id_department', $employee->id_department)
					  ->orWhere('courses.id_department', null);
			})->where(function($query) use($employee){
				$query->where('courses.id_employee_group', $employee->id_employee_group)
					  ->orWhere('courses.id_employee_group', null);
			})->where('is_published', 'yes');
		}

		return \DataTables::eloquent($data)
			->addColumn('admin_course_title', function($data){
				$result = $data->course_title;
				$added = '';

				if($department = $data->department) {
					$added .= '<span class="text-primary">['. $data->departmentName() .']</span>';
				}

				if($position = $data->position) {
					$added .= '<span class="text-success">['. $data->positionName() .']</span>';
				}

				if($employeeGroup = $data->employeeGroup) {
					$added .= '<span class="text-info">['. $data->employeeGroupName() .']</span>';
				}

				if(!empty($added)) {
					$result .= '<br>'.$added;
				}

				return $result;
			})
			->editColumn('deadline', function($data){
				return $data->deadlineText('d M Y');
			})
			->editColumn('created_at', function($data){
				return $data->createdAtText('d M Y H:i');
			})
			->editColumn('is_published', function($data){
				return $data->isPublishedHtml();
			})
			->addColumn('admin_action', function($data){
				if(user()->isAdmin()) {
					$button = '
					<div class="dropdown">
						<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Aksi
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="'.route('admin.course.detail', $data->id).'" title="Detail Course">
								<i class="mdi mdi-magnify"></i> Detail 
							</a>';

					if(UserPermission::check('course', 'u')) {
						$button .= '
							<a class="dropdown-item" href="'.route('admin.course.edit', $data->id).'" title="Edit Course">
								<i class="mdi mdi-pencil"></i> Edit 
							</a>';
					}

					if(UserPermission::check('course', 'd')) {
						$button .= '
							<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('admin.course.destroy', $data->id).'" title="Hapus Course">
								<i class="mdi mdi-trash-can"></i> Hapus
							</a>';
					}

					$button .= '
						</div>
					</div>';

				} else {
					$button = '
					<div class="dropdown">
						<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Aksi
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="'.route('course.detail', $data->id).'" title="Detail Course">
								<i class="mdi mdi-magnify"></i> Detail 
							</a>
						</div>
					</div>';
				}

				return $button;
			})
			->addColumn('employee_action', function($data){
				if(user()->isEmployee()) {
					$employeeId = employee()->id;
					$courseParticipant = CourseParticipant::where('id_employee', $employeeId)
														  ->where('id_course', $data->id)
														  ->first();

					$button = '
					<div class="dropdown">
						<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Aksi
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="'.route('employee.elearning.learn', $data->id).'" title="Pelajari Course">
								<i class="mdi mdi-book-open-page-variant"></i> Pelajari 
							</a>';

						if($courseParticipant) {
							if($courseParticipant->isHavePassed()) {
								$button .= '
								<a class="dropdown-item" href="'.route('employee.elearning.download_certificate', $data->id).'" title="Download Sertifikat Course">
									<i class="mdi mdi-certificate-outline"></i> Download Sertifikat 
								</a>';
							}
						}

					$button .= '	
						</div>
					</div>';

					return $button;
				}
			})
			->rawColumns([ 'admin_course_title', 'is_published', 'admin_action', 'employee_action' ])
			->make(true);
	}

	public static function checkCourseDeadline()
	{
		self::where('deadline', '!=', null)
			->where('deadline', '<', now()->format('Y-m-d'))
			->update([
				'is_published' => 'no'
			]);
	}
}
