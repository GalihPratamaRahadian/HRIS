<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registrant;
use App\Models\Employee;
use DB;

class RegisterController extends Controller
{
	
	public function index()
	{
		if(setting('menu_registration', 'yes') != 'yes') abort(404);
		
		return view('auth.register', [
			'title'	=> 'Pendaftaran'
		]);
	}


	public function xhrSaveRegister(Request $request)
	{
		$request->validate([
			'name'			=> 'required',
			'phone_number'	=> 'required',
			'email'			=> 'required'
		]);
		DB::beginTransaction();

		try {
			if(Employee::isEmployeeReachLimit()) {
				return \Res::invalid([
					'message'	=> 'Jumlah karyawan telah mencapai batas'
				]);
			}

			$phone = $request->phone_number;
			$registrant = Registrant::where('phone_number', $phone)->first();

			if($registrant) {
				return \Res::invalid([
					'message'	=> 'Nomor '.$request->phone_number.' tidak dapat digunakan',
				]);
			}

			if(!$registrant) {
				$user = \App\User::where('username', $phone)->first();

				if($user) {
					return \Res::invalid([
						'message'	=> 'Nomor '.$request->phone_number.' tidak dapat digunakan',
					]);
				}
			}

			if(!$registrant) {
				$employee = Employee::where('phone_number', $phone)->first();

				if($employee) {
					return \Res::invalid([
						'message'	=> 'Nomor '.$request->phone_number.' tidak dapat digunakan',
					]);
				}
			}

			Registrant::createNewRegistrant($request);
			DB::commit();

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function editProfile()
	{
		return view('profile.index', [
			'title'			=> 'Profil',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Profil',
					'link'	=> route('profile')
				]
			]
		]);
	}


	public function saveProfile(Request $request)
	{
		DB::beginTransaction();

		try {
			auth()->user()->registrant->saveProfile($request);
			DB::commit();

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function indexAdmin(Request $request)
	{
		if($request->ajax())
		{
			return Registrant::apiDT($request);
		}

		return view('admin.registrant.index', [
			'title'			=> 'Pendaftaran Pengguna',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Pendaftaran Pengguna',
					'link'	=> route('registration')
				]
			]
		]);
	}


	public function detail(Registrant $registrant)
	{
		return view('admin.registrant.detail', [
			'title'			=> 'Detail Pendaftaran Pengguna',
			'registrant'	=> $registrant,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Pendaftaran Pengguna',
					'link'	=> route('registration')
				],
				[
					'title'	=> 'Detail',
					'link'	=> route('registration.detail', $registrant->id)
				]
			]
		]);
	}


	public function photoRotateToLeft(Registrant $registrant)
	{
		try {
			$registrant->rotatePhotoToLeft();

			return redirect()->back();
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function photoRotateToRight(Registrant $registrant)
	{
		try {
			$registrant->rotatePhotoToRight();

			return redirect()->back();
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function resetAndSend(Registrant $registrant)
	{
		try {
			$registrant->resetUserAndSend();

			return \Res::success();
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function approve(Registrant $registrant)
	{
		DB::beginTransaction();

		try {
			if(Employee::isEmployeeReachLimit()) {
				return \Res::invalid([
					'message'	=> 'Jumlah karyawan telah mencapai batas'
				]);
			}

			$registrant->approve();
			DB::commit();
			$registrant->employee->pushToFaceTerminalDevice();

			return \Res::success([
				'message'	=> 'Berhasil disetujui'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function reject(Request $request, Registrant $registrant)
	{
		DB::beginTransaction();

		try {
			$registrant->reject($request);
			DB::commit();

			return \Res::success([
				'message'	=> 'Berhasil ditolak'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function destroy(Registrant $registrant)
	{
		DB::beginTransaction();

		try {
			$registrant->deleteRegistrant();
			DB::commit();

			return \Res::success([
 				'message'	=> 'Berhasil dihapus'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
