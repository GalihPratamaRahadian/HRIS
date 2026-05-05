<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseExamParticipantAnswer extends Model
{
	protected $fillable = [ 'id_course_exam_participant', 'id_course_exam_question', 'id_course_exam_answer', 'alphabet_answer', 'is_correct' ];


	/**
	 * 	Relationship methods
	 * */
	public function courseParticipantExam()
	{
		return $this->belongsTo('App\Models\CourseParticipantExam', 'id_course_exam_participant');
	}

	public function courseExamQuestion()
	{
		return $this->belongsTo('App\Models\CourseExamQuestion', 'id_course_exam_question');
	}

	public function courseExamAnswer()
	{
		return $this->belongsTo('App\Models\CourseExamAnswerOption', 'id_course_exam_answer');
	}



	/**
	 * 	Helper methods
	 * */
	public function question()
	{
		return $this->courseExamQuestion->question ?? '-';
	}

	public function answer()
	{
		if($this->courseExamAnswer->answer) {
			return $this->alphabet_answer.'. '.$this->courseExamAnswer->answer;
		} else {
			return '-';
		}
	}

	public function answerOptions()
	{
		try {
			return $this->courseExamQuestion->courseExamAnswerOptions;
		} catch (\Exception $e) {
			return [];
		}
	}

	public function setAnswer($request)
	{
		$this->update([
			'id_course_exam_answer'	=> $request->id_course_exam_answer,
			'alphabet_answer'		=> $request->alphabet_answer,
		]);
		return $this;
	}

	public function isCorrect()
	{
		return $this->is_correct == 'yes';
	}
}
