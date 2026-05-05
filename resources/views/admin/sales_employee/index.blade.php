@extends('template.backLayout')


@section('action')
<a href="{{ route('sales_employee.create') }}" class="btn btn-success">
	<i class="mdi mdi-plus-thick"></i> Tambah
</a>
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
								<th> No Induk </th>
								<th> Nama Karyawan </th>
								<th> Departemen </th>
								<th> Total Toko Ditangani </th>
								<th width="80"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> No Induk </th>
								<th> Nama Karyawan </th>
								<th> Departemen </th>
								<th> Total Toko Ditangani </th>
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


@section('script')
<script type="text/javascript">
	$(function(){


		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('sales_employee') }}"
			},
			columns : [
				{
					data : 'employee.employee_number',
					name : 'employees.employee_number'
				},
				{
					data : 'employee.employee_name',
					name : 'employees.employee_name'
				},
				{
					data : 'employee.department.department_name',
					name : 'departments.department_name'
				},
				{
					data : 'amount_of_stores',
					name : 'amount_of_stores'
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
		}


	});
</script>
@endsection