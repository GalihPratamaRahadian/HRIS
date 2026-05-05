<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
	public function clear()
	{
		\Artisan::call('cache:clear');
		\Artisan::call('route:clear');
		\Artisan::call('config:clear');
		\Artisan::call('route:clear');

		return \Res::success([
			'message' => 'Berhasil dibersihkan'
		]);
	}
}
