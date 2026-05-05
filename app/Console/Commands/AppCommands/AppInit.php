<?php

namespace App\Console\Commands\AppCommands;

use Illuminate\Console\Command;

class AppInit extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:init';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Inisialisasi awal sistem';

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
		// Membuat direktori untuk sistem
		\Artisan::call('app:make_directories');

		// Menyalin file sistem dasar
		\Artisan::call('app:make_system_files');

		// Membuat pengaturan dasar
		\Artisan::call('app:make_settings');

		// Membuat user dasar
		\Artisan::call('app:make_users');
	}
}
