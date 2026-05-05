@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-lg-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					{!! Template::requiredBanner() !!}
					
					<div class="form-group">
						<label> Judul Course {!! Template::required() !!} </label>
						<input type="text" name="course_title" class="form-control" placeholder="Judul Course">
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
						<label> Sumber Video {!! Template::required() !!} </label>
						<select name="video_source" class="form-control">
							<option value="" disabled selected> - Pilih Sumber Video - </option>
							<option value="link"> Link Youtube </option>
							<option value="file"> Upload File Video </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div id="video-source"></div>

					<hr>

					<div class="form-group">
						<label> Status Publikasi {!! Template::required() !!} </label>
						<select class="form-control" name="is_published">
							<option value="yes"> Publikasi </option>
							<option value="no"> Tidak Dipublikasi </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tenggat Waktu Course </label>
						<p class="small text-muted mb-1"> * Status publikasi akan otomatis berubah menjadi "Tidak Dipublikasi" sesuai dengan tenggat waktu yang ditentukan </p>
						<input type="date" name="deadline" class="form-control">
						<a href="javascript:void(0);" class="clear-deadline small"> Kosongkan </a>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Syarat Kelulusan {!! Template::required() !!} </label>
						<select class="form-control" name="pass_requirement">
							<option selected disabled> - Pilih - </option>
							<option value="pass_video"> Menyelesaikan Video Course </option>
							<option value="pass_exam"> Lulus Exam </option>
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
			$form[0].reset();
		}

		const init = () => {
			resetForm();
			$form.find('[name="course_title"]').focus();
		}

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);
			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('admin.course.store') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				contentType: false,
				processData: false
			})
			.done(response => {
				// init();
				// $submitBtn.ladda('stop')
				ajaxSuccessHandling(response);
				setTimeout(() => {
					window.location.href = `{{ route('course') }}`
				}, 1000)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});

		$form.find(`[name="video_source"]`).on('change', function(){
			const val = $(this).val()
			let html = '';

			if(val == 'link') {
				html = $('#videoSourceLinkTemplate').text();
			} else if(val == 'file') {
				html = $('#videoSourceFileTemplate').text();
			}

			$('#video-source').html(html)
		})

		$form.find(`[name="id_department"]`).select2({
			'placeholder': '- Pilih Departemen',
		})

		$form.find(`[name="id_position"]`).select2({
			'placeholder': '- Pilih Jabatan',
		})

		$form.find(`[name="id_employee_group"]`).select2({
			'placeholder': '- Pilih Grup Karyawan',
		})

		$form.find(`.clear-deadline`).on('click', function(){
			$form.find(`[name="deadline"]`).val('')
		})

		$form.find(`[name="id_department"]`).on('change', function(){
			const departmentId = $(this).val()
			$.get({
				url: `{{ route('helper.get_positions') }}?id_department=${departmentId}`,
				dataType: 'json'
			})
			.done(response => {
				const { positions } = response
				let html = ''
				html += `<option value="all"> - Semua Jabatan - </option>`

				positions.forEach(position => {
					html += `<option value="${position.id}"> ${position.position_name} </option>`
				})

				$form.find(`[name="id_position"]`).html(html)
				$form.find(`[name="id_position"]`).val('all').trigger('change')
			})
		})

		init();
	});
</script>

<script type="text/html" id="videoSourceLinkTemplate">
	<div class="form-group">
		<label> Link Youtube {!! Template::required() !!} </label>
		<input type="text" name="video_link" class="form-control" placeholder="https://youtube.com/xxxxx">
		<p class="small text-primary mt-2"> <i class="mdi mdi-information"></i> Contoh Link : https://www.youtube.com/watch?v=nMNkIdPmIzU </p>
		<span class="invalid-feedback"></span>
	</div>
</script>

<script type="text/html" id="videoSourceFileTemplate">
	<div class="form-group">
		<label> File Video {!! Template::required() !!} </label>
		<input type="file" name="file_video" class="form-control" accept=".mp4,.mkv">
		<p class="small text-primary mt-2"> <i class="mdi mdi-information"></i> Hanya mendukung file video (.mp4, .mkv) </p>
		<span class="invalid-feedback"></span>
	</div>
</script>
@endsection