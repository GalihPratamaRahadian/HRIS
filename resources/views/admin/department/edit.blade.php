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
						<label> Nama Departemen {!! Template::required() !!} </label>
						<input type="text" name="department_name" class="form-control" placeholder="Nama Departemen" value="{{ $department->department_name }}" required>
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

		const init = () => {
			$form.find('[name="department_name"]').focus();
		}


		$form.on('submit', function(e){
			e.preventDefault();

			let formData = $(this).serialize();

			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('admin.department.update', $department->id) }}`,
				method : 'put',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				init();
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response);
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling($form, error);
			})
		});

		init();
	});
</script>
@endsection