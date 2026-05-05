<?php

namespace App\Console\Commands\AppCommands;

use Illuminate\Console\Command;

class MakeDefaultSettings extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:make_settings';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Membuat pengaturan bawaan';

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
		foreach($this->getSettings() as $name => $value)
		{
			$setting = \App\Models\Setting::where('setting_name', $name)->first();

			if (!$setting) {
				$setting = \App\Models\Setting::create([
					'setting_name'	=> $name,
					'setting_value'	=> $value,
				]);
			} elseif (empty($setting->setting_value)) {
				$setting->update([
					'setting_value'	=> $value,
				]);
			}
		}
	}


	public function getSettings()
	{
		return [
			'background_image'	=> 'background.jpg',
			'background_blur'	=> 0.5,
			'app_name'			=> 'Faceterminal',
			'temperature_min'	=> 34.0,
			'temperature_max'	=> 37.5,
			'stranger_name'		=> 'Stranger',
		];
	}
}
