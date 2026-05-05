<?php

namespace App\Models;

use App\MyClass\Helper;
use App\MyClass\WhatsappNew;
use Illuminate\Database\Eloquent\Model;

class AttendancePermissionSubmissionApproval extends Model
{
	protected $fillable = [ 'id_attendance_permission', 'id_approver_position', 'id_user', 'status', 'approved_at', 'rejected_at' ];

	const STATUS_WAITING	= 'wait';
	const STATUS_APPROVED	= 'approved';
	const STATUS_REJECTED	= 'rejected';
	const STATUS_SKIP		= 'skip';
	const STATUS_CANCELED	= 'canceled';


	/**
	 * 	Relationships
	 * */
	public function attendancePermissionSubmission()
    {
        return $this->belongsTo(AttendancePermissionSubmission::class, 'id_attendance_permission');
    }


	public function approverPosition()
    {
        return $this->belongsTo(Position::class, 'id_approver_position');
    }


	public function approverPositionName()
	{
		return $this->approverPosition ? $this->approverPosition->position_name : '-';
	}

	public function employeeName()
	{
		try {
			return $this->attendancePermission->employee->employee_name;
		} catch (\Exception $e) {
			return '-';
		}
	}

	public function positionName()
	{
		try {
			return $this->attendancePermission->employee->position->position_name;
		} catch (\Exception $e) {
			return '-';
		}
	}

	public function permissionReasonText()
	{
		try {
			return $this->attendancePermission->reason;
		} catch (\Exception $e) {
			return '-';
		}
	}

	public function user()
	{
		return $this->belongsTo('App\User', 'id_user');
	}

	public function userName()
	{
		return $this->user->name ?? '-';
	}


	/**
	 * 	Helper methods
	 * */
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

