@extends('template.backLayout')


@section('action')
<button class="btn btn-primary" id="filterBtn">
	<i class="mdi mdi-filter"></i> Filter
</button>

@if(UserPermission::check('payroll', 'c'))
<a href="{{ route('payroll.create') }}" class="btn btn-success">
	<i class="mdi mdi-plus-thick"></i> Buat Penggajian
</a>
@endif
@endsection


@section('content')
<div class="row">
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
								<th> Periode </th>
								<th> Total </th>
								<th> Tanggal Dibuat </th>
								<th> Status Pengiriman </th>
								<th width="100px"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td> Karyawan </td>
								<td> Periode </td>
								<td> Total </td>
								<td> Tanggal Dibuat </td>
								<th> Status Pengiriman </th>
								<td width="100px"> Aksi </td>
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
				url : "{{ route('payroll') }}"
			},
			columns : [
				{
					data : "employee_name",
					name : 'employee.employee_name'
				},
				{
					data : "period_start",
					name : 'period_start'
				},
				{
					data : "total",
					name : 'total'
				},
				{
					data : "created_at",
					name : "created_at"
				},
				{
					data : "send_status",
					name : "send_status"
				},
				{
					data : 'action',
					name : 'action',
					orderable : false,
					searchable : false,
				}
			],
			order : [[ '3', 'desc' ]],
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

				swal({
					title: "Yakin ingin dihapus?",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Ya, hapus!",
					cancelButtonText: "Batal",
					closeOnConfirm: true
				}, () => {

					ajaxSetup();
					$.ajax({
						url : href,
						method : 'delete',
					})
					.done(response => {

						let { message } = response

						toastrAlert();
						toastr.success(message, 'Berhasil')
						reloadDT();
					})
					.fail(error => {

						let { responseJSON } = error
						let { message } = responseJSON

						toastrAlert();
						toastr.warning(message, 'Peringatan')
					})
				});
			});

		}


	});
</script>
@endsection