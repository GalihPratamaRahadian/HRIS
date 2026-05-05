@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-12">
		<form id="attendanceForm">
			<div class="row">
				<div class="col-lg-6 grid-margin">
					<div class="card support-pane-card">
						<div class="card-body">
							<div class="support-pane">
								<div class="row">
									<div class="col text-center" style="font-size: 100px;">
										<i class="mdi mdi-alert-circle-outline text-warning"></i>
									</div>
								</div>
								<div class="row">
									<div class="col text-center">
										<span> Hai, <b> {{ auth()->user()->getName() }} </b></span><br>
										<span> {{ $message }} </span>
									</div>
								</div>
								<div class="row mt-3">
									<div class="col text-center">
										<a href="{{ route('dashboard') }}" class="btn btn-danger mb-2">
											<i class="mdi mdi-chevron-left"></i> Kembali ke Dashboard
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection
