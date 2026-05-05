@extends('template.backLayout')


@section('content')
<div class="row mb-3">
	<div class="col-lg-4 grid-margin">
		<div class="card">
			<div class="card-body align-items-center">
				
				{!! Setting::titleBanner('Status Pendaftaran') !!}

				<div class="form-group">
					<label> Status </label> <br>
					<label><b> {!! auth()->user()->registrant->statusHtml() !!} </b></label>
				</div>

				@if(auth()->user()->registrant->isStatusWaiting())
				<div class="form-group">
					<label> Terakhir diubah </label> <br>
					<label><b> {!! auth()->user()->registrant->editedAtText() !!} </b></label>
				</div>
				@endif

				@if(auth()->user()->registrant->isStatusRejected())
				<div class="form-group">
					<label> Ditolak pada </label> <br>
					<label><b> {!! auth()->user()->registrant->rejectedAtText() !!} </b></label>
				</div>
				@endif

				@if(!auth()->user()->registrant->isStatusApproved())
				<div class="form-group">
					<a class="btn btn-primary btn-block" href="{{ route('profile') }}">
						<i class="mdi mdi-account-edit"></i> Edit Profil Saya
					</a>
				</div>
				@endif

			</div>
		</div>
	</div>

	<div class="col-lg-8">
		<div class="card">
			<div class="card-body align-items-center">
				
				{!! Setting::titleBanner('Log') !!}

				<div style="max-height: 500px; overflow-y: auto;">
					
					<table class="table table-hover">
						
					@foreach(auth()->user()->registrant->logs as $log)

					<tr>
						<td>
							<p>
								{!! $log->statusBadgeHtml() !!} {{ $log->createdAtText() }}
							</p>
							@if(!empty($log->description))
							{{ $log->descriptionText() }}
							@endif
						</td>
					</tr>

					@endforeach

					</table>

				</div>

			</div>
		</div>
	</div>
</div>
@endsection


@section('script')
<script type="text/javascript">
	$(function(){

		$('#todayAttendanceDataTable').DataTable({
			order: [['1', 'desc']]
		});

	})
</script>
@endsection