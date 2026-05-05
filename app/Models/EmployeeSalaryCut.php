<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryCut extends Model
{
	protected $fillable = [ 'id_employee_salary', 'cut_name', 'cut_nominal' ];


	public function employeeSalary()
	{
		return $this->belongsTo('App\Models\EmployeeSalary', 'id_employee_salary');
	}


	public function cutNominalText()
	{
		return 'Rp. '.number_format($this->cut_nominal);
	}
}
