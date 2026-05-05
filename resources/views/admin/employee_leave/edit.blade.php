@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					{!! \Setting::requiredBanner() !!}

					@method('put')
					
					<div class="form-group">
						<label> Karyawan {!! \Setting::required() !!} </label>
						<select name="id_employee" style="width: 100%;" required>
							
							@foreach(\App\Models\Employee::all() as $employee)
							<option value="{{ $employee->id }}">
								{{ $employee->employee_name }} | {{ $employee->departmentName() }}
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
						<input type="date" name="start_date" class="form-control" placeholder="Tanggal Awal" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Akhir {!! \Setting::required() !!} </label>
						<input type="date" name="end_date" class="form-control" placeholder="Tanggal Akhir" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Keterangan </label>
						<textarea name="description" class="form-control" placeholder="Keterangan"></textarea>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Penggajian {!! \Setting::required() !!} </label>
						<select class="form-control" name="salary" required>
							@foreach(GlobalData::salaryPercentForEmployeeLeave() as $val => $label)
							<option value="{{ $val }}"> {{ $label }} </option>
							@endforeach
						</select>
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

		$('[name="id_employee"]').select2({
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
				url : `{{ route('admin.employee_leave.update', $employeeLeave->id) }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				contentType : false,
				processData : false,
			})
			.done(response => {
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});

		$form.find('[name="id_employee"]').val(`{{ $employeeLeave->id_employee }}`).trigger('change')
		$form.find('[name="reason"]').val(`{{ $employeeLeave->reason }}`).trigger('change')
		$form.find('[name="start_date"]').val(`{{ $employeeLeave->start_date }}`)
		$form.find('[name="end_date"]').val(`{{ $employeeLeave->end_date }}`)
		$form.find('[name="description"]').val(`{{ $employeeLeave->description }}`)
	});
</script>
@endsection