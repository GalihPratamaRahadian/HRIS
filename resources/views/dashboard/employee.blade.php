@extends('template.backLayout')


@section('content')

@if($isMustClockIn)
<div class="row">
	<div class="col-12">
		<div class="alert alert-fill-danger p-2" role="alert">
			<i class="mdi mdi-alert-circle mr-1"></i>
			Kamu belum mengisi kehadiran hari ini!.
		</div>
	</div>
</div>
@endif

@if($employee->isAllowForClockOut())
<div class="row">
	<div class="col-12">
		<div class="alert alert-fill-danger p-2" role="alert">
			<i class="mdi mdi-alert-circle mr-1"></i>
			Kamu belum mengisi absensi pulang!.
		</div>
	</div>
</div>
@endif

@if($employee->employeeContractIsAlmostOver())
<div class="row">
	<div class="col-12">
		<div class="alert alert-fill-danger p-2" role="alert">
			<i class="mdi mdi-alert-circle mr-1"></i>
			Kontrak kerjamu hampir habis.
		</div>
	</div>
</div>
@endif

@if(!$employee->isHasContract() && !$employee->isJobStatusTetap())
<div class="row">
	<div class="col-12">
		<div class="alert alert-fill-danger p-2" role="alert">
			<i class="mdi mdi-alert-circle mr-1"></i>
			Kamu belum memiliki kontrak kerja yang berlaku.
		</div>
	</div>
</div>
@endif

@if(($pending = \App\Models\LeaveSubmissionApproval::where('id_approver_position', auth()->user()->employee->id_position)->where('status', 'wait')->count()) > 0)
<div class="alert alert-primary">
	Ada {{ $pending }} pengajuan cuti menunggu persetujuan. Cek <a href="{{ route('employee.leave_approval') }}?status=wait"> disini </a> untuk melihat.
</div>
@endif

@if(($pending = \App\Models\SickNecessitySubmissionApproval::where('id_approver_position', auth()->user()->employee->id_position)->where('status', 'wait')->count()) > 0)
<div class="alert alert-primary">
	Ada {{ $pending }} pengajuan izin/sakit menunggu persetujuan. Cek <a href="{{ route('employee.sick_necessity_approval') }}?status=wait"> disini </a> untuk melihat.
</div>
@endif

@if(($pending = \App\Models\AttendancePermissionSubmissionApproval::where('id_approver_position', auth()->user()->employee->id_position)->where('status', 'wait')->count()) > 0)
<div class="alert alert-primary">
	Ada {{ $pending }} pengajuan izin terlambat/pulang cepat menunggu persetujuan. Cek <a href="{{ route('employee.attendance_permission_approval') }}?status=wait"> disini </a> untuk melihat.
</div>
@endif
<?php 
	$approvals = \App\Models\OvertimeSubmissionApproval::with(['overtimeSubmission'])
		->has('overtimeSubmission')
		->where('id_approver_position', auth()->user()->employee->id_position)
		->where('status', 'wait')
		->get();
	$pending = 0;
	foreach($approvals as $app) {
		if($app->overtimeSubmission->approval_progress_level == $app->level) {
			$pending++;
		}
	}
?>
@if($pending > 0)
<div class="alert alert-primary">
	Ada {{ $pending }} pengajuan lembur menunggu persetujuan. Cek <a href="{{ route('employee.overtime_approval') }}?status=wait"> disini </a> untuk melihat.
</div>
@endif
		

