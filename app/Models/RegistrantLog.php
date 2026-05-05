<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrantLog extends Model
{
	protected $fillable = [ 'id_registrant', 'status', 'description' ];

	const STATUS_UNFILL		= 1;
	const STATUS_WAITING	= 2;
	const STATUS_APPROVED	= 3;
	const STATUS_REJECTED	= 4;


	public static function availableStatus()
	{
		return [
			self::STATUS_UNFILL		=> 'Belum Mengisi',
			self::STATUS_WAITING	=> 'Menunggu Persetujuan',
			self::STATUS_APPROVED	=> 'Telah disetujui',
			self::STATUS_REJECTED	=> 'Ditolak',
		];
	}


	public function registrant()
	{
		return $this->belongsTo('App\Models\Registrant', 'id_registrant');
	}


	public function isStatusUnfill()
	{
		return $this->status == self::STATUS_UNFILL;
	}


	public function isStatusWaiting()
	{
		return $this->status == self::STATUS_WAITING;
	}


	public function isStatusApproved()
	{
		return $this->status == self::STATUS_APPROVED;
	}


	public function isStatusRejected()
	{
		return $this->status == self::STATUS_REJECTED;
	}


	public function statusText()
	{
		try {
			return self::availableStatus()[$this->status];
		} catch (Exception $e) {
			return '-';
		}
	}


	public function statusHtml()
	{
		if($this->isStatusUnfill()) return "<span class='text-warning'> {$this->statusText()} </span>";
		if($this->isStatusWaiting()) return "<span class='text-primary'> {$this->statusText()} </span>";
		if($this->isStatusApproved()) return "<span class='text-success'> {$this->statusText()} </span>";
		if($this->isStatusRejected()) return "<span class='text-danger'> {$this->statusText()} </span>";
	}


	public function statusBadgeHtml()
	{
		if($this->isStatusUnfill()) return "<span class='badge badge-warning'> {$this->statusText()} </span>";
		if($this->isStatusWaiting()) return "<span class='badge badge-primary'> {$this->statusText()} </span>";
		if($this->isStatusApproved()) return "<span class='badge badge-success'> {$this->statusText()} </span>";
		if($this->isStatusRejected()) return "<span class='badge badge-danger'> {$this->statusText()} </span>";
	}


	public function descriptionText()
	{
		return $this->description ?? '-';
	}


	public function createdAtText()
	{
		return \Date::fullDateWithTime($this->created_at);
	}
}
