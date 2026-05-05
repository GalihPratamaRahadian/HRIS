@extends('template.backLayout')


@section('action')
<button class="btn btn-primary" data-toggle="modal" data-target="#filterModal">
	<i class="mdi mdi-filter"></i> Filter
</button>
<a class="btn btn-success" href="{{ route('employee.overtime_approval.create_submission') }}">
	<i class="mdi mdi-plus-thick"></i> Buat Pengajuan Untuk Staff
</a>
@endsection


@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">

				<div class="table-responsive support-pane">
					<table class='table table-bordered table-hover' id="dataTable">
						<thead>
							<tr>
								<th> Diajukan Pada </th>
								<th> Karyawan </th>
								<th> Departemen </th>
								<th> Jabatan </th>
								<th> Alasan </th>
								<th> Awal Lembur </th>
								<th> Selesai Lembur </th>
								<th> Jam Awal </th>
								<th> Jam Akhir </th>
								<th> Deskripsi </th>
								<th> Status Penyetujuan </th>
								<th width="90px"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Diajukan Pada </th>
								<th> Karyawan </th>
								<th> Departemen </th>
								<th> Jabatan </th>
								<th> Alasan </th>
								<th> Awal Lembur </th>
								<th> Selesai Lembur </th>
								<th> Jam Awal </th>
								<th> Jam Akhir </th>
								<th> Deskripsi </th>
								<th> Status Penyetujuan </th>
								<th width="90px"> Aksi </th>
							</tr>
						</tfoot>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>
@endsection


@section('script')
<script type="text/javascript">
	$(function(){

		const $filterForm = $('#filterForm');

		$filterForm.find(`[name="id_overtime_reason"]`).select2({
			'placeholder': '- Pilih Alasan -'
		})

		$filterForm.find(`[name="status"]`).select2({
			'placeholder': '- Pilih Status -'
		})

		$filterForm.find(`[name="id_department"]`).select2({
			'placeholder': '- Pilih Departemen -'
		})

		$filterForm.find(`[name="id_position"]`).select2({
			'placeholder': '- Pilih Jabatan -'
		})

		$filterForm.find(`[name="id_employee_group"]`).select2({
			'placeholder': '- Pilih Grup Karyawan -'
		})

		$filterForm.find(`[name="id_employee"]`).select2({
			'placeholder': '- Pilih Karyawan -'
		})

		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('employee.overtime_approval') }}"
			},
			columns : [
				{
					data : 'created_at',
					name : 'created_at'
				},
				{
					data : 'overtime_submissions.employee.employee_name',
					name : 'employees.employee_name'
				},
				{
					data : 'overtime_submissions.employee.position.position_name',
					name : 'positions.position_name',
					visible : false
				},
				{
					data : 'overtime_submissions.employee.department.department_name',
					name : 'departments.department_name',
					visible : false
				},
				{
					data : 'overtime_submission.overtime_reason.reason',
					name : 'overtime_reasons.reason'
				},
				{
					data : 'overtime_submissions.start_date',
					name : 'overtime_submissions.start_date'
				},
				{
					data : 'overtime_submissions.end_date',
					name : 'overtime_submissions.end_date'
				},
				{
					data : 'overtime_submission.clock_start',
					name : 'overtime_submissions.clock_start',
					visible : false
				},
				{
					data : 'overtime_submission.clock_end',
					name : 'overtime_submissions.clock_end',
					visible : false
				},
				{
					data : 'overtime_submissions.description',
					name : 'overtime_submissions.description'
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
			preDrawCallback: settings => {
				let formData = $filterForm.serialize()
				settings.ajax.url = `{{ route('employee.overtime_approval') }}?${formData}`;
			},
			order : [ ['0', 'desc'] ]
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
						ajaxSuccessHandling(response)
						reloadDT();
					})
					.fail(error => {
						ajaxErrorHandling(error)
					})
				})

			});

			$('.approve').off('click')
			$('.approve').on('click', function(){
				let href = $(this).data('href')

				confirmation('Yakin ingin Disetujui?', () => {
					ajaxSetup();
					$.ajax({
						url: href,
						method: 'post',
						dataType: 'json',
					})
					.done(response => {
						ajaxSuccessHandling(response)
						reloadDT();
					})
					.fail(error => {
						ajaxErrorHandling(error)
					})
				})
			})

			$('.reject').off('click')
			$('.reject').on('click', function(){

				let href = $(this).data('href')
				confirmation('Yakin ingin Ditolak?', () => {
					ajaxSetup();
					$.ajax({
						url: href,
						method: 'post',
						dataType: 'json',
					})
					.done(response => {
						ajaxSuccessHandling(response)
						reloadDT();
					})
					.fail(error => {
						ajaxErrorHandling(error)
					})
				})

			});
		}

		$filterForm.on('submit', function(e){
			e.preventDefault();
			reloadDT();
			$('#filterModal').modal('hide');
		})

		$('.set-date').on('click', function(){
			const { target, value } = $(this).data()
			$(target).val(value).trigger('change')
		})

		const generatePositionOptions = () => {
			let departmentId = $filterForm.find(`[name="id_department"]`).val()
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

					$filterForm.find(`[name="id_position"]`).html(html)
				})
			} else {
				$filterForm.find(`[name="id_position"]`).html(html)
			}
		}

		const generateEmployeesOptions = () => {
			let departmentId = $filterForm.find(`[name="id_department"]`).val()
			let positionId = $filterForm.find(`[name="id_position"]`).val()
			let employeeGroupId = $filterForm.find(`[name="id_employee_group"]`).val()
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

				$filterForm.find(`[name="id_employee"]`).html(html)
			})
		}

		$filterForm.find(`[name="id_department"]`).select2({
			'placeholder': '- Pilih Departemen -'
		})

		$filterForm.find(`[name="id_position"]`).select2({
			'placeholder': '- Pilih Jabatan -'
		})

		$filterForm.find(`[name="id_employee_group"]`).select2({
			'placeholder': '- Pilih Grup Karyawan -'
		})

		$filterForm.find(`[name="id_employee"]`).select2({
			'placeholder': '- Pilih Karyawan -'
		})

		$filterForm.find(`[name="id_department"]`).on('change', function(){
			generatePositionOptions()
			generateEmployeesOptions()
		})

		$filterForm.find(`[name="id_position"]`).on('change', function(){
			generateEmployeesOptions()
		})

		$filterForm.find(`[name="id_employee_group"]`).on('change', function(){
			generateEmployeesOptions()
		})

		@if(isset($_GET['status']))
		$filterForm.find(`[name="status"]`).val(`{{ $_GET['status'] }}`).trigger('change')
		@endif

		reloadDT()


	});
