<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceLocationRule;
use App\Models\WebAttendancePermission;
use DB;

class AttendanceSettingController extends Controller
{
	
	public function attendanceLocationRulesIndex(Request $request)
	{
		if($request->ajax()) {
			return AttendanceLocationRule::dt();
		}

		return view('admin.attendance_setting.location_rules.index', [
			'title'			=> 'Master Lokasi',
			'breadcrumbs'	=> [
				[
					'title'	=> 'WFH / Kerja Diluar Kantor',
					'link'	=> '#'
				],
				[
					'title'	=> 'Master Lokasi',
					'link'	=> route('attendance_location_rules')
				],
			]
		]);
	}


	public function attendanceLocationRulesCreate(Request $request)
	{
		return view('admin.attendance_setting.location_rules.create', [
			'title'			=> 'Tambah Master Lokasi',
			'breadcrumbs'	=> [
				[
					'title'	=> 'WFH / Kerja Diluar Kantor',
					'link'	=> '#'
				],
				[
					'title'	=> 'Master Lokasi',
					'link'	=> route('attendance_location_rules')
				],
				[
					'title'	=> 'Tambah',
					'link'	=> route('attendance_location_rules.create')
				],
			]
		]);
	}


	public function attendanceLocationRulesStore(Request $request)
	{
		DB::beginTransaction();

		try {
			AttendanceLocationRule::createAttendanceLocationRule($request);
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function attendanceLocationRulesDetail(AttendanceLocationRule $locationRules)
	{
		return view('admin.attendance_setting.location_rules.detail', [
			'title'			=> 'Master Lokasi',
			'locationRules'	=> $locationRules,
			'breadcrumbs'	=> [
				[
					'title'	=> 'WFH / Kerja Diluar Kantor',
					'link'	=> '#'
				],
				[
					'title'	=> 'Master Lokasi',
					'link'	=> route('attendance_location_rules')
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('attendance_location_rules.edit', $locationRules->id)
				],
			]
		]);
	}


	public function attendanceLocationRulesEdit(AttendanceLocationRule $locationRules)
	{
		return view('admin.attendance_setting.location_rules.edit', [
			'title'			=> 'Master Lokasi',
			'locationRules'	=> $locationRules,
			'breadcrumbs'	=> [
				[
					'title'	=> 'WFH / Kerja Diluar Kantor',
					'link'	=> '#'
				],
				[
					'title'	=> 'Master Lokasi',
					'link'	=> route('attendance_location_rules')
				],
				[
					'title'	=> 'Edit',
					'link'	=> route('attendance_location_rules.edit', $locationRules->id)
				],
			]
		]);
	}


	public function attendanceLocationRulesUpdate(Request $request, AttendanceLocationRule $locationRules)
	{
		DB::beginTransaction();

		try {
			$locationRules->updateAttendanceLocationRule($request);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function attendanceLocationRulesDestroy(AttendanceLocationRule $locationRules)
	{
		DB::beginTransaction();

		try {
			$locationRules->deleteAttendanceLocationRule();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}




	/**
	*		Web Attendance Permission
	*/
	public function webAttendancePermissionsIndex(Request $request)
	{
		if($request->ajax()) {
			return WebAttendancePermission::apiDT();
		}

		return view('admin.attendance_setting.web_attendance_permission.index', [
			'title'			=> 'Setting WFH',
			'breadcrumbs'	=> [
				[
					'title'	=> 'WFH / Kerja Diluar Kantor',
					'link'	=> '#'
				],
				[
					'title'	=> 'Setting WFH',
					'link'	=> route('web_attendance_permissions')
				],
			]
		]);
	}


	public function webAttendancePermissionsCreate()
	{
		return view('admin.attendance_setting.web_attendance_permission.create', [
			'title'			=> 'Buat Setting',
			'breadcrumbs'	=> [
				[
					'title'	=> 'WFH / Kerja Diluar Kantor',
					'link'	=> '#'
				],
				[
					'title'	=> 'Setting WFH',
					'link'	=> route('web_attendance_permissions')
				],
				[
					'title'	=> 'Buat Setting',
					'link'	=> route('web_attendance_permissions.create')
				],
			]
		]);
	}


	public function webAttendancePermissionsStore(Request $request)
	{
		DB::beginTransaction();

		try {
			WebAttendancePermission::createWebAttendancePermissions($request);
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function webAttendancePermissionsEdit(WebAttendancePermission $webAttendancePermissions)
	{
		return view('admin.attendance_setting.web_attendance_permission.edit', [
			'title'						=> 'Edit Setting',
			'webAttendancePermissions'	=> $webAttendancePermissions,
			'breadcrumbs'				=> [
				[
					'title'	=> 'WFH / Kerja Diluar Kantor',
					'link'	=> '#'
				],
				[
					'title'	=> 'Setting WFH',
					'link'	=> route('web_attendance_permissions')
				],
				[
					'title'	=> 'Edit Setting',
					'link'	=> route('web_attendance_permissions.edit', $webAttendancePermissions->id)
				],
			]
		]);
	}


	public function webAttendancePermissionsUpdate(Request $request, WebAttendancePermission $webAttendancePermissions)
	{
		DB::beginTransaction();

		try {
			$webAttendancePermissions->createOrUpdateWebAttendancePermission([
				'id_employee'	=> $webAttendancePermissions->id_employee,
				'valid_until'	=> $request->valid_until,
				'locations'		=> $request->id_locations,
			]);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function webAttendancePermissionsDestroy(WebAttendancePermission $webAttendancePermissions)
	{
		DB::beginTransaction();

		try {
			$webAttendancePermissions->deleteWebAttendancePermission();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
