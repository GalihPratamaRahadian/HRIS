@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Setting::titleBanner($title) !!}

				<form id="mainForm">
					@method('PUT')

					{!! Template::requiredBanner() !!}
					
					<div class="row">
						<div class="col-6">

							<div class="form-group">
								<label> No Induk Karyawan (Opsional) </label>
								<input type="text" name="employee_number" class="form-control" placeholder="No Induk Karyawan (Opsional)" value="{{ $employee->employee_number }}">
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Nama Karyawan {!! Template::required() !!} </label>
								<input type="text" name="employee_name" class="form-control" placeholder="Nama Karyawan" value="{{ $employee->employee_name }}" required>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Jenis Kelamin {!! Template::required() !!} </label> <br>
								<select name="gender" style="width: 100%;" required>
									<option value="L">Laki - laki</option>
									<option value="P">Perempuan</option>
								</select>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Email (Opsional) </label>
								<input type="email" name="email" class="form-control" placeholder="Alamat Email (Opsional)" value="{{ $employee->email }}">
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Nomor Telepon {!! Template::required() !!} </label>
								<input type="tel" name="phone_number" class="form-control" placeholder="628xxxxxxxxxx" value="{{ $employee->phone_number }}" required>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Departemen {!! Template::required() !!} </label><br>
								<select name="id_department" style="width: 100%;" required>

									@foreach(\App\Models\Department::all() as $department) {
									<option value="{{ $department->id }}"> {{ $department->department_name }} </option>
									@endforeach

								</select>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Jabatan {!! Template::required() !!} </label><br>
								<select name="id_position" style="width: 100%;" required>

									@foreach(\App\Models\Position::all() as $position) {
									<option value="{{ $position->id }}"> {{ $position->position_name }} </option>
									@endforeach

								</select>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Jenis Jam Kerja {!! Template::required() !!} </label><br>
								<select name="shift_type" class="form-control" required>
									<option value="" selected disabled> - Pilih Jenis Jam Kerja -</option>
									<option value="routine"> Jam Kerja Rutin </option>
									<option value="unroutine"> Jam Kerja Harian </option>
								</select>
								<small class="invalid-feedback"></small>
							</div>

							<div id="shift-routine">
							</div>

							<div class="form-group">
								<label> Grup Karyawan {!! Template::required() !!} </label><br>
								<select name="id_employee_group" style="width: 100%;" required>

									@foreach(\App\Models\EmployeeGroup::all() as $group) {
									<option value="{{ $group->id }}"> {{ $group->group_name }} </option>
									@endforeach

								</select>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Nomor Jamsostek (Opsional) </label>
								<input type="text" name="jamsostek" class="form-control" placeholder="Nomor Jamsostek (Opsional)" value="{{ $employee->jamsostek }}">
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Status Pekerjaan {!! Template::required() !!} </label><br>
								<select name="job_status" style="width: 100%;" required>
									<option value="{{ \App\Models\Employee::JOBSTATUS_TETAP }}"> Tetap </option>
									<option value="{{ \App\Models\Employee::JOBSTATUS_KONTRAK }}"> Kontrak </option>
									<option value="{{ \App\Models\Employee::JOBSTATUS_PROBATION }}"> Probation </option>
								</select>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Tanggal Mulai Bekerja (Opsional) </label>
								<input type="date" name="start_working_date" value="{{ $employee->start_working_date }}" class="form-control">
								<small class="invalid-feedback"></small>
							</div>
						</div>

						<div class="col-6">
							<div class="form-group">
								<label> Tempat Lahir {!! Template::required() !!} </label>
								<input type="text" name="place_of_birth" class="form-control" placeholder="Tempat Lahir" value="{{ $employee->place_of_birth }}" required>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Tanggal Lahir {!! Template::required() !!} </label>
								<input type="date" name="date_of_birth" value="{{ $employee->date_of_birth }}" class="form-control" required>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Alamat (Sesuai KTP) {!! Template::required() !!} </label>
								<textarea class="form-control" name="address" placeholder="Alamat (Sesuai KTP)" rows="2" required>{{ $employee->address }}</textarea>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Pendidikan Terakhir {!! Template::required() !!} </label>
								<select class="form-control" name="last_education" required>
									<option value="" selected disabled> - Pilih Pendidikan Terakhir - </option>
									@foreach(GlobalData::educationLevel() as $education)
									<option value="{{ $education }}"> {{ $education }} </option>
									@endforeach
								</select>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Jurusan Pendidikan Terakhir {!! Template::required() !!} </label>
								<input type="text" name="last_education_major" value="{{ $employee->last_education_major }}" class="form-control" placeholder="Jurusan Pendidikan Terakhir" required>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Status Pernikahan {!! Template::required() !!} </label>
								<select class="form-control" name="marital_status" required>
									<option value="" selected disabled> - Pilih Status Pernikahan - </option>
									@foreach(GlobalData::maritalStatus() as $maritalStatus)
									<option value="{{ $maritalStatus }}"> {{ $maritalStatus }} </option>
									@endforeach
								</select>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Golongan Darah (Opsional) </label>
								<select class="form-control" name="blood_type">
									<option value="" selected disabled> - Pilih Golongan Darah - </option>
									@foreach(GlobalData::bloodType() as $bloodType)
									<option value="{{ $bloodType }}"> {{ $bloodType }} </option>
									@endforeach
								</select>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> No KTP {!! Template::required() !!} </label>
								<input type="text" name="ktp_number" value="{{ $employee->ktp_number }}" class="form-control" placeholder="No KTP" required>
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> No NPWP (Opsional) </label>
								<input type="text" name="npwp_number" value="{{ $employee->npwp_number }}" class="form-control" placeholder="No NPWP (Opsional)">
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Foto (Isi Jika Ingin Ganti Foto) </label>
								<input type="file" name="file_photo" class="form-control" accept=".jpeg, .jpg, .png, .gif">
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Nama Bank Rekening (Opsional) </label>
								<input type="text" name="bank_name" class="form-control" placeholder="Nama Bank Rekening (Opsional)" value="{{ $employee->bank_name }}">
								<small class="invalid-feedback"></small>
							</div>

							<div class="form-group">
								<label> Nomor Rekening Bank (Opsional) </label>
								<input type="text" name="bank_account_number" class="form-control" placeholder="Nomor Rekening Bank (Opsional)" value="{{ $employee->bank_account_number }}">
								<small class="invalid-feedback"></small>
							</div>
						</div>
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
			$form.find('[name="employee_number"]').focus();
		}


		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);
			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('employee.update', $employee->id) }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				contentType: false,
				processData: false,
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

		$('[name="gender"]').select2({
			placeholder : '- Pilih Jenis Kelamin -'
		});
		$('[name="gender"]').val(`{{ $employee->gender }}`).trigger('change')

		$('[name="id_department"]').select2({
			placeholder : '- Pilih Departemen -'
		});
		$('[name="id_department"]').val(`{{ $employee->id_department }}`).trigger('change')

		$('[name="id_position"]').select2({
			placeholder : '- Pilih Jabatan -'
		});
		$('[name="id_position"]').val(`{{ $employee->id_position }}`).trigger('change')

		$('[name="job_status"]').select2({
			placeholder : '- Pilih Status Pekerjaan -'
		});
		$('[name="job_status"]').val(`{{ $employee->job_status }}`).trigger('change')

		$('[name="id_shift"]').select2({
			placeholder : '- Pilih Jam Kerja -'
		});	
		$('[name="id_shift"]').val(`{{ $employee->id_shift }}`).trigger('change')

		$('[name="id_employee_group"]').select2({
			placeholder : '- Pilih Grup Karyawan -'
		});	
		$('[name="id_employee_group"]').val(`{{ $employee->id_employee_group }}`).trigger('change')

		$('[name="last_education"]').select2({
			placeholder : 'Pilih Pendidikan Terakhir'
		})
		$('[name="last_education"]').val("{{ $employee->last_education }}").trigger('change');

		$('[name="marital_status"]').select2({
			placeholder : 'Pilih Status Pernikahan'
		})
		$('[name="marital_status"]').val("{{ $employee->marital_status }}").trigger('change');

		$('[name="blood_type"]').select2({
			placeholder : 'Pilih Golongan Darah'
		})
		$('[name="blood_type"]').val("{{ $employee->blood_type }}").trigger('change');

		$form.find(`[name="shift_type"]`).on('change', function(){
			let value = $(this).val();

			if(value == 'routine') {
				$('#shift-routine').html($('#shift-routine-template').html())

				$form.find('[name="id_shift"]').select2({
					placeholder : '- Pilih Jam Kerja -'
				});
				$form.find('[name="id_shift"]').val('').trigger('change')
			} else {
				$('#shift-routine').empty()
			}
		})

		let positionsReq = 0;

		$form.find(`[name="id_department"]`).on('change', function(){
			const departmentId = $(this).val()
			$.get({
				url: `{{ route('helper.get_positions') }}?id_department=${departmentId}`,
				dataType: 'json'
			})
			.done(response => {
				const { positions } = response
				let html = ''
				positions.forEach(position => {
					html += `<option value="${position.id}"> ${position.position_name} </option>`
				})

				$form.find(`[name="id_position"]`).html(html)

				if(positionsReq == 0) {
					$form.find('[name="id_position"]').val("{{ $employee->id_position }}").trigger('change');
				} else {
					$form.find(`[name="id_position"]`).val('').trigger('change')
				}

				positionsReq++;
			})
		})

		$form.find('[name="id_department"]').val("{{ $employee->id_department }}").trigger('change');

		$form.find('[name="shift_type"]').val("{{ $employee->shift_type }}").trigger('change');
		setTimeout(() => {
			$form.find(`[name="id_shift"]`).val(`{{ $employee->id_shift }}`).trigger('change');
		}, 500)

		init();
	});
</script>

<script type="text/html" id="shift-routine-template">
	<div class="form-group">
		<label> Jam Kerja </label><br>
		<select name="id_shift" style="width: 100%;" required>

			@foreach(\App\Models\Shift::all() as $shift) {
			<option value="{{ $shift->id }}"> {{ $shift->shift_name }} </option>
			@endforeach

		</select>
		<small class="invalid-feedback"></small>
	</div>
</script>
@endsection