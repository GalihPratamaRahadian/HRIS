<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DataTables;

class Shift extends Model
{
	protected $fillable = [ 'shift_name', 'clock_start_limit', 'clock_start', 'clock_end', 'late_tolerance', 'offday_shift' ];


	/**
	 * 	Relationship
	 * */
	public function shiftDetails()
	{
		return $this->hasMany('App\Models\ShiftDetail', 'id_shift')->orderBy('day', 'asc');
	}

	public function shiftDetail()
	{
		return $this->hasOne('App\Models\ShiftDetail', 'id_shift');
	}

	public function activeEmployees()
	{
		return $this->hasMany('App\Models\Employee', 'id_shift')->where('status', Employee::STATUS_ACTIVE);
	}

	public function isHasShiftDetails()
	{
		return count($this->shiftDetails) > 0;
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createShift($request)
	{
		$shift = self::create([
			'shift_name'		=> $request->shift_name,
			'clock_start_limit'	=> $request->clock_start_limit,
			'clock_start'		=> $request->clock_start,
			'clock_end'			=> $request->clock_end,
			'late_tolerance'	=> $request->late_tolerance ?? 0,
		]);

		$shift->setOffdayShift($request->offday_shift)
			  ->setShiftDetails($request);

		return $shift;
	}

	public function updateShift($request)
	{
		$this->update([
			'shift_name'		=> $request->shift_name,
			'clock_start_limit'	=> $request->clock_start_limit,
			'clock_start'		=> $request->clock_start,
			'clock_end'			=> $request->clock_end,
			'late_tolerance'	=> $request->late_tolerance ?? 0,
		]);

		$this->setOffdayShift($request->offday_shift)
			  ->setShiftDetails($request);

		return $this;
	}

	public function deleteShift()
	{
		foreach($this->shiftDetails as $shiftDetail) {
			$shiftDetail->delete();
		}

		return $this->delete();
	}

	public function setOffdayShift($offdayShift)
	{
		$this->update([
			'offday_shift'	=> serialize($offdayShift),
		]);

		return $this;
	}

	public function getOffdayShift()
	{
		try {
			$data = unserialize($this->offday_shift);
			if(!is_array($data)) return [];

			return $data;
		} catch (\Exception $e) {
			return [];
		}
	}



	/**
	 * 	Triggered CRUD methods
	 * */
	public function setShiftDetails($request)
	{
		if(!empty($request->shift_detail_day)) {
			foreach($this->shiftDetails as $shiftDetail) {
				$shiftDetail->delete();
			}

			$days = $request->shift_detail_day;
			$clockStarts = $request->shift_detail_clock_start;
			$clockEnds = $request->shift_detail_clock_end;
			$iteration = 0;

			foreach($days as $day)
			{
				$this->setShiftDetail($day, $clockStarts[$iteration], $clockEnds[$iteration]);
				$iteration++;
			}
		}
		
		return $this;
	}



	/**
	 * 	Helper methods
	 * */
	public function clockStartLimitText($format = 'H:i:s')
	{
		return date($format, strtotime($this->clock_start_limit));
	}

	public function clockStartText($format = 'H:i:s')
	{
		return date($format, strtotime($this->clock_start));
	}

	public function clockEndText($format = 'H:i:s')
	{
		return date($format, strtotime($this->clock_end));
	}


	public function offDayCheck($date)
	{
		$dayNumber = $this->dateToDayNumber($date);
		$offdayShift = unserialize($this->offday_shift);
		$offdayShift = is_array($offdayShift) ? $offdayShift : [];

		if (in_array($dayNumber, $offdayShift)) {
			return true;
		} else {
			return false;
		}
	}

	public function todayShift()
	{
		$date = date('Y-m-d');
		return $this->getShiftByDate($date);
	}

	public function getShiftByDate($date = null)
	{
		// Libur
		if($this->offDayCheck($date)) return false;

		$dayNumber = $this->dateToDayNumber($date);

		if($this->isHasShiftDetails()) {
			$shiftDetail = ShiftDetail::where('id_shift', $this->id)->where('day', $dayNumber)->first();
			if($shiftDetail) {
				return $shiftDetail;
			}
		}

		return $this;
	}


	public function getShiftMinutes($date)
	{
		if($this->offDayCheck($date)) return 0;
		$shift = $this->getShiftByDate($date);
		$shiftMinutes = round((strtotime($shift->clock_end) - strtotime($shift->clock_start)) / 60);

		return $shiftMinutes;
	}


	public function getShiftHours($date)
	{
		$shiftMinutes = $this->getShiftMinutes($date);
		$shiftHours = $shiftMinutes / 60;

		return $shiftHours;
	}


	public function getLateMinutes($clock = null, $date = null)
	{
		if(empty($clock)) $clock = date('H:i');
		if(empty($date)) $date = today();
		$shift = $this->getShiftByDate($date);

		if(!$shift) return 0;

		$clockNowMinutes = strtotime($clock) / 60;
		$clockStartMinutes = strtotime($shift->clock_start) / 60;

		if($clockNowMinutes < $clockStartMinutes) return 0;

		$late = $clockNowMinutes - $clockStartMinutes;
		$lateAfterTolerance = $late - $this->late_tolerance;

		return $lateAfterTolerance >= 0 ? $lateAfterTolerance : 0;
	}


	public function dateToDayNumber($date)
	{
		if(is_numeric($date)) return $date;

		return date('N', strtotime($date));
	}


	public function offdayShiftText()
	{
		try {
			$text = "";
			$offdayShift = unserialize($this->offday_shift);
			if(empty($offdayShift)) return '-';

			foreach($offdayShift as $offday) {
				$text .= ", ".\App\MyClass\Date::dayName($offday);
			}
			$text = substr($text, 2);

			return $text;
			
		} catch (Exception $e) {
			return '-';
		}
	}


	public function isAllowForClockOutNow()
	{
		return date('H:i') >= $this->getShiftByDate(today())->clock_end ? true : false;
	}
	

	public static function dataTable($request)
	{
		$data = self::all();

		return DataTables::of($data)
			->addColumn('offday', function($data){
				return $data->offdayShiftText();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('admin.shift.detail', $data->id).'" title="Detail Jam Kerja">
							<i class="mdi mdi-magnify"></i> Detail 
						</a>';

				if(UserPermission::check('shift', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('admin.shift.edit', $data->id).'" title="Edit Jam Kerja">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(UserPermission::check('shift', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('admin.shift.destroy', $data->id).'" title="Hapus Jam Kerja">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!UserPermission::check('shift', 'u') && !UserPermission::check('shift', 'd')) {
					$button .= '
						<a class="dropdown-item" href="javascript:void(0);">
							Tidak Ada Aksi
						</a>';
				}

				return $button;
			})
			->rawColumns([ 'action' ])
			->make(true);
	}



	public static function setEmployeeOffDay()
	{
		$day = date('N');
		
		foreach(self::all() as $shift)
		{
			if(in_array($day, $shift->getOffdayShift()))
			{
				$employees = Employee::where('id_shift', $shift->id)
									->where('status', Employee::STATUS_ACTIVE)
									->get();

				foreach($employees as $employee)
				{
					$attendance = Attendance::where('date', date('Y-m-d'))
											->where('id_employee', $employee->id)
											// ->where('type', Attendance::TYPE_LIBUR)
											->first();
					if(!$attendance)
					{
						Attendance::create([
							'id_employee' 		=> $employee->id,
							'date' 				=> date('Y-m-d'),
							'clock_in_method' 	=> Attendance::METHOD_SYSTEM,
							'clock_out_method' 	=> Attendance::METHOD_SYSTEM,
							'type' 				=> Attendance::TYPE_LIBUR,
							'late' 				=> 0,
							'overtime' 			=> 0,
							'is_overtime'		=> false,
							'description'		=> 'Libur shift',
						]);
					}
				}

			}

		}
	}


	public function clockStartTwelveHoursText($today = true)
	{
		try {
			if($today) {
				$clockStart = $this->todayShift()->clock_start;
			} else {
				$clockStart = $this->clock_start;
			}
			
			return date('g:i A', strtotime($clockStart));
		} catch (\Exception $e) {
			return '-';
		}
	}

	public function clockEndTwelveHoursText($today = true)
	{
		try {
			if($today) {
				$clockEnd = $this->todayShift()->clock_end;
			} else {
				$clockEnd = $this->clock_end;
			}
		
			return date('g:i A', strtotime($clockEnd));
		} catch (\Exception $e) {
			return '-';
		}
	}

	public function shiftTimeTwelveHoursText($today = true)
	{
		return "{$this->clockStartTwelveHoursText($today)} - {$this->clockEndTwelveHoursText($today)}";
	}


	public function amountOfWorkDayInMonth($date = null)
	{
		if(empty($date)) $date = date('Y-m-d');
		$tt = function($format) use ($date) { return date($format, strtotime($date)); };

		$amountOfDay = $tt('t');
		$yearMonth = $tt('Y-m');
		$offday = $this->getOffdayShift();
		$amountOfOffDay = OffDay::amountDayOfOffDaysByInterval($tt('Y-m-01'), $tt('Y-m-t'));

		$amountOfWorkDay = 0;

		for($i = 1; $i <= $amountOfDay; $i++) 
		{
			$date = "{$yearMonth}-{$i}";
			$dayNumber = date('N', strtotime($date));
			if(!in_array($dayNumber, $offday)) {
				$amountOfWorkDay++;
			}
		}


		return $amountOfWorkDay - $amountOfOffDay;
	}


	public function amountOfShiftOffDayInMonth($date = null)
	{
		if(empty($date)) $date = date('Y-m-d');
		$tt = function($format) use ($date) { return date($format, strtotime($date)); };

		$amountOfDay = $tt('t');
		$yearMonth = $tt('Y-m');
		$offday = $this->getOffdayShift();

		$amountOfOffDay= 0;

		for($i = 1; $i <= $amountOfDay; $i++) 
		{
			$date = "{$yearMonth}-{$i}";
			$dayNumber = date('N', strtotime($date));
			if(in_array($dayNumber, $offday)) {
				$amountOfOffDay++;
			}
		}

		return $amountOfOffDay;
	}


	public function setShiftDetail($day, $clockStart, $clockEnd)
	{
		$shiftDetail = ShiftDetail::where('id_shift', $this->id)
								  ->where('day', $day)
								  ->first();
		if($shiftDetail) {
			$shiftDetail->update([
				'clock_start'	=> $clockStart,
				'clock_end'		=> $clockEnd
			]);
		} else {
			$shiftDetail = ShiftDetail::create([
				'id_shift'		=> $this->id,
				'day'			=> $day,
				'clock_start'	=> $clockStart,
				'clock_end'		=> $clockEnd
			]);
		}

		return $this;
	}


	public function clockStartWithDate($date = null)
	{
		if(empty($date)) $date = now()->format('Y-m-d');
		$datetime = new \Carbon\Carbon($date." ".$this->clock_start);
		return $datetime->format('Y-m-d H:i:s');
	}


	public function clockEndWithDate($date = null)
	{
		if(empty($date)) $date = now()->format('Y-m-d');
		$clockEndWithDate = $date." ".$this->clock_end;

		if($this->clock_start > $this->clock_end) {
			$clockEndWithDate = new \Carbon\Carbon($clockEndWithDate);
			$clockEndWithDate->addDays(1);
			$clockEndWithDate = date('Y-m-d', strtotime($clockEndWithDate)).' '.$this->clock_end;
		}

		$datetime = new \Carbon\Carbon($clockEndWithDate);
		return $datetime->format('Y-m-d H:i:s');
	}
}
