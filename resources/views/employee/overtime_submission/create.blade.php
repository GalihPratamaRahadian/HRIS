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
						<label> Alasan {!! \Setting::required() !!} </label>
						<select name="id_overtime_reason" style="width: 100%;" required>
							
							@foreach(\App\Models\OvertimeReason::all() as $overtimeReason)
							<option value="{{ $overtimeReason->id }}"> {{ $overtimeReason->reason }} </option>
							@endforeach

						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Mulai Lembur {!! \Setting::required() !!} </label>
								<input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
								<span class="invalid-feedback"></span>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Jam Mulai Lembur {!! \Setting::required() !!} </label>
								<input type="text" name="clock_start" placeholder="Pilih Jam Mulai Lembur" class="form-control" autocomplete="off" readonly required style="background-color: white;">
								<span class="invalid-feedback"></span>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Selesai Lembur {!! \Setting::required() !!} </label>
								<input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
								<span class="invalid-feedback"></span>
							</div>
						</div>
						
						<div class="col-lg-6">
							<div class="form-group">
								<label> Jam Selesai Lembur {!! \Setting::required() !!} </label>
								<input type="text" name="clock_end" placeholder="Pilih Jam Selesai Lembur" class="form-control" autocomplete="off" readonly required style="background-color: white;">
								<span class="invalid-feedback"></span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label> Deskripsi {!! \Setting::required() !!} </label>
						<textarea class="form-control" name="description" rows="3" placeholder="Deskripsi" required></textarea>
						<span class="invalid-feedback"></span>
					</div>

					<hr>

					<div class="form-group">
						<button class="btn btn-success" type="submit">
							<i class="mdi mdi-check"></i> Ajukan
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

		$form.find('[name="id_overtime_reason"]').select2({
			placeholder: '- Pilih Alasan -'
		})
		$form.find('[name="id_overtime_reason"]').val('').trigger('change')
		$form.find(`[name="clock_start"], [name="clock_end"]`).clockpicker({
			autoclose: true,
			afterDone: function() {
				clearInvalid()
			}
		})

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);

			$submitBtn.ladda('start')
			$submitBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');
			ajaxSetup();
			$.ajax({
				url : `{{ route('employee.overtime_submission.store') }}`,
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
					window.location.href = `{{ route('employee.overtime_submission') }}`
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