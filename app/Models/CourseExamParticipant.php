<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseExamParticipant extends Model
{
	protected $fillable = [ 'id_course', 'id_employee', 'started_at', 'ended_at', 'status', 'result', 'correct_answer', 'incorrect_answer' ];

	const STATUS_ONGOING	= 'ongoing';
	const STATUS_ENDED		= 'ended';


	/**
	 * 	Relationship methods
	 * */
	public function course()
	{
		return $this->belongsTo('App\Models\Course', 'id_course')->withTrashed();
	}

	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee');
	}

	public function courseExamParticipantAnswers()
	{
		return $this->hasMany('App\Models\CourseExamParticipantAnswer', 'id_course_exam_participant');
	}



	/**
	 * 	CRUD methods
	 * */
	public static function createCourseExamParticipant(array $request)
	{
		CourseExamParticipant::where('id_employee', employee()->id)
							 ->where('ended_at', '<', now())
							 ->update([
							 	'status'	=> self::STATUS_ENDED,
							 ]);

		$course = Course::find($request['id_course']);
		$examParticipant = self::where('id_course', $request['id_course'])
							   ->where('id_employee', $request['id_employee'])
							   ->where('ended_at', '>=', now())
							   ->where('status', self::STATUS_ONGOING)
							   ->first();
		if(!$examParticipant) {
			$exam = $course->courseExam;
			$examParticipant = self::create(array_merge($request, [
				'started_at'	=> now(),
				'ended_at'		=> now()->addMinutes($exam->duration),
				'status'		=> self::STATUS_ONGOING,
			]));
			$examParticipant->generateQuestions();
		}

		return $examParticipant;
	}



	/**
	 * 	Helper methods
	 * */
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

	public function startedAt($format = 'd M Y')
	{
		return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->started_at)->format($format);
	}

	public function endedAt($format = 'd M Y')
	{
		return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->ended_at)->format($format);
	}

	public function amountOfQuestions()
	{
		return count($this->courseExamParticipantAnswers);
	}

	public function generateQuestions()
	{
		$this->load('courseExamParticipantAnswers');

		if(count($this->courseExamParticipantAnswers) == 0) {
			if($course = $this->course) {
				if($exam = $course->courseExam) {
					foreach($exam->generateQuestions() as $question)
					{
						CourseExamParticipantAnswer::create([
							 'id_course_exam_participant'	=> $this->id,
							 'id_course_exam_question'		=> $question->id
						]);
					}
				}
			}
			$this->load('courseExamParticipantAnswers');
		}

		return $this;
	}

	public function getSingleQuestion($number = 1)
	{
		if(!$number) $number = 1;
		$this->load('courseExamParticipantAnswers');
		$amount = count($this->courseExamParticipantAnswers);
		$questions = [];

		$i = 1;
		foreach($this->courseExamParticipantAnswers as $question) {
			$nextQuestionLink = null;
			$prevQuestionLink = null;

			if($i > 1) {
				$prevQuestionLink = route('employee.elearning.exam', $this->id_course).'?number='.($i - 1);
			}

			if($i < $amount) {
				$nextQuestionLink = route('employee.elearning.exam', $this->id_course).'?number='.($i + 1);
			}

			$questions[] = [
				'question'	=> $question,
				'nextQuestionLink'	=> $nextQuestionLink,
				'prevQuestionLink'	=> $prevQuestionLink,
			];

			$i++;
		}

		return (object) $questions[$number - 1];
	}

	public function timeRemainingInSeconds()
	{
		return now()->diffInSeconds($this->ended_at);
	}

	public function end()
	{
		$this->update([
			'ended_at'	=> now(),
			'status'	=> self::STATUS_ENDED,
		]);
		$this->generateResult();

		return $this;
	}

	public function percentageOfResult()
	{
		return ($this->correct_answer) / ($this->correct_answer + $this->incorrect_answer) * 100;
	}

	public function generateResult()
	{
		$correct = 0;
		$incorrect = 0;
		foreach($this->courseExamParticipantAnswers as $answer)
		{
			if($answer->courseExamAnswer->isCorrect()) {
				$correct++;
			} else {
				$incorrect++;
			}

			$answer->update([
				'is_correct' => $answer->courseExamAnswer->is_correct,
			]);
		}


		$this->update([
			'correct_answer'	=> $correct,
			'incorrect_answer'	=> $incorrect,
			'result'			=> 'failed',
		]);

		$minimumPercentageForExamPassed = setting('minimum_percentage_for_exam_passed', 100);

		if($this->percentageOfResult() >= $minimumPercentageForExamPassed) {
			$this->setPassed();
		} else {
			$this->setNotPassed();
		}

		return $this;
	}

	public function setPassed()
	{
		$this->update([
			'result'	=> 'passed',
		]);

		try {
			$participant = CourseParticipant::where('id_employee', $this->id_employee)
							->where('id_course', $this->id_course)
							->first();
			$participant->update([
				'have_passed'	=> 'yes',
				'passed_at'		=> now(),
				'exam_passed'	=> 'yes',
				'exam_passed_at' => now(),
			]);
			$participant->createEmployeeTraining();
		} catch (\Exception $e) {}

		return $this;
	}

	public function setNotPassed()
	{
		$this->update([
			'result'	=> 'failed',
		]);

		try {
			$participant = CourseParticipant::where('id_employee', $this->id_employee)
											->where('id_course', $this->id_course)
											->update([
												'have_passed'	=> 'no',
												'passed_at'		=> null,
											]);
		} catch (\Exception $e) {}

		return $this;
	}

	public function isHavePassed()
	{
		return $this->result == 'passed';
	}

	public function resultHtml()
	{
		if($this->isHavePassed()) {
			return '<span class="text-success"> Lulus </span>';
		} else {
			return '<span class="text-danger"> Belum Lulus </span>';
		}
	}



	/**
	 * 	Static
	 * */
	public static function dt($request)
	{
		$data = self::select([ 'course_exam_participants.*' ])
					->with([ 'course', 'employee.department' ])
					->leftJoin('courses', 'course_exam_participants.id_course', '=', 'courses.id')
					->leftJoin('employees', 'course_exam_participants.id_employee', '=', 'employees.id')
					->leftJoin('departments', 'employees.id_department', '=', 'departments.id');

		return \DataTables::eloquent($data)
			->editColumn('course.course_title', function($data){
				return $data->courseTitle();
			})
			->editColumn('employee.employee_name', function($data){
				return $data->employeeName();
			})
			->editColumn('department.department_name', function($data){
				return $data->employee ? $data->employee->departmentName() : '-';
			})
			->editColumn('result', function($data){
				return $data->resultHtml();
			})
			->editColumn('started_at', function($data){
				return $data->startedAt('d M Y H:i');
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('admin.course_exam_history.detail', $data->id).'" title="Detail Histori Exam">
							<i class="mdi mdi-magnify"></i> Detail
						</a>
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'result', 'action' ])
			->make(true);
	}

}
