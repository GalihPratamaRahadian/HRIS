@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> {{ $title }} </h4>
				</div>

				<form id="mainForm">
					{!! \Setting::requiredBanner() !!}
					
					<div class="form-group">
						<label> Alasan {!! \Setting::required() !!} </label>
						<select name="leave_reason" style="width: 100%;" required>
							
							@foreach(\App\Models\LeaveSubmission::availableReasons() as $val => $label)
							<option value="{{ $val }}"> {{ $label }} </option>
							@endforeach

						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Awal {!! \Setting::required() !!} </label>
						<input type="date" name="start_date" class="form-control" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Akhir {!! \Setting::required() !!} </label>
						<input type="date" name="end_date" class="form-control" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Deskripsi {!! \Setting::required() !!} </label>
						<textarea class="form-control" name="description" rows="3" placeholder="Deskripsi" required></textarea>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Lampiran File </label>
						<input type="file" name="file" class="form-control">
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
<script type="text/html" id="chinaFTFormTemplate">
	<div class="form-group">
		<label> Device ID {!! \Setting::required() !!} </label>
		<input type="number" name="device_id" class="form-control" placeholder="Device ID" required>
	</div>
</script>


<script type="text/javascript">
	$(function(){

		$('[name="leave_reason"]').select2({
			placeholder: '- Pilih Alasan -'
		})
		$('[name="leave_reason"]').val('').trigger('change')

		let form = $('#mainForm')
		let submitBtn = form.find('[type="submit"]').ladda();

		form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);

			submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('submission.leave.store') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				contentType : false,
				processData : false,
			})
			.done(response => {
				let { message, code } = response

				if(code == 200) {
					toastrAlert();
					toastr.success(message, 'Berhasil');

					setTimeout(() => {
						window.location.href = `{{ route('submission.leave') }}`
					}, 1000)
				}
			})
			.fail(error => {
				submitBtn.ladda('stop')

				let { status, responseJSON } = error
				let { message, errors } = responseJSON

				if(status == 422) {
					invalidResponse(form, errors)
				}

				toastrAlert();
				toastr.warning(message, 'Peringatan');
			})
		});

	});
</script>
@endsection