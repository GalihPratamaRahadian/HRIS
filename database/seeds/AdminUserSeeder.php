<?php

use Illuminate\Database\Seeder;
use App\User;

class AdminUserSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		if(User::where('role', User::ROLE_ADMIN)->count() == 0) {
			User::create([
				'name'		=> 'admin',
				'username'	=> 'admin',
				'password'	=> \Hash::make('pass'),
				'role'		=> User::ROLE_ADMIN,
			]);
		}
	}
}
