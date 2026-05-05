@extends('template.backLayout')


@section('style')
<style type="text/css">

	.profile-table {
		width: 100%;
		border-collapse: collapse;
	}

	.profile-table td {
		vertical-align: top;
		padding: 10px 20px;
	}
		
	.profile-table tbody tr:first-child td {
		border-top: 1px solid black;
		border-bottom: 1px solid black;
	}

	.profile-table tbody tr td:first-child {
		font-weight: bold;
		text-align: right;
	}

	.profile-table tbody tr:last-child td {
		padding-bottom: 50px;
	}

</style>
@endsection


@section('action')
<a href="{{ route('emp.personal_profile_download') }}" class="btn btn-success" target="_blank">
	<i class="mdi mdi-download"></i> Download
</a>
@endsection


@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">
				
				<div class="table-responsive">
					<table class="profile-table">

						<!-- Nama dan Jabatan -->
						<thead>
							<tr>
								<td width="200">  </td>
								<td>
									<h3> {{ employee()->employee_name }} </h3>
									<p> {{ employee()->positionName() }} </p>
								</td>
							</tr>
						</thead>
						<!-- End Nama dan Jabatan -->


						<!-- Pribadi -->
						<tbody>
							<tr>
								<td> Pribadi </td>
								<td></td>
							</tr>
							<tr>
								<td> TTL </td>
								<td> {{ employee()->place_of_birth }}, {{ employee()->dateOfBirthText('d M Y') }} </td>
							</tr>
							<tr>
								<td> Status Nikah </td>
								<td> {{ employee()->marital_status }} </td>
							</tr>
							<!-- <tr>
								<td> Agama </td>
								<td> </td>
							</tr> -->
						</tbody>
						<!-- End Pribadi -->


						<!-- Alamat -->
						<tbody>
							<tr>
								<td> Alamat </td>
								<td></td>
							</tr>
							<tr>
								<td> Alamat Domisili </td>
								<td> {{ employee()->address }} </td>
							</tr>
							<!-- <tr>
								<td> Alamat KTP </td>
								<td> </td>
							</tr> -->
						</tbody>
						<!-- End Alamat -->


						<!-- Keluarga -->
						<tbody>
							<tr>
								<td> Keluarga </td>
								<td></td>
							</tr>
							@foreach(employee()->employeeFamilies as $family)
							<tr>
								<td> {{ $family->relationship_status }} </td>
								<td>
									{{ $family->name }} <br>
									{{ $family->place_of_birth }}, {{ $family->dateOfBirthText('d M Y') }}
								</td>
							</tr>
							@endforeach
						</tbody>
						<!-- End Keluarga -->


						<!-- Pendidikan -->
						<tbody>
							<tr>
								<td> Pendidikan </td>
								<td></td>
							</tr>
							@foreach(employee()->employeeEducations as $education)
							<tr>
								<td> {{ $education->education_level }} </td>
								<td>
									{{ $education->major_name }} - {{ $education->school_name }} <br>
									{{ $education->year_start }} - {{ $education->year_end }}
								</td>
							</tr>
							@endforeach
						</tbody>
						<!-- End Pendidikan -->


						<!-- Pelatihan -->
						<tbody>
							<tr>
								<td> Pelatihan </td>
								<td></td>
							</tr>
							@foreach(employee()->employeeTrainings as $training)
							<tr>
								<td> {{ $training->date_start }} - {{ $training->date_end }} </td>
								<td> {{ $training->training_name }} </td>
							</tr>
							@endforeach
						</tbody>
						<!-- End Pelatihan -->

					</table>
					
				</div>

			</div>
		</div>
	</div>
</div>
@endsection