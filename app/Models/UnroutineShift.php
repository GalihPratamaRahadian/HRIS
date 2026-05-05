<?php

namespace App\Models;

use App\MyClass\Helper;
use App\MyClass\Whatsapp;
use App\MyClass\WhatsappNew;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UnroutineShift extends Model
{
	protected $fillable = [ 'id_employee', 'date', 'type', 'clock_start_limit', 'clock_start', 'clock_end', 'late_tolerance' ];


	/**
	 * 	Relationship
	 * */
	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee');
	}

	public function employeeName()
	{
		return $this->employee->employee_name ?? '';
	}


	/**
	 * 	CRUD Methods
	 * */
	public static function cretaeUnroutineShift(array $request)
	{
		return self::create($request);
	}

	public function updateUnroutineShift(array $request)
	{
		$this->update($request);
		return $this;
	}

	public function deleteUnroutineShift()
	{
		return $this->delete();
	}


	/**
	 * 	Helper methods
	 * */
	public function isAllowForClockin()
	{

	}

	public function isAllowForClockout()
	{

	}

	public function dateText($format = 'Y-m-d')
	{
		return date($format, strtotime($this->date));
	}

	public function clockStartLimitText($format = 'H:i')
	{
		if(empty($this->clock_start_limit)) return '-';
		return date($format, strtotime($this->clock_start_limit));
	}

	public function clockStartText($format = 'H:i')
	{
		if(empty($this->clock_start)) return '-';
		return date($format, strtotime($this->clock_start));
	}

	public function clockEndText($format = 'H:i')
	{
		if(empty($this->clock_end)) return '-';
		return date($format, strtotime($this->clock_end));
	}

	public function isOffDay()
	{
		return $this->type == 'libur';
	}

	public function clockStartTwelveHoursText()
	{
		return date('g:i A', strtotime($this->clock_start));
	}

	public function clockEndTwelveHoursText()
	{
		return date('g:i A', strtotime($this->clock_end));
	}

	public function shiftTimeTwelveHoursText()
	{
		return "{$this->clockStartTwelveHoursText()} - {$this->clockEndTwelveHoursText()}";
	}

	public function clockStartWithDate()
	{
		$datetime = new \Carbon\Carbon($this->date." ".$this->clock_start);
		return $datetime->format('Y-m-d H:i:s');
	}

	public function clockEndWithDate()
	{
		$clockEndWithDate = new \Carbon\Carbon($this->date." ".$this->clock_end);
		
		if($this->clock_start > $this->clock_end) {
			$clockEndWithDate->addDays(1);
		}

		return $clockEndWithDate->format('Y-m-d H:i:s');
	}



	/**
	 * 	Static methods
	 * */
	public static function importFromExcel($request, $employee)
	{
		$amount = 0;

		if(!empty($request->file))
		{
			$employeeId = $request->id_employee;
			$file = $request->file('file');
			$extension = $file->getClientOriginalExtension();
			$filename = date('Ymdhis').'_Jam_Kerja_Harian.'.$extension;
			$tempPath = \Helper::tempsPath();
			$filepath = $tempPath.'/'.$filename;
			$file->move($tempPath, $filename);
			$parseData = \App\MyClass\SimpleXLSX::parse($filepath);

			if($parseData)
			{
				$iter = 0;
				foreach($parseData->rows() as $row)
				{
					$iter++;
					if($iter == 1) continue;

					\DB::beginTransaction();
					try {
						$date = $row[0];
						$type = strtolower($row[1]);
						$clockStartLimit = $row[2];
						$clockStart = $row[3];
						$clockEnd = $row[4];
						$lateTolerance = 0;
						if(!empty($row[5])) {
							if(is_numeric($row[5])) $lateTolerance = (int) $row[5];
						}

						if(!empty($date) && !empty($type))
						{
							if(!in_array($type, [ 'libur', 'masuk' ])) continue;
							if($type == 'masuk' && (empty($clockStartLimit) || empty($clockStart) || empty($clockEnd))) continue;

							if(strlen(10)) {
								$date = Carbon::createFromFormat('d/m/Y', $date);
							} else {
								$date = Carbon::createFromFormat('d/m/y', $date);
							}

							$unroutineShift = self::where('date', $date->format('Y-m-d'))
												  ->where('id_employee', $employee->id)
												  ->first();

							// \App\MyClass\Whatsapp::sendChat([
							// 	'to'	=> '6282316425264',
							// 	'text'	=> $date->format('d M Y').' - '.$type,
							// ]);

							$EndPointWa = WhatsappNew::END_POINT_WA;
							$message = $date->format('d M Y').' - '.$type;
							if($EndPointWa == 'WA Baru'){
								// wa Baru
								$res = Helper::sendNotificationWhatsapp($phoneNumber = '6282316425264', $message);
							}else{
								$res = Whatsapp::sendChat([
									'to'    => '6282316425264',
									'text'  => $message,
								]);
							}

							if($type == 'libur') {
								$data = [
									'id_employee'		=> $employee->id,
									'date'				=> $date,
									'type'				=> $type,
									'clock_start_limit'	=> null,
									'clock_start'		=> null,
									'clock_end'			=> null,
									'late_tolerance'	=> 0,
								];

								if($unroutineShift) {
									$unroutineShift->update($data);
								} else {
									self::create($data);
								}
								$amount++;
							} elseif($type == 'masuk') {
								$data = [
									'id_employee'		=> $employee->id,
									'date'				=> $date,
									'type'				=> $type,
									'clock_start_limit'	=> $clockStartLimit.':00',
									'clock_start'		=> $clockStart.':00',
									'clock_end'			=> $clockEnd.':00',
									'late_tolerance'	=> $lateTolerance,
								];

								if($unroutineShift) {
									$unroutineShift->update($data);
								} else {
									self::create($data);
								}
								$amount++;
							} else {
								Helper::sendNotificationWhatsapp($phoneNumber = '6282316425264', $message = 'not found');
							}
						}
						\DB::commit();
					} catch (\Exception $e) {
						\DB::rollback();
					}
				}
			}

			\File::delete($filepath);
		}

		return $amount;
	}


	public static function dt($request)
	{
		$data = Employee::select([ 'employees.*' ])
					->with([ 'department', 'position', 'shift' ])
					->where('shift_type', 'unroutine')
					->leftJoin('departments', 'employees.id_department', '=', 'departments.id')
					->leftJoin('positions', 'employees.id_position', '=', 'positions.id')
					->leftJoin('shifts', 'employees.id_shift', '=', 'shifts.id');

		if(!empty($request->id_department)) {
			$departmentID = $request->id_department;

			if($departmentID != 'all') {
				if ($departmentID == 'no') {
					$data = $data->where('employees.id_department', null);
				} else {
					$data = $data->where('employees.id_department', $request->id_department);
				}
			}
		}

		if(!empty($request->id_shift)) {
			$shiftID = $request->id_shift;

			if($shiftID != 'all') {
				if ($shiftID == 'no') {
					$data = $data->where('employees.id_shift', null);
				} else {
					$data = $data->where('employees.id_shift', $request->id_shift);
				}
			}
		}

		if(!empty($request->employee_status)) {
			$status = $request->employee_status;

			if($status != 'all') {
				$data = $data->where('employees.status', $status);
			}
		}

		return \DataTables::eloquent($data)
			->editColumn('employee_number', function($data){
				return $data->employee_number ?? '-';
			})
			->addColumn('department_name', function($data){
				return $data->departmentName();
			})
			->addColumn('position_name', function($data){
				return $data->positionName();
			})
			->addColumn('shift_name', function($data){
				return $data->shiftName();
			})
			->editColumn('job_status', function($data){
				return $data->jobStatusText();
			})
			->editColumn('status', function($data){
				return $data->statusHtml();
			})
			->addColumn('action', function($data){

				$button = '
				<a class="btn btn-primary py-2" href="'. route('unroutine_shift.employee_detail', $data->id) .'">
					<i class="mdi mdi-magnify"></i> Lihat Shift
				</a>';

				return $button;
			})
			->rawColumns([ 'status', 'action' ])
			->make(true);
	}


	public static function unroutineShiftDt($request, $employee)
	{
		$data = self::select([ 'unroutine_shifts.*' ])
					->with([ 'employee.department', 'employee.position' ])
					->where('id_employee', $employee->id);


		return \DataTables::eloquent($data)
			->editColumn('date', function($data){
				return $data->dateText('d M Y');
			})
			->editColumn('type', function($data){
				return ucfirst($data->type);
			})
			->editColumn('clock_start_limit', function($data){
				return $data->clockStartLimitText();
			})
			->editColumn('clock_start', function($data){
				return $data->clockStartText();
			})
			->editColumn('clock_end', function($data){
				return $data->clockEndText();
			})
			->editColumn('late_tolerance', function($data){
				if($data->type == 'libur') return '-';
				return $data->late_tolerance.' menit';
			})
			->addColumn('action', function($data){

				$button = '
				<a class="btn btn-primary py-2" href="'. route('unroutine_shift.employee_detail', $data->id) .'">
					<i class="mdi mdi-magnify"></i> Lihat Shift
				</a>';

				return $button;
			})
			->rawColumns([ 'status', 'action' ])
			->make(true);
	}

}
