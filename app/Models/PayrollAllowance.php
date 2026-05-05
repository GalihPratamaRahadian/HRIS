<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollAllowance extends Model
{
	protected $fillable = [ 'id_payroll', 'allowance_name', 'allowance_nominal' ];


	public function payroll()
	{
		return $this->belongsTo('App\Models\Payroll', 'id_payroll');
	}


	public function allowanceNominalText()
	{
		return 'Rp. '.number_format($this->allowance_nominal);
	}
}
