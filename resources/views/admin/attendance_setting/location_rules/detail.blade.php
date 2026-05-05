@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<div class="table-responsive">
					<table class="table table-hover">
						<tr>
							<th> Nama Lokasi </th>
							<td width="10"> : </td>
							<td> {{ $locationRules->location_name }} </td>
						</tr>
						<tr>
							<th> Lokasi </th>
							<td width="10"> : </td>
							<td>
								<a href="{{ $locationRules->gmapsLink() }}" target="_blank">
									{{ $locationRules->coordinatePoint() }}
								</a>
							</td>
						</tr>
						<tr>
							<th> Radius </th>
							<td width="10"> : </td>
							<td> {{ $locationRules->radiusDistanceText() }} </td>
						</tr>
					</table>
				</div>

				<div class="mt-2">
					{!! $locationRules->makeLocation()->embeddedMap(null, '300px') !!}
				</div>

			</div>
		</div>
	</div>
</div>
@endsection