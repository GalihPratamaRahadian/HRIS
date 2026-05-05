<?php

namespace App\Models;

use App\MyClass\Helper;
use App\MyClass\Whatsapp;
use App\MyClass\WhatsappNew;
use App\MyClass\XLSXWriter;
use App\User;
use Illuminate\Database\Eloquent\Model;

class SickNecessitySubmission extends Model
{
	protected $fillable = [ 'id_employee', 'type', 'id_sick_reason', 'id_necessity_reason', 'reason', 'start_date', 'end_date', 'description', 'file', 'approval_progress_level', 'status', 'approved_at', 'rejected_at', 'canceled_at', 'id_employee_sick_necessity' ];


	const STATUS_WAIT		= 'wait';
	const STATUS_APPROVED	= 'approved';
	const STATUS_REJECTED	= 'rejected';
	const STATUS_CANCELED	= 'canceled';

	const TYPE_SAKIT	= 'Sakit';
	const TYPE_IZIN		= 'Izin';


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

	public function sickReason()
	{
		return $this->belongsTo('App\Models\SickReason', 'id_sick_reason')->withTrashed();
	}

	public function sickReasonText()
	{
		return $this->sickReason ? $this->sickReason->reason : '-';
	}

	public function necessityReason()
	{
		return $this->belongsTo('App\Models\NecessityReason', 'id_necessity_reason')->withTrashed();
	}

	public function necessityReasonText()
	{
		return $this->necessityReason ? $this->necessityReason->reason : '-';
	}

	public function sickNecessitySubmissionApprovals()
	{
		return $this->hasMany('App\Models\SickNecessitySubmissionApproval', 'id_sick_necessity_submission');
	}

	public function sickNecessitySubmissionApprovalsStatusWait()
	{
		return $this->hasMany('App\Models\SickNecessitySubmissionApproval', 'id_sick_necessity_submission')
					->where('status', SickNecessitySubmissionApproval::STATUS_WAIT);
	}

