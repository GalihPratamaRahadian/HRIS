@extends('template.backLayout')


@section('content')
<div class="row">

	<div class="col-md-6">

		<div class="card support-pane-card grid-margin show-on-lg-and-up">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<div class="table-responsive">
					<table class="table table-hover">

						<tr>
							<td> Nama Shift </td>
							<th> {{ $shift->shift_name }} </th>
						</tr>

						<tr>
							<td> Batas Awal Kehadiran </td>
							<th> {{ date('H:i', strtotime($shift->clock_start_limit)) }} </th>
						</tr>

						<tr>
							<td> Jam Mulai </td>
							<th> {{ date('H:i', strtotime($shift->clock_start)) }} </th>
						</tr>

						<tr>
							<td> Jam Selesai </td>
							<th> {{ date('H:i', strtotime($shift->clock_end)) }} </th>
						</tr>

						<tr>
							<td> Toleransi Keterlambatan </td>
							<th> {{ $shift->late_tolerance }} menit </th>
						</tr>

						<tr>
							<td> Hari Libur </td>
							<th> {{ $shift->offdayShiftText() }} </th>
						</tr>

					</table>
				</div>

			</div>
		</div>
	</div>

	@if($shift->isHasShiftDetails())
	<div class="col-md-6">

		<div class="card support-pane-card grid-margin show-on-lg-and-up">
			<div class="card-body">
				{!! Template::titleBanner('Jam Kerja Pengecualian') !!}

				<div class="table-responsive">
					<table class="table table-hover table-bordered">

						<thead>
							<tr>
								<th> Hari </th>
								<th> Jam Masuk </th>
								<th> Jam Keluar  </th>
							</tr>
						</thead>

						<tbody>
							@foreach($shift->shiftDetails as $detail)
							<tr>
								<td> {{ $detail->dayName() }} </td>
								<td> {{ $detail->clockStartText('H:i') }} </td>
								<td> {{ $detail->clockEndText('H:i') }} </td>
							</tr>
							@endforeach
						</tbody>

					</table>
				</div>

			</div>
		</div>
	</div>
	@endif
</div>
@endsection