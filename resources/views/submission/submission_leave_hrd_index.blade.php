@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">

				<div class="table-responsive support-pane">
					<table class='table table-bordered table-hover' id="dataTable">
						<thead>
							<tr>
								<th> Karyawan </th>
								<th> Alasan </th>
								<th> Tanggal </th>
								<th> Status </th>
								<th> Diajukan Pada </th>
								<th width="100px"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Karyawan </th>
								<th> Alasan </th>
								<th> Tanggal </th>
								<th> Status </th>
								<th> Diajukan Pada </th>
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
				url : "{{ route('submission.leave') }}"
			},
			columns : [
				{
					data : "employee.employee_name",
					name : 'employees.employee_name'
				},
				{
					data : "leave_reason.reason",
					name : 'leave_reasons.reason'
				},
				{
					data : "start_date",
					name : 'start_date'
				},
				{
					data : "status",
					name : 'status'
				},
				{
					data : "created_at",
					name : 'created_at'
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
			order : [ ['4', 'desc'], [ '2', 'desc' ] ]
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