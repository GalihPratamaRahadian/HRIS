<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffDayDetail extends Model
{
	protected $fillable = [ 'id_off_day', 'id_employee' ];


	/**
	 * 	Relationship methods
	 * */
	public function offDay()
	{
		return $this->belongsTo('App\Models\OffDay', 'id_off_day');
	}

	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee')->withTrashed();
	}


	/**
	 * 	Helper methods
	 * */
	public function employeeName()
	{
		return $this->employee->employee_name ?? '-';
	}
}
