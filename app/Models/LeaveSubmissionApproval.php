<?php

namespace App\Models;

use App\MyClass\Helper;
use App\MyClass\WhatsappNew;
use Illuminate\Database\Eloquent\Model;

class LeaveSubmissionApproval extends Model
{
	protected $fillable = [ 'id_leave_submission', 'level', 'id_approver_position', 'status', 'id_user', 'approved_at', 'rejected_at' ];


	const STATUS_WAIT		= 'wait';
	const STATUS_APPROVED	= 'approved';
	const STATUS_REJECTED	= 'rejected';
	const STATUS_CANCELED	= 'canceled';
	const STATUS_SKIP		= 'skip';


	/**
	 * 	Relationships
	 * */
	public function leaveSubmission()
	{
		return $this->belongsTo('App\Models\LeaveSubmission', 'id_leave_submission');
	}

	public function approverPosition()
	{
		return $this->belongsTo('App\Models\Position', 'id_approver_position');
	}

	public function user()
	{
		return $this->belongsTo('App\User', 'id_user');
	}



	/**
	 * 	Helper methods
	 * */
	public function isStatusWaiting()
	{
		return $this->status == self::STATUS_WAIT;
	}

	public function isStatusApproved()
	{
		return $this->status == self::STATUS_APPROVED;
	}

	public function isStatusRejected()
	{
		return $this->status == self::STATUS_REJECTED;
	}

	public function isStatusCanceled()
	{
		return $this->status == self::STATUS_CANCELED;
	}

	public function isStatusSkip()
	{
		return $this->status == self::STATUS_SKIP;
	}

	public function createdAtText($format = 'd M Y H:i')
	{
		return date($format, strtotime($this->created_at));
	}

	public function statusText()
	{
		if($this->isStatusWaiting()) return 'Menunggu';
		if($this->isStatusApproved()) return 'Disetujui';
		if($this->isStatusRejected()) return 'Ditolak';
		if($this->isStatusCanceled()) return 'Dibatalkan';
		if($this->isStatusSkip()) return 'Dilewat';
		return '-';
	}

	public function statusHtml()
	{
		$text = $this->statusText();
		if($this->isStatusWaiting()) return '<span class="text-primary">'.$text.'</span>';
		if($this->isStatusApproved()) return '<span class="text-success">'.$text.'</span>';
		if($this->isStatusRejected() || $this->isStatusCanceled()) return '<span class="text-danger">'.$text.'</span>';
		if($this->isStatusSkip()) return '<span class="text-primary">'.$text.'</span>';
		return '-';
	}

	public function approverPositionName()
	{
		return $this->approverPosition ? $this->approverPosition->position_name : '-';
	}

	public function approverDepartmentName()
	{
		return $this->approverPosition ? $this->approverPosition->departmentName() : '-';
	}

	public function employeeName()
	{
		try {
			return $this->leaveSubmission->employee->employee_name;
		} catch (\Exception $e) {
			return '-';
		}
	}

	public function positionName()
	{
		try {
			return $this->leaveSubmission->employee->position->position_name;
		} catch (\Exception $e) {
			return '-';
		}
	}

	public function departmentName()
	{
		try {
			return $this->leaveSubmission->employee->department->department_name;
		} catch (\Exception $e) {
			return '-';
		}
	}

	public function leaveReasonText()
	{
		try {
			return $this->leaveSubmission->leaveReasonText();
		} catch (\Exception $e) {
			return '-';
		}
	}

	public function userName()
	{
		return $this->user ? $this->user->name : '-';
	}


	/**
	 * 	Static methods
	 * */

