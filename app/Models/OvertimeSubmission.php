<?php

namespace App\Models;

use App\MyClass\Helper;
use App\MyClass\WhatsappNew;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OvertimeSubmission extends Model
{
	protected $fillable = [ 'id_employee', 'start_date', 'end_date', 'clock_start', 'clock_end', 'id_overtime_reason', 'description', 'approval_progress_level', 'status', 'approved_at', 'rejected_at' ];

	const STATUS_WAIT		= 'wait';
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

	public function overtimeReason()
	{
		return $this->belongsTo('App\Models\OvertimeReason', 'id_overtime_reason');
	}

	public function overtimeReasonText()
	{
		return $this->overtimeReason->reason ?? '-';
	}

	public function overtimeSubmissionApprovals()
	{
		return $this->hasMany('App\Models\OvertimeSubmissionApproval', 'id_overtime_submission');
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createOvertimeSubmission($request)
	{
		$idEmployee = user()->isEmployee() ? auth()->user()->employee->id : $request->id_employee;
		$submission = self::create([
			'id_employee'	=> $idEmployee,
			'start_date'	=> $request->start_date,
			'end_date'		=> $request->end_date,
			'clock_start'	=> $request->clock_start,
			'clock_end'		=> $request->clock_end,
			'id_overtime_reason' => $request->id_overtime_reason,
			'description'	=> $request->description,
			'status'		=> self::STATUS_WAIT,
		]);
		$submission->createOvertimeSubmissionApprovals();
		$submission->sendNotificationToAdmin();
		return $submission;
	}

	public function deleteOvertimeSubmission()
	{
		OvertimeSubmissionApproval::where('id_overtime_submission', $this->id)->delete();
		return $this->delete();
	}

	public function createOvertimeSubmissionApprovals()
	{
		$this->load('employee');
		if($employee = $this->employee)
		{
			$position = $employee->position;
			$level = 1;

			if($position)
			{
				if(!empty($position->approver_1)) {
					$approval = OvertimeSubmissionApproval::create([
						'id_overtime_submission'	=> $this->id,
						'id_approver_position'		=> $position->approver_1,
						'status'					=> 'wait',
						'level'						=> $level++,
					]);
					$approval->sendNotification();
				}

				if(!empty($position->approver_2)) {
					$approval = OvertimeSubmissionApproval::create([
						'id_overtime_submission'	=> $this->id,
						'id_approver_position'		=> $position->approver_2,
						'status'					=> 'wait',
						'level'						=> $level++,
					]);
				}
			}
		}
		$this->load('overtimeSubmissionApprovals');

		return $this;
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

	public function createdAtText($format = 'd M Y H:i')
	{
		return date($format, strtotime($this->created_at));
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

	public function employeeName()
	{
		return $this->employee->employee_name ?? '-';
	}

	public function departmentName()
	{
		if($this->employee) {
			if($this->employee->department) {
				return $this->employee->departmentName();
			}
		}
		return '-';
	}

	public function positionName()
	{
		if($this->employee) {
			if($this->employee->position) {
				return $this->employee->positionName();
			}
		}
		return '-';
	}

	public function descriptionHtml()
	{
		if($this->description) {
			return str_replace("\n", "<br>", $this->description);
		}
		return '-';
	}

    public function amountClockStartToEnd()
    {
        if ($this->clock_start && $this->clock_end) {
            $start = Carbon::parse($this->clock_start);
            $end = Carbon::parse($this->clock_end);

            // Tangani jika end < start, berarti lewat tengah malam
            if ($end->lessThan($start)) {
                $end->addDay();
            }

            $totalMinutes = $end->diffInMinutes($start);
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;

            $result = '';
            if ($hours > 0) {
                $result .= $hours;
            }
            if ($minutes > 0) {
                $result .= ($hours > 0 ? ' ' : '') . $minutes;
            }

            return $result ?: '0';
        }

        return '0';
    }

	public static function dataTable($request)
	{
		$data = self::select([ 'overtime_submissions.*' ])
					->with([ 'overtimeReason', 'employee.department', 'employee.position' ])
					->leftJoin('employees', 'overtime_submissions.id_employee', '=', 'employees.id')
					->leftJoin('departments', 'employees.id_department', '=', 'departments.id')
					->leftJoin('positions', 'employees.id_position', '=', 'positions.id')
					->leftJoin('overtime_reasons', 'overtime_submissions.id_overtime_reason', '=', 'overtime_reasons.id');

		if(user()->isEmployee()) {
			$data = $data->where('id_employee', employee()->id);
		}

		if(!empty($request->start_date)) {
			$data = $data->where('start_date', '>=', $request->start_date);
		}

		if(!empty($request->end_date)) {
			$data = $data->where('end_date', '<=', $request->end_date);
		}

		if(!empty($request->id_overtime_reason)) {
			if($request->id_overtime_reason != 'all') {
				$data = $data->where('overtime_submissions.id_overtime_reason', $request->id_overtime_reason);
			}
		}

		if(!empty($request->status)) {
			if($request->status != 'all') {
				$data = $data->where('overtime_submissions.status', $request->status);
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
			->editColumn('employees.employee_name', function($data){
				$html = $data->employeeName();
				$html .= '<br><span class="text-success">['.$data->departmentName().']<span>';
				$html .= ' <span class="text-primary">['.$data->positionName().']<span>';
				return $html;
			})
			->editColumn('departments.department_name', function($data){
				return $data->departmentName();
			})
			->editColumn('positions.position_name', function($data){
				return $data->positionName();
			})
			->editColumn('overtime_reason.reason', function($data){
				return $data->overtimeReasonText();
			})
			->editColumn('status', function($data){
				return $data->statusHtml();
			})
			->editColumn('created_at', function($data){
				return $data->createdAtText();
			})
			->editColumn('start_date', function($data){
				return $data->startDateText().' '.date('H:i', strtotime($data->clock_start));
			})
			->editColumn('end_date', function($data){
				return $data->endDateText().' '.date('H:i', strtotime($data->clock_end));
			})
			->editColumn('clock_start', function($data){
				return date('H:i', strtotime($data->clock_start));
			})
			->editColumn('clock_end', function($data){
				return date('H:i', strtotime($data->clock_end));
			})
			->editColumn('description', function($data){
				return $data->descriptionHtml();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('employee.overtime_submission.detail', $data->id).'" title="Detail Pengajuan Lembur">
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
									<a class="dropdown-item" href="'.route('admin.overtime_submission.detail', $data->id).'" title="Detail Pengajuan Lembur">
										<i class="mdi mdi-magnify"></i> Detail
									</a>
									<a class="dropdown-item cancel" href= "javascript:void(0)" data-href="'.route('admin.overtime_submission.cancel', $data->id).'" title="Batalkan Pengajuan Lembur">
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
									<a class="dropdown-item" href="'.route('admin.overtime_submission.detail', $data->id).'" title="Detail Pengajuan Lembur">
										<i class="mdi mdi-magnify"></i> Detail
									</a>
									<a class="dropdown-item approve" href= "javascript:void(0)" data-href="'.route('admin.overtime_submission.approve', $data->id).'" title="Setujui Pengajuan Lembur">
										<i class="mdi mdi-check"></i> Setuju
									</a>
									<a class="dropdown-item reject" href= "javascript:void(0)" data-href="'.route('admin.overtime_submission.reject', $data->id).'" title="Tolak Pengajuan Lembur">
										<i class="mdi mdi-close"></i> Tolak
									</a>
								</div>
							</div>';

						return $button;
					}

			})
			->rawColumns([ 'employees.employee_name', 'status', 'admin_action', 'action', 'description' ])
			->make(true);
	}

	public static function amountOfOvertimeSubmissionsWithStatusPending()
	{
		return self::where('status', self::STATUS_WAIT)
				   ->count();
	}



	/**
	 * 	APPROVAL BY ADMIN
	 * */
	public function approveOvertimeSubmissionByAdmin($notification = true)
	{
		foreach($this->overtimeSubmissionApprovals as $approval) {
			if($approval->isStatusWaiting()) {
				$approval->update([
					'status' => OvertimeSubmissionApproval::STATUS_SKIP
				]);
			}
		}

		OvertimeSubmissionApproval::create([
			'id_overtime_submission' => $this->id,
			'id_approver_position'	=> null,
			'id_user'				=> auth()->user()->id,
			'status'				=> OvertimeSubmissionApproval::STATUS_APPROVED,
			'approved_at'			=> now(),
		]);

		$this->approve($notification);
		return $this;
	}

	public function rejectOvertimeSubmissionByAdmin()
	{
		foreach($this->overtimeSubmissionApprovals as $approval) {
			if($approval->isStatusWaiting()) {
				$approval->update([
					'status' => OvertimeSubmissionApproval::STATUS_SKIP
				]);
			}
		}

		OvertimeSubmissionApproval::create([
			'id_overtime_submission' => $this->id,
			'id_approver_position'	=> null,
			'status'				=> OvertimeSubmissionApproval::STATUS_REJECTED,
			'id_user'				=> auth()->user()->id,
			'rejected_at'			=> now(),
		]);

		$this->reject();
		return $this;
	}

	public function cancelOvertimeSubmissionByAdmin()
	{
		foreach($this->overtimeSubmissionApprovals as $approval) {
			if($approval->isStatusWaiting()) {
				$approval->update([
					'status' => OvertimeSubmissionApproval::STATUS_SKIP
				]);
			}
		}

		OvertimeSubmissionApproval::create([
			'id_overtime_submission' => $this->id,
			'id_approver_position'	=> null,
			'status'				=> OvertimeSubmissionApproval::STATUS_CANCELED,
			'id_user'				=> auth()->user()->id,
			'rejected_at'			=> now(),
		]);

		$this->cancel();
		return $this;
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

		foreach($this->overtimeSubmissionApprovals as $approval)
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

		if($approved == count($this->overtimeSubmissionApprovals)) {
			$this->approve();
		} elseif($rejected > 0) {
			$this->reject();
			foreach($this->overtimeSubmissionApprovals as $approval) {
				if($approval->isStatusWaiting()) {
					$approval->cancel();
				}
			}
		} else {
			$this->update([
				'approval_progress_level'	=> ++$level,
			]);
			foreach($this->overtimeSubmissionApprovals as $approval) {
				if($approval->level == $level) {
					$approval->sendNotification();
				}
			}
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



	/**
	 * 	Notification
	 * */
	public function sendNotificationToAdmin()
	{
		$message = "*HRIS System*";
		$message .= "\n\nKaryawan atas nama *".$this->employeeName()."* telah mengajukan lembur, diharapkan untuk segera memproses penyetujuan/penolakan.";
		$message .= "\nKlik link berikut untuk lihat detail pengajuan ".route('admin.overtime_submission.detail', $this->id);
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
			$message = "Pengajuan lemburmu telah disetujui.";
			$message .= "\nCek detail melalui ".route('employee.overtime_submission.detail', $this->id);
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
			$message = "Mohon maaf, pengajuan lemburmu telah ditolak.";
			$message .= "\nCek detail melalui ".route('employee.overtime_submission.detail', $this->id);
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
			$message = "Mohon maaf, pengajuan lemburmu dibatalkan.";
			$message .= "\nCek detail melalui ".route('employee.overtime_submission.detail', $this->id);
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



	/**
	 * 	Resume Report
	 * */
	public static function resumeGenerateDataForReport($request, $filename = null)
	{
		if(empty($filename)) $filename = 'Rekap_Lembur';
		$overtime = self::select([ 'overtime_submissions.*' ])
					->with([ 'overtimeReason', 'employee.department', 'employee.position' ])
					->leftJoin('employees', 'overtime_submissions.id_employee', '=', 'employees.id')
					->leftJoin('departments', 'employees.id_department', '=', 'departments.id')
					->leftJoin('positions', 'employees.id_position', '=', 'positions.id')
					->leftJoin('overtime_reasons', 'overtime_submissions.id_overtime_reason', '=', 'overtime_reasons.id');

		if(user()->isEmployee()) {
			$overtime = $overtime->where('id_employee', employee()->id);
		}

		if(!empty($request->start_date)) {
			$overtime = $overtime->where('start_date', '>=', $request->start_date);
			$filename .= '_'.date('Ymd', strtotime($request->start_date));
		}

		if(!empty($request->end_date)) {
			$overtime = $overtime->where('end_date', '<=', $request->end_date);
			$filename .= '_'.date('Ymd', strtotime($request->end_date));
		}

		if(!empty($request->id_overtime_reason)) {
			if($request->id_overtime_reason != 'all') {
				$overtime = $overtime->where('overtime_submissions.id_overtime_reason', $request->id_overtime_reason);
			}
		}

		if(!empty($request->status)) {
			if($request->status != 'all') {
				$overtime = $overtime->where('overtime_submissions.status', $request->status);
			}
		}

		if(!empty($request->id_employee)) {
			if($request->id_employee != 'all') {
				$overtime = $overtime->where('overtime_submissions.id_employee', $request->id_employee);
			}
		}

		if(!empty($request->id_department)) {
			if($request->id_department != 'all') {
				$overtime = $overtime->where('employees.id_department', $request->id_department);
			}
		}

		if(!empty($request->id_position)) {
			if($request->id_position != 'all') {
				$overtime = $overtime->where('employees.id_position', $request->id_position);
			}
		}

		if(!empty($request->id_employee_group)) {
			if($request->id_employee_group != 'all') {
				$overtime = $overtime->where('employees.id_employee_group', $request->id_employee_group);
			}
		}

		$overtime = $overtime->orderBy('overtime_submissions.start_date', 'asc')
						 ->orderBy('overtime_submissions.clock_start', 'asc')
						 ->orderBy('employees.employee_name', 'asc')
						 ->get();

		return [
			'data'		=> $overtime,
			'startDate'	=> $request->start_date,
			'endDate'	=> $request->end_date,
			'filename'	=> $filename,
		];
	}


	public static function resumeGeneratePdfReport($request)
	{
		$data = self::resumeGenerateDataForReport($request);
		$overtimes = $data['data'];
		$startDate = $data['startDate'];
		$endDate = $data['endDate'];
		$filename = $data['filename'];

		$pdf = \PDF::loadView('admin.overtime_resume.pdf', [
			'overtimes'	=> $overtimes,
			'startDate'	=> $startDate,
			'endDate'	=> $endDate,
		])->setPaper('A4', 'landscape');
		$filename .= '.pdf';

		return (object) [
			'pdf'		=> $pdf,
			'filename'	=> $filename,
		];
	}


	public static function resumeStreamPdfReport($request)
	{
		$result = self::resumeGeneratePdfReport($request);

		return $result->pdf->stream($result->filename);
	}


	public static function resumeDownloadPdfReport($request)
	{
		$result = self::resumeGeneratePdfReport($request);

		return $result->pdf->download($result->filename);
	}


	public static function resumeDownloadExcelReport($request)
	{
		$data = self::resumeGenerateDataForReport($request);
		$overtimes = $data['data'];
		$startDate = $data['startDate'];
		$endDate = $data['endDate'];
		$filename = $data['filename'].'.xlsx';

		$headerStyle = [ 'font-style'=>'bold', 'halign'=>'center', 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin' ];
		$bodyStyle = [ 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin' ];

		$writer = new \App\MyClass\XLSXWriter();

		$totalRow = 0;
		$totalColumn = 8;

		$writer->writeSheetHeader('Sheet1', [
			'Rekap Lembur'	=> 'string',
		], [
			'widths'=> [5,20,20,15,25,25,25,25,25,25],
			'font-style'=>'bold', 'halign'=>'center', 'valign' => 'center', 'height'=> 5, 'wrap_text' => true
		]);
		$writer->markMergedCell('Sheet1', $start_row=0, $start_col=0, $end_row=0, $end_col=$totalColumn);
		$totalRow++;

		if(!empty($startDate) && !empty($endDate)) {
			$writer->writeSheetRow('Sheet1', []);
			$totalRow++;

			if($startDate == $endDate) {
				$periode = date('d-m-Y', strtotime($startDate));
			} else {
				$periode = date('d-m-Y', strtotime($startDate)).' s/d '.date('d-m-Y', strtotime($endDate));
			}

			$writer->writeSheetRow('Sheet1', [ 'Periode : '.$periode ], [
				'halign'=>'center', 'valign' => 'center',
			]);
			$writer->markMergedCell('Sheet1', $start_row=$totalRow, $start_col=0, $end_row=$totalRow, $end_col=$totalColumn);
			$totalRow++;
		}

		$writer->writeSheetRow('Sheet1', []);

		$writer->writeSheetRow('Sheet1', [
			'No',
			'Mulai Lembur',
			'Selesai Lembur',
            'Durasi Lembur',
			'Karyawan',
			'Departemen',
			'Jabatan',
			'Alasan',
			'Deskripsi',
			'Status Pengajuan',
		], $headerStyle);

		$iter = 1;

		foreach($overtimes as $ov) {
			$writer->writeSheetRow('Sheet1', [
				" $iter",
				$ov->startDateText().' '.date('H:i', strtotime($ov->clock_start)),
				$ov->endDateText().' '.date('H:i', strtotime($ov->clock_end)),
                $ov->amountClockStartToEnd(),
				$ov->employeeName(),
				$ov->departmentName() ?? '-',
				$ov->positionName(),
				$ov->overtimeReasonText(),
				$ov->descriptionHtml(),
				$ov->statusText(),
			], $bodyStyle);
			$iter++;
		}

		$writer->writeSheetRow('Sheet1', []);

		$writer->writeSheetRow('Sheet1', [
			'Total Pengajuan Lembur',
			'',
			'',
			count($overtimes),
		], $bodyStyle);
		$writer->markMergedCell('Sheet1', $start_row=5+$iter, $start_col=0, $end_row=5+$iter, $end_col=2);

		$path = \Helper::tempsPath($filename);
		$writer->writeToFile($path);

		return $path;
	}
}
