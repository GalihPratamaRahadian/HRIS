@extends('template.backLayout')


@section('content')
<form id="mainForm">
	<div class="row">
		<div class="col-md-6">
			<div class="card support-pane-card">
				<div class="card-body">
					{!! Template::titleBanner($title) !!}

					{!! Template::requiredBanner() !!}
					
					<div class="form-group">
						<label> Nama Hari Libur {!! Template::required() !!} </label>
						<input type="text" name="off_day_name" class="form-control" placeholder="Nama Hari Libur" value="{{ $offDay->off_day_name }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Awal Libur {!! Template::required() !!} </label>
						<input type="date" name="start_date" class="form-control" placeholder="Awal Libur" value="{{ $offDay->start_date }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Akhir Libur {!! Template::required() !!} </label>
						<input type="date" name="end_date" class="form-control" placeholder="Akhir Libur" value="{{ $offDay->end_date }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Target {!! Template::required() !!} </label>
						<select class="form-control" name="target" required>
							<option value="all"> Semua Karyawan </option>
							<option value="selected"> Pilih Karyawan </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

				</div>
			</div>
		</div>

		<div class="col-lg-6" id="choose-employee"></div>
	</div>

	<hr>

	<div class="form-group">
		<button class="btn btn-success" type="submit">
			<i class="mdi mdi-check"></i> Simpan
		</button>
	</div>
</form>
@endsection


@section('script')
<script type="text/javascript">
	$(function(){

		let $form = $('#mainForm')
		let $submitBtn = $form.find('[type="submit"]').ladda();

		const init = () => {
			$form.find('[name="off_day_name"]').focus();
		}

		const addEmployee = (id, name) => {
			let elem = $form.find('#employeeList').find('tbody').find(`.employeeItem[data-id="${id}"]`);

			if(elem.length > 0) {
				return false;
			} else {
				let html = $('#employeeItemTemplate').text()
							.replaceAll(/{id}/g, id)
							.replaceAll(/{name}/g, name)

				$form.find('#employeeList').find('tbody').append(html);
				employeeListEmptyCheck()
				renderEventForChooseEmployee()

				return true;
			}
		}

		const employeeListEmptyCheck = () => {
			if($('#employeeList').find('.employeeItem').length == 0) {
				if($('#employeeList').find('.empty').length == 0) {
					let html = `<tr class="empty">
									<td align="center"><i> Kosong </i></td>
								</tr>`;
					$form.find('#employeeList').find('tbody').append(html);
				}
			} else {
				$form.find('#employeeList').find('tbody').find('.empty').remove();
			}
		}

		const renderEventForChooseEmployee = () => {
			$form.find('.addEmployeeBtn').off('click')
			$form.find('.addEmployeeBtn').on('click', function(){
				const id = $('[name="id_employee"]').val();
				const name =  $('[name="id_employee"]').find(`option[value="${id}"]`).data('name');

				if(!isEmpty(id) && !isEmpty(name)) {
					const isSuccess = addEmployee(id, name);
					if(!isSuccess) {
						alert(`${name} sudah termasuk kedalam list`)
					} else {
						$('[name="id_employee"]').val('').trigger('change');
					}
				}
			})

			$form.find('.addActiveEmployeesBtn').off('click')
			$form.find('.addActiveEmployeesBtn').on('click', function(){
				@foreach(\App\Models\Employee::getActiveEmployees() as $employee)
				addEmployee(`{{ $employee->id }}`, `{{ $employee->employee_name }} - {{ $employee->departmentName() }}`)
				@endforeach
			})

			$form.find('.remove').off('click')
			$form.find('.remove').on('click', function(){
				$(this).parents('tr').remove();
				renderEventForChooseEmployee()
				employeeListEmptyCheck();
			})
		}

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			const target = $form.find(`[name="target"]`).val();
			if(target == 'selected') {
				if($form.find('.employeeItem').length == 0) {
					warningNotification('Peringatan', 'Pilih dan tambah minimal 1 karyawan')
					return false;
				}
			}


			let formData = $(this).serialize();
			$submitBtn.ladda('start')
			ajaxSetup();
			$.ajax({
				url : `{{ route('off_day.update', $offDay->id) }}`,
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

		$form.find(`[name="target"]`).on('change', function(){
			const target = $(this).val()

			if(target == 'selected') {
				const html = $('#choose-employee-template').text()
				$('#choose-employee').html(html)
				$form.find(`[name="id_employee"]`).select2({
					'placeholder': '- Pilih Karyawan -'
				})
				$form.find(`[name="id_employee"]`).val('').trigger('change')
				renderEventForChooseEmployee()
				employeeListEmptyCheck()
			} else {
				$('#choose-employee').html('')
			}
		})

		init();

		$form.find(`[name="target"]`).val(`{{ $offDay->target }}`).trigger('change')

		setTimeout(() => {
			@if($offDay->target == 'selected')
			@foreach($offDay->offDayDetails as $detail)

			@if($employee = $detail->employee)
			addEmployee(`{{ $employee->id }}`, `{{ $employee->employee_name }}`)
			@endif

			@endforeach
			@endif
		}, 500)

	});
</script>

<script type="text/html" id="choose-employee-template">
	<div class="card support-pane-card">
		<div class="card-body">
			{!! Template::titleBanner('Pilih Karyawan') !!}

			<div class="form-group">
				<label> Pilih Karyawan {!! \Setting::required() !!} </label> <br>
				<label> 
					<a href="javascript:void(0);" class="addActiveEmployeesBtn"> Pilih semua karyawan aktif </a>
				</label>
				<div class="mb-2">
					<table style="width: 100%;" border="0">
						<tr>
							<td>
								<select name="id_employee" style="width: 100%;">

									@foreach(\App\Models\Employee::getActiveEmployees() as $employee)

									<option value="{{ $employee->id }}" data-name="{{ $employee->employee_name }} - {{ $employee->departmentName() }}"> {{ $employee->employee_name }} - {{ $employee->departmentName() }} </option>

									@endforeach

								</select>
							</td>
							<td width="50">
								<button class="btn btn-success px-2 addEmployeeBtn" type="button">
									<i class="mdi mdi-plus"></i> Tambah
								</button>
							</td>
						</tr>
					</table>
				</div>
				<div style="max-height: 300px; overflow-y: auto;" class="border px-2 py-1 rounded">
					<table class="table table-hover" id="employeeList">
						<tbody>
									
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</script>

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
@endsection