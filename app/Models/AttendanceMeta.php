<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceMeta extends Model
{
	protected $fillable = [ 'id_attendance', 'clock_in_photo', 'clock_out_photo', 'clock_in_location', 'clock_out_location', 'id_clock_in_face_terminal_log', 'id_clock_out_face_terminal_log' ];


	public function attendance()
	{
		return $this->belongsTo('App\Models\Attendance', 'id_attendance');
	}


	public function clockInFaceTerminalLog()
	{
		return $this->belongsTo('App\Models\FaceTerminalLog', 'id_clock_in_face_terminal_log');
	}


	public function clockOutFaceTerminalLog()
	{
		return $this->belongsTo('App\Models\FaceTerminalLog', 'id_clock_out_face_terminal_log');
	}


	public function clockInPhotoPath()
	{
		return storage_path('app/public/attendance/'.$this->clock_in_photo);
	}


	public function clockInPhotoLink()
	{
		if($this->isHasClockInPhoto()) {
			return url('storage/attendance/'.$this->clock_in_photo);
		}

		return url('images/no-image.jpg');
	}


	public function clockOutPhotoPath()
	{
		return storage_path('app/public/attendance/'.$this->clock_out_photo);
	}


	public function clockOutPhotoLink()
	{
		if($this->isHasClockOutPhoto()) {
			return url('storage/attendance/'.$this->clock_out_photo);
		}

		return url('images/no-image.jpg');
	}


	public function isHasClockInFaceTerminalLog()
	{
		if(empty($this->id_clock_in_face_terminal_log)) return false;

		return !empty($this->clockInFaceTerminalLog);
	}


	public function isHasClockOutFaceTerminalLog()
	{
		if(empty($this->id_clock_out_face_terminal_log)) return false;

		return !empty($this->clockOutFaceTerminalLog);
	}


	public function isHasClockInPhoto()
	{
		if(empty($this->clock_in_photo)) return false;

		return \File::exists($this->clockInPhotoPath());
	}


	public function isHasClockOutPhoto()
	{
		if(empty($this->clock_out_photo)) return false;
		
		return \File::exists($this->clockOutPhotoPath());
	}


	public function isHasClockInLocation()
	{
		return !empty($this->clock_in_location);
	}


	public function isHasClockOutLocation()
	{
		return !empty($this->clock_out_location);
	}


	public function getClockInLocation()
	{
		$location = false;

		if($this->isHasClockInLocation()) {
			$location = unserialize($this->clock_in_location);
		}

		return $location;
	}


	public function getClockOutLocation()
	{
		$location = false;

		if($this->isHasClockOutLocation()) {
			$location = unserialize($this->clock_out_location);
		}

		return $location;
	}

}
