<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseExam extends Model
{
	protected $fillable = [ 'id_course', 'duration', 'is_random_question', 'amount_of_questions' ];


	/**
	 * 	Relationship methods
	 * */
	public function course()
	{
		return $this->belongsTo('App\Models\Course', 'id_course')->withTrashed();
	}

	public function courseExamQuestions()
	{
		return $this->hasMany('App\Models\CourseExamQuestion', 'id_course_exam');
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createCourseExam(array $request)
	{
		return self::create($request);
	}

	public function updateCourseExam(array $request)
	{
		$this->update($request);
		return $this;
	}

	public function deleteCourseExam()
	{
		$this->removeCourseExamQuestions();
		return $this->delete();
	}


	/**
	 * 	Helper methods
	 * */
	public function courseTitle()
	{
		return $this->course->course_title ?? '-';
	}

	public function removeCourseExamQuestions()
	{
		foreach($this->courseExamQuestions as $question) {
			$question->deleteCourseExamQuestion();
		}

		return $this;
	}

	public function durationText()
	{
		return number_format($this->duration).' Menit';
	}

	public function isRandomQuestion()
	{
		return $this->is_random_question == 'yes';
	}

	public function isRandomQuestionText()
	{
		return $this->isRandomQuestion() ? 'Ya' : 'Tidak';
	}

	public function updateAmountOfQuestions()
	{
		$this->update([
			'amount_of_questions' => count($this->courseExamQuestions)
		]);
		
		return $this;
	}

	public function generateQuestions()
	{
		$questions = [];
		foreach($this->courseExamQuestions as $question) {
			$questions[] = $question;
		}

		if($this->isRandomQuestion()) {
			shuffle($questions);
		}
		
		return $questions;
	}



	/**
	 * 	Static
	 * */
	public static function dt($request)
	{
		$data = self::select([ 'course_exams.*' ])
					->has('course')
					->with([ 'course' ])
					->leftJoin('courses', 'course_exams.id_course', '=', 'courses.id');

		return \DataTables::eloquent($data)
			->editColumn('duration', function($data){
				return $data->durationText();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('admin.course_exam.detail', $data->id).'" title="Detail Exam">
							<i class="mdi mdi-magnify"></i> Detail 
						</a>';

				if(UserPermission::check('course_exam', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('admin.course_exam.edit', $data->id).'" title="Edit Exam">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(UserPermission::check('course_exam', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('admin.course_exam.destroy', $data->id).'" title="Hapus Exam">
							<i class="mdi mdi-trash-can"></i> Hapus
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
