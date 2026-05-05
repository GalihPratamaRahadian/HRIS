@extends('template.backLayout')


@section('content')
<form id="mainForm">
	<div class="row">

		<div class="col-lg-5">
			<div class="card support-pane-card">
				<div class="card-body">
					
					{!! Template::titleBanner($title) !!}

					{!! Template::requiredBanner() !!}

					<div class="form-group">
						<label> Nama Jam Kerja {!! Template::required() !!} </label>
						<input type="text" name="shift_name" class="form-control" placeholder="Nama Jam Kerja" value="{{ $shift->shift_name }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Batas Jam Awal Kehadiran {!! Template::required() !!} </label>
						<input type="text" name="clock_start_limit" class="form-control" placeholder="Batas Jam Awal Kehadiran" value="{{ $shift->clockStartLimitText('H:i') }}" autocomplete="off" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label> Jam Mulai {!! Template::required() !!} </label>
								<input type="text" name="clock_start" class="form-control" placeholder="Jam Mulai" value="{{ $shift->clockStartText('H:i') }}" autocomplete="off" required>
								<span class="invalid-feedback"></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label> Jam Selesai {!! Template::required() !!} </label>
								<input type="text" name="clock_end" class="form-control" placeholder="Jam Selesai" value="{{ $shift->clockEndText('H:i') }}" autocomplete="off" required>
								<span class="invalid-feedback"></span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label> Toleransi Keterlambatan </label>
						<input type="number" name="late_tolerance" class="form-control" placeholder="Toleransi Keterlambatan" value="{{ $shift->late_tolerance }}">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Hari Libur </label>
						<div class="custom-control custom-checkbox ml-2">
							<input type="checkbox" class="custom-control-input offday" name="offday_shift[]" value="1" id="addSenin">
							<label class="custom-control-label pt-1" for="addSenin"> Senin </label>
						</div>
						<div class="custom-control custom-checkbox ml-2">
							<input type="checkbox" class="custom-control-input offday" name="offday_shift[]" value="2" id="addSelasa">
							<label class="custom-control-label pt-1" for="addSelasa"> Selasa </label>
						</div>
						<div class="custom-control custom-checkbox ml-2">
							<input type="checkbox" class="custom-control-input offday" name="offday_shift[]" value="3" id="addRabu">
							<label class="custom-control-label pt-1" for="addRabu"> Rabu </label>
						</div>
						<div class="custom-control custom-checkbox ml-2">
							<input type="checkbox" class="custom-control-input offday" name="offday_shift[]" value="4" id="addKamis">
							<label class="custom-control-label pt-1" for="addKamis"> Kamis </label>
						</div>
						<div class="custom-control custom-checkbox ml-2">
							<input type="checkbox" class="custom-control-input offday" name="offday_shift[]" value="5" id="addJumat">
							<label class="custom-control-label pt-1" for="addJumat"> Jum'at </label>
						</div>
						<div class="custom-control custom-checkbox ml-2">
							<input type="checkbox" class="custom-control-input offday" name="offday_shift[]" value="6" id="addSabtu">
							<label class="custom-control-label pt-1" for="addSabtu"> Sabtu </label>
						</div>
						<div class="custom-control custom-checkbox ml-2">
							<input type="checkbox" class="custom-control-input offday" name="offday_shift[]" value="7" id="addMinggu">
							<label class="custom-control-label pt-1" for="addMinggu"> Minggu </label>
						</div>
						<span class="invalid-feedback"></span>
					</div>

					<hr>

					<div class="form-group mt-3">
						<button class="btn btn-success" type="submit">
							<i class="mdi mdi-check"></i> Simpan
						</button>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-7">
			<div class="card support-pane-card">
				<div class="card-body">
					
					{!! Template::titleBanner('Jam Kerja Rinci') !!}

					{!! Template::infoBanner('Opsional. Diisi jika dihari tertentu memiliki jam kerja yang berbeda dibanding hari biasanya.') !!}

					<table class="table" id="shiftDetailTable">
						<thead>
							<tr>
								<th> Hari </th>
								<th> Jam Masuk </th>
								<th> Jam Keluar </th>
								<th> Aksi </th>
							</tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="4" align="right">
									<button class="btn btn-sm btn-primary" type="button" id="addShiftDetailBtn">
										<i class="mdi mdi-plus"></i> Tambah
									</button>
								</td>
							</tr>
						</tfoot>
					</table>

					
				</div>
			</div>
		</div>

	</div>