	public function employeeSickNecessity()
	{
		return $this->belongsTo('App\Models\EmployeeSickNecessity', 'id_employee_sick_necessity');
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

	public function typeText()
	{
		if ($this->type == self::TYPE_SAKIT){
			return 'Sakit';
		}
		if ($this->type == self::TYPE_IZIN){
			return 'Izin';
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
		return storage_path('app/public/sick_necessity_submissions/'.$this->file);
	}

	public function fileLink()
	{
		return url('storage/sick_necessity_submissions/'.$this->file);
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

	public function reason()
	{
		return $this->type == self::TYPE_SAKIT ? $this->sickReasonText() : $this->necessityReasonText();
	}

	public function saveFile($request)
	{
		if(!empty($request->file_attachment))
		{
			$file = $request->file('file_attachment');
			$filename =date('Ymd_His')."_".rand(1000,9999)."_".$file->getClientOriginalName();
			$file->move(storage_path('app/public/sick_necessity_submissions'), $filename);

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
			'type' => $this->type,
			'reason' => $this->reason(),
			'sick_reason' => $this->sickReasonText(),
			'necessity_reason' => $this->necessityReasonText(),
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
		$data = self::select([ 'sick_necessity_submissions.*' ])
					->with([ 'sickReason', 'necessityReason', 'employee' ])
					->leftJoin('employees', 'sick_necessity_submissions.id_employee', '=', 'employees.id')
					->leftJoin('sick_reasons', 'sick_necessity_submissions.id_sick_reason', '=', 'sick_reasons.id')
					->leftJoin('necessity_reasons', 'sick_necessity_submissions.id_necessity_reason', '=', 'necessity_reasons.id');

		if(user()->isEmployee()) {
			$data = $data->where('id_employee', employee()->id);
		}

		if(!empty($request->status)) {
			if($request->status != 'all') {
				$data = $data->where('sick_necessity_submissions.status', $request->status);
			}
		}

		return \DataTables::eloquent($data)
			->editColumn('employee.employee_name', function($data){
				return $data->employeeName();
			})
			->editColumn('reason', function($data){
				return $data->reason();
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
						<a class="dropdown-item" href="'.route('employee.sick_necessity_submission.detail', $data->id).'" title="Detail Pengajuan Cuti">
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
									<a class="dropdown-item" href="'.route('admin.sick_necessity_submission.detail', $data->id).'" title="Detail Pengajuan Cuti">
										<i class="mdi mdi-magnify"></i> Detail
									</a>
									<a class="dropdown-item cancel href="javascript:void(0);" data-href="'.route('admin.sick_necessity_submission.cancel', $data->id).'" title="Batalkan Pengajuan Izin/Sakit">
										<i class="mdi mdi-close"></i> Batalkan Pengajuan
									</a>
								</div>
							</div>';

					return $button;
					} elseif($data->isStatusWaiting()){
						$button = '
							<div class="dropdown">
								<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Aksi
								</button>
								<div class="dropdown-menu">
									<a class="dropdown-item" href="'.route('admin.sick_necessity_submission.detail', $data->id).'" title="Detail Pengajuan Cuti">
										<i class="mdi mdi-magnify"></i> Detail
									</a>
									<a class="dropdown-item approve href="javascript:void(0);" data-href="'.route('admin.sick_necessity_submission.approve', $data->id).'" title="Setujui Pengajuan Izin/Sakit">
										<i class="mdi mdi-check"></i> Setuju
									</a>
									<a class="dropdown-item reject href="javascript:void(0);" data-href="'.route('admin.sick_necessity_submission.reject', $data->id).'" title="Tolak Pengajuan Izin/Sakit">
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

	public static function amountOfSickNecessitySubmissionsWithStatusPending()
	{
		return self::where('status', self::STATUS_WAIT)
				   ->count();
	}

	public static function fetchSickNecessitySubmissions($sickNecessitySubmissions)
	{
		$results = [];
		foreach($sickNecessitySubmissions as $ls) {
			$results[] = $ls->fetchData();
		}

		return $results;
	}

	/**
	 * 	Resume sick necessities
	 * */
	public  static function resumeGenerateDataForReport($request, $filename=null)
	{
		if (empty($filename)) $filename="Rekap Izin dan Sakit";
			$sickNecessity = self::select(['sick_necessity_submissions.*'])
							->with(['sickReason','necessityReason','employee.department', 'employee.position'])
							->leftJoin('employees', 'sick_necessity_submissions.id_employee', '=', 'employees.id')
							->leftJoin('necessity_reasons', 'sick_necessity_submissions.id_necessity_reason', '=', 'necessity_reasons.id')
							->leftJoin('sick_reasons', 'sick_necessity_submissions.id_sick_reason', '=', 'sick_reasons.id')
							->leftJoin('departments', 'employees.id_department', '=', 'departments.id')
							->leftJoin('positions', 'employees.id_position', '=', 'positions.id');

		if (user()->isEmployee()) {
			$sickNecessity = $sickNecessity->where('id_employee', employee()->id);
		}

		if(!empty($request->start_date)) {
			$sickNecessity = $sickNecessity->where('start_date', '>=', $request->start_date);
			$filename .= '_'.date('Ymd', strtotime($request->start_date));
		}

		if(!empty($request->end_date)) {
			$sickNecessity =$sickNecessity->where('end_date', '<=', $request->end_date);
			$filename .= '_'.date('Ymd', strtotime($request->end_date));
		}

		if (!empty($request->id_employee)) {
			if ($request->id_employee != 'all') {
				$sickNecessity = $sickNecessity->where('sick_necessity_submissions.id_employee', $request->id_employee);
			}
		}

		if(!empty($request->id_sick_reason)) {
			if($request->id_sick_reason != 'all') {
				$sickNecessity = $sickNecessity->where('sick_necessity_submissions.id_sick_reason', $request->id_sick_reason);
			}
		}

		if(!empty($request->id_necessity_reason)) {
			if($request->id_necessity_reason != 'all') {
				$sickNecessity = $sickNecessity->where('sick_necessity_submissions.id_necessity_reason', $request->id_necessity_reason);
			}
		}

		if (!empty($request->type)) {
			if ($request->type !='all') {
				$sickNecessity = $sickNecessity->where('sick_necessity_submissions.type', $request->type);
			}
		}

        if (!empty($request->status)) {
            if ($request->status !='all') {
                $sickNecessity = $sickNecessity->where('sick_necessity_submissions.status', $request->status);
            }
        }

		if (!empty($request->id_department)) {
			if ($request->id_department != 'all') {
				$sickNecessity = $sickNecessity->where('employees.id_department', $request->id_department);
			}
		}

		if (!empty($request->id_position)) {
			if ($request->id_position != 'all') {
				$sickNecessity = $sickNecessity->where('employees.id_position', $request->id_position);
			}
		}

		if (!empty($request->id_employee_group)) {
			if ($request->id_employee_group != 'all') {
				$sickNecessity = $sickNecessity->where('employees.id_employee_group', $request->id_employee_group);
			}
		}

		$sickNecessity = $sickNecessity->orderBy('sick_necessity_submissions.start_date', 'asc')
									   ->orderBy('employees.employee_name', 'asc')
									   ->get();
		return [
			'data'	=> $sickNecessity,
			'filename' => $filename,
			'startDate' => $request->start_date,
			'endDate' => $request->end_date
		];
	}

	public static function resumeGeneratePdfReport($request)
	{
		$data = self::resumeGenerateDataForReport($request);
		$sickNecessities = $data['data'];
		$startDate = $data['startDate'];
		$endDate = $data['endDate'];
		$filename = $data['filename'];

		$pdf = \PDF::loadView('admin.sick_necessity_resume.pdf', [
			'sickNecessities'	=> $sickNecessities,
			'startDate'	=> $startDate,
			'endDate'	=> $endDate,
		])->setPaper('A4', 'landscape');
		$filename .= '.pdf';

		return (object) [
			'pdf' => $pdf,
			'filename' => $filename
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
		$sickNecessities = $data['data'];
		$startDate = $data['startDate'];
		$endDate = $data['endDate'];
		$filename = $data['filename'].'.xlsx';

		$headerStyle = [ 'font-style'=>'bold', 'halign'=>'center', 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin' ];
		$bodyStyle = [ 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin' ];

		$writer = new \App\MyClass\XLSXWriter();

		$totalRow = 0;
		$totalColumn = 10;

		$writer->writeSheetHeader('Sheet1', [
			'Rekap Izin dan Sakit'	=> 'string',
		], [
			'widths'=> [5,20,20,25,25,25,20,25,25,20],
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
			'Tanggal Mulai',
			'Tanggal Selesai',
			'Karyawan',
			'Departemen',
			'Jabatan',
			'Jenis Pengajuan',
			'Alasan Izin',
			'Alasan Sakit',
			'Status Pengajuan',
		], $headerStyle);

		$iter = 1;

		foreach($sickNecessities as $sickNecessity) {
			$writer->writeSheetRow('Sheet1', [
				" $iter",
				$sickNecessity->startDateText(),
				$sickNecessity->endDateText(),
				$sickNecessity->employeeName(),
				$sickNecessity->departmentName(),
				$sickNecessity->positionName(),
				$sickNecessity->typeText(),
				$sickNecessity->necessityReasonText(),
				$sickNecessity->sickReasonText(),
				$sickNecessity->statusText(),
			], $bodyStyle);
			$iter++;
		}

		$writer->writeSheetRow('Sheet1', []);

		$sakit = $sickNecessities->where('type', 'Sakit')->count();
		$izin = $sickNecessities->where('type', 'Izin')->count();

		$writer->writeSheetRow('Sheet1', [
			'Total Pengajuan Sakit',
			'',
			'',
			$sakit,
		], $bodyStyle);

		$writer->writeSheetRow('Sheet1', [
			'Total Pengajuan Izin',
			'',
			'',
			$izin,
		], $bodyStyle);
		$writer->markMergedCell('Sheet1', $start_row=5+$iter, $start_col=0, $end_row=5+$iter, $end_col=2);
		$writer->markMergedCell('Sheet1', $start_row=6+$iter, $start_col=0, $end_row=6+$iter, $end_col=2);

		$path = \Helper::tempsPath($filename);
		$writer->writeToFile($path);

		return $path;
	}

	/**
	 * 	CRUD methods
	 * */
	public static function createSickNecessitySubmission($request)
	{
		\DB::beginTransaction();
		$sickNecessitySubmission = self::create([
			'id_employee'	=> auth()->user()->employee->id ?? $request->id_employee,
			'type' 			=> $request->type,
			'id_sick_reason' => $request->id_sick_reason,
			'id_necessity_reason' => $request->id_necessity_reason,
			'reason'		=> $request->reason,
			'start_date'	=> $request->start_date,
			'end_date'		=> $request->end_date,
			'description'	=> $request->description,
			'status'		=> self::STATUS_WAIT,
		]);
		$sickNecessitySubmission->createSickNecessitySubmissionApprovals();
		\DB::commit();
		$sickNecessitySubmission->sendNotificationToAdmin();

		$sickNecessitySubmission->saveFile($request);

		return $sickNecessitySubmission;
	}

	public function deleteSickNecessitySubmission()
	{
		foreach($this->sickNecessitySubmissionApprovals as $approval) {
			$approval->delete();
		}
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

		foreach($this->sickNecessitySubmissionApprovals as $approval)
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

		if($approved == count($this->sickNecessitySubmissionApprovals)) {
			$this->approve();
		} elseif($rejected > 0) {
			$this->reject();
			foreach($this->sickNecessitySubmissionApprovals as $approval) {
				if($approval->isStatusWaiting()) {
					$approval->cancel();
				}
			}
		} else {
			$this->update([
				'approval_progress_level'	=> ++$level,
			]);
			foreach($this->sickNecessitySubmissionApprovals as $approval) {
				if($approval->level == $level) {
					$approval->sendNotification();
				}
			}
		}

		return $this;
	}



	public function createAttendances()
	{
		$dates = \App\MyClass\Date::dateInRange($this->start_date, $this->end_date);
		foreach($dates as $date)
		{
			$attendance = Attendance::where('id_employee', $this->id_employee)
									->where('date', $date)
									->first();
			$create = false;
			if($attendance) {
				if($attendance->isTypeTanpaKeterangan()) {
					$attendance->deleteAttendance();
					$create = true;
				}
			} else {
				$create = true;
			}

			$type = $this->type == 'Sakit' ? Attendance::TYPE_SAKIT : Attendance::TYPE_IZIN;

			if($create) {
				Attendance::create([
					'id_employee'		=> $this->id_employee,
					'date'				=> $date,
					'clock_in_method'	=> Attendance::METHOD_SYSTEM,
					'clock_out_method'	=> Attendance::METHOD_SYSTEM,
					'type'				=> $type,
					'id_sick_necessity_submission' => $this->id,
				]);
			} else {
				if($attendance->isTypeSakit() || $attendance->isTypeIzin()) {
					$attendance->update([
						'id_sick_necessity_submission'	=> $this->id,
					]);
				}
			}
		}

		return $this;
	}

	public function removeAttendances()
	{
		$type = $this->type == 'Sakit' ? Attendance::TYPE_SAKIT : Attendance::TYPE_IZIN;

		Attendance::where('id_employee', $this->id_employee)
				  ->whereBetween('date', [ $this->start_date, $this->end_date ])
				  ->where('type', $type)
				  ->where('id_sick_necessity_submission', $this->id)
				  ->delete();
		return $this;
	}


	public function createSickNecessitySubmissionApprovals()
	{
		$this->load('employee');
		if($employee = $this->employee)
		{
			$position = $employee->position;
			$level = 1;

			if(!empty($position->approver_1)) {
				$approval = SickNecessitySubmissionApproval::create([
					'id_sick_necessity_submission'	=> $this->id,
					'level'					=> $level++,
					'id_approver_position'	=> $position->approver_1,
					'status'				=> 'wait'
				]);
				$approval->sendNotification();
			}

			if(!empty($position->approver_2)) {
				$approval = SickNecessitySubmissionApproval::create([
					'id_sick_necessity_submission'	=> $this->id,
					'level'					=> $level++,
					'id_approver_position'	=> $position->approver_2,
					'status'				=> 'wait'
				]);
			}
		}

		$this->load('sickNecessitySubmissionApprovals');

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

		$this->createAttendances();
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

		if($this->employeeSickNecessity) {
			$this->employeeSickNecessity->deleteEmployeeSickNecessity();
		}

		$this->sendCanceledNotification();
		return $this;
	}



	/**
	 * 	APPROVAL BY ADMIN
	 * */
	public function approveSickNecessitySubmissionByAdmin($notification = true)
	{
		foreach($this->sickNecessitySubmissionApprovals as $approval) {
			if($approval->isStatusWaiting()) {
				$approval->update([
					'status' => SickNecessitySubmissionApproval::STATUS_SKIP
				]);
			}
		}

		SickNecessitySubmissionApproval::create([
			'id_sick_necessity_submission'	=> $this->id,
			'level'					=> 0,
			'id_approver_position'	=> null,
			'status'				=> SickNecessitySubmissionApproval::STATUS_APPROVED,
			'id_user'				=> auth()->user()->id,
			'approved_at'			=> now(),
		]);

		$this->approve($notification);
		return $this;
	}

	public function rejectSickNecessitySubmissionByAdmin()
	{
		foreach($this->sickNecessitySubmissionApprovals as $approval) {
			if($approval->isStatusWaiting()) {
				$approval->update([
					'status' => SickNecessitySubmissionApproval::STATUS_SKIP
				]);
			}
		}

		SickNecessitySubmissionApproval::create([
			'id_sick_necessity_submission'	=> $this->id,
			'level'					=> 0,
			'id_approver_position'	=> null,
			'status'				=> SickNecessitySubmissionApproval::STATUS_REJECTED,
			'id_user'				=> auth()->user()->id,
			'rejected_at'			=> now(),
		]);

		$this->reject();
		return $this;
	}

	public function cancelSickNecessitySubmissionByAdmin()
	{
		foreach($this->sickNecessitySubmissionApprovals as $approval) {
			if($approval->isStatusWaiting()) {
				$approval->update([
					'status' => SickNecessitySubmissionApproval::STATUS_SKIP
				]);
			}
		}

		SickNecessitySubmissionApproval::create([
			'id_sick_necessity_submission'	=> $this->id,
			'level'					=> 0,
			'id_approver_position'	=> null,
			'status'				=> SickNecessitySubmissionApproval::STATUS_CANCELED,
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
		$message .= "\n\nKaryawan atas nama *".$this->employeeName()."* telah mengajukan {$this->reason}, diharapkan untuk segera memproses penyetujuan/penolakan.";
		$message .= "\nKlik link berikut untuk lihat detail pengajuan ".route('admin.sick_necessity_submission.detail', $this->id);
		$adminPhoneNumbers = explode(",", setting('admin_whatsapp_number', '6282316425264'));
		foreach($adminPhoneNumbers as $adminPhoneNumber) {
			 $EndPointWa = WhatsappNew::END_POINT_WA;
            if($EndPointWa == 'WA Baru'){
                // wa Baru
				$res = Helper::sendNotificationWhatsapp($phoneNumber = \App\MyClass\Helper::idPhoneNumberFormat($adminPhoneNumber), $message);
            }else{
                $res = Whatsapp::sendChat([
                    'to'    => \App\MyClass\Helper::idPhoneNumberFormat($adminPhoneNumber),
                    'text'  => $message,
                ]);
            }
		}

		return $this;
	}

	public function sendApprovedNotification()
	{
		if($this->employee) {
			$message = "Pengajuanmu ({$this->reason}) telah disetujui.";
			$message .= "\nCek detail melalui ".route('employee.sick_necessity_submission.detail', $this->id);
			$message .= "\n\n*Attendance System*";

			// \Whatsapp::sendChat([
			// 	'to'	=> $this->employee->phone_number,
			// 	'text'	=> $message,
			// ]);

			$EndPointWa = WhatsappNew::END_POINT_WA;
			if($EndPointWa == 'WA Baru'){
				// wa Baru
				$res = Helper::sendNotificationWhatsapp($phoneNumber = $this->employee->phone_number, $message);
			}else{
				$res = \App\MyClass\Whatsapp::sendChat([
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
			$message = "Mohon maaf, pengajuanmu ({$this->reason}) telah ditolak.";
			$message .= "\nCek detail melalui ".route('employee.sick_necessity_submission.detail', $this->id);
			$message .= "\n\n*Attendance System*";

			// \Whatsapp::sendChat([
			// 	'to'	=> $this->employee->phone_number,
			// 	'text'	=> $message,
			// ]);

			$EndPointWa = WhatsappNew::END_POINT_WA;
			if($EndPointWa == 'WA Baru'){
				// wa Baru
				$res = Helper::sendNotificationWhatsapp($phoneNumber = $this->employee->phone_number, $message);
			}else{
				$res = \App\MyClass\Whatsapp::sendChat([
					'to'	=> $this->employee->phone_number,
					'text'	=> $message,
				]);
			}
		}

		return $this;
	}

	public function sendCanceledNotification()
	{
		$employee = $this->employee;
		if($employee) {
			$message = "Mohon maaf, pengajuanmu ({$this->reason}) dibatalkan.";
			$message .= "\nCek detail melalui ".route('employee.sick_necessity_submission.detail', $this->id);
			$message .= "\n\n*Attendance System*";

			// \Whatsapp::sendChat([
			// 	'to'	=> $this->employee->phone_number,
			// 	'text'	=> $message,
			// ]);

			$EndPointWa = WhatsappNew::END_POINT_WA;
			if($EndPointWa == 'WA Baru'){
				// wa Baru
				$res = Helper::sendNotificationWhatsapp($phoneNumber = $employee->phone_number, $message);
			}else{
				$res = \App\MyClass\Whatsapp::sendChat([
					'to'	=> $employee->phone_number,
					'text'	=> $message,
				]);
			}
		}

		return $this;
	}
}
