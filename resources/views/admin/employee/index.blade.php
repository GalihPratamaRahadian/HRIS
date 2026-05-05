@extends('template.backLayout')


@section('action')
<button class="btn btn-danger filterBtn">
	<i class="mdi mdi-filter"></i> Filter
</button>

<button class="btn btn-success exportBtn">
	<i class="mdi mdi-download"></i> Export
</button>

<button data-href="{{ route('employee.push_all_to_faceterminal') }}" class="btn btn-primary push-to-faceterminal">
	<i class="mdi mdi-face-recognition"></i> Push Semua Karyawan Aktif ke Device
</button>

@if(UserPermission::check('employee', 'c'))
<a href="{{ route('employee.create') }}" class="btn btn-success">
	<i class="mdi mdi-plus-thick"></i> Tambah
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
								<th> No Karyawan </th>
								<th> Nama Karyawan </th>
								<th> Departemen </th>
								<th> Jabatan </th>
								<th> Jam Kerja </th>
								<th> Status Pekerjaan </th>
								<th> Status </th>
								<th width="80"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> No Karyawan </th>
								<th> Nama Karyawan </th>
								<th> Departemen </th>
								<th> Jabatan </th>
								<th> Jam Kerja </th>
								<th> Status Pekerjaan </th>
								<th> Status </th>
								<th width="80"> Aksi </th>
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
<div class="modal fade" id="filterModal" role="dialog" aria-hidden="true">
	<div class="modal-dialog mt-4" role="document">
		<div class="modal-content">
			<form id="filterForm">

				<div class="modal-header">
					<h5 class="modal-title">
						Filter
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					<div class="form-group">
						<label> Departemen </label>
						<select name="id_department" style="width: 100%;" required>
							<option value="all"> - Semua - </option>
							<option value="no"> - Belum Memiliki Departemen - </option>

							@foreach(\App\Models\Department::all() as $department)
							<option value="{{ $department->id }}">
								{{ $department->department_name }}
							</option>
							@endforeach

						</select>
					</div>

					<div class="form-group">
						<label> Jam Kerja </label>
						<select name="id_shift" style="width: 100%;" required>
							<option value="all"> - Semua Jam Kerja - </option>
							<option value="no"> - Belum Memiliki Jam Kerja - </option>

							@foreach(\App\Models\Shift::all() as $shift)
							<option value="{{ $shift->id }}">
								{{ $shift->shift_name }}
							</option>
							@endforeach

						</select>
					</div>

					<div class="form-group">
						<label> Grup Karyawan </label>
						<select name="id_employee_group" style="width: 100%;" required>
							<option value="all"> - Semua Grup Karyawan - </option>
							<option value="no"> - Belum Memiliki Grup Karyawan - </option>
							@foreach(\App\Models\EmployeeGroup::all() as $group)
							<option value="{{ $group->id }}">
								{{ $group->group_name }}
							</option>
							@endforeach

						</select>
					</div>

					<div class="form-group">
						<label> Status Karyawan </label>
						<select name="employee_status" style="width: 100%;" required>

							<option value="all"> - Semua - </option>
							<option value="{{ \App\Models\Employee::STATUS_ACTIVE }}"> Aktif </option>
							<option value="{{ \App\Models\Employee::STATUS_NOTACTIVE }}"> Tidak Aktif </option>

						</select>
					</div>
					
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">
						<i class="mdi mdi-filter"></i> Filter
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

		let $filterModal = $('#filterModal');

		$filterModal.find('[name="id_department"]').select2({
			'placeholder' : '- Pilih Departemen -'
		})
		$filterModal.find('[name="id_department"]').val(`{{ $_GET['id_department'] ?? 'all' }}`).trigger('change')

		$filterModal.find('[name="id_shift"]').select2({
			'placeholder' : '- Pilih Jam Kerja -'
		})
		$filterModal.find('[name="id_shift"]').val(`{{ $_GET['id_shift'] ?? 'all' }}`).trigger('change')

		$filterModal.find('[name="id_employee_group"]').select2({
			'placeholder' : '- Pilih Grup Karyawan -'
		})
		$filterModal.find('[name="id_employee_group"]').val(`{{ $_GET['id_employee_group'] ?? 'all' }}`).trigger('change')

		$filterModal.find('[name="employee_status"]').select2({
			'placeholder' : '- Pilih Status -'
		})
		$filterModal.find('[name="employee_status"]').val(`{{ $_GET['employee_status'] ?? \App\Models\Employee::STATUS_ACTIVE }}`).trigger('change')

		$('.filterBtn').on('click', function(){
			$filterModal.modal('show')
		})

		$filterModal.find('#filterForm').on('submit', function(e){
			e.preventDefault();

			$filterModal.modal('hide')
			$('#dataTable').DataTable().ajax.reload();
		})

		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('employee') }}"
			},
			columns : [
				{
					data : 'employee_number',
					name : 'employee_number'
				},
				{
					data : 'employee_name',
					name : 'employee_name'
				},
				{
					data : 'department.department_name',
					name : 'departments.department_name'
				},
				{
					data : 'position.position_name',
					name : 'positions.position_name'
				},
				{
					data : 'shift.shift_name',
					name : 'shifts.shift_name'
				},
				{
					data : 'job_status',
					name : 'job_status'
				},
				{
					data : 'status',
					name : 'status'
				},
				{
					data : 'action',
					name : 'action',
					orderable : false,
					searchable : false,
				}
			],
			drawCallback : settings => {
				renderedEvent();
			},
			preDrawCallback : settings => {
				let formData = $('#filterForm').serialize();

				settings.ajax.url = `{{ route('employee') }}?${formData}`;
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
						ajaxSuccessHandling(response);
						reloadDT();
					})
					.fail(error => {
						ajaxErrorHandling(error)
					})
				})
			});

			$('.push-to-faceterminal').off('click')
			$('.push-to-faceterminal').on('click', function(){
				let href = $(this).data('href')
				confirmation("Ingin Push Ulang ke Face Terminal?", () => {
					ajaxSetup();
					$.ajax({
						url : href,
						method : 'post',
					})
					.done(response => {
						reloadDT();
						ajaxSuccessHandling(response)
					})
					.fail(error => {
						ajaxErrorHandling(error, $form)
					})
				})
			});
		}

		$('.exportBtn').on('click', function(){
			let formData = $('#filterForm').serialize();
			window.open(`{{ route('employee.export') }}?${formData}`, '_blank')
		})


	});
</script>
@endsection