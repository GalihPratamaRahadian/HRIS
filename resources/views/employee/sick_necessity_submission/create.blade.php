@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> {{ $title }} </h4>
				</div>

				<form id="form">
					{!! \Setting::requiredBanner() !!}

					<div class="form-group">
						<label> Jenis {!! \Setting::required() !!} </label>
						<select name="type" class="form-control" required>
							<option selected disabled> - Pilih - </option>
							<option value="Sakit"> Sakit </option>
							<option value="Izin"> Izin </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>
					
					<div class="form-group">
						<div class="reason"></div>
						<span class="invalid-feedback"></span>
						<input type="hidden" name="reason">
					</div>

					<div class="form-group">
						<label> Tanggal Awal {!! \Setting::required() !!} </label>
						<input type="date" name="start_date" class="form-control" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Akhir {!! \Setting::required() !!} </label>
						<input type="date" name="end_date" class="form-control" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Deskripsi </label>
						<textarea class="form-control" name="description" rows="2" placeholder="Deskripsi"></textarea>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Lampiran File </label>
						<input type="file" name="file_attachment" class="form-control">
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

		const $form = $('#form')
		const $submitBtn = $form.find(`[type="submit"]`).ladda();


		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);

			$submitBtn.ladda('start')
			$submitBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');
			ajaxSetup();
			$.ajax({
				url : `{{ route('employee.sick_necessity_submission.store') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				contentType : false,
				processData : false,
			})
			.done(response => {
				ajaxSuccessHandling(response)
				$submitBtn.prop('disabled', false).html('<i class="mdi mdi-check"></i> Simpan');

				setTimeout(() => {
					window.location.href = `{{ route('employee.sick_necessity_submission') }}`
				}, 1000)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});

		$form.find(`[name="type"]`).on('change', function(){
			const type = $(this).val()

			if(type == 'Sakit') {
				$('.reason').html($('#sick-reason-template').html())
			} else {
				$('.reason').html($('#necessity-reason-template').html())
			}

			renderEvent()
		})


		const renderEvent = () => {
			$form.find('[name="id_sick_reason"]').select2({
				placeholder: '- Pilih Alasan -'
			})
			$form.find('[name="id_sick_reason"]').val('').trigger('change')

			$form.find('[name="id_necessity_reason"]').select2({
				placeholder: '- Pilih Alasan -'
			})
			$form.find('[name="id_necessity_reason"]').val('').trigger('change')

			$form.find('[name="id_sick_reason"]').on('change', function(){
				const reason = $(this).find('option:selected').data('reason');
				$form.find(`[name="reason"]`).val(reason)
			})

			$form.find('[name="id_necessity_reason"]').on('change', function(){
				const reason = $(this).find('option:selected').data('reason');
				$form.find(`[name="reason"]`).val(reason)
			})
		}

	});
</script>

<script type="text/html" id="sick-reason-template">
	<label> Alasan Sakit {!! \Setting::required() !!} </label>
	<select name="id_sick_reason" style="width: 100%;" required>
		@foreach(\App\Models\SickReason::all() as $sickReason)
		<option value="{{ $sickReason->id }}" data-reason="{{ $sickReason->reason }}"> {{ $sickReason->reasonWithDurationText() }} </option>
		@endforeach
	</select>
</script>

<script type="text/html" id="necessity-reason-template">
	<label> Alasan Izin {!! \Setting::required() !!} </label>
	<select name="id_necessity_reason" style="width: 100%;" required>
		@foreach(\App\Models\NecessityReason::all() as $necessityReason)
		<option value="{{ $necessityReason->id }}" data-reason="{{ $necessityReason->reason }}"> {{ $necessityReason->reasonWithDurationText() }} </option>
		@endforeach
	</select>
</script>
@endsection