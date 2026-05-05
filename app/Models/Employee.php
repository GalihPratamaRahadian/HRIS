<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Intervention\Image\Facades\Image;
use DataTables;
use App\MyClass\Helper;
use App\MyClass\Photo;
use App\MyClass\MyImage;
use App\MyClass\Whatsapp;
use App\MyClass\WhatsappNew;

class Employee extends Model
{
	use SoftDeletes;

	protected $fillable = [ 'employee_number', 'employee_name', 'gender', 'email', 'phone_number', 'jamsostek', 'job_status', 'photo', 'id_department', 'id_position', 'shift_type', 'id_shift', 'id_user', 'id_employee_group', 'status', 'start_working_date', 'place_of_birth', 'date_of_birth', 'address', 'last_education', 'last_education_major', 'marital_status', 'blood_type', 'ktp_number', 'npwp_number', 'bank_name', 'bank_account_number' ] ;


	const JOBSTATUS_KONTRAK	= 'kontrak';
	const JOBSTATUS_TETAP	= 'tetap';
	const JOBSTATUS_PROBATION = 'probation';

	const STATUS_ACTIVE		= 1;
	const STATUS_NOTACTIVE	= 2;
	const STATUS_INACTIVE	= 2;


	/**
	 * 	Relationship
	 * */
	public function department()
	{
		return $this->belongsTo('App\Models\Department', 'id_department')->withTrashed();
	}

	public function position()
	{
		return $this->belongsTo(Position::class, 'id_position')->withTrashed();
	}

	public function shift()
	{
		return $this->belongsTo('App\Models\Shift', 'id_shift');
	}

	public function employeeGroup()
	{
		return $this->belongsTo('App\Models\EmployeeGroup', 'id_employee_group');
	}

	public function faceTerminalUser()
	{
		return $this->hasOne('App\Models\FaceTerminalUser', 'id_reference')
					->where('type', FaceTerminalUser::TYPE_EMPLOYEE);
	}

	public function employeeContract()
	{
		return $this->hasOne('App\Models\EmployeeContract', 'id_employee');
	}

	public function employeeSalary()
	{
		return $this->hasOne('App\Models\EmployeeSalary', 'id_employee');
	}

	public function employeeLeaveQuota()
	{
		return $this->hasOne('App\Models\EmployeeLeaveQuota', 'id_employee');
	}

	public function attendance()
	{
		return $this->hasOne('App\Models\Attendance', 'id_employee')->orderBy('created_at', 'desc');
	}

	public function todayAttendance()
	{
		return $this->hasOne('App\Models\Attendance', 'id_employee')->where('date', date('Y-m-d'));
	}

	public function todayAttendanceTypeHadir()
	{
		return $this->hasOne('App\Models\Attendance', 'id_employee')
					->where('date', date('Y-m-d'))
					->where('type', Attendance::TYPE_HADIR);
	}

	public function latestAttendance()
	{
		return $this->hasOne('App\Models\Attendance', 'id_employee')
					->orderBy('created_at', 'desc');
	}

	public function user()
	{
		return $this->belongsTo('App\User', 'id_user');
	}

	public function webAttendancePermission()
	{
		return $this->hasOne('App\Models\WebAttendancePermission', 'id_employee');
	}

	public function salesEmployee()
	{
		return $this->hasOne('App\Models\SalesEmployee', 'id_employee');
	}

	public function trackingEmployee()
	{
		return $this->hasOne('App\Models\TrackingEmployee', 'id_employee');
	}

	public function employeeGroupName()
	{
		return $this->employeeGroup->group_name ?? '-';
	}

	public function departmentName()
	{
		return $this->department->department_name ?? '-';
	}

	public function shiftName()
	{
		return $this->shift->shift_name ?? '-';
	}

	public function positionName()
	{
		return $this->position->position_name ?? '-';
	}

	public function username()
	{
		return $this->user->username ?? '-';
	}

	public static function getEmployeeWithJobStatusContract()
	{
		return self::where('job_status', self::JOBSTATUS_KONTRAK)->get();
	}

	public static function getEmployeeWithNoSalary()
	{
		return self::doesntHave('employeeSalary')->get();
	}

	public static function getActiveEmployees()
	{
		return self::where('status', self::STATUS_ACTIVE)
				   ->with([ 'department', 'position', 'salesEmployee', 'shift' ])
				   ->orderBy('employee_name', 'asc')
				   ->get();
	}



	public function firstName()
	{
		$firstName = explode(' ', $this->employee_name)[0];
		return $firstName;
	}



	public function isHasSalary()
	{
		if(!empty($this->employeeSalary)) {
			return $this->employeeSalary;
		}

		return false;
	}

	public function isHasContract()
	{
		if(!empty($this->activeEmployeeContract)) {
			return $this->activeEmployeeContract;
		}

		return false;
	}

	public function isHasSalesEmployee()
	{
		return !empty($this->salesEmployee);
	}

	public function isHasTrackingEmployee()
	{
		return !empty($this->trackingEmployee);
	}

	public function employeeEducations()
	{
		return $this->hasMany('App\Models\EmployeeEducation', 'id_employee')->orderBy('year_start', 'desc');
	}

	public function employeeFamilies()
	{
		return $this->hasMany('App\Models\EmployeeFamily', 'id_employee')->orderBy('date_of_birth', 'asc');
	}

	public function employeeTrainings()
	{
		return $this->hasMany('App\Models\EmployeeTraining', 'id_employee')->orderBy('date_start', 'desc');
	}


	public function isOffDay($date = null)
	{
		if(empty($date)) $date = date('Y-m-d');

		if($this->isShiftTypeRoutine()) {
			if(!$this->isHasShift()) return false;

			// Libur shift
			if($this->shift->offDayCheck($date)) return true;
		} else {
			$shift = $this->shiftByDate($date);
			if($shift) {
				return $shift->isOffDay();
			}
		}

		// Off day (libur serentak)
		return OffDay::checkEmployeeIsOffDay($this->id, $date);
	}


	public function photoPath($size = null)
	{
		$path = $size == null ? storage_path('app/public/employee/original/'.$this->photo) : false;
		$path = $size == 'small' || $size == 'thumb' ? storage_path('app/public/employee/thumb/'.$this->photo) : $path;
		$path = $size == 'face' ? storage_path('app/public/employee/face/'.$this->photo) : $path;

		return $path;
	}


	public function photoLink($size = null)
	{
		$path = $size == null ? url('storage/employee/original/'.$this->photo) : false;
		$path = $size == 'small' || $size == 'thumb' ? url('storage/employee/thumb/'.$this->photo) : $path;
		$path = $size == 'face' ? url('storage/employee/face/'.$this->photo) : $path;

		return $path;
	}


	public function jobStatusText()
	{
		$jobStatusText = $this->job_status == self::JOBSTATUS_TETAP ? 'Tetap' : '';
		$jobStatusText = $this->job_status == self::JOBSTATUS_KONTRAK ? 'Kontrak' : $jobStatusText;
		$jobStatusText = $this->job_status == self::JOBSTATUS_PROBATION ? 'Probation' : $jobStatusText;

		return $jobStatusText;
	}


