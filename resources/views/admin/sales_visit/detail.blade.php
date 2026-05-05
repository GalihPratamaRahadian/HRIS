@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-6">
		<div class="card grid-margin">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}
				<table class="table">
					<tr>
						<td> Sales </td>
						<td>
							<a href="{{ route('employee.detail', $salesEmployee->id_employee) }}">
								{{ $salesEmployee->employeeName() }}
							</a>
						</td>
					</tr>
					<tr>
						<td> Tanggal Kunjungan  </td>
						<th> {{ $date->format('d M Y') }} </th>
					</tr>
				</table>
			</div>
		</div>
	</div>	

	<div class="col-md-6">

		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				{!! Template::titleBanner('Kunjungan Toko') !!}

				<div class="table-responsive">
					<table class="table table-hover">

						<thead>
							<tr>
								<th> Waktu </th>
								<th> Toko </th>
								<th> Apakah Membeli </th>
							</tr>
						</thead>

						<tbody>
							@forelse($storeVisits as $storeVisit)
							<tr>
								<td> {{ $storeVisit->visitedAtText('H:i') }} </td>
								<td> {{ $storeVisit->storeName() }} </td>
								<td> {{ $storeVisit->purchaseText() }} </td>
							</tr>
							@empty
							<tr>
								<td colspan="3" align="center"> Belum melakukan kunjungan </td>
							</tr>
							@endforelse
						</tbody>

					</table>
				</div>

			</div>
		</div>

	</div>

	<div class="col-md-6">
		<div class="card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Peta </h4>
				</div>

				<div id="map" style="width: 100%; height: 300px;"></div>

			</div>
		</div>
	</div>

</div>
@endsection

@section('script')
<script src="https://maps.googleapis.com/maps/api/js?key="></script>
<script type="text/javascript">
	$(function(){

		let map = null

		let property = {
			center:new google.maps.LatLng('-6.735407', '108.550032'),
			zoom: 11,
			mapTypeId:google.maps.MapTypeId.ROADMAP,
			animation: google.maps.Animation.BOUNCE
		};
		map = new google.maps.Map(document.querySelector('#map'), property);

		const addMarker = (lat, long, contentData = null) => {
			let marker = new google.maps.Marker({
				position: new google.maps.LatLng(lat, long),
				map: map
			});

			let infoWindow = null;
			if(!isEmpty(contentData)) {
				infoWindow = new google.maps.InfoWindow({
					content : contentData,
				})
			}

			google.maps.event.addListener(marker, 'click', function(){

				if(!isEmpty(contentData)) {
					infoWindow.open(map, marker);
				}
			})
		}

			// showMap('#map');

		@foreach($storeVisits as $storeVisit)
		addMarker(`{{ $storeVisit->latitude }}`, `{{ $storeVisit->longitude }}`, `<p align="center"><b class="font-weight-bold">{{ $storeVisit->storeName() }}</b></p> <img src="{{ $storeVisit->photoLink() }}" style="width: 100px; height: auto;" />`)
		@endforeach
	})
</script>
@endsection