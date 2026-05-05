<?php

namespace App\Http\Controllers\MobileApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileAppToken;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
	public function list(Request $request)
	{
		try {
			$token = MobileAppToken::getByToken($request->token);
			$employee = $token->employee;

			$page = $request->page ?? 1;
			$limit = $request->limit ?? 5;

			$announcements = Announcement::where('is_published', 'yes')
									->where(function($query) use($employee) {
										$query->where('id_department', $employee->id_department)
											  ->orWhere('id_department', null);
									})->where(function($query) use($employee){
										$query->where('id_employee_group', $employee->id_employee_group)
											  ->orWhere('id_employee_group', null);
									})
									->take($limit)
									->skip(($page - 1) * $limit)
									->get();
				
			return \Res::success([
				'result' => [
					'announcements'   => Announcement::fetchAnnouncements($announcements),
				]
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function detail(Request $request)
	{
		$request->validate([
			'id_announcement' => 'required|exists:announcements,id',
		], [
			'id_announcement.required'=> 'ID Pengumuman diperlukan',
			'id_announcement.exists'  => 'Data Pengumuman tidak ditemukan'
		]);

		try {
			$announcement = Announcement::find($request->id_announcement);
				
			return \Res::success([
				'result' => [
					'announcement'    => $announcement->fetchData(),
				]
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
