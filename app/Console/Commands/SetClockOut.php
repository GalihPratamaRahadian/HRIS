<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;

class SetClockOut extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'attendance:set_clock_out';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Set ClockOut';

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
		if(env('AUTOFILL_CLOCKOUT') == 'FORCE')
		{
			foreach(Attendance::all() as $attendance) {

				if(empty($attendance->clock_out) || $attendance->clock_out == '00:00:17')
				{
					$attendance->update([
						'clock_out'			=> env('CLOCKOUT_HOUR', '17:00:00'),
						'clock_out_at'		=> date('Y-m-d ').env('CLOCKOUT_HOUR', '17:00:00'),
						'clock_out_method'	=> Attendance::METHOD_SYSTEM,
					]);
				}
			}
		}
	}
}
