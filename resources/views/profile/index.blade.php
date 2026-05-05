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
						<label> No Induk Karyawan</label>
						<input type="text" name="employee_number" class="form-control" value="{{ auth()->user()->registrant->employee_number }}" placeholder="No Induk Karyawan">
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Nama {!! Template::required() !!} </label>
						<input type="text" name="employee_name" class="form-control" value="{{ auth()->user()->registrant->employee_name }}" placeholder="Nama Karyawan" required>
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Jenis Kelamin {!! Template::required() !!} </label>
						<select name="gender" class="form-control" required>
							<option value="L">Laki - laki</option>
							<option value="P">Perempuan</option>
						</select>
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Email {!! Template::required() !!} </label>
						<input type="email" name="email" value="{{ auth()->user()->registrant->email }}" class="form-control" placeholder="Alamat Email" required>
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Nomor Telepon {!! Template::required() !!} </label>
						<input type="tel" name="phone_number" value="{{ auth()->user()->registrant->phone_number }}" class="form-control" placeholder="Nomor Telepon" required>
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Departemen {!! Template::required() !!} </label>
						<select name="id_department" class="form-control" required>
							@foreach(\App\Models\Department::all() as $department)
							<option value="{{ $department->id }}"> {{ $department->department_name }} </option>
							@endforeach
						</select>
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Jabatan {!! Template::required() !!} </label>
						<select name="id_position" class="form-control" required>
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
						<label> Grup Karyawan <small>(Opsional)</small> </label>
						<select name="id_employee_group" class="form-control">
							@foreach(\App\Models\EmployeeGroup::all() as $group)
							<option value="{{ $group->id }}"> {{ $group->group_name }} </option>
							@endforeach
						</select>
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> No Jamsostek </label>
						<input type="text" name="jamsostek" value="{{ auth()->user()->registrant->jamsostek }}" class="form-control" placeholder="No Jamsostek">
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Status Pekerjaan {!! Template::required() !!} </label>
						<select name="job_status" class="form-control" required>
							<option value="tetap">Tetap</option>
							<option value="kontrak">Kontrak</option>
							<option value="probation">Probation</option>
						</select>
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Tanggal Mulai Bekerja </label>
						<input type="date" name="start_working_date" value="{{ auth()->user()->registrant->start_working_date }}" class="form-control">
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Tempat Lahir {!! Template::required() !!} </label>
						<input type="text" name="place_of_birth" value="{{ auth()->user()->registrant->place_of_birth }}" class="form-control" placeholder="Tempat Lahir" required>
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Tanggal Lahir {!! Template::required() !!} </label>
						<input type="date" name="date_of_birth" value="{{ auth()->user()->registrant->date_of_birth }}" class="form-control" required>
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Alamat (Sesuai KTP) {!! Template::required() !!} </label>
						<textarea class="form-control" name="address" placeholder="Alamat (Sesuai KTP)" rows="2" required>{{ auth()->user()->registrant->address }}</textarea>
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
						<input type="text" name="last_education_major" value="{{ auth()->user()->registrant->last_education_major }}" class="form-control" placeholder="Jurusan Pendidikan Terakhir" required>
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
						<label> Golongan Darah </label>
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
						<input type="text" name="ktp_number" value="{{ auth()->user()->registrant->ktp_number }}" class="form-control" placeholder="No KTP" required>
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> No NPWP </label>
						<input type="text" name="npwp_number" value="{{ auth()->user()->registrant->npwp_number }}" class="form-control" placeholder="No NPWP">
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Foto {!! Template::required() !!} </label>
						@if(auth()->user()->registrant->isHasPhoto())
						<input type="file" name="photo" class="form-control">
						@else
						<input type="file" name="photo" class="form-control" required>
						@endif
						<small class="invalid-feedback"></small>
						<label class="mt-2" data-toggle="modal" data-target="#kriteriaModal">
						<a href="javascript:void(0);" class="text-dark">
							<i class="mdi mdi-information-outline"></i> Lihat Kriteria
						</a>
						</label>
					</div>

					<div class="form-group">
						@if(auth()->user()->registrant->isHasPhoto())
						<img src="{{ auth()->user()->registrant->photoLink('face') }}" alt="Loading..." style="max-width: 200px;" class="previewPhoto">
						@else
						<img src="#" alt="Loading..." style="max-width: 200px; display: none;" class="previewPhoto">
						@endif
					</div>

					<div class="form-group">
						<div class="progress" style="height: 20px; width: 100%; display: none;">
							<div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">0%</div>
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
<script type="text/html" id="shift-routine-template">
	<div class="form-group">
		<label> Jam Kerja {!! Template::required() !!} </label>
		<select name="id_shift" class="form-control" required>
			@foreach(\App\Models\Shift::all() as $shift)
			<option value="{{ $shift->id }}"> {{ $shift->shift_name }} </option>
			@endforeach
		</select>
		<small class="invalid-feedback"></small>
	</div>
</script>


