@extends('template.backLayout')


@section('content')

@if($approval->isStatusWaiting())
<div class="row grid-margin">
	<div class="col-lg-12">
		{!! Template::infoBanner('Jika melakukan penolakan maka otomatis lembur akan langsung ditolak.') !!}
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
								<a href="{{ route('employee.detail', $overtimeSubmission->id_employee) }}">
									{{ $overtimeSubmission->employeeName() }}
								</a>
							</td>
						</tr>

						<tr>
							<td> Dibuat Pada </td>
							<td> {{ $overtimeSubmission->createdAtText() }} </td>
						</tr>

						<tr>
							<td> Alasan </td>
							<td> {{ $overtimeSubmission->overtimeReasonText() }} </td>
						</tr>

						<tr>
							<td> Mulai Lembur </td>
							<td> {{ $overtimeSubmission->startDateText().' '.date('H:i', strtotime($overtimeSubmission->clock_start)) }} </td>
						</tr>

						<tr>
							<td> Selesai Lembur </td>
							<td> {{ $overtimeSubmission->endDateText().' '.date('H:i', strtotime($overtimeSubmission->clock_end)) }} </td>
						</tr>

						<tr>
							<td> Deskripsi </td>
							<td> {!! $overtimeSubmission->descriptionHtml() !!} </td>
						</tr>

						<tr>
							<td> Status Lembur </td>
							<td> {!! $overtimeSubmission->statusHtml() !!} </td>
						</tr>

						<tr>
							<td> Status Penyetujuan Anda </td>
							<td> {!! $approval->statusHtml() !!} </td>
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
					url: `{{ route('employee.overtime_approval.approve', $approval->id) }}`,
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
					url: `{{ route('employee.overtime_approval.reject', $approval->id) }}`,
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