@extends('template.backLayout')


@section('action')
@if(UserPermission::check('employee', 'u'))
@if($employee->isStatusActive())
<button class="btn btn-danger inactive-btn">
	<i class="mdi mdi-close"></i> Nonaktifkan Karyawan
</button>
@else
<button class="btn btn-success active-btn">
	<i class="mdi mdi-check"></i> Aktifkan Karyawan
</button>
@endif
@endif
@endsection


@section('content')
<div class="row">

	<div class="col-md-12">
		<ul class="nav nav-tabs tab-solid tab-solid-danger" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="tab-profile" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">
					Data Diri
				</a>
			</li>
			@if(UserPermission::check('employee', 'u'))
			<li class="nav-item">
				<a class="nav-link" id="tab-education" data-toggle="tab" href="#education" role="tab" aria-controls="education" aria-selected="false">
					Pendidikan
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="tab-training" data-toggle="tab" href="#training" role="tab" aria-controls="training" aria-selected="false">
					Pelatihan
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="tab-family" data-toggle="tab" href="#family" role="tab" aria-controls="family" aria-selected="false">
					Keluarga
				</a>
			</li>
			@endif
		</ul>

		<div class="tab-content tab-content-solid">
			
			<!-- PROFILE -->
			<div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile">
				<div class="row">

					<div class="col-lg-8">					
						<div class="card support-pane-card grid-margin">
							<div class="card-body">
								{!! Template::titleBanner($title) !!}

								<div class="table-responsive">
									<table class="table table-hover">

										<tr>
											<td> Nama Karyawan </td>
											<td> {{ $employee->employee_name }} </td>
										</tr>

										<tr>
											<td> Nomor Induk Karyawan </td>
											<td> {{ $employee->employeeNumber() }} </td>
										</tr>

										<tr>
											<td> Jenis Kelamin </td>
											<td> {{ $employee->genderText() }} </td>
										</tr>

										<tr>
											<td> Email </td>
											<td> 
												{!! $employee->email ? '<a href="mailto:'.$employee->email.'">'.$employee->email.'</a>' : '-' !!} </td>
										</tr>

										<tr>
											<td> Nomor Telepon </td>
											<td> 
												{!! $employee->phone_number ? '<a href="tel:'.$employee->phone_number.'">'.$employee->phone_number.'</a>' : '-' !!} </td>
										</tr>

										<tr>
											<td> Nomor Jamsostek </td>
											<td> {{ $employee->jamsostek ?? '-' }} </td>
										</tr>

										<tr>
											<td> Status Pekerjaan </td>
											<td> {{ $employee->jobStatusText() }} </td>
										</tr>

										<tr>
											<td> Status Keaktifan Karyawan </td>
											<td> 
												{!! $employee->statusHtml() !!} </td>
										</tr>

										<tr>
											<td> Departemen </td>
											<td> {{ $employee->departmentName() }} </td>
										</tr>

										<tr>
											<td> Jabatan </td>
											<td> {{ $employee->positionName() }} </td>
										</tr>

										<tr>
											<td> Grup Karyawan </td>
											<td> {{ $employee->employeeGroupName() }} </td>
										</tr>

										<tr>
											<td> Jam Kerja </td>
											<td>
												@if($shift = $employee->shift)
												<a href="{{ route('admin.shift.detail', $employee->id_shift) }}">
													{{ $employee->shiftName() }}
													({{ date('H:i', strtotime($shift->clock_start)) }} - {{ date('H:i', strtotime($shift->clock_end)) }})
												</a>
												@else
												-
												@endif
											</td>
										</tr>

										<tr>
											<td> Tanggal Mulai Bekerja </td>
											<td> {{ $employee->start_working_date ?? '-' }} </td>
										</tr>

										<tr>
											<td> Tempat, Tanggal Lahir </td>
											<td> {{ $employee->place_of_birth ?? '-' }}, {{ $employee->date_of_birth ?? '-' }} </td>
										</tr>

										<tr>
											<td> Alamat </td>
											<td> {{ $employee->address ?? '-' }} </td>
										</tr>

										<tr>
											<td> Pendidikan Terakhir </td>
											<td> {{ $employee->last_education ?? '-' }} </td>
										</tr>

										<tr>
											<td> Jurusan Pendidikan Terakhir </td>
											<td> {{ $employee->last_education_major ?? '-' }} </td>
										</tr>

										<tr>
											<td> Status Pernikahan </td>
											<td> {{ $employee->marital_status ?? '-' }} </td>
										</tr>

										<tr>
											<td> Golongan Darah </td>
											<td> {{ $employee->blood_type ?? '-' }} </td>
										</tr>

										<tr>
											<td> No KTP </td>
											<td> {{ $employee->ktp_number ?? '-' }} </td>
										</tr>

										<tr>
											<td> No NPWP </td>
											<td> {{ $employee->npwp_number ?? '-' }} </td>
										</tr>

									</table>
								</div>

							</div>
						</div>
					</div>

					<div class="col-lg-4">
						<div class="card support-pane-card grid-margin">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center mb-3">
									{!! Template::titleBanner('Foto Karyawan') !!}
									@if(UserPermission::check('employee', 'u'))
									<a href="{{ route('employee.change_photo', $employee->id) }}">
										<small>
											<i class="mdi mdi-pencil"></i> Ganti Foto
										</small>
									</a>
									@endif
								</div>

								<div>
									<img src="{{ $employee->photoLink() }}" class="img-fluid">
								</div>

							</div>
						</div>
					</div>

				</div>
			</div>
			<!-- END PROFILE -->


			<!-- EDUCATION -->
			<div class="tab-pane fade" id="education" role="tabpanel" aria-labelledby="education">
				<div class="card support-pane-card grid-margin">
					<div class="card-body">

						<div class="d-flex justify-content-between align-items-center mb-3">
							{!! Template::titleBanner('Pendidikan Karyawan - '.$employee->employee_name) !!}
							<div class="btn-toolbar mb-0 d-none d-sm-block" role="toolbar">

								<a href="{{ route('employee_education.create', $employee->id) }}" class="btn btn-success">
									<i class="mdi mdi-plus-thick"></i> Tambah
								</a>

							</div>
						</div>
						
						<div class="table-responsive">
							<table class="table table-bordered table-hover" id="educationTable">
								<thead>
									<tr>
										<th> Tingkat Pendidikan </th>
										<th> Nama Sekolah/Kampus/Universitas </th>
										<th> Jurusan </th>
										<th> Tahun Awal </th>
										<th> Tahun Akhir </th>
										<th width="100"> Aksi </th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>

					</div>
				</div>
			</div>
			<!-- END EDUCATION -->


			<!-- TRAINING -->
			<div class="tab-pane fade" id="training" role="tabpanel" aria-labelledby="training">
				<div class="card support-pane-card grid-margin">
					<div class="card-body">

						<div class="d-flex justify-content-between align-items-center mb-3">
							{!! Template::titleBanner('Pelatihan Karyawan - '.$employee->employee_name) !!}
							<div class="btn-toolbar mb-0 d-none d-sm-block" role="toolbar">

								<a href="{{ route('employee_training.create', $employee->id) }}" class="btn btn-success">
									<i class="mdi mdi-plus-thick"></i> Tambah
								</a>

							</div>
						</div>
						
						<div class="table-responsive">
							<table class="table table-bordered table-hover" id="trainingTable">
								<thead>
									<tr>
										<th> Nama Pelatihan </th>
										<th> Provider </th>
										<th> Tanggal Awal </th>
										<th> Tanggal Akhir </th>
										<th> Keterangan </th>
										<th> File </th>
										<th width="100"> Aksi </th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>

					</div>
				</div>
			</div>
			<!-- END TRAINING -->



			<!-- FAMILY -->
			<div class="tab-pane fade" id="family" role="tabpanel" aria-labelledby="family">
				<div class="card support-pane-card grid-margin">
					<div class="card-body">

						<div class="d-flex justify-content-between align-items-center mb-3">
							{!! Template::titleBanner('Keluarga Karyawan - '.$employee->employee_name) !!}
							<div class="btn-toolbar mb-0 d-none d-sm-block" role="toolbar">

								<a href="{{ route('employee_family.create', $employee->id) }}" class="btn btn-success">
									<i class="mdi mdi-plus-thick"></i> Tambah
								</a>

							</div>
						</div>
						
						<div class="table-responsive">
							<table class="table table-bordered table-hover" id="familyTable">
								<thead>
									<tr>
										<th> Nama </th>
										<th> Status Hubungan </th>
										<th> Tempat Lahir </th>
										<th> Tanggal Lahir </th>
										<th width="100"> Aksi </th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>

					</div>
				</div>
			</div>
			<!-- END FAMILY -->

		</div>
	</div>