<script type="text/javascript">
	$(function(){

		$form = $('#mainForm');
		$submitBtn = $form.find(`[type="submit"]`).ladda();

		$form.find('[name="gender"]').select2({
			placeholder : 'Pilih Jenis Kelamin'
		})
		$form.find('[name="gender"]').val("{{ auth()->user()->registrant->gender }}").trigger('change');

		$form.find('[name="id_department"]').select2({
			placeholder : 'Pilih Departemen'
		})

		$form.find('[name="id_position"]').select2({
			placeholder : 'Pilih Jabatan'
		})

		$form.find('[name="id_employee_group"]').select2({
			placeholder : 'Pilih Grup Karyawan'
		})
		$form.find('[name="id_employee_group"]').val("{{ auth()->user()->registrant->id_employee_group }}").trigger('change');

		$form.find('[name="job_status"]').select2({
			placeholder : 'Pilih Status Pekerjaan'
		})
		$form.find('[name="job_status"]').val("{{ auth()->user()->registrant->job_status }}").trigger('change');

		$form.find('[name="last_education"]').select2({
			placeholder : 'Pilih Pendidikan Terakhir'
		})
		$form.find('[name="last_education"]').val("{{ auth()->user()->registrant->last_education }}").trigger('change');

		$form.find('[name="marital_status"]').select2({
			placeholder : 'Pilih Status Pernikahan'
		})
		$form.find('[name="marital_status"]').val("{{ auth()->user()->registrant->marital_status }}").trigger('change');

		$form.find('[name="blood_type"]').select2({
			placeholder : 'Pilih Golongan Darah'
		})
		$form.find('[name="blood_type"]').val("{{ auth()->user()->registrant->blood_type }}").trigger('change');

		$form.find('[name="photo"]').on('change', function(){
			let file = $(this).val();
			
			if(file!=="") {
				let fileType = this.files[0].type;

				if(fileType.substring(0, 5) != "image") {
					alert('File harus berupa foto');
					$(this).val('');
				} else {
					let reader = new FileReader();

					reader.onload = function(e) {
						$('.previewPhoto').attr('src', e.target.result);
					}

					reader.readAsDataURL(this.files[0]);

					$('.previewPhoto').show();

					$("[name='delete']").val('yes');
				}
			} else {
				$('.previewPhoto').hide();
			}
		});

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

		$form.on('submit', function(e){
			e.preventDefault();

			let formData = new FormData(this),
				submitBtn = $(this).find('[type="submit"]')

			$form.find('.is-invalid').removeClass('is-invalid');
			$form.find('.invalid-feedback').html('');
			$submitBtn.ladda('start')

			// $form.find('.progress').show();

			ajaxSetup();

			$.ajax({
				url : `{{ route('save_profile') }}`,
				method : 'post',
				data : formData,
				contentType : false,
				processData : false,
				dataType : 'json',
				// beforeSend: function() {
				// 	$form.find('.progress-bar').css('width', '0%');
				// 	$form.find('.progress-bar').text('0%');
				// },
				// xhr : function() {
				// 	var xhr = new window.XMLHttpRequest();
				// 	xhr.upload.addEventListener('progress', function(e){
				// 		if(e.lengthComputable){
							
				// 			let uploaded    = e.loaded,
				// 				total       = e.total,
				// 				percent     = Math.round((uploaded / total) * 100);

				// 			$form.find('.progress-bar').css('width', percent+'%');
				// 			$form.find('.progress-bar').text(percent+'%');

				// 			if(percent == 100) {
				// 				$form.find('.progress-bar').html("<strong>Berhasil diupload</strong>");
				// 			}
				// 		}
				// 	});
				// 	return xhr;
				// }
			})
			.done(response => {
				ajaxSuccessHandling(response)

				setTimeout(() => {
					window.location.reload();
				}, 1000)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error)
			})
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
					$form.find('[name="id_position"]').val("{{ auth()->user()->registrant->id_position }}").trigger('change');
				} else {
					$form.find(`[name="id_position"]`).val('').trigger('change')
				}

				positionsReq++;
			})
		})

		$form.find('[name="id_department"]').val("{{ auth()->user()->registrant->id_department }}").trigger('change');

		$form.find('[name="shift_type"]').val("{{ auth()->user()->registrant->shift_type }}").trigger('change');

		setTimeout(() => {
			$form.find('[name="id_shift"]').val("{{ auth()->user()->registrant->id_shift }}").trigger('change');
		}, 500)
	});
</script>
@endsection


@section('modal')
<div class="modal inmodal fade" id="kriteriaModal" role="dialog"  aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"> Kriteria Foto </h4>
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			</div>
			<div class="modal-body">
				<h5><b> Ketentuan Foto </b></h5>
				<ul>
					<li> Tidak mengenakan masker, helm atau pelindung wajah lain nya </li>
					<li> Tidak terlalu gelap/terang </li>
					<li> Tidak buram/blur </li>
					<li> Bisa menggunakan gambar yang sudah ada atau ambil foto menggunakan kamera (khusus smartphone) </li>
					<li> Format foto yang di dukung : .jpg, .png, .jpeg </li>
					<li> Pastikan posisi wajah difoto berada di tengah. </li>
				</ul>
				<h5><b>Contoh</h5>
				<img src="{{ url('images/sample.jpg') }}" class="img-fluid">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal"><i class="mdi mdi-close"></i> Tutup</button>
			</div>
		</div>
	</div>
</div>
@endsection