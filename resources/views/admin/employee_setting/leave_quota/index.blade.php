@extends('template.backLayout')


@section('action')
<button class="btn btn-primary exportBtn">
	<i class="mdi mdi-download"></i> Export
</button>
@if(UserPermission::check('employee_leave_quota', 'c'))
<a href="{{ route('employee_leave_quota.create') }}" class="btn btn-success">
	<i class="mdi mdi-plus-thick"></i> Buat
</a>
@endif

@endsection


@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					{!! Template::titleBanner($title) !!}
				</div>

				<div class="table-responsive support-pane">
					<table class='table table-bordered table-hover' id="dataTable">
						<thead>
							<tr>
								<td> Nama Karyawan </td>
								<td> Tipe Periode </td>
								<td> Jatah Cuti </td>
								<td> Potongan Cuti Bersama </td>
								<td> Sisa Jatah Cuti (Belum Potong Cuti Bersama) </td>
								<td> Akumulasi </td>
								<th width="100px"> Aksi </td>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td> Nama Karyawan </td>
								<td> Tipe Periode </td>
								<td> Jatah Cuti </td>
								<td> Potongan Cuti Bersama </td>
								<td> Sisa Jatah Cuti (Belum Potong Cuti Bersama) </td>
								<td> Akumulasi </td>
								<th width="100px"> Aksi </td>
							</tr>
						</tfoot>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>
@endsection


