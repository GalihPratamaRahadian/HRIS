<?php

if(appconfig('is_using_proxy'))
{
	URL::forceRootUrl(appconfig('proxy_url'));
	URL::forceScheme(appconfig('proxy_schema'));
}

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/**
 * 	Employee Page
 * */
Route::group([ 'middleware' => 'allowedRole:employee,staff' ], function(){

	Route::get('employee-education', 'EmployeePageController@employeeEducation')->name('employee.employee_education');
	Route::get('employee-family', 'EmployeePageController@employeeFamily')->name('employee.employee_family');
	Route::get('employee-training', 'EmployeePageController@employeeTraining')->name('employee.employee_training');



	Route::prefix('personal-profile')->group(function(){
		Route::get('/', 'EmployeePageController@personalProfile')->name('emp.personal_profile');
		Route::get('download', 'EmployeePageController@personalProfileDownload')->name('emp.personal_profile_download');
	});

	Route::prefix('attendance')->group(function(){
		Route::get('clockin', 'EmployeePageController@clockIn')->name('attendance.clock_in');
		Route::post('clockin', 'EmployeePageController@clockInProcess')->name('attendance.store_clock_in');
		Route::get('check-day', 'EmployeePageController@checkDay')->name('attendance.check_day');
		Route::post('check-day', 'EmployeePageController@checkDayProcess')->name('attendance.store_check_day');
		Route::get('clockout', 'EmployeePageController@clockOut')->name('attendance.clock_out');
		Route::post('clockout', 'EmployeePageController@clockOutProcess')->name('attendance.store_clock_out');
	});

	Route::prefix('sales-tracking-app')->group(function(){
		Route::get('/', 'EmployeePageController@salesTrackingIndex')->name('emp.sales_tracking');
		Route::get('get-store', 'EmployeePageController@salesTrackingGetStore')->name('emp.sales_tracking.get_store');
		Route::get('create-store', 'EmployeePageController@salesTrackingCreateStore')->name('emp.sales_tracking.create_store');
		Route::post('create-store', 'EmployeePageController@salesTrackingSaveStore')->name('emp.sales_tracking.save_store');
		Route::get('{store}/check-in', 'EmployeePageController@salesTrackingStoreCheckIn')->name('emp.sales_tracking.create_store_check_in');
		Route::post('{store}/check-in', 'EmployeePageController@salesTrackingStoreCheckInSave')->name('emp.sales_tracking.save_store_check_in');
	});

});


