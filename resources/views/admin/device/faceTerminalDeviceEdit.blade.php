@extends('template.backLayout')


@section('content')
<?php 
	$wajibDiisi = '<span class="text-danger"> * </span>';
?>
<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> {{ $title }} </h4>
				</div>

				<form id="mainForm">
					<div class="alert alert-info">
						Kolom bertanda {!! $wajibDiisi !!} wajib diisi.
					</div>
					
					<div class="form-group">
						<label> Nama Device {!! $wajibDiisi !!} </label>
						<input type="text" name="device_name" class="form-control" placeholder="Nama Device" value="{{ $faceTerminalDevice->device_name }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> IP Address {!! $wajibDiisi !!} </label>
						<input type="text" name="ip_address" class="form-control" placeholder="IP Address" pattern="^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$" value="{{ $faceTerminalDevice->ip_address }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Port {!! $wajibDiisi !!} </label>
						<input type="number" name="port" class="form-control" placeholder="Port" value="{{ $faceTerminalDevice->port }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Username {!! $wajibDiisi !!} </label>
						<input type="text" name="username" class="form-control" placeholder="Username" value="{{ $faceTerminalDevice->username }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Password {!! $wajibDiisi !!} </label>
						<input type="text" name="password" class="form-control" placeholder="Password" value="{{ $faceTerminalDevice->password }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Status {!! \Setting::required() !!} </label>
						<select class="form-control" name="status" required>

							<option value="{{ \App\Models\FaceTerminalDevice::STATUS_ACTIVE }}">
								Aktif
							</option>
							<option value="{{ \App\Models\FaceTerminalDevice::STATUS_NOT_ACTIVE }}">
								Tidak Aktif
							</option>

						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tipe {!! $wajibDiisi !!} </label>
						<select class="form-control" name="type" required>
							
							@foreach(\App\Models\FaceTerminalDevice::availableTypes() as $device)
							<option value="{{ $device['type'] }}">
								{{ $device['label'] }}
							</option>
							@endforeach

						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div id="metaForm"></div>

					<div class="form-group">
						<label> Geolokasi </label>
						<p class="text-muted"> 
							* Format Penulisan "latitude;longitude" <br> 
							Contoh Penulisan "-6.715626;108.566854" tanpa tanda petik 
						</p>
						<input type="text" name="geolocation" class="form-control" placeholder="Geolokasi" value="{{ $faceTerminalDevice->getMeta('latitude').';'.$faceTerminalDevice->getMeta('longitude') }}">
						<span class="invalid-feedback"></span>
					</div>

					<hr>

					<div class="form-group">
						<button class="btn btn-success" type="submit">
							<i class="mdi mdi-check"></i> Simpan
						</button>
					</div>
				</form>

			</div>
		</div>
	</div>
</div>
@endsection


@section('script')
<script type="text/html" id="chinaFTFormTemplate">
	<div class="form-group">
		<label> Device ID {!! \Setting::required() !!} </label>
		<input type="number" name="device_id" class="form-control" placeholder="Device ID" required>
	</div>
</script>


<script type="text/javascript">
	$(function(){

		let $form = $('#mainForm')
		let $submitBtn = $form.find('[type="submit"]').ladda();

		const init = () => {
			$form.find('[name="device_name"]').focus();
		}


		$form.on('submit', function(e){
			e.preventDefault();

			let formData = $(this).serialize();
			$submitBtn.ladda('start')
			ajaxSetup();
			$.ajax({
				url : `{{ route('face_terminal_device.update', $faceTerminalDevice->id) }}`,
				method : 'put',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				init();
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});

		$form.find('[name="type"]').on('change', function(){
			const type = $(this).val();
			let html = "";

			if(type == parseInt(`{{ \App\Models\FaceTerminalDevice::TYPE_CHINA_FT }}`)) {
				html = $('#chinaFTFormTemplate').text();
			}

			$('#metaForm').html(html);
		});

		$form.find('[name="status"]').val(`{{ $faceTerminalDevice->status }}`)
		$form.find('[name="status"]').trigger('change');
		$form.find('[name="type"]').val(`{{ $faceTerminalDevice->type }}`)
		$form.find('[name="type"]').trigger('change');

		@foreach($faceTerminalDevice->getMetaData() as $key => $value)
		$form.find('[name="{{ $key }}"]').val(`{{ $value }}`);
		@endforeach

		init();
	});
</script>
@endsection