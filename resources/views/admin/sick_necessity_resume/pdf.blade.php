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

	<div class="title"> Rekap Izin dan Sakit </div>

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
				<th> Tanggal Awal Pengajuan </th>
				<th> Selesai Pengajuan </th>
				<th> Karyawan </th>
				<th> Departemen </th>
				<th> Jabatan </th>
				<th> Jenis Pengajuan </th>
				<th> Alasan Izin </th>
				<th> Alasan Sakit </th>
				<th> Status Pengajuan </th>
			</tr>
		</thead>

		<tbody>
			@forelse($sickNecessities as $sickNecessity)
			<tr>
				<td align="center"> {{ $loop->iteration }} </td>
				<td align="center"> {{ $sickNecessity->startDateText() }} </td>
				<td align="center"> {{ $sickNecessity->endDateText() }} </td>
				<td> {{ $sickNecessity->employeeName() }} </td>
				<td> {{ $sickNecessity->departmentName() }} </td>
				<td> {{ $sickNecessity->positionName() }} </td>
				<td> {{ $sickNecessity->typeText() }} </td>
				<td> {{ $sickNecessity->necessityReasonText() }} </td>
				<td> {{ $sickNecessity->sickReasonText() }} </td>
				<td> {{ $sickNecessity->statusText() }} </td>
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
		Total Pengajuan Sakit : <b> {{ $sickNecessities->where('type', 'Sakit')->count() }} </b>
	</p>
	<p>
		Total Pengajuan Izin : <b> {{ $sickNecessities->where('type', 'Izin')->count() }} </b>
	</p>
</body>
</html>