@extends('template.backLayout')


@section('content')
<form id="form">
	<div class="row">

		<div class="col-md-6">
			<div class="card support-pane-card grid-margin">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center mb-3">
						<h4 class="card-title mb-0"> {{ $title }} </h4>
					</div>

					{!! Setting::requiredBanner() !!}

					<div class="form-group">
						<label> Karyawan </label> <br>
						<b> {{ $employee->employee_name }}
							@if(!empty($employee->employee_number))
							- {{ $employee->employee_number }}
							@endif
						</b>
					</div>

					<div class="form-group">
						<label> Username {!! Setting::required() !!} </label>
						<input type="text" name="username" value="{{ $employee->user->username }}" class="form-control" placeholder="Username" required>
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Password </label>
						<input type="password" name="password" class="form-control" placeholder="Isi Jika Ingin Ganti Password">
						<small class="invalid-feedback"></small>
					</div>

					<hr>

					<button class="btn btn-success" type="submit">
						<i class="mdi mdi-check"></i> Simpan
					</button>

				</div>
			</div>
		</div>
	</div>
</form>
@endsection


@section('script')
<script type="text/javascript">
	$(function(){

		$form = $('#form')
		$submitBtn = $form.find(`[type="submit"]`).ladda();

		$form.on('submit', function(e){
			e.preventDefault();

			const formData = $(this).serialize();
			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url: `{{ route('employee.update_user', $employee->id) }}`,
				method: 'put',
				dataType: 'json',
				data: formData
			})
			.done(response => {
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		})

	})
</script>
@endsection