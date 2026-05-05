<?php

namespace App\Http\Controllers;

use App\MyClass\SetEnv;
use App\MyClass\Tools;
use DB;
use Illuminate\Http\Request;
use Setting;

class SettingController extends Controller
{


	/**
	*		APP
	*
	*/
	public function app()
	{
		return view('admin.setting.app', [
			'title'         => 'Setting Aplikasi',
			'breadcrumbs'   => [
				[
					'title'	=> 'Setting Aplikasi',
					'link'	=> route('setting.app')
				]
			]
		]);
	}


	public function xhrSaveGeneral(Request $request)
	{
		$request->validate([
			'app_name'	=> 'required'
		]);
		DB::beginTransaction();

		try {
			Setting::setValue('app_name', $request->app_name);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function xhrSaveNotificationToAdmin(Request $request)
	{
		$request->validate([
			'admin_whatsapp_number'	=> 'required'
		]);
		DB::beginTransaction();

		try {
			Setting::setValue('admin_whatsapp_number', $request->admin_whatsapp_number);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function xhrSaveLoginBackground(Request $request)
	{
		DB::beginTransaction();

		try {
			if(!Setting::isHasLoginBackground()) {
				$request->validate([
					'background_image'	=> 'required',
				]);
			}

			Setting::setLoginBackground($request);
			Setting::setValue('background_blur', $request->background_blur);
			DB::commit();

			return \Res::update([

			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function xhrSaveAttendanceWebMobile(Request $request)
	{
		$request->validate([
			'is_using_face_compare_for_attendance'	=> 'required|in:yes,no',
			'face_compare_similarity_for_attendance'	=> 'required|min:1,max:100',
		], [
			'is_using_face_compare_for_attendance.required'	=> 'Data Diperlukan',
			'is_using_face_compare_for_attendance.in'		=> 'Data Tidak Valid',
			'face_compare_similarity_for_attendance.required'	=> 'Data Diperlukan',
			'face_compare_similarity_for_attendance.min'	=> 'Minimal 1 Persen',
			'face_compare_similarity_for_attendance.max'	=> 'Maksimal 100 Persen',
		]);
		DB::beginTransaction();

		try {
			Setting::setValue('is_using_face_compare_for_attendance', $request->is_using_face_compare_for_attendance);
			Setting::setValue('face_compare_similarity_for_attendance', $request->face_compare_similarity_for_attendance);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function xhrSaveElearning(Request $request)
	{
		$request->validate([
			'minimum_percentage_for_exam_passed'	=> 'required|min:1|max:100'
		], [
			'minimum_percentage_for_exam_passed.required'	=> 'Data Diperlukan',
			'minimum_percentage_for_exam_passed.min'		=> 'Minimal 1 Persen',
			'minimum_percentage_for_exam_passed.max'		=> 'Maksimal 100 Persen',
		]);
		DB::beginTransaction();

		try {
			Setting::setValue('minimum_percentage_for_exam_passed', $request->minimum_percentage_for_exam_passed);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function xhrSaveActivateMenu(Request $request)
	{
		$request->validate([
			'menu_submission'		=> 'required|in:yes,no',
			'menu_registration'		=> 'required|in:yes,no',
			'menu_announcement'		=> 'required|in:yes,no',
			'menu_warning_letter'	=> 'required|in:yes,no',
			'menu_elearning'		=> 'required|in:yes,no',
			'menu_face_terminal_log' => 'required|in:yes,no',
			'menu_face_terminal_device' => 'required|in:yes,no',
			'menu_sales_tracking'	=> 'required|in:yes,no',
			'menu_tracking'			=> 'required|in:yes,no',
			'menu_face_compare'		=> 'required|in:yes,no',
		], [
			'menu_submission.required'		=> 'Data Diperlukan',
			'menu_submission.in'			=> 'Nilai Tidak Valid',
			'menu_registration.required'	=> 'Data Diperlukan',
			'menu_registration.in'			=> 'Nilai Tidak Valid',
			'menu_announcement.required'	=> 'Data Diperlukan',
			'menu_announcement.in'			=> 'Nilai Tidak Valid',
			'menu_warning_letter.required'	=> 'Data Diperlukan',
			'menu_warning_letter.in'		=> 'Nilai Tidak Valid',
			'menu_elearning.required'		=> 'Data Diperlukan',
			'menu_elearning.in'				=> 'Nilai Tidak Valid',
			'menu_face_terminal_log.required'	=> 'Data Diperlukan',
			'menu_face_terminal_log.in'			=> 'Nilai Tidak Valid',
			'menu_face_terminal_device.required'=> 'Data Diperlukan',
			'menu_face_terminal_device.in'		=> 'Nilai Tidak Valid',
			'menu_sales_tracking.required'	=> 'Data Diperlukan',
			'menu_sales_tracking.in'		=> 'Nilai Tidak Valid',
			'menu_tracking.required'		=> 'Data Diperlukan',
			'menu_tracking.in'				=> 'Nilai Tidak Valid',
			'menu_face_compare.required'	=> 'Data Diperlukan',
			'menu_face_compare.in'			=> 'Nilai Tidak Valid',
		]);
		DB::beginTransaction();

		try {
			Setting::setValues([
				'menu_submission' 		=> $request->menu_submission,
				'menu_registration' 	=> $request->menu_registration,
				'menu_announcement' 	=> $request->menu_announcement,
				'menu_warning_letter' 	=> $request->menu_warning_letter,
				'menu_elearning' 		=> $request->menu_elearning,
				'menu_face_terminal_log' => $request->menu_face_terminal_log,
				'menu_face_terminal_device' => $request->menu_face_terminal_device,
				'menu_sales_tracking' 	=> $request->menu_sales_tracking,
				'menu_tracking' 		=> $request->menu_tracking,
				'menu_face_compare' 	=> $request->menu_face_compare,
			]);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function xhrSaveApiIntegration(Request $request)
	{
		try {
			DB::beginTransaction();
			Setting::setValue('hris_api_key', \Str::random(24));
			DB::commit();

			return \Res::success([
				'message' => 'Berhasil direset'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function xhrSaveDataStorage(Request $request)
	{
		$request->validate([
			'max_photo_age'	=> 'required|min:1'
		], [
			'max_photo_age.required'	=> 'Data Diperlukan',
			'max_photo_age.min'			=> 'Minimal 1 Hari'
		]);
		DB::beginTransaction();

		try {
			Setting::setValue('max_photo_age', $request->max_photo_age);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function xhrSaveDeveloper(Request $request)
	{
		$request->validate([
			'relay_face_terminal_url'	=> 'required',
			'whatsapp_url_server'		=> 'required',
		], [
			'relay_face_terminal_url.required'	=> 'Data Diperlukan',
			'whatsapp_url_server.required'	=> 'Data Diperlukan',
		]);
		DB::beginTransaction();

		try {
			Setting::setValue('relay_face_terminal_url', $request->relay_face_terminal_url);
			Setting::setValue('recent_ws_url', $request->recent_ws_url);
			Setting::setValue('whatsapp_url_server', $request->whatsapp_url_server);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}




	/**
	*		BACKGROUND LOGIN
	*
	*/
	public function backgroundLogin()
	{
		return view('admin.setting.background_login', [
			'title'         => 'Setting Background Login',
			'breadcrumbs'   => [
				[
					'title'	=> 'Setting Background Login',
					'link'	=> route('setting.background_login')
				]
			]
		]);
	}




	public function settingTemperature()
	{
		return view('admin.setting/settingTemperature', [
			'tempMin'           => AppSetting::get('temperature_min'),
			'tempMax'           => AppSetting::get('temperature_max'),
			'tempClosedAbnormal'=> AppSetting::get('temperature_closed_for_abnormal'),
		]);
	}

	public function settingTemperatureUpdate(Request $request)
	{
		$request->validate([
			'tempMin'       => 'required',
			'tempMax'       => 'required',
		]);

		$abnormal = 0;
		if($request->tempAbnormal != null && $request->tempAbnormal != "")
		{
			$abnormal = 1;
		}

		AppSetting::set('temperature_min', $request->tempMin);
		AppSetting::set('temperature_max', $request->tempMax);
		AppSetting::set('temperature_closed_for_abnormal', $abnormal);

		return Response::json([
			'msg'   => 'Setting berhasil disimpan',
		], 200);
	}



	// Staff

	public function profileStaff()
	{
		if(auth()->user()->isStaff()) {
			return $this->profileStaff();
		}
	}


	public function profileStafff()
	{
		return view('admin.setting.profile_staff', [
			'title'			=> 'Edit Profil',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Edit Profil',
					'link'	=> route('setting.profile')
				],
			]
		]);
	}

	public function xhrSaveProfile(Request $request)
	{
		if (auth()->user()->isStaff()) {
			return $this->saveProfileStaff($request);
		}
		else if(auth()->user()->isHrd())
		{
			return $this->setProfileHrd($request);
		}
	}


	private function saveProfileStaff(Request $request)
	{
		$request->validate([
			'employee_name'	=> 'required',
			'gender'		=> 'required|in:L,P',
			'email'			=> 'required',
			'phone_number'	=> 'required',
			'jamsostek'		=> 'required',
			'username'		=> 'required',
		]);
		DB::beginTransaction();

		try {
			$user = \App\User::where('username', $request->username)->where('id', '!=', auth()->user()->id)->first();
			if($user) {
				 return \Setting::invalidResponse([
				 	'message'	=> 'Username tidak tersedia',
				 	'errors'	=> [
				 		'username'	=> 'Username tidak tersedia'
				 	]
				 ]);
			}

			auth()->user()->update([
				'username'	=> $request->username
			]);

			auth()->user()->employee->update([
				'employee_name'	=> $request->employee_name,
				'gender'		=> $request->gender,
				'email'			=> $request->email,
				'phone_number'	=> $request->phone_number,
				'jamsostek'		=> $request->jamsostek,
			]);
			DB::commit();

			return \Setting::saveResponse();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}



	/**
	*	@see Profile
	*/
	public function profile()
	{
		return view('admin.setting.profile', [
			'title'         => 'Edit Profil Akun',
			'breadcrumbs'   => [
				[
					'title'	=> 'Edit Profil Akun',
					'link'	=> route('setting.profile')
				]
			]
		]);
	}

	public function saveProfile(Request $request)
	{
		DB::beginTransaction();

		try {
			auth()->user()->update([
				'name'		=> $request->name,
				'username'	=> $request->username,
			]);
			DB::commit();

			return \Res::update([
				'message'	=> 'Berhasil'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}




	/**
	*		PASSWORD
	*
	*/
	public function password()
	{
		return view('admin.setting.password', [
			'title'         => 'Ganti Password',
			'breadcrumbs'   => [
				[
					'title'	=> 'Ganti Password',
					'link'	=> route('setting.password')
				]
			]
		]);
	}

	public function savePassword(Request $request)
	{
		$request->validate([
			'old_password'	=> 'required',
			'new_password'	=> 'required',
			'confirm_password'	=> 'required|same:new_password',
		], [
			'confirm_password.same'	=> 'Konfirmasi password baru harus sama dengan password baru'
		]);
		DB::beginTransaction();

		try {
			if(auth()->user()->comparePassword($request->old_password)) {
				auth()->user()->changePassword($request->new_password);
				DB::commit();

				return \Res::update();
			} else {
				DB::commit();

				return \Res::invalid([
					'message'	=> 'Password lama salah',
					'errors'	=> [
						'old_password'	=> 'Password lama salah'
					]
				]);
			}
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

    /**
	*		EMAIL SETTINGS
	*
	*/
	public function email()
	{
		return view('admin.setting.email', [
			'title'         => 'Ganti Email',
			'breadcrumbs'   => [
				[
					'title'	=> 'Ganti Email',
					'link'	=> route('setting.email')
				]
			]
		]);
	}

	public function saveEmail(Request $request)
	{
		$request->validate([
			'email_username'	=> 'required|email',
            'email_password'	=> 'required',
            'email_host'		=> 'required',
            'email_port'		=> 'required',
		], [
			'email_username.required'	=> 'Username Harus Diisi',
            'email_username.email'		=> 'Format Email Tidak Valid',
            'email_password.required'	=> 'Password Harus Diisi',
            'email_host.required'		=> 'Host Harus Diisi',
            'email_port.required'		=> 'Port Harus Diisi',
		]);

		DB::beginTransaction();

		try {

            Setting::setValue('email_username', $request->email_username);
            Setting::setValue('email_password', $request->email_password);
            Setting::setValue('email_host', $request->email_host);
            Setting::setValue('email_port', $request->email_port);

			DB::commit();

            // set email to env
            SetEnv::setEmailToEnv();

			return \Res::update();

		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

    /**
	*		SCAN QR CODE WHATSAPP
	*
	*/
	public function whatsapp()
	{
		return view('admin.setting.whatsapp', [
			'title'         => 'Scan QR Code Whatsapp',
			'breadcrumbs'   => [
				[
					'title'	=> 'Scan QR Code Whatsapp',
					'link'	=> route('setting.whatsapp')
				]
			]
		]);
	}

    public function saveWhatsapp(Request $request)
	{
		$request->validate([
			'url_whatsapp'	=> 'required',
		], [
			'url_whatsapp.required'	=> 'Url Whatsapp Harus Diisi',
		]);

		DB::beginTransaction();

		try {

            Setting::setValue('url_whatsapp', $request->url_whatsapp);

			DB::commit();

			return \Res::update();

		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


}
