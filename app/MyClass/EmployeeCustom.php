<?php

namespace App\MyClass;

use App\Models\Employee;

class EmployeeCustom
{
	public $employee;
	public $isValid;

	public function __construct($id)
	{
		$this->employee = Employee::find($id);
		$this->isValid = !empty($this->employee);
	}

	public static function make($id)
	{
		return new self($id);
	}


	public function isMustAttend()
	{
		if($this->isValid)
		{
			$isMustAttend = true;
			$shift = $this->employee->getTodayShift();
			if(!$shift) {
				$isMustAttend = false;
			}
			
			$attendance = $this->employee->latestAttendance;
			if($attendance) {
				if($attendance->date == date('Y-m-d')) {
					$isMustAttend = false;
				}

				if(empty($attendance->clock_out_at) && $attendance->date !== date('Y-m-d')) {
					$isMustAttend = false;
				}
			}

			if($shift) {
				$time = date('H:i:s');
				$clockStart = $shift->clock_start;
				$clockEnd = $shift->clock_end;

				if($clockStart > $time) {
					$isMustAttend = false;
				}

				if($clockEnd <= $time) {
					$isMustAttend = false;
				}
			}

			return $isMustAttend;
		}

		return false;
	}


	public function sendHaventFilledAttendanceNotification()
	{
		if($this->isMustAttend())
		{
			$now = date('H:i:s');
			$shift = $this->employee->shift;
			$clockStart = $shift->clock_start;
			
			if($shift->late_tolerance > 0) {
				$clockStartValid = today()->setHour(date('H', strtotime($clockStart)))
										  ->setMinute(date('i', strtotime($clockStart)))
										  ->setSecond(date('s', strtotime($clockStart)))
										  ->addMinutes($shift->late_tolerance);

				$clockStart = date('H:i:s', strtotime($clockStartValid));
			}

			$message = \Date::fullDateWithDayName(date('Y-m-d'));
			$message .= "\n". date('H:i:s') ." WIB";
			$message .= "\n\nHalo *". $this->employee->employee_name ."*";

			if($now <= $clockStart) {
				$message .= "\nSekarang sudah memasuki jam masuk, segera lakukan pengisian kehadiran";
			} else {
				$timeNow = new \Carbon\Carbon(date('Y-m-d'). ' '.$now);
				$timeClockStart = new \Carbon\Carbon(date('Y-m-d'). ' '.$clockStart);
				$lateInMinutes = $timeNow->diffInMinutes($timeClockStart);
				$lateInHours = floor($lateInMinutes / 60);
				$lateInMinutes = $lateInMinutes - ($lateInHours * 60);

				$message .= "\nSegera lakukan pengisian kehadiran.";
				$message .= "\nKamu telah terlambat selama ";
				if($lateInHours > 0) $message .= $lateInHours ." jam";
				if($lateInHours > 0 && $lateInMinutes > 0) $message .= " "; 
				if($lateInMinutes > 0) $message .= $lateInMinutes. " menit"; 
			}

			$message .= "\n\n*Attendance System*";
			$EndPointWa = WhatsappNew::END_POINT_WA;
			if($EndPointWa == 'WA Baru'){
				// wa Baru
				$res = Helper::sendNotificationWhatsapp($phoneNumber = $this->employee->phone_number, $message);
			}else{
				$res = Whatsapp::sendChat([
					'to'    => $this->employee->phone_number,
					'text'  => $message,
				]);
			}
		}
	}
}