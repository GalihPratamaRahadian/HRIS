<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\MyClass\Validations;
use DB;

class AnnouncementController extends Controller
{
	
	public function index(Request $request)
	{
		if($request->ajax()) {
			return Announcement::dt($request);
		}

		return view('admin.announcement.index', [
			'title'         => 'Pengumuman',
			'breadcrumbs'   => [
				[
					'title' => 'Pengumuman',
					'link'  => route('announcement')
				],
			]
		]);
	}


	public function create()
	{
		return view('admin.announcement.create', [
			'title'         => 'Buat Pengumuman',
			'breadcrumbs'   => [
				[
					'title' => 'Pengumuman',
					'link'  => route('announcement')
				],
				[
					'title' => 'Buat',
					'link'  => route('announcement.create')
				],
			]
		]);
	}


	public function store(Request $request)
	{
		Validations::validateAnnouncement($request);

		try {
			$announcement = Announcement::createAnnouncement($request);

			if($request->is_published == 'yes' && $request->broadcast == 'yes') {
				$announcement->update([
					'send_status'	=> 'Menunggu',
					'send_schedule' => now()->addMinutes(2),
				]);
			}

			return \Res::save();
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function edit(Announcement $announcement)
	{
		return view('admin.announcement.edit', [
			'title'         => 'Edit Pengumuman',
			'announcement'	=> $announcement,
			'breadcrumbs'   => [
				[
					'title' => 'Pengumuman',
					'link'  => route('announcement')
				],
				[
					'title' => 'Edit',
					'link'  => route('announcement.edit', $announcement->id)
				],
			]
		]);
	}


	public function update(Request $request, Announcement $announcement)
	{
		Validations::validateAnnouncement($request);

		try {
			$announcement->updateAnnouncement($request);

			return \Res::update();
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function destroy(Announcement $announcement)
	{
		DB::beginTransaction();

		try {
			$announcement->deleteAnnouncement();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function detail(Announcement $announcement)
	{
		if(!$announcement->checkAccessToData()) return redirect()->back();

		return view('admin.announcement.detail', [
			'title'         => 'Detail Pengumuman',
			'announcement'	=> $announcement,
			'breadcrumbs'   => [
				[
					'title' => 'Pengumuman',
					'link'  => route('announcement')
				],
				[
					'title' => 'Detail',
					'link'  => route('announcement.detail', $announcement->id)
				],
			]
		]);
	}


	public function sendBroadcast(Announcement $announcement)
	{
		if(!$announcement->checkAccessToData()) return redirect()->back();

		try {
			if($announcement->isPublished()) {
				$announcement->update([
					'send_status'	=> 'Menunggu',
					'send_schedule' => now()->addMinutes(2),
				]);
				
				return \Res::success([
					'message' => 'Pengumuman akan segera dibroadcast'
				]);
			} else {
				return \Res::invalid([
					'message' => 'Pengumuman dengan status Draft tidak bisa dibroadcast'
				]);
			}

		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
