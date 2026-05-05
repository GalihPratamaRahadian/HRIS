<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseParticipant;
use App\Models\CourseComment;
use App\Models\CourseExam;
use App\Models\CourseExamParticipant;
use App\Models\CourseExamParticipantAnswer;
use DB;

class ElearningController extends Controller
{
	public function index(Request $request)
	{
		if($request->ajax()) {
			return Course::dataTable($request);
		}

		return view('employee.elearning.index', [
			'title'         => 'Course',
			'breadcrumbs'   => [
				[
					'title' => 'Course',
					'link'  => route('employee.elearning')
				],
			]
		]);
	}

	public function learn(Course $course)
	{
		if(!$course->isPublished()) return redirect()->route('employee.elearning');

		$participant = CourseParticipant::createCourseParticipant([
			'id_course'		=> $course->id,
			'id_employee'	=> employee()->id,
		]);

		return view('employee.elearning.learn', [
			'title'			=> 'Pelajari Course',
			'course'		=> $course,
			'participant' 	=> $participant,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Course',
					'link'	=> route('employee.elearning')
				],
				[
					'title'	=> 'Pelajari',
					'link'	=> route('employee.elearning.learn', $course->id)
				],
			]
		]);
	}


	/**
	 * 	Pass Video & Pass Current Duration
	 * */
	public function passVideo(Request $request, $course)
	{
		$course = Course::withTrashed()->find($course);
		if(!$course) abort(404);
		
		if(user()->isEmployee()) {
			$courseParticipant = CourseParticipant::where('id_employee', employee()->id)
												  ->where('id_course', $course->id)
												  ->first();
			if($courseParticipant) {
				$courseParticipant->update([
					'video_passed'		=> 'yes',
					'video_passed_at'	=> now(),
				]);

				return \Res::success([
					'message' => 'Berhasil menyelesaikan video',
				]);
			}
		}

		return \Res::success();
	}

	public function saveVideoSecondsPassed(Request $request, $course)
	{
		$course = Course::withTrashed()->find($course);
		if(!$course) abort(404);
		
		if(user()->isEmployee()) {
			$courseParticipant = CourseParticipant::where('id_employee', employee()->id)
												  ->where('id_course', $course->id)
												  ->first();
			if($courseParticipant) {
				$videoSeconds = (int) $request->video_seconds_passed;
				$videoDuration = (int) $request->video_duration;

				if($courseParticipant->video_seconds_passed < $videoSeconds) {
					$courseParticipant->update([
						'video_seconds_passed' 	=> $videoSeconds,
						'video_duration' 		=> $videoDuration
					]);
				} else {
					if($request->video_duration != $videoDuration) {
						$courseParticipant->update([
							'video_duration' 		=> $videoDuration
						]);
					}
				}

				return \Res::success([
					'message' => 'Berhasil'
				]);
			}
		}

		return \Res::success();
	}



	/**
	 * 	Comment
	 * */
	public function commentCreate(Request $request)
	{
		try {
			DB::beginTransaction();
			CourseComment::createCourseComment($request->all());
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}



	/**
	 * 	Exam
	 * */
	public function exam(Request $request, Course $course)
	{
		// Cek kelulusan
		$check = CourseExamParticipant::where([
			'id_course'		=> $course->id,
			'id_employee'	=> employee()->id,
			'status'		=> CourseExamParticipant::STATUS_ENDED,
			'result'		=> 'passed',
		])->first();

		if($check) {
			return redirect()->route('employee.elearning.learn', $course->id);
		}

		$examParticipant = CourseExamParticipant::createCourseExamParticipant([
			'id_course'		=> $course->id,
			'id_employee'	=> employee()->id,
		]);

		$number = $request->number ?? 1;
		$question = $examParticipant->getSingleQuestion($number);
		$amountOfQuestions = count($examParticipant->courseExamParticipantAnswers);

		return view('employee.elearning.exam', [
			'title'			=> 'Exam',
			'course'		=> $course,
			'examParticipant' => $examParticipant,
			'question'		=> $question,
			'number'		=> $number,
			'amount'		=> $amountOfQuestions,
			'breadcrumbs'	=> [
				[
					'title'	=> 'E-Learning',
					'link'	=> route('employee.elearning')
				],
				[
					'title'	=> 'Exam',
					'link'	=> route('employee.elearning.exam', $course->id)
				],
			]
		]);
	}

	public function setAnswer(Request $request, CourseExamParticipantAnswer $courseExamParticipantAnswer)
	{
		DB::beginTransaction();

		try {
			$courseExamParticipantAnswer->setAnswer($request);
			DB::commit();

			return \Res::success([
				'courseExamParticipantAnswer' => $courseExamParticipantAnswer
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function examAnswers(Request $request, Course $course)
	{
		// Cek kelulusan
		$check = CourseExamParticipant::where([
			'id_course'		=> $course->id,
			'id_employee'	=> employee()->id,
			'status'		=> CourseExamParticipant::STATUS_ENDED,
			'result'		=> 'passed',
		])->first();

		if($check) {
			return redirect()->route('employee.elearning.learn', $course->id);
		}

		$examParticipant = CourseExamParticipant::createCourseExamParticipant([
			'id_course'		=> $course->id,
			'id_employee'	=> employee()->id,
		]);

		$answers = [];
		$data = [];
		$i = 1;
		foreach($examParticipant->courseExamParticipantAnswers as $answer) {
			$data[] = $answer;
			if($i == 10) {
				$answers[] = $data;
				$data = [];
				$i = 0;			}
			$i++;
		}

		if(count($data) > 0) {
			$answers[] = $data;
		}

		return view('employee.elearning.exam_answers', [
			'title'			=> 'Jawaban Exam',
			'course'		=> $course,
			'examParticipant' => $examParticipant,
			'answers'		=> $answers,
			'breadcrumbs'	=> [
				[
					'title'	=> 'E-Learning',
					'link'	=> route('employee.elearning')
				],
				[
					'title'	=> 'Jawaban Exam',
					'link'	=> route('employee.elearning.exam_answers', $course->id)
				],
			]
		]);
	}

	public function examDone(Request $request, Course $course)
	{
		DB::beginTransaction();

		try {
			$examParticipant = CourseExamParticipant::where([
				'id_course'		=> $course->id,
				'id_employee'	=> employee()->id,
				'status'		=> CourseExamParticipant::STATUS_ONGOING,
			])->first();
			$examParticipant->end();
			DB::commit();

			return \Res::success([
				'result_link'	=> route('employee.elearning.exam_result_detail', [ $course->id, $examParticipant->id ])
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function examResultDetail(Course $course, CourseExamParticipant $courseExamParticipant)
	{
		return view('employee.elearning.exam_result_detail', [
			'title'			=> 'Detail Hasil Exam',
			'course'		=> $course,
			'courseExamParticipant' => $courseExamParticipant,
			'breadcrumbs'	=> [
				[
					'title'	=> 'E-Learning',
					'link'	=> route('employee.elearning')
				],
				[
					'title'	=> 'Hasil Exam',
					// 'link'	=> route('course.exam_result', $course->id)
					'link'	=> 'javascript:void(0);'
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('employee.elearning.exam_result_detail', [ $course->id, $courseExamParticipant->id ])
				],
			]
		]);
	}

	public function courseDownloadCertificate(Request $request, $course)
	{
		$course = Course::withTrashed()->find($course);
		if(!$course) abort(404);

		if(auth()->user()->isAdmin()) {
			$idEmployee = $request->id_employee;
		} else {
			$idEmployee = employee()->id;
		}

		$participant = CourseParticipant::where([
			'id_course'		=> $course->id,
			'id_employee'	=> $idEmployee,
			'have_passed'	=> 'yes',
		])->first();

		if($participant) {
			return $participant->downloadCertificate();
		} else {
			abort(404);
		}
	}


	public function certificateIndex(Request $request)
	{
		if($request->ajax()) {
			return CourseParticipant::certificateDataTable($request);
		}

		return view('employee.elearning_certificate.index', [
			'title'			=> 'Sertifikat Kelulusan',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Sertifikat Kelulusan',
					'link'	=> route('employee.elearning_certificate')
				],
			]
		]);
	}


	public function certificateDownload(Request $request, $course)
	{
		$course = Course::withTrashed()->find($course);
		if(!$course) abort(404);

		if(auth()->user()->isAdmin()) {
			$idEmployee = $request->id_employee;
		} else {
			$idEmployee = employee()->id;
		}

		$participant = CourseParticipant::where([
			'id_course'		=> $course->id,
			'id_employee'	=> $idEmployee,
			'have_passed'	=> 'yes',
		])->first();

		if($participant) {
			return $participant->downloadCertificate();
		} else {
			abort(404);
		}
	}
}
