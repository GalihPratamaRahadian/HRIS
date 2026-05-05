<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<style type="text/css">

		@page { margin: 0 20px; }
		body { margin: 0 20px; }
		
		body {
			font-size: 10pt;
			font-family: Arial, sans-serif;
		}

		table {
			border-collapse: collapse;
		}

	</style>
</head>
<body>

	<table width="100%">
		<td width="25%">
			<img src="{{ storage_path('system_files/logo.png') }}" style="max-width: 100px;">
		</td>
		<td width="50%">
			<h4 align="center"> PT. ADIVA SUMBER SOLUSI </h4>
			<p align="center"> SLIP GAJI KARYAWAN </p>
		</td>
		<td width="25%"></td>
	</table>

	<hr>

	<table>
		<tr>
			<td width="100"> NIK </td>
			<td width="10"> : </td>
			<td width="110"> {{ $employee->employee_number ?? '-' }} </td>
			<td width="50"> </td>
			<td width="100"> Departemen </td>
			<td width="10"> : </td>
			<td width="110"> {{ $employee->departmentName() }} </td>
		</tr>
		<tr>
			<td width="100"> Nama </td>
			<td width="10"> : </td>
			<td width="110"> {{ $employee->employee_name }} </td>
			<td width="50"> </td>
			<td width="100"> No Rek </td>
			<td width="10"> : </td>
			<td width="110"> {{ $employee->bank_name }} - {{ $employee->bank_account_number }} </td>
		</tr>
		<tr>
			<td width="100"> Jabatan </td>
			<td width="10"> : </td>
			<td width="110"> {{ $employee->positionName() }} </td>
			<td width="50"> </td>
			<td width="100"> Periode </td>
			<td width="10"> : </td>
			<td width="110"> {{ \Carbon\Carbon::createFromFormat('Y-m-d', $payroll->period_end)->isoFormat('MMMM Y') }} </td>
		</tr>
	</table>

	<hr>

	<table>
		<tr>
			<td width="100">
				<b> PENDAPATAN : </b>
			</td>
			<td width="10"></td>
			<td width="90" colspan="2"></td>
			<td width="50"> </td>
			<td width="100">
				<b> POTONGAN : </b>
			</td>
			<td width="10"></td>
			<td width="90" colspan="2"></td>
		</tr>
		<?php
			$maxIter = count($incomes) >= count($cuts) ? count($incomes) : count($cuts);
		?>
		@for($i = 0; $i < $maxIter; $i++)
		<tr>
			@if(count($incomes) > $i)
			<td width="90"> {{ $incomes[$i]->name }} </td>
			<td width="10"> : </td>
			<td width="10"> Rp </td>
			<td width="90" align="right"> {{ number_format($incomes[$i]->nominal) }} </td>
			@else
			<td width="50"> </td>
			<td width="90"> </td>
			<td width="10"> </td>
			<td width="10"> </td>
			<td width="90"> </td>
			@endif
			@if(count($cuts) > $i)
			<td width="50"> </td>
			<td width="90"> {{ $cuts[$i]->name }} </td>
			<td width="10"> : </td>
			<td width="10"> Rp </td>
			<td width="90" align="right"> {{ number_format($cuts[$i]->nominal) }} </td>
			@else
			<td width="50"> </td>
			<td width="90"> </td>
			<td width="10"> </td>
			<td width="10"> </td>
			<td width="90"> </td>
			@endif
		</tr>
		@endfor
	</table>

	<br>

	<hr>

	<table>
		<tr>
			<td width="100">
				<b> Total Pendapatan </b>
			</td>
			<td width="10"> : </td>
			<td width="10"> Rp </td>
			<td width="90" align="right"> {{ number_format($payroll->basic_salary + $payroll->total_allowance) }} </td>
			<td width="50"> </td>
			<td width="100">
				<b> Total Potongan </b>
			</td>
			<td width="10"> : </td>
			<td width="10"> Rp </td>
			<td width="90" align="right"> {{ number_format($payroll->total_cut + $payroll->basicSalaryCut()) }} </td>
		</tr>
	</table>

	<hr>

	<table>
		
		<tr>
			<td width="275"> </td>
			<td width="100">
				<b> Take Home Pay </b>
			</td>
			<td width="10">  : </td>
			<td width="10"><b> Rp </b></td>
			<td width="90" align="right"><b> {{ number_format($payroll->total) }} </b></td>
		</tr>

	</table>

</body>
</html>