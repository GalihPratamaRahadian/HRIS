<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetEmployeeOffDay extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'attendance:set_employee_off_day';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Membuat libur karyawan';

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
		\App\Models\Shift::setEmployeeOffDay();
		\App\Models\OffDay::setEmployeeOffDay();
	}
}
