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
						<label> Alasan Izin {!! Template::required() !!} </label>
						<input type="text" name="reason" class="form-control" placeholder="Alasan Izin" value="{{ $necessityReason->reason }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Dengan Durasi Maksimal {!! Template::required() !!} </label>
						<select class="form-control" name="is_using_max_duration" required>
							<option value="no"> Tidak </option>
							<option value="yes"> Ya </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div id="max-duration"></div>

					<div class="form-group">
						<label> Terhitung Hadir {!! Template::required() !!} </label>
						<select class="form-control" name="is_counted_present" required>
							<option value="no"> Tidak </option>
							<option value="yes"> Ya </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Wajib Melampirkan File {!! Template::required() !!} </label>
						<select class="form-control" name="is_required_file" required>
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
<script type="text/html" id="max-duration-template">
	<div class="form-group">
		<label> Durasi Maksimal (Hari) {!! Template::required() !!} </label>
		<input type="number" name="max_duration" class="form-control" min="1" placeholder="Durasi Maksimal (Hari)" required>
		<span class="invalid-feedback"></span>
	</div>
</script>

<script type="text/javascript">
	$(function(){

		let $form = $('#mainForm')
		let $submitBtn = $form.find('[type="submit"]').ladda();

		const resetForm = () => {
			$form[0].reset();
		}

		const init = () => {
			// resetForm();
			$form.find('[name="reason"]').focus();
		}


		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();

			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('admin.necessity_reason.update', $necessityReason->id) }}`,
				method : 'put',
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

		$form.find(`[name="is_using_max_duration"]`).on('change', function(){
			const val = $(this).val();

			if(val == 'yes') {
				$('#max-duration').html($('#max-duration-template').html())
			} else {
				$('#max-duration').empty();
			}
		})

		$form.find(`[name="leave_type"]`).val(`{{ $necessityReason->leave_type }}`)

		$form.find(`[name="is_using_max_duration"]`).val(`{{ $necessityReason->is_using_max_duration }}`).trigger('change')
		setTimeout(() => {
			$form.find(`[name="max_duration"]`).val(`{{ $necessityReason->max_duration }}`)
		}, 500)

		$form.find(`[name="is_counted_present"]`).val(`{{ $necessityReason->is_counted_present }}`)
		$form.find(`[name="is_required_file"]`).val(`{{ $necessityReason->is_required_file }}`)


		init();
	});
</script>
@endsection