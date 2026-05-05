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
								<th> Jenis </th>
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
								<th> Jenis </th>
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
				url : "{{ route('employee.sick_necessity_approval') }}"
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
					data : 'sick_necessity_submission.type',
					name : 'sick_necessity_submissions.type'
				},
				{
					data : 'sick_necessity_submission.reason',
					name : 'sick_necessity_submissions.reason'
				},
				{
					data : 'start_date',
					name : 'sick_necessity_submissions.start_date'
				},
				{
					data : 'sick_necessity_submission.status',
					name : 'sick_necessity_submissions.status'
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