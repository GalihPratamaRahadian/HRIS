<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeEmployeeUsers extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:make_employee_users';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Membuat user untuk karyawan';

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
		\App\Models\Employee::createEmployeeUsers();
	}
}
