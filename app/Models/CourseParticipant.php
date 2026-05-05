<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseParticipant extends Model
{
	protected $fillable = [ 'id_course', 'id_employee', 'have_passed', 'video_passed', 'video_passed_at', 'video_seconds_passed', 'video_duration', 'exam_passed', 'exam_passed_at', 'passed_at', 'started_at' ];


	/**
	 * 	Relationship methods
	 * */
	public function course()
	{
		return $this->belongsTo('App\Models\Course', 'id_course')->withTrashed();
	}

	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee')->withTrashed();
	}

	public function employeeTraining()
	{
		return $this->hasOne('App\Models\EmployeeTraining', 'id_course_participant');
	}



	/**
	 * 	CRUD methods
	 * */
	public static function createCourseParticipant(array $request)
	{
		$participant = self::where('id_course', $request['id_course'])
						   ->where('id_employee', $request['id_employee'])
						   ->first();
		if(!$participant) {
			$participant = self::create(array_merge($request,[
				'started_at' => now()
			]));
		}

		return $participant;
	}



	/**
	 * 	Helper methods
	 * */
	public function isHavePassed()
	{
		return $this->have_passed == 'yes';
	}

	public function isHavePassedHtml()
	{
		if($this->isHavePassed()) {
			return '<span class="text-success"> Lulus </span>';
		} else {
			return '<span class="text-danger"> Belum Lulus </span>';
		}
	}

	public function isHavePassedText()
	{
		return $this->isHavePassed() ? 'Lulus' : 'Belum Lulus';
	}

	public function courseTitle()
	{
		return $this->course->course_title ?? '-';
	}

	public function employeeName()
	{
		return $this->employee->employee_name ?? '-';
	}

	public function departmentName()
	{
		return $this->employee->departmentName() ?? '-';
	}

	public function passedAtText($format = 'd M Y')
	{
		return $this->passed_at;
		if($this->passed_at) {
			return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->passed_at)->format($format);
		} else {
			return '-';
		}
	}

	public function videoPassedAtText($format = 'd M Y')
	{
		if($this->video_passed_at) {
			return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->video_passed_at)->format($format);
		} else {
			return '-';
		}
	}

	public function isVideoPassed()
	{
		return $this->video_passed == 'yes';
	}

	public function isVideoPassedHtml()
	{
		if($this->isVideoPassed()) {
			return '<span class="text-success"> [Sudah Akses Video - '.$this->videoPassedAtText('Y-m-d').'] </span>';
		} else {
			return '<span class="text-danger"> [Belum Selesai] </span>';
		}
	}

	public function examPassedAtText($format = 'd M Y')
	{
		if($this->exam_passed_at) {
			return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->exam_passed_at)->format($format);
		} else {
			return '-';
		}
	}

	public function isExamPassed()
	{
		return $this->exam_passed == 'yes';
	}

	public function isExamPassedHtml()
	{
		if($this->isExamPassed()) {
			return '<span class="text-success"> [Sudah Lulus Exam - '.$this->examPassedAtText('Y-m-d').'] </span>';
		} else {
			return '<span class="text-danger"> [Belum Lulus Exam] </span>';
		}
	}

	public function videoSecondsPassedFormatted()
	{
		$seconds = $this->video_seconds_passed;
		$hours = floor($seconds / 3600);
		$seconds -= $hours * 3600;
		$minutes = floor($seconds / 60);
		$seconds -= $minutes * 60;
		return str_pad($hours, 2, '0', STR_PAD_LEFT).':'.str_pad($minutes, 2, '0', STR_PAD_LEFT).':'.str_pad($seconds, 2, '0', STR_PAD_LEFT);
	}

	public function videoDurationFormatted()
	{
		$seconds = $this->video_duration;
		$hours = floor($seconds / 3600);
		$seconds -= $hours * 3600;
		$minutes = floor($seconds / 60);
		$seconds -= $minutes * 60;
		return str_pad($hours, 2, '0', STR_PAD_LEFT).':'.str_pad($minutes, 2, '0', STR_PAD_LEFT).':'.str_pad($seconds, 2, '0', STR_PAD_LEFT);
	}

	public function examScore()
	{
		if($this->isHavePassed())
		{
			$examPart = CourseExamParticipant::where('id_course', $this->id_course)
													->where('id_employee', $this->id_employee)
													->where('result', 'passed')
													->where('status', CourseExamParticipant::STATUS_ENDED)
													->first();
			if($examPart) {
				$amountOfQuestions = $examPart->correct_answer + $examPart->incorrect_answer;
				$score = ($examPart->correct_answer / $amountOfQuestions * 100);
				return $score;
			} else {
				return 0;
			}
		}
		else
		{
			$examPart = CourseExamParticipant::where('id_course', $this->id_course)
													->where('id_employee', $this->id_employee)
													->where('result', '!=', 'passed')
													->where('status', CourseExamParticipant::STATUS_ENDED)
													->first();
			if($examPart) {
				$amountOfQuestions = $examPart->correct_answer + $examPart->incorrect_answer;
				$score = ($examPart->correct_answer / $amountOfQuestions * 100);
				return $score;
			} else {
				return 0;
			}
		}
	}

	public function generateSertificate()
	{
		$this->load('course');
		$this->load('employee');
		$pdf = \PDF::loadView('admin.course.certificate', [
			'participant'	=> $this,
		])->setPaper('a4', 'landscape');
		return $pdf;
	}

	public function downloadCertificate()
	{
		$this->createEmployeeTraining();
		$pdf = $this->generateSertificate();
		return $pdf->stream($this->employeeName().' - '.$this->courseTitle().'.pdf');
	}

	public function startedAt($format = 'd M Y')
	{
		return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->started_at)->format($format);
	}

	public function createEmployeeTraining()
	{
		if($this->isHavePassed()) {
			$this->load('employeeTraining');
			if(!$this->employeeTraining) {
				$filename = date('Ymdhis_').$this->id.'.pdf';
				$path = storage_path('app/public/employee_training/'.$filename);
				$pdf = $this->generateSertificate();
				$pdf->save($path);
				EmployeeTraining::create([
					'id_employee'	=> $this->id_employee,
					'training_name'	=> $this->courseTitle(),
					'date_start'	=> $this->started_at,
					'date_end'		=> $this->passed_at,
					'provider'		=> 'PT. Primaplast Indonesia',
					'file'			=> $filename,
					'id_course_participant' => $this->id,
				]);
				$this->load('employeeTraining');
			}
		}

		return $this;
	}



	/**
	 * 	Static
	 * */
	public static function dt($request)
	{
		$data = self::select([ 'course_participants.*' ])
					->with([ 'course', 'employee.department' ])
					->leftJoin('courses', 'course_participants.id_course', '=', 'courses.id')
					->leftJoin('employees', 'course_participants.id_employee', '=', 'employees.id')
					->leftJoin('departments', 'employees.id_department', '=', 'departments.id');

		if($request->id_course) {
			if($request->id_course != 'all') {
				$data = $data->where('id_course', $request->id_course);
			}
		}

		if($request->have_passed) {
			if($request->have_passed != 'all') {
				$data = $data->where('have_passed', $request->have_passed);
			}
		}

		return \DataTables::eloquent($data)
			->editColumn('course.course_title', function($data){
				return $data->courseTitle();
			})
			->editColumn('employee.employee_name', function($data){
				return $data->employeeName();
			})
			->editColumn('department.department_name', function($data){
				return $data->departmentName();
			})
			->editColumn('have_passed', function($data){
				return $data->isHavePassedHtml();
			})
			->editColumn('exam_score', function($data){
				return $data->examScore();
			})
			->editColumn('passed_at', function($data){
				return $data->passedAtText('d F Y');
			})
			->addColumn('action', function($data){
				if($data->isHavePassed()) {
					$button = '
					<div class="dropdown">
						<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Aksi
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="'.route('course.download_certificate', $data->id_course).'?id_employee='.$data->id_employee.'" title="Download Sertifikat" target="_blank">
								<i class="mdi mdi-certificate-outline"></i> Download Sertifikat 
							</a>
						</div>
					</div>';
				} else {
					$button = '-';
				}

				return $button;
			})
			->addColumn('employee_action', function($data){
				$button = '
				<a class="btn btn-primary" href="'.route('employee.elearning_certificate.download', $data->id_course).'" title="Download Sertifikat" target="_blank">
					<i class="mdi mdi-certificate-outline"></i> Download Sertifikat 
				</a>';

				return $button;
			})
			->rawColumns([ 'have_passed', 'action', 'employee_action' ])
			->make(true);
	}


	public static function certificateDataTable($request)
	{
		$data = self::select([ 'course_participants.*' ])
					->where('course_participants.have_passed', 'yes')
					->where('course_participants.id_employee', employee()->id)
					->with([ 'course' ])
					->leftJoin('courses', 'course_participants.id_course', '=', 'courses.id');

		return \DataTables::eloquent($data)
			->editColumn('course.course_title', function($data){
				return $data->courseTitle();
			})
			->editColumn('exam_score', function($data){
				return $data->examScore();
			})
			->editColumn('passed_at', function($data){
				return $data->passedAtText('d F Y');
			})
			->addColumn('employee_action', function($data){
				$button = '
				<a class="btn btn-primary py-2" href="'.route('employee.elearning_certificate.download', $data->id_course).'" title="Download Sertifikat" target="_blank">
					<i class="mdi mdi-certificate-outline"></i> Download Sertifikat 
				</a>';

				return $button;
			})
			->rawColumns([ 'have_passed', 'employee_action' ])
			->make(true);
	}



	/**
	 * 	Report
	 * */
	public static function generateDataForReport($request, $filename = null)
	{
		$courseParticipants = self::with([ 'course', 'employee.department' ]);
		$course = null;
		if(empty($filename)) $filename = 'Hasil_Course';
		$notAccess = [];

		$courseParticipants = $courseParticipants->where('id_course', $request->id_course);
		$course = Course::find($request->id_course);

		if(!empty($request->have_passed)) {
			if($request->have_passed != 'all' && $request->have_passed != 'not_accessed') {
				$courseParticipants = $courseParticipants->where('have_passed', $request->have_passed);
			}
		}

		$courseParticipants = $courseParticipants->orderBy('id_course', 'asc')->get();

		$notAccessed = [];
		if($request->have_passed == 'all' || $request->have_passed == 'not_accessed') {
			$employeeIds = [];
			foreach($courseParticipants as $participant) {
				$employeeIds[] = $participant->id_employee;
			}

			foreach($course->getEmployees() as $employee) {
				if(!in_array($employee->id, $employeeIds)) {
					$notAccessed[] = $employee;
				}
			}
		}

		if($request->have_passed == 'not_accessed') {
			$courseParticipants = [];
		}

		return [
			'data'		=> $courseParticipants,
			'notAccessed' => $notAccessed,
			'course'	=> $course,
			'filename'	=> $filename
		];
	}

	public static function generatePdfReport($request)
	{
		$data = self::generateDataForReport($request);
		$courseParticipants = $data['data'];
		$notAccessed = $data['notAccessed'];
		$course = $data['course'];
		$filename = $data['filename'];

		$pdf = \PDF::loadView('admin.course_result.report', [
			'courseParticipants' => $courseParticipants,
			'notAccessed' => $notAccessed,
			'course' => $course,
		])->setPaper('a4', 'landscape');
		$filename .= '.pdf';

		return (object) [
			'pdf'		=> $pdf,
			'filename'	=> $filename,
		];
	}


	public static function streamPdfReport($request)
	{
		$result = self::generatePdfReport($request);

		return $result->pdf->stream($result->filename);
	}


	public static function downloadPdfReport($request)
	{
		$result = self::generatePdfReport($request);

		return $result->pdf->download($result->filename);
	}

	public static function downloadExcelReport($request)
	{
		$data = self::generateDataForReport($request);
		$courseParticipants = $data['data'];
		$notAccessed = $data['notAccessed'];
		$course = $data['course'];
		$filename = $data['filename'];

		$headerStyle = [ 'font-style'=>'bold', 'halign'=>'center', 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin' ];
		$bodyStyle = [ 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin' ];

		$writer = new \App\MyClass\XLSXWriter();
		$totalRow = 0;

		$header = [
			''=>'string',//text
			''=>'string',//text
			''=>'string',//text
			''=>'string',//text
			''=>'string',//text
			''=>'string',//text
		];

		$writer->writeSheetHeader('Sheet1', $header, [
			'widths'=> [5,35,30,20,20,20]
		]);
		$totalRow++;

		$writer->writeSheetRow('Sheet1', [
			'Hasil Course',
		], [ 'halign' => 'center', 'font-size' => '16pt' ]);
		$writer->markMergedCell('Sheet1', $start_row=$totalRow, $start_col=0, $end_row=$totalRow, $end_col=5);
		$totalRow++;

		$writer->writeSheetRow('Sheet1', [
			$course->course_title,
		], [ 'halign' => 'center', 'font-size' => '16pt' ]);
		$writer->markMergedCell('Sheet1', $start_row=$totalRow, $start_col=0, $end_row=$totalRow, $end_col=5);
		$totalRow++;

		$writer->writeSheetRow('Sheet1', []);
		$totalRow++;

		$writer->writeSheetRow('Sheet1', [
			'No', 'Karyawan', 'Departemen', 'Nilai', 'Status Kelulusan', 'Tgl Lulus'
		], $headerStyle);
		$totalRow++;

		$i = 1;
		foreach($courseParticipants as $courseParticipant) {
			$writer->writeSheetRow('Sheet1', [
				$i++,
				$courseParticipant->employeeName(),
				$courseParticipant->departmentName(),
				$courseParticipant->examScore(),
				$courseParticipant->isHavePassedText(),
				$courseParticipant->passedAtText(),
			], $bodyStyle);
			$totalRow++;
		}

		foreach($notAccessed as $employee) {
			$writer->writeSheetRow('Sheet1', [
				$i++,
				$employee->employee_name,
				$employee->departmentName(),
				'',
				'Belum Mengakses',
				'',
			], $bodyStyle);
			$totalRow++;
		}

		$filename .= '.xlsx';

		$path = \Setting::temps($filename);
		$writer->writeToFile($path);

		return $path;
	}
}
