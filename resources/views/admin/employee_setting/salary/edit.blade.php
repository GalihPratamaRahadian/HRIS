@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					{!! \Setting::requiredBanner() !!}
					
					<div class="form-group">
						<label> Karyawan </label>
						<p class="ml-2"><b> {{ $employeeSalary->employeeName() }} </b></p>
					</div>

					<div class="form-group">
                        <label> Gaji Pokok {!! \Setting::required() !!} </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> Rp </span>
                            </div>
                            <input type="number" name="basic_salary" class="form-control" placeholder="Gaji Pokok" value="{{ $employeeSalary->basic_salary }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label> Upah Lembur Per Jam {!! \Setting::required() !!} </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> Rp </span>
                            </div>
                            <input type="number" name="overtime_pay" class="form-control" placeholder="Upah Lembur Per Jam" value="{{ $employeeSalary->overtime_pay }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label> Upah Lembur Per Jam {!! \Setting::required() !!} </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> Rp </span>
                            </div>
                            <input type="number" name="overtime_pay" class="form-control" placeholder="Upah Lembur Per Jam" value="{{ $employeeSalary->overtime_pay }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label> Tunjangan Makan Harian {!! \Setting::required() !!} </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> Rp </span>
                            </div>
                            <input type="number" name="daily_meal_allowance" class="form-control" placeholder="Tunjangan Makan Harian" value="{{ $employeeSalary->daily_meal_allowance }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label> Tunjangan Transportasi Harian {!! \Setting::required() !!} </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> Rp </span>
                            </div>
                            <input type="number" name="daily_transportation_allowance" class="form-control" placeholder="Tunjangan Transportasi Harian" value="{{ $employeeSalary->daily_transportation_allowance }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label> Tunjangan Tetap </label><br>
                        <button type="button" class="btn btn-primary allowanceBtn py-1 px-2 ml-3 mb-2">
                        	<i class="mdi mdi-plus-thick m-0"></i> Tambah
                        </button>
                        <table class="allowanceGroup table table-sm" style="display: none;">
                            <thead>
                                <tr class="text-center">
                                    <th> Tunjangan </th>
                                    <th> Nominal </th>
                                    <th> Aksi </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                    </div>
                    <div class="form-group">
                        <label> Potongan Tetap </label><br>
                        <button type="button" class="btn btn-primary cutBtn py-1 px-2 ml-3 mb-2">
                        	<i class="mdi mdi-plus-thick m-0"></i> Tambah 
                        </button>
                        <table class="cutGroup table table-sm" style="display: none;">
                            <thead>
                                <tr class="text-center">
                                    <th> Potongan </th>
                                    <th> Nominal </th>
                                    <th> Aksi </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

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
<script type="text/html" id="allowanceTemplate">
	<tr>
		<td>
			<input type="text" name="allowance_name[]" class="form-control" placeholder="Nama Tunjangan" value="{name}" required>
		</td>
		<td>
			<input type="number" name="allowance_nominal[]" class="form-control" placeholder="Nominal Tunjangan" value="{nominal}" required>
		</td>
		<td>
			<button type="button" class="btn btn-danger removeBtn p-1">
				<i class="mdi mdi-close m-0"> </i>
			</button>
		</td>
	</tr>
</script>

<script type="text/html" id="cutTemplate">
	<tr>
		<td>
			<input type="text" name="cut_name[]" class="form-control" placeholder="Nama Potongan" value="{name}" required>
		</td>
		<td>
			<input type="number" name="cut_nominal[]" class="form-control" placeholder="Nominal Potongan" value="{nominal}" required>
		</td>
		<td>
			<button type="button" class="btn btn-danger removeBtn p-1">
				<i class="mdi mdi-close m-0"> </i>
			</button>
		</td>
	</tr>
</script>

<script type="text/javascript">
	$(function(){

		let $form = $('#mainForm')
		let $submitBtn = $form.find('[type="submit"]').ladda();

		const resetForm = () => {
			$form[0].reset();
		}

		const init = () => {
			resetForm();
			$form.find('[name="id_employee"]').val('').trigger('change')
		}

		$form.find('[name="id_employee"]').select2({
			placeholder : '- Pilih Karyawan -'
		})

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();
			$submitBtn.ladda('start')
			ajaxSetup();
			$.ajax({
				url : `{{ route('employee_salary.update', $employeeSalary->id) }}`,
				method : 'put',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});

		const allowanceGroup = $form.find('.allowanceGroup');
		const cutGroup = $form.find('.cutGroup');

		const renderEvent = () => {
			$form.find('.removeBtn').off('click');
			$form.find('.removeBtn').on('click', function(){
				$(this).parents('tr').remove();
				allowanceAndCutTableTrigger();
			})
		}


		const allowanceAndCutTableTrigger = () => {
			if(allowanceGroup.find('tbody').find('tr').length > 0) {
				allowanceGroup.show();
			} else {
				allowanceGroup.hide();
			}

			if(cutGroup.find('tbody').find('tr').length > 0) {
				cutGroup.show();
			} else {
				cutGroup.hide();
			}
		}


		$form.find('.allowanceBtn').on('click', function(){
			const html = $('#allowanceTemplate').text()
							.replaceAll(/{name}/g, '')
							.replaceAll(/{nominal}/g, 0);

			allowanceGroup.find('tbody').append(html);
			allowanceAndCutTableTrigger();
			renderEvent();
		})

		$form.find('.cutBtn').on('click', function(){
			const html = $('#cutTemplate').text()
							.replaceAll(/{name}/g, '')
							.replaceAll(/{nominal}/g, 0);

			cutGroup.find('tbody').append(html);
			allowanceAndCutTableTrigger();
			renderEvent();
		})

		let itemHtml = '';

		@foreach($employeeSalary->employeeSalaryAllowances as $allowance)
		itemHtml = $('#allowanceTemplate').text()
						.replaceAll(/{name}/g, `{{ $allowance->allowance_name }}`)
						.replaceAll(/{nominal}/g, `{{ $allowance->allowance_nominal }}`);;

		allowanceGroup.find('tbody').append(itemHtml);
		@endforeach

		@foreach($employeeSalary->employeeSalaryCuts as $cut)
		itemHtml = $('#cutTemplate').text()
						.replaceAll(/{name}/g, `{{ $cut->cut_name }}`)
						.replaceAll(/{nominal}/g, `{{ $cut->cut_nominal }}`);;

		cutGroup.find('tbody').append(itemHtml);
		@endforeach

		allowanceAndCutTableTrigger();
		renderEvent();

		init();
	});
</script>
@endsection