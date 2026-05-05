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

		$('.actionForm').on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let $form = $(this),
				$submitBtn = $(this).find(`[type="submit"]`).ladda(),
				formData = $(this).serialize(),
				href = $(this).attr('action');

			$submitBtn.ladda('start');
			ajaxSetup();
			$.ajax({
				url: href,
				method: 'post',
				data: formData,
				dataType: 'json'
			})
			.done(response => {
				ajaxSuccessHandling(response)
				setTimeout(() => {
					window.location.reload();
				}, 1000)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		})

	})

</script>
@endsection