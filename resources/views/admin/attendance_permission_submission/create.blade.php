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
						<label> Karyawan {!! \Setting::required() !!} </label>
						<select name="id_employee" style="width: 100%;" required>
							
							@foreach(\App\Models\Employee::getActiveEmployees() as $employee)
							<option value="{{ $employee->id }}"> {{ $employee->employee_name }} | {{ $employee->departmentName() }} </option>
							@endforeach

						</select>
						<span class="invalid-feedback"></span>
					</div>
					
					<div class="form-group">
						<label> Jenis {!! \Setting::required() !!} </label>
						<select name="type" class="form-control" required>
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
						<label> Persetujuan Izin Terlambat / Pulang Cepat  {!! \Setting::required() !!} </label>
						<select name="submission_approval_status" class="form-control" required>
							<option selected disabled> - Pilih - </option>
							<option value="Approve"> Langsung Setujui </option>
							<option value="Waiting"> Menunggu Persetujuan Atasan </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group" id="send-notification" style="display: none;">
						<label> Kirim Notifikasi Telah Disetujui Ke Karyawan Terkait {!! \Setting::required() !!} </label>
						<select name="send_notification" class="form-control" required>
							<option value="Ya"> Kirim Notifikasi </option>
							<option value="Tidak"> Tidak Perlu Kirim  </option>
						</select>
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

		$form.find('[name="id_employee"]').select2({
			placeholder: '- Pilih Karyawan -'
		})
		$form.find('[name="id_employee"]').val('').trigger('change')

		$form.find(`[name="time"]`).clockpicker({
			autoclose: true,
		})

		$form.find(`[name="submission_approval_status"]`).on('change', function(){
			const status = $(this).val()

			if(status == 'Approve') {
				$form.find('#send-notification').show()
			} else {
				$form.find('#send-notification').hide()
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
				url : `{{ route('admin.attendance_permission_submission.store') }}`,
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
					window.location.href = `{{ route('admin.attendance_permission_submission') }}`
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