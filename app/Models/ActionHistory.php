<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\MyClass\Whatsapp;
use Tools;

class ActionHistory extends Model
{
	protected $fillable = [ 'date', 'action', 'id_reference', 'reference', 'description' ];


	const REMIND_EMPLOYEE_TO_ATTEND			= 'remind_employee_to_attend';
	const ALERT_NOT_ATTEND_EMPLOYEE			= 'alert_not_attend_employee';
	const EMPLOYEE_ALREADY_CLOCK_IN			= 'employee_already_clock_in';
	const EMPLOYEE_ALREADY_CLOCK_OUT		= 'employee_already_clock_out';
	const TOMORROW_IS_OFFDAY_TO_EMPLOYEE	= 'tomorrow_is_offday_to_employee';
	const EMPLOYEE_CLOCK_OUT_CHECK			= 'employee_clock_out_check';


	public static function getAvailableActions()
	{
		return [
			[
				'key'	=> self::REMIND_EMPLOYEE_TO_ATTEND,
				'label'	=> 'Notifikasi pengingat karyawan untuk mengisi kehadiran'
			],
			[
				'key'	=> self::ALERT_NOT_ATTEND_EMPLOYEE,
				'label'	=> 'Notifikasi peringatan karyawan yang belum isi kehadiran'
			],
			[
				'key'	=> self::EMPLOYEE_ALREADY_CLOCK_IN,
				'label'	=> 'Notifikasi karyawan berhasil isi kehadiran masuk'
			],
			[
				'key'	=> self::EMPLOYEE_ALREADY_CLOCK_OUT,
				'label'	=> 'Notifikasi karyawan berhasil isi kehadiran keluar'
			],
			[
				'key'	=> self::TOMORROW_IS_OFFDAY_TO_EMPLOYEE,
				'label'	=> 'Notifikasi pemberitahuan besok libur'
			],
			[
				'key'	=> self::EMPLOYEE_CLOCK_OUT_CHECK,
				'label'	=> 'Cek Ulang Kehadiran'
			],
		];
	}


	public static function getLabelByKey($key)
	{
		foreach(self::getAvailableActions() as $action)
		{
			if($action['key'] == $key) {
				return $action['label'];
			}
		}

		return false;
	}


	public function label()
	{
		return self::getLabelByKey($this->action);
	}


	public static function createNotificationHistory($action, $referenceID, $description = null)
	{
		$history = self::create([
			'date'			=> now(),
			'action'		=> $action,
			'id_reference'	=> $referenceID,
			'reference'		=> 'employee',
			'description'	=> $description
		]);

		return $history;
	}


	public function dateText()
	{
		$tt = function($date) {
			return strtotime($date);
		};

		$dateText = Tools::dayName(date('N', $tt($this->date)));
		$dateText .= ", ";
		$dateText .= date('d', $tt($this->date))." ";
		$dateText .= Tools::monthName(date('m', $tt($this->date)))." ";
		$dateText .= date('Y', $tt($this->date))." ";
		$dateText .= date('H:i:s', $tt($this->clock_in))." WIB";

		return $dateText;
	}


	public static function sendHistoryToDeveloper()
	{
		$phoneNumbers = [ '6282316425264' ];
		
		$message = $this->dateText();
		// $message = $this->
	}


	public static function getHistory($date, $action, $referenceID = null)
	{
		$history = self::where('date', 'like', '%'.date('Y-m-d').'%')
						->where('action', $action);

		if(!empty($referenceID)) {
			$history = $history->where('id_reference', $referenceID);
		}

		return $history->first();
	}
}
