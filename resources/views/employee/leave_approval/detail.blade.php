@extends('template.backLayout')


@section('content')
@if($approval->isStatusWaiting())
<div class="row grid-margin">
	<div class="col-lg-12">
		{!! Template::infoBanner('Penyetujuan untuk pengajuan cuti. Jika melakukan penolakan maka pengajuan cuti akan ditolak.') !!}
	</div>

	<div class="col-lg-12">
		<button class="btn btn-success" id="approve-btn">
			<i class="mdi mdi-check"></i> Setujui
		</button>
		<button class="btn btn-danger" id="reject-btn">
			<i class="mdi mdi-close"></i> Tolak
		</button>
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
							<td> Nama Karyawan </td>
							<td> 
								{{ $approval->employeeName() }}
							</td>
						</tr>

						<tr>
							<td> Dibuat Pada </td>
							<td> {{ $approval->createdAtText() }} </td>
						</tr>

						<tr>
							<td> Alasan </td>
							<td> {{ $approval->leaveReasonText() }} </td>
						</tr>

						<tr>
							<td> Tanggal Cuti </td>
							<td> {{ $approval->leaveSubmission->intervalDateText() }} </td>
						</tr>

						<tr>
							<td> Status Penyetujuan </td>
							<td> {!! $approval->statusHtml() !!} </td>
						</tr>

						<tr>
							<td> Deskripsi </td>
							<td> {{ $approval->leaveSubmission->descriptionText() }} </td>
						</tr>

						<tr>
							<td> Lampiran File </td>
							<td>
								<a href="{{ $approval->leaveSubmission->fileLink() }}" target="_blank">
									Klik Disini
								</a>
							</td>
						</tr>

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

		$('#approve-btn').on('click', function(){
			let btn = $(this).ladda();
			confirmation('Yakin ingin disetujui?', () => {
				btn.ladda('start')
				ajaxSetup()
				$.ajax({
					url: `{{ route('employee.leave_approval.approve', $approval->id) }}`,
					method: 'post',
					dataType: 'json'
				})
				.done(response => {
					setTimeout(() => {
						window.location.reload();
					}, 1000)
					ajaxSuccessHandling(response)
				})
				.fail(error => {
					btn.ladda('stop')
					ajaxErrorHandling(error)
				})
			})
		})


		$('#reject-btn').on('click', function(){
			let btn = $(this).ladda();
			confirmation('Yakin ingin ditolak?', () => {
				btn.ladda('start')
				ajaxSetup()
				$.ajax({
					url: `{{ route('employee.leave_approval.reject', $approval->id) }}`,
					method: 'post',
					dataType: 'json'
				})
				.done(response => {
					setTimeout(() => {
						window.location.reload();
					}, 1000)
					ajaxSuccessHandling(response)
				})
				.fail(error => {
					btn.ladda('stop')
					ajaxErrorHandling(error)
				})
			})
		})

	})

</script>
@endsection