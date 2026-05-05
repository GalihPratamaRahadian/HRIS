<?php

namespace App\Console\Commands\AppCommands;

use Illuminate\Console\Command;
use Artisan;
use File;

class ResetFiles extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:reset_files';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Membersihkan file data';

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
		// Membersihkan file data
		$this->cleanDirectories();

		// Mengisi kembali dengan file sistem dasar
		Artisan::call('app:make_system_files');
	}


	private function cleanDirectories()
	{
		foreach($this->getDirectories() as $directory)
		{
			File::cleanDirectory($directory);
		}
	}


	private function getDirectories()
	{
		return [
			storage_path('app/public/attendance'),
			storage_path('app/public/employee/face'),
			storage_path('app/public/employee/original'),
			storage_path('app/public/employee/thumb'),
			storage_path('app/public/employee_leave'),
			storage_path('app/public/faceterminal_log'),
			storage_path('app/public/logs'),
			storage_path('app/public/system'),
			storage_path('app/public/temps'),
		];
	}
}
