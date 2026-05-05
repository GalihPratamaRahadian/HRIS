<?php

namespace App\Models;

use App\MyClass\Helper;
use App\MyClass\WhatsappNew;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
	protected $fillable = [ 'setting_name', 'setting_value' ];


	public static function get($name, $defaultValue = null)
	{
		$setting = self::where('setting_name', $name)->first();

		if(!$setting) {
			$setting = Setting::create([
				'setting_name'	=> $name,
				'setting_value'	=> $defaultValue,
			]);
		}

		return $setting;
	}


	public static function getValue($name, $defaultValue = null)
	{
		return self::get($name, $defaultValue)->setting_value;
	}


	public static function setValue($name, $value = null)
	{
		$setting = self::get($name, $value);

		$setting->update([
			'setting_value'	=> $value,
		]);

		return $setting;
	}


	public static function setValues($settings)
	{
		if(is_array($settings))
		{
			foreach($settings as $name => $value)
			{
				self::setValue($name, $value);
			}

			return true;
		}

		return false;
	}


	public static function increase($name)
	{
		$setting = self::get($name, 0);

		if(is_numeric($setting->setting_value)) {
			$setting->update([
				'setting_value'	=> $setting->setting_value + 1
			]);

			return true;
		}

		return false;
	}


	/**
	*
	*	Setting Helper
	*
	*/
	public static function getNoAvailableLink($size = 'square')
	{
		return url("storage/system/no_available_{$size}.jpg");
	}


	public static function isHasLoginBackground()
	{
		if(empty(self::getValue('background_image'))) return false;

		return \File::exists(self::getLoginBackgroundPath());
	}

	public static function getLoginBackgroundPath()
	{
		return storage_path('app/public/system/'.self::getValue('background_image'));
	}

	public static function getLoginBackgroundLink($require = true)
	{
		if($require) {
			if(!self::isHasLoginBackground()) return self::getNoAvailableLink();
		}

		return url('storage/system/'.self::getValue('background_image'));
	}

	public static function setLoginBackground($request)
	{
		if(!empty($request->background_image))
		{
			$image = $request->file('background_image');
			$filename = "background.{$image->getClientOriginalExtension()}";

			self::emptyingLoginBackground();

			$image->move(storage_path('app/public/system'), $filename);

			self::setValue('background_image', $filename);

			return true;
		}
		else
		{
			return false;
		}
	}


	public static function emptyingLoginBackground()
	{
		if(self::isHasLoginBackground()) {
			\File::delete(self::getLoginBackgroundPath());
		}
	}


	public static function errorMessage($e)
	{
		$error = "{$e->getFile()}:{$e->getLine()} - {$e->getMessage()}";
		self::sendErrorToDeveloper($e);
		return $error;
	}


	public static function errorResponse($e)
	{
		return response()->json([
			'message'	=> self::errorMessage($e),
		], 500);
	}


	public static function successResponse($data = [])
	{
		$response = [
			'message'	=> 'Berhasil',
			'code'		=> 200,
		];

		$response = array_merge($response, $data);

		return response()->json($response);
	}


	public static function saveResponse()
	{
		return response()->json([
			'message'	=> 'Berhasil disimpan',
			'code'		=> 200,
		]);
	}


	public static function updateResponse()
	{
		return response()->json([
			'message'	=> 'Berhasil diupdate',
			'code'		=> 200,
		]);
	}


	public static function deleteResponse()
	{
		return response()->json([
			'message'	=> 'Berhasil dihapus',
			'code'		=> 200,
		]);
	}


	public static function invalidResponse($data = [])
	{
		$response = [
			'message'	=> 'Gagal',
			'code'		=> 422,
		];

		$response = array_merge($response, $data);

		return response()->json($response, 422);
	}


	public static function isAccessControlModuleActive()
	{
		$status = env('ACCESS_CONTROL', false);

		return $status;
	}


	public static function required()
	{
		return "<span class='text-danger'> * </span>";
	}


	public static function requiredBanner()
	{
		return "<div class='alert alert-info'>
					Kolom bertanda ".self::required()." wajib diisi.
				</div>";
	}


	public static function titleBanner($title)
	{
		return "<div class='title-banner mb-3'>
					".$title."
				</div>";
	}


	public static function temps($filename)
	{
		if(!\File::exists(storage_path('app/public/temps'))) {
			\Artisan::call('app:make_directories');
		}

		return storage_path('app/public/temps/'.$filename);
	}


	public static function alertDangerBanner($text = '')
	{
		return trim("
			<div class='alert alert-danger'>
				<i class='mdi mdi-alert'></i> {$text} 
			</div>"
		);
	}


	public static function getMinTemperature()
	{
		return (double) self::getValue('temperature_min');
	}


	public static function getMaxTemperature()
	{
		return (double) self::getValue('temperature_max');
	}


	public static function isNormalTemperature($temperature)
	{
		$temperature = (double) $temperature;

		if( $temperature >= self::getMinTemperature() && 
			$temperature <= self::getMaxTemperature()) return true;
		return false;
	}


	public static function getAvailableLateCutType()
	{
		return [
			'each_minutes'			=> 'Per Menit',
			'every_few_minutes'		=> 'Setiap beberapa menit',
		];
	}


	public static function setLateCutSetting($request)
	{
		$type = $request->late_cut_type;
		self::setValue('late_cut_type', $type);

		if($type == 'every_few_minutes') {
			self::setValues([
				'late_cut_duration'	=> $request->late_cut_duration,
				'late_cut_nominal'	=> $request->late_cut_nominal,
			]);
		}
	}


	public static function ajaxTest()
	{
		return [
			'HTTP_X-Requested-With' => 'XMLHttpRequest'
		];
	}


	public static function sendErrorToDeveloper($error)
	{
		$message = "From : ".url('');
		$message .= "\n\nError : ". $error->getMessage();
		$message .= "\n\nTrace : ". $error->getTraceAsString();

		$EndPointWa = WhatsappNew::END_POINT_WA;
		if($EndPointWa == 'WA Baru'){
			// wa Baru
			$res = Helper::sendNotificationWhatsapp($phoneNumber = '6282316425264', $message);
		}else{
			$res = \App\MyClass\Whatsapp::sendChat([
				'to'	=>  '6282316425264',
				'text'	=> $message,
			]);
		}
	}
}
