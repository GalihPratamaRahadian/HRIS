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
						<label> Nama {!! Template::required() !!} </label>
						<input type="text" name="name" class="form-control" placeholder="Nama" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Status Hubungan {!! Template::required() !!} </label>
						<select name="relationship_status" style="width: 100%;" required>
							@foreach(GlobalData::relationshipStatus() as $relationshipStatus)
							<option value="{{ $relationshipStatus }}"> {{ $relationshipStatus }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tempat Lahir {!! Template::required() !!} </label>
						<input type="text" name="place_of_birth" class="form-control" placeholder="Tempat Lahir" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Lahir {!! Template::required() !!} </label>
						<input type="date" name="date_of_birth" class="form-control" max="{{ date('Y-m-d') }}" required>
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
			$form.find(`[name="relationship_status"]`).val('').trigger('change')
			$form.find('[name="name"]').focus();
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

		$form.find(`[name="relationship_status"]`).select2({
			'placeholder' : '- Pilih Status Hubungan -'
		})
	});
</script>
@endsection