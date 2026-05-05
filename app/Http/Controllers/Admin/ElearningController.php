<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseParticipant;
use App\Models\CourseExam;
use App\Models\CourseComment;
use App\Models\CourseExamQuestion;
use App\Models\CourseExamAnswerOption;
use App\Models\CourseExamParticipant;
use App\Models\CourseExamParticipantAnswer;
use App\MyClass\Validations;
use DB;

class ElearningController extends Controller
{
	/**
	*   Course
	*
	*/
	public function courseIndex(Request $request)
	{
		if($request->ajax()) {
			return Course::dataTable($request);
		}

		return view('admin.course.index', [
			'title'			=> 'Course',
			'breadcrumbs'	=> [
				[
					'title' => 'Course',
					'link'  => route('admin.course')
				],
			]
		]);
	}

	public function courseCreate()
	{
		return view('admin.course.create', [
			'title'         => 'Tambah Course',
			'breadcrumbs'   => [
				[
					'title' => 'Course',
					'link'  => route('admin.course')
				],
				[
					'title' => 'Tambah',
					'link'  => route('admin.course.create')
				],
			]
		]);
	}

	public function courseStore(Request $request)
	{
		Validations::validateCourse($request);
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
			'title'         => 'Detail Course',
			'course'        => $course,
			'breadcrumbs'   => [
				[
					'title' => 'Course',
					'link'  => route('admin.course')
				],
				[
					'title' => 'Detail',
					'link'  => route('admin.course.detail', $course->id)
				],
			]
		]);
	}

	public function courseEdit(Course $course)
	{
		return view('admin.course.edit', [
			'title'         => 'Edit Course',
			'course'        => $course,
			'breadcrumbs'   => [
				[
					'title' => 'Course',
					'link'  => route('admin.course')
				],
				[
					'title' => 'Edit',
					'link'  => route('admin.course.edit', $course->id)
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
			'id_course'     => $course->id,
			'id_employee'   => $idEmployee,
			'have_passed'   => 'yes',
		])->first();

		if($participant) {
			return $participant->downloadCertificate();
		} else {
			abort(404);
		}
	}


	

	public function courseExamResultDetail(Course $course, CourseExamParticipant $courseExamParticipant)
	{
		return view('admin.course.exam_result_detail', [
			'title'         => 'Detail Hasil Exam',
			'course'        => $course,
			'courseExamParticipant' => $courseExamParticipant,
			'breadcrumbs'   => [
				[
					'title' => 'Course',
					'link'  => route('admin.course')
				],
				[
					'title' => 'Hasil Exam',
					// 'link'   => route('admin.course.exam_result', $course->id)
					'link'  => 'javascript:void(0);'
				],
				[
					'title' => 'Detail',
					'link'  => route('admin.course.exam_result_detail', [ $course->id, $courseExamParticipant->id ])
				],
			]
		]);
	}

	


	/**
	*   Course Exam
	*
	*/
	public function courseExamIndex(Request $request)
	{
		if($request->ajax()) {
			return CourseExam::dt($request);
		}

		return view('admin.course_exam.index', [
			'title'         => 'Exam',
			'breadcrumbs'   => [
				[
					'title' => 'Exam',
					'link'  => route('admin.course_exam')
				],
			]
		]);
	}

	public function courseExamCreate()
	{
		return view('admin.course_exam.create', [
			'title'         => 'Tambah Exam',
			'breadcrumbs'   => [
				[
					'title' => 'Exam',
					'link'  => route('admin.course_exam')
				],
				[
					'title' => 'Tambah',
					'link'  => route('admin.course_exam.create')
				],
			]
		]);
	}

	public function courseExamStore(Request $request)
	{
		// Validations::validateCourse($request);
		$request->validate([
			'id_course' => 'required|unique:course_exams,id_course'
		], [
			'id_course.unique' => 'Course tersebut sudah dibuatkan exam'
		]);
		DB::beginTransaction();

		try {
			$courseExam = CourseExam::createCourseExam($request->all());
			DB::commit();

			return \Res::save([
				'detail_link'   => route('admin.course_exam.detail', $courseExam->id)
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
			'title'         => 'Detail Exam',
			'courseExam'    => $courseExam,
			'breadcrumbs'   => [
				[
					'title' => 'Exam',
					'link'  => route('admin.course_exam')
				],
				[
					'title' => 'Detail',
					'link'  => route('admin.course_exam.detail', $courseExam->id)
				],
			]
		]);
	}

	public function courseExamEdit(CourseExam $courseExam)
	{
		return view('admin.course_exam.edit', [
			'title'         => 'Edit Exam',
			'courseExam'    => $courseExam,
			'breadcrumbs'   => [
				[
					'title' => 'Exam',
					'link'  => route('admin.course_exam')
				],
				[
					'title' => 'Edit',
					'link'  => route('admin.course_exam.edit', $courseExam->id)
				],
			]
		]);
	}

	public function courseExamUpdate(Request $request, CourseExam $courseExam)
	{
		// Validations::validateCourse($request);
		$request->validate([
			'id_course' => 'required|unique:course_exams,id_course,'.$courseExam->id,
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
	 *  Course Exam Question
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
	 *  Course Exam Answer Option
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
	 *  Course Exam Result
	 * 
	 * */
	public function courseResultIndex(Request $request)
	{
		if($request->ajax()) {
			return CourseParticipant::dt($request);
		}

		return view('admin.course_result.index', [
			'title'         => 'Hasil Course',
			'breadcrumbs'   => [
				[
					'title' => 'Hasil Course',
					'link'  => route('admin.course_result')
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
	 *  Course Exam History
	 * 
	 * */
	public function courseExamHistoryIndex(Request $request)
	{
		if($request->ajax()) {
			return CourseExamParticipant::dt($request);
		}

		return view('admin.course_exam_history.index', [
			'title'         => 'Histori Exam',
			'breadcrumbs'   => [
				[
					'title' => 'Histori Exam',
					'link'  => route('admin.course_exam_history')
				],
			]
		]);
	}

	public function courseExamHistoryDetail(CourseExamParticipant $courseExamParticipant)
	{
		return view('admin.course_exam_history.detail', [
			'title'         => 'Detail Histori Exam',
			'courseExamParticipant' => $courseExamParticipant,
			'breadcrumbs'   => [
				[
					'title' => 'Histori Exam',
					'link'  => route('admin.course_exam_history')
				],
				[
					'title' => 'Detail',
					'link'  => route('admin.course_exam_history.detail', $courseExamParticipant->id)
				],
			]
		]);
	}



	/**
	 *  Comment
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

