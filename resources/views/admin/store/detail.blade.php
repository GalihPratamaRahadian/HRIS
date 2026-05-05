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
							<td> Nama Toko </td>
							<td> {{ $store->store_name }} </td>
						</tr>

						<tr>
							<td> Nomor Telepon </td>
							<td> {{ $store->phone_number }} </td>
						</tr>

						<tr>
							<td> Alamat </td>
							<td> {{ $store->address }} </td>
						</tr>

						<tr>
							<td> Ditangani Oleh </td>
							<td>
								<a href="{{ route('employee.detail', $store->handled_by) }}">
									{{ $store->handledByName() }}
								</a>
							</td>
						</tr>

						<tr>
							<td> Didaftarkan Oleh </td>
							<td>
								<a href="{{ route('employee.detail', $store->registered_by) }}">
									{{ $store->registeredByName() }}
								</a>
							</td>
						</tr>

					</table>
				</div>

			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="card grid-margin">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> Lokasi </h4>
				</div>

				{!! $store->getLocation()->embeddedMap() !!}
			</div>
		</div>

	</div>

</div>
@endsection