@section('modal')
<div class="modal fade" id="exportModal" role="dialog" aria-hidden="true">
	<div class="modal-dialog mt-4" role="document">
		<div class="modal-content">
			<form id="exportForm">

				<div class="modal-header">
					<h5 class="modal-title">
						Export
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">




					<div class="form-group">
						<label> Departemen </label>
						<select name="id_department" style="width: 100%;" required>
							<option value="all"> - Semua Departemen - </option>

							@foreach(\App\Models\Department::all() as $department)
							<option value="{{ $department->id }}">
								{{ $department->department_name }}
							</option>
							@endforeach

						</select>
					</div>

					<div class="form-group">
						<label> Jabatan </label>
						<select name="id_position" style="width: 100%;" required>
							<option value="all"> - Semua Jabatan - </option>
						</select>
					</div>

					<div class="form-group">
						<label> Grup Karyawan </label>
						<select name="id_employee_group" style="width: 100%;" required>
							<option value="all"> - Semua Grup Karyawan - </option>

							@foreach(\App\Models\EmployeeGroup::all() as $employeeGroup)
							<option value="{{ $employeeGroup->id }}">
								{{ $employeeGroup->group_name }}
							</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label> Karyawan </label>
						<select class="form-control" name="id_employee" style="width: 100%;" required>
							<option value="all"> - Semua Karyawan - </option>
						</select>
					</div>

					<div class="form-group">
						<label> Aksi </label>
						<select class="form-control" name="action">
							<option value="pdf_stream"> Preview Report PDF </option>
							<option value="pdf_download"> Download Report PDF </option>
							<option value="xlsx_download"> Download Report Excel </option>
						</select>
					</div>

				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">
						<i class="mdi mdi-download"></i> Export
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

		let $exportModal = $('#exportModal');
		let $exportForm = $('#exportForm');
		let $exportFormBtn = $exportForm.find(`[type="submit"]`).ladda();

		$exportForm.find('[name="id_department"]').select2({
			'placeholder' : '- Pilih Departemen -'
		})
		$exportForm.find('[name="id_department"]').val(`{{ $_GET['id_department'] ?? 'all' }}`).trigger('change')

		$exportForm.find('[name="id_position"]').select2({
			'placeholder' : '- Pilih Jabatan -'
		})
		$exportForm.find('[name="id_position"]').val(`{{ $_GET['id_position'] ?? 'all' }}`).trigger('change')

		$exportForm.find('[name="id_employee_group"]').select2({
			'placeholder' : '- Pilih Grup Karyawan -'
		})
		$exportForm.find('[name="id_employee_group"]').val(`{{ $_GET['id_employee_group'] ?? 'all' }}`).trigger('change')

		$exportForm.find('[name="id_employee"]').select2({
			'placeholder' : '- Pilih Karyawan -'
		})
		$exportForm.find('[name="id_employee"]').val(`{{ $_GET['id_employee'] ?? 'all' }}`).trigger('change')

		$('.exportBtn').on('click', function(){
			$exportModal.modal('show')
		})

		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('employee_leave_quota') }}"
			},
			columns : [
				{
					data : 'employee.employee_name',
					name : 'employees.employee_name'
				},
				{
					data : 'period_type',
					name : 'period_type'
				},
				{
					data : 'quota',
					name : 'quota'
				},
				{
					data : 'mass_leave_cut',
					name : 'mass_leave_cut'
				},
				{
					data : 'quota_available',
					name : 'quota_available'
				},
				{
					data : 'is_allow_accumulation',
					name : 'is_allow_accumulation',
					searchable : false,
				},
				{
					data : 'action',
					name : 'action',
					orderable : false,
					searchable : false,
				},
			],
			drawCallback : settings => {
				renderedEvent();
			}
		})

		const reloadDT = () => {
			$('#dataTable').DataTable().ajax.reload();
		}

		const renderedEvent = () => {
			$('.delete').off('click')
			$('.delete').on('click', function(){
				let href = $(this).data('href')

				confirmation('Yakin ingin dihapus?', () => {
					ajaxSetup();
					$.ajax({
						url : href,
						method : 'delete',
					})
					.done(response => {
						reloadDT();
						ajaxSuccessHandling(response)
					})
					.fail(error => {
						ajaxErrorHandling(error)
					})
				})
			});
		}

		const generatePositionOptions = () => {
			let departmentId = $exportForm.find(`[name="id_department"]`).val()
			let html = '';

			html += `<option value="all"> - Semua Jabatan - </option>`;
			if(departmentId != 'all') {
				$.get({
					url: `{{ route('helper.get_positions') }}?id_department=${departmentId}`,
					dataType: 'json'
				})
				.done(response => {
					const { positions } = response
					positions.forEach(position => {
						html += `<option value="${position.id}"> ${position.position_name} </option>`
					})

					$exportForm.find(`[name="id_position"]`).html(html)
				})
			} else {
				$exportForm.find(`[name="id_position"]`).html(html)
			}
		}

		const generateEmployeesOptions = () => {
			let departmentId = $exportForm.find(`[name="id_department"]`).val()
			let positionId = $exportForm.find(`[name="id_position"]`).val()
			let employeeGroupId = $exportForm.find(`[name="id_employee_group"]`).val()
			let html = `<option value="all"> - Semua Karyawan - </option>`;

			$.get({
				url: `{{ route('helper.get_employees') }}?id_department=${departmentId}&id_position=${positionId}&id_employee_group=${employeeGroupId}`,
				dataType: 'json'
			})
			.done(response => {
				const { employees } = response
				employees.forEach(employee => {
					html += `<option value="${employee.id}"> ${employee.employee_name} </option>`
				})

				$exportForm.find(`[name="id_employee"]`).html(html)
			})
		}

		$exportForm.find(`[name="id_department"]`).on('change', function(){
			generatePositionOptions()
			generateEmployeesOptions()
		})

		$exportForm.find(`[name="id_position"]`).on('change', function(){
			generateEmployeesOptions()
		})

		$exportForm.find(`[name="id_employee_group"]`).on('change', function(){
			generateEmployeesOptions()
		})

		$exportForm.on('submit', function(e){
			e.preventDefault();

			const formData = $(this).serialize()
			window.open(`{{ route('employee_leave_quota.export') }}?${formData}`)
		})


	});
</script>
@endsection
