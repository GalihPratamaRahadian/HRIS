@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					{!! \Setting::requiredBanner() !!}

					@method('PUT')
					
					<div class="form-group">
						<label> Nama Lokasi {!! \Setting::required() !!} </label>
						<input type="text" name="location_name" class="form-control" placeholder="Nama Lokasi" value="{{ $trackingLocation->location_name }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Lokasi Dipeta {!! \Setting::required() !!} </label>
						<div id="map" style="width: 100%; height: 300px;" class="mb-3"></div>
						<input type="text" name="coordinate" class="form-control" placeholder="Titik Koordinat" value="{{ $trackingLocation->latitude }},{{ $trackingLocation->langitude }}" readonly required>
						<input type="hidden" name="latitude" required>
						<input type="hidden" name="longitude" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Foto (Opsional) </label>
						<input type="file" name="file_photo" class="form-control" accept="image/*">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Alamat (Opsional) </label>
						<textarea class="form-control" name="address" placeholder="Alamat (Opsional)" rows="3">{{ $trackingLocation->address }}</textarea>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Deskripsi (Opsional) </label>
						<textarea class="form-control" name="description" placeholder="Deskripsi (Opsional)" rows="3">{{ $trackingLocation->description }}</textarea>
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

		const resetForm = () => {
			$form[0].reset();
		}

		const init = () => {
			resetForm();
			$form.find('[name="location_name"]').focus();
		}


		$form.on('submit', function(e){
			e.preventDefault();

			let formData = new FormData(this);

			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('admin.tracking_location.update', $trackingLocation->id) }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				processData: false,
				contentType: false,
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

		const map = document.querySelector('#map');
		const lp = new locationPicker(map, {
			setCurrentPosition: true,
			lat: `{{ $trackingLocation->latitude }}`,
			lng: `{{ $trackingLocation->longitude }}`
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