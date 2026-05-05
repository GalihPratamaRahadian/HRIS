<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmployeeLeave extends Model
{
	protected $fillable = [ 'id_employee', 'id_leave_reason', 'reason', 'start_date', 'end_date', 'description', 'file', 'meta' ];

	const REASON_LEAVE		= 1;
	const REASON_SICK		= 2;
	const REASON_NECESSITY	= 3;


	/**
	 * 	Relationship
	 * */
	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee');
	}

	public function leaveReason()
	{
		return $this->belongsTo('App\Models\LeaveReason', 'id_leave_reason');
	}

    public function leaveSubmission()
    {
        return $this->hasOne('App\Models\LeaveSubmission', 'id_employee_leave');
    }


	public function employeeName()
	{
		return $this->employee->employee_name ?? '-';
	}



	/**
	 * 	CRUD methods
	 * */
	public static function createEmployeeLeave($request)
	{
		$employeeLeave = self::create([
			'id_employee'	=> $request->id_employee,
			'reason'		=> $request->reason,
			'start_date'	=> $request->start_date,
			'end_date'		=> $request->end_date,
			'description'	=> $request->description,
		]);

		$employeeLeave->createAttendances();
		$employeeLeave->saveFile($request);
		$employeeLeave->useLeaveQuota();

		return $employeeLeave;
	}

	public function updateEmployeeLeave($request)
	{
		if(($this->start_date != $request->start_date) || ($this->end_date != $request->end_date)) {
			$this->removeAttendances();
			$this->rollbackLeaveQuota();
		}

		$this->update([
			'id_employee'	=> $request->id_employee,
			'reason'		=> $request->reason,
			'start_date'	=> $request->start_date,
			'end_date'		=> $request->end_date,
			'description'	=> $request->description,
		]);
		$this->saveFile($request);

		return $this;
	}

	public function deleteEmployeeLeave()
	{
		$this->removeAttendances();
		$this->rollbackLeaveQuota();
		$this->deleteFile();

		return $this->delete();
	}

	public function saveFile($request)
	{
		if(!empty($request->file))
		{
			$this->deleteFile();

			$file = $request->file('file');
			$filename = "{$this->id}_{$this->id_employee}_".date('Ymd_His').".{$file->getClientOriginalExtension()}";
			$file->move(storage_path('app/public/employee_leave'), $filename);

			$this->update([
				'file'	=> $filename
			]);
		}

		return $this;
	}

	public function deleteFile()
	{
		if($this->isHasFile()) {
			\File::delete($this->filePath());

			$this->update([
				'file'	=> null,
			]);
		}

		return $this;
	}

    public function getStatusFromLeaveSubmission()
    {
        return $this->leaveSubmission ? $this->leaveSubmission->status : '-';
    }



	/**
	 * 	Trigger CRUD
	 * */
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

			if($create) {
				Attendance::create([
					'id_employee'		=> $this->id_employee,
					'date'				=> $date,
					'clock_in_method'	=> Attendance::METHOD_SYSTEM,
					'clock_out_method'	=> Attendance::METHOD_SYSTEM,
					'type'				=> Attendance::TYPE_CUTI,
					'id_employee_leave'	=> $this->id,
				]);
			} else {
				if($attendance->isTypeCuti()) {
					$attendance->update([
						'id_employee_leave'	=> $this->id,
					]);
				}
			}
		}

		return $this;
	}

	public function removeAttendances()
	{
		Attendance::where('id_employee', $this->id_employee)
				  ->where('date', '>=', $this->start_date)
				  ->where('date', '<=', $this->end_date)
				  ->where('type', Attendance::TYPE_CUTI)
				  ->delete();
		return $this;
	}

	public function useLeaveQuota()
	{
		if($this->employee) {
			$this->employee->useLeaveQuota($this->amountOfDay());
		}

		return $this;
	}

	public function rollbackLeaveQuota()
	{
		if($this->employee) {
			$this->employee->useLeaveQuota(-$this->amountOfDay());
		}

		return $this;
	}



	/**
	 * 	Helper methods
	 * */
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

	public function amountOfDay()
	{
		$start = new Carbon($this->start_date);
		$end = new Carbon($this->end_date);

		return $start->diffInDays($end) + 1;
	}

	public function descriptionText()
	{
		return $this->description ?? '-';
	}

	public function fileIsImage()
	{
		if(!$this->isHasFile()) return false;
		$ext = \File::extension($this->filePath());
		$imageExts = \GlobalData::imageExtensions();

		return in_array($ext, $imageExts);
	}

	public function isHasFile()
	{
		if(!empty($this->file)) {
			if(\File::exists($this->filePath())) {
				return true;
			}
		}

		return false;
	}

	public function filePath()
	{
		return storage_path('app/public/employee_leave/'.$this->file);
	}

	public function fileLink()
	{
		return url('storage/employee_leave/'.$this->file);
	}

	public function reasonText()
	{
		return $this->reason;
	}

	public function reasonToAttendanceType()
	{
		$type = $this->reason == self::REASON_LEAVE ? Attendance::TYPE_CUTI : null;
		$type = $this->reason == self::REASON_SICK ? Attendance::TYPE_SAKIT : $type;
		$type = $this->reason == self::REASON_NECESSITY ? Attendance::TYPE_IZIN : $type;

		return $type;
	}



	/**
	 * 	Static methods
	 * */
	public static function dt()
	{
		$data = self::select([ 'employee_leaves.*' ])
					->has('employee')
					->with('employee')
					->leftJoin('employees', 'employee_leaves.id_employee', '=', 'employees.id');

		return \DataTables::eloquent($data)
			->addColumn('employee.employee_name', function($data){
				return '<a href="'. route('employee.detail', $data->id_employee) .'">'. $data->employeeName() .'</a>';
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('admin.employee_leave.detail', $data->id).'" title="Detail Sakit/Izin/Cuti">
							<i class="mdi mdi-magnify"></i> Detail
						</a>';

				if(UserPermission::check('employee_leave', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('admin.employee_leave.edit', $data->id).'" title="Edit Sakit/Izin/Cuti">
							<i class="mdi mdi-pencil"></i> Edit
						</a>';
				}

				if(UserPermission::check('employee_leave', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('admin.employee_leave.destroy', $data->id).'" title="Hapus Sakit/Izin/Cuti">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				$button .= '
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'employee.employee_name', 'action' ])
			->make(true);
	}

	/**
	 * 	Resume Report
	 * */
	public static function resumeGenerateDataForReport($request, $filename = null)
	{
		$leaves = LeaveSubmission::with(['employee.department','employee.position','employee.employeeGroup'])
                                ->where('start_date', '<=', $request->end_date)
                                ->where('end_date', '>=', $request->start_date);


        if (!empty($request->id_department) && $request->id_department != 'all') {
            $leaves->whereHas('employee', function ($query) use ($request) {
                $query->where('id_department', $request->id_department);
            });
        }

        if (!empty($request->id_position) && $request->id_position != 'all') {
            $leaves->whereHas('employee', function ($query) use ($request) {
                $query->where('id_position', $request->id_position);
            });
        }

        if (!empty($request->id_employee_group) && $request->id_employee_group != 'all') {
            $leaves->whereHas('employee', function ($query) use ($request) {
                $query->where('id_employee_group', $request->id_employee_group);
            });
        }

        if (!empty($request->id_employee) && $request->id_employee != 'all') {
            $leaves->where('id_employee', $request->id_employee);
        }

        if (!empty($request->status) && $request->status != 'all') {
            $leaves->where('status', $request->status);
        }

        $leaves = $leaves->orderBy('start_date', 'asc')
                         ->orderBy(Employee::select('employee_name')
                         ->whereColumn('employees.id', 'leave_submissions.id_employee')
                         ->limit(1))
                         ->get();

        if (empty($filename)) $filename = 'Rekap_Cuti';

        $filename .= '_'.date('Ymd', strtotime($request->start_date)).'_'.date('Ymd', strtotime($request->end_date));

        return [
            'data'      => $leaves,
            'startDate' => $request->start_date,
            'endDate'   => $request->end_date,
            'filename'  => $filename,
        ];
	}


	public static function resumeGeneratePdfReport($request)
	{
		$data = self::resumeGenerateDataForReport($request);
		$leaves = $data['data'];
		$startDate = $data['startDate'];
		$endDate = $data['endDate'];
		$filename = $data['filename'];

		$pdf = \PDF::loadView('admin.leave_resume.pdf', [
			'leaves'	=> $leaves,
			'startDate'	=> $startDate,
			'endDate'	=> $endDate,
		])->setPaper('A4', 'portrait');
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
        $leaves = $data['data'];
        $startDate = $data['startDate'];
        $endDate = $data['endDate'];
        $filename = $data['filename'] . '.xlsx';

        $headerStyle = [
            'font-style' => 'bold',
            'halign' => 'center',
            'border' => 'left,right,top,bottom',
            'border-color' => '#000',
            'border-style' => 'thin'
        ];
        $bodyStyle = [
            'border' => 'left,right,top,bottom',
            'border-color' => '#000',
            'border-style' => 'thin'
        ];

        $writer = new \App\MyClass\XLSXWriter();

        $totalRow = 0;
        $totalColumn = 5;

        $writer->writeSheetHeader('Sheet1', [
            'Rekap Cuti' => 'string',
        ], [
            'widths' => [5, 20, 30, 25, 20, 40],
            'font-style' => 'bold',
            'halign' => 'center',
            'valign' => 'center',
            'height' => 5,
            'wrap_text' => true
        ]);
        $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = $totalColumn);
        $totalRow++;

        if (!empty($startDate) && !empty($endDate)) {
            $writer->writeSheetRow('Sheet1', []);
            $totalRow++;

            if ($startDate == $endDate) {
                $periode = date('d-m-Y', strtotime($startDate));
            } else {
                $periode = date('d-m-Y', strtotime($startDate)) . ' s/d ' . date('d-m-Y', strtotime($endDate));
            }

            $writer->writeSheetRow('Sheet1', ['Periode : ' . $periode], [
                'halign' => 'center',
                'valign' => 'center',
            ]);
            $writer->markMergedCell('Sheet1', $start_row = $totalRow, $start_col = 0, $end_row = $totalRow, $end_col = $totalColumn);
            $totalRow++;
        }

        $writer->writeSheetRow('Sheet1', []);

        $writer->writeSheetRow('Sheet1', [
            'No',
            'Mulai Cuti',
            'Berakhir Cuti',
            'Karyawan',
            'Departemen',
            'Status Pengajuan',
            'Alasan',
        ], $headerStyle);

        $iter = 1;

        foreach ($leaves as $leave) {
            $writer->writeSheetRow('Sheet1', [
                " $iter",
                $leave->startDateText(),
                $leave->endDateText(),
                $leave->employee->employee_name ?? '-',
                $leave->employee->department->department_name ?? '-',
                $leave->statusText() ?? '-',
                $leave->leaveReason->reason ?? '-',
            ], $bodyStyle);

            $iter++;
        }

        $writer->writeSheetRow('Sheet1', []);

        $writer->writeSheetRow('Sheet1', [
            'Total Cuti',
            count($leaves),
        ], $bodyStyle);
        $writer->markMergedCell('Sheet1', $start_row = 4 + $iter, $start_col = 0, $end_row = 4 + $iter, $end_col = 3);

        $path = \Helper::tempsPath($filename);
        $writer->writeToFile($path);

        return $path;
    }


}
