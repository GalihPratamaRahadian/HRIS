@extends('template.backLayout')


@section('style')
<style type="text/css">
	html, body {
		scroll-behavior: smooth;
	}

	h4 {
		scroll-margin-top: 6rem;
	}
</style>
@endsection


@section('content')
@if(UserPermission::check('employee', 'r'))
@if( ($amount = \App\Models\Employee::amountOfActiveEmployeeWithNoHaveShift()) > 0)
<div class="grid-margin">
	<?php 
		$anchor = '<a href="'.route('employee', [ 'id_shift' => 'no' ]).'"> Disini </a>';
	?>
	{!! \App\Models\Setting::alertDangerBanner($amount.' Karyawan Belum Memiliki Shift. Cek '.$anchor) !!}
</div>
@endif
@endif

@if(UserPermission::check('employee', 'r'))
@if( ($amount = \App\Models\Employee::amountOfActiveEmployeeWithNoHaveDepartment()) > 0)
<div class="grid-margin">
	<?php 
		$anchor = '<a href="'.route('employee', [ 'id_department' => 'no' ]).'"> Disini </a>';
	?>
	{!! \App\Models\Setting::alertDangerBanner($amount.' Karyawan Belum Memiliki Departemen. Cek '.$anchor) !!}
</div>
@endif
@endif

@if(UserPermission::check('employee_contract', 'r'))
@if(($amount = count($employeeWithContractIsAlmostOver)) > 0)
<div class="grid-margin">
	{!! \App\Models\Setting::alertDangerBanner($amount.' Karyawan akan habis kontrak. Cek <a href="#employeeWithContractIsAlmostOver"> disini </a>') !!}
</div>
@endif
@endif

@if(UserPermission::check('employee_contract', 'r'))
@if(($amount = count($employeeWithDoesntHaveContract)) > 0)
<div class="grid-margin">
	{!! \App\Models\Setting::alertDangerBanner($amount.' Karyawan tidak memiliki kontrak. Cek <a href="#employeeWithDoesntHaveContract"> disini </a>') !!}
</div>
@endif
@endif

@if(UserPermission::check('leave_submission', 'r'))
@if(($pending = \App\Models\LeaveSubmission::amountOfLeaveSubmissionsWithStatusPending()) > 0)
<div class="alert alert-primary">
	Ada {{ $pending }} pengajuan cuti menunggu persetujuan. Cek <a href="{{ route('admin.leave_submission') }}?status=wait"> disini </a> untuk melihat.
</div>
@endif
@endif

@if(UserPermission::check('sick_necessity_submission', 'r'))
@if(($pending = \App\Models\SickNecessitySubmission::amountOfSickNecessitySubmissionsWithStatusPending()) > 0)
<div class="alert alert-primary">
	Ada {{ $pending }} pengajuan izin/sakit menunggu persetujuan. Cek <a href="{{ route('admin.sick_necessity_submission') }}?status=wait"> disini </a> untuk melihat.
</div>
@endif
@endif

@if(UserPermission::check('attendance_permission_submission', 'r'))
@if(($pending = \App\Models\AttendancePermissionSubmission::amountOfAttendancePermissionSubmissionsWithStatusPending()) > 0)
<div class="alert alert-primary">
	Ada {{ $pending }} pengajuan izin terlambat/pulang cepat menunggu persetujuan. Cek <a href="{{ route('admin.attendance_permission_submission') }}?status=wait"> disini </a> untuk melihat.
</div>
@endif
@endif

@if(UserPermission::check('overtime_submission', 'r'))
@if(($pending = \App\Models\OvertimeSubmission::amountOfOvertimeSubmissionsWithStatusPending()) > 0)
<div class="alert alert-primary">
	Ada {{ $pending }} pengajuan lembur menunggu persetujuan. Cek <a href="{{ route('admin.overtime_submission') }}?status=wait"> disini </a> untuk melihat.
</div>
@endif
@endif

