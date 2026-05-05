@extends('template.backLayout')


@section('content')

@if(UserPermission::check('attendance_permission_submission', 'a'))
@if($attendancePermissionSubmission->isStatusWaiting())
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
@elseif($attendancePermissionSubmission->isStatusApproved())
<div class="row grid-margin">
	<div class="col-lg-12">
		{!! Template::infoBanner('Pembatalan penyetujuan cuti akan menghapus data cuti karyawan tersebut.') !!}
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
							<td> Diajukan Pada </td>
							<td> {{ $attendancePermissionSubmission->createdAtText() }} </td>
						</tr>

						@if(!auth()->user()->isEmployee())
						<tr>
							<td> Nama Karyawan </td>
							<td>
								<a href="{{ route('employee.detail', $attendancePermissionSubmission->id_employee) }}">
									{{ $attendancePermissionSubmission->employeeName() }}
								</a>
							</td>
						</tr>
						@endif

						<tr>
							<td> Tanggal </td>
							<td> {{ $attendancePermissionSubmission->dateTimeText('d M Y') }} </td>
						</tr>

						<tr>
							<td> Jam </td>
							<td> {{ $attendancePermissionSubmission->dateTimeText('H:i') }} </td>
						</tr>

                        <tr>
                            <td> Jenis Pengajuan </td>
                            <td> {{ $attendancePermissionSubmission->typeText() }} </td>
                        </tr>

						<tr>
							<td> Alasan </td>
							<td> {{ $attendancePermissionSubmission->reason }} </td>
						</tr>

						<tr>
							<td> Status </td>
							<td> {!! $attendancePermissionSubmission->statusHtml() !!} </td>
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
							@foreach($attendancePermissionSubmission->attendancePermissionSubmissionApprovals as $approval)
							<tr>
								<td> {{ $approval->userName() }} </td>
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
					url: `{{ route('admin.attendance_permission_submission.approve', $attendancePermissionSubmission->id) }}`,
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
					url: `{{ route('admin.attendance_permission_submission.reject', $attendancePermissionSubmission->id) }}`,
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
					url: `{{ route('admin.attendance_permission_submission.cancel', $attendancePermissionSubmission->id) }}`,
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
                    url: `{{ route('admin.attendance_permission_submission.resend_broadcast', $approval->id) }}`,
                    method: 'post',
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
