@extends('template.backLayout')


@section('content')
<?php 
	$meta = $attendance->isHasMeta() ? $attendance->attendanceMeta : false;
?>

<div class="row">

	<div class="col-md-6">

		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> {{ $title }} </h4>
				</div>

				<div class="table-responsive">
					<table class="table table-hover">

						<tr>
							<td> Nama Karyawan </td>
							<td> 
								@if(!auth()->user()->isEmployee())
								<a href="{{ route('employee.detail', $attendance->id_employee) }}">
								@endif
									{{ $attendance->employeeName() }}
								@if(!auth()->user()->isEmployee())
								</a>
								@endif
							</td>
						</tr>

						<tr>
							<td> Tanggal Kehadiran </td>
							<td> {{ $attendance->dateText() }} </td>
						</tr>

						@if($attendance->isTypeHadir())
						<tr>
							<td> Jam Masuk </td>
							<td> {{ $attendance->clockInAtText('d M Y H:i') }} WIB </td>
						</tr>
						<tr>
							<td> Jam Keluar </td>
							<td> {{ $attendance->clockOutAtText('d M Y H:i') }} WIB </td>
						</tr>
						@endif

						<tr>
							<td> Status Kehadiran </td>
							<td> {!! $attendance->typeHtml() !!} </td>
						</tr>

						@if($attendance->isTypeHadir())
						<tr>
							<td> Metode Kehadiran </td>
							<td> {{ $attendance->clockInMethodText() }} | {{ $attendance->clockOutMethodText() }} </td>
						</tr>

						<tr>
							<td> Keterlambatan </td>
							<td> {{ $attendance->lateText() }} </td>
						</tr>
						@endif

						@if($attendance->isOvertime())
						<tr>
							<td> Lembur </td>
							<td> {{ $attendance->overtimeText() }} </td>
						</tr>
						@endif

					</table>
				</div>

			</div>
		</div>

		<!-- <div class="card support-pane-card grid-margin show-on-md-and-down">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> {{ $title }} </h4>
				</div>

				<div class="form-group">
					<label class="d-block"><b> Nama Karyawan </b></label>
					<label>
						@if(!auth()->user()->isEmployee())
						<a href="{{ route('employee.detail', $attendance->id_employee) }}">
						@endif
							{{ $attendance->employeeName() }}
						@if(!auth()->user()->isEmployee())
						</a>
						@endif
					</label>
				</div>

				<div class="form-group">
					<label class="d-block"><b> Tanggal Kehadiran </b></label>
					<label> {{ $attendance->dateText() }} </label>
				</div>

				@if($attendance->isTypeHadir())
				<div class="form-group">
					<label class="d-block"><b> Waktu Kehadiran </b></label>
					<label> {{ $attendance->clockInTextFull() }} - {{ $attendance->clockOutTextFull() }} </label>
				</div>
				@endif

				<div class="form-group">
					<label class="d-block"><b> Tanggal Kehadiran </b></label>
					<label> {{ $attendance->dateText() }} </label>
				</div>

				<div class="form-group">
					<label class="d-block"><b> Status Kehadiran </b></label>
					<label> {!! $attendance->typeHtml() !!} </label>
				</div>

				@if($attendance->isTypeHadir())
				<div class="form-group">
					<label class="d-block"><b> Metode Kehadiran </b></label>
					<label> {{ $attendance->clockInMethodText() }} | {{ $attendance->clockOutMethodText() }} </label>
				</div>

				<div class="form-group">
					<label class="d-block"><b> Keterlambatan </b></label>
					<label> {{ $attendance->lateText() }} </label>
				</div>
				@endif

				@if($attendance->isOvertime())
				<div class="form-group">
					<label class="d-block"><b> Lembur </b></label>
					<label> {{ $attendance->overtimeText() }} </label>
				</div>
				@endif

			</div>
		</div> -->

		@if($meta)
		<div class="card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Lokasi Kehadiran </h4>
				</div>

				<ul class="nav nav-tabs tab-solid tab-solid-danger" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="tab-5-1" data-toggle="tab" href="#clock-in-map" role="tab" aria-controls="clock-in-map" aria-selected="true"> Jam Masuk </a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="tab-5-2" data-toggle="tab" href="#clock-out-map" role="tab" aria-controls="clock-out-map" aria-selected="false"> Jam Keluar </a>
					</li>
				</ul>

				<div class="tab-content tab-content-solid">

					<div class="tab-pane fade show active" id="clock-in-map" role="tabpanel" aria-labelledby="tab-5-1">
						
						@if($meta->isHasClockInLocation())
						<?php 
							$loc = $meta->getClockInLocation();
						?>
							@if($loc)
							{!! location($loc->latitude, $loc->longitude)->embeddedMap() !!}
							@endif
						@else
						<p> Lokasi Tidak Tersedia </p>
						@endif

					</div>

					<div class="tab-pane fade" id="clock-out-map" role="tabpanel" aria-labelledby="tab-5-2">

						@if($attendance->isAlreadyClockOut())
						<?php 
							$loc = $meta->getClockOutLocation();
						?>
							@if($loc)
							{!! location($loc->latitude, $loc->longitude)->embeddedMap() !!}
							@endif
						@else
						<p> Belum isi jam keluar </p>
						@endif

					</div>

				</div>
			</div>
		</div>
		@endif

	</div>

	@if($meta)
	<div class="col-md-6">
		<div class="card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Foto Kehadiran </h4>
				</div>

				<ul class="nav nav-tabs tab-solid tab-solid-success" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="tab-5-1" data-toggle="tab" href="#clock-in-photo" role="tab" aria-controls="clock-in-photo" aria-selected="true"> Jam Masuk </a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="tab-5-2" data-toggle="tab" href="#clock-out-photo" role="tab" aria-controls="clock-out-photo" aria-selected="false"> Jam Keluar </a>
					</li>
				</ul>

				<div class="tab-content tab-content-solid">

					<div class="tab-pane fade show active" id="clock-in-photo" role="tabpanel" aria-labelledby="tab-5-1">
						@if($meta->isHasClockInPhoto())
						<img src="{{ $meta->clockInPhotoLink() }}" class="img-fluid">
						@else
						<p> Foto Tidak Tersedia </p>
						@endif
					</div>

					<div class="tab-pane fade" id="clock-out-photo" role="tabpanel" aria-labelledby="tab-5-2">
						@if($attendance->isAlreadyClockOut())
							@if($meta->isHasClockOutPhoto())
							<img src="{{ $meta->clockOutPhotoLink() }}" class="img-fluid">
							@else
							<p> Foto Tidak Tersedia </p>
							@endif
						@else
						<p> Belum isi jam keluar </p>
						@endif
					</div>

				</div>
			</div>
		</div>
	</div>
	@endif

</div>
@endsection