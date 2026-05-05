<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<style type="text/css">

		* {
			font-family: 'Calibri', 'Arial', sans-serif;
		}
		
		.table {
			border-collapse: collapse;
			width: 100%;
		}

		.table td,
		.table th {
			border: 1px solid black;
			padding: 2px 5px;
		}

	</style>
</head>
<body>

	<img src="{{ storage_path('system_files/logo-certificate.png') }}" style="height: 80px; width: auto;">

	<h1 align="center">
		Hasil Course <br>
		{{ $course->course_title }}
	</h1>

	<table class="table">
		<thead>
			<tr>
				<th style="width: 50px;"> No </th>
				<th> Karyawan </th>
				<th> Departemen </th>
				<th style="width: 80px;"> Nilai </th>
				<th style="width: 150px;"> Status Kelulusan </th>
				<th style="width: 120px;"> Tgl Lulus </th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1; ?>
			@foreach($courseParticipants as $courseParticipant)
			<tr>
				<td align="center"> {{ $i++ }} </td>
				<td> {{ $courseParticipant->employeeName() }} </td>
				<td> {{ $courseParticipant->departmentName() }} </td>
				<td align="center"> {{ $courseParticipant->examScore() }} </td>
				<td align="center"> {{ $courseParticipant->isHavePassedText() }} </td>
				<td align="center"> {{ $courseParticipant->passedAtText() }} </td>
			</tr>
			@endforeach

			@foreach($notAccessed as $employee)
			<tr>
				<td align="center"> {{ $i++ }} </td>
				<td> {{ $employee->employee_name }} </td>
				<td> {{ $employee->departmentName() }} </td>
				<td align="center"> - </td>
				<td align="center"> Belum Mengakses </td>
				<td align="center"> - </td>
			</tr>
			@endforeach

			@if(count($courseParticipants) == 0 && count($notAccessed) == 0)
			<tr>
				<td colspan="6" align="center"> Kosong </td>
			</tr>
			@endif
		</tbody>
	</table>

</body>
</html>