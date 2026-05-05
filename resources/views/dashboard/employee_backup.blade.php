@extends('template.backLayout')


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

</style>
@endsection


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
		

<div class="row flexbox-scroll-x mb-3">
	<div class="col-md-3 col-10">
		<div class="card bg-success">
			<div class="card-body p-3 align-items-center text-white">
				<div class="card-icon">
					<i class="mdi mdi-account"></i>
				</div>
				<div class="d-inline-block ml-5">
					<h3> Lembur </h3>
					<span> {{ $kehadiran->lembur }} Menit </span>
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
					<span> {{ $resume['necessity'] + $resume['leave'] }} </span>
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
					<span> {{ $resume['sick'] }} </span>
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
					<span> {{ $resume['not_attend'] }} </span>
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
										{{ $employee->shift->shiftTimeTwelveHoursText() }}
										@else
										Belum memiliki jam kerja
										@endif
									</span>

									@if($isAllowForClockIn)
										@if($isMustClockIn)
										<span class="text-muted">
											<small>Belum isi kehadiran <i class="mdi mdi-close-circle-outline text-danger"></i></small>
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
											<small> Sudah isi kehadiran <i class="mdi mdi-check-circle-outline text-success"></i></small>
											@if($employee->isAllowForClockOut())
											<br>
											<small> Boleh mengisi jam keluar <i class="mdi mdi-logout text-success"></i> </small>
											@endif
										</span>
										@endif
									@endif

									@if(!auth()->user()->employee->isAllowCreateAttendanceViaWeb())
									<br>
									<span class="text-muted">
										<small> Anda tidak diizinkan mengisi kehadiran via web <i class="mdi mdi-close-circle-outline text-danger"></i></small>
									</span>
									@endif

								</div>
							</div>
							<div class="row">
								<div class="col-6">
									@if($isAllowForClockIn && auth()->user()->employee->isAllowCreateAttendanceViaWeb())
									<a href="{{ route('attendance.clock_in') }}" class="btn btn-success btn-block px-1">
										<i class="mdi mdi-login"></i> Jam Masuk
									</a>
									@else
									<button class="btn btn-success btn-block px-1" disabled="">
										<i class="mdi mdi-login"></i> Jam Masuk
									</button>
									@endif
								</div>
								<div class="col-6">
									@if($employee->latestAttendance)
										@if(!$employee->isAlreadyClockOut())
										<a href="{{ route('attendance.clock_out') }}" class="btn btn-danger btn-block px-1">
											Jam Keluar <i class="mdi mdi-logout"></i>
										</a>
										@else
										<button class="btn btn-danger btn-block px-1" disabled="">
											Jam Keluar <i class="mdi mdi-logout"></i>
										</button>
										@endif
									@else
									<button class="btn btn-danger btn-block px-1" disabled="">
										Jam Keluar <i class="mdi mdi-logout"></i>
									</button>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-12 grid-margin">
				<div class="card support-pane-card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="card-title mb-0"> Jatah Cuti Bulan Ini </h4>
						</div>

						<div class="support-pane">

							Jatah Cuti {{ employee()->leaveQuotaAvailable() }} hari

						</div>

					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-12 grid-margin">
				<div class="card support-pane-card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="card-title mb-0"> Pengumuman </h4>
						</div>

						<div class="support-pane">

							@forelse(\App\Models\Announcement::take(3)->orderBy('created_at', 'desc')->get() as $announcement)
							<div class="mb-3">
								<small> {{ $announcement->createdAtText('d M Y H:i') }} </small>
								<a href="{{ route('announcement.detail', $announcement->id) }}">
									<p class="font-weight-bold"> {{ $announcement->title }} </p>
								</a>
							</div>
							@empty
							<div class="mb-3">
								<div class="col">
									<small class="text-muted"> Belum ada pengumuman </small>
								</div>
							</div>
							@endforelse

						</div>

					</div>
				</div>
			</div>
		</div>

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
									<small class="text-muted">Tidak ada libur dalam 30 hari kedepan</small>
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
		<div class="row">
			<div class="col-lg-12 grid-margin">
				<div class="card support-pane-card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="card-title mb-0"> Jadwal Kerja Minggu Ini </h4>
						</div>
						<div class="support-pane">

							<table class="table table-sm">
								<tr>
									@foreach($employee->workScheduleInWeek() as $schedule)
									<td align="center">

										<span data-date="{{ $schedule->date_text }}" data-description="{{ $schedule->description }}" data-isoffday="{{ $schedule->is_offday }}" class="badge schedule-box badge-{{ $schedule->is_offday ? 'danger' : 'success' }}">
											{{ $schedule->date_in_day }}
										</span>

									</td>
									@endforeach
								</tr>
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
		</div>

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
										{{ $employee->employeeContract->contractDateText() }}
										@else
										Belum atur kontrak
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
		@if(!empty(employee()->position->job_description))
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Deskripsi Pekerjaan </h4>
				</div>
				<div class="support-pane">
					{!! employee()->position->job_description !!}
				</div>
			</div>
		</div>
		@endif
		@endif
		<!-- End Job Description -->


		<!-- Sasaran Kinerja -->
		@if(employee()->position)
		@if(!empty(employee()->position->performance_goals))
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Sasaran Kinerja </h4>
				</div>
				<div class="support-pane">
					{!! employee()->position->performance_goals !!}
				</div>
			</div>
		</div>
		@endif
		@endif
		<!-- End Sasaran Kinerja -->


		<!-- Kompetensi -->
		@if(employee()->position)
		@if(!empty(employee()->position->competence))
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Kompetensi </h4>
				</div>
				<div class="support-pane">
					{!! employee()->position->competence !!}
				</div>
			</div>
		</div>
		@endif
		@endif
		<!-- End Kompetensi -->


		<div class="row show-on-lg-and-up">
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
									<th> Gaji Harian </th>
									<td>
										@if($employee->isHasSalary())

										@if($employee->isHasShift())
										{{ $employee->dailySalaryText() }}
										@else
										Belum atur shift
										@endif

										@else
										Belum atur gaji
										@endif
									</td>
								</tr>
								<tr>
									<th>Hari kerja</th>
									<td>
										@if($employee->isHasShift())
										{{ $employee->shift->amountOfWorkDayInMonth() }} hari
										@else
										Belum atur shift
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
		</div>

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
											
									<label>
										@if($employee->isHasSalary())

										@if($employee->isHasShift())
										{{ $employee->dailySalaryText() }}
										@else
										Belum atur shift
										@endif

										@else
										Belum atur gaji
										@endif
									</label>
								</div>

								<div class="form-group">
									<label class="title-label">
										Gaji Harian
									</label>
											
									<label>
										@if($employee->isHasSalary())

										@if($employee->isHasShift())
										{{ $employee->dailySalaryText() }}
										@else
										Belum atur shift
										@endif

										@else
										Belum atur gaji
										@endif
									</label>
								</div>

								<div class="form-group">
									<label class="title-label"> 
										Hari kerja 
									</label>
											
									<label>
										@if($employee->isHasShift())
										{{ $employee->shift->amountOfWorkDayInMonth() }} hari
										@else
										Belum atur shift
										@endif
									</label>
								</div>

								<div class="form-group">
									<label class="title-label"> 
										Lembur Per Jam 
									</label>
											
									<label>
										@if($employee->isHasSalary())
										{{ $employee->employeeSalary->overtimePayText() }}
										@else
										Belum atur shift
										@endif
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