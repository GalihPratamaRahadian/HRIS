<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftDetail extends Model
{
	protected $fillable = [ 'id_shift', 'day', 'clock_start', 'clock_end' ];


	/**
	 * 	Relationship
	 * */
	public function shift()
	{
		return $this->belongsTo('App\Models\Shift', 'id_shift');
	}



	/**
	 * 	Helper methods
	 * */
	public function clockStartText($format = 'H:i:s')
	{
		return date($format, strtotime($this->clock_start));
	}

	public function clockEndText($format = 'H:i:s')
	{
		return date($format, strtotime($this->clock_end));
	}


	public function clockStartWithDate($date = null)
	{
		if(empty($date)) $date = now()->format('Y-m-d');
		$datetime = new \Carbon\Carbon($date." ".$this->clock_start);
		return $datetime->format('Y-m-d H:i:s');
	}


	public function clockEndWithDate($date = null)
	{
		if(empty($date)) $date = now()->format('Y-m-d');
		$clockEndWithDate = $date." ".$this->clock_end;

		if($this->clock_start > $this->clock_end) {
			$clockEndWithDate = new \Carbon\Carbon($clockEndWithDate);
			$clockEndWithDate->addDays(1);
			$clockEndWithDate = date('Y-m-d', strtotime($clockEndWithDate)).' '.$this->clock_end;
		}

		$datetime = new \Carbon\Carbon($clockEndWithDate);
		return $datetime->format('Y-m-d H:i:s');
	}

	public function dayName()
	{
		return \App\MyClass\Date::dayName($this->day);
	}
}
