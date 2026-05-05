@extends('template.backLayout')


@section('content')
<div class="row grid-margin">
	<div class="col-lg-12">
		<a href="{{ route('submission.leave.create') }}" class="btn btn-primary">
			<i class="mdi mdi-file-plus"></i> Buat Pengajuan
		</a>
	</div>
</div>

<div class="row">
	<div class="col-md-10">

		@if(count($submissions) == 0)
		<p align="center">
			Tidak Ada Pengajuan
		</p>
		@else
		<div class="row">
			@foreach($submissions as $submission)
			<div class="col-lg-6">
				<div class="card support-pane-card grid-margin">
					<div class="card-body">

						<div class="row">

							<div class="col-lg-12 grid-margin">
								<label class="d-block"><b> Dibuat Pada </b></label>
								<span> {{ $submission->createdAtText() }} </span>
							</div>

							<div class="col-lg-12 grid-margin">
								<label class="d-block"><b> Tanggal {{ $submission->leaveReasonText() }} </b></label>
								<span> {{ $submission->intervalDateText() }} </span>
							</div>

							<div class="col-lg-12 grid-margin">
								<label class="d-block"><b> Status </b></label>
								<span> {!! $submission->statusHtml() !!} </span>
							</div>

							<div class="col-lg-12 grid-margin">
								<label class="d-block"><b> Deskripsi </b></label>
								<span> {!! $submission->subDescriptionText() !!} </span>
							</div>

							<div class="col-lg-12">
								<a class="btn btn-primary btn-block" href="{{ route('submission.leave.detail', $submission->id) }}">
									<i class="mdi mdi-magnify"></i> Lihat Detail
								</a>
							</div>
						
						</div>
					</div>
				</div>
			</div>
			@endforeach
		</div>

		<nav>
			<ul class="pagination d-flex justify-content-center">
				@if($activePage != 1)
				<li class="page-item">
					<a class="page-link" href="{{ route('submission.leave', [ 'page' => $activePage - 1 ]) }}">
						<i class="mdi mdi-chevron-left"></i>
					</a>
				</li>
				@if($startPage != 1)
				<li class="page-item">
					<a class="page-link" href="{{ route('submission.leave', [ 'page' => 1 ]) }}"> 1 </a>
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
					<a class="page-link" href="{{ route('submission.leave', [ 'page' => $i ]) }}"> {{ $i }} </a>
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
					<a class="page-link" href="{{ route('submission.leave', [ 'page' => $amountPage ]) }}"> {{ $amountPage }} </a>
				</li>
				@endif
				<li class="page-item">
					<a class="page-link" href="{{ route('submission.leave', [ 'page' => $activePage + 1 ]) }}">
						<i class="mdi mdi-chevron-right"></i>
					</a>
				</li>
				@endif
			</ul>
		</nav>
		@endif


	</div>
</div>
@endsection