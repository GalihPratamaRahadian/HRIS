<?php

namespace App\Http\Controllers\MobileApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileAppToken;
use App\User;
use DB;

class LoginController extends Controller
{
	public function index(Request $request)
	{
		DB::beginTransaction();

		try {
			$username = $request->username;
			$password = $request->password;

			$user = User::where('username', $username)->where('role', 'staff')->first();

			if($user) {
				if($user->comparePassword($password)) {
					$token = MobileAppToken::createToken($user->employee->id);
					DB::commit();
					return \Res::success([
						'result'	=> [
							'token'		=> $token->token,
							'employee'	=> $user->employee->fetchData() ?? (object) []
						],
						'message'	=> 'Login berhasil',
					]);
				}
			}

			return \Res::invalid([
				'message'	=> 'Username/password salah',
			]);
		} catch (\Exception $e) {
			DB::rollback();
			return \Res::error($e);
		}
	}
}
