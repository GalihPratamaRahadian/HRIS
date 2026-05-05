@extends('template.backLayout')


@section('content')
<div class="row">

	<div class="col-md-12 grid-margin">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="row">
					<div class="col-lg-3">
						Periode <strong id="period"> - </strong>
					</div>

					<div class="col-lg-9 text-right">
						<button class="btn btn-primary" id="filterBtn">
							<i class="mdi mdi-filter"></i> Filter
						</button>
					</div>
				</div>
			</div>
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
								<th> Karyawan </th>
								<th> Departemen </th>
								<th> Jam Check Day </th>
								<th width="80px"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Karyawan </th>
								<th> Departemen </th>
								<th> Jam Check Day </th>
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

					<div class="form-group">
						<label> Tanggal </label>
						<input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
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

		const reloadDT = () => {
			$('#dataTable').DataTable().ajax.reload();
		}

		const getAttendanceData = () => {
			
			const formData = $filterForm.serialize();

			$.get({
				url : `{{ route('check_day.data') }}?${formData}`,
				dataType : 'json'
			})
			.done(response => {
				if(response.code == 200) {
					const { date } = response;

					$('#period').html(date);
				}
			})
		}

		getAttendanceData()


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
						getAttendanceData()
						reloadDT();
					})
					.fail(error => {
						ajaxErrorHandling(error)
					})
				})
			});
		}


		const setUrl = () => {
			const date = $filterForm.find('[name="date"]').val();
			const departmentID = $filterForm.find('[name="id_department"]').val();
			const url = `{{ route('attendance') }}?date=${date}&id_department=${departmentID}`;

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


		$('#filterBtn').on('click', function(){
			$('#filterModal').modal('show');
		})


		@if(isset($_GET['date']))
		$filterForm.find('[name="date"]').val(`{{ $_GET['date'] }}`);
		@else
		$filterForm.find('[name="date"]').val(`{{ date('Y-m-d') }}`);
		@endif

		@if(isset($_GET['id_department']))
		$filterForm.find('[name="id_department"]').val(`{{ $_GET['id_department'] }}`).trigger('change');
		@endif


		const getQuery = () => {
			return $filterForm.serialize();
		}


		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('check_day') }}"
			},
			columns : [
				{
					data : "employee.employee_name",
					name : 'employees.employee_name'
				},
				{
					data : "employee.department.department_name",
					name : 'departments.department_name'
				},
				{
					data : "check_day_at",
					name : 'check_day_at'
				},
				{
					data : 'action',
					name : 'action',
					orderable : false,
					searchable : false,
				}
			],
			order: [[ '2', 'asc' ]],
			preDrawCallback : settings => {
				
				settings.ajax.url = `{{ route('check_day') }}?${getQuery()}`;
			},
			drawCallback : settings => {
				renderedEvent();
			}
		})

		$filterForm.find(`[name="id_department"]`).on('change', function(){
			generatePositionOptions()
		})

		$filterForm.on('submit', function(e){
			e.preventDefault();

			reloadDT();
			setUrl()
			getAttendanceData()

			$('#filterModal').modal('hide');
		})

	});
</script>
@endsection