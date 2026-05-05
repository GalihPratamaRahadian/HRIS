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
							<td> Sales </td>
							<td>
								<a href="{{ route('employee.detail', $storeVisit->id_employee) }}">
									{{ $storeVisit->employeeName() }}
								</a>
							</td>
						</tr>

						<tr>
							<td> Toko </td>
							<td>
								<a href="{{ route('store.detail', $storeVisit->id_store) }}">
									{{ $storeVisit->storeName() }}
								</a>
								@if($storeVisit->store)
								<br>
								{{ $storeVisit->store->address }}
								@endif
							</td>
						</tr>

						<tr>
							<td> Tanggal Kunjungan </td>
							<td> {{ $storeVisit->visitedAtText() }} </td>
						</tr>

						<tr>
							<td> Apakah Membeli </td>
							<td> {{ $storeVisit->purchaseText() }} </td>
						</tr>

					</table>
				</div>

			</div>
		</div>

		<div class="card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Lokasi Check In </h4>
				</div>

				{!! $storeVisit->getLocation()->embeddedMap() !!}
			</div>
		</div>

	</div>

	<div class="col-md-6">
		<div class="card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Foto Check In </h4>
				</div>

				@if($storeVisit->isHasPhoto())
				<img src="{{ $storeVisit->photoLink() }}" class="img-fluid">
				@endif

			</div>
		</div>
	</div>

</div>
@endsection