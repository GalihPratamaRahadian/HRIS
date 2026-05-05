@extends('template.backLayout')


@section('content')
<div class="row">

	<div class="col-md-12 grid-margin">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="row">
					<div class="col-lg-5">
						Periode <strong id="period"> - </strong>
					</div>

					<div class="col-lg-7 text-right">
						<a class="btn btn-success" href="{{ route('attendance.create_summary') }}">
							<i class="mdi mdi-file-excel"></i> Buat Rekapan Kehadiran
						</a>
						<!-- <button class="btn btn-danger" id="downloadPdfBtn">
							<i class="mdi mdi-file-pdf"></i> Export to PDF
						</button> -->
						<button class="btn btn-primary" id="filterBtn">
							<i class="mdi mdi-filter"></i> Filter
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-3 grid-margin">
		<div class="card support-pane-card bg-success text-white position-relative">
			<div class="card-body">
				<div class="justify-content-between align-items-center">
					<h3 id="amountOfHadir"> - </h3>
					<small class="d-block"> Hadir </small>
				</div>

			</div>
			<i class="mdi mdi-account-check mdi-48px position-absolute" style="bottom: 20px; right: 20px;"></i>
		</div>
	</div>

	<div class="col-md-3 grid-margin">
		<div class="card support-pane-card bg-warning text-white position-relative">
			<div class="card-body">
				<div class="justify-content-between align-items-center">
					<h3 id="amountOfIzinAndSakit"> - </h3>
					<small class="d-block"> Izin / Sakit </small>
				</div>
			</div>
			<i class="mdi mdi-account-alert mdi-48px position-absolute" style="bottom: 20px; right: 20px;"></i>
		</div>
	</div>

	<div class="col-md-3 grid-margin">
		<div class="card support-pane-card bg-primary text-white position-relative">
			<div class="card-body">
				<div class="justify-content-between align-items-center">
					<h3 id="amountOfCutiAndLibur"> - </h3>
					<small class="d-block"> Cuti / Libur </small>
				</div>

			</div>
			<i class="mdi mdi-account mdi-48px position-absolute" style="bottom: 20px; right: 20px;"></i>
		</div>
	</div>

	<div class="col-md-3 grid-margin">
		<div class="card support-pane-card bg-danger text-white position-relative">
			<div class="card-body">
				<div class="justify-content-between align-items-center">
					<h3 id="amountOfBelumAndTidakHadir"> - </h3>
					<small class="d-block"> Tidak / Belum Hadir </small>
				</div>

			</div>
			<i class="mdi mdi-account-remove mdi-flip-h mdi-48px position-absolute" style="bottom: 20px; right: 20px;"></i>
		</div>
	</div>

	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> {{ $title }} </h4>
				</div>

				<div class="table-responsive support-pane">
					<table class='table table-bordered table-hover' id="dataTable">
						<thead>
							<tr>
								<th> Tgl </th>
								<th> Karyawan </th>
								<th> Departemen </th>
								<th> Jam Datang </th>
								<th> Jam Keluar </th>
								<th> Terlambat </th>
								<th> Status </th>
								<th width="80px"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Tgl </th>
								<th> Karyawan </th>
								<th> Departemen </th>
								<th> Jam Datang </th>
								<th> Jam Keluar </th>
								<th> Terlambat </th>
								<th> Status </th>
								<th width="80px"> Aksi </th>
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

					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Awal </label>
								<input type="date" name="start_date" class="form-control" id="start-date" required>
								<div class="text-small mt-1">
									<a href="javascript:void(0);" class="set-date" data-target="#start-date" data-value="{{ today()->addDays(-1)->format('Y-m-d') }}"> Kemarin </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#start-date" data-value="{{ today()->format('Y-m-d') }}"> Hari Ini </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#start-date" data-value="{{ today()->format('Y-m-01') }}"> Awal Bulan </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#start-date" data-value="{{ today()->format('Y-m-t') }}"> Akhir Bulan </a>
								</div>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Akhir </label>
								<input type="date" name="end_date" class="form-control" id="end-date" required>
								<div class="text-small mt-1">
									<a href="javascript:void(0);" class="set-date" data-target="#end-date" data-value="{{ today()->addDays(-1)->format('Y-m-d') }}"> Kemarin </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#end-date" data-value="{{ today()->format('Y-m-d') }}"> Hari Ini </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#end-date" data-value="{{ today()->format('Y-m-01') }}"> Awal Bulan </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#end-date" data-value="{{ today()->format('Y-m-t') }}"> Akhir Bulan </a>
								</div>
							</div>
						</div>
					</div>


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
						<label> Status Kehadiran </label>
						<select name="type" style="width: 100%;" required>
							<option value="all"> - Semua Status - </option>
							@foreach(\App\Models\Attendance::availableTypes() as $key => $label)
							<option value="{{ $key }}"> {{ $label }} </option>
							@endforeach
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

		const $filterForm = $('#filterForm');

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

		$filterForm.find(`[name="type"]`).select2({
			'placeholder': '- Pilih Status -'
		})

		const reloadDT = () => {
			$('#dataTable').DataTable().ajax.reload();
		}

		const getAttendanceData = () => {
			
			const formData = $filterForm.serialize();

			$.get({
				url : `{{ route('attendance.data') }}?${formData}`,
				dataType : 'json'
			})
			.done(response => {
				if(response.code == 200) {
					const { summary, date } = response;
					const { hadir, sakit, izin, cuti, libur, belum_hadir, tanpa_keterangan } = summary;

					$('#period').html(date);
					$('#amountOfHadir').html(hadir);
					$('#amountOfIzinAndSakit').html(parseInt(sakit) + parseInt(izin));
					$('#amountOfCutiAndLibur').html(parseInt(cuti) + parseInt(libur));
					$('#amountOfBelumAndTidakHadir').html(parseInt(belum_hadir) + parseInt(tanpa_keterangan));
				}
			})
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
						getAttendanceData()
					})
					.fail(error => {
						ajaxErrorHandling(error)
					})
				})
			});

			$('.action-btn').off('click')
			$('.action-btn').on('click', function(){
				let { message, href } = $(this).data()

				confirmation(message, () => {
					ajaxSetup();
					$.ajax({
						url : href,
						method : 'post',
					})
					.done(response => {
						ajaxSuccessHandling(response)
						reloadDT();
						getAttendanceData()
					})
					.fail(error => {
						ajaxErrorHandling(error)
					})
				})
			});
		}


		const setUrl = () => {
			let query = $filterForm.serialize();
			const url = `{{ route('attendance') }}?${query}`;

			window.history.pushState('attendance', $('title').text(), url);
		}


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


		$('#filterBtn').on('click', function(){
			$('#filterModal').modal('show');
		})


		const getQuery = () => {
			return $filterForm.serialize();
		}

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

		$filterForm.find(`[name="id_employee"]`).select2({
			'placeholder': '- Pilih Karyawan -'
		})


		$filterForm.on('submit', function(e){
			e.preventDefault();

			reloadDT();
			getAttendanceData();
			setUrl()

			$('#filterModal').modal('hide');
		})

		$('#downloadPdfBtn').on('click', function(){
			window.open(`{{ route('attendance.export_to_pdf') }}?query=${getQuery()}`)
		})

		@if(isset($_GET['start_date']) || isset($_GET['end_date']))
		$filterForm.find('[name="start_date"]').val(`{{ $_GET['start_date'] ?? '' }}`);
		$filterForm.find('[name="end_date"]').val(`{{ $_GET['end_date'] ?? '' }}`);
		@else
		$filterForm.find('[name="start_date"]').val(`{{ date('Y-m-d') }}`);
		$filterForm.find('[name="end_date"]').val(`{{ date('Y-m-d') }}`);
		@endif

		@if(isset($_GET['id_department']))
		$filterForm.find('[name="id_department"]').val(`{{ $_GET['id_department'] }}`).trigger('change');
		@endif


		@if(isset($_GET['id_employee_group']))
		$filterForm.find('[name="id_employee_group"]').val(`{{ $_GET['id_employee_group'] }}`).trigger('change');
		@endif

		@if(isset($_GET['type']))
		$filterForm.find('[name="type"]').val(`{{ $_GET['type'] }}`).trigger('change');
		@endif

		@if(isset($_GET['id_position']))
		setTimeout(() => {
			$filterForm.find('[name="id_position"]').val(`{{ $_GET['id_position'] }}`).trigger('change');
			getAttendanceData();
			reloadDT();
		}, 1000)
		@else
			getAttendanceData();
		@endif

		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('attendance') }}"
			},
			columns : [
				{
					data : "date",
					name : "date"
				},
				{
					data : "employees.employee_name",
					name : "employees.employee_name"
				},
				{
					data : "departments.department_name",
					name : "departments.department_name"
				},
				{
					data : "clock_in",
					name : 'clock_in'
				},
				{
					data : "clock_out",
					name : 'clock_out'
				},
				{
					data : "late",
					name : 'late'
				},
				{
					data : "type",
					name : 'type'
				},
				{
					data : 'action',
					name : 'action',
					orderable : false,
					searchable : false,
				}
			],
			order: [[ '0', 'asc' ]],
			preDrawCallback : settings => {
				
				settings.ajax.url = `{{ route('attendance') }}?${getQuery()}`;
			},
			drawCallback : settings => {
				renderedEvent();
			}
		})

		$('.set-date').on('click', function(){
			const { target, value } = $(this).data()
			$(target).val(value).trigger('change')
		})

	});
</script>
@endsection