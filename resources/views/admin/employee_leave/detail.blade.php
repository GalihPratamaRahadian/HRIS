@extends('template.backLayout')


@section('content')
<div class="row">

	<div class="col-md-6">
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				
				{!! Setting::titleBanner($title) !!}

				<table class="table table-hover">
					<tr>
						<td> Nama Karyawan </td>
						<td> 
							<a href="{{ route('employee.detail', $employeeLeave->id_employee) }}">
								{{ $employeeLeave->employeeName() }}
							</a>
						</td>
					</tr>
					<tr>
						<td> Alasan </td>
						<td> {{ $employeeLeave->reason }} </td>
					</tr>
					<tr>
						<td> Waktu </td>
						<td> {{ $employeeLeave->intervalDateText() }} </td>
					</tr>
					<tr>
						<td> Keterangan </td>
						<td> {{ $employeeLeave->descriptionText() }} </td>
					</tr>
					@if($employeeLeave->isHasFile())
					<tr>
						<td> Lampiran </td>
						<td>
							<a href="{{ $employeeLeave->fileLink() }}" target="_blank"> Klik Disini </a>
						</td>
					</tr>
					@endif
				</table>

			</div>
		</div>
	</div>

</div>
@endsection