Route::prefix('employee')->middleware([ 'allowedRole:employee,staff' ])->group(function(){
	Route::prefix('leave-submission')->group(function(){
		Route::get('/', 'Employee\LeaveSubmissionController@index')->name('employee.leave_submission');
		Route::get('create', 'Employee\LeaveSubmissionController@create')->name('employee.leave_submission.create');
		Route::post('store', 'Employee\LeaveSubmissionController@store')->name('employee.leave_submission.store');
		Route::get('{leaveSubmission}/detail', 'Employee\LeaveSubmissionController@detail')->name('employee.leave_submission.detail');
	});

	Route::prefix('leave-approval')->group(function(){
		Route::get('/', 'Employee\LeaveApprovalController@index')->name('employee.leave_approval');
		Route::get('{leaveSubmissionApproval}/detail', 'Employee\LeaveApprovalController@detail')->name('employee.leave_approval.detail');
		Route::post('{leaveSubmissionApproval}/approve', 'Employee\LeaveApprovalController@approve')->name('employee.leave_approval.approve');
		Route::post('{leaveSubmissionApproval}/reject', 'Employee\LeaveApprovalController@reject')->name('employee.leave_approval.reject');
	});

	Route::prefix('overtime-submission')->group(function(){
		Route::get('/', 'Employee\OvertimeSubmissionController@index')->name('employee.overtime_submission');
		Route::get('create', 'Employee\OvertimeSubmissionController@create')->name('employee.overtime_submission.create');
		Route::post('store', 'Employee\OvertimeSubmissionController@store')->name('employee.overtime_submission.store');
		Route::get('{overtimeSubmission}/detail', 'Employee\OvertimeSubmissionController@detail')->name('employee.overtime_submission.detail');
	});

	Route::prefix('overtime-approval')->group(function(){
		Route::get('/', 'Employee\OvertimeApprovalController@index')->name('employee.overtime_approval');
		Route::get('create-submission', 'Employee\OvertimeApprovalController@createSubmission')->name('employee.overtime_approval.create_submission');
		Route::post('store-submission', 'Employee\OvertimeApprovalController@storeSubmission')->name('employee.overtime_approval.store_submission');
		Route::get('{overtimeSubmissionApproval}/detail', 'Employee\OvertimeApprovalController@detail')->name('employee.overtime_approval.detail');
		Route::post('{overtimeSubmissionApproval}/approve', 'Employee\OvertimeApprovalController@approve')->name('employee.overtime_approval.approve');
		Route::post('{overtimeSubmissionApproval}/reject', 'Employee\OvertimeApprovalController@reject')->name('employee.overtime_approval.reject');
	});

	Route::prefix('sick-necessity-submission')->group(function(){
		Route::get('/', 'Employee\SickNecessitySubmissionController@index')->name('employee.sick_necessity_submission');
		Route::get('create', 'Employee\SickNecessitySubmissionController@create')->name('employee.sick_necessity_submission.create');
		Route::post('store', 'Employee\SickNecessitySubmissionController@store')->name('employee.sick_necessity_submission.store');
		Route::get('{sickNecessitySubmission}/detail', 'Employee\SickNecessitySubmissionController@detail')->name('employee.sick_necessity_submission.detail');
	});

	Route::prefix('sick-necessity-approval')->group(function(){
		Route::get('/', 'Employee\SickNecessityApprovalController@index')->name('employee.sick_necessity_approval');
		Route::get('{sickNecessitySubmissionApproval}/detail', 'Employee\SickNecessityApprovalController@detail')->name('employee.sick_necessity_approval.detail');
		Route::post('{sickNecessitySubmissionApproval}/approve', 'Employee\SickNecessityApprovalController@approve')->name('employee.sick_necessity_approval.approve');
		Route::post('{sickNecessitySubmissionApproval}/reject', 'Employee\SickNecessityApprovalController@reject')->name('employee.sick_necessity_approval.reject');
	});

	Route::prefix('attendance-permission-submission')->group(function(){
		Route::get('/', 'Employee\AttendancePermissionSubmissionController@index')->name('employee.attendance_permission_submission');
		Route::get('create', 'Employee\AttendancePermissionSubmissionController@create')->name('employee.attendance_permission_submission.create');
		Route::post('store', 'Employee\AttendancePermissionSubmissionController@store')->name('employee.attendance_permission_submission.store');
		Route::get('{attendancePermissionSubmission}/detail', 'Employee\AttendancePermissionSubmissionController@detail')->name('employee.attendance_permission_submission.detail');
	});

	Route::prefix('attendance-permission-approval')->group(function(){
		Route::get('/', 'Employee\AttendancePermissionApprovalController@index')->name('employee.attendance_permission_approval');
		Route::get('{attendancePermissionApproval}/detail', 'Employee\AttendancePermissionApprovalController@detail')->name('employee.attendance_permission_approval.detail');
		Route::post('{attendancePermissionApproval}/approve', 'Employee\AttendancePermissionApprovalController@approve')->name('employee.attendance_permission_approval.approve');
		Route::post('{attendancePermissionApproval}/reject', 'Employee\AttendancePermissionApprovalController@reject')->name('employee.attendance_permission_approval.reject');
	});

	Route::prefix('announcement')->group(function(){
		Route::get('/', 'Employee\AnnouncementController@index')->name('employee.announcement');
		Route::get('{announcement}/detail', 'Employee\AnnouncementController@detail')->name('employee.announcement.detail');
	});


	Route::prefix('elearning')->group(function(){
		Route::get('/', 'Employee\ElearningController@index')->name('employee.elearning');
		Route::get('{course}/learn', 'Employee\ElearningController@learn')->name('employee.elearning.learn');
		Route::post('create-comment', 'Employee\ElearningController@commentCreate')->name('employee.elearning.comment.create');

		Route::get('{course}/exam', 'Employee\ElearningController@exam')->name('employee.elearning.exam');
		Route::get('{course}/exam-answers', 'Employee\ElearningController@examAnswers')->name('employee.elearning.exam_answers');
		Route::post('{course}/exam-done', 'Employee\ElearningController@examDone')->name('employee.elearning.exam_done');
		Route::get('{course}/exam-result', 'Employee\ElearningController@examResult')->name('employee.elearning.exam_result');
		Route::get('{course}/download-certificate', 'Employee\ElearningController@courseDownloadCertificate')->name('employee.elearning.download_certificate');

		Route::get('{course}/exam-result/{courseExamParticipant}/detail', 'Employee\ElearningController@examResultDetail')->name('employee.elearning.exam_result_detail');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('{courseExamParticipantAnswer}/set-answer', 'Employee\ElearningController@setAnswer')->name('employee.elearning.set_answer');
			Route::post('{course}/pass-video', 'Employee\ElearningController@passVideo')->name('employee.elearning.pass_video');
			Route::post('{course}/save-seconds-passed', 'Employee\ElearningController@saveVideoSecondsPassed')->name('employee.elearning.save_seconds_passed');
		});
	});

	Route::prefix('elearning-certificate')->group(function(){
		Route::get('/', 'Employee\ElearningController@certificateIndex')->name('employee.elearning_certificate');
		Route::get('{course}/download', 'Employee\ElearningController@certificateDownload')->name('employee.elearning_certificate.download');
	});


	/**
	 * 	Training
	 * */
	Route::prefix('training')->group(function(){
		Route::get('/', 'Employee\TrainingController@index')->name('employee.training');
		Route::get('{training}/learn', 'Employee\TrainingController@learn')->name('employee.training.learn');
        Route::get('/training/take-photo/{trainingParticipant}', 'Employee\TrainingController@takePhoto')->name('employee.training.take_photo');
        Route::post('/training/take-photo/save','Employee\TrainingController@takePhotoSave')->name('employee.training.save_take_photo');
	});


	/**
	 * 	Tracking
	 * */
	Route::prefix('tracking')->group(function(){
		Route::get('/', 'Employee\TrackingController@index')->name('employee.tracking');
		Route::get('get-location', 'Employee\TrackingController@getLocation')->name('employee.tracking.get_location');
		Route::get('{trackingLocation}/location-detail', 'Employee\TrackingController@trackingLocationDetail')->name('employee.tracking.location_detail');
		Route::get('{trackingLocation}/check-in', 'Employee\TrackingController@trackingCheckIn')->name('employee.tracking.check_in');
		Route::post('{trackingLocation}/check-in', 'Employee\TrackingController@trackingCheckInSave')->name('employee.tracking.save_check_in');
		Route::get('{trackingLocation}/check-day', 'Employee\TrackingController@trackingCheckDay')->name('employee.tracking.check_day');
		Route::post('{trackingLocation}/check-day', 'Employee\TrackingController@trackingCheckDaySave')->name('employee.tracking.save_check_day');
		Route::get('{trackingLocation}/check-out', 'Employee\TrackingController@trackingCheckOut')->name('employee.tracking.check_out');
		Route::post('{trackingLocation}/check-out', 'Employee\TrackingController@trackingCheckOutSave')->name('employee.tracking.save_check_out');
	});

	Route::prefix('company-rules')->group(function(){
		Route::get('/', 'Employee\CompanyRulesController@index')->name('employee.company_rules');
	});
});
