@extends('template.backLayout')


@section('content')

@if(!auth()->user()->isEmployee() && $submission->isStatusWaiting())
<div class="row grid-margin">
	<div class="col-lg-12">
		
		<div class="table-responsive">
			<button class="btn btn-success show-btn" data-target="#approveCard">
				<i class="mdi mdi-check"></i> Setujui
			</button>
			<button class="btn btn-danger show-btn" data-target="#rejectCard">
				<i class="mdi mdi-close"></i> Tolak
			</button>
		</div>

	</div>
</div>
@endif

<div class="row">
	<div class="col-md-6">

		<div class="card support-pane-card grid-margin" id="approveCard" style="display: none;">
			<div class="card-body">
				
				{!! Setting::titleBanner('Penyetujuan') !!}

				<form class="actionForm" action="{{ route('submission.leave.approve', $submission->id) }}">
					
					<div class="form-group">
						<label> Penggajian {!! \Setting::required() !!} </label>
						<select class="form-control" name="salary" required>
							@foreach(GlobalData::salaryPercentForEmployeeLeave() as $val => $label)
							<option value="{{ $val }}"> {{ $label }} </option>
							@endforeach
						</select>
					</div>

					<hr>

					<button class="btn btn-success" type="submit">
						<i class="mdi mdi-check"></i> Setujui
					</button>

					<button class="btn btn-danger show-btn" type="button" data-target="#approveCard">
						<i class="mdi mdi-close"></i> Batal
					</button>

				</form>

			</div>
		</div>


		<div class="card support-pane-card grid-margin" id="rejectCard" style="display: none;">
			<div class="card-body">
				
				{!! Setting::titleBanner('Penolakan') !!}

				<form class="actionForm" action="{{ route('submission.leave.reject', $submission->id) }}">
					
					<div class="form-group">
						<label> Pesan untuk karyawan (Opsional) </label>
						<textarea class="form-control" name="message" placeholder="Pesan untuk karyawan (Opsional)" rows="3"></textarea>
					</div>

					<hr>

					<button class="btn btn-success" type="submit">
						<i class="mdi mdi-check"></i> Lanjutkan
					</button>

					<button class="btn btn-danger show-btn" type="button" data-target="#rejectCard">
						<i class="mdi mdi-close"></i> Batal
					</button>

				</form>

			</div>
		</div>


		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				
				{!! Setting::titleBanner($title) !!}

				<div class="table-responsive">
					<table class="table table-hover">

						@if(!auth()->user()->isEmployee())
						<tr>
							<td> Nama Karyawan </td>
							<td> 
								<a href="{{ route('employee.detail', $submission->id_employee) }}">
									{{ $submission->employeeName() }}
								</a>
							</td>
						</tr>
						@endif

						<tr>
							<td> Dibuat Pada </td>
							<td> {{ $submission->createdAtText() }} </td>
						</tr>

						<tr>
							<td> Alasan </td>
							<td> {{ $submission->leaveReasonText() }} </td>
						</tr>

						<tr>
							<td> Tanggal {{ $submission->leaveReasonText() }} </td>
							<td> {{ $submission->intervalDateText() }} </td>
						</tr>

						<tr>
							<td> Status </td>
							<td> {!! $submission->statusHtml() !!} </td>
						</tr>

						<tr>
							<td> Deskripsi </td>
							<td> {{ $submission->descriptionText() }} </td>
						</tr>

						@if($submission->isHasMeta('salary_percent'))
						<tr>
							<td> Penggajian </td>
							<td> {{ $submission->getMeta('salary_label') }} </td>
						</tr>
						@endif

					</table>
				</div>

			</div>
		</div>
	</div>

	@if($submission->isHasFile())
	<div class="col-md-6">
		<div class="card support-pane-card grid-margin">
			<div class="card-body">

				{!! Setting::titleBanner('Lampiran File') !!}

				@if($submission->fileIsImage())
				<img src="{{ $submission->fileLink() }}" class="img-fluid mb-3">
				@endif
				<a href="{{ $submission->fileLink() }}" class="btn btn-primary btn-block" download>
					<i class="mdi mdi-download"></i> {{ $submission->file }}
				</a>

			</div>
		</div>
	</div>
	@endif

</div>
@endsection


@section('script')
<script type="text/javascript">
	
	$(function(){

		renderLibEvent()

		$('.actionForm').on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let form = $(this),
				formData = $(this).serialize(),
				submitBtn = $(this).find(`[type="submit"]`).ladda(),
				href = $(this).attr('action');

			submitBtn.ladda('start');
			ajaxSetup();
			$.ajax({
				url: href,
				method: 'post',
				data: formData,
				dataType: 'json'
			})
			.done(response => {
				let { code, message } = response

				if(code == 200) {
					toastrAlert();
					toastr.success(message, 'Berhasil');
					setTimeout(() => {
						window.location.reload();
					}, 1000)
				}
			})
			.fail(error => {
				let { status, responseJSON } = error
				let { message, code } = responseJSON

				if(code == 422) {
					let { errors } = responseJSON
					invalidResponse(form, errors)
				}

				toastrAlert();
				toastr.warning(message, 'Peringatan')

				submitBtn.ladda('stop')
			})
		})

		@if($submission->isReasonLeave())
		$('#approveCard').find('[name="salary"]').val('100');
		@else
		$('#approveCard').find('[name="salary"]').val('0');
		@endif

	})

</script>
@endsection