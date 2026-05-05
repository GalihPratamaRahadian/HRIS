<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaceTerminalUser extends Model
{
	protected $fillable = [ 'type', 'id_reference', 'employee_number', 'card_number', 'finger', 'valid_start', 'valid_end', 'status' ];

	const TYPE_EMPLOYEE	= 'employee';
	const TYPE_VISITOR	= 'visitor';


	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_reference');
	}


	public function visitor()
	{
		return $this->belongsTo('App\Visitor', 'id_reference');
	}


	public function photoLink()
	{
		$photoLink = $this->type == self::TYPE_EMPLOYEE ? $this->employee->photoLink() : false;
		$photoLink = $this->type == self::TYPE_VISITOR ? $this->visitor->photoLink() : $photoLink;

		return $photoLink;
	}


	public static function createFaceTerminalUser($data)
	{
		$faceTerminalUser = self::create($data);
		$faceTerminalUser->setDefaultSetting();

		return $faceTerminalUser;
	}


	public function setDefaultSetting()
	{
		$this->update([
			'employee_number'	=> $this->id,
			'card_number'		=> $this->id
		]);

		return $this;
	}


	public function isEmployee()
	{
		return $this->type == self::TYPE_EMPLOYEE ? true : false;
	}


	public function isVisitor()
	{
		return $this->type == self::TYPE_VISITOR ? true : false;
	}

}
