@extends('template.backLayout')


@section('content')

@if($leaveSubmission->isStatusWaiting())
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

						@if(!auth()->user()->isEmployee())
						<tr>
							<td> Nama Karyawan </td>
							<td> 
								<a href="{{ route('employee.detail', $leaveSubmission->id_employee) }}">
									{{ $leaveSubmission->employeeName() }}
								</a>
							</td>
						</tr>
						@endif

						<tr>
							<td> Dibuat Pada </td>
							<td> {{ $leaveSubmission->createdAtText() }} </td>
						</tr>

						<tr>
							<td> Alasan </td>
							<td> {{ $leaveSubmission->leaveReasonText() }} </td>
						</tr>

						<tr>
							<td> Tanggal </td>
							<td> {{ $leaveSubmission->intervalDateText() }} </td>
						</tr>

						<tr>
							<td> Status Pengajuan </td>
							<td> {!! $leaveSubmission->statusHtml() !!} </td>
						</tr>

						<tr>
							<td> Durasi Cuti </td>
							<td> {{ $leaveSubmission->duration() }} hari </td>
						</tr>

						<tr>
							<td> Deskripsi </td>
							<td> {{ $leaveSubmission->descriptionText() }} </td>
						</tr>

						<tr>
							<td> Lampiran File </td>
							<td>
								<a href="{{ $leaveSubmission->fileLink() }}" target="_blank">
									Klik Disini
								</a>
							</td>
						</tr>

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
							@foreach($leaveSubmission->leaveSubmissionApprovals as $approval)
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
