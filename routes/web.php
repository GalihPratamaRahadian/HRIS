<?php

if(appconfig('is_using_proxy'))
{
	URL::forceRootUrl(appconfig('proxy_url'));
	URL::forceScheme(appconfig('proxy_schema'));
}


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/**
*	Routes
*/
Route::redirect('/', 'login');

Route::get('privacy-policy', 'PublicPageController@privacyPolicy')->name('privacy_policy');
Route::get('terms-conditions', 'PublicPageController@termsConditions')->name('terms_conditions');

Route::prefix('access-register')->group(function(){
	Route::get('/', 'RegisterController@index')->name('access_register');

	Route::post('save', 'RegisterController@xhrSaveRegister')->name('save_access_register');
});


Route::group(['middleware' => 'cekLogin'], function(){

	Route::prefix('dashboard')->middleware('cekLogin')->group(function(){
		Route::get('/', 'DashboardController@index')->name('dashboard');
		Route::get('/dashHse', 'DashboardController@getHseData')->name('dash.hse');
		Route::get('/dashFrontSecurity', 'DashboardController@getFrontSecurityData')->name('dash.front.security');
		Route::get('/getHabisKontrak', 'DashboardController@getHabisKontrak')->name('get.habis.kontrak');
		Route::get('/getBelumAtur', 'DashboardController@getBelumAturKontrak')->name('get.belum.atur');
	});




	// EMPLOYEE
	Route::prefix('employee')->group(function(){
		Route::get('/', 'EmployeeController@employeeIndex')->name('employee')->middleware('checkPermission:employee,r');
		Route::get('create', 'EmployeeController@employeeCreate')->name('employee.create')->middleware('checkPermission:employee,c');
		Route::get('export', 'EmployeeController@employeeExport')->name('employee.export');
		Route::get('{employee}/edit', 'EmployeeController@employeeEdit')->name('employee.edit')->middleware('checkPermission:employee,u');
		Route::post('{employee}/inactive', 'EmployeeController@employeeSetInactive')->name('employee.inactive')->middleware('checkPermission:employee,u');
		Route::post('{employee}/active', 'EmployeeController@employeeSetActive')->name('employee.active')->middleware('checkPermission:employee,u');
		Route::get('{employee}/edit-user', 'EmployeeController@employeeEditUser')->name('employee.edit_user')->middleware('checkPermission:employee,u');
		Route::get('{employee}/change-photo', 'EmployeeController@employeeChangePhoto')->name('employee.change_photo')->middleware('checkPermission:employee,u');
		Route::get('{employee}/detail', 'EmployeeController@employeeDetail')->name('employee.detail');
		Route::get('{employee}/download-curriculum-vitae', 'EmployeeController@employeeCurriculumVitae')->name('employee.download_curriculum_vitae');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'EmployeeController@employeeStore')->name('employee.store')->middleware('checkPermission:employee,c');
			Route::get('options-get', 'EmployeeController@employeeOptionsGet')->name('employee.options_get');
			Route::put('{employee}/edit', 'EmployeeController@employeeUpdate')->name('employee.update')->middleware('checkPermission:employee,u');
			Route::put('{employee}/edit-user', 'EmployeeController@employeeUpdateUser')->name('employee.update_user')->middleware('checkPermission:employee,u');
			Route::post('{employee}/save-photo', 'EmployeeController@employeeSavePhoto')->name('employee.save_photo')->middleware('checkPermission:employee,u');
			Route::delete('{employee}/delete', 'EmployeeController@employeeDestroy')->name('employee.destroy')->middleware('checkPermission:employee,d');
			Route::post('{employee}/push_to_faceterminal', 'EmployeeController@xhrPushEmployeeToFaceTerminal')->name('employee.push_to_faceterminal');
			Route::post('push_all_to_faceterminal', 'EmployeeController@xhrPushAllEmployeeToFaceTerminal')->name('employee.push_all_to_faceterminal');
		});

		Route::prefix('{employee}/education')->middleware('checkPermission:employee,u')->group(function(){
			Route::get('/', 'EmployeeController@employeeEducationIndex')->name('employee_education');
			Route::get('create', 'EmployeeController@employeeEducationCreate')->name('employee_education.create');
			Route::get('{employeeEducation}/edit', 'EmployeeController@employeeEducationEdit')->name('employee_education.edit');
			Route::post('store', 'EmployeeController@employeeEducationStore')->name('employee_education.store');
			Route::put('{employeeEducation}/update', 'EmployeeController@employeeEducationUpdate')->name('employee_education.update');
			Route::delete('{employeeEducation}/destroy', 'EmployeeController@employeeEducationDestroy')->name('employee_education.destroy');
		});

		Route::prefix('{employee}/training')->middleware('checkPermission:employee,u')->group(function(){
			Route::get('/', 'EmployeeController@employeeTrainingIndex')->name('employee_training');
			Route::get('create', 'EmployeeController@employeeTrainingCreate')->name('employee_training.create');
			Route::get('{employeeTraining}/edit', 'EmployeeController@employeeTrainingEdit')->name('employee_training.edit');
			Route::post('store', 'EmployeeController@employeeTrainingStore')->name('employee_training.store');
			Route::put('{employeeTraining}/update', 'EmployeeController@employeeTrainingUpdate')->name('employee_training.update');
			Route::delete('{employeeTraining}/destroy', 'EmployeeController@employeeTrainingDestroy')->name('employee_training.destroy');
		});

		Route::prefix('{employee}/family')->middleware('checkPermission:employee,u')->group(function(){
			Route::get('/', 'EmployeeController@employeeFamilyIndex')->name('employee_family');
			Route::get('create', 'EmployeeController@employeeFamilyCreate')->name('employee_family.create');
			Route::get('{employeeFamily}/edit', 'EmployeeController@employeeFamilyEdit')->name('employee_family.edit');
			Route::post('store', 'EmployeeController@employeeFamilyStore')->name('employee_family.store');
			Route::put('{employeeFamily}/update', 'EmployeeController@employeeFamilyUpdate')->name('employee_family.update');
			Route::delete('{employeeFamily}/destroy', 'EmployeeController@employeeFamilyDestroy')->name('employee_family.destroy');
		});
	});


	// FACE TERMINAL DEVICE
	Route::prefix('face_terminal_device')->middleware('checkPermission:face_terminal_device,r')->group(function(){
		Route::get('/', 'DeviceController@faceTerminalDeviceIndex')->name('face_terminal_device');
		Route::get('create', 'DeviceController@faceTerminalDeviceCreate')->name('face_terminal_device.create')->middleware('checkPermission:face_terminal_device,c');
		Route::get('{faceTerminalDevice}/edit', 'DeviceController@faceTerminalDeviceEdit')->name('face_terminal_device.edit')->middleware('checkPermission:face_terminal_device,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'DeviceController@faceTerminalDeviceStore')->name('face_terminal_device.store')->middleware('checkPermission:face_terminal_device,c');
			Route::put('{faceTerminalDevice}/edit', 'DeviceController@faceTerminalDeviceUpdate')->name('face_terminal_device.update')->middleware('checkPermission:face_terminal_device,u');
			Route::delete('{faceTerminalDevice}/delete', 'DeviceController@faceTerminalDeviceDestroy')->name('face_terminal_device.destroy')->middleware('checkPermission:face_terminal_device,d');
		});
	});


	// OFF DAY
	Route::prefix('off-day')->middleware('checkPermission:off_day,r')->group(function(){
		Route::get('/', 'OffDayController@index')->name('off_day');
		Route::get('create', 'OffDayController@create')->name('off_day.create')->middleware('checkPermission:off_day,c');
		Route::get('{off_day}/edit', 'OffDayController@edit')->name('off_day.edit')->middleware('checkPermission:off_day,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'OffDayController@store')->name('off_day.store')->middleware('checkPermission:off_day,c');
			Route::put('{off_day}/edit', 'OffDayController@update')->name('off_day.update')->middleware('checkPermission:off_day,u');
			Route::delete('{off_day}/delete', 'OffDayController@destroy')->name('off_day.destroy')->middleware('checkPermission:off_day,d');
		});
	});



	// EMPLOYEE CONTRACT
	Route::prefix('employee_contract')->middleware('checkPermission:employee_contract,r')->group(function(){
		Route::get('/', 'EmployeeController@contractIndex')->name('employee_contract');
		Route::get('create', 'EmployeeController@contractCreate')->name('employee_contract.create')->middleware('checkPermission:employee_contract,c');
		Route::get('{employeeContract}/edit', 'EmployeeController@contractEdit')->name('employee_contract.edit')->middleware('checkPermission:employee_contract,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'EmployeeController@contractStore')->name('employee_contract.store')->middleware('checkPermission:employee_contract,c');
			Route::put('{employeeContract}/edit', 'EmployeeController@contractUpdate')->name('employee_contract.update')->middleware('checkPermission:employee_contract,u');
			Route::delete('{employeeContract}/delete', 'EmployeeController@contractDestroy')->name('employee_contract.destroy')->middleware('checkPermission:employee_contract,d');
		});
	});


	// EMPLOYEE SALARY
	Route::prefix('employee_salary')->middleware('checkPermission:employee_salary,r')->group(function(){
		Route::get('/', 'EmployeeController@salaryIndex')->name('employee_salary');
		Route::get('create', 'EmployeeController@salaryCreate')->name('employee_salary.create')->middleware('checkPermission:employee_salary,c');
		Route::get('{employeeSalary}/edit', 'EmployeeController@salaryEdit')->name('employee_salary.edit')->middleware('checkPermission:employee_salary,u');
		Route::get('{employeeSalary}/detail', 'EmployeeController@salaryDetail')->name('employee_salary.detail');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'EmployeeController@salaryStore')->name('employee_salary.store')->middleware('checkPermission:employee_salary,c');
			Route::put('{employeeSalary}/edit', 'EmployeeController@salaryUpdate')->name('employee_salary.update')->middleware('checkPermission:employee_salary,u');
			Route::delete('{employeeSalary}/delete', 'EmployeeController@salaryDestroy')->name('employee_salary.destroy')->middleware('checkPermission:employee_salary,d');
		});
	});


	// EMPLOYEE SHIFT CHANGE SCHEDULE
	Route::prefix('employee_shift_change_schedule')->middleware('checkPermission:employee_shift_change_schedule,r')->group(function(){
		Route::get('/', 'EmployeeController@shiftChangeScheduleIndex')->name('employee_shift_change_schedule');
		Route::get('create', 'EmployeeController@shiftChangeScheduleCreate')->name('employee_shift_change_schedule.create')->middleware('checkPermission:employee_shift_change_schedule,c');
		Route::get('{employeeShiftChangeSchedule}/edit', 'EmployeeController@shiftChangeScheduleEdit')->name('employee_shift_change_schedule.edit')->middleware('checkPermission:employee_shift_change_schedule,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'EmployeeController@shiftChangeScheduleStore')->name('employee_shift_change_schedule.store')->middleware('checkPermission:employee_shift_change_schedule,c');
			Route::put('{employeeShiftChangeSchedule}/edit', 'EmployeeController@shiftChangeScheduleUpdate')->name('employee_shift_change_schedule.update')->middleware('checkPermission:employee_shift_change_schedule,u');
			Route::delete('{employeeShiftChangeSchedule}/delete', 'EmployeeController@shiftChangeScheduleDestroy')->name('employee_shift_change_schedule.destroy')->middleware('checkPermission:employee_shift_change_schedule,d');
		});
	});


	// EMPLOYEE LEAVE QUOTA
	Route::prefix('employee_leave_quota')->middleware('checkPermission:employee_leave_quota,r')->group(function(){
		Route::get('/', 'EmployeeController@leaveQuotaIndex')->name('employee_leave_quota');
		Route::get('create', 'EmployeeController@leaveQuotaCreate')->name('employee_leave_quota.create')->middleware('checkPermission:employee_leave_quota,c');
		Route::get('export', 'EmployeeController@leaveQuotaExport')->name('employee_leave_quota.export');
		Route::get('{employeeLeaveQuota}/edit', 'EmployeeController@leaveQuotaEdit')->name('employee_leave_quota.edit')->middleware('checkPermission:employee_leave_quota,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'EmployeeController@leaveQuotaStore')->name('employee_leave_quota.store')->middleware('checkPermission:employee_leave_quota,c');
			Route::put('{employeeLeaveQuota}/edit', 'EmployeeController@leaveQuotaUpdate')->name('employee_leave_quota.update')->middleware('checkPermission:employee_leave_quota,u');
			Route::delete('{employeeLeaveQuota}/delete', 'EmployeeController@leaveQuotaDestroy')->name('employee_leave_quota.destroy')->middleware('checkPermission:employee_leave_quota,d');
		});
	});


	// Employee Unroutine Shift
	Route::prefix('unroutine-shift')->middleware('checkPermission:unroutine_shift,r')->group(function(){
		Route::get('/', 'EmployeeController@unroutineShiftIndex')->name('unroutine_shift')->middleware('checkPermission:unroutine_shift,r');
		Route::get('{employee}/employee-detail', 'EmployeeController@unroutineShiftEmployeeDetail')->name('unroutine_shift.employee_detail');
		Route::get('create', 'EmployeeController@unroutineShiftCreate')->name('unroutine_shift.create')->middleware('checkPermission:unroutine_shift,c');
		Route::get('{unroutineShift}/edit', 'EmployeeController@unroutineShiftEdit')->name('unroutine_shift.edit')->middleware('checkPermission:unroutine_shift,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'EmployeeController@unroutineShiftStore')->name('unroutine_shift.store')->middleware('checkPermission:unroutine_shift,c');
			Route::post('{employee}/import', 'EmployeeController@unroutineShiftImport')->name('unroutine_shift.import')->middleware('checkPermission:unroutine_shift,c');
			Route::put('{unroutineShift}/edit', 'EmployeeController@unroutineShiftUpdate')->name('unroutine_shift.update')->middleware('checkPermission:unroutine_shift,u');
			Route::delete('{unroutineShift}/delete', 'EmployeeController@unroutineShiftDestroy')->name('unroutine_shift.destroy')->middleware('checkPermission:unroutine_shift,d');
		});
	});


	// ATTENDANCE
	Route::prefix('attendance')->group(function(){
		Route::get('/', 'AttendanceController@index')->name('attendance');
		Route::get('{attendance}/detail', 'AttendanceController@detail')->name('attendance.detail');
		Route::get('{attendance}/edit', 'AttendanceController@edit')->name('attendance.edit')->middleware('checkPermission:attendance,r');
		Route::get('{attendance}/edit-clock-in-photo', 'AttendanceController@editClockInPhoto')->name('attendance.edit_clock_in_photo')->middleware('checkPermission:attendance,r');
		Route::get('export-to-pdf', 'AttendanceController@exportToPdf')->name('attendance.export_to_pdf');
		Route::get('create-attendances-summary', 'AttendanceController@createAttendancesSummary')->name('attendance.create_summary');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'AttendanceController@store')->name('attendance.store')->middleware('checkPermission:attendance,r');
			Route::put('{attendance}/edit', 'AttendanceController@update')->name('attendance.update')->middleware('checkPermission:attendance,r');
			Route::post('{attendance}/update-clock-in-photo', 'AttendanceController@updateClockInPhoto')->name('attendance.update_clock_in_photo')->middleware('checkPermission:attendance,r');
			Route::delete('{attendance}/delete', 'AttendanceController@destroy')->name('attendance.destroy')->middleware('checkPermission:attendance,r');
			Route::get('data', 'AttendanceController@xhrGetAttendanceData')->name('attendance.data');
			Route::post('generate-attendances-summary', 'AttendanceController@generateAttendancesSummary')->name('attendance.generate_summary');
			Route::post('{attendance}/send-clock-in-notification', 'AttendanceController@sendClockInNotification')->name('attendance.send_clock_in_notification')->middleware('checkPermission:attendance,r');
			Route::post('{attendance}/send-clock-out-notification', 'AttendanceController@sendClockOutNotification')->name('attendance.send_clock_out_notification')->middleware('checkPermission:attendance,r');
		});
	});


	// CHECK DAY
	Route::prefix('check-day')->group(function(){
		Route::get('/', 'Admin\CheckDayController@index')->name('check_day');
		Route::get('{checkDay}/detail', 'Admin\CheckDayController@detail')->name('check_day.detail');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::get('data', 'Admin\CheckDayController@xhrGetCheckDayData')->name('check_day.data');
			Route::delete('{checkDay}/delete', 'Admin\CheckDayController@destroy')->name('check_day.destroy')->middleware('checkPermission:attendance,r');
		});
	});


	// PAYROLL
	Route::prefix('payroll')->group(function(){
		Route::get('/', 'PayrollController@index')->name('payroll');
		Route::get('create', 'PayrollController@create')->name('payroll.create');
		Route::get('{payroll}/detail', 'PayrollController@detail')->name('payroll.detail');
		Route::get('{payroll}/edit', 'PayrollController@edit')->name('payroll.edit');
		Route::get('{payroll}/slip', 'PayrollController@slip')->name('payroll.slip');

		Route::post('xhr_chooose_period_and_employees', 'PayrollController@xhrChoosePeriodAndEmployeeForPayroll')->name('payroll.xhr_chooose_period_and_employees');
		Route::post('xhr_enter_nominal', 'PayrollController@xhrEnterNominalPayroll')->name('payroll.xhr_enter_nominal');
		Route::post('xhr_approve', 'PayrollController@xhrApprovePayroll')->name('payroll.xhr_approve');
		Route::delete('{payroll}/destroy', 'PayrollController@destroy')->name('payroll.destroy');
		Route::post('{payroll}/send', 'PayrollController@send')->name('payroll.send');
	});


	// SALARY SLIP
	Route::prefix('salary-slip')->middleware('checkPermission:salary_slip,r,yes')->group(function(){
		Route::get('/', 'Admin\SalarySlipController@index')->name('salary_slip');
		Route::get('create', 'Admin\SalarySlipController@create')->name('salary_slip.create')->middleware('checkPermission:salary_slip,c');
		Route::get('{salarySlip}/edit', 'Admin\SalarySlipController@edit')->name('salary_slip.edit')->middleware('checkPermission:salary_slip,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\SalarySlipController@store')->name('salary_slip.store')->middleware('checkPermission:salary_slip,c');
			Route::put('{salarySlip}/edit', 'Admin\SalarySlipController@update')->name('salary_slip.update')->middleware('checkPermission:salary_slip,u');
			Route::delete('{salarySlip}/delete', 'Admin\SalarySlipController@destroy')->name('salary_slip.destroy')->middleware('checkPermission:salary_slip,d');
		});
	});


	// LOG
	Route::prefix('face-terminal-log')->middleware('checkPermission:face_terminal_log,r')->group(function(){
		Route::get('/', 'LogController@index')->name('face_terminal_log');
	});


	// USER
	Route::prefix('user')->middleware('checkPermission:user,r')->group(function(){
		Route::get('/', 'UserController@index')->name('user');
		Route::get('create', 'UserController@create')->name('user.create')->middleware('checkPermission:user,c');
		Route::get('{user}/edit', 'UserController@edit')->name('user.edit')->middleware('checkPermission:user,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'UserController@store')->name('user.store')->middleware('checkPermission:user,c');
			Route::put('{user}/edit', 'UserController@update')->name('user.update')->middleware('checkPermission:user,u');
			Route::delete('{user}/delete', 'UserController@destroy')->name('user.destroy')->middleware('checkPermission:user,d');
		});
	});


	Route::prefix('api')->group(function(){
		Route::prefix('google-geolocation')->group(function(){
			Route::get('/', 'APISettingController@googleGeolocationIndex')->name('api.google_geolocation');
			Route::post('/', 'APISettingController@googleGeolocationSave')->name('api.save_google_geolocation');
		});
	});


	// SETTING ----------------------------------------------

	Route::prefix('setting')->group(function(){

		Route::prefix('app')->group(function(){
			Route::get('/', 'SettingController@app')->name('setting.app');
			Route::post('general', 'SettingController@xhrSaveGeneral')->name('setting.save_general');
			Route::post('notification-to-admin', 'SettingController@xhrSaveNotificationToAdmin')->name('setting.save_notification_to_admin');
			Route::post('elearning', 'SettingController@xhrSaveElearning')->name('setting.save_elearning');
			Route::post('attendance-web-mobile', 'SettingController@xhrSaveAttendanceWebMobile')->name('setting.save_attendance_web_mobile');
			Route::post('activate-menu', 'SettingController@xhrSaveActivateMenu')->name('setting.save_activate_menu');
			Route::post('data-storage', 'SettingController@xhrSaveDataStorage')->name('setting.save_data_storage');
			Route::post('api-integration', 'SettingController@xhrSaveApiIntegration')->name('setting.save_api_integration');
			Route::post('login-background', 'SettingController@xhrSaveLoginBackground')->name('setting.save_login_background');
			Route::post('developer', 'SettingController@xhrSaveDeveloper')->name('setting.save_developer');
		});

        Route::prefix('email')->group(function(){
            Route::get('/', 'SettingController@email')->name('setting.email');
            Route::post('save', 'SettingController@saveEmail')->name('setting.save_email');
        });

        Route::prefix('whatsapp')->group(function(){
            Route::get('/', 'SettingController@whatsapp')->name('setting.whatsapp');
            Route::post('save', 'SettingController@saveWhatsapp')->name('setting.save_whatsapp');
        });

		Route::get('clear', 'Admin\SettingController@clear')->name('admin.setting.clear');

		Route::prefix('profile')->group(function(){
			Route::get('/', 'SettingController@profile')->name('setting.profile');
			Route::post('save', 'SettingController@saveProfile')->name('setting.save_profile');
		});

		Route::prefix('password')->group(function(){
			Route::get('/', 'SettingController@password')->name('setting.password');
			Route::post('save', 'SettingController@savePassword')->name('setting.save_password');
		});


	});

	// END SETTING --------------------------------------------




	// Route::get('generate/gaji', 'PenggajianController@generate');


	Route::prefix('registration')->group(function(){
		Route::get('/', 'RegisterController@indexAdmin')->name('registration');
		Route::get('{registrant}/detail', 'RegisterController@detail')->name('registration.detail');
		Route::get('{registrant}/photo-rotate-to-left', 'RegisterController@photoRotateToLeft')->name('registration.photo_rotate_to_left');
		Route::get('{registrant}/photo-rotate-to-right', 'RegisterController@photoRotateToRight')->name('registration.photo_rotate_to_right');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::delete('{registrant}/delete', 'RegisterController@destroy')->name('registration.destroy');
			Route::post('{registrant}/approve', 'RegisterController@approve')->name('registration.approve');
			Route::post('{registrant}/reject', 'RegisterController@reject')->name('registration.reject');
			Route::post('{registrant}/reset-and-send', 'RegisterController@resetAndSend')->name('registration.reset_and_send');
		});
	});


	Route::prefix('profile')->group(function(){
		Route::get('/', 'RegisterController@editProfile')->name('profile');
		Route::post('/', 'RegisterController@saveProfile')->name('save_profile');
	});


	Route::prefix('attendance_location_rules')->middleware('checkPermission:attendance_location_rules,r')->group(function(){
		Route::get('/', 'AttendanceSettingController@attendanceLocationRulesIndex')->name('attendance_location_rules');
		Route::get('create', 'AttendanceSettingController@attendanceLocationRulesCreate')->name('attendance_location_rules.create')->middleware('checkPermission:attendance_location_rules,c');
		Route::get('{locationRules}/edit', 'AttendanceSettingController@attendanceLocationRulesEdit')->name('attendance_location_rules.edit')->middleware('checkPermission:attendance_location_rules,u');
		Route::get('{locationRules}/detail', 'AttendanceSettingController@attendanceLocationRulesDetail')->name('attendance_location_rules.detail');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'AttendanceSettingController@attendanceLocationRulesStore')->name('attendance_location_rules.store')->middleware('checkPermission:attendance_location_rules,c');
			Route::put('{locationRules}/edit', 'AttendanceSettingController@attendanceLocationRulesUpdate')->name('attendance_location_rules.update')->middleware('checkPermission:attendance_location_rules,u');
			Route::delete('{locationRules}/delete', 'AttendanceSettingController@attendanceLocationRulesDestroy')->name('attendance_location_rules.destroy')->middleware('checkPermission:attendance_location_rules,d');
		});
	});

	Route::prefix('web_attendance_permissions')->middleware('checkPermission:web_attendance_permissions,r')->group(function(){
		Route::get('/', 'AttendanceSettingController@webAttendancePermissionsIndex')->name('web_attendance_permissions');
		Route::get('create', 'AttendanceSettingController@webAttendancePermissionsCreate')->name('web_attendance_permissions.create')->middleware('checkPermission:web_attendance_permissions,c');
		Route::get('{webAttendancePermissions}/edit', 'AttendanceSettingController@webAttendancePermissionsEdit')->name('web_attendance_permissions.edit')->middleware('checkPermission:web_attendance_permissions,u');
		Route::get('{webAttendancePermissions}/detail', 'AttendanceSettingController@webAttendancePermissionsDetail')->name('web_attendance_permissions.detail');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'AttendanceSettingController@webAttendancePermissionsStore')->name('web_attendance_permissions.store')->middleware('checkPermission:web_attendance_permissions,c');
			Route::put('{webAttendancePermissions}/edit', 'AttendanceSettingController@webAttendancePermissionsUpdate')->name('web_attendance_permissions.update')->middleware('checkPermission:web_attendance_permissions,u');
			Route::delete('{webAttendancePermissions}/delete', 'AttendanceSettingController@webAttendancePermissionsDestroy')->name('web_attendance_permissions.destroy')->middleware('checkPermission:web_attendance_permissions,d');
		});
	});


	Route::prefix('notification')->group(function(){
		Route::get('/', 'NotificationController@index')->name('notification');
		Route::get('{notification}/detail', 'NotificationController@detail')->name('notification.detail');
	});


	Route::prefix('announcement')->group(function(){
		Route::get('/', 'AnnouncementController@index')->name('announcement');
		Route::get('create', 'AnnouncementController@create')->name('announcement.create')->middleware('checkPermission:announcement,c');
		Route::get('{announcement}/edit', 'AnnouncementController@edit')->name('announcement.edit')->middleware('checkPermission:announcement,u');
		Route::get('{announcement}/detail', 'AnnouncementController@detail')->name('announcement.detail');
		Route::post('{announcement}/send-broadcast', 'AnnouncementController@sendBroadcast')->name('announcement.send_broadcast');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'AnnouncementController@store')->name('announcement.store')->middleware('checkPermission:announcement,c');
			Route::put('{announcement}/edit', 'AnnouncementController@update')->name('announcement.update')->middleware('checkPermission:announcement,u');
			Route::delete('{announcement}/delete', 'AnnouncementController@destroy')->name('announcement.destroy')->middleware('checkPermission:announcement,d');
		});
	});


	Route::prefix('warning-letter')->middleware('checkPermission:warning_letter,r,yes')->group(function(){
		Route::get('/', 'WarningLetterController@index')->name('warning_letter');
		Route::get('create', 'WarningLetterController@create')->name('warning_letter.create')->middleware('checkPermission:warning_letter,c');
		Route::get('{warningLetter}/edit', 'WarningLetterController@edit')->name('warning_letter.edit')->middleware('checkPermission:warning_letter,u');
		Route::get('{warningLetter}/detail', 'WarningLetterController@detail')->name('warning_letter.detail');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'WarningLetterController@store')->name('warning_letter.store')->middleware('checkPermission:warning_letter,c');
			Route::put('{warningLetter}/edit', 'WarningLetterController@update')->name('warning_letter.update')->middleware('checkPermission:warning_letter,u');
			Route::delete('{warningLetter}/delete', 'WarningLetterController@destroy')->name('warning_letter.destroy')->middleware('checkPermission:warning_letter,d');
		});
	});


	Route::prefix('course')->middleware('checkPermission:course,r,yes')->group(function(){
		Route::get('/', 'ElearningController@courseIndex')->name('course');
		Route::get('create', 'ElearningController@courseCreate')->name('course.create')->middleware('checkPermission:course,c');
		Route::post('create-comment', 'ElearningController@courseCommentCreate')->name('course.comment.create');
		Route::post('{courseComment}/delete-comment', 'ElearningController@courseCommentDelete')->name('course.comment.delete')->middleware('checkPermission:course,d');
		Route::get('{course}/edit', 'ElearningController@courseEdit')->name('course.edit')->middleware('checkPermission:course,u');
		Route::get('{course}/detail', 'ElearningController@courseDetail')->name('course.detail');
		Route::get('{course}/learn', 'ElearningController@courseLearn')->name('course.learn');
		Route::get('{course}/learn', 'ElearningController@courseLearn')->name('course.learn');
		Route::get('{course}/exam', 'ElearningController@courseExam')->name('course.exam');
		Route::get('{course}/exam-answers', 'ElearningController@courseExamAnswers')->name('course.exam_answers');
		Route::get('{course}/exam-result', 'ElearningController@courseExamResult')->name('course.exam_result');
		Route::get('{course}/exam-result/{courseExamParticipant}/detail', 'ElearningController@courseExamResultDetail')->name('course.exam_result_detail');
		Route::post('{course}/exam-done', 'ElearningController@courseExamDone')->name('course.exam_done');
		Route::get('{course}/download-certificate', 'ElearningController@courseDownloadCertificate')->name('course.download_certificate');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'ElearningController@courseStore')->name('course.store')->middleware('checkPermission:course,c');
			Route::put('{course}/edit', 'ElearningController@courseUpdate')->name('course.update')->middleware('checkPermission:course,u');
			Route::delete('{course}/delete', 'ElearningController@courseDestroy')->name('course.destroy')->middleware('checkPermission:course,d');
			Route::post('{courseExamParticipantAnswer}/set-answer', 'ElearningController@setAnswer')->name('course.set_answer');
			Route::post('{course}/pass-video', 'ElearningController@coursePassVideo')->name('course.pass_video');
			Route::post('{course}/save-seconds-passed', 'ElearningController@courseSaveVideoSecondsPassed')->name('course.save_seconds_passed');
		});
	});


	Route::prefix('course-exam')->middleware('checkPermission:course_exam,r')->group(function(){
		Route::get('/', 'ElearningController@courseExamIndex')->name('course_exam');
		Route::get('create', 'ElearningController@courseExamCreate')->name('course_exam.create')->middleware('checkPermission:course_exam,c');
		Route::get('{courseExam}/edit', 'ElearningController@courseExamEdit')->name('course_exam.edit')->middleware('checkPermission:course_exam,u');
		Route::get('{courseExam}/detail', 'ElearningController@courseExamDetail')->name('course_exam.detail');
		Route::get('{courseExam}/get', 'ElearningController@courseExamGet')->name('course_exam.get');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'ElearningController@courseExamStore')->name('course_exam.store')->middleware('checkPermission:course_exam,c');
			Route::put('{courseExam}/edit', 'ElearningController@courseExamUpdate')->name('course_exam.update')->middleware('checkPermission:course_exam,u');
			Route::delete('{courseExam}/delete', 'ElearningController@courseExamDestroy')->name('course_exam.destroy')->middleware('checkPermission:course_exam,d');
		});
	});


	Route::prefix('course-result')->middleware('checkPermission:course_result,r')->group(function(){
		Route::get('/', 'ElearningController@courseResultIndex')->name('course_result');
		Route::get('export', 'ElearningController@courseResultExport')->name('course_result.export');
		Route::get('{courseParticipant}/detail', 'ElearningController@courseResultDetail')->name('course_result.detail');
	});

	Route::prefix('course-exam-history')->middleware('checkPermission:course_exam_history,r')->group(function(){
		Route::get('/', 'ElearningController@courseExamHistoryIndex')->name('course_exam_history');
		Route::get('{courseExamParticipant}/detail', 'ElearningController@courseExamHistoryDetail')->name('course_exam_history.detail');
	});


	Route::prefix('course-exam-question')->group(function(){
		Route::get('{courseExamQuestion}/get', 'ElearningController@courseExamQuestionGet')->name('course_exam_question.get');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'ElearningController@courseExamQuestionStore')->name('course_exam_question.store')->middleware('checkPermission:course_exam,u');
			Route::put('{courseExamQuestion}/edit', 'ElearningController@courseExamQuestionUpdate')->name('course_exam_question.update')->middleware('checkPermission:course_exam,u');
			Route::delete('{courseExamQuestion}/delete', 'ElearningController@courseExamQuestionDestroy')->name('course_exam_question.destroy')->middleware('checkPermission:course_exam,u');
		});
	});


	Route::prefix('course-exam-answer-option')->group(function(){
		Route::get('{courseExamAnswerOption}/get', 'ElearningController@courseExamAnswerOptionGet')->name('course_exam_answer_option.get');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'ElearningController@courseExamAnswerOptionStore')->name('course_exam_answer_option.store')->middleware('checkPermission:course_exam,u');
			Route::put('{courseExamAnswerOption}/edit', 'ElearningController@courseExamAnswerOptionUpdate')->name('course_exam_answer_option.update')->middleware('checkPermission:course_exam,u');
			Route::delete('{courseExamAnswerOption}/delete', 'ElearningController@courseExamAnswerOptionDestroy')->name('course_exam_answer_option.destroy')->middleware('checkPermission:course_exam,u');
		});
	});


	Route::prefix('face-compare')->middleware('checkPermission:face_compare,r')->group(function(){
		Route::get('/', 'FaceCompareController@index')->name('face_compare');
		Route::post('/', 'FaceCompareController@compare')->name('face_compare.compare');
	});


	Route::prefix('sales-employee')->group(function(){
		Route::get('/', 'TrackingFeatureController@salesEmployeeIndex')->name('sales_employee');
		Route::get('create', 'TrackingFeatureController@salesEmployeeCreate')->name('sales_employee.create');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'TrackingFeatureController@salesEmployeeStore')->name('sales_employee.store');
			Route::delete('{salesEmployee}/delete', 'TrackingFeatureController@salesEmployeeDestroy')->name('sales_employee.destroy');
		});
	});

	Route::prefix('sales-visit')->group(function(){
		Route::get('/', 'TrackingFeatureController@salesVisitIndex')->name('sales_visit');
		Route::get('{salesEmployee}/detail', 'TrackingFeatureController@salesVisitDetail')->name('sales_visit.detail');
	});

	Route::prefix('store')->group(function(){
		Route::get('/', 'TrackingFeatureController@storeIndex')->name('store');
		Route::get('{store}/edit', 'TrackingFeatureController@storeEdit')->name('store.edit');
		Route::get('{store}/detail', 'TrackingFeatureController@storeDetail')->name('store.detail');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::put('{store}/edit', 'TrackingFeatureController@storeUpdate')->name('store.update');
			Route::delete('{store}/delete', 'TrackingFeatureController@storeDestroy')->name('store.destroy');
			Route::post('{store}/set-active', 'TrackingFeatureController@storeSetActive')->name('store.set_active');
			Route::post('{store}/set-inactive', 'TrackingFeatureController@storeSetInactive')->name('store.set_inactive');
		});
	});

	Route::prefix('store-visit')->group(function(){
		Route::get('/', 'TrackingFeatureController@storeVisitIndex')->name('store_visit');
		Route::get('{storeVisit}/detail', 'TrackingFeatureController@storeVisitDetail')->name('store_visit.detail');
	});

});



