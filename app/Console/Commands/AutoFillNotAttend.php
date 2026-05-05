<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AutoFillNotAttend extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'attendance:auto_fill_not_attend';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Otomatis mengisi ketidakhadiran';

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
		\App\Models\Attendance::autoFillNotAttend();
	}
}
