<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetManually extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:manually';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

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
		$attendances = \App\Models\Attendance::where('date', '>=', now()->addMonths(-2)->format('Y-m-d'))
										     ->where('date', '<=', now()->format('Y-m-d'))
										     ->get();
		foreach($attendances as $attendance)
		{
			$attendance->update([
				'shift_clock_in'	=> $attendance->date . ' 08:00:00',
				'shift_clock_out'	=> $attendance->date . ' 17:00:00',
				'late_tolerance'	=> 30,
			]);
			$attendance->setLateMinutes();
		}
	}
}
