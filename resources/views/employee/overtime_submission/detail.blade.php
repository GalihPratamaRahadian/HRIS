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
							<td> Nama Karyawan </td>
							<td> 
								{{ $overtimeSubmission->employeeName() }}
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