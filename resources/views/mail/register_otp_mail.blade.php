<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>

	<h1> Pendaftaran Karyawan Baru </h1>
	<hr>
	<p>
		Pendaftaran karyawan baru a.n {{ $registrant->employee_name }} <br>
		Harap untuk segera melengkapi isian formulir dengan login ke {{ route('login') }} dengan menggunakan akun berikut :
	</p>
	<table>
		<tr>
			<td width="70"> Username </td>
			<td> : </td>
			<td> {{ $username }} </td>
		</tr>
		<tr>
			<td width="70"> Password </td>
			<td> : </td>
			<td> {{ $password }} </td>
		</tr>
	</table>

</body>
</html>