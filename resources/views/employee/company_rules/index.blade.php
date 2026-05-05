@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> {{ $title }} </h4>
				</div>

				@if(setting('link_pkb'))
				<div style="min-width: 300px; min-height: 700px;">
					<iframe src="{{ setting('link_pkb') }}" style="width: 100%; height: 100vh;"></iframe>
				</div>
				@else
				<p align="center">
					Belum Upload Link Peraturan Perusahaan
				</p>
				@endif


			</div>
		</div>
	</div>
</div>
@endsection