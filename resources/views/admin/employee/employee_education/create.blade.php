@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					{!! Template::requiredBanner() !!}

					<div class="form-group">
						<label> Tingkat Pendidikan {!! Template::required() !!} </label>
						<select name="education_level" style="width: 100%;" required>
							@foreach(GlobalData::educationLevelAlt() as $educationLevel)
							<option value="{{ $educationLevel }}"> {{ $educationLevel }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>
					
					<div class="form-group">
						<label> Nama Sekolah/Kampus/Universitas {!! Template::required() !!} </label>
						<input type="text" name="school_name" class="form-control" placeholder="Nama Sekolah/Kampus/Universitas" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Nama Jurusan {!! Template::required() !!} </label>
						<input type="text" name="major_name" class="form-control" placeholder="Nama Jurusan" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tahun Awal {!! Template::required() !!} </label>
						<input type="int" name="year_start" class="form-control" placeholder="Tahun Awal" max="{{ date('Y') }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tahun Akhir {!! Template::required() !!} </label>
						<input type="int" name="year_end" class="form-control" placeholder="Tahun Akhir" max="{{ date('Y') }}" required>
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
			$form.find(`[name="education_level"]`).val('').trigger('change')
		}


		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();

			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ $ajaxRoute }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				init();
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response);
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});

		init();

		$form.find(`[name="education_level"]`).select2({
			'placeholder' : '- Pilih Tingkat Pendidikan -'
		})
	});
</script>
@endsection