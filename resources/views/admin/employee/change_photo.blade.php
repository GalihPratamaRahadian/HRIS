@extends('template.backLayout')


@section('content')
<div class="row">

	<div class="col-lg-6">
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				
				{!! Template::titleBanner($title) !!}

				<form id="form">
					
					<div class="form-group">
						<label><b> Nama Karyawan </b></label> <br>
						<label> {{ $employee->employee_name }} </label>
					</div>

					<div class="form-group">
						<label><b> Foto </b> {!! Template::required() !!}</label>
						<br>
						<input type="file" name="photo" class="form-control" required>
						<p class="text-muted small"> * Hanya mendukung format jpeg, jpg dan png </p>
					</div>

					<hr>

					<div class="form-group">
						<button type="submit" class="btn btn-success">
							<i class="mdi mdi-check"></i> Simpan
						</button>
					</div>

				</form>

			</div>
		</div>
	</div>

	<div class="col-lg-6" id="photo-section" style="display: none;">
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				
				{!! Template::titleBanner("Preview Foto") !!}

				<img class="img-fluid rounded" id="photo-preview">

			</div>
		</div>
	</div>

</div>
@endsection


@section('script')
<script type="text/javascript">
	
	$(function(){

		let $form = $('#form');
		let $submitBtn = $form.find(`[type="submit"]`).ladda();


		$form.find(`[name="photo"]`).on('change', function(e){
			let file = $(this).val();

			toastrAlert();
			
			if(file !== "") {
				let fileType = this.files[0].type;

				if(fileType.substring(0, 5) == "image") {

					let ext = fileType.split('/');
					ext = ext[1];
					let allowedExts = [ 'jpeg', 'jpg', 'png' ];

					if(jQuery.inArray(ext, allowedExts) > -1) {
						let reader = new FileReader();

						reader.onload = function(e) {
							$('#photo-preview').attr('src', e.target.result);
						}
						reader.readAsDataURL(this.files[0]);

						$('#photo-section').show();
						return;
					} else {
						toastr.warning('Hanya mendukung format jpeg, jpg, png', 'Peringatan');
					}

				} else {
					toastr.warning('File wajib berupa foto', 'Peringatan');
				}
			}

			$(this).val('');
			$('#photo-section').hide();

		});


		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);
			$submitBtn.ladda('start');

			ajaxSetup();
			$.ajax({
				url: `{{ route('employee.save_photo', $employee->id) }}`,
				method: 'post',
				data : formData,
				dataType : 'json',
				contentType : false,
				processData : false,
			})
			.done(response => {
				ajaxSuccessHandling(response)
				setTimeout(() => {
					window.location.href = `{{ route('employee.detail', $employee->id) }}`
				}, 1000)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})

		})

	})

</script>
@endsection