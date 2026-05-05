@extends('template.backLayout')


@section('content')
<div class="vertical-tab" style="display: block;">
	<div class="row">
			
		<div class="col-lg-4">
			<ul class="nav nav-tabs tab-solid tab-solid-primary mr-4" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="link-tab-general" data-toggle="tab" href="#tab-general" role="tab" aria-controls="tab-general" aria-selected="true"> Umum </a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="link-tab-login-background" data-toggle="tab" href="#tab-login-background" role="tab" aria-controls="tab-login-background" aria-selected="false"> Background Login </a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="link-tab-notification-to-admin" data-toggle="tab" href="#tab-notification-to-admin" role="tab" aria-controls="tab-notification-to-admin" aria-selected="false"> Notifikasi Ke Admin </a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="link-tab-attendance-web-mobile" data-toggle="tab" href="#tab-attendance-web-mobile" role="tab" aria-controls="tab-attendance-web-mobile" aria-selected="false"> Kehadiran Via Web & Android </a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="link-tab-elearning" data-toggle="tab" href="#tab-elearning" role="tab" aria-controls="tab-elearning" aria-selected="false"> E-Learning </a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="link-tab-activate-menu" data-toggle="tab" href="#tab-activate-menu" role="tab" aria-controls="tab-activate-menu" aria-selected="false"> Pengaktifan Menu </a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="link-tab-api-integration" data-toggle="tab" href="#tab-api-integration" role="tab" aria-controls="tab-api-integration" aria-selected="false"> Integrasi / API </a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="link-tab-data-storage" data-toggle="tab" href="#tab-data-storage" role="tab" aria-controls="tab-data-storage" aria-selected="false"> Penyimpanan Data </a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="link-tab-developer" data-toggle="tab" href="#tab-developer" role="tab" aria-controls="tab-developer" aria-selected="false"> Pengaturan Developer </a>
				</li>
			</ul>
		</div>

		<div class="col-lg-8">
			<div class="tab-content tab-content-solid">


				<!-- Setting Umum -->
				<div class="tab-pane fade show active" id="tab-general" role="tabpanel" aria-labelledby="link-tab-general">
					<div class="row">
						<div class="col-lg-8">
							<div class="card support-pane-card">
								<div class="card-body">
									{!! Template::titleBanner('Umum') !!}

									<form id="general-form">
										{!! Template::requiredBanner() !!}
										
										<div class="form-group">
											<label> Nama Aplikasi {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* Muncul di halaman login dan dashboard (pojok kiri atas).
											</p>
											<input type="text" name="app_name" class="form-control" placeholder="Nama Aplikasi" value="{{ setting('app_name', '') }}" required>
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
				</div>
				<!-- End Setting Umum -->


				<!-- Setting Background Login -->
				<div class="tab-pane fade" id="tab-login-background" role="tabpanel" aria-labelledby="link-tab-login-background">
					<div class="row">
						<div class="col-lg-8">
							<div class="card support-pane-card">
								<div class="card-body">
									{!! Template::titleBanner('Background Login') !!}

									<form id="login-background-form">
										{!! Template::requiredBanner() !!}
										
										<div class="form-group">
											<label> Foto Background </label>
											<p class="text-muted text-medium">
												* Foto background untuk halaman login. Isi jika ingin mengganti background.
											</p>
											<input type="file" name="background_image" class="form-control">
											<span class="invalid-feedback"></span>
										</div>

										<div class="form-group">
											<label> Efek Blur </label>
											<p class="text-muted text-medium">
												* Efek blur foto.
											</p>
											<div class="input-group">
												<input type="number" name="background_blur" class="form-control" value="{{ Setting::getValue('background_blur', 0) }}" placeholder="Tingkat blur background" step="0.1">
												<div class="input-group-append">
													<span class="input-group-text">px</span>
												</div>
											</div>
											<span class="invalid-feedback"></span>
										</div>

										<div class="form-group">
											<label> Preview </label>
											<p class="text-muted text-medium">
												* Preview foto.
											</p>
											<img src="{{ Setting::getLoginBackgroundLink() }}" class="img-fluid" id="preview-bg">
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
				</div>
				<!-- End Setting Background Login -->




				<!-- Notification To Admin -->
				<div class="tab-pane fade" id="tab-notification-to-admin" role="tabpanel" aria-labelledby="link-tab-notification-to-admin">
					<div class="row">
						<div class="col-lg-8">
							<div class="card support-pane-card">
								<div class="card-body">
									{!! Template::titleBanner('Notifikasi Ke Admin') !!}

									<form id="notification-to-admin-form">
										{!! Template::requiredBanner() !!}
										
										<div class="form-group">
											<label> Nomor Whatsapp Admin {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* Pisahkan dengan koma (,) jika lebih dari satu. <br>
												* Contoh : 6282316425264,6282316425264
											</p>
											<input type="text" name="admin_whatsapp_number" class="form-control" placeholder="Nomor Whatsapp Admin" value="{{ setting('admin_whatsapp_number', '6282316425264') }}" required>
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
				</div>
				<!-- End Notification To Admin -->




				<!-- Setting Kehadiran -->
				<div class="tab-pane fade" id="tab-attendance-web-mobile" role="tabpanel" aria-labelledby="link-tab-attendance-web-mobile">
					<div class="row">
						<div class="col-lg-8">
							<div class="card support-pane-card">
								<div class="card-body">
									{!! Template::titleBanner('Kehadiran Via Web & Android') !!}

									<form id="attendance-web-mobile-form">
										{!! Template::requiredBanner() !!}
										
										<div class="form-group">
											<label> Komparasi Wajah {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* Status komparasi wajah jika mengisi kehadiran lewat web atau aplikasi android. Jika status aktif maka akan melakukan komparasi dengan database karyawan.
											</p>
											<select class="form-control" name="is_using_face_compare_for_attendance" required>
												<option value="yes"> Aktif </option>
												<option value="no"> Nonaktif </option>
											</select>
											<span class="invalid-feedback"></span>
										</div>

										<div class="form-group">
											<label> Persentase Minimal Kemiripan Komparasi Wajah (%) {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* Persentase minimal kemiripan wajah ketika mengisi kehadiran lewat web atau aplikasi android.
											</p>
											<input type="number" name="face_compare_similarity_for_attendance" class="form-control" placeholder="1-100" value="{{ setting('face_compare_similarity_for_attendance', 70) }}" min="1" max="100" required>
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
				</div>
				<!-- End Setting Kehadiran -->


				<!-- Setting E-Learning -->
				<div class="tab-pane fade" id="tab-elearning" role="tabpanel" aria-labelledby="link-tab-elearning">
					<div class="row">
						<div class="col-lg-8">
							<div class="card support-pane-card">
								<div class="card-body">
									{!! Template::titleBanner('E-Learning') !!}

									<form id="elearning-form">
										{!! Template::requiredBanner() !!}
										
										<div class="form-group">
											<label> Persentase Minimal Untuk Lulus Ujian (%) {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* Peserta ujian akan dinyatakan lulus jika hasil ujian lebih dari atau sama dengan minimum persentase yang telah ditentukan.
											</p>
											<input type="number" name="minimum_percentage_for_exam_passed" class="form-control" placeholder="1-100" value="{{ setting('minimum_percentage_for_exam_passed', 100) }}" min="1" max="100" required>
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
				</div>
				<!-- End Setting E-Learning -->


				<!-- Setting Pengaktifan Menu -->
				<div class="tab-pane fade" id="tab-activate-menu" role="tabpanel" aria-labelledby="link-tab-activate-menu">
					<div class="row">
						<div class="col-lg-8">
							<div class="card support-pane-card">
								<div class="card-body">
									{!! Template::titleBanner('Pengaktifan Menu') !!}

									<form id="activate-menu-form">
										{!! Template::requiredBanner() !!}

										<div class="form-group">
											<label> Menu Pengajuan {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* Modul pengajuan cuti, lembur, izin datang terlambat/pulang cepat.
											</p>
											<select class="form-control" name="menu_submission" required>
												<option value="yes"> Aktif </option>
												<option value="no"> Nonaktif </option>
											</select>
											<span class="invalid-feedback"></span>
										</div>

										<div class="form-group">
											<label> Menu Pendaftaran Karyawan {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* Modul aplikasi pendaftaran untuk karyawan. Jika nonaktifkan maka laman buat akun tidak akan bisa digunakan
											</p>
											<select class="form-control" name="menu_registration" required>
												<option value="yes"> Aktif </option>
												<option value="no"> Nonaktif </option>
											</select>
											<span class="invalid-feedback"></span>
										</div>

										<div class="form-group">
											<label> Menu Pengumuman {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* Modul pengumuman ke karyawan
											</p>
											<select class="form-control" name="menu_announcement" required>
												<option value="yes"> Aktif </option>
												<option value="no"> Nonaktif </option>
											</select>
											<span class="invalid-feedback"></span>
										</div>

										<div class="form-group">
											<label> Menu Surat Peringatan {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* Modul surat peringatan untuk karyawan
											</p>
											<select class="form-control" name="menu_warning_letter" required>
												<option value="yes"> Aktif </option>
												<option value="no"> Nonaktif </option>
											</select>
											<span class="invalid-feedback"></span>
										</div>

										<div class="form-group">
											<label> Menu E-Learning {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* Modul e-learning, course, exam
											</p>
											<select class="form-control" name="menu_elearning" required>
												<option value="yes"> Aktif </option>
												<option value="no"> Nonaktif </option>
											</select>
											<span class="invalid-feedback"></span>
										</div>

										<div class="form-group">
											<label> Menu Log Face Terminal {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* Modul log face terminal
											</p>
											<select class="form-control" name="menu_face_terminal_log" required>
												<option value="yes"> Aktif </option>
												<option value="no"> Nonaktif </option>
											</select>
											<span class="invalid-feedback"></span>
										</div>

										<div class="form-group">
											<label> Menu Device Face Terminal {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* Modul untuk mengelola device face terminal.
											</p>
											<select class="form-control" name="menu_face_terminal_device" required>
												<option value="yes"> Aktif </option>
												<option value="no"> Nonaktif </option>
											</select>
											<span class="invalid-feedback"></span>
										</div>
										
										<div class="form-group">
											<label> Menu Tracking Sales {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* Modul aplikasi tracking point untuk sales.
											</p>
											<select class="form-control" name="menu_sales_tracking" required>
												<option value="yes"> Aktif </option>
												<option value="no"> Nonaktif </option>
											</select>
											<span class="invalid-feedback"></span>
										</div>

										<div class="form-group">
											<label> Menu Tracking {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* Modul aplikasi tracking point.
											</p>
											<select class="form-control" name="menu_tracking" required>
												<option value="yes"> Aktif </option>
												<option value="no"> Nonaktif </option>
											</select>
											<span class="invalid-feedback"></span>
										</div>

										<div class="form-group">
											<label> Menu Lanjutan {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* Modul lanjutan untuk identifikasi foto dengan data karyawan (Tidak berpengaruh untuk komparasi saat isi kehadiran) dan mengirim pesan ke karyawan.
											</p>
											<select class="form-control" name="menu_advance" required>
												<option value="yes"> Aktif </option>
												<option value="no"> Nonaktif </option>
											</select>
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
				</div>
				<!-- End Setting Pengaktifan Menu -->


				<!-- Setting Integrasi / API -->
				<div class="tab-pane fade" id="tab-api-integration" role="tabpanel" aria-labelledby="link-tab-api-integration">
					<div class="row">
						<div class="col-lg-8">
							<div class="card support-pane-card">
								<div class="card-body">
									{!! Template::titleBanner('Integrasi / API') !!}

									<form id="api-integration-form">
										{!! Template::requiredBanner() !!}
										
										<div class="form-group">
											<label> API Key Integrasi {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* API Key untuk integrasi antar aplikasi
											</p>
											<input type="text" name="hris_api_key" class="form-control" placeholder="API Key Integrasi" value="{{ setting('hris_api_key', 'API_KEY') }}" readonly>
											<span class="invalid-feedback"></span>
										</div>

										<div class="form-group">
											<label class="d-block mb-3"> Dokumentasi </label>
											<a href="javascript:void(0);" class="google-btn">
												Download Dokumentasi API
											</a>
										</div>

										<hr>

										<div class="form-group">
											<button class="btn btn-success" type="submit">
												<i class="mdi mdi-sync"></i> Reset API Key
											</button>
										</div>
									</form>

								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Setting Integrasi / API -->
				

				<!-- Setting Penyimpanan Data -->
				<div class="tab-pane fade" id="tab-data-storage" role="tabpanel" aria-labelledby="link-tab-data-storage">
					<div class="row">
						<div class="col-lg-8">
							<div class="card support-pane-card">
								<div class="card-body">
									{!! Template::titleBanner('Penyimpanan Data') !!}

									<form id="data-storage-form">
										{!! Template::requiredBanner() !!}
										
										<div class="form-group">
											<label> Masa Umur Maksimal Data Foto (Dalam Hari) {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* Data foto yang sudah lewat waktu yang ditentukan akan dihapus dari server. Berlaku untuk foto kehadiran dan face terminal log. Penghapusan foto dilakukan setiap jam 00:00
											</p>
											<input type="number" name="max_photo_age" class="form-control" placeholder="Nama Aplikasi" value="{{ setting('max_photo_age', '60') }}" min="1" required>
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
				</div>
				<!-- End Setting Penyimpanan Data -->


				<!-- Setting Umum -->
				<div class="tab-pane fade" id="tab-developer" role="tabpanel" aria-labelledby="link-tab-developer">
					<div class="row">
						<div class="col-lg-8">
							<div class="card support-pane-card">
								<div class="card-body">
									{!! Template::titleBanner('Pengaturan Developer') !!}

									<form id="developer-form">
										{!! Template::dangerBanner('Pengaturan ini hanya boleh disetting oleh developer / administrator dengan izin dari developer') !!}
										{!! Template::requiredBanner() !!}
										
										<div class="form-group">
											<label> URL Relay Face Terminal {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* URL Relay NodeJS Express.
											</p>
											<input type="text" name="relay_face_terminal_url" class="form-control" placeholder="http://" value="{{ setting('relay_face_terminal_url', 'http://localhost:2000') }}" required>
											<span class="invalid-feedback"></span>
										</div>

										<div class="form-group">
											<label> URL WS Log Face Terminal </label>
											<p class="text-muted text-medium">
												* URL websocket untuk log face terminal.
											</p>
											<input type="text" name="recent_ws_url" class="form-control" placeholder="ws://" value="{{ setting('recent_ws_url') }}">
											<span class="invalid-feedback"></span>
										</div>

										<div class="form-group">
											<label> URL Whatsapp API {!! Template::required() !!} </label>
											<p class="text-muted text-medium">
												* URL Whatsapp API.
											</p>
											<input type="text" name="whatsapp_url_server" class="form-control" placeholder="http://" value="{{ setting('whatsapp_url_server', 'http://103.242.105.85:50000') }}" required>
											<span class="invalid-feedback"></span>
										</div>

										<hr>

										<div class="form-group">
											<button class="btn btn-success" type="submit">
												<i class="mdi mdi-check"></i> Simpan
											</button>
											<a class="btn btn-primary" href="{{ route('developer.face_terminal_log') }}">
												<i class="mdi mdi-magnify"></i> Developer
											</a>
										</div>
									</form>

								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Setting Umum -->


			</div>
		</div>
			
	</div>

</div>


@endsection


@section('script')
<script type="text/javascript">
	$(function(){

		const reload = (ms = 1000) => {
			setTimeout(() => {
				window.location.reload();
			}, ms)
		}

		let $generalForm = $('#general-form')
		let $generalFormSubmitBtn = $generalForm.find('[type="submit"]').ladda();

		$generalForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();
			$generalFormSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('setting.save_general') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				reload()
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$generalFormSubmitBtn.ladda('stop')
				ajaxErrorHandling(error, $generalForm)
			})
		});



		let $loginBackgroundForm = $('#login-background-form')
		let $loginBackgroundFormSubmitBtn = $loginBackgroundForm.find('[type="submit"]').ladda();

		const updatePreview = () => {
			let blur = $loginBackgroundForm.find(`[name="background_blur"]`).val();
			$loginBackgroundForm.find('#preview-bg').attr('style', `filter: blur(${blur}px)`);
		}

		updatePreview();

		$loginBackgroundForm.find('[name="background_blur"]').on('change keyup', function(){
			updatePreview();
		})

		$loginBackgroundForm.find('[name="background_photo"]').on('change', function(){
			let file = $(this).val();
			
			if(!isEmpty(file)) {
				let fileType = this.files[0].type;

				if(fileType.substring(0, 5) != "image") {
					toastrAlert();
					toastr.warning('File harus berupa foto', 'Peringatan')
					$(this).val('');
				} else {
					let reader = new FileReader();

					reader.onload = function(e) {
						$loginBackgroundForm.find('#preview-bg').attr('src', e.target.result);
					}

					reader.readAsDataURL(this.files[0]);

					$loginBackgroundForm.find('#preview-bg').show();
				}
			} else {
				$loginBackgroundForm.find('#preview-bg').hide();
			}
		});

		$loginBackgroundForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);

			$loginBackgroundFormSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('setting.save_login_background') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				contentType : false,
				processData : false,
			})
			.done(response => {
				$loginBackgroundFormSubmitBtn.ladda('stop')
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$loginBackgroundFormSubmitBtn.ladda('stop')
				ajaxErrorHandling(error, $loginBackgroundForm)
			})
		});



		let $notificationToAdminForm = $('#notification-to-admin-form')
		let $notificationToAdminFormSubmitBtn = $notificationToAdminForm.find('[type="submit"]').ladda();

		$notificationToAdminForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();
			$notificationToAdminFormSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('setting.save_notification_to_admin') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				$notificationToAdminFormSubmitBtn.ladda('stop')
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$notificationToAdminFormSubmitBtn.ladda('stop')
				ajaxErrorHandling(error, $notificationToAdminForm)
			})
		});



		let $attendanceWebMobileForm = $('#attendance-web-mobile-form')
		let $attendanceWebMobileFormSubmitBtn = $attendanceWebMobileForm.find('[type="submit"]').ladda();

		$attendanceWebMobileForm.find(`[name="is_using_face_compare_for_attendance"]`).val(`{{ setting('is_using_face_compare_for_attendance', 'yes') }}`)
		$attendanceWebMobileForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();
			$attendanceWebMobileFormSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('setting.save_attendance_web_mobile') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				$attendanceWebMobileFormSubmitBtn.ladda('stop')
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$attendanceWebMobileFormSubmitBtn.ladda('stop')
				ajaxErrorHandling(error, $attendanceWebMobileForm)
			})
		});




		let $elearningForm = $('#elearning-form')
		let $elearningFormSubmitBtn = $elearningForm.find('[type="submit"]').ladda();

		$elearningForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();
			$elearningFormSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('setting.save_elearning') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				$elearningFormSubmitBtn.ladda('stop')
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$elearningFormSubmitBtn.ladda('stop')
				ajaxErrorHandling(error, $elearningForm)
			})
		});



		let $activateMenuForm = $('#activate-menu-form')
		let $activateMenuFormSubmitBtn = $activateMenuForm.find('[type="submit"]').ladda();

		$activateMenuForm.find(`[name="menu_submission"]`).val(`{{ setting('menu_submission', 'yes') }}`)
		$activateMenuForm.find(`[name="menu_registration"]`).val(`{{ setting('menu_registration', 'yes') }}`)
		$activateMenuForm.find(`[name="menu_announcement"]`).val(`{{ setting('menu_announcement', 'yes') }}`)
		$activateMenuForm.find(`[name="menu_warning_letter"]`).val(`{{ setting('menu_warning_letter', 'yes') }}`)
		$activateMenuForm.find(`[name="menu_elearning"]`).val(`{{ setting('menu_elearning', 'yes') }}`)
		$activateMenuForm.find(`[name="menu_face_terminal_log"]`).val(`{{ setting('menu_face_terminal_log', 'yes') }}`)
		$activateMenuForm.find(`[name="menu_face_terminal_device"]`).val(`{{ setting('menu_face_terminal_device', 'yes') }}`)
		$activateMenuForm.find(`[name="menu_sales_tracking"]`).val(`{{ setting('menu_sales_tracking', 'yes') }}`)
		$activateMenuForm.find(`[name="menu_tracking"]`).val(`{{ setting('menu_tracking', 'yes') }}`)
		$activateMenuForm.find(`[name="menu_advance"]`).val(`{{ setting('menu_advance', 'yes') }}`)
		$activateMenuForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();
			$activateMenuFormSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('setting.save_activate_menu') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				reload()
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$activateMenuFormSubmitBtn.ladda('stop')
				ajaxErrorHandling(error, $activateMenuForm)
			})
		});



		let $apiIntegrationForm = $('#api-integration-form')
		let $apiIntegrationFormSubmitBtn = $apiIntegrationForm.find('[type="submit"]').ladda();

		$apiIntegrationForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();
			$apiIntegrationFormSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('setting.save_api_integration') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				reload()
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$apiIntegrationFormSubmitBtn.ladda('stop')
				ajaxErrorHandling(error, $dataStorageForm)
			})
		});





		let $dataStorageForm = $('#data-storage-form')
		let $dataStorageFormSubmitBtn = $dataStorageForm.find('[type="submit"]').ladda();

		$dataStorageForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();
			$dataStorageFormSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('setting.save_data_storage') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				$dataStorageFormSubmitBtn.ladda('stop')
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$dataStorageFormSubmitBtn.ladda('stop')
				ajaxErrorHandling(error, $dataStorageForm)
			})
		});



		let $developerForm = $('#developer-form')
		let $developerFormSubmitBtn = $developerForm.find('[type="submit"]').ladda();

		$developerForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();
			$developerFormSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('setting.save_developer') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				$developerFormSubmitBtn.ladda('stop')
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$developerFormSubmitBtn.ladda('stop')
				ajaxErrorHandling(error, $developerForm)
			})
		});
	});
</script>
@endsection


@section('style')
<style type="text/css">
	
	.text-medium {
		font-size: 90%;
	}

</style>
@endsection