@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-8">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					{!! Template::requiredBanner() !!}
					
					<div class="form-group">
						<label> Nama Jabatan {!! Template::required() !!} </label>
						<input type="text" name="position_name" class="form-control" placeholder="Nama Jabatan" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Departemen {!! Template::required() !!} </label>
						<select name="id_department" style="width: 100%;" required>
							@foreach(\App\Models\Department::all() as $department)
							<option value="{{ $department->id }}"> {{ $department->department_name }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Penyetuju Cuti/Lembur 1 </label>
						<select name="approver_1" style="width: 100%;">
							<option value="0"> - Tanpa Penyetuju - </option>
							@foreach(\App\Models\Position::all() as $position)
							<option value="{{ $position->id }}"> {{ $position->position_name }} - {{ $position->departmentName() }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Penyetuju Cuti/Lembur 2 </label>
						<select name="approver_2" style="width: 100%;" required>
							<option value="0"> - Tanpa Penyetuju - </option>
							@foreach(\App\Models\Position::all() as $position)
							<option value="{{ $position->id }}"> {{ $position->position_name }} - {{ $position->departmentName() }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Deskripsi Pekerjaan </label>
						<textarea class="form-control" name="job_description" rows="10" placeholder="Deskripsi Pekerjaan"></textarea>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Sasaran Kinerja </label>
						<textarea class="form-control" name="performance_goals" rows="10" placeholder="Sasaran Kinerja"></textarea>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Kompetensi </label>
						<textarea class="form-control" name="competence" rows="10" placeholder="Kompetensi"></textarea>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Wajib Melakukan Absensi {!! Template::required() !!} </label>
						<select name="is_must_attend" class="form-control" required>
							<option value="Ya"> Ya </option>
							<option value="Tidak"> Tidak </option>
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

		let $form = $('#mainForm')
		let $submitBtn = $form.find('[type="submit"]').ladda();

		$form.find(`[name="id_department"]`).select2({
			'placeholder': '- Pilih Departemen -'
		})

		$form.find(`[name="approver_1"]`).select2({
			'placeholder': '- Pilih Penyetuju Cuti/Lembur 1 -'
		})

		$form.find(`[name="approver_2"]`).select2({
			'placeholder': '- Pilih Penyetuju Cuti/Lembur 2 -'
		})

		const resetForm = () => {
			$form[0].reset();
		}

		const init = () => {
			resetForm();
			$form.find('[name="position_name"]').focus();
			$form.find('[name="id_department"]').val('').trigger('change');
			$form.find('[name="approver_1"]').val('0').trigger('change');
			$form.find('[name="approver_2"]').val('0').trigger('change');
			$form.find(`[name="job_description"]`).summernote('reset')
			$form.find(`[name="performance_goals"]`).summernote('reset')
			$form.find(`[name="competence"]`).summernote('reset')
		}


		$form.on('submit', function(e){
			e.preventDefault();

			let formData = $(this).serialize();

			$submitBtn.ladda('start')
			ajaxSetup();
			$.ajax({
				url : `{{ route('admin.position.store') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
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

		$form.find(`[name="job_description"]`).summernote({
			height: '300px'
		})

		$form.find(`[name="performance_goals"]`).summernote({
			height: '300px'
		})

		$form.find(`[name="competence"]`).summernote({
			height: '300px'
		})

		init();
	});
</script>
@endsection