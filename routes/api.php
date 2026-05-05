<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::get('test', 'FaceTerminalController@test');

// Route::post('visitor', 'FaceTerminalController@visitor');

// Route::post('convert', 'FaceTerminalController@convert');

// Route::post('faceterminal-event', 'ApiController@faceTerminalEvent');

// Route::post('gate/site', 'FaceTerminalController@event');
// Route::post('gate/attendance', 'FaceTerminalController@gateAttendance');


/**
*		FACE REGISTER
*/

Route::post('faceterminal-event', 'ApiController@faceTerminalEvent');

Route::get('department', 'ApiController@getDepartment');
Route::get('department/{department}', 'ApiController@findDepartment');

Route::get('position', 'ApiController@getPosition');
Route::get('position/{position}', 'ApiController@findPosition');

Route::post('employee-create', 'ApiController@createEmployee');


Route::prefix('webhook')->group(function(){
	Route::post('event', 'ApiController@eventSecond');
	Route::post('stranger', 'ApiController@strangerEvent');
});

Route::prefix('get')->group(function(){
	Route::prefix('device')->group(function(){
		Route::get('/', 'ApiController@getDevices');
	});
});

Route::post('whatsapp', 'ApiController@whatsapp');



/***
 * 
 * 		MOBILE APP
 * 
 * */


Route::prefix('mobile-app')->group(function(){
	Route::post('login', 'MobileApp\LoginController@index');

	Route::group([ 'middleware' => 'checkToken' ], function(){

		// Home
		Route::post('get-dashboard', 'MobileApp\HomeController@dashboard');

		// Attendance
		Route::prefix('attendance')->group(function(){
			Route::post('clock-in', 'MobileApp\AttendanceController@setClockIn');
			Route::post('clock-out', 'MobileApp\AttendanceController@setClockOut');
			Route::post('list', 'MobileApp\AttendanceController@list');
			Route::post('detail', 'MobileApp\AttendanceController@detail');
		});

		// Tracking Sales
		Route::prefix('tracking-sales')->group(function(){
			Route::post('create-store', 'MobileApp\TrackingSalesController@createStore');
			Route::post('list-store', 'MobileApp\TrackingSalesController@listStore');
			Route::post('get-store', 'MobileApp\TrackingSalesController@getStore');
			Route::post('get-store-visit', 'MobileApp\TrackingSalesController@getStoreVisit');
			Route::post('check-in-store', 'MobileApp\TrackingSalesController@checkInStore');
		});

		// Announcement
		Route::prefix('announcement')->group(function(){
			Route::post('list', 'MobileApp\AnnouncementController@list');
			Route::post('detail', 'MobileApp\AnnouncementController@detail');
		});

		// Pengajuan Cuti
		Route::prefix('leave-submission')->group(function(){
			Route::post('list', 'MobileApp\LeaveSubmissionController@list');
			Route::post('detail', 'MobileApp\LeaveSubmissionController@detail');
			Route::post('save', 'MobileApp\LeaveSubmissionController@save');
		});

		// Alasan Cuti
		Route::prefix('leave-reason')->group(function(){
			Route::post('list', 'MobileApp\LeaveReasonController@list');
		});

		// Payroll
		Route::prefix('payroll')->group(function(){
			Route::post('list', 'MobileApp\PayrollController@list');
			Route::post('detail', 'MobileApp\PayrollController@detail');
		});

		// Setting
		Route::prefix('setting')->group(function(){
			Route::post('get-profile', 'MobileApp\SettingController@getProfile');
			Route::post('set-profile', 'MobileApp\SettingController@setProfile');
			Route::post('change-password', 'MobileApp\SettingController@changePassword');
		});

		// Notification
		Route::prefix('notification')->group(function(){
			Route::post('list', 'MobileApp\NotificationController@list');
		});

		Route::prefix('list-attendance')->group(function(){
			Route::post('/', 'MobileApp\SettingController@listAttendance');
		});
		
	});
});

/**
 * 	HRIS API
 * */
Route::prefix('hris')->middleware('hrisApiCheck')->group(function(){
	Route::prefix('department')->group(function(){
		Route::post('/', 'HrisApi\DepartmentController@index');
		Route::get('/', 'HrisApi\DepartmentController@index');
	});

	Route::prefix('position')->group(function(){
		Route::post('/', 'HrisApi\PositionController@index');
		Route::get('/', 'HrisApi\PositionController@index');
		Route::post('{position}', 'HrisApi\PositionController@detail');
		Route::get('{position}', 'HrisApi\PositionController@detail');
	});

	Route::prefix('employee-group')->group(function(){
		Route::post('/', 'HrisApi\EmployeeGroupController@index');
		Route::get('/', 'HrisApi\EmployeeGroupController@index');
	});

	Route::prefix('employee')->group(function(){
		Route::post('/', 'HrisApi\EmployeeController@index');
		Route::get('/', 'HrisApi\EmployeeController@index');
		Route::post('{employee}', 'HrisApi\EmployeeController@get');
		Route::get('{employee}', 'HrisApi\EmployeeController@get');
	});

	Route::prefix('attendance')->group(function(){
		Route::post('/', 'HrisApi\AttendanceController@index');
		Route::get('/', 'HrisApi\AttendanceController@index');
		Route::post('{attendance}', 'HrisApi\AttendanceController@get');
		Route::get('{attendance}', 'HrisApi\AttendanceController@get');
		Route::get('/');
	});

	Route::prefix('salary-slip')->group(function(){
		Route::post('/', 'HrisApi\SalarySlipController@index');
		Route::get('/', 'HrisApi\SalarySlipController@index');
		Route::post('save', 'HrisApi\SalarySlipController@save');
	});

	
	
});
