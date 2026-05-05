@extends('template.backLayout')


@section('content')
<div class="row">

	<div class="col-md-12 grid-margin">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="text-right">
					<button class="btn btn-primary" id="upload-btn">
						<i class="mdi mdi-link mdi-rotate-45"></i> Upload Link Peraturan Perusahaan
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> {{ $title }} </h4>
				</div>

				@if(setting('link_pkb'))
				<div style="min-width: 300px; min-height: 700px;">
					<iframe src="{{ setting('link_pkb') }}" style="width: 100%; height: 100vh;"></iframe>
				</div>
				@else
				<p align="center">
					Belum Upload Link Peraturan Perusahaan
				</p>
				@endif

			</div>
		</div>
	</div>
</div>
@endsection


@section('modal')
<div class="modal fade" id="upload-modal" role="dialog" aria-hidden="true">
	<div class="modal-dialog mt-4" role="document">
		<div class="modal-content">
			<form id="upload-form">

				<div class="modal-header">
					<h5 class="modal-title">
						Upload
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					{!! Template::requiredBanner() !!}

					<div class="form-group">
						<label> Link Peraturan Perusahaan {!! Template::required() !!} </label>
						<input type="text" name="link_pkb" class="form-control" placeholder="https://" value="{{ setting('link_pkb') }}" required>
					</div>
					
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">
						<i class="mdi mdi-check"></i> Simpan
					</button>
					<button type="button" class="btn btn-light" data-dismiss="modal">
						<i class="mdi mdi-close"></i> Tutup
					</button>
				</div>

			</form>
		</div>
	</div>
</div>
@endsection


@section('script')
<script type="text/javascript">
	$(function(){

		const $uploadForm = $('#upload-form');
		const $uploadSubmitBtn = $uploadForm.find(`[type="submit"]`).ladda();

		$('#upload-btn').on('click', function(){
			$('#upload-modal').modal('show');
		})

		$uploadForm.on('submit', function(e){
			e.preventDefault();

			$uploadSubmitBtn.ladda('start')
			let formData = $(this).serialize();
			ajaxSetup()

			$.ajax({
				method: 'post',
				url: `{{ route('admin.company_rules.save') }}`,
				dataType: 'json',
				data: formData,
			})
			.done(response => {
				ajaxSuccessHandling(response)
				setTimeout(() => {
					window.location.reload();
				}, 1000)
			})
			.fail(error => {
				ajaxErrorHandling(error, $uploadForm)
				$uploadSubmitBtn.ladda('stop')
			})

			reloadDT();
			getAttendanceData();
			setUrl()

			$('#filterModal').modal('hide');
		})
	});
</script>
@endsection