Route::prefix('admin')->middleware([ 'cekLogin' ])->group(function(){


	/**
	 * 	Master Data
	 * */
	// Department
	Route::prefix('department')->middleware('checkPermission:department,r')->group(function(){
		Route::get('/', 'Admin\DepartmentController@index')->name('admin.department');
		Route::get('create', 'Admin\DepartmentController@create')->name('admin.department.create')->middleware('checkPermission:department,c');
		Route::get('{department}/detail', 'Admin\DepartmentController@detail')->name('admin.department.detail');
		Route::get('{department}/edit', 'Admin\DepartmentController@edit')->name('admin.department.edit')->middleware('checkPermission:department,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\DepartmentController@store')->name('admin.department.store')->middleware('checkPermission:department,c');
			Route::put('{department}/edit', 'Admin\DepartmentController@update')->name('admin.department.update')->middleware('checkPermission:department,u');
			Route::delete('{department}/delete', 'Admin\DepartmentController@destroy')->name('admin.department.destroy')->middleware('checkPermission:department,d');
		});
	});

	// Position
	Route::prefix('position')->middleware('checkPermission:position,r')->group(function(){
		Route::get('/', 'Admin\PositionController@index')->name('admin.position');
		Route::get('create', 'Admin\PositionController@create')->name('admin.position.create')->middleware('checkPermission:position,c');
		Route::get('{position}/detail', 'Admin\PositionController@detail')->name('admin.position.detail');
		Route::get('{position}/edit', 'Admin\PositionController@edit')->name('admin.position.edit')->middleware('checkPermission:position,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\PositionController@store')->name('admin.position.store')->middleware('checkPermission:position,c');
			Route::put('{position}/edit', 'Admin\PositionController@update')->name('admin.position.update')->middleware('checkPermission:position,u');
			Route::delete('{position}/delete', 'Admin\PositionController@destroy')->name('admin.position.destroy')->middleware('checkPermission:position,d');
		});
	});

	// Shift
	Route::prefix('shift')->middleware('checkPermission:shift,r')->group(function(){
		Route::get('/', 'Admin\ShiftController@index')->name('admin.shift');
		Route::get('create', 'Admin\ShiftController@create')->name('admin.shift.create')->middleware('checkPermission:shift,c');
		Route::get('{shift}/edit', 'Admin\ShiftController@edit')->name('admin.shift.edit')->middleware('checkPermission:shift,u');
		Route::get('{shift}/detail', 'Admin\ShiftController@detail')->name('admin.shift.detail')->middleware('checkPermission:shift,r');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\ShiftController@store')->name('admin.shift.store')->middleware('checkPermission:shift,c');
			Route::put('{shift}/edit', 'Admin\ShiftController@update')->name('admin.shift.update')->middleware('checkPermission:shift,u');
			Route::delete('{shift}/delete', 'Admin\ShiftController@destroy')->name('admin.shift.destroy')->middleware('checkPermission:shift,d');
		});
	});

	// Employee Group
	Route::prefix('employee-group')->middleware('checkPermission:employee_group,r')->group(function(){
		Route::get('/', 'Admin\EmployeeGroupController@index')->name('admin.employee_group');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\EmployeeGroupController@store')->name('admin.employee_group.store')->middleware('checkPermission:employee_group,c');
			Route::get('{employeeGroup}/get', 'Admin\EmployeeGroupController@get')->name('admin.employee_group.get');
			Route::put('{employeeGroup}/edit', 'Admin\EmployeeGroupController@update')->name('admin.employee_group.update')->middleware('checkPermission:employee_group,u');
			Route::delete('{employeeGroup}/delete', 'Admin\EmployeeGroupController@destroy')->name('admin.employee_group.destroy')->middleware('checkPermission:employee_group,d');
		});
	});

	// Leave Reason
	Route::prefix('leave-reason')->middleware('checkPermission:leave_reason,r')->group(function(){
		Route::get('/', 'Admin\MasterDataController@leaveReasonIndex')->name('admin.leave_reason');
		Route::get('create', 'Admin\MasterDataController@leaveReasonCreate')->name('admin.leave_reason.create')->middleware('checkPermission:leave_reason,c');
		Route::get('{leaveReason}/edit', 'Admin\MasterDataController@leaveReasonEdit')->name('admin.leave_reason.edit')->middleware('checkPermission:leave_reason,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\MasterDataController@leaveReasonStore')->name('admin.leave_reason.store')->middleware('checkPermission:leave_reason,c');
			Route::put('{leaveReason}/edit', 'Admin\MasterDataController@leaveReasonUpdate')->name('admin.leave_reason.update')->middleware('checkPermission:leave_reason,u');
			Route::delete('{leaveReason}/delete', 'Admin\MasterDataController@leaveReasonDestroy')->name('admin.leave_reason.destroy')->middleware('checkPermission:leave_reason,d');
		});
	});

	// Sick Reason
	Route::prefix('sick-reason')->middleware('checkPermission:sick_reason,r')->group(function(){
		Route::get('/', 'Admin\MasterDataController@sickReasonIndex')->name('admin.sick_reason');
		Route::get('create', 'Admin\MasterDataController@sickReasonCreate')->name('admin.sick_reason.create')->middleware('checkPermission:sick_reason,c');
		Route::get('{sickReason}/edit', 'Admin\MasterDataController@sickReasonEdit')->name('admin.sick_reason.edit')->middleware('checkPermission:sick_reason,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\MasterDataController@sickReasonStore')->name('admin.sick_reason.store')->middleware('checkPermission:sick_reason,c');
			Route::put('{sickReason}/edit', 'Admin\MasterDataController@sickReasonUpdate')->name('admin.sick_reason.update')->middleware('checkPermission:sick_reason,u');
			Route::delete('{sickReason}/delete', 'Admin\MasterDataController@sickReasonDestroy')->name('admin.sick_reason.destroy')->middleware('checkPermission:sick_reason,d');
		});
	});

	// Necessity Reason
	Route::prefix('necessity-reason')->middleware('checkPermission:necessity_reason,r')->group(function(){
		Route::get('/', 'Admin\MasterDataController@necessityReasonIndex')->name('admin.necessity_reason');
		Route::get('create', 'Admin\MasterDataController@necessityReasonCreate')->name('admin.necessity_reason.create')->middleware('checkPermission:necessity_reason,c');
		Route::get('{necessityReason}/edit', 'Admin\MasterDataController@necessityReasonEdit')->name('admin.necessity_reason.edit')->middleware('checkPermission:necessity_reason,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\MasterDataController@necessityReasonStore')->name('admin.necessity_reason.store')->middleware('checkPermission:necessity_reason,c');
			Route::put('{necessityReason}/edit', 'Admin\MasterDataController@necessityReasonUpdate')->name('admin.necessity_reason.update')->middleware('checkPermission:necessity_reason,u');
			Route::delete('{necessityReason}/delete', 'Admin\MasterDataController@necessityReasonDestroy')->name('admin.necessity_reason.destroy')->middleware('checkPermission:necessity_reason,d');
		});
	});

	// Overtime Reason
	Route::prefix('overtime-reason')->middleware('checkPermission:overtime_reason,r')->group(function(){
		Route::get('/', 'Admin\MasterDataController@overtimeReasonIndex')->name('admin.overtime_reason');
		Route::get('create', 'Admin\MasterDataController@overtimeReasonCreate')->name('admin.overtime_reason.create')->middleware('checkPermission:overtime_reason,c');
		Route::get('{overtimeReason}/edit', 'Admin\MasterDataController@overtimeReasonEdit')->name('admin.overtime_reason.edit')->middleware('checkPermission:overtime_reason,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\MasterDataController@overtimeReasonStore')->name('admin.overtime_reason.store')->middleware('checkPermission:overtime_reason,c');
			Route::put('{overtimeReason}/edit', 'Admin\MasterDataController@overtimeReasonUpdate')->name('admin.overtime_reason.update')->middleware('checkPermission:overtime_reason,u');
			Route::delete('{overtimeReason}/delete', 'Admin\MasterDataController@overtimeReasonDestroy')->name('admin.overtime_reason.destroy')->middleware('checkPermission:overtime_reason,d');
		});
	});

	/**
	 * 	End Master Data
	 * */


	// Employee Leave
	Route::prefix('employee-leave')->middleware('checkPermission:employee_leave,r')->group(function(){
		Route::get('/', 'Admin\EmployeeLeaveController@index')->name('admin.employee_leave');
		Route::get('create', 'Admin\EmployeeLeaveController@create')->name('admin.employee_leave.create')->middleware('checkPermission:employee_leave,c');
		Route::get('{employeeLeave}/edit', 'Admin\EmployeeLeaveController@edit')->name('admin.employee_leave.edit')->middleware('checkPermission:employee_leave,u');
		Route::get('{employeeLeave}/detail', 'Admin\EmployeeLeaveController@detail')->name('admin.employee_leave.detail');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\EmployeeLeaveController@store')->name('admin.employee_leave.store')->middleware('checkPermission:employee_leave,c');
			Route::put('{employeeLeave}/edit', 'Admin\EmployeeLeaveController@update')->name('admin.employee_leave.update')->middleware('checkPermission:employee_leave,u');
			Route::delete('{employeeLeave}/delete', 'Admin\EmployeeLeaveController@destroy')->name('admin.employee_leave.destroy')->middleware('checkPermission:employee_leave,d');
		});
	});


	// Company Rule
	Route::prefix('company-rules')->group(function(){
		Route::get('/', 'Admin\CompanyRulesController@index')->name('admin.company_rules');
		Route::post('save', 'Admin\CompanyRulesController@save')->name('admin.company_rules.save');
	});



	// Leave Submission
	Route::prefix('leave-submission')->middleware('checkPermission:leave_submission,r')->group(function(){
		Route::get('/', 'Admin\LeaveSubmissionController@index')->name('admin.leave_submission');
		Route::get('create', 'Admin\LeaveSubmissionController@create')->name('admin.leave_submission.create')->middleware('checkPermission:leave_submission,c');
		Route::get('{leaveSubmission}/detail', 'Admin\LeaveSubmissionController@detail')->name('admin.leave_submission.detail');
		Route::get('{leaveSubmission}/edit', 'Admin\LeaveSubmissionController@edit')->name('admin.leave_submission.edit')->middleware('checkPermission:leave_submission,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\LeaveSubmissionController@store')->name('admin.leave_submission.store')->middleware('checkPermission:leave_submission,c');
			Route::post('{leaveSubmission}/approve', 'Admin\LeaveSubmissionController@approve')->name('admin.leave_submission.approve')->middleware('checkPermission:leave_submission,a');
			Route::post('{leaveSubmission}/reject', 'Admin\LeaveSubmissionController@reject')->name('admin.leave_submission.reject')->middleware('checkPermission:leave_submission,a');
			Route::post('{leaveSubmission}/cancel', 'Admin\LeaveSubmissionController@cancel')->name('admin.leave_submission.cancel')->middleware('checkPermission:leave_submission,a');
			Route::delete('{leaveSubmission}/delete', 'Admin\LeaveSubmissionController@destroy')->name('admin.leave_submission.destroy')->middleware('checkPermission:leave_submission,d');
            Route::post('{approval}/resend-broadcast', 'Admin\LeaveSubmissionController@resendBroadcastToApproval')->name('admin.leave_submission.resend_broadcast');
		});
	});



	// Sick Necessity Submission
	Route::prefix('sick-necessity-submission')->middleware('checkPermission:sick_necessity_submission,r')->group(function(){
		Route::get('/', 'Admin\SickNecessitySubmissionController@index')->name('admin.sick_necessity_submission');
		Route::get('create', 'Admin\SickNecessitySubmissionController@create')->name('admin.sick_necessity_submission.create')->middleware('checkPermission:sick_necessity_submission,c');
		Route::get('{sickNecessitySubmission}/detail', 'Admin\SickNecessitySubmissionController@detail')->name('admin.sick_necessity_submission.detail');
		Route::get('{sickNecessitySubmission}/edit', 'Admin\SickNecessitySubmissionController@edit')->name('admin.sick_necessity_submission.edit')->middleware('checkPermission:sick_necessity_submission,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\SickNecessitySubmissionController@store')->name('admin.sick_necessity_submission.store')->middleware('checkPermission:sick_necessity_submission,c');
			Route::post('{sickNecessitySubmission}/approve', 'Admin\SickNecessitySubmissionController@approve')->name('admin.sick_necessity_submission.approve')->middleware('checkPermission:sick_necessity_submission,a');
			Route::post('{sickNecessitySubmission}/reject', 'Admin\SickNecessitySubmissionController@reject')->name('admin.sick_necessity_submission.reject')->middleware('checkPermission:sick_necessity_submission,a');
			Route::post('{sickNecessitySubmission}/cancel', 'Admin\SickNecessitySubmissionController@cancel')->name('admin.sick_necessity_submission.cancel')->middleware('checkPermission:sick_necessity_submission,a');
			Route::delete('{sickNecessitySubmission}/delete', 'Admin\SickNecessitySubmissionController@destroy')->name('admin.sick_necessity_submission.destroy')->middleware('checkPermission:sick_necessity_submission,d');
            Route::post('{approval}/resend-broadcast', 'Admin\SickNecessitySubmissionController@resendBroadcastToApproval')->name('admin.sick_necessity_submission.resend_broadcast');
		});
	});


	// Attendance Permission Submission
	Route::prefix('attendance-permission-submission')->middleware('checkPermission:attendance_permission_submission,r')->group(function(){
		Route::get('/', 'Admin\AttendancePermissionSubmissionController@index')->name('admin.attendance_permission_submission');
		Route::get('create', 'Admin\AttendancePermissionSubmissionController@create')->name('admin.attendance_permission_submission.create')->middleware('checkPermission:attendance_permission_submission,c');
		Route::get('{attendancePermissionSubmission}/detail', 'Admin\AttendancePermissionSubmissionController@detail')->name('admin.attendance_permission_submission.detail');
		Route::get('{attendancePermissionSubmission}/edit', 'Admin\AttendancePermissionSubmissionController@edit')->name('admin.attendance_permission_submission.edit')->middleware('checkPermission:attendance_permission_submission,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\AttendancePermissionSubmissionController@store')->name('admin.attendance_permission_submission.store')->middleware('checkPermission:attendance_permission_submission,c');
			Route::post('{attendancePermissionSubmission}/approve', 'Admin\AttendancePermissionSubmissionController@approve')->name('admin.attendance_permission_submission.approve')->middleware('checkPermission:attendance_permission_submission,a');
			Route::post('{attendancePermissionSubmission}/reject', 'Admin\AttendancePermissionSubmissionController@reject')->name('admin.attendance_permission_submission.reject')->middleware('checkPermission:attendance_permission_submission,a');
			Route::post('{attendancePermissionSubmission}/cancel', 'Admin\AttendancePermissionSubmissionController@cancel')->name('admin.attendance_permission_submission.cancel')->middleware('checkPermission:attendance_permission_submission,a');
			Route::delete('{attendancePermissionSubmission}/delete', 'Admin\AttendancePermissionSubmissionController@destroy')->name('admin.attendance_permission_submission.destroy')->middleware('checkPermission:attendance_permission_submission,d');
            Route::post('{approval}/resend-broadcast', 'Admin\AttendancePermissionSubmissionController@resendBroadcastToApproval')->name('admin.attendance_permission_submission.resend_broadcast');
		});
	});


	// Overtime Submission
	Route::prefix('overtime-submission')->middleware('checkPermission:overtime_submission,r')->group(function(){
		Route::get('/', 'Admin\OvertimeSubmissionController@index')->name('admin.overtime_submission');
		Route::get('create', 'Admin\OvertimeSubmissionController@create')->name('admin.overtime_submission.create')->middleware('checkPermission:overtime_submission,c');
		Route::get('{overtimeSubmission}/detail', 'Admin\OvertimeSubmissionController@detail')->name('admin.overtime_submission.detail');
		Route::get('{overtimeSubmission}/edit', 'Admin\OvertimeSubmissionController@edit')->name('admin.overtime_submission.edit')->middleware('checkPermission:overtime_submission,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\OvertimeSubmissionController@store')->name('admin.overtime_submission.store')->middleware('checkPermission:overtime_submission,c');
			Route::post('{overtimeSubmission}/approve', 'Admin\OvertimeSubmissionController@approve')->name('admin.overtime_submission.approve')->middleware('checkPermission:overtime_submission,a');
			Route::post('{overtimeSubmission}/reject', 'Admin\OvertimeSubmissionController@reject')->name('admin.overtime_submission.reject')->middleware('checkPermission:overtime_submission,a');
			Route::post('{overtimeSubmission}/cancel', 'Admin\OvertimeSubmissionController@cancel')->name('admin.overtime_submission.cancel')->middleware('checkPermission:overtime_submission,a');
			Route::delete('{overtimeSubmission}/delete', 'Admin\OvertimeSubmissionController@destroy')->name('admin.overtime_submission.destroy')->middleware('checkPermission:overtime_submission,d');
            Route::post('{approval}/resend-broadcast', 'Admin\OvertimeSubmissionController@resendBroadcastToApproval')->name('admin.overtime_submission.resend_broadcast');
        });
	});


	// COURSE
	Route::prefix('course')->middleware('checkPermission:course,r,yes')->group(function(){
		Route::get('/', 'Admin\ElearningController@courseIndex')->name('admin.course');
		Route::get('create', 'Admin\ElearningController@courseCreate')->name('admin.course.create')->middleware('checkPermission:course,c');
		Route::post('create-comment', 'Admin\ElearningController@courseCommentCreate')->name('admin.course.comment.create');
		Route::post('{courseComment}/delete-comment', 'Admin\ElearningController@courseCommentDelete')->name('admin.course.comment.delete')->middleware('checkPermission:course,d');
		Route::get('{course}/edit', 'Admin\ElearningController@courseEdit')->name('admin.course.edit')->middleware('checkPermission:course,u');
		Route::get('{course}/detail', 'Admin\ElearningController@courseDetail')->name('admin.course.detail');
		Route::get('{course}/learn', 'Admin\ElearningController@courseLearn')->name('admin.course.learn');
		Route::get('{course}/learn', 'Admin\ElearningController@courseLearn')->name('admin.course.learn');
		Route::get('{course}/exam', 'Admin\ElearningController@courseExam')->name('admin.course.exam');
		Route::get('{course}/exam-answers', 'Admin\ElearningController@courseExamAnswers')->name('admin.course.exam_answers');
		Route::get('{course}/exam-result', 'Admin\ElearningController@courseExamResult')->name('admin.course.exam_result');
		Route::get('{course}/exam-result/{courseExamParticipant}/detail', 'Admin\ElearningController@courseExamResultDetail')->name('admin.course.exam_result_detail');
		Route::post('{course}/exam-done', 'Admin\ElearningController@courseExamDone')->name('admin.course.exam_done');
		Route::get('{course}/download-certificate', 'Admin\ElearningController@courseDownloadCertificate')->name('admin.course.download_certificate');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\ElearningController@courseStore')->name('admin.course.store')->middleware('checkPermission:course,c');
			Route::put('{course}/edit', 'Admin\ElearningController@courseUpdate')->name('admin.course.update')->middleware('checkPermission:course,u');
			Route::delete('{course}/delete', 'Admin\ElearningController@courseDestroy')->name('admin.course.destroy')->middleware('checkPermission:course,d');
			Route::post('{courseExamParticipantAnswer}/set-answer', 'Admin\ElearningController@setAnswer')->name('admin.course.set_answer');
			Route::post('{course}/pass-video', 'Admin\ElearningController@coursePassVideo')->name('admin.course.pass_video');
			Route::post('{course}/save-seconds-passed', 'Admin\ElearningController@courseSaveVideoSecondsPassed')->name('admin.course.save_seconds_passed');
		});
	});


	Route::prefix('course-exam')->middleware('checkPermission:course_exam,r')->group(function(){
		Route::get('/', 'Admin\ElearningController@courseExamIndex')->name('admin.course_exam');
		Route::get('create', 'Admin\ElearningController@courseExamCreate')->name('admin.course_exam.create')->middleware('checkPermission:course_exam,c');
		Route::get('{courseExam}/edit', 'Admin\ElearningController@courseExamEdit')->name('admin.course_exam.edit')->middleware('checkPermission:course_exam,u');
		Route::get('{courseExam}/detail', 'Admin\ElearningController@courseExamDetail')->name('admin.course_exam.detail');
		Route::get('{courseExam}/get', 'Admin\ElearningController@courseExamGet')->name('admin.course_exam.get');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\ElearningController@courseExamStore')->name('admin.course_exam.store')->middleware('checkPermission:course_exam,c');
			Route::put('{courseExam}/edit', 'Admin\ElearningController@courseExamUpdate')->name('admin.course_exam.update')->middleware('checkPermission:course_exam,u');
			Route::delete('{courseExam}/delete', 'Admin\ElearningController@courseExamDestroy')->name('admin.course_exam.destroy')->middleware('checkPermission:course_exam,d');
		});
	});


	Route::prefix('course-result')->middleware('checkPermission:course_result,r')->group(function(){
		Route::get('/', 'Admin\ElearningController@courseResultIndex')->name('admin.course_result');
		Route::get('export', 'Admin\ElearningController@courseResultExport')->name('admin.course_result.export');
		Route::get('{courseParticipant}/detail', 'Admin\ElearningController@courseResultDetail')->name('admin.course_result.detail');
	});

	Route::prefix('course-exam-history')->middleware('checkPermission:course_exam_history,r')->group(function(){
		Route::get('/', 'Admin\ElearningController@courseExamHistoryIndex')->name('admin.course_exam_history');
		Route::get('{courseExamParticipant}/detail', 'Admin\ElearningController@courseExamHistoryDetail')->name('admin.course_exam_history.detail');
	});


	Route::prefix('course-exam-question')->group(function(){
		Route::get('{courseExamQuestion}/get', 'Admin\ElearningController@courseExamQuestionGet')->name('admin.course_exam_question.get');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\ElearningController@courseExamQuestionStore')->name('admin.course_exam_question.store')->middleware('checkPermission:course_exam,u');
			Route::put('{courseExamQuestion}/edit', 'Admin\ElearningController@courseExamQuestionUpdate')->name('admin.course_exam_question.update')->middleware('checkPermission:course_exam,u');
			Route::delete('{courseExamQuestion}/delete', 'Admin\ElearningController@courseExamQuestionDestroy')->name('admin.course_exam_question.destroy')->middleware('checkPermission:course_exam,u');
		});
	});


	Route::prefix('course-exam-answer-option')->group(function(){
		Route::get('{courseExamAnswerOption}/get', 'Admin\ElearningController@courseExamAnswerOptionGet')->name('admin.course_exam_answer_option.get');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\ElearningController@courseExamAnswerOptionStore')->name('admin.course_exam_answer_option.store')->middleware('checkPermission:course_exam,u');
			Route::put('{courseExamAnswerOption}/edit', 'Admin\ElearningController@courseExamAnswerOptionUpdate')->name('admin.course_exam_answer_option.update')->middleware('checkPermission:course_exam,u');
			Route::delete('{courseExamAnswerOption}/delete', 'Admin\ElearningController@courseExamAnswerOptionDestroy')->name('admin.course_exam_answer_option.destroy')->middleware('checkPermission:course_exam,u');
		});
	});


	/**
	 * 	Training
	 * */
	Route::prefix('training')->middleware('checkPermission:training,r')->group(function(){
		Route::get('/', 'Admin\TrainingController@index')->name('admin.training');
		Route::get('create', 'Admin\TrainingController@create')->name('admin.training.create')->middleware('checkPermission:training,c');
		Route::get('{training}/edit', 'Admin\TrainingController@edit')->name('admin.training.edit')->middleware('checkPermission:training,u');
		Route::get('{training}/detail', 'Admin\TrainingController@detail')->name('admin.training.detail')->middleware('checkPermission:training,r');
        Route::get('export', 'Admin\TrainingController@trainingParticipantExport')->name('admin.training.export');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\TrainingController@store')->name('admin.training.store')->middleware('checkPermission:training,c');
			Route::put('{training}/edit', 'Admin\TrainingController@update')->name('admin.training.update')->middleware('checkPermission:training,u');
			Route::delete('{training}/destroy', 'Admin\TrainingController@destroy')->name('admin.training.destroy')->middleware('checkPermission:training,d');
		});
	});



	/**
	 * 	Leave Resume
	 * */
	Route::prefix('leave-resume')->middleware('checkPermission:leave_resume,r')->group(function(){
		Route::get('/', 'Admin\LeaveResumeController@index')->name('admin.leave_resume');
		Route::get('generate', 'Admin\LeaveResumeController@generate')->name('admin.leave_resume.generate');
	});

	/**
	 * 	Sick Necessity Resume
	 * */
	Route::prefix('sick-necessity-resume')->middleware('checkPermission:sick_necessity_resume,r')->group(function(){
		Route::get('/', 'Admin\SickNecessityResumeController@index')->name('admin.sick_necessity_resume');
		Route::get('generate', 'Admin\SickNecessityResumeController@generate')->name('admin.sick_necessity_resume.generate');
	});


	/**
	 * 	Overtime Resume
	 * */
	Route::prefix('overtime-resume')->middleware('checkPermission:overtime_resume,r')->group(function(){
		Route::get('/', 'Admin\OvertimeResumeController@index')->name('admin.overtime_resume');
		Route::get('generate', 'Admin\OvertimeResumeController@generate')->name('admin.overtime_resume.generate');
	});


	/**
	 * 	Payroll Resume
	 * */
	Route::prefix('payroll-resume')->middleware('checkPermission:payroll_resume,r')->group(function(){
		Route::get('/', 'Admin\PayrollResumeController@index')->name('admin.payroll_resume');
		Route::get('generate', 'Admin\PayrollResumeController@generate')->name('admin.payroll_resume.generate');
	});


	/**
	 * 	Tracking
	 * */
	// Tracking Location
	Route::prefix('tracking-location')->middleware('checkPermission:tracking_location,r')->group(function(){
		Route::get('/', 'Admin\TrackingController@trackingLocationIndex')->name('admin.tracking_location');
		Route::get('create', 'Admin\TrackingController@trackingLocationCreate')->name('admin.tracking_location.create')->middleware('checkPermission:tracking_location,c');
		Route::get('{tracking_location}/edit', 'Admin\TrackingController@trackingLocationEdit')->name('admin.tracking_location.edit')->middleware('checkPermission:tracking_location,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\TrackingController@trackingLocationStore')->name('admin.tracking_location.store')->middleware('checkPermission:tracking_location,c');
			Route::post('import', 'Admin\TrackingController@trackingLocationImport')->name('admin.tracking_location.import')->middleware('checkPermission:tracking_location,c');
			Route::put('{tracking_location}/edit', 'Admin\TrackingController@trackingLocationUpdate')->name('admin.tracking_location.update')->middleware('checkPermission:tracking_location,u');
			Route::delete('{tracking_location}/delete', 'Admin\TrackingController@trackingLocationDestroy')->name('admin.tracking_location.destroy')->middleware('checkPermission:tracking_location,d');
		});
	});


	// Tracking Employee
	Route::prefix('tracking-employee')->middleware('checkPermission:tracking_employee,r')->group(function(){
		Route::get('/', 'Admin\TrackingController@trackingEmployeeIndex')->name('admin.tracking_employee');
		Route::get('create', 'Admin\TrackingController@trackingEmployeeCreate')->name('admin.tracking_employee.create')->middleware('checkPermission:tracking_employee,c');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\TrackingController@trackingEmployeeStore')->name('admin.tracking_employee.store')->middleware('checkPermission:tracking_employee,c');
			Route::delete('{tracking_employee}/delete', 'Admin\TrackingController@trackingEmployeeDestroy')->name('admin.tracking_employee.destroy')->middleware('checkPermission:tracking_employee,d');
		});
	});


	// Tracking
	Route::prefix('tracking')->middleware('checkPermission:tracking,r')->group(function(){
		Route::get('/', 'Admin\TrackingController@trackingIndex')->name('admin.tracking');
		Route::get('{tracking}/detail', 'Admin\TrackingController@trackingDetail')->name('admin.tracking.detail');
	});

	// Send Message To Employee
	Route::prefix('send-message-to-employee')->group(function(){
		Route::get('/', 'Admin\SendMessageToEmployeeController@index')->name('admin.send_message_to_employee');
		Route::post('/', 'Admin\SendMessageToEmployeeController@send')->name('admin.send_message_to_employee.send');
	});

	// Mobile App Notification
	Route::prefix('mobile-app-notification')->middleware('checkPermission:mobile_app_notification,r')->group(function(){
		Route::get('/', 'Admin\MobileAppNotificationController@index')->name('admin.mobile_app_notification');
		Route::get('create', 'Admin\MobileAppNotificationController@create')->name('admin.mobile_app_notification.create')->middleware('checkPermission:mobile_app_notification,c');
		Route::get('{mobileAppNotification}/detail', 'Admin\MobileAppNotificationController@detail')->name('admin.mobile_app_notification.detail');
		Route::get('{mobileAppNotification}/edit', 'Admin\MobileAppNotificationController@edit')->name('admin.mobile_app_notification.edit')->middleware('checkPermission:mobile_app_notification,u');

		Route::group([ 'middleware' => 'requestAjax' ], function(){
			Route::post('create', 'Admin\MobileAppNotificationController@store')->name('admin.mobile_app_notification.store')->middleware('checkPermission:mobile_app_notification,c');
			Route::put('{mobileAppNotification}/edit', 'Admin\MobileAppNotificationController@update')->name('admin.mobile_app_notification.update')->middleware('checkPermission:mobile_app_notification,u');
			Route::delete('{mobileAppNotification}/delete', 'Admin\MobileAppNotificationController@destroy')->name('admin.mobile_app_notification.destroy')->middleware('checkPermission:mobile_app_notification,d');
		});
	});
});


