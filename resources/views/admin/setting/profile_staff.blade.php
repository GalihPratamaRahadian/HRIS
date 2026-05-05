@extends('template.backLayout')


@section('content')
<div class="row">

	<div class="col-lg-6">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> {{ $title }} </h4>
				</div>

				<form id="mainForm">
					{!! \Setting::requiredBanner() !!}

					<p class="text-muted">
						<small>
							* Foto, No Induk Karyawan, Departemen, Jabatan, Shift dan Status Pekerjaan hanya bisa diubah oleh HRD
						</small>
					</p>
					
					<div class="form-group">
						<label> No Induk Karyawan</label>
						<input type="text" class="form-control" value="{{ auth()->user()->employee->employee_number }}" readonly>
					</div>

					<div class="form-group">
						<label> Nama {!! Setting::required() !!} </label>
						<input type="text" name="employee_name" class="form-control" value="{{ auth()->user()->employee->employee_name }}" placeholder="Nama Karyawan" required>
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Jenis Kelamin {!! Setting::required() !!} </label>
						<select name="gender" class="form-control" required>
							<option value="L">Laki - laki</option>
							<option value="P">Perempuan</option>
						</select>
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Email </label>
						<input type="email" name="email" value="{{ auth()->user()->employee->email }}" class="form-control" placeholder="Alamat Email">
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Nomor Telepon {!! Setting::required() !!} </label>
						<input type="tel" name="phone_number" value="{{ auth()->user()->employee->phone_number }}" class="form-control" placeholder="Nomor Telepon" required>
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Departemen </label>
						<input type="text" class="form-control" value="{{ auth()->user()->employee->departmentName() }}" readonly>
					</div>

					<div class="form-group">
						<label> Jabatan </label>
						<input type="text" class="form-control" value="{{ auth()->user()->employee->positionName() }}" readonly>
					</div>

					<div class="form-group">
						<label> Shift </label>
						<input type="text" class="form-control" value="{{ auth()->user()->employee->shiftName() }}" readonly>
					</div>

					<div class="form-group">
						<label> No Jamsostek </label>
						<input type="text" name="jamsostek" value="{{ auth()->user()->employee->jamsostek }}" class="form-control" placeholder="No Jamsostek">
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Status Pekerjaan </label>
						<input type="text" class="form-control" value="{{ auth()->user()->employee->jobStatusText() }}" readonly>
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Username {!! Setting::required() !!} </label>
						<input type="text" class="form-control" value="{{ auth()->user()->username }}" name="username" required>
						<small class="invalid-feedback"></small>
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

		$('[name="gender"]').select2({
			placeholder : 'Pilih Jenis Kelamin'
		})
		$('[name="gender"]').val("{{ auth()->user()->employee->gender }}").trigger('change');

		let form = $('#mainForm');
		let submitBtn = form.find(`[type="submit"]`).ladda();

		form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();

			submitBtn.ladda('start')
			ajaxSetup();
			$.ajax({
				url : `{{ route('setting.save_profile') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {

				let { message, code } = response

				if(code == 200) {
					toastrAlert();
					toastr.success(message, 'Berhasil');
					setTimeout(() => {
						window.location.reload();
					}, 1000)
				}
			})
			.fail(error => {
				submitBtn.ladda('stop')

				let { status, responseJSON } = error
				let { message } = responseJSON

				if(status == 422) {
					let { errors } = responseJSON
					invalidResponse(form, errors)
				}

				toastrAlert();
				toastr.warning(message, 'Peringatan')
			})
		})
	});
</script>
@endsection