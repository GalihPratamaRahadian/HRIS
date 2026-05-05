@extends('template.backLayout')


@section('content')
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
							<td> Karyawan </td>
							<td>
								<a href="{{ route('employee.detail', $tracking->id_employee) }}">
									{{ $tracking->employeeName() }}
								</a>
							</td>
						</tr>

						<tr>
							<td> Lokasi </td>
							<td>
								{{ $tracking->trackingLocationName() }}
							</td>
						</tr>

						<tr>
							<td> Waktu Check In </td>
							<td> {{ $tracking->checkInAtFormatted('d M Y H:i') }} </td>
						</tr>

						<tr>
							<td> Waktu Check Out </td>
							<td> {{ $tracking->checkOutAtFormatted('d M Y H:i') }} </td>
						</tr>

						<tr>
							<td> File Penerimaan Barang </td>
							<td>
								<a href="{{ $tracking->fileGoodReceiptLink() }}" target="_blank"> Lihat File Penerimaan Barang </a>
							</td>
						</tr>

					</table>
				</div>

			</div>
		</div>

		<div class="card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Lokasi Tracking </h4>
				</div>

				<ul class="nav nav-tabs tab-solid tab-solid-success" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="tab-5-1" data-toggle="tab" href="#check-in-map" role="tab" aria-controls="check-in-map" aria-selected="true"> Check In </a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="tab-5-2" data-toggle="tab" href="#check-day-map" role="tab" aria-controls="check-day-map" aria-selected="false"> Check Day </a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="tab-5-2" data-toggle="tab" href="#check-out-map" role="tab" aria-controls="check-out-map" aria-selected="false"> Check Out </a>
					</li>
				</ul>

				<div class="tab-content tab-content-solid">

					<div class="tab-pane fade show active" id="check-in-map" role="tabpanel" aria-labelledby="tab-5-1">
						{!! $tracking->getLocation()->embeddedMap() !!}
					</div>

					<div class="tab-pane fade" id="check-day-map" role="tabpanel" aria-labelledby="tab-5-2">
						@if($tracking->isCheckedDay())
						{!! $tracking->getLocation()->embeddedMap() !!}
						@else
						<p> Belum melakukan Check-Day </p>
						@endif
					</div>

					<div class="tab-pane fade" id="check-out-map" role="tabpanel" aria-labelledby="tab-5-2">
						@if($tracking->isCheckedOut())
						{!! $tracking->getLocation()->embeddedMap() !!}
						@else
						<p> Belum melakukan Check-Out </p>
						@endif
					</div>

				</div>
			</div>
		</div>

	</div>

	<div class="col-md-6">
		<div class="card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Foto Tracking </h4>
				</div>

				<ul class="nav nav-tabs tab-solid tab-solid-danger" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="tab-5-1" data-toggle="tab" href="#check-in-photo" role="tab" aria-controls="check-in-photo" aria-selected="true"> Check In </a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="tab-5-2" data-toggle="tab" href="#check-day-photo" role="tab" aria-controls="check-day-photo" aria-selected="false"> Check Day </a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="tab-5-2" data-toggle="tab" href="#check-out-photo" role="tab" aria-controls="check-out-photo" aria-selected="false"> Check Out </a>
					</li>
				</ul>

				<div class="tab-content tab-content-solid">

					<div class="tab-pane fade show active" id="check-in-photo" role="tabpanel" aria-labelledby="tab-5-1">
						<img src="{{ $tracking->checkInPhotoLink() }}" class="img-fluid">
					</div>

					<div class="tab-pane fade" id="check-day-photo" role="tabpanel" aria-labelledby="tab-5-2">
						@if($tracking->isCheckedDay())
						<img src="{{ $tracking->checkDayPhotoLink() }}" class="img-fluid">
						@else
						<p> Belum melakukan Check-Day </p>
						@endif
					</div>

					<div class="tab-pane fade" id="check-out-photo" role="tabpanel" aria-labelledby="tab-5-2">
						@if($tracking->isCheckedOut())
						<img src="{{ $tracking->checkOutPhotoLink() }}" class="img-fluid">
						@else
						<p> Belum melakukan Check-Out </p>
						@endif
					</div>
					
				</div>



			</div>
		</div>
	</div>

</div>
@endsection