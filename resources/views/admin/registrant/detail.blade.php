@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-lg-4">
		<img src="{{ $registrant->photoLink('face') }}" class="img-fluid">
	</div>
	<div class="col-md-8">

		@if(!$registrant->isStatusApproved())
		<div class="row grid-margin">
			<div class="col-lg-12">

				@if($registrant->isStatusWaiting() || $registrant->isStatusRejected())
				@if(UserPermission::check('registration', 'u') || UserPermission::check('registration', 'a'))
					@if(UserPermission::check('registration', 'u'))
					<a href="{{ route('registration.photo_rotate_to_left', $registrant->id) }}" class="btn mb-1 btn-danger action-btn">
						<i class="mdi mdi-rotate-left"></i> Putar Ke Kiri
					</a>

					<a href="{{ route('registration.photo_rotate_to_right', $registrant->id) }}" class="btn mb-1 btn-primary action-btn">
						Putar Ke Kanan <i class="mdi mdi-rotate-right"></i>
					</a>
					@endif

					@if(UserPermission::check('registration', 'a'))
					<button class="btn mb-1 btn-success action-btn" data-href="{{ route('registration.approve', $registrant->id) }}" data-alert="Yakin setujui?">
						<i class="mdi mdi-check"></i> Setujui
					</button>

					@if($registrant->isStatusWaiting())
					<button class="btn mb-1 btn-danger reject-btn">
						<i class="mdi mdi-close"></i> Tolak
					</button>
					@endif
					@endif
				@endif
				@endif
				
				<button class="btn mb-1 btn-primary action-btn" data-href="{{ route('registration.reset_and_send', $registrant->id) }}" data-alert="Yakin ingin reset dan kirim ulang login pengguna?">
					<i class="mdi mdi-sync"></i> Reset & Kirim Ulang Login Pengguna
				</button>
			</div>
		</div>
		@endif

		<div class="card support-pane-card grid-margin reject" style="display: none;">
			<div class="card-body">

				{!! Setting::titleBanner('Tolak Pendaftaran') !!}

				<form id="rejectForm">
					
					<div class="form-group">
						<label> Catatan </label>
						<textarea class="form-control" name="notes" rows="3" placeholder="Catatan (Opsional)"></textarea>
					</div>

					<hr>

					<button class="btn btn-success btn-sm" type="submit">
						<i class="mdi mdi-check"></i> Lanjutkan
					</button>

					<button class="btn btn-danger btn-sm reject-btn" type="button">
						<i class="mdi mdi-close"></i> Batal
					</button>

				</form>

			</div>
		</div>
		
		<div class="card support-pane-card">
			<div class="card-body">

				{!! Setting::titleBanner('Detail Pendaftaran') !!}

				<table class="table table-hover">
					
					<tr>
						<td> Nomor Induk </td>
						<td> {{ $registrant->employee_number }} </td>
					</tr>

					<tr>
						<td> Nama </td>
						<td>
							@if(!empty($registrant->employee))
							<a href="{{ route('employee.detail', $registrant->id) }}">
								{{ $registrant->employee_name }}
							</a>
							@else
							{{ $registrant->employee_name }}
							@endif
						</td>
					</tr>

					<tr>
						<td> Jenis Kelamin </td>
						<td> {{ $registrant->genderText() }} </td>
					</tr>

					<tr>
						<td> Email </td>
						<td> {{ $registrant->email }} </td>
					</tr>

					<tr>
						<td> Nomor Telepon </td>
						<td> {{ $registrant->phone_number }} </td>
					</tr>

					<tr>
						<td> No Jamsostek </td>
						<td> {{ $registrant->jamsostekText() }} </td>
					</tr>

					<tr>
						<td> Status Pekerjaan </td>
						<td> {{ $registrant->jobStatusText() }} </td>
					</tr>

					<tr>
						<td> Departemen </td>
						<td> {{ $registrant->departmentName() }} </td>
					</tr>

					<tr>
						<td> Jabatan </td>
						<td> {{ $registrant->positionName() }} </td>
					</tr>

					<tr>
						<td> Jenis Jam Kerja </td>
						<td> {{ $registrant->shift_type == 'routine' ? 'Shift Rutin' : 'Shift Harian' }} </td>
					</tr>

					@if($registrant->shift_type == 'routine')
					<tr>
						<td> Jam Kerja </td>
						<td> {{ $registrant->shiftName() }} </td>
					</tr>
					@endif

					<tr>
						<td> Grup Karyawan </td>
						<td> {{ $registrant->employeeGroupName() }} </td>
					</tr>

					<tr>
						<td> Tanggal Mulai Bekerja </td>
						<td> {{ $registrant->start_working_date ?? '-' }} </td>
					</tr>

					<tr>
						<td> Tempat, Tanggal Lahir </td>
						<td> {{ $registrant->place_of_birth ?? '-' }}, {{ $registrant->date_of_birth ?? '-' }} </td>
					</tr>

					<tr>
						<td> Alamat </td>
						<td> {{ $registrant->address ?? '-' }} </td>
					</tr>

					<tr>
						<td> Pendidikan Terakhir </td>
						<td> {{ $registrant->last_education ?? '-' }} </td>
					</tr>

					<tr>
						<td> Jurusan Pendidikan Terakhir </td>
						<td> {{ $registrant->last_education_major ?? '-' }} </td>
					</tr>

					<tr>
						<td> Status Pernikahan </td>
						<td> {{ $registrant->marital_status ?? '-' }} </td>
					</tr>

					<tr>
						<td> Golongan Darah </td>
						<td> {{ $registrant->blood_type ?? '-' }} </td>
					</tr>

					<tr>
						<td> No KTP </td>
						<td> {{ $registrant->ktp_number ?? '-' }} </td>
					</tr>

					<tr>
						<td> No NPWP </td>
						<td> {{ $registrant->npwp_number ?? '-' }} </td>
					</tr>

					<tr>
						<td> Status Pendaftaran </td>
						<td> {!! $registrant->statusHtml() !!} </td>
					</tr>

					@if(!$registrant->isStatusUnfill())
					<tr>
						
						@if($registrant->isStatusApproved())

						<td> Disetujui Pada </td>
						<td> {{ $registrant->approvedAtText() }} </td>

						@elseif($registrant->isStatusRejected())

						<td> Ditolak Pada </td>
						<td> {{ $registrant->rejectedAtText() }} </td>

						@else
						<td> Terakhir Diubah Pada </td>
						<td> {{ $registrant->editedAtText() }} </td>
						@endif

					</tr>
					@endif

				</table>
			</div>
		</div>
	</div>
