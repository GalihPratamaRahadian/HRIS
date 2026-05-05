@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> {{ $title }} </h4>
				</div>

				<form id="form">
					{!! \Setting::requiredBanner() !!}
					
					<div class="form-group">
						<label> Jenis {!! \Setting::required() !!} </label>
						<select name="type" style="width: 100%;" required>
							<option value="Pulang Cepat"> Pulang Cepat </option>
							<option value="Terlambat"> Terlambat </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal {!! \Setting::required() !!} </label>
						<input type="date" name="date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Jam Pengajuan {!! \Setting::required() !!} </label>
						<input type="text" name="time" placeholder="Pilih Jam Pengajuan" class="form-control" autocomplete="off" required>
					</div>

					<div class="form-group">
						<label> Alasan {!! \Setting::required() !!} </label>
						<textarea class="form-control" name="reason" rows="3" placeholder="Alasan" required></textarea>
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

		const $form = $('#form')
		const $submitBtn = $form.find(`[type="submit"]`).ladda();

		$form.find('[name="type"]').select2({
			placeholder: '- Pilih Jenis -'
		})
		$form.find('[name="type"]').val('').trigger('change')

		$form.find(`[name="time"]`).clockpicker({
			autoclose: true,
		})

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);

			$submitBtn.ladda('start')
			$submitBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');
			ajaxSetup();
			$.ajax({
				url : `{{ route('employee.attendance_permission_submission.store') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				contentType : false,
				processData : false,
			})
			.done(response => {
				ajaxSuccessHandling(response)
				$submitBtn.prop('disabled', false).html('<i class="mdi mdi-check"></i> Simpan');

				setTimeout(() => {
					window.location.href = `{{ route('employee.attendance_permission_submission') }}`
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