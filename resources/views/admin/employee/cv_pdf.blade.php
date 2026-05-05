<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title> CV </title>

	<style type="text/css">

		body {
			font-family: Calibri, Arial, sans-serif;
			font-size: 8pt;
		}

		table {
			/*width: 100%;*/
			border-collapse: collapse;
		}

		td {
			vertical-align: top;
			padding: 10px 20px;
		}
		
		tbody tr:first-child td {
			border-top: 1px solid black;
			border-bottom: 1px solid black;
		}

		tbody tr td:first-child {
			font-weight: bold;
			text-align: right;
		}

		tbody tr:last-child td {
			padding-bottom: 50px;
		}

	</style>
</head>
<body>

	<table>
		
		<thead>
			<tr>
				<td style="width: 200px;"></td>
				<td style="width: 420px;">
					<h2> {{ $employee->employee_name }} </h2>
					<p> {{ $employee->positionName() }} </p>
				</td>
			</tr>
		</thead>

		<!-- PRIBADI -->
		<tbody>
			<tr>
				<td> Pribadi </td>
				<td></td>
			</tr>
			<tr>
				<td> TTL </td>
				<td> {{ $employee->place_of_birth }}, {{ $employee->dateOfBirthText('d M Y') }} </td>
			</tr>
			<tr>
				<td> Status Nikah </td>
				<td> {{ $employee->marital_status }} </td>
			</tr>
			<!-- <tr>
				<td> Agama </td>
				<td> </td>
			</tr> -->
		</tbody>

		<!-- ALAMAT -->
		<tbody>
			<tr>
				<td> Alamat </td>
				<td></td>
			</tr>
			<tr>
				<td> Alamat Domisili </td>
				<td> {{ $employee->address }} </td>
			</tr>
			<!-- <tr>
				<td> Alamat KTP </td>
				<td> </td>
			</tr> -->
		</tbody>


		<!-- KELUARGA -->
		<tbody>
			<tr>
				<td> Keluarga </td>
				<td></td>
			</tr>
			@foreach($employee->employeeFamilies as $family)
			<tr>
				<td> {{ $family->relationship_status }} </td>
				<td>
					{{ $family->name }} <br>
					{{ $family->place_of_birth }}, {{ $family->dateOfBirthText('d M Y') }}
				</td>
			</tr>
			@endforeach
		</tbody>

		<!-- PENDIDIKAN -->
		<tbody>
			<tr>
				<td> Pendidikan </td>
				<td></td>
			</tr>
			@foreach($employee->employeeEducations as $education)
			<tr>
				<td> {{ $education->education_level }} </td>
				<td>
					{{ $education->major_name }} - {{ $education->school_name }} <br>
					{{ $education->year_start }} - {{ $education->year_end }}
				</td>
			</tr>
			@endforeach
		</tbody>

		<!-- PELATIHAN -->
		<tbody>
			<tr>
				<td> Pelatihan </td>
				<td></td>
			</tr>
			@foreach($employee->employeeTrainings as $training)
			<tr>
				<td> {{ $training->date_start }} - {{ $training->date_end }} </td>
				<td> {{ $training->training_name }} </td>
			</tr>
			@endforeach
		</tbody>

	</table>



</body>
</html>