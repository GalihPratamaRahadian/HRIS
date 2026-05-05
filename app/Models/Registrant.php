<?php

namespace App\Models;

use App\MyClass\Helper;
use App\MyClass\WhatsappNew;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class Registrant extends Model
{
	protected $fillable = [ 'employee_number', 'employee_name', 'gender', 'email', 'phone_number', 'jamsostek', 'job_status', 'photo', 'id_department', 'id_position', 'shift_type', 'id_shift', 'id_user', 'id_employee_group', 'id_employee', 'start_working_date', 'place_of_birth', 'date_of_birth', 'address', 'last_education', 'last_education_major', 'marital_status', 'blood_type', 'ktp_number', 'npwp_number', 'registration_status', 'approved_at', 'rejected_at', 'edited_at'  ];


	const STATUS_UNFILL		= 1;
	const STATUS_WAITING	= 2;
	const STATUS_APPROVED	= 3;
	const STATUS_REJECTED	= 4;

	const JOBSTATUS_KONTRAK	= 'kontrak';
	const JOBSTATUS_TETAP	= 'tetap';


	public static function availableStatus()
	{
		return [
			self::STATUS_UNFILL		=> 'Belum Mengisi',
			self::STATUS_WAITING	=> 'Menunggu Persetujuan',
			self::STATUS_APPROVED	=> 'Telah disetujui',
			self::STATUS_REJECTED	=> 'Ditolak',
		];
	}


	public function department()
	{
		return $this->belongsTo('App\Models\Department', 'id_department');
	}


	public function position()
	{
		return $this->belongsTo('App\Models\Position', 'id_position');
	}


	public function shift()
	{
		return $this->belongsTo('App\Models\Shift', 'id_shift');
	}


	public function user()
	{
		return $this->belongsTo('App\User', 'id_user');
	}


	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee');
	}


	public function employeeGroup()
	{
		return $this->belongsTo('App\Models\EmployeeGroup', 'id_employee_group');
	}


	public function logs()
	{
		return $this->hasMany('App\Models\RegistrantLog', 'id_registrant')->orderBy('created_at', 'desc');
	}


	public function registrantLogs()
	{
		return $this->hasMany('App\Models\RegistrantLog', 'id_registrant')->orderBy('created_at', 'desc');
	}


	public function employeeGroupName()
	{
		return $this->employeeGroup->group_name ?? '-';
	}


	public function genderText()
	{
		if($this->gender == 'L') return 'Pria';
		if($this->gender == 'P') return 'Wanita';

		return '-';
	}


	public function jobStatusText()
	{
		$jobStatusText = $this->job_status == self::JOBSTATUS_TETAP ? 'Tetap' : '';
		$jobStatusText = $this->job_status == self::JOBSTATUS_KONTRAK ? 'Kontrak' : $jobStatusText;

		return $jobStatusText;
	}


	public function jamsostekText()
	{
		return $this->jamsostek ?? '-';
	}


	public static function createNewRegistrant($request)
	{
		$registrant = self::create([
			'employee_name'	=> $request->name,
			'phone_number'	=> $request->phone_number,
			'email'			=> $request->email,
		]);

		// $password = rand(1000,9999);
        $password = 'rahasiaprimaplash';
		$registrant->setUser(null, $password);
		$registrant->sendAuthentication($password);
		$registrant->writeLog('Telah melakukan pendaftaran');

		return $registrant;
	}


	public function setUser($username = null, $password = null)
	{
		if(empty($this->user)) {
			$password = $password ?? rand(1000,9999);
			$password = \Hash::make($password);

			$user = \App\User::create([
				'name'		=> $this->employee_name,
				'username'	=> $username ?? $this->phone_number,
				'password'	=> $password,
				'role'		=> \App\User::ROLE_REGISTRANT,
			]);

			$this->update([
				'id_user'	=> $user->id
			]);
		} else {
			if(!empty($username)) {
				$user = \App\User::where('username', $username)
								 ->where('id', '!=', $this->id_user)->first();

				if($user) throw new Exception("Username tidak tersedia", 1);
			}

			$this->user->update([
				'username'	=> $username ?? $this->user->username,
				'password'	=> $password ?? $this->user->password,
			]);
		}

		return $this;
	}


	public function sendAuthentication($password = null)
	{
		$user = \App\User::find($this->id_user);
		if(!$user) return $this;

		try {
			$message = "Silahkan login ke ".route('login')." menggunakan akun berikut";
			$message .= "\nUsername : ".$user->username;
			$message .= !empty($password) ? "\nPassword : ".$password : '';
			$this->sendChat($message);
		} catch (\Exception $e) {}



		try {
			\Mail::to($this->email)->send(new \App\Mail\RegisterOTPMail($this, $user->username, $password));
		} catch (\Exception $e) {}

		return $this;
	}


	public function resetUserAndSend()
	{
		$password = rand(1000,9999);
		if($this->user) {
			$this->user->update([
				'password'	=> \Hash::make($password),
			]);
		}

		$this->sendAuthentication($password);

		return $this;
 	}


	public function writeLog($description = null)
	{
		$log = RegistrantLog::create([
			'id_registrant'	=> $this->id,
			'status'		=> $this->registration_status ?? self::STATUS_UNFILL,
			'description'	=> $description,
		]);

		return $this;
	}


	public function isStatusUnfill()
	{
		return $this->registration_status == self::STATUS_UNFILL;
	}


	public function isStatusWaiting()
	{
		return $this->registration_status == self::STATUS_WAITING;
	}


	public function isStatusApproved()
	{
		return $this->registration_status == self::STATUS_APPROVED;
	}


	public function isStatusRejected()
	{
		return $this->registration_status == self::STATUS_REJECTED;
	}


	public function statusText()
	{
		try {
			return self::availableStatus()[$this->registration_status];
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


	public function photoPath($size = 'original')
	{
		return storage_path("app/public/registrant/{$size}/{$this->photo}");
	}


	public function photoLink($size = 'original')
	{
		if($this->isHasPhoto()) {
			return url("storage/registrant/{$size}/{$this->photo}");
		} else {
			return url("storage/system/no_available_square.jpg");
		}
	}


	public function isHasPhoto()
	{
		if(empty($this->photo)) return false;

		return \File::exists($this->photoPath());
	}


	public function removePhoto()
	{
		try {
			if(\File::exists($this->photoPath('original'))) \File::delete($this->photoPath('original'));
			if(\File::exists($this->photoPath('face'))) \File::delete($this->photoPath('face'));
			if(\File::exists($this->photoPath('thumb'))) \File::delete($this->photoPath('thumb'));

			$this->update([
				'photo'	=> null
			]);
		} catch (\Exception $e) {

		}

		return $this;
	}


	public function setPhoto($request)
	{
		if(!empty($request->photo)) {
			$file = $request->file('photo');
			$filename = rand(100,999)."_".$file->getClientOriginalName();
			$this->removePhoto();

			$file->move(storage_path('app/public/registrant/original'), $filename);
			$this->update([
				'photo'	=> $filename
			]);

			$this->resizerPhoto();
		}

		return $this;
	}


	public function saveProfile($request)
	{
		$this->update([
			'employee_number'	=> $request->employee_number,
			'employee_name'		=> $request->employee_name,
			'gender'			=> $request->gender,
			'email'				=> $request->email,
			'phone_number'		=> $request->phone_number,
			'id_department'		=> $request->id_department,
			'id_position'		=> $request->id_position,
			'shift_type'		=> $request->shift_type,
			'id_shift'			=> $request->id_shift,
			'id_employee_group'	=> $request->id_employee_group,
			'jamsostek'			=> $request->jamsostek,
			'job_status'		=> $request->job_status,
			'start_working_date'=> $request->start_working_date,
			'place_of_birth'	=> $request->place_of_birth,
			'date_of_birth'		=> $request->date_of_birth,
			'address'			=> $request->address,
			'last_education'	=> $request->last_education,
			'last_education_major' => $request->last_education_major,
			'marital_status'	=> $request->marital_status,
			'blood_type'		=> $request->blood_type,
			'ktp_number'		=> $request->ktp_number,
			'npwp_number'		=> $request->npwp_number,
			'edited_at'			=> now(),
		]);

		$this->setPhoto($request);
		$this->setStatusWaiting();
		$this->writeLog('Profil diperbarui');

		return $this;
	}


	public function rotatePhotoToLeft()
	{
		$this->rotate(90);
		return $this;
	}


	public function rotatePhotoToRight()
	{
		$this->rotate(-90);
		return $this;
	}


	private function rotate($degree)
	{
		if($this->isHasPhoto())
		{
			$img = Image::make($this->photoPath());
			$img->rotate($degree);
			$img->save();
			$this->resizerPhoto();
		}

		return $this;
	}


	private function resizerPhoto()
	{
		\App\MyClass\Resizer::createThumbSize($this->photoPath(), $this->photo, $this->photoPath('thumb'));
		\App\MyClass\Resizer::createFaceSize($this->photoPath(),$this->photo, $this->photoPath('face'));

		return $this;
	}


	public function setStatusWaiting()
	{
		$this->update([
			'registration_status'	=> self::STATUS_WAITING
		]);

		return $this;
	}


	public function setStatusApproved()
	{
		$this->update([
			'registration_status'	=> self::STATUS_APPROVED
		]);

		return $this;
	}


	public function setStatusRejected()
	{
		$this->update([
			'registration_status'	=> self::STATUS_REJECTED
		]);

		return $this;
	}


	public function editedAtText()
	{
		return \Date::fullDateWithDayNameAndTime($this->edited_at);
	}


	public function rejectedAtText()
	{
		return \Date::fullDateWithDayNameAndTime($this->rejected_at);
	}


	public function approvedAtText()
	{
		return \Date::fullDateWithDayNameAndTime($this->approved_at);
	}


	public static function isExistsRegistrantWithStatusWaiting()
	{
		return self::amountOfRegistrantWithStatusWaiting() > 0;
	}


	public static function amountOfRegistrantWithStatusWaiting()
	{
		return self::where('registration_status', self::STATUS_WAITING)->count();
	}


	public function departmentName()
	{
		return !empty($this->department) ? $this->department->department_name : '-';
	}


	public function shiftName()
	{
		return !empty($this->shift) ? $this->shift->shift_name : '-';
	}


	public function positionName()
	{
		return !empty($this->position) ? $this->position->position_name : '-';
	}


	public static function apiDT($request)
	{
		$data = self::with([ 'department' ]);

		if(!empty($request->status)) {
			$status = $request->status;

			if($status != 'all') {
				$data = $data->where('registration_status', $request->status);
			}
		}

		$data = $data->get();

		return \DataTables::of($data)
			->addColumn('department_name', function($data){
				return $data->departmentName();
			})
			->editColumn('status', function($data){
				return $data->statusHtml();
			})
			->addColumn('photo', function($data){
				$photo = '<img src="'.$data->photoLink('face').'">';

				return $photo;
			})
			->editColumn('created_at', function($data){
				return $data->createdAtText();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('registration.detail', $data->id).'" title="Detail Pendaftaran">
							<i class="mdi mdi-magnify"></i> Detail
						</a>';

				if(UserPermission::check('registration', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);"  data-href="'.route('registration.destroy', $data->id).'" title="Hapus Pendaftaran">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				$button .= '
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'photo', 'status', 'action' ])
			->make(true);
	}


	public function approve()
	{
		$this->update([
			'registration_status'	=> self::STATUS_APPROVED,
			'approved_at'			=> now(),
		]);

		$this->copyRegistrantToEmployee();
		$this->writeLog();
		$this->swithUserFromRegistrantToEmployee();

		return $this;
	}


	public function copyRegistrantToEmployee()
	{
		$data = [
			'employee_number'	=> $this->employee_number,
			'employee_name'		=> $this->employee_name,
			'gender'			=> $this->gender,
			'email'				=> $this->email,
			'phone_number'		=> $this->phone_number,
			'jamsostek'			=> $this->jamsostek,
			'job_status'		=> $this->job_status,
			'id_department'		=> $this->id_department,
			'id_position'		=> $this->id_position,
			'shift_type'		=> $this->shift_type,
			'id_shift'			=> $this->id_shift,
			'id_user'			=> $this->id_user,
			'id_employee_group'	=> $this->id_employee_group,
			'start_working_date'=> $this->start_working_date,
			'place_of_birth'	=> $this->place_of_birth,
			'date_of_birth'		=> $this->date_of_birth,
			'address'			=> $this->address,
			'last_education'	=> $this->last_education,
			'last_education_major' => $this->last_education_major,
			'marital_status'	=> $this->marital_status,
			'blood_type'		=> $this->blood_type,
			'ktp_number'		=> $this->ktp_number,
			'npwp_number'		=> $this->npwp_number,
			'photo'				=> $this->photo,
			'status'			=> Employee::STATUS_ACTIVE,
		];

		if(empty($this->id_employee))
		{
			$employee = Employee::create($data);
			$this->update([
				'id_employee'	=> $employee->id,
			]);
		} else {
			$employee = Employee::find($this->id_employee);
			$employee->update($data);
		}

		$this->copyPhotoRegistrantToEmployee();

		$message = "Pendaftaran disetujui";
		$message .= "\n\n*Sistem*";
		$this->sendChat($message);

		return $this;
	}


	private function copyPhotoRegistrantToEmployee()
	{
		$employee = Employee::find($this->id_employee);
		\File::copy($this->photoPath(), $employee->photoPath());
		\File::copy($this->photoPath('face'), $employee->photoPath('face'));
		\File::copy($this->photoPath('thumb'), $employee->photoPath('thumb'));

		$employee->update([
			'photo'	=> $this->photo
		]);

		return $this;
	}


	public function reject($request)
	{
		$this->update([
			'registration_status'	=> self::STATUS_REJECTED,
			'rejected_at'			=> now(),
		]);
		$this->writeLog($request->notes);

		$message = "Pendaftaran anda ditolak";
		$message .= !empty($request->notes) ? "\n*Catatan* : {$request->notes}" : "";
		$message .= "\n\n*Sistem*";

		$this->sendChat($message);

		return $this;
	}


	public function sendChat($message)
	{
		$EndPointWa = WhatsappNew::END_POINT_WA;
		if($EndPointWa == 'WA Baru'){
			// wa Baru
			return Helper::sendNotificationWhatsapp($phoneNumber = $this->phone_number, $message);
		}else{
			return \App\MyClass\Whatsapp::sendChat([
				'to'	=> $this->phone_number,
				'text'	=> $message,
			]);
		}
	}


	public function swithUserFromRegistrantToEmployee()
	{
		$this->user->update([
			'role'	=> \App\User::ROLE_STAFF,
		]);

		return $this;
	}


	public function createdAtText()
	{
		return date('Y-m-d H:i', strtotime($this->created_at));
	}


	public function deleteRegistrant()
	{
		try {
			\File::delete($this->photoPath());
		} catch (\Exception $e) { }

		foreach($this->registrantLogs as $log) {
			$log->delete();
		}

		if(!$this->isStatusApproved()) {
			$this->user->delete();
		}

		return $this->delete();
	}

}
