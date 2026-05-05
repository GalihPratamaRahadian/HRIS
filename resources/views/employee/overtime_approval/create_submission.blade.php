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
						<label> Karyawan {!! \Setting::required() !!} </label>
						<select name="id_employee" style="width: 100%;" required>
							
							@foreach(auth()->user()->employee->getStaffs() as $employee)
							<option value="{{ $employee->id }}"> {{ $employee->employee_name }} | {{ $employee->departmentName() }} </option>
							@endforeach

						</select>
						<span class="invalid-feedback"></span>
					</div>
					
					<div class="form-group">
						<label> Alasan {!! \Setting::required() !!} </label>
						<select name="id_overtime_reason" style="width: 100%;" required>
							
							@foreach(\App\Models\OvertimeReason::all() as $overtimeReason)
							<option value="{{ $overtimeReason->id }}"> {{ $overtimeReason->reason }} </option>
							@endforeach

						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Mulai Lembur {!! \Setting::required() !!} </label>
								<input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
								<span class="invalid-feedback"></span>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Jam Mulai Lembur {!! \Setting::required() !!} </label>
								<input type="text" name="clock_start" placeholder="Jam Mulai Lembur" class="form-control" autocomplete="off" readonly required style="background-color: white;">
								<span class="invalid-feedback"></span>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Selesai Lembur {!! \Setting::required() !!} </label>
								<input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
								<span class="invalid-feedback"></span>
							</div>
						</div>
						
						<div class="col-lg-6">
							<div class="form-group">
								<label> Jam Selesai Lembur {!! \Setting::required() !!} </label>
								<input type="text" name="clock_end" placeholder="Jam Selesai Lembur" class="form-control" autocomplete="off" readonly required style="background-color: white;">
								<span class="invalid-feedback"></span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label> Deskripsi {!! \Setting::required() !!} </label>
						<textarea class="form-control" name="description" rows="3" placeholder="Deskripsi" required></textarea>
						<span class="invalid-feedback"></span>
					</div>

					<hr>

					<div class="form-group">
						<button class="btn btn-success" type="submit">
							<i class="mdi mdi-check"></i> Buat
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

		const checkTimeValidity = (selector) => {
			const minTime = (new Date).getHours() + ':' + (new Date).getMinutes()
			const time = $form.find(selector).val()

			if(time != '') {
				if(minTime > time) {
					const message = `Jam yang diinput harus diatas jam ${minTime}`
					warningNotification('Peringatan', message)
					$form.find(selector).addClass('is-invalid')
					$form.find(selector).siblings('.invalid-feedback').text(message)
					$form.find(selector).val('')
				}
			}
		}

		$form.find('[name="id_employee"]').select2({
			placeholder: '- Pilih Karyawan -'
		})
		$form.find('[name="id_employee"]').val('').trigger('change')

		$form.find('[name="id_overtime_reason"]').select2({
			placeholder: '- Pilih Alasan -'
		})
		$form.find('[name="id_overtime_reason"]').val('').trigger('change')
		$form.find(`[name="clock_start"], [name="clock_end"]`).clockpicker({
			autoclose: true,
			afterDone: function() {
				clearInvalid()
				checkTimeValidity(`[name="clock_start"]`)
				checkTimeValidity(`[name="clock_end"]`)
			},
		})

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);

			$submitBtn.ladda('start')
			ajaxSetup();
			$.ajax({
				url : `{{ route('employee.overtime_approval.store_submission') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				contentType : false,
				processData : false,
			})
			.done(response => {
				ajaxSuccessHandling(response)

				setTimeout(() => {
					window.location.href = `{{ route('employee.overtime_approval') }}`
				}, 1000)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});

	});
</script>
@endsection