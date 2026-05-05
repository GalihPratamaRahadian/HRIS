<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileAppToken extends Model
{
	protected $fillable = [ 'id_employee', 'token', 'valid_until', 'last_active_at' ];

	const VALID_DURATION_IN_MONTH = 2;


	/**
	 * 	Relationship
	 * */
	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee');
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createToken($employeeId)
	{
		return self::create([
			'id_employee'	=> $employeeId,
			'token'			=> \Str::random(32),
			'valid_until'	=> now()->addMonths(self::VALID_DURATION_IN_MONTH),
		]);
	}


	/**
	 * 	Helper methods
	 * */
	public function setLastActiveAt()
	{
		$this->update([
			'last_active_at' => now(),
		]);
		return $this;
	}

	public function setValidUntil()
	{
		$this->update([
			'valid_until' => now()->addMonths(self::VALID_DURATION_IN_MONTH),
		]);
		return $this;
	}

	public function isValid()
	{
		return $this->valid_until >= now()->format('Y-m-d H:i:s');
	}



	/**
	 * 	Static methods
	 * */
	public static function getByToken($token)
	{
		return self::where('token', $token)->first();
	}

	public static function removeExpireTokens()
	{
		return self::where('valid_until', '<', now()->format('Y-m-d H:i:s'))->delete();
	}
}