	public static function dataTable($request)
	{
		$data = self::select([ 'leave_submission_approvals.*' ])
					->has('leaveSubmission')
					->with([ 'leaveSubmission.employee.position', 'leaveSubmission.leaveReason' ])
					->leftJoin('leave_submissions', 'leave_submission_approvals.id_leave_submission', '=', 'leave_submissions.id')
					->leftJoin('employees', 'leave_submissions.id_employee', '=', 'employees.id')
					->leftJoin('positions', 'employees.id_position', '=', 'positions.id')
					->leftJoin('leave_reasons', 'leave_submissions.id_leave_reason', '=', 'leave_reasons.id');

		if(user()->isEmployee()) {
			$data = $data->where('leave_submission_approvals.id_approver_position', employee()->id_position)
						 ->where(function($q1){
						 	$q1->where(function($q2){
						 		$q2->where('leave_submission_approvals.level', 1)
						 		   ->where('leave_submissions.approval_progress_level', 1);
						 	})->orWhere(function($q2){
						 		$q2->where('leave_submission_approvals.level', 2)
						 		   ->where('leave_submissions.approval_progress_level', 2);
						 	});
						 });
		} elseif (user()->isAdmin()) {
			$data = $data->where('leave_submission_approvals.position_level', '0');
		}

		return \DataTables::eloquent($data)
			->editColumn('created_at', function($data){
				return $data->createdAtText();
			})
			->editColumn('employee_name', function($data){
				$html = $data->employeeName();
				if($departmentName = $data->departmentName()) {
					$html .= "<br><span class='text-primary'>[". $departmentName ."]</span>";
				}

				return $html;
			})
			->editColumn('leave_submission.employee.position.position_name', function($data){
				return $data->positionName();
			})
			->editColumn('leave_submission.leave_reason.reason', function($data){
				return $data->leaveReasonText();
			})
			->editColumn('status', function($data){
				return $data->statusHtml();
			})
			->editColumn('leave_submission.status', function($data){
				return $data->leaveSubmission->statusHtml();

				return '-';
			})
			->editColumn('start_date', function($data){
				$leaveSub = $data->leaveSubmission;
				if($leaveSub->start_date == $leaveSub->end_date) {
					return $leaveSub->startDateText('d M Y');
				} else {
					return $leaveSub->startDateText('d M Y').' - <br>'.$leaveSub->endDateText('d M Y');
				}
			})
			->addColumn('action', function($data){
				if ($data->isStatusWaiting()) {
					$button = '
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Aksi
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="'.route('employee.leave_approval.detail', $data->id).'" title="Detail Pengajuan Cuti">
									<i class="mdi mdi-magnify"></i> Detail
								</a>
								<a class="dropdown-item approve" href="javascript:void(0)" data-href="'.route('employee.leave_approval.approve', $data->id).'" title="Setujui Pengajuan Cuti">
									<i class="mdi mdi-check"></i> Setujui
								</a>
								<a class="dropdown-item reject" href="javascript:void(0)" data-href="'.route('employee.leave_approval.reject', $data->id).'" title="Tolak Pengajuan Cuti">
									<i class="mdi mdi-close"></i> Tolak
								</a>
							</div>
						</div>';

					return $button;
				}elseif ($data->isStatusApproved()) {
					$button = '
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Aksi
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="'.route('employee.leave_approval.detail', $data->id).'" title="Detail Pengajuan Cuti">
									<i class="mdi mdi-magnify"></i> Detail
								</a>
							</div>
						</div>';

				return $button;
				}
			})
			->rawColumns([ 'status', 'employee_name', 'start_date', 'action', 'leave_submission.status' ])
			->make(true);
	}


	public function approve()
	{
		$this->update([
			'approved_at'	=> now(),
			'id_user'		=> user()->id,
			'status'		=> self::STATUS_APPROVED
		]);
		$this->leaveSubmission->checkingApproval();

		return $this;
	}

	public function reject()
	{
		$this->update([
			'rejected_at'	=> now(),
			'id_user'		=> user()->id,
			'status'		=> self::STATUS_REJECTED
		]);
		$this->leaveSubmission->checkingApproval();

		return $this;
	}

	public function cancel()
	{
		$this->update([
			'status'	=> self::STATUS_CANCELED
		]);

		return $this;
	}

	public function sendNotification()
	{
		$this->load('leaveSubmission.employee');
		$this->load('leaveSubmission.leaveReason');
		$message = "*HRIS System*";
		$message .= "\n\nKaryawan atas nama *".$this->leaveSubmission->employeeName()."* telah mengajukan cuti, diharapkan untuk segera memproses penyetujuan/penolakan.";
		$message .= "\nKlik link berikut untuk lihat detail pengajuan ".route('employee.leave_approval.detail', $this->id);
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
            'leaveSubmission.employee',
            'leaveSubmission.leaveReason',
            'approverPosition.employees',
        ]);

        if (!$this->leaveSubmission) {
            return \Res::invalid([
                'message' => 'Pengajuan cuti tidak ditemukan.',
            ]);
        }

        $employeeName = $this->leaveSubmission->employeeName();
        $leaveId = $this->leaveSubmission->id;

        $message = "*HRIS System*";
        $message .= "\n\nKaryawan atas nama *{$employeeName}* telah mengajukan cuti, diharapkan untuk segera memproses penyetujuan/penolakan.";
        $message .= "\nKlik link berikut untuk lihat detail pengajuan: ";
        $message .= route('employee.leave_approval.detail', ['leaveSubmissionApproval' => $leaveId]);


        foreach ($this->getEmployeePosition() as $employee) {
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
