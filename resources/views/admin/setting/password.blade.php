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
						<label> Password Lama {!! Template::required() !!} </label>
						<input type="password" name="old_password" class="form-control" placeholder="Password Lama" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Password Baru {!! Template::required() !!} </label>
						<input type="password" name="new_password" class="form-control" placeholder="Password Baru" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Ulangi Password Baru {!! Template::required() !!} </label>
						<input type="password" name="confirm_password" class="form-control" placeholder="Ulangi Password Baru" required>
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
<script type="text/javascript">
	$(function(){

		let $form = $('#mainForm')
		let $submitBtn = $form.find('[type="submit"]').ladda();

		const resetForm = () => {
			$form[0].reset();
		}

		const init = () => {
			resetForm();
			$form.find('[name="old_password"]').focus();
		}


		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();

			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('setting.save_password') }}`,
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
				ajaxErrorHandling(error, $form)
			})
		});

		init();
	});
</script>
@endsection