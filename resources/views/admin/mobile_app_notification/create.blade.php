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
						<label> Target Notifikasi {!! Template::required() !!} </label>
						<select name="id_user" style="width: 100%;" required>
							@foreach(\App\Models\Employee::getActiveEmployees() as $employee)
							<option value="{{ $employee->id_user }}"> {{ $employee->employee_name }} | {{ $employee->departmentName() }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Judul {!! Template::required() !!} </label>
						<input type="text" name="title" class="form-control" placeholder="Judul" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Pesan {!! Template::required() !!} </label>
						<input type="text" name="message" class="form-control" placeholder="Pesan" required>
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

		$form.find(`[name="id_user"]`).select2({
			'placeholder': '- Pilih Target Notifikasi -'
		})

		const resetForm = () => {
			$form[0].reset();
		}

		const init = () => {
			resetForm();
			$form.find('[name="id_user"]').val('').trigger('change');
		}


		$form.on('submit', function(e){
			e.preventDefault();

			let formData = $(this).serialize();

			$submitBtn.ladda('start')
			ajaxSetup();
			$.ajax({
				url : `{{ route('admin.mobile_app_notification.store') }}`,
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