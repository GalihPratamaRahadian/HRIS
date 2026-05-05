<?php 

namespace App\MyClass;

class Validations
{

	public static function validateDepartment($request)
	{
		$request->validate([
			'department_name'	=> 'required',
		]);
	}


	public static function validatePosition($request)
	{
		$request->validate([
			'position_name'	=> 'required',
		]);
	}


	public static function validateShift($request)
	{
		$request->validate([
			'shift_name'		=> 'required',
			'clock_start'		=> 'required',
			'clock_end'			=> 'required',
			'late_tolerance'	=> 'nullable|numeric',
			'offday_shift.*'	=> 'nullable|numeric',
		]);
	}


	public static function validateEmployee($request)
	{
		$request->validate([
			'employee_name'		=> 'required',
			'gender'			=> 'required|in:L,P',
			'email'				=> 'nullable|email',
			'id_department'		=> 'required|exists:departments,id',
			'id_position'		=> 'required|exists:positions,id',
			'job_status'		=> 'required|in:kontrak,tetap,probation',
			'id_shift'			=> 'nullable|exists:shifts,id',
		]);
	}


	public static function validateEmployeePhoto($request)
	{
		$request->validate([
			'file_photo'	=> 'required|mimes:jpeg,jpg,png,gif',
		]);
	}


	public static function validateFaceTerminalDevice($request)
	{
		$request->validate([
			'device_name'	=> 'required',
			'ip_address'	=> 'required',
			'port'			=> 'required|numeric',
			'username'		=> 'required',
			'password'		=> 'required'
		]);
	}


	public static function validateOffDay($request)
	{
		$request->validate([
			'off_day_name'	=> 'required',
			'start_date'	=> 'required',
			'end_date'		=> 'required',
		]);
	}


	public static function validateEmployeeContract($request)
	{
		$request->validate([
			'id_employee'	=> 'required|exists:employees,id',
			'start_date'	=> 'required',
			'end_date'		=> 'required',
		]);
	}


	public static function validateEmployeeSalary($request)
	{
		$request->validate([
			'id_employee'	=> 'required|exists:employees,id|unique:employee_salaries,id_employee',
			'basic_salary'	=> 'required|numeric|min:0',
			'overtime_pay'	=> 'required|numeric|min:0',
		]);
	}


	public static function validateEmployeeShiftChangeSchedule($request)
	{
		$request->validate([
			'id_employee'	=> 'required|exists:employees,id',
			'id_shift'		=> 'required|exists:shifts,id',
			'date'			=> 'required',
		]);
	}


	public static function validateEmployeeLeave($request)
	{
		$request->validate([
			'id_employee'	=> 'required|exists:employees,id',
			'reason'		=> 'required',
			'start_date'	=> 'required',
			'end_date'		=> 'required|after_or_equal:start_date'
		], [
			'end_date.after_or_equal'	=> 'Tanggal akhir harus sama atau setelah tanggal awal'
		]);
	}


	public static function validateEmployeeLeaveQuota($request)
	{
		$request->validate([
			'id_employee'			=> 'required|exists:employees,id',
			'period_type'			=> 'required|in:monthly,yearly',
			'quota'					=> 'required|numeric|min:1',
			'is_allow_accumulation'	=> 'required|in:no,yes'
		], [
			'quota.min'	=> 'Minimal bernilai 1'
		]);
	}


	public static function validateLeaveSubmission($request)
	{
		$request->validate([
			'id_leave_reason'	=> 'required|exists:leave_reasons,id',
			'start_date'		=> 'required',
			'end_date'			=> 'required|after_or_equal:start_date',
		], [
			'start_date.after_or_equal'	=> 'Tanggal awal harus sama dengan '.date('Y-m-d').' atau lebih',
			'end_date.after_or_equal'	=> 'Tanggal akhir harus sama dengan tanggal awal atau lebih',
		]);
	}


	public static function validateOvertimeSubmission($request)
	{
		$request->validate([
			'id_overtime_reason'	=> 'required|exists:overtime_reasons,id',
			'start_date'			=> 'required',
			'end_date'				=> 'required|after_or_equal:start_date',
			'clock_start'			=> 'required',
			'clock_end'				=> 'required',
		], [
			'start_date.after_or_equal'	=> 'Tanggal awal harus sama dengan '.date('Y-m-d').' atau lebih',
			'end_date.after_or_equal'	=> 'Tanggal akhir harus sama dengan tanggal awal atau lebih',
		]);
	}

