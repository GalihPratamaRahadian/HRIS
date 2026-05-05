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
						<label> Nama {!! Template::required() !!} </label>
						<input type="text" name="name" class="form-control" placeholder="Nama" value="{{ auth()->user()->name }}" required @if(auth()->user()->isEmployee()) readonly @endif>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Username {!! Template::required() !!} </label>
						<input type="text" name="username" class="form-control" placeholder="Username" value="{{ auth()->user()->username }}" required>
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

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();

			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('setting.save_profile') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				ajaxSuccessHandling(response)
				setTimeout(() => {
					window.location.reload();
				}, 1000)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});
	});
</script>
@endsection