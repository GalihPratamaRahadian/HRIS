@extends('template.backLayout')


@section('content')

@if($sickNecessitySubmission->isStatusWaiting())
<div class="row grid-margin">
	<div class="col-lg-12">
		{!! Template::infoBanner('Menunggu penyetujuan dari semua penyetuju atau oleh admin.') !!}
	</div>
</div>
@endif

<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				
				{!! Setting::titleBanner($title) !!}

				<div class="table-responsive">
					<table class="table table-hover">

						<tr>
							<td> Dibuat Pada </td>
							<td> {{ $sickNecessitySubmission->createdAtText() }} </td>
						</tr>

						<tr>
							<td> Jenis </td>
							<td> {{ $sickNecessitySubmission->type }} </td>
						</tr>

						<tr>
							<td> Alasan </td>
							<td> {{ $sickNecessitySubmission->reason }} </td>
						</tr>

						<tr>
							<td> Tanggal </td>
							<td> {{ $sickNecessitySubmission->intervalDateText() }} </td>
						</tr>

						<tr>
							<td> Status Pengajuan </td>
							<td> {!! $sickNecessitySubmission->statusHtml() !!} </td>
						</tr>

						<tr>
							<td> Durasi </td>
							<td> {{ $sickNecessitySubmission->duration() }} hari </td>
						</tr>

						<tr>
							<td> Deskripsi </td>
							<td> {{ $sickNecessitySubmission->descriptionText() }} </td>
						</tr>

						@if($sickNecessitySubmission->isHasFile())
						<tr>
							<td> Lampiran File </td>
							<td>
								<a href="{{ $sickNecessitySubmission->fileLink() }}" target="_blank">
									Klik Disini
								</a>
							</td>
						</tr>
						@endif

					</table>
				</div>

			</div>
		</div>
	</div>


	<div class="col-md-6">
		
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				
				{!! Setting::titleBanner('Penyetujuan') !!}

				<div class="table-responsive">
					<table class="table table-hover table-bordered">
						<thead>
							<tr>
								<th> Oleh </th>
								<th> Hasil </th>
							</tr>
						</thead>

						<tbody>
							@foreach($sickNecessitySubmission->sickNecessitySubmissionApprovals as $approval)
							<tr>
								@if($approval->isStatusWaiting())
								<td> {{ $approval->approverPositionName() }} - {{ $approval->approverDepartmentName() }} </td>
								<td> {!! $approval->statusHtml() !!} </td>
								@else
								<td>
									@if($approval->user)
									{{ $approval->userName() }}

										@if($approval->user->isEmployee()) -
										{{ $approval->approverDepartmentName() }}
										@endif
									@else
									{{ $approval->approverPositionName() }} - {{ $approval->approverDepartmentName() }}
									@endif 
								</td>
								<td> {!! $approval->statusHtml() !!} </td>
								@endif
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>
@endsection
