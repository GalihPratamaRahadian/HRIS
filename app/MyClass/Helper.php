<?php

/**
 *	Develop by Rohim Wahyudin (adiva)
 *	Manfaat class ini
 *	-> Formating ke format nomor hp indonesia
 *
 *	@method static string idPhoneNumberFormat(string $phone) untuk formating ke nomor hp indonesia
 *	@method static string tempsPath(string|null $filename) untuk mendapat filepath temp
 * */

namespace App\MyClass;

use App\MyClass\Date;
use Illuminate\Support\Str;


class Helper
{

	/**
	*	Untuk ubah ke format no telepon indonesia
	*	@param String $phone => Nomor telepon
	*	@return String $output => Nomor telepon sudah terformat
	*/
	public static function idPhoneNumberFormat($phone)
	{
		$output = $phone;
		$output = substr($output, 0, 1) == '0'? "62".substr($output, 1) : $output;
		$output = substr($output, 0, 3) == '+62'? substr($output, 1) : $output;
		$output = substr($output, 0, 2) != '62'? "62".$output : $output;

		return $output;
	}

	public static function lastMonth()
	{
		$startDate 	= today()->addMonths(-1);
		$startDate 	= date('Y-m-01', strtotime($startDate));
		$endDate 	= date('Y-m-t', strtotime($startDate));
		$monthNumber= date('m', strtotime($startDate));
		$monthName 	= Date::monthName($monthNumber);
		$year		= date('Y', strtotime($startDate));

		return (object) [
			'startDate'	=> $startDate,
			'endDate'	=> $endDate,
			'monthName'	=> $monthName,
			'year'		=> $year,
		];
	}


	public static function thisMonth()
	{
		$startDate 	= today();
		$startDate 	= date('Y-m-01', strtotime($startDate));
		$endDate 	= date('Y-m-t', strtotime($startDate));
		$monthNumber= date('m', strtotime($startDate));
		$monthName 	= Date::monthName($monthNumber);
		$year		= date('Y', strtotime($startDate));

		return (object) [
			'startDate'	=> $startDate,
			'endDate'	=> $endDate,
			'monthName'	=> $monthName,
			'year'		=> $year,
		];
	}


	public static function tempsPath($filename = '')
	{
		$dir = storage_path('app/public/temps');
		if(!\File::exists($dir)) \File::makeDirectory($dir);
		return $dir . '/'. $filename;
	}


	public static function createDirectoryIfNotExists($paths)
	{
		if(!is_array($paths)) {
			$paths = [ $paths ];
		}

		foreach($paths as $directory) {
			if(!\File::exists($directory)) {
				\File::makeDirectory($directory);
			}
		}
	}

    public static function sendNotificationWhatsapp($phoneNumber, $message, $filePath=null, $caption=null){
        $phoneNumber = Helper::idPhoneNumberFormat($phoneNumber);

        if($filePath != null){
            if(\File::exists($filePath) === true){
                if (Str::endsWith($filePath, ['jpg', 'jpeg', 'png'])) {
                    return WhatsappNew::sendChatAndImageWhatsapp($phoneNumber, $message, $filePath, $caption);
                } elseif (Str::endsWith($filePath, ['pdf'])) {
                    return WhatsappNew::sendChatanPdfWhatsapp($phoneNumber, $message, $filePath, $caption);
					// return true;
                }
            }
        }else{
			return WhatsappNew::sendChatanPdfWhatsapp($phoneNumber, $message, $filePath, $caption);
			// return true;
		}

        return false;
    }

    public static function sendBroadcastForSubmission($phoneNumber, $message, $filePath=null, $caption=null){
        $phoneNumber = Helper::idPhoneNumberFormat($phoneNumber);
        return WhatsappNew::sendChatAndImageWhatsapp($phoneNumber, $message, $filePath, $caption);
    }
}
