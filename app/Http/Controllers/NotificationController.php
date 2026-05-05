<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
	
	public function index(Request $request)
	{
		if($request->ajax()) {
			return Notification::dt();
		}

		return view('admin.notification.index', [
			'title'			=> 'Notifikasi',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Notifikasi',
					'link'	=> route('notification')
				],
			]
		]);
	}


	public function detail(Notification $notification)
	{
		$notification->setRead();
		return view('admin.notification.detail', [
			'title'			=> 'Detail Notifikasi',
			'notification'	=> $notification,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Notifikasi',
					'link'	=> route('notification')
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('notification.detail', $notification->id)
				],
			]
		]);
	}
}
