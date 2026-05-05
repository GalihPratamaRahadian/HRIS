@extends('template.backLayout')


@section('content')
<?php 
	$wajibDiisi = '<span class="text-danger"> * </span>';
?>
<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> {{ $title }} </h4>
				</div>

				<form id="mainForm">
					<div class="alert alert-info">
						Kolom bertanda {!! $wajibDiisi !!} wajib diisi.
					</div>
					
					<div class="form-group">
						<label> Karyawan </label> <br>
						<label><b> {{ $attendance->employeeName() }} </b></label>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tipe Kehadiran </label> <br>
						<label><b> {!! $attendance->typeHtml() !!} </b></label>
					</div>

					<div class="form-group">
						<label> Foto {!! Setting::required() !!} </label>
						<?php 
							$meta = $attendance->isHasMeta() ? $attendance->attendanceMeta : false;
						?>
						@if($meta)
						<img src="{{ $meta->clockInPhotoLink() }}" class="img-fluid mb-3">
						@endif
						<input type="file" name="clock_in_photo" class="form-control" required>
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

		const form = $('#mainForm')
		const submitBtn = form.find('[type="submit"]').ladda();

		const resetForm = () => {
			form[0].reset();
		}

		const init = () => {
			$('[name="type"]').focus();
		}


		form.on('submit', function(e){
			e.preventDefault();

			let formData = new FormData(this);

			submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('attendance.update_clock_in_photo', $attendance->id) }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				processData: false,
				contentType: false,
			})
			.done(response => {
				init();
				submitBtn.ladda('stop')

				let { message, code } = response

				if(code == 200) {
					toastrAlert();
					toastr.success(message, 'Berhasil');
				}
			})
			.fail(error => {
				submitBtn.ladda('stop')

				let { status, responseJSON } = error
				let { message, errors } = responseJSON

				if(status == 422) {
					invalidResponse(form, errors)
				}

				toastrAlert();
				toastr.warning(message, 'Peringatan');
			})
		});

		init();
	});
</script>
@endsection