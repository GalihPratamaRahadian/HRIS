<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ManualCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:manual';

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
		$attendances = \App\Models\Attendance::where('date', '>=', '2022-11-01')->get();
		$this->info('Total : '.count($attendances));
		$count = 0;

		foreach($attendances as $attendance) {
			$attendance->update([
				'shift_clock_in'	=> date('Y-m-d 08:00:00', strtotime($attendance->date)),
				'shift_clock_out'	=> date('Y-m-d 17:00:00', strtotime($attendance->date)),
				'late_tolerance'	=> 30,
			]);

			$this->line('Changed');
			$count++;
		}

		$this->info('Changed : '.$count);
	}
}
