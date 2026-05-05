<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollAttendance extends Model
{
	protected $fillable = [ 'id_payroll', 'id_attendance', 'salary' ];


	public function payroll()
	{
		return $this->belongsTo('App\Models\Payroll', 'id_payroll');
	}


	public function attendance()
	{
		return $this->belongsTo('App\Models\Attendance', 'id_attendance');
	}


	public function salaryText()
	{
		return 'Rp. '.number_format($this->salary);
	}
}
