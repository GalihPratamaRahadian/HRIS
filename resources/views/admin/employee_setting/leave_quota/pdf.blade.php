<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		* {
			font-family: Calibri, Arial, sans-serif;
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
			vertical-align: top;
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

	<div class="title"> Rekap Sisa Cuti </div>

	<br>


	<table class="table">
		<thead>
			<tr>
				<th> No </th>
				<th> Karyawan </th>
				<th> Departemen </th>
				<th> Jabatan </th>
				<th> Periode Reset Cuti </th>
				<th> Jatah Cuti Per Periode </th>
				<th> Potongan Cuti Bersama </th>
				<th> Sisa Jatah Cuti (Belum Potong Cuti Bersama) </th>
			</tr>
		</thead>

		<tbody>
			@forelse($quotas as $quota)
			<tr>
				<td align="center"> {{ $loop->iteration }} </td>
				<td> {{ $quota->employeeName() }} </td>
				<td align="center"> {{ $quota->employee->departmentName() }} </td>
				<td align="center"> {{ $quota->employee->positionName() }} </td>
				<td align="center"> {{ $quota->periodTypeText() }} </td>
				<td align="center"> {{ $quota->quota }} </td>
				<td align="center"> {{ $quota->mass_leave_cut }} </td>
				<td align="center"> {{ $quota->quota_available }} </td>
			</tr>
			@empty
			<tr>
				<td colspan="8" align="center">
					Kosong
				</td>
			</tr>
			@endforelse
		</tbody>
	</table>

</body>
</html>