<?php

namespace App\Models;

use App\MyClass\Helper;
use App\MyClass\WhatsappNew;
use Illuminate\Database\Eloquent\Model;
use DataTables;

class OffDay extends Model
{
	protected $fillable = [ 'off_day_name', 'start_date', 'end_date', 'target' ];

	const TARGET_ALL		= 'all';
	const TARGET_SELECTED 	= 'selected';


	/**
	 * 	Relationship methods
	 * */
	public function offDayDetails()
	{
		return $this->hasMany('App\Models\OffDayDetail', 'id_off_day');
	}


	public static function offDayCheck($date)
	{
		if (self::getOffDayByDate($date)) {
			return true;
		} else {
			return false;
		}
	}


	public static function getOffDayByDate($date)
	{
		$offDay = self::where('start_date', '<=', $date)
					->where('end_date', '>=', $date)
					->first();

		return $offDay;
	}


	public static function getOffDayByDateRange($startDate, $endDate)
	{
		if(!auth()->guest() && user()->isEmployee()) {
			$offDays = self::where('end_date', '>=', $startDate)
						   ->where('end_date', '<=', $endDate)
						   ->where(function($q1){
						   		$q1->where('target', 'all')
						   		   ->orWhere(function($q2){
						   			$q2->where('target', 'selected')
									   ->whereHas('offDayDetails', function($q3){
									   	$q3->where('id_employee', employee()->id);
									   });
						   		   });
						   })
						   ->get();
		} else {
			$offDays = self::where('end_date', '>=', $startDate)
						   ->where('end_date', '<=', $endDate)
						   ->get();
		}

		return $offDays;
	}


	public static function createOffDay($request)
	{
		$offDay = self::create($request->all());
		$offDay->createOffDayDetails($request);
		$offDay->setOffDayToAttendance();
		return $offDay;
	}


	public function updateOffDay($request)
	{
		$this->update($request->all());
		$this->removeOffDayDetails();
		$this->createOffDayDetails($request);
		$this->setOffDayToAttendance();
		return $this;
	}


	public function deleteOffDay()
	{
		return $this->delete();
	}


