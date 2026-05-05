@extends('template.backLayout')


@section('content')
<?php 
	$wajibDiisi = '<span class="text-danger"> * </span>';
?>
<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					<div class="alert alert-info">
						Kolom bertanda {!! $wajibDiisi !!} wajib diisi.
					</div>
					
					<div class="form-group">
						<label> Karyawan {!! $wajibDiisi !!} </label>
						<select name="id_employee" style="width: 100%;" required>
							
							@foreach(\App\Models\Employee::getActiveEmployees() as $employee)
							<option value="{{ $employee->id }}"> {{ $employee->employee_name }} </option>
							@endforeach

						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tipe Periode {!! $wajibDiisi !!} </label>
						<select class="form-control" name="period_type" required>
							
							@foreach(\App\Models\EmployeeLeaveQuota::availablePeriodTypes() as $value => $label)
							<option value="{{ $value }}"> {{ $label }} </option>
							@endforeach

						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Jatah Cuti Per Periode {!! $wajibDiisi !!} </label>
						<input type="number" name="quota" class="form-control" value="1" placeholder="Jatah Cuti Per Periode" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Sisa Jatah Cuti {!! $wajibDiisi !!} </label>
						<input type="number" name="quota_available" class="form-control" value="0"  placeholder="Sisa Jatah Cuti" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Potongan Cuti Bersama </label>
						<input type="number" name="mass_leave_cut" class="form-control" value="0" placeholder="Potongan Cuti Bersama">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Jatah Terakumulasi {!! $wajibDiisi !!} </label>
						<small class="d-block text-muted mb-2">
							* Sisa jatah cuti akan di akumulasikan ke periode kerja berikut nya jika pilih Ya
						</small>
						<select class="form-control" name="is_allow_accumulation" required>
							
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
<script type="text/javascript">
	$(function(){

		let $form = $('#mainForm')
		let $submitBtn = $form.find('[type="submit"]').ladda()

		$('[name="id_employee"]').select2({
			placeholder : '- Pilih Karyawan -'
		})

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();

			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('employee_leave_quota.update', $leaveQuota->id) }}`,
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
				ajaxErrorHandling(error, $form)
			})
		});

		$form.find('[name="id_employee"]').val(`{{ $leaveQuota->id_employee }}`).trigger('change')
		$form.find('[name="period_type"]').val(`{{ $leaveQuota->period_type }}`).trigger('change')
		$form.find('[name="quota"]').val(`{{ $leaveQuota->quota }}`).trigger('change')
		$form.find('[name="quota_available"]').val(`{{ $leaveQuota->quota_available }}`).trigger('change')
		$form.find('[name="mass_leave_cut"]').val(`{{ $leaveQuota->mass_leave_cut }}`).trigger('change')
		$form.find('[name="is_allow_accumulation"]').val(`{{ $leaveQuota->is_allow_accumulation }}`)
	});
</script>
@endsection