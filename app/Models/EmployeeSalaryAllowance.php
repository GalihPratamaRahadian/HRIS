<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryAllowance extends Model
{
	protected $fillable = [ 'id_employee_salary', 'allowance_name', 'allowance_nominal' ];


	public function employeeSalary()
	{
		return $this->belongsTo('App\Models\EmployeeSalary', 'id_employee_salary');
	}


	public function allowanceNominalText()
	{
		return 'Rp. '.number_format($this->allowance_nominal);
	}
}