<div class="row mb-3">
	<div class="col-md-3 grid-margin">
		<div class="card bg-primary">
			<div class="card-body p-3 align-items-center text-white">
				<div class="card-icon" style="font-size: 48px;">
					<i class="mdi mdi-account-tie"></i>
				</div>
				<div class="d-inline-block ml-5">
					<h5> Karyawan </h5>
					<span> {{ \App\Models\Employee::count() }}  Orang</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3 grid-margin">
		<div class="card bg-success">
			<div class="card-body p-3 align-items-center text-white">
				<div class="card-icon" style="font-size: 48px;">
					<i class="mdi mdi-office-building"></i>
				</div>
				<div class="d-inline-block ml-5">
					<h5>Departemen</h5>
					<span> {{ \App\Models\Department::count() }} </span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3 grid-margin">
		<div class="card bg-warning">
			<div class="card-body p-3 align-items-center text-white">
				<div class="card-icon" style="font-size: 48px;">
					<i class="mdi mdi-account"></i>
				</div>
				<div class="d-inline-block ml-5">
					<h5>Pengunjung terdaftar</h5>
					<span> 0 </span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3 grid-margin">
		<div class="card bg-danger">
			<div class="card-body p-3 align-items-center text-white">
				<div class="card-icon" style="font-size: 48px;">
					<i class="mdi mdi-account-group"></i>
				</div>
				<div class="d-inline-block ml-5">
					<h5> Banyak Pengunjung </h5>
					<span> {{ \App\Models\FaceTerminalLog::amountOfLogsToday() }} </span>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row mb-3 grid-margin">

	<div class="col-lg-12">
		<ul class="nav nav-tabs tab-solid tab-solid-primary" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="tab-cuti-libur" data-toggle="tab" href="#hadir" role="tab" aria-controls="hadir" aria-selected="true">
					Kehadiran
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="tab-belum-hadir" data-toggle="tab" href="#belum-hadir" role="tab" aria-controls="belum-hadir" aria-selected="false">
					Belum Hadir
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="tab-cuti-libur" data-toggle="tab" href="#cuti-libur" role="tab" aria-controls="cuti-libur" aria-selected="false">
					Cuti & Libur
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="tab-izin-sakit" data-toggle="tab" href="#izin-sakit" role="tab" aria-controls="izin-sakit" aria-selected="false">
					Izin & Sakit
				</a>
			</li>
		</ul>

		<div class="tab-content tab-content-solid">
			
			<!-- HADIR -->
			<div class="tab-pane fade show active" id="hadir" role="tabpanel" aria-labelledby="hadir">
				<div class="card">
					<div class="card-body align-items-center">
						
						{!! Template::titleBanner('Kehadiran Hari Ini | '.date('d M Y')) !!}

						<div class="table-responsive">
							<table class="table table-hover table-bordered" id="hadir-table">
								<thead>
									<tr>
										<th> Nama Karyawan </th>
										<th> Jam Datang </th>
										<th> Jam Keluar </th>
										<th> Terlambat </th>
										<th> Aksi </th>
									</tr>
								</thead>
								<tbody>
									
									@foreach($todayAttendances as $attendance)
									@if($attendance->isTypeHadir())
									<tr>
										<td>
											<a href="{{ route('employee.detail', $attendance->id_employee) }}">
												{{ $attendance->employeeName() }}
											</a>
										</td>
										<td> {{ $attendance->clockInText() }} </td>
										<td> {{ $attendance->clockOutText() }} </td>
										<td> {{ $attendance->lateText() }} </td>
										<td>
											<a href="{{ route('attendance.detail', $attendance->id) }}">
												<i class="mdi mdi-magnify"></i> Detail
											</a>
										</td>
									</tr>
									@endif
									@endforeach

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<!-- BELUM HADIR -->
			<div class="tab-pane fade" id="belum-hadir" role="tabpanel" aria-labelledby="belum-hadir">
				<div class="card">
					<div class="card-body align-items-center">
						{!! Template::titleBanner('Belum Hadir Hari Ini | '.date('d M Y')) !!}

						<div class="table-responsive">
							<table class="table table-hover table-bordered" id="belum-hadir-table">
								<thead>
									<tr>
										<th> Nama Karyawan </th>
										<th> Jam Kerja </th>
										<th style="width: 80px;"> Aksi </th>
									</tr>
								</thead>
								<tbody>
									
									@foreach($waitingForClockInEmployees as $employee)
									<tr>
										<td>
											<a href="{{ route('employee.detail', $employee->id) }}">
												{{ $employee->employee_name }}
											</a>
										</td>
										@if($employee->isShiftTypeRoutine())
										<td>
											@if($shift = $employee->shift)
											{{ $shift->clock_start }} - {{ $shift->clock_end }}
											@endif 
											({{ $employee->shiftName() }}) 
										</td>
										@else
										<td>
											@if($shift = $employee->getTodayShift())
											{{ $shift->clock_start }} - {{ $shift->clock_end }}
											@endif 
											(Harian)
										</td>
										@endif
										<td>
											<div class="dropdown">
												<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												Aksi
												</button>
												<div class="dropdown-menu">
													<a class="dropdown-item" href="{{ route('admin.send_message_to_employee') }}?id_employee={{ $employee->id }}" title="Kirim Pesan">
														<i class="mdi mdi-email"></i> Kirim Pesan 
													</a>
												</div>
											</div>
										</td>
									</tr>
									@endforeach

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<!-- CUTI & LIBUR -->
			<div class="tab-pane fade" id="cuti-libur" role="tabpanel" aria-labelledby="cuti-libur">
				<div class="card">
					<div class="card-body align-items-center">
						{!! Template::titleBanner('Cuti & Libur Hari Ini | '.date('d M Y')) !!}

						<div class="table-responsive">
							<table class="table table-hover table-bordered" id="cuti-libur-table">
								<thead>
									<tr>
										<th> Nama Karyawan </th>
										<th> Keterangan </th>
										<th> Aksi </th>
									</tr>
								</thead>
								<tbody>
									
									@foreach($todayAttendances as $attendance)
									@if($attendance->isTypeCuti() || $attendance->isTypeLibur())
									<tr>
										<td>
											<a href="{{ route('employee.detail', $attendance->id_employee) }}">
												{{ $attendance->employeeName() }}
											</a>
										</td>
										<td> {!! $attendance->typeHtml() !!} </td>
										<td>
											<a href="{{ route('attendance.detail', $attendance->id) }}">
												<i class="mdi mdi-magnify"></i> Detail
											</a>
										</td>
									</tr>
									@endif
									@endforeach

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<!-- IZIN & SAKIT -->
			<div class="tab-pane fade" id="izin-sakit" role="tabpanel" aria-labelledby="izin-sakit">
				<div class="card">
					<div class="card-body align-items-center">
						{!! Template::titleBanner('Izin & Sakit Hari Ini | '.date('d M Y')) !!}

						<div class="table-responsive">
							<table class="table table-hover table-bordered" id="izin-sakit-table">
								<thead>
									<tr>
										<th> Nama Karyawan </th>
										<td> Keterangan </td>
										<th> Aksi </th>
									</tr>
								</thead>
								<tbody>
									
									@foreach($todayAttendances as $attendance)
									@if($attendance->isTypeIzin() || $attendance->isTypeSakit())
									<tr>
										<td>
											<a href="{{ route('employee.detail', $attendance->id_employee) }}">
												{{ $attendance->employeeName() }}
											</a>
										</td>
										<td> {!! $attendance->typeHtml() !!} </td>
										<td>
											<a href="{{ route('attendance.detail', $attendance->id) }}">
												<i class="mdi mdi-magnify"></i> Detail
											</a>
										</td>
									</tr>
									@endif
									@endforeach

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>