</div>
@endsection


@section('script')
<script type="text/javascript">

	$(function(){
	
		@if(UserPermission::check('employee', 'u'))
		$('#educationTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('employee_education', $employee->id) }}"
			},
			columns : [
				{
					data : "education_level",
					name : 'education_level'
				},
				{
					data : "school_name",
					name : 'school_name'
				},
				{
					data : "major_name",
					name : 'major_name'
				},
				{
					data : "year_start",
					name : 'year_start'
				},
				{
					data : "year_end",
					name : 'year_end'
				},
				{
					data : 'action',
					name : 'action',
					orderable : false,
					searchable : false,
				}
			],
			drawCallback : settings => {
				renderEvent();
			}
		})


		$('#trainingTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('employee_training', $employee->id) }}"
			},
			columns : [
				{
					data : "training_name",
					name : 'training_name'
				},
				{
					data : "provider",
					name : 'provider'
				},
				{
					data : "date_start",
					name : 'date_start'
				},
				{
					data : "date_end",
					name : 'date_end'
				},
				{
					data : "description",
					name : 'description'
				},
				{
					data : "file",
					name : 'file'
				},
				{
					data : 'action',
					name : 'action',
					orderable : false,
					searchable : false,
				}
			],
			drawCallback : settings => {
				renderEvent();
			}
		})


		$('#familyTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('employee_family', $employee->id) }}"
			},
			columns : [
				{
					data : "name",
					name : 'name'
				},
				{
					data : "relationship_status",
					name : 'relationship_status'
				},
				{
					data : "place_of_birth",
					name : 'place_of_birth'
				},
				{
					data : "date_of_birth",
					name : 'date_of_birth'
				},
				{
					data : 'action',
					name : 'action',
					orderable : false,
					searchable : false,
				}
			],
			drawCallback : settings => {
				renderEvent();
			}
		})


		const reloadDT = () => {
			$('#educationTable').DataTable().ajax.reload();
			$('#trainingTable').DataTable().ajax.reload();
			$('#familyTable').DataTable().ajax.reload();
		}


		const renderEvent = () => {
			$('.delete').off('click')
			$('.delete').on('click', function(){
				let href = $(this).data('href')
				confirmation('Yakin ingin dihapus?', () => {
					ajaxSetup();
					$.ajax({
						url : href,
						method : 'delete',
					})
					.done(response => {
						ajaxSuccessHandling(response);
						reloadDT();
					})
					.fail(error => {
						ajaxErrorHandling(error)
					})
				})
			});
		}

		$(`.active-btn`).on('click', function(){
			confirmation(`Yakin ingin aktifkan karyawan atas nama {{ $employee->employee_name }}`, () => {
				ajaxSetup();
				$.ajax({
					url : `{{ route('employee.active', $employee->id) }}`,
					method : 'post',
				})
				.done(response => {
					ajaxSuccessHandling(response);
					setTimeout(() => {
						window.location.reload();
					}, 1000)
				})
				.fail(error => {
					ajaxErrorHandling(error)
				})
			})
		})

		$(`.inactive-btn`).on('click', function(){
			confirmation(`Yakin ingin menonaktifkan karyawan atas nama {{ $employee->employee_name }}`, () => {
				ajaxSetup();
				$.ajax({
					url : `{{ route('employee.inactive', $employee->id) }}`,
					method : 'post',
				})
				.done(response => {
					ajaxSuccessHandling(response);
					setTimeout(() => {
						window.location.reload();
					}, 1000)
				})
				.fail(error => {
					ajaxErrorHandling(error)
				})
			})
		})

		@endif
	})

</script>

@if(isset($_GET['tab']))
<script type="text/javascript">
	$(`#tab-{{ $_GET['tab'] }}`).click()
</script>
@endif
@endsection