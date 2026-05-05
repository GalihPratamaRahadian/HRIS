@extends('template.backLayout')



@section('content')
<div class="row">
	<div class="col-md-6">

		@foreach($payrolls as $payroll)
		<div class="card support-pane-card grid-margin">
			<div class="card-body">

				<div class="row">

					<div class="col-lg-12 grid-margin">
						<label class="d-block"><b> Periode </b></label>
						<span> {{ $payroll->periodText() }} </span>
					</div>

					<div class="col-lg-12 grid-margin">
						<label class="d-block"><b> Total </b></label>
						<span> {{ $payroll->totalText() }}</span>
					</div>

					@if($payroll->isHasBasicSalaryCut())
					<div class="col-lg-12 grid-margin">
						<label class="d-block"><b> Denda </b></label>
						<span class="text-danger"> - {{ $payroll->basicSalaryCutText() }}</span>
					</div>
					@endif

					<div class="col-lg-12 grid-margin">
						<label class="d-block"><b> Total </b></label>
						<span> {{ $payroll->totalText() }}</span>
					</div>

					<div class="col-lg-12">
						<a class="btn btn-primary btn-block" href="{{ route('payroll.detail', $payroll->id) }}">
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
					<a class="page-link" href="{{ route('payroll', [ 'page' => $activePage - 1 ]) }}">
						<i class="mdi mdi-chevron-left"></i>
					</a>
				</li>
				@if($startPage != 1)
				<li class="page-item">
					<a class="page-link" href="{{ route('payroll', [ 'page' => 1 ]) }}"> 1 </a>
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
					<a class="page-link" href="{{ route('payroll', [ 'page' => $i ]) }}"> {{ $i }} </a>
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
					<a class="page-link" href="{{ route('payroll', [ 'page' => $amountPage ]) }}"> {{ $amountPage }} </a>
				</li>
				@endif
				<li class="page-item">
					<a class="page-link" href="{{ route('payroll', [ 'page' => $activePage + 1 ]) }}">
						<i class="mdi mdi-chevron-right"></i>
					</a>
				</li>
				@endif
			</ul>
		</nav>

	</div>
</div>
@endsection