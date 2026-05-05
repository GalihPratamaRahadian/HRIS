<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckCourseDeadline extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'course:check_deadline';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Cek Deadline Course';

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
		\App\Models\Course::checkCourseDeadline();
	}
}
