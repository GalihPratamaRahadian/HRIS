<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollCut extends Model
{
	protected $fillable = [ 'id_payroll', 'cut_name', 'cut_nominal' ];


	public function payroll()
	{
		return $this->belongsTo('App\Models\Payroll', 'id_payroll');
	}


	public function cutNominalText()
	{
		return 'Rp. '.number_format($this->cut_nominal);
	}
}
