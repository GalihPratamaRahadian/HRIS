@extends('template.backLayout')


@section('content')
<div class="row grid-margin">
	<div class="col-lg-12">
		<a href="{{ route('payroll.slip', $payroll->id) }}" class="btn btn-danger" target="_blank">
			<i class="mdi mdi-file"></i> Slip Gaji
		</a>
		<button class="btn btn-primary" id="send-btn">
			<i class="mdi mdi-send"></i> Kirim Ulang
		</button>
	</div>
</div>

<div class="row">

	<div class="col-md-6">
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> {{ $title }} </h4>
				</div>

				<div class="table-responsive">
					<table class="table table-hover">

						<tr>
							<td> Nama Karyawan </td>
							<td> 
								{{ $payroll->employeeName() }}
							</td>
						</tr>

						<tr>
							<td> Periode </td>
							<td> 
								{{ $payroll->periodText() }}
							</td>
						</tr>

						<tr>
							<td> Gaji Pokok </td>
							<td> 
								{{ $payroll->basicSalaryText() }}
							</td>
						</tr>

						<tr>
							<td> Total Tunjangan </td>
							<td> 
								{{ $payroll->totalAllowanceText() }}
							</td>
						</tr>

						<tr>
							<td> Total Potongan </td>
							<td> 
								<span class="text-danger"> {{ $payroll->totalCutText() }} </span>
							</td>
						</tr>

						<tr>
							<td> Bonus </td>
							<td> 
								{{ $payroll->bonusText() }}
							</td>
						</tr>

						<tr>
							<td> Total Gaji </td>
							<td> 
								{{ $payroll->totalText() }}
							</td>
						</tr>

						<tr>
							<td> Catatan </td>
							<td> 
								{!! $payroll->noteHtml() !!}
							</td>
						</tr>

						<tr>
							<td class="column1"> Status Pengiriman </td>
							<td> {!! $payroll->sendStatusHtml() !!} </td>
						</tr>

						@if($payroll->isWaitingToSend())
						<tr>
							<td class="column1"> Jadwal Pengiriman </td>
							<td> {{ $payroll->sendScheduleFormatted('d M Y H:i') }} </td>
						</tr>
						@endif

					</table>
				</div>

			</div>
		</div>
	</div>


	<div class="col-md-6">

		@if($payroll->isHasAllowance())
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Tunjangan </h4>
				</div>

				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th> Nama Tunjangan </th>
								<th> Nominal Tunjangan </th>
							</tr>
						</thead>
						<tbody>

							@foreach($payroll->payrollAllowances as $allowance)
							<tr>
								<td> {{ $allowance->allowance_name }} </td>
								<td> {{ $allowance->allowanceNominalText() }} </td>
							</tr>
							@endforeach

						</tbody>
					</table>
				</div>

			</div>
		</div>
		@endif

		@if($payroll->isHasCut())
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Potongan </h4>
				</div>

				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th> Nama Potongan </th>
								<th> Nominal Potongan </th>
							</tr>
						</thead>
						<tbody>

							@foreach($payroll->payrollCuts as $cut)
							<tr>
								<td> {{ $cut->cut_name }} </td>
								<td> {{ $cut->cutNominalText() }} </td>
							</tr>
							@endforeach

						</tbody>
					</table>
				</div>

			</div>
		</div>
		@endif

	</div>

</div>
@endsection


@section('script')
<script type="text/javascript">
	
	$(function(){

		$('#send-btn').on('click', function(){
			$(this).ladda()

			confirmation('Yakin ingin kirim ulang?', () => {
				$(this).ladda('start')

				ajaxSetup()
				$.ajax({
					url: `{{ route('payroll.send', $payroll->id) }}`,
					method: 'post',
				})
				.done(response => {
					// $(this).ladda('stop')
					ajaxSuccessHandling(response)
					setTimeout(() => {
						window.location.reload()
					}, 1000)
				})
				.fail(error => {
					$(this).ladda('stop')
					ajaxErrorHandling(error)
				})
			})			
		})

	})

</script>
@endsection