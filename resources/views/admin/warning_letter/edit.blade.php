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
						<label> Karyawan {!! Template::required() !!} </label>
						<select name="id_employee" style="width: 100%;" required>
							@foreach(\App\Models\Employee::all() as $employee)
							<option value="{{ $employee->id }}"> {{ $employee->employee_name }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Jenis {!! Template::required() !!} </label>
						<select name="type" style="width: 100%;" required>
							<option value="Teguran"> Teguran </option>
							<option value="SP1"> SP1 </option>
							<option value="SP2"> SP2 </option>
							<option value="SP3"> SP3 </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Jangka Waktu Awal {!! Template::required() !!} </label>
								<input type="date" name="start_date" class="form-control" placeholder="Jangka Waktu Awal" value="{{ $warningLetter->start_date }}" required>
								<span class="invalid-feedback"></span>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Jangka Waktu Akhir {!! Template::required() !!} </label>
								<input type="date" name="end_date" class="form-control" placeholder="Jangka Waktu Akhir" value="{{ $warningLetter->end_date }}" required>
								<span class="invalid-feedback"></span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label> Pesan </label>
						<textarea class="form-control" name="message" rows="4" placeholder="Pesan">{{ $warningLetter->message }}</textarea>
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

		$form.find(`[name="id_employee"]`).select2({
			'placeholder': '- Pilih Karyawan -'
		})

		$form.find(`[name="type"]`).select2({
			'placeholder': '- Pilih Jenis -'
		})

		const resetForm = () => {
			$form[0].reset();
		}

		const init = () => {
			// resetForm();
		}


		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();

			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('warning_letter.update', $warningLetter->id) }}`,
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

		$form.find(`[name="id_employee"]`).val('{{ $warningLetter->id_employee }}').trigger('change')
		$form.find(`[name="type"]`).val('{{ $warningLetter->type }}').trigger('change')
	});
</script>
@endsection