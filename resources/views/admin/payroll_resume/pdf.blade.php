<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		* {
			font-family: Calibri, Arial, sans-serif;
			font-size: 10pt;
		}

		.title {
			text-align: center;
			font-size: 20pt;
			margin-bottom: 10px;
		}

		.date {
			text-align: center;
			font-size: 10pt;
		}

		.table {
			width: 100%;
			border-collapse: collapse;
		}

		.table td,
		.table th {
			font-size: 10pt;
			padding: 3px;
			border: 1px solid black;
		}

		.bg-grey {
			background: rgb(199 199 199);
		}

		.red {
			color: red;
		}
	</style>
</head>
<body>

	<div class="title"> Rekap Penggajian </div>

	@if(!empty($startDate) && !empty($endDate))
	<div class="date">
		Periode : 
		@if($startDate == $endDate)
		{{ date('d-m-Y', strtotime($startDate)) }}
		@else
		{{ date('d-m-Y', strtotime($startDate)) }} s/d {{ date('d-m-Y', strtotime($endDate)) }}
		@endif	
	</div>
	@endif

	<br>


	<table class="table">
		<thead>
			<tr>
				<th> No </th>
				<th> Karyawan </th>
				<th> Departemen </th>
				<th> Bank </th>
				<th> No Rekening </th>
				<th> Bulan Penggajian </th>
				<th align="right"> Nominal </th>
			</tr>
		</thead>

		<tbody>
			<?php $total = 0; ?>
			@forelse($payrolls as $payroll)
			<?php $total += $payroll->total; ?>
			<tr>
				<td align="center"> {{ $loop->iteration }} </td>
				<td> {{ $payroll->employeeName() }} </td>
				<td> {{ $payroll->departmentName() }} </td>
				<td align="center"> {{ $payroll->employee->bank_name ?? '-' }} </td>
				<td align="center"> {{ $payroll->employee->bank_account_number ?? '-' }} </td>
				<td align="center"> {{ \App\MyClass\Date::monthName($payroll->period_start) }} {{ date('Y', strtotime($payroll->period_start)) }} </td>
				<td align="right"> {{ $payroll->totalText() }} </td>
			</tr>
			@empty
			<tr>
				<td colspan="7" align="center">
					Kosong
				</td>
			</tr>
			@endforelse
			<tr>
				<td align="right" colspan="6"><b> Total Penggajian </b></td>
				<td align="right"><b> Rp {{ number_format($total) }} </b></td>
			</tr>
		</tbody>
	</table>

</body>
</html>