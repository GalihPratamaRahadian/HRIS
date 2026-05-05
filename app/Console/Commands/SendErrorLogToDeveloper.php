<?php

namespace App\Console\Commands;

use App\MyClass\Helper;
use App\MyClass\WhatsappNew;
use Illuminate\Console\Command;

class SendErrorLogToDeveloper extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:send_errors';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Kirim laporan error ke developer';

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
		ini_set('memory_limit', '-1');
		$path = storage_path('logs/laravel.log');

		if(!\File::exists($path)) {
			$this->info('Tidak ada error');
			return;
		}

		$error = trim(\File::get($path));
		if(empty($error)) {
			$this->info('Tidak ada error');
			return;
		}

		$text = \Date::fullDateWithDayName(date('Y-m-d'));
		$text .= "\n".date('H:i:s');
		$text .= "\n\nFrom : ".url('');
		$text .= "\n\n".$error;
		$text = trim($text);

		$EndPointWa = WhatsappNew::END_POINT_WA;
		if($EndPointWa == 'WA Baru'){
			// wa Baru
			Helper::sendNotificationWhatsapp($phoneNumber = '6282316425264', $message = $text);
		}else{
			\App\MyClass\Whatsapp::sendChat([
				'to'	=> '6282316425264',
				'text'	=> $text,
			]);
		}

		$laravelLogBackupPath = \Setting::temps('laravel.log.backup');
		if(!\File::exists($laravelLogBackupPath)) {
			\File::put($laravelLogBackupPath, '');
		}

		\File::put($path, '');
		\File::prepend($laravelLogBackupPath, "{$error}\n");
	}
}
