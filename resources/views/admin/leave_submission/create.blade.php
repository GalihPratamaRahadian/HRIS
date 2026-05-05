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
						<label> Target {!! Template::required() !!} </label>
						<select class="form-control" name="target" required>
							<option value="all"> Semua Karyawan </option>
							<option value="selected"> Pilih Karyawan </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Alasan {!! \Setting::required() !!} </label>
						<select name="id_leave_reason" style="width: 100%;" required>

							@foreach(\App\Models\LeaveReason::all() as $leaveReason)
							<option value="{{ $leaveReason->id }}"> {{ $leaveReason->reasonWithDurationText() }} </option>
							@endforeach

						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Awal {!! \Setting::required() !!} </label>
						<input type="date" name="start_date" class="form-control" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Akhir {!! \Setting::required() !!} </label>
						<input type="date" name="end_date" class="form-control" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Deskripsi </label>
						<textarea class="form-control" name="description" rows="2" placeholder="Deskripsi"></textarea>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Lampiran File </label>
						<input type="file" name="file_attachment" class="form-control">
						<span class="invalid-feedback"></span>
					</div>

					<hr>

					<div class="form-group">
						<label> Persetujuan Cuti {!! \Setting::required() !!} </label>
						<select name="submission_approval_status" class="form-control" required>
							<option selected disabled> - Pilih - </option>
							<option value="Approve"> Langsung Setujui </option>
							<option value="Waiting"> Menunggu Persetujuan Atasan </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group" id="send-notification" style="display: none;">
						<label> Kirim Notifikasi Telah Disetujui Ke Karyawan Terkait {!! \Setting::required() !!} </label>
						<select name="send_notification" class="form-control" required>
							<option value="Ya"> Kirim Notifikasi </option>
							<option value="Tidak"> Tidak Perlu Kirim  </option>
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

		const resetForm = () => {
			$form[0].reset();
		}

		const init = () => {
			resetForm();
			$form.find('[name="target"]').val('all').trigger('change');
			$form.find('[name="id_leave_reason"]').select2({
                placeholder: '- Pilih Alasan -'
            })
            $form.find('[name="id_leave_reason"]').val('').trigger('change')

            $form.find(`[name="submission_approval_status"]`).on('change', function(){
                const status = $(this).val()

                if(status == 'Approve') {
                    $form.find('#send-notification').show()
                } else {
                    $form.find('#send-notification').hide()
                }
            })
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
            $submitBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');
			ajaxSetup();
			$.ajax({
				url : `{{ route('admin.leave_submission.store') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				init();
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response)
                $submitBtn.prop('disabled', false).html('<i class="mdi mdi-check"></i> Simpan');
                setTimeout(() => {
					window.location.href = `{{ route('admin.leave_submission') }}`
				}, 1000)
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
