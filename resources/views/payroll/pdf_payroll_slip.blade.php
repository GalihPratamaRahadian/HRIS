<!DOCTYPE html>
<html>
<head>
	<title></title>

	<style type="text/css">
		
		* {
			text-transform: uppercase;
			font-family: Calibri, sans-serif;
		}

		h3 {
			font-size: 12pt;
			margin-bottom: 0rem;
			margin-top: 0rem;
		}

		td, th {
			font-size: 8pt;
			vertical-align: top;
		}

		.attendance-table {
			width: 100%;
			border-collapse: collapse;
		}

		.attendance-table td,
		.attendance-table th {
			padding: 3px;
			text-align: center;
		}

		.row-danger > td > span {
			color: red;
		}

	</style>
</head>
<body>

	<h3 align="center"> Slip Gaji </h3>
	<h3 align="center"> {{ \Setting::getValue('company_name', 'PT. Adiva Sumber Solusi') }} </h3>
	<br>


	<table width="100%">

		<tr>
			<td width="100"> Nama </td>
			<td> {{ $payroll->employeeName() }} </td>
			<td> </td>
			<td rowspan="11" align="right">
				<img src="{{ $payroll->employee->photoPath('face') }}" width="150">
			</td>
		</tr>

		<tr>
			<td> Departemen </td>
			<td> {{ $payroll->departmentName() }} </td>
			<td> </td>
			<td> </td>
		</tr>

		<tr>
			<td> Bulan </td>
			<td> {{ $payroll->monthNameText() }} </td>
			<td> </td>
			<td> </td>
		</tr>

		<tr>
			<td> Tahun </td>
			<td> {{ $payroll->yearText() }} </td>
			<td> </td>
			<td> </td>
		</tr>

		<tr>
			<td> Jumlah Hari </td>
			<td> {{ $payroll->amountOfDays() }} </td>
			<td> </td>
			<td> </td>
		</tr>

		<tr>
			<td> Hari Libur Shift </td>
			<td> {{ $payroll->amountOfSunday() }} </td>
			<td> </td>
			<td> </td>
		</tr>

		<tr>
			<td> Hari Libur Nasional </td>
			<td> {{ $payroll->amountOfOffDaysWithoutSunday() }} </td>
			<td> </td>
			<td> </td>
		</tr>

		<tr>
			<td> Total Hari Kerja </td>
			<td> {{ $payroll->amountOfWorkDay() }} </td>
			<td> </td>
			<td> </td>
		</tr>

		<tr>
			<td> Gaji Pokok </td>
			<td align="right"> {!! $payroll->basicSalaryText() !!} </td>
			<td> </td>
			<td> </td>
		</tr>

		@if($payroll->isHasBonus())
		<tr>
			<td> Bonus </td>
			<td align="right"> {{ $payroll->bonusText() }} </td>
			<td> </td>
			<td> </td>
		</tr>
		@endif

		@if($payroll->isHasAllowance())
		<tr>
			<td> Total Tunjangan </td>
			<td align="right"> {{ $payroll->totalAllowanceText() }} </td>
			<td> </td>
			<td> </td>
		</tr>
		@endif

		@if($payroll->isHasCut())
		<tr>
			<td> Total Potongan </td>
			<td style="color: red;" align="right">  - {{ $payroll->totalCutText() }} </td>
			<td> </td>
			<td> </td>
		</tr>
		@endif

		@if($payroll->isHasBasicSalaryCut())
		<tr>
			<td> Denda </td>
			<td style="color: red;" align="right"> - {{ $payroll->basicSalaryCutText() }} </td>
			<td> </td>
			<td> </td>
		</tr>
		@endif

		<tr>
			<td> Total Akhir </td>
			<td align="right"><b> {{ $payroll->totalText() }} </b></td>
			<td> </td>
			<td> </td>
		</tr>

		<tr>
			<td colspan="4"></td>
		</tr>

		<tr>
			<td> Upah Harian </td>
			<td align="right"> {{ $payroll->dailySalaryText() }} </td>
			<td> </td>
			<td> </td>
		</tr>




	</table>

	<br>

	<table class="attendance-table" border="1">
		<tr>
			<th> Hari </th>
			<th> Tanggal </th>
			<th> Foto Absensi </th>
			<th> Jam Masuk </th>
			<th> Jam Keluar </th>
			<th> Terlambat </th>
			<th> Jam Kerja Valid </th>
			<th> Lembur </th>
			<th> Rupiah </th>
		</tr>
		@foreach($payroll->payrollAttendances as $payrollAttendance)
		@if($attendance = $payrollAttendance->attendance)

		@if($attendance->isTypeHadir())
		<tr>
			<td> {{ \Date::dayName($attendance->date) }} </td>
			<td> {{ $attendance->date }} </td>
			@if($attendanceMeta = $attendance->attendanceMeta)
				@if($attendanceMeta->isHasClockInPhoto())
				<td> 
					<img src="{{ $attendanceMeta->clockInPhotoLink() }}" style="width: 50px;">
				</td>
				@else
				<td> - </td>
				@endif
			@else
			<td> - </td>
			@endif
			<td> {{ $attendance->clockInText() }} </td>
			<td> {{ $attendance->clockOutText() }} </td>
			<td> {{ $attendance->lateTimeTextShort() }} </td>
			<td> {{ $attendance->getWorkTimeLikeClockFormat() }} </td>
			<td> Rp. 0 </td>
			<td> {{ $payrollAttendance->salaryText() }} </td>
		</tr>
		@elseif($attendance->isTypeCuti() || $attendance->isTypeSakit())
		<tr>
			<td> {{ \Date::dayName($attendance->date) }} </td>
			<td> {{ $attendance->date }} </td>
			<td> {{ $attendance->typeText() }} </td>
			<td> {{ $attendance->typeText() }} </td>
			<td> {{ $attendance->typeText() }} </td>
			<td> {{ $attendance->typeText() }} </td>
			<td> {{ $attendance->typeText() }} </td>
			<td> {{ $attendance->typeText() }} </td>
			<td> {{ $payrollAttendance->salaryText() }} </td>
		</tr>
		@elseif($attendance->isTypeLibur() || $attendance->isTypeTanpaKeterangan() || $attendance->isTypeIzin())
		<tr class="row-danger">
			<td><span> {{ \Date::dayName($attendance->date) }} </span></td>
			<td><span> {{ $attendance->date }} </span></td>
			<td><span> {{ $attendance->typeText() }} </span></td>
			<td><span> {{ $attendance->typeText() }} </span></td>
			<td><span> {{ $attendance->typeText() }} </span></td>
			<td><span> {{ $attendance->typeText() }} </span></td>
			<td><span> {{ $attendance->typeText() }} </span></td>
			<td><span> {{ $attendance->typeText() }} </span></td>
			<td><span> {{ $attendance->typeText() }} </span></td>
		</tr>
		@endif

		@endif
		@endforeach
	</table>

</body>
</html>