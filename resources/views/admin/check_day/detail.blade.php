@extends('template.backLayout')


@section('content')
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
								@if(!auth()->user()->isEmployee())
								<a href="{{ route('employee.detail', $checkDay->id_employee) }}">
								@endif
									{{ $checkDay->employeeName() }}
								@if(!auth()->user()->isEmployee())
								</a>
								@endif
							</td>
						</tr>

						<tr>
							<td> Tanggal Check Day </td>
							<td> {{ $checkDay->dateText() }} </td>
						</tr>

						<tr>
							<td> Jam Check Day </td>
							<td> {{ $checkDay->checkDayAtText('H:i:s') }} WIB </td>
						</tr>

					</table>
				</div>

			</div>
		</div>

		<div class="card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Lokasi Check Day </h4>
				</div>

				{!! location($checkDay->latitude, $checkDay->longitude)->embeddedMap() !!}
			</div>
		</div>

	</div>

	<div class="col-md-6">
		<div class="card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Foto Check Day </h4>
				</div>

				<img src="{{ $checkDay->photoLink() }}" class="img-fluid">
			</div>
		</div>
	</div>

</div>
@endsection