	public function statusText()
	{
		$text = $this->status == self::STATUS_ACTIVE ? 'Aktif' : '';
		$text = $this->status == self::STATUS_NOTACTIVE ? 'Tidak Aktif' : $text;

		return $text;
	}


	public function statusHtml()
	{
		$statusHtml = $this->status == self::STATUS_ACTIVE ? '<span class="text-success"> Aktif </span>' : '';
		$statusHtml = $this->status == self::STATUS_NOTACTIVE ? '<span class="text-danger"> Tidak Aktif </span>' : $statusHtml;

		return $statusHtml;
	}


	public function isPhotoExists()
	{
		if(empty($this->photo)) return false;

		return \File::exists($this->photoPath());
	}


	public function deletePhoto()
	{
		if($this->isPhotoExists()) {
			\File::delete($this->photoPath());
			\File::delete($this->photoPath('thumb'));
			\File::delete($this->photoPath('face'));

			$this->update([
				'photo'	=> null,
			]);
		}

		return $this;
	}


	public function resizePhoto()
	{
		$img = Image::make($this->photoPath());
		$x = 0;
		$y = 0;

		if($img->width() >= $img->height()) {
			$img->resize(null, 432, function ($constraint) {
				$constraint->aspectRatio();
			});
			$x = ($img->width() - 352)/2;
		} else {
			$img->resize(352, null, function ($constraint) {
				$constraint->aspectRatio();
			});
			$y = ($img->height() - 432)/2;
		}

		$img->crop(352, 432, (int) $x, (int) $y);
		// $img->resize(352, 432);
		$img->save($this->photoPath('face'));
		//461x565
		//900x700

		//resize
		MyImage::setHeight([
			'path'      => $this->photoPath(),
			'height'    => 500,
		]);
		//thumb
		MyImage::setWidth([
			'path'      => $this->photoPath(),
			'width'     => 50,
			'result'    => $this->photoPath('thumb'),
		]);
	}


	public function dateOfBirthText($format = 'd M Y')
	{
		return date($format, strtotime($this->date_of_birth));
	}


