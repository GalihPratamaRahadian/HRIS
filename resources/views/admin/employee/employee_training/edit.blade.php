@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					{!! Template::requiredBanner() !!}

					@method('PUT')
					
					<div class="form-group">
						<label> Nama Pelatihan {!! Template::required() !!} </label>
						<input type="text" name="training_name" class="form-control" placeholder="Nama Pelatihan" value="{{ $employeeTraining->training_name }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Awal {!! Template::required() !!} </label>
						<input type="date" name="date_start" class="form-control" max="{{ date('Y-m-d') }}" value="{{ $employeeTraining->date_start }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Akhir {!! Template::required() !!} </label>
						<input type="date" name="date_end" class="form-control" max="{{ date('Y-m-d') }}" value="{{ $employeeTraining->date_end }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Provider / Penyedia {!! Template::required() !!} </label>
						<input type="text" name="provider" class="form-control" placeholder="Provider/Penyedia" value="{{ $employeeTraining->provider }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> File (Opsional) </label>
						<input type="file" name="file_training" class="form-control">
						<p class="small">
							Download File <a href="{{ $employeeTraining->fileLink() }}" target="_blank"> Klik Disini </a>
						</p>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Keterangan </label>
						<textarea class="form-control" name="description" placeholder="Keterangan" rows="3">{{ $employeeTraining->description }}</textarea>
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
			$form.find('[name="training_name"]').focus();
		}


		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);

			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ $ajaxRoute }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				processData: false,
				contentType: false
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
	});
</script>
@endsection