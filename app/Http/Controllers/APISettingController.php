<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class APISettingController extends Controller
{

	public function googleGeolocationIndex()
	{
		return view('api_setting.google_geolocation', [
			'title'			=> 'Google Geolocation',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Dashboard',
					'link'	=> route('dashboard')
				],
				[
					'title'	=> 'API',
					'link'	=> '#'
				],
				[
					'title'	=> 'Google Geolocation',
					'link'	=> route('api.google_geolocation')
				]
			]
		]);
	}


	public function googleGeolocationSave(Request $request)
	{
		DB::beginTransaction();

		try {
			\Setting::setValue('google_geolocation_api_key', $request->google_geolocation_api_key);
			DB::commit();

			return \Setting::saveResponse();
		} catch (\Exception $e) {
			DB::rollback();

			return \Setting::errorResponse($e);
		}
	}
}
