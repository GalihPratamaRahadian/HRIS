<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseExamAnswerOption extends Model
{
	protected $fillable = [ 'id_course_exam_question', 'answer', 'is_correct' ];


	/**
	 * 	CRUD methods
	 * */
	public static function createCourseExamAnswerOption(array $request)
	{
		$answer = self::create($request);
		return $answer;
	}

	public function updateCourseExamAnswerOption(array $request)
	{
		$this->update($request);
		return $this;
	}

	public function deleteCourseExamAnswerOption()
	{
		return $this->delete();
	}


	/**
	 * 	Helper methods
	 * */
	public function isCorrect()
	{
		return $this->is_correct == 'yes';
	}

	public function fetchData()
	{
		return (object) [
			'id'						=> $this->id,
			'id_course_exam_question'	=> $this->id_course_exam_question,
			'answer'					=> $this->answer,
			'is_correct'    			=> $this->isCorrect(),
			'get_link'					=> route('course_exam_answer_option.get', $this->id),
			'update_link'				=> route('course_exam_answer_option.update', $this->id),
			'destroy_link'				=> route('course_exam_answer_option.destroy', $this->id),
		];
	}
}
