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

	<div class="title"> Rekap Lembur </div>

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
				<th> Mulai Lembur </th>
				<th> Selesai Lembur </th>
                <th> Durasi Lembur </th>
				<th> Karyawan </th>
				<th> Departemen </th>
				<th> Jabatan </th>
				<th> Alasan </th>
				<th> Deskripsi </th>
				<th> Status Pengajuan </th>
			</tr>
		</thead>

		<tbody>
			@forelse($overtimes as $ov)
			<tr>
				<td align="center"> {{ $loop->iteration }} </td>
				<td align="center"> {{ $ov->startDateText().' '.date('H:i', strtotime($ov->clock_start)) }} </td>
				<td align="center"> {{ $ov->endDateText().' '.date('H:i', strtotime($ov->clock_end)) }} </td>
                <td align="center"> {{ $ov->amountClockStartToEnd() }} </td>
				<td> {{ $ov->employeeName() }} </td>
				<td> {{ $ov->departmentName() ?? '-' }} </td>
				<td> {{ $ov->positionName() }} </td>
				<td> {{ $ov->overtimeReasonText() }} </td>
				<td> {!! $ov->descriptionHtml() !!} </td>
				<td> {{ $ov->statusText() }} </td>
			</tr>
			@empty
			<tr>
				<td colspan="9" align="center">
					Kosong
				</td>
			</tr>
			@endforelse
		</tbody>
	</table>

	<p>
		Total Pengajuan Lembur : <b> {{ count($overtimes) }} </b>
	</p>

</body>
</html>