<div class="row flexbox-scroll-x mb-3">
	<div class="col-md-3 col-10">
		<div class="card bg-success">
			<div class="card-body p-3 align-items-center text-white">
				<div class="card-icon">
					<i class="mdi mdi-account"></i>
				</div>
				<div class="d-inline-block ml-5">
					<h3> Jatah Cuti </h3>
					<span> {{ employee()->leaveQuotaAvailable(true) }} Hari </span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3 col-10">
		<div class="card bg-primary">
			<div class="card-body p-3 align-items-center text-white">
				<div class="card-icon">
					<i class="mdi mdi-account-alert"></i>
				</div>
				<div class="d-inline-block ml-5">
					<h3> Izin / Cuti </h3>
					<span> {{ $attendanceResume->amount_of_necessity + $attendanceResume->amount_of_leave }} </span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3 col-10">
		<div class="card bg-warning">
			<div class="card-body p-3 align-items-center text-white">
				<div class="card-icon">
					<i class="mdi mdi-account-plus"></i>
				</div>
				<div class="d-inline-block ml-5">
					<h3> Sakit </h3>
					<span> {{ $attendanceResume->amount_of_sick }} </span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3 col-10">
		<div class="card bg-danger">
			<div class="card-body p-3 align-items-center text-white">
				<div class="card-icon">
					<i class="mdi mdi-account-remove"></i>
				</div>
				<div class="d-inline-block ml-5">
					<h3> Alpa </h3>
					<span> {{ $attendanceResume->amount_of_not_attend }} </span>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-7">

		<div class="row">
			<div class="col-12 grid-margin">
				<div class="card support-pane-card">
					<div class="card-body">

						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="card-title mb-0"> Jam Kerja </h4>
							<div class="btn-toolbar mb-0 d-block" role="toolbar">
								<small> {{ \Date::fullDateWithDayName() }} </small>
							</div>
						</div>

						<div class="support-pane">
							<div class="row border-bottom mb-3 pb-3">
								<div class="col text-center">

									<span class="shift-time">
										@if($employee->isHasShift())
										{{ employee()->clockStartActive('H:i') }} - {{ employee()->clockEndActive('H:i') }}
										@else
										Belum memiliki jam kerja
										@endif
									</span>

									@if($isAllowForClockIn)
										@if($isMustClockIn)
										<span class="text-muted">
											<small> Belum isi jam masuk <i class="mdi mdi-close-circle-outline text-danger"></i></small>
										</span>
										@else
										<span class="text-muted">
											@if(!$isOffDay)
											<small> Sudah isi jam keluar <i class="mdi mdi-check-circle-outline text-success"></i></small>
											@endif
											<br>
											<small> Dapat lakukan kehadiran untuk lembur </small>
										</span>
										@endif
									@else
										@if($isOvertime)
										<span class="text-muted">
											<small>Sudah isi kehadiran lembur <i class="mdi mdi-check-circle-outline text-success"></i></small>
										</span>
										@else
										<span class="text-muted">
											@if($latestAttendance = employee()->latestAttendance)
												@if(!$latestAttendance->isAlreadyClockOut())
												<small> Sudah isi jam masuk <i class="mdi mdi-check-circle-outline text-success"></i></small>
												@else
												<small> Sudah isi jam keluar untuk kehadiran {{ $latestAttendance->clockInAtText('d M Y') }} <i class="mdi mdi-check-circle-outline text-success"></i></small>
												@endif
											@endif
											@if($employee->isAllowForClockOut())
											<br>
											<small> Boleh mengisi jam keluar <i class="mdi mdi-logout text-success"></i> </small>
											@endif
										</span>
										@endif
									@endif

									@if(!employee()->isAllowCreateAttendanceViaWeb())
									<br>
									<span class="text-muted">
										<small> Anda tidak diizinkan mengisi kehadiran via web <i class="mdi mdi-close-circle-outline text-danger"></i></small>
									</span>
									@endif

								</div>
							</div>
							<div class="row">
								<div class="col-4">
									@if($isAllowForClockIn && employee()->isAllowCreateAttendanceViaWeb())
									<a href="{{ route('attendance.clock_in') }}" class="btn btn-success btn-block px-1">
										<i class="mdi mdi-login"></i> Check In
									</a>
									@else
									<button class="btn btn-success btn-block px-1" disabled="">
										<i class="mdi mdi-login"></i> Check In
									</button>
									@endif
								</div>
								<div class="col-4">
									@if(!employee()->isAlreadyClockOut())
									<a href="{{ route('attendance.check_day') }}" class="btn btn-warning btn-block px-1">
										<i class="mdi mdi-map-marker"></i> Check Day
									</a>
									@else
									<button class="btn btn-warning btn-block px-1" disabled="">
										<i class="mdi mdi-map-marker"></i> Check Day
									</button>
									@endif
								</div>
								<div class="col-4">
									@if($employee->latestAttendance)
										@if(!$employee->isAlreadyClockOut())
										<a href="{{ route('attendance.clock_out') }}" class="btn btn-danger btn-block px-1">
											<i class="mdi mdi-logout"></i> Check Out
										</a>
										@else
										<button class="btn btn-danger btn-block px-1" disabled="">
											<i class="mdi mdi-logout"></i> Check Out
										</button>
										@endif
									@else
									<button class="btn btn-danger btn-block px-1" disabled="">
										<i class="mdi mdi-logout"></i> Check Out
									</button>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		@if(employee()->isHasSalesEmployee() && setting('menu_sales_tracking', 'yes') == 'yes')
		<div class="row">
			<div class="col-12 grid-margin">
				<div class="card support-pane-card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="card-title mb-0"> Kunjungan Toko </h4>
						</div>

						<div class="support-pane">

							<p>
								Jumlah Kunjungan Hari Ini : <span class="badge badge-success"> 0 </span> 
							</p>

							<a class="btn btn-primary" href="{{ route('emp.sales_tracking') }}">
								Klik Disini Untuk Check-In Toko
							</a>

						</div>

					</div>
				</div>
			</div>
		</div>
		@endif

		@if(employee()->isHasTrackingEmployee() && setting('menu_tracking', 'yes') == 'yes')
		<div class="row">
			<div class="col-12 grid-margin">
				<div class="card support-pane-card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="card-title mb-0"> Check-In Lokasi Tracking </h4>
						</div>

						<div class="support-pane">

							<p>
								Jumlah Check-In Hari Ini : <span class="badge badge-success"> {{ $amountOfTrackingCheckInToday }} </span> 
							</p>

							<a class="btn btn-primary" href="{{ route('employee.tracking') }}">
								Klik Disini Untuk Check-In
							</a>

						</div>

					</div>
				</div>
			</div>
		</div>
		@endif

		@if(setting('menu_announcement', 'yes') == 'yes')
		<div class="row">
			<div class="col-12 grid-margin">
				<div class="card support-pane-card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="card-title mb-0"> Pengumuman </h4>
						</div>

						<div class="support-pane">

							@forelse($announcements as $announcement)
							<div class="mb-3 rounded-border">
								<small> {{ $announcement->createdAtText('d M Y H:i') }} </small>
								<a href="{{ route('employee.announcement.detail', $announcement->id) }}">
									<p class="font-weight-bold mb-0"> {{ $announcement->title }} </p>
								</a>
							</div>
							@empty
							<div class="mb-3">
								<div class="col">
									<small class="text-muted"> Belum ada pengumuman </small>
								</div>
							</div>
							@endforelse

							@if(count($announcements) > 0)
							<a href="{{ route('employee.announcement') }}"> 
								Lihat Semua Pengumuman <i class="mdi mdi-arrow-right"></i>
							</a>
							@endif

						</div>

					</div>
				</div>
			</div>
		</div>
		@endif

		<div class="row show-on-md-and-down">
			<div class="col-12 grid-margin">
				<div class="card support-pane-card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="card-title mb-0"> Agenda Libur </h4>
						</div>
						<div class="support-pane">

							@if(count($offDays) > 0)

							@foreach($offDays as $offDay)
							<div class="row">
								<div class="col mb-3">
									{{ $offDay->off_day_name }} <br>
									<small class="text-muted"> {{ $offDay->offDayDateText() }} </small>
								</div>
							</div>
							@endforeach

							@else
							<div class="row">
								<div class="col mb-3">
									<small class="text-muted"> Belum ada agenda libur </small>
								</div>
							</div>
							@endif

						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row show-on-lg-and-up">
			<div class="col-12 grid-margin">
				<div class="card support-pane-card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="card-title mb-0"> Agenda Libur </h4>
						</div>

						<div class="support-pane">

							@if(count($offDays) > 0)

							@foreach($offDays as $offDay)
							<div class="row mb-3">
								<div class="col">
									{{ $offDay->off_day_name }}
								</div>

								<small class="text-muted"> {{ $offDay->offDayDateText() }} </small>
							</div>
							@endforeach

							@else
							<div class="row mb-3">
								<div class="col">
									<small class="text-muted"> Tidak ada libur dalam 30 hari kedepan </small>
								</div>
							</div>
							@endif

						</div>

					</div>
				</div>
			</div>
		</div>

	</div>

	<div class="col-lg-5">
		<!-- <div class="row">
			<div class="col-lg-12 grid-margin">
				<div class="card support-pane-card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="card-title mb-0"> Jadwal Kerja Minggu Ini </h4>
						</div>
						<div class="support-pane">

							<table class="table table-sm">
								<tr> -->
									<!--- Jadwal Nanti disini -->
								<!-- </tr>
							</table>

							<table class="mt-2">
								<tr>
									<td>
										<span class="badge badge-success"></span>
									</td>
									<td> Hari Aktif </td>
								</tr>
								<tr>
									<td>
										<span class="badge badge-danger"></span>
									</td>
									<td> Libur </td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div> -->

		@if($employee->isJobStatusKontrak())
		<div class="row show-on-lg-and-up">
			<div class="col-lg-12 grid-margin">
				<div class="card support-pane-card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="card-title mb-0"> Informasi Kontrak Kerja </h4>
						</div>
						<div class="support-pane">
							<table class="table">
								<tr>
									<th> Tgl Kontrak </th>
									<td>

										@if($employee->isHasContract())
										{{ $employee->activeEmployeeContract->contractDateText() }}
										@else
										Belum Atur Kontrak/Kontrak Habis
										@endif

									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row show-on-md-and-down">
			<div class="col-lg-12 grid-margin">
				<div class="card support-pane-card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="card-title mb-0"> Informasi Kontrak Kerja </h4>
						</div>
						<div class="support-pane">
							<div class="form-group">
								<label class="title-label"> Tgl Kontrak </label> 
								
								<label>
									@if($employee->isHasContract())
									{{ $employee->employeeContract->contractDateText() }}
									@else
									Belum atur kontrak
									@endif
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		@endif



		<!-- Job Description -->
		@if(employee()->position)
		@if(!empty(strip_tags(employee()->position->job_description)))
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Deskripsi Pekerjaan </h4>
				</div>
				<div class="support-pane max-height-300">
					{!! employee()->position->job_description !!}
				</div>
			</div>
		</div>
		@endif
		@endif
		<!-- End Job Description -->


		<!-- Sasaran Kinerja -->
		@if(employee()->position)
		@if(!empty(strip_tags(employee()->position->performance_goals)))
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Sasaran Kinerja </h4>
				</div>
				<div class="support-pane max-height-300">
					{!! employee()->position->performance_goals !!}
				</div>
			</div>
		</div>
		@endif
		@endif
		<!-- End Sasaran Kinerja -->


		<!-- Kompetensi -->
		@if(employee()->position)
		@if(!empty(strip_tags(employee()->position->competence)))
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Kompetensi </h4>
				</div>
				<div class="support-pane max-height-300">
					{!! employee()->position->competence !!}
				</div>
			</div>
		</div>
		@endif
		@endif
		<!-- End Kompetensi -->


		<!-- <div class="row show-on-lg-and-up">
			<div class="col-lg-12 grid-margin">
				<div class="card support-pane-card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="card-title mb-0"> Penggajian {{ \Date::monthNameWithYear() }} </h4>
						</div>
						<div class="support-pane">
							<table class="table">
								<tr>
									<th> Gaji Pokok </th>
									<td>
										@if($employee->isHasSalary())
										{{ $employee->employeeSalary->basicSalaryText() }}
										@else
										Belum atur gaji
										@endif
									</td>
								</tr>
								<tr>
									<th>Lembur Per Jam</th>
									<td>
										@if($employee->isHasSalary())
										{{ $employee->employeeSalary->overtimePayText() }}
										@else
										Belum atur shift
										@endif
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div> -->

		<div class="row show-on-md-and-down">
			<div class="col-lg-12 grid-margin">
				<div class="card support-pane-card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="card-title mb-0"> Penggajian {{ \Date::monthNameWithYear() }}</h4>
						</div>
						<div class="support-pane">
							<table class="table">

								<div class="form-group">
									<label class="title-label"> 
										Gaji Pokok 
									</label>
								</div>

							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		@if($employee->isHasSalary())

		@if($employee->employeeSalary->isHasAllowances())
		<div class="row">
			<div class="col-lg-12 grid-margin">
				<div class="card support-pane-card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="card-title mb-0"> Tunjangan </h4>
						</div>
						<div class="support-pane">
							<table class="table">

								@foreach($employee->employeeSalary->employeeSalaryAllowances as $allowance)
								<tr>
									<th> {{ $allowance->allowance_name }} </th>
									<td> {{ $allowance->allowanceNominalText() }} </td>
								</tr>
								@endforeach

							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		@endif

		@if($employee->employeeSalary->isHasAllowances())
		<div class="row">
			<div class="col-lg-12 grid-margin">
				<div class="card support-pane-card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="card-title mb-0"> Potongan </h4>
						</div>
						<div class="support-pane">
							<table class="table">
										
								@foreach($employee->employeeSalary->employeeSalaryCuts as $cut)
								<tr>
									<th> {{ $cut->cut_name }} </th>
									<td> {{ $cut->cutNominalText() }} </td>
								</tr>
								@endforeach

							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		@endif
		@endif

		<div class="row">
			<div class="col-lg-12 grid-margin">
				<div class="card support-pane-card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="card-title mb-0"> Berulang Tahun Bulan Ini </h4>
						</div>
						<div class="support-pane max-height-300">
							@forelse($employeeWithBirthday as $emp)
							<div class="birthday-item">
								<div class="employee-photo">
									<img src="{{ $emp['photo_link'] }}">
								</div>
								<div class="employee-info">
									<div class="employee-name"> {{ $emp['employee_name'] }} </div>
									<div class="employee-department"> {{ $emp['department_name'] }} </div>
									<div class="employee-date-of-birth"> {{ $emp['date_of_birthday'] }} ({{ $emp['age'] }}) </div>
								</div>
							</div>
								@if(!$loop->last)
								<hr>
								@endif
							@empty
							<p align="center">
								Tidak ada yang berulang tahun bulan ini
							</p>
							@endforelse
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection


