@extends('template.authLayout')


@section('content')
<div class="row w-100 mx-auto">
	<div class="col-lg-4 mx-auto px-4">
		<div class="auto-form-wrapper py-5">
			<h3 align="center" class="mb-3"> {{ Setting::getValue('app_name', 'Attendance App') }} </h3>
			<form id="loginForm">

				<div class="form-group">
					<label class="label"> Username </label>
					<div class="input-group">
						<input type="text" class="form-control loginBorder" placeholder="Username" name="username" autocomplete="off" required>
						<div class="input-group-append">
							<span class="input-group-text loginBorder loginText">
							</span>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label class="label"> Password </label>
					<div class="input-group">
						<input type="password" class="form-control loginBorder" placeholder="Password" name="password" required>
						<div class="input-group-append">
							<span class="input-group-text loginBorder loginText">
							</span>
						</div>
					</div>
				</div>

				<div class="form-group">
					<input type="checkbox" id="show-password">
					<label for="show-password" class="mt-1"> Show Password </label>
				</div>

				<div class="form-group mb-3">
					<button class="btn btn-primary submit-btn btn-block" type="submit">
					<i class="mdi mdi-login"></i> Login
					</button>
				</div>

				<p class="text-center textMsg"><br></p>

				@if(setting('menu_registration', 'yes') == 'yes')
				<div class="text-block text-center my-3">
					<span class="text-small font-weight-semibold"> Belum memiliki akun ?</span>
					<a href="{{ route('access_register') }}" class="text-black text-small"> Daftar sekarang </a>
				</div>
				@endif

			</form>
		</div>
		<p class="footer-text text-center text-white mt-3">
			Copyright © {{ date('Y') }} {{ appconfig('developer_name') }}. All rights reserved.
		</p>
	</div>
</div>
@endsection


@section('script')
<script>
	$(function(){
		let form = $('#loginForm');
		let submitBtn = form.find(`[type="submit"]`).ladda();

		const clearError = () => {
			$('.loginBorder').removeClass('border-danger');
			$('.loginBorder').removeClass('text-danger');
			$('.loginBorder').removeClass('border-success');
			$('.loginBorder').removeClass('text-success');
			$('.loginText').html('<i class="mdi mdi-check-circle-outline"></i>');
		}

		form.on('submit', function(e) {
			e.preventDefault();
			clearError();

			submitBtn.ladda('start');

			let formData = $(this).serialize();

			ajaxSetup();
			$.ajax({
				url : `{{ route('login') }}`,
				method : "post",
				data : formData,
			})
			.done(response => {
					$('.loginBorder').addClass('border-success');
					$('.loginText').addClass('text-success');
					$('.loginText').html('<i class="mdi mdi-check-circle-outline"></i>');
					$('.textMsg').removeClass("text-danger");
					$('.textMsg').html("Login berhasil, laman sedang dialihkan..");
					$('.textMsg').addClass("text-success");
				setTimeout(() => {
					@if(isset($_GET['redirect']))
					window.location.replace("{{ $_GET['redirect'] }}");
					@else
					window.location.replace("{{ url('dashboard') }}");
					@endif
				}, 1000)
			})
			.fail(error => {
				submitBtn.ladda('stop')
					$('.loginBorder').addClass('border-danger');
					$('.loginText').addClass('text-danger');
					$('.loginText').html('<i class="mdi mdi-close-circle-outline"></i>');
					$('.textMsg').html("Username/Password salah");
					$('.textMsg').addClass("text-danger");
			});
		});

		$('.form-control').on('change, keyup', function(){
			clearError();
		});

		$('#show-password').on('change', function(){
			if($(this).prop('checked') == true) {
				form.find(`[name="password"]`).attr('type', 'text')
			} else {
				form.find(`[name="password"]`).attr('type', 'password')
			}
		})
	})
</script>
@endsection