@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					{!! Template::requiredBanner() !!}

					<div class="form-group">
						<label> Username {!! Template::required() !!} </label>
						<input type="text" name="email_username" class="form-control" placeholder="Contoh : admin@example.com" value="{{ setting('email_username', '') }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Password {!! Template::required() !!} </label>
						<input type="text" name="email_password" class="form-control" placeholder="Masukkan Password Disini" value="{{ setting('email_password', '') }}" required>
						<span class="invalid-feedback"></span>
					</div>

                    <div class="form-group">
						<label> Host {!! Template::required() !!} </label>
						<input type="text" name="email_host" class="form-control" placeholder="Contoh : mail.example.com" value="{{ setting('email_host', '') }}" required>
						<span class="invalid-feedback"></span>
					</div>

                    <div class="form-group">
						<label> Port {!! Template::required() !!} </label>
						<input type="text" name="email_port" class="form-control" placeholder="Contoh : 465" value="{{ setting('email_port', '') }}" required>
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

	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">

				<form id="mainForm">

					<div class="form-group">
						<label> {!! Template::required() !!} Email Wajib SMTP & SSL</label>
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

		const resetForm = () => {
			$form[0].reset();
		}


		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();

			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('setting.save_email') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response)
                location.reload();
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});

		init();
	});
</script>
@endsection
