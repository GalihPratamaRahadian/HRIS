<!DOCTYPE html>
<html>
<head>
	<title>Rekap Cuti</title>
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

	<div class="title">Rekap Cuti</div>

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
				<th>No</th>
				<th>Mulai Cuti</th>
                <th>Selesai Cuti</th>
				<th>Karyawan</th>
				<th>Departemen</th>
				<th>Status Pengajuan</th>
				<th>Alasan</th>
			</tr>
		</thead>
		<tbody>
			@forelse($leaves as $leave)
			<tr>
				<td align="center">{{ $loop->iteration }}</td>
				<td align="center">{{ $leave->startDateText() }}</td>
                <td align="center">{{  $leave->endDateText() }}</td>
				<td>{{ $leave->employee->employee_name ?? '-' }}</td>
				<td align="center">{{ $leave->employee->department->department_name ?? '-' }}</td>
				<td align="center">{{ $leave->statusText() ?? '-' }}</td>
				<td>{{ $leave->leaveReason->reason ?? '-' }}</td>
			</tr>
			@empty
			<tr>
				<td colspan="6" align="center">Kosong</td>
			</tr>
			@endforelse
		</tbody>
	</table>

	<p>Total Cuti: <b>{{ count($leaves) }}</b></p>

</body>
</html>
