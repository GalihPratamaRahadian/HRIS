@extends('template.backLayout')


@section('style')
<style type="text/css">
	.frame-image {
		height: 300px; 
		width: 400px; 
		position: absolute; 
		top: 0px; 
		left: 0px;
	}

	.photo-image {
		height: 300px; 
		width: 400px; 
		display: none;
	}
</style>
@endsection


@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">

				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					{!! Template::requiredBanner() !!}
					
					<div class="row">
						<div class="col-lg-6">

							<div class="form-group">
								<label> No Induk Karyawan </label>
								<input type="number" name="employee_number" class="form-control" placeholder="No Induk Karyawan">
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Nama Karyawan {!! Template::required() !!} </label>
								<input type="text" name="employee_name" class="form-control" placeholder="Nama Karyawan" required>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Jenis Kelamin {!! Template::required() !!} </label> <br>
								<select name="gender" style="width: 100%;" required>
									<option value="L">Laki - laki</option>
									<option value="P">Perempuan</option>
								</select>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Email </label>
								<input type="email" name="email" class="form-control" placeholder="Alamat Email">
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Nomor Telepon {!! Template::required() !!} </label>
								<input type="tel" name="phone_number" class="form-control" placeholder="628xxxxxxxxxx" required>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Departemen {!! Template::required() !!} </label><br>
								<select name="id_department" style="width: 100%;" required>

									@foreach(\App\Models\Department::all() as $department) {
									<option value="{{ $department->id }}"> {{ $department->department_name }} </option>
									@endforeach

								</select>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Jabatan {!! Template::required() !!} </label><br>
								<select name="id_position" style="width: 100%;" required>
								</select>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Jam Kerja </label><br>
								<select name="id_shift" style="width: 100%;" required>

									@foreach(\App\Models\Shift::all() as $shift) {
									<option value="{{ $shift->id }}"> {{ $shift->shift_name }} </option>
									@endforeach

								</select>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Nomor Jamsostek </label>
								<input type="number" name="jamsostek" class="form-control" placeholder="Nomor Jamsostek">
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Status Pekerjaan {!! Template::required() !!} </label><br>
								<select name="job_status" style="width: 100%;" required>
									<option value="{{ \App\Models\Employee::JOBSTATUS_TETAP }}"> Tetap </option>
									<option value="{{ \App\Models\Employee::JOBSTATUS_KONTRAK }}"> Kontrak </option>
								</select>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Tanggal Mulai Bekerja </label>
								<input type="date" name="start_working_date" class="form-control">
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Tempat Lahir {!! Template::required() !!} </label>
								<input type="text" name="place_of_birth" class="form-control" placeholder="Tempat Lahir" required>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Tanggal Lahir {!! Template::required() !!} </label>
								<input type="date" name="date_of_birth" class="form-control" required>
								<small class="invalid-feedback"></small>
							</div>

						</div>

						<div class="col-lg-6">

							<div class="form-group">
								<label> Alamat (Sesuai KTP) {!! Template::required() !!} </label>
								<textarea class="form-control" name="address" placeholder="Alamat (Sesuai KTP)" rows="2" required></textarea>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Pendidikan Terakhir {!! Template::required() !!} </label>
								<select class="form-control" name="last_education" required>
									<option value="" selected disabled> - Pilih Pendidikan Terakhir - </option>
									@foreach(GlobalData::educationLevel() as $education)
									<option value="{{ $education }}"> {{ $education }} </option>
									@endforeach
								</select>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Jurusan Pendidikan Terakhir {!! Template::required() !!} </label>
								<input type="text" name="last_education_major" class="form-control" placeholder="Jurusan Pendidikan Terakhir" required>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Status Pernikahan {!! Template::required() !!} </label>
								<select class="form-control" name="marital_status" required>
									<option value="" selected disabled> - Pilih Status Pernikahan - </option>
									@foreach(GlobalData::maritalStatus() as $maritalStatus)
									<option value="{{ $maritalStatus }}"> {{ $maritalStatus }} </option>
									@endforeach
								</select>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Golongan Darah </label>
								<select class="form-control" name="blood_type">
									<option value="" selected disabled> - Pilih Golongan Darah - </option>
									@foreach(GlobalData::bloodType() as $bloodType)
									<option value="{{ $bloodType }}"> {{ $bloodType }} </option>
									@endforeach
								</select>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> No KTP {!! Template::required() !!} </label>
								<input type="text" name="ktp_number" class="form-control" placeholder="No KTP" required>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> No NPWP </label>
								<input type="text" name="npwp_number" class="form-control" placeholder="No NPWP">
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group text-center">
								<label> Foto {!! Template::required() !!} </label>
								<textarea name="photo" class="photo_blob" style="display: none;"></textarea>
								<center>
									<div class="w-100" style="align-items: center;">
										<div style="position: relative; height: 300px; width: 400px;">
											<video style="height: 300px; width: 400px;" class="video" autoplay="true">
											</video>
											<img src="{{ asset('images/system/frameCrop1.png') }}" class="frame-image">
											<img src="#" style="" class="photo-image" alt="Memuat gambar..">
											<canvas id="photo-canvas" style="display: none;"></canvas>
										</div>
									</div>
								</center>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group mt-3 text-center">
								<button type="button" class="btn btn-primary btn-sm campTakeBtn"><i class="mdi mdi-camera"></i> Ambil Gambar</button>
								<button type="button" class="btn btn-danger btn-sm campStopBtn" data-play="yes"><i class="mdi mdi-stop"></i> Stop</button>
								<button type="button" class="btn btn-danger btn-sm camDeleteBtn" style="display: none;"><i class="mdi mdi-trash-can"></i> Hapus</button>
								<a href="#" style="display: none;" class="btn btn-success btn-sm camDownloadBtn" download="photo.jpg"><i class="mdi mdi-download"></i> Unduh</a>
							</div>

							<div class="form-group">
								<div class="progress" style="width: 100%; display: none;">
									<div class="progress-bar progress-bar-striped progress-bar-animated progress-bar-success" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
								</div>
								<p class="progress-text mt-1" style="width: 100%; display: none;" align="center"><strong>60%</strong></p>
							</div>
						</div>
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
<script type="text/javascript">
	$(function(){

		let $form = $('#mainForm')
		let $submitBtn = $form.find('[type="submit"]').ladda();
		let canvas = null;
		let ctx = null;
		let video = document.querySelector(".video");
		let mediaStream = null;

		const resetForm = () => {
			$form[0].reset();
			$('[name="gender"], [name="id_department"], [name="id_position"], [name="job_status"], [name="id_shift"], [name="last_education"], [name="marital_status"], [name="blood_type"]').val('').trigger('change');
		}

		const init = () => {
			resetForm();
			$('[name="department_name"]').focus();
			canvas = document.getElementById("photo-canvas");
			ctx = canvas.getContext('2d');

			cameraResultDelete();
			cameraStart();
		}


		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			// photo is required
			if(isEmpty($('[name="photo"]').val())) {
				toastrAlert();
				toastr.warning('Wajib mengisi foto', 'Peringatan')
				return;
			}

			let formData = $(this).serialize();
			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('employee.store') }}`,
				method : 'post',
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
				ajaxErrorHandling(error, form)
			})
		});

		$form.find('[name="gender"]').select2({
			placeholder : '- Pilih Jenis Kelamin -'
		});

		$form.find('[name="id_department"]').select2({
			placeholder : '- Pilih Departemen -'
		});

		$form.find('[name="id_position"]').select2({
			placeholder : '- Pilih Jabatan -'
		});

		$form.find('[name="job_status"]').select2({
			placeholder : '- Pilih Jabatan -'
		});	

		$form.find('[name="id_shift"]').select2({
			placeholder : '- Pilih Jam Kerja -'
		});

		$form.find('[name="last_education"]').select2({
			placeholder : 'Pilih Pendidikan Terakhir'
		})
		$form.find('[name="last_education"]').val('').trigger('change');

		$form.find('[name="marital_status"]').select2({
			placeholder : 'Pilih Status Pernikahan'
		})
		$form.find('[name="marital_status"]').val('').trigger('change');

		$form.find('[name="blood_type"]').select2({
			placeholder : 'Pilih Golongan Darah'
		})
		$form.find('[name="blood_type"]').val('').trigger('change');

		$form.find(`[name="id_department"]`).on('change', function(){
			const departmentId = $(this).val()
			$.get({
				url: `{{ route('helper.get_positions') }}?id_department=${departmentId}`,
				dataType: 'json'
			})
			.done(response => {
				const { positions } = response
				let html = ''
				positions.forEach(position => {
					html += `<option value="${position.id}"> ${position.position_name} </option>`
				})

				$form.find(`[name="id_position"]`).html(html)
				$form.find(`[name="id_position"]`).val('').trigger('change')

				positionsReq++;
			})
		})


		$form.find('.camOpenBtn').on('click', function(){
			cameraStart();
		});

		$form.find(".campTakeBtn").on('click', function(){
			cameraCapture();
			$('.video').hide();
			$('.photo-image').show();
			$('.campTakeBtn').hide();
			$('.campStopBtn').hide();
			$('.camDownloadBtn').show();
			$('.camDeleteBtn').show();
		});

		$form.find(".campStopBtn").on('click', function(){
			let play = $(this).data('play');

			if(play == "yes") {
				cameraStop();
			} else {
				cameraStart();
			}
			
		});

		$form.find('.camDeleteBtn').on('click', function(){
			cameraResultDelete()
		});


		const cameraResultDelete = () => {
			$('.photo-image').attr('src', '#');
			$('.blobImage').val('');
			$('.photo-image').hide();
			$('.video').show();
			$('.campTakeBtn').show();
			$('.campStopBtn').show();
			$('.camDownloadBtn').hide();
			$('.camDeleteBtn').hide();
		}


		const cameraStart = () => {
			if (navigator.mediaDevices.getUserMedia) {
				navigator.mediaDevices.getUserMedia({ video: true })
				.then(function (stream) {
					video.srcObject = stream;
					mediaStream = stream;


					$('.campTakeBtn').removeAttr('disabled');
					$(".campStopBtn").data('play', 'yes');
					$(".campStopBtn").removeClass('btn-success');
					$(".campStopBtn").addClass('btn-danger');
					$(".campStopBtn").html("<i class='mdi mdi-stop'></i> Stop");
				})
				.catch(function (err) {
					console.log("Something went wrong!");
					console.log(err);
				});
			}
		}


		const cameraCapture = () => {
			var scale = 1;
			canvas.width = 900*scale;
			canvas.height = 700*scale;
			ctx.drawImage(video, 0,0, canvas.width, canvas.height);
			canvas.toBlob(function(blob){
				var url = window.URL.createObjectURL(blob);
				$('.photo-image').attr('src', url);
				$('.camDownloadBtn').attr('href', url);
			});
			let dataUrl = canvas.toDataURL("image/jpeg");
			$('[name="photo"]').val(dataUrl);
			
		}

		const cameraStop = () => {
			if(mediaStream != null) {
				if(mediaStream.active == true) {
					mediaStream.getTracks()[0].stop();
				}
			} 
			$('.campTakeBtn').attr('disabled', '');
			$('.campStopBtn').data('play', 'no');
			$('.campStopBtn').removeClass('btn-danger')
							 .addClass('btn-success');
			$('.campStopBtn').html("<i class='mdi mdi-play'></i> Play");
		}

		init();
	});
</script>
@endsection