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

					<div class="alert alert-primary">
						Jatah/Kuota Cuti : <b>{{ employee()->leaveQuotaAvailable(true) }} hari</b>
					</div>
					
					<div class="form-group">
						<label> Alasan {!! \Setting::required() !!} </label>
						<select name="id_leave_reason" style="width: 100%;" required>
							
							@foreach(\App\Models\LeaveReason::all() as $leaveReason)
							<option value="{{ $leaveReason->id }}"> {{ $leaveReason->reasonWithDurationText() }} </option>
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
						<label> Deskripsi </label>
						<textarea class="form-control" name="description" rows="2" placeholder="Deskripsi"></textarea>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Lampiran File </label>
						<input type="file" name="file_attachment" class="form-control">
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

		$form.find('[name="id_leave_reason"]').select2({
			placeholder: '- Pilih Alasan -'
		})
		$form.find('[name="id_leave_reason"]').val('').trigger('change')

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);

			$submitBtn.ladda('start')
			ajaxSetup();
			$.ajax({
				url : `{{ route('employee.leave_submission.store') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				contentType : false,
				processData : false,
			})
			.done(response => {
				ajaxSuccessHandling(response)

				setTimeout(() => {
					window.location.href = `{{ route('employee.leave_submission') }}`
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