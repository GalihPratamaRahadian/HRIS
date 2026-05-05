<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<style type="text/css">

		@page { margin: 0px; }
		body {
			margin: 0px;
			background-image: url("{{ storage_path('system_files/bg-certificate.jpg') }}");
			background-size: cover;
			font-size: 16pt;
		}
		
		* {
			font-family: Arial, sans-serif;
		}

		h1 {
			color: #2e3092;
			font-size: 36pt;
			font-weight: bolder;
		}

		.employee-name {
			text-align: center;
			font-size: 30pt;
			font-weight: bolder;
			margin-bottom: 0px;
		}

		.department-name {
			text-align: center;
			margin-top: 0px;
			font-weight: bold;
		}

		.course-title {
			text-align: center;
			font-weight: bold;
			font-size: 24pt;
			margin-bottom: 0px;
		}

	</style>
</head>
<body>

	<br> <br> <br>

	<h1 align="center"> SERTIFIKAT E-LEARNING </h1>

	<p align="center"> Diberikan Kepada </p>

	<p class="employee-name"> {{ $participant->employeeName() }} </p>

	<p class="department-name"> {{ $participant->departmentName() }} </p>

	<h4 align="center" style="margin-bottom: 5px;"> Atas Partisipasinya Mengikuti dan Menyelesaikan E-Training </h4>

	<p align="center" style="margin-top: 0px;"> Dengan Tema </p>

	<p class="course-title"> "{{ $participant->courseTitle() }}" </p>

	<br>

	<p align="center"> Tanggal : {{ $participant->startedAt('d F Y') }} </p>
	<p align="center"><b> Human Resources Development </b></p>
</body>
</html>