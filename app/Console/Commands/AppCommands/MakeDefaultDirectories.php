<?php

namespace App\Console\Commands\AppCommands;

use Illuminate\Console\Command;
use File;

class MakeDefaultDirectories extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:make_directories';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Membuat direktori bawaan untuk sistem';

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
		foreach($this->getDirectories() as $directory)
		{
			if(!File::exists($directory))
			{
				File::makeDirectory($directory);
			}
		}
	}


	private function getDirectories()
	{
		return [
			storage_path('event_logs'),
			storage_path('photo_logs'),
			storage_path('app/public/announcement'),
			storage_path('app/public/attendance'),
			storage_path('app/public/check_day'),
			storage_path('app/public/course'),
			storage_path('app/public/course/certificate'),
			storage_path('app/public/course/video'),
			storage_path('app/public/employee'),
			storage_path('app/public/employee/face'),
			storage_path('app/public/employee/original'),
			storage_path('app/public/employee/thumb'),
			storage_path('app/public/employee_leave'),
			storage_path('app/public/employee_training'),
			storage_path('app/public/faceterminal_log'),
			storage_path('app/public/faceterminal_log/face'),
			storage_path('app/public/faceterminal_log/full'),
			storage_path('app/public/faceterminal_log/with_watermark'),
			storage_path('app/public/leave_submissions'),
			storage_path('app/public/logs'),
			storage_path('app/public/registrant'),
			storage_path('app/public/registrant/face'),
			storage_path('app/public/registrant/original'),
			storage_path('app/public/registrant/thumb'),
			storage_path('app/public/salary_slip'),
			storage_path('app/public/sick_necessity_submissions'),
			storage_path('app/public/store'),
			storage_path('app/public/store_visit'),
			storage_path('app/public/system'),
			storage_path('app/public/temps'),
			storage_path('app/public/track_location'),
			storage_path('app/public/tracking_photo'),
			storage_path('app/public/tracking_good_receipt'),
		];
	}
}
