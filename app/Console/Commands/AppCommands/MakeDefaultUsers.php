<?php

namespace App\Console\Commands\AppCommands;

use Illuminate\Console\Command;
use App\User;
use Hash;

class MakeDefaultUsers extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:make_users';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Membuat user bawaan';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{
		$user = User::where('role', User::ROLE_DEVELOPER)->first();

		if(!$user) {
			User::create([
				'name'		=> 'Developer',
				'username'	=> 'developer',
				'password'	=> Hash::make('pass'),
				'role'		=> User::ROLE_DEVELOPER,
			]);
		}

		$user = User::where('role', User::ROLE_ADMIN)->first();

		if(!$user) {
			User::create([
				'name'		=> 'admin',
				'username'	=> 'admin',
				'password'	=> Hash::make('pass'),
				'role'		=> User::ROLE_ADMIN,
			]);
		}
	}
}