	public static function validateAttendanceSubmission($request)
	{
		$request->validate([
			'time'			=> 'required|after_or_equal:created_at',
		],
		[
			'time.after_or_equal'	=> 'Waktu harus sama dengan waktu sekarang atau lebih',
		]);
	}

	public static function validateAnnouncement($request)
	{
		$request->validate([
			'title'				=> 'required',
			'file_announcement'	=> 'nullable|mimes:png,jpg,jpeg,gif,pdf,xlsx,xls,doc,docx,mp4,mkv',
		], [
			'title.required' => 'Judul wajib diisi',
			'file_announcement.mimes'	=> 'Format file tidak didukung',
		]);

		if(empty($request->content) && empty($request->file_announcement)) {
			$request->validate([
				'file_announcement'	=> 'required|mimes:png,jpg,jpeg,gif,pdf,xlsx,xls,doc,docx,mp4,mkv',
				'content'	=> 'required',
			], [
				'content.required' => 'Salah satu antara konten dan file pengumuman harus terisi',
				'file_announcement.required' => 'Salah satu antara konten dan file pengumuman harus terisi',
				'file_announcement.mimes'	=> 'Format file tidak didukung',
			]);
		}
	}

	public static function validateSalarySlipMultiple($request)
	{
		$request->validate([
			'title'		=> 'required',
			'file.*'	=> 'required|mimes:pdf',
			'id_employee.*'	=> 'required|exists:employees,id',
			'total.*'	=> 'required|min:0',
			'year'		=> 'required',
			'month'		=> 'required',
		], [
			'title.required' => 'Judul wajib diisi',
			'file.*.mimes'	=> 'Hanya mendukung file .pdf',
			'total.*.required'	=> 'Total nominal wajib diisi',
			'total.*.min'	=> 'Minimal diisi 0',
			'id_employee.*.required'	=> 'Karyawan wajib diisi',
			'year.required' => 'Tahun wajib diisi',
			'month.required' => 'Bulan wajib diisi',
		]);
	}

	public static function validateCourse($request)
	{
		$criteria = [
			'course_title'		=> 'required',
			'video_source'		=> 'required|in:link,file',
			'is_published'		=> 'required|in:yes,no',
			'pass_requirement'	=> 'required|in:pass_video,pass_exam'
		];

		if($request->video_source == 'link') {
			$criteria['video_link'] = 'required|url';
		} elseif($request->video_source == 'file') {
			$criteria['file_video'] = 'required|file|mimes:mp4,mkv';
		}

		$request->validate($criteria, [
			'course_title.required' => 'Judul course wajib diisi',
			'video_source.required' => 'Sumber video wajib diisi',
			'video_source.in' => 'Sumber video tidak valid',
			'video_link.required' => 'Link video wajib diisi',
			'video_link.url' => 'Link video tidak valid',
			'file_video.required' => 'File video wajib diisi',
			'file_video.file' => 'File video wajib berupa file video',
			'file_video.mimes' => 'Hanya mendukung ekstensi .mp4 dan .mkv',
			'is_published.required' => 'Status publikasi wajib diisi',
			'is_published.in' => 'Status publikasi tidak valid',
			'pass_requirement.required' => 'Syarat kelulusan wajib diisi',
			'pass_requirement.in' => 'Syarat kelulusan tidak valid',
		]);
	}

	public static function validateTraining($request)
	{
		$criteria = [
			'title'	=> 'required',
			'trainer_name'	=> 'required',
			'is_published'	=> 'required|in:Ya,Tidak',
			'start_date'	=> 'required',
			'end_date'	=> 'required|after_or_equal:start_date'
		];

		// if($request->video_source == 'link') {
		// 	$criteria['video_link'] = 'required|url';
		// } elseif($request->video_source == 'file') {
		// 	$criteria['file_video'] = 'required|file|mimes:mp4,mkv';
		// }

		$request->validate($criteria, [
			'title.required' => 'Judul wajib diisi',
			'trainer_name.required' => 'Nama Trainer / Provider wajib diisi',
			'is_published.required' => 'Status publikasi wajib diisi',
			'is_published.in' => 'Status publikasi tidak valid',
			'start_date.required' => 'Tanggal awal pelaksanaan wajib diisi',
			'end_date.required' => 'Tanggal akhir pelaksanaan wajib diisi',
			'end_date.after_or_equal' => 'Tanggal akhir harus diatas atas sama dengan tanggal awal',
		]);
	}
}