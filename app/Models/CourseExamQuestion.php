<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseExamQuestion extends Model
{
	protected $fillable = [ 'id_course_exam', 'question' ];


	/**
	 * 	Relationship
	 * */
	public function courseExam()
	{
		return $this->belongsTo('App\Models\CourseExam', 'id_course_exam');
	}

	public function courseExamAnswerOptions()
	{
		return $this->hasMany('App\Models\CourseExamAnswerOption', 'id_course_exam_question');
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createCourseExamQuestion(array $request)
	{
		$question = self::create($request);
		$question->courseExam->updateAmountOfQuestions();
		return $question;
	}

	public function updateCourseExamQuestion(array $request)
	{
		$this->update($request);
		$this->courseExam->updateAmountOfQuestions();
		return $this;
	}

	public function deleteCourseExamQuestion()
	{
		$this->removeCourseExamAnswerOptions();
		$courseExam = $this->courseExam;
		$delete = $this->delete();
		$courseExam->updateAmountOfQuestions();
		return $delete;
	}


	/**
	 * 	Helper methods
	 * */
	public function removeCourseExamAnswerOptions()
	{
		CourseExamAnswerOption::where('id_course_exam_question', $this->id)->delete();
		return $this;
	}

	public function fetchData()
	{
		$answers = [];
		foreach($this->courseExamAnswerOptions as $option) {
			$answers[] = $option->fetchData();
		}

		return (object) [
			'id'			=> $this->id,
			'id_course_exam'=> $this->id_course_exam,
			'question'		=> $this->question,
			'answers'		=> $answers,
			'get_link'		=> route('course_exam_question.get', $this->id),
			'update_link'	=> route('course_exam_question.update', $this->id),
			'destroy_link'	=> route('course_exam_question.destroy', $this->id),
		];
	}


	public static function fetchedCourseExamQuestions($questions)
	{
		$results = [];
		foreach($questions as $question) {
			$results[] = $question->fetchData();
		}

		return $results;
	}
}
