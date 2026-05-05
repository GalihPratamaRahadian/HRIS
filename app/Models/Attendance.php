<?php

namespace App\Models;

use App\MyClass\Helper;
use App\Mail\ClockInMail;
use App\MyClass\Whatsapp;
use App\Mail\ClockOutMail;
use App\MyClass\WhatsappNew;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
	protected $fillable = [ 'id_employee', 'id_shift', 'date', 'shift_clock_in', 'shift_clock_out', 'late_tolerance', 'clock_in', 'clock_out', 'clock_in_method', 'clock_out_method', 'type', 'description', 'late', 'overtime', 'is_overtime', 'clock_in_at', 'clock_out_at', 'id_employee_leave', 'id_off_day', 'id_sick_necessity_submission' ];


	const METHOD_FACETERMINAL 	= 'faceterminal';
	const METHOD_GADGET			= 'gadget';
	const METHOD_CARD			= 'card';
	const METHOD_FINGERPRINT	= 'fingerprint';
	const METHOD_SYSTEM			= 'system';
	const METHOD_ADMIN			= 'admin';
	const METHOD_WHATSAPP		= 'whatsapp';

	const TYPE_HADIR			= 1;
	const TYPE_SAKIT			= 2;
	const TYPE_IZIN				= 3;
	const TYPE_LIBUR			= 4;
	const TYPE_TANPA_KETERANGAN	= 5;
	const TYPE_CUTI				= 6;

	const NOT_OVERTIME			= 1;
	const OVERTIME				= 2;


	/**
	 * 	Relationship
	 * */
	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee')->withTrashed();
	}

	public function attendanceMeta()
	{
		return $this->hasOne('App\Models\AttendanceMeta', 'id_attendance');
	}

	public function shift()
	{
		return $this->belongsTo('App\Models\Shift', 'id_shift');
	}

	public function employeeLeave()
	{
		return $this->belongsTo('App\Models\EmployeeLeave', 'id_employee_leave');
	}

	public function offDay()
	{
		return $this->belongsTo('App\Models\OffDay', 'id_off_day');
	}


	public function dateText()
	{
		return \Date::fullDateWithDayName($this->date);
	}


	public function clockInText()
	{
		if(!$this->isTypeHadir()) return '-';
		return date('H:i:s', strtotime($this->clock_in));
	}


	public function clockOutText()
	{
		if(!$this->isTypeHadir()) return '-';
		if(empty($this->clock_out)) {
			return '-';
		}

		return date('H:i:s', strtotime($this->clock_out));
	}


	public function clockInAtText($format = 'H:i:s')
	{
		if(!$this->isTypeHadir() || empty($this->clock_in_at)) return '-';
		return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->clock_in_at)->format($format);
	}


	public function clockOutAtText($format = 'H:i:s')
	{
		if(!$this->isTypeHadir() || empty($this->clock_out_at)) return '-';
		return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->clock_out_at)->format($format);
	}


	public function clockInTextHoursAndMinutes()
	{
		return date('H:i', strtotime($this->clock_in));
	}


	public function clockOutTextHoursAndMinutes()
	{
		if(empty($this->clock_out)) {
			return '-';
		}

		return date('H:i', strtotime($this->clock_out));
	}


	public function clockInTextFull()
	{
		if(empty($this->clock_in)) {
			return '-';
		}

		return "{$this->clockInText()} WIB";
	}


	public function clockOutTextFull()
	{
		return "{$this->clockOutText()} WIB";
	}

    public function getStatusFromLeaveSubmission()
    {
        return $this->employeeLeave->leaveSubmission ? $this->employeeLeave->leaveSubmission->statusText() : '-';
    }


	public static function availableTypes()
	{
		return [
			self::TYPE_HADIR			=> 'Hadir',
			self::TYPE_SAKIT			=> 'Sakit',
			self::TYPE_IZIN				=> 'Izin',
			self::TYPE_LIBUR			=> 'Libur',
			self::TYPE_TANPA_KETERANGAN	=> 'Tanpa Keterangan',
			self::TYPE_CUTI				=> 'Cuti',
		];
	}


	public static function clockIn($request)
	{
		$attendance = self::create([
			'id_employee'	=> auth()->user()->karyawan->id,
			'date'			=> today(),
			'clock_in'		=> date('H:i:s'),
			'type'			=> self::TYPE_ON_DAY
		]);

		return $attendance;
	}


	public function clockOut($method, $date = null)
	{
		if(empty($date)) {
			$date = now();
		} else {
			try {
				$date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date);
			} catch (\Exception $e) {}
		}

		if(!$this->isOvertime())
		{
			$this->update([
				'clock_out'			=> $date->format('H:i:s'),
				'clock_out_method'	=> $method,
				'clock_out_at'		=> $date->format('Y-m-d H:i:s'),
			]);
		}
		else
		{
			$this->update([
				'clock_out'			=> $date->format('H:i:s'),
				'clock_out_method'	=> $method,
				'clock_out_at'		=> $date->format('Y-m-d H:i:s'),
				'overtime'			=> $this->overtimeCount(),
			]);

			// Pengajuan Lembur
			// $upahLembur = Staff::getGaji($karyawanId)->upah_lembur * $menitLembur / 60;
			// PengajuanLembur::create([
			// 	'kehadiran_id'  => $hadir->id,
			// 	'karyawan_id'   => $karyawanId,
			// 	'lama_lembur'   => $menitLembur,
			// 	'upah_lembur'   => $upahLembur,
			// 	'status'        => 'W',
			// ]);
		}

		if($this->employee) {
			if($this->employee->user) {
				$this->employee->user->createMobileAppNotification([
					'title'			=> 'Berhasil mengisi jam keluar',
					'message'		=> 'Kamu berhasil mengisi jam keluar pada '.$date->format('Y-m-d H:i:s'),
					'type'			=> 'Presensi',
					'id_reference'	=> $this->id,
				]);
			}
		}

		// $this->sendNotifikasiClockOut($hadir);

		return $this;
	}


	public function isOvertime()
	{
		return $this->is_overtime == self::OVERTIME ? true : false;
	}


	public function overtimeCount()
	{
		if(!$this->isOvertime()) return 0;

		$countMinutes = function($time) {
			return (int) strtotime(date('H:i', strtotime($time))) / 60;
		};

		$clockInMinutes = $countMinutes($this->clock_in);
		$clockOutMinutes = !empty($this->clock_out) ? $this->clock_out : date('H:i');
		$clockOutMinutes = $countMinutes($clockOutMinutes);

		return ($clockOutMinutes - $clockInMinutes);
	}


	public function lateTime()
	{
		$result['minutes'] = 0;
		$result['hours'] = 0;
		$minutes = $this->late;

		if($minutes >= 60) {
			$result['hours'] = floor($minutes / 60);
			$minutes -= $result['hours'] * 60;
		}

		$result['minutes'] = round($minutes);

		return $result;
	}


	public function lateTimeTextShort()
	{
		$lateTime = $this->lateTime();
		$hours = (string) $lateTime['hours'];
		$minutes = (string) $lateTime['minutes'];
		$result = '';

		$hours = strlen($hours) < 2 ? str_repeat('0', 2 - strlen($hours) ).$hours : $hours;
		$minutes = strlen($minutes) < 2 ? str_repeat('0', 2 - strlen($minutes) ).$minutes : $minutes;

		return "{$hours}:{$minutes}";
	}


	public function lateText()
	{
		$lateText = '';
		$late = $this->lateTime();

		if($late['hours'] > 0) {
			$lateText .= "{$late['hours']} jam";
		}

		if($late['minutes'] > 0 || ( $late['minutes'] == 0 && $late['hours'] == 0 ) ) {
			$lateText .= " {$late['minutes']} menit";
		}

		return trim($lateText);
	}


	public function overtimeText()
	{
		$overtimeText = '';
		// Jam
		if($this->overtime >= 60) {
			$overtime = floor($this->overtime / 60);
			$overtimeText .= "{$overtime} jam ";
		}
		// Menit
		if($this->overtime % 60 > 0) {
			$overtime = $this->overtime % 60;
			$overtimeText .= "{$overtime} menit";
		}

		if($overtimeText == '') $overtimeText = '0 menit';

		return trim($overtimeText);
	}


	public function isAlreadyClockOut()
	{
		if(!$this->isTypeHadir()) return true;

		return !empty($this->clock_out_at);
	}


	public function getAttendanceMinutes()
	{
		try {
			$employee = $this->employee;
			$shift = $employee->shift;



		} catch (Exception $e) {
			return 0;
		}


			$shift = Staff::getShiftHariIni($karyawanId);
			$hadir = Staff::getHadir($karyawanId);
			if($hadir->lembur == 'Y' || empty($shift))
			{
				$jamMasuk = $hadir->jam_masuk;
			}
			else
			{
				$jamMasuk = $hadir->jam_masuk <= $shift->jam_mulai? $shift->jam_mulai : $hadir->jam_masuk;
			}

			$jamKeluar = $hadir->jam_keluar != null? $hadir->jam_keluar : date('H:i:s');

			$menitKerja = floor((strtotime($jamKeluar) - strtotime($jamMasuk))/60);

			return $menitKerja;
	}


	public static function createAttendanceViaFaceTerminal($employeeID, $log)
	{
		$employee = Employee::find($employeeID);

		if(!$employee) return false;

		$attendance = $employee->latestAttendance;

		if(empty($attendance)) {
			if($employee->isAllowForClockIn()) {
				return self::storeAttendanceFromFaceTerminal($employee, $log);
			}
		} else {
			if(!$attendance->isAlreadyClockOut() && $employee->isAllowForClockOut())
			{
				// Clock Out
				$data = $attendance->clockOut(self::METHOD_FACETERMINAL);
				$attendance->setClockOutFaceTerminalLog($log->id);
				if($location = $log->getLocation()) {
					$attendance->setClockOutLocation($location->latitude, $location->longitude);
				}

                $data->sendClockOutNotification();

				return $attendance;
			}
		}


		if($employee->isAllowForClockIn()) {
			return self::storeAttendanceFromFaceTerminal($employee, $log);
		} else {
			return false;
		}

	}


	public static function storeAttendanceFromFaceTerminal($employee, $log, $enableNotification = true)
	{
		$shift = $employee->shiftByDate(date('Y-m-d', strtotime($log->date)));
		$clock = date('H:i:s', strtotime($log->date));
		$shiftClockIn = null;
		$shiftClockOut = null;
		$date = date('Y-m-d', strtotime($log->date));
		$lateTolerance = 0;

		if($shift) {
			$shiftClockIn = $shift->clockStartWithDate($date);
			$shiftClockOut = $shift->clockEndWithDate($date);
			$lateTolerance = $shift->late_tolerance;
		}

		self::where('date', $date)
			->where('id_employee', $employee->id)
			->whereIn('type', [
				self::TYPE_TANPA_KETERANGAN,
				self::TYPE_LIBUR,
				self::TYPE_HADIR
			])
			->delete();

		$late = $employee->isHasShift() ? $employee->lateMinutes($log->date) : 0;

		$logDate = strtotime($log->date) >= strtotime($log->created_at->format('Y-m-d H:i:s')) ? $log->created_at->format('Y-m-d H:i:s') : $log->date;

		$attendance = self::create([
			'id_employee'   	=> $employee->id,
			'date'   			=> $date,
			'shift_clock_in'	=> $shiftClockIn,
			'shift_clock_out'	=> $shiftClockOut,
			'clock_in'			=> date('H:i:s', strtotime($logDate)),
			'clock_in_at'		=> $logDate,
			'clock_in_method' 	=> self::METHOD_FACETERMINAL,
			'type'				=> self::TYPE_HADIR,
			'late'				=> $late,
			'late_tolerance'	=> $lateTolerance,
		]);

		$attendance->setClockInFaceTerminalLog($log->id);

		if($location = $log->getLocation()) {
			$attendance->setClockInLocation($location->latitude, $location->longitude);
		}

		if($enableNotification) {
			$attendance->sendClockInNotification();
			if($employee->user) {
				$employee->user->createMobileAppNotification([
					'title'			=> 'Berhasil mengisi jam masuk',
					'message'		=> 'Kamu berhasil mengisi jam masuk pada '.$logDate,
					'type'			=> 'Presensi',
					'id_reference'	=> $attendance->id,
				]);
			}
		}

		return $attendance;
	}


	public static function createAttendanceViaWebApp($request)
	{
		$photoBlob = base64_decode(explode(',', $request->blobImage)[1]);
		$tempFilename = date('Ymd_His_').rand(100,999).'.jpeg';
		$tempPath = \Setting::temps($tempFilename);
		\File::put($tempPath, $photoBlob);
		$employee = employee();

		if(setting('is_using_face_compare_for_attendance', 'yes') == 'yes')
		{
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

		$forOvertime = $request->for_overtime ? true : false;
		$late = !$forOvertime && $employee->isHasShift() ? $employee->lateMinutes() : 0;

		$shiftClockIn = null;
		$shiftClockOut = null;

		if($employee->isHasShift() && !$forOvertime)
		{
			$shift = $employee->shift;
			$shiftClockIn = $employee->clockStartActiveWithDate();
			$shiftClockOut = $employee->clockEndActiveWithDate();
		}

		$clockInAt = date('Y-m-d H:i:s');
		$attendance = Attendance::create([
			'id_employee'		=> $employee->id,
			'date'				=> date('Y-m-d'),
			'shift_clock_in'	=> $shiftClockIn,
			'shift_clock_out'	=> $shiftClockOut,
			'clock_in'			=> date('H:i:s'),
			'clock_in_method'	=> Attendance::METHOD_GADGET,
			'type'				=> Attendance::TYPE_HADIR,
			'late'				=> $late,
			'overtime'			=> 0,
			'is_overtime'		=> $forOvertime ? Attendance::OVERTIME : Attendance::NOT_OVERTIME,
			'clock_in_at'		=> $clockInAt,
		]);

		$attendance->setClockInPhoto($tempPath, $tempFilename);
		$attendance->setClockInLocation($request->latitude, $request->longitude);
		$attendance->sendClockInNotification();

		\File::delete($tempPath);

		if($employee->user) {
			$employee->user->createMobileAppNotification([
				'title'			=> 'Berhasil mengisi jam masuk',
				'message'		=> 'Kamu berhasil mengisi jam masuk pada '.$clockInAt,
				'type'			=> 'Presensi',
				'id_reference'	=> $attendance->id,
			]);
		}

		// return $attendance;
		return \Res::success([
			'message'	=> 'Berhasil mengisi jam masuk'
		]);
	}


	public function clockOutViaWebApp($request)
	{
		$photoBlob = base64_decode(explode(',', $request->blobImage)[1]);
		$tempFilename = date('Ymd_His_').rand(100,999).'.jpeg';
		$tempPath = \Setting::temps($tempFilename);
		\File::put($tempPath, $photoBlob);
		$employee = auth()->user()->employee;

		if(setting('is_using_face_compare_for_attendance', 'yes') == 'yes')
		{
			if(!$employee->isEmployeeFaceValid($tempPath)) {
				\File::delete($tempPath);

				return \Res::invalid([
					'message'	=> 'Foto karyawan tidak sesuai',
				]);
			}
		}

		$this->clockOut(self::METHOD_GADGET);
		$this->setClockOutPhoto($tempPath, $tempFilename);
		$this->setClockOutLocation($request->latitude, $request->longitude);
		$this->sendClockOutNotification();

		return \Res::success([
			'message'	=> 'Berhasil mengisi jam keluar'
		]);
	}


	public static function createAttendanceViaMobileApp($request)
	{
		if($request->hasFile('photo'))
		{
			$file = $request->file('photo');
			$tempFilename = date('Ymdhis_').rand(100,999).'.'.$file->getClientOriginalExtension();
			$file->move(\Setting::temps(''), $tempFilename);
			$tempPath = \Setting::temps($tempFilename);
		}
		else
		{
			try {
				$photoBlob = base64_decode(explode(',', $request->photo)[1]);
			} catch (\Exception $e) {
				$photoBlob = base64_decode(explode(',', $request->photo)[0]);
			}

			$tempFilename = date('Ymd_His_').rand(100,999).'.jpeg';
			$tempPath = \Setting::temps($tempFilename);
			\File::put($tempPath, $photoBlob);
		}

		$token = MobileAppToken::getByToken($request->token);
		$employee = $token->employee;

		if(setting('is_using_face_compare_for_attendance', 'yes') == 'yes')
		{
			if(!$employee->isEmployeeFaceValid($tempPath)) {
				\File::delete($tempPath);

				return \Res::invalid([
					'message'	=> 'Foto wajib menampakan wajah anda.',
				]);
			}
		}

		if(!$employee->isLocationValid($request->latitude, $request->longitude)) {
			return \Res::invalid([
				'message'	=> 'Tidak di izinkan isi kehadiran di lokasi ini',
			]);
		}

		$late = $employee->isHasShift() ? $employee->lateMinutes() : 0;

		$shiftClockIn = null;
		$shiftClockOut = null;

		if($employee->isHasShift())
		{
			$shift = $employee->shift;
			$shiftClockIn = $employee->clockStartActiveWithDate();
			$shiftClockOut = $employee->clockEndActiveWithDate();
		}

		$attendance = Attendance::create([
			'id_employee'		=> $employee->id,
			'date'				=> date('Y-m-d'),
			'shift_clock_in'	=> $shiftClockIn,
			'shift_clock_out'	=> $shiftClockOut,
			'clock_in'			=> date('H:i:s'),
			'clock_in_method'	=> Attendance::METHOD_GADGET,
			'type'				=> Attendance::TYPE_HADIR,
			'late'				=> $late,
			'overtime'			=> 0,
			'is_overtime'		=> Attendance::NOT_OVERTIME,
			'clock_in_at'		=> date('Y-m-d H:i:s'),
		]);

		$attendance->setClockInPhoto($tempPath, $tempFilename);
		$attendance->setClockInLocation($request->latitude, $request->longitude);
		$attendance->sendClockInNotification();

		\File::delete($tempPath);

		return \Res::success([
			'message'	=> 'Berhasil mengisi jam masuk'
		]);
	}


	public static function clockOutViaMobileApp($request)
	{
		try {
			$photoBlob = base64_decode(explode(',', $request->image)[1]);
		} catch (\Exception $e) {
			$photoBlob = base64_decode(explode(',', $request->image)[0]);
		}
		$tempFilename = date('Ymd_His_').rand(100,999).'.jpeg';
		$tempPath = \Setting::temps($tempFilename);
		\File::put($tempPath, $photoBlob);

		$token = MobileAppToken::getByToken($request->token);
		$employee = $token->employee;

		if(setting('is_using_face_compare_for_attendance', 'yes') == 'yes')
		{
			if(!$employee->isEmployeeFaceValid($tempPath)) {
				\File::delete($tempPath);

				return \Res::invalid([
					'message'	=> 'Foto karyawan tidak sesuai',
				]);
			}
		}

		$attendance = $employee->latestAttendance;

		$attendance->clockOut(self::METHOD_GADGET);
		$attendance->setClockOutPhoto($tempPath, $tempFilename);
		$attendance->setClockOutLocation($request->latitude, $request->longitude);
		$attendance->sendClockOutNotification();

		return \Res::success([
			'message'	=> 'Berhasil mengisi jam keluar'
		]);
	}


	/**
	 * 	Attend via Whatsapp
	 * */
	public static function clockInOrClockOutViaWhatsapp($employee, $whatsappResult)
	{
		if($employee->isAllowForClockOut())
		{
			$photoFilename = basename($whatsappResult['file_path']);
			$attendance = auth()->user()->employee->latestAttendance;
			$attendance->clockOut(self::METHOD_WHATSAPP);
			$attendance->setClockOutPhoto($whatsappResult['file_path'], $photoFilename);
			$attendance->sendClockOutNotification();

			return $attendance;
		}
		elseif($employee->isAllowForClockIn())
		{
			$forOvertime = $employee->isOvertime();
			$late = !$forOvertime && $employee->isHasShift() ? $employee->shift->getLateMinutes() : 0;

			$shiftClockIn = null;
			$shiftClockOut = null;
			$shift = null;

			if($employee->isHasShift() && !$forOvertime)
			{
				$shift = $employee->shift;
				$shiftClockIn = $shift->clockStartWithDate();
				$shiftClockOut = $shift->clockEndWithDate();
			}

			$attendance = self::create([
				'id_employee'		=> $employee->id,
				'id_shift'			=> $shift ? $shift->id : null,
				'date'				=> date('Y-m-d'),
				'shift_clock_in'	=> $shiftClockIn,
				'shift_clock_out'	=> $shiftClockOut,
				'clock_in'			=> date('H:i:s'),
				'clock_in_method'	=> Attendance::METHOD_WHATSAPP,
				'type'				=> Attendance::TYPE_HADIR,
				'late'				=> $late,
				'overtime'			=> 0,
				'is_overtime'		=> $forOvertime ? Attendance::OVERTIME : Attendance::NOT_OVERTIME,
				'clock_in_at'		=> date('Y-m-d H:i:s'),
			]);

			$photoFilename = basename($whatsappResult['file_path']);
			$attendance->setClockInPhoto($whatsappResult['file_path'], $photoFilename);
			$attendance->sendClockInNotification();

			return $attendance;
		}
		else
		{
			$message = "Hai, *". $employee->employee_name ."*";
			$message .= "\n\nKamu belum boleh mengisi jam keluar";
			$message .= "\n\n*Attendance System*";

			$EndPointWa = WhatsappNew::END_POINT_WA;
			if($EndPointWa == 'WA Baru'){
				// wa Baru
				Helper::sendNotificationWhatsapp($phoneNumber = $employee->phone_number, $message);
			}else{
				\App\MyClass\Whatsapp::sendChat([
					'to'	=> $employee->phone_number,
					'text'	=> $message,
				]);
			}
		}
	}



	/**
	*			SEND NOTIFICATION
	*
	*/
	public function sendClockInNotification()
	{
		$employee = $this->employee;

		if(!$employee) return false;

		if(empty($employee->phone_number)) return false;

		// if(\ActionHistory::getHistory(date('Y-m-d'), \ActionHistory::EMPLOYEE_ALREADY_CLOCK_IN, $employee->id)) return false;

		$message = '';

		$message = $this->dateText();
		$message .= "\n{$this->clockInTextFull()}";
		$message .= "\n\nTerima kasih {$employee->firstName()}, kamu telah mengisi jam masuk hari ini";

		if($this->late > 0) {
			$message .= "\nKamu terlambat hadir ".$this->lateText();
			$message .= ", jangan diulangi lagi lho.";
		} else {
			$message .= "\nKamu hadir tepat waktu^^";
		}

		$photoPath = null;

		if($meta = AttendanceMeta::where('id_attendance', $this->id)->first())
		{
			if(!empty($log = $meta->clockInFaceTerminalLog))
			{
				$message .="\nSuhu tubuh mu {$log->temperatureText()}";
				$message .= $log->isUsingMask() ? "\nTerima kasih telah telah menggunakan masker" : "\nKamu tidak menggunakan masker. Harap gunakan masker.";

				$photoPath = $log->watermarkedPhotoPath();
			}
			elseif(!empty($meta->isHasClockInPhoto()))
			{
				$photoPath = $meta->clockInPhotoPath();
			}
		}

		$message .= "\nSemangat dan selamat menjalani aktivitas^^";
		$message .= "\n\n*Adiva HRIS*";


        $EndPointWa = WhatsappNew::END_POINT_WA;
        if($EndPointWa == 'WA Baru'){
            // wa Baru
            $res = Helper::sendNotificationWhatsapp($phoneNumber = $employee->phone_number, $message, $filePath=$photoPath, $caption="Absen Datang");
        }else{
            $res = Whatsapp::sendChat([
                'to'    => $employee->phone_number,
                'text'  => $message,
            ]);

            if(!empty($photoPath)) {
                Whatsapp::sendMedia([
                    'to'    => $employee->phone_number,
                    'path'  => $photoPath,
                ]);
            }
        }

		try {
    		$data = [
                'message' => $message,
            ];

            $title = "Pemberitahuan Jam Masuk";

            Mail::to($employee->email)->send(new ClockInMail($data, $title));
		} catch (\Exception $e) {}
    		\ActionHistory::createNotificationHistory(
    			\ActionHistory::EMPLOYEE_ALREADY_CLOCK_IN,
    			$employee->id,
    			"Notifikasi jam masuk ke {$employee->employee_name}"
    		);

		$messageOk = "Harap Ketik Ok/Ya";
        $EndPointWa = WhatsappNew::END_POINT_WA;
        if($EndPointWa == 'WA Baru'){
            // wa Baru
            Helper::sendNotificationWhatsapp($phoneNumber = $employee->phone_number, $messageOk);
        }else{
            Whatsapp::sendChat([
                'to'    => $employee->phone_number,
                'text'  => $messageOk,
            ]);
        }

		return $res;
	}


	public function sendClockOutNotification()
	{
		$employee = $this->employee;

		if(!$employee) return false;

		if(empty($employee->phone_number)) return false;

		// if(\ActionHistory::getHistory(date('Y-m-d'), \ActionHistory::EMPLOYEE_ALREADY_CLOCK_OUT, $employee->id)) return false;

		$message = '';

		$message = $this->dateText();
		$message .= "\n{$this->clockOutTextFull()}";
		$message .= "\n\nTerima kasih {$employee->firstName()}, kamu telah mengisi jam keluar kehadiran tanggal ".$this->clockInAtText('d F Y').".";

		$message .= "\n\nBerikut adalah jam kehadiranmu : ";
		$message .= "\n{$this->clockInText()} - {$this->clockOutText()}";

		$message .= "\n\n*Adiva Attendance System*";

        $EndPointWa = 'WA Baru';
        if($EndPointWa == 'WA Baru'){
            // wa Baru
            if($meta = AttendanceMeta::where('id_attendance', $this->id)->first())
            {
                if(!empty($log = $meta->clockOutFaceTerminalLog))
                {
                   $photoPath = $log->watermarkedPhotoPath();
                }
                else
                {
                    $photoPath = $meta->clockOutPhotoPath();
                }
            }
            Helper::sendNotificationWhatsapp($phoneNumber = $employee->phone_number, $message, $filePath=$photoPath, $caption="Absen Pulang");
        }else{
            // wa lama
            Whatsapp::sendChat([
                'to'    => $employee->phone_number,
                'text'  => $message,
            ]);

            if($meta = AttendanceMeta::where('id_attendance', $this->id)->first())
            {
                if(!empty($log = $meta->clockOutFaceTerminalLog))
                {
                    Whatsapp::sendMedia([
                        'to'    => $employee->phone_number,
                        'path'  => $log->watermarkedPhotoPath(),
                    ]);
                }
                else
                {
                    Whatsapp::sendMedia([
                        'to'    => $employee->phone_number,
                        'path'  => $meta->clockOutPhotoPath(),
                    ]);
                }
            }
        }

        try {
    		$data = [
                'message' => $message,
            ];

            $title = "Pemberitahuan Jam Keluar";

            Mail::to($employee->email)->send(new ClockOutMail($data, $title));
		} catch (\Exception $e) {}

		\ActionHistory::createNotificationHistory(
			\ActionHistory::EMPLOYEE_ALREADY_CLOCK_OUT,
			$employee->id,
			"Notifikasi jam keluar ke {$employee->employee_name}"
		);

		return true;
	}


	public static function sendReminderForAttend()
	{
		foreach(\App\Models\Employee::getActiveEmployees() as $employee) {
			$employee->sendReminderForAttend();
		}
	}



	public function saveClockInPhoto($request)
	{
		if(!empty($request->clock_in_photo))
		{
			$file = $request->file('clock_in_photo');
			$filename = $this->date . '_' . \Str::slug($this->employeeName(), '_') .'.'. $file->getClientOriginalExtension();
			$file->move(storage_path('app/public/attendance'), $filename);

			if($this->isHasMeta()) {
				$this->attendanceMeta->update([
					'clock_in_photo'	=> $filename,
				]);
			} else {
				AttendanceMeta::create([
					'id_attendance'		=> $this->id,
					'clock_in_photo'	=> $filename,
				]);
			}
		}

		return $this;
	}




	/**
	*			SET META
	*
	*/
	public function setClockInPhoto($path, $filename)
	{
		\File::copy($path, storage_path('app/public/attendance/'.$filename));

		if($meta = AttendanceMeta::where('id_attendance', $this->id)->first())
		{
			$meta->update([
				'clock_in_photo'	=> $filename,
			]);
		}
		else
		{
			AttendanceMeta::create([
				'id_attendance'		=> $this->id,
				'clock_in_photo'	=> $filename
			]);
		}
		return $this;
	}

	public function setClockOutPhoto($path, $filename)
	{
		\File::copy($path, storage_path('app/public/attendance/'.$filename));

		if($meta = AttendanceMeta::where('id_attendance', $this->id)->first())
		{
			$meta->update([
				'clock_out_photo'	=> $filename,
			]);
		}
		else
		{
			AttendanceMeta::create([
				'id_attendance'		=> $this->id,
				'clock_out_photo'	=> $filename
			]);
		}
		return $this;
	}

	public function setClockInFaceTerminalLog($logID)
	{
		$log = FaceTerminalLog::find($logID);
		if(empty($log)) return $this;

		if($meta = AttendanceMeta::where('id_attendance', $this->id)->first())
		{
			$meta->update([
				'id_clock_in_face_terminal_log'	=> $logID,
			]);
		}
		else
		{
			AttendanceMeta::create([
				'id_attendance'					=> $this->id,
				'id_clock_in_face_terminal_log'	=> $logID
			]);
		}

		$this->setClockInPhoto($log->photoPath(), $log->photo);

		return $this;
	}

	public function setClockOutFaceTerminalLog($logID)
	{
		$log = FaceTerminalLog::find($logID);
		if(empty($log)) return $this;

		if($meta = AttendanceMeta::where('id_attendance', $this->id)->first())
		{
			$meta->update([
				'id_clock_out_face_terminal_log'	=> $logID,
			]);
		}
		else
		{
			AttendanceMeta::create([
				'id_attendance'						=> $this->id,
				'id_clock_out_face_terminal_log'	=> $logID
			]);
		}

		$this->setClockOutPhoto($log->photoPath(), $log->photo);
		$this->update([
			'clock_out_at' => date('Y-m-d H:i:s', strtotime($log->date)),
			'clock_out' => date('H:i:s', strtotime($log->date)),
		]);

		return $this;
	}

	public function setClockInLocation($latitude, $longitude)
	{
		if($meta = AttendanceMeta::where('id_attendance', $this->id)->first())
		{
			$meta->update([
				'clock_in_location'	=> serialize((object) [ 'latitude' => $latitude, 'longitude' => $longitude ]),
			]);
		}
		else
		{
			AttendanceMeta::create([
				'id_attendance'		=> $this->id,
				'clock_in_location'	=> serialize((object) [ 'latitude' => $latitude, 'longitude' => $longitude ]),
			]);
		}
		return $this;
	}

	public function setClockOutLocation($latitude, $longitude)
	{
		if($meta = AttendanceMeta::where('id_attendance', $this->id)->first())
		{
			$meta->update([
				'clock_out_location'	=> serialize((object) [ 'latitude' => $latitude, 'longitude' => $longitude ]),
			]);
		}
		else
		{
			AttendanceMeta::create([
				'id_attendance'			=> $this->id,
				'clock_out_location'	=> serialize((object) [ 'latitude' => $latitude, 'longitude' => $longitude ]),
			]);
		}
		return $this;
	}


	public function employeeName()
	{
		return !empty($this->employee) ? $this->employee->employee_name : 'Karyawan tidak terdaftar';
	}

	public function departmentName()
	{
		return !empty($this->employee) ? $this->employee->departmentName() : '-';
	}

	public function positionName()
	{
		return !empty($this->employee) ? $this->employee->positionName() : '-';
	}


	public static function todayAttendance()
	{
		return self::where('date', date('Y-m-d'))->with([ 'employee' ])->get();
	}


	public static function dataTable($request)
	{
		$data = self::select([ 'attendances.*' ])
					->leftJoin('employees', 'employees.id', '=', 'attendances.id_employee')
					->leftJoin('departments', 'departments.id', '=', 'employees.id_department')
					->leftJoin('positions', 'positions.id', '=', 'employees.id_position')
					->leftJoin('employee_groups', 'employee_groups.id', '=', 'employees.id_employee_group')
					->has('employee')
					->with([ 'employee.department', 'employee.position', 'employee.employeeGroup', 'attendanceMeta' ]);

		if(!empty($request->start_date)) {
			$data = $data->where('date', '>=', $request->start_date);
		}

		if(!empty($request->end_date)) {
			$data = $data->where('date', '<=', $request->end_date);
		}

		if(!empty($type = $request->type)) {
			if($type != 'all') {
				$data = $data->where('attendances.type', $request->type);
			}
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

		if(!empty($employeeId = $request->id_employee)) {
			if($employeeId != 'all') {
				$data = $data->where('id_employee', $employeeId);
			}
		}

		return \DataTables::eloquent($data)
			->addColumn('employees.employee_name', function($data){
				return "<a href='".route('employee.detail', $data->id_employee)."' target='_blank'> {$data->employeeName()} </a>";
			})
			->addColumn('departments.department_name', function($data){
				return $data->departmentName();
			})
			->editColumn('clock_in', function($data){
				return $data->clockInTextFull();
			})
			->editColumn('clock_out', function($data){
				return $data->clockOutTextFull();
			})
			->editColumn('late', function($data){
				return $data->lateText();
			})
			->editColumn('type', function($data){
				return $data->typeHtml();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('attendance.detail', $data->id).'" title="Detail Kehadiran">
							<i class="mdi mdi-magnify"></i> Detail
						</a>';

				if($data->isTypeHadir()) {
					$button .= '
						<a class="dropdown-item action-btn" href="javascript:void(0);" data-href="'.route('attendance.send_clock_in_notification', $data->id).'" title="Kirim Notif Absen Masuk" data-message="Yakin ingin kirim absen masuk?">
							<i class="mdi mdi-whatsapp"></i> Kirim Notif Absen Masuk
						</a>';
				}

				if($data->isTypeHadir() && $data->isAlreadyClockOut()) {
					$button .= '
						<a class="dropdown-item action-btn" href="javascript:void(0);" data-href="'.route('attendance.send_clock_out_notification', $data->id).'" title="Kirim Notif Absen Keluar" data-message="Yakin ingin kirim absen keluar?">
							<i class="mdi mdi-whatsapp"></i> Kirim Notif Absen Keluar
						</a>';
				}

				if(UserPermission::check('attendance', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('attendance.edit', $data->id).'" title="Edit Kehadiran">
							<i class="mdi mdi-pencil"></i> Edit
						</a>';
				}

				if(UserPermission::check('attendance', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('attendance.destroy', $data->id).'" title="Hapus Kehadiran">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				$button .= '
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'employees.employee_name', 'type', 'action' ])
			->make(true);
	}


	public function typeText()
	{
		$text = $this->isTypeHadir() ? 'Hadir' : '';
		$text = $this->isTypeSakit() ? 'Sakit' : $text;
		$text = $this->isTypeIzin() ? 'Izin' : $text;
		$text = $this->isTypeCuti() ? 'Cuti' : $text;
		$text = $this->isTypeLibur() ? 'Libur' : $text;
		$text = $this->isTypeTanpaKeterangan() ? 'Alpa' : $text;

		return $text;
	}


	public function typeHtml()
	{
		$text = $this->isTypeHadir() ? '<span class="text-success">'.$this->typeText().'</span>' : '';
		$text = $this->isTypeSakit() ? '<span class="text-warning">'.$this->typeText().'</span>' : $text;
		$text = $this->isTypeIzin() ? '<span class="text-warning">'.$this->typeText().'</span>' : $text;
		$text = $this->isTypeCuti() ? '<span class="text-primary">'.$this->typeText().'</span>' : $text;
		$text = $this->isTypeLibur() ? '<span class="text-primary">'.$this->typeText().'</span>' : $text;
		$text = $this->isTypeTanpaKeterangan() ? '<span class="text-danger">'.$this->typeText().'</span>' : $text;

		return $text;
	}


	public static function getAttendanceSummary($request)
	{
		$date = $request->date;
		$startDate = $request->start_date;
		$endDate = $request->end_date;

		$summary = [
			'hadir'	=> 0,
			'sakit'	=> 0,
			'izin'	=> 0,
			'cuti'	=> 0,
			'libur'	=> 0,
			'tanpa_keterangan'	=> 0,
		];

		$attendances = self::with([ 'employee', 'attendanceMeta' ])
						   ->has('employee');

		if(!empty($date)) {
			$attendances = $attendances->where('date', $date);
		} else {
			if(!empty($startDate)) {
				$attendances = $attendances->where('date', '>=', $startDate);
			}

			if(!empty($endDate)) {
				$attendances = $attendances->where('date', '<=', $endDate);
			}
		}

		if(!empty($departmentId = $request->id_department)) {
			if($departmentId !== 'all') {
				$attendances = $attendances->whereHas('employee', function($query) use ($departmentId){
					$query->where('id_department', $departmentId);
				});
			}
		}

		if(!empty($positionId = $request->id_position)) {
			if($positionId !== 'all') {
				$attendances = $attendances->whereHas('employee', function($query) use ($positionId){
					$query->where('id_position', $positionId);
				});
			}
		}

		if(!empty($employeeGroupId = $request->id_employee_group)) {
			if($employeeGroupId !== 'all') {
				$attendances = $attendances->whereHas('employee', function($query) use ($employeeGroupId){
					$query->where('id_employee_group', $employeeGroupId);
				});
			}
		}

		if(!empty($employeeId = $request->id_employee)) {
			if($employeeId != 'all') {
				$attendances = $attendances->where('id_employee', $employeeId);
			}
		}

		$attendances = $attendances->get();

		foreach($attendances as $attendance)
		{
			if ($attendance->type == self::TYPE_HADIR) {
				$summary['hadir']++;
			} elseif($attendance->type == self::TYPE_SAKIT) {
				$summary['sakit']++;
			} elseif($attendance->type == self::TYPE_IZIN) {
				$summary['izin']++;
			} elseif($attendance->type == self::TYPE_CUTI) {
				$summary['cuti']++;
			} elseif($attendance->type == self::TYPE_LIBUR) {
				$summary['libur']++;
			} elseif($attendance->type == self::TYPE_TANPA_KETERANGAN) {
				$summary['tanpa_keterangan']++;
			}
		}

		if($date == now()->format('Y-m-d'))
		{
			$belumHadir = Employee::where('status', Employee::STATUS_ACTIVE)
							->whereDoesntHave('attendance', function($query) use ($date) {
								$query->where('date', $date);
							});
			if(!empty($departmentId = $request->id_department)) {
				if($departmentId !== 'all') {
					$belumHadir = $belumHadir->where('id_department', $departmentId);
				}
			}

			if(!empty($positionId = $request->id_position)) {
				if($positionId !== 'all') {
					$belumHadir = $belumHadir->where('id_position', $positionId);
				}
			}

			if(!empty($employeeGroupId = $request->id_employee_group)) {
				if($employeeGroupId !== 'all') {
					$belumHadir = $belumHadir->where('id_employee_group', $employeeGroupId);
				}
			}

			if(!empty($employeeId = $request->id_employee)) {
				if($employeeId !== 'all') {
					$belumHadir = $belumHadir->where('id_employee', $employeeId);
				}
			}

			$belumHadir = $belumHadir->count();

			$summary['belum_hadir'] = $belumHadir;
		}
		else
		{
			$summary['belum_hadir'] = 0;
		}

		return $summary;
	}


	public function isHasMeta()
	{
		return !empty($this->attendanceMeta) ? true : false;
	}


	public static function methodText($methodCode)
	{
		if ($methodCode == self::METHOD_FACETERMINAL) {
			return 'Faceterminal';
		} elseif ($methodCode == self::METHOD_FINGERPRINT) {
			return 'Fingerprint';
		} elseif ($methodCode == self::METHOD_GADGET) {
			return 'Gadget';
		} elseif ($methodCode == self::METHOD_CARD) {
			return 'Kartu';
		} elseif ($methodCode == self::METHOD_ADMIN) {
			return 'Admin';
		} elseif ($methodCode == self::METHOD_WHATSAPP) {
			return 'Whatsapp';
		} else {
			return '-';
		}
	}


	public function clockInMethodText()
	{
		return self::methodText($this->clock_in_method);
	}


	public function clockOutMethodText()
	{
		return self::methodText($this->clock_out_method);
	}


	public function getWorkSecondTime()
	{
		// $clockIn = strtotime($this->shift_clock_in) > strtotime($this->clock_in)? $this->shift_clock_in : $this->clock_in;
		$clockIn = $this->shift_clock_in ?? $this->clock_in;
		$clockIn = date('H:i:s', strtotime($clockIn));
		$clockOut = $this->isAlreadyClockOut() ? date('H:i:s', strtotime($this->shift_clock_out)) :  $this->clock_out;
		$clockOut = empty($clockOut) ? date('H:i:s') : $clockOut;

		$lateSeconds = $this->late * 60;
		$workSeconds = strtotime($clockOut) - strtotime($clockIn);
		$workSeconds = $workSeconds - $lateSeconds;

		return (double) $workSeconds;
		// return $clockIn.' # '.$clockOut;
	}


	public function getWorkMinuteTime()
	{
		$workMinutes = $this->getWorkSecondTime() / 60;

		return (double) $workMinutes;
	}


	public function getWorkHourTime()
	{
		$workHours = $this->getWorkMinuteTime() / 60;

		return (double) $workHours;
	}


	public function getHourTime()
	{
		if(empty($this->shift_clock_in) && empty($this->shift_clock_out)) {
			if(!empty($this->employee->shift)) {
				return $this->employee->shift->getShiftHours($this->date);
			}
		}

		$clockIn = $this->shift_clock_in ?? $this->clock_in;
		$clockOut = $this->shift_clock_out ?? $this->clock_out;
		$clockOut = empty($clockOut) ? date('H:i:s') : $clockOut;

		$hourTime = ((strtotime($clockOut) - strtotime($clockIn)) / 60) / 60;

		return (double) $hourTime;
	}


	public function getWorkTime()
	{
		$workTime = strtotime($this->shift_clock_out) - strtotime($this->shift_clock_in);
		$workTime -= $this->late_tolerance * 60;
		$workTimeInHour = $workTime / 60 / 60;

		return round($workTimeInHour, 1);
	}


	public function getAttendTime()
	{
		if($this->isTypeHadir()) {
			$workTime = $this->getWorkTime();
			$attendTimeInSeconds = $this->getAttendTimeInSeconds();

			$attendTimeInHour = (($attendTimeInSeconds / 60) + $this->late_tolerance) / 60;
			$attendTimeInHour = $attendTimeInHour > $workTime ? $workTime : $attendTimeInHour;

			return round($attendTimeInHour, 1);
		} else {
			return 0;
		}

	}





	public function getAttendTimeInSeconds()
	{
		if($this->isTypeHadir()) {
			$clockIn = date('H:i:s', strtotime($this->clock_in_at));
			$clockOut = date('H:i:s', strtotime($this->clock_out_at));
			$shiftIn = date('H:i:s', strtotime($this->shift_clock_in));
			$shiftOut = date('H:i:s', strtotime($this->shift_clock_out));

			$clockIn = $shiftIn >= $clockIn ? $shiftIn : $clockIn;
			$clockOut = $shiftOut <= $clockOut ? $shiftOut : $clockOut;

			$attendTime = strtotime($clockOut) - strtotime($clockIn) + ($this->late_tolerance * 60);

			return $attendTime;
		} else {
			return 0;
		}
	}


	public function getWorkTimeInDetail()
	{
		$workSeconds = $this->getAttendTimeInSeconds();
		$result['seconds'] = 0;
		$result['minutes'] = 0;
		$result['hours'] = 0;

		if($workSeconds > 3600) {
			$workHours = floor($workSeconds / 3600);
			$result['hours'] = $workHours;
			$workSeconds -= $workHours * 3600;
		}

		if($workSeconds > 60) {
			$workMinutes = floor($workSeconds / 60);
			$result['minutes'] = $workMinutes;
			$workSeconds -= $workMinutes * 60;
		}

		if($workSeconds > 0) {
			$result['seconds'] = $workSeconds;
		}

		return $result;
	}


	public function getWorkTimeText()
	{
		$workTime = $this->getWorkTimeInDetail();
		$result = '';

		if($workTime['hours'] > 0) {
			$result .= "{$workTime['hours']} jam";
		}

		if($workTime['minutes'] > 0) {
			$result .= " {$workTime['minutes']} menit";
		}

		if($workTime['seconds'] > 0) {
			$result .= " {$workTime['seconds']} detik";
		}

		return trim($result);
	}


	public function getWorkTimePercent()
	{
		return round($this->getWorkHourTime() / $this->getHourTime(), 2);
	}


	public function getWorkTimeTextShort()
	{
		$workTime = $this->getWorkTimeInDetail();
		$hours = (string) $workTime['hours'];
		$minutes = (string) $workTime['minutes'];
		$result = '';

		$hours = strlen($hours) < 2 ? str_repeat('0', 2 - strlen($hours) ).$hours : $hours;
		$minutes = strlen($minutes) < 2 ? str_repeat('0', 2 - strlen($minutes) ).$minutes : $minutes;

		return "{$hours}:{$minutes}";
	}


	public function dailySalary($defaultSalary = 0, $defaultOvertimePay = 0)
	{
		try {
			if($this->isTypeHadir() || $this->isTypeSakit() || $this->isTypeCuti())
			{
				if($this->isOvertime()) {
					return $this->getWorkHourTime() * $this->employee->overtimePay($defaultOvertimePay);
				} else {
					$salary = $this->employee->dailySalary($this->date, $defaultSalary);
					$cutNominal = $this->cutNominal($defaultSalary);
					return $cutNominal;

					return $salary - $cutNominal;
				}
			}
		} catch (Exception $e) {}

		return 0;
	}


	public function cutNominal($defaultSalary = 0)
	{
		$salary = $this->employee->dailySalary($this->date, $defaultSalary);
		$lateCutType = \Setting::getValue('late_cut_type');

		if ($lateCutType == 'each_minutes') {
			return $salary * $this->percentageOfAttend();
		} elseif ($lateCutType == 'every_few_minutes') {
			$lateCutDuration = \Setting::getValue('late_cut_duration');
			$lateCutNominal = \Setting::getValue('late_cut_nominal');
			$lateCutTimes = ceil($this->late / $lateCutDuration);
			$cutNominal = $lateCutNominal * $lateCutTimes;

			return floor($cutNominal);
		}
	}


	public function cutNominalText($defaultSalary = 0)
	{
		return 'Rp. '.number_format($this->cutNominal($defaultSalary));
	}


	public function dailySalaryText()
	{
		return 'Rp. '.number_format($this->dailySalary());
	}





	public static function autoFillNotAttend()
	{
		$employees = Employee::doesntHave('todayAttendance')
							 ->where('status', Employee::STATUS_ACTIVE)
							 ->get();
		$date = date('Y-m-d');

		foreach($employees as $employee)
		{
			$attendance = self::where('date', $date)
							  ->where('id_employee', $employee->id)
							  ->where('type', self::TYPE_TANPA_KETERANGAN)
							  ->first();

			if(!$attendance) {
				Attendance::create([
					'id_employee' 		=> $employee->id,
					'date' 				=> $date,
					'clock_in_method' 	=> self::METHOD_SYSTEM,
					'clock_out_method' 	=> self::METHOD_SYSTEM,
					'type' 				=> self::TYPE_TANPA_KETERANGAN,
					'late' 				=> 0,
					'overtime' 			=> 0,
					'is_overtime'		=> false,
				]);
			}
		}
	}


	public function isTypeHadir()
	{
		return $this->type == self::TYPE_HADIR;
	}


	public function isTypeSakit()
	{
		return $this->type == self::TYPE_SAKIT;
	}


	public function isTypeIzin()
	{
		return $this->type == self::TYPE_IZIN;
	}


	public function isTypeLibur()
	{
		return $this->type == self::TYPE_LIBUR;
	}


	public function isTypeTanpaKeterangan()
	{
		return $this->type == self::TYPE_TANPA_KETERANGAN;
	}


	public function isTypeCuti()
	{
		return $this->type == self::TYPE_CUTI;
	}


	public function isLate()
	{
		return $this->late > 0;
	}


	public function typeBadgeHtml()
	{
		$text = $this->isTypeHadir() ? '<span class="badge p-2 badge-success attendance-type">'.$this->typeText().'</span>' : '';
		$text = $this->isTypeSakit() ? '<span class="badge p-2 badge-warning attendance-type">'.$this->typeText().'</span>' : $text;
		$text = $this->isTypeIzin() ? '<span class="badge p-2 badge-warning attendance-type">'.$this->typeText().'</span>' : $text;
		$text = $this->isTypeCuti() ? '<span class="badge p-2 badge-primary attendance-type">'.$this->typeText().'</span>' : $text;
		$text = $this->isTypeLibur() ? '<span class="badge p-2 badge-primary attendance-type">'.$this->typeText().'</span>' : $text;
		$text = $this->isTypeTanpaKeterangan() ? '<span class="badge p-2 badge-danger attendance-type">'.$this->typeText().'</span>' : $text;

		return $text;
	}


	public static function generateAttendancesSummaryData($request)
	{
		$employees = Employee::where('status', Employee::STATUS_ACTIVE)
							 ->with([ 'department', 'position', 'employeeGroup' ]);

		if($request->id_department != 'all') {
			$employees = $employees->where('id_department', $request->id_department);
		}

		if($request->id_position != 'all') {
			$employees = $employees->where('id_position', $request->id_position);
		}

		if($request->id_employee_group != 'all') {
			$employees = $employees->where('id_employee_group', $request->id_employee_group);
		}

		if($request->id_employee != 'all') {
			$employees = $employees->where('id', $request->id_employee);
		}

		$employees = $employees->get();

		return (object) [
			'employees'		=> $employees,
			'start_date'	=> $request->start_date,
			'end_date'		=> $request->end_date,
		];
	}


	public static function generateAttendancesSummary($request)
	{
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', -1);


		$data = self::generateAttendancesSummaryData($request);

		$type = $request->type;

		if($type == 'type_1') {
			return self::generateAttendancesSummaryType1($data);
		} elseif($type == 'type_2') {
			return self::generateAttendancesSummaryType2($data);
		} elseif($type == 'type_3') {
			return self::generateAttendancesSummaryType3($data);
		}
	}


	public static function generateAttendancesSummaryType1($data)
	{
		$writer = new \App\MyClass\XLSXWriter();

		// Master Style
		$sBorder = [ 'border' => 'left,top,right,bottom', 'border-style' => 'thin' ];
		$sValignCenter = [ 'valign' => 'center' ];
		$sAlignCenter = [ 'valign' => 'center', 'halign' => 'center' ];

		// Sheet
		$sheet1 = 'Rekapan Kehadiran';


		// Make Header Column
		// First
		$colNum = 1;
		$style = [];
		$headerRow1st = [];
		$headerRow1st[ 'Karyawan' ] = 'string';
		// $headerRow1st[] = 'Karyawan';
		$colNum++;
		$style[] = array_merge($sAlignCenter, $sBorder);
		$widths = [];
		$widths[] = 20;

		$date = new \Carbon\Carbon($data->start_date);
		$limit = new \Carbon\Carbon($data->end_date);
		$limit = $limit->addDays(1);
		$dates = [];

		while ($date->format('Y-m-d') != $limit->format('Y-m-d')) {
			$headerRow1st[ $date->format('Y-m-d') ] = 'string' ;
			$dates[] = $date->format('Y-m-d');
			$colNum++;
			$headerRow1st["{$colNum}"] = 'string';
			$colNum++;
			$headerRow1st["{$colNum}"] = 'string';
			$widths[] = 7;
			$widths[] = 7;
			$widths[] = 9;

			$date = $date->addDays(1);
		}

		$style = array_merge($sAlignCenter, [ 'widths' => $widths ], $sBorder);
		$writer->writeSheetHeader($sheet1, $headerRow1st, $style);
		// Merge
		$i = 1;
		while($i <= (count($headerRow1st))) {
			$startColoumn = $i;
			$endColumn = $i + 2;
			$i = $endColumn + 1;
			$writer->markMergedCell($sheet1, $start_row=0, $start_col=$startColoumn, $end_row=0, $end_col=$endColumn);
		}

		// Second
		$headerRow2nd = [];
		$headerRow2nd[] = '';
		$style = [];
		$style[] = array_merge($sAlignCenter, $sBorder);
		for ($i = 1; $i <= (count($headerRow1st)/3); $i++) {
			$headerRow2nd[] = 'Masuk';
			$headerRow2nd[] = 'Keluar';
			$headerRow2nd[] = 'Terlambat';
			$style[] = array_merge($sAlignCenter, $sBorder);
			$style[] = array_merge($sAlignCenter, $sBorder);
			$style[] = array_merge($sAlignCenter, $sBorder);
		}
		$writer->writeSheetRow($sheet1, $headerRow2nd, $style);

		// Merge
		$writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=1, $end_col=0);


		// Baris Karyawan
		foreach($data->employees as $employee)
		{
			$style = [];
			$employeeRow = [ $employee->employee_name ];
			$style[] = array_merge( $sBorder, [ 'valign' => 'center' ]);
			$lastDate = '';
			$attendances = self::where('id_employee', $employee->id)
								->where('date', '>=', $data->start_date)
								->where('date', '<=', $data->end_date)
								->orderBy('date', 'asc')
								->get();
			$datesIter = 0;
			$attendanceDate = null;
			$attendanceType = null;

			foreach($attendances as $attendance)
			{
				if($attendanceDate == $attendance->date && $attendanceType == $attendance->type) {
					continue;
				} else {
					$attendanceDate = $attendance->date;
					$attendanceType = $attendance->type;
				}

				$atStyle = array_merge($sAlignCenter, $sBorder);

				$attendDate = date('Y-m-d', strtotime($attendance->date));

				try {
					while($dates[$datesIter] != $attendDate) {

						$employeeRow[] = '';
						$employeeRow[] = '';
						$employeeRow[] = '';
						$atStyle = array_merge($atStyle, [ 'color' => '#000000' ]);

						$style[] = $atStyle;
						$style[] = $atStyle;
						$datesIter++;

						if($datesIter == count($dates)) {
							$date = $dates[$datesIter - 1];
						} else {
							$date = $dates[$datesIter];
						}
					}
				} catch (\Exception $e) {
					$message = "Karyawan : ".$attendance->employeeName();
					$message .= "\n\ndate : ".$attendDate;

					\Whatsapp::sendChat([
						'to'	=> '6282316425264',
						'text'	=> $message
					]);
					exit();
					die();
				}

				if($attendance->isTypeHadir()) {
					$employeeRow[] = $attendance->clockInTextHoursAndMinutes();
					$employeeRow[] = $attendance->clockOutTextHoursAndMinutes();
					$employeeRow[] = $attendance->late.' menit';
				} elseif($attendance->isTypeTanpaKeterangan()) {
					$employeeRow[] = 'Alpa';
					$employeeRow[] = 'Alpa';
					$employeeRow[] = '-';
					$atStyle = array_merge($atStyle, [ 'color' => '#000000' ]);
				} else {
					$employeeRow[] = $attendance->typeText();
					$employeeRow[] = $attendance->typeText();
					$employeeRow[] = '-';
					$atStyle = [ 'fill' => '#0000ff' ];
				}

				$style[] = $atStyle;
				$style[] = $atStyle;
				$style[] = $atStyle;
				$datesIter++;
			}

			$addEmptyBorder = count($dates) - ($datesIter);
			for ($i = 1; $i <= $addEmptyBorder; $i++) {
				$employeeRow[] = '';
				$employeeRow[] = '';
				$employeeRow[] = '';
			}

			$writer->writeSheetRow($sheet1, $employeeRow, array_merge($sAlignCenter, $sBorder, [ 'height' => 25 ]));
		}


		$filename = 'Rekapan_Kehadiran_'.rand(100,999).'_'.date('Ymd', strtotime($data->start_date)).'_'.date('Ymd', strtotime($data->start_date)).'.xlsx';
		$path = storage_path('app/public/'.$filename);
		$writer->writeToFile($path);

		return [
			'file_data'	=> base64_encode(\File::get($path)),
			'file_mime'	=> mime_content_type($path),
			'file_name'	=> $filename,
		];
	}


	public static function generateAttendancesSummaryType2($data)
	{
		$writer = new \App\MyClass\XLSXWriter();

		// Master Style
		$sBorder = [ 'border' => 'left,top,right,bottom', 'border-style' => 'thin' ];
		$sValignCenter = [ 'valign' => 'center' ];
		$sAlignCenter = [ 'valign' => 'center', 'halign' => 'center' ];

		// Sheet
		$sheet1 = 'Rekapan Kehadiran';

		// Make Header Column
		// First
		$colNum = 1;
		$style = [];
		$headerRow1st = [];
		$headerRow1st[ 'Karyawan' ] = 'string';
		// $headerRow1st[] = 'Karyawan';
		$colNum++;
		$style[] = array_merge($sAlignCenter, $sBorder);
		$widths = [];
		$widths[] = 20;

		$date = new \Carbon\Carbon($data->start_date);
		$limit = new \Carbon\Carbon($data->end_date);
		$limit = $limit->addDays(1);
		$dates = [];

		while ($date->format('Y-m-d') != $limit->format('Y-m-d')) {
			$headerRow1st[ $date->format('Y-m-d') ] = 'string' ;
			$dates[] = $date->format('Y-m-d');
			$colNum++;
			$headerRow1st["{$colNum}"] = 'string';
			$colNum++;
			$headerRow1st["{$colNum}"] = 'string';
			$widths[] = 7;
			$widths[] = 7;
			$widths[] = 9;

			$date = $date->addDays(1);
		}

		$writer->writeSheetHeader($sheet1, [
			'Date'		=> 'string',
			'Name'		=> 'string',
			'Check In'	=> 'string',
			'Check Day'	=> 'string',
			'Check Out'	=> 'string',
			'Terlambat'	=> 'string',
		], array_merge($sAlignCenter, [
			'widths' => [
				20, 30, 12, 12, 12, 12,
			],
			'font-style' => 'bold',
		], $sBorder));


		// Baris Karyawan
		foreach($data->employees as $employee)
		{
			$style = [];
			$employeeRow = [ $employee->employee_name ];
			$style[] = array_merge( $sBorder, [ 'valign' => 'center' ]);
			$lastDate = '';
			$attendances = self::where('id_employee', $employee->id)
								->where('date', '>=', $data->start_date)
								->where('date', '<=', $data->end_date)
								->orderBy('date', 'asc')
								->get();
			$datesIter = 0;
			$attendanceDate = null;
			$attendanceType = null;

			foreach($attendances as $attendance)
			{
				if($attendanceDate == $attendance->date && $attendanceType == $attendance->type) {
					continue;
				} else {
					$attendanceDate = $attendance->date;
					$attendanceType = $attendance->type;
				}

				$atStyle = array_merge($sAlignCenter, $sBorder);

				$attendDate = date('Y-m-d', strtotime($attendance->date));

				try {
					while($dates[$datesIter] != $attendDate) {
						$datesIter++;

						if($datesIter == count($dates)) {
							$date = $dates[$datesIter - 1];
						} else {
							$date = $dates[$datesIter];
						}
					}
				} catch (\Exception $e) {
					$message = "Karyawan : ".$attendance->employeeName();
					$message .= "\n\ndate : ".$attendDate;

                    $EndPointWa = WhatsappNew::END_POINT_WA;
                    if($EndPointWa == 'WA Baru'){
                        // wa Baru
                        Helper::sendNotificationWhatsapp($phoneNumber = '6282316425264', $message);
                    }else{
                        \App\MyClass\Whatsapp::sendChat([
                            'to'	=> '6282316425264',
                            'text'	=> $message,
                        ]);
                    }
					exit();
					die();
				}

				$clockDay = '-';

				if($attendance->isTypeHadir()) {
					$clockIn = $attendance->clockInTextHoursAndMinutes();
					$clockOut = $attendance->clockOutTextHoursAndMinutes();
					$late = $attendance->late.' menit';
					$checkDay = CheckDay::where('id_employee', $employee->id)
										->where('check_day_at', 'like', '%'.$attendance->date.'%')
										->orderBy('created_at', 'desc')
										->first();
					if($checkDay) $clockDay = $checkDay->checkDayAtText('H:i:s');
				} elseif($attendance->isTypeTanpaKeterangan()) {
					$clockIn = 'Alpa';
					$clockOut = 'Alpa';
					$late = '-';
				} else {
					$clockIn = $attendance->typeText();
					$clockOut = $attendance->typeText();
					$late = '-';
				}

				$writer->writeSheetRow($sheet1, [
					$attendance->date,
					$employee->employee_name,
					$clockIn,
					$clockDay,
					$clockOut,
					$late,
				], array_merge($sAlignCenter, $sBorder));
				$datesIter++;
			}
		}

		$filename = 'Rekapan_Kehadiran_'.rand(100,999).'_'.date('Ymd', strtotime($data->start_date)).'_'.date('Ymd', strtotime($data->start_date)).'.xlsx';
		$path = storage_path('app/public/'.$filename);
		$writer->writeToFile($path);

		return [
			'file_data'	=> base64_encode(\File::get($path)),
			'file_mime'	=> mime_content_type($path),
			'file_name'	=> $filename,
		];
	}

	public static function generateAttendancesSummaryType3($data)
	{
		$writer = new \App\MyClass\XLSXWriter();

		// Master Style
		$sBorder = [ 'border' => 'left,top,right,bottom', 'border-style' => 'thin' ];
		$sValignCenter = [ 'valign' => 'center' ];
		$sAlignCenter = [ 'valign' => 'center', 'halign' => 'center' ];

		// Sheet
		$sheet1 = 'Rekapan Kehadiran';


		// Make Header Column
		// First
		$colNum = 1;
		$style = [];
		$headerRow1st = [];
		$headerRow1st[ 'Karyawan' ] = 'string';
		// $headerRow1st[] = 'Karyawan';
		$colNum++;
		$style[] = array_merge($sAlignCenter, $sBorder);
		$widths = [];
		$widths[] = 20;

		$date = new \Carbon\Carbon($data->start_date);
		$limit = new \Carbon\Carbon($data->end_date);
		$limit = $limit->addDays(1);
		$dates = [];

		while ($date->format('Y-m-d') != $limit->format('Y-m-d')) {
			$headerRow1st[ $date->format('Y-m-d') ] = 'string' ;
			$dates[] = $date->format('Y-m-d');
			$colNum++;
			$headerRow1st["{$colNum}"] = 'string';
			$colNum++;
			$widths[] = 7;
			$widths[] = 9;

			$date = $date->addDays(1);
		}

		$style = array_merge($sAlignCenter, [ 'widths' => $widths ], $sBorder);
		$writer->writeSheetHeader($sheet1, $headerRow1st, $style);
		// Merge
		$i = 1;
		while($i <= (count($headerRow1st))) {
			$startColoumn = $i;
			$endColumn = $i + 1;
			$i = $endColumn + 1;
			$writer->markMergedCell($sheet1, $start_row=0, $start_col=$startColoumn, $end_row=0, $end_col=$endColumn);
		}

		// Second
		$headerRow2nd = [];
		$headerRow2nd[] = '';
		$style = [];
		$style[] = array_merge($sAlignCenter, $sBorder);
		for ($i = 1; $i <= (count($headerRow1st)/2); $i++) {
			$headerRow2nd[] = 'Masuk';
			$headerRow2nd[] = 'Terlambat';
			$style[] = array_merge($sAlignCenter, $sBorder);
			$style[] = array_merge($sAlignCenter, $sBorder);
		}
		$writer->writeSheetRow($sheet1, $headerRow2nd, $style);

		// Merge
		$writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=1, $end_col=0);


		// Baris Karyawan
		foreach($data->employees as $employee)
		{
			$style = [];
			$employeeRow = [ $employee->employee_name ];
			$style[] = array_merge( $sBorder, [ 'valign' => 'center' ]);
			$lastDate = '';
			$attendances = self::where('id_employee', $employee->id)
								->where('date', '>=', $data->start_date)
								->where('date', '<=', $data->end_date)
								->orderBy('date', 'asc')
								->get();
			$datesIter = 0;
			$attendanceDate = null;
			$attendanceType = null;

			foreach($attendances as $attendance)
			{
				if($attendanceDate == $attendance->date && $attendanceType == $attendance->type) {
					continue;
				} else {
					$attendanceDate = $attendance->date;
					$attendanceType = $attendance->type;
				}

				$atStyle = array_merge($sAlignCenter, $sBorder);

				$attendDate = date('Y-m-d', strtotime($attendance->date));

				try {
					while($dates[$datesIter] != $attendDate) {

						$employeeRow[] = '';
						$employeeRow[] = '';
						$employeeRow[] = '';
						$atStyle = array_merge($atStyle, [ 'color' => '#000000' ]);

						$style[] = $atStyle;
						$style[] = $atStyle;
						$datesIter++;

						if($datesIter == count($dates)) {
							$date = $dates[$datesIter - 1];
						} else {
							$date = $dates[$datesIter];
						}
					}
				} catch (\Exception $e) {
					$message = "Karyawan : ".$attendance->employeeName();
					$message .= "\n\ndate : ".$attendDate;

					$EndPointWa = WhatsappNew::END_POINT_WA;
                    if($EndPointWa == 'WA Baru'){
                        // wa Baru
                        Helper::sendNotificationWhatsapp($phoneNumber = '6282316425264', $message);
                    }else{
                        \App\MyClass\Whatsapp::sendChat([
                            'to'	=> '6282316425264',
                            'text'	=> $message,
                        ]);
                    }
					exit();
					die();
				}

				if($attendance->isTypeHadir()) {
					$employeeRow[] = $attendance->clockInTextHoursAndMinutes();
					$employeeRow[] = $attendance->late.' menit';
				} elseif($attendance->isTypeTanpaKeterangan()) {
					$employeeRow[] = 'Alpa';
					$employeeRow[] = '-';
					$atStyle = array_merge($atStyle, [ 'color' => '#000000' ]);
				} else {
					$employeeRow[] = $attendance->typeText();
					$employeeRow[] = '-';
					$atStyle = [ 'fill' => '#0000ff' ];
				}

				$style[] = $atStyle;
				$style[] = $atStyle;
				$style[] = $atStyle;
				$datesIter++;
			}

			$addEmptyBorder = count($dates) - ($datesIter);
			for ($i = 1; $i <= $addEmptyBorder; $i++) {
				$employeeRow[] = '';
				$employeeRow[] = '';
			}

			$writer->writeSheetRow($sheet1, $employeeRow, array_merge($sAlignCenter, $sBorder, [ 'height' => 25 ]));
		}


		$filename = 'Rekapan_Kehadiran_'.rand(100,999).'_'.date('Ymd', strtotime($data->start_date)).'_'.date('Ymd', strtotime($data->start_date)).'.xlsx';
		$path = storage_path('app/public/'.$filename);
		$writer->writeToFile($path);

		return [
			'file_data'	=> base64_encode(\File::get($path)),
			'file_mime'	=> mime_content_type($path),
			'file_name'	=> $filename,
		];
	}


	public static function clearSundayDouble()
	{
		$attendances = self::where('date', '2021-11-07')
						   ->where('type', self::TYPE_LIBUR)
						   ->get();

		$employeesID = [];
		$deleted = 0;

		foreach($attendances as $attendance)
		{
			if (!in_array($attendance->id_employee, $employeesID)) {
				$employeesID[] = $attendance->id_employee;
			} else {
				$attendance->delete();
				$deleted++;
			}
		}

		return $deleted;
	}


	public function syncAndRefresh()
	{
		$this->syncWithFaceTerminalLog();
		$this->refresh();

		return $this;
	}


	public function syncWithFaceTerminalLog()
	{
		if($this->isHasMetas())
		{
			$meta = $this->attendanceMeta;

			if($meta->isHasClockInFaceTerminalLog()) {
				$this->update([
					'clock_in_at'	=> $meta->clockInFaceTerminalLog->created_at,
				]);
			}

			if($meta->isHasClockOutFaceTerminalLog()) {
				$this->update([
					'clock_out_at'	=> $meta->clockOutFaceTerminalLog->created_at,
				]);
			}
		}

		return $this;
	}


	public function refresh()
	{
		if($this->isTypeHadir())
		{
			$shift = $this->shift;
			if($shift) {
				$clockIn = date('H:i:s', strtotime($this->clock_in));
				$this->update([
					'late'			=> $shift->getLateMinutes($clockIn, $this->date),
					'clock_in_at'	=> $this->date." ".$clockIn,
				]);
			}
		}

		return $this;
	}


	public static function refreshByRangeTime($start, $end)
	{
		$attendances = self::whereBetween('date', [$start, $end])->get();

		foreach($attendances as $attendance)
		{
			$attendance->refresh();
		}
	}


	public static function setShiftInEveryAttendance()
	{
		$attendances = self::where('type', 1)
							->where('id_shift', NULL)
							->get();
		$count = 0;

		foreach($attendances as $attendance)
		{
			if(!empty($attendance->shift_clock_in) && !empty($attendance->shift_clock_out))
			{
				$shift = Shift::where('clock_start', $attendance->shift_clock_in)
							  ->where('clock_end', $attendance->shift_clock_out)
							  ->first();

				if($shift)
				{
					$attendance->update([
						'id_shift'			=> $shift->id,
						'late_tolerance'	=> $shift->late_tolerance,
					]);
					$count++;
				}
			}
		}

		return $count;
	}


	public static function setShiftClockInAndShiftClockOut()
	{
		$attendances = self::where('id_shift', '!=', NULL)
						   ->where('type', 1)
						   ->get();
		$count = 0;

		foreach($attendances as $attendance)
		{
			$shiftClockIn = date('H:i:s', strtotime($attendance->shift_clock_in));
			$shiftClockOut = date('H:i:s', strtotime($attendance->shift_clock_out));
			$shiftClockInDate = $attendance->date;
			$shiftClockOutDate = $attendance->date;

			if(strtotime($shiftClockIn) > strtotime($shiftClockOut)) {
				$shiftClockOutDate = new \Carbon\Carbon($shiftClockOutDate);
				$shiftClockOutDate->addDays(1);
				$shiftClockOutDate = date('Y-m-d', strtotime($shiftClockOutDate));
			}

			$attendance->update([
				'shift_clock_in'	=> $shiftClockInDate." ".$shiftClockIn,
				'shift_clock_out'	=> $shiftClockOutDate." ".$shiftClockOut,
			]);

			$count++;
		}

		return $count;
	}


	public function deleteAttendance()
	{
		$meta = $this->attendanceMeta;
		if($meta)
		{
			try {
				\File::delete($meta->clockInPhotoPath());
				\File::delete($meta->clockOutPhotoPath());
				$meta->delete();
			} catch (\Exception $e) { }
		}

		return $this->delete();
	}


	public function setLateMinutes()
	{
		if(!$this->isTypeHadir()) return $this;

		if($this->employee) {
			if($this->employee->shift) {
				$shift = $this->employee->shift;
				$clock = date('H:i:s', strtotime($this->clock_in_at));
				$this->update([
					'late'	=> $shift->getLateMinutes($clock),
				]);
			}
		}

		return $this;
	}


	public function clockInAtFormatDatetimeLocal()
	{
		$date = $this->clock_in_at ?? $this->date;
		return date('Y-m-d', strtotime($date)).'T'.date('H:i', strtotime($this->clock_in_at));
	}


	public function clockOutAtFormatDatetimeLocal()
	{
		$date = $this->clock_out_at ?? $this->date;
		return date('Y-m-d', strtotime($this->date)).'T'.date('H:i', strtotime($this->clock_out_at));
	}

	public function shiftClockInFormatDatetimeLocal()
	{
		$date = $this->shift_clock_in ?? $this->date;
		return date('Y-m-d', strtotime($date)).'T'.date('H:i', strtotime($this->shift_clock_in));
	}

	public function shiftClockOutFormatDatetimeLocal()
	{
		$date = $this->shift_clock_out ?? $this->date;
		return date('Y-m-d', strtotime($date)).'T'.date('H:i', strtotime($this->shift_clock_out));
	}


	public function updateAttendance($request)
	{
		$clock = date('H:i:s', strtotime($request->clock_in_at));
		$date = date('Y-m-d', strtotime($request->clock_in_at));

		$shift = $this->shift;
		$clockStart = $shift ? $shift->getShiftByDate($date)->clock_start : $clock;
		$clockEnd = $shift ? $shift->getShiftByDate($date)->clock_end : date('H:i:s', strtotime($request->clock_out_at));
		$dateStart = $date;
		$dateEnd = $date;

		// Shift Clock In
		if($request->shift_clock_in) {
			$shiftClockIn = date('Y-m-d H:i:s', strtotime($request->shift_clock_in));
		} else {
			$shiftClockIn = $dateStart.' '.$clockStart;
		}

		// Shift Clock Out
		if($request->shift_clock_out) {
			$shiftClockOut = date('Y-m-d H:i:s', strtotime($request->shift_clock_out));
		} else {
			$shiftClockOut = $dateEnd.' '.$clockEnd;
		}

		// Late Tolerance
		if($request->late_tolerance) {
			$lateTolerance = $request->late_tolerance;
		} else {
			$lateTolerance = $shift ? $shift->late_tolerance : 0;
		}

		if(strtotime($clockEnd) < strtotime($clockStart)) {
			$dateEnd = today()->addDays(-1);
			$dateEnd = date('Y-m-d', strtotime($dateEnd));
		}

		$method = $request->type == self::TYPE_HADIR ? self::METHOD_FACETERMINAL : self::METHOD_ADMIN;

		$this->update([
			'type'				=> $request->type,
			'date'				=> $date,
			'shift_clock_in'	=> $shiftClockIn,
			'shift_clock_out'	=> $shiftClockOut,
			'clock_in_at'		=> $date.' '.$clock,
			'clock_out_at'		=> date('Y-m-d H:i:s', strtotime($request->clock_out_at)),
			'late'				=> $this->shift ? $this->shift->getLateMinutes($clock, $date) : 0,
			'clock_in'			=> $clock,
			'clock_out'			=> date('H:i:s', strtotime($request->clock_out_at)),
			'late_tolerance'	=> $lateTolerance,
			'clock_in_method'	=> $method,
			'clock_out_method'	=> $method,
		]);

		$this->setLateMinutes();

		return $this;
	}


	public static function deleteAttendanceTypeAlpaByDate($date = null)
	{
		$date = $date ?? date('Y-m-d');

		return self::where('date', $date)
				   ->where('type', self::TYPE_TANPA_KETERANGAN)
				   ->delete();
	}




	/**
	 * 	Autofill Clockout
	 *
	 * */
	public static function autoFillClockOut()
	{
		if(appconfig('is_using_autofill_clock_out'))
		{
			$autofillMethod = appconfig('autofill_method');

			if ($autofillMethod == 'last_log') {
				self::autoFillClockOutWithLastLog();
			} elseif ($autofillMethod == 'shift_clock_end') {
				self::autoFillClockOutWithShiftClockEnd();
			}
		}
	}

	private static function autoFillClockOutWithLastLog()
	{
		$attendances = self::where('clock_out', null)
						   ->has('employee.faceTerminalUser')
						   ->with('employee.faceTerminalUser')
						   ->get();

		foreach($attendances as $attendance)
		{
			$faceTerminalUser = $attendance->employee->faceTerminalUser;
			$lastLog = FaceTerminalLog::where('date', $attendance->date)
									  ->where('auth_id', $faceTerminalUser->id)
									  ->orderBy('created_at', 'desc')
									  ->first();
			if($lastLog) {
				$attendance->update([
					'clock_out'			=> date('H:i:s', strtotime($lastLog->created_at)),
					'clock_out_method'	=> self::METHOD_FACETERMINAL,
					'clock_out_at'		=> now(),
				]);

				$attendance->setClockOutFaceTerminalLog($lastLog->id);
				$attendance->sendClockOutNotification();
			}
		}
	}

	public static function autoFillClockOutWithShiftClockEnd($date = null)
	{
		$date = $date ?? date('Y-m-d');
		$attendances = self::where('clock_out', null)
						   ->where('date', $date)
						   ->has('employee.shift')
						   ->with('employee.shift')
						   ->get();
		$count = 0;
		foreach($attendances as $attendance)
		{
			$shift = $attendance->employee->shift;

			$attendance->update([
				'clock_out'			=> date('H:i:s', strtotime($shift->clock_end)),
				'clock_out_at'		=> date('Y-m-d H:i:s', strtotime($date.' '.$shift->clock_end)),
				'clock_out_method'	=> self::METHOD_SYSTEM,
			]);

			$count++;
		}

		return $count;
	}


	/**
	 * 	Helper methods
	 * */
	public function isAllowForClockOut()
	{
		if(!$this->isAlreadyClockOut()) {
			return now()->format('Y-m-d H:i:s') > $this->shift_clock_out;
		} else {
			return false;
		}
	}


	public function getAttendTimeText()
	{
		$workTime = $this->getWorkTimeInDetail();
		$result = '';

		if($workTime['hours'] > 0) {
			$result .= "{$workTime['hours']} jam";
		}

		if($workTime['minutes'] > 0) {
			$result .= " {$workTime['minutes']} menit";
		}

		if($workTime['seconds'] > 0) {
			$result .= " {$workTime['seconds']} detik";
		}

		return trim($result);
	}


	/**
	 * 	Helper methods
	 * */
	public function getWorkTimeInMinutes()
	{
		$shiftClockIn = new \Carbon\Carbon($this->shift_clock_in);
		$shiftClockOut = new \Carbon\Carbon($this->shift_clock_out);
		return $shiftClockIn->diffInMinutes($shiftClockOut) - $this->late_tolerance;
	}

	public function getAttendTimeInMinutes()
	{
		$shiftClockIn = new \Carbon\Carbon($this->shift_clock_in);
		$clockIn = new \Carbon\Carbon($this->clock_in_at);

		$shiftClockIn->addMinutes($this->late_tolerance);

		if($shiftClockIn->format('Y-m-d H:i:s') > $clockIn->format('Y-m-d H:i:s')) {
			$clockIn = $shiftClockIn;
		}

		$clockOut = new \Carbon\Carbon($this->shift_clock_out);
		return $clockIn->diffInMinutes($clockOut);
	}

	public function getWorkTimeLikeClockFormat()
	{
		$hours = round($this->getAttendTimeInMinutes() / 60);
		$minutes = $this->getAttendTimeInMinutes() % 60;

		$hours = str_pad($hours, 2, 0, STR_PAD_LEFT);
		$minutes = str_pad($minutes, 2, 0, STR_PAD_LEFT);

		return $hours.':'.$minutes;
	}

	public function percentageOfAttend()
	{
		if($this->isTypeCuti()) {
			return 1;
		} elseif($this->isTypeSakit()) {
			return 1;
		} elseif($this->isTypeHadir()) {
			$workTime = $this->getWorkTimeInMinutes();
			$attendTime = $this->getAttendTimeInMinutes();

			if($workTime > 0) {
				return round($attendTime / $workTime, 4);
			}
		}

		return 0;
	}


	public function fetchData()
	{
		$meta = $this->attendanceMeta;

		return (object) [
			'id'			=> $this->id,
			'id_employee'	=> $this->id_employee,
			'type'			=> $this->typeText(),
			'employee_name'	=> $this->employeeName(),
			'date'			=> $this->dateText(),
			'clock_in'		=> $this->clockInText(),
			'clock_out'		=> $this->clockOutText(),
			'clock_in_photo_link' => $meta? $meta->clockInPhotoLink() : null,
			'clock_out_photo_link' => $meta? $meta->clockOutPhotoLink() : null,
			'clock_in_location' => $meta? $meta->getClockInLocation() : null,
			'clock_out_location' => $meta? $meta->getClockOutLocation() : null,
		];
	}


	public static function fetchAttendances($attendances)
	{
		$results = [];

		foreach($attendances as $attendance) {
			$results[] = $attendance->fetchData();
		}

		return $results;
	}

	/**
	 * 	Helper methods
	 * */
	public function removePhoto()
	{
		if($this->isHasMeta()) {
			$meta = $this->attendanceMeta;

			if($meta->isHasClockInPhoto()) {
				\File::delete($meta->clockInPhotoPath());
			}

			if($meta->isHasClockOutPhoto()) {
				\File::delete($meta->clockOutPhotoPath());
			}

			$meta->update([
				'clock_in_photo' => null,
				'clock_out_photo' => null,
			]);
		}

		return $this;
	}



	/**
	 * 	Static methods
	 * */
	public static function attendFromFaceTerminalLog()
	{
		foreach(Employee::getActiveEmployees() as $employee) {
			if($employee->isAllowForClockIn()) {
				$logs = FaceTerminalLog::where('name', $employee->employee_name)
									   ->where('date', 'like', "%".date('Y-m-d')."%")
									   ->get();
				$found = false;
				foreach($logs as $log) {
					if($found) continue;
					if($log->date >= $employee->clockStartLimitActive('Y-m-d H:i:s')) {
						self::storeAttendanceFromFaceTerminal($employee, $log, true);
						$found = true;
					}
				}
			}

			if($employee->isAllowForClockOut()) {
				$attendance = self::where('date', date('Y-m-d'))
								  ->where('id_employee', $employee->id)
								  ->where('type', self::TYPE_HADIR)
								  ->first();

				if($attendance) {
					$logs = FaceTerminalLog::where('name', $employee->employee_name)
										   ->where('date', 'like', "%".date('Y-m-d', strtotime($attendance->shift_clock_out))."%")
										   ->get();
					$found = false;
					foreach($logs as $log) {
						if($found) continue;
						if($log->date >= $attendance->shift_clock_out) {
							$attendance->clockOut(Attendance::METHOD_FACETERMINAL);
							$attendance->setClockOutFaceTerminalLog($log->id);
							if($location = $log->getLocation()) {
								$attendance->setClockOutLocation($location->latitude, $location->longitude);
							}
							$found = true;
						}
					}
				}

			}
		}


	}
}
