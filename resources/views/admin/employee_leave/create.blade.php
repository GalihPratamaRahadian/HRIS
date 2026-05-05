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
							<option value="{{ $employee->id }}">
								{{ $employee->employee_name }} | {{ $employee->departmentName() }} | Jatah Cuti : {{ $employee->leaveQuotaAvailable() }} hari
							</option>
							@endforeach

						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Alasan {!! \Setting::required() !!} </label>
						<select class="form-control" name="reason" required>
							
							@foreach(\App\Models\LeaveReason::all() as $reason)
							<option value="{{ $reason->reason }}"> {{ $reason->reason }} </option>
							@endforeach

						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Awal {!! \Setting::required() !!} </label>
						<input type="date" name="start_date" class="form-control" placeholder="Tanggal Awal" min="{{ date('Y-m-01') }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Akhir {!! \Setting::required() !!} </label>
						<input type="date" name="end_date" class="form-control" placeholder="Tanggal Akhir" min="{{ date('Y-m-01') }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Keterangan {!! \Setting::required() !!} </label>
						<textarea name="description" class="form-control" placeholder="Keterangan" required></textarea>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> File </label>
						<input type="file" name="file" class="form-control" placeholder="File">
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
			$form.find('[name="id_employee"]').val('').trigger('change')
			$form.find('[name="reason"]').val('').trigger('change')
		}

		$form.find('[name="id_employee"]').select2({
			placeholder : '- Pilih Karyawan -'
		})

		$form.find('[name="reason"]').select2({
			placeholder : '- Pilih Alasan -'
		})

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);
			$submitBtn.ladda('start')
			ajaxSetup();
			$.ajax({
				url : `{{ route('admin.employee_leave.store') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				contentType : false,
				processData : false,
			})
			.done(response => {
				init();
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response)
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