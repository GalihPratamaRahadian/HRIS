<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendPayrolls extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'payroll:send';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send waiting payrolls';

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
		ini_set('memory_limit','512M');
		return \App\Models\Payroll::sendWaitingPayrolls();
	}
}
