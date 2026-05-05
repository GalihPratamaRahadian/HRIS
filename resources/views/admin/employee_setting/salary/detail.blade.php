@extends('template.backLayout')


@section('content')
<div class="row">

	<div class="col-md-6">
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<div class="table-responsive">
					<table class="table table-hover">
						<tr>
							<td> Nama Karyawan </td>
							<td> 
								<a href="{{ route('employee.detail', $employeeSalary->id_employee) }}">
									{{ $employeeSalary->employeeName() }}
								</a>
							</td>
						</tr>
						<tr>
							<td> Gaji Pokok </td>
							<td> {{ $employeeSalary->basicSalaryText() }} </td>
						</tr>
						<tr>
							<td> Upah Lembur Per Jam </td>
							<td> {{ $employeeSalary->overtimePayText() }} </td>
						</tr>
						<tr>
							<td> Tunjangan Makan Per Hari </td>
							<td> {{ $employeeSalary->dailyMealAllowanceText() }} </td>
						</tr>
						<tr>
							<td> Tunjangan Transportasi Per Hari </td>
							<td> {{ $employeeSalary->dailyTransportationAllowanceText() }} </td>
						</tr>
						<tr>
							<td> Total Tunjangan Tetap </td>
							<td> {{ $employeeSalary->totalAllowanceText() }} </td>
						</tr>
						<tr>
							<td> Total Potongan Tetap </td>
							<td> {{ $employeeSalary->totalCutText() }} </td>
						</tr>
					</table>
				</div>

			</div>
		</div>
	</div>

	<div class="col-md-6">

		@if($employeeSalary->isHasAllowances())
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				{!! Template::titleBanner('Tunjangan Tetap') !!}

				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th> Nama Tunjangan </th>
								<th> Nominal </th>
							</tr>
						</thead>
						<tbody>

							@foreach($employeeSalary->employeeSalaryAllowances as $allowance)
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

		@if($employeeSalary->isHasCuts())
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				{!! Template::titleBanner('Potongan') !!}

				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th> Nama Potongan </th>
								<th> Nominal </th>
							</tr>
						</thead>
						<tbody>

							@foreach($employeeSalary->employeeSalaryCuts as $cut)
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