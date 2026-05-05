@extends('template.backLayout')


@section('action')
<a href="{{ route('employee.leave_submission.create') }}" class="btn btn-success">
	<i class="mdi mdi-plus-thick"></i> Buat
</a>
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
								<th> Alasan </th>
								<th> Tanggal Awal </th>
								<th> Tanggal Akhir </th>
								<th> Status </th>
								<th width="100px"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Diajukan Pada </th>
								<th> Alasan </th>
								<th> Tanggal Awal </th>
								<th> Tanggal Akhir </th>
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
				url : "{{ route('employee.leave_submission') }}"
			},
			columns : [
				{
					data : 'created_at',
					name : 'created_at'
				},
				{
					data : 'leave_reason.reason',
					name : 'leave_reasons.reason'
				},
				{
					data : 'start_date',
					name : 'start_date'
				},
				{
					data : 'end_date',
					name : 'end_date'
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