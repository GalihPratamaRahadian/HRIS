@extends('template.backLayout')


@section('action')
@if(UserPermission::check('employee_salary', 'c'))
<a href="{{ route('employee_salary.create') }}" class="btn btn-success">
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
								<th> Nama Karyawan </th>
								<th> Gaji Pokok </th>
								<th> Upah Lembur </th>
								<th> Tunjangan Tetap </th>
								<th> Potongan Tetap </th>
								<th width="100px"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Nama Karyawan </th>
								<th> Gaji Pokok </th>
								<th> Upah Lembur </th>
								<th> Tunjangan Tetap </th>
								<th> Potongan Tetap </th>
								<th width="100px"> Aksi </th>
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
				url : "{{ route('employee_salary') }}"
			},
			columns : [
				{
					data : 'employee.employee_name',
					name : 'employees.employee_name'
				},
				{
					data : 'basic_salary',
					name : 'basic_salary'
				},
				{
					data : 'overtime_pay',
					name : 'overtime_pay'
				},
				{
					data : 'total_allowance',
					name : 'total_allowance'
				},
				{
					data : 'total_cut',
					name : 'total_cut'
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


	});
</script>
@endsection