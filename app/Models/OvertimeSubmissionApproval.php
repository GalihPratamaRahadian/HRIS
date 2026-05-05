<?php

namespace App\Models;

use App\MyClass\Helper;
use App\MyClass\WhatsappNew;
use Illuminate\Database\Eloquent\Model;

class OvertimeSubmissionApproval extends Model
{
	protected $fillable = [ 'id_overtime_submission', 'level', 'id_approver_position', 'id_user', 'status', 'approved_at', 'rejected_at' ];

	const STATUS_WAIT		= 'wait';
	const STATUS_APPROVED	= 'approved';
	const STATUS_REJECTED	= 'rejected';
	const STATUS_CANCELED	= 'canceled';
	const STATUS_SKIP		= 'skip';


	/**
	 * 	Relationships
	 * */
	public function overtimeSubmission()
	{
		return $this->belongsTo('App\Models\OvertimeSubmission', 'id_overtime_submission');
	}

	public function approverPosition()
	{
		return $this->belongsTo('App\Models\Position', 'id_approver_position');
	}

	public function approverPositionName()
	{
		return $this->approverPosition ? $this->approverPosition->position_name : '-';
	}

	public function employeeName()
	{
		try {
			return $this->overtimeSubmission->employee->employee_name;
		} catch (\Exception $e) {
			return '-';
		}
	}

	public function positionName()
	{
		try {
			return $this->overtimeSubmission->employee->position->position_name;
		} catch (\Exception $e) {
			return '-';
		}
	}

	public function departmentName()
	{
		try {
			return $this->overtimeSubmission->employee->department->department_name;
		} catch (\Exception $e) {
			return '-';
		}
	}

