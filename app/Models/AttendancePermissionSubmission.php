<?php

namespace App\Models;

use App\MyClass\Helper;
use App\MyClass\WhatsappNew;
use Illuminate\Database\Eloquent\Model;

class AttendancePermissionSubmission extends Model
{
	protected $fillable = [ 'id_employee', 'type', 'date', 'time', 'reason', 'status', 'approved_at', 'rejected_at' ];

	const STATUS_WAITING	= 'wait';
	const STATUS_APPROVED	= 'approved';
	const STATUS_REJECTED	= 'rejected';
	const STATUS_CANCELED	= 'canceled';


	/**
	 * 	Relationship
	 * */
	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee');
	}

	public function employeeName()
	{
		return $this->employee ? $this->employee->employee_name : '-';
	}

	public function attendancePermissionSubmissionApprovals()
	{
		return $this->hasMany(AttendancePermissionSubmissionApproval::class, 'id_attendance_permission');
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createAttendancePermissionSubmission(array $request)
	{
		$submission = self::create($request);
		$submission->createAttendancePermissionSubmissionApprovals();
		$submission->sendNotificationToAdmin();
		return $submission;
	}

	public function createAttendancePermissionSubmissionApprovals()
	{
		$this->load('employee');
		if($employee = $this->employee)
		{
			$position = $employee->position;

			if($position)
			{
				if(!empty($position->approver_1)) {
					$approval = AttendancePermissionSubmissionApproval::create([
						'id_attendance_permission'	=> $this->id,
						'id_approver_position'		=> $position->approver_1,
						'status'					=> 'wait'
					]);
					$approval->sendNotification();

					// if ($approval->isStatusApproved()) {
					// 	$approval2 = AttendancePermissionSubmissionApproval::create([
					// 		'id_attendance_permission'	=> $this->id,
					// 		'id_approver_position'		=> $position->approver_2,
					// 		'status'					=> 'wait'
					// 	]);

					// 	$approval2->sendNotification();
					// }
				}

				if(!empty($position->approver_2)) {
					$approval = AttendancePermissionSubmissionApproval::create([
						'id_attendance_permission'	=> $this->id,
						'id_approver_position'		=> $position->approver_2,
						'status'					=> 'wait'
					]);
					$approval->sendNotification();
				}
			}
		}
		$this->load('attendancePermissionSubmissionApprovals');
		$this->checkingApproval();

		return $this;
	}

	public static function deleteAttendancePermissionSubmission()
	{
		foreach($this->attendancePermissionSubmissionApprovals as $app) {
			$app->delete();
		}
		return $this->delete();
	}


	/**
	 * 	Helper methods
	 * */

    public function typeHomeEarly()
    {
        return $this->type == 'Pulang Cepat';
    }

    public function typeLate()
    {
        return $this->type == 'Terlambat';
    }
	public function isApproved()
	{
		return $this->status == self::STATUS_APPROVED;
	}

	public function isRejected()
	{
		return $this->status == self::STATUS_REJECTED;
	}

	public function isWaiting()
	{
		return $this->status == self::STATUS_WAITING;
	}

	public function isStatusApproved()
	{
		return $this->isApproved();
	}

	public function isStatusRejected()
	{
		return $this->isRejected();
	}

	public function isStatusWaiting()
	{
		return $this->isWaiting();
	}

	public function statusText()
	{
		if($this->isStatusWaiting()) return 'Menunggu';
		if($this->isStatusApproved()) return 'Disetujui';
		if($this->isStatusRejected()) return 'Ditolak';
		return '-';
	}

	public function statusHtml()
	{
		$text = $this->statusText();
		if($this->isStatusWaiting()) return '<span class="text-primary">'.$text.'</span>';
		if($this->isStatusApproved()) return '<span class="text-success">'.$text.'</span>';
		if($this->isStatusRejected()) return '<span class="text-danger">'.$text.'</span>';
		return '-';
	}

    public function typeText()
    {
        if($this->typeHomeEarly()) return 'Pulang Cepat';
        if($this->typeLate()) return 'Terlambat';
        return '-';
    }

	public function createdAtText($format = 'd M Y H:i')
	{
		return date($format, strtotime($this->created_at));
	}

	public function dateTimeText($format = 'd M Y H:i')
	{
		$dateTime = $this->date.' '.$this->time;
		return date($format, strtotime($dateTime));
	}

	public static function dt($request)
	{
		$data = self::select([ 'attendance_permission_submissions.*' ])
					->with([ 'employee' ])
					->leftJoin('employees', 'attendance_permission_submissions.id_employee', '=', 'employees.id');

		if(user()->isEmployee()) {
			$data = $data->where('id_employee', employee()->id);
		}

		if(!empty($request->status)) {
			if($request->status != 'all') {
				$data = $data->where('attendance_permission_submissions.status', $request->status);
			}
		}

		return \DataTables::eloquent($data)
			->editColumn('employee.employee_name', function($data){
				return $data->employeeName();
			})
			->editColumn('status', function($data){
				return $data->statusHtml();
			})
			->editColumn('date', function($data){
				return $data->dateTimeText('d M Y');
			})
			->editColumn('time', function($data){
				return $data->dateTimeText('H:i');
			})

			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('employee.attendance_permission_submission.detail', $data->id).'" title="Detail Izin Terlambat/Pulang Cepat">
							<i class="mdi mdi-magnify"></i> Detail
						</a>
					</div>
				</div>';

				return $button;
			})
			->addColumn('admin_action', function($data){
					if ($data->isStatusApproved()) {
						$button = '
							<div class="dropdown">
								<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Aksi
								</button>
								<div class="dropdown-menu">
									<a class="dropdown-item" href="'.route('admin.attendance_permission_submission.detail', $data->id).'" title="Detail Pengajuan Izin Terlambat/Pulang Cepat">
										<i class="mdi mdi-magnify"></i> Detail
									</a>
									<a class="dropdown-item cancel" href="javascript:void(0)" data-href="'.route('admin.attendance_permission_submission.cancel', $data->id).'" title="Batalkan Pengajuan Izin Terlambat/Pulang Cepat">
										<i class="mdi mdi-close"></i> Batalkan
									</a>
								</div>
							</div>';

						return $button;
					} elseif ($data->isStatusWaiting()) {
						$button = '
							<div class="dropdown">
								<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Aksi
								</button>
								<div class="dropdown-menu">
									<a class="dropdown-item" href="'.route('admin.attendance_permission_submission.detail', $data->id).'" title="Detail Pengajuan Cuti">
										<i class="mdi mdi-magnify"></i> Detail
									</a>
									<a class="dropdown-item approve" href="javascript:void(0)" data-href="'.route('admin.attendance_permission_submission.approve', $data->id).'" title="Setuju Pengajuan Izin Terlambat/Pulang Cepat">
										<i class="mdi mdi-check"></i> Setuju
									</a>
									<a class="dropdown-item reject" href="javascript:void(0)" data-href="'.route('admin.attendance_permission_submission.reject', $data->id).'" title="Tolak Pengajuan Izin Terlambat/Pulang Cepat">
										<i class="mdi mdi-close"></i> Tolak
									</a>
								</div>
							</div>';

						return $button;
					}
			})
			->editColumn('created_at', function($data){
				return $data->createdAtText();
			})
			->rawColumns([ 'status', 'action', 'admin_action' ])
			->make(true);
	}

	public static function dataTable($request)
	{
		return self::dt($request);
	}

	public static function amountOfAttendancePermissionSubmissionsWithStatusPending()
	{
		return self::where('status', self::STATUS_WAITING)
				   ->count();
	}


	/**
	 * 	Approve & Reject
	 * */
	public function checkingApproval()
	{
		$wait = 0;
		$approved = 0;
		$rejected = 0;

		$this->load('attendancePermissionSubmissionApprovals');
		foreach($this->attendancePermissionSubmissionApprovals as $approval)
		{
			if($approval->isStatusWaiting()) $wait++;
			if($approval->isStatusApproved()) $approved++;
			if($approval->isStatusRejected()) $rejected++;
		}

		if($approved == count($this->attendancePermissionSubmissionApprovals)) {
			$this->approve();
		} elseif($rejected > 0 && $wait == 0) {
			$this->reject();
		}

		return $this;
	}

	public function approve($notification = true)
	{
		$this->update([
			'status'		=> self::STATUS_APPROVED,
			'approved_at'	=> now(),
		]);

		if($notification) {
			$this->sendApprovedNotification();
		}
		return $this;
	}

	public function reject()
	{
		$this->update([
			'status'		=> self::STATUS_REJECTED,
			'rejected_at'	=> now(),
		]);

		$this->sendRejectedNotification();
		return $this;
	}

	public function cancel()
	{
		$this->update([
			'status'		=> self::STATUS_CANCELED,
			'canceled_at'	=> now(),
		]);

		$this->sendCanceledNotification();
		return $this;
	}


	public function approveAttendancePermissionSubmissionByAdmin($notification = true)
	{
		foreach($this->attendancePermissionSubmissionApprovals as $approval) {
			if($approval->isStatusWaiting()) {
				$approval->update([
					'status' => AttendancePermissionSubmissionApproval::STATUS_SKIP
				]);
			}
		}

		AttendancePermissionSubmissionApproval::create([
			'id_attendance_permission'	=> $this->id,
			'id_approver_position'	=> null,
			'status'				=> AttendancePermissionSubmissionApproval::STATUS_APPROVED,
			'id_user'				=> auth()->user()->id,
			'approved_at'			=> now(),
		]);

		$this->approve($notification);
		return $this;
	}

	public function rejectLeaveSubmissionByAdmin()
	{
		foreach($this->attendancePermissionSubmissionApprovals as $approval) {
			if($approval->isStatusWaiting()) {
				$approval->update([
					'status' => AttendancePermissionSubmissionApproval::STATUS_SKIP
				]);
			}
		}

		AttendancePermissionSubmissionApproval::create([
			'id_attendance_permission'	=> $this->id,
			'id_approver_position'	=> null,
			'status'				=> AttendancePermissionSubmissionApproval::STATUS_REJECTED,
			'id_user'				=> auth()->user()->id,
			'rejected_at'			=> now(),
		]);

		$this->reject();
		return $this;
	}

	public function cancelLeaveSubmissionByAdmin()
	{
		foreach($this->attendancePermissionSubmissionApprovals as $approval) {
			if($approval->isStatusWaiting()) {
				$approval->update([
					'status' => AttendancePermissionSubmissionApproval::STATUS_SKIP
				]);
			}
		}

		AttendancePermissionSubmissionApproval::create([
			'id_attendance_permission'	=> $this->id,
			'id_approver_position'	=> null,
			'status'				=> AttendancePermissionSubmissionApproval::STATUS_CANCELED,
			'id_user'				=> auth()->user()->id,
			'rejected_at'			=> now(),
		]);

		$this->cancel();
		return $this;
	}



	/**
	 * 	APPROVAL NOTIFICATION
	 * */
	public function sendNotificationToAdmin()
	{
		$message = "*HRIS System*";
		$message .= "\n\nKaryawan atas nama *".$this->employeeName()."* telah mengajukan ".strtolower($this->type).", diharapkan untuk segera memproses penyetujuan/penolakan.";
		$message .= "\nKlik link berikut untuk lihat detail pengajuan ".route('admin.attendance_permission_submission.detail', $this->id);
		$adminPhoneNumbers = explode(",", setting('admin_whatsapp_number', '6282316425264'));
		foreach($adminPhoneNumbers as $adminPhoneNumber) {
			$EndPointWa = WhatsappNew::END_POINT_WA;
				if($EndPointWa == 'WA Baru'){
					// wa Baru
					Helper::sendNotificationWhatsapp($phoneNumber = \App\MyClass\Helper::idPhoneNumberFormat($adminPhoneNumber), $message);
				}else{
					\App\MyClass\Whatsapp::sendChat([
						'to'	=> \App\MyClass\Helper::idPhoneNumberFormat($adminPhoneNumber),
						'text'	=> $message
					]);
				}
		}

		return $this;
	}

	public function sendApprovedNotification()
	{
		if($this->employee) {
			$message = "Pengajuan ".strtolower($this->type)." telah disetujui.";
			$message .= "\nCek detail melalui ".route('employee.attendance_permission_submission.detail', $this->id);
			$message .= "\n\n*Attendance System*";

			$EndPointWa = WhatsappNew::END_POINT_WA;
			if($EndPointWa == 'WA Baru'){
			// wa Baru
				Helper::sendNotificationWhatsapp($phoneNumber = $this->employee->phone_number, $message);
			}else{
			\Whatsapp::sendChat([
				'to'	=> $this->employee->phone_number,
				'text'	=> $message,
			]);
			}
		}

		return $this;
	}

	public function sendRejectedNotification()
	{
		if($this->employee) {
			$message = "Mohon maaf, pengajuan ".strtolower($this->type)." telah ditolak.";
			$message .= "\nCek detail melalui ".route('employee.attendance_permission_submission.detail', $this->id);
			$message .= "\n\n*Attendance System*";

			$EndPointWa = WhatsappNew::END_POINT_WA;
			if($EndPointWa == 'WA Baru'){
			// wa Baru
				Helper::sendNotificationWhatsapp($phoneNumber = $this->employee->phone_number, $message);
			}else{
			\Whatsapp::sendChat([
				'to'	=> $this->employee->phone_number,
				'text'	=> $message,
			]);
			}
		}

		return $this;
	}

	public function sendCanceledNotification()
	{
		if($this->employee) {
			$message = "Mohon maaf, pengajuan ".strtolower($this->type)." telah dibatalkan.";
			$message .= "\nCek detail melalui ".route('employee.leave_submission.detail', $this->id);
			$message .= "\n\n*Attendance System*";

			$EndPointWa = WhatsappNew::END_POINT_WA;
			if($EndPointWa == 'WA Baru'){
			// wa Baru
				Helper::sendNotificationWhatsapp($phoneNumber = $this->employee->phone_number, $message);
			}else{
				\Whatsapp::sendChat([
					'to'	=> $this->employee->phone_number,
					'text'	=> $message,
				]);
			}
		}

		return $this;
	}
}