@section('modal')
<div class="modal fade" id="scheduleDetailModal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"> Detail Jadwal </h5>
				<button type="button" class="close">
					<span aria-hidden="true" data-dismiss="modal">&times;</span>
				</button>
			</div>
			<div class="modal-body pt-3">
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-light" data-dismiss="modal">
					<i class="mdi mdi-close"></i> Tutup
				</button>
			</div>
		</div>
	</div>
</div>
@endsection


@section('script')
<script type="text/html" id="scheduleDetailModalBodyTemplate">
	<small class="text-muted d-block mb-1"> {date} </small>
	<span class="h6 text-{class} d-block"> {offday_text} </span>
	<p> {description} </p>
</script>

<script type="text/javascript">
	
	$(function(){

		$('.schedule-box').on('click', function(){
			let { date, description, isoffday } = $(this).data()
			let html = $('#scheduleDetailModalBodyTemplate').text()
						.replaceAll(/{date}/g, date)
						.replaceAll(/{description}/g, description)
						.replaceAll(/{class}/g, isoffday ? 'danger' : 'success')
						.replaceAll(/{offday_text}/g, isoffday ? 'Hari Libur' : 'Hari Aktif');

			let modal = $('#scheduleDetailModal');
			modal.find('.modal-body').html(html)
			modal.modal('show');
		})

	})

</script>
@endsection


@section('style')
<style type="text/css">
	
	.shift-time {
		font-size: 1.3rem;
		font-weight: bold;
		display: block;
	}

	.card-icon {
		font-size: 48px;
	}

	.flexbox-scroll-x {
		flex-wrap: unset;
		overflow-x: auto;
	}

	.title-label {
		display: block;
		font-weight: bold;
	}

	.schedule-box {
		cursor: pointer;
	}

	.birthday-item {
		display: flex;
	}

	.birthday-item .employee-photo {
		max-width: 100px;
	}

	.birthday-item .employee-photo img {
		width: 100px;
		height: 100px;
		object-fit: cover;
		object-position: center;
		border-radius: 20px;
	}

	.birthday-item .employee-info {
		padding: 5px 20px;
	}

	.birthday-item .employee-name {
		font-weight: bold;
	}

	.birthday-item .employee-department {
		margin-bottom: 10px;
	}

	.birthday-item .employee-date-of-birth {
		/*font-weight: bold;*/
	}

	.max-height-300 {
		max-height: 300px;
		overflow-y: auto;
	}

	ol {
		padding-left: 2rem;
	}

</style>
@endsection