Route::prefix('developer')->group(function(){
	Route::prefix('face-terminal-log')->group(function(){
		Route::get('/', 'Developer\FaceTerminalLogController@index')->name('developer.face_terminal_log');
		Route::get('{employee}/set-log-to-attendance/{clockInLog}/{clockOutLog}', 'Developer\FaceTerminalLogController@setLogToAttendance');
	});
	Route::get('employee/{employee}/work-time/{start}/{end}', 'Developer\FaceTerminalLogController@getWorkTimeByDateRange');
	Route::get('set-manually', 'Developer\FaceTerminalLogController@setManually')->name('developer.set_manually');

	Route::prefix('daily-shift-resume')->group(function(){
		Route::get('/', 'Developer\DailyShiftResumeController@index')->name('developer.daily_shift_resume');
	});
});



Route::prefix('helper')->group(function(){

	Route::get('map-generate', 'HelperController@mapGenerator')->name('helper.map_generate');
	Route::get('get-positions', 'HelperController@getPositions')->name('helper.get_positions');
	Route::get('get-employees', 'HelperController@getEmployees')->name('helper.get_employees');
	Route::get('import-templates/{filename}', 'HelperController@importTemplates')->name('helper.import_templates');

});

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout');

Route::redirect('home', 'dashboard');
Route::post('/cekUserForLogin', 'UserController@cekUser')->name('cek.user');

Route::prefix('recent')->group(function(){
	Route::get('/', 'RecentController@index');
	Route::get('get/{start}/{end}', 'RecentController@xhrGetLogs');
	Route::get('detail/{faceTerminalLog}', 'RecentController@detail');
	Route::get('latest', 'RecentController@xhrGetLatest');
});

Route::prefix('helper')->group(function(){

	Route::prefix('recent')->group(function(){
		Route::get('info', 'HelperController@recentInfo')->name('helper.recent.info');
	});
});

Route::get('login-using-id/{userId}', 'DashboardController@loginUsingId');

Route::get('resume/{employee}', 'HelperController@resume');
Route::get('fixing', 'HelperController@fixing');
Route::get('unserialize/{filename}', 'HelperController@unserialize');

Route::get('test-course', function(){
	$participants = \App\Models\CourseParticipant::all();
	foreach($participants as $p) {
		$p->createEmployeeTraining();
	}
});