<div class="row mb-3 grid-margin">
	<div class="col-md-12">
		<div class="card grid-margin">
			<div class="card-body align-items-center">
				@if(($amount = count($employeeWithContractIsAlmostOver)) > 0)
				{!! Template::titleBanner('Kontrak Hampir Habis <span class="badge badge-danger" id="employeeWithContractIsAlmostOver">'. $amount.'</span>') !!}
				@else
				{!! Template::titleBanner('Kontrak Hampir Habis') !!}
				@endif

				<div class="table-responsive">
					<table class="table table-hover table-bordered dataTable">
						<thead>
							<tr>
								<th> Nama Karyawan </th>
								<th> Departemen </th>
								<th> Jabatan </th>
								<th> Tanggal Habis Kontrak </th>
								<th width="100"> Aksi </th>
							</tr>
						</thead>
						<tbody>
							
							@foreach($employeeWithContractIsAlmostOver as $employee)
							<tr>
								<td>
									<a href="{{ route('employee.detail', $employee->id) }}">
										{{ $employee->employee_name }}
									</a>
								</td>
								<td> {{ $employee->departmentName() }} </td>
								<td> {{ $employee->positionName() }} </td>
								<td> {{ $employee->activeEmployeeContract->endDateText('d M Y') }} </td>
								<td>
									<a href="{{ route('employee_contract.create', [ 'id_employee' => $employee->id ]) }}">
										<i class="mdi mdi-plus-thick"></i> Buat Kontrak
									</a>
								</td>
							</tr>
							@endforeach

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-12">
		<div class="card grid-margin">
			<div class="card-body align-items-center">
				@if(($amount = count($employeeWithDoesntHaveContract)) > 0)
				{!! Template::titleBanner('Tidak Punya Kontrak <span class="badge badge-danger" id="employeeWithDoesntHaveContract">'. $amount.'</span>') !!}
				@else
				{!! Template::titleBanner('Tidak Punya Kontrak') !!}
				@endif

				<div class="table-responsive">
					<table class="table table-hover table-bordered dataTable">
						<thead>
							<tr>
								<th> Nama Karyawan </th>
								<th> Departemen </th>
								<th> Jabatan </th>
								<th width="100"> Aksi </th>
							</tr>
						</thead>
						<tbody>
							
							@foreach($employeeWithDoesntHaveContract as $employee)
							<tr>
								<td>
									<a href="{{ route('employee.detail', $employee->id) }}">
										{{ $employee->employee_name }}
									</a>
								</td>
								<td> {{ $employee->departmentName() }} </td>
								<td> {{ $employee->positionName() }} </td>
								<td>
									<a href="{{ route('employee_contract.create', [ 'id_employee' => $employee->id ]) }}">
										<i class="mdi mdi-plus-thick"></i> Buat Kontrak
									</a>
								</td>
							</tr>
							@endforeach

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection


@section('script')
<script type="text/javascript">
	$(function(){

		$('#hadir-table').DataTable({
			order: [['1', 'desc']],
			autoWidth: false,
		});

		$('#belum-hadir-table').DataTable({
			order: [['0', 'asc']],
			autoWidth: false,
		});

		$('#cuti-libur-table').DataTable({
			order: [['0', 'asc']],
			autoWidth: false,
		});

		$('#izin-sakit-table').DataTable({
			order: [['0', 'asc']],
			autoWidth: false,
		});

		$('.dataTable').DataTable();

	})
</script>
@endsection