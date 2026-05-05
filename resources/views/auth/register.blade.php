@extends('template.authLayout')


@section('content')
<div class="row w-100 mx-auto">
	<div class="col-lg-4 mx-auto px-4">
		<div class="auto-form-wrapper py-5">

			<h3 align="center" class="mb-3"> Pendaftaran </h3>

			<form id="mainForm">

				<div class="form-group">
					<label class="label"> Nama Lengkap </label>
					<input type="text" class="form-control" placeholder="Nama Lengkap" name="name" autocomplete="off" required>
					<small class="invalid-feedback"></small>
				</div>

				<div class="form-group">
					<label class="label"> Nomor Whatsapp </label>
					<input type="number" class="form-control" placeholder="Nomor Whatsapp" name="phone_number" autocomplete="off" required>
					<small class="invalid-feedback"></small>
				</div>

				<div class="form-group">
					<label class="label"> Email </label>
					<input type="email" class="form-control" placeholder="Email" name="email" autocomplete="off" required>
					<small class="invalid-feedback"></small>
				</div>

				<div class="form-group mb-3">
					<button class="btn btn-primary submit-btn btn-block" type="submit">
						<i class="mdi mdi-check"></i> Daftar Sekarang
					</button>
				</div>

				<p class="text-center textMsg"><br></p>

				<div class="text-block text-center my-3">
					<span class="text-small font-weight-semibold"> Sudah memiliki akun ?</span>
					<a href="{{ route('login') }}" class="text-black text-small"> Login disini </a>
				</div>

			</form>
		</div>
		<p class="footer-text text-center text-white mt-3">
			Copyright © {{ date('Y') }} PT. Adiva Sumber Solusi. All rights reserved.
		</p>
	</div>
</div>
@endsection


@section('script')
<script>
	$(function(){
		let form = $('#mainForm');
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
				url : `{{ route('save_access_register') }}`,
				method : "post",
				data : formData,
			})
			.done(response => {
				let { message, code } = response

				if(code == 200) {
					$('.textMsg').removeClass("text-danger");
					$('.textMsg').html(message);
					$('.textMsg').addClass("text-success");

					toastrAlert()
					toastr.success(message, 'Berhasil')

					setTimeout(() => {
						window.location.replace("{{ url('dashboard') }}");
					}, 1000)
				}
			})
			.fail(error => {
				submitBtn.ladda('stop')
				let { status, responseJSON } = error
				let { message } = responseJSON

				toastrAlert()
				toastr.warning(message, 'Peringatan')
			});
		});

		$('.form-control').on('change, keyup', function(){
			clearError();
		});
	})
</script>
@endsection