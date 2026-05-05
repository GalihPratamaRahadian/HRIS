<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseParticipant;
use App\Models\CourseExam;
use App\Models\CourseComment;
use App\Models\CourseExamQuestion;
use App\Models\CourseExamAnswerOption;
use App\Models\CourseExamParticipant;
use App\Models\CourseExamParticipantAnswer;
use DB;

class ElearningController extends Controller
{
	/**
	*	Course
	*
	*/
	public function courseIndex(Request $request)
	{
		if($request->ajax()) {
			return Course::dt($request);
		}

		return view('admin.course.index', [
			'title'			=> 'Course',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Course',
					'link'	=> route('course')
				],
			]
		]);
	}

	public function courseCreate()
	{
		return view('admin.course.create', [
			'title'			=> 'Tambah Course',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Course',
					'link'	=> route('course')
				],
				[
					'title'	=> 'Tambah',
					'link'	=> route('course.create')
				],
			]
		]);
	}

	public function courseStore(Request $request)
	{
		// Validations::validateCourse($request);
		DB::beginTransaction();

		try {
			Course::createCourse($request);
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function courseDetail(Course $course)
	{
		return view('admin.course.detail', [
			'title'			=> 'Detail Course',
			'course'		=> $course,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Course',
					'link'	=> route('course')
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('course.detail', $course->id)
				],
			]
		]);
	}

	public function courseEdit(Course $course)
	{
		return view('admin.course.edit', [
			'title'			=> 'Edit Course',
			'course'		=> $course,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Course',
					'link'	=> route('course')
				],
				[
					'title'	=> 'Edit',
					'link'	=> route('course.edit', $course->id)
				],
			]
		]);
	}

	public function courseUpdate(Request $request, Course $course)
	{
		// Validations::validateCourse($request);
		DB::beginTransaction();

		try {
			$course->updateCourse($request);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function courseDestroy(Course $course)
	{
		DB::beginTransaction();

		try {
			$course->deleteCourse();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function courseLearn(Course $course)
	{
		$participant = CourseParticipant::createCourseParticipant([
			'id_course'		=> $course->id,
			'id_employee'	=> employee()->id,
		]);

		return view('admin.course.learn', [
			'title'			=> 'Pelajari Course',
			'course'		=> $course,
			'participant' 	=> $participant,
			'course'		=> $course,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Course',
					'link'	=> route('course')
				],
				[
					'title'	=> 'Pelajari',
					'link'	=> route('course.learn', $course->id)
				],
			]
		]);
	}

	public function courseExam(Request $request, Course $course)
	{
		// Cek kelulusan
		$check = CourseExamParticipant::where([
			'id_course'		=> $course->id,
			'id_employee'	=> employee()->id,
			'status'		=> CourseExamParticipant::STATUS_ENDED,
			'result'		=> 'passed',
		])->first();

		if($check) {
			return redirect()->route('course.learn', $course->id);
		}

		$examParticipant = CourseExamParticipant::createCourseExamParticipant([
			'id_course'		=> $course->id,
			'id_employee'	=> employee()->id,
		]);

		$number = $request->number ?? 1;
		$question = $examParticipant->getSingleQuestion($number);
		$amountOfQuestions = count($examParticipant->courseExamParticipantAnswers);

		return view('admin.course.exam', [
			'title'			=> 'Exam',
			'course'		=> $course,
			'examParticipant' => $examParticipant,
			'question'		=> $question,
			'number'		=> $number,
			'amount'		=> $amountOfQuestions,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Course',
					'link'	=> route('course')
				],
				[
					'title'	=> 'Exam',
					'link'	=> route('course.exam', $course->id)
				],
			]
		]);
	}

	public function courseExamAnswers(Request $request, Course $course)
	{
		// Cek kelulusan
		$check = CourseExamParticipant::where([
			'id_course'		=> $course->id,
			'id_employee'	=> employee()->id,
			'status'		=> CourseExamParticipant::STATUS_ENDED,
			'result'		=> 'passed',
		])->first();

		if($check) {
			return redirect()->route('course.learn', $course->id);
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

		return view('admin.course.exam_answers', [
			'title'			=> 'Jawaban Exam',
			'course'		=> $course,
			'examParticipant' => $examParticipant,
			'answers'		=> $answers,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Course',
					'link'	=> route('course')
				],
				[
					'title'	=> 'Jawaban Exam',
					'link'	=> route('course.exam_answers', $course->id)
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


	public function coursePassVideo(Request $request, $course)
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


	public function courseSaveVideoSecondsPassed(Request $request, $course)
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


	public function courseExamResultDetail(Course $course, CourseExamParticipant $courseExamParticipant)
	{
		return view('admin.course.exam_result_detail', [
			'title'			=> 'Detail Hasil Exam',
			'course'		=> $course,
			'courseExamParticipant' => $courseExamParticipant,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Course',
					'link'	=> route('course')
				],
				[
					'title'	=> 'Hasil Exam',
					// 'link'	=> route('course.exam_result', $course->id)
					'link'	=> 'javascript:void(0);'
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('course.exam_result_detail', [ $course->id, $courseExamParticipant->id ])
				],
			]
		]);
	}

	public function courseExamDone(Request $request, Course $course)
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
				'result_link'	=> route('course.exam_result_detail', [ $course->id, $examParticipant->id ])
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
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



	/**
	*	Course Exam
	*
	*/
	public function courseExamIndex(Request $request)
	{
		if($request->ajax()) {
			return CourseExam::dt($request);
		}

		return view('admin.course_exam.index', [
			'title'			=> 'Exam',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Exam',
					'link'	=> route('course_exam')
				],
			]
		]);
	}

	public function courseExamCreate()
	{
		return view('admin.course_exam.create', [
			'title'			=> 'Tambah Exam',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Exam',
					'link'	=> route('course_exam')
				],
				[
					'title'	=> 'Tambah',
					'link'	=> route('course_exam.create')
				],
			]
		]);
	}

	public function courseExamStore(Request $request)
	{
		// Validations::validateCourse($request);
		$request->validate([
			'id_course'	=> 'required|unique:course_exams,id_course'
		], [
			'id_course.unique' => 'Course tersebut sudah dibuatkan exam'
		]);
		DB::beginTransaction();

		try {
			$courseExam = CourseExam::createCourseExam($request->all());
			DB::commit();

			return \Res::save([
				'detail_link'	=> route('course_exam.detail', $courseExam->id)
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function courseExamGet(CourseExam $courseExam)
	{
		try {
			$courseExam->load('courseExamQuestions.courseExamAnswerOptions');
			$results = CourseExamQuestion::fetchedCourseExamQuestions($courseExam->courseExamQuestions);

			return \Res::success([
				'courseExamQuestions' => $results
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}

	public function courseExamDetail(CourseExam $courseExam)
	{
		return view('admin.course_exam.detail', [
			'title'			=> 'Detail Exam',
			'courseExam'	=> $courseExam,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Exam',
					'link'	=> route('course_exam')
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('course_exam.detail', $courseExam->id)
				],
			]
		]);
	}

	public function courseExamEdit(CourseExam $courseExam)
	{
		return view('admin.course_exam.edit', [
			'title'			=> 'Edit Exam',
			'courseExam'	=> $courseExam,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Exam',
					'link'	=> route('course_exam')
				],
				[
					'title'	=> 'Edit',
					'link'	=> route('course_exam.edit', $courseExam->id)
				],
			]
		]);
	}

	public function courseExamUpdate(Request $request, CourseExam $courseExam)
	{
		// Validations::validateCourse($request);
		$request->validate([
			'id_course'	=> 'required|unique:course_exams,id_course,'.$courseExam->id,
		], [
			'id_course.unique' => 'Course tersebut sudah dibuatkan exam'
		]);
		DB::beginTransaction();

		try {
			$courseExam->updateCourseExam($request->all());
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function courseExamDestroy(CourseExam $courseExam)
	{
		DB::beginTransaction();

		try {
			$courseExam->deleteCourseExam();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}



	/**
	 * 	Course Exam Question
	 * 
	 * */
	public function courseExamQuestionStore(Request $request)
	{
		DB::beginTransaction();

		try {
			CourseExamQuestion::createCourseExamQuestion($request->all());
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function courseExamQuestionGet(CourseExamQuestion $courseExamQuestion)
	{
		try {
			return \Res::success([
				'courseExamQuestion' => $courseExamQuestion
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}

	public function courseExamQuestionUpdate(Request $request, CourseExamQuestion $courseExamQuestion)
	{
		DB::beginTransaction();

		try {
			$courseExamQuestion->updateCourseExamQuestion($request->all());
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function courseExamQuestionDestroy(CourseExamQuestion $courseExamQuestion)
	{
		DB::beginTransaction();

		try {
			$courseExamQuestion->deleteCourseExamQuestion();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}



	/**
	 * 	Course Exam Answer Option
	 * 
	 * */
	public function courseExamAnswerOptionStore(Request $request)
	{
		DB::beginTransaction();

		try {
			CourseExamAnswerOption::createCourseExamAnswerOption($request->all());
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function courseExamAnswerOptionGet(CourseExamAnswerOption $courseExamAnswerOption)
	{
		try {
			return \Res::success([
				'courseExamAnswerOption' => $courseExamAnswerOption
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}

	public function courseExamAnswerOptionUpdate(Request $request, CourseExamAnswerOption $courseExamAnswerOption)
	{
		DB::beginTransaction();

		try {
			$courseExamAnswerOption->updateCourseExamAnswerOption($request->all());
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function courseExamAnswerOptionDestroy(CourseExamAnswerOption $courseExamAnswerOption)
	{
		DB::beginTransaction();

		try {
			$courseExamAnswerOption->deleteCourseExamAnswerOption();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}



	/**
	 * 	Course Exam Result
	 * 
	 * */
	public function courseResultIndex(Request $request)
	{
		if($request->ajax()) {
			return CourseParticipant::dt($request);
		}

		return view('admin.course_result.index', [
			'title'			=> 'Hasil Course',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Hasil Course',
					'link'	=> route('course_result')
				],
			]
		]);
	}

	public function courseResultExport(Request $request)
	{
		try {
			$action = $request->action;

			if ($action == 'pdf_stream') {
				return CourseParticipant::streamPdfReport($request);
			} elseif ($action == 'pdf_download') {
				return CourseParticipant::downloadPdfReport($request);
			} elseif ($action == 'xlsx_download') {
				$path = CourseParticipant::downloadExcelReport($request);

				return response()->download($path)->deleteFileAfterSend();
			} else {
				return CourseParticipant::streamPdfReport($request);
			}
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	/**
	 * 	Course Exam History
	 * 
	 * */
	public function courseExamHistoryIndex(Request $request)
	{
		if($request->ajax()) {
			return CourseExamParticipant::dt($request);
		}

		return view('admin.course_exam_history.index', [
			'title'			=> 'Histori Exam',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Histori Exam',
					'link'	=> route('course_exam_history')
				],
			]
		]);
	}

	public function courseExamHistoryDetail(CourseExamParticipant $courseExamParticipant)
	{
		return view('admin.course_exam_history.detail', [
			'title'			=> 'Detail Histori Exam',
			'courseExamParticipant' => $courseExamParticipant,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Histori Exam',
					'link'	=> route('course_exam_history')
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('course_exam_history.detail', $courseExamParticipant->id)
				],
			]
		]);
	}



	/**
	 * 	Comment
	 * */
	public function courseCommentCreate(Request $request)
	{
		DB::beginTransaction();

		try {
			CourseComment::createCourseComment($request->all());
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function courseCommentDelete(Request $request, CourseComment $courseComment)
	{
		DB::beginTransaction();

		try {
			$courseComment->deleteCourseComment();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

}
