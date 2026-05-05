@extends('template.backLayout')


@section('content')

@if(UserPermission::check('sick_necessity_submission', 'a'))
@if($sickNecessitySubmission->isStatusWaiting())
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
@elseif($sickNecessitySubmission->isStatusApproved())
<div class="row grid-margin">
	<div class="col-lg-12">
		{!! Template::infoBanner('Pembatalan penyetujuan sakit/izin akan menghapus data sakit/izin karyawan tersebut.') !!}
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
								<b>
									<a href="{{ route('employee.detail', $sickNecessitySubmission->id_employee) }}">
									{{ $sickNecessitySubmission->employeeName() }}
									</a>
								</b>
							</td>
						</tr>

						<tr>
							<td> Dibuat Pada </td>
							<td> <b> {{ $sickNecessitySubmission->createdAtText() }} </b> </td>
						</tr>

						<tr>
							<td> Jenis </td>
							<td> <b> {{ $sickNecessitySubmission->type }} </b> </td>
						</tr>

						<tr>
							<td> Alasan </td>
							<td> <b> {{ $sickNecessitySubmission->reason }} </b> </td>
						</tr>

						<tr>
							<td> Tanggal </td>
							<td> <b> {{ $sickNecessitySubmission->intervalDateText() }} </b> </td>
						</tr>

						<tr>
							<td> Status Pengajuan </td>
							<td> <b> {!! $sickNecessitySubmission->statusHtml() !!} </b> </td>
						</tr>

						<tr>
							<td> Deskripsi </td>
							<td> <b> {{ $sickNecessitySubmission->descriptionText() }} </b> </td>
						</tr>

						<tr>
							<td> Lampiran File </td>
							<td>
								<a href="{{ $sickNecessitySubmission->fileLink() }}" target="_blank">
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
					url: `{{ route('admin.sick_necessity_submission.approve', $sickNecessitySubmission->id) }}`,
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
					url: `{{ route('admin.sick_necessity_submission.reject', $sickNecessitySubmission->id) }}`,
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
					url: `{{ route('admin.sick_necessity_submission.cancel', $sickNecessitySubmission->id) }}`,
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
                    url: `{{ route('admin.sick_necessity_submission.resend_broadcast', $approval->id) }}`,
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
