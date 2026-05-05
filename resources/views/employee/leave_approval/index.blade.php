@extends('template.backLayout')


@section('action')
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
								<th> Alasan </th>
								<th> Tanggal </th>
								<th> Status Pengajuan </th>
								<th> Status Penyetujuan </th>
								<th width="90px"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Diajukan Pada </th>
								<th> Karyawan </th>
								<th> Alasan </th>
								<th> Tanggal </th>
								<th> Status Pengajuan </th>
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

		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('employee.leave_approval') }}"
			},
			columns : [
				{
					data : 'created_at',
					name : 'created_at'
				},
				{
					data : 'employee_name',
					name : 'employees.employee_name'
				},
				{
					data : 'leave_submission.leave_reason.reason',
					name : 'leave_reasons.reason'
				},
				{
					data : 'start_date',
					name : 'leave_submissions.start_date'
				},
				{
					data : 'leave_submission.status',
					name : 'leave_submissions.status'
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


	});
</script>
@endsection