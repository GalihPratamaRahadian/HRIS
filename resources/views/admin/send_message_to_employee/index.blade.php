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
						<label> Karyawan </label>
						<select name="id_employee" style="width: 100%">
							@foreach(\App\Models\Employee::with('department')->get() as $emp)
							<option value="{{ $emp->id }}" data-phone-number="{{ $emp->phone_number }}"> {{ $emp->employee_name }} [{{ $emp->departmentName() }}] </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Nomor Whatsapp </label>
						<input type="text" class="form-control" name="phone_number" placeholder="Nomor Telepon" readonly>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Konten (Opsional) </label>
						<textarea class="form-control" name="message" rows="3" placeholder="Masukkan konten"></textarea>
						<span class="invalid-feedback"></span>
					</div>

					<hr>

					<div class="form-group">
						<button class="btn btn-success" type="submit">
							<i class="mdi mdi-send"></i> Kirim
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
			$form.find(`[name="id_employee"]`).val('').trigger('change')
		}

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();
			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('admin.send_message_to_employee.send') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response);
				resetForm()
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});

		$form.find(`[name="id_employee"]`).select2({
			'placeholder': '- Pilih Karyawan -',
		})

		$form.find(`[name="id_employee"]`).on('change', function(){
			const val = $(this).val()

			if(val) {
				const phone = $(this).find('option:selected').data('phoneNumber')
				$form.find(`[name="phone_number"]`).val(phone)
			} else {
				$form.find(`[name="phone_number"]`).val('-')
			}
		})

		resetForm()

		@if($employee)
		$form.find(`[name="id_employee"]`).val(`{{ $employee->id }}`).trigger('change')
		@endif
	});
</script>
@endsection