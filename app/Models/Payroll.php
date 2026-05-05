<?php

namespace App\Models;

use App\MyClass\Helper;
use App\MyClass\WhatsappNew;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
	protected $fillable = [ 'id_employee', 'period_start', 'period_end', 'basic_salary', 'total_allowance', 'total_cut', 'bonus', 'total', 'notes', 'publish_date', 'publish_status', 'send_status', 'send_schedule' ];


	const STATUS_PENDING	= 1;
	const STATUS_PUBLISHED	= 2;


	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee')->withTrashed();
	}


	public function payrollAllowances()
	{
		return $this->hasMany('App\Models\PayrollAllowance', 'id_payroll');
	}


	public function payrollCuts()
	{
		return $this->hasMany('App\Models\PayrollCut', 'id_payroll');
	}


	public function payrollAttendances()
	{
		return $this->hasMany('App\Models\PayrollAttendance', 'id_payroll');
	}


	public function publishDateText()
	{
		return date('d M Y', strtotime($this->publish_date));
	}


	public function publishStatusText()
	{
		if($this->publish_status == self::STATUS_PENDING) return 'Pending';
		if($this->publish_status == self::STATUS_PUBLISHED) return 'Telah Terbit';

		return null;
	}


	public function basicSalaryText()
	{
		return 'Rp. '.number_format($this->basic_salary);
	}


	public function totalAllowanceText()
	{
		return 'Rp. '.number_format($this->total_allowance);
	}


	public function totalCutText()
	{
		return 'Rp. '.number_format($this->total_cut);
	}


	public function bonusText()
	{
		return 'Rp. '.number_format($this->bonus);
	}


	public function totalText()
	{
		return 'Rp. '.number_format($this->total);
	}


	public function periodText()
	{
		$date = function($d) { return date('d M Y', strtotime($d)); };

		if($this->period_start == $this->period_end)
		{
			return $date($this->period_start);
		}
		else
		{
			return "{$date($this->period_start)} - {$date($this->period_end)}";
		}
	}


	public static function createPayroll($request)
	{

	}


	public static function dataTable($request)
	{
		$data = self::has('employee')
					->with([ 'employee' ]);

		return \DataTables::eloquent($data)
			->addColumn('employee_name', function($data){
				return $data->employeeName();
			})
			->editColumn('period_start', function($data){
				return $data->periodText();
			})
			->editColumn('send_status', function($data){
				$status = $data->sendStatusHtml();
				if($data->isWaitingToSend()) {
					$status .= '<br>['.$data->sendScheduleFormatted('d M Y H:i').']';
				}
				return $status;
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('payroll.detail', $data->id).'" title="Detail Penggajian">
							<i class="mdi mdi-magnify"></i> Detail 
						</a>
						<a class="dropdown-item" href="'.route('payroll.slip', $data->id).'" title="Download Slip Penggajian" download>
							<i class="mdi mdi-download"></i> Download Slip 
						</a>';

				if(UserPermission::check('payroll', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('payroll.destroy', $data->id).'" title="Hapus Penggajian">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				$button .= '
					</div>
				</div>';

				return $button;
			})
			->editColumn("total", function($data){
				return $data->totalText();
			})
			->editColumn("created_at", function($data){
				return $data->createdAtTextSortable();
			})
			->rawColumns([ 'action', 'send_status' ])
			->make(true);
	}


	public function createdAtTextSortable()
	{
		return date('Y-m-d', strtotime($this->created_at));
	}


	public function employeeName()
	{
		return $this->employee ? $this->employee->employee_name : '-';
	}


	public function departmentName()
	{
		return $this->employee ? $this->employee->departmentName() : '-';
	}


	public function monthNameText()
	{
		$monthStart = date('Y-m', strtotime($this->period_start));
		$monthEnd = date('Y-m', strtotime($this->period_end));

		if($monthStart == $monthEnd) {
			$month = (int) date('n', strtotime($monthStart.'-01'));

			return \Date::monthName($month);
		} else {
			$monthStart = (int) date('n', strtotime($monthStart.'-01'));
			$monthEnd = (int) date('n', strtotime($monthEnd.'-01'));

			return \Date::monthName($monthStart)." - ".\Date::monthName($monthEnd);
		}
	}


	public function yearText()
	{
		$yearStart = date('Y', strtotime($this->period_start));
		$yearEnd = date('Y', strtotime($this->period_end));

		if($yearStart == $yearEnd) {
			return $yearStart;
		} else {
			return "{$yearStart} - {$yearEnd}";
		}
	}


	public function amountOfDays()
	{
		return \Date::diffInDays($this->period_start, $this->period_end) + 1;
	}


	public function amountOfSunday()
	{
		$amountOfSunday = 0;
		$date = \Date::toCarbon($this->period_start);
		$endDate = \Date::toCarbon($this->period_end);

		while($date != $endDate) {
			if(\Date::tt($date, 'N') == 7) {
				$amountOfSunday++;
			}
			$date->addDays(1);
		}

		return (int) $amountOfSunday;
	}


	public function amountOfOffDays()
	{
		$amountOfOffDays = 0;

		foreach($this->payrollAttendances as $attendance) {
			if($attendance->attendance->isTypeLibur()) {
				$amountOfOffDays++;
			}
		}

		return (int) $amountOfOffDays;
	}


	public function amountOfOffDaysWithoutSunday()
	{
		$offDays = $this->amountOfOffDays();
		$sunday = $this->amountOfSunday();
		$amount = $offDays - $sunday;

		return $amount >= 0 ? $amount : 0;
	}


	public function amountOfWorkDay()
	{
		return (int) ( $this->amountOfDays() - $this->amountOfOffDaysWithoutSunday() - $this->amountOfSunday() );
	}


	public function dailySalary()
	{
		return round($this->basic_salary / $this->amountOfWorkDay(), 2);
	}


	public function dailySalaryText()
	{
		return 'Rp. '.number_format($this->dailySalary());
	}


	public function removePayrollAttendances()
	{
		foreach($this->payrollAttendances as $attendance)
		{
			$attendance->delete();
		}

		return $this;
	}


	public function removePayrollAllowances()
	{
		foreach($this->payrollAllowances as $allowance)
		{
			$allowance->delete();
		}

		return $this;
	}


	public function removePayrollCuts()
	{
		foreach($this->payrollCuts as $cut)
		{
			$cut->delete();
		}

		return $this;
	}


	public function deletePayroll()
	{
		$this->removePayrollAttendances();
		$this->removePayrollAllowances();
		$this->removePayrollCuts();

		return $this->delete();
	}


	public static function processingPeriodAndEmployees($request)
	{
		$employeeIDs = '';

		foreach($request->id_employees as $id)
		{
			$employee = Employee::find($id);
			if($employee) {
				$employeeIDs .= ",{$id}";
			}
		}

		$employeeIDs = substr($employeeIDs, 1);

		$route = route('payroll.create', [
			'action'		=> 'enter-nominal',
			'employee_list'	=> $employeeIDs,
			'period_start'	=> $request->period_start,
			'period_end'	=> $request->period_end
		]);

		return [
			'route'	=> $route,
		];
	}


	public static function processingNominal($request)
	{
		$data = [];
		$iteration = 0;

		$idEmployees 	= $request->id_employee;
		$basicSalary 	= $request->basic_salary;
		$overtimePay 	= $request->total_overtime_pay;
		$bonus 			= $request->bonus;
		$totalSalary 	= $request->total_salary;
		$allowances 	= $request->allowance_item;
		$cuts 			= $request->cut_item;
		$notes			= $request->notes;

		foreach($idEmployees as $idEmployee)
		{
			$totalAllowance = 0;
			$totalCut 		= 0;
			$dataAllowances	= [];
			$dataCuts		= [];

			if(!empty($allowances)) {

				if(array_key_exists($idEmployee, $allowances))
				{
					foreach($allowances[$idEmployee] as $allowance)
					{
						$allowance = explode('###', $allowance);
						$totalAllowance += $allowance[1];
						$dataAllowances[] = [
							'allowance_name'	=> $allowance[0],
							'allowance_nominal'	=> $allowance[1]
						];
					}
				}
			}

			if(!empty($cuts)) {

				if(array_key_exists($idEmployee, $cuts))
				{
					foreach($cuts[$idEmployee] as $cut)
					{
						$cut = explode('###', $cut);
						$totalCut += $cut[1];
						$dataCuts[] = [
							'cut_name'		=> $cut[0],
							'cut_nominal'	=> $cut[1]
						];
					}
				}

			}

			$employee = [
				'id'				=> $idEmployee,
				'employee' 			=> \App\Models\Employee::find($idEmployee),
				'basic_salary' 		=> $basicSalary[$iteration],
				'overtime_pay' 		=> $overtimePay[$iteration],
				'bonus'				=> $bonus[$iteration],
				'total_cut'			=> $totalCut,
				'total_allowance'	=> $totalAllowance,
				'total_salary' 		=> $totalSalary[$iteration],
				'notes'				=> $notes[$iteration],
				'allowances'		=> $dataAllowances,
				'cuts'				=> $dataCuts
			];
			$data[] = $employee;
			$iteration++;
		}

		$result = serialize($data);
		$filename = 'payrol_'.date('Ymd_His').'.txt';
		\File::put(\Setting::temps($filename), $result);

		$route = route('payroll.create', [
			'action'		=> 'approval',
			'temp'			=> $filename,
			'period_start'	=> $request->period_start,
			'period_end'	=> $request->period_end
		]);

		return [
			'route'	=> $route,
		];
	}


	public static function processingApprove($request)
	{
		set_time_limit(0);
		$data = \File::get(\Setting::temps($request->temp));
		$data = unserialize($data);
		$iteration = 1;

		foreach($data as $employee)
		{
			\DB::beginTransaction();

			try {
				$payroll = self::create([
					'id_employee'		=> $employee['id'],
					'period_start'		=> $request->period_start,
					'period_end'		=> $request->period_end,
					'basic_salary'		=> $employee['basic_salary'],
					'total_allowance'	=> $employee['total_allowance'],
					'total_cut'			=> $employee['total_cut'],
					'bonus'				=> $employee['bonus'],
					'total'				=> $employee['total_salary'],
					'notes'				=> $employee['notes'],
					'publish_date'		=> now(),
					'publish_status'	=> Payroll::STATUS_PUBLISHED
				]);

				$attendances = $payroll->employee->getAttendanceByDateRange($request->period_start, $request->period_end);
				foreach($attendances as $attendance)
				{
					$payroll->addAttendance([
						'id_attendance'	=> $attendance->id,
						'salary'		=> $attendance->dailySalary($employee['basic_salary'], $employee['overtime_pay']),
					]);
				}

				foreach($employee['allowances'] as $allowance)
				{
					$payroll->addAllowance($allowance);
				}

				foreach($employee['cuts'] as $cut)
				{
					$payroll->addCut($cut);
				}

				if($request->broadcast == 'yes') {
					$sendSchedule = now()->addMinutes(ceil($iteration/5));
					$payroll->update([
						'send_status'	=> 'Menunggu',
						'send_schedule'	=> $sendSchedule,
					]);
				}
				\DB::commit();
				
			} catch (\Exception $e) {
				$emp = Employee::find($employee['id']);
				$message = $emp->employee_name;
				$message .= "\n". $e->getMessage().' '.$e->getLine();
				$message .= "\n". $e->getTraceAsString();
				$EndPointWa = WhatsappNew::END_POINT_WA;
				if($EndPointWa == 'WA Baru'){
					// wa Baru
					Helper::sendNotificationWhatsapp($phoneNumber = '6282316425264', $message);
				}else{
						\App\MyClass\Whatsapp::sendChat([
							'message'	=> $message,
							'to'		=> '6282316425264',
						]);
				}
				\DB::rollback();
			}

		}
	}


	public function addAttendance($data)
	{
		$data['id_payroll']	= $this->id;
		PayrollAttendance::create($data);

		return $this;
	}


	public function addAllowance($data)
	{
		$data['id_payroll']	= $this->id;
		PayrollAllowance::create($data);

		return $this;
	}


	public function addCut($data)
	{
		$data['id_payroll']	= $this->id;
		PayrollCut::create($data);

		return $this;
	}


	public function noteHtml()
	{
		if(empty($this->notes)) return '-';

		return str_replace('\n', '<br>', $this->notes);
	}


	public function isHasAllowance()
	{
		return count($this->payrollAllowances) > 0;
	}


	public function isHasBonus()
	{
		return $this->bonus > 0;
	}


	public function isHasCut()
	{
		return count($this->payrollCuts) > 0;
	}


	private function generateSlip()
	{
		$this->load('payrollAllowances');
		$this->load('payrollCuts');
		$this->load('employee.department');
		$this->load('employee.position');

		$incomes = [];
		$cuts = [];

		if($this->basic_salary > 0) {
			$incomes[] = (object) [
				'name'		=> 'Gaji Pokok',
				'nominal'	=> $this->basic_salary,
			];
		}

		// if($this->)

		if($this->isHasBasicSalaryCut()) {
			$cuts[] = (object) [
				'name'		=> 'Denda Terlambat/Alfa',
				'nominal'	=> $this->basicSalaryCut(),
			];
		}

		foreach($this->payrollAllowances as $allowance) {
			$incomes[] = (object) [
				'name'		=> $allowance->allowance_name,
				'nominal'	=> $allowance->allowance_nominal,
			];
		}

		foreach($this->payrollCuts as $cut) {
			$cuts[] = (object) [
				'name'		=> $cut->cut_name,
				'nominal'	=> $cut->cut_nominal,
			];
		}

		$customPaper = array(0,0,567.00,380.00);
		$pdf = \PDF::loadView('payroll.pdf_payroll_new', [
			'payroll' 	=> $this,
			'incomes'	=> $incomes,
			'cuts'		=> $cuts,
			'employee'	=> $this->employee
		])->setPaper($customPaper, 'portrait');

		// return $pdf->stream();
		// $payroll = self::with([ 'payrollAttendances.attendance.attendanceMeta' ])->find($this->id);
		// $pdf = \PDF::loadView('payroll.pdf_payroll_slip', [
		// 	'payroll'	=> $payroll
		// ]);

		return $pdf;
	}


	public function downloadSlip()
	{
		$pdf = $this->generateSlip();
		$employeeName = $this->employee ? $this->employee->employee_name : '';
		$filename = 'Slip_Gaji_'.\Str::slug($employeeName.' '.$this->periodText(), '_').'.pdf';

		return $pdf->stream($filename);
	}


	public function saveSlip()
	{
		$pdf = $this->generateSlip();
		$employeeName = $this->employee ? $this->employee->employee_name : '';
		$filename = 'Slip_Gaji_'.\Str::slug($employeeName.' '.$this->periodText(), '_').'.pdf';

		$pdf->save(\Setting::temps($filename));

		return \Setting::temps($filename);
	}


	public function slipLink()
	{
		$employeeName = $this->employee ? $this->employee->employee_name : '';
		$filename = 'Slip_Gaji_'.\Str::slug($employeeName.' '.$this->periodText(), '_').'.pdf';
		if(!\File::exists(storage_path('app/public/temps/'.$filename))) {
			$this->saveSlip();
		}
		return url('storage/temps/'.$filename);
	}


	public function realBasicSalary()
	{
		return $this->total - $this->total_allowance + $this->total_cut - $this->bonus; 
	}


	public function realBasicSalaryText()
	{
		return 'Rp. '.number_format($this->realBasicSalary());
	}


	public function isBasicSalaryFully()
	{
		return $this->basic_salary == $this->realBasicSalary();
	}


	public function basicSalaryCut()
	{
		return $this->basic_salary - $this->realBasicSalary();
	}


	public function basicSalaryCutText()
	{
		return 'Rp. '.number_format($this->basicSalaryCut());
	}


	public function isHasBasicSalaryCut()
	{
		return $this->basicSalaryCut() > 0;
	}


	public function basicSalaryWithCutHtml()
	{
		if($this->isHasBasicSalaryCut()) return $this->realBasicSalaryText();

		return $this->realBasicSalaryText().' (<span class="text-danger"> - '.$this->basicSalaryCut().'</span>)';
	}


	public function sendToEmployee()
	{
		if($this->employee)
		{
			$message = '*'.\Setting::getValue('company_name').'*';
			$message .= "\n\nTotal gaji bulan ini ".$this->totalText();
			$message .= "\nDetail penggajian bisa dilihat melalui aplikasi web based.";
			$message .= "\nLink Login ".route('login');

			$EndPointWa = WhatsappNew::END_POINT_WA;
				if($EndPointWa == 'WA Baru'){
					// wa Baru
					Helper::sendNotificationWhatsapp($phoneNumber = '6282316425264', $message, $filePath = $this->saveSlip(), $caption="Slip Gaji");
				}else{
					\App\MyClass\Whatsapp::sendMedia([
						'to'	=> $this->employee->phone_number,
						'path'	=> $this->saveSlip(),
					]);


					\App\MyClass\Whatsapp::sendChat([
						'to'	=> $this->employee->phone_number,
						'text'	=> $message,
					]);
				}
		}

		return $this;
	}


	public function fetchData()
	{
		$allowanceList = [];
		$cutList = [];

		foreach($this->payrollAllowances as $allowance) {
			$allowanceList[] = (object) [
				'allowance_name'	=> $allowance->allowance_name,
				'allowance_nominal'	=> $allowance->allowanceNominalText(),
			];
		}

		foreach($this->payrollCuts as $cut) {
			$cutList[] = (object) [
				'cut_name'		=> $cut->cut_name,
				'cut_nominal'	=> $cut->cutNominalText(),
			];
		}

		if($this->realBasicSalary() > 0) {
			$cutList[] = (object) [
				'cut_name'		=> 'Denda Terlambat/Alpa',
				'cut_nominal'	=> $this->basicSalaryCutText(),
			];
		}

	
		 $idEmployee = $this->id_employee;
		 $periodStart = $this->period_start;
		 $periodEnd =  $this->period_end;
		 $attendances = Attendance::where('id_employee', $idEmployee)->whereBetween('date',[$periodStart, $periodEnd])->get();

		 $data = [];

			foreach($attendances as $attendance){
				array_push($data,[
					'tanggal' => $attendance->dateText(),
					'jam_masuk'	=> $attendance->clockInText(),
					'keterlambatan'	=> $attendance->lateText(),
				]);
			}

		return (object) [
			'id'			=> $this->id,
			'id_employee'	=> $this->id_employee,
			'employee_name'	=> $this->employeeName(),
			'basic_salary'	=> $this->basicSalaryText(),
			'total_allowance' => $this->totalAllowanceText(),
			'total_cut'		=> $this->totalCutText(),
			'bonus'			=> $this->bonusText(),
			'total' 		=> $this->totalText(),
			'notes'			=> $this->notes,
			'bulan_tahun'	=> Carbon::parse($periodStart)->isoFormat('MMMM Y'),
			'allowance_list'=> $allowanceList,
			'cut_list'		=> $cutList,
			'slip_link'		=> $this->slipLink(),
			'list_attendance' => $data,
		];

		
	}

	public function sendStatusHtml()
	{
		$status = $this->send_status;

		if($status == 'Menunggu') {
			return '<span class="text-primary"> Menunggu </span>';
		} elseif ($status == 'Terkirim') {
			return '<span class="text-success"> Terkirim </span>';
		} elseif ($status == 'Pengiriman') {
			return '<span class="text-success"> Pengiriman </span>';
		} else {
			return '-';
		}
	}

	public function isWaitingToSend()
	{
		return $this->send_status == 'Menunggu';
	}


	public static function fetchPayrolls($payrolls)
	{
		$results = [];

		foreach($payrolls as $payroll) {
			$results[] = $payroll->fetchData();
		}

		return $results;
	}

	public function sendScheduleFormatted($format = 'Y-m-d')
	{
		if(empty($this->send_schedule)) return '-';
		return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->send_schedule)->format($format);
	}



	/**
	 * 	Resume Report
	 * */
	public static function resumeGenerateDataForReport($request, $filename = null)
	{
		$payrolls = self::select([ 'payrolls.*' ])
						->with([ 'employee.department', 'employee.position', 'employee.employeeGroup' ])
						->leftJoin('employees', 'employees.id', '=', 'payrolls.id_employee')
						->leftJoin('departments', 'departments.id', '=', 'employees.id_department')
						->where('period_start', '>=', $request->start_date)
						->where('period_start', '<=', $request->end_date);

		if(empty($filename)) $filename = 'Rekap_Payroll';

		if(!empty($request->id_department)) {
			if($request->id_department != 'all') {
				$payrolls = $payrolls->where('employees.id_department', $request->id_department);
			}
		}

		if(!empty($request->id_position)) {
			if($request->id_position != 'all') {
				$payrolls = $payrolls->where('employees.id_position', $request->id_position);
			}
		}

		if(!empty($request->id_employee_group)) {
			if($request->id_employee_group != 'all') {
				$payrolls = $payrolls->where('employees.id_employee_group', $request->id_employee_group);
			}
		}

		if(!empty($request->id_employee)) {
			if($request->id_employee != 'all') {
				$payrolls = $payrolls->where('payrolls.id_employee', $request->id_employee);
			}
		}

		$payrolls = $payrolls->orderBy('payrolls.period_start', 'asc')
							 ->orderBy('employees.employee_name', 'asc')
							 ->orderBy('departments.department_name', 'asc')
							 ->get();

		return [
			'data'		=> $payrolls,
			'startDate'	=> $request->start_date,
			'endDate'	=> $request->end_date,
			'filename'	=> $filename,
		];
	}


	public static function resumeGeneratePdfReport($request)
	{
		$data = self::resumeGenerateDataForReport($request);
		$payrolls = $data['data'];
		$startDate = $data['startDate'];
		$endDate = $data['endDate'];
		$filename = $data['filename'];

		$pdf = \PDF::loadView('admin.payroll_resume.pdf', [
			'payrolls'	=> $payrolls,
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
		$payrolls = $data['data'];
		$startDate = $data['startDate'];
		$endDate = $data['endDate'];
		$filename = $data['filename'].'.xlsx';

		$headerStyle = [ 'font-style'=>'bold', 'halign'=>'center', 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin' ];
		$bodyStyle = [ 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin' ];

		$writer = new \App\MyClass\XLSXWriter();

		$totalRow = 0;
		$totalColumn = 6;

		$writer->writeSheetHeader('Sheet1', [
			'Rekap Penggajian'	=> 'string',
		], [
			'widths'=> [5,30,20,15,15,20,20],
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
			'Karyawan',
			'Departemen',
			'Bank',
			'No Rekening',
			'Bulan Penggajian',
			'Nominal',
		], $headerStyle);

		$iter = 1;
		$total = 0;

		foreach($payrolls as $payroll) {
			$total += $payroll->nominal;
			$writer->writeSheetRow('Sheet1', [
				" $iter",
				$payroll->employeeName('d/m/Y'),
				$payroll->departmentName(),
				$payroll->employee->bank_name ?? '-',
				$payroll->employee->bank_account_number ?? '-',
				\App\MyClass\Date::monthName($payroll->period_start).' '.date('Y', strtotime($payroll->period_start)),
				$payroll->total,
			], $bodyStyle);
			$iter++;

			$total += $payroll->total;
		}

		$writer->writeSheetRow('Sheet1', [
			'Total Penggajian',
			'',
			'',
			'',
			'',
			'',
			$total,
		], $bodyStyle);
		$writer->markMergedCell('Sheet1', $start_row=4+$iter, $start_col=0, $end_row=4+$iter, $end_col=5);

		$path = \Helper::tempsPath($filename);
		$writer->writeToFile($path);

		return $path;
	}

	public static function sendWaitingPayrolls()
	{
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		$payrolls = self::has('employee')
						->where('send_status', 'Menunggu')
						->where('send_schedule', '!=', null)
						->where('send_schedule', '<=', now()->format('Y-m-d H:i:s'))
						->orderBy('send_schedule', 'asc')
						->take(5)
						->get();

		foreach($payrolls as $payroll) {
			$payroll->update([
				'send_status' 	=> 'Pengiriman',
				'send_schedule'	=> null,
			]);

			$payroll->sendToEmployee();

			$payroll->update([
				'send_status' 	=> 'Terkirim'
			]);
		}
	}
}
