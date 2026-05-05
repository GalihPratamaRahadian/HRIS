<?php

namespace App\Models;

use App\MyClass\Helper;
use App\MyClass\WhatsappNew;
use Illuminate\Database\Eloquent\Model;

class LeaveSubmission extends Model
{
	protected $fillable = [ 'id_employee', 'id_leave_reason', 'start_date', 'end_date', 'description', 'file', 'approval_progress_level', 'status', 'approved_at', 'rejected_at', 'canceled_at', 'id_employee_leave' ];

	const STATUS_WAIT		= 'wait';
	const STATUS_APPROVED	= 'approved';
	const STATUS_REJECTED	= 'rejected';
	const STATUS_CANCELED	= 'canceled';


	/**
	 * 	Relationship
	 * */
	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee')->withTrashed();
	}

	public function employeeName()
	{
		return $this->employee ? $this->employee->employee_name : '-';
	}

	public function leaveReason()
	{
		return $this->belongsTo('App\Models\LeaveReason', 'id_leave_reason')->withTrashed();
	}

	public function leaveReasonText()
	{
		return $this->leaveReason ? $this->leaveReason->reason : '-';
	}

	public function leaveSubmissionApprovals()
	{
		return $this->hasMany('App\Models\LeaveSubmissionApproval', 'id_leave_submission');
	}

	public function leaveSubmissionApprovalsStatusWait()
	{
		return $this->hasMany('App\Models\LeaveSubmissionApproval', 'id_leave_submission')
					->where('status', LeaveSubmissionApproval::STATUS_WAIT);
	}

	public function employeeLeave()
	{
		return $this->belongsTo('App\Models\EmployeeLeave', 'id_employee_leave');
	}


	/**
	 *	Helper methods
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

	public function startDateText($format = 'd M Y')
	{
		return date($format, strtotime($this->start_date));
	}

	public function endDateText($format = 'd M Y')
	{
		return date($format, strtotime($this->end_date));
	}

	public function intervalDateText()
	{
		if($this->start_date == $this->end_date) {
			return $this->startDateText();
		} else {
			return "{$this->startDateText()} - {$this->endDateText()}";
		}
	}

	public function duration()
	{
		return \App\MyClass\Date::diffInDays($this->start_date, $this->end_date) + 1;
	}

	public function descriptionText()
	{
		return $this->description ?? '-';
	}

	public function filePath()
	{
		return storage_path('app/public/leave_submissions/'.$this->file);
	}

	public function fileLink()
	{
		return url('storage/leave_submissions/'.$this->file);
	}

	public function isHasFile()
	{
		if(empty($this->file)) return false;
		return \File::exists($this->filePath());
	}

	public function fileIsImage()
	{
		if(!$this->isHasFile()) return false;
		$ext = \File::extension($this->filePath());
		$imageExts = \GlobalData::imageExtensions();
		return in_array($ext, $imageExts);
	}

	public function createdAtTextFull()
	{
		return \Date::fullDateWithDayName($this->created_at);
	}

	public function createdAtText($format = 'd M Y H:i')
	{
		return date($format, strtotime($this->created_at));
	}

	public function approvedAtText($format = 'd M Y H:i')
	{
		if(empty($this->approved_at)) return '-';
		return date($format, strtotime($this->approved_at));
	}

	public function rejectedAtText($format = 'd M Y H:i')
	{
		if(empty($this->rejected_at)) return '-';
		return date($format, strtotime($this->rejected_at));
	}

	public function canceledAtText($format = 'd M Y H:i')
	{
		if(empty($this->canceled_at)) return '-';
		return date($format, strtotime($this->canceled_at));
	}

	public function statusText()
	{
		if($this->isStatusWaiting()) return 'Menunggu';
		if($this->isStatusApproved()) return 'Disetujui';
		if($this->isStatusRejected()) return 'Ditolak';
		if($this->isStatusCanceled()) return 'Dibatalkan';
		return '-';
	}

	public function statusHtml()
	{
		$text = $this->statusText();
		if($this->isStatusWaiting()) return '<span class="text-primary">'.$text.'</span>';
		if($this->isStatusApproved()) return '<span class="text-success">'.$text.'</span>';
		if($this->isStatusRejected()) return '<span class="text-danger">'.$text.'</span>';
		if($this->isStatusCanceled()) return '<span class="text-danger">'.$text.'</span>';
		return '-';
	}

	public function saveFile($request)
	{
		if(!empty($request->file_attachment))
		{
			$file = $request->file('file_attachment');
			$filename = $file->getClientOriginalName();
			$file->move(storage_path('app/public/leave_submissions'), $filename);

			$this->update([
				'file'	=> $filename
			]);
		}

		return $this;
	}

	public function fetchData()
	{
		return (object) [
			'id'		=> $this->id,
			'employee_name' => $this->employeeName(),
			'leave_reason' => $this->leaveReasonText(),
			'start_date_formatted' => $this->startDateText(),
			'end_date_formatted' => $this->endDateText(),
			'is_has_file' => $this->isHasFile(),
			'file_link'	=> $this->isHasFile() ? $this->fileLink() : null,
			'status' => $this->statusText(),
			'description' => $this->descriptionText(),
			'approved_at_formatted' => $this->approvedAtText(),
			'rejected_at_formatted' => $this->rejectedAtText(),
			'canceled_at_formatted' => $this->canceledAtText(),
		];
	}



	/**
	 * 	Static methods
	 * */
	public static function dataTable($request)
	{
		$data = self::select([ 'leave_submissions.*' ])
					->with([ 'leaveReason', 'employee' ])
					->leftJoin('employees', 'leave_submissions.id_employee', '=', 'employees.id')
					->leftJoin('leave_reasons', 'leave_submissions.id_leave_reason', '=', 'leave_reasons.id');

		if(user()->isEmployee()) {
			$data = $data->where('id_employee', employee()->id);
		}

		if(!empty($request->status)) {
			if($request->status != 'all') {
				$data = $data->where('leave_submissions.status', $request->status);
			}
		}

		return \DataTables::eloquent($data)
			->editColumn('employee.employee_name', function($data){
				return $data->employeeName();
			})
			->editColumn('leave_reason.reason', function($data){
				return $data->leaveReasonText();
			})
			->editColumn('status', function($data){
				return $data->statusHtml();
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
						<a class="dropdown-item" href="'.route('employee.leave_submission.detail', $data->id).'" title="Detail Pengajuan Cuti">
							<i class="mdi mdi-magnify"></i> Detail
						</a>
					</div>
				</div>';

				return $button;
			})
			->addColumn('admin_action', function($data){
					if ($data->isStatusApproved()){
						$button = '
					<div class="dropdown">
						<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Aksi
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="'.route('admin.leave_submission.detail', $data->id).'" title="Detail Pengajuan Cuti">
								<i class="mdi mdi-magnify"></i> Detail
							</a>
							<a class="dropdown-item cancel" href="javascript:void(0);" data-href="'.route('admin.leave_submission.cancel', $data->id).'">
								<i class="mdi mdi-close"></i> Batalkan Pengajuan
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
							<a class="dropdown-item" href="'.route('admin.leave_submission.detail', $data->id).'" title="Detail Pengajuan Cuti">
								<i class="mdi mdi-magnify"></i> Detail
							</a>
							<a class="dropdown-item approve" href="javascript:void(0);" data-href="'.route('admin.leave_submission.approve', $data->id).'" title="Menyetujui Pengajuan Cuti">
								<i class="mdi mdi-check"></i> Setuju
							</a>
							<a class="dropdown-item reject" href="javascript:void(0);" data-href="'.route('admin.leave_submission.reject', $data->id).'" title="Menolak Pengajuan Cuti">
								<i class="mdi mdi-close"></i> Tolak
							</a>
						</div>
					</div>';

				return $button;
					}
			})
			->rawColumns([ 'status', 'action', 'admin_action' ])
			->make(true);
	}

	public static function amountOfLeaveSubmissionsWithStatusPending()
	{
		return self::where('status', self::STATUS_WAIT)
				   ->count();
	}

	public static function fetchLeaveSubmissions($leaveSubmissions)
	{
		$results = [];
		foreach($leaveSubmissions as $ls) {
			$results[] = $ls->fetchData();
		}

		return $results;
	}

    public function getTargetEmployees()
    {
        $employees = [];
        if($this->target == 'all') {
            foreach(Employee::getActiveEmployees() as $employee) {
                $employees[] = $employee;
            }
        } else {
            foreach($this->leaveSubmissionDetails as $detail) {
                if($employee = $detail->employee) {
                    $employees[] = $employee;
                }
            }
        }

        return $employees;
    }

    public function targetText()
	{
		if($this->target == 'all') {
			return 'Semua Karyawan';
		} else {
			return 'Karyawan Yg Dipilih';
		}
	}




	/**
	 * 	CRUD methods
	 * */
	public static function createLeaveSubmission($request)
	{
		\DB::beginTransaction();
		$idEmployee = user()->isEmployee() ? auth()->user()->employee->id : $request->id_employee;
		$leaveSubmission = self::create([
			'id_employee'	=> $idEmployee,
			'id_leave_reason' => $request->id_leave_reason,
			'leave_reason'	=> $request->leave_reason,
			'start_date'	=> $request->start_date,
			'end_date'		=> $request->end_date,
			'description'	=> $request->description,
			'status'		=> self::STATUS_WAIT,
		]);
		$leaveSubmission->createLeaveSubmissionApprovals();
		$leaveSubmission->sendNotificationToAdmin();
		$leaveSubmission->takeLeaveQuota();
		\DB::commit();

		$leaveSubmission->saveFile($request);

		return $leaveSubmission;
	}

	public function deleteLeaveSubmission()
	{
		return $this->delete();
	}



	/**
	 * 	Approve & Reject
	 * */
	public function checkingApproval()
	{
		$wait = 0;
		$approved = 0;
		$rejected = 0;
		$level = 0;

		foreach($this->leaveSubmissionApprovals as $approval)
		{
			if($approval->isStatusWaiting()) $wait++;
			if($approval->isStatusApproved()) {
				$approved++;
				if($level < $approval->level) {
					$level = $approval->level;
				}
			}
			if($approval->isStatusRejected()) $rejected++;
		}

		if($approved == count($this->leaveSubmissionApprovals)) {
			$this->approve();
		} elseif($rejected > 0) {
			$this->reject();
			foreach($this->leaveSubmissionApprovals as $approval) {
				if($approval->isStatusWaiting()) {
					$approval->cancel();
				}
			}
		} else {
			$this->update([
				'approval_progress_level'	=> ++$level,
			]);
			foreach($this->leaveSubmissionApprovals as $approval) {
				if($approval->level == $level) {
					$approval->sendNotification();
				}
			}
		}

		return $this;
	}



	public function createEmployeeLeave()
	{
		$employeeLeave = EmployeeLeave::create([
			'id_employee'	=> $this->id_employee,
			'id_leave_reason' => $this->id_leave_reason,
			'reason'		=> $this->leaveReasonText(),
			'start_date'	=> $this->start_date,
			'end_date'		=> $this->end_date,
			'description'	=> $this->description,
			'file'			=> $this->file,
		]);

		$employeeLeave->createAttendances();
		if($this->isHasFile()) {
			\File::copy($this->filePath(), storage_path('app/public/employee_leave/'.$this->file));
		}

		$this->update([
			'id_employee_leave'	=> $employeeLeave->id,
		]);
		return $this;
	}

	public function createLeaveSubmissionApprovals()
	{
		$this->load('employee');
		if($employee = $this->employee)
		{
			$position = $employee->position;
			$level = 1;

			if($position) {
				if(!empty($position->approver_1)) {
					$approval = LeaveSubmissionApproval::create([
						'id_leave_submission'	=> $this->id,
						'level'					=> $level++,
						'id_approver_position'	=> $position->approver_1,
						'status'				=> 'wait'
					]);
					$approval->sendNotification();
				}

				if(!empty($position->approver_2)) {
					$approval = LeaveSubmissionApproval::create([
						'id_leave_submission'	=> $this->id,
						'level'					=> $level++,
						'id_approver_position'	=> $position->approver_2,
						'status'				=> 'wait'
					]);
				}
			}
		}

		$this->load('leaveSubmissionApprovals');

		return $this;
	}

	public function takeLeaveQuota()
	{
		if($leaveReason = $this->leaveReason) {
			if($leaveReason->isCutLeaveQuota()) {
				if($employee = $this->employee) {
					$employee->useLeaveQuota($this->duration());
				}
			}
		}

		return $this;
	}

	public function rollbackLeaveQuota()
	{
		if($leaveReason = $this->leaveReason) {
			if($leaveReason->isCutLeaveQuota()) {
				if($employee = $this->employee) {
					$employee->useLeaveQuota(- $this->duration());
				}
			}
		}

		return $this;
	}



	/**
	 * 	APPROVAL
	 * */
	public function approve($notification = true)
	{
		$this->update([
			'status'		=> self::STATUS_APPROVED,
			'approved_at'	=> now(),
		]);

		$this->createEmployeeLeave();
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

		$this->rollbackLeaveQuota();
		$this->sendRejectedNotification();
		return $this;
	}

	public function cancel()
	{
		$this->update([
			'status'		=> self::STATUS_CANCELED,
			'canceled_at'	=> now(),
		]);

		if($this->employeeLeave) {
			$this->employeeLeave->deleteEmployeeLeave();
		}

		$this->rollbackLeaveQuota();
		$this->sendCanceledNotification();
		return $this;
	}



	/**
	 * 	APPROVAL BY ADMIN
	 * */
	public function approveLeaveSubmissionByAdmin($notification = true)
	{
		foreach($this->leaveSubmissionApprovals as $approval) {
			if($approval->isStatusWaiting()) {
				$approval->update([
					'status' => LeaveSubmissionApproval::STATUS_SKIP
				]);
			}
		}

		LeaveSubmissionApproval::create([
			'id_leave_submission'	=> $this->id,
			'level'					=> 0,
			'id_approver_position'	=> null,
			'status'				=> LeaveSubmissionApproval::STATUS_APPROVED,
			'id_user'				=> auth()->user()->id,
			'approved_at'			=> now(),
		]);

		$this->approve($notification);
		return $this;
	}

	public function rejectLeaveSubmissionByAdmin()
	{
		foreach($this->leaveSubmissionApprovals as $approval) {
			if($approval->isStatusWaiting()) {
				$approval->update([
					'status' => LeaveSubmissionApproval::STATUS_SKIP
				]);
			}
		}

		LeaveSubmissionApproval::create([
			'id_leave_submission'	=> $this->id,
			'level'					=> 0,
			'id_approver_position'	=> null,
			'status'				=> LeaveSubmissionApproval::STATUS_REJECTED,
			'id_user'				=> auth()->user()->id,
			'rejected_at'			=> now(),
		]);

		$this->reject();
		return $this;
	}

	public function cancelLeaveSubmissionByAdmin()
	{
		foreach($this->leaveSubmissionApprovals as $approval) {
			if($approval->isStatusWaiting()) {
				$approval->update([
					'status' => LeaveSubmissionApproval::STATUS_SKIP
				]);
			}
		}

		LeaveSubmissionApproval::create([
			'id_leave_submission'	=> $this->id,
			'level'					=> 0,
			'id_approver_position'	=> null,
			'status'				=> LeaveSubmissionApproval::STATUS_CANCELED,
			'id_user'				=> auth()->user()->id,
			'canceled_at'			=> now(),
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
		$message .= "\n\nKaryawan atas nama *".$this->employeeName()."* telah mengajukan cuti, diharapkan untuk segera memproses penyetujuan/penolakan.";
		$message .= "\nKlik link berikut untuk lihat detail pengajuan ".route('admin.leave_submission.detail', $this->id);
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
			$message = "Pengajuan cuti mu ({$this->leaveReasonText()}) telah disetujui.";
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

	public function sendRejectedNotification()
	{
		if($this->employee) {
			$message = "Mohon maaf, pengajuan cuti mu ({$this->leaveReasonText()}) telah ditolak.";
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

	public function sendCanceledNotification()
	{
		if($this->employee) {
			$message = "Mohon maaf, pengajuan cuti mu ({$this->leaveReasonText()}) dibatalkan.";
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
