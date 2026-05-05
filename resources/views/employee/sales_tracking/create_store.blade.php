@extends('template.backLayout')

@section('content')

<div class="row">
	<div class="col-md-12">
		<form id="mainForm">
			<div class="row">
				<div class="col-lg-6 grid-margin">
					<div class="card support-pane-card">
						<div class="card-body">
							<div class="support-pane">

								{!! Template::infoBanner('Pastikan anda mengisi data ketika sedang berada di dalam toko') !!}

								{!! Template::requiredBanner() !!}

								<div class="form-group">
									<label> Nama Toko {!! Template::required() !!} </label>
									<input type="text" name="store_name" class="form-control" placeholder="Nama Toko" required>
								</div>

								<div class="form-group">
									<label> Nomor Whatsapp (Opsional) </label>
									<input type="text" name="phone_number" class="form-control" placeholder="Nomor Whatsapp">
								</div>

								<div class="form-group">
									<label> Alamat {!! Template::required() !!} </label>
									<textarea name="address" class="form-control" placeholder="Alamat" rows="2" required></textarea>
								</div>

								<div class="form-group">
									<label> Lokasi </label> <br>
									<a href="javascript:void();" class="mapBtn">Lihat Peta</a>
									
									<input type="hidden" name="latitude">
									<input type="hidden" name="longitude">
								</div>

								<hr>

								<button type="submit" class="btn btn-success mt-2">
									<i class="mdi mdi-check"></i> Simpan
								</button>

							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection


@section('modal')
<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog mt-4" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Lokasi</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="map"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-light" data-dismiss="modal"><i class="mdi mdi-close"></i>Tutup</button>
			</div>
		</div>
	</div>
</div>
@endsection


@section('script')
<script type="text/javascript">
	$(function(){

		const form = $('#mainForm');
		const submitBtn = form.find('[type="submit"]').ladda();

		
		

		const getLocation = () => {
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(showPosition, showError);
				console.log("Geolocation get the position.");
			} else {
				console.log("Geolocation is not supported by this browser.");
			}
		}


		const showPosition = position => {
			const { latitude, longitude } = position.coords

			form.find(`[name="latitude"]`).val(latitude)
			form.find(`[name="longitude"]`).val(longitude)
			$.get({
				url: `{{ route('helper.map_generate') }}?latitude=${latitude}&longitude=${longitude}`,
				dataType: 'json'
			})
			.done(response => {
				const { embedded_map_html } = response
				$('#map').html(embedded_map_html)
			})

		}


		const showError = error => {
			switch(error.code) {
				case error.PERMISSION_DENIED:
					alert(`Mohon berikan izin akses lokasi anda. Cek link berikut : https://support.google.com/chrome/answer/142065?hl=en`);
					$('.address').html(`Aplikasi tidak berjalan semesti nya. <br>Mohon berikan izin akses lokasi anda. Petunjuk <a target="_blank" href="https://support.google.com/chrome/answer/142065?hl=en">Klik disini</a>`)
					$('.mapBtn').hide();
					break;
				case error.POSITION_UNAVAILABLE:
					alert("Location information is unavailable.");
					break;
				case error.TIMEOUT:
					alert("The request to get user location timed out.");
					break;
				case error.UNKNOWN_ERROR:
					alert("An unknown error occurred.");
					break;
			}
		}

		const buttonEvent = () => {
			$('.mapBtn').on('click', function(){
				$('#mapModal').modal('show');
			})
		}


		const validateInput = () => {
			let latitude = $('[name="latitude"]').val(),
				longitude = $('[name="longitude"]').val();

			if(isEmpty(latitude) || isEmpty(longitude)) {
				swal("Peringatan", "Lokasi anda tidak berfungsi", "error");
				return false;
			} else {
				return true;
			}
		}


		form.on('submit', function(e){
			e.preventDefault();

			if(!validateInput()) {
				return;
			}

			let formData = $(this).serialize();

			submitBtn.ladda('start')
			ajaxSetup();
			$.ajax({
				url : `{{ route('emp.sales_tracking.save_store') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				ajaxSuccessHandling(response)
				setTimeout(() => {
					window.location.href = `{{ route('emp.sales_tracking') }}`
				}, 1000)
			})
			.fail(error => {
				submitBtn.ladda('stop')
				ajaxErrorHandling(error, form)
			})
		})


		const init = () => {
			buttonEvent();
			getLocation();
		}

		init();
	});
</script>
@endsection
