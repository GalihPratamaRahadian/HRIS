@extends('template.backLayout')


@section('style')
<style type="text/css">
	.location-image {
		height: 150px;
		width: 150px;
		object-fit: cover;
		object-position: center;
	}
</style>
@endsection


@section('content')
<div class="row">
	<div class="col-lg-6">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="text-center">
					<img src="{{ $trackingLocation->photoLink() }}" class="location-image">
					<p class="mt-3 mb-1">
						<b> {{ $trackingLocation->location_name }} </b> <br>
						<b> {{ $trackingLocation->address }} </b>
					</p>
					<p>
						{{ $trackingLocation->description ?? '' }}
					</p>
				</div>

				<hr>

				@if(!$trackingLocation->isCheckedInToday())
				<p align="center">
					Belum Melakukan Check-In
					<i class="mdi mdi-close-circle-outline text-danger"></i>
				</p>
				@elseif($trackingLocation->isCheckedInToday())
					@if($trackingLocation->isCheckedOutToday())
					<p align="center">
						Sudah Melakukan Check-Out
						<i class="mdi mdi-check-circle-outline text-success"></i>
					</p>
					@elseif($trackingLocation->isCheckedDayToday())
					<p align="center">
						Sudah Melakukan Check-Day
						<i class="mdi mdi-check-circle-outline text-success"></i>
					</p>
					@else
					<p align="center">
						Sudah Melakukan Check-In 
						<i class="mdi mdi-check-circle-outline text-success"></i>
					</p>
					@endif
				@endif

				<hr>

				<div class="row">
					<div class="col-lg-4">
						<button class="btn btn-success btn-block px-1 mb-3 check-in-btn" disabled>
							<i class="mdi mdi-login"></i> Check In
						</button>
					</div>
					<div class="col-lg-4">
						<button class="btn btn-warning btn-block px-1 mb-3 check-day-btn" disabled>
							<i class="mdi mdi-map-marker"></i> Check Day
						</button>
					</div>
					<div class="col-lg-4">
						<button class="btn btn-danger btn-block px-1 mb-3 check-out-btn" disabled>
							<i class="mdi mdi-logout"></i> Check Out
						</button>
					</div>
				</div>

				<hr>

				<p align="center">
					<a href="{{ $trackingLocation->getLocation()->gmapsLink() }}" target="_blank">
						<i class="mdi mdi-map-marker-radius-outline"></i> Lihat Petunjuk di Maps
					</a>
				</p>

			</div>
		</div>
	</div>
</div>
@endsection


@section('script')
<script type="text/javascript">
	$(function(){

		$('.check-in-btn').on('click', function(){
			window.location.href = `{{ route('employee.tracking.check_in', $trackingLocation->id) }}`
		})

		$('.check-day-btn').on('click', function(){
			window.location.href = `{{ route('employee.tracking.check_day', $trackingLocation->id) }}`
		})

		$('.check-out-btn').on('click', function(){
			window.location.href = `{{ route('employee.tracking.check_out', $trackingLocation->id) }}`
		})

		@if(!$trackingLocation->isCheckedInToday())
		$('.check-in-btn').removeAttr('disabled')
		@endif

		@if($trackingLocation->isCheckedInToday() && !$trackingLocation->isCheckedDayToday())
		$('.check-day-btn').removeAttr('disabled')
		@endif

		@if($trackingLocation->isCheckedInToday() && !$trackingLocation->isCheckedOutToday())
		$('.check-out-btn').removeAttr('disabled')
		@endif

	});
</script>
@endsection
