<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB;

class UserController extends Controller
{
	
	public function index(Request $request)
	{
		if($request->ajax()) {
			return User::dt($request);
		}

		return view('admin.user.index', [
			'title'         => 'User',
			'breadcrumbs'   => [
				[
					'title' => 'User',
					'link'  => route('user')
				],
			]
		]);
	}


	public function create()
	{
		return view('admin.user.create', [
			'title'         => 'Buat User',
			'breadcrumbs'   => [
				[
					'title' => 'User',
					'link'  => route('user')
				],
				[
					'title' => 'Buat',
					'link'  => route('user.create')
				],
			]
		]);
	}

	
	public function store(Request $request)
	{
		$request->validate([
			'username'	=> 'required|unique:users,username',
			'password'	=> 'required',
			'confirm_password'	=> 'required|same:password',
		], [
			'username.unique'	=> 'Username tidak tersedia',
			'confirm_password.same'	=> 'Konfirmasi password harus sama dengan password',
		]);

		DB::beginTransaction();

		try {
			$user = User::createUser($request->all());
			$user->modifyPermissions($request->permissions);
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function edit(User $user)
	{
		return view('admin.user.edit', [
			'title'         => 'Edit User',
			'user'			=> $user,
			'breadcrumbs'   => [
				[
					'title' => 'User',
					'link'  => route('user')
				],
				[
					'title' => 'Edit',
					'link'  => route('user.edit', $user->id)
				],
			]
		]);
	}

	
	public function update(Request $request, User $user)
	{
		$request->validate([
			'username'	=> 'required|unique:users,username,'.$user->id,
			'confirm_password'	=> 'same:password',
		], [
			'username.unique'	=> 'Username tidak tersedia'
		]);

		DB::beginTransaction();

		try {
			$user->updateUser($request->all());
			$user->modifyPermissions($request->permissions);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	
	public function destroy(User $user)
	{
		DB::beginTransaction();

		try {
			$user->deleteUser();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