	public static function dt($request)
	{
		$data = self::select([ 'employees.*' ])
					->with([ 'department', 'position', 'shift', 'employeeGroup' ])
					->leftJoin('departments', 'employees.id_department', '=', 'departments.id')
					->leftJoin('positions', 'employees.id_position', '=', 'positions.id')
					->leftJoin('shifts', 'employees.id_shift', '=', 'shifts.id')
					->leftJoin('employee_groups', 'employees.id_employee_group', '=', 'employee_groups.id');

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
					$data = $data->where('employees.id_shift', null)
								 ->where('employees.shift_type', 'routine');
				} else {
					$data = $data->where('employees.id_shift', $request->id_shift);
				}
			}
		}

		if(!empty($request->id_employee_group)) {
			$shiftID = $request->id_employee_group;

			if($shiftID != 'all') {
				if ($shiftID == 'no') {
					$data = $data->where('employees.id_employee_group', null);
				} else {
					$data = $data->where('employees.id_employee_group', $request->id_employee_group);
				}
			}
		}

		if(!empty($request->employee_status)) {
			$status = $request->employee_status;

			if($status != 'all') {
				$data = $data->where('employees.status', $status);
			}
		}

		return DataTables::eloquent($data)
			->editColumn('employee_number', function($data){
				return $data->employee_number ?? '-';
			})
			->addColumn('department.department_name', function($data){
				return $data->departmentName();
			})
			->addColumn('position.position_name', function($data){
				return $data->positionName();
			})
			->editColumn('shift.shift_name', function($data){
				if($data->isShiftTypeRoutine()) {
					return $data->shiftName();
				} else {
					return '<span class="text-success"> [Shift Harian] </span>';
				}
			})
			->addColumn('employee_group.group_name', function($data){
				return $data->employeeGroupName();
			})
			->editColumn('job_status', function($data){
				return $data->jobStatusText();
			})
			->editColumn('status', function($data){
				return $data->statusHtml();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('employee.detail', $data->id).'" title="Detail Karyawan">
							<i class="mdi mdi-magnify"></i> Detail
						</a>
						<a class="dropdown-item" href="'.route('employee.download_curriculum_vitae', $data->id).'" title="Download Personal CV" target="_blank">
							<i class="mdi mdi-account-box-outline"></i> Download Personal CV
						</a>';

				if(UserPermission::check('employee', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('employee.edit', $data->id).'" title="Edit Karyawan">
							<i class="mdi mdi-pencil"></i> Edit
						</a>
						<a class="dropdown-item" href="'.route('employee.edit_user', $data->id).'" title="Edit Login Pengguna">
							<i class="mdi mdi-account-edit"></i> Edit Login Pengguna
						</a>';
				}

				if(UserPermission::check('employee', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);"  data-href="'.route('employee.destroy', $data->id).'" title="Hapus Karyawan">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				$button .= '
						<a class="dropdown-item push-to-faceterminal" href="javascript:void(0);" data-href="'.route('employee.push_to_faceterminal', $data->id).'" title="Push ke Device Face Terminal">
							<i class="mdi mdi-face-recognition"></i> Push ke Device Face Terminal
						</a>
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'shift.shift_name', 'status', 'action' ])
			->make(true);
	}


	public static function createEmployeeFromAdmin($request)
	{
		$employee = self::create([
			'employee_name'		=> $request->employee_name,
			'gender'			=> $request->gender,
			'email'				=> !empty($request->email) ? $request->email : null,
			'phone_number'		=> !empty($request->phone_number) ? Helper::idPhoneNumberFormat($request->phone_number) : null,
			'id_department'		=> $request->id_department,
			'id_position'		=> $request->id_position,
			'shift_type'		=> $request->shift_type,
			'id_shift'			=> $request->id_shift,
			'id_employee_group'	=> $request->id_employee_group ?? null,
			'employee_number'	=> !empty($request->employee_number) ? $request->employee_number : null,
			'jamsostek'			=> !empty($request->jamsostek) ? $request->jamsostek : null,
			'job_status'		=> $request->job_status,
			'start_working_date'=> $request->start_working_date ?? null,
			'place_of_birth'	=> $request->place_of_birth ?? null,
			'date_of_birth'		=> $request->date_of_birth ?? null,
			'address'			=> $request->address ?? null,
			'last_education'	=> $request->last_education ?? null,
			'last_education_major' => $request->last_education_major ?? null,
			'marital_status'	=> $request->marital_status ?? null,
			'blood_type'		=> $request->blood_type ?? null,
			'ktp_number'		=> $request->ktp_number ?? null,
			'npwp_number'		=> $request->npwp_number ?? null,
			'bank_name'			=> $request->bank_name ?? null,
			'bank_account_number' => $request->bank_account_number ?? null,
			'whatsapp_notification_status' => $request->whatsapp_notification_status ?? null,
		]);

		// $employee->setPhotoFromBase64($request->photo);
		$employee->setPhoto($request);
		$employee->createUserIfDoesntExist();

		return $employee;
	}


	public function updateEmployee($request)
	{
		$this->update([
			'employee_name'		=> $request->employee_name,
			'gender'			=> $request->gender,
			'email'				=> $request->email ?? null,
			'phone_number'		=> !empty($request->phone_number) ? Helper::idPhoneNumberFormat($request->phone_number) : null,
			'id_department'		=> $request->id_department,
			'id_position'		=> $request->id_position,
			'id_shift'			=> $request->id_shift,
			'employee_number'	=> $request->employee_number ?? null,
			'jamsostek'			=> $request->jamsostek ?? null,
			'job_status'		=> $request->job_status,
			'shift_type'		=> $request->shift_type,
			'id_shift'			=> $request->id_shift ?? null,
			'id_employee_group'	=> $request->id_employee_group ?? null,
			'start_working_date'=> $request->start_working_date ?? null,
			'place_of_birth'	=> $request->place_of_birth ?? null,
			'date_of_birth'		=> $request->date_of_birth ?? null,
			'address'			=> $request->address ?? null,
			'last_education'	=> $request->last_education ?? null,
			'last_education_major' => $request->last_education_major ?? null,
			'marital_status'	=> $request->marital_status ?? null,
			'blood_type'		=> $request->blood_type ?? null,
			'ktp_number'		=> $request->ktp_number ?? null,
			'npwp_number'		=> $request->npwp_number ?? null,
			'bank_name'			=> $request->bank_name ?? null,
			'bank_account_number' => $request->bank_account_number ?? null,
			'whatsapp_notification_status' => $request->whatsapp_notification_status ?? null,
		]);

		$this->setPhoto($request);
		$this->createUserIfDoesntExist();

		return $this;
	}


	public function setPhotoFromBase64($base64)
	{
		if(!empty($base64))
		{
			$filename 	= date('Ymd_His').".jpeg";
			$path 		= storage_path('app/public/employee/original/'.$filename);

			$explode 	= explode(',', $base64);
			$cek 		= count($explode);
			$blob 		= base64_decode($cek == 1 ? $explode[0] : $explode[1]);

			\File::put($path, $blob);
			if($this->isPhotoExists()) {
				$this->deletePhoto();
			}

			$this->update([
				'photo'	=> $filename
			]);

			$this->resizePhoto();
		}

		return $this;
	}


	public function setPhoto($request)
	{
		if(!empty($request->file_photo)) {
			$this->removePhoto();
			$file = $request->file('file_photo');
			$extension = $file->getClientOriginalExtension();
			$filename = date('Ymd_His').".".$extension;
			$path = storage_path('app/public/employee/original/');
			$file->move($path, $filename);

			$this->update([
				'photo'	=> $filename,
			]);
			$this->resizePhoto();
			$this->pushToFaceTerminalDevice();
		}

		return $this;
	}

	public function removePhoto()
	{
		$this->deletePhoto();
		return $this;
	}


	public function fetchEmployeeForFaceTerminalDevice()
	{
		$faceTerminalUser = null;

		if(empty($this->faceTerminalUser))
		{
			$faceTerminalUser = FaceTerminalUser::createFaceTerminalUser([
				'type'			=> FaceTerminalUser::TYPE_EMPLOYEE,
				'id_reference'	=> $this->id,
				'valid_start'	=> today(),
				'valid_end'		=> "2025-12-31 00:00:00"
			]);
		}
		else
		{
			$faceTerminalUser = $this->faceTerminalUser;
		}

		return [
			"id"		=> $faceTerminalUser->id,
			"card"		=> $faceTerminalUser->id,
			"name"		=> $this->employee_name,
			"validStart"=> date('Y-m-d 00:00:00', strtotime($faceTerminalUser->valid_start)),
			"validEnd"	=> date('Y-m-d 23:59:59', strtotime($faceTerminalUser->valid_end)),
			"fp"		=> base64_encode(\File::get($this->photoPath('face'))),
			"path"		=> $this->photoPath('face')
		];
	}


	public function pushToFaceTerminalDevice()
	{
		if(empty($this->photo)) return false;

		FaceTerminalDevice::pushUserToAllDevices($this->fetchEmployeeForFaceTerminalDevice());

		return $this;
	}


	public function removeFromFaceTerminalDevice()
	{
		FaceTerminalDevice::removeUserFromAllDevices([
			"id"	=> $this->faceTerminalUser->id,
		]);

		return $this;
	}


	public function isJobStatusValid()
	{
		if ($this->job_status == self::JOBSTATUS_TETAP)
		{
			return true;
		}
		elseif ($this->job_status == self::JOBSTATUS_KONTRAK)
		{
			if($contract = $this->employeeContract)
			{
				return $contract->isValid();
			}
		}

		return false;
	}


	public function isJobStatusKontrak()
	{
		return $this->job_status == self::JOBSTATUS_KONTRAK;
	}


	public function isJobStatusTetap()
	{
		return $this->job_status == self::JOBSTATUS_TETAP;
	}


	public function getLatestAttendance()
	{
		$attendance = Attendance::where([
			'id_employee'	=> $this->id,
			'type'			=> Attendance::TYPE_HADIR,
		])->orderBy('created_at', 'desc')->first();

		return $attendance;
	}


	public function isAllowForClockOut()
	{
		if($this->isOffDay()) return false;

		return $this->isAllowForClockOut2();
	}


	public function messageAllowForClockOut()
	{
		return $this->getAllowForClockOut()->message;
	}


	public function getAllowForClockOut()
	{
		$result = null;

		$attendance = $this->getLatestAttendance();

		// Cek Kehadiran
		if(!$attendance) return $this->notExistsLatestAttendanceForAllowClockOut();

		// Cek status clock out
		if($attendance->isAlreadyClockOut()) return $this->notExistsLatestAttendanceForAllowClockOut();

		// Cek status lembur
		if(!$attendance->isOvertime())
		{
			// Tidak Lembur

			// Cek shift
			if($this->isHasShift())
			{
				$shift = $this->shift;

				if($shift->isAllowForClockOutNow())
				{
					$result = [
						'is_allow'	=> true,
						'message'	=> 'Boleh clock out'
					];
				}
				else
				{
					$result = [
						'is_allow'	=> false,
						'tolerance'	=> true,
						'message'	=> 'Belum waktu nya clock out'
					];
				}
			}
			else
			{
				$result = [
					'is_allow'	=> true,
					'msg'		=> 'Tidak punya shift boleh keluar kapan saja',
				];
			}
		}
		else
		{
			// Lembur

			$result = [
				'is_allow'	=> true,
				'message'	=> 'Lembur tidak mengikuti waktu shift'
			];
		}

		return (object) $result;
	}


	private function notExistsLatestAttendanceForAllowClockOut()
	{
		return (object) [
			'is_allow'	=> false,
			'tolerance'	=> false,
			'message'	=> 'Tidak ada kehadiran yang belum clockout',
		];
	}


	public function sendReminderForAttend()
	{
		if($this->position) {
			if(!$this->position->isMustAttend()) {
				return false;
			}
		}

		if($this->isOffDay()) return false;

		if($attendance = $this->getLatestAttendance()) {
			if($attendance->date == date('Y-m-d')) return false;
		}

		if(empty($this->phone_number)) return false;

		$shift = $this->getTodayShift();
		if(!$shift) return false;

		if(\ActionHistory::getHistory(date('Y-m-d'), \ActionHistory::REMIND_EMPLOYEE_TO_ATTEND, $this->id)) return false;

		$message = "Hai, {$this->employee_name}";
		$message .= "\nHarap lakukan absensi tepat waktu karena akan mempengaruhi upah harian anda.";
		$message .= "\nTerima Kasih.";

		$message .= "\n\nShift kerja hari ini";
		$message .= "\n{$shift->clockStartText()} - {$shift->clockEndText()}";

		$message .= "\n\n*Adiva Attendance System*.";

        $EndPointWa = WhatsappNew::END_POINT_WA;
        if($EndPointWa == 'WA Baru'){
            // wa Baru
            Helper::sendNotificationWhatsapp($phoneNumber = $this->phone_number, $message);
        }else{
            Whatsapp::sendChat([
                'to'	=> $this->phone_number,
                'text'	=> $message,
            ]);
        }

		\ActionHistory::createNotificationHistory(
			\ActionHistory::REMIND_EMPLOYEE_TO_ATTEND,
			$this->id,
			"Reminder ke {$this->employee_name}"
		);

		return true;
	}


	public static function createEmployeeFromRegister($request)
	{
		$filename 	= "push_".date('Ymd_His').".jpeg";

		$employee = self::create([
			'employee_name'		=> $request->employee_name,
			'gender'			=> $request->gender,
			'email'				=> !empty($request->email) ? $request->email : null,
			'phone_number'		=> !empty($request->phone_number) ? Helper::idPhoneNumberFormat($request->phone_number) : null,
			'id_department'		=> $request->id_department,
			'id_position'		=> $request->id_position,
			'employee_number'	=> !empty($request->employee_number) ? $request->employee_number : null,
			'jamsostek'			=> !empty($request->jamsostek) ? $request->jamsostek : null,
			'job_status'		=> $request->job_status,
			'photo'				=> $filename,
		]);

		Photo::createFromBase64($request->full_photo, storage_path('app/public/employee/original/'.$filename));
		Photo::createFromBase64($request->thumb_photo, storage_path('app/public/employee/thumb/'.$filename));
		Photo::createFromBase64($request->face_photo, storage_path('app/public/employee/face/'.$filename));

		$employee->pushToFaceTerminalDevice();

		return $employee;
	}


	public function deleteEmployee()
	{
		if(!empty($this->faceTerminalUser)) {
			$this->faceTerminalUser->delete();
		}

		if(!empty($this->employeeContract)) {
			$this->employeeContract->delete();
		}

		if(!empty($this->employeeSalary)) {
			$this->employeeSalary->deleteEmployeeSalary();
		}

		if(!empty($this->employeeLeaveQuota)) {
			$this->employeeLeaveQuota->deleteEmployeeLeaveQuota();
		}

		try {
			// $this->removeFromFaceTerminalDevice();
		} catch (Exception $e) {

		}

		$this->delete();
	}


	public function createUserIfDoesntExist()
	{
		if(empty($this->phone_number)) return $this;

		if(empty($this->user))
		{
			$username = $this->generateStaffUsername();
			$password = substr($this->phone_number, -4, 4);

			$user = \App\User::create([
				'name'		=> $this->employee_name,
				'username'	=> $username,
				'password'	=> \Hash::make($password),
				'role'		=> \App\User::ROLE_STAFF,
			]);

			$this->update([
				'id_user'	=> $user->id,
			]);

			$this->sendUserAuthNotification($username, $password);
		}

		return $this;
	}


	public function generateStaffUsername()
	{
		if(!empty($this->user)) return $this->user->username;

		$names = explode(' ', $this->employee_name);
		$username = strtolower($names[0]);
		$found = false;

		while(!$found) {
			$user = \App\User::where('username', $username)->first();
			if(!$user) {
				$found = true;
				break;
			}
		}

		return $username;
	}


	private function sendUserAuthNotification($username, $password)
	{
		$text = "Akun anda ";
		$text .= "\nUsername : {$username}";
		$text .= "\nPassword : {$password}";
		$text .= "\nSilahkan login melalui link ".route('login');
		$text .= "\n\n*Attendance Sistem*";


        $EndPointWa = WhatsappNew::END_POINT_WA;
        if($EndPointWa == 'WA Baru'){
            // wa Baru
            Helper::sendNotificationWhatsapp($phoneNumber = $this->phone_number, $text);
        }else{
             \App\MyClass\Whatsapp::sendChat([
                'to'	=> $this->phone_number,
                'text'	=> $text,
            ]);
        }

        return true;

	}


	public static function amountOfActiveEmployee()
	{
		$amount = self::where('id_department', null)
					->where('status', self::STATUS_ACTIVE)
					->count();

		return $amount;
	}


	public static function amountOfActiveEmployeeWithNoHaveDepartment()
	{
		$amount = self::where('id_department', null)
					->where('status', self::STATUS_ACTIVE)
					->count();

		return $amount;
	}


	public static function amountOfActiveEmployeeWithNoHaveShift()
	{
		$amount = self::where('id_shift', null)
					->where('shift_type', 'routine')
					->where('status', self::STATUS_ACTIVE)
					->count();

		return $amount;
	}


	public function employeeNumber()
	{
		return !empty($this->employee_number) ? $this->employee_number : '-';
	}


	public function genderText()
	{
		if($this->gender == 'L') return 'Pria';
		if($this->gender == 'P') return 'Wanita';

		return '-';
	}


	public function getAttendanceByDateRange($start = null, $end = null)
	{
		if(empty($start)) $start = date('Y-m-d');
		if(empty($end)) $end = date('Y-m-d');

		return Attendance::where('id_employee', $this->id)
						 ->where('date', '>=', $start)
						 ->where('date', '<=', $end)
						 ->orderBy('date')
						 ->get();
	}


	public function getWT($month) {
		$start = date('Y').'-'.$month.'-01';
		$end = date('Y-m-t', strtotime($start));
		return $this->getWorkTimeByDateRange($start, $end);
	}

	public function getWorkTimeByDateRange($start, $end)
	{
		$totalOfDay = 0; // Jumlah Hari
		$days = [];
		$amountOfWorkDay = 0; // Jumlah Hari Kerja
		$amountOfAttend = 0; // Jumlah Hadir
		$amountOfLeave = 0; // Jumlah Cuti
		$amountOfSick = 0; // Jumlah Sakit
		$amountOfNecessity = 0; // Jumlah Izin
		$amountOfOffDay = 0; // Jumlah Hari Libur
		$totalOfPercentage = (double) 0; // Total persentase harian
		$totalOfLatePercentage = (double) 0; // Total persentase terlambat;
		$resumeAttendances = [];
		$salary = 0;
		$dailySalaries = [];

		foreach($this->getAttendanceByDateRange($start, $end) as $attendance)
		{
			// Tgl
			$day = (int) date('d', strtotime($attendance->date));
			$totalOfDay++;
			$days[] = $day;

			if($attendance->isTypeLibur()) {
				$amountOfOffDay++;
				continue;
			}

			$resumeAttendances['work'][$day] = $attendance->percentageOfAttend();
			$totalOfPercentage += $attendance->percentageOfAttend();
			$salary += $attendance->dailySalary();
			$dailySalaries[$day] = $attendance->dailySalary();

			$amountOfWorkDay++;

			if($attendance->isTypeHadir()) {
				$amountOfAttend++;
				$totalOfLatePercentage += 1 - $attendance->percentageOfAttend();
			}

			if($attendance->isTypeCuti()) {
				$amountOfLeave++;
			}

			if($attendance->isTypeSakit()) {
				$amountOfSick++;
			}

			if($attendance->isTypeIzin()) {
				$amountOfNecessity++;
			}
		}

		// Cek Apakah hari
		$startDate = new \Carbon\Carbon($start);
		$endDate = new \Carbon\Carbon($end);
		$fullDay = (int) ($startDate->diffInDays($endDate)) + 1;

		if($totalOfDay < $fullDay) {
			$date = new \Carbon\Carbon($start);
			while($date->format('Y-m-d') <= $endDate->format('Y-m-d')) {
				$day = (int) $date->format('d');
				if(!in_array($day, $days)) {
					if(OffDay::offDayCheck($date->format('Y-m-d'))) {
						$amountOfOffDay++;
					} else {
						if($this->shift_type == 'routine') {
							if($this->shift->offDayCheck($date->format('Y-m-d'))) {
								$amountOfOffDay++;
							} else {
								$amountOfWorkDay++;
							}
						}
					}

					$days[] = $day;
				}
				$date->addDays(1);
			}
		}

		$percentageOfAttendance = 10;
		$percentageOfLateAttendance = 10;
		if($amountOfWorkDay > 0) {
			$percentageOfAttendance = round($totalOfPercentage / $amountOfWorkDay, 4) * 100;
			$percentageOfLateAttendance = round($totalOfLatePercentage / $amountOfWorkDay, 4) * 100;
		}
		$amountOfNotAttend = $amountOfWorkDay - $amountOfAttend - $amountOfLeave - $amountOfSick -$amountOfNecessity;

		return (object) [
			'amount_of_work_day'	=> $amountOfWorkDay,
			'amount_of_attend'		=> $amountOfAttend,
			'amount_of_leave'		=> $amountOfLeave,
			'amount_of_sick'		=> $amountOfSick,
			'amount_of_necessity'	=> $amountOfNecessity,
			'amount_of_offday'		=> $amountOfOffDay,
			'amount_of_not_attend'	=> $amountOfNotAttend,
			'total_of_percentage'	=> $totalOfPercentage,
			'total_of_late_percentage'	=> $totalOfLatePercentage,
			'percentage_of_attendance'	=> $percentageOfAttendance,
			'percentage_of_late_attendance'	=> $percentageOfLateAttendance,
			'overtime'				=> 0,
			'salary'				=> $salary,
			'daily_salaries'		=> $dailySalaries,
			'days'					=> $days,
			'resume_of_attendances' => $resumeAttendances,
		];
	}


	public static function createEmployeeUsers()
	{
		foreach(self::getActiveEmployees() as $employee)
		{
			$employee->createUserIfDoesntExist();
		}

		return true;
	}


	public function dailySalary($date = null, $defaultSalary = 0)
	{
		if(empty($date)) $date = date('Y-m-d');

		try {
			if($this->shift_type == 'routine') {
				if($this->isHasSalary()) {
					if($this->isHasShift()) {
						return $this->employeeSalary->basic_salary / $this->shift->amountOfWorkDayInMonth($date);
					}
				} else {
					return $defaultSalary / $this->shift->amountOfWorkDayInMonth($date);
				}
			}

			return 0;
		} catch (\Exception $e) {
			return 0;
		}
	}


	public function dailySalaryText($date = null)
	{
		return 'Rp '.number_format($this->dailySalary($date));
	}


	public function overtimePay($defaultOvertimePay = 0)
	{
		if($this->isHasSalary()) {
			return $this->employeeSalary->overtime_pay;
		} else {
			return $defaultOvertimePay;
		}
	}


	public function workScheduleInWeek()
	{
		$numberDayToday = date('w'); // start from 0
		$date = today()->addDays(-$numberDayToday);

		$shiftOffDays = $this->isHasShift() ? $this->shift->getOffdayShift() : [];

		$results = [];

		for ($i = 0; $i <= 6; $i++) {

			$isOffDay = false;
			$description = 'Jam kerja rutin';

			// Libur Shift
			if(in_array( ($i + 1), $shiftOffDays)) {
				$isOffDay = true;
				$description = 'Libur shift';
			}

			$dateData = $date->addDays(1);
			$dateData = date('Y-m-d', strtotime($dateData));

			// Employee Leave
			$employeeLeave = EmployeeLeave::where('id_employee', $this->id)
										  ->where('start_date', '<=', $dateData)
										  ->where('end_date', '>=', $dateData)
										  ->first();
			if($employeeLeave) {
				$isOffDay = true;
				$description = $employeeLeave->description;
			}
			// $description .= $dateData.'_'.$employeeLeave;

			$result = [
				'date'			=> $dateData,
				'date_text'		=> \Date::fullDateWithDayName($dateData),
				'date_in_day'	=> date('d', strtotime($dateData)),
				'is_offday'		=> $isOffDay,
				'description'	=> $description,
			];

			$results[] = (object) $result;
		}

		return $results;
	}


	public function isEmployeeFaceValid($path)
	{
		try {
			$similarityMinimum = setting('face_compare_similarity_for_attendance', 70);
			$similarity = FaceTerminalDevice::faceCompare($this->photoPath('face'), $path);

			if($similarity) {
				if($similarity >= $similarityMinimum) {
					return true;
				}
			}

			return false;
		} catch (\Exception $e) {
            $EndPointWa = WhatsappNew::END_POINT_WA;
            if($EndPointWa == 'WA Baru'){
                // wa Baru
                Helper::sendNotificationWhatsapp($phoneNumber = '6282316425264', $message = 'Masalah face compare '.$this->employee_name."\n\n".$e->getTraceAsString());
            }else{
                \App\MyClass\Whatsapp::sendChat([
                    'to'	=> '6282316425264',
                    'text'	=> 'Masalah face compare '.$this->employee_name."\n\n".$e->getTraceAsString(),
                ]);
            }

			return true;
		}
	}


	public static function pushActiveEmployeesToFaceTerminalDevice()
	{
		$count = 1;
		foreach(self::getActiveEmployees() as $employee) {
			try {
				$employee->pushToFaceTerminalDevice();
				$count++;
			} catch (\Exception $e) {}
		}

		return $count;
	}


	public static function isEmployeeReachLimit()
	{
		$limit = appconfig('employee_limit', 0);

		if($limit == 0) {
			return false;
		} else {
			return self::count() >= $limit;
		}
	}


	public function isHasLeaveQuota($date = null)
	{
		if(empty($this->employeeLeaveQuota)) return false;

		if(empty($date)) $date = date('Y-m-d');

		$month = date('m', strtotime($date));
		$year = date('Y', strtotime($date));

		$employeeLeaves = EmployeeLeave::where('reason', EmployeeLeave::REASON_LEAVE)
									   ->where('id_employee', $this->id)
									   ->where('start_date', '>=', "{$year}-{$month}-01")
									   ->where('end_date', '<=', date('Y-m-t', strtotime("{$year}-{$month}-01")))
									   ->get();
		$amount = 0;
		foreach($employeeLeaves as $leave) {
			$amount += $leave->amountOfDay();
		}

		$quota = $this->employeeLeaveQuota->quota;

		return $quota > $amount;
	}


	public function useLeaveQuota($amount = 1)
	{
		if(!empty($this->employeeLeaveQuota))
		{
			$this->employeeLeaveQuota->update([
				'quota_available'	=> (int) $this->employeeLeaveQuota->quota_available - $amount,
				'quota_used'		=> (int) $this->employeeLeaveQuota->quota_used + $amount,
			]);
		}

		return $this;
	}


	public function resumeOfAttendanceThisMonth()
	{
		$start = date('Y-m-01');
		$end = date('Y-m-t');
		$attendances = Attendance::where('id_employee', $this->id)
								 ->where('date', '<=', $start)
								 ->where('date', '>=', $end)
								 ->get();
		$resume = [
			'attend'			=> 0,
			'sick'				=> 0,
			'leave'				=> 0,
			'necessity'			=> 0,
			'offday'			=> 0,
			'not_attend'		=> 0,
			'overtime_minute'	=> 0,
		];

		foreach($attendances as $attendance)
		{
			if($attendance->isTypeHadir()) $resume['attend']++;
			if($attendance->isTypeSakit()) $resume['sick']++;
			if($attendance->isTypeIzin()) $resume['necessity']++;
			if($attendance->isTypeLibur()) $resume['offday']++;
			if($attendance->isTypeTanpaKeterangan()) $resume['not_attend']++;
			if($attendance->isTypeCuti()) $resume['leave']++;
			if($attendance->isOvertime()) $resume['overtime_minute'] += $attendance->overtime;
		}

		$date1 = now();
		$date2 = new \Carbon\Carbon(date('Y-m-01'));
		$amountOfPassedDay = $date1->diffInDays($date2);
		$resume['not_attend'] += $amountOfPassedDay - count($attendances);

		return $resume;
	}


	public function isAllowCreateAttendanceViaWeb()
	{
		if(empty($this->webAttendancePermission)) return false;

		return $this->webAttendancePermission->isValid();
	}


	public function isLocationValid($latitude, $longitude)
	{
		if(empty($this->webAttendancePermission)) return false;
		if(!$this->webAttendancePermission->isHasLocations()) return true;

		$isValid = false;

		foreach($this->webAttendancePermission->getLocations() as $location)
		{
			if($location->isInRadius($latitude, $longitude)) {
				$isValid = true;
				break;
			}
		}

		return $isValid;
	}


	public function isAlreadyClockOut()
	{
		$attendance = $this->latestAttendance;
		if($attendance) {
			return $attendance->isAlreadyClockOut();
		}

		return true;
	}


	private function checkForAttendance()
	{
		// Hari ini libur atau tidak
		$isOffDay = $this->isOffday();

		// Sudah masuk & cek lembur
		$isAllowForClockIn = true;
		$isMustClockIn = !$isOffDay;
		$isOvertime = false;
		$attendance = $this->latestAttendance;

		if($attendance) {
			if(!$attendance->isAlreadyClockOut()) {
				$isMustClockIn = false;
				$isAllowForClockIn = false;

				if($attendance->isOvertime()) {
					$isOvertime = true;
				}
			} else {
				if($attendance->date == date('Y-m-d')) {
					$isMustClockIn = false;
				}
			}
		}

		return (object) [
			'isAllowForClockIn'	=> $isAllowForClockIn,
			'isMustClockIn'	=> $isMustClockIn,
			'isOvertime'	=> $isOvertime,
		];
	}

	public function isMustClockIn()
	{
		return $this->checkForAttendance()->isMustClockIn;
	}

	public function isOvertime()
	{
		return $this->checkForAttendance()->isOvertime;
	}

	public function isLowestPosition()
	{
		$lowestPosition = Position::where('id_department', $this->id_department)
								  ->orderBy('position_level', 'desc')
								  ->first();
		$position = $this->position;

		if($lowestPosition && $position) {
			$positionLevel = $lowestPosition->position_level;
			return $position->position_level == $positionLevel;
		}

		return true;
	}

	public function positionLevel() : int
	{
		if($this->position) {
			return $this->position->position_level;
		}

		return -1;
	}


	public function downloadCurriculumVitae()
	{
		return \PDF::loadView('admin.employee.cv_pdf', [
			'employee'	=> $this
		])->setPaper('a4', 'portrait')->download($this->employee_name.'.pdf');
	}


	/**
	 * 	CRUD methods
	 * */



	/**
	 * 	Triggered CRUD methods
	 * */
	public function setActive()
	{
		$this->update([
			'status'	=> self::STATUS_ACTIVE,
		]);

		return $this;
	}

	public function setInactive()
	{
		$this->update([
			'status'	=> self::STATUS_INACTIVE,
		]);

		return $this;
	}


	/**
	 * 	Helper methods
	 * */
	public function leaveQuotaAvailable($withMassLeaveCut = false)
	{
		if($leaveQuota = $this->employeeLeaveQuota) {
			if($withMassLeaveCut) {
				return $leaveQuota->quota_available - $leaveQuota->mass_leave_cut;
			}
			return $leaveQuota->quota_available;
		}

		return 0;
	}

	public function isShiftTypeRoutine()
	{
		return $this->shift_type == 'routine';
	}

	public function isStatusActive()
	{
		return $this->status == self::STATUS_ACTIVE;
	}

	public function isStatusInactive()
	{
		return $this->status == self::STATUS_INACTIVE;
	}

	public function fetchData()
	{
		return (object) [
			'id'				=> $this->id,
			'employee_name'		=> $this->employee_name,
			'jamsostek'			=> $this->jamsostek,
			'employee_number'	=> $this->employee_number,
			'phone_number'		=> $this->phone_number,
			'email'				=> $this->email,
			'gender'			=> $this->gender,
			'department_name'	=> $this->departmentName(),
			'position_name'		=> $this->positionName(),
			'address'			=> $this->address,
			'photo_link'		=> $this->photoLink('face'),
			'username'			=> $this->username(),
		];
	}

	public function isApprover()
	{
		$count = Position::where('approver_1', $this->id_position)
						 ->orWhere('approver_2', $this->id_position)
						 ->count();
		return $count > 0;
	}




	/**
	 * 	Contract Helper
	 * */
	public function jobStatusIsContract()
	{
		return $this->job_status == self::JOBSTATUS_KONTRAK;
	}

	public function jobStatusIsProbation()
	{
		return $this->job_status == self::JOBSTATUS_PROBATION;
	}

	public function activeEmployeeContract()
	{
		return $this->hasOne('App\Models\EmployeeContract', 'id_employee')
					->where('start_date', '<=', now()->format('Y-m-d'))
					->where('end_date', '>=', now()->format('Y-m-d'));
	}

	public function almostOverEmployeeContract()
	{
		return $this->hasOne('App\Models\EmployeeContract', 'id_employee')
					->where('start_date', '<=', now()->format('Y-m-d'))
					->where('end_date', '>=', now()->format('Y-m-d'))
					->where('end_date', '<=', now()->addDays(14)->format('Y-m-d'));
	}

	public static function getEmployeesWithContractIsAlmostOver()
	{
		return self::where('status', self::STATUS_ACTIVE)
				   ->where(function($query){
				   		$query->where('job_status', self::JOBSTATUS_KONTRAK)
				   			  ->orWhere('job_status', self::JOBSTATUS_PROBATION);
				   })
				   ->has('almostOverEmployeeContract')
				   ->with('activeEmployeeContract')
				   ->get();
	}

	public static function getEmployeesWithDoesntHaveContract()
	{
		return self::where('status', self::STATUS_ACTIVE)
				   ->where(function($query){
				   		$query->where('job_status', self::JOBSTATUS_KONTRAK)
				   			  ->orWhere('job_status', self::JOBSTATUS_PROBATION);
				   })
				   ->doesntHave('activeEmployeeContract')
				   ->get();
	}

	public function employeeContractIsAlmostOver()
	{
		return $this->almostOverEmployeeContract != null;
	}



	/**
	 * 	Shift Helper
	 * */
	public function clockStartActive($format = 'H:i:s')
	{
		$result = null;
		if($attendance = $this->latestAttendance) {
			if(!$attendance->isAlreadyClockOut()) {
				$result = $attendance->shift_clock_in;
			}
		}

		if(!$result) {
			if($shift = $this->todayShift()) {
				$result = $shift->clock_start;
			} else {
				$result = '00:00:00';
			}
		}

		return date($format, strtotime($result));
	}

	public function clockEndActive($format = 'H:i:s')
	{
		$result = null;

		if($attendance = $this->latestAttendance) {
			if(!$attendance->isAlreadyClockOut()) {
				$result = $attendance->shift_clock_out;
			}
		}

		if(!$result) {
			if($shift = $this->todayShift()) {
				$result = $shift->clock_end;
			} else {
				$result = '00:00:00';
			}
		}

		return date($format, strtotime($result));
	}

	public function clockStartLimitActive($format = 'H:i:s')
	{
		$result = null;

		if($shift = $this->todayShift()) {
			$result = $shift->clock_start_limit;
		} else {
			$result = $this->clockStartActive();
		}

		return date($format, strtotime($result));
	}

	public function clockStartActiveWithDate()
	{
		return now()->format('Y-m-d').' '.$this->clockStartActive();
	}

	public function clockEndActiveWithDate()
	{
		if($this->clockStartActive() < $this->clockEndActive()) {
			return now()->format('Y-m-d').' '.$this->clockEndActive();
		} else {
			return now()->addDays(1)->format('Y-m-d').' '.$this->clockEndActive();
		}
	}


	public function clockEndWithDate()
	{

	}

	public function isHasShift()
	{
		if($this->shift_type == 'routine') {
			if (!empty($this->id_shift)) {
				if (!empty($this->shift)) {
					return true;
				}
			}
		} elseif($this->shift_type == 'unroutine') {
			$unroutineShift = UnroutineShift::where('date', now()->format('Y-m-d'))
											->where('id_employee', $this->id)
											->first();
			if($unroutineShift) {
				return true;
			}
		}

		return false;
	}

	public function lateMinutes($now = null)
	{
		if(!$now) $now = now()->format('Y-m-d H:i:s');
		try {
			$now = \Carbon\Carbon::createFromFormat('Y-m-d G:i:s', $now);
		} catch (\Exception $e) {
			$now = new \Carbon\Carbon($now);
		}

		$attendancePermission = AttendancePermissionSubmission::where('id_employee', $this->id)
									->where('type', 'Terlambat')
									->where('date', $now->format('Y-m-d'))
									->where('status', AttendancePermissionSubmission::STATUS_APPROVED)
									->first();

		if($attendancePermission)
		{
			$date = $now->format('Y-m-d');
			$time = now()->format('H:i:s');
			if ($attendancePermission->date) {
				$date = $attendancePermission->date;
			}
			if ($attendancePermission->time) {
				$time = $attendancePermission->time;
			}
			// $clockStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $now->format('Y-m-d').' '.now()->format('H:i:s'));
			$clockStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date.' '.$time);
		}
		else
		{
			$clockStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $now->format('Y-m-d').' '.$this->clockStartActive());
			$clockStart->addMinutes($this->lateTolerance());
		}

		$clockStartFormatted = $clockStart->format('Y-m-d H:i:s');


		if($now->format('Y-m-d H:i:s') <= $clockStartFormatted) {
			return 0;
		} else {
			return $now->diffInMinutes($clockStart);
		}
	}

	public function getTodayShift()
	{
		return $this->shiftByDate(now()->format('Y-m-d'));
	}

	public function todayShift()
	{
		return $this->getTodayShift();
	}

	public function shiftByDate($date)
	{
		if($this->shift_type == 'routine') {
			return $this->shift->getShiftByDate($date);
		} elseif($this->shift_type == 'unroutine') {
			$unroutineShift = UnroutineShift::where('date', $date)
											->where('id_employee', $this->id)
											->first();
			return $unroutineShift;
		}
	}

	public function lateTolerance()
	{
		try {
			if($this->shift_type == 'routine') {
				return $this->todayShift()->late_tolerance;
			} elseif($this->shift_type == 'unroutine') {
				return $this->todayShift()->late_tolerance;
			}
		} catch (\Exception $e) {
			return 0;
		}
	}

	public function isAllowForClockIn()
	{
		// Hari ini libur atau tidak
		$isOffDay = $this->isOffday();

		if(!$isOffDay) {
			$attendance = $this->latestAttendance;

			if($attendance) {
				if(!$attendance->isAlreadyClockOut()) {
					return false;
				}

				if($attendance->date == date('Y-m-d')) {
					return false;
				}
			}

			$clock = now()->format('H:i:s');
			$clockStartLimit = $this->clockStartLimitActive();
			$clockEnd = $this->clockEndActive();

			if($clockStartLimit <= $clockEnd) {
				if($clock >= $clockStartLimit && $clock <= $clockEnd) {
					// return '1';
					return true;
				} else {
					// return '2';
					return false;
				}
			} else {
				if($clock >= $clockStartLimit && $clock <= '23:59:59') {
					// return '3';
					return true;
				} elseif($clock >= '00:00:00' && $clock <= $clockEnd) {
					// return '4';
					return true;
				} else {
					// return '5';
					return false;
				}
			}

		} else {
			// return '6'
			return false;
		}
	}

	public function isAllowForClockOut2()
	{
		$attendance = $this->latestAttendance;

		if($attendance) {
			$isAllow = $attendance->isAllowForClockOut();

			if($isAllow) {
				return $isAllow;
			} else {
				if(!$attendance->isAlreadyClockOut()) {
					$attendancePermissionSubmission = AttendancePermissionSubmission::where('id_employee', $this->id)
							->where('date', now()->format('Y-m-d'))
							->where('type', 'Pulang Cepat')
							->where('status', 'approved')
							->first();
					if($attendancePermissionSubmission) {
						return true;
					}
				}
			}

			return false;
		} else {
			return false;
		}
	}


	public function getStaffs()
	{
		$staffs = [];

		if($this->position) {
			$positions = Position::where('approver_1', $this->id_position)
								 ->with([ 'employees.department' ])
								 ->get();
			foreach($positions as $position) {
				foreach($position->employees as $employee) {
					if(!array_key_exists($employee->id, $staffs)) {
						$staffs[$employee->id] = $employee;
					}
				}
			}

			$positions = Position::where('approver_2', $this->id_position)
								 ->with([ 'employees.department' ])
								 ->get();
			foreach($positions as $position) {
				foreach($position->employees as $employee) {
					if(!array_key_exists($employee->id, $staffs)) {
						$staffs[$employee->id] = $employee;
					}
				}
			}
		}

		return array_values($staffs);
	}


	/**
	 * 	Static methods
	 * */
	public static function exportToExcel($request)
	{
		$employees = self::select([ 'employees.*' ])
					->with([ 'department', 'position', 'shift', 'employeeGroup' ])
					->leftJoin('departments', 'employees.id_department', '=', 'departments.id')
					->leftJoin('positions', 'employees.id_position', '=', 'positions.id')
					->leftJoin('shifts', 'employees.id_shift', '=', 'shifts.id')
					->leftJoin('employee_groups', 'employees.id_employee_group', '=', 'employee_groups.id');

		if(!empty($request->id_department)) {
			$departmentID = $request->id_department;

			if($departmentID != 'all') {
				if ($departmentID == 'no') {
					$employees = $employees->where('employees.id_department', null);
				} else {
					$employees = $employees->where('employees.id_department', $request->id_department);
				}
			}
		}

		if(!empty($request->id_shift)) {
			$shiftID = $request->id_shift;

			if($shiftID != 'all') {
				if ($shiftID == 'no') {
					$employees = $employees->where('employees.id_shift', null)
								 ->where('employees.shift_type', 'routine');
				} else {
					$employees = $employees->where('employees.id_shift', $request->id_shift);
				}
			}
		}

		if(!empty($request->id_employee_group)) {
			$shiftID = $request->id_employee_group;

			if($shiftID != 'all') {
				if ($shiftID == 'no') {
					$employees = $employees->where('employees.id_employee_group', null);
				} else {
					$employees = $employees->where('employees.id_employee_group', $request->id_employee_group);
				}
			}
		}

		if(!empty($request->employee_status)) {
			$status = $request->employee_status;

			if($status != 'all') {
				$employees = $employees->where('employees.status', $status);
			}
		}

		$employees = $employees->get();

		$headStyle = [ 'font-style'=>'bold', 'halign'=>'center', 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin', 'wrap_text' => true, 'valign' => 'top' ];
		$bodyStyle = [ 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin', 'valign' => 'top' ];

		$writer = new \App\MyClass\XLSXWriter();

		$writer->writeSheetHeader('Sheet1', [
			'Data Karyawan' => 'string',
			'' => 'string',
			'' => 'string'
		], [
			'font-style' => 'bold',
			'halign'=>'center',
			'widths'=> [ 5, 20, 30, 15, 15, 15, 15, 10, 20, 20, 20, 20, 20, 30, 15, 15, 15, 15, 15, 10, 20 ]
		]);
		$writer->markMergedCell('Sheet1', $start_row=0, $start_col=0, $end_row=0, $end_col=20);

		$writer->writeSheetRow('Sheet1', [ '' ]);

		$writer->writeSheetRow('Sheet1', [
			'No',
			'No Induk Karyawan',
			'Nama',
			'Departemen',
			'Jabatan',
			'Status Pekerjaan',
			'Status Keaktifan',
			'Jenis Kelamin',
			'Email',
			'No Telepon',
			'No Jamsostek',
			'No KTP',
			'No NPWP',
			'Alamat',
			'Tempat Lahir',
			'Tanggal Lahir',
			'Pendidikan Terakhir',
			'Jurusan',
			'Status Pernikahan',
			'Golongan Darah',
			'Tanggal Mulai Bekerja'
		], $headStyle);

		$no = 1;
		foreach($employees as $employee) {
			$writer->writeSheetRow('Sheet1', [
				$no++,
				$employee->employee_number,
				$employee->employee_name,
				$employee->departmentName(),
				$employee->positionName(),
				$employee->jobStatusText(),
				$employee->statusText(),
				$employee->genderText(),
				$employee->email,
				" {$employee->phone_number}",
				" {$employee->jamsostek}",
				" {$employee->ktp_number}",
				" {$employee->npwp_number}",
				$employee->address,
				$employee->place_of_birth,
				$employee->dateOfBirthText('d/m/Y'),
				$employee->last_education,
				$employee->last_education_major,
				$employee->marital_status,
				$employee->blood_type,
				date('d/m/Y', strtotime($employee->start_working_date)),
			], $bodyStyle);
		}

		$filename = date('Ymdhis_').'Data_Karyawan.xlsx';
		$path = \Setting::temps($filename);
		$writer->writeToFile($path);

		return $path;
	}


	public static function getEmployeeWithBirthdayThisMonth()
	{
		$employees = self::where('status', self::STATUS_ACTIVE)
						 ->where('date_of_birth', 'like', '%-'.date('m').'-%')
						 ->orderBy('date_of_birth', 'asc')
						 ->get();
		return $employees;
	}

}
