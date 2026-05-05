@extends('template.backLayout')

@section('content')
<div class="row">
	<div class="col-md-12">
		<form id="takePhotoForm">
			<div class="row">
				<div class="col-lg-6 grid-margin">
					<div class="card support-pane-card">
						<div class="card-body">
                            <input type="hidden" name="id_employee" value="{{ $trainingParticipant->id_employee }}">
                            <input type="hidden" name="id_training" value="{{ $trainingParticipant->id_training }}">
							<div class="support-pane">
								<label> Foto </label> <br>
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

								<div class="form-group">
									<input type="hidden" name="blobImage">
									<small class="invalid-feedback"></small>
								</div>

								<hr>

								<button type="submit" class="btn btn-warning btn-block mt-2">
									<i class="mdi mdi-check"></i> Simpan Foto
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


@section('script')
<script type="text/javascript">
	$(function(){

		const form = $('#takePhotoForm');
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
			$('[name="blobImage"]').val(dataUrl);
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
				$('[name="blobImage"]').val('');
				$(this).hide();
				$('.downloadBtn').hide();
				$('.stopBtn').show();
				$('.captureBtn').show();
			})
		}


		const validateInput = () => {
			let blobImage = $('[name="blobImage"]').val(),
				latitude = $('[name="latitude"]').val(),
				longitude = $('[name="longitude"]').val();

			if(isEmpty(blobImage)) {
				swal("Peringatan", "Wajib melakukan capture", "error");
				return false;
			} else {
				return true;
			}
		}


		const attendanceSubmit = () => {
			$('#takePhotoForm').on('submit', function(e){
				e.preventDefault();

				if(!validateInput()) {
					return;
				}

				let formData = $(this).serialize();

				submitBtn.ladda('start')
				ajaxSetup();
				$.ajax({
					url : `{{ route('employee.training.save_take_photo') }}`,
					method : 'post',
					data : formData,
					dataType : 'json',
				})
				.done(response => {
					const { message, code } = response

					if(code == 200) {
						toastrAlert();
						toastr.success(message, 'Berhasil');

						setTimeout(() => {
							window.location.replace(`{{ route('employee.training') }}`);
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
			attendanceSubmit();
		}

		init();
	});
</script>
@endsection
