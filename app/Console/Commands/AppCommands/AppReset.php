<?php

namespace App\Console\Commands\AppCommands;

use Illuminate\Console\Command;
use Artisan;

class AppReset extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:reset';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Reset aplikasi';

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
		if($this->confirm("Reset data pada aplikasi?", false)) {
			
			$this->info("Aplikasi ".url('')." akan segera direset");

			if($this->confirm("Yakin lanjutkan?", false)) {
				// Membersihkan record
				Artisan::call('app:reset_database');

				// Membersihkan file data
				Artisan::call('app:reset_files');

				// Inisialisasi ulang
				Artisan::call('app:init');
			}

		}
	}
}
