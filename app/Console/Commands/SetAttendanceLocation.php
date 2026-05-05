<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetAttendanceLocation extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'attendance:set_location';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Set Lokasi Kehadiran';

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
		foreach(\App\Models\Attendance::all() as $attendance)
		{
			if($attendance->isHasMeta())
			{
				$meta = $attendance->attendanceMeta;
				if(!$meta->isHasClockInLocation())
				{
					if($log = $meta->clockInFaceTerminalLog)
					{
						if($location = $log->getLocation())
						{
							$attendance->setClockInLocation($location->latitude, $location->longitude);
						}
					}
				}

				if(!$meta->isHasClockOutLocation())
				{
					if($log = $meta->clockOutFaceTerminalLog)
					{
						if($location = $log->getLocation())
						{
							$attendance->setClockOutLocation($location->latitude, $location->longitude);
						}
					}
				}
			}
		}
	}
}