	public function isSkip()
	{
		return $this->status == self::STATUS_SKIP;
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

	public function isStatusSkip()
	{
		return $this->isSkip();
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

	public function createdAtText($format = 'd M Y H:i')
	{
		return date($format, strtotime($this->created_at));
	}

    public function getTypeAttendancePermission()
    {
        return $this->attendancePermissionSubmission->type ?? '-';
    }

	public static function dt($request)
	{
		$data = self::select([ 'attendance_permission_submission_approvals.*' ])
					->has('attendancePermissionSubmission')
					->with([ 'attendancePermissionSubmission.employee.position' ])
					->leftJoin('attendance_permission_submissions', 'attendance_permission_submission_approvals.id_attendance_permission', '=', 'attendance_permission_submissions.id')
					->leftJoin('employees', 'attendance_permission_submissions.id_employee', '=', 'employees.id')
					->leftJoin('positions', 'employees.id_position', '=', 'positions.id');

		if(user()->isEmployee()) {
			$data = $data->where('attendance_permission_submission_approvals.id_approver_position', employee()->id_position);
		} elseif (user()->isAdmin()) {
			$data = $data->where('attendance_permission_submission_approvals.position_level', '0');
		}

		return \DataTables::eloquent($data)
			->editColumn('created_at', function($data){
				return $data->createdAtText();
			})
			->editColumn('attendance_permission.employee.employee_name', function($data){
				return $data->employeeName();
			})
			->editColumn('attendance_permission.employee.position.position_name', function($data){
				return $data->positionName();
			})
			->editColumn('status', function($data){
				return $data->statusHtml();
			})
			->editColumn('attendance_permission_submission.date', function($data){
				return $data->attendancePermissionSubmission->dateTimeText('d M Y') ?? '-';
			})
			->editColumn('attendance_permission_submission.time', function($data){
				return $data->attendancePermissionSubmission->dateTimeText('H:i') ?? '-';
			})
			->addColumn('action', function($data){
				if ($data->isStatusApproved()) {
					$button = '
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Aksi
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="'.route('employee.attendance_permission_approval.detail', $data->id).'" title="Detail Izin Kehadiran">
									<i class="mdi mdi-magnify"></i> Detail
								</a>
							</div>
						</div>';

					return $button;
				}elseif ($data->isStatusWaiting()) {
					$button = '
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Aksi
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="'.route('employee.attendance_permission_approval.detail', $data->id).'" title="Detail Izin Kehadiran">
									<i class="mdi mdi-magnify"></i> Detail
								</a>
								<a class="dropdown-item approve" href="javascript:void(0);" data-href="'.route('employee.attendance_permission_approval.approve', $data->id).'" title="Setujui Izin Kehadiran">
									<i class="mdi mdi-check"></i> Setujui
								</a>
								<a class="dropdown-item reject" href="javascript:void(0);" data-href="'.route('employee.attendance_permission_approval.reject', $data->id).'" title="Tolak Izin Kehadiran">
									<i class="mdi mdi-close"></i> Tolak
								</a>
							</div>
						</div>';

					return $button;
				}
			})
			->rawColumns([ 'status', 'action' ])
			->make(true);
	}


	public function approve()
	{
		$this->update([
			'approved_at'	=> now(),
			'id_user'		=> user()->id,
			'status'		=> self::STATUS_APPROVED
		]);
		$this->attendancePermissionSubmission->checkingApproval();

		return $this;
	}

	public function reject()
	{
		$this->update([
			'rejected_at'	=> now(),
			'id_user'		=> user()->id,
			'status'		=> self::STATUS_REJECTED
		]);
		$this->attendancePermissionSubmission->checkingApproval();

		return $this;
	}

	public function sendNotification()
	{
		$this->load('attendancePermissionSubmission.employee');

		$message = "*HRIS System*";
		$message .= "\n\nKaryawan atas nama *".$this->attendancePermissionSubmission->employeeName()."* telah mengajukan ".strtolower($this->attendancePermissionSubmission->type).", diharapkan untuk segera memproses penyetujuan/penolakan.";
		$message .= "\nKlik link berikut untuk lihat detail pengajuan ".route('employee.attendance_permission_approval.detail', $this->id);
		foreach($this->approverPosition->employees as $employee) {
			$EndPointWa = WhatsappNew::END_POINT_WA;
				if($EndPointWa == 'WA Baru'){
					// wa Baru
					Helper::sendNotificationWhatsapp($phoneNumber = \App\MyClass\Helper::idPhoneNumberFormat($employee->phone_number), $message);
				}else{
					\App\MyClass\Whatsapp::sendChat([
						'to'	=> \App\MyClass\Helper::idPhoneNumberFormat($employee->phone_number),
						'text'	=> $message
					]);
				}
		}

		return $this;
	}

     public function getEmployeePosition()
    {
        return Employee::where('status', Employee::STATUS_ACTIVE)
            ->when(!empty($this->id_approver_position), function ($query) {
                $query->where('id_position', $this->id_approver_position);
            })->get();
    }


    public function resendBroadcastToApproval()
    {
        $this->loadMissing([
            'attendancePermissionSubmission.employee',
            'approverPosition.employees',
        ]);

        if (!$this->attendancePermissionSubmission) {
            return \Res::invalid([
                'message' => 'Pengajuan tidak ditemukan',
            ]);
        }

        $employeeName = $this->attendancePermissionSubmission->employeeName();
        $type = strtolower($this->attendancePermissionSubmission->type);
        $permissionId = $this->attendancePermissionSubmission->id;

        $message = '*HRIS System*';
        $message .= "\n\nKaryawan atas nama *{$employeeName}* telah mengajukan {$type}, diharapkan untuk segera memproses penyetujuan/penolakan.";
        $message .= "\nKlik link berikut untuk lihat detail pengajuan: ";
        $message .= route('employee.attendance_permission_approval.detail', ['attendancePermissionApproval' => $permissionId]);

        foreach ($this->approverPosition->employees as $employee) {
            if (!$employee->phone_number) {
                continue;
            }

            $phoneNumber = \App\MyClass\Helper::idPhoneNumberFormat($employee->phone_number);
            $endPointWa = \App\MyClass\WhatsappNew::END_POINT_WA;

            if ($endPointWa === 'WA Baru') {
                \App\MyClass\Helper::sendNotificationWhatsapp($phoneNumber, $message);
            } else {
                \App\MyClass\Whatsapp::sendChat([
                    'to'   => $phoneNumber,
                    'text' => $message,
                ]);
            }
        }

        return $this;
    }

}