	public function overtimeReasonText()
	{
		try {
			return $this->overtimeSubmission->overtimeReasonText();
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
		return $this->status == self::STATUS_WAIT;
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

	public function isStatusCanceled()
	{
		return $this->status == self::STATUS_CANCELED;
	}

	public function isStatusSkip()
	{
		return $this->status == self::STATUS_SKIP;
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

	public function createdAtText($format = 'd M Y H:i')
	{
		return date($format, strtotime($this->created_at));
	}

	public static function dataTable($request)
	{
		$data = self::select([ 'overtime_submission_approvals.*' ])
					->has('overtimeSubmission')
					->with([ 'overtimeSubmission.employee.position', 'overtimeSubmission.overtimeReason', 'overtimeSubmission.employee.department' ])
					->leftJoin('overtime_submissions', 'overtime_submission_approvals.id_overtime_submission', '=', 'overtime_submissions.id')
					->leftJoin('employees', 'overtime_submissions.id_employee', '=', 'employees.id')
					->leftJoin('positions', 'employees.id_position', '=', 'positions.id')
					->leftJoin('departments', 'employees.id_department', '=', 'departments.id')
					->leftJoin('overtime_reasons', 'overtime_submissions.id_overtime_reason', '=', 'overtime_reasons.id');

		if(user()->isEmployee()) {
			$data = $data->where('overtime_submission_approvals.id_approver_position', employee()->id_position)
						 ->where(function($q1){
						 	$q1->where(function($q2){
						 		$q2->where('overtime_submission_approvals.level', 1)
						 		   ->where('overtime_submissions.approval_progress_level', 1);
						 	})->orWhere(function($q2){
						 		$q2->where('overtime_submission_approvals.level', 1)
						 		   ->where('overtime_submissions.approval_progress_level', 2);
						 	})->orWhere(function($q2){
						 		$q2->where('overtime_submission_approvals.level', 2)
						 		   ->where('overtime_submissions.approval_progress_level', 2);
						 	});
						 });
		} elseif (user()->isAdmin()) {
			$data = $data->where('overtime_submission_approvals.position_level', '0');
		}

		if(!empty($request->start_date)) {
			$data = $data->where('overtime_submissions.start_date', '>=', $request->start_date);
		}

		if(!empty($request->end_date)) {
			$data = $data->where('overtime_submissions.end_date', '<=', $request->end_date);
		}

		if(!empty($request->id_overtime_reason)) {
			if($request->id_overtime_reason != 'all') {
				$data = $data->where('overtime_submissions.id_overtime_reason', $request->id_overtime_reason);
			}
		}

		if(!empty($request->status)) {
			if($request->status != 'all') {
				$data = $data->where('overtime_submission_approvals.status', $request->status);
			}
		}

		if(!empty($request->id_employee)) {
			if($request->id_employee != 'all') {
				$data = $data->where('overtime_submissions.id_employee', $request->id_employee);
			}
		}

		if(!empty($request->id_department)) {
			if($request->id_department != 'all') {
				$data = $data->where('employees.id_department', $request->id_department);
			}
		}

		if(!empty($request->id_position)) {
			if($request->id_position != 'all') {
				$data = $data->where('employees.id_position', $request->id_position);
			}
		}

		if(!empty($request->id_employee_group)) {
			if($request->id_employee_group != 'all') {
				$data = $data->where('employees.id_employee_group', $request->id_employee_group);
			}
		}

		return \DataTables::eloquent($data)
			->editColumn('created_at', function($data){
				return $data->createdAtText();
			})
			->editColumn('overtime_submissions.employee.employee_name', function($data){
				$html = $data->employeeName();
				$html .= '<br><span class="text-success">['.$data->departmentName().']<span>';
				$html .= ' <span class="text-primary">['.$data->positionName().']<span>';
				return $html;
			})
			->editColumn('overtime_submissions.employee.department.department_name', function($data){
				return $data->departmentName();
			})
			->editColumn('overtime_submissions.employee.position.position_name', function($data){
				return $data->positionName();
			})
			->editColumn('overtime_submission.overtime_reason.reason', function($data){
				return $data->overtimeReasonText();
			})
			->editColumn('overtime_submissions.start_date', function($data){
				return $data->overtimeSubmission->startDateText().' '.date('H:i', strtotime($data->overtimeSubmission->clock_start));
			})
			->editColumn('overtime_submissions.end_date', function($data){
				return $data->overtimeSubmission->endDateText().' '.date('H:i', strtotime($data->overtimeSubmission->clock_end));
			})
			->editColumn('overtime_submissions.description', function($data){
				return $data->overtimeSubmission->descriptionHtml();
			})
			->editColumn('status', function($data){
				return $data->statusHtml();
			})
			->addColumn('action', function($data){
				// if (UserPermission::check('overtime_submission_approval', 'u')) {
					if ($data->isStatusWaiting()) {
						$button = '
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Aksi
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="'.route('employee.overtime_approval.detail', $data->id).'" title="Detail Pengajuan Lembur">
									<i class="mdi mdi-magnify"></i> Detail
								</a>
								<a class="dropdown-item approve" href= "javascript:void(0)" data-href="'.route('employee.overtime_approval.approve', $data->id).'" title="Setujui Pengajuan Lembur">
									<i class="mdi mdi-check"></i> Setuju
								</a>
								<a class="dropdown-item reject" href= "javascript:void(0)" data-href="'.route('employee.overtime_approval.reject', $data->id).'" title="Tolak Pengajuan Lembur">
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
									<a class="dropdown-item" href="'.route('employee.overtime_approval.detail', $data->id).'" title="Detail Pengajuan Lembur">
										<i class="mdi mdi-magnify"></i> Detail
									</a>
								</div>
							</div>';

							return $button;
					}
				// }
			})
			->rawColumns([ 'overtime_submissions.employee.employee_name', 'status', 'action' ])
			->make(true);
	}


	public function approve()
	{
		$this->update([
			'approved_at'	=> now(),
			'id_user'		=> user()->id,
			'status'		=> self::STATUS_APPROVED
		]);
		$this->overtimeSubmission->checkingApproval();

		return $this;
	}

	public function reject()
	{
		$this->update([
			'rejected_at'	=> now(),
			'id_user'		=> user()->id,
			'status'		=> self::STATUS_REJECTED
		]);
		$this->overtimeSubmission->checkingApproval();

		return $this;
	}

	public function sendNotification()
	{
		$this->load('overtimeSubmission.employee');
		$this->load('overtimeSubmission.overtimeReason');
		$message = "*HRIS System*";
		$message .= "\n\nKaryawan atas nama *".$this->overtimeSubmission->employeeName()."* telah mengajukan lembur, diharapkan untuk segera memproses penyetujuan/penolakan.";
		$message .= "\nKlik link berikut untuk lihat detail pengajuan ".route('employee.overtime_approval.detail', $this->id);
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
            'overtimeSubmission.employee',
            'overtimeSubmission.overtimeReason',
            'approverPosition.employees',
        ]);

        if (!$this->overtimeSubmission) {
            return \Res::invalid([
                'message' => 'Pengajuan cuti tidak ditemukan.',
            ]);
        }

        $employeeName = $this->overtimeSubmission->employeeName();
        $overtimeId = $this->overtimeSubmission->id;

        $message = "*HRIS System*";
        $message .= "\n\nKaryawan atas nama *{$employeeName}* telah mengajukan lembur, diharapkan untuk segera memproses penyetujuan/penolakan.";
        $message .= "\nKlik link berikut untuk lihat detail pengajuan: ";
        $message .= route('employee.overtime_approval.detail', ['overtimeSubmissionApproval' => $overtimeId]);


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
