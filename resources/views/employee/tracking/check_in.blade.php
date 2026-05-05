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

								{!! Template::requiredBanner() !!}

								<div class="form-group">
									<label> Foto {!! Template::required() !!} </label> <br>
									<video style="height: auto; width: 100%;" id="cameraVideo" autoplay="true"></video>
									<canvas id="cameraCanvas" style="display: none;"></canvas>
									<img id="cameraPict" style="height: auto; width: 100%; display: none;">
									<div class="w-100 text-center mt-2 mb-4">
										<div class="btn-group">
											<button type="button" class="btn btn-primary captureBtn text-wrap" disabled=""><i class="mdi mdi-camera"></i> Capture</button>
											<button type="button" class="btn btn-success playBtn text-wrap"><i class="mdi mdi-play"></i> Play</button>
											<button type="button" class="btn btn-danger stopBtn text-wrap" style="display: none;"><i class="mdi mdi-stop"></i> Stop</button>
											<a href="#" class="btn btn-success downloadBtn text-wrap" download="capture" target="_blank" style="display: none;"><i class="mdi mdi-download"></i> Download</a>
											<button type="button" class="btn btn-danger removeBtn text-wrap" style="display: none;"><i class="mdi mdi-trash-can"></i> Remove</button>
										</div>
									</div>
								</div>
								
								<div class="form-group">
									<input type="hidden" name="check_in_photo_base64">
									<small class="invalid-feedback"></small>
									<input type="hidden" name="id_tracking_location" value="{{ $trackingLocation->id }}">
								</div>

								<div class="form-group">
									<label> Lokasi Anda Saat Ini </label> <br>
									<a href="javascript:void();" class="mapBtn">Lihat Peta</a>
									
									<input type="hidden" name="latitude">
									<input type="hidden" name="longitude">
								</div>

								<hr>

								<button type="submit" class="btn btn-success btn-block mt-2">
									Check In <i class="mdi mdi-login"></i>
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

		let cameraVideo = document.getElementById("cameraVideo"),
			mediaStream = null,
			canvas = document.getElementById("cameraCanvas"),
			canvasContext = canvas.getContext('2d'),
			camera = {
				width: null,
				height: null
			};

		const play = () => {
			if (navigator.mediaDevices.getUserMedia) {
				navigator.mediaDevices.getUserMedia({ video: true })
				.then(stream => {
					cameraVideo.srcObject = stream;
					mediaStream = stream;
					let {height, width} = stream.getTracks()[0].getSettings();
					camera.width = width;
					camera.height = height;
					$('.playBtn').hide();
					$('.stopBtn').show();
					enable($('.captureBtn'));
				})
				.catch(err => {
					console.log("Something went wrong!");
					console.log(err);
				});
			}
		}


		const capture = () => {
			let scale = 1;
			canvas.width = camera.width;
			canvas.height = camera.height;
			canvasContext.drawImage(cameraVideo, 0,0, canvas.width, canvas.height);
			canvas.toBlob(blob =>{
				let url = window.URL.createObjectURL(blob);
				// $('.cameraPict').attr('src', url);
				$('.downloadBtn').attr('href', url);
			});
			$('#cameraVideo').hide();
			$('#cameraPict').show();
			disable($('.captureBtn'));
			let dataUrl = canvas.toDataURL("image/jpeg");
			$('[name="check_in_photo_base64"]').val(dataUrl);
			$('#cameraPict').attr('src', dataUrl);
		}


		const stop = () => {
			if(mediaStream != null) {
				if(mediaStream.active == true) {
					mediaStream.getTracks()[0].stop();
					$('.playBtn').show();
					$('.stopBtn').hide();
					disable($('.captureBtn'));
				}
			}
		}        
		

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
			$('.playBtn').on('click', function(){
				play();
			})

			$('.stopBtn').on('click', function(){
				stop();
			})

			$('.captureBtn').on('click', function(){
				capture();
				$(this).hide();
				$('.stopBtn').hide();
				$('.removeBtn').show();
				$('.downloadBtn').show();
			})

			$('.removeBtn').on('click', function(){
				$('#cameraPict').removeAttr('src');
				$('#cameraPict').hide();
				$('#cameraVideo').show();
				enable($('.captureBtn'));
				$('[name="check_in_photo_base64"]').val('');
				$(this).hide();
				$('.downloadBtn').hide();
				$('.stopBtn').show();
				$('.captureBtn').show();
			})

			$('.mapBtn').on('click', function(){
				$('#mapModal').modal('show');
			})
		}


		const validateInput = () => {
			let photo = $('[name="check_in_photo_base64"]').val(),
				latitude = $('[name="latitude"]').val(),
				longitude = $('[name="longitude"]').val();

			if(isEmpty(photo)) {
				swal("Peringatan", "Wajib melakukan capture", "error");
				return false;
			} else if(isEmpty(latitude) || isEmpty(longitude)) {
				swal("Peringatan", "Lokasi anda tidak berfungsi", "error");
				return false;
			} else {
				return true;
			}
		}


		const formSubmit = () => {
			$('#mainForm').on('submit', function(e){
				e.preventDefault();

				if(!validateInput()) {
					return;
				}

				let formData = new FormData(this);

				submitBtn.ladda('start')
				ajaxSetup();
				$.ajax({
					url : `{{ route('employee.tracking.save_check_in', $trackingLocation->id) }}`,
					method : 'post',
					data : formData,
					dataType : 'json',
					contentType: false,
					processData: false
				})
				.done(response => {
					const { message, code } = response

					if(code == 200) {
						toastrAlert();
						toastr.success(message, 'Berhasil');

						setTimeout(() => {
							window.location.replace(`{{ route('employee.tracking.location_detail', $trackingLocation->id) }}`);
						}, 1000);
					}
				})
				.fail(error => {
					submitBtn.ladda('stop')

					const { responseJSON, status } = error;
					const { message, errors } = responseJSON

					if(status == 422) {
						invalidResponse(form, errors);
					}

					toastrAlert();
					toastr.warning(message, 'Peringatan')
				})
			})
		}


		const init = () => {
			buttonEvent();
			play();
			getLocation();
			formSubmit();
		}

		init();
	});
</script>
@endsection
