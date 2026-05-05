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
						<label> 
							<a href="javascript:void(0);" class="addActiveEmployeesBtn"> Pilih semua karyawan aktif </a>
						</label>
						<div class="mb-2">
							<table style="width: 100%;" border="0">
								<tr>
									<td>
										<select name="id_employee" style="width: 100%;">

											@foreach(\App\Models\Employee::getActiveEmployees() as $employee)

											@if(empty($employee->salesEmployee))
											<option value="{{ $employee->id }}" data-name="{{ $employee->employee_name }} - {{ $employee->departmentName() }}"> {{ $employee->employee_name }} - {{ $employee->departmentName() }} </option>
											@endif

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


<script type="text/javascript">
	$(function(){

		const $form = $('#mainForm')
		const $submitBtn = $form.find('[type="submit"]').ladda();
		const $employeeListElem = $('#employeeList').find('tbody');

		const resetForm = () => {
			$form[0].reset();
			$employeeListElem.html('');
			employeeListEmptyCheck()
		}

		const init = () => {
			resetForm();
			$form.find('[name="id_employee"]').select2({
				placeholder : '- Pilih Karyawan -',
			}).trigger('change')
			$form.find('[name="id_employee"]').val('').trigger('change')
		}


		const addEmployee = (id, name) => {
			let elem = $employeeListElem.find(`.employeeItem[data-id="${id}"]`);

			if(elem.length > 0) {
				return false;
			} else {
				let html = $('#employeeItemTemplate').text()
							.replaceAll(/{id}/g, id)
							.replaceAll(/{name}/g, name)

				$employeeListElem.append(html);
				employeeListEmptyCheck()
				renderEvent()

				return true;
			}
		}

		const employeeListEmptyCheck = () => {
			if($('#employeeList').find('.employeeItem').length == 0) {
				if($('#employeeList').find('.empty').length == 0) {
					let html = `<tr class="empty">
									<td align="center"><i> Kosong </i></td>
								</tr>`;
					$employeeListElem.append(html);
				}
			} else {
				$employeeListElem.find('.empty').remove();
			}
		}

		const renderEvent = () => {
			$form.find('.remove').off('click')
			$form.find('.remove').on('click', function(){
				$(this).parents('tr').remove();
				renderEvent()
				employeeListEmptyCheck();
			})
		}


		$form.on('submit', function(e){
			e.preventDefault();

			let formData = $(this).serialize();

			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('sales_employee.store') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				resetForm();
				// $submitBtn.ladda('stop');
				setTimeout(() => {
					window.location.reload();
				}, 1000)
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});


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

		$form.find('.addActiveEmployeesBtn').on('click', function(){
			@foreach(\App\Models\Employee::getActiveEmployees() as $employee)
			addEmployee(`{{ $employee->id }}`, `{{ $employee->employee_name }} - {{ $employee->departmentName() }}`)
			@endforeach
		})


		init();
	});
</script>
@endsection