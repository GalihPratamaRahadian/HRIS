@extends('template.backLayout')

@section('action')
@if(UserPermission::check('department', 'u'))
<a href="{{ route('admin.department.update', $department->id) }}" class="btn btn-warning">
	<i class="mdi mdi-pencil"></i> Edit Departemen
</a>
@endif
@endsection


@section('content')
<div class="row">
	<div class="col-md-3">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<table class="table table-bordered">
					<tbody>
						<tr>
							<th> Nama Departemen </th>
							<td> {{ $department->department_name }} </td>
						</tr>
						<tr>
							<th> Jumlah Karyawan </th>
							<td> {{ count($department->activeEmployees) }} </td>
						</tr>
					</tbody>
				</table>

			</div>
		</div>
	</div>


	<div class="col-md-9">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner("Karyawan Departemen ".$department->department_name) !!}

				<div class="table-responsive support-pane">
					<table class='table table-bordered table-hover' id="dataTable">
						<thead>
							<tr>
								<th> No Karyawan </th>
								<th> Nama Karyawan </th>
								<th> Jabatan </th>
								<th> Jam Kerja </th>
								<th> Status Pekerjaan </th>
								<th width="80"> Aksi </th>
							</tr>
						</thead>
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

		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('employee') }}?id_department={{ $department->id }}&&employee_status={{ \App\Models\Employee::STATUS_ACTIVE }}"
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
					data : 'action',
					name : 'action',
					orderable : false,
					searchable : false,
				}
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

	});
</script>
@endsection