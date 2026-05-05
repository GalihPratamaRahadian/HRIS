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
						<label> Course {!! Template::required() !!} </label>
						<select name="id_course" style="width: 100%" required>
							@foreach(\App\Models\Course::all() as $course)
							<option value="{{ $course->id }}"> {{ $course->course_title }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Durasi (Satuan Menit) {!! Template::required() !!} </label>
						<input type="number" name="duration" class="form-control" placeholder="Durasi (Satuan Menit)" value="{{ $courseExam->duration }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Acak Pertanyaan {!! Template::required() !!} </label>
						<select class="form-control" name="is_published">
							<option value="no"> Tidak </option>
							<option value="yes"> Ya </option>
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
			// resetForm();
			$form.find('[name="title"]').focus();
		}

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();
			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('course_exam.update', $courseExam->id) }}`,
				method : 'put',
				data : formData,
				dataType : 'json'
			})
			.done(response => {
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response);
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});

		$form.find(`[name="id_course"]`).select2({
			'placeholder': '- Pilih Course -'
		})
		$form.find(`[name="id_course"]`).val(`{{ $courseExam->id_course }}`).trigger('change')
		$form.find(`[name="is_random_question"]`).val(`{{ $courseExam->is_random_question }}`).trigger('change')

		init();
	});
</script>
@endsection