</form>
@endsection


@section('script')
<script type="text/javascript">
	$(function(){

		let $form = $('#mainForm')
		let $submitBtn = $form.find('[type="submit"]').ladda();

		$form.find(`[name="clock_start_limit"], [name="clock_start"], [name="clock_end"]`).clockpicker({
			autoclose: true
		})

		const checkShiftDetailEmptyOrNot = () => {
			$('#shiftDetailTable').find('tbody').find('.empty').remove();

			let items = $('#shiftDetailTable').find('tbody').find('.shiftDetailItem');

			if(items.length == 0) {
				let html = $('#emptyShiftDetailTemplate').text();
				$('#shiftDetailTable').find('tbody').append(html);
			}
		}


		const clearShiftDetail = () => {
			$('#shiftDetailTable').find('tbody').find('.shiftDetailItem').remove();
			checkShiftDetailEmptyOrNot();
		}

		const renderEvent = () => {
			$('.removeRow').off('click');
			$('.removeRow').on('click', function(){
				$(this).parents('tr').remove();
				checkShiftDetailEmptyOrNot();
			})

			$('.clock-start, .clock-end').clockpicker({
				autoclose: true
			})
		}


		const addShiftDetail = (day = null, clockStart = null, clockEnd = null) => {
			let html = $('#shiftDetailTemplate').text();

			if(day != null) {
				html = html.replace(`value="${day}"`, `value="${day}" selected`);
			}

			if(clockStart != null) {
				html = html.replace(/{clock_start}/g, clockStart);
			}

			if(clockEnd != null) {
				html = html.replace(/{clock_end}/g, clockEnd);
			}

			$('#shiftDetailTable').find('tbody').append(html);
			renderEvent();
		}

		const init = () => {
			$form.find('[name="shift_name"]').focus();
		}


		$form.on('submit', function(e){
			e.preventDefault();

			let formData = $(this).serialize();

			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('admin.shift.update', $shift->id) }}`,
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

		init();

		@foreach($shift->getOffdayShift() as $offday)
		$('.offday[value="{{ $offday }}"]').prop('checked', true)
		@endforeach


		$('#addShiftDetailBtn').on('click', function(){
			addShiftDetail();
			checkShiftDetailEmptyOrNot();
		});

		@foreach($shift->shiftDetails as $shiftDetail)
		addShiftDetail(`{{ $shiftDetail->day }}`, `{{ $shiftDetail->clockStartText('H:i') }}`, `{{ $shiftDetail->clockEndText('H:i') }}`);
		@endforeach
		
		checkShiftDetailEmptyOrNot();

	});
</script>

<script type="text/html" id="shiftDetailTemplate">
	<tr class="shiftDetailItem">
		<td>
			<select class="form-control" name="shift_detail_day[]" required>
				<option value="1"> Senin </option>
				<option value="2"> Selasa </option>
				<option value="3"> Rabu </option>
				<option value="4"> Kamis </option>
				<option value="5"> Jum'at </option>
				<option value="6"> Sabtu </option>
				<option value="7"> Minggu </option>
			</select>
		</td>
		<td>
			<input type="text" name="shift_detail_clock_start[]" class="form-control clock-start" value="{clock_start}" placeholder="Jam Mulai" autocomplete="off" required>
		</td>
		<td>
			<input type="text" name="shift_detail_clock_end[]" class="form-control clock-end" value="{clock_end}" placeholder="Jam Selesai" autocomplete="off" required>
		</td>
		<td>
			<button class="btn btn-danger btn-sm p-2 removeRow" type="button">
				<i class="mdi mdi-close mr-0"></i>
			</button>
		</td>
	</tr>
</script>

<script type="text/html" id="emptyShiftDetailTemplate">
	<tr class="empty">
		<td colspan="4" align="center">
			<i> Kosong </i>
		</td>
	</tr>
</script>
@endsection