	public static function dt()
	{
		$data = self::select([ 'off_days.*' ]);

		$data = $data->where('end_date', '>=', now()->format('Y-m-d'));

		return DataTables::eloquent($data)
			->editColumn('target', function($data){
				return $data->targetText();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">';

				if(UserPermission::check('off_day', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('off_day.edit', $data->id).'" title="Edit Hari Libur">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(UserPermission::check('off_day', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('off_day.destroy', $data->id).'" title="Hapus Hari Libur">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!UserPermission::check('off_day', 'u') && !UserPermission::check('off_day', 'd')) {
					$button .= '
						<a class="dropdown-item" href="javascript:void(0);">
							Tidak Ada Aksi
						</a>';
				}

				$button .= '
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'action' ])
			->make(true);
	}


	public function startDateText()
	{
		$tt = function($date) {
			return strtotime($date);
		};

		$dateText = \App\MyClass\Date::dayName(date('N', $tt($this->start_date)));
		$dateText .= ", ";
		$dateText .= date('d', $tt($this->start_date))." ";
		$dateText .= \App\MyClass\Date::monthName(date('m', $tt($this->start_date)))." ";
		$dateText .= date('Y', $tt($this->start_date));

		return $dateText;
	}


	public function endDateText()
	{
		$tt = function($date) {
			return strtotime($date);
		};

		$dateText = \App\MyClass\Date::dayName(date('N', $tt($this->end_date)));
		$dateText .= ", ";
		$dateText .= date('d', $tt($this->end_date))." ";
		$dateText .= \App\MyClass\Date::monthName(date('m', $tt($this->end_date)))." ";
		$dateText .= date('Y', $tt($this->end_date));

		return $dateText;
	}


	public function dateText()
	{
		if($this->start_date == $this->end_date) {
			return "{$this->startDateText()}";
		} else {
			return "{$this->startDateText()} sd {$this->endDateText()}";
		}
	}


	public function createOffDayDetails($request)
	{
		if($request->target == 'selected')
		{
			$employeeIds = $request->id_employees;
			foreach($employeeIds as $employeeId)
			{
				OffDayDetail::create([
					'id_off_day'	=> $this->id,
					'id_employee'	=> $employeeId
				]);
			}
			$this->load('offDayDetails');
		}

		return $this;
	}

	public function removeOffDayDetails()
	{
		OffDayDetail::where([
			'id_off_day' => $this->id,
		])->delete();
		$this->load('offDayDetails');
		return $this;
	}

	public function getTargetEmployees()
	{
		$employees = [];
		if($this->target == 'all') {
			foreach(Employee::getActiveEmployees() as $employee) {
				$employees[] = $employee;
			}
		} else {
			foreach($this->offDayDetails as $detail) {
				if($employee = $detail->employee) {
					$employees[] = $employee;
				}
			}
		}

		return $employees;
	}

	public function getDateList()
	{
		$dates = [];
		$date = $this->start_date;
		while($date <= $this->end_date) {
			$dates[] = $date;
			$date = \Carbon\Carbon::createFromFormat('Y-m-d', $date)->addDay(1)->format('Y-m-d');
		}

		return $dates;
	}

	public function setOffDayToAttendance()
	{
		$this->load('offDayDetails.employee');
		Attendance::where('id_off_day', $this->id)->delete();

		foreach($this->getDateList() as $date)
		{
			if($date > date('Y-m-d')) {
				break;
			}

			foreach($this->getTargetEmployees() as $employee) {
				Attendance::where('id_employee', $employee->id)
						  ->where('date', $date)
						  ->where('type', Attendance::TYPE_TANPA_KETERANGAN)
						  ->delete();

				$cekCuti = Attendance::where('id_employee', $employee->id)
									 ->where('date', $date)
									 ->where('type', Attendance::TYPE_CUTI)
									 ->first();
				if($cekCuti) {
					continue;
				}

				$cekLibur = Attendance::where('id_employee', $employee->id)
										->where('date', $date)
										->where('type', Attendance::TYPE_LIBUR)
										->first();
				if($cekLibur) {
					continue;
				}

				Attendance::create([
					'id_employee'		=> $employee->id,
					'date'				=> $date,
					'type'				=> Attendance::TYPE_LIBUR,
					'description'		=> $this->off_day_name,
					'id_off_day'		=> $this->id,
					'clock_in_method' 	=> Attendance::METHOD_SYSTEM,
					'clock_out_method' 	=> Attendance::METHOD_SYSTEM,
					'late' 				=> 0,
					'overtime' 			=> 0,
					'is_overtime'		=> false,
				]);
			}
		}

		return $this;
	}

	public function targetText()
	{
		if($this->target == 'all') {
			return 'Semua Karyawan';
		} else {
			return 'Karyawan Yg Dipilih';
		}
	}


	public static function sendTomorrowIsOffDay()
	{
		$tomorrowDate = date('Y-m-d', strtotime(today()->addDays(1)));

		$offDays = self::where('start_date', $tomorrowDate)->get();

		if(count($offDays) > 0)
		{
			foreach($offDays as $offDay) {

				$offDayText = "*{$offDay->off_day_name}* pada {$offDay->dateText()}";
				foreach($this->getTargetEmployees() as $employee)
				{
					if(empty($employee->phone_number)) continue;

					if(\ActionHistory::getHistory(date('Y-m-d'), \ActionHistory::TOMORROW_IS_OFFDAY_TO_EMPLOYEE, $employee->id)) continue;

					$message = "Hai {$employee->employee_name}";
					$message .= "\n\nPemberitahuan hari libur :";
					$message .= "\n{$offDayText}";
					$message .= "\n\nTerima kasih.";
					$message .= "\n\n*Adiva Attendance System*.";

					$EndPointWa = WhatsappNew::END_POINT_WA;
					if($EndPointWa == 'WA Baru'){
						// wa Baru
						Helper::sendNotificationWhatsapp($phoneNumber = $employee->phone_number, $message);
					}else{
						// wa lama
						\App\MyClass\Whatsapp::sendChat([
							'to'		=> $employee->phone_number,
							'message'	=> $message,
						]);
					}

					\ActionHistory::createNotificationHistory(
						\ActionHistory::TOMORROW_IS_OFFDAY_TO_EMPLOYEE, 
						$employee->id, 
						"Notifikasi hari libur ke {$employee->employee_name}"
					);
				}
			}

			return true;
		}

		return true;
	}


	public static function setEmployeeOffDay($date = null)
	{
		if(empty($date)) $date = date('Y-m-d');

		$offDays = self::where('start_date', '<=', $date)
					   ->where('end_date', '>=', $date)
					   ->get();

		foreach($offDays as $offDay) {
			$offDay->setOffDayToAttendance();
		}
	}


	public function offDayDateText()
	{
		if($this->start_date == $this->end_date) {
			return \Date::fullDate($this->start_date);
		} else {
			return \Date::fullDate($this->start_date).' - '.\Date::fullDate($this->end_date);
		}
	}


	public function amountDayOfOffDay()
	{
		$start = new \Carbon\Carbon($this->start_date);
		$end = new \Carbon\Carbon($this->end_date);

		return ($start->diffInDays($end) + 1);
	}


	public static function amountDayOfOffDaysByInterval($startDate = null, $endDate = null)
	{
		$date = new \Carbon\Carbon($startDate);
		$endDate = $endDate ?? date('Y-m-d');
		$amount = 0;

		while(date('Y-m-d', strtotime($date)) <= $endDate)
		{
			if(self::offDayCheck(date('Y-m-d', strtotime($date)))) {
				$amount++;
			}
			$date->addDays(1);
		}

		return $amount;
	}

	public static function checkEmployeeIsOffDay($employeeId, $date)
	{
		$offDay = self::where('start_date', '<=', $date)
					  ->where('end_date', '>=', $date)
					  ->where('target', self::TARGET_ALL)
					  ->first();
		if($offDay) {
			return true;
		} else {
			$offDay = self::where('start_date', '<=', $date)
						  ->where('end_date', '>=', $date)
						  ->where('target', self::TARGET_SELECTED)
						  ->whereHas('offDayDetails', function($q) use ($employeeId){
						  		$q->where('id_employee', $employeeId);
						  })
						  ->first();
			if($offDay) {
				return true;
			}
		}

		return false;
	}

}
