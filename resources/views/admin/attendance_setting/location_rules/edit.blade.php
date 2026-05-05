@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					{!! \Setting::requiredBanner() !!}
					
					<div class="form-group">
						<label> Nama Lokasi {!! \Setting::required() !!} </label>
						<input type="text" name="location_name" class="form-control" placeholder="Nama Lokasi" value="{{ $locationRules->location_name }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Lokasi Dipeta {!! \Setting::required() !!} </label>
						<div id="map" style="width: 100%; height: 300px;" class="mb-3"></div>
						<input type="text" name="coordinate" class="form-control" placeholder="Titik Koordinat" readonly required>
						<input type="hidden" name="latitude" required>
						<input type="hidden" name="longitude" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Satuan Radius {!! \Setting::required() !!} </label>
						<select class="form-control" name="radius_unit" required>

							@foreach(\App\Models\AttendanceLocationRule::availableRadiusUnit() as $unit => $label)
							<option value="{{ $unit }}"> {{ $label }} </option>
							@endforeach

						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Jarak Radius {!! \Setting::required() !!} </label>
						<input type="number" name="radius_distance" class="form-control" placeholder="Jarak Radius" value="{{ $locationRules->radius_distance }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<hr>

					<div class="form-group">
						<button class="btn btn-success" type="submit">
							<i class="mdi mdi-check"></i> Simpan
						</button>
					</div>
				</form>

			</div>
		</div>
	</div>
</div>
@endsection


@section('script')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ appconfig('google_geolocation_api_key') }}"></script>
<script src="{{ url('vendors/location-picker/location-picker.min.js') }}"></script>
<script type="text/javascript">
	$(function(){

		let $form = $('#mainForm')
		let $submitBtn = $form.find('[type="submit"]').ladda();

		const init = () => {
			$form.find('[name="location_name"]').focus();
		}


		$form.on('submit', function(e){
			e.preventDefault();

			let formData = $(this).serialize();
			$submitBtn.ladda('start')
			ajaxSetup();
			$.ajax({
				url : `{{ route('attendance_location_rules.update', $locationRules->id) }}`,
				method : 'put',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				init();
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});

		init();

		$form.find('[name="radius_unit"]').val(`{{ $locationRules->radius_unit }}`);

		const map = document.querySelector('#map');
		const lp = new locationPicker(map, {
			setCurrentPosition: true,
			lat: `{{ $locationRules->latitude }}`,
			lng: `{{ $locationRules->longitude }}`
		}, {
			zoom: 15
		});

		google.maps.event.addListener(lp.map, 'idle', function (event) {
			const location = lp.getMarkerPosition();
			$form.find(`[name="coordinate"]`).val(`${location.lat},${location.lng}`)
			$form.find(`[name="latitude"]`).val(`${location.lat}`)
			$form.find(`[name="longitude"]`).val(`${location.lng}`)
		});
	});
</script>
@endsection