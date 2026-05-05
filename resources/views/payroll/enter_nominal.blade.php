@extends('template.backLayout')


@section('style')
<link rel="stylesheet" type="text/css" href="{{ url('css/step-wizard.css') }}">

<style type="text/css">
	
	.allowanceItem,
	.cutItem {
		border: 1px solid #e2e2e2;
		padding: 9px;
		border-radius: 7px;
		position: relative;
		margin-bottom: 1rem;
	}

	.allowanceClose,
	.cutClose {
		position: absolute;
		background: #F1635F;
		padding: 4px;
		text-align: center;
		display: block;
		top: -5px;
		right: -5px;
		border-radius: 3px;
		color: white;
		font-size: 8pt;
		cursor: pointer;
	}

	.table td {
		vertical-align: top !important;
	}

</style>
@endsection


@section('content')
@include('payroll.step_wizard')

<div class="row">
	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> {{ $subtitle }} </h4>
				</div>

				<form id="mainForm">
					{!! \Setting::requiredBanner() !!}

					<div class="table-responsive">
						<table class="table table-hover table-bordered table-md table-vertical-middle">

							@foreach($employees as $employee)
							<?php 
								$worktime = $employee->getWorkTimeByDateRange($_GET['period_start'], $_GET['period_end']);
							?>
							<tbody class="employeeItem">
								<tr>

									<td rowspan="2"> 
										<label class="d-block"><b> Nama Karyawan </b></label>
										{{ $employee->employee_name }} ({{ $employee->departmentName() }})
										<input type="hidden" name="id_employee[]" value="{{ $employee->id }}">
									</td>

									<td class="worktimeHours" data-total-percentage="{{ $worktime->total_of_percentage }}" data-amount-work-day="{{ $worktime->amount_of_work_day }}" data-percentage-late="{{ $worktime->total_of_late_percentage }}">
										<label class="d-block"><b> Persentase Kehadiran </b></label>
										{{ $worktime->percentage_of_attendance }} %
									</td>
									
									<td>
										<label class="d-block"><b> Gaji Pokok {!! \Setting::required() !!} </b></label>
										<input type="number" class="form-control basicSalary" name="basic_salary[]" placeholder="Gaji Pokok" value="{{ $employee->employeeSalary ? $employee->employeeSalary->basic_salary : 0 }}" required>
									</td>

									<td>
										<label class="d-block"><b> Gaji Harian </b></label>
										<span class="dailyRateSalary">
											Rp 0
										</span>
									</td>
									
									<td>
										<label class="d-block"><b> Tunjangan </b></label>
										<div class="allowanceList" data-employee="{{ $employee->id }}">
											
										</div>
										<a href="javascript:void(0);" class="addAllowanceBtn" data-employee="{{ $employee->id }}">
											<i class="mdi mdi-plus"></i> Tambah
										</a>
									</td>

									<td>
										<label class="d-block"><b> Potongan </b></label>
										<div class="cutList" data-employee="{{ $employee->id }}">
											
										</div>
										<a href="javascript:void(0);" class="addCutBtn" data-employee="{{ $employee->id }}">
											<i class="mdi mdi-plus"></i> Tambah
										</a>
									</td>

									<td>
										<label class="d-block"><b> Total Gaji </b></label>
										<span class="salaryTotal"> Rp 0 </span>
									</td>
								</tr>

								<tr>
									<td class="overtimeHours" data-hour="{{ $worktime->overtime }}">
										<label class="d-block"><b> Jam Lembur </b></label>
										<input type="number" class="form-control overtimeHour" name="overtime_hour[]" placeholder="Jam Lembur" value="{{ $worktime->overtime ?? 0 }}" required>
									</td>

									<td>
										<label class="d-block"><b> Upah Lembur Per Jam {!! \Setting::required() !!} </b></label>
										<input type="number" class="form-control overtimePay" name="overtime_pay[]" placeholder="Upah Lembur / Jam" value="{{ $employee->employeeSalary ? $employee->employeeSalary->overtime_pay : 0 }}" required>
										<input type="hidden" value="40000" name="total_overtime_pay[]" class="totalOvertimePay">
									</td>

									<td>
										<label class="d-block"><b> Bonus {!! \Setting::required() !!} </b></label>
										<input type="number" class="form-control bonus" name="bonus[]" placeholder="Bonus" value="0" required>
										<input type="hidden" name="total_salary[]" class="totalSalary">
									</td>

									<td>
										<label><b> Denda Terlambat / Alfa </b></label>
										<select class="form-control lateCharge" name="late_charge[]" required>
											<option value="1"> Ya </option>
											<option value="0"> Tidak </option>
										</select>
									</td>


									<td colspan="2">
										<label><b> Catatan </b></label>
										<textarea class="form-control" name="notes[]" placeholder="Catatan untuk karyawan"></textarea>
									</td>
								</tr>
								
							</tbody>
							@endforeach
						</table>
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


