<?php

namespace App\Console\Commands\AppCommands;

use Illuminate\Console\Command;
use File;

class MakeDefaultSystemFiles extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:make_system_files';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Membuat file sistem dasar';

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
		// Menyalin file yang sudah ada
		$this->copyingExistingFiles();

		// Membuat file
		$this->createNonExistingFiles();
	}


	private function copyingExistingFiles()
	{
		foreach($this->existingFiles() as $file)
		{
			if( !File::exists($file['destination']) ) {
				File::copy( $file['source'], $file['destination'] );
			}
		}
	}


	private function existingFiles()
	{
		return [
			[
				'source'		=> storage_path('system_files/no_available_landscape.jpg'),
				'destination'	=> storage_path('app/public/system/no_available_landscape.jpg')
			],
			[
				'source'		=> storage_path('system_files/no_available_portrait.jpg'),
				'destination'	=> storage_path('app/public/system/no_available_portrait.jpg')
			],
			[
				'source'		=> storage_path('system_files/no_available_square.jpg'),
				'destination'	=> storage_path('app/public/system/no_available_square.jpg')
			],
			[
				'source'		=> storage_path('system_files/default-background.jpg'),
				'destination'	=> storage_path('app/public/system/background.jpg')
			]
		];
	}


	private function createNonExistingFiles()
	{
		foreach($this->nonExistingFiles() as $file)
		{
			if( !File::exists($file) ) {
				File::put($file, '');
			}
		}
	}


	private function nonExistingFiles()
	{
		return [
			storage_path('app/public/logs/ft_event_log.txt'),
			storage_path('app/public/logs/ft_push_log.txt'),
		];
	}
}
