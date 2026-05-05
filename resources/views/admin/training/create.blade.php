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
						<label> Judul {!! Template::required() !!} </label>
						<input type="text" name="title" class="form-control" placeholder="Judul">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Nama Trainer / Provider {!! Template::required() !!} </label>
						<input type="text" name="trainer_name" class="form-control" placeholder="Nama Trainer / Provider">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Departemen </label>
						<select name="id_department" style="width: 100%">
							<option value="all"> - Semua Departemen - </option>
							@foreach(\App\Models\Department::all() as $department)
							<option value="{{ $department->id }}"> {{ $department->department_name }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Jabatan </label>
						<select name="id_position" style="width: 100%">
							<option value="all"> - Semua Jabatan - </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Grup Karyawan </label>
						<select name="id_employee_group" style="width: 100%">
							<option value="all"> - Semua Grup Karyawan - </option>
							@foreach(\App\Models\EmployeeGroup::all() as $employeeGroup)
							<option value="{{ $employeeGroup->id }}"> {{ $employeeGroup->group_name }} </option>
							@endforeach
						</select>
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

					<hr>

					<div class="form-group">
						<label> Status Publikasi {!! Template::required() !!} </label>
						<select class="form-control" name="is_published">
							<option value="Ya"> Publikasi </option>
							<option value="Tidak"> Tidak Dipublikasi </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Awal Pelaksanaan {!! Template::required() !!} </label>
						<input type="date" name="start_date" class="form-control">
						<a href="javascript:void(0);" class="clear-start-date small"> Kosongkan </a>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Akhir Pelaksanaan {!! Template::required() !!} </label>
						<p class="small text-muted mb-1"> * Status publikasi akan otomatis berubah menjadi "Tidak Dipublikasi" jika sudah melewati tanggal akhir pelaksanaan </p>
						<input type="date" name="end_date" class="form-control">
						<a href="javascript:void(0);" class="clear-end-date small"> Kosongkan </a>
						<span class="invalid-feedback"></span>
					</div>


				</div>
			</div>
		</div>

		<div class="col-lg-7">
            <div class="card support-pane-card">
                <div class="card-body">
                    {!! Template::titleBanner('Materi Training') !!}

                    <hr>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="material-table">
                            <thead>
                                <tr>
                                    <th> Jenis {!! Template::required() !!}</th>
                                    <th> Judul Materi </th>
                                    <th> Konten </th>
                                    <th> Aksi </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <hr>

                    <button class="btn btn-primary px-2 py-2 text-small add-material" type="button">
                        <i class="mdi mdi-plus-thick"></i> Tambah
                    </button>

                    <div class="mt-4" id="choose-employee"></div>
                </div>
            </div>
        </div>
	</div>

	<hr>

	<div class="form-group">
		<button class="btn btn-success" type="submit">
			<i class="mdi mdi-check"></i> Simpan
		</button>

        <div class="col-lg-6" id="choose-employee"></div>
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
			$form.find('[name="title"]').focus();
            $form.find(`[name="id_department"]`).select2({
			'placeholder': '- Pilih Departemen',
		})

		$form.find(`[name="id_position"]`).select2({
			'placeholder': '- Pilih Jabatan',
		})

		$form.find(`[name="id_employee_group"]`).select2({
			'placeholder': '- Pilih Grup Karyawan',
		})

		$form.find(`.clear-start-date`).on('click', function(){
			$form.find(`[name="start_date"]`).val('')
		})

		$form.find(`.clear-end-date`).on('click', function(){
			$form.find(`[name="end_date"]`).val('')
		})

		$form.find(`[name="id_department"]`).on('change', function(){
			const departmentId = $(this).val()
			$.get({
				url: `{{ route('helper.get_positions') }}?id_department=${departmentId}`,
				dataType: 'json'
			})
			.done(response => {
				const { positions } = response
				let html = ''
				html += `<option value="all"> - Semua Jabatan - </option>`

				positions.forEach(position => {
					html += `<option value="${position.id}"> ${position.position_name} </option>`
				})

				$form.find(`[name="id_position"]`).html(html)
				$form.find(`[name="id_position"]`).val('all').trigger('change')
			})
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


		const renderEvent = () => {
			$('#material-table').find('.material-type').off('change')
			$('#material-table').find('.material-type').on('change', function(){
				const materialType = $(this).val()
				let titleHtml = '-'
				let contentHtml = '-'
				if(materialType == 'Upload File') {
					titleHtml = `<input type="text" class="form-control" name="title_material[]" placeholder="Judul Materi">`
					contentHtml = `<input type="file" class="form-control" name="upload_file_material[]" required>`
				} else if(materialType == 'Upload Video') {
					titleHtml = `<input type="text" class="form-control" name="title_video[]" placeholder="Judul Materi">`
					contentHtml = `<input type="file" class="form-control" name="upload_file_video[]" accept="video/*" required>`
				} else if(materialType == 'Link Youtube') {
					titleHtml = `<input type="text" class="form-control" name="title_youtube[]" placeholder="Judul Materi">`
					contentHtml = `<input type="text" class="form-control" name="link_youtube[]" placeholder="https://" required>`
				}
				$(this).parents('tr').find('.title').html(titleHtml)
				$(this).parents('tr').find('.content').html(contentHtml)
			})

			$('#material-table').find('.delete').off('click')
			$('#material-table').find('.delete').on('click', function(){
				if($('#material-table').find('.material-item').length > 1) {
					$(this).parents('tr').remove()
				} else {
					alert('Minimal input 1 materi')
				}
			})
		}

		$('.add-material').on('click', function(){
			const html = $('#material-item-template').text()
			$('#material-table').find('tr.empty').remove()
			$('#material-table').find('tbody').append(html)
			renderEvent()
		})

		$('.add-material').click()

        $form.find(`[name="target"]`).on('change', function(){
            const target = $(this).val();

            if (target == 'selected') {
                const html = $('#choose-employee-template').text();
                $('#choose-employee').html(html);

                // Ambil filter yang sedang dipilih
                const id_department = $form.find(`[name="id_department"]`).val();
                const id_position = $form.find(`[name="id_position"]`).val();
                const id_employee_group = $form.find(`[name="id_employee_group"]`).val();

                // Ambil karyawan berdasarkan filter
                $.get({
                    url: `{{ route('helper.get_employees') }}`,
                    data: {
                        id_department,
                        id_position,
                        id_employee_group
                    },
                    dataType: 'json'
                })
                .done(response => {
                    const $select = $form.find(`[name="id_employee"]`);
                    $select.empty().append(`<option value="">- Pilih Karyawan -</option>`);
                    response.employees.forEach(emp => {
                        $select.append(`<option value="${emp.id}" data-name="${emp.text}">${emp.text}</option>`);
                    });
                    $select.select2({ placeholder: '- Pilih Karyawan -' });
                });

                renderEventForChooseEmployee();
                employeeListEmptyCheck();

            } else {
                $('#choose-employee').html('');
            }
        });


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

			let formData = new FormData(this);
			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('admin.training.store') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				contentType: false,
				processData: false
			})
			.done(response => {
                init();
				ajaxSuccessHandling(response);
				setTimeout(() => {
					window.location.href = `{{ route('admin.training') }}`
				}, 1000)
			})
			.fail(error => {
				$submitBtn.ladda('stop');

                let response = error.responseJSON || {};
                let message = response.message || 'Terjadi kesalahan tidak terduga';

                ajaxErrorHandling(error, $form);
			})
		});

		init();
	});
