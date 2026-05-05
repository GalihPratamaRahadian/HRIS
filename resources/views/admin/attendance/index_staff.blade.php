@extends('template.backLayout')


@section('style')
<style type="text/css">
	.attendance-item {
		position: relative;
	}

	.attendance-item > .attendance-type {
		position: absolute;
		top: 20px;
		right: 20px;
	}
</style>
@endsection


@section('content')
<div class="row">
	<div class="col-md-6">

		@foreach($attendances as $attendance)
		<div class="card support-pane-card grid-margin">
			<div class="card-body attendance-item">

				{!! $attendance->typeBadgeHtml() !!}

				<div class="row">

					<div class="col-lg-12 grid-margin">
						<label class="d-block"><b> Hari, Tanggal </b></label>
						<span> {{ $attendance->dateText() }} </span>
					</div>

					@if($attendance->isTypeHadir())
					<div class="col-lg-12 grid-margin">
						<label class="d-block"><b> Jam Kerja </b></label>
						<span> {{ $attendance->clockInTextFull() }} -  {{ $attendance->clockOutTextFull() }}</span>
					</div>
					@endif

					@if($attendance->isTypeLibur() && $attendance->isTypeCuti())
					<div class="col-lg-12 grid-margin">
						<label class="d-block"><b> Keterangan </b></label>
						<span> {{ $attendance->description }} </span>
					</div>
					@endif

					@if($attendance->isLate())
					<div class="col-lg-12 grid-margin">
						<label class="d-block"><b> Terlambat </b></label>
						<span class="text-danger"> {{ $attendance->lateText() }} </span>
					</div>

					<div class="col-lg-12 grid-margin">
						<label class="d-block"><b> Denda </b></label>
						<span class="text-danger"> {{ $attendance->cutNominalText() }} </span>
					</div>
					@endif

					<div class="col-lg-12">
						<a class="btn btn-primary btn-block" href="{{ route('attendance.detail', $attendance->id) }}">
							<i class="mdi mdi-magnify"></i> Lihat Detail
						</a>
					</div>
				
				</div>
			</div>
		</div>
		@endforeach

		<nav>
			<ul class="pagination d-flex justify-content-center">
				@if($activePage != 1)
				<li class="page-item">
					<a class="page-link" href="{{ route('attendance', [ 'page' => $activePage - 1 ]) }}">
						<i class="mdi mdi-chevron-left"></i>
					</a>
				</li>
				@if($startPage != 1)
				<li class="page-item">
					<a class="page-link" href="{{ route('attendance', [ 'page' => 1 ]) }}"> 1 </a>
				</li>
				@endif
				@if($activePage > 3)
				<li class="page-item">
					<a class="page-link" href="javascript:void(0);"> .. </a>
				</li>
				@endif
				@endif

				@for($i = $startPage; $i <= $endPage; $i++)
				<li class="page-item @if($i == $activePage) active @endif">
					<a class="page-link" href="{{ route('attendance', [ 'page' => $i ]) }}"> {{ $i }} </a>
				</li>
				@endfor

				@if($activePage != $amountPage)
				@if($activePage < ($amountPage - 2))
				<li class="page-item">
					<a class="page-link" href="javascript:void(0);"> .. </a>
				</li>
				@endif
				@if($endPage != $amountPage)
				<li class="page-item">
					<a class="page-link" href="{{ route('attendance', [ 'page' => $amountPage ]) }}"> {{ $amountPage }} </a>
				</li>
				@endif
				<li class="page-item">
					<a class="page-link" href="{{ route('attendance', [ 'page' => $activePage + 1 ]) }}">
						<i class="mdi mdi-chevron-right"></i>
					</a>
				</li>
				@endif
			</ul>
		</nav>

	</div>
</div>
@endsection