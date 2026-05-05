@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-6">

		<div class="card support-pane-card grid-margin">
			<div class="card-body">

				{!! Setting::titleBanner($title) !!}

				<div class="table-responsive">
					<table class="table table-hover">

						<tr>
							<td> Diajukan Pada </td>
							<td> {{ $approval->attendancePermissionSubmission->createdAtText() }} </td>
						</tr>

						@if(!auth()->user()->isEmployee())
						<tr>
							<td> Nama Karyawan </td>
							<td>
								<a href="{{ route('employee.detail', $approval->attendancePermissionSubmission->id_employee) }}">
									{{ $approval->attendancePermissionSubmission->employeeName() }}
								</a>
							</td>
						</tr>
						@endif

						<tr>
							<td> Tanggal </td>
							<td> {{ $approval->attendancePermissionSubmission->dateTimeText('d M Y') }} </td>
						</tr>

						<tr>
							<td> Jam </td>
							<td> {{ $approval->attendancePermissionSubmission->dateTimeText('H:i') }} </td>
						</tr>

                        <tr>
                            <td> Jenis Pengajuan </td>
                            <td> {!! $approval->attendancePermissionSubmission->typeText() !!} </td>
                        </tr>

						<tr>
							<td> Alasan </td>
							<td> {{ $approval->attendancePermissionSubmission->reason }} </td>
						</tr>

						<tr>
							<td> Status </td>
							<td> {!! $approval->statusHtml() !!} </td>
						</tr>

					</table>
				</div>

				@if($approval->isStatusWaiting())
				<hr>

				<button class="btn btn-success" id="approve-btn">
					<i class="mdi mdi-check"></i> Setujui
				</button>
				<button class="btn btn-danger" id="reject-btn">
					<i class="mdi mdi-close"></i> Tolak
				</button>
				@endif

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
					url: `{{ route('employee.attendance_permission_approval.approve', $approval->id) }}`,
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
					url: `{{ route('employee.attendance_permission_approval.reject', $approval->id) }}`,
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
