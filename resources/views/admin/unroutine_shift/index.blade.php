@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">

					{!! Template::titleBanner($title) !!}

					<div class="btn-toolbar mb-0 d-none d-sm-block" role="toolbar">

					</div>
				</div>

				<div class="table-responsive support-pane">
					<table class='table table-bordered table-hover' id="dataTable">
						<thead>
							<tr>
								<th> Nama Karyawan </th>
								<th> Nomor Induk </th>
								<th> Departemen </th>
								<th> Jabatan </th>
								<th width="100px"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Nama Karyawan </th>
								<th> Nomor Induk </th>
								<th> Departemen </th>
								<th> Jabatan </th>
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
				url : "{{ route('unroutine_shift') }}"
			},
			columns : [
				{
					data : 'employee_name',
					name : 'employee_name'
				},
				{
					data : 'employee_number',
					name : 'employee_number'
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