@section('modal')
<div class="modal fade" id="addAllowanceModal" role="dialog" aria-hidden="true">
	<div class="modal-dialog mt-4" role="document">
		<div class="modal-content">
			<form id="addAllowanceForm">

				<div class="modal-header">
					<h5 class="modal-title"> Tambah Tunjangan </h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">
					<input type="hidden" name="id_employee">
					<div class="form-group">
						<label> Nama Tunjangan </label>
						<input type="text" name="allowance_name" class="form-control" placeholder="Nama Tunjangan" required>
					</div>

					<div class="form-group">
						<label> Nominal Tunjangan </label>
						<input type="number" name="allowance_nominal" class="form-control" min="0" placeholder="Nominal Tunjangan" required>
					</div>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">
						<i class="mdi mdi-plus"></i> Tambah
					</button>
					<button type="button" class="btn btn-light" data-dismiss="modal">
						<i class="mdi mdi-close"></i> Tutup
					</button>
				</div>

			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="addCutModal" role="dialog" aria-hidden="true">
	<div class="modal-dialog mt-4" role="document">
		<div class="modal-content">
			<form id="addCutForm">

				<div class="modal-header">
					<h5 class="modal-title"> Tambah Potongan </h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">
					<input type="hidden" name="id_employee">
					<div class="form-group">
						<label> Nama Potongan </label>
						<input type="text" name="cut_name" class="form-control" placeholder="Nama Potongan" required>
					</div>

					<div class="form-group">
						<label> Nominal Potongan </label>
						<input type="number" name="cut_nominal" class="form-control" min="0" placeholder="Nominal Potongan" required>
					</div>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">
						<i class="mdi mdi-plus"></i> Tambah
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

		let form = $('#mainForm')
		let submitBtn = form.find('[type="submit"]').ladda();
		let addAllowanceForm = $('#addAllowanceForm');
		let addAllowanceSubmitBtn = addAllowanceForm.find(`[type="submit"]`).ladda();
		let addCutForm = $('#addCutForm');
		let addCutSubmitBtn = addCutForm.find(`[type="submit"]`).ladda();


		const calculate = () => {
			let employeeItems = $('.employeeItem');

			$.each(employeeItems, (i, employeeItem) => {
				employeeItem = $(employeeItem)
				let basicSalary = or(employeeItem.find('.basicSalary').val(), 0);
				let totalPercentage = or(employeeItem.find('.worktimeHours').data('total-percentage'), 0);
				let percentageOfLate = or(employeeItem.find('.worktimeHours').data('percentage-late'), 0);
				let amountDayWork = or(employeeItem.find('.worktimeHours').data('amount-work-day'), 0);
				let bonus = or(employeeItem.find('.bonus').val(), 0);
				let lateCharge = or(employeeItem.find('.lateCharge').val(), 0);
				let allowance = 0;
				let cut = 0;
				let overtimeHour = or(employeeItem.find('.overtimeHour').val(), 0);
				let overtimePay = or(employeeItem.find('.overtimePay').val(), 0);
				let totalOvertimePay = overtimeHour * overtimePay;

				basicSalary = parseFloat(basicSalary);
				totalPercentage = parseFloat(totalPercentage);
				percentageOfLate = parseFloat(percentageOfLate);
				amountDayWork = parseFloat(amountDayWork);
				bonus = parseFloat(bonus);

				let allowances = employeeItem.find('.allowance-item');
				if(allowances.length > 0) {
					$.each(allowances, (i, allowanceElem) => {
						let nominal = or($(allowanceElem).data('nominal'), 0);
						allowance += parseInt(nominal);
					})
				}

				let cuts = employeeItem.find('.cut-item');
				if(cuts.length > 0) {
					$.each(cuts, (i, cutElem) => {
						let nominal = or($(cutElem).data('nominal'), 0);
						cut += parseInt(nominal);
					})
				}

				let dailyRateSalary = basicSalary / amountDayWork;
				dailyRateSalary = parseInt(dailyRateSalary)
				dailyRateSalary = Number.isNaN(dailyRateSalary) ? 0 : dailyRateSalary;
				employeeItem.find('.dailyRateSalary').html(`Rp ${numberFormat(dailyRateSalary)}`);
				let salary = 0

				if(lateCharge == 1) {
					salary = basicSalary * totalPercentage / amountDayWork;
				} else {
					salary = basicSalary;
				}

				let salaryTotal = salary + allowance - cut + bonus + totalOvertimePay;
				salaryTotal = parseInt(Math.ceil(salaryTotal))
				employeeItem.find('.salaryTotal').html(`Rp ${numberFormat(salaryTotal)}`);
				employeeItem.find('.totalSalary').val(salaryTotal);
				employeeItem.find('.totalOvertimePay').val(totalOvertimePay);
			})
		}


		const addAllowance = (employeeID, name, nominal) => {
			let list = $(`.allowanceList[data-employee="${employeeID}"]`);

			if(list.length > 0) {
				let html = $('#allowanceItemTemplate').text()
							.replaceAll(/{id_employee}/g, employeeID)
							.replaceAll(/{name}/g, name)
							.replaceAll(/{nominal}/g, nominal)
							.replaceAll(/{nominal_text}/g, `Rp. ${numberFormat(nominal)}`)
				list.append(html);

				renderedEvent();
				calculate();
			}
		}


		const addCut = (employeeID, name, nominal) => {
			let list = $(`.cutList[data-employee="${employeeID}"]`);

			if(list.length > 0) {
				let html = $('#cutItemTemplate').text()
							.replaceAll(/{id_employee}/g, employeeID)
							.replaceAll(/{name}/g, name)
							.replaceAll(/{nominal}/g, nominal)
							.replaceAll(/{nominal_text}/g, `Rp. ${numberFormat(nominal)}`)
				list.append(html);

				renderedEvent();
				calculate();
			}
		}


		const clearAddAllowance = () => {
			addAllowanceForm[0].reset();
		}


		const clearAddCut = () => {
			addCutForm[0].reset();
		}


		form.on('submit', function(e){
			e.preventDefault();

			let formData = $(this).serialize();

			submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{!! route('payroll.xhr_enter_nominal', [ 'period_start' => $_GET['period_start'], 'period_end' => $_GET['period_end'] ]) !!}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				let { message, code, route } = response

				if(code == 200) {
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
		

		calculate()

		$('.basicSalary, .overtimePay, .bonus, .overtimeHour').on('keyup', function(){
			calculate();
		})

		$('.lateCharge').on('change', function(){
			calculate();
		})


		$('#addAllowanceModal').on('shown.bs.modal', function(){
			$(this).find('[name="allowance_name"]').focus()
		})


		$('#addCutModal').on('shown.bs.modal', function(){
			$(this).find('[name="cut_name"]').focus()
		})


		addAllowanceForm.on('submit', function(e) {
			e.preventDefault();

			let data = $(this).serializeArray();
			let [ id_employee, allowance_name, allowance_nominal ] = data

			addAllowance(id_employee.value, allowance_name.value, allowance_nominal.value);
			clearAddAllowance();
			$('#addAllowanceModal').modal('hide');
		});


		addCutForm.on('submit', function(e) {
			e.preventDefault();

			let data = $(this).serializeArray();
			let [ id_employee, cut_name, cut_nominal ] = data

			addCut(id_employee.value, cut_name.value, cut_nominal.value);
			clearAddCut();
			$('#addCutModal').modal('hide');
		});


		const renderedEvent = () => {
			$('.remove').off('click')
			$('.remove').on('click', function(){
				$(this).parents('.remove-target').remove();
				renderedEvent();
				calculate();
			})

			$('.addAllowanceBtn').off('click')
			$('.addAllowanceBtn').on('click', function(){
				let employeeID = $(this).data('employee');

				$('#addAllowanceModal').find(`[name="id_employee"]`).val(employeeID);
				$('#addAllowanceModal').modal('show');
			})

			$('.addCutBtn').off('click')
			$('.addCutBtn').on('click', function(){
				let employeeID = $(this).data('employee');

				$('#addCutModal').find(`[name="id_employee"]`).val(employeeID);
				$('#addCutModal').modal('show');
			})
		}

		renderedEvent();

		@foreach($employees as $employee)
			@if($employee->isHasSalary())

				@if($employee->employeeSalary->isHasAllowances())
					@foreach($employee->employeeSalary->employeeSalaryAllowances as $allowance)
					addAllowance(`{{ $employee->id }}`, `{{ $allowance->allowance_name }}`, `{{ $allowance->allowance_nominal }}`)
					@endforeach
				@endif

				@if($employee->employeeSalary->isHasAllowances())
					@foreach($employee->employeeSalary->employeeSalaryCuts as $cut)
					addCut(`{{ $employee->id }}`, `{{ $cut->cut_name }}`, `{{ $cut->cut_nominal }}`)
					@endforeach
				@endif

			@endif
		@endforeach

	});
</script>


<script type="text/html" id="allowanceItemTemplate">
	<div class="allowanceItem remove-target">
		<span> {name} ({nominal_text}) </span>
		<input type="hidden" name="allowance_item[{id_employee}][]" class="allowance-item" value="{name}###{nominal}" data-nominal="{nominal}">
		<span class="allowanceClose remove">
			<i class="mdi mdi-close"></i>
		</span>
	</div>
</script>

<script type="text/html" id="cutItemTemplate">
	<div class="cutItem remove-target">
		<span> {name} ({nominal_text}) </span>
		<input type="hidden" name="cut_item[{id_employee}][]" class="cut-item" value="{name}###{nominal}" data-nominal="{nominal}">
		<span class="cutClose remove">
			<i class="mdi mdi-close"></i>
		</span>
	</div>
</script>
@endsection