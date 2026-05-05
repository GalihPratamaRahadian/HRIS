<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileAppNotification;
use DB;

class MobileAppNotificationController extends Controller
{
	/**
	*   Mobile App Notification
	*
	*/
	public function index(Request $request)
	{
		if($request->ajax()) {
			return MobileAppNotification::dataTable($request);
		}

		return view('admin.mobile_app_notification.index', [
			'title'         => 'Notifikasi Mobile App',
			'breadcrumbs'   => [
				[
					'title' => 'Lanjutan',
					'link'  => 'javascript:void(0);'
				],
				[
					'title' => 'Notifikasi Mobile App',
					'link'  => route('admin.mobile_app_notification')
				],
			]
		]);
	}

	public function create()
	{
		return view('admin.mobile_app_notification.create', [
			'title'         => 'Tambah Notifikasi Mobile App',
			'breadcrumbs'   => [
				[
					'title' => 'Lanjutan',
					'link'  => 'javascript:void(0);'
				],
				[
					'title' => 'Notifikasi Mobile App',
					'link'  => route('admin.mobile_app_notification')
				],
				[
					'title' => 'Tambah',
					'link'  => route('admin.position.create')
				],
			]
		]);
	}

	public function store(Request $request)
	{
		DB::beginTransaction();

		try {
			MobileAppNotification::createMobileAppNotification($request->all());
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
