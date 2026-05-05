@extends('template.backLayout')


@section('content')

<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Setting::titleBanner($title) !!}

				<form id="mainForm">
					{!! \Setting::requiredBanner() !!}

					<div class="form-group">
						<label> Pilih Karyawan {!! \Setting::required() !!} </label> <br>
						<label><b> {{ $webAttendancePermissions->employeeName() }} </b></label>
					</div>

					<div class="form-group">
						<label> Pilih Lokasi {!! \Setting::required() !!} </label> <br>
						<label> 
							<a href="javascript:void(0);" class="addActiveLocationsBtn"> Pilih semua lokasi </a>
						</label>
						<div class="mb-2">
							<table style="width: 100%;" border="0">
								<tr>
									<td>
										<select name="id_location" style="width: 100%;">

											@foreach(\App\Models\AttendanceLocationRule::all() as $locationRule)

											<option value="{{ $locationRule->id }}" data-name="{{ $locationRule->location_name }}"> {{ $locationRule->location_name }} </option>

											@endforeach

										</select>
									</td>
									<td width="50">
										<button class="btn btn-success px-2 addLocationBtn" type="button">
											<i class="mdi mdi-plus"></i> Tambah
										</button>
									</td>
								</tr>
							</table>
						</div>
						<div style="max-height: 300px; overflow-y: auto;" class="border px-2 py-1 rounded">
							<table class="table table-hover" id="locationList">
								<tbody>
									
								</tbody>
							</table>
						</div>
					</div>

					<div class="form-group">
						<label> Berlaku Hingga {!! \Setting::required() !!} </label>
						<input type="date" name="valid_until" class="form-control" required>
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
<script type="text/html" id="employeeItemTemplate">
	<tr class="employeeItem" data-id="{id}">
		<td class="p-2">
			{name}
		</td>
		<td width="30" class="p-2">
			<input type="hidden" name="id_employees[]" value="{id}">
			<button class="btn btn-danger p-1 remove" type="button">
				<i class="mdi mdi-trash-can mr-0"></i>
			</button>
		</td>
	</tr>
</script>

<script type="text/html" id="locationItemTemplate">
	<tr class="locationItem" data-id="{id}">
		<td class="p-2">
			{name}
		</td>
		<td width="30" class="p-2">
			<input type="hidden" name="id_locations[]" value="{id}">
			<button class="btn btn-danger p-1 remove" type="button">
				<i class="mdi mdi-trash-can mr-0"></i>
			</button>
		</td>
	</tr>
</script>


<script type="text/javascript">
	$(function(){

		const $form = $('#mainForm')
		const $submitBtn = $form.find('[type="submit"]').ladda();
		const $locationListElem = $('#locationList').find('tbody');

		const resetForm = () => {
			locationListEmptyCheck()
		}

		const init = () => {
			resetForm();

			$form.find('[name="id_location"]').select2({
				placeholder : '- Pilih Lokasi -',
			}).trigger('change')
			$form.find('[name="id_location"]').val('').trigger('change')
		}


		const addLocation = (id, name) => {
			let elem = $locationListElem.find(`.locationItem[data-id="${id}"]`);

			if(elem.length > 0) {
				return false;
			} else {
				let html = $('#locationItemTemplate').text()
							.replaceAll(/{id}/g, id)
							.replaceAll(/{name}/g, name)

				$locationListElem.append(html);
				locationListEmptyCheck()
				renderedEvent()

				return true;
			}
		}

		const locationListEmptyCheck = () => {
			if($('#locationList').find('.locationItem').length == 0) {
				if($('#locationList').find('.empty').length == 0) {
					let html = `<tr class="empty">
									<td align="center"><i> Kosong </i></td>
								</tr>`;
					$locationListElem.append(html);
				}
			} else {
				$locationListElem.find('.empty').remove();
			}
		}

		const renderedEvent = () => {
			$form.find('.remove').off('click')
			$form.find('.remove').on('click', function(){
				$(this).parents('tr').remove();
				renderedEvent()
				locationListEmptyCheck();
			})
		}


		$form.on('submit', function(e){
			e.preventDefault();

			let formData = $(this).serialize();
			$submitBtn.ladda('start')
			ajaxSetup();
			$.ajax({
				url : `{{ route('web_attendance_permissions.update', $webAttendancePermissions->id) }}`,
				method : 'put',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				$submitBtn.ladda('stop');
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});


		$form.find('.addLocationBtn').on('click', function(){
			const id = $('[name="id_location"]').val();
			const name =  $('[name="id_location"]').find(`option[value="${id}"]`).data('name');

			if(!isEmpty(id) && !isEmpty(name)) {
				const isSuccess = addLocation(id, name);
				console.log(isSuccess);
				if(!isSuccess) {
					alert(`${name} sudah termasuk kedalam list`)
				} else {
					$('[name="id_location"]').val('').trigger('change');
				}
			}
		})

		$form.find('.addActiveLocationsBtn').on('click', function(){
			@foreach(\App\Models\AttendanceLocationRule::all() as $location)
			addLocation(`{{ $location->id }}`, `{{ $location->location_name }}`)
			@endforeach
		})

		@foreach($webAttendancePermissions->getLocations() as $location)
		addLocation(`{{ $location->id }}`, `{{ $location->location_name }}`)
		@endforeach

		init();

		$form.find(`[name="valid_until"]`).val(`{{ $webAttendancePermissions->valid_until }}`);
	});
</script>
@endsection