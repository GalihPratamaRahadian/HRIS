@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					{!! \Setting::requiredBanner() !!}
					
					<div class="form-group">
						<label> Karyawan {!! \Setting::required() !!} </label>
						<select name="id_employee" style="width: 100%;" required>
							
							@foreach(\App\Models\Employee::all() as $employee)
							<option value="{{ $employee->id }}"> {{ $employee->employee_name }} </option>
							@endforeach

						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Jam Kerja {!! \Setting::required() !!} </label>
						<select name="id_shift" style="width: 100%;" required>
							
							@foreach(\App\Models\Shift::all() as $shift)
							<option value="{{ $shift->id }}"> {{ $shift->shift_name }} </option>
							@endforeach

						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Perubahan {!! \Setting::required() !!} </label>
						<input type="date" name="date" class="form-control" placeholder="Tanggal Perubahan" value="{{ $shiftChangeSchedule->changeAtText('Y-m-d') }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Jam Perubahan </label>
						<input type="text" name="time" class="form-control" placeholder="Jam Perubahan" value="{{ $shiftChangeSchedule->changeAtText('H:i') }}">
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

		$form.find(`[name="time"]`).clockpicker({
			placement: 'bottom',
			autoclose: true,
		});

		$form.find('[name="id_employee"]').select2({
			placeholder : '- Pilih Karyawan -'
		})

		$form.find('[name="id_shift"]').select2({
			placeholder : '- Pilih Jam Kerja -'
		})

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();
			$submitBtn.ladda('start')
			ajaxSetup();
			$.ajax({
				url : `{{ route('employee_shift_change_schedule.update', $shiftChangeSchedule->id) }}`,
				method : 'put',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error)
			})
		});

		$form.find('[name="id_employee"]').val(`{{ $shiftChangeSchedule->id_employee }}`).trigger('change')
		$form.find('[name="id_shift"]').val(`{{ $shiftChangeSchedule->id_shift }}`).trigger('change')
	});
</script>
@endsection