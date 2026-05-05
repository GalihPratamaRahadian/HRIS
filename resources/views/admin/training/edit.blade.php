@extends('template.backLayout')


@section('content')
<form id="mainForm">
	<div class="row">
		<div class="col-lg-6">
			<div class="card support-pane-card">
				<div class="card-body">
					@method('PUT')
					{!! Template::titleBanner($title) !!}

					{!! Template::requiredBanner() !!}
					
					<div class="form-group">
						<label> Judul {!! Template::required() !!} </label>
						<input type="text" name="title" class="form-control" placeholder="Judul" value="{{ $training->title }}">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Nama Trainer / Provider {!! Template::required() !!} </label>
						<input type="text" name="trainer_name" class="form-control" placeholder="Nama Trainer / Provider" value="{{ $training->trainer_name }}">
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

					<hr>

					<div class="form-group">
						<label> Status Publikasi {!! Template::required() !!} </label>
						<select class="form-control" name="is_published">
							<option value="Ya"> Publikasi </option>
							<option value="Tidak"> Tidak Dipublikasi </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Awal Pelaksanaan {!! Template::required() !!} </label>
						<input type="date" name="start_date" class="form-control" value="{{ $training->start_date->format('Y-m-d') }}">
						<a href="javascript:void(0);" class="clear-start-date small"> Kosongkan </a>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Akhir Pelaksanaan {!! Template::required() !!} </label>
						<p class="small text-muted mb-1"> * Status publikasi akan otomatis berubah menjadi "Tidak Dipublikasi" jika sudah melewati tanggal akhir pelaksanaan </p>
						<input type="date" name="end_date" class="form-control" value="{{ $training->end_date->format('Y-m-d') }}">
						<a href="javascript:void(0);" class="clear-end-date small"> Kosongkan </a>
						<span class="invalid-feedback"></span>
					</div>

					<hr>

					<div class="form-group">
						<button class="btn btn-success" type="submit">
							<i class="mdi mdi-check"></i> Simpan
						</button>
					</div>
					
				</div>
			</div>
		</div>
	</div>

</form>
@endsection


@section('script')
<script type="text/javascript">
	$(function(){

		let $form = $('#mainForm')
		let $submitBtn = $form.find('[type="submit"]').ladda();

		const init = () => {
			$form.find('[name="title"]').focus();
		}

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);
			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('admin.training.update', $training->id) }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				contentType: false,
				processData: false
			})
			.done(response => {
				ajaxSuccessHandling(response);
				$submitBtn.ladda('stop')
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});

		$form.find(`[name="id_department"]`).select2({
			'placeholder': '- Pilih Departemen',
		})

		$form.find(`[name="id_position"]`).select2({
			'placeholder': '- Pilih Jabatan',
		})

		$form.find(`[name="id_employee_group"]`).select2({
			'placeholder': '- Pilih Grup Karyawan',
		})

		$form.find(`.clear-start-date`).on('click', function(){
			$form.find(`[name="start_date"]`).val('')
		})

		$form.find(`.clear-end-date`).on('click', function(){
			$form.find(`[name="end_date"]`).val('')
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

		$form.find(`[name="id_department"]`).val(`{{ $training->id_department ?? 'all' }}`).trigger('change')
		$form.find(`[name="id_employee_group"]`).val(`{{ $training->id_employee_group ?? 'all' }}`).trigger('change')
		$form.find(`[name="is_published"]`).val(`{{ $training->is_published }}`).trigger('change')
		setTimeout(() => {
			$form.find(`[name="id_position"]`).val(`{{ $training->id_position ?? 'all' }}`).trigger('change')
		}, 500)
	});
</script>
@endsection