</div>
@endsection


@section('script')
<script type="text/javascript">
	
	$(function(){

		$('.action-btn').on('click', function(){
			let { alert, href } = $(this).data()
			const btn = $(this).ladda()

			confirmation(alert, () => {
				btn.ladda('start')
				ajaxSetup();
				$.ajax({
					url : href,
					method : 'post',
				})
				.done(response => {
					ajaxSuccessHandling(response)
					setTimeout(() => {
						window.location.reload();
					}, 1000)
				})
				.fail(error => {
					btn.ladda('stop')
					ajaxErrorHandling(error);
				})

			})
		});

		$('.reject-btn').on('click', function(){
			if($('.reject').first().is(":hidden")){
				$('.reject').slideDown( "slow" );
			} else {
				$('.reject').slideUp("slow");
			}
		})

		const rejectForm = $('#rejectForm');
		const rejectSubmitBtn = rejectForm.find('[type="submit"]').ladda();

		rejectForm.on('submit', function(e){
			e.preventDefault();

			let formData = $(this).serialize();

			rejectSubmitBtn.ladda('start');

			ajaxSetup();
			$.ajax({
				url : `{{ route('registration.reject', $registrant->id) }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {

				let { message, code } = response

				if(code == 200) {
					toastrAlert();
					toastr.success(message, 'Berhasil');
					setTimeout(() => {
						window.location.reload();
					}, 1000)
				}
			})
			.fail(error => {
				rejectSubmitBtn.ladda('stop')

				let { status, responseJSON } = error
				let { message } = responseJSON

				if(status == 422) {
					let { errors } = responseJSON
					invalidResponse($(this), errors)
				}

				toastrAlert()
				toastr.warning(message, 'Peringatan')

			})
		})
	})

</script>
@endsection