</script>
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

					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Awal Mulai Lembur </label>
								<input type="date" name="start_date" class="form-control" id="start-date">
								<div class="text-small mt-1">
									<a href="javascript:void(0);" class="set-date" data-target="#start-date" data-value="{{ today()->addDays(-1)->format('Y-m-d') }}"> Kemarin </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#start-date" data-value="{{ today()->format('Y-m-d') }}"> Hari Ini </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#start-date" data-value="{{ today()->format('Y-m-01') }}"> Awal Bulan </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#start-date" data-value="{{ today()->format('Y-m-t') }}"> Akhir Bulan </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#start-date" data-value=""> Kosongkan </a>
								</div>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Akhir Selesai Lembur </label>
								<input type="date" name="end_date" class="form-control" id="end-date">
								<div class="text-small mt-1">
									<a href="javascript:void(0);" class="set-date" data-target="#end-date" data-value="{{ today()->addDays(-1)->format('Y-m-d') }}"> Kemarin </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#end-date" data-value="{{ today()->format('Y-m-d') }}"> Hari Ini </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#end-date" data-value="{{ today()->format('Y-m-01') }}"> Awal Bulan </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#end-date" data-value="{{ today()->format('Y-m-t') }}"> Akhir Bulan </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#end-date" data-value=""> Kosongkan </a>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label> Alasan Lembur </label>
						<select name="id_overtime_reason" style="width: 100%;">
							<option value="all"> - Semua Alasan - </option>

							@foreach(\App\Models\OvertimeReason::all() as $reason)
							<option value="{{ $reason->id }}">
								{{ $reason->reason }}
							</option>
							@endforeach

						</select>
					</div>

					<div class="form-group">
						<label> Status Pengajuan </label>
						<select name="status" style="width: 100%;">
							<option value="all"> - Semua Status - </option>
							<option value="wait"> Menunggu </option>
							<option value="approved"> Disetujui </option>
							<option value="rejected"> Ditolak </option>
							<option value="canceled"> Dibatalkan </option>
							<option value="skip"> Dilewat </option>
						</select>
					</div>

					<hr>

					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Departemen </label>
								<select class="form-control" name="id_department" style="width: 100%;">
									<option value="all"> - Semua Departemen - </option>

									@foreach(\App\Models\Department::all() as $department)
									<option value="{{ $department->id }}"> {{ $department->department_name }} </option>
									@endforeach

								</select>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Jabatan </label>
								<select class="form-control" name="id_position" style="width: 100%;">
									<option value="all"> - Semua Jabatan - </option>
								</select>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Grup Karyawan </label>
								<select class="form-control" name="id_employee_group" style="width: 100%;">
									<option value="all"> - Semua Grup Karyawan - </option>

									@foreach(\App\Models\EmployeeGroup::all() as $employeeGroup)
									<option value="{{ $employeeGroup->id }}"> {{ $employeeGroup->group_name }} </option>
									@endforeach

								</select>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Karyawan </label>
								<select class="form-control" name="id_employee" style="width: 100%;">
									<option value="all"> - Semua Karyawan - </option>
								</select>
							</div>
						</div>
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