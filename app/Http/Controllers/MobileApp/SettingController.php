<?php

namespace App\Http\Controllers\MobileApp;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\MobileAppToken;
use App\User;
use Carbon\Carbon;
use DB;
use Exception;

class SettingController extends Controller
{
	public function getProfile(Request $request)
	{
		try {
			$token = MobileAppToken::getByToken($request->token);
			return \Res::success([
				'result'    => [
					'employee'  => $token->employee->fetchData(),
				]
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function setProfile(Request $request)
	{
		try {
			$token = MobileAppToken::getByToken($request->token);
			$user = $token->employee->user;
			$username = $request->username;

			if($user) {
				$userCheck = User::where('username', $username)
								 ->where('id', '!=', $user->id)
								 ->count();

				if($userCheck == 0) {
					DB::beginTransaction();
					$user->update([
						'username'  => $username
					]);
					DB::commit();
					
					return \Res::update();
				} else {
					return \Res::invalid([
						'message'   => 'Username tidak tersedia'
					]);
				}
			} else {
				return \Res::invalid([
					'message'   => 'User anda tidak valid'
				]);
			}

		} catch (\Exception $e) {
			DB::rollback();
			return \Res::error($e);
		}
	}


	public function changePassword(Request $request)
	{
		try {
			$token = MobileAppToken::getByToken($request->token);
			$user = $token->employee->user;
			$oldPassword = $request->old_password;
			$newPassword = $request->new_password;
			$confirmPassword = $request->confirm_password;

			if($newPassword != $confirmPassword) {
				return \Res::invalid([
					'message'   => 'Konfirmasi password wajib sama dengan password baru'
				]);
			}

			if($user) {
				if($user->comparePassword($oldPassword)) {
					DB::beginTransaction();
					$user->changePassword($newPassword);
					DB::commit();
					
					return \Res::update();
				} else {
					return \Res::invalid([
						'message'   => 'Password lama salah'
					]);
				}
			} else {
				return \Res::invalid([
					'message'   => 'User anda tidak valid'
				]);
			}

		} catch (\Exception $e) {
			DB::rollback();
			return \Res::error($e);
		}
	}

	public function listAttendance(Request $request)
	{
		try{

			$token = MobileAppToken::getByToken($request->token);

			$idEmployee = $token->employee->id;
			$attendances = Attendance::where('id_employee', $idEmployee)->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year)->get()->sortBy('date');
		
			$data = [];

			foreach($attendances as $attendance){
				array_push($data,[
					'tanggal' => $attendance->dateText(),
					'jam_masuk'	=> $attendance->clockInText(),
					'keterlambatan'	=> $attendance->lateText(),
				]);
			}
				
		return \Res::success([
			'result' => [
				'kehadiran' => $data
			]
		]);
		
	}catch (\Exception $e){
		DB::rollback();
		return \Res::error($e);
	}
	
}

}
