@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-lg-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					{!! Template::requiredBanner() !!}

					@method('PUT')
					
					<div class="form-group">
						<label> Judul {!! Template::required() !!} </label>
						<input type="text" name="title" class="form-control" placeholder="Judul" value="{{ $announcement->title }}">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Departemen </label>
						<select name="id_department" style="width: 100%">
							<option value="all"> - Semua Departemen - </option>
							@foreach(\App\Models\Department::all() as $department)
							<option value="{{ $department->id }}"> {{ $department->department_name }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Jabatan </label>
						<select name="id_position" style="width: 100%">
							<option value="all"> - Semua Jabatan - </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Grup Karyawan </label>
						<select name="id_employee_group" style="width: 100%">
							<option value="all"> - Semua Grup Karyawan - </option>
							@foreach(\App\Models\EmployeeGroup::all() as $employeeGroup)
							<option value="{{ $employeeGroup->id }}"> {{ $employeeGroup->group_name }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Konten (Opsional) </label>
						<textarea class="form-control" name="content" rows="3" placeholder="Masukkan konten">{{ $announcement->content }}</textarea>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> File (Jika ingin ganti file) </label>
						<input type="file" name="file_announcement" class="form-control">
						<p class="small text-primary mt-2"> <i class="mdi mdi-information"></i> Hanya mendukung file gambar (.png, .jpeg, .gif), dokumen (.pdf, .xlsx, .docx), video (.mp4, .mkv) </p>
						<span class="invalid-feedback"></span>
					</div>

					<hr>

					<div class="form-group">
						<label> Status Publikasi </label>
						<select class="form-control" name="is_published">
							<option value="yes"> Publikasi </option>
							<option value="no"> Draft </option>
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

		const resetForm = () => {
			// $form[0].reset();
		}

		const init = () => {
			resetForm();
			$form.find('[name="title"]').focus();
		}


		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);

			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('announcement.update', $announcement->id) }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				contentType: false,
				processData: false
			})
			.done(response => {
				// init();
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response);
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});

		$form.find(`[name="id_department"]`).select2({
			'placeholder': '- Pilih Departemen -',
		})

		$form.find(`[name="id_position"]`).select2({
			'placeholder': '- Pilih Jabatan -',
		})

		$form.find(`[name="id_employee_group"]`).select2({
			'placeholder': '- Pilih Grup Karyawan -',
		})
		let initPosition = true;

		$form.find(`[name="id_department"]`).on('change', function(){
			const departmentId = $(this).val()
			$.get({
				url: `{{ route('helper.get_positions') }}?id_department=${departmentId}`,
				dataType: 'json'
			})
			.done(response => {
				const { positions } = response
				let html = '<option value="all"> - Semua Jabatan - </option>'
				positions.forEach(position => {
					html += `<option value="${position.id}"> ${position.position_name} </option>`
				})

				$form.find(`[name="id_position"]`).html(html)

				if(initPosition) {
					$form.find(`[name="id_position"]`).val(`{{ $announcement->id_position ?? 'all' }}`).trigger('change')
					initPosition = false
				} else {
					$form.find(`[name="id_position"]`).val('all').trigger('change')
				}
			})
		})

		$form.find(`[name="id_department"]`).val(`{{ $announcement->id_department ?? 'all' }}`).trigger('change')
		$form.find(`[name="id_employee_group"]`).val(`{{ $announcement->id_employee_group ?? 'all' }}`).trigger('change')

		init();
	});
</script>
@endsection