</script>

<script type="text/html" id="material-item-template">
	<tr class="material-item">
		<td>
			<select class="form-control material-type" required>
				<option value="" disabled selected> - Pilih - </option>
				<option value="Upload File"> Upload File </option>
				<option value="Upload Video"> Upload Video </option>
				<option value="Link Youtube"> Link Youtube </option>
			</select>
		</td>
		<td class="title"> - </td>
		<td class="content"> - </td>
		<td>
			<button type="button" class="btn btn-danger p-1 delete" title="Hapus materi">
				<i class="mdi mdi-close mr-0"></i>
			</button>
		</td>
	</tr>
</script>

<script type="text/html" id="choose-employee-template">
	<div class="card support-pane-card mt-4">
		<div class="card-body">
			<h5 class="mb-3">Pilih Karyawan</h5>
			<label>
				<a href="javascript:void(0);" class="addActiveEmployeesBtn">Pilih semua karyawan</a>
			</label>
			<table style="width: 100%;" class="mb-2">
				<tr>
					<td>
						<select name="id_employee" class="form-control"></select>
					</td>
					<td width="50">
						<button type="button" class="btn btn-success px-2 addEmployeeBtn">
							<i class="mdi mdi-plus"></i>
						</button>
					</td>
				</tr>
			</table>
			<div style="max-height: 300px; overflow-y: auto;" class="border rounded p-2">
				<table class="table table-hover" id="employeeList">
					<tbody></tbody>
				</table>
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
