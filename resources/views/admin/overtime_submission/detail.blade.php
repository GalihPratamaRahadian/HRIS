@extends('template.backLayout')


@section('content')

@if(UserPermission::check('overtime_submission', 'a'))
@if($overtimeSubmission->isStatusWaiting())
<div class="row grid-margin">
	<div class="col-lg-12">
		{!! Template::infoBanner('Jika melakukan penyetujuan/penolakan maka akan melewati semua penyetujuan/penolakan dari staff penyetuju terkait.') !!}
	</div>

	<div class="col-lg-12">
		<button class="btn btn-success" id="approve-btn">
			<i class="mdi mdi-check"></i> Setujui
		</button>
		<button class="btn btn-danger" id="reject-btn">
			<i class="mdi mdi-close"></i> Tolak
		</button>
        <button class="btn btn-primary" id="broadcast-btn">
            <i class="mdi mdi-send"></i> Broadcast Ulang
        </button>
	</div>
</div>
@elseif($overtimeSubmission->isStatusApproved())
<div class="row grid-margin">
	<div class="col-lg-12">
		{!! Template::infoBanner('Lembur yang dibatalkan tidak akan dihitung ketika payroll.') !!}
	</div>

	<div class="col-lg-12">
		<button class="btn btn-danger" id="cancel-btn">
			<i class="mdi mdi-close"></i> Batalkan Penyetujuan
		</button>
	</div>
</div>
@endif
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
							<td> Status </td>
							<td> {!! $overtimeSubmission->statusHtml() !!} </td>
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
							@foreach($overtimeSubmission->overtimeSubmissionApprovals as $approval)
							<tr>
								<td>
									@if($approval->user)
										{{ $approval->userName() }}
									@else
										{{ $approval->approverPositionName() }}
									@endif
								</td>
								<td> {!! $approval->statusHtml() !!} </td>
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


@section('script')
<script type="text/javascript">

	$(function(){

		renderLibEvent()

		$('#approve-btn').on('click', function(){
			$(this).ladda();

			confirmation('Yakin ingin disetujui?', () => {
				$(this).ladda('start');
				ajaxSetup()
				$.ajax({
					url: `{{ route('admin.overtime_submission.approve', $overtimeSubmission->id) }}`,
					method: 'post',
					dataType: 'json',
				})
				.done(response => {
					ajaxSuccessHandling(response)
					setTimeout(() => {
						window.location.reload();
					}, 1000)
				})
				.fail(error => {
					$(this).ladda('stop');
					ajaxErrorHandling(error)
				})
			})
		})


		$('#reject-btn').on('click', function(){
			$(this).ladda();

			confirmation('Yakin ingin ditolak?', () => {
				$(this).ladda('start');
				ajaxSetup()
				$.ajax({
					url: `{{ route('admin.overtime_submission.reject', $overtimeSubmission->id) }}`,
					method: 'post',
					dataType: 'json',
				})
				.done(response => {
					ajaxSuccessHandling(response)
					setTimeout(() => {
						window.location.reload();
					}, 1000)
				})
				.fail(error => {
					$(this).ladda('stop');
					ajaxErrorHandling(error)
				})
			})
		})


		$('#cancel-btn').on('click', function(){
			$(this).ladda();

			confirmation('Yakin ingin membatalkan penyetujuan?', () => {
				$(this).ladda('start');
				ajaxSetup()
				$.ajax({
					url: `{{ route('admin.overtime_submission.cancel', $overtimeSubmission->id) }}`,
					method: 'post',
					dataType: 'json',
				})
				.done(response => {
					ajaxSuccessHandling(response)
					setTimeout(() => {
						window.location.reload();
					}, 1000)
				})
				.fail(error => {
					$(this).ladda('stop');
					ajaxErrorHandling(error)
				})
			})
		})

        $('#broadcast-btn').on('click', function(){
            $(this).ladda();

            confirmation('Yakin ingin broadcast ulang?', () => {
                $(this).ladda('start');
                ajaxSetup()
                $.ajax({
                    url: `{{ route('admin.overtime_submission.resend_broadcast', $approval->id) }}`,
                    method: 'post',
                    dataType: 'json',
                })
                .done(response => {
                    ajaxSuccessHandling(response)
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000)
                })
                .fail(error => {
                    $(this).ladda('stop');
                    ajaxErrorHandling(error)
                })
            })
        })

	})

</script>
@endsection
