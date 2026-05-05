@extends('template.backLayout')


@section('style')
<link rel="stylesheet" type="text/css" href="{{ url('css/step-wizard.css') }}">
@endsection


@section('content')
@include('payroll.step_wizard')

<div class="row">
	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> {{ $subtitle }} </h4>
				</div>

				<form id="mainForm">

					<div class="form-group">
						<label> Total Karyawan </label> <br>
						<label><strong> {{ $total_employee }} </strong></label>
					</div>

					<div class="form-group">
						<label> Total Nominal </label> <br>
						<label><strong> Rp {{ number_format($total_salary) }} </strong></label>
					</div>

					<div class="row">
						<div class="col-lg-4">
							<div class="form-group">
								<label> Kirim Slip Gaji Via Whatsapp </label>
								<p class="text-muted small">
									* Broadcast Akan Dilakukan 2 Menit Setelah Di Simpan
								</p>
								<select class="form-control" name="broadcast">
									<option value="yes"> Ya </option>
									<option value="no"> Tidak </option>
								</select>
							</div>					
						</div>
					</div>
								
					<hr>

					<div class="form-group">
						<button class="btn btn-success" type="submit">
							<i class="mdi mdi-check"></i> Setujui
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

		let form = $('#mainForm')
		let submitBtn = form.find('[type="submit"]').ladda();


		form.on('submit', function(e){
			e.preventDefault();

			let formData = $(this).serialize();

			submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{!! route('payroll.xhr_approve', [ 'period_start' => $_GET['period_start'], 'period_end' => $_GET['period_end'], 'temp' => $_GET['temp'] ]) !!}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				// console.log(response)
				// init();

				let { message, code, route } = response

				if(code == 200) {
					window.location.href = route
				}
			})
			.fail(error => {
				submitBtn.ladda('stop')

				let { status, responseJSON } = error
				let { message, errors } = responseJSON

				if(status == 422) {
					invalidResponse(form, errors)
				}

				toastrAlert();
				toastr.warning(message, 'Peringatan');
			})
		});

	});
</script>
@endsection