@extends('template.backLayout')


@section('style')
<link rel="stylesheet" type="text/css" href="{{ url('css/step-wizard.css') }}">
@endsection


@section('content')
@include('payroll.step_wizard')

<div class="row">
	<div class="col-md-6">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> {{ $subtitle }} </h4>
				</div>

				<form id="mainForm">
					{!! \Setting::requiredBanner() !!}

					<div class="row mb-4">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Periode Awal {!! \Setting::required() !!} </label>
								<input type="date" name="period_start" class="form-control" required>
								<span class="invalid-feedback"></span>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Periode Akhir {!! \Setting::required() !!} </label>
								<input type="date" name="period_end" class="form-control" required>
								<span class="invalid-feedback"></span>
							</div>
						</div>

						<div class="col-lg-12">
							<label style="font-size: 10pt;">
								<a href="javascript:void(0);" data-start="{{ \Helper::lastMonth()->startDate }}" data-end="{{ \Helper::lastMonth()->endDate }}" class="periodOption"> 
									{{ \Helper::lastMonth()->monthName }} {{ \Helper::lastMonth()->year }} 
								</a>
								|
								<a href="javascript:void(0);" data-start="{{ \Helper::thisMonth()->startDate }}" data-end="{{ \Helper::thisMonth()->endDate }}" class="periodOption"> 
									{{ \Helper::thisMonth()->monthName }} {{ \Helper::lastMonth()->year }}
								</a>
							</label>
						</div>
					</div>

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

											<option value="{{ $employee->id }}" data-name="{{ $employee->employee_name }} - {{ $employee->departmentName() }}"> {{ $employee->employee_name }} </option>

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

					<hr>

					<div class="form-group">
						<button class="btn btn-primary" type="submit">
							Lanjut <i class="mdi mdi-chevron-right"></i> 
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


<script type="text/javascript">
	$(function(){

		let form = $('#mainForm')
		let submitBtn = form.find('[type="submit"]').ladda();

		const resetForm = () => {
			form[0].reset();
		}

		const init = () => {
			resetForm();
			$('[name="id_employee"]').select2({
				placeholder : '- Pilih Karyawan -',
			}).trigger('change')
			$('[name="id_employee"]').val('').trigger('change')
		}

		const employeeListElem = $('#employeeList').find('tbody');

		const addEmployee = (id, name) => {
			let elem = employeeListElem.find(`.employeeItem[data-id="${id}"]`);

			if(elem.length > 0) {
				return false;
			} else {
				let html = $('#employeeItemTemplate').text()
							.replaceAll(/{id}/g, id)
							.replaceAll(/{name}/g, name)

				employeeListElem.append(html);
				employeeListEmptyCheck()
				renderedEvent()

				return true;
			}
		}

		const employeeListEmptyCheck = () => {
			if($('#employeeList').find('.employeeItem').length == 0) {
				if($('#employeeList').find('.empty').length == 0) {
					let html = `<tr class="empty">
									<td align="center"><i> Kosong </i></td>
								</tr>`;
					employeeListElem.append(html);
				}
			} else {
				employeeListElem.find('.empty').remove();
			}
		}

		const renderedEvent = () => {
			$('.remove').off('click')
			$('.remove').on('click', function(){
				$(this).parents('tr').remove();
				renderedEvent()
				employeeListEmptyCheck()
			})
		}


		form.on('submit', function(e){
			e.preventDefault();

			let formData = $(this).serialize();

			submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('payroll.xhr_chooose_period_and_employees') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				let { message, code, route } = response

				if(code == 200) {
					submitBtn.ladda('stop')
					window.location.href = route
				}
			})
			.fail(error => {
				submitBtn.ladda('stop')

				let { status, responseJSON } = error
				let { message, errors } = responseJSON

				if(status == 422) {
					invalidResponse(form, errors)
				}

				toastrAlert();
				toastr.warning(message, 'Peringatan');
			})
		});


		$('.addEmployeeBtn').on('click', function(){
			const id = $('[name="id_employee"]').val();
			const name =  $('[name="id_employee"]').find(`option[value="${id}"]`).data('name');

			if(!isEmpty(id) && !isEmpty(name)) {
				const isSuccess = addEmployee(id, name);
				console.log(isSuccess);
				if(!isSuccess) {
					alert(`${name} sudah termasuk kedalam list`)
				} else {
					$('[name="id_employee"]').val('').trigger('change');
				}
			}
		})

		$('.addActiveEmployeesBtn').on('click', function(){
			@foreach(\App\Models\Employee::getActiveEmployees() as $employee)
			addEmployee(`{{ $employee->id }}`, `{{ $employee->employee_name }} - {{ $employee->departmentName() }}`)
			@endforeach
		})


		employeeListEmptyCheck()


		init();

		@if(isset($_GET['employee_list']))
		<?php 
			$IDs = [];
			foreach(explode(',', $_GET['employee_list']) as $id) {
				if(!empty(trim($id))) $IDs[] = $id;
			}
			$employees = \App\Models\Employee::whereIn('id', $IDs)->get();
		?>
		@foreach($employees as $employee)
		addEmployee(`{{ $employee->id }}`, `{{ $employee->employee_name }} - {{ $employee->departmentName() }}`)
		@endforeach
		@endif

		@if(isset($_GET['period_start']))
		$('[name="period_start"]').val(`{{ $_GET['period_start'] }}`)
		@endif

		@if(isset($_GET['period_end']))
		$('[name="period_end"]').val(`{{ $_GET['period_end'] }}`)
		@endif

		$('.periodOption').on('click', function(){
			let start = $(this).data('start');
			let end = $(this).data('end');

			$('[name="period_start"]').val(start)
			$('[name="period_end"]').val(end)
		});
	});
</script>
@endsection