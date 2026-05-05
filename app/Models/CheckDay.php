<?php

namespace App\Models;

use App\MyClass\Helper;
use Illuminate\Database\Eloquent\Model;
use App\MyClass\Whatsapp;
use App\MyClass\WhatsappNew;

class CheckDay extends Model
{
	protected $fillable = [ 'id_employee', 'check_day_at', 'photo', 'latitude', 'longitude' ];


	/**
	 * 	Relationship methods
	 * */
	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee');
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createCheckDay($request)
	{
		$photoBlob = base64_decode(explode(',', $request->blobImage)[1]);
		$tempFilename = date('Ymd_His_').rand(100,999).'.jpeg';
		$tempPath = \Setting::temps($tempFilename);
		\File::put($tempPath, $photoBlob);
		$employee = employee();

		if(setting('is_using_face_compare_for_attendance', 'yes') == 'yes') {
			if(!$employee->isEmployeeFaceValid($tempPath)) {
				\File::delete($tempPath);

				return \Res::invalid([
					'message'	=> 'Foto wajib menampakan wajah anda.',
				]);
			}

			if(!$employee->isLocationValid($request->latitude, $request->longitude)) {
				return \Res::invalid([
					'message'	=> 'Tidak di izinkan isi kehadiran di lokasi ini',
				]);
			}
		}

		\File::copy($tempPath, storage_path('app/public/check_day/'.$tempFilename));

		\DB::beginTransaction();
		$checkDay = self::create([
			'id_employee'	=> $employee->id,
			'check_day_at'	=> now(),
			'latitude'		=> $request->latitude,
			'longitude'		=> $request->longitude,
			'photo'			=> $tempFilename,
		]);
		\DB::commit();

		$checkDay->sendCheckDayNotification();

		\File::delete($tempPath);

		return \Res::success([
			'message'	=> 'Berhasil melakukan check day'
		]);
	}


	/**
	 * 	Helper methods
	 * */
	public function employeeName()
	{
		return $this->employee->employee_name ?? '-';
	}

	public function departmentName()
	{
		try {
			return $this->employee->departmentName();
		} catch (\Exception $e) {
			return '-';
		}
	}

	public function photoPath()
	{
		return storage_path('app/public/check_day/'.$this->photo);
	}

	public function photoLink()
	{
		return url('storage/check_day/'.$this->photo);
	}

	public function sendCheckDayNotification()
	{
		$employee = $this->employee;
		if(!$employee) return false;
		if(empty($employee->phone_number)) return false;

		$message = '';
		$message = $this->dateText();
		$message .= "\n\nTerima kasih {$employee->firstName()}, kamu telah melakukan check day hari ini";
		$message .= "\n\n*Attendance System*";

		$EndPointWa = WhatsappNew::END_POINT_WA;
        if($EndPointWa == 'WA Baru'){
            // wa Baru
            $res =  Helper::sendNotificationWhatsapp($phoneNumber = $employee->phone_number, $message, $filePath= $this->photoPath(), $caption="Check Day");
        }else{
			$res = Whatsapp::sendChat([
				'to'    => $employee->phone_number,
				'text'  => $message,
			]);

			if(!empty($this->photoPath())) {
				Whatsapp::sendMedia([
					'to'    => $employee->phone_number,
					'path'  => $this->photoPath(),
				]);
			}
		}

		return $res;
	}

	public function dateText()
	{
		return \Date::fullDateWithDayName($this->check_day_at);
	}

	public function checkDayAtText($format = 'd M Y H:i')
	{
		return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->check_day_at)->format($format);
	}


	/**
	 * 	Static methods
	 * */
	public static function dt($request)
	{
		$data = self::select([ 'check_days.*' ])
					->has('employee')
					->leftJoin('employees', 'employees.id', '=', 'check_days.id_employee')
					->leftJoin('departments', 'departments.id', '=', 'employees.id_department')
					->with([ 'employee.department', 'employee.position', 'employee.employeeGroup' ]);

		if(!empty($request->date)) {
			$data = $data->where('check_day_at', 'like', '%'.$request->date.'%');
		}

		if(!empty($departmentId = $request->id_department)) {
			if($departmentId != 'all') {
				$data = $data->whereHas('employee', function($query) use ($departmentId) {
					$query->where('id_department', $departmentId);
				});
			}
		}

		if(!empty($positionId = $request->id_position)) {
			if($positionId != 'all') {
				$data = $data->whereHas('employee', function($query) use ($positionId) {
					$query->where('id_position', $positionId);
				});
			}
		}

		if(!empty($employeeGroupId = $request->id_employee_group)) {
			if($employeeGroupId != 'all') {
				$data = $data->whereHas('employee', function($query) use ($employeeGroupId) {
					$query->where('id_employee_group', $employeeGroupId);
				});
			}
		}

		$data = $data->get();

		return \DataTables::of($data)
			->addColumn('employee_name', function($data){
				return "<a href='".route('employee.detail', $data->id_employee)."' target='_blank'> {$data->employeeName()} </a>";
			})
			->addColumn('department_name', function($data){
				return $data->departmentName();
			})
			->editColumn('check_day_at', function($data){
				return $data->checkDayAtText('H:i:s').' WIB';
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('check_day.detail', $data->id).'" title="Detail Check Day">
							<i class="mdi mdi-magnify"></i> Detail 
						</a>';

				if(UserPermission::check('attendance', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('check_day.destroy', $data->id).'" title="Hapus Check Day">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				$button .= '
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'employee_name', 'action' ])
			->make(true);
	}
}
