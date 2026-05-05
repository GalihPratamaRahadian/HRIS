@extends('template.backLayout')


@section('content')
<?php 
	$wajibDiisi = '<span class="text-danger"> * </span>';
?>
<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> {{ $title }} </h4>
				</div>

				<form id="mainForm">
					<div class="alert alert-info">
						Kolom bertanda {!! $wajibDiisi !!} wajib diisi.
					</div>
					
					<div class="form-group">
						<label> Karyawan </label> <br>
						<label><b> {{ $attendance->employeeName() }} </b></label>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tipe Kehadiran {!! Setting::required() !!} </label>
						<select class="form-control" name="type" required>
							
							@foreach(\App\Models\Attendance::availableTypes() as $value => $label)
							<option value="{{ $value }}"> {{ $label }} </option>
							@endforeach

						</select>
					</div>

					<div class="form-group">
						<label> Tanggal & Jam Masuk {!! Setting::required() !!} </label>
						<input type="datetime-local" name="clock_in_at" class="form-control" value="{{ $attendance->clockInAtFormatDatetimeLocal() }}" required>
					</div>

					<div class="form-group">
						<label> Tanggal & Jam Keluar {!! Setting::required() !!} </label>
						<input type="datetime-local" name="clock_out_at" class="form-control" value="{{ $attendance->clockOutAtFormatDatetimeLocal() }}" required>
					</div>

					<hr>

					<div class="form-group">
						<label> Tanggal & Jam Shift Masuk {!! Setting::required() !!} </label>
						<input type="datetime-local" name="shift_clock_in" class="form-control" value="{{ $attendance->shiftClockInFormatDatetimeLocal() }}" required>
					</div>

					<div class="form-group">
						<label> Tanggal & Jam Shift Keluar {!! Setting::required() !!} </label>
						<input type="datetime-local" name="shift_clock_out" class="form-control" value="{{ $attendance->shiftClockOutFormatDatetimeLocal() }}" required>
					</div>

					<div class="form-group">
						<label> Toleransi Keterlambatan {!! Setting::required() !!} </label>
						<input type="number" name="late_tolerance" class="form-control" value="{{ $attendance->late_tolerance }}" required>
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

		const form = $('#mainForm')
		const submitBtn = form.find('[type="submit"]').ladda();

		form.find(`[name="type"]`).val(`{{ $attendance->type }}`)

		form.on('submit', function(e){
			e.preventDefault();

			let formData = $(this).serialize();

			submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('attendance.update', $attendance->id) }}`,
				method : 'put',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				submitBtn.ladda('stop')

				let { message, code } = response

				if(code == 200) {
					toastrAlert();
					toastr.success(message, 'Berhasil');
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