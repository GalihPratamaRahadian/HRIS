<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
	public function index(Request $request)
	{
		if($request->ajax()) {
			return Announcement::dt($request);
		}

		return view('employee.announcement.index', [
			'title'         => 'Pengumuman',
			'breadcrumbs'   => [
				[
					'title' => 'Pengumuman',
					'link'  => route('employee.announcement')
				],
			]
		]);
	}


	public function detail(Announcement $announcement)
	{
		if(!$announcement->checkAccessToData()) return redirect()->back();

		return view('employee.announcement.detail', [
			'title'         => 'Detail Pengumuman',
			'announcement'  => $announcement,
			'breadcrumbs'   => [
				[
					'title' => 'Pengumuman',
					'link'  => route('employee.announcement')
				],
				[
					'title' => 'Detail',
					'link'  => route('employee.announcement.detail', $announcement->id)
				],
			]
		]);
	}
}
