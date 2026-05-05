<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeaveQuota extends Model
{
	protected $fillable = [ 'id_employee', 'period_type', 'quota', 'quota_available', 'quota_used', 'mass_leave_cut', 'is_allow_accumulation', 'reset_at' ];

	const PERIOD_TYPE_MONTHLY	= 'monthly';
	const PERIOD_TYPE_YEARLY	= 'yearly';

	const NOT_ALLOWED	= 'no';
	const ALLOWED 		= 'yes';


	public static function availablePeriodTypes()
	{
		return [
			self::PERIOD_TYPE_MONTHLY	=> 'Bulanan',
			self::PERIOD_TYPE_YEARLY	=> 'Tahunan',
		];
	}

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


	public function isAllowForAccumulation()
	{
		return $this->is_allow_accumulation == self::ALLOWED;
	}


	// public function isReadyForReset()
	// {
	// 	return $this->reset_at <= now();
	// }

    public function isReadyForReset()
    {
        $now = now();
        $resetAt = $this->reset_at;

        \Log::debug("Cek isReadyForReset untuk ID {$this->id_employee}, reset_at: $resetAt, now: $now");

        return !$resetAt || $now->greaterThanOrEqualTo($resetAt);
    }


	public function isMonthly()
	{
		return $this->period_type == self::PERIOD_TYPE_MONTHLY;
	}


	public function isYearly()
	{
		return $this->period_type == self::PERIOD_TYPE_YEARLY;
	}

    public static function resetQuotaAvailable()
    {
        try {
            $now = now();

            // Ambil semua record yang waktunya sudah harus reset
            $records = self::with('employee')->where('reset_at', '<=', $now)->get();

            \Log::info("Jumlah record untuk reset: {$records->count()}");

            foreach ($records as $record) {
                $employee = $record->employee;

                // Cek jika employee atau tanggal mulai kerja tidak valid
                if (!$employee || !$employee->start_working_date) {
                    \Log::warning("Employee/start_working_date kosong untuk quota ID: {$record->id}");
                    continue;
                }

                // Ambil tanggal mulai kerja
                $startWorking = \Carbon\Carbon::parse($employee->start_working_date);
                $nextReset = $startWorking->copy()->year($now->year);

                // Jika tahun ini sudah lewat tanggal reset-nya, pakai tahun depan
                if ($nextReset->lessThanOrEqualTo($now)) {
                    $nextReset->addYear();
                }

                \Log::info("Resetting quota for employee ID: {$record->id_employee}");

                $record->quota = 12;
                $record->quota_available = 12;
                $record->quota_used = 0;
                $record->mass_leave_cut = 0;
                $record->reset_at = $nextReset;

                $record->save();

                \Log::info("Sukses reset quota ID {$record->id} ke 12, next reset at {$nextReset->format('Y-m-d')}");
            }

        } catch (\Throwable $e) {
            \Log::error("Error saat reset kuota: " . $e->getMessage());
        }
    }

	// public function getNextResetAt()
	// {
	// 	return $this->isMonthly() ? today()->addMonths(1)->setDay(1) : today()->addYears(1)->setMonth(1)->setDay(1);
	// }

    public function getNextResetAt()
    {
        if ($this->isMonthly()) {
            return today()->addMonth()->startOfMonth();
        }

        $startDate = $this->employee->start_working_date;

        if (!$startDate) {
            return today()->addYear()->startOfYear();
        }

        $startDate = \Carbon\Carbon::parse($startDate)->setYear(now()->year);

        if ($startDate->lessThanOrEqualTo(now())) {
            $startDate->addYear();
        }

        return $startDate;
    }


	public function setNextResetAt()
    {
        $nextReset = $this->getNextResetAt();

        // Jika quota masih null atau tidak valid, set default 12
        $baseQuota = $this->quota > 0 ? $this->quota : 12;

        $this->update([
            'reset_at'        => $nextReset,
            'quota'           => $baseQuota,
            'quota_available' => $baseQuota, // agar awal quota tersedia langsung 12
        ]);

        return $this;
    }



	public function resetQuotaUsed()
	{
		$this->update([
			'quota_used'	=> 0,
		]);

		return $this;
	}


	// public function reset()
	// {
	// 	if(!$this->isReadyForReset()) return $this;

	// 	if($this->isAllowForAccumulation())
	// 	{
	// 		$this->update([
	// 			'quota_available'	=> (int) $this->quota_available + (int) $this->quota,
	// 		]);
	// 	}
	// 	else
	// 	{
	// 		$this->update([
	// 			'quota_available'	=> (int) $this->quota,
	// 		]);
	// 	}

	// 	$this->setNextResetAt();
	// 	$this->resetQuotaUsed();

	// 	return $this;
	// }

    public function reset()
    {
        if (!$this->isReadyForReset()) {
            \Log::info("Reset dibatalkan karena belum waktunya untuk employee ID: {$this->id_employee}, reset_at: {$this->reset_at}");
            return $this;
        }

        // Ambil quota dasar yang sudah disimpan (hindari hardcode)
        $baseQuota = $this->quota;

        if (!$baseQuota || $baseQuota <= 0) {
            \Log::warning("Reset gagal: jatah cuti dasar tidak valid untuk employee ID: {$this->id_employee}");
            return $this;
        }

        // // Hitung quota yang tersedia baru
        // $newQuotaAvailable = $this->isAllowForAccumulation()
        //     ? (int) $this->quota_available + $baseQuota
        //     : $baseQuota;

        $newQuotaAvailable = 12;
        $quota = 12;
        $nextResetAt = $this->getNextResetAt();

        $this->update([
            'quota'           => $quota,
            'quota_available' => $newQuotaAvailable,
            'quota_used'      => 0,
            'mass_leave_cut'  => 0,
            'reset_at'        => $nextResetAt,
        ]);

        \Log::info("Sukses reset jatah cuti untuk karyawan ID: {$this->id_employee}, tersedia: $newQuotaAvailable, reset_at berikutnya: $nextResetAt");

        return $this;
    }


	public static function createEmployeeLeaveQuota($request)
	{
		$quota = self::create([
			'id_employee'			=> $request->id_employee,
			'period_type'			=> $request->period_type,
			'quota'					=> $request->quota,
			'quota_available'		=> $request->quota_available,
			'mass_leave_cut'		=> $request->mass_leave_cut ?? 0,
			'quota_used'			=> 0,
			'is_allow_accumulation'	=> $request->is_allow_accumulation,
		]);

		$quota->setNextResetAt();

		return $quota;
	}


	public function updateEmployeeLeaveQuota($request)
	{
		$this->update([
			'id_employee'			=> $request->id_employee,
			'period_type'			=> $request->period_type,
			'quota'					=> $request->quota,
			'quota_available'		=> $request->quota_available,
			'mass_leave_cut'		=> $request->mass_leave_cut ?? 0,
			'is_allow_accumulation'	=> $request->is_allow_accumulation,
		]);

		return $this;
	}


	public function deleteEmployeeLeaveQuota()
	{
		return $this->delete();
	}


	public static function resetEmployeeLeaveQuotas()
	{
		$quotas = self::where('reset_at', '<=', now())->get();

		foreach($quotas as $quota)
		{
			if($quota->isReadyForReset()) {
				$quota->reset();
			}
		}

		return count($quotas);
	}


	public function isHasAvailableQuota()
	{
		return $this->quota_available > 0;
	}


	public function useQuota()
	{
		$this->update([
			'quota_available'	=> (int) $this->quota_available - 1
		]);

		return $this;
	}


	public function cancelUseQuota()
	{
		$this->update([
			'quota_available'	=> (int) $this->quota_available + 1
		]);

		return $this;
	}


	public function periodTypeText()
	{
		return self::availablePeriodTypes()[$this->period_type];
	}


	public function isAllowAccumulationHtml()
	{
		return $this->isAllowForAccumulation() ? '<span class="text-success"> Ya </span>' : '<span class="text-danger"> Tidak </span>';
	}


	public function resetQuota()
	{
		$this->update([
			'quota_available'	=> $this->quota,
			'quota_used'		=> 0,
		]);

		return $this;
	}


	public function countQuotaBalance()
	{
		$startDateForAccumulation = null;
		$employee = $this->employee;

		if($this->isMonthly()) {
			if(!empty($employee->start_working_date)) {
				$startDate = date('Y-m-01');
				$endDate = date('Y-m-t');
				$startDateForAccumulation = new \Carbon\Carbon($employee->start_working_date);
			} else {
				$startDate = date('Y-m-01');
				$endDate = date('Y-m-t');
				$startDateForAccumulation = new \Carbon\Carbon($startDate);
			}
		} else {
			if(!empty($employee->start_working_date)) {
				$startWorkingDate = new \Carbon\Carbon($employee->start_working_date);
				$startDateForAccumulation = new \Carbon\Carbon($employee->start_working_date);
				$startWorkingDate->setYear(date('Y'));
				if($startWorkingDate->format('Y-m-d') > date('Y-m-d')) {
					$startWorkingDate->addYears(-1);
					$startDate = $startWorkingDate->format('Y-m-d');
					$endDate = $startWorkingDate->addYears(1)->addDay(-1)->format('Y-m-d');
				}
			} else {
				$startDate = date('Y-01-01');
				$endDate = date('Y-12-31');
				$startDateForAccumulation = new \Carbon\Carbon($startDate);
			}
		}

		$amount = 0;
		$attendances = Attendance::with([ 'employeeLeave.leaveReason' ])
							->where('type', Attendance::TYPE_CUTI)
							->where('id_employee', $this->id_employee)
							->get();

		foreach($attendances as $attendance) {
			$isCutLeaveQuota = true;
			if($employeeLeave = $attendance->employeeLeave) {
				if($leaveReason = $employeeLeave->leaveReason) {
					if(!$leaveReason->isCutLeaveQuota()) {
						$isCutLeaveQuota = false;
					}
				}
			}

			if($isCutLeaveQuota) {
				$amount++;
			}
		}


		if($this->isAllowForAccumulation()) {
			if($this->isMonthly()) {
				$amountOfPeriod = now()->diffInMonths($startDateForAccumulation);
			} else {
				$amountOfPeriod = now()->diffInYears($startDateForAccumulation);
			}
			$amountOfPeriod++;
			$quotaAvailable = ($this->quota * $amountOfPeriod) - $amount;
		} else {
			$quotaAvailable = $this->quota - $amount;
		}

		$this->update([
			'quota_available'	=> $quotaAvailable,
			'quota_used'		=> $amount,
		]);

		return $this;
	}


	public static function dt()
	{
		$data = self::select([ 'employee_leave_quotas.*' ])
					->has('employee')
					->with([ 'employee' ])
					->join('employees', 'employees.id', '=', 'employee_leave_quotas.id_employee');

		return \DataTables::of($data)
			->addColumn('employee.employee_name', function($data){
				return '<a href="'. route('employee.detail', $data->id_employee) .'">'. $data->employeeName() .'</a>';
			})
			->editColumn('period_type', function($data){
				return $data->periodTypeText();
			})
			->editColumn('is_allow_accumulation', function($data){
				return $data->isAllowAccumulationHtml();
			})
			->editColumn('quota_available', function($data){
				return $data->quota_available;
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">';

				if(UserPermission::check('employee_leave_quota', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('employee_leave_quota.edit', $data->id).'" title="Edit Jatah Cuti">
							<i class="mdi mdi-pencil"></i> Edit
						</a>';
				}

				if(UserPermission::check('employee_leave_quota', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('employee_leave_quota.destroy', $data->id).'" title="Hapus Jatah Cuti">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!UserPermission::check('employee_leave_quota', 'u') && !UserPermission::check('employee_leave_quota', 'd')) {
					$button .= '
						<a class="dropdown-item" href="javascript:void(0);">
							Tidak Ada Aksi
						</a>';
				}

				$button .= '
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'employee.employee_name', 'is_allow_accumulation', 'action' ])
			->make(true);
	}

	// public static function resetEmployeeLeaveQuotas2()
	// {
	// 	$employeeLeaveQuotas = self::whereHas('employee', function($query){
	// 		$query->where('status', Employee::STATUS_ACTIVE);
	// 	})->with([ 'employee' ])->get();

	// 	foreach($employeeLeaveQuotas as $employeeLeaveQuota) {
	// 		if($employeeLeaveQuota->isYearly()) {
	// 			if(date('m-d') == date('m-d', strtotime($employeeLeaveQuota->employee->start_working_date))) {
	// 				$employeeLeaveQuota->reset();
	// 			}
	// 		}
	// 	}
	// }

    public static function resetEmployeeLeaveQuotas2()
    {
        \Log::info('Menjalankan resetEmployeeLeaveQuotas2...');

        $employeeLeaveQuotas = self::whereHas('employee', function($query){
            $query->where('status', Employee::STATUS_ACTIVE);
        })->with(['employee'])->get();

        \Log::info('Jumlah data kuota cuti ditemukan: ' . $employeeLeaveQuotas->count());

        foreach ($employeeLeaveQuotas as $employeeLeaveQuota) {
            $employee = $employeeLeaveQuota->employee;
            if (!$employee || !$employee->start_working_date) {
                \Log::warning("Lewati: melewati data tanggal mulai kerja yang kosong untuk ID: {$employeeLeaveQuota->id_employee}");
                continue;
            }

            $startWorkingDate = \Carbon\Carbon::parse($employee->start_working_date);
            $now = now();

            if ($employeeLeaveQuota->isYearly()) {
                $tanggalHariIni = $now->format('m-d');
                $tanggalMulaiKerja = $startWorkingDate->format('m-d');

                \Log::info("Cek tipe data YEARLY untuk employee ID {$employeeLeaveQuota->id_employee} | Tanggal: $tanggalMulaiKerja");

                if ($tanggalHariIni === $tanggalMulaiKerja) {
                    \Log::info("RESET YEARLY: ID {$employeeLeaveQuota->id} - Employee ID: {$employeeLeaveQuota->id_employee}");
                    $employeeLeaveQuota->reset();
                }

            } elseif ($employeeLeaveQuota->isMonthly()) {
                $tanggalHariIni = $now->day;
                $tanggalMulaiKerja = $startWorkingDate->day;

                \Log::info("Cek MONTHLY untuk employee ID {$employeeLeaveQuota->id_employee} | Hari: $tanggalMulaiKerja");

                if ($tanggalHariIni === $tanggalMulaiKerja) {
                    \Log::info("RESET MONTHLY: ID {$employeeLeaveQuota->id} - Employee ID: {$employeeLeaveQuota->id_employee}");
                    $employeeLeaveQuota->reset();
                }
            }
        }
    }




	/**
	 * 	Resume Report
	 * */
	public static function generateDataForReport($request, $filename = null)
	{
		$quotas = self::select([ 'employee_leave_quotas.*' ])
					  ->has('employee')
					  ->with([ 'employee.department', 'employee.position', 'employee.employeeGroup' ])
					  ->leftJoin('employees', 'employees.id', '=', 'employee_leave_quotas.id_employee')
					  ->leftJoin('departments', 'departments.id', '=', 'employees.id_department')
					  ->leftJoin('positions', 'positions.id', '=', 'employees.id_position')
					  ->leftJoin('employee_groups', 'employee_groups.id', '=', 'employees.id_employee_group')
					  ->where('employees.status', Employee::STATUS_ACTIVE);

		if(empty($filename)) $filename = 'Rekap Jatah Cuti';

		if(!empty($request->id_department)) {
			if($request->id_department != 'all') {
				$quotas = $quotas->where('employees.id_department', $request->id_department);
			}
		}

		if(!empty($request->id_position)) {
			if($request->id_position != 'all') {
				$quotas = $quotas->where('employees.id_position', $request->id_position);
			}
		}

		if(!empty($request->id_employee_group)) {
			if($request->id_employee_group != 'all') {
				$quotas = $quotas->where('employees.id_employee_group', $request->id_employee_group);
			}
		}

		if(!empty($request->id_employee)) {
			if($request->id_employee != 'all') {
				$quotas = $quotas->where('attendances.id_employee', $request->id_employee);
			}
		}

		$quotas = $quotas->orderBy('employees.employee_name', 'asc')
						 ->orderBy('departments.department_name', 'asc')
						 ->get();

		return [
			'data'		=> $quotas,
			'filename'	=> $filename,
		];
	}


	public static function generatePdfReport($request)
	{
		$data = self::generateDataForReport($request);
		$quotas = $data['data'];
		$filename = $data['filename'];

		$pdf = \PDF::loadView('admin.employee_setting.leave_quota.pdf', [
			'quotas'	=> $quotas,
		])->setPaper('A4', 'portrait');
		$filename .= '.pdf';

		return (object) [
			'pdf'		=> $pdf,
			'filename'	=> $filename,
		];
	}


	public static function streamPdfReport($request)
	{
		$result = self::generatePdfReport($request);

		return $result->pdf->stream($result->filename);
	}


	public static function downloadPdfReport($request)
	{
		$result = self::generatePdfReport($request);

		return $result->pdf->download($result->filename);
	}


	public static function downloadExcelReport($request)
	{
		$data = self::generateDataForReport($request);
		$quotas = $data['data'];
		$filename = $data['filename'].'.xlsx';

		$headerStyle = [ 'font-style'=>'bold', 'halign'=>'center', 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin' ];
		$bodyStyle = [ 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin' ];

		$writer = new \App\MyClass\XLSXWriter();

		$totalRow = 0;
		$totalColumn = 7;

		$writer->writeSheetHeader('Sheet1', [
			'Rekap Cuti'	=> 'string',
		], [
			'widths'=> [5,25,25,25,25,25,25,25],
			'font-style'=>'bold', 'halign'=>'center', 'valign' => 'center', 'height'=> 5, 'wrap_text' => true
		]);
		$writer->markMergedCell('Sheet1', $start_row=0, $start_col=0, $end_row=0, $end_col=$totalColumn);
		$totalRow++;

		$writer->writeSheetRow('Sheet1', []);

		$writer->writeSheetRow('Sheet1', [
			'No',
			'Karyawan',
			'Departemen',
			'Jabatan',
			'Periode Reset Cuti',
			'Jatah Cuti Per Periode',
			'Potongan Cuti Bersama',
			'Sisa Jatah Cuti (Belum Potong Cuti Bersama)',
		], $headerStyle);

		$iter = 1;

		foreach($quotas as $quota) {
			$writer->writeSheetRow('Sheet1', [
				" $iter",
				$quota->employeeName(),
				$quota->employee->departmentName(),
				$quota->employee->positionName(),
				$quota->periodTypeText(),
				$quota->quota,
				$quota->mass_leave_cut,
				$quota->quota_available,
			], $bodyStyle);
			$iter++;
		}

		$path = \Helper::tempsPath($filename);
		$writer->writeToFile($path);

		return $path;
	}
}
