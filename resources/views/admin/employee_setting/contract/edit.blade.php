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
							
							@foreach($employees as $employee)
							<option value="{{ $employee->id }}"> {{ $employee->employee_name }} </option>
							@endforeach

						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Awal Kontrak {!! $wajibDiisi !!} </label>
						<input type="date" name="start_date" class="form-control" placeholder="Awal Kontrak" value="{{ $employeeContract->start_date }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Akhir Kontrak {!! $wajibDiisi !!} </label>
						<input type="date" name="end_date" class="form-control" placeholder="Akhir Kontrak" value="{{ $employeeContract->end_date }}" required>
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

		$form.find('[name="id_employee"]').select2({
			placeholder : '- Pilih Karyawan -'
		})

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();
			$submitBtn.ladda('start')
			ajaxSetup();
			$.ajax({
				url : `{{ route('employee_contract.update', $employeeContract->id) }}`,
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

		$form.find('[name="id_employee"]').val('{{ $employeeContract->id_employee }}').trigger('change')
	});
</script>
@endsection