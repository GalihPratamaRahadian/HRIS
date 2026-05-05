@extends('template.backLayout')


@section('action')
@if(user()->isEmployee())
<a href="{{ route('employee.attendance_permission_submission.create') }}" class="btn btn-success">
	<i class="mdi mdi-plus-thick"></i> Buat
</a>
@endif
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
								<th> Jenis </th>
								<th> Alasan </th>
								<th> Tanggal </th>
								<th> Jam Diizinkan </th>
								<th> Status </th>
								<th width="100px"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Diajukan Pada </th>
								<th> Jenis </th>
								<th> Alasan </th>
								<th> Tanggal </th>
								<th> Jam Diizinkan </th>
								<th> Status </th>
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
				url : "{{ route('employee.attendance_permission_submission') }}"
			},
			columns : [
				{
					data : 'created_at',
					name : 'created_at'
				},
				{
					data : 'type',
					name : 'type'
				},
				{
					data : 'reason',
					name : 'reason'
				},
				{
					data : 'date',
					name : 'date'
				},
				{
					data : 'time',
					name : 'time'
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
		}


	});
</script>
@endsection