<!DOCTYPE html>
<html>
<head>
	<title></title>

	<style type="text/css">
		table {
			border-collapse: collapse;
		}
	</style>

</head>
<body>
	<h1 align="center"> Kehadiran </h1>
	<hr>

	<table style="width: 100%" border="1">
		<thead>
			<tr>
				<th> Tanggal </th>
				<th> Karyawan </th>
				<th> Departemen </th>
				<th> Jam Datang </th>
				<th> Jam Keluar </th>
				<th> Terlambat </th>
				<th> Status </th>
			</tr>
		</thead>
		<tbody>
			
			@foreach($attendances as $attendance)
			<tr>
				<td> {{ $attendance->dateText() }} </td>
				<td> {{ $attendance->employeeName() }} </td>
				<td> {{ $attendance->departmentName() }} </td>
				<td> {{ $attendance->clockInTextFull() }} </td>
				<td> {{ $attendance->clockOutTextFull() }} </td>
				<td> {{ $attendance->lateText() }} </td>
				<td> {!! $attendance->typeText() !!} </td>
			</tr>
			@endforeach

		</tbody>
	</table>

</body>
</html>